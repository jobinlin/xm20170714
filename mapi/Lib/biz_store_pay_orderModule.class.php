<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class biz_store_pay_orderApiModule extends MainBaseApiModule
{
    /**
     * 	 消费券验证接口
     *
     * 	输入:time int 时间 [可选]
     *
     *  输出:
    Array
    (
        [biz_user_status] => 1		                    int             商户登录状态 0未登录/1已登录
        [is_auth] => 1				                    int             模块操作权限 0没有权限 / 1有权限
        [item] => Array
        (
            [0] => Array
            (
            [id] => 49
            [order_sn] => 2017011405301978              varchar         订单号
            [total_price] => 20	                        decimal(20,4)   消费金额
            [user_name] => 0123
            [order_status] => 1	                        tinyint         订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）
            [pay_status] => 2	                        tinyint	        支付状态 0:未支付 2:全部付款
            [create_time] => 2017-01-14 17:30:19	    int	            创建时间
            [pay_amount] => 7	                        decimal(20,4)	实付金额 当pay_amount+discount_price = total_price 支付成功
            [payment_fee] => 0	                        decimal(20,4)	手续费

            text	该订单享受的优惠的详细数据
            [promote] => Array
            (
                [0] => Array
                (
                    [discount_price] => 3
                    [class_name] => Discountamount
                    [name] => 减额促销
                    [description] => 开心就减 10-3
                    [discount_role] => 0
                )
            )
            [discount_price] => 3	                    decimal(20,4)   优惠金额
            [other_money] => 0	                        decimal(20,4)	不可优惠金额
            [location_name] => 长胖胖1                   varchar(255)    门店名称
            [status] => 已付款
        )
    )
        [page] => Array
        (
            [page] => 1
            [page_total] => 0
            [page_size] => 20
            [data_total] => 0
        )
        [page_title] => 买单记录
    )
     */
    public function index()
    {
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*获取参数*/
        $page = intval($GLOBALS['request']['page']); //分页
        $page=$page==0?1:$page;

        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //返回商户权限
        if(!check_module_auth('store_pay_order')){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作权限");
        }else{
            $root['is_auth'] = 1;
        }

        $create_ym = strim($GLOBALS['request']['create_ym']);
        $date_ym = substr($create_ym, 0,4).'年'.substr($create_ym, -2).'月';

        if(!$create_ym){//提交查询的年月
            $create_ym = date('Ym',NOW_TIME);
            $date_ym = substr($create_ym, 0,4).'年'.substr($create_ym, -2).'月';
        }
        $time_ym = date('Ym',NOW_TIME);
        $create_y = substr($time_ym, 0,4);    //年份
        $create_m = substr($time_ym, -2);     //月份
        $date_list = array();
        $date_list[] = $create_ym;
        $i=12;
        while($i>0){
            if($create_m>0){
                if(strlen($create_m)==1){
                    $create_m = "0".$create_m;//如果是一位数前面补0
                }
            }else{
                $create_m=12;
                $create_y = intval($create_y)-1;//小于0的时候减一年
            }
            $date_ym_list[$create_y.$create_m] = $create_y.'年'.$create_m.'月';
            $create_m = intval($create_m)-1;
            $i--;
        }
        $root['date_list'] = $date_ym_list;

        //分页
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;

        $supplier_id = $account_info[supplier_id];//商户id

        $sql = "select sto.* , sl.name as location_name from ".DB_PREFIX."store_pay_order as sto left join ".DB_PREFIX."supplier_location as sl on sto.location_id=sl.id where sto.supplier_id={$supplier_id} AND sto.is_delete=0 and pay_status = 2 and create_ym={$create_ym} order by sto.create_time desc limit ".$limit;

        $sql_count = "select count(*) from ".DB_PREFIX."store_pay_order as sto left join ".DB_PREFIX."supplier_location as sl on sto.location_id=sl.id where sto.supplier_id={$supplier_id} AND sto.is_delete=0 and pay_status = 2 and create_ym={$create_ym}";
        $list = $GLOBALS['db']->getAll($sql);
        $count = $GLOBALS['db']->getOne($sql_count);
        $page_total = ceil($count/$page_size);
        //end 分页

        //要返回的字段
        $data = array();
        foreach($list as $k=>$v)
        {
            $order_item = array();
            $order_item['id'] = $v['id'];
            $order_item['order_sn'] = $v['order_sn'];
            if($v['pay_status']==0){
                $order_item['total_price'] = number_format(round($v['total_price']-$v['payment_fee'],2),2);
            }else{
                $order_item['total_price'] = number_format(round($v['total_price'],2),2);
            }

            $order_item['user_name'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id={$v['user_id']}");
            $order_item['order_status'] = $v['order_status'];
            $order_item['pay_status'] = $v['pay_status'];
            $order_item['create_time'] = to_date($v['create_time']);
            $order_item['pay_amount'] = number_format(round($v['pay_amount'],2),2);
            $order_item['payment_fee'] = round($v['payment_fee'],2);
            $order_item['promote'] = unserialize($v['promote']);
            $order_item['discount_price'] = number_format(round(($v['discount_price']+$v['exchange_money']),2),2);
            $order_item['other_money'] = round($v['other_money'],2);
            $order_item['location_name'] = $v['location_name'];

            //订单状态
            $order_status = '已付款';
            $order_item['status'] = $order_status;

            $data[$k] = $order_item;
        }
        $total_income = $GLOBALS['db']->getOne("select sum(pay_amount) as total_income from ".DB_PREFIX."store_pay_order where supplier_id={$supplier_id} AND is_delete=0 and pay_status = 2 and create_ym={$create_ym} ");
        $root['total_income'] = number_format(round($total_income,2),2);
        $root['create_ym'] = $create_ym;
        $root['date_ym'] = $date_ym;
        $root['item'] = $data;

        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        $root['page_title'] = "买单记录";

        return output($root);
    }
}
?>