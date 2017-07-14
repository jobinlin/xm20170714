<?php 
return array(			
			//fx 
			"Fx"	=>	array(
					"name"	=>	"分销中心",
					"node"	=>	array(
							"uc_fx"=>array("name"=>"我的分销","module"=>"uc_fx","action"=>"index"),  //查看分销等级，与该等级的通会佣金设置，查看上架的分销商品，以及相关的销量情况，查看分销报表
							"uc_fx_withdraw"=>array("name"=>"分销提现","module"=>"uc_fx_withdraw","action"=>"index"),
							"uc_fx_invite"=>array("name"=>"分销推荐","module"=>"uc_fx_invite","action"=>"index"), //查看分销的会员上下级关系

					)
			),
		
		);
				
?>