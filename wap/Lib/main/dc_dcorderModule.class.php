<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class dc_dcorderModule extends MainBaseModule
{

	
	public function index()
	{
	    global_run();
		$param['page']=intval($_REQUEST['page']);
		$is_ajax=0;
		$is_ajax = intval($_REQUEST['is_ajax']);
		$data = call_api_core("dc_dcorder","index",$param);
		init_app_page();
		if($data['user_login_status']==1)
		{
	
			
			require_once(APP_ROOT_PATH."system/model/dc.php");
			foreach($data['order_list'] as $k=>$v){
				$data['order_list'][$k]['order_state']=get_order_state($v,'wap','index');		
			}
			if(isset($data['page']) && is_array($data['page'])){
					
				//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
				$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
				//$page->parameter
				$p  =  $page->show();
				//print_r($p);exit;
				$GLOBALS['tmpl']->assign('pages',$p);
			}
		
			if($is_ajax==1){
			    $GLOBALS['tmpl']->assign("data",$data);
			    $ajaxobj['html'] = $GLOBALS['tmpl']->fetch("dc/inc/dc_order.html");
			    ajax_return($ajaxobj);
			}else{
			    $GLOBALS['tmpl']->assign("data",$data);
                if(APP_INDEX=='app')
                    $back_url ='javascript:App.app_detail(107,0);';
                else
                    $back_url=wap_url("index","user_center");
                $GLOBALS['tmpl']->assign("back_url",$back_url);
			    $GLOBALS['tmpl']->display("dc/uc/dcorder_index.html");
			}
			
		
		}
		else
		{
			app_redirect(wap_url('index','user#login'));
		}

	}
	
	

	public function view()
	{
	    global_run();
	    init_app_page();
		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_dcorder","view",$param);
		if($data['user_login_status']==1)
		{

			if($data['is_order_exists']==1){
				
				require_once(APP_ROOT_PATH."system/model/dc.php");
				$data['order_info']['order_state']=get_order_state($data['order_info'],'wap','view');

				$GLOBALS['tmpl']->assign("data",$data);
				$GLOBALS['tmpl']->assign("back_url",wap_url("index","dc_dcorder"));
				$GLOBALS['tmpl']->display("dc/uc/dcorder_view.html");
			}else{
				showErr('订单不存在',0,wap_url('index','dc_dcorder'));
			}
		
		}
		else
		{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}
	
	
	/**
	 * 外卖确认收货接口
	 * status：为外卖确认收货的状态，status=0,确认收货失败，status=1,确认收货成功
	 * info:返回的提示信息
	 *
	 */
	public function verify_delivery()
	{
		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_dcorder","verify_delivery",$param);
		if($data['user_login_status']==1)
		{
			if($data['is_order_exist']==1){
				$result['status']=$data['status'];
				$result['info']=$data['info'];
				ajax_return($result);
			}else{
			    $result['status']=$data['status'];
			    $result['info']=$data['info'];
			    ajax_return($result);
// 				showErr('订单不存在',0,wap_url('index','dc_dcorder'));
			}
		}
		else
		{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}
	
	
	/**
	 * 外卖取消订单接口
	 * status：为取消订单的操作的状态，status=0,订单取消失败;status=1,订单取消成功
	 * info:返回的提示信息
	 *
	 */
	public function cancel()
	{
		$param['id']=intval($_REQUEST['id']);
		$time_status=intval($_REQUEST['time_status']);//订单详情页自动取消状态
		$data = call_api_core("dc_dcorder","cancel",$param);
		if($data['user_login_status']==1)
		{	
			if($time_status){
				showErr('订单已取消',0,wap_url('index','dc_dcorder#view',array('id'=>$param['id'])));
			}else{
				
				if($data['is_order_exist']==1){
				
					$result['status']=$data['status'];
					$result['info']=$data['info'];
					$result['act']=$data['act'];
					$result['location_tel']=$data['location_tel'];
					ajax_return($result);
				}else{
					showErr('订单不存在',0,wap_url('index','dc_dcorder'));
				}
			
			}
			
		}
		else
		{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}
	
	
	
	

	/**
	 * 外卖催单接口
	 * status：为催单的操作的状态，status=0,催单失败，status=1,催单成功
	 * info:返回的提示信息
	 *
	 */
	public function dc_reminder(){
	
		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_dcorder","dc_reminder",$param);
		
		if($data['user_login_status']==1)
		{
			if($data['is_order_exist']==1){
		
				$result['status']=$data['status'];
				$result['info']=$data['info'];
	
				ajax_return($result);
			}else{
				showErr('订单不存在',0,wap_url('index','dc_dcorder'));
			}
		}
		else
		{
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
	}

	

}
?>