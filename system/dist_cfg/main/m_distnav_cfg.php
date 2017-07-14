<?php 
return array(
            "Order"	=>	array(
                "name"	=>	"订单管理",
                "node"	=>	array(
                    "undeliver"=>array("name"=>"待发货","module"=>"undeliver","action"=>"index"),
                    "unarrive"=>array("name"=>"待收货","module"=>"unarrive","action"=>"index"),
                    "arrived"=>array("name"=>"已完成","module"=>"arrived","action"=>"index"),
                    "goodso"=>array("name"=>"全部订单","module"=>"goodso","action"=>"index"),
                )
            ),
            "More"=>   array(
                "name"=>    "更多",
                "node"=>  array(
                    "more" => array("name"=>"基本信息","module"=>"more","action"=>"index"),
                    "shipping" => array("name"=>"配送设置","module"=>"shipping","action"=>"index"),
                    "user" => array("name"=>"退出","module"=>"user","action"=>"loginout"),
                )
            )
		);
				
?>