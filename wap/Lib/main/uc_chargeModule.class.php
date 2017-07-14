<?php
// +----------------------------------------------------------------------
// | Fanwe 方维商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_chargeModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();

		
		$param=array();	
		$data = call_api_core("uc_charge","index",$param);

		$user_data = $GLOBALS['user_info'];
		$user_id = intval($user_data['id']);
		
		$payment_notice_id =intval( es_session::get("user_charge_".$user_id));
		if($payment_notice_id  > 0 ){
		    $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id=".$payment_notice_id);
		    if($payment_notice['is_paid']==1){
		        es_session::delete("user_charge_".$user_id);
		        app_redirect(wap_url("index","payment#done",array("id"=>$payment_notice['order_id'])));
		    }
		}
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_charge.html");
	}
	
	public function done()
	{
		global_run();
		init_app_page();		
		$param=array();	
		$param['money'] = floatval($_REQUEST['money']);	
		$param['payment_id'] = intval($_REQUEST['payment_id']);	
		$data = call_api_core("uc_charge","done",$param);
		
		
// 		print_r($data);exit;
		if($data['status']==-1)
		{
			$ajaxobj['status'] = 1;
			$ajaxobj['jump'] = wap_url("index","user#login");
			ajax_return($ajaxobj);
		}
		elseif($data['status']==1)
		{
		    
		    $data['jump'] = $data['pay_url'];

			ajax_return($data);
		}
		else
		{
			$ajaxobj['status'] = $data['status'];
			$ajaxobj['info'] = $data['info'];
			ajax_return($ajaxobj);
		}
	}


}
?>