<?php 
return array( 
	    //fx 分销模块 -------start---------------------------	
	"FxStatement"	=>	array(
			"name"	=>	"分销报表",
			"node"	=>	array(
					"index"	=>	array("name"=>"报表查看","action"=>"index"),
					"foreverdelete"	=>	array("name"=>"删除报表","action"=>"foreverdelete"),
			)
	),
		
    "Fxsalary"	=>	array(
        "name"	=>	"佣金设置",
        "node"	=>	array(
            "index"	=>	array("name"=>"全局分销佣金设置","action"=>"index"),
            "level_index"	=>	array("name"=>"会员等级佣金设置","action"=>"level_index"),
            "add_level"	=>	array("name"=>"添加分销等级","action"=>"add_level"),
            "edit_level"	=>	array("name"=>"编辑分销等级","action"=>"edit_level"),
            "deal_index"	=>	array("name"=>"分销商品佣金设置","action"=>"deal_index"),
            "add_deal"	=>	array("name"=>"添加分销商品","action"=>"add_deal"),
            "save"	=>	array("name"=>"保存分销数据","action"=>"save"),
            "save_1"	=>	array("name"=>"保存全局分销佣金设置","action"=>"save_1"),
            "save_2"	=>	array("name"=>"保存会员等级佣金设置","action"=>"save_2"),
            "save_3"	=>	array("name"=>"保存分销商品佣金设置","action"=>"save_3"),
        )
    ),
    
    "FxWithdraw"	=>	array(
        "name"	=>	"分销提现",
        "node"	=>	array(
            "index"	=>	array("name"=>"分销提现列表","action"=>"index"),
            "withdrawal"	=>	array("name"=>"分销提现","action"=>"withdrawal_edit"),
            "do_withdrawal"	=>	array("name"=>"保存提现","action"=>"do_withdrawal"),
            "del_withdrawal"	=>	array("name"=>"删除提现","action"=>"del_withdrawal"),
        )
    ),

    "FxOrder"	=>	array(
        "name"	=>	"分销订单",
        "node"	=>	array(
            "index"	=>	array("name"=>"分销订单列表","action"=>"index"),

        )
    ),

     "FxUser"	=>	array(
        "name"	=>	"分销会员",
        "node"	=>	array(
            "index"	=>	array("name"=>"分销会员列表","action"=>"index"),
            "edit_referrer"	=>	array("name"=>"修改推荐人","action"=>"edit_referrer"),
            "update_referrer"	=>	array("name"=>"保存推荐人","action"=>"update_referrer"),
            "foreverdelete"	=>	array("name"=>"删除分销商品","action"=>"foreverdelete"),
		    "set_effect"	=>	array("name"=>"设置会员分销状态","action"=>"set_effect"),
		    "set_deal_effect"	=>	array("name"=>"设置会员分销商品状态","action"=>"set_deal_effect"),
		    "deal_index"	=>	array("name"=>"会员分销商品列表","action"=>"deal_index"),
		    "load_seach_deal"	=>	array("name"=>"添加分销商品","action"=>"load_seach_deal"),
		    "save"	=>	array("name"=>"保存分销商品","action"=>"save"),
		    "money_log"	=>	array("name"=>"会员分销资金日志","action"=>"money_log"),
    		"log_delete"	=>	array("name"=>"删除分销资金日志","action"=>"log_delete"),
        )
    ),   
    //fx 分销模块 -------end---------------------------
    
);
?>