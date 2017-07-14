<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 关于商家登录的流程，所有接口除了登录，登出外，都要返回登录状态
   wap:除登录登录外其他的接口当没登录时都先跳到登录页
       登录页：不用访问接口，在wap服务端判断是否登录，已登录则跳到dealv页
 * @author jobin.lin
 *
 */



class dist_userApiModule extends MainBaseApiModule
{

    /**
     * 	 商户登录状态检测
     *
     * 	 输入:
     *  无
     *
     *  输出:
     *  biz_user_status [int] 用户状态  0未登录 / 1已经登录
     *  以下仅在biz_user_status为1时会返回
     *  [biz_user_status] => 1  [int] 商户登录状态 0未登录 /1已经登录
        [account_info] => Array [array] 商户账户数据
         (
            [account_name] => fanwe     [string]登录名称
            [account_password] => 6714ccb93be0fda4e51f206b91b46358  [string]登录密码
         )
    
     */
    public function check_dist_login(){
        $root = array();
        $root['dist_user_status'] = 0;
        if($GLOBALS['dist_info']){
            $root['dist_user_status'] = 1;
            $root['dist_info'] = array(
                'account_name'=>$GLOBALS['dist_info']['account_name'],
                'account_password'=>$GLOBALS['dist_info']['account_password']
            );
        }
       
        return output($root);
    }
    
    /**
     * 	 商户登录接口
     *
     * 	 输入:
     *  account_name: string 商户账号
     *  account_password: string 密码
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  
     *
     *  以下仅在status为1时会返回
     *   [biz_user_status] => 1  [int] 商户登录状态 0未登录 /1已经登录
         [account_info] => Array [array] 商户账户数据
         (
             [account_name] => fanwe     [string]登录名称
             [account_password] => 6714ccb93be0fda4e51f206b91b46358  [string]登录密码
         )
    
     */
	public function dologin(){
	    $root = array();
	    /*获取参数*/
	    $account_name = strim($GLOBALS['request']['account_name']);
	    $account_password = strim($GLOBALS['request']['account_password']);


// 	    /*业务逻辑*/
// 	    if($GLOBALS['account_info']){
// 	        //如果存在商户信息直接条状
// 	        $root['biz_user_status'] = 1;
// 	        $root['account_info'] = array(
// 	            'account_name'=>$GLOBALS['account_info']['account_name'],
// 	            'account_password'=>$GLOBALS['account_info']['account_password']
// 	        );
// 	        return output($root,1,"登录成功");
// 	    }

        //验证
	    if($account_name == ''){
	        return output($root,0,"请输入用户名");
	    }
	    if($account_password == ''){
	        return output($root,0,"请输入密码");
	    }
	    
	    require_once APP_ROOT_PATH."system/model/dist_user.php";
	    if(check_ipop_limit(get_client_ip(),"dist_dologin",intval(app_conf("SUBMIT_DELAY"))))
	        $result = do_login_dist($account_name,$account_password);
	    else
	    {
	        return output($root,0,"提交太快了");
	    }
	    $dist_info = $result['dist_info'];
	    
	    if($result['status'])
	    {
	        $root['dist_user_status'] = 1;
	        $root['dist_info'] = array(
	            'id'=>$dist_info['id'],
	            'account_name'=>$dist_info['username'],
	            'account_password'=>$dist_info['password']
	        );
	        
//	        $root['m_dist_nav_list'] = assign_dist_nav_list();
	        
	        return output($root,1,"登录成功");
	    }
	    else
	    {
	        if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
	        {
	            $err = "帐户不存在";
	        }
	        if($result['data'] == ACCOUNT_PASSWORD_ERROR)
	        {
	            $err = "帐户密码错误";
	        }
	        if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
	        {
	            $err = "帐户未激活";
	        }
	        return output($root,0,$err);
	    }	    
    }
    
    /**
     * 注销接口
     * 传入
     * 无
     *
     * 传出
     * 无
     */
    public function loginout()
    {
        require_once APP_ROOT_PATH."system/model/dist_user.php";
        
        loginout_dist();
        es_session::delete("m_dist_nav_list");
        return output("",1,"登出成功");
    }
    /**
     * 	 手机短信修改密码接口
     *
     * 	 输入:
     *  mobile: string 手机号
     *  sms_verify: string 验证码
     *  new_pwd:string 新密码
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *
     */
    public function phmodifypassword()
    {
        $user_mobile = strim($GLOBALS['request']['mobile']);
        $sms_verify = strim($GLOBALS['request']['sms_verify']);
        $new_pwd = strim($GLOBALS['request']['new_pwd']);
        if(app_conf("SMS_ON")==0)
        {
            return output("",0,"短信功能未开启");
        }
        if($user_mobile=="")
        {
            return output("",0,"请输入手机号");
        }
        if($sms_verify=="")
        {
            return output("",0,"请输入收到的验证码");
        }
        if($new_pwd=="")
        {
            return output("",0,"请输入密码");
        }


        if(strlen($new_pwd)<4||strlen($new_pwd)>30)
        {
            return output("",0,"密码必须在4-30个字符之间");
        }

        $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
        if(!$mobile_data)
        {
            return output("",0,"验证码过期，请重新发送");
        }

        if($mobile_data['code']==$sms_verify)
        {
            //开始绑定
            //1. 未登录状态提示登录
            //2. 已登录状态绑定
            $dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where tel = '".$user_mobile."'");
            if(!$dist_info)
            {
                return output("",0,"手机号未注册");
            }
            else
            {

                $dist_info['user_pwd'] = $new_pwd;
                $new_pwd = md5($new_pwd);
                $result = 1;  //初始为1
                //载入会员整合

                if($result>0)
                {
                    $GLOBALS['db']->query("update ".DB_PREFIX."distribution set password = '".$new_pwd."' where id = ".$dist_info['id'] );
                }
                else
                {
                    return output("",0,"密码修改失败");
                }
                return output("",1,"密码修改成功");
            }
        }
        else
        {
            return output("",0,"验证码错误");
        }
    }
    
}

