<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_score_orderModule extends MainBaseModule
{
	
	/**
	 * A-11-4 兑换记录
	 **/
	public function index()
	{	
		global_run();

		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		$param['pay_status'] = 9;
		// $param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("uc_order","wap_index",$param);
// 		print_r($data);exit;
		foreach ($data['item'] as $k=>$v){
			$data['item'][$k]['format_total_price']=format_price_html($v['total_price'],1);
			foreach ($v['deal_order_item'] as $kk=>$vv){
				if($vv['count']==1){
					foreach ($vv['list'] as $kkk=>$vvv){
						$data['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['return_total_score'] = '积分:'.$vvv['return_total_score'];
					}
				}
				
				
			}
		}

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
// print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	

		$GLOBALS['tmpl']->display("uc_score_order_demo.html");	
		
	}
	
	
	/**
	 * A-11-4-1 兑换记录详情
	 **/
	public function view()
	{
	    global_run();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("uc_order","wap_view",array("data_id"=>$data_id));
	    //echo "<pre>";print_r($data);exit;
    	$data['item']['format_total_price']=format_price_html($data['item']['total_price'],1);
    	$data['item']['format_pay_amount']=format_price_html($data['item']['pay_amount']);
    	
    	
    	foreach ($data['item']['deal_order_item'] as $kk=>$vv){
    		if($vv['count']==1){
    			foreach ($vv['list'] as $kkk=>$vvv){
    				$data['item']['deal_order_item'][$kk]['list'][$kkk]['unit_price'] = format_price_html($vvv['unit_price']);
    			}
    		}
    
    
    	}
    	//echo "<pre>";print_r($data);exit;
    	// print_r($data);exit;
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->display("uc_score_order_view_demo.html");
	
	}
	
	
	
	
}
?>