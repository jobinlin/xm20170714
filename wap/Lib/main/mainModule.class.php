<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class mainModule extends MainBaseModule
{
	
	/**
	 * 团购首页
	 **/
	public function index()
	{	
		global_run();	
		init_app_page();
		$data = call_api_core("main","index");
		//移到mapi
		/*foreach($data['advs'] as $k=>$v)
		{
				
			$data['advs'][$k]['url'] =  getWebAdsUrl($v);
		}
		$data['advs_count'] = count($data['advs']);*/

		foreach ($data['deal_list'] as $k=>$v){
			$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
			/*$deal_param['data_id'] = $v['id'];
			$data['deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
		
		
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1000)
				{
					$distance_str =  round($distance/1000,2)."km";
				}
				else
				{
					$distance_str = round($distance)."m";
				}
			}
			$data['deal_list'][$k]['distance'] = $distance_str;*/
			 
		}
		
		/*foreach($data['recommend_deal_cate'] as $k=>$v)
		{
			$data['recommend_deal_cate'][$k]['url'] =  wap_url("index","tuan",array("cate_id"=>$v['id']));
		}*/

		$back_url = wap_url("index","index");
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("main.html");	
		
	}
	
	public function load_index_list_data(){
	    global_run();
	    $param['page'] = intval($_REQUEST['page']);
	    $data = call_api_core("main","load_index_list_data",$param);
	 	//logger::write(print_r($data,1));
	    if($data['deal_list']){
	    
	       
	        foreach($data['deal_list'] as $k=>$v){
	        	$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
	        	/*$deal_param['data_id'] = $v['id'];
	        	$data['deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
	        
	        
	        	$distance = $v['distance'];
	        	$distance_str = "";
	        	if($distance>0)
	        	{
	        		if($distance>1500)
	        		{
	        			$distance_str =  round($distance/1000,2)."km";
	        		}
	        		else
	        		{
	        			$distance_str = round($distance)."m";
	        		}
	        	}
	        	$data['deal_list'][$k]['distance'] = $distance_str;*/
	        
	        }
	        
	    }
	    
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	     
	    $deal_html =  $GLOBALS['tmpl']->fetch("style5.2/inc/page/main_deal_list.html");
	    //logger::write($deal_html);
	    $deal_data=array();
	    $deal_data['html'] = $deal_html;
	    $deal_data['page_total'] = $data['page_total'];
	    ajax_return($deal_data);
	     
	     
	     
	}
	

	
	
	
}
?>