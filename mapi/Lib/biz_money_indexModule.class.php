<?php

/**
 * 用户账户管理首页
 *
 * @author jobinlin
 */
class biz_money_indexApiModule extends MainBaseApiModule
{
    
    /**
     * 用户账户管理首页
     * 输入：
     * 输出： money ：float 用余额
     */
    public function index()
	{
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
        $supplier_info=$GLOBALS['db']->getRow("select id,name,money,bank_name,bank_info from  ".DB_PREFIX."supplier where id=".$supplier_id);
        $supplier_info['money'] = round($supplier_info['money'],2);
        $root['supplier_info'] = $supplier_info;
		$root['page_title'].="余额";
		return output($root);
	}
  
}
