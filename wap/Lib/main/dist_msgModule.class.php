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
class dist_msgModule extends MainBaseModule
{

	/**
	 * 获取信息分类和最近的一条消息
	 *
	 * @return mixed 
	 */
	public function index()
	{
        dist_global_run();
        dist_init_app_page();	
		// 每个分类返回条数设置
		$page = intval($_REQUEST['page']);
		$param['page'] = $page;


		$data = call_api_core('dist_msg','index', $param);

		if($data['dist_user_status'] == 0){
			app_redirect(wap_url("dist","user#login"));
		}

		$this->_pageFormat($data['page']);

		$GLOBALS['tmpl']->assign('item', $data['item']);
		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->display('dist_msg_index.html');

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