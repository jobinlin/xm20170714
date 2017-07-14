<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/page.php';
class demoModule extends BizBaseModule
{
	public function index()
	{
		global_run();

// 		showBizErr("111");
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		if (!$GLOBALS['tmpl']->is_cached('index.html', $cache_id))
		{
			init_app_page();

		}
		$GLOBALS['tmpl']->display("demo.html",$cache_id);
	}
	
	
}
?>