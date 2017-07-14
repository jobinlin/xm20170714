<?php
// +----------------------------------------------------------------------
// | EaseTHINK 易想团购系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.easethink.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'微信支付v3(WAP版本)',
	'appid'	=>	'微信公众号ID',
	'appsecret'=>'微信公众号SECRT',
	'mchid'	=>	'微信支付MCHID',
  	'partnerid'	=>	'商户ID',
	'partnerkey'	=>	'商户key',
	'key'	=>	'商户支付密钥Key',
	'sslcert'=>'apiclient_cert证书路径',
	'sslkey'=>'apiclient_key证书路径',
	'type'=>'类型(V2或V3)',
	'scan' => '扫码支付',
	'scan_0'=>	'关闭',
	'scan_1'=> 	'开启',
);
$config = array(
	'appid'=>array(
		'INPUT_TYPE'=>'0',
	),//微信公众号ID
	'appsecret'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //微信公众号SECRT
	'mchid'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //微信支付MCHID
	'key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户支付密钥Key
	'scan'	=>	array(
			'INPUT_TYPE'	=>	'1',
			'VALUES'	=> 	array(0,1)
	),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Wwxjspay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '2';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

// 支付宝手机支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
require_once(APP_ROOT_PATH."system/payment/Wxjspay/WxPay.Api.php");
require_once(APP_ROOT_PATH."system/payment/Wxjspay/WxPay.JsApiPay.php");
require_once(APP_ROOT_PATH."system/model/payment.php");
class Wwxjspay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		
		$order_sn = $payment_notice['notice_sn'];
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		
		$title_name = get_order_type_name($payment_notice);
		
		if(APP_INDEX=="index")
		{		
			$script = '<script type="text/javascript">$("#wxscan").everyTime(2000,function(){

				var query = new Object();
				query.act = "check_payment_notice";
				query.notice_id = '.$payment_notice_id.';
				$.ajax({
					url:AJAX_URL,
					dataType: "json",
					data:query,
			        type:"POST",
			        global:false,
					success:function(data)
					{
					    if(data.status)			    		   
					    {
					    	$("#wxscan").stopTime();
					    	location.reload();
					    }
					}
				});
				
			});</script>';	
			return "<img id='wxscan' src='".url("index","file#wxpay_qr_code",array("notice_id"=>$payment_notice_id))."' /> <br /> 打开微信扫码，即可支付".$script;
		}
		else
		{	 		
	
			$pay['pay_info'] = $title_name;
			$pay['pay_action'] = SITE_DOMAIN.APP_ROOT."/cgi/payment/wwxjspay/redirect.php?notice_id=".$payment_notice_id."&from=".$GLOBALS['request']['from'];
			$pay['payment_name'] = "微信支付v3";
			$pay['pay_money'] = $money;
			$pay['class_name'] = "Wwxjspay";
			return $pay;		
		}			
	}
	
	public function get_redirect_url($payment_notice_id)
	{
		$from = strim($_REQUEST['from']);
	
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		if($payment_notice['order_type']==3){
			$sql = "select sub_name ".
					"from ".DB_PREFIX."deal_order_item ".
					"where order_id =". intval($payment_notice['order_id']);
			$title_name = $GLOBALS['db']->getOne($sql);
            $title_name = $title_name?$title_name:"会员充值";
		}elseif($payment_notice['order_type']==4){
			$title_name='会员买单';
		}
		$table_name=get_table_name($payment_notice);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".$table_name." where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$money_fen=intval($money*100);
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo,name from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));	
		$payment_info['config'] = unserialize($payment_info['config']);
		$wx_config=$payment_info['config'];
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/callback/payment/wwxjspay_notify.php';
		$pay_action = SITE_DOMAIN.APP_ROOT."/cgi/payment/wwxjspay/redirect.php?notice_id=".$payment_notice_id."&from=".$from;



        //①、获取用户openid
        $tools = new JsApiPay();
		$user_info = es_session::get('user_info');
		$openid = $user_info['wx_openid'];
		
		if(empty($openid))
		{
			if(WEIXIN_TYPE=="platform")
			{
				//方维云平台saas模式接入
				$appid = FANWE_APP_ID;
				$appsecret = FANWE_AES_KEY;
				$server = new SAASAPIServer($appid, $appsecret);
				$ret = $server->takeSecurityParams($_SERVER['QUERY_STRING']);
					
					
				if($ret['openid'])
				{
					$openid = $ret['openid'];
			
				}
				else
				{
					//加密
					$client = new SAASAPIClient($appid, $appsecret);
					$widthAppid = true;  // 生成的安全地址是否附带appid参数
					$timeoutMinutes = 10; // 安全参数过期时间（单位：分钟），小于等于0表示永不过期
					$params['from'] = $pay_action;
					$params['appsys_name'] = $GLOBALS['_FANWE_SAAS_ENV']['APPSYS_ID'];
			
					$url = 'http://service.yun.fanwe.com/weixin/create_url';
					$wx_url = $client->makeSecurityUrl($url, $params, $widthAppid, $timeoutMinutes);
					//var_dump($wx_url);exit;
					//$wx_url = 'http://service.yun.fanwe.com/weixin/create_url?from='.urlencode($back_url);
					app_redirect($wx_url);
				}
			}
			else
			{
                $openid = $tools->GetOpenid();
			}
		}

        //②、统一下单
        $timeStamp =NOW_TIME;
        $input = new WxPayUnifiedOrder();

        $input->SetBody(iconv_substr($title_name,0,50, 'UTF-8'));
        $input->SetOut_trade_no($payment_notice['notice_sn']);
        $input->SetTotal_fee($money_fen);
        $input->SetNotify_url($data_notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);

        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        $prepay_id = $order['prepay_id'];


		$html_text = @file_get_contents(APP_ROOT_PATH."system/payment/Wxjspay/pay.html");
		$html_text = str_replace("__jsApiParameters__", $jsApiParameters, $html_text);
		$html_text = str_replace("__pay_url__", SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$payment_notice['order_id'])), $html_text);
// 		var_dump(htmlspecialchars($html_text));exit;


		$html_text = str_replace("__cart_pay_url__", SITE_DOMAIN.wap_url("index","cart#pay",array('id'=>$payment_notice['order_id'])), $html_text);

		return $html_text;
	
	}
	
	
	public function response($request)
	{	
							
	}
	
	public function notify($request){
	    require_once APP_ROOT_PATH."system/payment/Wxjspay/WxPay.Notify.php";
			//进入V3
        $msg="ok";
        //存储微信的回调
        $notify = new WxPayNotify();
        $result = $notify->Handle();
        if($result == false){
            $notify->ReplyNotify(false);
            return;
        } else {
            //print_r($result);exit;
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $trade_no=$result['transaction_id'];
            $order_id = intval($result['order_id']);
            $payment_notice_sn = strim($result['out_trade_no']);
            if ($order_id == 0){
                $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
                $order_id = intval($payment_notice['order_id']);
            }else{
                $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id);
            }
            notify_base($trade_no,$payment_notice);
        }
        $notify->ReplyNotify(true);


	}
	
	public function get_display_code(){
		return "微信支付v3";
	}
	
	
	public function get_web_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Wwxjspay'");
		if($payment_item)
		{
		    $html = "<label class='ui-radiobox' rel='payment_rdo'><input type='radio' name='payment' value='".$payment_item['id']."' />";
		    
// 			if($payment_item['logo']!='')
// 			{
// 			    $html = "<label class='ui-radiobox' style='background:url(".APP_ROOT.$payment_item['logo'].") 15px 50% no-repeat' rel='payment_rdo'><input type='radio' name='payment' value='".$payment_item['id']."' />";
// 			    $html .= "<span class='f_l' style='padding-left: 50px;'>微信扫码支付</span>";
// 			}
		    $html .= "微信扫码支付";
			
			$html.="</label>";
			return $html;
		}
		else
		{
			return '';
		}
	}
}
?>