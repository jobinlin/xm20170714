<?php 
return array(
//fx 营销菜单位于订单菜单后面
	"marketing"	=>	array(
			"name"	=>	"营销管理",
			"key"	=>	"marketing",
			"groups"	=>	array(
					"fx"	=>	array(
							"name"	=>	"分销管理",
							"key"	=>	"fx",
							"nodes"	=>	array(
									array("name"=>"推荐会员佣金设置","module"=>"FxSalary","action"=>"index"),
									array("name"=>"推荐商家佣金设置","module"=>"FxSalary","action"=>"ref_salary"),
									array("name"=>"推荐会员分销订单","module"=>"FxOrder","action"=>"index"),
									array("name"=>"推荐商家分销订单","module"=>"FxOrder","action"=>"ref_index"),
									array("name"=>"推荐买单分销订单","module"=>"FxOrder","action"=>"store_pay_index"),
									// array("name"=>"分销报表","module"=>"FxStatement","action"=>"index"),
									array("name"=>"分销会员","module"=>"FxUser","action"=>"index"),
									array("name"=>"分销提现","module"=>"FxWithdraw","action"=>"index"),
							        array("name"=>"分销资质设置","module"=>"FxQualification","action"=>"index"),
							        array("name"=>"缴费明细","module"=>"FxDetail","action"=>"index"),
							),
					),
					'fx_report' => array(
							'name' => '分销报表',
							"key" => 'fx_report',
							'nodes' => array(
									// array('name' => '营业额报表', 'module' => 'FxStatement', 'action' => 'sales'),
									// array('name' => '分销佣金报表', 'module' => 'FxStatement', 'action' => 'salary'),
									array('name' => '推广会员佣金报表', 'module' => 'FxStatement', 'action' => 'promote'),
									array('name' => '推广商家佣金报表', 'module' => 'FxStatement', 'action' => 'ref_promote'),
									array('name' => '推广买单佣金报表', 'module' => 'FxStatement', 'action' => 'store_payment_promote'),
									array('name' => '分销提现报表', 'module' => 'FxStatement', 'action' => 'withdraw'),
									array('name' => '推荐返佣报表', 'module' => 'FxStatement', 'action' => 'refer'),
								)
						),
					'score_marketing' => array(
							'name' => '积分营销',
							"key" => 'score_marketing',
							'nodes' => array(
									array('name' => '积分抵现', 'module' => 'ScoreMarketing', 'action' => 'score_purchase'),
									array('name' => '积分购买', 'module' => 'ScoreMarketing', 'action' => 'score_recharge'),
								)
						),
					"youhui_marketing"	=>	array(
							"name"	=>	"优惠营销",
							"key"	=>	"youhui_marketing",
							"nodes"	=>	array(
									array("name"=>"自营优惠券","module"=>"Youhui","action"=>"index")
							),
					),
			),
	),
);
?>