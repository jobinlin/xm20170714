<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class tuanApiModule extends MainBaseApiModule
{
	
	/**
	 * 团购列表接口
	 * 输入：
	 * cate_id: int 团购大分类ID
	 * tid: int 团购小分类ID
	 * page:int 当前的页数
	 * keyword: string 关键词
	 * qid: int 商圈ID
	 * order_type: string 排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
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
	 * item:array:array 团购列表，结构如下
	 *  Array
        (
            [0] => Array
                (
                    [id] => 74 [int] 团购ID
                    [name] => 仅售75元！价值100元的镜片代金券1张，仅适用于镜片，可叠加使用。[string] 团购名称
                    [sub_name] => 镜片代金券 [string] 团购短名称
                    [brief] => 【36店通用】明视眼镜 [string] 团购简介
                    [buy_count] => 1 [int] 销量
                    [current_price] => 75 [float] 现价
                    [origin_price] => 100 [float] 原价
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9d05a1020_140x85.jpg [string] 团购图片 140x85
                    [end_time_format] => 2017-02-28 18:00:08 [string] 格式化的结束时间
                    [begin_time_format] => 2015-02-25 18:00:10 [string] 格式化的开始时间
                    [begin_time] => 1424829610 [int] 开始时间戳
                    [end_time] => 1488247208 [int] 结束时间戳
                    [auto_order] => 1 [int] 免预约 0:否 1:是
                    [is_lottery] => 1 [int] 是否抽奖 0:否 1:是
                    [distance]	=>	[float] 有地理定位时的离当前地的距离(米)
                    [xpoint] => [float] 团购所在经度
                    [ypoint] => [float] 团购所在纬度
                    [is_today] => [int] 是否为今日团购 0否 1是
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
			array("name"=>"销量","code"=>"buy_count"),
			array("name"=>"价格最低","code"=>"price_asc"),
			array("name"=>"价格最高","code"=>"price_desc"),
		);
	 * 
	 */
	public function index()
	{
		//缓存下来的地区配置
		$area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));
		
		if(!$GLOBALS['geo']['xpoint']){
		    $ypoint =  26.076553;  //ypoint
		    $xpoint =  119.282241;  //xpoint
		    $GLOBALS['geo']['xpoint'] = $xpoint;
		    $GLOBALS['geo']['ypoint'] = $ypoint;
		}
		
		$root = array();
		$catalog_id = intval($GLOBALS['request']['cate_id']);//商品分类ID
		$cata_type_id=intval($GLOBALS['request']['tid']);//商品二级分类
		$city_id = intval($GLOBALS['city']['id']);//城市分类ID			
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		$page=$page==0?1:$page;
		$quan_id = intval($GLOBALS['request']['qid']); //商圈id	
		$area_id = intval($area_data[$quan_id]['pid']); //大区id
		$order_type=strim($GLOBALS['request']['order_type']);


		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = $GLOBALS['geo']['ypoint'];  //ypoint 
		$xpoint = $m_longitude = $GLOBALS['geo']['xpoint'];  //xpoint
		

		/*输出分类*/
		$bcate_list = getCateList();
		
		/*输出商圈*/
		$quan_list=getQuanList($city_id);
		
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$ext_condition = " d.buy_type <> 1 and d.is_shop = 0 ";
		if($keyword)
		{
			$ext_condition.=" and d.name like '%".$keyword."%' ";
		}
		
		if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
		{		
			$pi = PI;  //圆周率
			$r = EARTH_R;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((d.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((d.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (d.xpoint * $pi) / 180 ) ) * $r) as distance ";
			
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
			{
				if($ext_condition!="")
				$ext_condition.=" and ";
				$ext_condition.= " d.ypoint > $ybottom and d.ypoint < $ytop and d.xpoint > $xleft and d.xpoint < $xright ";
				
				$limit = 300;
			}
			$order = " distance asc ";
		}
		else
			$order = "";

		/*排序  
		 智能排序和 离我最的 是一样的 都以距离来升序来排序，只有这两种情况有传经纬度过来，就没有把 这两种情况写在 下面的判断里，写在上面了。
		default 智能（默认），nearby  离我，avg_point 评价，newest 最新，buy_count 人气，price_asc 价低，price_desc 价高 */
		if($order_type=='avg_point')/*评价*/
			$order= " d.avg_point desc  ";
		elseif($order_type=='newest')/*最新*/
			$order= " d.create_time desc  ";
		elseif($order_type=='buy_count')/*销量*/
			$order= " d.buy_count desc  ";
		elseif($order_type=='price_asc')/*价格升*/
			$order= " d.current_price asc  ";
		elseif($order_type=='price_desc')/*价格降*/
			$order= " d.current_price desc  ";
			
			

		$condition_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
		require_once(APP_ROOT_PATH."system/model/deal.php");
		$deal_result  = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),$condition_param,"",$ext_condition,$order,$field_append);
		
		$list = $deal_result['list'];
		$count= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d where ".$deal_result['condition']);
		
		$page_total = ceil($count/$page_size);
		
		$root = array();

		$goodses = array();
		foreach($list as $k=>$v)
		{
			$goodses[$k] = format_deal_list_item($v);
		}
		
		$root['city_id']= $city_id;
		$root['area_id']= $area_id;
		$root['quan_id']= $quan_id;
		$root['cate_id']=$catalog_id;
	
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="团购列表";
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		$root['item'] = $goodses?$goodses:array();
		$root['bcate_list'] = $bcate_list?$bcate_list:array();
		$root['quan_list'] = $quan_list?$quan_list:array();
		
		//排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
		$root['navs'] = array(
			array("name"=>"默认","code"=>"default"),
			array("name"=>"好评","code"=>"avg_point"),
			array("name"=>"最新","code"=>"newest"),
			array("name"=>"销量","code"=>"buy_count"),
			array("name"=>"价格最低","code"=>"price_asc"),
			array("name"=>"价格最高","code"=>"price_desc"),
		);
		
		return output($root);
	}

	/* 团购列表接口
	* 输入：
	* cate_id: int 团购大分类ID
	* tid: int 团购小分类ID
	* page:int 当前的页数
	* keyword: string 关键词
	* qid: int 商圈ID
	* order_type: string 排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
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
	* item:array:array 团购列表，结构如下
	* Array (
      660 =>  [int] 这个标是根据门店id设置的
      Array (
        0 => 
        Array (
          [id] => 3842 [int] 团购ID
          [distance] => 0 [float] 有地理定位时的离当前地的距离(米)
          [ypoint] => 29.591387999999998 [float] 团购所在纬度
          [xpoint] => 120.831467 [float] 团购所在经度
          [name] => '【嵊州】回头客蒸饺 仅售14.9元！价值18元的单人餐，提供免费WiFi。' [string] 团购名称
          [sub_name] => '回头客蒸饺14.9元单人餐' [string] 团购短名称
          [brief] => '仅售14.9元！价值18元的单人餐，提供免费WiFi。' [string] 团购简介
          [buy_count] => '0' [int] 销量
          [current_price] => 14.9 [float] 现价
          [origin_price] => 18 [float] 原价
          [icon] => 'http://192.168.3.148/fwshop/public/attachment/201509/07/15/55ed39dc43f10_184x164.jpg' [string] 团购图片92x82
          [end_time_format] => '2016-03-07 23:20:00' [string] 格式化的结束时间
          [begin_time_format] => '2015-09-07 15:20:33' [string] 格式化的开始时间
          [begin_time] => '1441581633' [int] 开始时间戳
          [end_time] => '1457335200' [int] 结束时间戳
          [auto_order] => '1' [int] 免预约 0:否 1:是
          [is_lottery] => '0' [int] 是否抽奖 0:否 1:是
          [is_refund] => '1' [int] 是否可推狂 0:否 1:是
          [allow_promote] => 1 [int] 优惠开关  0:关 1:开
          [location_id] => 660 [int] 门店id
          [location_name] => '回头客蒸饺' [string] 门店名称
          [location_address] => '三江街道医院路171号' [string] 门店地址
          [location_avg_point] => 0 [string] 门店评论平均分
          [area_name] => '三江城' [string] 门店地区名称
          [promotes_desc] => '满1000减200' [string] 优惠描述
          [is_today] => [int] 是否为今日团购 0否 1是
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
	        array("name"=>"销量","code"=>"buy_count"),
	        array("name"=>"价格最低","code"=>"price_asc"),
	        array("name"=>"价格最高","code"=>"price_desc"),
	    );
	    *
	    */
    public function index_v2()
    {
        //缓存下来的地区配置
        $area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));

        $root = array();
        $catalog_id = intval($GLOBALS['request']['cate_id']);//商品分类ID
        $cata_type_id=intval($GLOBALS['request']['tid']);//商品二级分类
        $city_id = intval($GLOBALS['city']['id']);//城市分类ID
        $page = intval($GLOBALS['request']['page']); //分页
        $keyword = strim($GLOBALS['request']['keyword']);
        $page=$page==0?1:$page;
        $quan_id = intval($GLOBALS['request']['qid']); //商圈id
        $area_id = intval($area_data[$quan_id]['pid']); //大区id
        $order_type=strim($GLOBALS['request']['order_type'])?strim($GLOBALS['request']['order_type']):'avg_point';
        $ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
        $ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
        $xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
        $xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
        $ypoint =  $m_latitude = $GLOBALS['geo']['ypoint'];  //ypoint
        $xpoint = $m_longitude = $GLOBALS['geo']['xpoint'];  //xpoint


        /*输出分类*/
        $bcate_list = getCateList();

        /*输出商圈*/
        $quan_list=getQuanList($city_id);


        $page_size = 20;
        $limit = (($page-1)*$page_size).",".$page_size;

        $ext_condition = " d.buy_type <> 1 and d.is_shop = 0 and ((d.is_coupon = 1 AND (d.coupon_end_time >= ".NOW_TIME." or d.coupon_end_time=0)) or d.is_coupon=0) ";
		if($keyword)
        {
            $ext_condition.=" and (d.name like '%".$keyword."%' or d.sub_name like '%".$keyword."%') ";
        }

        if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
        {
            $pi = PI;  //圆周率
            $r = EARTH_R;  //地球平均半径(米)
            $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
            	
            if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
            {
                if($ext_condition!="")
                    $ext_condition.=" and ";
                $ext_condition.= " sl.ypoint > $ybottom and sl.ypoint < $ytop and sl.xpoint > $xleft and sl.xpoint < $xright ";
            }
            $order = " (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) asc ";
        }
        else
            $order = "";

        /*排序
         智能排序和 离我最的 是一样的 都以距离来升序来排序，只有这两种情况有传经纬度过来，就没有把 这两种情况写在 下面的判断里，写在上面了。
         default 智能（默认），nearby  离我，avg_point 评价，newest 最新，buy_count 人气，price_asc 价低，price_desc 价高 */
			if($order_type=='avg_point')/*评价*/
	            $order= " sl.dp_count>0 desc,sl.avg_point desc  ";
	        elseif($order_type=='distance' && $xpoint>0 )
	        	$order= " (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r)  ";
	        elseif($order_type=='newest')/*最新*/
	        	$order= " d.create_time desc  ";
	        elseif($order_type=='buy_count')/*销量*/
	        	$order= " d.buy_count desc  ";
	        elseif($order_type=='price_asc')/*价格升*/
	        	$order= " d.current_price asc  ";
	        elseif($order_type=='price_desc')/*价格降*/
	        	$order= " d.current_price desc  ";
        	
        $field_append .= ", l.location_id location_id ,sl.is_verify,sl.open_store_payment,sl.dp_count location_dp_count,sl.xpoint location_xpoint,sl.ypoint location_ypoint";

        $condition_param = array("cid"=>$catalog_id,"tid"=>$cata_type_id,"aid"=>$area_id,"qid"=>$quan_id,"city_id"=>intval($GLOBALS['city']['id']));
        require_once(APP_ROOT_PATH."system/model/deal.php");
        
        $deal_result  = get_location_deal_list($limit,array(DEAL_ONLINE),$condition_param,"",$ext_condition,$order,$field_append);

        $list = $deal_result['list'];
        
        
        $sql_count = "SELECT count(*) FROM (SELECT count(location_id) FROM ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."deal_location_link dl ON dl.deal_id = d.id WHERE dl.location_id <> '' AND ".$deal_result['condition']." GROUP BY dl.location_id) as t";

        $data_count= $GLOBALS['db']->getOne($sql_count);
        
        $page_total = ceil($data_count/$page_size);

        $city_str=implode(",",load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id)));
		$root = array();
		//一级分类
        $time = NOW_TIME;
        $condition = 'select d.cate_id , count(*) as count from '.DB_PREFIX."deal d   where";
        $where ='';
        if($city_id){
			$where .= ' d.city_id in ('.$city_str.') and ';
        }
        
        if($quan_id>0)
        {
            $area_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."area where id = ".$quan_id);
            $kw_unicodes[] = str_to_unicode_string($area_name);
            $kw_unicode = implode(" ",$kw_unicodes);
            //有筛选
            $quan_where =" (match(d.locate_match) against('".$kw_unicode."' IN BOOLEAN MODE)) and ";
        }
        
        $where .= ' d.is_effect = 1 and d.is_delete = 0 and d.is_shop=0 AND d.is_location=1 and (';
        $where .= " ((".$time.">= d.begin_time or d.begin_time = 0) and (".$time."< d.end_time or d.end_time = 0) and d.buy_status <> 2) ";
        $where .= " or (".$time." < d.begin_time and d.begin_time <> 0 and d.notice = 1) ";
        $where .=" )";
		$where .=" and ((d.is_coupon = 1 AND (d.coupon_end_time >= ".NOW_TIME." or d.coupon_end_time=0)) or d.is_coupon=0)";
		if($keyword)
        {
            $where .=" and (d.name like '%".$keyword."%' or d.sub_name like '%".$keyword."%') ";
        }
		$condition.=$quan_where.$where;
        $cate_count = $GLOBALS['db']->getAll($condition." group by d.cate_id");
		
		foreach($cate_count as $k=>$v){
			$arr=explode(',',$v['cate_id']);
			foreach($arr as $vv){
				$cate_count_key[$vv]+=$v['count'];
			}
			
		}
		$cate_count=array();
		foreach($cate_count_key as $k=>$v){
			$arr=array();
			$arr['cate_id']=$k;
			$arr['count']=$v;
			$cate_count[$k]=$arr;
		}
        //require_once(APP_ROOT_PATH."system/model/dc.php");
        //$cate_count = data_format_idkey($cate_count , $key='cate_id');

        $root['cate_count']=$cate_count;
        //二级分类
        $e_sql='select dc.cate_id,dl.deal_cate_type_id,count(*) as count from '.DB_PREFIX.'deal_cate_type_deal_link dl LEFT JOIN '.DB_PREFIX.'deal_cate_type_link dc ON dl.deal_cate_type_id = dc.deal_cate_type_id LEFT JOIN '.DB_PREFIX.'deal d on dl.deal_id=d.id where';
        
        
        $e_sql.=$quan_where.$where;
        $ey_count = $GLOBALS['db']->getAll($e_sql." and dc.cate_id=d.cate_id group by dl.deal_cate_type_id,dc.cate_id");

        foreach ($ey_count as $k=>$v){
        	$e_count[$v['cate_id']][$v['deal_cate_type_id']]=$v['count'];
        }
        
        
        //$e_count = data_format_idkey($e_count , $key='cate_id');
        $root['e_count']=$e_count;
        
        
        $t_sql='select dl.deal_cate_type_id,dt.name,count(*) as count from '.DB_PREFIX.'deal_cate_type_deal_link dl LEFT JOIN '.DB_PREFIX.'deal_cate_type dt ON dl.deal_cate_type_id = dt.id LEFT JOIN '.DB_PREFIX.'deal d on dl.deal_id=d.id where';
        
		$t_sql.=$quan_where.$where;
        $tj_count = $GLOBALS['db']->getAll($t_sql." AND dt.is_recommend = 1 group by dl.deal_cate_type_id");
        foreach ($tj_count as $k=>$v){
        	$value['id']=$v['deal_cate_type_id'];
        	$value['cate_id']=0;
        	$value['name']=$v['name'];
        	$value['count']=$v['count'];
        	$bcate_list['0']['bcate_type'][]=$value;
        }
        
        $root['tj_count']=$tj_count;
        
        //全部分类的个数计算
        $root['cate_counts']=$GLOBALS['db']->getOne('select count(*) as count from '.DB_PREFIX."deal d   where".$quan_where.$where);

        // 获取1个全站优惠
        $sql = " select id,description from ".DB_PREFIX."promote where type = '0'  order by id desc limit 0,1";
        $promotes = $GLOBALS['db']->getRow($sql);
        
        $goodses = array();
        if($quan_id){
        	$is_quan=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."area where pid >0 and id=".$quan_id);
        }
       
        foreach($list as $k=>$v)
        {
            $goodses[$k] = format_deal_list_item($v);
            
            if ($list[$k]['allow_promote'] == 1) {
                $goodses[$k]['promotes_desc'] = $promotes['description'];
            }
            if($is_quan){
            	$goodses[$k]['area_name']=$is_quan['name'];
            	$list[$k]['area_name']=$is_quan['name'];
            }
        }

        foreach ($goodses as $key=>$value){
            $value['location_avg_point'] = round($value['location_avg_point'],1);
            $value['bfb'] = ($value['location_avg_point']/5)*100;
			$latest_goods[$value['location_id']][] = $value;
        }
		foreach ($latest_goods as $key=>$value){
			$arr['location_id']=$key;
			$arr['location_name']=$value['0']['location_name'];
			$arr['location_dp_count']=$value['0']['location_dp_count'];
			$arr['bfb']=$value['0']['bfb'];
			$arr['area_name']=$value['0']['area_name'];
			$arr['distance']=$value['0']['distance'];
			$arr['avg_point']=$value['0']['location_avg_point'];
			$arr['is_verify']=$value['0']['is_verify'];
			$arr['open_store_payment']=$value['0']['open_store_payment'];
			$arr['deal']=$value;
			$latest_goods[$key]=$arr;
		}
        $root['city_id']= $city_id;
        $root['area_id']= $area_id;
        $root['quan_id']= $quan_id;
        $root['cate_id']=$catalog_id;

        //$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
        $root['page_title']="团购列表";

        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$data_count);
        
        $root['item'] = $latest_goods?array_values($latest_goods):array();
		foreach($bcate_list as $k=>$v)
		{		
			$tmp_url_param = $GLOBALS['request'];
			$tmp_url_param['cate_id']=$v['id'];			
			
			$bcate_list[$k]["url"] = wap_url("index","tuan",$tmp_url_param);
			if($bcate_list[$k]['id']){
				$bcate_list[$k]["bcate_type"]['0']['cate_id']=$bcate_list[$k]['id'];
			}
			foreach($v['bcate_type'] as $kk=>$vv)
			{				
				$tmp_url_param = $GLOBALS['request'];
				$tmp_url_param['cate_id']=$v['id'];
				$tmp_url_param['tid']=$vv["id"];

				$bcate_list[$k]["bcate_type"][$kk]["url"]= wap_url("index","tuan",$tmp_url_param);
				if($v['id']=="0"&&$vv["id"]=="0"&&$vv["cate_id"]=="0")
					$bcate_list[$k]["bcate_type"][$kk]["count"]= intval($root['cate_counts']);
				elseif($v['id']=="0"&&$vv["id"]!="0"&&$vv["cate_id"]=="0")
					$bcate_list[$k]["bcate_type"][$kk]["cate_id"]=0;
				elseif($v['id']!="0"&&$vv["id"]=="0"&&$vv["cate_id"]=="0")
					$bcate_list[$k]["bcate_type"][$kk]["count"]= intval($root['cate_count'][$v['id']]['count']);
				else{
					//echo $v['id'].":".$vv["id"]."||".intval($data['e_count'][$v['id']]['count'])."<br>";
					$bcate_list[$k]["bcate_type"][$kk]["count"]= intval($root['e_count'][$v['id']][$vv['id']]['count']);
				}
				if($bcate_list[$k]["bcate_type"][$kk]["count"]==0){
					if($bcate_list[$k]["bcate_type"][$kk]['id']==0&&$bcate_list[$k]["bcate_type"][$kk]['cate_id']==0){
					}else{
						unset($bcate_list[$k]["bcate_type"][$kk]);
					}
				}
				if($GLOBALS['request']['cate_id']==$v['id']&&$GLOBALS['request']['tid']==$vv["id"]){
					if($GLOBALS['request']['tid']=="0"){
						$root['catename'] = $v['name'];
					}else{
						$root['catename'] = $vv['name'];
					}
					$quan_list[0]['quan_sub']['0']['count']=$bcate_list[$k]["bcate_type"][$kk]["count"];
				}
			}
			if(count($bcate_list[$k]["bcate_type"])==0){
				if($bcate_list[$k]["id"]==0){
				}else{
					unset($bcate_list[$k]);
				}
				//$bcate_list[$k]["bcate_type"]=array_values($bcate_list[$k]["bcate_type"]);
			}else{
				$bcate_list[$k]["bcate_type"]=array_values($bcate_list[$k]["bcate_type"]);
			}
			sort($bcate_list[$k]['bcate_type']);
		}
		sort($bcate_list);
		foreach($bcate_list as $k=>$v)
		{
			if($v['id']==$GLOBALS['request']['cate_id']){//默认分类标识
				$root['default_cate_id']=$k;
				//$GLOBALS['tmpl']->assign("default_cate_id",$k);
			}
		}
		$root['bcate_list'] = $bcate_list?array_values($bcate_list):array();
		//查询商圈存在的商品数量
        $q_sql='select a.id, count(distinct d.id) as count from '
                .DB_PREFIX.'area a LEFT JOIN '
                .DB_PREFIX.'supplier_location_area_link sla on a.id = sla.area_id LEFT JOIN '
                .DB_PREFIX.'deal_location_link dll on sla.location_id=dll.location_id LEFT JOIN '
                .DB_PREFIX.'deal d on dll.deal_id=d.id where';

        if($catalog_id>0||$cata_type_id>0)
        {
            $cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$catalog_id);
            $cate_name_unicode = str_to_unicode_string($cate_name);

            if($cata_type_id>0)
            {
                $deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$cata_type_id);
                $deal_type_name_unicode = str_to_unicode_string($deal_type_name);
                $cata_where = " (match(d.deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) and";

            }
            else
            {
                $cata_where = " (match(d.deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) and";
            }
        }

        $q_sql.=$cata_where.$where;
        $q_count = $GLOBALS['db']->getAll($q_sql." and a.city_id in (".$city_str.") group by a.id");
		foreach($q_count as $k=>$v){
			$q_count_new[$v['id']]=$v;
		}
		
		foreach($quan_list as $k=>$v){
			if($v['id']!=0){
				foreach($v['quan_sub'] as $kk=>$vv){
					if(intval($q_count_new[$vv['id']]['count'])>0){
						$quan_list[$k]['quan_sub'][$kk]['count']=$q_count_new[$vv['id']]['count'];
					}else{
						if($kk!=0){
							unset($quan_list[$k]['quan_sub'][$kk]);
						}
						
					}
				}
				if(count($quan_list[$k]['quan_sub'])==1&&$quan_list[$k]['quan_sub'][0]['count']==0){
					unset($quan_list[$k]);
				}else{
					sort($quan_list[$k]['quan_sub']);
				}
			}
			
		}
		sort($quan_list);
		//$quan_list[0]['quan_sub']['0']['count']=$GLOBALS['db']->getOne('select count(*) as count from '.DB_PREFIX."deal d   where".$cata_where.$where);
        //全部分类的个数计算
        $root['quan_list'] = $quan_list?$quan_list:array();
		$root['quanname'] = $area_data[$GLOBALS['request']['qid']]['name']?$area_data[$GLOBALS['request']['qid']]['name']:"全城·热门";	
        //排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
        $root['navs'] = array(
            array("name"=>"默认","code"=>"default"),
            array("name"=>"好评","code"=>"avg_point"),
            array("name"=>"最新","code"=>"newest"),
            array("name"=>"销量","code"=>"buy_count"),
            array("name"=>"价格最低","code"=>"price_asc"),
            array("name"=>"价格最高","code"=>"price_desc"),
        );

        return output($root);
    }
	
}
?>