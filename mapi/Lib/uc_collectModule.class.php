<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

require_once(APP_ROOT_PATH."system/model/uc_center_service.php");
class uc_collectApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 会员中心我的商品团购收藏列表接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * goods_list:array:array 商品和团购收藏列表，结构如下
	 *  Array
        (
            [0] => Array
                (
                    [id] => 67  [int] 收藏对应的商品或团购id
                    [cid] => 27 [int] 收藏记录id                    
                    [sub_name] => 精油开背套餐  [string]商品或团购简短名称
                    [origin_price] => 236.0000  [int]原价
                    [current_price] => 158.0000 [int]当前销售价
                    [buy_count] => 0 [int]销售量
                    [brief] => 【五一广场】爱丁堡尊贵养生会所 [string]简介
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/16/54ed8ed63ee25_280x170.jpg  [string]图片路径 140*85像素
                )
         )     
	 */
	public function index()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){			
			$root['user_login_status'] = $user_login_status;
		
		}else{
			$root['user_login_status'] = 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;			
			$result = get_collect_list($limit,$user_id);
			$page_total = ceil($result['count']/$page_size);
			$list=array();
			foreach($result['list'] as $k=>$v)
			{	
				$list[$k]['id']=$v['id'];
				$list[$k]['cid']=$v['cid'];
				$list[$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,1));
				$list[$k]['sub_name']=$v['sub_name'];
				$list[$k]['origin_price']= round($v['origin_price'],2);
				$list[$k]['current_price']=round($v['current_price'],2);
				$list[$k]['buy_count']=$v['buy_count'];
				$list[$k]['brief']=$v['brief'];
			}
			
			$root['goods_list'] = $list?$list:array();			
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$result['count']);
			
			//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
			$root['page_title']="商品团购收藏";

		}
		return output($root);

	}

	
	
	
	
	/**
	 * 	 会员中心我的优惠券收藏列表接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录 2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * youhui_list:array:array 优惠券收藏列表，结构如下
	 *  Array
        (
            [0] => Array
                (
                    [id] => 67  [int] 收藏对应的优惠券id
                    [cid] => 27 [int] 收藏记录id                    
                    [name] => 精油开背套餐  [string]优惠券名称
                    [down_count] => 0 [int]下载数量
                    [begin_time]=>2015-02-01至2021-02-26[string]  起止时间
                    [list_brief] => 【五一广场】爱丁堡尊贵养生会所 [string]简介
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/16/54ed8ed63ee25_280x170.jpg  [string]图片路径140*85像素
                )
         )     
	 */
	public function youhui_collect()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();
		if(check_login()!=1){			
			$root['user_login_status'] = $user_login_status;
		
		}else{
			$root['user_login_status'] = 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;			
			$result = get_youhui_collect($limit,$user_id);
			$page_total = ceil($result['count']/$page_size);
			$list=array();
			foreach($result['list'] as $k=>$v)
			{	
				$list[$k]['id']=$v['id'];
				$list[$k]['cid']=$v['cid'];
				$list[$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,1));
				$list[$k]['name']=$v['name'];
				$list[$k]['down_count']=$v['user_count'];
				$list[$k]['list_brief']=$v['list_brief'];
				
				$begin_time = to_date($v['begin_time'],"Y-m-d");
				$end_time = to_date($v['end_time'],"Y-m-d");
				if($begin_time&&$end_time)
					$time_str = $begin_time."至".$end_time;
				elseif($begin_time&&!$end_time)
				$time_str = $begin_time."开始";
				elseif(!$begin_time&&$end_time)
				$time_str = $end_time."结束";
				else
					$time_str = "无限期";
				$list[$k]['begin_time'] = $time_str;
			}
			
			$root['youhui_list'] = $list?$list:array();			
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$result['count']);
			
			//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
			$root['page_title']="优惠券收藏";

		}
		
		return output($root);

	}	
	
	

	
	/**
	 * 	 会员中心我的活动收藏列表接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * event_list:array:array 活动收藏列表，结构如下
	 *  Array
        (
            [0] => Array
                (
                    [id] => 67  [int] 收藏对应的活动id
                    [cid] => 27 [int] 收藏记录id                    
                    [name] => 精油开背套餐  [string]活动名称
                    [submit_count] => 0 [int]已报名人数
                    ['sheng_time_format']=>已过期/永不过期/16天03小时12分[string]  活动剩余时间
                    [brief] => 【五一广场】爱丁堡尊贵养生会所 [string]简介
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/16/54ed8ed63ee25_280x170.jpg  [string]图片路径140*85像素
                )
         )     
	 */
	public function event_collect()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页

		$user_login_status = check_login();
		if(check_login()!=1){			
			$root['user_login_status'] = $user_login_status;

		}else{
			$root['user_login_status'] = 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;			
			$result = get_event_collect($limit,$user_id);
			$page_total = ceil($result['count']/$page_size);
			$list=array();
			foreach($result['list'] as $k=>$v)
			{	
				$list[$k]['id']=$v['id'];
				$list[$k]['cid']=$v['cid'];
				$list[$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1));
				$list[$k]['name']=$v['name'];
				$list[$k]['submit_count']=$v['submit_count'];
				$list[$k]['brief']=$v['brief'];				
				if($v['submit_end_time']==0)
				$list[$k]['sheng_time_format']= "永不过期";
				elseif($v['submit_end_time']-NOW_TIME<0)
				$list[$k]['sheng_time_format']="已过期";
				else
				$list[$k]['sheng_time_format']= to_date($v['submit_end_time']-NOW_TIME,"d天h小时i分");
			}
			
			$root['event_list'] = $list?$list:array();			
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$result['count']);
			
			//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
			$root['page_title']="活动收藏";

		}
		
		return output($root);

	}		
	
	


	/**
	 * wap端 我的收藏方法重写
	 * @return array
	 * 会员中心我的活动收藏列表接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  sc_status => 0（商品团购），1（优惠券），2（活动）
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * event_list:array:array 活动收藏列表，结构如下
	 * [sc_status] => 0（商品团购），1（优惠券），2（活动）
	 *  商品、团购：Array
        (
            [0] => Array
                (
                    [id] => 244
                    [name] => 榭都中长款毛呢大衣 格子茧型冬季外套 秋冬韩版呢子呢大衣品牌女
                    [sub_name] => 榭都中长款毛呢大衣
                    [end_time] => 1543606372
                    [origin_price] => 999
                    [current_price] => 449
                    [buy_count] => 564
                    [brief] => 榭都中长款毛呢大衣 格子茧型冬季外套
                    [icon] => http://localhost/o2onew/public/attachment/201610/08/14/57f890748e5c0_560x560.jpg
                    [is_valid] => 1
                    [add_time] => 1482085736
                    [cid] => 143
                )
         ) 
                优惠券：Array
        (
            [0] => Array
                (
                    [id] => 44
                    [name] => 积分10000
                    [icon] => http://localhost/o2onew/public/attachment/201611/10/17/582441c8705d0_560x560.jpg
                    [user_count] => 9
                    [list_brief] => 
                    [begin_time] => 0
                    [end_time] => 0
                    [score_limit] => 10000
                    [point_limit] => 0
                    [is_valid] => 1
                    [uid] => 127
                    [add_time] => 1481932578
                    [cid] => 155
                )
         ) 
                活动：Array
        (
            [0] => Array
                (
                    [id] => 11
                    [name] => 大通冰室（元洪城）双人餐免费抢
                    [score_limit] => 0
                    [point_limit] => 10
                    [img] => ./public/attachment/201612/01/16/583fe68f043b2.png
                    [icon] => http://localhost/o2onew/public/attachment/201612/01/16/583fe68f043b2_560x560.jpg
                    [brief] => 
                    [submit_count] => 4
                    [submit_end_time] => 0
                    [is_valid] => 1
                    [uid] => 127
                    [add_time] => 1481334569
                    [cid] => 19
                    [sheng_time_format] => 永久
                )
         ) 
         
                 店铺：Array
        (
            [0] => Array
                (
                    [id] => 21
                    [name] => 桥亭活鱼小镇
                    [preview] => http://localhost/o2onew/public/attachment/201611/11/12/582543677954f_560x560.jpg
                    [city_id] => 15              -> 城市ID
                    [avg_point] => 3.8462        ->评分
                    [dp_count] => 13
                    [city_name] => 福州
                    [add_time] => 2017-04-13
                    [link_id] => 1
                    [distance] => 1.87km
                    [url] => http://localhost/o2onew/wap/index.php?ctl=dc_location&data_id=21
                    [cate_name] => 甜品                            ->订餐分类
                    [area_name] => 宝龙城市广场             ->商圈
                ) 
         ) 
	 */
	
	public function wap_index(){
	    $root = array();
	    	
	    $user_data = $GLOBALS['user_info'];
	    $user_id = intval($user_data['id']);
	    $user_login_status = check_login();
	    if($user_login_status == LOGIN_STATUS_LOGINED){
	       //分页
			$page = intval($GLOBALS['request']['page']);
			$status = intval($GLOBALS['request']['sc_status']);
			$page= $page ?: 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			
			if($status==0){
    	        /*商品、团购收藏*/
    			$result = get_collect_list($limit,$user_id);
    			$list=$result['list'];
    			foreach($list as $k=>$v)
    			{
    			    $tmp_url_param['data_id']=$v['id'];
    			    $list[$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1));
    			    $list[$k]['origin_price']= round($v['origin_price'],2);
    			    $list[$k]['current_price']=round($v['current_price'],2);
    			    $list[$k]['url']=SITE_DOMAIN.wap_url("index","deal",$tmp_url_param);
    			    if($v['end_time']<NOW_TIME&&$v['end_time']!=0){
    			        $list[$k]['out_time']=1;
    			    }
    			}
			}
			/* else if ($status==1){
			    //优惠券收藏
			    $result = get_youhui_collect($limit,$user_id);
			    $list=$result['list'];
			    foreach($list as $k=>$v)
			    {
			        $tmp_url_param['data_id']=$v['id'];
			        $list[$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1));
			        
			        $begin_time = to_date($v['begin_time'],"Y-m-d");
			        $end_time = to_date($v['end_time'],"Y-m-d");
			        if($begin_time&&$end_time){
			            $time_str = $begin_time."至".$end_time;
			        }
			        elseif($begin_time&&!$end_time){
			            $time_str = $begin_time."开始";
			        }
			        elseif(!$begin_time&&$end_time){
			            $time_str = $end_time."结束";
			        }
			        else{
			            $time_str = "永久";
			        }
			        $list[$k]['url']=SITE_DOMAIN.wap_url("index","youhui",$tmp_url_param);
			        if($v['end_time']<NOW_TIME&&$v['end_time']!=0){
			            $list[$k]['out_time']=1;
			            $list[$k]['begin_time'] = $time_str;
			        }
			    }
			} */
			else if($status==1){
			    /*活动收藏*/
			    $result = get_event_collect($limit, $user_id);
			    $list=$result['list'];
			    foreach($list as $k=>$v)
			    {
			        $tmp_url_param['data_id']=$v['id'];
			        $list[$k]['icon'] = get_abs_img_root(get_spec_image($v['img'],280,280,1));
			        $list[$k]['url']=SITE_DOMAIN.wap_url("index","event",$tmp_url_param);
			        if($v['submit_end_time']==0 && $v['event_end_time']==0){
			            $list[$k]['sheng_time_format']= "永久";
			        }
			        elseif($v['submit_end_time']-NOW_TIME<0 && $v['submit_end_time']!=0){
			            $list[$k]['sheng_time_format']="已结束";
			            $list[$k]['out_time']=1;
			        }elseif ($v['event_end_time']-NOW_TIME<0 && $v['event_end_time']!=0){
			            $list[$k]['sheng_time_format']="已结束";
			            $list[$k]['out_time']=1;
			        }
			        else{
			            if($v['submit_end_time']<$v['event_end_time'] || $v['event_end_time']==0){
			                $list[$k]['sheng_time_format']= to_date($v['submit_end_time']-NOW_TIME,"d天h小时i分");
			            }else {
			                $list[$k]['sheng_time_format']= to_date($v['event_end_time']-NOW_TIME,"d天h小时i分");
			            }
			        }
			        
			    }
			    
			}
			else if($status==2){
			    /*订餐店铺收藏*/
			    require_once(APP_ROOT_PATH . "system/model/supplier.php");
			    //获取定位
			    $geo=$GLOBALS['geo'];
			    if(	$GLOBALS['request']['from']=='wap'){
    			    $ypoint =  $geo['ypoint'];  //ypoint
    			    $xpoint =  $geo['xpoint'];  //xpoint
			    
			    }else{
			        $ypoint = $GLOBALS['request']['ypoint'];  //ypoint
			        $xpoint = $GLOBALS['request']['xpoint'];  //xpoint
			    }
			    if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
			    {
			        $pi = PI;  //圆周率
			        $r = EARTH_R;  //地球平均半径(米)
			    }
			    
			    $result = get_dc_location_collect($limit,$user_id,$geo,$ypoint,$xpoint,$pi,$r);
			    $list=$result['list'];
			    foreach($list as $k=>$v)
			    {
			        //获取商圈，订餐分类
			        $dc_cate_name= get_location_dc_cate_name($v['id']);
			        $dc_area_name= get_location_area_name($v['id']);
			        
			        $tmp_url_param['data_id']=$v['id'];
			        $list[$k]['preview'] = get_abs_img_root(get_spec_image($v['preview'],280,280,1));
			        $list[$k]['url']=SITE_DOMAIN.wap_url("index","dc_location",$tmp_url_param);
			        $list[$k]['add_time'] = to_date($v['add_time'],"Y-m-d");
                    $list[$k]['cate_name'] = $dc_cate_name[$v['id']];
                    $list[$k]['area_name'] = $dc_area_name[$v['id']];
                    $list[$k]['format_point'] = ($v['avg_point'] / 5) * 100;
                    $list[$k]['avg_point'] = sprintf('%.1f', round($v['avg_point'], 1));
			        //定位数据处理
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
			        $list[$k]['distance'] = $distance_str;
			    }
			     
			}
			
			//print_r($list);exit;
			
			//分页
			$count=$result['count'];
			$page_total = ceil($count/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
			
	    }
	    $root['user_login_status'] = $user_login_status;
	    $root['item'] = $list;
	    $root['sc_status'] = $status;
	    $root['page_title'] = "我的收藏";
	    //print_r($root);exit;
	    return output($root);
	}
	public function del_collect()
	{
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    $id=$GLOBALS['request']['id'];
	    $type=$GLOBALS['request']['type'];
	    //logger::write(print_r($type,1));

	    $user_login_status = check_login();
	    
	    if($user_login_status==LOGIN_STATUS_NOLOGIN){
	        $root['user_login_status'] = $user_login_status;
	        $root['jump'] = wap_url("index","user#login");
	        $status = 0;
	        $info = '请先登录';
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	    }
	    
	    //判断收藏类型
	    if($type=='deal'){
	        $sql="delete from  ".DB_PREFIX."deal_collect where user_id=".$user_id." and deal_id in(".$id.")";
	    }
	    elseif ($type=='youhui'){
	        $sql="delete from  ".DB_PREFIX."youhui_sc where uid=".$user_id." and youhui_id in(".$id.")";
	    }
	    elseif($type == 'event'){
	        $sql="delete from  ".DB_PREFIX."event_sc where uid=".$user_id." and event_id in(".$id.")";
	    }
	    else{//收藏订单预留
	        $sql="delete from  ".DB_PREFIX."dc_location_sc where user_id=".$user_id." and location_id in(".$id.")";
	    }
	    
	    //取消收藏处理
	    if($sql){
	        $GLOBALS['db']->query($sql);
	        if($GLOBALS['db']->affected_rows()>0){
	            $root['is_collect'] = 1;
	            $info = "取消成功";
	            $status = 1;
	        }else{
	            $root['is_collect'] = 0;
	            $info = "操作失败";
	            $status = 0;
	        } 
	    }
	    return output($root,$status,$info);
	}
}
?>