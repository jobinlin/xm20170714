<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 输出接口数据
 * @param unknown_type $data 返回的接口数据
 * @param unknown_type $status 当前状态 0/1
 * @param unknown_type $info 当前消息 可选，为空时由客户端取默认提示(默认提示包含成功的默认提示与失败的默认提示)
 */
function output($data,$status=1,$info="") 
{

    if (isset($_REQUEST['r_type'])){
        $r_type = intval($_REQUEST['r_type']);//返回数据格式类型; 0:base64;1;json_encode;2:array 3:jsonp
    }elseif(APP_INDEX=='app'){
        $r_type=5;
    }else{
        $r_type = 0;
    }

	$data[CTL] = MODULE_NAME;
	$data[ACT] = ACTION_NAME;
	$data['status'] = $status;
	$data['info'] = $info;
	$data['city_name'] = $GLOBALS['city']['name'];
	$data['return'] = 1; //弃用该返回，统一返回1
	$data['sess_id'] = $GLOBALS['sess_id'];
	$data['ref_uid'] = $GLOBALS['ref_uid'];
	if(defined("APP_INDEX")&&APP_INDEX=="wap")
	{
		ob_clean();
		return $data;
	}
	else
	{
	    
		header("Content-Type:text/html; charset=utf-8");
		ob_clean();
		
		if ($r_type == 0)
		{
			echo base64_encode(json_encode($data));
		}
		else if ($r_type == 1)
		{
			echo(json_encode($data));
		}
		else if ($r_type == 2)
		{
			print_r($data);
		}
		else if($r_type == 3)
		{
			$json = json_encode($data);
			echo $_GET['callback']."(".$json.")";
		}else if($r_type == 4){
			require_once(APP_ROOT_PATH.'/system/libs/crypt_aes.php');
			$aes = new CryptAES();
			$aes->set_key(FANWE_AES_KEY);
			$aes->require_pkcs5();
			$encText = $aes->encrypt(json_encode($data));
			echo $encText;
		}else if($r_type == 5){
            ob_clean();
            return $data;
        }
        
		exit;
	}
	
}


function get_abs_img_root($content)
{
	return format_image_path($content);
}
function get_muser_avatar($id,$type,$is_rand=1)
{
	$uid = sprintf("%09d", $id);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$path = $dir1.'/'.$dir2.'/'.$dir3;

	$id = str_pad($id, 2, "0", STR_PAD_LEFT);
	$id = substr($id,-2);
	$avatar_file = format_image_path("./public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg");
	$avatar_check_file = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";
	if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		if(!check_remote_file_exists($avatar_file))
			$avatar_file =  SITE_DOMAIN.APP_ROOT."/public/avatar/noavatar_".$type.".gif";
	}
	else
	{
		if(!file_exists($avatar_check_file))
			$avatar_file =  SITE_DOMAIN.APP_ROOT."/public/avatar/noavatar_".$type.".gif";
	}
	if($is_rand==1){
		$stat = stat($avatar_check_file);
		//return  $avatar_file.'?'.(mt_rand(1,3) / mt_rand(100,500));
		return  $avatar_file.'?'.md5($stat['mtime']);
	}else{
		return  $avatar_file;
	}
	
}


/**
 * 刷新会员安全登录状态
 */
if(!function_exists("refresh_user_info"))
{
function refresh_user_info()
{
	global $user_info;
	global $user_logined;

    require_once(APP_ROOT_PATH."system/model/user.php");
    $user_info = es_session::get('user_info');
    if(empty($user_info))
    {
        $cookie_uname = $GLOBALS['request']['user_key']?$GLOBALS['request']['user_key']:'';
        $cookie_upwd = $GLOBALS['request']['user_pwd']?$GLOBALS['request']['user_pwd']:'';
        if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
        {
            $cookie_uname = strim($cookie_uname);
            $cookie_upwd = strim($cookie_upwd);
            auto_do_login_user($cookie_uname,$cookie_upwd,false);
            $user_info = es_session::get('user_info');
        }
    }

	//实时刷新会员数据
	if($user_info)
	{
		$user_info = load_user($user_info['id']);
		$user_level = load_auto_cache("cache_user_level");
		$user_info['level'] = $user_level[$user_info['level_id']]['level'];
		$user_info['level_name'] = $user_level[$user_info['level_id']]['name'];
		es_session::set('user_info',$user_info);

		$user_logined_time = intval(es_session::get("user_logined_time"));
		$user_logined = es_session::get("user_logined");
		if(NOW_TIME-$user_logined_time>=MAX_LOGIN_TIME)
		{
			es_session::set("user_logined_time",0);
			es_session::set("user_logined", false);
			$user_logined = false;
		}
		else
		{
			if($user_logined)
				es_session::set("user_logined_time",NOW_TIME);
		}
	}


    //商户信息
    require_once(APP_ROOT_PATH."system/libs/biz_user.php");
    global $account_info;


    //可以只退出商户
    if($user_info)
    {
        //获取商户登录信息
        $supplier_login_info = $GLOBALS['db']->getRow("select account_name,account_password from ".DB_PREFIX."supplier_account where account_name = '".$user_info['merchant_name']."'");
        if($supplier_login_info){
            auto_do_login_biz($supplier_login_info['account_name'],$supplier_login_info['account_password'],false);
            $account_info = es_session::get('account_info');
        }
    }
    //实时刷新商户数据
    if($account_info){

        $account_info = $GLOBALS['db']->getRow("select sa.*,s.is_open_dada_delivery,s.delivery_money,s.money,s.publish_verify_balance*100 as publish_verify_balance,s.platform_status from ".DB_PREFIX."supplier_account as sa left join ".DB_PREFIX."supplier as s on sa.supplier_id=s.id where sa.is_delete = 0 and sa.is_effect = 1 and sa.id = ".intval($account_info['id']));
        if($account_info['is_main'] == 1){ //主账户取所有门店
            $account_locations = $GLOBALS['db']->getAll("select id as location_id from ".DB_PREFIX."supplier_location where is_effect=1 and supplier_id = ".$account_info['supplier_id']);
        }else
            $account_locations = $GLOBALS['db']->getAll("select sl.location_id from ".DB_PREFIX."supplier_account_location_link as sl left join ".DB_PREFIX."supplier_location as l on l.id=sl.location_id where l.is_effect=1 and sl.account_id = ".$account_info['id']);

        $account_location_ids = array();
        foreach($account_locations as $row)
        {
            $account_location_ids[] = $row['location_id'];
        }
        $account_info['location_ids'] =  $account_location_ids;
        $GLOBALS['account_info']['location_ids'] =  $account_location_ids;
        es_session::set('account_info',$account_info);
    }
}
}

/**
 * 前端全运行函数，生成系统前台使用的全局变量
 * 1. 定位城市 GLOBALS['city'];
 * 2. 加载会员 GLOBALS['user_info'];
 * 4. 加载推荐人与来路
 * 5. 更新购物车
 */
if(!function_exists("global_run"))
{
function global_run()
{
	if(app_conf("SHOP_OPEN")==0)  //网站关闭时跳转到站点关闭页
	{
		//app_redirect(wap_url("index","close"));
	}

	//处理城市
	global $city;
	require_once(APP_ROOT_PATH."system/model/city.php");
	$city = City::locate_city($GLOBALS['request']['city_id']);
	
	//处理经纬度
	global $geo;
	$geo = City::locate_geo($GLOBALS['request']['m_longitude'],$GLOBALS['request']['m_latitude']);
	
	//会员自动登录及输出
	global $cookie_uname;
	global $cookie_upwd;
	global $user_info;
	global $user_logined;
    //商户信息
    global $account_info;

	refresh_user_info();

	//刷新购物车
	require_once(APP_ROOT_PATH."system/model/cart.php");
	refresh_cart_list();

	global $ref_uid;
		
	//保存返利的cookie
	if($GLOBALS['request']['ref_uid'])
	{
		$rid = intval($GLOBALS['request']['ref_uid']);
		$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval($rid)));
	}
	
	global $supplier_info;  //商家信息
	//处理商户信息
	global $spid;
	$spid = intval($GLOBALS['request']['spid']);
	if($spid>0)
	{
		$supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$spid);
	}
	
	global $referer;
	//保存来路
	$referer = $GLOBALS['request']['from'];
	$referer = strim($referer);

}
}
/**
 * 获取生活服务分类的大->小类结构
 * @return array
 * 结构如下
 * Array(
 * 		Array
        (
            [id] => 0
            [name] => 全部分类
            [icon_img] => 
            [iconfont]=>
            [iconcolor]=>
            [bcate_type] => Array
                (
                    [0] => Array
                        (
                            [id] => 0
                            [cate_id] => 0
                            [name] => 全部分类
                        )

                )

        )
 * )
 */
function getCateList(){
	$cate_list_rs = load_auto_cache("cache_cate_tree",array("type"=>0));
	
	$cate_list = array(
		array(
			"id"	=>	0,
			"name"	=>	"全部分类",	
			"icon_img"	=>	"",
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
	foreach($cate_list_rs as $k=>$v)
	{
		$cate = array();
		$cate['id'] = $v['id'];
		$cate['name'] = $v['name']?$v['name']:"";
		$cate['icon_img'] = $v['icon_img']?$v['icon_img']:"";
		$cate['iconfont'] = $v['iconfont']?$v['iconfont']:"";
		$cate['iconcolor'] = $v['iconcolor']?$v['iconcolor']:"";
		$cate['bcate_type']	= array(
			array(
					"id"	=>	0,
					"cate_id"	=>	0,
					"name"	=>	"全部"
			)		
		);
		foreach($v['pop_nav'] as $kk=>$vv)
		{
			$bcate_type = array();
			$bcate_type['id']	=	$vv['id'];
			$bcate_type['cate_id'] = $vv['cate_id']?$vv['cate_id']:"";
			$bcate_type['name'] = $vv['name']?$vv['name']:"";
			$cate['bcate_type'][] = $bcate_type;
		}		
		$cate_list[] = $cate;
	}
	
	return $cate_list;
}

//获取领券中心分类
function getYouhuiCateList(){
	$youhui_ids = $GLOBALS['db']->getCol("select DISTINCT deal_cate_id from ".DB_PREFIX."youhui where is_effect=1 and (begin_time < ".NOW_TIME." or begin_time = 0) 
	and ( total_num > user_count or total_num = 0 )");
	$sql = "select id,name from ".DB_PREFIX."deal_cate where id in (".implode(',',$youhui_ids).") and is_effect=1 and is_delete=0";
	$cate_list_rs = $GLOBALS['db']->getAll($sql);

	$cate_list = array(
			array(
					"id"	=>	0,
					"name"	=>	"精选"
			)
	);
	foreach($cate_list_rs as $k=>$v)
	{
		$cate = array();
		$cate['id'] = $v['id'];
		$cate['name'] = $v['name']?$v['name']:"";

		$cate_list[] = $cate;
	}

	return $cate_list;
}


/**
 * 获取活动分类的结构
 * @return array
 * 结构如下
 * Array(
 * 		Array
        (
            [id] => 0
            [name] => 全部分类            
        )
 * )
 */
function getEventCateList(){
	$cate_list_rs = load_auto_cache("cache_cate_tree",array("type"=>4));

	$cate_list = array(
			array(
					"id"	=>	0,
					"name"	=>	"全部"					
			)
	);
	foreach($cate_list_rs as $k=>$v)
	{
		$cate = array();
		$cate['id'] = $v['id'];
		$cate['name'] = $v['name']?$v['name']:"";		
		$cate_list[] = $cate;
	}

	return $cate_list;
}


/**
 * 获取商城分类的大->小类结构
 * @return array
 * 结构如下
 * Array(
 * 		Array
        (
            [id] => 0
            [name] => 全部分类
            [iconfont]=>
            [iconcolor]=>
            [bcate_type] => Array
                (
                    [0] => Array
                        (
                            [id] => 0
                            [cate_id] => 0
                            [name] => 全部分类
                        )

                )

        )
 * )
 */
function getShopCateList(){
	$cate_list_rs = load_auto_cache("cache_cate_tree",array("type"=>1));

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
	foreach($cate_list_rs as $k=>$v)
	{
		$cate = array();
		$cate['id'] = $v['id'];
		$cate['name'] = $v['name']?$v['name']:"";
		$cate['iconfont'] = $v['m_iconfont']?$v['m_iconfont']:"";
		$cate['iconcolor'] = $v['m_iconcolor']?$v['m_iconcolor']:"";
		$cate['iconbgcolor'] = $v['m_iconbgcolor']?$v['m_iconbgcolor']:"";
		$cate['cate_img'] = $v['cate_img']?$v['cate_img']:"";
		$cate['bcate_type']	= array(
				array(
						"id"	=>	$v['id'],
						"cate_id"	=>	$v['id'],
						"name"	=>	"全部"
				)
		);
		foreach($v['pop_nav'] as $kk=>$vv)
		{
			$bcate_type = array();
			$bcate_type['id']	=	$vv['id'];
			$bcate_type['cate_id'] = $vv['pid']?$vv['pid']:"";
			$bcate_type['name'] = $vv['name']?$vv['name']:"";
			$bcate_type['cate_img'] = $vv['cate_img']?$vv['cate_img']:"";
			$cate['bcate_type'][] = $bcate_type;
		}
		$cate_list[] = $cate;
	}

	return $cate_list;
}



/**
 * 获取地区商圈列表
 * @param int $city_id
 * @return array
 * 结构如下
 * Array(
 * 		Array
        (
            [id] => 0
            [name] => 全城
            [quan_sub] => Array
                (
                    [0] => Array
                        (
                            [id] => 0
                            [pid] => 0
                            [name] => 全城
                        )

                )

        )
 * )
 */
function getQuanList($city_id =0){
	$all_quan_list= load_auto_cache("cache_area",array("city_id"=>$city_id));

	
	$quan_list = array(
			array(
					"id"	=>	0,
					"name"	=>	"全城",
					"quan_sub"	=>	array(
							array(
									"id"	=>	0,
									"pid"	=>	0,
									"name"	=>	"全城"
							)
					)
			)
	);
	
	foreach($all_quan_list as $k=>$v)
	{
		if($v['pid']==0)
		{
			$area = array();
			$area['id'] = $v['id'];
			$area['name'] = $v['name']?$v['name']:"";
			$area['quan_sub']	= array(
					array(
							"id"	=>	$v['id'],
							"pid"	=>	0,
							"name"	=>	"全部"
					)
			);
			foreach($all_quan_list as $kk=>$vv)
			{
				if($vv['pid']==$v['id'])
				{
					$quan = array();
					$quan['id'] = $vv['id'];
					$quan['name'] = $vv['name']?$vv['name']:"";
					$quan['pid'] = $vv['pid'];
					$area['quan_sub'][] = $quan;
				}
			}
			$quan_list[] = $area;
		}
		
	}
	return $quan_list;
}

/**
 * 获取品牌
 * @param unknown_type $shop_cate_id
 */
function getBrandList($shop_cate_id)
{
	//获取品牌
	//$brand_list = array( array("id"=>0,"name"=>"全部") );
	$brand_list =array();
	if($shop_cate_id>0)
	{
		$cate_key = load_auto_cache("shop_cate_key",array("cid"=>$shop_cate_id));
		$brand_list_rs = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."brand where match(tag_match) against('".$cate_key."' IN BOOLEAN MODE)  order by sort limit 100");
		foreach($brand_list_rs as $k=>$v)
		{
			$row['id'] = $v['id'];
			$row['name'] = $v['name'];
			$brand_list[]=$row;
		}
	}else{
	    $brand_list_rs = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."brand order by sort limit 100");
	    foreach($brand_list_rs as $k=>$v)
	    {
	        $row['id'] = $v['id'];
	        $row['name'] = $v['name'];
	        $brand_list[]=$row;
	    }
	}
	return $brand_list;
}


/**
 * 格式化列表的商品
 * @param unknown_type $v
 * @return unknown
 */
function format_deal_list_item($v)
{	
	$v['name']=htmlspecialchars_decode($v['name']);
	$v['sub_name']=htmlspecialchars_decode($v['sub_name']);
	$group_id = $GLOBALS['user_info']['group_id'];
    
    if($group_id && $v['allow_user_discount']){
        $group_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".$group_id);
    
        if($group_info && $group_info['discount']<1){
            $v['current_price'] = round($v['current_price']*$group_info['discount'],2);
        }
    }
    
	$deal['id'] = $v['id'];
	$deal['distance']= floatval($v['distance']);
	$deal['ypoint'] = floatval($v['ypoint']);
	$deal['xpoint'] = floatval($v['xpoint']);
	$deal['name'] = $v['name'];
	$deal['sub_name'] = $v['sub_name'];
	$deal['brief'] = $v['brief'];
	$deal['buy_count'] = $v['buy_count'];
	$deal['current_price']=round($v['current_price'],2);
	$deal['origin_price']=round($v['origin_price'],2);
	$deal['icon']=get_abs_img_root(get_spec_image($v['icon'],92,82,1));
	$deal['icon_v1']=get_abs_img_root(get_spec_image($v['icon'],180,165,1));
	$deal['f_icon']=get_abs_img_root(get_spec_image($v['icon'],370,370,1)); //WAP,正方形图片
	$deal['f_icon_v1']=get_abs_img_root(get_spec_image($v['icon'],185,185,1)); //WAP,正方形图片
	$deal['end_time_format']=to_date($v['end_time']);
	$deal['begin_time_format']=to_date($v['begin_time']);
	$deal['begin_time'] = $v['begin_time'];
	$deal['end_time'] = $v['end_time'];
	$deal['auto_order'] = $v['auto_order'];
	$deal['is_lottery'] = $v['is_lottery'];
	$deal['is_refund'] = $v['is_refund'];
	$deal['deal_score'] = abs($v['return_score']);
	$deal['buyin_app'] = intval($v['buyin_app']);
	$deal['allow_promote'] = intval($v['allow_promote']);
	$deal['location_id']   = intval($v['location_id']);
	$deal['location_name'] = $v['location_name'];
	$deal['location_address'] = $v['location_address'];
	$deal['location_avg_point'] = $v['location_avg_point'];
	$deal['location_dp_count'] = $v['location_dp_count'];
	$deal['location_dp_xpoint'] = $v['location_xpoint'];
	$deal['location_dp_ypoint'] = $v['location_ypoint'];
	$deal['is_verify'] = $v['is_verify'];
	$deal['open_store_payment'] = $v['open_store_payment'];
	$deal['area_name'] = $v['area_name'];
	$deal['url'] = $v['url'];
    $deal['share_url'] = $v['share_url'];
    //app 使用  返回app 的原生页面 page_finsh=1
    $deal['app_url'] = SITE_DOMAIN.wap_url("index","deal#index",array('data_id'=>$v['id']));
	
	if (empty($v['brief'])){
		$deal['brief'] = $v['name'];
		$deal['name'] = $v['sub_name'];
	}
		
	$today_begin = to_timespan(to_date(NOW_TIME,'Y-m-d'));
	$today_end = $today_begin*24*60*60;	
	if(($v['begin_time']>0) && ($today_begin<$v['begin_time'] && $v['begin_time']<$today_end))
	{
		$deal['is_today']=1;
	}
	else
	{
		$deal['is_today']=0;
	}
	$deal['supplier_id'] = $v['supplier_id'];
	return $deal;
}

/**
 * 格式化商家列表的商家数据
 * @param unknown_type $v
 * @return unknown
 */
function format_store_list_item($v)
{	
	$store['preview']=get_abs_img_root(get_spec_image($v['preview'],92,82,1));
	$store['preview_v1']=get_abs_img_root(get_spec_image($v['preview'], 90, 65,1));
	$store['preview_v2']=get_abs_img_root(get_spec_image($v['preview'], 180, 180,1));
	$store['id'] = $v['id'];
	$store['is_verify'] = $v['is_verify'];
	$store['avg_point'] = $v['avg_point'];
	$store['address'] = $v['address'];
	$store['name'] = $v['name'];
	$store['distance'] = $v['distance'] >0 ? floatval($v['distance']) : '';
	$store['xpoint'] = floatval($v['xpoint']);
	$store['ypoint'] = floatval($v['ypoint']);
	$store['tel'] =$v['tel'];
	$store['dealcate_name'] =$v['dealcate_name'];
	$store['area_name'] =$v['area_name'];
	$store['deal_cate_id'] = $v['deal_cate_id'];
	$store['open_store_payment'] = $v['open_store_payment'];
	$store['supplier_id'] = $v['supplier_id'];
    $store['app_url'] = $v['app_url'];;
	return $store;
}


function format_store_item($v)
{

	$store['open_store_payment'] = $v['open_store_payment'];
	$store['xpoint'] = floatval($v['xpoint']);
	$store['ypoint'] = floatval($v['ypoint']);
	$store['preview']=get_abs_img_root(get_spec_image($v['preview'],300,182));
	$store['preview_v1']=get_abs_img_root(get_spec_image($v['preview'],150, 150));
	$store['id'] = $v['id'];
	$store['supplier_id'] = $v['supplier_id'];
	$store['is_verify'] = $v['is_verify'];
	$store['avg_point'] = round($v['avg_point'],1);
	$store['tags'] = $v['tags'];
	$store['route'] = $v['route'];
	$store['address'] = $v['address'];
	$store['name'] = $v['name'];
	$store['tel'] = $v['tel'];
	$store['brief'] = get_abs_img_root(format_html_content_image($v['brief'],150));//get_abs_url_root($v['brief']);
	$store['mobile_brief'] = $v['mobile_brief'];
	$store['store_images'] = $v['store_images'];
	$store['share_url'] = $v['share_url'];
	$store['ref_avg_price'] = $v['ref_avg_price'];
	$store['promotes'] = $v['promotes'];
	$store['dp_count'] = $v['dp_count'];
	$store['open_time'] = $v['open_time'];



	return $store;
}

function format_event_item($v){
    $event['id']= $v['id'];
    $event['name']= $v['name'];
    //$event['icon']=get_abs_img_root(get_spec_image($v['icon'],300,182,1));
    $event['img']=get_abs_img_root(get_spec_image($v['img'],414,138,1));
    $event['event_begin_time'] = $v['event_begin_time'];
    $event['event_end_time'] = $v['event_end_time'];
    $event['event_begin_time_format'] = to_date($v['event_begin_time'],"Y-m-d");
    $event['event_end_time_format'] = to_date($v['event_end_time'],"Y-m-d");
    $event['submit_begin_time_format']= to_date($v['submit_begin_time'],"Y-m-d");
    $event['submit_end_time_format']= to_date($v['submit_end_time'],"Y-m-d");
    $event['submit_count'] = $v['submit_count'];
    $event['total_count'] = $v['total_count'];
    $event['score_limit'] = $v['score_limit'];
    $event['point_limit'] = $v['point_limit'];
    $event['supplier_location_count'] = $v['supplier_location_count'];
    $event['now_time']= NOW_TIME;
    $event['submit_begin_time'] = $v['submit_begin_time'];
    $event['submit_end_time'] = $v['submit_end_time'];
    $event['supplier_info_name'] = $v['supplier_info']['name'];
    $event['content'] = get_abs_img_root(format_html_content_image($v['content'],150));
    $event['address'] = $v['address'];
    $event['avg_point'] = round($v['avg_point'],1);
    $event['ypoint'] = floatval($v['ypoint']);
    $event['xpoint'] = floatval($v['xpoint']);
    if ($v['submitted_data']){
        $submitted_data['is_verify'] = $v['submitted_data']['is_verify'];
        $event['submitted_data'] = $submitted_data;
    }
    
    $event['event_fields'] = $v['event_fields'];
    $event['share_url'] = $v['share_url'];
    return $event;
}

/**
 * 格式化活动列表的活动数据
 * @param unknown_type $v
 * @return string
 */
function format_event_list_item($v)
{
    //print_r($v);exit;
	$event['id']= $v['id'];
	$event['name']= $v['name'];
	$event['distance']= floatval($v['distance']);
	$event['icon']=get_abs_img_root(get_spec_image($v['icon'],300,182,1));
	$event['img']=get_abs_img_root(get_spec_image($v['img'],414,138,1));
	$event['submit_begin_time_format']= to_date($v['submit_begin_time'],'Y-m-d');
	$event['submit_end_time_format']= to_date($v['submit_end_time'],'Y-m-d');
	$event['supplier_info_name'] = $v['location_name'];
	$event['supplier_info_preview']=get_abs_img_root(get_spec_image($v['location_preview'],300,182,1));
	$event['district'] = $v['area_name'];
	$event['submit_count'] = $v['submit_count'];
	$event['total_count'] = $v['total_count'];
	$event['xpoint'] = floatval($v['xpoint']);
	$event['ypoint'] = floatval($v['ypoint']);
	$event['out_time'] = $v['out_time'];
	$event['is_over'] = $v['is_over'];
	if($v['submit_end_time']==0)
		$event['sheng_time_format']= "长期有效";
	else
		$event['sheng_time_format']= to_date($v['submit_end_time']-NOW_TIME,"d天h小时i分");
	return $event;
}

function format_youhui_item($v){
    $item['id'] = $v['id'];
    $item['name'] = $v['name'];
    $item['icon'] = get_abs_img_root(get_spec_image($v['icon'],300,182));
    $item['now_time'] = NOW_TIME;
    $item['begin_time'] = $v['begin_time'];
    $item['end_time'] = $v['end_time'];
    $item['format_end_time'] = empty($v['end_time']) ? '领券期限：永久' : '领券期至：'.date('Y-m-d', $v['end_time']);
    // $item['format_end_time'] = date('Y-m-d H:i', $v['end_time']);
    $item['last_time'] = intval($v['end_time'])>NOW_TIME?(intval($v['end_time'])-NOW_TIME):0;
    $item['last_time_format'] = $item['last_time']%86400>0?intval($item['last_time']/86400)."天以上":$item['last_time']/86400;
    $item['expire_day'] = $v['expire_day'];
    if(empty($v['expire_day']) && !empty($v['end_time'])){
	    $item['format_expire'] = '优惠券领取后，在'.date('Y-m-d', $v['end_time'])."前可使用";
    } else {
	    $item['format_expire'] = empty($v['expire_day']) ? '使用期限：永久' : "优惠券领取后".$v['expire_day']."天内可用";
    }
    $item['total_num'] = $v['total_num'];
    $item['is_effect'] = $v['is_effect'];
    $item['user_count'] = $v['user_count'];
    $item['user_limit'] = $v['user_limit'];
    $item['score_limit'] = $v['score_limit'];
    $item['point_limit'] = $v['point_limit'];
    $item['supplier_info_name'] = $v['supplier_info']['name'];
    $item['avg_point'] = round($v['avg_point'],1);
    $item['ypoint'] = floatval($v['ypoint']);
    $item['xpoint'] = floatval($v['xpoint']);
    $item['description'] = get_abs_img_root(format_html_content_image($v['description'],150));
    $item['use_notice'] = get_abs_img_root(format_html_content_image($v['use_notice'],150));
    $item['share_url'] = $v['share_url'];
    $item['less'] = $v['less'];
    $item['image_3'] = $v['image_3'] ? get_abs_img_root(get_spec_image($v['image_3'],414,138)) : SITE_DOMAIN.APP_ROOT.'/wap/Tpl/main/fanwe/style5.2/images/static/event-no-banner.png';

    return $item;
}

/**
 * 格式化优惠券列表优惠券数据
 * @param unknown_type $v
 * @return string
 */
function format_youhui_list_item($v)
{
    $user = $GLOBALS['user_info'];
    
	$youhui['id'] = $v['id'];
	$youhui['distance']= floatval($v['distance']);
	$youhui['name'] = $v['name'];
	$youhui['list_brief'] = $v['list_brief'];
	$youhui['icon']=get_abs_img_root(get_spec_image($v['icon'],92,82,1));
	$youhui['down_count'] = $v['user_count'];
	$youhui['user_limit'] = $v['user_limit'];
	$youhui['youhui_type'] = $v['youhui_type'];
	$youhui['total_num'] = $v['total_num'];
	$youhui['xpoint'] = floatval($v['xpoint']);
	$youhui['ypoint'] = floatval($v['ypoint']);
	$youhui['area_name'] = $v['area_name'];
	$youhui['location_name']=$v['location_name'];
	$youhui['youhui_value'] = intval($v['youhui_value']);
	if($v['youhui_type']){
	    $youhui['youhui_value'] = round($v['youhui_value']/10,1);
	}
	$youhui['location_avg_point'] = intval($v['location_avg_point']);
	$begin_time = to_date($v['begin_time'],"Y-m-d");
	$end_time = to_date($v['end_time'],"Y-m-d");
	
	if(APP_INDEX=="app"){
	    $youhui['jump']="?ctl=store&data_id=".$v['location_id'];
	}else{
	    $youhui['jump']=wap_url("index","store",array("data_id"=>$v['location_id']));
	}
	
	//优惠券状态
	if(NOW_TIME<$v['begin_time']){
	    $time=$v['begin_time']-NOW_TIME;
	    $day=$time/(24*3600);
	    $youhui['status']=-1;
	    if(intval($day)>1){
	       $youhui['info']=intval($day)."<span>天</sapan>";
	    }else{
	       $hour=intval($time/3600);
	       $minu=intval(($time-$hour*3600)/60);
	       $youhui['info']=$hour."<span>小时</span>".$minu."<span>分钟</span>";
	    }
	    $youhui['order_status']=4;
	}
	else if($v['end_time']<NOW_TIME && $v['end_time']!=0){
	    $youhui['status']=0;
	    $youhui['info']="已结束";
	    $youhui['order_status']=7;
	}
	else if($v['total_num']<=$v['user_count']){
	    $youhui['status']=0;
	    $youhui['info']="已抢光";
	    $youhui['order_status']=6;
	}
	else if($user){
	    if($user['score']<$v['score_limit']){
	        $youhui['status']=0;
	        $youhui['info']="积分不足";
	        $youhui['order_status']=2;
	    }
	    else if($user['point']<$v['point_limit']){
	        $youhui['status']=0;
	        $youhui['info']="经验不足";
	        $youhui['order_status']=3;
	    }else {
	        $date_begin = to_timespan(to_date(NOW_TIME,"Y-m-d"),"Y-m-d");
			$date_end = $date_begin+24*3600;
			//验证每日限量
			$user_day_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_log where user_id = ".$user['id']." and youhui_id = ".$v['id']." and create_time > ".$date_begin." and create_time < ".$date_end);
			$youhui['day_count'] = $user_day_count;
            if($user_day_count>=$v['user_limit'] && $v['user_limit']!=0){
	            $youhui['status']=2;
	            $youhui['info']="已领取";
	            $youhui['order_status']=5;
	            
	        }else {
	            $youhui['status']=1;
	            $youhui['info']=round(($v['user_count']/$v['total_num'])*100,1);
	            $youhui['info']=$youhui['info']."%";
	            $youhui['order_status']=1;
	            if(APP_INDEX=="app"){
	                $youhui['jump']="?ctl=youhui&data_id=".$v['id'];
	                $youhui['end_jump']="?ctl=store&data_id=".$v['location_id'];
	            }else{
    	            $youhui['jump']=wap_url("index","youhui",array("data_id"=>$v['id']));
    	            $youhui['end_jump']=wap_url("index","store",array("data_id"=>$v['location_id']));
	            }
	        }
	    }
	}
	else{
        $youhui['status']=1;
        $youhui['info']=round($v['user_count']/$v['total_num']*100,1);
        $youhui['info']=$youhui['info']."%";
        $youhui['jump']=wap_url("index","youhui",array("data_id"=>$v['id']));  
	}
	
	if($begin_time&&$end_time)
		$time_str = $begin_time."至".$end_time;
	elseif($begin_time&&!$end_time)
	$time_str = $begin_time."开始";
	elseif(!$begin_time&&$end_time)
	$time_str = $end_time."结束";
	else
		$time_str = "无限期";
	$youhui['begin_time'] = $time_str;
	
	return $youhui;
}


/**
 * 格式化商品的返回数据
 * @param unknown_type $v
 */
function format_deal_item($deal)
{
//     echo 90572744%86400>0?intval(90572744/86400)."天以上":90572744/86400;exit;
	$deal['name']=htmlspecialchars_decode($deal['name']);
	$deal['sub_name']=htmlspecialchars_decode($deal['sub_name']);
    $group_id = $GLOBALS['user_info']['group_id'];
    
    if($group_id && $deal['allow_user_discount']){
        $group_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".$group_id);
        
        if($group_info && $group_info['discount']<1){
            $deal['current_price'] = round($deal['current_price']*$group_info['discount'],2);
            $data['discount_name']=$group_info['name']."优惠价";
        }else{
            $data['discount_name']="销售价";
        }
    }
    
    
	$data['id'] = $deal['id'];
	$data['name'] = $deal['name'];
	$data['sub_name'] = $deal['sub_name'];
	$data['brief'] = $deal['brief'];
	$data['max_bought'] = $deal['max_bought'];
	$data['current_price'] = round($deal['current_price'],2);
	$data['origin_price'] = round($deal['origin_price'],2);
	$data['icon'] = get_abs_img_root(get_spec_image($deal['icon'],300,182,1));
	//2016-09-18 改版数据
	//补零处理
	$f_current_price = sprintf("%01.2f",$deal['current_price']);
	$data['f_current_price'] = $f_current_price;
	$data['f_current_price_arr'] = explode(".",$f_current_price);
	$data['f_origin_price'] = sprintf("%01.2f",$deal['origin_price']);
	//倒计时
	$data['f_end_time'] = $deal['end_time'] - NOW_TIME;
	$data['supplier_id'] = $deal['supplier_id'];
	$data['f_icon'] = get_abs_img_root(get_spec_image($deal['icon'],370,370,1));
	$data['f_icon_middle'] = get_abs_img_root(get_spec_image($deal['icon'],184,184,1));
	$data['percent'] = $deal['percent']; //算好的评分百分比
	$data['phone_description'] = $deal['phone_description'];
	//end 2016-09-18 改版数据
	$data['begin_time'] = $deal['begin_time'];
	$data['end_time'] = $deal['end_time'];
	$data['time_status'] = $deal['time_status'];
	$data['now_time'] = NOW_TIME;
	$data['buy_count'] = $deal['buy_count'];
	$data['buy_type'] = $deal['buy_type'];
	$data['is_shop'] = $deal['is_shop'];	
	if($data['buy_type']==1)
		$data['return_score_show'] = abs($deal['return_score']);
	$data['deal_attr'] = $deal['deal_attr'];
	$data['avg_point'] = round($deal['avg_point'],2);
	$data['dp_count'] = $deal['dp_count'];
	$data['supplier_location_count'] = $deal['supplier_location_count'];
// 	[less_time] => 90576716
// 	[less_time_format] => 1048天以上
	$data['last_time'] = intval($deal['end_time'])>NOW_TIME?(intval($deal['end_time'])-NOW_TIME):0;
	$data['last_time_format'] = $data['last_time']%86400>0?intval($data['last_time']/86400)."天以上":$data['last_time']/86400;
	
	$deal_tags = $deal['deal_tags'];	
	$deal_tags_txt = array("0元抽奖","免预约","多套餐","可订座","折扣券","过期退","随时退","七天退","免运费","满立减","","闪送","平台自营","货到付款","厂家直发","当日达");
	$deal_tags_icon = array("&#xe61a;","&#xe622;","&#xe628;","&#xe627;","&#xe626;","&#xe621;","&#xe620;","&#xe625;","&#xe617;","&#xe623;","&#xe623;","&#xe623;","&#xe623;","&#xe623;","&#xe623;","&#xe623;");
	$data['deal_tags'] = array();
	foreach($deal_tags as $k=>$v)
	{
		$tag['k'] = $v;
		$tag['v'] = $deal_tags_txt[$v];
		$tag['icon'] = $deal_tags_icon[$v];
		$data['deal_tags'][$k] = $tag;
	}
	$images = array();
	$oimages = array();
	$f_images = array();
	foreach ($deal['image_list'] as $k=>$v){
	    $images[] = get_abs_img_root(get_spec_image($v['img'],230,140,1));
	    $oimages[] = get_abs_img_root($v['img']);
	    //2016-09-18改版数据
	    $f_images[] = get_abs_img_root(get_spec_image($v['img'],400,400,1));
	}
	$data['images'] = $images;
	$data['oimages'] = $oimages;
	$data['f_images'] = $f_images;
	$data['description']=get_abs_img_root(format_html_content_image($deal['description'],300,0,false));
	$data['notes']=get_abs_img_root(format_html_content_image($deal['notes'],220));
	$data['set_meal']=get_abs_img_root(format_html_content_image($deal['set_meal'],220));
	$data['share_url'] = $deal['share_url'];
	$data['ypoint'] = floatval($deal['ypoint']);
	$data['xpoint'] = floatval($deal['xpoint']);
	$data['buyin_app'] = intval($deal['buyin_app']);
	$data['is_fx'] = intval($deal['is_fx']);
	$data['allow_promote'] = intval($deal['allow_promote']);
	$data['auto_order'] = intval($deal['auto_order']);
	$data['expire_refund'] = intval($deal['expire_refund']);
	$data['any_refund'] = intval($deal['any_refund']);

	//库存信息
	$data['deal_stock'] = $deal['deal_stock'];
	$data['deal_attr_stock_json'] = $deal['deal_attr_stock_json'];
	$data['user_min_bought'] = $deal['user_min_bought'];
	$data['user_max_bought'] = $deal['user_max_bought'];
	$data['allow_user_discount'] = $deal['allow_user_discount'];
	$data['is_delivery'] = $deal['is_delivery'];
	return $data;
}

/**
 * 格式化部分商品的返回数据
 * @param unknown_type $v
 */
function format_short_deal_item($deal)
{
    //     echo 90572744%86400>0?intval(90572744/86400)."天以上":90572744/86400;exit;
    $group_id = $GLOBALS['user_info']['group_id'];
    
    if($group_id && $deal['allow_user_discount']){
        $group_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".$group_id);
    
        if($group_info && $group_info['discount']<1){
            $deal['current_price'] = round($deal['current_price']*$group_info['discount'],2);
        }
    }
    
    $data['id'] = $deal['id'];
    $data['name'] = $deal['name'];
    $data['sub_name'] = $deal['sub_name'];
    $data['brief'] = $deal['brief'];
    $data['current_price'] = round($deal['current_price'],2);
    $data['origin_price'] = round($deal['origin_price'],2);
    $f_current_price = sprintf("%01.2f",$deal['current_price']);
    $data['f_current_price'] = $f_current_price;
    $data['f_origin_price'] = sprintf("%01.2f",$deal['origin_price']);
    $data['f_current_price_arr'] = explode('.',$f_current_price);
    
    $data['icon'] = get_abs_img_root(get_spec_image($deal['icon'],90, 82,1));
    $data['f_icon'] = get_abs_img_root(get_spec_image($deal['icon'],185,185,1)); //740*740
    $data['buy_count'] = $deal['buy_count'];
    $data['buy_type'] = $deal['buy_type'];
    $data['deal_attr'] = $deal['deal_attr'];
    $data['avg_point'] = round($deal['avg_point'],2);
    $data['dp_count'] = $deal['dp_count'];
    $data['distance'] = format_distance_str($deal['distance']);
    return $data;
}

/**  
 * 
 * 格式化评价
 * */
function format_dp_item($v){
    $temp_arr = array();
    
    $temp_arr['id'] = $v['id'];
    $temp_arr['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"small"))?get_abs_img_root(get_muser_avatar($v['user_id'],"small")):"";
    $temp_arr['create_time'] = $v['create_time'] > 0 ?to_date($v['create_time'],'Y-m-d'):'';
    $temp_arr['content'] = $v['content'];
    $temp_arr['reply_content']= $v['reply_content']?$v['reply_content']:'';
    $temp_arr['point'] = $v['point'];
    $temp_arr['point_percent'] = round($v['point_percent'], 2);
     
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
    
    return $temp_arr;
}

/**
 * 登入状态检测
 * @param 
 * @return int  0表示未登录  1表示已登录 2表示临时登录
 */
function check_login(){
	require_once(APP_ROOT_PATH."system/model/user.php");
	return check_save_login();
}

function format_dp_list($dp_list){
    require_once(APP_ROOT_PATH."system/model/user.php");
    $format_dp_list = array();
     
    foreach($dp_list['list'] as $k=>$v){
         
        $temp_arr = array();
    
        $temp_arr['id'] = $v['id'];
        $temp_arr['create_time'] = $v['create_time'] > 0 ?to_date($v['create_time'],'Y-m-d'):'';
        $temp_arr['content'] = $v['content'];
        $temp_arr['reply_content']= $v['reply_content']?$v['reply_content']:'';
        $temp_arr['point'] = $v['point'];
         
        $uinfo = load_user($v['user_id']);
        $temp_arr['user_name'] = $uinfo['user_name'];
        //$temp_arr['user_avatar'] = get_abs_img_root(format_html_content_image($uinfo['avatar'],150));
        $temp_arr['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"big"))?get_abs_img_root(get_muser_avatar($v['user_id'],"big")):"";
        if ($v['point'] > 0) {
			$temp_arr['point_percent'] = $v['point'] / 5 * 100;
		} else {
			$temp_arr['point_percent'] = 0;
		} 
        $images = array();
        $oimages = array();
         
        if($v['images']){
            foreach ($v['images'] as $ik=>$iv){
                $images[] = get_abs_img_root(get_spec_image($iv,60,60,1));
                $oimages[] = get_abs_img_root($iv);
            }
    
        }
        $temp_arr['images'] = $images;
        $temp_arr['oimages'] = $oimages;
         
         
        $format_dp_list[] = $temp_arr;
    }
    return $format_dp_list;
}

/**
 * 获取商户权限
 * @return boolean|Ambigous <mixed, multitype:>
 */
function get_biz_account_auth(){
    $s_account_info = $GLOBALS["account_info"];
    if(es_session::get("biz_account_auth")){
        $biz_account_auth = unserialize(base64_decode(es_session::get("biz_account_auth")));
    }else{
        $nav_list = require APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/biznav_cfg.php";
        if(OPEN_WEIXIN)
        {
        	if($weixin_conf['platform_status']==1)
        	{
        	$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/wxbiznav_cfg.php";
        	$nav_list = array_merge_biznav($nav_list, $config_file);
        	}
        }
        if(defined("FX_LEVEL"))
        {
        	$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/fxbiznav_cfg.php";
        	$nav_list = array_merge_biznav($nav_list, $config_file);
        }
        if(defined("DC"))
        {
        	$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/dcbiznav_cfg.php";
        	$nav_list = array_merge_biznav($nav_list, $config_file);
        }
		if($s_account_info['is_main']){//管理员
			foreach($nav_list as $k=>$v)
			{
				foreach($v['node'] as $kk=>$vv)
				{
					$has_module[]  = $vv['module']."_".$vv['action'];

				}
			}
			
			$biz_account_auth = array_unique($has_module);
		}else{

            $result = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."supplier_account_auth WHERE supplier_account_id='".$s_account_info['id']."' order by id asc");

            if(empty($result)){
                return false;
            }
            foreach($result as $k=>$v){
				$has_module[] = $v['module']."_".$v['node'];
            }
            $biz_account_auth = array_unique($has_module);
        }
        es_session::set("biz_account_auth", base64_encode(serialize($biz_account_auth)));
    }
    return $biz_account_auth;
}

/**
 * 验证商户权限
 * @param unknown $module
 * @return boolean
 */
function check_module_auth($ctl="",$act="index")
{
	if ($ctl){
		$auth_key = $ctl."_".$act;
	}else{
		$ctl = strim(strtolower($_REQUEST['ctl']));
		$act = strim(strtolower($_REQUEST['act']));
		$act = $act?$act:"index";
	}

	$auth_key = $ctl."_".$act;
	//获取权限进行判断
	$biz_account_auth = get_biz_account_auth();

    if(!in_array($auth_key, $biz_account_auth)){
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * 分享点评的上传，上传到comment目录，按日期划分
 * 错误返回 error!=0,message错误消息
 * 正确时返回 error=0, url: ./public格式的文件相对路径  path:物理路径 name:文件名
 * thumb->preview 60x60的小图 url,path
 */
function upload_topic($_files)
{


	//上传处理
	//创建comment目录
	if (!is_dir(APP_ROOT_PATH."public/comment")) {
		@mkdir(APP_ROOT_PATH."public/comment");
		@chmod(APP_ROOT_PATH."public/comment", 0777);
	}

	$dir = to_date(NOW_TIME,"Ym");
	if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
		@mkdir(APP_ROOT_PATH."public/comment/".$dir);
		@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	}
		
	$dir = $dir."/".to_date(NOW_TIME,"d");
	if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
		@mkdir(APP_ROOT_PATH."public/comment/".$dir);
		@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	}

	$dir = $dir."/".to_date(NOW_TIME,"H");
	if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
		@mkdir(APP_ROOT_PATH."public/comment/".$dir);
		@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	}
		
	if(app_conf("IS_WATER_MARK")==1)
		$img_result = save_image_upload($_files,"file","comment/".$dir,$whs=array('preview'=>array(60,60,1,0)),1,1);
	else
		$img_result = save_image_upload($_files,"file","comment/".$dir,$whs=array('preview'=>array(60,60,1,0)),0,1);
	if(intval($img_result['error'])!=0)
	{
		return $img_result;
	}
	else
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
			syn_to_remote_image_server($img_result['file']['url']);
			syn_to_remote_image_server($img_result['file']['thumb']['preview']['url']);
		}

	}

	$data_result['error'] = 0;
	$data_result['url'] = $img_result['file']['url'];
	$data_result['path'] = $img_result['file']['path'];
	$data_result['name'] = $img_result['file']['name'];
	$data_result['thumb'] = $img_result['file']['thumb'];

	require_once(APP_ROOT_PATH."system/utils/es_imagecls.php");
	$image = new es_imagecls();
	$info = $image->getImageInfo($img_result['file']['path']);

	$image_data['width'] = intval($info[0]);
	$image_data['height'] = intval($info[1]);
	$image_data['name'] = valid_str($_FILES['file']['name']);
	$image_data['filesize'] = filesize($img_result['file']['path']);
	$image_data['create_time'] = NOW_TIME;
	$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
	$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
	$image_data['path'] = $img_result['file']['thumb']['preview']['url'];
	$image_data['o_path'] = $img_result['file']['url'];
	$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);

	$data_result['id'] = intval($GLOBALS['db']->insert_id());

	return $data_result;

}

/**
 * 
 * @param unknown_type $_files 上传的头像文件数据 file
 * @param unknown_type $id 会员ID
 * @return data: error:0无错，1错误 message:消息
 * error:0时
 * small_url 小图
 * middle_url 中图
 * big_url 大图
 */
function upload_avatar($_files,$id){

	//创建avatar临时目录
	if (!is_dir(APP_ROOT_PATH."public/avatar")) {
		@mkdir(APP_ROOT_PATH."public/avatar");
		@chmod(APP_ROOT_PATH."public/avatar", 0777);
	}
	if (!is_dir(APP_ROOT_PATH."public/avatar/temp")) {
		@mkdir(APP_ROOT_PATH."public/avatar/temp");
		@chmod(APP_ROOT_PATH."public/avatar/temp", 0777);
	}
	$upd_id = $id;

	if (is_animated_gif($_files['file']['tmp_name']))
	{
		$rs = save_image_upload($_files,"file","avatar/temp",$whs=array());

		$im = get_spec_gif_anmation($rs['file']['path'],48,48);
		$file_name = APP_ROOT_PATH."public/avatar/temp/".md5(get_gmtime().$upd_id)."_small.jpg";
		file_put_contents($file_name,$im);
		$img_result['file']['thumb']['small']['path'] = $file_name;

		$im = get_spec_gif_anmation($rs['file']['path'],120,120);
		$file_name = APP_ROOT_PATH."public/avatar/temp/".md5(get_gmtime().$upd_id)."_middle.jpg";
		file_put_contents($file_name,$im);
		$img_result['file']['thumb']['middle']['path'] = $file_name;

		$im = get_spec_gif_anmation($rs['file']['path'],200,200);
		$file_name = APP_ROOT_PATH."public/avatar/temp/".md5(get_gmtime().$upd_id)."_big.jpg";
		file_put_contents($file_name,$im);
		$img_result['file']['thumb']['big']['path'] = $file_name;
	}
	else{
		$img_result = save_image_upload($_files,"file","avatar/temp",$whs=array('small'=>array(48,48,1,0),'middle'=>array(120,120,1,0),'big'=>array(200,200,1,0)));
	}


	if(intval($img_result['error'])!=0)
	{
		$data['error'] = 1;
		$data['message'] = "上传失败";
		return $data;
	}
		
	//开始移动图片到相应位置

	$uid = sprintf("%09d", $id);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$path = $dir1.'/'.$dir2.'/'.$dir3;

	//创建相应的目录
	if (!is_dir(APP_ROOT_PATH."public/avatar/".$dir1)) {
		@mkdir(APP_ROOT_PATH."public/avatar/".$dir1);
		@chmod(APP_ROOT_PATH."public/avatar/".$dir1, 0777);
	}
	if (!is_dir(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2)) {
		@mkdir(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2);
		@chmod(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2, 0777);
	}
	if (!is_dir(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2.'/'.$dir3)) {
		@mkdir(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2.'/'.$dir3);
		@chmod(APP_ROOT_PATH."public/avatar/".$dir1.'/'.$dir2.'/'.$dir3, 0777);
	}

	$id = str_pad($id, 2, "0", STR_PAD_LEFT);
	$id = substr($id,-2);
	$avatar_file_big = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_big.jpg";
	$avatar_file_middle = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_middle.jpg";
	$avatar_file_small = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_small.jpg";


	@file_put_contents($avatar_file_big, file_get_contents($img_result['file']['thumb']['big']['path']));
	@file_put_contents($avatar_file_middle, file_get_contents($img_result['file']['thumb']['middle']['path']));
	@file_put_contents($avatar_file_small, file_get_contents($img_result['file']['thumb']['small']['path']));
	@unlink($img_result['file']['thumb']['big']['path']);
	@unlink($img_result['file']['thumb']['middle']['path']);
	@unlink($img_result['file']['thumb']['small']['path']);
	@unlink($img_result['file']['path']);

	if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_big.jpg");
		syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_middle.jpg");
		syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_small.jpg");
	}

	//上传成功更新用户头像的动态缓存
	$data['error'] = 0;
	$data['small_url'] = get_muser_avatar($upd_id,"small");
	$data['middle_url'] = get_muser_avatar($upd_id,"middle");
	$data['big_url'] = get_muser_avatar($upd_id,"big");
	return $data;
}

/**
 * 验证当前版本是否正在升级审核中，是否允许显示第三方的支付接口与登录接口
 * 返回true/false
 */
function allow_show_api()
{
	if($GLOBALS['request']['from']=="ios")
	{
		if($GLOBALS['request']['version_name']==IOS_CLIENT_VERSION&&IS_IOS_UPGRADING)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		if($GLOBALS['request']['version_name']==ANDROID_CLIENT_VERSION&&IS_ANDROID_UPGRADING)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}

/**
 * 加载首页专题
 */
function load_zt()
{
	global $is_app;
	$city_id = $GLOBALS['city']['id'];

	if($is_app||APP_INDEX=="app")
	{
		$sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '0' and city_id in (0,".intval($city_id).") and status = 1 and instr(page,1)>0 order by sort asc ";
		$zt_list = $GLOBALS['db']->getAll($sql);
		if(empty($zt_list))
		{
			$sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '1' and city_id in (0,".intval($city_id).") and status = 1 and instr(page,1)>0 order by sort asc ";
			$zt_list = $GLOBALS['db']->getAll($sql);
		}
	}
	else
	{
		$sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '1' and city_id in (0,".intval($city_id).") and status = 1 and instr(page,1)>0 order by sort asc ";
		$zt_list = $GLOBALS['db']->getAll($sql);
	}

	$html = $GLOBALS['cache']->get("MOBILE_INDEX_ZT_".intval($city_id)."_".APP_INDEX);

	if($html===false)
	{
		$html = "";
		if($zt_list)
			$html .= $GLOBALS['zt_tmpl']->fetch("inc/".APP_INDEX."_header.html");
		foreach($zt_list as $k=>$v)
		{
			if($is_app||APP_INDEX=="app")
			{
				$sql = " select * from ".DB_PREFIX."m_adv where position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$v['id'];
				$zt_layout_list = $GLOBALS['db']->getAll($sql);
				if(empty($zt_layout_list))
				{
					$sql = " select * from ".DB_PREFIX."m_adv where position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$v['id'];
					$zt_layout_list = $GLOBALS['db']->getAll($sql);
				}
			}			
			else
			{
				$sql = " select * from ".DB_PREFIX."m_adv where position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$v['id'];
				$zt_layout_list = $GLOBALS['db']->getAll($sql);
			}

			//先输出推荐位的变量
			$v['data'] = unserialize($v['data']);

			$GLOBALS['zt_tmpl']->assign("url",getHtmlUrl($v));
			$GLOBALS['zt_tmpl']->assign("zt_page",1);
			$GLOBALS['zt_tmpl']->assign("title",$v['zt_title']);

			//开始输出每个广告位的变量
			foreach($zt_layout_list as $kk=>$vv)
			{
				$vv['data'] = unserialize($vv['data']);
				$GLOBALS['zt_tmpl']->assign($vv['zt_position']."_a",getHtmlUrl($vv));
				$GLOBALS['zt_tmpl']->assign($vv['zt_position']."_img",$vv['img']);
			}

			$html .= $GLOBALS['zt_tmpl']->fetch($v['zt_moban']);
			foreach($zt_layout_list as $kk=>$vv)
			{
				$GLOBALS['zt_tmpl']->assign($vv['zt_position']."_a","");
				$GLOBALS['zt_tmpl']->assign($vv['zt_position']."_img","");
			}
		}
		$GLOBALS['cache']->set("MOBILE_INDEX_ZT_".intval($city_id)."_".APP_INDEX,$html);
	}

	return $html;
}

//解析URL标签
// $str = u:wap#index|id=10&name=abc
if(!function_exists('parse_url_tag')){
function parse_url_tag($str)
{
    $key = md5("URL_TAG_".$str);
    if(isset($GLOBALS[$key]))
    {
        return $GLOBALS[$key];
    }

    $url = load_dynamic_cache($key);
    $url=false;
    if($url!==false)
    {
        $GLOBALS[$key] = $url;
        return $url;
    }
    $str = substr($str,2);
    $str_array = explode("|",$str);
    $app_index = $str_array[0];
    $route = $str_array[1];
    $param_tmp = explode("&",$str_array[2]);
    $param = array();

    foreach($param_tmp as $item)
    {
        if($item!='')
            $item_arr = explode("=",$item);
        if($item_arr[0]&&$item_arr[1])
            $param[$item_arr[0]] = $item_arr[1];
    }
    if(APP_INDEX=='app')
        $param['show_prog'] = 1;
    $GLOBALS[$key]= wap_url($app_index,$route,$param);
    set_dynamic_cache($key,$GLOBALS[$key]);
    return $GLOBALS[$key];
}
}
/**
 * 加载单个专题
 */
function load_zt_unit($zt_moban,$page=1)
{
    global $is_app;

    $city_id = $GLOBALS['city']['id'];

    if(APP_INDEX=="app")
    {
        $sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '0' and city_id in (0,".intval($city_id).") and status = 1  and zt_moban='".$zt_moban."' and instr(page,{$page})>0 order by sort asc limit 1";
        $zt_unit = $GLOBALS['db']->getRow($sql);
//         if(empty($zt_unit))
//         {
//             $sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '1' and city_id in (0,".intval($city_id).") and status = 1 and zt_moban='".$zt_moban."' and instr(page,{$page})>0 order by sort asc limit 1";
//             $zt_unit = $GLOBALS['db']->getRow($sql);
//         }
    }
    else
    {

        $sql = " select * from ".DB_PREFIX."m_zt where mobile_type = '1' and city_id in (0,".intval($city_id).") and status = 1 and zt_moban='".$zt_moban."' and instr(page,{$page})>0 order by sort asc limit 1";
        $zt_unit = $GLOBALS['db']->getRow($sql);
    }
    $html = $GLOBALS['cache']->get("MOBILE_INDEX_ZT_".intval($city_id)."_".APP_INDEX."_".$zt_moban);


    if($html===false)
    {
        $html = "";
        if($zt_unit){
            //$html .= $GLOBALS['zt_tmpl']->fetch("inc/".APP_INDEX."_header.html");

            if($is_app||APP_INDEX=="app")
            {

                $sql = " select * from ".DB_PREFIX."m_adv where  position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$zt_unit['id'];
                $zt_layout_list = $GLOBALS['db']->getAll($sql);
                if(empty($zt_layout_list))
                {
                    $sql = " select * from ".DB_PREFIX."m_adv where  position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$zt_unit['id'];
                    $zt_layout_list = $GLOBALS['db']->getAll($sql);
                }
            }
            else
            {
                $sql = " select * from ".DB_PREFIX."m_adv where  position = 2 and city_id in (0,".intval($city_id).") and status = 1 and zt_id = ".$zt_unit['id'];
                $zt_layout_list = $GLOBALS['db']->getAll($sql);
            }

            //先输出推荐位的变量
            $zt_unit['data'] = unserialize($zt_unit['data']);
            $GLOBALS['zt_tmpl']->assign("url",getHtmlUrl($zt_unit));
            $GLOBALS['zt_tmpl']->assign("zt_page",1);
            
            $GLOBALS['zt_tmpl']->assign("title",$zt_unit['zt_title']);

            // $GLOBALS['zt_tmpl']->assign("title",$zt_unit['name']);
            //开始输出每个广告位的变量
            foreach($zt_layout_list as $kk=>$vv)
            {
                $vv['data'] = unserialize($vv['data']);
                $GLOBALS['zt_tmpl']->assign($vv['zt_position']."_a",getHtmlUrl($vv));
                $GLOBALS['zt_tmpl']->assign($vv['zt_position']."_img",$vv['img']);
            }

            $html .= $GLOBALS['zt_tmpl']->fetch($zt_unit['zt_moban']);
            foreach($zt_layout_list as $kk=>$vv)
            {
                $GLOBALS['zt_tmpl']->assign($vv['zt_position']."_a","");
                $GLOBALS['zt_tmpl']->assign($vv['zt_position']."_img","");
            }
            
        }
        $GLOBALS['cache']->set("MOBILE_INDEX_ZT_".intval($city_id)."_".APP_INDEX,$html);
    }

    return $html;
}
/**
 * 返回菜单列表
 * @param int APP_INDEX 是否是手机端，0为WAP端，1为手机端
 * @param int $city_id 城市ID
 * @param int $page 菜单显示的页面，1为首页，2为团购首页，3.为商城首页 ，4为积分商城
 */
function mindex_cate_menu($city_id,$page=1){
    
    //首页菜单列表
    $indexs_list = $GLOBALS['cache']->get("WAP_INDEX_INDEX_PAGE".$page.'_'.intval($city_id).'_'.APP_INDEX);
    if($indexs_list===false)
    {
        /*if($is_app)
        {
            $indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 0 and city_id in (0,".intval($city_id).") and find_in_set('".$page."', page) order by sort asc");
            if(empty($indexs))
                $indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 1 and city_id in (0,".intval($city_id).") and find_in_set('".$page."', page) order by sort asc");
        }
        else {
            $indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 1 and city_id in (0,".intval($city_id).") and find_in_set('".$page."', page)  order by sort asc");
        }*/

        $mobile_type = APP_INDEX=='app' ? 0 : 1;
        $sql = " select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = ".$mobile_type." and city_id in (0,".intval($city_id).") and find_in_set('".$page."', page)  order by sort asc";
        $indexs = $GLOBALS['db']->getAll($sql);
        $indexs_list = array();
        foreach($indexs as $k=>$v)
        {
            $indexs_list[$k]['id'] = $v['id'];
            $indexs_list[$k]['name'] = $v['name'];
            $indexs_list[$k]['icon_name'] = $v['vice_name'];//图标名 http://fontawesome.io/icon/bars/
            $indexs_list[$k]['color'] = $v['desc'];//颜色
            $indexs_list[$k]['bg_color'] = $v['iconbgcolor'];//背景颜色
            $indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
            $indexs_list[$k]['ctl'] = $v['ctl'];
            $indexs_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'], 100, 100,1));
            $indexs_list[$k]['type'] = $v['type'];
        }
    
    
        $GLOBALS['cache']->set("WAP_INDEX_INDEX_PAGE".$page.'_'.intval($city_id).'_'.APP_INDEX,$indexs_list,300);
    }
    
    return $indexs_list;
    
}

if(!function_exists('getWebAdsUrl')){
    function getWebAdsUrl($data){
        //2:URL广告;9:团购列表;10:商品列表;11:活动列表;12:优惠列表;14:团购明细;15:商品明细;17:优惠明细;22:商家列表;23：商家明细; 24:门店自主下单

        if($data['ctl']=="url")
        {
            $url = $data['data']['url'];
            if(empty($url))
            {
                $url = "javascript:void(0);";
            }
            else
            {
                $url = "javascript:App.app_detail(0,'".$data['data']['url']."');";
            }
        }
        else
            $url = wap_url("index",$data['ctl'],$data['data']);

        return $url;

    }
}


function getHtmlUrl($data){
	//2:URL广告;9:团购列表;10:商品列表;11:活动列表;12:优惠列表;14:团购明细;15:商品明细;17:优惠明细;22:商家列表;23：商家明细; 24:门店自主下单

	if($data['ctl']=="url")
	{
		$url = $data['data']['url'];
		if(empty($url))
		{
			$url = "javascript:void(0);";
		}
		else
		{
			if(APP_INDEX=="wap"){
				//$url = $data['data']['url'];
				
				$url = "javascript:open_url('".$data['data']['url']."');";
			}else{
				$url = "javascript:App.app_detail(0,'".$data['data']['url']."');";
			}
		}
	}
	else
	{
		if(APP_INDEX=="wap")
			$url = SITE_DOMAIN.wap_url("index",$data['ctl'],$data['data']);
		else
		{
			static $nav_cfg;
			if($nav_cfg===null)
				$nav_cfg = require_once(APP_ROOT_PATH."system/mobile_cfg/".APP_TYPE."/webnav_cfg.php");
				
			if(defined("FX_LEVEL"))
			{
				$config_file = APP_ROOT_PATH."system/mobile_cfg/".APP_TYPE."/fxwebnav_cfg.php";
				$nav_cfg = array_merge_mobile_cfg($nav_cfg, $config_file);
			}
			if(OPEN_WEIXIN)
			{
				if($weixin_conf['platform_status']==1)
				{
					$config_file = APP_ROOT_PATH."system/mobile_cfg/".APP_TYPE."/wxwebnav_cfg.php";
					$nav_cfg = array_merge_mobile_cfg($nav_cfg, $config_file);
				}
			}
			if(defined("DC"))
			{
				$config_file = APP_ROOT_PATH."system/mobile_cfg/".APP_TYPE."/dcwebnav_cfg.php";
				$nav_cfg = array_merge_mobile_cfg($nav_cfg, $config_file);
			}
			$key = $nav_cfg[APP_INDEX]['nav'][$data['ctl']]['field'];
			$id = intval($data['data'][$key]);
			$url = "javascript:App.app_detail(".$data['type'].",".$id.")";
		}
	}

	return $url;

}

function load_dist()
{
    $distOldInfo = es_session::get('dist_info');
    $distName = $distOldInfo['username'];
    $dist_sql = 'SELECT * FROM '.DB_PREFIX.'distribution where username="'.$distName.'"';
    $distInfo = $GLOBALS['db']->getRow($dist_sql);
    es_session::set('dist_info', $distInfo);
}




/**
 * 将购物车未指定会员的商品根据session_id分配会员
 * @param  int $location_id 商户id
 * @param  int $user_id     会员id
 * @return null              
 */
function update_dc_cart($location_id, $user_id)
{
	$cart_info = array('user_id' => $user_id);
	$GLOBALS['db']->autoExecute(DB_PREFIX.'dc_cart', $cart_info, $mode='UPDATE', 'session_id="'.es_session::id().'" AND location_id='.$location_id.' AND user_id=0', 'SILENT');
}

?>