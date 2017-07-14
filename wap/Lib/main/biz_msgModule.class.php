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
class biz_msgModule extends MainBaseModule
{

	/**
	 * 获取信息分类和最近的一条消息
	 *
	 * @return mixed 
	 */
	public function index()
	{
		global_run();
		init_app_page();
		$data = call_api_core('biz_msg', 'index');
		if($data['biz_user_status'] == 0){
			app_redirect(wap_url("biz","user#login"));
		}
        
		if($data['item']['order']){
		    $data['item']['order']['url']=wap_url("biz","msg#cate",array("cate"=>"order"));
		}
		
		if($data['item']['account']){
		    $data['item']['account']['url']=wap_url("biz","msg#cate",array("cate"=>"account"));
		}
		// print_r($data);
		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->assign('msg', $data['item']);

		$GLOBALS['tmpl']->display('biz_msg_index.html');

	}


	/**
	 * 分类信息列表
	 * 
	 * @return [type] [description]
	 */
	public function cate()
	{
		global_run();		
		init_app_page();
		// 每个分类返回条数设置
		$page = intval($_REQUEST['page']);
		$param['page'] = $page;
		$param['cate'] = strim($_REQUEST['cate']);

		$data = call_api_core('biz_msg','cate', $param);

		if($data['biz_user_status'] == 0){
			app_redirect(wap_url("biz","user#login"));
		}

		$this->_pageFormat($data['page']);

		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->display('biz_msg_cate.html');
	}


	/**
	 * 用户手动删除消息  (暂无需求)
	 * @return json 
	 */
	public function delete()
	{
		$param['id'] = intval($_REQUEST['data_id']);
		$data = call_api_core('biz_msg', 'delete', $param);
		if ($data['biz_user_status'] == 0) {
			app_redirect(wap_url('biz', 'user#login'));
		}
		ajax_return($data);
	}
}