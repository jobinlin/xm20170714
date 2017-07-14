<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
class indexModule extends BizBaseModule
{
    
	public function index()
	{
	    global_run();
	    //判断用户是否登录
        if($GLOBALS['user_info']){
            //判断是否存在商户
            if(intval($GLOBALS['user_info']['is_merchant'])){
                if($GLOBALS['account_info']){
                    //获取权限
                    $biz_account_auth = get_biz_account_auth();

                    if(empty($biz_account_auth)){
                        showBizErr("此商户账户权限不足,请更换账户登录!",0,'',2);
                    }else{
                        $url_arr = explode("_",$biz_account_auth[0]);
                        $jump_url = url("biz",$url_arr[0]."#".$url_arr[1]);
                        app_redirect($jump_url);
                    }
                }else{
                    app_redirect(url("biz","user#login"));
                }

            }else{//用户登录但是没有绑定商户的时候跳转商户入驻申请页面
                app_redirect(url("biz","user#register"));
            }
        }else{//未登录的情况
            app_redirect(url("biz","user#login"));
        }
	}
	
	
}
?>