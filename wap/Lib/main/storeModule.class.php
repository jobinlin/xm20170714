<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class storeModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();		

		$param['data_id'] = intval($_REQUEST['data_id']); //分类ID
        
		$request = $param;
		//获取品牌
		$data = call_api_core("store","index",$param);

		if(intval($data['id'])==0)
		{
		    //app_redirect(wap_url("index"));
		    // $jump_url = wap_url('index', 'events');
		    $script = suiShow('商家不存在或已删除', $jump_url);
		    $GLOBALS['tmpl']->assign('suijump', $script);
		    $GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
		}else{
		
    		//星星
    		$data['store_info']['bfb'] = ($data['store_info']['avg_point']/5)*100;
    		
    		
    		// 活动
    		foreach ($data['event_list'] as $k=>$v){
    		    $data['event_list'][$k]['event_url'] = wap_url("index", 'event', array('data_id'=>$v['id']) );
    		}
    		
    		// 团购
    		foreach ($data['tuan_list'] as $k=>$v){
    			$data['tuan_list'][$k]['current_price'] = format_price_html($v['current_price']);
    			$data['tuan_list'][$k]['origin_price'] = sprintf("%.2f",$v['origin_price']);
    		    $data['tuan_list'][$k]['tuan_url'] = wap_url("index", 'deal', array('data_id'=>$v['id'],'location_id'=>$param['data_id']) );
    		}
    		//echo "<pre>";print_r($data['tuan_list']);exit;
    		// 商品
    		foreach ($data['deal_list'] as $k=>$v){
    			$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
    			$data['deal_list'][$k]['origin_price'] = sprintf("%.2f",$v['origin_price']);
    		    $data['deal_list'][$k]['deal_url'] = wap_url("index", 'deal', array('data_id'=>$v['id']) );
    		}
    		foreach ($data['dp_list'] as $k=>$v){
    			$data['dp_list'][$k]['bfb'] = ($v['point']/5)*100;
    		}
    		// 推荐商家
    		foreach ($data['location_list'] as $k=>$v){
    		    $data['location_list'][$k]['location_url'] = wap_url("index", 'store', array('data_id'=>$v['id']) );
    		    $data['location_list'][$k]['avg_point'] = round($v['avg_point'], 1);
    		    
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
    		    $data['location_list'][$k]['distance'] = $distance_str;
    		    
    		}
    		
    		
    		foreach ($data['other_supplier_location'] as $k=>$v){
    		    $data['other_supplier_location'][$k]['location_url'] = wap_url("index", 'store', array('data_id'=>$v['id']) );
    		    
    		    
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
    		    $data['other_supplier_location'][$k]['distance'] = $distance_str;
    		}
    		
    		// 分店数
    		$data['other_supplier_location_count'] = count($data['other_supplier_location']);
    		// 评价链接
    		$data['dp_url'] = wap_url("index", 'dp_list', array( 'data_id'=>$param['data_id'], 'type'=>'store') );
    		
    		// 优惠买单地址
    		$data['store_pay_url'] = wap_url("index", 'store_pay', array('id'=>$param['data_id']) );
    		
    		$data['store_info']['ref_avg_price'] = round($data['store_info']['ref_avg_price']);
           
    		if(intval($data['id'])==0)
    		{
    		    app_redirect(wap_url("index"));
    		}
    		//$back_url = wap_url("index","stores");
    		//$GLOBALS['tmpl']->assign("back_url",$back_url);
    		$GLOBALS['tmpl']->assign("request",$request);
    		$GLOBALS['tmpl']->assign("store_info",$data['store_info']);
    		//echo "<pre>";print_r($data);exit;
    		//print_r($data);exit;
    		$GLOBALS['tmpl']->assign("data",$data);		
    		$GLOBALS['tmpl']->display("store.html");
		}
	}
	
	
	/**
	 *  商户详情/团购列表
	 **/
	public function tuan()
	{	
		global_run();		
		init_app_page();
		$param['data_id'] = intval($_REQUEST['data_id']);
		$request = $param;
		//获取品牌
		$data = call_api_core("store","tuan",$param);

		// 团购
		foreach ($data['tuan_list'] as $k=>$v){
			$data['tuan_list'][$k]['current_price'] = format_price_html($v['current_price']);
			$data['tuan_list'][$k]['origin_price'] = sprintf("%.2f",$v['origin_price']);
		    $data['tuan_list'][$k]['tuan_url'] = wap_url("index", 'deal', array('data_id'=>$v['id'],'location_id'=>$param['data_id']) );
		}
		$data['tuan_count']=count($data['tuan_list']);

       
		if(intval($data['id'])==0)
		{
		    app_redirect(wap_url("index"));
		}
		//echo "<pre>";print_r($data);exit;
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->assign("tuan",$data['tuan_list']);
		$GLOBALS['tmpl']->display("store_tuan.html");	
		
		
	}
	
	/**
	 *    A-9-4商户详情/商品列表
	 **/
	public function shop()
	{	
		global_run();		
		init_app_page();
		$param['data_id'] = intval($_REQUEST['data_id']);
		$param['cate_id'] = intval($_REQUEST['cate_id']);
		$param['order_type'] = $_REQUEST['order_type'];
		$param['page'] = intval($_REQUEST['page']); //分页
		
		$GLOBALS['tmpl']->assign("order_type",$_REQUEST['order_type']);
		
		/* $request = $param;
		$GLOBALS['tmpl']->assign("request",$request); */
		//获取商品
		$data = call_api_core("store","shop",$param);
		
		// 商品
		foreach ($data['deal_list'] as $k=>$v){
		    $data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
		    $data['deal_list'][$k]['origin_price'] = sprintf("%.2f",$v['origin_price']);
		    $data['deal_list'][$k]['deal_url'] = wap_url("index", 'deal', array('data_id'=>$v['id']) );
		}
		$data['deal_count']=count($data['deal_list']);
		
		
		if(intval($data['id'])==0)
		{
		    app_redirect(wap_url("index"));
		}
		
		//格式化bcate_list的url
		$bcate_list = $data['cate_list'];
		foreach($bcate_list as $k=>$v)
		{
		    $tmp_url_param = $param;
		    unset($tmp_url_param['order_type']);
		    $tmp_url_param['cate_id']=$v['id'];
		    	
		    $bcate_list[$k]["url"] = wap_url("index","store#shop",$tmp_url_param);
		    foreach($v['bcate_type'] as $kk=>$vv)
		    {
		        $tmp_url_param = $param;
		        unset($tmp_url_param['order_type']);
		        $tmp_url_param['cate_id']=$vv['id'];
		        $tmp_url_param['bid']=0;
		        $bcate_list[$k]["bcate_type"][$kk]["url"]= wap_url("index","store#shop",$tmp_url_param);
		    }
		}
		$data['cate_list'] = $bcate_list;
		
		if(isset($data['page']) && is_array($data['page'])){
		    
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    //$page->parameter
		    $p  =  $page->show();
		    //print_r($p);exit;
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		
		if($param['order_type']=='price_asc'){
		    $param['order_type']='price_desc';
		    $price_url =  wap_url("index","store#shop",$param);
		}else{
		    $param['order_type']='price_asc';
		    $price_url =  wap_url("index","store#shop",$param);
		}
		
		$param['order_type']='buy_count';
		$sale_url =  wap_url("index","store#shop",$param);
		
		$GLOBALS['tmpl']->assign("sale_url",$sale_url);
		$GLOBALS['tmpl']->assign("price_url",$price_url);
		
		//$back_url = wap_url("index","store",array("data_id"=>$param['data_id']));
		//$GLOBALS['tmpl']->assign("back_url",$back_url);
		
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("shop",$data['deal_list']);
		$GLOBALS['tmpl']->display("store_shop.html");	
		
	}
	
	/**
	 * A-9-5 全部评论页
	 * @return  
	 */
	public function reviews(){
	    global_run();
	    init_app_page();
		$param['data_id'] = intval($_REQUEST['data_id']);
		$param['page'] = abs(intval($_REQUEST['page']));

	    $data = call_api_core("store","reviews",$param);

	    if(isset($data['page']) && is_array($data['page'])){
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
	    $GLOBALS['tmpl']->assign('data', $data);
	    $GLOBALS['tmpl']->display("store_reviews.html");
	}
	
}
?>