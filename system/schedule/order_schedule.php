<?php

class order_schedule {
	
	/**
	 * $data 格式
	 * array("dest"=>openid,"content"=>序列化的消息配置);
	 */
	public function exec($data){
		//关闭未付款的买单定单(1小时)
		$order_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."store_pay_order where is_delete=0 and pay_status = 0 and create_time < ".(NOW_TIME-3600)." order by create_time asc limit 20");

		if(count($order_list)>0){
			require_once APP_ROOT_PATH."system/model/store_pay.php";
			foreach ($order_list as $key=>$order_info){
				if($order_info)
				{
					syn_cancel_store_order($order_info['id']);
				}
			}
		}
		//发券的不支持过期退的团购订单，自动结单
		$select_where='';
		
		syn_auto_over_status(0,$limit='limit 20');
		
        $schedule_obj = new Schedule();
        $schedule_obj->send_schedule_plan("order", "定时任务", array(), NOW_TIME);

        $result['status'] = 1;
        $result['attemp'] = 0;
        $result['info'] = "处理成功";
        return $result;
	}	
}
?>