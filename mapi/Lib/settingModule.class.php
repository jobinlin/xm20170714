<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class settingApiModule extends MainBaseApiModule
{
 
    public function index() {
        $root = array();
        $user_data = $GLOBALS['user_info'];
        $user_login_status = check_login();
        $user_id  = intval($user_data['id']);
        
        $version=include(APP_ROOT_PATH."public/version.php");
        
        $root['DB_VERSION']=app_conf("DB_VERSION");
        $root['APP_ABOUT_US']=app_conf("APP_ABOUT_US");

        $config=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."m_config where code in ('kf_phone','kf_email')");
        foreach ($config as $t => $v){
            if($v['code']=='kf_phone'){
                $root['SHOP_TEL']=$v['val'];
            }
            if($v['code']=='kf_email'){
                $root['REPLY_ADDRESS']=$v['val'];
            }
        }
        
        $root['url'] = wap_url("index","setting");
        $root['user_login_status'] = $user_login_status;

        $root['page_title']= "设置";
        return output($root);
    }
   
}
?>