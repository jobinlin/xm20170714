<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class dcxorderModule extends BizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
        $this->check_auth();
    }
	
    
	public function index()
	{	
		init_app_page();
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);

		$order_list_sql = "select * from ".DB_PREFIX."dc_order where pay_status=1 and order_status=0 and is_delivery_cancel=1 and is_cancel=0 and location_id in (".implode(",",$s_account_info['location_ids']).") order by id desc";
		$order_list = $GLOBALS['db']->getAll($order_list_sql);
		
		$GLOBALS['tmpl']->assign("order_list",$order_list);
		 
		$GLOBALS['tmpl']->assign("ajax_url", url("biz","dcorder"));
		$GLOBALS['tmpl']->assign("head_title","异常外卖订单记录");
		$GLOBALS['tmpl']->display("pages/dcxorder/index.html");	
		

	
	}
	public function get_abnormal_order_num()
	{

	    $s_account_info = $GLOBALS['account_info'];
	    $supplier_id = intval($s_account_info['supplier_id']);
	
	    $order_count_sql = "select count(*) as num from ".DB_PREFIX."dc_order where pay_status=1 and order_status=0 and is_delivery_cancel=1 and is_cancel=0 and location_id in (".implode(",",$s_account_info['location_ids']).")";
	    $order_count = $GLOBALS['db']->getOne($order_count_sql);
	 
	    $root['status'] = 1;
	    $root['num'] = intval($order_count);
	    ajax_return($root);
	
	
	
	}

	
	
}
?>