<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class userApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 普通登录接口
	 * 
	 * 	 输入:  
	 *  user_key: string 会员账号： 手机号/邮箱/email
	 *  user_pwd: string 密码
	 *  
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 */
	public function dologin()
	{
		$root = array();	

		if(strim($GLOBALS['request']['user_key'])=="")
		{
			return output("",0,"请输入登录帐号");
		}
		if(strim($GLOBALS['request']['user_pwd'])=="")
		{
			return output("",0,"请输入密码");
		}
		
		
		require_once(APP_ROOT_PATH."system/model/user.php");
		if(check_ipop_limit(get_client_ip(),"user_dologin",intval(app_conf("SUBMIT_DELAY"))))
			$result = do_login_user(strim($GLOBALS['request']['user_key']),strim($GLOBALS['request']['user_pwd']),$is_wap=true);
		else
		{
			return output("",0,"提交太快了");
		}
			
		if($result['status'])
		{
			$s_user_info = es_session::get("user_info");
			$data['id'] = $s_user_info['id'];
			$data['user_name'] = $s_user_info['user_name'];
			$data['user_pwd'] = $s_user_info['user_pwd'];
			$data['email'] = $s_user_info['email'];
			$data['mobile'] = $s_user_info['mobile'];
			$data['is_tmp'] = $s_user_info['is_tmp'];
			return output($data,1,"登录成功");				
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$field = "user_key";
				$err = "用户不存在";
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$field = "user_pwd";
				$err = "密码错误";
			}
			if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
			{
				$field = "user_key";
				$err = "用户未通过验证";
			}
			return output("",0,$err);
		}
	}
	
	
	/**
	 * 	 手机短信登录接口
	 *
	 * 	 输入:
	 *  mobile: string 手机号
	 *  sms_verify: string 验证码
	 *
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 */
	public function dophlogin()
	{
		$user_mobile = strim($GLOBALS['request']['mobile']);
		$sms_verify = strim($GLOBALS['request']['sms_verify']);
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
		
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
		
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
		
		
		if($mobile_data['code']==$sms_verify)
		{
			//开始登录
			//1. 有用户使用已有用户登录
			//2. 无用户产生一个用户登录
			require_once(APP_ROOT_PATH."system/model/user.php");
			if(check_ipop_limit(get_client_ip(),"user_dophlogin",intval(app_conf("SUBMIT_DELAY"))))
			{
				$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
				$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
				if($user_info)
				{
					//使用已有用户
					$result = do_login_user($user_info['user_name'],$user_info['user_pwd'],$is_wap=true);
		
					if($result['status'])
					{

						$s_user_info = es_session::get("user_info");
						$data['id'] = $s_user_info['id'];
						$data['user_name'] = $s_user_info['user_name'];
						$data['user_pwd'] = $s_user_info['user_pwd'];
						$data['email'] = $s_user_info['email'];
						$data['mobile'] = $s_user_info['mobile'];
						$data['is_tmp'] = $s_user_info['is_tmp'];
						es_session::set("send_sms_code_0_ip", null);
						return output($data,1,"登录成功");							
							
					}
					else
					{
						if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
						{
							$field = "";
							$err = "用户不存在";
						}
						if($result['data'] == ACCOUNT_PASSWORD_ERROR)
						{
							$field = "";
							$err = "密码错误";
						}
						if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
						{
							$field = "";
							$err = "用户未通过验证";
						}
						return output("",0,$err);
					}
				}
				else
				{
					//ip限制
					$ip = get_client_ip();
					$ip_nums = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where login_ip = '".$ip."'");
					if($ip_nums>intval(app_conf("IP_LIMIT_NUM"))&&intval(app_conf("IP_LIMIT_NUM"))>0)
					{
						return output("",0,"IP受限");
					}
						
						
					if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".$user_mobile."' or mobile = '".$user_mobile."' or email = '".$user_mobile."'")>0)
					{
						return output("",0,"手机号已被抢占");
					}
						
					//生成新用户
					$user_data = array();
					$user_data['mobile'] = $user_mobile;
						
					/*
					 $user_data['user_pwd'] = md5(rand(100000,999999));
					$user_data['is_effect'] = 1;
					$user_data['pid'] = $GLOBALS['ref_uid'];
					$user_data['create_time'] = NOW_TIME;
					$user_data['update_time'] = NOW_TIME;
					$user_data['login_time'] = NOW_TIME;
					$user_data['login_ip'] = get_client_ip();
					$user_data['is_tmp'] = 1;
					$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT","","SILENT");
					$user_id = intval($GLOBALS['db']->insert_id());
					if($user_id==0)
					{
					$data['status'] = false;
					$data['info']	=	"手机号已被抢占";
					ajax_return($data);
					}
					$user_name = "游客_".$user_id;
					$GLOBALS['db']->query("update ".DB_PREFIX."user set user_name = '".$user_name."' where id = ".$user_id,"SILENT");
					$result = do_login_user($user_name,$user_data['user_pwd']);
					*/
						
					$rs_data = auto_create($user_data, 1);
					if(!$rs_data['status'])
					{
						return output("",0,$rs_data['info']);
					}
						
					$result = do_login_user($rs_data['user_data']['user_name'],$rs_data['user_data']['user_pwd'],$is_wap=true);
						
					if($result['status'])
					{
						$s_user_info = es_session::get("user_info");
						$data['id'] = $s_user_info['id'];
						$data['user_name'] = $s_user_info['user_name'];
						$data['user_pwd'] = $s_user_info['user_pwd'];
						$data['email'] = $s_user_info['email'];
						$data['mobile'] = $s_user_info['mobile'];
						$data['is_tmp'] = $s_user_info['is_tmp'];
						$data['new_user'] = 1; // 新注册的用户标识
						es_session::set("send_sms_code_0_ip", null);
						return output($data,1,"登录成功");	

					}
				}
			}
			else
			{
				return output("",0,"提交太快了");
			}
		}
		else
		{
			$log_msg = date('Y-m-d H:i').'验证码错误bug：请求验证码:'.$sms_verify.'; 数据库验证码:'.$mobile_data['code'];
			return output("",0,"验证码错误");
		}
	}
	
	
	/**
	 * 	 手机号码绑定接口
	 *
	 * 	 输入:
	 *  mobile: string 手机号
	 *  sms_verify: string 验证码
	 *
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
	 *
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 */
	public function dophbind()
	{
		$user_mobile = strim($GLOBALS['request']['mobile']);
		$sms_verify = strim($GLOBALS['request']['sms_verify']);
		global_run();
		$data['user_login_status'] = check_login();
		if(app_conf("SMS_ON")==0)
		{
			return output($data,0,"短信功能未开启");
		}
		if($user_mobile=="")
		{
			return output($data,0,"请输入手机号");
		}
		if($sms_verify=="")
		{
			return output($data,0,"请输入收到的验证码");
		}
	
		
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
	
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
	
		if($mobile_data['code']==$sms_verify)
		{
			//开始绑定
			//1. 未登录状态提示登录
			//2. 已登录状态绑定
			require_once(APP_ROOT_PATH."system/model/user.php");
			
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
			if($user_info)
			{
				if($user_info['union_id']){
					return output($data,0,"手机号已绑定微信，请解绑后再来");
				}elseif(!$GLOBALS['user_info']['union_id']){
					return output($data,0,"微信账号才能合并已注册手机");
				}elseif($user_info['bind_count']>10){
					return output($data,0,"该账户合并会员次数到达上限");
				}
				if($GLOBALS['user_info']){
			        if($GLOBALS['user_info']['mobile']==''){
			            $update = array();
						if($user_info['pid']==0&&$GLOBALS['user_info']['pid']>0&&$GLOBALS['user_info']['pid']!=$user_info['id']){
							//保留上级推荐人关系
							$update['pid']=$GLOBALS['user_info']['pid'];
						}
						$update['bind_count']=$user_info['bind_count']+1;
						$update['wx_openid']=$GLOBALS['user_info']['wx_openid'];
						$update['m_openid']=$GLOBALS['user_info']['m_openid'];
						$update['union_id']=$GLOBALS['user_info']['union_id'];
						$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'mobile='.$user_mobile);
						//保留下级推荐人
						$update = array();
						$update['pid']=$user_info['id'];
						$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'pid='.$GLOBALS['user_info']['id'].' and id!='.$user_info['id']);
						if($GLOBALS['user_info']['money']>0||$GLOBALS['user_info']['score']>0||$GLOBALS['user_info']['point']>0){
							require_once(APP_ROOT_PATH."system/model/user.php");
							$data = array("money"=>$GLOBALS['user_info']['money'],"score"=>$GLOBALS['user_info']['score'],"point"=>$GLOBALS['user_info']['point']);
							modify_account($data,$user_info['id'],"在".to_date(NOW_TIME)."微信账号".$GLOBALS['user_info']['user_name']."绑定手机,合并到手机账号");
						}
						$GLOBALS['db']->query("delete from ".DB_PREFIX."user where id = ".$GLOBALS['user_info']['id']);
			        }else{
						return output($data,0,"未绑定过手机的用户，才能绑定手机账号");
					}
			    }else{
			        return output($data,0,"您还未登录");
			    }
				$result = do_login_user($user_mobile,$user_info['user_pwd']);
			    if($result['status'])
			    {
			    	$s_user_info = es_session::get("user_info");
			    	$data['id'] = $s_user_info['id'];
			    	$data['user_name'] = $s_user_info['user_name'];
			    	$data['user_pwd'] = $s_user_info['user_pwd'];
			    	$data['email'] = $s_user_info['email'];
			    	$data['mobile'] = $s_user_info['mobile'];
			    	$data['is_tmp'] = $s_user_info['is_tmp'];
			    	$data['user_login_status'] = check_login();
			    	refresh_user_info();
			    	es_session::set("send_sms_code_0_ip", null);
			    
			    	return output($data,1,"绑定成功");
			    		
			    }
			}
			else
			{
				
				if($GLOBALS['user_info'])
				{
					$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
					$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$user_mobile."' where id = ".$GLOBALS['user_info']['id']);

					$result = do_login_user($user_mobile,$GLOBALS['user_info']['user_pwd'],$is_wap=true);
					
					if($result['status'])
					{
						$s_user_info = es_session::get("user_info");
						$data['id'] = $s_user_info['id'];
						$data['user_name'] = $s_user_info['user_name'];
						$data['user_pwd'] = $s_user_info['user_pwd'];
						$data['email'] = $s_user_info['email'];
						$data['mobile'] = $s_user_info['mobile'];
						$data['is_tmp'] = $s_user_info['is_tmp'];
						$data['user_login_status'] = check_login();
						return output($data,1,"绑定成功");
					
					}
				}
				else
				{					
					return output($data,0,"您还未登录");
				}

				
			}
		}
		else
		{
			return output($data,0,"验证码错误");
		}
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
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
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
		    	 
	
		/* $sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN)."and mobile_phone=".$user_mobile;
		$GLOBALS['db']->query($sql); */
	
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
			require_once(APP_ROOT_PATH."system/model/user.php");
				
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
			if(!$user_info)
			{
				return output("",0,"手机号未注册");
			}
			else
			{	
				
				$user_info['user_pwd'] = $new_pwd;
				$new_pwd = md5($new_pwd);
				$result = 1;  //初始为1
				//载入会员整合
				$integrate_code = trim(app_conf("INTEGRATE_CODE"));
				if($integrate_code!='')
				{
					$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
					if(file_exists($integrate_file))
					{
						require_once $integrate_file;
						$integrate_class = $integrate_code."_integrate";
						$integrate_obj = new $integrate_class;
					}
				}
				
				if($integrate_obj)
				{
					$result = $integrate_obj->edit_user($user_info,$user_info['user_pwd']);
				}
				if($result>0)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd = '".$new_pwd."',password_verify='' where id = ".$user_info['id'] );					
				}
				else
				{
					return output("",0,"密码修改失败");				
				}
				
				/* $result = do_login_user($user_mobile,$new_pwd);
					
				if($result['status'])
				{
					$s_user_info = es_session::get("user_info");
					$data['id'] = $s_user_info['id'];
					$data['user_name'] = $s_user_info['user_name'];
					$data['user_pwd'] = $s_user_info['user_pwd'];
					$data['email'] = $s_user_info['email'];
					$data['mobile'] = $s_user_info['mobile'];
					$data['is_tmp'] = $s_user_info['is_tmp'];
					return output($data,1,"密码修改成功");						
				}	 */
				
				return output("",1,"密码修改成功");
			}
		}
		else
		{
			return output("",0,"验证码错误");
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
		require_once(APP_ROOT_PATH."system/model/user.php");
		loginout_user();
		es_cookie::delete("user_name");
		es_cookie::delete("user_pwd");
		es_session::delete("wx_info");	
		return output("",1,"登出成功");
	}
	
	
	/**
	 * 会员普通注册接口
	 * 输入参数:
	 * user_name: string 注册的用户名
	 * user_email: string 注册的邮箱
	 * user_pwd: string 注册的密码
	 * ref_uid: int 推荐人
	 * ref_uname: string 推荐人用户名
	 * 
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 * 
	 */
	public function doregister()
	{		

		//ip限制
		$ip = get_client_ip();
		$ip_nums = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where login_ip = '".$ip."'");
		if($ip_nums>intval(app_conf("IP_LIMIT_NUM"))&&intval(app_conf("IP_LIMIT_NUM"))>0)
		{
			return output("",0,"IP受限");
		}

		$user_name = strim($GLOBALS['request']['user_name']);
		$email = strim($GLOBALS['request']['user_email']);
		$user_pwd = strim($GLOBALS['request']['user_pwd']);
		$ref_uid = intval($GLOBALS['request']['ref_uid']);
		if($ref_uid==0&&$GLOBALS['request']['ref_uname'])
		{
			$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where user_name = '".strim($GLOBALS['request']['ref_uname'])."'"));
		}
		
		
		require_once(APP_ROOT_PATH."system/model/user.php");
		$user_data['user_name'] = $user_name;
		$user_data['email'] = $email;
		$user_data['user_pwd'] = $user_pwd;
				
		if($user_data['user_pwd']=='')
		{
			return output("",0,"请输入密码");
		}
		
		$user_data['pid'] = $ref_uid;

		$res = save_user($user_data);
		
		if($res['status'] == 1)
		{
			//自动订阅邮箱
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_list where mail_address = '".$user_data['email']."'")==0)
			{
				$mail_item['city_id'] = intval($GLOBALS['city']['id']);
				$mail_item['mail_address'] = $user_data['email'];
				$mail_item['is_effect'] = app_conf("USER_VERIFY");
				$GLOBALS['db']->autoExecute(DB_PREFIX."mail_list",$mail_item,'INSERT','','SILENT');
			}
			$user_id = intval($res['data']);
			//更新来路
			$GLOBALS['db']->query("update ".DB_PREFIX."user set referer = '".$GLOBALS['referer']."' where id = ".$user_id);
			
			//在此自动登录
			do_login_user($user_data['email'],$user_data['user_pwd'],$is_wap=true);
			
			$s_user_info = es_session::get("user_info");
			$data['id'] = $s_user_info['id'];
			$data['user_name'] = $s_user_info['user_name'];
			$data['user_pwd'] = $s_user_info['user_pwd'];
			$data['email'] = $s_user_info['email'];
			$data['mobile'] = $s_user_info['mobile'];
			$data['is_tmp'] = $s_user_info['is_tmp'];
			
			//原来为直接挑战 现改为 完善资料
			return output($data,1,"注册成功");
			
		}
		else
		{
			$error = $res['data'];
			if($error['field_name']=="user_name")
			{
				$error_field = "用户名";
			}
			elseif($error['field_name']=="email")
			{
				$error_field = "邮箱";
			}
			elseif($error['field_name']=="user_pwd")
			{
				$error_field = "密码";
			}
			
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = $error_field."不能为空";
			}
			if($error['error']==FORMAT_ERROR)
			{
			    if($error['field_name']=="user_pwd"){
			        $error_msg = $error_field."必须在4-30个字符之间";
			    }else {
			        $error_msg = $error_field."格式错误";
			    }
				
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = $error_field."已经存在";
			}
			
			return output("",0,$error_msg);
			
		}
	}
	/**
	 * 会员手机注册接口
	 * 输入参数:
	 * user_mobile: string 注册的用户名
	 * user_pwd: string 注册的密码
	 *  sms_verify: string 验证码
	 * ref_uid: int 推荐人
	 * ref_uname: string 推荐人用户名
	 * 
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *
	 *  以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 * 
	 */
	public function dophregister()
	{
		//ip限制
		$ip = get_client_ip();
		$ip_nums = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where login_ip = '".$ip."'");
		if($ip_nums>intval(app_conf("IP_LIMIT_NUM"))&&intval(app_conf("IP_LIMIT_NUM"))>0)
		{
			return output("",0,"IP受限");
		}
	
		$user_mobile = strim($GLOBALS['request']['user_mobile']);
		$sms_verify = strim($GLOBALS['request']['sms_verify']);
		$user_pwd = strim($GLOBALS['request']['user_pwd']);
		$ref_uid = intval($GLOBALS['request']['ref_uid']);
		if(app_conf("SMS_ON")==0)
		{
			return output("",0,"短信功能未开启");
		}
		
		if($user_mobile=="")
		{
			$data['field'] = "user_mobile";
			return output($data,0,"请输入手机号");
		}
		
		if($user_pwd=='')
		{
			$data['field'] = "user_pwd";
			return output($data,0,"请输入密码");
		}
		if(strlen($user_pwd)<4||strlen($user_pwd)>30)
		{
			if(strlen($user_pwd)<4){
				$info="密码不能小于4位";
			}elseif(strlen($user_pwd)>30){
				$info="密码不能多于30位";
			}
			$data['field'] = "user_pwd";
			return output($data,0,$info);
		}
		
		
		if($sms_verify=="")
		{
			$data['info']	=	"请输入收到的验证码";
			$data['field'] = "sms_verify";
			return output($data,0,"请输入收到的验证码");
		}
		
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
		
		if($mobile_data['code']!=$sms_verify)
		{
			$data['field'] = "sms_verify";
			return output($data,0,"验证码错误");
		}
		
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".$user_mobile."' or mobile = '".$user_mobile."' or email = '".$user_mobile."'")>0)
		{
			$data['field'] = "user_mobile";
			return output($data,0,"手机号已被抢占");
		}
		
		if($ref_uid==0&&$GLOBALS['request']['ref_uname'])
		{
			$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where user_name = '".strim($GLOBALS['request']['ref_uname'])."'"));
		}
	
	
		require_once(APP_ROOT_PATH."system/model/user.php");
		$user_data = array();
		$user_data['mobile'] = $user_mobile;
		$user_data['user_pwd'] = md5($user_pwd);
	
		$rs_data = auto_create($user_data, 1);
		if(!$rs_data['status'])
		{
			return output("",0,$rs_data['info']);
		}
		$user_id = intval($rs_data['user_data']['id']);
		//更新来路
		$GLOBALS['db']->query("update ".DB_PREFIX."user set referer = '".$GLOBALS['referer']."' where id = ".$user_id);
		
		
		$result = do_login_user($rs_data['user_data']['user_name'],$rs_data['user_data']['user_pwd'],$is_wap=true);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'"); //删除验证码
		if($result['status'])
		{
			$s_user_info = es_session::get("user_info");
				
			$data['id'] = $s_user_info['id'];
			$data['user_name'] = $s_user_info['user_name'];
			$data['user_pwd'] = $s_user_info['user_pwd'];
			$data['email'] = $s_user_info['email'];
			$data['mobile'] = $s_user_info['mobile'];
			$data['is_tmp'] = $s_user_info['is_tmp'];
				
			return output($data,1,"注册成功");
		
		}
		
	}
	public function protocol()
	{
		$data=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."agreement where is_effect = 1 ORDER BY sort desc");
		//echo "select * from ".DB_PREFIX."agreement where is_effect = 1 ORDER BY sort desc'";
		//echo "<pre>";print_r($data);exit;
		return output($data,1,"用户协议");
	}

	/**
	 * 密码修改展示页方法
	 * @return array 
	 */
	public function changepassword()
	{
		$user_info = $GLOBALS['user_info'];
		$user_login_status = check_login();

		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$root['user_info'] = $user_info;
		}

		$root['user_login_status'] = $user_login_status;
		$root['page_title']="修改密码";
		return output($root);
	}

	/**
	 * 执行密码修改操作
	 * @return array 
	 */
	public function dochangepassword()
	{
		$user_data = $GLOBALS['user_info'];

		$status = 0;
		$request = $GLOBALS['request'];

		if (!check_mobile($request['mobile'])) {
			$info = '手机号码格式错误';
			goto end;
		}

		$user_login_status = check_login();

		if ($user_login_status == LOGIN_STATUS_LOGINED) {
	
			$mobile = strim($request['user_mobile']);
			$sms_verify = intval($request['sms_verify']);
			$sql = 'SELECT add_time FROM '.DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
			$add_time = $GLOBALS['db']->getOne($sql);
			if (empty($add_time)) {
				$info = '验证码错误';
			} elseif ($add_time < NOW_TIME - 300) {
				$info = '验证码已过期';
			} else {
				$user_pwd = strim($request['user_pwd']);
				if (strlen($user_pwd) < 4 || strlen($user_pwd) > 30) {
					$info = '密码必须在4-30个字符之间';
				} else {
					$user_id = $user_data['id'];
					$update = array('user_pwd' => md5($user_pwd), 'update_time' => NOW_TIME);
					$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'id='.$user_id);
					$info = '密码修改成功';
					$status = 1;

					// 删除验证码
					$sql = "DELETE FROM ".DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
					$GLOBALS['db']->query($sql);
				}
			}
		}

		end:

		$root['user_login_status'] = $user_login_status;


		return output($root, $status, $info);
	}


	/**
	 * 昵称修改展示页方法
	 * @return array
	 */
	public function changeuname()
	{
	    $user_info = $GLOBALS['user_info'];
	    $user_login_status = check_login();
	
	    if ($user_login_status == LOGIN_STATUS_LOGINED) {
	        $root['user_info'] = $user_info;
	    }
	
	    $root['user_login_status'] = $user_login_status;
	    $root['page_title']="修改昵称";
	    return output($root);
	}
	
	/**
	 * 执行昵称修改操作
	 * @return array
	 */
	public function dochangeuname()
	{
	    $user_data = $GLOBALS['user_info'];
	
	    $status = 0;
	    $info = '';
	    $request = $GLOBALS['request'];
	
	    $user_login_status = check_login();
	
	    if ($user_login_status == LOGIN_STATUS_LOGINED) {
	    	if ($user_data['is_tmp'] == 0) {
	    		$info = '会员名已不允许修改';
	    	} else {
	    		$user_name = strim($request['user_name']);
		        $count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".$user_name."'");
	            if (mb_strlen($user_name, 'UTF-8') < 2) {
	                $info = '昵称过短';
	            } elseif (mb_strlen($user_name, 'UTF-8') > 16) {
	            	$info = '名称最长不得超过16个字';
	            } elseif ($count>0){
	                $info = '昵称重复，请重新输入';
	            } else {
	                $user_id = $user_data['id'];
	                $update = array('user_name' => strim($user_name),'is_tmp' => "0");
	                $GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'id='.$user_id);
	                $info = '昵称修改成功';
	                $status = 1;
	            }
	    	}	
	   	}
	   	$root['user_login_status'] = $user_login_status;
	    return output($root, $status, $info);
    }
}
?>