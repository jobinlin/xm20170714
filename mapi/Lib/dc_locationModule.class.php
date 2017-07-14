<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dc_locationApiModule extends MainBaseApiModule
{
    public function index()
    {
        global_run();
        
        require_once(APP_ROOT_PATH."system/model/dc.php");
        $location_id = intval($GLOBALS['request']['data_id']);
        $menu_id = intval($GLOBALS['request']['menu_id']);
        
        $root=array();
        $tname='l';
        //开始身边团购的地理定位
        $user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;
        
        if(	$GLOBALS['request']['from']=='wap'){
            $ypoint =  $GLOBALS['geo']['ypoint'];  //ypoint
            $xpoint =  $GLOBALS['geo']['xpoint'];  //xpoint
        
        }else{
            $ypoint = $GLOBALS['request']['ypoint'];  //ypoint
            $xpoint = $GLOBALS['request']['xpoint'];  //xpoint
        }
        
        
        if($xpoint>0)
        {
            $pi = PI;
            $r = EARTH_R;
            $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((".$tname.".ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((".$tname.".ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (".$tname.".xpoint * $pi) / 180 ) ) * $r)/1000 as distance ";
        
        }
        
        $dclocation=get_location_info($tname,$location_id,$field_append);
        
        $location_info=array();
        if($dclocation && $dclocation['is_dc']==1)
        {
            $dclocation['preview']=get_abs_img_root(get_spec_image($dclocation['preview'],150,150,1));
            
            $is_colloect=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."dc_location_sc where location_id=".$dclocation['id']." and user_id=".$user_id);
            if($is_colloect>0){
                $dclocation['is_collected']=1;
            }else{
                $dclocation['is_collected']=0;
            }
            
            if(!$dclocation['open_time_cfg_str']){
                $dclocation['open_time_cfg_str']='全天24小时';
            }
            
            //关于分类信息与seo
            $page_title = $dclocation['name'];
            $page_keyword = $dclocation['name'];
            $page_description = $dclocation['name'];
            $id_arr=array($dclocation['id']=>array('id'=>$dclocation['id'],'distance'=>$dclocation['distance']));
            $location_delivery_info=get_location_delivery_info($id_arr);
            	
            $location_delivery=array();
            foreach($location_delivery_info as $kk=>$vv){
                $vv['format_start_price']    = format_price($vv['start_price']);
                $vv['format_delivery_price'] = format_price($vv['delivery_price']);
                $location_delivery=$vv;
            }
            
            $cate_data=$GLOBALS['db']->getOne("select group_concat(dc.name) from ".DB_PREFIX."dc_cate as dc left join ".DB_PREFIX."dc_cate_supplier_location_link as dcl on dc.id=dcl.dc_cate_id where dcl.location_id=".$location_id);
            $cate_data_new=explode(',',$cate_data);
            $dclocation['cate_data']=$cate_data_new?$cate_data_new:array();
            
            $dclocation['location_delivery_info']=$location_delivery?$location_delivery:array();
            	
            $dclocation['distance']=$dclocation['distance']*1000;
            
            $promote_info=get_dc_promote_info();
            
            $promote_count=0;
            if($dclocation['is_firstorderdiscount']){
                $dclocation['firstorderdiscount'] = $promote_info['is_firstorderdiscount'];
                $promote_count++;
            }
            if($dclocation['is_payonlinediscount']){
                $dclocation['payonlinediscount']  = $promote_info['is_payonlinediscount'];
                $payonline_conf = unserialize($promote_info['is_payonlinediscount']['config']);
                
                $new_conf=array();
                foreach ($payonline_conf['discount_limit'] as $t => $v){
                    $new_conf[$v]['discount_limit']=$v;
                    $new_conf[$v]['discount_amount']=$payonline_conf['discount_amount'][$t];
                }
                sort($new_conf);
                
                $promote_count++;
            }
            $dclocation['payonline_conf']=json_encode($new_conf);
            $dclocation['promote_count']=$promote_count;
            
            //打包费
            $package_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_package_conf where location_id=".$location_id);

            $root['is_has_location']=1;
            $root['page_title']=$page_title;
            $root['page_keyword']=$page_keyword;
            $root['page_description']=$page_description;
            
            //评论
            $dp_info=$this->location_dp_list();
            
            $dclocation['dc_avg_point']=round($dp_info['avg_point'],1);
            $dclocation['dc_avg_point_p']=$dclocation['dc_avg_point']*100/5;
            
            $root['dp_info']=$dp_info;
            $root['dclocation']=$dclocation;
            $root['page_title']=$dclocation['name'];
            
            $root['package_info']=$package_info;
            
            //获取菜单
            $location_menu_cate=$this->get_menu_cate($dclocation['id']);
            foreach($location_menu_cate as $k=>$v){
                $location_menu_cate[$k]['main_cate']['icon_img']=get_abs_img_root(get_spec_image($v['main_cate']['icon_img'],250,250,1));
                foreach($location_menu_cate[$k]['sub_menu'] as $kk=>$vv){
                    $location_menu_cate[$k]['sub_menu'][$kk]['image']=get_abs_img_root(get_spec_image($vv['image'],250,250,1));
                    if($vv['id']==$menu_id){
                        $menu_add=$vv;
                    }
                }
            }
            $root['menu_list']=$location_menu_cate;
            $root['menu_add']=$menu_add;
            
            
            //获取购物车
            /*$location_dc_table_cart=load_dc_cart_list(true,$dclocation['id'],$type=1);
            $cart_total_count=0;
            foreach($location_dc_table_cart['cart_list'] as $xx=>$zz)
            {
                $location_dc_table_cart['cart_list'][$xx]['format_unit_price']=format_price($zz['unit_price']);
                $location_dc_table_cart['cart_list'][$xx]['icon']=get_abs_img_root(get_spec_image($zz['icon'],200,150,1));
                $cart_total_count+=$zz['num'];
            }
            $location_dc_table_cart['total_data']['format_total_price']=format_price($location_dc_table_cart['total_data']['total_price']);
            if($location_dc_table_cart['total_data']['total_price']<$package_info['package_start_price'] || $package_info['package_start_price']<0)
            {
                $location_dc_table_cart['total_data']['total_package_price']=$package_info['package_price']*$location_dc_table_cart['total_data']['total_count'];
                $location_dc_table_cart['total_data']['format_total_package_price']=format_price($location_dc_table_cart['total_data']['total_package_price']);
                $location_dc_table_cart['total_data']['all_price']=$location_dc_table_cart['total_data']['total_package_price']+$location_dc_table_cart['total_data']['total_price'];
                $location_dc_table_cart['total_data']['format_all_price']=format_price($location_dc_table_cart['total_data']['all_price']);
                $location_dc_table_cart['total_data']['cart_count']=$cart_total_count;
            }*/
            
			$is_in_open_time=is_in_open_time($dclocation['id']);
			
			$root['is_in_open_time']=$is_in_open_time;
            
			if($GLOBALS['request']['from']=='wap'){
			    /* if($location_dc_table_cart['total_data']['total_count'] == 0 ){ */
			        
	            if ($is_in_open_time==1){
	                if($location_delivery['is_free_delivery']==2){
	                    $is_allow_add_cart=0;
	                }else{
	                    $is_allow_add_cart=1;
	                }   	
		            
		        }else{
		            $is_allow_add_cart=0;
		        }
			    /* }else{
			        $is_allow_add_cart=1;
			    } */
			    	
			}else{
			    
		        if ($is_in_open_time==1){
		            if($location_delivery['is_free_delivery']==2){
		                $is_allow_add_cart=0;
		            }else{
		                $is_allow_add_cart=1;
		            }
		            	
		        }else{
		            $is_allow_add_cart=0;
		        }
			    
			}            
            
			$root['is_free_delivery']=$location_delivery['is_free_delivery']?$location_delivery['is_free_delivery']:0;
			
			$root['is_allow_add_cart']=$is_allow_add_cart;
			
			// $root['cart_data']=$is_allow_add_cart?$location_dc_table_cart:array();
			
            return output($root);
        } else {
            $root['is_has_location']=0;
            return output($root);
        }
        
    }
    
    
    /**
     * 商户评价列表
     *  */
    public function location_dp_list() {
        global_run();
        
        require_once(APP_ROOT_PATH."system/model/dc.php");
        $location_id = strim($GLOBALS['request']['data_id']);
        
        $location_id = strim($GLOBALS['request']['data_id']);
        
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page=$page==0?1:$page;
        	
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
        
        $orderby = " order by create_time desc ";
        $limit = " limit ".$limit;
        $where=" where status=1 and event_id=0 and deal_id=0 and youhui_id=0 ";
        $sql = "SELECT id,title,content,create_time,point,user_id,status,reply_content,images_cache,avg_price,supplier_location_id FROM ".DB_PREFIX."supplier_location_dp ".$where." and supplier_location_id =".$location_id.$orderby.$limit;
        $data = $GLOBALS['db']->getAll($sql);
        $sql_count = "SELECT count(*) FROM ".DB_PREFIX."supplier_location_dp ".$where." and supplier_location_id =".$location_id;
        $sql_avg = "SELECT avg(point) FROM ".DB_PREFIX."supplier_location_dp ".$where." and supplier_location_id =".$location_id;
        
        $total_count=$GLOBALS['db']->getOne($sql_count);
        $avg_point=$GLOBALS['db']->getOne($sql_avg);
        
        $page_total = ceil($total_count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$total_count);
        
        $dp_list=array();
        if($data){
            foreach ($data as $k=>$v){  
                //获取用户信息
                $user_name = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$v['user_id']);
                $v['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"big"))?get_abs_img_root(get_muser_avatar($v['user_id'],"big")):"";
                $v['user_name'] = $user_name;
                
                $v['point_p'] = $v['point']*100/5;
        
                //获取条件关联信息
                $v['create_time_format'] = to_date($v['create_time']);
                $images = unserialize($v['images_cache']);
        
                $images_new=array();
                $i=1;
                foreach($images as $kk=>$vv){
                    $images_new[$i]=get_abs_img_root(get_spec_image($vv,100,80,1));
                    $i++;
                }
        
                $v['images'] = $images_new?$images_new:array();
                $dp_list[] = $v;
        
            }
        }
        
        $result['avg_point']=$avg_point;
        $result['dp_list']=$dp_list;
        
        return $result;
    }
    
    
    
    /**
     * 获取餐厅的菜单分类与菜单
     * @param unknown_type $location_id  门店ID
     * @return $location_menu_arr 返回菜单的数据集
     */
    public function get_menu_cate($location_id){
        require_once(APP_ROOT_PATH."system/model/dc.php");
        global_run();
    
        $user_id=isset($GLOBALS['user_info']['id'])?intval($GLOBALS['user_info']['id']):0;
        $location_menu_cate=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_supplier_menu_cate where is_effect=1 and location_id=".$location_id." order by sort");
        $location_menu=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_menu where is_effect=1 and location_id=".$location_id." order by image desc");
        $location_menu_arr=array();
        $location_menu_info=array();
    
        if($location_menu){
            $id_str=get_id_str($location_menu);
            // $cart_info=$GLOBALS['db']->getAll("select num, menu_id from ".DB_PREFIX."dc_cart where user_id=".$user_id." and session_id='".es_session::id()."' and cart_type=1  and is_effect=1 and menu_id in (".$id_str.")");
            // $cart_info_new=data_format_idkey($cart_info,$key='menu_id');
    
            $location_menu_new=data_format_idkey($location_menu,$key='id');
            foreach($location_menu_new as $m=>$n){
                // $n['cart_count']=isset($cart_info_new[$m]['num'])?$cart_info_new[$m]['num']:0;
                // $n['format_price']=format_price($n['price']);
                $n['format_price'] = format_price($n['price']);
                $location_menu_info[$n['cate_id']][]=$n;
            }
    
            $id_cat_str=get_id_str($location_menu_cate);
            $has_p_menu_count=$GLOBALS['db']->getAll("select count(*) as count,cate_id from ".DB_PREFIX."dc_menu where is_effect=1 and location_id=".$location_id." and cate_id in (".$id_cat_str.") and image!='' group by cate_id");
            $no_p_menu_count=$GLOBALS['db']->getAll("select count(*) as count,cate_id from ".DB_PREFIX."dc_menu where is_effect=1 and location_id=".$location_id." and cate_id in (".$id_cat_str.") and image='' group by cate_id");
            $has_p_menu_count_new=data_format_idkey($has_p_menu_count,$key='cate_id');
            $no_p_menu_count_new=data_format_idkey($no_p_menu_count,$key='cate_id');
    
            foreach($location_menu_cate as $k=>$v){
                if(count($location_menu_info[$v['id']])>0){
                    $location_menu_arr[$v['id']]['main_cate']=$v;
                    $location_menu_arr[$v['id']]['has_image_count']=isset($has_p_menu_count_new[$v['id']]['count'])?$has_p_menu_count_new[$v['id']]['count']:0;
                    $location_menu_arr[$v['id']]['no_image_count']=isset($no_p_menu_count_new[$v['id']]['count'])?$no_p_menu_count_new[$v['id']]['count']:0;
                    $location_menu_arr[$v['id']]['sub_menu']=$location_menu_info[$v['id']];
                }
            }
    
        }
    
        /* $row_num=3;
        foreach($location_menu_arr as $k=>$v){
            if($v['has_image_count'] > 0){
                $last_row=ceil($v['has_image_count']/$row_num);
                $min=$row_num*($last_row-1);
                foreach($v['sub_menu'] as $m=>$n){
                    // 有图片菜单的最后一行 
                    if($m>=$min && $m<$v['has_image_count']){
                        $location_menu_arr[$k]['sub_menu'][$m]['is_last_row']=1;
                    }
                    if(($m+1)%$row_num==0){
                        $location_menu_arr[$k]['sub_menu'][$m]['is_last_col']=1;
                    }
                }
            }
            if($v['no_image_count'] > 0){
                // 没有图片菜单的第一行 
                $location_menu_arr[$k]['sub_menu'][$v['has_image_count']]['is_first_row']=1;
            }
    
    
        } */
    
        return array_values($location_menu_arr);
    }
    
}