<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dcorderApiModule extends MainBaseApiModule
{

	
	
	/**
	 * 提交订单页面
	 * 测试地址：http://localhost/o2onew/mapi/index.php?ctl=dcorder&act=cart&r_type=2&lid=41
	 * 
	 * 	输入：
	 * menu_num：菜单信息，array('菜品ID'=>数量,'菜品ID'=>数量);
	 * $menu_num=array('45'=>2,'46'=>3);
	 * rs_num：	预订信息，array('id'=>预订的时间ID,'num'=>预订的数量,'rs_date'=>预订日期),
			$rs_num=array(
					array('id'=>9,'num'=>2,'rs_date'=>'2015-08-12'),
					array('id'=>10,'num'=>3,'rs_date'=>'2015-08-13'),
					
			);
			
	 *
	 * lid：商家ID
	 * payment_id:付款方式 payment_id=0为在线支付，payment_id=1为货到付款
	 * dc_type:	预订订单时，dc_type=1，不享受促销优惠，外卖订单时，dc_type=-1，享受促销优惠
	 * consignee_id	：送货地址的ID，如果没传此参数，会默认选择用户的默认地址
	 * ecvsn:红包支付的编号
	 * 
	 * 输出：
	 * total_price总额，pay_price应付金额，delivery_fee配送费，payment_fee手续费，package_fee手包费，account_money余额支付金额，ecv_money红包支付金额 ，
	 * promote_amount:已优惠总金额
	 * dc_promote:为享受的优惠
	 * 
	    [buy_total] => Array
	        (
	            [total_price] => 30   
	            [pay_price] => 20
	            [pay_total_price] => 30
	            [delivery_fee] => 2
	            [payment_fee] => 1
	            [payment_info] => 
	            [package_fee] => 1
	            [account_money] => 0
	            [ecv_money] => 10
	            [ecv_data] => 
	            [ecv_pass] => 0
	            [paid_account_money] => 0
	            [paid_ecv_money] => 0
	            [paid_promote_amount] => 0
	            [location_delivery_info] => 
	            [dc_package_info] => 
	            [dc_promote] => Array
	                (
	                    [PayOnlineDiscount] => Array
	                        (
	                            [name] => 在线支付优惠
	                            [discount_amount] => 5
	                            [promote_description] => 在线支付下单满20减5元，满40减12元，活动期间每天2单
	                        )

	                )
	        )
		*status 为错误提示状态，status=0，有错误，status=1，无错误
		*info，当state=0时的错误提示信息，如： 预订库存不足	
		*delivery_time当前可以配送的时间
		* Array
	        (
	            [0] => 立即送达
	            [1] => 16:00
	            [2] => 16:15
	            [3] => 16:30
	            [4] => 16:45
	            [5] => 17:00
	            [6] => 17:15
	            [7] => 17:30
	            [8] => 17:45
	            [9] => 18:00
	       ) 
	       consignee_info_all:用户配送地址信息，其中，list为配送地址列表，is_main为默认的配送地址
	        Array
	        (
	            [list] => Array
	                (
	                    [0] => Array
	                        (
	                            [id] => 120
	                            [user_id] => 71
	                            [address] => 地中心
	                            [api_address] => 福州市仓山区福州仓山万达广场
	                            [xpoint] => 119.281567
	                            [ypoint] => 26.042483
	                            [consignee] => 王明
	                            [mobile] => 15158789965
	                            [is_main] => 1
	                        )



	                )

	            [is_main] => Array
	                (
	                    [id] => 120
	                    [user_id] => 71
	                    [address] => 地中心
	                    [api_address] => 福州市仓山区福州仓山万达广场
	                    [xpoint] => 119.281567
	                    [ypoint] => 26.042483
	                    [consignee] => 王明
	                    [mobile] => 15158789965
	                    [is_main] => 1
	                )

	        )
	        voucher_list:红包列表
	        Array
	        (
	            [0] => Array
	                (
	                    [sn] => 366633373935
	                    [name] => 10元代金券
	                )

	            [1] => Array
	                (
	                    [sn] => 643436666236
	                    [name] => 2元代金券
	                )
	          )      
	 */
    public function old_cart()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$root['user_login_status']=0;
			return output($root);
		}else{
			$root['user_login_status']=1;
			$root['page_title']='提交订单';
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$location_id = intval($GLOBALS['request']['lid']);
		
		//付款方式 $payment_id=0为在线支付，$payment_id=1为货到付款
		$payment_id = intval($GLOBALS['request']['payment_id']);

		$menu_req = $GLOBALS['request']['menu_num'];
		
		$rs_req = $GLOBALS['request']['rs_num'];
		$user_id = intval($GLOBALS['user_info']['id']);
		$consignee = strim($GLOBALS['request']['consignee']);
		$mobile = strim($GLOBALS['request']['mobile']);
		$dc_comment = strim($GLOBALS['request']['dc_comment']);
		$ecvsn = strim($GLOBALS['request']['ecvsn']);
		if($consignee!=''){
			$root['consignee']=$consignee;
		}
		if($mobile!=''){
			$root['mobile']=$mobile;
		}
		if($dc_comment!=''){
			$root['dc_comment']=$dc_comment;
		}
	
		//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$dc_type = isset($GLOBALS['request']['dc_type'])?intval($GLOBALS['request']['dc_type']):-1;
		$consignee_id = $GLOBALS['request']['consignee_id']?intval($GLOBALS['request']['consignee_id']):'';
		$root['dc_type']=$dc_type;
		if($consignee_id=='' && $dc_type==-1){
			$consignee_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."dc_consignee where user_id=".$user_id." and is_main=1");
		}
		$root['is_return']=0;
		$ecvpassword ='';
		$payment = 0;
		$all_account_money = 0;
		$bank_id = '';
		$paid_account_money=$paid_ecv_money=$paid_promote_amount=0;
		$menu_num = array();
		foreach ($menu_req as $k=>$v)
		{
			$sv = intval($v);
			if($sv)
				$menu_num[$k] = intval($sv);
		}
		
		$rs_num = array();
		foreach ($rs_req as $kd=>$vd)
		{
			$sv = intval($vd['num']);
			if($sv){
				$rs_info=array();
				$rs_info['id']=$vd['id'];
				$rs_info['num']=$vd['num'];
				$rs_info['rs_date']=$vd['rs_date'];
				$rs_num[] = $rs_info;
			
			}
		}
	
		
		/*
		$menu_num=array('45'=>2,'46'=>3);
		
		$rs_num=array(
				array('id'=>9,'num'=>2,'rs_date'=>'2015-08-12'),
				array('id'=>10,'num'=>3,'rs_date'=>'2015-08-13'),
		
		);
		*/
	
		//定义$type=0 为预订，$type=1为外卖
		if(count($rs_num)>0){
			$type=0;
		}else{
			$type=1;
		}
		
		$menu_id=array();
		foreach($menu_num as $k=>$v){
			$menu_id[]=$k;
		}
		$menu_arr=implode(",",$menu_id);
		

		$menu_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_menu where id in(".$menu_arr.")");
		$menu_list=data_format_idkey($menu_list,$key='id');
		$location_dc_cart=array();
		$location_dc_cart['total_data']['total_price']=0;
		$location_dc_cart['total_data']['total_count']=0;
		foreach($menu_num as $k=>$v){
			$location_dc_cart['total_data']['total_price']+=$menu_list[$k]['price']*$v;
			$location_dc_cart['total_data']['total_count']+=$v;
		}
		
		$rs_id=array();
		foreach($rs_num as $k=>$v){
			$rs_id[]=$v['id'];
		}
		$rs_arr=implode(",",$rs_id);
		
		$rs_list=$GLOBALS['db']->getAll("select rs_item.price , rs_time.* from ".DB_PREFIX."dc_rs_item_time as rs_time left join ".DB_PREFIX."dc_rs_item as rs_item on rs_time.item_id=rs_item.id  where rs_time.id in(".$rs_arr.")");
		$rs_list=data_format_idkey($rs_list,$key='id');
		$location_dc_table_cart=array();
		$location_dc_table_cart['total_data']['total_price']=0;
		$location_dc_table_cart['total_data']['total_count']=0;
		$rs_num=data_format_idkey($rs_num,$key='id');
		
		foreach($rs_num as $k=>$v){
			$location_dc_table_cart['total_data']['total_price']+=$rs_list[$k]['price']*$v['num'];
			$location_dc_table_cart['total_data']['total_count']+=$v['num'];
		}

		
		$dclocation=$GLOBALS['db']->getRow("select id,name,dc_allow_ecv,dc_allow_cod,dc_online_pay from ".DB_PREFIX."supplier_location where id=".$location_id);
		
		if(!$dclocation){
			$root['is_return']=1;
			return output($root,0,'无此商家');
		}
		
		if($payment_id==0){
			
			if($dclocation['dc_allow_cod']==1 && $dclocation['dc_online_pay']==0){
				
				$payment_id=1;
			}
		}
		
		
		
		$root['payment_id']=$payment_id;
		
		
		$dc_count_buy_total=dc_count_buy_total($location_id,$location_dc_table_cart,$location_dc_cart,$payment_id,$dc_type,$consignee_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword, $bank_id,$paid_account_money,$paid_ecv_money,$paid_promote_amount);
		$dc_count_buy_total['promote_amount']=0;
		foreach($dc_count_buy_total['dc_promote'] as $zz=>$yy){
			$dc_count_buy_total['promote_amount']+=$yy['discount_amount'];
		}
		
		$root['buy_total']=$dc_count_buy_total;
		
		if($location_dc_cart['total_data']['total_price']==0 && $location_dc_table_cart['total_data']['total_price'] ==0 ){
			$root['is_return']=1;
			return output($root,0,'购物车是空的');
		}
		
		if($type==0){
			
			//验证预订位置的库存
			foreach($rs_num as $kk=>$vv){
				//$rs_date=to_date($vv['table_time'],"Y-m-d");
				$total_count=$GLOBALS['db']->getOne("select total_count from ".DB_PREFIX."dc_rs_item_time where  id=".$vv['id']);
				$rs_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_rs_item_day where time_id=".$vv['id']." and rs_date='".$vv['rs_date']."'");
			
				if(!$rs_info && $total_count==0){
					$root['is_return']=1;
					return output($root,0,'预订库存不足');
					break;
				}elseif($rs_info && $rs_info['buy_count'] + $vv['num'] > $total_count ){
					$root['is_return']=1;
					return output($root,0,'预订库存不足');
					break;
				}
			}
			
			if($location_dc_cart['total_data']['total_price']>0 && $location_dc_table_cart['total_data']['total_price'] > $location_dc_cart['total_data']['total_price'] ){	
				$root['is_return']=1;
				return output($root,0,'点菜金不足');
			}
			

			return output($root);
			
			
		}else{
			//开始身边团购的地理定位
			$tname='sl';

			$consignee_info_all=$this->load_user_consignee($consignee_id);
			$root['consignee_info']=$consignee_info_all;

			$begin_time=to_timespan(to_date(NOW_TIME,"Y-m-d"));
			$end_time=$begin_time+3600*24-1;
			
		
			//当天使用优惠劵的订单个数
			$today_order_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where user_id=".$user_id ." and create_time > ".$begin_time." and create_time < ".$end_time." and ecv_money > 0");
			if($today_order_count==0 && $dclocation['dc_allow_ecv']==1 && $payment_id==0){
				$root['voucher_list']=$this->get_voucher_list();
			}

			$root['dclocation']= $dclocation;

			$is_in_open_time=is_in_open_time($location_id);
			if ($is_in_open_time==0){	
				$root['is_return']=1;
				return output($root,0,'商家休息中');
			}

			$delivery_time=$this->get_delivery_time($location_id);
			if($delivery_time){
				array_unshift($delivery_time, '立即送达');
				$root['delivery_time']=$delivery_time;
			}else{
			
				$root['is_return']=1;
				return output($root,0,'不在商家配送时间内');
			
					
			}
			
			if($location_dc_table_cart['total_data']['total_price'] > 0){
				$dc_type=1;
			}else{
				$dc_type=-1;
			}
		
			$root['dc_type']=$dc_type;
			return output($root);
			
			}
		
		}
	}
	public function cart()
	{
		$root = array();
		do {
			$status = 0;
			$info = '';
			// 登录状态验证
			$login_status = check_save_login();
			if ($login_status != LOGIN_STATUS_LOGINED) {
				$info = '用户未登录';
				break;
			}
			$user_info = $GLOBALS['user_info'];

			// 请求参数验证
			$request = $GLOBALS['request'];
			$location_id = intval($request['lid']); // 商家id
			$dc_type = intval($request['dc_type']); //  > 0 预订(不享受优惠) , -1 : 
			$consignee_id = intval($request['consignee_id']); // 配送地址id
			$ecvsn = strim($request['ecvsn']); // 优惠券编码
			// 商家信息
			$locaSql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$location_id.' AND is_dc=1 AND is_close=0 AND is_effect=1';
			$location_info = $GLOBALS['db']->getRow($locaSql);
			if (empty($location_info)) {
				$info = '商家不存在';
				break;
			}

			$location_info['preview'] = get_abs_img_root(get_spec_image($location_info['preview'],90,90,1));

			require_once(APP_ROOT_PATH."system/model/dc.php");
			$is_in_open_time=is_in_open_time($location_id);
			if ($is_in_open_time==0){	
				$info = '商家休息中';
				break;
			}

			update_dc_cart($location_id, $user_info['id']);


			// 购物车验证
			$dc_cart_list = load_dc_cart_list(true, $location_id, 1);
			// print_r($dc_cart_list);exit;
			if (empty($dc_cart_list['cart_list'])) {
				$info = '购物车为空';
				break;
			}

			$hasconsignee = 0;
			$consInfo = array(); // 配送地址
			// 配送地址验证
			if ($consignee_id) { // 如果传了配送地址id
				$consSql = 'SELECT * FROM '.DB_PREFIX.'dc_consignee WHERE id='.$consignee_id.' AND user_id='.$user_info['id'];
				$consInfo = $GLOBALS['db']->getRow($consSql);
				if (!empty($consInfo)) {
					$hasconsignee = 1;
				}
			}
			
			if (empty($consignee_id) || empty($consInfo)) { // 如果没传地址id或id错误
				$consSql = 'SELECT * FROM '.DB_PREFIX.'dc_consignee WHERE user_id='.$user_info['id'].' ORDER BY id DESC';
				$consInfos = $GLOBALS['db']->getAll($consSql);
				foreach ($consInfos as $cons) {
					$hasconsignee = 1;
					if ($location_info['max_delivery_scale'] <= 0 || getDistance($cons['ypoint'], $cons['xpoint'], $location_info['ypoint'], $location_info['xpoint']) / 1000 < $location_info['max_delivery_scale']) {
						$consInfo = $cons;
						break;
					}
				}
			}

			// 购物车金额计算
			$total_price = 0;
			$total_num = 0;
			foreach ($dc_cart_list['cart_list'] as $item) {
				$total_num += $item['num']; // 打包的分数
				$total_price += $item['total_price'];
			}
			$total_price = round($total_price, 2);

			// 配送费的金额计算
			$delivery_price = 0;  // 配送费
			$delivery_start_price = 0;  // 起送价
			$isInScale = false; // 是否在配送范围内
			$isInStartPrice = false; // 是否在起送金额内
			if ($consInfo) { // 如果选择了地址计算配送费
				$delivery_price_sql = 'SELECT * FROM '.DB_PREFIX.'dc_delivery WHERE location_id='.$location_info['id'].' ORDER BY scale';
				$delivery_price_list = $GLOBALS['db']->getAll($delivery_price_sql);
				if (!empty($delivery_price_list)) {
					$distance = getDistance($consInfo['ypoint'], $consInfo['xpoint'], $location_info['ypoint'], $location_info['xpoint']);
					$distance /= 1000;  // 转换成公里数
					foreach ($delivery_price_list as $v) {
						if ($v['scale'] >= $distance) {
							$isInScale = true;
							if ($v['start_price'] <= $total_price) {
								$isInStartPrice = true;
							}
							$delivery_price = round($v['delivery_price'],2);
							$delivery_start_price = $v['start_price'];
							break;
						}
					}
				}
			}
			
			// 打包费的金额计算
			$package_price = 0;
			$package_fee_sql = 'SELECT * FROM '.DB_PREFIX.'dc_package_conf WHERE location_id='.$location_id;
			$package_fee = $GLOBALS['db']->getRow($package_fee_sql);
			if (($package_fee['package_start_price'] > 0 && $total_price < $package_fee['package_start_price'])
				|| $package_fee['package_start_price'] == -1) {
				// 起送价设置为(大于0且购物车金额小于基础价)或者(等于-1)时
				$package_price = round($package_fee['package_price'] * $total_num,2);
			}

			// 促销金额计算
			$dcPromote = $this->getDcPromote($location_info, $user_info, $total_price);

			// 优惠券红包减免计算



			// 商家配送时间
			$delivery_time = $this->get_delivery_time($location_id);
			if($delivery_time) {
				array_unshift($delivery_time, '尽快配送');
				$root['delivery_time']=$delivery_time;
			}

			$total_count = $total_price + $package_price + $delivery_price;
			$promote_price = ($dcPromote['has_promote'] && !empty($dcPromote['promote_info'])) ? $dcPromote['promote_price'] : 0;
			$pay_price = $total_count - $promote_price; // 待支付的金额
			if ($pay_price <= 0) {
				$pay_price = 0;
			}

			$root = array(
				'location_info' => $location_info, // 商家信息
				'list' => $dc_cart_list['cart_list'], // 商品列表
				'package_price' => $package_price, // 打包费
				'delivery_price' => $delivery_price, // 配送费
				// 'promote_info' => $dcPromote['promote_info'], // 参与的促销
				'total_price' => $total_price, // 订单商品总金额
				'total_count' => $total_count, // 订单未优惠前的金额
				'pay_price' => $pay_price,
				'discount_price' => $promote_price, // 优惠的金额
				'isInScale' => $isInScale, // 是否在配送范围内
				'isInStartPrice' => $isInStartPrice, // 是否超过起送价
				'delivery_time' => $delivery_time, // 当前支持的配送时间
				'consignee_info' => $consInfo,
				'hasconsignee' => $hasconsignee,
			);

			if (!empty($dcPromote['promote_info'])) {
				$root['promote_info'] = $dcPromote['promote_info'];
			}

			$status = 1;

			$root['baidu_m_key'] = app_conf('BAIDU_MAP_APPKEY');

		} while(0);
		$root['user_login_status'] = $login_status;
		$root['page_title'] = '提交订单';

		return output($root, $status, $info);
	}

	public function res_cart()
	{
        $root = array();
		do {
			$status = 0;
			$info = '';

			// 请求参数验证
			$request = $GLOBALS['request'];
			$location_id = intval($request['lid']); // 商家id
			$locaSql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$location_id.' AND is_reserve=1 AND is_effect=1';
			$location_info = $GLOBALS['db']->getRow($locaSql);
			if (empty($location_info)) {
				$info = '商家不存在';
				break;
			}
			if ($location_info['is_close']) {
				$info = '商家休息中';
				break;
			}
			$rs_date = strim($request['rs_date']);
			if (!preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $rs_date)) {
				$info = '时间参数异常';
				break;
			}
			$now_date = to_date(NOW_TIME, 'Y-m-d');
			

			$item_id = intval($request['item_id']);
			$item_sql = 'SELECT * FROM '.DB_PREFIX.'dc_rs_item WHERE id='.$item_id.' AND location_id='.$location_id.' AND is_effect=1';
			$item_info = $GLOBALS['db']->getRow($item_sql);
			if (empty($item_info)) {
				$info = '预约项目不存在或已停止';
				break;
			}
			$item_info['format_price'] = format_price($item_info['price']);

			$item_time_sql = 'select it.*, id.buy_count from '.DB_PREFIX.'dc_rs_item_time it left join '.DB_PREFIX.'dc_rs_item_day id on it.id=id.item_id AND id.rs_date="'.$rs_date.'" where it.item_id='.$item_id;
			$db_item_time = $GLOBALS['db']->getAll($item_time_sql);
			$item_time = array();
			foreach ($db_item_time as $item) {
				if ($item['is_effect'] == 0) {
					continue;
				}
				// 可预约的时间进行限制
				$rstimestamp = to_timespan($rs_date.' '.$item['rs_time']);
				$item['reasion'] = '';
				if ($rstimestamp - 3600 < NOW_TIME || $rstimestamp > NOW_TIME + 3600 * 24 * 7) {
					$item['is_effect'] = 0;
					$item['reasion'] = 'timeerror';
				} elseif ($item['buy_count'] >= $item['total_count']) {
					$item['is_effect'] =0;
					$item['reasion'] = 'fullbuy';
				}
				$item_time[] = $item;
			}

			// 从数据库获取点菜的信息。
			$hasCart = 0;
			// $cartSql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE cart_type=0 AND table_menu_id='.$item_id.' AND location_id='.$location_id;
			$cartSql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE cart_type=0 AND location_id='.$location_id;
			if (!empty($GLOBALS['user_info'])) {
				$cartSql .= ' AND user_id='.$GLOBALS['user_info']['id'];
			} else {
				$cartSql .= ' AND session_id="'.es_session::id().'"';
			}

			$cart_menu = $GLOBALS['db']->getAll($cartSql);
			$cart_total_price = 0;
			if ($cart_menu) {
				$hasCart = 1;
				// 计算一下菜金合计
				foreach ($cart_menu as &$cart) {
					$cart_total_price += $cart['total_price'];
					$cart['format_unit_price'] = format_price($cart['unit_price']);
				}unset($cart);
			}

			$isCollected = 0;
			if (!empty($GLOBALS['user_info']['id'])) {
				$user_id = $GLOBALS['user_info']['id'];
				$isCollectedSql = 'SELECT count(id) FROM '.DB_PREFIX.'dc_location_sc WHERE location_id='.$location_id.' AND user_id='.$user_id;
				$isCollected = $GLOBALS['db']->getOne($isCollectedSql);
			}
			// $root['isCollected'] = $isCollected ? 1 : 0;

			$root = array(
				// 'location_info' => $location_info, // 商家信息
				'item_info' => $item_info, // 订座信息
				'item_time' => $item_time, // 订座时间信息
				'has_cart' => $hasCart,
				// 'item_sql' => $item_time_sql,
				'is_collected' => $isCollected ? 1 : 0,
				'page_title' => '预约订座',
			);
			// $root['sql'] = $cartSql;
			if ($hasCart) {
				$root['cart_menu'] = $cart_menu;
				$root['cart_total_price'] = $cart_total_price;
				$root['format_cart_total_price'] = format_price($cart_total_price);
			}

		} while(0);
		$root['location_info'] = $location_info;

		return output($root, $status, $info);
	}

	public function res_cart_item()
	{

    	$location_id = intval($GLOBALS['request']['lid']);
		//获取菜单
        /*$location_menu_cate=$this->get_menu_cate($location_id);
        foreach($location_menu_cate as $k=>$v){
            $location_menu_cate[$k]['main_cate']['icon_img']=get_abs_img_root(get_spec_image($v['main_cate']['icon_img'],250,250,1));
            foreach($location_menu_cate[$k]['sub_menu'] as $kk=>$vv){
                $location_menu_cate[$k]['sub_menu'][$kk]['image']=get_abs_img_root(get_spec_image($vv['image'],250,250,1));
            }
        }
        $root['menu_list']=$location_menu_cate;*/

        //获取购物车
        $table_menu_id = intval($GLOBALS['request']['table_menu_id']);
        // $cartSql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE cart_type=0 AND table_menu_id='.$table_menu_id.' AND location_id='.$location_id;
        $cartSql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE cart_type=0 AND location_id='.$location_id;
		if (!empty($GLOBALS['user_info'])) {
			$cartSql .= ' AND user_id='.$GLOBALS['user_info']['id'];
		} else {
			$cartSql .= ' AND session_id="'.es_session::id().'"';
		}
		$cartList = $GLOBALS['db']->getAll($cartSql);
        $cart_total_price=0;
        $cart_menu_count = array();
        foreach($cartList as $key => $cart)
        {
            $cart_total_price += $cart['total_price'];
            $cart_menu_count[$cart['menu_id']] = $cart['num'];
            $cartList[$key]['format_price'] = format_price($cartList[$key]['unit_price']);
        }
        
        $cate_sql = 'SELECT mc.id, mc.name as cname, mc.icon_img,m.id as menu_id, m.name as mname, m.price, m.image FROM '.DB_PREFIX.'dc_supplier_menu_cate mc LEFT JOIN '.DB_PREFIX.'dc_menu m ON m.cate_id=mc.id AND m.is_effect=1 WHERE mc.location_id='.$location_id.' AND mc.is_effect=1';
		$orig_cate_list = $GLOBALS['db']->getAll($cate_sql);
		// print_r($cate_sql);
		// print_r($orig_cate_list);exit;
		$cate_list = array();
		foreach ($orig_cate_list as $cate) {
			if (empty($cate_list[$cate['id']]['name'])) {
				$cate_list[$cate['id']]['name'] = $cate['cname'];
				$cate_list[$cate['id']]['icon_img'] = get_abs_img_root(get_spec_image($cate['icon_img'],250,250,1));
			}
			
			$sub_menu = array(
				'id' => $cate['menu_id'],
				'name' => $cate['mname'],
				'price' => $cate['price'],
				'format_price' => format_price($cate['price']),
				'image' => get_abs_img_root(get_spec_image($cate['image'],250,250,1)),
			);
			$cart_count = 0;
			if (array_key_exists($cate['menu_id'], $cart_menu_count)) {
				$cart_count = $cart_menu_count[$cate['menu_id']];
			}
			$sub_menu['cart_count'] = $cart_count;

			$cate_list[$cate['id']]['sub_menu'][] = $sub_menu;
		}


		$root['cate_list'] = $cate_list;

        $root['cart_data'] = array('cart_list' => $cartList, 'cart_count' => count($cartList), 'cart_total_price' => $cart_total_price, 'format_total_price' => format_price($cart_total_price));
        return output($root);
	}


	/**
	 * 外卖促销活动减免计算
	 * @param  array $location_info 商家信息
	 * @param  array $user_info     用户信息
	 * @param  float $total_price   购物车金额
	 * @return array                
	 */
	private function getDcPromote($location_info, $user_info, $total_price)
	{
		$promote_info = array(); // 可参与的促销活动
		$has_promote = false;
		$promote_finish = false; // 促销计算完成
		$promote_price = 0; // 促销减免金额

		$promote_sql = 'SELECT * FROM '.DB_PREFIX.'dc_promote';
		$promote_list = $GLOBALS['db']->getAll($promote_sql);
		$promote_list_format = array();
		if ($promote_list) {
			foreach ($promote_list as $prom) {
				$promote_list_format[$prom['class_name']] = $prom;
			}
			$has_promote = true;
		}
		if ($has_promote) {
			$user_order_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_supplier_order where user_id=".$user_info['id']);
			// 先计算首单立减  有安装并且门店支持并且会员还未享受促销
			if (array_key_exists('FirstOrderDiscount', $promote_list_format) && $location_info['is_firstorderdiscount'] && $user_info['dc_is_share_first'] == 0 && $user_order_count == 0) {
				$promote_list_format['FirstOrderDiscount']['config'] = unserialize($promote_list_format['FirstOrderDiscount']['config']);
				$promote_price = $promote_list_format['FirstOrderDiscount']['config']['discount_amount'];
				$promote_info = $promote_list_format['FirstOrderDiscount'];
				$promote_finish = true;
			}
			// 计算满减  不是首单且门店支持
			if (!$promote_finish && array_key_exists('PayOnlineDiscount', $promote_list_format) && $location_info['is_payonlinediscount']) {
				$pconf = $promote_list_format['PayOnlineDiscount']['config'] = unserialize($promote_list_format['PayOnlineDiscount']['config']);
				$daily_limit = $pconf['daily_limit'];
				if ($daily_limit > 0) { // 每日有限制订单数量
					$begin_time=to_timespan(to_date(NOW_TIME,"Y-m-d"));
					$end_time=$begin_time+3600*24-1;
					$order_count_sql = 'SELECT count(*) FROM '.DB_PREFIX.'dc_supplier_order where user_id='.$user_info['id'] .' and create_time > '.$begin_time.' and create_time < '.$end_time.' and pay_status=1 and payment_id=0 and promote_amount > 0 and is_cancel=0 and refund_status=0';
					$today_order_count=$GLOBALS['db']->getOne($order_count_sql);
					if ($today_order_count >= $daily_limit) {
						$promote_finish = true;
					}
				}
				if (!$promote_finish) {
					$discount_limit = $pconf['discount_limit'];
					arsort($discount_limit, SORT_NUMERIC);
					$discount_amount = $pconf['discount_amount'];
					foreach ($discount_limit as $k => $lim) {
						if ($total_price >= $lim) {
							$promote_price = $discount_amount[$k];
							$promote_info = $promote_list_format['PayOnlineDiscount'];
							$promote_info['config'] = array('discount_limit' => $lim, 'discount_amount' => $discount_amount[$k]);
							break;
						}
					}
				}
			}
		}
		return array('has_promote' => $has_promote, 'promote_info' => $promote_info, 'promote_price' => $promote_price);
	}

	/**
	 * 生成订单接口
	 * 测试链接：http://localhost/o2onew/mapi/index.php?ctl=dcorder&act=make_order&r_type=2&location_id=41
	 * 
	 * 	输入：
	 * menu_num：菜单信息，array('菜品ID'=>数量,'菜品ID'=>数量);
	 * $menu_num=array('45'=>2,'46'=>3);
	 * rs_num：	预订信息，array('id'=>预订的时间ID,'num'=>预订的数量,'rs_date'=>预订日期),
	 $rs_num=array(
	 array('id'=>9,'num'=>2,'rs_date'=>'2015-08-12'),
	 array('id'=>10,'num'=>3,'rs_date'=>'2015-08-13'),
	 	
	 );
	 	
	 *
	 * lid：商家ID
	 * payment_id:付款方式 payment_id=0为在线支付，payment_id=1为货到付款
	 * dc_type:	预订订单时，dc_type=1，不享受促销优惠，外卖订单时，dc_type=-1，享受促销优惠
	 * consignee_id	：送货地址的ID
	 * ecvsn：红包的编号
	 * dc_comment：订单的备注
	 * order_delivery_time外卖 的送餐时间，order_delivery_time=1时，为立即送达，具体时间传入，如：17:45
	 * invoice：发票信息
	 * consignee:预订订单传过来的姓名
	 * mobile:预订订单传过来的电话
	 *  输出：
	 * status：为提示状态，status=0,有错误;status=1,成功
	 * info:返回的提示信息
	 * order_id : 生成新订单的id
	 * payment_method :int 支付方式ID，0表示在线支付，1表示货到付款,如果payment_method=1，货到付款，直接访问order_done接口
	 */
	public function old_make_order()
	{
		require_once(APP_ROOT_PATH."system/model/dc.php");

		$payment = 0;
		$account_money = 0;
		$all_account_money = 0;
		$ecvsn = $GLOBALS['request']['ecvsn']?strim($GLOBALS['request']['ecvsn']):'';
		$ecvpassword = '';
        $location_id = intval($GLOBALS['request']['lid']);
		$consignee = strim($GLOBALS['request']['consignee']);
		$mobile = strim($GLOBALS['request']['mobile']);

		// 兼容原先的订座购物车数据
		// $table_menu_id = 
		$rs_date = strim($GLOBALS['request']['rs_date']);
		$table_time_id = intval($GLOBALS['request']['item_time_id']);
		$rs_type =intval($GLOBALS['request']['rs_type']); // 1:不提前点菜 2提前点菜
		$dc_type = intval($GLOBALS['request']['dc_type']);
		if ($dc_type == 1) {
			$addRes = $this->add_table_cart($location_id, $rs_date, $table_time_id);
			if (!$addRes) {
				return output($root,0,'预定额已满');
			}
		}
		// -----

		$location_dc_table_cart=load_dc_cart_list(true,$location_id,$type=0);
		$location_dc_cart=load_dc_cart_list(true,$location_id,$type=1);
		$menu_req=array();
		foreach($location_dc_cart['cart_list'] as $k=>$v) {
			$menu_req[$v['menu_id']]=$v['num'];
		}
		$rs_req=array();
		foreach($location_dc_table_cart['cart_list'] as $k=>$v) {
			$rs_info=array();
			$rs_info['id']=$v['table_time_id'];
			$rs_info['num']=$v['num'];
			$rs_info['rs_date']=to_date($v['table_time'],"Y-m-d");
			$rs_req[]=$rs_info;
		}
		
		$dc_comment =  $GLOBALS['request']['dc_comment']?strim( $GLOBALS['request']['dc_comment']):'';
		$invoice =  $GLOBALS['request']['invoice']?strim( $GLOBALS['request']['invoice']):'';
		$payment_id =  $GLOBALS['request']['payment_id']?intval( $GLOBALS['request']['payment_id']):0;  //付款方式 ,1为货到付款，0为在线支付
		// $dc_type = isset( $GLOBALS['request']['dc_type'])?intval( $GLOBALS['request']['dc_type']):-1;          //$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
		$bank_id =  '';
		$order_delivery_time =  $GLOBALS['request']['order_delivery_time']?strim( $GLOBALS['request']['order_delivery_time']):'';
		$consignee_id =  $GLOBALS['request']['consignee_id']?intval( $GLOBALS['request']['consignee_id']):'';
		$user_id = intval($GLOBALS['user_info']['id']);
		$session_id = es_session::id();

		//验证用户是否已经登录
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$root['user_login_status']=0;
			return output($root);
		}else{
			$root['user_login_status']=1;

			if($consignee=='' && $dc_type==1){
				return output($root,0,'请填写姓名');
			}
			
			if( $dc_type==1){

				if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
					return output($root,0,'请正确填写手机号');
				}
			}
			
			$menu_num = array();
			foreach ($menu_req as $k=>$v)
			{
				$sv = intval($v);
				if($sv)
					$menu_num[$k] = intval($sv);
			}

			$rs_num = array();
			foreach ($rs_req as $kd=>$vd)
			{
				$sv = intval($vd['num']);
				if($sv){
					$rs_info=array();
					$rs_info['id']=$vd['id'];
					$rs_info['num']=$vd['num'];
					$rs_info['rs_date']=$vd['rs_date'];
					$rs_num[] = $rs_info;
						
				}
			}

			/*
			$ecvsn='386638353764';
			$dc_type=-1;
			$consignee_id=120;
			$menu_num=array('45'=>2,'46'=>3);
			$rs_num=array(
					array('id'=>9,'num'=>2,'rs_date'=>'2015-08-12'),
					array('id'=>10,'num'=>3,'rs_date'=>'2015-08-13'),
						
			);
			*/
			$menu_id=array();
			foreach($menu_num as $k=>$v){
				$menu_id[]=$k;
			}
			$menu_arr=implode(",",$menu_id);
				
				
			$menu_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_menu where id in(".$menu_arr.")");
			$menu_list=data_format_idkey($menu_list,$key='id');
			$location_dc_cart=array();
			$location_dc_cart['total_data']['total_price']=0;
			$location_dc_cart['total_data']['total_count']=0;
			foreach($menu_num as $k=>$v){
				$location_dc_cart['total_data']['total_price']+=$menu_list[$k]['price']*$v;
				$location_dc_cart['total_data']['total_count']+=$v;
			}
				
			if($dc_type==-1){  //外卖时才检测
					
				$consignee_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_consignee where id=".$consignee_id);
				if(!$consignee_info){
					return output($root,0,'请选择配送地址');
				}
			
				$tname='sl';
			
				$xpoint = $consignee_info['xpoint'];
				$ypoint = $consignee_info['ypoint'];
				$consignee_info_all=$this->load_user_consignee($consignee_id);
					
				if($xpoint>0)
				{
					$pi = PI;
					$r = EARTH_R;
					$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
						
				}
				$dclocation=$GLOBALS['db']->getRow("select ".$tname.".id,".$tname.".name".$field_append." from ".DB_PREFIX."supplier_location as ".$tname." where ".$tname.".id=".$location_id);
					
				if(!$dclocation){
					$root['is_return']=1;
					return output($root,0,'无此商家');
				}
				
				$delivery_time=$this->get_delivery_time($location_id);
				if(!$delivery_time){

					$root['is_return']=1;
					return output($root,0,'不在商家配送时间内');	
				}
					
				$id_arr=array($location_id=>array('id'=>$location_id,'distance'=>$dclocation['distance']));
				$location_delivery_info=get_location_delivery_info($id_arr);
				$location_delivery=array();
				foreach($location_delivery_info as $kk=>$vv){
					$location_delivery=$vv;
				}
					
				if($location_delivery['is_free_delivery']==2){
					return output($root,0,'不在配送范围内');
				}
					
				//起送价验证
				if($location_delivery['is_free_delivery']==0 && $location_delivery['start_price'] > $location_dc_cart['total_data']['total_price']){
			
					return output($root,0,'起送价不足,起送价为：'.round($location_delivery['start_price'],2)."元");
				}
					
			
			}
			
			foreach($menu_list as $kk=>$vv){
				$menu_order=array();
				$menu_order['session_id']=es_session::id();
				$menu_order['user_id']=$user_id;
				$menu_order['location_id']=$location_id;
				$menu_order['name']=$vv['name'];
				$menu_order['icon']=$vv['image'];
				$menu_order['num']=$menu_num[$kk];
				$menu_order['unit_price']=$vv['price'];
				$menu_order['total_price']=$vv['price']*$menu_order['num'];
				$menu_order['menu_id']=$vv['id'];
				$menu_order['cart_type']=1;
				$menu_order['add_time']=NOW_TIME;
				$menu_order['is_effect']=1;
				$location_dc_cart['cart_list'][]=$menu_order;
			}
				
			$rs_id=array();
			foreach($rs_num as $k=>$v){
				$rs_id[]=$v['id'];
			}
			$rs_arr=implode(",",$rs_id);
				
			$rs_list=$GLOBALS['db']->getAll("select rs_item.price ,rs_item.name ,rs_item.id as item_id , rs_time.* from ".DB_PREFIX."dc_rs_item_time as rs_time left join ".DB_PREFIX."dc_rs_item as rs_item on rs_time.item_id=rs_item.id  where rs_time.id in(".$rs_arr.")");
			$rs_list=data_format_idkey($rs_list,$key='id');
			$location_dc_table_cart=array();
			$location_dc_table_cart['total_data']['total_price']=0;
			$location_dc_table_cart['total_data']['total_count']=0;
			$rs_num=data_format_idkey($rs_num,$key='id');
				
			foreach($rs_num as $k=>$v){
				$location_dc_table_cart['total_data']['total_price']+=$rs_list[$k]['price']*$v['num'];
				$location_dc_table_cart['total_data']['total_count']+=$v['num'];
			}
			$rows=array("日","一","二","三","四","五","六");
			foreach($rs_list as $kk=>$vv){
				$menu_order=array();
				$menu_order['session_id']=es_session::id();
				$menu_order['user_id']=$user_id;
				$menu_order['location_id']=$location_id;
				$menu_order['name']=$vv['name'];

				$menu_order['num']=$rs_num[$kk]['num'];
				$menu_order['unit_price']=$vv['price'];
				$menu_order['total_price']=$vv['price']*$menu_order['num'];
				$menu_order['menu_id']=$vv['item_id'];
				$menu_order['cart_type']=0;
				$menu_order['add_time']=NOW_TIME;
				$menu_order['is_effect']=1;
				$menu_order['table_time_id']=$vv['id'];
				$menu_order['table_time']=to_timespan($rs_num[$kk]['rs_date'].' '.$vv['rs_time']);
				$menu_order['table_time_format']='<span class="time_span">'.to_date($menu_order['table_time'],"Y-m-d").'</span><span class="time_span">星期'.$rows[to_date($menu_order['table_time'],"w")].'</span><span class="time_span">'.to_date($menu_order['table_time'],"H:i").'</span>';
				$location_dc_table_cart['cart_list'][]=$menu_order;
			}

			//验证购物车是否为空
			if($dc_type==-1){  //外卖购物车验证
				if($location_dc_cart['total_data']['total_price']==0){
					return output($root,0,$GLOBALS['lang']['CART_EMPTY_TIP']);
				}
				
			}else{  //预订购物车验证
				if($location_dc_table_cart['total_data']['total_count']==0){
					return output($root,0,'请选择预订时间');
				}
			}
			
			$is_location_close=$GLOBALS['db']->getOne("select is_close from ".DB_PREFIX."supplier_location where id=".$location_id);
			if($is_location_close==1){
				return output($root,0,$GLOBALS['lang']['DC_LOCATION_CLOSE']);
			
			}

			/* 验证购物车中每条购物是否存在，暂无  */
			$tname='sl';
			$location_info=get_location_info($tname,$location_id,$field_append='');

			if($payment_id==0 && $location_info['dc_online_pay']==0){
				//不支持在线支付
				return output($root,0,$GLOBALS['lang']['CANNOT_PAY_ONLINE']);
					
			}
			if($payment_id==1 && $location_info['dc_allow_cod']==0){
				//不支持货到付款
				return output($root,0,$GLOBALS['lang']['CANNOT_ALLOW_COD_PAY']);
			}

			//结束验证购物车
			//开始验证订单接交信息
			
			$result = dc_count_buy_total($location_id,$location_dc_table_cart,$location_dc_cart,$payment_id,$dc_type,$consignee_id,$payment,$account_money,$all_account_money,$ecvsn,$ecvpassword,$bank_id);

			$dc_consignee=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_consignee where id=".$consignee_id);
			//外卖时 $dc_type==-1，验证配送地址是否存在
			if($dc_type==-1){
				if($dc_consignee['xpoint']=='' || $dc_consignee['ypoint']=='' || $dc_consignee['consignee']=='' || $dc_consignee['mobile']=='' || $dc_consignee['api_address']=='' || $dc_consignee['address']=='' ){
					return output($root,0,$GLOBALS['lang']['DC_DELIVERY_NO_EXIST']);
						
				}
			}else{
				//验证预订位置的库存
				foreach($location_dc_table_cart['cart_list'] as $kk=>$vv){
					$rs_date=to_date($vv['table_time'],"Y-m-d");
					$total_count=$GLOBALS['db']->getOne("select total_count from ".DB_PREFIX."dc_rs_item_time where  id=".$vv['table_time_id']);
					$rs_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_rs_item_day where time_id=".$vv['table_time_id']." and rs_date='".$rs_date."'");
					if(!$rs_info && $total_count==0){
						return output($root,0,$GLOBALS['lang']['DC_TABLE_OUT_STOCK']);
						break;
					}elseif($rs_info && $rs_info['buy_count'] + $vv['num'] > $total_count ){
						return output($root,0,$GLOBALS['lang']['DC_TABLE_OUT_STOCK']);
						break;
					}
				}

				if($location_dc_table_cart['total_data']['total_price'] > $location_dc_cart['total_data']['total_price'] && $location_dc_cart['total_data']['total_price'] >0 ){
					return output($root,0,'点菜金不足');
				}

			}

			//验证是否超过该商家配送范围，$is_out_scale=0为不超出，$is_out_scale=1为超出商家配送范围
			$is_out_scale=0;
			if($location_dc_table_cart['total_data']['total_price']==0 && $location_dc_cart['total_data']['total_price']>0){
				if($result['location_delivery_info']['is_free_delivery']==2){
					$is_out_scale=1;
				}
			}
			if($is_out_scale==1){
				return output($root,0,$GLOBALS['lang']['DC_OUT_DELIVERY_SCALE']);
					
			}

			//结束验证订单接交信息

			//开始生成订单

			$consignee_info_all=$this->load_user_consignee($consignee_id);
			$consignee_info=$consignee_info_all;
			if($dc_type==-1){
				$consignee=$consignee_info['consignee'];
				$mobile=$consignee_info['mobile'];
			}


			//获取代金劵ID
			$ecvid=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."ecv where sn=".$ecvsn." and user_id=".$user_id);

			//享受的优惠措失，带换行的字符串
			foreach($result['dc_promote'] as $k=>$v){
				$order['promote_amount'] += $v['discount_amount'];
			}
			$order['promote_str']=serialize($result['dc_promote']);
			//$dc_type大等于0为预订方式，不享受促销优惠，-1代表享受促销优惠
			if($dc_type==-1){
				$order['rs_price'] = 0;   //定金
				$order['is_rs'] = 0;   //是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券
					
			}else{
				$order['rs_price'] = $location_dc_table_cart['total_data']['total_price'];   //定金
				$order['is_rs'] = 1;   //是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券
					
			}

			//
			$dc_cart_total['rs_list']=$location_dc_table_cart;
			$dc_cart_total['menu_list']=$location_dc_cart;
			$order['order_menu'] = serialize($dc_cart_total); //序列化下来的订单的商品数据，含名称，数量，图片，单价，总价等
			//计算网站提成，和应给商家的钱 ,商家的balance_type=0为  按百分比收取 ，balance_type=1按单收取
			if($location_info['balance_type']==0){
			$order['balance_price']=$result['pay_total_price']*(1-$location_info['balance_amount']);
			}elseif($location_info['balance_type']==1){
			$order['balance_price']=$result['pay_total_price']-$location_info['balance_amount'];
			}


			$now = NOW_TIME;
			$order['supplier_id'] = $location_info['supplier_id'];
			$order['location_id'] = $location_id;
			$order['create_time'] = $now;
			$order['order_status'] = 0;  //结单状态，新单都为零， 等下面的流程同步订单状态
			$order['confirm_status'] = 0;  //订单商家确认状态，0:未确认（未接单，用户未付款可以取消，已付款可直接退款），1.已确认（商家已接单,客户可与商家联系，申请退款），2.已配送（商家已配送,不可以申请退款）
			$order['pay_status'] = 0;  //支付状态，新单都为零， 等下面的流程同步订单状态
			$order['total_price'] = $result['pay_total_price'];  //应付总额  商品价  + 打包费 + 支付手续费 + 配送费

			$order['bank_id'] = $bank_id;
			$order['menu_price'] = $location_dc_cart['total_data']['total_price'];
			$order['package_price'] = $result['package_fee'];
			$order['delivery_price'] = $result['delivery_fee'];
			$order['payment_fee'] = $result['payment_fee'];
			$order['pay_amount'] = $result['ecv_money']; // 已付总额，pay_amount>=total_price时表示支付成功
			$order['pay_time'] = 0;   //支付成功时间
			$order['online_pay'] = 0;  //在线支付的额度
			$order['ecv_id'] = $ecvid;
			$order['ecv_money'] = $result['ecv_money'];
			$order['account_money'] = 0;
			$order['payment_id'] = $payment_id;  //支付方式ID，0表示在线支付，1表示货到付款
			$order['user_id'] = $user_id;
			$order['xpoint'] = $consignee_info['xpoint'];
			$order['ypoint'] = $consignee_info['ypoint'];
			$order['address'] = $consignee_info['address'];
			$order['api_address'] = $consignee_info['api_address'];
			$order['consignee'] = $consignee;
			$order['mobile'] = $mobile;
			if($order_delivery_time==''){
				$order['order_delivery_time']==0;
			}elseif($order_delivery_time==1){  //1表示尽快送达
				$order['order_delivery_time'] = $order_delivery_time;
			}else{
				$order['order_delivery_time'] = to_timespan($order_delivery_time);
			}

			$order['dc_comment'] = $dc_comment;  //外卖预订的客户留言
			$order['invoice'] = $invoice;
			$order['location_name'] = $location_info['name'];  //门店名
			$order['type_del'] = 0;   // 删除状态: 0未经过删除处理 ，1为经过删除
			$user_info = es_session::get("user_info");
			$order['user_name'] = $user_info['user_name']; //会员名称


			do
			{
				$order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
				$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order",$order,'INSERT','','SILENT');
				$order_id = intval($GLOBALS['db']->insert_id());
			}while($order_id==0);

			if($location_dc_table_cart['total_data']['total_price']>0){
				foreach($location_dc_table_cart['cart_list'] as $k=>$v){
					$goods_item['name'] =$v['name'];
					//$goods_item['icon'] = 0;
					$goods_item['menu_id'] = $v['menu_id'];
					$goods_item['supplier_id'] = $location_info['supplier_id'];
					$goods_item['location_id'] = $location_id;
					$goods_item['order_id'] = $order_id;
					$goods_item['user_id'] = $user_id;
					$goods_item['num'] = $v['num'];
					$goods_item['unit_price'] = $v['unit_price'];
					$goods_item['total_price'] = $v['total_price'];
					$goods_item['order_sn'] = $order['order_sn'];
					$goods_item['table_time_id'] = $v['table_time_id'];
					$goods_item['type'] = 0;
					$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order_menu",$goods_item,'INSERT','','SILENT');


				}
			}
			if($location_dc_cart['total_data']['total_price']>0){
				foreach($location_dc_cart['cart_list'] as $k=>$v){
					$goods_item['name'] =$v['name'];
					//$goods_item['icon'] = 0;
					$goods_item['menu_id'] = $v['menu_id'];
					$goods_item['supplier_id'] = $location_info['supplier_id'];
					$goods_item['location_id'] = $location_id;
					$goods_item['order_id'] = $order_id;
					$goods_item['user_id'] = $user_id;
					$goods_item['num'] = $v['num'];
					$goods_item['unit_price'] = $v['unit_price'];
					$goods_item['total_price'] = $v['total_price'];
					$goods_item['order_sn'] = $order['order_sn'];
					$goods_item['table_time_id']=0;
					$goods_item['type'] = 1;
					$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order_menu",$goods_item,'INSERT','','SILENT');
				}
			}

			$order_idx=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."dc_order where id=".$order_id);
			//删除购物车该订单的数据
			$GLOBALS['db']->query("delete from ".DB_PREFIX."dc_cart where user_id=".$user_id." and location_id=".$location_id." and session_id='".es_session::id()."'");
			if($order_idx){
				$root['order_id']=$order_idx;
				$root['payment_method']=$payment_id;
				

				// 订单生成成功后，生成代金劵支付付款单号，同时更新代金劵使用状态
					
				$ecv_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where id=".$ecvid);
				if($ecv_data)
				{
					$ecv_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Voucher'");
					if($ecv_data['money']>$order['total_price'])$ecv_data['money'] = $order['total_price'];
					$payment_notice_id = make_dcpayment_notice($ecv_data['money'],$order_id,$ecv_payment_id,"",$ecv_data['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set is_paid=1,pay_time=".NOW_TIME." where id=".$payment_notice_id);
					$GLOBALS['db']->query("update ".DB_PREFIX."ecv set use_count = use_count + 1 where id=".$ecvid);
					
				}
				
				return output($root,1,'订单生成成功');
				
			}else{
				return output($root,0,'订单生成失败');
			}
		}
	}


	/**
	 * 向购物车里写入订座信息，兼容原来的make_order
	 * @param int $location_id   门店id
	 * @param string $rs_date          订座的日期
	 * @param int $table_time_id 订座id
	 * @return boolean 是否可预订
	 */
	public function add_table_cart($location_id, $rs_date, $table_time_id)
	{
		$supplier_id=$GLOBALS['db']->getOne("select supplier_id from ".DB_PREFIX."supplier_location where id=".$location_id);
		$session_id=es_session::id();
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$table_time_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_rs_item_time where id=".$table_time_id);
	
		$is_count=item_day($location_id,$rs_date,$table_time_info['item_id'],$table_time_id,$table_time_info['total_count']);//判断给定的日期是否有库存
		
		return $is_count;

		/*if($is_count) {
			$session_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_cart where session_id= '".$session_id."' ");
				
			foreach($session_list as $k=>$v) {   //删去已订购的桌子
				if($v["table_time_id"]>0) {
					$sql = "DELETE FROM ".DB_PREFIX."dc_cart WHERE session_id='".$session_id."' and id=".$v['id'];
					$GLOBALS['db']->query($sql);	
				}
			}
	
			$user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;
	
			$time=$rs_date." ".$table_time_info['rs_time'];
				
			$time=to_timespan($time);
	
			$cart_item['session_id'] = $session_id;
			$cart_item['supplier_id'] = $supplier_id;
			$cart_item['location_id'] = $location_id;
			$cart_item['user_id'] = $user_id;
			$cart_item['add_time'] = NOW_TIME;
				
			$table_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_rs_item where id=".$table_time_info['item_id']);
				
			$cart_item['unit_price']=$table_info['price'];
			$cart_item['num'] = 1;
			$cart_item['total_price'] = $table_info['price']*$cart_item['num'];
			$cart_item['icon'] = $table_info['icon'];
			$cart_item['name'] = $table_info['name'];
			$cart_item['table_time'] = $time;
			$cart_item['table_time_id'] = $table_time_id;
			$cart_item['menu_id'] = $table_info['id'];
				
			$GLOBALS['db']->autoExecute(DB_PREFIX."dc_cart",$cart_item);
			return true;
		}
		return false;*/
	}

	

	/**
	 * 继续支付的页面
	 * 测试页面：http://localhost/o2onew/mapi/index.php?ctl=dcorder&act=order&r_type=2&id=43
	 * 输入：
	 * id: int 订单ID
	 * payment:int 付款方式的ID
	 * all_account_money：int 当有选择余额支付时，all_account_money=1，当没有选择余额支付时,all_account_money=0;
	 * 
	 * 输出：
	 * id:int 订单ID
	 * status：为申请退款操作的状态，status=0,失败;status=1,成功
	 * info:返回的提示信息
	 * account_pay:余额支付
	 * payment_list支付方式列表
	 *  Array
	 (

	            [0] => Array
	                (
	                    [id] => 29
	                    [code] => Upacpapp
	                    [logo] => 
	                    [name] => 银联支付
	                )

	            [1] => Array
	                (
	                    [id] => 34
	                    [code] => Walipay
	                    [logo] => 
	                    [name] => 支付宝wap支付
	                )
	 )

	 location_info：商家信息
	 Array
	 (
		 [id] => 21
		 [name] => 桥亭活鱼小镇（万象城店）
		 [dc_allow_ecv] => 0
	 )

	 order_info：订单信息
	 其中，total_price：总计，menu_price：商品金额，不包括预订的定金，package_price打包费，delivery_price：配送费，ecv_money：红包支付的金额
	 pay_amount：已经支付的金额，promote_amount优惠的金额，account_money余额支付的金额
	 $total_price:订单总额，$payment_fee：手续费，$pay_amount:已付金额，$account_money:余额支付的金额，$pay_price：应付金额
	 payment:int 如果大于0，表示当前的第三方支付ID
	 all_account_money:int 是否使用余额支付，等于1，使用，等于0，不使用

	 Array
		 (
			 [id] => 41
			 [order_sn] => 2015081102152563
			 [supplier_id] => 43
			 [location_id] => 41
			 [create_time] => 1439244925
			 [order_status] => 0
			 [confirm_status] => 0
			 [pay_status] => 0
			 [total_price] => 24.2000
			 [menu_price] => 20.0000
			 [package_price] => 0.2000
			 [delivery_price] => 1.0000
			 [promote_str] =>
			 [pay_amount] => 0.0000
			 [pay_time] => 0
			 [online_pay] => 0.0000
			 [ecv_id] => 0
			 [ecv_money] => 0.000
			 .
			 .
			 .
			 .
			 .
			
		 )
	 *
	 *
	 *
	 */

	public function old_order()
	{
		global_run();
		
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$root['user_login_status']=0;
			return output($root);
		}else{
			$root['user_login_status']=1;
			$user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;

			$id = intval($GLOBALS['request']['id']);
			$root['id']=$id;
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where id = ".$id." and type_del = 0 and pay_status=0 and order_status=0 and is_cancel=0 and user_id =".intval($GLOBALS['user_info']['id']));
			$root['is_rs']=$order_info['is_rs'];
			if(!$order_info)
			{	$root['is_return']=1;
				return output($root,0,$GLOBALS['lang']['INVALID_ORDER_DATA']);


			}else{
				if(NOW_TIME-$order_info['create_time'] > 900){
					$root['is_return']=1;
					$root['time_status']=1;
					return output($root,0,"请在下单后15分钟内支付");
				}
					
			}


			//如果之前已经享受过首单立减，就不能享受了
			$is_ordered=$GLOBALS['db']->getOne("select dc_is_share_first from ".DB_PREFIX."user where id=".$user_id);
			$order_info['promote_str']=unserialize($order_info['promote_str']);
			

			foreach($order_info['promote_str'] as $k=>$v){
			
				if($v['class_name']=='FirstOrderDiscount'){

					if($is_ordered==1){
						$GLOBALS['db']->query("update ".DB_PREFIX."dc_order set promote_str='' , promote_amount=0 where id=".$id);
							
					}
				}elseif($v['class_name']=='PayOnlineDiscount'){
					//取出上线支付优惠接口配置
					$promote_obj = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_promote where class_name='PayOnlineDiscount'");
					$promote_cfg = unserialize($promote_obj['config']);
					//如果今天上线支付优惠已经用完，就不能享受了
					$begin_time=to_timespan(to_date(NOW_TIME,"Y-m-d"));
					$end_time=$begin_time+3600*24-1;
					
					$today_order_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_supplier_order where user_id=".$user_id ." and create_time > ".$begin_time." and create_time < ".$end_time." and pay_status=1 and payment_id=0 and promote_amount > 0 and is_cancel=0 and refund_status=0");
					if($promote_cfg['daily_limit'] <= $today_order_count && $promote_cfg['daily_limit'] > 0){
						$GLOBALS['db']->query("update ".DB_PREFIX."dc_order set promote_str='' , promote_amount=0 where id=".$id);
					}
					
				}
			}


			$location_id=$order_info['location_id'];
			$location_info=$GLOBALS['db']->getRow("select id,name,dc_allow_ecv,is_close from ".DB_PREFIX."supplier_location where id=".$location_id);

			if($location_info['is_close']==1){
				$root['is_return']=1;
				return output($root,0,$GLOBALS['lang']['DC_LOCATION_CLOSE']);

			}

			require_once(APP_ROOT_PATH."system/model/dc.php");

			$order_menu=unserialize($order_info['order_menu']);

			$location_dc_table_cart=$order_menu['rs_list'];
			$location_dc_cart=$order_menu['menu_list'];




			if(count($location_dc_table_cart['cart_list'])==0 && count($location_dc_cart['cart_list'])==0 ){
				$root['is_return']=1;
				return output($root,0,$GLOBALS['lang']['DC_CART_EMPTY']);

			}else{

				$cart_menu_info=$location_dc_cart;
				//当有预定餐桌时，商品订单总额必须大等于餐桌定金
				if($location_dc_table_cart['total_data']['total_price'] > 0){
						
					if($cart_menu_info['total_price'] > 0 && $location_dc_table_cart['total_data']['total_price'] > $cart_menu_info['total_price']){
						$root['is_return']=1;
						return output($root,0,'点菜金不足');
					}
				}else{
						
					$id_arr=array($location_info['id']=>array('id'=>$location_info['id'],'distance'=>$location_info['distance']));
					$location_delivery_info=get_location_delivery_info($id_arr);

					$delivery_time=$this->get_delivery_time($location_id);
					if(!$delivery_time){
					
						$root['is_return']=1;
						return output($root,0,'不在商家配送时间内');
					}
					
					if($location_delivery_info[$location_id]['is_free_delivery']==2){
						$root['is_return']=1;
						return output($root,0,'不在配送范围内');
					}

					if($location_delivery_info[$location_id]['is_free_delivery']==0 && $location_delivery_info[$location_id]['start_price']>$location_dc_cart['total_data']['total_price']){
						$root['is_return']=1;
						return output($root,0,'未达到起送价');
					}
				}

				
				
				
				//输出支付方式
				if ($GLOBALS['request']['from'] == 'wap')
				{	
					
					//支付列表
					$sql = "select id, class_name as code, logo,name from ".DB_PREFIX."payment where ( online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
				}
				else
				{	
					//支付列表
					$sql = "select id, class_name as code, logo,name from ".DB_PREFIX."payment where ( online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
				}

				$root['page_title']='支付订单';
				$root['location_info']=$location_info;
				//总计
				$total_price=$order_info['total_price']-$order_info['payment_fee'];
				$root['total_price']=$total_price-$order_info['ecv_money']-$order_info['promote_amount'];
				//已付
				$root['pay_amount']=$order_info['pay_amount']-$order_info['ecv_money'];
					
				$pay_price=$root['total_price']-$root['pay_amount'];
				//支付方式ID，0表示在线支付,1表示货到付款
				$root['payment_id']=$order_info['payment_id'];
				if($order_info['payment_id']==0){
					
				
				if(allow_show_api())
				{
					$payment_list = $GLOBALS['db']->getAll($sql);
				}
				foreach($payment_list as $k=>$v)
				{
					$directory = APP_ROOT_PATH."system/Dc_payment/";
					$file = $directory. '/Dc_' .$v['code']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = 'Dc_'.$v['code']."_payment";
						$payment_object = new $payment_class();
						$payment_list[$k]['name'] = $payment_object->get_display_code();
					}
						
					if($v['logo']!="")
						$payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'],40,40,1));
				}
					
				sort($payment_list);

				$root['payment_list'] = $payment_list;


				$all_account_money=$GLOBALS['request']['all_account_money'];
				$pid = intval($GLOBALS['request']['payment']);
				if($GLOBALS['request']['from']=='wap'){
					$type='wap';
				}
				
				$result=$this->count_order_total($user_id,$id,$all_account_money,$pid,$type);
				
				$account_money=$result['account_money'];
				$payment_fee=$result['payment_fee'];
				//手续费
				$root['payment']=$result['payment'];
				$root['all_account_money']=$result['all_account_money'];
				$root['account_money']=$account_money;
				$root['payment_fee']=$payment_fee;
				$root['pay_price']=$pay_price=$result['pay_price'];
				$root['is_return']=$result['is_return'];
				
				$root['total_price']=$result['total_price'];
				//已付
				$root['pay_amount']=$result['pay_amount'];
				
				$root['account_pay']=$result['account_pay'];
				
				}else{
					$root['pay_price']=$pay_price;
				}
				
				//$root['order_info']=$order_info;
				return output($root);

			}


		}
	}

	public function order()
	{	
		do {
			assign_form_verify(); // 不知道这个函数是干嘛的
			$root = array();
			$status = 0;
			$info = '';
			$id = intval($GLOBALS['request']['id']);
			$user_login_status = check_save_login();	
			if($user_login_status == LOGIN_STATUS_LOGINED) {
				$user_info = $GLOBALS['user_info'];
				$user_id = $user_info['id'];

				$order_sql = 'SELECT * FROM '.DB_PREFIX.'dc_order WHERE id='.$id.' AND type_del = 0 AND user_id='.$user_id;
				$order_info = $GLOBALS['db']->getRow($order_sql);
				$root['is_rs'] = $order_info['is_rs'];

				if(!$order_info || $order_info['is_cancel'] != 0) {
					// logger::write($GLOBALS['db']->getLastSql());
					$info = '订单不存在或已取消';
					break;
				}

				if ($order_info['pay_status'] == 1) {
					$info = '订单已经支付';
					break;
				}

				if(NOW_TIME - $order_info['create_time'] > 900){ // 外卖订单超过15分钟未支付
					$info = '订单已超时，请重新下单';
					// 将订单 is_cancel状态变更
					/*if ($order_info['is_cancel'] == 0) {
						$update = array('is_cancel' => 1);
						$GLOBALS['db']->autoExecute(DB_PREFIX.'dc_order', $update, 'UPDATE', 'id='.$id, 'SILENT');
					}*/
					break;
				}

				if ($order_info['is_rs']) { // 判断预订订单是否提前一小时
					$order_menu = unserialize($order_info['order_menu']);
					$table_time = $order_menu['rs_list']['cart_list'][0]['table_time'];
					if ($table_time - NOW_TIME < 3600) {
						$info = '预订需提前一小时';
						break;
					}
				}

				// 商户状态验证
				$location_id = $order_info['location_id'];
				$location_info = $GLOBALS['db']->getRow("select id,name,dc_allow_ecv,is_close,is_payonlinediscount,is_firstorderdiscount from ".DB_PREFIX."supplier_location where id=".$location_id);

				if($location_info['is_close']==1){
					$info = $GLOBALS['lang']['DC_LOCATION_CLOSE'];
					break;

				}

				//总计
				$total_price = $order_info['total_price'];
				$root['total_price']=$total_price-$order_info['ecv_money']-$order_info['promote_amount'];

				// 验证当前的优惠是否仍有效
				$promote_str = unserialize($order_info['promote_str']);
				if ($promote_str) {
					$currentProm = $this->getDcPromote($location_info, $user_info, $total_price);
					if ($promote_str[0]['class_name'] != $currentProm['promote_info']['class_name']) {
						$info = '优惠信息已变更，请重新下单';
						break;
					}
				}

				//已付
				$root['pay_amount']=$order_info['pay_amount']-$order_info['ecv_money'];
				$pay_price=$root['total_price']-$root['pay_amount'];

				$is_weixin = isWeixin();
				/*if (APP_INDEX=='wap'  && $is_weixin) { // 微信
				    $sql = "select id, class_name as code, logo, fee_amount,fee_type from ".DB_PREFIX."payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
				} elseif (APP_INDEX=='wap' && !$is_weixin) { // wap
				    $sql = "select id, class_name as code, logo, fee_amount,fee_type from ".DB_PREFIX."payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1 and class_name !='Wwxjspay'";
				} else { // app
				    $sql = "select id, class_name as code, logo, fee_amount,fee_type from ".DB_PREFIX."payment where (online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
				}*/
				$paymentSql = 'SELECT id, class_name AS code, logo, fee_amount, fee_type FROM '.DB_PREFIX.'payment WHERE is_effect=1 AND ';
				if (APP_INDEX == 'wap') {
					$paymentSql .= 'online_pay in (2,4,5) ';
					if (!$is_weixin) {
						$paymentSql .= ' AND class_name != "Wwxjspay"';
					}
				} else {
					$paymentSql .= 'online_pay in (3,4,5)';
				}

				if(allow_show_api()) {
					$payment_list = $GLOBALS['db']->getAll($paymentSql);
				}

				foreach($payment_list as $k => $v) {
					$directory = APP_ROOT_PATH."system/dc_payment/";
					$file = $directory. '/Dc_' .$v['code']."_payment.php";
					if(file_exists($file)) {
						require_once($file);
						$payment_class = 'Dc_'.$v['code']."_payment";
						$payment_object = new $payment_class();
						$payment_list[$k]['name'] = $payment_object->get_display_code();
					}
						
					if($v['logo']!="") {
						$payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'],40,40,1));
					}

					if($v['fee_type']) {
                		$payment_list[$k]['fee_amount']= $order_info['total_price']*$v['fee_amount'];
					}
				}
					
				sort($payment_list);

				$root['payment_list'] = $payment_list;

				$all_account_money=$GLOBALS['request']['all_account_money'];
				$pid = intval($GLOBALS['request']['payment']);
				if($GLOBALS['request']['from']=='wap'){
					$type='wap';
				}
				
				$result = $this->count_order_total($user_id,$id,$all_account_money,$pid,$type);
				
				$account_money = $result['account_money'];
				$payment_fee = $result['payment_fee'];
				//手续费
				$root['payment'] = $result['payment'];
				$root['all_account_money'] = $result['all_account_money'];
				$root['account_money'] = $account_money;
				$root['payment_fee'] = $payment_fee;
				$root['pay_price'] = $pay_price = $result['pay_price'];
				$root['is_return'] = $result['is_return'];
				
				$root['total_price'] = $result['total_price'];
				//已付
				$root['pay_amount'] = $result['pay_amount'];
				
				$root['account_pay'] = $result['account_pay'];

				$status = 1;
			}
		} while(0);
		$root['id'] = $id;
		$root['page_title'] = '收银台';
		$root['user_login_status'] = $user_login_status;
		return output($root, $status, $info);
	}


	/**
	 *  继续支付页面，点击 “确认支付”后的提交地址
	 * 	输入：http://localhost/o2onew/mapi/index.php?ctl=dcorder&act=order_done&r_type=2&id=43
	 *
	 * id：订单ID
	 * payment:付款方式的ID
	 * all_account_money：当有选择余额支付时，all_account_money=1，当没有选择余额支付时,all_account_money=0;
	 * 
	 * 
	 *
	 * 输出：
	 * pay_status 支付返回状态
	 *  pay_status=0 ，正常支付，还有部分未完成
	 *  pay_status=1 ，正常支付，支付完成
	 *  pay_status=2，付款单号重复支付,当前支付的退到会员帐户
	 *  pay_status=5 ，货到付款
	 * order_id : 订单id
	 * payment_notice_id：int 支付单号的ID,只有pay_status=0和pay_status=2时，才有这个值输出
	 */
	public function old_order_done()
	{

		require_once(APP_ROOT_PATH."system/model/dc.php");
		global_run();
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$root['user_login_status']=0;
			return output($root);
		}else{
			$root['user_login_status']=1;
			$user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;
		
			$id = intval($GLOBALS['request']['id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where id = ".$id." and type_del = 0 and pay_status=0 and order_status=0 and is_cancel=0 and user_id =".intval($GLOBALS['user_info']['id']));

			$all_account_money=$GLOBALS['request']['all_account_money'];
			$pid = intval($GLOBALS['request']['payment']);
			
			$result=$this->count_order_total($user_id,$id,$all_account_money,$pid);
			
			$account_money=$result['account_money'];
			$payment_fee=$result['payment_fee'];
			$pay_price=$result['pay_price'];
			
			//手续费
			/*
			$root['payment']=$pid;
			$root['all_account_money']=$all_account_money;
			$root['account_money']=$account_money;
			$root['payment_fee']=$payment_fee;
			$root['pay_price']=$pay_price=$result['pay_price'];
			$root['is_return']=$result['is_return'];
			*/
			
			if(!$order_info)
			{
				return output($root,0,$GLOBALS['lang']['INVALID_ORDER_DATA']);
			
			
			}else{
				if(NOW_TIME-$order_info['create_time'] > 900){
					$root['is_return']=1;
					return output($root,0,$GLOBALS['lang']['DC_ORDER_OUT_TIME']);
				}
			
			}

			if($order_info['payment_id']==0){
				if($pay_price<0){
					return output($root,0,'输入金额有误');
				}
				

				if($pid==0 && $all_account_money==0 && $pay_price > 0){
					return output($root,0,$GLOBALS['lang']['PLEASE_SELECT_PAYMENT']);
				}
				
				if($pid==0 && $pay_price > 0){
					return output($root,0,$GLOBALS['lang']['PLEASE_SELECT_PAYMENT']);
				}
				
				if($all_account_money==0 && $account_money>0){
					
					return output($root,0,'余额支付有误');
				}
			}
			

		
		
			$location_id=$order_info['location_id'];
			$location_info=$GLOBALS['db']->getRow("select id,name,dc_allow_ecv,is_close from ".DB_PREFIX."supplier_location where id=".$location_id);
		
			if($location_info['is_close']==1){
		
				return output($root,0,$GLOBALS['lang']['DC_LOCATION_CLOSE']);
		
			}
		
			require_once(APP_ROOT_PATH."system/model/dc.php");
		
			$order_menu=unserialize($order_info['order_menu']);
		
			$location_dc_table_cart=$order_menu['rs_list'];
			$location_dc_cart=$order_menu['menu_list'];
		
		

		
			if(count($location_dc_table_cart['cart_list'])==0 && count($location_dc_cart['cart_list'])==0 ){
				return output($root,0,$GLOBALS['lang']['DC_CART_EMPTY']);
		
			}else{
		
				$cart_menu_info=$location_dc_cart;
				//当有预定餐桌时，商品订单总额必须大等于餐桌定金
				if($location_dc_table_cart['total_data']['total_price'] > 0){
					
					//验证预订位置的库存
					foreach($location_dc_table_cart['cart_list'] as $kk=>$vv){
						$rs_date=to_date($vv['table_time'],"Y-m-d");
						$total_count=$GLOBALS['db']->getOne("select total_count from ".DB_PREFIX."dc_rs_item_time where  id=".$vv['table_time_id']);
						$rs_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_rs_item_day where time_id=".$vv['table_time_id']." and rs_date='".$rs_date."'");
						if(!$rs_info && $total_count==0){
							return output($root,0,$GLOBALS['lang']['DC_TABLE_OUT_STOCK']);
							break;
						}elseif($rs_info && $rs_info['buy_count'] + $vv['num'] > $total_count ){
							return output($root,0,$GLOBALS['lang']['DC_TABLE_OUT_STOCK']);
							break;
						}
					}
						
					if($cart_menu_info['total_price'] > 0 && $location_dc_table_cart['total_data']['total_price'] > $cart_menu_info['total_price']){
						return output($root,0,'点菜金不足');
					}
				}else{
						
					$id_arr=array($location_info['id']=>array('id'=>$location_info['id'],'distance'=>$location_info['distance']));
					$location_delivery_info=get_location_delivery_info($id_arr);
		
					$delivery_time=$this->get_delivery_time($location_id);
					if(!$delivery_time){
					
						$root['is_return']=1;
						return output($root,0,'不在商家配送时间内');
					}
					
					if($location_delivery_info[$location_id]['is_free_delivery']==2){
						return output($root,0,'不在配送范围内');
					}
		
					if($location_delivery_info[$location_id]['is_free_delivery']==0 && $location_delivery_info[$location_id]['start_price']>$location_dc_cart['total_data']['total_price']){
						return output($root,0,'未达到起送价');
					}
				}
		
			}	
				
			

			//如果之前已经享受过首单立减，就不能享受了
			$is_ordered=$GLOBALS['db']->getOne("select dc_is_share_first from ".DB_PREFIX."user where id=".$user_id);
			$order_info['promote_str']=unserialize($order_info['promote_str']);
			if($order_info['promote_str']['FirstOrderDiscount'] && $is_ordered==1){
				$GLOBALS['db']->query("update ".DB_PREFIX."dc_order set promote_str='' , promote_amount=0 where id=".$id);
				return output($root,0,'首单立减优惠已使用');
				
			}
			
			
			//取出上线支付优惠接口配置
			if($order_info['promote_str']['PayOnlineDiscount']){
				$promote_obj = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_promote where class_name='PayOnlineDiscount'");
				$promote_cfg = unserialize($promote_obj['config']);
				//如果今天在线支付优惠已经用完，就不能享受了
				$begin_time=to_timespan(to_date(NOW_TIME,"Y-m-d"));
				$end_time=$begin_time+3600*24-1;
				
				$today_order_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_supplier_order where user_id=".$user_id ." and create_time > ".$begin_time." and create_time < ".$end_time." and pay_status=1 and payment_id=0 and promote_amount > 0 and is_cancel=0 and refund_status=0");
				if($promote_cfg['daily_limit'] <= $today_order_count && $promote_cfg['daily_limit'] > 0){
					$GLOBALS['db']->query("update ".DB_PREFIX."dc_order set promote_str='' , promote_amount=0 where id=".$id);
					return output($root,0,'今日在线优惠已用完');
				}
			}
			
			

			$now = NOW_TIME;
			
			$order['payment_fee'] = $payment_fee;
			$total_price=$order_info['total_price']-$order_info['payment_fee'];	
			$order['total_price'] = $total_price+$payment_fee; 
		 


			$user_info = es_session::get("user_info");
			$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order",$order,'UPDATE','id='.$id,'SILENT');

			//如果是在线支付
			$order_id=$id;
			if($order_info['payment_id']==0){

				//1. 余额支付
				$account_pid = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
				if(floatval($account_money) > 0 && $all_account_money==1)
				{
					$payment_notice_id = make_dcpayment_notice($account_money,$order_id,$account_pid);
					require_once(APP_ROOT_PATH."system/dc_payment/Dc_Account_payment.php");
					$account_payment = new Dc_Account_payment();
					$account_payment->get_payment_code($payment_notice_id);
				}
				
				
				//3. 相应的支付接口
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".$pid);
				if($payment_info && $pay_price> 0)
				{
					$payment_notice_id = make_dcpayment_notice($pay_price,$order_id,$payment_info['id']);
					//创建支付接口的付款单
				}

				$rs = dcorder_paid($order_id);

				if($rs){
					//正常支付，支付完成

					$root['pay_status']=1;
					$root['order_id'] = $order_id;
					return output($root);
						
				}
				else
				{
					if($order_info['pay_status'] == 1)
					{   //付款单号重复支付,当前支付的退到会员帐户
							
						$root['pay_status']=2;
						$root['order_id'] = $order_id;
						$root['payment_notice_id']=$payment_notice_id;
						return output($root);
					}else{ //正常支付，还有部分未完成
						$root['pay_status']=0;
						$root['order_id'] = $order_id;
						$root['payment_notice_id']=$payment_notice_id;

					}
					return output($root);
				}
			}else{
				// 代表货到付款
				$root['pay_status']=5;
				$root['order_id'] = $order_id;
					
				require_once(APP_ROOT_PATH."system/model/dc.php");
				dcorder_paid_done($order_id);
				return output($root);
			}

		}
	}

	public function order_done()
	{
		$root = array();
		$status = 0;
		do {
			$user_login_status = check_save_login();
			if ($user_login_status != LOGIN_STATUS_LOGINED) {
				$info = '未登录';
				break;
			}
			$user_info = $GLOBALS['user_info'];
			$request = $GLOBALS['request'];
			$id = intval($request['id']);
			$order_sql = 'SELECT * FROM '.DB_PREFIX.'dc_order WHERE id='.$id.' AND type_del = 0 AND user_id='.$user_info['id'];
			$order_info = $GLOBALS['db']->getRow($order_sql);
			// $order_info = $this->update_order_status($order_info);
			$location_info = $GLOBALS['db']->getRow('SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$order_info['location_id']);

			// 验证当前的优惠是否仍有效
			$promote_str = unserialize($order_info['promote_str']);
			if ($promote_str) {
				$currentProm = $this->getDcPromote($location_info, $user_info, $order_info['total_price']);
				if ($promote_str[0]['class_name'] != $currentProm['promote_info']['class_name']) {
					$info = '优惠信息已变更，请重新下单';
					break;
				}
			}

			if(!$order_info || $order_info['is_cancel'] != 0) {

				$info = '订单不存在或已取消';
				break;
			}

			if ($order_info['pay_status'] == 1) {
				$info = '订单已经支付';
				break;
			}

			$root['is_rs'] = $order_info['is_rs'];
			if(NOW_TIME - $order_info['create_time'] > 900 && $order_info['payment_id'] == 0) { // 在线支付超过15分钟未支付
				$info = '订单已超时，请重新下单';
				// 将订单 is_cancel状态变更
				/*if ($order_info['is_cancel'] == 0) {
					$update = array('is_cancel' => 1);
					$GLOBALS['db']->autoExecute(DB_PREFIX.'dc_order', $update, 'UPDATE', 'id='.$id, 'SILENT');
				}*/
				break;
			}

			if ($order_info['is_rs']) { // 判断预订订单是否提前一小时
				$order_menu = unserialize($order_info['order_menu']);
				$table_time = $order_menu['rs_list']['cart_list'][0]['table_time'];
				if ($table_time - NOW_TIME < 3600) {
					$info = '预订需提前一小时';
					break;
				}
			}

			require_once(APP_ROOT_PATH."system/model/dc.php");
			// 货到付款
			if ($order_info['payment_id'] == 1) { // 0:在线支付  1:到付
				$root['pay_status']=5;
				$root['order_id'] = $id;
				dcorder_paid_done($id);
				break;
			}

			$payByAccount = intval($request['all_account_money']); // 是否余额支付
			$paymentId = intval($request['payment']); // 支付方式id
			$pre_pay_price = $order_info['total_price']-$order_info['ecv_money']-$order_info['promote_amount'];
			if ($pre_pay_price == 0) {
				$payByAccount = 1;
			}
			if ($paymentId == 0 && $payByAccount == 0) {
				// 未选择支付方式
				$info = $GLOBALS['lang']['PLEASE_SELECT_PAYMENT'];
				break;
			}

			// 计算需要支付的金额
			$paymentSql = 'SELECT * FROM '.DB_PREFIX.'payment WHERE is_effect=1';
			if ($payByAccount) { // 如果选择余额支付
				$paymentSql .= ' AND class_name="Account"';
			} else {
				$paymentSql .= ' AND id='.$paymentId;
			}
			$payment = $GLOBALS['db']->getRow($paymentSql);
			if (empty($payment)) {
				$info = '支付方式错误';
				break;
			}
			// 需要支付的金额+支付的手续费
			$pay_price = $pre_pay_price - $order_info['pay_amount'];// $order_info['total_price'] - $order_info['pay_amount'];
			if($payment['fee_type'] == 0) {//定额	
				$payment_fee = $payment['fee_amount'];
			} else {//比率	
				$payment_fee = $pay_price * $payment['fee_amount'];
			}
			$pay_price += $payment_fee;  // 需要支付的金额
			if ($pay_price <= 0) {
				$info = '支付金额异常';
				break;
			}

			if ($payByAccount) { // 余额支付，判断余额支付够
				if ($pre_pay_price > $user_info['money']) {
					$info = '当前余额不足';
					break;
				}
			}
			
			$payment_notice_id = make_dcpayment_notice($pay_price,$id,$payment['id']);
			if ($payByAccount) {
				require_once(APP_ROOT_PATH."system/dc_payment/Dc_Account_payment.php");
				$account_payment = new Dc_Account_payment();
				$account_payment->get_payment_code($payment_notice_id);
				$info = '余额支付成功';
			}
			
			$rs = dcorder_paid($id);
			
			if ($rs) { //正常支付，支付完成
				$root['pay_status']=1;
				$root['order_id'] = $id;
			} else {
				$root['pay_status']=0;
				$root['order_id'] = $id;
				$root['payment_notice_id']=$payment_notice_id;
			}
			$status = 1;
		} while(0);
		$root['user_login_status'] = $user_login_status;
		$root['app_index'] = APP_INDEX;
		return output($root, $status, $info);
	}

	/**
	 * 更新订单的状态: 如可享受的优惠，订单超时等
	 * @param int $order_id 订单ID
	 * @return null
	 */
	private function update_order_status($order, $user_info)
	{
		if (empty($order) || $order['is_cancel'] == 1 || $order['pay_status'] == 1 || $order['payment_id'] == 0) {
			// 到付或者已取消或者已支付的订单不更新
			return $order;
		}
		$update = array();
		if (NOW_TIME - $order['create_time'] > 900) { // 超过15分钟未支付
			$update = array('is_cancel' => 1);
		} elseif ($order['promote_amount'] > 0) {
			$location_sql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$order['location_id'];
			$location_info = $GLOBALS['db']->getRow($location_sql);
			$dcPromote = $this->getDcPromote($location_info, $user_info, $order['total_price']);
			$update = array('promote_amount' => $dcPromote['promote_price']);
		}
		if (!empty($update)) {
			$result = $GLOBALS['db']->autoExecute(DB_PREFIX.'dc_order', $update, 'UPDATE', 'id='.$order['id'], 'SILENT');
			if ($result) {
				$order = array_merge($order, $update);
			} else {
				logger::write('订单状态更新失败(dcorder->update_order_status):'.$GLOBALS['db']->error().'--'.$GLOBALS['db']->getLastSql());
			}
		}
		return $order;
	}


	

	/**
	 *  获取外卖订单页面配送时间段(只适合于订餐的时间)
	 * @param unknown_type $time_span 多少秒一个时间段，默认900秒，15分钟
	 * @param unknown_type $delivery_time 自定义中午和早上配送时间段
	 * @return array 返回配送时间段数组
	 */
	public function get_delivery_time_eat($location_id,$time_span=900,$delivery_time=array('am'=>array("11:00","14:00"),'pm'=>array("16:00","20:00"))){
	
		$delivery_time_span=array();
		foreach($delivery_time as $delivery_time_item){
			$delivery_time_span[]=$this->get_time_span(to_timespan($delivery_time_item[0]),to_timespan($delivery_time_item[1]),$time_span);
		}

		$delivery_time_new=array();
		foreach($delivery_time_span as $delivery_time_span_row){
	
			foreach($delivery_time_span_row as $delivery_time_span_one){
	
				$delivery_time_new[]=$delivery_time_span_one;
			}
		}
		$delivery_time_arr=array();
		foreach($delivery_time_new as $delivery_time_row){
			if(NOW_TIME + 3600 <= $delivery_time_row){
			//	$delivery_time_arr[]= to_date($delivery_time_row,"H:i");
				$delivery_time_arr[]= $delivery_time_row;
			}
		}
		$open_time_info=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_supplier_location_open_time where location_id=".$location_id);

		if($open_time_info){
		$open_time_arr=array();
		foreach($open_time_info as $kp=>$vp){
			$open_time_row=array();
			$open_time_row['begin_time']=to_timespan($vp['begin_time_h'].":".$vp['begin_time_m']);
			$open_time_row['end_time']=to_timespan($vp['end_time_h'].":".$vp['end_time_m']);
			$open_time_arr[]=$open_time_row;
	
		}
		}
		$open_time_arr_new=array();
		foreach($delivery_time_arr as $k=>$v){
			if($open_time_info){
				foreach($open_time_arr as $kp=>$vp){
					if($v >= $vp['begin_time'] && $v <= $vp['end_time']){
						$open_time_arr_new[]=to_date($v,"H:i");
						break;
							
					}
				}	
					
			}else{
				
				$open_time_arr_new[]=to_date($v,"H:i");
			}
		}

		return $open_time_arr_new;
	}
	
	
	/**
	 *  获取外卖订单页面配送时间段
	 * @param unknown_type $time_span 多少秒一个时间段，默认900秒，15分钟
	 * @param unknown_type $delivery_time 自定义中午和早上配送时间段
	 * @return array 返回配送时间段数组
	 */
	public function get_delivery_time($location_id,$time_span=900,$delivery_time=array('am'=>array("00:00","23:45"))){
	
		$delivery_time_span=array();
		foreach($delivery_time as $delivery_time_item){
			$delivery_time_span[]=$this->get_time_span(to_timespan($delivery_time_item[0]),to_timespan($delivery_time_item[1]),$time_span);
		}
	
		$delivery_time_new=array();
		foreach($delivery_time_span as $delivery_time_span_row){
	
			foreach($delivery_time_span_row as $delivery_time_span_one){
	
				$delivery_time_new[]=$delivery_time_span_one;
			}
		}
		$delivery_time_arr=array();
		foreach($delivery_time_new as $delivery_time_row){
			if(NOW_TIME + 3600 <= $delivery_time_row){
				//	$delivery_time_arr[]= to_date($delivery_time_row,"H:i");
				$delivery_time_arr[]= $delivery_time_row;
			}
		}
		$open_time_info=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_supplier_location_open_time where location_id=".$location_id);
	
		if($open_time_info){
		$open_time_arr=array();
		foreach($open_time_info as $kp=>$vp){
			$open_time_row=array();
			$open_time_row['begin_time']=to_timespan($vp['begin_time_h'].":".$vp['begin_time_m']);
			$open_time_row['end_time']=to_timespan($vp['end_time_h'].":".$vp['end_time_m']);
			$open_time_arr[]=$open_time_row;
	
		}
		}
		$open_time_arr_new=array();
		foreach($delivery_time_arr as $k=>$v){
			if($open_time_info){
				foreach($open_time_arr as $kp=>$vp){
					if($v >= $vp['begin_time'] && $v <= $vp['end_time']){
						$open_time_arr_new[]=to_date($v,"H:i");
						break;
							
					}
				}	
					
			}else{
				
				$open_time_arr_new[]=to_date($v,"H:i");
			}
		}
	
		if(!$open_time_info){  //24小时营业，增加一个24：00
			$open_time_arr_new[]='23:59';
		}
		
		return $open_time_arr_new;
	}

	

	
	
	/**
	 * 获取开始时间和结束时间之间，以$time_span为分隔长度的时间数据
	 * @param unknown_type $begin_time 开始时间，格式为时间戳
	 * @param unknown_type $end_time   结束时间，格式为时间戳
	 * @param unknown_type $time_span  时间分隔长度，以秒为单位
	 * @return array 返回   开始时间和结束时间之间，以$time_span为分隔长度的时间数组
	 */
	public function get_time_span($begin_time,$end_time,$time_span){
		$time_arr=array();
		$num=($end_time-$begin_time)/$time_span+1;
		for($i=0;$i<$num;$i++){
			$time_arr[]=$begin_time+$time_span*$i;
		}
		return $time_arr;
	
	}
	

	/**
	 * 
	 * 获取用户的送餐地址
	 */
	public function load_user_consignee($id)
	{
		global_run();
		
		$consignee_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_consignee where id=".$id);

		return $consignee_info;
	}
	


	/**
	 * 获取红包列表
	 */
	
	public function get_voucher_list()
	{
		$sql = "select e.sn as sn,t.name as name from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as t on e.ecv_type_id = t.id where ".
				" e.user_id = '".$GLOBALS['user_info']['id']."' and (e.begin_time < ".NOW_TIME.") and (e.end_time = 0 or e.end_time > ".NOW_TIME.") ".
				" and (e.use_limit = 0 or e.use_count<e.use_limit)";
		$voucher_list = $GLOBALS['db']->getAll($sql);
		return $voucher_list;	
	} 
	
	
	public function count_order_total($user_id,$order_id,$all_account_money,$pid,$type='app')
	{
		$root=array();
		//余额支付方式
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where id = ".$order_id." and type_del = 0 and pay_status=0 and order_status=0 and is_cancel=0 and user_id =".$user_id);
		
		$user_info = $GLOBALS['db']->getRow("select money from ".DB_PREFIX."user where id = ".$user_id." and is_effect = 1 and is_delete = 0");
		$account_info=$GLOBALS['db']->getRow("select id,class_name from ".DB_PREFIX."payment where class_name = 'Account' and is_effect=1");
		
		if($account_info){
			$account_pay['id']=$account_info['id'];
			$account_pay['name']='余额支付';
			$account_pay['code']=$account_info['class_name'];
			$account_pay['money']=$user_info['money'];
			if($account_pay['money']>0){
				$root['account_pay']=$account_pay;
				if($all_account_money==0 && $pid==0 && $type=='wap'){
					$all_account_money=1;
				}
			}
		
		}
		
		
		$root['is_return']=1;
		$total_price=$order_info['total_price']-$order_info['payment_fee'];
		$root['total_price']=$total_price-$order_info['ecv_money']-$order_info['promote_amount'];
		//已付
		$root['pay_amount']=$order_info['pay_amount']-$order_info['ecv_money'];
		
		$pay_price=$root['total_price']-$root['pay_amount'];
		
		$account_money=0;
		$payment_fee=0;
		/*if($all_account_money==1){
		
			$payment_fee=0;
			if($account_pay['money']>=$pay_price){
				$account_money=$pay_price;
				$pay_price=0;
				$pid=0;
			}else{
				$account_money=$account_pay['money'];
				$pay_price=$pay_price-$account_money;
			}
		}*/
		
			
		if($pay_price>0){
		
			if($pid>0){
		
		
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$pid);
		
				if($payment_info['fee_type']==0) //定额
				{
					$payment_fee = $payment_info['fee_amount'];
				}
				else //比率
				{
					$payment_fee = $pay_price * $payment_info['fee_amount'];
				}
		
			}else{
				$root['is_return']=0;
				//return output($root,0,$GLOBALS['lang']['PLEASE_SELECT_PAYMENT']);
			}
		}
		
			
		//手续费
		$root['payment']=$pid;
		$root['all_account_money']=$all_account_money;
		$root['account_money']=$account_money;
		$root['payment_fee']=$payment_fee;
			
			
		//应付
		$root['pay_price']=$pay_price+=$payment_fee;
		
		return $root;
	}

	public function make_order()
	{
		$root = array();
		$status = 0;
		$info = '';

		do {
			// 先判断是否登录
			$login_status = check_save_login();
			if ($login_status != LOGIN_STATUS_LOGINED) {
				$info = '未登录';
				break;
			}
			// 外卖/订座请求分开处理
			$request = $GLOBALS['request'];
			$dc_type = intval($request['dc_type']);

			if ($dc_type == 1) { // 订座
				$result = $this->resHandle($request);
			} else { // 外卖
				$result = $this->dcHandle($request);
			}

			$status = $result['status'];
			$info = $result['info'];
			$root = $result['data'];
		} while (0);

		$root['user_login_status'] = $login_status;

		return output($root, $status, $info);
	}

	private function dcHandle($request)
	{
		$data = array();
		$status = 0;
		$info = '';
		do {
			$dc_type = -1; // 1: 预订方式，-1:外卖
			$consignee_id = $request['consignee_id'];
			$consignee_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_consignee where id=".$consignee_id);
			if(!$consignee_info){
				$info = '请选择配送地址';
				break;
			}
			$xpoint = $consignee_info['xpoint'];
			$ypoint = $consignee_info['ypoint'];
        	$location_id = intval($request['lid']);
        	$tname='sl';
			if($xpoint>0) {
				$pi = PI;
				$r = EARTH_R;
				$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
			}
			$location_sql = "select ".$tname.".*".$field_append." from ".DB_PREFIX."supplier_location as ".$tname." where ".$tname.".id=".$location_id;
			$dclocation=$GLOBALS['db']->getRow($location_sql);
				
			if(!$dclocation){
				$data['sql'] = $location_sql;
				$data['is_return']=1;
				$info = '无此商家';
				break;
			}

			if($$dclocation['is_close'] == 1){
				$info = $GLOBALS['lang']['DC_LOCATION_CLOSE'];
				break;
			}

			$order_delivery_time = strim($request['order_delivery_time']);
			$delivery_time=$this->get_delivery_time($location_id);
			if(!$delivery_time){
				$data['is_return'] = 1;
				$info = '不在商家配送时间内';
				break;
			}

			require_once(APP_ROOT_PATH."system/model/dc.php");
			$id_arr=array($location_id=>array('id'=>$location_id,'distance'=>$dclocation['distance']));
			$location_delivery_info=get_location_delivery_info($id_arr);
			$location_delivery=array();
			foreach($location_delivery_info as $kk=>$vv){
				$location_delivery=$vv;
			}
			if($location_delivery['is_free_delivery']==2){
				$info = '不在配送范围内';
				break;
			}

			$location_dc_cart = load_dc_cart_list(true,$location_id,$type=1);
			//验证购物车是否为空
			if(empty($location_dc_cart['cart_list'])) {
				// 根据产品要求变更提示信息
				$info = '商品数据改变，请重新下单';
				// $info = $GLOBALS['lang']['CART_EMPTY_TIP'];
				break;
			}

			$data['cart_list'] = $location_dc_cart['cart_list'];
			$data['isPriceChange'] = 0;
			$isPriceChange = $this->isMenuPriceChange($location_dc_cart['cart_list']);
			if ($isPriceChange) {
				$info = '商品价格出错，请重新下单';
				$data['isPriceChange'] = 1;
				break;
			}


			$menu_num = array();
			$menu_id=array();
			foreach($location_dc_cart['cart_list'] as $k=>$v) {
				if ($sv = intval($v['num'])) {
					$menu_num[$v['menu_id']] = $sv;
					$menu_id[] = $v['menu_id'];
				}
			}
			$menu_arr=implode(",",$menu_id);
			$menu_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_menu where id in(".$menu_arr.")");
			$menu_list=data_format_idkey($menu_list,$key='id');
			$location_dc_cart=array();
			$location_dc_cart['total_data']['total_price']=0;
			$location_dc_cart['total_data']['total_count']=0;
			foreach($menu_num as $k=>$v){
				$location_dc_cart['total_data']['total_price']+=$menu_list[$k]['price']*$v;
				$location_dc_cart['total_data']['total_count']+=$v;
			}

			//起送价验证
			if($location_delivery['is_free_delivery']==0 && $location_delivery['start_price'] > $location_dc_cart['total_data']['total_price']){
				$info = '起送价不足,起送价为：'.round($location_delivery['start_price'],2)."元";
				break;
			}

			$user_info = $GLOBALS['user_info'];
			$session_id = es_session::id();

			$location_dc_cart['cart_list'] = array();
			foreach($menu_list as $kk=>$vv){
				$menu_order=array();
				$menu_order['session_id']=es_session::id();
				$menu_order['user_id']=$user_info['id'];
				$menu_order['location_id']=$location_id;
				$menu_order['name']=$vv['name'];
				$menu_order['icon']=$vv['image'];
				$menu_order['num']=$menu_num[$kk];
				$menu_order['unit_price']=$vv['price'];
				$menu_order['total_price']=$vv['price']*$menu_order['num'];
				$menu_order['menu_id']=$vv['id'];
				$menu_order['cart_type']=1;
				$menu_order['add_time']=NOW_TIME;
				$menu_order['is_effect']=1;
				$location_dc_cart['cart_list'][]=$menu_order;
			}

			//结束验证购物车

			$payment = 0;
			$account_money = 0;
			$all_account_money = 0;
			$ecvpassword = '';
			$bank_id = 0;
			$payment_id = 0;  //付款方式 ,1为货到付款，0为在线支付
			$ecvid = 0;
			$ecvsn = '';
			if (isset($request['ecvsn'])) { // 获取代金劵ID
				$ecvsn = strim($request['ecvsn']);
				$ecvid=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."ecv where sn=".$ecvsn." and user_id=".$user_id);
			}
			//开始验证订单接交信息
			$result = dc_count_buy_total($location_id,$location_dc_table_cart,$location_dc_cart,$payment_id,$dc_type,$consignee_id,$payment,$account_money,$all_account_money,$ecvsn,$ecvpassword,$bank_id);

			//验证是否超过该商家配送范围，$is_out_scale=0为不超出，$is_out_scale=1为超出商家配送范围
			$is_out_scale=0;
			if($location_dc_cart['total_data']['total_price']>0){
				if($result['location_delivery_info']['is_free_delivery']==2){
					$is_out_scale=1;
				}
			}
			if($is_out_scale==1){
				$info = $GLOBALS['lang']['DC_OUT_DELIVERY_SCALE'];
				break;
			}
			//结束验证订单接交信息

			//开始生成订单
			$order = array();

			//享受的优惠措失，带换行的字符串
			foreach($result['dc_promote'] as $k=>$v){
				$order['promote_amount'] += $v['discount_amount'];
			}
			$order['promote_str']=serialize($result['dc_promote']);

			$order['rs_price'] = 0;   //定金
			$order['is_rs'] = 0;   //是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券

			$dc_cart_total['rs_list'] = array();
			$dc_cart_total['menu_list'] = $location_dc_cart;
			$order['order_menu'] = serialize($dc_cart_total); //序列化下来的订单的商品数据，含名称，数量，图片，单价，总价等

			$invoice = strim($request['invoice']); // 发票??
        	$dc_comment =  strim($request['dc_comment']); // 用户留言
        	$dc_comment = mb_substr($dc_comment, 0, 100, 'utf-8');

	        // 计算网站提成，和应给商家的钱 ,商家的balance_type=0为  按百分比收取 ，balance_type=1按单收取
			if($dclocation['balance_type']==0){
			$order['balance_price']=$result['pay_total_price']*(1-$dclocation['balance_amount']);
			}elseif($dclocation['balance_type']==1){
			$order['balance_price']=$result['pay_total_price']-$dclocation['balance_amount'];
			}

			$order['supplier_id'] = $dclocation['supplier_id'];
			$order['location_id'] = $location_id;
			$order['create_time'] = NOW_TIME;
			$order['order_status'] = 0;  //结单状态，新单都为零， 等下面的流程同步订单状态
			$order['confirm_status'] = 0;  //订单商家确认状态，0:未确认（未接单，用户未付款可以取消，已付款可直接退款），1.已确认（商家已接单,客户可与商家联系，申请退款），2.已配送（商家已配送,不可以申请退款）
			$order['pay_status'] = 0;  //支付状态，新单都为零， 等下面的流程同步订单状态
			$order['total_price'] = $result['pay_total_price'];  //应付总额  商品价  + 打包费 + 支付手续费 + 配送费

			$order['bank_id'] = $bank_id;
			$order['menu_price'] = $location_dc_cart['total_data']['total_price'];
			$order['package_price'] = $result['package_fee'];
			$order['delivery_price'] = $result['delivery_fee'];
			$order['payment_fee'] = $result['payment_fee'];
			$order['pay_amount'] = $result['ecv_money']; // 已付总额，pay_amount>=total_price时表示支付成功
			$order['pay_time'] = 0;   //支付成功时间
			$order['online_pay'] = 0;  //在线支付的额度
			$order['ecv_id'] = $ecvid;
			$order['ecv_money'] = $result['ecv_money'];
			$order['account_money'] = 0;
			$order['payment_id'] = $payment_id;  //支付方式ID，0表示在线支付，1表示货到付款
			$order['user_id'] = $user_info['id'];
			$order['xpoint'] = $consignee_info['xpoint'];
			$order['ypoint'] = $consignee_info['ypoint'];
			$order['address'] = $consignee_info['address'];
			$order['api_address'] = $consignee_info['api_address'];
			$order['consignee'] = $consignee_info['consignee'];
			$order['mobile'] = $consignee_info['mobile'];
			if($order_delivery_time==''){
				$order['order_delivery_time']==0;
			}elseif($order_delivery_time==1){  //1表示尽快送达
				$order['order_delivery_time'] = $order_delivery_time;
			}else{
				$order['order_delivery_time'] = to_timespan($order_delivery_time);
			}

			$order['dc_comment'] = $dc_comment;  //外卖预订的客户留言
			$order['invoice'] = $invoice;
			$order['location_name'] = $dclocation['name'];  //门店名
			$order['type_del'] = 0;   // 删除状态: 0未经过删除处理 ，1为经过删除
			$order['user_name'] = $user_info['user_name']; //会员名称

			$maxInsertTimes = 5;
			do {
				$order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
				$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order",$order,'INSERT','','SILENT');
				$order_id = intval($GLOBALS['db']->insert_id());
				$maxInsertTimes--;
			}while($order_id==0 && $maxInsertTimes > 0);

			if ($order_id <= 0) {
				$info = '订单生成失败';
				break;
			}

			if(!empty($location_dc_cart['cart_list'])){
				foreach($location_dc_cart['cart_list'] as $k=>$v){
					$goods_item['name'] =$v['name'];
					//$goods_item['icon'] = 0;
					$goods_item['menu_id'] = $v['menu_id'];
					$goods_item['supplier_id'] = $dclocation['supplier_id'];
					$goods_item['location_id'] = $location_id;
					$goods_item['order_id'] = $order_id;
					$goods_item['user_id'] = $user_info['id'];
					$goods_item['num'] = $v['num'];
					$goods_item['unit_price'] = $v['unit_price'];
					$goods_item['total_price'] = $v['total_price'];
					$goods_item['order_sn'] = $order['order_sn'];
					$goods_item['table_time_id']=0;
					$goods_item['type'] = 1;
					$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order_menu",$goods_item,'INSERT','','SILENT');
				}
			}
			//删除购物车该订单的数据
			$deleteCartSql = "delete from ".DB_PREFIX."dc_cart where user_id=".$user_info['id']." and location_id=".$location_id." and session_id='".$session_id."' AND cart_type=1";
			$GLOBALS['db']->query($deleteCartSql);
			// $data['sql'] = $deleteCartSql;
			
			// 订单生成成功后，生成代金劵支付付款单号，同时更新代金劵使用状态	
			$ecv_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where id=".$ecvid);
			if($ecv_data) {
				$ecv_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Voucher'");
				if($ecv_data['money']>$order['total_price'])$ecv_data['money'] = $order['total_price'];
				$payment_notice_id = make_dcpayment_notice($ecv_data['money'],$order_id,$ecv_payment_id,"",$ecv_data['id']);
				$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set is_paid=1,pay_time=".NOW_TIME." where id=".$payment_notice_id);
				$GLOBALS['db']->query("update ".DB_PREFIX."ecv set use_count = use_count + 1 where id=".$ecvid);
			}
			$data['order_id']=$order_id;
			$info = '订单生成成功';
			$status = 1;
		} while (0);

		return array('data' => $data, 'status' => $status, 'info' => $info);
	}

	public function isMenuPriceChange(array $cartList)
	{
		$menuIdAndPrice = array();
		foreach ($cartList as $cart) {
			$menuIdAndPrice[$cart['menu_id']] = $cart['unit_price'];
		}
		$menuIds = array_keys($menuIdAndPrice);
		$menuSql = 'SELECT id, price FROM '.DB_PREFIX.'dc_menu WHERE id in ('.implode(',', $menuIds).')';
		$menuList = $GLOBALS['db']->getAll($menuSql);
		foreach ($menuList as $menu) {
			if ($menu['price'] != $menuIdAndPrice[$menu['id']]) {
				return true;
			}
		}
		return false;
	}

	private function resHandle($request)
	{
		$data = array();
		$status = 0;
		$info = '';
		do {
			$user_info = $GLOBALS['user_info'];
			$payment = 0;
			$account_money = 0;
			$all_account_money = 0;
			$ecvsn = '';
			$ecvpassword = '';
	        $location_id = intval($request['lid']);

	      	$location_info_sql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$location_id;
	      	$location_info = $GLOBALS['db']->getRow($location_info_sql);
	      	if (empty($location_info)) {
	      		$info = '无此商家';
	      		break;
	      	}
	      	if ($location_info['is_reserve'] != 1) {
	      		$info = '商家不支持预订';
	      		break;
	      	}
	      	if ($location_info['is_close'] == 1) {
	      		$info = $GLOBALS['lang']['DC_LOCATION_CLOSE'];
				break;
	      	}

			$consignee = strim($request['consignee']);
			$mobile = strim($request['mobile']);
			$dc_comment = strim($request['dc_comment']);
			$dc_comment = mb_substr($dc_comment, 0, 100, 'utf-8');

			if (empty($consignee)) {
				$info = '请填写姓名';
				break;
			}
			if (!check_mobile($mobile)) {
				$info = '请正确填写手机号';
				break;
			}

			$payment_id =  0;  //付款方式 ,1为货到付款，0为在线支付
			$dc_type = 1;  
			$bank_id =  '';
			$rs_date = strim($request['rs_date']);
			$table_time_id = intval($request['item_time_id']);

			// 兼容原先的订座购物车数据
			$addRes = $this->add_table_cart($location_id, $rs_date, $table_time_id);
			if (!$addRes) {
				$info = '预定额已满';
				break;
			}

			$user_id = $user_info['id'];
			$session_id = es_session::id();

			$rs_table_info = $GLOBALS['db']->getRow("select rs_item.price ,rs_item.name ,rs_item.id as item_id , rs_time.* from ".DB_PREFIX."dc_rs_item_time as rs_time left join ".DB_PREFIX."dc_rs_item as rs_item on rs_time.item_id=rs_item.id  where rs_time.id =".$table_time_id);
			if (empty($rs_table_info)) {
				$info = '预订时间参数错误';
				break;
			}
			$table_time = to_timespan($rs_date.' '.$rs_table_info['rs_time']);
			if ($table_time < NOW_TIME + 3600) {
				$info = '需至少提前一小时预订';
				break;
			}
			if ($table_time > NOW_TIME + 3600 * 24 * 7) {
				$info = '只能进行一周内的预订';
				break;
			}
			$rs_table = array(
				'total_data' => array(
					'total_price' => $rs_table_info['price'],
					'total_count' => 1
				),
			);
			
			$rows=array("日","一","二","三","四","五","六");

			$menu_order=array();
			$menu_order['session_id']=$session_id;
			$menu_order['user_id']=$user_id;
			$menu_order['location_id']=$location_id;
			$menu_order['name']=$rs_table_info['name'];

			$menu_order['num'] = 1;
			$menu_order['unit_price']=$rs_table_info['price'];
			$menu_order['total_price']=$rs_table_info['price']*$menu_order['num'];
			$menu_order['menu_id']=$rs_table_info['item_id'];
			$menu_order['cart_type']=0;
			$menu_order['add_time']=NOW_TIME;
			$menu_order['is_effect']=1;
			$menu_order['table_time_id']=$rs_table_info['id'];
			$menu_order['table_time']=$table_time;
			$menu_order['table_time_format']='<span class="time_span">'.$rs_date.'</span><span class="time_span">星期'.$rows[to_date($menu_order['table_time'],"w")].'</span><span class="time_span">'.to_date($menu_order['table_time'],"H:i").'</span>';
			$rs_table['cart_list'][]=$menu_order;

			$rs_type = intval($request['rs_type']); // 1:不提前点菜 2提前点菜
			$rs_cart_list = array();
			if ($rs_type == 2) {
				require_once(APP_ROOT_PATH."system/model/dc.php");
				$rs_cart_list = load_dc_cart_list(true,$location_id,$type=0);

				if($rs_table['total_data']['total_price'] > $rs_cart_list['total_data']['total_price'] && $rs_cart_list['total_data']['total_price'] > 0){
					$info = '点菜金不足';
					break;
				}
			}
			$pay_status = 0;
			$pay_total_price = $rs_table_info['price'];
			if ($pay_total_price == 0 && $rs_type == 1) {
				$pay_status = 1;
			} elseif ($rs_type == 2) {
				$pay_total_price = $rs_cart_list['total_data']['total_price'];
			}

			//开始生成订单
			$order['rs_price'] = $rs_table['total_data']['total_price'];   //定金
			$order['is_rs'] = 1;   //是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券

			$dc_cart_total['rs_list']=$rs_table;
			$dc_cart_total['menu_list']=$rs_cart_list;
			$order['order_menu'] = serialize($dc_cart_total); //序列化下来的订单的商品数据，含名称，数量，图片，单价，总价等
			//计算网站提成，和应给商家的钱 ,商家的balance_type=0为  按百分比收取 ，balance_type=1按单收取
			if($location_info['balance_type']==0){
				$order['balance_price']= $pay_total_price * (1-$location_info['balance_amount']);
			}elseif($location_info['balance_type']==1){
				$order['balance_price']= $pay_total_price - $location_info['balance_amount'];
			}

			$order['supplier_id'] = $location_info['supplier_id'];
			$order['location_id'] = $location_id;
			$order['create_time'] = NOW_TIME;
			$order['order_status'] = 0;  //结单状态，新单都为零， 等下面的流程同步订单状态
			$order['confirm_status'] = 0;  //订单商家确认状态，0:未确认（未接单，用户未付款可以取消，已付款可直接退款），1.已确认（商家已接单,客户可与商家联系，申请退款），2.已配送（商家已配送,不可以申请退款）
			$order['pay_status'] = $pay_status;  //支付状态，新单都为零， 等下面的流程同步订单状态
			$order['total_price'] = $pay_total_price;  //应付总额  商品价  + 打包费 + 支付手续费 + 配送费

			$order['bank_id'] = $bank_id;
			$order['menu_price'] = ($rs_type == 1 ? 0 : $rs_cart_list['total_data']['total_price']);
			$order['package_price'] = 0;
			$order['delivery_price'] = 0;
			$order['payment_fee'] = 0;
			$order['pay_amount'] = 0; // 已付总额，pay_amount>=total_price时表示支付成功
			$order['pay_time'] = 0;   //支付成功时间
			$order['online_pay'] = 0;  //在线支付的额度
			$order['ecv_id'] = 0;
			$order['ecv_money'] = 0;
			$order['account_money'] = 0;
			$order['payment_id'] = 0;  //支付方式ID，0表示在线支付，1表示货到付款
			$order['user_id'] = $user_id;
			$order['consignee'] = $consignee;
			$order['mobile'] = $mobile;
			$order['order_delivery_time']==0;
			
			$order['dc_comment'] = $dc_comment;  //外卖预订的客户留言
			$order['location_name'] = $location_info['name'];  //门店名
			$order['type_del'] = 0;   // 删除状态: 0未经过删除处理 ，1为经过删除
			$order['user_name'] = $user_info['user_name']; //会员名称

			$maxInsertTimes = 5;
			do {
				$order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
				$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order",$order,'INSERT','','SILENT');
				$order_id = intval($GLOBALS['db']->insert_id());
				$maxInsertTimes--;
			}while($order_id == 0 && $maxInsertTimes > 0);

			if ($order_id <= 0) {
				$info = '订单生成失败';
				break;
			}

			if(!empty($rs_cart_list['cart_list'])){
				foreach($rs_cart_list['cart_list'] as $k=>$v){
					$goods_item['name'] =$v['name'];
					//$goods_item['icon'] = 0;
					$goods_item['menu_id'] = $v['menu_id'];
					$goods_item['supplier_id'] = $location_info['supplier_id'];
					$goods_item['location_id'] = $location_id;
					$goods_item['order_id'] = $order_id;
					$goods_item['user_id'] = $user_id;
					$goods_item['num'] = $v['num'];
					$goods_item['unit_price'] = $v['unit_price'];
					$goods_item['total_price'] = $v['total_price'];
					$goods_item['order_sn'] = $order['order_sn'];
					$goods_item['table_time_id'] = $v['table_time_id'];
					$goods_item['type'] = 0;
					$GLOBALS['db']->autoExecute(DB_PREFIX."dc_order_menu",$goods_item,'INSERT','','SILENT');
				}
			}
			//删除购物车该订单的数据
			$GLOBALS['db']->query("delete from ".DB_PREFIX."dc_cart where user_id=".$user_id." and location_id=".$location_id." and session_id='".$session_id."' AND cart_type=0");

			$data['order_id']=$order_id;
			$info = '订单生成成功';
			$status = 1;
		} while (0);
	
		return array('data' => $data, 'status' => $status, 'info' => $info);
	}
	
	public function to_pay(){
	    $user_info = $GLOBALS['user_info'];
	    $id = $GLOBALS['request']['id'];
	    $is_rs = $GLOBALS['request']['is_rs'];
	    $order_info = $GLOBALS['db']->getRow("select id ,order_sn ,location_id ,create_time ,order_status ,confirm_status ,pay_status ,
					total_price ,package_price,delivery_price,ecv_money,promote_amount,pay_amount,payment_id ,refund_status ,refuse_memo ,refund_memo,consignee ,mobile,api_address,address,dc_comment,invoice,promote_str,order_delivery_time ,is_cancel  ,is_rs ,
					location_name,dp_id,is_dp,dada_delivery,xpoint,ypoint,order_menu  from ".DB_PREFIX."dc_order where type_del = 0 and user_id = ".$user_info['id']." and id = ".$id);
	    $supplier_location_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where id=".$order_info['location_id']);
	    if($order_info['pay_status']==0){
	        $time = 900;//15分钟为支付超时
	        if(NOW_TIME-$order_info['create_time'] > $time){
	            $root['status'] = 0;
	            $root['info'] = "该订单支付超时，已无效";
	            return output($root,0,$root['info']);
	        }else{
	            
	            if ($order_info['is_rs']) { // 判断预订订单是否提前一小时
	                $order_menu = unserialize($order_info['order_menu']);
	                $table_time = $order_menu['rs_list']['cart_list'][0]['table_time'];
	                if ($table_time - NOW_TIME < 3600) {
	                    $root['info'] = '预订需提前一小时';
	                    return output($root,0,$root['info']);
	                }else{
	                    $root['status'] = 1;
	                    return output($root,1);
	                } 
	            }else{
	                $discount_info = $this->getDcPromote($supplier_location_info, $user_info, $order_info['total_price']);
	                $order_info['promote_str'] = unserialize($order_info['promote_str']);
	                if($order_info['promote_str'][0]['class_name'] != $discount_info['promote_info']['class_name']){
	                
	                    $root['status'] = 0;
	                    $root['info'] = "该订单已不享受当前优惠，已无效";
	                    return output($root,0,$root['info']);
	                
	                }else{
	                    $root['status'] = 1;
	                    return output($root,1);
	                } 
	            }
	            
	        }
	    }else{
	        $root['status'] = 0;
	        $root['info'] = "订单信息错误";
	        return output($root,0,$root['info']);
	    }
	     
	}
	
}

?>