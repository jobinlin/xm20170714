<?php
/**
 * @desc      驿站个人中心
 * @author    
 * @since       
 */
class dist_centerApiModule extends MainBaseApiModule{
    public function index(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=". $GLOBALS['dist_info']['id']);
        /*业务逻辑*/
         $root['dist_user_status'] = $dist_info?1:0;
         if (empty($dist_info)){
             return output($root,0,"驿站用户未登录");
         }

        //我的中心数据处理
        $item = array();
        $item = $dist_info;
        $item['name'] = strim($item['name']);
        $item['money'] = number_format($item['money'],2);
        $root['item'] = $item?$item:array();
        $root['page_title'] = $item['name'];
        return output($root);
    }

}
