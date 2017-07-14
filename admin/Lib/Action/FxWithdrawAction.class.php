<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class FxWithdrawAction extends CommonAction{

	
	/*
	 * 分销收益提现列表
	 */
	public function index()
	{
		if(isset($_REQUEST['is_paid']))
		{
			if(intval($_REQUEST['is_paid'])==0)
			{
				$map['is_paid'] = intval($_REQUEST['is_paid']);
				$map['is_delete'] = 0;
			}
		}
		$model = D ("FxWithdraw");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	
	/*
	 * 分销收益提现编辑
	 */	
	public function withdrawal_edit()
	{
		$id = intval($_REQUEST['id']);

		$withdrawal_info = M("FxWithdraw")->getById($id);
		$user_info= M("User")->where("id=".$withdrawal_info['user_id'])->find();
		$withdrawal_info['user_name']=$user_info['user_name'];
		$withdrawal_info['user_money']= $user_info['fx_money'];
		$this->assign("withdrawal_info",$withdrawal_info);
		$this->display();
	}		
	

	/*
	 * 分销收益提现审核
	 */	
	public function do_withdrawal()
	{
		$id = intval($_REQUEST['id']);
		$log=strim($_REQUEST['log']);
		require_once(APP_ROOT_PATH."system/model/user.php");
		require_once(APP_ROOT_PATH."system/model/fx.php");
		$withdrawal_info = M("FxWithdraw")->getById($id);
		$user_info=M("User")->getById($withdrawal_info['user_id']);
		$withdrawal_info['money']=floatval($_REQUEST['money']);
		if($withdrawal_info['money']<=0)$this->error("提现金额必须大于0");
		
		$result=$this->get_fx_withdraw_money();
		if($withdrawal_info['money']>$user_info['fx_money']-$result)$this->error("提现超额");				
		
		if($withdrawal_info['is_paid']==0)
		{
			M("FxWithdraw")->where("id=".$id)->setField("is_paid",1);
			M("FxWithdraw")->where("id=".$id)->setField("money",$withdrawal_info['money']);
			M("FxWithdraw")->where("id=".$id)->setField("pay_time",NOW_TIME);
			
			if($withdrawal_info['type']==0){//提至余额
				modify_account(array('money'=>$withdrawal_info['money'],'is_admin'=>1),$withdrawal_info['user_id'],$user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元至余额审核通过。".$log);
				modify_fx_account("-".$withdrawal_info['money'],$withdrawal_info['user_id'],$user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元至余额审核通过。".$log);
				modify_fx_statements($withdrawal_info['money'],3,$user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元至余额审核通过。".$log,$withdrawal_info['user_id']);
			}/*elseif($withdrawal_info['type']==1){//提至银行卡
				modify_fx_account("-".$withdrawal_info['money'],$withdrawal_info['user_id'],$user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元至银行卡审核通过。".$log);
				modify_fx_statements($withdrawal_info['money'],3,$user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元至银行卡审核通过。".$log,$withdrawal_info['user_id']);
			}*/else{
				$this->error("提现类型出错超额");		
			}

			
			
			//发短信与邮件
			send_user_withdraw_sms($user_info['id'],$withdrawal_info['money']);
			send_user_withdraw_mail($user_info['id'],$withdrawal_info['money']);
			
			save_log($user_info['user_name'].FX_NAME."提现".format_price($withdrawal_info['money'])."元审核通过。".$log,1);
			$this->success("确认提现成功");
		}
		else
		{
			$this->error("已提现过，无需再次提现");
		}
	
		
	}	
	
	
	public function del_withdrawal()
	{
		$id = intval($_REQUEST['id']);
		$withdrawal = M("FxWithdraw")->getById($id);
		
		$list = M("FxWithdraw")->where ("id=".$id )->delete();		
		if ($list!==false) {					 
				save_log($withdrawal['user_id']."号会员分销提现".$withdrawal['money']."元记录".l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),1);
		} else {
				save_log($withdrawal['user_id']."号会员分销提现".$withdrawal['money']."元记录".l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),1);
		}

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