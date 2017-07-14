<?php
// +----------------------------------------------------------------------
// | Fanwe 方维商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_moneyModule extends MainBaseModule
{
    /**
     * 资金记录
     */
    public function index(){
        global_run();
        init_app_page();
        $param['page'] = intval($_REQUEST['page']);			
		$data = call_api_core("uc_money","index",$param);	
			
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}	
  		if(isset($data['page']) && is_array($data['page'])){			
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象			
			$p  =  $page->show();					
			$GLOBALS['tmpl']->assign('pages',$p);
		}	
		set_gopreview();		
		
		$back_url =wap_url("index","user_center");
		$GLOBALS['tmpl']->assign("back_url",$back_url);

		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	            
        
        $GLOBALS['tmpl']->display("uc_money_index.html");
    }

		
	
	public function withdraw_bank_list(){
	    global_run();
	    init_app_page();
		$param=array();
		$param['page'] = intval($_REQUEST['page']);
		$data = call_api_core("uc_money","withdraw_bank_list",$param);				
		set_gopreview();
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		if(isset($data['page']) && is_array($data['page'])){
		    $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
		    $p  =  $page->show();
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
   		$GLOBALS['tmpl']->assign("data",$data);
   		$GLOBALS['tmpl']->assign("bank_list",$data['bank_list']);
	    $GLOBALS['tmpl']->display("uc_money_withdraw.html");
	}

	public function add_card(){
	    global_run();
	    init_app_page();
		$param=array();
        
		$data = call_api_core("uc_money","withdraw_bank_list",$param);			
		set_gopreview();		
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		$data['step']=2;
		$data['page_title']="添加银行卡";
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("data",$data);		      
	    $GLOBALS['tmpl']->display("uc_money_withdraw.html");
	}		  

	public function do_bind_bank(){
	    global_run();
		$param=array();
        $param['bank_name'] = strim($_REQUEST['bank_name']);
        $param['bank_account']= strim($_REQUEST['bank_account']);
        $param['bank_user'] = strim($_REQUEST['bank_user']);
        $param['sms_verify'] = strim($_REQUEST['sms_verify']);
        $param['bank_mobile'] = $_REQUEST['bank_mobile'];
		$data = call_api_core("uc_money","do_bind_bank",$param);				
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
 		if($data['status']==1){
			$result['status'] = 1;
			$result['url'] = wap_url("index","uc_money#withdraw_bank_list");
			ajax_return($result);			
		}else{
			$result['status'] =0;
			$result['info'] =$data['info'];					
			ajax_return($result);		
		}
	}		  


	public function do_withdraw(){
 		global_run();
		$param=array();
        $param['user_bank_id'] = intval($_REQUEST['bank_id']);
        $param['money']= floatval($_REQUEST['money']);
        $param['check_pwd'] = strim($_REQUEST['pwd']);

		$data = call_api_core("uc_money","do_withdraw",$param);	
        
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
 		if($data['status']==1){
			$result['status'] = 1;
			$result['url'] = wap_url("index","uc_money#withdraw_log");
			ajax_return($result);			
		}else{
			$result['status'] =0;
			$result['info'] =$data['info'];					
			ajax_return($result);		
		}		 	
	}		 
	public function withdraw_log(){
 		global_run();
 		init_app_page();
		$param=array();
		$param['page'] = intval($_REQUEST['page']);	
		$data = call_api_core("uc_money","withdraw_log",$param);				
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
 		if(isset($data['page']) && is_array($data['page'])){			
			$page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象			
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);	
		$GLOBALS['tmpl']->display("uc_withdraw_log.html");	 	
	}

	/**
	 * 获取用户添加过银行卡列表
	 * @return html 
	 */
	public function banklist()
    {
  		global_run();
  		init_app_page();
  		$data = call_api_core('uc_money', 'bank_list');
  		if ($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			login_is_app_jump();
  			//app_redirect(wap_url('index', 'user#login'));
  		}
  		
  		$step = 1;
  		$bank_list = $data['data'];
        //print_r($data);exit;
  		$GLOBALS['tmpl']->assign('step', 1);
  		$GLOBALS['tmpl']->assign('bank_list', $bank_list);
  		$GLOBALS['tmpl']->assign('data', $data);
  		$GLOBALS['tmpl']->display('uc_banklist.html');
    }

    /**
     * 删除银行卡信息 (支持批量删除)
     * @return json
     */
    public function del_bank()
    {
        $data = call_api_core('uc_money', 'bank_list');
    	$bank_id = strim($_REQUEST['id']);
    	if (empty($bank_id)) {
    		ajax_return(array('status' => 0, 'info' => '参数不能为空'));
    	}
    	$param['bank_id'] = $bank_id;
    	$delete = call_api_core('uc_money', 'del_bank', $param);
    	if ($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			login_is_app_jump();
  			//app_redirect(wap_url('index', 'user#login'));
  		}
  		
    	$result = array(
    		'status' => $delete['status'],
    		'info' => $delete['info'],
    	);
    	ajax_return($result);
    }		  

    /**
     * 资金明细
     * @return json
     */
    public function money_log(){
        global_run();
        init_app_page();
        
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("uc_money","money_log",$param);

        if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
            //app_redirect(wap_url("index","user#login"));
        }
        if(isset($data['page']) && is_array($data['page'])){
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            $p  =  $page->show();
            	
            $GLOBALS['tmpl']->assign('pages',$p);
        }
        
        $GLOBALS['tmpl']->assign('log', $data['item']);
        $GLOBALS['tmpl']->assign('data', $data);
        $GLOBALS['tmpl']->display('uc_money_log.html');
    }
}
?>
