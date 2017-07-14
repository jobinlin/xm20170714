<?php
/**
 * @desc      
 * @author    吴庆祥
 * @since     2017-02-08 16:51  
 */

class dist_centerModule extends MainBaseModule
{

    public function index(){
        dist_global_run();
        dist_init_app_page();
        $data = call_api_core("dist_center","index");
        if ($data['dist_user_status']==0){ //用户未登录
            app_redirect(wap_url("dist","user#login"));
        }

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("item",$data['item']);
        $GLOBALS['tmpl']->display("dist_center.html");
    }



}