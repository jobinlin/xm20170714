<?php
/**
*
* @author hhcycj
*/
class  DealOrderViewModel extends ViewModel{
    public $viewFields = array(
        'DealOrder'=>array('id','order_sn', 'type', 'total_price', 'consignee', 'pay_amount',  'create_time',  'pay_status',  'delivery_status',  'refund_status',  'order_status', 'region_lv2','region_lv3','region_lv4', 'deal_order_item', 'supplier_id', 'is_delete', '_type'=>'left'),
        'User'=>array('user_name', 'mobile', 'email', '_on'=>'DealOrder.user_id=User.id'),
        'DealOrderItem'=>array('_on'=>'DealOrder.id=DealOrderItem.order_id'),
    );
    
    // 设置需要查询的字段
    public $serchFields = array(
        'DealOrder.order_sn'            =>  'order_sn',
        'DealOrder.pay_status'          =>  'pay_status',
        'DealOrder.delivery_status'     =>  'delivery_status',
        'DealOrder.refund_status'       =>  'refund_status',
        'DealOrder.order_status'        =>  'order_status',
        'User.user_name'                =>  'user_name',
        'User.mobile'                   =>  'mobile',
    );
}


 