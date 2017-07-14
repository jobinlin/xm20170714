<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class scores_indexModule extends MainBaseModule
{
	
	/**
	 * 积分商城页面控制器
	 **/
	public function index()
	{	
		global_run();		
		init_app_page();
		
		$param['page'] = intval($_REQUEST['page']); //分页
		$data = call_api_core("scores_index","index",$param);
			
		//end bcate_list
		set_gopreview();
		if(isset($data['page']) && is_array($data['page'])){
		
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    //$page->parameter
		    $p  =  $page->show();
		    //print_r($p);exit;
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
//print_r($data);
		$back_url = wap_url("index","index");
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("scores_index.html");	
	}
	
	public function load_index_list_data(){
	    global_run();
	    $param['page'] = intval($_REQUEST['page']);
	    $data = call_api_core("scores_index","load_index_list_data",$param);
	     
	    $GLOBALS['tmpl']->assign("data",$data);
	
	    $deal_html =  $GLOBALS['tmpl']->fetch("style5.2/inc/page/scores_index_deal_list.html");
	    $deal_data=array();
	    $deal_data['html'] = $deal_html;
	    $deal_data['page_total'] = $data['page_total'];
	    ajax_return($deal_data);

	}

	public function signin(){
	    global_run();
	    $data = call_api_core("scores_index","signin");
	
	    ajax_return($data['result']);
	
	}
	
}
?>