<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class HizBaseModule
{
	public function __construct()
	{	
		$GLOBALS['tmpl']->assign("MODULE_NAME",MODULE_NAME);
		$GLOBALS['tmpl']->assign("ACTION_NAME",ACTION_NAME);
		set_hiz_gopreview();
	}

	public function index()
	{
		showErr("invalid access");
	}
	
	public function __destruct()
	{
		
	}

}
?>