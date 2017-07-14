<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class PaymentNoticeAction extends CommonAction{
	public function index()
	{
		if(strim($_REQUEST['order_sn'])!='')
		{
			$condition['order_id'] = M("DealOrder")->where("order_sn='".strim($_REQUEST['order_sn'])."'")->getField("id");
		}
		if(strim($_REQUEST['notice_sn'])!='')
		{
			$condition['notice_sn'] = $_REQUEST['notice_sn'];
		}	
	   if(strim($_REQUEST['user_name'])!='')
		{
			$condition['user_id'] = M("User")->where("user_name='".strim($_REQUEST['user_name'])."'")->getField("id");
		}	
	 
		$condition['order_type']  = array( 'eq' , 3);
		
		if(intval($_REQUEST['payment_id'])==0)unset($_REQUEST['payment_id']);
		$this->assign("default_map",$condition);
		$this->assign("payment_list",M("Payment")->findAll());
		parent::index();
	}
	
	public function store_pay_index()
	{
	    if(strim($_REQUEST['order_sn'])!='')
	    {
	        $condition['order_id'] = M("StorePayOrder")->where("order_sn='".strim($_REQUEST['order_sn'])."'")->getField("id");
	    }
	    if(strim($_REQUEST['notice_sn'])!='')
	    {
	        $condition['notice_sn'] = $_REQUEST['notice_sn'];
	    }
	    if(strim($_REQUEST['user_name'])!='')
	    {
	        $condition['user_id'] = M("User")->where("user_name='".strim($_REQUEST['user_name'])."'")->getField("id");
	    }
	
	    $condition['order_type'] = 4;
	    
	    if(intval($_REQUEST['payment_id'])==0)unset($_REQUEST['payment_id']);
	    $this->assign("default_map",$condition);
	    $this->assign("payment_list",M("Payment")->findAll());
	    parent::index();
	}
	
	public function dc_index()
	{
		if(strim($_REQUEST['order_sn'])!='')
		{
			$condition['order_id'] = M("DcOrder")->where("order_sn='".strim($_REQUEST['order_sn'])."'")->getField("id");
		}
		if(strim($_REQUEST['notice_sn'])!='')
		{
			$condition['notice_sn'] = $_REQUEST['notice_sn'];
		}
		if(strim($_REQUEST['user_name'])!='')
		{
			$condition['user_id'] = M("User")->where("user_name='".strim($_REQUEST['user_name'])."'")->getField("id");
		}
	
		$condition['order_type']  = array( 'eq' , 1);
	
		if(intval($_REQUEST['payment_id'])==0)unset($_REQUEST['payment_id']);
		$this->assign("default_map",$condition);
		$this->assign("payment_list",M("Payment")->findAll());
		parent::index();
	}
	
	
}
?>