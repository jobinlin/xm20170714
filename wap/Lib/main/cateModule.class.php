<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cateModule extends MainBaseModule
{
	
	/**
	 * 商家点评页面
	 **/
	public function index()
	{	
		global_run();	
		init_app_page();
		$param=array();
		$data = call_api_core("cate","index",$param);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("cate.html");	
		
	}
	
	

	
	
	
}
?>