<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class dist_withdrawalModule extends MainBaseModule
{

	public function index(){
        dist_global_run();
        dist_init_app_page();
	    $param['page'] = intval($_REQUEST['page']);
	    $data = call_api_core("biz_withdrawal","index",$param);
	    

	    if ($data['dist_user_status']==0){ //用户未登录
	        app_redirect(wap_url("dist","user#login"));
	    }
	    
	    if(isset($data['page']) && is_array($data['page'])){
	        //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
	        $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
	        $p  =  $page->show();
	        $GLOBALS['tmpl']->assign('pages',$p);
	    }
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("ajax_url",wap_url("biz","withdrawal"));
	    $GLOBALS['tmpl']->display("biz_withdrawal.html");
    }
    
    public function submit_form(){
        dist_global_run();
        dist_init_app_page();

        $data = call_api_core("dist_withdrawal","submit_form");

        if ($data['dist_user_status']==0){ //用户未登录
            app_redirect(wap_url("dist","user#login"));
        }
        
//         if ($data['is_auth']==0){ //没有操作权限
//             app_redirect(wap_url("biz","shop_verify"));
//         }
        
        if($data['mobile']==""){ //未绑定手机
            app_redirect(wap_url("dist","shop_verify"));
        }
        
        //$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
        //print_r($data);exit;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("dist_info",$data['dist_info']);
        $GLOBALS['tmpl']->assign("ajax_url",wap_url("dist","withdrawal"));
        $GLOBALS['tmpl']->display("dist_withdrawal_form.html");
    }
    
    public function do_submit(){
        dist_global_run();
        dist_init_app_page();
        $param = array();
        $param['money'] = floatval($_REQUEST['money']);
        $param['bank_name'] = strim($_REQUEST['bank_name']);//开户行
        $param['bank_card'] = strim($_REQUEST['bank_info']);//银行卡帐号
        $param['bank_user'] = strim($_REQUEST['bank_user']);//持卡人姓名
        $param['sms_verify'] = strim($_REQUEST['sms_verify']);
        $param['pwd_verify'] = strim($_REQUEST['pwd_verify']);

        $data = call_api_core("dist_withdrawal","do_submit",$param);
         
        
        if ($data['dist_user_status']==0){ //用户未登录
            $data['jump'] = wap_url("dist","user#login");
        }
        
//         if ($data['is_auth']==0){ //没有操作权限
//             $data['jump'] = wap_url("biz","shop_verify");
//         }
        
        if($data['status']==1){
            $data['jump'] = wap_url("dist","withdrawal#withdraw_log");
        }
        
        ajax_return($data);
        
    }
    
    /**
     * 银行卡绑定
     */
    public function bindbank(){
        dist_global_run();
        dist_init_app_page();
        
        $data = call_api_core("dist_withdrawal","bindbank");
        
        if ($data['dist_user_status']==0){ //用户未登录
            app_redirect(wap_url("dist","user#login"));
        }
        
//         if ($data['is_auth']==0){ //没有操作权限
//             app_redirect(wap_url("biz","shop_verify"));
//         }
        
        if($data['mobile']==""){ //未绑定手机
            app_redirect(wap_url("dist","shop_verify"));
        }
        
        $GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("ajax_url",wap_url("dist","withdrawal"));
        $GLOBALS['tmpl']->display("dist_withdrawal_bindbank.html");
    }
    
    public function do_bindbank(){
        dist_global_run();
        dist_init_app_page();
        
        $param = array();
        $param['bank_name'] = strim($_REQUEST['bank_name']);
        $param['bank_num'] = str_replace(' ','',strim($_REQUEST['bank_account'])) ;
        $param['bank_user'] = strim($_REQUEST['bank_user']);
        
        $param['sms_verify'] = strim($_REQUEST['sms_verify']);
        $param['pwd_verify'] = strim($_REQUEST['pwd_verify']);
        
        $data = call_api_core("dist_withdrawal","do_bindbank",$param);
         
        
        if ($data['dist_user_status']==0){ //用户未登录
            $data['jump'] = wap_url("dist","user#login");
        }
        
//         if ($data['is_auth']==0){ //没有操作权限
//             $data['jump'] = wap_url("biz","shop_verify");
//         }
        
        if($data['status'] == 1){
            $data['jump']  = wap_url("dist","money_index#index");
        }
        
        ajax_return($data);
    }
    
    /**
     * 提现明细
     */
    public function withdraw_log(){
        dist_global_run();
        dist_init_app_page();
        $param=array();
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("dist_withdrawal","withdraw_log",$param);
//         if(isset($data['page']) && is_array($data['page'])){
//             $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
//             $p  =  $page->show();
//             $GLOBALS['tmpl']->assign('pages',$p);
//         }
        $this->_pageFormat($data['page']);
        $url=$_SERVER['HTTP_REFERER'];
        $strlen = strlen($url);  //全部字符长度
        $tp = strpos($url,"ctl");  //limit之前的字符长度
        if(substr($url,$tp,30)=="ctl=withdrawal&act=submit_form"){
            //print_r(substr($url,$tp,30));exit;
            $GLOBALS['tmpl']->assign("back_url",wap_url("dist","money_index#index"));
        }
        //print_r($data);exit;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_withdraw_log.html");
    }
    
    /**
     * 资金明细
     */
    public function money_log(){
        dist_global_run();
        dist_init_app_page();
        $param=array();
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("dist_withdrawal","money_log",$param);
        if ($data['dist_user_status']==0){ //用户未登录
            app_redirect(wap_url("dist","user#login"));
        }
//         if(isset($data['page']) && is_array($data['page'])){
//             $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
//             $p  =  $page->show();
//             $GLOBALS['tmpl']->assign('pages',$p);
//         }
        $this->_pageFormat($data['page']);
        //print_r($data);exit;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("dist_money_log.html");
    }
}
?>

