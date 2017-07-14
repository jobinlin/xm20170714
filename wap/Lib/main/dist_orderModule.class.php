<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dist_orderModule extends MainBaseModule
{
    public function index() {
        dist_global_run();	
		dist_init_app_page();		

		$param['status'] = intval($_REQUEST['status']);
        
		if($param['status']==3){
		    $data = call_api_core("dist_order","refund_order_list");
		}else{
		    $data = call_api_core("dist_order","index",$param);
		}
        
		if($data['dist_user_status']==0){
		    app_redirect(wap_url("dist","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    
		    $p  =  $page->show();
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		
		$GLOBALS['tmpl']->assign("status",$param['status']);
		$GLOBALS['tmpl']->assign("data",$data);
		
        $GLOBALS['tmpl']->display("dist_order_index.html");
    }
    
    public function view() {
        dist_global_run();
        dist_init_app_page();
        
        $param['data_id'] = intval($_REQUEST['data_id']);
        
        $data = call_api_core("dist_order","view",$param);
        
        if($data['dist_user_status']==0){
            app_redirect(wap_url("dist","user#login"));
        }
        
        if($data['order_status']==0){
            app_redirect(wap_url("dist","order"));
        }
        
        $GLOBALS['tmpl']->assign("data",$data);
        
        $GLOBALS['tmpl']->display("dist_order_view.html");
    }
    
    public function delivery() {
        dist_global_run();
        dist_init_app_page();
        
        $param['data_id'] = intval($_REQUEST['data_id']);
        
        $data = call_api_core("dist_order","delivery",$param);
        
        if($data['dist_user_status']==0){
            app_redirect(wap_url("dist","user#login"));
        }
        if($data['order_status']==0){
            app_redirect(wap_url("dist","order"));
        }
        if($data['delivery_status']==0){
            app_redirect(wap_url("dist","order#view",$param));
        }
        
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_order_delivery.html");
    }
    
    public function do_delivery() {
        dist_global_run();
        dist_init_app_page();
        
        $param['order_id'] = $_REQUEST['data_id'];
        
        $data = call_api_core("dist_order","do_delivery",$param);
        
        if($data['status']){
            $data['jump']=wap_url("dist","order#view",array("data_id"=>$data['order_id']));
        }
        
        ajax_return($data);
    }
    
}