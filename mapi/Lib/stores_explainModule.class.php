<?php

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class stores_explainApiModule extends MainBaseApiModule {

    /**
     * 用户到店付说明
     * 输入
     * location_id int 门店ID
     * 
     * 输出
     * data :array
     * 结构如下:
     * 
        Array
        (
            [explain] => Array 
                (
                    [supplier_id] => 21    商户id
                    [store_pay_explain] => 买单规则说明     商户买单说明
                )
            [page_title] => 买单说明   标题
        )
     * 
     */
    public function index() {
        //获取页面参数
        $location_id = intval($GLOBALS['request']['location_id']);
        $supplier_id = $GLOBALS['db']->getOne("select supplier_id from " . DB_PREFIX . "supplier_location where id=" . $location_id);
        $explain = $GLOBALS['db']->getRow("select id supplier_id,store_pay_explain from " . DB_PREFIX . "supplier where id=" . $supplier_id);
        if ($explain && !empty($explain['store_pay_explain'])) {
            $explain['store_pay_explain'] = str_replace('./public', SITE_DOMAIN.APP_ROOT.'/public', $explain['store_pay_explain']);
        }
        $root['explain'] = $explain;
        $root['page_title'] = '买单说明';
        return output($root);
    }
    
  
    
}

?>