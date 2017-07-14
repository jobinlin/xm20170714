<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class shopApiModule extends MainBaseApiModule
{
	
	
	/**
	 * 商城首页接口
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
		$adv_list = $GLOBALS['cache']->get("WAP_SHOP_ADVS_".intval($city_id));
		
		$article_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."m_notice where is_effect=1 order by sort limit 10"); 
  
		$root['article']=$article_list;
		$root['is_banner_square'] =1;  //广告图，0为沃形显示，1为长方形显示
		
		//广告列表
		if($adv_list===false)
		{		
			if(APP_INDEX=='app')
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '0' and position=5 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs = $GLOBALS['db']->getAll($sql);
				if(empty($advs))
				{
					$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=5 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
					$advs = $GLOBALS['db']->getAll($sql);
				}
			}			
			else
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=5 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs = $GLOBALS['db']->getAll($sql);
			}
				
				
			$adv_list = array();
			foreach($advs as $k=>$v)
			{
				$adv_list[$k]['id'] = $v['id'];
				$adv_list[$k]['name'] = $v['name'];
				$adv_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'], 750, 230,1));  //首页顶部广告图片规格为 宽: 750px 高: 325px
				$adv_list[$k]['type'] = $v['type'];
				$adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				$adv_list[$k]['ctl'] = $v['ctl'];
			}
			$GLOBALS['cache']->set("WAP_SHOP_ADVS_".intval($city_id),$adv_list,300);
		}
		$root['advs'] = $adv_list?$adv_list:array();
		$root['advs_count']=count($adv_list);
        // 广告2
		$adv_list2 = $GLOBALS['cache']->get("WAP_INDEX_ADVS2_".intval($city_id));
		if($adv_list2===false)
		{
			if(APP_INDEX=='app')
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '0' and position=3 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs2 = $GLOBALS['db']->getAll($sql);
				if(empty($advs2))
				{
					$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=3 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
					$advs2 = $GLOBALS['db']->getAll($sql);
				}
			}
			else
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=3 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs2 = $GLOBALS['db']->getAll($sql);
			}
		   
		    $adv_list2 = array();
		    foreach($advs2 as $k=>$v)
		    {
		        $adv_list2[$k]['id'] = $v['id'];
		        $adv_list2[$k]['name'] = $v['name'];
		        $adv_list2[$k]['img'] =  get_abs_img_root(get_spec_image($v['img'], 750, 190,1)); //首页中部广告图片规格为 宽: 750px 高: 140px
		        $adv_list2[$k]['type'] = $v['type'];
		        $adv_list2[$k]['data'] = $v['data'] = unserialize($v['data']);
		        $adv_list2[$k]['ctl'] = $v['ctl'];
		    }
		    $GLOBALS['cache']->set("WAP_INDEX_ADVS2_".intval($city_id), $adv_list2, 300);
		}
		$root['advs2'] = $adv_list2?$adv_list2:array();
		
		//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
		//$root['get_domain'] = $domain;
		//return output($root);
		
		//商城首页菜单列表
		$indexs_list = $GLOBALS['cache']->get("WAP_SHOP_INDEX");
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
			
			$condition = 'select shop_cate_id , count(*) as shop_cate_count from '.DB_PREFIX."deal where";
			
			$condition .= ' is_effect = 1 and is_delete = 0 and buy_type=0 and is_shop=1 and (';
			
			$condition .= " ((".$time.">= begin_time or begin_time = 0) and (".$time."< end_time or end_time = 0) and buy_status <> 2) ";
			
			$condition .= " or (".$time." < begin_time and begin_time <> 0 and notice = 1) ";
			
			$condition .=" )";
			
			$condition .=" group by shop_cate_id";
			
			$shop_cate_count = $GLOBALS['db']->getAll($condition);
			require_once(APP_ROOT_PATH."system/model/dc.php");
			$shop_cate_count = data_format_idkey($shop_cate_count , $key='shop_cate_id');
			
			
			$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."shop_cate where pid = 0 and is_delete = 0 and is_effect=1 order by sort asc");
			$shop_id = $GLOBALS['db']->getAll(" select id,name,pid from ".DB_PREFIX."shop_cate where pid <> 0 and is_delete = 0 and is_effect=1 order by sort asc");
			foreach ($shop_id as $k=>$v){
				$shop_pid_id[$v['pid']][]=$v;
			}
			$indexs_list = array();
			foreach($indexs as $k=>$v)
			{
				if(!$shop_cate_count[$v['id']]){
					if(!$shop_pid_id[$v['id']]){
						continue;
					}else{
						$is_lock=0;
						foreach ($shop_pid_id[$v['id']] as $kk=>$vv){
							if($shop_cate_count[$vv['id']]){
								$is_lock=1;
								continue;
							}
						}
						if($is_lock!=1)
							continue;
					}
					
					
				}
				$indexs_list[$k]['id'] = $v['id'];
				$indexs_list[$k]['name'] = $v['name'];
				$indexs_list[$k]['icon_name'] = $v['m_iconfont'];//图标名 http://fontawesome.io/icon/bars/
				$indexs_list[$k]['color'] = $v['m_iconcolor'];//颜色
				$indexs_list[$k]['bg_color'] = $v['m_iconbgcolor'];//背景颜色
				$indexs_list[$k]['data']['cate_id'] = $v['id'];
				$indexs_list[$k]['ctl'] = "goods";
				$indexs_list[$k]['img'] = get_abs_img_root(get_spec_image($v['app_icon_img'], 100, 100,1));//app菜单背景图
				$indexs_list[$k]['type'] = 12;
				$indexs_list[$k]['url'] = SITE_DOMAIN.wap_url("index","goods",array("cate_id"=>$v['id']));
				//$indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				//$indexs_list[$k]['ctl'] = $v['ctl'];
			}
				
				
			$GLOBALS['cache']->set("WAP_SHOP_INDEX",$indexs_list,300);
		}
		
		//首页菜单列表
		$mindexs_list = mindex_cate_menu($city_id,$page=3);
		
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
		
		$root['zt_html3'] = load_zt_unit("index_zt3.html",$page=3);
		$root['zt_html4'] = load_zt_unit("index_zt4.html",$page=3);
		$root['zt_html5'] = load_zt_unit("index_zt5.html",$page=3);
		$root['zt_html6'] = load_zt_unit("index_zt6.html",$page=3);
		
		$adv_list2 = $GLOBALS['cache']->get("WAP_SHOP_ADVS2_".intval($city_id));
		if($adv_list2===false)
		{
			if(APP_INDEX=='app')
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '0' and position=6 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs2 = $GLOBALS['db']->getAll($sql);
				if(empty($advs2))
				{
					$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=6 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
					$advs2 = $GLOBALS['db']->getAll($sql);
				}
			}
			else
			{
				$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = '1' and position=6 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ";
				$advs2 = $GLOBALS['db']->getAll($sql);
			}
			 
			$adv_list2 = array();
			foreach($advs2 as $k=>$v)
			{
				$adv_list2[$k]['id'] = $v['id'];
				$adv_list2[$k]['name'] = $v['name'];
				$adv_list2[$k]['img'] =  get_abs_img_root(get_spec_image($v['img'], 750, 190,1)); //首页中部广告图片规格为 宽: 750px 高: 140px
				$adv_list2[$k]['type'] = $v['type'];
				$adv_list2[$k]['data'] = $v['data'] = unserialize($v['data']);
				$adv_list2[$k]['ctl'] = $v['ctl'];
			}
			$GLOBALS['cache']->set("WAP_SHOP_ADVS2_".intval($city_id), $adv_list2, 300);
		}
		$root['advs2'] = $adv_list2?$adv_list2:array();
		//echo "<pre>";
		//print_r($adv_list2);exit;
		$root['zt_htm5'] = load_zt_unit("index_zt5.html",$page=3);
		
		//推荐商品
		if(!$GLOBALS['m_config']['close_index_shop'])
		{
			$indexs_supplier_deal = $GLOBALS['cache']->get("WAP_INDEX_SUPPLIER_DEAL_".intval($city_id));
			if($indexs_supplier_deal === false)
			{
		
				require_once(APP_ROOT_PATH."system/model/deal.php");
				$result = get_goods_list(10,$type=array(DEAL_ONLINE,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0),""," d.is_recommend = 1 and d.buy_type <> 1 and d.is_shop = 1 ");
				$indexs_supplier_deal_rs = $result['list'];
					
				foreach($indexs_supplier_deal_rs as $k=>$v){
					$indexs_supplier_deal[$k]=format_deal_list_item($v);
				}
				$GLOBALS['cache']->set("WAP_INDEX_SUPPLIER_DEAL_".intval($city_id),$indexs_supplier_deal,300);
			}
		}
		$root['supplier_deal_list'] = $indexs_supplier_deal?$indexs_supplier_deal:array();
		//app需要发放链接，从wap移过来的
		foreach($root['advs'] as $k=>$v)
		{
		
			$root['advs'][$k]['url'] =  getWebAdsUrl($v);
		}
		
		foreach($root['advs2'] as $k=>$v)
		{
		
			$root['advs2'][$k]['url'] =  getWebAdsUrl($v);
		}
		foreach ($root['supplier_deal_list'] as $k=>$v){
			//$root['supplier_deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
			$deal_param['data_id'] = $v['id'];
			$root['supplier_deal_list'][$k]['url'] = wap_url("index", 'deal', $deal_param);
			
		
			$distance = $v['distance'];
			$distance_str = "";
			if($distance>0)
			{
				if($distance>1500)
				{
					$distance_str =  round($distance/1000)."km";
				}
				else
				{
					$distance_str = round($distance)."米";
				}
			}
			$root['supplier_deal_list'][$k]['distance'] = $distance_str;
		
		}
		$root['page'] = 1;
		$root['has_next'] = 1;
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="商城首页";
		$root['mobile_btns_download'] = url("index","app_download");
		return output($root);
	}
	//商城首页《猜你喜欢》分页
	public function load_index_list_data()
	{
		$root = array();
		$city_id = $GLOBALS['city']['id'];
		require_once(APP_ROOT_PATH."system/model/deal.php");
		$page = intval($GLOBALS['request']['page']); //分页
	
		$page=$page==0?1:$page;
	
		$page_size = 10;
		$limit = (($page-1)*$page_size).",".$page_size;
		$result = get_deal_list($limit,$type=array(DEAL_ONLINE,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0),""," d.is_recommend = 1 and d.buy_type <> 1 and d.is_shop = 1 ");
		$indexs_deal_rs = $result['list'];
		$condition = $result['condition'];
		$tname = "d";
		$sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." where  ".$condition;
		$deals_count = $GLOBALS['db']->getOne($sql);
	
		$indexs_deal = array();
		foreach($indexs_deal_rs as $k=>$v){
			$indexs_deal[$k] = format_deal_list_item($v);
		}
		 
		$root['deal_list'] = $indexs_deal?$indexs_deal:array();
		if($root['deal_list']){
		
			foreach($root['deal_list'] as $k=>$v){
					
				$root['deal_list'][$k]['url'] = wap_url("index", 'deal', array('data_id'=>$v['id']));
					
				//$data['deal_list'][$k]['current_price'] = format_price_html($v['current_price']);
		
			}
		
		}
		$root['page_total'] = ceil($deals_count / $page_size);
		$root['page'] = $page;
		$root['has_next'] = $root['page_total']>$root['page']?1:0;
		return output($root);
	}
	

	
}
?>