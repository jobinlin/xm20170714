<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cartModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		global $is_app;
		
		$data = call_api_core("cart","index");
		//$user=$GLOBALS['user_info'];
		//if($user == null){
		//    login_is_app_jump();
		//	  app_redirect(wap_url("index","user#login"));
		//}
		$goback=$_REQUEST['goback'];
		if($goback){
		    $back_url = wap_url("index","index");
		    $GLOBALS['tmpl']->assign("back_url",$back_url);
		}
		
		if($data['cart_list'])
		{		
    		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
    		$GLOBALS['tmpl']->assign("promote_cfg",json_encode($data['promote_cfg']));
    		

        }
		//print_r($data);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("cart.html");
	}
	
	public function addcart(){
	    global_run();
		$is_relate = false;
		$ids = $_REQUEST['id'];

		//$buy_type = intval($_REQUEST['buy_type']);
		//$in_cart = intval($_REQUEST['in_cart']);
	    if( !empty($ids)&&(is_array($ids)) ){
			$is_relate = true;
			$data = call_api_core("cart","addcartByRelate",array("ids"=>$ids,"deal_attr"=>$_REQUEST['dealAttrArray'],"idnumArray"=>$_REQUEST['idnumArray']));
			$id=$ids[0];
	    }else{
			$id = intval($ids);
			$num = intval($_REQUEST['num'])?intval($_REQUEST['num']):1;
			$deal_attr = array();
			if($_REQUEST['deal_attr'])
			{
				foreach($_REQUEST['deal_attr'] as $k=>$v)
				{
					$deal_attr[$k] = intval($v);
				}
			}
			$data = call_api_core("cart","addcart",array("id"=>$id,"deal_attr"=>$deal_attr,"num"=>$num));
		}
		
	    $ajax_data = array();
	    $ajax_data['status'] = $data['status'];
		$ajax_data['cart_num']=$data['cart_num'];
		$ajax_data['in_cart']=1;
	    if($data['status']==1)
	    {
// 	        if($buy_type==1){  //积分兑换，直接进入订单提交页面
// 	            $ajax_data['jump'] = wap_url("index","cart#check",array("buy_type"=>1));
// 	        }else{
// 	            $ajax_data['jump'] = wap_url("index","cart");
// 	        }
	        
	         
	         $cart_data = get_cart_type($id);
	         $ajax_data['jump'] =$cart_data['jump'];
	         $ajax_data['in_cart'] =$cart_data['in_cart'];
	    	
	    }
	    elseif($data['status']==-1)
	    {
	    	$ajax_data['jump'] = wap_url("index","user#login");
	    }
	    else
	    {
			if( $is_relate ){
				//有没有购买成功的商品
//				$ajax_data['info'] = array();
//				foreach($data as $kk=>$info){
//					if( in_array($kk,$ids) ){
//						$ajax_data['info'][$kk] = $info;
//					}
//				}
				$ajax_data['jump'] = wap_url("index","cart");
				$ajax_data['info'] = $data['info'];
				//foreach ($data as $k=>$v){
				//	if( is_numeric($k) ){
				//		$ajax_data['info'][$k] = $v['info'];
				//	}
				//}
			}else{
				$ajax_data['info'] = $data['info'];
			}
	    }
	    
	    ajax_return($ajax_data);
	}
	
	public function get_youhui(){
	    global_run();
	    
	    $data = call_api_core("cart","get_youhui",array("id"=>intval($_REQUEST['id'])));
	    
	    ajax_return($data);
	}
	
	/**
	 * 领取优惠券
	 * */
	public function download_youhui(){
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("youhuis","download_youhui",array("data_id"=>$data_id));
	    
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED)
	    {
	        $data['status'] = 0;
	        $data['info'] = "请登录后操作";
	        $data['jump']  = wap_url("index","user#login");
	    }
	    ajax_return($data);
	}
	
	public function check_cart()
	{
		global_run();
		
		$num = array();
	    if($_REQUEST['num'])
	    {
	    	foreach($_REQUEST['num'] as $k=>$v)
	    	{
	    		$num[$k] = intval($v);
	    	}
	    }
	    
	    $mobile = strim($_REQUEST['mobile']);
	    $sms_verify = strim($_REQUEST['sms_verify']);
	    
	    $data = call_api_core("cart","check_cart",array("num"=>$num,"mobile"=>$mobile,"sms_verify"=>$sms_verify));
	    
	    if($data['status'])
	    {
	    	$ajaxdata['jump'] = wap_url("index","cart#check");
	    	$ajaxdata['status'] = 1;
	    	ajax_return($ajaxdata);
	    }
	    else
	    {
	    	$ajaxdata['status'] = 0;
	    	$ajaxdata['info'] = $data['info'];
	    	ajax_return($ajaxdata);
	    }
	}
	
	
	public function del()
	{
		global_run();
		$id = intval($_REQUEST['id']);
		$data = call_api_core("cart","del",array("id"=>$id));
		
		app_redirect(get_gopreview());
	}
	
	public function check()
	{
		global_run();		
		init_app_page();
        set_gopreview();
		$address_id=intval($_REQUEST['address_id']);
		$lid=intval($_REQUEST['lid']);  //自提门店ID
		$id=intval($_REQUEST['id']);   //直接购买，不进入购物车的时，商品ID
		$GLOBALS['tmpl']->assign("address_id",$address_id);
		$GLOBALS['tmpl']->assign("id",$id);
	    $data = call_api_core("cart","check",array("address_id"=>$address_id,"id"=>$id,'lid'=>$lid));
		if(!$GLOBALS['is_weixin'])
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Wwxjspay")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}
		else
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Upacpwap")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}
//		print_r($data);exit;

		if($data['status']==-1)
		{
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		if(empty($data['cart_list']))
		{
			app_redirect(wap_url("index","cart"));
		}

		
		
		//print_r($cart_list_new);
		//满减计算
		$promote_obj = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."promote where class_name='Appdiscount'");
		$data['total_data']['discount_amount'] = 0;
		$data['total_data']['deal_price']= $data['total_data']['total_price']; //商品金额
		if($promote_obj){
		    $promote_cfg = unserialize($promote_obj['config']);
		    if($data['total_data']['total_price'] >= $promote_cfg['discount_limit']){
		        $data['total_data']['total_price']-=$promote_cfg['discount_amount'];  //总计
		        $data['total_data']['discount_amount'] = $promote_cfg['discount_amount'];  //优惠金额
		    }
		}
		foreach ($data['delivery_list'] as $tt => $vv){
		    if(!$vv['delivery_fee']){
		        $data['delivery_list'][$tt]['delivery_fee']=0;
		    }
		}
		$account_amount = round($GLOBALS['user_info']['money'],2);

		if($id > 0){
		    $back_deal_url = $v['url'] = wap_url("index","deal",array("data_id"=>$id));
		    $back_url = $back_deal_url;
		    
		}else{
		    $back_url = wap_url("index","cart#index",array("goback"=>1));    
		}
		$GLOBALS['tmpl']->assign("app_index",APP_INDEX);
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		$GLOBALS['tmpl']->assign("address_id",$address_id);
		$buy_type = $data['buy_type'];
		$GLOBALS['tmpl']->assign("buy_type",$buy_type);
		$GLOBALS['tmpl']->assign("is_pick",$data['is_pick']);
		$GLOBALS['tmpl']->assign("supplier_id",$data['supplier_id']);
		$GLOBALS['tmpl']->assign("account_amount",$account_amount);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("cart_check.html");
	}
	
	public function done()
	{
		global_run();
		$param['delivery_id'] =  intval($_REQUEST['delivery']); //配送方式
		$param['ecvsn'] = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
		$param['ecvpassword'] = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
		$param['payment'] = intval($_REQUEST['payment']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['all_score'] = intval($_REQUEST['all_score']);
		$param['content'] = $_REQUEST['content'];
		$param['address_id'] = intval($_REQUEST['address_id']);
		$param['buy_type'] = intval($_REQUEST['buy_type']);
		$param['location_id']=intval($_REQUEST['location_id']);  //自提门店ID
		$param['id'] = intval($_REQUEST['id']);
		$param['youhui_ids'] = $_REQUEST['youhui_log_id'];

		// 发票
		$param['invoice_type'] = $_REQUEST['invoice_type'];
		$param['invoice_content'] = $_REQUEST['invoice_content'];
		$param['invoice_title'] = $_REQUEST['invoice_title'];
		$param['invoice_taxnu'] = $_REQUEST['invoice_taxnu'];

		$data = call_api_core("cart","done",$param);

		if($data['status']==-1) {
			$ajaxobj['status'] = 1;
			$ajaxobj['jump'] = wap_url("index","user#login");
			ajax_return($ajaxobj);
		} elseif($data['status']==1) {
			$ajaxobj['status'] = 1;
			$ajaxobj['app_index'] = APP_INDEX;
			//if(APP_INDEX=='app'){  //先留着，后期有用
			if(1==0){
			    if($data['pay_status']==1){  //订单成功页面
			        $ajaxobj['type'] = 312;
			    }else{ //收银台页面
			        $ajaxobj['type'] = 311;

			    }
			    $ajaxobj['id'] = $data['order_id'];
			}else{
			    if($data['pay_status']==1){
			        $ajaxobj['jump'] = wap_url("index","payment#done",array("id"=>$data['order_id']));
			    }else{
			        $ajaxobj['jump'] = wap_url("index","cart#pay",array("id"=>$data['order_id']));
			    }  
			}

			

			
			ajax_return($ajaxobj);
		} else {
			$ajaxobj['status'] = $data['status'];
			$ajaxobj['info'] = $data['info'];
			if ($data['status'] == -2) {
				$ajaxobj['jump'] = wap_url("index","uc_account#phone");
			}
			ajax_return($ajaxobj);
		}
		
	}

	
	public function order_done()
	{
		global_run();
		
		$param['order_id'] = intval($_REQUEST['id']);

		$param['payment'] = intval($_REQUEST['payment']);
		$param['rel'] = strim($_REQUEST['rel']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['content'] = strim($_REQUEST['content']);
		
		$data = call_api_core("cart","order_done",$param);
		//logger::write(print_r($data,1));
		if($data['status']==-1)
		{
			$ajaxobj['status'] = 1;
			$ajaxobj['jump'] = wap_url("index","user#login");
			ajax_return($ajaxobj);
		}
		elseif($data['status']==1)
		{
		    $ajaxobj['app_index']=$data['app_index'];

		    if($data['app_index']=='app'){
                $ajaxobj['online_pay'] = $data['online_pay'];
		        $ajaxobj['sdk_code']=$data['sdk_code'];
		        if($data['pay_status']==1){
		            $ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));
		        }else{
		            $ajaxobj['jump'] = $data['pay_url'];//SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));	            		 
		        }

		    }else{
		        $ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));
		    }

		    $ajaxobj['title'] = $data['title'];
		    $ajaxobj['pay_status'] = $data['pay_status'];
		    $ajaxobj['url'] = SITE_DOMAIN.wap_url("index","cart#pay",array("id"=>$data['order_id']));
			$ajaxobj['status'] = 1;

			ajax_return($ajaxobj);
		}
		else
		{
			$ajaxobj['status'] = $data['status'];
			$ajaxobj['info'] = $data['info'];
			ajax_return($ajaxobj);
		}
	}
	
	public function pay()
	{
		global_run();		
		init_app_page();
		$is_ajax=0;
		$param['id'] = intval($_REQUEST['id']);
		$is_ajax =$param['is_ajax']= intval($_REQUEST['is_ajax']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['payment'] = intval($_REQUEST['payment']);
        $param['rel'] = strim($_REQUEST['rel']);
		

		$data = call_api_core("cart","pay",$param);
		
		if($data['status']==-1)
		{
		    login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}elseif($data['status']==0){
		    app_redirect(SITE_DOMAIN.wap_url("index","uc_order",array('pay_status'=>1)));
		}elseif($data['status']==2){
		    app_redirect(SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$param['id'])));
		}
		
		
		if(empty($data['cart_list']))
		{
		    app_redirect(wap_url("index"));
		}

	
		if(APP_INDEX=='app'){
			$json_parma=addslashes(json_encode(array("data_id"=>$param['id'])));
			$back_url ='javascript:App.app_detail(308,"'.$json_parma.'");';
			
		}else{
			$back_url = SITE_DOMAIN.wap_url("index","uc_order#view",array("data_id"=>$param['id']));
		}
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		
		if($is_ajax==1){
		    $GLOBALS['tmpl']->assign("data",$data);
    		$ajaxobj['html'] = $GLOBALS['tmpl']->fetch("style5.2/inc/page/pay.html");
    		ajax_return($ajaxobj);
		}else{
		    $data['all_account_money']=1;
		    $GLOBALS['tmpl']->assign("data",$data);
		    $GLOBALS['tmpl']->display("pay.html");
		}
		
		
		
	}
	
	
	public function clear_deal_cart()
	{
	    global_run();
	
	    $param['id'] = $_REQUEST['id'];
	    $data = call_api_core("cart","clear_deal_cart",$param);

	    //if($data['status']==-1)
	    //{
	    //    $ajaxobj['status'] = -1;
	    //    $ajaxobj['info'] = $data['info'];
	    //    $ajaxobj['jump'] = wap_url("index","user#login");
	    //    ajax_return($ajaxobj);
	    //}
	    //else
	    //{
	        $ajaxobj['status'] = $data['status'];
	        $ajaxobj['info'] = $data['info'];
	        ajax_return($ajaxobj);
	    //}
	}
	
	

	public function get_cart_deal_attr()
	{
	    global_run();
	
	    $param['id'] = intval($_REQUEST['id']);
	    $param['attr_key'] = strim($_REQUEST['attr_key']);
	    //logger::write($param['attr_key']);
	    $data = call_api_core("cart","get_cart_deal_attr",$param);
	    if($data['status']==-1)
	    {
	        $ajaxobj['status'] = -1;
	        $ajaxobj['info'] = $data['info'];
	        $ajaxobj['jump'] = wap_url("index","user#login");
	        ajax_return($ajaxobj);
	    }
	    else
	    {
	        $data['deal_info']['attr_num']=count($data['deal_info']['deal_attr']);
	        $GLOBALS['tmpl']->assign("deal_info",$data['deal_info']);
	        $ajaxobj['html'] = $GLOBALS['tmpl']->fetch("style5.2/inc/page/cart_deal_attr.html");
	        $ajaxobj['status'] = 1;
	        //logger::write(print_r($ajaxobj,1));
	        ajax_return($ajaxobj);
	    }
	}
	
	
	public function set_cart_status()
	{
	    global_run();
	    $param['checked_ids'] = $_REQUEST['checked_ids'];
	    $param['nochecked_ids'] = $_REQUEST['nochecked_ids'];
	    
	    $data = call_api_core("cart","set_cart_status",$param);
	
	    if($data['status']==-1)
	    {
	        $ajaxobj['status'] = -1;
	        $ajaxobj['info'] = $data['info'];
	        $ajaxobj['jump'] = wap_url("index","user#login");
	        ajax_return($ajaxobj);
	    }
	    else if($data['status']==-2)
	    {
	        $ajaxobj['status'] = -1;
	        $ajaxobj['info'] = $data['info'];
	        $ajaxobj['jump'] = wap_url("index","uc_account#phone");
	        ajax_return($ajaxobj);
	    }
	    else
	    {
	        if($data['jump']){
	            $ajaxobj['jump'] = $data['jump'];
	        }
	        
	        $ajaxobj['status'] = $data['status'];
	        $ajaxobj['info'] = $data['info'];
	        ajax_return($ajaxobj);
	    }
	}
}
?>