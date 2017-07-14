<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class storesApiModule extends MainBaseApiModule
{
	
	/**
	 * 商家列表接口
	 * 输入：
	 * cate_id: int 大分类ID
	 * tid: int 小分类ID
	 * page:int 当前的页数
	 * keyword: string 关键词
	 * qid: int 商圈ID
	 * order_type: string 排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序))
	 * 
	 * latitude_top:float 最上边纬线值 ypoint
	 * latitude_bottom:float 最下边纬线值 ypoint
	 * longitude_left:float 最左边经度值  xpoint
	 * longitude_right:float 最右边经度值 xpoint
	 * 
	 * 
	 * 
	 * 输出：
	 * city_id:int 当前城市ID
	 * area_id:int 当前大区ID
	 * quan_id:int 当前商圈ID
	 * cate_id:int 当前大分类ID
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * item:array:array 商家列表，结构如下
	 * Array
        (
            [0] => Array
                (
                    [preview] => http://192.168.1.41/o2onew/public/attachment/201502/25/14/54ed67b2cd14b_140x85.jpg [string] 商户图片 140x85
                    [id] => 22 [int] 商家ID
                    [is_verify] => 0 [int] 是否为认证商户 0:否 1:是
                    [avg_point] => 0.0000 [float] 平均分
                    [address] => 晋安区新店镇五四北泰禾广场六楼（中影影院旁，音乐-百度KTV旁边） [string] 地址
                    [name] => 桥亭活鱼小镇（泰禾广场店）[string] 商家名称
                    [distance] => 0 [float] 距离
                    [xpoint] => [float] 所在经度
                    [ypoint] => [float] 所在纬度
                )
        )
	 * bcate_list:array 大类列表
	 * 结构如下
	 * Array(
	 * 		Array
	        (
	            [id] => 0 [int]分类ID
	            [name] => 全部分类 [string] 分类名
	            [icon_img] => [string] app端使用的分类图标
	            [iconfont]=> [string] wap端使用的iconfont代码
	            [iconcolor]=> #f0f0f0 [string] 颜色配置 16进度
	            [bcate_type] => Array
	                (
	                    [0] => Array
	                        (
	                            [id] => 0 [int]小分类ID
	                            [cate_id] => 0 [int]父分类ID
	                            [name] => 全部分类 [string] 分类名称
	                        )
	
	                )
	
	        )
	 )
	 * quan_list:array 商圈列表
	 * 结构如下
	 * Array(
	 * 		Array
	        (
	            [id] => 0 [int] 大区ID
	            [name] => 全城 [string] 大区名称
	            [quan_sub] => Array
	                (
	                    [0] => Array
	                        (
	                            [id] => 0 [int] 小区ID
	                            [pid] => 0 [int] 大区ID
	                            [name] => 全城 [string] 商圈名称
	                        )
	
	                )
	
	        )
	 * )
	 * navs:array 排序菜单 
	 * 固定数据如下
	 * array(
			array("name"=>"默认","code"=>"default"),
			array("name"=>"好评","code"=>"avg_point"),
			array("name"=>"最新","code"=>"newest"),
		);
	 * 
	 */
	public function index()
	{
		$root = array();
		$page_title = '商家列表';

		//缓存下来的地区配置
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));

		$root = array();
		$catalog_id = intval($GLOBALS['request']['cate_id']);//商品分类ID
		$cata_type_id=intval($GLOBALS['request']['tid']);//商品二级分类
		$city_id = intval($GLOBALS['city']['id']);//城市分类ID			
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		$page=$page==0?1:$page;
		$quan_id_l = intval($GLOBALS['request']['qid']); //商圈id	
		$area_id_l = intval($area_data[$quan_id_l]['pid']); //大区id
	
		if($area_id_l ==0 && $quan_id_l>0){
			$area_id =$quan_id= intval($GLOBALS['request']['qid']); //商圈id
		}else{
			$quan_id = intval($GLOBALS['request']['qid']); //商圈id	
		    $area_id = intval($area_data[$quan_id_l]['pid']); //大区id
		}
		
		$order_type=strim($GLOBALS['request']['order_type']);


		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = $GLOBALS['geo']['ypoint'];  //ypoint 
		$xpoint = $m_longitude = $GLOBALS['geo']['xpoint'];  //xpoint
		// $xpoint = 119.350423; // 模拟数据
		// $ypoint = 26.058986; // 模拟数据
		
		/*输出分类*/
		$bcate_list = getCateList();
		
		/*输出商圈*/
		$quan_list=getQuanList($city_id);
		
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$ext_condition = '';
		if($keyword) {		
			$kw_unicode = str_to_unicode_string($keyword);			
			$ext_condition.=" ( sl.name like '%".$keyword."%' or match(sl.tags_match) against('".$kw_unicode."' IN BOOLEAN MODE) ) ";
		}
		
		if($xpoint>0)/* 排序（$order_type）  default 智能（默认），nearby  离我最近*/
		{		
			$pi = PI;  //圆周率
			$r = EARTH_R;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
			
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
			{
				if($ext_condition!="")
				$ext_condition.=" and ";
				$ext_condition.= " sl.ypoint > $ybottom and sl.ypoint < $ytop and sl.xpoint > $xleft and sl.xpoint < $xright ";
				
				$limit = 300;
			}
			$order = " distance asc ";
		}
		else
			$order = "";

		/*排序  
		 智能排序和 离我最的 是一样的 都以距离来升序来排序，只有这两种情况有传经纬度过来，就没有把 这两种情况写在 下面的判断里，写在上面了。
		default 智能（默认），nearby  离我，avg_point 评价，newest 最新，buy_count 人气，price_asc 价低，price_desc 价高 */
		if($order_type=='avg_point'){/*评价*/
			$order= " sl.avg_point DESC";
		} elseif($order_type=='newest') {/*最新*/
			$order= " sl.id DESC";
		} elseif ($order_type == 'distance') {
			if ($xpoint > 0) {
				$order = ' distance ASC';
			}
		}

		// 获取门店的优惠买单信息
		$psql = 'SELECT class_name, description,supplier_id FROM '.DB_PREFIX.'promote WHERE supplier_id <> 0';
		$temp_pro = $GLOBALS['db']->getAll($psql);
		$promote = array();
		$promote_ids = array();
		if ($temp_pro) {
			foreach ($temp_pro as $item) {
				global $is_app;
				if (APP_INDEX=='wap' && substr($item['class_name'],0,3) == 'App') {
					continue;
				}
				$promote[$item['supplier_id']][] = $item['description'];
				$promote_ids[] = $item['supplier_id'];
			}
			$promote_ids = array_unique($promote_ids);
		}
		$root['promote'] = $promote;

		// 只获取开启到店支付的门店
		if ($GLOBALS['request']['payment']) {
			if($ext_condition!=""){
				$ext_condition.=" and ";
			}
			$ext_condition .= ' sl.open_store_payment = 1';// and sl.supplier_id in ('.implode(',', $promote_ids).')';
			$page_title = '优惠买单';
		}		

		$condition_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
		require_once(APP_ROOT_PATH."system/model/supplier.php");
		$deal_result  = get_location_list($limit,$condition_param,"",$ext_condition,$order,$field_append);
		
		$list = $deal_result['list'];
		$location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location as sl where ".$deal_result['condition']);
		
		$count = count($location_list);
		$page_total = ceil($count/$page_size);

		// 商户门店ID数组 查询分类数量用
		$location_ids = array();
		foreach ($location_list as $val) {
			$location_ids[] = $val['id'];
		}

		$cateSql_param = array("aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
		$cate_result = get_location_list(9999,$cateSql_param,"",$ext_condition,$order,$field_append);
		$cate_ids = array();
		$cate_count = array();
		foreach ($cate_result['list'] as $k => $cate) {
			$cate_ids[] = $cate['id'];
			$cate_count[$cate['deal_cate_id']][] = $cate['id'];
		}

		$quanSql_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"city_id"=>intval($GLOBALS['city']['id']));
		$quan_result = get_location_list(9999,$quanSql_param,"",$ext_condition,$order,$field_append);
		$quan_ids = array();
		foreach ($quan_result['list'] as $k => $quan) {
			$quan_ids[] = $quan['id'];
		}

		$goodses = array(); // 格式化信息
		$supplier_ids = array(); // 商户ID数组
		foreach($list as $k=>$v)
		{
			$goodses[$k] = format_store_list_item($v);
			$goodses[$k]['preview'] = get_abs_img_root(get_spec_image($v['preview'],92,82,1));
			$supplier_ids[] = $v['supplier_id'];
		}
		
		// 获取门店的优惠买单信息
		/*$psql = 'SELECT class_name, description,supplier_id FROM '.DB_PREFIX.'promote WHERE supplier_id <> 0';
		$temp_pro = $GLOBALS['db']->getAll($psql);
		$promote = array();
		if ($temp_pro) {
			foreach ($temp_pro as $item) {
				global $is_app;
				if ($is_app != 1 && substr($item['class_name'],0,3) == 'App') {
					continue;
				}
				$promote[$item['supplier_id']][] = $item['description'];
			}
		}
		$root['promote'] = $promote;*/

		// 获取 商圈id => 名称 的数组
		// 和 商圈id => 数量的数组
		/*$sql = 'SELECT name,location_id,id,pid FROM '.DB_PREFIX.'supplier_location_area_link AS s1 INNER JOIN '.DB_PREFIX.'area AS a1 ON s1.area_id=a1.id WHERE city_id='.$city_id.' AND location_id IN ('.implode(',', $quan_ids).')  ORDER BY pid';
		$location_info_temp = $GLOBALS['db']->getAll($sql);
		$location_id_name = array();// GROUP BY location_id
		$location_count_temp = array();
		$location_quan_count = array(); // 商圈数量统计
		foreach ($location_info_temp as $item) {
			//if (empty($GLOBALS['request']['qid'])) {
				$location_id_name[$item['location_id']] = $item['name'];
			//}
			if ($item['pid'] == 0) {
				$location_count_temp[$item['id']][$item['id']][] = $item;
				$location_quan_count[$item['id']][] = $item['id'];
			} else {
				$location_count_temp[$item['pid']][$item['id']][] = $item;
				$location_quan_count[$item['pid']][] = $item['id'];
			}
		}
		$location_count = array();
		foreach ($location_count_temp as $k => $v) {
			foreach ($v as $kk => $vv) {
				if ($k != $kk) {
					$location_count[$k][$kk] = count($vv);
				} else {
					$location_count[$k][$kk] = count(array_unique($location_quan_count[$k]));
				}
			}
		}
		foreach ($location_count as $key => $val) {
			if (!array_key_exists($key, $val)) {
				$location_count[$key][$key] = count($val);
			}
		}
		// 商圈的id=>count 数组,包含统计类别数量
		$root['location_count'] = $location_count;
		// 当前商圈的id=>名称数组
		$root['location_info'] = $location_id_name;
		$root['location_total'] = count($quan_ids);*/
		
		// 各商圈的汇总数量查询和格式化
		$sql = 'SELECT name,location_id,id,pid FROM '.DB_PREFIX.'supplier_location_area_link AS s1 INNER JOIN '.DB_PREFIX.'area AS a1 ON s1.area_id=a1.id WHERE city_id='.$city_id.' AND location_id IN ('.implode(',', $quan_ids).')';
		$location_info_temp = $GLOBALS['db']->getAll($sql);
		$location_id_name = array(); // 商圈id=>名称数组
		$location_count = array(); // 大商圈统计
		$location_item_count = array(); // 小商圈统计
		foreach ($location_info_temp as $item) {
			if ($item['pid'] == 0) {
				$location_count[$item['id']][] = $item['location_id'];
			} else {
				$location_count[$item['pid']][] = $item['location_id'];
				$location_item_count[$item['id']][] = $item['location_id'];
			}
			if (!empty($quan_id_l) && $item['id'] == $quan_id_l) {
				$location_id_name[$item['location_id']] = $item['name'];
				continue;
			}
			$location_id_name[$item['location_id']] = $item['name'];
			
		}
		$root['location_count'] = $location_count;
		$root['location_item_count'] = $location_item_count;
		$root['location_id_name'] = $location_id_name;
		$root['location_total'] = count($quan_ids);
		// print_r(array($location_count, $location_item_count, $location_id_name));exit;
		// 获取商家的商品分类数量
		/*$loc_sql = 'SELECT COUNT(location_id) AS counts, location_id, dctll.`deal_cate_type_id`, cate_id FROM '.DB_PREFIX.'deal_cate_type_location_link AS dctll LEFT JOIN '.DB_PREFIX.'deal_cate_type_link dctl ON dctll.`deal_cate_type_id`=dctl.`deal_cate_type_id` INNER JOIN '.DB_PREFIX.'deal_cate_type dct ON dct.id=dctll.`deal_cate_type_id` WHERE location_id IN ('.implode(',', $cate_ids).') GROUP BY cate_id, dctll.`deal_cate_type_id`';
		$cate_count_temp = $GLOBALS['db']->getAll($loc_sql);
		$cate_count = array();
		$location_cate_temp = array();
		foreach ($cate_count_temp as $k => $v) {
			$cate_count[$v['cate_id']][$v['deal_cate_type_id']] = $v['counts'];
			$location_cate_temp[$v['cate_id']][] = $v['location_id'];
		}
		$root['cate_count'] = $cate_count;
		$root['location_cate_count'] = $location_cate_temp;
		$root['cate_total'] = count($cate_ids);*/

		// 各分类的汇总数量查询和格式化
		$loc_sql = 'SELECT ll.*, tl.cate_id FROM '.DB_PREFIX.'deal_cate_type_location_link AS ll INNER JOIN '.DB_PREFIX.'deal_cate_type_link AS tl ON tl.deal_cate_type_id=ll.deal_cate_type_id WHERE location_id IN ('.implode(',', $cate_ids).')';
		$cate_count_temp = $GLOBALS['db']->getAll($loc_sql);
		$cate_item_count = array();
		// $cate_count = array();
		foreach ($cate_count_temp as $cate) {
			// $cate_count[$cate['cate_id']][] = $cate['location_id'];
			$cate_item_count[$cate['cate_id']][$cate['deal_cate_type_id']][] = $cate['location_id'];
		}
		$root['cate_count'] = $cate_count;
		$root['cate_item_count'] = $cate_item_count;
		$root['cate_total'] = count($cate_ids);

		// 获取商户的商品销量数据
		/*$deal_count_sql = 'SELECT supplier_id, COUNT(buy_count) AS counts FROM '.DB_PREFIX.'deal GROUP BY supplier_id';
		$deal_counts = $GLOBALS['db']->getAll($deal_count_sql);
		$counts = array();
		foreach ($deal_counts as $dcount) {
			$counts[$dcount['supplier_id']] = $dcount['counts'];
		}
		$root['deal_counts'] = $counts;*/
		
		$root['city_id']= $city_id;
		$root['area_id']= $area_id;
		$root['quan_id']= $quan_id;
		$root['cate_id']=$catalog_id;
	
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title'] = $page_title;
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		$root['item'] = $goodses?$goodses:array();
		$root['bcate_list'] = $bcate_list?$bcate_list:array();
		$root['quan_list'] = $quan_list?$quan_list:array();
		
		//排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
		$root['navs'] = array(
			array("name"=>"默认","code"=>"default"),
			array("name"=>"距离","code"=>"distance"),
			array("name"=>"好评","code"=>"avg_point"),
			array("name"=>"最新","code"=>"newest"),
		);
		
		return output($root);
	}
	

	public function wap_index()
	{
		$root = array();
		$page_title = '商家列表';

		//缓存下来的地区配置
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));

		$root = array();
		$catalog_id = intval($GLOBALS['request']['cate_id']);//商品分类ID
		$cata_type_id=intval($GLOBALS['request']['tid']);//商品二级分类
		$city_id = intval($GLOBALS['city']['id']);//城市分类ID			
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		$page=$page==0?1:$page;
		$quan_id_l = intval($GLOBALS['request']['qid']); //商圈id	
		$area_id_l = intval($area_data[$quan_id_l]['pid']); //大区id
	
		if($area_id_l ==0 && $quan_id_l>0){
			$area_id =$quan_id= intval($GLOBALS['request']['qid']); //商圈id
		}else{
			$quan_id = intval($GLOBALS['request']['qid']); //商圈id	
		    $area_id = intval($area_data[$quan_id_l]['pid']); //大区id
		}
		
		$order_type=strim($GLOBALS['request']['order_type']);


		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = $GLOBALS['geo']['ypoint'];  //ypoint 
		$xpoint = $m_longitude = $GLOBALS['geo']['xpoint'];  //xpoint
		
		
		/*输出商圈*/
		$quan_list=getQuanList($city_id);
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$ext_condition = '';
		if($keyword) {		
			$kw_unicode = str_to_unicode_string($keyword);			
			$ext_condition.=" ( sl.name_match_row like '%".$keyword."%' or match(sl.tags_match) against('".$kw_unicode."' IN BOOLEAN MODE) ) ";
		}
		
		if($xpoint>0) {/* 排序（$order_type）  default 智能（默认），nearby  离我最近*/
			$pi = PI;  //圆周率
			$r = EARTH_R;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0) {
				if($ext_condition!="")
				$ext_condition.=" and ";
				$ext_condition.= " sl.ypoint > $ybottom and sl.ypoint < $ytop and sl.xpoint > $xleft and sl.xpoint < $xright ";
				
				$limit = 300;
			}
			$order = " distance asc ";
		} else {
			$order = "";
		}

		/*排序  
		 智能排序和 离我最的 是一样的 都以距离来升序来排序，只有这两种情况有传经纬度过来，就没有把 这两种情况写在 下面的判断里，写在上面了。
		default 智能（默认），nearby  离我，avg_point 评价，newest 最新，buy_count 人气，price_asc 价低，price_desc 价高 */
		if($order_type=='avg_point'){/*评价*/
			$order= " sl.avg_point DESC";
		} elseif($order_type=='newest') {/*最新*/
			$order= " sl.id DESC";
		} elseif ($order_type == 'distance') {
			if ($xpoint > 0) {
				$order = ' distance ASC';
			}
		}

		// 获取门店的优惠买单信息
		$psql = 'SELECT class_name, description,supplier_id FROM '.DB_PREFIX.'promote WHERE supplier_id <> 0';
		$temp_pro = $GLOBALS['db']->getAll($psql);
		$promote = array();
		$promote_ids = array();
		if ($temp_pro) {
			foreach ($temp_pro as $item) {
				global $is_app;
				if (APP_INDEX=='wap' && substr($item['class_name'],0,3) == 'App') {
					continue;
				}
				$promote[$item['supplier_id']][] = $item['description'];
				$promote_ids[] = $item['supplier_id'];
			}
			$promote_ids = array_unique($promote_ids);
		}
		$root['promote'] = $promote;

		// 只获取开启到店支付的门店
		if ($GLOBALS['request']['payment']) {
			if($ext_condition!=""){
				$ext_condition.=" and ";
			}
			$ext_condition .= ' sl.open_store_payment = 1';// and sl.supplier_id in ('.implode(',', $promote_ids).')';
			$page_title = '优惠买单';
		}		

		$condition_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
		require_once(APP_ROOT_PATH."system/model/supplier.php");
		$deal_result  = get_location_list($limit,$condition_param,"",$ext_condition,$order,$field_append);
		
		$list = $deal_result['list'];
		$location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location as sl where ".$deal_result['condition']);
		
		$count = count($location_list);
		$page_total = ceil($count/$page_size);

		// 商户门店ID数组 查询分类数量用
		$location_ids = array();
		foreach ($location_list as $val) {
			$location_ids[] = $val['id'];
		}

		$cateSql_param = array("aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
		$cate_result = get_location_list(9999,$cateSql_param,"",$ext_condition,$order,$field_append);
		$cate_ids = array();
		$cate_count = array();
		foreach ($cate_result['list'] as $k => $cate) {
			$cate_ids[] = $cate['id'];
			$cate_count[$cate['deal_cate_id']][] = $cate['id'];
		}

		$quanSql_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"city_id"=>intval($GLOBALS['city']['id']));
		$quan_result = get_location_list(9999,$quanSql_param,"",$ext_condition,$order,$field_append);
		$quan_ids = array();
		foreach ($quan_result['list'] as $k => $quan) {
			$quan_ids[] = $quan['id'];
		}

		// 各商圈的汇总数量查询和格式化
		$sql = 'SELECT name,location_id,id,pid FROM '.DB_PREFIX.'supplier_location_area_link AS s1 INNER JOIN '.DB_PREFIX.'area AS a1 ON s1.area_id=a1.id WHERE city_id='.$city_id.' AND location_id IN ('.implode(',', $quan_ids).')';
		$location_info_temp = $GLOBALS['db']->getAll($sql);
		$location_id_name = array(); // 商圈id=>名称数组
		$location_count = array(); // 大商圈统计
		$location_item_count = array(); // 小商圈统计
		foreach ($location_info_temp as $item) {
			if ($item['pid'] == 0) {
				$location_count[$item['id']][] = $item['location_id'];
			} else {
				$location_count[$item['pid']][] = $item['location_id'];
				$location_item_count[$item['id']][] = $item['location_id'];
			}
			if (!empty($quan_id_l) && $item['id'] == $quan_id_l) {
				$location_id_name[$item['location_id']] = $item['name'];
				continue;
			}
			$location_id_name[$item['location_id']] = $item['name'];
			
		}
		$root['location_count'] = $location_count;
		$root['location_item_count'] = $location_item_count;
		$root['location_id_name'] = $location_id_name;
		$root['location_total'] = count($quan_ids);


		// 各分类的汇总数量查询和格式化
		$loc_sql = 'SELECT ll.*, tl.cate_id FROM '.DB_PREFIX.'deal_cate_type_location_link AS ll INNER JOIN '.DB_PREFIX.'deal_cate_type_link AS tl ON tl.deal_cate_type_id=ll.deal_cate_type_id WHERE location_id IN ('.implode(',', $cate_ids).')';
		$cate_count_temp = $GLOBALS['db']->getAll($loc_sql);
		$cate_item_count = array();
		foreach ($cate_count_temp as $cate) {
			$cate_item_count[$cate['cate_id']][$cate['deal_cate_type_id']][] = $cate['location_id'];
		}
		$root['cate_count'] = $cate_count;
		$root['cate_item_count'] = $cate_item_count;
		$root['cate_total'] = count($cate_ids);
		
		/*输出分类*/
		$bcate_list = getCateList();
		$cate_names = array();
		foreach ($bcate_list as $kc => $vc) {
		    if ($vc['id'] > 0) {
		        $cate_names[$vc['id']] = $vc['name'];
		    }
		    foreach($vc['bcate_type'] as $kk=>$vv)
		    {
		        if ($kk == 0) {
		            $cate_count = count(array_unique($root['cate_count'][$vc['id']]));
		    
		        } else {
		            $cate_count = count($root['cate_item_count'][$vv['cate_id']][$vv['id']]);
		        }
		    
		        $bcate_list[$kc]['bcate_type'][$kk]['count'] = $cate_count;
		    
		        // $bcate_list[$k]['bcate_type'][$kk]['count'] = $cate_count;
		    }
		}
		$bcate_list[0]['bcate_type'][0]['count'] = $root['cate_total'];
		$root['bcate_list'] = $bcate_list;

		$goodses = array(); // 格式化信息
		foreach($list as $k=>$v) {
		    $goodses[$k] = format_store_list_item($v);
		    $goodses[$k]['preview'] = get_abs_img_root(get_spec_image($v['preview'],92,82,1));
		    if ($v['open_store_payment'] == 1) {
		        if (!empty($root['promote'][$v['supplier_id']])) {
		            $goodses[$k]['promote_info'] = implode(' ', $root['promote'][$v['supplier_id']]);    
		        }
		        $goodses[$k]['promote_url'] = wap_url('index', 'store_pay', array('id' => $v['id']));
		    }
		
		    if (!empty($v['avg_point'])) {
		        $goodses[$k]['format_point'] = ($v['avg_point'] / 5) * 100;
		        $goodses[$k]['avg_point'] = sprintf('%.1f', round($v['avg_point'], 1));
		    }
		    $goodses[$k]['quan_name'] = $location_id_name[$v['id']];
		    $goodses[$k]['store_type'] = $cate_names[$v['deal_cate_id']];
		}
		
		$root['city_id']= $city_id;
		$root['area_id']= $area_id;
		$root['quan_id']= $quan_id;
		$root['cate_id']=$catalog_id;
	
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title'] = $page_title;
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		$root['item'] = $goodses?$goodses:array();
		$root['bcate_list'] = $bcate_list?$bcate_list:array();
		$root['quan_list'] = $quan_list?$quan_list:array();
		
		//排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
		$root['navs'] = array(
			array("name"=>"默认","code"=>"default"),
			array("name"=>"距离","code"=>"distance"),
			array("name"=>"好评","code"=>"avg_point"),
			array("name"=>"最新","code"=>"newest"),
		);
		
		return output($root);
	}
}
?>