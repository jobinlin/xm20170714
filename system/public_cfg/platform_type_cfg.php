<?php
/*
 * 所属发布平台配置
 */
return array(
    "platform" => array(
        "name" => "平台自营",
        "key" => "platform",
        "value"=>"1",
        "delivery_type"	=>"1,2,3", //配送方式(对应配送方式配置表逗号分隔)
        "default_delivery"=>"3" //默认配送方式
    ),
    "supplier" => array(
        "name" => "商户",
        "key" => "supplier",
        "value"=>"2",
        "delivery_type"	=>"1,2", //配送方式(对应配送方式配置表逗号分隔)
        "default_delivery"=>"1" //默认配送方式
    )
);