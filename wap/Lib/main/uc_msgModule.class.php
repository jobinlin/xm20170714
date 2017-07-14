<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


/**
* 用户消息中心
*/
class uc_msgModule extends MainBaseModule
{

	/**
	 * 获取信息分类和最近的一条消息
	 * @return json 
	 */
	public function index()
	{
		global_run();
		init_app_page();
		$data = call_api_core('uc_msg', 'index');
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
        
		if($data['data']['delivery']){
		    $data['data']['delivery']['url']=wap_url("index","uc_msg#cate",array("type"=>"delivery"));
		}
		if($data['data']['notify']){
		    $data['data']['notify']['url']=wap_url("index","uc_msg#cate",array("type"=>"notify"));
		}
		if($data['data']['account']){
		    $data['data']['account']['url']=wap_url("index","uc_msg#cate",array("type"=>"account"));
		}
		if($data['data']['confirm']){
		    $data['data']['confirm']['url']=wap_url("index","uc_msg#cate",array("type"=>"confirm"));
		}
		
		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->assign('msg', $data['data']);

		$GLOBALS['tmpl']->display('uc_msg_index.html');

	}


	/**
	 * 分类信息列表
	 * @return [type] [description]
	 */
	public function cate()
	{
		global_run();		
		init_app_page();
		// 每个分类返回条数设置
		$page = intval($_REQUEST['page']);
		$param['page'] = $page;
		$param['msgType'] = strim($_REQUEST['type']);

		$data = call_api_core('uc_msg','cate', $param);

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}

		$this->_pageFormat($data['page']);
        
		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->display('uc_msg_cate.html');
	}


	/**
	 * 用户手动删除消息 
	 * @return json 
	 */
	public function delete()
	{
		$param['id'] = intval($_REQUEST['data_id']);
		$data = call_api_core('uc_msg', 'delete', $param);
		if ($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			login_is_app_jump();
			//app_redirect(wap_url('index', 'user#login'));
		}
		ajax_return($data);
	}

}