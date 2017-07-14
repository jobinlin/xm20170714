<?php
class uc_shareApiModule extends MainBaseApiModule
{
    public function index() {
        $root = array();
        /*参数初始化*/
        
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else {
            $root['user_login_status'] = $user_login_status;
            
            $share_url = get_domain().APP_ROOT."/wap/index.php";
            if($user_id)
		    $share_url .= "?r=".base64_encode($user_id);
            $img_url =  get_abs_img_root(gen_qrcode($share_url));
            
            $root['share_url']=$share_url;
            $root['img_url']=$img_url;
            $root['share_content']=app_conf("SHOP_DESCRIPTION");
            //$root['share_title']=$GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']:"";
            
            $root['value']=app_conf('INVITE_REFERRALS');
            if(app_conf('INVITE_REFERRALS_TYPE')==1){
                $root['type']=1;
                $root['type_value']="积分";
            }else {
                $root['type_value']="现金";
            }
            if(app_conf("USER_REGISTER_MONEY")){
                $root['money']=app_conf("USER_REGISTER_MONEY");
            }
            if(app_conf("USER_REGISTER_SCORE")){
                $root['score']=app_conf("USER_REGISTER_SCORE");
            }
            
            $root['page_title']="分享有礼";
            
            return output($root);
        }
    }
}
?>