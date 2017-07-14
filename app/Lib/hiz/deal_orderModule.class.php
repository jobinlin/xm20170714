<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class deal_orderModule extends HizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
		if(!$GLOBALS['hiz_account_info']){
	   		app_redirect(url("hiz","user#login"));
		}
		
    }
	protected function order_list($type=0){
		$s_account_info = $GLOBALS['hiz_account_info'];
		$account_id = $s_account_info['id'];
		//echo "<pre>";print_r($_REQUEST);print_r($s_account_info);
		if($type==4){
			$_REQUEST['order_sn']=$order_sn = strim($_REQUEST['order_sn']);
			$_REQUEST['user_name_mobile']=$user_name_mobile= strim($_REQUEST['user_name_mobile']);
		}elseif($type==6){
			$_REQUEST['key']=$key= strim($_REQUEST['key']);
		}elseif($type==5){
			$_REQUEST['order_sn']=$order_sn = strim($_REQUEST['order_sn']);
			$_REQUEST['user_mobile']=$user_mobile= strim($_REQUEST['user_mobile']);
			$_REQUEST['supplier_name']=$supplier_name= strim($_REQUEST['supplier_name']);
		}
		$_REQUEST['delivery_status']=$delivery_status = strim($_REQUEST['delivery_status']);
		$_REQUEST['refund_status']=$refund_status = strim($_REQUEST['refund_status']);
		$_REQUEST['order_status']=$order_status = intval($_REQUEST['order_status']);
		
		//分页
		$page_size = 10;
		$page = intval($_REQUEST['p']);
		//echo "<pre>";print_r($_REQUEST);exit;
		$GLOBALS['tmpl']->assign("request",$_REQUEST);
		if($page==0) $page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
		$select='';
		$join='';
		$condition='';
		$select.="do.id,do.order_sn,do.type,do.user_id,do.create_time,do.pay_status,do.pay_amount,do.ecv_money,do.delivery_status,u.user_name,do.is_delete,do.order_status,do.refund_status";
		if($order_sn!=''&&($type==4||$type==5)){
			$condition.=" and do.order_sn ='".$order_sn."'";
		}
		if($user_name_mobile!=''&&$type==4){
			$condition.=" and (u.user_name='".$user_name_mobile."' or do.mobile='".$user_name_mobile."')";
		}
		if($key!=''&&$type==6){
			$condition.=" and (do.order_sn ='".$key."' or do.mobile='".$key."' or supplier.name='".$key."' or (do.consignee_id=0 and u.mobile='".$key."'))";
		}
		if($type==5){
			if($user_mobile!=''){
				$condition.=" and u.mobile='".$user_mobile."'";
			}
			if($supplier_name!=''){
				$condition.=" and supplier.name='".$supplier_name."'";
			}
		}
		if($delivery_status!=''){
			$condition.=" and do.delivery_status ='".$delivery_status."' and pay_status=2";
		}
		if($refund_status!=''){
			$condition.=" and do.refund_status ='".$refund_status."'";
		}
		if($order_status >0){//订单状态
			if($order_status==4){//交易关闭
				$condition.=" and do.is_delete =1 and do.pay_status <>2";
			}elseif($order_status==1){//待付款
				$condition.=" and do.is_delete =0 and do.pay_status <>2";
			}elseif($order_status==3){//已结单
				$condition.=" and do.order_status =1";
			}else{//待结单
				$condition.=" and do.order_status =0 and do.pay_status =2";
			}
		}
		if($type==5){//团购订单
			$select.=',u.mobile as user_mobile'; 
		}else{
			$select.=',do.consignee_id,do.region_lv3,do.region_lv4,do.address,do.street,do.doorplate,do.mobile,u.mobile as user_mobile';
			if($type==6){//商城订单
				$select.=',do.location_id,sl.name as location_name,sl.address as location_address';
				$join.=" left join ".DB_PREFIX."supplier_location as sl on do.location_id= sl.id";
			}
		}
		if($type==4){//驿站订单
			$select.=',do.distribution_id,dist.name';
			$join.=" left join ".DB_PREFIX."distribution as dist on do.distribution_id= dist.id";
		}elseif($type==6||$type==5){//商城订单或者团购订单
			$select.=',supplier.name';
			$join.=" left join ".DB_PREFIX."supplier as supplier on do.supplier_id= supplier.id";
		}
		$sql = "select ".$select." from ".DB_PREFIX."deal_order as do left join ".
	 	    	DB_PREFIX."user as u on do.user_id = u.id "
				.$join.
	    		" where do.type = ".$type." and do.is_main=0 and do.agency_id=".$account_id." ".$condition." order by do.id desc limit ".$limit;
	    $sql_count = "select count(*) from ".DB_PREFIX."deal_order as do left join ".
	    		DB_PREFIX."user as u on do.user_id = u.id ".
				$join." where do.type = ".$type." and do.is_main=0 and do.agency_id=".$account_id." ".$condition;
		$list = $GLOBALS['db']->getAll($sql);
		
		$count=intval($GLOBALS['db']->getOne($sql_count));
		$page = new Page($count,$page_size);   //初始化分页对象
	    $p  =  $page->show();
		return array('list'=>$list,'p'=>$p);
		
		
	}
	protected function order_format($list=array(),$type){
		foreach($list as $k=>$v){
			
			$list[$k]['create_time']=to_date($v['create_time']);
			if($type==4){
				if($v['distribution_id']==0){//驿站名称
					$list[$k]['name']='未分配';
				}
			}
			
			if($v['pay_status']==2){//实付金额
				$list[$k]['pay_amount']=format_price_html(round(($v['pay_amount']-$v['ecv_money']),2),3);
			}else{
				$list[$k]['pay_amount']='-';
			}
			if($v['consignee_id']==0){
				$list[$k]['mobile']=$v['user_mobile'];
			}
			$is_close=0;//是否显示关闭订单
			$data=$this->order_status($v);
			$list[$k]['delivery_info']=$data['delivery_info'];
			$list[$k]['return_info']=$data['return_info'];
			$list[$k]['order_info']=$data['order_info'];
			
			/*
			if($v['pay_status']!=2&&$v['is_delete']){//交易关闭
				$status=1;
				$list[$k]['delivery_info']='-';
				$list[$k]['return_info']='-';
				$list[$k]['order_info']='交易关闭';
			}elseif($v['pay_status']!=2&&!$v['is_delete']){//待付款
				$status=2;
				$list[$k]['delivery_info']='-';
				$list[$k]['return_info']='-';
				$list[$k]['order_info']='待付款';
				$is_close==1;
			}elseif($v['order_status']){//已结单
				$status=3;
				if($v['delivery_status']==0){
					$list[$k]['delivery_info']='待发货';
				}elseif($v['delivery_status']==1){
					$list[$k]['delivery_info']='部分发货';
				}elseif($v['delivery_status']==2){
					$list[$k]['delivery_info']='已发货';
				}
				$list[$k]['return_info']='-';
				$list[$k]['order_info']='已结单';
			}else{//待结单
				$status=4;
				if($v['delivery_status']==0){
					$list[$k]['delivery_info']='待发货';
				}elseif($v['delivery_status']==1){
					$list[$k]['delivery_info']='部分发货';
				}elseif($v['delivery_status']==2){
					$list[$k]['delivery_info']='已发货';
				}
				if($v['refund_status']==1){
					$list[$k]['return_info']='申请退款';
				}else{
					$list[$k]['return_info']='-';
				}
				$list[$k]['order_info']='待结单';
			}*/
			$list[$k]['is_close']=$data['is_close'];
			$list[$k]['address'] = $this->address_format($v);
			unset($list[$k]['region_lv3']);
			unset($list[$k]['region_lv4']);
			unset($list[$k]['street']);
			unset($list[$k]['doorplate']);
			unset($list[$k]['is_delete']);
			unset($list[$k]['order_status']);
			unset($list[$k]['user_mobile']);
		}
		return $list;
	}
	protected function deal_status($order,$v){//获得商品状态
		$item=array();
		$item['id']=$v['id'];
		$item['name']=$v['name'];
		$item['number']=$v['number'];
		$item['unit_price']=format_price_html(round($v['unit_price'],2),3);
		if($order['type']==5){
			
		}else{
			$is_check=0;//管理员验证消费
			$is_deliver=0;//发货按钮
			$is_receipt=0;//强制收货
		}
		$is_return=0;//退款按钮
		if($v['is_shop']==0){//团购券
			if($v['is_coupon']==1){
				if($order['pay_status']!=2){
					$item['deal_status']='待付款';
				}else{
					if($v['cou_is_balance']==0&&($v['cou_refund_status']==0||$v['cou_refund_status']==3)&&($v['end_time']==0 || $v['end_time']>NOW_TIME)){
						$item['deal_status']='待使用';
						//if($v['any_refund']==1){
						$is_return=1;
						//}
					}elseif($v['cou_refund_status']==1){
						if($v['cou_is_balance']==1&&$v['cou_confirm_time']>0){
							if($v['dp_id']==0){
								$item['deal_status'] = "待评价";
							}else{
								$item['deal_status'] = "已完成";
							}
						}else{
							$item['deal_status'] = "申请退款";
						}
					}elseif($v['cou_refund_status']==2){
						$item['deal_status'] = "已退款";
					}else{
						if($v['cou_is_balance']==1&&$v['cou_confirm_time']>0){
							if($v['dp_id']==0){
								$item['deal_status'] = "待评价";
							}else{
								$item['deal_status'] = "已完成";
							}
						}elseif($v['end_time'] < NOW_TIME && $v['end_time'] <> 0){
							$item['deal_status'] = "已过期";
							//if($v['expire_refund']==1){
							$is_return=1;
							//}
						}
					}
				}
			}else{
				if($v['dp_id']==0){
					$item['deal_status'] = "待评价";
				}elseif($v['dp_id']>0){
					$item['deal_status'] = "已完成";
				}
			}
			
		}else{
			if($order['pay_status']!=2&&$order['is_delete']){
				$item['deal_status']='-';
			}elseif($order['pay_status']!=2){
				$item['deal_status']='待付款';
			}elseif($v['refund_status']==1){
				$item['deal_status']='申请退款';
				$is_return=1;
			}elseif($v['refund_status']==2){
				$item['deal_status']='已退款';
			}else{
				if($v['is_pick'] == 1){
					if ($v['consume_count'] < 1) {
						$item['deal_status']='待提货';
						$is_check=1;
						$is_return=1;
					}else{
						if($v['dp_id'] == 0) {
							$item['deal_status']='待评价';
						}else{
							$item['deal_status']='已完成';
						}
					}
				}else{
					if($v['delivery_status'] == 0){
						$item['deal_status']='待发货';
						$is_return=1;
						$is_deliver=1;
					}elseif($v['delivery_status'] == 1){
						if ($v['is_arrival'] == 0) {
							$item['deal_status']='待收货';
							$is_return=1;
							$is_receipt=1;
						}elseif($v['is_arrival'] == 1){
							if($v['dp_id'] == 0) {
								$item['deal_status']='待评价';
							}else{
								$item['deal_status']='已完成';
							}
						}
					}
				}
			}
		}
		if($order['type']==5){
			
		}else{
			$item['is_deliver']=$is_deliver;
			$item['is_receipt']=$is_receipt;
		}
		$item['is_return']=$is_return;
		$item['is_shop']=$v['is_shop'];
		if(($v['is_shop']==0&&$v['is_coupon']==1)||($v['is_pick'] == 1&&$v['is_shop']==1)){
			$item['coupon_id']=$v['coupon_id'];
		}
		
		return $item;
	}
	protected function order_status($v){//获得商品状态
		$is_close=0;
		if($v['pay_status']!=2&&$v['is_delete']){//交易关闭
			$status=1;
			$delivery_info='-';
			$return_info='-';
			$order_info='交易关闭';
		}elseif($v['pay_status']!=2&&!$v['is_delete']){//待付款
			$status=2;
			$delivery_info='-';
			$return_info='-';
			$order_info='待付款';
			$is_close=1;
		}elseif($v['order_status']){//已结单
			$status=3;
			if($v['delivery_status']==0){
				$delivery_info='待发货';
			}elseif($v['delivery_status']==1){
				$delivery_info='部分发货';
			}elseif($v['delivery_status']==2){
				$delivery_info='已发货';
			}
			$return_info='-';
			$order_info='已结单';
		}else{//待结单
			$status=4;
			if($v['delivery_status']==0){
				$delivery_info='待发货';
			}elseif($v['delivery_status']==1){
				$delivery_info='部分发货';
			}elseif($v['delivery_status']==2){
				$delivery_info='已发货';
			}elseif($v['delivery_status']==5){
				$delivery_info='-';
			}
			if($v['refund_status']==1){
				$return_info='申请退款';
			}else{
				$return_info='-';
			}
			$order_info='待结单';
		}
		return array('delivery_info'=>$delivery_info,'return_info'=>$return_info,'order_info'=>$order_info,'is_close'=>$is_close,'status'=>$status);
	}
	protected function address_format($order){//获得地址
		if($order['consignee_id']>0){
			$d_arr=array();
			$d_arr[]=$order['region_lv3'];
			$d_arr[]=$order['region_lv4'];
			$d_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX. "delivery_region where id in (".implode(',',$d_arr).")");
			$region_list=array();
			foreach($d_list as $d_item){
				$region_list[$d_item['id']]=$d_item;
			}
			//$order_item['address'] = $v['address'];
			$order['address'] = $region_list[$order['region_lv1']]['name'].$region_list[$order['region_lv2']]['name'].$region_list[$order['region_lv3']]['name'].$region_list[$order['region_lv4']]['name'].$order['address'].$order['street'].$order['doorplate'];
		}elseif($order['location_id']>0){
			$order['address']=$order['location_name'];
		}
		
		return $order['address'];
	}
    /**
	 * 驿站订单列表
	 * 
	 * 输入：act:deal_order
			 ctl:self_order
			 order_sn:订单号
			 user_name_mobile：会员名手机号
			 delivery_status： 发货状态，null:请选择，0：待发货 2：已发货
			 refund_status： 退款申请，null:请选择，0：无 1：有
			 order_status：订单状态，null:请选择，1：待付款 2：待结单 3：已结单 4：交易关闭
	 * 
	 * 输出：
	 Array
		(
			[list] => Array
				(
					[0] => Array
						(
							[id] => 920
							[order_sn] => 201703250334146600p_yz
							[type] => 4
							[user_id] => 73
							[create_time] => 2017-03-25 15:34:14
							[pay_status] => 2
							[pay_amount] => 250.00
							[delivery_status] => 0
							[user_name] => fanwe
							[consignee_id] => 116
							[address] => 福州鼓楼区工业路193号宝龙城市广场
							[mobile] => 18774922171
							[distribution_id] => 1
							[name] => 鼓楼驿站
							[delivery_info] => 待发货
							[return_info] => 
							[order_info] => 待结单
							[is_close] => 0   是否显示关闭订单 0：不显示  1：显示
						)
				)

			[p] =>  2 条记录 1/1 页           分页
		)
	 */
	public function self_order()
	{	
		init_app_page();
		$order=$this->order_list(4);
		$order['list']=$this->order_format($order['list'],4);
		
		//echo "<pre>"; print_r($order);
		//exit;
		
	    $GLOBALS['tmpl']->assign('pages',$order['p']);
		$GLOBALS['tmpl']->assign("list",$order['list']);
		$GLOBALS['tmpl']->assign("head_title","自营-驿站订单");
		$GLOBALS['tmpl']->display("pages/deal_order/self_order.html");	
		

	
	}
	/**
	 * 商城订单列表
	 * 
	 * 输入：act:deal_order
			 ctl:shop_order
			 key: 订单号/会员手机/商家名
			 delivery_status： 发货状态，null:请选择，0：待发货 1：已发货 2：已发货 5：无需发货
			 refund_status： 退款申请，null:请选择，0：无 1：申请退款
			 order_status：订单状态，null:请选择，1：待付款 2：待结单 3：已结单 4：交易关闭
	 * 
	 * 输出：
	 Array
		(
			[list] => Array
				(
					[0] => Array
						(
							[id] => 934
							[order_sn] => 2017032909420663
							[type] => 6
							[user_id] => 73
							[create_time] => 2017-03-29 09:42:06
							[pay_status] => 2
							[pay_amount] => 26.40
							[delivery_status] => 0
							[user_name] => fanwe
							[consignee_id] => 0
							[address] => 
							[mobile] => 
							[location_name] => 
							[location_address] => 
							[name] => 令狐冲窑烤活鱼
							[delivery_info] => 待发货
							[return_info] => 
							[order_info] => 待结单
							[is_close] => 0    是否显示关闭订单 0：不显示  1：显示
						)

			[p] =>  11 条记录 1/2 页      1  2     下一页 
		)
	 */
	public function shop_order()
	{	
		init_app_page();
		$order=$this->order_list(6);
		$order['list']=$this->order_format($order['list'],6);
		
		//echo "<pre>"; print_r($order);
		//exit;
		
	    $GLOBALS['tmpl']->assign('pages',$order['p']);
		$GLOBALS['tmpl']->assign("list",$order['list']);
		$GLOBALS['tmpl']->assign("head_title","商城订单");
		$GLOBALS['tmpl']->display("pages/deal_order/shop_order.html");
	}
	/**
	 * 团购订单列表
	 * 
	 * 输入：act:deal_order
			 ctl:self_order
			 order_sn:订单号
			 user_mobile：会员名手机号
			 supplier_name：商户名
			 refund_status： 退款申请，null:请选择，0：无 1：有
			 order_status：订单状态，null:请选择，1：待付款 2：待结单 3：已结单 4：交易关闭
	 * 
	 * 输出：
	 Array
		(
			[list] => Array
				(
					[0] => Array
						(
							[id] => 920
							[order_sn] => 201703250334146600p_yz
							[type] => 4
							[user_id] => 73
							[create_time] => 2017-03-25 15:34:14
							[pay_status] => 2
							[pay_amount] => 250.00
							[delivery_status] => 0
							[user_name] => fanwe
							[consignee_id] => 116
							[address] => 福州鼓楼区工业路193号宝龙城市广场
							[mobile] => 18774922171
							[distribution_id] => 1
							[name] => 鼓楼驿站
							[delivery_info] => 待发货
							[return_info] => 
							[order_info] => 待结单
							[is_close] => 0   是否显示关闭订单 0：不显示  1：显示
						)
				)

			[p] =>  2 条记录 1/1 页           分页
		)
	 */
	public function tuan_order()
	{	
		init_app_page();
		$order=$this->order_list(5);
		$order['list']=$this->order_format($order['list'],5);
		
		//echo "<pre>"; print_r($order);
		//exit;
		
	    $GLOBALS['tmpl']->assign('pages',$order['p']);
		$GLOBALS['tmpl']->assign("list",$order['list']);
		$GLOBALS['tmpl']->assign("head_title","团购订单");
		$GLOBALS['tmpl']->display("pages/deal_order/tuan_order.html");
	}
	/**
	 * 订单详情页
	 * 
	 * 输入：act:deal_order
			 ctl:order_view
			 data_id:订单id
	 * 
	 * 输出：
	 Array
		(
			[consignee_info] => Array 收货人信息
				(
				)

			[pick_location] => Array 自提信息
				(
				)

			[order_info] => Array  订单信息
				(
					[user_name] => fanwe
					[create_time] => 2017-03-28 20:16:12
					[order_info] => 已结单
					[delivery_info] => 
				)

			[order_money] => Array 资金明细
				(
					[deal_total_price] => 76.0000
					[delivery_fee] => 0.0000
					[discount_price] => 15.2000
					[total_price] => 60.8000
				)

			[payment_info] => Array 付款信息
				(
					[notice_sn] => 2017032808161250
					[outer_notice_sn] => 
					[name] => 余额支付
					[pay_time] => 2017-03-28 20:16:12
				)

			[deal_order_item] => Array 商品列表
				(
					[name] => 店铺：令狐冲窑烤活鱼
					[list] => Array
						(
							[0] => Array
								(
									[id] => 1681
									[name] => 令狐冲团购
									[number] => 2
									[unit_price] => 38.00
									[deal_status] => 待评价
									[is_return] => 0     是显示退款按钮 0不显示，1显示
								)

						)

				)
 
			[delivery_info] => Array //发货信息
				(
				)

			[order_log] => Array  操作日志
				(
					[0] => Array
						(
							[id] => 2732
							[log_info] => 订单完结
							[log_time] => 1490674572
							[order_id] => 930
						)

					[1] => Array
						(
							[id] => 2731
							[log_info] => 2017032808161276订单付款完成
							[log_time] => 1490674572
							[order_id] => 930
						)

				)

		)
	 */
	public function order_view()
	{	
		init_app_page();
		$s_account_info = $GLOBALS['hiz_account_info'];
		$account_id = $s_account_info['id'];
		
		$data_id = intval($_REQUEST['data_id']);
		if($data_id==0){
			showErr("关键id不存在");
		}
		$type=intval($GLOBALS['db']->getOne('select type from '.DB_PREFIX.'deal_order where id='.$data_id));
		if($type==0){
			showErr("数据错误");
		}
		$select = '';
		$join = '';
		if($type==4){
			$select.=',do.distribution_id,dist.name as distribution_name';
			$join.=" left join ".DB_PREFIX."distribution as dist on do.distribution_id= dist.id";
		}else{
			$select.=',supplier.name as supplier_name';
			$join.=" left join ".DB_PREFIX."supplier as supplier on do.supplier_id= supplier.id";
			$select.=',sl.name as location_name,sl.address as location_address,sl.tel';
			$join.=" left join ".DB_PREFIX."supplier_location as sl on do.location_id= sl.id";
		}
		$sql = 'select do.id,do.order_sn,do.type,do.memo,do.region_lv3,do.region_lv4,do.address,do.address,do.mobile,do.zip,do.consignee,do.consignee_id,do.street,do.doorplate,u.user_name,do.create_time,do.pay_status,do.is_delete,do.order_status,do.delivery_status,do.deal_total_price,do.exchange_money,do.youhui_money,do.delivery_fee,do.discount_price,do.total_price,do.deal_order_item,do.location_id,do.ecv_money,do.record_delivery_fee,do.payment_fee,do.return_total_score,do.order_id'.$select.
				' from '.DB_PREFIX.'deal_order as do '.$join.
				' left join '.DB_PREFIX.'user as u on do.user_id= u.id'.
				' where do.is_main=0 and do.agency_id='.$account_id.' and do.id='.$data_id;
		//echo $sql;
		$order = $GLOBALS['db']->getRow($sql);
		if(!$order){
			showErr("数据错误");
		}
		$root['order_id']=$order['id'];//订单号
		$root['order_sn']=$order['order_sn'];//订单号
		$root['type']=$type;//订单号
		//输出订单付款单
		$oid=$order['order_id']?$order['order_id'] : $order['id'];
		$payment_notice = $GLOBALS['db']->getAll('select pn.*,p.name,p.class_name from '.DB_PREFIX.'payment_notice as pn left join '.DB_PREFIX.'payment as p on pn.payment_id=p.id where pn.order_id='.$oid.' and pn.order_type=3 and pn.is_paid=1 order by pn.pay_time desc');
		if(count($payment_notice)>1){
			//if()
			foreach($payment_notice as $k=>$v){
				if($v['class_name']=='Voucher'){
					unset($payment_notice[$k]);
				}
			}
			$payment_notice=reset($payment_notice);
		}else{
			$payment_notice=$payment_notice['0'];
		}
		if ($order['deal_order_item']) {
			$order['deal_order_item'] = unserialize($order['deal_order_item']);
		} else {
			update_order_cache($data_id);
			$order['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $data_id);
		}
		if($type==5){
			if($order['deal_order_item']['0']['is_coupon'] == 1&&$order['pay_status']==2){
				$coupon_arr = $GLOBALS['db']->getAll("select id as coupon_id,password,is_balance as cou_is_balance,refund_status as cou_refund_status,end_time,end_time,confirm_time as cou_confirm_time,any_refund,expire_refund from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $order['deal_order_item']['0']['id']);
				$item=$order['deal_order_item']['0'];
				$item['number']=1;
				unset($order['deal_order_item']['0']);
				foreach($coupon_arr as $k=>$v){
					$order['deal_order_item'][]=array_merge($item,$v);
				}
			}
		}
		$deal_order_item=array();
		if($type==4){
			$deal_order_item['name']='自营商城－商品列表';
		}else{
			$deal_order_item['name']='店铺：'.$order['supplier_name'];
		}
		foreach($order['deal_order_item'] as $k=>$v){
			$deal_order_item['list'][]=$this->deal_status($order,$v);
		}
		$delivery_info=array();
		if($type==4){
			if($order['distribution_id']>0){
				if($order['delivery_status']==2){
					$dist_create_time=$GLOBALS['db']->getOne('select create_time from '.DB_PREFIX.'distribution_coupon where order_id='.$data_id);
					$delivery_item=array();
					$delivery_item['name']="发货驿站：".$order['distribution_name']."(".to_date($dist_create_time,"Y.m.d H:i:s").")";
					foreach($order['deal_order_item'] as $k=>$v){
						$item=array();
						$item['name']=$v['name'];
						$item['number']=$v['number'];
						$delivery_item['list'][]=$item;
					}
					$delivery_info[]=$delivery_item;
				}
			}
		}elseif($type==6){
			if($order['consignee_id']>0){
				if($order['delivery_status']==2||$order['delivery_status']==1){
					$dnSql = "select dn.* , do.order_sn , t.state , t.ischeck , `t`.`data` as `track_data`,t.express_company ".
					"from ".DB_PREFIX."delivery_notice as dn ".
					"left join ".DB_PREFIX."deal_order as do on dn.order_id=do.id ".
					"left join ".DB_PREFIX."track as t on dn.order_id=t.order_id and t.express_number=dn.notice_sn ".
					"left join ".DB_PREFIX."express as e on e.id=dn.express_id and t.express_code=e.class_name ".
					"where dn.order_id=".$data_id." order by dn.id asc";
					$dn_list=$GLOBALS['db']->getAll($dnSql);
					foreach($order['deal_order_item'] as $k=>$v){
						$deal_order_item_key[$v['id']]=$v;
					}
					$i=1;
					foreach($dn_list as $k=>$v){
						$item=array();
						if(!$delivery_info[$v['notice_sn']]){
							$delivery_info[$v['notice_sn']]['name']='包裹'.($i++).'：'.$v['express_company'].'-'.$v['notice_sn'].'('.to_date($deal_order_item_key[$v['order_item_id']]['delivery_time'],"Y.m.d H:i:s").')<br/>备注：'.($v['memo']?$v['memo']:"无");
							$delivery_info[$v['notice_sn']]['memo']=$v['memo'];
							
						}
						$item['name']=$deal_order_item_key[$v['order_item_id']]['name'];
						$item['number']=$deal_order_item_key[$v['order_item_id']]['number'];
						$delivery_info[$v['notice_sn']]['list'][]=$item;
						
					}
				}
			}else{
				if($order['delivery_status']!=5&&$order['pay_status']==2&&$order['delivery_status']==2){
					$delivery_item=array();
					$delivery_item['name']="发货时间：(".to_date($order['deal_order_item']['0']['delivery_time'],"Y.m.d H:i:s").")<br/>备注：".$order['deal_order_item']['0']['delivery_memo'];
					foreach($order['deal_order_item'] as $k=>$v){
						$item=array();
						$item['name']=$v['name'];
						$item['number']=$v['number'];
						$delivery_item['list'][]=$item;
					}
					$delivery_info[]=$delivery_item;
				}
			}
		}
		//输出订单日志
		$log_list = $GLOBALS['db']->getAll('select * from '.DB_PREFIX.'deal_order_log where order_id='.$data_id.' order by log_time desc, id desc');
		
		$consignee_info=array();//收货人信息
		if($order['consignee_id']>0){
			$consignee_info['name']=$order['consignee'];
			$consignee_info['address']=$this->address_format($order);
			$consignee_info['mobile']=$order['mobile'];
			$consignee_info['zip']=$order['zip'];
			$consignee_info['memo']=$order['memo'];
		}
		$root['consignee_info']=$consignee_info;//收货人信息end
		$pick_location=array();//自提门店
		if($order['location_id']>0){
			$pick_location['location_name']=$order['location_name'];
			$pick_location['location_address']=$order['location_address'];
			$pick_location['tel']=$order['tel'];
			$pick_location['memo']=$order['memo'];
		}
		$root['pick_location']=$pick_location;//自提门店end
		$order_info=array();//订单信息
		$order_info['user_name']=$order['user_name'];
		$order_info['create_time']=to_date($order['create_time'],"Y.m.d H:i:s");
		$data=$this->order_status($order);
		$order_info['order_info']=$data['order_info'];
		$order_info['delivery_info']=$data['delivery_info'];
		$order_info['type']=$order['type'];
		$order_info['distribution_id']=$order['distribution_id'];
		$order_info['distribution_name']=$order['distribution_name'];
		$order_info['delivery_status']=$order['delivery_status'];
		$root['order_info']=$order_info;//订单信息end

		require_once(APP_ROOT_PATH . "system/model/deal_order.php");
		$root['order_money']=order_fee_arr($order,1);
		$payment_info=array();//付款信息
		if($payment_notice){
			$payment_info['notice_sn']=$payment_notice['notice_sn'];
			$payment_info['class_name']=$payment_notice['class_name'];
			$payment_info['outer_notice_sn']=$payment_notice['outer_notice_sn'];
			$rel='';
			if($payment_info['class_name']=='Cod'){
				$payment_config=unserialize($payment_notice['payment_config']);
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' ."Cod_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					if(count($payment_config)>0){
						$rel='('.$payment_lang['COD_PAYMENT_'.$payment_config['COD_PAYMENT']].')';
					}
				}
			}
			$payment_info['name']=$payment_notice['name'].$rel;
			$payment_info['pay_time']=to_date($payment_notice['pay_time'],"Y.m.d H:i:s");
			
		}
		$root['payment_info']=$payment_info;//付款信息end
		$root['deal_order_item']=$deal_order_item;//订单商品列表
		$root['delivery_info']=$delivery_info;//发货信息
		$root['order_log']=$log_list;//操作日志
		//echo "<pre>";print_r($root);exit;
		
	    if($type==4){
			$str="驿站订单详情";
		}elseif($type==5){
			$str="团购订单详情";
		}else{
			$str="商城订单详情";
		}
		$GLOBALS['tmpl']->assign("head_title",$str);
		$GLOBALS['tmpl']->assign("root",$root);
		//echo "<pre>";print_r($root);exit;
		$GLOBALS['tmpl']->display("pages/deal_order/view.html");
	}
	/**
	 * 关闭订单
	 * 
	 * 输入：act:deal_order
			 ctl:cancel
			 data_id:订单id
	 * 
	 * 输出：
	Array
		(
			[status]=> 状态 0：失败  1：成功
			[info]=> 提示信息
		)
	 */
	public function cancel()
	{	
		
		$s_account_info = $GLOBALS['hiz_account_info'];
		$account_id = $s_account_info['id'];
		
		$id = intval($_REQUEST['data_id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and agency_id=".$account_id);
		if($order_info)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_delete = 1 where (order_status = 1 or pay_status = 0) and is_delete = 0 and  id = ".$id);
			if($GLOBALS['db']->affected_rows())
			{
				require_once(APP_ROOT_PATH."system/model/deal_order.php");
				//开始退已付的款
				if($order_info['pay_status']==0&&$order_info['pay_amount']>0)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = 0,ecv_id = 0,ecv_money=0,account_money = 0 where id = ".$order_info['id']);
					require_once(APP_ROOT_PATH."system/model/user.php");
					if($order_info['account_money']>0)
					{
						$supplier_id = $GLOBALS['db']->getOne("select supplier_id from ".DB_PREFIX."deal_order_item where order_id = ".$id);
						modify_account(array("money"=>$order_info['account_money'],"is_admin"=>1,"supplier_id"=>$supplier_id), $order_info['user_id'],"取消订单，退回余额支付 ");
						order_log("交易被关闭，退回余额支付 ".$order_info['account_money']." 元", $order_info['id']);
					}
					if($order_info['ecv_id'])
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."ecv set use_count = use_count - 1 where id = ".$order_info['ecv_id']);
						order_log("交易被关闭，代金券退回 ", $order_info['id']);
					}

				}
				if($order_info['pay_status'] == 0 && $order_info['exchange_money'] > 0){
					$GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set exchange_money = 0,total_price=total_price+".$order_info['exchange_money']." where id = " . $order_info['id']);
					$score_purchase=unserialize($order_info['score_purchase']);
					modify_account(array("score" =>$score_purchase['user_use_score'] ), $order_info['user_id'], "取消订单，退回积分抵扣 ");
					order_log("交易被关闭，积分抵扣退回 ", $order_info['id']);
				}
				over_order($order_info['id']);
				$data=array();
				$data['status'] = 1;
				$data['info'] = "操作成功";
			}
			else
			{
				$data=array();
				$data['status'] = 0;
				$data['info'] = "交易无法被关闭";
			}
		}
		else
		{
			$data=array();
			$data['status'] = 0;
			$data['info'] = "订单不存在";
		}
		
		ajax_return($data);
	}
	/**
	 * 退款
	 * 
	 * 输入：act:deal_order
			 ctl:refund
			 order_item_id:订单商品id
			 coupon_id：消费券id
	 * 
	 * 输出：
	Array
		(
			[status] => 1
			[data] => Array    //商品消息
				(
					[key] => order_item_id     上传退款接口  id的变量名
					[id] => 1669                订单商品id
					[deal_icon] =>              商品图片
					[name] => 驿站-哔哩哔哩-03     商品名
					[refund_content] =>           会员申请退款消息
					[real_total_price] => 240.00      退款金额
				)

			[order_info] => Array     //订单消息
				(
					[user_name] => fanwe
					[pay_amount] => 250.00
					[ecv_money] => 0.00
					[delivery_fee] => 10.00
					[refund_amount] => 0
				)

			[do_refund_url] => /o2onew/hiz.php?ctl=deal_order&act=do_refund   通过审核
			[do_refuse_url] => /o2onew/hiz.php?ctl=deal_order&act=do_refuse   拒绝审核
			[info] => 
		)
	 */
	public function refund()
	{

		$s_account_info = $GLOBALS['hiz_account_info'];
		$account_id = $s_account_info['id'];

		$order_item_id = intval($_REQUEST['order_item_id']);
		$coupon_id = intval($_REQUEST['coupon_id']);
		if($order_item_id)
		{			
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
		
			if($data)
			{
				$order_id = $data['order_id'];
				$data['price'] = $data['total_price'];
				if($data['return_total_score']<0){
				    $data['score'] = abs($data['return_total_score']);
				}else{
				    $data['score']=0;
				}
				
				$data['key'] = "order_item_id";
			}
		}elseif($coupon_id){
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
			if($data)
			{
				$order_id = $data['order_id'];
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
				$data['name'] = $order_item['name'];
				
				$data['deal_icon'] = $order_item['deal_icon'];
				if($data['deal_type']==0)//按件
				{
					$data['price'] = $order_item['unit_price'];
					$data['score'] = abs($order_item['return_score']);
				}
				else
				{
					$data['price'] = $order_item['total_price'];
					$data['score'] = abs($order_item['return_total_score']);
								
				}	
				$data['key'] = "coupon_id";
			}	
		}
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = '".$order_id."'");
		if($data)
		{
		    
		    $data['refund_content'] = $GLOBALS['db']->getOne("select content from ".DB_PREFIX."message where id = '{$data['message_id']}' ");
		    
		    $data['total_price'] = $data['total_price'] > 0 ? $data['total_price'] : $data['coupon_price'];
		    
		    // 退款是否是最后一单，如果是：把全部可以退的都退了
		    //$count = M('DealOrderItem')->where("refund_status <> 2 and order_id='{$data['order_id']}'")->count();
			$count = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where refund_status <> 2 and order_id='".$order_id."'");
            if ($count === '1') {
                $real_total_price = $order_info['pay_amount'] - $order_info['refund_money'] - $order_info['ecv_money'];
                $data['real_total_price'] = sprintf("%.2f",substr(sprintf("%.4f", $real_total_price), 0, -2));
            }else{
                
                // 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
                $scale = $data['total_price'] / ( $order_info['total_price'] + $order_info['discount_price'] - $order_info['delivery_fee']);
                // 当前商品折扣价    = 当前商品价格比例  * 总折扣价
                $scale_discount_price = $scale * $order_info['discount_price'];
                // 当前商品红包价格
                $scale_ecv_money      = $scale * $order_info['ecv_money'];
                
                // 当前商品实际支付价格
                $real_total_price         = $data['total_price'] - $scale_discount_price - $scale_ecv_money;
                $data['real_total_price'] = sprintf("%.2f",substr(sprintf("%.4f", $real_total_price), 0, -2));
            }	    
		    
		    // 红包有可能扣的是运费，导致退款为负数，所以小于0的时候，显示0
		    $data['real_total_price'] = $data['real_total_price'] < 0 ? 0 : $data['real_total_price'];
		    
		    $order_info['ecv_money']      = sprintf("%.2f",substr(sprintf("%.4f", $order_info['ecv_money']), 0, -2));
		    $order_info['pay_amount']     = sprintf("%.2f",substr(sprintf("%.4f", $order_info['pay_amount']), 0, -2));;
		    $order_info['delivery_fee']   = sprintf("%.2f",substr(sprintf("%.4f", $order_info['delivery_fee']), 0, -2));;
		    
		    
			if($data['price']<0)$data['price'] = 0;
			if($data['score']<0)$data['score'] = 0;
			
			$GLOBALS['tmpl']->assign("data",$data);
			$GLOBALS['tmpl']->assign("order_info",$order_info);
			$data=array();
			$data['status'] = true;
			$data['html'] = $GLOBALS['tmpl']->fetch("pages/deal_order/refund.html");
			/*$root=array();
			$root['key']=$data['key'];
			$root['id']=$data['id'];
			$root['deal_icon']=$data['deal_icon'];
			$root['name']=$data['name'];
			$root['refund_content']=$data['refund_content'];
			$root['real_total_price']=$data['real_total_price'];
			
			$order_root['user_name']=$order_info['user_name'];
			$order_root['pay_amount']=$order_info['pay_amount'];
			$order_root['ecv_money']=$order_info['ecv_money'];
			$order_root['delivery_fee']=$order_info['delivery_fee'];
			$order_root['refund_amount']=floatval($order_info['refund_amount']);
			$data=array();
			$data['status'] = 1;
			$data['data']=$root;
			$data['order_info']=$order_root;
			$data['do_refund_url']=url("hiz","deal_order#do_refund");
			$data['do_refuse_url']=url("hiz","deal_order#do_refuse");
			$data['info'] = "";*/
		}else{
			$data=array();
			$data['status'] = 0;
			$data['info'] = "非法的数据";
		}
		ajax_return($data);
	}
	/**
	 * 执行退款
	 * 退款执行流：
	 * 1. 退还金额至会员账户
	 * 2. 更新商家账户
	 * 3. 更新订单及订单关联表的相关状态
	 * 3. 更新平台报表
	 * 4. 更新订单缓存
	 * 5. 为订单重新分片
	 * 输入：act:deal_order
			 ctl:refund
			 order_item_id:订单商品id
			 coupon_id：消费券id
			 price：本次退款金额
			 content：备注
	 * 
	 * 输出：
	Array
		(
			[status] => 1   0：失败    1：成功
			[info]=>退款成功        消息
		)
	 */
	public function do_refund()
	{
		$order_item_id = intval($_REQUEST['order_item_id']);
		$coupon_id = intval($_REQUEST['coupon_id']);
		$price = floatval($_REQUEST['price']);
		$score= intval($_REQUEST['score']);
		 
		$content = strim($_REQUEST['content']);
		if( $price < 0 )
		{
			$root=array();
			$root['status'] = 0;
			$root['info'] = "金额出错";
			ajax_return($root);
		}
		
		if($score<0)
		{
			$root=array();
			$root['status'] = 0;
			$root['info'] = "积分出错";
			ajax_return($root);
		}
		
		if($order_item_id)
		{
			$oi = $order_item_id;
			$order_item = $data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
			if($data['refund_status']==2) 
			{
				$root=array();
				$root['status'] = 0;
				$root['info'] = "已退款";
				ajax_return($root);
			}
			
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = {$data['order_id']}");
			
			// 要退的金额 + 已经退的金额      >  实付价格 
			$real_pay_amount = sprintf("%.2f",substr(sprintf("%.4f", ( $order_info['pay_amount'] - $order_info['ecv_money'] )), 0, -2));
			
			if( $price + $order_info['refund_amount'] >  $real_pay_amount ){
				$root=array();
				$root['status'] = 0;
				$root['info'] = "您的退款金额，已经超出订单的总金额！";
				ajax_return($root);
			}
			
			
			if($order_info['type']== 2 && $order_item['return_total_score'] < 0)
			{
			    
			    $score = abs($order_item['return_total_score']);
			    require_once(APP_ROOT_PATH."system/model/user.php");
			    modify_account(array("score"=>$score,"is_admin"=>1,"supplier_id"=>$data['supplier_id']), $order_info['user_id'],$data['name']."成功退积分".",退款金额：".format_price($price));
			    order_log($data['name']."积分订单成功退".$score."积分", $data['order_id']);
			}
			
			if($data)
			{
				$order_id = $data['order_id'];
				$supplier_id = $data['supplier_id'];
			}
					
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_money=refund_money+{$price}, refund_status = 2,is_arrival = 0, admin_memo='{$content}' where id = ".$order_item_id);
			
			// 如果是自提商品，退款的时候，消费券也要设置退款状态
			if ($order_item['is_pick']) {
			    $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_money=refund_money+{$price},  refund_status = 2, admin_memo='{$content}' where order_deal_id = {$order_item_id}");
			}
			
			// 申请退款 或者 没收到货
			$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
			// 申请退款的消费券
			$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);

			
			// 判断是否全部退款，设置deal_order 表状态
			if(intval($refund_item_count)==0&&intval($coupon_item_count)==0){
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",refund_status = 2,after_sale = 1,is_refuse_delivery=0 where id = ".$order_id);
			}else{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",is_refuse_delivery=0 where id = ".$order_id);
			}		
		}
		elseif($coupon_id)
		{
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
			if($data['refund_status']==2)
			{
				$root=array();
				$root['status'] = 0;
				$root['info'] = "已退款";
				ajax_return($root);
			}
			if($data)
			{
				$oi = $data['order_deal_id'];
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
				if($data['deal_type']==0){//消费券的生成方式 0:按件生成 1:按单生成
				    $order_item['number']=1;
				}
				
				
				$data['name'] = $order_item['name'];
				$order_id = $data['order_id'];		
				$supplier_id = $data['supplier_id'];
			}
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_money=refund_money+{$price},  refund_status = 2, admin_memo='{$content}' where id = ".$coupon_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_money=refund_money+{$price},  refund_status = 2, admin_memo='{$content}' where id = ".$data['order_deal_id']);
			
			$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
			$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
			if(intval($refund_item_count)==0&&intval($coupon_item_count)==0)
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price.",refund_status = 2,after_sale = 1 where id = ".$order_id);
			else
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount + ".$price.",refund_money = refund_money + ".$price." where id = ".$order_id);
					
		}
		
		
		$attr_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = ".$order_item['deal_id']." and locate(attr_str,'".$order_item['attr_str']."')");
		if($attr_stock)
		{
		    if($attr_stock['stock_cfg']>=0)
		    {
		        $sql = "update ".DB_PREFIX."attr_stock set buy_count = buy_count - ".$order_item['number'].",stock_cfg = stock_cfg + ".$order_item['number']." where deal_id = ".$order_item['deal_id'].
		        " and locate(attr_str,'".$order_item['attr_str']."') > 0 ";
		    }
		    else
		    {
		        $sql = "update ".DB_PREFIX."attr_stock set buy_count = buy_count - ".$order_item['number']." where deal_id = ".$order_item['deal_id'].
		        " and locate(attr_str,'".$order_item['attr_str']."') > 0 ";
		    }
		}

		$GLOBALS['db']->query($sql); 
		
		$deal_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_stock where deal_id = '".$order_item['deal_id']."'");
		if($deal_stock['stock_cfg']>=0)
		    $sql = "update ".DB_PREFIX."deal_stock set buy_count = buy_count - ".$order_item['number'].
		    ",stock_cfg = stock_cfg + ".$order_item['number']." where deal_id=".$order_item['deal_id'].
		    " and time_status <> 2";
		else
		    $sql = " update ".DB_PREFIX."deal_stock set buy_count = buy_count - ".$order_item['number'].
		    " where deal_id=".$order_item['deal_id']." and time_status <> 2";
		
		$GLOBALS['db']->query($sql);
		
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		
		if($price>0)
		{
			require_once(APP_ROOT_PATH."system/model/user.php");
			
			$info = $coupon_id ? $data['name'].' 的消费券 '.$data['password'].' 退款成功' : $data['name']." 退款成功";
			modify_account(array("money"=>$price,"is_admin"=>1,"supplier_id"=>$data['supplier_id']), $order_info['user_id'], $info);
			modify_statements($price, 6, $data['name']."用户退款");
		}
		
		 
		
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		order_log($data['name']."退款成功 ".format_price($price)." ".$content, $order_id);
		auto_over_status($order_id);
		update_order_cache($order_id);
		distribute_order($order_id);

		// send_msg($order_info['user_id'], $data['name']."退款成功 ".format_price($price)." ".$content, "orderitem", $oi);		
		if($order_item['return_score'] < 0){
			$msg_content = '您购买的积分商品<'.$data['name'].'>退款成功,积分增加 '.$score;
			send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 10, 'data_id' => $oi));
			// send_msg($order_info['user_id'], $data['name']."积分订单成功退 ".$score."积分 ".$content, "orderitem", $oi);		
		}
		$msg_content = '您的商品<'.$data['name'].'>退款成功,余额增加￥'.round($price,2);
		send_msg_new($order_info['user_id'], $msg_content, 'account', array('type' => 3, 'data_id' => $oi));
		
		//发微信通知
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1)
		{
			$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_id);
			send_wx_msg("TM00430", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"refund_price"=>$price,"deal_name"=>$data['name'],"order_sn"=>$order_info['order_sn']));
		}
		$root=array();
		$root['status'] = 1;
		$root['info'] = "退款成功";
		ajax_return($root);
		
	}
	/**
	 * 拒绝退款
	 * 输入：act:deal_order
			 ctl:refund
			 order_item_id:订单商品id
			 coupon_id：消费券id
			 price：本次退款金额
			 content：备注
	 * 
	 * 输出：
		Array
			(
				[status] => 1   0：失败    1：成功
				[info]=>退款成功        消息
			)
	 */
	public function do_refuse()
	{
		$order_item_id = intval($_REQUEST['order_item_id']);
		$coupon_id = intval($_REQUEST['coupon_id']);
		$price = floatval($_REQUEST['price']);
		$score = floatval($_REQUEST['score']);
		$balance_price = floatval($_REQUEST['balance_price']);
		$content = strim($_REQUEST['content']);
		
		if($order_item_id)
		{
			$oi = $order_item_id;
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
			if($data['refund_status']==2)
			{
				$root=array();
				$root['status'] = 0;
				$root['info'] = "已退款";
				ajax_return($root);
			}
			if($data)
			{
				$order_id = $data['order_id'];
				$supplier_id = $data['supplier_id'];
			}
				
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3,is_arrival = 0 where id = ".$order_item_id);
				
			
			
			// 如果是自提商品，退款的时候，消费券也要设置拒绝退款状态
			if ($data['is_pick']) {
			    $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3 where order_deal_id = {$order_item_id}");
			}
			
			// 如果有申请退款的，设置为1，否则为3
			$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where refund_status = 1 and order_id = ".$order_id);
			$refund_status = $refund_item_count > 0 ? 1 : 3;
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = {$refund_status},is_refuse_delivery=0 where id = ".$order_id);
			
		}
		elseif($coupon_id)
		{
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id);
			if($data['refund_status']==2)
			{
				$root=array();
				$root['status'] = 0;
				$root['info'] = "已退款";
				ajax_return($root);
			}
			if($data)
			{
				$oi = $data['order_deal_id'];
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
				$data['name'] = $order_item['name'];
				$order_id = $data['order_id'];
				$supplier_id = $data['supplier_id'];
			}
			
			
			
				
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3 where id = ".$coupon_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3 where id = ".$data['order_deal_id']);
			
			// 如果有申请退款的，设置为1，否则为3
			$refund_coupon_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
			$refund_status = $refund_coupon_count > 0 ? 1 : 3;
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = {$refund_status} where id = ".$order_id);
			
				
		}
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		
		
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		order_log($data['name']."退款不通过 "." ".$content, $order_id);
		auto_over_status($order_id);
		update_order_cache($order_id);
		distribute_order($order_id);
		if($data['return_score']<0){
			order_log($data['name']."退积分不通过 "." ".$content, $order_id);

			$msg_content = '您购买的积分商品<'.$data['name'].', 退货申请已被驳回';
			send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 6, 'data_id' => $oi));
			// send_msg($order_info['user_id'], $data['name']."退积分不通过 "." ".$content, "orderitem", $oi);
		}
		$msg_content = '您的商品<'.$data['name'].'>退款申请被驳回';
		send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 5, 'data_id' => $oi));
		
		// send_msg($order_info['user_id'], $data['name']."退款不通过 "." ".$content, "orderitem", $oi);
		$root=array();
		$root['status'] = 1;
		$root['info'] = "操作成功";
		ajax_return($root);
		
	}
    /**
     * @desc 强制退单
     * 输入 ：order_item_id
     *      coupon_id
     * 输出： array（status=>"",info=>""）
     * @author    吴庆祥
     */
    public function do_verify()
    {
        $order_item_id = intval($_REQUEST['order_item_id']);
        $coupon_id = intval($_REQUEST['coupon_id']);
        require_once APP_ROOT_PATH."system/model/deal_order.php";
        $data=do_verify($order_item_id,$coupon_id);
        ajax_return($data);
    }

    /**
     * @desc
     * @author    吴庆祥
     */
    public function do_delivery(){
    	// $id = intval($_REQUEST['id']); //发货商品的ID
		$id = $_REQUEST['ids'];
	    if (empty($id)) {
	    	$data['status'] = 0;
	        $data['info'] = "请选择需要发货的商品";
	        ajax_return($data);
	    }
	    $ids = implode(',', $id);
		$express_id = intval($_REQUEST['express_id']);
		if(empty($express_id)) {
			$data['status'] = 0;
			$data['info'] = "请选择快递公司";
			ajax_return($data);
		}
    	$order_id_supplier_id = $GLOBALS['db']->getRow("select order_id,supplier_id from ".DB_PREFIX."deal_order_item where id in (".$ids.')');
    	$order_id=$order_id_supplier_id['order_id'];
    	$supplier_id=$order_id_supplier_id['supplier_id'];
   
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
		$order_table_name = get_supplier_order_table_name($supplier_id);
		 
		
		$express_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."express where id = ".$express_id);
		if (empty($express_info)) {
			$data['status'] = 0;
			$data['info'] = "快递公司选择有误，请重选或去后台编辑";
			ajax_return($data);
		}
		$delivery_sn = strim($_REQUEST['delivery_sn']);
		if(empty($delivery_sn)) {
			$data['status'] = 0;
			$data['info'] = "请输入快递单号";
			ajax_return($data);
		}
		// 检验单号是否重复
		$exist = $GLOBALS['db']->getOne('SELECT id FROM '.DB_PREFIX.'delivery_notice WHERE notice_sn = "'.$delivery_sn.'" AND express_id = '.$express_id);
		if ($exist) {
			$data['status'] = 0;
			$data['info'] = "快递单号已经存在";
			ajax_return($data);
		}
		$memo = strim($_REQUEST['memo']);
		$location_id = intval($_REQUEST['location_id']);
		
		$order_info = $GLOBALS['db']->getRow("select * from ".$order_table_name." where id = '".$order_id."'");
		// $is_delivery = intval($_REQUEST['is_delivery']);
		
		
		$item_sql = "select name, delivery_status from ".$order_item_table_name.' where id in('.$ids.') and refund_status in (0,3) and supplier_id='.$supplier_id;
		$item = $GLOBALS['db']->getAll($item_sql);
		$isvalid = true; // 判断是否每个商品都是可发货状态
		if ($item && (count($item) == count($id))) { // 获取的条数应和传递的数量一致
			$item_names = array();
			foreach ($item as $v) {
				if ($v['delivery_status'] != 0 && $isvalid == true) {
					$isvalid = false;
					break;
				}
				$item_names[] = $v['name'];
			}
			if (!$isvalid) {
				$data['status'] = 0;
				$data['info'] = "非法的参数";
				ajax_return($data);
			}
			$rs = make_delivery_notices($order_id, $id, $delivery_sn, $memo, $express_id, $location_id);
			if ($rs) {
				$delivery_status_sql = "update ".DB_PREFIX."deal_order_item set delivery_status = 1,delivery_time=".NOW_TIME.",delivery_memo='".$memo."',location_id=".$location_id." where id in (".$ids.')';
				$GLOBALS['db']->query($delivery_status_sql);
				$item_name = implode(',', $item_names);
				send_delivery_mail($delivery_sn, $item_name, $order_id);
				send_delivery_sms($delivery_sn, $item_name, $order_id);

				//开始同步订单的发货状态
				$order_deal_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);

				$nod5 = 0; // 无需发货的数量
				$nod0 = 0; // 未发货的数量
				foreach ($order_deal_items as $item) {
					if ($item['delivery_status'] == 5) {
						$nod5++;
					}
					if ($item['delivery_status'] == 0) {
						$nod0++;
					}
				}
				$update_sql_format = "update ".DB_PREFIX.'deal_order set delivery_status = %d, update_time = '.NOW_TIME.' where id='.$order_id;
				$itemCount = count($order_deal_items);
				$needSend = $itemCount - $nod5;
				$updateNum = 0;
				if ($nod0 > 0 && $needSend > $nod0) { // 待发货的条数大于0并且小于需要发货的条数
					$updateNum = 1; // 部分发货
				} elseif ($nod0 == 0 && $needSend > $nod0) { // 所有商品都已经发货
					$updateNum = 2;
				}
				if ($updateNum > 0) {
					$update_sql = sprintf($update_sql_format, $updateNum);
					$GLOBALS['db']->query($update_sql);
				}
				// 订单同步结束
					
				$log_msg = $item_name." 发货了，发货单号：".$delivery_sn;
				if ($memo) {
					$log_msg .= ' 备注: '.$memo;
				}
				order_log($log_msg, $order_id);
				update_order_cache($order_id);
				distribute_order($order_id);
				
				$msg_content = '您购买的<'.$item_name.'>已发货,物流单号: '.$delivery_sn;
				send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));
				
				//发微信通知			
				$weixin_conf = load_auto_cache("weixin_conf");
				if($weixin_conf['platform_status']==1) {
					$wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$supplier_id);
					
					send_wx_msg("OPENTM200565259", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"order_sn"=>$order_info['order_sn'],"company_name"=>$express_info['name'],"delivery_sn"=>$delivery_sn,"order_item_id"=>$id[0]));
				}
				
				if($delivery_sn){
				    //向快递网发送快递查询订阅
				    require_once(APP_ROOT_PATH.'system/model/express.php');
				    $express = new express();
				    $result = $express->get($expressCode=$express_info['class_name'],$delivery_sn,0,$order_info['region_lv3'],$order_id,$order_info['user_id'],$supplier_id,1,$memo,3);
				}

				$data['status'] = 1;
				$data['info'] = "发货成功";

				ajax_return($data);
			}
		}
		$data['status'] = 0;
		$data['info'] = "数据错误，请刷新重试";
		ajax_return($data);
    }
    public function un_do_delivery()
    {
    	//$s_account_info = $GLOBALS['account_info'];
    	//$supplier_id = intval($s_account_info['supplier_id']);
    	$ids = $_REQUEST['ids']; //发货商品的ID数组
    	$id = intval($ids[0]);  // 只会有一个商品

    	$supplier_location = $GLOBALS['db']->getAll("select doi.order_id,doi.supplier_id,dl.id as location_id from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."supplier_location as dl on doi.supplier_id = dl.supplier_id where doi.id = ".$id);
    	$account_location_ids = array();
    	foreach($supplier_location as $row)
    	{
    		$account_location_ids[] = $row['location_id'];
    	}
    	$order_id = intval($supplier_location[0]['order_id']);
    	$supplier_id = intval($supplier_location[0]['supplier_id']);
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    	$order_table_name = get_supplier_order_table_name($supplier_id);
    
    	
    	$location_id = intval($_REQUEST['location_id']);
    	$order_info = $GLOBALS['db']->getRow("select * from ".$order_table_name." where id = '".$order_id."'");
    	$is_delivery = intval($_REQUEST['is_delivery']);
    	$memo = strim($_REQUEST['memo']);
    	$item = $GLOBALS['db']->getRow("select doi.* from ".$order_item_table_name." as doi left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id where doi.id = ".$id." and l.location_id in (".implode(",",$account_location_ids).")");
    	if($item && $item['delivery_status']!=5) {
    		 
    		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,delivery_time=".NOW_TIME.",delivery_memo='".$memo."',location_id=".$location_id." where id = ".$id); //修改发货状态
    		 
    		 
    		if($GLOBALS['db']->affected_rows()) {
    			//开始同步订单的发货状态
    			$order_deal_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
    			foreach($order_deal_items as $k=>$v) {
    				if($v['delivery_status']==5) { //无需发货的商品
    					unset($order_deal_items[$k]);
    				}
    			}
    			$delivery_deal_items = $order_deal_items;
    			foreach($delivery_deal_items as $k=>$v) {
    				if($v['delivery_status']==0) {//未发货去除
    					unset($delivery_deal_items[$k]);
    				}
    			}
    
    			if(count($delivery_deal_items)==0&&count($order_deal_items)!=0) {
    				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0,update_time = '".NOW_TIME."' where id = ".$order_id); //未发货
    			} elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items)) {
    				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1,update_time = '".NOW_TIME."' where id = ".$order_id); //部分发
    			} else {
    				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2,update_time = '".NOW_TIME."' where id = ".$order_id); //全部发
    			}
    			 
    			update_order_cache($order_id);
    			distribute_order($order_id);
    
    			$data['status'] = 1;
    			$data['info'] = "发货成功";
    			 
    			ajax_return($data);
    		} else {
    			$data['status'] = 0;
    			$data['info'] = "发货失败";
    			ajax_return($data);
    		}
    
    	} else {
    		$data['status'] = 0;
    		$data['info'] = "数据错误，请刷新重试";
    		ajax_return($data);
    	}
    }
    public function load_delivery_form()
    {
    	$s_account_info = $GLOBALS['hiz_account_info'];
    	$account_id = $s_account_info['id'];
    	$id = intval($_REQUEST['id']); //发货商品的ID
    	//$s_account_info = $GLOBALS['account_info'];
    	$supplier_location = $GLOBALS['db']->getAll("select doi.supplier_id,dl.id as location_id from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."supplier_location as dl on doi.supplier_id = dl.supplier_id where doi.order_id = ".$id);
    	$account_location_ids = array();
    	foreach($supplier_location as $row)
    	{
    		$account_location_ids[] = $row['location_id'];
    	}
    	$supplier_id = intval($supplier_location[0]['supplier_id']);
    	
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    	$order_table_name = get_supplier_order_table_name($supplier_id);
    	
    	$ordersql = 'SELECT * FROM '.$order_table_name.' WHERE id='.$id;
    	$order = $GLOBALS['db']->getRow($ordersql);
    	if ($order) {
    		$itemsql = 'SELECT * FROM '.$order_item_table_name.' WHERE order_id = '.$order['id'].' AND delivery_status=0 AND refund_status in(0,3)';
    		$item = $GLOBALS['db']->getAll($itemsql);
    		if ($item) {
    			$is_delivery = 1;
    			$total_balance = 0;
    			foreach ($item as &$val) {
    				if ($val['is_delivery'] == 0) { // 无需配送
    					$is_delivery = 0;
    				}
    				$total_balance += $val['balance_total_price'];
    				$val['balance_unit_price'] = format_price($val['balance_unit_price']);
    				$val['balance_total_price'] = format_price($val['balance_total_price']);
    			}
    			unset($val);
    			$order['create_time'] = to_date($order['create_time']);
    			$assign = array(
    					'order' => $order,
    					'is_delivery' => $is_delivery,
    					'item' => $item,
    					'total_balance' => $total_balance,
    			);
    			if ($is_delivery) { // 需要配送。获取配送地址信息
    				$region_lv = array($order['region_lv1'], $order['region_lv2'], $order['region_lv3'], $order['region_lv4']);
    				$region_lv_sql = 'select name from '.DB_PREFIX.'delivery_region where id in ('.implode(',', $region_lv).') order by id';
    				$region_names = $GLOBALS['db']->getCol($region_lv_sql);
    	
    				$address = $order['address'];
    				$mobile = $order['mobile'];
    				$consignee = $order['consignee'];
    				$street = $order['street'];
    				$doorplate = $order['doorplate'];
    				$zip = $order['zip'];
    	
    				$location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where id in (".implode(",",$account_location_ids).")");
    				$express_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."express where is_effect = 1");
    				$assign['express_list'] = $express_list;
    				$assign['location_list'] = $location_list;
    				$assign['address'] = $consignee.'&nbsp;&nbsp;'.$mobile.'&nbsp;&nbsp;'.implode('', $region_names).$address.$street.$doorplate.'&nbsp;&nbsp;'.$zip;
    			}
    			$GLOBALS['tmpl']->assign($assign);
    			$data['html'] = $GLOBALS['tmpl']->fetch("pages/deal_order/delivery_form_new.html");
    			$data['status'] = 1;
    			ajax_return($data);
    		}
    	}
    	$data['status'] = 0;
    	$data['info'] = "非法的数据";
    	ajax_return($data);
    	 
    }
    /**
     * 根据关键字搜索可分配驿站(未删除、已审核且未禁用)
     * @return mix
     */
    public function keySearch()
    {
    	$s_account_info = $GLOBALS['hiz_account_info'];
    	$account_id = $s_account_info['id'];
    	$ajax = intval($_REQUEST['ajax']);
    	$key = strim($_REQUEST['key']);
    	if (empty($key)) {
    		$data['status'] = 0;
    		$data['info'] = "搜索关键字不能为空";
    		ajax_return($data);
    	}
    	//$condition = array('name' => array('like', '%'.$key.'%'), 'is_delete' => 0, 'status' => 1, 'disabled' => 0);
    	$res = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."distribution where status=1 and disabled=0 and is_delete=0 and name like '%".$key."%' and agency_id=".$account_id);
    	//M(MODULE_NAME)->where($condition)->findAll();
    	if ($res) {
    		$html = '<select name="dist_result" class="ui-select search-select"><option value="0">请选择</option>';
    		foreach ($res as $val) {
    			$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
    		}
    		$html .='</select>';
    		$data['status'] = 1;
    		$data['html'] = $html;
    		ajax_return($data);
    	} else {
    		$data['status'] = 0;
    		$data['info'] = "未检索到站点，换个关键字试试";
    		ajax_return($data);
    	}
    }
    public function changeDist()
    {
    	$id = intval($_REQUEST['id']);
    	$ajax = intval($_REQUEST['ajax']);
    	//$model = M('DealOrder');
    	$orderSql = 'SELECT id, distribution_id FROM '.DB_PREFIX.'deal_order WHERE id='.$id.' AND delivery_status = 0 AND pay_status = 2';
    	// $order = $GLOBALS['db']->getRow($orderSql);
    	$order = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where delivery_status=0 and id=".$id);
    	//$model->where(array('id' => $id, 'delivery_status' => 0))->find();
    	$info = '订单不存在或未付款';
    	$status=0;
    	if ($order) {
    		$did = intval($_REQUEST['did']);
    		if ($order['distribution_id'] == $did) {
    			$info = '相同驿站，无需重新分配';
    		} else {
    			$res = setDistAndAgent($did, $id);
    			if ($res) {
    				$status=1;
    				$info = '分配成功';
    			} else {
    				$info = '分配失败,请重试';
    			}
    		}
    	}
    	$data['status'] = $status;
    	$data['info'] = $info;
    	ajax_return($data);
    }
}
