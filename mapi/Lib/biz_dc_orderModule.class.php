<?php 
/**
 * 
 * 商家外卖
 * 
 */
require_once(APP_ROOT_PATH."system/model/dc.php");
class biz_dc_orderApiModule extends MainBaseApiModule
{
    


	/**
	 * 	商家外卖新订单
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&r_type=2&page=1
	 * 	输入:
	 *	page:int 当前的页数，没输入些参数时，默认为第一页
	 *  lid：int 门店ID
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * dc_order,array, 外卖订单详细页，结构如下
	 * total_price:订单总额
	 * pay_amount:已支付金额
	 * pay_price:合计
	 * package_price：打包费
	 * delivery_price：配送费
	 * payment_fee：手续费
	 * account_money：余额支付金额
	 * ecv_money：红包支付的金额
	 * promote_amount：优惠的金额
	 * pay_status：支付状态: 0未支付 1已支付
	 * location_id:int门店ID
	 * order_status：订单的结单状态标识，结单后的订单允许删除,0:否 1:是(结单条件:1.用户确认到货,2.商家在超期后帮用户确认到货,3.用户退款被确认)
	 * confirm_status：订单商家确认状态，0:未确认（未接单，用户未付款可以取消，已付款可直接退款），1.已确认（商家已接单,客户可与商家联系，申请退款），2.已配送，或者预订订单验证成功
	 * order_delivery_time：外卖送达时间，等于1时，为立即送达，大于1时，我具体送达时间，格式为时间戳
	 * location_name：商家名称
	 * payment_id:0为在线支付，1为货到付款
	 * consignee:联系人
	 * mobile：手机号
	 * api_address:定位地址
	 * address:详细地址， 完成地址是： api_address+address
	 * dc_comment：订单备注
	 * invoice：发票信息
	 * create_time_format:格式化后的下单时间
	 * promote_str：array:array  此订单享受的优惠信息
	 * order_state返回订单的状态  state_format为状态的文字描述 ，state为状态，代表意义如下：
	 *  1、待支付
		2、待接单
		3、已接单
		4、已完成
		5、订单关闭
		6.退款申请中
		7.已退款
		8.退款驳回
	 * order_menu：array:array菜单信息
	 *  Array
        (
            [0] => Array
                (
                    [id] => 69
                    [order_sn] => 2015081412594639
                    [create_time] => 1439499586
                    [confirm_status] => 0
                    [pay_status] => 1
                    [total_price] => 89.0000
                    [package_price] => 1.0000
                    [delivery_price] => 1.0000
                    [payment_fee] => 0.0000
                    [promote_str] => Array
                        (
                        )

                    [pay_amount] => 89.0000
                    [ecv_money] => 0.0000
                    [account_money] => 89.0000
                    [payment_id] => 0
                    [order_menu] => Array
                        (
                            [cart_list] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 260
                                            [session_id] => uf5jq74qf5iq4rcke6fuhmt405
                                            [user_id] => 71
                                            [location_id] => 41
                                            [supplier_id] => 43
                                            [name] => 饕餮鸡排饭
                                            [icon] => ./public/attachment/201504/17/10/55306e5b0f72a.jpg
                                            [num] => 1
                                            [unit_price] => 23.0000
                                            [total_price] => 23.0000
                                            [menu_id] => 48
                                            [table_time_id] => 0
                                            [table_time] => 0
                                            [cart_type] => 1
                                            [add_time] => 1439499579
                                            [is_effect] => 1
                                            [url] => /o2onew/index.php?ctl=dcbuy&lid=41
                                        )
                                   )
                            )
                    [consignee] => 王明
                    [mobile] => 15158789965
                    [api_address] => 福州市仓山区福州仓山万达广场
                    [address] => 地中心
                    [order_delivery_time] => 1
                    [dc_comment] => 
                    [invoice] => 
                    [promote_amount] => 0.0000
                    [create_time_format] => 2015-08-14 12:59
                    [pay_price] => 89                  
			)
		)	
	 */  
	
    
	public function index()
	{	

		/*初始化*/
		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
		$root['page_title'] = "外卖新订单";
			
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
		

		$lid = intval($GLOBALS['request']['lid']);
		$page = intval($GLOBALS['request']['page']);
		if($page==0){
			$page = 1;
		}
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		if(!in_array($lid, $account_info['location_ids']))
		{
			return output($root,0,"没有管理权限");
		}
		
		//返回商户权限
		if(!check_module_auth("dcorder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
		$from = strim($GLOBALS['request']['from']);
	
		if($from=='wap'){
			$type='wap';
		}
		
		$dc_order=$GLOBALS['db']->getAll("select id,order_sn,location_id,create_time,confirm_status,pay_status,total_price,package_price,delivery_price,payment_fee,promote_str,pay_amount,ecv_money,is_cancel,refund_status,is_rs,
				account_money,payment_id,order_menu,consignee,mobile,api_address,address,order_delivery_time,dc_comment,invoice,promote_amount from ".DB_PREFIX."dc_order where confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." order by id desc limit ".$limit);
		foreach($dc_order as $k=>$v){
			
			$dc_order[$k]['promote_str']=unserialize($v['promote_str'])?unserialize($v['promote_str']):array();
			$menu_list=unserialize($v['order_menu']);
			$menu_list=$menu_list['menu_list']?$menu_list['menu_list']:array();
			foreach($menu_list['cart_list'] as $aa=>$bb){
				$menu_list['cart_list'][$aa]['icon']=get_abs_img_root($bb['icon']);
			}
			
			$menu_list['cart_list']=array_values($menu_list['cart_list']);
			$dc_order[$k]['order_menu']=$menu_list;
			$dc_order[$k]['create_time_format']=to_date($v['create_time'],"Y-m-d H:i");
			
			if($dc_order[$k]['order_delivery_time']==1){
				$dc_order[$k]['order_delivery_time_format']='立即送达';
			}else{
				$dc_order[$k]['order_delivery_time_format']=to_date($dc_order[$k]['order_delivery_time']);
			}
			
			$dc_order[$k]['pay_price']=$dc_order[$k]['total_price']-$dc_order[$k]['ecv_money']-$dc_order[$k]['promote_amount'];
			$dc_order[$k]['order_state']=get_order_state($v);
	
			$dc_order[$k]['biz_order_state']=get_biz_order_state($v,$type);
		}
		$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where  confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid);
		$page_total = ceil($total/$page_size);
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
		$root['lid']=$lid;
		$root['dc_order']=$dc_order;
		return output($root);
	}
	

	
	/**
	 * 	商家外卖订单记录
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&r_type=2&page=1&act=order
	 * 	输入:
	 *	page:int 当前的页数，没输入些参数时，默认为第一页
	 *  lid：int 门店ID
	 *  date:string 筛选的日期,如：2015-08-15，如果没有传入其参数，则默认当天
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * dc_order,array, 外卖订单详细页，结构如下
	 * total:订单个数
	 * total_price:订单总额
	 * pay_amount:已支付金额
	 * pay_price:合计
	 * package_price：打包费
	 * delivery_price：配送费
	 * payment_fee：手续费
	 * account_money：余额支付金额
	 * ecv_money：红包支付的金额
	 * promote_amount：优惠的金额
	 * pay_status：支付状态: 0未支付 1已支付
	 * location_id:int门店ID
	 * order_status：订单的结单状态标识，结单后的订单允许删除,0:否 1:是(结单条件:1.用户确认到货,2.商家在超期后帮用户确认到货,3.用户退款被确认)
	 * confirm_status：订单商家确认状态，0:未确认（未接单，用户未付款可以取消，已付款可直接退款），1.已确认（商家已接单,客户可与商家联系，申请退款），2.已配送，或者预订订单验证成功
	 * order_delivery_time：外卖送达时间，等于1时，为立即送达，大于1时，我具体送达时间，格式为时间戳
	 * location_name：商家名称
	 * payment_id:0为在线支付，1为货到付款
	 * consignee:联系人
	 * mobile：手机号
	 * api_address:定位地址
	 * address:详细地址， 完成地址是： api_address+address
	 * dc_comment：订单备注
	 * invoice：发票信息
	 * create_time_format:格式化后的下单时间
	 * promote_str：array:array  此订单享受的优惠信息
	 * 	order_state返回订单的状态  state_format为状态的文字描述 ，state为状态，代表意义如下：
	 *  1、待支付
		2、待接单
		3、已接单
		4、已完成
		5、订单关闭
		6.退款申请中
		7.已退款
		8.退款驳回
	 * order_menu：array:array菜单信息
	 *  Array
		 (
		 [0] => Array
			 (
			 [id] => 69
			 [order_sn] => 2015081412594639
			 [create_time] => 1439499586
			 [confirm_status] => 0
			 [pay_status] => 1
			 [total_price] => 89.0000
			 [package_price] => 1.0000
			 [delivery_price] => 1.0000
			 [payment_fee] => 0.0000
			 [promote_str] => Array
						 (
						 )
			
			 [pay_amount] => 89.0000
			 [ecv_money] => 0.0000
			 [account_money] => 89.0000
			 [payment_id] => 0
			 [order_menu] => Array
				 (
					 [cart_list] => Array
						 (
							 [0] => Array
								 (
									 [id] => 260
									 [session_id] => uf5jq74qf5iq4rcke6fuhmt405
									 [user_id] => 71
									 [location_id] => 41
									 [supplier_id] => 43
									 [name] => 饕餮鸡排饭
									 [icon] => ./public/attachment/201504/17/10/55306e5b0f72a.jpg
									 [num] => 1
									 [unit_price] => 23.0000
									 [total_price] => 23.0000
									 [menu_id] => 48
									 [table_time_id] => 0
									 [table_time] => 0
									 [cart_type] => 1
									 [add_time] => 1439499579
									 [is_effect] => 1
									 [url] => /o2onew/index.php?ctl=dcbuy&lid=41
								 )
							 )
					 )
			 [consignee] => 王明
			 [mobile] => 15158789965
			 [api_address] => 福州市仓山区福州仓山万达广场
			 [address] => 地中心
			 [order_delivery_time] => 1
			 [dc_comment] =>
			 [invoice] =>
			 [promote_amount] => 0.0000
			 [create_time_format] => 2015-08-14 12:59
			 [pay_price] => 89
			 [order_state] => Array
                        (
                            [state] => 4
                            [state_format] => 已完成
                        )
             
           
		)
		)
	
	 */
	
	
	public function order()
	{
	
		/*初始化*/
		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
		$root['page_title'] = "外卖订单记录";
			
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
	
		$date = strim($GLOBALS['request']['date']);
		if(!$date){
			$date=to_date(NOW_TIME,"Y-m-d");
		}
		
		
		
		$begin_time=to_timespan($date);
		$end_time=$begin_time+3600*24-1;

		$lid = intval($GLOBALS['request']['lid']);
		$page = intval($GLOBALS['request']['page']);
		
		$from = strim($GLOBALS['request']['from']);
		if($from=='wap'){
			$type='wap';
		}
		if($page==0){
			$page = 1;
		}
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		if(!in_array($lid, $account_info['location_ids']))
		{
			return output($root,0,"没有管理权限");
		}
	
		//返回商户权限
		if(!check_module_auth("dcorder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
	
		
		$dc_order=$GLOBALS['db']->getAll("select id,order_sn,is_cancel,refund_status,location_id,create_time,confirm_status,pay_status,total_price,package_price,delivery_price,payment_fee,promote_str,pay_amount,ecv_money,is_rs,
				account_money,payment_id,order_menu,consignee,mobile,api_address,address,order_delivery_time,dc_comment,invoice,promote_amount from ".DB_PREFIX."dc_order where is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." and create_time between ".$begin_time." and ".$end_time." order by id desc limit ".$limit);
		foreach($dc_order as $k=>$v){
				
			$dc_order[$k]['promote_str']=unserialize($v['promote_str'])?unserialize($v['promote_str']):array();
			$menu_list=unserialize($v['order_menu']);
			$menu_list=$menu_list['menu_list']?$menu_list['menu_list']:array();
			foreach($menu_list['cart_list'] as $aa=>$bb){
				$menu_list['cart_list'][$aa]['icon']=get_abs_img_root($bb['icon']);
			}
				
			$menu_list['cart_list']=array_values($menu_list['cart_list']);
			$dc_order[$k]['order_menu']=$menu_list;
			$dc_order[$k]['create_time_format']=to_date($v['create_time'],"Y-m-d H:i");
			if($dc_order[$k]['order_delivery_time']==1){
				$dc_order[$k]['order_delivery_time_format']='立即送达';
			}else{
				$dc_order[$k]['order_delivery_time_format']=to_date($dc_order[$k]['order_delivery_time']);
			}
				
			$dc_order[$k]['pay_price']=$dc_order[$k]['total_price']-$dc_order[$k]['ecv_money']-$dc_order[$k]['promote_amount'];
			$dc_order[$k]['order_state']=get_order_state($v);
			
			$dc_order[$k]['biz_order_state']=get_biz_order_state($v,$type);
		}
		$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where  is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." and create_time between ".$begin_time." and ".$end_time);
		$page_total = ceil($total/$page_size);
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
		
		$today=to_timespan(to_date(NOW_TIME,"Y-m-d"));
		
		$data_arr=array();
		for($i=0;$i<10;$i++){
			$data_arr[]=to_date($today-3600*24*$i,"Y-m-d");
				
		}
		$root['lid']=$lid;
		$root['data_arr']=$data_arr;
		$root['dc_order']=$dc_order;
		$root['total']=$total;
		$root['date']=$date;
		return output($root);
	}
	
	/**
	 * 	商家外卖催单记录
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&r_type=2&page=1&act=dc_reminder
	 * 	输入:
	 *	page:int 当前的页数，没输入些参数时，默认为第一页
	 *  lid：int 门店ID
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 *  page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * 
	 *  list:array:array ,催单列表，结构如下：
	 *  完成地址为：api_address+address
	 *  create_time_format:格式化后的催单时间
	 *   Array
        (
            [0] => Array            
                (
                    [id] => 7
                    [order_sn] => 2015081410542227
                    [order_id] => 59
                    [location_id] => 41
                    [supplier_id] => 43
                    [create_time] => 1439492235
                    [user_id] => 71
                    [address] => 地中心
                    [api_address] => 福州市仓山区福州仓山万达广场
                    [consignee] => 王明
                    [mobile] => 15158789965
                    [create_time_format] => 2015-08-14 10:57:15
                )

            [1] => Array
                (
                    [id] => 6
                    [order_sn] => 2015081310135049
                    [order_id] => 55
                    [location_id] => 41
                    [supplier_id] => 43
                    [create_time] => 1439488390
                    [user_id] => 71
                    [address] => 地中心
                    [api_address] => 福州市仓山区福州仓山万达广场
                    [consignee] => 王明
                    [mobile] => 15158789965
                    [create_time_format] => 2015-08-14 10:57:15
                )
          )      
	 */ 
	
	public function dc_reminder(){
	
		
		/*初始化*/
		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
		$root['page_title'] = "外卖新订单";
			
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
		
		$lid = intval($GLOBALS['request']['lid']);
		$page = intval($GLOBALS['request']['page']);
		if($page==0){
			$page = 1;
		}
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		if(!in_array($lid, $account_info['location_ids']))
		{
			return output($root,0,"没有管理权限");
		}
		
		//返回商户权限
		if(!check_module_auth("dcreminder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
				
		$sql="select * from ".DB_PREFIX."dc_reminder where location_id=".$lid;
		$condition=$sql;
		$sql.=" order by create_time desc limit ".$limit;

		$list=$GLOBALS['db']->getAll($sql);
		foreach($list as $k=>$v){
			$list[$k]['create_time_format']=to_date($v['create_time']);
		}
		$list=$list?$list:array();
		$total=intval(count($GLOBALS['db']->getAll($condition)));

		$page_total = ceil($total/$page_size);
		$root['lid']=$lid;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
		$root['list']=$list;
		return output($root);
	}
	
	
	
	/**
	 * 	商家外卖接单接口
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&id=2&r_type=2&act=accept_order
	 * 	输入:
	 *	id:int 订单
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 *  
	 *  status：为接单的操作的状态，status=0,接单失败;status=1,接单成功
	 *  info:返回的提示信息
	 */  
	
	public function accept_order()
	{	
		
		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
		
		//门店ID
		//$lid = intval($GLOBALS['request']['lid']);
		
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
		
		//返回商户权限
		if(!check_module_auth("dcorder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
		
		
		$id = intval($GLOBALS['request']['id']);
		$type = intval($GLOBALS['request']['type']);
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where type_del = 0 and id=".$id);

		if($order_info){
			
    		if(!in_array($order_info['location_id'], $account_info['location_ids']))
    		{
    			return output($root,0,"没有管理权限");
    		}
    		
    		if($type==-1){
    		    require_once(APP_ROOT_PATH."system/model/dc.php");
    		
    		    $root['info'] ="订单关闭成功";
    		    $close_reason='商家关闭订单';
    		    dc_order_close($id,2,$close_reason);
    		    
    		    return output($root,1,"订单关闭成功");
    		    
    		}else{
        	   	 if($order_info['order_delivery_time']==1){
        	   	 	//立即送达超过两小时不接单，直接关闭订单
        	   	 	if(NOW_TIME-$order_info['create_time'] > 3600 * 2){
        	   	 		require_once(APP_ROOT_PATH."system/model/dc.php");
        		   	 	$close_reason='商家接单超时，订单关闭';
        				dc_order_close($id,2,$close_reason);
        				$root['time_status']=1;
        	   	 		return output($root,0,"请在用户下单后2小时内接单，接单超时，订单关闭");
        	   	 	}
        	   	 }elseif($order_info['order_delivery_time'] > 10000){
        	   	 	//有具体送达时间，超过送达时间，直接关闭订单
        	   	 	if(NOW_TIME > $order_info['order_delivery_time']){
        		   	 	require_once(APP_ROOT_PATH."system/model/dc.php");
        		   	 	$close_reason='商家接单超时，订单关闭';
        				dc_order_close($id,2,$close_reason);
        		   	 	$root['time_status']=1;
        		   	 	return output($root,0,"超过用户配送时间，接单超时，订单关闭");
        	   	 	}
        	   	 }
    		
    			$GLOBALS['db']->query("update ".DB_PREFIX."dc_order set delivery_part=".$type."  where id = ".$id);
    			$GLOBALS['db']->query("update ".DB_PREFIX."dc_supplier_order set delivery_part=".$type."  where order_id = ".$id);
    			
    			$rs=$GLOBALS['db']->affected_rows();
    			if($rs> 0){
    
    			    $root['status'] = 1;
    				$root['jump'] = url("biz","dcorder#index");
    				$root['info'] ="接单成功";
    				require_once(APP_ROOT_PATH."system/model/dc.php");
    				
    				dc_accept_order($id);
    			    
    				return output($root,1,"接单成功");
    				
    			}else{
    				
    				return output($root,0,"已接单，不用重复操作");
    				
    			}
    		}

	}else{
			return output($root,0,"订单不存在");
	}
}
	
	/**
	 * 	商家关闭订单接口
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&id=2&r_type=2&act=close_order
	 * 	输入:
	 *	id:int 订单ID
	 *  close_reason:string 关闭订单的原因
	 *  
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 *
	 *  status：为关闭订单的操作的状态，status=0,关闭订单失败;status=1,关闭订单成功
	 *  info:返回的提示信息
	 */
	
	public function close_order()
	{

		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
		
		//门店ID
		$lid = intval($GLOBALS['request']['lid']);
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
		//返回商户权限
		if(!check_module_auth("dcorder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
		$id = intval($GLOBALS['request']['id']);
 		$close_reason=strim($GLOBALS['request']['close_reason']);	
		
		if($close_reason==''){
// 			return output($root,0,"请填写关闭订单原因");
		    $close_reason=='协商关闭';
		}
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where type_del = 0 and is_rs = 0 and id=".$id);
		
		
		if(!$order_info)
		{
	        return output($root,0,"订单不存在");
		}
		
		if(!in_array($order_info['location_id'], $account_info['location_ids']))
		 {
	        return output($root,0,"没有管理权限");
	   	 }
	   	 
	   	 require_once(APP_ROOT_PATH."system/model/dc.php");
	 	 dc_order_close($id,2,$close_reason);
	     return output($root,1,"关闭订单成功");
		
	}
	
	/**
	 * 	商家外卖 确认订单接口
	 *  测试链接： http://localhost/o2onew/mapi/index.php?ctl=dc_biz_order&id=2&r_type=2&act=over_order
	 * 	输入:
	 *	id:int 订单
	 *  输出:
	 *  status:int 结果状态 0失败 1成功
	 *  info:信息返回
	 *  biz_user_status：int 商户登录状态 0未登录/1已登录
	 *
	 *  以下仅在biz_user_status为1时会返回
	 *  is_auth：int 模块操作权限 0没有权限 / 1有权限
	 *  
	 *  status：为确认订单的操作的状态，status=0,确认订单失败;status=1,确认订单成功
	 *  info:返回的提示信息
	 */ 
	
	public function over_order()
	{	
		
		$root = array();
		$account_info = $GLOBALS['account_info'];
		$supplier_id = $account_info['supplier_id'];
			
		/*业务逻辑*/
		$root['biz_user_status'] = $account_info?1:0;
		if (empty($account_info)){
			return output($root,0,"商户未登录");
		}
		//返回商户权限
		if(!check_module_auth("dcorder")){
			$root['is_auth'] = 0;
			return output($root,0,"没有操作验证权限");
		}else{
			$root['is_auth'] = 1;
		}
		$id = intval($GLOBALS['request']['id']);

		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where type_del = 0 and is_rs = 0 and id=".$id);
	
		if(!$order_info)
		{
	        return output($root,0,"订单不存在");
		}
		
		 if(!in_array($order_info['location_id'], $account_info['location_ids']))
		 {
	        return output($root,0,"没有管理权限");
	   	 }
	   	 require_once(APP_ROOT_PATH."system/model/dc.php");
	   	 $result=dc_confirm_delivery($id);
		 return output($root,$result['status'],$result['info']);
		
	}
	
	
    public function biz_wap_index(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        
        $root['page_title'] = "外卖订单";
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //门店ID
        $lid = intval($GLOBALS['request']['lid']);

        //分页
        $page = intval($GLOBALS['request']['page']);       
        if($page==0){
            $page = 1;
        }
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
        

        if(!in_array($lid, $account_info['location_ids']))
        {
            return output($root,0,"没有管理权限");
        }
        
        //返回商户权限
        if(!check_module_auth("dcorder")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作验证权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        $from = strim($GLOBALS['request']['from']);
        if($from=='wap'){
            $type='wap';
        }
        
        /* sort 订单状态，sort=0,全部，sort=1 待接单，sort=2 待完成 ，sort=3 催单*/
        $sort = intval($GLOBALS['request']['sort']);
        
        if($sort==0){//全部订单
            $dc_order_sql="select id,order_sn,is_cancel,refund_status,location_id,create_time,confirm_status,pay_status,total_price,package_price,delivery_price,payment_fee,promote_str,pay_amount,ecv_money,is_rs,
				account_money,payment_id,order_menu,consignee,mobile,api_address,address,order_delivery_time,dc_comment,invoice,promote_amount from ".DB_PREFIX."dc_order where is_delivery_cancel=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." order by id desc limit ".$limit;
            $dc_order_count="select count(*) from ".DB_PREFIX."dc_order where is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid;
        }elseif ($sort==1){//待接单
            $dc_order_sql="select id,order_sn,location_id,create_time,confirm_status,pay_status,total_price,package_price,delivery_price,payment_fee,promote_str,pay_amount,ecv_money,is_cancel,refund_status,is_rs,
				account_money,payment_id,order_menu,consignee,mobile,api_address,address,order_delivery_time,dc_comment,invoice,promote_amount from ".DB_PREFIX."dc_order where is_delivery_cancel=0 and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." order by id desc limit ".$limit;
            $dc_order_count="select count(*) from ".DB_PREFIX."dc_order where is_delivery_cancel=0 and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid;
        }elseif ($sort==2){//待完成
            $dc_order_sql="select id,order_sn,location_id,create_time,confirm_status,pay_status,total_price,package_price,delivery_price,payment_fee,promote_str,pay_amount,ecv_money,is_cancel,refund_status,is_rs,
				account_money,payment_id,order_menu,consignee,mobile,api_address,address,order_delivery_time,dc_comment,invoice,promote_amount from ".DB_PREFIX."dc_order where is_delivery_cancel=0 and confirm_status=1 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid." order by id desc limit ".$limit;
            $dc_order_count="select count(*) from ".DB_PREFIX."dc_order where confirm_status=1 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid;
        }elseif ($sort==3){//催单
            $dc_order_sql="select * from ".DB_PREFIX."dc_reminder where location_id=".$lid." order by create_time desc limit ".$limit;
            $dc_order_count="select * from ".DB_PREFIX."dc_reminder where location_id=".$lid;
        }
        
        
        if($dc_order_sql){
            $dc_order=$GLOBALS['db']->getAll($dc_order_sql);
            foreach($dc_order as $k=>$v){
                if($sort!=3){                   
                    $dc_order[$k]['promote_str']=unserialize($v['promote_str'])?unserialize($v['promote_str']):array();
                    $menu_list=unserialize($v['order_menu']);
                    $menu_list=$menu_list['menu_list']?$menu_list['menu_list']:array();
                    foreach($menu_list['cart_list'] as $aa=>$bb){
                        $menu_list['cart_list'][$aa]['icon']=get_abs_img_root($bb['icon']);
                    }
                    $dc_order[$k]['cart_list_count']=count($menu_list['cart_list']);
                    $menu_list['cart_list']=array_values($menu_list['cart_list']);
                    $dc_order[$k]['order_menu']=$menu_list;
                    $dc_order[$k]['create_time_format']=to_date($v['create_time'],"Y-m-d H:i:s");
                    if($dc_order[$k]['order_delivery_time']==1){
                        $dc_order[$k]['order_delivery_time_format']='立即送达';
                    }else{
                        $dc_order[$k]['order_delivery_time_format']=to_date($dc_order[$k]['order_delivery_time']);
                    }
                    
                    $dc_order[$k]['pay_price']=round($dc_order[$k]['total_price']-$dc_order[$k]['ecv_money']-$dc_order[$k]['promote_amount'],2);
                    $dc_order[$k]['order_state']=get_order_state($v);
                     
                    $dc_order[$k]['biz_order_state']=get_biz_order_state_new($v,$type);
                    $dc_order[$k]['dcorder_url'] = wap_url('biz','dc_order#view',array('lid'=>$lid,'data_id'=>$v['id']));
                }
                else{
                    $dc_order[$k]['create_time']=to_date($v['create_time'],"Y-m-d H:i:s");
                    $dc_order[$k]['dcorder_url'] = wap_url('biz','dc_order#view',array('lid'=>$lid,'data_id'=>$v['order_id']));
                }
               
            }
            
            $count = intval($GLOBALS['db']->getOne($dc_order_count));
            $page_total = ceil($count/$page_size);
            $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
            $root['lid']=$lid;
            $root['dc_order']=$dc_order;
            
            $root['dc_new_order_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where is_delivery_cancel=0 and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid);
            $root['dc_wait_order_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where confirm_status=1 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id=".$lid);
            $root['sort']=$sort;
            
            //达达配送状态
            $is_open_dada=IS_OPEN_DADA;
            $root['is_open_dada']=$is_open_dada;
            
            if($is_open_dada){
            
                $supplier_location = $GLOBALS['db']->getRow("select dada_account from ".DB_PREFIX."supplier_location where id=".$lid);
                
                $is_open_dada_delivery = $account_info['is_open_dada_delivery'];
                $root['is_open_dada_delivery']=$is_open_dada_delivery;
                
                $delivery_min_money = app_conf('DELIVERY_MIN_MONEY');
                $delivery_money = $account_info['delivery_money'];
                
                $root['dada_account']=$supplier_location['dada_account'];
                
                $delivery_money_enough = 1;  //配送费充足
                if($delivery_money < $delivery_min_money){
                    $delivery_money_enough = 0; //配送费不足
                }
                $root['delivery_money_enough']=$delivery_money_enough;
            }
        }

        return output($root);
        
    }
    
    public function abnormal_order(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $root['page_title'] = "异常订单";
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        $root['is_dc_delivery']=IS_DC_DELIVERY;
        if(IS_DC_DELIVERY == 0){
            return output($root,0,"没有管理权限");
        }
        
        //分页
        $page = intval($GLOBALS['request']['page']);
        if($page==0){
            $page = 1;
        }
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
        
        
        if(!$account_info['location_ids'])
        {
            return output($root,0,"没有管理权限");
        }
        
        $dc_order_sql="select do.*,sl.name,sl.dada_account from ".DB_PREFIX."dc_order as do 
            left join ".DB_PREFIX."supplier_location as sl on sl.id=do.location_id
            where do.is_delivery_cancel=1 and do.is_cancel=0 and do.refund_status=0 and do.order_status=0 and do.type_del=0 and do.is_rs=0 and ((do.payment_id=0 and do.pay_status=1) or do.payment_id=1) 
            and location_id in (".implode(',', $account_info['location_ids']).") order by do.id desc limit ".$limit;
        $dc_order_count="select count(*) from ".DB_PREFIX."dc_order where do.is_delivery_cancel=1 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id in (".implode(',', $account_info['location_ids']).")";
    
        $dc_order=$GLOBALS['db']->getAll($dc_order_sql);
        /* foreach ($dc_order as $t =>$v){
            
        } */
        
        $count = intval($GLOBALS['db']->getOne($dc_order_count));
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
    
        $root['order_list']=$dc_order?$dc_order:array();
        
        //达达配送状态
        $is_open_dada=IS_OPEN_DADA;
        $root['is_open_dada']=$is_open_dada;
        
        if($is_open_dada){
            
            $is_open_dada_delivery = $account_info['is_open_dada_delivery'];
            $root['is_open_dada_delivery']=$is_open_dada_delivery;
            
            $delivery_min_money = app_conf('DELIVERY_MIN_MONEY');
            $delivery_money = $account_info['delivery_money'];
            
            $delivery_money_enough = 1;  //配送费充足
            if($delivery_money < $delivery_min_money){
                $delivery_money_enough = 0; //配送费不足
            }
            $root['delivery_money_enough']=$delivery_money_enough;
        }
        
        return output($root);
    }
    
    public function view(){
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        
        //门店ID
        $lid = intval($GLOBALS['request']['lid']);
        
        $root['page_title'] = "外卖订单详情";
        /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        
        if(!in_array($lid, $account_info['location_ids']))
        {
            return output($root,0,"没有管理权限");
        }
        

        //返回商户权限
        if(!check_module_auth("dcorder")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作验证权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        $from = strim($GLOBALS['request']['from']);
        if($from=='wap'){
            $type='wap';
        }
        
        
        
        //门店信息获取
        $supplier_location = $GLOBALS['db']->getRow("select name,preview,dada_account,xpoint,ypoint from ".DB_PREFIX."supplier_location where id=".$lid);
        $supplier_location['preview'] = get_abs_img_root(get_spec_image($supplier_location['preview'],360,270,1));
        
        $root['supplier_location'] = $supplier_location;
        
        //订单ID
        $order_id = intval($GLOBALS['request']['data_id']);
        
        $dc_order_sql="select * from ".DB_PREFIX."dc_order where id=".$order_id." and location_id=".$lid;
        
        $dc_order=$GLOBALS['db']->getRow($dc_order_sql);
        
        $dc_order['promote_str']=unserialize($dc_order['promote_str'])?unserialize($dc_order['promote_str']):array();
        $menu_list=unserialize($dc_order['order_menu']);
        $menu_list=$menu_list['menu_list']?$menu_list['menu_list']:array();
        foreach($menu_list['cart_list'] as $aa=>$bb){
            $menu_list['cart_list'][$aa]['icon']=get_abs_img_root($bb['icon']);
        }
        $dc_order['cart_list_count']=count($menu_list['cart_list']);
        $menu_list['cart_list']=array_values($menu_list['cart_list']);
        $dc_order['order_menu']=$menu_list;
        $dc_order['create_time_format']=to_date($dc_order['create_time'],"Y-m-d H:i:s");
        if($dc_order['order_delivery_time']==1){
            $dc_order['order_delivery_time_format']='立即送达';
        }else{
            $dc_order['order_delivery_time_format']=to_date($dc_order['order_delivery_time']);
        }
    
        $dc_order['pay_price']=round($dc_order['total_price']-$dc_order['ecv_money']-$dc_order['promote_amount'],2);
        $dc_order['order_state']=get_order_state($dc_order);
         
        $dc_order['biz_order_state']=get_biz_order_state_new($dc_order,$type);
        
        if($dc_order['delivery_part']==2 || $dc_order['delivery_part']==3){
            $dada_delivery = unserialize($dc_order['dada_delivery']);
            if($dada_delivery['order_status'] == 1 || $dada_delivery['order_status'] == 2 || $dada_delivery['order_status'] == 3 || $dada_delivery['order_status'] == 4){
                require_once(APP_ROOT_PATH."system/delivery/DaDaDelivery.php");
                $DaDaDelivery = new DaDaDelivery();
                $dada_info = $DaDaDelivery->queryOrder($dc_order['order_sn']);
                
                $dc_order['dada_order_status'] = $dada_delivery['order_status'];
                if($dada_delivery['order_status']==1){  //达达待接单
                    $no_confirm_time = ceil((NOW_TIME - $dc_order['create_time'] ) / 60);
                    $dc_order['send_info']=' 委托达达配送( 已'.$no_confirm_time.'分钟未接单 )';
                }
                elseif($dada_delivery['order_status'] == 2){
                    $dc_order['send_info']="骑手正赶往商家";
                    $distance = getDistance($dada_info['transporterLng'],$dada_info['transporterLat'],$supplier_location['xpoint'],$supplier_location['ypoint']);
                    if($distance>1000){
                        $dc_order['distance'] = $distance / 1000;
                        $dc_order['send_info'].="(".$dc_order['distance']."km)";
                    }else{
                        $dc_order['distance'] = $distance;
                        $dc_order['send_info'].="(".$dc_order['distance']."m)";
                    }
                    
                }
                elseif($dada_delivery['order_status'] == 3){
                    $dc_order['send_info']="骑手正在送餐";
                    $distance = getDistance($dada_info['transporterLng'],$dada_info['transporterLat'],$dc_order['xpoint'],$dc_order['ypoint']);
                    if($distance>1000){
                        $dc_order['distance'] = $distance / 1000;
                        $dc_order['send_info'].="(".$dc_order['distance']."km)";
                    }else{
                        $dc_order['distance'] = $distance;
                        $dc_order['send_info'].="(".$dc_order['distance']."m)";
                    }
                }
                elseif($dada_delivery['order_status'] == 4){
                    $dc_order['send_info']="已送达";
                }
            }
        }
        
        //达达配送状态
        $is_open_dada=IS_OPEN_DADA;
        $root['is_open_dada']=$is_open_dada;
        
        if($is_open_dada){
        
            //$supplier_location = $GLOBALS['db']->getRow("select name,preview,dada_account from ".DB_PREFIX."supplier_location where id=".$lid);
            
            $is_open_dada_delivery = $account_info['is_open_dada_delivery'];
            $root['is_open_dada_delivery']=$is_open_dada_delivery;
            
            $delivery_min_money = app_conf('DELIVERY_MIN_MONEY');
            $delivery_money = $account_info['delivery_money'];
            
            $root['dada_account']=$supplier_location['dada_account'];
            
            $delivery_money_enough = 1;  //配送费充足
            if($delivery_money < $delivery_min_money){
                $delivery_money_enough = 0; //配送费不足
            }
            $root['delivery_money_enough']=$delivery_money_enough;
        }

        $root['order_id']=$order_id;
        $root['lid']=$lid;
        $root['dc_order']=$dc_order;
        //print_r($dc_order);exit;
        
        return output($root);

    }
    
    
    public function change_delivery()
    {
        $s_account_info = $GLOBALS['account_info'];
        $supplier_id = intval($s_account_info['supplier_id']);
        
        $id = intval($GLOBALS['request']['id']);
        
        $root['biz_user_status'] = $s_account_info?1:0;
        if (empty($s_account_info)){
            return output($root,0,"商户未登录");
        }
    
        $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where type_del = 0 and is_rs = 0 and id=".$id);
    
        if(!$order_info)
        {
            return output($root,0,"数据不存在");
        }
    
        if(!in_array($order_info['location_id'], $s_account_info['location_ids']))
        {
            $root['is_auth'] = 0;
            return output($root,0,"没有管理权限");
        }
    
    
    
        $GLOBALS['db']->query("update ".DB_PREFIX."dc_order set delivery_part=1 , delivery_operation=2  where id = ".$id);
        $GLOBALS['db']->query("update ".DB_PREFIX."dc_supplier_order set delivery_part=1 where order_id = ".$id);
        $rs_row = $GLOBALS['db']->affected_rows();
        if($rs_row > 0){
            return output($root,1,"商家自己接单成功");
        }else{
            return output($root,1,"商家自己接单成功");
        }
         
    }

}
?>