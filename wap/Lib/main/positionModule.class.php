<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 团购地图
 * @author jobin.lin
 *
 */
class positionModule extends MainBaseModule
{
	public function index()
	{		
		global_run();
		init_app_page();
		$param['location_id'] = intval($_REQUEST['location_id']);
		$location_info = $GLOBALS['db']->getRow("select id , city_id, xpoint , ypoint ,api_address ,address from ".DB_PREFIX."supplier_location where id=".$param['location_id']);
        $city_info =  $GLOBALS['db']->getRow("select id , name from ".DB_PREFIX."deal_city where id=".$location_info['city_id']);
        $location_info['city_name'] = $city_info['name'];
        $data['page_title'] = "商家位置";
        $GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("location_info",$location_info); 
		$GLOBALS['tmpl']->display("position.html");
		
	}
	public function do_posiotn(){
	    global_run();
	    app_redirect(url("index","tuan"));
	}
	
	public function clear(){
	    require_once APP_ROOT_PATH.'system/model/city.php';
	    City::clear_geo();
	    app_redirect_preview();
	}
	
}
?>