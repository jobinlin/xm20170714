<?php
return array(
    "dist"	=>	array(
        "name"	=>	"驿站管理",
        "key"	=>	"dist",
        "groups"	=>	array(
            "distorder"	=>	array(
                "name"	=>	"订单管理",
                "key"	=>	"distorder",
                "nodes"	=>	array(
                    array("name"=>"驿站订单","module"=>"DistributionOrder","action"=>"distributionOrder"),
                ),
            ),
            // 配送点配置
            "distlist"	=>	array(
                "name"	=>	"社区驿站",
                "key"	=>	"distlist",
                "nodes"	=>	array(
                    array("name"=>"社区驿站列表","module"=>"Distribution","action"=>"index"),
                    array("name"=>"驿站配送点列表","module"=>"DistributionShipping","action"=>"index"),
                    array("name"=>"社区驿站提现","module"=>"Distribution","action"=>"charge_index"),
                    array("name"=>"驿站入驻申请列表","module"=>"DistributionAuth","action"=>"index"),
                ),
            ),
        ),
    ),
);
?>