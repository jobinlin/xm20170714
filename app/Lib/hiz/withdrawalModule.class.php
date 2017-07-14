<?php 
/**
 * 提现
 */
require APP_ROOT_PATH.'app/Lib/page.php';
//require_once(APP_ROOT_PATH."system/model/supplier.php");
class withdrawalModule extends HizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
       
    }
	
    
	public function index()
	{		
				
		init_app_page();
		$s_account_info = $GLOBALS["hiz_account_info"];
		$account_id = $s_account_info['id'];

		
		$agency_info=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."agency where id=".$account_id);
        $result=$this->get_agency_withdraw_money($account_id);
        $withdraw_money=$result['withdraw_money'];//账户可提现余额
		
	    //分页
	    $page_size = 10;
	    $page = intval($_REQUEST['p']);
	    if($page==0) $page = 1;
	    $limit = (($page-1)*$page_size).",".$page_size;
	   
	    $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."agency_money_submit  where agency_id=".$account_id." order by create_time desc limit ".$limit);
	    
		foreach($list as $k=>$v){
			$list[$k]['create_time'] = to_date($v['create_time']);
			$list[$k]['money'] = format_price($v['money']);
			if($v['is_paid']==1){
				$list[$k]['is_paid']="已确认提现";
			}else if($v['is_paid']==0){
				$list[$k]['is_paid']="待审核";
				
			}else{
				$list[$k]['is_paid']="已拒绝";
			}	    	

	    }

	    $total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."agency_money_submit  where agency_id=".$account_id);
	    $page = new Page($total,$page_size);   //初始化分页对象
	    $p  =  $page->show();
	    $GLOBALS['tmpl']->assign('pages',$p);

	    $GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
	    $GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());
	    $GLOBALS['tmpl']->assign("list",$list);
	    $GLOBALS['tmpl']->assign("agency_info",$agency_info);		
	    $GLOBALS['tmpl']->assign("withdraw_money",$withdraw_money);
		$GLOBALS['tmpl']->assign("head_title","账户提现");
		$GLOBALS['tmpl']->display("pages/withdrawal/index.html");	
	
	}
	
	
	public function withdraw_done()
	{

		$s_account_info = $GLOBALS["hiz_account_info"];
		$account_id = $s_account_info['id'];

		$result=$this->get_agency_withdraw_money($account_id);
		$money = $result['withdraw_money'];//账户可提现余额

		if($money <= 0)
		{
		    $data['status'] = false;
		    $data['info']	=  "无可提现余额";
		    ajax_return($data);
		}

		if(strim($_REQUEST['money'])>$money)
		{
		    $data['status'] = false;
		    $data['info']	=  "输入的金额不可大于可提现余额";
		    ajax_return($data);
		}
		if(app_conf("SMS_ON")==1){
			//短信码验证
			$sms_verify = strim($_REQUEST['sms_verify']);
			$mobile_phone=$GLOBALS['db']->getOne("select mobile from ".DB_PREFIX."agency where id=".$account_id);
			if($sms_verify == ''){
				$data['status'] = false;
				$data['info'] = "请输入手机验证码";			
				ajax_return($data);
			}
			$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
			$GLOBALS['db']->query($sql);
			
			$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");

			if($mobile_data['code']!=$sms_verify)
			{
				$data=array();
				$data['status'] = false;
				$data['info']	=  "手机验证码错误";
				$data['field'] = "sms_verify";
				ajax_return($data);
			}
		}else{
			$account_password = strim($_REQUEST['pwd']);			
			if($account_password == ''){
				$data['status'] = false;
				$data['info'] = "请输入密码";			
				ajax_return($data);
			}
			if(md5($account_password)!=$s_account_info['account_password']){
				$data['status'] = false;
				$data['info'] = "密码不正确";			
				ajax_return($data);
			}
		}
		
		$agency_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."agency where id=".$account_id);
		$withdraw_data = array();
		$withdraw_data['agency_id'] = $account_id;
		$withdraw_data['money'] = round(floatval($_REQUEST['money']),2);
		$withdraw_data['create_time'] = NOW_TIME;
		$withdraw_data['is_paid'] = 0;
		
		$withdraw_data['bank_name'] = $agency_info['bank_name'];
		$withdraw_data['bank_account'] = $agency_info['bank_info'];
		$withdraw_data['bank_user'] = $agency_info['bank_user'];
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."agency_money_submit",$withdraw_data);	
		$info=$s_account_info['name']."提现".format_price(round(floatval($_REQUEST['money']),2));		
		modify_agency_account(round(floatval($_REQUEST['money']),2),$account_id,4,$info);//提交申请时，将代理商的账户余额减掉
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
			
		$data['status'] = 1;
		$data['info'] = "提现申请提交成功，请等待管理员审核";

		ajax_return($data);		
		
	}
	

	/**
	 * 发送商家提现手机验证码
	 */
	public function biz_sms_code()
	{
		$s_account_info = $GLOBALS["hiz_account_info"];
		$verify_code = strim($_REQUEST['verify_code']);
		$account_id = $s_account_info['id'];
	
	
		$sms_ipcount = load_sms_ipcount();
		if($sms_ipcount>1)
		{
			//需要图形验证码
			if(es_session::get("verify")!=md5($verify_code))
			{
				$data['status'] = false;
				$data['info'] = "图形验证码错误";
				$data['field'] = "verify_code";
				ajax_return($data);
			}
		}
	
		if(!check_ipop_limit(CLIENT_IP, "send_sms_code",SMS_TIMESPAN))
		{
			showErr("请勿频繁发送短信",1);
		}
	
		$mobile_phone=$GLOBALS['db']->getOne("select mobile from ".DB_PREFIX."agency where id=".$account_id);
		
		if(empty($mobile_phone))
		{
			$data['status'] = false;
			$data['info'] = "商户未提供验证手机号，请联系管理员";
			ajax_return($data);
		}
	
		//删除失效验证码
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
	
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
		if($mobile_data)
		{
			//重新发送未失效的验证码
			$code = $mobile_data['code'];
			$mobile_data['add_time'] = NOW_TIME;
			$GLOBALS['db']->query("update ".DB_PREFIX."sms_mobile_verify set add_time = '".$mobile_data['add_time']."',send_count = send_count + 1 where mobile_phone = '".$mobile_phone."'");
		}
		else
		{
			$code = rand(100000,999999);
			$mobile_data['mobile_phone'] = $mobile_phone;
			$mobile_data['add_time'] = NOW_TIME;
			$mobile_data['code'] = $code;
			$mobile_data['ip'] = CLIENT_IP;
			$GLOBALS['db']->autoExecute(DB_PREFIX."sms_mobile_verify",$mobile_data,"INSERT","","SILENT");
				
		}
		send_verify_sms($mobile_phone,$code);
		es_session::delete("verify"); //删除图形验证码
		$data['status'] = true;
		$data['info'] = "发送成功";
		$data['lesstime'] = SMS_TIMESPAN -(NOW_TIME - $mobile_data['add_time']);  //剩余时间
		$data['sms_ipcount'] = load_sms_ipcount();
		ajax_return($data);	
	
	}		
	
	/**
	 * 获得代理商T+N的可提现金额
	 * @param unknown $agency_id
	 */
	public function get_agency_withdraw_money($agency_id){
	    
	    $day=app_conf("AGENCY_WITHDRAW_DAY");
	    $withdraw_day=to_date((NOW_TIME),"Y-m-d");
	    $withdraw_start_day=to_date((NOW_TIME-3600 * 24 *$day),"Y-m-d");//N天前
	    
		$money=floatval($GLOBALS["hiz_account_info"]['money']);//代理商账户总余额
		//echo $money;
		$withdraw_money=floatval($GLOBALS['db']->getOne("select sum(toady_wd_money) as withdraw_money from ".DB_PREFIX."agency_statements where stat_time >= '".$withdraw_start_day ."' and stat_time<='".$withdraw_day."' and agency_id=".$agency_id));//N天内不可提现金额
	    //echo $withdraw_money;
		//$submit_withdraw_money=floatval($GLOBALS['db']->getOne("select sum(money) as submit_withdraw_money from ".DB_PREFIX."agency_money_submit where agency_id=".$agency_id));//已提交提现申请金额
		//echo $submit_withdraw_money;
		$allow_withdraw_money=floatval($money-$withdraw_money);
		
	    $result=array();
	    $result['withdraw_money']=$allow_withdraw_money;
		
	    return $result;
	}
	

}
?>