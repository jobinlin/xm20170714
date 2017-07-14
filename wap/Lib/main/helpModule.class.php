<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class helpModule extends MainBaseModule
{
	/**
	 * 文章搜索
	 * 请求参数：keyword(string)：搜索关键字 不能为空
	 * 返回数据：Array
			(
			    [data] => Array
			        (
			            [11] => Array
			                (
			                    [id] => 11
			                    [title] => 公司信息
			                    [brief] => 
			                    [pid] => 0
			                    [is_effect] => 1
			                    [is_delete] => 0
			                    [type_id] => 1
			                    [sort] => 4
			                    [iconfont] => 
			                    [list] => Array
			                        (
			                            [0] => Array
			                                (
			                                    [id] => 20
			                                    [title] => 关于我们
			                                    [content] => 关于我们
			                                    [cate_id] => 11
			                                    [create_time] => 0
			                                    [update_time] => 1305160934
			                                    [add_admin_id] => 0
			                                    [is_effect] => 1
			                                    [rel_url] => 
			                                    [update_admin_id] => 0
			                                    [is_delete] => 0
			                                    [click_count] => 22
			                                    [sort] => 11
			                                    [seo_title] => 
			                                    [seo_keyword] => 
			                                    [seo_description] => 
			                                    [uname] => 
			                                    [notice_page] => 0
			                                    [sub_title] => 
			                                    [brief] => 
			                                )
			                            [1] => Array
			                                (
			                                )
			                        )
			                )
			            [10] => Array
			                (
			                )

	 */
	public function index()
	{
		global_run();		
		init_app_page();

		$data = call_api_core('help');
		// print_r($data);exit;
		$GLOBALS['tmpl']->assign('data', $data);

		$GLOBALS['tmpl']->display('help.html');
	}

	public function search()
	{
		global_run();		
		init_app_page();


		$GLOBALS['tmpl']->display('help_search.html');
	}

	/**
	 * 文章搜索
	 * 请求参数：keyword(string)：搜索关键字 不能为空
	 * 返回数据：Array
				(
				    [list] => Array
				        (
				            [0] => Array
				                (
				                    [id] => 20
				                    [title] => 关于我们
				                    [content] => 关于我们
				                    [cate_id] => 11
				                    [create_time] => 0
				                    [update_time] => 1305160934
				                    [add_admin_id] => 0
				                    [is_effect] => 1
				                    [rel_url] => 
				                    [update_admin_id] => 0
				                    [is_delete] => 0
				                    [click_count] => 22
				                    [sort] => 11
				                    [uname] => 
				                    [notice_page] => 0
				                    [sub_title] => 
				                    [brief] => 
				                )

				            [1] => Array
				                (
				                    
				                )
				        )

				    [ctl] => help
				    [act] => do_search
				    [status] => 1
				    [info] => 
				    [city_name] => 福州
				    [return] => 1
				    [sess_id] => isn3128ckjbql2289jr3564es3
				    [ref_uid] => 
				)
	 */	
	public function do_search()
	{
		global_run();

		$keyword = strim($_REQUEST['keyword']);

		$param = array(
			'keyword' => $keyword
		);

		$data = call_api_core('help', 'do_search', $param);
		ajax_return($data);
		$GLOBALS['tmpl']->assign('data', $data);
	}

	/**
	 * 获取文章详情
	 * 请求参数: id(int) 文章id
	 * 返回数据: Array
			(
			    [article] => Array
			        (
			            [id] => 20
			            [title] => 关于我们
			            [content] => 关于我们
			            [cate_id] => 11
			            [create_time] => 0
			            [update_time] => 1305160934
			            [add_admin_id] => 0
			            [is_effect] => 1
			            [rel_url] => 
			            [update_admin_id] => 0
			            [is_delete] => 0
			            [click_count] => 22
			            [sort] => 11
			            [uname] => 
			            [notice_page] => 0
			            [sub_title] => 
			            [brief] => 
			        )
			    [ctl] => help
			    [act] => detail
			    [status] => 1
			    [info] => 
			    [city_name] => 福州
			    [return] => 1
			    [sess_id] => isn3128ckjbql2289jr3564es3
			    [ref_uid] => 
			)
	 * @return  
	 */	
	public function detail()
	{
		global_run();		
		init_app_page();

		$id = intval($_REQUEST['id']);
		$param = array('id' => $id);

		$data = call_api_core('help', 'detail', $param);

		$GLOBALS['tmpl']->assign('data', $data);
		$GLOBALS['tmpl']->display('help_detail.html');
	}
}