<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

if (!defined('THINK_PATH')) exit();

//过滤请求
filter_request($_REQUEST);
filter_request($_GET);
filter_request($_POST);
define("AUTH_NOT_LOGIN", 1); //未登录的常量
define("AUTH_NOT_AUTH", 2);  //未授权常量

// 全站公共函数库
// 更改系统配置, 当更改数据库配置时为永久性修改， 修改配置文档中配置为临时修改
function conf($name,$value = false)
{
	if($value === false)
	{
		return C($name);
	}
	else
	{
		if(M("Conf")->where("is_effect=1 and name='".$name."'")->count()>0)
		{
			if(in_array($name,array('EXPIRED_TIME','SUBMIT_DELAY','SEND_SPAN','WATER_ALPHA','MAX_IMAGE_SIZE','INDEX_LEFT_STORE','INDEX_LEFT_TUAN','INDEX_LEFT_YOUHUI','INDEX_LEFT_DAIJIN','INDEX_LEFT_EVENT','INDEX_RIGHT_STORE','INDEX_RIGHT_TUAN','INDEX_RIGHT_YOUHUI','INDEX_RIGHT_DAIJIN','INDEX_RIGHT_EVENT','SIDE_DEAL_COUNT','DEAL_PAGE_SIZE','PAGE_SIZE','BATCH_PAGE_SIZE','HELP_CATE_LIMIT','HELP_ITEM_LIMIT','REC_HOT_LIMIT','REC_NEW_LIMIT','REC_BEST_LIMIT','REC_CATE_GOODS_LIMIT','SALE_LIST','INDEX_NOTICE_COUNT','RELATE_GOODS_LIMIT')))
			{
				$value = intval($value);
			}
			M("Conf")->where("is_effect=1 and name='".$name."'")->setField("value",$value);
		}
		C($name,$value);
	}
}



function write_timezone($zone='')
{
	if($zone=='')
	$zone = conf('TIME_ZONE');
		$var = array(
			'0'	=>	'UTC',
			'8'	=>	'PRC',
		);
		
		//开始将$db_config写入配置
	    $timezone_config_str 	 = 	"<?php\r\n";
	    $timezone_config_str	.=	"return array(\r\n";
	    $timezone_config_str.="'DEFAULT_TIMEZONE'=>'".$var[$zone]."',\r\n";
	    
	    $timezone_config_str.=");\r\n";
	    $timezone_config_str.="?>";
	   
	    @file_put_contents(get_real_path()."public/timezone_config.php",$timezone_config_str);
}



//后台日志记录
function save_log($msg,$status)
{
	if(conf("ADMIN_LOG")==1)
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$log_data['log_info'] = $msg;
		$log_data['log_time'] = NOW_TIME;
		$log_data['log_admin'] = intval($adm_session['adm_id']);
		$log_data['log_ip']	= CLIENT_IP;
		$log_data['log_status'] = $status;	
		$log_data['module']	=	MODULE_NAME;
		$log_data['action'] = 	ACTION_NAME;
		M("Log")->add($log_data);
	}
}


//状态的显示
function get_toogle_status($tag,$id,$field)
{
	if($tag)
	{
		return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("YES")."</span>";
	}
	else
	{
		return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("NO")."</span>";
	}
}

//状态的显示
function get_is_effect($tag,$id)
{
	if($tag)
	{
		return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_1")."</span>";
	}
	else
	{
		return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_0")."</span>";
	}
}

//状态审核的显示
function get_is_verify($tag,$id)
{
	if($tag)
	{
		return "<span class='is_effect' onclick='set_verify(".$id.",this);'>".l("IS_EFFECT_1")."</span>";
	}
	else
	{
		return "<span class='is_effect' onclick='set_verify(".$id.",this);'>".l("IS_EFFECT_0")."</span>";
	}
}


//排序显示
function get_sort($sort,$id)
{
	if($tag)
	{
		return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>";
	}
	else
	{
		return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>";
	}
}
function get_nav($nav_id)
{
	return M("RoleNav")->where("id=".$nav_id)->getField("name");	
}
function get_module($module_id)
{
	return M("RoleModule")->where("id=".$module_id)->getField("module");
}
function get_group($group_id)
{
	if($group_data = M("RoleGroup")->where("id=".$group_id)->find())
	$group_name = $group_data['name'];
	else
	$group_name = L("SYSTEM_NODE");
	return $group_name;
}
function get_role_name($role_id)
{
	return M("Role")->where("id=".$role_id)->getField("name");
}
function get_admin_name($admin_id)
{
	$adm_name = M("Admin")->where("id=".$admin_id)->getField("adm_name");
	if($adm_name)
	return $adm_name;
	else
	return l("NONE_ADMIN_NAME");
}
function get_log_status($status)
{
	return l("LOG_STATUS_".$status);
}
//验证相关的函数
//验证排序字段
function check_sort($sort)
{
	if(!is_numeric($sort))
	{
		return false;
	}
	return true;
}
function check_empty($data)
{
	if(strim($data)=='')
	{
		return false;
	}
	return true;
}

function set_default($null,$adm_id)
{

	$admin_name = M("Admin")->where("id=".$adm_id)->getField("adm_name");
	if($admin_name == conf("DEFAULT_ADMIN"))
	{
		return "<span style='color:#f30;'>".l("DEFAULT_ADMIN")."</span>";
	}
	else
	{
		return "<a href='".u("Admin/set_default",array("id"=>$adm_id))."'>".l("SET_DEFAULT_ADMIN")."</a>";
	}
}
function get_order_sn($order_id)
{
	return M("DealOrder")->where("id=".$order_id)->getField("order_sn");
}
function get_order_sn_with_link($order_id, $notice)
{
	$func = array(
	    1=>'incharge_index',
	    2=>'scoresOrder',
	    3=>'selfOrder',
	    4=>'selfDakOrder',
	    5=>'tuanOrder',
	    6=>'shopOrder',
	);
	 
	$order = unserialize($notice['sub_order_data']);
	 
	foreach ($order as $val){
	    $type = $val['type']; 
	    $order_sn[] ="<a href='".u("DealOrder/{$func[$type]}",array("order_sn"=>$val['order_sn'], 'type'=>$type))."'>".$val['order_sn']."</a>";
	}
	
	$order_sn = join('<br />&nbsp;', $order_sn);
	return $order_sn;
	
}

function get_store_pay_order_sn_with_link($order_id)
{
    $order_info = M("StorePayOrder")->where("id=".$order_id)->find();
    if($order_info['type']==0)
        $str = l("DEAL_ORDER_TYPE_2")."：<a href='".u("StorePayOrder/index",array("order_sn"=>$order_info['order_sn']))."'>".$order_info['order_sn']."</a>";
    if($order_info['is_delete']==1)
        $str ="<span style='text-decoration:line-through;'>".$str."</span>";
    return $str;
}

function get_user_name($user_id)
{
	$user_name =  M("User")->where("id=".$user_id." and is_delete = 0")->getField("user_name");
	
	if(!$user_name)
	return l("NO_USER");
	else
	return "<a href='".u("User/index",array("user_name"=>$user_name))."'>".$user_name."</a>";
	
	
}
function get_user_name_js($user_id)
{
	$user_name =  M("User")->where("id=".$user_id." and is_delete = 0")->getField("user_name");
	
	if(!$user_name)
	return l("NO_USER");
	else
	return "<a href='javascript:void(0);' onclick='account(".$user_id.")'>".$user_name."</a>";
	
	
}
function get_pay_status($status)
{
	return L("PAY_STATUS_".$status);
}
function get_delivery_status($status,$order_info)
{
   /*  if($order_info['is_delete'] == 1 || $order_info['order_status'] == 1){
       return '-';
    } */
    
    // 团购和无需发货的订单，没有发货状态, 结单的订单, 未付款的订单
    if ($order_info['type'] == 5 || $order_info['order_status'] == 1 || $order_info['pay_status'] != 2) {
        return '-';
    }
   
	$status_array = array(
	    '0' => '<span style="color:red">待发货</span>',
	    '1' => '<span style="color:blue">部份发货</span>',
	    '2' => '全部发货',
	    '5' => '<span style="color:red">无需发货</span>',
	);
	return $status_array[$status];
}

function get_deal_item_delivery_status($status, $item_info)
{
    
    if ($status == 0){
        $str = '未发货';
    }elseif ($status == 1){
        $express = $GLOBALS['db']->getRow("select e.name express_name, dn.notice_sn from ".DB_PREFIX."delivery_notice dn left join ".DB_PREFIX."express e on dn.express_id=e.id  where dn.order_item_id = {$item_info['id']} ");
        
        if ($express['express_name']) {
            $str = "已发货（{$express['express_name']}：<span style=\"color:red;\">{$express['notice_sn']}</span>）";
        }else {
            $str = '已发货';
        }
    }elseif ($status == 5){
        $str = '无需发货';
    }
    
    return $str;
}

function get_coupon_refund_status($refund_status, $coupon_item){
    
    if ($coupon_item['is_delete']) {
        return '交易关闭';
    }
    
    
    // 待评价
    if ($coupon_item['confirm_time'] > 0) {
        $dp_id = $GLOBALS['db']->getOne("select dp_id from ".DB_PREFIX."deal_order_item where id = ".$coupon_item['order_deal_id']);
        if ($dp_id > 0) {
            return '已完成';
        }else {
            return '待评价';
        }
        
    }
    
    if ( $coupon_item['end_time'] < NOW_TIME && $coupon_item['end_time'] != 0 && $refund_status != 1 && $refund_status != 2 ) {
        return '已过期';
    }
    
    if ( $coupon_item['begin_time'] > NOW_TIME && $coupon_item['begin_time'] != 0 ) {
        return '未开始';
    }
    
    switch ($refund_status) {
        case 0:
            $str = '待使用';
            break;
            
        case 1:
            $str = '申请退款';
            break;
            
        case 2:
            $str = '已退款';
            break;
            
        case 3:
            $str = '拒绝退款';
            break;
        
        default:
            break;
    }
    
    
    
    return $str;
}

/**
 * 获取xiao操作
 * @param unknown $pay_status 订单支付状态
 * @param unknown $item_info  订单商品信息
 * @return string
 */
function get_coupon_operate($refund_status, $coupon_item){
    
    $refund_status_str = get_coupon_refund_status($refund_status, $coupon_item);
    
    if ( $coupon_item['confirm_time'] == 0 && $refund_status != 2 && $refund_status != 1 && $refund_status_str != '已过期') {
        $url = U("DealOrder/do_verify",array("coupon_id"=>$coupon_item['id']));
        $str = "<a href=\"javascript:void(0);\" class=\"do_verify\"  action=\"{$url}\">管理员验证消费</a>";
    }
    
    // 只要不是已经退款，已拒绝的都可以退款, 且未消费
    if ( ($refund_status == 0 || $refund_status == 1) && $coupon_item['confirm_time'] == 0 && $coupon_item['is_delete'] != 1 ) {
        $str .= $str ? '<br />' : '';
        $url = U("DealOrder/refund", array("coupon_id"=>$coupon_item['id']));
        $str .= "<a href=\"javascript:void(0);\" class=\"do_refund\" action=\"{$url}\">退款</a>";
    }
    $str = $str ? $str : '-';
    return $str;
    
}

/**
 * 获取订单中的商品 操作
 * @param unknown $pay_status 订单支付状态
 * @param unknown $item_info  订单商品信息
 * @return string
 */
function get_deal_operate($pay_status, $item_info){
    
    $str = '';
    
    $status_str = get_deal_status($pay_status, $item_info);

    
    // 如果是自提，需要消费券验证
    if ($item_info['is_pick'] == 1) {
        $coupon_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where order_deal_id = ".$item_info['id']);
       
        if ( $status_str == '待自提' || $status_str == '拒绝退款') {
            $url = U("DealOrder/do_verify",array("coupon_id"=>$coupon_item['id']));
            $str = "<a href=\"javascript:void(0);\" class=\"do_verify\"  action=\"{$url}\">管理员验证消费</a>";
        }
        
        if ($status_str == '已完成' || $status_str == '待评价' ) {
            return '-';
        }
    }
    
    $order_info = M("DealOrder")->where("id={$item_info['order_id']}")->find();
    
    if($order_info['type'] !=4){
        switch ($status_str) {
            case '待发货':
                $url = U("DealOrder/delivery&order_id={$item_info['order_id']}&item_id={$item_info['id']}");
                $str .= "<a href=\"{$url}\"  >发货</a>";
                break;
            
            case '待收货':
                $url = U("DealOrder/do_verify&order_item_id={$item_info['id']}");
                $str .= "<a href=\"javascript:void(0);\" class=\"do_verify\" action=\"{$url}\">强制收货</a>";
                break;
            case '退款申请':
                if ($item_info['delivery_status'] == 0) {
                    $str = '-';
                }else{
                    $url = U("DealOrder/do_verify&order_item_id={$item_info['id']}");
                    $str .= "<a href=\"javascript:void(0);\" class=\"do_verify\" action=\"{$url}\">强制收货</a>";
                }
                break;
            default:
                $str = $str ? $str:'-';
                break;
        }
    }
   
    
     
    // 显示退款条件：商品 未收货、未退款； 订单 未关闭，已付款
    if ( $item_info['is_arrival'] !=1 && $item_info['refund_status'] < 2 && $order_info['is_delete'] != 1 && $order_info['pay_status'] != 0) {
        $url = U("DealOrder/refund&order_item_id={$item_info['id']}");
        if ( $str != '' && $str != '-') {
            $str .= "<br /><a href=\"javascript:void(0);\" class=\"do_refund\" action=\"{$url}\" >退款</a>";
        }else{
            $str = "<a href=\"javascript:void(0);\" class=\"do_refund\" action=\"{$url}\" >退款</a>";
        }
    }
    
    return $str;
}

/**
 * 获取积分订单中的商品 操作
 * @param unknown $pay_status 订单支付状态
 * @param unknown $item_info  订单商品信息
 * @return string
 */
function get_distribution_deal_operate($pay_status, $item_info){
    $status_str = get_deal_status($pay_status, $item_info);
    $str = '';
    switch ($status_str) {
        case '待发货':
            $url = U("DistributionOrder/delivery&order_id={$item_info['order_id']}&item_id={$item_info['id']}");
            $str = "<a href=\"{$url}\"  >发货</a>";
            break;

        case '待收货':
            $url = U("DistributionOrder/do_verify&order_item_id={$item_info['id']}");
            $str = "<a href=\"javascript:void(0);\" class=\"do_verify\" action=\"{$url}\">强制收货</a>";
            break;
        default:
            $str = '-';
            break;
    }

    $order_info = M("DealOrder")->where("id={$item_info['order_id']}")->find();
     
    // 显示退款条件：商品 未收货、未退款； 订单 未关闭，已付款
    if ( $item_info['is_arrival'] !=1 && $item_info['refund_status'] < 2 && $order_info['is_delete'] != 1 && $order_info['pay_status'] != 0) {
        $url = U("DistributionOrder/refund&order_item_id={$item_info['id']}");
        if ( $str != '' && $str != '-') {
            $str .= "<br /><a href=\"javascript:void(0);\" class=\"do_refund\" action=\"{$url}\" >退款</a>";
        }else{
            $str = "<a href=\"javascript:void(0);\" class=\"do_refund\" action=\"{$url}\" >退款</a>";
        }
    }

    return $str;
}

/**
 * 获取订单中的商品状态
 * @param unknown $pay_status 订单支付状态
 * @param unknown $item_info  订单商品信息
 * @return string
 */
function get_deal_status($pay_status, $item_info){
/**
按这个顺序，从上往下逐一判断
   未付款     ===> 待付款   deal_order 表 pay_status
    已评价     ===> 完成
    已收货     ===> 待评价   `dp_id` int(11) NOT NULL COMMENT '为该商品点评的ID',
    已发货     ===> 待收货   `is_arrival` tinyint(1) NOT NULL COMMENT '是否已收货0:未收货1:已收货2:没收到货',
    已退款     ===> 已退款 `is_refund` tinyint(1) NOT NULL COMMENT '是否支持退款(由商品表同步而来)',
    申请退款 ====> 申请退款  `refund_status` tinyint(1) NOT NULL COMMENT '退款状态 0:无 1:用户申请退款 2:已确认 3:拒绝退款',
    
    未发货     ===> 待发货   `delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:已发货 5.无需发货',
   
*/
    
    if ($item_info['is_pick'] == 1) {
        $coupon_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where order_deal_id = ".$item_info['id']);
        $str = get_coupon_refund_status($coupon_item['refund_status'], $coupon_item);
        return $str = $str == '待使用' ? '待自提' : $str;
    }
    
    if($pay_status != 2){
        return '待付款';
    }
    
    // 已评价
    if( $item_info['dp_id'] > 0 ){
        return '已完成';
    } 
    
    // 已收货，或者已验证
    if( $item_info['is_arrival'] == 1 || $item_info['consume_count'] > 0){
        return '待评价';
    }
    
    if($item_info['refund_status'] == 1){
        return '退款申请';
    }else if($item_info['refund_status'] == 2){
        return '已退款';
    }
    
    //delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:已发货 5.无需发货',
    if ( $item_info['delivery_status'] == 0 ) {
        return '待发货';
    }
   
    
    // 已发货 且 未收货
    if( $item_info['delivery_status'] == 1 ){
        return '待收货';
    }
    
    
    return '-';
     
}




function get_order_status($s,$order_info)
{
    // 交易关闭 必须是为结单的
    if ($order_info['is_delete'] && $order_info['pay_status'] != 2 ) {
        return '交易关闭';
    }
    
    if ( $order_info['pay_status'] == 0 || $order_info['pay_status'] == 1) {
        return '待付款';
    }
    
    
    
	if($s){
		$msg = "已结单";
	}else{
	    $msg = "<span style=\"color:red;\">待结单</span>";
	}
	 
	return $msg;
}



function get_city($region_lv2, $order_info){
    if ($order_info['type'] == 5) { // 团购订单的城市按商户城市
        $city = $GLOBALS['db']->getRow("select name, pid from ".DB_PREFIX."deal_city where id = ".$order_info['city_id']);
        $p_city = $GLOBALS['db']->getRow("select name, pid from ".DB_PREFIX."deal_city where id = ".$city['pid']);
        $str = $p_city['name'].$city['name'];
    }else{
        $str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv2']);
        $str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv3']);
        $str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$order_info['region_lv4']);
    }
    
    if (!$str) {
        $str = '-';
    }
    return $str;
}

function get_order_status_csv($s,$order_info)
{
    if ($order_info['is_delete'] && $order_info['order_status'] != 1) {
        return '交易关闭';
    }
    
    if ( $order_info['pay_status'] == 0 || $order_info['pay_status'] == 1) {
        return '待付款';
    }
    
    if($s){
        $msg = "已结单";
    }else{
        $msg = "待结单";
    }
    
    return $msg;
}
function get_notice_info($sn,$notice_id)
{
		$express_name = M()->query("select e.name as ename from ".DB_PREFIX."express as e left join ".DB_PREFIX."delivery_notice as dn on dn.express_id = e.id where dn.id = ".$notice_id);
		$express_name = $express_name[0]['ename'];
		if($express_name)
		$str = $express_name."<br/>".$sn;
		else 
		$str = $sn;
		return $str;
}

function get_order_payment_name($order_id)
{
    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
    $rel='';
	if ( $order_info['pay_amount'] > 0 && $order_info['payment_id'] == 0 ) {
        return '余额支付';
    }elseif($order_info['cod_mode']!=''&&$order_info['cod_money']>0){
		$rel=get_payment_name_rel($order_info['cod_mode']);
	}
    return M("Payment")->where("id=".$order_info['payment_id'])->getField("name").$rel;
}

function get_payment_name($payment_id,$payment_config)
{
	$payment_info=M("Payment")->where("id=".$payment_id)->field('name,class_name')->find();
	$rel='';
	if($payment_info['class_name']=='Cod'){
		$rel=get_payment_name_rel('',$payment_config);
	}
	return $payment_info['name'].$rel;
}
function get_delivery_name($delivery_id)
{
	return M("Delivery")->where("id=".$delivery_id)->getField("name");
}
function get_region_name($region_id)
{
	return M("DeliveryRegion")->where("id=".$region_id)->getField("name");
}
function get_city_name($id)
{
	return M("DealCity")->where("id=".$id)->getField("name");
}
function get_message_is_effect($status)
{
	return $status==1?l("YES"):l("NO");
}
function get_message_type($type_name,$rel_id)
{
	$show_name = M("MessageType")->where("type_name='".$type_name."'")->getField("show_name");
	if($type_name=='deal_order')
	{
		$order_sn = M("DealOrder")->where("id=".$rel_id)->getField("order_sn");
		if($order_sn)
		return "[".$order_sn."] <a href='".u("DealOrder/deal_index",array("id"=>$rel_id))."'>".$show_name."</a>";
		else
		return $show_name;
	}
	elseif($type_name=='deal')
	{
		$sub_name = M("Deal")->where("id=".$rel_id)->getField("sub_name");
		if($sub_name)
		return "[".$sub_name."]" .$show_name;
		else
		return $show_name;
	}
	elseif($type_name=='supplier')
	{
		$name = M("Supplier")->where("id=".$rel_id)->getField("name");
		if($name)
		return "[".$name."] <a href='".u("Supplier/index",array("id"=>$rel_id))."'>".$show_name."</a>";
		else
		return $show_name;
	}
	else
	{
		if($show_name)
		return $show_name;
		else
		return $type_name;
	}
}

function get_send_status($status)
{
	return L("SEND_STATUS_".$status);
}
function get_send_mail_type($deal_id)
{
	if($deal_id>0)
	return l("DEAL_NOTICE");
	else 
	return l("COMMON_NOTICE");
}
function get_send_type($send_type)
{
	return l("SEND_TYPE_".$send_type);
}

function get_all_files( $path )
{
		$list = array();
		$dir = @opendir($path);
	    while (false !== ($file = @readdir($dir)))
	    {
	    	if($file!='.'&&$file!='..')
	    	if( is_dir( $path.$file."/" ) ){
	         	$list = array_merge( $list , get_all_files( $path.$file."/" ) );
	        }
	        else 
	        {
	        	$list[] = $path.$file;
	        }
	    }
	    @closedir($dir);
	    return $list;
}
function get_order_item_name($id)
{
	return M("DealOrderItem")->where("id=".$id)->getField("name");
}
function get_supplier_name($id)
{
	return M("Supplier")->where("id=".$id)->getField("name");
}


function get_is_store_payment($id){
    return M('Supplier')->where('id='.$id)->getField('is_store_payment');

}

function get_location_name($id){
		return M('SupplierLocation')->where('id='.$id)->getField('name');
	
}
	
function get_send_type_msg($status)
{
	//发送类型 0:短信 1:邮件;2:微信;3:andoird;4:ios
	if($status==0)
	{
		return l("SMS_SEND");
	}
	elseif($status==2)
	{
		return '微信';
	}
	elseif($status==3)
	{
		return 'andorid';
	}
	elseif($status==4)
	{
		return 'ios';
	}		
	else 
	{
		return l("MAIL_SEND");
	}
}

function show_content($content,$id)
{
	return "<a title='".l("VIEW")."' href='javascript:void(0);' onclick='show_content(".$id.")'>".l("VIEW")."</a>";
}



function get_is_send($is_send)
{
	if($is_send==0)
	return L("NO");
	else
	return L("YES");
}
function get_send_result($result)
{
	if($result==0)
	{
		return L("FAILED");
	}
	else
	{
		return L("SUCCESS");
	}
}

function get_is_buy($is_buy)
{
	return l("IS_BUY_".$is_buy);	
}

function get_point($point)
{
	return l("MESSAGE_POINT_".$point);
}

function get_status($status)
{
	if($status)
	{
		return l("YES");
	}
	else
	return l("NO");
}


function getMPageName($page)
{
	return L('MPAGE_'.strtoupper($page));
}

function getMTypeName($type,$item)
{

	$cfg = $GLOBALS['mobile_cfg'];

	$navs = null;
	foreach($cfg as $k=>$v)
	{
		if($v['mobile_type']==$item['mobile_type'])
		{
			$navs = $v['nav'];
			break;
		}
	}


	foreach($navs as $k=>$v)
	{
        if($item['zt_moban']=='index_zt3.html' or $item['zt_moban']=='index_zt6.html' ){
                return '空';
        }else{
            if($v['type']==$type)
            {
                return $v['name'];
            }
        }

	}
	
}
function get_submit_user($uid)
{
		if($uid==0)
		return "管理员发布";
		else
		{
			$uname = M("SupplierAccount")->where("id=".$uid)->getField("account_name");
			return $uname?$uname:"商家不存在";
		}
		
}
function get_event_cate_name($id)
	{
		return M("EventCate")->where("id=".$id)->getField("name");
	}
	
function show_table_substr($word,$cut=20)
{
	return "<span title='".$word."'>".msubstr($word,0,$cut)."</span>";
}

function get_balance_status($status)
{
	return l("BALANCE_".$status);
}

/**
 * 结算
 * @param unknown_type $rel_ids 结算的数据ID数组
 * @param unknown_type $deal_id 项目编号
 * @param memo 备注 
 */
function do_balance($rel_ids,$deal_id,$memo="")
{
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	$now = NOW_TIME;
	if($deal_info['is_coupon']==1)
	{
		$sql = "update ".DB_PREFIX."deal_coupon set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id in (".implode(",",$rel_ids).") and is_balance <> 2";
		$sql_amount = "select sum(balance_price)+sum(add_balance_price) from ".DB_PREFIX."deal_coupon where id in (".implode(",",$rel_ids).") and is_balance <> 2";
		$amount = $GLOBALS['db']->getOne($sql_amount);
		$GLOBALS['db']->query($sql);	
		
		//同步更新订单商品
		$sql_item = "select doi.* from ".DB_PREFIX."deal_order_item as doi where doi.id in(select distinct(dc.order_deal_id) as item_id from ".DB_PREFIX."deal_coupon as dc where dc.id in (".implode(",",$rel_ids)."))";
		$item_list = $GLOBALS['db']->getAll($sql_item);
		foreach($item_list as $k=>$v)
		{
			if($deal_info['deal_type']==1)
			{
				//按单
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2");
			}
			else
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and is_balance = 2")==$v['number'])
				{
					//全部	
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2");			
				}
				else
				{
					//部份
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 3,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2");			
				}
			}
		}		
	}
	else
	{
		$sql_amount = "select sum(balance_total_price)+sum(add_balance_price_total) from ".DB_PREFIX."deal_order_item where id in (".implode(",",$rel_ids).") and is_balance <> 2";
		$amount = $GLOBALS['db']->getOne($sql_amount);
		$sql = "update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id in (".implode(",",$rel_ids).") and is_balance <> 2";
		
		$GLOBALS['db']->query($sql);
		
	}
	supplier_money_log($deal_info['supplier_id'],$amount, $deal_info['sub_name']."结算 ".$memo);
}

/**
 * 弃用

 */
function supplier_money_log($supplier_id,$money,$info)
{
	if($money!=0)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."supplier set money = money +".$money." where id = ".$supplier_id);
		
		$log_info['log_info'] = $info;
		$log_info['create_time'] = NOW_TIME;
		$log_info['money'] = floatval($money);
		$log_info['supplier_id'] = $supplier_id;
	
		$GLOBALS['db']->autoExecute(DB_PREFIX."supplier_money_log",$log_info);

	}
}



function getMobileTypeName($type)
{
	$cfg = $GLOBALS['mobile_cfg'];
	foreach($cfg as $k=>$v)
	{
		if($v['mobile_type']==$type)
		{
			return $v['name'];
		}
	}
}

function msubstr_name($n)
{
	return msubstr($n,0,40);
	
}

/**
 * 分页处理
 * @param string $type 所在页面
 * @param array  $args 参数
 * @param int $total_count 总数
 * @param int $page 当前页
 * @param int $page_size 分页大小
 * @param string $url 自定义路径
 * @param int $offset 偏移量
 * @return array
 */
function buildPage($type,$args,$total_count,$page = 1,$page_size = 0,$url='',$offset = 5){
	$pager['total_count'] = intval($total_count);
	$pager['page'] = $page;
	$pager['page_size'] = ($page_size == 0) ? 20 : $page_size;
	/* page 总数 */
	$pager['page_count'] = ($pager['total_count'] > 0) ? ceil($pager['total_count'] / $pager['page_size']) : 1;

	/* 边界处理 */
	if ($pager['page'] > $pager['page_count'])
		$pager['page'] = $pager['page_count'];

	$pager['limit'] = ($pager['page'] - 1) * $pager['page_size'] . "," . $pager['page_size'];
	$page_prev  = ($pager['page'] > 1) ? $pager['page'] - 1 : 1;
	$page_next  = ($pager['page'] < $pager['page_count']) ? $pager['page'] + 1 : $pager['page_count'];
	$pager['prev_page'] = $page_prev;
	$pager['next_page'] = $page_next;

	if (!empty($url)){
		$pager['page_first'] = $url . 1;
		$pager['page_prev']  = $url . $page_prev;
		$pager['page_next']  = $url . $page_next;
		$pager['page_last']  = $url . $pager['page_count'];
	}
	else{
		$args['page'] = '_page_';
		if(!empty($type)){
			if(strpos($type,'javascript:') === false){
				//$page_url = JKU($type,$args);
			}else{
				$page_url = $type;
			}
		}else{
			$page_url = 'javascript:;';
		}
		$pager['page_first'] = str_replace('_page_',1,$page_url);
		$pager['page_prev']  = str_replace('_page_',$page_prev,$page_url);
		$pager['page_next']  = str_replace('_page_',$page_next,$page_url);
		$pager['page_last']  = str_replace('_page_',$pager['page_count'],$page_url);
	}
	$pager['page_nums'] = array();
	if($pager['page_count'] <= $offset * 2){
		for ($i=1; $i <= $pager['page_count']; $i++){
			$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
		}
	}else{
		if($pager['page'] - $offset < 2){
			$temp = $offset * 2;
			for ($i=1; $i<=$temp; $i++){
				$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
			}
			$pager['page_nums'][] = array('name'=>'...');
			$pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
		}else{
			$pager['page_nums'][] = array('name' => 1,'url' => empty($url) ? str_replace('_page_',1,$page_url) : $url . 1);
			$pager['page_nums'][] = array('name'=>'...');
			$start = $pager['page'] - $offset + 1;
			$end = $pager['page'] + $offset - 1;
			if($pager['page_count'] - $end > 1){
				for ($i=$start;$i<=$end;$i++){
					$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
				}

				$pager['page_nums'][] = array('name'=>'...');
				$pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
			}else{
				$start = $pager['page_count'] - $offset * 2 + 1;
				$end = $pager['page_count'];
				for ($i=$start;$i<=$end;$i++){
					$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
				}
			}
		}
	}
	return $pager;
}

function get_buy_count($buy_count,$deal)
{
    if(intval($deal['id'])==0){return 0;}
	$real_buy_count = intval($GLOBALS['db']->getOne("select buy_count from ".DB_PREFIX."deal_stock where deal_id = ".intval($deal['id'])));
	if($real_buy_count==$buy_count)
		return $buy_count;
	else
		return "虚拟".$buy_count.",真实".$real_buy_count;
}

/**
 * 此函数只针对 "小数点默认最多4位" 的格式化
 * @param unknown $price
 * @return string
 */
function format_price_floor($price){
    // 格式化数字为4个小数点，然后截取保留两位
    return sprintf("%.2f",substr(sprintf("%.4f", $price), 0, -2));
}
/**
 * 因为数据错误，需要执行的一些同步操作
 */
function syn_DeaLocationAndCateData(){
    //zx  deal商品表is_location字段表示商品有门店支持，团购只有存在支持的门店才能使用，因不知名原因出现门店存在支持门店但是此字段为0的情况,此时需要一个一个商品区编辑，在此加上一个同步sql
	$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal SET ".DB_PREFIX."deal.is_location = 1 WHERE (select count(*) from ".DB_PREFIX."deal_location_link  where ".DB_PREFIX."deal_location_link.deal_id=".DB_PREFIX."deal.id  )");
	/* $GLOBALS['db']->query("update ".DB_PREFIX."msg_box set data = ''"); */
	//zx 团购分类按新方式存储
	$sql = " SELECT id,cate_id FROM ".DB_PREFIX."deal where is_shop =0";
	$tuan_info= $GLOBALS['db']->getAll($sql);
	foreach($tuan_info as $v){
		$sql = "SELECT dct.cate_id FROM ".DB_PREFIX."deal_cate_type_deal_link dctdl LEFT JOIN ".DB_PREFIX."deal_cate_type_link dct on dctdl.deal_cate_type_id=dct.deal_cate_type_id where deal_id=".$v['id'];
		$cate_pid= $GLOBALS['db']->getAll($sql);
		if($v['cate_id']!=''){
			$cate_arr=explode(',',$v['cate_id']);
		}else{
			$cate_arr=array();
		}
		foreach($cate_pid as $vv){
			if($vv['cate_id']&&!in_array($vv['cate_id'],$cate_arr)){
				$cate_arr[]=$vv['cate_id'];
			}
		}
		if(count($cate_arr)>0){
			$cate_id_str=implode(',',$cate_arr);
		}else{
			$cate_id_str="";
		}
		if($cate_id_str!=$v['cate_id']){
			$GLOBALS['db']->query("update ".DB_PREFIX."deal set cate_id = '".$cate_id_str."' where id=".$v['id']);
		}
		syn_deal_match($v['id']);
	}
	$fx_vip_order= $GLOBALS['db']->getAll("select user_id,total_price,rebate,rebate_data from ".DB_PREFIX."fx_buy_order where pay_status=2");
	require_once(APP_ROOT_PATH."system/model/user.php");
	foreach($fx_vip_order as $v){
		$rebate_data=unserialize($v['rebate_data']);
		if($rebate_data['pid']>0){
			$p_user = load_user($rebate_data['pid']);
			if($p_user['fx_total_vip_money']==0){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user SET fx_total_vip_money = fx_total_vip_money+".$v['total_price']." where id=".$rebate_data['pid']);
			}
		}
	}
}
?>