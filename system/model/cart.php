<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


/**
 *
 * @param boolean $reload 是否重新加载，废弃
 * @param boolean $is_wap 是否是wap端
 * @param boolean $wap_show_disable wap端是否显示未选择中商品，用于wap端购物车和订单提交页面分别显示
 * @param int $in_cart 购物车中的商品是否需要经过购物车页面，1为要经过购物车页面，0为不需要经过购物车页面，直接进入订单提交页面
 */

function load_cart_list($deal_id=0,$wap_show_disable=false)
{

    if($wap_show_disable){
        $deal_id=0; //如果进入购物车，则$deal_id=0;调用 所有购物车中的商品信息
    }
    $base_sql = "select c.*,d.icon,d.id as did,d.delivery_type,d.uname as duname,d.is_delivery as is_delivery,d.allow_promote as allow_promote,d.user_min_bought as user_min_bought,d.user_max_bought as user_max_bought,d.is_shop,d.allow_user_discount from ".DB_PREFIX."deal_cart as c left join ".DB_PREFIX."deal as d on c.deal_id = d.id where ";
    $base_total_sql = "select sum(total_price) as total_price,sum(return_total_score) as return_total_score,sum(return_total_money) as return_total_money ,count(*) as total_num from ".DB_PREFIX."deal_cart where ";
    if($GLOBALS['user_info']){
        
        if($deal_id > 0){
            $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.deal_id=".$deal_id." and c.user_id = ".intval($GLOBALS['user_info']['id']));
            $total_data = $GLOBALS['db']->getRow($base_total_sql."deal_id=".$deal_id." and user_id = ".intval($GLOBALS['user_info']['id']));
        }else{
            if($wap_show_disable){
                $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.in_cart=1 and c.user_id = ".intval($GLOBALS['user_info']['id']));
                $total_data = $GLOBALS['db']->getRow($base_total_sql."in_cart=1 and user_id = ".intval($GLOBALS['user_info']['id']));
            }else{
                $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.in_cart=1 and c.is_effect=1 and c.user_id = ".intval($GLOBALS['user_info']['id']));      
                $total_data = $GLOBALS['db']->getRow($base_total_sql."in_cart=1 and is_effect=1 and user_id = ".intval($GLOBALS['user_info']['id']));
            }
        }
    }else{      
        if($deal_id > 0){
             $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.deal_id=".$deal_id." and c.user_id =0 and c.session_id = '".es_session::id()."'");
             $total_data = $GLOBALS['db']->getRow($base_total_sql."deal_id=".$deal_id." and user_id =0 and session_id = '".es_session::id()."'"); 
        }else{
            if($wap_show_disable){
                $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.in_cart=1 and c.user_id =0 and c.session_id = '".es_session::id()."'");
                $total_data = $GLOBALS['db']->getRow($base_total_sql."in_cart=1 and user_id =0 and session_id = '".es_session::id()."'");
            }else{
                $cart_list_res = $GLOBALS['db']->getAll($base_sql."c.in_cart=1 and c.is_effect=1 and c.user_id =0 and c.session_id = '".es_session::id()."'");
                $total_data = $GLOBALS['db']->getRow($base_total_sql."in_cart=1 and is_effect=1 and user_id =0 and session_id = '".es_session::id()."'");
            }
        }
    }

    $cart_list = array();
    foreach($cart_list_res as $k=>$v) {
        if($v['duname']!=""){
            $v['url'] = url("index","deal#".$v['duname']);
        } else {
            $v['url'] = url("index","deal#".$v['did']);
        }

        if($v['supplier_id']>0){
            $v['supplier_part'] = $v['supplier_id'];
        }else{
            if($v['delivery_type']==1){//平台物流配送
                $v['supplier_part'] = 'p_wl';
            }elseif ($v['delivery_type']==2){//平台无需配送 
                $v['supplier_part'] = 0;
            }elseif ($v['delivery_type']==3){//平台驿站配送 
                $v['supplier_part'] = 'p_yz';
            }
        }   
        $cart_list[$v['id']] = $v;
        
    }

    $result = array("cart_list"=>$cart_list,"total_data"=>$total_data);

    es_session::set("cart_result", $result);
    return $result;

}

/**
 * 刷新购物车，过期超时
 */
function refresh_cart_list()
{
	$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where is_disable=1");
		
}


//计算购买价格
/**
 * region_id      //配送最终地区
 * delivery_id    //配送方式
 * payment        //支付ID
 * account_money  //支付余额
 * all_account_money  //是否全额支付
 * ecvsn  //代金券帐号
 * ecvpassword  //代金券密码
 * goods_list   //统计的商品列表
 * $paid_account_money 已支付过的余额
 * $paid_ecv_money 已支付过的代金券
 * 
 * 返回 array(
		'total_price'	=>	$total_price,	商品总价
		'pay_price'		=>	$pay_price,     支付费用
		'pay_total_price'		=>	$total_price+$delivery_fee+$payment_fee-$user_discount,  应付总费用
		'delivery_fee'	=>	$delivery_fee,  运费
		'delivery_fee_supplier' => array(supplier_id=>delivery_fee,......) //每个商家的运费
		'delivery_info' =>  $delivery_info, 配送方式
		'payment_fee'	=>	$payment_fee,   支付手续费
		'payment_info'  =>	$payment_info,  支付方式
		'user_discount'	=>	$user_discount, 会员折扣
		'account_money'	=>	$account_money, 余额支付	
		'ecv_money'		=>	$ecv_money,		代金券金额
		'ecv_data'		=>	$ecv_data,      代金券数据
		'region_info'	=>	$region_info,	地区数据
		'is_delivery'	=>	$is_delivery,   是否要配送
		'return_total_score'	=>	$return_total_score,   购买返积分
		'return_total_money'	=>	$return_total_money    购买返现
		'buy_type'	=>	0,1 //1为积分商品
		
 */
function count_buy_total($region_id,$consignee_id,$payment,$account_money,$all_account_money,$ecvsn,$ecvpassword,$goods_list,$paid_account_money = 0,$paid_ecv_money = 0,$bank_id = '',$all_score=0,$paid_exchange_money=0,$youhui_ids=array(),$paid_youhui_money=0)
{
	//获取商品总价
	//计算运费
	$pay_price = 0;   //支付总价
	$total_price = 0;
	$return_total_score = 0;
	$return_total_money = 0;
	$is_delivery = 0;
	$is_consignment=1;
	$free_delivery_item = 0;  //默认为免运费   需要所有商品满足免运费条件
	$deal_id = 0;
	$deal_count = 0;
	$deal_item_count = 0;
	$buy_type = 0;
	$is_pick = 1;
	$delivery_id = 0;
    $delivery_fee = 0;  //默认运费为0;
    
    //先计算用户等级折扣
	$user_id = intval($GLOBALS['user_info']['id']);
	$user_discount_percent = 1;
	$user_discount = 0; // 折扣金额
	if ($user_id) {
		$user_discount_percent = getUserDiscount($user_id);
	}

	if($youhui_ids){
	  $youhui_ids = array_unique($youhui_ids);
	}

	$deal_ids = array();
	$supplier_ids=array();

	$good_total_data = array();
	$good_origin_total_data = array();
	foreach($goods_list as $k=>$v)
	{
        if(in_array($v['supplier_id'], $supplier_ids)==false){
            $supplier_ids[]=$v['supplier_id'];
        }
		
        
        
		$deal_ids[] = $v['deal_id'];
		if($v['buy_type']==1)$buy_type=1;
		$deal_user_discount = 0;
		if ($v['allow_user_discount']) {
		    $deal_user_discount =  round($v['unit_price'] * (1-$user_discount_percent), 2) * $v['number'];
		    
			$user_discount += $deal_user_discount;
			// $user_discount += ($v['total_price'] * (1 - $user_discount_percent));
		}
		$good_total_data[$v['supplier_part']]+=$v['total_price']-$deal_user_discount;
		$good_origin_total_data[$v['supplier_part']]+=$v['total_price'];
		$total_price += $v['total_price'];
		$return_total_money+=$v['return_total_money'];
		$return_total_score+=$v['return_total_score'];
		if($v['is_pick']==0)$is_pick=0;
		
		$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
		if($deal_info['is_delivery'] == 1&&$is_pick == 0) //需要配送叠加重量
		{
		    $is_delivery = 1;
		}

		if($deal_info['is_shop']==1 && $deal_info['delivery_type']==2){
		    $is_consignment=1;
		}elseif($deal_info['is_shop']==1 && $is_pick == 0){
		    $is_consignment=1;
		}elseif($deal_info['is_shop']==0){
		    $is_consignment=0;
		}
		
	}

	if($is_pick == 1 && $consignee_id ==0 ){
	    $is_consignment=0;
	}

	$region_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".intval($region_id));
	if($region_info['region_level']!=4&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."delivery_region where pid = ".intval($region_id))==0)
	{
		$region_info['region_level'] = 4;
	}
	
	$delivery_list = array();
	if($consignee_id > 0){
	    $delivery_list = get_express_fee($goods_list,$consignee_id);
	    if($delivery_list){
	        foreach($delivery_list as $k=>$v){
	            $supplier_data[$k]['delivery_fee'] = $v['total_fee'];
	            $delivery_fee+=$v['total_fee'];
	        }
	    }
	}
	
	
	foreach($good_total_data as $k=>$v){
	    $good_total_data[$k]=$v + $supplier_data[$k]['delivery_fee'];
	}
	
	$count_delivery_fee = $delivery_fee;
    
	$pay_price = $total_price + $delivery_fee; //加上运费
    
	$pay_price = $pay_price - $paid_account_money - $paid_ecv_money-$paid_exchange_money - $paid_youhui_money;
	
	
	/*$group_info = $GLOBALS['db']->getRow("select g.* from ".DB_PREFIX."user as u left join ".DB_PREFIX."user_group as g on u.group_id = g.id where u.id = ".$user_id);
	if(intval($group_info['id'])>0&&floatval($group_info['discount'])>0&&$total_price>0)
	$user_discount = $total_price-$total_price*$group_info['discount'];//$total_price*floatval(1-floatval($group_info['discount']));	
	else
	$user_discount = 0;*/
	
	$pay_price = $pay_price - $user_discount; //扣除用户折扣
	//余额支付
	$user_money = $GLOBALS['db']->getOne("select money from ".DB_PREFIX."user where id = ".$user_id);
	if($all_account_money == 1)
	{
		$account_money = $user_money;
	}
    
	if($account_money>$user_money)
	$account_money = $user_money;  //余额支付量不能超过帐户余额
	
	//计算优惠券支付
	$youhui_price=0;
	$youhui_data = array();
	$pay_limit_price = $total_price + $delivery_fee - $user_discount;

	if($youhui_ids){
	    foreach ($youhui_ids as $t => $v){
	        $youhui_sql="select y.youhui_value from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as y on y.id=yl.youhui_id
                  where yl.confirm_time=0 and (expire_time=0 or expire_time>".NOW_TIME.") and yl.id=".$v." and y.supplier_id in (".implode(",", $supplier_ids).")
	              and (y.start_use_price<=".$pay_limit_price." or y.start_use_price=0) and yl.user_id=".$user_id;
	        
	        $youhui_price_item=$GLOBALS['db']->getOne($youhui_sql);
	        
	        if($good_total_data[$t] < $youhui_price_item){
	            $youhui_price_item = $good_total_data[$t];
	        }
	        $youhui_data[$t]['youhui_log_id']=$v;
	        $youhui_data[$t]['youhui_money']=$youhui_price_item;
	        $youhui_data[$t]['total_price']=$good_total_data[$t];
	        $youhui_data[$t]['origin_total_price']=$good_origin_total_data[$t];
	        $youhui_price+=$youhui_price_item;
	    }
	}
	if($youhui_price>=$pay_price){
	    $ecv_no_use_status=1;
	    $youhui_price=$pay_price;
	}
	$pay_price=$pay_price-$youhui_price;
	if($paid_youhui_money > 0){
	    $youhui_price = $paid_youhui_money;
	}


	//开始计算代金券
	$now = NOW_TIME;
	$ecv_sql = "select e.* from ".DB_PREFIX."ecv as e left join ".
				DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.sn = '".
				$ecvsn."' and ((e.begin_time <> 0 and e.begin_time < ".$now.") or e.begin_time = 0) and ".
				"((e.end_time <> 0 and e.end_time > ".$now.") or e.end_time = 0) and ((e.use_limit <> 0 and e.use_limit > e.use_count) or (e.use_limit = 0)) ".
				"and (e.user_id = ".$user_id." or e.user_id = 0) and (et.start_use_price<=".$pay_limit_price." or et.start_use_price=0)";
	
	$ecv_data = $GLOBALS['db']->getRow($ecv_sql);
	$ecv_money = $ecv_data['money'];
	
	$exchange_money=0;
	if($paid_exchange_money>0){
		$exchange_money=$paid_exchange_money;
	}else{
		if($return_total_score<0){
			$score_purchase['score_purchase_switch']=0;
		}else{
			if(app_conf("SCORE_PURCHASE_SWITCH")==1){
				if($paid_exchange_money>0){//已参与过积分抵现
					$exchange_money=$paid_exchange_money;
					$score_purchase['score_purchase_switch']=0;
				}else{
					if($pay_price<=$ecv_money){
						$score_purchase['score_purchase_switch']=0;
					}else{
					   
						$user_score = $GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$user_id);
						$score_purchase=score_purchase_count($user_score,($total_price + $delivery_fee-$user_discount),($pay_price-$ecv_money));

						$score_purchase['score_purchase_switch']=1;

						if($score_purchase['exchange_money']>0){

							if($all_score==1){
								$exchange_money=$score_purchase['exchange_money'];//扣除积分抵现
								$pay_price=$pay_price-$exchange_money;
							}
						}else{
							$score_purchase['score_purchase_switch']=0;
						}
					}
				}
			}else{
				$score_purchase['score_purchase_switch']=0;
			}
		}
	}

	// 当余额 + 代金券 > 支付总额时优先用代金券付款  ,代金券不够付，余额为扣除代金券后的余额
	
	if($ecv_money + $account_money > $pay_price)
	{
		if($ecv_money >= $pay_price)
		{
			$ecv_use_money = $pay_price;
			$account_money = 0;
		}
		else
		{
			$ecv_use_money = $ecv_money;
			$account_money = $pay_price - $ecv_use_money;
		}
	}
	else
	{
		$ecv_use_money = $ecv_money;
	}

    $pay_price = $pay_price - $ecv_use_money - $account_money;

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
	
	if($account_money<0)$account_money = 0;

	$result = array(
		'total_price'	=>	$total_price,
		'pay_price'		=>	$pay_price,
		'pay_total_price'		=>	$total_price+$delivery_fee+$payment_fee-$user_discount-$exchange_money,
		'delivery_fee'	=>	$delivery_fee,
		'record_delivery_fee'	=>	$delivery_fee,
	    'count_delivery_fee'  => $count_delivery_fee,
		'delivery_fee_supplier' => array(),
		'delivery_info' =>  $delivery_list,
		'payment_fee'	=>	$payment_fee,
		'payment_info'  =>	$payment_info,
		'user_discount'	=>	$user_discount,
		'account_money'	=>	$account_money,
	    'youhui_money'  =>	$youhui_price,
	    'youhui_data'  =>	$youhui_data,
		'ecv_money'		=>	$ecv_use_money,
		'ecv_data'		=>	$ecv_data,
		'exchange_money'=>	$exchange_money,
		'score_purchase'=>	$score_purchase,
		'region_info'	=>	$region_info,
		'is_delivery'	=>	$is_delivery,
		'return_total_score'	=>	$return_total_score,
		'return_total_money'	=>	$return_total_money,
		'paid_account_money'	=>	$paid_account_money,
		'paid_ecv_money'	=>	$paid_ecv_money,
		'buy_type'	=> $buy_type,
		'is_pick' => $is_pick,
	    'is_consignment'  =>  $is_consignment,
	    'ecv_no_use_status'=>$ecv_no_use_status,
	);
	
	//以下对促销接口进行实现
	
	$allow_promote = 1; //默认为支持促销接口
		foreach($goods_list as $k=>$v)
		{
			$allow_promote = $GLOBALS['db']->getOne("select allow_promote from ".DB_PREFIX."deal where id = ".$v['deal_id']);
			if($allow_promote == 0)
			{
				break;
			}
		}
	if($allow_promote==1)
	{
		$promote_list = load_auto_cache("cache_promote");
				
		foreach($promote_list as $k=>$v)
		{
					$directory = APP_ROOT_PATH."system/promote/";
					$file = $directory. '/' .$v['class_name']."_promote.php";
					if(file_exists($file))
					{
						require_once($file);
						$promote_class = $v['class_name']."_promote";
						$promote_object = new $promote_class();
						$result = $promote_object->count_buy_total($region_id,
										$delivery_id,
										$payment,
										$account_money,
										$all_account_money,
										$ecvsn,
										$ecvpassword,
										$goods_list,
										$result,
										$paid_account_money,
										$paid_ecv_money,
										$result);
						
					}
	
		}
	}
    
	return $result;
}


/**
 * 
 * 创建付款单号
 * @param $money 付款金额
 * @param $order_id 订单ID
 * @param $payment_id 付款方式ID
 * @param $memo 付款单备注
 * @param $ecv_id 如为代金券支付，则指定代金券ID
 * return payment_notice_id 付款单ID
 * 
 */
function make_payment_notice($money,$order_id,$payment_id,$memo='',$ecv_id=0,$payment_config=array())
{
    if($money > 0){
        $notice['create_time'] = NOW_TIME;
        $notice['order_id'] = $order_id;
        $notice['user_id'] = $GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."deal_order where id = ".$order_id);
        $notice['payment_id'] = $payment_id;
        $notice['memo'] = $memo;
        $notice['money'] = $money;
        $notice['ecv_id'] = $ecv_id;
        $notice['order_type'] = 3;  //普通订单

		$notice['payment_config']=serialize($payment_config);
        do{
            $notice['notice_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
            $GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$notice,'INSERT','','SILENT');
            $notice_id = intval($GLOBALS['db']->insert_id());
        }while($notice_id==0);
        return $notice_id;
    }
}


/**
 *
 * 创建付款单号
 * @param $money 付款金额
 * @param $order_id 订单ID
 * @param $payment_id 付款方式ID
 * @param $memo 付款单备注
 * @param $ecv_id 如为代金券支付，则指定代金券ID
 * return payment_notice_id 付款单ID
 *
 */
function make_store_pay_payment_notice($money,$order_id,$payment_id,$memo='',$ecv_id=0)
{
    if($money > 0){
    	$notice['create_time'] = NOW_TIME;
    	$notice['order_id'] = $order_id;
    	$notice['user_id'] = $GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."store_pay_order where id = ".$order_id);
    	$notice['order_type'] =4;//会员买单
    	$notice['payment_id'] = $payment_id;
    	$notice['memo'] = $memo;
    	$notice['money'] = $money;
    	$notice['ecv_id'] = $ecv_id;
    	do{
    		$notice['notice_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
    		$GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$notice,'INSERT','','SILENT');
    		$notice_id = intval($GLOBALS['db']->insert_id());
    	}while($notice_id==0);
    	return $notice_id;
    }
}


/**
 *
 * 创建分销资格付款单号
 * @param $money 付款金额
 * @param $order_id 订单ID
 * @param $payment_id 付款方式ID
 * @param $memo 付款单备注
 * @param $ecv_id 如为代金券支付，则指定代金券ID
 * return payment_notice_id 付款单ID
 *
 */
function make_fx_pay_payment_notice($money,$order_id,$payment_id,$memo='',$ecv_id=0)
{
    if($money > 0){
        $notice['create_time'] = NOW_TIME;
        $notice['order_id'] = $order_id;
        $notice['user_id'] = $GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
        $notice['order_type'] =5;//分销资格购买
        $notice['payment_id'] = $payment_id;
        $notice['memo'] = $memo;
        $notice['money'] = $money;
        $notice['ecv_id'] = $ecv_id;
        do{
            $notice['notice_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
            $GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$notice,'INSERT','','SILENT');
            $notice_id = intval($GLOBALS['db']->insert_id());
        }while($notice_id==0);
        return $notice_id;
    }
}

/**
 * 付款单的支付
 * @param unknown_type $payment_notice_id
 * 当超额付款时在此进行退款处理
 */
function payment_paid($payment_notice_id)
{
	require_once(APP_ROOT_PATH."system/model/user.php");
	$payment_notice_id = intval($payment_notice_id);
	$now = NOW_TIME;
	$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = ".$now.",is_paid = 1 where id = ".$payment_notice_id." and is_paid = 0");	
	$rs = $GLOBALS['db']->affected_rows();
	if($rs)
	{		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);
		if($payment_info['class_name'] == 'Voucher')
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = pay_amount + ".$payment_notice['money'].",ecv_money = ".$payment_notice['money'].",ecv_id=".$payment_notice['ecv_id']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and ((pay_amount + ".$payment_notice['money']." <= total_price) or ".$payment_notice['money'].">=total_price)");
			$order_incharge_rs = $GLOBALS['db']->affected_rows();
		}
		elseif($payment_info['class_name'] == 'Account')
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = pay_amount + ".$payment_notice['money'].",account_money = account_money + ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");
			$order_incharge_rs = $GLOBALS['db']->affected_rows();
		}elseif($payment_info['class_name'] == 'Cod')
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = pay_amount + ".$payment_notice['money'].",cod_money = ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");
			$order_incharge_rs = $GLOBALS['db']->affected_rows();
		}
		else
		{
// 			if($order_info['type']==0&&$payment_notice['money']!=0)
// 			{			
// 				/**
// 				 * 订单在线支付记录日志
// 				 */
// 				$log_info['log_info'] = $order_info['order_sn']."订单在线支付";
// 				$log_info['log_time'] = NOW_TIME;
// 				$adm_session = es_session::get(md5(app_conf("AUTH_KEY")));
					
// 				$adm_id = intval($adm_session['adm_id']);
// 				if($adm_id!=0)
// 				{
// 					$log_info['log_admin_id'] = $adm_id;
// 				}
// 				else
// 				{
// 					$log_info['log_user_id'] = $payment_notice['user_id'];
// 				}
// 				$log_info['money'] = floatval($payment_notice['money']);
// 				$log_info['user_id'] = $payment_notice['user_id'];
// 				$GLOBALS['db']->autoExecute(DB_PREFIX."user_log",$log_info);
// 			}
			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = pay_amount + ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");
			$order_incharge_rs = $GLOBALS['db']->affected_rows();
			
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."payment set total_amount = total_amount + ".$payment_notice['money']." where class_name = '".$payment_info['class_name']."'");									
		if(!$order_incharge_rs&&$payment_notice['money']>0)//订单支付超出
		{
			if($payment_info['class_name'] == 'Cod'){
				//货到付款超额
				if(($order_info['total_price']-$order_info['pay_amount'])>0){
					$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set money = ".($order_info['total_price']-$order_info['pay_amount'])." where id = ".$payment_notice_id);
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = total_price,cod_money = total_price-pay_amount where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0");
					$order_incharge_rs = $GLOBALS['db']->affected_rows();
				}else{
					$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = '',is_paid = 0 where id = ".$payment_notice_id." and is_paid = 0");
				}
			}else{
				//超出充值	
				if($order_info['is_delete']==1||$order_info['order_status']==1)
				$msg = sprintf($GLOBALS['lang']['DELETE_INCHARGE'],$payment_notice['notice_sn']);
				else
				$msg = sprintf($GLOBALS['lang']['PAYMENT_INCHARGE'],$payment_notice['notice_sn']);			
				modify_account(array('money'=>$payment_notice['money'],'score'=>0),$payment_notice['user_id'],$msg);
				modify_statements($payment_notice['money'], 2, $order_info['order_sn']."订单超额支付"); //订单超额充值
				
				order_log($order_info['order_sn']."订单超额支付，".format_price($payment_notice['money'])."已退到会员余额", $order_info['id']);
				//更新订单的extra_status为1
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set extra_status = 1 where is_delete = 0 and id = ".intval($payment_notice['order_id']));
			}
		}
		else
		{
			//收入, by hc 0507,移至订单支付成功后增加，则支付失败后的退款取消
			//modify_statements($payment_notice['money'], 0, $order_info['order_sn']."订单成功付款"); //总收入
			//modify_statements($payment_notice['money'], 1, $order_info['order_sn']."订单成功付款"); //订单支付收入
		}
		if($payment_info['class_name'] == 'Cod'){
			//在此处开始生成付款的短信及邮件
			send_cod_payment_sms($payment_notice_id);
			send_cod_payment_mail($payment_notice_id);
		}else{
			//在此处开始生成付款的短信及邮件
			send_payment_sms($payment_notice_id);
			send_payment_mail($payment_notice_id);
		}
	}
	return $rs;
}

function store_pay_payment_paid($payment_notice_id)
{
	require_once(APP_ROOT_PATH."system/model/supplier.php");
	require_once(APP_ROOT_PATH."system/model/store_pay.php");
	$payment_notice_id = intval($payment_notice_id);
	$now = NOW_TIME;
	$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = ".$now.",is_paid = 1 where id = ".$payment_notice_id." and is_paid = 0");
	$rs = $GLOBALS['db']->affected_rows();
	if($rs)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$payment_notice['order_id']);
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);


		$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set pay_amount = pay_amount + ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");
		$order_incharge_rs = $GLOBALS['db']->affected_rows();


		$GLOBALS['db']->query("update ".DB_PREFIX."payment set total_amount = total_amount + ".$payment_notice['money']." where class_name = '".$payment_info['class_name']."'");
		if(!$order_incharge_rs&&$payment_notice['money']>0)//订单支付超出
		{
			//超出充值
			if($order_info['is_delete']==1||$order_info['order_status']==1)
				$msg = sprintf($GLOBALS['lang']['DELETE_INCHARGE'],$payment_notice['notice_sn']);
			else
				$msg = sprintf($GLOBALS['lang']['PAYMENT_INCHARGE'],$payment_notice['notice_sn']);
			modify_account(array('money'=>$payment_notice['money'],'score'=>0),$payment_notice['user_id'],$msg);
			
			modify_statements($payment_notice['money'], 2, $order_info['order_sn']."订单超额支付"); //订单超额充值

			store_pay_order_log($order_info['order_sn']."订单超额支付，".format_price($payment_notice['money'])."已退到会员余额", $order_info['id']);
			//更新订单的extra_status为1
			$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set extra_status = 1 where is_delete = 0 and id = ".intval($payment_notice['order_id']));
		}
		else
		{
			//收入, by hc 0507,移至订单支付成功后增加，则支付失败后的退款取消
			//modify_statements($payment_notice['money'], 0, $order_info['order_sn']."订单成功付款"); //总收入
			//modify_statements($payment_notice['money'], 1, $order_info['order_sn']."订单成功付款"); //订单支付收入
		}

	}
	return $rs;
}

function fx_buy_paid($payment_notice_id)
{
    require_once(APP_ROOT_PATH."system/model/fx.php");
    $payment_notice_id = intval($payment_notice_id);
    $now = NOW_TIME;
    $GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = ".$now.",is_paid = 1 where id = ".$payment_notice_id." and is_paid = 0");
    $rs = $GLOBALS['db']->affected_rows();
    if($rs)
    {
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
        $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$payment_notice['order_id']);
        $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);


        $GLOBALS['db']->query("update ".DB_PREFIX."fx_buy_order set pay_amount = pay_amount + ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");
        $order_incharge_rs = $GLOBALS['db']->affected_rows();


        $GLOBALS['db']->query("update ".DB_PREFIX."payment set total_amount = total_amount + ".$payment_notice['money']." where class_name = '".$payment_info['class_name']."'");
        if(!$order_incharge_rs&&$payment_notice['money']>0)//订单支付超出
        {
            //超出充值
            if($order_info['is_delete']==1||$order_info['order_status']==1)
                $msg = sprintf($GLOBALS['lang']['DELETE_INCHARGE'],$payment_notice['notice_sn']);
            else
                $msg = sprintf($GLOBALS['lang']['PAYMENT_INCHARGE'],$payment_notice['notice_sn']);
            modify_account(array('money'=>$payment_notice['money'],'score'=>0),$payment_notice['user_id'],$msg);
            	
            modify_statements($payment_notice['money'], 2, $order_info['order_sn']."订单超额支付"); //订单超额充值

            fx_buy_order_log($order_info['order_sn']."订单超额支付，".format_price($payment_notice['money'])."已退到会员余额", $order_info['id']);
            //更新订单的extra_status为1
            $GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set extra_status = 1 where is_delete = 0 and id = ".intval($payment_notice['order_id']));
        }
        else
        {
            //收入, by hc 0507,移至订单支付成功后增加，则支付失败后的退款取消
            //modify_statements($payment_notice['money'], 0, $order_info['order_sn']."订单成功付款"); //总收入
            //modify_statements($payment_notice['money'], 1, $order_info['order_sn']."订单成功付款"); //订单支付收入
        }

    }
    return $rs;
}

//同步订单支付状态
function order_paid($order_id)
{	
		$order_id  = intval($order_id);
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		if($order['pay_amount'] + $order['youhui_money'] >=$order['total_price'])
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_status = 2 , pay_amount = pay_amount + youhui_money where id =".$order_id." and pay_status <> 2");
			$rs = $GLOBALS['db']->affected_rows();
			if($rs)
			{				
				
				//支付完成
				order_paid_done($order_id);
				$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
				if($order['pay_status']==2&&$order['after_sale']==0)
				{
					require_once(APP_ROOT_PATH."system/model/deal_order.php");
					distribute_order($order_id);
					$result = true;
				}
				else
				$result = false;
			}
		}
		elseif($order['pay_amount']<$order['total_price']&&$order['pay_amount']!=0)
		{
			//by hc 0507
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_status = 0 where id =".$order_id);
			$result = false;  //订单未支付成功
		}
		elseif($order['pay_amount']==0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_status = 0 where id =".$order_id);
			$result = false;  //订单未支付成功
		}		
		return $result;
}

//订单付款完毕后执行的操作,充值单也在这处理，未实现
function order_paid_done($order_id)
{
	//处理支付成功后的操作
	/**
	 * 1. 发货
	 * 2. 超量发货的存到会员中心
	 * 3. 发券
	 * 4. 发放抽奖
	 */
	require_once(APP_ROOT_PATH."system/model/deal.php");
	require_once(APP_ROOT_PATH."system/model/supplier.php");
	require_once(APP_ROOT_PATH."system/model/deal_order.php");
	
	
	$order_id = intval($order_id);
	$stock_status = true;  //团购状态
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	$payment_notice = $GLOBALS['db']->getRow("select pn.id,p.class_name from ".DB_PREFIX."payment_notice as pn left join ".DB_PREFIX."payment AS p on pn.payment_id=p.id where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid=1 and money>0");
    //积分充值订单
    if($order_info["type"]==7){
		require_once(APP_ROOT_PATH."system/model/user.php");
        $money = $order_info['total_price'] - $order_info['payment_fee'];
        modify_account(array('score'=>$order_info['return_total_score']),$order_info['user_id'],'充值'.format_price($money).'成功，可用积分增加'.$order_info['return_total_score']);
		modify_account(array('frozen_score'=>$order_info['frozen_score']),$order_info['user_id'],'充值'.format_price($money).'成功，冻结积分增加'.$order_info['frozen_score']);
		modify_statements($order_info['total_price'], 0, $order_info['order_sn']."会员充值积分订单，含手续费"); //总收入
		$msg_content = '您已成功充值'.format_price($money).', 积分增加'.$order_info['return_score'].",冻结积分增加".$order_info['frozen_score'];
		send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 4, 'data_id' => $order_id));
		auto_over_status($order_id);
    }
	else if($order_info['type'] != 1)
	{	
	    $GLOBALS['db']->query("START TRANSACTION");
		//首先验证所有的规格库存
		$order_goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);		
		foreach($order_goods_list as $k=>$v)
		{
			$attr_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = ".$v['deal_id']." and locate(attr_str,'".$v['attr_str']."')");
			if($attr_stock)
			{
				if($attr_stock['stock_cfg']>=0)
				{
					$sql = "update ".DB_PREFIX."attr_stock set buy_count = buy_count + ".$v['number'].",stock_cfg = stock_cfg - ".$v['number']." where deal_id = ".$v['deal_id'].
						   " and (stock_cfg - ".$v['number']." >= 0)".
					       " and locate(attr_str,'".$v['attr_str']."') > 0 ";
				}
				else
				{
					$sql = "update ".DB_PREFIX."attr_stock set buy_count = buy_count + ".$v['number']." where deal_id = ".$v['deal_id'].
					" and locate(attr_str,'".$v['attr_str']."') > 0 ";
				}
				$GLOBALS['db']->query($sql); //增加商品的发货量
				$rs = $GLOBALS['db']->affected_rows();
				
				if($rs)
				{
					$affect_attr_list[] = $v;
				}
				else
				{				
							
					$stock_status = false;
					break;
				}
			}
		}
		
		if($stock_status)
		{
			$goods_list = $GLOBALS['db']->getAll("select buy_type,deal_id,sum(number) as num,sum(add_balance_price_total) as add_balance_price_total,sum(balance_total_price) as balance_total_price,sum(return_total_money) as return_total_money,sum(return_total_score) as return_total_score,min(is_pick) as is_pick from ".DB_PREFIX."deal_order_item where order_id = ".$order_id." group by deal_id");	
			foreach($goods_list as $k=>$v)
			{
				//$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
				$deal_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_stock where deal_id = '".$v['deal_id']."'");
				if($deal_stock['stock_cfg']>=0)
// 					$sql = "update ".DB_PREFIX."deal set buy_count = buy_count + ".$v['num'].
// 						   ",user_count = user_count + 1, max_bought = max_bought-".$v['num']." where id=".$v['deal_id'].
// 						   " and (max_bought - ".$v['num'].">= 0) ".
// 						   " and time_status = 1 and buy_status <> 2";
					$sql = "update ".DB_PREFIX."deal_stock set buy_count = buy_count + ".$v['num'].
						   ",stock_cfg = stock_cfg - ".$v['num']." where deal_id=".$v['deal_id'].
						  " and (stock_cfg - ".$v['num'].">= 0) and time_status <> 2";
				else
// 					$sql = "update ".DB_PREFIX."deal set buy_count = buy_count + ".$v['num'].
// 						   ",user_count = user_count + 1 where id=".$v['deal_id'].
// 						   " and time_status = 1 and buy_status <> 2";
					$sql = " update ".DB_PREFIX."deal_stock set buy_count = buy_count + ".$v['num'].
						   " where deal_id=".$v['deal_id']." and time_status <> 2";
		
				$GLOBALS['db']->query($sql); //增加商品的发货量
				$rs = $GLOBALS['db']->affected_rows();
				
				if($rs)
				{
					$affect_list[] = $v;  //记录下更新成功的团购商品，用于回滚
				}
				else
				{
					//失败成功，即过期支付，超量支付
					$stock_status = false;
					break;
				}
			}
		}
	
		$return_money = 0; //非发券非配送的即时返还
		$return_score = 0; //非发券非配送的即时返还
		$use_score = 0;  //积分商品所耗费的积分
		if($stock_status)
		{
			$GLOBALS['db']->query("COMMIT");
			foreach($goods_list as $k=>$v)
			{
					//$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".intval($v['deal_id']));
					$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
					//统计商户销售额
					
					if($deal_info['max_bought']>=0)
 						$sql = "update ".DB_PREFIX."deal set buy_count = buy_count + ".$v['num'].
 						",user_count = user_count + 1,max_bought = max_bought - ".$v['num']." where id=".$v['deal_id'];
					else
						$sql = "update ".DB_PREFIX."deal set buy_count = buy_count + ".$v['num'].
						",user_count = user_count + 1 where id=".$v['deal_id'];
 					$GLOBALS['db']->query($sql);
					if($payment_notice['class_name']=='Cod'||$order_info['cod_money']>0){
						//货到付款，目前不产生商户报表和平台报表，不结算金额给商家
					}else{
						$supplier_log =  "ID:".$deal_info['id']." ".$deal_info['sub_name']." 订单：".$order_info['order_sn'];
						modify_supplier_account($v['balance_total_price']+$v['add_balance_price_total'], $deal_info['supplier_id'], 0,$supplier_log);
						modify_supplier_account($v['balance_total_price']+$v['add_balance_price_total'], $deal_info['supplier_id'], 1, $supplier_log);  //冻结资金
					}

					
					//不发券的实时返还	
					if($deal_info['is_coupon']==0&&$deal_info['is_shop']==0&&$v['buy_type']==0)
					{
					    $return_money+=$v['return_total_money'];
					    $return_score+=$v['return_total_score'];
					}
					if($v['buy_type']==1)
					{
						$use_score+=$v['return_total_score'];
					}
					$balance_price+=$v['balance_total_price'];
					$add_balance_price+=$v['add_balance_price_total'];
					
					//发券
					if($deal_info['is_coupon'] == 1||$v['is_pick']==1)
					{
						if($deal_info['deal_type'] == 1||$v['is_pick']==1) //按单发券
						{
							$deal_order_item_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']." and deal_id = ".$v['deal_id']);
							foreach($deal_order_item_list as $item)
							{
	//							for($i=0;$i<$item['number'];$i++) //按单
	//							{
									//需要发券
									/**
									 * 1. 先从已有消费券中发送
									 * 2. 无有未发送的券，自动发送
									 * 3. 发送状态的is_valid 都是 0, 该状态的激活在syn_deal_status中处理
									 */
									 /*修正后台手动建团购劵，购买的时候按单发送团购劵，数量不一致*/
									$sql = "update ".DB_PREFIX."deal_coupon set user_id=".$order_info['user_id'].
										   ",order_id = ".$order_info['id'].
										   ",order_deal_id = ".$item['id'].
										   ",expire_refund = ".$deal_info['expire_refund'].
										   ",any_refund = ".$deal_info['any_refund'].
										   ",coupon_price = ".$item['total_price'].
										   ",coupon_score = ".$item['return_total_score'].
										   ",coupon_money = ".$item['return_total_money'].
										   ",add_balance_price = ".$item['add_balance_price'].
										   ",deal_type = ".$deal_info['deal_type'].
										   ",balance_price = ".$item['balance_total_price'].
										   " where deal_id = ".$v['deal_id'].
										   " and user_id = 0 ".
										   " and is_delete = 0 order by id ASC limit 1";
									$GLOBALS['db']->query($sql);
									$exist_coupon = $GLOBALS['db']->affected_rows();
									if(!$exist_coupon)
									{
										//未发送成功，即无可发放的预设消费券
										add_coupon($v['deal_id'],$order_info['user_id'],0,'','',0,0,$item['id'],$order_info['id']);
									}
	//							}
							}
						}
						else
						{
							$deal_order_item_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']." and deal_id = ".$v['deal_id']);
							foreach($deal_order_item_list as $item)
							{
								for($i=0;$i<$item['number'];$i++) //按件
								{
									//需要发券
									/**
									 * 1. 先从已有消费券中发送
									 * 2. 无有未发送的券，自动发送
									 * 3. 发送状态的is_valid 都是 0, 该状态的激活在syn_deal_status中处理
									 */
									$sql = "update ".DB_PREFIX."deal_coupon set user_id=".$order_info['user_id'].
										   ",order_id = ".$order_info['id'].
										   ",order_deal_id = ".$item['id'].
										   ",expire_refund = ".$deal_info['expire_refund'].
										   ",any_refund = ".$deal_info['any_refund'].
										   ",coupon_price = ".$item['unit_price'].
										   ",coupon_score = ".$item['return_score'].
										   ",coupon_money = ".$item['return_money'].
										   ",add_balance_price = ".$item['add_balance_price'].
										   ",deal_type = ".$deal_info['deal_type'].
										   ",balance_price = ".$item['balance_unit_price'].
										   " where deal_id = ".$v['deal_id'].
										   " and user_id = 0 ".
										   " and is_delete = 0 limit 1";
									$GLOBALS['db']->query($sql);
									$exist_coupon = $GLOBALS['db']->affected_rows();
									if(!$exist_coupon)
									{
										//未发送成功，即无可发放的预设消费券
										add_coupon($v['deal_id'],$order_info['user_id'],0,'','',0,0,$item['id'],$order_info['id']);
									}
								}
							}
						}//发券结束	
					} elseif ($deal_info['is_coupon'] == 0 && $deal_info['is_shop'] == 0) { 
						// 不发券的团购商品直接结算
						$order_item_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']);
						
						auto_balance_coupon($order_item_id);
					}					
			}
			//开始处理返还的积分或现金,此处返还不用发货不用配送不用发券的产品返还
			require_once(APP_ROOT_PATH."system/model/user.php");
			if($return_money!=0)
			{
				$msg = sprintf($GLOBALS['lang']['ORDER_RETURN_MONEY'],$order_info['order_sn']);
				modify_account(array('money'=>$return_money,'score'=>$return_score),$order_info['user_id'],$msg);	
			}
			
			if($return_score < 0)   //积分兑换，积分变动提醒
			{   
				$msg = sprintf($GLOBALS['lang']['ORDER_RETURN_SCORE'],$order_info['order_sn']);
				modify_account(array('money'=>0,'score'=>$return_score),$order_info['user_id'],$msg);	
				send_score_sms($order_info['id']);
				send_score_mail($order_info['id']);
			}else if($return_score>0){
			    $msg = sprintf($GLOBALS['lang']['ORDER_RETURN_SCORE'],$order_info['order_sn']);
			    modify_account(array('money'=>0,'score'=>$return_score),$order_info['user_id'],$msg);
			}
			

			
			if($use_score!=0)
			{
				$user_score =  $GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$order_info['user_id']);
				if($user_score+$use_score<0)
				{
					//积分不足，不能支付
					$msg = $order_info['order_sn']."订单支付失败，积分不足";
					if($payment_notice['class_name']=='Cod'||$order_info['cod_money']>0){
						//货到付款的金额不参与退款
						$refund_money = $order_info['pay_amount']-$order_info['cod_money'];
					}else{
						$refund_money = $order_info['pay_amount'];
					}
					if($order_info[' ']>$refund_money)$account_money_now = $order_info['account_money'] - $refund_money; else $account_money_now = 0;
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set account_money = ".$account_money_now." where id = ".$order_info['id']);
					
					//if($order_info['ecv_money']>$refund_money)$ecv_money_now = $order_info['ecv_money'] - $refund_money; else $ecv_money_now = 0;
					//$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set ecv_money = ".$ecv_money_now." where id = ".$order_info['id']);
					
					if($order_info['ecv_money']>0){//红包不参与退款
						$refund_money = $refund_money-$order_info['ecv_money'];
					}
					if($refund_money>0)
					{
						modify_account(array('money'=>$refund_money,'score'=>0),$order_info['user_id'],$msg);
						$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_money = refund_money + ".$refund_money.",refund_amount = refund_amount + ".$refund_money.",after_sale = 1,refund_status = 2 where id = ".$order_info['id']);
						$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 2 where order_id = ".$order_info['id']);
						
						order_log($order_info['order_sn']."积分不足，".format_price($refund_money)."已退到会员余额", $order_info['id']);
						
						//修改 hc 0507 去掉退款报表
// 						modify_statements("-".$refund_money, 1, $order_info['order_sn']."积分不足，退款");
						modify_statements($refund_money, 2, $order_info['order_sn']."积分不足，退款");
						//收入, by hc 0507,积分商品付款退款，增加退款
						modify_statements($refund_money, 0, $order_info['order_sn']."积分不足，退到余额"); //总收入
					}
					else
					{
						order_log($order_info['order_sn']."积分不足", $order_info['id']);
					}
					require_once(APP_ROOT_PATH."system/model/deal_order.php");
					over_order($order_info['id']);
				}
				else
				{
					modify_account(array('score'=>$use_score),$order_info['user_id'],"积分商品兑换");
					send_score_sms($order_info['id']);
					send_score_mail($order_info['id']);
					order_log($order_info['order_sn']."积分订单支付成功", $order_info['id']);

					$msg_content = '您的积分订单<'.$order_info['order_sn'].'>兑换成功';
					send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 11, 'data_id' => $order_id));
					// send_msg($order_info['user_id'], "订单".$order_info['order_sn']."兑换成功", "orderitem", $order_goods_list[0]['id']);
					if($payment_notice['class_name']=='Cod'){
						//货到付款，目前不产生商户报表和平台报表，不结算金额给商家
					}else{
						if($order_info['total_price']>0)
						//收入, by hc 0507,积分商品付款成功，增加报表
						modify_statements($order_info['total_price'], 0, $order_info['order_sn']."订单成功付款"); //总收入
						modify_statements($order_info['total_price'], 1, $order_info['order_sn']."订单成功付款"); //订单支付收入
						
						modify_statements($order_info['total_price'], 8, $order_info['order_sn']."订单成功付款");  //增加营业额
					}
				}
			}
			else
			{				
				//超出充值
				if($order_info['pay_amount']>$order_info['total_price'])
				{
					require_once(APP_ROOT_PATH."system/model/user.php");
					if($order_info['total_price']<0)
						$msg = sprintf($GLOBALS['lang']['MONEYORDER_INCHARGE'],$order_info['order_sn']);
					else
						$msg = sprintf($GLOBALS['lang']['OUTOFMONEY_INCHARGE'],$order_info['order_sn']);
					$refund_money = $order_info['pay_amount']-$order_info['total_price'];
				
					if($order_info['account_money']>$refund_money)$account_money_now = $order_info['account_money'] - $refund_money; else $account_money_now = 0;
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set account_money = ".$account_money_now." where id = ".$order_info['id']);
				
					if($order_info['ecv_money']>$refund_money)$ecv_money_now = $order_info['ecv_money'] - $refund_money; else $ecv_money_now = 0;
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set ecv_money = ".$ecv_money_now." where id = ".$order_info['id']);
				
					modify_account(array('money'=>$refund_money,'score'=>0),$order_info['user_id'],$msg);
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_money = refund_money + ".$refund_money.",refund_amount = refund_amount + ".$refund_money." where id = ".$order_info['id']);
				
					order_log($order_info['order_sn']."订单超额支付，".format_price($refund_money)."已退到会员余额", $order_info['id']);
				//by hc,0507 不再退款时记录
// 					modify_statements("-".$refund_money, 1, $order_info['order_sn']."订单超额");
					modify_statements($refund_money, 2, $order_info['order_sn']."订单超额");
					//收入, by hc 0507
					modify_statements($refund_money, 0, $order_info['order_sn']."订单超额"); //总收入
				}
	
				//生成抽奖
				$lottery_list = $GLOBALS['db']->getAll("select d.id as did,doi.number from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where d.is_lottery = 1 and do.id = ".$order_info['id']);
				$lottery_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($order_info['user_id']));
					
				//如为首次抽奖，先为推荐人生成抽奖号
				$lottery_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where user_id = ".intval($order_info['user_id']));
				if($lottery_count == 0&&$lottery_user['pid']!=0)
				{
					$lottery_puser = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($lottery_user['pid']));
					foreach($lottery_list as $lottery)
					{
						$k = 0;
						do{
							if($k>10)break;
							$buy_count = $GLOBALS['db']->getOne("select buy_count from ".DB_PREFIX."deal where id = ".$lottery['did']);
							$max_sn = $buy_count - $lottery['number'] + intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where deal_id = ".intval($lottery['did'])." and buyer_id <> 0 "));
							//$max_sn = intval($GLOBALS['db']->getOne("select lottery_sn from ".DB_PREFIX."lottery where deal_id = '".$lottery['did']."' order by lottery_sn desc limit 1"));
							$sn = $max_sn + 1;
							$sn = str_pad($sn,"6","0",STR_PAD_LEFT);
							$sql = "insert into ".DB_PREFIX."lottery (`lottery_sn`,`deal_id`,`user_id`,`mobile`,`create_time`,`buyer_id`) select '".$sn."','".$lottery['did']."',".$lottery_puser['id'].",'".$lottery_puser['lottery_mobile']."',".NOW_TIME.",".$order_info['user_id']." from dual where not exists( select * from ".DB_PREFIX."lottery where deal_id = ".$lottery['did']." and lottery_sn = '".$sn."')";
							$GLOBALS['db']->query($sql);
							send_lottery_sms(intval($GLOBALS['db']->insert_id()));
							$k++;
						}while(intval($GLOBALS['db']->insert_id())==0);
					}
				}
					
					
				foreach($lottery_list as $lottery)
				{
					for($i=0;$i<$lottery['number'];$i++) //按购买数量生成抽奖号
					{
						$k = 0;
						do{
							if($k>10)break;
							$buy_count = $GLOBALS['db']->getOne("select buy_count from ".DB_PREFIX."deal where id = ".$lottery['did']);
							$max_sn = $buy_count - $lottery['number'] + intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where deal_id = ".intval($lottery['did'])." and buyer_id <> 0 "));
							//$max_sn = intval($GLOBALS['db']->getOne("select lottery_sn from ".DB_PREFIX."lottery where deal_id = '".$lottery['did']."' order by lottery_sn desc limit 1"));
							$sn = $max_sn + $i + 1;
							$sn = str_pad($sn,"6","0",STR_PAD_LEFT);
							$sql = "insert into ".DB_PREFIX."lottery (`lottery_sn`,`deal_id`,`user_id`,`mobile`,`create_time`,`buyer_id`) select '".$sn."','".$lottery['did']."',".$order_info['user_id'].",'".$lottery_user['mobile']."',".NOW_TIME.",0 from dual where not exists( select * from ".DB_PREFIX."lottery where deal_id = ".$lottery['did']." and lottery_sn = '".$sn."')";
							$GLOBALS['db']->query($sql);
							send_lottery_sms(intval($GLOBALS['db']->insert_id()));
							$k++;
						}while(intval($GLOBALS['db']->insert_id())==0);
					}
				}

			}
			
			syn_order_done($order_info['id']);
		}	
		else
		{
			//开始模拟事务回滚
// 			foreach($affect_attr_list as $k=>$v)
// 			{
// 				$sql = "update ".DB_PREFIX."attr_stock set buy_count = buy_count - ".$v['number']." where deal_id = ".$v['deal_id'].
//    			           " and locate(attr_str,'".$v['attr_str']."') > 0 ";

// 				$GLOBALS['db']->query($sql); //回滚已发的货量
// 			}
// 			foreach($affect_list as $k=>$v)
// 			{
// 				$sql = "update ".DB_PREFIX."deal set buy_count = buy_count - ".$v['num'].
// 				   	   ",user_count = user_count - 1 where id=".$v['deal_id'];
// 				$GLOBALS['db']->query($sql); //回滚已发的货量
// 			}
			$GLOBALS['db']->query("ROLLBACK");
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 2 where order_id = ".$order_info['id']);
			
			if($payment_notice['class_name']=='Cod'||$order_info['cod_money']>0){
				//货到付款的金额不参与退款
				$return_money=$order_info['pay_amount']-$order_info['cod_money'];
			}else{
				$return_money=$order_info['pay_amount'];
			}
			if($order_info['ecv_money']>0){
				$return_money=$return_money-$order_info['ecv_money'];
			}
			//超出充值
			require_once(APP_ROOT_PATH."system/model/user.php");
			$msg = sprintf($GLOBALS['lang']['OUTOFSTOCK_INCHARGE'],$order_info['order_sn']);			
			modify_account(array('money'=>$return_money,'score'=>0),$order_info['user_id'],$msg);	
			
			order_log($order_info['order_sn']."订单库存不足，".format_price($return_money)."已退到会员余额", $order_info['id']);
			
			//by hc 0507
// 			modify_statements("-".$order_info['total_price'], 1, $order_info['order_sn']."订单库存不足");
			modify_statements($return_money, 2, $order_info['order_sn']."订单库存不足，退到余额");
			
			//收入, by hc 0507,积分商品付款成功，增加报表
			modify_statements($return_money, 0, $order_info['order_sn']."订单库存不足，退到余额"); //总收入
			
			
			//将订单的extra_status 状态更新为2，并自动退款
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set extra_status = 2, after_sale = 1, refund_money = pay_amount where id = ".intval($order_info['id']));
			//记录退款的订单日志		
			$log['log_info'] = $msg;
			$log['log_time'] = NOW_TIME;
			$log['order_id'] = intval($order_info['id']);
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_log",$log);
		}
		update_order_cache($order_info['id']);
		//同步所有未过期的团购状态
		foreach($goods_list as $item)
		{
			syn_deal_status($item['deal_id'],true);
		}
		
		auto_over_status($order_id); //自动结单
	}//end 普通团购
	else
	{  
	    
		//订单充值
// 		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 1 where id = ".$order_info['id']); //充值单自动结单
		require_once(APP_ROOT_PATH."system/model/user.php");
		$msg = sprintf($GLOBALS['lang']['USER_INCHARGE_DONE'],$order_info['order_sn']);			
		modify_account(array('money'=>$order_info['total_price']-$order_info['payment_fee'],'score'=>0),$order_info['user_id'],$msg);	
		
		//by hc 0507
// 		modify_statements("-".($order_info['total_price']), 1, $order_info['order_sn']."会员充值");
		modify_statements(($order_info['total_price']), 2, $order_info['order_sn']."会员充值，含手续费");
		
		//收入, by hc 0507
		modify_statements($order_info['total_price'], 0, $order_info['order_sn']."会员充值，含手续费"); //总收入
		
		$money = $order_info['total_price'] - $order_info['payment_fee'];
		$msg_content = '您已成功充值<'.format_price($money).', 余额增加'.$money;
		send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 4, 'data_id' => $order_id));
		// send_msg($order_info['user_id'], "成功充值".format_price($order_info['total_price']-$order_info['payment_fee']), "notify", $order_id);
		
		auto_over_status($order_id); //自动结单
	}
}



//同步商户订单支付状态
function store_pay_order_paid($order_id)
{
	$order_id  = intval($order_id);
	$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$order_id);
	if($order['pay_amount'] + $order['discount_price']+ $order['exchange_money']>=$order['total_price'])
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set pay_status = 2 where id =".$order_id." and pay_status <> 2");
		$rs = $GLOBALS['db']->affected_rows();
		if($rs)
		{


			//支付完成
			store_pay_order_paid_done($order_id);
			$result = true;
		}
	}
	elseif($order['pay_amount'] + $order['discount_price'] <$order['total_price']&&$order['pay_amount']!=0)
	{
		//by hc 0507
		$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set pay_status = 0 where id =".$order_id);
		$result = false;  //订单未支付成功
	}
	elseif($order['pay_amount']==0)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."store_pay_order set pay_status = 0 where id =".$order_id);
		$result = false;  //订单未支付成功
	}
	return $result;
}

//同步分销资格购买订单支付状态
function fx_buy_order_paid($order_id)
{
    $order_id  = intval($order_id);
    $order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
    if($order['pay_amount'] >=$order['total_price'])
    {
        $GLOBALS['db']->query("update ".DB_PREFIX."fx_buy_order set pay_status = 2 where id =".$order_id." and pay_status <> 2");
        $rs = $GLOBALS['db']->affected_rows();
        if($rs)
        {


            //支付完成
            fx_buy_order_paid_done($order_id);
            $result = true;
        }
    }
    elseif($order['pay_amount'] < $order['total_price']&&$order['pay_amount']!=0)
    {
        //by hc 0507
        $GLOBALS['db']->query("update ".DB_PREFIX."fx_buy_order set pay_status = 0 where id =".$order_id);
        $result = false;  //订单未支付成功
    }
    return $result;
}

//订单付款完毕后执行的操作,充值单也在这处理，未实现
function store_pay_order_paid_done($order_id)
{
	//处理支付成功后的操作
	require_once(APP_ROOT_PATH."system/model/supplier.php");

	$order_id = intval($order_id);

	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$order_id);

		//盘缠充值
		// 		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 1 where id = ".$order_info['id']); //充值单自动结单
		$msg = "订单号为:".$order_info['order_sn']." 买单成功";
		
		$promote=unserialize($order_info['promote']);
		
		$money=$tatal_money=$order_info['total_price']-$order_info['discount_price'];
		$rate=$GLOBALS['db']->getOne("select store_payment_rate from ".DB_PREFIX."supplier where id = ".$order_info['supplier_id']);
		$money=$money * floatval(1-$rate);
		
		if($promote){
			foreach($promote as $k=>$v){
				if($v['discount_role']==0){  //补贴者：平台
					$money=$money+$v['discount_price'];
				}
			}
		}
		
		modify_supplier_account($money,$order_info['supplier_id'],0,$msg); //商户销售额增加
		modify_supplier_account($money,$order_info['supplier_id'],3,$msg); //商户余额增加
		modify_supplier_account($order_info['total_price'],$order_info['supplier_id'],7,$msg); //优惠买单销售额增加
		$agency_id=intval($GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id=".$order_info['supplier_id']));
		
		$money_admin=$tatal_money-$money;  //总利润
		//代理商佣金增加
		if($money_admin > 0){
			
		    modify_agency_account($money_admin,$agency_id,3,$msg);
		}
		
		require_once(APP_ROOT_PATH."system/model/store_pay.php");
		store_pay_auto_over_status($order_id); //自动结单
}


//分销资格订单付款完毕后执行的操作
function fx_buy_order_paid_done($order_id)
{
    //处理支付成功后的操作
    $order_id = intval($order_id);

    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);

    //盘缠充值
    // 		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 1 where id = ".$order_info['id']); //充值单自动结单
    $msg = "订单号为:".$order_info['order_sn']." 买单成功";

    $money=$tatal_money=$order_info['total_price']-$order_info['discount_price'];

    //用户分销信息变化
    $GLOBALS['db']->query("update ".DB_PREFIX."user set is_fx = 1 where id = ".$order_info['user_id']);
    
    require_once(APP_ROOT_PATH."system/model/fx.php");
    fx_buy_auto_over_status($order_id); //自动结单


}


//处理同步购物车中的相关状态的团购产品
/**
 * 
1. 如果购物车中有禁用(3), 如果禁用项最后加入，保留禁用项，反之，删除禁用项
2. 如购物车中有按商户禁用(2), 如果加入商户禁用是最后加入，删除与之不相同的商户的商品，反之删除需商户禁用的所有相关的商品
3. 如购物车中有按商品禁用(1), 如果加入商品禁用是最后加入，删除与之不相同的商品，反之删除该商品
 */
function syn_cart()
{

	$first_row = $GLOBALS['db']->getRow("select dc.*,d.cart_type as cart_type from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."deal as d on dc.deal_id = d.id where dc.session_id = '".es_session::id()."' and dc.user_id = ".intval($GLOBALS['user_info']['id'])." order by dc.create_time desc");
	//1. 处理禁用全部的状态 cart_type 3
	$result = $GLOBALS['db']->getAll("select dc.id,dc.deal_id,dc.supplier_id from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."deal as d on dc.deal_id = d.id where dc.session_id = '".es_session::id()."' and dc.user_id = ".intval($GLOBALS['user_info']['id'])." and d.cart_type = 3");
	if($result)
	{		
		if($first_row['cart_type']==3)
		{
			//保留禁用购物车的产品，其他删除
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and id <> ".$first_row['id']);
			return;
		}
		else
		{
			$ids = array(0);
			foreach($result as $row)
			{
				array_push($ids,$row['id']);
			}
			//删除禁用购物车的产品			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and id in (".implode(",",$ids).")");
			return;
		}
	}
	
	//2. 处理按商户禁用的状态 cart_type 2
	$result = $GLOBALS['db']->getAll("select dc.id,dc.deal_id,dc.supplier_id from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."deal as d on dc.deal_id = d.id where dc.session_id = '".es_session::id()."' and dc.user_id = ".intval($GLOBALS['user_info']['id'])." and d.cart_type = 2");
	if($result)
	{
		if($first_row['cart_type']==2)
		{
			//保留禁用商户的产品以及同商户商品，其他删除
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and supplier_id <> ".$first_row['supplier_id']);
			return;
		}
		else
		{
			$ids = array(0);
			foreach($result as $row)
			{
				array_push($ids,$row['supplier_id']);
			}
			//删除禁用商户的产品以及同商户商品
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and supplier_id in (".implode(",",$ids).")");
			return;
		}
	}
	
	//3. 处理按商品禁用的状态 cart_type 1
	$result = $GLOBALS['db']->getAll("select dc.id,dc.deal_id,dc.supplier_id from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."deal as d on dc.deal_id = d.id where dc.session_id = '".es_session::id()."' and dc.user_id = ".intval($GLOBALS['user_info']['id'])." and d.cart_type = 1");
	if($result)
	{
		if($first_row['cart_type']==1)
		{
			//保留禁用商品以及其他款式的商品，其他删除
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and deal_id <> ".$first_row['deal_id']);
			return;
		}
		else
		{
			$ids = array(0);
			foreach($result as $row)
			{
				array_push($ids,$row['deal_id']);
			}
			//删除禁用商户的产品以及同款商品
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])." and deal_id in (".implode(",",$ids).")");
			return;
		}
	}
	
}

/**
 * 验证购物车
 */
function check_cart($id,$number)
{

    $cart_data = $GLOBALS['db']->getRow("select deal_id,id from ".DB_PREFIX."deal_cart where id=".$id);
   
	$cart_result = load_cart_list($cart_data['deal_id']);
	$cart_item = $cart_result['cart_list'][$id];

	if(empty($cart_item))
	{
		$result['info'] = "非法的数据";
		$result['status'] = 0;
		return $result;
	}
	if($number<=0)
	{
		$result['info'] = "数量不能为0";
		$result['status'] = 0;
		return $result;
	}
	$add_number = $number - $cart_item['number'];
	
	require_once(APP_ROOT_PATH."system/model/deal.php");		
	
	//属性库存的验证
	
	$attr_setting_str = '';
	if($cart_item['attr']!='')
	{
		$attr_setting_str = $cart_item['attr_str'];
	}	
	if($attr_setting_str!='')
	{
		$check = check_deal_number_attr($cart_item['deal_id'],$attr_setting_str,$add_number);
		if($check['status']==0)
		{
			$result['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
			$result['status'] = 0;
			return $result;
		}
	}
	
	//属性库存的验证
	$check = check_deal_number($cart_item['deal_id'],$add_number,true);
	if($check['status']==0)
	{
		$result['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
		$result['status'] = 0;
		return $result;
	}
	
	//验证时间
	$checker = check_deal_time($cart_item['deal_id']);
	if($checker['status']==0)
	{
		$result['info'] = $checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']];
		$result['status'] = 0;
		return $result;		
	}
	//验证时间
	
	
	$result['status'] = 1;
	return $result;
}


/**
 * 为购物车中的商品列表按商户分组并获取商家信息
 * @param unknown_type $goods_list
 */
function cart_list_group($goods_list)
{
	$group_list = array();
	foreach($goods_list as $k=>$v)
	{
		$group_list["sid_".$v['supplier_id']]['goods_list'][] = $v;
		$group_list["sid_".$v['supplier_id']]['supplier_id'] = $v['supplier_id'];
	}
	return $group_list;
}

/**
 * 主单支付成功后，更新子单的金额和订单状态，优惠数据等
 * @param unknown $order_id
 */
function syn_order_done($order_id){
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);

    if($order_info['is_main']==1){  //如果是主单，则更新子单的金额和订单状态  
        $sub_order_info = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where is_main=0 and pay_status <> 2 and order_id = ".$order_id);   
        $ecv_payment_memo='';
        $account_payment_memo='';
        $third_payment_memo='';
        $third_payment_id = $order_info['payment_id'];
        $ecv_order_data = array();
        $account_order_data = array();
        $third_order_data = array();

        // 订单拆分时部分均摊金额临时变量
        $tempfee = 0; // 运费
        $tempecv = 0; // 红包抵扣
        $tempaccount = 0; // 余额支付部分
        // $tempdisc = 0; // 折扣金额
        $tempCount = 0; // 拆分的订单数量
        
        $total_price_all = 0;
        foreach($sub_order_info as $k=>$v){
            if($v['total_price'] > $v['youhui_money']){  //子单由商家的优惠劵全部抵扣，不参与红包，余额支付，第三方支付拆分
                $total_price_all+=($v['total_price']-$v['youhui_money']);
                $tempCount++;
            }
        }

        foreach($sub_order_info as $k=>$v){
            
            if($v['total_price'] > $v['youhui_money']){  //子单由商家的优惠劵全部抵扣，不参与红包，余额支付，第三方支付拆分
            
                $rate = ($v['total_price'] -  $v['youhui_money'] ) / $total_price_all; //主单和子单的比率
    
                $sub_data = array();
    
                if (($k+1) == $tempCount) {
                	$sub_data['payment_fee'] = $order_info['payment_fee'] - $tempfee;
    	            $sub_data['ecv_money'] = $order_info['ecv_money'] - $tempecv;
    	            $sub_data['account_money'] = $order_info['account_money'] - $tempaccount;
    	            // $sub_data['discount_price'] = $order_info['discount_price'] - $tempdisc;
                } else {
                	$sub_data['payment_fee'] = round($order_info['payment_fee'] * $rate, 2);
                	$tempfee += $sub_data['payment_fee'];
    	            $sub_data['ecv_money'] = round($order_info['ecv_money'] * $rate, 2);
    	            $tempecv += $sub_data['ecv_money'];
    	            $sub_data['account_money'] = round($order_info['account_money'] * $rate, 2);
    	            $tempaccount += $sub_data['account_money']; 
    	            // $sub_data['discount_price'] = round($order_info['discount_price'] * $rate, 2);
    	            // $tempdisc += $sub_data['discount_price'];
                }
                      
                $sub_data['pay_status'] = $order_info['pay_status'];
                $sub_data['total_price'] = $sub_data['pay_amount'] = $v['total_price'] + $sub_data['payment_fee'];
                $sub_data['order_status'] = $order_info['order_status'];
              /*  $sub_data['return_total_score'] = $order_info['return_total_score']; */
              /*  $sub_data['return_total_money'] = $order_info['return_total_money']; */
                $sub_data['ecv_id'] = $order_info['ecv_id'];
                $sub_data['payment_id'] = $order_info['payment_id'];
                
                $sub_data['bank_id'] = $order_info['bank_id'];
                $sub_data['promote_description'] = $order_info['promote_description'];
                $sub_data['promote_arr'] = $order_info['promote_arr'];
                
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_order", $sub_data, $mode = 'UPDATE', "id=".$v['id'], $querymode = 'SILENT');
                
            }else{
                $sub_data = array();
                $sub_data['pay_status'] = $order_info['pay_status'];
                $sub_data['pay_amount'] = $v['total_price'];
                $sub_data['promote_description'] = $order_info['promote_description'];
                $sub_data['promote_arr'] = $order_info['promote_arr'];
                $sub_data['order_status'] = $order_info['order_status'];
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_order", $sub_data, $mode = 'UPDATE', "id=".$v['id'], $querymode = 'SILENT');
                
            }
            
            $rs = $GLOBALS['db']->affected_rows();
            if($rs){
                //生成子单的付款单号
                
                if($sub_data['ecv_money'] > 0){  //生成代金劵付款单号     
                    $ecv_payment_memo .='订单号：'.$v['order_sn']."(".round($sub_data['ecv_money'],2)."元),";
                    $data=array();
                    $data['order_id'] = $v['id'];
                    $data['order_sn'] = $v['order_sn'];
                    $data['type'] = $v['type'];
                    $data['money'] = $sub_data['ecv_money'];
                    $ecv_order_data[] = $data;
                }
                if($sub_data['account_money'] > 0){  //生成余额付款单号
                    
                    $account_payment_memo .='订单号：'.$v['order_sn']."(".round($sub_data['account_money'],2)."元),";
                    $data=array();
                    $data['order_id'] = $v['id'];
                    $data['order_sn'] = $v['order_sn'];
                    $data['type'] = $v['type'];
                    $data['money'] = $sub_data['account_money'];
                    $account_order_data[] = $data;
                }
                if($sub_data['payment_id'] > 0){  //生成第三方支付付款单号
                    $third_payment_fee =  $sub_data['total_price'] - $sub_data['ecv_money'] - $sub_data['account_money'];
                    $third_payment_memo .='订单号：'.$v['order_sn']."(".round($third_payment_fee,2)."元),";
                    $data=array();
                    $data['order_id'] = $v['id'];
                    $data['order_sn'] = $v['order_sn'];
                    $data['type'] = $v['type'];
                    $data['money'] = $third_payment_fee;
                    $third_order_data[] = $data;
                }
                
                
                
                $msg_content = '您的订单<'.$v['order_sn'].'>已成功付款';
                send_msg_new($v['user_id'], $msg_content, 'account', array('type' => 9, 'data_id' => $v['id']));
                
                
                //收入, by hc 0507,增加报表
                modify_statements($sub_data['total_price'], 0, $v['order_sn']."订单成功付款"); //总收入
                modify_statements($sub_data['total_price'], 1, $v['order_sn']."订单成功付款"); //订单支付收入
                
                modify_statements($sub_data['total_price'], 8, $v['order_sn']."订单成功付款");  //增加营业额
                $balance_total = $GLOBALS['db']->getOne("select sum(balance_total_price)+sum(add_balance_price_total) from ".DB_PREFIX."deal_order_item where order_id = ".$v['id']);
                modify_statements($balance_total, 9, $v['order_sn']."订单成功付款");  //增加营业额中的成本
                
                order_log($v['order_sn']."订单付款完成", $v['id']);
                
                //通知商户

                //发送微信通知
                send_supplier_msg($v['supplier_id'], 'send', $v['id']);
                $weixin_conf = load_auto_cache("weixin_conf");
                if($weixin_conf['platform_status']==1)
                {
                    $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$v['supplier_id']);
                    send_wx_msg("OPENTM201490080", $v['user_id'], $wx_account,array("order_id"=>$v['id']));
                } 
                
                //如果是驿站订单，则进行驿站配送
                if($v['type']==4){
                    setDist($v);
                }

            }
  
        } 
        $payment_notice = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment_notice where order_type=3 and is_paid = 1 and order_id =" .$order_info['id']);
        $ecv_payment_id = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name='Voucher'"));
        $account_payment_id = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name='Account'"));
        foreach($payment_notice as $kk=>$vv){
            $payment_memo='';
            $sub_order_data='';
            if($vv['payment_id']==$ecv_payment_id){
                $payment_memo = $ecv_payment_memo;
                $sub_order_data=serialize($ecv_order_data);
            }elseif($vv['payment_id']==$account_payment_id){
                $payment_memo = $account_payment_memo;
                $sub_order_data=serialize($account_order_data);
            }elseif($vv['payment_id']==$third_payment_id){
                $payment_memo = $third_payment_memo;
                $sub_order_data=serialize($third_order_data);
            }
            $payment_memo = substr($payment_memo,0,-1);
            $GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set sub_order_data ='".$sub_order_data."' , memo='".$payment_memo."' where id=".$vv['id']);
        }
    }else{
		$payment_notice = $GLOBALS['db']->getRow("select pn.id,p.class_name from ".DB_PREFIX."payment_notice as pn left join ".DB_PREFIX."payment AS p on pn.payment_id=p.id where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid=1 and money>0");
		if($payment_notice['class_name']=="Cod"){
			$msg_content = '您的订单<'.$order_info['order_sn'].'>已下单成功';
			send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 9, 'data_id' => $order_id));
			order_log($order_info['order_sn']."订单成功下单", $order_id);
		}else{
			$msg_content = '您的订单<'.$order_info['order_sn'].'>已成功付款';
			send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 9, 'data_id' => $order_id));
			
			
			//收入, by hc 0507,增加报表
			modify_statements($order_info['total_price'], 0, $order_info['order_sn']."订单成功付款"); //总收入
			modify_statements($order_info['total_price'], 1, $order_info['order_sn']."订单成功付款"); //订单支付收入
			
			modify_statements($order_info['total_price'], 8, $order_info['order_sn']."订单成功付款");  //增加营业额
			$balance_total = $GLOBALS['db']->getOne("select sum(balance_total_price)+sum(add_balance_price_total) from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']);
			modify_statements($balance_total, 9, $order_info['order_sn']."订单成功付款");  //增加营业额中的成本
			
			order_log($order_info['order_sn']."订单付款完成", $order_id);
		}
		
        
        
        //通知商户
        $supplier_list = $GLOBALS['db']->getAll("select distinct(supplier_id) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
        foreach($supplier_list as $row)
        {
            //发送微信通知
            send_supplier_msg($row['supplier_id'], 'send', $order_id);
            $weixin_conf = load_auto_cache("weixin_conf");
            if($weixin_conf['platform_status']==1)
            {
                $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$row['supplier_id']);
                send_wx_msg("OPENTM201490080", $order_info['user_id'], $wx_account,array("order_id"=>$order_id));
            }
        }
        
        //如果是驿站订单，则进行驿站配送
        if($order_info['type']==4){
            setDist($order_info);
        }
    }
    
}

/**
 * 分配驿站和驿站消息推送
 * @param array $order_info 订单信息
 */
function setDist($order_info)
{
	require_once(APP_ROOT_PATH."system/model/Distribute.php");
    $dist_id = Distribute::setDist($order_info['consignee_id']);
    if($dist_id > 0) {
        setDistAndAgent($dist_id, $order_info['id']);
    }
}

/**
 * 购物车提交时判断是否允许提交订单
 *$checked_ids = Array
 * (
 *     [0] => 	Array(
			[id] => 11 int 购物车id
			[attr] => 11 varchar 购买的相关属性的ID，用半角逗号分隔
			[attr_str] => 11 varchar 购买的相关属性的ID，用半角逗号分隔
			[number] => 11 int 数量
		)
 * )
		
 */
function cheak_wap_cart($checked_ids){

	$user_info=$GLOBALS['user_info'];
	if($checked_ids){
		 
		$deal_num=array();
		foreach($checked_ids as $k=>$v){
			$cart_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cart where user_id = " . $user_info['id']." and id=".$v['id']);
			$deal_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id=".$cart_info['deal_id']);
			$checked_ids[$k]['deal_id'] = $deal_info['id'];
			$checked_ids[$k]['deal_info'] = $deal_info;
			 
			if($v['attr_str']){
				$deal_num[$deal_info['id']][$v['attr_str']]+=$v['number'];
			}
			$deal_num[$deal_info['id']]['number']+=$v['number'];
			 
			 
		}
		$checked_id = array();
		foreach($checked_ids as $k=>$v){
			$checked_id[] = $v['id'];
			$checked_ids[$k]['attr_number']=0;
			if($v['attr_str']){
				$checked_ids[$k]['attr_number']=intval($deal_num[$v['deal_id']][$v['attr_str']]);   //有属性的商品购物车一共有几个
			}
			$checked_ids[$k]['total_number']=intval($deal_num[$v['deal_id']]['number']);  //无属性的商品购物车一共有几个
			 
		}

		/* 判断购物车选择中的商品，是否有平台自营的商品和入驻商家的商品，平台自营商品不能与商家商品一起下单   */
// 		$deal_cart_status = check_deal_cart($checked_id);
// 		if(!$deal_cart_status){ //平台自营商品不能与商家商品一起下单
// 			$result['status'] = 0;
// 			$result['info'] = '平台自营商品不能与商家商品一起下单';
// 			return $result;
// 		}
		 
		require_once(APP_ROOT_PATH."system/model/deal.php");
		foreach($checked_ids as $k=>$v){

			$deal_info=$v['deal_info'];
			 
			/*验证数量*/
			//定义几组需要的数据
			//1. 本团购记录下的购买量
			$deal_buy_count = $deal_info['buy_count'];
			//2. 本团购当前会员的购物车中数量
			$number = $v['number'];
			$total_number = $v['total_number'];

			$attr_number = $v['attr_number'];
			$attr_str = $v['attr_str'];
			//3. 本团购当前会员已付款的数量
			$deal_user_paid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.is_main=0 and o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status = 2 and oi.deal_id = ".$deal_info['id']." and o.is_delete = 0"));

			if($total_number<=0)
			{
				$result['status'] = 0;
				$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
				$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],1)." ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
				return $result;
				break;
			}

			if($deal_info['max_bought'] == 0||($total_number>$deal_info['max_bought']&&$deal_info['max_bought']>=0))
			{
				$result['status'] = 0;
				$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
				$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$deal_info['max_bought'])." ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
				return $result;
				break;
			}

			if( $total_number < $deal_info['user_min_bought'] && $deal_info['user_min_bought'] > 0)
			{
				$result['status'] = 0;
				$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
				$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],$deal_info['user_min_bought']).",购物车数量为".($total_number)."件, ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
				return $result;
				break;
			}

			if( $deal_user_paid_count  + $total_number > $deal_info['user_max_bought'] && $deal_info['user_max_bought'] > 0)
			{
				$deal_buy_num =  $deal_user_paid_count;
				$result['status'] = 0;
				$result['data'] = DEAL_ERROR_MAX_USER_BUY;  //用户最大购买数超出
				$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MAX_BOUGHT'],$deal_info['user_max_bought']).",已购买".$deal_buy_num."件, ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
				return $result;
				break;
			}
			 

			$now = NOW_TIME;
			//开始验证团购时间
			if($deal_info['begin_time']!=0)
			{
				//有开始时间
				if($now<$deal_info['begin_time'])
				{
					$result['status'] = 0;
					$result['data'] = DEAL_NOTICE;  //未上线
					$result['info'] = $deal_info['sub_name']." ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
					return $result;
					break;
				}
			}

			 
			if($deal_info['end_time']!=0)
			{
				//有结束时间
				if($now>=$deal_info['end_time'])
				{
					$result['status'] = 0;
					$result['data'] = DEAL_HISTORY;  //过期
					$result['info'] = $deal_info['sub_name']." ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
					return $result;
					break;
				}
			}

			$attr_stock_cfg = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = ".$deal_info['id']." and locate(attr_str,'".$attr_str."') > 0 ");
			 
			$stock_setting = $attr_stock_cfg?intval($attr_stock_cfg['stock_cfg']):-1;
			$stock_attr_setting = $attr_stock_cfg['attr_str'];
			// 获取到当前规格的库存

			/*验证数量*/
			//定义几组需要的数据
			//1. 本团购记录下的购买量
			$deal_buy_count = intval($attr_stock_cfg['buy_count']);

			if($stock_setting == 0||($attr_number>$stock_setting&&$stock_setting>=0))
			{
				$result['status'] = 0;
				$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
				$result['info'] = $deal_info['sub_name'].$stock_attr_setting." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$stock_setting)." ".$GLOBALS['lang']['DEAL_ERROR_'.$result['data']];
				$result['attr'] = $stock_attr_setting;
				return $result;
				break;
			}

		}
		$result['status'] = 1;
		$result['info'] = '验证通过';
		return $result;
		 
	}
	 
}

/**
 * 获取配送费用
 * @param $cart_list    购物车过来的商品数据
 * @param $user_consignee_id    用户的配送地址ID
 * @return array{   //p_wl 为平台物流，  p_yz平台驿站，  数字编号为商户
            [p_wl] => Array
                    (
                        [deal_item] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 285
                                    [express_fee] => 0
                                )

                            [1] => Array
                            (
                                [id] => 287
                                [express_fee] => 20
                            )

                        )
                        [total_fee] => 20
                    )

            [22] => Array
            (
                [deal_item] => Array
                            (
                                [0] => Array
                                (
                                    [id] => 289
                                    [express_fee] => 14
                                )
                            )
                [total_fee] => 14
            )
 * }
 */
function get_express_fee($cart_list,$user_consignee_id){

    $express_fee_arr = array(); //运费数组
    if (intval($user_consignee_id)){//用户的配送地址
        $consignee_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id=".intval($user_consignee_id));
        if (empty($consignee_item)){   //如果用户没有配送地址，直接返回空数组
            return $express_fee_arr;
        }
    }else//用户地址ID 必须存在
        return $express_fee_arr;



    //获取商品的配送模板
    $deal_ids = array();
    foreach ($cart_list as $k=>$v){
        $deal_ids[] = $v['deal_id'];
        $deal_number_arr[$v['deal_id']] = $v['number'];
    }

    $deal_list = $GLOBALS['db']->getAll("select id,name,supplier_id,weight,platform_type,delivery_type,carriage_template_id from ".DB_PREFIX."deal where id in (".implode($deal_ids,",").")");
    $carriage_template_ids = array();
    foreach ($deal_list as $k=>$v){
        //购买的件数
        $deal_list[$k]['number'] = $deal_number_arr[$v['id']];
        $carriage_template_ids[] = $v['carriage_template_id'];
    }


    if ($carriage_template_ids){



        //获取运费模板价格
        $sql = "select ct.id,ct.name,ct.supplier_id,ct.carriage_type,ct.valuation_type,cd.id as carriage_detail_id,cd.express_start,cd.express_plus,cd.express_postage,cd.express_postage_plus,cd.region_ids from  ".DB_PREFIX."carriage_template ct LEFT JOIN ".DB_PREFIX."carriage_detail cd on ct.id=cd.carriage_id WHERE ct.id in (".implode($carriage_template_ids,",").")";
        $carriage_template_list = $GLOBALS['db']->getAll($sql);
        $format_carriage_templat_list = array();
        foreach ($carriage_template_list as $k=>$v){
            $format_carriage_templat_list[$v['id']][]=$v;
        }


        //根据模板类型来计费，同一类型的模板用重量或者件数累加计费
        //把商品根据商户或者平台来分割数组-根据配送方式分组
        $supplier_by_deals = array();
        foreach ($deal_list as $k=>$v){
            if($v['supplier_id']>0){
                $supplier_by_deals[$v['supplier_id']][] = $v;
            }else{
                if($v['delivery_type']==1){//平台物流配送
                    $supplier_by_deals['p_wl'][] = $v;
                }elseif ($v['delivery_type']==3){//平台驿站配送
                    $supplier_by_deals['p_yz'][] = $v;
                }
            }
        }

        foreach ($supplier_by_deals as $k=>$v){

            $carriage_temp = array();

            foreach($v as $deal_k=>$deal_v){
                $carriage_id = 0;           //模板运费ID
                $carriage_type = 0;         //运费类型：1自定义，2平台/卖家承担运费（免运费）
                $carriage_detail_id = 0;    //模板运费详情ID
                $express_start = 0;         //初始运费商品：多少件或kg
                $express_postage = 0;       //运费金额
                $express_plus = 0;          //增加运费商品：多少件或kg
                $express_postage_plus = 0;  //运费根据商品增加增加的金额
                $valuation_type = 0;        //计价类型：1按件数，2按重量
                $express_info_temp = array();   //运费计算详情数组

                //根据模板ID 遍历，模板运费详情
                foreach ($format_carriage_templat_list[$deal_v['carriage_template_id']] as $ck=>$cv){
                    $carriage_id = $cv['id'];
                    if ($cv['carriage_type']==2){//如果是卖家承担运费直接跳过，运费详情的计算
                        $carriage_type = $cv['carriage_type'];
                        break;
                    }
                    if($cv['region_ids']){  //判断是否存在指定运费地区内
                        if (in_array($consignee_item['region_lv3'],explode(",",$cv['region_ids']))){
                            $carriage_detail_id = intval($cv['carriage_detail_id']);
                            $express_start = $cv['express_start'];
                            $express_postage = $cv['express_postage'];
                            $express_plus = $cv['express_plus'];
                            $express_postage_plus = $cv['express_postage_plus'];
                            $valuation_type = $cv['valuation_type'];
                            break;
                        }
                    }else{  //默认运费详情
                        $carriage_detail_id = intval($cv['carriage_detail_id']);
                        $express_start = $cv['express_start'];
                        $express_postage = $cv['express_postage'];
                        $express_plus = $cv['express_plus'];
                        $express_postage_plus = $cv['express_postage_plus'];
                        $valuation_type = $cv['valuation_type'];
                    }
                }
                //运费计算详情数组
                $express_info_temp = array(
                    'carriage_id'=>$carriage_id,
                    'carriage_detail_id'=>$carriage_detail_id,
                    'carriage_type' => $carriage_type,
                    'express_start'=>$express_start,
                    'express_postage'=>$express_postage,
                    'express_plus'=>$express_plus,
                    'express_postage_plus'=>$express_postage_plus,
                    'valuation_type'=>$valuation_type,
                );

                //根据运费计算详情建立数组
                if (empty($carriage_temp[$carriage_id."_".$carriage_detail_id]['express_detail'])){
                    $carriage_temp[$carriage_id."_".$carriage_detail_id]['express_detail']=$express_info_temp;
                }
                //同一个运费模板并且计算规则相同的商品放入一个数组
                $carriage_temp[$carriage_id."_".$carriage_detail_id]['deals'][] = $deal_v;
            }

            //遍历获取到使用的运费模板计算规则
            foreach ($carriage_temp as $ctemp_k=>$ctemp_v){
                $temp_express_fee = 0;
                $temp_weight = 0;
                $temp_number = 0;
                $temp_express_detail = $ctemp_v['express_detail'];

                //同一运费计算规则的商品，件数或者数量进行累加计算总运费
                foreach ($ctemp_v['deals'] as $deal_item_k=>$deal_item_v){
                    $express_fee_arr[$k]['deal_ids'][] = array("id"=>$deal_item_v['id'],"name"=>$deal_item_v['name'],'weight'=>$deal_item_v['weight'],'number'=>$deal_item_v['number'],'express_detail'=>$temp_express_detail);
                    $temp_weight += $deal_item_v['weight']*$deal_item_v['number'];
                    $temp_number += $deal_item_v['number'];
                }

                if($temp_express_detail['carriage_type']==2){//卖家承担运费
                    $temp_express_fee=0;
                }else{

                    //按件计费，按照重量计费计算最后不能有小数
                    if($temp_express_detail['valuation_type']==1){//按件计费
                        if($temp_number <= $temp_express_detail['express_start']){
                            $temp_express_fee = $temp_express_detail['express_postage'];
                        }else{
                            $temp_express_fee = $temp_express_detail['express_postage']+ceil(($temp_number - $temp_express_detail['express_start'])/$temp_express_detail['express_plus'])*$temp_express_detail['express_postage_plus'];
                        }
                    }elseif($temp_express_detail['valuation_type']==2){//按照重量计费
                        if($temp_weight <= $temp_express_detail['express_start']){
                            $temp_express_fee = $temp_express_detail['express_postage'];
                        }else{
                            $temp_express_fee = $temp_express_detail['express_postage']+ceil(($temp_weight - $temp_express_detail['express_start'])/$temp_express_detail['express_plus'])*$temp_express_detail['express_postage_plus'];
                        }

                    }
                }
                $express_fee_arr[$k][$ctemp_k]+=$temp_express_fee;
                //分类 平台自营-物流、平台自营-驿站、商户
                $express_fee_arr[$k]['total_fee'] += $temp_express_fee;

            }
        }
        //运费只能为整数
        foreach ($express_fee_arr as $k=>$v){
            $express_fee_arr[$k]['total_fee'] = round($v['total_fee']);
        }
    }

    return $express_fee_arr;
}
/**
 * 积分抵现统计
 * @param unknown_type $goods_list
 */
function score_purchase_count($user_score,$total_price,$pay_price)
{
	$arr=array();
	
	$arr['score_purchase_switch']=app_conf("SCORE_PURCHASE_SWITCH");
	$arr['user_score']=$user_score;
	$score_purchase_exchange_money=app_conf("SCORE_PURCHASE_EXCHANGE_MONEY");
	$score_purchase_max_money=app_conf("SCORE_PURCHASE_MAX_MONEY");
	$score_purchase_max_proportion_money=app_conf("SCORE_PURCHASE_MAX_PROPORTION_MONEY");
	
	if($score_purchase_max_money>0&&$score_purchase_max_proportion_money>0){
		$score_purchase_max_money=floor($score_purchase_max_money*100)/100;
		$score_purchase_max_proportion_money=floor($score_purchase_max_proportion_money*$total_price*100)/100;
		$exchange_money=$score_purchase_max_money>$score_purchase_max_proportion_money?$score_purchase_max_proportion_money:$score_purchase_max_money;
	}elseif($score_purchase_max_money<=0&&$score_purchase_max_proportion_money>0){
		$exchange_money=0;
	}elseif($score_purchase_max_money>0&&$score_purchase_max_proportion_money<=0){
		$exchange_money=0;
	}else{
		$exchange_money=0;
	}

	if($exchange_money==0){
		$exchange_money=0;
	}else{
		$exchange_money=$pay_price>$exchange_money?$exchange_money:$pay_price;
	}
	$user_max_money=floor($user_score*($score_purchase_exchange_money/100)*100)/100;

	if($user_max_money>$exchange_money){
		$arr['user_use_score']=ceil($exchange_money*(100/$score_purchase_exchange_money));
		$arr['exchange_money']=$exchange_money;
	}else{
		$arr['user_use_score']=$user_score;
		$arr['exchange_money']=$user_max_money;
	}
	$arr['score_purchase_exchange_money']=$score_purchase_exchange_money;
	return $arr;
}
/**
 * 积分抵现
 * @param unknown_type $goods_list
 */
function score_purchase_paid($score_purchase,$order_id)
{
	if($order_id>0){
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		$user_score = $GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$order['user_id']);
		$msg='订单号'.$order['order_sn'].'，使用'.$score_purchase['user_use_score'].'积分，抵扣'.round($order['exchange_money'],2).'元';
		order_log("积分抵现成功", $order['id']);
		modify_account(array('money'=>0,'score'=>'-'.$score_purchase['user_use_score']),$order['user_id'],$msg);
	}
	return true;
}
?>