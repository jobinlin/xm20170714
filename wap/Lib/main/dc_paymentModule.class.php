<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dc_paymentModule extends MainBaseModule
{
	
	

	/**
	 *  支付订单页面，点击“确认支付”的跳转地址
	 *
	 */
	public function done()
	{
	
		global_run();
		init_app_page();
		// $param['pay_status'] = intval($_REQUEST['pay_status']);
		$param['order_id'] = intval($_REQUEST['id']);
		// $param['payment_notice_id'] = intval($_REQUEST['payment_notice_id']);
		// $param['form'] = 'wap';
		$data = call_api_core("dc_payment","wap_done",$param);

		if ($data['status'] == 0) {
			app_redirect(wap_url('index', 'dc'));
		}

		if($data['is_rs']==1){
			$back_url = wap_url('index','dc_rsorder');
			$viewCtl = 'dc_rsorder';
			$indexCtl = 'dctable';
		}else{
			$back_url = wap_url('index','dc_dcorder');
			$viewCtl = 'dc_dcorder';
			$indexCtl = 'dc';
		}
		$GLOBALS['tmpl']->assign("back_url",$back_url);

		$data['view_url']=wap_url('index', $viewCtl.'#view', array('id' => $data['order_id']));
		$data['index_url'] = wap_url('index', $indexCtl);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("dc/dc_payment_done.html");
	

	}
	


}
?>