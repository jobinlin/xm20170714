<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dealgroupApiModule extends MainBaseApiModule
{
	
	/**
	 */
	public function index()
	{
		if( $GLOBALS['request']['from']=='wap'){
			$is_wap=true;
		}else{
			$is_wap=false;
		}
		/*关联商品数据*/
		$data_id = intval($GLOBALS['request']['data_id']);//商品ID
		
		$deal_ids = $GLOBALS['db']->getOne("select relate_ids from ".DB_PREFIX."relate_goods where good_id=".$data_id);
		
				
		$result = array();
		if($deal_ids){
			require_once(APP_ROOT_PATH."system/model/deal.php");
			$result = getDetailedList_v1($deal_ids.','.$data_id);
		}
		$relate_data = $result;
		if( !empty($relate_data) ){
			//app版本不需要
			$type = intval($GLOBALS['request']['type']);//商品ID
			if( empty($type) ){
				unset($relate_data['attrArray']);
				unset($relate_data['stockArray']);
			}
			
		}
		
		foreach ($relate_data['goodsList'] as $k=>$v){
			if($v['deal_attr']){
				$relate_data['deal_attr'][$v['id']]['current_price']=sprintf("%.2f",$v['current_price']);
				$max=0;
				foreach ($v['deal_attr'] as $kk=>$vv){
					$arr=array();
					foreach ($vv['attr_list'] as $kkk=>$vvv){
						$arr[]=$vvv['price'];
					}
					$max+=max($arr);
				}
				$relate_data['deal_attr'][$v['id']]['max_current_price']=sprintf("%.2f",$v['current_price']+$max);
				$relate_data['deal_attr'][$v['id']]['icon']=$v['f_icon_middle'];
				$relate_data['deal_attr'][$v['id']]['deal_attr']=$v['deal_attr'];
				
				//$deal_attr_stock_json[$v['id']]=
				$attr_stock_list =$GLOBALS['db']->getAll("select id,attr_cfg,stock_cfg,attr_str,buy_count,attr_key,price,add_balance_price from ".DB_PREFIX."attr_stock where deal_id = ".$v['id'],false);
				$deal_attr_price_all=$GLOBALS['db']->getAll("select id from ".DB_PREFIX."deal_attr where deal_id = ".$v['id']);
				foreach($deal_attr_price_all as $kkkkk=>$vvvvv)
				{
					$deal_attr_price[$vvvvv['id']] = $vvvvv['price'];
				}
				$attr_stock_data = array();
				foreach($attr_stock_list as $row)
				{
					$row['attr_cfg'] = unserialize($row['attr_cfg']);
					/* $row['price']=0;
					$key=explode('_',$row['attr_key']);
					foreach ($key as $kk=>$vv){
						$row['price']=$row['price']+$deal_attr_price[$vv];
					} */
					$group_id = $GLOBALS['user_info']['group_id'];
					
					if($group_id && $v['allow_user_discount']){
					    $group_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".$group_id);
					    	
					    if($group_info && $group_info['discount']<1){
					        $row['price'] = round($row['price']*$group_info['discount'],2);
					    }
					}
					$attr_stock_data[$row['attr_key']] = $row;
				}
				//echo "<pre>";print_r($attr_stock_data);exit;
				$deal_attr_stock_json[$v['id']] = $attr_stock_data;//json_encode($attr_stock_data);
				//echo "<pre>";print_r($deal_attr_stock_json);exit;
				
			}
			
			$relate_data['deal_attr'][$v['id']]['stock']=$v['max_bought'];
			//$relate_data['deal_attr'][$v['id']]['stock'].="件";
			//$relate_data['deal_attr'][$v['id']]['stock']=$v['stock'];
			//$deal_list[$v['id']]['current_price']=sprintf("%.2f",$v['current_price']);
			$relate_data['goodsList'][$k]['current_price']=sprintf("%.2f",$v['current_price']);
			/*
			if($v['user_min_bought']>0||$v['user_max_bought']>0){
				
				require_once(APP_ROOT_PATH."system/model/cart.php");
				$cart_result = load_cart_list(false,$is_wap,$wap_show_disable=true);
				//本商品当前会员的购物车中数量
				$deal_user_cart_count = 0;
				foreach($cart_result['cart_list'] as $key=>$value)
				{
					if($value['deal_id']==$v['id'])
					{
						$deal_user_cart_count += intval($value['number']);
					}
				}
				//本商品当前会员未付款的数量
				$deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and o.order_status = 0 and oi.deal_id = ".$v['id']." and o.is_delete = 0"));
				if($v['user_min_bought']>0){
					if($v['user_min_bought']>=($deal_user_cart_count+$deal_user_unpaid_count)){
						$relate_data['goodsList'][$k]['user_min_bought']=$relate_data['goodsList'][$k]['user_min_bought']-$deal_user_cart_count-$deal_user_unpaid_count;
					}else{
						$relate_data['goodsList'][$k]['user_min_bought']=0;
					}
				}
				if($v['user_max_bought']>0){
					//本商品当前会员已付款的数量
					$deal_user_paid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status = 2 and oi.deal_id = ".$v['id']." and o.is_delete = 0"));
					if($v['user_max_bought']>=($deal_user_cart_count+$deal_user_unpaid_count+$deal_user_paid_count)){
						$relate_data['goodsList'][$k]['user_max_bought']=$relate_data['goodsList'][$k]['user_max_bought']-$deal_user_cart_count-$deal_user_unpaid_count-$deal_user_paid_count;
					}else{
						$relate_data['goodsList'][$k]['user_min_bought']=0;
					}
				}
			}*/
			if($v['id']==$data_id){
				$relate_data['subject']=$relate_data['goodsList'][$k];
				unset($relate_data['goodsList'][$k]);
			}
			
		}
		$relate_data['goodsList']=array_values($relate_data['goodsList']);
		$root['deal_attr_stock_json'] = json_encode($deal_attr_stock_json);
        $cart_list_data=load_cart_list(0,true);
        $root['cart_total_num'] = $cart_list_data['total_data']['total_num'];
		$root['relate_data'] = $relate_data;
		$root['page_title'] = "最佳组合";
		return output($root);
	}
}
?>