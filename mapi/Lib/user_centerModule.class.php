<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class user_centerApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 会员中心首页接口
	 * 
	 * 	 输入:  
	 *  
	 *  输出:
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);	
	 * uid:int  	71 会员id
	 * user_name:string     fanwe  会员名
	 * user_money_format:string   ¥9973.2会员账户余额
  	 * user_avatar:string   http://localhost/o2onew/public/avatar/000/00/00/71virtual_avatar_big.jpg 会员头像图路径
  	 * user_score: int 会员积分
  	 * user_score_format:string 会员积分格式化
  	 * not_pay_order_count:int 未付款订单数
	 * wait_dp_count: int 待点评数量
	 * coupon_count: int  已发放给用户消费券数  
     * youhui_count: int   已发放给用户优惠券数 
     * not_pay_order_count:int  未付款的订单数 
	 */
	public function index()
	{
		$root = $this->baseData();
		return output($root);
	}

	// app 用的接口
	public function wap_index()
	{
		$root =  $this->baseData();

		// 初始化的信息
	    $orderManage = array(
	    	'uc_order' => isset($root['countOrder']) ? $root['countOrder'] : 0,
	    	'uc_order_tuan' => isset($root['countTuan']) ? $root['countTuan'] : 0,
	    	'dc_dcorder' => isset($root['countDc']) ? $root['countDc'] : 0,
	    	'dc_rsorder' => isset($root['countRs']) ? $root['countRs'] : 0,
	    );

	    $service = array(
	    	'uc_share' => $root['share_info'],
	    	'scores_index' => '签到领积分',
	    );

	    $imgBaseRoot = SITE_DOMAIN.APP_ROOT.'/mapi/image/user_center/';
		$mobileuccenter = require APP_ROOT_PATH."system/web_cfg/".APP_TYPE."/mobileuccenter_cfg.php";
		foreach($mobileuccenter['OrderManager']['menu'] as $k=> &$v){
			$v['url'] = SITE_DOMAIN.wap_url('index',$v['module'].'#'.$v['action'],$v['data']);
			$orderNumKey = $v['module'];
			if (array_key_exists('tuan', $v['data'])) {
				$orderNumKey .= '_tuan';
			}
			if (isset($orderManage[$orderNumKey]) && $v['action'] == 'index') {
				$v['num'] = $orderManage[$orderNumKey];
			}
			$imgKey = $orderNumKey;
			if ($v['action'] == 'refund_list') {
				$imgKey .= '_'.$v['action'];
			}
			$v['img'] = $imgBaseRoot.$imgKey.'.png';
		} unset($v);

		if($root['is_user_fx']){
			$mobileuccenter['fx_nav']['layout_type']=1;
			foreach($mobileuccenter['fx_nav']['menu'] as $k=>&$v){
				$v['url']=SITE_DOMAIN.wap_url('index',$v['module'].'#'.$v['action'],$v['data']);
				$v['img'] = $imgBaseRoot.$v['imgKey'].'.png';
			}unset($v);
		}else{
			$mobileuccenter['fx_nav']['layout_type']=2;
			foreach($mobileuccenter['fx_nav']['list'] as $k=>$v){
				$mobileuccenter['fx_nav']['list'][$k]['url']=SITE_DOMAIN.wap_url('index',$v['module'].'#'.$v['action'],$v['data']);
			}
		}

		foreach($mobileuccenter['Service']['list'] as $k => &$v){
			$v['url']=SITE_DOMAIN.wap_url('index',$v['module'].'#'.$v['action'],$v['data']);
			if (isset($service[$v['module']])) {
				$v['tip'] = $service[$v['module']];
			}
		}unset($v);

		foreach($mobileuccenter['Administration']['list'] as $k=>$v){
			$mobileuccenter['Administration']['list'][$k]['url']=SITE_DOMAIN.wap_url('index',$v['module'].'#'.$v['action'],$v['data']);
		}
		$root['mobileuccenter']=$mobileuccenter;

		return output($root);
	}

	private function baseData()
	{
		$root = array();	
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
		if($user_login_status == LOGIN_STATUS_LOGINED){			
		    $root['is_user_fx']=$user_data['is_fx'];
			$user_id = $user_data['id'];
			$root['uid'] = $user_id;
			$root['user_name'] = $user_data['user_name']?$user_data['user_name']:'';

			// 未读消息数量
			$msgType = '"account", "delivery", "confirm", "notify"';
			$not_read_msg_sql = 'SELECT count(id) FROM '.DB_PREFIX.'msg_box WHERE user_id='.$user_id.' AND is_read = 0 AND is_delete = 0 AND type IN('.$msgType.')';
			$not_read_msg = $GLOBALS['db']->getOne($not_read_msg_sql);
			$root['not_read_msg'] = $not_read_msg < 10 ? intval($not_read_msg) : '9+';

			// 会员组别
			$root['user_group'] = $GLOBALS['db']->getOne('SELECT name FROM '.DB_PREFIX.'user_group WHERE id = '.$user_data['group_id']);
	
			$root['user_money'] = round($user_data['money'],2);//用户金额
			$root['user_score'] = intval($user_data['score']+$user_data['frozen_score']);
			// $root['user_score_format'] = format_score($user_data['score']);
			$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"))?:'';
			
			//  手机绑定情况判断
			$root['user_mobile_empty'] = empty($user_data['mobile']) ? 1 : 0;

			// 未使用红包
			$ecv_sql='SELECT COUNT(id) FROM '.DB_PREFIX.'ecv WHERE user_id='.$user_id.' and use_count=0 and (end_time=0 or end_time>'.NOW_TIME.')';
			$new_ecv = $GLOBALS['db']->getOne($ecv_sql);
			$root['new_ecv'] = $new_ecv;
			
			// 未使用优惠券
			$youhuiSql = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_log AS yl INNER JOIN '.DB_PREFIX.'youhui as y ON yl.youhui_id = y.id WHERE yl.confirm_time=0 and y.id>0 and y.is_effect=1 and yl.user_id='.$user_id;
			$new_youhui = $GLOBALS['db']->getOne($youhuiSql);
			$root['new_youhui'] = $new_youhui;
			$root['youhui_count'] = $new_youhui + $new_ecv;
			
			// 未使用消费券
			$new_coupon = $GLOBALS['db']->getOne('SELECT COUNT(id) FROM '.DB_PREFIX.'deal_coupon WHERE user_id='.$user_id.' AND is_valid=1 AND is_delete=0 and confirm_time=0 and (end_time=0 or end_time>'.NOW_TIME.')');
			if(IS_OPEN_DISTRIBUTION==1){
			    $dist_count_sql="select count(dc.*) from ".DB_PREFIX."distribution_coupon as dc where dc.user_id=".$user_id." and dc.confirm_time=0 and dc.is_delete=0";
			    $dist_count=$GLOBALS['db']->getOne($dist_count_sql);
			    $new_coupon+=$dist_count;
			}
			$root['new_coupon'] = $new_coupon;
			$root['coupon_count'] = $new_coupon;
			
			require_once(APP_ROOT_PATH."system/model/deal_order.php");

			$order_table_name = get_user_order_table_name($user_id);

			// 统计未付款和未评价的订单数
			$countOrderSql = 'SELECT count(id) FROM '.$order_table_name.' WHERE user_id='.$user_id.' AND (pay_status <> 2 OR (pay_status = 2 AND order_process_status = 4)) AND is_delete = 0 AND type in (0, 3, 4, 6) AND is_main=0';
			$countOrder = $GLOBALS['db']->getOne($countOrderSql);
			$root['countOrder'] = $countOrder < 10 ? $countOrder : '9+';
			$countTuanSql = 'SELECT count(id) FROM '.$order_table_name.' WHERE user_id='.$user_id.' AND (pay_status <> 2 OR (pay_status = 2 AND order_process_status = 4)) AND is_delete = 0 AND type = 5 AND is_main=0';
			$countTuan = $GLOBALS['db']->getOne($countTuanSql);
			$root['countTuan'] = $countTuan < 10 ? $countTuan : '9+';

			$countDcSql = 'SELECT count(id) FROM '.DB_PREFIX.'dc_order WHERE user_id='.$user_id.' AND ((pay_status = 0 AND is_cancel = 0) OR (pay_status = 1 AND order_status = 2 AND is_dp = 0)) AND is_rs = 0';
			$countDc = $GLOBALS['db']->getOne($countDcSql);
			$root['countDc'] = $countDc < 10 ? $countDc : '9+';

			$countRsSql = 'SELECT count(id) FROM '.DB_PREFIX.'dc_order WHERE user_id='.$user_id.' AND ((pay_status = 0 AND is_cancel = 0) OR (pay_status = 1 AND is_sn_confirm <> 0 AND is_dp = 0)) AND is_rs = 1';
			$countRs = $GLOBALS['db']->getOne($countRsSql);
			$root['countRs'] = $countRs < 10 ? $countRs : '9+';
		}

		$share_value=app_conf('INVITE_REFERRALS');
        if(app_conf('INVITE_REFERRALS_TYPE')==1){
            $root['share_info']=$share_value."积分奖励";
        }else {
            $root['share_info']=$share_value."元现金奖励";
        }

		$root['page_title'] = "会员中心";
		$root['user_login_status'] = $user_login_status;
		$root['coupon_name'] = app_conf("COUPON_NAME");
		
		return $root;
	}

	
}
?>