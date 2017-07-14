<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_centerModule extends MainBaseModule
{

	public function index(){
	    global_run();
	    init_app_page();
	    $data = call_api_core("biz_center","index");
	    if ($data['biz_user_status']==0){ //用户未登录
	        app_redirect(wap_url("biz","user#login"));
	    }
	    //print_r($data);exit;
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("item",$data['item']);
	    $GLOBALS['tmpl']->display("biz_center.html");
    }
    
  
    
}
?>

