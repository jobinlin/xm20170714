<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class store_payModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
        $location_id = intval($_REQUEST['id']);

          //请求接口
        $data = call_api_core("store_pay","pay",array("location_id"=>$location_id));

        // 门店不存在或不支持到店买单直接跳转到到店买单列表
        if ($data['status'] == -1) {
        	app_redirect(wap_url('index', 'stores_pay'));
        }

        $data['stores_explain_url']=wap_url("index","stores_explain#index",array("data_id"=>$location_id));
     	
        set_gopreview();
        // 优惠买单的折扣信息
        if (!empty($data['promote'])) {
        	$promote_data = array();
        	$promote_infos = array();
	        foreach ($data['promote'] as $k=>$v){
	        	// 过滤App 的促销优惠
	        	global $is_app;
	        	if (!$is_app && substr($v['class_name'], 0, 3) == 'App') {
	        		continue;
	        	}
	            // $v['config'] = unserialize($v['config']);
	            $promote_data[] = $v;
	            $promote_infos[] = $v['descriptions'];
	        }
	        $GLOBALS['tmpl']->assign('promote', 1);
	        $GLOBALS['tmpl']->assign("promote_data",  $promote_data);
	        $promotes = implode('&nbsp;',$promote_infos);
	        if (strlen($promotes) > 20) {
	        	// 防中文乱码
	        	$promotes = mb_substr($promotes, 0, 16, 'UTF-8').'...';
	        }
        	$GLOBALS['tmpl']->assign('promote_infos', $promotes);
        }

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("location_id",$location_id);
		$GLOBALS['tmpl']->display("store_pay_index.html");
	}

	
	public function make_order()
	{
		global_run();
		init_app_page();
	
		$param = array();
	
        // 消费金额
		$param['money'] = floatval($_REQUEST['money']);
		// 不可优惠金额
		$param['other_money'] = floatval($_REQUEST['other_money']);
		
		$param['all_score'] = intval($_REQUEST['all_score']);
		$param['location_id'] = intval($_REQUEST['location_id']);
	
		$data = call_api_core("store_pay","make_order",$param);
        
        $jump = '';
		if($data['user_login_status']==1){

			if($data['status']==1){
				if($data['pay_status']==1){
					$jump=wap_url("index","store_payment#done",array("order_id"=>$data['order_id']));
				}else{
					$jump=wap_url("index","store_pay#check",array("order_id"=>$data['order_id']));
				}
			} else if ($data['nomobile'] == 1) {
				$jump = wap_url('index', 'uc_account#phone');
			}
		}else{
			$jump = wap_url("index","user#login");
			//showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
		$data['jump'] = $jump;

		ajax_return($data);
		
	}
	
	
    public function check()
	{ 
		global_run();		
		init_app_page();

        $param = array();
        $param['order_id'] = intval($_REQUEST['order_id']);
        
		$data = call_api_core("store_pay","check",$param);
         
        if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		if($data['status']==2){
		    
		    $jump = wap_url('index','store_payment#done',array('order_id'=> $param['order_id']));
		    app_redirect( $jump );
		    //app_redirect(wap_url("index","user#login"));
		}
		// if(!$GLOBALS['is_weixin']) {
		// 	foreach($data['payment_list'] as $k=>$v) {
		// 		if($v['code']=="Wwxjspay")
		// 		{
		// 			unset($data['payment_list'][$k]);
		// 		}
		// 	}
		// } else {
		// 	foreach($data['payment_list'] as $k=>$v) {
		// 		if($v['code']=="Upacpwap") {
		// 			unset($data['payment_list'][$k]);
		// 		}
		// 	}
		// }
		
		$account_amount = round($GLOBALS['user_info']['money'],2);
		$data['page_title'] = "收银台";
		$GLOBALS['tmpl']->assign("account_amount",$account_amount);
		$GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("ajax_url",  wap_url("index","store_pay"));
   		
		// $GLOBALS['tmpl']->display("pages/store_pay/check.html");
		$GLOBALS['tmpl']->display('store_pay_check.html');
	}
	
	public function count_store_pay_total(){
	    global_run();
            $payment_id = intval($_REQUEST['payment']);
            $bank_id = intval($_REQUEST['bank_id']);
            $order_id = intval($_REQUEST['order_id']);
            $all_account_money = intval($_REQUEST['all_account_money']);
            
            $param = array();
            $param['payment_id'] = $payment_id;
            $param['bank_id'] = $bank_id;
            $param['order_id'] = $order_id;
            $param['all_account_money'] = $all_account_money;
            
            $data = call_api_core("store_pay","count_store_pay_total",$param);
            $GLOBALS['tmpl']->assign("data",$data);
            // $data['html']=$GLOBALS['tmpl']->fetch("inc/store_pay.html");
            $data['html']=$GLOBALS['tmpl']->fetch("store_pay_check_partial.html");
            
	    ajax_return($data);
	}
        
    public function done()
    {
        $payment_id = intval($_REQUEST['payment']);
        $bank_id = intval($_REQUEST['bank_id']);
        $order_id = intval($_REQUEST['order_id']);
        $all_account_money = intval($_REQUEST['all_account_money']);
        $param['payment_id']=$payment_id;
        $param['bank_id']=$bank_id;
        $param['order_id']=$order_id;
        $param['all_account_money']=$all_account_money;
        
        $data = call_api_core("store_pay","done",$param);
        
        if($data['user_login_status']==1) {
            if($data['pay_status']==1){
                $data['jump']=wap_url('index','store_payment#done',array('order_id'=>$data['order_id']));
            }else{
                $data['jump'] = $data['pay_url'];
            }
        }/*else{

        	showErr('未登录，请先登录',0,wap_url('index','user#login'));
        }*/
        
        ajax_return($data);
    }
    
    public function promote()
    {
    	global_run();
    
    	$param = array();
    	$param['money'] = floatval($_REQUEST['money']);
    	$param['other_money'] = floatval($_REQUEST['other_money']);
    	$param['location_id'] = intval($_REQUEST['location_id']);
    	$param['id'] = explode(',',strim($_REQUEST['id']));
    
    	$data = call_api_core("store_pay","promote",$param);
    //	$data['promote']['id']=json_encode($data['promote']['id']);
    	ajax_return($data);
    
    }
	public function score_purchase_count()
    {
    	global_run();
    
    	$param = array();
		$param['pay_money'] = floatval($_REQUEST['pay_money']);
		$param['final_pay'] = floatval($_REQUEST['final_pay']);
    	$param['all_score'] = intval($_REQUEST['all_score']);
    	$data = call_api_core("store_pay","score_purchase_count",$param);
    	ajax_return($data);
    
    }
}
?>