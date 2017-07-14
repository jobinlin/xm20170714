<?php 
/**
 * 
 * 商家外卖
 * 
 */
require_once(APP_ROOT_PATH."system/model/dc.php");
class biz_dc_orderModule extends MainBaseModule
{
    


	/**
	 * 	商家外卖新订单
	 */  
	
    
	public function index()
	{	

		
		global_run();
		init_app_page();
		$param['lid'] = intval($_REQUEST['lid']);
		$param['page']=intval($_REQUEST['page']);
		$param['sort'] = intval($_REQUEST['sort']);
		$sort=intval($_REQUEST['sort']);
		$data = call_api_core("biz_dc_order","biz_wap_index",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{
			
			if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){	
// 				showErr($data['info'],0,wap_url("index","dc_biz"));
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
			
			//print_r($data);exit;
			
			if($data['status']==0){
			    $jump_url = wap_url('biz', 'shop_verify');
			    $script = suiShow($data['info'], $jump_url);
			    $GLOBALS['tmpl']->assign('suijump', $script);
			    $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
			}
			
			$GLOBALS['tmpl']->assign("sort",$sort);
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->display("dc/biz/biz_dc_order.html");
		}
	}
	
    public function abnormal_order(){
        global_run();
        init_app_page();
        $param['page']=intval($_REQUEST['page']);
        $data = call_api_core("biz_dc_order","abnormal_order",$param);
        
        if ($data['biz_user_status']==0){ //用户未登录
            showErr('商户未登录',0,wap_url("biz","user#login"));
        }else{
            
            if($data['is_dc_delivery'] == 0){
                showErr('没有管理权限',0,wap_url("biz","manage"));
            }
            	
            if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
                // 				showErr($data['info'],0,wap_url("index","dc_biz"));
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
            	
            //print_r($data);exit;
            	
            if($data['status']==0){
                $jump_url = wap_url('biz', 'shop_verify');
                $script = suiShow($data['info'], $jump_url);
                $GLOBALS['tmpl']->assign('suijump', $script);
                $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
            }
            	
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("dc/biz/biz_dc_abnormal_order.html");
        }
    }
	
	/**
	 * 	商家外卖订单记录
	 */
	
	
	public function order()
	{
	
		
		global_run();
		init_app_page();
		$param['lid'] = intval($_REQUEST['lid']);
		$param['page']=intval($_REQUEST['page']);
		$param['date']=strim($_REQUEST['date']);
		$data = call_api_core("biz_dc_order","order",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{
			
			if($data['status']==0){
				showErr($data['info'],0,wap_url("index","dc_biz"));
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
			$GLOBALS['tmpl']->display("dc/biz/biz_dc_resorder.html");
		}
	}
	
	/**
	 * 	商家外卖催单记录   
	 */ 
	
	public function dc_reminder(){
		
		global_run();
		init_app_page();
		$param['lid'] = intval($_REQUEST['lid']);
		$param['page']=intval($_REQUEST['page']);
		$data = call_api_core("biz_dc_order","dc_reminder",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{
			if($data['status']==0){
				showErr($data['info'],0,wap_url("index","dc_biz"));
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
			$GLOBALS['tmpl']->display("dc/biz/biz_dcorder_reminer.html");
		}
	}
	
	
	
	/**
	 * 	商家外卖接单接口
	 */  
	
	public function accept_order()
	{	
		

		global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['id']);
		$param['type'] = intval($_REQUEST['type']);
		//$param['lid'] = intval($_REQUEST['lid']);
		$data = call_api_core("biz_dc_order","accept_order",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{
			
			if($data['time_status']){
				
				$result['time_status']=$data['time_status'];
			}
			$result['status']=$data['status'];
			$result['info']=$data['info'];
			ajax_return($result);
		}
}
	
	/**
	 * 	商家外卖关闭订单接口
	 */
	
	public function close_order()
	{

	
		global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['id']);
		$param['close_reason'] = strim($_REQUEST['close_reason']);
		$data = call_api_core("biz_dc_order","close_order",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{

			$result['status']=$data['status'];
			$result['info']=$data['info'];
			ajax_return($result);
		}
		
	}
	
	/**
	 * 	商家外卖 确认订单接口
	 */ 
	
	public function over_order()
	{	
		global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_dc_order","over_order",$param);
		
		if ($data['biz_user_status']==0){ //用户未登录
			showErr('商户未登录',0,wap_url("biz","user#login"));
		}else{
		
			$result['status']=$data['status'];
			$result['info']=$data['info'];
			ajax_return($result);
		}
		
	}
	

	/**
	 * 	外卖订单详情
	 */
	
	public function view(){
	
	    global_run();
	    init_app_page();
	    $param['lid'] = intval($_REQUEST['lid']);
	    $param['data_id'] = intval($_REQUEST['data_id']);
	    $param['page']=intval($_REQUEST['page']);
	    $data = call_api_core("biz_dc_order","view",$param);
	
	    if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){	
			    app_redirect(wap_url("biz","user#login"));
		}else{
	        if($data['status']==0){
	            showErr($data['info'],0,wap_url("biz","user#login"));
	        }
	        //print_r($data);exit;
	        $GLOBALS['tmpl']->assign("dc_order",$data['dc_order']);
	        $GLOBALS['tmpl']->assign("data",$data);
	        $GLOBALS['tmpl']->display("dc/biz/biz_dc_view.html");
	    }
	}
	
	public function change_delivery()
	{	
	    global_run();
	    init_app_page();
	    
	    $param['id'] = intval($_REQUEST['id']);
	
	    $data = call_api_core("biz_dc_order","change_delivery",$param);
	    
	    if ($data['biz_user_status']==0){ //用户未登录
	        showErr('商户未登录',0,wap_url("biz","user#login"));
	    }else{

	        ajax_return($data);
	    }
	}

}
?>