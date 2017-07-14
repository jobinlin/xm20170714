<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class biz_tuan_orderModule extends MainBaseModule
{
	

    /**
     * 团购订单列表
     **/
    public function index()
    {
        global_run();
        $page = intval($_REQUEST['p']);
        $type = intval($_REQUEST['type']);
        $data = call_api_core("biz_tuan_order","index",array("page"=>$page,'type'=>$type));
        if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
    
        if(isset($data['page']) && is_array($data['page'])){
    
            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            //print_r($p);exit;
            $GLOBALS['tmpl']->assign('pages',$p);
        }
    
        if($data['status']==0){
            $jump_url = wap_url('biz', 'shop_verify');
            $script = suiShow($data['info'], $jump_url);
            $GLOBALS['tmpl']->assign('suijump', $script);
            $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
        }else{
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("biz_tuan_order.html");
        }
    
    }
    
    
    /**
     * 团购订单详情
     **/
    public function view()
    {
        global_run();
        $data_id = intval($_REQUEST['data_id']);
        $data = call_api_core("biz_tuan_order","view",array("data_id"=>$data_id));
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
             
        	   // print_r($data);
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("biz_tuan_view.html");
        }
    
    }
	
	
}
?>