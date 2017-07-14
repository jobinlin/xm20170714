<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_fxModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$param['page'] = intval($_REQUEST['page']);
		$data = call_api_core("uc_fx","my_fx",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		if(!$data['is_fx']){
		    app_redirect(wap_url("index","user_center"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    $p  =  $page->show();
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
        
		$GLOBALS['tmpl']->assign("back_url",wap_url("index","user_center"));
		$GLOBALS['tmpl']->assign("r",base64_encode($data['ref_uid']));
		$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","uc_fx"));
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("uc_fx.html");
	}
	
	public function deal_fx()
	{
	    global_run();
	    init_app_page();

	    $param['page'] = intval($_REQUEST['page']);
	    $param['fx_seach_key'] = strim($_REQUEST['fx_seach_key']);
	    $data = call_api_core("uc_fx","deal_fx",$param);
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }

	    if(!$data['is_fx']){
		    app_redirect(wap_url("index","user_center"));
		}
	
	    if(isset($data['page']) && is_array($data['page'])){
	        $page = new Page($data['page']['data_total'],$data['page']['page_size']);
	        $p  =  $page->show();
	        $GLOBALS['tmpl']->assign('pages',$p);
	    }
		// print_r($data);
	    $GLOBALS['tmpl']->assign("back_url",wap_url("index","user_center"));
	    $GLOBALS['tmpl']->assign("ajax_url",wap_url("index","uc_fx"));
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->display("uc_fx_deal.html");
	}

	public function add_user_fx_deal(){
	    global_run();
	    init_app_page();
	    
	    $param['deal_id'] = intval($_REQUEST['deal_id']);
	    $data = call_api_core("uc_fx","add_user_fx_deal",$param);
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	        $data['jump'] = wap_url("index","user#login");
	        $data['info'] = '请登录后操作';
	    }
	    ajax_return($data);
	}
	
    public function do_is_effect(){
	    global_run();		
		init_app_page();
		$param['deal_id'] = intval($_REQUEST['deal_id']);
		$data = call_api_core("uc_fx","do_is_effect",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
		    $data['jump'] = wap_url("index","user#login");
		}
		ajax_return($data);
	}
	
	public function del_user_deal(){
	    global_run();		
		init_app_page();
		
		$param['deal_id'] = intval($_REQUEST['deal_id']);
		$data = call_api_core("uc_fx","del_user_deal",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
		    $data['jump'] = wap_url("index","user#login");
		}
		ajax_return($data);
	}
	
	public function mall(){
	    global_run();		
		init_app_page();

		$param['page'] = intval($_REQUEST['page']);
		// $param['type'] = intval($_REQUEST['type']);
		$param['type'] = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
		if (isset($_REQUEST['r'])) {
			$param['rid'] = intval(base64_decode($_REQUEST['r']));
		}

		$data = call_api_core("uc_fx","wap_mall",$param);
		// var_dump($data);exit;

		/*if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    
	    if(!$data['is_fx']){
		    app_redirect(wap_url("index","user_center"));
		}*/
		
		if ($data['status'] == 0) {
			$jump_url = wap_url('index', 'user_center');
	    	$script = suiShow($data['info'], $jump_url);
	    	$GLOBALS['tmpl']->assign('suijump', $script);
	    	$GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
	    	
		} else {

			if(isset($data['page']) && is_array($data['page'])){
			    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
			    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			    $p  =  $page->show();
			    $GLOBALS['tmpl']->assign('pages',$p);
			}

			//商城模版填充
			/*if($data['type']==1 && count($data['deal_list'])%2 !=0){
			    array_push($data['deal_list'],array());
			}*/
			$GLOBALS['tmpl']->assign("r",base64_encode($GLOBALS['ref_uid']));
			// print_r($data);
			$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","uc_fx"));
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->assign('type', $param['type']);
			$GLOBALS['tmpl']->display("uc_fx_mall.html");
	    }
	}
	

	/**
	 * 分销收益统计
	 **/
	public function income()
	{
	    global_run();
		init_app_page();
		$param=array();	
		$param['page'] = intval($_REQUEST['page']);
		$param['user_id'] = intval($_REQUEST['user_id']);
		$data = call_api_core("uc_fx","income",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		if(!$data['is_fx']){
		    app_redirect(wap_url("index","user_center"));
		}
		if(isset($data['page']) && is_array($data['page'])){			
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象			
			$p  =  $page->show();
			
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		
		$data['ptype']="moneylog";
		//echo "<pre>";print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->display("uc_fx_income.html");
	
	}
	
	/**
	 * 分销小店二维码&推广
	 **/
	public function qrcode()
	{
	    global_run();
	    init_app_page();
        $is_ajax = intval($_REQUEST['is_ajax']);
		$param=array();
		$data = call_api_core("uc_fx","qrcode",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		if(!$data['is_fx']){
		    app_redirect(wap_url("index","user_center"));
		}
		//echo "<pre>";print_r($data);exit;
        if($is_ajax==0){
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("uc_fx_qrcode.html");
        }else{
            $arr=array();
            $arr['status']=1;
			$arr['user_data']=$data['user_data'];
            $arr['share_mall_qrcode']=$data['user_data']['share_mall_qrcode'];
            ajax_return($arr);
        }
	}

    /**
     * 分销小店二维码&推广
     **/
    public function save_qrcode_type()
    {
        global_run();
        init_app_page();

        $param['qrcode_type'] = intval($_REQUEST['qrcode_type']);
        $data = call_api_core("uc_fx","save_qrcode_type",$param);
        if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
            $data['jump'] = wap_url("index","user#login");
        }
        ajax_return($data);

    }
	/**
	 * 分销小店二维码下载
	 **/
	public function qrcode_don()
	{
		ob_start(); 
		$filename=$_REQUEST['img'];
		$date="小店二维码";//date("Ymd-H:i:m");
		header( "Content-type:  application/octet-stream "); 
		header( "Accept-Ranges:  bytes "); 
		header( "Content-Disposition:  attachment;  filename= {$date}.png"); 
		$size=readfile($filename); 
		header( "Accept-Length: " .$size);
	
	}
	
	/**
	 * 分销资格购买
	 **/
	public function vip_buy()
	{
	    global_run();
		init_app_page();
	    $data = call_api_core("uc_fx","vip_buy");
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
		if($data['mobile']==""){
		    app_redirect(wap_url("index","uc_account#phone",array("is_fx"=>1)));
		}
	    if($data['is_fx']){
	        app_redirect(wap_url("index","uc_fx"));
	    }
	    
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	    
	    $GLOBALS['tmpl']->display("uc_fx_vip_buy.html");
	
	}
	
	/**
	 * 分销资格购买
	 **/
	public function pay()
	{
	    global_run();
	
	    $GLOBALS['tmpl']->display("uc_fx_pay.html");
	
	}
	
	/**
	 * 生成分销资格购买订单
	 *   */
	public function make_order()
	{
	    global_run();
	    init_app_page();
	
	    $data = call_api_core("uc_fx","make_order");
	     
	    if($data['user_login_status']==1){
	        if($data['mobile']){
	            $data['jump']=wap_url("index","uc_account#phone");
	        }
	        else if($data['is_fx']){
	            $data['jump']=wap_url("index","uc_fx");
	        }
	        else if($data['free']){
	            if($data['is_open']){
	                $data['jump']=wap_url("index","user_center");
	            }
	        }
	        else {
    	        if($data['status']==1){
    	            $data['jump']=wap_url("index","uc_fx#check",array("order_id"=>$data['order_id']));
    	        }
	        }	
	    }else{
	        
	        $data['jump']=wap_url("index","user#login");
	        
	    }
	
	    ajax_return($data);
	
	}
	
	/**
	 * 订单支付页面
	 *   */
	public function check()
	{
	    global_run();
	    init_app_page();
	
	    $param = array();
	    $param['order_id'] = intval($_REQUEST['order_id']);
	
	    $data = call_api_core("uc_fx","check",$param);
	     
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    
	    if($data['status']==2){
	        app_redirect(wap_url("index","uc_fx#payment_done",array("order_id"=>$data['order_id'])));
	    }
	    elseif ($data['status']==0){
	        app_redirect(wap_url("index","user_center"));
	    }

	    $data['pay_price'] = $data['order_info']['total_price'] - $data['order_info']['discount_price'];
	    $account_amount = round($GLOBALS['user_info']['money'],2);
	    $GLOBALS['tmpl']->assign("account_amount",$account_amount);
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("ajax_url",  wap_url("index","uc_fx#vip_buy"));
	     
	    $GLOBALS['tmpl']->display('uc_fx_buy_check.html');
	}
	
	
	public function pay_done() {
	    $payment_id = intval($_REQUEST['payment']);
	    $bank_id = strim($_REQUEST['bank_id']);
	    $order_id = intval($_REQUEST['order_id']);
	    $all_account_money = intval($_REQUEST['all_account_money']);
	    $param['payment_id']=$payment_id;
	    $param['bank_id']=$bank_id;
	    $param['order_id']=$order_id;
	    $param['all_account_money']=$all_account_money;
	    
	    $data = call_api_core("uc_fx","pay_done",$param);
	    
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
		            $ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","uc_fx#payment_done",array("order_id"=>$data['order_id']));
		        }else{
		            $ajaxobj['jump'] = $data['pay_url'];//SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));	            		 
		        }

		    }else{
		        // 选择第三方支付
	            $ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","uc_fx#payment_done",array("order_id"=>$data['order_id']));
		    }

		    $ajaxobj['title'] = $data['title'];
		    $ajaxobj['pay_status'] = $data['pay_status'];
		    $ajaxobj['url'] = SITE_DOMAIN.wap_url("index","uc_fx#pay_done",array("order_id"=>$data['order_id']));
			$ajaxobj['status'] = 1;

			ajax_return($ajaxobj);
		}
		else
		{
			$ajaxobj['status'] = $data['status'];
			$ajaxobj['info'] = $data['info'];
			ajax_return($ajaxobj);
		}
	    
	    /* if($data['user_login_status']==1)
	    {
	        if($data['pay_status']==1){
	    
	            $data['jump']=wap_url('index','uc_fx#payment_done',array('order_id'=>$data['order_id']));
	        }else{
	            // 选择第三方支付
	            $param['from'] = 'wap';
	            $pay_interface = call_api_core('uc_fx', 'third_pay_interface', $param);
	            $data['jump'] = $pay_interface['pay_action'];
	        }
	    
	        ajax_return($data);
	    
	    }else{
	    
	        showErr('未登录，请先登录',0,wap_url('index','user#login'));
	    } */
	}
	
	public function payment_done() {
	    global_run();
	    init_app_page();
	    
	    $param = array();
	    $param['order_id'] = intval($_REQUEST['order_id']);
	    
	    $data = call_api_core("uc_fx","payment_done",$param);
	    
	    if($data['user_login_status']!=1){
	         
	        showErr('未登录，请先登录',0,wap_url('index','user#login'));
	    }
	    if($data['status']==-1){
	    
	        showErr('订单不存在',0,wap_url('index','user_center'));
	    }
	    if($data['pay_status']==1)
		{
	    
    	    $GLOBALS['tmpl']->assign("data",$data);
    	    if(APP_INDEX=='app')
    	        $GLOBALS['tmpl']->assign("back_url","javascript:App.app_detail(107,0);");
    	    else
    	       $GLOBALS['tmpl']->assign("back_url",wap_url("index","user_center"));
    	     
    	    $GLOBALS['tmpl']->display('uc_fx_payment_done.html');
		}
		else {
		    
		    $pay_url = $data['payment_code']['pay_action'];
		    app_redirect($pay_url);
		    
		}
	}
}
?>