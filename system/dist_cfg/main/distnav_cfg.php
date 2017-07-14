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
        "System"	=>	array(
            "name"	=>	"系统设置",
            "node"	=>	array(
                "setting"=>array("name"=>"基本信息","module"=>"setting","action"=>"index"),
                "shipping" => array("name"=>"配送设置","module"=>"shipping","action"=>"index"),
            ),
        )
			/* "Verify"	=>	array(
					"name"	=>	"验证管理",
					"node"	=>	array(
							"dealv"=>array("name"=>"消费券验证","module"=>"dealv","action"=>"index","service_type"=>array(0,1,2),"staff"=>1),
					)
			),
			"Order"	=>	array(
					"name"	=>	"订单管理",
					"node"	=>	array(
							"dealo"=>array("name"=>"服务订单列表","module"=>"dealo","action"=>"index","service_type"=>array(0,1,2),"staff"=>1),
							"goodso"=>array("name"=>"商品订单列表","module"=>"goodso","action"=>"index","service_type"=>array(0,1)),
					)
			),
			"Review"	=>	array(
					"name"	=>	"点评管理",
					"node"	=>	array(
							"dealr"=>array("name"=>"项目点评","module"=>"dealr","action"=>"index","service_type"=>array(0,1)),
							"storer"=>array("name"=>"门店点评","module"=>"storer","action"=>"index","service_type"=>array(0,1)),
							"staffr"=>array("name"=>"服务人员点评","module"=>"staffr","action"=>"index","service_type"=>array(0,1,2)),
					)
			),
			"Project"	=>	array(
					"name"	=>	"项目管理",
					"node"	=>	array(
							"dealservice"=>array("name"=>"服务项目","module"=>"dealservice","action"=>"index","service_type"=>array(0,1)),
							"dealshop"=>array("name"=>"商品项目","module"=>"dealshop","action"=>"index","service_type"=>array(0,1)),
					)
			),
			"League"	=>	array(
					"name"	=>	"项目加盟",
					"node"	=>	array(
							"league"=>array("name"=>"项目加盟","module"=>"league","action"=>"index","service_type"=>array(2)),
							"arrange"=>array("name"=>"加盟排期","module"=>"arrange","action"=>"index","service_type"=>array(2)),
					)
			),
			"Bills"	=>	array(
					"name"	=>	"财务管理",
					"node"	=>	array(
							"balance"=>array("name"=>"财务报表","module"=>"balance","action"=>"index","service_type"=>array(0,1,2)),
							"statistics"=>array("name"=>"项目报表","module"=>"statistics","action"=>"index","service_type"=>array(0,1)),
							"withdrawal"=>array("name"=>"商户提现","module"=>"withdrawal","action"=>"index","service_type"=>array(0,1,2)),
							"bankinfo"=>array("name"=>"银行账户","module"=>"bankinfo","action"=>"index","service_type"=>array(0,1,2)),
					)
			),
			"Wx"	=>	array(
					"name"	=>	"微信公众平台",
					"node"	=>	array(
							"wxconf"=>array("name"=>"公众号接入","module"=>"wxconf","action"=>"index","service_type"=>array(0,1)),
							"wxnav"=>array("name"=>"公众号菜单设置","module"=>"wxnav","action"=>"index","service_type"=>array(0,1)),
							"wxreply"=>array("name"=>"默认回复设置","module"=>"wxreply","action"=>"index","service_type"=>array(0,1)),
							"wxfocus"=>array("name"=>"关注回复设置","module"=>"wxfocus","action"=>"index","service_type"=>array(0,1)),
							"wxnews"=>array("name"=>"图文回复设置","module"=>"wxnews","action"=>"index","service_type"=>array(0,1)),
							"wxtext"=>array("name"=>"文本回复设置","module"=>"wxtext","action"=>"index","service_type"=>array(0,1)),
							"wxlbs"=>array("name"=>"LBS回复设置","module"=>"wxlbs","action"=>"index","service_type"=>array(0,1)),
					),
			),
			"System"	=>	array(
					"name"	=>	"系统设置",
					"node"	=>	array(							
							"setting"=>array("name"=>"基本信息","module"=>"setting","action"=>"index","service_type"=>array(0,1,2)),
							"location"=>array("name"=>"门店列表","module"=>"location","action"=>"index","service_type"=>array(0,1)),
							"staff"=>array("name"=>"服务人员","module"=>"staff","action"=>"index","service_type"=>array(0,1)),
							"delivery"=>array("name"=>"异地运费模板","module"=>"delivery","action"=>"index","service_type"=>array(0,1)),
							"deliverylbs"=>array("name"=>"同城运费模板","module"=>"deliverylbs","action"=>"index","service_type"=>array(0,1)),
							"account"=>array("name"=>"子账户列表","module"=>"account","action"=>"index","service_type"=>array(0,1)),
							),
					) */
		);
				
?>