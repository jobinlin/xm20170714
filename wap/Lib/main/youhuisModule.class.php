<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class youhuisModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$deal_cate = load_auto_cache("cache_deal_cate");

		$param['cate_id'] = intval($_REQUEST['cate_id']);//分类id
		$param['page'] = intval($_REQUEST['page']);

		$request = $param;
		$request['catename'] = $deal_cate[$param['cate_id']]['name'];
		$data = call_api_core("youhuis","wap_index",$param);

		$GLOBALS['tmpl']->assign("request",$request);
		
		//格式化bcate_list的url
		$bcate_list = $data['bcate_list'];//分类
		foreach($bcate_list as $k=>$v)
		{	
		    if($v['id']==$param['cate_id']){//默认分类标识
		        $GLOBALS['tmpl']->assign("default_cate_id",$k);
		    }
		    
			$tmp_url_param = $param;
			$tmp_url_param['cate_id']=$v['id'];			
			
			$bcate_list[$k]["url"] = wap_url("index","youhuis",$tmp_url_param);
			
			foreach($v['bcate_type'] as $kk=>$vv)
			{				
				$tmp_url_param = $param;
				$tmp_url_param['cate_id']=$v['id'];

				$bcate_list[$k]["bcate_type"][$kk]["url"]= wap_url("index","youhuis",$tmp_url_param);
			}
		}
		$data['bcate_list'] = $bcate_list;
		//end bcate_list

		$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("youhuis.html");
	}
	
	
}
?>