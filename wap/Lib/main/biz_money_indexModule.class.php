<?php
// +----------------------------------------------------------------------
// | Fanwe 方维商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_money_indexModule extends MainBaseModule
{
    /**
     * 账户管理首页
     */
    public function index(){
        global_run();
        init_app_page();	
		$data = call_api_core("biz_money_index","index");	
        if ($data['biz_user_status']==0){ //用户未登录
	        app_redirect(wap_url("biz","user#login"));
	    }
	    $url=$_SERVER['HTTP_REFERER'];
	    $strlen = strlen($url);  //全部字符长度
	    $tp = strpos($url,"ctl");  //limit之前的字符长度
	    if(substr($url,$tp,28)=="ctl=withdrawal&act=bindbank"||
	        substr($url,$tp,32)=="ctl=withdrawal&act=withdraw_log"){
	        //print_r(substr($url,$tp,32));exit;
	        $GLOBALS['tmpl']->assign("back_url",wap_url("biz","center#index"));
	    }
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	            
        
        $GLOBALS['tmpl']->display("biz_money_index.html");
    }
   
}
?>
