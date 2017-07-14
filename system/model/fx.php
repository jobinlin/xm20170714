<?php
//分销功能的函数库,分销商品上架后,产生的分销订单，商品一旦下架将不再返还分销佣金，以订单成功为节点

/**
 * 为订单发放分销的佣金(旧方法弃用 见 #439 send_fx_order_salary())
 * @param unknown_type $order_id 订单ID
 */
function send_fx_order_salary_old($order_id)
{
	require_once(APP_ROOT_PATH."system/model/user.php");
	
	//取出全局的分销配置
	$fx_salary = $GLOBALS['db']->getAll("select fx_salary,fx_salary_type from ".DB_PREFIX."fx_salary where level_id = 0 order by fx_level asc limit ".FX_LEVEL);
		
	$deal_order_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = '".$order_id."' and refund_status <> 2");
	foreach($deal_order_items as $k=>$item)
	{
		$deal_is_fx = intval($GLOBALS['db']->getOne("select is_fx from ".DB_PREFIX."deal where id = ".$item['deal_id']));
		if($item['fx_user_id']>0&&$deal_is_fx) //订单为分销单，并且商品为分销商品
		{
			$user_id = $item['fx_user_id'];
			//是分销单
			$fx_user = load_user($user_id);
			if($fx_user['is_fx']==1)
			{
				
				$user_fx_salary = $GLOBALS['db']->getAll("select fx_salary,fx_salary_type from ".DB_PREFIX."fx_salary where level_id = ".$fx_user['fx_level']." order by fx_level asc limit ".FX_LEVEL);
				
				//会员支持分销再查看商品是不是该会员分销的
				//不再强制每个商品必需被会员领取才可以产生分销
				//$user_deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_deal where deal_id = '".$item['deal_id']."' and user_id = '".$user_id."'");
				//if($user_deal||$deal_is_fx==1)
				if($deal_is_fx)//条件永远成立了
				{
					//查看商品是否有分销佣金配置
					$deal_fx_salary = $GLOBALS['db']->getAll("select fx_salary,fx_salary_type from ".DB_PREFIX."deal_fx_salary where deal_id = ".$item['deal_id']." order by fx_level asc limit ".FX_LEVEL);
					//开始发放销售佣金
					$salary_config = null; //分销销售佣金设置
					$salary = 0; //销售佣金
					if(floatval($deal_fx_salary[0]['fx_salary'])>0)
					{
						$salary_config = $deal_fx_salary[0];
					}
					else
					{
						if(floatval($user_fx_salary[0]['fx_salary'])>0)
						{
							$salary_config = $user_fx_salary[0];
						}
						else
						{
							if(floatval($fx_salary[0]['fx_salary'])>0)
							{
								$salary_config = $fx_salary[0];
							}
						}
					}				
					
					if($salary_config) //计算销售佣金
					{
						if($salary_config['fx_salary_type']==0) //定额
						{
							$salary = $salary_config['fx_salary'];
						}
						else
						{
							$salary = $item['total_price']*$salary_config['fx_salary'];
						}
					}
					
					$is_send = send_fx_user_salary($user_id,$item['total_price'],$salary,0,"分销商".$fx_user['user_name']."售出".$item['number']."件".$item['name']."获得佣金");
					if($is_send)
					{
						update_fx_order_item_log($item['id'],$salary,0);
						modify_fx_statements($item['total_price'], 0, "分销商".$fx_user['user_name']."售出".$item['number']."件".$item['name']);
						modify_fx_statements($salary, 1, "分销商".$fx_user['user_name']."售出".$item['number']."件".$item['name']."获得佣金");
					}
					//end 发放销售佣金
					
					//开始发放推广佣金
					$fx_level = 1;
					$tg_user = $fx_user;
					while(true)
					{
						if($tg_user['pid']==0||$fx_level>FX_LEVEL)
						{
							break;
						}
						else
						{
							//开始发放当前级别的推广佣金
							$tg_user = load_user($tg_user['pid']);
							if($tg_user['is_fx']==0)
							{
								break; //当前分销链中的会员被关闭分销身份，退出
							}
							$salary_config = null; //分销推广佣金设置
							$salary = 0; //推广佣金
							if(floatval($deal_fx_salary[$fx_level]['fx_salary'])>0)
							{
								$salary_config = $deal_fx_salary[$fx_level];
							}
							else
							{
								if(floatval($user_fx_salary[$fx_level]['fx_salary'])>0)
								{
									$salary_config = $user_fx_salary[$fx_level];
								}
								else
								{
									if(floatval($fx_salary[$fx_level]['fx_salary'])>0)
									{
										$salary_config = $fx_salary[$fx_level];
									}
								}
							}
								
							if($salary_config) //计算销售佣金
							{
								if($salary_config['fx_salary_type']==0) //定额
								{
									$salary = $salary_config['fx_salary'];
								}
								else
								{
									$salary = $item['total_price']*$salary_config['fx_salary'];
								}
							}
								
							$is_send = send_fx_user_salary($tg_user['id'],$item['total_price'],$salary,$fx_level,"分销商".$fx_user['user_name']."售出".$item['number']."件".$item['name']."获得推广佣金");
							if($is_send)
							{
								//更新每一层级的返佣关系
								$fx_user_reward = array("pid"=>$tg_user['id'],"user_id"=>$fx_user['id'],"money"=>$salary);
								$GLOBALS['db']->autoExecute(DB_PREFIX."fx_user_reward",$fx_user_reward,"INSERT","","SILENT");
								if($GLOBALS['db']->errno())
								{
									$GLOBALS['db']->query("update ".DB_PREFIX."fx_user_reward set money = money + ".$salary." where pid = ".$tg_user['id']." and user_id = ".$fx_user['id']);
								}
								
								update_fx_order_item_log($item['id'],$salary,$fx_level);
								modify_fx_statements($salary, 2, "分销商".$fx_user['user_name']."售出".$item['number']."件".$item['name']."获得推广佣金");
							}
							//end 发放当前级别的推广佣金
							
							$fx_level++;
						}
					}
					//end 发放推广佣金
					
				}
			}// end if($fx_user['is_fx']==1)
		} //end if($item['fx_user_id']>0)
	}
}


/**
 * ###分销佣金暂时没有发放##
 * 
 * 将分销佣金发放给会员,并生成会员的分销佣金日志
 * @param unknown_type $user_id 会员ID
 * @param unknown_type $money 佣金金额
 * @param unknown_type $level int 佣金等级 0为销售佣金
 * @param unknown_type $log 日志
 * 
 * 返回 bool
 */
function send_fx_user_salary($user_id,$sale_money,$money,$level,$log)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."user set fx_total_money = fx_total_money+".$sale_money.",fx_total_balance=fx_total_balance+".$money." where id = ".$user_id." and is_fx = 1");
	if($GLOBALS['db']->affected_rows()>0)
	{
		//开始更新分销等级
		// $fx_total_money = floatval($GLOBALS['db']->getOne("select fx_total_money from ".DB_PREFIX."user where id = ".$user_id));
		$fx_total_balance = floatval($GLOBALS['db']->getOne("select fx_total_balance from ".DB_PREFIX."user where id = ".$user_id));
		$level_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."fx_level where money<=".$fx_total_balance." order by money desc");
		$GLOBALS['db']->query("update ".DB_PREFIX."user set fx_level = ".$level_id." where id = ".$user_id);
		modify_fx_account($money,$user_id,$log);
		return true;
	}
	else
		return false;
}

/**
 * 将分销佣金发放给会员,并生成会员的分销佣金日志
 * @param unknown_type $user_id 会员ID
 * @param unknown_type $money 佣金金额
 * @param unknown_type $level int 佣金等级 0为销售佣金
 * @param unknown_type $log 日志
 *
 * 返回 bool
 */
function send_fx_buy_salary($user_id,$salary,$log,$sale_money)
{
    $GLOBALS['db']->query("update ".DB_PREFIX."user set fx_total_vip_money = fx_total_vip_money+".(floor($sale_money*100)/100).",fx_total_vip_buy=fx_total_vip_buy+".$salary." where id = ".$user_id." and is_fx = 1");
    if($GLOBALS['db']->affected_rows()>0)
    {
        modify_fx_level($user_id);
        modify_fx_account($salary,$user_id,$log);
        return true;
    }
    else
        return false;
}

/**
 * 按累积发放佣金更改用户等级
 */
function send_fx_user_salary_new($user_id, $salary, $log,$sale_money)
{
	$sql = 'UPDATE '.DB_PREFIX.'user set fx_total_money = fx_total_money+'.(floor($sale_money*100)/100).',fx_total_balance=fx_total_balance+'.(floor($salary*100)/100)
        .' WHERE id = '.$user_id.' AND is_fx = 1';
	$GLOBALS['db']->query($sql);
	if ($GLOBALS['db']->affected_rows() > 0) {
		modify_fx_level($user_id);
		modify_fx_account($salary, $user_id, $log);
		return true;
	}
	return false;
}

/**
 * 更改会员分销等级
 * @param  int $user_id 
 * @return           
 */
function modify_fx_level($user_id)
{
	$user_sql = 'SELECT fx_total_balance, fx_total_vip_buy, fx_level FROM '.DB_PREFIX.'user WHERE id = '.$user_id;
	$user = $GLOBALS['db']->getRow($user_sql);
	$balance_level_sql = 'SELECT id FROM '.DB_PREFIX.'fx_level WHERE money <= '.($user['fx_total_balance'] + $user['fx_total_vip_buy']).' ORDER BY money DESC';
	$balance_level = $GLOBALS['db']->getOne($balance_level_sql);
	
	$update = 'UPDATE '.DB_PREFIX.'user set fx_level = '.$balance_level.' WHERE id = '.$user_id;
	$GLOBALS['db']->query($update);
}


/**
 * 更新会员的分销帐户
 * @param unknown_type $money
 * @param unknown_type $user_id
 * @param unknown_type $log
 * 

 */
function modify_fx_account($money,$user_id,$log)
{
	if($money>0){
		$sql = "update ".DB_PREFIX."user set fx_money=fx_money+".(floor($money*100)/100)." where id = ".$user_id;
		$GLOBALS['db']->query($sql);
		// logger::write($sql, 'INFO', 3, 'fx_log');
		require_once(APP_ROOT_PATH."system/model/user.php");
		load_user($user_id,true);
		
		//生成分销资金日志
		$log_info['log'] = $log;
		$log_info['create_time'] = NOW_TIME;
		$log_info['money'] = (floor($money*100)/100);//floatval($money);
		$log_info['user_id'] = $user_id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."fx_user_money_log",$log_info);
	}
}

/**
 * 生成平台的分销报表
 * @param unknown_type $money
 * @param unknown_type $type 0:营业销 1分销佣金 2推广佣金 3分销提现 4开通分销资格佣金 5推荐商家出售商品和团购产生的佣金 6推荐商家到店买单佣金
 * @param unknown_type $info
 */
function modify_fx_statements($money, $type, $info, $user_id)
{
	$field_array = array(
			'sale_money',
			'fx_salary',
			'fx_extend_salary',
			'fx_withdraw',
	        'vip_buy_salary',
			'ref_salary',
			'store_payment_salary'
	);
	
	$stat_time = to_date(NOW_TIME,"Y-m-d");
	$stat_month = to_date(NOW_TIME,"Y-m");
	$state_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_statements where stat_time = '".$stat_time."'");
	if($state_data)
	{
		$state_data[$field_array[$type]] = $state_data[$field_array[$type]]+floatval($money);
		$GLOBALS['db']->autoExecute(DB_PREFIX."fx_statements",$state_data, $mode = 'UPDATE', "id=".$state_data['id'], $querymode = 'SILENT');
		$rs = $GLOBALS['db']->affected_rows();
	}
	else
	{
		$state_data[$field_array[$type]] = floatval($money);
		$state_data["stat_time"] = $stat_time;
		$state_data["stat_month"] = $stat_month;
		$GLOBALS['db']->autoExecute(DB_PREFIX."fx_statements",$state_data, $mode = 'INSERT', "", $querymode = 'SILENT');
		$rs = $GLOBALS['db']->insert_id();
	}
	
	if($rs)
	{
		$log_data = array();
		$log_data['log'] = $info;
		$log_data['create_time'] = NOW_TIME;
		$log_data['money'] = floatval($money);
		$log_data['type'] = $type;
		$log_data['user_id'] = intval($user_id);
		$log_data['stat_time'] = to_date(NOW_TIME,"Y-m-d");
		$GLOBALS['db']->autoExecute(DB_PREFIX."fx_statements_log",$log_data);
	}
}

/**
 * 更新订单商品的分销佣金
 * @param unknown_type $order_item_id
 * @param unknown_type $salary
 * @param unknown_type $level
 */
function update_fx_order_item_log($order_item_id,$salary,$level)
{
	$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
	$fx_salary_all = unserialize($order_item['fx_salary_all']); 
	if(!$fx_salary_all)
	{
		$fx_salary_all = array();
	}
	$fx_salary_all[$level] = $salary;
	$fx_salary_all = serialize($fx_salary_all);
	if($level==0)
	{
		$sale_salary = $salary;
		$GLOBALS['db']->query("update ".DB_PREFIX."user_deal set sale_count=sale_count+".$order_item['number'].",sale_total=sale_total+".$order_item['total_price'].",sale_balance=sale_balance+".$salary." where deal_id=".$order_item['deal_id']." and user_id = ".$order_item['fx_user_id']);
	}
	else
		$sale_salary = 0;
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set fx_salary=fx_salary+".$sale_salary.",fx_salary_total=fx_salary_total+".$salary.",fx_salary_all='".$fx_salary_all."' where id = ".$order_item_id);
	
	
}


/**
 * 分销提现记录列表

 */
function get_fx_withdraw($limit,$user_id)
{
	$user_id = intval($user_id);
	$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."fx_withdraw where user_id = ".$user_id." order by create_time desc limit ".$limit);
	foreach($list as $k=>$v)
	{
		$bank_account_end = substr($v['bank_account'],-4,4);
		$bank_account_show_length = strlen($v['bank_account']) - 4;
		$bank_account = "";
		for($i=0;$i<$bank_account_show_length;$i++)
		{
			$bank_account.="*";
		}
		$bank_account.=$bank_account_end;
		$list[$k]['bank_account'] =  $bank_account;
		
		$bank_user_end = msubstr($v['bank_user'],-1,1,"utf-8",false);
		$bank_user_show_length = mb_strlen($v['bank_user'],"utf-8")-1;
		$bank_user = "";
		for($i=0;$i<$bank_user_show_length;$i++)
		{
			$bank_user.="*";
		}
		$bank_user.=$bank_user_end;
		$list[$k]['bank_user_bak'] =  $v['bank_user'];
		$list[$k]['bank_user'] =  $bank_user;
	}
	$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_withdraw where user_id = ".$user_id);

	return array("list"=>$list,'count'=>$count);
}

function add_user_fx_deal($user_id,$deal_id){
    $user_id = intval($user_id);
    $deal = $GLOBALS['db']->getRow('select id,is_fx from '.DB_PREFIX."deal where id=".$deal_id);
    if ($deal['id']>0 && $deal['is_fx']==2){ //允许会员领取的数据
        if($GLOBALS['db']->getOne('select count(*) from '.DB_PREFIX.'user_deal where deal_id='.$deal_id.' and user_id ='.$user_id)){
            return true;
        }else{
            $ins_data['deal_id'] = $deal_id;
            $ins_data['add_time'] = NOW_TIME;
            $ins_data['user_id'] = $user_id;
            $ins_data['is_effect'] = 1; //上架


            $GLOBALS['db']->autoExecute(DB_PREFIX."user_deal",$ins_data);
            return $GLOBALS['db']->insert_id()>0;
        }
    }
    return false;

}
function do_is_effect($user_id,$deal_id){
    $user_id = intval($user_id);
    $deal_id = intval($deal_id);
    
    $u_deal = $GLOBALS['db']->getRow('select * from '.DB_PREFIX.'user_deal where user_id='.$user_id.' and deal_id = '.$deal_id.' and type=0 '); 
    if($u_deal){
        $is_effect = $u_deal['is_effect'] == 1?0:1;
        return $GLOBALS['db']->autoExecute(DB_PREFIX.'user_deal',array('is_effect'=>$is_effect),'UPDATE','user_id='.$user_id.' and deal_id = '.$deal_id.' and type=0 ');
    }else{
        return false;
    }
    
}

function del_user_deal($user_id,$deal_id){
    $user_id = intval($user_id);
    $deal_id = intval($deal_id);
    
    $u_deal = $GLOBALS['db']->getRow('select * from '.DB_PREFIX.'user_deal where user_id='.$user_id.' and deal_id = '.$deal_id.' and type=0 '); 
    if($u_deal){
        return $GLOBALS['db']->query('delete from '.DB_PREFIX.'user_deal where user_id='.$user_id.' and deal_id = '.$deal_id.' and type=0 ');
    }else{
        return false;
    }
    
}

/**
 * 计算分销佣金
 * @param  int $order_id 订单ID
 * @return null           
 */
function send_fx_order_salary($order_id)
{
	// 先判断订单是否存在并获取用户信息
	$order_sql = 'SELECT user_id FROM '.DB_PREFIX.'deal_order WHERE id = '.$order_id;
	// logger::write($order_sql, 'INFO', 3, 'fx_log');
	$user_id = $GLOBALS['db']->getOne($order_sql);
	if (!$user_id) {
		return;
	}
	// 获取订单的分销商品信息
	$order_item_sql = 'SELECT oi.*, d.is_fx FROM '.DB_PREFIX.'deal_order_item oi INNER JOIN '.DB_PREFIX.'deal d ON d.id = oi.deal_id AND d.is_fx <> 0 WHERE oi.order_id = '.$order_id.' AND oi.refund_status <> 2';
	// logger::write($order_item_sql, 'INFO', 3, 'fx_log');
	$order_item = $GLOBALS['db']->getAll($order_item_sql);
	if (count($order_item) > 0) {
		$user_info_sql = 'SELECT id, pid FROM '.DB_PREFIX.'user WHERE id='.$user_id;
		// logger::write($user_info_sql, 'INFO', 3, 'fx_log');
		$user_info = $GLOBALS['db']->getRow($user_info_sql);
		// 当前计算佣金用户
		$current_user = $user_info;
		if ($current_user['pid'] > 0) {
			$log_user = '';
			$arr=array();
			for ($i=0; $i < 3; $i++) { 
				$p_user_sql = 'SELECT id,user_name,pid,fx_level,is_fx,is_effect,is_delete FROM '.DB_PREFIX.'user WHERE id='.$current_user['pid'];
				// logger::write($p_user_sql, 'INFO', 3, 'fx_log');
				$p_user = $GLOBALS['db']->getRow($p_user_sql);
				if (empty($p_user)) {
					break;
				}
				// 跳过没开通分销的会员
				if ($p_user['is_fx'] == 0 || $p_user['is_effect'] != 1 || $p_user['is_delete'] != 0) {
					continue;
				}
				$salary_sql = 'SELECT fx_salary, fx_salary_type FROM '.DB_PREFIX.'%s WHERE %s = %d AND fx_level = %d';
				$salary = 0;
				$order_money = 0; // 订单总金额
				// 计算佣金
				// 1. 如果设置商品佣金
				// OR 2. 如果设置等级佣金
				// OR 3. 如果设置全局佣金
				foreach ($order_item as $deal) {
					$rate = 0;
					$fx_salary_sql = sprintf($salary_sql,'deal_fx_salary', 'deal_id', $deal['deal_id'], $i);
					$fx_salary = $GLOBALS['db']->getRow($fx_salary_sql);
					if (!$fx_salary) { // 如果未设置商品佣金
						$fx_salary_sql = sprintf($salary_sql, 'fx_salary', 'level_id', $p_user['fx_level'], $i);
						$fx_salary = $GLOBALS['db']->getRow($fx_salary_sql);
						if (!$fx_salary) { // 如果未设置等级佣金
							$fx_salary_sql = sprintf($salary_sql, 'fx_salary', 'level_id', 0, $i);
							$fx_salary = $GLOBALS['db']->getRow($fx_salary_sql);
						}
					}
					// logger::write($fx_salary_sql, 'INFO', 3, 'fx_log');
					if ($fx_salary) {
						$rate = $fx_salary['fx_salary_type'] ? $deal['total_price'] : 1;
					}

					// $sale_log_format = '分销商 %s 售出 %d 件 %s';
					// $sale_log = sprintf($sale_log_format, $p_user['user_name'], $deal['number'], $deal['name']);
					// modify_fx_statements($deal['total_price']*, 0, $sale_log);

					$salary = $fx_salary['fx_salary'] * $rate;
					if ($salary > 0) {
						$salary_log_format = '%s 售出 %d 件 %s 获得推广佣金';
						
						if ($i == 0) {
							$log_user = '';
						} else {
							$log_user = '好友-'.$current_user['user_name'].(($i == 1) ? '' : '的'.$log_user);
						}
						$salary_log = sprintf($salary_log_format, $log_user, $deal['number'], $deal['name']);
						//$send_status = send_fx_user_salary($p_user['id'], /*$deal['total_price']*/0, $salary, $i+1, $salary_log);
						$send_status = send_fx_user_salary_new($p_user['id'], $salary, $salary_log,$deal['total_price']);
						if ($send_status) {
							// 会员针对每个上一级的返佣情况
							fx_user_reward($p_user['id'],$user_info['id'],$salary);
							// 更新订单商品的分销佣金信息
							update_fx_order_item_log($deal['id'], $salary, $i + 1);
							if(!$arr[$deal['id']]){
								$arr[$deal['id']]=true;
								modify_fx_statements($deal['total_price'], 0, $salary_log, $p_user['id']);
							}
							modify_fx_statements($salary, 2, $salary_log, $p_user['id']);
						}
					}
				}
				// 没有上一级推荐的停止计算
				if ($p_user['pid'] <= 0) {
					break;
				}
				$current_user = $p_user;
			}
		}
		// logger::write('返佣计算结束', 'INFO', 3, 'fx_log');
	}
}

/**
 * 支付方式
 * location_id      //门店ID
 * money    //总消费金额
 * payment_id        //支付ID
 * bank_id  //银行编号
 * account_money    //余额
 *
 * 返回 array(
 'total_price'	=>	$total_price,	商品总价
 'pay_price'		=>	$pay_price,     支付费用
 'pay_total_price'		=>	$total_price+$delivery_fee+$payment_fee-$user_discount,  应付总费用
 'payment_fee'	=>	$payment_fee,   支付手续费
 'payment_info'  =>	$payment_info,  支付方式
 'account_money'	=>	$account_money, 余额支付
 *              'promote_ids'=>     促销规则ID 逗号分隔
 *              'promote_data'=> 促销规则数据
 *
 */
function fx_pay_total($money,$payment=0,$bank_id=0,$account_money=0,$all_account_money=0,$user_id=0,$order_id=0) {
    
    $order_info=$GLOBALS['db']->getRow("select * from " . DB_PREFIX . "fx_buy_order where id=".$order_id);

    $pay_price = 0;
    $pay_amount = 0;

    //应支付金额
    $pay_price = $money;

    //余额支付
    if($all_account_money == 1)
    {
        $user_money = $GLOBALS['db']->getOne("select money from ".DB_PREFIX."user where id = ".$user_id);
         
        $account_money = $user_money;
    }


    if( $account_money >= $pay_price)
    {
        $account_money = $pay_price;
    }else{
        $account_money=0;
    }

    $pay_price = $pay_price - $account_money;

    //支付手续费
    if($payment!=0)
    {
        if($pay_price>0)
        {
            $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment);
            $directory = APP_ROOT_PATH."system/payment/";
            $file = $directory. '/' .$payment_info['class_name']."_payment.php";
            if(file_exists($file))
            {
                require_once($file);
                $payment_class = $payment_info['class_name']."_payment";
                $payment_object = new $payment_class();
                if(method_exists($payment_object,"get_name"))
                {
                    $payment_info['name'] = $payment_object->get_name($bank_id);
                }
            }



            if($payment_info['fee_type']==0) //定额
            {
                $payment_fee = $payment_info['fee_amount'];
            }
            else //比率
            {
                $payment_fee = $pay_price * $payment_info['fee_amount'];
            }
            $pay_price = $pay_price + $payment_fee;
        }
    }
    else
    {
        $payment_fee = 0;
    }




    $result = array(
        'pay_price' =>$pay_price,
        'total_price' => $money,
        'payment_fee' => $payment_fee,
        'payment_info' => $payment_info,
        'account_money' => $account_money
    );


    return $result;

}

/**
 * 
 **/
function fx_buy_order_log($log_info,$order_id)
{
    $data['id'] = 0;
    $data['log_info'] = $log_info;
    $data['log_time'] = NOW_TIME;
    $data['order_id'] = $order_id;
    $GLOBALS['db']->autoExecute(DB_PREFIX."fx_buy_order_log", $data);
}

/**
 * 自动结单检测，如通过则结单
 * 自动结单规则
 * 注：自动结单条件
 * 1. 团购券全部验证成功
 * 2. 商品全部已收货
 * 3. 商品验证部份收货部份，其余退款
 * 结单后的商品不可再退款，不可再验证，不可再发货，可删除
 * @param unknown_type $order_id
 * return array("status"=>bool,"info"=>str)
 */
function fx_buy_auto_over_status($order_id)
{
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
    if($order_info)
    {
        if($order_info['pay_status']<>2)
        {
            return array("status"=>false,"info"=>"订单未支付");
        }
        if($order_info['order_status']<>0)
        {
            return array("status"=>false,"info"=>"订单已结单");
        }

        fx_buy_over_order($order_id);
        	
        return array("status"=>true,"info"=>"结单成功");
    }
    else
    {
        return array("status"=>false,"info"=>"订单不存在");
    }
}

/**
 * 结单操作
 * @param unknown_type $order_id
 */
function fx_buy_over_order($order_id)
{

    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where order_status = 0 and id = ".$order_id);
    if($order_info)
    {
        $GLOBALS['db']->query("update ".DB_PREFIX."fx_buy_order set order_status = 1 where order_status = 0 and id = ".$order_id);
        if(!$GLOBALS['db']->affected_rows())
        {
            return;  //结单失败
        }
        //上级返佣
        $user=$GLOBALS['db']->getRow("select id,user_name,pid,agency_id from ".DB_PREFIX."user where id=".$order_info['user_id']);
        $pid=$GLOBALS['db']->getRow("select id,user_name from ".DB_PREFIX."user where id=".$user['pid']." and is_fx=1");

        if(!empty($pid)&& $order_info['total_price']>0){
            send_fx_buy_salary($pid['id'],$order_info['rebate'],"推荐注册的会员".$user['user_name']."购买了分销资格，获得推荐佣金".round($order_info['rebate'],2)."元。",$order_info['total_price']);
            modify_fx_statements($order_info['total_price'],0,"推荐注册的会员".$user['user_name']."购买了分销资格，获得推荐佣金".round($order_info['rebate'],2)."元。",$user['pid']);
			modify_fx_statements($order_info['rebate'],4,"推荐注册的会员".$user['user_name']."购买了分销资格，获得推荐佣金".round($order_info['rebate'],2)."元。",$user['pid']);
            $reward['pid']=$user['pid'];
            $reward['user_id']=$user['id'];
            $reward['money']=$order_info['rebate'];
            $GLOBALS['db']->autoExecute(DB_PREFIX."fx_user_reward",$reward,"INSERT","","SILENT");
        }
        if($user['agency_id']>0&&$order_info['fx_charge_price']!=0){
            $agency=$GLOBALS['db']->getRow("select id,name from ".DB_PREFIX."agency where id=".$user['agency_id']);
            modify_agency_account($order_info['fx_charge_price'],$user['agency_id'],5,"网宝【".$agency['name']."】推荐会员【".$user['user_name']."】购买网宝成功，获得推荐佣金");
        }
        fx_buy_order_log($order_info['order_sn']."订单完结", $order_id);
        
        send_msg_new($user['id'],"你已成功购买分销资格，快去邀请好友吧！", 'notify', array('type' => 11));
        send_msg_new($user['pid'],"推荐注册的会员".$user['user_name']."购买了分销资格，获得推荐佣金".round($order_info['rebate'],2)."元。", 'account', array('type' => 13));
    }
}

/**
 * 用户消费成功，发放三级推荐商家入驻佣金
 * @param unknown_type $order_id
 * @param unknown_type $type $type等于0为普通订单，$type等于1为买单订单
 */
function send_user_supplier_salary($order_id,$type=0){

	//取出推荐商家佣金设置
	if($type==0){
		$ref_salary_conf = unserialize(app_conf("REF_SALARY"));
		$ref_salary_switch=intval($ref_salary_conf['ref_salary_switch']);
		if($ref_salary_switch==0){//判断后台是否开启推荐商家入驻三级分销
			return false;
		}
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		$supplier_info = $GLOBALS['db']->getRow("select id,name,ref_user_id,user_id from ".DB_PREFIX."supplier where id=".$order_info['supplier_id']);
		if($supplier_info['ref_user_id']==0){
			return false;
		}
	}elseif($type==1){
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$order_id);
		$supplier_info = $GLOBALS['db']->getRow("select id,name,ref_user_id,user_id,is_store_payment_fx,store_payment_fx_salary from ".DB_PREFIX."supplier where id = ".$order_info['supplier_id']);
		if($supplier_info['is_store_payment_fx']==0||$supplier_info['ref_user_id']==0){
			return false;
		}
		$GLOBALS['db']->query('UPDATE '.DB_PREFIX.'store_pay_order SET is_participate_ref_salary = 1 WHERE id='.$order_id);
		$ref_salary_conf = unserialize($supplier_info['store_payment_fx_salary']);
		$ref_salary_conf['is_store_payment_fx']=$supplier_info['is_store_payment_fx'];
		
	}
	$ref_salary_limit = $ref_salary_conf['ref_salary_limit'];
	$ref_salary = $ref_salary_conf['ref_salary'];
	$user_lever_1_id = $supplier_info['ref_user_id'];
	if($type==0 && ($order_info['type']==5||$order_info['type']==6)){  //充值订单不返佣金
		if($order_info['type']==5){
			$deal_order_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where is_shop=0 and order_id = '".$order_id."' and ((is_coupon=1 and consume_count>0) or is_coupon=0) ");
		}else{
			$deal_order_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where buy_type=0 and order_id = '".$order_id."' and refund_status <> 2");
		}
		$total_price=0;
		foreach($deal_order_items as $k=>$item)
		{
			if($item['is_shop']==1){
				$total_price+=$item['total_price'];
			}else{
				if($item['is_coupon']==0){
					$total_price+=$item['total_price'];
				}else{
					$deal_coupon = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where order_id = '".$order_id."' and order_deal_id='".$item['id']."' and is_balance=1 ");
					if(count($deal_coupon)==1){
						if($deal_coupon['0']['deal_type']==1){
							$total_price+=$item['total_price'];
						}else{
							$total_price+=$item['unit_price'];
						}
					}else{
						$total_price+=$item['unit_price']*count($deal_coupon);
					}
				}
			}
		}
		$num=$GLOBALS['db']->getOne("select SUM(number) from ".DB_PREFIX."deal_order_item where order_id = '".$order_id."'");
		$log='推荐商家【'.$supplier_info['name'].'】，售出了【'.$deal_order_items[0]['name'].'】等'.$num.'件商品，获得推广佣金';
		$statements_type=5;
	}elseif($type==1){  //买单订单
		$total_price=$order_info['total_price'];
		$log='推荐商家【'.$supplier_info['name'].'】，成交了买单订单'.$order_info['order_sn'].'，获得推广佣金';
		$statements_type=6;
	}
	if($total_price<$ref_salary_limit){//订单金额 ≥ 输入值时，分销员才可获得分佣
		return false;
	}
	require_once APP_ROOT_PATH."system/model/user.php";
	$ref_salary_total=0;
	$ref_salary_all=array();
	$ref_salary_all['ref_salary_conf']=$ref_salary_conf;
	$ref_salary_all['ref_salary_all']['0']=array();
	$ref_salary_all['ref_salary_all']['1']=array();
	$ref_salary_all['ref_salary_all']['2']=array();
	$ref_salary_all['log']=$log;
	if($user_lever_1_id){
		$user_lever_1_info = load_user($user_lever_1_id);
		$arr=array();
		$arr['user_id']=$user_lever_1_info['id'];
		$arr['user_name']=$user_lever_1_info['user_name'];
		$arr['salary']=0;
		if($user_lever_1_info['is_fx']==1 && $user_lever_1_info['is_effect']==1 && $user_lever_1_info['is_delete']==0){ //给第一级发放推荐商家入驻佣金
			
			$salary=$total_price*$ref_salary[0]/100>=0.01?round($total_price*$ref_salary[0]/100,2):0;
			$is_send1 = send_fx_user_salary_new($user_lever_1_id, $salary, $log,$total_price);
			if($is_send1){
				fx_user_reward($arr['user_id'],0,$salary,$supplier_info['id']);
				$ref_salary_total+=$salary;
				$arr['salary']=$salary;
				modify_fx_statements($salary,$statements_type,$log,$user_lever_1_id);
			}
		}
		$ref_salary_all['ref_salary_all']['0']=$arr;
		if($user_lever_1_info['pid']>0){
			$user_lever_2_info = load_user($user_lever_1_info['pid']);
			$arr=array();
			$arr['user_id']=$user_lever_2_info['id'];
			$arr['user_name']=$user_lever_2_info['user_name'];
			$arr['salary']=0;
			if($user_lever_2_info['is_fx']==1 && $user_lever_2_info['is_effect']==1 && $user_lever_2_info['is_delete']==0){ //给第二级发放推荐商家入驻佣金
				$salary=$total_price*$ref_salary[1]/100>=0.01?round($total_price*$ref_salary[1]/100,2):0;
				$is_send2 = send_fx_user_salary_new($user_lever_2_info['id'], $salary, $log,$total_price);
				if($is_send2){
					fx_user_reward($arr['user_id'],0,$salary,$supplier_info['id']);
					$ref_salary_total+=$salary;
					$arr['salary']=$salary;
					modify_fx_statements($salary,$statements_type,$log,$user_lever_2_info['id']);
				}
			}
		
			$ref_salary_all['ref_salary_all']['1']=$arr;
			if($user_lever_2_info['pid']>0){
				$user_lever_3_info = load_user($user_lever_2_info['pid']);
				$arr=array();
				$arr['user_id']=$user_lever_3_info['id'];
				$arr['user_name']=$user_lever_3_info['user_name'];
				$arr['salary']=0;
				if($user_lever_3_info['is_fx']==1 && $user_lever_3_info['is_effect']==1 && $user_lever_3_info['is_delete']==0){ //给第三级发放推荐商家入驻佣金
					$salary=$total_price*$ref_salary[2]/100>=0.01?round($total_price*$ref_salary[2]/100,2):0;
					$is_send3 = send_fx_user_salary_new($user_lever_3_info['id'], $salary, $log,$total_price);
					if($is_send3){
						fx_user_reward($arr['user_id'],0,$salary,$supplier_info['id']);
						$ref_salary_total+=$salary;
						$arr['salary']=$salary;
						modify_fx_statements($salary,$statements_type,$log,$user_lever_2_info['id']);
					}
				}
				$ref_salary_all['ref_salary_all']['2']=$arr;
			}
		}
	}
	if($is_send1||$is_send2||$is_send3){
		$arr=array();
		$arr['is_participate_ref_salary']=1;
		$arr['ref_salary_total']=$ref_salary_total;
		$arr['ref_salary_all']=serialize($ref_salary_all);
		if($type==0){
			$arr['ref_total']=$total_price;
			$table_name="deal_order";
		}else{
			$table_name="store_pay_order";
		}
		$GLOBALS['db']->autoExecute(DB_PREFIX.$table_name,$arr,"UPDATE","id=".$order_id);
		modify_fx_statements($total_price, 0,$log,$user_lever_1_id);
	}
}
/**
 *  会员针对每个上一级的返佣情况
 */
function fx_user_reward($pid,$user_id=0,$money,$supplier_id=0){
	$fx_reward = array('pid'=>$pid, 'money' => $money);
	$fx_reward_where =' pid ='.$pid;
	if($user_id>0){
		$fx_reward['user_id']=$user_id;
		$fx_reward_where.=' and user_id='.$user_id;
	}else{
		$fx_reward['supplier_id']=$supplier_id;
		$fx_reward_where.=' and supplier_id='.$supplier_id;
	}
	$fx_user_reward=$GLOBALS['db']->getAll('select * from '.DB_PREFIX.'fx_user_reward where '.$fx_reward_where);
	if(count($fx_user_reward)==0){
		$GLOBALS['db']->autoExecute(DB_PREFIX.'fx_user_reward', $fx_reward, 'INSERT', '', 'SILENT');
	}else{
		if(count($fx_user_reward)==1){
			$rewardSql = 'UPDATE '.DB_PREFIX.'fx_user_reward SET money = money + '.$money.' WHERE '.$fx_reward_where;
			$GLOBALS['db']->query($rewardSql);
		}
	}
}
?>