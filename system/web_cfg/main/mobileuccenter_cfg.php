<?php 
return array(
			"OrderManager"	=>	array(
					"name"	=>	"我的订单",
					"layout_type"=>	1,
					"menu"	=>	array(
							array("name"=>"商城单","type"=>401,"module"=>"uc_order","action"=>"index",'data'=>array(),'num'=>0),
							array("name"=>"团购单","type"=>402,"module"=>"uc_order","action"=>"index",'data'=>array('tuan'=>1),'num'=>0),
							array("name"=>"到店买单","type"=>307,"module"=>"uc_store_pay_order","action"=>"index",'data'=>array(),'num'=>0),
							array("name"=>"退款/售后","type"=>306,"module"=>"uc_order","action"=>"refund_list",'data'=>array(),'num'=>0),
					        array("name"=>"外卖单","type"=>403,"module"=>"dc_dcorder","action"=>"index",'data'=>array(),'num'=>0),
							array("name"=>"预定单","type"=>404,"module"=>"dc_rsorder","action"=>"index",'data'=>array(),'num'=>0),
							array("name"=>"活动报名","type"=>405,"module"=>"uc_event","action"=>"index",'data'=>array(),'num'=>0),
							array("name"=>"我的评价","type"=>406,"module"=>"uc_review","action"=>"index",'data'=>array(),'num'=>0),
					)
			),
			"fx_nav"	=>	array(
					"name"	=>	"分销管理",
					"layout_type"=>	1,
					"menu"	=>	array(
							array("name"=>"小店","type"=>407,"module"=>"uc_fx","action"=>"index",'data'=>array(),'num'=>0,'imgKey' => 'uc_fx'),
							array("name"=>"市场","type"=>408,"module"=>"uc_fx","action"=>"deal_fx",'data'=>array(),'num'=>0,'imgKey' => 'deal_fx'),
							array("name"=>"提现","type"=>409,"module"=>"uc_fxwithdraw","action"=>"index",'data'=>array(),'num'=>0,'imgKey' => 'uc_fxwithdraw'),
							array("name"=>"推荐","type"=>410,"module"=>"uc_fxinvite","action"=>"index",'data'=>array(),'num'=>0,'imgKey' => 'uc_fxinvite')
					),
					"list"	=>	array(
							array("name"=>"分销管理","type"=>411,"module"=>"uc_fx","action"=>"vip_buy",'data'=>array(),'tip'=>'开通分销资格'),
					),
			),
			"Service"	=>	array(
					"name"	=>	"会员服务",
					"layout_type"=>	2,
					"list"	=>	array(
							array("name"=>"分享有礼","type"=>204,"module"=>"uc_share","action"=>"index",'data'=>array(),'tip'=>''),  
							array("name"=>"积分商城","type"=>412,"module"=>"scores_index","action"=>"index",'data'=>array(),'tip'=>'签到领积分'),  
							array("name"=>"我的收藏","type"=>317,"module"=>"uc_collect","action"=>"index",'data'=>array(),'tip'=>''),
							array("name"=>"我的抽奖","type"=>413,"module"=>"uc_lottery","action"=>"index",'data'=>array(),'tip'=>'')
					)
			),
			"Administration"	=>	array(
					"name"	=>	"会员管理",
					"layout_type"=>	1,
					"list"	=>	array(
							array("name"=>"收货地址","type"=>316,"module"=>"uc_address","action"=>"index",'data'=>array(),'tip'=>''),
							array("name" => "客服中心", "type" => 414, "module" => "help", "action" => "index", 'data' => array(), 'tip' => '')
					)
			),
		
		);
				
?>