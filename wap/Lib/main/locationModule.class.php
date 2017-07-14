<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class locationModule extends MainBaseModule
{
	
	/**
	 * 
	 **/
	public function index()
	{	
		global_run();		
		init_app_page();	
		$data_id = intval($_REQUEST['data_id']);
		$type=$_REQUEST['type'];
		$page=intval($_REQUEST['page']);
		
		if($data_id==0)
			$data_id = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal where uname = '".strim($_REQUEST['data_id'])."'"));
		$data = call_api_core("location","wap_index",array("data_id"=>$data_id,"type"=>$type,'page'=>$page));
		
		// 商家其它门店
		foreach ($data['supplier_location_list'] as $k=>$v){
			$data['supplier_location_list'][$k]['location_url'] =  wap_url("index", 'store', array('data_id'=>$v['id']) );
	
			// $data['supplier_location_list'][$k]['distance'] = format_distance_str($v['distance']);
		}
		
		if(isset($data['page']) && is_array($data['page'])){
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);
		    $p  =  $page->show();
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		
		$GLOBALS['tmpl']->assign("data",$data);
		
		$GLOBALS['tmpl']->display("location.html");	
		
	}
	
	

	
	
	
}
?>