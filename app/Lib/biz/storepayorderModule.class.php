<?php
/**
*
* @author hhcycj
*/
require APP_ROOT_PATH.'app/Lib/page.php';
class storepayorderModule extends BizBaseModule{
    public function __construct(){
        parent::__construct();
        global_run();
        $this->check_auth();
    }
    
    public function index(){
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        // 获取买单订单信息
        $this->get_store_pay_order();
        
//         // 获取商户优惠规则
//         $promote_sql  = "SELECT `id`,`name` FROM `".DB_PREFIX."promote` WHERE `supplier_id`={$supplier_id}";
//         $promote_info = $GLOBALS['db']->getAll($promote_sql);
//         $GLOBALS['tmpl']->assign("promote_info", $promote_info);
        
        $GLOBALS['tmpl']->assign("head_title","到店付管理");
		$GLOBALS['tmpl']->display("pages/storepayorder/index.html");
    }
    
    public function detail(){
        init_app_page();
        
        $id = intval($_REQUEST['id']);
        $where[] = "StorePayOrder.id =".$id;
        $where[] = "StorePayOrder.is_delete = 0";
        $where_str = join(' and ', $where);
        $where_str = ' WHERE '.$where_str;
        $sql = "SELECT
                	StorePayOrder.id AS id,
                	StorePayOrder.order_sn AS order_sn,
                	StorePayOrder.supplier_id AS supplier_id,
                	StorePayOrder.create_time AS create_time,
                	StorePayOrder.pay_status AS pay_status,
                	StorePayOrder.total_price AS total_price,
                    StorePayOrder.other_money AS other_money,
                	StorePayOrder.pay_amount AS pay_amount,
                	StorePayOrder.discount_price AS discount_price,
					StorePayOrder.exchange_money AS exchange_money,
                	StorePayOrder.promote_ids AS promote_ids,
                    StorePayOrder.promote_data AS promote_data,
        			StorePayOrder.promote AS promote,
                	StorePayOrder.after_sale AS after_sale,
                	StorePayOrder.order_status AS order_status,
                	StorePayOrder.payment_id AS payment_id,
                	StorePayOrder.location_id AS location_id,
                	StorePayOrder.user_id AS user_id,
                	StorePayOrder.user_mobile AS user_mobile,
                	USER.user_name AS user_name,
                	SupplierLocation. NAME AS supplier_location_name,
                	Payment.NAME AS payment_name
                FROM
                    ".DB_PREFIX."store_pay_order StorePayOrder
                    left JOIN ".DB_PREFIX."user USER ON StorePayOrder.user_id = USER .id
                    left JOIN ".DB_PREFIX."supplier_location SupplierLocation ON StorePayOrder.location_id = SupplierLocation.id
                    left JOIN ".DB_PREFIX."payment Payment ON StorePayOrder.payment_id = Payment.id ".$where_str;
        
        $store_pay_order_info = $GLOBALS['db']->getRow($sql);
        $store_pay_order_info['promote']=unserialize($store_pay_order_info['promote']);
		$store_pay_order_info['discount_price']=$store_pay_order_info['discount_price']+$store_pay_order_info['exchange_money'];
        $GLOBALS['tmpl']->assign("store_pay_order_info", $store_pay_order_info);
        $GLOBALS['tmpl']->assign("head_title","订单详情");
        $GLOBALS['tmpl']->display("pages/storepayorder/order_info.html");
        
        
    }
    
    public function get_store_pay_order(){
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        
        $order_sn = strim($_REQUEST['order_sn']);
        $begin_time = strtotime(strim($_REQUEST['begin_time']));
        $end_time   = strtotime(strim($_REQUEST['end_time'])) ? strtotime(strim($_REQUEST['end_time'])) + 24*60*60 : false;
        
        $where =  '';
        if ($order_sn) {
            $where[] = "StorePayOrder.order_sn='{$order_sn}'";
        }
        
        if ($begin_time && $end_time) {
            $where[] = "StorePayOrder.create_time between {$begin_time} and {$end_time}";
        }else if($begin_time){
            $where[] = "StorePayOrder.create_time >= {$begin_time}";
        }else if($end_time){
            $where[] = "StorePayOrder.create_time <= {$end_time}";
        }
        $where[] = "StorePayOrder.supplier_id={$supplier_id}";
        $where[] = "StorePayOrder.is_delete = 0";
        $where[] = "StorePayOrder.pay_status=2";
        $where_str = join(' and ', $where);
        $where_str = ' WHERE '.$where_str;
       
        
       
       // $store_pay_order_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."store_pay_order $where order by create_time DESC");
         
        $sql = "SELECT
                	StorePayOrder.id AS id,
                	StorePayOrder.order_sn AS order_sn,
                	StorePayOrder.supplier_id AS supplier_id,
                	StorePayOrder.create_time AS create_time,
                	StorePayOrder.pay_status AS pay_status,
                	StorePayOrder.total_price AS total_price,
                    StorePayOrder.other_money AS other_money,
                	StorePayOrder.pay_amount AS pay_amount,
                	StorePayOrder.discount_price AS discount_price,
					StorePayOrder.exchange_money AS exchange_money,
                	StorePayOrder.promote_ids AS promote_ids,
                	StorePayOrder.after_sale AS after_sale,
                	StorePayOrder.order_status AS order_status,
                	StorePayOrder.payment_id AS payment_id,
                	StorePayOrder.location_id AS location_id,
                	StorePayOrder.user_id AS user_id,
                	StorePayOrder.user_mobile AS user_mobile,
                	USER .user_name AS user_name,
                	SupplierLocation. NAME AS supplier_location_name,
                	Payment. NAME AS payment_name
                FROM
                    ".DB_PREFIX."store_pay_order StorePayOrder
                    left JOIN ".DB_PREFIX."user USER ON StorePayOrder.user_id = USER .id
                    left JOIN ".DB_PREFIX."supplier_location SupplierLocation ON StorePayOrder.location_id = SupplierLocation.id
                    left JOIN ".DB_PREFIX."payment Payment ON StorePayOrder.payment_id = Payment.id ".$where_str;
        
        $sql_count = $base_sql = "SELECT
                	                   count(StorePayOrder.id) total,
                                       sum(StorePayOrder.total_price) total_price,
                                       sum(StorePayOrder.pay_amount) pay_amount
                                FROM
                                    ".DB_PREFIX."store_pay_order StorePayOrder
                                    left JOIN ".DB_PREFIX."user USER ON StorePayOrder.user_id = USER .id
                                    left JOIN ".DB_PREFIX."supplier Supplier ON StorePayOrder.supplier_id = Supplier.id
                                    left JOIN ".DB_PREFIX."supplier_location SupplierLocation ON StorePayOrder.location_id = SupplierLocation.id
                                    left JOIN ".DB_PREFIX."payment Payment ON StorePayOrder.payment_id = Payment.id ".$where_str;
        
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)  $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $count = $GLOBALS['db']->getRow($sql_count);
        
        $page = new Page($count['total'], $page_size); // 初始化分页对象
        
        
        //分页跳转的时候保证查询条件
        $parameter['order_sn'] = strim($_REQUEST['order_sn']);
        $parameter['begin_time'] = strim($_REQUEST['begin_time']);
        $parameter['end_time'] = strim($_REQUEST['end_time']);
        
        foreach ( $parameter as $key => $val ) {
            if ($val) {
                $page->parameter .= "&{$key}=" . urlencode ( $val ) . "&";
            }
        }
         
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);
        $store_pay_order_info = $GLOBALS['db']->getAll($sql." order by StorePayOrder.id desc limit " . $limit);
         
        foreach ($store_pay_order_info as $key=>$value){
			$store_pay_order_info[$key]['discount_price']=$value['discount_price']+$value['exchange_money'];
            // 0:未支付 2:全部付款
            switch ($value['pay_status']){
                case 0:
                    $store_pay_order_info[$key]['pay_status'] = '未付款';
                    break;
                case 2:
                    $store_pay_order_info[$key]['pay_status'] = '已付款';
                    break;
                    
            }
            
            
        }
      
        $GLOBALS['tmpl']->assign("count", $count);
        $GLOBALS['tmpl']->assign("order_sn", $order_sn);
        $GLOBALS['tmpl']->assign("begin_time", $_REQUEST['begin_time']);
        $GLOBALS['tmpl']->assign("end_time",   $_REQUEST['end_time']);
        $GLOBALS['tmpl']->assign("store_pay_order_info", $store_pay_order_info);
    }
    
    
}