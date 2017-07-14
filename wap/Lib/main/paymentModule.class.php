<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class paymentModule extends MainBaseModule
{

	
	public function done()
	{
		global_run();
		init_app_page();
		$id = intval($_REQUEST['id']);
		
		$data = call_api_core("payment","done",array("id"=>$id));
		
		if(!$data['status'])
		{
			showErr($data['info']);
		}
        //print_r($data);
		if($data['pay_status']==1)
		{ 
			$data['page_title'] = "支付结果";
			if($data['is_main']==1){
			   
			    if(APP_INDEX=='app'){
			        $back_url ='javascript:App.app_detail(305,0);';
			        $back_go_url ='javascript:App.app_detail(1,0);';
			    }else{
			        $back_go_url = wap_url("index","index#index");
			        $back_url = wap_url("index","uc_order");
			    }
			    $type=305;  // 我的订单--普通订单列表  
			}else{
			   
			    
			    if($data['order_type']==1){ //充值订单
			        $back_url = wap_url("index","uc_money#index");
			        $type=0;  // wap链接
			        $json_parma = json_encode(array());    			   
			        
			    }else if($data['order_type']==7){
                    $back_url = '';
                    $back_go_url=wap_url("index","scores_index");
                    $GLOBALS['tmpl']->assign("detail_url",wap_url("index","uc_score#index"));
                }else{
			       
			        if(APP_INDEX=='app'){
			            $json_parma = addslashes(json_encode(array('data_id'=>$id)));
			            $back_url ='javascript:App.app_detail(308,"'.$json_parma.'");';
			            $back_go_url ='javascript:App.app_detail(1,0);';
			        }else{
			            $back_url = wap_url("index","uc_order#view",array("data_id"=>$id));
			            $back_go_url = wap_url("index","index#index");
			        }

			    }
			}
			
			$GLOBALS['tmpl']->assign("back_url",$back_url);
			$GLOBALS['tmpl']->assign("back_go_url",$back_go_url);
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->display("payment_done.html");
		}
		else
		{
		        $pay_url = $data['payment_code']['pay_action'];
		        app_redirect($pay_url);
    
		    /*
			$data['payment_code']['page_title'] = "订单付款";
			$GLOBALS['tmpl']->assign("data",$data['payment_code']);
			$GLOBALS['tmpl']->display("payment_pay.html");
			*/
		}
		
	}
	public function incharge_done(){
	    $this->done();
	}
	public function order_share(){
	    global_run();
	    init_app_page();
	    $id = intval($_REQUEST['id']);
	    $is_share = intval($_REQUEST['is_share']);

	    if($is_share){
	        $data = call_api_core("payment","order_share",array("id"=>$id));
	        if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
				login_is_app_jump();
	            //app_redirect(wap_url("index","user#login"));
	            exit;
	        }
	    }
	    app_redirect(wap_url("index","uc_order#index",array('pay_status'=>1)));
	}
		
}
?>