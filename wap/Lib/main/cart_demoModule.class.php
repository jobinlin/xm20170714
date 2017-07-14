<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cart_demoModule extends MainBaseModule
{
	
	/**
	 * 提交订单
	 **/
	public function cart_check()
	{	
		global_run();		

		$GLOBALS['tmpl']->display("style5.2/inc/case/demo_cart_check.html");	
		
	}
	
		
	/**
	 * 收银台
	 **/
	public function pay()
	{	
		global_run();		

		$GLOBALS['tmpl']->display("style5.2/inc/case/demo_pay.html");	
		
	}

			
	/**
	 * 成功页面
	 **/
	public function done()
	{	
		global_run();		

		$GLOBALS['tmpl']->display("style5.2/inc/case/demo_done.html");	
		
	}

	
	
}
?>