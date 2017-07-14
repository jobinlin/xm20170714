<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class ajaxModule extends MainBaseModule
{

	public function send_sms_code()
	{
		$param = array(
			'mobile' => strim($_REQUEST['mobile']),
			'unique' => intval($_REQUEST['unique']),
			'verify_code' => strim($_REQUEST['verify_code']),
		);
		if (isset($_REQUEST['account'])) {
			$param['account'] = strim($_REQUEST['verify_code']);
		}
		if (isset($_REQUEST['biz'])) {
			$param['biz'] = intval($_REQUEST['biz']);
		}
		if(isset($_REQUEST['dist'])){
            $param['dist'] = intval($_REQUEST['dist']);
        }
		$data = call_api_core("sms", "send_sms_code", $param);
		
		ajax_return($data);
	}
	
	public function send_fxsms_code()
	{
		global_run();
	
		$mobile = $GLOBALS['user_info']['mobile'];
		$unique = intval($_REQUEST['unique']);
		$verify_code = strim($_REQUEST['verify_code']);
		if($mobile==""){
			$data['status']=0;
			$data['info']="请完善会员手机号";
			ajax_return($data);
		}
		$data = call_api_core("sms","send_sms_code",array("mobile"=>$mobile,"unique"=>$unique,"verify_code"=>$verify_code));
		ajax_return($data);
	}
	
	public function close_appdown()
	{
		es_cookie::set('is_app_down',1,3600*24*7);
	}
	
	
	public function count_buy_total()
	{
		$delivery_id =  intval($_REQUEST['delivery_id']); //配送方式
		$ecvsn = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
		$ecvpassword = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
		$payment = intval($_REQUEST['payment']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		$all_score = intval($_REQUEST['all_score']);
		$reward=intval($_REQUEST['reward']);
		$address_id =  intval($_REQUEST['address_id']); //配送方式
		$id =  intval($_REQUEST['id']); //购物方式
		$buy_type =  intval($_REQUEST['buy_type']); //购物方式
		$youhui = $_REQUEST['youhui_ids'];

		$data = call_api_core("cart","count_buy_total",array("youhui_ids"=>$youhui,"delivery_id"=>$delivery_id,"buy_type"=>$buy_type,"address_id"=>$address_id,"id"=>$id,"ecvsn"=>$ecvsn,"ecvpassword"=>$ecvpassword,"payment"=>$payment,"all_account_money"=>$all_account_money,"all_score"=>$all_score));

		
        //if($reward==1){
		    $GLOBALS['tmpl']->assign("buy_type",$buy_type);
    		$GLOBALS['tmpl']->assign("feeinfo",$data['feeinfo']);
    		$GLOBALS['tmpl']->assign("paid",$data['paid']);
    		$GLOBALS['tmpl']->assign("promote",$data['promote']);
			$GLOBALS['tmpl']->assign("all_score",$all_score);
			$GLOBALS['tmpl']->assign("score_purchase",$data['score_purchase']);
    		$GLOBALS['tmpl']->assign("total_promote_price",$data['total_promote_price']);
    		$GLOBALS['tmpl']->assign("pay_price",round($data['pay_price'],2));
    		$GLOBALS['tmpl']->assign("total_price",round($data['total_price'],2));

    		$ajaxdata['ecv_no_use_status']=$data['ecv_no_use_status'];
    		$ajaxdata['free'] = $data['free'];
    		$ajaxdata['html'] = $GLOBALS['tmpl']->fetch("style5.2/inc/page/cart_total.html");
    		$ajaxdata['pay_price_html'] = format_price_txt(round($data['pay_price'],2));
    		$ajaxdata['total_price'] = round($data['total_price'],2);
    		ajax_return($ajaxdata);
        /* }else {
            ajax_return(round($result['pay_price'],2));
        } */
	}
	
	public function count_order_total()
	{
		$order_id = intval($_REQUEST['id']);
		$delivery_id =  intval($_REQUEST['delivery_id']); //配送方式
		$address_id =  intval($_REQUEST['address_id']); //配送方式
		$payment = intval($_REQUEST['payment']);
		$all_account_money = intval($_REQUEST['all_account_money']);
	
		$data = call_api_core("cart","count_order_total",array("id"=>$order_id,"delivery_id"=>$delivery_id,"address_id"=>$address_id,"payment"=>$payment,"all_account_money"=>$all_account_money));
        $result = $data['result'];
		//logger::write(print_r($result,1));
        $ajaxdata['all_account_money'] =0;
		if($result['account_money'] >0){
		    $ajaxdata['all_account_money'] = 1;
		}
		
		$ajaxdata['payment']=0;
        if($result['payment_info']){
            $ajaxdata['payment'] = $result['payment_info']['id'];
        }
        
        $ajaxdata['payment_fee'] = $result['payment_fee'];
        $ajaxdata['payment_fee_html'] = format_price_html($result['payment_fee']);
        $ajaxdata['pay_price'] = $result['pay_price'];
		ajax_return($ajaxdata);
	}
	
	public function focus(){
	    global_run();
	    $param=array();
	    $param['uid'] = intval($_REQUEST['uid']);
	     
	    $data = call_api_core("uc_home","focus",$param);
	     
	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	        $data['info'] = "请先登录后操作";
	        $data['jump'] = wap_url("index","user#login");
	    }
	     
	    ajax_return($data);
	}

    public function order_shere(){
	    $id = intval($_REQUEST['id']);
	    require_once(APP_ROOT_PATH.'system/model/topic.php');
        order_share($id);

        $result['info'] = $GLOBALS['lang']['MESSAGE_POST_SUCCESS'];
        $result['status'] = 1;
        ajax_return($result);
        
	}
    function get_wx_app_userinfo()
    {
        global_run();
		//$weixin_conf = load_auto_cache("weixin_conf");
		if($_REQUEST['type']){
			$wx_info=es_session::get('user_wx_info');//$GLOBALS['user_wx_info'];
		}else{
			//$weixin_conf = load_auto_cache("weixin_conf");
			$wx_info = json_decode(trim($_REQUEST['param']),1);
		}
		//logger::write(print_r($GLOBALS['user_info'],1));
		if(empty($GLOBALS['user_info']) && es_cookie::get("user_login_id")>0){
            $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".es_cookie::get("user_login_id"));
            //如果会员存在，直接登录
           auto_do_login_user($user_info['user_name'],$user_info['user_pwd'],false);
        }
		
		if($GLOBALS['user_info']){
			$is_login = wx_info_bind($wx_info,1);
		}else{
			es_session::delete("user_wx_info");
			ajax_return(array("err_code"=>1,'err'=>"未登录"));
		}
		
			

		if($is_login)
		{
			if($is_login['status']==1){
				$url = wap_url("index","uc_account");//get_gopreview();
				// 	        		$url = preg_replace("/[&|?]show_prog=[^&]*/i", "", $url);
				$user_info = es_session::get('user_info');
				
				es_cookie::set("user_name",$user_info['user_name'],3600*24*30);
				es_cookie::set("user_pwd",md5($user_info['user_pwd']."_EASE_COOKIE"),3600*24*30);
				es_session::delete("user_wx_info");	
				ajax_return(array("err_code"=>0,"jump"=>$url));
			}else{
				
				ajax_return(array("err_code"=>2,"err"=>$is_login['info']));
			}
		}
		else
		{
			ajax_return(array("err_code"=>2,'err'=>"绑定失败"));
		}


    }
    /**
	 * 解绑微信
	 */
	function wx_unbind () {
	    global_run();
	    
	    $data = call_api_core("uc_account","wx_unbind");
	    $data['jump'] = wap_url('index', 'uc_account#index');
	    ajax_return($data);
	}
	 /**
	 * 删除已操作的保留值
	 */
	function del_is_weixin_bind () {
		es_session::delete("is_weixin_bind");
	    ajax_return(array());
	}
	/**
	 * 判断微信是否是会员
	 */
	function is_user() {
		global_run();
		
		if($_REQUEST['type']){
			$wx_info=es_session::get('user_wx_info');//$GLOBALS['user_wx_info'];
		}else{
			//$weixin_conf = load_auto_cache("weixin_conf");
			$wx_info = json_decode(trim($_REQUEST['param']),1);
		}
		
		$unionid=$wx_info['unionid'];
		$openid=$wx_info['openid'];
		
		if(!$openid)
		{
			es_session::delete("user_wx_info");
			ajax_return(array("err_code"=>"微信的唯一ID未传递","err"=>1));
		}
		if($unionid)
		{
			if(!isApp())
				$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where wx_openid='".$openid."' or union_id = '".$unionid."' order by id desc limit 1");
			else
				$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where m_openid='".$openid."' or union_id = '".$unionid."' order by id desc limit 1");
		}
		else
		{
			if(!isApp())
				$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where wx_openid='".$openid."' order by id desc limit 1");
			else
				$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where m_openid='".$openid."' order by id desc limit 1");
		}
		$is_mobile=0;
		if($user_data){
			if($GLOBALS['user_info']['bind_count']>10)
			{
				es_session::delete("user_wx_info");
				ajax_return(array("err_code"=>"该账户合并会员次数到达上限","err"=>1));
			}
			if($user_data['mobile']){
				es_session::delete("user_wx_info");
				$err=1;
				$err_code='该微信已被使用';
			}
				
			$is_user=1;
			
		}else{
			$is_user=0;
			$err_code='会员不存在';
		};
		
		ajax_return(array("is_user"=>$is_user,"err_code"=>$err_code,"err"=>$err,"jump"=>wap_url("index","uc_account")));
	}
    public function load_page()
    {
        $url = strim($_REQUEST['url']);
        $GLOBALS['tmpl']->assign("ajax_url",$url);
        $data['html'] = $GLOBALS['tmpl']->fetch("style5.2/inc/module/ajax_page.html");
        ajax_return($data);
    }

    /**
     * 获取推荐商品
     */
    public function get_recommend_list(){
        $data = call_api_core("cart","get_recommend_list");
        $GLOBALS['tmpl']->assign("data",$data);
        echo $GLOBALS['tmpl']->fetch("style5.2/inc/module/ajax_cart_recommend_list.html");
        exit;
    }
    
}
?>