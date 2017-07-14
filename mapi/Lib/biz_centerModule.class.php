<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_centerApiModule extends MainBaseApiModule
{

    /**
     * 	我的（个人中心）
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  
     *  有权限的情况下返回以下内容
     *  
        [item] => Array
            (
                [id] => 8   ：int 商户id
                [supplier_id] => 23   ：int 商户编号
                [account_name] => fanwe ：商户用户名
                [name] => 桥亭活鱼小镇       ：商户名
                [money] => 6779       ：商户可提现余额
                [mobile] => 13344455555
                [location_count] => 3   ：门店个数 
                [allow_refund] => 1  ：是否支持商户退款（1支持，0不支持）
                [allow_publish_verify] => 1   ：是否支持自动发布（1支持，0不支持）
                [publish_verify_balance] => 15%  ：自动审核时的结算费用率（[allow_publish_verify]为1时显示）
                [agency_name] => 优秀的代理商      ：代理商名称
            )
    
        [page_title] => 我的

     */
	public function index(){
	    
	    /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
        //返回商户权限
       if(!check_module_auth()){
           $root['is_auth'] = 0;
           return output($root,0,"没有操作权限");
       }else{
           $root['is_auth'] = 1;
       }
        
	    $list = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id=".$supplier_id);
        
        //我的中心数据处理
	    $item = array();
	    $item['id'] = $account_info['id'];
	    $item['supplier_id'] = $supplier_id;
	    $item['account_name'] = $account_info['account_name'];
	    $item['preview'] = get_abs_img_root(get_spec_image($list['preview'],27,27,1));
        $item['name'] = $list['name'];
		$item['money'] = round($list['money'],2);
		$item['mobile'] = substr_replace($account_info['mobile'],'****',3,4);
		$item['location_count'] = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."supplier_location where is_effect=1 and id in (".implode(",", $account_info['location_ids']).")");
		$item['allow_refund'] = $list['allow_refund'];
		$item['allow_publish_verify'] = $list['allow_publish_verify'];
		if($list['allow_publish_verify'] == 1){
		    $item['publish_verify_balance'] = round($list['publish_verify_balance']*100)."%";
		}else{
		    $item['publish_verify_balance'] = "未开启自动发布";
		}
		if($list['agency_id'] != 0){
		    $item['agency_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."agency where id=".$list['agency_id']);
		}else{
		    $item['agency_name'] = "暂无";
		}
        if (isOpenXN()) {
            $item['open_xn_talk'] = $list['open_xn_talk'];
        }
	    $root['item'] = $item?$item:array();
	    $root['page_title'] = "我的";
        return output($root);
    }
 
}
?>

