<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_accountApiModule extends MainBaseApiModule
{
	
	/**
	 * 用户信息完善页面接口
	 * 
	 * 输入：无
	 * 
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
	 * user_info:array 会员信息
	 * Array(
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 * )
	 */
	public function index()
	{
		$root = array();	
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
		$root['user_login_status'] = $user_login_status;				
			
		$s_user_info = $GLOBALS['user_info'];
		$data['id'] = $s_user_info['id'];
		$data['user_name'] = $s_user_info['user_name'];
		$data['user_pwd'] = $s_user_info['user_pwd'];
		$data['email'] = $s_user_info['email'];
		$data['mobile'] = $s_user_info['mobile'];
		$data['is_tmp'] = $s_user_info['is_tmp'];
		$root['user_info'] = $data;
		$root['is_open_idvalidate'] = app_conf("IS_OPEN_IDVALIDATE");
		$root['page_title'] = "会员资料更新";
		
		return output($root);
	}

	/**
	 * A-10-1 帐户管理
	 * @return array 
	 */
	public function wap_index()
	{
		$root = array();	
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
						
		if ($user_login_status == LOGIN_STATUS_LOGINED) {

			$user_data['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"))?:'';
			
			$return_user_data = array(
				'id' => $user_data['id'],
				'user_name' => $user_data['user_name'],
				'total_score' => $user_data['total_score'],
				'point' => $user_data['point'],
				'mobile' => $user_data['mobile'],
				'email' => $user_data['email'],
				'password' => $user_data['password'],
				'user_avatar' => $user_data['user_avatar'],
				'is_tmp' => $user_data['is_tmp'],
				'is_id_validate' => $user_data['is_id_validate'],
				'union_id' => $user_data['union_id']
			);

			$root['user_info'] = $return_user_data;

			// 当前会员组和下一级会员组
			$hgsql = 'SELECT * FROM '.DB_PREFIX.'user_group WHERE score >= '.$user_data['total_score'].' ORDER BY score';
			$hginfo = $GLOBALS['db']->getAll($hgsql);
			$hgcount = count($hginfo);
			$ghigh = 0; // 是否已到最高级标识
			$lginfo = array();
			if (empty($hginfo) || ($hgcount == 1 && $hginfo[0]['score'] == $user_data['total_score'])) {
				// 已经是最高级
				$ghigh = 1;
				$llimit = $hgcount;
			} elseif ($hgcount >= 2 && $hginfo[0]['score'] == $user_data['total_score']) {
				$lginfo = array_slice($hginfo, 0, 2);
			} else {
				$llimit = 1;
			}
			if (empty($lginfo)) {
				$lgsql = 'SELECT * FROM '.DB_PREFIX.'user_group WHERE score < '.$user_data['total_score'].' ORDER BY score DESC LIMIT '.(2 - $llimit);
				$lginfo = $GLOBALS['db']->getAll($lgsql);
				if (!empty($hginfo) && !empty($lginfo)) {
					array_push($lginfo, $hginfo[0]);
				} elseif (empty($lginfo)) {
					$lginfo = array_slice($hginfo, 0, 2);
					$lginfo = array_reverse($lginfo);
				} else {
					$lginfo = array_reverse($lginfo);
				}
			}
			
			$root['group_info'] = $lginfo;
			$root['ghighest'] = $ghigh;
			$root['currdis'] = $ghigh ? $lginfo[1] : $lginfo[0];
			$root['currdis']['discount'] = intval($root['currdis']['discount'] * 100) / 10;

			// 当前等级和下一等级
			$hpsql = 'SELECT * FROM '.DB_PREFIX.'user_level WHERE point >= '.$user_data['point'].' ORDER BY point';
			$hpinfo = $GLOBALS['db']->getAll($hpsql);
			$hgcount = count($hpinfo);
			$phigh = 0; // 是否已到最高级标识
			$lpinfo = array();
			if (empty($hpinfo) || ($hgcount == 1 && $hpinfo[0]['point'] == $user_data['point'])) {
				// 已经是最高级
				$phigh = 1;
				$llimit = $hgcount;
			} elseif ($hgcount >= 2 && $hpinfo[0]['point'] == $user_data['point']) {
				$lpinfo = array_slice($hpinfo, 0, 2);
			} else {
				$llimit = 1;
			}
			if (empty($lpinfo)) {
				$lpsql = 'SELECT * FROM '.DB_PREFIX.'user_level WHERE point < '.$user_data['point'].' ORDER BY point DESC LIMIT '.(2 - $llimit);
				$lpinfo = $GLOBALS['db']->getAll($lpsql);
				if (!empty($hpinfo) && !empty($lpinfo)) {
					array_push($lpinfo, $hpinfo[0]);
				} elseif (empty($lpinfo)) {
					$lpinfo = array_slice($hpinfo, 0, 2);
					$lpinfo = array_reverse($lpinfo);
				} else {
					$lpinfo = array_reverse($lpinfo);
				}
			}
			$root['phighest'] = $phigh;
			$root['level_info'] = $lpinfo;


			if(es_session::get('user_wx_info')){
				if(es_session::get("is_weixin_bind")){
					$root['is_weixin_bind'] = 0;
				}else{
					es_session::set("is_weixin_bind",1);
					$root['is_weixin_bind'] = 1;
				}
			}else{
				$root['is_weixin_bind'] = 0;
			}
			$root['is_open_idvalidate'] = app_conf("IS_OPEN_IDVALIDATE");
		}

		$root['user_login_status'] = $user_login_status;
		
		$root['page_title'] = "帐户管理";
		
		return output($root);
	}
	
	
	/**
	 * 临时会员更新会员资料接口
	 * 
	 * 输入:
	 * user_name:string 用户名
	 * user_email:string 邮箱
	 * user_pwd:string 密码
	 * 
	 * 输出:
	 * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
	 * status:int 结果状态 0失败 1成功
	 * info:信息返回
	 * 
	 * 	 以下六项仅在status为1时会返回
	 *  id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 */
	public function save()
	{
		$root = array();
		$user_login_status = check_login();
		$root['user_login_status'] = $user_login_status;
		
		$user_name = strim($GLOBALS['request']['user_name']);
		$email = strim($GLOBALS['request']['user_email']);
		$user_pwd = strim($GLOBALS['request']['user_pwd']);
		
		if($GLOBALS['user_info']['is_tmp']==1)
		{			
			if($user_name=="")
			{
				return output($root,0,"请输入您的用户名");
			}
			if($email=="")
			{
				return output($root,0,"请输入您的真实邮箱");
			}
			if($user_pwd=="")
			{
				return output($root,0,"请设置您的登录密码");
			}
			
			//
			$user_data['user_name'] = $user_name;
			$user_data['email'] = $email;
			$user_data['user_pwd'] = $user_pwd;
			$user_data['id'] = $GLOBALS['user_info']['id'];
			$res = save_user($user_data,'UPDATE');
			if($res['status'] == 1)
			{			
				do_login_user($user_data['user_name'],$user_data['user_pwd']);
				
				$s_user_info = es_session::get("user_info");
				$root['id'] = $s_user_info['id'];
				$root['user_name'] = $s_user_info['user_name'];
				$root['user_pwd'] = $s_user_info['user_pwd'];
				$root['email'] = $s_user_info['email'];
				$root['mobile'] = $s_user_info['mobile'];
				$root['is_tmp'] = $s_user_info['is_tmp'];
				return output($root,1,"资料更新成功");
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
					$error_msg = $error_field."格式错误";
				}
				if($error['error']==EXIST_ERROR)
				{
					$error_msg = $error_field."已经存在";
				}
				
				return output($root,0,$error_msg);
			}			
			//
		}
		else
		{
			return output($root,0,"资料已经更新");
		}
		
		
		
	}
	
	/**
	 * 上传用户头像
	 * 
	 * 输入：
	 * $_FILES['file']：头像文件
	 * 
	 * 输出：
	 * status: int 0失败 1成功
	 * info:string 信息提示
	 * small_url: string 头像小图
	 * middle_url:string 头像中图
	 * big_url:string 头像大图
	 */
	public function upload_avatar()
	{
		$root = array();

		if($GLOBALS['user_info'])
		{
			if($_FILES['file'])
			{
				$res = upload_avatar($_FILES, $GLOBALS['user_info']['id']);
				if($res['error']==0)
				{
					$root['small_url'] = $res['small_url'];
					$root['middle_url'] = $res['middle_url'];
					$root['big_url'] = $res['big_url'];
					
					return output($root);
				}
				else
				{
					return output($root,0,$res['message']);
				}
			}
			else
			{
				return output($root,0,"请上传文件");
			}
		}
		else
		{
			return output($root,0,"请先登录");
		}		
		
	}

	public function phone()
	{
		$page_title = '绑定手机号';
		$root = array();
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			if (!empty($user_data['mobile'])) {
				$page_title = '验证绑定手机号';
				$data['mobile'] = $user_data['mobile'];
				$root['step'] = 1;
			} else {
				$root['step'] = 2;
			}
			$root['is_fx'] = strim($GLOBALS['request']['is_fx']);
			$root['user_info'] = $data;
		}
		$root['user_login_status'] = $user_login_status;
		$root['page_title'] = $page_title;
		return output($root);
	}
	
	public function bindPhone()
	{
		$root = array();	
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();

		$status = 0;
		$request = $GLOBALS['request'];
		$data=array();
		if (!check_mobile($request['mobile'])) {
			$info = '手机号码格式错误';
			return output($data,0,$info);
		}
		$root['mobile'] = $user_data['mobile'];
		
		if ($user_login_status == LOGIN_STATUS_LOGINED) {

			$user_id = $user_data['id'];
			$user_mobile = $request['mobile'];
			$sms_verify = $request['sms_verify'];
			$is_luck = strim($GLOBALS['request']['is_luck']);
			$is_fx = intval($GLOBALS['request']['is_fx']);
			if($GLOBALS['user_info']['mobile']){
				if ($request['step'] == 1) {
					if($user_mobile!=$GLOBALS['user_info']['mobile']){
						return output($data,0,"不是本人");
					}
				}else{
					if(es_session::get("is_luck")!=md5($is_luck)){
						return output($data,0,"验证失效");
					}
				}
			}
			//删除验证码
			$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
			$GLOBALS['db']->query($sql);
			$sql = 'SELECT id,add_time FROM '.DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$user_mobile.' AND code='.$sms_verify;
			$add_time = $GLOBALS['db']->getRow($sql);
			// 验证码三分钟有效
			
			if (empty($add_time)) {
				$info = '验证码错误';
				return output($data,0,$info);
			} elseif ($add_time['add_time'] < NOW_TIME - 300) {
				$info = '验证码已过期';
				return output($data,0,$info);
			} else {
				if ($request['step'] == 2) {
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
					if($user_info){
						if($user_info['union_id']){
							return output($data,0,"手机号已绑定微信，请解绑后再来");
						}elseif(!$GLOBALS['user_info']['union_id']){
							return output($data,0,"微信账号才能合并已注册手机");
						}elseif($user_info['bind_count']>=10){
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
								return output($data,0,"手机号已被占用");
							}
						}else{
							$data['jump'] = wap_url("index","user#login");
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
							$data['step'] = 2;
							refresh_user_info();
							es_session::set("send_sms_code_0_ip", null);
							$data['is_fx'] = $is_fx;
							return output($data,1,"绑定成功");
								
						}
					}else{
						if($GLOBALS['user_info'])
						{
							$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
							$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$user_mobile."' where id = ".$GLOBALS['user_info']['id']);

							$result = do_login_user($user_mobile,$GLOBALS['user_info']['user_pwd'],$is_wap);
							
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
								es_session::set("send_sms_code_0_ip", null);
								$data['step'] = 2;
								$data['is_fx'] = $is_fx;
								return output($data,1,"绑定成功");
							
							}
						}
						else
						{					
							$data['jump'] = wap_url("index","user#login");
							return output($data,0,"您还未登录");
						}
					}
					// 绑定手机  未绑定和修改绑定
					//$update = array('mobile' => $mobile, 'update_time' => NOW_TIME);
					//$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'id='.$user_id);
					//$info = '绑定成功';
					//$root['step'] = 2;
				} else {
					// 修改绑定手机
					es_session::set("is_luck", md5($add_time['id']));
					$page_title = '绑定新的手机';
					$root['step'] = 2;
					$root['is_luck'] = $add_time['id'];
					$root['page_title'] = $page_title;
					es_session::set("send_sms_code_0_ip", null);
					$status = 1;
					$root['user_login_status'] = $user_login_status;
					return output($root, $status, $info);
				}
			}	
		}
	}
	/**
	 * 解绑微信api
	 */
	public function wx_unbind () {
	    $root = array();
	    
	    $root['user_login_status'] = check_login();
	    if (!$root['user_login_status']) {
	        return output($root,0,"请先登录");
	    }
	    
        // 解绑微信
        $GLOBALS['db']->query('UPDATE ' . DB_PREFIX . 'user SET wx_openid = "",m_openid="",union_id="" WHERE id = ' . $GLOBALS['user_info']['id']);
        // 更新用户
        load_user($GLOBALS['user_info']['id'],true);
        
        return output($root,1,'解绑成功！');
	}

//    /**
//     * 头像上传
//     */
//	public function app_upload_avatar(){
//
//        $root = array();
//        $root['user_login_status'] = check_login();
//        if (!$root['user_login_status']) {
//            return output($root,0,"请先登录");
//        }
//        $base64_img = $GLOBALS['request']['base64_img'];
//
//        $user = $GLOBALS['user_info'];
//
//
//        preg_match("/data:image\/(jpg|jpeg|png|gif);base64,/i",$base64_img,$res);
//        $img_ext = $res[1];
//
//        if(!in_array($img_ext,array("jpg","jpeg","png","gif"))){
//            $result['status'] = 0;
//            $result['info'] = '上传文件格式有误';
//            ajax_return($result);
//        }
//
//        $img_data = preg_replace("/data:image\/(jpg|jpeg|png|gif);base64,/i","",$base64_img);
//
//
//        //开始移动图片到相应位置
//        $id = intval($user['id']);
//        $uid = sprintf("%09d", $id);
//        $dir1 = substr($uid, 0, 3);
//        $dir2 = substr($uid, 3, 2);
//        $dir3 = substr($uid, 5, 2);
//        $path = $dir1.'/'.$dir2.'/'.$dir3;
//
//        //创建相应的目录
//        $avatar_dir = APP_ROOT_PATH."public/avatar/";
//        if (!is_dir($path)) {
//            $mkdir = mkdir($avatar_dir.$path, 0777, true);
//            if (!$mkdir) {
//                logger::write('创建头像目录失败,权限不足');
//            }
//        }
//
//        $id = str_pad($id, 2, "0", STR_PAD_LEFT);
//        $id = substr($id,-2);
//        $avatar_file = $avatar_dir.$path."/".$id.".jpg";
//
//        if (file_put_contents($avatar_file, base64_decode($img_data))===false) {
//            $result['status'] = 0;
//            $result['info'] = '上传文件失败';
//            ajax_return($result);
//        }
//
//        $avatar_file_big = $avatar_dir.$path."/".$id."virtual_avatar_big.jpg";
//        $avatar_file_middle = $avatar_dir.$path."/".$id."virtual_avatar_middle.jpg";
//        $avatar_file_small = $avatar_dir.$path."/".$id."virtual_avatar_small.jpg";
//        get_spec_image($avatar_file,48,48);
//
//        $im = get_spec_gif_anmation($avatar_file,48,48);
//
//        file_put_contents($avatar_file_small,$im);
//
//        $im = get_spec_gif_anmation($avatar_file,120,120);
//        file_put_contents($avatar_file_middle,$im);
//
//        $im = get_spec_gif_anmation($avatar_file,200,200);
//        file_put_contents($avatar_file_big,$im);
//
//        $root['user_avatar'] = $avatar_file_big;
//        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
//        {
//            syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_big.jpg");
//            syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_middle.jpg");
//            syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_small.jpg");
//        }
//
//        //上传成功更新用户头像的动态缓存
//        return output($root,1,'头像上传成功');
//    }

}
?>