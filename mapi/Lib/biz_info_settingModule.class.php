<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
class biz_info_settingApiModule extends MainBaseApiModule
{
    
    /**
     * 	 商户设置接口
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
    
     Array
     (
     [biz_user_status] => 1                    int 商户登录状态 0未登录/1已登录
     [is_auth] => 0                              int 模块操作权限 0没有权限 / 1有权限
     [status] => 1                               int 结果状态 0失败 1成功
     [page_title]                                 设置
     
     [conf] => Array
        (
            [DB_VERSION] => Array                 版本
                (
                    [id] => 6
                    [name] => DB_VERSION
                    [value] => 5.0.8643
                )

            [SHOP_TEL] => Array                     客服
                (
                    [id] => 32
                    [name] => SHOP_TEL
                    [value] => 400-800-8888
                )

            [REPLY_ADDRESS] => Array                   邮箱
                (
                    [id] => 62
                    [name] => REPLY_ADDRESS
                    [value] => info@fanwe.com
                )

        )
    
     )
    
    
     */
    public function index(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $root['page_title'] = "设置";
        
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info ? 1 : 0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        //版本，客服，邮箱
        $conf = $GLOBALS['db']->getAll("select id,name,value from ".DB_PREFIX."conf where name in ('SHOP_TEL','REPLY_ADDRESS','DB_VERSION')");
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
        
        $root['url'] = wap_url("biz","info_setting");
        
        return output($root);
    }
}


?>