<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cityModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('notice_index.html', $cache_id)){
				$param=array();				
				$param['type']=strim($_REQUEST['type']);
				$data = call_api_core("city","index",$param);
				foreach($data['city_list'] as $k=>$v){
					foreach($data['city_list'][$k] as $kk=>$vv){
						$data['city_list'][$k][$kk]['url']=wap_url("index","city#city_change",array("city_id"=>$vv['id'],"type"=>$data['type']));
					}
				}
				
				foreach($data['hot_city'] as $k=>$v)
				{
					$data['hot_city'][$k]['url'] = wap_url("index","city#city_change",array("city_id"=>$v['id'],"type"=>$data['type']));
				}
			
		}
		//print_r($data);
		$GLOBALS['tmpl']->assign("data",$data);		
		$GLOBALS['tmpl']->display("city.html",$cache_id);
	}

	
	
	public function city_change()
	{	
		global_run();
		$id=intval($_REQUEST['city_id']);
		$param=array();
		$param['id'] = intval($_REQUEST['city_id']);
		$param['type']=strim($_REQUEST['type']);
		$data = call_api_core("city","city_change",$param);
		if($data['type']=="main")
		$data['jump']=wap_url("index","main");
		else
		$data['jump']=wap_url("index","index");
		
		ajax_return($data);
	}
	
	//加载搜索城市列表
	public function searchcity(){
	    $param=array();
	    $param['kw'] = strim($_REQUEST['kw']);
	    $data = call_api_core("city","searchcity",$param);
	    
      
        foreach($data['city']['list'] as $k=>$v){
            
            $data['city']['list'][$k]['url'] = wap_url("index","city#city_change",array("city_id"=>$v['id']));
        }
        $GLOBALS['tmpl']->assign("data",$data);
        $data['city']['html'] = $GLOBALS['tmpl']->fetch("style5.2/inc/page/searchcity.html");
      

	    ajax_return($data);
	}
	
}
?>