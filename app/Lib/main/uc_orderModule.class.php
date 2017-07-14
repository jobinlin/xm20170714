<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_orderModule extends MainBaseModule
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
		    case '1':
		        //'未付款订单';
		        //$condition = ' and do.is_delete = 0 and do.pay_status <> 2 ';
		        $condition .= " and do.pay_status <> 2 and do.return_total_score >= 0 ";
		        break;
		    case '2':
		        //'待发货订单';
		        //$condition = ' and do.is_delete = 0 and do.pay_status = 2 and do.delivery_status in (0,1) and d.is_pick=0 and d.is_shop=1 and d.delivery_status=0 and d.refund_status in (0,3) and do.order_status = 0 ';
		        $condition .= " and do.order_process_status = 2 ";
		        break;
		    case '3':
		        //'待确认订单';  未删除 and 已支付 and （商品退款状态[无，已拒绝] or （券的退款状态为[无，拒绝退款] and 使用状态为未使用)) and 发货状态[全部发货、无需发货] and 未结单
		        /* $join=" left join " . DB_PREFIX . "deal_coupon as dc on do.id=dc.order_id ";
		        $condition = ' and do.is_delete = 0 
                                and do.pay_status = 2 
                                and (d.refund_status in (0,3) or (dc.refund_status in (0,3) and dc.confirm_time=0)) 
                                and ( do.delivery_status =2  or do.delivery_status = 5)  and do.order_status = 0 '; */
		        $condition .= " and do.order_process_status in(2,3) ";
		        break;
		    case '4':
		        //'待评价订单';
		        //$condition = ' and do.is_delete = 0 and do.pay_status = 2 and do.order_status = 1 and (d.is_arrival = 1 or d.consume_count>0 ) and d.dp_id =0 ';
		        $condition .= " and do.order_process_status = 4 ";
		        break;
		}
		$condition .= ' and do.type != 5 and do.type!=7  ';
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
		
		foreach($list as $k=>$v)
		{
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
		$GLOBALS['tmpl']->assign("page_title","我的订单");
		assign_uc_nav_list();
		$GLOBALS['tmpl']->display("uc/uc_order_index.html");
	}
	
	/**
	 * 快递查询
	 */
	public function check_delivery()
	{
		global_run();
		$id = intval($_REQUEST['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$order_table_name = get_user_order_table_name($user_id);
		
		$delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".$order_table_name." as o on n.order_id = o.id where n.order_item_id = ".$id." and o.user_id = ".$user_id." order by delivery_time desc");
        
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
				}elseif (app_conf("KUAIDI_TYPE")==3){
				    if($typeCom && $typeNu){
				        $url = "http://www.kuaidi100.com/applyurl?key=".$AppKey."&com=".$typeCom."&nu=".$typeNu;
				        $api_url = trim(file_get_contents($url));
				        $html = '<iframe name="kuaidi100" src="'.$api_url.'" width="600" height="380" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>';
				        $data['html'] = $html;
				        $data['status'] = true;   //API查询
				        $data['time'] = NOW_TIME;
				        ajax_return($data);
				    }else{
				        $data['status'] = false;
				        $data['info'] = "无效的快递查询";
				        ajax_return($data);
				    }
				    
				}else{
					$url = "http://www.kuaidi100.com/chaxun?com=".$typeCom."&nu=".$typeNu;
					app_redirect($url);
				}
			
			}else{
				if(app_conf("KUAIDI_TYPE")==1)
				{
					$data['status'] = false;
					$data['info'] = "无效的快递查询";
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
	
	/**
	 * 确认无需收货的商品收货
	 */
	public function verify_no_delivery()
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
			$user_id = intval($GLOBALS['user_info']['id']);
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_table_name = get_user_order_table_name($user_id);
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$id." and is_arrival <> 1 and delivery_status=1");
			
			if($data && $data['is_delivery']==0){
			    $res=confirm_no_delivery($id); 
    			
    			if($res)
    			{
    			    $data['status'] = true;
    			    $data['dp_url'] = url("index","review",array("order_item_id"=>$id));
    			    ajax_return($data);
    			}
    			else
    			{
    			    $data['status'] = 0;
    			    $data['info'] = "收货失败";
    			    ajax_return($data);
    			}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "非法操作";
				ajax_return($data);
			}
		}
	}
	
	
	/**
	 * 确认物流商品收货
	 **/
	public function verify_delivery()
	{
	    global_run();
	    if(check_save_login()!=LOGIN_STATUS_LOGINED)
	    {
	        $data['status'] = 1000;
	        ajax_return($data);
	    }
	    else{
	        require_once(APP_ROOT_PATH."system/model/deal_order.php");
	        $notice_id=$_REQUEST['notice_id'];
	        
	        $delivery_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "delivery_notice where id=".$notice_id);
	       
	        if($delivery_notice){
	            $res = order_confirm_delivery($delivery_notice['notice_sn'],$delivery_notice['express_id'], $delivery_notice['order_id']);
	            
	            if($res)
	            {
	                $data['status'] = 1;
	                $data['info'] = "收货成功";
	                ajax_return($data);
	            }
	            else
	            {
	                $data['status'] = 0;
	                $data['info'] = "收货失败";
	                ajax_return($data);
	            }
	        }
	        else
	        {
	            $data['status'] = 0;
	            $data['info'] = "非法操作";
	            ajax_return($data);
	        }
	    }
	}
	
	public function refuse_delivery()
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
			$user_id = intval($GLOBALS['user_info']['id']);
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_table_name = get_user_order_table_name($user_id);
			$content = strim($_REQUEST['content']);

			if($content=="")
			{
				$data['status'] = 0;
				$data['info'] = "请输入具体说明";
				ajax_return($data);
			}
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$id." and is_arrival=0 and delivery_status=1");
				
			if($data){
    			$delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".$order_table_name." as o on n.order_id = o.id where n.order_item_id = ".$id." and o.user_id = ".$user_id." and is_arrival = 0 order by delivery_time desc");
    			if($delivery_notice && $data['is_delivery']==1)
    			{
    				$res = refuse_delivery($delivery_notice['notice_sn'],$id);
    			}else{
    			    $res=refuse_no_delivery($id);
    			}
    			if($res)
    			{
    			    	
    			    $msg = array();
    			    $msg['rel_table'] = "deal_order";
    			    $msg['rel_id'] = $delivery_notice['order_id'];
    			    $msg['title'] = "订单维权";
    			    $msg['content'] = "订单维权：".$content;
    			    $msg['create_time'] = NOW_TIME;
    			    $msg['user_id'] = $GLOBALS['user_info']['id'];
    			    $GLOBALS['db']->autoExecute(DB_PREFIX."message",$msg);
    			    	
    			    $data['status'] = true;
    			    $data['info'] = "维权提交成功";
    			    ajax_return($data);
    			}
    			else
    			{
    			    $data['status'] = 0;
    			    $data['info'] = "维权提交失败";
    			    ajax_return($data);
    			}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "订单未发货";
				ajax_return($data);
			}
		}
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
			if($order_info)
			{
			    if(($order_info['is_delete'] && $order_info['is_cancel']) || $order_info['order_status']==1){
			        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_delete = 1,is_cancel = 0 where (order_status = 1 or pay_status = 0) and ( ( is_delete = 1 and is_cancel=1 ) or is_delete = 0)  and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
			        if($GLOBALS['db']->affected_rows())
			        {
			            
    			        $data['status'] = 1;
    			        $data['info'] = "订单删除成功";
    			        ajax_return($data);
			        }
			        else{
			            $data['status'] = 0;
			            $data['info'] = "订单删除失败";
			            ajax_return($data);
			        }
			    }
			    else{
			        
			        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_delete = 1,is_cancel = 1 where pay_status = 0 and is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
			        
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
			                    modify_account(array("money"=>$order_info['account_money']), $order_info['user_id'],"取消订单，退回余额支付 ");
			                    order_log("用户取消订单，退回余额支付 ".$order_info['account_money']." 元", $order_info['id']);
			                }
			                if($order_info['ecv_id'])
			                {
			                    $GLOBALS['db']->query("update ".DB_PREFIX."ecv set use_count = use_count - 1 where id = ".$order_info['ecv_id']);
			                    order_log("用户取消订单，代金券退回 ", $order_info['id']);
			                }
			        
			            }
						if($order_info['pay_status'] == 0 && $order_info['exchange_money'] > 0){
							$GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set exchange_money = 0,total_price=total_price+".$order_info['exchange_money']." where id = " . $order_info['id']);
							$score_purchase=unserialize($order_info['score_purchase']);
							modify_account(array("score" =>$score_purchase['user_use_score'] ), $order_info['user_id'], "取消订单，退回积分抵扣 ");
							order_log("用户取消订单，积分抵扣退回 ", $order_info['id']);
						}
			            over_order($order_info['id']);
			            $data['status'] = 1;
			            $data['info'] = "订单取消成功";
			            ajax_return($data);
			        }
			        else
			        {
			            $data['status'] = 0;
			            $data['info'] = "订单取消失败";
			            ajax_return($data);
			        }
			    }
				
			}
			else
			{
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
		
		$GLOBALS['tmpl']->assign("page_title","我的订单");
		assign_uc_nav_list();
		
		$id = intval($_REQUEST['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where (is_delete = 0 or (is_delete=1 and is_cancel=1)) and user_id = ".$GLOBALS['user_info']['id']." and id = ".$id);
		
		if($order_info)
		{
			if($order_info['deal_order_item'])
			{
				$order_info['deal_order_item'] = unserialize($order_info['deal_order_item']);
			}
			else
			{
				update_order_cache($order_info['id']);
				$order_info['deal_order_item'] = $GLOBALS['db']->getAll("select doi.*,d.is_shop from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id=d.id where doi.order_id = ".$order_info['id']);
			}
			
			if($order_info['type']==4){
			    $order_info["order_type"]="post";
			}
			elseif ($order_info['type']<>1){
			    $order_info["order_type"]="delivery";
			}
			
			$order_info['create_time'] = to_date($order_info['create_time']);
			$order_info['deal_total_price']=$order_info['deal_total_price']-$order_info['discount_price'];
			
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
			
			//获取驿站信息
			if($order_info['type']==4){
			    if($order_info['distribution_id']){
			        $dist_info=$GLOBALS['db']->getRow("select tel,address from ".DB_PREFIX."distribution where id=".$order_info['distribution_id']);
			        $GLOBALS['tmpl']->assign("dist_info",$dist_info);
			    }
			    $dist_coupon=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution_coupon where order_id=".$order_info['id']);
			    
			    if($dist_coupon){
    			    if($dist_coupon['confirm_time']>0){
    			        $dist_coupon['status']="已使用";
    			    }else{
    			        $item_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where order_id=".$order_info['id']." and delivery_status=1 and is_arrival=0 and refund_status in (0,3)");
    			        
    			        if($item_count>0){
    			            $dist_coupon['status']="有效";
    			        }else {
    			            $apply_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."order_item where order_id=".$order_info['id']." and delivery_status=1 and is_arrival=0 and refund_status=1");
    			            if($apply_count>0){
    			                $dist_coupon['status']="有效";
    			            }else{
    			                $dist_coupon['status']="已退款";
    			            }
    			        }
    			    }
			    }
			    
			    $GLOBALS['tmpl']->assign("dist_coupon",$dist_coupon);
			}
			
			//配送地址
			if($order_info['delivery_status']<>5 && $order_info['consignee']){
    			$region_lv1 = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv1']);
    			$region_lv2 = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv2']);
    			$region_lv3 = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv3']);
    			$region_lv4 = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv4']);
    			
    			$order_info['address_info']=$order_info['consignee'].", ".$order_info['mobile'].", ".
        			$region_lv2['name'].$region_lv3['name'].$region_lv4['name'].
    			    $order_info['street'].$order_info['address'].$order_info['doorplate'];
    			if($order_info['zip']){
    			    $order_info['address_info'] .= ", ".$order_info['zip'];
    			}
			}
			$order_info['c'] = count($order_info['deal_order_item']);
			
			//包裹快递
			$notice_sql="select n.*,e.name from ".DB_PREFIX."delivery_notice as n left join ".DB_PREFIX."express as e on e.id=n.express_id where n.order_id=".$order_info['id']." group by n.notice_sn,n.express_id";
			$notice=$GLOBALS['db']->getAll($notice_sql);
			
			if($notice){
			    foreach ($notice as $t => $v){
			        $notice[$t]['deal_ids']=array();
			        $deal_id=$GLOBALS['db']->getAll("select deal_id from ".DB_PREFIX."delivery_notice where notice_sn='".$v['notice_sn']."' and express_id=".$v['express_id']);
			        foreach ($deal_id as $tt => $vv){
			            $notice[$t]['deal_ids'][]=$vv["deal_id"];
			        }
			    }
			}
			
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
				
			    if($order_info["delivery_status"]==5 && $vv["is_shop"]==1){
			        $order_info['deal_order_item'][$kk]['coupon_sn'] = $GLOBALS['db']->getOne("select dc.password from ".DB_PREFIX."deal_coupon as dc where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.order_deal_id = ".$vv['id']);
			        $order_info["order_type"]="pick";
			    }
			    if($order_info["delivery_status"]==5 && $vv["is_shop"]==0){
			        $tuan_coupon = $GLOBALS['db']->getAll("select dc.*,m.content from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."message as m on dc.message_id = m.id where dc.is_valid > 0 and dc.user_id = ".$GLOBALS['user_info']['id']." and dc.order_deal_id = ".$vv['id']);
			        $order_info["order_type"]="tuan";
			    }
			    if($order_info["delivery_status"]<>5 && $vv["is_delivery"]==0){
			        $order_info["order_type"]="no_delivery";
			    }
			    
			    //按包裹归类商品
			    foreach ($notice as $t => $v){
			        $notice[$t]['number']=intval($t)+1;
			        if(in_array($vv['deal_id'], $v['deal_ids'])){
			            $notice[$t]['order_item'][]=$order_info['deal_order_item'][$kk];
			            if($vv['is_arrival']<>1 && ($vv['refund_status']==0 || $vv['refund_status']==3)){
			                $notice[$t]['unconfirm']=1;
			            }
			            unset($order_info['deal_order_item'][$kk]);
			        }
			    }
			}
			
			if($order_info["order_type"]=="pick")
			{
			    $location=$GLOBALS['db']->getRow("select name,address from ".DB_PREFIX."supplier_location where id=".$order_info['location_id']);
			    $order_info['location_name']=$location['name'];
			    $order_info['location_address']=$location['address'];
			}
			
			if($order_info['cod_money']>0){
				
				$order_info['payment_info']=$GLOBALS['db']->getRow("select pn.id,pn.money,pn.payment_config,p.class_name,p.name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id=p.id where order_id = ".$order_info['id']." and p.class_name='Cod' and pn.is_paid=1");
				if($order_info['payment_info']){
					$rel=get_payment_name_rel($order_info['cod_mode']);
					$order_info['payment_info']['name']=$order_info['payment_info']['name'].$rel;
				}else{
					$order_info['payment_info']['name']="货到付款(现金)";
					$order_info['payment_info']['money']=$order_info['cod_money'];
				}
				$order_info['payment_info']['money']=format_price($order_info['payment_info']['money']);
			}

			if ($order_info['invoice_info']) {
				$order_info['invoice_info'] = unserialize($order_info['invoice_info']);
			}
			
			//团购券状态处理
			if($tuan_coupon){
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
			
			$GLOBALS['tmpl']->assign("notice",$notice);
			/*			
			//输出收款单日志
			$payment_list_res = load_auto_cache("cache_payment");
			foreach($payment_list_res as $k=>$v)
			{
				$payment_list[$v['id']] = $v;
			}
			$payment_notice_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_info['id']." and is_paid = 1 order by create_time desc");
			foreach($payment_notice_list as $k=>$v)
			{
				$payment_notice_list[$k]['payment'] = $payment_list[$v['payment_id']];
			}
			$GLOBALS['tmpl']->assign("payment_notice_list",$payment_notice_list); 
			*/
			
			//订单日志
			$order_logs = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_log where order_id = ".$order_info['id']." order by id desc");
			foreach ($order_logs as $kk => $vv){
			    $order_logs[$kk]['log_time']=to_date($vv['log_time']);
			}
			$GLOBALS['tmpl']->assign("order_logs",$order_logs);
			
			$GLOBALS['tmpl']->assign("NOW_TIME",NOW_TIME);
			$GLOBALS['tmpl']->display("uc/uc_order_view.html");
		}
		else
		{
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
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		else
		{
			$did = intval($_REQUEST['did']);
			$cid = intval($_REQUEST['cid']);
			$cids =  $_REQUEST['cids'];
 			
			if($did)
			{
				//退单
				$deal_order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$did);		
				$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = '".$deal_order_item['order_id']."' and order_status = 0 and user_id = ".$GLOBALS['user_info']['id']);
				
				if($order_info)
				{										
					if(($deal_order_item['delivery_status']==0 || ($deal_order_item['delivery_status']==1 && $deal_order_item['is_arrival']!=1) )&&$order_info['pay_status']==2&&$deal_order_item['is_refund']==1)
					{
						if($deal_order_item['refund_status']==0)
						{
							$data['status'] = true;
							$GLOBALS['tmpl']->assign("did",$did);
							$data['html'] = $GLOBALS['tmpl']->fetch("inc/refund_form.html");
							ajax_return($data);
						}
						else
						{
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					}
					else
					{
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "非法操作";
					ajax_return($data);
				}
			}
			elseif($cid)
			{
				//退券
				$coupon = $GLOBALS['db']->getRow("select dc.*,d.is_shop,doi.is_refund from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."deal as d on dc.deal_id=d.id left join ".DB_PREFIX."deal_order_item as doi on doi.id = dc.order_deal_id where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.id = ".$cid);
				
				if($coupon)
				{
					if($coupon['refund_status']==0&&$coupon['confirm_time']==0)//从未退过款可以退款，且未使用过
					{
						if(($coupon['is_shop']==1 && $coupon['is_refund']==1)||
						    ($coupon['any_refund']==1 && ($coupon['end_time']==0 || $coupon['end_time']>NOW_TIME))||
						    ($coupon['expire_refund']==1&&$coupon['end_time']>0&&$coupon['end_time']<NOW_TIME))//随时退或过期退已过期
						{
							$data['status'] = true;
							$GLOBALS['tmpl']->assign("cid",$cid);
							$data['html'] = $GLOBALS['tmpl']->fetch("inc/refund_form.html");
							ajax_return($data);
						}
						else
						{
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					}
					else
					{
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "非法操作";
					ajax_return($data);
				}
			}
			else
			{
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
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$data['status'] = 1000;
			ajax_return($data);
		}
		else
		{
			$did = intval($_REQUEST['did']);
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
			
			if($did)
			{
				//退单
				$deal_order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$did);
				$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = '".$deal_order_item['order_id']."' and order_status = 0 and user_id = ".$GLOBALS['user_info']['id']);
				if($order_info)
				{							
					if(($deal_order_item['delivery_status']==0 || ($deal_order_item['delivery_status']==1 && $deal_order_item['is_arrival']!=1) )&&$order_info['pay_status']==2&&$deal_order_item['is_refund']==1)
					{
						if($deal_order_item['refund_status']==0)
						{
							//执行退单,标记：deal_order_item表与deal_order表，
							$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set refund_status = 1 where id = ".$deal_order_item['id']);
							$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 1 where id = ".$deal_order_item['order_id']);
							
							$msg = array();
							$msg['rel_table'] = "deal_order";
							$msg['rel_id'] = $deal_order_item['order_id'];
							$msg['title'] = "退款申请";
							$msg['content'] = "退款申请：".$content;
							$msg['create_time'] = NOW_TIME;
							$msg['user_id'] = $GLOBALS['user_info']['id'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."message",$msg);
							$message_id = intval($GLOBALS['db']->insert_id());
							update_order_cache($deal_order_item['order_id']);
							if($message_id){
							    if ($did) {
							        $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set message_id = " . $message_id . " where id = " .$did);
							    }
							}
							order_log($deal_order_item['sub_name']."申请退款，等待审核", $deal_order_item['order_id']);
							
							require_once(APP_ROOT_PATH."system/model/deal_order.php");
							distribute_order($order_info['id']);
							
							$data['status'] = true;
							$data['info'] = "退款申请已提交，请等待审核";
							ajax_return($data);
						}
						else
						{
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					}
					else
					{
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "非法操作";
					ajax_return($data);
				}
			}
			elseif($cid)
			{
				//退券
				$coupon = $GLOBALS['db']->getRow("select dc.*,d.is_shop,doi.is_refund from ".DB_PREFIX."deal_coupon as dc left join ".DB_PREFIX."deal as d on dc.deal_id=d.id left join ".DB_PREFIX."deal_order_item as doi on doi.id = dc.order_deal_id where dc.user_id = ".$GLOBALS['user_info']['id']." and dc.id = ".$cid);
				if($coupon)
				{
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
						}
						else
						{
							$data['status'] = 0;
							$data['info'] = "不允许退款";
							ajax_return($data);
						}
					}
					else
					{
						$data['status'] = 0;
						$data['info'] = "非法操作";
						ajax_return($data);
					}
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "非法操作";
					ajax_return($data);
				}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "非法操作";
				ajax_return($data);
			}
				
		}
	}
	
	/**
	 * 整笔订单的状态
	 * @param unknown $order_item 订单中的所有商品，二维数据
	 */
	public function order_status($order_info) {
	    $handle=array(
	        0=>array(
	            "status"=>"order_view",
	            "info"=>"查看订单",
	            "url"=>url("index","uc_order#view",array("id"=>$order_info['id'])),
	        ),
	        1=>array(
	            "status"=>"go_pay",
	            "info"=>"继续支付",
	            "url"=>url("index","cart#order",array("id"=>$order_info['id'])),
	        ),
	        2=>array(
	            "status"=>"cancel_order",
	            "info"=>"取消订单",
	            "action"=>url("index","uc_order#cancel",array("id"=>$order_info['id'])),
	            "class"=>"cancel_order",   
	        ),
	        3=>array(
	            "status"=>"del_order",
	            "info"=>"删除订单",
	            "action"=>url("index","uc_order#cancel",array("id"=>$order_info['id'])),
	            "class"=>"del_order",
	        ),
	        4=>array(
	            "status"=>"delivery_view",
	            "info"=>"查看物流",
	            "url"=>url("index","uc_order#view",array("id"=>$order_info['id'])),
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
 
        if($order_info['delivery_status']==5){
            //团购自提商品待结单订单
            $status=array(
	            "handle"=>array(
	                $handle[0],
	            ),
	        );
	        return $status;
        }
        else{
            $item = unserialize($order_info['deal_order_item']);
            $is_delivery=0;
            foreach ($item as $t => $v){
                if($v['is_delivery']){
                    $is_delivery=1;
                }
                break;
            }
            
            if($is_delivery==1 && $order_info['delivery_status']<>0 && $order_info['type']<>4){
                $status=array(
                    "handle"=>array(
                        $handle[0],
                        $handle[4],
                    ),
                );
                return $status;
            }
            
	        $status=array(
	            "handle"=>array(
	                $handle[0],
	            ),
	        );
	        return $status;
        }
	    
	}
	
	/**
     * 获取订单中单个商品的状态
     * $order_item_info 订单中单个商品，一维数据
     * $type  订单类型(0:商品订单 1:用户充值单,2:积分兑换订单，3:平台自营物流配送订单，4:平台自营驿站配送订单)
     */
	public function order_item_status($order_item_info,$order_status=0) {
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
	        "check_delivery"=>array(
	            "info"=>"查看物流",
	            "url"=>url("index","uc_order#check_delivery",array("id"=>$order_item_info['id'])),
	        ),
	        "confirm_no_delivery"=>array(
	            "info"=>"确认到货",
	            "action"=>url("index","uc_order#verify_no_delivery",array("id"=>$order_item_info['id'])),
	            "class"=>"verify_delivery",
	        ),
	        "evaluate"=>array(
	            "info"=>"评价",
	            "url"=>url("index","review",array("order_item_id"=>$order_item_info['id'])),
	        ),
	        "tuan_coupon"=>array(
	            "info"=>"查看消费券",
	            "url"=>url("index","uc_coupon",array("did"=>$order_item_info['id'])),
	        ),
	        "pick_coupon"=>array(
	            "info"=>"查看自提券",
	            "url"=>url("index","uc_coupon",array("did"=>$order_item_info['id'])),
	        ),
	        "post_coupon"=>array(
	            "info"=>"查看配送码",
	            "url"=>"",
	        ),
	    );
	    
        if ($order_item_info['delivery_status']==5){
            if($order_item_info['is_shop']==1){
                //自提
                if($order_item_info['refund_status']==2){
                    $status=array(
                        "status"=>"已退款"
                    );
                    return $status;
                }
                if($order_item_info['consume_count']==0){
                    $status=array(
                        "status"=>"待自提"
                    );
                    if($order_item_info['is_refund']==1 && $order_item_info['refund_status']==0){
                        $status['handle']=array(
                            $handle['coupon_refund']
                        );
                    }
                    if($order_item_info['refund_status']==1){
                       $status['handle']=array();
                        $status['vaice_status']="退款中";
                    }
                    else if($order_item_info['refund_status']==3){
                        $status['handle']=array();
                        $status['vaice_status']="拒绝退款";
                    }
                    return $status;
                }
                
                if($order_item_info['consume_count']>0 && $order_item_info['dp_id']==0){
                    $status=array(
                        "status"=> "评价",
                        "url"   => url("index","review",array("order_item_id"=>$order_item_info['id'])),
                    );
                    return $status;
                }
                if($order_item_info['dp_id']>0){
                    $status=array(
                        "status"=>"已评价"
                    );
                    return $status;
                }
            }else {
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
        }else {
	        if($order_item_info['refund_status']==2){
	            $status=array(
                    "status"=>"已退款"
                );
                return $status;
	        }
	        if($order_item_info['delivery_status']==0){
	            $status=array(
	                "status"=>"待发货"
	            );
                if($order_item_info['is_refund']==1 && $order_item_info['refund_status']==0){
                    $status['handle']=array(
                        $handle['deal_refund'],
                    );
                }
                
                if($order_item_info['refund_status']==1){
                    $status['handle']=array();
                    $status['vaice_status']="退款中";
                }
                if($order_item_info['refund_status']==3){
                    $status['vaice_status']="拒绝退款";
                }
                
                return $status;   
	        }
	        if($order_item_info['delivery_status']==1 && $order_item_info['is_arrival']!=1){
	            $status=array(
	                "status"=>"待收货",
	            );
	            if($order_item_info["is_delivery"]==0){
	                $status["handle"]=array(
	                    $handle['confirm_no_delivery'],
	                );
	            }
	            if($order_item_info['is_refund']==1 && $order_item_info['refund_status']==0){
	                $status['handle'][]=$handle['deal_refund'];
	            }
	            if($order_item_info['refund_status']==1){
	                $status['handle']=array();
	                $status['vaice_status']="退款中";
	            }
	            else if($order_item_info['refund_status']==3){
	                if($order_item_info["is_delivery"]==0){
    	                $status['handle']=array(
    	                    $handle['confirm_no_delivery'],
    	                );
	                }else{
	                    $status['handle']=array();
	                }
	                $status['vaice_status']="拒绝退款";
	            }
	            return $status;  
	        }
	        if($order_item_info['delivery_status']==1 && $order_item_info['is_arrival']==1 && $order_item_info['dp_id']==0){
	            $status=array(
	                "status"=>"评价",
                    "url"   => url("index","review",array("order_item_id"=>$order_item_info['id'])),
	            );
	            if($order_item_info['refund_status']==3){
	                $status['vaice_status']="拒绝退款";
	            }
	            return $status;

	        }
	        $status=array(
	            "status"=>"已评价"
	        );
	        return $status;
        }
	    
	}
}
?>