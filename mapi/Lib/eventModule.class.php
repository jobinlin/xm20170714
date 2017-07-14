<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class eventApiModule extends MainBaseApiModule
{
    
    /**
     * 活动详情接口
     * 输入：
     * data_id: int 活动ID
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * [id] => 4
     * [submit]=>array(
            [status]=>0;
            [info]=>"活动已结束";
       );
        [event_info] => Array
        (
            [id] => 4   [int]
            [name] => 贵安温泉自驾游   [string]
            [share_url] => [string] 分享链接
            [icon] => http://localhost/o2onew/public/attachment/201502/26/14/54eec33c40e99_600x364.jpg  [string] 展示图 300x182
            [event_begin_time] => 1422744893    [string]    活动开始时间
            [event_end_time] => 1582671295      [string]    活动结束时间
            [event_begin_time_format] => 2015-02-01     [string]    格式化活动开始时间
            [event_end_time_format] => 2020-02-26       [string]    格式话活动结束时间
            [submit_begin_time] => 1422744899   [string]报名开始时间
            [submit_end_time] => 1582671301 [string]报名结束时间
            [submit_begin_time_format] => 2015-02-01 14:54:59       [string] 格式化报名开始时间
            [submit_end_time_format] => 2020-02-26 14:55:01         [string] 格式化报名结束时间
            [submit_count] => 2 [int]   报名总数
            [total_count] => 10 [int]   活动名额 
            [score_limit] => 10 [int]   消耗积分
            [point_limit] => 0  [int]   经验限制
            [now_time] => 1430876905    [string]    当前时间
            [supplier_info_name] => 贵安温泉    [string]    活动主商户名称
            [content] => <ul class="list">保存须知：0-4℃保存24小时</li></ul> [string]活动明细
            [address] => 台江区万象城5楼（观光电梯直上5楼AGOGOKTV旁）                                        [string]活动地址
            [xpoint] => [float] 所在经度
            [ypoint] => [float] 所在纬度
            [avg_point] => 2    [float] 点评平均分
            [submitted_data] => Array   [array]   用户报名记录 如果没有报名过返回  array();
                (
                    [is_verify] => 0     [int]用户报名状态
                )

            [event_fields] => Array [array] 报名表单字段
                (
                   [0] => Array [array]输入框类型
                        (
                            [id] => 14
                            [event_id] => 3
                            [field_show_name] => 报名人数   [string] 字段名
                            [field_type] => 0   [int]   输入类型： 0 输入框 ，1 下拉框
                            [value_scope] => Array  [array] 下拉框类型用的值
                                (
                                    [0] =>  [string]下拉框的值
                                )

                            [sort] => 1 [int] 排序
                            [result] => 224444  [string]用户输入/选中的值
                        )

                    [1] => Array    [array]下拉框类型
                        (
                            [id] => 16  
                            [event_id] => 3 [int]
                            [field_show_name] => 游玩项目   [string]
                            [field_type] => 1   [int]   输入类型
                            [value_scope] => Array
                                (
                                    [0] => 好意山色 [string]
                                    [1] => 一线天美景    [string]
                                )

                            [sort] => 2
                            [result] => 一线天美景
                        )

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
     *
     * */
    public function index(){
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        
        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $root['user_login_status'] = check_login();
        
        //获取优惠数据
        require_once(APP_ROOT_PATH."system/model/event.php");
        $event_info = get_event($data_id);
        if($event_info){
            $root['id'] = $event_info['id'];
        }else{
            return output($root,0,"活动不存在");
        }
        //活动报名数据
        $submitted_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where event_id = ".$data_id." and user_id = '".$user_id."'");
        $event_info['submitted_data'] = $submitted_data?$submitted_data:array();
        
        //活动提交表单数据
        $user_submit = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id = ".$user_id." and event_id = ".$data_id);
        if($user_submit)
        {
                $event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$data_id." order by sort asc");
                foreach($event_fields as $k=>$v)
                {
                    $event_fields[$k]['result'] = $GLOBALS['db']->getOne("select result from ".DB_PREFIX."event_submit_field where submit_id = ".$user_submit['id']." and field_id = ".$v['id']." and event_id = ".$data_id);
                    $event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
                }
        }
        else
        {
            $event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$data_id." order by sort asc");
            foreach($event_fields as $k=>$v)
            {
                $event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
            }
        }
        
        
        $event_info['event_fields'] = $event_fields;
        
        /* 获取门店数据 */
        if(!$GLOBALS['geo']['xpoint']){
            $ypoint =  26.076553;  //ypoint
            $xpoint =  119.282241;  //xpoint
            $GLOBALS['geo']['xpoint'] = $xpoint;
            $GLOBALS['geo']['ypoint'] = $ypoint;
        }
        require_once(APP_ROOT_PATH."system/model/supplier.php");
        if($event_info['supplier_location_count']){
            $join = '';
            $field_append = '';
            //开始身边团购的地理定位
            $geo=$GLOBALS['geo'];
            $ypoint =  $geo['ypoint'];  //ypoint
            $xpoint =  $geo['xpoint'];  //xpoint
            
            $address = $geo['address'];
            
            if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
            {
                $pi = PI;  //圆周率
                $r = EARTH_R;  //地球平均半径(米)
                $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
            }
            $join .= " left join ".DB_PREFIX."event_location_link as l on sl.id = l.location_id ";
            $where = " l.event_id = ".$event_info['id']." ";
            $locations = get_location_list($event_info['supplier_location_count'],array("supplier_id"=>$event_info['supplier_id']),$join,$where, '', $field_append);
        }
        $supplier_location_list = array();
		if($locations){
		    foreach ($locations['list'] as $k=>$v){
		        $temp_location = array();
		        $temp_location['id'] = $v['id'];
		        $temp_location['name'] = $v['name'];
		        $temp_location['address'] = $v['address'];
		        $temp_location['tel'] = $v['tel'];
		        $temp_location['xpoint'] = $v['xpoint'];
		        $temp_location['ypoint'] = $v['ypoint'];
		        $temp_location['distance'] = $v['distance'];
                $temp_location['distance_format'] = format_distance_str($v['distance']);
		        $supplier_location_list[] = $temp_location;
		    }
		}
        
        /*点评数据*/
        require_once(APP_ROOT_PATH."system/model/review.php");
         
        /*获点评数据数量*/
        $dp_list = get_dp_list(2,$param=array("event_id"=>$data_id),"","");
        if ($dp_list['list']) {
            $dp_count_sql = 'SELECT count(id) FROM '.DB_PREFIX."supplier_location_dp WHERE  ".$dp_list['condition'];
            $dp_count = $GLOBALS['db']->getOne($dp_count_sql);
            $root['dp_count'] = $dp_count;
            foreach ($dp_list['list'] as $t => $v){
                $dp_list['list'][$t]['reply_content']='';
            }
        }
        
        $submit=array();
        /*获取当前报名状态*/
        if($event_info['submit_begin_time']>NOW_TIME){
            $submit['status']=0;
            $submit['info']="活动未开始";
        }
        else if($event_info['event_end_time']>0 && $event_info['event_end_time']<NOW_TIME)
        {
            $submit['status']=0;
            $submit['info']="您来晚了，活动已结束";
        }
        else if($event_info['submit_end_time']>0&&$event_info['submit_end_time']<NOW_TIME)
        {
            $submit['status']=0;
            $submit['info']="您来晚了，报名已结束";
        }      
        else if($event_info['submit_count']>=$event_info['total_count']&&$event_info['total_count']>0)
        {
            $submit['status']=0;
            $submit['info']="您来晚了，活动已满";
            if($user_id){
                $is_verify=$GLOBALS['db']->getOne("select is_verify from ".DB_PREFIX."event_submit where user_id=".$user_id." and event_id=".$data_id);
                if($is_verify==1){
                    $submit['info']="您已报名";
                }
            }
        }
        if(!$submit){
            if($user_id){
                $submit=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id=".$user_id." and event_id=".$data_id);
                if($submit){
                    if($submit['is_verify']==2){
                        $submit['status']=0;
                        $submit['info']="您的报名未通过审核";
                    }else if($submit['is_verify']==1){
                        $submit['status']=0;
                        $submit['info']="您已报名";
                    }else{
                        $field=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_field where event_id=".$data_id);
                        if($field){
                            $submit['status']=1;
                            $submit['info']="修改报名信息";
                        }else{
                            $submit['status']=0;
                            $submit['info']="报名审核中";
                        }
                    }
                }
                else if($user['point']<$event_info['point_limit']){
                    $submit['status']=0;
                    $submit['info']="您的经验值不足";
                }
                else if($user['score']<$event_info['score_limit']){
                    $submit['status']=0;
                    $submit['info']="您的积分不足";
                }
                else {
                    $submit['status']=1;
                    $submit['info']="马上报名";
                }
            }else{
                $submit['status']=-1;
                $submit['info']="登录后报名";
            }
        }
        
        //判断是否收藏
        if($user_id){
            $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id." and event_id=".$event_info['id']);
            $user_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id); 
            if($count){
                $root['is_collect']=1;
            }else {
                $root['is_collect']=0;
            }
            $root['collect_count']=$user_count;
        }
        
        $root['supplier_location_list'] = $supplier_location_list?$supplier_location_list:array();
        $root['submit']=$submit?$submit:array();
        $root['event_info'] = $event_info?format_event_item($event_info):array();
        $root['dp_list'] = $dp_list?format_dp_list($dp_list):array();
        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title']="活动详情";
        
        return output($root);
    }
    
    
    /**
     * 活动表单接口
     * 输入：
     * data_id: int 活动ID
     * 
     * 输出：
     * [btn_name] => 立即报名  [string] 按钮名称
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * [event_fields] => Array [array] 报名表单字段
                (
                   [0] => Array [array]输入框类型
                        (
                            [id] => 14
                            [event_id] => 3
                            [field_show_name] => 报名人数   [string] 字段名
                            [field_type] => 0   [int]   输入类型： 0 输入框 ，1 下拉框
                            [value_scope] => Array  [array] 下拉框类型用的值
                                (
                                    [0] =>  [string]下拉框的值
                                )

                            [sort] => 1 [int] 排序
                            [result] => 224444  [string]用户输入/选中的值
                        )

                    [1] => Array    [array]下拉框类型
                        (
                            [id] => 16  
                            [event_id] => 3 [int]
                            [field_show_name] => 游玩项目   [string]
                            [field_type] => 1   [int]   输入类型
                            [value_scope] => Array
                                (
                                    [0] => 好意山色 [string]
                                    [1] => 一线天美景    [string]
                                )

                            [sort] => 2
                            [result] => 一线天美景
                        )

                )

        )
     **/
    public function load_event_submit(){
        $root = array();
        /*参数列表*/
        $event_id = intval($GLOBALS['request']['data_id']);
        
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
       	$root['user_login_status'] = check_login();

        require_once(APP_ROOT_PATH."system/model/event.php");
        $event = get_event($event_id);
        if(!$event)
        {
            return output($root,0,"活动不存在");
        }
        if($event['submit_begin_time']>NOW_TIME)
        {
            return output($root,0,"活动报名未开始");
        }
        if($event['submit_end_time']>0&&$event['submit_end_time']<NOW_TIME)
        {
            return output($root,0,"活动报名已结束");
        }
        if($event['submit_count']>=$event['total_count']&&$event['total_count']>0)
        {
            return output($root,0,"活动名额已满");
        }
        
        $GLOBALS['tmpl']->assign("event_id",$event_id);

        $user_submit = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id = ".$user_id." and event_id = ".$event_id);
        if($user_submit)
        {
            if($user_submit['is_verify']==1)
            {
                return output($root,0,"您已经报名");
            }
            else
            {
                $event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$event_id." order by sort asc");
                foreach($event_fields as $k=>$v)
                {
                    $event_fields[$k]['result'] = $GLOBALS['db']->getOne("select result from ".DB_PREFIX."event_submit_field where submit_id = ".$user_submit['id']." and field_id = ".$v['id']." and event_id = ".$event_id);
                    $event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
                }
                 $btn_name = "修改报名";
            }
        }
        else
        {
            $event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$event_id." order by sort asc");
            foreach($event_fields as $k=>$v)
            {
                $event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
            }
            $btn_name = "立即报名";
        }
        
        $root['btn_name'] = $btn_name;
        $root['event_fields'] = $event_fields;
        
        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title'] = $btn_name;
        return output($root);
    }
    
    
    
    /**
     * 活动表单提交接口
     * 输入：
     * event_id: int 活动ID
     * field_id：array 字段编号
     * result：array 字段的值
     * 
     * 格式如下：
     *  [result] => Array  [array] 填入的值/下拉框选中的文本内容
        (
            [13] => 111111
            [14] => 22222
            [16] => 好意山色
        )

        [field_id] => Array  [array] 字段的编号数组
            (
                [0] => 13
                [1] => 14
                [2] => 16
            )
    
        [event_id] => 3 [int]活动的编号
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * [status] 0失败 1成功
     * [info] 成功/失败消息
     **/
    public function do_submit(){
       
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $root['user_login_status'] = check_login();
        /*获取参数*/
        $event_id = intval($GLOBALS['request']['event_id']);
        $field_ids = $GLOBALS['request']['field_id'];
        $results = $GLOBALS['request']['result'];
        
        if(APP_INDEX=="app"){
            if(!is_array($results)){
                $results=json_decode($results);
                $array=array();
                if (is_object($results)) {
                    foreach ($results as $key => $value) {
                        $array[$key] = $value;
                    }
                    $results=$array;
                }
            }
        
            if(!is_array($field_ids)){
                $field_ids=explode(",",$field_ids);
            }
        }
        
        require_once(APP_ROOT_PATH."system/model/event.php");
        $event = get_event($event_id);
        if(!$event)
        {
            return output($root,0,"活动不存在");
        }
        if($event['submit_begin_time']>NOW_TIME)
        {
            return output($root,0,"活动报名未开始");
        }
        if($event['submit_end_time']>0&&$event['submit_end_time']<NOW_TIME)
        {
            return output($root,0,"活动报名已结束");
        }
        
        if($event['submit_count']>=$event['total_count']&&$event['total_count']>0)
        {
            return output($root,0,"活动名额已满");
        }
        
        foreach ($results as $t => $v){
            if($this->filterEmoji($v)==""){
                return output($root,0,"报名信息格式错误");
            }
        }

        $user_submit = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id = ".$user_id." and event_id = ".$event_id);
        if($user_submit)
        {
            if($user_submit['is_verify']==1)
            {
                return output($root,0,"您已经报名");
            }
            elseif($user_submit['is_verify']==2)
            {
                return output($root,0,"您的报名审核不通过");
            }
            else
            {
                //已经报名，仅作修改
                $GLOBALS['db']->query("delete from ".DB_PREFIX."event_submit_field where submit_id = ".$user_submit['id']);
                
                foreach($field_ids as $field_id)
                {
                    $current_result =  strim($results[$field_id]);
                    $field_data = array();
                    $field_data['submit_id'] = $user_submit['id'];
                    $field_data['field_id'] = $field_id;
                    $field_data['event_id'] = $event_id;
                    $field_data['result'] = $current_result;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."event_submit_field",$field_data,"INSERT");
                }

                return output($root,1,"报名修改成功");
            }
        }
        else
        {
            	
            //$GLOBALS['db']->query("update ".DB_PREFIX."event set submit_count = submit_count+1 where id=".$event_id." and submit_count + 1 <= total_count and total_count > 0");
            $count=$GLOBALS['db']->getRow("select count(*) from ".DB_PREFIX."event where id=".$event_id." and submit_count< total_count and total_count > 0");
            
            if(!$count)
            {
                return output($root,0,"活动名额已满");
            }
            	
            	
            if($event['score_limit']>0||$event['point_limit']>0)
            {
                $c_user_info = $GLOBALS['user_info'];
                	
                if($c_user_info['score']<$event['score_limit'])
                {
                    return output($root,0,"积分不足，不能报名");
                }
                	
                if($c_user_info['point']<$event['point_limit'])
                {
                    return output($root,0,"经验不足，不能报名");
                }
            }
            	
            $submit_data = array();
            $submit_data['user_id'] = $user_id;
            $submit_data['event_id'] = $event_id;
            $submit_data['create_time'] = NOW_TIME;
            $submit_data['event_begin_time'] = $event['event_begin_time'];
            $submit_data['event_end_time'] = $event['event_end_time'];
            $submit_data['return_money'] = $event['return_money'];
            $submit_data['return_score'] = $event['return_score'];
            $submit_data['return_point'] = $event['return_point'];
            $GLOBALS['db']->autoExecute(DB_PREFIX."event_submit",$submit_data,"INSERT");
            $submit_id = $GLOBALS['db']->insert_id();
            if($submit_id)
            {

                foreach($field_ids as $field_id)
                {
                    $current_result =  strim($results[$field_id]);
                    $field_data = array();
                    $field_data['submit_id'] = $submit_id;
                    $field_data['field_id'] = $field_id;
                    $field_data['event_id'] = $event_id;
                    $field_data['result'] = $current_result;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."event_submit_field",$field_data,"INSERT");
                }
        
                if($event['is_auto_verify']==1)
                {
                    //自动审核，发券
                    $GLOBALS['db']->query("update ".DB_PREFIX."event set submit_count = submit_count+1 where id=".$event_id." and submit_count + 1 <= total_count and total_count > 0");
                    $sn = verify_event_submit($submit_id);
                }
                	
                //同步分享
                $title = "报名参加了".$event['name'];
                $content = "报名参加了".$event['name']." - ".$event['brief'];
                $url_route = array(
                    'rel_app_index'	=>	'index',
                    'rel_route'	=>	'event#'.$event['id'],
                    'rel_param' => ''
                );
                	
                require_once(APP_ROOT_PATH."system/model/topic.php");
                $tid = insert_topic($content,$title,$type="eventsubmit",$group="", $relay_id = 0, $fav_id = 0,$group_data ="",$attach_list=array(),$url_route);
                if($tid)
                {
                    $GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
                }
                	
                require_once(APP_ROOT_PATH."system/model/user.php");
                modify_account(array("score"=>"-".$event['score_limit']), $user_id,"活动报名：".$event['name']);
        
                $data['status'] = 1;
                $data['info'] = "报名成功";
                if($sn){
                    $data['info'].="，验证码：".$sn;
                    $data['sn']=$sn;
                }
        
                rm_auto_cache("event",array("id"=>$event['id']));
                return output($root,$data['status'],$data['info']);
            }
            else
            {
                $data['status'] = 0;
                $data['info'] = "报名失败";
                return output($root,$data['status'],$data['info']);
            }
        }
    }
    
    
    /**
     * 活动图文详情接口
     * 输入：
     * data_id: int 活动ID
     *
     * 输出：
     * 
     * [event_info] => Array
        (
            [id] => 4
            [name] => 贵安温泉自驾游 [string] 活动名称
            [content] => [string] 活动详情
        )

     **/
    public function detail(){
        $root = array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
    
        /*业务逻辑*/
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
    
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        //获取优惠数据
        require_once(APP_ROOT_PATH."system/model/event.php");
        $event_info = get_event($data_id);
        if($event_info){
            $root['id'] = $event_info['id'];
        }else{
            return output($root,0,"活动不存在");
        }
        
        //判断是否收藏
        if($user_id){
            $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id." and event_id=".$event_info['id']);
            $user_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id);
            if($count){
                $root['is_collect']=1;
            }else {
                $root['is_collect']=0;
            }
            $root['collect_count']=$user_count;
        }
        
        $data['id'] = $event_info['id'];
        $data['name'] = $event_info['name'];
        $data['content'] = get_abs_img_root($event_info['phone_description'],150);
        
        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title']="图文详情";
        $root['event_info'] = $data;
        return output($root);
    }
    
    
    /**
     * 收藏活动 
     * 输入：id 活动id
     * 
     **/
    public function add_collect()
    {
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
    
        $user_login_status = check_login();
        if($user_login_status==LOGIN_STATUS_NOLOGIN){
            $root['user_login_status'] = $user_login_status;
            $root['jump'] = wap_url("index","user#login");
            $status = 0;
            $info = '请先登录';
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
    
            $event_id = intval($GLOBALS['request']['id']);
            $event_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event where id = ".$event_id." and is_effect = 1");
            if($event_info)
            {
                $sql = "INSERT INTO `".DB_PREFIX."event_sc` (`id`,`event_id`, `uid`, `add_time`) select '0','".$event_info['id']."','".$user_id."','".get_gmtime()."' from dual where not exists (select * from `".DB_PREFIX."event_sc` where `event_id`= '".$event_info['id']."' and `uid` = ".$user_id.")";
                $GLOBALS['db']->query($sql);
                if($GLOBALS['db']->affected_rows()>0){
                    $root['is_collect'] = 1;
                    $info = "收藏成功";
                    $status = 1;
                }
                else
                {
                    $root['is_collect'] = 1;
                    $info = "您已经收藏了该商品";
                    $status = 1;
                }
                $root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id);
                 
            }
        }
        
        return output($root,$status,$info);
    }
    
    /**
     * 取消收藏 
     * 输入活动id：id；
     * */
    public function del_collect()
    {
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
    
        $user_login_status = check_login();
        if($user_login_status==LOGIN_STATUS_NOLOGIN){
            $root['user_login_status'] = $user_login_status;
            $status = 0;
            $root['jump'] = wap_url("index","user#login");
            $info = '请先登录';
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
    
            $event_id = intval($GLOBALS['request']['id']);
            $event_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event where id = ".$event_id." and is_effect = 1");
            if($event_info)
            {
                $sql = "delete from  ".DB_PREFIX."event_sc where uid=".$user_id." and event_id=".$event_id;
                $GLOBALS['db']->query($sql);
                if($GLOBALS['db']->affected_rows()>0){
                    $root['is_collect'] = 0;
                    $info = "取消成功";
                    $status = 1;
                }
                else
                {
                    $root['is_collect'] = 0;
                    $info = "您已经取消收藏";
                    $status = 1;
                }
                $root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_sc where uid=".$user_id);
                	
            }
        }
        return output($root,$status,$info);
    }
    
    /** 
     * 活动报名页面 
     * 输入：
     * 活动id：id
     * 输出：
     * 活动id：id
     * [field] => Array
        ( 
            [0]=>array(
                [id] => 4
                [event_id] => 2
                [field_show_name] => [string] 手机号、
                [field_type] => 0/1
                [value_scope] =>array(   //下拉框的值
                    [0]=>1-10
                    [1]=>11-20
                    ……
                )
                [sort] => 1
                [value]=>1-10     //当前值
            )
        )
     * */
    public function event_submit() {
        //检查用户
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $data_id = intval($GLOBALS['request']['data_id']);
        
        $field=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id=".$data_id);
        foreach ($field as $t => $v){
            if($v['field_type']==1){
                $value_attr=explode(' ',$v['value_scope']);
                $new_value=array();
                foreach ($value_attr as $v){
                    if(strim($v)!=''){
                        $new_value[]=$v;
                    }
                }
                $value=$new_value[0];
                $field[$t]['app_value_scope']=$new_value;
                $value_scope=implode(",", $new_value);
                $field[$t]['value_scope']=$value_scope;
                $field[$t]['value']=$value;
            }
        }
       
        //获取字段的初始值
        $submit_id=$GLOBALS['db']->getRow("select id from ".DB_PREFIX."event_submit where event_id=".$data_id." and user_id=".$user_id);
        if($submit_id){
            $value=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_submit_field where submit_id=".$submit_id['id']);
            foreach ($field as $t => $v){
                foreach ($value as $tt => $vv){
                    if($v['id']==$vv['field_id']){
                        $field[$t]['value']=$vv['result'];
                    }
                }
            }
            $submit_status=1;
        }
        
        $root['submit_status']=$submit_status?$submit_status:0;
        $root['id']=$data_id;
        $root['field']=$field;
        $root['page_title']="活动提交";
        
        return output($root);
    }
    
    public function reviews()
    {
        $event_id = intval($GLOBALS['request']['data_id']);
    
        /*点评数据*/
        require_once(APP_ROOT_PATH."system/model/review.php");
        require_once(APP_ROOT_PATH."system/model/user.php");
    
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : abs($page);
    
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
         
        /*获点评数据*/
        $dp_list = get_dp_list($limit,$param=array("event_id"=>$event_id),"","");
        $format_dp_list = array();
    
        $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'supplier_location_dp WHERE event_id = '.$event_id;
         
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
        $sql = 'SELECT name,avg_point FROM '.DB_PREFIX.'event WHERE id='.$event_id;
        $event_info = $GLOBALS['db']->getRow($sql);
        $event_info['avg_point_percent'] = round($event_info['avg_point'] / 5 * 100, 1);
        $event_info['avg_point'] = round($event_info['avg_point'], 1);
        $root['supplier_info'] = $event_info;
         
        $root['page_title'] = $event_info['name'].'-'.'全部评价';
    
    
        return output($root);
    }
    
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
    
        return $str;
    }
}

