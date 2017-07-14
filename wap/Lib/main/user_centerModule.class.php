<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class user_centerModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();

		$param=array();
		$data = call_api_core("user_center","index",$param);
		/*if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}*/
		$url=$_SERVER['HTTP_REFERER'];

		// print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("user_center.html");
	}
	
	
}
?>