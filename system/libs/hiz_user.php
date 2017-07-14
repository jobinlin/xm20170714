<?php 

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("EMPTY_ERROR",1);  //未填写的错误
define("FORMAT_ERROR",2); //格式错误
define("EXIST_ERROR",3); //已存在的错误

define("ACCOUNT_NO_EXIST_ERROR",1); //帐户不存在
define("ACCOUNT_PASSWORD_ERROR",2); //帐户密码错误
define("ACCOUNT_NO_VERIFY_ERROR",3); //帐户未激活



function do_login_hiz($account_user,$account_password){
	$hiz_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."agency WHERE account_name='".$account_user."' AND is_delete = 0");
	
	$result = array();
	$result['status'] =1;
	$result['data'] = '';
	if(!$hiz_data)
	{
		$result['status'] = 0;
		$result['data'] = ACCOUNT_NO_EXIST_ERROR;
		return $result;
	}else{
		
		$result['account_info'] = $hiz_data;
		if(strlen($account_password)==32)
		{			
			if($account_password!=$hiz_data['account_password'])
			{
				$result['data'] = ACCOUNT_PASSWORD_ERROR;
				return $result;
			}
		}
		elseif($hiz_data['account_password'] != md5($account_password)){
			$result['status'] = 0;
			$result['data'] = ACCOUNT_PASSWORD_ERROR;
			return $result;
		}
		elseif($hiz_data['is_effect'] != 1)
		{
			$result['status'] = 0;
			$result['data'] = ACCOUNT_NO_VERIFY_ERROR;
			return $result;
		}
		
	
		es_session::set("hiz_account_info",$hiz_data);
		$GLOBALS['hiz_account_info'] = $hiz_data;
		

		$GLOBALS['db']->query("update ".DB_PREFIX."agency set login_ip = '".CLIENT_IP."',login_time= ".NOW_TIME." ,login_count=login_count+1 where id =".$hiz_data['id']);
		return $result;
	}
	
}

/**
 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
 */
function loginout_hiz()
{
	$account_info = es_session::get("hiz_account_info");
	if(!$account_info)
	{
		return false;
	}
	else
	{
		es_session::delete("hiz_account_info");

	}
}

?>