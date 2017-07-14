<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class storeApiModule extends MainBaseApiModule
{
	
	
	/**
	 * 商家详细页
	 * 输入：data_id 门店id
	 * 无
	 * 

	 * store_info:object 门店信息
	 * 结构如下
		Array
		(
		    [preview] => http://localhost/o2onew/public/attachment/201502/25/14/54ed67b2cd14b_388x236.jpg [string] 展示图：300x182
		    [id] => 21
		    [share_url] => [string] 分享链接
		    [supplier_id] => 23
		    [is_verify] => 0
		    [avg_point] => 5.0000
		    [address] => 台江区宝龙万象城4楼391号
		    [name] => 桥亭活鱼小镇（万象城店）
		    [tel] => 059188855588
		    [brief] => <p align="center"><br />			
		    [store_images] => Array
		        (
		            [0] => Array
		                (
		                    [brief] => 
		                    [image] => http://localhost/o2onew/public/attachment/201502/25/14/54ed6a9a856ba.jpg [string]图集： 300x182
		                )
		        )
		     [xpoint] => float 经度
		     [ypoint] => float 纬度
		
		)
		
	 * other_supplier_location:array 其它门店
	 * 结构如下		
		Array
        (
            [0] => Array
                (
                    [preview] => http://localhost/o2onew/public/attachment/201502/25/14/54ed67b2cd14b_388x236.jpg [string]其它门店展示图： 150x84
                    [id] => 22
                    [is_verify] => 0
                    [avg_point] => 0.0
                    [address] => 晋安区新店镇五四北泰禾广场六楼（中影影院旁，音乐-百度KTV旁边）
                    [name] => 桥亭活鱼小镇（泰禾广场店）
                    [distance] => 0
                )
       )         
	 * tuan_list:array 团购列表
	 * 结构如下
	 * Array
        (
            [0] => Array
                (
                    [id] => 74 [int] 团购ID
                    [name] => 仅售75元！价值100元的镜片代金券1张，仅适用于镜片，可叠加使用。[string] 团购名称
                    [sub_name] => 镜片代金券 [string] 团购短名称
                    [brief] => 【36店通用】明视眼镜 [string] 团购简介
                    [buy_count] => 1 [int] 销量
                    [current_price] => 75 [float] 现价
                    [origin_price] => 100 [float] 原价
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9d05a1020_150x84.jpg [string] 团购图片 150x84
                    [end_time_format] => 2017-02-28 18:00:08 [string] 格式化的结束时间
                    [begin_time_format] => 2015-02-25 18:00:10 [string] 格式化的开始时间
                    [begin_time] => 1424829610 [int] 开始时间戳
                    [end_time] => 1488247208 [int] 结束时间戳
                    [auto_order] => 1 [int] 免预约 0:否 1:是
                    [is_lottery] => 1 [int] 是否抽奖 0:否 1:是
                    [distance]	=>	[float] 有地理定位时的离当前地的距离(米)
                    [xpoint] => [float] 团购所在经度
                    [ypoint] => [float] 团购所在纬度
                    [is_today] => [int] 是否为今日团购 0否 1是
                )
       )
	 * deal_list:array 商城商品列表
	 * 结构如下
	 * Array
        (
            [0] => Array
                (
                    [id] => 74 [int] 商品ID
                    [name] => 仅售75元！价值100元的镜片代金券1张，仅适用于镜片，可叠加使用。[string] 商品名称
                    [sub_name] => 镜片代金券 [string] 商品短名称
                    [brief] => 【36店通用】明视眼镜 [string] 商品简介
                    [buy_count] => 1 [int] 销量
                    [current_price] => 75 [float] 现价
                    [origin_price] => 100 [float] 原价
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9d05a1020_150x84.jpg [string] 商品图片 150x84
                    [end_time_format] => 2017-02-28 18:00:08 [string] 格式化的结束时间
                    [begin_time_format] => 2015-02-25 18:00:10 [string] 格式化的开始时间
                    [begin_time] => 1424829610 [int] 开始时间戳
                    [end_time] => 1488247208 [int] 结束时间戳
                    [is_refund] => 1 [int] 是否随时退 0:否 1:是
                )
       )
	 * event_list:array 活动列表
	 * 结构如下
	 * Array
       (
            [0] => Array
                (
                    [id] => 4 [int] 活动ID
                    [name] => 贵安温泉自驾游 [string] 活动名称
                    [icon] => http://localhost/o2onew/public/attachment/201502/26/14/54eec33c40e99_600x364.jpg [string] 活动图片 300x182
                    [submit_begin_time_format] => 2015-02-01 14:54:53 [string] 格式化活动报名开始时间
                    [submit_end_time_format] => 2020-02-26 14:54:55 [string] 格式化活动报名结束时间
                    [sheng_time_format] => 06天04小时50分 [string] 活动报名剩余时间
                )
       )
	 * youhui_list:array 优惠列表
	 * Array
        (
            [0] => Array
                (
                    [id] => 23 [int] 优惠券ID
                    [name] => 华莱士30元抵用券 [string] 优惠券名称
                    [list_brief] => 华莱士30元抵用券 [string] 优惠券列表简介
                    [icon] => http://localhost/o2onew/public/attachment/201502/26/11/54ee8fc5497f9_150x84.jpg [string] 优惠券图片 150x84
                    [down_count] => 4 [int] 下载量
                    [begin_time] => 2015-02-01至2020-02-26 [string] 时间
                )
       )
     *
     *
     * dp_list 评论列表
     * Array 
       (
        [0] => Array
        (
            [id] => '7' [int] 评论id
            [create_time] => '2013-04-26' [string] 评论时间
            [content] => '垃圾地方，老板坑人，两个人去吃硬要给我们2斤8两的鱼，一大半都没吃，最关键的没吃过这么难吃的酸菜鱼。去吃保证会后悔' [string] 评论内容
            [reply_content] => '' [string] 回复内容
            [point] => '1' [int] 评论分数
            [user_name] => 'z3074219' [string] 评论的用户名
            [images] => 
                Array (
                  [0] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/fe7b2a5dc01d7c82f5197448c160d2ee58_120x120.jpg' [string] 评论图片 60x60
                  [1] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/d66685328e8b42c0d38cb7461ba78c6151_120x120.jpg' [string] 评论图片 60x60
                  [2] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/c0e84dbc56f72e881053ffcb103280fe31_120x120.jpg' [string] 评论图片 60x60
                )
            [images_v1' => 
                Array (
                  [0] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/fe7b2a5dc01d7c82f5197448c160d2ee58_200x200.jpg' [string] 评论图片 50x50
                  [1] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/d66685328e8b42c0d38cb7461ba78c6151_200x200.jpg' [string] 评论图片 50x50
                  [2] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/c0e84dbc56f72e881053ffcb103280fe31_200x200.jpg' [string] 评论图片 50x50
                )
            [oimages' => 
                Array (
                  [0] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/fe7b2a5dc01d7c82f5197448c160d2ee58.jpg' [string] 评论图片 原图
                  [1] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/d66685328e8b42c0d38cb7461ba78c6151.jpg' [string] 评论图片 原图
                  [2] => 'http://192.168.3.148/fwshop/public/comment/201510/25/06/c0e84dbc56f72e881053ffcb103280fe31.jpg' [string] 评论图片 原图
                )
          )
        )
     *
     * location_list 推荐商家
     * Array (
          [0 => 
              Array (
                [preview] => 'http://192.168.3.148/fwshop/public/attachment/201601/29/11/56aada5615140_360x330.jpg' [string] 门店展示图片 92x82
                [preview_v1] => 'http://192.168.3.148/fwshop/public/attachment/201601/29/11/56aada5615140_360x260.jpg' [string] 门店展示图片 90x65
                [preview_v2] => 'http://192.168.3.148/fwshop/public/attachment/201601/29/11/56aada5615140_128x128.jpg' [string] 门店展示图片 64x64
                [id] => [23] [string] 门店id
                [is_verify] => 0 [int] 是否认证
                [avg_point] => '5.0000' [float] 评论的平均分数
                [address] => '嵊州市三江街道世贸广场27、29号（新国商北侧，中行边）' [string] 门店地址
                [name] => '金樽人家' [string] 门店名称
                [distance] => '' [string] 当前位置与门店距离（米）
                [xpoint] => [float] 门店所在经度
                [ypoint] => [float] 门店所在纬度
                [tel] => '0575-83178977' [string] 门店电话
                [dealcate_name' => '酒店' [string] 门店分类名称
                [area_name' => NULL  [string] 门店地区名称
              )
         )
      
	 * page_title:string 页面标题
	 * 
	 */
	public function index()
	{
		$root = array();
		$root['status'] = 1;
		$root['info'] = '';

		$store_id = intval($GLOBALS['request']['data_id']);//门店ID
		
		
		
		require_once(APP_ROOT_PATH."system/model/supplier.php");
		$store_info = get_location($store_id);
		$supplier_id = $store_info['supplier_id'];

	    if($store_info){
            $root['id'] = $store_info['id'];
        }else{
            return output($root,0,"门店数据未找到");
        }

		// 门店实体券+电子券
		$sql = "select y.* from fanwe_youhui as y left join fanwe_youhui_location_link as yll on yll.youhui_id = y.id where ( ( y.begin_time = 0 or y.begin_time < ".NOW_TIME." )
		and ( y.end_time = 0 or y.end_time > ".NOW_TIME." ) ) and (y.total_num =0 or (y.total_num>y.user_count) ) and y.is_effect = 1
and y.supplier_id = ".$supplier_id." and ( ( y.youhui_type = 1 and yll.location_id = ".$store_id." ) OR y.youhui_type = 2 ) order by y.create_time desc,y.id desc";
		$root['youhui_data'] = $GLOBALS['db']->getAll($sql);

        // 获取商户优惠信息
        $store_info['promotes'] = array();
        if ($store_info['open_store_payment'] == 1) {
            $promotes = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."promote where supplier_id=".$store_info['supplier_id']);
			// 优惠信息
    		foreach ($promotes as $k=>$v){
				if($v['description']==''){
					$promotes[$k]['config']=unserialize($v['config']);
					$promotes[$k]['description']='每满'.$promotes[$k]['config']['discount_limit'].'元减'.$promotes[$k]['config']['discount_amount'].'元';
				}
    		}
            $store_info['promotes'] = $promotes;
        }

		//商户图库
		$store_images = $GLOBALS['db']->getAll("select brief,image from ".DB_PREFIX."supplier_location_images where supplier_location_id = ".$store_id." and status = 1 order by sort limit ".MAX_SP_IMAGE);
	 
		foreach($store_images as $k=>$v)
		{
			$store_images[$k]['image'] = get_abs_img_root(get_spec_image($v['image'],300,182));
		}
		$store_info['store_images'] = $store_images;
		
		//is_auto_order 1:手机自主下单;消费者(在手机端上)可以直接给该门店支付金额
		$store_info['is_auto_order'] = 0;
		$root['store_info'] = format_store_item($store_info);
		//标签
    	$root['store_info']['tags']= array_filter(explode(" ", $root['store_info']['tags']));
		$root['store_info']['brief']=$root['store_info']['mobile_brief'];
		//其它门店
		$ext_condition = " supplier_id = ".$store_info['supplier_id']." and id != ".$store_id;
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
	    
	    $result = get_location_list(50, array(), '', $ext_condition, '', $field_append);
	    
	    $indexs_supplier_rs = $result['list'];
	    foreach($indexs_supplier_rs as $k=>$v){
	        $indexs_supplier_rs[$k] = format_store_list_item($v);
	    }
	    $root['other_supplier_location'] = $indexs_supplier_rs?$indexs_supplier_rs:array();

		require_once(APP_ROOT_PATH."system/model/deal.php");
		
		//门店团购
		$result = get_deal_list(3,array(DEAL_ONLINE,DEAL_NOTICE),array("city_id"=>intval($GLOBALS['city']['id']))," left join ".DB_PREFIX."deal_location_link as l on d.id = l.deal_id "," d.buy_type <> 1 and d.is_shop = 0 and d.is_location = 1 and l.location_id =".$store_id);
		$indexs_deal = $result['list'];
		foreach($indexs_deal as $k=>$v){
			$indexs_deal[$k] = format_deal_list_item($v);
		}
		
		$root['tuan_list']=$indexs_deal?$indexs_deal:array();
		$root['tuan_count']= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON d.id = l.deal_id where ".$result['condition']);
		
		//门店商品
		$result = get_goods_list(3,array(DEAL_ONLINE,DEAL_NOTICE),array()," left join ".DB_PREFIX."deal_location_link as l on d.id = l.deal_id "," d.buy_type <> 1 and d.is_shop = 1 and l.location_id =".$store_id);
		$indexs_deal = $result['list'];
		foreach($indexs_deal as $k=>$v){
			$indexs_deal[$k]=format_deal_list_item($v);
		}		
		
		$root['deal_list'] = $indexs_deal?$indexs_deal:array();
		$root['deal_count']= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d LEFT JOIN ".DB_PREFIX."deal_location_link AS l ON d.id = l.deal_id where ".$result['condition']);
		
		//门店活动
		require_once(APP_ROOT_PATH."system/model/event.php");
		$result = get_event_list(10,array(EVENT_NOTICE,EVENT_ONLINE),array("city_id"=>intval($GLOBALS['city']['id'])),""," l.location_id = ".$store_id);
		$indexs_event_rs = $result['list'];
		foreach($indexs_event_rs as $k=>$v){
			$indexs_event[$k] = format_event_list_item($v);
		}
		$root['event_list'] = $indexs_event?$indexs_event:array();
		
		//门店优惠券
		$sql = "select y.id,y.youhui_value,y.start_use_price,y.valid_type,y.expire_day,y.use_end_time from fanwe_youhui as y left join fanwe_youhui_location_link as yll on yll.youhui_id = y.id
					where ( ( y.begin_time = 0 or y.begin_time < ".NOW_TIME." )
					and ( y.end_time = 0 or y.end_time > ".NOW_TIME." ) ) and (y.total_num =0 or (y.total_num>y.user_count) ) and y.is_effect=1
					and y.supplier_id = ".$supplier_id." and ( ( y.youhui_type = 1 and yll.location_id = ".$store_id." ) OR y.youhui_type = 2 ) order by y.create_time desc,y.id desc";
		$youhui_list = $GLOBALS['db']->getAll($sql);

		foreach($youhui_list as $k => $v){
			if($v['valid_type']==1){ //固定天数（0为永不过期）
				if($v['expire_day']==0){
					$youhui_list[$k]['use_end_time']= '永久有效';
				}else{
					$youhui_list[$k]['use_end_time']= '领取之日起'.$v['expire_day'].'天有效';
				}
			}
			else if($v['valid_type']==2){ //固定日期（0为永不过期）
				if($v['use_end_time']==0){
					$youhui_list[$k]['use_end_time']= '永久有效';
				}
				else{
					$youhui_list[$k]['use_end_time'] = '截止'.to_date($v['use_end_time'],'Y-m-d H:i');
				}
			}
		}
		$root['youhui_list'] = $youhui_list?$youhui_list:array();
		
		/*点评数据*/
		require_once(APP_ROOT_PATH."system/model/review.php");
	    require_once(APP_ROOT_PATH."system/model/user.php");
	    
	    /*获点评数据*/
	    $dp_list = get_dp_list(5,$param=array("location_id"=>$store_id),"","");
	    $format_dp_list = array();
	    
	    
	    foreach($dp_list['list'] as $k=>$v){
	    
	        $temp_arr = array();
	         
	        $temp_arr['id'] = $v['id'];
	        $temp_arr['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"small"))?get_abs_img_root(get_muser_avatar($v['user_id'],"small")):"";
	        $temp_arr['create_time'] = $v['create_time'] > 0 ?to_date($v['create_time'],'Y-m-d'):'';
	        $temp_arr['content'] = $v['content'];
	        //$temp_arr['reply_content']= $v['reply_content']?$v['reply_content']:'';
	        $temp_arr['point'] = $v['point'];
	    
	        $uinfo = load_user($v['user_id']);
	        $temp_arr['user_name'] = $uinfo['user_name'];
	    
	    
	    
	        $images = array();
	        $images_v1 = array();
	        $oimages = array();
	    
	        if($v['images']){
	            foreach ($v['images'] as $ik=>$iv){
	                $images[] = get_abs_img_root(get_spec_image($iv,60,60,1));
	                $images_v1[] = get_abs_img_root(get_spec_image($iv, 50, 50,1));
	                $oimages[] = get_abs_img_root($iv);
	            }
	             
	        }
	        $temp_arr['images'] = $images;
	        $temp_arr['images_v1'] = $images_v1;
	        $temp_arr['oimages'] = $oimages;
	    
	    
	        $format_dp_list[] = $temp_arr;
	    }
	    $root['dp_list'] = $format_dp_list;
	    $root['store_info']['dp_count']=count($format_dp_list);
	    /* 推荐商家 */
	    //缓存下来的地区配置
	    $area_data = load_auto_cache("cache_area",array("city_id"=>$store_info['city_id']));
	    $city_id = intval($GLOBALS['city']['id']);//城市分类ID
	    $quan_id_l = intval($GLOBALS['request']['qid']); //商圈id
	    $area_id_l = intval($area_data[$quan_id_l]['pid']); //大区id
	    if($area_id_l ==0 && $quan_id_l>0){
	        $area_id = $quan_id = intval($GLOBALS['request']['qid']); //商圈id
	    }else{
	        $quan_id = intval($GLOBALS['request']['qid']); //商圈id
	        $area_id = intval($area_data[$quan_id_l]['pid']); //大区id
	    }
	    //门店商圈，显示前10个
	    $store_area = $GLOBALS['db']->getAll("select area_id from ".DB_PREFIX."supplier_location_area_link where location_id = ".$store_id);
	    
	    foreach ($store_area as $k=>$v){
	    	$store_area_s[$v['area_id']]=$area_data[$v['area_id']];
	    }
	    
	    $root['store_info']['store_area']=array_values($store_area_s);
		//门店商圈
		foreach ($root['store_info']['store_area'] as $k=>$v){
			$area_str.="".$v['name']."/";
		}
		$root['store_info']['area_str']=(rtrim($area_str,"/")!=""?rtrim($area_str,"/"):"无")."商圈";
	    
	    $ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
	    $ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
	    $xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
	    $xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
	    $ypoint =  $m_latitude = $GLOBALS['geo']['ypoint'];  //ypoint
	    $xpoint = $m_longitude = $GLOBALS['geo']['xpoint'];  //xpoint
	    
	    $ext_condition = '';
	    if($xpoint>0) 
	    {
	        $pi = PI;  //圆周率
	        $r = EARTH_R;  //地球平均半径(米)
	        $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
	        	
	        if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
	        {
	            if($ext_condition!="")
	                $ext_condition.=" and ";
	            $ext_condition.= " sl.ypoint > $ybottom and sl.ypoint < $ytop and sl.xpoint > $xleft and sl.xpoint < $xright ";
	    
	            $limit = 300;
	        }
	        $order = " distance asc, ";
	    }
	    else
	        $order = "";
	  
	    $condition_param = array("aid"=>$area_id,"qid"=>$quan_id,"city_id"=>$city_id);
	    $order .= " sl.is_recommend desc, sl.good_rate desc, sl.avg_point desc, sl.is_verify desc ";
	    
	    // 设置多表链接
		$join .= ' LEFT JOIN '.DB_PREFIX.'deal_cate Dealcate ON Dealcate.id = sl.deal_cate_id ';
		$join .= ' LEFT JOIN '.DB_PREFIX.'supplier_location_area_link slal ON sl.id = slal.location_id';
		$join .= ' LEFT JOIN '.DB_PREFIX.'area area ON area.id = slal.area_id';
		
		$field_append .= ", Dealcate.name as dealcate_name, slal.area_id as area_id,  area.name as area_name ";
		$group_by = " GROUP BY  sl.id ";
	    
	    require_once(APP_ROOT_PATH."system/model/supplier.php");
	    if($ext_condition!=""){
	       $ext_condition .= ' and sl.id <> '.$store_id.' ';
	    }else{
	        $ext_condition .= ' sl.id <> '.$store_id.' ';
	    }
	    $location_result = get_location_promote_list(5,$condition_param, $join,$ext_condition, $group_by, $order,$field_append);
	    
	    foreach($location_result['list'] as $k=>$v) 
	    {
	        $location_list[$k] = format_store_list_item($v);
	    }
	    $root['location_list'] = $location_list;
		
        $root['store_info_non_existent']="店家比较懒,什么都没有留下";
		$root['page_title']=$store_info['name'];
		//print_r($root);exit;
		return output($root);
	}
	
	public function tuan()
	{
	    $root = array();
	    $root['status'] = 1;
	    $root['info'] = '';
	
	    $store_id = intval($GLOBALS['request']['data_id']);//门店ID

	    require_once(APP_ROOT_PATH."system/model/supplier.php");
	    $store_info = get_location($store_id);
	
	    if($store_info){
	        $root['id'] = $store_info['id'];
	    }else{
	        return output($root,0,"门店数据未找到");
	    }

	    
	    require_once(APP_ROOT_PATH."system/model/deal.php");
	
	    //门店团购
	    $result = get_deal_list(50,array(DEAL_ONLINE,DEAL_NOTICE),array()," left join ".DB_PREFIX."deal_location_link as l on d.id = l.deal_id "," d.buy_type <> 1 and d.is_shop = 0 and d.is_location = 1 and l.location_id =".$store_id);
	    $indexs_deal = $result['list'];
	    foreach($indexs_deal as $k=>$v){
	        $indexs_deal[$k] = format_deal_list_item($v);
	    }
	
	    $root['tuan_list']=$indexs_deal?$indexs_deal:array();;
	   
	    $name= implode($GLOBALS['db']->getRow("select name from ".DB_PREFIX."supplier_location where id=".$store_id));
	    $root['page_title']=$name."-"."团购";
	    //print_r($root);exit;
	    return output($root);
	}
	
	
	/**
	 * 商品列表接口
	 * 输入：
	 * data_id: int 商店ID
	 * cate_id: int 商品分类ID
	 * page:int 当前的页数
	 * order_type: string 排序类型(default(默认)/nearby(离我)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))
	 *
	 *
	 *
	 * 输出：
	 * cate_id:int 当前分类ID
	 * bid:int 当前品牌ID
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * deal_list:array:array 商品列表，结构如下
	 *  Array
	 (
	 [0] => Array
	 (
	 [id] => 74 [int] 商品ID
	 [name] => 仅售75元！价值100元的镜片代金券1张，仅适用于镜片，可叠加使用。[string] 商品名称
	 [sub_name] => 镜片代金券 [string] 商品短名称
	 [brief] => 【36店通用】明视眼镜 [string] 商品简介
	 [buy_count] => 1 [int] 销量
	 [current_price] => 75 [float] 现价
	 [origin_price] => 100 [float] 原价
	 [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9d05a1020_140x85.jpg [string] 团购图片 140x85
	 [end_time_format] => 2017-02-28 18:00:08 [string] 格式化的结束时间
	 [begin_time_format] => 2015-02-25 18:00:10 [string] 格式化的开始时间
	 [begin_time] => 1424829610 [int] 开始时间戳
	 [end_time] => 1488247208 [int] 结束时间戳
	 [is_refund] => [int] 随时退 0:否 1:是
	 [buyin_app] => 0
	 )
	 )
	 * cate_list:array 大类列表
	 * 结构如下
	 * Array(
	 * 		Array
	 (
	 [id] => 0 [int]分类ID
	 [name] => 全部分类 [string] 分类名
	 [iconfont]=> [string] wap端使用的iconfont代码
	 [iconcolor]=> #f0f0f0 [string] 颜色配置 16进度
	 [bcate_type] => Array
	 (
	 [0] => Array
	 (
	 [id] => 0 [int]小分类ID
	 [cate_id] => 0 [int]父分类ID
	 [name] => 全部分类 [string] 分类名称
	 )
	
	 )
	
	 )
	 )
	 *
	 */
	public function shop()
	{
	    $root = array();
	    $root['status'] = 1;
	    $root['info'] = '';
	    
	    $catalog_id = intval($GLOBALS['request']['cate_id']);//商品分类ID
	    $page = intval($GLOBALS['request']['page']); //分页
	    $page=$page==0?1:$page;
	    $order_type=strim($GLOBALS['request']['order_type']);
	    $store_id = intval($GLOBALS['request']['data_id']);//门店ID
	    
	    $page_size = PAGE_SIZE;
	    $limit = (($page-1)*$page_size).",".$page_size;
	    require_once(APP_ROOT_PATH."system/model/supplier.php");
	    $store_info = get_location($store_id);
	    if($store_info){
	        $root['id'] = $store_info['id'];
	    }else{
	        return output($root,0,"门店数据未找到");
	    }
	    
	    //分类输出
	    $store_cate=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."shop_cate where id in(select distinct shop_cate_id from ".DB_PREFIX."deal where id in (select deal_id from ".DB_PREFIX."deal_location_link where location_id=".$store_info['id'].") )");
	    //logger::write(print_r($store_cate,1));
	    $cate_id=array();
	    foreach ($store_cate as $t => $v){
	        $cate_id[]=$v['id'];
	    }
	    $cate_list = array(
	        array(
	            "id"	=>	0,
	            "name"	=>	"全部分类",
	            "iconfont"	=>	"",
	            "iconcolor"	=>	"",
	            "bcate_type"	=>	array(
	                array(
	                    "id"	=>	0,
	                    "cate_id"	=>	0,
	                    "name"	=>	"全部分类"
	                )
	            )
	        )
	    );
	    foreach ($store_cate as $t => $v){
	        if($v['pid']==0){
	            $cate['id'] = $v['id'];
    	        $cate['name'] = $v['name']?$v['name']:"";
    	        $cate['iconfont'] = $v['iconfont']?$v['iconfont']:"";
    	        $cate['iconcolor'] = $v['iconcolor']?$v['iconcolor']:"";
    	        $cate['cate_img'] = $v['cate_img']?$v['cate_img']:"";
    	        $cate['bcate_type']	= array(
    	            array(
    	                "id"	=>	$v['id'],
    	                "cate_id"	=>	$v['id'],
    	                "name"	=>	"全部"
    	            )
    	        );
    	        foreach ($store_cate as $tt => $vv){
	                if(in_array($vv['pid'],$cate_id) && $vv['pid']==$cate['id']){
	                    $bcate_type = array();
	                    $bcate_type['id']	=	$vv['id'];
	                    $bcate_type['cate_id'] = $vv['pid']?$vv['pid']:"";
	                    $bcate_type['name'] = $vv['name']?$vv['name']:"";
	                    $bcate_type['cate_img'] = $vv['cate_img']?$vv['cate_img']:"";
	                    $cate['bcate_type'][] = $bcate_type;
	                }
    	        }
    	        $cate_list[] = $cate;
	        }else if ($v['pid']!=0 && !in_array($v['pid'],$cate_id)){
	            $i=0;
	            foreach ($cate_list as $ttt => $vvv){
	                
	                if($vvv['id']==$v['pid']){
	                    $bcate_type = array();
	                    $bcate_type['id']	=	$v['id'];
	                    $bcate_type['cate_id'] = $v['pid']?$v['pid']:"";
	                    $bcate_type['name'] = $v['name']?$v['name']:"";
	                    $bcate_type['cate_img'] = $v['cate_img']?$v['cate_img']:"";
	                    $cate_list[$ttt]['bcate_type'][] = $bcate_type;
	                    break;
	                }
	                $i++;
	            }
	            if($i>=count($cate_list)){
    	            $pcate=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where id=".$v['pid']);
    	            $cate['id'] = $pcate['id'];
    	            $cate['name'] = $pcate['name']?$pcate['name']:"";
    	            $cate['iconfont'] = $pcate['iconfont']?$pcate['iconfont']:"";
    	            $cate['iconcolor'] = $pcate['iconcolor']?$pcate['iconcolor']:"";
    	            $cate['cate_img'] = $pcate['cate_img']?$pcate['cate_img']:"";
    	            $cate['bcate_type']	= array(
    	                array(
    	                    "id"	=>	$pcate['id'],
    	                    "cate_id"	=>	$pcate['id'],
    	                    "name"	=>	"全部"
    	                )
    	            );
    	            $bcate_type = array();
    	            $bcate_type['id']	=	$v['id'];
    	            $bcate_type['cate_id'] = $v['pid']?$v['pid']:"";
    	            $bcate_type['name'] = $v['name']?$v['name']:"";
    	            $bcate_type['cate_img'] = $v['cate_img']?$v['cate_img']:"";
    	            $cate['bcate_type'][] = $bcate_type;
    	            $cate_list[] = $cate;
	            }
	        }
	    }
	    
	    //获取商品信息
	    $ext_condition = "d.id in (select deal_id from ".DB_PREFIX."deal_location_link where location_id=".$store_info['id'].") and d.buy_type <> 1 and d.is_shop = 1 ";
	    
	    $order = "";
	    /*排序
	           智能排序和 离我最的 是一样的 都以距离来升序来排序，只有这两种情况有传经纬度过来，就没有把 这两种情况写在 下面的判断里，写在上面了。
	     default 智能（默认），nearby  离我，avg_point 评价，newest 最新，buy_count 人气，price_asc 价低，price_desc 价高 */
	    if($order_type=='avg_point')/*评价*/
	        $order= " d.avg_point desc  ";
	    elseif($order_type=='newest')/*最新*/
	    $order= " d.create_time desc  ";
	    elseif($order_type=='buy_count')/*销量*/
	    $order= " d.buy_count desc  ";
	    elseif($order_type=='price_asc')/*价格升*/
	    $order= " d.current_price asc  ";
	    elseif($order_type=='price_desc')/*价格降*/
	    $order= " d.current_price desc  ";
	    
	    $condition_param=array();
	    if ($catalog_id){
	       $condition_param = array("cid"=>$catalog_id);
	    }
	    require_once(APP_ROOT_PATH."system/model/deal.php");
	    $result  = get_goods_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),$condition_param,"",$ext_condition,$order);
	    $indexs_deal = $result['list'];
	    foreach($indexs_deal as $k=>$v){
	        $indexs_deal[$k]=format_deal_list_item($v);
	    }
	    $root['deal_list'] = $indexs_deal?$indexs_deal:array();
	    
	    //分页的计算
	    $list = $result['list'];
	    $count_deal= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d where ".$result['condition']);

	    
	    $page_total = ceil($count_deal/$page_size);
	    
	    
	    //分类个数的计算
	    if(APP_INDEX=="wap_index"||APP_INDEX=="wap"||1){
			
			//$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
			
			$time = NOW_TIME;
			
			$condition = 'select shop_cate_id , count(*) as shop_cate_count from '.DB_PREFIX."deal where";
			
			$condition .= ' is_effect = 1 and is_delete = 0 and buy_type=0 and is_shop=1 and id in (select deal_id from '.DB_PREFIX.'deal_location_link where location_id='.$store_info['id'].') and (';
			
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
		    //二级分类的个数计算
		    foreach($cate_list as $k=>$v ){
		        $cate_list[$k]['active'] = 0;
		    
		        if($catalog_id == $v['id'] ){
		            $cate_list[$k]['active'] = 1;
		        }
		    
		        foreach($v['bcate_type'] as $kk=>$vv ){
		            $cate_id = $vv['id'];
		            $cate_list[$k]['bcate_type'][$kk]['count'] = intval($shop_cate_count[$cate_id]['shop_cate_count']);
		            $cate_list[$k]['bcate_type'][$kk]['active'] =0;
		            if($cate_id==$catalog_id){
		                $cate_list[$k]['bcate_type'][$kk]['active'] =1;
		                $cate_list[$k]['active'] =1;
		            }
		        }
		    }
		    
		    //一级分类的个数计算
		    foreach($cate_list as $k=>$v ){
		        $count=0;
		        foreach($v['bcate_type'] as $kk=>$vv ){
		            $count += $vv['count'];
		        }
		        //$count += intval($shop_cate_count[$v['id']]['shop_cate_count']);
		        $cate_list[$k]['bcate_type'][0]['count'] = $count;
		    }
		    
		    //全部分类的个数计算

		    $cate_count_all = 'select count(*) from '.DB_PREFIX.'deal as d where  d.is_effect = 1 and d.is_delete = 0 and ( 1<>1  or (('.NOW_TIME.'>= d.begin_time or d.begin_time = 0) and ('.NOW_TIME.'< d.end_time or d.end_time = 0) and d.buy_status <> 2)  or (('.NOW_TIME.' < d.begin_time and d.begin_time <> 0 and d.notice = 1)) )   and d.id in (select deal_id from '.DB_PREFIX.'deal_location_link where location_id='.$store_id.') and d.buy_type <> 1 and d.is_shop = 1';
		    $count = $GLOBALS['db']->getOne($cate_count_all);
		    $cate_list[0]['bcate_type'][0]['count'] = intval($count);
	   
	    }
        /*输出分类名 */
        if(!$catalog_id){
            $root['cate_name']="全部";
        }else{
            $cate_name=$GLOBALS['db']->getRow("select name from ".DB_PREFIX."shop_cate where id=".$catalog_id);
            $root['cate_name']=$cate_name['name'];
        }
		//cate_list
		foreach ($cate_list as $k=>$v){
		    if($v['bcate_type'][0]['count']==0){
		        unset($cate_list[$k]);
		    }else {
		        foreach ($v['bcate_type'] as $kk => $vv){
		            if($vv['count']==0){
		                unset($cate_list[$k]['bcate_type'][$kk]);
		            }
		        }
		        sort($cate_list[$k]['bcate_type']);
		    }
		}
		sort($cate_list);
	    $root['cate_list']=$cate_list?$cate_list:array();
	    
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count_deal);
	    
	    $name= implode($GLOBALS['db']->getRow("select name from ".DB_PREFIX."supplier_location where id=".$store_id));
	    $root['page_title']=$name."-"."商品";
	    return output($root);
	    
	}


	public function reviews()
	{
		$store_id = intval($GLOBALS['request']['data_id']);

		/*点评数据*/
		require_once(APP_ROOT_PATH."system/model/review.php");
	    require_once(APP_ROOT_PATH."system/model/user.php");

	    //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : abs($page);

        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
	    
	    /*获点评数据*/
	    $dp_list = get_dp_list($limit,$param=array("location_id"=>$store_id),"","");
	    $format_dp_list = array();

	    $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'supplier_location_dp WHERE supplier_location_id = '.$store_id;
	    
	    if (!empty($dp_list['list'])) {
	    	$count = $GLOBALS['db']->getOne($sqlCount);
	    	$page_total = ceil($count/$page_size);

	    	$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
	    }
	    foreach($dp_list['list'] as $k=>$v){
	        $format_dp_list[] = format_dp_item($v);
	    }
	    $root['dp_list'] = $format_dp_list;

	    // 获取商家平均评价分数
	    $sql = 'SELECT name, avg_point FROM '.DB_PREFIX.'supplier_location WHERE id='.$store_id;
	    $supplier_info = $GLOBALS['db']->getRow($sql);
	    $supplier_info['avg_point_percent'] = round($supplier_info['avg_point'] / 5 * 100, 2);
	    $supplier_info['avg_point'] = round($supplier_info['avg_point'], 1);
	    $root['supplier_info'] = $supplier_info;
	    
	    $root['page_title'] = $supplier_info['name'].'-'.'全部评价';


	    return output($root);
	}
	
}
?>