<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class youhuiApiModule extends MainBaseApiModule
{
    /**
     * 优惠券详细页接口
     * 输入：
     * data_id: int 优惠券ID
     * 
     * 输出：
     * [array] 优惠券数据数组
     * [youhui_info] => Array  
        (
            [id] => 24  [int] 优惠券数据ID
            [share_url] => [string] 分享链接
            [name] => 烤羊腿       [string] 优惠券名称
            [icon] => http://localhost/o2onew/public/attachment/201505/04/11/5546e29f58225_600x364.jpg [string] 展示图 300x182
            [now_time] => 1430875272   [string] 当前时间
            [begin_time] => 1430766480   [string]   开始时间
            [end_time] => 1431716880     [string]   结束时间    
            [last_time] => 841608    [string]       最后剩下时间（结束时间-当前时间） 结束时间必须大于0才有，否则为0
            [last_time_format] => 9天以上   [string]   格式化最后剩下时间
            [expire_day] => 50   [int]  领取后有效天数
            [total_num] => 1000  [int]  优惠券总数
            [is_effect] => 1 [int]      是否有效
            [user_count] => 9   [int]   已经领取数量
            [user_limit] => 10  [int]   用户每天最多领取数量（用于限制）
            [score_limit] => 10 [int]   消耗积分
            [point_limit] => 20 [int]   经验限制
            [supplier_info_name] => 福州肯德基     [string]商户主门店名称
            [avg_point] => 3    [float] 点评平均分
            [description]=>fsafsa  [string] 优惠详情 / 展示图 300x？
            [use_notice]=>afas  [string] 使用须知/ 展示图 300x？
            [xpoint] => [float] 所在经度
            [ypoint] => [float] 所在纬度
        )
    [array] 其它支持门店
    [other_supplier_location] => Array
        (
            [0] => Array
                (
                    [id] => 23  [int]   门店编号
                    [name] => 肯德基（省府店）  [string]    门店名称
                    [address] => 鼓楼区八一七北路68号福建供销大厦二楼    [string]    门店地址
                    [tel] => 059188855566   [string] 门店电话
                    [xpoint] => [float] 所在经度
            		[ypoint] => [float] 所在纬度
                )

        )

    [dp_list] => Array [array] 点评数据列表
        (
          [4] => Array
                (
                    [id] => 5 [int] 点评数据ID
                    [create_time] => 2015-04-07 [string] 点评时间
                    [content] => 不错不错   [string] 点评内容
                    [reply_content] => 那是不错的了，可以信任的品牌 [string] 管理员回复内容
                    [point] => 5    [int] 点评分数
                    [user_name] => fanwe  [string] 点评用户名称
                    [images] => Array [array] 点评图集 压缩后的图片
                        (
                            [0] => http://localhost/o2onew/public/comment/201504/07/14/bca3b3e37d26962d0d76dbbd91611a6a36_120x120.jpg   [string] 点评图片 60X60
                            [1] => http://localhost/o2onew/public/comment/201504/07/14/5ea94540a479810a7559b5db909b09e986_120x120.jpg   [string] 点评图片 60X60
                            [2] => http://localhost/o2onew/public/comment/201504/07/14/093e5a064c4081a83f31864881bd802061_120x120.jpg   [string] 点评图片 60X60
                        )

                    [oimages] => Array [array] 点评图集 原图
                        (
                            [0] => http://localhost/o2onew/public/comment/201504/07/14/bca3b3e37d26962d0d76dbbd91611a6a36.jpg [string] 点评图片 原图
                            [1] => http://localhost/o2onew/public/comment/201504/07/14/5ea94540a479810a7559b5db909b09e986.jpg [string] 点评图片 原图  
                            [2] => http://localhost/o2onew/public/comment/201504/07/14/093e5a064c4081a83f31864881bd802061.jpg [string] 点评图片 原图
                        )

                )

        )
     * */
    public function index(){
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        
        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $user_login_status = check_login();
        if($user_login_status == LOGIN_STATUS_LOGINED){
            // 判断是否收藏
            $root['is_collect'] = $this->is_collect($user_id, $data_id);
        }
        $root['user_login_status'] = $user_login_status;
        //获取优惠数据
        require_once(APP_ROOT_PATH."system/model/youhui.php");
        $youhui_info = get_youhui($data_id);
        
        if($youhui_info){
            $root['id'] = $youhui_info['id'];
        }else{
            return output($root,0,"优惠券数据未找到");
        }
        //获取支持门店数据
        // 增加一个距离的字段
        $field_append = '';
        if($GLOBALS['geo']['xpoint']){
            $geo = $GLOBALS['geo'];  //开始身边团购的地理定位
            $ypoint =  $geo['ypoint'];  //ypoint
            $xpoint =  $geo['xpoint'];  //xpoint
            $pi = PI;  //圆周率
            $r = EARTH_R;  //地球平均半径(米)
            $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
        }

        $supplier_locations = $GLOBALS['db']->getAll("select sl.id,sl.name,sl.address,sl.tel,sl.xpoint,sl.ypoint $field_append from ".DB_PREFIX."youhui_location_link as yll left join ".DB_PREFIX."supplier_location as sl on sl.id = yll.location_id where yll.youhui_id = ".$data_id);
        foreach ($supplier_locations as $k=>$v){
            if (array_key_exists('distance', $v)) {
                $supplier_locations[$k]['distance_format'] = format_distance_str($v['distance']);
            }
        }
        /*点评数据*/
        require_once(APP_ROOT_PATH."system/model/review.php");
        // print_r($GLOBALS['db']->getLastSql());exit;
        /*获点评数据*/
        $dp_list = get_dp_list(2,$param=array("youhui_id"=>$data_id),"","");
        $root['dp_list'] = $dp_list['list'] ? format_dp_list($dp_list):array();
        // 获取评论总数
        $countSql = "select count(*) from ".DB_PREFIX."supplier_location_dp where  ".$dp_list['condition'];
        $countDp = $GLOBALS['db']->getOne($countSql);
        $root['dp_count'] = $countDp;

        $root['other_supplier_location'] = $supplier_locations?$supplier_locations:array();
        $root['youhui_info'] = format_youhui_item($youhui_info);
        $root['youhui_info']['status'] = $this->format_status($youhui_info, $user_login_status);
        $root['youhui_info']['location_count'] = count($root['other_supplier_location']); // 计算商家门店数量
        
        // 获取优惠券的被收藏数量
        if($user_id){
            $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_sc where uid=".$user_id." and youhui_id=".$youhui_info['id']);
            $user_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_sc where uid=".$user_id);
            if($count){
                $root['is_collect']=1;
            }else {
                $root['is_collect']=0;
            }
            $root['collect_count']=$user_count;
        }
        //$root['collect_count'] = youhui_collect_count($data_id);

        $root['page_title']="优惠券详情";

        //分享使用数据
        $root['share_title'] = $root['youhui_info']['name'];
        $root['share_content'] = $root['youhui_info']['share_url'];
        $root['share_url'] = $root['youhui_info']['share_url'];
        $root['share_img'] = $root['youhui_info']['icon'];

        return output($root);
    }
    
    /**
     * 优惠券的可领取状态判断
     * @param  array $v 优惠信息
     * @param int $login_status 登录状态
     * @return int    
     */
    public function format_status($youhui, $login_status)
    {
        $status = 9;
        if($login_status != LOGIN_STATUS_LOGINED){
            $status = 0; // 登录后领取
        } elseif ($youhui['end_time'] > 0 && $youhui['end_time'] < NOW_TIME) {
            $status = 1; // 领取已结束
        } elseif ($youhui['begin_time'] > 0 && $youhui['begin_time'] > NOW_TIME) {
            $status = 2; // 活动未开始
        } elseif (/*$youhui['total_num'] > 0 &&*/ $youhui['total_num'] <= $youhui['user_count']) {
            $status = 3; // 已抢光
        } else { // 判断登录后的状态
            $user_info = $GLOBALS['user_info'];
            // 判断是否已领取
            $today_begin = strtotime(date('Y-m-d'));
            $today_end = $today_begin + 3600 * 24 - 1;
            $sql = 'SELECT count(*) FROM '.DB_PREFIX.'youhui_log WHERE user_id='.$user_info['id'].' AND youhui_id='.$youhui['id'].' AND create_time BETWEEN '.$today_begin.' AND '.$today_end;
            $has_get = $GLOBALS['db']->getOne($sql);
            if ($youhui['user_limit'] > 0 && $has_get >= $youhui['user_limit']) {
                $status = 4; // 今日已领完
            } elseif ($user_info['point'] < $youhui['point_limit']) {
                $status = 5; // 经验不足
            } elseif ($user_info['score'] < $youhui['score_limit']) {
                $status = 6; // 积分不足
            }
        }
        return $status;
    }
    
    /**
     * 优惠券下载接口
     * 输入：
     * data_id: int 优惠券ID
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * [info] string 错误消息/成功消息
     * [status] int 0 失败， 1成功
     * 
     * */
    public function download_youhui(){
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
       
        $root['user_login_status'] = check_login();
        if($root['user_login_status']!=LOGIN_STATUS_LOGINED){
            return output($root,0,'');
        }
         
        
        require_once(APP_ROOT_PATH."system/model/youhui.php");
        $youhui_info = get_youhui($data_id);
   
        $result = download_youhui($data_id,$user_id);
        //status:1领取成功 0.领取失败 2.库存已满 3.时间超期
        $root['jump']=$result['jump'];
        if($result['status']>=0)
        {
            if($result['status']==YOUHUI_OUT_OF_STOCK||$result['status']==YOUHUI_USER_OUT_OF_STOCK)
            {

                return output($root,0,$result['info']);
            }
            else if($result['status']==YOUHUI_DOWNLOAD_SUCCESS)
            {
                $youhui=$GLOBALS['db']->getRow("select y.*,l.location_id from ".DB_PREFIX."youhui as y left join ".DB_PREFIX."youhui_location_link l ON y.id = l.youhui_id where y.id=".$data_id." group by y.id");
                $youhui_data=format_youhui_list_item($youhui);
                
                $root['data']=$youhui_data;
                return output($root,1,$result['info']);
            }
            else if($result['status']==YOUHUI_DOWNLOAD_LIMIT)
            {
                return output($root,8,$result['info']);
            }
            else
            {
                return output($root,0,$result['info']);
            }
        }
        else
        {
            return output($root,0,$result['info']);
        }

        return output($root);
    }
    
    /**
     * 优惠券详情接口
     * 输入：
     * data_id: int 优惠券ID
     *
     * 输出：
     * 优惠券部分数据
     * [youhui_info] => Array
        (
            [id] => 24
            [name] => 烤羊腿   [string] 优惠券名称
            [description] => <img src='http://localhost/o2onew/public/attachment/201505/06/17/5549dbd2183b6_300x0.jpg' lazy='true' /> [string]优惠券详情 图片大小宽度均为 150x?
            [use_notice] => <div>团将以短信形式通知中奖用户，请届时注意查收短信本单奖品不可折现</dd></div> [string]优惠须知， 图片大小宽度均为 150x?
        )
     *
     * */
    public function detail(){
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);

        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $user_login_status = check_login();

        if($user_login_status == LOGIN_STATUS_LOGINED){
            // 判断是否收藏
            $root['is_collect'] = $this->is_collect($user_id, $data_id);
            $root['user_login_status'] = $user_login_status;
        }

        //获取优惠数据
        require_once(APP_ROOT_PATH."system/model/youhui.php");
        $youhui_info = get_youhui($data_id);
        if($youhui_info){
            $root['id'] = $youhui_info['id'];
        }else{
            return output($root,0,"优惠券不存在");
        }
        $data['id'] = $youhui_info['id'];
        $data['name'] = $youhui_info['name'];
        $data['description'] = get_abs_img_root(format_html_content_image($youhui_info['phone_description'],150,0,false));
        $data['use_notice'] = get_abs_img_root(format_html_content_image($youhui_info['use_notice'],150,0,false));
        
        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title']="优惠券详情";
        
        // 获取优惠券的被收藏数量
        $root['collect_count'] = youhui_collect_count($data_id);

        $root['youhui_info'] = $data;
        return output($root);
    }

    /**
     * 优惠券添加收藏接口
     */
    public function add_collect()
    {
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        
        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        $user_login_status = check_login();
        $status = 0;
        $info = '';
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            // 判断是否已经收藏
            $sql = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_sc WHERE uid='.$user_id.' AND youhui_id='.$data_id;
            $result = $GLOBALS['db']->getOne($sql);
            if ($result > 0) {
                $root['is_collect'] = 1;
                $info = '该优惠券已收藏';
            } else {
                $data = array(
                    'uid' => $user_id,
                    'youhui_id' => $data_id,
                    'add_time' => NOW_TIME,
                );
                $addResult = $GLOBALS['db']->autoExecute(DB_PREFIX.'youhui_sc', $data);
                if (!$addResult) {
                    $info = '操作异常,请重试';
                } else {
                    $root['is_collect'] = 1;
                    $status = 1;
                    $info = '收藏成功';
                }
            }
        }
        $root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_sc where uid=".$user_id);
        

        $root['user_login_status'] = $user_login_status;

        return output($root, $status, $info);
    }

    /**
     * 优惠券取消收藏接口
     * @return array 
     */
    public function del_collect()
    {
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        
        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        $user_login_status = check_login();
        $status = 0;
        $info = '';
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            // 判断是否已经收藏
            $sql = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_sc WHERE uid='.$user_id.' AND youhui_id='.$data_id;
            $result = $GLOBALS['db']->getOne($sql);
            if (!$result) {
                $root['is_collect'] = 0;
                $info = '您还未收藏该优惠券';
            } else {
                $delSql = 'DELETE FROM '.DB_PREFIX.'youhui_sc WHERE uid='.$user_id.' AND youhui_id='.$data_id;
                $GLOBALS['db']->query($delSql);
                if ($GLOBALS['db']->affected_rows()) {
                    $root['is_collect'] = 0;
                    $status = 1;
                    $info = '已取消收藏';
                } else {
                    $info = '操作异常,请重试';
                }
            }            
        }
        $root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_sc where uid=".$user_id);
        

        $root['user_login_status'] = $user_login_status;

        return output($root, $status, $info);
    }

    /**
     * 判断用户是否收藏
     * @return boolean [description]
     */
    public function is_collect($user_id, $data_id)
    {
       
        $sql = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_sc WHERE uid='.$user_id.' AND youhui_id='.$data_id;
        $result = $GLOBALS['db']->getOne($sql);
        return $result;
    }
    
    public function reviews()
    {
        $youhui_id = intval($GLOBALS['request']['data_id']);
    
        /*点评数据*/
        require_once(APP_ROOT_PATH."system/model/review.php");
        require_once(APP_ROOT_PATH."system/model/user.php");
    
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : abs($page);
    
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
         
        /*获点评数据*/
        $dp_list = get_dp_list($limit,$param=array("youhui_id"=>$youhui_id),"","");
        $format_dp_list = array();
    
        $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'supplier_location_dp WHERE youhui_id = '.$youhui_id;
         
        if (!empty($dp_list['list'])) {
            $count = $GLOBALS['db']->getOne($sqlCount);
            $page_total = ceil($count/$page_size);
    
            $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        }
        foreach($dp_list['list'] as $k=>$v){
            $format_dp_list[] = format_dp_item($v);
        }
        $root['dp_list'] = $format_dp_list;
    
        // 获取商家平均评价分数
        $sql = 'SELECT name,avg_point FROM '.DB_PREFIX.'youhui WHERE id='.$youhui_id;
        $event_info = $GLOBALS['db']->getRow($sql);
        $event_info['avg_point_percent'] = round($event_info['avg_point'] / 5 * 100, 1);
        $event_info['avg_point'] = round($event_info['avg_point'], 1);
        $root['supplier_info'] = $event_info;
         
        $root['page_title'] = $event_info['name'].'-'.'全部评价';
    
    
        return output($root);
    }
}