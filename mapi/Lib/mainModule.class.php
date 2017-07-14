<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class mainApiModule extends MainBaseApiModule
{
	
	
	/**
	 * 团购首页接口
	 * 输入：
	 * 无
	 * 
	 * 输出：
	 * advs: array 首页广告
	 * 结构如下
	 * Array
       (
            [0] => Array
                (
                    [id] => 21 [int] 广告的ID
                    [name] => 商品明细 [string] 广告名称
                    [img] => http://localhost/o2onew/public/attachment/sjmapi/5451eb7862ae7.jpg [string] 广告图片 640x360
                    [data] => Array [array] 以key->value方式存储的内容 用于url参数组装
                        (
                            [url] => http://www 
                        )

                    [ctl] => url [string] 定义的ctl
                )
       )
     
	 * 
	 */
	public function index()
	{
		global $is_app;
		$root = array();
		$root['return'] = 1;
		
		$city_id = $GLOBALS['city']['id'];
		$city_name =  $GLOBALS['city']['name'];
		
		$root['city_id'] = $city_id;
		$root['city_name'] = $city_name;
		$adv_list = $GLOBALS['cache']->get("WAP_MAIN_ADVS_".intval($city_id));
		
		$article_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."m_notice where is_effect=1 order by sort limit 10"); 
  
		$root['article']=$article_list;
		$root['is_banner_square'] = 1;  //广告图，0为沃形显示，1为长方形显示
		
		//广告列表
		if($adv_list===false)
		{		
			if(APP_INDEX=='app')
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '0' and position=4 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs = $GLOBALS['db']->getAll($sql);
				if(empty($advs))
				{
					$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=4 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
					$advs = $GLOBALS['db']->getAll($sql);
				}
			}			
			else
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=4 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs = $GLOBALS['db']->getAll($sql);
			}
				
				
			$adv_list = array();
			foreach($advs as $k=>$v)
			{
				$adv_list[$k]['id'] = $v['id'];
				$adv_list[$k]['name'] = $v['name'];
				$adv_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'], 750, 230,1));  //首页顶部广告图片规格为 宽: 750px 高: 230px
				$adv_list[$k]['type'] = $v['type'];
				$adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				$adv_list[$k]['ctl'] = $v['ctl'];
			}
			$GLOBALS['cache']->set("WAP_MAIN_ADVS_".intval($city_id),$adv_list,300);
		}
		$root['advs'] = $adv_list?$adv_list:array();
		
		//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
		//$root['get_domain'] = $domain;
		//return output($root);
		
		//商城首页菜单列表
		$indexs_list = $GLOBALS['cache']->get("WAP_MAIN_INDEX".$city_id);
		if($indexs_list===false)
		{
			//if($is_app)
			//{
			//	$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 0 and city_id in (0,".intval($city_id).") order by sort asc");
			//	if(empty($indexs))
			//		$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 1 and city_id in (0,".intval($city_id).") order by sort asc");
			//}
			//else
			$time = NOW_TIME;
			$condition = 'select d.cate_id , count(*) as count from '.DB_PREFIX."deal d   where";
			$condition .= ' city_id in ('.implode(",",load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id))).') and d.is_effect = 1 and d.is_delete = 0 and d.is_shop=0 AND d.is_location=1 and (';
			$condition .= " ((".$time.">= d.begin_time or d.begin_time = 0) and (".$time."< d.end_time or d.end_time = 0) and d.buy_status <> 2) ";
			$condition .= " or (".$time." < d.begin_time and d.begin_time <> 0 and d.notice = 1) ";
			$condition .=" )";
			$condition .=" and ((d.is_coupon = 1 AND (d.coupon_end_time >= ".NOW_TIME." or d.coupon_end_time=0)) or d.is_coupon=0)";
			$condition .=" group by d.cate_id";
			$cate_count = $GLOBALS['db']->getAll($condition);
			
			
			//require_once(APP_ROOT_PATH."system/model/dc.php");
			//$cate_count = data_format_idkey($cate_count , $key='cate_id');
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
			$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."deal_cate where is_delete = 0 and is_effect=1 order by sort asc");
			$indexs_list = array();
			foreach($indexs as $k=>$v)
			{
				if(!$cate_count[$v['id']]){
					continue;
				}
				
				$indexs_list[$k]['id'] = $v['id'];
				$indexs_list[$k]['name'] = $v['name'];
				$indexs_list[$k]['icon_name'] = $v['m_iconfont'];//图标名 http://fontawesome.io/icon/bars/
				$indexs_list[$k]['color'] = $v['m_iconcolor'];//颜色
				$indexs_list[$k]['bg_color'] = $v['m_iconbgcolor'];//背景颜色
				$indexs_list[$k]['data']['cate_id']=$v['id'];
				$indexs_list[$k]['ctl'] = "tuan";
				$indexs_list[$k]['img'] = get_abs_img_root(get_spec_image($v['app_icon_img'], 100, 100,1));//app菜单背景图
				$indexs_list[$k]['type'] = 11;
				$indexs_list[$k]['url'] =  SITE_DOMAIN.wap_url("index","tuan",array("cate_id"=>$v['id']));
				//$indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				//$indexs_list[$k]['ctl'] = $v['ctl'];
			}
				
				
			$GLOBALS['cache']->set("WAP_MAIN_INDEX".$city_id,$indexs_list,300);
		}
		
		//首页菜单列表
		$mindexs_list = mindex_cate_menu($city_id,$page=2);
		
		$cate_new=array();
		foreach($mindexs_list as $k=>$v){
		    $cate_new[$k]=$v;
		    $cate_new[$k]['url']=getWebAdsUrl($v);
		}
		
		$indexs_list = array_merge($cate_new,$indexs_list);
		
		$indexs = array();
		$indexs['list'] = $indexs_list?$indexs_list:array();
		$indexs['count'] = intval(count($indexs_list));
		$root['indexs'] = $indexs;
		
		//专题位
		$root['zt_html5'] = load_zt_unit("index_zt5.html",$page=2);
		$root['zt_html3'] = load_zt_unit("index_zt3.html",$page=2);
		$root['zt_html4'] = load_zt_unit("index_zt4.html",$page=2);
		$root['zt_html6'] = load_zt_unit("index_zt6.html",$page=2);
		//推荐团购
		if(!$GLOBALS['m_config']['close_index_tuan'])
		{
			$indexs_deal = $GLOBALS['cache']->get("WAP_INDEX_DEAL_".intval($city_id));
			if($indexs_deal === false)
			{
		
				require_once(APP_ROOT_PATH."system/model/deal.php");
				$result = get_deal_list(10,$type=array(DEAL_ONLINE,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>$city_id),""," d.is_recommend = 1 and d.buy_type <> 1 and d.is_shop = 0 AND d.is_location=1 AND ((is_coupon = 1 AND (coupon_end_time >= ".NOW_TIME." or coupon_end_time=0)) or is_coupon=0) ");
				$indexs_deal_rs = $result['list'];
					
				// 获取1个全站优惠
				$sql = " select id,description from ".DB_PREFIX."promote where type = '0'  order by id desc limit 0,1";
				$promotes = $GLOBALS['db']->getRow($sql);
					
		
				$indexs_deal = array();
				foreach($indexs_deal_rs as $k=>$v){
					$indexs_deal[$k] = format_deal_list_item($v);
					$indexs_deal[$k]['supplier_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$indexs_deal[$k]['supplier_id']);
					if ($indexs_deal[$k]['allow_promote'] == 1) {
						$indexs_deal[$k]['promotes_desc'] = $promotes['description'];
					}
					if($GLOBALS['geo']['xpoint']){
						$geo=$GLOBALS['geo'];
						$ypoint =  $geo['ypoint'];  //ypoint
						$xpoint =  $geo['xpoint'];  //xpoint
						$pi = PI;  //圆周率
						$r = EARTH_R;  //地球平均半径(米)
						$distance_sql="SELECT min(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) AS distance
					    FROM fanwe_deal_location_link dll LEFT JOIN fanwe_supplier_location AS sl ON dll.location_id = sl.id
						WHERE dll.deal_id = ".$v['id'];
						$indexs_deal[$k]['distance']=$GLOBALS['db']->getOne($distance_sql);
					}
						
				}
					
				$GLOBALS['cache']->set("WAP_INDEX_DEAL_".intval($city_id),$indexs_deal,300);
			}
		}
		$root['deal_list'] = $indexs_deal?$indexs_deal:array();
		$root['page'] = 1;
		$root['has_next'] = 1;
		//推荐分类
		$sql = " select id,name from ".DB_PREFIX."deal_cate where is_delete = 0 and is_effect = 1 and recommend = 1 order by sort";
		$recommend_deal_cate=$GLOBALS['db']->getAll($sql);
		foreach ($recommend_deal_cate as $tt => $vv){
		    $deal_sql=" select count(*) from ".DB_PREFIX."deal where cate_id=".$vv['id']." and is_effect=1 and is_delete=0 and (coupon_end_time>".NOW_TIME." or coupon_end_time=0)";
		    $count=$GLOBALS['db']->getOne($deal_sql);
			$recommend_deal_cate[$tt]['type']=11;
		    if(!$count){
		       unset($recommend_deal_cate[$tt]);
		    }
		}

		foreach($root['advs'] as $k=>$v)
		{
		
			$root['advs'][$k]['url'] =  getWebAdsUrl($v);
		}
		$root['advs_count'] = count($root['advs']);
		foreach ($root['deal_list'] as $k=>$v){
			//$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
			$deal_param['data_id'] = $v['id'];
			$root['deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
		
		
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1000)
				{
					$distance_str =  round($distance/1000,2)."km";
				}
				else
				{
					$distance_str = round($distance)."m";
				}
			}
			$root['deal_list'][$k]['distance'] = $distance_str;
		
		}
		foreach($recommend_deal_cate as $k=>$v)
		{
			$recommend_deal_cate[$k]['url'] =  wap_url("index","tuan",array("cate_id"=>$v['id']));
		}
		$root['recommend_deal_cate']=array_values($recommend_deal_cate);
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="团购首页";
		$root['mobile_btns_download'] = url("index","app_download");
		//echo "<pre>";print_r($root);exit;
		return output($root);
	}

	//团购首页《推荐团购》分页
	public function load_index_list_data()
	{
	    $root = array();
	    $city_id = $GLOBALS['city']['id'];       	
        require_once(APP_ROOT_PATH."system/model/deal.php");
        $page = intval($GLOBALS['request']['page']); //分页
        
        $page=$page==0?1:$page;
        
        $page_size = 10;
        $limit = (($page-1)*$page_size).",".$page_size;
        $result = get_deal_list($limit,$type=array(DEAL_ONLINE,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>$city_id),""," d.is_recommend = 1 and d.buy_type <> 1 and d.is_shop = 0 AND d.is_location=1 AND ((is_coupon = 1 AND (coupon_end_time >= ".NOW_TIME." or coupon_end_time=0)) or is_coupon=0)");
        $indexs_deal_rs = $result['list'];
        $condition = $result['condition'];
        $tname = "d";
        $sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." where  ".$condition;
        $deals_count = $GLOBALS['db']->getOne($sql);
        
        $indexs_deal = array();
        foreach($indexs_deal_rs as $k=>$v){
            $indexs_deal[$k] = format_deal_list_item($v);
            $indexs_deal[$k]['supplier_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$indexs_deal[$k]['supplier_id']);
            if($GLOBALS['geo']['xpoint']){
            	$geo=$GLOBALS['geo'];
            	$ypoint =  $geo['ypoint'];  //ypoint
            	$xpoint =  $geo['xpoint'];  //xpoint
            	$pi = PI;  //圆周率
            	$r = EARTH_R;  //地球平均半径(米)
            	$distance_sql="SELECT min(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) AS distance
            	FROM fanwe_deal_location_link dll LEFT JOIN fanwe_supplier_location AS sl ON dll.location_id = sl.id
            	WHERE dll.deal_id = ".$v['id'];
            	$indexs_deal[$k]['distance']=$GLOBALS['db']->getOne($distance_sql);
            }
        }
	    
        $root['deal_list'] = $indexs_deal?$indexs_deal:array();
        if($root['deal_list']){
        	 
        
        	foreach($root['deal_list'] as $k=>$v){
        		//$root['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
        		$deal_param['data_id'] = $v['id'];
        		$root['deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
        		 
        		 
        		$distance = $v['distance'];
        		$distance_str = "";
        		if($distance>0)
        		{
        			if($distance>1500)
        			{
        				$distance_str =  round($distance/1000,2)."km";
        			}
        			else
        			{
        				$distance_str = round($distance)."m";
        			}
        		}
        		$root['deal_list'][$k]['distance'] = $distance_str;
        		 
        	}
        	 
        }
        $root['page_total'] = ceil($deals_count / $page_size); 
        $root['page'] = $page;
        $root['has_next'] = $root['page_total']>$root['page']?1:0;
        return output($root);
	}

	
}
?>