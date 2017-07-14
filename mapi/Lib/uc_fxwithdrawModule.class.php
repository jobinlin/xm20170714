<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_fxwithdrawApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 会员中心分销提现列表接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录
	
	 * page_title:string 页面标题
	 * fxmoney:string 当前分销收益	 
	 * list:array:array 分销提现记录列表，结构如下
	 *  Array
		(
		    [0] => Array
		        (
		                [id] => 19 int 提现记录id
			            [money] =>¥3.9 string 提现金额
			            [create_time] => 2015-06-16 18:12:31  string  申请时间
			            [is_paid] => 1 int 管理员是否确认提现  0表示未确认 1表示已确认
			            [type] => 1 int 提现方式  1表示提现至银行卡  0表示提现至账户余额

		        )
		)
	 */
	public function index()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];	
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();	
		if($user_login_status!=LOGIN_STATUS_LOGINED){	
				
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;
			$root['fxmoney']=format_price($user_data['fx_money']);
			
			
			require_once(APP_ROOT_PATH."system/model/fx.php");
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;			
			$result = get_fx_withdraw($limit,$user_id);
			$page_total = ceil($result['count']/$page_size);
			
			foreach($result['list'] as $k=>$v)
			{	
				$list[$k]['id']=$v['id'];
				
				$list[$k]['money'] = format_price($v['money']);
				$list[$k]['create_time']=to_date($v['create_time']);
				$list[$k]['is_paid']=$v['is_paid'];

				$list[$k]['type']=$v['type'];
			}			
			
			
			$root['list'] = $list?$list:array();
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$result['count']);
			//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
			$root['page_title']="分销提现";

		}
		
		return output($root);

	}

	
	public function wap_index(){
	    $root = array();
	    /*参数初始化*/
	     
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    //print_r($user);exit;
	    $user_login_status = check_login();
	    //分页
	    $page = intval($GLOBALS['request']['page']);
	    $page=$page==0?1:$page;
	    $page_size = PAGE_SIZE;
	    $limit = (($page-1)*$page_size).",".$page_size;
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    } else {
	        $root['user_login_status'] = $user_login_status;
	        /*$default = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_withdraw where user_id=".$user['id']." and id=(select max(id) from ".DB_PREFIX."fx_withdraw where user_id=".$user['id'].")");
	        if($default){
    	        if(!$default['type']){
    	            $root['default_id']=0;
    	            $root['bank_info']="账户余额";
    	        }else{
    	            $sql="select id from ".DB_PREFIX."user_bank where user_id=".$user['id']." and bank_name='".$default['bank_name']."' and bank_account='".$default["bank_account"]."' and bank_user='".$default['bank_user']."'";
    	            $bank_id=$GLOBALS['db']->getOne($sql);
        	        if($bank_id){
        	            $root['default_id']=$bank_id;
            	        $root['bank_user']=$default['bank_user'];
            	        $root['bank_info']=$default['bank_name']."&nbsp;尾号".substr($default["bank_account"], -4);
        	        }else {
        	            $root['default_id']=0;
        	            $root['bank_info']="账户余额";
        	        }
    	        }
	        }else{
                $bank = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_bank where user_id=".$user['id']." and id=(select max(id) from ".DB_PREFIX."user_bank where user_id=".$user['id'].")");
	            if($bank) {
	                $root['default_id']=$bank['id'];
	                $root['bank_user']=$bank['bank_user'];
	                $root['bank_info']=$bank['bank_name']."&nbsp;尾号".substr($bank["bank_account"], -4);
	           }else{
	               $root['default_id']=0;
	               $root['bank_info']="账户余额";
	           }
	        }*/
	        $root['default_id']=0;
	        $root['bank_info']="账户余额";
	        
	        //可提现金额
	        $fx_money=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id=".$user['id']." and is_paid=0 and is_delete=0");
	        $money=$user['fx_money']-$fx_money;
	        //echo $money;exit;
			$result=$this->get_fx_withdraw_money();
			$money=$money-$result;
			
	        $root['fxmoney']=round($money,2);

	        $root['fx_withdraw_rate'] = app_conf('FX_WITHDRAW_RATE');
	        
	        /*$bank_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_bank where user_id = ".$user['id']);
	        $f_bank_list = array();
	        
	        //账户余额
	        $my_account=array();
	        $my_account['id'] = 0;
	        //$tmp_bank_name = strripos($v['bank_name'], "银行")?substr($v['bank_name'],  0,strripos($v['bank_name'], "银行")+8):$v['bank_name'];
	        $my_account['bank_name'] = "账户余额";
	        $f_bank_list[]=$my_account;
	        
	        //分页
	        $page = intval($GLOBALS['request']['page']);
	        $page=$page==0?1:$page;
	    
	        if($bank_list){
	            foreach ($bank_list as $k=>$v){
	                $temp_arr = array();
	                $temp_arr['id'] = $v['id'];
	                $temp_arr['bank_user'] = $v['bank_user'];
	                //$tmp_bank_name = strripos($v['bank_name'], "银行")?substr($v['bank_name'],  0,strripos($v['bank_name'], "银行")+8):$v['bank_name'];
	                $temp_arr['bank_name'] = $v['bank_name']." 尾号".  substr($v['bank_account'], -4);
	                $f_bank_list[] = $temp_arr;
	            }
	        }
	        
	        $root['bank_list'] = $f_bank_list?$f_bank_list:array();*/
	        $root['step']=1;
	    }
	    
	    $root['page_title'].="分销提现";
	    //print_r($root);exit;
	    return output($root);
	}
	
	
	

	
	

	
	/**
	 * 	 会员中心分销提现接口
	 * 
	 * 	  输入：
	 *  sms_verify:string 手机验证码，仅在app初始化配置中开启短信功能时传入，没开短信功能时不传
	 *  money:5.5 [int] 提现金额
	 *  type:0[int]  提现类型 0表示提现至余额 1表示提至银行卡
	 *  bank_name:中国建设银行[string] 开户行名称
	 *  bank_account:6227001856239566887 [string] 银行卡号
	 *  bank_user:陈新国 [string] 开户人姓名

	 *  
	 *  输出：
	 * user_login_status:[int]   0表示未登录   1表示已登录

		底层的status字段为1时表示成功，0表示失败，info表示失败信息
	

   
	 */
	public function save()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$id = intval($GLOBALS['request']['id']);
		
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){			
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;

			$data['sms_verify'] = intval($GLOBALS['request']['sms_verify']);
			$data['money'] = floatval($GLOBALS['request']['money']);
			$data['type'] = intval($GLOBALS['request']['type']);
			$mobile = $GLOBALS['user_info']['mobile'];
			
			$data['bank_name'] = strim($GLOBALS['request']['bank_name']);
			$data['bank_account'] = strim($GLOBALS['request']['bank_account']);
			$data['bank_user'] = strim($GLOBALS['request']['bank_user']);
	

			if($data['bank_name']==""&&$data['type']==1)
			{

				return output($root,0,"请输入开户行全称");
			}
			if($data['bank_account'] ==""&&$data['type']==1)
			{
				

				return output($root,0,"请输入开户行账号");
			}
			if($data['bank_user']==""&&$data['type']==1)
			{				

				return output($root,0,"请输入开户人真实姓名");				
			}
			if($data['money']<=0)
			{				

				return output($root,0,"请输入正确的提现金额");		
			}			
			
			if(app_conf("SMS_ON")==1)
			{
				if($mobile=="")
				{

					return output($root,0,"请先完善会员的手机号码");		
				}				
			
				if($data['sms_verify']=="")
				{					

					return output($root,0,"请输入收到的验证码");	
				}
			
				//短信码验证
				$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
				$GLOBALS['db']->query($sql);
			
				$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");
			
				if($mobile_data['code']!=$data['sms_verify'])
				{

					return output($root,0,"验证码错误");					
				}			
				
			}			
			
			$submitted_money = floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id = ".$GLOBALS['user_info']['id']." and is_delete = 0 and is_paid = 0"));
			if($submitted_money+$money>$GLOBALS['user_info']['fx_money'])
			{					

				return output($root,0,"提现超额");		
			}
			
			$withdraw_data = array();
			$withdraw_data['user_id'] = $user_id;
			$withdraw_data['money'] = $data['money'];
			$withdraw_data['create_time'] = NOW_TIME;
			$withdraw_data['bank_name'] =$data['bank_name'];
			$withdraw_data['bank_account'] = $data['bank_account'];
			$withdraw_data['bank_user'] = $data['bank_user'];
			$withdraw_data['type'] = $data['type'];

			$GLOBALS['db']->autoExecute(DB_PREFIX."fx_withdraw",$withdraw_data);

			$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");

				
			$root['add_status'] = 1;					
				
		}

	
		
		return output($root);

	}	
	
	/**
	 * 	 会员中心分销提现明细接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录
	 **/
	public function wap_save(){
	    $root = array();
	    	
	    $user_data = $GLOBALS['user_info'];
	    $user_id = intval($user_data['id']);
	    $id = intval($GLOBALS['request']['id']);
	    
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }else{
	        $root['user_login_status'] = $user_login_status;
	        
	        $data['money'] = floatval($GLOBALS['request']['money']);
	        $data['type'] = intval($GLOBALS['request']['type']);
	        $data['bank_id'] = intval($GLOBALS['request']['bank_id']);
	        $pwd=strim($GLOBALS['request']['password']);

	        if($user_data['user_pwd']!=md5($pwd)){
	            return output($root,0,"密码错误");
	        }
	        $user_money=$user_data['fx_money'];

	        // 提现手续费
			$fee = 0;
			$rate = app_conf('FX_WITHDRAW_RATE');
			if ($data['money'] > 0 && $rate > 0) {
				$fee = ceil(($data['money'] * $rate) / 10) / 100;
			}
			// $money = $data['money'] + $fee;
			$submitted_sql = 'SELECT sum(money) money, sum(fee) fee FROM '.DB_PREFIX.'fx_withdraw WHERE user_id = '.$user_id.' AND is_delete=0 AND is_paid=0';
			$submitted_arr = $GLOBALS['db']->getRow($submitted_sql);
			$fx_money = $submitted_arr['money'] + $submitted_arr['fee'];

	        // $fx_money=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id=".$user_id." and is_paid=0 and is_delete=0");
	        $money=$user_money-$fx_money;
			$result=$this->get_fx_withdraw_money();
			$money=$money-$result;
	        if($money<=0){
	            return output($root,0,"无可提现金额");
	        }
	        if($data['money']<=0)
	        {
	            return output($root,0,"请输入正确的提现金额");
	        }
	        if($data['bank_id']!=0){
	            $bank=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_bank where user_id=".$user_id." and id = ".$data['bank_id']);
	            if(!$bank){
	                return output($root,0,"请选择提现方式");
	            }
	            $data['type']=1;
	        }else {
	            $data['type']=0;
	        }
	        $moneyAndFee = $data['money'] + $fee;
	        if($money < $moneyAndFee){
	            return output($root,0,"提现超额");
	        }

	        
	        $withdraw_data = array();
	        $withdraw_data['user_id'] = $user_id;
	        $withdraw_data['money'] = $data['money'];
	        $withdraw_data['create_time'] = NOW_TIME;
	        $withdraw_data['bank_name'] =$bank['bank_name'];
	        $withdraw_data['bank_account'] = $bank['bank_account'];
	        $withdraw_data['bank_user'] = $bank['bank_user'];
	        $withdraw_data['type'] = $data['type'];
	        $withdraw_data['fee'] = $fee;

	        // 如果开启余额自动审核功能
			$autoWithdraw = app_conf('FX_AUTO_WITHDRAW');
			if ($autoWithdraw) {
				$withdraw_data['pay_time'] = NOW_TIME;
				$withdraw_data['is_paid'] = 1;
			}

	        $GLOBALS['db']->autoExecute(DB_PREFIX."fx_withdraw",$withdraw_data);
	        
	        $affected_rows = $GLOBALS['db']->affected_rows();
	        $info = '提现审核提交成功';
	        if ($autoWithdraw &&  $affected_rows > 0) {
	        	$info = '提现成功';
				require_once(APP_ROOT_PATH."system/model/fx.php");
				modify_account(array('money'=>$moneyAndFee,'is_admin'=>0),$user_id,$user_data['user_name'].FX_NAME."提现".format_price($data['money'])."元至余额自动审核通过。".$log);
				modify_fx_account("-".$moneyAndFee,$user_id,$user_data['user_name'].FX_NAME."提现".format_price($data['money'])."元至余额自动审核通过。".$log);
				modify_fx_statements($data['money'],3,$user_data['user_name'].FX_NAME."提现".format_price($data['money'])."元至余额自动审核通过。".$log,$user_id);
			}

	        if($affected_rows){
	            $root['add_status'] = 1;
	            return output($root,1,$info);
	        }else {
	            return output($root,0,"提现审核提交失败");
	        }
	        
	    }
	}
	

	/** page_title:string 页面标题
	 * fxmoney:string 当前分销收益	 
	 * list:array:array 分销提现记录列表，结构如下
	 *  Array
		(
		    [0] => Array
		        (
		                [id] => 19 int 提现记录id
			            [money] =>¥3.9 string 提现金额
			            [create_time] => 2015-06-16 18:12:31  string  申请时间
			            [is_paid] => 1 int 管理员是否确认提现  0表示未确认 1表示已确认
			            [type] => 1 int 提现方式  1表示提现至银行卡  0表示提现至账户余额

		        )
		)
	 */
	public function detail()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];	
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();	
		if($user_login_status!=LOGIN_STATUS_LOGINED){	
				
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;
			$root['fxmoney']=format_price($user_data['fx_money']);
			
			
			require_once(APP_ROOT_PATH."system/model/fx.php");
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;			
			$result = get_fx_withdraw($limit,$user_id);
			$page_total = ceil($result['count']/$page_size);
			
			foreach($result['list'] as $k=>$v)
			{	
				$list[$k]['id']=$v['id'];
				
				$list[$k]['money'] = format_price($v['money']);
				$list[$k]['create_time']=to_date($v['create_time']);
				$list[$k]['is_paid']=$v['is_paid'];
				$list[$k]['pay_time']=$v['pay_time'];
				$list[$k]['type']=$v['type'];
				if($list[$k]['type']==1){
					$list[$k]['bank_name']=$v['bank_name'];
					$list[$k]['bank_account']=substr($v['bank_account'],-4);//$v['bank_account'];
					$list[$k]['bank_user_bak']=$v['bank_user_bak'];
				}
			}			
			
			$root['is_fx']=$user_data['is_fx'];
			$root['list'] = $list?$list:array();
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$result['count']);
			//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
			$root['page_title']="分销提现明细";

		}
		
		return output($root);

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