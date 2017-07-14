<?php
// +----------------------------------------------------------------------
// | Fanwe 方维商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_chargeApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 会员中心充值页面接口
	 * 
	 * 	  输入：
	 *  
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题

	 */
	public function index()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		
		
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){			
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;
			    //输出支付方式
			
			$is_weixin=isWeixin();
			
			//输出支付方式
			if (APP_INDEX == 'wap' && !$is_weixin) {
			    //支付列表
			    $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and class_name != 'Wwxjspay' and is_effect = 1";
			}
			elseif (APP_INDEX == 'wap' && $is_weixin) {
			    $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
			     
			}
			else {
			    //支付列表
			    $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
			}
			
			if(allow_show_api())
			{
				$payment_list = $GLOBALS['db']->getAll($sql);
			}
			//输出支付方式
	        foreach($payment_list as $k=>$v)
	        {
	                $directory = APP_ROOT_PATH."system/payment/";
	                $file = $directory. '/' .$v['code']."_payment.php";
	                if(file_exists($file))
	                {
	                        require_once($file);
	                        $payment_class = $v['code']."_payment";
	                        $payment_object = new $payment_class();
	                        $payment_list[$k]['name'] = $payment_object->get_display_code();
	                }
	
	                if($v['logo']!="")
	                $payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'],40,40,1));
	        }
			
	        sort($payment_list);


			$root['payment_list']=$payment_list?$payment_list:array();


            $root['money_number_array'] = explode(",", app_conf("SCORE_RECHARGE_MONEY_NUMBER_SET"));
            logger( $root['money_number_array']);
            $root['money_number_array'] = $root['money_number_array'] ? $root['money_number_array'] : "50,100,300,500,1000,1500";
            sort($root['money_number_array']);
            $root['money_number'] = count($root['money_number_array']);
            $root['money_number_array_other'] = 0;
            //将数量拆分成数组
            if ($root['money_number'] > 6) {
                $money_number_array = array();
                $money_number_array_other = array();
                for ($i = 0; $i < count($root['money_number_array']); $i++) {
                    if ($i < 5) {
                        $money_number_array[] = $root['money_number_array'][$i];
                    } else {
                        $money_number_array_other[] = "&yen;" . $root['money_number_array'][$i];
                    }
                }
                $root['money_number_array'] = $money_number_array;
                $root['money_number_array_other'] = json_encode($money_number_array_other);
            }
			$root['page_title'].="会员充值";
		}

		return output($root);	

	}


	/**
	 * 	 会员中心充值操作接口
	 * 
	 * 	  输入：
	 *  payment_id:int 支付方式id
	 *  money: float  支付金额
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题

	 */	
	public function done()
	{
		$root = array();		

		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$payment_id = intval($GLOBALS['request']['payment_id']);
		$money = floatval($GLOBALS['request']['money']);
		
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){			
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;

			if($money<=0)
			{
				return output("",0,"请输入正确的金额");
			}

			$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_id);
			if(!$payment_info)
			{
				return output("",0,"支付方式不存在");
			}

			if($payment_info['fee_type']==0) //定额
			{
			    $payment_fee = $payment_info['fee_amount'];
			}
			else //比率
			{
			    $payment_fee = $money * $payment_info['fee_amount'];
			}
			
			//开始生成订单
			$now = NOW_TIME;
			$order['type'] = 1; //充值单
			$order['user_id'] = $GLOBALS['user_info']['id'];
			$order['create_time'] = $now;
			$order['update_time'] = $now;
			$order['total_price'] = $money + $payment_fee;
			$order['deal_total_price'] = $money;
			$order['pay_amount'] = 0;
			$order['pay_status'] = 0;
			$order['delivery_status'] = 5;
			$order['order_status'] = 0;
			$order['payment_id'] = $payment_id;
			$order['payment_fee'] = $payment_fee;

		
			do
			{
				$order['order_sn'] = to_date(get_gmtime(),"Ymdhis").rand(100,999);
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT'); 
				$order_id = intval($GLOBALS['db']->insert_id());
			}while($order_id==0);
			
			require_once(APP_ROOT_PATH."system/model/cart.php");
			$payment_notice_id = make_payment_notice($order['total_price'],$order_id,$payment_info['id']);
			//创建支付接口的付款单
			$root['app_index'] = APP_INDEX;
			$rs = order_paid($order_id);  
			if($rs)
			{
					$root['pay_status'] = 1;
					$root['order_id'] = $order_id;
			}
			else
			{

			    require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
			    $payment_class = $payment_info['class_name']."_payment";
			    $payment_object = new $payment_class();
			    $payment_code = $payment_object->get_payment_code($payment_notice_id);
			    $root['online_pay'] = $payment_info['online_pay'];
			    es_session::set("user_charge_".$user_id, $payment_notice_id);
				if($payment_info['online_pay']==3) //sdk在线支付
				{

					$root['pay_status'] = 0;
					$root['order_id'] = $order_id;
					$root['sdk_code'] = $payment_code['sdk_code'];

					
					return output($root); //sdk支付
				}
				else
				{		
					
					$root['pay_status'] = 0;
					$root['payment_code'] = $payment_code;
					$root['pay_url'] = $payment_code['pay_action'];
					$root['page_title'].="充值中……";	
					$root['order_id'] = $order_id;	
				}
			}

		}	
		return output($root);	

	}
	
	
}
?>