<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class smsApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 短信验证码发送接口
	 * 
	 * 	 输入:  
	 *  mobile:string 手机号
	 *  unique:int 是否需要检测被占用 0:不检测 1:要检测是否被抢占（用于注册，绑定时使用）2:要检测是否存在（取回密码）3 检测会员是否绑定手机
	 *  verify_code:string 图形验证码（可为空）
	 *  
	 *  输出:
	 *  status:int 发送结果状态 0失败 1成功 -1验证码错误(错误时返回verify_image,width,height)
	 *  info:信息返回
	 *  lesstime: int 剩余时间，秒
	 */
	public function send_sms_code()
	{
		$root = array();	
		$root['is_bind']=0;
		$root['bind_ts']='';
		if(app_conf("SMS_ON")==0)
		{
			return output($root,0,"短信功能未开启");
		}
		
		$mobile_phone = strim($GLOBALS['request']['mobile']);
		$unique = intval($GLOBALS['request']['unique']);
		$verify_code = strim($GLOBALS['request']['verify_code']);
		//提现功能要用到
		$account = intval($GLOBALS['request']['account']);
		$isbiz = false;
        $isdist=false;
		if (isset($GLOBALS['request']['biz'])) {
			$isbiz = true;
		}
        if (isset($GLOBALS['request']['dist'])) {
            $isdist = true;
        }
		if($account==1)
		{
			global_run();
			$mobile_phone = $GLOBALS['user_info']['mobile'];
			if($mobile_phone=="")
			{
				return output($root,0,"请先完善手机号");
			}
		} elseif($isbiz) { // 商户的手机验证码发送判断
			$mobile_phone = $GLOBALS['account']['mobile'];
			if (empty($mobile_phone)) {
				return output($root, 0, '请先完善商户手机号');
			}
		} elseif($isdist){
            $mobile_phone = $GLOBALS['dist_info']['tel'];
            if (empty($mobile_phone)) {
                return output($root, 0, '请先完善驿站手机号');
            }
        }
        else {
			if($unique==3)
			{
				if($GLOBALS['user_info']['mobile']=="") return output($root,0,"请先完善手机号");
			}
			if($mobile_phone=="")
			{
				return output($root,0,"请输入手机号");
			}
			if(!check_mobile($mobile_phone))
			{
				return output($root,0,"手机号格式不正确");
			}
			if($unique==4)
			{
				$user_mobile_phone=$GLOBALS['db']->getRow("select id,union_id from ".DB_PREFIX."user where mobile = '".$mobile_phone."'");
				if($user_mobile_phone){	
					if($GLOBALS['user_info']['union_id']){
						if($user_mobile_phone['union_id']){
							return output($root,0,"该手机号已绑定微信，请解绑后再来");
						}elseif($GLOBALS['user_info']['mobile']!=''){
							return output($root,0,"手机号已被占用");
						}elseif($user_mobile_phone['bind_count']>10){
							return output($root,0,"该账户合并会员次数到达上限");
						}
						$root['is_bind']=1;
						$root['bind_ts']="此手机号已绑定会员,绑定此手机号，会以此手机号对应的会员为主合并账户，只转移推荐人关系和余额与积分";
					}else{
						return output($root,0,"手机号已被占用");
					}
				}
			}
			if($unique==1)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile_phone."'")>0)
				{
					return output($root,0,"手机号已被占用");
				}
			}
			
			if($unique==2)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile_phone."'")==0)
				{
					return output($root,0,"手机号未注册");
				}
			}
            //验证驿站手机号是否为空
			if($unique==5){
                if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution where tel = '".$mobile_phone."'")==0)
                {
                    return output($root,0,"手机号未注册");
                }
            }
		}
		
		
		$sms_ipcount = load_sms_ipcount();
		if($sms_ipcount>1)
		{
			//需要图形验证码
			if(es_session::get("verify")!=md5($verify_code))
			{
				//$root = array("verify_image"=>SITE_DOMAIN.APP_ROOT."/verify.php?sess_id=".$GLOBALS['sess_id'],"width"=>50,"height"=>22);
				$root['verify_image']=SITE_DOMAIN.APP_ROOT."/verify.php?sess_id=".$GLOBALS['sess_id'];
				$root['width']=50;
				$root['height']=22;
				es_session::delete("verify");
				return output($root,-1,"图形验证码错误");
			}
			es_session::delete("verify");
		}
		
		if(!check_ipop_limit(get_client_ip(), "send_sms_code",SMS_TIMESPAN))
		{
			$root['lesstime']=load_sms_lesstime();
			return output($root,0,"请勿频繁发送短信");
		}		
		
		
		//删除失效验证码
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
		
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
		if($mobile_data)
		{
			//重新发送未失效的验证码
			$code = $mobile_data['code'];
			$mobile_data['add_time'] = NOW_TIME;
			$GLOBALS['db']->query("update ".DB_PREFIX."sms_mobile_verify set add_time = '".$mobile_data['add_time']."',send_count = send_count + 1 where mobile_phone = '".$mobile_phone."'");
		}
		else
		{
			$code = rand(100000,999999);
			$mobile_data['mobile_phone'] = $mobile_phone;
			$mobile_data['add_time'] = NOW_TIME;
			$mobile_data['code'] = $code;
			$mobile_data['ip'] = get_client_ip();
			$GLOBALS['db']->autoExecute(DB_PREFIX."sms_mobile_verify",$mobile_data,"INSERT","","SILENT");
				
		}

		send_verify_sms($mobile_phone,$code);
		$root['lesstime'] = SMS_TIMESPAN -(NOW_TIME - $mobile_data['add_time']);  //剩余时间
		return output($root,1,"发送成功");
	}
	
}
?>