<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

require_once(APP_ROOT_PATH."app/Lib/main/core/dc_init.php");
/**
 * 外卖订餐
 * 
 *
 */
class dcApiModule extends MainBaseApiModule
{

	public function index() {
	    global_run();
	    dc_global_run();
	    //init_app_page();
	    require_once(APP_ROOT_PATH."system/model/dc.php");
	    /* 获取最新搜索名 */
	    
	    $root = array();
	    $user_id=isset($GLOBALS['user_info']['id'])?intval($GLOBALS['user_info']['id']):0;
	    //开始身边团购的地理定位
	    
	    if(	$GLOBALS['request']['from']=='wap'){
	        $ypoint =  $GLOBALS['geo']['ypoint'];  //ypoint
	        $xpoint =  $GLOBALS['geo']['xpoint'];  //xpoint
	    
	    }else{
	        $ypoint = $GLOBALS['request']['ypoint'];  //ypoint
	        $xpoint = $GLOBALS['request']['xpoint'];  //xpoint
	    }
	    
	    
	    $tname='sl';
	    /* if($GLOBALS['kw'])
	    {
	        $ext_condition.=" and  ".$tname.".is_delete=0 and ".$tname.".is_effect=1 and is_dc ";
	    } */
	    
	    if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
	    {
	        $pi = PI;  //圆周率
	        $r = EARTH_R;  //地球平均半径(米)
	        $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
	    
	        $sort_field = " distance asc ";
	    }
	    
	    
	    
	    //参数处理
	    $deal_city_id = intval($GLOBALS['city']['id']);
	    
	    $page = intval($GLOBALS['request']['page']);
	    $page= $page ?: 1;
			
		$page_size = 6;
		$limit = (($page-1)*$page_size).",".$page_size;
	    
	    $param=array("city_id"=>$deal_city_id);
	    
	    //获取餐厅列表
	    $dc_location_list  = get_dc_location_list($type='is_dc',$limit,$param,$tag=array(), $ext_condition,$sort_field,$field_append);
	    
	    if($GLOBALS['db']->getAll($dc_location_list['condition'])){
	        $total = count($GLOBALS['db']->getAll($dc_location_list['condition']));
	    }else{
	        $total=0;
	    }
	    $page_total = ceil($total/$page_size);
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
	    
	    $location_delivery_info=get_location_delivery_info($dc_location_list['id_arr']);
	    
	    $promote_info=get_dc_promote_info();
	    $root['promote_info']=$promote_info?$promote_info:array();
	    
	    $dc_location_list_close=array();
	    foreach($dc_location_list['list'] as $k=>$v){
	    
	        $dc_location_list['list'][$k]['url']=wap_url('index','dcbuy',array('lid'=>$v['id']));
	        if($location_delivery_info[$k]){
	            $dc_location_list['list'][$k]['location_delivery_info']=$location_delivery_info[$k];
	        }
	        if(isset($location_delivery_info[$k])){
	            $dc_location_list['list'][$k]['is_free_delivery']=$location_delivery_info[$k]['is_free_delivery'];
	            $dc_location_list['list'][$k]['start_price']=$location_delivery_info[$k]['start_price'];
	            $dc_location_list['list'][$k]['delivery_price']=$location_delivery_info[$k]['delivery_price'];
	        }else{
	            $dc_location_list['list'][$k]['is_free_delivery']=1;
	            $dc_location_list['list'][$k]['start_price']=0;
	            $dc_location_list['list'][$k]['delivery_price']=0;
	        }
	        
	        $dc_promote_count=0;
	        
	        if($promote_info['is_payonlinediscount'] && $v['is_payonlinediscount']){
	            $dc_promote_count++;
	        }
	        if($promote_info['is_firstorderdiscount'] && $v['is_firstorderdiscount']){
	            $dc_promote_count++;
	        }
	        $dc_location_list['list'][$k]['promote_count']=$dc_promote_count;
	        
	        $dc_location_list['list'][$k]['format_start_price']=format_price($location_delivery_info[$k]['start_price']);
	        $dc_location_list['list'][$k]['format_delivery_price']=format_price($location_delivery_info[$k]['delivery_price']);
	        
	        
	        
	        $dc_location_list['list'][$k]['preview']=get_abs_img_root(get_spec_image($v['preview'],360,360,1));
	        $dc_location_list['list'][$k]['distance']=$v['distance']*1000;
	        
	        if($v['distance']>1){
	            $dc_location_list['list'][$k]['format_distance']=round($v['distance'],2)."km"; 
	        }
	        else{
	            $dc_location_list['list'][$k]['format_distance']=round($v['distance']*1000)."m";
	        }
	        	
	        /* if($dc_location_list['list'][$k]['in_opentime']==0 || $dc_location_list['list'][$k]['is_close']!=0){
	            $dc_location_list_close[]=$dc_location_list['list'][$k];
	            unset($dc_location_list['list'][$k]);
	        } */
	        	
	    }
	    
	    $root['dc_location_list']=$dc_location_list?$dc_location_list:array();
	    
	    //广告
	    $city_id = $GLOBALS['city']['id'];
	    $city_name =  $GLOBALS['city']['name'];
	    
	    $root['city_id'] = $city_id;
	    $root['city_name'] = $city_name;
	    $adv_list = $GLOBALS['cache']->get("MOBILE_INDEX_ADVS_".intval($city_id));
	    
	    //广告列表
	    if($adv_list===false)
	    {
	        if($GLOBALS['request']['from']=='wap'){
	            $mobile_type=1;
	        }else{
	            $mobile_type=0;
	        }
	        $sql = " select * from ".DB_PREFIX."m_adv where mobile_type = ".$mobile_type." and  position=7 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
	        $advs = $GLOBALS['db']->getAll($sql);
	    
	    
	        $adv_list = array();
	        foreach($advs as $k=>$v)
	        {
	            $adv_list[$k]['id'] = $v['id'];
	            $adv_list[$k]['name'] = $v['name'];
	            $adv_list[$k]['img'] = get_abs_img_root($v['img']);  //首页广告图片规格为 宽: 640px 高: 240px
	            $adv_list[$k]['type'] = $v['type'];
	            $adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
	            $adv_list[$k]['ctl'] = $v['ctl'];
	    
	            if($adv_list[$k]['data']['url']){
	                $adv_list[$k]['url']=$adv_list[$k]['data']['url'];
	            }else{
	                $adv_list[$k]['url']=wap_url("index",$adv_list[$k]['ctl'],$adv_list[$k]['data']);
	            }
	    
	        }
	        $GLOBALS['cache']->set("MOBILE_INDEX_ADVS_".intval($city_id),$adv_list,300);
	    }
	    
	    $root['advs']=$adv_list?$adv_list:array();
	    
	    $indexs_list = $GLOBALS['cache']->get("WAP_DC_INDEX");
	    if($indexs_list===false)
	    {
	        $shop_cate_count = data_format_idkey($shop_cate_count , $key='shop_cate_id');
	        	
	        $list_sql="select dc.*,count(dcl.location_id) as location_count from ".DB_PREFIX."dc_cate as dc left join ".DB_PREFIX."dc_cate_supplier_location_link as dcl on dc.id=dcl.dc_cate_id LEFT JOIN ".DB_PREFIX."supplier_location as sl on sl.id=dcl.location_id 
	            left join ".DB_PREFIX."dc_delivery as dd on dd.location_id=sl.id and 
                dd.scale>(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r)/1000  
	            where dc.is_effect=1 and sl.city_id = ".$GLOBALS['city']['id']." and sl.is_effect=1 and sl.is_dc=1 group by dc.id order by dc.sort asc limit 10";	
	        
	        $indexs = $GLOBALS['db']->getAll($list_sql);
	        
	        $indexs_list = array();
	        foreach($indexs as $k=>$v)
	        {
	            if(!$v['location_count'])continue;     
	            
	            $indexs_list[$k]['id'] = $v['id'];
	            $indexs_list[$k]['name'] = $v['name'];
	            $indexs_list[$k]['iconfont'] = $v['iconfont'];//图标名 http://fontawesome.io/icon/bars/
	            $indexs_list[$k]['color'] = $v['iconcolor'];//颜色
	            $indexs_list[$k]['ctl'] = "dc_locations";
	            $indexs_list[$k]['img'] = get_abs_img_root(get_spec_image($v['icon_img'], 100, 100,1));//app菜单背景图
	            $indexs_list[$k]['type'] = 12;
	            $indexs_list[$k]['url'] = SITE_DOMAIN.wap_url("index","dc_locations",array("cid"=>$v['id']));
	        }
	    
	        $GLOBALS['cache']->set("WAP_DC_INDEX",$indexs_list,300);
	    }
	    
	    $root['indexs_list']=$indexs_list?$indexs_list:array();
	    
	    return output($root);
	}

	public function table_index()
	{
	    $root = array();
	    
	    if(	$GLOBALS['request']['from']=='wap'){
	        $ypoint =  $GLOBALS['geo']['ypoint'];  //ypoint
	        $xpoint =  $GLOBALS['geo']['xpoint'];  //xpoint
	    
	    }else{
	        $ypoint = $GLOBALS['request']['ypoint'];  //ypoint
	        $xpoint = $GLOBALS['request']['xpoint'];  //xpoint
	    }
	    
	    $tname='sl';
	    if($GLOBALS['kw']) {
	        $ext_condition.=" and ".$tname.".is_recommend=1 and  ".$tname.".is_delete=0 and ".$tname.".is_effect=1 ";
	    }
	    
	    if($xpoint>0) { /* 排序（$order_type）  default 智能（默认）*/
	        $pi = PI;  //圆周率
	        $r = EARTH_R;  //地球平均半径(米)
	        $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
	    
	        $sort_field = " distance asc ";
	    }
	    
	    //参数处理
	    $city_id = intval($GLOBALS['city']['id']);
	    $city_name =  $GLOBALS['city']['name'];
	    $root['city_id'] = $city_id;
	    $root['city_name'] = $city_name;
	    
	    $page = intval($GLOBALS['request']['page']);
	    $page= $page ?: 1;
		$page_size = 6;
		$limit = (($page-1)*$page_size).",".$page_size;
	    
	    $param=array("city_id"=>$city_id);
	    require_once(APP_ROOT_PATH."system/model/dc.php");
	    // 
	    //获取餐厅列表
	    $dc_location_list  = get_dc_location_list($type='is_res',$limit,$param,$tag=array(), $ext_condition,$sort_field,$field_append);
	    $dc_location_list = $this->formatResLocation($dc_location_list);

	    // 获取门店的商圈信息
	    $locationIdArray = array();
	    foreach ($dc_location_list['list'] as $val) {
	    	$locationIdArray[] = $val['id'];
	    }
	    require_once(APP_ROOT_PATH."system/model/supplier.php");
	    $locationIdAndArea = get_location_area_name($locationIdArray);
	    $locationIdAndCate = get_location_dc_cate_name($locationIdArray);
	    foreach ($dc_location_list['list'] as &$location) {
	    	$location['preview']=get_abs_img_root(get_spec_image($location['preview'],90,90,1));
	    	$location['format_point'] = round($location['avg_point'], 1);
	    	$location['point_percent'] = $location['avg_point'] / 5 * 100;
	        $location['format_distance'] = $location['distance'] > 1 ? round($location['distance'],2)."km" : round($location['distance']*1000)."m";
	    	$location['area_name'] = $locationIdAndArea[$location['id']];
	    	$location['cate_name'] = $locationIdAndCate[$location['id']];
	    }unset($location);
	    // print_r($dc_location_list);exit;
	    $root['dc_location_list'] = $dc_location_list;

	    // 分页
	    if($GLOBALS['db']->getAll($dc_location_list['condition'])){
	        $total = count($GLOBALS['db']->getAll($dc_location_list['condition']));
	    }else{
	        $total=0;
	    }
	    $page_total = ceil($total/$page_size);
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
	    //广告
        if($GLOBALS['request']['from']=='wap'){
            $mobile_type=1;
        }else{
            $mobile_type=0;
        }
        $adv_list = $this->getAdvList($city_id, $mobile_type,8);
	    $root['advs']=$adv_list?$adv_list:array();
	    
	    // 菜单
	    $indexs_list = $this->getIndexs(2);
	    $root['indexs_list']=$indexs_list?$indexs_list:array();
	    
	    return output($root);
	}

	/**
	 * 获取外卖和订座的首页广告列表
	 * @param  integer $city_id     城市id
	 * @param  integer $mobile_type 终端类型 0:app  1:wap
	 * @return [type]               [description]
	 * @param  integer $position    7外卖首页，8预定首页 
	 */
	private function getAdvList($city_id = 0, $mobile_type = 1 , $position)
	{
		$adv_list = $GLOBALS['cache']->get("MOBILE_INDEX_ADVS_".intval($city_id));
	    
	    //广告列表
	    if($adv_list===false) {
	        if($position == 8){
	            $sql = " select * from ".DB_PREFIX."m_adv where mobile_type = ".$mobile_type." and  position=8 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
	        }else{
	            $sql = " select * from ".DB_PREFIX."m_adv where mobile_type = ".$mobile_type." and  position=0 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
	        }
	        $advs = $GLOBALS['db']->getAll($sql);
	    
	        $adv_list = array();
	        foreach($advs as $k=>$v) {
	            $adv_list[$k]['id'] = $v['id'];
	            $adv_list[$k]['name'] = $v['name'];
	            $adv_list[$k]['img'] = get_abs_img_root($v['img']);  //首页广告图片规格为 宽: 640px 高: 240px
	            $adv_list[$k]['type'] = $v['type'];
	            $adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
	            $adv_list[$k]['ctl'] = $v['ctl'];
	    
	            if($adv_list[$k]['data']['url']){
	                $adv_list[$k]['url']=$adv_list[$k]['data']['url'];
	            }else{
	                $adv_list[$k]['url']=wap_url("index",$adv_list[$k]['ctl'],$adv_list[$k]['data']);
	            }
	        }
	        $GLOBALS['cache']->set("MOBILE_INDEX_ADVS_".intval($city_id),$adv_list,300);
	    }
	    return $adv_list;
	}

	/**
	 * 获取首页的菜单
	 * @param  string $type 首页类型 1:外卖 2:订座
	 * @return array       
	 */
	public function getIndexs($type)
	{
		$key = 'WAP_DC_INDEX_'.$type;
		$indexs = $GLOBALS['cache']->get($key);
	    if($indexs===false) {
	    	$module = $type == 2 ? 'dctable#lists' : 'dc_locations';
	        // $list_sql="select dc.*,count(dcl.location_id) as location_count from ".DB_PREFIX."dc_cate as dc left join ".DB_PREFIX."dc_cate_supplier_location_link as dcl on dc.id=dcl.dc_cate_id where dc.is_effect=1 group by dc.id having location_count > 0 order by dc.sort asc limit 10";
	        $list_sql = "select dc.*,count(dcl.location_id) as location_count from ".DB_PREFIX."dc_cate as dc left join ".DB_PREFIX."dc_cate_supplier_location_link as dcl on dc.id=dcl.dc_cate_id LEFT JOIN ".DB_PREFIX."supplier_location as sl on sl.id=dcl.location_id   
	            where dc.is_effect=1 and sl.city_id = ".$GLOBALS['city']['id']." and sl.is_effect=1 and sl.is_reserve=1 group by dc.id having location_count > 0 order by dc.sort asc";
	        $indexs = $GLOBALS['db']->getAll($list_sql);
	        
	        foreach($indexs as &$v) {
	            $v['color'] = $v['iconcolor'];//颜色
	            $v['ctl'] = "dc_locations";
	            $v['img'] = get_abs_img_root(get_spec_image($v['icon_img'], 100, 100,1));//app菜单背景图
	            $v['type'] = 12;
	            $v['url'] = SITE_DOMAIN.wap_url("index",$module,array("cid"=>$v['id']));
	        } unset($v);
	        $GLOBALS['cache']->set($key,$indexs,300);
	    }
	    return $indexs;
	}

	private function formatResLocation($locations)
	{
		foreach ($locations as &$loc) {
			
		}
		unset($loc);
		return $locations;
	}


}
?>