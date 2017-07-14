<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: hhcycj
// +----------------------------------------------------------------------

class stores_explainModule extends MainBaseModule
{
	public function index()
	{
	    
	    $location_id = intval($_REQUEST['data_id']);
	    
	    //请求接口
	    $data = call_api_core("stores_explain","index",array("location_id"=>$location_id));
	    $data['page_title'] = "买单说明";
	    $GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("stores_explain.html");
	}
	
	
}
?>