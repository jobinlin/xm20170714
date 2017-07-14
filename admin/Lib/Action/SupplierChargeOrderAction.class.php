<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class SupplierChargeOrderAction extends CommonAction{

    public function index() {

        $model = M("SupplierDeliveryChargeOrder");
        $map['is_delete'] = 0;
        if(!empty($model)){
            $this->_list($model,$map);
        }
        
        $this->display ();
    }



}