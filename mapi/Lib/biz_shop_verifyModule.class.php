]<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
class biz_shop_verifyApiModule extends MainBaseApiModule
{
    
    /**
     * 	 消费券验证接口
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
    
     Array
     (
     [biz_user_status] => 1                    int 商户登录状态 0未登录/1已登录
     [is_auth] => 0                              int 模块操作权限 0没有权限 / 1有权限
     [status] => 1                               int 结果状态 0失败 1成功
    
     [today_order] => 1                        今日订单数量
     [today_money] => 5.00                     今日成交金额
     [yesterday_money] => 0.00                 昨日成交金额
     [not_delivery] => 68                      待发货数量
    
     [account_info] => Array                     登陆信息
        (
            [id] => 8
            [account_name] => qthy
            [account_password] => 21232f297a57a5a743894a0e4a801fc3
            [supplier_id] => 22                      商户id                
        )
    
     )
    
    
     */
    
    public function index(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        $root['page_title'] = "消费券验证";
        
        //返回买单记录权限
        if(!check_module_auth('store_pay_order')){
            $root['store_pay_order_auth'] = 0;
            $root['store_pay_order_auth_info'] = "没有操作权限";
        }else{
            $root['store_pay_order_auth'] = 1;
        }
        
        // 今日订单数量
        $today_order_sql="select count(distinct(doi.order_id)) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.is_delete = 0 AND (do.type = 5 or do.type = 6) AND do.pay_status = 2 and do.create_time >= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and do.create_time < ".(to_timespan(to_date( NOW_TIME,'Ymd')) + ( 24 * 60 * 60 ));
        $root['today_order'] = intval($GLOBALS['db']->getOne($today_order_sql));
    
        // 今日成交金额
        $today_money_sql="select sum(doi.total_price) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.is_delete = 0 AND  (do.type = 5 or do.type = 6) AND do.pay_status = 2 and do.create_time >= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and do.create_time < ".(to_timespan(to_date( NOW_TIME,'Ymd')) + ( 24 * 60 * 60 ));
        $root['today_money'] = number_format($GLOBALS['db']->getOne($today_money_sql),2);
    
        // 昨日成交金额
        $yesterday_money_sql="select sum(doi.total_price) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.is_delete = 0 AND (do.type = 5 or do.type = 6) AND do.pay_status = 2 and do.create_time >= ".(to_timespan(to_date(NOW_TIME , 'Ymd' )) - ( 24 * 60 * 60 ) )." and do.create_time < ".to_timespan( to_date(NOW_TIME ,'Ymd') );
        $root['yesterday_money'] = number_format($GLOBALS['db']->getOne($yesterday_money_sql),2);
    
        //待发货数量
        $not_delivery_sql="select count(distinct(doi.order_id)) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.is_delete = 0 AND  (do.type = 5 or do.type = 6) AND do.pay_status = 2 and doi.delivery_status=0 and ( doi.refund_status=0 or doi.refund_status=3 ) and doi.is_shop = 1 ";
        $root['not_delivery'] = intval($GLOBALS['db']->getOne($not_delivery_sql));

        //商户是否支持到店买单
        $root['is_store_payment'] = $GLOBALS['db']->getOne("select is_store_payment from ".DB_PREFIX."supplier where id =".$account_info['supplier_id']);
        if($root['is_store_payment']==1){
            $root['open_store_payment_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location where id in(".implode(",",$account_info['location_ids']).") and open_store_payment=1 order by is_main desc");
        }else{
            $root['open_store_payment_count']=0;
        }

        $root['account_mobile'] = substr_replace($account_info['mobile'],'****',3,4);
        $root['account_info'] = $account_info;
        return output($root);
        
    }
    
    
    
    /**
     * 	 消费简单验证接口，不验证门店     输入验证码用
     *
     * 	 输入:
     *  coupon_pwd:string  消费券序列号
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  
     *  有权限的情况下返回以下内容
        Array
        (
            [coupon_type] => 1                              int     消费券类型，1团购 ，2优惠券，3活动 ，4自提
            [status] => 0                                   status  1验证成，0失败
            [info] => 该券已于2017-01-13 15:42:07使用过                info    提示信息
        )

     *  
     **/
    public function index_check(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        /*获取参数*/
        $page = intval($GLOBALS['request']['page']); //分页
        $page=$page==0?1:$page;

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //返回商户权限
        if(!check_module_auth()){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }

        // 获取参数
        $coupon_pwd  = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $type = substr($coupon_pwd, 0, 1);
        if($type==5){
            $lid=$GLOBALS['db']->getOne("select location_id from ".DB_PREFIX."dc_coupon where sn = '".$coupon_pwd."'");
        }else{
            $lid=0;
        }
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $check_result = biz_unified_check_coupon($account_info, $coupon_pwd, $lid, 1);

        $result['status']           = $check_result['status']; 
        $result['info']             = $check_result['info'];
        $result['coupon_type']      = $check_result['coupon_type'];
       
        $root = array_merge($root, $result);
        
        $root['url'] = wap_url("biz","shop_verify#coupon_check",array('coupon_pwd' =>$coupon_pwd ));
        
        return output($root, $result['status'], $result['info']);
    }
    
    
    /**
     * 	 消费简单验证接口，不验证门店   扫码专用
     *
     * 	 输入:
     *  coupon_pwd:string  消费券序列号
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
    Array
    (
        [coupon_type] => 1                              int     消费券类型，1团购 ，2优惠券，3活动 ，4自提
        [status] => 0                                   status  1验证成，0失败
        [info] => 该券已于2017-01-13 15:42:07使用过                info    提示信息
    )
    
     *
     **/
    public function scan_index_check(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //返回商户权限
        if(!check_module_auth("shop_verify","index_check")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
        // 获取参数
        $coupon_pwd  = strim($GLOBALS['request']['coupon_pwd']);
        
//         $reg = '|([^\d]+)(\d+)|is' ;
//         preg_match($reg, $coupon_pwd, $coupon_pwd);
        
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $check_result = biz_unified_check_coupon($account_info, $coupon_pwd, 0, 1);
    
        $result['status']           = $check_result['status'];
        $result['info']             = $check_result['info'];
        $result['coupon_type']      = $check_result['coupon_type'];
         
        $root = array_merge($root, $result);
    
        $root['url'] = wap_url("biz","shop_verify#coupon_check",array('coupon_pwd' =>$coupon_pwd[2] ));
    
        return output($root, $result['status'], $result['info']);
    }
    
    
    /**
     * 	 消费券验证接口   不验证门店
     *
     * 	 输入:
     *  location_id:int 门店id
     *  coupon_pwd:string  消费券序列号
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
        
    Array
    (
        [biz_user_status] => 1                    int 商户登录状态 0未登录/1已登录
        [is_auth] => 0                              int 模块操作权限 0没有权限 / 1有权限
        [coupon_type] => 1                          int 消费券类型，1团购 ，2优惠券，3活动 ，4自提
        [status] => 1                               int 结果状态 0失败 1成功

        coupon_data 根据不同的消费券，返回数据有所不同

        团购券
        [coupon_data] => Array
            (
                [sub_name] => 目鱼丸 [1斤]           string     商品短名称
                [unit_price] => 90.0000             decimal    商品单价
                [begin_time] => 0                   string     有效开始时间
                [end_time] => 永久                                                 string     有效结束时间
                [number] => 5                       int        总的券数（购买的数量）
                [count] => 5                        int        剩余有效券数
                [coupon_pwd]=>148418525588          string     消费券序列号
            )

        优惠券
        [coupon_data] => Array
        (
            [name] => 大鱼优惠券7折扣                                string     商品名称
            [youhui_type] => 1                  int        1折扣券 ，0减免券
            [youhui_value] => 7                 int        相对于优惠类型的优惠值，折扣时7%，减免时直接扣款
            [begin_time] => 0                   string     有效开始时间
            [end_time] => 0                     string     有效结束时间
            [coupon_pwd]=>148418525588          string     消费券序列号
        )

        活动券
        [coupon_data] => Array
        (
            [name] => 活动测试                                                  string     活动名称
            [city_id] => 15                     int        城市id
            [address] => 晋安区融侨广场5楼                   string     地址信息
            [event_begin_time] => 0             string     有效开始时间
            [event_end_time] => 0               string     有效结束时间
            [user_id] =>                        int        用户id
            [user_name] => hhhcx                string     报名用户名
            [mobile] => 18760546541             string     报名手机号
            [coupon_pwd]=>148418525588          string     消费券序列号
        )

        自提券
        [coupon_data] => Array
        (
            [sub_name] => 真皮沙发 [1G,中国]     string       商品短名称
            [unit_price] => 100.0000            decimal      商品单价
            [begin_time] => 0                   string       有效开始时间
            [end_time] => 永久                                                string       有效结束时间
            [number] => 1                       int          总的券数（购买的数量）
            [attr_str] => 1G中国                                           string       商品属性
            [count] => 1                        int          剩余有效券数
            [coupon_pwd]=>148418525588          string       消费券序列号
        )

        门店信息，门店可能有多个
        [location_list] => Array
        (
            [0] => Array
            (
                [id] => 91
                [name] => 福州大鱼丸
            )
        )
    )
     */
    public function coupon_check(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //返回商户权限
        if(!check_module_auth('shop_verify','coupon_check')){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
        $root['page_title'] = "确认消费";
        // 获取参数
        $coupon_pwd  = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        
        $type = substr($coupon_pwd, 0, 1); // $type = 1团购 ，2优惠券，3活动 ，4自提, 5订餐 1和4验证使用都是用一个方法
        if($type==5){
            $lid=$GLOBALS['db']->getOne("select location_id from ".DB_PREFIX."dc_coupon where sn = '".$coupon_pwd."'");
        }else{
            $lid=0;
        }
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $check_result = biz_unified_check_coupon($account_info, $coupon_pwd, $lid,1);
        
        if ($check_result['status'] != 1) {
            return output($root, $check_result['status'], $check_result['info']);
        } 
         
        $root = array_merge($root, $check_result);
      
        //获取商户支持的门店
        $location_list = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "supplier_location where is_effect=1 and id in(" . implode(",", $account_info['location_ids']) . ")");
        $root['location_list'] = $location_list?$location_list:array();

        //券码支持的门店
        if($type==1){
            $sql = "select l.location_id from ".DB_PREFIX."deal_coupon as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.deal_id where d.password = ".$coupon_pwd;
            $deal_location_id = $GLOBALS['db']->getAll($sql);
            foreach ($deal_location_id as $kk => $vv){
                $dealo_location_id[] = $vv['location_id'];
            }
//             $deal_location_id = array_column($deal_location_id, 'location_id');
            foreach ($root['location_list'] as $k => $v ){
                if(!in_array($v['id'], $dealo_location_id)){
                    unset($root['location_list'][$k]);
                }
            }
        }elseif($type==2){
            $sql = "select l.location_id from ".DB_PREFIX."youhui_log as y left join ".DB_PREFIX."youhui_location_link as l on l.youhui_id = y.youhui_id where y.youhui_sn = ".$coupon_pwd;
            $youhui_location_id = $GLOBALS['db']->getAll($sql);
            foreach ($youhui_location_id as $kk => $vv){
                $youhuio_location_id[] = $vv['location_id'];
            }
//             $youhui_location_id = array_column($youhui_location_id, 'location_id');
            foreach ($root['location_list'] as $k => $v ){
                if(!in_array($v['id'], $youhuio_location_id)){
                    unset($root['location_list'][$k]);
                }
            }
        }elseif($type==3){
            $sql = "SELECT el.location_id FROM ".DB_PREFIX."event_submit as e left join ".DB_PREFIX."event_location_link as el on el.event_id = e.event_id where e.sn = ".$coupon_pwd;
            $event_location_id = $GLOBALS['db']->getAll($sql);
            foreach ($event_location_id as $kk => $vv){
                $evento_location_id[] = $vv['location_id'];
            }
//             $event_location_id = array_column($event_location_id, 'location_id');
            foreach ($root['location_list'] as $k => $v ){
                if(!in_array($v['id'], $evento_location_id)){
                    unset($root['location_list'][$k]);
                }
            }
        }elseif($type==4){
            $sql = "select l.location_id from ".DB_PREFIX."deal_coupon as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.deal_id where d.password = ".$coupon_pwd;
            $deal_location_id = $GLOBALS['db']->getAll($sql);
            foreach ($deal_location_id as $kk => $vv){
                $evento_location_id[] = $vv['location_id'];
            }
//             $deal_location_id = array_column($deal_location_id, 'location_id');
            foreach ($root['location_list'] as $k => $v ){
                if(!in_array($v['id'], $evento_location_id)){
                    unset($root['location_list'][$k]);
                }
            }
        }
        
        //门店列表重新排序
        array_multisort($root['location_list']);
        //门店列表的第一个作为默认打钩
        if($root['location_list'][0]){
            $root['location_list'][0]['is_check']=1;
        }
        //print_r($root);exit;
        // 商品名称，价格
        return output($root, $check_result['status'], $check_result['msg']);
         
    }
    
    /**
     * 	 消费券使用接口
     *
     * 	 输入:
     *  location_id:int         门店id
     *  coupon_pwd:string       消费券序列号
     *  coupon_use_count:int    使用优惠券数量
     
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     
     *  有权限的情况下返回以下内容
    Array
    (
        [biz_user_status] => 1                      int     商户登录状态 0未登录/1已登录
        [is_auth] => 1                              int     模块操作权限 0没有权限 / 1有权限
        [coupon_type] => 1                          int     消费券类型，1团购 ，2优惠券，3活动 ，4自提
        [status] => 1                               int     结果状态 0失败 1成功

        [location_id] => 91                         int     门店id
        [coupon_pwd] => 148424650576                int     输入的消费券序列号
        [coupon_use_count] => 1                     int     使用券的数量
        [name] => 鲨鱼丸                                                                           sting   商品名称（短名称）
        [info] => 核销成功                                                                      sting   提示信息

    )

     */
    public function coupon_use(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //返回商户权限
        if(!check_module_auth('shop_verify','coupon_use')){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        // 获取参数
        $coupon_pwd = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $type = substr($coupon_pwd, 0, 1);
        if($type==5){
            $location_id=$GLOBALS['db']->getOne("select location_id from ".DB_PREFIX."dc_coupon where sn = '".$coupon_pwd."'");
        }else{
            $location_id = intval($GLOBALS['request']['location_id']);
        }
        $coupon_use_count   = intval($GLOBALS['request']['coupon_use_count']);
        
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $use_result = biz_unified_use_coupon($account_info, $coupon_pwd, $location_id, $coupon_use_count);
        $root = array_merge($root, $use_result);
         
        return output($root, $root['status'], $root['info']);
        
    }
    /**
     * 	 消费券使用接口
     *
     * 	 输入:
     *  location_id:int         门店id
     *  coupon_pwd:string       消费券序列号
     *  coupon_use_count:int    使用优惠券数量
      
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
      
     *  有权限的情况下返回以下内容
     
     [data] => Array
        (
            [0] => Array
                (
                    [id] => 7                         int     日志id
                    [uid] => 104                      int     消费用户id
                    [data_id] => 4                    int     对应查询日志详情的id
                    [name] => 古典真皮沙发 [1G,中国]    string  商品名称
                    [pwd] => 448424789411             int     输入的消费券序列号
                    [type] => 4                       int     消费券类型，1团购 ，2优惠券，3活动 ，4自提
                    [location_id] => 91               int     门店id
                    [create_time] => 1484337078       int     创建时间
                )
        )

        [page] => Array
        (
            [page] => 1
            [page_total] => 1
            [page_size] => 20
            [data_total] => 4
        )
     
     */
    public function coupon_use_log(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商户权限
        if(!check_module_auth('shop_verify','coupon_use_log')){
            $root['search_log_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['search_log_auth'] = 1;
        }

        $root['page_title'] = "验证记录";
         
        /*获取参数*/
        $page = intval($GLOBALS['request']['page']); //分页
        $page=$page==0?1:$page;
        
        
        //分页
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
         
       
        $lids = implode(",", $account_info['location_ids']);
        $result = $GLOBALS['db']->getAll("select ucl.*, u.user_name  from ".DB_PREFIX."use_coupon_log ucl left join ".DB_PREFIX."user u on u.id = ucl.uid where location_id in($lids) order by ucl.id desc limit ".$limit);
        $count  = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."use_coupon_log where location_id in($lids) ");
        //分页
        $page_total = ceil($count/$page_size);
        $root['data'] = $result ? $result : array();
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        //print_r($root);exit;
        return output($root);
    }
    
    /**
     * 搜索消费券
     */
    public function search_log(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        $root['page_title'] = "验证记录";
        // 获取参数
        $coupon_pwd         = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
       
        $location_ids = $account_info['location_ids'];
        $location_ids = join($location_ids, ',');
        $result = $GLOBALS['db']->getAll("select ucl.*, u.user_name  from ".DB_PREFIX."use_coupon_log ucl left join ".DB_PREFIX."user u on u.id = ucl.uid where location_id in({$location_ids}) and pwd='{$coupon_pwd}' order by ucl.id desc ");
        $root['data'] = $result ? $result : array();
         
        return output($root);
      
    }
    /**
     * 推广二维码

     *
     * */
    public function qrcode(){
        $root = array();

        //检查用户,用户密码
        $account_info = $GLOBALS['account_info'];
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        $root['biz_user_status'] = $account_info?1:0;
		$account_id  = intval($account_info['id']);
		$location_list = $GLOBALS['cache']->get("ACCOUNT_QRCODE_".intval($account_id));
		if($location_list===false){
			$location_ids = $account_info['location_ids'];
			$location_ids = join($location_ids, ',');
			$location_list = $GLOBALS['db']->getAll("select id,name,is_main from ".DB_PREFIX."supplier_location where id in({$location_ids}) and open_store_payment=1 order by is_main desc");


			require APP_ROOT_PATH.'system/utils/es_image.php';
			$new_pic= new es_image();
			$dir_invite=APP_ROOT_PATH.'public/attachment/invite/biz';
			if(!file_exists($dir_invite)){
				@mkdir($dir_invite);
			}
			require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
			$image = new es_imagecls();
			$img_arr=array();
			foreach($location_list as $k=>$v){
				$location_id=$v['id'];
				$store_pay_url=wap_url("index","store_pay",array('id'=>$location_id));
				$store_pay_qrcode_url=get_abs_img_root(gen_qrcode(SITE_DOMAIN.$store_pay_url,8));
				$store_pay_qrcode_path=str_replace(SITE_DOMAIN.APP_ROOT,APP_ROOT_PATH,$store_pay_qrcode_url);
				
				$thumbname_yuan=$dir_invite."/invite_biz_".$account_id."_".$location_id.".jpg";
				
				
				file_put_contents($thumbname_yuan,file_get_contents($store_pay_qrcode_path));
				//copy($store_pay_qrcode_path, $thumbname_yuan);
				$thumbname=$dir_invite."/invite_biz_".$account_id."_".$location_id."_300x300.jpg";
				// 图片大小设置,生成二维码缩略图
				$thumb=$image->thumb($thumbname_yuan,$width=300,$height=300,$thumb_type=0,true,$thumbname);
				@unlink($thumbname_yuan);

				//背景图片
				$savename_bg=$dir_invite."/invite_biz_bg_".$account_id."_".$location_id.".png";
				@unlink($savename_bg);
				//$new_pic::background_png($savename_bg,array("width"=>300,"height"=>450),array("1","192","232"));
				//copy(SITE_DOMAIN.APP_ROOT."/public/font_bg/biz_qrcode_background.png",$savename_bg);
				$image->thumb(APP_ROOT_PATH.'public/font_bg/biz_qrcode_background.png',$width=600,$height=900,$thumb_type=0,true,$savename_bg);
				// 把二维码加入背景图中
				$new_pic::water($savename_bg, $thumb['path'], $savename_bg,$alpha=100,$position="6");
				$location_list[$k]["qrcode_url"]=$store_pay_qrcode_url;
				@unlink($thumb['path']);


				//$new_pic::png_str($savename_bg,$savename_bg,$v['name'],"simhei.ttf",3,array('posY'=>375));
				//$new_pic::png_str($savename_bg,$savename_bg,"天上飘过来十五个字这些都不是事","simhei.ttf",0);
				$str_png_bg=$dir_invite."/str_".$account_id."_".$location_id.".jpg";
				$this->make_transparent_pic(200,200,$str_png_bg,$v['name']);

				$new_pic::water($savename_bg, $str_png_bg, $savename_bg,$alpha=100,$position="11");
				@unlink($str_png_bg);
				if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
				{
					syn_to_remote_image_server(str_replace(APP_ROOT_PATH,"./", $savename_bg));
				}
				
				$location_list[$k]["qrcode_urls"]=get_spec_image("./public/attachment/invite/biz/invite_biz_bg_".$account_id."_".$location_id.".png");//str_replace(substr(APP_ROOT_PATH,0,-1),SITE_DOMAIN.APP_ROOT,$savename_bg);
			}
			$GLOBALS['cache']->set("ACCOUNT_QRCODE_".intval($account_id),$location_list,300);
		}

        $root['location_list']=$location_list;
		$root['location_count']=count($location_list);
        $root['store_pay_qrcode_url']=$location_list['0']['qrcode_url'];
        $root['store_pay_qrcode_urls']=$location_list['0']['qrcode_urls'];
        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title']="买单二维码";
        return output($root);
    }
    public function make_transparent_pic($width,$height,$path,$text=''){

        $font_file=APP_ROOT_PATH.'public/font_bg/simhei.ttf';

        $text_size=imagettfbbox(15,0,$font_file,$text);
        $w=abs($text_size[2]-$text_size[0]);

        $h =abs($text_size[5]-$text_size[3]);
        //1.生成真彩图
        $img = imagecreatetruecolor($w, $h+2);
        //2.上色
        $color=imagecolorallocate($img,255,255,255);
        //3.设置透明
        imagecolortransparent($img,$color);
        imagefill($img,0,0,$color);
        //4.向画布上写字
        $textcolor=imagecolorallocate($img,0,0,0);


        //$img = $this->  pngthumb($path,$path,$width,$height);
        imagettftext($img, 15, 0, 0, $h, $textcolor, $font_file, $text);
        //5.保存

        imagepng($img,$path);
        //6.释放

        imagedestroy($img);
        return $path;
    }
}





