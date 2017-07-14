<?php
class dcajaxApiModule extends MainBaseApiModule
{

	

	/**
	 * 添加或者取消餐厅收藏
	 * 测试链接：http://localhost/o2onew/mapi/index.php?ctl=dcajax&act=add_location_collect&r_type=2&location_id=41
	 * 
	 * 输入：
	 * location_id：商家ID
	 * 
	 * 输出：
	 * user_login_status:用户登录状态，等于1为已登录，等于0为未登录
	 * status：操作返回的状态：status=0，操作失败，status=1，为收藏成功或取消收藏成功
	 * info，当state=0时的错误提示信息，如： 无效商家
	 * 
	 * 
	 */
	public function add_location_collect()
	{
		$root = array();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$root['user_login_status']=0;
			return output($root);
		}else{
			$root['user_login_status']=1;
			$location_id = intval($GLOBALS['request']['location_id']);
			$location_info = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."supplier_location where id = ".$location_id." and is_effect = 1");
			if($location_info)
			{
	
				$sql = "INSERT INTO `".DB_PREFIX."dc_location_sc` (`id`,`location_id`, `user_id`, `add_time`) select '','".$location_info['id']."','".intval($GLOBALS['user_info']['id'])."','".get_gmtime()."' from dual where not exists (select * from `".DB_PREFIX."dc_location_sc` where `location_id`= '".$location_info['id']."' and `user_id` = ".intval($GLOBALS['user_info']['id']).")";
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()>0){		

					$root['is_collected']=1;
					return output($root,1,$GLOBALS['lang']['COLLECT_SUCCESS']);
					
					
				}else{
					$GLOBALS['db']->query("delete from ".DB_PREFIX."dc_location_sc where location_id=".$location_info['id']." and user_id=".intval($GLOBALS['user_info']['id']));
					if($GLOBALS['db']->affected_rows()>0){	
						$root['is_collected']=0;
						return output($root,1,$GLOBALS['lang']['LOCATION_COLLECT_CANCEL']);
					}
	
				}
			}
			else
			{
				return output($root,0,$GLOBALS['lang']['INVALID_LOCATION']);
			}
		}
	}
	

	
	/*
	 * 返回当前用户的ID
	 */
	public function get_user_id(){
	
		global_run();
		$user_id=isset($GLOBALS['user_info']['id'])?intval($GLOBALS['user_info']['id']):0;
		ajax_return($user_id);
	}
	
	
	/**
	 * 添加购物车
	 * 请求参数：$request => array(
	 * 				'location_id' => int  门店id
	 * 				'menu_id' => int 菜品id
	 * 				'number' => int 添加后的数量
	 * 				'tid' => int(可选) 订座时间项目id
	 * 			)
	 * @return array (
	 *         		'status' => int 操作状态 1:成功 0:失败
	 *         		'info' => string 操作状态描述
	 *         )
	 */
	public function dc_add_cart()
	{
		$root = array();
		$status = 0;
		$info = '';
		do {
			$request = $GLOBALS['request'];
			$location_id = intval($request['location_id']);
	        $location_sql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$location_id.' AND is_effect=1 AND is_close=0';
	        $location = $GLOBALS['db']->getRow($location_sql);
			$tid = 0; // 餐桌项目关联id?? 时间在订单提交时再确认
			if (isset($request['table_menu_id'])) {
				$tid = intval($request['table_menu_id']);
			}
			
	        if (empty($location) || ($location['is_dc'] == 0 && $tid == 0) || ($location['is_reserve'] == 0 && $tid == 1)) {
	        	// 商户不存在、不支持外卖、或不支持订座点餐
	            $info = '商户参数错误';
	            break;
	        }
			// 验证餐桌是否能有效(是否存在或者是否定满,订座的日期数据??)
	        /*if ($tid) {
	        	$time_sql = 'SELECT * FROM '.DB_PREFIX.'dc_rs_item_time WHERE id='.$tid;
	        	$time_info = $GLOBALS['db']->getRow($time_sql);
	        	if (empty($time_info)) {
	        		$info = '订座数据异常';
	        		break;
	        	}
	        }*/

	        $session_id = es_session::id();
	        $extWhere = ' AND session_id="'.$session_id.'"';
			$user_id = 0;
			if (!empty($GLOBALS['user_info'])) {
				$user_id = $GLOBALS['user_info']['id'];
				$extWhere .= ' AND user_id='.$user_id;
			}

			$cart_type = $tid ? 0 : 1; //  0:餐桌 1:菜品

			$number = intval($request['number']);

			$menu_id = intval($request['menu_id']);

			$menu_sql = 'SELECT * FROM '.DB_PREFIX.'dc_menu WHERE id='.$menu_id.' AND is_effect=1 AND location_id='.$location_id;
			$menu = $GLOBALS['db']->getRow($menu_sql);
			if (empty($menu)) {
				$info = '菜品参数错误';
				break;
			}
			// 先判断购物车是否有该商品
			// $cart_sql_where = ' menu_id='.$menu_id.' AND is_effect=1 AND table_menu_id='.$tid.$extWhere;
			$cart_sql_where = ' menu_id='.$menu_id.' AND cart_type='.$cart_type.' AND is_effect=1'.$extWhere;
			$cart_menu_sql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE'.$cart_sql_where;
			$cart_exist = $GLOBALS['db']->getRow($cart_menu_sql);
			if ($cart_exist) { // 如果购物车中已经有了该商品
				if ($number <= 0) { // 如果传递的值小于0，当删除处理
					$delete_sql = 'DELETE FROM '.DB_PREFIX.'dc_cart WHERE '.$cart_sql_where;
					$res = $GLOBALS['db']->query($delete_sql);
					if ($res) {
						$status = 1;
					}
				} else { // 增加或减少
					$updata = array('num' => $number, 'unit_price' => $menu['price'], 'total_price' => $menu['price'] * $number);
					$upres = $GLOBALS['db']->autoExecute(DB_PREFIX.'dc_cart', $updata, 'UPDATE', $cart_sql_where, '', 'SILENT');
					if ($upres) {
						$status = 1;
					}
				}
				break;
			}
			// 新增购物车处理
			
			// 组合要新增的购物车数据
			$cart = array(
				'session_id' => $session_id,
				'user_id' => $user_id,
				'location_id' => $location_id,
				'supplier_id' => $location['supplier_id'],
				'name' => $menu['name'],
				'icon' => $menu['image'],
				'num' => $number,
				'unit_price' => $menu['price'],
				'total_price' => $menu['price'] * $number,
				'menu_id' => $menu_id,
				'cart_type' => $cart_type,
				'add_time' => NOW_TIME,
				'is_effect' => 1,
			);
			if ($tid) {
				// $cart['table_time_id'] = $tid;
				// $cart['table_time'] = $time_info['rs_time'];
				$cart['table_menu_id'] = $tid;
			}
			$addRs = $GLOBALS['db']->autoExecute(DB_PREFIX.'dc_cart', $cart);
			if ($addRs) {
				$status = 1;
			} else {
				$info = '添加失败';
				logger::write($GLOBALS['db']->error.':'.$GLOBALS['db']->getLastSql());
			}
		} while(0);

		return output($root, $status, $info); 
	}

	/**
	 * 清空购物车
	 * 请求参数 $request => array(
	 * 				'location_id' => int 当前门店id
	 * 				'tid' => int  订座时间段id 			
	 * 			)
	 * @return [type] [description]
	 */
	public function dc_cart_clear()
	{
		$request = $GLOBALS['request'];
		$location_id = intval($request['location_id']);
		$user_id = 0;
		if (!empty($GLOBALS['user_info'])) {
			$user_id = $GLOBALS['user_info']['id'];
		}
		$session_id = es_session::id();
		$tid = intval($request['tid']);
		$delete_sql = 'DELETE FROM '.DB_PREFIX.'dc_cart WHERE session_id="'.$session_id.'" AND user_id='.$user_id.' AND location_id='.$location_id; // .' AND table_menu_id='.$tid;
		$delete = $GLOBALS['db']->query($delete_sql);
		$status = 0;
		if ($delete) {
			$status = 1;
		}
		return output(array(), $status);
	}

	public function dc_res_cart_list()
	{
		$user_id = 0;
		if (!empty($GLOBALS['user_info'])) {
			$user_id = $GLOBALS['user_info']['id'];
		}
		$session_id = es_session::id();
		$location_id = intval($GLOBALS['request']['location_id']);
		$table_menu_id = intval($GLOBALS['request']['table_menu_id']);
		$sql = 'SELECT * FROM '.DB_PREFIX.'dc_cart WHERE session_id="'.$session_id.'" AND cart_type=0 AND user_id='.$user_id.' AND location_id='.$location_id; // .' AND table_menu_id='.$table_menu_id;
		$orig_list = $GLOBALS['db']->getAll($sql);
		$list = array();
		$total_price = 0;
		foreach ($orig_list as $item) {
			$total_price += $item['total_price'];
			$item['format_unit_price'] = format_price($item['unit_price']);
			$list[] = $item;
		}
		$root = array(
			'list' => $list,
			'total_price' => $total_price,
			'format_total_price' => format_price($total_price)
		);
		return output($root);
	}
	
	public function get_dc_cart_list()
	{
		$root = array('hasCart' => 0);
		$location_id = intval($GLOBALS['request']['location_id']);
		if ($location_id > 0) {
			require_once(APP_ROOT_PATH."system/model/dc.php");
			$result = load_dc_cart_list(true, $location_id, 1);
			
			if ($result['cart_list']) {
				$list = $result['cart_list'];
				$cartCount = 0;
				$menuidAndNum = array();
				foreach ($list as &$cart) {
					$cart['format_unit_price'] = format_price($cart['unit_price']);
					$cart['icon'] = get_abs_img_root(get_spec_image($cart['icon'], 200, 150, 1));
					$cartCount += $cart['num'];
					$menuidAndNum[$cart['menu_id']] = $cart['num'];
				}unset($cart);
				$root['list'] = $list;
				$root['cartCount'] = $cartCount;
				$root['menuidAndNum'] = $menuidAndNum;
				$root['hasCart'] = 1;
			}
		}
		return output($root);
	}
}
?>