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



class biz_userApiModule extends MainBaseApiModule
{

    /**
     * 	 商户登录页
     *
     * 	 输入:
     *  无
     *
     *  输出:
     *  logo [string] 商城logo

     */
    public function login(){

        $root['logo']=get_abs_img_root(app_conf("SHOP_LOGO"));
        $root['page_title'] = "商户登录";
        return output($root);
    }
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
    public function check_biz_login(){
        $root = array();
        $root['biz_user_status'] = 0;
        if($GLOBALS['account_info']){
            $root['biz_user_status'] = 1;
            $root['account_info'] = array(
                'account_name'=>$GLOBALS['account_info']['account_name'],
                'account_password'=>$GLOBALS['account_info']['account_password']
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


        //验证
        if($account_name == ''){
            return output($root,0,"请输入用户名");
        }
        if($account_password == ''){
            return output($root,0,"请输入密码");
        }

        require_once APP_ROOT_PATH."system/model/user.php";
        if(check_ipop_limit(get_client_ip(),"biz_dologin",intval(app_conf("SUBMIT_DELAY"))))
            $result = do_login_user($account_name,$account_password);
        else
        {
            return output($root,0,"提交太快了");
        }

        global $account_info;
        if($result['status'])
        {
            global_run();

            //获取权限
            $biz_account_auth = get_biz_account_auth();

            if(empty($biz_account_auth)){
                return output($root,0,"请更换帐号登录，此账户还没有分配权限");
            }else{
                $root['biz_user_status'] = 1;
                $root['account_info'] = array(
                    'id'=>$account_info['id'],
                    'account_name'=>$account_info['account_name'],
                    'account_password'=>$account_info['account_password'],
                    'user_name'=>$result['user']['user_name'],
                    'user_pwd'=>$result['user']['user_pwd']
                );
            }

            if (APP_INDEX == 'app'){
                //手机类型dev_type=android,ios
                $data = array();
                $data['dev_type'] = strim($GLOBALS['request']['dev_type']);
                $data['device_token'] = strim($GLOBALS['request']['device_token']);

                $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_account", $data, 'UPDATE','id = '.intval($account_info['id']));
            }

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
        require_once(APP_ROOT_PATH."system/libs/biz_user.php");

        if (APP_INDEX == 'app'){
            //手机类型dev_type=android,ios
            $account_info = es_session::get("account_info");
            $data = array();
            $data['dev_type'] = '';
            $data['device_token'] = '';
            $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_account", $data, 'UPDATE','id = '.intval($account_info['id']));
        }

        loginout_biz();


        return output("",1,"登出成功");
    }

    /**
     * 找回密码初始化页面
     *
     * 输入:
     *     无
     *
     * 输出:
     *     Array (
    [page_title] => 找回密码
    [biz_user_status] => 1
    [ctl] => biz_test
    [act] => index
    [status] => 1
    [info] =>
    [city_name] => 福州
    [return] => 1
    [sess_id] => jj0vparnu9imivrcm6fba244b1
    [ref_uid] =>
    )

     *
     * @return mixed
     */
    public function getpassword()
    {
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $root['page_title'] = "找回密码";
        $status = 1;
        $msg = '';

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info ? 1 : 0;
        if (empty($account_info)){
            $status = 0;
            $msg = "商户未登录";
        }
        $root['account'] = $GLOBALS['account_info'];
        // print_r($GLOBALS['user_info']);exit;
        return output($root, $status, $msg);
    }

    /**
     * 找回密码
     * 输入:
     *     mobile: sms_verify 验证码
     *     account_pwd: 新的密码
     *
     * 输出:
     *     Array (
    [ctl] => biz_test
    [act] => index
    [status] => 0
    [info] => 验证码错误
    [city_name] => 福州
    [return] => 1
    [sess_id] => jj0vparnu9imivrcm6fba244b1
    [ref_uid] =>
    )
     * @return
     */
    public function dogetpwd()
    {
        $root = array();
        $account_info = $GLOBALS['account_info'];

        $status = 0;
        $request = $GLOBALS['request'];

        if (empty($account_info)){
            $status = -1;
            $info = '商户未登录';
            goto end;
        }

        /*if (!check_mobile($request['mobile'])) {
            $info = '手机号码格式错误';
            goto end;
        }*/

        // $mobile = strim($request['mobile']);
        $mobile = $account_info['mobile'];
        $sms_verify = intval($request['sms_verify']);
        $sql = 'SELECT add_time FROM '.DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
        $add_time = $GLOBALS['db']->getOne($sql);
        if (empty($add_time)) {
            $info = '验证码错误';
        } elseif ($add_time < NOW_TIME - 300) {
            $info = '验证码已过期';
        } else {
            $account_pwd = strim($request['account_pwd']);
            if (strlen($account_pwd) < 4) {
                $info = '密码过短';
            } else {
                $biz_id = $account_info['id'];
                $update = array('account_password' => md5($account_pwd), 'update_time' => NOW_TIME);
                $GLOBALS['db']->autoExecute(DB_PREFIX.'supplier_account', $update, 'UPDATE', 'id='.$biz_id);
                $info = '密码修改成功';
                $status = 1;

                // 删除验证码
                $sql = "DELETE FROM ".DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
                $GLOBALS['db']->query($sql);
            }
        }

        end:

        return output($root, $status, $info);
    }
}
?>