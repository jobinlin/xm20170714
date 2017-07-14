<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class shopModule extends MainBaseModule
{
	
	/**
	 * 商城首页
	 **/
	public function index()
	{	
		global_run();	
		init_app_page();
		$data = call_api_core("shop","index");
		//移到mapi
		/*foreach($data['advs'] as $k=>$v)
		{
		
			$data['advs'][$k]['url'] =  getWebAdsUrl($v);
		}

		foreach($data['advs2'] as $k=>$v)
		{
		
			$data['advs2'][$k]['url'] =  getWebAdsUrl($v);
		}*/
		
		foreach ($data['supplier_deal_list'] as $k=>$v){
			$data['supplier_deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
			//移到mapi
			//$deal_param['data_id'] = $v['id'];
			//$data['supplier_deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
		
		
			/*$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1500)
				{
					$distance_str =  round($distance/1000)."km";
				}
				else
				{
					$distance_str = round($distance)."米";
				}
			}
			$data['supplier_deal_list'][$k]['distance'] = $distance_str;*/
		
		}
		$back_url = wap_url("index","index");
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		//echo "<pre>";
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("shop.html");	
		
	}
	
	public function load_index_list_data(){
		global_run();
		$param['page'] = intval($_REQUEST['page']);
		$data = call_api_core("shop","load_index_list_data",$param);
		if($data['deal_list']){
		  
			foreach($data['deal_list'] as $k=>$v){
				
				$data['deal_list'][$k]['url'] = wap_url("index", 'deal', array('data_id'=>$v['id']));
				$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
				//$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
	
			}
			 
		}
		$GLOBALS['tmpl']->assign("data",$data);
	
		$deal_html =  $GLOBALS['tmpl']->fetch("style5.2/inc/page/shop_deal_list.html");
		$deal_data=array();
		$deal_data['html'] = $deal_html;
		$deal_data['page_total'] = $data['page_total'];
		ajax_return($deal_data);
	
	
	
	}
	

	
	
	
}
?>