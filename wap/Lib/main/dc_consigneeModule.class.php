<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class dc_consigneeModule extends MainBaseModule
{
	/**
	 *
	 * 会员中心收货地址列表页
	 */ 
	public function old_index()
	{
		global_run();

		$param=array();
		$from=strim($_REQUEST['from']);
		$lid=intval($_REQUEST['lid']);
		$data = call_api_core("dc_consignee","index",$param);

		if($data['user_login_status']==1)
		{
			if($from=='cart'){
				$GLOBALS['tmpl']->assign("from",$from);
				$GLOBALS['tmpl']->assign("lid",$lid);
			}
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->display("dc/dc_consignee.html");
		}
		else
		{
			app_redirect(wap_url('index','user#login'));
		}
		
	}

	/**
	 * 外卖配送地址列表
	 * @return mixed 
	 */
	public function index()
	{
		global_run();
		$param = array('lid' => intval($_REQUEST['lid']));
		$data = call_api_core('dc_consignee', 'index', $param);
		$GLOBALS['tmpl']->assign('data', $data);
		$html = $GLOBALS['tmpl']->fetch('dc/inc/consignee_list.html');
		ajax_return(array('html' => $html));
	}
	
	/**
	 * 新增或编辑送货地址页面
	 * 
	 */
	
	public function old_add()
	{
			global_run();
			$param['id']=strim($_REQUEST['id']);
			$from=strim($_REQUEST['from']);
			$lid=intval($_REQUEST['lid']);
			$data = call_api_core("dc_consignee","add",$param);
			
			if($data['user_login_status']==1)
			{
				$GLOBALS['tmpl']->assign("from",$from);
				$GLOBALS['tmpl']->assign("lid",$lid);
				$GLOBALS['tmpl']->assign("data",$data);
				$GLOBALS['tmpl']->display("dc/dc_consignee_add.html");
			}
			else
			{
			
				showErr('未登录，请先登录',0,wap_url('index','user#login'));
			}
			
		
	
	}

	public function add()
	{
		global_run();
		$param = array('id' => intval($_REQUEST['id']));
		$data = call_api_core('dc_consignee', 'add', $param);
		$GLOBALS['tmpl']->assign('data', $data);
		$html = $GLOBALS['tmpl']->fetch('dc/inc/consignee_info.html');
		ajax_return(array('html' => $html));
	}
	
	
	/**
	 * 新增或修改外卖 送货的提交地址
	 */
	
	public function old_save_dc_consignee(){

			$param['xpoint']=strim($_REQUEST['xpoint']);
			$param['ypoint']=strim($_REQUEST['ypoint']);
			$param['api_address']=strim($_REQUEST['api_address']);
			$param['address']=strim($_REQUEST['address']);		
			$param['consignee']=strim($_REQUEST['consignee']);
			$param['mobile']=strim($_REQUEST['mobile']);
			$param['is_main']=intval($_REQUEST['is_main']);
			$param['id']=intval($_REQUEST['id']);
			$data = call_api_core("dc_consignee","save_dc_consignee",$param);
			
			if($data['user_login_status']==1)
			{

				$result['status']=$data['status'];
				$result['info']=$data['info'];
				ajax_return($result);

			}
			else
			{
			
				showErr('未登录，请先登录',0,wap_url('index','user#login'));
			}

			
		
	}
	public function save_dc_consignee()
	{
		$param['xpoint']=strim($_REQUEST['xpoint']);
		$param['ypoint']=strim($_REQUEST['ypoint']);
		$param['api_address']=strim($_REQUEST['api_address']);
		$param['address']=strim($_REQUEST['address']);		
		$param['consignee']=strim($_REQUEST['consignee']);
		$param['mobile']=strim($_REQUEST['mobile']);
		$param['is_main']=intval($_REQUEST['is_main']);
		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_consignee","save_dc_consignee",$param);
		
		/*if($data['user_login_status']==1) {
			$result['status']=$data['status'];
			$result['info']=$data['info'];
			ajax_return($result);
		}*/
		ajax_return($data);
	}
	
	/**
	 * 删除送货地址
	 */
	
	public function old_del(){


		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_consignee","del",$param);
			
		if($data['user_login_status']==1)
		{
		
			$result['status']=$data['status'];
			$result['info']=$data['info'];
			ajax_return($result);
		
		}
		else
		{
				
			showErr('未登录，请先登录',0,wap_url('index','user#login'));
		}
		
	}
	
	public function del()
	{
		$param['id']=intval($_REQUEST['id']);
		$data = call_api_core("dc_consignee","del",$param);
		/*if($data['user_login_status'] == 1) {
			$result['status'] = $data['status'];
			$result['info'] = $data['info'];
			ajax_return($result);
		} else {
			app_redirect(wap_url('index','user#login', $param));
		}*/
		ajax_return($data);
	}

}
?>