<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_fx_inviteModule extends MainBaseModule
{

	/**
	 * 查看推荐下线
	 */
	public function index()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
		init_app_page();
		$user_info = $GLOBALS['user_info'];
		if($user_info['is_fx']==0){
		    app_redirect(url("index","uc_fx#vip_buy"));
		}
		if($user_info['pid']==0){
			$inviter="无推荐人";
		}else{
			$inviter = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$user_info['pid']);
		}
		$GLOBALS['tmpl']->assign("inviter",$inviter);
		
		
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)	$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$user_id=$user_info['id'];
		$user_temid=intval($_REQUEST['user_id']);
		if($user_temid>0){
			$pid =$GLOBALS['db']->getOne("select pid from ".DB_PREFIX."user where id=".$user_temid);
			if($pid==$user_info['id']){
				$user_id=$user_temid;
			}
		}
		
		$share_url = get_domain().APP_ROOT."/";
		if($GLOBALS['user_info']){
		    $share_url .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
		}
		$GLOBALS['tmpl']->assign("share_url",$share_url);
		
		$result =$GLOBALS['db']->getAll("select u.id,u.user_name,fxr.money from ".DB_PREFIX."user as u left join ".DB_PREFIX."fx_user_reward as fxr on fxr.pid=".$user_info['id']." and fxr.user_id=u.id where u.pid =".$user_id." order by u.create_time desc limit ".$limit);
		$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where pid = ".$user_id);
		$GLOBALS['tmpl']->assign("list",$result);
		$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		//通用模版参数定义
		assign_uc_nav_list();//左侧导航菜单
		$GLOBALS['tmpl']->assign("user_temid",$user_temid);//无分类下拉
		$GLOBALS['tmpl']->assign("no_nav",true);//无分类下拉
		$GLOBALS['tmpl']->assign("page_title","我的推荐"); //title
		$GLOBALS['tmpl']->display("uc/uc_fx_invite.html");
	} 
	
	public function moneylog()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
		init_app_page();
		$user_info = $GLOBALS['user_info'];		
		if($user_info['is_fx']==0){
		    app_redirect(url("index","uc_fx#vip_buy"));
		}
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)	$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");		
		
		$result =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_info['id']." order by create_time desc limit ".$limit);
		$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_info['id']);
		
		$GLOBALS['tmpl']->assign("list",$result);
		$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		//通用模版参数定义
		assign_uc_nav_list();//左侧导航菜单
		
		$GLOBALS['tmpl']->assign("no_nav",true);//无分类下拉
		$GLOBALS['tmpl']->assign("page_title","我的分销资金日志"); //title		
		
		$GLOBALS['tmpl']->display("uc/uc_fx_moneylog.html");
	}
	
	

    
  
}
?>