<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class idvalidateModule extends MainBaseModule
{
	public function index(){
        global_run();
        init_app_page();
        $data = call_api_core("idvalidate", "index", $data);
        
        if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
            //app_redirect(wap_url("index","user#login"));
        }
        
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("id_validate.html");
    }
	public function scanId(){
        global_run();
        init_app_page();
		$data['name']=strim($_REQUEST['name']);
		$data['idvalidate']=strim($_REQUEST['idvalidate']);
		$data['sex']=intval($_REQUEST['sex']);
        $data = call_api_core("idvalidate", "scanId", $data);
        ajax_return($data);
    }
	public function delete(){
        global_run();
        init_app_page();
        $data['type']  = $_REQUEST['type'];
    
        $data = call_api_core("idvalidate", "delete", $data);
        ajax_return($data);
    
    }
}
?>