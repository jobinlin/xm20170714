<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_shop_orderApiModule extends MainBaseApiModule
{

    /**
     * 	商城订单列表
     *
     * 	 输入:
     *  [page] int 分页页数
     *  [type] int 订单类型 ，type=0,全部，type=1 未发货，type=2 已发货
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

                    [order_status] => 待发货   订单状态
                    [op_array] => Array    可以操作的动作
                        (
                            [0] => Array
                                (
                                    [js_obj] => js_delivery
                                    [name] => 发货
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
        $page = intval($GLOBALS['request']['page']);

        /* type 订单状态，type=0,全部，type=1 未发货，type=2 已发货*/
        $type = intval($GLOBALS['request']['type']);
        if($page==0) $page = 1;
        $limit = (($page-1)*$page_size).",".$page_size;
        
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商户权限  PC端模块命名为所以用goodso
        if(!check_module_auth('goodso')){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.type = 6 AND do.pay_status = 2 and doi.is_shop=1 and do.supplier_id = ".$supplier_id;
        if($type==0){ //全部订单

            $order_sql = "select distinct(doi.order_id) as order_id , do.order_sn ".$condition." order by doi.order_id desc limit ".$limit;
            $order_count_sql = "select count(distinct(doi.order_id)) ". $condition;
        }elseif($type==1){//未发货订单
            
            $order_sql = "select distinct(doi.order_id) as order_id , do.order_sn ".$condition." and doi.delivery_status=0 and doi.is_pick=0 order by doi.order_id desc limit ".$limit;
            $order_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=0 and doi.is_pick=0";
             
        }elseif($type==2){//已发货订单
            $order_sql = "select distinct(doi.order_id) as order_id , do.order_sn ". $condition." and doi.delivery_status=1 and doi.is_pick=0 order by doi.order_id desc limit ".$limit;
            $order_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=1 and doi.is_pick=0";
             
        }
        //print_r($order_sql);exit;
        $order_arr = $GLOBALS['db']->getAll($order_sql);
        $count = intval($GLOBALS['db']->getOne($order_count_sql));
        $order_arr2=array();
        foreach($order_arr as $k=>$v){
            $order_arr2[] = $v['order_id'];
        }

        $order_item_sql = "select doi.* from ".DB_PREFIX."deal_order_item as doi LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") and doi.is_shop=1 and doi.order_id in(".implode(",",$order_arr2).") group by doi.id";

        //echo $order_item_sql;exit;
        $order_item = $GLOBALS['db']->getAll($order_item_sql);
        
        
        foreach($order_arr as $k=>$v){
            $order_refund_count=0;
            $order_delivery_count=0;
            foreach($order_item as $kk=>$vv){ 
                if($v['order_id']==$vv['order_id']){
                    $order_arr[$k]['order_item'][]=$vv;
                    if($vv['delivery_status']==0 && $type==2 && $vv['refund_status'] <= 1){  //有一个商品未发货，属于未发货
                        unset($order_arr[$k]);
                    }
                    if(($vv['refund_status']==1 || $vv['refund_status']==2) && $type==1){  //统计订单中退款维权和已退款的数量
                       $order_refund_count++;
                    }
                    if(($vv['delivery_status']==1 || $vv['delivery_status']==5 ) && ($vv['refund_status']==0 || $vv['refund_status']==3) && $type==1){  //统计订单中退款维权和已退款的数量
                       $order_delivery_count++;
                    }
                }
            } 
            if($type==1 && ($order_refund_count + $order_delivery_count==count($order_arr[$k]['order_item']))){  //退款数量+已发货数量+无需发货数量=商品总数，订单不在未发货列表显示
                unset($order_arr[$k]);
            }
        }

        foreach($order_arr as $k=>$v){
            //logger::write($v['order_sn']);
            $order_status = $this->get_order_status($v['order_item'],$v['location_id']);
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
        
        //sort($order_arr);
        $root['order']=$order_arr;
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        
        
        $root['page_title'] = "商城订单";
        return output($root);
    }
    
    /**
     * 	商城订单详情
     *
     * 	 输入: [id] int 订单ID 
     *  无
     *
     *  输出:
     *  
     *   [order] => Array
        (
            [id] => 484
            [order_sn] => 2017011409551198
            [type] => 0
            [user_id] => 71
            [create_time] => 1484330111
            [update_time] => 0
            [pay_status] => 2
            [total_price] => 131.4400
            [pay_amount] => 131.4400
            [delivery_status] => 0
            [order_status] => 0
            [is_delete] => 0
            [return_total_score] => 0
            [refund_amount] => 0.0000
            [admin_memo] => 
            [memo] => 
            [region_lv1] => 1
            [region_lv2] => 3
            [region_lv3] => 37
            [region_lv4] => 410
            [address] => 中国安徽蚌埠东市区sdfadfasdf   送货地址
            [mobile] => 14534512311  用户电话
            [zip] => 2343552  邮编
            [consignee] => dsafdsa  收货人的姓名
            [deal_total_price] => 151.8000
            [discount_price] => 30.3600
            [delivery_fee] => 10.0000
            [ecv_id] => 0
            [ecv_money] => 0.0000
            [account_money] => 131.4400
            [delivery_id] => 10
            [payment_id] => 0
            [payment_fee] => 0.0000
            [return_total_money] => 0.0000
            [extra_status] => 0
            [after_sale] => 0
            [refund_money] => 0.0000
            [bank_id] => 
            [referer] => 
            [deal_ids] => 64,74,76
            [user_name] => fanwe
            [refund_status] => 0
            [retake_status] => 0
            [promote_description] => 
            [deal_order_item] => Array  订单中的商品信息
                (
                    [0] => Array
                        (
                            [id] => 803
                            [deal_id] => 64
                            [number] => 1
                            [unit_price] => 72.0000
                            [total_price] => 72.0000
                            [delivery_status] => 1
                            [name] => 仅售69元！价值398元的龙中龙男士棉服1件，可脱卸帽保暖加厚棉衣，青年休闲外套。 [黑色,S]
                            [return_score] => 0
                            [return_total_score] => 0
                            [attr] => 508,509
                            [verify_code] => 37e85754bbfc59399aec085ab03695b8
                            [order_sn] => 2017011409551198
                            [order_id] => 484
                            [return_money] => 0.0000
                            [return_total_money] => 0.0000
                            [buy_type] => 0
                            [sub_name] => 龙中龙男士棉服 [黑色,S]
                            [attr_str] => 黑色S
                            [is_balance] => 0
                            [balance_unit_price] => 69.0000
                            [balance_memo] => 
                            [balance_total_price] => 69.0000
                            [balance_time] => 0
                            [add_balance_price] => 3.0000
                            [add_balance_price_total] => 3.0000
                            [refund_status] => 0
                            [dp_id] => 0
                            [is_arrival] => 1
                            [is_coupon] => 1
                            [deal_icon] => ./public/attachment/201502/25/16/54ed82ca42ddd.jpg
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
                            [is_delivery] => 1
                            [order_status_text] => 待评价  订单中商品的状态
                        )

                )

            [is_refuse_delivery] => 0
            [consignee_id] => 0
            [promote_arr] => N;
            [record_delivery_fee] => 10.0000
            [is_cancel] => 0
            [order_status_text] => 待收货  订单状态
            [op_array] => Array  订单可以操作的动作
                (
                    [0] => Array
                        (
                            [js_obj] => js_delivery
                            [name] => 发货
                        )

                )

            [delivery_name] => 宅急送快递  配送方式
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
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") and doi.is_shop=1 and doi.order_id =".$order['id']." group by doi.id";
        
        
        $order_item = $GLOBALS['db']->getAll($order_item_sql);
        
        $order_status = $this->get_order_status($order_item);
        $order['order_status_text'] = $order_status['order_status_text'];
        $order['op_array'] = $order_status['op_array'];
        
        $region_arr=array('region_lv1'=> $order['region_lv1'],'region_lv2'=> $order['region_lv2'],'region_lv3'=> $order['region_lv3'],'region_lv4'=> $order['region_lv4']);
        $consignee_address = $this->get_consignee_address($region_arr);
        $order['address'] = $consignee_address.$order['address'];

        $total_refund_money=0;
        $total_balance_price=0;
        foreach($order_item as $k=>$v){
            $order_item[$k]['deal_icon'] =get_abs_img_root(get_spec_image($v['deal_icon'], 375, 375,1));
            $order_item[$k]['order_status_text'] =$this->get_order_item_status($v,$order['location_id']);  
            $order_item[$k]['unit_price'] =round($v['unit_price'],2);
            $total_refund_money+=$v['refund_money'];
            
            if($v['refund_status']<>2){
                $total_balance_price+=$v['balance_total_price'];
            }
        }
        
        $total_balance_price=$total_balance_price+$order['delivery_fee'];
        
        $order['deal_order_item'] = $order_item;
        $order['create_time_format'] = to_date($order['create_time'],'Y-m-d H:i');
        $delivery_name_sql= "select name from ".DB_PREFIX."express where id=".$order['delivery_id'];
        $delivery_name = $GLOBALS['db']->getOne($delivery_name_sql);
        $order['delivery_name'] = $delivery_name;
        $root['order'] = $order;
        
        $feeinfo = array();
        
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
        //$root['pay_price'] =round( $order['total_price']-$order['ecv_money']-$order['youhui_money'],2);
        $root['page_title'] = "订单详情";
        return output($root);
    }
    
    public function logistics()
    {
        $root = array();
        $data_id = $GLOBALS['request']['data_id'];

        $account_info = $GLOBALS['account_info'];
        $supplier_id = intval($account_info['supplier_id']);
        //判断是否登录
        if(!$account_info){
            $root['biz_user_status']=0;
            return output($root,0,"用户未登录");
        }else{
            $user_login_status=1;
        }
        $page_title = '物流跟踪';

        if ($user_login_status == LOGIN_STATUS_LOGINED) {

            //需要发货的商品
            $dnSql = "select dn.* , do.order_sn , t.state , t.ischeck , `t`.`data` as `track_data`,t.express_company from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order as do on dn.order_id=do.id left join ".DB_PREFIX."track as t on dn.order_id=t.order_id and t.express_number=dn.notice_sn left join ".DB_PREFIX."express as e on e.id=dn.express_id and t.express_code=e.class_name where dn.order_id=".$data_id." group by dn.notice_sn order by dn.id asc";
//            echo $dnSql;exit;
            $delivery_notice = $GLOBALS['db']->getAll($dnSql);
            if($delivery_notice){
                foreach($delivery_notice as $k=>$v){
                    unset($delivery_notice[$k]['order_item_id']);
                    $itemSql = "select doi.deal_id,doi.number,doi.unit_price,doi.total_price,doi.is_arrival,doi.refund_status,doi.name,doi.deal_icon,doi.attr_str from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id=doi.id where dn.notice_sn='".$v['notice_sn']."' and dn.express_id=".$v['express_id']." and dn.order_id=".$data_id;
                    $deal_info = $GLOBALS['db']->getAll($itemSql);
                    $now_status=1;
                    foreach($deal_info as $kk=>$vv){
                        $deal_info[$kk]['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 360, 360, 1));
                        $deal_info[$kk]['unit_price_format']=format_price_html($vv['unit_price']);

                        if($vv['is_arrival']==1){
                            $deal_info[$kk]['deal_status']='已收货';
                        }elseif($vv['refund_status']==2){
                            $deal_info[$kk]['deal_status']='已退款';
                        }elseif($vv['refund_status']==3){
                            $deal_info[$kk]['deal_status']='待确认收货';//'拒绝退款';
                            $now_status=0;
                        }elseif($vv['refund_status']==1){
                            $deal_info[$kk]['deal_status']='退款申请中';
                        }else{
                            $deal_info[$kk]['deal_status']='待确认收货';
                            $now_status=0;
                        }

                    }
                    $delivery_notice[$k]['deal_info']=$deal_info;
                    $delivery_notice[$k]['now_status']=$now_status;
                    $delivery_notice[$k]['state_text'] = $this->get_delivery_state($v['state']);
                    if($delivery_notice[$k]['track_data']){
                        $delivery_notice[$k]['track_data']=unserialize($v['track_data']);
                    }

                }
            }
            $root['delivery_notice']=$delivery_notice?$delivery_notice:array();
            $root['delivery_count'] = count($delivery_notice);

            //无需发货的商品
            $order_item=$GLOBALS['db']->getAll("select do.id,do.attr_str,do.is_arrival,do.number,do.unit_price,do.total_price,do.deal_icon,do.name,do.deal_id,do.order_id,do.refund_status from ".DB_PREFIX."deal_order_item as do where do.delivery_status=1 and is_delivery=0 and order_id=".$data_id." and supplier_id=".$supplier_id);
            if($order_item){
                foreach($order_item as $kk=>$vv){
                    $order_item[$kk]['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 360, 360, 1));
                    $order_item[$kk]['unit_price_format']=format_price_html($vv['unit_price']);

                    if($vv['is_arrival']==1){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="已收货";
                    }else if($vv['refund_status']==1){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="退款申请中";
                    }else if($vv['refund_status']==2){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="已退款";
                    }else if($vv['refund_status']==3){
                        $order_item[$kk]['is_use']=1;
                        $order_item[$kk]['info']="拒绝退款";
                    }else{
                        $order_item[$kk]['is_use']=1;
                        $order_item[$kk]['info']="待确认收货";
                    }

                }
                $root['delivery_count']=$root['delivery_count']+1;
            }

            $root['no_delivery_item']=$order_item?$order_item:array();
        }
        $root['biz_user_status'] = $user_login_status;
        $root['page_title'] = $page_title;
        return output($root);

    }
    /**
     *
     * @param unknown $state
     */
    public function get_delivery_state($state){

        $state_text='';
        switch ($state){
            case 0:
                $state_text='在途中';
                break;
            case 1:
                $state_text='已揽收';
                break;
            case 2:
                $state_text='疑难';
                break;
            case 3:
                $state_text='已签收';
                break;
            case 4:
                $state_text='退签';
                break;
            case 5:
                $state_text='同城 派送中';
                break;
            case 6:
                $state_text='退回';
                break;
            case 7:
                $state_text='转单';
                break;
            default:
                $state_text='不懂是什么鬼！';

        }

        return $state_text;
    }
    
    /**
     * 	 商品发货页面接口
     *
     * 	 输入:
     *  data_id [int] 订单ID
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
     *[is_delivery]   => 1      :是否有需要物流的商品 0：无；1：有
     *[no_delivery]   => 1      :是否有需要物流的商品 0：无；1：有
     *[order_id]      => 11     :int 订单id
     *[location_list] => Array  门店列表
     (
         [0] => Array
         (
             [id] => 32  :int 门店ID
             [name] => 爱丁堡尊贵养生会所（福祥店)    :string 门店名称
         )
    
     )
     [express_name] => 圆通快递      :string 用户选择的快递(有物流商品时输出)
     [express_list] => Array     快递列表(有物流商品时输出)
     (
          [0] => Array
          (
              [id] => 3   :int 快递ID
              [name] => EMS   :string 快递名称
          )
     )
     [address_data] => Array         配送地址信息(有物流商品时输出)
     (
         [consignee] => 张三                           :string 收件人
         [mobile] => 15544433333     :收件人手机
         [address] => 中国 福建 福州 台江区,群升国际E区111,350000  :string 收货地址
     )
     [doi_list] => Array     要发货的订单商品
     (
         [0] => Array
         (
             [id] => 182         :int 商品订单ID
             [deal_id] => 86     :int 商品ID
             [deal_icon] => http://localhost/o2onew/public/attachment/201502/26/11/54ee903778026_168x140.jpg     :string 商品缩略图  84*70
             [name] => 仅售8.9元！价值39元的下曹吸盘收纳置物架1个，…    :string 商品名称
             [number] => 1   :int 购买商品数量
             [unit_price]=> 8.9    :float 购买商品的单价
             [total_price] => 8.9    :float 购买商品的总价格
             [is_delivery] => 1    :int 是否需要物流
         )
     )
     */
    public function delivery(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
    
        $root['page_title'] = "商品发货";
    
        /*获取参数*/
        $order_id = intval($GLOBALS['request']['data_id']);
        $root['order_id']=$order_id;
    
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
    
        //返回商户权限
        if(!check_module_auth("goodso")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
    
        //获取支持的门店
        $location_list = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $account_info['location_ids']) . ")");
        $root['location_list'] = $location_list?$location_list:array();
    
        $supplier_id = $account_info['supplier_id'];
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
        $order_table_name = get_supplier_order_table_name($supplier_id);
    
        $sql = "select do.id,do.delivery_status,do.delivery_id,do.region_lv1,do.region_lv2,do.region_lv3,do.region_lv4,do.consignee,do.address,do.zip,do.mobile from ".
            $order_table_name." as do ".
            " where do.order_status=0 and do.type = 6 and do.pay_status = 2 and do.id=".$order_id;
        $order_info=$GLOBALS['db']->getRow($sql);
    
        if($order_info && $order_info['delivery_status']!=2){
    
            //查询订单相关要发货的商品
            $condition = " and doi.order_id = ". $order_id." and dl.location_id in (".implode(",",$account_info['location_ids']).") group by doi.id";
    
            $sql = "select doi.id,doi.deal_id,doi.deal_icon,doi.name,doi.number,doi.unit_price,doi.total_price,doi.is_delivery,doi.attr_str  from ".$order_item_table_name." as doi left join ".
                $order_table_name." as do on doi.order_id = do.id  ".
                " left join ".DB_PREFIX."deal_location_link as dl on doi.deal_id = dl.deal_id".
                " where do.type = 6 and (doi.refund_status=0 or doi.refund_status=3) and doi.is_shop = 1 and do.pay_status = 2 and doi.delivery_status=0 ".$condition;
            $doi_list = $GLOBALS['db']->getAll($sql);
    
            if($doi_list){
    
                $is_delivery=0;
                $no_delivery=0;
    
                foreach ($doi_list as $k => $v){
                    $doi_list[$k]['deal_icon'] =  get_abs_img_root(get_spec_image($v['deal_icon'],84,70,1));
                    $doi_list[$k]['unit_price'] = round($v['unit_price'],2);
                    $doi_list[$k]['total_price'] = round($v['total_price'],2);
                    $doi_list[$k]['name'] = msubstr($v['name'],0,25);
    
                    if($v['is_delivery']==1){
                        $is_delivery=1;
                    }
    
                    if($v['is_delivery']==0){
                        $no_delivery=1;
                    }
                }
    
                if($is_delivery==1){
                    //快递
                    $region_conf = load_auto_cache("cache_delivery_region_conf");
                    $delivery_conf = load_auto_cache("cache_delivery");
                    $root['express_name'] = $delivery_conf[$order_info['delivery_id']]['name'];
                    //获取支持快递
                    $express_list =$GLOBALS['db']->getAll('select id,name from '.DB_PREFIX.'express where is_effect=1');
    
                    //获取地址
                    $address_data=array();
    
                    $address_data['consignee'] = $order_info['consignee'];
                    $address_data['mobile'] = $order_info['mobile'];
                    $address_data['address'] = $region_conf[$order_info['region_lv1']]['name']." ".$region_conf[$order_info['region_lv2']]['name']
                    ." ".$region_conf[$order_info['region_lv3']]['name']." ".$region_conf[$order_info['region_lv4']]['name'].",".$order_info['address'].",".$order_info['zip'];
                    $root['address_data'] = $address_data;
                }
    
                if($no_delivery==1 && $express_list){
                    $no_express=array();
                    $no_express['id']=0;
                    
                    $no_express['name']="无需发货";
                    $express_list[]=$no_express;
                }
                
                $root['is_delivery']=$is_delivery;
                $root['no_delivery']=$no_delivery;
                
                $root['express_list']=$express_list;
    
                $root['doi_list'] = $doi_list;
    
            }else {
                return output($root,0,"非法订单");
            }
    
        }else {
            return output($root,0,"非法订单");
        }
    
        return output($root);
    
    }
    
    
    /**
     * 商品发货接口
     * 输入：
     * doi_ids:订单商品id;
     * delivery_sn:物流单号;
     * memo:备注;
     * express_id:快递id;
     * location_id:门店id;
     * is_delivery:是否为物流商品;
     * 
     * 输出:
     * status:int 结果状态 0失败 1成功
     * info:信息返回
     * biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     * 以下仅在biz_user_status为1时会返回
     * is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  
     * @return unknown_type  */
    public function do_delivery(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
    
    
        /*获取参数*/
        //$order_id = intval($GLOBALS['request']['order_id']);
        $doi_ids = $GLOBALS['request']['doi_ids'];
        $delivery_sn = strim($GLOBALS['request']['delivery_sn']);
        $memo = strim($GLOBALS['request']['memo']);
        $express_id = intval($GLOBALS['request']['express_id']);
        $location_id = intval($GLOBALS['request']['location_id']);
        $is_delivery = intval($GLOBALS['request']['is_delivery']);
    
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
    
        //返回商户权限
        if(!check_module_auth("goodso")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }
    
        if(!in_array($location_id,$account_info['location_ids'])){
            return output($root,0,"请选择正确的门店");
        }
        
        $supplier_id = intval($account_info['supplier_id']);
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
        $order_table_name = get_supplier_order_table_name($supplier_id);
         
        $item_info = $GLOBALS['db']->getAll("select order_id,delivery_status,refund_status,is_delivery,supplier_id from ".$order_item_table_name." where id in (".implode(",", $doi_ids).") ");
        
        $order_id=$item_info['0']['order_id'];
        $root['order_id']=$order_id;
        
        $order_info=$GLOBALS['db']->getRow("select * from ".$order_table_name." where id=".$order_id." and delivery_status<>2");
        if(!$order_info){
            return output($root,0,"提交数据有误");
        }
        
        $is_notorder_id =0;
        foreach ($item_info as $k=>$v){
            if($k>0 && $v['order_id'] != $order_id || $v['delivery_status']!=0
            || $v['refund_status']==1 || $v['refund_status']==2 
            || $v['is_delivery']!=$is_delivery || $v['supplier_id']!=$supplier_id)
            {
                $is_notorder_id = 1;
                break;
            }
        }
    
        if ($is_notorder_id){
            return output($root,0,"提交数据有误");
        }
    
        $items = $GLOBALS['db']->getAll("select distinct(doi.id),doi.* from ".$order_item_table_name." as doi left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id where doi.id in (".implode(",", $doi_ids).") and l.location_id in (".implode(",",$account_info['location_ids']).")");
    
        if(count($items) == count($doi_ids) && count($items)>0){
            if($is_delivery==1){
                //物流商品
                if(empty($delivery_sn))
                {
                    return output($root,0,"请输入快递单号");
                }else {
                    $express_info = $GLOBALS['db']->getRow("select name,class_name from ".DB_PREFIX."express where id=".$express_id);
                    $express_name = $express_info['name'];
                    $ordertrack = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."track where express_code='".$express_info['class_name']."' and express_number='".$delivery_sn."' and order_type=3");
                    if($ordertrack){
                        return output($root,0,"快递单号已存在，请重新填写！");
                    }
                }
                
                $names=array();
                foreach ($items as $k=>$v){
                    $id = $v['id'];
                    $deal_name = $v['name'];
                    array_push($deal_names,$deal_name);
                    $rs = make_delivery_notice($order_id,$id,$delivery_sn,$memo,$express_id,$location_id);
                    if($rs)
                    {
                        $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,is_arrival = 0,location_id = ".$location_id." where id = ".$id);
    
                        send_delivery_mail($delivery_sn,$v['name'],$order_id);
                        send_delivery_sms($delivery_sn,$v['name'],$order_id);
    
                        $names[]=$v['name'];
                    }
    
                }
                $names=implode(",", $names);
                
                $msg_content = '您购买的<'.$names.'>已发货,物流单号:'.$delivery_sn;
                send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));
                
                $deal_names = implode(",",$deal_names);
    
                //查询快递名
                require_once(APP_ROOT_PATH."system/model/deal_order.php");
                order_log("发货成功".$express_name.$delivery_sn.$memo,$order_id);
                 
                if($delivery_sn){
                    //向快递网发送快递查询订阅
                    require_once(APP_ROOT_PATH.'system/model/express.php');
                    $express = new express();
                    $result = $express->get($expressCode=$express_info['class_name'],$delivery_sn,0,$order_info['region_lv3'],$order_id,$order_info['user_id'],0,1,$memo,3);
                }
                 
            }else {
                //无需发货的商品
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,is_arrival = 0,location_id = ".$location_id." where id in (".implode(",", $doi_ids).")");
                foreach ($items as $k=>$v){
                    $msg_content = '您购买的<'.$v['name'].'>已发货,请注意查收';
                    send_msg_new($order_info['user_id'], $msg_content, 'delivery', array('type' => 1, 'data_id' => $order_id));
                    order_log($v['name']."发货成功",$order_id);
                }
    
            }
    
            //开始同步订单的发货状态
            $order_deal_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
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
    
             
            if(count($delivery_deal_items)==0&&count($order_deal_items)!=0)
            {
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0,update_time = '".NOW_TIME."' where id = ".$order_id); //未发货
            }
            elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items))
            {
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1,update_time = '".NOW_TIME."' where id = ".$order_id); //部分发
            }
            else
            {
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2,update_time = '".NOW_TIME."' where id = ".$order_id); //全部发
            }
    
            require_once(APP_ROOT_PATH."system/model/deal_order.php");
            update_order_cache($order_id);
            distribute_order($order_id);
             
            //发微信通知
            //通知商户
            $supplier_list = $GLOBALS['db']->getAll("select distinct(supplier_id) from ".DB_PREFIX."deal_order_item where id in (".implode(",", $doi_ids).")");
            foreach($supplier_list as $row)
            {
                $weixin_conf = load_auto_cache("weixin_conf");
                if($weixin_conf['platform_status']==1)
                {
                    $wx_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = ".$row['supplier_id']);
                    $order_item_id = $GLOBALS['db']->getAll("select id from ".DB_PREFIX."deal_order_item where supplier_id = ".$row['supplier_id']." and delivery_status = 1 and id in (".implode(",", $doi_ids).")");
                    foreach ($order_item_id as $t => $v){
                        send_wx_msg("OPENTM200565259", $order_info['user_id'], $wx_account,array("order_id"=>$order_id,"order_sn"=>$order_info['order_sn'],"company_name"=>$express_name,"delivery_sn"=>$delivery_sn,"order_item_id"=>$v['id']));
                    }
                }
            }
             
            return output($root,1,"发货成功");
             
        }else {
            return output($root,0,"非法的数据");
        }
    }
    
    /**
     * 整笔订单的状态
     * @param unknown $order_item 订单中的所有商品，二维数据
     */
    public function get_order_status($order_item,$pick_location_id)
    {
        $order_status_array = array(
            6 => '待自提',
            3 => '已完成',
            2 => '待评价',
            1 => '待收货',
            0 => '待发货',
            4 => '退款维权',
            5 => '已退款',
        );

        $status = 0;
        $delivery_count=0;//已发货数量
        $arrival_count=0;//已发货数量
        $dp_count=0;//已点评数量
        $refund = 0; // 退款状态的商品数量
        $refund1 = 0;
        foreach ($order_item as $k => $v) {
            $order_id = $v['order_id'];
            if ($v['delivery_status'] == 5 && ($v['refund_status'] == 0 || $v['refund_status'] == 3) && $pick_location_id > 0 ) {
                $status |= pow(2, 6);
                break;
            }
            if ($v['refund_status'] == 1 || $v['refund_status'] == 2) {
                $status |= pow(2, 3 + $v['refund_status']);
                $refund++;
                if ($v['refund_status'] == 1)
                    $refund1++;
            } else {
                if($v['delivery_status'] == 0 && ($v['refund_status'] == 0 || $v['refund_status'] == 3) && $pick_location_id == 0) {
                    $status |= pow(2, 0);
                }
                if ($v['dp_id'] > 0) {
                    $dp_count++;
                }
                if($v['is_arrival']==1){
                    $arrival_count++;
                }
                if($v['delivery_status']==1){
                    $delivery_count++;
                }
            }
        }
        $no = count($order_item) - $refund;
        if ($no > 0) {
            if($dp_count == $no){//全部点评完(不含退款)，订单状态才为 已完成
                if ($refund1 == 0) {
                    $status |= pow(2, 3);
                }
            } elseif($arrival_count == $no){//全收到货(不含退款)，未点评完，订单状态才为 待评价
                $status |= pow(2, 2);
            } elseif($delivery_count == $no){//全部发货(不含退款)，订单状态才为 待收货
                $status |= pow(2, 1);
            }
        }
        
        $op_array=array();//每种订单状态可进行的操作
        $track_url = wap_url("biz",'shop_order#logistics',array('data_id'=>$order_id));
        $delivery_url = wap_url("biz",'shop_order#delivery',array('data_id'=>$order_id));
        if ($status & 1) {
            if($delivery_count==0){
                $op_array=array(
                    array('js_obj'=>'js_delivery','name'=>'发货','url'=>$delivery_url),
                );
            }else{
                $op_array=array(
                    array('js_obj'=>'js_delivery','name'=>'发货','url'=>$delivery_url),
                    array('js_obj'=>'js_track','name'=>'查看物流','url'=>$track_url)
                );
            }
        } else {
            if($delivery_count > 0){
                $op_array=array(
                    array('js_obj'=>'js_track','name'=>'查看物流','url'=>$track_url)
                );
            }
        }
        foreach ($order_status_array as $key => $value) {
            if ((pow(2, $key) & $status) > 0) {
                $order_status = $value;
                break;
            }
        }
// print_r($status);exit;
        /*$delivery_count=0;//已发货数量
        $arrival_count=0;//已发货数量
        $dp_count=0;//已点评数量
        $order_status='待发货';
        $order_status_num=0;
        $has_refund=0;
        foreach($order_item as $k=>$v){
            $order_id=$v['order_id'];
            if($v['delivery_status']==0 && ($v['refund_status']==0 || $v['refund_status']==3) && $v['is_pick']==0){
                $order_status='待发货';
                $order_status_num=0;
            }
            if($v['delivery_status']==5 && ($v['refund_status']==0 || $v['refund_status']==3) && $v['is_pick']==1){
                $order_status='待自提';
                $order_status_num=6;
                break; //自提的订单，订单中所有商品都是自提
            }
            if($v['dp_id']>0){
                $dp_count++;
            }
            if($v['is_arrival']==1){
                $arrival_count++;
            }
            if($v['delivery_status']==1){
                $delivery_count++;
            }
            if($v['refund_status']==1){
               $order_status='退款维权';
               $order_status_num=4;
               $has_refund=1;
            }
            if($v['refund_status']==2){
               $order_status='已退款';
               $order_status_num=5;
            }
            
        }
        
        if($has_refund==1){
            $order_status='退款维权';
            $order_status_num=4;
        }
        if($delivery_count==count($order_item)){//全部发货，订单状态才为 待收货
            $order_status='待收货';
            $order_status_num=1;
        }
        if($arrival_count==count($order_item)){//全收到货，未点评完，订单状态才为 待评价
            $order_status='待评价';
            $order_status_num=2;
        }
        
        if($dp_count==count($order_item)){//全部点评完，订单状态才为 已完成
            $order_status='已完成';
            $order_status_num=3;
        }
        
        $op_array=array();//每种订单状态可进行的操作
        $track_url = wap_url("biz",'shop_order#logistics',array('data_id'=>$order_id));
        $delivery_url = wap_url("biz",'shop_order#delivery',array('data_id'=>$order_id));
        switch ($order_status_num){
            case 0:
                if($delivery_count==0){
                    $op_array=array(
                        array('js_obj'=>'js_delivery','name'=>'发货','url'=>$delivery_url),
                    );
                }else{
                    $op_array=array(
                        array('js_obj'=>'js_delivery','name'=>'发货','url'=>$delivery_url),
                        array('js_obj'=>'js_track','name'=>'查看物流','url'=>$track_url)
                    );
                }
                break;
            default:
                if($delivery_count > 0){
                    $op_array=array(
                        array('js_obj'=>'js_track','name'=>'查看物流','url'=>$track_url)
                    );
                }
        }
        */
        $result['order_status_text'] = $order_status;
        $result['op_array'] = $op_array;
        
        return $result;
        
    }
    
    /**
     * 获取订单中单个商品的状态
     * @param unknown $order_item 订单中单个商品，一维数据
     */
    public function get_order_item_status($order_item,$pick_location_id){
        $order_status='';
        if($order_item){
            if($order_item['refund_status']==1){
                $order_status='退款维权';
            }elseif($order_item['refund_status']==2){
                $order_status='已退款';
            }elseif($order_item['delivery_status']==0 && $pick_location_id == 0){
                $order_status='待发货';
            }elseif($order_item['delivery_status']==5 && $pick_location_id > 0){
                $order_status='待自提';
            }elseif($order_item['delivery_status']==1 && $order_item['is_arrival']==0){
                $order_status='待收货';
            }elseif($order_item['is_arrival']==1 && $order_item['dp_id']==0){
                $order_status='待评价';
            }elseif($order_item['dp_id']>0){
                $order_status='已完成';
            }
        }
        return $order_status;
        
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
?>

