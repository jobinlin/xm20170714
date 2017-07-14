<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_manageApiModule extends MainBaseApiModule
{

    /**
     * 	订单主页面
     *
     * 	 输入:
     *  无
     *
     *  输出:
        
     */
	public function index(){
	    /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id=$account_info['supplier_id'];
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商城订单权限
        if(!check_module_auth('goodso')){
            $root['goodso_auth'] = 0;
            $root['goodso_auth_info'] = "没有操作权限";
        }else{
            $root['goodso_auth'] = 1;
        }
        
        //返回团购订单权限
        if(!check_module_auth('dealo')){
            $root['dealo_auth'] = 0;
            $root['dealo_auth_info'] = "没有操作权限";
        }else{
            $root['dealo_auth'] = 1;
        }
        
        //返回买单订单权限
        if(!check_module_auth('store_pay_order')){
            $root['store_pay_order_auth'] = 0;
            $root['store_pay_order_auth_info'] = "没有操作权限";
        }else{
            $root['store_pay_order_auth'] = 1;
        }
        
        //返回退款维权权限，需团购和订单同时有权限
        if(!check_module_auth("goodso")||!check_module_auth("dealo")){
            $root['refund_order_auth'] = 0;
            $root['refund_order_auth_info'] = "没有操作权限";
        }else{
            $root['refund_order_auth'] = 1;
        }
        
        /* 商城待发货订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON doi.deal_id = l.deal_id
			WHERE l.location_id IN (".implode(",",$account_info['location_ids']).") AND do.is_delete = 0 AND do.type = 6 AND do.pay_status = 2 ";
         
        $shop_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=0 and doi.is_pick=0 and ( doi.refund_status=0 or doi.refund_status=3 ) and doi.is_shop=1 ";
        //echo $shop_count_sql;exit;
        $shop_count = intval($GLOBALS['db']->getOne($shop_count_sql));
        $root['shop_count'] = $shop_count;
       
        /* 退款维权商品数量统计 */

        $refund_count_sql = "select count(*) from (select DISTINCT (doi.id),dc.message_id AS msg_id from ".DB_PREFIX."deal_order_item as doi
				left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id
				LEFT JOIN ".DB_PREFIX."deal_coupon AS dc ON doi.id = dc.order_deal_id
				left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id ".
        				" where l.location_id in (".implode(",",$account_info['location_ids']).") and do.is_delete = 0 and (do.type = 5 or do.type =6 )and do.pay_status = 2 and (
                (doi.refund_status=1 and doi.message_id >0 and doi.is_coupon=0 )
                or
                (dc.refund_status =1 and dc.message_id >0 and doi.is_coupon=1)  
                ) ) aa";
        //echo $refund_count_sql;exit;
        $refund_count = intval($GLOBALS['db']->getOne($refund_count_sql));
        $root['refund_count'] = $refund_count;
        
        
        /* 获取门店及外卖订单统计 */
        $lib=$account_info['location_ids'];
        $locations=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."supplier_location where supplier_id=".$supplier_id." and id in(".implode(",", $lib).")");
        foreach($locations as $k=>$v){
            $location_dcorder_count=intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where supplier_id=".$supplier_id." and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id =".$v['id']));
            $location_rsdcorder_count=intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_order where supplier_id=".$supplier_id." and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=1 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id =".$v['id']));
            $locations[$k]['location_dcorder_count']=$location_dcorder_count;
            $locations[$k]['location_rsdcorder_count']=$location_rsdcorder_count;
        }
        
        $root['locations'] = $locations;
        //待接单订单
        $dc_order_count="select count(*) from ".DB_PREFIX."dc_order where supplier_id=".$supplier_id." and is_delivery_cancel=0 and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1) and location_id in(".implode(",", $lib).")";
        $dc_order=intval($GLOBALS['db']->getOne($dc_order_count));
        $root['dc_order_count'] = $dc_order;
        //confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1)
        //预定待接单统计
        $dc_resorder_count="select count(*) from ".DB_PREFIX."dc_order where supplier_id=".$supplier_id." and confirm_status=0 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=1 and pay_status=1 and location_id in(".implode(",", $lib).")";
        $dc_resorder=intval($GLOBALS['db']->getOne($dc_resorder_count));
        $root['dc_resorder_count'] = $dc_resorder;
        
        //异常订单
        if(IS_DC_DELIVERY){
            $dc_location_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location where is_effect=1 and is_dc=1");
            
            $dc_abnormal_count="select count(*) from ".DB_PREFIX."dc_order where  is_delivery_cancel=1 and is_cancel=0 and refund_status=0 and order_status=0 and type_del=0 and is_rs=0 and ((payment_id=0 and pay_status=1) or payment_id=1)";
            $dc_abnormal_order=intval($GLOBALS['db']->getOne($dc_resorder_count));
            $root['dc_abnormal_count'] = $dc_abnormal_order;
            
            $root['is_dc_delivery']=IS_DC_DELIVERY;
            $root['dc_location_count']=$dc_location_count;
        }
        
        $root['page_title'] = "订单管理";
        return output($root);
    }
    
    
   
}
?>

