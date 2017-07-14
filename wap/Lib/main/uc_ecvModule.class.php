<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_ecvModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		$param=array();
        $param['n_valid'] = intval($_REQUEST['n_valid']);
		$data = call_api_core("uc_ecv","wap_ecv",$param);
		$re = call_api_core("uc_ecv","exchange",array());
		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
		
		if(isset($data['page']) && is_array($data['page'])){
		    //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    //$page->parameter
		    $p  =  $page->show();
		    //print_r($p1);exit;
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
 		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->assign("list",$data['item']);
		$GLOBALS['tmpl']->assign("ecv_s",$re['data']);
		$GLOBALS['tmpl']->display("uc_ecv.html");
	}
	
	public function load_ecv_list(){
	    global_run();
	    init_app_page();
	    $n_valid = intval($_REQUEST['n_valid']);
	    $data = call_api_core("uc_ecv","load_ecv_list",array('page'=>intval($_REQUEST['page']),'n_valid'=>$n_valid));
	    
	    if($data['user_login_status']!=LOGIN_STATUS_NOLOGIN){
	        $result['status']=-1;
	        $result['jump'] = wap_url("index","user#login");
	    }
	    
	    $result['status'] =1;
	    if($data['page']['page']==$data['page']['page_total'])
	        $result['is_lock'] = 1;
	    if($data['data']){
	        $GLOBALS['tmpl']->assign("ecv_list",$data['data']);
	        $result['html'] = $GLOBALS['tmpl']->fetch("load_ecv_list.html");
	    }
	    ajax_return($result);
	 }
	 /**
	  * 兑换红包
	  */
	 public function exchange(){
	    global_run();		
		init_app_page();
        
		$data = call_api_core("uc_ecv","exchange",array());

	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
		    //app_redirect(wap_url("index","user#login"));
		}
        //print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->assign("ecv_list",$data['data']);
		$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","uc_ecv"));
		$GLOBALS['tmpl']->display("uc_ecv_exchange.html");
	 }
	 
	 public function do_snexchange(){
	     global_run();
	     init_app_page();
	     $sn = strim($_REQUEST['sn']);
	     if($sn==''){
	         $data['status'] = 0;
	         $data['info'] = '口令不能为空';
	         ajax_return($data);
	     }
	     $data = call_api_core("uc_ecv","do_snexchange",array('sn'=>$sn));
	     
	     if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	         $data['status'] = 0;
	         $data['jump'] = wap_url("index","user#login");
	     }
	     
	     ajax_return($data);
	 }
	 
	 public function do_exchange(){
	     global_run();
	     init_app_page();
	     $id = intval($_REQUEST['id']);
	     $data = call_api_core("uc_ecv","do_exchange",array('id'=>$id));
	     if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	         $data['status'] = 0;
	         $data['jump'] = wap_url("index","user#login");
	     }else {
	         if($data['status']==1){
	             $data['jump'] = wap_url("index","uc_ecv");
	         }
	     }
	 
	     ajax_return($data);
	 }
	 
	 public function load_ecv_exchange_list(){
	     global_run();
	     init_app_page();
	     $data = call_api_core("uc_ecv","load_ecv_exchange_list",array('page'=>intval($_REQUEST['page'])));
	      
	     if($data['user_login_status']!=LOGIN_STATUS_NOLOGIN){
	         $result['status']=-1;
	         $result['jump'] = wap_url("index","user#login");
	     }
	      
	     $result['status'] =1;
	     if($data['page']['page']==$data['page']['page_total'])
	         $result['is_lock'] = 1;
	     if($data['data']){
	         $GLOBALS['tmpl']->assign("ecv_list",$data['data']);
	         $result['html'] = $GLOBALS['tmpl']->fetch("load_ecv_exchange_list.html");
	     }
	     ajax_return($result);
	 }
}
?>