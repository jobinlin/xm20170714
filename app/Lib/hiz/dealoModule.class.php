<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class dealoModule extends HizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();

    }
	
    
	public function index()
	{	
		init_app_page();
		$s_account_info = $GLOBALS['hiz_account_info'];
		$account_id = $s_account_info['id'];

		$supplier_id = intval($_REQUEST['supplier_id']);
		$name = strim($_REQUEST['name']);
		$begin_time = strim($_REQUEST['begin_time']);
		$end_time = strim($_REQUEST['end_time']);
		
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i");
		$end_time_s = to_timespan($end_time,"Y-m-d H:i");
		
		/* 业务逻辑部分 */
		$conditions .= " where agency_id = ".$account_id; // 查询条件
		
		$supplier_list=$GLOBALS['db']->getAll(" select * from " . DB_PREFIX . "supplier".$conditions);
		$s_id=array();
		foreach($supplier_list as $k=>$v){
		    $s_id[]=$v['id'];
		}
	
		$GLOBALS['tmpl']->assign("supplier_list", $supplier_list);
		$condition = "";
		if($name!="")
		    $condition .=" and (doi.name like '%".$name."%' or doi.sub_name like '%".$name."%') ";
		if($begin_time_s)
		    $condition .=" and do.create_time > ".$begin_time_s." ";
		if($end_time_s)
		    $condition .=" and do.create_time < ".$end_time_s." ";
		if($supplier_id)
		    $condition .=" and doi.supplier_id = ".$supplier_id." ";
		
		$GLOBALS['tmpl']->assign("name",$name);
		$GLOBALS['tmpl']->assign("begin_time",$begin_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		$GLOBALS['tmpl']->assign("supplier_id",$supplier_id);
		
		//分页
		$page_size = 10;
		$page = intval($_REQUEST['p']);
		if($page==0) $page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$total=0;
		$list_total=array();
		foreach($s_id as $k=>$v){
		
		$supplier_id = $v;
		
		$location_list=$GLOBALS['db']->getAll("select id from ".DB_PREFIX."supplier_location where supplier_id=".$supplier_id);
		$s_account_info['location_ids']=array();
		foreach($location_list as $kk=>$vv){
		    $s_account_info['location_ids'][]=$vv['id'];
		    
		}	

	    $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
	    $order_table_name = get_supplier_order_table_name($supplier_id);
	    
	    $sql = "select distinct(doi.id),doi.*,s.name as supplier_name ,s.allow_refund, do.memo,do.create_time,do.order_sn,do.total_price,do.pay_amount,doi.refund_status from ".$order_item_table_name." as doi left join ".
	 	    	$order_table_name." as do on doi.order_id = do.id left join ".
	    		DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".
	    		"left join ".DB_PREFIX."supplier as s on doi.supplier_id=s.id ".
	    		" where l.location_id in (".implode(",",$s_account_info['location_ids']).") and do.is_delete = 0 and do.type = 0 and doi.is_shop = 0 and do.pay_status = 2 $condition order by doi.id desc limit ".$limit;
	    
	    $sql_count = "select count(distinct(doi.id)) from ".$order_item_table_name." as doi left join ".
	    		$order_table_name." as do on doi.order_id = do.id left join ".
	    		DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".
	    		" where l.location_id in (".implode(",",$s_account_info['location_ids']).") and do.is_delete = 0 and do.type = 0 and doi.is_shop = 0  and do.pay_status = 2 $condition ";
	    
	 
	    
	    $list = $GLOBALS['db']->getAll($sql);
	    if($list){
	    $list_total=array_merge($list_total,$list);
	    }
	    $total += intval($GLOBALS['db']->getOne($sql_count));
		}

	
		
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$list=array_sort($list_total,$keys='create_time',$type='desc');

	    foreach($list as $k=>$v){
	    	$uinfo = load_user($v['user_id']);
	    	$list[$k]['user_name']= $uinfo['user_name'];
	    	$mobile = $uinfo['mobile'];
	    	 	
	    	
	    	$list[$k]['user_mobile'] =  $mobile;
	    	
	    	$list[$k]['create_time'] = to_date($v['create_time']);
	    	$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
	    	$list[$k]['url'] = $deal_info['url'];
	    	$list[$k]['s_total_price'] = $v['balance_total_price'] + $v['add_balance_price_total'];

	    	
	    	$verify_count_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and confirm_time <> 0 and deal_type = 0");
	    	$verify_count_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and confirm_time <> 0 and deal_type = 1");
	    	$refund_status_1_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 1 and deal_type = 0");
	    	$refund_status_1_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 1 and deal_type = 1");
	    	$refund_status_2_0 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 2 and deal_type = 0");
	    	$refund_status_2_1 = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and refund_status = 2 and deal_type = 1");
	    	
	    	$list[$k]['verify_count'] = $verify_count_0 + $verify_count_1*$v['number'];
	    	$list[$k]['refund_status_1'] = $refund_status_1_0 + $refund_status_1_1*$v['number'];
	    	$list[$k]['refund_status_2'] = $refund_status_2_0 + $refund_status_2_1*$v['number'];
	    }
	    
	    
	    $page = new Page($total,$page_size);   //初始化分页对象
	    $p  =  $page->show();
	    $GLOBALS['tmpl']->assign('pages',$p);


	    $GLOBALS['tmpl']->assign("list",$list);
	    		
		
		$GLOBALS['tmpl']->assign("head_title","团购订单记录");
		$GLOBALS['tmpl']->display("pages/dealo/index.html");	
		

	
	}
	
	

}
?>