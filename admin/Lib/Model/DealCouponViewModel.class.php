<?php
/**
*
* @author hhcycj
*/
class  DealCouponViewModel extends ViewModel{
    public $viewFields = array(
        'DealCoupon'        =>array('id','sn', 'password', 'deal_id', 'begin_time', 'end_time', 'is_valid',  'is_delete',  'confirm_time',  'is_balance', 'refund_status','expire_refund','any_refund', 'coupon_price', 'coupon_score', 'coupon_money', '_type'=>'left'),
        'Supplier'          =>array('id'=>'supplier_id', 'name'=>'supplier_name', '_on'=>'Supplier.id=DealCoupon.supplier_id'),
        'DealOrderItem'     =>array('name'=>'deal_name', '_on'=>'DealOrderItem.id=DealCoupon.order_deal_id')
    );
}


