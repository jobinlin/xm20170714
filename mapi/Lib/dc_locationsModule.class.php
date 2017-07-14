<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dc_locationsApiModule extends MainBaseApiModule
{
    public function index()
    {
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
        if($GLOBALS['kw'])
        {
            $ext_condition.=" and ".$tname.".name like '%".$GLOBALS['kw']."%' ";
        }
        
        if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
        {
            $pi = PI;  //圆周率
            $r = EARTH_R;  //地球平均半径(米)
            $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
        
            $sort_field = "distance asc ";
        }
        
        //参数处理
        $deal_city_id = intval($GLOBALS['city']['id']);
        $sort = intval($GLOBALS['request']['sort']);
        if($sort==1){
            $sort_field = "avg_point desc ";
        }
        $deal_cate_id = intval($GLOBALS['request']['cid']);
        $page = intval($GLOBALS['request']['page']);
        $page=$page==0?1:$page; 
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
        
        //商品属性
        $dc_online_pay = intval($GLOBALS['request']['dc_online_pay']);
        $dc_allow_cod = intval($GLOBALS['request']['dc_allow_cod']);
        $dc_allow_invoice = intval($GLOBALS['request']['dc_allow_invoice']);
        $no_start_price = intval($GLOBALS['request']['no_start_price']);
        $no_delivery_price = intval($GLOBALS['request']['no_delivery_price']);
        $is_firstorderdiscount = intval($GLOBALS['request']['is_firstorderdiscount']);
        $is_payonlinediscount = intval($GLOBALS['request']['is_payonlinediscount']);
        
        $dc_attr=array();
        
        if($dc_online_pay)
        {
            $dc_attr['dc_online_pay']=1;
        }
        if($dc_allow_cod)
        {
            $dc_attr['dc_allow_cod']=1;
        }
        if($dc_allow_invoice)
        {
            $dc_attr['dc_allow_invoice']=1;
        }
        if($is_firstorderdiscount)
        {
            $dc_attr['is_firstorderdiscount']=1;
        }
        if($is_payonlinediscount)
        {
            $dc_attr['is_payonlinediscount']=1;
        }
        $attr_count=0;
        $attr_sql="";
        foreach ($dc_attr as $t => $v){
            if($attr_count==0){
                $attr_sql.=" ".$tname.".".$t."=1 ";
            }else {
                $attr_sql.=" or ".$tname.".".$t."=1 ";
            }
            $attr_count++;
        }
        
        $join=" LEFT JOIN ".DB_PREFIX."dc_delivery as dd on dd.location_id=sl.id and dd.scale>(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 ";
        $join_group=" GROUP BY sl.id,dd.scale ASC ";
        $field_append.=",dd.start_price,dd.scale,dd.delivery_price ";
            
        if($no_start_price){
            if($attr_sql){
                $attr_sql.=" or dd.start_price=0 or dd.start_price=NULL ";
            }else{
                $attr_sql.=" dd.start_price=0  or dd.start_price=NULL ";
            }
        }
        if($no_delivery_price){
            if($attr_sql){
                $attr_sql.=" or dd.delivery_price=0 or dd.delivery_price is NULL ";
            }else{
                $attr_sql.=" dd.delivery_price=0  or dd.delivery_price is NULL ";
            }
        }
        
        if($attr_sql){
            $ext_condition.= " and (".$attr_sql.") ";
        }
        
        if($page==0){
            $sort_field = " sl.avg_point desc ";
        }
        if($deal_cate_id)$url_param['cid'] = $deal_cate_id;
        
        $param=array("cid"=>$deal_cate_id,"city_id"=>$deal_city_id);
        
        //seo元素
        $page_title = "外卖";
        $page_keyword = "外卖";
        $page_description = "外卖";
        
        $area_result = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));	 //商圈缓存
        
        $cate_list = $GLOBALS['cache']->get("WAP_CATE_LIST");
        
        if($cate_list == false){
            $cate_list = load_auto_cache("cache_dc_cate"); //分类缓存
        
            foreach ($cate_list as $t => $v){
                $cate_sql="select count(distinct sl.id) from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."dc_cate_supplier_location_link as dcl on dcl.location_id=sl.id 
                    left join ".DB_PREFIX."dc_delivery as dd on dd.location_id=sl.id and 
                    dd.scale>(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r)/1000 
                    where sl.city_id=".$GLOBALS['city']['id']." and sl.is_dc=1 and sl.is_effect=1 and dcl.dc_cate_id=".$v['id'];
                
                $cate_count=$GLOBALS['db']->getOne($cate_sql);
                if($cate_count){
                    $cate_list[$t]['cate_count']=$cate_count;
                }else{
                    unset($cate_list[$t]);
                }
                
            }
            
            $GLOBALS['cache']->set("WAP_CATE_LIST",$cate_list,300);
        
        }
        
        $all_sql="select count(*) from ".DB_PREFIX."supplier_location as sl
                left join ".DB_PREFIX."dc_delivery as dd on dd.location_id=sl.id and
                        dd.scale>(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r)/1000
                        where sl.city_id=".$GLOBALS['city']['id']." and sl.is_dc=1 and sl.is_effect=1";
        $root['all_count']=$GLOBALS['db']->getOne($all_sql);
        
        $root['cate_list']=$cate_list;
        
        //获取餐厅列表
        $dc_location_list  = get_dc_location_list($type='is_dc',$limit,$param,$tag=array(), $ext_condition,$sort_field,$field_append,$join,$join_group);
        
        if($GLOBALS['db']->getAll($dc_location_list['condition'])){
            $total = count($GLOBALS['db']->getAll($dc_location_list['condition']));
        }else{
            $total=0;
        }
        
        $new_dc_location=array();
        $promote_info=get_dc_promote_info();
        
        $root['promote_info']=$promote_info;
        
        
        foreach ($dc_location_list['list'] as $t => $v){
            $v['preview']=get_abs_img_root(get_spec_image($v['preview'],250,250,1));
            
            $promote_count=0;
            if($v['is_firstorderdiscount'] && $promote_info['is_firstorderdiscount']){
                $promote_count++;
            }
            if($v['is_payonlinediscount'] && $promote_info['is_payonlinediscount']){
                $promote_count++;
            }
            $v['promote_count']=$promote_count;
            
            $v['format_start_price']=format_price($v['start_price']);
            $v['format_delivery_price']=format_price($v['delivery_price']);
            
            if($v['distance']>1){
                $v['format_distance']=round($v['distance'],2)."km";
            }
            else{
                $v['format_distance']=round($v['distance']*1000)."m";
            }
            
            $new_dc_location[]=$v;
        }
        
        $page_total = ceil($total/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total);
        $root['dc_location_list']=$new_dc_location;
        
        return output($root);
    }
}