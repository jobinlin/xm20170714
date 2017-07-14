<?php
/**
 * @desc
 * @since     2017-01-13 11:06  
 */
class biz_refund_orderApiModule extends MainBaseApiModule
{
    /* 
	* @desc    退款维权
    * 	 输入:  
	*	 p int 页数
	* 	 输出:
	*	Array
	*	(
	*		[is_auth] => 1  有权限：1 无权限：0
	*		[list] => Array       列表
	*			(
	*				[0] => Array
	*					(
	*						[id] => 289			    	//订单商品id
	*						[deal_id] => 257       	 	//商品id
	*						[number] => 4				//当前数量
	*						[unit_price] => 148.0000    //单价
	*						[total_price] => 473.6000   //总价
	*						[delivery_status] => 5
	*						[name] => 仅售83元！价值89元，碧海蓝天下，欢乐无限量！无需加价！时令海鲜、鲜美肉类、四时果蔬、招牌特饮、精致小点，同时引入海盗船长主题、时尚欢乐舞蹈！
	*						[return_score] => 0
	*						[return_total_score] => 0
	*						[attr] => 
	*						[verify_code] => b719334e2e452a069f651b8b24e6c921
	*						[order_sn] => 2017011410302541   //订单号
	*						[order_id] => 185				 //订单id
	*						[return_money] => 0.0000
	*						[return_total_money] => 0.0000
	*						[buy_type] => 0
	*						[sub_name] => 【4店通用】蓝海一家时尚自助餐厅
	*						[attr_str] => 					//规格
	*						[is_balance] => 0
	*						[balance_unit_price] => 148.0000
	*						[balance_memo] => 
	*						[balance_total_price] => 592.0000
	*						[balance_time] => 0
	*						[add_balance_price] => 0.0000
	*						[add_balance_price_total] => 0.0000
	*						[refund_status] => 3
	*						[dp_id] => 0
	*						[is_arrival] => 0
	*						[is_coupon] => 1
	*						[deal_icon] => http://localhost/o2onew/public/attachment/201610/09/15/57f9f03048e09.jpg
	*						[location_id] => 0
	*						[supplier_id] => 22
	*						[is_refund] => 0			//是否允许退款
	*						[user_id] => 73
	*						[is_shop] => 0
	*						[consume_count] => 0
	*						[is_pick] => 0
	*						[fx_user_id] => 0
	*						[fx_salary] => 0.0000
	*						[fx_salary_total] => 0.0000
	*						[fx_salary_all] => 
	*						[is_delivery] => 0
	*						[message_id] => 105
	*						[memo] => 
	*						[create_time] => 1484332225
	*						[pay_amount] => 473.6000
	*						[status_info] => 已拒绝		//订单状态   退款申请，已退款，已拒绝
	*						[s_total_price] => 592		//结算价
	*					)
	*			)
	*
	*		[page] => Array			分页
	*			(
	*				[page] => 1
	*				[page_total] => 1
	*				[page_size] => 10
	*				[data_total] => 3
	*			)
	*
	*		[biz_login_status] => 1    //商家登录状态    已登录：1   未登录：0
	*		[page_title] => 退款维权   //标题
	*		[ctl] => biz_refund_order
	*		[act] => index
	*		[status] => 1
	*		[info] => 
	*		[city_name] => 福州
	*		[return] => 1
	*		[sess_id] => j2184jngnmn2ht91lp8tqnos16
	*		[ref_uid] => 
	*	)
	*/
	public function index()
    {
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = intval($account_info['supplier_id']);
        //判断是否登录
        if(!$account_info){
            $root['biz_login_status']=0;
            return output($root,0,"用户未登录");
        }else{
            $biz_login_status=1;
			//返回商户权限
			if(!check_module_auth("goodso")||!check_module_auth("dealo")){
				$root['is_auth'] = 0;
				return output($root,0,"没有操作权限");
			}else{
				$root['is_auth'] = 1;
			}
			//分页
			$page_size = 10;
			$page = intval($GLOBALS['request']['page']);
			if($page==0) $page = 1;
			$limit = (($page-1)*$page_size).",".$page_size;
			$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
			$order_table_name = get_supplier_order_table_name($supplier_id);
			$sql = "select distinct(doi.id),dc.message_id as msg_id,doi.*,do.memo,do.create_time,do.order_sn,do.total_price,do.pay_amount,doi.refund_status from ".$order_item_table_name." as doi 
				left join ".$order_table_name." as do on doi.order_id = do.id 
				LEFT JOIN ".DB_PREFIX."deal_coupon AS dc ON doi.id = dc.order_deal_id 
				left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".	    		
	    		" where l.location_id in (".implode(",",$account_info['location_ids']).") and do.is_delete = 0 and (do.type = 5 or do.type = 6) and do.pay_status = 2 and (( doi.refund_status > 0 and doi.is_coupon=0) or (dc.refund_status <> 0 and doi.is_coupon=1)) order by ".
				"(( doi.refund_status = 1 AND doi.is_coupon = 0 ) OR ( dc.refund_status = 1 AND doi.is_coupon = 1 )) DESC,doi.id DESC limit ".$limit;
			//echo $sql;exit;
			$sql_count = "select count(*) from (select DISTINCT (doi.id),dc.message_id AS msg_id from ".$order_item_table_name." as doi 
				left join ".$order_table_name." as do on doi.order_id = do.id 
				LEFT JOIN ".DB_PREFIX."deal_coupon AS dc ON doi.id = dc.order_deal_id 
				left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".
	    		" where l.location_id in (".implode(",",$account_info['location_ids']).") and do.is_delete = 0 and (do.type = 5 or do.type = 6) and do.pay_status = 2 and (( doi.refund_status > 0 and doi.is_coupon=0) or (dc.refund_status <> 0 and doi.is_coupon=1))) aa";
			$total = $GLOBALS['db']->getOne($sql_count);
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				$list[$k]['deal_icon']=get_abs_img_root($v['deal_icon']);
				$refund_status_1=0;
				$refund_status_2=0;
				$refund_status_3=0;
				if($v['is_coupon']==1){
					$refund_coupon=$GLOBALS['db']->getAll("select id,refund_status,deal_type from ".DB_PREFIX."deal_coupon 
					where order_deal_id = ".$v['id']." and refund_status>0 and message_id=".$v['msg_id']);
					$refund_status_1_0_list=array();
					$refund_status_2_0_list=array();
					$refund_status_3_0_list=array();
					$refund_status_1_1_list=array();
					$refund_status_2_1_list=array();
					$refund_status_3_1_list=array();
					foreach($refund_coupon as $kk=>$vv){
						if($vv['deal_type']==0){
							if($vv['refund_status']==1){
								$refund_status_1_0_list[]=$vv;
							}elseif($vv['refund_status']==2){
								$refund_status_2_0_list[]=$vv;
							}elseif($vv['refund_status']==3){
								$refund_status_3_0_list[]=$vv;
							}
						}elseif($vv['deal_type']==1){
							if($vv['refund_status']==1){
								$refund_status_1_1_list[]=$vv;
							}elseif($vv['refund_status']==2){
								$refund_status_2_1_list[]=$vv;
							}elseif($vv['refund_status']==3){
								$refund_status_3_1_list[]=$vv;
							}
						}
					}
					$refund_status_1_0=count($refund_status_1_0_list);
					$refund_status_1_1=count($refund_status_1_1_list);
					$refund_status_2_0=count($refund_status_2_0_list);
					$refund_status_2_1=count($refund_status_2_1_list);
					$refund_status_3_0=count($refund_status_3_0_list);
					$refund_status_3_1=count($refund_status_3_1_list);
					
					
					$refund_status_1 = $refund_status_1_0 + $refund_status_1_1*$v['number'];
					$refund_status_2 = $refund_status_2_0 + $refund_status_2_1*$v['number'];
					$refund_status_3 = $refund_status_3_0 + $refund_status_3_1*$v['number'];
				}
				
				$list[$k]['is_refund']=0;
				if($v['is_coupon']==1){
					if($refund_status_1>0){
						$list[$k]['status_info']="退款申请";
						if($allow_refund==1){
							$list[$k]['is_refund']=1;
						}
						$list[$k]['number']=$refund_status_1;
					}elseif($refund_status_2>0){
						$list[$k]['status_info']="已退款";
						$list[$k]['number']=$refund_status_2;
					}elseif($refund_status_3>0){
						$list[$k]['status_info']="已拒绝";
						$list[$k]['number']=$refund_status_3;
					}
					$list[$k]['message_id']=$v['msg_id'];
				}else{
					if($v['refund_status']==1&&$refund_status_1==0){
						$list[$k]['status_info']="退款申请";
						if($allow_refund==1){
							$list[$k]['is_refund']=1;
						}
					}elseif($v['refund_status']==2&&$refund_status_2==0){
						$list[$k]['status_info']="已退款";
					}elseif($v['refund_status']==3&&$refund_status_3==0){
						$list[$k]['status_info']="已拒绝";
					}
					//$list[$k]['message_id'];
				}
				$message_info=$GLOBALS['db']->getRow("select create_time from ".DB_PREFIX."message 
						where id = ".$list[$k]['message_id']);
				$list[$k]['refund_time']=to_date($message_info['create_time'],'Y.m.d H:i');
				$list[$k]['s_total_price'] = $v['balance_total_price'] + $v['add_balance_price_total'];
			}
			$root['list']=$list;
			//$total
			//分页
			$page_total = ceil($total/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
			
        }
        $root['biz_login_status'] = $biz_login_status;
        $root['page_title'] = '退款维权';
        return output($root);

    }
	 /* 
	* @desc    退款详情
    * 	 输入:  
	*	data_id int 订单商品id
	*	msg_id int 退款消息id
	* 	 输出:
	*	Array
	*	(
	*		[is_auth] => 1                 		//有权限：1 无权限：0
	*		[item] => Array						//详情
	*			(
	*				[id] => 289					//订单商品id
	*				[deal_id] => 257			//商品id
	*				[number] => 4				//当前数量
	*				[unit_price] => 148.0000	//单价
	*				[total_price] => 473.6000	//总价
	*				[delivery_status] => 5
	*				[name] => 仅售83元！价值89元，碧海蓝天下，欢乐无限量！无需加价！时令海鲜、鲜美肉类、四时果蔬、招牌特饮、精致小点，同时引入海盗船长主题、时尚欢乐舞蹈！
	*				[return_score] => 0
	*				[return_total_score] => 0
	*				[attr] => 
	*				[verify_code] => b719334e2e452a069f651b8b24e6c921
	*				[order_sn] => 2017011410302541		//订单号
	*				[order_id] => 185					//订单id
	*				[return_money] => 0.0000
	*				[return_total_money] => 0.0000
	*				[buy_type] => 0
	*				[sub_name] => 【4店通用】蓝海一家时尚自助餐厅
	*				[attr_str] => 				//规格
	*				[is_balance] => 0
	*				[balance_unit_price] => 148.0000
	*				[balance_memo] => 
	*				[balance_total_price] => 592.0000
	*				[balance_time] => 0
	*				[add_balance_price] => 0.0000
	*				[add_balance_price_total] => 0.0000
	*				[refund_status] => 3
	*				[dp_id] => 0
	*				[is_arrival] => 0
	*				[is_coupon] => 1
	*				[deal_icon] => http://localhost/o2onew/public/attachment/201610/09/15/57f9f03048e09.jpg
	*				[location_id] => 0
	*				[supplier_id] => 22
	*				[is_refund] => 0
	*				[user_id] => 73
	*				[is_shop] => 0
	*				[consume_count] => 0
	*				[is_pick] => 0
	*				[fx_user_id] => 0
	*				[fx_salary] => 0.0000
	*				[fx_salary_total] => 0.0000
	*				[fx_salary_all] => 
	*				[is_delivery] => 0
	*				[message_id] => 105
	*				[memo] => 
	*				[create_time] => 1484332225
	*				[pay_amount] => 473.6000
	*				[status_info] => 已拒绝		//订单状态   退款申请，已退款，已拒绝
	*				[s_total_price] => 592      //结算价
					[message_info]       //退款原因
	*			)
	*
	*		[biz_login_status] =>1    //商家登录状态    已登录：1   未登录：0
	*		[page_title] => 退款详情  //标题
	*		[ctl] => biz_refund_order
	*		[act] => view
	*		[status] => 1
	*		[info] => 
	*		[city_name] => 福州
	*		[return] => 1
	*		[sess_id] => j2184jngnmn2ht91lp8tqnos16
	*		[ref_uid] => 
	*	)
	*/
	public function view()
    {
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = intval($account_info['supplier_id']);
        //判断是否登录
        if(!$account_info){
            $root['biz_login_status']=0;
            return output($root,0,"用户未登录");
        }else{
            $root['biz_login_status']=1;
			//返回商户权限
			if(!check_module_auth("goodso")&&!check_module_auth("dealo")){
				$root['is_auth'] = 0;
				return output($root,0,"没有操作权限");
			}else{
				$root['is_auth'] = 1;
			}
			$data_id = intval($GLOBALS['request']['data_id']);
			$msg_id = intval($GLOBALS['request']['msg_id']);
			$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
			$order_table_name = get_supplier_order_table_name($supplier_id);
			$sql = "select doi.*,do.memo,do.create_time,do.order_sn,do.total_price,do.pay_amount,doi.refund_status from ".$order_item_table_name." as doi left join ".
	 	    	$order_table_name." as do on doi.order_id = do.id 
				where doi.refund_status>0 and doi.id=".$data_id;
			//echo "ss";exit;
			$item = $GLOBALS['db']->getRow($sql);
			if(!$item){
				$root['is_null'] = 1;
				return output($root,0,"");
			}
			//echo "<pre>";print_r($item);exit;
			$item['deal_icon']=get_abs_img_root($item['deal_icon']);
			$item['is_refund']=0;
			if($item){
				$refund_status_1=0;
				$refund_status_2=0;
				$refund_status_3=0;
				if($item['is_coupon']==1){
					$refund_coupon=$GLOBALS['db']->getAll("select id,password,refund_status,deal_type,message_id from ".DB_PREFIX."deal_coupon 
					where order_deal_id = ".$item['id']." and refund_status>0 and message_id=".$msg_id);
					foreach($refund_coupon as $kk=>$vv){
						if($vv['deal_type']==0){
							if($vv['refund_status']==1){
								$refund_status_1_0_list[]=$vv;
							}elseif($vv['refund_status']==2){
								$refund_status_2_0_list[]=$vv;
							}elseif($vv['refund_status']==3){
								$refund_status_3_0_list[]=$vv;
							}
						}elseif($vv['deal_type']==1){
							if($vv['refund_status']==1){
								$refund_status_1_1_list[]=$vv;
							}elseif($vv['refund_status']==2){
								$refund_status_2_1_list[]=$vv;
							}elseif($vv['refund_status']==3){
								$refund_status_3_1_list[]=$vv;
							}
						}
					}
					$refund_status_1_0=count($refund_status_1_0_list);
					$refund_status_1_1=count($refund_status_1_1_list);
					$refund_status_2_0=count($refund_status_2_0_list);
					$refund_status_2_1=count($refund_status_2_1_list);
					$refund_status_3_0=count($refund_status_3_0_list);
					$refund_status_3_1=count($refund_status_3_1_list);
					
					$refund_status_1 = $refund_status_1_0 + $refund_status_1_1*$item['number'];
					$refund_status_2 = $refund_status_2_0 + $refund_status_2_1*$item['number'];
					$refund_status_3 = $refund_status_3_0 + $refund_status_3_1*$item['number'];
					if($refund_status_1>0){
						$item['status_info']="退款申请";
						if($allow_refund==1){
							$item['is_refund']=1;
						}
						$item['number']=$refund_status_1;
					}elseif($refund_status_2>0){
						$item['status_info']="已退款";
						$item['number']=$refund_status_2;
					}elseif($refund_status_3>0){
						$item['status_info']="已拒绝";
						$item['number']=$refund_status_3;
					}
				}else{
					if($item['refund_status']==1&&$refund_status_1==0){
						$item['status_info']="退款申请";
						if($allow_refund==1){
							$item['is_refund']=1;
						}
					}elseif($item['refund_status']==2&&$refund_status_2==0){
						$item['status_info']="已退款";
					}elseif($item['refund_status']==3&&$refund_status_3==0){
						$item['status_info']="已拒绝";
					}
				}
				$message_info=$GLOBALS['db']->getRow("select create_time,content from ".DB_PREFIX."message 
						where id = ".$msg_id);
				if(!$message_info){
					$root['is_null'] = 1;
					return output($root,0,"");
				}
				$item['refund_time']=to_date($message_info['create_time'],'Y.m.d H:i');
				$item['message_info']=$message_info['content'];
				$user_info=load_user($item['user_id']);
				$item['user_name']=$user_info['user_name'];
				$item['user_mobile']=$user_info['mobile'];
				$item['s_total_price'] = $item['balance_total_price'] + $item['add_balance_price_total'];
				
			}
			$root['item']=$item;
			
			
        }
		$root['msg_id']=$msg_id;
        $root['page_title'] = '退款详情';
        return output($root);

    }
	 /* 
	* @desc    退款
    * 	 输入:  
	* 	data_id int 	//订单商品id
	* 	msg_id int 		//退款消息id
	* 	 输出:
	*	status int    	//成功：1    失败：0
	*	biz_login_status int    //商家登录状态    已登录：1   未登录：0
	*	info  string 	//消息
	*/
	public function do_refund()
    {
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = intval($account_info['supplier_id']);
        //判断是否登录
        if(!$account_info){
            $root['biz_login_status']=0;
            return output($root,0,"用户未登录");
        }else{
			$root['biz_login_status'] = 1;
			$data_id = intval($GLOBALS['request']['data_id']);
			$msg_id = intval($GLOBALS['request']['msg_id']);
			if($data_id<=0&&$msg_id<=0)
			{
				return output($root,0,"数据错误");
			}
			$is_coupon=$GLOBALS['db']->getOne("select is_coupon from ".DB_PREFIX."deal_order_item where id = ".$data_id);
			if($is_coupon==1){
				if(!check_module_auth("dealo")&&!check_module_auth("goods"))
				{
					return output($root,0,"权限不足");
				}
			}else{
				if(!check_module_auth("goodso"))
				{
					return output($root,0,"权限不足");
				} 
			}
			  
            
			
			$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
			if(!$allow_refund)
			{
				return output($root,0,"权限不足");
			}
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
			$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where id = ".$data_id." and supplier_id = ".$supplier_id);
			if($order_item)
			{
				if($is_coupon==1){
					$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_deal_id = ".$order_item['id']." and message_id=".$msg_id);
					foreach($coupon_list as $c)
					{
						refund_coupon($c['id']);
					}
				}else{
					refund_item($order_item['id']);
				}
				return output($root,1,"退款操作成功");
			}
			else
			{
				return output($root,0,"非法的数据");
			}
        }
        
        return output($root); 

    }
	/* 
	* @desc    拒绝退款
    * 	 输入:  
	* 	data_id int 	//订单商品id
	* 	msg_id int 		//退款消息id
	* 	 输出:
	*	status int    	//成功：1    失败：0
	*	biz_login_status int    //商家登录状态    已登录：1   未登录：0
	*	info  string 	//消息
	*/
	public function do_refuse()
    {
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = intval($account_info['supplier_id']);
        //判断是否登录
        if(!$account_info){
            $root['biz_login_status']=0;
            return output($root,0,"用户未登录");
        }else{
			$root['biz_login_status'] = 1;
			$data_id = intval($GLOBALS['request']['data_id']);
			$msg_id = intval($GLOBALS['request']['msg_id']);
			$is_coupon=$GLOBALS['db']->getOne("select is_coupon from ".DB_PREFIX."deal_order_item where id = ".$data_id);
			if($is_coupon==1){
				if(!check_module_auth("dealo")&&!check_module_auth("goods"))
				{
					return output($root,0,"权限不足");
				}
			}else{
				if(!check_module_auth("goodso"))
				{
					return output($root,0,"权限不足");
					
				} 
			}
			  
            
			
			$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
			if(!$allow_refund)
			{
				return output($root,0,"权限不足");
			}
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
			
			$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where id = ".$data_id." and supplier_id = ".$supplier_id);
			if($order_item)
			{
				if($is_coupon==1){
					$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_deal_id = ".$order_item['id']." and message_id=".$msg_id);
					foreach($coupon_list as $c)
					{
						refuse_coupon($c['id']);
					}
				}else{
					refuse_item($order_item['id']);
				}
				return output($root,1,"拒绝退款操作成功");
			}
			else
			{
				return output($root,0,"非法的数据");
			}
        }
        return output($root); 

    }
}
