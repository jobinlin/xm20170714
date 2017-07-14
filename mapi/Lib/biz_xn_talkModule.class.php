<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_xn_talkApiModule extends MainBaseApiModule
{

    /**
     * 	
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
    
	    $xn_info_sql = "select open_xn_talk,xn_talk_id,xn_talk_login_id,xn_talk_pwd from ".DB_PREFIX."supplier where id=".$supplier_id;
        $xn_info = $GLOBALS['db']->getRow($xn_info_sql);
        $root['item'] = $xn_info;

	    $root['page_title'] = "小能帐户";
        return output($root);
    }
 
}
?>

