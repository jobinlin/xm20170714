<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class locationApiModule extends MainBaseApiModule
{	
	/**
	 *
	 */	
	
	
	public function index()
	{
		if(!$GLOBALS['geo']['xpoint']){
			$ypoint =  26.076553;  //ypoint
			$xpoint =  119.282241;  //xpoint
			$GLOBALS['geo']['xpoint'] = $xpoint;
			$GLOBALS['geo']['ypoint'] = $ypoint;
		}
		
		$root=array();
		$data_id = intval($GLOBALS['request']['data_id']);//
		$type=$GLOBALS['request']['type'];
		
		$page = intval($GLOBALS['request']['page']); //分页
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		require_once(APP_ROOT_PATH."system/model/cart.php");
		require_once(APP_ROOT_PATH."system/model/supplier.php");
		if(!$type){
    		require_once(APP_ROOT_PATH."system/model/deal.php");
    		$data = get_deal($data_id);
		}else {
    		require_once(APP_ROOT_PATH."system/model/event.php");
    		$event_info = get_event($data_id);
		}
		
		if($data['id']>0)
		{
			$join = '';
			$field_append = '';
			//开始身边团购的地理定位
			$geo=$GLOBALS['geo'];
			$ypoint =  $geo['ypoint'];  //ypoint
			$xpoint =  $geo['xpoint'];  //xpoint
		
			$address = $geo['address'];
		
			if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
			{
				$pi = PI;  //圆周率
				$r = EARTH_R;  //地球平均半径(米)
				$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
			}
		
			$join .= " left join ".DB_PREFIX."deal_location_link as l on sl.id = l.location_id ";
			$where = " l.deal_id = ".$data['id']." ";
			$locations = get_location_list($data['supplier_location_count'],array("supplier_id"=>$data['supplier_id']),$join,$where, '', $field_append);
		}else if($event_info['id']>0){
		    $join = '';
		    $field_append = '';
		    //开始身边团购的地理定位
		    $geo=$GLOBALS['geo'];
		    $ypoint =  $geo['ypoint'];  //ypoint
		    $xpoint =  $geo['xpoint'];  //xpoint
		    
		    $address = $geo['address'];
		    
		    if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
		    {
		        $pi = PI;  //圆周率
		        $r = EARTH_R;  //地球平均半径(米)
		        $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
		    }
		    $join .= " left join ".DB_PREFIX."event_location_link as l on sl.id = l.location_id ";
            $where = " l.event_id = ".$event_info['id']." ";
            $locations = get_location_list($event_info['supplier_location_count'],array("supplier_id"=>$event_info['supplier_id']),$join,$where, '', $field_append);
		}
		else{
			$locations = get_location_list($data['supplier_location_count'],array("supplier_id"=>$data['supplier_id']));
		}
		if($locations){
			foreach ($locations['list'] as $k=>$v){
				$temp_location = array();
				$temp_location['id'] = $v['id'];
				$temp_location['name'] = $v['name'];
				$temp_location['address'] = $v['address'];
				$temp_location['tel'] = $v['tel'];
				$temp_location['xpoint'] = $v['xpoint'];
				$temp_location['ypoint'] = $v['ypoint'];
				$temp_location['distance'] = $v['distance'];

		
				$supplier_location_list[] = $temp_location;
			}
		}
		$root['supplier_location_list'] = $supplier_location_list;
		//echo "<pre>";
		//print_r($data);exit;
		$root['supplier_info']=$data['supplier_info'];
		$root['page_title'] = "门店列表";
		return output($root);
	}


	/**
	 * 重写获取门店的方法
	 * 参数格式
	 * [type: youhui|event|deal // 默认deal
	 * data_id: id]
	 * @return array 
	 */
	public function wap_index()
	{
		$root=array();
		$data_id = intval($GLOBALS['request']['data_id']);//
		$type = strtolower($GLOBALS['request']['type']);
		$type = in_array($type, array('youhui', 'event', 'deal')) ? $type : 'deal';
		
		$page = intval($GLOBALS['request']['page']); //分页
		$page=$page?$page:1;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;

		$type_table = DB_PREFIX.$type.'_location_link';
		$field_id = $type.'_id';

		$field_append = '';
		if($GLOBALS['geo']['xpoint']){
			$geo = $GLOBALS['geo'];  //开始身边团购的地理定位
			$ypoint =  $geo['ypoint'];  //ypoint
			$xpoint =  $geo['xpoint'];  //xpoint
			$pi = PI;  //圆周率
			$r = EARTH_R;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
		}
		
		if($type=="deal")
		    $deal_info=$GLOBALS['db']->getRow("select is_pick,supplier_id from ".DB_PREFIX."deal where id = ".$data_id);
		
		if($deal_info['is_pick']){
		    $sql="select sl.id, sl.name, sl.address, sl.tel, sl.xpoint, sl.ypoint".$field_append." from ".DB_PREFIX."supplier_location sl where supplier_id=".$deal_info['supplier_id']." limit ".$limit;
		    $sql_count="select count(*) from ".DB_PREFIX."supplier_location sl where supplier_id=".$deal_info['supplier_id'];
		}
		else{
		    $sql = 'SELECT sl.id, sl.name, sl.address, sl.tel, sl.xpoint, sl.ypoint'.$field_append.' FROM '.$type_table.' tl INNER JOIN '.DB_PREFIX.'supplier_location sl ON tl.location_id = sl.id WHERE '.$field_id.'='.$data_id." limit ".$limit;
		    $sql_count = 'SELECT count(*) FROM '.$type_table.' tl INNER JOIN '.DB_PREFIX.'supplier_location sl ON tl.location_id = sl.id WHERE '.$field_id.'='.$data_id;
		}
		
		$locations = $GLOBALS['db']->getAll($sql);
		
		$count=$GLOBALS['db']->getOne($sql_count);
		
		$page_total = ceil($count/$page_size);
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);

		foreach ($locations as $k => $v) {
			if (array_key_exists('distance', $v)) {
                $locations[$k]['distance_format'] = format_distance_str($v['distance']);
            }
		}

		$root['supplier_location_list'] = $locations;
		$root['page_title'] = "门店列表";

		return output($root);
	}	
}
?>