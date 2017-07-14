<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cartModule extends MainBaseModule
{
	public function index()
	{	
		global_run();
		init_app_page();
		clear_form_verify();
		$GLOBALS['tmpl']->display("cart.html");
	}
	
	/**
	 * 购物车的提交页
	 */
	public function check()
	{
		assign_form_verify();
		global_run();
		init_app_page();
		if((check_save_login()!=LOGIN_STATUS_LOGINED&&$GLOBALS['user_info']['money']>0)||check_save_login()==LOGIN_STATUS_NOLOGIN)
		{
			app_redirect(url("index","user#login"));
		}
		$id=intval($_REQUEST['id']);
		$address_id=intval($_REQUEST['address_id']);
		$GLOBALS['tmpl']->assign("deal_id",$id);
		$buy_type = 0;
		require_once(APP_ROOT_PATH."system/model/cart.php");
		if($id > 0){
		    $cart_result = load_cart_list($id);
		    $cart_list_temp = end($cart_result['cart_list']);
		    $buy_type = $cart_list_temp['buy_type'];
		}else{
		    $cart_result = load_cart_list($id=0,false);
		}

		$cart_list = $cart_result['cart_list'];
		if(!$cart_list) {
			app_redirect(url("index"));
		}

		//先计算用户等级折扣
		$user_id = intval($GLOBALS['user_info']['id']);
		$user_discount = 1;
		if ($user_id) {
			$user_discount = getUserDiscount($user_id);
		}
		$group_list=array();

		$delivery_type_ids = array();
		$supplier_ids = array();
		$showInvoiceInfo = 1; // 是否显示发票信息
		foreach ($cart_list as $val) {
			if ($val['supplier_id'] == 0) {
				$delivery_type_ids[] = $val['deal_id'];
			} else {
				$supplier_ids[] = $val['supplier_id'];
			}
			if ($showInvoiceInfo && $val['buy_type'] == 1) {
				$showInvoiceInfo = 0;
			}
		}
		$delivery_type = array();
		if ($delivery_type_ids) {
			$db_delivery_type = $GLOBALS['db']->getAll('select id, delivery_type from '.DB_PREFIX.'deal where id in('.implode(',', $delivery_type_ids).')');
			foreach ($db_delivery_type as $type) {
				$delivery_type[$type['id']] = $type['delivery_type'];
			}
		}
		
		$supplier_names = array();
		if ($supplier_ids) {
			$db_supplier_names = $GLOBALS['db']->getAll('select id, name from '.DB_PREFIX.'supplier where id in('.implode(',', $supplier_ids).')');
			foreach ($db_supplier_names as $name) {
				$supplier_names[$name['id']] = $name['name'];
			}
		}

		// 获取平台和商户的开票信息
		if ($showInvoiceInfo) {
			$merge_supplier_ids = array_merge(array(0), $supplier_ids);
			$invoice_sql = 'SELECT * FROM '.DB_PREFIX.'invoice_conf WHERE supplier_id in('.implode(',', $merge_supplier_ids).')';
			$db_invoice_list = $GLOBALS['db']->getAll($invoice_sql);
			$invoice_list = array();
			foreach ($db_invoice_list as $key => $value) {
				if ($value['invoice_type'] > 0) {
					if (!empty($value['invoice_content'])) {
						$value['invoice_content'] = explode(' ', $value['invoice_content']);
					} else {
						$value['invoice_content'] = array('明细');
					}
				}
				$invoice_list[$value['supplier_id']] = $value;
			}
			if (!empty($invoice_list)) {
				$invoice_notice = app_conf('INVOICE_NOTICE');
				$GLOBALS['tmpl']->assign('invoice_notice', $invoice_notice);
			}
		}
		$GLOBALS['tmpl']->assign('showInvoiceInfo',  $showInvoiceInfo);
		

		foreach($cart_list as $k=>$v){
			$unit_price = $v['unit_price'];
			$total_price = $v['total_price'];
			if ($v['allow_user_discount']) {
				$unit_price = round($unit_price * $user_discount,2);
				$total_price = $unit_price * $v['number'];
			}
			$v['unit_price'] = $unit_price;
			$format=format_price_html($unit_price,2);
			$v['unit_price_format']=$format['bai'].'.'.$format['fei'];
			
			$v['total_price'] = $total_price;
			$format=format_price_html($total_price,2);
			$v['total_price_format']=$format['bai'].'.'.$format['fei'];
			if($v['supplier_id']==0){
				// $delivery_type = $GLOBALS['db']->getOne("select delivery_type from ".DB_PREFIX."deal where id=".$v['deal_id']);
				if($delivery_type[$v['deal_id']]==1){ //平台物流配送商品
		            $group_list['p_wl']['goods_list'][$v['id']]=$v;
		            $group_list['p_wl']['supplier']="平台自营";

		            $ivoKey = 'p_wl';
		            // $group_list['p_wl']['invoice_conf'] = $invoice_list[$v['supplier_id']];
		        }elseif($delivery_type[$v['deal_id']]==3){ //平台驿站配送商品
		            $group_list['p_yz']['goods_list'][$v['id']]=$v;
		            $group_list['p_yz']['supplier']="平台自营 - 驿站配送";

		            $ivoKey = 'p_yz';
		            // $group_list['p_yz']['invoice_conf'] = $invoice_list[$v['supplier_id']];
		        }else{ //平台无需配送商品，直接进入订单提交页面，不进入购物车页面
		            $group_list[$v['supplier_id']]['goods_list'][$v['id']]=$v;
		            $group_list[$v['supplier_id']]['supplier']="平台自营";

		            $ivoKey = $v['supplier_id'];
		            // $group_list['p_yz']['invoice_conf'] = $invoice_list[$v['supplier_id']];
		        }
			}else{
				$group_list[$v['supplier_id']]['goods_list'][$v['id']] = $v;
				if(!$group_list[$v['supplier_id']]['supplier']) {
					$group_list[$v['supplier_id']]['supplier']= $supplier_names[$v['supplier_id']]; // $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = '".$v['supplier_id']."'");
				}

				$ivoKey = $v['supplier_id'];
				// $group_list[$v['supplier_id']]['invoice_conf'] = $invoice_list[$v['supplier_id']];
			}
			if ($showInvoiceInfo && !empty($invoice_list[$v['supplier_id']])) {
				$group_list[$ivoKey]['invoice_conf'] = $invoice_list[$v['supplier_id']];
			}
		}
		
		$total_price = $cart_result['total_data']['total_price'] * $user_discount;
		
		foreach($cart_list as $k=>$v)
		{
			$id = intval($v['id']);
			$number = intval($v['number']);
			$data = check_cart($id, $number);
			if(!$data['status'])
			{
				showErr($data['info']);
			}
		}

		//输出购物车内容
		$GLOBALS['tmpl']->assign("cart_list",$cart_list);
		
		$GLOBALS['tmpl']->assign('total_price',$total_price);

		
		$is_delivery = 0;
		foreach($cart_list as $k=>$v)
		{			

			if($v['is_delivery']==1)
			{
				$is_delivery = 1;
				break;
			}
		}
			
		if($is_delivery)
		{
			//输出配送方式
			$consignee_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']);
			$GLOBALS['tmpl']->assign("consignee_count",intval($consignee_count));
			if($address_id>0){
				$consignee_id=$address_id;
			}else{
				$consignee_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']." and is_default = 1");
			}
			
			$GLOBALS['tmpl']->assign("consignee_id",intval($consignee_id));
			$consignee_info=load_auto_cache("consignee_info",array("consignee_id"=>intval($consignee_id)));
			$GLOBALS['tmpl']->assign("region_id",$consignee_info['consignee_info']['region_lv4']);
			$consignee_list = $GLOBALS['db']->getAll("select id from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']." ORDER BY is_default DESC");
			foreach($consignee_list as $k=>$v){
				$consignee_list[$k]=load_auto_cache("consignee_info",array("consignee_id"=>$v['id']));
				if($v['id']==intval($consignee_id)){
					$consignee_info=$consignee_list[$k];
					unset($consignee_list[$k]);
					array_unshift($consignee_list,$consignee_info);
				}
			}
			$GLOBALS['tmpl']->assign("consignee_list",$consignee_list);
			$GLOBALS['tmpl']->assign("consignee_count",count($consignee_list));
					
			
		}
		
		$delivery_info=get_express_fee($cart_list,intval($consignee_id));
		$has_p_yz = 0;
		$has_p_wl = 0;
		foreach ($group_list as $k=>$v){
		    $supplier_total_pirce = 0;
		    foreach($v['goods_list'] as $kk=>$vv){
		        $supplier_total_pirce+=$vv['total_price'];
		    }
		    
			if($delivery_info){
				$group_list[$k]['delivery_fee']=$delivery_info[$k];
				$supplier_total_pirce+=$delivery_info[$k]['total_fee'];
			}else{
				$group_list[$k]['delivery_fee']=-1;
			}

			if($buy_type == 0){  //积分商品兑换不使用优惠劵和红包
			    $youhui_supplier_id = intval($k);

			    $log_sql="select yl.id,y.youhui_value,y.start_use_price from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as y on y.id=yl.youhui_id
                  where y.supplier_id = ".$youhui_supplier_id." and yl.confirm_time=0 and (yl.expire_time=0 or yl.expire_time>".NOW_TIME.") and y.is_effect=1
	              and (y.start_use_price<=".$supplier_total_pirce." or y.start_use_price=0) and y.youhui_type=2 and yl.user_id=".$GLOBALS['user_info']['id']." order by y.youhui_value desc";
			  
			    $youhui_info=$GLOBALS['db']->getAll($log_sql);
			   
			    if($k=='p_yz' ){
			        $has_p_yz = 1;
			    }
			    if($k=='p_wl' ){
			        $has_p_wl = 1;
			    }
			    if($has_p_yz==1 && $has_p_wl==1){
			        $youhui_temp =  $youhui_info[0];
			        $youhui_info[0] =  $youhui_info[1];
			        $youhui_info[1] = $youhui_temp;
			    }

			    $group_list[$k]['youhui_info'] = $youhui_info;

			}
	
		}
		$cart_list_group = $group_list;
		//print_r($cart_list_group);exit;
		$GLOBALS['tmpl']->assign("cart_list_group",$cart_list_group);
		$GLOBALS['tmpl']->assign("is_delivery",$is_delivery);
		//配送方式由ajax由 consignee 中的地区动态获取
		//判断是否允许自提
		$is_pick=1;
		$supplier=array();
		$is_zy=0;//是否是平台自营商品
		foreach ($cart_list as $k=>$v){
			if($v['is_shop']==1 && $v['is_pick']==0){
		        $is_pick=0;
		        break;
		    }
		    if(!in_array($v['supplier_id'], $supplier)){
		    	$supplier[]=$v['supplier_id'];
		    }
		    if($v['supplier_id']==0){  //如果是平台自营，不能自提
		    	$is_zy=1;
		    }
			
		}
		//只有普通商家才能上门自提,且多商家下单时，不允许自提
		if($is_pick==1 && count($supplier) == 1 && $is_zy==0)
		{
			$supplier_id=$supplier[0];
			$is_pick=1;
		}else{
			$supplier_id=0;
			$is_pick=0;
		}
		if($is_pick==1){
			$location = $GLOBALS['db']->getAll("select id,name,address,tel from ".DB_PREFIX."supplier_location where supplier_id=".$supplier_id." and name <> '' and (address <> '' or tel <> '') ");
		}
		$GLOBALS['tmpl']->assign("location",$location);
		$GLOBALS['tmpl']->assign("is_pick",$is_pick);
			
		//输出支付方式
		$payment_list = load_auto_cache("cache_payment");
		
		foreach($cart_list as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
			{
				$define_payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$v['deal_id']);
				$define_payment = array();
				foreach($define_payment_list as $kk=>$vv)
				{
					array_push($define_payment,$vv['payment_id']);
				}
				foreach($payment_list as $k=>$v)
				{
					if(in_array($v['id'],$define_payment))
					{
						unset($payment_list[$k]);
					}
				}
			}
		}		


		$icon_paylist = array(); //用图标展示的支付方式
		$disp_paylist = array(); //特殊的支付方式(Voucher,Account,Otherpay)
		$bank_paylist = array(); //网银直连
		
		$wx_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Wwxjspay'");
		if($wx_payment)
		{
			$wx_payment['config'] = unserialize($wx_payment['config']);
			if($wx_payment['config']['scan']==1)
			{
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' .$wx_payment['class_name']."_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					$payment_class = $wx_payment['class_name']."_payment";
					$payment_object = new $payment_class();
					$wx_payment['display_code'] = $payment_object->get_web_display_code();
					$disp_paylist[] = $wx_payment;
				}
			}
		}
		
		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=="Voucher"||$v['class_name']=="Account"||$v['class_name']=="Otherpay")
			{
				if($v['class_name']=="Account")
				{
					if($GLOBALS['user_info']['money']<0.01){
						continue;
					}
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();					
					}
				}
				if($v['class_name']=="Voucher")
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code($total_price);
					}
				}
				$disp_paylist[] = $v;
			}
			else
			{
				if($v['is_bank']==1)
				$bank_paylist[] = $v;	
				else
				$icon_paylist[] = $v;
			}
		}
	
		
		
		
		$GLOBALS['tmpl']->assign("icon_paylist",$icon_paylist);
		$GLOBALS['tmpl']->assign("disp_paylist",$disp_paylist);//支付方式
		$GLOBALS['tmpl']->assign("bank_paylist",$bank_paylist);
		
			
		$GLOBALS['tmpl']->assign("is_delivery",$is_delivery);
			
		$is_coupon = 0;
		foreach($cart_list as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select is_coupon from ".DB_PREFIX."deal where id = ".$v['deal_id']." and forbid_sms = 0")==1)
			{
				$is_coupon = 1;
				break;
			}
		}
		$GLOBALS['tmpl']->assign("is_coupon",$is_coupon);
		$GLOBALS['tmpl']->assign("coupon_name",app_conf("COUPON_NAME"));
			
		//查询总金额
		$delivery_count = 0;
		foreach($cart_list as $k=>$v)
		{
			if($v['is_delivery']==1)
			{
				$delivery_count++;
			}
		}
		if($total_price > 0 || $delivery_count > 0)
			$GLOBALS['tmpl']->assign("show_payment",true);
		
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);
		
		
		//关于短信发送的条件
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());

		//购物车检测页
		$GLOBALS['tmpl']->display("cart_check.html");
		//exit;
	}
	
	
	
	//购物车订单提交
	public function done()
	{
		require_once(APP_ROOT_PATH."system/model/cart.php");
		require_once(APP_ROOT_PATH."system/model/deal.php");
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		global_run();
		$ajax = 1;
		//配送验证
		//if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
		//{
		//	showErr($GLOBALS['lang']['SUPPLIER_LOGIN_FIRST'],$ajax);
		//}
		//if (empty($GLOBALS['user_info']['mobile'])) {
		//	showErr($GLOBALS['lang']['FILL_MOBILE_PHONE'],$ajax);
		//}
		$address_id = intval($_REQUEST['address_id']);
		$location_id = intval($_REQUEST['location_id']);
		if($location_id>0){
			$address_id=0;
		}
		$consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$address_id));
		
		$region4_id = intval($consignee_info['consignee_info']['region_lv4']);
		$region3_id = intval($consignee_info['consignee_info']['region_lv3']);
		$region2_id = intval($consignee_info['consignee_info']['region_lv2']);
		$region1_id = intval($consignee_info['consignee_info']['region_lv1']);
		
		if ($region4_id==0)
		{
			if ($region3_id==0)
			{
				if ($region2_id==0)
				{
					$region_id = $region1_id;
				}
				else
					$region_id = $region2_id;
			}
			else
				$region_id = $region3_id;
		}
		else
			$region_id = $region4_id;
	
		$delivery_id = 0;//intval($_REQUEST['delivery']);
		$payment = intval($_REQUEST['payment']);
		$account_money = floatval($_REQUEST['account_money']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		$ecvsn = $_REQUEST['ecvsn']?strim($_REQUEST['ecvsn']):'';
		$ecvpassword = $_REQUEST['ecvpassword']?strim($_REQUEST['ecvpassword']):'';
		$content = $_REQUEST['memo'];
		$youhui_ids = $_REQUEST['youhui_log_id'];
		
		
		if(count($content)==1){
			$memo = end($content);
		}
		
		$supplier_data = array();
		if($content){
			foreach($content as $k=>$v){
				$supplier_data[$k]['memo'] = $v;
			}
		}

		// 发票的数据处理
		$invoice_types = $_REQUEST['invoice_type'];
		$invoice_titles = $_REQUEST['invoice_title'];
		$invoice_persons = $_REQUEST['invoice_person'];
		$invoice_taxnus = $_REQUEST['invoice_taxnu'];
		$invoice_contents = $_REQUEST['invoice_content'];
		$firstInvoice = '';
		if ($invoice_types) {
			$invIndex = 0;
			foreach ($invoice_types as $key => $value) {
				$value = intval($value);
				if ($value !== 0) {
					$invoices['type'] = $value;
					$invoices['title'] = intval($invoice_titles[$key]);
					$invoices['persons'] = strim($invoice_persons[$key]);
					if ($invoices['title'] == 1) {
						$invoices['taxnu'] = strim($invoice_taxnus[$key]);
					}
					$invoices['content'] = strim($invoice_contents[$key]);
					$seriInv = serialize($invoices);
					$supplier_data[$key]['invoice_info'] = $seriInv;
					if ($invIndex === 0) {
						$firstInvoice = $seriInv;
					}
					$invIndex++;
				}
			}
		}

		
		$id = $_REQUEST['deal_id']?intval($_REQUEST['deal_id']):0;
		$user_id = intval($GLOBALS['user_info']['id']);
		$session_id = es_session::id();
		if($id > 0){
		    $cart_result = load_cart_list($id);
		}else{
		    $cart_result = load_cart_list($id=0,false);
		}
		$goods_list = $cart_result['cart_list'];
	
		if(!$goods_list)
		{
			showErr($GLOBALS['lang']['CART_EMPTY_TIP'],$ajax);
		}
	
		//验证购物车
		if((check_save_login()!=LOGIN_STATUS_LOGINED&&$GLOBALS['user_info']['money']>0)||check_save_login()==LOGIN_STATUS_NOLOGIN)
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax,url("index","user#login"));
		}
		$supplier=array();
		$has_wuliu=0;//是否有物流配送
		$has_yizhan=0; //是否有驿站配送
		$has_stuan=0;  //是否有商家团购商品
		$has_sshop=0;  //是否有商家商城商品
		$deal_ids = array();
		foreach($goods_list as $k=>$v)
		{
			$data = check_cart($v['id'], $v['number']);
			if(!$data['status'])
				showErr($data['info'],$ajax,url("index","cart#index"));
			$deal_ids[$v['deal_id']]['deal_id'] = $v['deal_id']; 
			
			if(!in_array($v['supplier_id'], $supplier)){
			    $supplier[]=$v['supplier_id'];
			}
			$order_deal = $GLOBALS['db']->getRow("select delivery_type,id,is_shop from ".DB_PREFIX."deal where id=".$v['deal_id']);
			if($v['supplier_id']==0){  //平台自营，平台自营商品物流配送和驿站配送，都要拆单
			    
			    if($order_deal['delivery_type']==1){
			       $has_wuliu=1; 
			    }elseif($order_deal['delivery_type']==3){
			       $has_yizhan=1; 
			    }elseif($order_deal['delivery_type']==2){  //平台无需配送商品
			       $has_nodlivery=1; 
			    }
			}else{
				if($order_deal['is_shop']==0){  //团购
					$has_stuan=1;
				}else{//商城商品
					$has_sshop=1;
				}
			}
		}
		//判断该订单是否需要拆单，$is_main，是否是订单的主单，1为订单的主单,则需要进行拆单，0为订单的子单
		$is_main=0;
		if(count($supplier)>1 ||( $has_wuliu==1 && $has_yizhan==1)){ //多个普通商家和平台自营商品物流配送和驿站配送，都要拆单
		    $is_main=1;
		}
		foreach($deal_ids as $row)
		{	
			//验证支付方式的支持
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$row['deal_id'])==1)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_payment where deal_id = ".$row['deal_id']." and payment_id = ".$payment))
				{
					showErr($GLOBALS['lang']['INVALID_PAYMENT'],$ajax,url("index","cart#index"));
				}
			}
		}
					
			
		//结束验证购物车
		//开始验证订单接交信息
		$data = count_buy_total($region_id,$address_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword,$goods_list,0,0,0,$all_score=array(),0,$youhui_ids,0);

		if(!$consignee_info['consignee_info'] && $location_id==0 && ( $data['is_delivery']==1 || $data['is_pick']==1))
		{
			showErr('请设置收货地址',$ajax);
		}
		if(round($data['pay_price'],4)>0&&!$data['payment_info'])
		{
			showErr($GLOBALS['lang']['PLEASE_SELECT_PAYMENT'],$ajax);
		}
		//结束验证订单接交信息
		
		if(count($supplier)==1){  //订单只有单个商家时，保存商家ID
			$order['supplier_id']=end($supplier);
		}else{
			$has_stuan=0;  //是否有商家团购商品
			$has_sshop=0;  //是否有商家商城商品
			$has_wuliu=0;  //平台自营物流配送订单
			$has_yizhan=0; //平台自营驿站配送订单
		}
		//开始生成订单
		$now = NOW_TIME;
		$type=0;
		if($data['return_total_score'] < 0){
			$type =2;  //积分兑换订单
		}elseif($has_stuan==1){
		    $type=5;   //商家团购订单
		}elseif($has_sshop==1){
		    $type=6;   //商家商品订单
		}elseif(($has_wuliu==1 && $has_yizhan==0)||$has_nodlivery==1){
		    $type=3;  //平台自营物流配送订单
		}elseif($has_wuliu==0 && $has_yizhan==1){
		    $type=4;  //平台自营驿站配送订单
		}
		
		$delivery_fee = 0;
		if($address_id > 0){
			$delivery_list = get_express_fee($cart_result['cart_list'],$address_id);
			if($delivery_list){
				foreach($delivery_list as $k=>$v){
					$supplier_data[$k]['delivery_fee'] = $v['total_fee'];
		
					$delivery_fee += $v['total_fee'];
				}
			}
		}
		//订单分配代理商id
		if($is_main==1){//需要拆单的订单
			foreach($supplier_data as $k=>$v){
				if($k=="p_wl"){//物流
					$supplier_data[$k]['agency_id']=0;
				}elseif($k=="p_yz"){//驿站
					if($consignee_info['consignee_info']){
						if($consignee_info['consignee_info']['region_lv3_code']){
							$agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code = '".$consignee_info['consignee_info']['region_lv3_code']."'");
							$supplier_data[$k]['agency_id']=intval($agency_id);
						}
					}
				}else{//商户
					$agency_id=$GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id = '".$k."'");
					$supplier_data[$k]['agency_id']=intval($agency_id);
				}
				$supplier_data[$k]['youhui_data'] = $data['youhui_data'][$k];
			}
			$order['youhui_money']=$data['youhui_money'];
		}else{//不需要拆单的订单
			if($order['supplier_id']>0){//存在商户的订单
				 $agency_id= $GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id = ".$order['supplier_id']);
				 $order['agency_id']=intval($agency_id);
			}elseif($type==4){//驿站订单
				if($consignee_info['consignee_info']){
					if($consignee_info['consignee_info']['region_lv3_code']){
						$agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code = '".$consignee_info['consignee_info']['region_lv3_code']."'");
						$order['agency_id']=intval($agency_id);
					}
				}
			}
			$youhui_data = end($data['youhui_data']);
			$order['youhui_money']=$youhui_data['youhui_money'];
			$order['youhui_log_id']=$youhui_data['youhui_log_id'];
		}
		$order['type'] = $type; //普通订单
		$order['user_id'] = $user_id;
		$order['create_time'] = $now;
		$order['total_price'] = $data['pay_total_price'];  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费

		$order['pay_amount'] = 0;
		$order['pay_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
		$order['delivery_status'] = $data['is_consignment']==0?5:0;
		$order['order_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
		$order['return_total_score'] = $data['return_total_score'];  //结单后送的积分
		$order['return_total_money'] = $data['return_total_money'];  //结单后送的现金
		$order['memo'] = $memo;//strim($_REQUEST['memo']);
		$order['region_lv1'] = $region1_id;
		$order['region_lv2'] = $region2_id;
		$order['region_lv3'] = $region3_id;
		$order['region_lv4'] = $region4_id;
		$order['address']	=	$consignee_info['consignee_info']['address'];//strim($_REQUEST['address']);
		$order['mobile']	=	$consignee_info['consignee_info']['mobile'];//strim($_REQUEST['mobile']);
		$order['consignee']	=	$consignee_info['consignee_info']['consignee'];
		$order['street']	=	$consignee_info['consignee_info']['street'];
		$order['doorplate']	=	$consignee_info['consignee_info']['doorplate'];//strim($_REQUEST['consignee']);
		$order['zip']	=	$consignee_info['consignee_info']['zip'];
		$order['consignee_id']	=	$consignee_info['consignee_info']['id'];
		$order['deal_total_price'] = $data['total_price'];   //团购商品总价
		$order['discount_price'] = $data['user_discount'];
		$order['delivery_fee'] = $delivery_fee;
		$order['record_delivery_fee'] = $delivery_fee;
		$order['ecv_money'] = 0;
		$order['account_money'] = 0;
		$order['ecv_sn'] = '';
		$order['delivery_id'] = 0;
		$order['payment_id'] = $data['payment_info']['id'];
		$order['payment_fee'] = $data['payment_fee'];
		$order['bank_id'] = strim($_REQUEST['bank_id']);
		$order['is_main'] = $is_main;
		$order['location_id'] = $location_id;
		$order['supplier_data'] = serialize($supplier_data);
		$order['invoice_info'] = $firstInvoice;
		$order['is_all_balance'] = 0;
		foreach($data['promote_description'] as $promote_item)
		{
			$order['promote_description'].=$promote_item."<br />";
		}
		$order['promote_arr']=serialize($data['promote_arr']);
		//更新来路
		$order['referer'] =	$GLOBALS['referer'];
		$user_info = es_session::get("user_info");
		$order['user_name'] = $user_info['user_name'];
		if($is_main==0&&($type==5||$type==6)&&defined("FX_LEVEL")){
			$ref_salary_conf = unserialize(app_conf("REF_SALARY"));
			$ref_salary_switch=intval($ref_salary_conf['ref_salary_switch']);
			if($ref_salary_switch==1){//判断后台是否开启推荐商家入驻三级分销
				$order['is_participate_ref_salary'] = 1;
			}
		}
		/** 更新会员手机号
		$coupon_mobile = htmlspecialchars(addslashes(trim($_REQUEST['coupon_mobile'])));
		if($coupon_mobile!='')
			$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$coupon_mobile."' where id = ".intval($user_info['id']));
		*/
		
		/*if($user_info['mobile']=="")
		{			
			$user_mobile = strim($_REQUEST['user_mobile']);
			
			if($user_mobile=="")
			{
				$data = array();
				$data['status'] = false;
				$data['info']	=  "请输入手机号";
				$data['jump']  = "";
				ajax_return($data);
			}
			
			if(!check_mobile($user_mobile))
			{
				$data = array();
				$data['status'] = false;
				$data['info']	=  "手机号格式不正确";
				$data['jump']  = "";
				ajax_return($data);
			}
			
			if(app_conf("SMS_ON")==1)
			{
				$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
				$sms_verify = strim($_POST['sms_verify']);
				if(empty($mobile_data)||$mobile_data['code']!=$sms_verify)
				{
					$data = array();
					$data['status'] = false;
					$data['info']	=  "手机验证码错误";
					$data['jump']  = "";
					ajax_return($data);
				}
			}
			
			$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$user_mobile."' where id = ".$user_info['id'],"SILENT");
			if($GLOBALS['db']->affected_rows()>0)
			{
				$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'"); //删除验证码
			}
			else
			{
				$data = array();
				$data['status'] = false;
				$data['info']	=  "手机号已被注册";
				$data['jump']  = "";
				ajax_return($data);
			}
		}
		
		check_form_verify();*/
		
		do
		{
			$order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT');
			$order_id = intval($GLOBALS['db']->insert_id());

		}while($order_id==0);
	
		//生成商户的运费记录
//		$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_order_supplier_fee where order_id = ".$order_id);
//		foreach($data['delivery_fee_supplier'] as $key=>$fee)
//		{			
//				$sp_id = str_replace("sid_","",$key);			
//				$fee_data = array();
//				$fee_data['order_id'] = $order_id;
//				$fee_data['supplier_id'] = $sp_id;
//				$fee_data['delivery_fee'] = $fee;
//				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_supplier_fee",$fee_data);
			
//		}

		//先计算用户等级折扣
		$user_id = intval($GLOBALS['user_info']['id']);
		$user_discount_percent = 1;
		if ($user_id) {
			$user_discount_percent = getUserDiscount($user_id);
		}
		
		//生成订单商品
		foreach($goods_list as $k=>$v)
		{
			$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
			$goods_item = array();
			
			//关于fx
			if($deal_info['is_fx'])
			{
				/*$fx_user_id = intval($GLOBALS['ref_uid']);
				if($fx_user_id)
				{
					$user_deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_deal where deal_id = '".$deal_info['id']."' and user_id = '".$fx_user_id."'");
					if($user_deal||$deal_info['is_fx'])
						$goods_item['fx_user_id'] =  $fx_user_id;
				}*/
				// 更改为推荐注册人
				$fx_user_id = intval($GLOBALS['user_info']['pid']);
				if ($fx_user_id) {
					$goods_item['fx_user_id'] =  $fx_user_id;
				}
			}
			//关于fx
			
			$goods_item['deal_id'] = $v['deal_id'];
			$goods_item['number'] = $v['number'];
			$unit_price = $v['unit_price'];
			if ($v['allow_user_discount']) {
				$unit_price = round($unit_price * $user_discount_percent, 2);
			}
			$goods_item['unit_price'] = $v['unit_price'];
			$goods_item['total_price'] = $v['total_price'];
			$goods_item['discount_unit_price'] = $unit_price;  //商品折扣后单价
			$goods_item['name'] = $v['name'];
			$goods_item['sub_name'] = $v['sub_name'];
			$goods_item['attr'] = $v['attr'];
			$goods_item['verify_code'] = $v['verify_code'];
			$goods_item['order_id'] = $order_id;
			$goods_item['return_score'] = $v['return_score'];
			$goods_item['return_total_score'] = $v['return_total_score'];
			$goods_item['return_money'] = $v['return_money'];
			$goods_item['return_total_money'] = $v['return_total_money'];
			$goods_item['buy_type']	=	$v['buy_type'];
			$goods_item['attr_str']	=	$v['attr_str'];
			$goods_item['add_balance_price'] = $v['add_balance_price'];
			$goods_item['add_balance_price_total'] = $v['add_balance_price'] * $v['number'];
			$goods_item['balance_unit_price'] = $deal_info['balance_price'];
			$goods_item['balance_total_price'] = $deal_info['balance_price'] * $v['number'];
			
			$goods_item['deal_icon'] = $deal_info['icon'];

			$goods_item['supplier_id'] = $deal_info['supplier_id'];
			$goods_item['is_refund'] = $deal_info['is_refund'];
			$goods_item['user_id'] = $user_id;
			$goods_item['order_sn'] = $order['order_sn'];
			$goods_item['is_shop'] = $deal_info['is_shop'];
			$goods_item['delivery_type'] = $deal_info['delivery_type'];
			
			
			if($is_main==1){ //如果是需要拆单的主单
			    // $supplier_data
			    if($goods_item['supplier_id']==0){
			        if($goods_item['delivery_type']==1){  //物流配送
			            $supplier_id = 'p_wl';
			        }elseif($goods_item['delivery_type']==2){  //无需配送
			            $supplier_id = 0;
			        }elseif($goods_item['delivery_type']==3){  //驿站配送
			            $supplier_id = 'p_yz';
			        }
			    }else{
			        $supplier_id = $goods_item['supplier_id'];
			    }
			    $youhui_money = $supplier_data[$supplier_id]['youhui_data']['youhui_money'];
			    $total_price = $supplier_data[$supplier_id]['youhui_data']['total_price'];
			    $origin_total_price = $supplier_data[$supplier_id]['youhui_data']['origin_total_price'];
			     
			}else{
			    $youhui_money = $order['youhui_money'];
			    $total_price = $order['total_price'];
			    $origin_total_price = $order['deal_total_price'];
			}
			
			if($youhui_money >= $total_price){
			    $goods_item['add_balance_price'] = 0;
			    $goods_item['add_balance_price_total'] = 0;
			    $goods_item['balance_unit_price'] = 0;
			    $goods_item['balance_total_price'] = 0;
			}else{
			    $rate = $goods_item['total_price'] / $origin_total_price;
			    $youhui_money = $youhui_money * $rate;
			
			    if($youhui_money >= $goods_item['add_balance_price_total'] + $goods_item['balance_total_price']){
			        $goods_item['add_balance_price'] = 0;
			        $goods_item['add_balance_price_total'] = 0;
			        $goods_item['balance_unit_price'] = 0;
			        $goods_item['balance_total_price'] = 0;
			    }else{
			        $rate2 = $goods_item['add_balance_price_total'] /$goods_item['balance_total_price'] + $goods_item['add_balance_price_total'];
			        $rate3 = $goods_item['balance_total_price'] /$goods_item['balance_total_price'] + $goods_item['add_balance_price_total'];
			        $goods_item['add_balance_price_total'] -= $youhui_money * $rate2 ;
			        $goods_item['add_balance_price'] = $goods_item['add_balance_price_total'] / $v['number'] ;
			        $goods_item['balance_total_price'] -= $youhui_money * $rate3 ;
			        $goods_item['balance_unit_price'] = $goods_item['balance_total_price'] / $v['number'] ;
			    }
			}
				
			
			$deal_data = array();
			$deal_data['dist_service_rate'] = $deal_info['dist_service_rate'];
			$deal_data['recommend_user_id'] = $deal_info['recommend_user_id'];
			$deal_data['recommend_user_return_ratio'] = $deal_info['recommend_user_return_ratio'];
			$goods_item['deal_data'] = serialize($deal_data);
			
			
			$goods_item['distribution_fee'] = ($goods_item['total_price'] - $goods_item['balance_total_price'] - $goods_item['add_balance_price_total'])*$deal_info['dist_service_rate']/100;
						
			if($location_id > 0){
				$goods_item['is_pick'] = 1;
			}else{
				$goods_item['is_pick'] = 0;
			}
			$goods_item['is_coupon'] =  $goods_item['is_pick']==1?1:$deal_info['is_coupon'];
			$goods_item['is_delivery'] = $goods_item['is_pick']==1?0:$deal_info['is_delivery'];
			
			//$goods_item['delivery_status'] = $data['is_delivery']==1?0:5;
			//if($data['is_pick']==0 && $data['is_consignment']==1 && $deal_info['is_shop']==1){
			//    $goods_item['delivery_status'] = 0;
			//}else {
			//    $goods_item['delivery_status'] = 5;
			//}
			$goods_item['delivery_status'] = $data['is_consignment']==0?5:0;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_item",$goods_item,'INSERT','','SILENT');
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where id = '".$v['id']."'");
		}
	
		//开始更新订单表的deal_ids
		$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set deal_ids = '".$deal_ids."' where id = ".$order_id);
	
		//$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where session_id = '".$session_id."'");
	
		/*if($data['is_delivery']==1)
		{
			//保存收款人
			$consignee_id = intval($_REQUEST['consignee_id']);
			$user_consignee = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where user_id = ".$order['user_id']." and id = ".$consignee_id);
			$user_consignee['region_lv1'] = intval($_REQUEST['region_lv1']);
			$user_consignee['region_lv2'] = intval($_REQUEST['region_lv2']);
			$user_consignee['region_lv3'] = intval($_REQUEST['region_lv3']);
			$user_consignee['region_lv4'] = intval($_REQUEST['region_lv4']);
			$user_consignee['address']	=	strim($_REQUEST['address']);
			$user_consignee['mobile']	=	strim($_REQUEST['mobile']);
			$user_consignee['consignee']	=	strim($_REQUEST['consignee']);
			$user_consignee['zip']	=	strim($_REQUEST['zip']);
			$user_consignee['user_id']	=	$order['user_id'];
			if(intval($user_consignee['id'])==0)
			{
				//新增
				$user_consignee['is_default'] = 1;
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$user_consignee,'INSERT','','SILENT');
			}
			else
			{
				//更新
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$user_consignee,'UPDATE','id='.$user_consignee['id'],'SILENT');
				rm_auto_cache("consignee_info",array("consignee_id"=>intval($user_consignee['id'])));
			}
			
		}*/
	
		if($data['youhui_data']){
		    require_once(APP_ROOT_PATH."system/model/biz_verify.php");
		    foreach($data['youhui_data'] as $k=>$youhui){
		        online_youhui_use($youhui['youhui_log_id']);
		    }
		}
		
	
		//生成order_id 后
		//1. 代金券支付
		$ecv_data = $data['ecv_data'];
		if($ecv_data)
		{
			$ecv_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Voucher'");
			if($ecv_data['money']>$order['total_price'])$ecv_data['money'] = $order['total_price'];
			$payment_notice_id = make_payment_notice($ecv_data['money'],$order_id,$ecv_payment_id,"",$ecv_data['id']);
			require_once(APP_ROOT_PATH."system/payment/Voucher_payment.php");
			$voucher_payment = new Voucher_payment();
			$voucher_payment->direct_pay($ecv_data['sn'],$ecv_data['password'],$payment_notice_id);
		}
	
		//2. 余额支付
		$account_money = $data['account_money'];
		if(floatval($account_money) > 0)
		{
			$account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
			$payment_notice_id = make_payment_notice($account_money,$order_id,$account_payment_id);
			require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
			$account_payment = new Account_payment();
			$account_payment->get_payment_code($payment_notice_id);
		}
	
		//3. 相应的支付接口
		$payment_info = $data['payment_info'];
		if($payment_info&&$data['pay_price']>0)
		{
			$payment_notice_id = make_payment_notice($data['pay_price'],$order_id,$payment_info['id']);
			//创建支付接口的付款单
		}
		if($is_main==1){ //如果是需要拆单的主单，则进行拆单
		    syn_order($order_id);
		}
		$rs = order_paid($order_id);
		update_order_cache($order_id);
		if($rs)
		{
			$data = array();
			$data['info'] = "";
			$data['jump'] = url("index","payment#done",array("id"=>$order_id));
		}
		else
		{
			distribute_order($order_id);
			$data = array();
			$data['info'] = "";
			$data['jump'] = url("index","payment#pay",array("id"=>$payment_notice_id));
		}
		
		ajax_return($data); //支付成功
	}
	
	
	
	public function order()
	{
			
		global_run();
		assign_form_verify();
		init_app_page();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}

		
		$id = intval($_REQUEST['id']);
		$order_status = check_order($id);
		
		if(!$order_status){
			showErr("非法数据",0,url("index","uc_order"));
		}
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and pay_status <> 2 and order_status <> 1 and user_id =".intval($GLOBALS['user_info']['id']));
		if(!$order_info)
		{
			app_redirect(url("index"));
		}
		if($order_info['type']==1)
		{
			app_redirect(url("index","uc_money#incharge"));
		}

		
		if ($order_info['invoice_info']) {
			$invoice = unserialize($order_info['invoice_info']);
			if ($invoice['type'] == 1) {
				$invoices = '普通发票: ';
				$invoices .= ' ('.$invoice['content'].') ';
				$invoices .= '- '.$invoice['persons'];
				if (!empty($invoice['taxnu'])) {
					$invoices .= ' 纳税人识别号:'.$invoice['taxnu'];
				}
				
			} else {
				$invoices = '不开发票';
			}
			$order_info['invoice_info'] = $invoices;
			// print_r($invoice);exit;
		}
		$supplier_data = unserialize($order_info['supplier_data']);

		$GLOBALS['tmpl']->assign('order_info',$order_info);
		$cart_list = $GLOBALS['db']->getAll("select doi.*,d.id as did,d.icon,d.uname as duname from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where doi.order_id = ".$order_info['id']);
		//echo "<pre>";print_r($cart_list);exit;
		$group_list=array();
		foreach($cart_list as $k=>$v){
			$format=format_price_html(round($v['discount_unit_price'],2),2);
			$v['unit_price_format']=$format['bai'].'.'.$format['fei'];
			$format=format_price_html(round($v['discount_unit_price'] * $v['number'],2),2);
			$v['total_price_format']=$format['bai'].'.'.$format['fei'];
			if($v['supplier_id']==0){
				$delivery_type = $GLOBALS['db']->getOne("select delivery_type from ".DB_PREFIX."deal where id=".$v['deal_id']);
				if($delivery_type==1){ //平台物流配送商品
					$group_list['p_wl']['goods_list'][$v['id']]=$v;
					$group_list['p_wl']['supplier']="平台自营";
				}elseif($delivery_type==3){ //平台驿站配送商品
					$group_list['p_yz']['goods_list'][$v['id']]=$v;
					$group_list['p_yz']['supplier']="平台自营 - 驿站配送";
				}else{ //平台无需配送商品，直接进入订单提交页面，不进入购物车页面
					$group_list[$v['supplier_id']]['goods_list'][$v['id']]=$v;
					$group_list[$v['supplier_id']]['supplier']="平台自营";
				}
			}else{
				$group_list[$v['supplier_id']]['goods_list'][$v['id']] = $v;
				if(!$group_list[$v['supplier_id']]['supplier'])
					$group_list[$v['supplier_id']]['supplier']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = '".$v['supplier_id']."'");
			}
		}
		//echo "<pre>";print_r($group_list);exit;
		/*$cart_list_group = cart_list_group($cart_list);
		
		foreach($cart_list_group as $k=>$v)
		{
			$cart_list_group[$k]['supplier'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = '".$v['supplier_id']."'");
			$cart_list_group[$k]['supplier'] = $cart_list_group[$k]['supplier']?$cart_list_group[$k]['supplier']:app_conf("SHOP_TITLE")."直营";
		}*/
		if(!$cart_list)
		{
			app_redirect(url("index"));
		}
		/*else
		{
			foreach($cart_list as $k=>$v)
			{
				$bind_data = array();
				$bind_data['id'] = $v['id'];
				if($v['buy_type']==1)
				{
					$cart_list[$k]['unit_price'] = abs($v['return_score']);
					$cart_list[$k]['total_price'] = abs($v['return_total_score']);
				}
					
				if($v['duname']!="")
					$cart_list[$k]['url'] = url("index","deal#".$v['duname']);
				else
					$cart_list[$k]['url'] = url("index","deal#".$v['did']);
			}
		}*/
		if($order_info['is_main']==1){
			$supplier_data_arr=unserialize($order_info['supplier_data']);
			foreach ($group_list as $k=>$v){
				$group_list[$k]['delivery_fee']['total_fee']=floatval($supplier_data_arr[$k]['delivery_fee']);
				$group_list[$k]['youhui_money'] = $supplier_data[$k]['youhui_data']['youhui_money'];
			}
		}else{
			foreach ($group_list as $k=>$v){
				$group_list[$k]['delivery_fee']['total_fee']=floatval($order_info['delivery_fee']);
				$group_list[$k]['youhui_money'] = $order_info['youhui_money'];
			}
		}

		//输出购物车内容
		$GLOBALS['tmpl']->assign("cart_list",$cart_list);
		$GLOBALS['tmpl']->assign("cart_list_group",$group_list);
		$GLOBALS['tmpl']->assign('total_price',$order_info['deal_total_price']);
		//echo "<pre>";print_r($order_info);exit;
		
		$is_delivery = 0;
		foreach($cart_list as $k=>$v)
		{
			if($v['is_delivery']==1)
			{
				$is_delivery = 1;
				break;
			}
		}
	
		if($is_delivery)
		{
			if($order_info['consignee_id']!=0){
				$consignee_list['0']=load_auto_cache("consignee_info",array("consignee_id"=>intval($order_info['consignee_id'])));
				$GLOBALS['tmpl']->assign("consignee_list",$consignee_list);
				$GLOBALS['tmpl']->assign("consignee_id",$consignee_list['0']['consignee_info']['id']);
				$GLOBALS['tmpl']->assign("region_id",$consignee_list['0']['consignee_info']['region_lv4']);
			}
			if($order_info['location_id']!=0){
				$location = $GLOBALS['db']->getAll("select id,name,address,tel from ".DB_PREFIX."supplier_location where id=".$order_info['location_id']);
				$GLOBALS['tmpl']->assign("location",$location);
			}
		}
		$GLOBALS['tmpl']->assign("is_delivery",$is_delivery);
		//配送方式由ajax由 consignee 中的地区动态获取
	
		//输出支付方式
		$payment_list = load_auto_cache("cache_payment");
	
		foreach($cart_list as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
			{
				$define_payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$v['deal_id']);
				$define_payment = array();
				foreach($define_payment_list as $kk=>$vv)
				{
					array_push($define_payment,$vv['payment_id']);
				}
				foreach($payment_list as $k=>$v)
				{
					if(in_array($v['id'],$define_payment))
					{
						unset($payment_list[$k]);
					}
				}

			}
		}
		$icon_paylist = array(); //用图标展示的支付方式
		$disp_paylist = array(); //特殊的支付方式(Voucher,Account,Otherpay)
		$bank_paylist = array(); //网银直连
		
		$wx_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Wwxjspay'");
		if($wx_payment)
		{
			$wx_payment['config'] = unserialize($wx_payment['config']);
			if($wx_payment['config']['scan']==1)
			{
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' .$wx_payment['class_name']."_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					$payment_class = $wx_payment['class_name']."_payment";
					$payment_object = new $payment_class();
					$wx_payment['display_code'] = $payment_object->get_web_display_code();
					$disp_paylist[] = $wx_payment;
				}
			}
		}
		
		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=="Voucher"||$v['class_name']=="Account"||$v['class_name']=="Otherpay")
			{
				if($v['class_name']=="Account")
				{
					if($GLOBALS['user_info']['money']<0.01){
						continue;
					}
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();
					}
				}
				
				if($v['class_name']=="Account"||$v['class_name']=="Otherpay") //代金券在订单修改时不再允许支付
				$disp_paylist[] = $v;
			}
			else
			{
				if($v['is_bank']==1)
					$bank_paylist[] = $v;
				else
					$icon_paylist[] = $v;
			}
		}
		
		$GLOBALS['tmpl']->assign("icon_paylist",$icon_paylist);
		$GLOBALS['tmpl']->assign("disp_paylist",$disp_paylist);
		$GLOBALS['tmpl']->assign("bank_paylist",$bank_paylist);
		
	
		$GLOBALS['tmpl']->assign("is_delivery",$is_delivery);
	
		$is_coupon = 0;
		foreach($cart_list as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select is_coupon from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
			{
				$is_coupon = 1;
				break;
			}
		}
		$GLOBALS['tmpl']->assign("is_coupon",$is_coupon);
		$GLOBALS['tmpl']->assign("coupon_name",app_conf("COUPON_NAME"));
	
		$GLOBALS['tmpl']->assign("show_payment",true);
		
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);
		
		
		//关于短信发送的条件
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());
		
		//购物车检测页
		$GLOBALS['tmpl']->display("cart_check.html");
	
	}
	
	public function order_done()
	{
		require_once(APP_ROOT_PATH."system/model/deal.php");
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		global_run();
		$ajax = 1;
		//验证购物车
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax,url("index","user#login"));
		}
		$user_info = $GLOBALS['user_info'];
		$id = intval($_REQUEST['id']); //订单号
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and user_id = ".$user_info['id']);
		if(!$order)
		{
			showErr($GLOBALS['lang']['INVALID_ORDER_DATA'],$ajax);
		}
			
		if($order['refund_status'] == 1)
		{
			showErr($GLOBALS['lang']['REFUNDING_CANNOT_PAY'],$ajax);
		}
		if($order['refund_status'] == 2)
		{
			showErr($GLOBALS['lang']['REFUNDED_CANNOT_PAY'],$ajax);
		}
		//$region4_id = intval($_REQUEST['region_lv4']);
		//$region3_id = intval($_REQUEST['region_lv3']);
		//$region2_id = intval($_REQUEST['region_lv2']);
		//$region1_id = intval($_REQUEST['region_lv1']);
	
		/*if ($region4_id==0)
		{
			if ($region3_id==0)
			{
				if ($region2_id==0)
				{
					$region_id = $region1_id;
				}
				else
					$region_id = $region2_id;
			}
			else
				$region_id = $region3_id;
		}
		else
			$region_id = $region4_id;*/
		$region_id=$order['region_lv4'];
		//$delivery_id = intval($_REQUEST['delivery']);
		$payment = intval($_REQUEST['payment']);
		$account_money = floatval($_REQUEST['account_money']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		//$ecvsn = $_REQUEST['ecvsn']?strim($_REQUEST['ecvsn']):'';
		//$ecvpassword = $_REQUEST['ecvpassword']?strim($_REQUEST['ecvpassword']):'';

		$goods_list = $GLOBALS['db']->getAll("select doi.* , d.allow_user_discount from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id=d.id where doi.order_id = ".$order['id']);
		
		
		//验证支付方式的支持
		foreach($goods_list as $k=>$row)
		{
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$row['deal_id'])==1)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_payment where deal_id = ".$row['deal_id']." and payment_id = ".$payment))
				{
					showErr($GLOBALS['lang']['INVALID_PAYMENT'],$ajax);
				}
			}
		}
		//结束验证购物车
		$deal_s = $GLOBALS['db']->getAll("select distinct(deal_id) as deal_id , number from ".DB_PREFIX."deal_order_item where order_id = ".$order['id']);
	
		//如果属于未支付的
		if($order['pay_status'] == 0)
		{				
			foreach($deal_s as $row)
			{	
				$checker = check_deal_number($row['deal_id'],$row['number']);
				if($checker['status']==0)
				{
					showErr($checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']],$ajax);
						
				}
			}
			
			foreach($goods_list as $k=>$v)
			{
				$checker = check_deal_number_attr($v['deal_id'],$v['attr_str'],$v['number']);
				if($checker['status']==0)
				{
					showErr($checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']],$ajax);				
				}
			}
			
			//验证商品是否过期
			foreach($deal_s as $row)
			{			
				$checker = check_deal_time($row['deal_id']);
				if($checker['status']==0)
				{
					showErr($checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']],$ajax);
				}
			}
		}
	
		
	
		//开始验证订单接交信息
		require_once(APP_ROOT_PATH."system/model/cart.php");
		$data = count_buy_total($region_id,$order['consignee_id'],$payment,$account_money,$all_account_money,$ecvsn,$ecvpassword,$goods_list,$order['account_money'],$order['ecv_money'],'',0,$order['exchange_money'],array(),$order['youhui_money']);
	
		if($data['is_delivery'] == 1)
		{
			
		}
	
		if(round($data['pay_price'],4)>0&&!$data['payment_info'])
		{
			showErr($GLOBALS['lang']['PLEASE_SELECT_PAYMENT'],$ajax);
		}
		//结束验证订单接交信息
		check_form_verify();
		//开始修正订单
		$now = NOW_TIME;
		$order['total_price'] = $order['total_price']-$order['payment_fee']+$data['payment_fee'];//$data['pay_total_price'];  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
		//$order['memo'] = strim($_REQUEST['memo']);
		//$order['region_lv1'] = intval($_REQUEST['region_lv1']);
		//$order['region_lv2'] = intval($_REQUEST['region_lv2']);
		//$order['region_lv3'] = intval($_REQUEST['region_lv3']);
		//$order['region_lv4'] = intval($_REQUEST['region_lv4']);
		//$order['address']	=	strim($_REQUEST['address']);
		//$order['mobile']	=	strim($_REQUEST['mobile']);
		//$order['consignee']	=	strim($_REQUEST['consignee']);
		//$order['zip']	=	strim($_REQUEST['zip']);
		//$order['delivery_fee'] = $data['delivery_fee'];
		$order['delivery_id'] = $data['delivery_info']['id'];
		$order['payment_id'] = $data['payment_info']['id'];
		$order['payment_fee'] = $data['payment_fee'];
		//$order['record_delivery_fee'] = $data['record_delivery_fee'];
		$order['discount_price'] = $data['user_discount'];
		$order['bank_id'] = strim($_REQUEST['bank_id']);

		// 生成订单时已经写入了促销信息。这里重复了
		//$order['promote_description'] = "";
		//foreach($data['promote_description'] as $promote_item)
		//{
		//	$order['promote_description'].=$promote_item."<br />";
		//}
		$order['promote_arr']=serialize($data['promote_arr']);
	
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'UPDATE','id='.$order['id'],'SILENT');
	
	
	
		//生成order_id 后
		//1. 余额支付
		$account_money = $data['account_money'];
		if(floatval($account_money) > 0)
		{
			$account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
			$payment_notice_id = make_payment_notice($account_money,$order['id'],$account_payment_id);
			require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
			$account_payment = new Account_payment();
			$account_payment->get_payment_code($payment_notice_id);
		}
	
		//3. 相应的支付接口
		$payment_info = $data['payment_info'];
		if($payment_info&&$data['pay_price']>0)
		{
			$payment_notice_id = make_payment_notice($data['pay_price'],$order['id'],$payment_info['id']);
			//创建支付接口的付款单
		}
	
		$rs = order_paid($order['id']);		
		update_order_cache($order['id']);
		if($rs)
		{
			$data = array();
			$data['info'] = "";
			$data['jump'] = url("index","payment#done",array("id"=>$order['id']));
			ajax_return($data); //支付成功
		
		}
		else
		{
			distribute_order($order['id']);
			$data = array();
			$data['info'] = "";
			$data['jump'] = url("index","payment#pay",array("id"=>$payment_notice_id));
			ajax_return($data);
		}

	}
	
}
?>