<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_fxwithdrawModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		

		
		$param=array();	
		$param['page'] = intval($_REQUEST['page']);	
		$data = call_api_core("uc_fxwithdraw","wap_index",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){			
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象			
			$p  =  $page->show();
			
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		
		//print_r($data);exit;
		//$GLOBALS['tmpl']->assign("back_url",wap_url("index","user_center"));
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("bank_list",$data['bank_list']);
		$GLOBALS['tmpl']->display("uc_fxwithdraw.html");
	}
	

	

	public function save()
	{
		global_run();
		$param=array();
		
		$param['money'] = floatval($_REQUEST['money']);
		$param['type'] = intval($_REQUEST['type']);
		$param['bank_id'] = intval($_REQUEST['bank_id']);
		$param['password'] = strim($_REQUEST['pwd']);
        
		$data = call_api_core("uc_fxwithdraw","wap_save",$param);

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$result['status'] = 0;
			$result['info'] = "";
			$result['url'] = wap_url("index","user#login");
			ajax_return($result);
		}else{
			if($data['status']==0){
				$result['status'] = 0;
				$result['info']=$data['info'];
				ajax_return($result);	
			}elseif($data['status']==1){
				$result['status'] = 1;
				$result['url'] = wap_url("index","uc_fxwithdraw#detail");
				ajax_return($result);					
			}
		}
	}
	

	/**
	 * 分销提现明细
	 **/
	public function detail()
	{
	    global_run();
		init_app_page();
		

		
		$param=array();	
		$param['page'] = intval($_REQUEST['page']);	
		$data = call_api_core("uc_fxwithdraw","detail",$param);
		
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
		
		//echo "<pre>";print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	
	    $GLOBALS['tmpl']->display("uc_fxwithdraw_detail.html");
	
	}
	


}
?>