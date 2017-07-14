<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class goodsoModule extends BizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
        $this->check_auth();
    }
	
    
	public function index_old()
	{			
		init_app_page();
		$s_account_info = $GLOBALS["account_info"];
		$supplier_id = intval($s_account_info['supplier_id']);
		
		//退款允许
		$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
		$GLOBALS['tmpl']->assign("allow_refund",$allow_refund);
		
		$name = strim($_REQUEST['name']);
		$begin_time = strim($_REQUEST['begin_time']);
		$end_time = strim($_REQUEST['end_time']);
		
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i");
		$end_time_s = to_timespan($end_time,"Y-m-d H:i");
		
		$condition = "";
		if($name!="")
			$condition .=" and (doi.name like '%".$name."%' or doi.sub_name like '%".$name."%') ";
		if($begin_time_s)
			$condition .=" and do.create_time > ".$begin_time_s." ";
		if($end_time_s)
			$condition .=" and do.create_time < ".$end_time_s." ";
		
		$GLOBALS['tmpl']->assign("name",$name);
		$GLOBALS['tmpl']->assign("begin_time",$begin_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
	    //分页
	    $page_size = 10;
	    $page = intval($_REQUEST['p']);
	    if($page==0) $page = 1;
	    $limit = (($page-1)*$page_size).",".$page_size;
	   
	    require_once(APP_ROOT_PATH."system/model/deal_order.php");
	    $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
	    $order_table_name = get_supplier_order_table_name($supplier_id);
	    
	    $sql = "select distinct(doi.id),doi.*,do.order_status,do.delivery_id,do.memo,do.create_time,do.order_sn,do.total_price,do.pay_amount,doi.refund_status,do.region_lv1,do.region_lv2,do.region_lv3,do.region_lv4,do.consignee,do.address,do.zip,do.mobile from ".$order_item_table_name." as doi left join ".
	 	    	$order_table_name." as do on doi.order_id = do.id left join ".
	    		DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".	    		
	    		" where l.location_id in (".implode(",",$s_account_info['location_ids']).") and do.is_delete = 0 and do.type = 0 and doi.is_shop = 1 and do.pay_status = 2 $condition order by doi.id desc limit ".$limit;
	    
	    $sql_count = "select count(distinct(doi.id)) from ".$order_item_table_name." as doi left join ".
	    		$order_table_name." as do on doi.order_id = do.id left join ".
	    		DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".
	    		" where l.location_id in (".implode(",",$s_account_info['location_ids']).") and do.is_delete = 0 and do.type = 0 and doi.is_shop = 1  and do.pay_status = 2 $condition ";
	
	    $list = $GLOBALS['db']->getAll($sql);
// 	    foreach($list as $kk=>$vv){
// 	        if($vv['delivery_status']==5 && $vv['is_shop']==1 && $vv['is_pick']==0){
// 	            $list[$kk]['is_delivery']=1;
// 	        }
// 	        else{
// 	            $list[$kk]['is_delivery']=0;
// 	        }
// 	    }
	    //print_r($list);exit;
	    $region_conf = load_auto_cache("cache_delivery_region_conf");
	    $delivery_conf = load_auto_cache("cache_delivery");
	   
	    foreach($list as $k=>$v){
	    	$uinfo = load_user($v['user_id']);
	    	$list[$k]['user_name']= $uinfo['user_name'];
	    	$list[$k]['create_time'] = to_date($v['create_time']);
	    	$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
	    	$list[$k]['url'] = $deal_info['url'];
	    	$list[$k]['s_total_price'] = $v['balance_total_price'] + $v['add_balance_price_total'];
	    	$list[$k]['region_lv1'] = $region_conf[$v['region_lv1']]['name'];
	    	$list[$k]['region_lv2'] = $region_conf[$v['region_lv2']]['name'];
	    	$list[$k]['region_lv3'] = $region_conf[$v['region_lv3']]['name'];
	    	$list[$k]['region_lv4'] = $region_conf[$v['region_lv4']]['name'];
	    	
	    	$list[$k]['delivery'] = $delivery_conf[$v['delivery_id']]['name'];
	    	
	    	$list[$k]['delivery_notice'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_notice where order_item_id = ".$v['id']." order by delivery_time desc limit 1");
	    
	    	$verify_count_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and confirm_time <> 0 and deal_type = 0");
	    	$verify_count_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and confirm_time <> 0 and deal_type = 1");
	    	$refund_status_1_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 1 and deal_type = 0");
	    	$refund_status_1_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 1 and deal_type = 1");
	    	$refund_status_2_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 2 and deal_type = 0");
	    	$refund_status_2_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 2 and deal_type = 1");
	    	$refund_status_3_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 3 and deal_type = 0");
	    	$refund_status_3_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 3 and deal_type = 1");
	    	
	    	$list[$k]['verify_count'] = $verify_count_0 + $verify_count_1*$v['number'];
	    	$list[$k]['refund_status_1'] = $refund_status_1_0 + $refund_status_1_1*$v['number'];
	    	$list[$k]['refund_status_2'] = $refund_status_2_0 + $refund_status_2_1*$v['number'];
	    	$list[$k]['refund_status_3'] = $refund_status_3_0 + $refund_status_3_1*$v['number'];
	    	
	    	
	    }
	    
	    $total = $GLOBALS['db']->getOne($sql_count);
	    $page = new Page($total,$page_size);   //初始化分页对象
	    $p  =  $page->show();
	    
	    $GLOBALS['tmpl']->assign('pages',$p);
	    $GLOBALS['tmpl']->assign('NOW_TIME',NOW_TIME);

	    // print_r($list);exit;
	    $GLOBALS['tmpl']->assign("list",$list);
	    		
		$GLOBALS['tmpl']->assign("ORDER_DELIVERY_EXPIRE",ORDER_DELIVERY_EXPIRE);
		$GLOBALS['tmpl']->assign("head_title","商品订单记录");
		$GLOBALS['tmpl']->display("pages/goodso/index.html");	
	}
	
	public function index()
	{			
		init_app_page();
		$s_account_info = $GLOBALS["account_info"];
		$supplier_id = intval($s_account_info['supplier_id']);
		
		//退款允许
		$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
		$GLOBALS['tmpl']->assign("allow_refund",$allow_refund);
		
		$name = strim($_REQUEST['name']);
		$begin_time = strim($_REQUEST['begin_time']);
		$end_time = strim($_REQUEST['end_time']);
		
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i");
		$end_time_s = to_timespan($end_time,"Y-m-d H:i");
		
		$condition = " do.type = 6 and doi.is_shop = 1 and do.pay_status = 2";
		if($name!="") {
			$condition .=" and (doi.name like '%".$name."%' or doi.sub_name like '%".$name."%') ";
		}
		if($begin_time_s) {
			$condition .=" and do.create_time > ".$begin_time_s." ";
		}
		if($end_time_s) {
			$condition .=" and do.create_time < ".$end_time_s." ";
		}
		
	    $assign = array(
	    	'name' => $name,
	    	'begin_time' => $begin_time,
	    	'end_time' => $end_time,
	    	'head_title' => '商品订单记录',
	    );

	    //分页
	    $page_size = 10;
	    $page = intval($_REQUEST['p']);
	    if($page==0) $page = 1;
	    $limit = (($page-1)*$page_size).",".$page_size;
	   
	    require_once(APP_ROOT_PATH."system/model/deal_order.php");
	    $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
	    $order_table_name = get_supplier_order_table_name($supplier_id);

	    $sql = 'SELECT do.id as doid,do.order_status,do.delivery_status as dstatus,do.create_time,do.order_sn,do.total_price as dtotal,do.user_name,do.invoice_info,doi.*,d.id AS did,d.uname AS duname FROM '.$order_table_name.' AS do LEFT JOIN '.$order_item_table_name.' AS doi ON do.id = doi.order_id LEFT JOIN '.DB_PREFIX.'deal AS d ON doi.deal_id = d.id WHERE do.supplier_id = '.$supplier_id.' AND '.$condition.' ORDER BY do.id DESC LIMIT '.$limit;
	    // print_r($sql);exit;
	    $sql_count = 'SELECT count(do.id) FROM '.$order_table_name.' AS do LEFT JOIN '.$order_item_table_name.' AS doi ON do.id = doi.order_id WHERE do.supplier_id = '.$supplier_id.' AND '.$condition;
	
	    $list = $GLOBALS['db']->getAll($sql);
	    
	    if ($list) {
	    	$total = $GLOBALS['db']->getOne($sql_count);
	    	$page = new Page($total,$page_size);   //初始化分页对象
	    	$p  =  $page->show();

	    	// 使用两次循环来判断获取订单状态
	    	$list1 = array();
	    	$doi_ids = array(); // 订单商品id
	    	foreach ($list as $val) {
	    		$format_time = to_date($val['create_time']);
	    		$dealUrl = url('index', 'deal#'.($val['uname'] ?: $val['did']));
	    		$val['url'] = $dealUrl;
	    		$val['item_status'] = $this->_itemStatus($val);
	    		if ($val['item_status'] == 4) { // 待收货
	    			$doi_ids[] = $val['id'];
	    		}
	    		
	    		$list1[$val['doid']]['order_sn'] = $val['order_sn'];
	    		$list1[$val['doid']]['create_time'] = $format_time;
		    	$list1[$val['doid']]['user_name'] = $val['user_name'];
		    	$list1[$val['doid']]['total_price'] = format_price($val['dtotal']);
		    	$list1[$val['doid']]['delivery_status'] = $val['dstatus'];

		    	// 处理发票信息
	    		if ($val['invoice_info']) {
	    			$val['invoice_info'] = unserialize($val['invoice_info']);
	    		}

	    		$list1[$val['doid']]['list'][] = $val;
	    	}

	    	// 获取每个商品的发货时间，超期收货用
	    	$dtArr = array();
	    	if ($doi_ids) {
	    		$dt_sql = "SELECT distinct(order_item_id), delivery_time FROM ".DB_PREFIX."delivery_notice WHERE order_item_id in (".implode(',', $doi_ids).")";
	    		$dt = $GLOBALS['db']->getAll($dt_sql);
	    		foreach ($dt as $t) {
	    			$dtArr[$t['order_item_id']] = $t['delivery_time'];
	    		}
	    	}
	    	


	    	foreach ($list1 as $key => $vals) {
	    		$vCount = count($vals['list']);  // 订单的商品条数
	    		$refund1 = 0;  // 退款中的数量
	    		$refund2 = 0;  // 已退款的数量
	    		$fi = 0; // 已完结|已自提 的数量
	    		$de = 0; // 待发货的数量
	    		// $is_pick = false;
	    		foreach ($vals['list'] as $k => $v) {
	    			switch ($v['item_status']) {
	    				case 0:
	    					$refund1++;
	    					// $de++;
	    					break;
	    				case 1:
	    					$refund1++;
	    					break;
	    				case 2:
	    					$refund2++;
	    					break;
	    				case 3:
	    					$de++;
	    					break;
	    				case 4:
	    					if ($dtArr[$v['id']] && (NOW_TIME - $dtArr[$v['id']] > 3600 * 24 * ORDER_DELIVERY_EXPIRE)) {
	    						// 强制收货时间判断
	    						$list1[$key]['force_dev'] = true;
	    					}
	    					break;
	    				case 5:
	    				case 6:
	    					$fi++;
	    					break;
	    				case 8:
	    					$fi++;
	    					$is_pick = true;
	    					break;
	    				case 9:
	    					$is_pick = true;
	    					break;
	    			}
	    		}
	    		if ($de > 0 && !$is_pick) { // 非自提订单待发货
	    			$status = 1;
	    		} elseif (($fi + $refund2) == $vCount) { // 已收货和退款通过的数量
	    			$status = 2; // 已完结
	    		} else {
	    			$status = 0;
	    		}
	    		$list1[$key]['ostatus'] = $status;
	    	}
	    	$assign['pages'] = $p;
	    	$assign['list'] = $list1;
	    }

	    // 判断商户是否开票
	    $invoiceConfSql = 'SELECT * FROM '.DB_PREFIX.'invoice_conf WHERE supplier_id='.$supplier_id;
	    $invoiceConf = $GLOBALS['db']->getRow($invoiceConfSql);
	    if ($invoiceConf && $invoiceConf['invoice_type']) {
	    	$assign['hasInvoiceConf'] = 1;
	    }

	    $GLOBALS['tmpl']->assign($assign);

		$GLOBALS['tmpl']->display("pages/goodso/index.html");	
	}

	protected function _itemStatus($order_item)
	{
		if ($order_item['refund_status'] == 1 && $order_item['delivery_status'] == 0) {
			return 0; // 申请退款且未发货
		} elseif ($order_item['refund_status'] == 1) {
			return 1; // 申请退款
		} elseif ($order_item['refund_status'] == 2) {
			return 2; // 退款审核通过
		} elseif ($order_item['is_pick']) { // 自提判断
			if ($order_item['consume_count'] > 0 && $order_item['dp_id'] == 0) {
				return 5;  // 自提完
			} elseif ($order_item['consume_count'] > 0 && $order_item['dp_id'] > 0) {
				return 6;
			}
			return 9; // 待验证
		} elseif ($order_item['delivery_status'] == 0) {
			return 3; // 待发货
		} elseif ($order_item['delivery_status'] == 1 && $order_item['is_arrival'] == 0) {
			return 4; // 待收货
		} elseif ($order_item['delivery_status'] == 1 && $order_item['is_arrival'] == 1 && $order_item['dp_id'] == 0) {
			return 5; // 待评价
		} elseif ($order_item['dp_id'] > 0) {
			return 6; // 已评价
		}
	}
	
	/**
	 * 快递查询
	 */
	public function check_delivery()
	{
		$id = intval($_REQUEST['id']);
		
		$s_account_info = $GLOBALS["account_info"];
		$supplier_id = intval($s_account_info['supplier_id']);
		
		
		$delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".DB_PREFIX."deal_location_link as l on l.deal_id = n.deal_id where n.order_item_id = ".$id." and  l.location_id in (".implode(",",$s_account_info['location_ids']).")  order by n.delivery_time desc");
		if($delivery_notice)
		{
			$data['status'] = true;
				
			$express_id = intval($delivery_notice['express_id']);
			$typeNu = strim($delivery_notice["notice_sn"]);
			$express_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."express where is_effect = 1 and id = ".$express_id);
			$express_info['config'] = unserialize($express_info['config']);
			$typeCom = strim($express_info['config']["app_code"]);
				
			if(isset($typeCom)&&isset($typeNu)){
					
				$AppKey = app_conf("KUAIDI_APP_KEY");//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
				$url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=0&muti=1&order=asc';
					
					
				//优先使用curl模式发送数据
				//KUAIDI_TYPE : 1. API查询 2.页面查询
				if (app_conf("KUAIDI_TYPE")==1){
					$data = es_session::get(md5($url));
					if(empty($data)||(NOW_TIME - $data['time'])>600)
					{
						$api_result = get_delivery_api_content($url);
						$api_result_status = $api_result['status'];
						$get_content = $api_result['html'];
							
						//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
						$powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
							
						$data['html'] = $get_content . '<br/>' . $powered;
						$data['status'] = true;   //API查询
						$data['time'] = NOW_TIME;
						if($api_result_status)
						es_session::set(md5($url),$data);
					}
						
					ajax_return($data);
				}else{
					$url = "http://www.kuaidi100.com/chaxun?com=".$typeCom."&nu=".$typeNu;
					app_redirect($url);
				}
					
			}else{
				if(app_conf("KUAIDI_TYPE")==1)
				{
					$data['status'] = false;
					$data['status'] = "非法的快递查询";
					ajax_return($data);
				}
				else
				{
					init_app_page();
					showErr("非法的快递查询");
				}
			}
				
		}
		else
		{
			if(app_conf("KUAIDI_TYPE")==1)
			{
				$data['status'] = false;
				ajax_return($data);
			}
			else
			{
				init_app_page();
				showErr("非法的快递查询");
			}
				
		}
	
	
	}
	
	
	public function do_delivery()
	{
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
		$order_table_name = get_supplier_order_table_name($supplier_id);
		 
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
		$order_id = $GLOBALS['db']->getOne("select order_id from ".$order_item_table_name." where id in (".$ids.')');
		$order_info = $GLOBALS['db']->getRow("select * from ".$order_table_name." where id = '".$order_id."'");
		// $is_delivery = intval($_REQUEST['is_delivery']);
		
		
		$item_sql = "select name, delivery_status from ".$order_item_table_name.' where id in('.$ids.') and refund_status in (0,3) and supplier_id='.$supplier_id;
		$item = $GLOBALS['db']->getAll($item_sql);
		// logger::write($item_sql, logger::ERR, logger::FILE);
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
	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	    require_once(APP_ROOT_PATH."system/model/deal_order.php");
	    $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
	    $order_table_name = get_supplier_order_table_name($supplier_id);
	    	
	    $ids = $_REQUEST['ids']; //发货商品的ID数组
	    $id = intval($ids[0]);  // 只会有一个商品
	    $location_id = intval($_REQUEST['location_id']);
	    $order_id = $GLOBALS['db']->getOne("select order_id from ".$order_item_table_name." where id = ".$id);
	    $order_info = $GLOBALS['db']->getRow("select * from ".$order_table_name." where id = '".$order_id."'");
	    $is_delivery = intval($_REQUEST['is_delivery']);
	    $memo = strim($_REQUEST['memo']);
	    $item = $GLOBALS['db']->getRow("select doi.* from ".$order_item_table_name." as doi left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id where doi.id = ".$id." and l.location_id in (".implode(",",$s_account_info['location_ids']).")");
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

}
?>