<?php
/**
 * @desc      
 * @author    吴庆祥
 * @since     2017-02-08 16:11  
 */
class dist_undeliverApiModule extends MainBaseApiModule{
    public function index(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }

        $root['page_title'] = "消费券验证";


        // 今日收入
        $today_order_sql="select sum(money) from ".DB_PREFIX."distribution_money_log where money>0 and create_time >= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and create_time <= ".(to_timespan(to_date( NOW_TIME,'Ymd')) + ( 24 * 60 * 60 ))." and distribution_id=".$dist_info['id'];
        $root['today_money'] = number_format($GLOBALS['db']->getOne($today_order_sql),2);
        // 今日配送数量
        $today_money_sql="select count(*) from ".DB_PREFIX."distribution_coupon where confirm_time >= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and confirm_time <= ".(to_timespan(to_date( NOW_TIME,'Ymd')) + ( 24 * 60 * 60 ))." and distribution_id=".$dist_info['id'];
        $root['today_delivery_order'] = intval($GLOBALS['db']->getOne($today_money_sql));
        
        // 昨日收入
        $yesterday_money_sql="select sum(money) from ".DB_PREFIX."distribution_money_log where money>0 and create_time <= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and create_time >= ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 24 * 60 * 60 ))." and distribution_id=".$dist_info['id'];
        $root['yesterday_money'] = number_format($GLOBALS['db']->getOne($yesterday_money_sql),2);

        //昨日配送数量
        $not_delivery_sql="select count(*) from ".DB_PREFIX."distribution_coupon where confirm_time <= ".to_timespan(to_date( NOW_TIME,'Ymd'))." and confirm_time >= ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 24 * 60 * 60 ))." and distribution_id=".$dist_info['id'];
        $root['yesterday_delivery_order'] = intval($GLOBALS['db']->getOne($not_delivery_sql));

        //待发货订单数量
        $refund_order_sql="select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join ".DB_PREFIX."deal_order_item AS d on do.order_sn=d.order_sn  where do.type=4 and do.distribution_id=".$dist_info['id']." and do.pay_status=2  and do.delivery_status in (0,1) and  do.order_status=0 and d.delivery_status=0 and d.refund_status in (0,3)";
        $root['no_send_order']=intval($GLOBALS['db']->getOne($refund_order_sql));
        
        //待配送订单数量
        $no_delivery_sql="select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join ".DB_PREFIX."deal_order_item AS d on do.order_sn=d.order_sn  where do.type=4 and do.distribution_id=".$dist_info['id']." and do.pay_status=2  and do.delivery_status = 2  and  do.order_status=0 and d.delivery_status=1 and d.refund_status<>2 ";
        $root['no_delivery_order']=intval($GLOBALS['db']->getOne($no_delivery_sql));


        $root['dist_info'] = $dist_info;
        return output($root);
    }
    public function scope(){
        $root = array();
        $account_info = $GLOBALS['dist_info'];
        $root['dist_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"驿站未登录");
        }
        $root['page_title']="服务范围";
        $distribution_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=".$account_info['id']);
        $root['distribution']=$distribution_info;
        $root['baidu_m_key'] = app_conf('BAIDU_MAP_APPKEY');
        return output($root,1);
    }

    /**
     * 商家配送点
     * @return mixed 
     */
    public function point_list()
    {
        $root = array();
        $account_info = $GLOBALS['dist_info'];
        $root['dist_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"驿站未登录");
        }

        $distpSql = 'SELECT * FROM '.DB_PREFIX.'distribution_shipping WHERE dist_id = '.$account_info['id'].' AND is_delete=0';
        $distList = $GLOBALS['db']->getAll($distpSql);

        $root['list'] = $distList;

        $root['page_title']="配送点列表";
        return output($root);
    }

    /**
     * @desc 简单验证提货码
     * @author    吴庆祥
     * @return unknown_type
     */
    public function index_check(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }

        // 获取参数
        $coupon_pwd  = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $check_result = $this->_checkDeliveryCode($dist_info, $coupon_pwd);
        if($check_result['dist_coupon']['confirm_time']){
            return output($root,0, "该提货码已经使用过了");
        }
        if(!$check_result['status']){
            return output($root,0,$check_result['info']);
        }
        $root = array_merge($root, $check_result);
		$root['url'] = wap_url("dist","undeliver#deliverycode_check",array('coupon_pwd' =>$coupon_pwd ));
        $root['jump'] = wap_url("dist","undeliver#deliverycode_check",array('coupon_pwd' =>$coupon_pwd ));
        return output($root,1, $root['info']);
    }

    /**
     * @desc 检验提货码是否有效
     * @author    吴庆祥
     * @param $dist_info
     * @param $coupon_pwd
     */
    private function _checkDeliveryCode($dist_info, $coupon_pwd){
        $data=array();
        $data['status']=0;
        $coupon_row=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution_coupon where sn='".$coupon_pwd."' and distribution_id=".$dist_info['id']);
        if(!$coupon_row){
            $data['info']="该提货码无效";
            return $data;
        }
        $data['dist_coupon']=$coupon_row;
        $data['status']=1;
        return $data;
    }
    public function deliverycode_check(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        $root['page_title'] = "确认送达";
        // 获取提货码并校验
        $coupon_pwd  = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $check_result = $this->_checkDeliveryCode($dist_info, $coupon_pwd);
        if($check_result['dist_coupon']['confirm_time']){
            return output($root,0, "该提货码已经使用过了");
        }
        $root['coupon_pwd']=$coupon_pwd;
        if ($check_result['status'] != 1) {
            return output($root,0, $check_result['info']);
        }
        $root = array_merge($root, $check_result);
        $dist_coupon=$root['dist_coupon'];
        //输出下单用户信息
        $deal_order=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id=".$dist_coupon['order_id']);
        $user_info=array();
        $user_info['mobile']=$deal_order['mobile'];
        $user_info['address']=$this->_getAddressFromDealOrder($deal_order);
        $user_info['user_name']=$deal_order['user_name'];
        $root['user_info']=$user_info;
		if($deal_order['cod_money']>0){
			$deal_order['payment_info']=$GLOBALS['db']->getRow("select pn.id,pn.money,pn.payment_config,p.class_name,p.name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id=p.id where order_id = ".$dist_coupon['order_id']." and p.class_name='Cod' and pn.is_paid=1");
			if($deal_order['payment_info']){
				$rel=get_payment_name_rel($deal_order['cod_mode']);
				$deal_order['payment_info']['name']=$deal_order['payment_info']['name'].$rel;
			}else{
				$deal_order['payment_info']['name']="货到付款(现金)";
				$deal_order['payment_info']['money']=$deal_order['cod_money'];
			}
			$deal_order['payment_info']['money']=format_price($deal_order['payment_info']['money']);
		}
		$root['payment_info']=$deal_order['payment_info'];
        //输出商品信息
        $deal_order_item=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id=".$dist_coupon['order_id']);
        require APP_ROOT_PATH."system/model/deal_order.php";
        foreach($deal_order_item as $key=>$val){
            $deal_order_item[$key]["unit_price"]=round($val['unit_price'],2);
            $deal_order_item[$key]["order_status"]=get_order_item_status($val,$deal_order['location_id']);
        }
        $root['deal_order_item']=$deal_order_item;
        return output($root, $check_result['status'], $check_result['msg']);
    }
    public function deliverycode_use(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        // 获取参数
        $coupon_pwd         = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        if(!$coupon_pwd){
            return output($root,0,"提货码为空");
        }
        $distribution_coupon=$GLOBALS['db']->getRow("select order_id,id,sn from ".DB_PREFIX."distribution_coupon where sn='".$coupon_pwd."'");
        if(!$distribution_coupon){
            return output($root,0,"驿站订单不存在");
        }
        //确认收货
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $result = distribution_confirm_delivery($distribution_coupon['order_id']);
        if($result['status']){
            $GLOBALS['db']->query("update ".DB_PREFIX."distribution_coupon set confirm_time=".NOW_TIME.",is_balance=1 where sn='".$coupon_pwd."'");
            if(!$GLOBALS['db']->affected_rows()){
                return output($root,0,"确认订单失败");
            }
        }else{
            return output($root,0,"确认收货失败");
        }
        $root = array_merge($root, $result);
        $root['jump']=wap_url("dist","undeliver#verify_log_detail",array("coupon_pwd"=>$distribution_coupon['sn']));
        return output($root, $root['status'], $root['info']);
    }
    /**
     * @desc 根据订单字段获取地址
     * @author    吴庆祥
     * @param $deal_order
     * @return string
     */
    private  function _getAddressFromDealOrder($deal_order){
        $address='';
        if($deal_order){
            $address=$deal_order['address'].$deal_order['street'].$deal_order['doorplate'];
        }
        return $address;
    }
    public function verify_log_list(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"商户未登录");
        }
        $root['page_title'] = "验证记录";

        /*获取参数*/
        $limit = formatLimit(intval($GLOBALS['request']['page']));
        $result = $GLOBALS['db']->getAll("select ucl.*,do.*,u.user_name  from ".DB_PREFIX."distribution_coupon ucl left join ".DB_PREFIX."user u on u.id = ucl.user_id left join ".DB_PREFIX."deal_order do on ucl.order_id=do.id where ucl.distribution_id='".$dist_info['id']."' and is_balance=1 order by ucl.id desc ".$limit);
        $count  = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution_coupon");
        //分页
        foreach($result as $key =>$val){
            $result[$key]['confirm_time']=to_date($val['confirm_time']);
            $result[$key]['address']=$this->_getAddressFromDealOrder($val);
            $result[$key]['url']=wap_url("dist","undeliver#verify_log_detail",array("coupon_pwd"=>$val['sn']));
        }
        $root['data'] = $result ? $result : array();
        $root['total'] = $count;

        return output($root);
    }

    /**
     * 搜索消费券
     */
    public function search_log(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }

        $root['page_title'] = "验证记录";
        // 获取参数
        $coupon_pwd         = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $result = $GLOBALS['db']->getAll("select ucl.*,do.*,u.user_name  from ".DB_PREFIX."distribution_coupon ucl left join ".DB_PREFIX."user u on u.id = ucl.user_id left join ".DB_PREFIX."deal_order do on ucl.order_id=do.id where ucl.confirm_time!=0 and ucl.sn='{$coupon_pwd}' order by ucl.id desc ");
        foreach($result as $key =>$val){
            $result[$key]['confirm_time']=to_date($val['confirm_time']);
            $result[$key]['address']=$this->_getAddressFromDealOrder($val);
            $result[$key]['url']=wap_url("dist","undeliver#verify_log_detail",array("coupon_pwd"=>$val['sn']));
        }
        $root['data'] = $result ? $result : array();

        return output($root);

    }
    public function verify_log_detail(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        $root['page_title'] = "验证详情";
        // 获取提货码并校验
        $coupon_pwd  = strim( str_replace(' ', '', $GLOBALS['request']['coupon_pwd']) );
        $check_result = $this->_checkDeliveryCode($dist_info, $coupon_pwd);
        $root['coupon_pwd']=$coupon_pwd;
        if ($check_result['status'] != 1) {
            return output($root, 0, $check_result['info']);
        }
        $root = array_merge($root, $check_result);
        $dist_coupon=$root['dist_coupon'];

        //输出下单用户信息
        $deal_order=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id=".$dist_coupon['order_id']);
        $user_info=array();
        $user_info['mobile']=$deal_order['mobile'];
        $user_info['address']=$this->_getAddressFromDealOrder($deal_order);
        $user_info['user_name']=$deal_order['user_name'];
        $user_info['confirm_time']=to_date($dist_coupon['confirm_time']);
        $user_info['sn']=implode(" ",str_split($coupon_pwd,4));
        $root['user_info']=$user_info;
		if($deal_order['cod_money']>0){
			$deal_order['payment_info']=$GLOBALS['db']->getRow("select pn.id,pn.money,pn.payment_config,p.class_name,p.name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id=p.id where order_id = ".$dist_coupon['order_id']." and p.class_name='Cod' and pn.is_paid=1");
			if($deal_order['payment_info']){
				$rel=get_payment_name_rel($deal_order['cod_mode']);
				$deal_order['payment_info']['name']=$deal_order['payment_info']['name'].$rel;
			}else{
				$deal_order['payment_info']['name']="货到付款(现金)";
				$deal_order['payment_info']['money']=$deal_order['cod_money'];
			}
			$deal_order['payment_info']['money']=format_price($deal_order['payment_info']['money']);
		}
		$root['payment_info']=$deal_order['payment_info'];
        //输出商品信息
        $deal_order_item=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id=".$dist_coupon['order_id']);
        require APP_ROOT_PATH."system/model/deal_order.php";
        foreach($deal_order_item as $key=>$val){
            $deal_order_item[$key]["unit_price"]=round($val['unit_price'],2);
            $deal_order_item[$key]["order_status"]=get_order_item_status($val,$deal_order['location_id']);
        }
        $root['deal_order_item']=$deal_order_item;
        return output($root, $check_result['status'], $check_result['msg']);
    }
}