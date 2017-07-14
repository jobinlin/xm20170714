<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class scores_dealModule extends MainBaseModule
{
	
	/**
	 * 积分商品详情页
	 **/
	public function index()
	{	
		global_run();		

		$GLOBALS['tmpl']->display("scores_deal.html");	
		
	}
	
	

	
	
	
}
?>