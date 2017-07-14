<?php

/**
 * @desc
 * @author    吴庆祥
 * @since     2017-02-08 16:06
 */
class dist_undeliverModule extends MainBaseModule
{
    function __construct()
    {
        parent::__construct();
        dist_global_run();

    }

    public function index()
    {
        dist_init_app_page();
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("dist_undeliver", "index", $param);
        if ($data['dist_user_status'] == 0) { //用户未登录
            app_redirect(wap_url("dist", "user#login"));
        }

        //设定页面类型为工作台
        $GLOBALS['tmpl']->assign("data", $data);
        $GLOBALS['tmpl']->display("dist_undeliver.html");
    }

    /**
     * 商家的配送范围
     * @desc
     * @author    吴庆祥
     */
    public function scope()
    {
        dist_init_app_page();
        $data = call_api_core("dist_undeliver", "scope");
        if ($data['dist_user_status'] == 0) { //用户未登录
            app_redirect(wap_url("dist", "user#login"));
        }
        $GLOBALS['tmpl']->assign("data", $data);
        $GLOBALS['tmpl']->display("dist_undeliver_scope.html");
    }

    /**
     * 商家配送点
     * @desc
     * @author    吴庆祥
     */
    public function point_list()
    {
        dist_init_app_page();
        $data = call_api_core("dist_undeliver", "point_list");
        if ($data['dist_user_status'] == 0) { //用户未登录
            app_redirect(wap_url("dist", "user#login"));
        }
        $GLOBALS['tmpl']->assign("data", $data);
        $GLOBALS['tmpl']->display("dist_undeliver_point_list.html");
    }

    /**
     * @desc 确认使用消费卷
     * @author    吴庆祥
     */
    public function deliverycode_use()
    {
        dist_init_app_page();
        $param['coupon_pwd']        = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("dist_undeliver","deliverycode_use",$param);
        ajax_return($data);
    }
    public function scan_index_check(){
        dist_init_app_page();
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("dist_undeliver","index_check",$param);
        if ($data['status']) {
            $data['url'] = $data['jump'];
        }
        ajax_return($data);
    }
    public function deliverycode_check()
    {
        dist_init_app_page();
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("dist_undeliver", "deliverycode_check", $param);

        if ($data['dist_user_status'] != LOGIN_STATUS_LOGINED) {
            app_redirect(wap_url("dist", "user#login"));
        }
        $GLOBALS['tmpl']->assign("data", $data);
        $GLOBALS['tmpl']->display("dist_undeliver_coupon_check.html");
    }

    public function index_check()
    {
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("dist_undeliver", "index_check", $param);
        $data = $this->_checkLogin($data);
        ajax_return($data);
    }

    private function _checkLogin($data)
    {
        if ($data['dist_user_status'] != LOGIN_STATUS_LOGINED) {
            $data['status']=0;
            $data['jump'] = wap_url("dist", "user#login");
        }
        return $data;
    }
    public function verify_log_list(){
        dist_init_app_page();
        $param['page']=$_REQUEST['page'];
        $data = call_api_core("dist_undeliver", "verify_log_list",$param);
        if ($data['dist_user_status'] != LOGIN_STATUS_LOGINED) {
            app_redirect(wap_url("dist", "user#login"));
        }
        formatPage($data['total']);
        $GLOBALS['tmpl']->assign("back_url",wap_url("dist","undeliver#index"));
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_undeliver_verify_log_list.html");
    }
    public function verify_log_detail(){
        dist_init_app_page();
        $param['coupon_pwd']=$_REQUEST['coupon_pwd'];
        $data = call_api_core("dist_undeliver", "verify_log_detail",$param);
        if ($data['dist_user_status'] != LOGIN_STATUS_LOGINED) {
            app_redirect(wap_url("dist", "user#login"));
        }
        $GLOBALS['tmpl']->assign("back_url",wap_url("dist","undeliver#verify_log_list"));
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_undeliver_verify_log_detail.html");
    }
    public function search_log(){
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("dist_undeliver","search_log", $param);
        if($data['dist_user_status'] != LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("dist","user#login"));
        }
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display('dist_undeliver_verify_log_list.html');
    }
}