<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_youhuiModule extends MainBaseModule
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

		require_once(APP_ROOT_PATH."app/Lib/page.php");
		//$page_size = app_conf("PAGE_SIZE");
		$page_size = 12;
		$page = intval($_REQUEST['p']);
		if($page==0){
			$page = 1;
		}

		$limit = (($page-1)*$page_size).",".$page_size;

		$user_id = $GLOBALS['user_info']['id'];
		//优惠券列表
		$order = " order by youhui_status, yl.id desc limit ".$limit;
		$sql = "SELECT yl.*, s. NAME AS supplier_name, y.start_use_price, y.supplier_id, y.city_id, y.youhui_type, y.youhui_value,
	CASE WHEN ( yl.expire_time <> 0 AND yl.expire_time < ".NOW_TIME." ) THEN 2 WHEN yl.confirm_time > 0 THEN 1 ELSE 0 END youhui_status
	FROM ".DB_PREFIX."youhui_log AS yl LEFT JOIN ".DB_PREFIX."youhui AS y ON yl.youhui_id = y.id LEFT JOIN ".DB_PREFIX."supplier AS s ON s.id = y.supplier_id WHERE y.is_effect=1 and yl.user_id = ".$user_id.$order;

		$list = $GLOBALS['db']->getAll($sql);
		$youhui_count = "SELECT count(*)
	FROM ".DB_PREFIX."youhui_log AS yl LEFT JOIN ".DB_PREFIX."youhui AS y ON yl.youhui_id = y.id WHERE yl.user_id = ".$user_id;

		$count = $GLOBALS['db']->getOne($youhui_count);
		$page = new Page($count,$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);

		$GLOBALS['tmpl']->assign("is_sms",app_conf("SMS_SEND_COUPON"));
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("NOW_TIME",NOW_TIME);
		$GLOBALS['tmpl']->assign("page_title","我的优惠券");
		assign_uc_nav_list();
		$GLOBALS['tmpl']->display("uc/uc_youhui_index.html");
	}

	//查看可用门店列表
	public function view_shop_list(){
		$supplier_id = intval($_REQUEST['supplier_id']);//商户id
		$youhui_id = intval($_REQUEST['youhui_id']);//券id
		$res = $GLOBALS['db']->getRow("select youhui_type,supplier_id from ".DB_PREFIX."youhui where id=".$youhui_id);

		if($res['youhui_type']==1 && $res['supplier_id']>0){
			//实体券
			$shop_list=$GLOBALS['db']->getAll("select sl.id,sl.name stroe_name,sl.address,sl.tel from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."youhui_location_link as yl on yl.location_id=sl.id where yl.youhui_id=".$youhui_id);
			foreach($shop_list as $k => $v){
				$shop_list[$k]['url'] = url("index","store#".$v['id']);
			}
			$data['shop_list'] = $shop_list;
			$data['is_online'] = 0;//线下店铺
		}elseif($res['youhui_type']==2 && $res['supplier_id']>0){
			//电子券
			$shop_list = $GLOBALS['db']->getAll("SELECT name,address FROM ".DB_PREFIX."supplier_location where supplier_id=".$supplier_id);
			foreach($shop_list as $k => $v){
				$shop_list[$k]['url'] = url("index","store#".$v['id']);
			}
			$data['shop_list'] = $shop_list;
			$data['is_online'] = 1;//线上店铺
		}
		ajax_return($data);
	}

	
	public function send()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		else
		{
			$t = strim($_REQUEST['t']);
			$id = intval($_REQUEST['id']);
			$youhui_log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_log where id = ".$id." and user_id = ".$GLOBALS['user_info']['id']);
			$youhui_info = load_auto_cache("youhui",array("id"=>$youhui_log['youhui_id']));
			if($youhui_log)
			{
				if($youhui_log['confirm_time']>0)
				{
					$data['status'] = 0;
					$data['info'] = "优惠券已使用";
					ajax_return($data);
				}
				elseif($youhui_log['expire_time']>0&&$youhui_log['expire_time']<NOW_TIME)
				{
					$data['status'] = 0;
					$data['info'] = "优惠券已过期";
					ajax_return($data);
				}
				else
				{
					if($t=="sms")
					{
						if(app_conf("SMS_ON")==0||$youhui_info['is_sms']==0)
						{
							$data['status'] = 0;
							$data['info'] = "不支持短信发送";
							ajax_return($data);
						}
						elseif($youhui_log['sms_count']>=app_conf("SMS_COUPON_LIMIT"))
						{
							$data['status'] = 0;
							$data['info'] = "短信发送已超过".app_conf("SMS_COUPON_LIMIT")."次";
							ajax_return($data);
						}
						elseif($GLOBALS['user_info']['mobile']=="")
						{
							$data['status'] = 0;
							$data['info'] = "请先设置手机号";
							$data['jump'] = url("index","uc_account");
							ajax_return($data);
						}
						else
						{
							send_youhui_log_sms($id);
							$GLOBALS['db']->query("update ".DB_PREFIX."youhui_log set sms_count = sms_count + 1 where id = ".$id);
							$data['status'] = 1;
							$data['info'] = "短信成功发送到".$GLOBALS['user_info']['mobile']."，请注意查收。";
							ajax_return($data);
						}
		
					}					
					else
					{
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "优惠券不存在";
				ajax_return($data);
			}
		}
	}
}
?>