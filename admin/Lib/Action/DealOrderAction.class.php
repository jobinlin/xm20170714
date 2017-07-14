<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class DealOrderAction extends CommonAction{
    
    /**
     * 自动根据get请求的参数，生成搜索条件
     * @param $model   查询的模型
     * {@inheritDoc}
     * @see CommonAction::_search()
     */
    protected function _search($model) {
         
        //生成查询条件
        $map = array ();
        foreach ( $model->serchFields as $key => $val ) {
            if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '' ) {
                $map[$key] = $_REQUEST [$val];
            }
        }
        return $map;
    
    }
    
    
    /**
     * 查询订单数据
     * @param object  $model        查询的模型
     * @param integer $type         订单类型 1:用户充值单,2:积分兑换订单，3:平台自营物流配送订单，4:平台自营驿站配送订单,5:商家团购订单,6:商家商品订单
     * @param integer $is_supplier  是否商户订单 1是，0否
     * @param integer $pageNum      分页的当前页码
     * @return array  商品列表数组
     */
    protected function order_list($model, $type=0, $is_supplier=0, $pageNum=0){
        $orderModel = $model ? $model : D('DealOrderView');
        $map = $this->_search ($orderModel);
        $map['DealOrder.is_main']       = 0;
        $map['DealOrder.type']          = $type;
        
        // 商户订单，或者非商户订单
        $map['DealOrder.supplier_id'] = $is_supplier == 1 ?  array( 'gt' , 0) :  array( 'eq' , 0);
        
        $start_time = strtotime($_REQUEST['start_time']) - date('Z');
        $end_time   = strtotime($_REQUEST['end_time']) - date('Z');
        
        if ($start_time > 0 && $end_time > 0) {
            if ( $start_time >= $end_time ) {
                $this->error('开始时间必须小于结束时间');
            };
            $map['DealOrder.create_time'] = array('between', "{$start_time}, {$end_time}");
        }
        
        
        if( isset($map['DealOrder.refund_status']) ){
            $map['DealOrder.refund_status'] = $map['DealOrder.refund_status'] > 0 ? '1' : array( 'neq' , 1);
        }
        
        // 发货状态，必须是未关闭、未结单、已付款的订单
        if( isset($map['DealOrder.delivery_status']) ){
            if($map['DealOrder.delivery_status']==4){//统计未发货和部分发货款的
               // $map['DealOrder.delivery_status'] = array(0,1,'or');
               unset($map['DealOrder.delivery_status']);
               $map['DealOrderItem.delivery_status']=0;
            }
            
            $map['DealOrder.is_delete'] = 0;
            $map['DealOrder.order_status'] = 0;
            $map['DealOrder.pay_status']   = 2;
            
        }
        
        if( isset($map['DealOrder.order_status']) ){
            
            
            switch ( $map['DealOrder.order_status'] ) {
                case '0': // 待结单，已付款且没关闭的订单
                    $map['DealOrder.pay_status']   = 2;
                    $map['DealOrder.is_delete']    = 0;
                    break;
                case '1': // 交未关闭，且结单的
                    $map['DealOrder.is_delete'] = 0;
                    break;
                case '2': // 待付款订单
                    unset($map['DealOrder.order_status']);
                    $map['DealOrder.pay_status'] = array( 'neq' , 2);
                    $map['DealOrder.is_delete'] = 0;
                    break;
                
                case '3': // 交易关闭 订单
                    unset($map['DealOrder.order_status']);
                    $map['DealOrder.is_delete'] = 1;
                    break;
                
                default:
                    ;
                    break;
            }
        }
        //print_r($map);exit;
        $group='DealOrder.id';
        $distinct_field = 'DealOrder.id';
        $this->assign ('type', $type);
        $this->assign ('is_supplier', $is_supplier);
        return $this->_list($orderModel, $map, '', false, $pageNum,$group,$distinct_field);
       //echo $orderModel->getLastSql();
    }
     
    
    /**
     * type = 2
     * 积分订单
     */
    public function scoresOrder(){
        $orderModel = D('ScoresOrderView');
        $this->order_list($orderModel, 2);
        $this->assign ('title', '积分订单');
        $this->display();
    
    }
    
    /**
     * type = 3
     * 自营配送
     */
    public function selfOrder(){
        $this->order_list('', 3);
        $this->assign ('title', '自营订单');
        $this->display();
    }
    
    /**
     * type = 4
     * 自营驿站订单
     */
    public function distributionOrder(){
        $this->order_list('', 4);
        $this->assign ('title', '自营驿站订单');
        $this->display('selfOrder');
    }
    
    /**
     * type = 5
     * 团购订单
     */
    public function tuanOrder(){
        $orderModel = D('SupplierOrderView');
        $this->order_list($orderModel, 5, 1);
        $this->assign ('title', '团购订单');
        $this->display("supplierOrder");
    }
    
    /**
     * type = 6
     * 商城订单
     */
    public function shopOrder(){
        $orderModel = D('SupplierOrderView');
        $this->order_list($orderModel, 6, 1);
        $this->assign ('title', '商城订单');
        $this->display("supplierOrder");
    }
    
    public function order_detail(){
        $id = intval($_REQUEST['id']);
        $order_info = M("DealOrder")->where("id={$id}")->find();
        
        $type = intval($order_info['type']);
        $this->assign("type",$type);
        
        if(!$order_info)
        {
            $this->error(l("INVALID_ORDER"));
        }
        
        // 配送信息
        $region_ids = $order_info['region_lv2'].",".$order_info['region_lv3'].",".$order_info['region_lv4'];
        $region_names_db = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where id in(".$region_ids.")");
        $region_names = array();
        foreach ($region_names_db as $k=>$v){
            $region_names[$v['id']] = $v['name'];
        }
        $order_info['region_lv2'] = $region_names[$order_info['region_lv2']];
        $order_info['region_lv3'] = $region_names[$order_info['region_lv3']];
        $order_info['region_lv4'] = $region_names[$order_info['region_lv4']];


       // 驿站信息
        $disSql = 'SELECT name FROM '.DB_PREFIX.'distribution WHERE id = '.$order_info['distribution_id'];

        $order_info['distribute'] = $GLOBALS['db']->getOne($disSql);
        
        $order_deal_items = M("DealOrderItem")->where("order_id=".$order_info['id'])->findAll();
        
        
        $this->assign("is_pick", $order_deal_items[0]['is_pick']);
        
        $buy_type=intval($order_deal_items[0]['buy_type']);
        
        
        require_once(APP_ROOT_PATH."system/model/cart.php");
        $order_deal_items = cart_list_group($order_deal_items);
        $supplier_name = '';
        if($order_info['supplier_id']){ //存在商户id
            $supplier_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = ".$order_info['supplier_id']);
        }

        foreach($order_deal_items as $k=>$v)
        {
            if ($type==5){ //为团购的时候，只会有一条订单商品信息
                $order_deal_item = $v['goods_list'][0];
            }

            $order_deal_items[$k]['supplier'] = $supplier_name?$supplier_name:app_conf("SHOP_TITLE")."直营";
            $s_is_delivery = 0;
            foreach($v['goods_list'] as $kk=>$vv)
            {
                if($vv['is_delivery'])
                    $s_is_delivery = 1;
            }
            if($s_is_delivery)
                $order_deal_items[$k]['delivery_fee'] = round($GLOBALS['db']->getOne("select delivery_fee from ".DB_PREFIX."deal_order_supplier_fee where order_id = ".$order_info['id']." and supplier_id = ".$v['supplier_id']),2);
                else
                    $order_deal_items[$k]['delivery_fee'] = -1;
        }
        
        // 发票信息
        if (!empty($order_info['invoice_info'])) {
            $order_info['invoice_info'] = unserialize($order_info['invoice_info']);
        }
        
        $order_info['ecv_money']        = 0 - $order_info['ecv_money'];
        $order_info['youhui_money']     = 0 - $order_info['youhui_money'];
        $order_info['discount_price']   = 0 - $order_info['discount_price'];
		$order_info['pay_price']        = $order_info['total_price']+$order_info['ecv_money']+$order_info['youhui_money'];
        $this->assign("order_deals", $order_deal_items);
        $this->assign("order_info", $order_info);
        $this->assign("buy_type",$buy_type);
        
        $oid = $order_info['order_id'] ? $order_info['order_id'] : $order_info['id'];
        $payment_notice = M("PaymentNotice")->where("order_id = {$oid} and is_paid = 1 and order_type=3")->order("pay_time desc")->findAll();
        $this->assign("payment_notice",$payment_notice);

        //输出订单相关的消费券
        if ( $order_info['type'] == 5 ) {
            if($order_deal_item['is_coupon']){ //是否发券
                $coupon_list = D("DealCouponView")->where("DealCoupon.order_id = ".$order_info['id']." and DealCoupon.is_delete = 0")->order('DealCoupon.deal_id desc')->findAll();
                foreach($coupon_list as $k=>$v){
                    $coupon_list[$k]['is_coupon']=1;
                }
            }else{
                $coupon_list[] = array('deal_id'=>$order_deal_item['deal_id'],
                    'deal_name'=>$order_deal_item['name'],
                    'coupon_price'=>$order_deal_item['unit_price'],
                    'supplier_name'=>$supplier_name,
                    'supplier_id'=>$order_deal_item['supplier_id'],
                    'is_valid'=>0,
                    'end_time'=>'-',
                    'confirm_time'=>'-',
                    'password'=>'-',
                    'refund_status'=>$order_deal_item['refund_status'],
                    'is_coupon'=>$order_deal_item['is_coupon']);
              
            }
            $this->assign("coupon_list",$coupon_list);
        }

        
        //输出订单日志
        $log_list = M("DealOrderLog")->where("order_id=".$order_info['id'])->order("log_time desc, id desc")->findAll();
        $this->assign("log_list",$log_list);
        
        
        $item_info    = M("DealOrderItem")->where("order_id={$order_info['id']}")->find();
        
        $this->assign("delivery_type",$item_info['delivery_type']);
              
        $title = array(1=>'充值订单',2=>'积分订单',3=>'自营订单',4=>'自营-驿站订单',5=>'团购订单',6=>'商城订单', );
        
        $this->assign("title",$title[$type]);
        if ($type == 2) {
            $this->display('score_order_detail');
        }else{
            $this->display();
        }
        
    }
    
    
	public function incharge_index()
	{
		$reminder = M("RemindCount")->find();
		$reminder['incharge_count_time'] = NOW_TIME;
		M("RemindCount")->save($reminder);

		$condition['type'] = 1;
		if(strim($_REQUEST['user_name'])!='')
		{		
			$ids = M("User")->where(array("user_name"=>array('eq',strim($_REQUEST['user_name']))))->field("id")->findAll();
			$ids_arr = array();
			foreach($ids as $k=>$v)
			{
				array_push($ids_arr,$v['id']);
			}	
			$condition['user_id'] = array("in",$ids_arr);
		}
		
		$this->assign("default_map",$condition);
		parent::index();
	}
	
	public function incharge_trash()
	{
		$condition['type'] = 1;
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
			$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name="DealOrderHistory";
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	
	public function export_csv($parameter)
	{
		set_time_limit(0);
		error_reporting(0);
		$type         = intval($_REQUEST['type']);
		$is_supplier  = intval($_REQUEST['is_supplier']);
		
	    $is_history   = $parameter[1] ? $parameter[1] : intval($_REQUEST['is_history']);
	    $page         = $parameter[0] ? $parameter[0] : 1;
	    
	    // 如果是历史订单
	    if ($is_history == 1) {
	        $list = $this->deal_trash(1, $page);
	    }else{
	        $list = $this->order_list('', $type, $is_supplier, $parameter[0]);
	    }
	    
		
		if($list)
		{
			register_shutdown_function( array(&$this, 'export_csv'), array($page+1, $is_history));
			
			$order_value = array('sn'=>'""', 'user_name'=>'""', 'deal_name'=>'""','number'=>'""', 'create_time'=>'""', 'total_price'=>'""', 'pay_amount'=>'""', 'consignee'=>'""', 'address'=>'""', 'mobile'=>'""', 'memo'=>'""', 'delivery_status'=>'""','refund_status'=>'""','order_status'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","订单编号,用户名,商品名称,订购数量,下单时间,订单总额,已收金额,收货人,发货地址,手机号码,订单留言,发货状态,退款申请,订单状态");	    		    	
		    	$content = $content . "\n";
	    	}
	    	
			foreach($list as $k=>$v)
			{
			    require_once(APP_ROOT_PATH."system/model/user.php");
				$user_info = load_user($v['user_id']);
				$order_value['sn'] = '"' . "sn:".iconv('utf-8','gbk',$v['order_sn']) . '"';		
				$order_value['user_name'] = '"' . iconv('utf-8','gbk',$v['user_name']) . '"';
				$order_items = unserialize($v['deal_order_item']);
				$names = "";
				$total_num = 0;
				foreach($order_items as $key => $row)
				{
					$names.=  addslashes($row['name'])."[".$row['number']."]";
					if($key<count($order_items)-1)
					$names.="\n";
					$total_num+=$row['number'];
				}
				 
				$order_value['deal_name'] = '"' . iconv('utf-8','gbk',$names) . '"';
				$order_value['number'] = '"' . iconv('utf-8','gbk',$total_num) . '"';

				$order_value['create_time'] = '"' . iconv('utf-8','gbk',to_date($v['create_time'])) . '"';
				$order_value['total_price'] = '"' . iconv('utf-8','gbk',floatval($v['total_price'])."元") . '"';

				$order_value['pay_amount'] = '"' . iconv('utf-8','gbk',floatval($v['pay_amount'])."元") . '"';
				
				$order_value['consignee'] = '"' . iconv('utf-8','gbk',$v['consignee']) . '"';
				
				$region = array(
				    $v['region_lv1'],
				    $v['region_lv2'],
				    $v['region_lv3'],
				    $v['region_lv4']
				);
				$region = array_filter($region);
				$region_ids = join(',', $region);
				
				$region_info = $GLOBALS['db']->getAll( "select name from ".DB_PREFIX."delivery_region where id in($region_ids)" );
			   
				$address = $region_info[0]['name'].$region_info[1]['name'].$region_info[2]['name'].$region_info[3]['name'].$v['address'];
				$order_value['address'] = '"' . iconv('utf-8','gbk',$address) . '"';
				 
				 
				if($v['mobile']!='')
					$mobile = $v['mobile'];
				else
					$mobile = $user_info['mobile'];
				$order_value['mobile'] = '"' . iconv('utf-8','gbk',$mobile) . '"';
				$order_value['memo'] = '"' . iconv('utf-8','gbk',$v['memo']) . '"';
				
				// 发货状态get_delivery_status($status,$order_info)
				if($v['is_delete'] == 1 ){
				    $delivery_status =  '-';
				}elseif ($v['order_status'] == 1){
				    $delivery_status = '全部发货';
				}else{
				    $status_array = array(
				        '0' => '待发货',
				        '1' => '部份发货',
				        '2' => '全部发货',
				        '5' => '待发货',
				    );
				    $delivery_status = $status_array[$v['delivery_status']];
				}
				$order_value['delivery_status'] = '"' . iconv('utf-8','gbk', $delivery_status) . '"';
				
				
				$refund_status = $refund_status ? '申请退款':'-';
				$refund_status = $v['is_delete'] == 1 ? '-':$refund_status;
				$order_value['refund_status'] = '"' . iconv('utf-8','gbk', $refund_status) . '"';
				
				
				$order_value['order_status'] = '"' . iconv('utf-8','gbk',get_order_status_csv($v['order_status'], $v)) . '"';
				 
				$content .= implode(",", $order_value) . "\n";
				
			}
			
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content;
		}
		else
		{
		  $this->error('查询数据为空');
		}	
		
	}
	
	public function deal_trash($is_export=0, $pageNum)
	{
	    $type          = intval($_REQUEST['type']);
	    $is_supplier   = intval($_REQUEST['is_supplier']);
	    
	    $this->assign ('type', $type);
	    $this->assign ('is_supplier', $is_supplier);
	    
	    $orderModel = D('DealOrderHistoryView');
	    $map = $this->_search ($orderModel);
	    $map['DealOrderHistory.is_main']     = 0;
	    $map['DealOrderHistory.supplier_id'] = $is_supplier;
	    $map['DealOrderHistory.type']        = $type;
	    $map['DealOrderHistory.supplier_id'] = $is_supplier == 1 ?  array( 'gt' , 0) :  array( 'eq' , 0);
	    
	    if( isset($map['DealOrderHistory.refund_status']) ){
	        $map['DealOrderHistory.refund_status'] = $map['DealOrderHistory.refund_status'] > 0 ? '1' : array( 'neq' , 1);
	    }
	   
	    if ($is_export == 1) {
	        $list = $this->_list($orderModel, $map, '', false, $pageNum);
	        return $list;
	    }else{
	        $list = $this->_list($orderModel, $map);
	    }
	    $this->display();
	}
	
	
	
	public function pay_incharge()
	{
		$id = intval($_REQUEST['id']);
		//开始由管理员手动收款
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id);
		if($order_info['pay_status'] != 2)
		{
			require_once(APP_ROOT_PATH."system/model/cart.php");
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid = 0");
			if(!$payment_notice)
			{
				make_payment_notice($order_info['total_price'],$order_info['id'],$order_info['payment_id']);
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid = 0");
			}
			
			payment_paid(intval($payment_notice['id']));	//对其中一条款支付的付款单付款					
			$msg = sprintf(l("ADMIN_PAYMENT_PAID"),$payment_notice['notice_sn']);
			save_log($msg,1);
			$rs = order_paid($order_info['id']);
			
			if($rs)
			{
				$msg = sprintf(l("ADMIN_ORDER_PAID"),$order_info['order_sn']);
				save_log($msg,1);
				$this->success(l("ORDER_PAID_SUCCESS"));
			}
			else
			{
				$msg = sprintf(l("ADMIN_ORDER_PAID"),$order_info['order_sn']);
				save_log($msg,0);
				$this->error(l("ORDER_PAID_FAILED"));
			}
		}
		else 
		{
			$this->error(l("ORDER_PAID_ALREADY"));
		}
	}	
	public function delete() {
		//删除指定记录
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M('DealOrder')->where($condition)->findAll();					
				foreach($rel_data as $data)
				{
					if(del_order($data['id']))
					{
						$info[] = $data['order_sn'];
					}
				}
				$info = implode(",", $info);
				save_log($info.l("DELETE_SUCCESS"),1);
				$this->success (l("DELETE_SUCCESS"),$ajax);
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("DealOrderHistory")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['order_sn'];
				}
				if($info) $info = implode(",",$info);
				$list = M("DealOrderHistory")->where ( $condition )->delete();	
		
				if ($list!==false) {
					//删除关联数据
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function view_order()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$id)->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		$order_deal_items = M("DealOrderItem")->where("order_id=".$order_info['id'])->findAll();
	 
		$buy_type=intval($order_deal_items[0]['buy_type']);
		
		 
		require_once(APP_ROOT_PATH."system/model/cart.php");
		$order_deal_items = cart_list_group($order_deal_items);
		foreach($order_deal_items as $k=>$v)
		{
			$order_deal_items[$k]['supplier'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = '".$v['supplier_id']."'");
			$order_deal_items[$k]['supplier'] = $order_deal_items[$k]['supplier']?$order_deal_items[$k]['supplier']:app_conf("SHOP_TITLE")."直营";
			$s_is_delivery = 0;
			foreach($v['goods_list'] as $kk=>$vv)
			{
				if($vv['is_delivery'])
					$s_is_delivery = 1;
			}
			if($s_is_delivery)
			$order_deal_items[$k]['delivery_fee'] = round($GLOBALS['db']->getOne("select delivery_fee from ".DB_PREFIX."deal_order_supplier_fee where order_id = ".$order_info['id']." and supplier_id = ".$v['supplier_id']),2);
			else
				$order_deal_items[$k]['delivery_fee'] = -1;
		}
        
		$this->assign("order_deals",$order_deal_items);
		$this->assign("order_info",$order_info);
		$this->assign("buy_type",$buy_type);
		$payment_notice = M("PaymentNotice")->where("order_id = ".$order_info['id']." and is_paid = 1 and order_type=3")->order("pay_time desc")->findAll();
		$this->assign("payment_notice",$payment_notice);
		
		
		
		//输出订单留言
		$map['rel_table'] = 'deal_order';
		$map['rel_id'] = $order_info['id'];
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name= "Message"; 
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		//输出订单相关的消费券
		$coupon_list = M("DealCoupon")->where("order_id = ".$order_info['id']." and is_delete = 0")->findAll();
		$this->assign("coupon_list",$coupon_list);
		
		//输出订单日志
		$log_list = M("DealOrderLog")->where("order_id=".$order_info['id'])->order("log_time desc")->findAll();
		$this->assign("log_list",$log_list);
		
		$this->display();
	}
	
	
	public function view_order_history()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("DealOrderHistory")->where("id=".$id)->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		$order_deal_items = unserialize($order_info['history_deal_order_item']);
		$buy_type=intval($order_deal_items[0]['buy_type']);
		foreach($order_deal_items as $k=>$v)
		{
			$order_deal_items[$k]['is_delivery'] = $v['delivery_status']==5?0:1;
		}
		
		require_once(APP_ROOT_PATH."system/model/cart.php");
		$order_deal_items = cart_list_group($order_deal_items);
		
		foreach($order_deal_items as $k=>$v)
		{
			$order_deal_items[$k]['supplier'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = '".$v['supplier_id']."'");
			$order_deal_items[$k]['supplier'] = $order_deal_items[$k]['supplier']?$order_deal_items[$k]['supplier']:app_conf("SHOP_TITLE")."直营";
			$s_is_delivery = 0;
			foreach($v['goods_list'] as $kk=>$vv)
			{
				if($vv['is_delivery'])
					$s_is_delivery = 1;
			}
			
			
			$delivery_fees = unserialize($order_info['history_delivery_fee']);
			
			if($s_is_delivery)
			{
				foreach($delivery_fees as $kk=>$vv)
				{
					if($vv['supplier_id']==$v['supplier_id'])
					{
						$delivery_fee = $vv['delivery_fee'];
					}
				}
				$order_deal_items[$k]['delivery_fee'] = round($delivery_fee,2);
			}
			else
				$order_deal_items[$k]['delivery_fee'] = -1;
		}
		
		$this->assign("order_deals",$order_deal_items);
		$this->assign("order_info",$order_info);
		$this->assign("buy_type",$buy_type);
		$payment_notice = unserialize($order_info['history_payment_notice']);
		$this->assign("payment_notice",$payment_notice);
		
		
		$delivery_notice_rs = unserialize($order_info['history_delivery_notice']);
		foreach($delivery_notice_rs as $k=>$v)
		{
			$v['express_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."express where id = ".$v['express_id']);
			$delivery_notice[$v['order_item_id']] = $v;
		}
		$this->assign("delivery_notice",$delivery_notice);
	
	
	
		$deal_msg = unserialize($order_info['history_message']);
		$this->assign("deal_msg",$deal_msg);
		
		//输出订单相关的消费券
		$coupon_list = unserialize($order_info['history_deal_coupon']);
		$this->assign("coupon_list",$coupon_list);
	
		//输出订单日志
		$log_list = unserialize($order_info['history_deal_order_log']);
		$this->assign("log_list",$log_list);
	
		$this->display();
	}
	
	public function delivery()
	{
		$order_id = intval($_REQUEST['order_id']);
		$type     = intval($_REQUEST['type']); 
		$item_id  = intval($_REQUEST['item_id']);
		
		
		$item_info    = M("DealOrderItem")->where("id={$item_id}")->find();
		$order_info   = M("DealOrder")->where("id={$order_id} and is_delete = 0")->find();
		
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		 
		if ($item_info['delivery_status'] == 5) {
		    $this->error('该商品无需发货');
		}
		
		/* 
		`delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:已发货 5.无需发货（没有发货操作）',
		`delivery_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '配送方式(默认物流配送)1物流（如果自提，delivery_status=5，无需发货）、2无需配送(要发货，无物流)、3驿站',
		
		delivery_status = 5 没有发货按钮
		delivery_type    */
		
		$order_deal_items = M("DealOrderItem")->where("order_id={$order_info['id']} and delivery_status=0")->findAll();
		 

		foreach($order_deal_items as $k=>$v)
    		{
    		    // 已发货、无需发货、或者是已退款的商品不需要发货
    			if( $v['delivery_status'] == 1 || $v['refund_status']==1 || $v['refund_status']==2 || $v['is_arrival']==1 ) 
    			{
    				unset($order_deal_items[$k]);
    			}
    		}
		//输出快递接口
		
		$express_list = M("Express")->where("is_effect = 1")->findAll();		
		 
		$this->assign('delivery_status', $item_info['delivery_status']);
		$this->assign('delivery_type', $item_info['delivery_type']);
		$this->assign("express_list",$express_list);
		$this->assign("type",$type);
		$this->assign("order_deals",$order_deal_items);
		$this->assign("order_info",$order_info);
		$this->display();
	}
	
	//批量发货
	public function do_batch_delivery()
	{
		$delivery_sn = floatval($_REQUEST['begin_sn']);
		$order_ids = $_REQUEST['ids'];
		$order_ids = explode(",",$order_ids);
		$_REQUEST['silent'] = 1;	

		$idx = 0;
		foreach($order_ids as $k=>$order_id)
		{
			$_REQUEST['order_id'] = $order_id;			
			$order_items = $GLOBALS['db']->getAll("select doi.* from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where doi.order_id = ".$order_id." and d.is_delivery = 1");
			$order_deals = array();
			foreach($order_items as $kk=>$vv)
			{
				array_push($order_deals,$vv['id']);
			}
			if(count($order_deals)>0)
			{
				$_REQUEST['delivery_sn'] = $delivery_sn + $idx;
				$idx++;
			}
			$_REQUEST['order_deals'] = $order_deals;
			$_REQUEST['express_id'] = intval($_REQUEST['express_id']);
			$this->do_delivery();
		}
		
		$this->assign("jumpUrl",U("DealOrder/deal_index"));
		$this->success(l("BATCH_DELIVERY_SUCCESS"));	
	}
	public function load_batch_delivery()
	{
		$ids = strim($_REQUEST['ids']);
		$express_id = intval($_REQUEST['express_id']);
		if($express_id==0)
		{
			header("Content-Type:text/html; charset=utf-8");
			echo l("SELECT_EXPRESS_WARNING");
			exit;
		}
		$this->assign("ids",$ids);
		$this->assign("express_id",$express_id);
		$this->display();
	}

	public function do_delivery()
	{
		$params['silent'] = intval($_REQUEST['silent']);
		$params['order_id'] = intval($_REQUEST['order_id']);
		$params['order_deals'] = $_REQUEST['order_deals'];
		$params['delivery_sn'] = $_REQUEST['delivery_sn'];
		$params['express_id'] = intval($_REQUEST['express_id']);
		$params['memo'] = strim($_REQUEST['memo']);
        require_once APP_ROOT_PATH."system/model/deal_order.php";
        $data=do_delivery($params);
        $this->assign("jumpUrl",U("DealOrder/order_detail",array("id"=>$params['order_id'])));
        if($data['status']){
            $this->success($data['info']);
        }else{
            $this->error($data['info']);
        }
	}
	
	public function un_do_delivery()
	{
	    $silent = intval($_REQUEST['silent']);
	    $order_id = intval($_REQUEST['order_id']);
	    $order_deals = $_REQUEST['order_deals'];
	    $memo = strim($_REQUEST['memo']);
	    if(!$order_deals)
	    {
	        if($silent==0)
	            $this->error(l("PLEASE_SELECT_DELIVERY_ITEM"));
	    }
	    else
	    {
	        $deal_names = array();
	        foreach($order_deals as $order_deal_id)
	        {
	            $deal_info =$GLOBALS['db']->getRow("select d.*,doi.id as doiid from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_order_item as doi on doi.deal_id = d.id where doi.id = ".$order_deal_id);
	            $deal_name = $deal_info['sub_name'];
	            array_push($deal_names,$deal_name);
	        }
	        $deal_names = implode(",",$deal_names);
	       
	        $order_item_id = implode(",",$order_deals);
	        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,is_arrival = 0,delivery_time=".NOW_TIME.",delivery_memo='".$memo."' where id in (".$order_item_id.")");
	        
	        if($GLOBALS['db']->affected_rows()){
    	        //开始同步订单的发货状态
    	        $order_deal_items = M("DealOrderItem")->where("order_id=".$order_id)->findAll();
    	        
    	        foreach($order_deal_items as $k=>$v)
    	        {
    	            if($v['delivery_status']==5) //无需发货的商品
    	            {
    	                unset($order_deal_items[$k]);
    	            }
    	        }
    	        $delivery_deal_items = $order_deal_items;
    	        foreach($delivery_deal_items as $k=>$v)
    	        {
    	            if($v['delivery_status']==0) //未发货去除
    	            {
    	                unset($delivery_deal_items[$k]);
    	            }
    	        }
    	        
    	        //M("DealOrderItem")->where("order_id=".$order_id)->setField("delivery_status",1);
    	
    	        if(count($delivery_deal_items)==0&&count($order_deal_items)!=0)
    	        {
    	            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0 where id = ".$order_id); //未发货
    	        }
    	        elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items))
    	        {
    	            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1 where id = ".$order_id); //部分发
    	        }
    	        else
    	        {
    	            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2 where id = ".$order_id); //全部发
    	        }
    	        
    	        M("DealOrder")->where("id=".$order_id)->setField("update_time",NOW_TIME);
    	        M("DealOrder")->where("id=".$order_id)->setField("is_refuse_delivery",0);
    	
    	        $refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
    	        $coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
    	        if($refund_item_count==0&&$coupon_item_count==0){
    	            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 0,is_refuse_delivery=0 where id = ".$order_id);
    	        }
    	        $msg = l("DELIVERY_SUCCESS");
    	      
    	        $this->assign("jumpUrl",U("DealOrder/order_detail",array("id"=>$order_id)));
    	        require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	        update_order_cache($order_id);
    	        distribute_order($order_id);
    	
    	        $order_info = M("DealOrder")->getById($order_id);
    	        
    	        $msg_content = '您购买的<'.$deal_info['name'].'>已发货!';
    	        send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));
    	        order_log("发货成功    "."备注：".$_REQUEST['memo'],$order_id);
    	        if($silent==0)
    	            $this->success($msg);
	        }else {
	            $this->error(l("发货失败"));
	        }
	    }
	}
	
	//查看快递
	public function check_delivery()
	{
		$express_id = intval($_REQUEST['express_id']);
		$typeNu = addslashes(trim($_REQUEST["express_sn"]));
		$express_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."express where is_effect = 1 and id = ".$express_id);
		$express_info['config'] = unserialize($express_info['config']);
		$typeCom = trim($express_info['config']["app_code"]);
		
		if(isset($typeCom)&&isset($typeNu)){
		
			$AppKey = app_conf("KUAIDI_APP_KEY");//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY

			$data['msg'] = "http://www.kuaidi100.com/chaxun?com=".$typeCom."&nu=".$typeNu;
			$data['status'] = 1;   //页面查询
			ajax_return($data);
		}else{
			$data['msg'] = '查询失败，请重试';
			$data['status'] = 0;   //查询失败
			ajax_return($data);
		}
		exit();
	}	
	
	
	public function order_incharge()
	{
		$order_id  = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER")."");
		}
		
		
		if($order_info['region_lv4']>0)
		$region_id = $order_info['region_lv4'];
		elseif($order_info['region_lv3']>0)
		$region_id = $order_info['region_lv3'];
		elseif($order_info['region_lv2']>0)
		$region_id = $order_info['region_lv2'];
		else
		$region_id = $order_info['region_lv1'];
		
		$delivery_id = $order_info['delivery_id'];
		$payment_id = 0;		
		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);		
		$GLOBALS['user_info']['id'] = $order_info['user_id'];
		require_once(APP_ROOT_PATH."system/model/cart.php");
		 
		$result = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=0,$ecvsn,$ecvpassword,$goods_list,$order_info['account_money'],$order_info['ecv_money'],$order_info['bank_id'],0,$order_info['exchange_money']);
		$result['delivery_fee'] = $order_info['delivery_fee'];
		$result['pay_total_price'] = $result['pay_total_price'] + $order_info['delivery_fee'];
		$result['pay_price'] = $result['pay_price'] + $order_info['delivery_fee'];
		$this->assign("result",$result);
		
	
		
		
		$payment_list = M("Payment")->where("is_effect = 1 and class_name <> 'Voucher' and class_name <> 'Cod'")->findAll();
		$this->assign("payment_list",$payment_list);
		$this->assign("user_money",M("User")->where("id=".$order_info['user_id'])->getField("money"));
		$this->assign("order_info",$order_info);
		$this->display();
	}
	
	public function do_incharge()
	{
		$order_id  = intval($_REQUEST['order_id']);
		$payment_id = intval($_REQUEST['payment_id']);
		$payment_info = M("Payment")->getById($payment_id);
		$memo = $_REQUEST['memo'];
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		
		if($order_info['region_lv4']>0)
		$region_id = $order_info['region_lv4'];
		elseif($order_info['region_lv3']>0)
		$region_id = $order_info['region_lv3'];
		elseif($order_info['region_lv2']>0)
		$region_id = $order_info['region_lv2'];
		else
		$region_id = $order_info['region_lv1'];
		
		$delivery_id = $order_info['delivery_id'];
		$payment_id = intval($_REQUEST['payment_id']);		
		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);		
		$GLOBALS['user_info']['id'] = $order_info['user_id'];
		require_once(APP_ROOT_PATH."system/model/cart.php");
		$result = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=0,$ecvsn,$ecvpassword,$goods_list,$order_info['account_money'],$order_info['ecv_money'],$order_info['bank_id'],0,$order_info['exchange_money']);
		
		$result['delivery_fee']    = $order_info['delivery_fee'];
		$result['pay_total_price'] = $result['pay_total_price'] + $order_info['delivery_fee'];
		$result['pay_price']       = $result['pay_price'] + $order_info['delivery_fee'];
		

		$user_money = M("User")->where("id=".$order_info['user_id'])->getField("money");
		//$pay_amount = $order_info['deal_total_price']+ $order_info['delivery_fee']-$order_info['account_money']-$order_info['ecv_money']+$payment_info['fee_amount'];
		$pay_amount = $result['pay_price'];
		
		
		if($payment_info['class_name']=='Account'&&$user_money<$pay_amount) 
		$this->error(l("ACCOUNT_NOT_ENOUGH"));

		$notice_id = make_payment_notice($pay_amount,$order_id,$payment_id,$memo);
		
		$order_info['total_price'] = $result['pay_total_price'];
		$order_info['payment_fee'] = $result['payment_fee'];  
		$order_info['discount_price'] = $result['user_discount'];
		$order_info['payment_id'] = $payment_info['id'];
		$order_info['update_time'] = NOW_TIME;
		M("DealOrder")->save($order_info);
		
		$payment_notice = M("PaymentNotice")->getById($notice_id);
		$rs = payment_paid($payment_notice['id']);	
		if($rs&&$payment_info['class_name']=='Account')
		{
			//余额支付
			require_once(APP_ROOT_PATH."system/payment/Account_payment.php");				
			require_once(APP_ROOT_PATH."system/model/user.php");
			
			$msg = sprintf('%s订单付款,付款单号%s',$order_info['order_sn'],$payment_notice['notice_sn']);			
			modify_account(array('money'=>"-".$payment_notice['money'],'is_admin'=>1,'score'=>0),$payment_notice['user_id'],$msg);
		}

		
		if($rs)
		{	
			order_paid($order_id);
			$msg = sprintf(l("MAKE_PAYMENT_NOTICE_LOG"),$order_info['order_sn'],$payment_notice['notice_sn']);
			save_log($msg,1);
			order_log($msg.$_REQUEST['memo'],$order_id);
			$this->assign("jumpUrl",U("DealOrder/order_detail",array("id"=>$order_id)));
			$this->success(l("ORDER_INCHARGE_SUCCESS"));
		}
		else
		{
			$this->assign("jumpUrl",U("DealOrder/order_detail",array("id"=>$order_id)));
			$this->success(l("ORDER_INCHARGE_FAILED"));
		}
	}
	
	public function lottery_index()
	{
		if(strim($_REQUEST['user_name'])!='')
		{		
			$ids = M("User")->where(array("user_name"=>array('eq',strim($_REQUEST['user_name']))))->field("id")->findAll();
			$ids_arr = array();
			foreach($ids as $k=>$v)
			{
				array_push($ids_arr,$v['id']);
			}	
			$map['user_id'] = array("in",$ids_arr);
		}
		
		if(intval($_REQUEST['deal_id'])>0)
		$map['deal_id'] = intval($_REQUEST['deal_id']);
		
		if(strim($_REQUEST['lottery_sn'])!='')
		$map['lottery_sn'] = strim($_REQUEST['lottery_sn']);

		$model = D ("Lottery");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	public function del_lottery()
	{
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("Lottery")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['lottery_sn'];						
				}
				if($info) $info = implode(",",$info);
				$list = M("Lottery")->where ( $condition )->delete();
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function referer()
	{
		if(isset($_REQUEST['referer'])&&strim($_REQUEST['referer'])!='')
		{
			$where = "referer = '".strim($_REQUEST['referer'])."' ";
			$map['referer'] = array("eq",strim($_REQUEST['referer']));

		}
		else
		{
			$where = " 1=1 ";

		}
		$where.=" and type <> 1";
		$map['type'] = array("neq",1);
		$begin_time  = strim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = strim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		if($end_time==0)
		{
			$where.=" and create_time > ".$begin_time;			
			$map['create_time'] = array("gt",$begin_time);
		}
		else
		{
			$where.=" and create_time between ".$begin_time." and ".$end_time;	
			$map['create_time'] = array("between",array($begin_time,$end_time));
		}	
		$sql = "select referer,count(id) as ct from ".DB_PREFIX."deal_order where ".$where." group by referer having count(id) > 0 ";
		$sql_count = "select referer from ".DB_PREFIX."deal_order where ".$where." group by referer having count(id) > 0 ";
		
		$count = $GLOBALS['db']->getAll($sql_count);
		
		//开始list
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : "ct";
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = count($count);
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
			$sql .= "order by `" . $order . "` " . $sort;
			$sql .= " limit ".$p->firstRow . ',' . $p->listRows;

			$voList = $GLOBALS['db']->getAll($sql);
			
//			echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		$this->display ();
	}
	
	
	
	//退款的审核界面
	public function refund()
	{
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
		}		
		elseif($coupon_id)
		{
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
           
            // 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
            $scale = $data['total_price'] / $order_info['deal_total_price'] ;
            // 当前商品折扣价    = 当前商品价格比例  * 总折扣价
            $scale_discount_price = $scale * $order_info['discount_price'];
            // 当前商品红包价格
            $scale_ecv_money      = $scale * $order_info['ecv_money'];
            // 当前商品优惠劵价格
            $scale_youhui_money   = $scale * $order_info['youhui_money'];
            
            // 当前商品积分抵扣价格
            $scale_exchange_money   = $scale * $order_info['exchange_money'];
            	
            // 当前商品实际支付价格
            $real_total_price         = $data['total_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;
            $data['real_total_price'] = format_price_floor($real_total_price);
   
		    // 红包有可能扣的是运费，导致退款为负数，所以小于0的时候，显示0
		    $data['real_total_price'] = $data['real_total_price'] < 0 ? 0 : $data['real_total_price'];
			if($order_info['cod_money']>0){//判断为货到付款的订单
				$data['real_total_price']=0;
			}
		    
		    $order_info['ecv_money']      = format_price_floor( $order_info['ecv_money'] );
		    $order_info['pay_amount']     = format_price_floor( ($order_info['pay_amount']-$order_info['cod_money']) );
		    $order_info['delivery_fee']   = format_price_floor( $order_info['delivery_fee'] );
		    $order_info['youhui_money']   = format_price_floor( $order_info['youhui_money'] );
		    
			if($data['price']<0)$data['price'] = 0;
			if($data['score']<0)$data['score'] = 0;
			$this->assign("data",$data);
			$this->assign("order_info",$order_info);
			$obj['status'] = true;
			$obj['html'] = $this->fetch();
			ajax_return($obj);
		}
		else		
		$this->error("非法请求",1);
	}
	
	/**
	 * 退款执行流：
	 * 1. 退还金额至会员账户
	 * 2. 更新商家账户
	 * 3. 更新订单及订单关联表的相关状态
	 * 3. 更新平台报表
	 * 4. 更新订单缓存
	 * 5. 为订单重新分片
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
			$this->error("金额出错",1);
		}
		
		if($score<0)
		{
			$this->error("积分出错",1);
		}
		
		if($order_item_id)
		{
			$oi = $order_item_id;
			$order_item = $data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
			if($data['refund_status']==2) 
			{
				$this->error("已退款",1);
			}
			
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = {$data['order_id']}");
			
			// 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
			$scale = $data['total_price'] / $order_info['deal_total_price'];
			// 当前商品折扣价    = 当前商品价格比例  * 总折扣价
			$scale_discount_price = $scale * $order_info['discount_price'];
			// 当前商品红包价格
			$scale_ecv_money      = $scale * $order_info['ecv_money'];
			// 当前商品优惠劵价格
			$scale_youhui_money   = $scale * $order_info['youhui_money'];
			// 当前商品积分抵扣价格
			$scale_exchange_money   = $scale * $order_info['exchange_money'];
			
			// 当前商品实际支付价格
			$real_total_price         = $data['total_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;

			if( $price > $real_total_price ){
			    $this->error("您的退款金额，已经超出订单的总金额！",1);
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
				$this->error("已退款",1);
			}
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = {$data['order_id']}");
				
			// 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
			$scale = $data['coupon_price'] / $order_info['deal_total_price'];
			// 当前商品折扣价    = 当前商品价格比例  * 总折扣价
			$scale_discount_price = $scale * $order_info['discount_price'];
			// 当前商品红包价格
			$scale_ecv_money      = $scale * $order_info['ecv_money'];
			// 当前商品优惠劵价格
			$scale_youhui_money   = $scale * $order_info['youhui_money'];
			// 当前商品积分抵扣价格
			$scale_exchange_money   = $scale * $order_info['exchange_money'];
				
			// 当前商品实际支付价格
			$real_total_price     = $data['coupon_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money - $scale_exchange_money;
			
			if( $price > $real_total_price ){
			    $this->error("您的退款金额，已经超出订单的总金额！",1);
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
		$this->success("退款成功",1);
	}
	
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
				$this->error("已退款",1);
			}
			if($data)
			{
				$order_id = $data['order_id'];
				$supplier_id = $data['supplier_id'];
			}
				
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3,is_arrival = 0, admin_memo='{$content}' where id = ".$order_item_id);
				
			
			
			// 如果是自提商品，退款的时候，消费券也要设置拒绝退款状态
			if ($data['is_pick']) {
			    $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3, admin_memo='{$content}' where order_deal_id = {$order_item_id}");
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
				$this->error("已退款",1);
			}
			if($data)
			{
				$oi = $data['order_deal_id'];
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$data['order_deal_id']);
				$data['name'] = $order_item['name'];
				$order_id = $data['order_id'];
				$supplier_id = $data['supplier_id'];
			}
			
			
			
				
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 3, admin_memo='{$content}' where id = ".$coupon_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 3, admin_memo='{$content}' where id = ".$data['order_deal_id']);
			
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

			$msg_content = '-<'.$data['name'].', 退货申请已被驳回';
			send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 6, 'data_id' => $oi));
			// send_msg($order_info['user_id'], $data['name']."退积分不通过 "." ".$content, "orderitem", $oi);
		}
		$msg_content = '您的商品<'.$data['name'].'>退款申请被驳回';
		send_msg_new($order_info['user_id'], $msg_content, 'notify', array('type' => 5, 'data_id' => $oi));
		
		// send_msg($order_info['user_id'], $data['name']."退款不通过 "." ".$content, "orderitem", $oi);
		$this->success("操作成功",1);
	}
	
	
	public function do_verify()
	{
		$order_item_id = intval($_REQUEST['order_item_id']);
		$coupon_id = intval($_REQUEST['coupon_id']);
		require_once APP_ROOT_PATH."system/model/deal_order.php";
        $data=do_verify($order_item_id,$coupon_id);
		ajax_return($data);
		
	}
	
	public function cancel()
	{
	
			$id = intval($_REQUEST['id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id);
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
							//order_log("交易被关闭，退回余额支付 ".$order_info['account_money']." 元", $order_info['id']);
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
					$this->success("操作成功");
				}
				else
				{
					$this->error("交易无法被关闭");
				}
			}
			else
			{
				$this->error("订单不存在");
			}
		
	}


	public function changeDist()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$model = M('DealOrder');
		$orderSql = 'SELECT id, distribution_id FROM '.DB_PREFIX.'deal_order WHERE id='.$id.' AND delivery_status = 0 AND pay_status = 2';
		// $order = $GLOBALS['db']->getRow($orderSql);
		$order = $model->where(array('id' => $id, 'delivery_status' => 0))->find();
		$info = '订单不存在或未付款';
		if ($order) {
			$did = intval($_REQUEST['did']);
			if ($order['distribution_id'] == $did) {
				$info = '相同驿站，无需重新分配';
			} else {
				$res = setDistAndAgent($did, $id);
				if ($res) {
					$info = '分配成功';
					$this->success($info, $ajax);
				} else {
					$info = '分配失败,请重试';
				}
			}
		}
		$this->error($info, $ajax);
	}

}
?>