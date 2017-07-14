<?php
/**
 * @desc
 * @author    钱彩凡
 * @since     驿站订单列表、订单详情、发货、配送包裹
 */
class dist_orderApiModule extends MainBaseApiModule
{
    /**
     * 订单列表
     * 输入：
     * status： 0/null-全部，1-待发货，2-待配送
     * 
     * 输出：
     * [dist_user_status] => 1  登录状态
     * [order_status] => 0/null-全部，1-待发货，2-待配送
     * [count] => 10  订单数量
     * [count_1]=>5   待发货数量
     * [count_2]=>3   待配送数量
       [page] => Array  分页信息
        (
            [page] => 1
            [page_total] => 1
            [page_size] => 10
            [data_total] => 4
        )
       [order_list]=>array  订单信息
       (
            [0] => Array
                (
                    [order_id] => 184
                    [order_sn] => 2016112204551972  订单号
                    [order_item] => Array  订单中商品的信息
                        (
                            [0] => Array
                                (
                                    [id] => 310
                                    [number] => 1
                                    [unit_price] => 8.9000
                                    [name] => 仅售8.9元！价值39元的下曹吸盘收纳置物架1个，双吸盘设计，瓷砖.不锈钢等光滑平整表面都可放置，可挂浴室墙上，也可挂水槽上
                                    [return_score] => 0
                                    [deal_icon] => ./public/attachment/201502/26/11/54ee903778026.jpg
                                    [attr_str] => 1
                                    [status]=>array(
                                        [info]=>待发货
                                    )
                                )

                        )

                    [status] => array   订单状态
                    (
                        "info"=>"待发货"        订单状态
                        "handle"=>array(       可执行的操作
    	                    [0] =>array(
    	                       "info"=>"发货",     操作名
    	                       "action"=>wap_url("index","dist_order#delivery",array("data_id"=>$order_info['id'])),  执行操作的链接
    	                    )
    	                ),
                    )
                )
        )
     * 
     **/
    public function index() { 
        $root=array();
        
        $dist_info=$GLOBALS['dist_info'];
        
        $dist_id=$dist_info['id'];
        
        if (empty($dist_info)){
            $root['dist_user_status']=0;
            return output($root,0,"驿站未登录");
        }
        
        $root['dist_user_status']=1;
        
        $status=intval($GLOBALS['request']['status']);
        
        $root['order_status']=$status;
        
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : $page;
        
        $page_size = PAGE_SIZE;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $condition=" where do.type=4 and do.distribution_id=".$dist_id." and do.pay_status=2 ";
        
        $condition_1 = $condition." and do.delivery_status in (0,1) and do.order_status=0 and d.delivery_status=0 and d.refund_status in (0,3)";
        $condition_2 = $condition." and do.delivery_status = 2  and  do.order_status=0 and d.delivery_status=1 and d.refund_status<>2 ";
        
        $root['count_1']=$GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_1);
        $root['count_2']=$GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_2);
        
        switch ($status){
            case 1:
                $sql = "select do.*  from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_1." 
                GROUP BY do.id order by do.create_time desc limit ".$limit;                
                break;
            case 2:
                $sql = "select do.*  from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_2." 
                GROUP BY do.id order by do.create_time desc limit ".$limit;                
                break;
            default:
                $sql = "select do.*  from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition."
                GROUP BY do.id order by do.create_time desc limit ".$limit;
                break;
        }
        
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
                
        $list = $GLOBALS['db']->getAll($sql);
        
        $order_list=array();
        foreach($list as $t=>$v)
        {
            $new_list=array();
            $new_list['id']=$v['id'];
            $new_list['order_sn']=$v['order_sn'];
            $new_list['create_time']=to_date($v['create_time']);
            $new_list['status']=$this->order_status($v);
            
            if($v['deal_order_item'])
			{
				$item = unserialize($v['deal_order_item']);				
			}
			else
			{
				$order_id = $v['id'];
				update_order_cache($order_id);
				$item = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
			}
			$new_list['order_item_count']=count($item);
            foreach ($item as $tt => $vv){
                $new_item=array();
                $new_item['id']=$vv['id'];
                $new_item['return_score']=$vv['return_score'];
                $new_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 375, 375,1));
                $new_item['number']=$vv['number'];
                $new_item['name']=$vv['name'];
                $new_item['status']=$this->order_item_status($vv);
                $new_item['unit_price']=format_price($vv['unit_price']);
                $new_item['attr_str']=$vv['attr_str'];
                
                $new_list['order_item'][]=$new_item;
            }
            
            $order_list[]=$new_list;
        }
        
        $root['order']=$order_list?$order_list:array();
        
        if($status==1){
            $count=$root['count_1'];
        }elseif ($status==2){
            $count=$root['count_2'];
        }else {
            $count=$GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition);      
        }
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count); 
        $root['count'] = $count;
        
        $root['page_title'] = "订单中心";
        
        return $root;       
    }
    
    /**
     *退款订单列表
     *
     * 输出：
     * [dist_user_status] => 1  登录状态
     * [count] => 10  订单数量
       [page] => Array  分页信息
        (
            [page] => 1
            [page_total] => 1
            [page_size] => 10
            [data_total] => 4
        )
      [refund_list] => Array  订单中商品的信息
        (
            [0] => Array
                (
                    [id] => 310
                    [deal_id] => 86
                    [number] => 1
                    [unit_price] => 8.9000
                    [total_price] => 8.9000
                    [delivery_status] => 0
                    [name] => 仅售8.9元！价值39元的下曹吸盘收纳置物架1个，双吸盘设计，瓷砖.不锈钢等光滑平整表面都可放置，可挂浴室墙上，也可挂水槽上
                    [return_score] => 0
                    [return_total_score] => 0
                    [attr] => 
                    [verify_code] => ea35b59c135ad578bb43a0db40789000
                    [order_sn] => 2016112204551972
                    [order_id] => 184
                    [return_money] => 0.0000
                    [return_total_money] => 0.0000
                    [buy_type] => 0
                    [sub_name] => 下曹吸盘收纳置物架
                    [attr_str] => 
                    [is_balance] => 0
                    [balance_unit_price] => 8.0000
                    [balance_memo] => 
                    [balance_total_price] => 8.0000
                    [balance_time] => 0
                    [add_balance_price] => 0.0000
                    [add_balance_price_total] => 0.0000
                    [refund_status] => 0
                    [dp_id] => 0
                    [is_arrival] => 1
                    [is_coupon] => 0
                    [deal_icon] => ./public/attachment/201502/26/11/54ee903778026.jpg
                    [location_id] => 0
                    [supplier_id] => 23
                    [is_refund] => 1
                    [user_id] => 71
                    [is_shop] => 1
                    [consume_count] => 0
                    [is_pick] => 0
                    [fx_user_id] => 0
                    [fx_salary] => 0.0000
                    [fx_salary_total] => 0.0000
                    [fx_salary_all] => 
                    [message_id] => 0
                    [is_delivery] => 0
                    [status]=>退款中
                )

        )
     **/
    public function refund_order_list(){
        $root=array();
        
        $dist_info=$GLOBALS['dist_info'];
        
        $dist_id=$dist_info['id'];
        
        if (empty($dist_info)){
            $root['dist_user_status']=0;
            return output($root,0,"驿站未登录");
        }
        
        $root['dist_user_status']=1;
        
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : $page;
        
        $condition=" where do.type=4 and do.distribution_id=".$dist_id." and do.pay_status=2 ";
        
        $condition_1 = $condition." and do.delivery_status in (0,1) and do.order_status=0 and d.delivery_status=0 and d.refund_status in (0,3)";
        $condition_2 = $condition." and do.delivery_status = 2  and  do.order_status=0 and d.delivery_status=1 and d.refund_status<>2 ";
        
        $root['count_1']=$GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_1);
        $root['count_2']=$GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join  " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$condition_2);
        
        $page_size = PAGE_SIZE;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $sql="select doi.* from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on do.id=doi.order_id where doi.refund_status<>0 and do.distribution_id=".$dist_id." group by doi.id desc limit ".$limit;
        
        $sql_count="select count(doi.*) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on do.id=doi.order_id where doi.refund_status<>0 and do.distribution_id=".$dist_id;
        
        $refund_list=$GLOBALS['db']->getAll($sql);
        
        foreach ($refund_list as $t => $v){
            if($v['refund_status']==1)
            {
                $refund_list[$t]['status']="退款中";
            }else if ($v['refund_status']==2)
            {
                $refund_list[$t]['status']="已退款";
            }else if($v['refund_status']==3)
            {
                $refund_list[$t]['status']="拒绝退款";
            }
            $refund_list[$t]['unit_price']=format_price($v['unit_price']);
            $refund_list[$t]['deal_icon'] = get_abs_img_root(get_spec_image($v['deal_icon'], 375, 375,1));
        }

        $root['refund_list']=$refund_list?$refund_list:array();
        
        $count=$GLOBALS['db']->getOne($sql_count);
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        $root['count'] = $count;
        
        $root['page_title'] = "订单中心";
        
        return $root;
    }
    
    
    /**
     * 订单详情
     * 输入：
     * data_id:订单id
     * 
     * 输出：
     *
     **/
    public function view() {
        $root=array();
        
        $dist_info=$GLOBALS['dist_info'];
        
        $dist_id=$dist_info['id'];
        
        if (empty($dist_info)){
            $root['dist_user_status']=0;
            return output($root,0,"驿站未登录");
        }
        
        $root['dist_user_status']=1;
        $order_id = intval($GLOBALS['request']['data_id']);
        
        $order_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id=".$order_id." and distribution_id=".$dist_id." and type=4 and pay_status=2");
        if($order_info){
            $root['order_status']=1;
            
            $order_info['create_time']=to_date($order_info['create_time']);
            $order_info['status']=$this->order_status($order_info);
            $order_info['total_price']=format_price($order_info['total_price']);
            $order_info['delivery_fee']=format_price($order_info['delivery_fee']);
            
            $region_arr=array('region_lv1'=> $order_info['region_lv1'],'region_lv2'=> $order_info['region_lv2'],'region_lv3'=> $order_info['region_lv3'],'region_lv4'=> $order_info['region_lv4']);
            $consignee_address = $this->get_consignee_address($region_arr);
            $order_info['address'] = $consignee_address.$order_info['address'].$order_info['street'].$order_info['doorplate'];
            
            if($order_info['deal_order_item'])
            {
                $item = unserialize($order_info['deal_order_item']);
            }
            else
            {
                $order_id = $order_info['id'];
                update_order_cache($order_id);
                $item = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
            }
            $order_info['item_count']=count($item);
            $order_info['distribution_fee']=0;
            
            $new_list=array();
            $is_apply=0;
            $is_refund=0;
            foreach ($item as $tt => $vv){
                if($vv['refund_status']==1){
                    $is_apply=1;
                }
                
                if($vv['refund_status']==2){
                    $is_refund++;
                }
                
                $new_item=array();
                $new_item['id']=$vv['id'];
                $new_item['return_score']=$vv['return_score'];
                $new_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 375, 375,1));
                $new_item['number']=$vv['number'];
                $new_item['name']=$vv['name'];
                $new_item['status']=$this->order_item_status($vv);
                $new_item['unit_price']=format_price($vv['unit_price']);
                $new_item['attr_str']=$vv['attr_str'];
                $new_item['deal_data']=unserialize($vv['deal_data']);
                
                if($vv['refund_status']==2)$vv['distribution_fee']=0;        
                $new_item['distribution_fee']=format_price($vv['distribution_fee']);
                
                $new_list[]=$new_item;
                
                $order_info['distribution_fee']=$order_info['distribution_fee']+$vv['distribution_fee'];
            }
            $order_info['order_item']=$new_list;
            $order_info['distribution_fee']=format_price($order_info['distribution_fee']);
			if($order_info['cod_money']>0){
				$order_info['payment_info']=$GLOBALS['db']->getRow("select pn.id,pn.money,pn.payment_config,p.class_name,p.name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id=p.id where order_id = ".$order_id." and p.class_name='Cod' and pn.is_paid=1");
				if($order_info['payment_info']){
					$rel=get_payment_name_rel($order_info['cod_mode']);
					$order_info['payment_info']['name']=$order_info['payment_info']['name'].$rel;
				}else{
					$order_info['payment_info']['name']="货到付款(现金)";
					$order_info['payment_info']['money']=$order_info['cod_money'];
				}
				$order_info['payment_info']['money']=format_price($order_info['payment_info']['money']);
			}
            $root['order']=$order_info;
            
            $is_coupon=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution_coupon where order_id=".$order_info['id']);

            if(count($item)==$is_refund || $is_coupon || $is_apply){
                $root['do_delivery'] = 1;
            }else{
                $root['do_delivery'] = 0;
            }
            
            $root['page_title'] = "订单详情";
            
            return $root;
            
        }
        else{
            $root['order_status']=0;
            return output($root,0,"订单不存在");
        }
    
    }
    
    
    /**
     * 发货生成配送码
     * 输入：
     * doi_ids:订单商品id
     *
     * 输出：
     *
     **/
    public function do_delivery(){
        $root=array();
        
        $dist_info=$GLOBALS['dist_info'];
        
        $dist_id=$dist_info['id'];
        
        if (empty($dist_info)){
            $root['dist_user_status']=0;
            return output($root,0,"驿站未登录");
        }
        
        $root['dist_user_status']=1;
        $order_id = $GLOBALS['request']['order_id'];
        
        $order_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id=".$order_id." and type=4 and delivery_status=0 and distribution_id=".$dist_id);
        
        if(!$order_info){
            return output($root,0,"提交数据有误");
        }
        
        $is_coupon=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution_coupon where order_id=".$order_id);
        if($is_coupon){
            return output($root,0,"商品已发货");
        }  
        
        $apply_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where refund_status=1 and order_id=".$order_id);
        
        if($apply_count){
            return output($root,0,"存在退款中商品，无法发货");
        }
    
        $item = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where refund_status in (0,3) and delivery_status=0 and order_id=".$order_id);
        if(!$item){
            return output($root,0,"没有需要发货的商品");
        }
        
        //生成配送码
        $coupon=array();
        $coupon['create_time']=NOW_TIME;
        $coupon['is_valid']=1;
        $coupon['user_id']=$order_info['user_id'];
        $coupon['order_id']=$order_info['id'];
        $coupon['distribution_id']=$dist_id;

        $coupon['sn'] = '5' . substr(NOW_TIME, 1, 9). sprintf('%02s', rand(0, 99));
        $GLOBALS['db']->autoExecute(DB_PREFIX."distribution_coupon",$coupon,'INSERT','','SILENT');
        
        $coupon['id'] = $GLOBALS['db']->insert_id();
        
        if($coupon['id']){
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,is_arrival = 0 where order_id = ".$order_id." and refund_status in (0,3)");
        }
        else {
            return output($root,0,"发货失败");
        }
        
        //发送发货消息
        order_log("发货成功:"."订单".$order_info['order_sn']."由驿站-".$dist_info["name"]."配送",$order_id);
        $msg_content = "订单:".$order_info['order_sn']."已发货，由驿站-".$dist_info["name"]."配送，请注意查收!";
        send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));
        
        //开始同步订单的发货状态
        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2,update_time = '".NOW_TIME."' where id = ".$order_id); 
        
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        update_order_cache($order_id);
        distribute_order($order_id);
        
        return output($root,1,"发货成功");
    }
    
    
    /**
	 * 整笔订单的状态
	 * @param unknown $order_item 订单中的所有商品，二维数据
	 * 
	 * 输出：
	 *  "info"=>"待发货",       状态
        "handle"=>array(       可执行的操作
            0=>array(
                "info"=>"发货",
                "action"=>wap_url("dist","order#delivery",array("data_id"=>$order_info['id'])),
            )
        ),
	 */
	public function order_status($order_info) {
	    $item = unserialize($order_info['deal_order_item']);
	   
	    $no_delivery=0;
	    $delivery=0;
	    $apply=0;
	    $refund=0;
	    $is_arrival=1;
	    foreach ($item as $t => $v){
	        if($v['delivery_status']==0 && ($v['refund_status']==0 || $v['refund_status']==3)){
	            $no_delivery++;
	        }
	        if($v['refund_status']==1 && $v['is_arrival']==0){
	            $apply++;
	        }else if($v['refund_status']==2){
	            $refund++;
	        }else if($v['delivery_status']==1 && $v['is_arrival']==0){
	            $delivery++;
	        }else if($v['is_arrival']==1){
	            $is_arrival++;
	        }
	    }
	    
	    if($no_delivery){
	        $status=array(
                "info"=>"待发货",
                "handle"=>array(
                    0=>array(
                        "info"=>"发货",
                        "action"=>wap_url("dist","order#view",array("data_id"=>$order_info['id'])),
                    )
                ),
	        );
	        /* if($delivery){
	            $status["handle"][]=array(
                    "info"=>"查看包裹",
                    "action"=>wap_url("dist","order#parcel",array("data_id"=>$order_info['id'])),
	            );
	        } */
	        return $status;
	    }
	    
	    if($delivery){
	        $status=array(
	            "info"=>"待配送",
	            /* "handle"=>array(
	                0=>array(
	                    "info"=>"查看包裹",
                        "action"=>wap_url("dist","order#parcel",array("data_id"=>$order_info['id'])),
	                )
	            ), */
	        );
	        return $status;
	    }
	    
	    if($apply){
	        $status=array(
	            "info"=>"退款中",
	        );
	        return $status;
	    }
	    
	    if($is_arrival){
	        $status=array(
	            "info"=>"已结单",
	        );
	        return $status;
	    }
	    
	    $status=array(
	        "info"=>"已退款",
	    );
	    return $status;
	}
	
	/**
	 * 获取订单中单个商品的状态
	 * $order_item_info 订单中单个商品，一维数据
	 */
	public function order_item_status($order_item) {
	    if($order_item['dp_id']){
	        $status=array(
	            "info"=>"已完结",
	        );
	        return $status;
	    }
	    
	    if($order_item['is_arrival']==1){
	        $status=array(
	            "info"=>"待评价",
	        );
	        return $status;
	    }
	    
	    if($order_item['refund_status']==1){
	        $status=array(
	            "info"=>"退款中",
	        );
	        return $status;
	    }
	    
	    if($order_item['refund_status']==2){
	        $status=array(
	            "info"=>"已退款",
	        );
	        return $status;
	    }
	    
	    if($order_item['delivery_status']==0){
	        $status=array(
	            "info"=>"待发货",
	        );
	        return $status;
	    }
	    
	    if($order_item['delivery_status']==1){
	        $status=array(
	            "info"=>"待配送",
	        );
	        return $status;
	    }
	}
	
	/**
	 * 获取收货地址
	 * @param unknown $region_arr
	 * @return string
	 */
	public function get_consignee_address($region_arr){
	    $address='';
	    if($region_arr){
	        foreach($region_arr as $k=>$v){
	            $sql = "select name from ".DB_PREFIX."delivery_region where id=".$v;
	            $address .= $GLOBALS['db']->getOne($sql);
	        }
	    }
	    return $address;
	
	}
}