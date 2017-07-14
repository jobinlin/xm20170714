<?php
/**
 * 商户中心商品管理
 * @author Administrator
 *
 */
require APP_ROOT_PATH . 'app/Lib/page.php';

class goodsModule extends BizBaseModule
{

    function __construct()
    {
        parent::__construct();
        global_run();
        $this->check_auth();
    }

    public function index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        /* 获取参数 */
        
        /* 业务逻辑部分 */
        $conditions .= " where (d.end_time>".NOW_TIME." or d.end_time=0) and (d.begin_time<".NOW_TIME." or d.begin_time=0) and d.is_effect = 1 and d.is_delete = 0 and (d.max_bought>0 or d.max_bought=-1) and d.is_shop = 1 and d.supplier_id = ".$supplier_id; // 查询条件
        

        // 需要连表操作 只查询支持门店的
        $join = " left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id ";
        $conditions .= " and dll.location_id in(" . implode(",", $account_info['location_ids']) . ") ";
        
        
        $sql_count = " select count(distinct(d.id)) from " . DB_PREFIX . "deal d";
        //$sql = " select distinct(d.id),d.name,d.sub_name,d.begin_time,d.end_time,time_status from " . DB_PREFIX . "deal d";
        $sql = " select distinct(d.id),dc.name as cate_name,d.name,d.sub_name,d.icon,d.max_bought,d.buy_count,d.current_price,d.balance_price,d.begin_time,d.end_time,time_status from " . DB_PREFIX . "deal d left join "
            . DB_PREFIX . "shop_cate dc on d.shop_cate_id=dc.id";
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $total = $GLOBALS['db']->getOne($sql_count . $join . $conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);

        $list = $GLOBALS['db']->getAll($sql . $join . $conditions . " order by d.id desc limit " . $limit);
        foreach ($list as $k => $v) {
            $list[$k]['cate_name'] =$v['cate_name']?$v['cate_name']:"暂无分类";
            $list[$k]['max_bought'] = $v['max_bought']<0?"充足":$v['max_bought'];
            $list[$k]['current_price'] = round($v['current_price'],2);
            $list[$k]['balance_price'] = round($v['balance_price'],2);
            $list[$k]['images'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_gallery where deal_id=" . $v['id'] . " order by sort desc");
            $list[$k]['edit_url'] = url("biz", "goods#edit", array(
                "id" => $v['id'],
                "edit_type" =>1
            ));
            $list[$k]['preview_url'] = url("index","preview#deal",array("id"=>$v['id'],"type"=>0));
            if(defined("FX_LEVEL"))
            $list[$k]['fx_count'] =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_deal where deal_id=".$v['id']);
            	 
        }
        
        /* 数据 */
        //print_r($list);exit;
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "goods#publish"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "goods#index"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "goods#index"));
        $GLOBALS['tmpl']->assign("sale_over_index", url("biz", "goods#sale_over_index"));
        $GLOBALS['tmpl']->assign("down_line_index", url("biz", "goods#down_line_index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "goods#no_online_index"));
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "商品列表");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }
    
    /**
     * 已售罄的列表
     */
    public function sale_over_index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        /* 获取参数 */
        
        /* 业务逻辑部分 */
        $conditions .= " where d.is_effect = 1 and d.is_delete = 0 and d.max_bought=0 and d.is_shop = 1 and d.supplier_id = ".$supplier_id; // 查询条件
        

        // 需要连表操作 只查询支持门店的
        $join = " left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id ";
        $conditions .= " and dll.location_id in(" . implode(",", $account_info['location_ids']) . ") ";
        
        
        $sql_count = " select count(distinct(d.id)) from " . DB_PREFIX . "deal d";
        //$sql = " select distinct(d.id),d.name,d.sub_name,d.begin_time,d.end_time,time_status from " . DB_PREFIX . "deal d";
        $sql = " select distinct(d.id),dc.name as cate_name,d.name,d.sub_name,d.icon,d.max_bought,d.buy_count,d.current_price,d.balance_price,d.begin_time,d.end_time,time_status from " . DB_PREFIX . "deal d left join "
            . DB_PREFIX . "shop_cate dc on d.shop_cate_id=dc.id";
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $total = $GLOBALS['db']->getOne($sql_count . $join . $conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);

        $list = $GLOBALS['db']->getAll($sql . $join . $conditions . " order by d.id desc limit " . $limit);
        
        foreach ($list as $k => $v) {
            $list[$k]['cate_name'] =$v['cate_name']?$v['cate_name']:"暂无分类";
            $list[$k]['max_bought'] = $v['max_bought']<0?"充足":$v['max_bought'];
            $list[$k]['current_price'] = round($v['current_price'],2);
            $list[$k]['balance_price'] = round($v['balance_price'],2);
            $list[$k]['begin_time'] = $v['begin_time'] != 0 ? to_date($v['begin_time']) : "不限";
            $list[$k]['end_time'] = $v['end_time'] != 0 ? to_date($v['end_time']) : "不限";
            $list[$k]['images'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_gallery where deal_id=" . $v['id'] . " order by sort desc");
            $list[$k]['edit_url'] = url("biz", "goods#edit", array(
                "id" => $v['id'],
                "edit_type" =>1
            ));
            $list[$k]['preview_url'] = url("index","preview#deal",array("id"=>$v['id'],"type"=>0));
            if(defined("FX_LEVEL"))
            $list[$k]['fx_count'] =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_deal where deal_id=".$v['id']);
            	 
        }
        
        /* 数据 */
        //print_r($list);exit;
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "goods#publish"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "goods#sale_over_index"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "goods#index"));
        $GLOBALS['tmpl']->assign("sale_over_index", url("biz", "goods#sale_over_index"));
        $GLOBALS['tmpl']->assign("down_line_index", url("biz", "goods#down_line_index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "goods#no_online_index"));
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "商品列表");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }
    
    /**
     * 仓库中的列表
     */
    public function down_line_index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        /* 获取参数 */
        
        /* 业务逻辑部分 */
        $conditions .= " where (((d.end_time<".NOW_TIME." and d.end_time!=0) or d.is_effect=0) or ((d.begin_time>".NOW_TIME." and d.begin_time!=0) or d.is_effect=0)) and d.is_delete = 0 and d.is_shop = 1 and d.supplier_id = ".$supplier_id; // 查询条件
        
        
        // 需要连表操作 只查询支持门店的
        $join = " left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id ";
        $conditions .= " and dll.location_id in(" . implode(",", $account_info['location_ids']) . ") ";
        
        
        $sql_count = " select count(distinct(d.id)) from " . DB_PREFIX . "deal d";
        $sql = " select distinct(d.id),dc.name as cate_name,d.name,d.sub_name,d.icon,d.max_bought,d.buy_count,d.current_price,d.balance_price,d.begin_time,d.end_time,time_status from " . DB_PREFIX . "deal d left join "
            . DB_PREFIX . "shop_cate dc on d.shop_cate_id=dc.id";
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $total = $GLOBALS['db']->getOne($sql_count . $join . $conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);
        
        $list = $GLOBALS['db']->getAll($sql . $join . $conditions . " order by d.id desc limit " . $limit);
        
        foreach ($list as $k => $v) {
            $list[$k]['cate_name'] =$v['cate_name']?$v['cate_name']:"暂无分类";
            $list[$k]['max_bought'] = $v['max_bought']<0?"充足":$v['max_bought'];
            $list[$k]['current_price'] = round($v['current_price'],2);
            $list[$k]['balance_price'] = round($v['balance_price'],2);
            $list[$k]['begin_time'] = $v['begin_time'] != 0 ? to_date($v['begin_time']) : "不限";
            $list[$k]['end_time'] = $v['end_time'] != 0 ? to_date($v['end_time']) : "不限";
            $list[$k]['images'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_gallery where deal_id=" . $v['id'] . " order by sort desc");
            $list[$k]['edit_url'] = url("biz", "deal#edit", array(
                "id" => $v['id'],
                "edit_type" =>1
            ));
        
            $list[$k]['preview_url'] = url("index","preview#deal",array("id"=>$v['id'],"type"=>0));
            if(defined("FX_LEVEL"))
                $list[$k]['fx_count'] =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_deal where id=".$v['id']);
        }
        
        /*数据*/
        //print_r($list);exit;
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "goods#publish"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "goods#down_line_index"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "goods#index"));
        $GLOBALS['tmpl']->assign("sale_over_index", url("biz", "goods#sale_over_index"));
        $GLOBALS['tmpl']->assign("down_line_index", url("biz", "goods#down_line_index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "goods#no_online_index"));
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "商品列表");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }

    /**
     * 未发布的列表
     */
    public function no_online_index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        $online_check = $_REQUEST['online_check'];
        /* 获取参数 */
        if(isset($_REQUEST['filter_admin_check']) && $_REQUEST['filter_admin_check']!=''){
            $filter_admin_check = intval($_REQUEST['filter_admin_check']);
        }else{
            $filter_admin_check = -1;
        }
        if(isset($online_check) && $online_check!=''){
            $online_check = intval($online_check);
        }else{
            $online_check = -1;
        }
        /* 业务逻辑部分 */
        $conditions .= " where d.is_effect = 1 and d.is_delete = 0 and d.is_shop = 1 "; // 查询条件
        
        if ($account_info['is_main'] == 1) { // 总管理员
            $conditions .= " and d.supplier_id = " . $supplier_id;
        } else { // 子账户操作
               // 只查询支持门店的
            $conditions .= " and d.account_id =" . $account_id;
        }
        
        if ( $filter_admin_check >= 0) {
            $conditions .= " and admin_check_status = " . $filter_admin_check;
        }
        if($online_check>=0){
            $online_conditions .=" and d.biz_apply_status = ".$online_check;
        }
        $sql_count = " select count(*) from " . DB_PREFIX . "deal_submit d";
        $sql = " select d.id,d.name,d.sub_name,d.create_time as create_time,d.begin_time,d.end_time,d.biz_apply_status,d.admin_check_status,d.cache_focus_imgs,d.deal_id,d.deal_submit_memo from " . DB_PREFIX . "deal_submit d";
        
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $total = $GLOBALS['db']->getOne($sql_count . $conditions .$online_conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);
//         echo $sql . $conditions . " order by id desc limit " . $limit;exit;
        $list = $GLOBALS['db']->getAll($sql . $conditions . $online_conditions ." order by id desc limit " . $limit);
        
        foreach ($list as $k => $v) {
            $list[$k]['images'] = unserialize($v['cache_focus_imgs']);
            $list[$k]['create_time'] = to_date($list[$k]['create_time']);
            $list[$k]['edit_url'] = url("biz", "goods#edit", array(
                "id" => $v['id'],
                "edit_type" =>2
            ));
            $list[$k]['preview_url'] = url("index","preview#deal",array("id"=>$v['id'],"type"=>1));
        }
        
        /* 数据 */
        $GLOBALS['tmpl']->assign("online_check", $online_check);
        $GLOBALS['tmpl']->assign("filter_admin_check", $filter_admin_check);
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "goods#publish"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "goods#no_online_index"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "goods#index"));
        $GLOBALS['tmpl']->assign("sale_over_index", url("biz", "goods#sale_over_index"));
        $GLOBALS['tmpl']->assign("down_line_index", url("biz", "goods#down_line_index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "goods#no_online_index"));
        
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "商品列表");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }

    /**
     * 商品发布
     */
    public function publish()
    {
        /* 基本参数初始化 */
         init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $this->_assignCarriageNumber();
        $this->_assignCarriageTemplate();
        /* 业务逻辑 */
        
        // 支持门店
        $location_infos = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX ."supplier_location where id in(" . implode(",", $GLOBALS['account_info']['location_ids']).") and is_effect=1");
        // 标签数据
        for($i=0;$i<10;$i++)
        {
            if($i!=0&&$i!=1&&$i!=3&&$i!=4&&$i!=5&&$i!=6&&$i!=9)
            {
                $tags_html .= '<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_tag[]" value="' . $i . '" />' . lang("DEAL_TAG_" . $i) . '</label>';
            }
        }
        // 商品分类
        $shop_cate_tree = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."shop_cate where is_delete = 0");
		$shop_cate_1=array();
		$shop_cate_2=array();
		foreach($shop_cate_tree as $k=>$v){
			if($v['pid']==0){
				$shop_cate_1[$v['id']]=$v;
			}
			if($v['pid']>0){
				$shop_cate_2[$v['pid']][$v['id']]=$v;
			}
		}
		//echo "<pre>";print_r($shop_cate_1);print_r($shop_cate_2);exit;
		$GLOBALS['tmpl']->assign("shop_cate_1", $shop_cate_1); // 商品分类
		$GLOBALS['tmpl']->assign("shop_cate_2", json_encode($shop_cate_2)); // 商品分类
        $shop_cate_tree = toFormatTree($shop_cate_tree,"name");
        

        // 商品类型
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
        //输出配送类型
        $this->_assignDeliveryType();
        /* 数据 */
        $GLOBALS['tmpl']->assign("shop_cate_tree", $shop_cate_tree); // 商品分类
        $GLOBALS['tmpl']->assign("brand_list", $brand_list); // 品牌
        $GLOBALS['tmpl']->assign("location_infos", $location_infos); // 支持门店
        $GLOBALS['tmpl']->assign("tags_html", $tags_html); // 标签数据
        $GLOBALS['tmpl']->assign("goods_type_list", $goods_type_list); // 商品类型
        $GLOBALS['tmpl']->assign("go_list_url", url("biz", "goods")); // 返回列表连接
        //是否开启自动审核
        $GLOBALS['tmpl']->assign('allow_publish_verify',intval($GLOBALS['db']->getOne("select allow_publish_verify from ".DB_PREFIX."supplier where id=".$supplier_id)));
        
        $biz_root=SITE_DOMAIN.$_SERVER['PHP_SELF'];
		$GLOBALS['tmpl']->assign("biz_root",$biz_root);
		$app_root=SITE_DOMAIN.APP_ROOT;
		$GLOBALS['tmpl']->assign("app_root",$app_root);
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
        $GLOBALS['tmpl']->assign("page_title", "商品项目发布");
        $GLOBALS['tmpl']->display("pages/project/goods_publish.html");
    }

    public function edit()
    {
    	/* 基本参数初始化 */
    	init_app_page();
    	$this->_assignDeliveryType();
    	$this->_assignCarriageTemplate();
    	$this->_assignCarriageNumber();
    	$account_info = $GLOBALS['account_info'];
    	$supplier_id = $account_info['supplier_id'];
    	$account_id = $account_info['id'];
    
    	$id = intval($_REQUEST['id']);
    	$edit_type = intval($_REQUEST['edit_type']);
    
    	if($edit_type == 1 && $id>0){ //判断是否有存在修改
    		$deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where deal_id = ".$id." and supplier_id = ".$supplier_id);
    		if($deal_submit_info && $deal_submit_info['admin_check_status']==0){
    			showBizErr("已经存在申请操作，请先删除避免重复申请",0,url("biz","goods#index"));
    			exit;
    		}
    	}
    
    	/* 业务逻辑 */
    
    	if ($edit_type == 1) {//管理员发布
    		/*********************************
    		 * 取真正的商品数据表数据
    		********************************/
    		$deal_info = $GLOBALS['db']->getRow("select d.* from " . DB_PREFIX . "deal d left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id  where d.is_effect = 1 and d.is_delete = 0 and is_shop=1 and id=".$id." and supplier_id = ".$supplier_id."
                         and dll.location_id in(" . implode(",", $account_info['location_ids']).")");
    
    		if (empty($deal_info)) {
    			showBizErr("数据不存在或没有操作权限！",0,url("biz","goods#index"));
    			exit();
    		}
    		$deal_info['max_bought'] = $GLOBALS['db']->getOne("select stock_cfg from ".DB_PREFIX."deal_stock where deal_id = '".$deal_info['id']."'");
    
    		//支持门店 , 门店选中状态
    		$location_infos = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $GLOBALS['account_info']['location_ids']) . ") and is_effect=1");
    		$curr_location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_location_link where  deal_id = ".$id);
    		foreach($curr_location_list as $k=>$v){
    			$curr_locations[] = $v['location_id'];
    		}
    
    		foreach ($location_infos as $k => $v) {
    			if (in_array($v['id'], $curr_locations) ) {
    				$location_infos[$k]['checked'] = 1;
    			}
    		}
    
    		// 图集
    		$img_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id=".$id." order by sort asc");
    
    		$imgs = array();
    		foreach($img_list as $k=>$v)
    		{
    			$focus_imgs[$v['sort']] = $v['img'];
    		}
    
    
    		//关联商品
    		$related_deal_id=$GLOBALS['db']->getOne("select relate_ids from ".DB_PREFIX."relate_goods where is_shop=1 and good_id = ".$id);
    		$related_deal=$GLOBALS['db']->getAll("select id,name,icon from ".DB_PREFIX."deal where id in(".$related_deal_id.")");
    		$GLOBALS['tmpl']->assign("related_deal",$related_deal);
    		$GLOBALS['tmpl']->assign("related_deal_id",$related_deal_id);
    
    		//输出配送方式列表
    		$free_delivery = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."free_delivery where deal_id = ".$id);
    		$deal_delivery = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_delivery where deal_id = ".$id);
    		//输出支付方式
    		$deal_payment = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$id);
    		//筛选条件
    		//输出配送类型
    
    		$GLOBALS['tmpl']->assign("cache_free_delivery",base64_encode(serialize($free_delivery)));
    		$GLOBALS['tmpl']->assign("cache_deal_delivery", base64_encode(serialize($deal_delivery)));
    		$GLOBALS['tmpl']->assign("cache_deal_payment", base64_encode(serialize($deal_payment)));
    		// 输出规格库存的配置
    		$attr_stock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."attr_stock where deal_id=".$id." order by id asc");
    		$go_list_url = url("biz","goods#index");
    	} elseif($edit_type == 2) {//商户提交
    		/**********************************
    		 * 取商户提交数据表
    		*********************************/
    		$deal_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_submit where is_shop = 1 and id=" . $id." and supplier_id = ".$supplier_id);
    
    		if (empty($deal_info)) {
    			showBizErr("数据不存在或没有操作权限！",0,url("biz","goods#no_online_index"));
    			exit();
    		}
    		// 支持门店 , 门店选中状态
    		$cache_location_id = unserialize($deal_info['cache_location_id']);
    		$location_infos = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $GLOBALS['account_info']['location_ids']) . ") and is_effect=1");
    
    		foreach ($location_infos as $k => $v) {
    			if (in_array($v['id'], $cache_location_id)) {
    				$location_infos[$k]['checked'] = 1;
    			}
    		}
    
    
    		//关联商品
    		$related_deal_id=unserialize($deal_info['cache_relate']);
    		$related_deal_id=$related_deal_id['relate_ids'];
    		$related_deal=$GLOBALS['db']->getAll("select id,name,icon from ".DB_PREFIX."deal where is_delivery=1 and id in(".$related_deal_id.")");
    		$GLOBALS['tmpl']->assign("related_deal",$related_deal);
    		$GLOBALS['tmpl']->assign("related_deal_id",$related_deal_id);
    
    		// 图集
    		$focus_imgs = unserialize($deal_info['cache_focus_imgs']);
    
    		//筛选关键词
    		$filter = unserialize($deal_info['cache_deal_filter']);
    
    		// 输出规格库存的配置
    		$attr_stock = unserialize($deal_info['cache_attr_stock']);
    
    
    		$GLOBALS['tmpl']->assign("cache_free_delivery",base64_encode($deal_info['cache_free_delivery']));
    		$GLOBALS['tmpl']->assign("cache_deal_delivery", base64_encode($deal_info['cache_deal_delivery']));
    		$GLOBALS['tmpl']->assign("cache_deal_payment", base64_encode($deal_info['cache_deal_payment']));
    
    		$go_list_url = url("biz","goods#no_online_index");
    	}
    	if( $deal_info['publish_verify_balance'] == 0){
    		$deal_info['publish_verify_balance'] = $account_info['publish_verify_balance']/100;
    	}
    	// 商品分类
    	$shop_cate_tree = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."shop_cate where is_delete = 0");
    	$shop_cate_1=array();
    	$shop_cate_2=array();
    	foreach($shop_cate_tree as $k=>$v){
    		if($v['pid']==0){
    			$shop_cate_1[$v['id']]=$v;
    		}
    		if($v['pid']>0){
    			$shop_cate_2[$v['pid']][$v['id']]=$v;
    		}
    	}
    	//echo "<pre>";print_r($shop_cate_1);print_r($shop_cate_2);exit;
    	$GLOBALS['tmpl']->assign("shop_cate_1", $shop_cate_1); // 商品分类
    	$GLOBALS['tmpl']->assign("shop_cate_2", json_encode($shop_cate_2)); // 商品分类
    	$shop_cate_tree = toFormatTree($shop_cate_tree,"name");
    	$sql = "select sc.id,sc.name,sc.pid,psc.name as pname ".
    		   "from ".DB_PREFIX."shop_cate sc LEFT JOIN ".DB_PREFIX."shop_cate psc on sc.pid=psc.id ".
    		   "where sc.id in(".$deal_info['shop_cate_id'].")";
    	$deal_shop_cate = $GLOBALS['db']->getAll($sql);
    	$GLOBALS['tmpl']->assign("deal_shop_cate", $deal_shop_cate); // 商品分类
    	
    	
    
    	//转换头部SCRIPT 用的 库存 JSON
    	$attr_cfg_json = "{";
    	$attr_stock_json = "{";
    
    	foreach ($attr_stock as $k => $v) {
    		$attr_cfg_json .= $k . ":" . "{";
    		$attr_stock_json .= $k . ":" . "{";
    		foreach ($v as $key => $vvv) {
    			if ($key != 'attr_cfg')
    				$attr_stock_json .= "\"" . $key . "\":" . "\"" . $vvv . "\",";
    		}
    		$attr_stock_json = substr($attr_stock_json, 0, - 1);
    		$attr_stock_json .= "},";
    
    		$attr_cfg_data = unserialize($v['attr_cfg']);
    		foreach ($attr_cfg_data as $attr_id => $vv) {
    			$attr_cfg_json .= $attr_id . ":" . "\"" . $vv . "\",";
    		}
    		$attr_cfg_json = substr($attr_cfg_json, 0, - 1);
    		$attr_cfg_json .= "},";
    	}
    	if ($attr_stock) {
    		$attr_cfg_json = substr($attr_cfg_json, 0, - 1);
    		$attr_stock_json = substr($attr_stock_json, 0, - 1);
    	}
    
    	$attr_cfg_json .= "}";
    	$attr_stock_json .= "}";
    
    	/*******************************************
    	 * 通用数据部分
    	********************************************/
    
    	// 商品类型
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
    	foreach ($goods_type_list as $k => $v) {
    		if ($v['id'] == $deal_info['deal_goods_type']) {
    			$goods_type_list[$k]['selected'] = 1;
    			break;
    		}
    	}
    
    	// 标签数据
    	for ($i = 1; $i < 10; $i ++) {
    		if($i!=0 && $i!=1&&$i!=3&&$i!=4&&$i!=5&&$i!=6&&$i!=9) {
    			if (($deal_info['deal_tag'] & pow(2, $i)) == pow(2, $i))
    				$tags_html .= '<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_tag[]" value="' . $i . '" checked="checked"/>' . lang("DEAL_TAG_" . $i) . '</label>';
    			else
    				$tags_html .= '<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_tag[]" value="' . $i . '" />' . lang("DEAL_TAG_" . $i) . '</label>';
    		}
    	}
    
    
    
    
    	// 时间格式化
    	$deal_info['begin_time'] = to_date($deal_info['begin_time'], "Y-m-d H:i");
    	$deal_info['end_time'] = to_date($deal_info['end_time'], "Y-m-d H:i");
    	$deal_info['coupon_begin_time'] = to_date($deal_info['coupon_begin_time'], "Y-m-d H:i");
    	$deal_info['coupon_end_time'] = to_date($deal_info['coupon_end_time'], "Y-m-d H:i");
    	$deal_info['publish_verify_balance'] = $deal_info['publish_verify_balance']*100;
    
    	/* 数据 */
    	$GLOBALS['tmpl']->assign("shop_cate_tree", $shop_cate_tree); // 商品分类
    	$GLOBALS['tmpl']->assign("goods_type_list", $goods_type_list); // 属性类型
    	$GLOBALS['tmpl']->assign("attr_cfg_json", $attr_cfg_json); // 属性配置
    	$GLOBALS['tmpl']->assign("attr_stock_json", $attr_stock_json); // 属性配置
    	$GLOBALS['tmpl']->assign("location_infos", $location_infos); // 支持门店
    	$GLOBALS['tmpl']->assign("tags_html", $tags_html); // 标签数据
    	$GLOBALS['tmpl']->assign("filter", $filter); // 筛选关键词
    	$GLOBALS['tmpl']->assign("img_list",$focus_imgs); // 图集数组
    	$GLOBALS['tmpl']->assign("img_index",count($focus_imgs));
    	$GLOBALS['tmpl']->assign("deal_info", $deal_info); // 商品所有数据
    	$GLOBALS['tmpl']->assign("edit_type", $edit_type); // 请求数据类型
    	$GLOBALS['tmpl']->assign("go_list_url", $go_list_url); // 返回列表连接
    
    	//是否开启自动审核
    	$GLOBALS['tmpl']->assign('allow_publish_verify',intval($GLOBALS['db']->getOne("select allow_publish_verify from ".DB_PREFIX."supplier where id=".$supplier_id)));
    
    	$biz_root=SITE_DOMAIN.$_SERVER['PHP_SELF'];
    	$GLOBALS['tmpl']->assign("biz_root",$biz_root);
    	$app_root=SITE_DOMAIN.APP_ROOT;
    	$GLOBALS['tmpl']->assign("app_root",$app_root);
    	/* 系统默认 */
    	$GLOBALS['tmpl']->assign("ajax_url", url("biz", "goods"));
    	$GLOBALS['tmpl']->assign("page_title", "商品项目编辑");
    	$GLOBALS['tmpl']->display("pages/project/goods_edit.html");
    }

    public function del()
    {
        /* 基本参数初始化 */
        init_app_page();
        
        $id = intval($_REQUEST['id']);
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        /* 业务逻辑 */
        if ($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "deal_submit where id=" . $id . " and account_id =" . $account_id)) {
            // 存在切用户有权限删除
            $GLOBALS['db']->query("delete from " . DB_PREFIX . "deal_submit where id=" . $id . " and account_id =" . $account_id);
            $data['status'] = 1;
            $data['info'] = "删除成功";
        } else {
            $data['status'] = 0;
            $data['info'] = "数据不存在货没有管理权限";
        }
        ajax_return($data);
    }

    /**
     * 保存商品产品数据
     */
    public function do_save_publish()
    {
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        if(count($_REQUEST['shop_cate_id'])==0){
			$result['status'] = 0;
			$result['info'] = "未选择分类";
			ajax_return($result);
		}
        $edit_type = intval($_REQUEST['edit_type']);
        $id = intval($_REQUEST['id']);
        unset($_REQUEST['id']);
        //是否开启自动审核
        $supplier_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id=".$supplier_id);
        $publish_verify_balance = $supplier_data['publish_verify_balance'];
        if($supplier_data['allow_publish_verify']){
            $allow_publish_verify = $supplier_data['allow_publish_verify'];
            require_once(APP_ROOT_PATH.'system/model/deal.php');
        }
        
        if($edit_type == 1 && $id>0){ //判断是否有存在修改
            $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where deal_id = ".$id." and supplier_id = ".$supplier_id);
            if($deal_submit_info && $deal_submit_info['admin_check_status']==0){
                $result['status'] = 0;
                $result['info'] = "已经存在申请操作，请先删除避免重复申请";
                ajax_return($result);
                exit;
            }else{
                $deal_info = $GLOBALS['db']->getRow("select d.* from " . DB_PREFIX . "deal d 
                        left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id  
                            where d.is_effect = 1 and d.is_delete = 0 and id=".$id." and supplier_id = ".$supplier_id."
                            and dll.location_id in(" . implode(",", $account_info['location_ids']).")");

                if(empty($deal_info)){
                    $result['status'] = 0;
                    $result['info'] = "数据不存在或没有权限操作该数据";
                    ajax_return($result);
                    exit;
                }
                //如果数据已经有存在，通过审核的数据，先清除掉在进行插入更新操作
                if($deal_submit_info && $deal_submit_info['admin_check_status']!=0){
                    $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_submit where id=".$deal_submit_info['id']);
                }
              
                //先建立数据
                //$GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$new_data);
                //$deal_submit_id = $GLOBALS['db']->insert_id();
            }
        
        }
        if($id > 0){ //更新操作需要替换图片地址
        	foreach ($_REQUEST['img'] as $k => $v) {
        		$v = replace_domain_to_public($v);;
        		$_REQUEST['img'][$k] = $v;
        	}
        }
        foreach ($_REQUEST['img'] as $k => $v) {
        	if ($v != '') {
        		$_REQUEST['img'][$k] = $v;
        		break;
        	}
        }
        $_REQUEST['cache_focus_imgs'] = serialize($_REQUEST['img']);
        
       
        $this->check_goods_publish_data($_REQUEST);
        
        // 白名单过滤
        require_once(APP_ROOT_PATH . 'system/model/no_xss.php');
        $_REQUEST['description'] = btrim(replace_domain_to_public(no_xss($_REQUEST['description']))); //描述
        
        $_REQUEST['supplier_id'] = $supplier_id; // 所属商户
        $_REQUEST['account_id'] = $account_id;
        
        $_REQUEST['buy_type'] = 0; // 默认为普通商品
    	//当库存没值，属性里大于0时状态
        if(count($_REQUEST['stock_cfg_num'])>0)
        {
        	$_REQUEST['max_bought'] = -1;
        }
        if($_REQUEST['is_refund']==1){
        	$_REQUEST['deal_tag'][] = 6;
        }
        $_REQUEST['publish_verify_balance']=$publish_verify_balance * 100;
        $_REQUEST['balance_price'] = $_REQUEST['current_price']*$publish_verify_balance; // 商户结算价
        
        $_REQUEST['create_time'] = NOW_TIME;
        $_REQUEST['update_time'] = NOW_TIME;
        $_REQUEST['is_shop'] = 1;
        $_REQUEST['is_effect'] = 1;
        $_REQUEST['is_delete '] = 0;
        
        $_REQUEST['cache_location_id'] = serialize($_REQUEST['location_id']);   //支持门店缓存
        
        // 开始处理属性
        $deal_attr = $_REQUEST['deal_attr'];
        
        foreach ($deal_attr as $goods_type_attr_id => $arr) {
            foreach ($arr as $k => $v) {
                if ($v != '') {
                    $deal_attr_item['attr_name'] = $v;
                    $deal_attr_item['is_checked'] = 1;
                    $deal_attr_item['deal_attr_id'] = $goods_type_attr_id.$k;
                    $deal_attr_item['id'] = $goods_type_attr_id;
                    $deal_attr_item['goods_type_id'] = intval($_REQUEST['deal_goods_type']);
                    $deal_attr_item['supplier_id'] = $GLOBALS['account_info']['supplier_id'];
                    
                    
                    $deal_attr_data[] = $deal_attr_item;
                }
            }
        }
        $_REQUEST['cache_deal_attr'] = serialize($deal_attr_data);
        
	    $deal_attr_new=array();
		foreach ($deal_attr as $goods_type_attr_id => $v) {
		    $arr=array();
			foreach ($v as $kk => $vv) {
				$arr[$goods_type_attr_id.$kk]=$vv;
			}
			$deal_attr_new[]=$arr;
		}
		$attr_cfg_arr=array();
		foreach ($deal_attr_new[0] as $k => $v) {
			if($deal_attr_new[1]){
				foreach ($deal_attr_new[1] as $kk => $vv) {
					if($deal_attr_new[2]){
						foreach ($deal_attr_new[2] as $kkk => $vvv) {
							$attr_cfg_arr[]=array($k=>$v,$kk=>$vv,$kkk=>$vvv);
						}
					}else{
						$attr_cfg_arr[]=array($k=>$v,$kk=>$vv);
					}
				}
			}else{
				$attr_cfg_arr[]=array($k=>$v);
			}
		
		}
		$attr_stock=array();
		foreach ($attr_cfg_arr as $k => $v){
			$attr_stock[$k]['attr_cfg']=$v;
			$attr_stock[$k]['attr_str']=implode("",$v);
			$attr_stock[$k]['attr_key']=implode("_",array_keys($v));
			$attr_stock[$k]['stock_cfg']=$_REQUEST['stock_cfg_num'][$k];
			$attr_stock[$k]['buy_count']=$_REQUEST['attr_buy_count'][$k];
			$attr_stock[$k]['add_balance_price']=$publish_verify_balance*$_REQUEST['deal_attr_price'][$k];//$_REQUEST['deal_add_balance_price'][$k];
			$attr_stock[$k]['price']=$_REQUEST['deal_attr_price'][$k];
			$attr_stock[$k]['buy_count']=$_REQUEST['stock_buy_count'][$k];
			
		}
        $_REQUEST['cache_attr_stock'] = serialize($attr_stock);

        //关联商品
        $related_deal=strim($_REQUEST['related_deal']);
        $_REQUEST['relate_goods_id']=explode(",",$related_deal);
        $relate_array=array();
        $relate_array['is_shop']=1;        
        $relate_array['good_id']=$id;
        $relate_array['relate_ids']=$related_deal;
        $_REQUEST['cache_relate']=serialize($relate_array);
        
        
        
        // 管理员状态
        $_REQUEST['admin_check_status'] = 0; // 待审核
        //过滤掉改版后不要的数据
        $_REQUEST['free_delivery'] = 0;
        $_REQUEST['define_payment'] = 0;
        $_REQUEST['cart_type'] = 0;
        $_REQUEST['allow_promote'] = 0;
        $_REQUEST['carriage_template_id']=intval($_REQUEST['carriage_template_id']);
        $_REQUEST=$this->_deliveryTypeBindIsDelivery($_REQUEST);
        
        if ($id > 0) {
        	if($edit_type == 1){
        		$_REQUEST['deal_id'] = $id;
        		$_REQUEST['biz_apply_status'] = 2;
        	}
        }else{
        	$_REQUEST['biz_apply_status'] = 1;
        }
        require_once APP_ROOT_PATH."/system/model/DealObject.php";
        $deal_object = new DealObject();
        $deal_object->setParamet($_REQUEST, "shop");
        
        //$result = $deal_object->save($_REQUEST);
        $result = $deal_object->SaveSupplierSubmiet($allow_publish_verify);
        if($result){
        	if($allow_publish_verify){
        		if($id>0){
                    $result['info'] = "修改成功";
				}else{
                    $result['info'] = "发布成功";
				}
        		$result['jump'] = url("biz", "goods");
        	}else{
                $result['info'] = "提交成功，等待管理员审核";
        		$result['jump'] = url("biz", "goods#no_online_index");
        	}
        	$result['status'] = 1;
        }else{
        	$result['info'] = "修改失败";
        	$result['status'] = 0;
        }
        //end
        ajax_return($result);
    }
    
    /**
     * 上架操作
     */
    public function up_line(){
        $account_info = $GLOBALS['account_info'];
        $account_id = $account_info['id'];
        $supplier_id = $account_info['supplier_id'];
    
        $id = intval($_REQUEST['id']);
        //是否开启自动审核
        $supplier_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id=".$supplier_id);
        if($supplier_data['allow_publish_verify']){
            $allow_publish_verify = $supplier_data['allow_publish_verify'];
            $publish_verify_balance = $supplier_data['publish_verify_balance'];
            require_once(APP_ROOT_PATH.'system/model/deal.php');
        }
        if($id>0){
            //商户提交数据
            $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where admin_check_status=0 and deal_id =".$id." and supplier_id=".$supplier_id);
            //真实团购数据
            $deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal d left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id where id=".$id." and dll.location_id in(".implode(",", $GLOBALS['account_info']['location_ids']).")");
            if($deal_info){
                if(($deal_info['end_time']>$now || $deal_info['end_time']==0) && ($deal_info['begin_time']<$now || $deal_info['begin_time']==0)){
                    //数据导入 deal_submit表
                    $data = array();
                    $data['admin_check_status'] = 0;
                    $data['biz_apply_status'] = 4;
                    $data['supplier_id'] = $supplier_id;
                    $data['account_id'] = $account_id;
                    $data['is_shop'] = 1;
                    $data['is_effect'] = 1;
                    $data['is_delete'] = 0;
                    
                    if($deal_submit_info){ //存在数据
                        if($allow_publish_verify){ //自动审核
                            if($deal_submit_info['biz_apply_status']!=4){ //更新状态
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$data,"UPDATE","id=".$deal_submit_info['id']);
                            }
                            deal_auto_downline($deal_submit_info['id']);
                            $result['status'] = 1;
                            $result['info'] = "修改成功";
                        }else{
                            if($deal_submit_info['biz_apply_status']!=4){ //更新状态
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$data,"UPDATE","id=".$deal_submit_info['id']);
                                $result['status'] = 1;
                                $result['info'] = "修改成功，等待管理员审核";
                            }elseif($deal_submit_info['biz_apply_status']==4){
                                $result['status'] = 0;
                                $result['info'] = "上架待审核中，请勿重复申请";
                            }
                        }
                    
                    }else{ //增加新数据
                    
                        $data['deal_id'] = $deal_info['id'];
                        $data['name'] = $deal_info['name'];
                        $data['cate_id'] = $deal_info['cate_id'];
                        $data['city_id'] = $deal_info['city_id'];
                        $data['create_time'] = NOW_TIME;
                        // 图集
                        $img_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id=".$id." order by sort asc");
                    
                        $imgs = array();
                        foreach($img_list as $k=>$v)
                        {
                            $focus_imgs[$v['sort']] = $v['img'];
                        }
                    
                        $data['cache_focus_imgs'] = serialize($focus_imgs);
                        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$data);
                        $id = $GLOBALS['db']->insert_id();
                        if($allow_publish_verify){ //自动审核
                            deal_auto_downline($id);
                            $result['status'] = 1;
                            $result['info'] = "上架申请成功";
                        }else{
                            $result['status'] = 1;
                            $result['info'] = "上架申请成功等待管理员审核";
                        }
                    
                    }
                }else{
                    $result['status'] = 0;
                    $result['info'] = "已到期或还未到上架时间，请重现编辑！";
                }
            }else{
                $result['status'] = 0;
                $result['info'] = "数据不存在或权限不足";
    
            }
        }else{
            $result['status'] = 0;
            $result['info'] = "请正确提交数据";
        }
        ajax_return($result);
    }
    
    /**
     * 下架操作
     */
    public function down_line(){
        $account_info = $GLOBALS['account_info'];
        $account_id = $account_info['id'];
        $supplier_id = $account_info['supplier_id'];
        //是否开启自动审核
        $supplier_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id=".$supplier_id);
        if($supplier_data['allow_publish_verify']){
            $allow_publish_verify = $supplier_data['allow_publish_verify'];
            $publish_verify_balance = $supplier_data['publish_verify_balance'];
          
        }
        $id = intval($_REQUEST['id']);
        require_once(APP_ROOT_PATH.'system/model/deal.php');
        if($id>0){
            //商户提交数据
            $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where admin_check_status=0 and deal_id =".$id." and supplier_id=".$supplier_id);
            //真实商品数据
            $deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal d left join " . DB_PREFIX . "deal_location_link dll on dll.deal_id = d.id where id=".$id." and dll.location_id in(".implode(",", $GLOBALS['account_info']['location_ids']).")");
            if($deal_info){
                //数据导入 deal_submit表
                $data = array();      
                $data['admin_check_status'] = 0;
                $data['biz_apply_status'] = 3;
                $data['supplier_id'] = $supplier_id;
                $data['account_id'] = $account_id;
                $data['is_shop'] = 1;
                $data['is_effect'] = 1;
                $data['is_delete'] = 0;
                if($deal_submit_info){ //存在数据
                        if($deal_submit_info['biz_apply_status']!=3){ //更新状态
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$data,"UPDATE","id=".$deal_submit_info['id']);
                            if($allow_publish_verify){ //自动审核
                                deal_auto_downline($deal_submit_info['id']);
                                $result['status'] = 1;
                                $result['info'] = "下架申请成功";
                            }else{
                                $result['status'] = 1;
                                $result['info'] = "下架申请成功等待管理员审核";
                            }
                        }elseif($deal_submit_info['biz_apply_status']==3){
                            if($deal_submit_info['admin_check_status']==0){
                                $result['status'] = 0;
                                $result['info'] = "下架待审核中，请勿重复申请";
                            }elseif($deal_submit_info['admin_check_status']==1){
                                $result['status'] = 0;
                                $result['info'] = "下架申请通过";
                            }elseif($deal_submit_info['admin_check_status']==2){
                                $result['status'] = 0;
                                $result['info'] = "下架申请拒绝";
                            }
                        }
                    
                }else{ //增加新数据

                    $data['deal_id'] = $deal_info['id'];
                    $data['name'] = $deal_info['name'];
                    $data['cate_id'] = $deal_info['cate_id'];
                    $data['city_id'] = $deal_info['city_id'];
                    $data['create_time'] = NOW_TIME;
                    // 图集
                    $img_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id=".$id." order by sort asc");
                    foreach($img_list as $k=>$v)
                    {
                        $focus_imgs[$v['sort']] = $v['img'];
                    }
                    
                    $data['cache_focus_imgs'] = serialize($focus_imgs);
                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",$data);
                    $id = $GLOBALS['db']->insert_id();
                    if($allow_publish_verify){ //自动审核
                        deal_auto_downline($id);
                        $result['status'] = 1;
                        $result['info'] = "下架申请成功";
                    }else{
                        $result['status'] = 1;
                        $result['info'] = "下架申请成功等待管理员审核";
                    }
                }
                
            }else{
                $result['status'] = 0;
                $result['info'] = "数据不存在或权限不足";
                
            }
        }else{
            $result['status'] = 0;
            $result['info'] = "请正确提交数据";
        }
        ajax_return($result);
    }
    
    

    /**
     * 验证提交的 商品数据是否符合
     * 
     * @param unknown $data            
     */
    function check_goods_publish_data($data)
    {
        $is_err = 0;
        if (strim($data['name']) == '' && $is_err == 0) {
            $result['status'] = 0;
            $result['info'] = '商品名称不能为空！';
            $is_err = 1;
        }
        if ($is_err == 0 && strim($data['sub_name']) == '') {
            $result['status'] = 0;
            $result['info'] = '简短名称不能为空！';
            $is_err = 1;
        }

        if ($is_err == 0 && intval($data['shop_cate_id']) == 0) {
            $result['status'] = 0;
            $result['info'] = '请选择分类！';
            $is_err = 1;
        }
        if ($is_err == 0 && count($data['location_id']) <= 0) {
            $result['status'] = 0;
            $result['info'] = '至少支持一家门店！';
            $is_err = 1;
        }
        
        if ($is_err == 0 && count($data['img']) <1) {
            $result['status'] = 0;
            $result['info'] = '至少上传1张图集！';
            $is_err = 1;
        }
		if ($is_err == 0) {
			if($data['max_bought'] < 0){
				$result['status'] = 0;
				$result['info'] = '请输入有效库存！';
				$is_err = 1;
			}
		}
		if($is_err == 0){
			if($_REQUEST['deal_attr']){
				foreach($_REQUEST['deal_attr'] as $k=>$deal_attr){
					foreach($deal_attr as $kk=>$vv){
						if(strim($vv)==''){
							$result['status'] = 0;
							$result['info'] = '请填写商品属性！';
							$is_err = 1;
							break 2;
						}
					}
				}
			}
		}
        if ($is_err == 1) {
            $result['jump'] = '';
            ajax_return($result);
        }
    }

    /**
     * 增加商品分类
     */
    public function load_add_goods_type_weebox()
    {
        $data['html'] = $GLOBALS['tmpl']->fetch("pages/project/deal_add_goods_type_weebox.html");
        ajax_return($data);
    }

    public function do_save_goods_type()
    {
        $account_info = $GLOBALS['account_info'];
        
        $result['status'] = 0;
        $result['info'] = '';
        $result['jump'] = '';
        
        $goods_type_name = strim($_REQUEST['goods_type_name']);
        $goods_attr_arr = $_REQUEST['goods_attr'];
        // 去重复
        $goods_attr_arr = array_unique($goods_attr_arr);
        foreach ($goods_attr_arr as $k => $v) {
            if (strim($v)) {
                $attr_arr[] = $v;
            }
        }
        $goods_attr_arr = $attr_arr;
        $supplier_id = $account_info['supplier_id'];
        // 存在数据
        if ($goods_type_name && $attr_arr) {
            /* 保存分类 */
            $GLOBALS['db']->autoExecute(DB_PREFIX . "goods_type", array(
                "name" => $goods_type_name,
                "supplier_id" => $supplier_id
            ));
            $goods_type_id = $GLOBALS['db']->insert_id();
            if ($goods_type_id) {
                foreach ($goods_attr_arr as $k => $v) {
                    $data = array();
                    $data['name'] = $v;
                    $data['input_type'] = 0;
                    $data['goods_type_id'] = $goods_type_id;
                    $data['supplier_id'] = $supplier_id;
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "goods_type_attr", $data);
                }
                $result['status'] = 1;
            } else {
                $result['status'] = 0;
                $result['info'] = '执行失败请稍后再试';
                $result['jump'] = '';
            }
        } else {
            $result['status'] = 0;
            $result['info'] = '请正确填写数据';
            $result['jump'] = '';
        }
        ajax_return($result);
    }
    /**
     * 输出商户配送类型的单选数据
     * @desc
     * @author    吴庆祥
     */
    private function _assignDeliveryType(){
        //获取所属发布平台输出配置
        $platform_cfg = require APP_ROOT_PATH."/system/public_cfg/platform_type_cfg.php";
        $delivery_cfg = require APP_ROOT_PATH."/system/public_cfg/delivery_type_cfg.php";
        //输出配送类型
        $platform_type = $platform_cfg['supplier'];
        foreach (explode(",",$platform_type['delivery_type']) as $k=>$v){
            $delivery_temp = $delivery_cfg['d_'.$v];
            if($platform_type['default_delivery']==$v){
                $delivery_temp['is_default'] = 1;
            }else{
                $delivery_temp['is_default'] = 0;
            }
            $delivery_type[] = $delivery_temp;
        }
        $GLOBALS['tmpl']->assign("delivery_type",$delivery_type);
    }

    /**
     * 获取配送模板id
     * @desc
     * @author    吴庆祥
     */
    public function getCarriageTemplate(){
        $account_info = $GLOBALS['account_info'];
        $data['status'] = 1;
        $data['info'] = '';
        $data['jump'] = '';
        $data['data']=$GLOBALS['db']->getAll("select id,name,valuation_type as type from ".DB_PREFIX."carriage_template where supplier_id=".$account_info['supplier_id']);
        ajax_return($data);
    }
    private function _assignCarriageTemplate(){
        $account_info = $GLOBALS['account_info'];
        $data=$GLOBALS['db']->getAll("select id,name,valuation_type as type,cache_carriage_detail_data from ".DB_PREFIX."carriage_template where supplier_id=".$account_info['supplier_id']);
        $GLOBALS['tmpl']->assign("carriage_template",$data);
    }
    private function _assignCarriageNumber(){
        $account_info = $GLOBALS['account_info'];
        $number=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."carriage_template where supplier_id=".$account_info['supplier_id']);
        $GLOBALS['tmpl']->assign("carriage_number",$number);
    }
    /**
     * delivery_type的值绑定is_delivery
     * @desc
     * @author    吴庆祥
     */
    private function _deliveryTypeBindIsDelivery($data){
        $delivery_type=intval($_REQUEST['delivery_type']);
        if($delivery_type==1){
            $data['is_delivery']=1;
        }else{
            $data['is_delivery']=0;
        }
       return $data;
    }

    /**
     * @desc      根据id获取地址和模板详情的默认
     * @author    吴庆祥
     */
    public function get_carriage_detail(){
        $data=array();
        $carriage_template_id=$_REQUEST['id'];
        $carriage_template=$GLOBALS['db']->getRow("select id,name,valuation_type as type,cache_carriage_detail_data,province,area,city from ".DB_PREFIX."carriage_template where id=".$carriage_template_id);
        $address=$GLOBALS['db']->getCol("select name from ".DB_PREFIX."delivery_region where id in (".$carriage_template['province'].",".$carriage_template['city'].",".$carriage_template['area'].") order by id");
        $data['address']=implode("-",$address);
        $carriage_template_detail=unserialize($carriage_template['cache_carriage_detail_data']);
        foreach($carriage_template_detail as $key =>$val){
            if(!$val['region_ids']){
                $data['carriage_template_detail']=$val;
            }
        }
        ajax_return($data);
    }
}
