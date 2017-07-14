<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
class biz_info_settingModule extends MainBaseModule
{
    /**
     * 商户中心设置页面
     **/
    
    public function index()
    {
        global_run();
        init_app_page();
    
        $data = call_api_core("biz_info_setting","index");

        if ($data['biz_user_status']==0){ //用户未登录
            app_redirect(wap_url("biz","user#login"));
        }
        $GLOBALS['tmpl']->assign("conf",$data['conf']);
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_info_setting.html");
    }
    
    public function loginout()
    {
        $data = call_api_core("biz_user","loginout");
//         es_cookie::delete("account_name");
//         es_cookie::delete("account_password");
        es_cookie::delete("biz_uname");
        es_cookie::delete("biz_upwd");
//         es_session::delete("wx_info");
//         es_cookie::set("deny_weixin_".intval($GLOBALS['supplier_info']['id']), 1); //人工退出禁止微信登录
         
        $data['jump']=wap_url("biz","shop_verify");
         
        ajax_return($data);
    }
}

?>