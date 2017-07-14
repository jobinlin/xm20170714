<?php 
/**
 * 订单记录
 */
require APP_ROOT_PATH.'app/Lib/page.php';
require_once(APP_ROOT_PATH."system/model/user.php");
class dcallorderModule extends BizBaseModule
{
    
	function __construct()
	{
        parent::__construct();
        global_run();
        $this->check_auth();
    }
	
    
	public function index()
	{	//print_r($_REQUEST);die;
		init_app_page();
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);
		$tpl_id = intval($_REQUEST['tpl_id']);
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$sn = strim($_REQUEST['sn']);
		$l_id = strim($_REQUEST['l_id']);
		$rs = intval($_REQUEST['rs']);
		$condition = "";
		if($sn!="")
			$condition .=" and (order_sn like '%".$sn."%' or mobile like '%".$sn."%') ";
	
		//	print_r($pay_type);die;
		$GLOBALS['tmpl']->assign("sn",$sn);
			
			$condition .=" and ((pay_status=1 and payment_id=0 ) or payment_id=1 ) and confirm_status=0 and is_cancel=0 ";
			
		if($rs>0){
			
			if($rs==1)
			$condition .=" and is_rs=1 ";
			if($rs==2)
			$condition .=" and is_rs=0 ";	
			
		}	
		if($l_id!="")
			$condition .=" and location_id = ".$l_id." ";
	    //分页
	    $page_size = 8;
	    $page = intval($_REQUEST['p']);
	    if($page==0) $page = 1;
	    $limit = (($page-1)*$page_size).",".$page_size;
	    
	    $sql = "select * from ".DB_PREFIX."dc_order where location_id in (".implode(",",$s_account_info['location_ids']).") and type_del = 0 $condition order by id desc limit ".$limit;
	    $sql_count = "select count(id) from ".DB_PREFIX."dc_order where location_id in (".implode(",",$s_account_info['location_ids']).") and type_del = 0 $condition ";
	    $list = $GLOBALS['db']->getAll($sql);
	    $total = $GLOBALS['db']->getOne($sql_count);
		$count=count($list);
	    foreach($list as $k=>$v){
	    
			if($v['is_rs']==0){
			$order_menu=unserialize($v['order_menu']);
	    	$order_promote=unserialize($v['promote_str']);
	    	$list[$k]['order_promote']=$order_promote;
	    	
	    	if($v['order_delivery_time']==1){
	    		//立即送达，商户从下单时间后4小时，可以确认订单
	    		$list[$k]['over_time']=$v['create_time']+3600*4;
	    	}elseif($v['order_delivery_time']>1){
	    		//有具体配送时间，商户从送达时间后4小时，可以确认订单
	    		$list[$k]['over_time']=$v['order_delivery_time']+3600*4;
	    	}
	    	$list[$k]['now']=NOW_TIME;
	    	$list[$k]['create_time'] = to_date($v['create_time']);
	  
			$m_cart_list=$order_menu['menu_list'];
	    	$list[$k]['m_cart_list']=$m_cart_list;
	    	$list[$k]['pay_price']=$v['total_price']-$v['ecv_money']-$v['promote_amount'];

				
			}else{
				
				
			$list[$k]['sort']=$count-$k;
	    	$order_menu=unserialize($v['order_menu']);
	    	$order_promote=unserialize($v['promote_str']);
	    	$list[$k]['order_promote']=$order_promote;
	    	
	    	$list[$k]['create_time'] = to_date($v['create_time']);

	    	$list[$k]['m_cart_list']=$order_menu;
		
			}
			$list[$k]['order_info']=get_dc_delivery_info($v['id']);
	    	$list[$k]['index']=$total-$k-$page_size*($page-1);	
	    	
	    }
	    
	    $page = new Page($total,$page_size);   //初始化分页对象
	    $p  =  $page->show();
	    $GLOBALS['tmpl']->assign('pages',$p);
		
		$sid_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id." and is_effect = 1");
	    $id_count = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id." and is_effect = 1");
		$is_voice = $GLOBALS['db']->getOne("select is_voice from ".DB_PREFIX."supplier where id =".$supplier_id);
	 	$GLOBALS['tmpl']->assign("is_voice",$is_voice);
	 	$GLOBALS['tmpl']->assign("l_id",$l_id);
	 	$GLOBALS['tmpl']->assign("total",$total);
	    $GLOBALS['tmpl']->assign("sid_list",$sid_list);
	    $GLOBALS['tmpl']->assign("id_count",$id_count);
	    $GLOBALS['tmpl']->assign("list",$list);
	    $GLOBALS['tmpl']->assign("rs",$rs); 		
		 $GLOBALS['tmpl']->assign("ajax_url", url("biz","dcorder"));
		 $GLOBALS['tmpl']->assign("ajax_url2", url("biz","dcresorder"));
		 $GLOBALS['tmpl']->assign("ajax_urlb", url("biz","dcrallorder"));
		$GLOBALS['tmpl']->assign("head_title","新订单");
		if($tpl_id==2){
			$result['is_voice']=$is_voice;
			$result['refresh_time']=intval(app_conf("REFRESH_TIME"));
			$result['count']=$total;
			$result['html']=$GLOBALS['tmpl']->fetch("pages/dcallorder/all_order_list.html");
			ajax_return($result);
		}else{
				
			$GLOBALS['tmpl']->display("pages/dcallorder/index.html");
		}
			

	}
	
	
	public function audio_play()
	{
		
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);

		require_once(APP_ROOT_PATH."system/model/dc.php");

		
		$sn = strim($_REQUEST['sn']);
		$l_id = strim($_REQUEST['l_id']);
		$rs = intval($_REQUEST['rs']);
		
		$condition .=" and ((pay_status=1 and payment_id=0 ) or payment_id=1 ) and confirm_status=0 and is_cancel=0 ";
	    
	    $sql = "select * from ".DB_PREFIX."dc_order where location_id in (".implode(",",$s_account_info['location_ids']).") and type_del = 0 $condition ";
	    $sql_count = "select count(id) from ".DB_PREFIX."dc_order where location_id in (".implode(",",$s_account_info['location_ids']).") and type_del = 0 $condition ";
	    $list = $GLOBALS['db']->getAll($sql);
	    $total = $GLOBALS['db']->getOne($sql_count);
		$count=count($list);
	    foreach($list as $k=>$v){
	    
			if($v['is_rs']==0){
			$order_menu=unserialize($v['order_menu']);
	    	$order_promote=unserialize($v['promote_str']);
	    	$list[$k]['order_promote']=$order_promote;
	    	
	    	if($v['order_delivery_time']==1){
	    		//立即送达，商户从下单时间后4小时，可以确认订单
	    		$list[$k]['over_time']=$v['create_time']+3600*4;
	    	}elseif($v['order_delivery_time']>1){
	    		//有具体配送时间，商户从送达时间后4小时，可以确认订单
	    		$list[$k]['over_time']=$v['order_delivery_time']+3600*4;
	    	}
	    	$list[$k]['now']=NOW_TIME;
	    	$list[$k]['create_time'] = to_date($v['create_time']);
	  
			$m_cart_list=$order_menu['menu_list'];
	    	$list[$k]['m_cart_list']=$m_cart_list;
	    	$list[$k]['pay_price']=$v['total_price']-$v['ecv_money']-$v['promote_amount'];
	    
				
			}else{
				
				
			$list[$k]['sort']=$count-$k;
	    	$order_menu=unserialize($v['order_menu']);
	    	$order_promote=unserialize($v['promote_str']);
	    	$list[$k]['order_promote']=$order_promote;
	    	
	    	$list[$k]['create_time'] = to_date($v['create_time']);

	    	$list[$k]['m_cart_list']=$order_menu;
		
			}
	
	    	
	    }
	    $GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->display("pages/dcallorder/order_audio.html");
		
	
	}
	
	
	
	
	
	public function voice_switch(){
		
		$s_account_info = $GLOBALS['account_info'];
		$supplier_id = intval($s_account_info['supplier_id']);
		$sql = "select is_voice from ".DB_PREFIX."supplier where id =".$supplier_id;
		$is_voice = $GLOBALS['db']->getOne($sql);
		if($is_voice==1){
			
			$GLOBALS['db']->query("update ".DB_PREFIX."supplier set is_voice = 0 where id = ".$supplier_id);
			$result=0;
			ajax_return($result);
			
		}else{
			
			$GLOBALS['db']->query("update ".DB_PREFIX."supplier set is_voice = 1 where id = ".$supplier_id);
			$result=1;
			ajax_return($result);
		}
	
		
	}
	

}
?>