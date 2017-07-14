<?php

class dc_order_schedule {
	
	/**
	 * $data 格式
	 * array("dest"=>openid,"content"=>序列化的消息配置);
	 */
	public function exec($data){
		//关闭未付款的买单定单(1小时)
		$dc_order_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_order where delivery_operation >0");

		if(count($dc_order_list)>0){
		    require_once(APP_ROOT_PATH."system/model/dc.php");
		    require_once(APP_ROOT_PATH."system/delivery/DaDaDelivery.php");
            foreach($dc_order_list as $k=>$v){
                if($v['delivery_part']==1){  //商家配送，这个是把先前的第三方配送改为商家自己配送，并向第三方配送取消订单
                    switch ($v['delivery_operation']){
                        case 2:  //向达达取消订单
                            $DaDaDelivery = new DaDaDelivery();
                            $result = $DaDaDelivery->cancelOrder($v['order_sn']);
                
                            if($result['code']==0 && $result['result']['deduct_fee']>0){  //达达配送取消成功，扣除商家违约金
                                $info = '外卖订单'.$v['order_sn'].'关闭,扣除达达配送违约金';
                                dc_balance_delivery_fee($v['supplier_id'] , $result['result']['deduct_fee'] ,$info);
                            }
                            $sql = "update ".DB_PREFIX."dc_order set delivery_operation=0 where id= ".$v['id'];
                            $GLOBALS['db']->query($sql);
                            break;
                    }
                }elseif($v['delivery_part']==2){  //达达配送
                    switch ($v['delivery_operation']){
                        case 1:  //向达达推单
                            $DaDaDelivery = new DaDaDelivery();
                            $DaDaDelivery->sendOrder($v['id']);
                            dc_alarm_supplier_delivery_fee($v['supplier_id']);
                            $sql = "update ".DB_PREFIX."dc_order set delivery_operation=0 where id= ".$v['id'];
                            $GLOBALS['db']->query($sql);
                            break;
                        case 2:  //向达达取消订单
                            $DaDaDelivery = new DaDaDelivery();
                            $result = $DaDaDelivery->cancelOrder($v['order_sn']);

                            if($result['code']==0 && $result['result']['deduct_fee']>0){  //达达配送取消成功，扣除商家违约金
                                $info = '外卖订单'.$v['order_sn'].'关闭,扣除达达配送违约金';
                                dc_balance_delivery_fee($v['supplier_id'] , $result['result']['deduct_fee'] ,$info);
                            }                       
                            $sql = "update ".DB_PREFIX."dc_order set delivery_operation=0 where id= ".$v['id'];
                            $GLOBALS['db']->query($sql);
                            break;
                    }
                }

            }
		    
		}
		//超时接单的订单处理，把商家超时接单的订单，自动关闭
		timeout_accept_order_process();
        //超时支付的订单处理，把用户超时支付的订单，自动关闭，超时支付的时间为15分钟
		timeout_pay_order_process();
		

        $schedule_obj = new Schedule();
        $schedule_obj->send_schedule_plan("dc_order", "定时任务", array(), NOW_TIME);

        $result['status'] = 1;
        $result['attemp'] = 0;
        $result['info'] = "处理成功";
        return $result;
	}	
}
?>