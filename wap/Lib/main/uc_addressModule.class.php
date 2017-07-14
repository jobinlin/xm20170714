<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_addressModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		/*
		$cart = intval($_REQUEST['cart']);
		$order_id = intval($_REQUEST['order_id']);
		*/
		$check= $_REQUEST['check'];
		$is_pick= $_REQUEST['is_pick'];
		$id= $_REQUEST['id'];
		$supplier_id= intval($_REQUEST['supplier_id']);
		if($check){
		    $buy_type=intval($_REQUEST['buy_type']);
			$address_id= $_REQUEST['address_id'];
		    $GLOBALS['tmpl']->assign("check",$check);
		    $GLOBALS['tmpl']->assign("buy_type",$buy_type);
		    $back_url = wap_url("index","cart#check",array('address_id'=>$address_id,'is_pick'=>$is_pick,'supplier_id'=>$supplier_id,'id'=>$id));
		   
		}else{
		    $back_url = wap_url("index","user_center#index");
		}
		
		$GLOBALS['tmpl']->assign("back_url",$back_url);
		
		$param=array('is_pick'=>$is_pick,'supplier_id'=>$supplier_id);		
		$data = call_api_core("uc_address","index",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		foreach($data['consignee_list'] as $k=>$v){
		    if($check){
	            $data['consignee_list'][$k]['carturl']= wap_url("index","cart#check",array("address_id"=>$v['id'],"buy_type"=>$buy_type,'id'=>$id));
		        $data['consignee_list'][$k]['url']= wap_url("index","uc_address#add",array("id"=>$v['id'],"check"=>"check","buy_type"=>$buy_type,'deal_id'=>$id,'supplier_id'=>$supplier_id,'is_pick'=>$is_pick));
		    }else{
			    $data['consignee_list'][$k]['url']= wap_url("index","uc_address#add",array("id"=>$v['id']));
		    }
			$data['consignee_list'][$k]['del_url']=wap_url('index','uc_address#del',array('id'=>$v['id']));
			$data['consignee_list'][$k]['dfurl']=wap_url('index','uc_address#set_default',array('id'=>$v['id']));			
		}
		
		if($check){
			$param=array();
			$param['check']='check';
			$param['deal_id']=$id;
			$param['supplier_id']=$supplier_id;
			$param['is_pick']=$is_pick;
			if($buy_type){
				$param['buy_type']=1;
				$data['add_url']=wap_url('index','uc_address#add',$param);
			}else{
				$data['add_url']=wap_url('index','uc_address#add',$param);
			}
		}else{
			$data['add_url']=wap_url('index','uc_address#add');
		}
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("id",$id);
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_address_index.html");
	}
	
	public function add()
	{
		global_run();
		init_app_page();
		$cart = intval($_REQUEST['cart']);
		$first = intval($_REQUEST['fr']);
		$order_id = intval($_REQUEST['order_id']);
		$check= $_REQUEST['check'];
		$deal_id= intval($_REQUEST['deal_id']);
		if($check){
			$buy_type=$_REQUEST['buy_type'];
			//返回选择地址页要保留的参数
			$deal_id= intval($_REQUEST['deal_id']);
			$supplier_id= intval($_REQUEST['supplier_id']);
			$is_pick= intval($_REQUEST['is_pick']);
			//end
		    $GLOBALS['tmpl']->assign("check",$check);
			$GLOBALS['tmpl']->assign("is_pick",$is_pick);
			$param=array();
			$param['check']='check';
			$param['id']=$deal_id;
			$param['buy_type']=$buy_type;
			$param['supplier_id']=$supplier_id;
			$param['is_pick']=$is_pick;
			$GLOBALS['tmpl']->assign("back_uc_address_index",wap_url('index','uc_address',$param));
			unset($param);
		}
		if($first){
		    $GLOBALS['tmpl']->assign("first",$first);
		}
		
		if($cart)
		{
			if($order_id)
				es_session::set("wap_cart_set_address_url",wap_url("index","cart#order",array("id"=>$order_id)));
			else
				es_session::set("wap_cart_set_address_url",wap_url("index","cart#check"));
		}
		else
		{
			es_session::set("wap_cart_set_address_url","");
		}
		
		$param=array();
		$param['id'] = intval($_REQUEST['id']);		
		$data = call_api_core("uc_address","add",$param);
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		$GLOBALS['tmpl']->assign("data",$data);

		if($data['consignee_info'])
		$GLOBALS['tmpl']->assign("is_region_lv1_reset","1");
		$GLOBALS['tmpl']->display("uc_address_add.html");	
	}
	

	public function save()
	{
		global_run();
		$param=array();
		$param['id'] = intval($_REQUEST['region_id']);
		$param['region_lv1'] = intval($_REQUEST['region_lv1']);
		$param['region_lv2'] = intval($_REQUEST['region_lv2']);
		$param['region_lv3'] = intval($_REQUEST['region_lv3']);
		$param['region_lv4'] = intval($_REQUEST['region_lv4']);
		$param['address'] = strim($_REQUEST['address']);
		$param['mobile'] = strim($_REQUEST['mobile']);
		$param['consignee'] = strim($_REQUEST['consignee']);
		$param['zip'] = strim($_REQUEST['zip']);
		$param['is_default'] = intval($_REQUEST['is_default']);
		$param['street'] = strim($_REQUEST['street']);
		$param['xpoint'] = $_REQUEST['xpoint'];
		$param['ypoint'] = $_REQUEST['ypoint'];
		$param['doorplate'] = strim($_REQUEST['doorplate']);
		$data = call_api_core("uc_address","save",$param);
// 		print_r($data);exit;
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$result['info'] = "";
			$result['url'] = wap_url("index","user#login");
			ajax_return($result);
		}else{
			if($data['status']==0){
					$result['status'] = 0;
					$result['info']=$data['info'];
					ajax_return($result);	
			}elseif($data['status']==1){
					$result['status'] = 1;
					$wap_cart_set_address_url = es_session::get("wap_cart_set_address_url","");
					if($wap_cart_set_address_url)
						$result['url'] = $wap_cart_set_address_url;
					else
						$result['url'] = wap_url("index","uc_address");
					if($data['address_id'] && $_REQUEST['first']){
					    $result['url'] = wap_url("index","cart#check",array("address_id"=>$data['address_id']));
					}
					ajax_return($result);					
			}
		}
		
		

	}

	public function del()
	{
			global_run();
			$param=array();
			$param['id'] = intval($_REQUEST['id']);
			$data = call_api_core("uc_address","del",$param);
			
			if($data['status']==1){
					$wap_cart_set_address_url = es_session::get("wap_cart_set_address_url","");
					if($wap_cart_set_address_url)
						$data['url'] = $wap_cart_set_address_url;
					else
						$data['url'] = wap_url("index","uc_address");							
			}	
			ajax_return($data);
	
	}
	
	
	public function set_default()
	{
			global_run();
			$param=array();
			$param['id'] = intval($_REQUEST['id']);
			$data = call_api_core("uc_address","set_default",$param);
			
			if($data['set_status']==1){
					$result['status'] = 1;
					$wap_cart_set_address_url = es_session::get("wap_cart_set_address_url","");
					if($wap_cart_set_address_url)
						$result['url'] = $wap_cart_set_address_url;
					else
						$result['url'] = wap_url("index","uc_address");				
					ajax_return($result);			
			}else{
					$result['status'] =0;					
					ajax_return($result);		
			}		
	
	}
}
?>