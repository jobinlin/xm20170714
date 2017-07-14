<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


require APP_ROOT_PATH.'app/Lib/page.php';
require APP_ROOT_PATH."system/model/uc_center_service.php";
class uc_voucherModule extends MainBaseModule
{
	public function index()
	{		
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}	
		
		$page = intval($_REQUEST['p']);
		if($page<=0)	$page = 1;

		$page_size = 9;
		$limit = (($page-1)*$page_size).",".$page_size;
		$user_id = intval($GLOBALS['user_info']['id']);
		$result = get_voucher_list($limit,$user_id);	
		$list = $result['list'];

		foreach ($list as $t => $v){
			if( $v['use_count']>0 ){
				$list[$t]['status']=1;
				$list[$t]['info']="已使用";
			}
			else if($v['end_time']<NOW_TIME && $v['end_time']!=0){
				$list[$t]['status']=0;
				$list[$t]['info']="已过期";
			}
			else{
				$list[$t]['status']=2;
				$list[$t]['info']="待使用";
			}

			$list[$t]['start_use_price']=intval($v['start_use_price']);
			$list[$t]['money']=intval($v['money']);
			$list[$t]['end_time'] = $v['end_time'] > 0 ? to_date($v['end_time'], 'Y-m-d H:i'):"永久有效";
		}

		$GLOBALS['tmpl']->assign("list",$list);
		$page = new Page($result['count'],$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_VOUCHER']);
		assign_uc_nav_list();//左侧导航菜单
		$GLOBALS['tmpl']->display("uc/uc_voucher_index.html");
		
	}

	public function exchange()
	{
		/*参数列表*/
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}			 
		/*业务逻辑*/
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);

		//分页
		$page = intval($_REQUEST['p']);
		if($page<=0)	$page = 1;

		//$page_size = PAGE_SIZE;
		$page_size = 9;
		$limit = (($page-1)*$page_size).",".$page_size;

		//查询可兑换的红包
		$result = get_exchange_voucher_list($limit,$user_id,$user['score']);
		$list = $result['list'];

		foreach($list as $k=>$v){
			$list[$k]['start_use_price'] =  intval($v['start_use_price']);
			$list[$k]['money'] =  intval($v['money']);
			$list[$k]['end_time'] =  to_date($v['end_time'],'Y-m-d H:i')?to_date($v['end_time'],'Y-m-d H:i'):"永久有效";
			$list[$k]['expire_day'] = $v['expire_day']?"领取之日起".$v['expire_day']."天可用":"永久有效";
		}

		$GLOBALS['tmpl']->assign("list",$list);
		$page = new Page($result['count'],$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
	
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_VOUCHER']);
		assign_uc_nav_list();//左侧导航菜单	
		$GLOBALS['tmpl']->display("uc/uc_voucher_exchange.html");
	}	
	
	public function do_exchange()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$result['status'] = 2;
			ajax_return($result);	
		}

		/*业务逻辑*/
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$id = intval($_REQUEST['id']);
		$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id = ".$id." and send_type=1");
		if(!$ecv_type)
		{
			showErr($GLOBALS['lang']['INVALID_VOUCHER'],1);
		}
		else
		{
			$exchange_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ecv where ecv_type_id = ".$id." and user_id = ".$user_id);
			if($ecv_type['exchange_limit']>0&&$exchange_count>=$ecv_type['exchange_limit'])
			{
				$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_LIMIT'],$ecv_type['exchange_limit']);
				showErr($msg,1);
			}
			elseif($ecv_type['exchange_score']>intval($GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$user_id)))
			{
				showErr($GLOBALS['lang']['INSUFFCIENT_SCORE'],1);
			}
			else
			{
				require_once(APP_ROOT_PATH."system/libs/voucher.php");
				$rs = send_voucher($ecv_type['id'],$user_id,1);
				if($rs>0)
				{
					require_once(APP_ROOT_PATH."system/model/user.php");
					$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_USE_SCORE'],$ecv_type['name'],$ecv_type['exchange_score']);
					modify_account(array('money'=>0,'score'=>"-".$ecv_type['exchange_score']),$user_id,$msg);
					showSuccess($GLOBALS['lang']['EXCHANGE_SUCCESS'],1,url('index','uc_voucher'));
				}
				else if($rs==-1)
				{
					showErr("您来晚了，已兑换光了",1);
				}
				else
				{
					showErr($GLOBALS['lang']['EXCHANGE_FAILED'],1);
				}
			}
		}
	}
	
	
	public function do_snexchange()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$result['status'] = 2;
			ajax_return($result);
		}
		$sn = strim($_REQUEST['sn']);
		$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where exchange_sn = '".$sn."'");
		if(!$ecv_type)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."ecv set user_id = '".$GLOBALS['user_info']['id']."' where sn = '".$sn."' and user_id = 0");
			if($GLOBALS['db']->affected_rows())
			{
				showSuccess($GLOBALS['lang']['EXCHANGE_SUCCESS'],1,url('index','uc_voucher'));
			}
			else
			showErr($GLOBALS['lang']['INVALID_VOUCHER'],1);
		}
		else
		{
			$exchange_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ecv where ecv_type_id = ".$ecv_type['id']." and user_id = ".intval($GLOBALS['user_info']['id']));
			if($ecv_type['exchange_limit']>0&&$exchange_count>=$ecv_type['exchange_limit'])
			{
				$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_LIMIT'],$ecv_type['exchange_limit']);
				showErr($msg,1);
			}
			else
			{
				require_once(APP_ROOT_PATH."system/libs/voucher.php");
				$rs = send_voucher($ecv_type['id'],$GLOBALS['user_info']['id'],1);
				if($rs>0)
				{
					showSuccess($GLOBALS['lang']['EXCHANGE_SUCCESS'],1,url('index','uc_voucher'));
				}
				else if($rs==-1)
				{
					showErr("您来晚了，已兑换光了",1);
				}
				else
				{
					showErr($GLOBALS['lang']['EXCHANGE_FAILED'],1);
				}
			}
		}
	}
	
}
?>