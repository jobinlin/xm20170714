<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//计算消费金额
/**
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
function count_store_pay_total($location_id, $money, $payment=0,$bank_id=0,$account_money=0,$all_account_money=0,$user_id=0,$order_id=0) {
    //获取门店信息
    $location_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "supplier_location where id=" . $location_id);
    //根据门店查询商户促销规则
    $promote_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "promote where supplier_id = " . $location_info['supplier_id']);

    $order_info=$GLOBALS['db']->getRow("select * from " . DB_PREFIX . "store_pay_order where id=".$order_id);
    
    //测试数据 21 | 100.0000 | 29 | 0 | 0 | 1
    $pay_price = 0;
    $pay_amount = 0;
    $discount_price = 0;
    $promote_ids = array();
    $promote_data = array();
    $promote_arr = array();
    //计算促销优惠
    if ($promote_list && $order_id==0) {
        
        foreach ($promote_list as $k => $v) {
            $directory = APP_ROOT_PATH . "system/promote/";
            $file = $directory . '/' . $v['class_name'] . "_promote.php";
            if (file_exists($file)) {
                require_once($file);
                $promote_class = $v['class_name'] . "_promote";
                $promote_object = new $promote_class();
                $tmp_arr = array();
                $tmp_arr = $promote_object->count_store_pay($money,$v['config']);

                $pay_amount = $tmp_arr['pay_amount'];
                $discount_price += $tmp_arr['discount_price'];
                $promote_ids[] = $v['id'];
                $promote_data[] = $v;
                $promote_unit=array();
                $promote_unit['discount_price']=$tmp_arr['discount_price'];
                $promote_unit['class_name']=$v['class_name'];
                $promote_unit['name']=$v['name'];
                $promote_unit['description']=$v['description'];
                $promote_unit['discount_role']=$v['supplier_or_platform'];
                $promote_arr[]=$promote_unit;
                
            }
        }
        
        $promote_ids = implode(",", $promote_ids);
        $promote_data = serialize($promote_data);
        
        $promote_arr = serialize($promote_arr);
    }
    
    if($order_id > 0){
    	$discount_price=$order_info['discount_price'];
		$exchange_money=$order_info['exchange_money'];
    }
    
    //应支付金额
    $pay_price = $money-$discount_price-$exchange_money-$order_info['pay_amount'];
    
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
        'discount_price' => $discount_price,
        'payment_fee' => $payment_fee,
        'payment_info' => $payment_info,
        'account_money' => $account_money,
        'promote_ids' => $promote_ids,
    	'promote' => $promote_arr,
        'promote_data' => $promote_data);
    
    
    return $result;
    
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
function store_pay_auto_over_status($order_id)
{	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$order_id);
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
		
		store_pay_over_order($order_id); //充值单只要支付过就结单
			
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
function store_pay_over_order($order_id)
{	
	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where order_status = 0 and id = ".$order_id);
	if($order_info)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set order_status = 1 where order_status = 0 and id = ".$order_id);
		if(!$GLOBALS['db']->affected_rows())
		{
			return;  //结单失败
		}
		require_once(APP_ROOT_PATH."system/model/fx.php");
		if($order_info['pay_status']==2){
			send_user_supplier_salary($order_id , $type=1);
		}
		store_pay_order_log($order_info['order_sn']."订单完结", $order_id);
		
	}
}


function store_pay_order_log($log_info,$order_id)
{
	$data['id'] = 0;
	$data['log_info'] = $log_info;
	$data['log_time'] = NOW_TIME;
	$data['order_id'] = $order_id;
	$GLOBALS['db']->autoExecute(DB_PREFIX."store_pay_order_log", $data);
}


//查询会员充值订单
function get_store_pay_incharge($limit,$supplier_id)
{
	$supplier_id = intval($supplier_id);

	$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_order where supplier_id = ".$supplier_id." and type = 1 and is_delete = 0 order by create_time desc limit ".$limit);
	$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_order where user_id = ".$user_id." and type = 1 and is_delete = 0");
	foreach($list as $k=>$v)
	{
		$list[$k]['payment_notice'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$v['id']);
		$list[$k]['payment'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$v['payment_id']);
	}
	return array("list"=>$list,'count'=>$count);
}
//会员买单积分抵扣扣积分
function store_pay_score_purchase_paid($score_purchase,$order_id)
{
	if($order_id>0){
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where order_status = 0 and id = ".$order_id);
		$user_score = $GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$order['user_id']);
		$msg='订单号'.$order['order_sn'].'，使用'.$score_purchase['user_use_score'].'积分，抵扣'.round($order['exchange_money'],2).'元';
		store_pay_order_log("积分抵现成功",$order['id']);
		modify_account(array('money'=>0,'score'=>'-'.$score_purchase['user_use_score']),$order['user_id'],$msg);
	}
	return true;
}
//会员买单订单长时间未支付，定时取消
function syn_cancel_store_order($order_id)
{

	if($order_id>0){
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where is_delete=0 and id = ".$order_id);
		
		if($order){
			$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set is_delete = 1 where is_delete = 0 and  id = ".$order['id']);
			if($order['pay_status']==0&&($order['pay_amount']>0||$order['exchange_money'])){
				$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set pay_amount = 0,exchange_money = 0 where id = ".$order['id']);
				require_once(APP_ROOT_PATH."system/model/user.php");
				if($order['pay_amount']>0)
				{
					modify_account(array("money"=>$order['pay_amount'],), $order['user_id'],"订单超时取消，退回余额支付 ");
					store_pay_order_log("交易被关闭，退回余额支付 ".$order['pay_amount']." 元",$order['id']);
				}

				if($order['exchange_money'] > 0){
					$score_purchase=unserialize($order['score_purchase']);

					modify_account(array("score" =>$score_purchase['user_use_score'] ), $order['user_id'], "订单超时取消，退回积分抵扣 ");
					store_pay_order_log("交易被关闭，积分抵扣退回 ".$order['pay_amount']." 元",$order['id']);
				}
				
			}
		}
	}
	return true;
}

