<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class dc_searchApiModule extends MainBaseApiModule
{
	
	/**
	 * 搜索首页
	 * 输入：
	 *           
	 * 
	 * 
	 * 
	 */
	public function index()
	{

		$root = array();
		$root['page_title']='搜索';
		return output($root);
	}
	
	

	/**
	 * 搜索商家和商品的提交接口
	 * 
	 * 输入：
	 * keyword：string 搜索关键词
	 *
	 * 输出：
	 * 
	 * location：array 商家列表
	 * lid_count：int 商家数量
	 * menu：array,商品列表
	 * menu_count：商品数量
	 * 
	 * 
	 */ 
	public function do_search(){
	
		$root=array();
		$keyword=strim($GLOBALS['request']['keyword']);
		$type=intval($GLOBALS['request']['type']);

		if($keyword==''){
			return output($root,0,'请输入关键词');
		}
		
			
		if($type==1){
		    
		    /*  搜索外卖店铺
		     */
		    $location_info=$GLOBALS['db']->getAll("select id,name,preview,dc_buy_count,avg_point from ".DB_PREFIX."supplier_location where name like '%".$keyword."%' and is_dc=1");
		    require_once(APP_ROOT_PATH."system/model/dc.php");
		    foreach($location_info as $k=>$v){
		        $location_info[$k]['url']=wap_url('index','dc_location',array('data_id'=>$v['id']));
		        $location_info[$k]['preview']=get_abs_img_root(get_spec_image($v['preview'],180,135,1));
		        $location_info[$k]['format_point']=($v['avg_point'] / 5) * 100;
		        $location_info[$k]['avg_point'] = sprintf('%.1f', round($v['avg_point'], 1));
		        	
		    }
		    $root['location']=$location_info ? $location_info:array();
		    $root['lid_count']=count($location_info);
		    
		    /*
		     * 搜索美食
		     */
		    
		    $menu_info=$GLOBALS['db']->getAll("select id,name,price,location_id,image from ".DB_PREFIX."dc_menu where name like '%".$keyword."%' and is_effect=1");
		    foreach($menu_info as $k=>$v){
		        $lid_info=$GLOBALS['db']->getRow("select id,name from ".DB_PREFIX."supplier_location where id=".$v['location_id']." and is_dc=1");
		        if($lid_info){
		            $menu_info[$k]['lid_name']=$lid_info['name'];
		            $menu_info[$k]['image'] = get_abs_img_root(get_spec_image($v['image'],180,135,1));
		            $menu_info[$k]['url']=wap_url('index','dc_location',array('data_id'=>$lid_info['id'],'menu_id'=>$v['id']));
		            //$menu_info[$k]['url']=wap_url('index','dcbuy',array('lid'=>$lid_info['id']));
		        }else{
		            unset($menu_info[$k]);
		        }
		    }
		    
		    $menu_info=array_values($menu_info);
		    $root['menu']=$menu_info;
		    $root['menu_count']=count($menu_info);
		}elseif($type==2){
		    
		    /*  搜索预定店铺
		     */
		    $location_info=$GLOBALS['db']->getAll("select id,name,preview,dc_buy_count,avg_point from ".DB_PREFIX."supplier_location where name like '%".$keyword."%' and is_dc=1 and is_reserve=1");
		    require_once(APP_ROOT_PATH."system/model/dc.php");
		    foreach($location_info as $k=>$v){
		        $location_info[$k]['url']=wap_url('index','dctable#detail',array('lid'=>$v['id']));
		        $location_info[$k]['preview']=get_abs_img_root(get_spec_image($v['preview'],180,135,1));
		        $location_info[$k]['format_point']=($v['avg_point'] / 5) * 100;
		        $location_info[$k]['avg_point'] = sprintf('%.1f', round($v['avg_point'], 1));
		        	
		    }
		    $root['location']=$location_info ? $location_info:array();
		    $root['lid_count']=count($location_info);
		    
		    /*
		     * 搜索预订
		     */
		    $dctable_info=$GLOBALS['db']->getAll("select sl.id as l_id,sl.name as l_name,sl.preview,rsi.name as rs_name,rsi.price,count(rit.id) as ecount from ".DB_PREFIX."dc_rs_item as rsi  
		        left join ".DB_PREFIX."supplier_location as sl on rsi.location_id=sl.id 
		        left join ".DB_PREFIX."dc_rs_item_time as rit on rsi.id=rit.item_id and rit.is_effect=1 
		        where ((rsi.name like '%".$keyword."%') or (sl.name like '%".$keyword."%') ) and sl.is_dc=1 and rsi.is_effect=1
		        group by rsi.id having ecount >0");
		    foreach($dctable_info as $k=>$v){
		        $dctable_info[$k]['s_name']=$v['l_name']."-".$v['rs_name'];
		        $dctable_info[$k]['preview']=get_abs_img_root(get_spec_image($v['preview'],180,135,1));
		        $dctable_info[$k]['price'] = round($v['price'],2);
		        $dctable_info[$k]['url']=wap_url('index','dctable#detail',array('lid'=>$v['l_id']));
		    }
		    //logger::write(print_r($dctable_info,1));
		    $dctable_info=array_values($dctable_info);
		    $root['dctable_info']=$dctable_info;
		    $root['dctable_info_count']=count($dctable_info);
		}

		

		
		return output($root);
	}
	
}
?>

