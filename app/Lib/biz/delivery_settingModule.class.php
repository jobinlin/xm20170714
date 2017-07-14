<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class delivery_settingModule extends BizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
        $this->check_auth();
    }
	
    
	public function index()
	{	
		init_app_page();
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);
		$id = intval($_REQUEST['id']);
		$delivery_min_money = app_conf('DELIVERY_MIN_MONEY');
		$delivery_money = $s_account_info['delivery_money'];

        $payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment where online_pay in (0,1) and is_effect = 1 and class_name <> 'Account' and class_name <> 'Voucher' and class_name <> 'Wwxjspay' order by sort desc");    	
     
        foreach($payment_list as $k=>$v)
        {
            $directory = APP_ROOT_PATH."system/payment/";
            $file = $directory. '/' .$v['class_name']."_payment.php";
            if(file_exists($file))
            {
                require_once($file);
                $payment_class = $v['class_name']."_payment";
                $payment_object = new $payment_class();
                $payment_list[$k]['display_code'] = $payment_object->get_display_code();
                $payment_list[$k]['is_bank'] = intval($payment_object->is_bank);
        
            }
            else
            {
                unset($payment_list[$k]);
            }
        }
        $order_list_sql = "select * , total_price - payment_fee as total_price from ".DB_PREFIX."supplier_delivery_charge_order where pay_status=2 and supplier_id=".$supplier_id." order by id desc";
        $order_list = $GLOBALS['db']->getAll($order_list_sql);
        $GLOBALS['tmpl']->assign("order_list",$order_list);
        $GLOBALS['tmpl']->assign("is_open_dada_delivery",$s_account_info['is_open_dada_delivery']);
        $GLOBALS['tmpl']->assign("mobile",$s_account_info['mobile']);
        $GLOBALS['tmpl']->assign("money",$s_account_info['money']);
        $GLOBALS['tmpl']->assign("payment_list",$payment_list);
		$GLOBALS['tmpl']->assign("delivery_min_money",$delivery_min_money);
		$GLOBALS['tmpl']->assign("delivery_money",$delivery_money);
		$GLOBALS['tmpl']->assign("head_title","配送设置");
		$GLOBALS['tmpl']->display("pages/delivery_setting/index.html");	
		

	
	}
	
	public function dada_acount()
	{	
		init_app_page();
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);

		$id = intval($_REQUEST['id']);
		//退款允许
		/* 业务逻辑部分 */
		if (!in_array($id, $s_account_info['location_ids'])){
		    showBizErr("没有权限",0,url("biz","location#index"));
		}
		
		$sql="select dada_account,dada_password from ".DB_PREFIX."supplier_location where id=".$id;
		$supplier_location = $GLOBALS['db']->getRow($sql);
	    if($supplier_location['dada_account'] && $supplier_location['dada_password']){
	        showBizErr("已注册达达帐户",0,url("biz","location#index"));
	    }	
		
		$GLOBALS['tmpl']->assign("head_title","达达帐户注册");
		$GLOBALS['tmpl']->assign("id",$id);
		$GLOBALS['tmpl']->display("pages/delivery_setting/dada_acount.html");	
		

	
	}
	
	public function dada_account_create()
	{
	    init_app_page();
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	
	    $id = intval($_REQUEST['id']);
	    $dada_account = strim($_REQUEST['dada_account']);
	    $dada_password = strim($_REQUEST['dada_password']);
	    $dada_confirm_password = strim($_REQUEST['dada_confirm_password']);

	    //退款允许
	    /* 业务逻辑部分 */
	    if (!in_array($id, $s_account_info['location_ids'])){
	       
	        $result['status']=0;
	        $result['info']='无权限';
	        $result['jump']=url("biz","location#index");
	        ajax_return($result);
	    }

	    $sql = "select sl.* , dc.name as city_name ,sa.mobile from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."deal_city as dc on
       sl.city_id=dc.id left join ".DB_PREFIX."supplier_account as sa on sl.id=sa.supplier_id where sl.id=".$id;
	    $supplier_location = $GLOBALS['db']->getRow($sql);
	    if($supplier_location['dada_account'] && $supplier_location['dada_password']){
	        $result['status']=0;
	        $result['info']='已注册达达帐户';
	        $result['jump']=url("biz","location#index");
	        ajax_return($result);
	        
	    }
	    
	    if($dada_account==''){
	        $result['status']=0;
	        $result['info']='请输入用户名';
	        ajax_return($result);
	    }
	    
	    if($dada_password==''){
	        $result['status']=0;
	        $result['info']='请输入密码';
	        ajax_return($result);
	    }
	    
	    if($dada_confirm_password==''){
	        $result['status']=0;
	        $result['info']='请输入确认密码';
	        ajax_return($result);
	    }
	    
	    if($dada_confirm_password !=$dada_password){
	        $result['status']=0;
	        $result['info']='确认密码不一致';
	        ajax_return($result);
	    }
  
	    require_once(APP_ROOT_PATH."system/delivery/DaDaDelivery.php");
	    $DaDaDelivery = new DaDaDelivery();
        $rand = to_date(NOW_TIME,'Yhis').rand(10,99);
	    $location['dada_shop_id']=$supplier_location['dada_shop_id'] = 'fw'.$rand.$id;	    
	    $location['dada_account']=$supplier_location['dada_account'] = $dada_account;
	    $location['dada_password']=$supplier_location['dada_password'] = $dada_password;
	    $result_data = $DaDaDelivery->createStore($supplier_location);
	    
	    if($result_data['code']==0){      
	        $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location", $location, $mode = 'UPDATE', "id=".$id, $querymode = 'SILENT');
	        $rs_row = $GLOBALS['db']->affected_rows();
	        if($rs_row>0){
	            $result['status']=1;
	            $result['info']='注册成功';
	            $result['jump']=url('biz','dc#dc_set',array('id'=>$id));
	        }else{
	            $result['status']=0;
	            $result['info']='注册失败';
	        }
	    }else{
	        $result['status']=0;
	        $result['info']=$result_data['result']['failedList'][0]['msg'];
	    }

        ajax_return($result);
	    
	
	
	}
	
	

	public function charge()
	{
	    init_app_page();
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	
	    $money = floatval($_REQUEST['money']);
	    $verify_code = strim($_REQUEST['verify_code']);
	    $sms_verify = strim($_REQUEST['sms_verify']);
	    
	    $payment_id = intval($_REQUEST['payment_id']);
	    $bank_id = strim($_REQUEST['bank_id']);
	    
	    if($money==0){
	        $result['status']=0;
	        $result['info']='请输入正确的充值金额';
	        ajax_return($result);
	    }
	    
	    //需要图形验证码

	    $mobile_phone = $s_account_info['mobile'];
	    $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
	    if($mobile_data['code']!=$sms_verify){
	        $result['status'] = 0;
	        $result['info'] = "验证码错误";
	        ajax_return($result);
	    }

	    //支付手续费
	    if($payment_id!=0)
	    {
	        if($money>0)
	        {
	            $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_id);
	            $directory = APP_ROOT_PATH."system/payment/";
	            $file = $directory. '/' .$payment_info['class_name']."_payment.php";
	            if(file_exists($file))
	            {
	                require_once($file);
	                $payment_class = $payment_info['class_name']."_payment";
	                $payment_object = new $payment_class();
	                if(method_exists($payment_object,"get_name"))
	                {
	                    $payment_info['name'] = $payment_object->get_name($bank_id);
	                }
	            }
	    
	    
	            	
	            if($payment_info['fee_type']==0) //定额
	            {
	                $payment_fee = $payment_info['fee_amount'];
	            }
	            else //比率
	            {
	                $payment_fee = $money * $payment_info['fee_amount'];
	            }
	        }
	    }
	    else
	    {
	       if($money > $s_account_info['money']){
	           $result['status'] = 0;
	           $result['info'] = "商家余额不足";
	           ajax_return($result);
	       }  
	       $payment_fee = 0;
	    }
	    
	    
	    $order=array();   
	    $order['create_time'] = NOW_TIME;
	    $order['total_price'] = $money + $payment_fee;
	    $order['pay_amount'] = 0;
	    $order['pay_status'] = 0;
	    $order['order_status'] = 0;
	    $order['payment_id'] = $payment_id;
	    $order['bank_id'] = $bank_id;
	    $order['payment_fee'] = $payment_fee;
	    $order['supplier_id'] = $supplier_id;
	    $order['is_delete'] = 0;
	    $order['supplier_id'] = $supplier_id;

	    do
	    {
	        $order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
	        $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_delivery_charge_order",$order,'INSERT','','SILENT');
	        $order_id = intval($GLOBALS['db']->insert_id());
	        	
	    }while($order_id==0);
	    
	    require_once(APP_ROOT_PATH."system/model/delivery_charge_order.php");
	    if($payment_id>0 && $order['total_price']>0){
	        
	        $payment_notice_id = make_delivery_charge_notice($order['total_price'],$order_id,$payment_id);
	        require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
	        $payment_class = $payment_info['class_name']."_payment";
	        $payment_object = new $payment_class();
	        $payment_code = $payment_object->get_payment_code($payment_notice_id);

	        $result['status']=2;
	        if($payment_fee > 0){
	            $payment_fee_html = '<div style="text-align: center;color:red;">( 手续费：'.format_price($payment_fee).' )</div>';
	        }
	      
	        $result['html']=$payment_code.$payment_fee_html;
	        $result['order_id']=$order_id;
	    }elseif($payment_id==0){
	        require_once(APP_ROOT_PATH."system/model/supplier.php");
	        $info='充值配送费';
	        modify_supplier_account('-'.$order['total_price'],$supplier_id,$type=3,$info);
	        $GLOBALS['db']->query("update ".DB_PREFIX."supplier_delivery_charge_order set pay_amount = pay_amount + ".$order['total_price']." where id = ".$order_id." and is_delete = 0 and order_status = 0 and pay_amount + ".$order['total_price']." <= total_price");
	         
	        $data = delivery_charge_order_paid($order_id);
	        if($data){
	            $result['status']=1;
	            $result['info']='充值成功';
	            $result['jump']=url('biz','delivery_setting');
	        }else{
	            $result['status']=0;
	            $result['info']='充值失败';
	        }
	        
	    }

	    ajax_return($result);

	}
	


	public function check_order_status()
	{
	    init_app_page();
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	    $order_id = intval($_REQUEST['order_id']);
	    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_delivery_charge_order where id = ".$order_id);
	    
	    if($order_info['pay_status']==2){

	        $result['status']=1;
	        $result['info']='充值成功';
	    }else{

	        $result['status']=0;
	        $result['info']='充值失败';
	    }

	    ajax_return($result);
	
	}
	
	public function setting_delivery()
	{
	    init_app_page();
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	    $delivery_type = strim($_REQUEST['delivery_type']);
	    $is_open = intval($_REQUEST['is_open']);

	    if($delivery_type=='dada'){
	        $field='is_open_dada_delivery';
	    }

	    $GLOBALS['db']->query("update ".DB_PREFIX."supplier set ".$field." = ".$is_open." where id = ".$supplier_id);
	    $rs = $GLOBALS['db']->affected_rows();
	    if($rs){

	        $result['status']=1;
	        $result['info']='设置成功';
	    }else{
	
	        $result['status']=0;
	        $result['info']='设置失败';
	    }
	
	    ajax_return($result);
	
	}
	
	
	public function withdraw()
	{
	    init_app_page();
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	
	    $money = floatval($_REQUEST['money']);
	    $verify_code = strim($_REQUEST['verify_code']);
	    $sms_verify = strim($_REQUEST['sms_verify']);	     
	     
	    if($money<=0){
	        $result['status']=0;
	        $result['info']='请输入正确的提现金额';
	        ajax_return($result);
	    }
	    
	    if($money > $s_account_info['delivery_money']){
	        $result['status']=0;
	        $result['info']='提现超额';
	        ajax_return($result);
	    }
	    
	    if(IS_OPEN_DADA && $s_account_info['is_open_dada_delivery']==1){
	        $result['status']=0;
	        $result['info']='达达配送未关闭，不允许提现';
	        ajax_return($result);
	    }
	    
	     
	    //需要图形验证码
	
	    $mobile_phone = $s_account_info['mobile'];
	    $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
	    if($mobile_data['code']!=$sms_verify){
	        $result['status'] = 0;
	        $result['info'] = "验证码错误";
	        ajax_return($result);
	    }
	    
	    $sql = "update ".DB_PREFIX."supplier set money = money + ".$money." , delivery_money = delivery_money - ".$money." where id = ".$supplier_id." and delivery_money  - ".$money." >= 0";
	    $GLOBALS['db']->query($sql);
	    $rs = $GLOBALS['db']->affected_rows();

	    if($rs > 0){
	        $result['status']=1;
	        $result['info']='提现成功';
	    }else{
	        $result['status']=0;
	        $result['info']='提现失败';
	    }

	    ajax_return($result);
	
	}
	
	
}
?>