<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dctableApiModule extends MainBaseApiModule
{
	
	/**
	 * 商家详细页中的订座页面
	 *
	 * 输入：
	 * lid:int 商家ID
	 * tid:座位ID,当第一次进入该页面时，不需要传该参数，接口会默认分配第一个tid;
	 *
	 * 输出：
	 * is_has_location:int是否存在些商家， 0为不存在，1为存在
	 * city_name：string当前城市名称
	 * page_title:string 页面标题
	 * page_keyword:string 页面关键词
	 * page_description:string 页面描述
	 * location_dc_table_cart为订座购物车信息
	 * table_now:array,当前选中座位的信息 
	 * Array
        (
            [id] => 16
            [name] => 2人桌
            [price] => 50.0000
        )
	 * cart_list:购物车中订座或者菜品的购物信息，total_data为购物车的统计数据，total_price为购物总金额，total_count：为购物总数量

	 *  Array
        (
            [cart_list] => Array
                (
                    [0] => Array
                        (
                            [id] => 198
                            [session_id] => db0gdd485luijc8391rr4l5416
                            [user_id] => 0
                            [location_id] => 41
                            [supplier_id] => 43
                            [name] => 散桌8-10人桌
                            [icon] => 
                            [num] => 1
                            [unit_price] => 150.0000
                            [total_price] => 150.0000
                            [menu_id] => 7
                            [table_time_id] => 12
                            [table_time] => 1438651800
                            [cart_type] => 0
                            [add_time] => 1438639174
                            [is_effect] => 1
                            [url] => /o2onew/index.php?ctl=dctable&lid=41
                            [table_time_format] => <span class="time_span">2015-08-04</span><span class="time_span">星期二</span><span class="time_span">17:30</span>
                        )
                 )
           [total_data] => Array
                (
                    [total_price] => 117.0000
                    [total_count] => 6
                )
          )          
	 * 
	 * $dclocation:array:array 商家信息 ,结构如下
	 * is_collected为是否已经收藏
	 * Array
        (
            [id] => 41
            [name] => 果果外卖
            [is_dc] => 1
            [is_reserve] => 1
            [is_collected] => 1
        )   
        
          

     * time_info下面的date_info为日期时间，table_info为该天的可预订的座位时间段
            Array
                (
                    [0] => Array
                        (
                            [date_info] => 2015-08-05
                            [table_info] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 12
                                            [total_count] => 2
                                            [rs_time] => 17:30
                                            [item_id] => 7
                                        )
                                 )
                          )
                  )
                                        
  
          
	 **/
	public function index()
	{	
		global_run();
		
		require_once(APP_ROOT_PATH."system/model/dc.php");

		$user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;

		$tname='l';
				
		$location_id = intval($GLOBALS['request']['lid']);
		$table_id = intval($GLOBALS['request']['tid']);
		
		$dclocation=$GLOBALS['db']->getRow("select id,name,is_dc,is_reserve,is_close from ".DB_PREFIX."supplier_location where id=".$location_id);

		$root=array();
		if($dclocation)
		{	
			$is_colloect=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_location_sc where location_id=".$dclocation['id']." and user_id=".$user_id);
			if($is_colloect>0){
				$dclocation['is_collected']=1;
			}else{
				$dclocation['is_collected']=0;
			}

			//关于分类信息与seo
			$page_title = $dclocation['name'];
			$page_keyword = $dclocation['name'];
			$page_description = $dclocation['name'];
		
			
			$table_info=$GLOBALS['db']->getAll("select id,name,price from ".DB_PREFIX."dc_rs_item where is_effect=1 and location_id=".$location_id." order by sort desc");
			if($table_id==0){
				$table_id=$table_info[0]['id'];
			}
			$table_data=$this->mapi_get_rs_item($table_id);
			
			$table_info_t=data_format_idkey($table_info,$key='id');
			$table_now=$table_info_t[$table_id];
			
			
			$user_info=$GLOBALS['db']->getRow("select consignee,mobile from ".DB_PREFIX."dc_consignee where user_id=".$user_id." and is_main=1");
			$root['user_info']=$user_info?$user_info:array();

			$root['is_has_location']=1;
			$root['page_title']=$page_title;
			$root['page_keyword']=$page_keyword;
			$root['page_description']=$page_description;
			$root['table_info']=$table_info;
			$root['table_now']=$table_now;
			$root['time_info']=$table_data;
			$root['dclocation']=$dclocation;
			
			return output($root);
		}
		else
		{	
			$root['is_has_location']=0;
			return output($root);
		}
		
		
	}
	
	/**
	 * @param $location_id门店的id
	 * @param $table_id 预订座位的信息
	 * @return array('list'=>$rs_list); 1.计算出这天有多少桌子可以预定 或者今天 余下的时间还有多少桌子可以预定
	 */
	
	public function mapi_get_rs_item($table_id){
		 
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$sql = "select * from ".DB_PREFIX."dc_rs_item where is_effect = 1 and id = ".$table_id." order by sort desc";
		$rs_list = $GLOBALS['db']->getRow($sql,false);
		//$rs_list=data_format_idkey($rs_list,$key='id');
	
		$rs_time_list = $GLOBALS['db']->getAll("select id,total_count,rs_time,item_id from ".DB_PREFIX."dc_rs_item_time where is_effect = 1 and total_count > 0 and item_id=".$table_id);	//营业时间 和现在时间 分割对比
		$rs_time_list=data_format_idkey($rs_time_list,$key='id');
	
		$arr_d=array();
		foreach($rs_time_list as $kk=>$vv){
			if(!in_array($vv['id'], $arr_d)){
				$arr_d[]=$vv['id'];
			}
			
			$rs_time_list[$kk]['rs_time']=to_date(to_timespan($vv['rs_time']),"H:i");
		}
	
		$table_data=array();
		$table_data['table_info']=$rs_list;
		 
		//获得7天的内的座位库存信息
		$begin_time=to_date(NOW_TIME,"Y-m-d");
		$end_time= to_date(to_timespan($begin_time)+3600*24*7,"Y-m-d");
	
		for($i=to_timespan($begin_time);$i<=to_timespan($end_time);$i+=3600*24){
			$table_data['time_info'][to_date($i,"Y-m-d")]['date_info']=to_date($i,"Y-m-d");
			$table_data['time_info'][to_date($i,"Y-m-d")]['table_info']=$rs_time_list;
		}
	
		//库存
		 $table_stock=$GLOBALS['db']->getAll("select id,buy_count,time_id,rs_time,rs_date from ".DB_PREFIX."dc_rs_item_day where time_id in (".implode(',',$arr_d).") and rs_date between '".$begin_time."' and '".$end_time."'");
		//return $table_stock=data_format_idkey($table_stock,$key='time_id');
		foreach($table_stock as $xl=>$zl){
			if($table_data['time_info'][$zl['rs_date']]['table_info'][$zl['time_id']]['total_count']-$zl['buy_count']<=0){
				unset($table_data['time_info'][$zl['rs_date']]['table_info'][$zl['time_id']]);
			}
		}
		 
		$now_time=NOW_TIME+3600;//延后1个小时
	
		foreach($table_data['time_info'][$begin_time]['table_info'] as $kk=>$vv){
			if(to_timespan($vv['rs_time'])<$now_time){
				unset($table_data['time_info'][$begin_time]['table_info'][$kk]);
			}
		}
		
		//去掉没有座位的信息
		foreach($table_data['time_info'] as $aa=>$bb){
			if(count($table_data['time_info'][$aa]['table_info'])==0){
				unset($table_data['time_info'][$aa]);	
			}	
		}
		ksort($table_data['time_info']);
		
		$table_data['time_info']=array_values($table_data['time_info']);
		
		foreach($table_data['time_info'] as $ka=>$kb){	
			$table_data['time_info'][$ka]['table_info']=data_format_idkey($table_data['time_info'][$ka]['table_info'],$key='rs_time');
		}
		
		foreach($table_data['time_info'] as $ka=>$kb){
			ksort($table_data['time_info'][$ka]['table_info']);
		}
		
		
		foreach($table_data['time_info'] as $ka=>$kb){
			$table_data['time_info'][$ka]['table_info']=array_values($kb['table_info']);
		}

		return $table_data['time_info'];
	}


	public function lists()
	{
		require_once(APP_ROOT_PATH."system/model/dc.php");
        $root = array();
        
        $request = $GLOBALS['request'];

        if(	$request['from']=='wap'){
            $ypoint =  $GLOBALS['geo']['ypoint'];  //ypoint
            $xpoint =  $GLOBALS['geo']['xpoint'];  //xpoint
        
        }else{
            $ypoint = $request['ypoint'];  //ypoint
            $xpoint = $request['xpoint'];  //xpoint
        }
        
        $tname='sl';
        $ext_condition = '';

        // 搜索关键字处理
        if($GLOBALS['kw']) {
            $ext_condition.=" and ".$tname.".name like '%".$GLOBALS['kw']."%' ";
        }
        $sort_str = 'avg_point desc '; // 排序方式 默认按评分？
        // 城市id
        $city_id = intval($GLOBALS['city']['id']);
        
        // 距离计算
        if($xpoint>0) {/* 排序（$order_type）  default 智能（默认）*/
            $pi = PI;  //圆周率
            $r = EARTH_R;  //地球平均半径(米)
            $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
            $sort_field = "distance asc ";
        }
        
        $sort = intval($request['sort']);
        if($sort == 1){
            $sort_field = "avg_point desc ";
        }

        $param = array(); // 城市、商圈、分类的数组
        // 分类id
        if (isset($request['cid'])) {
        	$param['cid'] = intval($request['cid']);
        }
        if (isset($request['aid'])) {
        	$param['aid'] = intval($request['aid']);
        }
        // 商圈id
        if (isset($request['qid'])) {
        	$param['qid'] = intval($request['qid']);
        }

        // 分页处理
        $page = intval($request['page']);
        $page=$page==0?1:$page; 
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
        

        //seo元素
        $page_title = "订座";
        $page_keyword = "订座";
        $page_description = "订座";

        // 门店列表
        $dc_location_list_result = get_dc_location_list('is_res', $limit, $param, '', $ext_condition, $sort_field, $field_append, '', '');


        // 分别构建商圈和分类的所有门店id数组
        $cate_param = $param;
        unset($cate_param['cid']);
        $dc_cate_location_list_result = get_dc_location_list('is_res', '', $cate_param, '', $ext_condition);
        $all_cate_location_ids = array_keys($dc_cate_location_list_result['id_arr']);

        $quan_param = $param;
        unset($quan_param['qid']);
        unset($quan_param['aid']);
        $dc_quan_location_list_result = get_dc_location_list('is_res', '', $quan_param, '', $ext_condition);
        $all_quan_location_ids = array_keys($dc_quan_location_list_result['id_arr']);
        
        $all_location_list = $GLOBALS['db']->getAll($dc_location_list_result['condition']);
        $all_location_ids = array(); // 所有门店id
        foreach ($all_location_list as $item) {
        	$all_location_ids[] = $item['id'];
        }
        $total = count($all_location_list);
        
        require_once(APP_ROOT_PATH."system/model/supplier.php");
        // 门店商圈名称
	    $locationIdAndArea = get_location_area_name($all_location_ids);
	    // 门店分类名称
	    $locationIdAndCate = get_location_dc_cate_name($all_location_ids);

        $dc_location_list=array();
        foreach ($dc_location_list_result['list'] as $t => $v){
            $v['preview']=get_abs_img_root(get_spec_image($v['preview'],90,90,1));

            $v['format_distance']= $v['distance']>1 ? round($v['distance'],2)."km" : round($v['distance']*1000)."m";
            $v['format_point'] = round($v['avg_point'], 1);
	    	$v['point_percent'] = $v['avg_point'] / 5 * 100;
            $v['area_name'] = $locationIdAndArea[$v['id']];
            $v['cate_name'] = $locationIdAndCate[$v['id']];
            $dc_location_list[] = $v;
        }

        $areaLinkSql = 'SELECT area_id, count(location_id) as count FROM '.DB_PREFIX.'supplier_location_area_link WHERE location_id in('.implode(',', $all_quan_location_ids).') GROUP BY area_id';
        $areaLink = $GLOBALS['db']->getAll($areaLinkSql);
        $areaLinkCount = array();
        foreach ($areaLink as $area) {
        	$areaLinkCount[$area['area_id']] = $area['count'];
        }
        //商圈缓存
        $area_cache = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));
        $area_list = array(); // 格式化后的商圈数组
        $quan2areaIds = array(); // 商圈 => 
        $area_total = 0;
        foreach ($area_cache as $key => $area) {
        	$pid = $area['pid'] == 0 ? $key : $area['pid'];
        	$area['count'] = $areaLinkCount[$key] ?: 0;
        	$area_list[$pid]['list'][$key] = $area;
        	if ($area['pid'] == 0) {
        		$area_total += $area['count'];
        		$area_list[$pid]['name'] = $area['name'];
        		if ($area['id'] == $param['qid']) {
	        		$root['rid'] = $area['pid'] ?: $area['id'];
	        	}
        	}
        	
        }
        $all_area_array = array('list' => array(0 => array('id'=> 0, 'name' => '全城', 'pid' => 0, 'count' => $area_total)), 'name' => '全城');
        array_unshift($area_list, $all_area_array);
        // print_r($area_list);exit;
        $root['area_total'] = $area_total;
        $root['area_list'] = $area_list;


        //分类缓存
        $cate_cache = load_auto_cache("cache_dc_cate"); 
        $cateLinkSql = 'SELECT dc_cate_id, count(location_id) as count FROM '.DB_PREFIX.'dc_cate_supplier_location_link WHERE location_id in('.implode(',', $all_cate_location_ids).') GROUP BY dc_cate_id';
        $cateLink = $GLOBALS['db']->getAll($cateLinkSql);
        $cateLinkCount = array();
        foreach ($cateLink as $cate) {
        	$cateLinkCount[$cate['dc_cate_id']] = $cate['count'];
        }
        $cate_list = array();
        $cate_total = 0;
        foreach ($cate_cache as $key => $cate) {
        	$cate['count'] = $cateLinkCount[$cate['id']] ?: 0;
        	$cate_total += $cate['count'];
        	$cate_list[$key] = $cate;
        }
        $root['cate_total'] = $cate_total;
        $root['cate_list'] = $cate_list;
        $root['current_cate_name'] = $cate_list[$param['cid']]['name'] ?: '全部分类';

        // 分类的数量统计重写
        $cateCountSql = 'SELECT dc.`id`, dc.`name`, count(dc.`id`) as idcount FROM'.DB_PREFIX.'dc_cate dc LEFT JOIN '.DB_PREFIX.'dc_cate_supplier_location_link dl ON dc.id=dl.dc_cate_id AND dc.is_effect=1 WHERE dl.location_id>0';
        $cateCountList = $GLOBALS['db']->getAll($cateCountSql);


        $page_total = ceil($total/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
        $root['dc_location_list'] = $dc_location_list;
        
        return output($root);
	}
	
	

	public function detail()
	{
		do {
			$root = array();
			$lid = intval($GLOBALS['request']['lid']);

			// 商户信息
			$location_sql = 'SELECT * FROM '.DB_PREFIX.'supplier_location WHERE id='.$lid.' AND is_effect=1';
			$location = $GLOBALS['db']->getRow($location_sql);
			$root['location_info'] = $location;
			if (empty($location) || $location['is_reserve'] == 0) {
				break;
			}
			$location['preview'] = get_abs_img_root(get_spec_image($location['preview'],90,90,1));

			$isCollected = 0;
			if (!empty($GLOBALS['user_info']['id'])) {
				$user_id = $GLOBALS['user_info']['id'];
				$isCollectedSql = 'SELECT count(id) FROM '.DB_PREFIX.'dc_location_sc WHERE location_id='.$lid.' AND user_id='.$user_id;
				$isCollected = $GLOBALS['db']->getOne($isCollectedSql);
			}
			$root['isCollected'] = $isCollected ? 1 : 0;
			
			$rsItemSql2 = 'SELECT ri.id,ri.name,ri.location_id,ri.price,it.is_effect,it.total_count,it.rs_time FROM '.DB_PREFIX.'dc_rs_item ri LEFT JOIN '.DB_PREFIX.'dc_rs_item_time it ON ri.id=it.item_id WHERE ri.location_id='.$lid.' AND ri.is_effect=1';
			$rsItemList2 = $GLOBALS['db']->getAll($rsItemSql2);
			if ($rsItemList2) { // 如果商家设置了订座
				$hasRsItem = 1;
				// 7天内的格式日期
				$formatDateAndWeekday = $this->formatDateAndWeekday();
				$keyRsDate = array_keys($formatDateAndWeekday);
				
				foreach ($keyRsDate as &$val) {
					$val = '"'.$val.'"';
				}unset($val);
				// 获取7天内预订的数量
				$buyCountSql = 'SELECT rs_date,item_id,sum(buy_count) as count FROM '.DB_PREFIX.'dc_rs_item_day WHERE location_id='.$lid.' AND rs_date in('.implode(',', $keyRsDate).') GROUP BY rs_date,item_id';
				$buyCount = $GLOBALS['db']->getAll($buyCountSql);
				
				$formatBuy = array();
				foreach ($buyCount as $buy) {
					$formatBuy[$buy['rs_date']][$buy['item_id']] = $buy['count'];
				}

				foreach ($formatDateAndWeekday as $dateKey => &$dateItem) {
					$newItemList = array();
					foreach ($rsItemList2 as $rsKey => $rsItem) {
						if ($rsItem['is_effect'] == 0) {
							continue;
						}
						$itemDateTime = $dateKey.' '.$rsItem['rs_time'];
						$itemTimestamp = to_timespan($itemDateTime);
						/*echo $itemDateTime;echo "\n";
						echo $itemTimestamp + 3600 - NOW_TIME;
						echo "\n";*/
						if (($itemTimestamp - 3600) < NOW_TIME) {
							continue;
						}
						$count =0;
						if (!empty($formatBuy[$dateKey][$rsItem['id']])) {
							$count = $formatBuy[$dateKey][$rsItem['id']];
						}
						$newItemList[$rsItem['id']]['id'] = $rsItem['id'];
						$newItemList[$rsItem['id']]['name'] = $rsItem['name'];
						$newItemList[$rsItem['id']]['location_id'] = $rsItem['location_id'];
						$newItemList[$rsItem['id']]['format_price'] = format_price($rsItem['price']);
						$newItemList[$rsItem['id']]['total'] += $rsItem['total_count'];
						$newItemList[$rsItem['id']]['buyCount'] += $count;
					}
					if (empty($newItemList)) {
						unset($formatDateAndWeekday[$dateKey]);
						continue;
					}
					$dateItem['rsItem'] = $newItemList;
				}unset($dateItem);
				$root['weekday'] = $formatDateAndWeekday;
			}
			$root['hasRsItem'] = $hasRsItem;

			// 商户评价信息
			require_once(APP_ROOT_PATH."system/model/review.php");
			$dp_list = get_dc_location_dp_list($lid, 2);
			$root['dp_list'] = $dp_list;
		} while (0);
		
		// print_r($root);exit;
		return output($root);
	}

	private function formatDateAndWeekday($start = 0)
	{
		$start = intval($start);
		if ($start == 0) {
			$start = NOW_TIME;
		}
		
		$origDateStr = date('Y-m-d', $start);
		$weekdayIndex = date('w', $start);
		$objDate = date_create($origDateStr);
		$returnDate = array();
		$returnDate[$origDateStr] = array('weekday' => '今天', 'shortDate' => date_format($objDate, 'm-d'));
		for ($i=0; $i < 6; $i++) { 
			date_add($objDate, date_interval_create_from_date_string('1 day'));
			$currentDateStr = date_format($objDate, 'Y-m-d');
			$currentWeekday = $this->getWeekday(date_format($objDate, 'w'));
			$currentShortDate = date_format($objDate, 'm-d');
			$returnDate[$currentDateStr] = array('weekday' => $currentWeekday, 'shortDate' => $currentShortDate);
		}
		return $returnDate;
	}

	private function getWeekday($index)
	{
		$weekdayStr = array(
			'周天', '周一', '周二', '周三', '周四', '周五', '周六'
		);
		$index = intval($index) % 7;
		return $weekdayStr[$index];
	}
	
}
?>