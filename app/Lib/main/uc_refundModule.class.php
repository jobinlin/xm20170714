<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_refundModule extends MainBaseModule
{
	public function index_old()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
	
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
	
		$did = intval($_REQUEST['did']);
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		$page_size = app_conf("PAGE_SIZE");
		$page = intval($_REQUEST['p']);
		
		if($page==0)
			$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$user_id = $GLOBALS['user_info']['id'];
		$refund_str = array(
		    '', '退款申请中', '已退款', '驳回申请',
		);
		$sql = 'SELECT distinct(m.`id`) mid, m.`user_id`, doi.`id`, doi.`number`, doi.`name`, doi.`order_sn` , doi.`attr_str`, doi.`deal_icon` , doi.`unit_price`, doi.`total_price`, doi.`is_coupon`, doi.`deal_id`, doi.`supplier_id` sid1, doi.`refund_status` rs1, dc.`refund_status` rs2 , m.`create_time` ,m.`content`
		        FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id`
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.' AND doi.`unit_price` IS NOT NULL
                ORDER BY m.id DESC,m.create_time DESC LIMIT '.$limit;

		$sql_count = 'SELECT COUNT(distinct(m.`id`))
                FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.' AND doi.`unit_price` IS NOT NULL';

		$list = $GLOBALS['db']->getAll($sql);

		$data = array();
		if (count($list)) {
		    $count = $GLOBALS['db']->getOne($sql_count);//count($list);
    		$page = new Page($count,$page_size);   //初始化分页对象
    		$p  =  $page->show();

		    $s_ids = array();
		    foreach ($list as $item) {
		        if ($item['sid1']) {
		            $s_ids[] = $item['sid1'];
		        }
		    }

		    $snameSql = 'SELECT id, name , preview FROM '.DB_PREFIX.'supplier WHERE id in ('.implode(',', $s_ids).')';
		    $slist = $GLOBALS['db']->getAll($snameSql);
		    $fslist = array();//对应商家名称
			$plist = array();//对应商家图标
		    foreach ($slist as $s) {
				$fslist[$s['id']] = $s['name'];
			}
			foreach ($slist as $s) {
				$plist[$s['id']] = $s['preview'];
			}
		    foreach ($list as $item) {
		        if ($item['is_coupon']) {
		            $item['number'] = 1;
		            $item['total_price'] = $item['unit_price'];
		        }
		        
		        $item['unit_price'] = round($item['unit_price'], 2);
		        $item['total_price'] = round($item['total_price'], 2);
		        // $item['supplier_name'] = $item['supplier_name'] ? : '平台自营';
		        $item['supplier_name'] = $fslist[$item['sid1']] ?: '平台自营';
				$item['preview'] = $plist[$item['sid1']];
		        //$item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));
		        $item['create_time'] = to_date($item['create_time']);
		        
		        // 退款状态
		        $rs_k = $item['is_coupon'] ? 'rs2' : 'rs1';
		        $item['refund_status'] = $item[$rs_k];
		        $item['status_str'] = $refund_str[$item[$rs_k]];
		        $data[] = $item;
		    }
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("page_title","退款记录");
		assign_uc_nav_list();
		$GLOBALS['tmpl']->display("uc/uc_refund_index.html");
	}


	public function index()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
	
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
	
		$did = intval($_REQUEST['did']);
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		$page_size = app_conf("PAGE_SIZE");
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$user_id = $GLOBALS['user_info']['id'];
		$refund_str = array(
		    '', '退款申请中', '已退款', '驳回申请',
		);
		$sql = 'SELECT distinct(m.`id`) mid, count(m.id) cid , doi.`id`, doi.`number` , doi.`order_sn` , doi.`deal_id` , doi.`refund_money` rm1 , doi.`unit_price`, doi.`total_price`, doi.`refund_status` rs1, dc.`refund_status` rs2 , m.`create_time` ,m.`content`, dc.`coupon_price`,dc.`order_deal_id`,sum(dc.`refund_money`) rm2
		        FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id` AND doi.is_shop = 1
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.'
                GROUP BY m.id, doi.id
                ORDER BY m.id DESC,m.create_time DESC LIMIT '.$limit;

		$sql_count = 'SELECT COUNT(distinct(m.`id`))
                FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id;

		$list = $GLOBALS['db']->getAll($sql);

		$data = array();
		if (count($list)) {
            $count = $GLOBALS['db']->getOne($sql_count);//count($list);
    		$page = new Page($count,$page_size);   //初始化分页对象
    		$p  =  $page->show();


            $s_ids = array();
            $doid = array();
            foreach ($list as $item) {
                // 获取相关的订单商品id
                    if ($item['id'] || $item['order_deal_id']) {
                        $doid[] = $item['id'] ?: $item['order_deal_id'];
                    }
            }
            // 另外获取商品的名称和商家id信息
            $doiSql = 'SELECT id, name, deal_icon,order_sn,supplier_id,deal_id FROM '.DB_PREFIX.'deal_order_item WHERE id in ('.implode(',', $doid).')';
            $doi = $GLOBALS['db']->getAll($doiSql);
            $format = array();
            foreach ($doi as $val) {
                if ($val['supplier_id']) {
                    $s_ids[] = $val['supplier_id'];
                }
                $format[$val['id']] = $val;
            }
		    $snameSql = 'SELECT id, name , preview FROM '.DB_PREFIX.'supplier WHERE id in ('.implode(',', $s_ids).')';
		    $slist = $GLOBALS['db']->getAll($snameSql);
		    $fslist = array();//对应商家名称
			$plist = array();//对应商家图标
		    foreach ($slist as $s) {
				$fslist[$s['id']] = $s['name'];
				$plist[$s['id']] = $s['preview'];
			}
			
		    foreach ($list as $item) {
		    	$item_id = $item['id'];
		        if (empty($item['unit_price'])) {
		        	$item_id = $item['order_deal_id'];
                    $item['number'] = $item['cid'];
                    $item['unit_price'] = $item['coupon_price'];
                    $item['total_price'] = $item['coupon_price'];
                }
                $item['name'] = $format[$item_id]['name'] ?: $format[$item['order_deal_id']]['name'];
                $item['deal_id'] = $item['deal_id']? $item['deal_id']:$format[$item['order_deal_id']]['deal_id'];
                $item['deal_icon'] = $format[$item_id]['deal_icon'] ?: $format[$item['order_deal_id']]['deal_icon'];
		        $item['order_sn'] = $item['order_sn']?$item['order_sn']:$format[$item['order_deal_id']]['order_sn'];
		        $item['unit_price'] = round($item['unit_price'], 2);
		        $item['total_price'] = round($item['total_price'], 2);
		        // $item['supplier_name'] = $item['supplier_name'] ? : '平台自营';
		        $item['supplier_name'] = $fslist[$format[$item_id]['supplier_id']] ?'店铺：'.$fslist[$format[$item_id]['supplier_id']]: '平台自营';
				$item['preview'] = $plist[$format[$item['id']]['supplier_id']];
		        //$item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));
		        $item['create_time'] = to_date($item['create_time']);
		        $item['rm1']=round($item['rm1'],2);
		        $item['rm2']=round($item['rm2'],2);
		        // 退款状态
		        $rs_k = $item['rs1'] ? 'rs1' : 'rs2';
		        $item['refund_status'] = $item[$rs_k];
		        $item['status_str'] = $refund_str[$item[$rs_k]];
		        $data[] = $item;
		    }
		    $GLOBALS['tmpl']->assign('pages',$p);
		}
		//print_r($data);exit;
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->assign("page_title","退款记录");
		assign_uc_nav_list();
		$GLOBALS['tmpl']->display("uc/uc_refund_index.html");
	}
}
?>