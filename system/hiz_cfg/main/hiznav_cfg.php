<?php 
return array(

            "Location"	=>	array(
                "name"	=>	"商户管理",
                "node"	=>	array(
                    "supplier"=>array("name"=>"商户列表","module"=>"supplier","action"=>"index"),
                    "location"=>array("name"=>"门店列表","module"=>"location","action"=>"index"),
                )
            ),
			"Order"	=>	array(
					"name"	=>	"订单管理",
					"node"	=>	array(
							//"dealo"=>array("name"=>"团购订单列表","module"=>"dealo","action"=>"index"),
							//"goodso"=>array("name"=>"商品订单列表","module"=>"goodso","action"=>"index"),
							"deal_order_self_order"=>array("name"=>"自营-驿站订单","module"=>"deal_order","action"=>"self_order"),
							"deal_order_shop_order"=>array("name"=>"商城订单","module"=>"deal_order","action"=>"shop_order"),
							"deal_order_tuan_order"=>array("name"=>"团购订单","module"=>"deal_order","action"=>"tuan_order"),
							/* "dcorder"=>array("name"=>"外卖订单","module"=>"dcorder","action"=>"index"),
							"dcresorder"=>array("name"=>"预订订单","module"=>"dcresorder","action"=>"index"), */
					        "storepayorder"=>array("name"=>"到店付","module"=>"storepayorder","action"=>"index"),
					)
			),
            "User" => array(
                     "name"=>"会员管理",
                     "node"=>array(
                            "user"=>array("name"=>"会员列表","module"=>"user","action"=>"user_list"),
                            "fx"=>array("name"=>"网宝列表","module"=>"fx","action"=>"index")
                     )

            ),
			"Bills"	=>	array(
					"name"	=>	"资金账户",
					"node"	=>	array(
							"balance"=>array("name"=>"资金明细","module"=>"balance","action"=>"detail"),
							"withdrawal"=>array("name"=>"账户提现","module"=>"withdrawal","action"=>"index"),
							"bankinfo"=>array("name"=>"银行账户","module"=>"bankinfo","action"=>"index"),
					)
			),
			'Distribution' => array(
				'name' => '驿站管理',
				'node' => array(
					'distribution' => array('name' => '驿站列表', 'module' => 'distribution', 'action' => 'index'),
					)
			)

		);
				
?>