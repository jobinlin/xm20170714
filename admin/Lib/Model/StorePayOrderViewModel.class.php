<?php
/**
*
* @author hhcycj
*/
class  StorePayOrderViewModel extends ViewModel{
    public $viewFields = array(
    
        'StorePayOrder'=>array('id','order_sn','supplier_id', 'create_time' ,'pay_status', 'total_price', 'other_money', 'pay_amount', 
                      'discount_price', 'promote_ids', 'promote_data', 'after_sale', 'order_status', 'payment_id', 'location_id','exchange_money', 'user_id', 'user_mobile', '_type'=>'left'),
        
        'User'=>array('user_name', '_on'=>'StorePayOrder.user_id=User.id', '_type'=>'left'),
        
        'Supplier'=>array('name'=>'supplier_name', '_on'=>'StorePayOrder.supplier_id=Supplier.id', '_type'=>'left'),
        
        'SupplierLocation' => array('name'=>'supplier_location_name', '_on'=>'StorePayOrder.location_id=SupplierLocation.id', '_type'=>'left'),
        
        'Payment' => array('name'=>'payment_name', '_on'=>'StorePayOrder.payment_id=Payment.id', '_type'=>'left'),
    );
}


 