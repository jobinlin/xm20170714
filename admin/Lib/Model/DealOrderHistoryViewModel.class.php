<?php
/**
*
* @author hhcycj
*/
class  DealOrderHistoryViewModel extends ViewModel{
    public $viewFields = array(
        'DealOrderHistory'=>array('id','order_sn', 'type', 'total_price',  'pay_amount', 'consignee',  'create_time',  'pay_status',  'delivery_status',  'refund_status',  'order_status', 'region_lv2','region_lv3','region_lv4', 'history_deal_order_item'=>'deal_order_item', 'is_delete', '_type'=>'left'),
        'User'=>array('user_name', 'mobile', 'email', '_on'=>'DealOrderHistory.user_id=User.id'),
    );
    
    // 设置需要查询的字段
    public $serchFields = array(
        'DealOrderHistory.order_sn'            =>  'order_sn',
        'DealOrderHistory.pay_status'          =>  'pay_status',
        'DealOrderHistory.delivery_status'     =>  'delivery_status',
        'DealOrderHistory.refund_status'       =>  'refund_status',
        'DealOrderHistory.order_status'        =>  'order_status',
        'User.user_name'                       =>  'user_name',
        'User.mobile'                          =>  'mobile',
    );
}


 