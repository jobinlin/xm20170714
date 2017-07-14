<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: hhcycj
// +----------------------------------------------------------------------

class stores_payModule extends MainBaseModule
{
	public function old_index()
	{
		global_run();		
		init_app_page();
		
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id'])); //当前城市的所有地区配置
		$deal_cate = load_auto_cache("cache_deal_cate");
		$deal_cate_type = load_auto_cache("cache_deal_cate_type");
		
		$param['cate_id'] = intval($_REQUEST['cate_id']);
		$param['tid'] = intval($_REQUEST['tid']);
		$param['page'] = intval($_REQUEST['page']);
		$param['keyword'] = strim($_REQUEST['keyword']);
		$param['qid'] = intval($_REQUEST['qid']);
		$param['order_type'] = strim($_REQUEST['order_type']);
		

		$request = $param;
		$request['catename'] = $deal_cate[$param['cate_id']]['name'];
		$request['quanname'] = $area_data[$param['qid']]['name'];	
		$data = call_api_core("stores_pay","index",$param);
		 
		foreach($data['navs'] as $k=>$v)
		{
			if($param['order_type']==$v['code'])
			{
				$request['ordername'] = $v['name'];
			}
		}
		
		$GLOBALS['tmpl']->assign("request",$request);
		
		//格式化bcate_list的url
		$bcate_list = $data['bcate_list'];
		foreach($bcate_list as $k=>$v)
		{		
			$tmp_url_param = $param;
			$tmp_url_param['cate_id']=$v['id'];			
			
			$bcate_list[$k]["url"] = wap_url("index","stores_pay",$tmp_url_param);
			
			foreach($v['bcate_type'] as $kk=>$vv)
			{				
				$tmp_url_param = $param;
				$tmp_url_param['cate_id']=$v['id'];
				$tmp_url_param['tid']=$vv["id"];

				$bcate_list[$k]["bcate_type"][$kk]["url"]= wap_url("index","stores_pay",$tmp_url_param);
			}
		}
		$data['bcate_list'] = $bcate_list;
		//end bcate_list
		
		//格式化 quan_list
		$quan_list = $data['quan_list'];
		foreach($quan_list as $k=>$v)
		{		
			$tmp_url_param = $param;
			$tmp_url_param['qid']=$v['id'];					
			$quan_list[$k]["url"] = wap_url("index","stores_pay",$tmp_url_param);
				
			foreach($v['quan_sub'] as $kk=>$vv)
			{		
				$tmp_url_param = $param;
				$tmp_url_param['qid']=$vv['id'];
				$quan_list[$k]["quan_sub"][$kk]["url"] = wap_url("index","stores_pay",$tmp_url_param);
			}		
		}
		$data['quan_list'] = $quan_list;
		//end quan_list
		$tuan_list = $data['item'];
		//var_dump($tuan_list);exit;
		$promote_desc_array = array();
		foreach($tuan_list as $k=>$v)
		{
		    
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1500)
				{
					$distance_str = round($distance/1000)."km";
				}
				else
				{
					$distance_str = round($distance)."m";
				}
			}
			$tuan_list[$k]['distance'] = $distance_str;
			
			
			// 去除重复的promote简介
			$promote_desc_array = explode(',', $v['promote_description']);
			//$promote_desc_array = array_unique($promote_desc_array);
			$promote_desc = join(',', $promote_desc_array);
			$tuan_list[$k]['promote_description'] = $promote_desc;
			
			$tuan_list[$k]['avg_point'] = round($v['avg_point'], 1);
			
		}
		$data['item'] = $tuan_list;
		 
		//重写navs 排序的url
		$navs = $data['navs'];
		foreach($navs as $k=>$v)
		{
			$tmp_url_param = $param;
			$tmp_url_param['order_type'] = $v['code'];			
			$navs[$k]['url'] = wap_url("index","stores_pay",$tmp_url_param);
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

		$GLOBALS['tmpl']->assign("data",$data);		
		$GLOBALS['tmpl']->display("stores_pay.html");
	}
	
	
	public function index()
	{
		global_run();		
		init_app_page();
		
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id'])); //当前城市的所有地区配置
		$deal_cate = load_auto_cache("cache_deal_cate");
		$deal_cate_type = load_auto_cache("cache_deal_cate_type");
		
		$param['cate_id'] = intval($_REQUEST['cate_id']);
		$param['tid'] = intval($_REQUEST['tid']);
		$param['page'] = intval($_REQUEST['page']);
		$param['keyword'] = strim($_REQUEST['keyword']);
		$param['qid'] = intval($_REQUEST['qid']);
		$param['order_type'] = strim($_REQUEST['order_type']) ?: 'distance';
		$param['payment'] = 1; // 只获取支持到店支付的参数
		

		$request = $param;
		$request['catename'] = $deal_cate[$param['cate_id']]['name'];
		$request['quanname'] = $area_data[$param['qid']]['name'];	
		$data = call_api_core("stores","wap_index",$param);

		foreach($data['navs'] as $k=>$v)
		{
			if($param['order_type']==$v['code'])
			{
				$request['ordername'] = $v['name'];
			}
		}
		

		$GLOBALS['tmpl']->assign("request",$request);

		// id => 分类 的数组
		$cate_names = array();

		//格式化bcate_list的url
		$bcate_list = $data['bcate_list'];
		foreach($bcate_list as $k=>$v)
		{
			if ($v['id'] == $param['cate_id']) {
				$GLOBALS['tmpl']->assign('default_cate_id', $k);
			}

			$tmp_url_param = $param;
			$tmp_url_param['cate_id']=$v['id'];			
			
			$bcate_list[$k]["url"] = wap_url("index","stores",$tmp_url_param);
			
			foreach($v['bcate_type'] as $kk=>$vv)
			{				
				$tmp_url_param = $param;
				$tmp_url_param['cate_id']=$v['id'];
				$tmp_url_param['tid']=$vv["id"];

				$bcate_list[$k]["bcate_type"][$kk]["url"]= wap_url("index","stores",$tmp_url_param);

				if ($kk == 0) {
					$cate_count = count(array_unique($data['cate_count'][$v['id']]));

				} else {
					$cate_count = count($data['cate_item_count'][$vv['cate_id']][$vv['id']]);
				}
				
				$bcate_list[$k]['bcate_type'][$kk]['count'] = $cate_count;

			}

			if ($v['id'] > 0) {
				$cate_names[$v['id']] = $v['name'];
			}
		}
		$bcate_list[0]['bcate_type'][0]['count'] = $data['cate_total'];
		$data['bcate_list'] = $bcate_list;

		//格式化 quan_list
		$quan_list = array();
		$quan_list_detail = array(); // 商圈ID名称表
		foreach($data['quan_list'] as $k=>$v)
		{
			if ($v['id'] == $param['qid']) {
				$GLOBALS['tmpl']->assign('default_qid', $k);
			}

			$tmp_url_param = $param;
			$tmp_url_param['qid']=$v['id'];					
			$v["url"] = wap_url("index","stores",$tmp_url_param);
				
			foreach($v['quan_sub'] as $kk=>$vv)
			{
				if ($vv['id'] == $param['qid']) {
					$GLOBALS['tmpl']->assign('default_qid', $k);
				}

				$tmp_url_param = $param;
				$tmp_url_param['qid']=$vv['id'];
				$v["quan_sub"][$kk]["url"] = wap_url("index","stores",$tmp_url_param);
				
				if ($vv['pid'] == 0) {
					$sub_count = count(array_unique($data['location_count'][$vv['id']]));
				} else {
					$sub_count = count($data['location_item_count'][$vv['id']]);
				}
				$v['quan_sub'][$kk]['count'] = $sub_count;

			}
			$quan_list[$v['id']] = $v;	
		}
		$quan_list[0]['quan_sub'][0]['count'] = $data['location_total'];
		$data['quan_list'] = $quan_list;
		//end quan_list

		$tuan_list = $data['item'];
		foreach($tuan_list as $k=>$v) {
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0) {
				if($distance>1000) {
					$distance_str .= round($distance/1000, 2)."km";
				} else {
					$distance_str .= round($distance)."m";
				}
			}
			$tuan_list[$k]['distance'] = $distance_str;

			// 门店分类
			$tuan_list[$k]['store_type'] = $cate_names[$v['deal_cate_id']];
			// 门店优惠买单信息
			if ($v['open_store_payment'] == 1/* && !empty($data['promote'][$v['supplier_id']])*/) {
				if (!empty($data['promote'][$v['supplier_id']])) {
					$tuan_list[$k]['promote_info'] = implode(' ', $data['promote'][$v['supplier_id']]);
				}
				$pro_param['id'] = $v['id'];
				if (APP_INDEX == 'app') {
					$promote_url = 'javascript:App.app_detail(431, {\'location_id\': '.$pro_param['id'].'})';
				} else {
					$promote_url = wap_url('index', 'store_pay', $pro_param);
				}
				$tuan_list[$k]['promote_url'] = $promote_url;
			}

			if (APP_INDEX == 'app') {
				$store_url = 'javascript:App.app_detail(430, {\'data_id\': '.$v['id'].'})';
			} else {
				$store_url = wap_url('index', 'store', array('data_id' => $v['id']));
			}
			$tuan_list[$k]['store_url'] = $store_url;

			
			$tuan_list[$k]['quan_name'] = $data['location_id_name'][$v['id']];

			// 判断店铺是否有评分
			if (!empty($v['avg_point'])) {
				$tuan_list[$k]['format_point'] = ($v['avg_point'] / 5) * 100;
				$tuan_list[$k]['avg_point'] = sprintf('%.1f', round($v['avg_point'], 1));
			} 

		}
		$data['item'] = $tuan_list;

		//重写navs 排序的url
		$navs = array();
		foreach($data['navs'] as $k=>$v)
		{
			$tmp_url_param = $param;
			$tmp_url_param['order_type'] = $v['code'];			
			$v['url'] = wap_url("index","stores",$tmp_url_param);
			$navs[$v['code']] = $v;
		}
		$data['navs'] = $navs;
		//end navs

		$address = $GLOBALS['geo']['address'];
		$GLOBALS['tmpl']->assign("address",$address);

		if(isset($data['page']) && is_array($data['page'])){
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
		$back_url = wap_url("index","index");
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		$GLOBALS['tmpl']->assign("data",$data);		
		$GLOBALS['tmpl']->display("stores.html");
	}
}
?>