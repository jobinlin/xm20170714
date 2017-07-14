<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_accountModule extends MainBaseModule
{
	public function index()
	{
	    //==基本参数定义==
	    global_run();
	    init_app_page();
	    $user_info = $GLOBALS['user_info'];
	    
	    //==业务逻辑部分==
	    if($GLOBALS['user_info']['is_tmp']==1)
	    {
	        if(check_save_login()==LOGIN_STATUS_NOLOGIN)
	        {
	            app_redirect(url("index","user#login"));
	        }
	    }
	    else
	    {
	        if(check_save_login()!=LOGIN_STATUS_LOGINED)
	        {
	            app_redirect(url("index","user#login"));
	        }
	    }

	    /*第三方微博列表*/
	    $iconfont = require_once(APP_ROOT_PATH.'system/weibo_iconfont_cfg.php');
	    $apis = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login");

	    foreach($apis as $k=>$v)
	    {
	        if($user_info[strtolower($v['class_name'])."_id"])
	        {
	            $apis[$k]['is_bind'] = 1;
	            if($user_info["is_syn_".strtolower($v['class_name'])]==1)
	            {
	                $apis[$k]['is_syn'] = 1;
	            }
	            else
	            {
	                $apis[$k]['is_syn'] = 0;
	            }
	        }
	        else
	        {
	            $apis[$k]['is_bind'] = 0;
	        }
            if(file_exists(APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php"))
            {
            	require_once(APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php");
            	
            	$api_class = $v['class_name']."_api";
            	$api_obj = new $api_class($v);
            	$api_item = $api_obj->get_bind_api_url_arr();
            	$apis[$k]['api_item'] = $api_item;
            	$apis[$k]['url'] = $api_url['url'];
            	$apis[$k]['iconfont'] = $iconfont[strtolower($v['class_name'])];
            }
	    }

	    //地区列表
	    $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
	    foreach($region_lv2 as $k=>$v)
	    {
	        if($v['id'] == intval($GLOBALS['user_info']['province_id']))
	        {
	            $region_lv2[$k]['selected'] = 1;
	            break;
	        }
	    }
	    $region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($GLOBALS['user_info']['province_id']));  //三级地址
	    foreach($region_lv3 as $k=>$v)
	    {
	        if($v['id'] == intval($GLOBALS['user_info']['city_id']))
	        {
	            $region_lv3[$k]['selected'] = 1;
	            break;
	        }
	    }
	    
	    //==模版数据申明==
	    $GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);
	    $GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
	    $GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
	    $GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
	    $GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());
	    
	    $GLOBALS['tmpl']->assign("apis",$apis);
	    
	    //==通用模版参数定义==
	    assign_uc_nav_list();//左侧导航菜单
	    $GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
	    $GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_ACCOUNT']);
	    $GLOBALS['tmpl']->display("uc/uc_account_index.html");
	    
	}
	
	public function save()
	{
		global_run();
		require_once(APP_ROOT_PATH.'system/model/user.php');
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = strim($v);
		}

		$data = array();
		$data['error'] = 0;
		$data['info'] = '';
		
		
		if($GLOBALS['user_info']['is_tmp']==1)
		{
			if(check_save_login()==LOGIN_STATUS_NOLOGIN)
			{
				$data['error'] = 1000;
				ajax_return($data);
			}
			
			//临时会员必需有密码
			$current_password = strim($_REQUEST['current_password']);
			if(md5($current_password.$GLOBALS['user_info']['code'])!=$GLOBALS['user_info']['user_pwd']&&strim($_REQUEST['user_pwd'])=="")
			{
				if($current_password==""){
                    $data['error'] = 1;
                    $data['info']	=	"请输入您的密码";
                    ajax_return($data);
                }else{
                    $data['error'] = 1;
                    $data['info']	=	"密码错误";
                    ajax_return($data);
                }
			}
			if($GLOBALS['user_info']['user_name']==""&&$_REQUEST['user_name']=="")
			{
				$data['error'] = 1;
				$data['info']	=	"请输入您的用户名";
				ajax_return($data);
			}
			$len = mb_strlen($GLOBALS['user_info']['user_name']);
			if ($len > 16 || $len < 4) {
				$data['error'] = 1;
				$data['info'] = '用户名应在2-16个字符之间';
			}
			if($GLOBALS['user_info']['email']==""&&$_REQUEST['email']=="")
			{
				$data['error'] = 1;
				$data['info']	=	"请输入您的真实邮箱";
				ajax_return($data);
			}
		}
		else
		{
			if(check_save_login()!=LOGIN_STATUS_LOGINED)
			{
				$data['error'] = 1000;
				ajax_return($data);
			}
			
			//用户有修改密码
			if(strim($_REQUEST['user_pwd'])!='' && strim($_REQUEST['user_pwd_confirm'])!=''){
			    if(strim($_REQUEST['current_password'])==''){
			        $data['error'] = 1;
			        $data['info']	=	"修改密码，必须输入当前密码进行验证!";
			        ajax_return($data);
			    }elseif (strim($_REQUEST['current_password'])!='' && md5(strim($_REQUEST['current_password']).$GLOBALS['user_info']['code'])!=$GLOBALS['user_info']['user_pwd']){
			        $data['error'] = 1;
			        $data['info']	=	"当前密码错误，无法修改密码!";
			        ajax_return($data);
			    }
			}
			
		}

		

		$account_mobile = $_REQUEST['mobile'];
		
		$sms_verify = $_REQUEST['sms_verify'];

		if($account_mobile!=''&& $account_mobile!=$GLOBALS['user_info']['mobile']){
			if($_REQUEST['mobile']=="")
			{
				$data['error'] = 1;
				$data['info']	=	"请输入手机号";
				ajax_return($data);
			}
			
		 	if(strim($_REQUEST['current_password'])==''){
		        $data['error'] = 1;
		        $data['info']	=	"修改手机号，必须输入当前密码进行验证!";
		        ajax_return($data);
		    }elseif (strim($_REQUEST['current_password'])!='' && md5(strim($_REQUEST['current_password']).$GLOBALS['user_info']['code'])!=$GLOBALS['user_info']['user_pwd']){
		        $data['error'] = 1;
		        $data['info']	=	"当前密码错误，无法修改密码!";
		        ajax_return($data);
		    }
			
			if(app_conf("SMS_ON")==1)
			{
				if($_REQUEST['sms_verify']=="")
				{
					$data['error'] = 1;
					$data['info']	=	"请输入收到的验证码";
					ajax_return($data);
				}
				
				//短信码验证
				$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
				$GLOBALS['db']->query($sql);
				
				$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$account_mobile."'");
				
				if($mobile_data['code']!=$sms_verify)
				{
					$data['error'] = 1;
					$data['info']	=  "验证码错误";
					ajax_return($data);
				}
			}
		}
		if($account_mobile!=''){
			$user_info=$GLOBALS['db']->getRow("select id,union_id,user_pwd from ".DB_PREFIX."user where mobile = '".$account_mobile."' and id!=".$GLOBALS['user_info']['id']);
			if($user_info){
				if($GLOBALS['user_info']['mobile']==""&&$GLOBALS['user_info']['union_id']){
					if($user_info['union_id']){
						$data['error'] = 1;
						$data['info']	=  "该手机号已绑定微信，请解绑后再来";
						ajax_return($data);
					}elseif($user_info['bind_count']>=10){
						$data['error'] = 1;
						$data['info']	=  "该账户合并会员次数到达上限";
						ajax_return($data);
					}
					$update = array();
					if($user_info['pid']==0&&$GLOBALS['user_info']['pid']>0&&$GLOBALS['user_info']['pid']!=$user_info['id']){
						//保留上级推荐人关系
						$update['pid']=$GLOBALS['user_info']['pid'];
					}
					$update['bind_count']=$user_info['bind_count']+1;
					$update['wx_openid']=$GLOBALS['user_info']['wx_openid'];
					$update['m_openid']=$GLOBALS['user_info']['m_openid'];
					$update['union_id']=$GLOBALS['user_info']['union_id'];
					$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'mobile='.$account_mobile);
					//保留下级推荐人
					$update = array();
					$update['pid']=$user_info['id'];
					$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'pid='.$GLOBALS['user_info']['id'].' and id!='.$user_info['id']);
					if($GLOBALS['user_info']['money']>0||$GLOBALS['user_info']['score']>0||$GLOBALS['user_info']['point']>0){
						require_once(APP_ROOT_PATH."system/model/user.php");
						$data_1 = array("money"=>$GLOBALS['user_info']['money'],"score"=>$GLOBALS['user_info']['score'],"point"=>$GLOBALS['user_info']['point']);
						modify_account($data_1,$user_info['id'],"在".to_date(NOW_TIME)."微信账号".$GLOBALS['user_info']['user_name']."绑定手机,合并到手机账号");
					}
					
					$GLOBALS['db']->query("delete from ".DB_PREFIX."user where id = ".$GLOBALS['user_info']['id']);
					$result = do_login_user($account_mobile,$user_info['user_pwd']);
					if($result['status'])
					{
						$s_user_info = es_session::get("user_info");
						//自动登录，保存cookie
						$user_data = $s_user_info;
						es_cookie::set("user_name",$user_data['email'],3600*24*30);
						es_cookie::set("user_pwd",md5($user_data['user_pwd']."_EASE_COOKIE"),3600*24*30);
						$data['jump'] = url("index","uc_account#index");
						ajax_return($data);
						
					}
				}
                else{
					$data['error'] = 1;
					$data['info']	=  "手机号已被抢占";
					ajax_return($data);
				}
			}
		}
        $this->_checkUserFx();
		unset($_REQUEST['is_check_mobile']);
		unset($_REQUEST['sms_verify']);

		$_REQUEST['id'] = $GLOBALS['user_info']['id'];
		$res = save_user($_REQUEST,'UPDATE');

		if($res['status'] == 1)
		{
		    
			$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$account_mobile."'");
			$s_user_info = es_session::get("user_info");
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($s_user_info['id'])."'");
			es_session::set("user_info",$user_info);
			if(intval($_REQUEST['is_ajax'])==1){
				$data['jump'] = url("index","uc_account#index");
				ajax_return($data);
			}
			else{
				showSuccess($GLOBALS['lang']['SAVE_USER_SUCCESS']);
			}
				
		}
		else
		{
			$error = $res['data'];		
			if(!$error['field_show_name'])
			{
					$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
			}
			
			$data['error'] = 1;
			$data['info'] = $error_msg;
			
			if(intval($_REQUEST['is_ajax'])==1)
				ajax_return($data);
			else
				showErr($data);
		}
	}

    /**
     * @desc 判断用户是否有网宝
     * @author    吴庆祥
     */
    private function _checkUserFx(){
       if(!IS_OPEN_AGENCY)return;
       $user_info=$GLOBALS['user_info'];
       $update=array("city_code"=>$user_info['city_code'],"agency_id"=>$user_info['agency_id']);
       $request_city_id=$_REQUEST['city_id'];
       if(!$request_city_id){
           $data['error'] = 1;
           $data['info']	=  "城市不能为空";
           ajax_return($data);
       }
       if(!$update['city_code']){
           $city_code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."delivery_region where id=".$request_city_id);
           $update['city_code']=$city_code;
       }
       if(!$update['agency_id']){
           $agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code=".$update['city_code']);
           if($agency_id){
               $update['agency_id']=$agency_id;
           }
       }
       //无更新数据直接跳过
       if(!$update['city_code']&&!$update['agency_id'])return;
       $GLOBALS['db']->autoExecute(DB_PREFIX."user",$update,"update","id=".$user_info['id']);
       refresh_user_info();
   }
}