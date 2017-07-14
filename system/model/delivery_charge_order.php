<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//计算消费金额

function make_delivery_charge_notice($money,$order_id,$payment_id,$memo='')
{
    if($money > 0){
        $notice['create_time'] = NOW_TIME;
        $notice['order_id'] = $order_id;
        $notice['supplier_id'] = $GLOBALS['db']->getOne("select supplier_id from ".DB_PREFIX."supplier_delivery_charge_order where id = ".$order_id);
        $notice['payment_id'] = $payment_id;
        $notice['memo'] = $memo;
        $notice['money'] = $money;
        $notice['order_type'] = 2;  //商户配送费预充值订单

        do{
            $notice['notice_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
            $GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$notice,'INSERT','','SILENT');
            $notice_id = intval($GLOBALS['db']->insert_id());
        }while($notice_id==0);
        return $notice_id;
    }
}


function delivery_charge_paid($payment_notice_id)
{

    $payment_notice_id = intval($payment_notice_id);
    $now = NOW_TIME;
    $GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = ".$now.",is_paid = 1 where id = ".$payment_notice_id." and is_paid = 0");
    $rs = $GLOBALS['db']->affected_rows();
    if($rs)
    {
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
        $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);
     	
        $GLOBALS['db']->query("update ".DB_PREFIX."supplier_delivery_charge_order set pay_amount = pay_amount + ".$payment_notice['money']." where id = ".$payment_notice['order_id']." and is_delete = 0 and order_status = 0 and pay_amount + ".$payment_notice['money']." <= total_price");   	
        
        $GLOBALS['db']->query("update ".DB_PREFIX."payment set total_amount = total_amount + ".$payment_notice['money']." where class_name = '".$payment_info['class_name']."'");

        
    }
    return $rs;
}

//同步订单支付状态
function delivery_charge_order_paid($order_id)
{
    $order_id  = intval($order_id);
    $order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_delivery_charge_order where id = ".$order_id);
    if($order['pay_amount']>=$order['total_price'])
    {
        $GLOBALS['db']->query("update ".DB_PREFIX."supplier_delivery_charge_order set pay_status = 2,order_status=1 where id =".$order_id." and pay_status <> 2");
        $rs = $GLOBALS['db']->affected_rows();
        if($rs)
        {
            //支付完成
            delivery_charge_order_paid_done($order_id);
            $result = true;
      
        }else{
            $result = false;
        }
    }else{
        $result = false;
    }
    return $result;
}

function delivery_charge_order_paid_done($order_id){
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_delivery_charge_order where id = ".$order_id);
    $delivery_money = $order_info['total_price'] - $order_info['payment_fee'];
    $GLOBALS['db']->query("update ".DB_PREFIX."supplier set delivery_money = delivery_money + ".$delivery_money." where id =".$order_info['supplier_id']);
     
}


?>