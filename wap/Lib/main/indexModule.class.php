<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class indexModule extends MainBaseModule
{
	public function index()
	{
		global_run();
		
		init_app_page();
		
		$data = call_api_core("index","wap");
				
		
		foreach ($data['supplier_list'] as $k=>$v){
		    $data['supplier_list'][$k]['url'] = wap_url("index", 'store', array('data_id'=>$v['id']));
		}
		
		foreach ($data['cate_list'] as $k=>$v){
		    $data['cate_list'][$k]['url'] = wap_url("index", 'tuan', array('cate_id'=>$v['id']));
		    if( ($v['is_new'] == 1 && $v['recommend']) or $v['is_new'] == 1){
		        $data['cate_list'][$k]['show_hot_new'] = 'NEW';
		        $data['cate_list'][$k]['show_hot_new_low'] = 'new';
		    }elseif ($v['recommend'] == 1){
		        $data['cate_list'][$k]['show_hot_new'] = 'HOT';
		        $data['cate_list'][$k]['show_hot_new_low'] = 'hot';
		    }
		}
		 
		foreach ($data['article'] as $k => $v){
		    $data['article'][$k]['url']=wap_url("index", 'notice', array('data_id'=>$v['id']));
		}
		
		foreach($data['advs'] as $k=>$v)
		{
			
			$data['advs'][$k]['url'] =  getWebAdsUrl($v);
		}
		$data['advs_count'] = count($data['advs']);
		foreach($data['advs2'] as $k=>$v)
		{
		    	
		    $data['advs2'][$k]['url'] =  getWebAdsUrl($v);
		}
		
		foreach ($data['deal_list'] as $k=>$v){
		    $deal_param['data_id'] = $v['id']; 
		    $data['deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
		    $data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
		    
		    
		        $distance = $v['distance'];
		        $distance_str = "";
		        if($distance>0)
		        {
		            if($distance>1500)
		            {
		                $distance_str = round($distance/1000,2)."km";
		            }
		            else
		            {
		                $distance_str = round($distance)."m";
		            }
		        }
		        $data['deal_list'][$k]['distance'] = $distance_str;
		     
		}
		
		foreach($data['indexs']['list'] as $k=>$v)
		{
			$data['indexs']['list'][$k]['url'] =  getWebAdsUrl($v);
		}
		// 计算首页导航隐藏页数
		$data_nav_row = intval(ceil(count($data['indexs']) / 8));
		$data_nav_row_str = '';
		for($i=0; $i<$data_nav_row; $i++){
		    $data_nav_row_str .= '<li class=""></li>';
		}
		$GLOBALS['tmpl']->assign("data_nav_row_str",$data_nav_row_str);
	  
		$GLOBALS['tmpl']->assign("data",$data);
		
		if($GLOBALS['geo']['xpoint']>0||$GLOBALS['geo']['ypoint']>0)
		{
			$GLOBALS['tmpl']->assign('has_location',1);
		}
		else
		{
			$GLOBALS['tmpl']->assign('has_location',0);
		}
		
		if (es_cookie::get('is_app_down')){
			$GLOBALS['tmpl']->assign('is_show_down',0);//用户已下载
		}else{
			$GLOBALS['tmpl']->assign('is_show_down',1);//用户未下载
		}		
		
		
		//输出友情链接
		$links = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."link where is_effect = 1 and show_index = 1  order by sort desc");
			
		foreach($links as $kk=>$vv)
		{
			if(substr($vv['url'],0,7)=='http://')
			{
				$links[$kk]['url'] = str_replace("http://","",$vv['url']);
			}
		}			

		$GLOBALS['tmpl']->assign("links",$links);
		
		$GLOBALS['tmpl']->display("index.html");
	}
	
	
	public function load_index_list_data(){
	    global_run();
	    $param['page'] = intval($_REQUEST['page']);
	    $data = call_api_core("index","load_index_list_data",$param);
	 
	    if($data['deal_list']){
	    
	        foreach($data['deal_list'] as $k=>$v){    
	            
	            $data['deal_list'][$k]['url'] = wap_url("index", 'deal', array('data_id'=>$v['id']));
	            
	            $data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);  
	           
	        }
	        
	    }
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	     
	    $deal_html =  $GLOBALS['tmpl']->fetch("style5.2/inc/page/index_deal_list.html");
	    $deal_data=array();
	    $deal_data['html'] = $deal_html;
	    $deal_data['page_total'] = $data['page_total'];
	    $deal_data['page'] = $data['page'];
	    ajax_return($deal_data);
	     
	     
	     
	}
	
	
}
?>