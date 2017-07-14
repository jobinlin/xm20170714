<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 与订单相关的函数库
 */

/**
 * 使用消费券
 * @param unknown_type $password 密码
 * @param unknown_type $location_id 所消费的门店ID
 * @param unknown_type $account_id 执行使用的商家账号ID
 * @param unknown_type $send_return 是否要发放奖励
 * @param unknown_type $send_notify 是否发放通知(短信/邮件)
 * return:true,false true:已使用掉  false:未使用掉
 */
function use_coupon($password,$location_id=0,$account_id=0,$send_return=false,$send_notify=false)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set is_balance = 1 ,location_id=".$location_id.", confirm_account = ".$account_id.",confirm_time=".NOW_TIME." where password = '".$password."' and confirm_time = 0");
	$coupon_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where password = '".$password."'");
	
	if($GLOBALS['db']->affected_rows()&&$coupon_data)
	{		
	    if($coupon_data['refund_status']==1){
	        $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3 where id=".$coupon_data['id']);
	    }
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set consume_count = consume_count + 1,location_id=".$location_id." where id = ".$coupon_data['order_deal_id']);
		update_order_cache($coupon_data['order_id']);
		distribute_order($coupon_data['order_id']);
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$coupon_data['order_id']);
		if($order_info)
		{
			$order_msg = "订单号".$order_info['order_sn']." ";
		}
		
		if($send_return)
		{
			if($coupon_data['coupon_money']>0||$coupon_data['coupon_score']>0)
			{
				$money = $coupon_data['coupon_money'];
				$score = $coupon_data['coupon_score'];
				require_once(APP_ROOT_PATH."system/model/user.php");
				$log = $order_msg.$password."消费券验证成功";
				modify_account(array("money"=>$money,"score"=>$score),$coupon_data['user_id'],$log);	
			}
		}
		if($send_notify)
		{
			send_use_coupon_sms(intval($coupon_data['id'])); //发送消费券确认消息
			send_use_coupon_mail(intval($coupon_data['id'])); //发送消费券确认消息
		}
		update_balance($coupon_data['id'],$coupon_data['deal_id']);
		
		$balance_price = $coupon_data['balance_price'] + $coupon_data['add_balance_price'] ;
		 		
		if($order_info['is_all_balance'] == 0){  //是否整笔订单一起结算
        	require_once(APP_ROOT_PATH."system/model/supplier.php");
    		modify_supplier_account("-".$balance_price, $coupon_data['supplier_id'], 1, $order_msg.$password."消费券验证成功");  //解冻资金
    		modify_supplier_account($balance_price, $coupon_data['supplier_id'], 2, $order_msg.$password."消费券验证成功");  //等结算金额增加
    		
    		modify_supplier_account($coupon_data['coupon_price'], $coupon_data['supplier_id'], 6, $order_msg.$password."消费券验证成功");  //团购商城销售额增加(不是结算价)
    	}
			
			
		//代理商佣金增加
	    $agency_id=intval($GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id=".$coupon_data['supplier_id']));
	   
	    $money_admin=$coupon_data['coupon_price']-$coupon_data['balance_price'] - $coupon_data['add_balance_price'];  //该卷总利润
	    if($money_admin > 0){
			modify_agency_account($money_admin,$agency_id,1,$order_msg.$password."消费券验证成功");
	    }
		
		
		modify_statements($coupon_data['coupon_price'], 11, $order_msg.$password."消费券验证成功"); //增加消费额
		modify_statements($balance_price, 12, $order_msg.$password."消费券验证成功"); //增加消费额成本
		
		$msg_content = '您的消费码<'.$coupon_data['password'].'>已验证成功';
		send_msg_new($coupon_data['user_id'], $msg_content, 'confirm', array('type' => 2, 'data_id' => $coupon_data['order_deal_id']));
		// send_msg($coupon_data['user_id'], "消费券验证成功", "orderitem", $coupon_data['order_deal_id']);
		
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1)
		{
				$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$coupon_data['supplier_id']);
				$rs = send_wx_msg("OPENTM200738546", $order_info['user_id'], $wx_account,array("coupon_sn"=>$password));

		}
		send_recommend_user_money(0,$coupon_data['id']);
		auto_over_status($coupon_data['order_id']); //检测自动结单
	}
	return $coupon_data['confirm_time']>0;
}

/**
 * 收货操作：收货后发放积分，钱的返还，更新商家的结算
 * @param unknown_type $delivery_sn
 * @param unknown_type $order_item_id 订单商品ID，将会确认相关的所有订单的同序号发货号。
 */
function order_confirm_delivery($delivery_sn,$express_id,$order_id)
{

    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".intval($order_id));
    if($order_info)
    {
        //未申请退款和拒绝退款的，可以收货
        $delivery_notices = $GLOBALS['db']->getAll("select dn.* from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id=doi.id where doi.refund_status<>2 and doi.is_balance=0 and dn.order_id = ".$order_info['id']." and dn.notice_sn = '".$delivery_sn."' and dn.express_id=".$express_id);
        $order_item_ids = array(0);
        foreach($delivery_notices as $k=>$v)
        {
            $order_item_ids[] = $v['order_item_id'];
        }
        
        $sql = "update ".DB_PREFIX."deal_order_item set is_arrival = 1,consume_count = consume_count + 1 where is_arrival <> 1 and id in (".implode(",", $order_item_ids).")";
        $GLOBALS['db']->query($sql);
          if($GLOBALS['db']->affected_rows())
        {
            
            $log = $order_info['order_sn']."订单已收货";
            //团购商城订单,已收货的单个商品逐一结算
            foreach($order_item_ids as $k=>$v){
                $order_info_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$v);
                //代理商佣金增加
                $agency_id=intval($GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id=".$order_info_item['supplier_id']));
                if(floatval($order_info_item['total_price']) > 0){
                    $money_admin=$order_info_item['total_price']-$order_info_item['balance_total_price'] - $order_info_item['add_balance_price_total'];
                    modify_agency_account($money_admin,$agency_id,1,$log);
                }
 
            }

            $delivery_refund_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id=doi.id where doi.refund_status=1 and doi.is_balance=0 and dn.order_id = ".$order_info['id']." and dn.notice_sn = '".$delivery_sn."' and dn.express_id=".$express_id);
            
            if(intval($delivery_refund_count)==0){
                //没有存在这个包裹中有退款申请中的商品时，可以确认收货
                $GLOBALS['db']->query("update ".DB_PREFIX."delivery_notice set is_arrival = 1,arrival_time = '".NOW_TIME."' where notice_sn = '".$delivery_sn."' and is_arrival <> 1 and order_id = ".$order_info['id']." and express_id=".$express_id);              
            }
           
            
            $return_total = $GLOBALS['db']->getRow("select sum(return_total_score) as return_total_score,
					sum(return_total_money) as return_total_money,
					sum(total_price) as total_price,
					sum(balance_total_price) as balance_total_price,
					sum(add_balance_price_total) as add_balance_price_total from ".DB_PREFIX."deal_order_item where id in (".implode(",", $order_item_ids).")");

            	
            if($return_total['return_total_score']>0||$return_total['return_total_money']>0)
            {
                $money = $return_total['return_total_money'];
                $score = $return_total['return_total_score'];
                require_once(APP_ROOT_PATH."system/model/user.php");
                modify_account(array("money"=>$money,"score"=>$score),$order_info['user_id'],$log);
            }

            //订单商品
            $sql = "update ".DB_PREFIX."deal_order_item set is_balance = 1 where id in (".implode(",", $order_item_ids).") and is_balance = 0";
            $GLOBALS['db']->query($sql);
            	
            $is_refuse_delivery = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."delivery_notice where is_arrival = 2 and order_id = ".$order_id);
            if(!$is_refuse_delivery)
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refuse_delivery = 0 where id = ".$order_id);
           
            if($order_info['is_all_balance'] == 0){  //是否整笔订单一起结算
                $balance_list = $GLOBALS['db']->getAll("select sum(balance_total_price) as balance_total_price,sum(add_balance_price_total) as add_balance_price_total, sum(total_price) as total_price , supplier_id , id from ".DB_PREFIX."deal_order_item  where id in (".implode(",", $order_item_ids).") group by supplier_id");
                foreach($balance_list as $k=>$v)
                {
                    //结算价要扣除商家的优惠金额
                    $balance_price = $v['balance_total_price'] + $v['add_balance_price_total'];
                    require_once(APP_ROOT_PATH."system/model/supplier.php");
                    modify_supplier_account("-".$balance_price, $v['supplier_id'], 1, $log);  //解冻资金
                    modify_supplier_account($balance_price, $v['supplier_id'], 2, $log);  //等结算金额增加
                    modify_supplier_account($v['total_price'], $v['supplier_id'], 6, $log);  //团购商城销售额增加(不是结算价) 
                }
            }

	
            $stat_balance_price = $return_total['balance_total_price']+$return_total['add_balance_price_total'];
            modify_statements($return_total['total_price'], 11, $log); //增加消费额
            modify_statements($stat_balance_price, 12,$log); //增加消费额成本
            	
            auto_over_status($order_info['id']); //检测自动结单
            update_order_cache($order_info['id']);
            distribute_order($order_info['id']);

            $weixin_conf = load_auto_cache("weixin_conf");
            $supplier_list = $GLOBALS['db']->getAll("select distinct(supplier_id) from ".DB_PREFIX."deal_order_item where id in (".implode(",", $order_item_ids).")");
			foreach($supplier_list as $row) {
				send_supplier_msg($row['supplier_id'], 'balance', $delivery_sn);
				if($weixin_conf['platform_status']==1) {			
					$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$row['supplier_id']);
					$order_item_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_order_item where supplier_id = ".$row['supplier_id']." and delivery_status = 1");
					send_wx_msg("OPENTM202314085", $order_info['user_id'], $wx_account,array("order_item_id"=>$order_item_id));
				}
			}
			send_recommend_user_money($order_item_ids);
            $result['status']=1;
            $result['ids']=$order_item_ids;
            return $result;
        }
    }
    return $result;
}

/**  
 * 无需发货的商品确认到货
 * 
 * */
function confirm_no_delivery($item_id){
    $order_info_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$item_id);
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_info_item['order_id']);
    $sql = "update ".DB_PREFIX."deal_order_item set is_arrival = 1,consume_count = consume_count + 1 where is_arrival <> 1 and id = ".$item_id;
    $GLOBALS['db']->query($sql);
    if($GLOBALS['db']->affected_rows())
    {
        //团购商城订单,已收货的单个商品逐一结算
        $log=$order_info['order_sn'].$order_info_item['name']."已收货";
        //代理商佣金增加
        $agency_id=intval($GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id=".$order_info_item['supplier_id']));
        if(floatval($order_info_item['total_price']) > 0){
            $money_admin=$order_info_item['total_price']-$order_info_item['balance_total_price'] - $order_info_item['add_balance_price_total'];
            modify_agency_account($money_admin,$agency_id,1,$log);
        }
        
        //返现返积分计算
        $return_total = $GLOBALS['db']->getRow("select return_total_score,
					return_total_money,
					total_price,
					balance_total_price,
					add_balance_price_total from ".DB_PREFIX."deal_order_item where id=".$order_info_item['id']);
        if($return_total['return_total_score']>0||$return_total['return_total_money']>0)
        {
            $money = $return_total['return_total_money'];
            $score = $return_total['return_total_score'];
            require_once(APP_ROOT_PATH."system/model/user.php");
            modify_account(array("money"=>$money,"score"=>$score),$order_info['user_id'],$log);
        }
        
        $sql = "update ".DB_PREFIX."deal_order_item set is_balance = 1 where id=".$order_info_item['id']." and is_balance = 0";
        $GLOBALS['db']->query($sql);
        
        $is_refuse_delivery = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where is_arrival = 2 and order_id = ".$order_info['id']);
        if(!$is_refuse_delivery)
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refuse_delivery = 0 where id = ".$order_info['id']);
    
        if($order_info['is_all_balance'] == 0){  //是否整笔订单一起结算
            $balance_row = $GLOBALS['db']->getRow("select balance_total_price,add_balance_price_total,total_price,supplier_id,id from ".DB_PREFIX."deal_order_item  where id=".$order_info_item['id']);
            $balance_price = $balance_row['balance_total_price'] + $balance_row['add_balance_price_total']  ;
            require_once(APP_ROOT_PATH."system/model/supplier.php");
            modify_supplier_account("-".$balance_price, $balance_row['supplier_id'], 1, $log);  //解冻资金
            modify_supplier_account($balance_price, $balance_row['supplier_id'], 2, $log);  //等结算金额增加
            modify_supplier_account($balance_row['total_price'], $balance_row['supplier_id'], 6, $log);  //团购商城销售额增加(不是结算价)
        }
        
        $stat_balance_price = $return_total['balance_total_price']+$return_total['add_balance_price_total'];
        modify_statements($return_total['total_price'], 11, $log); //增加消费额
        modify_statements($stat_balance_price, 12,$log); //增加消费额成本
    
        auto_over_status($order_info['id']); //检测自动结单
        update_order_cache($order_info['id']);
        distribute_order($order_info['id']);
        
        $weixin_conf = load_auto_cache("weixin_conf");
        if($weixin_conf['platform_status']==1)
        {
            $supplier_list = $GLOBALS['db']->getRow("select distinct(supplier_id) from ".DB_PREFIX."deal_order_item where id in =".$order_info_item['id']);
          
            $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_list['supplier_id']);
            $order_item_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_order_item where supplier_id = ".$supplier_list['supplier_id']." and delivery_status = 1");
            send_wx_msg("OPENTM202314085", $order_info['user_id'], $wx_account,array("order_item_id"=>$item_id));

        }

        $order_item_ids = array();
        $order_item_ids[] = $item_id;
        send_recommend_user_money($order_item_ids);
        return true;
    }else {
        return false;
    }
}


/**
 *
 * 驿站的收货
 * @param int $order_id 订单的ID
 */
function distribution_confirm_delivery($order_id){
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
    $result=array();
    $result['status']=0;
    $result['info']='收货失败';
    if($order_info['type']==4){  //驿站订单
        $order_info_arr = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where refund_status <> 2 and order_id = ".$order_id);
        $sql = "update ".DB_PREFIX."deal_order_item set is_arrival = 1,consume_count = consume_count + 1 where is_arrival <> 1 and order_id = ".$order_id ." and refund_status <> 2";
        $GLOBALS['db']->query($sql);
        if($GLOBALS['db']->affected_rows()){
               $return_total = array();
               $return_total['return_total_score'] =0;
               $return_total['return_total_money'] =0;
               $distribution_fee = 0;
               $order_item_ids = array();
               foreach($order_info_arr as $k=>$order_info_item){  

                $return_total['return_total_score'] += $order_info_item['return_total_score'];
                $return_total['return_total_money'] += $order_info_item['return_total_money'];
                $distribution_fee += $order_info_item['distribution_fee'];
                $sql = "update ".DB_PREFIX."deal_order_item set is_balance = 1 where id=".$order_info_item['id']." and is_balance = 0";
                $GLOBALS['db']->query($sql);
            
                $is_refuse_delivery = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where is_arrival = 2 and order_id = ".$order_info['id']);
                if(!$is_refuse_delivery)
                    $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refuse_delivery = 0 where id = ".$order_info['id']);
            
                $stat_balance_price = $order_info_item['balance_total_price']+$order_info_item['add_balance_price_total'];
				if($order_info['cod_money']>0){
					//货到付款
				}else{
					//$log="驿站订单：".$order_info['order_sn'].$order_info_item['name']."已收货";
					$log=$order_info_item['name']." 的服务费";
					modify_statements($order_info['total_price'], 11, $log); //增加消费额
					modify_statements($stat_balance_price, 12,$log); //增加消费额成本
				}
            

                $weixin_conf = load_auto_cache("weixin_conf");
                if($weixin_conf['platform_status']==1)
                {
                    $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = 0");
                    send_wx_msg("OPENTM202314085", $order_info['user_id'], $wx_account,array("order_item_id"=>$order_info_item['id']));
            
                }
               
                $order_item_ids[] = $order_info_item['id'];
               
            }
            
            send_recommend_user_money($order_item_ids);
            //把正在退款申请中的退款状态变更为拒绝退款
            $sql = "update ".DB_PREFIX."deal_order_item set refund_status = 3 where refund_status = 1 and id in (".implode(",", $order_item_ids).")";
            $GLOBALS['db']->query($sql);
            

            $log="驿站订单：".$order_info['order_sn']."已收货";
            //返现返积分计算
            if($return_total['return_total_score']>0||$return_total['return_total_money']>0)
            {
                $money = $return_total['return_total_money'];
                $score = $return_total['return_total_score'];
                require_once(APP_ROOT_PATH."system/model/user.php");
                modify_account(array("money"=>$money,"score"=>$score),$order_info['user_id'],$log);
            }
            
            auto_over_status($order_info['id']); //检测自动结单
            update_order_cache($order_info['id']);
            distribute_order($order_info['id']);

			if($order_info['cod_money']>0){
				//货到付款
			}else{
				require_once(APP_ROOT_PATH."system/model/dist_user.php");
				$data['money'] = $distribution_fee;
				modify_dist_account($data,$order_info['distribution_id'],$log);
			}
            
            $result['status']=1;
            $result['info']='收货成功';
        }else{
            
            $result['status']=0;
            $result['info']='收货失败';
        }   
    }
    return $result;
}


/**
 * 维权没收到货
 * @param unknown_type $delivery_sn
 * @param unknown_type $order_item_id 订单商品ID，将会确认相关的所有订单的同序号发货号。
 */
function refuse_delivery($delivery_sn,$order_item_id)
{
	$order_id = $GLOBALS['db']->getOne("select order_id from ".DB_PREFIX."deal_order_item where id = '".$order_item_id."'");
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".intval($order_id));
	if($order_info)
	{

		$delivery_notices = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_notice where order_id = ".$order_info['id']." and notice_sn = '".$delivery_sn."'");
		$order_item_ids = array(0);
		foreach($delivery_notices as $k=>$v)
		{
			$order_item_ids[] = $v['order_item_id'];
		}
		$sql = "update ".DB_PREFIX."deal_order_item set is_arrival = 2 where is_arrival = 0 and id in (".implode(",", $order_item_ids).")";
		$GLOBALS['db']->query($sql);
		if($GLOBALS['db']->affected_rows()||true)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refuse_delivery = 1 where id = ".$order_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."delivery_notice set is_arrival = 2 where notice_sn = '".$delivery_sn."' and is_arrival = 0 and order_id = ".$order_info['id']);

			$log = "订单：".$order_info['order_sn'].",运单：".$delivery_sn."未收到货";

			order_log($log, $order_id);
			
			update_order_cache($order_info['id']);
			distribute_order($order_info['id']);
			return true;
		}
	}
	return false;
}
/**
 * 无需发货商品维权没收到货
 * @param unknown_type $delivery_sn
 * @param unknown_type $order_item_id 订单商品ID，将会确认相关的所有订单的同序号发货号。
 */
function refuse_no_delivery($order_item_id)
{
    $order_id = $GLOBALS['db']->getOne("select order_id from ".DB_PREFIX."deal_order_item where id = '".$order_item_id."'");
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".intval($order_id));
    if($order_info)
    {
        $sql = "update ".DB_PREFIX."deal_order_item set is_arrival = 2 where is_arrival = 0 and id = ".$order_item_id;
        $GLOBALS['db']->query($sql);
        if($GLOBALS['db']->affected_rows()||true)
        {
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refuse_delivery = 1 where id = ".$order_id);

            $log = "订单：".$order_info['order_sn'].",".$order_id['name']."未收到货";

            order_log($log, $order_id);
            	
            update_order_cache($order_info['id']);
            distribute_order($order_info['id']);
            return true;
        }
    }
    return false;
}


/**
 * 使用优惠券
 * @param unknown_type $password 密码
 * @param unknown_type $location_id 所消费的门店ID
 * @param unknown_type $account_id 执行使用的商家账号ID
 * @param unknown_type $send_return 是否要发放奖励
 * @param unknown_type $send_notify 是否发放通知(短信/邮件)
 */
function use_youhui($password,$location_id=0,$account_id=0,$send_return=false,$send_notify=false)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."youhui_log set location_id=".$location_id.", confirm_id = ".$account_id.",confirm_time=".NOW_TIME." where youhui_sn = '".$password."' and confirm_time = 0");
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_log where youhui_sn = '".$password."'");
	if($GLOBALS['db']->affected_rows()&&$data)
	{
		if($send_return)
		{
			if($data['return_money']>0||$data['return_score']>0||$data['return_point']>0)
			{
				$money = $data['return_money'];
				$score = $data['return_score'];
				$point = $data['return_point'];
				require_once(APP_ROOT_PATH."system/model/user.php");
				$log = "验证优惠券,序列号：".$password;
				modify_account(array("money"=>$money,"score"=>$score,"point"=>$point),$data['user_id'],$log);
			}
		}
		
		$content='您的优惠券码<'.$password.'>已验证成功';
		send_msg_new($data['user_id'],$content,"confirm",array("type"=>3));
	}
	
	return $data['confirm_time']>0;
}

/**
 * 使用活动报名
 * @param unknown_type $password 密码
 * @param unknown_type $location_id 所消费的门店ID
 * @param unknown_type $account_id 执行使用的商家账号ID
 * @param unknown_type $send_return 是否要发放奖励
 * @param unknown_type $send_notify 是否发放通知(短信/邮件)
 */
function use_event($password,$location_id=0,$account_id=0,$send_return=false,$send_notify=false)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."event_submit set location_id=".$location_id.", confirm_id = ".$account_id.",confirm_time=".NOW_TIME." where sn = '".$password."' and confirm_time = 0");
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where sn = '".$password."'");
	if($GLOBALS['db']->affected_rows()&&$data)
	{
		if($send_return)
		{
			if($data['return_money']>0||$data['return_score']>0||$data['return_point']>0)
			{
				$money = $data['return_money'];
				$score = $data['return_score'];
				$point = $data['return_point'];
				require_once(APP_ROOT_PATH."system/model/user.php");
				$log = "验证活动,序列号:".$password;
				modify_account(array("money"=>$money,"score"=>$score,"point"=>$point),$data['user_id'],$log);
			}
		}
		$content='您的活动券码<'.$password.'>已验证成功';
		send_msg_new($data['user_id'],$content,"confirm",array("type"=>4));
	}
	return $data['confirm_time']>0;
}


/**
 * 自动结单检测，如通过则结单
 * 自动结单规则
 * 注：自动结单条件
 * 1. 消费券全部验证成功 
 * 2. 商品全部已收货
 * 3. 商品验证部份收货部份，其余退款
 * 结单后的商品不可再退款，不可再验证，不可再发货，可删除
 * @param unknown_type $order_id
 * return array("status"=>bool,"info"=>str)
 */
function auto_over_status($order_id)
{	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
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
		
		if($order_info['type'] <> 1)
		{
			//消费券未验证且未退款的数量为0
			$coupon_less = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_id = ".$order_id." and confirm_time = 0 and refund_status <> 2  and ( end_time=0 or ( end_time>=".NOW_TIME." or (end_time<".NOW_TIME." and expire_refund=1 and refund_status in (0,1))))");
			
			//全部未收货且未退款的数量为0
			$delivery_less = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id." and is_arrival <> 1 and refund_status <> 2 and is_shop=1 and is_coupon=0");
			
			if(($coupon_less==0&&$delivery_less==0)||$order_info['extra_status']==2)//补充，发货失败自动结单
			{
				over_order($order_id); 
			}
		}
		else
		{
			over_order($order_id); //充值单只要支付过就结单
		}	
		return array("status"=>true,"info"=>"结单成功");
	}
	else
	{
		return array("status"=>false,"info"=>"订单不存在");
	}
}

/**
 * 结单操作，结单操作将发放邀请返利
 * @param unknown_type $order_id
 */
function over_order($order_id)
{	
	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where order_status = 0 and id = ".$order_id);
	if($order_info)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 1,is_refuse_delivery = 0 where order_status = 0 and id = ".$order_id);
		if(!$GLOBALS['db']->affected_rows())
		{
			return;  //结单失败
		}
		
		order_log("订单完结", $order_id);
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set is_valid = 2 where order_id = ".$order_id);
		distribute_order($order_id);
		
		//==关于fx==//
		require_once(APP_ROOT_PATH."system/model/fx.php");
		//订单结单成功后，开始为订单商品进行分销佣金利润计算
		if(defined("FX_LEVEL")&&$order_info['pay_status']==2)
		{
			// logger::write('ctl:'.$_REQUEST[CTL].';act:'.$_REQUEST[ACT].'开始计算分销佣金', 'INFO', 3, 'fx_log');
			send_fx_order_salary($order_id);
			
			
		}
		if($order_info['is_participate_ref_salary']==1&&$order_info['pay_status']==2){//是否参与推荐商家返佣
			//用户消费成功，发放三级推荐商家入驻佣金
			send_user_supplier_salary($order_id,$type=0);
		}
		//==关于fx==//
		
		//结单后只要有未退款的才可返利
		$coupon_refunded = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_id = ".$order_id." and refund_status = 2");
		$order_item_refunded = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id." and refund_status = 2");
		if($order_item_refunded>0||$coupon_refunded>0||$order_info['is_delete']==1)
		{
			return; //不再返利
		}
		
		$goods_list = $GLOBALS['db']->getAll("select deal_id,sum(number) as num from ".DB_PREFIX."deal_order_item where order_id = ".$order_id." group by deal_id");
		//返利
		//开始处理返利，只创建返利， 发放将与msg_list的自动运行一起执行
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$order_info['user_id']);
		//开始查询所购买的列表中支不支持促销
		$is_referrals = 1; //默认为返利
		foreach($goods_list as $k=>$v)
		{
			$is_referrals = $GLOBALS['db']->getOne("select is_referral from ".DB_PREFIX."deal where id = ".$v['deal_id']);
			if($is_referrals == 0)
			{
				break;
			}
		}
		if($user_info['referral_count']<app_conf("REFERRAL_LIMIT")&&$is_referrals == 1)
		{
			//开始返利给推荐人
			$parent_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_info['pid']);
			if($parent_info)
			{
				if((app_conf("REFERRAL_IP_LIMIT")==1&&$parent_info['login_ip']!=CLIENT_IP)||app_conf("REFERRAL_IP_LIMIT")==0) //IP限制
				{
					if(app_conf("INVITE_REFERRALS_TYPE")==0) //现金返利
					{
						$referral_data['user_id'] = $parent_info['id']; //初返利的会员ID
						$referral_data['rel_user_id'] = $user_info['id'];	 //被推荐且发生购买的会员ID
						$referral_data['create_time'] = NOW_TIME;
						$referral_data['money']	=	app_conf("INVITE_REFERRALS");
						$referral_data['order_id']	=	$order_info['id'];
						$GLOBALS['db']->autoExecute(DB_PREFIX."referrals",$referral_data); //插入
					}
					else
					{
						$referral_data['user_id'] = $parent_info['id']; //初返利的会员ID
						$referral_data['rel_user_id'] = $user_info['id'];	 //被推荐且发生购买的会员ID
						$referral_data['create_time'] = NOW_TIME;
						$referral_data['score']	=	app_conf("INVITE_REFERRALS");
						$referral_data['order_id']	=	$order_info['id'];
						$GLOBALS['db']->autoExecute(DB_PREFIX."referrals",$referral_data); //插入
					}
					$GLOBALS['db']->query("update ".DB_PREFIX."user set referral_count = referral_count + 1 where id = ".$user_info['id']);
				}
					
			}
		}
		//返利over
		if($order_info['cod_money']>0){
			//货到付款
		}else{
			//订单完结，统一结算运费给商家
			$delivery_fee = $order_info['delivery_fee'];
			if($delivery_fee > 0){
				$log = $order_info['order_sn']."订单完结,结算运费";
				require_once(APP_ROOT_PATH."system/model/supplier.php");
				modify_supplier_account($delivery_fee, $order_info['supplier_id'], 2, $log);
			}
		}

	    update_order_cache($order_id);
	}
}

/**
 * 删除订单至回收站(历史订单)
 * @param unknown_type $order_id
 * 返回:true/false
 */
function del_order($order_id)
{
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id." and order_status = 1");
	if($order_info)
	{
		unset($order_info['id']);
		unset($order_info['deal_order_item']);
		$order_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		$order_info['history_deal_order_item'] = serialize($order_items);
		$order_info['history_deal_coupon'] = serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where order_id = ".$order_id));
		$order_info['history_deal_order_log'] =  serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_log where order_id = ".$order_id));
		$order_info['history_delivery_notice'] =  serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_notice where order_id = ".$order_id));
		$order_info['history_payment_notice'] = serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id));
		$order_info['history_message'] = serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."message where rel_table = 'deal_order' and rel_id = ".$order_id));
		$order_info['history_delivery_fee'] = serialize($GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_supplier_fee where order_id = ".$order_id));
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_history",$order_info,'INSERT','','SILENT');
		if($GLOBALS['db']->insert_id())
		{
			//删除会员相关分表
			$user_order_table = get_user_order_table_name($order_info['user_id']);
			$user_order_item_table = get_user_order_item_table_name($order_info['user_id']);
			$GLOBALS['db']->query("delete from ".$user_order_table." where id = ".$order_id);
			$GLOBALS['db']->query("delete from ".$user_order_item_table." where order_id = ".$order_id);
			
			//删除商户相关表
			foreach($order_items as $item)
			{
				$supplier_order_table = get_supplier_order_table_name($item['supplier_id']);
				$supplier_order_item_table = get_supplier_order_item_table_name($item['supplier_id']);
				$GLOBALS['db']->query("delete from ".$supplier_order_table." where id = ".$order_id);
				$GLOBALS['db']->query("delete from ".$supplier_order_item_table." where order_id = ".$order_id);
			}
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order where id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
			//$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_coupon where order_id = ".$order_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set order_id = -1 where order_id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order_log where order_id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."delivery_notice where order_id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."payment_notice where order_id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."message where rel_table='deal_order' and rel_id = ".$order_id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order_supplier_fee where order_id = ".$order_id);
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}


/**
 * 某个商品项的退款,只为具体的某款不发券商品退款,主要用于商户审核退款，自动计算退款额
 * @param unknown_type $order_item_id
 */
function refund_item($order_item_id)
{
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
	$order_id = $data['order_id'];
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = '".$order_id."'");
	if($order_info['order_status']==0&&$order_info['pay_status']==2&&$data)
	{
		if($data['refund_status']!=2)
		{
			if($data['is_coupon']==0)
			{					
			    
			    // 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
			    $scale = $data['total_price'] /  $order_info['deal_total_price'] ;
			    // 当前商品折扣价    = 当前商品价格比例  * 总折扣价
			    $scale_discount_price = $scale * $order_info['discount_price'];
			    // 当前商品红包价格
			    $scale_ecv_money      = $scale * $order_info['ecv_money'];
			    // 当前商品优惠劵价格
			    $scale_youhui_money   = $scale * $order_info['youhui_money'];
			    // 当前商品积分抵扣价格
			    $scale_exchange_money   = $scale * $order_info['exchange_money'];
			    	
			    	
			    // 当前商品实际支付价格
			    $price = $data['total_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;

				$balance_price = $data['balance_total_price'] + $data['add_balance_price_total'];
				
				$oi = $order_item_id;				
				
				$supplier_id = $data['supplier_id'];		
					
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 2,is_arrival = 0,refund_money=".$price." where id = ".$order_item_id);
				
				$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
				$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
				if(intval($refund_item_count)==0&&intval($coupon_item_count)==0)
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",refund_status = 2,after_sale = 1,is_refuse_delivery=0 where id = ".$order_id);
				else
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",is_refuse_delivery=0 where id = ".$order_id);
								
								
				if($price>0)
				{
					require_once(APP_ROOT_PATH."system/model/user.php");
					modify_account(array("money"=>$price), $order_info['user_id'],$data['name']."退款成功");
					modify_statements($price, 6, $data['name']."用户退款");
				}
				
				if($balance_price>0)
				{
					require_once(APP_ROOT_PATH."system/model/supplier.php");
					modify_supplier_account("-".$balance_price, $supplier_id, 1, $data['name']."用户退款"); //冻结资金减少
					modify_supplier_account($balance_price, $supplier_id, 4, $data['name']."用户退款"); //退款增加
					modify_statements($balance_price, 7, $data['name']."用户退款");
				}
				
				order_log($data['name']."退款成功 ".format_price($price), $order_id);
				auto_over_status($order_id);
				update_order_cache($order_id);
				distribute_order($order_id);
				
				$msg_content = '您的商品<'.$data['sub_name'].'>退款成功, 余额增加￥'.round($price,2);
				send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 3, 'data_id' => $oi));
				// send_msg($order_info['user_id'], $data['name']."退款成功 ".format_price($price), "orderitem", $oi);
				
				//发微信通知
				$weixin_conf = load_auto_cache("weixin_conf");
				if($weixin_conf['platform_status']==1)
				{
					$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_id);
					send_wx_msg("TM00430", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"refund_price"=>$price,"deal_name"=>$data['name'],"order_sn"=>$order_info['order_sn']));
				}
			}//不发券
		}
	}
}

/**
 * 商户订单商品退款审核处理 (输入金额计算,与原先refund_item 区分)
 * @param  id $oid 订单商品id
 * @param  float $money         退款金额
 * @param  string $memo          备注
 * @return boolean                
 */
function refund_item_new($oid, $money, $memo = '')
{
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$oid);
	$order_id = $data['order_id'];
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = '".$order_id."'");
	if($data && $data['refund_status'] == 1 && $order_info['order_status'] == 0 && $order_info['pay_status'] == 2) {
		// if ($data['is_coupon'] == 0) {

			// 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
			$scale = $data['total_price'] /  $order_info['deal_total_price'] ;
			// 当前商品折扣价    = 当前商品价格比例  * 总折扣价
			$scale_discount_price = $scale * $order_info['discount_price'];
			// 当前商品红包价格
			$scale_ecv_money      = $scale * $order_info['ecv_money'];
			// 当前商品优惠劵价格
			$scale_youhui_money   = $scale * $order_info['youhui_money'];
			// 当前商品积分抵扣价格
			$scale_exchange_money   = $scale * $order_info['exchange_money'];
			
			
			// 当前商品实际支付价格
			$real_total_price         = $data['total_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;
				
			if ($money > $real_total_price) {
				return 1;//false;
			}

			$price = $money;
			$balance_price = $data['balance_total_price'] + $data['add_balance_price_total'];			
			
			$supplier_id = $data['supplier_id'];		
				
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 2,is_arrival = 0,refund_money=".$price.", admin_memo='{$memo}' where id = ".$oid);
			
			$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
			$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
			if(intval($refund_item_count)==0&&intval($coupon_item_count)==0)
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",refund_status = 2,after_sale = 1,is_refuse_delivery=0 where id = ".$order_id);
			else
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",is_refuse_delivery=0 where id = ".$order_id);
							
							
			if ($price>0) {
				require_once(APP_ROOT_PATH."system/model/user.php");
				modify_account(array("money"=>$price), $order_info['user_id'],$data['name']."退款成功");
				modify_statements($price, 6, $data['name']."用户退款");
			}
			
			if ($balance_price>0) {
				require_once(APP_ROOT_PATH."system/model/supplier.php");
				modify_supplier_account("-".$balance_price, $supplier_id, 1, $data['name']."用户退款"); //冻结资金减少
				modify_supplier_account($balance_price, $supplier_id, 4, $data['name']."用户退款"); //退款增加
				modify_statements($balance_price, 7, $data['name']."用户退款");
			}
			
			order_log($data['name']."退款成功 ".format_price($price).$memo, $order_id);
			auto_over_status($order_id);
			update_order_cache($order_id);
			distribute_order($order_id);
			
			$msg_content = '您的商品<'.$data['sub_name'].'>退款成功, 余额增加'.round($price,2)."元";
			send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 3, 'data_id' => $oid));
			// send_msg($order_info['user_id'], $data['name']."退款成功 ".format_price($price), "orderitem", $oi);
			
			//发微信通知
			$weixin_conf = load_auto_cache("weixin_conf");
			if($weixin_conf['platform_status']==1)
			{
				$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_id);
				send_wx_msg("TM00430", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"refund_price"=>$price,"deal_name"=>$data['name'],"order_sn"=>$order_info['order_sn']));
			}
			return 99;
		/*} else {
			return 2;
		}*/
	}
	return 3;
}

/**
 * 退券
 * @param unknown_type $coupon_id
 */
function refund_coupon($coupon_id)
{
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
	if($data)
	{
		$order_id = $data['order_id'];
		$supplier_id = $data['supplier_id'];
		$oi = $data['order_deal_id'];
		$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
		$data['name'] = $order_item['name'];	
// 		if($data['deal_type']==0)//按件
// 		{
// 			$price = $order_item['unit_price'];
// 			$balance_price = $order_item['balance_unit_price'] + $order_item['add_balance_price'];
// 		}
// 		else
// 		{
// 			$price = $order_item['total_price'];
// 			$balance_price = $order_item['balance_total_price'] + $order_item['add_balance_price_total'];
// 		}

		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		// 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
		$scale = $data['coupon_price'] / $order_info['deal_total_price'];
		// 当前商品折扣价    = 当前商品价格比例  * 总折扣价
		$scale_discount_price = $scale * $order_info['discount_price'];
		// 当前商品红包价格
		$scale_ecv_money      = $scale * $order_info['ecv_money'];
		// 当前商品优惠劵价格
		$scale_youhui_money   = $scale * $order_info['youhui_money'];
		// 当前商品积分抵扣价格
		$scale_exchange_money   = $scale * $order_info['exchange_money'];
		
		 
		// 当前商品实际支付价格
		$price = $data['coupon_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;
		$balance_price = $data['balance_price'] + $data['add_balance_price'];
		
		
		if($data['refund_status']==2)
		{
			return;
		}
			
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 2 where id = ".$coupon_id);
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 2 where id = ".$data['order_deal_id']);
			
		$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
		$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
		if(intval($refund_item_count)==0&&intval($coupon_item_count)==0)
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",refund_status = 2,after_sale = 1 where id = ".$order_id);
		else
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price." where id = ".$order_id);
	

		
		if($price>0)
		{
			require_once(APP_ROOT_PATH."system/model/user.php");
			modify_account(array("money"=>$price), $order_info['user_id'],$data['name']."退款成功");
			modify_statements($price, 6, $data['name']."退款");
		}
		
		if($balance_price>0)
		{
			require_once(APP_ROOT_PATH."system/model/supplier.php");
			modify_supplier_account("-".$balance_price, $supplier_id, 1, $data['name']."退款"); //冻结资金减少
			modify_supplier_account($balance_price, $supplier_id, 4, $data['name']."退款"); //退款增加
			modify_statements($balance_price, 7, $data['name']."退款");
		}
		
		order_log($data['name']."退款成功 ".format_price($price), $order_id);
		auto_over_status($order_id);
		update_order_cache($order_id);
		distribute_order($order_id);
		
		$msg_content = '您的商品<'.$data['sub_name'].'>退款成功, 余额增加￥'.round($price,2);
		send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 3, 'data_id' => $oi));
		// send_msg($order_info['user_id'], $data['name']."退款成功 ".format_price($price), "orderitem", $oi);
		
		//发微信通知
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1)
		{
			$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_id);
			send_wx_msg("TM00430", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"refund_price"=>$price,"deal_name"=>$data['name'],"order_sn"=>$order_info['order_sn']));
		}
	}
}

/**
 * 拒绝退货
 * @param unknown_type $order_item_id
 */
function refuse_item($order_item_id, $memo = '')
{
	$oi = $order_item_id;
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
	if($data['refund_status']==2)
	{
		return;
	}
	if($data)
	{
		$order_id = $data['order_id'];
		$supplier_id = $data['supplier_id'];
	}
	
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3,is_arrival = 0, admin_memo='{$memo}' where id = ".$order_item_id);
	
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 3,is_refuse_delivery=0 where id = ".$order_id);
	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	
	
	order_log($data['name']."退款不通过 ", $order_id);
	auto_over_status($order_id);
	update_order_cache($order_id);
	distribute_order($order_id);
	
	$msg_content = '您的退款申请被驳回,商品<'.$data['name'].'>恢复<'.order_delivery_status($data['delivery_status']).'>';
	send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 5, 'data_id' => $oi));
	// send_msg($order_info['user_id'], $data['name']."退款不通过 ", "orderitem", $oi);
}


function refuse_coupon($coupon_id)
{
	$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
	if($data['refund_status']==2)
	{
		return;
	}
	if($data)
	{
		$oi = $data['order_deal_id'];
		$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
		$data['name'] = $order_item['name'];
		$order_id = $data['order_id'];
		$supplier_id = $data['supplier_id'];
	}
	
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3 where id = ".$coupon_id);
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3 where id = ".$data['order_deal_id']);
	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set  refund_status = 3  where id = ".$order_id);
	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	
	
	order_log($data['name']."退款不通过 ", $order_id);
	auto_over_status($order_id);
	update_order_cache($order_id);
	distribute_order($order_id);
	
	$msg_content = '您的退款申请被驳回,商品<'.$data['name'].'>恢复<'.order_delivery_status($data['delivery_status']).'>';
	send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 5, 'data_id' => $oi));
	// send_msg($order_info['user_id'], $data['name']."退款不通过 ", "orderitem", $oi);
}

/**
 * 订单商品的发货状态判断
 * @param  int $status 
 * @return string         
 */
function order_delivery_status($key)
{
	$status_collection = array(
		0 => '未发货',
		1 => '已发货',
		5 => '无需发货'
	);
	if (array_key_exists((int)$key, $status_collection)) {
		return $status_collection[$key];
	}
	return '';
}


/**
 * 订单分片,用户散列订单表，商户散列订单商品表
 * @param unknown_type $order_id
 */
function distribute_order($order_id)
{
	if($GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']>1)
	{
		//定义建表sql
		$rs = $GLOBALS['db']->getRow("show create table ".DB_PREFIX."deal_order");
		$order_sql = $rs['Create Table'];
		$order_sql = preg_replace("/create table/i", "create table if not exists ", $order_sql);
		$rs_item = $GLOBALS['db']->getRow("show create table ".DB_PREFIX."deal_order_item");
		$order_item_sql = $rs_item['Create Table'];
		$order_item_sql = preg_replace("/create table/i", "create table if not exists ", $order_item_sql);
		
		//散列订单
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		$order_end = hash_table($order_info['user_id'], $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单用户ID的散列后缀
		$order_table_name = DB_PREFIX."deal_order_u_".$order_end; //散列订单表	
		$sql = preg_replace("/".DB_PREFIX."deal_order/", $order_table_name, $order_sql);	
		$GLOBALS['db']->query($sql); //创建散列表
		$GLOBALS['db']->query("delete from ".$order_table_name." where id = ".$order_id);
		$GLOBALS['db']->autoExecute($order_table_name,$order_info);
		
		//开始散列订单商品
		$order_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		foreach($order_items as $k=>$item)
		{
			$order_end = hash_table($item['supplier_id'], $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单用户ID的散列后缀
			$order_table_name = DB_PREFIX."deal_order_s_".$order_end; //散列订单表
			$sql = preg_replace("/".DB_PREFIX."deal_order/", $order_table_name, $order_sql);
			$GLOBALS['db']->query($sql); //创建散列表
			$GLOBALS['db']->query("delete from ".$order_table_name." where id = ".$order_id);
			$GLOBALS['db']->autoExecute($order_table_name,$order_info);			
			
			$order_item_end = hash_table($item['supplier_id'], $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单商品商家ID的散列后缀
			$order_item_table_name = DB_PREFIX."deal_order_item_s_".$order_item_end; //散列订单商品表
			$sql = preg_replace("/".DB_PREFIX."deal_order_item/", $order_item_table_name, $order_item_sql);	
			$GLOBALS['db']->query($sql); //创建散列表
			$GLOBALS['db']->query("delete from ".$order_item_table_name." where id = ".$item['id']);
			$GLOBALS['db']->autoExecute($order_item_table_name,$item);
			
			$order_item_end = hash_table($order_info['user_id'], $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单商品用户ID的散列后缀
			$order_item_table_name = DB_PREFIX."deal_order_item_u_".$order_item_end; //散列订单商品表
			$sql = preg_replace("/".DB_PREFIX."deal_order_item/", $order_item_table_name, $order_item_sql);
			$GLOBALS['db']->query($sql); //创建散列表
			$GLOBALS['db']->query("delete from ".$order_item_table_name." where id = ".$item['id']);
			$GLOBALS['db']->autoExecute($order_item_table_name,$item);
		}
	}	
}

/**
 * 为会员获取指定的散列订单表名
 * @param unknown_type $user_id
 */
function get_user_order_table_name($user_id)
{
	if($GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']>1)
	{
		$order_end = hash_table($user_id, $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单用户ID的散列后缀
		$order_table_name = DB_PREFIX."deal_order_u_".$order_end; //散列订单表
		return $order_table_name;
	}
	else
	{
		return DB_PREFIX."deal_order";
	}
}

/**
 * 为会员获取指定的散列订单表名
 * @param unknown_type $user_id
 */
function get_supplier_order_table_name($supplier_id)
{
	if($GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']>1)
	{
		$order_end = hash_table($supplier_id, $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); //通过订单用户ID的散列后缀
		$order_table_name = DB_PREFIX."deal_order_s_".$order_end; //散列订单表
		return $order_table_name;
	}
	else
	{
		return DB_PREFIX."deal_order";
	}
}

/**
 * 为商户获取指定的散列订单商品表名
 * @param unknown_type $supplier_id
 */
function get_supplier_order_item_table_name($supplier_id)
{
	if($GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']>1)
	{
		$order_end = hash_table($supplier_id, $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']); 
		$order_table_name = DB_PREFIX."deal_order_item_s_".$order_end; //散列订单表
		return $order_table_name;
	}
	else
	{
		return DB_PREFIX."deal_order_item";
	}
}

/**
 * 为用户获取指定的散列订单商品表名
 * @param unknown_type $user_id
 */
function get_user_order_item_table_name($user_id)
{
	if($GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']>1)
	{
		$order_end = hash_table($user_id, $GLOBALS['distribution_cfg']['ORDER_DISTRIBUTE_COUNT']);
		$order_table_name = DB_PREFIX."deal_order_item_u_".$order_end; //散列订单表
		return $order_table_name;
	}
	else
	{
		return DB_PREFIX."deal_order_item";
	}
}

/**
 * 获取指定用户未支付的商品订单数量
 * @param  int $user_id 用户ID
 * @return int          未支付的数量
 */
function countNotPayOrder($user_id)
{
	$table = get_user_order_table_name($user_id);
	$sql = 'SELECT COUNT(id) FROM '.$table.' WHERE user_id='.$user_id.' AND pay_status <> 2 AND type = 0 AND is_delete = 0';
	$counts = $GLOBALS['db']->getOne($sql);
	return $counts;
}

function send_recommend_user_money($order_item_ids,$deal_coupon_id,$nocoupon=false){
    
    if($order_item_ids){
        foreach($order_item_ids as $k=>$order_item_id){
            if($order_item_id > 0){
                $order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where is_balance_recommend_money=0 and id=".$order_item_id);
                $deal_data = unserialize($order_item['deal_data']);
                if($order_item && $deal_data['recommend_user_return_ratio']>0){                   
                    $user_info = $GLOBALS['db']->getRow("select id ,is_fx from ".DB_PREFIX."user where is_fx=1 and id=".$deal_data['recommend_user_id']);                    
                    $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance_recommend_money=1 where id=".$order_item_id);
                    $rs = $GLOBALS['db']->affected_rows();
                    if($user_info['is_fx']==1  && $rs){
                        // require_once(APP_ROOT_PATH."system/model/fx.php");
                        $recommend_money = ($order_item['total_price'] - $order_item['balance_total_price'] - $order_item['add_balance_price_total'])*$deal_data['recommend_user_return_ratio']/100;
                        $log = '推荐商品：'. $order_item['name'] .'卖出'.$order_item['number']."件，获得推荐佣金";
                        // modify_fx_account($recommend_money, $deal_data['recommend_user_id'],$log);
                        send_recommend_user_money2($order_item['total_price'],$recommend_money, $deal_data['recommend_user_id'],$log);
                    }
                }
            }
        }
    }
    
    if($deal_coupon_id > 0 && !$nocoupon){
        $coupon_data = $GLOBALS['db']->getRow("select dc.*,doi.deal_data ,doi.name from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."deal_order_item as doi on dc.order_deal_id=doi.id where is_balance_recommend_money=0 and dc.id=".$deal_coupon_id);
        $deal_data = unserialize($coupon_data['deal_data']);
        $user_info = $GLOBALS['db']->getRow("select id ,is_fx from ".DB_PREFIX."user where is_fx=1 and id=".$deal_data['recommend_user_id']);
        $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set is_balance_recommend_money=1 where id=".$deal_coupon_id);
        $rs = $GLOBALS['db']->affected_rows();
        if($user_info['is_fx']==1 && $deal_data['recommend_user_return_ratio']>0 && $rs){
            // require_once(APP_ROOT_PATH."system/model/fx.php");
            $recommend_money = ($coupon_data['coupon_price'] - $coupon_data['balance_price'] - $coupon_data['add_balance_price'])*$deal_data['recommend_user_return_ratio']/100;
            $log = '推荐团购：'. $coupon_data['name'] .'卖出，团购劵'.$coupon_data['password']."验证消费成功，获得推荐佣金";
            // modify_fx_account($recommend_money, $deal_data['recommend_user_id'],$log);
            send_recommend_user_money2($coupon_data['coupon_price'], $recommend_money, $deal_data['recommend_user_id'], $log);
        }
    } elseif ($deal_coupon_id > 0 && $nocoupon) {
        $order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where is_balance_recommend_money=0 and id=".$deal_coupon_id);
        if($order_item){
            $deal_data = unserialize($order_item['deal_data']);
            $user_info = $GLOBALS['db']->getRow("select id ,is_fx from ".DB_PREFIX."user where is_fx=1 and id=".$deal_data['recommend_user_id']);
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance_recommend_money=1 where id=".$deal_coupon_id);
            $rs = $GLOBALS['db']->affected_rows();
            if($user_info['is_fx']==1 && $deal_data['recommend_user_return_ratio']>0 && $rs){
                // require_once(APP_ROOT_PATH."system/model/fx.php");
                $recommend_money = ($order_item['total_price'] - $order_item['balance_total_price'] - $order_item['add_balance_price_total'])*$deal_data['recommend_user_return_ratio']/100;
                $log = '推荐团购：'. $order_item['name'] .'卖出团购单'.$order_item['order_sn'].'共'.$order_item['number']."件，获得推荐佣金";
                // modify_fx_account($recommend_money, $deal_data['recommend_user_id'],$log);
                send_recommend_user_money2($order_item['total_price'], $recommend_money, $deal_data['recommend_user_id'], $log);
            }
        }
    }
    
}
/**
 * 更新会员分销等级、发放推荐佣金
 * @param  float $sale_money 销售金额
 * @param  float $money      推荐佣金
 * @param  int $rec_uid    推荐会员id
 * @param  string $log        日志内容
 * @return null             
 */
function send_recommend_user_money2($sale_money, $money, $rec_uid, $log)
{
    require_once(APP_ROOT_PATH."system/model/fx.php");
    $GLOBALS['db']->query("update ".DB_PREFIX."user set fx_total_money = fx_total_money+".$sale_money.",fx_total_balance=fx_total_balance+".$money." where id = ".$rec_uid." and is_fx = 1");
    modify_fx_level($rec_uid);
    modify_fx_account($money, $rec_uid, $log);
}

/**
 * 获取订单中单个商品的状态
 * @param unknown $order_item 订单中单个商品，一维数据
 */
function get_order_item_status($order_item,$pick_location_id){
    $order_status='';
    if($order_item){
        if($order_item['refund_status']==1){
            $order_status='退款维权';
        }elseif($order_item['refund_status']==2){
            $order_status='已退款';
        }elseif($order_item['delivery_status']==0 && $pick_location_id == 0){
            $order_status='待发货';
        }elseif($order_item['delivery_status']==5 && $pick_location_id > 0){
            $order_status='待自提';
        }elseif($order_item['delivery_status']==1 && $order_item['is_arrival']==0){
            $order_status='待收货';
        }elseif($order_item['is_arrival']==1 && $order_item['dp_id']==0){
            $order_status='待评价';
        }elseif($order_item['dp_id']>0){
            $order_status='已完成';
        }
    }
    return $order_status;

}

/**
 * @desc 进行强制收货或者确认一张消费券
 * @author    吴庆祥
 * @param $order_item_id
 * @param $coupon_id
 * @return array
 */
function do_verify($order_item_id,$coupon_id)
{
    if($order_item_id)
    {
        $oi = $order_item_id;
        $data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id." and is_arrival <> 1 and delivery_status=1");
        $delivery_inifo = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_notice where order_item_id = {$order_item_id}");

        if($data){
            $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$data['order_id']);
            $delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".DB_PREFIX."deal_order as o on n.order_id = o.id where n.order_item_id = ".$order_item_id." and o.id = ".$data['order_id']." and is_arrival <> 1 order by delivery_time desc");
            if($delivery_notice && $data['delivery_type'] != 2)
            {
                // 已退款的就不会再收货了
                $res = order_confirm_delivery($delivery_inifo['notice_sn'], $delivery_inifo['express_id'], $data['order_id']);
            }else{
                $res= confirm_no_delivery($order_item_id);
            }
            if($res)
            {
                // 如果存在退款中的商品。变为已拒绝
                $sql = 'select order_item_id from '.DB_PREFIX.'delivery_notice where notice_sn="'.$delivery_notice['notice_sn'].'"';
                $item_ids = $GLOBALS['db']->getCol($sql);
                $rsql = 'select id from '.DB_PREFIX.'deal_order_item where id in ('.implode(',', $item_ids).') and refund_status=1';
                $ritem_ids = $GLOBALS['db']->getCol($rsql);
                if (!empty($ritem_ids)) {
                    $GLOBALS['db']->query('update '.DB_PREFIX.'deal_order_item set refund_status=3 where id in('.implode(',', $ritem_ids).')');
                    update_order_cache($data['order_id']);
                }
                $msg_content = '您购买的<'.$data['name'].'>已签收成功';
                send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 2, 'data_id' => $oi));
                $data['status'] = true;
                $data['info'] = "操作收货成功";
                return $data;
            }
            else
            {
                $data['status'] = 0;
                $data['info'] = "操作收货失败";
                return $data;
            }
        }
        else
        {
            $data['status'] = 0;
            $data['info'] = "订单已收货";
            return $data;
        }
    }
    elseif($coupon_id)
    {
        $data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
        if($data['refund_status']==2)
        {
            return array("status"=>0,"info"=>"已退款");
        }
        if($data)
        {
            $rs = use_coupon($data['password'],0,0,true,true);
            if($rs)
            {
                return array("status"=>1,"info"=>"验证成功");
            }
            else
            {
                return array("status"=>0,"info"=>"验证失败");
            }
        }
        else
        {
            return array("status"=>0,"info"=>"非法操作");
        }

    }
}

/**
 * @desc  发货
 * @author    吴庆祥
 * @param $param
 * @return array
 */
function do_delivery($param)
{
    $silent = intval($param['silent']);
    $order_id = intval($param['order_id']);
    $order_deals = $param['order_deals'];
    $delivery_sn = $param['delivery_sn'];
    $express_id = intval($param['express_id']);
    $memo = strim($param['memo']);
    $order_info = $GLOBALS['db']->getOne("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
    $delivery_status=$order_info['delivery_status'];
    if(!$order_deals)
    {
        if($silent==0)
        return array("status"=>0,"info"=>"请选择要发货的商品");
    }
    elseif($express_id==0 && $delivery_status != 5){
        return array("status"=>0,"info"=>"请选择快递");
    }
    elseif($delivery_sn=="" && $delivery_status != 5){
        return array("status"=>0,"info"=>"请填写正确的快递单号");
    }
    else
    {
    	//查询快递名
    	$express_info = $GLOBALS['db']->getRow("select name,class_name from ".DB_PREFIX."express where id=".$express_id);
    	$express_name = $express_info['name'];
    	if($delivery_sn){
    		$ordertrack = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."track where express_code='".$express_info['class_name']."' and express_number='".$delivery_sn."' and order_type=3");
    		if($ordertrack){
    			return array("status"=>0,"info"=>"快递单号已存在，请重新填写！");
    		}
    	}
    	$deal_names = array();
        foreach($order_deals as $order_deal_id)
        {
            $deal_info =$GLOBALS['db']->getRow("select d.*,doi.id as doiid from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_order_item as doi on doi.deal_id = d.id where doi.id = ".$order_deal_id);
            $deal_name = $deal_info['sub_name'];
            array_push($deal_names,$deal_name);
            $rs = make_delivery_notice($order_id,$order_deal_id,$delivery_sn,$memo,$express_id);
            if($rs)
            {
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,delivery_time=".NOW_TIME.",delivery_memo='".$memo."',is_arrival = 0 where id = ".$order_deal_id);
            }
        }
        $deal_names = implode(", ",$deal_names);
        send_delivery_mail($delivery_sn,$deal_names,$order_id);
        send_delivery_sms($delivery_sn,$deal_names,$order_id);

        //开始同步订单的发货状态
        // 查询所有需要发货的商品
        $order_deal_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id=".$order_id." and delivery_status !=5 and refund_status !=2");

        $delivery_deal_items = $order_deal_items;

        // 获得已发货的商品
        foreach($delivery_deal_items as $k=>$v)
        {
            if($v['delivery_status']==0) //未发货去除
            {
                unset($delivery_deal_items[$k]);
            }
        }

        $option=",update_time=".NOW_TIME.",is_refuse_delivery=0 ";
        if(count($delivery_deal_items)==0&&count($order_deal_items)!=0)
        {
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0 {$option} where id = ".$order_id); //未发货
        }
        elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items))
        {
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1 {$option} where id = ".$order_id); //部分发
        }
        else
        {
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2 {$option} where id = ".$order_id); //全部发
        }

        $refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
        $coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
        if($refund_item_count==0&&$coupon_item_count==0)
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 0,is_refuse_delivery=0 where id = ".$order_id);
        $msg = "发货成功";
        //发货完毕，开始同步相应支付接口中的发货状态
        if(intval($_REQUEST['send_goods_to_payment'])==1)
        {
            $payment_notices = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id);
            foreach($payment_notices as $k=>$v)
            {
                $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$v['payment_id']);
                if($v['outer_notice_sn']!='')
                {
                    require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
                    $payment_class = $payment_info['class_name']."_payment";
                    $payment_object = new $payment_class();
                    if(method_exists ($payment_object,"do_send_goods"))
                    {
                        $result = $payment_object->do_send_goods($v['id'],$delivery_sn);
                        $msg = $msg."[".$payment_info['name'].$result."]";
                    }
                    else
                    {
                        $msg = $msg."[".$payment_info['name']."不支持同步发货]";
                    }
                }
                else
                {
                    $msg = $msg."[".$payment_info['name']."未记录对应的支付单号]";
                }
            }
        }
        
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        order_log("发货成功".$express_name.$delivery_sn.$_REQUEST['memo'],$order_id);
        update_order_cache($order_id);
        distribute_order($order_id);

        if($delivery_sn){

            //向快递网发送快递查询订阅
            require_once(APP_ROOT_PATH.'system/model/express.php');
            $express = new express();
            $result = $express->get($expressCode=$express_info['class_name'],$delivery_sn,0,$order_info['region_lv3'],$order_id,$order_info['user_id'],0,1,$memo,3);
        }


        $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id=".$order_id);

        $msg_content = '您购买的<'.$deal_names.'>已发货,物流单号:'.$delivery_sn;
        send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));

        //发微信通知
        //通知商户
        $supplier_list = $GLOBALS['db']->getAll("select distinct(supplier_id) from ".DB_PREFIX."deal_order_item where id in (".implode(",", $order_deals).")");
        foreach($supplier_list as $row)
        {
            $weixin_conf = load_auto_cache("weixin_conf");
            if($weixin_conf['platform_status']==1)
            {
                $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$row['supplier_id']);
                $order_item_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_order_item where supplier_id = ".$row['supplier_id']." and delivery_status = 1");
                send_wx_msg("OPENTM200565259", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"order_sn"=>$order_info['order_sn'],"company_name"=>$express_name,"delivery_sn"=>$delivery_sn,"order_item_id"=>$order_item_id));
            }
        }

        if($silent==0)
            return array("status"=>1,"info"=>$msg);
    }
	
}

/**
 * 获得订单状态
 * $order_info：订单信息
 * 输出
 * order_status:0,待付款1,待发货2,待确认3,待评价4,已取消5,已完成6,退款中7,已删除8
 * Array
		(
			[status] => 1             订单状态:待付款1,待发货2,待确认3,待评价4,已取消5,已完成6,退款中7,已删除8
			[is_dp]=>0                待评价商品，0：不存在，1：存在 
			[is_do_delivery]=>0       待发货商品，0：不存在，1：存在   
			[is_do_verify]=>0		  待收货商品，0：不存在，1：存在 
			[is_use_coupon]=>0		  待验证的消费券，0：不存在，1：存在 
			[is_return_item]=>0		  退款中的商品，0：不存在，1：存在
		)
 */
function get_order_status_is($order_info,$deal_order_item)
{
	
	$is_dp=0;//待评价商品，0：不存在，1：存在             	订单有效
	$is_do_delivery=0;//待发货商品，0：不存在，1：存在    	订单有效
	$is_do_verify=0;//待收货商品，0：不存在，1：存在      	订单有效
	$is_use_coupon=0;//待验证的消费券，0：不存在，1：存在 	订单有效
	$is_return_item=0;//退款中的商品，0：不存在，1：存在 	订单有效
	if($order_info['pay_status']==2){
		foreach ($deal_order_item as $k=>$v){ //遍历订单商品
			if($v['is_shop']==1){//商城商品 无需配送or物流配送or驿站配送or自提商品
				if($v['is_coupon']==1){//自提商品
					$coupon=$GLOBALS['db']->getRow("select id,deal_type,refund_status,confirm_time from " . DB_PREFIX . "deal_coupon where order_deal_id=".$v['id']);
					if($coupon['confirm_time']>0&&$v['dp_id']==0){//已验证，未评价
						$is_dp=1;
					}
					if($coupon['confirm_time']==0&&$coupon['refund_status']!=1&&$coupon['refund_status']!=2){
						//未验证and不是退款中and不是已退款
						$is_use_coupon=1;
					}
					if($coupon['confirm_time']==0&&$coupon['refund_status']==1){//未验证and退款中
						$is_return_item=1;//退款中
					}
				}else{ //无需配送or物流配送or驿站配送
					if($v['is_balance']>0){//已结算
						if($v['dp_id']==0){//未评价
							$is_dp=1;
						}
					}else{//未结算
						if($v['delivery_status']==0&&$v['refund_status']!=1&&$v['refund_status']!=2){//未发货and不是退款中and不是已退款
							$is_do_delivery=1;//存在待发货的商品
						}
						if($v['delivery_status']==1&&$v['is_arrival']==0&&$v['refund_status']!=1&&$v['refund_status']!=2){
							//已发货and未收货and不是退款中and不是已退款
							$is_do_verify=1;
						}
						if($v['refund_status']==1){//退款中
							$is_return_item=1;//退款中
						}
					}
				}
			}else{
				$is_confirm=0;//已使用的团购券，0：不存在，1：存在，2，没有券   当前商品有效
				$is_tuan_end=0;//团购流程是否结束，0：未结束，1：结束           当前商品有效
				$is_available_coupon=0;//存在可使用的券，0：不存在，1：存在     当前商品有效
				if($v['is_coupon']==0){//不发券的团购
					$is_confirm=2;//没有券
					$is_tuan_end=1;//结束
				}else{//发券的团购
					$coupon_arr = $GLOBALS['db']->getAll("select id,end_time,is_valid,confirm_time,is_balance,refund_status from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $v['id']);
					//print_r($coupon_arr);
					if(count($coupon_arr)==1){//一张券的情况
						if($coupon_arr['0']['confirm_time']>0){//已使用
							$is_confirm=1;//存在已使用的团购券
							$is_tuan_end=1;//结束
						}
						if($coupon_arr['0']['refund_status']==2){//已退款
							//$is_confirm=0;//存在已使用的团购券
							$is_tuan_end=1;//结束
						}
						if($coupon_arr['0']['confirm_time']==0&&$coupon_arr['0']['refund_status']==1){//券退款中
							//$is_tuan_end=0;//未结束
							$is_return_item=1;//退款中
							
						}
						if($coupon_arr['0']['confirm_time']==0&&$coupon_arr['0']['refund_status']!=1&&$coupon_arr['0']['refund_status']!=2&&(($coupon_arr['0']['end_time']==0||$coupon_arr['0']['end_time']>=NOW_TIME)||($coupon_arr['0']['end_time']!=0&&NOW_TIME>$coupon_arr['0']['end_time']&&$coupon_arr['0']['is_valid']!=2))){//未使用and(未过期or(已过期and退款未禁用))=可验证的券
							$is_available_coupon=1;//存在可使用的券
							//$is_tuan_end=0;//未结束
						}
					}elseif(count($coupon_arr)>1){//多张券的情况
						$is_tuan_end=1;
						foreach ($coupon_arr as $kk=>$vv){//遍历该团购的团购券 券状态：待使用，退款中，待评价，已完成，已退款
							if($vv['confirm_time']>0){//已使用
								$is_confirm=1;//存在已使用的团购券
							}
							if($vv['confirm_time']==0&&$vv['refund_status']!=1&&$vv['refund_status']!=2&&(($vv['end_time']==0||$vv['end_time']>=NOW_TIME)||($vv['end_time']!=0&&NOW_TIME>$vv['end_time']&&$vv['is_valid']!=2))){//未使用and(未过期or(已过期and退款未禁用))=可验证的券
								$is_available_coupon=1;//存在可使用的券
								$is_tuan_end=0;//团购流程未结束
							}
							if($vv['refund_status']==1){//券退款中
								$is_tuan_end=0;//团购流程未结束
								$is_return_item=1;//退款中
							}
						}
					}else{ //券不存在
						//从未发生
					}
					
					if($is_available_coupon==1){
						$is_use_coupon=1;
					}
				}
				if($is_tuan_end==1&&($is_confirm==1||$is_confirm==2)&&$v['dp_id']==0){//团购流程结束and (不发券的or至少使用了一张)and未评估
					$is_dp=1;
				}
			}
		}
	}
	$status=0;
	if($order_info['pay_status']!=2){ //下单未支付
		$status=1;//待付款
		if($order_info['is_delete'] == 1&&$order_info['is_cancel']=1){ //会员执行取消订单
			$status=5;//已取消
		}elseif($order_info['is_delete'] == 1&&$order_info['is_cancel']=0){//会员取消完，执行删除订单，或者后台关闭交易
			$status=8;  //已删除
		}
	}else{//下单付款完成
		if($order_info['order_status']==1){  //订单所有商品流程结束(退款or已使用or过期无法退款)，订单结单
			if($is_dp==1){
				$status=4;//待评价
			}else{
				$status=6;//已完成
			}
		}else{
			if($is_do_delivery==1){//存在待发货的商品
				$status=2;//待发货
			}elseif($is_do_delivery==0&&($is_do_verify==1||$is_use_coupon==1)){//不存在待发货and存在待收货的商品and存在待验证的券
				$status=3;//待确认
			}elseif($is_do_delivery==0&&$is_do_verify==0&&$is_use_coupon==0&&$is_dp==1){//不存在待发货and存在待收货的商品and存在待验证的券
				$status=4;//待评价
			}
			if($is_do_delivery==0&&$is_do_verify==0&&$is_use_coupon==0&&$is_dp==0&&$is_return_item==1){
				$status=7;//退款中
			}
			
		}
		
	}
	$data=array();
	$data['status']=$status;//订单状态
	$data['is_dp']=$is_dp;//是否存在待评价商品
	$data['is_do_delivery']=$is_do_delivery;//是否存在待发货商品
	$data['is_do_verify']=$is_do_verify;//是否存在待收货商品
	$data['is_use_coupon']=$is_use_coupon;//是否存在待验证券
	$data['is_return_item']=$is_return_item;//是否存在退款中的商品
	return $data;
}
/**
 * 订单金额组合
 输入Array
		(
			[discount_price] =>
			[ecv_money]=>
			[deal_total_price]=> 
			[record_delivery_fee]=>
			[payment_fee]=>
			[total_price]=>
			[return_total_score]=>
			[promote_arr]=>       促销信息，暂时不需要，
		)
		$show_type=0/1 0:为会员端显示，1为商户端代理商后台显示
 *输出
 */
function order_fee_arr($order_info,$show_type=0)
{
	// echo "<pre>";print_r($order_info);exit;
	$youhui_price = 0;//$order_info['ecv_money']; //优惠价=会员折扣+代金券
	/*foreach ($order_info['promote_arr'] as $k => $v) {
		$v['config'] = unserialize($v['config']);
		$order_info['promote_arr'][$k]['config'] = $v['config'];
		if ($v['class_name'] == 'Discountamount' || $v['class_name'] == 'Appdiscount') {
			$order_info['deal_total_price'] += $v['config']['discount_amount'];
			$youhui_price += $v['config']['discount_amount']; //优惠价+=满减
		}
		if ($v['class_name'] == 'Freebynumber' || $v['class_name'] == 'Freebyprice' || $v['class_name'] == 'Freedelivery') {
			$is_delivery = 1;
			unset($order_info['promote_arr'][$k]);
		}
	}*/

	//$order_info['is_delivery'] = $is_delivery;
	//echo $order_info['total_price'];exit();
	$order_total_price = $order_info['total_price']; //原价=商品总价+运费+手续费
	//$goods_total_price = $order_total_price - $order_info['record_delivery_fee'] + $order_info['payment_fee'];
	/*if ($is_delivery == 1)
		$youhui_price += $order_info['record_delivery_fee'];//优惠价+=+满免*/

	$order_pay_price = $order_info['total_price'] - $order_info['ecv_money']-$order_info['youhui_money'];//$order_total_price - $youhui_price;
	
	$feeinfo=array();
	if ($order_info['total_price'] > 0) {
		$feeinfo[] = array(
			"hiz_name" => "商品总价",
			"pc_name" => "商品总价",
			"name" => "商品金额",
			"symbol" => 1,
			"value" => format_price_txt(round($order_info['deal_total_price'], 2)),
			"buy_type" => 0
		);
	}
	if ($order_info['return_total_score'] < 0) {
		$feeinfo[] = array(
			"hiz_name" => "商品积分",
			"pc_name" => "消耗积分",
			"name" => "商品积分",
			"symbol" => 1,
			"value" => abs($order_info['return_total_score']),
			"buy_type" => 1
		);
	}
	if ($order_info['record_delivery_fee'] > 0) {
		$feeinfo[] = array(
			"hiz_name" => "运费金额",
			"pc_name" => "运费",
			"name" => "运费",
			"symbol" => 1,
			"value" => format_price_txt(round($order_info['record_delivery_fee'], 2)),
			"buy_type" => 0
		);
	}
	if ($order_info['payment_fee'] > 0) {
		$feeinfo[] = array(
			"hiz_name" => "手续费",
			"pc_name" => "手续费",
			"name" => "手续费",
			"symbol" => 1,
			"value" => format_price_txt(round($order_info['payment_fee'], 2)),
			"buy_type" => 0
		);
	}
	//促销关闭隐藏
	/*if ($order_info['record_delivery_fee'] > 0 && $order_info['delivery_fee'] == 0) {
		$paid[] = array(
			"name" => "满免优惠",
			"symbol" => -1,
			"value" => format_price_txt(round($order_info['record_delivery_fee'], 2)),
			"buy_type" => 0
		);
	}

	if (isset($order_info['promote_arr'])) {
		foreach ($order_info['promote_arr'] as $kkey => $vvalue) {
			$discount_amount += $vvalue['config']['discount_amount'];
		}
		$paid[] = array(
			"name" => "满减优惠",
			"symbol" => -1,
			"value" => format_price_txt(round($discount_amount, 2)),
			"buy_type" => 0
		);
	}*/
	if ($order_info['ecv_money'] > 0) {
		$paid[] = array(
			"hiz_name" => "红包支付",
			"pc_name" => "红包",
			"name" => "红包支付",
			"symbol" => -1,
			"value" => format_price_txt(round($order_info['ecv_money'], 2)),
			"buy_type" => 0
		);
		$youhui_price+=$order_info['ecv_money'];
	}
	if ($order_info['exchange_money'] > 0) {
		$paid[] = array(
			"hiz_name" => "积分抵扣",
			"pc_name" => "积分抵扣",
			"name" => "积分抵扣",
			"symbol" => -1,
			"value" => format_price_txt(round($order_info['exchange_money'], 2)),
			"buy_type" => 0
		);
		$youhui_price+=$order_info['exchange_money'];
	}
	if ($order_info['youhui_money'] > 0) {
	    $paid[] = array(
	        "hiz_name" => "店铺优惠",
	        "pc_name" => "店铺优惠",
	        "name" => "店铺优惠",
	        "symbol" => -1,
	        "value" => format_price_txt(round($order_info['youhui_money'], 2)),
	        "buy_type" => 0
	    );
	    $youhui_price+=$order_info['youhui_money'];
	}
	
	if($show_type==1){
    	if ($order_info['discount_price'] > 0) {
    		$paid[] = array(
    			"hiz_name" => "会员折扣",
    			"pc_name" => "会员等级折扣",
    			"name" => "会员折扣",
    			"symbol" => -1,
    			"value" => format_price_txt(round($order_info['discount_price'], 2)),
    			"buy_type" => 0
    		);
    	}
    	
    	$paid[] = array(
    	    "hiz_name" => "应支付金额",
    	    "pc_name" => "应支付金额",
    	    "name" => "应支付金额",
    	    "symbol" => 1,
    	    "value" => format_price_txt(round($order_info['total_price']-$order_info['ecv_money']-$order_info['youhui_money'], 2)),
    	    "buy_type" => 0
    	);
	}
	
	return array("youhui_price"=>round($youhui_price, 2),"order_total_price"=>round($order_total_price, 2),"order_pay_price"=>round($order_pay_price,2),"feeinfo"=>$feeinfo,"paid"=>$paid);
}



/**
 * 不发券的团购订单自动结算方法
 * @param  int $order_deal_id 团购订单商品表id
 * @return null                
 */
function auto_balance_coupon($order_deal_id)
{      
    $oiSql = 'SELECT * FROM '.DB_PREFIX.'deal_order_item WHERE id = '.$order_deal_id;
    $oiInfo = $GLOBALS['db']->getRow($oiSql);
    if (empty($oiInfo)) {
        return;
    }
    $upOiSql = 'UPDATE '.DB_PREFIX.'deal_order_item SET is_balance = 1, consume_count='.$oiInfo['number'].' WHERE id='.$oiInfo['id'];
    $GLOBALS['db']->query($upOiSql);
    
    update_order_cache($oiInfo['order_id']);
    distribute_order($oiInfo['order_id']);
    
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$oiInfo['order_id']);
    if($order_info) {
        $order_msg = "订单号".$order_info['order_sn']." ";
    }

    update_order_cache($order_info['id']);
    
    
    $balance_price = $oiInfo['balance_total_price'] + $oiInfo['add_balance_price_total'];
    require_once(APP_ROOT_PATH."system/model/supplier.php");
    modify_supplier_account("-".$balance_price, $oiInfo['supplier_id'], 1, $order_msg."未发券团购验证成功");  //解冻资金
    modify_supplier_account($balance_price, $oiInfo['supplier_id'], 2, $order_msg."未发券团购验证成功");  //等结算金额增加
    
    modify_supplier_account($oiInfo['total_price'], $oiInfo['supplier_id'], 6, $order_msg."未发券团购验证成功");  //团购商城销售额增加(不是结算价)
        //代理商佣金增加
    $agency_id=intval($GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id=".$oiInfo['supplier_id']));
   
    $money_admin=$oiInfo['total_price']-$balance_price;  //该卷总利润
    if($money_admin > 0){
        modify_agency_account($money_admin,$agency_id,1,$order_msg."未发券团购验证成功");
    }
    
    modify_statements($oiInfo['coupon_price'], 11, $order_msg."未发券团购验证成功"); //增加消费额
    modify_statements($balance_price, 12, $order_msg."未发券团购验证成功"); //增加消费额成本
    
    // $msg_content = '您的团购'.$order_msg.'已验证成功';
    // send_msg_new($oiInfo['user_id'], $msg_content, 'confirm', array('type' => 2, 'data_id' => $oiInfo['id']));

    send_recommend_user_money(0,$oiInfo['id'],true);

}