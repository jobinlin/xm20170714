<?php 
return array(
		"mobile"	=>	array(
				"name"	=>	"移动平台",
				"key"	=>	"mobile",
				"groups"	=>	array(						
						"weixinconf"	=>	array(
								"name"	=>	"微信平台",
								"key"	=>	"WeixinConf",
								"nodes"	=>	array(
										array("name"=>"公众号开放平台","module"=>"WeixinAccount","action"=>"index"),
										array("name"=>"公众号消息","module"=>"WeixinTemplate","action"=>"index"),
// 								
						
								),
						),
						
				),
		),
		
);
?>