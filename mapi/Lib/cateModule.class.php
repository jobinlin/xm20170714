<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class cateApiModule extends MainBaseApiModule
{
	
	/**
	 */
	public function index()
	{
		/*输出分类*/
		$bcate_list = getShopCateList();
		unset($bcate_list['0']);
		
		$time = NOW_TIME;
		
		$condition = 'select shop_cate_id , count(*) as shop_cate_count from '.DB_PREFIX."deal where";
		
		$condition .= ' is_effect = 1 and is_delete = 0 and buy_type=0 and is_shop=1 and (';
		
		$condition .= " ((".$time.">= begin_time or begin_time = 0) and (".$time."< end_time or end_time = 0) and buy_status <> 2) ";
		
		$condition .= " or (".$time." < begin_time and begin_time <> 0 and notice = 1) ";
		
		$condition .=" )";
		
		$condition .=" group by shop_cate_id";
		
		$shop_cate_count = $GLOBALS['db']->getAll($condition);
		$shop_cate_count_key=array();
		foreach($shop_cate_count as $k=>$v){
			$arr=explode(',',$v['shop_cate_id']);
			foreach($arr as $vv){
				$shop_cate_count_key[$vv]+=$v['shop_cate_count'];
			}
			
		}
		$shop_cate_count=array();
		foreach($shop_cate_count_key as $k=>$v){
			$arr['shop_cate_id']=$k;
			$arr['shop_cate_count']=$v;
			$shop_cate_count[$k]=$arr;
		}
		//require_once(APP_ROOT_PATH."system/model/dc.php");
		//$shop_cate_count = data_format_idkey($shop_cate_count , $key='shop_cate_id');

		foreach ($bcate_list as $k=>$v){
			$bcate_list[$k]['cate_img']=$bcate_list[$k]['cate_img']?get_abs_img_root(get_spec_image($bcate_list[$k]['cate_img'],82,82,1)):"";
			foreach ($v['bcate_type'] as $kk=>$vv){
				$bcate_list[$k]['bcate_type'][$kk]['count']=intval($shop_cate_count[$vv['id']]['shop_cate_count']);
				$bcate_list[$k]['bcate_type'][$kk]['cate_img']=$vv['cate_img']?get_abs_img_root(get_spec_image($vv['cate_img'],82,82,1)):"";
                $bcate_list[$k]['bcate_type'][$kk]['app_url'] = SITE_DOMAIN.wap_url("index","goods#index",array('cate_id'=>$vv['id'],'id'=>$vv['cate_id']));
			}
			$bcate_list[$k]['bcate_type']['0']['name']=$bcate_list[$k]['name'];
			$bcate_list[$k]['bcate_type']['0']['cate_img']=$bcate_list[$k]['cate_img'];

			
		}

		foreach($bcate_list as $k=>$v ){
			$count=0;
			foreach($v['bcate_type'] as $kk=>$vv ){
				$count += $vv['count'];
			}
			$bcate_list[$k]['bcate_type'][0]['count'] = $count;
		}
		//分类下无商品隐藏
		foreach($bcate_list as $k=>$v ){
			if($bcate_list[$k]['bcate_type']['0']['count']=="0"){
				unset($bcate_list[$k]);
				continue;
			}
			foreach($v['bcate_type'] as $kk=>$vv ){
				if($vv['count']=="0"){
					unset($bcate_list[$k]['bcate_type'][$kk]);
				}
			}
		}
		foreach($bcate_list as $k=>$v ){
			$bcate_list[$k]['bcate_type'] = array_values($v['bcate_type']);
		}
		$root['bcate_list'] = array_values($bcate_list);
		$root['page_title'] = "商品分类";
		return output($root);
	}
}
?>