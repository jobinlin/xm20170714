<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class IndexAction extends AuthAction{
	//首页
    public function index(){
		$this->display();
    }
    

    //框架头
	public function top()
	{
		$navs = require_once(APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/admnav_cfg.php");	
		if(OPEN_WEIXIN)
		{
			if(WEIXIN_TYPE=="platform")
			{
				$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/wxadmnav_platform_cfg.php";
			}
			else
			{
				$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/wxadmnav_cfg.php";
			}
			$navs = array_merge_admnav($navs, $config_file);
		}
		//if(defined("FX_LEVEL"))
		//{
			$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/fxadmnav_cfg.php";
			$navs = array_merge_admnav($navs, $config_file);
		//}
		if(defined("DC"))
		{
			$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/dcadmnav_cfg.php";
			$navs = array_merge_admnav($navs, $config_file);
		}
        if(IS_OPEN_DISTRIBUTION)
        {
            $config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/distadmnav_cfg.php";
            $navs = array_merge_admnav($navs, $config_file);
        }

		$this->assign("navs",$navs);
		$this->display();
	}
	//框架左侧
	public function left()
	{
		$navs = require_once(APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/admnav_cfg.php");
		if(OPEN_WEIXIN)
		{
			if(WEIXIN_TYPE=="platform")
			{
				$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/wxadmnav_platform_cfg.php";
			}
			else
			{
				$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/wxadmnav_cfg.php";
			}
			$navs = array_merge_admnav($navs, $config_file);
		}
		//if(defined("FX_LEVEL"))
		//{
			$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/fxadmnav_cfg.php";
			$navs = array_merge_admnav($navs, $config_file);
		//}
		if(!defined("FX_LEVEL")){
			unset($navs['marketing']['groups']['fx']);
			unset($navs['marketing']['groups']['fx_report']);
		}
		if(defined("DC"))
		{
			$config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/dcadmnav_cfg.php";
			$navs = array_merge_admnav($navs, $config_file);
		}
        if(defined("IS_OPEN_DISTRIBUTION"))
        {
            $config_file = APP_ROOT_PATH."system/adm_cfg/".APP_TYPE."/distadmnav_cfg.php";
            $navs = array_merge_admnav($navs, $config_file);
        }
       
        
        if(IS_OPEN_AGENCY==0){
            unset($navs['system']['groups']['agency']);
        }

        if(IS_DC_DELIVERY==0){
            unset($navs['dc']['groups']['dcthirddelivery']);
        }

		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
        //隐藏
        foreach($navs as $k=>$v){
            if($v['is_hide']==1){
                unset($navs[$k]);
                continue;
            }
            foreach($navs[$k]['groups'] as $kk=>$vv){
                if($vv['is_hide']==1){
                    unset($navs[$k]['groups'][$kk]);
                    continue;
                }
                foreach($navs[$k]['groups'][$kk]['nodes'] as $kkk=>$vvv){
                    if($vvv['is_hide']==1){
                        unset($navs[$k]['groups'][$kk]['nodes'][$kkk]);
                        continue;
                    }
                }
            }

        }
		$nav_key = strim($_REQUEST['key']);
		$nav_group = $navs[$nav_key]['groups'];
		$this->assign("menus",$nav_group);
		$this->display();
	}
	//默认框架主区域
	public function main()
	{
		$this->assign("apptype",APP_TYPE);
		$this->assign("FANWE_APP_ID",FANWE_APP_ID);

		//关于订单
        /* 平台商城待发货订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id WHERE do.is_delete = 0 AND do.type = 3  AND do.pay_status = 2 and do.is_main=0 ";
        $shop_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=0 and doi.is_shop=1 and do.order_status=0 ";
        $shop_count = intval($GLOBALS['db']->getOne($shop_count_sql));
        $this->assign("shop_count",$shop_count);

        /* 平台商城退款订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id WHERE do.is_delete = 0 AND do.type = 3 AND do.pay_status = 2 and do.is_main=0 ";
        $shop_refund_status_count_sql = "select count(distinct(doi.order_id)) ". $condition."  and do.refund_status=1 and doi.is_shop=1 and do.order_status=0 ";
        $shop_refund_status_count = intval($GLOBALS['db']->getOne($shop_refund_status_count_sql));
        $this->assign("shop_refund_status_count",$shop_refund_status_count);

        /* 积分待发货订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id WHERE do.is_delete = 0 AND do.type = 2 AND do.pay_status = 2 and do.is_main=0 ";
        $shop_score_status_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=0  and doi.is_shop=1 and do.order_status=0 ";
        $shop_score_status_count = intval($GLOBALS['db']->getOne($shop_score_status_count_sql));
        $this->assign("shop_score_status_count",$shop_score_status_count);

        /* 团购退款订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order as do LEFT JOIN ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id LEFT JOIN ".DB_PREFIX."deal_coupon as dc on dc.order_deal_id = doi.id 
                    WHERE do.type=5 and do.refund_status=1 and do.is_main=0 ";
        $tuan_refund_status_count_sql = "select count(distinct(do.id)) ". $condition." and doi.delivery_status=5  and doi.is_shop=0";
        $tuan_refund_status_count = intval($GLOBALS['db']->getOne($tuan_refund_status_count_sql));
        $this->assign("tuan_refund_status_count",$tuan_refund_status_count);

        /* 商城待发货订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id WHERE do.is_delete = 0 AND do.type = 6 AND do.pay_status = 2 and do.is_main=0 ";
        $supplier_shop_count_sql = "select count(distinct(doi.order_id)) ". $condition." and doi.delivery_status=0 and doi.is_shop=1 and do.order_status=0 ";
        $supplier_shop_count = intval($GLOBALS['db']->getOne($supplier_shop_count_sql));
        $this->assign("supplier_shop_count",$supplier_shop_count);

        /* 商城退款订单数量统计 */
        $condition=" from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id=do.id WHERE do.is_delete = 0 AND do.type = 6 AND do.pay_status = 2 ";
        $supplier_shop_refund_status_count_sql = "select count(distinct(doi.order_id)) ". $condition." and do.refund_status=1 and doi.is_shop=1 and do.order_status=0 ";
        $supplier_shop_refund_status_count = intval($GLOBALS['db']->getOne($supplier_shop_refund_status_count_sql));
        $this->assign("supplier_shop_refund_status_count",$supplier_shop_refund_status_count);

        /* 会员提现申请 */
		$user_withdraw_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."withdraw where is_paid=0  and is_delete = 0"));
        $this->assign("user_withdraw_count",$user_withdraw_count);

        /* 商户提现申请 */
        $supplier_withdraw_count =intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_money_submit where status=0"));
        $this->assign("supplier_withdraw_count",$supplier_withdraw_count);

        /*待审核团购*/

        $supplier_tuan_check_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_submit where admin_check_status=0  and  biz_apply_status IN (1,2) and is_shop=0"));
        $this->assign("supplier_tuan_check_count",$supplier_tuan_check_count);

        /*待审核商品*/
        $supplier_shop_check_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_submit where admin_check_status=0 and  biz_apply_status IN (1,2) and is_shop=1"));
        $this->assign("supplier_shop_check_count",$supplier_shop_check_count);

        /*待审核商户*/
        $supplier_check_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_submit where is_publish=0"));
        $this->assign("supplier_check_count",$supplier_check_count);

        /*待审核门店*/
        $supplier_location_check_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_biz_submit where admin_check_status=0"));
        $this->assign("supplier_location_check_count",$supplier_location_check_count);

		
		//关于用户
//		$user_count = M("User")->count();
//		$this->assign("user_count",$user_count);
//		$income_incharge = M("Statements")->sum("income_incharge");
//		$this->assign("income_incharge",$income_incharge);

		
		//上线的团购
//		$tuan_count = M("Deal")->where("is_shop = 0 and is_effect = 1 and is_delete = 0")->count();
//		$this->assign("tuan_count",$tuan_count);
// 		$tuan_dp_wait_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp  where dp.deal_id >0 and dp.reply_content = '' "));
// 		$tuan_dp_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp  where dp.deal_id >0 "));
// 		$this->assign("tuan_dp_wait_count",$tuan_dp_wait_count);
// 		$this->assign("tuan_dp_count",$tuan_dp_count);
		
//		$tuan_submit_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_submit where is_shop = 0 and admin_check_status = 0");
//		$this->assign("tuan_submit_count",$tuan_submit_count);
		
		//上线的商品
//		$shop_count = M("Deal")->where("is_shop = 1 and is_effect = 1 and is_delete = 0")->count();
//		$this->assign("shop_count",$shop_count);
		
//		$this->assign("shop_dp_wait_count",$tuan_dp_wait_count);
//		$this->assign("shop_dp_count",$tuan_dp_count);
		
//		$shop_submit_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_submit where is_shop = 1 and admin_check_status = 0");
//		$this->assign("shop_submit_count",$shop_submit_count);
		
		//关于优惠
//		$youhui_count = M("Youhui")->where("is_effect = 1")->count();
//		$this->assign("youhui_count",$youhui_count);
		
// 		$youhui_dp_wait_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.youhui_id >0 and dp.reply_content = ''"));
// 		$youhui_dp_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.youhui_id >0"));
// 		$this->assign("youhui_dp_wait_count",$youhui_dp_wait_count);
// 		$this->assign("youhui_dp_count",$youhui_dp_count);
		
//		$youhui_submit_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_biz_submit where admin_check_status = 0");
//		$this->assign("youhui_submit_count",$youhui_submit_count);
		
		//关于活动
//		$event_count = M("Event")->where("is_effect = 1")->count();
//		$this->assign("event_count",$event_count);
		
// 		$event_dp_wait_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.event_id >0 and dp.reply_content = ''"));
// 		$event_dp_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.event_id >0"));
// 		$this->assign("event_dp_wait_count",$event_dp_wait_count);
// 		$this->assign("event_dp_count",$event_dp_count);
		
//		$event_submit_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_biz_submit where admin_check_status = 0");
//		$this->assign("event_submit_count",$event_submit_count);
		
		//关于商户
//		$supplier_count = M("Supplier")->count();
//		$this->assign("supplier_count",$supplier_count);
//		$store_count = M("SupplierLocation")->where("is_effect = 1")->count();
//		$this->assign("store_count",$store_count);
//
//		$supplier_submit_count = M("SupplierSubmit")->where("is_publish = 0")->count();
//		$this->assign("supplier_submit_count",$supplier_submit_count);
		
//		$store_dp_wait_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.supplier_location_id >0 and dp.reply_content = ''"));
//		$store_dp_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.supplier_location_id >0"));
//		$this->assign("store_dp_wait_count",$store_dp_wait_count);
//		$this->assign("store_dp_count",$store_dp_count);
		
//		$location_submit_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_biz_submit where admin_check_status = 0");
//		$this->assign("location_submit_count",$location_submit_count);
//
//		$sp_withdraw_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_money_submit where status = 0");
//		$this->assign("sp_withdraw_count",$sp_withdraw_count);



        $dp_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp"));
        $dp_wait_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp where reply_content = ''"));

        $this->assign("dp_wait_count",$dp_wait_count);
        $this->assign("dp_count",$dp_count);

		$this->display();
	}	
	//底部
	public function footer()
	{
		$this->display();
	}
	
	//修改管理员密码
	public function change_password()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_data",$adm_session);
		$this->display();
	}
	public function do_change_password()
	{
		$adm_id = intval($_REQUEST['adm_id']);
		if(!check_empty($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if(!check_empty($_REQUEST['adm_new_password']))
		{
			$this->error(L("ADM_NEW_PASSWORD_EMPTY_TIP"));
		}
		if($_REQUEST['adm_confirm_password']!=$_REQUEST['adm_new_password'])
		{
			$this->error(L("ADM_NEW_PASSWORD_NOT_MATCH_TIP"));
		}		
		if(M("Admin")->where("id=".$adm_id)->getField("adm_password")!=md5($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_ERROR"));
		}
		M("Admin")->where("id=".$adm_id)->setField("adm_password",md5($_REQUEST['adm_new_password']));
		save_log(M("Admin")->where("id=".$adm_id)->getField("adm_name").L("CHANGE_SUCCESS"),1);
		$this->success(L("CHANGE_SUCCESS"));
		
		
	}
	
	public function reset_sending()
	{
		$field = strim($_REQUEST['field']);
		if($field=='DEAL_MSG_LOCK'||$field=='PROMOTE_MSG_LOCK'||$field=='APNS_MSG_LOCK')
		{
			M("Conf")->where("name='".$field."'")->setField("value",'0');
			$this->success(L("RESET_SUCCESS"),1);
		}
		else
		{
			$this->error(L("INVALID_OPERATION"),1);
		}
	}
}
?>