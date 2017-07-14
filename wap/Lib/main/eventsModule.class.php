<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class eventsModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id'])); //当前城市的所有地区配置
		$event_cate = load_auto_cache("cache_event_cate");
		
		$param['cate_id'] = intval($_REQUEST['cate_id']); //活动分类ID
		$param['page'] = intval($_REQUEST['page']);
		$param['keyword'] = strim($_REQUEST['keyword']);
		$param['qid'] = intval($_REQUEST['qid']);
		$param['order_type'] = strim($_REQUEST['order_type']);
		

		$request = $param;
		$request['catename'] = $event_cate[$param['cate_id']]['name'];
		$request['quanname'] = $area_data[$param['qid']]['name'];	
		$data = call_api_core("events","wap_index",$param);
		foreach($data['navs'] as $k=>$v)
		{
			if($param['order_type']==$v['code'])
			{
				$request['ordername'] = $v['name'];
			}
		}
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("request",$request);
		
		//格式化bcate_list的url
		$bcate_list = $data['bcate_list'];
		foreach($bcate_list as $k=>$v)
		{		
			$tmp_url_param = $param;
			$tmp_url_param['cate_id']=$v['id'];			
			$bcate_list[$k]["url"] = wap_url("index","events",$tmp_url_param);			
		}
		$data['bcate_list'] = $bcate_list;
		//end bcate_list
		
		//格式化 quan_list
		$quan_list = $data['quan_list'];
		foreach($quan_list as $k=>$v)
		{		
			$tmp_url_param = $param;
			$tmp_url_param['qid']=$v['id'];					
			$quan_list[$k]["url"] = wap_url("index","events",$tmp_url_param);
				
			foreach($v['quan_sub'] as $kk=>$vv)
			{		
				$tmp_url_param = $param;
				$tmp_url_param['qid']=$vv['id'];
				$quan_list[$k]["quan_sub"][$kk]["url"] = wap_url("index","events",$tmp_url_param);
			}
			
		}
		$data['quan_list'] = $quan_list;
		//end quan_list
		
		$tuan_list = $data['item'];
		$out_tuan = array();
		foreach($tuan_list as $k=>$v)
		{
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1000)
				{
					$distance_str = round($distance/1000,2)."km";
				}
				else
				{
					$distance_str = round($distance)."m";
				}
			}
			$tuan_list[$k]['distance'] = $distance_str;
// 			if($v['is_over']==1||$v['out_time']==1){
// 			    $out_tuan[]=$v;
// 			    unset($tuan_list[$k]);
// 			}
			
		}
		
		$data['item'] = $tuan_list;
		//print_r($data['item']);exit;
		
		//重写navs 排序的url
		$navs = $data['navs'];
		
		foreach($navs as $k=>$v)
		{
			$tmp_url_param = $param;
			$tmp_url_param['order_type'] = $v['code'];			
			$navs[$k]['url'] = wap_url("index","events",$tmp_url_param);
		}
		$data['navs'] = $navs;
		//end navs
		if(isset($data['page']) && is_array($data['page'])){

			//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			//$page->parameter
			$p  =  $page->show();
			//print_r($p);exit;
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
		
//  		$new_url=json_encode($url);
//  		$GLOBALS['tmpl']->assign("b_url",$new_url);
		
		$GLOBALS['tmpl']->assign("data",$data);		
		$GLOBALS['tmpl']->display("events.html");
	}
	
	
}
?>