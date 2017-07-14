<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class settingModule extends MainBaseModule
{
	
	/**
	 * 会员中心设置页面
	 **/
	public function index()
	{	
		global_run();	
		init_app_page();
		
		$data = call_api_core("setting","index");
		
		/* if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		} */
		
		/* $GLOBALS['tmpl']->assign("conf",$data['conf']); */
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("setting.html");		
	}
	
	public function loginout()
	{
	    $data = call_api_core("user","loginout");
	    es_cookie::delete("user_name");
	    es_cookie::delete("user_pwd");
	    es_cookie::delete("user_login_id");
	    es_session::delete("wx_info");
	    es_cookie::set("deny_weixin_".intval($GLOBALS['supplier_info']['id']), 1); //人工退出禁止微信登录
	    
	    $data['jump']=wap_url("index","index");
	    
	    ajax_return($data);
	}
}
?>