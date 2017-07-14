<?php
/**
 * @desc      
 * @author    wuqingxiang
 * @since     2017-01-13 10:07  
 */
class biz_refund_orderModule extends MainBaseModule
{
    /**
     * @desc    退款维权
     * @author    郑雄
     */
    public function index()
    {
        global_run();
        $data = call_api_core("biz_refund_order","idnex",array('page'=>intval($_REQUEST['page'])));

        if($data['biz_login_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
		foreach($data['list'] as $k=>$v){
			$s_total_price=round($v['s_total_price'],1);
			if(!strstr($s_total_price,'.')){
				$s_total_price=$s_total_price.".0";
			}
			$data['list'][$k]['s_total_price']=$s_total_price;
			$unit_price=round($v['unit_price'],1);
			if(!strstr($unit_price,'.')){
				$unit_price=$unit_price.".0";
			}
			$data['list'][$k]['format_unit_price']=$unit_price;
		}
		if(isset($data['page']) && is_array($data['page'])){
        	//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
        	$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
        	$p  =  $page->show();
        	$GLOBALS['tmpl']->assign('pages',$p);
        }
		//echo "<pre>";print_r($data);exit;
        if($data['status']==0){
            $jump_url = wap_url('biz', 'shop_verify');
            $script = suiShow($data['info'], $jump_url);
            $GLOBALS['tmpl']->assign('suijump', $script);
            $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
        }else{
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("biz_refund_order.html");
        }

    }
	    /**
     * @desc    退款详情
     * @author    郑雄
     */
    public function view()
    {
        global_run();
        $data_id = intval($_REQUEST['data_id']);
		$msg_id = intval($_REQUEST['msg_id']);
        $data = call_api_core("biz_refund_order","view",array('data_id'=>$data_id,'msg_id'=>$msg_id));

        if($data['biz_login_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
		if($data['is_null']==1){
            app_redirect(wap_url("biz","refund_order"));
        }
		$s_total_price=round($data['item']['s_total_price'],1);
		if(!strstr($s_total_price,'.')){
			$s_total_price=$s_total_price.".0";
		}
		$unit_price=round($data['item']['unit_price'],1);
		if(!strstr($unit_price,'.')){
			$unit_price=$unit_price.".0";
		}
		$data['item']['format_unit_price']=$unit_price;
		$data['item']['s_total_price']=$s_total_price;
      
        //echo "<pre>";print_r($data);exit;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_refund_order_view.html");

    }
	 /**
     * @desc    退货
     * @author    郑雄
     */
    public function do_refund()
    {
        global_run();
        $data = call_api_core("biz_refund_order","do_refund",array('data_id'=>intval($_REQUEST['data_id']),'msg_id'=>intval($_REQUEST['msg_id'])));

        if($data['biz_login_status']!=LOGIN_STATUS_LOGINED){
            $data['jump']=wap_url("biz","user#login");
        }else{
			$data['jump']= get_current_url();
		}
        ajax_return($data);

    }
	 /**
     * @desc    拒绝退货
     * @author    郑雄
     */
    public function do_refuse()
    {
        global_run();
		$data = call_api_core("biz_refund_order","do_refuse",array('data_id'=>intval($_REQUEST['data_id']),'msg_id'=>intval($_REQUEST['msg_id'])));

        if($data['biz_login_status']!=LOGIN_STATUS_LOGINED){
            $data['jump']=wap_url("biz","user#login");
        }else{
			$data['jump']= get_current_url();
		}
        ajax_return($data);

    }
	
}