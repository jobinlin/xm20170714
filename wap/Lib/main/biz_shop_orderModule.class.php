<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class biz_shop_orderModule extends MainBaseModule
{
	
	/**
	 * 商品订单列表
	 **/
	public function index()
	{	
		global_run();	
		$page = intval($_REQUEST['page']);
		$type = intval($_REQUEST['type']);
		$data = call_api_core("biz_shop_order","index",array("page"=>$page,'type'=>$type));
		if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
		    app_redirect(wap_url("biz","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
		
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    //$page->parameter
		    $p  =  $page->show();
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		//logger::write(print_r($data,1));
		$GLOBALS['tmpl']->assign("type",$type);
		$GLOBALS['tmpl']->assign("data",$data);
		if($type==0){
		    $type_arr=array(1,2);
		}elseif($type==1){
		    $type_arr=array(0,2);
		}elseif($type==2){
		    $type_arr=array(0,1);
		}
		if($data['status']==0){
		    $jump_url = wap_url('biz', 'shop_verify');
		    $script = suiShow($data['info'], $jump_url);
		    $GLOBALS['tmpl']->assign('suijump', $script);
		    $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
		}else{
		    $GLOBALS['tmpl']->assign("type_arr",$type_arr);
		    $GLOBALS['tmpl']->display("biz_shop_order.html");
		}
		
	}
	

	/**
	 * 商品订单列表
	 **/
	public function view()
	{
	    global_run();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("biz_shop_order","view",array("data_id"=>$data_id));
	    if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
	        app_redirect(wap_url("biz","user#login"));
	    }
		if(intval($data['data_id'])==0)
		{
		    //app_redirect(wap_url("index"));
		    $jump_url = wap_url('biz', 'shop_verify');
		    $script = suiShow('订单不存在', $jump_url);
		    $GLOBALS['tmpl']->assign('suijump', $script);
		    $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
		}else{
	    
    	    //print_r($data);
		    $GLOBALS['tmpl']->assign("data",$data);
    	    $GLOBALS['tmpl']->display("biz_shop_view.html");
		}
	
	}
    /**
     * @desc    物流跟踪
     * @author    wuqingxiang
     */
    public function logistics()
    {
        global_run();
        $data_id = intval($_REQUEST['data_id']);
        $data = call_api_core("biz_shop_order","logistics",array("data_id"=>$data_id));

        if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
//        if(!$data['delivery_count']>0){
//            app_redirect(wap_url("biz","uc_order#view",array("data_id"=>$data_id)));
//        }
        $refund_count = 0;
        foreach($data['delivery_notice'] as $k=>$v){
            foreach($v['deal_info'] as $kk=>$vv){
                if($vv['deal_status']=='已退款'){
                    $refund_count=$refund_count+1;
                }
            }

        }
        $data['refund_count']=$refund_count;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_shop_order_logistics.html");

    }
	
    public function delivery(){
        global_run();
        init_app_page();
    
        $param['data_id'] = intval($_REQUEST['data_id']);
    
        $data = call_api_core("biz_shop_order","delivery",$param);
    
        if ($data['biz_user_status']==0){ //用户未登录
            app_redirect(wap_url("biz","user#login"));
        }
        
        if ($data['is_auth']==0){ //没有操作权限
            app_redirect(wap_url("biz","shop_verify"));
        }
        
        if($data['status']==0){//非法订单
            app_redirect(wap_url("biz","shop_verify"));
        }
        
        //设定页面类型为验证部分
        $data['page_type'] = "o";
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("ajax_url",wap_url("biz","biz_shop_order#do_delibery",$param));
        $GLOBALS['tmpl']->display("biz_shop_order_delivery.html");
    }
    
    public function do_delivery(){
        global_run();
        init_app_page();
    
        $param = array();
        $param['doi_ids'] = $_REQUEST['doi_ids'];
        $param['location_id'] = intval($_REQUEST['location_id']);
        $param['express_id'] = intval($_REQUEST['express_id']);
        $param['delivery_sn'] = strim($_REQUEST['delivery_sn']);
        $param['memo'] = strim($_REQUEST['memo']);
        $param['is_delivery']=intval($_REQUEST['is_delivery']);
        
        $data = call_api_core("biz_shop_order","do_delivery",$param);
        
        if ($data['is_auth']==0){ //没有操作权限
            $data['jump']= wap_url("biz","shop_verify");
        }
        
        if ($data['status'] == 1){
            $data['jump']= wap_url("biz","shop_order#view",array('data_id'=>$data['order_id']));
        }
        
        ajax_return($data);
    }
	
	
	
}
?>