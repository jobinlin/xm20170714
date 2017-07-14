<?php 
return array( 
	"DcCate"	=>	array(
		"name"	=>	"商家分类", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"商家分类列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"delete"	=>	array("name"=>"删除","action"=>"delete"),
			"set_effect"	=>	array("name"=>"设置有效性","action"=>"set_effect"),
			"set_sort"	=>	array("name"=>"设置排序","action"=>"set_sort"),

		)
	),
	"DcMenuCate"	=>	array(
		"name"	=>	"外卖预订标签", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"外卖预订标签列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"delete"	=>	array("name"=>"删除","action"=>"delete"),
			"set_effect"	=>	array("name"=>"设置有效性","action"=>"set_effect"),
			"set_sort"	=>	array("name"=>"设置排序","action"=>"set_sort"),

		)
	),
	"DcSupplierMenuCate"	=>	array(
		"name"	=>	"商家自定义宝贝分类", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"商家自定义宝贝分类列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"delete"	=>	array("name"=>"删除","action"=>"delete"),
			"set_effect"	=>	array("name"=>"设置有效性","action"=>"set_effect"),
			"set_sort"	=>	array("name"=>"设置排序","action"=>"set_sort"),

		)
	),
	"DcMenu"	=>	array(
		"name"	=>	"外卖宝贝", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"外卖宝贝列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"delete"	=>	array("name"=>"删除","action"=>"delete"),
			"set_effect"	=>	array("name"=>"设置有效性","action"=>"set_effect"),


		)
	),
	"DcRsItem"	=>	array(
		"name"	=>	"预订设置", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"预订项目列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"foreverdelete"	=>	array("name"=>"删除","action"=>"foreverdelete"),
			"table_set_effect"	=>	array("name"=>"设置有效性","action"=>"table_set_effect"),

		)
	),
	"DcRsItemTime"	=>	array(
		"name"	=>	"预订时间设置", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"预订时间列表","action"=>"index"),
			"insert"	=>	array("name"=>"添加","action"=>"insert"),
			"update"	=>	array("name"=>"编辑","action"=>"update"),
			"foreverdelete"	=>	array("name"=>"删除","action"=>"foreverdelete"),
			"time_set_effect"	=>	array("name"=>"设置有效性","action"=>"time_set_effect"),

		)
	),
	"DcBalance"	=>	array(
		"name"	=>	"外卖报表", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"销售报表","action"=>"index"),
			"bill"	=>	array("name"=>"结算报表","action"=>"bill"),
			"foreverdelete"	=>	array("name"=>"删除","action"=>"foreverdelete"),
		)
	),
	"DcOrder"	=>	array(
			"name"	=>	"外卖订单模块",
			"node"	=>	array(
					"index"	=>	array("name"=>"外卖订单列表","action"=>"index"),
					"view_order"	=>	array("name"=>"外卖订单详细页","action"=>"view_order"),
					"delete"	=>	array("name"=>"删除","action"=>"delete"),
					"order_incharge"	=>	array("name"=>"管理员收款","action"=>"order_incharge"),
					"export_csv"	=>	array("name"=>"导出csv","action"=>"export_csv"),
					"close_order"	=>	array("name"=>"关闭订单","action"=>"close_order"),
					"accept_order"	=>	array("name"=>"接单","action"=>"accept_order"),
					"over_order"	=>	array("name"=>"确认订单","action"=>"over_order"),
			)
	),
	"DcOrderHistory"	=>	array(
			"name"	=>	"外卖历史订单模块",
			"node"	=>	array(
					"index"	=>	array("name"=>"外卖订单列表","action"=>"index"),
					"view_order"	=>	array("name"=>"外卖订单详细页","action"=>"view_order"),
					"delete"	=>	array("name"=>"删除","action"=>"delete"),
					"export_csv"	=>	array("name"=>"导出csv","action"=>"export_csv"),
			)
	),	
	"DcResOrder"	=>	array(
			"name"	=>	"预订订单模块",
			"node"	=>	array(
					"index"	=>	array("name"=>"预订订单列表","action"=>"index"),
					"view_order"	=>	array("name"=>"预订订单详细页","action"=>"view_order"),
					"delete"	=>	array("name"=>"删除","action"=>"delete"),
					"order_incharge"	=>	array("name"=>"管理员收款","action"=>"order_incharge"),
					"export_csv"	=>	array("name"=>"导出csv","action"=>"export_csv"),
					"close_order"	=>	array("name"=>"关闭订单","action"=>"close_order"),
					"accept_order"	=>	array("name"=>"接单","action"=>"accept_order"),
					"over_order"	=>	array("name"=>"确认订单","action"=>"over_order"),
					"refund"	=>	array("name"=>"退款","action"=>"refund"),
					"send_coupon_sms"	=>	array("name"=>"发送电子劵","action"=>"send_coupon_sms"),
					"admin_verify"	=>	array("name"=>"管理验证电子劵","action"=>"admin_verify"),
			)
	),
	"DcResOrderHistory"	=>	array(
			"name"	=>	"预订历史订单模块",
			"node"	=>	array(
					"index"	=>	array("name"=>"预订订单列表","action"=>"index"),
					"view_order"	=>	array("name"=>"预订订单详细页","action"=>"view_order"),
					"delete"	=>	array("name"=>"删除","action"=>"delete"),
					"export_csv"	=>	array("name"=>"导出csv","action"=>"export_csv"),
			)
	),	
    "DcThirdDelivery"	=>	array(
        "name"	=>	"外卖配送设置",
        "node"	=>	array(
            "index"	=>	array("name"=>"外卖接口列表","action"=>"index"),
            "insert"	=>	array("name"=>"安装保存","action"=>"insert"),
            "update"	=>	array("name"=>"编辑执行","action"=>"update"),
            "uninstall"	=>	array("name"=>"卸载","action"=>"uninstall"),
            "setting"	=>	array("name"=>"配送全局设置","action"=>"setting"),
            "setting_update"	=>	array("name"=>"配送全局设置保存","action"=>"setting_update"),
        )
    ),
    "SupplierChargeOrder"	=>	array(
        "name"	=>	"配送账户资金记录",
        "node"	=>	array(
            "index"	=>	array("name"=>"商户充值记录","action"=>"index"),
        )
    ),
	
);
?>