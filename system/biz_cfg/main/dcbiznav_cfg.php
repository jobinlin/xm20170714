<?php 
return array(
			"DcOrder"	=>	array(
					"name"	=>	"外卖预订管理",
			        "iconfont"=>"&#xe60a;",
					"node"	=>	array(
							"dcallorder_index"=>array("name"=>"新订单","module"=>"dcallorder","action"=>"index",'is_pc'=>1),
							"dcorder_index"=>array("name"=>"外卖订单","module"=>"dcorder","action"=>"index",'is_pc'=>1),
							"dcresorder_index"=>array("name"=>"预订订单","module"=>"dcresorder","action"=>"index",'is_pc'=>1),
					        "dcxorder_index"=>array("name"=>"异常订单","module"=>"dcxorder","action"=>"index",'is_pc'=>1),
							"dcborder_index"=>array("name"=>"对账单","module"=>"dcborder","action"=>"index",'is_pc'=>1),
							"dcreminder_index"=>array("name"=>"催单记录","module"=>"dcreminder","action"=>"index",'is_pc'=>1),
							"dcverify_index"=>array("name"=>"预订电子券验证","module"=>"dcverify","action"=>"index",'is_pc'=>1),
					)
			),			
			"Location"	=>	array(
					"name"	=>	"门店管理",
			        "iconfont"=>"&#xe609;",
					"node"	=>	array(
							"dc_index"=>array("name"=>"外卖预订设置","module"=>"dc","action"=>"index",'is_pc'=>1),
					        "delivery_setting_index"=>array("name"=>"配送设置","module"=>"delivery_setting","action"=>"index",'is_pc'=>1),

					)
			),

)
				
?>