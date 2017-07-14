<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class biz_locationApiModule extends MainBaseApiModule
{
    /**
     * 	 消费券验证接口
     *
     * 	输入:无
     *
     *  输出:
    Array
    (
        [biz_user_status] => 1          int             商户登录状态 0未登录/1已登录
        [is_auth] => 1                  int             模块操作权限 0没有权限 / 1有权限
        [item] => Array
        (
            [0] => Array
            (
                [id] => 113
                [is_main] => 1          tinyint(1)      是否为默认门店(总店)
                [name] => 长胖胖1        varchar(255)    门店名称
            )

        )

        [page] => Array
        (
            [page] => 1
            [page_total] => 1
            [page_size] => 20
            [data_total] => 1
        )

        [page_title] => 门店列表
    )
     */
    public function index()
    {
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];

        /*获取参数*/
        $page = intval($GLOBALS['request']['page']); //分页
        $page=$page==0?1:$page;

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

        //分页
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;

        $supplier_id = $account_info[supplier_id];//商户id

        $sql = "select id,is_main,name from ".DB_PREFIX."supplier_location where supplier_id={$supplier_id} and is_effect=1 order by sort desc limit ".$limit;
        $sql_count = "select count(*) from ".DB_PREFIX."supplier_location where supplier_id={$supplier_id} and is_effect=1 order by sort desc limit ".$limit;

        $list = $GLOBALS['db']->getAll($sql);
        $count = $GLOBALS['db']->getOne($sql_count);
        $page_total = ceil($count/$page_size);
        //end 分页

        $root['item'] = $list;
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);

        $root['page_title'] = "门店列表";

        return output($root);
    }

    /**
     * 消费券验证接口
     *
     * 	输入:无
     *
     *  输出:
     * Array
    (
        [biz_user_status] => 1
        [is_auth] => 1
        [item] => Array
        (
        [0] => Array
        (
            [name] => 长胖胖1
            [city_id] => 15
            [address] =>
            [contact] =>
            [tel] =>
            [open_time] => 8
            [is_main] => 1
            [open_store_payment] => 1
            [city_name] => 福州
        )

    )

    [page_title] => 门店信息
    [ctl] => biz_location
    [act] => detail
    [status] => 1
    [info] =>
    [city_name] => 福州
    [return] => 1
    [sess_id] => rtlkhuppicdkqi94mnihcvsaq1
    [ref_uid] =>
    )
     */
    public function detail()
    {
        /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $id = intval($_GET['id']);
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

        $sql = "select name,city_id,address,contact,tel,open_time,is_main,open_store_payment from ".DB_PREFIX."supplier_location where id={$id}";
        $list = $GLOBALS['db']->getAll($sql);

        foreach($list as $k => $v){
            $list[$k]['city_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_city where id={$v['city_id']}");
        }

        $root['item'] = $list['0'];
        $root['page_title'] = "门店信息";

        return output($root);
    }
}
?>