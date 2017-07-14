<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/page.php';
class locationModule extends HizBaseModule
{
    public function __construct()
    {
        parent::__construct();
        global_run();

    }
    
    /**
     * 门店列表
     * 输入：
     * data_id  商户id
     * 
     * 输出：
     * list = array(
     *    [0]=array(
     *      [id] => 128      id 
            [name] => re54qwe      商家名称
            [open_store_payment] => 0     是否支持到店买单
            [is_verify] => 0      是否为认证商家
            [is_effect] => 0      是否禁用
            [cate_name] => KTV    分类
            [edit_url] => /o2onew/hiz.php?ctl=location&act=edit&data_id=128     编辑地址
          )
     * )
     * 
     *   */
	public function index()
	{		
	    /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }
        
        $supplier_id=$_REQUEST['data_id'];

        $supplier_info=$GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id=".$supplier_id);
         
        if($supplier_id && !$supplier_info){
            showHizErr("商家不存在",0,url("hiz","supplier"));
        }
        
        /* 业务逻辑部分 */
        $conditions .= " where s.agency_id = ".$account_id; // 查询条件
        if($supplier_id){
            $conditions .= " and sl.supplier_id=".$supplier_id;
            $GLOBALS['tmpl']->assign("page_title", $supplier_info['name']."-门店列表");
        }else{
            $GLOBALS['tmpl']->assign("page_title", "门店列表");
        }
        
        /* 分页 */
        $page_size = 20;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $sql="select sl.id,sl.name,sl.open_store_payment,sl.is_verify,sl.is_effect,dc.name as cate_name from " . DB_PREFIX . "supplier_location as sl left join " . DB_PREFIX . "supplier as s on s.id=sl.supplier_id left join " . DB_PREFIX . "deal_cate as dc on dc.id=sl.deal_cate_id ".$conditions." group by sl.id order by sl.id desc limit ".$limit;
        $sql_count = " select count(distinct(sl.id)) from " . DB_PREFIX . "supplier_location as sl  left join " . DB_PREFIX . "supplier as s on s.id=sl.supplier_id ".$conditions;
        
        $total = $GLOBALS['db']->getOne($sql_count);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);

        $list = $GLOBALS['db']->getAll($sql);
        
        //分类数据集
        foreach ($list as $t => $v){
            $list[$t]['edit_url']=url("hiz","location#edit",array("data_id"=>$v['id']));
        }
        
        /* 数据 */
        $GLOBALS['tmpl']->assign("supplier_id",$supplier_id);
	    $GLOBALS['tmpl']->assign("list", $list);
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "location"));
	    $GLOBALS['tmpl']->assign("back_url",url("hiz","supplier"));
        
        /* 系统默认 */
        
        $GLOBALS['tmpl']->display("pages/location/index.html");
	}
	
	
	/**
	 * 待审核门店列表
	 * 输出：
	 * list=array(
	 *     [0] => Array
           (
             [id] => 28   
             [name] => 桥亭活鱼小镇（橘园洲店）            门店名称
             [supplier_name] => 桥亭活鱼小镇             所属商家
             [admin_check_status] => 0        审核状态（为0时，显示彻底删除）
             [detail_url] => /o2onew/hiz.php?ctl=location&act=publish_detail&data_id=28   详情页链接
             [status] => 未审核       审核状态说明
           )
	 * )
	 *   */
	public function publish_location()
	{
	    /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }	    
	    
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
	    /* 业务逻辑部分 */
	    $sql="select sl.id,sl.name,s.name as supplier_name,sl.admin_check_status from " . DB_PREFIX . "supplier_location_biz_submit as sl left join " . DB_PREFIX . "supplier as s on s.id=sl.supplier_id where s.agency_id=".$account_id." group by sl.id order by sl.id desc limit " . $limit;
	    $count_sql="select count(distinct(sl.id)) from " . DB_PREFIX . "supplier_location_biz_submit as sl left join " . DB_PREFIX . "supplier as s on s.id=sl.supplier_id where s.agency_id=".$account_id;
	
	    $total = $GLOBALS['db']->getOne($count_sql);
	    $page = new Page($total, $page_size); // 初始化分页对象
	    $p = $page->show();
	    $GLOBALS['tmpl']->assign('pages', $p);
	    

	    $list = $GLOBALS['db']->getAll($sql);
	
	    foreach ($list as $k => $v) {
	        $list[$k]['detail_url'] = url("hiz", "location#publish_detail", array("data_id" => $v['id']));
	        
	        if($v['admin_check_status']==0){
	            $list[$k]['status']="未审核";
	        }
	        elseif ($v['admin_check_status']==1){
	            $list[$k]['status']="通过审核";
	        }
	        else{
	            $list[$k]['status']="拒绝申请";
	        }
	    }
	    
	    /* 数据 */
	    $GLOBALS['tmpl']->assign("list", $list);
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "location"));

	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("page_title", "门店列表");
	    $GLOBALS['tmpl']->display("pages/location/publish_location.html");
	}
	
	/**
	 * 待审核门店详情
	 * info=array(
	 *      [id] => 32
            [name] => 桥亭活鱼小镇（福清店）
            [route] => 
            [address] => 
            [tel] => 2134124323423
            [contact] => 1234353454325
            [xpoint] => 119.358401
            [ypoint] => 25.726495
            [supplier_id] => 22
            [open_time] => fgsdgdsfgg
            [api_address] => 
            [preview] => ./public/attachment/201611/11/12/58254342569fe_100x100.jpg
            [tags] => 
            [is_effect] => 1
            [location_id] => 68
            [cache_supplier_location_area_link] => N;
            [cache_deal_cate_type_location_link] => N;
            [cache_supplier_tag] => 
            [cache_supplier_location_images] => N;
            [biz_apply_status] => 2
            [admin_check_status] => 1
            [is_dc] => 0
            [district] => 
            [memo] => 
            [supplier_location_images] => 
            [area_name] =>  - 
            [cate_name] => 美食
            [status] => 通过审核
	 * )
	 * 
	 *   */
	public function publish_detail(){
	    
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	    
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    $data_id=intval($_REQUEST['data_id']);
	    
	    $info=$GLOBALS['db']->getRow("select sl.*,s.name as s_name from ".DB_PREFIX."supplier_location_biz_submit as sl left join ".DB_PREFIX."supplier as s on sl.supplier_id=s.id where sl.id=".$data_id." and s.agency_id=".$account_id);
	    
	    if(!info){
	        showHizErr("非法数据",0,url("hiz","supplier#publish_supplier"));
	    }
	    
	    //图片处理
	    $info['supplier_location_images']=unserialize($info['cache_supplier_location_images']); 
	    if($info['supplier_location_images']){
	        foreach ($info['supplier_location_images'] as $t => $v){
	            $info['supplier_location_images'][$t]=$v;
	        }
	    }
	    
	    //输出地区
	    $area=unserialize($info['cache_supplier_location_area_link']);
	    $area=implode(',', $area);
	    $area_info=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where id in (".$area.")");
	    $area_name=array();
	    foreach ($area_info as $v){
	        if($v['pid']==0){
	            $main_area=$v['name'];
	        }else {
	            $area_name[]=$v['name'];
	        }
	    }
	    $info['area_name']=$main_area." - ".implode("、", $area_name);
	    
	    //输出分类
	    $info['cate_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id=".$info['deal_cate_id']);
	    $type_id=unserialize($info['cache_deal_cate_type_location_link']);
	    $type_info=$GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_cate_type where id in (".implode(",", $type_id).")");
	    if($type_info){
    	    $type_name=array();
    	    foreach ($type_info as $t => $v){
    	        $type_name[] = $v['name'];
    	    }
    	    $info['cate_name'].=" - ".implode("、", $type_name);
	    }
	    
	    if($info['admin_check_status']==0){
	        $info['status']='未审核';
	    }
	    else if($info['admin_check_status']==1){
	        $info['status']='通过审核';
	    }
	    else{
	        $info['status']='拒绝申请';
	    }
	    
	    /* 数据 */
	    $GLOBALS['tmpl']->assign("info", $info);
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "location"));
	    
	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("page_title", "待审核门店详情");
	    $GLOBALS['tmpl']->display("pages/location/publish_detail.html");
	    
	}
	
	
	/**
	 * 申请门店
	 * 输出：
	 * cate_list=array(        分类
	 *  [1] => Array     一级分类
        (
            [id] => 32    
            [name] => 下午时光          
            [sub_type] => Array     二级分类
                (
                    [0] => Array
                        (
                            [id] => 28
                            [name] => 甜点
                            [is_recommend] => 0
                            [sort] => 0
                        )

                    [1] => Array
                        (
                            [id] => 53
                            [name] => 四星及高档
                            [is_recommend] => 0
                            [sort] => 23
                        )

                )

        )
	 * )
	 *  json_cate_list  json化的分类
	 *  
	 *  area_list = array(     地区列表
	 *      [0] => Array
            (
                [id] => 8
                [name] => 鼓楼区
                [city_id] => 15
                [sort] => 1
                [pid] => 0
                [sub_area_list] => Array     该地区包含的商圈
                (
                    [0] => Array
                    (
                        [id] => 13
                        [name] => 五一广场
                        [city_id] => 15
                        [sort] => 6
                        [pid] => 8
                    )
                )
            )
	 *  )
	 *  json_area_list  json化的地区商圈
	 *   */
	public function publish(){
	    /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }	   
	    
	    /* 获取地区 */
        $area_list = $GLOBALS['db']->getAll("select a.* from ".DB_PREFIX."area a LEFT JOIN fanwe_deal_city dc ON a.city_id = dc.id LEFT JOIN fanwe_delivery_region dr ON dc.code = dr.code where dr.id = ".$account_info['city_id']." and a.pid = 0");
        foreach ($area_list as $t => $v){
            $sub_area_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where pid=".$v['id']);
            $area_list[$t]['sub_area_list']=$sub_area_list?$sub_area_list:array();
        }
        $GLOBALS['tmpl']->assign("area_list",$area_list);
        $GLOBALS['tmpl']->assign("json_area_list",json_encode($area_list));
	    
	    /* 获取分类 */
	    $cate_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_effect = 1 and is_delete = 0 order by sort desc");
	     
	    foreach ($cate_list as $t => $v){
	        $sub_sql="select ct.* from ".DB_PREFIX."deal_cate_type_link as cl left join ".DB_PREFIX."deal_cate_type as ct on ct.id=cl.deal_cate_type_id where cl.cate_id=".$v['id'];
	        $cate_list[$t]['sub_type']=$GLOBALS['db']->getAll($sub_sql);
	    }
	    $GLOBALS['tmpl']->assign("cate_list",$cate_list);
	    $GLOBALS['tmpl']->assign("json_cate_list",json_encode($cate_list));
	    
	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("back_url",url("hiz","location#publish_location"));
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "location"));
	    $GLOBALS['tmpl']->assign("url", url("hiz", "location#select_supplier_dada"));
	    $GLOBALS['tmpl']->assign("page_title", "新增门店");
	    $GLOBALS['tmpl']->display("pages/location/publish.html");
	}

	//配送设置
	public function select_supplier_dada(){
		$supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id= ".$_REQUEST['id']);
		$delivery_min_money = app_conf('DELIVERY_MIN_MONEY');
		$delivery_money = $supplier_info['delivery_money'];
		$location_id = intval($_REQUEST ['location_id']);
		$delivery_money_enough = 1;  //配送费充足
		if($delivery_money < $delivery_min_money){
			$delivery_money_enough = 0; //配送费不足
		}
		$location_info = $GLOBALS['db']->getRow("select delivery_type,id,dada_account from ".DB_PREFIX."supplier_location where id= ".$location_id);

		//查询是否开启达达配送
		$is_open_dada_delivery = $supplier_info['is_open_dada_delivery'];

		//开启了达达配送，是否已注册有无账号
		$data=array();
		$data['delivery_money_enough'] = $delivery_money_enough;
		$data['is_open_dada_delivery'] = intval($is_open_dada_delivery);

		$GLOBALS['tmpl']->assign('location_info',$location_info);
		$GLOBALS['tmpl']->assign('data',$data);
		$html = $GLOBALS['tmpl']->fetch('pages/location/select_supplier_dada.html');

		ajax_return($html);

	}
	
	
	/**
	 *门店申请提交处理
	 *
	 *输入：
	 *supplier_id  所属商户id
	 *name    名称
	 *is_dc   是否支持外卖
	 *tags    标签
	 *preview  logo
	 *supplier_location_images  门店图片
	 *area_id   地区
	 *sub_area_id    地区商圈
	 *cate_id   分类id
	 *deal_cate_type_id  二级分类id
	 *address   地址
	 *route     交通线路
	 *tel    联系电话
	 *contact   联系人
	 *open_time   营业时间
	 *api_address  地图定位地址
	 *xpoint  经度
	 *ypoint  纬度
	 *brief   部门简介
	 *district   门店区域
	 **/
	public function do_save_publish(){
	    /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }	   
	    
	    $id = intval($_REQUEST['id']);

    	$supplier_id= intval($_REQUEST['supplier_id']);
	    
	    // 白名单过滤
	    require_once(APP_ROOT_PATH . 'system/model/no_xss.php');
	    
	    //数据验证
	    if($supplier_id==''){
	        $result['status'] = 0;
	        $result['info'] = '请选择商家';
	        $data['field'] = "supplier_id";
	        ajax_return($result);
	    }
	    $this->check_location_publish_data($_REQUEST);
	    
	    $data['supplier_id'] = $supplier_id; // 所属商户
	    $data['name'] = strim($_REQUEST['name']); // 名称
	    $data['is_dc'] = intval($_REQUEST['is_dc']); // 是否支持订餐
	    $data['tags'] = strim($_REQUEST['tags']); // 标签
	    //供应商标志图片
	    $preview_img = strim($_REQUEST['preview']); // 缩略图
	    if($id > 0){ //更新操作需要替换图片地址
	        $preview_img = replace_domain_to_public($preview_img);
	    }
	    $data['preview'] = $preview_img;
	    //图库
	    $location_images = $_REQUEST['supplier_location_images'];
	    foreach ($location_images as $k=>$v){
	        $cache_location_images[] = replace_domain_to_public($v);
	    }
	    $data['cache_supplier_location_images'] = serialize($cache_location_images);
	    
	    
	    $data['city_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_city where code=".$account_info['city_code']); // 城市
	    $area_id = $_REQUEST['area_id']; // 地区列表
	    $sub_area_id = $_REQUEST['sub_area_id'];
	    $sub_area_id[]=$area_id;
	    $data['cache_supplier_location_area_link'] = serialize($sub_area_id);
	    
	    $data['deal_cate_id'] = intval($_REQUEST['deal_cate_id']); // 分类
	    $deal_cate_type_id = $_REQUEST['deal_cate_type_id']; // 子分类
	    $data['cache_deal_cate_type_location_link'] = serialize($deal_cate_type_id);

		$data['delivery_type']= intval($_REQUEST['delivery_type']);
	    $data['address'] = strim($_REQUEST['address']); // 地址
	    $data['route'] = strim($_REQUEST['route']); // 交通路线
	    $data['tel'] = strim($_REQUEST['tel']); // 联系电话
	    $data['contact'] = strim($_REQUEST['contact']); // 联系人
	    $data['open_time'] = strim($_REQUEST['open_time']); // 营业时间
	    $data['api_address'] = strim($_REQUEST['api_address']); // 地图定位的地址
	    $data['xpoint'] = strim($_REQUEST['xpoint']); // 经度
	    $data['ypoint'] = strim($_REQUEST['ypoint']); // 纬度
	    $data['brief'] = btrim(no_xss($_REQUEST['brief'])); // 部门简介
	    $data['mobile_brief'] = btrim(no_xss($_REQUEST['mobile_brief'])); // 部门简介
	    $data['district']=strim($_REQUEST['district']);
	    
	    /*默认参数*/
	    $data['is_main'] = 0;
	    $data['is_effect'] = 1;
	    
	    // 管理员状态
	    $data['admin_check_status'] = 0; // 待审核
	    
	    
        $data['biz_apply_status'] = 1; // 新增申请

        $list = $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location_biz_submit", $data);
        if ($list) {
            $result['status'] = 1;
            $result['info'] = "提交成功，等待管理员审核";
            $result['jump'] = url("hiz","location#publish_location");
        }
        else 
        {
            $result['status'] = 0;
            $result['info'] = "提交失败，请重新提交";   
        }
	    
	    ajax_return($result);
	}
	
	/**
	 * 门店编辑
	 * 输入：data_id 门店id
	 * 
	 * * 输出：
	 * cate_list=array(        分类
	 *  [1] => Array     一级分类
        (
            [id] => 32    
            [name] => 下午时光 
            [is_check]=>1     是否选择         
            [sub_type] => Array     二级分类
                (
                    [0] => Array
                        (
                            [id] => 28
                            [name] => 甜点
                            [is_check]=>1     是否选择         
                            [is_recommend] => 0
                            [sort] => 0
                        )
                )

        )
	 * )
	 *  json_cate_list  json化的分类
	 *  
	 *  area_list = array(     地区列表
	 *      [0] => Array
            (
                [id] => 8
                [name] => 鼓楼区
                [city_id] => 15
                [sort] => 1
                [pid] => 0
                [is_check]=>1     是否选择         
                [sub_area_list] => Array     该地区包含的商圈
                (
                    [0] => Array
                    (
                        [id] => 13
                        [name] => 五一广场
                        [city_id] => 15
                        [sort] => 6
                        [pid] => 8
                        [is_check]=>1     是否选择         
                    )
                )
            )
	 *  )
	 *  json_area_list  json化的地区商圈
	 *  
	 *  info = array(     编辑的信息
        	 name    名称
        	 is_dc   是否支持外卖
        	 tags    标签
        	 preview  logo
        	 supplier_location_images  门店图片
        	 address   地址
        	 route     交通线路
        	 tel    联系电话
        	 contact   联系人
        	 open_time   营业时间
        	 api_address  地图定位地址
        	 xpoint  经度
        	 ypoint  纬度
        	 brief   部门简介
	 *  )
	 *   */
	public function edit()
	{
	    /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }
	  
	
	    $data_id = intval($_REQUEST['data_id']);
	    
	    $location_info=$GLOBALS['db']->getRow("select sl.*,s.name as supplier_name from " . DB_PREFIX . "supplier_location as sl left join " . DB_PREFIX . "supplier as s on sl.supplier_id = s.id where sl.id=".$data_id." and s.agency_id=".$account_id);

	    if(!$location_info){
	        showHizErr("数据不存在",0,url("hiz","location"));
	    }

	    /* 输出地区 */
	    $location_area=$GLOBALS['db']->getAll("select area_id from ".DB_PREFIX."supplier_location_area_link where location_id=".$data_id);
	    $area_ids=array();
	    foreach ($location_area as $t => $v){
	        $area_ids[]=$v['area_id'];
	    }
	    
	    
	    $area_list = $GLOBALS['db']->getAll("select a.* from ".DB_PREFIX."area a LEFT JOIN fanwe_deal_city dc ON a.city_id = dc.id LEFT JOIN fanwe_delivery_region dr ON dc.code = dr.code where dr.id = ".$account_info['city_id']." and a.pid = 0");
	    
	    foreach ($area_list as $t => $v){
	        
	        $area_list[$t]['sub_area_list']=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where pid=".$v['id']);
	        
	        if(in_array($v['id'],$area_ids)){
	            $area_list[$t]['is_check']=1;
	            $GLOBALS['tmpl']->assign("area_item",$v['id']);
	            foreach ($area_list[$t]['sub_area_list'] as $ttt => $vvv){
	                if(in_array($vvv['id'],$area_ids)){
	                    $area_list[$t]['sub_area_list'][$ttt]['is_check']=1;
	                }
	            }
	        }
	
	    }
	    $GLOBALS['tmpl']->assign("area_list",$area_list);
	    $GLOBALS['tmpl']->assign("json_area_list",json_encode($area_list));
	     
	    /* 获取分类 */
	    $cate_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_effect = 1 and is_delete = 0 order by sort desc");
	    $location_type=$GLOBALS['db']->getAll("select deal_cate_type_id from ".DB_PREFIX."deal_cate_type_location_link where location_id=".$data_id);
	    $type_ids=array();
	    foreach ($location_type as $t => $v){
	        $type_ids[]=$v['deal_cate_type_id'];
	    }
	    
	    foreach ($cate_list as $t => $v){	        
	        $sub_sql="select ct.* from ".DB_PREFIX."deal_cate_type_link as cl left join ".DB_PREFIX."deal_cate_type as ct on ct.id=cl.deal_cate_type_id where cl.cate_id=".$v['id'];
	        $cate_list[$t]['sub_type']=$GLOBALS['db']->getAll($sub_sql);
	        
	        if($v['id']==$location_info['deal_cate_id']){
	            $cate_list[$t]['is_check']=1;
                foreach ($cate_list[$t]['sub_type'] as $ttt => $vvv){
                    if(in_array($vvv['id'], $type_ids))
                        $cate_list[$t]['sub_type'][$ttt]['is_check']=1;
                }
	        }
	    }
	    $GLOBALS['tmpl']->assign("cate_list",$cate_list);
	    $GLOBALS['tmpl']->assign("json_cate_list",json_encode($cate_list));
	    
	    /* 图片输出 */
	    $location_images_data = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "supplier_location_images  where supplier_location_id = ".$data_id." and status=1");
	    
	    $location_info['img_count']=count($location_images_data);
	    
	    foreach ($location_images_data as $t => $v){
	        $location_info['location_images'][]=$v['image'];
	    }
        $location_info['preview']=$location_info['preview'];
	    
	    /* 数据 */
	    $GLOBALS['tmpl']->assign("info", $location_info); // 门店所有数据
	    
	
	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "location"));
		$GLOBALS['tmpl']->assign("url", url("hiz", "location#select_supplier_dada"));
	    $GLOBALS['tmpl']->assign("page_title",$location_info['supplier_name']."-门店资料编辑");
	    $GLOBALS['tmpl']->display("pages/location/edit.html");
	}
	
	/**  
	 * 门店资料修改
	       输入：
	    id      门店id
        name    名称
        is_dc   是否支持外卖
        tags    标签
        preview  logo
        supplier_location_images  门店图片
        area_id   地区
        sub_area_id    地区商圈
        deal_cate_id   分类id
        deal_cate_type_id  二级分类id
        address   地址
        route     交通线路
        tel    联系电话
        contact   联系人
        open_time   营业时间
        api_address  地图定位地址
        xpoint  经度
        ypoint  纬度
        brief   部门简介
        district  门店区域
	 * */
	public function do_location_update(){
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	    
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    // 白名单过滤
	    require_once(APP_ROOT_PATH . 'system/model/no_xss.php');
	    
	    $this->check_location_publish_data($_REQUEST);
	    
	    /*获取参数*/
	    $id = intval($_REQUEST['id']);
	     
	    $location_data = $GLOBALS['db']->getRow("select sl.* from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."supplier as s on sl.supplier_id=s.id where sl.id=".$id." and s.agency_id=".$account_id);
	    if(empty($location_data)){
	        $result['status'] = 0;
	        $result['info'] = "数据不存在或没有权限操作该数据";
	        ajax_return($result);
	    }
	    
	    $location_submit_info=$GLOBALS['db']->getRow("select * from " . DB_PREFIX . "supplier_location_biz_submit where location_id=".$id);
	    
	    //供应商标志图片
	    $preview_img = strim($_REQUEST['preview']); // 缩略图
	    $data['preview'] = replace_domain_to_public($preview_img);
	    
	    //图库
	    $location_images = $_REQUEST['supplier_location_images'];
	    foreach ($location_images as $k=>$v){
	        $f_location_images[] = replace_domain_to_public($v);
	    }
		$data['delivery_type']= intval($_REQUEST['delivery_type']);
	    $data['address'] = strim($_REQUEST['address']); // 地址
	    $data['route'] = strim($_REQUEST['route']); // 交通路线
	    $data['tel'] = strim($_REQUEST['tel']); // 电话
	    $data['contact'] = strim($_REQUEST['contact']); // 联系人
	    $data['open_time'] = strim($_REQUEST['open_time']); // 营业时间
	    $data['api_address'] = strim($_REQUEST['api_address']); // 地图定位的地址
	    $data['xpoint'] = strim($_REQUEST['xpoint']); // 经度
	    $data['ypoint'] = strim($_REQUEST['ypoint']); // 纬度
	    $data['district']=strim($_REQUEST['district']);
	    $data['brief'] = btrim(no_xss($_REQUEST['brief'])); // 部门简介
	    $data['mobile_brief'] = btrim(no_xss($_REQUEST['mobile_brief'])); // 部门简介
	    
	    $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location", $data, "UPDATE", " id=" . $id);
	    /* logger::write($GLOBALS['db']->affected_rows());
	    if($GLOBALS['db']->affected_rows() || $f_location_images){ */
	        //更新图库
	        $GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_images where supplier_location_id=".$id);
	     
	        if(count($f_location_images)>0){
	    
	            foreach($f_location_images as $k=>$v){
	                $imgdata = array();
	                $imgdata['image'] = $v;
	                $imgdata['sort'] = 100;
	                $imgdata['create_time'] = NOW_TIME;
	                $imgdata['supplier_location_id'] = $id;
	                $imgdata['status'] = 1;
	    
	                $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location_images", $imgdata);
	            }
	        }
	        $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location", array("image_count"=>count($f_location_images)), "UPDATE", " id=" . $id);
	
	        //更新后更新商户缓存
	        rm_auto_cache("store",array("id"=>$id));
	        
	        
	        //判断基本内容是否修改
	        $data['name']=strim($_REQUEST['name']);
	        $data['tags'] = strim($_REQUEST['tags']);
	        $data['is_dc'] = intval($_REQUEST['is_dc']);
	        $data['deal_cate_id'] = intval($_REQUEST['deal_cate_id']); // 分类
	        
	        $deal_cate_type_id = $_REQUEST['deal_cate_type_id']; // 子分类
	        $area_id = intval($_REQUEST['area_id']); // 地区列表
	        $sub_area_id=$_REQUEST['sub_area_id'];
	        $sub_area_id[]=$area_id;
	        
	        $is_change=0;
	        
	        $location_info=$GLOBALS['db']->getRow("select name,tags,is_dc,deal_cate_id from ".DB_PREFIX . "supplier_location where id=".$id);
	        
	        if($data['name']!=$location_info['name'] || $data['tags']!=$location_info['tags']
	           || $data['is_dc']!=$location_info['is_dc'] || $data['deal_cate_id']!=$location_info['deal_cate_id'])
	        {
	            $is_change=1;
	        }else {
	            $location_type=$GLOBALS['db']->getAll("select deal_cate_type_id from ".DB_PREFIX . "deal_cate_type_location_link where location_id=".$id);
	            $cate_type=array();
	            foreach ($location_type as $t => $v){
	                $cate_type[]=$v['deal_cate_type_id'];
	            }
	            
	            if(array_diff($cate_type,$deal_cate_type_id) || array_diff($deal_cate_type_id,$cate_type))
	            {
	                $is_change=1;
	            }
	            else 
	            {
	                /* $sub_area_id[]=$area_id; */
	                $location_area_id=$GLOBALS['db']->getAll("select area_id from ".DB_PREFIX . "supplier_location_area_link where location_id=".$id);
	                $location_area=array();
	                foreach ($location_area_id as $t => $v){
	                    $location_area[]=$v['area_id'];
	                }
	                
	                if(array_diff($location_area,$sub_area_id) || array_diff($sub_area_id,$location_area))
	                {
	                    $is_change=1;
	                }
	            }
	        }
	        
	        //门店提交审核修改内容
	        //图库
	        
	        $data['cache_supplier_location_images'] = serialize($f_location_images);
	         
	        $area_id = intval($_REQUEST['area_id']); // 地区列表
	        $sub_area_id = $_REQUEST['sub_area_id']; // 地区列表
	        $sub_area_id[]=$area_id;
	        $data['cache_supplier_location_area_link'] = serialize($sub_area_id);
	        
	        $deal_cate_type_id = $_REQUEST['deal_cate_type_id']; // 子分类
	        $data['cache_deal_cate_type_location_link'] = serialize($deal_cate_type_id);
	        $data['is_effect'] = 1;
	        
	        // 管理员状态
	        $data['admin_check_status'] = 0; // 待审核
	        $data['biz_apply_status'] = 2; // 修改申请
	        
	        if($is_change==1){
	            $location_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "supplier_location
                            where id=".$id);
	             
	            if(empty($location_info)){
	                $result['status'] = 0;
	                $result['info'] = "数据不存在或没有权限操作该数据";
	                ajax_return($result);
	                exit;
	            }
	            $new_data = $location_info;
	            $new_data['location_id'] = $location_info['id'];
	            unset($new_data['id']);
	             
	            //如果数据已经有存在，通过审核的数据，先清除掉在进行插入更新操作
	            if($location_submit_info && $location_submit_info['admin_check_status']!=0){//删除已审核 或 拒绝的数据
	                $GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_biz_submit where id=".$location_submit_info['id']);
	                //先建立数据
	                $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",$new_data);
	                $location_submit_id = $GLOBALS['db']->insert_id();
	            }
	            elseif (!$location_submit_info){
	                //先建立数据
	                $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",$new_data);
	                $location_submit_id = $GLOBALS['db']->insert_id();
	            }else {
	                $location_submit_id=$location_submit_info['id'];
	            }
	            
	            $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location_biz_submit", $data, "UPDATE", " id=" . $location_submit_id);
	            if($GLOBALS['db']->affected_rows()){
    	            $result['status'] = 1;
    	            $result['info'] = "修改成功，等待管理员审核";
    	            $result['jump'] = url("hiz","location#publish_location");
	            }else {
	                $result['status'] = 0;
	                $result['info'] = "修改失败，请稍后再试";
	            }
	        }
	        else {
	            if($location_submit_info && $location_submit_info['admin_check_status']==0)
	            {
	                $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location_biz_submit", $data, "UPDATE", " id=" . $location_submit_info['id']);
	            }
	            
	            $result['status'] = 1;
	            $result['info'] = "修改成功";
	        }
	        
	    //}
	    /* else{
	        
            $result['status'] = 0;
            $result['info'] = "修改失败，请稍后再试";
        } */
		
		ajax_return($result);
	
	}
	
    /**
     * 获取子分类
     **/
    public function load_sub_cate(){
        $cate_id = intval($_REQUEST['cate_id']);
        $id = intval($_REQUEST['id']); //门店id
    
        $sub_cate_list = $GLOBALS['db']->getAll("select c.* from ".DB_PREFIX."deal_cate_type as c left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = c.id where l.cate_id = ".$cate_id);
        
        
        
        ajax_return($sub_cate_list);
    }
	
	/**
	 * 城市子集地区
	 */
	public function load_area_list_box()
	{
	    $id =  intval($_REQUEST['id']); //门店id
	    $city_id = intval($_REQUEST['city_id']);
	    $edit_type = intval($_REQUEST['edit_type']);
	    
	    if($edit_type == 1){//来自管理员
	        $location_curr_area = $GLOBALS['db']->getAll("select area_id from ".DB_PREFIX."supplier_location_area_link where location_id = ".$id);
	        foreach ($location_curr_area as $k=>$v){
	            $f_curr_area[] = $v['area_id'];
	        }
	    }
	    
	    if($edit_type == 2){//来自商户提交
	        $location_curr_area = $GLOBALS['db']->getOne("select cache_supplier_location_area_link from ".DB_PREFIX."supplier_location_biz_submit where id = ".$id);
	        $f_curr_area = unserialize($location_curr_area);
	    }
	    
	    $area_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where city_id = ".$city_id);
	    
	    
        foreach($area_list as $k=>$v)
        {
            if(in_array($v['id'], $f_curr_area))
            {
                $area_list[$k]['checked'] = true;
            }
        }
	    $GLOBALS['tmpl']->assign("area_list",$area_list);
	    echo $GLOBALS['tmpl']->fetch("inc/area_box.html");
	}
	
	/**
	 * 表单验证
	 */
	private function check_location_publish_data($data){
	    $id = intval($data['id']);
	    $edit_type = intval($data['edit_type']);
	    
	    if(strim($data['name'])==''){
	        $result['status'] = 0;
	        $result['info'] = '门店名称不允许为空';
	        $data['field'] = "name";
	        ajax_return($result);
	    }
	    
	    $sql = "select count(*) from ".DB_PREFIX."supplier_location where name=".$data['name']." and is_delete=0 ";
	    $publish_sql="select count(*) from ".DB_PREFIX."supplier_location_biz_submit where admin_check_status=0 and name=".$data['name']." and is_delete=0 ";
	    if($data['location_id']){
	        $sql.=" and id<>".$data['location_id'];
	        $publish_sql.=" and location_id<>".$data['location_id'];
	    }
	    /*查询是否有重复数据*/
	    if($GLOBALS['db']->getOne($sql) || $GLOBALS['db']->getOne($publish_sql)){
	        $result['status'] = 0;
	        $result['info'] = '门店名称已被使用';
	        ajax_return($result);
	    }
	    
	    if($data['area_id']==""){
	        $result['status'] = 0;
	        $result['info'] = '请选择地区';
	        $data['field'] = "deal_cate_id";
	        ajax_return($result);
	    }
	    /* if($data['sub_area_id']==""){
	        $result['status'] = 0;
	        $result['info'] = '请选择商圈';
	        $data['field'] = "deal_cate_id";
	        ajax_return($result);
	    } */
	    if($data['deal_cate_id']==""){
	        $result['status'] = 0;
	        $result['info'] = '请选择经营分类';
	        $data['field'] = "deal_cate_id";
	        ajax_return($result);
	    }
	    if(strim($data['preview'])==""){
	        $result['status'] = 0;
	        $result['info'] = '请上传门店logo';
	        $data['field'] = "preview";
	        ajax_return($result);
	    }
	    /* if(count($data['supplier_location_images'])==0){
	        $result['status'] = 0;
	        $result['info'] = '请上传至少一张的门店图片';
	        $data['field'] = "supplier_location_images";
	        ajax_return($result);
	    } */
	    if(strim($data['address'])==''){
	        $result['status'] = 0;
	        $result['info'] = '请填写门店地址';
	        $data['field'] = "address";
	        ajax_return($result);
	    }
	    if($data['xpoint']=='' || $data['ypoint']=='' || $data['district']==''){
	        $result['status'] = 0;
	        $result['info'] = '请选择地址定位';
	        $data['field'] = "api_address";
	        ajax_return($result);
	    }
	    if(strim($data['contact'])==""){
	        $result['status'] = 0;
	        $result['info'] = '请填写联系人';
	        $data['field'] = "contact";
	        ajax_return($result);
	    }
	    if($data['tel']==""){
	        $result['status'] = 0;
	        $result['info'] = '请输入联系方式';
	        $data['field'] = "tel";
	        ajax_return($result);
	    }
	    /* if(!check_mobile($data['tel'])){
	        $result['status'] = 0;
	        $result['info'] = '请输入正确的手机号码';
	        $data['field'] = "tel";
	        ajax_return($result);
	    } */

	    
	    return true;
	}
	
	/**
	 * 搜素商户
	 * 输入 ： key  关键字
	 *   */
	public function search_supplier_location()
	{
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	    
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
        $sql  ="select id,name from ".DB_PREFIX."supplier where name like '%".strim($_REQUEST['key'])."%' and agency_id=".$account_id;
	    
	    $supplier_info = $GLOBALS['db']->getAll($sql);
	    
	    if($supplier_info){
	        $data['status']=1;
	        $data['info']="搜索成功";
	        $data['list']=$supplier_info;
	    }else{
	        $data['status']=0;
	        $data['info']="该商户不存在";
	    }
	    ajax_return($data);
	}
	
	/**
	 * 设置门店状态
	 *   */
	public function is_effect(){
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	     
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    $data_id=$_REQUEST['data_id'];
	    
	    $location = $GLOBALS['db']->getRow("select sl.id,sl.is_effect from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."supplier as s on sl.supplier_id=s.id where sl.id=".$data_id." and s.agency_id=".$account_id);
	    
	    if(!$location){
	        $data['status']=false;
	        $data['info']="非法操作";
	        ajax_return($data);
	    }
	    
	    if($location['is_effect']==0){
	        $new_data['is_effect']=1;
	    }else {
	        $new_data['is_effect']=0;
	    }
	    $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location", $new_data, "UPDATE", " id=".$data_id);
	    
	    if($GLOBALS['db']->affected_rows()){
	        $new_data['status']=true;
	        $new_data['info']="操作成功";
	        ajax_return($new_data);
	    }else {
	        $new_data['status']=false;
	        $new_data['info']="操作失败";
	        ajax_return($new_data);
	    }
	    
	}
	
	
	/**
	 * 删除申请
	 *   */
	public function delete_publish(){
	    $account_info = $GLOBALS['hiz_account_info'];
	     
	    $account_id = $account_info['id'];
	     
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	
	    $data_id=$_REQUEST['data_id'];
	
	    $id=$GLOBALS['db']->getOne("select sl.id from ".DB_PREFIX."supplier_location_biz_submit as sl left join ".DB_PREFIX."supplier as s on sl.supplier_id=s.id where sl.id=".$data_id." and s.agency_id=".$account_id." and sl.admin_check_status=0");
	
	    if(!$id){
	        $data['status']=true;
	        $data['info']="操作有误";
	        ajax_return($data);
	    }
	
	    $GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_biz_submit where id=".$id." and admin_check_status=0");
	
	    if($GLOBALS['db']->affected_rows()){
	        $data['status']=true;
	        $data['info']="申请删除成功";
	        ajax_return($data);
	    }
	    else{
	        $data['status']=true;
	        $data['info']="申请删除失败";
	        ajax_return($data);
	    }
	}
	
}
?>