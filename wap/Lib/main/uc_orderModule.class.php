<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_orderModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		$param['pay_status'] = intval($_REQUEST['pay_status']);
		if (isset($_REQUEST['tuan']) && intval($_REQUEST['tuan']) == 1) {
			$param['tuan'] = 1;
		}
		if ($param['pay_status']==9){
		    $GLOBALS['tmpl']->assign("pay_status",$param['pay_status']);
		}else {
			if(APP_INDEX=='app'){
				$back_url ='javascript:App.app_detail(107,0);';
			}else{
				$back_url = wap_url("index","user_center");
			}
		    $GLOBALS['tmpl']->assign('back_url',$back_url);
		}
		// $param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("uc_order","wap_index",$param);
// 		print_r($data);exit;
		foreach ($data['item'] as $k=>$v){
			$data['item'][$k]['format_total_price']=format_price_html($v['total_price'],1);
			if(APP_INDEX=='app'){
				$json_parma = addslashes(json_encode(array('data_id'=>$v['id'])));
			    $data['item'][$k]['url'] ='javascript:App.app_detail(308,"'.$json_parma.'");';
			}else{
				$data['item'][$k]['url']=wap_url('index','uc_order#view',array('data_id'=>$v['id']));
			}
			foreach ($v['deal_order_item'] as $kk=>$vv){
				if($vv['count']==1){
					foreach ($vv['list'] as $kkk=>$vvv){
						$data['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['unit_price'] = format_price_html($vvv['unit_price'],2);
						$data['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['discount_unit_price'] = format_price_html($vvv['discount_unit_price'],2);
						if($vvv['buy_type']==1){
							$data['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['return_score'] = format_price_html($vvv['return_score'],2);
						}
					}
				}
				
				
			}
		}
		
		for ($i=0;$i<=9;$i++){
			if($i==$data['pay_status']){
				$tab_box[$i]=$data['item'];
			}else{
				$tab_box[$i]="";
			}
			
		}
		unset($data['item']);
		$data['tab_box']=$tab_box;
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
			//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			//$page->parameter
			$p  =  $page->show();
			//print_r($p);exit;
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_order.html");
	}
	
	public function cancel()
	{
		global_run();
		$param=array();
		$param['id'] = intval($_REQUEST['id']);
		$param['is_cancel']=intval($_REQUEST['is_cancel']);
		$data = call_api_core("uc_order","cancel",$param);
		$pay_status=intval($_REQUEST['pay_status']);
// 		print_r($data);exit;
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		if($data['status']==0)
		{
			ajax_return($data);
		}
		else
		{
            // $data['jump']=get_gopreview();
            if($pay_status)
                $data['jump'] = wap_url('index', 'uc_order',array("pay_status"=>$pay_status));
            else
                $data['jump'] = wap_url('index', 'uc_order');
			ajax_return($data);
		}		
	}
	
	/**
	 * 退单（实体商品）页面
	 */
	public function refund()
	{
		global_run();
		init_app_page();
		$item_id = intval($_REQUEST['item_id']);
		
		$deal_id = strim($_REQUEST['deal_id']);
		$coupon_id = strim($_REQUEST['coupon_id']);
		
		$data = call_api_core("uc_order","refund",array("item_id"=>$item_id,"deal_id"=>$deal_id,"coupon_id"=>$coupon_id));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		//echo "<pre>";print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("deal_id",strim($_REQUEST['deal_id']));
		$GLOBALS['tmpl']->assign("coupon_id",strim($_REQUEST['coupon_id']));
		$GLOBALS['tmpl']->display("uc_order_message.html");
	}
	
	
	public function do_refund()
	{
		global_run();
		$item_id = intval($_REQUEST['item_id']);	
			
		$deal_id = strim($_REQUEST['deal_id']);
		$coupon_id = strim($_REQUEST['coupon_id']);
		
		
		$content =  strim($_REQUEST['content']);
		$data = call_api_core("uc_order","do_refund",array("item_id"=>$item_id,"content"=>$content,"deal_id"=>$deal_id,"coupon_id"=>$coupon_id));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['jump'] = wap_url("index","user#login");
		}
		else
		{
			if($data['status'])
			$data['jump'] = wap_url("index","uc_order#view",array("data_id"=>$data['order_id']));
		}
		
		ajax_return($data);
	}
	
	
	/**
	 * 退券（团购商品）页面
	 */
	public function refund_coupon()
	{
		global_run();
		init_app_page();
		$item_id = intval($_REQUEST['item_id']);
		
		$data = call_api_core("uc_order","refund_coupon",array("item_id"=>$item_id));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		$data['placeholder'] = "请输入退款理由";
		$data['action'] = wap_url("index","uc_order#do_refund_coupon");
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("uc_order_coupon_message.html");
	}
	
	
	public function do_refund_coupon()
	{
		global_run();
		
		$item_id = array();
		if($_REQUEST['item_id'])
		{
			foreach($_REQUEST['item_id'] as $k=>$v)
			{
				$item_id[$k] = intval($v);
			}
		}		
		$content =  strim($_REQUEST['content']);
		$data = call_api_core("uc_order","do_refund_coupon",array("item_id"=>$item_id,"content"=>$content));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['jump'] = wap_url("index","user#login");
		}
		else
		{
			if($data['status'])
				$data['jump'] = get_gopreview();
		}
	
		ajax_return($data);
	}
	
	
	/**
	 * 查看物流
	 */
	public function check_delivery()
	{
		$item_id = intval($_REQUEST['item_id']);
		$data = call_api_core("uc_order","check_delivery",array("item_id"=>$item_id));
		
		if($data['status']==0)
		{
			showErr($data['info']);
		}
		else
		{
			app_redirect($data['url']);
		}
	}
	
	
	/**
	 * 确认收货
	 */
	public function verify_delivery()
	{
		global_run();
		$item_id = intval($_REQUEST['item_id']);
		$data = call_api_core("uc_order","verify_delivery",array("item_id"=>$item_id));

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		$ids=array();
		foreach ($data['ids'] as $v){
		    $ids[]=$v;
		}
		$ids=implode(',',$ids);
		$json_parma = json_encode(array("data_id"=>$id));
		if($data['status']==1){
            $data['ids']=$ids;
		    $data['jump'] = wap_url("index","uc_order#order_done",array("id"=>$ids));
		}
		ajax_return($data);
	}
	
	public function verify_no_delivery()
	{
	    global_run();
	    $order_ids=json_decode($_REQUEST['order_ids']);
	    $data = call_api_core("uc_order","verify_no_delivery",array("order_ids"=>$order_ids));
	
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    $ids=implode(',', $order_ids);
	    
	    if($data['status']==1){
	        $data['jump'] = wap_url("index","uc_order#order_done",array("id"=>$ids));
	    }
	    ajax_return($data);
	}
	
	
	/**
	 * 拒绝收货
	 */
	public function refuse_delivery()
	{
		global_run();
		init_app_page();
		$item_id = intval($_REQUEST['item_id']);
		
		$data = call_api_core("uc_order","refuse_delivery",array("item_id"=>$item_id));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		$data['placeholder'] = "请输入详细原因";
		$data['action'] = wap_url("index","uc_order#do_refuse_delivery");
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("uc_order_message.html");
	}
	
	
	public function do_refuse_delivery()
	{
		global_run();
		$item_id = intval($_REQUEST['item_id']);
		$content =  strim($_REQUEST['content']);
		$data = call_api_core("uc_order","do_refuse_delivery",array("item_id"=>$item_id,"content"=>$content));
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['jump'] = wap_url("index","user#login");
		}
		else
		{
			if($data['status'])
			$data['jump'] = get_gopreview();
		}
		
		ajax_return($data);
	}
	
	/**
	 * 退款申请列表
	 **/
	public function refund_list()
	{
	    global_run();
		init_app_page();
		
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		
		$data = call_api_core("uc_order","refund_list",$param);
		// print_r($data);
		$GLOBALS['tmpl']->assign('data', $data);
		if(isset($data['page']) && is_array($data['page'])){
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
	    $GLOBALS['tmpl']->display("uc_refund_list.html");
	
	}
	
	/**
	 * 订单详情
	 **/
	public function view()
	{
	    global_run();
	    init_app_page();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("uc_order","wap_view",array("data_id"=>$data_id));
	    // echo "<pre>";print_r($data);exit;
	    if (!$data['item']) {
	    	// 订单不存在或被删除时
	    	$jump_url = wap_url('index', 'uc_order');
	    	$script = suiShow('订单不存在或已删除', $jump_url);
	    	$GLOBALS['tmpl']->assign('suijump', $script);
	    	$GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
	    } else {
	    	$data['item']['format_total_price']=format_price_html($data['item']['total_price'],1);
	    	$data['item']['format_pay_amount']=format_price_html($data['item']['pay_amount']);
	    	
	    	//判断是否为支付成功的跳转
	    	$url=$_SERVER['HTTP_REFERER'];
	    	$strlen = strlen($url);  //全部字符长度
	    	$tp = strpos($url,"ctl");  //limit之前的字符长度
	    	$param = array();
	    	if(substr($url,$tp,20)=="ctl=payment&act=done" 
	    	   || substr($url,$tp,27)=="ctl=uc_order&act=order_done"
	    	   || substr($url,$tp,25)=="ctl=uc_order&act=order_dp"
	    	   || substr($url,$tp,21)=="ctl=uc_order&act=view"
    	       || substr($url,$tp,16)=="ctl=cart&act=pay")
	    	{
	    		if ($data['item']['type'] == 5) {
	    			$param['tuan'] = 1;
	    		}
	    	    $GLOBALS['tmpl']->assign("back_url",wap_url("index","uc_order", $param));
	    	    
	    	    if($data['buy_type']==1){
	    	        $GLOBALS['tmpl']->assign("back_url",wap_url("index","user_center"));
	    	    }
	    	}
	    	
	    	if($data['buy_type']==1){
	    	    foreach ($data["item"]['operation'] as $k => $v){
	    	        if($v['icon']=="j-del" || $v['icon']=="j-cancel"){
	    	            $data["item"]['operation'][$k]['url'].=$v['url']."&pay_status=9";
	    	        }
	    	    }
	    	}
	    	
	    	foreach ($data['item']['deal_order_item'] as $kk=>$vv){
	    		foreach ($vv['list'] as $kkk=>$vvv){
	    			$data['item']['deal_order_item'][$kk]['list'][$kkk]['unit_price'] = format_price_html($vvv['unit_price'],2);
	    			$data['item']['deal_order_item'][$kk]['list'][$kkk]['discount_unit_price'] = format_price_html($vvv['discount_unit_price'],2);
	    			if($vvv['buy_type']==1){
	    				$data['item']['deal_order_item'][$kk]['list'][$kkk]['return_score'] = format_price_html($vvv['return_score'],2);
	    			}
	    		}
	    
	    
	    	}
		    $GLOBALS['tmpl']->assign("data",$data);
		    $GLOBALS['tmpl']->display("uc_view.html");
	    }
	}
	
	/**
	 * 物流跟踪
	 **/
	public function logistics()
	{
	    global_run();
	    init_app_page();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("uc_order","logistics",array("data_id"=>$data_id));
	    
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    if(!$data['delivery_count']>0){
	        app_redirect(wap_url("index","uc_order#view",array("data_id"=>$data_id)));
	    }
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
	    $GLOBALS['tmpl']->display("uc_logistics.html");
	
	}
	
	/**
	 * 交易完成
	 **/
	public function order_done()
	{
	    global_run();
	    init_app_page();
	    $ids = $_REQUEST['id'];
	    $data = call_api_core("uc_order","order_done",array("ids"=>$ids));

	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    if($data['order_status']==0 && $data['order_id']){
	        app_redirect(wap_url("index","uc_order#view",array("data_id"=>$data['order_id'])));
	    }
	    elseif ($data['order_status']==0){
	        app_redirect(wap_url("index","user_center"));   
	    }
	    
	    if(APP_INDEX=="app"){
	        $view_data=addslashes(json_encode(array("data_id"=>intval($data['order_id']))));
	        $dp_data=addslashes(json_encode(array("id"=>intval($data['order_id']))));
	        $view_url = 'javascript:App.app_detail(308,"'.$view_data.'");';
	        $dp_url   = 'javascript:App.app_detail(310,"'.$dp_data.'");';
	        
	    }
	    else{
	        $view_url = SITE_DOMAIN.wap_url("index","uc_order#view",array("data_id"=>$data['order_id']));
	        $dp_url   = SITE_DOMAIN.wap_url("index","uc_order#order_dp",array("id"=>$data['order_id']));
	    }
	    
	    $GLOBALS['tmpl']->assign("back_url",$view_url);
	    
	    $GLOBALS['tmpl']->assign("view_url",$view_url);
	    $GLOBALS['tmpl']->assign("dp_url",$dp_url);
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("item",$data['item']);
	    $GLOBALS['tmpl']->display("uc_order_done.html");
	
	}
	
	
	/**
	 * 订单评价
	 **/
	public function order_dp()
	{
	    global_run();
	    init_app_page();
	    $id =  intval($_REQUEST['id']);
	    $data = call_api_core("uc_order","order_dp",array("id"=>$id));
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	        //app_redirect(wap_url("index","user#login"));
	    }
	    if(!$data['order_id']){
	        app_redirect(wap_url("index","uc_order"));
	    }
	    if(!$data['item']){
	        app_redirect(wap_url("index","uc_order#view",array("data_id"=>$data['order_id'])));
	    }
	    //print_r($data);exit;
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("item_list",$data['item']);
	    $GLOBALS['tmpl']->display("uc_order_dp.html");
	
	}
	
	/**
	 * 订单评价提交
	 **/
	public function order_dp_do(){
	    global_run();
	    
	    $param['order_id'] =  intval($_REQUEST['order_id']);
	    $param["content"] = $_REQUEST['content'];
	    $param["item_id"] = $_REQUEST['item_id'];
	    $param['point'] = $_REQUEST['point'];
	    $data = call_api_core("uc_order","order_dp_do",$param);
	    
	    if ($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	        $data['status'] = 0;
	        $data['info'] = "请先登录";
	        $data['jump'] = wap_url("index","user#login");
	    }
	    else
	    {
	        if ($data['status'] == 1){
	            $data['jump'] = wap_url("index","uc_order#view",array("data_id"=>$param['order_id']));
	        }/* else{
	            $data['jump'] = wap_url("index","event#index",array("data_id"=>$event_id));
	        } */
	    }
	     
	    ajax_return($data);
	}
	
	
	/**
	 * 选择退款商品
	 **/
	public function order_refund()
	{
	    global_run();
	    init_app_page();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("uc_order","order_refund",array("data_id"=>$data_id));
	    //echo "<pre>";print_r($data);exit;
	    $data['item']['format_total_price']=format_price_html($data['item']['total_price'],1);
	    $data['item']['format_pay_amount']=format_price_html($data['item']['pay_amount']);
	    //echo "<pre>";print_r($data);exit;
	    foreach ($data['item']['deal_order_item'] as $kk=>$vv){
	        if($vv['count']==1){
	            foreach ($vv['list'] as $kkk=>$vvv){
	                $data['item']['deal_order_item'][$kk]['list'][$kkk]['unit_price'] = format_price_html($vvv['unit_price']);
	            }
	        }
	    }
	    
	    $deal_ids= implode(',',$data['deal_ids']);
	    $coupon_ids= implode(',',$data['coupon_ids']);
	    //print_r($data);exit;
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("data_coupon",$data['coupon_ids']);
	    $GLOBALS['tmpl']->assign("data_deal",$data['deal_ids']);
	    $GLOBALS['tmpl']->assign("deal_ids",$deal_ids);
	    $GLOBALS['tmpl']->assign("coupon_ids",$coupon_ids);
	    $GLOBALS['tmpl']->assign("deal_order_item",$data['item']['deal_order_item']);
	    $GLOBALS['tmpl']->display("uc_order_refund.html");
	
	}
	
	/**
	 * 退款详情
	 **/
	public function refund_view()
	{
	    global_run();
	    init_app_page();
	    
	    $param=array();
	    $param['page'] = intval($_REQUEST['page']);
	    $param['data_id'] = intval($_REQUEST['data_id']);
	    $param['did'] = intval($_REQUEST['did']);
	    $data = call_api_core("uc_order","refund_view",$param);
	    //print_r($data);exit;
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("item",$data['item']);
	    $GLOBALS['tmpl']->display("uc_refund_view.html");
	}
	
}
?>