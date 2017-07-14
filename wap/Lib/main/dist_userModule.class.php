<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dist_userModule extends MainBaseModule
{
	public function login()
	{
		dist_global_run();		
		dist_init_app_page();	
		
		if ($GLOBALS['dist_info']) {//已登录跳转
		    app_redirect(wap_url("dist","undeliver#index"));
		}
		
		$data['page_title'] = "驿站登录";
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("dist_user_login.html");
	}
	
	public function dologin(){
	    dist_global_run();			

		$param['account_name'] = strim($_REQUEST['account_name']);
		$param['account_password'] = strim($_REQUEST['account_password']);

		//获取品牌
		$data = call_api_core("dist_user","dologin",$param);
        if ($data['status']){
            $data['jump'] = wap_url("dist","undeliver#index");
			es_cookie::set("dist_uname",$data['dist_info']['account_name'],3600*24*30);
			es_cookie::set("dist_upwd",md5($data['dist_info']['account_password']."_EASE_COOKIE"),3600*24*30);
        }
        ajax_return($data);
	   
	}
	
	public function loginout(){
	    $data = call_api_core("dist_user","loginout");
		
		es_cookie::delete("dist_uname");
		es_cookie::delete("dist_upwd");
		app_redirect(wap_url("dist","user#login"));
	}
	public function getpassword(){
        dist_global_run();
        dist_init_app_page();
        $GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
        $data['page_title']="重置密码";
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_user_getpassword.html");
    }
    public function dogetpassword() {
        global_run();
        $user_mobile = strim($_POST['user_mobile']);
        $sms_verify = strim($_POST['sms_verify']);
        $user_pwd = strim($_REQUEST['user_pwd']);
        $data = call_api_core("dist_user","phmodifypassword",array("mobile"=>$user_mobile,"sms_verify"=>$sms_verify,"new_pwd"=>$user_pwd));
        if($data['status'])
        {
            $data['jump'] = wap_url("dist","undeliver#index");
        }
        ajax_return($data);
    }

}