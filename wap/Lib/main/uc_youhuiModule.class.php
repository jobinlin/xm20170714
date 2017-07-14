<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 我的优惠券列表
 * @author jobin.lin
 *
 */
class uc_youhuiModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		$param['type'] = intval($_REQUEST['type']);
		if($_REQUEST['tag']){
		    $param['tag'] = intval($_REQUEST['tag']);
		}
		$data = call_api_core("uc_youhui","wap_index",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		$list = $data['item'];
		foreach($list as $k=>$v)
		{
		    $value=$v['value'];
		    if($v['type']){
		        $value=round($value/10,1);
		        $value=explode(".",$value);
		        if(count($value)==1){
		            //array_unshift($value,0);
		            $value[1]=0;
		        }
		    }
		    $list[$k]['value'] = $value;
		}
		$data['item'] = $list;
		
		if(isset($data['page']) && is_array($data['page'])){
			//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			//$page->parameter
			$p  =  $page->show();
			//print_r($p);exit;
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		$GLOBALS['tmpl']->assign("type",$param['type']?$param['type']:0);
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_youhui.html");
	}

	/**
	 * 获取门店
	 * 输入：id 优惠券ID
	 * 
	 *   */
	public function get_location(){
	    
	    $data = call_api_core("uc_youhui","get_location",array('id'=>$_REQUEST['id']));
	    
	    ajax_return($data);
	}
	
	
	/**
	 * 会员中心优惠卷详情页面
	 **/
	public function view()
	{	
		global_run();		
		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		/*if($_REQUEST['tag']){
		    $param['tag'] = intval($_REQUEST['tag']);
		}*/
		$param['data_id'] = intval($_REQUEST['data_id']);

		$data = call_api_core("uc_youhui","wap_view",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
			
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		$GLOBALS['tmpl']->assign("data",$data);	

		$GLOBALS['tmpl']->display("uc_youhui_view.html");	
		
	}

}
?>