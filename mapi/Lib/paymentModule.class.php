<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class paymentApiModule extends MainBaseApiModule
{
	
	/**
	 * 订单支付页，包含检测状态，获取支付代码与消费券
	 * 
	 * 输入:
	 * id: int 订单ID
	 * 
	 * 输出:
	 * status:int 状态 0:失败 1:成功
	 * info: string 失败的原因
	 * 以下参数为成功时返回
	 * pay_status: int 支付状态 0:未支付 1:已支付 
	 * order_id: int 订单ID
	 * order_sn: string 订单号
	 * buy_type int 0 代表普通购买，1为积分兑换
	 * order_type int 1为充值订单，否则为商品或者团购下单
	 * pay_info: string 显示的信息
	 * consignee_info 收货地址
	 * 当pay_status 为1时
	 * coupon_name  消费劵的名称
	 * couponlist: array 消费券列表
	 * Array
	 * (
	 * 		Array(
	 * 			"password" => string 验证码
	 * 			"qrcode"  => string 二维码地址
	 * 		)
	 * )
	 * 
	 * 当pay_status 为0时
	 * payment_code: Array() 相关支付接口返回的支付数据
	 */
	public function done()
	{
		global_run();
		$root = array();
		$order_id = intval($GLOBALS['request']['id']);
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		if(empty($order_info))
		{
			return output(array(),0,"订单不存在");
		}
		
		$root['order_sn'] = $order_info['order_sn'];
		$root['order_id'] = $order_id;
		$root['is_main'] = $order_info['is_main'];
		$root['order_type']=$order_info['type'];
		if($order_info['pay_status']==2)
		{
			if($order_info['type'] !=1)
			{
			    $buy_type=0;
				$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
				if(!$deal_ids)
					$deal_ids = 0;
				$order_deals = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."deal as d where id in (".$deal_ids.")");
				
				$is_lottery = 0;
				foreach($order_deals as $k=>$v)
				{
					if($v['is_lottery'] == 1&&$v['buy_status']>0)
					{
						$is_lottery = 1;
					}
					
					if($v['buy_type'] == 1)
					{
					    $buy_type = 1;
					}
					
				}
				
				$order_info['region_lv1'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv1']);
				$order_info['region_lv2'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv2']);
				$order_info['region_lv3'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv3']);
				$order_info['region_lv4'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv4']);
				$consignee_info=array();
				if($order_info['consignee']){
				    $consignee_info['consignee'] = $order_info['consignee'];
				    $consignee_info['mobile'] = $order_info['mobile'];
				    $consignee_info['address'] = $order_info['region_lv1']['name'].$order_info['region_lv2']['name'].$order_info['region_lv3']['name'].$order_info['region_lv4']['name'].$order_info['address'].$order_info['street'].$order_info['doorplate'];
				}
				$root['buy_type']=$buy_type;
				$root['consignee_info'] = count($consignee_info)>0?$consignee_info:null;
				$root['pay_status'] = 1;					
				$root['pay_info'] = '订单已经收款';
				if($is_lottery)
					$root['pay_info'] .= '，您已经获得抽奖序列号';
		
				//有消费券,再显示消费券列表
				$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where user_id = ".$order_info['user_id']." and order_id = ".$order_id);
		
				$couponlist = array();
				foreach($list as $k=>$v)
				{
						$couponlist[$k]['password'] = $v['password'];
						$couponlist[$k]['qrcode'] = get_abs_img_root(gen_qrcode("tuan".$v['password']));
				}
				$root['couponlist'] = $couponlist;
				$root['coupon_count'] = intval(count($couponlist));
				$root['coupon_name'] = app_conf('COUPON_NAME');
				
				return output($root);
			}
			else
			{
				$root['pay_status'] = 1;
				$root['pay_info'] = round($order_info['pay_amount'],2)." 元 充值成功";
				return output($root);
			}
		}
		else
		{
            if($order_info['type']==1){//用户充值订单
                $pay_price = $order_info['total_price'];
                $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$order_info['payment_id']);
            }else{
                require_once(APP_ROOT_PATH."system/model/cart.php");
                	
                $delivery_id =  $order_info['delivery_id']; //配送方式
                $region_id = 0; //配送地区
                if($delivery_id)
                {
                    $consignee_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']." and is_default = 1");
                    $consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
                    $consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:array();
                    if($consignee_info)
                        $region_id = intval($consignee_info['region_lv4']);
                }
                $payment = $order_info['payment_id'];
                	
              //  $goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']);
                	
               // $data = count_buy_total($region_id,$order_info['consignee_id'],$payment,0,0,'','',$goods_list,$order_info['account_money'],$order_info['ecv_money']);

                
                
                $pay_price = $order_info['total_price'] - $order_info['pay_amount'];
                $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$order_info['payment_id']);
                if(empty($payment_info))
                {
                    return output(array(),0,"支付方式不存在");
                }
                if($pay_price<=0)
                {
                    return output(array(),0,"无效的支付方式");
                }
                $app_index = APP_INDEX;
                if($app_index=="wap")
                {
                    if ($payment_info['online_pay']!=2&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5)
                    {
                        return output(array(),0,"该支付方式不支持wap支付");
                    }
                }
                else
                {
                    if ($payment_info['online_pay']!=2&&$payment_info['online_pay']!=3&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5)
                    {
                        return output(array(),0,"该支付方式不支持手机支付");
                    }
                }
            }
			
			
			$payment_notice_id = make_payment_notice($pay_price,$order_id,$order_info['payment_id']);
			require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			$payment_code = $payment_object->get_payment_code($payment_notice_id);
			$root['title'] = $payment_info['name'];
			
			if($payment_info['class_name']=='Account'){
			   $is_account_pay = 1; 
			}else{
			   $is_account_pay = 0;
			}
			$root['is_account_pay'] = $is_account_pay;
			$root['pay_status'] = 0;
			$root['payment_code'] = $payment_code;
			$root['app_index'] = $app_index;
			return output($root);
		}		
	}
	
	public function order_share(){
	    global_run();
	    $root = array();
	    $order_id = intval($GLOBALS['request']['id']);

	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	        require_once(APP_ROOT_PATH.'system/model/topic.php');
	        order_share($order_id);
	    }
	    return output($root);
	    
	}
	
	
}
?>

