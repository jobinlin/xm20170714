<?php 
return array(
			"Verify"	=>	array(
					"name"	=>	"验证管理",
					"node"	=>	array(
							"dealv_index"=>array("name"=>"验证消费券","module"=>"dealv","action"=>"index", "is_pc"=>1),
							"youhuiv_index"=>array("name"=>"验证优惠券","module"=>"youhuiv","action"=>"index", "is_pc"=>1),
							"eventv_index"=>array("name"=>"验证活动报名","module"=>"eventv","action"=>"index", "is_pc"=>1),
					)
			),
    
            "shop_verify" => array(
					"name"	=>	"消费券验证管理",
					"node"	=>	array(
							"shop_verify_index_check"=> array("name"=>"简单验证优惠券", "module"=>"shop_verify", "action"=>"index_check", "is_pc"=>0),
							"shop_verify_coupon_check"=> array("name"=>"完整验证消费券", "module"=>"shop_verify", "action"=>"coupon_check", "is_pc"=>0),
							"shop_verify_coupon_use"=> array("name"=>"使用消费券", "module"=>"shop_verify", "action"=>"coupon_use", "is_pc"=>0),
							"shop_verify_search_log"=> array("name"=>"搜索消费券", "module"=>"shop_verify", "action"=>"search_log", "is_pc"=>0),
					        "shop_verify_coupon_use_log"=> array("name"=>"消费券验证记录", "module"=>"shop_verify", "action"=>"coupon_use_log", "is_pc"=>0),
					)
            ),
    
			"Order"	=>	array(
					"name"	=>	"订单管理",
					"node"	=>	array(
							"dealo_index"=>array("name"=>"团购订单列表","module"=>"dealo","action"=>"index", "is_pc"=>1),
							"goodso_index"=>array("name"=>"商品订单列表","module"=>"goodso","action"=>"index", "is_pc"=>1),
							"youhuio_index"=>array("name"=>"优惠券下载记录","module"=>"youhuio","action"=>"index", "is_pc"=>1),
							"evento_index"=>array("name"=>"活动报名","module"=>"evento","action"=>"index", "is_pc"=>1),
					        "storepayorder_index"=>array("name"=>"到店付","module"=>"storepayorder","action"=>"index", "is_pc"=>1),
					        "store_pay_order_index"=>array("name"=>"买单记录","module"=>"store_pay_order","action"=>"index", "is_pc"=>0),
					)
			),

			"Review"	=>	array(
					"name"	=>	"点评管理",
					"node"	=>	array(
							"dealr_index"=>array("name"=>"消费评价","module"=>"dealr","action"=>"index", "is_pc"=>1),
							"youhuir_index"=>array("name"=>"优惠券点评","module"=>"youhuir","action"=>"index", "is_pc"=>1),
							"eventr_index"=>array("name"=>"活动点评","module"=>"eventr","action"=>"index", "is_pc"=>1),
							"storer_index"=>array("name"=>"门店点评","module"=>"storer","action"=>"index", "is_pc"=>1),
					)
			),

			"Project"	=>	array(
					"name"	=>	"项目管理",
					"node"	=>	array(
							"deal_index"=>array("name"=>"团购","module"=>"deal","action"=>"index", "is_pc"=>1),
							"goods_index"=>array("name"=>"商品","module"=>"goods","action"=>"index", "is_pc"=>1),
							"youhui_index"=>array("name"=>"优惠券","module"=>"youhui","action"=>"index", "is_pc"=>1),
							"event_index"=>array("name"=>"活动","module"=>"event","action"=>"index", "is_pc"=>1),
					)
			),

			"Bills"	=>	array(
					"name"	=>	"财务管理",
					"node"	=>	array(
							"balance_index"=>array("name"=>"财务报表","module"=>"balance","action"=>"index", "is_pc"=>1),
							"withdrawal_index"=>array("name"=>"商户提现","module"=>"withdrawal","action"=>"index", "is_pc"=>1),
							"bankinfo_index"=>array("name"=>"银行账户","module"=>"bankinfo","action"=>"index", "is_pc"=>1),
							'invoiceConf_index' => array('name' => '开票设置', 'module' => 'invoiceconf', 'action' => 'index', 'is_pc' => 1),
					)
			),

			"Location"	=>	array(
					"name"	=>	"门店管理",
					"node"	=>	array(
							"location_index"=>array("name"=>"门店列表","module"=>"location","action"=>"index", "is_pc"=>1),
							"location_detail"=>array("name"=>"门店详情","module"=>"location","action"=>"detail", "is_pc"=>0),
					)
			),

			"Account"	=>	array(
					"name"	=>	"账户管理",
					"node"	=>	array(
                            "carriage_template_index"=>array("name"=>"配送模板","module"=>"carriage_template","action"=>"index", "is_pc"=>1),
							"account_index"=>array("name"=>"子账户列表","module"=>"account","action"=>"index", "is_pc"=>1),
							"setting_index"=>array("name"=>"微店设置","module"=>"setting","action"=>"index", "is_pc"=>1),
					)
			),

            "User"	=>	array(
					"name"	=>	"个人中心",
					"node"	=>	array(
							"center_index"=>array("name"=>"我的","module"=>"center","action"=>"index", "is_pc"=>0),
							"money_index_index"=>array("name"=>"余额","module"=>"money_index","action"=>"index", "is_pc"=>0),
							"withdrawal_money_log"=>array("name"=>"资金明细","module"=>"withdrawal","action"=>"money_log", "is_pc"=>0),
							"withdrawal_withdraw_log"=>array("name"=>"提现明细","module"=>"withdrawal","action"=>"withdraw_log", "is_pc"=>0),
					)
			)


		);
				
?>