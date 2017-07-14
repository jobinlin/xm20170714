<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 我的消费券列表
 * @author jobin.lin
 *
 */
class uc_couponModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		$param['order_id'] = intval($_REQUEST['order_id']);
		$param['coupon_status'] = intval($_REQUEST['coupon_status']);
		$data = call_api_core("uc_coupon","wap_index",$param);
		
		if($param['order_id']){
		    $GLOBALS['tmpl']->assign("order_id",$param['order_id']);
		}

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}

		if(isset($data['page']) && is_array($data['page'])){
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page2 = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    //$page->parameter
		    $p2  =  $page2->show();
		    
		    $GLOBALS['tmpl']->assign('pages',$p2);
		}
		$GLOBALS['tmpl']->assign("status",$param['coupon_status']);
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->assign("tuan",$data['tuan_item']);
		$GLOBALS['tmpl']->assign("pick",$data['pick_item']);
		$GLOBALS['tmpl']->assign("dist",$data['dist_item']);
		$GLOBALS['tmpl']->display("uc_coupon.html");
	}
	

	/**
	 * 消费券详情
	 * @return html 
	 */
	public function view()
	{
		global_run();
		init_app_page();
		$param['page'] = intval($_REQUEST['page']);
		$param['sp_id'] = intval($_REQUEST['sp_id']); // 商户ID
		$param['deal_id'] = intval($_REQUEST['deal_id']); // 商品ID
		$deal_id = intval($_REQUEST['deal_id']);
		$data = call_api_core('uc_coupon', 'wap_view', $param);

		if ($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			login_is_app_jump();
			//app_redirect(wap_url('index', 'user#login'));
		}

		if(isset($data['page']) && is_array($data['page'])){
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
        

		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_coupon_view.html");
	}

}
?>