<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


require_once(APP_ROOT_PATH.'system/model/user.php');
class uc_fx_withdrawModule extends MainBaseModule
{

	/**
	 * 提现
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
		
		//可提现金额
		$fx_money=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id=".$user_info['id']." and is_paid=0 and is_delete=0");
		$money=$user_info['fx_money']-$fx_money;
		//echo $money;exit;
		$result=$this->get_fx_withdraw_money();
		$money=$money-$result;
	
		$GLOBALS['tmpl']->assign("withdraw_money",round($money,2));

		
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		
		
		
		require_once(APP_ROOT_PATH."system/model/fx.php");
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)	$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$result = get_fx_withdraw($limit,$GLOBALS['user_info']['id']);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		// 提现费率
		$rate = app_conf('FX_WITHDRAW_RATE');
		if ($rate) {
			$GLOBALS['tmpl']->assign('withdraw_rate', $rate);
		}

		//通用模版参数定义
		assign_uc_nav_list();//左侧导航菜单
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		$GLOBALS['tmpl']->assign("page_title","分销收益提现"); //title
		$GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());
		$GLOBALS['tmpl']->display("uc/uc_fx_withdraw.html"); //title
	} 
	
	public function del_withdraw()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_withdraw where id = ".$id." and is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']);
			if($order_info)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."fx_withdraw set is_delete = 1 where is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
				if($GLOBALS['db']->affected_rows())
				{
					$data['status'] = 1;
					$data['info'] = "删除成功";
					ajax_return($data);
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "删除失败";
					ajax_return($data);
				}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "提现单不存在";
				ajax_return($data);
			}
		}
	}
	
	
	public function withdraw_done()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		
		$user_info = $GLOBALS['user_info'];
		if($user_info['is_fx']==0){
		    $data['status'] = 0;
			$data['info'] = "请先购买分销资格会员";
			$data['jump'] = url("index","uc_fx#vip_buy");
			ajax_return($data);
		}
		
		$bank_name = strim($_REQUEST['bank_name']);
		$bank_account = strim($_REQUEST['bank_account']);
		$bank_user = strim($_REQUEST['bank_user']);
		$money = floatval($_REQUEST['money']);
		$type = intval($_REQUEST['type']);
		$mobile = $GLOBALS['user_info']['mobile'];
		
		$sms_verify = strim($_REQUEST['sms_verify']);
		if($bank_name==""&&$type==1)
		{
			$data['status'] = 0;
			$data['info'] = "请输入开户行全称";
			ajax_return($data);
		}
		if($bank_account==""&&$type==1)
		{
			$data['status'] = 0;
			$data['info'] = "请输入开户行账号";
			ajax_return($data);
		}
		if($bank_user==""&&$type==1)
		{
			$data['status'] = 0;
			$data['info'] = "请输入开户人真实姓名";
			ajax_return($data);
		}
		if($money<=0)
		{
			$data['status'] = 0;
			$data['info'] = "请输入正确的提现金额";
			ajax_return($data);
		}
		
		if(app_conf("SMS_ON")==1)
		{
			if($mobile=="")
			{
				$data['status'] = 0;
				$data['info'] = "请先完善会员的手机号码";
				$data['jump'] = url("index","uc_account");
				ajax_return($data);
			}
			
			
			
		
			if($sms_verify=="")
			{
				$data['status'] = 0;
				$data['info']	=	"请输入收到的验证码";
				ajax_return($data);
			}
		
			//短信码验证
			$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
			$GLOBALS['db']->query($sql);
		
			$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");
		
			if($mobile_data['code']!=$sms_verify)
			{
				$data['status'] = 1;
				$data['info']	=  "验证码错误";
				ajax_return($data);
			}
		
			
		}

		// 提现手续费
		$fee = 0;
		$rate = app_conf('FX_WITHDRAW_RATE');
		if ($rate > 0) {
			$fee = ceil(($money * $rate) / 10) / 100;
		}
		$moneyAndFee = $money + $fee;
		$submitted_sql = 'SELECT sum(money) money, sum(fee) fee FROM '.DB_PREFIX.'fx_withdraw WHERE user_id = '.$user_info['id'].' AND is_delete=0 AND is_paid=0';
		$submitted_arr = $GLOBALS['db']->getRow($submitted_sql);
		$submitted_money = $submitted_arr['money'] + $submitted_arr['fee'];
		
		// $submitted_money = floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id = ".$GLOBALS['user_info']['id']." and is_delete = 0 and is_paid = 0"));

		$result=$this->get_fx_withdraw_money();
		if($submitted_money+$moneyAndFee >$GLOBALS['user_info']['fx_money'] - $result)
		{
			$data['status'] = 0;
			$data['info'] = "提现超额";
			ajax_return($data);
		}
		
		$withdraw_data = array();
		$withdraw_data['user_id'] = $user_info['id'];
		$withdraw_data['money'] = $money;
		$withdraw_data['create_time'] = NOW_TIME;
		$withdraw_data['bank_name'] = $bank_name;
		$withdraw_data['bank_account'] = $bank_account;
		$withdraw_data['bank_user'] = $bank_user;
		$withdraw_data['type'] = $type;
		$withdraw_data['fee'] = $fee;

		// 如果开启余额自动审核功能
		$autoWithdraw = app_conf('FX_AUTO_WITHDRAW');
		if ($autoWithdraw) {
			$withdraw_data['pay_time'] = NOW_TIME;
			$withdraw_data['is_paid'] = 1;
		}

		$GLOBALS['db']->autoExecute(DB_PREFIX."fx_withdraw",$withdraw_data);
		
		$info = '提现申请提交成功，请等待审核';
		if ($autoWithdraw && $GLOBALS['db']->affected_rows() > 0) {
			require_once(APP_ROOT_PATH."system/model/fx.php");
			modify_account(array('money'=>$moneyAndFee,'is_admin'=>0),$user_info['id'],$user_info['user_name'].FX_NAME."提现".format_price($money)."元至余额自动审核通过。".$log);
			modify_fx_account("-".$moneyAndFee,$user_info['id'],$user_info['user_name'].FX_NAME."提现".format_price($money)."元至余额自动审核通过。".$log);
			modify_fx_statements($money,3,$user_info['user_name'].FX_NAME."提现".format_price($money)."元至余额自动审核通过。".$log,$user_info['id']);
			$info = '提现成功';
		}
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");
		$data['status'] = 1;
		$data['info'] = $info;
		ajax_return($data);
	}
    /**
	 * 获得分销T+N的可提现金额
	 * @param unknown $agency_id
	 */
	public function get_fx_withdraw_money(){
	    $day=app_conf("FX_WITHDRAW_CYCLE");
	    $withdraw_day=to_date((NOW_TIME),"Y-m-d");
	    $withdraw_start_day=to_date((NOW_TIME-3600 * 24 *$day),"Y-m-d");//N天前
	    
		$money=floatval($GLOBALS["user_info"]['fx_money']);//代理商账户总余额
		$withdraw_money=floatval($GLOBALS['db']->getOne("select sum(money) as withdraw_money from ".DB_PREFIX."fx_statements_log where type in (2,4,5,6) AND stat_time >= '".$withdraw_start_day ."' and stat_time<='".$withdraw_day."' and user_id=".intval($GLOBALS['user_info']['id'])));
		//N天内不可提现金额
	    return $withdraw_money;
	}
  
}
?>