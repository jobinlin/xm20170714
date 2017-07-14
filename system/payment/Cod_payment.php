<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'货到付款',
	'account_credit'	=>	'货到付款a',
	'use_user_money' =>	'货到付款b',
	'use_all_money'	=>	'全额支付',
	'USER_ORDER_PAID'	=>	'%s订单付款,付款单号%s',

    'COD_PAYMENT'	=>	'货到付款支付方式',
    'COD_PAYMENT_POS'	=>	'POS机刷卡',
    'COD_PAYMENT_CASH'	=>	'现金',
);
$config = array(
    'COD_PAYMENT'	=>	array(
        'INPUT_TYPE'	=>	'3',
        'VALUES'	=>	array(
            'POS', //POS机刷卡
            'CASH', //现金
        ),
        'tip'	=>	'选择《POS机刷卡》显示《货到付款（POS机刷卡）》,选择《现金》显示《货到付款（现金）》,未选择默认显示《货到付款》'
    ),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Cod';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = 6;

    /* 配送 */
    $module['config'] = $config;

    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

// 货到付款支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
require_once(APP_ROOT_PATH."system/model/payment.php");
class Cod_payment implements payment {
	static $payment_lang;
	public function __construct(){  
        $this->payment_lang = array(
			'name'	=>	'货到付款',
			'account_credit'	=>	'货到付款a',
			'use_user_money' =>	'货到付款b',
			'use_all_money'	=>	'全额支付',
			'USER_ORDER_PAID'	=>	'%s订单付款,付款单号%s',

			'COD_PAYMENT'	=>	'货到付款支付方式',
			'COD_PAYMENT_POS'	=>	'POS机刷卡',
			'COD_PAYMENT_CASH'	=>	'现金',
		);  
    }  
	public function get_payment_code($payment_notice_id)
	{		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);	
		
		$payment_result=payment_base($payment_notice);
		/*if($payment_result['rs'])
		{

		}*/
	}
	
	/**
	 * 直接处理付款单
	 * @param unknown_type $payment_notice
	 */
	public function response($payment_notice)
	{
		return false;	
	}
	
	public function notify($request)
	{
		return false;
	}
	
	public function get_display_code()
	{
		return '货到付款';
	}
}
?>