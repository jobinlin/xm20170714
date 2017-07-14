<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class eventModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();		

		$param['data_id'] = intval($_REQUEST['data_id']); //分类ID

		$request = $param;
		//获取品牌
		$data = call_api_core("event","index",$param);

		if($data['user_login_status']!=LOGIN_STATUS_NOLOGIN){
		    $data['is_login'] = 1;
		}
		
		if(intval($data['id'])==0)
		{
		    //app_redirect(wap_url("index"));
		   // $jump_url = wap_url('index', 'events');
		    $script = suiShow('活动不存在或已删除', $jump_url);
		    $GLOBALS['tmpl']->assign('suijump', $script);
		    $GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
		}else{

            
    		//获取星级
    		$data['event_info']['avg_star']=($data['event_info']['avg_point']/5)*100;
    		foreach ($data['dp_list'] as $t => $v){
    		    $star=($v['point']/5)*100;
    		    $data['dp_list'][$t]['point_star']=$star;
    		}
    		
    		//报名进度
    		$data['event_info']['sub_pre']=$data['event_info']['submit_count']/$data['event_info']['total_count']*100;
    		
    		$GLOBALS['tmpl']->assign("event",$data['event_info']);
    		$GLOBALS['tmpl']->assign("data",$data);	
    		$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","event"));
    		$GLOBALS['tmpl']->display("event.html");
		}
	}
	
	/*
	 * 领取优惠券
	 * */
	public function load_event_submit(){
	    global_run();
	    init_app_page();
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("event","load_event_submit",array("data_id"=>$data_id));
	    if ($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			login_is_app_jump();
	    	//app_redirect(wap_url("index","user#login"));
	    }
	    
	    if($data['status']==0){
	        showErr($data['info'],0,wap_url("index","event#index",array("data_id"=>$data_id)));
	    }
	    
	    $GLOBALS['tmpl']->assign("event_id",$data_id);
		$GLOBALS['tmpl']->assign("event_fields",$data['event_fields']);	
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","event"));
		$GLOBALS['tmpl']->display("event_submit.html");
	}
	
	public function do_submit(){
	   
	    global_run();
        /*获取参数*/
	    $event_id = intval($_REQUEST['event_id']);
	    $param=array();
	    $param['event_id'] = $event_id;
	    $param['result'] = $_REQUEST['result'];
	    $param['field_id'] = $_REQUEST['field_id'];
	    
	    $data = call_api_core("event","do_submit",$param);
		if ($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			$data['status'] = 0;
			$data['info'] = "请先登录";
	        $data['jump'] = wap_url("index","user#login");
	    }
	    else
	    {
	        if ($data['status'] == 1){
	            if($data["sn"]){
	                $data['jump'] = wap_url("index","uc_event#index");
	            }else{
	                $data['jump'] = wap_url("index","event#index",array("data_id"=>$event_id));
	            }
	        }else{
	            $data['jump'] = wap_url("index","event#index",array("data_id"=>$event_id));
	        }
	    }
	  
	    ajax_return($data);
	    
	}
	
	public function detail()
	{
	    global_run();
	    init_app_page();
	
	    $data_id = intval($_REQUEST['data_id']);
	
	    $data = call_api_core("event","detail",array("data_id"=>$data_id));
	
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("event_info",$data['event_info']);
	    $GLOBALS['tmpl']->display("event_detail.html");
	}
	
	public function add_collect(){
	    global_run();
	    init_app_page();
	     
	
	    $param=array();
	    $param['id'] = intval($_REQUEST['id']);
	    $data = call_api_core("event","add_collect",$param);
	    
	    ajax_return($data);
	}
	
	public function del_collect(){
	    global_run();
	    init_app_page();
	
	
	    $param=array();
	    $param['id'] = intval($_REQUEST['id']);
	    $data = call_api_core("event","del_collect",$param);
	    ajax_return($data);
	}
	
	public function event_submit() {
	    global_run();
	    init_app_page();
	    
	    $param=array();
	    $param['data_id'] = intval($_REQUEST['data_id']);
	    $data = call_api_core("event","event_submit",$param);
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("event_id",$param['data_id']);
	    $GLOBALS['tmpl']->display("event_submit.html");
	}
	
	/**
	 * A-9-5 全部评论页
	 * @return
	 */
	public function reviews(){
	    global_run();
	    init_app_page();
	    $param['data_id'] = intval($_REQUEST['data_id']);
	    $param['page'] = abs(intval($_REQUEST['page']));
	
	    $data = call_api_core("event","reviews",$param);
	
	    if(isset($data['page']) && is_array($data['page'])){
	        $page = new Page($data['page']['data_total'],$data['page']['page_size']);
	        $p  =  $page->show();
	        $GLOBALS['tmpl']->assign('pages',$p);
	    }
	    $GLOBALS['tmpl']->assign('data', $data);
	    $GLOBALS['tmpl']->display("store_reviews.html");
	}
}
?>