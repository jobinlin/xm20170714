<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_tuan_orderApiModule extends MainBaseApiModule
{

    /**
     * 	团购订单列表
     *
     * 	 输入:
     *  [page] int 分页页数
     *
     *  输出:
     *   [biz_user_status] => 1
         [order] => Array
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
                                )

                        )

                    [order_status] => 待使用   订单状态
                    [op_array] => Array    可以操作的动作
                        (
                            [0] => Array
                                (
                                    [js_obj] => js_coupon
                                    [name] => 查看团购劵
                                )

                        )

                )
          )      
       [page] => Array  分页信息
        (
            [page] => 1
            [page_total] => 1
            [page_size] => 10
            [data_total] => 4
        )
        */
    public function index(){
         
         
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id=$account_info['supplier_id'];
        //分页
        $page_size = 10;
        $page = intval($GLOBALS['request']['p']);
    
        /* type 订单状态，type=0,全部，type=1 未发货，type=2 已发货*/

        if($page==0) $page = 1;
        $limit = (($page-1)*$page_size).",".$page_size;
    
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商户权限  PC端模块命名为所以用dealo
        if(!check_module_auth('dealo')){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
    
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.type = 5 AND do.pay_status = 2 and doi.is_shop=0 and do.supplier_id = ".$supplier_id;
         
        $order_sql = "select distinct(doi.order_id) as order_id , do.order_sn ".$condition." order by doi.order_id desc limit ".$limit;
        $order_count_sql = "select count(distinct(doi.order_id)) ". $condition;

        $order_arr = $GLOBALS['db']->getAll($order_sql);
        $count = intval($GLOBALS['db']->getOne($order_count_sql));
        $order_arr2=array();
        foreach($order_arr as $k=>$v){
            $order_arr2[] = $v['order_id'];
        }
    
       $order_item_sql = "select doi.* from ".DB_PREFIX."deal_order_item as doi LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") and doi.is_shop=0 and doi.order_id in(".implode(",",$order_arr2).") group by doi.id";
        
    
        $order_item = $GLOBALS['db']->getAll($order_item_sql);
    
    
        foreach($order_arr as $k=>$v){
            foreach($order_item as $kk=>$vv){
                if($v['order_id']==$vv['order_id']){
                    $order_arr[$k]['order_item'][]=$vv;
                }
            }
        }
    
        foreach($order_arr as $k=>$v){
            $order_status = $this->get_order_status($v['order_item']);
            $order_arr[$k]['order_status_text'] = $order_status['order_status_text'];
            $order_arr[$k]['op_array'] = $order_status['op_array'];
            $order_arr[$k]['order_item_count'] = count($v['order_item']);
            foreach($v['order_item'] as $kk=>$vv){
                $order_arr[$k]['order_item'][$kk]['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 375, 375,1));
                $order_arr[$k]['order_item'][$kk]['url'] = wap_url("index","deal",array("data_id"=>$vv['deal_id']));
                //$order_arr[$k]['order_item'][$kk]['unit_price'] =str_pad(round($vv['unit_price'],2),2,0,STR_PAD_RIGHT);
                $order_arr[$k]['order_item'][$kk]['unit_price'] =round($vv['unit_price'],2);
            }
        }
    
    
        $root['order']=$order_arr;
    
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
    
        $root['page_title'] = "团购订单";
        return output($root);
    }
    
    /**
     * 	团购订单详情
     *
     * 	 输入: [id] int 订单ID 
     *  无
     *
     *  输出:
     *    [biz_user_status] => 1
        [id] => 486
        [order] => Array
        (
            [id] => 486
            [order_sn] => 2017011710025583
            [type] => 0
            [user_id] => 71
            [create_time] => 1484589775
            [update_time] => 0
            [pay_status] => 2
            [total_price] => 1079.8000
            [pay_amount] => 1079.8000
            [delivery_status] => 5
            [order_status] => 0
            [is_delete] => 0
            [return_total_score] => 120
            [refund_amount] => 226.0000
            [admin_memo] => 
            [memo] => 
            [region_lv1] => 0
            [region_lv2] => 0
            [region_lv3] => 0
            [region_lv4] => 0
            [address] => 
            [mobile] => 
            [zip] => 
            [consignee] => 
            [deal_total_price] => 1351.0000
            [discount_price] => 271.2000
            [delivery_fee] => 0.0000
            [ecv_id] => 0
            [ecv_money] => 0.0000
            [account_money] => 1079.8000
            [delivery_id] => 0
            [payment_id] => 0
            [payment_fee] => 0.0000
            [return_total_money] => 0.0000
            [extra_status] => 0
            [after_sale] => 1
            [refund_money] => 226.0000
            [bank_id] => 
            [referer] => 
            [deal_ids] => 57
            [user_name] => fanwe
            [refund_status] => 1
            [retake_status] => 0
            [promote_description] => 减额促销<br />满200免运费<br />免运费<br />全场免运费<br />
            [deal_order_item] => Array   订单中的商品信息
                (
                    [0] => Array
                        (
                            [id] => 807
                            [deal_id] => 57
                            [number] => 12
                            [unit_price] => 113.0000
                            [total_price] => 1356.0000
                            [delivery_status] => 5
                            [name] => 桥亭活鱼小镇 仅售88元！价值100元的代金券1张 [18点以后,2-5人套餐]
                            [return_score] => 10
                            [return_total_score] => 120
                            [attr] => 256,258
                            [verify_code] => b691c4b3c7a596a606e50d8210550011
                            [order_sn] => 2017011710025583
                            [order_id] => 486
                            [return_money] => 0.0000
                            [return_total_money] => 0.0000
                            [buy_type] => 0
                            [sub_name] => 88元桥亭活鱼小镇代金券 [18点以后,2-5人套餐]
                            [attr_str] => 18点以后2-5人套餐
                            [is_balance] => 0
                            [balance_unit_price] => 50.0000
                            [balance_memo] => 
                            [balance_total_price] => 600.0000
                            [balance_time] => 0
                            [add_balance_price] => 12.0000
                            [add_balance_price_total] => 144.0000
                            [refund_status] => 1
                            [dp_id] => 0
                            [is_arrival] => 0
                            [is_coupon] => 1
                            [deal_icon] => ./public/attachment/201502/25/14/54ed67b2cd14b.jpg
                            [location_id] => 0
                            [supplier_id] => 23
                            [is_refund] => 1
                            [user_id] => 71
                            [is_shop] => 0
                            [consume_count] => 2
                            [is_pick] => 0
                            [fx_user_id] => 0
                            [fx_salary] => 0.0000
                            [fx_salary_total] => 0.0000
                            [fx_salary_all] => 
                            [message_id] => 164
                            [is_delivery] => 0
                            [order_status_text] => 待使用   订单中商品的状态
                        )

                )

            [is_refuse_delivery] => 0
            [consignee_id] => 0
            [promote_arr] => a:4:{i:0;a:9:{s:2:"id";s:1:"8";s:10:"class_name";s:14:"Discountamount";s:4:"sort";s:1:"1";s:6:"config";s:69:"a:2:{s:14:"discount_limit";s:3:"100";s:15:"discount_amount";s:1:"5";}";s:11:"description";s:12:"减额促销";s:4:"type";s:1:"0";s:11:"supplier_id";s:1:"0";s:4:"name";s:12:"减额促销";s:20:"supplier_or_platform";s:1:"0";}i:1;a:9:{s:2:"id";s:1:"9";s:10:"class_name";s:11:"Freebyprice";s:4:"sort";s:1:"2";s:6:"config";s:47:"a:1:{s:23:"free_delivery_buy_price";s:3:"100";}";s:11:"description";s:15:"满200免运费";s:4:"type";s:1:"0";s:11:"supplier_id";s:1:"0";s:4:"name";s:21:"可配置的免运费";s:20:"supplier_or_platform";s:1:"0";}i:2;a:9:{s:2:"id";s:2:"12";s:10:"class_name";s:12:"Freebynumber";s:4:"sort";s:1:"3";s:6:"config";s:45:"a:1:{s:23:"free_delivery_buy_count";s:1:"1";}";s:11:"description";s:9:"免运费";s:4:"type";s:1:"0";s:11:"supplier_id";s:1:"0";s:4:"name";s:21:"可配置的免运费";s:20:"supplier_or_platform";s:1:"0";}i:3;a:9:{s:2:"id";s:2:"13";s:10:"class_name";s:12:"Freedelivery";s:4:"sort";s:1:"4";s:6:"config";s:2:"N;";s:11:"description";s:15:"全场免运费";s:4:"type";s:1:"0";s:11:"supplier_id";s:1:"0";s:4:"name";s:15:"全场免运费";s:20:"supplier_or_platform";s:1:"0";}}
            [record_delivery_fee] => 0.0000
            [is_cancel] => 0
            [order_status_text] => 待使用    订单状态
            [op_array] => Array   订单可以操作的动作
                (
                    [0] => Array
                        (
                            [js_obj] => js_coupon
                            [name] => 查看团购劵
                        )

                )

        )
    
     */
    public function view(){



        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id=$account_info['supplier_id'];
        $data_id = intval($GLOBALS['request']['data_id']);
        
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        $order_sql="select * from ".DB_PREFIX."deal_order where supplier_id = ".$supplier_id." and id=".$data_id;
        $order = $GLOBALS['db']->getRow($order_sql);
        $root['data_id'] = intval($order['id']);
        if (empty($order)){
            return output($root,0,"订单不存在");
        }
        

        $order_item_sql = "select doi.*,d.balance_price from ".DB_PREFIX."deal_order_item as doi LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
            left join ".DB_PREFIX."deal as d on d.id = doi.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") and doi.is_shop=0 and doi.order_id =".$order['id']." group by doi.id";
        
        $order_item = $GLOBALS['db']->getAll($order_item_sql);
        
        $order_status = $this->get_order_status($order_item);
        $order['order_status_text'] = $order_status['order_status_text'];
        $order['op_array'] = $order_status['op_array'];
        
        $total_refund_money=0;
        $total_balance_price=0;
        foreach($order_item as $k=>$v){
            $order_item[$k]['deal_icon'] =get_abs_img_root(get_spec_image($v['deal_icon'], 375, 375,1));
            $order_item[$k]['order_status_text'] =$this->get_order_item_status($v);
            $order_item[$k]['unit_price'] =round($v['unit_price'],2);  
            
            $deal_coupon_sql = "select sum(refund_money) as total_refund_money,count(*) as refund_count from ".DB_PREFIX."deal_coupon where refund_status=2 and order_deal_id =".$v['id'];
            //echo $deal_coupon_sql;exit();
            $deal_coupon_info=$GLOBALS['db']->getRow($deal_coupon_sql);
            
            $total_refund_money+=$deal_coupon_info['total_refund_money'];
            
            $total_balance_price+=$v['balance_total_price']*($v['number']-$deal_coupon_info['refund_count'])/$v['number'];
            
        }
        
        $order['deal_order_item'] = $order_item;
        $order['create_time_format'] = to_date($order['create_time'],'Y-m-d H:i');
        $root['order'] = $order;
        
        $feeinfo = array();
        $fee_detail = array();
        $fee_detail['name'] = '实际支付';
        $fee_detail['symbol'] = 1;
        $fee_detail['value'] = round($order['total_price']-$order['youhui_money']-$order['ecv_money'],2);
        $feeinfo[] = $fee_detail;
        
        if( $order['youhui_money'] > 0){
            $fee_detail['name'] = '优惠券';
            $fee_detail['symbol'] = -1;
            $fee_detail['value'] = round($order['youhui_money'],2);
            $feeinfo[] = $fee_detail;
        }
        
        if( $total_refund_money > 0){
            $fee_detail['name'] = '退款金额';
            $fee_detail['symbol'] = -1;
            $fee_detail['value'] = round($total_refund_money,2);
            $feeinfo[] = $fee_detail;
        }
        
        
        $fee_detail['name'] = '结算金额';
        $fee_detail['symbol'] = 1;
        $fee_detail['value'] = round($total_balance_price,2);
        $feeinfo[] = $fee_detail;
        
        $root['feeinfo'] = $feeinfo;
        $root['pay_price'] =round( $order['total_price'],2);
        
        $root['page_title'] = "订单详情";
        return output($root);
        
    }
    
    public function get_order_status($order_item){
        $confirm_count=0;//团购劵已使用数量
        $dp_count=0;//已点评数量
        $refund_count=0; //已退款数量
        $order_item_id=array();
        foreach($order_item as $k=>$v){
            $order_item_id[]=$v['id'];
        }
    
        $deal_coupon_sql = "select * from ".DB_PREFIX."deal_coupon where order_deal_id in(".implode(",",$order_item_id).")";
        $deal_coupon = $GLOBALS['db']->getAll($deal_coupon_sql);
        $deal_coupon_num = count($deal_coupon);  //订单中团购劵的数量
        foreach($order_item as $k=>$v){
            foreach($deal_coupon as $kk=>$vv){
                if($v['id'] == $vv['order_deal_id'] ){
                    $order_item[$k]['deal_coupon'][]=$vv;
                }      
            }
        }
        
        $has_refund=0;
        foreach($order_item as $k=>$v){
            foreach($v['deal_coupon'] as $kk=>$vv){
                if($vv['is_balance']==0 && ( $vv['refund_status']==0 || $vv['refund_status']==3)){
                    $order_status='待使用';
                    $order_status_num=0;
                    break;
                }
                
                if($vv['is_balance']==1){
                    $confirm_count++;
                }
                
                if($vv['refund_status']==1){
                    $order_status='退款维权';
                    $order_status_num=3;
                    $has_refund=1;
                }
                
                if($vv['refund_status']==2){
                    $order_status='已退款';
                    $order_status_num=4;
                    $refund_count++;
                }  
            }
            
            if($v['dp_id'] > 0){
                $dp_count++;
            }

        }
    
        
        if($dp_count==count($order_item)){//团购劵全部使用,并且全部点评完，订单状态为已完成
            $order_status='已完成';
            $order_status_num=2;
        }
        $op_array=array();
        if($has_refund==1 && $order_status_num > 0){ 
            $order_status='退款维权';
            $order_status_num=3;
            /*
            $op_array=array(
                array('js_obj'=>'js_refund','name'=>'退款审核'),
            );
            */
        }
        
        if(($confirm_count + $refund_count == $deal_coupon_num) && $confirm_count > 0){//团购劵 使用数量 + 已退款数量 = 团购总数，订单状态才为 待评价
            $order_status='待评价';
            $order_status_num=1;
        }
    
        if($order_status_num==0){
            $order_status='待使用';
            $order_status_num=0;
        }

        $result['order_status_text'] = $order_status;
        $result['op_array'] = $op_array;
    
        return $result;
    
    }
    
    /**
     * 获取订单中单个商品的状态
     * @param unknown $order_item 订单中单个商品，一维数据
     */
    public function get_order_item_status($order_item){
        
        $confirm_count=0;//团购劵已使用数量
        $refund_count=0; //已退款数量
        $deal_coupon_sql = "select * from ".DB_PREFIX."deal_coupon where order_deal_id =".$order_item['id'];
        $deal_coupon = $GLOBALS['db']->getAll($deal_coupon_sql);
        $deal_coupon_num = count($deal_coupon);  //商品团购劵的数量
        
        $order_status='';
        if($deal_coupon){
            foreach($deal_coupon as $k=>$v){
                if($v['is_balance']==0 && ( $v['refund_status']==0 || $v['refund_status']==3)){
                    $order_status='待使用';
                    $order_status_num=0;
                    break;
                }
                
                if($v['is_balance']==1){
                    $confirm_count++;
                }
                
                if($v['refund_status']==1){
                    $order_status='退款维权';
                    $order_status_num=3;
                    $has_refund=1;
                }
                
                if($v['refund_status']==2){
                    $order_status='已退款';
                    $order_status_num=4;
                    $refund_count++;
                }
            }
        }


        if(($confirm_count + $refund_count ==$deal_coupon_num) && $confirm_count > 0){//团购劵 使用数量 + 已退款数量 = 团购总数，商品状态才为 待评价
            $order_status='待评价';
            $order_status_num=1;
        }
        if($order_item['dp_id'] > 0){//商品状态为已完成
            $order_status='已完成';
            $order_status_num=2;
        }
        
        if($has_refund==1 && $order_status_num > 0){
            $order_status='退款维权';
            $order_status_num=3;
        }
        
        return $order_status;
    
    }
    
}
?>

