<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dcorderModule extends MainBaseModule
{

	
	
/**
 * 提交订单页面     
 */
	public function old_cart()
	{
	
		global_run();

		$param['lid']=$location_id = intval($_REQUEST['lid']);
		
		require_once(APP_ROOT_PATH."wap/Lib/main/dcajaxModule.class.php");
		dcajaxModule::update_dc_cart($location_id);
		
		$param['form']='wap';
		$param['not_check_delivery']= intval($_REQUEST['not_check_delivery']);
		//付款方式 $payment_id=0为在线支付，$payment_id=1为货到付款
		$param['payment_id'] = intval($_REQUEST['payment_id']);
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$location_dc_table_cart=load_dc_cart_list(true,$location_id,$type=0);
		$location_dc_cart=load_dc_cart_list(true,$location_id,$type=1);
		$menu_num=array();
		foreach($location_dc_cart['cart_list'] as $k=>$v){
			$menu_num[$v['menu_id']]=$v['num'];
			
		}
		$param['menu_num'] = $menu_num;
		$rs_num=array();
		foreach($location_dc_table_cart['cart_list'] as $k=>$v){
			$rs_info=array();
			$rs_info['id']=$v['table_time_id'];
			$rs_info['num']=$v['num'];
			$rs_info['rs_date']=to_date($v['table_time'],"Y-m-d");
			$rs_num[]=$rs_info;
		}

		$param['ecvsn'] = strim($_REQUEST['ecvsn']);
		//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$param['dc_type'] = isset($_REQUEST['dc_type'])?intval($_REQUEST['dc_type']):-1;
		$param['consignee_id'] =$_REQUEST['consignee_id']?intval($_REQUEST['consignee_id']):'';

		$data = call_api_core("dcorder","cart",$param);
		// print_r($data);exit;
		//set_gopreview();
		//设置当前页面为前一页面
		$url=wap_url('index','dcorder#cart',array('lid'=>$location_id));
		es_session::set("wap_gopreview",$url);
		if($data['user_login_status']==1)
		{
			if($data['status']==0){

				if($data['is_return']==1){
					showErr($data['info'],0,wap_url('index','dcbuy',array('lid'=>$location_id)));
				}
			}

		//	print_r($data);
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->assign("location_dc_table_cart",$location_dc_table_cart);
			$GLOBALS['tmpl']->assign("location_dc_cart",$location_dc_cart);
			$GLOBALS['tmpl']->display("dc/dc_cart.html");
		
		}else{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
			//app_redirect(wap_url('index','user#login'));
		}
	}


	public function cart()
	{
		global_run();
		init_app_page();

		$param['lid']=$location_id = intval($_REQUEST['lid']);
		$param['not_check_delivery']= intval($_REQUEST['not_check_delivery']);

		//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$param['dc_type'] = isset($_REQUEST['dc_type'])?intval($_REQUEST['dc_type']):-1;

		if (isset($_REQUEST['ecvsn'])) {
			$param['ecvsn'] = intval($_REQUEST['ecvsn']);
		}

		if (isset($_REQUEST['consignee_id'])) {
			$param['consignee_id'] = intval($_REQUEST['consignee_id']);
		}

		$data = call_api_core("dcorder","cart",$param);
		if ($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			set_gopreview();
			app_redirect(wap_url('index', 'user#login'));
		}

		//设置当前页面为前一页面
		// $url=wap_url('index','dcorder#cart',array('lid'=>$location_id));
		// es_session::set("wap_gopreview",$url);
		if ($data['status'] == 0) {
			app_redirect(wap_url('index', 'dc_location', array('data_id' => $location_id)));
		}
		$GLOBALS['tmpl']->assign("data",$data);

		$GLOBALS['tmpl']->display("dc/dc_cart.html");
	}

	public function res_cart()
	{
		global_run();
		init_app_page();
		$param['lid'] = intval($_REQUEST['lid']);
		$param['item_id'] = intval($_REQUEST['item_id']);
		$param['rs_date'] = strim($_REQUEST['rs_date']);

		$data = call_api_core('dcorder', 'res_cart', $param);
		if ($data['location_info']['is_close']) {
			app_redirect(wap_url('index', 'dctable#detail', array('lid' => $param['lid'])));
		}
		// print_r($data);exit;
		$GLOBALS['tmpl']->assign('rs_date', $param['rs_date']);
		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->display('dc/dc_res_cart.html');
	}

	public function res_cart_item()
	{
		global_run();
		init_app_page();
		$param['lid'] = intval($_REQUEST['lid']);
		$param['table_menu_id'] = intval($_REQUEST['table_menu_id']); // 
		// $param['rs_date'] = strim($_REQUEST['rs_date']);
		$data = call_api_core('dcorder', 'res_cart_item', $param);
		// print_r($data);exit;
		$GLOBALS['tmpl']->assign('cate_list', $data['cate_list']);
		$GLOBALS['tmpl']->assign('cart_data', $data['cart_data']);
		$html = $GLOBALS['tmpl']->fetch('dc/inc/dc_cart_res_item.html');
		// echo $html;
		$return['html'] = $html;
		ajax_return($return);
	}


	/**
	 * 生成订单接口
	 *
	 */
	public function old_make_order()
	{
		global_run();
		
		//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$param['dc_type'] = isset($_REQUEST['dc_type'])?intval($_REQUEST['dc_type']):-1;
		
		$param['lid']=$location_id = intval($_REQUEST['lid']);
		$param['consignee'] = strim($_REQUEST['consignee']);
		$param['mobile'] = strim($_REQUEST['mobile']);
		$param['ecvsn'] = $_REQUEST['ecvsn']?strim($_REQUEST['ecvsn']):'';	
		$param['dc_comment'] = $_REQUEST['dc_comment']?strim( $_REQUEST['dc_comment']):'';
		$param['invoice'] =  $_REQUEST['invoice']?strim($_REQUEST['invoice']):'';

		//付款方式 $payment_id=0为在线支付，$payment_id=1为货到付款
		$param['payment_id'] = intval($_REQUEST['payment_id']);
		
		$param['consignee_id'] =$_REQUEST['consignee_id']?intval($_REQUEST['consignee_id']):'';
		$param['order_delivery_time']=  $_REQUEST['order_delivery_time']?strim( $_REQUEST['order_delivery_time']):'';

		// 新增参数
		if (isset($_REQUEST['item_time_id'])) {
			$param['item_time_id'] = intval($_REQUEST['item_time_id']);
		}
		if (isset($_REQUEST['table_menu_id'])) {
			$param['table_menu_id'] = intval($_REQUEST['table_menu_id']);
		}
		if (isset($_REQUEST['rs_date'])) {
			$param['rs_date'] = intval($_REQUEST['rs_date']);
		}
		
		/*
		$param['lid']=41;
		$param['dc_type']=1;
		$param['consignee_id']=119;
		$param['order_delivery_time']='18:30';
		$param['dc_comment']='不要吃';
		$param['invoice']='发标';
		*/
		$data = call_api_core("dcorder","old_make_order",$param);
		//ajax_return($data);

		if($param['dc_type']==1){
			// $url=wap_url('index','dctable',array('lid'=>$location_id));
			// es_session::set("wap_gopreview",$url);
		}
		if($data['user_login_status']==1) {
			
			$result['status']=$data['status'];
			$result['info']=$data['info'];
			if($result['status']==0){
				$result['jump']=wap_url('index','dcbuy',array('lid'=>$location_id));
			}else{
				if($data['payment_method']==1){
					//货到付款，直接跳到成功页面
					$param2=array();
					$param2['id']=$data['order_id'];
					$data2 = call_api_core("dcorder","order_done",$param2);
					
					if($data2['status']==1){
						$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where id = ".$data2['order_id']);
						if($data2['pay_status']==1 || $data2['pay_status']==5 ){
							$GLOBALS['db']->query("delete from ".DB_PREFIX."dc_cart where user_id=".$order_info['user_id']." and location_id=".$order_info['location_id']." and session_id='".es_session::id()."'");
						}
						$result['jump']=wap_url('index','dc_payment#done',array('pay_status'=>$data2['pay_status'],'order_id'=>$data2['order_id'],'payment_notice_id'=>$data2['payment_notice_id']));
					}else{
						$result['jump']=wap_url('index','dcorder#order',array('id'=>$data2['order_id']));
					}
	
				}else{
					$result['jump']=wap_url('index','dcorder#order',array('id'=>$data['order_id']));
				}
			}
			ajax_return($result);
		
		}else{
			//app_redirect(wap_url('index','user#login'));
			$result['status']=-1;
			$result['info']='未登录，请先登录';
			$result['jump']=wap_url('index','user#login');
			ajax_return($result);
			//app_redirect(wap_url('index','user#login'));
		}
	}

	


	public function make_order()
	{
		global_run();
		init_app_page();
		
		//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$param['dc_type'] = -1;
		
		$param['lid']=$location_id = intval($_REQUEST['lid']);

		$param['ecvsn'] = $_REQUEST['ecvsn']?strim($_REQUEST['ecvsn']):'';	
		$param['dc_comment'] = $_REQUEST['dc_comment']?strim( $_REQUEST['dc_comment']):'';
		$param['invoice'] =  $_REQUEST['invoice']?strim($_REQUEST['invoice']):'';

		//付款方式 $payment_id=0为在线支付，$payment_id=1为货到付款
		// $param['payment_id'] = intval($_REQUEST['payment_id']);
		$param['consignee_id'] =$_REQUEST['consignee_id']?intval($_REQUEST['consignee_id']):'';
		$param['order_delivery_time']=  strim( $_REQUEST['order_delivery_time']);

		/*
		$param['lid']=41;
		$param['dc_type']=1;
		$param['consignee_id']=119;
		$param['order_delivery_time']='18:30';
		$param['dc_comment']='不要吃';
		$param['invoice']='发标';
		*/
		$data = call_api_core("dcorder","make_order",$param);

		if($param['dc_type'] == 1){
			$url=wap_url('index','dctable',array('lid'=>$location_id));
			es_session::set("wap_gopreview",$url);
		}
		if($data['user_login_status'] == 1) {
			
			// $result['status']=$data['status'];
			// $result['info']=$data['info'];
			if ($data['status'] == 0) {
				$data['jump'] = wap_url('index','dcbuy',array('lid'=>$location_id));
			} else {
				$data['jump'] = wap_url('index','dcorder#order',array('id'=>$data['order_id']));
			}
		}
		ajax_return($data);
	}

	public function res_make_order()
	{
		global_run();
		init_app_page();
		$param['lid']=$location_id = intval($_REQUEST['lid']);
		$param['consignee'] = strim($_REQUEST['consignee']);
		$param['mobile'] = strim($_REQUEST['mobile']);
		$param['item_id'] = intval($_REQUEST['item_id']);
		$param['rs_date'] = strim($_REQUEST['rs_date']);
		$param['item_time_id'] = intval($_REQUEST['item_time_id']);
		$param['table_menu_id'] = intval($_REQUEST['table_menu_id']);
		$param['rs_type'] = intval($_REQUEST['rs_type']);
		$param['dc_type'] = 1;
		// 如果是提前点菜？？点菜数据的传递方式？？
		if (isset($_REQUEST['dc_comment'])) {
			$param['dc_comment'] = $_REQUEST['dc_comment']?strim( $_REQUEST['dc_comment']):'';
		}
		
		$data = call_api_core("dcorder","make_order",$param);

		if($param['dc_type'] == 1){
			$url=wap_url('index','dctable',array('lid'=>$location_id));
			es_session::set("wap_gopreview",$url);
		}
		$jump = '';
		if($data['user_login_status'] == 1) {
			if ($data['status'] == 1) {
				$jump = wap_url('index','dcorder#order',array('id'=>$data['order_id']));
			}
		}else{
			$jump = wap_url('index','user#login');
		}
		$data['jump'] = $jump;
		ajax_return($data);
	}


	/**
	 * 继续支付的页面
	 *
	 */

	public function old_order(){

		global_run();
		assign_form_verify();
		$param['id'] = intval($_REQUEST['id']);
		$param['payment'] = intval($_REQUEST['payment']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['from'] = 'wap';
		$data = call_api_core("dcorder","order",$param);

		if($data['user_login_status']==1)
		{

			if($data['status']==0){
				if($data['is_rs']==1){
					$url=wap_url('index','dc_rsorder#view',array('id'=>$data['id']));
				}else{
					$url=wap_url('index','dc_dcorder#view',array('id'=>$data['id']));
				}
				if($data['is_return']==1){
					showErr($data['info'],0,$url);
				}else{
					showErr($data['info'],1,get_current_url());
				}
			
			}
			
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->display("dc/dcorder.html");
		
		}else{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}

	public function order()
	{
		global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['id']);
		$param['payment'] = intval($_REQUEST['payment']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		// $param['from'] = 'wap';
		$data = call_api_core("dcorder","order",$param);

		if($data['user_login_status']==1) {
			if($data['is_rs']==1){
				$back_url = wap_url('index','dc_rsorder');
				$url=wap_url('index','dc_rsorder#view',array('id'=>$data['id']));
			}else{
				$back_url = wap_url('index','dc_dcorder');
				$url=wap_url('index','dc_dcorder#view',array('id'=>$data['id']));
			}
			if($data['status']==0){
				app_redirect($url);
			}
			$GLOBALS['tmpl']->assign("back_url",$back_url);
			// print_r($data);exit;
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->display("dc/dcorder.html");
		
		}else{
			// showErr('未登录，请先登录',0,wap_url('index','user#login'));
			set_gopreview();
			app_redirect(wap_url('index','user#login'));
		}
	}


	/**
	 *  继续支付页面，点击 “确认支付”后的提交地址
	 *
	 */
	public function old_order_done()
	{
		
		$param['id'] = intval($_REQUEST['id']);
		$param['payment'] = intval($_REQUEST['payment']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['account_money'] = floatval($_REQUEST['account_money']);
		$param['payment_fee'] = floatval($_REQUEST['payment_fee']);
		$param['pay_price'] = floatval($_REQUEST['pay_price']);
		check_form_verify();
		$data = call_api_core("dcorder","order_done",$param);
		if($data['user_login_status']==1)
		{
			if($data['status']==1){
				$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where id = ".$data['order_id']);
				if($data['pay_status']==1 || $data['pay_status']==5 ){
				$GLOBALS['db']->query("delete from ".DB_PREFIX."dc_cart where user_id=".$order_info['user_id']." and location_id=".$order_info['location_id']." and session_id='".es_session::id()."'");
				}
				$data['jump']=wap_url('index','dc_payment#done',array('pay_status'=>$data['pay_status'],'order_id'=>$data['order_id'],'payment_notice_id'=>$data['payment_notice_id']));
			}else{
				$data['jump']=wap_url('index','dcorder#order',array('id'=>$param['id']));
			}

			ajax_return($data);
		
		}else{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}

	public function order_done()
	{
		global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['id']);
		if (isset($_REQUEST['payment'])) {
			$param['payment'] = intval($_REQUEST['payment']);
		}
		if (isset($_REQUEST['all_account_money'])) {
			$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		}

		$data = call_api_core('dcorder', 'order_done', $param);
		$jump = '';
		if ($data['user_login_status'] == 1) {
			if ($data['status']) {
				if ($data['pay_status'] == 1) {
				$jump = wap_url('index', 'dc_payment#done', array('id' => $data['order_id']));
				} elseif ($param['payment'] > 0) {
					$order = array('order_id' => $data['order_id'], 'payment_notice_id' => $data['payment_notice_id']);
					$payment = call_api_core('dc_payment', 'get_payment_code', $order);
					if ($payment['status'] == 1) {
						$jump = $payment['payment_code']['pay_action'];
					} else {
						$data['status'] = $payment['status'];
						$data['info'] = $payment['info'];
					}
				}
			} else {
				$jump = wap_url('index', 'dcorder#view', array('id' => $param['id']));
			}
		}
		$data['jump'] = $jump;
		ajax_return($data);
	}
	
	public function to_pay(){
	    global_run();
	    init_app_page();
	    
	    $param['id'] = intval($_REQUEST['id']);
	    $param['is_rs'] = intval($_REQUEST['is_rs']);
	    $data = call_api_core('dcorder', 'to_pay', $param);
	    ajax_return($data);
	}




}

?>