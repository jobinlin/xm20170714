<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_tuanModule extends MainBaseModule
{
	public function index()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		/* 
		 * pay_status:订单状态    0:全部；1：待付款；2：待发货；3：待收货；4:待评价
		 *  */
		$pay_status=intval($_REQUEST['pay_status']);
		
		if($pay_status){
		    $GLOBALS['tmpl']->assign("pay_status",$pay_status);
		}else{
		    $GLOBALS['tmpl']->assign("pay_status",0);
		}
		
		// 条件判断重写
		$join=" ";
		$condition=" and (do.is_delete = 0 or (do.is_delete = 1 and do.is_cancel = 1)) ";
		switch ($pay_status) {
		    case '1': //'未付款订单';
		        $condition = " and do.order_process_status = 1 ";
		        break;
		    case '2': //'待发货订单';
		        $condition = " and do.order_process_status = 2 ";
		        break;
		    case '3':
		        //'待确认订单';  未删除 and 已支付 and （商品退款状态[无，已拒绝] or （券的退款状态为[无，拒绝退款] and 使用状态为未使用)) and 发货状态[全部发货、无需发货] and 未结单
		        $condition = " and do.order_process_status = 3 ";
		        break;
		    case '4': //'待评价订单';
		        $condition = " and do.order_process_status = 4 ";
		        break;
		}
		$condition .= ' and do.type = 5'; // 团购订单条件
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}	
		
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		$page_size = 10;
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;

		$user_id = $GLOBALS['user_info']['id'];
		
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$order_table_name = get_user_order_table_name($user_id);
		
		$sql = "select do.*,s.name as supplier_name,s.preview as supplier_preview  from ".$order_table_name." as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn left join " . DB_PREFIX . "supplier as s on s.id=do.supplier_id ".$join." where do.is_main=0 and ".
		" do.user_id = ".$user_id." and do.type <> 1 ".$condition." GROUP BY do.id order by do.create_time desc limit ".$limit;		
		
		$sql_count = "select count(distinct(do.id)) from ".$order_table_name." as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn left join " . DB_PREFIX . "supplier as s on s.id=do.supplier_id ".$join." where do.is_main=0  and ".
		" do.user_id = ".$user_id." and do.type <> 1 ".$condition;
		
		$list = $GLOBALS['db']->getAll($sql);
		
		foreach($list as $k=>$v) {
		    $list[$k]['status']=$this->order_status($v);
			$list[$k]['create_time'] = to_date($v['create_time']);
			$list[$k]['pay_amount_format'] = format_price($v['pay_amount']);
			$list[$k]['total_price_format'] = format_price($v['total_price']);
			$list[$k]['delivery_fee_format'] = format_price($v['delivery_fee']);
			
			if(!$v['supplier_name']){
			    $list[$k]['supplier_name']="平台自营";
			    $list[$k]['supplier_preview']=app_conf("SHOP_LOGO");
			}
			if($v['deal_order_item'])
			{
				$list[$k]['deal_order_item'] = unserialize($v['deal_order_item']);				
			}
			else
			{
				$order_id = $v['id'];
				update_order_cache($order_id);
				$list[$k]['deal_order_item'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
			}
			$list[$k]['c'] = count($list[$k]['deal_order_item']);
			foreach($list[$k]['deal_order_item'] as $kk=>$vv)
			{
				$list[$k]['deal_order_item'][$kk]['total_price'] = format_price($vv['total_price']);
				$list[$k]['deal_order_item'][$kk]['unit_price'] = format_price($vv['discount_unit_price']);
				$deal_info = load_auto_cache("deal",array("id"=>$vv['deal_id']));
				$list[$k]['deal_order_item'][$kk]['url'] = $deal_info['url'];
				if($v['pay_status']==2){
				    $list[$k]['deal_order_item'][$kk]['status']=$this->order_item_status($vv,$v['order_status']);
				}else{
				    if($v['is_delete']==1 && $v['is_cancel']==1){
				        $list[$k]['deal_order_item'][$kk]['status']=array(
				           "status"=>"已取消",
				        );
				    }else {
				        $list[$k]['deal_order_item'][$kk]['status']=array(
				            "status"=>"未付款",
				        );
				    }
				}
			}
		}
		
		$count = $GLOBALS['db']->getOne($sql_count);

		$page = new Page($count,$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("page_title","团购订单");
		assign_uc_nav_list();
		$GLOBALS['tmpl']->display("uc/uc_tuan_index.html");
	}
	
	
	
	/**
	 * 取消订单
	 */
	public function cancel()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and (is_delete = 0 or (is_delete = 1 and is_cancel = 1))and user_id = ".$GLOBALS['user_info']['id']);
			if($order_info) {
			    if(($order_info['is_delete'] && $order_info['is_cancel']) || $order_info['order_status']==1){
			        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_delete = 1,is_cancel = 0 where (order_status = 1 or pay_status = 0) and ( ( is_delete = 1 and is_cancel=1 ) or is_delete = 0)  and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
			        if($GLOBALS['db']->affected_rows()) {
			            
    			        $data['status'] = 1;
    			        $data['info'] = "订单删除成功";
    			        ajax_return($data);
			        } else {
			            $data['status'] = 0;
			            $data['info'] = "订单删除失败";
			            ajax_return($data);
			        }
			    } else {
			        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_delete = 1,is_cancel = 1 where pay_status = 0 and is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
			        
			        if($GLOBALS['db']->affected_rows()) {
			            require_once(APP_ROOT_PATH."system/model/deal_order.php");
			            //开始退已付的款
			            if($order_info['pay_status']==0&&$order_info['pay_amount']>0) {
			                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set pay_amount = 0,ecv_id = 0,ecv_money=0,account_money = 0 where id = ".$order_info['id']);
			                require_once(APP_ROOT_PATH."system/model/user.php");
			                if($order_info['account_money']>0) {
			                    modify_account(array("money"=>$order_info['account_money']), $order_info['user_id'],"取消订单，退回余额支付 ");
			                    order_log("用户取消订单，退回余额支付 ".$order_info['account_money']." 元", $order_info['id']);
			                }
			                if($order_info['ecv_id']) {
			                    $GLOBALS['db']->query("update ".DB_PREFIX."ecv set use_count = use_count - 1 where id = ".$order_info['ecv_id']);
			                    order_log("用户取消订单，代金券退回 ", $order_info['id']);
			                }
			        
			            }
			            over_order($order_info['id']);
			            $data['status'] = 1;
			            $data['info'] = "订单取消成功";
			            ajax_return($data);
			        } else {
			            $data['status'] = 0;
			            $data['info'] = "订单取消失败";
			            ajax_return($data);
			        }
			    }
				
			} else {
				$data['status'] = 0;
				$data['info'] = "订单不存在";
				ajax_return($data);
			}
		}
	}
	
	
	/**
	 * 查看订单内容
	 */
	public function view()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
		
		$GLOBALS['tmpl']->assign("page_title","团购订单");
		assign_uc_nav_list();
		
		$id = intval($_REQUEST['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where (is_delete = 0 or (is_delete=1 and is_cancel=1)) and type=5 and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
		
		if($order_info) {
			if($order_info['deal_order_item']) {
				$order_info['deal_order_item'] = unserialize($order_info['deal_order_item']);
			} else {
				update_order_cache($order_info['id']);
				$order_info['deal_order_item'] = $GLOBALS['db']->getAll("select doi.*,d.is_shop from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id=d.id where doi.order_id = ".$order_info['id']);
			}
			
			$order_info['create_time'] = to_date($order_info['create_time']);
			$order_info['deal_total_price'] = $order_info['deal_total_price']-$order_info['discount_price'];
			$order_info['deal_total_price_format'] = format_price($order_info['deal_total_price']-$order_info['discount_price']);
			$order_info['total_price_format'] = format_price($order_info['total_price']);
			$order_info['delivery_fee_format'] = format_price($order_info['delivery_fee']);
			$order_info['ecv_money_format'] = format_price($order_info['ecv_money']);
			//$order_info['discount_price_format'] = format_price($order_info['discount_price']);
			
			require_once(APP_ROOT_PATH . "system/model/deal_order.php");
			$fee=order_fee_arr($order_info);
			$order_info['fee']=$fee;
			
			//获取商家
			if($order_info['supplier_id']){
			    $order_info['supplier_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$order_info['supplier_id']);
			    $order_info["supplier_mobile"] = $GLOBALS['db']->getOne("select mobile from ".DB_PREFIX."supplier_account where is_main=1 and supplier_id=".$order_info['supplier_id']);
			}else if($order_info['type']==3){
			    $order_info['supplier_name'] = "平台自营";
			    $order_info["supplier_mobile"] = app_conf("SHOP_TEL");
			}

			$order_info['c'] = count($order_info['deal_order_item']);
			
			foreach($order_info['deal_order_item'] as $kk=>$vv)
			{
				$order_info['deal_order_item'][$kk]['discount_unit_price'] = format_price($vv['discount_unit_price']);
				$deal_info = load_auto_cache("deal",array("id"=>$vv['deal_id']));
				$order_info['deal_order_item'][$kk]['url'] = $deal_info['url'];
				$order_info['deal_order_item'][$kk]['forbid_sms'] = $deal_info['forbid_sms'];
				
				//获取商品状态
				if($order_info['pay_status']==2){
				    $order_info['deal_order_item'][$kk]['status']=$this->order_item_status($vv, $order_info['order_status']);
				}else{
				    if($order_info['is_delete']==1 && $order_info['is_cancel']==1){
				        $order_info['deal_order_item'][$kk]['status']=array(
				            "status"=>"已取消",
				        );
				    }else {
				        $order_info['deal_order_item'][$kk]['status']=array(
				            "status"=>"未付款",
				        );
				    }
				}

			    if($order_info["delivery_status"]==5 && $vv["is_shop"]==0){
			        $tuan_coupon = $GLOBALS['db']->getAll("select dc.*,m.content from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."message as m on dc.message_id = m.id where dc.is_valid > 0 and dc.user_id = ".$GLOBALS['user_info']['id']." and dc.order_deal_id = ".$vv['id']);
			        $order_info["order_type"]="tuan";
			    }
			}

			if ($order_info['invoice_info']) {
				$order_info['invoice_info'] = unserialize($order_info['invoice_info']);
			}
			
			//团购券状态处理
			if($tuan_coupon) {
			    foreach ($tuan_coupon as $t => $v){
			        if($v["end_time"]>0){
			            $tuan_coupon[$t]["end_time"]=to_date($v["end_time"],'Y-m-d');
			        }else{
			            $tuan_coupon[$t]["end_time"]="无限期";
			        }
			        if($v['confirm_time']>0){
			            $tuan_coupon[$t]['status']="已验证";
			        }
			        else if($v['refund_status']==1){
			            $tuan_coupon[$t]['status']="退款申请中";
			        }
			        else if($v['refund_status']==2){
			            $tuan_coupon[$t]['status']="已退款";
			        }
			        else if($v["end_time"]>0 && $v["end_time"]<NOW_TIME){
			            $tuan_coupon[$t]['status']="已过期";
			            if($v['expire_refund']==1 && $v['refund_status']<>3){
			                $tuan_coupon[$t]['handle']="申请退款";
			                $tuan_coupon[$t]["action"]=url("index","uc_order#refund",array("cid"=>$v['id']));
			                $tuan_coupon[$t]["class"] ="refund";
			            }
			        }
			        else if($v['refund_status']==3){
			            $tuan_coupon[$t]['status']="有效";
			        }
			        else if($v["any_refund"]){
			            $tuan_coupon[$t]['status']="有效";
			            $tuan_coupon[$t]['handle']="申请退款";
			            $tuan_coupon[$t]["action"]=url("index","uc_order#refund",array("cid"=>$v['id']));
	                    $tuan_coupon[$t]["class"] ="refund";
			        }else{
			            $tuan_coupon[$t]['status']="有效";
			        }
			    }
			}			
			
			$GLOBALS['tmpl']->assign("order_info",$order_info);
			
			$GLOBALS['tmpl']->assign("tuan_coupon",$tuan_coupon);

			//订单日志
			$order_logs = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_log where order_id = ".$order_info['id']." order by id desc");
			foreach ($order_logs as $kk => $vv){
			    $order_logs[$kk]['log_time']=to_date($vv['log_time']);
			}
			$GLOBALS['tmpl']->assign("order_logs",$order_logs);
			
			$GLOBALS['tmpl']->assign("NOW_TIME",NOW_TIME);
			$GLOBALS['tmpl']->display("uc/uc_tuan_view.html");
		} else {
			showErr("订单不存在");
		}
	}
	
	/**
	 * 退款申请
	 */
	public function refund()
	{
		global_run();
		init_app_page();
		if(check_save_login()!=LOGIN_STATUS_LOGINED) {
			$data['status'] = 1000;
			ajax_return($data);
		} else {
			$cid = intval($_REQUEST['cid']);
			$cids =  $_REQUEST['cids'];
 			
			if($cid) { //退券
				$coupon = $GLOBALS['db']->getRow("select dc.*,d.is_shop,doi.is_refund from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."deal as d on dc.deal_id=d.id left join ".DB_PREFIX."deal_order_item as doi on doi.id = dc.order_deal_id where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.id = ".$cid);
				
				if($coupon) {
					if($coupon['refund_status']==0&&$coupon['confirm_time']==0) { //从未退过款可以退款，且未使用过
						if(($coupon['is_shop']==1 && $coupon['is_refund']==1)||
						    ($coupon['any_refund']==1 && ($coupon['end_time']==0 || $coupon['end_time']>NOW_TIME))||
						    ($coupon['expire_refund']==1&&$coupon['end_time']>0&&$coupon['end_time']<NOW_TIME))//随时退或过期退已过期
						{
							$data['status'] = true;
							$GLOBALS['tmpl']->assign("cid",$cid);
							$data['html'] = $GLOBALS['tmpl']->fetch("inc/refund_form.html");
							ajax_return($data);
						} else {
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					} else {
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				} else {
					$data['status'] = 0;
					$data['info'] = "非法操作";
					ajax_return($data);
				}
			} else {
				$data['status'] = 0;
				$data['info'] = "非法操作";
				ajax_return($data);
			}
		}
	}
	
	
	
	/**
	 * 退款申请
	 */
	public function do_refund()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED) {
			$data['status'] = 1000;
			ajax_return($data);
		} else {
			$cid = intval($_REQUEST['cid']);
			$cids = $_REQUEST['cids'];
			$content = strim($_REQUEST['content']);
			$order_id=0;
			if(empty($content))
			{
				$data['status'] = 0;
				$data['info'] = "请填写退款原因";
				ajax_return($data);
			}
			
			if($cid) {
				//退券
				$coupon = $GLOBALS['db']->getRow("select dc.*,d.is_shop,doi.is_refund from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."deal as d on dc.deal_id=d.id left join ".DB_PREFIX."deal_order_item as doi on doi.id = dc.order_deal_id where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.id = ".$cid);
				if($coupon) {
					if($coupon['refund_status']==0&&$coupon['confirm_time']==0)//从未退过款可以退款，且未使用过
					{
						if(($coupon['is_shop']==1 && $coupon['is_refund']==1)||
						    ($coupon['any_refund']==1 && ($coupon['end_time']==0 || $coupon['end_time']>NOW_TIME))||
						    ($coupon['expire_refund']==1&&$coupon['end_time']>0&&$coupon['end_time']<NOW_TIME))//随时退或过期退已过期
						{
							//执行退券
							$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 1 where id = ".$coupon['id']);
							$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 1 where id = ".$coupon['order_deal_id']);
							$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 1 where id = ".$coupon['order_id']);
							
							$deal_order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$coupon['order_deal_id']);
							
							$msg = array();
							$msg['rel_table'] = "deal_order";
							$msg['rel_id'] = $coupon['order_id'];
							$msg['title'] = "退款申请";
							$msg['content'] = $content;
							$msg['create_time'] = NOW_TIME;
							$msg['user_id'] = $GLOBALS['user_info']['id'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."message",$msg);
							$message_id = intval($GLOBALS['db']->insert_id());
							update_order_cache($coupon['order_id']);
							if($message_id){
							    if ($cid) {
							        $GLOBALS['db']->query("update " . DB_PREFIX . "deal_coupon set message_id = " . $message_id . " where id =".$cid);
							        $item = $GLOBALS['db']->getRow("select id,order_deal_id from " . DB_PREFIX . "deal_coupon where id = ".$cid);
							        $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set message_id = " . $message_id . " where id = " . $item['order_deal_id']);
							    }
							}
							order_log($deal_order_item['sub_name']."申请退一张消费券，等待审核", $coupon['order_id']);
							
							require_once(APP_ROOT_PATH."system/model/deal_order.php");
							distribute_order($coupon['order_id']);
							$data['status'] = true;
							$data['info'] = "退款申请已提交，请等待审核";
							ajax_return($data);
						} else {
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					}
				}
			}
			$data['status'] = 0;
			$data['info'] = "非法操作";
			ajax_return($data);	
		}
	}
	
	/**
	 * 整笔订单的状态
	 * @param unknown $order_item 订单中的所有商品，二维数据
	 */
	public function order_status($order_info)
	{
	    $handle=array(
	        0=>array(
	            "status"=>"order_view",
	            "info"=>"查看订单",
	            "url"=>url("index","uc_tuan#view",array("id"=>$order_info['id'])),
	        ),
	        1=>array(
	            "status"=>"go_pay",
	            "info"=>"继续支付",
	            "url"=>url("index","cart#order",array("id"=>$order_info['id'])),
	        ),
	        2=>array(
	            "status"=>"cancel_order",
	            "info"=>"取消订单",
	            "action"=>url("index","uc_tuan#cancel",array("id"=>$order_info['id'])),
	            "class"=>"cancel_order",   
	        ),
	        3=>array(
	            "status"=>"del_order",
	            "info"=>"删除订单",
	            "action"=>url("index","uc_tuan#cancel",array("id"=>$order_info['id'])),
	            "class"=>"del_order",
	        ),
	    );
	    
	    if($order_info['pay_status']!=2 && $order_info['is_delete']==0){
	        //未支付
	        $status=array(
	            "handle"=>array(
	                $handle[0],
	                $handle[1],
	                $handle[2],
	            ),
	        );
	        return $status;
	    }
	    if($order_info['is_delete']==1 && $order_info['is_cancel']==1){
	        //已取消
	        $status=array(
	            "handle"=>array(
	                $handle[0],
	                $handle[3],
	            ),
	        );
	        return $status;
	    }
	    
	    if($order_info['order_status']==1){
	        //已完结
	        $status=array(
	            "status"=>"已完结",
	            "handle"=>array(
	                $handle[0],
	                $handle[3],
	            ),
	        );
	        return $status;
	    }

        //团购自提商品待结单订单
        $status=array(
            "handle"=>array(
                $handle[0],
            ),
        );
        return $status;
	}
	
	/**
     * 获取订单中单个商品的状态
     * $order_item_info 订单中单个商品，一维数据
     * $type  订单类型(0:商品订单 1:用户充值单,2:积分兑换订单，3:平台自营物流配送订单，4:平台自营驿站配送订单)
     */
	public function order_item_status($order_item_info,$order_status=0)
	{
	    $coupon_id=$GLOBALS['db']->getOne("select dc.id from ".DB_PREFIX."deal_coupon as dc where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.order_deal_id = ".$order_item_info['id']);
	    
	    $handle=array(
	        "deal_refund"=>array(
	            "info"=>"申请退款",
	            "action"=>url("index","uc_order#refund",array("did"=>$order_item_info['id'])),
	            "class"=>"refund",
	        ),
	        "coupon_refund"=>array(
	            "info"=>"申请退款",
	            "action"=>url("index","uc_order#refund",array("cid"=>$coupon_id)),
	            "class"=>"refund",
	        ),
	        "evaluate"=>array(
	            "info"=>"评价",
	            "url"=>url("index","review",array("order_item_id"=>$order_item_info['id'])),
	        ),
	        "tuan_coupon"=>array(
	            "info"=>"查看消费券",
	            "url"=>url("index","uc_coupon",array("did"=>$order_item_info['id'])),
	        ),
	    );
	    
        if ($order_item_info['delivery_status']==5){
                //团购
            if($order_item_info['dp_id']>0){
                $status=array(
                    "status"=>"已评价",
                );
                return $status;
            }
            $is_refund=0;
            $is_apply=0;
            $is_use=0;
            $is_end=0;
            $no_use=0;
            $coupon_info=$GLOBALS['db']->getAll("select id,confirm_time,deal_type,refund_status,end_time from " . DB_PREFIX . "deal_coupon where order_id=".$order_item_info['order_id']." and order_deal_id=".$order_item_info['id']);
            if(!$coupon_info || ($order_item_info['consume_count']>0 && $order_status==1)){
                $status=array(
                    "status"=> "评价",
                    "url"   => url("index","review",array("order_item_id"=>$order_item_info['id'])),
                );
                return $status;
            }
            
            foreach ($coupon_info as $vv){
                if(($vv["end_time"]>0 && $vv["end_time"]<NOW_TIME)){
                    $is_end=1;
                }
                
                if(!$vv['confirm_time'] && ($vv['refund_status']==0 || $vv['refund_status']==3)){
                    $no_use++;
                }
                
                if($vv['confirm_time']>0){
                    $is_use++;
                }
                
                if($vv['refund_status']==2){
                    $is_refund++;
                }
                if($vv['refund_status']==1){
                    $is_apply++;
                }
            }
            if($is_refund==count($coupon_info)){
                $status=array(
                    "status"=>"已退款"
                );
                return $status;
            }
            
            if($is_end==0 && $no_use>0){
                $status=array(
                    "status"=>"待验证"
                );
                return $status;
            }
            
            if($is_end==1 && $no_use>0){
                $status=array(
                    "status"=>"已过期"
                );
                return $status;
            }
            
            if($is_use>0 && !$is_apply){
                $status=array(
                    "status"=> "评价",
                    "url"   => url("index","review",array("order_item_id"=>$order_item_info['id'])),
                );
                return $status;
            }
            if($is_apply){
                $status=array(
                    "status"=>"退款中"
                );
                return $status;
            }
        }
	}
}
?>