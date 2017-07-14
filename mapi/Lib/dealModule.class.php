<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class dealApiModule extends MainBaseApiModule
{
	
	/**
	 * 商品详细页接口
	 * 输入：
	 * data_id: int 商品ID
	 * 
	 * 
	 * 
	 * 输出：
	 *
    	[id] => 73 [int] 商品ID
        [name] => 仅售388元！价值899元的福州明视眼镜单人配镜套餐，含全场599元以内镜框1次+全场300元以内镜片1次。 [string] 商品名称
        [share_url] => [string] 分享链接
        [sub_name] => 明视眼镜  [string] 简短商品名称
        [brief] => 【37店通用】明视眼镜  [string] 简介
        [current_price] => 388  [ float] 当前价格
        [origin_price] => 899  [ float] 原价
        [return_score_show] => 0 [int] 所需要的积分，buy_type为1时显示的价格
        [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9b8e44904_600x364.jpg   [string] 商品缩略图 300X182
        [begin_time] => 1424829400  [string] 开始时间
        [end_time] => 1519782997    [string] 结束时间
        [time_status] => 1  [int] 时间状态  (0 未开始 / 1 可以兑换或者购买 / 2 已经过期)
        [now_time] => 1429125598 [string] 当前时间
        [buy_count] => 7 [int] 销量（购买的件数）
        [buy_type] => 0 [int] 购买类型， 团购商品的类型0：普通 1:积分商品
        [is_shop] => 0 [int] 是否为商城商品0：否 1:是
        [is_collect] =>0 [int ] 是否收藏商品   0：否 1：是
        [is_my_fx]=>0 [int] 是否是我的分销商品    0：否 1：是
        [deal_attr] => Array [array] 商品属性数据
                    [0] => Array
                    (
                        [id] => 17  [int] 属性分类 ID
                        [name] => 时段    [string] 属性名称
                        [attr_list] => Array [array] 属性下的套餐
                            (
                                [0] => Array
                                    (
                                        [id] => 274  [int]套餐编号
                                        [name] => 早上  [string] 套餐名称
                                        [price] => 0.0000 [float] 递增的价格
                                    )
    
                            )
    
                    )
        [avg_point] => 3 [float] 商品点评平均分
        [dp_count] => 5 [int] 点评人数
        [supplier_location_count] => 4  [int] 门店总数
        [last_time] => 90572607 [int] 剩余的秒数
        [last_time_format] => 1048天以上   [string] 剩余的天数 (结束为0)
        [deal_tags] => Array [array] 商品标签
            (
                [0] => Array
                    (
                        [k] => 2  [int] 标签编号
                        [v] => 多套餐 [string] 标签名称
                    )
    
            )
        [images] => Array [array] 商品图集 230X140
        (
            [0] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9b8e44904_460x280.jpg
            [1] => http://localhost/o2onew/public/attachment/201504/17/11/5530793f0e95d_460x280.jpg
            [2] => http://localhost/o2onew/public/attachment/201504/17/11/553079440bbbf_460x280.jpg
        )

        [oimages] => Array  商品图集原图  
        (
            [0] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9b8e44904.jpg
            [1] => http://localhost/o2onew/public/attachment/201504/17/11/5530793f0e95d.jpg
            [2] => http://localhost/o2onew/public/attachment/201504/17/11/553079440bbbf.jpg
        )
        [description]=> <li id="side;"><b>店内部分菜品价格参考</b>[string] 商品详情 HTML 格式
        [notes] => <span style="font-family:ht;background-color:#e2e8eb;">购买须知</span> [string] 购买须知 HTML 格式
       	[xpoint] => [float] 所在经度
        [ypoint] => [float] 所在纬度
        [supplier_location_list] => Array [array] 门店数据列表
        (
            [0] => Array
                (
                    [id] => 35  [int] 门店编号
                    [name] => 明视眼镜（台江万达店）  [string] 门店名称
                    [address] => 台江区鳌江路8号金融街万达广场一层B区37号 [string] 门店地址
                    [tel] => 0591-89800987 [string] 门店联系方式
                    [xpoint] => 经度 [float]
                    [ypoint] => 纬度 [float]
                )

        )
        [dp_list] => Array [array] 点评数据列表
        (
          [4] => Array
                (
                    [id] => 5 [int] 点评数据ID
                    [create_time] => 2015-04-07 [string] 点评时间
                    [content] => 不错不错   [string] 点评内容
                    [reply_content] => 那是不错的了，可以信任的品牌 [string] 管理员回复内容
                    [point] => 5    [int] 点评分数
                    [user_name] => fanwe  [string] 点评用户名称
                    [images] => Array [array] 点评图集 压缩后的图片
                        (
                            [0] => http://localhost/o2onew/public/comment/201504/07/14/bca3b3e37d26962d0d76dbbd91611a6a36_120x120.jpg   [string] 点评图片 60X60
                            [1] => http://localhost/o2onew/public/comment/201504/07/14/5ea94540a479810a7559b5db909b09e986_120x120.jpg   [string] 点评图片 60X60
                            [2] => http://localhost/o2onew/public/comment/201504/07/14/093e5a064c4081a83f31864881bd802061_120x120.jpg   [string] 点评图片 60X60
                        )

                    [oimages] => Array [array] 点评图集 原图
                        (
                            [0] => http://localhost/o2onew/public/comment/201504/07/14/bca3b3e37d26962d0d76dbbd91611a6a36.jpg [string] 点评图片 原图
                            [1] => http://localhost/o2onew/public/comment/201504/07/14/5ea94540a479810a7559b5db909b09e986.jpg [string] 点评图片 原图  
                            [2] => http://localhost/o2onew/public/comment/201504/07/14/093e5a064c4081a83f31864881bd802061.jpg [string] 点评图片 原图
                        )

                )

        ),
        
        
        //当前门店的商品数据
        [other_location_deal]	=>	Array (
              [0] => 
              Array (
                [id] => '3872' [int] 商品ID
                [name] => '【嵊州】巴蜀石锅鱼 仅售199元！价值346元的石锅鱼套餐，建议6-8人使用，提供免费WiFi。' [string] 商品名称
                [sub_name] => '巴蜀石锅鱼199元石锅鱼套餐' [string] 简短商品名称
                [current_price] => 199 [ float] 当前价格
                [origin_price] => 346 [ float] 原价
                [icon] => 'http://192.168.3.148/fwshop/public/attachment/201509/10/15/55f12d3c1721a_180x164.jpg' [string] 商品缩略图 90X82
                [buy_count] => '1' [int] 销量（购买的件数）
              )
        )
        
        // 推荐商品数据
        [recommend_data]	=>	Array (
              [0] => 
              Array (
                [id] => '3872' [int] 商品ID
                [name] => '【嵊州】巴蜀石锅鱼 仅售199元！价值346元的石锅鱼套餐，建议6-8人使用，提供免费WiFi。' [string] 商品名称
                [sub_name] => '巴蜀石锅鱼199元石锅鱼套餐' [string] 简短商品名称
                [current_price] => 199 [ float] 当前价格
                [origin_price] => 346 [ float] 原价
                [icon] => 'http://192.168.3.148/fwshop/public/attachment/201509/10/15/55f12d3c1721a_180x164.jpg' [string] 商品缩略图 90X82
                [buy_count] => '1' [int] 销量（购买的件数）
              )
        )
              
                    
        
        
		//如果该商品有关联商品
		[relate_data]	=>	Array[array]	关联商品数据
			'goodsList'	=>	array(
								//其他字段与主商品一致，增加两个key(属性和库存)
								[stock] => Array(
										[335_337] => Array(
												[id] => 162
												[deal_id] => 64
												[attr_cfg] => Array(
														[19] => 棕色
														[20] => 均码
													)
												[stock_cfg] => 2
												[attr_str] => 棕色均码
												[buy_count] => 0
												[attr_key] => 335_337
											)
									)
								[deal_attr] => Array(
										[0] => Array(
												[id] => 20
												[name] => 尺码
												[attr_list] => Array(
														[0] => Array(
																[id] => 337
																[name] => 均码
																[price] => 3.0000
																[is_checked] => 1
															)
													)
											)
								
										[1] => Array(
												[id] => 19
												[name] => 颜色
												[attr_list] => Array(
														[0] => Array(
																[id] => 335
																[name] => 棕色
																[price] => 1.0000
																[is_checked] => 1
															)
													)
											)
									)
								)
							),
			'dealArray'	=>	array(
								'id'=>array(
									'name'=>'',
									'origin_price'=>'',
									'current_price'=>'',
									'min_bought'=>'',
									'max_bought'=>''
								),
							),
			'attrArray'	=>	array(
								'id'=>array(
									'规格类型'=>array(
										'规格id'=>array(),
									),
								),
							),
			'stockArray'	=>	array(
								'id'=>array(
									'规格类型_规格类型'=>array(),
								),
							),
	  )
      
	 * 
	 */
	public function index()
	{
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
        $data_id = intval($GLOBALS['request']['data_id']);//商品ID


        require_once(APP_ROOT_PATH."system/model/deal.php");
        require_once(APP_ROOT_PATH."system/model/cart.php");
        require_once(APP_ROOT_PATH."system/model/supplier.php");
        $data = get_deal($data_id);

        //统计购物车商品数量
        if($user_id){//wap端会查询3个端都存在的购物车数据，使用只用user_id 来判断
            $cart_num = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_cart where  buy_type = 0 and in_cart=1 and user_id =".$user_id));
        }else{//判断必须是未登录的用户，session_id 来查询
            $cart_num = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_cart where  buy_type = 0 and in_cart=1 and user_id = 0 and session_id ='".es_session::$sess_id."'"));
        }


		//商品描述替换成手机端描述
		$data['description']=get_abs_img_root(format_html_content_image($data['phone_description'], 375,0,true,"wap"));

        $cate_id=$data['cate_id'];
		$return_money=$data['return_money'];
		$return_score=$data['return_score'];
		if($data['id']>0 && $data['is_pick']==0)
		{
		    $join = '';
		    $field_append = '';
		    //开始身边团购的地理定位
		    $geo=$GLOBALS['geo'];
		    $ypoint =  $geo['ypoint'];  //ypoint
		    $xpoint =  $geo['xpoint'];  //xpoint

		    $address = $geo['address'];
		    $sort="";
		    if($xpoint>0)/* 排序（$order_type）  default 智能（默认）*/
		    {
		        $pi = PI;  //圆周率
		        $r = EARTH_R;  //地球平均半径(米)
		        $field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance ";
		        $sort=" distance ";
		    }

			$join .= " left join ".DB_PREFIX."deal_location_link as l on sl.id = l.location_id ";
			$where = " l.deal_id = ".$data['id']." ";
			$locations = get_location_list($data['supplier_location_count'],array("supplier_id"=>$data['supplier_id']),$join,$where, $sort, $field_append);
		}
		elseif($data['is_pick']==1){
		    $locations = get_location_list("",array("supplier_id"=>$data['supplier_id']));   
		}
		else{
			$locations = get_location_list($data['supplier_location_count'],array("supplier_id"=>$data['supplier_id']));
		}
		
		$data['supplier_location_count']=count($locations['list']);
		
		$data = format_deal_item($data);
        //额外参数
        $data['cart_num'] = $cart_num;//购物车数量

		if($data['buy_type']==1){ //积分商品才判断登录状态和会员积分是否足够兑换


		    $user_login_status = check_login();
		    if($user_login_status==LOGIN_STATUS_TEMP)
		    {
		        $user_login_status = LOGIN_STATUS_LOGINED;
		    }
		    $score_button=array();
		    $data['user_login_status'] = $user_login_status;
		    if($data['user_login_status']==1){
		        if( $GLOBALS['user_info']['score'] + $return_score >=0){//足够兑换
		            $score_button['name']='立即兑换';
		            $score_button['status']=1;
		        }else{
		            $score_button['name']='积分不足';
		            $score_button['status']=-1;
		        }
		    }else{
		        $score_button['name']='立即兑换';
		        $score_button['status']=1;
		    }
		    $data['score_button'] = $score_button;
		}

		if($user_id>0){
		    $is_collect = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_collect where user_id = ".$user_id." and deal_id = ".$data_id);
		    if(defined("FX_LEVEL"))
		    $is_my_fx = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_deal where user_id = ".$user_id." and deal_id = ".$data_id);
		}
		$data['is_my_fx'] =$is_my_fx?$is_my_fx:0;
		$data['is_collect'] = $is_collect>0?1:0;

		/*门店数据*/
		$supplier_location_list = array();
		$location_id=0;
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
		        $temp_location['is_main'] = $v['is_main'];
		        if($v['is_main']==1)
		        	$location_id=$v['id'];
		        $supplier_location_list[] = $temp_location;
		    }

		}
		
		$data['supplier_location_list'] = $supplier_location_list;

		if(!$location_id&&$supplier_location_list['0']['id'])
			$location_id=$supplier_location_list['0']['id'];
		if($data['supplier_id']<=0)
			$location_id=0;
		$data["location_id"]=$location_id;
	    sort($data['deal_attr']);
		$data['page_title'] = $data['sub_name'];



		// 获取当前门店的商品数据
		if($supplier_location_list){

		     $join = ' LEFT JOIN '.DB_PREFIX.'deal_location_link dl ON d.id = dl.deal_id ';

		    foreach ($supplier_location_list as $key=>$value){
		        $deal_ids[] = $value['id'];
		    }
		    $ext_con = join(',', $deal_ids);
		    if(!$location_id){
		        $location_id=$supplier_location_list[0]['id'];
		    }
			if($data['is_shop']==0){
				$other_location_deal = get_deal_list(4,array(DEAL_ONLINE,DEAL_NOTICE), '', $join, " dl.location_id={$location_id} and id <> {$data_id} and d.is_shop =".$data['is_shop']." and ((d.is_coupon = 1 AND (d.coupon_end_time >= ".NOW_TIME." or d.coupon_end_time=0)) or d.is_coupon=0)");
			}else{
				$other_location_deal = array();
			}

		    $temp_deal = array();
		    foreach ($other_location_deal['list'] as $key=>$val){
		        $temp_deal[] = format_short_deal_item($val);
		    }
		    $data['other_location_deal'] = $temp_deal;
		    $data['count_other_location_deal'] = count($temp_deal)-1;
		}

		// 是否有优惠
		if($data['allow_promote'] == 1){
		    // 获取1个全站优惠
		    if(APP_INDEX=='wap')
		    	$promote_where=" and class_name <> 'Appdiscount'";
		    $sql = " select * from ".DB_PREFIX."promote where type = '0' ".$promote_where."  order by id desc";
		    $promotes = $GLOBALS['db']->getAll($sql);

	        foreach ($promotes as $k=>$v){
	            if($v['class_name']=='Appdiscount' || $v['class_name']=='Discountamount'){
	                $pro['content'] = $v['description'];
	                $pro['type'] = "minus";
	                $promotes_list[] = $pro;
	            }else{
	               //logger::write($cate_id);
	               if(!$cate_id){
	                   $pro['content'] = $v['description'];
	                   $pro['type'] = "free";
	                   $promotes_list[] = $pro;
	               }
	            }
	        }
		}
		if($return_money>0){
		    $pro['content']="购买返现".round($return_money,2)."元";
		    $pro['type'] = "return";
		    $promotes_list[] = $pro;
		}
		if($return_score>0){
		    $pro['content']="购买返".$return_score."积分";
		    $pro['type'] = "return";
		    $promotes_list[] = $pro;
		}
		$data['promotes_list'] = $promotes_list;

		//关联商品数据
		$relate_data = $this->getRelateData($data_id);
		if( !empty($relate_data) ){
			//app版本不需要
			$type = intval($GLOBALS['request']['type']);//商品ID
			if( empty($type) ){
				unset($relate_data['attrArray']);
				unset($relate_data['stockArray']);
			}
			foreach ($relate_data['goodsList'] as $k=>$v){
				if(intval($v['id'])!=$data_id){
					$format_relate_data['goodsList'][] = array(
						'id' =>$v['id'],
						'f_icon_middle' =>$v['f_icon_middle'],
						'current_price'=>$v['current_price'],
					);
				}
			    
                if (count($format_relate_data['goodsList'])==3){
                    break;
                }
            }
			$data['relate_data'] = $format_relate_data;
		}
		//echo "<pre>";print_r($data['relate_data']);exit;
		$data['check_deal_time']=check_deal_time($data_id);
		$data['check_deal_time']['DEAL_NOTICE']=DEAL_NOTICE;
		$data['check_deal_time']['DEAL_HISTORY']=DEAL_HISTORY;
		$data['check_deal_time']['COUPON_HISTORY']=COUPON_HISTORY;
		$data['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_collect where user_id=".$user['id']);
        $cart_list_data=load_cart_list(0,true);
        $data['cart_total_num'] = $cart_list_data['total_data']['total_num'];
		$data['supplier_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$data['supplier_id']);
		$data['avg_point']=sprintf("%.1f",$data['avg_point']);
        $data['share_title'] = $data['name'];
        $data['share_img'] = $data['icon'];
        $data['share_content'] = $data['brief'];
        $cart_data = get_cart_type($data_id,$app_index='wap');
        $data['in_cart'] = $cart_data['in_cart'];

        // 小能链接参数
        if (APP_INDEX == 'app' && isOpenXN() && $data['buy_type'] != 1) {
        	// 只在APP上且不能为积分商品
        	$settingid = app_conf('XN_SETTING_ID'); //'md_198_1496913879749';
	        if ($data['supplier_id']) {
	        	$xnInfo = $GLOBALS['db']->getRow('SELECT xn_talk_login_id, xn_talk_custom_id FROM '.DB_PREFIX.'supplier WHERE id='.$data['supplier_id']);
	        	$settingid = $xnInfo['xn_talk_custom_id'] ? $xnInfo['xn_talk_custom_id'] : ($xnInfo['xn_talk_login_id'] ? $xnInfo['xn_talk_login_id'].'_9999' : '');
	        }
	        if ($settingid) {
	        	$data['settingid'] = $settingid;
	        	$data['goodsTitle'] = $data['name'];
		        $data['goods_URL'] = SITE_DOMAIN.wap_url('index', 'deal', array('data_id' => $data_id));// $data['share_url'];
		        $data['goodsPrice'] = $data['current_price'];
		        $data['goods_showURL'] = $data['icon'];
	        }  
        }
         
        $data['user_login_status'] = check_login();

		return output($data);
	}

    /**
     * /mapi/index.php?ctl=deal&act=get_recommend_data
     * 输入：data_id(deal商品的id)
     * 输出：
     * @desc
     * @author    吴庆祥
     * @return unknown_type
     */
    public function get_recommend_data(){
        require_once(APP_ROOT_PATH."system/model/deal.php");
        $data_id = intval($GLOBALS['request']['data_id']);//商品ID
        $data = get_deal($data_id);
        // 获取推荐数据
        $area_data = load_auto_cache("cache_area",array("city_id"=>$GLOBALS['city']['id']));//缓存下来的地区配置
        $city_id = intval($GLOBALS['city']['id']);//城市分类ID
        $quan_id = intval($GLOBALS['request']['qid']); //商圈id
        $area_id = intval($area_data[$quan_id]['pid']); //大区id
        if($data['buy_type']==0){  //积分商品不展示推荐商品
            $condition_param = array("aid"=>$area_id,"qid"=>$quan_id,"city_id"=>$city_id);
            $order= " d.buy_count desc";
            $shop_where="";
            if($data['is_shop']==0){

                $shop_where=' and d.supplier_id<>'.$data['supplier_id'].' and d.is_location=1 and ((d.is_coupon = 1 AND (d.coupon_end_time >= '.NOW_TIME.' or d.coupon_end_time=0)) or d.is_coupon=0)';
                if($GLOBALS['geo']['xpoint']){
                    $geo=$GLOBALS['geo'];
                    $ypoint =  $geo['ypoint'];  //ypoint
                    $xpoint =  $geo['xpoint'];  //xpoint
                    $pi = PI;  //圆周率
                    $r = EARTH_R;  //地球平均半径(米)
                    $nowtime = NOW_TIME;
                    $tuan_list_sql="SELECT
    								d.*,(ACOS(SIN(($ypoint * $pi) / 180) * SIN((sl.ypoint * $pi) / 180) + COS(($ypoint * $pi) / 180) * COS((sl.ypoint * $pi) / 180) * COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180)) * $r) AS distance
    								FROM ".DB_PREFIX."supplier_location sl left join ".DB_PREFIX."deal_location_link dll on sl.id=dll.location_id left join ".DB_PREFIX."deal d on dll.deal_id=d.id
    								where 	d.is_effect = 1 AND d.is_delete = 0
    								AND ( 1 <> 1 OR ( ( $nowtime >= d.begin_time OR d.begin_time = 0 ) AND ( $nowtime < d.end_time OR d.end_time = 0 ) AND d.buy_status <> 2 )OR ( ( $nowtime < d.begin_time AND d.begin_time <> 0 AND d.notice = 1 ) ))
    								AND d.city_id IN (15, 0) AND d.is_recommend = 1 AND d.buy_type <> 1 AND d.is_shop = 0 ".$shop_where." group by sl.supplier_id ORDER BY distance,d.buy_count desc limit 6";
                    $tuan_list = $GLOBALS['db']->getAll($tuan_list_sql);
                    //echo "<pre>";print_r($tuan_list);exit;
                    $deal_recommend['list']=format_deal_list($tuan_list);
                    //echo "<pre>";print_r($deal_recommend);exit;

                }else{
                    $deal_recommend  = get_deal_list(6, array(DEAL_ONLINE), $condition_param,"", 'd.id <> '.$data_id.' and d.is_shop='.$data['is_shop'].$shop_where,$order);
                }
            }else{
                $deal_recommend  = get_deal_list(6, array(DEAL_ONLINE), $condition_param,"", 'd.id <> '.$data_id.' and d.is_shop='.$data['is_shop'].$shop_where,$order);
            }

            $temp_deal = array();
            foreach ($deal_recommend['list'] as $key=>$val){
                $temp_deal[] = format_short_deal_item($val);
            }

            $data['recommend_data'] = $temp_deal;
        }
        // 推荐商品
        if($data['recommend_data']){
            foreach ($data['recommend_data'] as $k=>$v){
                $data['recommend_data'][$k]['rd_url'] =  wap_url("index", 'deal', array('data_id'=>$v['id']) );
            }
        }
        return output($data,1,"");
    }
	/**
	 * 商品详细页接口
	 * 输入：
	 * data_id: int 商品ID
	 *
	 * 输出：
	 * id:[int]商品ID
	 * name: [string] 商品名称
	 * description： [string] 商品详情 HTML 格式
	 */
	public function detail(){
	    $root = array();
	    $data_id = intval($GLOBALS['request']['data_id']);//商品ID
	    
	    require_once(APP_ROOT_PATH."system/model/deal.php");
	    $deal = get_deal($data_id);
	    if($deal){
	        $data['id']=$deal['id'];
	        $data['name']=$deal['name'];
	        $data['description']= get_abs_img_root(format_html_content_image($deal['phone_description'], 150));
	    }

	    if($deal['is_shop']==0)
	    $data['page_title'] = "团购详情";
	    else
	    	$data['page_title'] = "商品详情";
	    return output($data);
	}
	
	/**
	 * 收藏接口
	 * 输入：
	 * data_id: int 商品ID
	
	 *
	 *
	 *
	 * 输出：
	 * is_collect [int] 0：未收藏 ，1已收藏
	 *
	 */
	public function add_collect()
	{
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	     
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
	
	        $goods_id = intval($GLOBALS['request']['id']);
	        $goods_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$goods_id." and is_effect = 1 and is_delete = 0");
	        if($goods_info)
	        {
	            $sql = "INSERT INTO `".DB_PREFIX."deal_collect` (`id`,`deal_id`, `user_id`, `create_time`) select '0','".$goods_info['id']."','".$user_id."','".get_gmtime()."' from dual where not exists (select * from `".DB_PREFIX."deal_collect` where `deal_id`= '".$goods_info['id']."' and `user_id` = ".$user_id.")";
	            $GLOBALS['db']->query($sql);
	            if($GLOBALS['db']->affected_rows()>0){
	                $root['is_collect'] = 1;
	                $info = "收藏成功";
	                $status = 1;
	            }
	            else
	            {
	            	$root['is_collect'] = 1;
	            	$info = "您已经收藏了该商品";
	            	$status = 1;
	            }
	            $root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_collect where user_id=".$user_id);
	            
	        }
	    }
	    return output($root,$status,$info);
	}
	
	public function del_collect()
	{
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
	
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
	
			$goods_id = intval($GLOBALS['request']['id']);
			$goods_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$goods_id." and is_effect = 1 and is_delete = 0");
			if($goods_info)
			{
				$sql = "delete from  ".DB_PREFIX."deal_collect where user_id=".$user_id." and deal_id=".$goods_id;
				//logger::write($sql);
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()>0){
					$root['is_collect'] = 0;
					$info = "取消成功";
					$status = 1;
				}
				else
				{
					$root['is_collect'] = 0;
					$info = "您已经取消了该商品";
					$status = 1;
				}
				$root['collect_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_collect where user_id=".$user_id);
				 
			}
		}
		return output($root,$status,$info);
	}
	
	/**
	 * 
	 * 根据deal_ids获取列表信息(包括属性，库存)
	 * 
	 * return array(
	 * 	'goodsList'	=>	array(),
	 * 	'dealArray'	=>	array(
	 * 						'id'=>array(
	 * 							'name'=>'','origin_price'=>'','current_price'=>''
	 * 						),
	 * 					),
	 * 	'attrArray'	=>	array(
	 * 						'id'=>array(
	 * 							'规格类型'=>array(
	 * 								'规格id'=>array(),
	 * 							),
	 * 						),
	 * 					),
	 * 	'stockArray'	=>	array(
	 * 						'id'=>array(
	 * 							'规格类型_规格类型'=>array(),
	 * 						),
	 * 					),
	 * 
	 * )
	*/
	private function getRelateData($data_id){
		$deal_ids = $GLOBALS['db']->getOne("select relate_ids from ".DB_PREFIX."relate_goods where good_id=".$data_id);
// 		echo "select relate_ids from ".DB_PREFIX."relate_goods where good_id=".$data_id;exit;
		$result = array();
		if($deal_ids){
			require_once(APP_ROOT_PATH."system/model/deal.php");
			$result = getDetailedList_v1($deal_ids.','.$data_id);
		}
		return $result;
	}
	
	
	
	/**
	 * 加载商品点评信息
	 * @return array 
	 */
	public function ajax_dp_list()
	{
		require_once(APP_ROOT_PATH."system/model/review.php");
	    require_once(APP_ROOT_PATH."system/model/user.php");
        require_once(APP_ROOT_PATH."system/model/deal.php");
	    $data_id = intval($GLOBALS['request']['data_id']);
	    $page = intval($GLOBALS['request']['page']) ?: 1;
	    $ajax = intval($GLOBALS['request']['dpajax']) ?: 0;
        $deal = get_deal($data_id);
	    $page_size = PAGE_SIZE;
	    $limit = (($page -1) * $page_size).','.$page_size;
	    $dp_list = get_dp_list($limit,$param=array("deal_id"=>$data_id),"","");
	    $format_dp_list = array();
	    $dp_count = 0;
	    if ($dp_list['list'] && !$ajax) {
	    	$dp_count_sql = 'SELECT count(*) FROM '.DB_PREFIX.'supplier_location_dp where '.$dp_list['condition'];
	    	$dp_count = $GLOBALS['db']->getOne($dp_count_sql);
	    }
	    foreach($dp_list['list'] as $k=>$v){
	    
	        $temp_arr = array();
	         
	        $temp_arr['id'] = $v['id'];
	        $temp_arr['create_time'] = $v['create_time'] > 0 ?to_date($v['create_time'],'Y-m-d'):'';
	        $temp_arr['content'] = $v['content'];
	        $temp_arr['reply_content']= $v['reply_content']?$v['reply_content']:'';
	        $temp_arr['point'] = $v['point'];
	        $temp_arr['point_percent'] = $v['point_percent'];
	        $temp_arr['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"middle"));
	        $temp_arr['format_show_date'] = $v['create_time'] > 0 ?format_show_date($v['create_time']):'';
	        $uinfo = load_user($v['user_id']);
	        $temp_arr['user_name'] = $uinfo['user_name'];
	    
	        $v['images'] = unserialize($v['images_cache']);
	    
	        $images = array();
	        $oimages = array();
	    
	        if($v['images']){
	            foreach ($v['images'] as $ik=>$iv){
	                $images[] = get_abs_img_root(get_spec_image($iv,400,400,1));
	                $oimages[] = get_abs_img_root($iv);
	            }
	             
	        }
	        $temp_arr['images'] = $images;
	        $temp_arr['oimages'] = $oimages;
	    
	        $format_dp_list[] = $temp_arr;
	    }
	    	$data['list'] = $format_dp_list;
            $data['dp_count']=$dp_count;
            $data['buy_type']=$deal['buy_type'];
            $data['supplier_info']['avg_point']=sprintf("%.1f",$deal['avg_point']);
            $data['supplier_info']['avg_point_percent']=$deal['avg_point']/5*100;
	    	return output($data);
	}
	
}
?>