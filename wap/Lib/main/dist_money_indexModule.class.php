<?php
// +----------------------------------------------------------------------
// | Fanwe 方维商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class dist_money_indexModule extends MainBaseModule
{
    /**
     * 账户管理首页
     */
    public function index(){
        dist_global_run();
        dist_init_app_page();	
		$data = call_api_core("dist_money_index","index");	
        if ($data['dist_user_status']==0){ //用户未登录
	        app_redirect(wap_url("dist","user#login"));
	    }
	    //控制返回跳转问题
	    $url=$_SERVER['HTTP_REFERER'];
	    $strlen = strlen($url);  //全部字符长度
	    $tp = strpos($url,"ctl");  //limit之前的字符长度
	    if(substr($url,$tp,28)=="ctl=withdrawal&act=bindbank"||
	        substr($url,$tp,32)=="ctl=withdrawal&act=withdraw_log"){
	        //print_r(substr($url,$tp,32));exit;
	        $GLOBALS['tmpl']->assign("back_url",wap_url("dist","center#index"));
	    }
        //--end控制返回跳转	
	    $GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("dist_info",$data['dist_info']);	            
        
        $GLOBALS['tmpl']->display("dist_money_index.html");
    }
   
}
?>
