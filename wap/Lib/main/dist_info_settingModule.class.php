<?php
/**
 * @desc      
 * @author    吴庆祥
 * @since     2017-02-08 17:07  
 */
class dist_info_settingModule extends MainBaseModule{
    /**
     * 商户中心设置页面
     **/

    public function index()
    {
        dist_global_run();
        dist_init_app_page();

        $data = call_api_core("dist_info_setting","index");

        if ($data['dist_user_status']==0){ //用户未登录
            app_redirect(wap_url("dist","user#login"));
        }
        $GLOBALS['tmpl']->assign("conf",$data['conf']);
        $GLOBALS['tmpl']->assign("m_conf",$data['m_conf']);
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_info_setting.html");
    }

    public function loginout()
    {
        $data = call_api_core("dist_user","loginout");
		es_cookie::delete("dist_uname");
		es_cookie::delete("dist_upwd");
        $data['jump']=wap_url("dist","undeliver");

        ajax_return($data);
    }
}