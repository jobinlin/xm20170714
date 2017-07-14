<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class DistributionOrderAction extends DealOrderAction{
    function __construct()
    {
        parent::__construct();
        if(!IS_OPEN_DISTRIBUTION){
            $this->error (l("请先开启驿站功能"),0);
        }
    }
}
?>