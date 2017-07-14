<?php

/**
 * 用户账户管理首页
 *
 * @author jobinlin
 */
class dist_money_indexApiModule extends MainBaseApiModule
{
    
    /**
     * 驿站用户账户管理首页
     * 输入：
     * 输出： money ：float 用余额
     */
    public function index()
	{
	    /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=". $GLOBALS['dist_info']['id']);
	    /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"用户未登录");
        }

        $dist_info['money'] = number_format($dist_info['money'],2);
        $dist_info['bank_name'] = strim($dist_info['bank_name']);
        $root['dist_info'] = $dist_info;
		$root['page_title'].="余额";
		return output($root);
	}
  
}
