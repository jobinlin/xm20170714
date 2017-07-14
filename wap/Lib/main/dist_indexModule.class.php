<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dist_indexModule extends MainBaseModule
{
	public function index()
	{
		dist_global_run();		
		dist_init_app_page();
		app_redirect(wap_url("dist","user#login"));
	}
}
?>