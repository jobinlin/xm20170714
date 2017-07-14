<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_accountModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$data = call_api_core("uc_account","wap_index");
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		set_gopreview();
		if (!empty($data['user_info']['mobile'])) {
				$data['user_info']['mobile'] = substr_replace($data['user_info']['mobile'], '****', 4, 4);
			}

		foreach ($data['group_info'] as &$item) {
			$item['discount'] = intval($item['discount'] * 100) / 10;
		}unset($item);

		if ($data['ghighest'] == 0) {
			$nextGroups = '还差'.($data['group_info'][1]['score'] - $data['user_info']['total_score']).'积分升级至'.$data['group_info'][1]['name'].', 购物享'.$data['group_info'][1]['discount'].'折优惠';
			$data['group_percent'] = ($data['user_info']['total_score'] - $data['group_info'][0]['score']) / ($data['group_info'][1]['score'] - $data['group_info'][0]['score']) * 100;
		} else {
			$data['group_percent'] = 100;
			$nextGroups = '您已升至最高级';
		}
		$data['next_group_info'] = $nextGroups;

		if ($data['phighest'] == 0) {
			$nextLevels = '还差'.($data['level_info'][1]['point'] - $data['user_info']['point']).'经验值升级至'.$data['level_info'][1]['name'];
			$data['level_percent'] = ($data['user_info']['point']-$data['level_info'][0]['point'])/($data['level_info'][1]['point'] - $data['level_info'][0]['point']) * 100;	
		} else {
			$data['level_percent'] = 100;
			$nextLevels = '您已升至最高级';
		}
		$data['next_level_info'] = $nextLevels;
		$GLOBALS['tmpl']->assign('back_url', wap_url('index', 'user_center'));
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("user_info",$data['user_info']);
		$GLOBALS['tmpl']->display("uc_account.html");
	}
	
	
	public function save()
	{
		global_run();
		
		$param['user_name'] = strim($_REQUEST['user_name']);
		$param['user_email'] = strim($_REQUEST['user_email']);
		$param['user_pwd'] = strim($_REQUEST['user_pwd']);
		
		$data = call_api_core("uc_account","save",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['jump'] = wap_url("index","user#login");
			ajax_return($data);
		}
		
		if($data['status'])
		{
			$data['jump'] = wap_url("index","user_center#index");
			ajax_return($data);
		}
		else
		{
			ajax_return($data);
		}
	}
	
	/**
	 * 绑定手机和修改绑定手机
	 * @return  
	 */
	public function phone()
	{
		global_run();
		init_app_page();
		$param=array();
		$param['is_fx'] = intval($_REQUEST['is_fx']);
		$data = call_api_core("uc_account","phone", $param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}

		if (!empty($data['user_info']['mobile'])) {
			$data['user_info']['format_mobile'] = substr_replace($data['user_info']['mobile'], '****', 4, 4);
		}
		
		$GLOBALS['tmpl']->assign('referer_url', $_SERVER['HTTP_REFERER']);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("user_info",$data['user_info']);
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->display("uc_account_phone.html");
	}
	
	/**
	 * 手机绑定异步处理接口
	 * @return json 
	 */
	public function bindPhone()
	{
		global_run();
		
		$mobile = strim($_REQUEST['mobile']);
		$sms_verify = intval($_REQUEST['sms_verify']);
		$is_fx = intval($_REQUEST['is_fx']);

		$param = array(
			'mobile' => $mobile,
			'sms_verify' => $sms_verify,
			'is_fx' => $is_fx
		);
		if (!empty($_REQUEST['step'])) {
			$param['step'] = intval($_REQUEST['step']);
			if($param['step']==2){
				$param['is_luck'] = strim($_REQUEST['is_luck']);
			}
		}

		$data = call_api_core("uc_account","bindPhone", $param);
		/*if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['jump'] = wap_url("index","user#login");
			$data['info'] = '请登录';
		} else*/if ($data['status'] == 1) {
			if ($param['step'] == 2) {
				if($data['is_fx']==1){
					$url = $url ?: wap_url('index', 'uc_fx#vip_buy');
				}else{
					$url = es_session::get("wap_gopreview");
					$url = $url ?: wap_url('index', 'uc_account');
				}
				$data['jump'] = $url;
				//保存cookie
				es_cookie::set("fanwe_mobile", $mobile,3600*24*7);
				es_cookie::set("user_name",$data['user_name'],3600*24*30);
				es_cookie::set("user_pwd",md5($data['user_pwd']."_EASE_COOKIE"),3600*24*30);
			}
			
		}

		ajax_return($data);
	}

//	public function app_upload_avatar(){
//        global_run();
//        $param = array();
//
//        $param['base64_img'] = $_REQUEST['base64_img'];
//        $data = call_api_core("uc_account","app_upload_avatar", $param);
//        ajax_return($data);
//    }

}
?>