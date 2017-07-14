<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class biz_userModule extends MainBaseModule
{
	public function login()
	{
		global_run();		
		init_app_page();		
		
		if($GLOBALS['account_info']){ //用户已经登录
		    //app_redirect(wap_url("biz","dealv#index"));
			app_redirect(wap_url("biz","shop_verify#index"));
		}
		$data = call_api_core("biz_user","login");

		$GLOBALS['tmpl']->assign("data",$data);

		$GLOBALS['tmpl']->display("biz_user_login.html");
	}
	
	public function dologin(){
	    
	    global_run();

		$param['account_name'] = strim($_REQUEST['account_name']);
		$param['account_password'] = strim($_REQUEST['account_password']);

		//获取品牌
		$data = call_api_core("biz_user","dologin",$param);
        if ($data['status']){
            //写入COOKIE
            es_cookie::set("user_name",$data['account_info']['user_name'],604800);
            es_cookie::set("user_pwd",$data['account_info']['user_pwd'],604800);
            es_cookie::set("biz_uname",$data['account_info']['account_name'],604800);
            es_cookie::set("biz_upwd",$data['account_info']['account_password'],604800);

            $data['jump'] = wap_url("biz","shop_verify#index");//wap_url("biz","dealv#index");
        }
        ajax_return($data);
	   
	}
	
	public function loginout(){
	    $data = call_api_core("biz_user","loginout");
		

		app_redirect(wap_url("biz","user#login"));
	}
	
	
	public function getpassword()
	{
		global_run();
		init_app_page();
		
		$data = call_api_core('biz_user', 'getpassword');

		if (!$data['biz_user_status']) {
			app_redirect(wap_url("biz","user#login"));
		}

		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$data['account']['format_mobile'] = substr_replace($data['account']['mobile'], "****", 3, 4);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("biz_getpassword.html");
	}

	public function dogetpwd()
	{
		global_run();

		$param = $_REQUEST;

		$data = call_api_core('biz', 'dogetpwd', $param);
		if (!$data['biz_user_status']) {
			app_redirect(wap_url('biz', 'user#login'));
		}

		ajax_return($data);
	}
}
?>