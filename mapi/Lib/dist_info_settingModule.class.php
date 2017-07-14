<?php
/**
 * @desc      
 * @author    驿站个人中心设置
 * @since      
 */
class dist_info_settingApiModule extends MainBaseApiModule
{
    public function index(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['dist_info'];
        $root['page_title'] = "设置";

        /*业务逻辑*/
        $root['dist_user_status'] = $account_info ? 1 : 0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

//        //返回商户权限
//        if(!check_module_auth()){
//            $root['is_auth'] = 0;
//            return output($root,0,"没有操作验证权限");
//        }else{
//            $root['is_auth'] = 1;
//        }


        //版本，客服，电话
        $conf = $GLOBALS['db']->getAll("select id,name,value from ".DB_PREFIX."conf where name = 'DB_VERSION'");
        $m_conf = $GLOBALS['db']->getAll("select id,title,val from ".DB_PREFIX."m_config where code in ('kf_phone','kf_email')");
        $version=include(APP_ROOT_PATH."public/version.php");
        $new_conf=array();
        foreach ($conf as $t => $v){
            if($v['name']=='DB_VERSION'){
                $v['value'].='.'.$version['APP_SUB_VER'];
            }
            $new_conf[$v['name']]=$v;
        }
        $root['APP_ABOUT_US']=app_conf("APP_ABOUT_US");
        $root['conf']=$new_conf?$new_conf:array();
        $root['m_conf'] = $m_conf?$m_conf:array();
        $root['url'] = wap_url("dist","info_setting");

        return output($root);
    }
}
