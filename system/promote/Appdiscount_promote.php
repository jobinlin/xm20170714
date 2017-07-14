<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$lang = array(
	'name'	=>	'手机下单立减',
	'description'	=>	'手机下单满XX金额立减XX元',
	'discount_limit'	=>	'减额标准，即满XX元',
	'discount_amount'	=>	'减额额度，即立减XX元',
        'discount_type' =>"满立减类型   1满立减  2折扣 ..后续继续增加"
    
);

$config = array(
	'discount_limit'	=>	'',
	'discount_amount' =>	'',
        'discount_type' => '1' ,
		'is_supplier'=>'1'
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Appdiscount';

    /* 名称 */
    $module['name']    = $lang['name'];
    
    /* 描述 */
    $module['description']    = $lang['description'];

	$module['config'] = $config;
    $module['lang'] = $lang;
    return $module;
}



require_once(APP_ROOT_PATH.'system/libs/promote.php');
class Appdiscount_promote implements promote {
	public function count_buy_total($region_id,
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
									$old_result){
		//取出接口配置
		$promote_obj = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."promote where class_name='Appdiscount'");
		$promote_cfg = unserialize($promote_obj['config']);
		
		global $is_app;
		if(APP_INDEX=='app' &&$old_result['total_price']>=$promote_cfg['discount_limit'])
		{
				
			$old_result['total_price'] = $old_result['total_price'] - $promote_cfg['discount_amount'] > 0 ? $old_result['total_price'] - $promote_cfg['discount_amount'] : 0;
			$old_result['pay_total_price']	=	$old_result['pay_total_price'] - $promote_cfg['discount_amount'] > 0 ? $old_result['pay_total_price'] - $promote_cfg['discount_amount'] : 0;
				
			$old_result['pay_price'] = $old_result['total_price'] + $old_result['delivery_fee'] +  $old_result['payment_fee']; //加上运费
			$old_result['pay_price'] = $old_result['pay_price'] - $old_result['paid_account_money'] - $old_result['paid_ecv_money'];
			$old_result['pay_price'] = $old_result['pay_price'] - $old_result['user_discount']; //扣除用户折扣
				
			// 当余额 + 代金券 > 支付总额时优先用代金券付款  ,代金券不够付，余额为扣除代金券后的余额
			if($old_result['ecv_money'] + $old_result['account_money'] > $old_result['pay_price'])
			{
				if($old_result['ecv_money'] >= $old_result['pay_price'])
				{
					$ecv_use_money = $old_result['pay_price'];
					$old_result['account_money'] = 0;
				}
				else
				{
					$ecv_use_money = $old_result['ecv_money'];
					$old_result['account_money'] = $old_result['pay_price'] - $ecv_use_money;
				}
			}
			else
			{
				$ecv_use_money = $old_result['ecv_money'];
			}
			
			$old_result['ecv_money'] = $ecv_use_money;
			
			if( $old_result['ecv_money']){
			    $old_result['ecv_data']['money'] = $old_result['ecv_money'];
			}
			$old_result['pay_price'] = $old_result['pay_price'] - $ecv_use_money - $old_result['account_money'];
		
			$old_result['promote_description'][] = $promote_obj['description'];
			$old_result['promote_arr'][] = $promote_obj;
			$old_result['promote']['Appdiscount']['name'] = $promote_obj['name'];
			$old_result['promote']['Appdiscount']['discount_amount'] += $promote_cfg['discount_amount'];
			$result = $old_result;
		}
		else
			$result = $old_result;
		
		return $result;
	}
        
        
        
        /**
         * 买单优惠
         * @param type $money
         * @return type
         */
        public function count_store_pay($money,$conf){
        	$pay_amount = 0;
        	$discount_price = 0;

        	// 增加app的判断
        	global $is_app;
        	if (APP_INDEX=='app') {
				//取出促销规则
	        	$conf_obj = unserialize($conf);
	        	$discount_limit = $conf_obj['discount_limit'];
	        	$discount_amount = $conf_obj['discount_amount'];
	        
	        	if($money>=$discount_limit){
	        		// $discount_price = intval($money/$discount_limit)*$discount_amount;
	        		// 调整优惠的减免方式 
	        		$discount_price = $discount_amount;
	        		$pay_amount = $money - $discount_price;
	        	}else{
	        		$pay_amount = $money;
	        		$discount_price = 0;
	        	}
        	}
        	
        
        	$result = array();
        	//业务流程
        	$resutl['pay_amount'] = $pay_amount;
        	$resutl['discount_price'] = $discount_price;
        	return $resutl;
        }
}
?>