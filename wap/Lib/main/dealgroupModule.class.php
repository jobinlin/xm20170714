<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dealgroupModule extends MainBaseModule
{
	
	/**
	 * 商家点评页面
	 **/
	public function index()
	{	
		global_run();		
		init_app_page();
		$param=array();
		$param['data_id'] = intval($_REQUEST['data_id']);
		$data = call_api_core("dealgroup","index",$param);
		$GLOBALS['tmpl']->assign("data",$data);
		//echo "<pre>";
		//print_r($data);exit;
		$GLOBALS['tmpl']->display("dealgroup.html");	
		
	}
	
	

	
	
	
}
?>