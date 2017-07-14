<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

$lang = array(
		'DEAL_ERROR_1'	=>	'团购进行中',
		'DEAL_ERROR_2'	=>	'已过期',
		'DEAL_ERROR_3'	=>	'未开始',
		'DEAL_ERROR_4'	=>	'产品剩余库存不足',
		'DEAL_ERROR_5'	=>	'用户最小购买数不足',
		'DEAL_ERROR_6'	=>	'用户最大购买数超出',
);

class cartApiModule extends MainBaseApiModule
{
	
	/**
	 * 获取购物车列表
	 * 
	 * 输入:
	 * 无
	 * 
	 * 输出:
	 * is_score: int 当前购物车中的商品类型 0:普通商品，展示时显示价格 1:积分商品，展示时显示积分
	 * cart_list: object 购物车列表内容，结构如下
	 * Array
        (
            [478] => Array key [int] 购物车表中的主键
                (
                    [id] => 478 [int] 同key
                    [return_score] => 0 [int] 当is_score为1时单价的展示
                    [return_total_score] => 0 [int] 当is_score为1时总价的展示
                    [unit_price] => 108 [float] 当is_score为0时单价的展示
                    [total_price] => 108 [float] 当is_score为0时总价的展示
                    [number] => 1 [int] 购买件数
                    [deal_id] => 57 [int] 商品ID
                    [attr] => 287,290 [string] 购买商品的规格ID组合，用逗号分隔的规格ID
                    [name] => 桥亭活鱼小镇 仅售88元！价值100元的代金券1张 [9点至18点,2-5人套餐] [string] 商品全名，包含属性
                    [sub_name] => 88元桥亭活鱼小镇代金券 [9点至18点,2-5人套餐] [string] 商品缩略名，包含属性
                    [max] => int 最大购买量 加减时用
                    [icon] => string 商品图标 140x85
                )
		)
		
	 * total_data: array 购物车总价统计,结构如下
	 *	Array
        (
            [total_price] => 108 [float] 当is_score为0时的总价显示
            [return_total_score] => 0 [int] 当is_score为1时的总价显示
        )
     *  user_login_status:int 用户登录状态(1 已经登录/0 用户未登录) 该接口不返回临时登录状态，未登录时使用手机短信验证自动注册登录，已登录时判断is_mobile
     *  has_mobile: int 是否有手机号 0无 1有
	 */
	public function index()
	{
		require_once(APP_ROOT_PATH."system/model/cart.php");

		$user_info=$GLOBALS['user_info'];
		$user_id=$user_info['id'];
		//把购物车中用户当时设为无效的购物记录设为有效
		//$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=1 where user_id = " . $user_info['id'] );
		
		if( $GLOBALS['request']['from']=='wap'){
		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		
		$cart_result = load_cart_list(0,$wap_show_disable=true);
		$cart_list_o = $cart_result['cart_list'];
		$cart_list_data = array();
		
		$total_data_o = $cart_result['total_data'];		
		$is_score = 0;
		require_once(APP_ROOT_PATH."system/model/deal.php");
		foreach($cart_list_o as $k=>$v)
		{
			$bind_data = array();
			$bind_data['id'] = $v['id'];
			if($v['buy_type']==1)
			{
				$is_score = 1;
				$bind_data['return_score'] = abs($v['return_score']);
				$bind_data['return_total_score'] = abs($v['return_total_score']);
				$bind_data['unit_price'] = 0;
				$bind_data['total_price'] = 0;
			}
			else
			{
				$bind_data['return_score'] = 0;
				$bind_data['return_total_score'] = 0;
				$bind_data['unit_price'] = round($v['unit_price'],2);
				$bind_data['total_price'] = round($v['total_price'],2);
			}
			$bind_data['number'] = $v['number'];
			$bind_data['supplier_id'] = $v['supplier_id'];
			$bind_data['deal_id'] = $v['deal_id'];
			
			
			$bind_data['attr'] = $v['attr'];
			$bind_data['attr_str'] = $v['attr_str'];
			if($bind_data['attr']){
			    $deal_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal where id=".$v['deal_id']);
			    $bind_data['deal_name']=$deal_name;
			    $max_bought=$GLOBALS['db']->getRow("select stock_cfg from ".DB_PREFIX."attr_stock where deal_id=".$v['deal_id']." and attr_str='".$v['attr_str']."'");
			    /* $deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and o.order_status = 0 and oi.deal_id = ".$v['deal_id']." and o.is_delete = 0 and oi.attr_str like '%".$v['attr_str']."%'"));
			    $num=$max_bought['max_bought']-$deal_user_unpaid_count; */
			    if($max_bought['stock_cfg']>0){
			        $bind_data['max_bought']=$max_bought['stock_cfg'];
			        /* if($num>0){
			            $bind_data['max_bought'] = $num;
			        }else {
			            $bind_data['max_bought'] = 0;
			        } */
			    }else{
			        $bind_data['max_bought']=-1;
			    }
			}else {
			    
			    $max_bought=$GLOBALS['db']->getRow("select max_bought from ".DB_PREFIX."deal where id=".$v['deal_id']);
			    if($max_bought['max_bought']>0){
			        $bind_data['max_bought']=$max_bought['max_bought'];
			    }
			}
			if($v['user_min_bought']){
			     $bind_data['user_min_bought'] = $v['user_min_bought'];
			}
			if($v['user_max_bought']){
			     $bind_data['user_max_bought'] = $v['user_max_bought'];
			}
			$bind_data['is_effect'] = $v['is_effect'];
			$bind_data['is_disable'] = $v['is_disable'];
			$deal_info = get_deal($v['deal_id']);
			$bind_data['deal_attr_stock_json'] = $deal_info['deal_attr_stock_json'];
			$bind_data['deal_attr'] = $deal_info['deal_attr'];
			//如果商家或者平台有重新编辑过商品属性，更新购物车商品属性信息
			//sys_cart_attr($bind_data['deal_id'],$bind_data['attr_str']);
			//$bind_data['attr_str_format'] = $GLOBALS['db']->getOne("select group_concat(name) from ".DB_PREFIX."deal_attr where deal_id=".$v['deal_id']." and id in (".$v['attr'].") group by deal_id");
			
			$bind_data['name'] = $v['name'];
			$bind_data['sub_name'] = $v['sub_name'];
			$bind_data['max'] = 100;
			$bind_data['allow_user_discount'] = $v['allow_user_discount'];
			if($is_wap){
				$bind_data['icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1)) ;
				$bind_data['f_icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1)) ;
			}else{
				$bind_data['icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,1)) ;
				$bind_data['f_icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,1)) ;
			}
			
			$cart_list_data[] = $bind_data;
		}
		$root = array();

		$total_data = array();
		
		if($is_score)
		{
		    $total_data['total_num'] = $total_data_o['total_num'];
			$total_data['total_price'] = 0;
			$total_data['return_total_score'] = abs($total_data_o['return_total_score']);
		}		
		else
		{
		    $total_data['total_num'] = $total_data_o['total_num'];
			$total_data['total_price'] = round($total_data_o['total_price'],2);
			$total_data['return_total_score'] = 0;
		}		

		$root['total_data'] = $total_data;
		$root['is_score'] = $is_score;		
		
		$user_login_status = check_login();
		
		
		if($GLOBALS['user_info']['mobile']=="")
			$root['has_mobile'] = 0;
		else
			$root['has_mobile'] = 1;
		
		if($user_login_status==LOGIN_STATUS_TEMP)
		{
			$user_login_status = LOGIN_STATUS_LOGINED; //购物车页不存在临时状态
		}
		
		$root['user_login_status'] = $user_login_status;
		$root['page_title'] = "购物车";
		
		$user_discount_percent = 1;
		if (!empty($user_info['id'])) {
			$user_discount_percent = getUserDiscount($user_info['id']);
		}
		if($cart_list_data)
		{
		
		    
		    $goods_list = $cart_list_data;
		    $cart_list=array();
		    foreach($cart_list_data as $k=>$v){
		        //最大购买量
		        //本团购当前会员已付款的数量
		        $deal_user_paid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status = 2 and oi.deal_id = ".$v['deal_id']." and o.is_delete = 0"));
		        //本团购当前会员未付款的数量
		        $deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and o.order_status = 0 and oi.deal_id = ".$v['deal_id']." and o.is_delete = 0"));
		        if($v['user_max_bought']>0){
		            $v['user_max_bought']=$v['user_max_bought']-$deal_user_paid_count-$deal_user_unpaid_count;
		            if($v['user_max_bought']<=0){
		                $v['user_max_bought']=1;
		            }
		        }
		
		        $attr = array();
		        $attr['attr_id'] = explode($v['attr']);
		        $attr['attr_str'] = $v['attr_str'];
		        $check_info = check_deal_status($v['deal_id'],$attr,0);
        
		        if($check_info['stock']<10 && $check_info['stock']!=-1){
		            $v['stock'] = $check_info['stock'] ;
		        }
		        $unit_price = $v['unit_price'];
		        if ($v['allow_user_discount']) {
		        	$unit_price = round($unit_price * $user_discount_percent, 2);
		        }
		        $v['unit_price'] = $unit_price;
		        $bai = floor($unit_price);
		        $fei = str_pad(round(($unit_price - $bai) * 100,2),2,'0',STR_PAD_LEFT);
		        $v['unit_price_format'] = "&yen; <i class='j-goods-money'>" .$bai.".</i>".$fei;
		        $v['url'] = wap_url("index","deal",array("data_id"=>$v['deal_id']));
		        $v['check_info'] = $check_info;
		        $v['allow_promote'] = intval($GLOBALS['db']->getOne("select allow_promote from ".DB_PREFIX."deal where id=".$v['deal_id']));
		        if($check_info['status']==0){
		            $root['total_data']['total_price']-=$v['total_price'];
		            $root['total_data']['total_num']-=1;
		            $cart_list['disable'][$v['id']]=$v;
		        }else{
		            if($v['is_disable']==1){
		                //$data['total_data']['total_price']-=$v['total_price'];
		                //$data['total_data']['total_num']-=1;
		                $v['check_info']['status']=0;
		                $v['check_info']['info']='商品更改属性，商品失效';
		                $cart_list['disable'][$v['id']]=$v;
		            }else{
		
		                $cart_list[$v['supplier_id']][]=$v;
		            }
		        }
		
		    }
		    /*
		     $allow_promote = 1; //默认为支持促销接口
		     foreach($goods_list as $k=>$v)
		     {
		     $allow_promote = $GLOBALS['db']->getOne("select allow_promote from ".DB_PREFIX."deal where id = ".$v['deal_id']);
		     if($allow_promote == 0)
		     {
		     break;
		     }
		     }
		     */
		    //满减计算
		    if(APP_INDEX=='app'){
		        $promote_where=" class_name in ('Discountamount','Appdiscount') ";
		    }else{
		        $promote_where=" class_name='Discountamount'";
		    }
		    $promote_where .= ' AND type = 0';
		    $promote_obj = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."promote where ".$promote_where);
		    $root['total_data']['discount_amount'] = 0;
		    $total=$root['total_data']['total_price'];
		    if($promote_obj){
		        $promote_arr=array();
		        foreach ($promote_obj as $t => $v){
		            $promote_arr[$t]=unserialize($v['config']);
		            if($total >= $promote_arr[$t]['discount_limit']){
		                $root['total_data']['total_price']-=$promote_arr[$t]['discount_amount'];
		                $root['total_data']['discount_amount'] += $promote_arr[$t]['discount_amount'];
		            }
		        }
		        $root['promote_cfg'] = $promote_arr;
		    }else{
		        $root['promote_cfg'] = array();		       
		    }

		    $cart_list_new=array();

		    foreach($cart_list as $k=>$v){
		        $cart_list_new[$k]['id']=$k;
		        $supplier_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$k);
		        
		        
		        $youhui_sql="select count(*) from ".DB_PREFIX."youhui where 
		                    youhui_type=2 and is_effect=1 and (total_num>user_count or total_num=0) and (end_time>".NOW_TIME." or end_time=0)
	                        and (begin_time=0 or begin_time<".NOW_TIME.") and supplier_id=".$k;
		        
		        $cart_list_new[$k]['youhui_count']=$GLOBALS['db']->getOne($youhui_sql);
		        
		        if($k==='disable'){
		            $cart_list_new[$k]['supplier_name']='失效商品';
		        }else{
		
		            $cart_list_new[$k]['supplier_name']=$supplier_name?$supplier_name:'平台自营';
		        }
		        sort($v);
		        $cart_list_new[$k]['list'] =$v;
		        $is_effect=1;
		        foreach($v as $kk=>$vv){
		            if($vv['is_effect']==0){
		                $is_effect=0;
		                break;
		            }
		        }
		        $cart_list_new[$k]['is_effect'] =$is_effect;
		    }
		//print_r($cart_list_new);exit;
		    rsort($cart_list_new);
		
		    $root['cart_list'] = $cart_list_new?$cart_list_new:null;
		
		    $bai = floor($root['total_data']['total_price']);
		    $fei = str_pad(round(($root['total_data']['total_price'] - $bai) * 100,2),2,'0',STR_PAD_LEFT);
		    $root['total_data']['total_price_format'] = "&yen; <i class='j-price-int'>" .$bai."</i>.<em class='j-price-piont'>".$fei."</em>";
		}else{
		
		    $root['total_data']['total_price_format'] = "&yen; <i class='j-price-int'>0</i>.<em class='j-price-piont'>00</em>";
		}
		
		

		return output($root);
	}
	
	public function get_youhui(){
	    require_once(APP_ROOT_PATH."system/model/cart.php");
	    
	    $user_info=$GLOBALS['user_info'];
	    $user_id=$user_info['id'];
	    
	    $supplier_id=intval($GLOBALS['request']['id']);
	    
	    $supplier_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$supplier_id);
	    
	    $root['supplier_name']=$supplier_name?$supplier_name:"平台自营";
	    
	    $youhui_sql="select id,name,youhui_value,valid_type,start_use_price,expire_day,use_begin_time,use_end_time,user_limit,user_everyday_limit
		                    from ".DB_PREFIX."youhui where youhui_type=2 and is_effect=1 and (total_num>user_count or total_num=0) and (end_time>".NOW_TIME." or end_time=0)
	                        and (begin_time=0 or begin_time<".NOW_TIME.") and supplier_id=".$supplier_id;
	    
	    $youhui_list=$GLOBALS['db']->getAll($youhui_sql);
	    
	    foreach ($youhui_list as $tt => $vv){
	        if($vv['valid_type']==1){
	            if($vv['use_begin_time']){
	                $use_info=to_date($vv['use_begin_time'],'Y-m-d H:i')."-";
	            }
	            $use_info.=to_date($vv['use_end_time'],'Y-m-d H:i')?to_date($vv['use_end_time'],'Y-m-d H:i'):"永久";
	        }elseif ($vv['valid_type']==2){
	            $use_info=$vv['expire_day']?"领取之日起".$vv['expire_day']."日有效":"永久";
	        }
	        $youhui_list[$tt]['time_info']=$use_info;
	        
	        if($vv['start_use_price']>0){
	            $youhui_list[$tt]['use_info']="订单满".$vv['start_use_price']."元可用";
	        }else{
	            $youhui_list[$tt]['use_info']="无使用限制";
	        }
	    
	        if($user_id){
	            $today=strtotime(date("Y-m-d"),NOW_TIME);
	            $end = $today+60*60*24;
	            $use_day_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_log where user_id = ".$user_id." and youhui_id=".$vv['id']." and create_time>=".$today." and create_time<".$end);
	            $use_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_log where user_id = ".$user_id." and youhui_id=".$vv['id']);
	            
	            if($vv['user_everyday_limit']<=$use_day_count && $vv['user_everyday_limit']!=0){
	                $youhui_list[$tt]['status']=0;
	                $youhui_list[$tt]['status_info']="已领取";
	            }
	            elseif ($vv['user_limit']<=$use_count && $vv['user_limit']!=0){
	                $youhui_list[$tt]['status']=0;
	                $youhui_list[$tt]['status_info']="已领取";
	            }
	            else {
	                $youhui_list[$tt]['status']=1;
	                $youhui_list[$tt]['status_info']="领取";
	            }
	            
	    
	        }else{
	            $youhui_list[$tt]['status']=1;
	            $youhui_list[$tt]['status_info']="领取";
	        }
	    }
	    
	    $root['list']=$youhui_list;

	    return output($root);
	}

    /**
     * 获取推荐商品
     */
	public function get_recommend_list(){
        require_once(APP_ROOT_PATH."system/model/deal.php");
        $city_id = $GLOBALS['city']['id'];
        //购物车为空时，调用推荐商品
        $result = get_deal_list(10,$type=array(DEAL_ONLINE,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>$city_id),""," d.is_recommend = 1 and d.buy_type <> 1 and ( d.is_shop = 1 or ( d.is_shop = 0 AND d.is_location=1 and ((is_coupon = 1 AND (coupon_end_time >= ".NOW_TIME." or coupon_end_time=0)) or is_coupon=0) )) ");

        $indexs_deal_rs = $result['list'];


        foreach($indexs_deal_rs as $k=>$v){
            $indexs_deal_rs[$k] = format_deal_list_item($v);
        }
        $root['ref_list'] = $indexs_deal_rs;

        return output($root);
    }
	
	/**
	 * 加入购物车接口
	 * 
	 * 输入： 
	 * id:int 商品id
	 * deal_attr: array 结构如下
	 * Array
	 * (
	 *     [属性组ID] => 11 int 属性值ID
	 * )
	 
	 
	 * =======新增两个参数============
	 * @param bool $outputReturn 是否以output返回
	 * @param array $param 该值不为空，则加入购物车的id,attr以此为准，否则取$_REQUEST
	 * @param
				 *$param = Array
				 * (
				 	   [id]	  =>	商品id int
				 *     [attr] => 	Array(
										[属性组ID] => 11 int 属性值ID
									)
				 * )
	 * =======//新增两个参数============ *
	 
	  
	 * 
	 * 输出：
	 * status: int 状态 0错误 1加入成功 -1未登录需要登录
	 * info: string 状态为1时该值为空，否则为出错的提示
	 */
	public function addcart($outputReturn=true,$param=array())
	{
	
		$root = array();
		
		//========
		require_once(APP_ROOT_PATH.'system/model/cart.php');
		require_once(APP_ROOT_PATH.'system/model/deal.php');
		
		if( !empty($param)&&!empty($param['id']) ){
			$id = intval($param['id']);
			$deal_attr_req = $param['attr'];
			$is_wap=$param['is_wap'];
			$GLOBALS['request']['num']=$param['num'];
		}else{
			$id = intval($GLOBALS['request']['id']);
			if($GLOBALS['request']['from']=="app"){
				$GLOBALS['request']['deal_attr']=json_decode($GLOBALS['request']['deal_attr'],true);
			}
			$deal_attr_req = $GLOBALS['request']['deal_attr'];
		}		
		if( $GLOBALS['request']['from']=='wap'){
			$is_wap=true;
		}else{
			$is_wap=false;
		}
		$deal_attr = array();
		foreach ($deal_attr_req as $k=>$v)
		{
			$sv = intval($v);
			if($sv)
			$deal_attr[$k] = intval($sv);
		}
		
		$user_login_status = check_login();
		
		$deal_info = get_deal($id);
		if(!$deal_info)
		{
			if($outputReturn){
				return output("",0,"没有可以购买的产品");
			}else{
				return array('status'=>0,'info'=>'没有可以购买的产品');
			}
			
		}
		
		if($deal_info['buyin_app']==1&&APP_INDEX!="app")
		{
		    if($outputReturn){
		        return output("",0,"仅限APP购买，请下载APP");
		    }else{
		        return array('status'=>0,'info'=>'仅限APP购买，请下载APP');
		    }
		    	
		}
		
		
		if(($deal_info['is_lottery']==1||$deal_info['buy_type']==1))
		{
			if($user_login_status==LOGIN_STATUS_NOLOGIN)
			{
				if($outputReturn){
					$root['user_login_status']=$user_login_status;
					return output($root,-1,"请先登录");
				}else{
					return array('status'=>-1,'info'=>'请先登录');
				}
			}
		}
			
		$check = check_deal_time($id);
		if($check['status'] == 0)
		{
			$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
			if($outputReturn){
				return output($root,0,$res['info']);
			}else{
				return array('status'=>0,'info'=>$res['info']);
			}
			
		}			
		
		if(count($deal_attr)!=count($deal_info['deal_attr']))
		{
			$res['info'] = "请选择商品规格";
			if($outputReturn){
				return output($root,0,$res['info']);
			}else{
				return array('status'=>0,'info'=>'请选择商品规格');
			}
			
		}
		else
		{
			//加入购物车处理，有提交属性， 或无属性时
			$attr_str = '0';
			$attr_name = '';
			$attr_name_str = '';
			if($deal_attr)
			{				
				$attr_str = implode(",",$deal_attr);
				$attr_names = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_attr where id in(".$attr_str.")");
				$attr_name = '';
				foreach($attr_names as $attr)
				{
					$attr_name .=$attr['name'].",";
					$attr_name_str.=$attr['name'];
				}
				$attr_name = substr($attr_name,0,-1);
			}
			$verify_code = md5($id."_".$attr_str);
			$session_id = es_session::id();
		
			if(app_conf("CART_ON")==0)
			{
				$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where user_id = '".intval($GLOBALS['user_info']['id'])."'");
				
			}
		
			
			$cart_data=get_cart_type($id);
			if($cart_data['in_cart']==0){  //如果是不经过购物车的商品下单，先删掉之前的购物车历史记录
			    $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where user_id = '".intval($GLOBALS['user_info']['id'])."' and deal_id=".$id);
			    
			}
			$cart_result = load_cart_list($id);
			foreach($cart_result['cart_list'] as $k=>$v)
			{
				if($v['verify_code']==$verify_code)
				{
					$cart_item = $v;
				}
			}
			$add_number = $number = $GLOBALS['request']['num']?$GLOBALS['request']['num']:1; 
		
		
			//开始运算购物车的验证
			if($cart_item)
			{
		
// 				$check = check_deal_number($cart_item['deal_id'],$add_number);
// 				if($check['status']==0)
// 				{
// 					$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];						
// 					return output($root,0,$res['info']);
// 				}
		
				//属性库存的验证
				$attr_setting_str = '';
				if($cart_item['attr']!='')
				{
					$attr_setting_str = $cart_item['attr_str'];
				}
		
 				if($attr_setting_str!='')
 				{
 					$check = check_deal_number_attr($cart_item['deal_id'],$attr_setting_str,$add_number);
					if($check['status']==0)
 					{
 						$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
 						return output($root,0,$res['info']);
 					}
 				}
 				$check = check_deal_number($deal_info['id'],$add_number,false);
 				if($check['status']==0)
 				{
 					$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
 					return output("",0,$res['info']);
 				}
 				
				//属性库存的验证
			}
			else //添加时的验证
			{
// 				$check = check_deal_number($deal_info['id'],$add_number);
// 				if($check['status']==0)
// 				{
// 					$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
// 					return output($root,0,$res['info']);
// 				}
		
				//属性库存的验证
				$attr_setting_str = '';
				if($attr_name_str!='')
				{
					$attr_setting_str =$attr_name_str;
				}
		
		
					
 				if($attr_setting_str!='')
 				{
 					$check = check_deal_number_attr($deal_info['id'],$attr_setting_str,$add_number);
 					if($check['status']==0)
 					{
		
 						$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
 						return output("",0,$res['info']);
 					}
 				}
 				if($cart_data['in_cart']==0){
 					$check = check_deal_number($deal_info['id'],$add_number);
 				}else{
 					$check = check_deal_number($deal_info['id'],$add_number,false);
 				}
	 			if($check['status']==0)
	 			{
	 				$res['info'] = $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
	 				return output("",0,$res['info']);
	 			}
				//属性库存的验证
			}
		
			if($deal_info['return_score']<0)
			{
				//需要积分兑换
				$user_score = intval($GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id'])));
				if($user_score < abs(intval($deal_info['return_score'])*$add_number))
				{		
					$res['info'] = $check['info']." "."积分不足";
					if($outputReturn){
						return output($root,0,$res['info']);
					}else{
						return array('status'=>0,'info'=>$res['info']);
					}
				}
			}
		
			//验证over

			if(!$cart_item || $cart_data['in_cart']==0)
			{
				// $attr_price = $GLOBALS['db']->getOne("select sum(price) from ".DB_PREFIX."deal_attr where id in($attr_str)");
				// $add_balance_price = $GLOBALS['db']->getOne("select sum(add_balance_price) from ".DB_PREFIX."deal_attr where id in($attr_str)");
				$add_balance_price = 0;
				$attr_price = 0;
				if ($deal_attr) {
					$attr_key = implode('_', $deal_attr);
					$add_price_sql = 'SELECT price, add_balance_price FROM '.DB_PREFIX.'attr_stock WHERE deal_id='.$id.' AND attr_key="'.$attr_key.'"';
                	$add_price_info = $GLOBALS['db']->getRow($add_price_sql);
                	$add_balance_price = $add_price_info['add_balance_price'];
                	$attr_price = $add_price_info['price'];
				}
				
				$cart_item['session_id'] = $session_id;
				$cart_item['user_id'] = intval($GLOBALS['user_info']['id']);
				$cart_item['deal_id'] = $id;
				//属性
				if($attr_name != '')
				{
					$cart_item['name'] = $deal_info['name']." [".$attr_name."]";
					$cart_item['sub_name'] = $deal_info['sub_name']." [".$attr_name."]";
				}
				else
				{
					$cart_item['name'] = $deal_info['name'];
					$cart_item['sub_name'] = $deal_info['sub_name'];
				}
				$cart_item['name'] = strim($cart_item['name']);
				$cart_item['sub_name'] = strim($cart_item['sub_name']);
				$cart_item['attr'] = $attr_str;
				$cart_item['add_balance_price'] = $add_balance_price;
				$cart_item['unit_price'] = $deal_info['current_price'] + $attr_price;
				$cart_item['number'] = $number;
				$cart_item['total_price'] = $cart_item['unit_price'] * $cart_item['number'];
				$cart_item['verify_code'] = $verify_code;
				$cart_item['create_time'] = NOW_TIME;
				$cart_item['update_time'] = NOW_TIME;
				$cart_item['return_score'] = $deal_info['return_score'];
				$cart_item['return_total_score'] = $deal_info['return_score'] * $cart_item['number'];
				$cart_item['return_money'] = $deal_info['return_money'];
				$cart_item['return_total_money'] = $deal_info['return_money'] * $cart_item['number'];
				$cart_item['buy_type']	=	$deal_info['buy_type'];
				$cart_item['supplier_id']	=	$deal_info['supplier_id'];
				$cart_item['attr_str'] = $attr_name_str;
				$cart_item['is_pick'] = $deal_info['is_pick'];
				
				$cart_item['in_cart'] = $cart_data['in_cart'];
				$cart_item['is_effect']=1;
		
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_cart",$cart_item);
		
			}
			else
			{
				if($number>0)
				{
					$cart_item['number'] += $number;
					$cart_item['in_cart'] = $cart_data['in_cart'];
					$cart_item['is_effect'] = 1;
					$cart_item['total_price'] = $cart_item['unit_price'] * $cart_item['number'];
					$cart_item['return_total_score'] = $deal_info['return_score'] * $cart_item['number'];
					$cart_item['return_total_money'] = $deal_info['return_money'] * $cart_item['number'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_cart",$cart_item,"UPDATE","id=".$cart_item['id']);
				}
			}
				
		
				
			syn_cart(); //同步购物车中的状态 cart_type
			//load_cart_list(true,$is_wap);
			//统计购物车商品数量
			$cart_num = 0;
			if(intval($GLOBALS['user_info']['id'])){//wap端会查询3个端都存在的购物车数据，使用只用user_id 来判断
				$cart_num = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_cart where in_cart=1 and buy_type = 0 and  user_id =".intval($GLOBALS['user_info']['id'])));
			}else{//判断必须是未登录的用户，session_id 来查询
				$cart_num = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_cart where in_cart=1 and buy_type = 0 and user_id = 0 and session_id ='".es_session::id()."'"));
			}
			$root['cart_num']=$cart_num;
			if($outputReturn){
				return output($root);
			}else{
				return $root;
			}
		}
		//========
		
		
	}
	
	
	/**
	 * 提交修改购物车，并生成会员接口
	 * 
	 * 输入
	 * num: 购物车列表的数量修改 array
	 * 结构如下
	 * Array(
	 * 	"123"=>1  key[int] 购物车主键   value[int] 数量
	 * )
	 * 
	 * mobile string 手机号
	 * sms_verify string 手机验证码
	 * 
	 * 输出
	 * status: int 状态 0失败 1成功
	 * info: string 消息
	 * user_data: 当前的会员信息，用于同步本地信息 array
	 * Array(
	 * 	id:int 会员ID
	 *  user_name:string 会员名
	 *  user_pwd:string 加密过的密码
	 *  email:string 邮箱
	 *  mobile:string 手机号
	 *  is_tmp: int 是否为临时会员 0:否 1:是
	 * )
	 */
	public function check_cart()
	{
		
		$root = array();
		
		$num_req = $GLOBALS['request']['num'];		
		$num = array();
		foreach ($num_req as $k=>$v)
		{
			$sv = intval($v);
			if($sv)
				$num[$k] = intval($sv);
		}
		$user_mobile = strim($GLOBALS['request']['mobile']);
		$sms_verify = strim($GLOBALS['request']['sms_verify']);
		$user_login_status = check_login();
	
		if( $GLOBALS['request']['from']=='wap'){
		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		
		
		require_once(APP_ROOT_PATH."system/model/cart.php");		
		if($user_login_status==LOGIN_STATUS_NOLOGIN)
		{
				//自动创建会员或手机登录
				if(app_conf("SMS_ON")==0)
				{
					return output($root,0,"短信功能未开启");
				}
				if($user_mobile=="")
				{
					return output($root,0,"请输入手机号");
				}
				if($sms_verify=="")
				{
					return output($root,0,"请输入收到的验证码");
				}
				
				$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
				$GLOBALS['db']->query($sql);
				
				$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
				
				if($mobile_data['code']==$sms_verify)
				{
					//开始登录
					//1. 有用户使用已有用户登录
					//2. 无用户产生一个用户登录
					require_once(APP_ROOT_PATH."system/model/user.php");
					
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
					$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
					if($user_info)
					{
						//使用已有用户
						$result = do_login_user($user_info['user_name'],$user_info['user_pwd']);
			
						if($result['status'])
						{
			
							$s_user_info = es_session::get("user_info");
							$userdata['id'] = $s_user_info['id'];
							$userdata['user_name'] = $s_user_info['user_name'];
							$userdata['user_pwd'] = $s_user_info['user_pwd'];
							$userdata['email'] = $s_user_info['email'];
							$userdata['mobile'] = $s_user_info['mobile'];
							$userdata['is_tmp'] = $s_user_info['is_tmp'];
								
						}
						else
						{
							if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
							{
								$field = "";
								$err = "用户不存在";
							}
							if($result['data'] == ACCOUNT_PASSWORD_ERROR)
							{
								$field = "";
								$err = "密码错误";
							}
							if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
							{
								$field = "";
								$err = "用户未通过验证";
							}
							return output($root,0,$err);
						}
					}
					else
					{
						//ip限制
						$ip = get_client_ip();
						$ip_nums = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where login_ip = '".$ip."'");
						if($ip_nums>intval(app_conf("IP_LIMIT_NUM"))&&intval(app_conf("IP_LIMIT_NUM"))>0)
						{
							return output($root,0,"IP受限");
						}
			
						if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".$user_mobile."' or mobile = '".$user_mobile."' or email = '".$user_mobile."'")>0)
						{
							return output($root,0,"手机号已被抢占");
						}
			
						//生成新用户
						$user_data = array();
						$user_data['mobile'] = $user_mobile;
			
			
						$rs_data = auto_create($user_data, 1);
						if(!$rs_data['status'])
						{
							return output($root,0,$rs_data['info']);
						}
			
						$result = do_login_user($rs_data['user_data']['user_name'],$rs_data['user_data']['user_pwd']);
			
						if($result['status'])
						{
							$s_user_info = es_session::get("user_info");
							$userdata['id'] = $s_user_info['id'];
							$userdata['user_name'] = $s_user_info['user_name'];
							$userdata['user_pwd'] = $s_user_info['user_pwd'];
							$userdata['email'] = $s_user_info['email'];
							$userdata['mobile'] = $s_user_info['mobile'];
							$userdata['is_tmp'] = $s_user_info['is_tmp'];
			
						}
						else 
						{
							return output($root,0,"登录失败");
						}
					}

				}
				else
				{
					return output($root,0,"验证码错误");
				}
				
				//end 自动创建会员或手机登录
		
		}
		else 
		{
			if($GLOBALS['user_info']['mobile']=="")
			{
				//绑定手机号
				if(app_conf("SMS_ON")==0)
				{
					return output($root,0,"短信功能未开启");
				}
				if($user_mobile=="")
				{
					return output($root,0,"请输入手机号");
				}
				if($sms_verify=="")
				{
					return output($root,0,"请输入收到的验证码");
				}
				
				$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
				$GLOBALS['db']->query($sql);
				
				$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
				
				if($mobile_data['code']==$sms_verify)
				{
					//开始绑定
					//1. 未登录状态提示登录
					//2. 已登录状态绑定
					require_once(APP_ROOT_PATH."system/model/user.php");
						
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."'");
					if($user_info)
					{
						$supplier_user_origin = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_user where account_id = '".$GLOBALS['supplier_info']['id']."' and user_id = '".$GLOBALS['user_info']['id']."'");
						//return output($root,0,"手机号已被抢占");
						$result = do_login_user($user_info['user_name'],$user_info['user_pwd']);
							
						if($result['status'])
						{
							$s_user_info = es_session::get("user_info");
							$userdata['id'] = $s_user_info['id'];
							$userdata['user_name'] = $s_user_info['user_name'];
							$userdata['user_pwd'] = $s_user_info['user_pwd'];
							$userdata['email'] = $s_user_info['email'];
							$userdata['mobile'] = $s_user_info['mobile'];
							$userdata['is_tmp'] = $s_user_info['is_tmp'];
							
							if($supplier_user_origin)
							{
								$supplier_user = array();
								$supplier_user['user_id'] = $s_user_info['id'];
								$supplier_user['account_id'] = $GLOBALS['supplier_info']['id'];
								$supplier_user['openid'] = $supplier_user_origin['openid']; //商户openid
								$supplier_user['nickname'] = $s_user_info['user_name'];
								$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$supplier_user);
								$supplier_user['id'] = $GLOBALS['db']->insert_id();
							}
						}
						else
						{
							return output($root,0,"登录失败");
						}
					}
					else
					{				
						$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
						$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$user_mobile."' where id = ".$GLOBALS['user_info']['id']);
			
						$result = do_login_user($user_mobile,$GLOBALS['user_info']['user_pwd']);
							
						if($result['status'])
						{
							$s_user_info = es_session::get("user_info");
							$userdata['id'] = $s_user_info['id'];
							$userdata['user_name'] = $s_user_info['user_name'];
							$userdata['user_pwd'] = $s_user_info['user_pwd'];
							$userdata['email'] = $s_user_info['email'];
							$userdata['mobile'] = $s_user_info['mobile'];
							$userdata['is_tmp'] = $s_user_info['is_tmp'];								
						}
						else
						{
							return output($root,0,"登录失败");
						}				
					}
				}
				else
				{
					return output($root,0,"验证码错误");
				}
				
				//end 绑定手机号
			}
			else 
			{
				$s_user_info = es_session::get("user_info");
				$userdata['id'] = $s_user_info['id'];
				$userdata['user_name'] = $s_user_info['user_name'];
				$userdata['user_pwd'] = $s_user_info['user_pwd'];
				$userdata['email'] = $s_user_info['email'];
				$userdata['mobile'] = $s_user_info['mobile'];
				$userdata['is_tmp'] = $s_user_info['is_tmp'];
			}
		}
		
		$total_score = 0;
		$total_money = 0;
		foreach ($num as $k=>$v)
		{
			$id = intval($k);
			$number = $v;
			
			$cart_data = $GLOBALS['db']->getRow("select return_score,return_money from ".DB_PREFIX."deal_cart where id=".$id);
			$total_score+=$cart_data['return_score']*$number;
			$total_money+=$cart_data['return_money']*$number;
		}
		
		//验证积分
		// 		$total_score = $cart_result['total_data']['return_total_score'];
		if($GLOBALS['user_info']['score']+$total_score<0)
		{
			return output($root,0,"积分不足");
		}
		//验证积分
		
		
		//关于现金的验证
		// 		$total_money = $cart_result['total_data']['return_total_money'];
		if($GLOBALS['user_info']['money']+$total_money<0)
		{
			return output($root,0,"余额不足");
		}
		//关于现金的验证
		
		foreach ($num as $k=>$v)
		{
			$id = intval($k);
			$number = intval($v);
			$data = check_cart($id, $number);
			if(!$data['status'])
			{
				return output($root,0,$data['info']);
			}
		}
		
		foreach ($num as $k=>$v)
		{
			$id = intval($k);
			$number = intval($v);
			if($is_wap){
			    $GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set number =".$number.", total_price = ".$number."* unit_price, return_total_score = ".$number."* return_score, return_total_money = ".$number."* return_money where id =".$id);
			}else{
			    $GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set number =".$number.", total_price = ".$number."* unit_price, return_total_score = ".$number."* return_score, return_total_money = ".$number."* return_money where id =".$id." and session_id = '".es_session::id()."'");
			}
			
			//load_cart_list(true,$is_wap);
		}
		$root['user_data'] = $userdata;
		return output($root);
	}
	
	
	/**
	 * 删除购物车
	 * 
	 * 输入
	 * id:int 购物车中的商品ID，该参数不传时表示为清空所有购物车内容 
	 * 
	 * 输出
	 * 无
	 */
	public function del()
	{
		$root = array();
		if( $GLOBALS['request']['from']=='wap'){
		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		
		if(isset($GLOBALS['request']['id']))
		{
			$id = intval($GLOBALS['request']['id']);
			$sql = "delete from ".DB_PREFIX."deal_cart  where session_id = '".es_session::id()."' and id = ".$id;
		}
		else
		{
			$sql = "delete from ".DB_PREFIX."deal_cart  where session_id = '".es_session::id()."'";
		}
		$GLOBALS['db']->query($sql);
		
		require_once(APP_ROOT_PATH."system/model/cart.php");
		
// 		if($GLOBALS['db']->affected_rows()>0)
// 		{
// 			load_cart_list(true,$is_wap);  //重新刷新购物车
// 		}
		return output($root);
	}
	
	
	/**
	 * 购物车的提交页
	 * 输入:id 直接购买，不进入购物车的时，商品ID
	 * lid;  //自提门店ID
	 * address_id 配送地址ID
	 * 无
	 * 
	 * 输出:
	 * status: int 状态 1:正常 -1未登录需要登录
	 * info:string 信息
	 * cart_list: object 购物车列表，如该列表为空数组则跳回首页,结构如下
	 * Array
        (
            [478] => Array key [int] 购物车表中的主键
                (
                    [id] => 478 [int] 同key
                    [return_score] => 0 [int] 当is_score为1时单价的展示
                    [return_total_score] => 0 [int] 当is_score为1时总价的展示
                    [unit_price] => 108 [float] 当is_score为0时单价的展示
                    [total_price] => 108 [float] 当is_score为0时总价的展示
                    [number] => 1 [int] 购买件数
                    [deal_id] => 57 [int] 商品ID
                    [attr] => 287,290 [string] 购买商品的规格ID组合，用逗号分隔的规格ID
                    [name] => 桥亭活鱼小镇 仅售88元！价值100元的代金券1张 [9点至18点,2-5人套餐] [string] 商品全名，包含属性
                    [sub_name] => 88元桥亭活鱼小镇代金券 [9点至18点,2-5人套餐] [string] 商品缩略名，包含属性
                    [max] => int 最大购买量 加减时用
                    [icon] => string 商品图标 140x85
                )
		)
		
	 * total_data: array 购物车总价统计,结构如下
	 *	Array
        (
            [total_price] => 108 [float] 当is_score为0时的总价显示
            [return_total_score] => 0 [int] 当is_score为1时的总价显示
        )
	 * is_score: int 当前购物车中的商品类型 0:普通商品，展示时显示价格 1:积分商品，展示时显示积分
	 * is_delivery: int 是否需要配送 0无需 1需要
     * consignee_count: int 预设的配送地址数量 0：提示去设置收货地址 1以及以上显示选择其他收货方式
     * consignee_info: object 当前配送地址信息，结构如下
     * Array
        (
            [id] => 19 int 配送方式的主键
            [user_id] => 71 int 当前会员ID
            [region_lv1] => 1 int 国ID
            [region_lv2] => 4 int 省ID
            [region_lv3] => 53 int 市ID
            [region_lv4] => 519 int 区ID
            [address] => 群升国际 string 详细地址
            [mobile] => 13555566666 string 手机号
            [zip] => 350001 string 邮编
            [consignee] => 李四 string 收货人姓名
            [is_default] => 1
            [region_lv1_name] => 中国 string 国名
            [region_lv2_name] => 福建 string 省名
            [region_lv3_name] => 福州 string 市名
            [region_lv4_name] => 台江区 string 区名
        )

	 * delivery_list: array 配送方式列表，结构如下
	 * Array
        (
            [0] => Array
                (
                    [id] => 8 [int] 主键
                    [name] => 顺风快递 [string] 名称
                    [description] => 顺风快递,福州地区2元 [string] 描述
                )

        )
	 * payment_list: array 支付方式列表，结构如下
     * Array
        (
            [0] => Array
                (
                    [id] => 20 [int] 支付方式主键
                    [code] => Walipay [string] 类名
                    [logo] => http://192.168.1.41/o2onew/public/attachment/sjmapi/4f2ce3d1827e4.jpg [string] 图标 40x40
                    [name] => 支付宝支付 [string] 显示的名称
                )
        )
     * is_coupon: int 是否为发券订单，0否 1:是
     * show_payment: int 是否要显示支付方式 0:否（0元抽奖类） 1:是
     * has_account: int 是否显示余额支付 0否  1是
     * has_ecv: int 是否显示代金券支付 0否  1是
     * voucher_list:array 可用的代金券列表
     * array(
     * array(
     * 	"sn"=>"xxxxx" string 代金券序列号,
     *  "name" => "红包名称" string
     * )
     * )
     * account_money:float 余额
     * 
	 */
	public function check()
	{	
		$root = array();
		$root['user_login_status']=check_login();
		
		if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
		{
			return output($root,-1,"请先登录");
		}
		$address_id=intval($GLOBALS['request']['address_id']);
		if( $GLOBALS['request']['from']=='wap'){
			
		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		
		$id=intval($GLOBALS['request']['id']);  //直接购买，不进入购物车的时，商品ID
		$lid=intval($GLOBALS['request']['lid']);  //自提门店ID
		if($lid > 0){
		    $location = $GLOBALS['db']->getRow("select id,name,address,tel from ".DB_PREFIX."supplier_location where id=".$lid);
		    $root['location'] = $location;
		}
		
		
		require_once(APP_ROOT_PATH."system/model/cart.php");
		if($id > 0){
		    $cart_result = load_cart_list($id);
		}else{
		    $cart_result = load_cart_list($id=0,false);
		}
		
		$user_discount_percent = 1;
		if (!empty($GLOBALS['user_info']['id'])) {
			$user_discount_percent = getUserDiscount($GLOBALS['user_info']['id']);
		}
		
		
		/* //查找是否有未登陆时的商品
		$unlogin_cart=$GLOBALS['db']->getAll("selsect * for ") */

		$total_price = $cart_result['total_data']['total_price']*$user_discount_percent;
		
		//处理购物车输出
		$cart_list_o = $cart_result['cart_list'];
		$cart_list = array();
		
		$total_data_o = $cart_result['total_data'];
		$is_score = 0;
		
		// 存储商户id用于获取商户开票设置
		$supplier_ids = array();

		foreach($cart_list_o as $k=>$v)
		{
		    
			$bind_data = array();
			$bind_data['id'] = $v['id'];
			if($v['buy_type']==1)
			{
				$is_score = 1;
				$bind_data['return_score'] = abs($v['return_score']);
				$bind_data['return_total_score'] = abs($v['return_total_score']);
				$bind_data['unit_price'] = 0;
				$bind_data['total_price'] = 0;
				$buy_type=1;
			}
			else
			{
				$bind_data['return_score'] = 0;
				$bind_data['return_total_score'] = 0;
				$u_price = $v['unit_price'];
				$t_price = $v['total_price'];
				if ($v['allow_user_discount']) {
					$u_price = round($u_price * $user_discount_percent, 2);
					$t_price = $u_price * $v['number'];
				}
				$bind_data['unit_price'] = $u_price;
				$bind_data['total_price'] = $t_price;
				$buy_type=0;
			}
			$bind_data['allow_promote'] = $v['allow_promote'];
			$bind_data['number'] = $v['number'];
			$bind_data['is_pick'] = $v['is_pick'];
			$bind_data['deal_id'] = $v['deal_id'];
			$bind_data['attr'] = $v['attr'];
			$bind_data['attr_str'] = $v['attr_str'];
			$bind_data['name'] = $v['name'];
			$bind_data['sub_name'] = $v['sub_name'];
			$bind_data['max'] = 100;
			$bind_data['is_shop'] = $v['is_shop'];
			$bind_data['supplier_id'] = $v['supplier_id'];
			$bind_data['icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,1)) ;
			$bind_data['f_icon'] = get_abs_img_root(get_spec_image($v['icon'],280,280,1)) ;
			$cart_list[$v['id']] = $bind_data;
			$supplier_ids[] = $v['supplier_id'];
		}

		// 获取商户的开票信息配置
		if (!$is_score) {
			$invoice_sql = 'SELECT * FROM '.DB_PREFIX.'invoice_conf WHERE supplier_id in ('.implode(',', $supplier_ids).')';
			$db_invoice_list = $GLOBALS['db']->getAll($invoice_sql);
			$invoice_list = array();
			foreach ($db_invoice_list as $value) {
				if ($value['invoice_type'] > 0) {
					if (!empty($value['invoice_content'])) {
						$value['invoice_content'] = explode(' ', $value['invoice_content']);
					} else {
						$value['invoice_content'] = array('明细');
					}
				}
				$invoice_list[$value['supplier_id']] = $value;
			}
			if (!empty($invoice_list)) {
				$invoice_notice = app_conf('INVOICE_NOTICE');
				$root['invoice_notice'] = $invoice_notice;
			}
			$root['invoice_list'] = $invoice_list;
		}
		
		
		$total_data = array();
		
		if($is_score)
		{
			$total_data['total_price'] = 0;
			$total_data['return_total_score'] = abs($total_data_o['return_total_score']);
		}
		else
		{
			$total_data['total_price'] = round($total_data_o['total_price'],2);
			$total_data['return_total_score'] = 0;
		}
		

		
		$root['total_data'] = $total_data;
		$root['is_score'] = $is_score;
		//end购物车输出
		
	
		$is_delivery = 0;
		foreach($cart_list_o as $k=>$v)
		{	
			if($v['is_delivery']==1)
			{
				$is_delivery = 1;
				break;
			}
		}
		$root['is_delivery'] = $is_delivery;

		if($is_delivery)
		{
			//输出配送方式
			$consignee_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']);
			$GLOBALS['tmpl']->assign("consignee_count",intval($consignee_count));
			$address_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_consignee where id=".$address_id." and user_id=".$GLOBALS['user_info']['id']);
			if($address_id){
			    $consignee_id=$address_id;
			}else {
			    $consignee_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']." and is_default = 1");
			}
			if($lid > 0){
			    $consignee_id=0;
			}
			$GLOBALS['tmpl']->assign("consignee_id",intval($consignee_id));
		}
		$root['consignee_count'] = intval($consignee_count);
		$consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
		$consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:null;
		$root['consignee_info'] = $consignee_info;
		
		if($consignee_info)
			$region_id = intval($consignee_info['region_lv4']);


		//配送方式由ajax由 consignee 中的地区动态获取
			
		//输出支付方式
		if ($GLOBALS['request']['from'] == 'wap')
		{
			//支付列表
			$sql = "select id, class_name as code, logo from ".DB_PREFIX."payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
		}
		else
		{
			//支付列表
			$sql = "select id, class_name as code, logo from ".DB_PREFIX."payment where (online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
		}
		if(allow_show_api())
		{
			$payment_list = $GLOBALS['db']->getAll($sql);
		}
		foreach($cart_list as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
			{
				$define_payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$v['deal_id']);
				$define_payment = array();
				foreach($define_payment_list as $kk=>$vv)
				{
					array_push($define_payment,$vv['payment_id']);
				}
				foreach($payment_list as $k=>$v)
				{
					if(in_array($v['id'],$define_payment))
					{
						unset($payment_list[$k]);
					}
				}
			}
		}
		
		
		foreach($payment_list as $k=>$v)
		{
			$directory = APP_ROOT_PATH."system/payment/";
			$file = $directory. '/' .$v['code']."_payment.php";
			if(file_exists($file))
			{
				require_once($file);
				$payment_class = $v['code']."_payment";
				$payment_object = new $payment_class();
				$payment_list[$k]['name'] = $payment_object->get_display_code();
			}
			
			if($v['logo']!="")
			$payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'],40,40,1));
		}
		
		sort($payment_list);
		$root['payment_list'] = $payment_list;
		
		$is_coupon = 0;
		foreach($cart_list_o as $k=>$v)
		{
			if($GLOBALS['db']->getOne("select is_coupon from ".DB_PREFIX."deal where id = ".$v['deal_id']." and forbid_sms = 0")==1)
			{
				$is_coupon = 1;
				break;
			}
		}
		$root['is_coupon'] = $is_coupon;
		
		$root['buy_type'] = intval($GLOBALS['db']->getOne("select buy_type from ".DB_PREFIX."deal where id=".$id));
			
		//查询总金额
		$delivery_count = 0;
		foreach($cart_list_o as $k=>$v)
		{
			if($v['is_delivery']==1)
			{
				$delivery_count++;
			}
		}
		
		if($total_price > 0 || $delivery_count > 0)
		    $show_payment = 1;
		else
		 	$show_payment = 0;
		$root['show_payment'] = $show_payment;
		
		if($show_payment)
		{
			$web_payment_list = load_auto_cache("cache_payment");
			foreach($cart_list as $k=>$v)
			{
				if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
				{
					$define_payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$v['deal_id']);
					$define_payment = array();
					foreach($define_payment_list as $kk=>$vv)
					{
						array_push($define_payment,$vv['payment_id']);
					}
					foreach($web_payment_list as $k=>$v)
					{
						if(in_array($v['id'],$define_payment))
						{
							unset($web_payment_list[$k]);
						}
					}
				}
			}
			
			foreach($web_payment_list as $k=>$v)
			{
				if($v['class_name']=="Account"&&$GLOBALS['user_info']['money']>0)
				{
					$root['has_account'] = 1;					
				}
				if($v['class_name']=="Voucher")
				{
					$root['has_ecv'] = 1;
					$sql = "select e.sn as sn,t.name as name,e.money as money,t.start_use_price from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as t on e.ecv_type_id = t.id where ".
							" e.user_id = '".$GLOBALS['user_info']['id']."' and (e.begin_time < ".NOW_TIME.") and (e.end_time = 0 or e.end_time > ".NOW_TIME.") ".
							" and (e.use_limit = 0 or e.use_count<e.use_limit) and (t.start_use_price<=".$total_price." or t.start_use_price=0)";
					$sql_count = "select count(*) from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as t on e.ecv_type_id = t.id where ".
					    " e.user_id = '".$GLOBALS['user_info']['id']."' and (e.begin_time < ".NOW_TIME.") and (e.end_time = 0 or e.end_time > ".NOW_TIME.") ".
					    " and (e.use_limit = 0 or e.use_count<e.use_limit) and (t.start_use_price<=".$total_price." or t.start_use_price=0)";
					
					$voucher_list = $GLOBALS['db']->getAll($sql);
					
					foreach ($voucher_list as $t => $v){
					    $voucher_list[$t]['money']=round($v['money']);
					    $voucher_list[$t]['start_use_price']=round($v['start_use_price']);
					}
					
					$root['voucher_count']=$GLOBALS['db']->getOne($sql_count);
				    $root['voucher_list']=$voucher_list;
				}
			}			
			
		}
		else
		{
			$root['has_account'] = 0;
			$root['has_ecv'] = 0;
		}
		$root['page_title'] = "提交订单";
		
		$root['account_money'] = (string)round($GLOBALS['user_info']['money'],2);
		
		
		//$root['cart_list'] = $cart_list?$cart_list:null;
		$cart_list_x=array();
		$is_pick=1;
		foreach($cart_list as $k=>$v){
		    if($id > 0){
		        $stock=$GLOBALS['db']->getOne("select stock_cfg from ".DB_PREFIX."deal_stock where deal_id=".$v['deal_id']);
		        if($stock<10 && $stock!=-1){
		            $v['stock']=$stock;
		        }
		    }else {
		        $attr = array();
		        $attr['attr_id'] = explode($v['attr']);
		        $attr['attr_str'] = $v['attr_str'];
		        $check_info = check_deal_status($v['deal_id'],$attr,0,true);//进入确认订单，数量用0判断
		        if($check_info['stock']<10 && $check_info['stock']!=-1){
		            $v['stock'] = $check_info['stock'] ;//加上被减去的购物车商品数量
		        }
		    }
		    $bai = floor($v['unit_price']);
		    $fei = str_pad(round(($v['unit_price'] - $bai) * 100,2),2,'0',STR_PAD_LEFT);
		    $v['unit_price_format'] = "&yen; <i>" .$bai."</i>.".$fei;
		    $back_deal_url = $v['url'] = wap_url("index","deal",array("data_id"=>$v['deal_id']));
		    $v['return_score_format'] = "<i>" .$v['return_score']."</i> 积分";
		    $v['return_total_score_format'] = "<i>" .$v['return_total_score']."</i> 积分";
		    
		    
		    $delivery_type = $GLOBALS['db']->getOne("select delivery_type from ".DB_PREFIX."deal where id=".$v['deal_id']);
		    $v['delivery_type'] = $delivery_type;
		    
		    if($v['supplier_id']==0){
		        if($delivery_type==1){ //平台物流配送商品
		            $cart_list_x['p_wl'][$v['id']]=$v;
		        }elseif($delivery_type==3){ //平台驿站配送商品
		            $cart_list_x['p_yz'][$v['id']]=$v;
		        }else{ //平台无需配送商品，直接进入订单提交页面，不进入购物车页面
		            $cart_list_x[$v['supplier_id']][$v['id']]=$v;
		        }
		    }else{
		        $cart_list_x[$v['supplier_id']][$v['id']]=$v;
		    }
		    
		    if($v['is_shop']==1 && $v['is_pick']==0){
		        $is_pick=0;
		    
		    }
		    
		}
		
		$cart_list_new=array();
		$supplier=array();
		$is_zy=0;//是否是平台自营商品
		
		
        if($consignee_id > 0){
            $delivery_list = get_express_fee($cart_result['cart_list'],$consignee_id);
        }
        
        $p_wl_youhui_id=0;
        $p_yz_youhui_id=0;
		foreach($cart_list_x as $k=>$v){
		    $cart_list_new[$k]['id']=$k;
		    
		    $supplier_total_pirce=0;
		    foreach ($v as $tt => $vv){
		        $supplier_total_pirce+=$vv['total_price'];
		    }
		    
		    $cart_list_new[$k]['total_price']=$supplier_total_pirce;
		    
		    if($k =='p_wl'){
		        $supplier_name = '平台自营';
		        
		        $youhui_supplier_id=0;
		        
		    }elseif($k =='p_yz'){
		        $supplier_name = '平台自营（驿站配送）';
		        
		        $youhui_supplier_id=0;

		    }else{
		        $supplier_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$k);
		       
		        $youhui_supplier_id=$k;
		        
		    }
		    
		    $cart_list_new[$k]['supplier_name']=$supplier_name;
		    
		    if($buy_type==0){
		        
		        $log_sql="select yl.id,y.youhui_value,y.start_use_price from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as y on y.id=yl.youhui_id
                  where y.supplier_id = ".$youhui_supplier_id." and yl.confirm_time=0 and (yl.expire_time=0 or yl.expire_time>".NOW_TIME.") and y.youhui_type=2 and y.is_effect=1
	              and (y.start_use_price<=".$supplier_total_pirce." or y.start_use_price=0) and yl.user_id=".$GLOBALS['user_info']['id']." order by y.youhui_value desc";
		        
		        $log_info=$GLOBALS['db']->getAll($log_sql);
		        
		        
		        if($log_info){
    		        
    		        if($k =='p_wl' && $p_yz_youhui_id!=$log_info[0]["id"]){
    		            $p_wl_youhui_id=$log_info[0]["id"];
    		            $log_info[0]['is_checked']=1;
    		            
    		            $youhui_value=$log_info[0]["youhui_value"];
    		        }
    		        elseif ($k =='p_wl'){
    		            $p_wl_youhui_id=$log_info[1]["id"];
    		            $log_info[1]['is_checked']=1;
    		            
    		            $youhui_value=$log_info[1]["youhui_value"];
    		        }
    		        elseif($k =='p_yz' && $p_wl_youhui_id!=$log_info[0]["id"]){
    		            $p_yz_youhui_id=$log_info[0]["id"];
    		            $log_info[0]['is_checked']=1;
    		            
    		            $youhui_value=$log_info[0]["youhui_value"];
    		        }
    		        elseif ($k =='p_yz'){
    		            $p_yz_youhui_id=$log_info[1]["id"];
    		            $log_info[1]['is_checked']=1;
    		            
    		            $youhui_value=$log_info[1]["youhui_value"];
    		        }
    		        else{
    		            $log_info[0]['is_checked']=1;
    		            
    		            $youhui_value=$log_info[0]["youhui_value"];
    		        }
    		        
    		        $cart_list_new[$k]['youhui_value']=$youhui_value;
        		    $cart_list_new[$k]['youhui_list']=$log_info;
		        }
		    
		    }
		    sort($v);
		    $cart_list_new[$k]['list'] =$v;
		    
		    
		    	
		    $cart_list_new[$k]['delivery_fee'] =$delivery_list[$k]['total_fee'];
		    
		    if(!in_array($k, $supplier)){
		        $supplier[]=$k;
		    }
		    if($k==0){  //如果是平台自营，不能自提
		        $is_zy=1;
		    }
		    
		    $ivoKey = 0;
		    if (intval($k) > 0) {
		    	$ivoKey = $k;
		    }
		    if (isset($invoice_list[$ivoKey])) {
		    	$cart_list_new[$k]['invoice_conf'] = $invoice_list[$ivoKey];
		    }
		}
        
		if($cart_list_new['p_wl'])
		   $cart_list_new['p_wl']['p_youhui_id']=$p_yz_youhui_id;
		
		if($cart_list_new['p_yz'])
		   $cart_list_new['p_yz']['p_youhui_id']=$p_wl_youhui_id;
		
		sort($cart_list_new);

		$root['cart_list']=$cart_list_new;
		
		//print_r($cart_list_new);exit;

		//只有普通商家才能上门自提,且多商家下单时，不允许自提
		if($is_pick==1 && count($supplier) == 1 && $is_zy==0)
		{
		    $supplier_id=$supplier[0];
		    $is_pick=1;
		}else{
		    $supplier_id=0;
		    $is_pick=0;
		}
		$root['supplier_id'] = $supplier_id;
		$root['is_pick'] = $is_pick;
		
		
		return output($root);
	}

	
	/**
	 * 计算购物车总价
	 * 
	 * 输入:
	 * id：如果是直接购买的商品，传商品ID，不是直接购买的，就不用传
	 * ecvsn:string 代金券序列号
	 * address_id:配送地址ID
	 * 
	 * 
	 * 输出:
	 * feeinfo: array 费用清单，结构如下
	 * Array(
	 * 	    Array(
					"name" => "折扣", string 费用清单项名称
					"value" => "7折" string 费用清单项内容
			),
	 * )
	 * 
	 * paid: array 已付清单，结构如下
	 * Array(
	 * 	    Array(
					"name" => "折扣", string 费用清单项名称
					"value" => "7折" string 费用清单项内容
			),
	 * )
	 * 
	 * promote: array 优惠清单，结构如下
	 * Array(
	 * 	    Array(
					"name" => "折扣", string 费用清单项名称
					"value" => "7折" string 费用清单项内容
			),
	 * )
	 * total_promote_price 总优惠
	 * pay_price 还需支付
	 * total_price总价
	 */
	public function count_buy_total()
	{

		require_once(APP_ROOT_PATH."system/model/cart.php");
		$delivery_id =  intval($GLOBALS['request']['delivery_id']); //配送方式
		$id =  intval($GLOBALS['request']['id']); //购物方式
		$address_id =  intval($GLOBALS['request']['address_id']);
		$youhui_ids=$GLOBALS['request']['youhui_ids'];
		if( $GLOBALS['request']['from']=='wap'){

		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		
		$region_id = 0; //配送地区
		$consignee_id = $address_id;
		$consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
		$consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:array();			
		if($consignee_info)
			$region_id = intval($consignee_info['region_lv4']);

	
		$ecvsn = $GLOBALS['request']['ecvsn']?strim($GLOBALS['request']['ecvsn']):'';
		$ecvpassword = $GLOBALS['request']['ecvpassword']?strim($GLOBALS['request']['ecvpassword']):'';
		$payment = intval($GLOBALS['request']['payment']);
		$all_account_money = intval($GLOBALS['request']['all_account_money']);
		$all_score = intval($GLOBALS['request']['all_score']);
		$bank_id = '';
	
		if($id > 0){
		    $cart_result = load_cart_list($id);
		}else{
		    $cart_result = load_cart_list($id=0,false);
		}
		
		$goods_list = $cart_result['cart_list'];
		
		
		$result = count_buy_total($region_id,$consignee_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword,$goods_list,0,0,$bank_id,$all_score,0,$youhui_ids);
		$root = array();

		if(!$consignee_id){
		    if($result['delivery_fee']>0){
		        $result['pay_price']=$result['pay_price']-$result['delivery_fee'];
		        if($result['pay_price']<0){
		            $result['pay_price']=0;
		        }
		    }
		    unset($result['delivery_fee']);
		    unset($result['count_delivery_fee']);
		}

		if(isset($result['promote']['Appdiscount'])){
		    $result['total_price'] +=$result['promote']['Appdiscount']['discount_amount'];//商品金额，要加上满减金额
		}
		
		$feeinfo=array();  //费用
		$paid=array();     //已付和已优惠
		$promote=array();  //这订单完成后的返现或返积分
		if($result['total_price']>0)
		{
		    $feeinfo[] = array(
		        "name" => "商品金额",
		        "class"=>"total_price",
		        "symbol" => 1,
		        "value" => round($result['total_price']-$result['user_discount'],2)
		    );
		}
		
		if($result['buy_type']==1 && abs($result['return_total_score'])>0){
		    $feeinfo[] = array(
		        "name" => "消耗积分",
		        "class"=>"return_total_score",
		        "symbol" => 2,
		        "value" => round(abs($result['return_total_score']))
		    );
		}
		
		if($result['count_delivery_fee'] > 0)
		{
		    $feeinfo[] = array(
		        "name" => "运费",
		        "symbol" => 1,
		        "value" => round($result['count_delivery_fee'],2)
		    );
		    $root['free'] = 0;
		}else {
		    $root['free'] = 1;
		}
		$total_promote_price = 0;
		
		if($result['youhui_money'] > 0)
		{
		    $paid[] = array(
		        "name" => "优惠券",
		        "symbol" => -1,
		        "value" => round($result['youhui_money'],2)
		    );
		    $total_promote_price+=$result['youhui_money'];
		}
		
		if($result['count_delivery_fee'] > 0 && $result['delivery_fee'] == 0)
		{
		    $paid[] = array(
		        "name" => "满免优惠",
		        "symbol" => -1,
		        "value" => round($result['count_delivery_fee'],2)
		    );
		    $total_promote_price+=$result['count_delivery_fee'];
		}
		
		if(isset($result['promote']['Appdiscount'])){
		    $paid[] = array(
		        "name" => "满减优惠",
		        "symbol" => -1,
		        "value" => round($result['promote']['Appdiscount']['discount_amount'],2)
		    );
		    $total_promote_price+=$result['promote']['Appdiscount']['discount_amount'];
		}
		
		if($result['ecv_money']>0)
		{
		    if(($result['total_price']+$result['delivery_fee']) < $result['ecv_money']){
		        $result['ecv_money'] = $result['total_price']+$result['delivery_fee'];
		    }
		
		    if($result['ecv_money'] > 0){
		        $paid[] = array(
		            "name" => "红包支付",
		            "symbol" => -1,
		            "value" => round($result['ecv_money'],2)
		        );
		        $total_promote_price+=$result['ecv_money'];
		    }
		
		}
		if($result['exchange_money']>0)
		{
		    $paid[] = array(
		        "name" => "积分抵现",
		        "symbol" => -1,
		        "value" => round($result['exchange_money'],2)
		    );
			$total_promote_price+=$result['exchange_money'];
		}
		/*if(round($result['user_discount'],2)>0)
		 {
		 $paid[] = array(
		 "name" => "会员折扣",
		 "symbol" => -1,
		 "value" => round($result['user_discount'],2)
		 );
		 $total_promote_price+=$result['user_discount'];
		 }*/
		
		
		if($result['buy_type']==0)
		{
		    if($result['return_total_score'])
		    {
		        $promote[] = array(
		            "name" => "返还积分",
		            "symbol" => 2,
		            "value" => round($result['return_total_score'])
		        );
		    }
		}
		
		if($result['return_total_money'])
		{
		    $promote[] = array(
		        "name" => "返现",
		        "symbol" => 1,
		        "value" => round($result['return_total_money'],2)
		    );
		}
		
		if($result['paid_account_money']>0)
		{
		    $paid[] = array(
		        "name" => "已付",
		        "symbol" => -1,
		        "value" => round($result['paid_account_money'],2)
		    );
		}
		
		if($result['paid_ecv_money']>0)
		{
		    $paid[] = array(
		        "name" => "红包已付",
		        "symbol" => -1,
		        "value" => round($result['paid_ecv_money'],2)
		    );
		}
		
		
		
		// 		if($result['buy_type']==0)
		    // 		{
		    // 		    $feeinfo[] = array(
		    // 		        "name" => "总计",
		    // 		        "value" => round($result['pay_total_price'],2)."元"
		    // 		    );
		    // 		}
		// 		else
		    // 		{
		    // 		    $feeinfo[] = array(
		    // 		        "name" => "所需积分",
		    // 		        "value" => abs(round($result['return_total_score']))
		    // 		    );
		    // 		}
		
		// 		if($result['pay_price'])
		    // 		{
		    // 		    $feeinfo[] = array(
		    // 		        "name" => "应付总额",
		    // 		        "value" => round($result['pay_price'],2)."元"
		    // 		    );
		    // 		}
		
		$total_price = $result['total_price'] + $result['count_delivery_fee'] - $result['user_discount'];
		if(round($total_price,2)<=round($total_promote_price,2)){
		    $total_promote_price=$total_price;
		}

		$root['ecv_no_use_status']=$result['ecv_no_use_status'];
		$root['feeinfo'] = $feeinfo;
		$root['paid'] = $paid;
		$root['promote'] = $promote;
		$root['score_purchase'] = $result['score_purchase'];
		$root['total_promote_price'] = round($total_promote_price,2);
		$root['pay_price'] = round($result['pay_price'],2);
		$root['total_price'] = round($total_price,2);
		$root['all_score'] = $all_score;

	
		return output($root);
	}
	
	
	/**
	 * 购物车提交订单接口
	 * 输入：
	 * address_id: int 配送方式主键
	 * ecvsn:string 代金券序列号
	 * content:string 订单备注
	 * buy_type 如果是积分兑换，传1
	 * id 直接购买，不进入购物车的时，商品ID
	 * 
	 * 输出：
	 * status: int 状态 0:失败 1:成功 -1:未登录
	 * info: string 失败时返回的错误信息，用于提示
	 * 以下参数为status为1时返回
	 * pay_status:int 0未支付成功 1全部支付
	 * order_id:int 订单ID
	 * 
	 */
	public function done()
	{
		$root=array();
		$root['user_login_status']=check_login();
		if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN) {
			return output($root,-1,"请先登录");
		}

		if (empty($GLOBALS['user_info']['mobile'])) {
			return output($root, -2, '请先绑定手机');
		}

		require_once(APP_ROOT_PATH."system/model/cart.php");
		require_once(APP_ROOT_PATH."system/model/deal.php");
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		$delivery_id =  intval($GLOBALS['request']['delivery_id']); //配送方式
		$address_id=intval($GLOBALS['request']['address_id']);
		$location_id=intval($GLOBALS['request']['location_id']);  //自提门店的ID
		$youhui_ids=$GLOBALS['request']['youhui_ids'];  //优惠ID

		if($location_id > 0){
		    $address_id=0;
		}
		if( $GLOBALS['request']['from']=='wap'){

		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		$buy_type=intval($GLOBALS['request']['buy_type']);
		$id=intval($GLOBALS['request']['id']);
		
		$root['buy_type']=$buy_type;
		
		$region_id = 0; //配送地区


	    $consignee_id=$address_id;

	    $consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
		$consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:array();
		if($consignee_info)
			$region_id = intval($consignee_info['region_lv4']);

		
		$payment = intval($GLOBALS['request']['payment']);
		$all_account_money = intval($GLOBALS['request']['all_account_money']);
		$all_score = intval($GLOBALS['request']['all_score']);
		$ecvsn = $GLOBALS['request']['ecvsn']?strim($GLOBALS['request']['ecvsn']):'';
		$ecvpassword = $GLOBALS['request']['ecvpassword']?strim($GLOBALS['request']['ecvpassword']):'';
		//$memo = substr(strim($GLOBALS['request']['content']), 0, 100);
		if($GLOBALS['request']['from']=="app"){
			$GLOBALS['request']['content']=json_decode($GLOBALS['request']['content'],true);
		}
		$content = $GLOBALS['request']['content'];
		
        if(count($content)==1){
            $memo = end($content);
        }
        
        $supplier_data = array();
        if($content){
            foreach($content as $k=>$v){
                $supplier_data[$k]['memo'] = $v;
            }
        }

        // 发票的数据处理
		$invoice_types = $_REQUEST['invoice_type'];
		$invoice_titles = $_REQUEST['invoice_title'];
		$invoice_persons = $_REQUEST['invoice_person'];
		$invoice_taxnus = $_REQUEST['invoice_taxnu'];
		$invoice_contents = $_REQUEST['invoice_content'];
		$firstInvoice = '';
		if ($invoice_types) {
			$invIndex = 0;
			foreach ($invoice_types as $key => $value) {
				$value = intval($value);
				if ($value !== 0) {
					$invoices['type'] = 1;
					$invoices['title'] = $value == 1 ? 0 : 1;
					$invoices['persons'] = strim($invoice_titles[$key]);
					if ($invoices['title'] == 1) {
						$invoices['taxnu'] = strim($invoice_taxnus[$key]);
					}
					$invoices['content'] = strim($invoice_contents[$key]);
					$seriInv = serialize($invoices);
					$supplier_data[$key]['invoice_info'] = $seriInv;
					if ($invIndex === 0) {
						$firstInvoice = $seriInv;
					}
					$invIndex++;
				}
			}
		}

		if($id > 0){
		    $cart_result = load_cart_list($id);
		}else{
		    $cart_result = load_cart_list($id=0,false);
		}
		
		
		$goods_list = $cart_result['cart_list'];

		if(!$goods_list)
		{			
			return output($root,0,"购物车为空");
		}
		
		//验证购物车
		$deal_ids = array();	
		foreach($goods_list as $row)
		{
			$checker = check_deal_number($row['deal_id'],0);
			if($checker['status']==0)
			{
				return output($root,0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);
			}
		}
		foreach($goods_list as $k=>$v)
		{
			$checker = check_deal_number_attr($v['deal_id'],$v['attr_str'],0);
			if($checker['status']==0)
			{
				return output($root,0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);
			}
		}
		//验证商品是否过期
		foreach($goods_list as $row)
		{
			$checker = check_deal_time($row['deal_id']);
			if($checker['status']==0)
			{
				return output($root,0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);
			}
		}
		
		
		$supplier=array();
		$has_wuliu=0;//是否有物流配送
		$has_yizhan=0; //是否有驿站配送
		$has_stuan=0;  //是否有商家团购商品
		$has_sshop=0;  //是否有商家商城商品
		foreach($goods_list as $k=>$v)
		{
			$data = check_cart($v['id'], $v['number']);
			if(!$data['status']){
				return output($root,0,$data['info']);
			}	
			$deal_ids[$v['deal_id']]['deal_id'] = $v['deal_id'];
			
			
			if(!in_array($v['supplier_id'], $supplier)){
			    $supplier[]=$v['supplier_id'];
			}
			$order_deal = $GLOBALS['db']->getRow("select delivery_type,id,is_shop from ".DB_PREFIX."deal where id=".$v['deal_id']);
			if($v['supplier_id']==0){  //平台自营，平台自营商品物流配送和驿站配送，都要拆单
			  
			    if($order_deal['delivery_type']==1){
			       $has_wuliu=1; 
			    }elseif($order_deal['delivery_type']==3){
			       $has_yizhan=1; 
			    }elseif($order_deal['delivery_type']==2){  //平台无需配送商品
			       $has_nodlivery=1; 
			    }
			}else{
			    if($order_deal['is_shop']==0){  //团购
			        $has_stuan=1;
			    }else{//商城商品
			        $has_sshop=1;
			    }
			}
			
		}
		//判断该订单是否需要拆单，$is_main，是否是订单的主单，1为订单的主单,则需要进行拆单，0为订单的子单
		$is_main=0;
		if(count($supplier)>1 ||( $has_wuliu==1 && $has_yizhan==1)){ //多个普通商家和平台自营商品物流配送和驿站配送，都要拆单
		    $is_main=1;
		}

		
		foreach($deal_ids as $row)
		{
			//验证支付方式的支持
			if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$row['deal_id'])==1)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_payment where deal_id = ".$row['deal_id']." and payment_id = ".$payment))
				{
					return output($root,0,"支付方式不支持");
				}
			}
		}
			
			
		//结束验证购物车
		//开始验证订单接交信息
	
		$data = count_buy_total($region_id,$consignee_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword,$goods_list,0,0,'',$all_score,0,$youhui_ids);
	
		if(!$consignee_info && $location_id==0 && ( $data['is_delivery']==1 || $data['is_pick']==1))
		{
			return output($root,0,"请设置收货地址");
		}

		//结束验证订单接交信息
	      
		if(count($supplier)==1){  //订单只有单个商家时，保存商家ID
		    $order['supplier_id']=end($supplier);
		}else{
		    $has_stuan=0;  //是否有商家团购商品
		    $has_sshop=0;  //是否有商家商城商品
		    $has_wuliu=0;  //平台自营物流配送订单
		    $has_yizhan=0; //平台自营驿站配送订单
		}
		
		$user_id = $GLOBALS['user_info']['id'];
		//开始生成订单
		$now = NOW_TIME;
		$type=0;
		if($data['return_total_score'] < 0){
		    $type =2;  //积分兑换订单
		}elseif($has_stuan==1){
		    $type=5;   //商家团购订单
		}elseif($has_sshop==1){
		    $type=6;   //商家商品订单
		}elseif( ($has_wuliu==1 && $has_yizhan==0) || $has_nodlivery==1){
		    $type=3;  //平台自营物流配送订单
		}elseif($has_wuliu==0 && $has_yizhan==1){
		    $type=4;  //平台自营驿站配送订单
		}

		
		$delivery_fee = 0;
		if($consignee_id > 0){
		    $delivery_list = get_express_fee($cart_result['cart_list'],$consignee_id);
		    if($delivery_list){
		        foreach($delivery_list as $k=>$v){
		            $supplier_data[$k]['delivery_fee'] = $v['total_fee'];
		            
		            $delivery_fee += $v['total_fee'];
		        }
		    } 
		}

		//订单分配代理商id
		if($is_main==1){//需要拆单的订单
			foreach($supplier_data as $k=>$v){
				if($k=="p_wl"){//物流
					$supplier_data[$k]['agency_id']=0;
				}elseif($k=="p_yz"){//驿站
					if($consignee_info){
						if($consignee_info['region_lv3_code']){
							$agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code = '".$consignee_info['region_lv3_code']."'");
							$supplier_data[$k]['agency_id']=intval($agency_id);
						}
					}
				}else{//商户
					$agency_id=$GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id = '".$k."'");
					$supplier_data[$k]['agency_id']=intval($agency_id);
				}
				$supplier_data[$k]['youhui_data'] = $data['youhui_data'][$k];
			}
			$order['youhui_money']=$data['youhui_money'];

		}else{//不需要拆单的订单
			if($order['supplier_id']>0){//存在商户的订单
				 $agency_id= $GLOBALS['db']->getOne("select agency_id from ".DB_PREFIX."supplier where id = ".$order['supplier_id']);
				 $order['agency_id']=intval($agency_id);
			}elseif($type==4){//驿站订单
				if($consignee_info){
					if($consignee_info['region_lv3_code']){
						$agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code = '".$consignee_info['region_lv3_code']."'");
						$order['agency_id']=intval($agency_id);
					}
				}
			}
			$youhui_data = end($data['youhui_data']);
			$order['youhui_money']=$youhui_data['youhui_money'];
			$order['youhui_log_id']=$youhui_data['youhui_log_id'];
		}
		
		$order['type'] = $type; //普通订单
		$order['user_id'] = $user_id;
		$order['create_time'] = $now;
		$order['total_price'] = $data['pay_total_price'];  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
		
		$order['pay_amount'] = 0;
		$order['pay_status'] = 0;  //新单都为零， 等下面的流程同步订单状态

		$order['delivery_status'] = $data['is_consignment']==0?5:0;
		$order['order_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
		$order['return_total_score'] = $data['return_total_score'];  //结单后送的积分
		$order['return_total_money'] = $data['return_total_money'];  //结单后送的现金
		$order['memo'] = $memo;
		$order['region_lv1'] = intval($consignee_info['region_lv1']);
		$order['region_lv2'] = intval($consignee_info['region_lv2']);
		$order['region_lv3'] = intval($consignee_info['region_lv3']);
		$order['region_lv4'] = intval($consignee_info['region_lv4']);
		$order['address']	= strim($consignee_info['address']);
		$order['mobile']	=	strim($consignee_info['mobile']);
		$order['consignee']	=	strim($consignee_info['consignee']);
		$order['street']	=	strim($consignee_info['street']);
		$order['doorplate']	=	strim($consignee_info['doorplate']);
		$order['zip']	=	strim($consignee_info['zip']);
		$order['consignee_id']	=	intval($consignee_id);
		$order['deal_total_price'] = $data['total_price'];   //团购商品总价
		$order['discount_price'] = $data['user_discount'];
		$order['delivery_fee'] = $delivery_fee;
		$order['record_delivery_fee'] = $delivery_fee;
		$order['ecv_money'] = 0;
		$order['account_money'] = 0;
		$order['ecv_sn'] = '';
		$order['delivery_id'] =0;
		$order['payment_id'] = $data['payment_info']['id'];
		$order['payment_fee'] = $data['payment_fee'];
		$order['bank_id'] = "";
		$order['is_main'] = $is_main;
		$order['location_id'] = $location_id;
		$order['supplier_data'] = serialize($supplier_data);
		$order['invoice_info'] = $firstInvoice;
		$order['is_all_balance'] = 0;
		foreach($data['promote_description'] as $promote_item)
		{
			$order['promote_description'].=$promote_item."<br />";
		}
		$order['promote_arr']=serialize($data['promote_arr']);
		//更新来路
		$order['referer'] =	$GLOBALS['referer'];
		$user_info = es_session::get("user_info");
		$order['user_name'] = $user_info['user_name'];
		$order['exchange_money'] = $data['exchange_money'];
		$order['score_purchase'] = serialize($data['score_purchase']);
		
		if($is_main==0&&($type==5||$type==6)&&defined("FX_LEVEL")){
			$ref_salary_conf = unserialize(app_conf("REF_SALARY"));
			$ref_salary_switch=intval($ref_salary_conf['ref_salary_switch']);
			if($ref_salary_switch==1){//判断后台是否开启推荐商家入驻三级分销
				$order['is_participate_ref_salary'] = 1;
			}
		}
		
		do
		{
			$order['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT');
			$order_id = intval($GLOBALS['db']->insert_id());
			
		}while($order_id==0);

		//先计算用户等级折扣
		$user_id = intval($GLOBALS['user_info']['id']);
		$user_discount_percent = 1;
		if ($user_id) {
			$user_discount_percent = getUserDiscount($user_id);
		}

		foreach($goods_list as $k=>$v)
		{
			$deal_info = load_auto_cache("deal",array("id"=>$v['deal_id']));
			$goods_item = array();
			
			//关于fx
			if($deal_info['is_fx'])
			{
				/*$fx_user_id = intval($GLOBALS['ref_uid']);
				if($fx_user_id)
				{
					$user_deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_deal where deal_id = '".$deal_info['id']."' and user_id = '".$fx_user_id."'");
					if($user_deal||$deal_info['is_fx'])
						$goods_item['fx_user_id'] =  $fx_user_id;
				}*/

				// 更改为推荐注册人
				$fx_user_id = intval($GLOBALS['user_info']['pid']);
				if ($fx_user_id) {
					$goods_item['fx_user_id'] =  $fx_user_id;
				}
			}
			//关于fx

			$goods_item['deal_id'] = $v['deal_id'];
			$goods_item['number'] = $v['number'];
			$unit_price = $v['unit_price'];
			if ($v['allow_user_discount']) {
				$unit_price = round($unit_price * $user_discount_percent, 2);
			}
			$goods_item['unit_price'] = $v['unit_price'];
			$goods_item['total_price'] = $v['total_price'];
			$goods_item['discount_unit_price'] = $unit_price;  //商品折扣后单价
			$goods_item['name'] = $v['name'];
			$goods_item['sub_name'] = $v['sub_name'];
			$goods_item['attr'] = $v['attr'];
			$goods_item['verify_code'] = $v['verify_code'];
			$goods_item['order_id'] = $order_id;
			$goods_item['return_score'] = $v['return_score'];
			$goods_item['return_total_score'] = $v['return_total_score'];
			$goods_item['return_money'] = $v['return_money'];
			$goods_item['return_total_money'] = $v['return_total_money'];
			$goods_item['buy_type']	=	$v['buy_type'];
			$goods_item['attr_str']	=	$v['attr_str'];
			
			$goods_item['add_balance_price'] = $v['add_balance_price'];
			$goods_item['add_balance_price_total'] = $v['add_balance_price'] * $v['number'];
			$goods_item['balance_unit_price'] = $deal_info['balance_price'];
			$goods_item['balance_total_price'] = $deal_info['balance_price'] * $v['number'];
			
			$goods_item['supplier_id'] = $deal_info['supplier_id'];
			$goods_item['delivery_type'] = $deal_info['delivery_type'];
			if($is_main==1){ //如果是需要拆单的主单
			    // $supplier_data
			    if($goods_item['supplier_id']==0){  
			        if($goods_item['delivery_type']==1){  //物流配送
                        $supplier_id = 'p_wl';			            
			        }elseif($goods_item['delivery_type']==2){  //无需配送
			            $supplier_id = 0;			            
			        }elseif($goods_item['delivery_type']==3){  //驿站配送
			            $supplier_id = 'p_yz';			            
			        }			         
			    }else{
			        $supplier_id = $goods_item['supplier_id'];
			    }
			    $youhui_money = $supplier_data[$supplier_id]['youhui_data']['youhui_money'];
			    $total_price = $supplier_data[$supplier_id]['youhui_data']['total_price'];
			    $origin_total_price = $supplier_data[$supplier_id]['youhui_data']['origin_total_price'];
			    
			}else{
			     $youhui_money = $order['youhui_money'];
			     $total_price = $order['total_price'];
			     $origin_total_price = $order['deal_total_price'];
			}

			if($youhui_money >= $total_price){
			    $goods_item['add_balance_price'] = 0;
			    $goods_item['add_balance_price_total'] = 0;
			    $goods_item['balance_unit_price'] = 0;
			    $goods_item['balance_total_price'] = 0;
			}else{
			    $rate = $goods_item['total_price'] / $origin_total_price;
			    $youhui_money = $youhui_money * $rate;
			     
			    if($youhui_money >= $goods_item['add_balance_price_total'] + $goods_item['balance_total_price']){
			        $goods_item['add_balance_price'] = 0;
			        $goods_item['add_balance_price_total'] = 0;
			        $goods_item['balance_unit_price'] = 0;
			        $goods_item['balance_total_price'] = 0;
			    }else{
			        $rate2 = $goods_item['add_balance_price_total'] /$goods_item['balance_total_price'] + $goods_item['add_balance_price_total'];
			        $rate3 = $goods_item['balance_total_price'] /$goods_item['balance_total_price'] + $goods_item['add_balance_price_total'];
			        $goods_item['add_balance_price_total'] -= $youhui_money * $rate2 ;
			        $goods_item['add_balance_price'] = $goods_item['add_balance_price_total'] / $v['number'] ;
			        $goods_item['balance_total_price'] -= $youhui_money * $rate3 ;
			        $goods_item['balance_unit_price'] = $goods_item['balance_total_price'] / $v['number'] ;
			    }
			}


			$goods_item['deal_icon'] = $deal_info['icon'];
			$goods_item['is_refund'] = $deal_info['is_refund'];
			$goods_item['user_id'] = $user_id;
			$goods_item['order_sn'] = $order['order_sn'];
			$goods_item['is_shop'] = $deal_info['is_shop'];

			$deal_data = array();
			$deal_data['dist_service_rate'] = $deal_info['dist_service_rate'];
			$deal_data['recommend_user_id'] = $deal_info['recommend_user_id'];
			$deal_data['recommend_user_return_ratio'] = $deal_info['recommend_user_return_ratio'];
			$goods_item['deal_data'] = serialize($deal_data);

			$goods_item['distribution_fee'] = ($goods_item['total_price'] - $goods_item['balance_total_price'] - $goods_item['add_balance_price_total'])*$deal_info['dist_service_rate']/100;

			if($location_id > 0){
			    $goods_item['is_pick'] = 1;
			}else{
			    $goods_item['is_pick'] = 0;
			}
			
			$goods_item['is_coupon'] = $goods_item['is_pick']==1?1:$deal_info['is_coupon'];
			
			$goods_item['delivery_status'] = $data['is_consignment']==0?5:0;
			$goods_item['is_delivery'] = $location_id > 0 ? 0:$deal_info['is_delivery'];
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_item",$goods_item,'INSERT','','SILENT');
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where id = '".$v['id']."'");
		}
	
		//开始更新订单表的deal_ids
		$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set deal_ids = '".$deal_ids."' where id = ".$order_id);
	
		if($data['youhui_data']){
    		require_once(APP_ROOT_PATH."system/model/biz_verify.php");
    		foreach($data['youhui_data'] as $k=>$youhui){
    		    online_youhui_use($youhui['youhui_log_id']);
    		}
		}
	
		//生成order_id 后
		//1. 代金券支付
		$ecv_data = $data['ecv_data'];
		if($ecv_data)
		{
			$ecv_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Voucher'");
			if($ecv_data['money']>$order['total_price'])$ecv_data['money'] = $order['total_price'];
			$payment_notice_id = make_payment_notice($ecv_data['money'],$order_id,$ecv_payment_id,"",$ecv_data['id']);
			require_once(APP_ROOT_PATH."system/payment/Voucher_payment.php");
			$voucher_payment = new Voucher_payment();
			$voucher_payment->direct_pay($ecv_data['sn'],$ecv_data['password'],$payment_notice_id);
		}
		//积分抵扣
		if($all_score==1){
			$score_purchase = $data['score_purchase'];
			if($score_purchase['score_purchase_switch']==1&&$data['exchange_money']>0){
				score_purchase_paid($score_purchase,$order_id);
			}
			
		}
	
		//2. 余额支付
		$account_money = $data['account_money'];
		if(floatval($account_money) > 0)
		{
			$account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
			$payment_notice_id = make_payment_notice($account_money,$order_id,$account_payment_id);
			require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
			$account_payment = new Account_payment();
			$account_payment->get_payment_code($payment_notice_id);
		}
	
// 		//3. 相应的支付接口
// 		$payment_info = $data['payment_info'];
// 		if($payment_info&&$data['pay_price']>0)
// 		{
// 			$payment_notice_id = make_payment_notice($data['pay_price'],$order_id,$payment_info['id']);
// 			//创建支付接口的付款单
// 		}
        if($is_main==1){ //如果是需要拆单的主单，则进行拆单
            syn_order($order_id);
        }
		$rs = order_paid($order_id);
		update_order_cache($order_id);
		if($rs)
		{
			$root['pay_status'] = 1;
			$root['order_id'] = $order_id;
		}
		else
		{
			distribute_order($order_id);
			$root['pay_status'] = 0;
			$root['order_id'] = $order_id;
			
		}

		return output($root);
	}

	
	/**
	 * 计算订单总价
	 *
	 * 输入:
	 * id:int 订单ID
	 * delivery_id: int 配送方式主键
	 * payment:int 支付方式ID
	 * all_account_money:int 是否使用余额支付 0否 1是
	 *
	 * 输出:
	 * pay_price:float 当前要付的余额，如为0表示不需要使用在线支付，则支付方式不让选中
	 * feeinfo: array 费用清单，结构如下
	 * Array(
	 * 	    Array(
			 "name" => "折扣", string 费用清单项名称
			 "value" => "7折" string 费用清单项内容
			 ),
	 * )
	 *
	 */	
	public function count_order_total()
	{
		require_once(APP_ROOT_PATH."system/model/cart.php");
		$order_id = intval($GLOBALS['request']['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		
		
		$address_id =  intval($GLOBALS['request']['address_id']); //配送方式
		$region_id = 0; //配送地区

		$consignee_id = $address_id;
		$consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
		$consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:array();
		if($consignee_info)
			$region_id = intval($consignee_info['region_lv4']);

		
		$ecvsn = '';
		$ecvpassword = '';
		$payment = intval($GLOBALS['request']['payment']);
		$all_account_money = intval($GLOBALS['request']['all_account_money']);
		$bank_id = '';
		
		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		
		$result = count_buy_total($region_id,$consignee_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword,$goods_list,$order_info['account_money'],$order_info['ecv_money'],$bank_id);
		
		$root = array();
		
		
		if($result['total_price']>0)
		{
			$feeinfo[] = array(
					"name" => "商品总价",
					"value" => round($result['total_price'],2)."元"
			);
		}
		
		
		if($result['user_discount']>0)
		{
			$feeinfo[] = array(
					"name" => "折扣",
					"value" => round(($result['total_price']-$result['user_discount'])/$result['total_price']*10,1)."折"
			);
		}
		
		if($result['delivery_info'])
		{
			$feeinfo[] = array(
					"name" => "配送方式",
					"value" => $result['delivery_info']['name']
			);
		}
		
		if($result['delivery_fee']>0)
		{
			$feeinfo[] = array(
					"name" => "运费",
					"value" => round($result['delivery_fee'],2)."元"
			);
		}
		
		
		
		if($result['payment_info'])
		{
			$directory = APP_ROOT_PATH."system/payment/";
			$file = $directory. '/' .$result['payment_info']['class_name']."_payment.php";
			if(file_exists($file))
			{
				require_once($file);
				$payment_class = $result['payment_info']['class_name']."_payment";
				$payment_object = new $payment_class();
				$payment_name = $payment_object->get_display_code();
			}
				
			$feeinfo[] = array(
					"name" => "支付方式",
					"value" => $payment_name
			);
		}
		
		if($result['payment_fee']>0)
		{
			$feeinfo[] = array(
					"name" => "手续费",
					"value" => round($result['payment_fee'],2)."元"
			);
		}
		
		if($result['account_money']>0)
		{
			$feeinfo[] = array(
					"name" => "余额支付",
					"value" => round($result['account_money'],2)
			);
		}
		
		if($result['ecv_money']>0)
		{
			$feeinfo[] = array(
					"name" => "红包支付",
					"value" => round($result['ecv_money'],2)
			);
		}
		
		if($result['buy_type']==0)
		{
			if($result['return_total_score'])
			{
				$feeinfo[] = array(
						"name" => "返还积分",
						"value" => round($result['return_total_score'])
				);
			}
		}
		
		if($result['return_total_money'])
		{
			$feeinfo[] = array(
					"name" => "返现",
					"value" => round($result['return_total_money'],2)."元"
			);
		}
		
		if($result['paid_account_money']>0)
		{
			$feeinfo[] = array(
					"name" => "已付",
					"value" => round($result['paid_account_money'],2)."元"
			);
		}
		
		if($result['paid_ecv_money']>0)
		{
			$feeinfo[] = array(
					"name" => "红包已付",
					"value" => round($result['paid_ecv_money'],2)."元"
			);
		}
		
		
		
		if($result['buy_type']==0)
		{
			$feeinfo[] = array(
					"name" => "总计",
					"value" => round($result['pay_total_price'],2)."元"
			);
		}
		else
		{
			$feeinfo[] = array(
					"name" => "所需积分",
					"value" => abs(round($result['return_total_score']))
			);
		}
		
		if($result['pay_price'])
		{
			$feeinfo[] = array(
					"name" => "应付总额",
					"value" => round($result['pay_price'],2)."元"
			);
		}
		
		if($result['promote_description'])
		{
			foreach($result['promote_description'] as $row)
			{
				$feeinfo[] = array(
						"name" => "",
						"value" => $row
				);
			}
		}
		$root['feeinfo'] = $feeinfo;
		$root['delivery_fee_supplier'] = $result['delivery_fee_supplier'];
		$root['delivery_info'] = $result['delivery_info'];
		$root['pay_price'] = round($result['pay_price'],2);
		$root['is_pick'] = $result['is_pick'];
		$root['result'] = $result;
		
		return output($root);
	}
	
	
	/**
	 * 订单继续支付接口
	 * 输入：
	 * order_id:int 订单ID
	 * payment:int 支付方式ID
	 * all_account_money:int 是否使用余额支付 0否 1是
	 * 
	 * 输出：
	 * status: int 状态 0:失败 1:成功 -1:未登录
	 * info: string 失败时返回的错误信息，用于提示
	 * 以下参数为status为1时返回
	 * pay_status:int 0未支付成功 1全部支付
	 * order_id:int 订单ID
	 * sdk_code 第三方支付的SDK_CODE
	 * 
	 */
	public function order_done()
	{
		require_once(APP_ROOT_PATH."system/model/cart.php");
		require_once(APP_ROOT_PATH."system/model/deal.php");
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		//验证购物车
		if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
		{
			return output(array(),-1,"请先登录");
		}
		
		$order_id = intval($GLOBALS['request']['order_id']);
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id." and is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']);
		
		if(empty($order))
		{
			return output(array(),0,"订单不存在");
		}
		if($order['refund_status'] == 1)
		{
			return output(array(),0,"订单退款中");
		}
		if($order['refund_status'] == 2)
		{
			return output(array(),0,"订单已退款");
		}		
		
    	$delivery_id =  $order['delivery_id']; //配送方式
		$consignee_id =  $order['consignee_id'];//会员收货地址id
		
		if( $GLOBALS['request']['from']=='wap'){
			$is_wap=true;
		}else{
			$is_wap=false;
		}
		
		$region_id = 0; //配送地区

		$consignee_info = load_auto_cache("consignee_info",array("consignee_id"=>$consignee_id));
		$consignee_info = $consignee_info['consignee_info']?$consignee_info['consignee_info']:array();
		if($consignee_info)
			$region_id = intval($consignee_info['region_lv4']);

		$payment = intval($GLOBALS['request']['payment']);
		$rel = strim($GLOBALS['request']['rel']);
		$all_account_money = intval($GLOBALS['request']['all_account_money']);
		$ecvsn = $GLOBALS['request']['ecvsn']?strim($GLOBALS['request']['ecvsn']):'';
		$ecvpassword = $GLOBALS['request']['ecvpassword']?strim($GLOBALS['request']['ecvpassword']):'';
		if(!$is_wap){
		    $memo = strim($GLOBALS['request']['content']);
		}
		
		
		$goods_list = $GLOBALS['db']->getAll("select doi.* , d.allow_user_discount from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id=d.id where doi.order_id = ".$order['id']);
		
		//结束验证购物车
		$deal_s = $GLOBALS['db']->getAll("select distinct(deal_id) as deal_id ,number  from ".DB_PREFIX."deal_order_item where order_id = ".$order['id']);
		//如果属于未支付的
		if($order['pay_status'] == 0)
		{
			foreach($deal_s as $row)
			{
				$checker = check_deal_number($row['deal_id'],$row['number']);
				if($checker['status']==0)
				{
					return output(array(),0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);		
				}
			}
				
			foreach($goods_list as $k=>$v)
			{
				$checker = check_deal_number_attr($v['deal_id'],$v['attr_str'],$v['number']);
				if($checker['status']==0)
				{
					return output(array(),0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);
				}
			}
				
			//验证商品是否过期
			foreach($deal_s as $row)
			{
				$checker = check_deal_time($row['deal_id']);
				if($checker['status']==0)
				{
					return output(array(),0,$checker['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$checker['data']]);
				}
			}
		}
			
			
		//结束验证购物车
		//开始验证订单接交信息
		$data = count_buy_total($region_id,$consignee_id,$payment,0,$all_account_money,$ecvsn,$ecvpassword,$goods_list,$order['account_money'],$order['ecv_money'],'',0,$order['exchange_money'],$youhui_ids=array(),$order['youhui_money']);
        //logger::write(print_r($data,1));exit;
	
		if(round($data['pay_price'],4)>0&&!$data['payment_info'])
		{
			return output(array(),0,"请选择支付方式");
		}
		//结束验证订单接交信息
	
		$user_id = $GLOBALS['user_info']['id'];
		//开始生成订单
		$now = NOW_TIME;
		$order['total_price'] = $data['pay_total_price'];  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
		$order['deal_total_price'] = $data['total_price'];   //团购商品总价
		$order['discount_price'] = $data['user_discount'];
		$order['delivery_fee'] = $data['delivery_fee'];
		$order['record_delivery_fee'] = $data['record_delivery_fee'];
		$order['payment_id'] = $data['payment_info']['id'];
		if($data['payment_info']['class_name']=="Cod"){
			$order['cod_mode'] = $rel;
		}else{
			$order['cod_mode'] = '';
		}
		$order['payment_fee'] = $data['payment_fee'];
		$order['bank_id'] = "";
		
		// 生成订单时已经写入了促销信息。这里重复了
		/*foreach($data['promote_description'] as $promote_item)
		{
			$order['promote_description'].=$promote_item."<br />";
		}*/
		$order['promote_arr']=serialize($data['promote_arr']);
		//更新来路
		
			
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'UPDATE','id='.$order['id'],'SILENT');
	
		//生成order_id 后		
	
		//2. 余额支付
		$account_money = $data['account_money'];
		if(floatval($account_money) > 0)
		{
			$account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
			$payment_notice_id = make_payment_notice($account_money,$order_id,$account_payment_id);
			require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
			$account_payment = new Account_payment();
			$account_payment->get_payment_code($payment_notice_id);
		}
		if($data['payment_info']['class_name']=="Cod"&&$data['pay_price']>0){
			$account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Cod'");
			$payment_notice_id = make_payment_notice($data['pay_price'],$order_id,$account_payment_id,'',0,array('COD_PAYMENT'=>$rel));
			require_once(APP_ROOT_PATH."system/payment/Cod_payment.php");
			$account_payment = new Cod_payment();
			$account_payment->get_payment_code($payment_notice_id);
		}
// 		//3. 相应的支付接口
// 		$payment_info = $data['payment_info'];
// 		if($payment_info&&$data['pay_price']>0)
// 		{
// 			$payment_notice_id = make_payment_notice($data['pay_price'],$order_id,$payment_info['id']);
// 			//创建支付接口的付款单
// 		}
        $root['app_index'] = APP_INDEX;
		$rs = order_paid($order_id);
		update_order_cache($order_id);
		if($rs)
		{
			$root['pay_status'] = 1;
			$root['order_id'] = $order_id;
		}
		else
		{
			distribute_order($order_id);
			
			if(APP_INDEX=='app'){
			    $data_pay = call_api_core("payment","done",array("id"=>$order_id));
			    $root['sdk_code'] = $data_pay['payment_code']['sdk_code'];
			    $root['pay_url'] = $data_pay['payment_code']['pay_action'];
                $root['online_pay'] = $data['payment_info']['online_pay'];
			    $root['title'] = $data_pay['title'];
			    $root['is_account_pay'] =$data_pay['is_account_pay'];
			}
			
			$root['pay_status'] = 0;
			$root['order_id'] = $order_id;

		}
		return output($root);
	}
	
	
	/**
	 * 多商品合并购买
	 * ids:array 结构如下
		Array(
			[0] => 64	int 商品id
			[1] => 85
			[2] => 87
		)
		
	 * deal_attr: array 结构如下
		 Array(
			[64] => Array(	//商品属性(与购买单个商品的属性结构一样)
				[19] => 335
				[20] => 337
			)
		)
	*
	 * 输出：
	 * status: int 状态 0有错误 1加入成功 -1未登录需要登录 -2没有可以购买的产品
	 * 当status=0时 表示有部分商品加入出错 返回格式 
		Array(
			[商品id] => Array(	//这里的结构跟单条加入购物车的错误提示一样
				
					[status] => 0
					[info] => 请选择商品规格
				)
			[84] => Array(
					[status] => 0
					[info] => 没有可以购买的商品
				)
		)
	 *		
	*/
	public function addcartByRelate(){
		if($GLOBALS['request']['from']=="app"){
			$GLOBALS['request']['ids']=json_decode($GLOBALS['request']['ids'],true);
			$GLOBALS['request']['deal_attr']=json_decode($GLOBALS['request']['deal_attr'],true);
			$GLOBALS['request']['idnumArray']=json_decode($GLOBALS['request']['idnumArray'],true);
		}
		//商品id数组
		$ids 		= $GLOBALS['request']['ids'];
		//商品属性数组
		$deal_attr  = $GLOBALS['request']['deal_attr'];
		$idnumArray  = $GLOBALS['request']['idnumArray'];
		if( empty($ids)||!is_array($ids) ){
// 			return output("",-2,"没有可以购买的产品");
		    return output("",-2,"请在手机端购买");
		}
		if( $GLOBALS['request']['from']=='wap'){
		    $is_wap=true;
		}else{
		    $is_wap=false;
		}
		//先判断数量是否符合限购
		$result = array();
		require_once(APP_ROOT_PATH.'system/model/deal.php');
		foreach($ids as $id){
			$id = intval($id);
			if( $id>0 ){
				$param = array(
						'id'	=>	$id,
						'is_wap' => $is_wap,
						'num' => $idnumArray[$id],
				);
				if(!empty($deal_attr[$id])){
					$param['attr'] = $deal_attr[$id];
				}
				$tmpData = check_deal_number($id,$idnumArray[$id],false);
				if($tmpData['status']==0)
				{
					$tmpData['info'] = $tmpData['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$tmpData['data']];
				}else{
					
					if($param['attr']){
						foreach ($param['attr'] as $k=>$v)
						{
							$sv = intval($v);
							if($sv)
								$deal_attr_s[$k] = intval($sv);
						}

						//加入购物车处理，有提交属性， 或无属性时
						$attr_str = '0';
						$attr_name = '';
						$attr_name_str = '';
						if($deal_attr_s)
						{
							$attr_str = implode(",",$deal_attr_s);
							$attr_names = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_attr where id in(".$attr_str.")");
							$attr_name = '';
							foreach($attr_names as $attr)
							{
								$attr_name .=$attr['name'].",";
								$attr_name_str.=$attr['name'];
							}
							$attr_name = substr($attr_name,0,-1);
						}

						if($attr_name_str!='')
						{
							$tmpData = check_deal_number_attr($id,$attr_name_str,$idnumArray[$id]);
							if($tmpData['status']==0)
							{
								$tmpData['info'] = $tmpData['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$tmpData['data']];
							}else{
								unset($tmpData['info']);
							}
						}else{
							unset($tmpData['info']);
						}
					}else{
						unset($tmpData['info']);
					}
				}
				if( !empty($tmpData['info']) ){
					$result[$id] = $tmpData;
				}
			}
		}
		if(!empty($result) ){
			$data=array();
			foreach ($result as $k=>$v){
				if( is_numeric($k) ){
					$data['info'][$k] = $v['info'];
				}
			}
			return output($data,0,$data['info']);
		}
		$result = array();
		foreach($ids as $id){
			$id = intval($id);
			if( $id>0 ){
				$param = array(
					'id'	=>	$id,
				    'is_wap' => $is_wap,
					'num' => $idnumArray[$id],
				);
				if(!empty($deal_attr[$id])){
					$param['attr'] = $deal_attr[$id];
				}
				
				$tmpData = $this->addcart(false,$param);
				if( $tmpData['status']==-1 ){
					return output('',-1,'请先登录');
				}else if( !empty($tmpData['info']) ){
					$result[$id] = $tmpData;
				}
			}	
		}
		if( empty($result) ){
			return output($result);
		}else{
			return output($result,0);
		}
	}
	
	
	public function clear_deal_cart(){
	    $root = array();
	    //if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
	    //{
	    //    return output(array(),-1,"请先登录");
	  	// }
		if($GLOBALS['request']['from']=="app"){
			$GLOBALS['request']['id']=json_decode($GLOBALS['request']['id'],true);
		}
	    $id= $GLOBALS['request']['id'];
	    $user_info=$GLOBALS['user_info'];
	    $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where user_id = " . intval($user_info['id'])." and id in (".implode(",",$id).")");
	    if($GLOBALS['db']->affected_rows() > 0){
	        return output($root,1,"删除成功");
	    }else{
	        return output($root,1,"删除失败");
	    }
	}
	
	
	
	public function get_cart_deal_attr(){
	    $root = array();
	    /* if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
	    {
	        return output(array(),-1,"请先登录");
	    } */
	    $id= $GLOBALS['request']['id'];
	    $attr_key= $GLOBALS['request']['attr_key'];
	    $user_info=$GLOBALS['user_info'];
	    require_once(APP_ROOT_PATH."system/model/deal.php");
	    $cart_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cart where id=".$id);
	    $deal_info_all = get_deal($cart_info['deal_id']);
		
		
		$deal_info['deal_attr_stock_json']=$deal_info_all['deal_attr_stock_json'];
		$deal_info['current_price']=$deal_info_all['current_price'];
		$deal_info['f_current_price']=format_price_html(round($deal_info_all['current_price'],2),3);
		$deal_info['attr_num']=$deal_info_all['attr_num'];
		$deal_info['name']=$deal_info_all['name'];
		$deal_info['icon']=$deal_info_all['icon'];
		$deal_info['buy_type']=$deal_info_all['buy_type'];
		$deal_info['deal_score']=$deal_info_all['deal_score'];
		$deal_info['deal_attr']=$deal_info_all['deal_attr'];
		
	    $attr_arr = explode(",",$attr_key);
	    $deal_attr = $deal_info_all['deal_attr'];
	    $choose_attr_name='';
	    if($deal_attr){
	        foreach($deal_attr as $k=>$v){
	            foreach($v['attr_list'] as $kk=>$vv){
	                if(in_array($vv['id'], $attr_arr)){
	                    $deal_attr[$k]['attr_list'][$kk]['is_choose'] = 1;
	                    $choose_attr_name+=$vv['name']." ";
	                }else{
	                    $deal_attr[$k]['attr_list'][$kk]['is_choose'] = 0;
	                }
					
	            }
	        }
	    }
	   $deal_info['icon'] = get_abs_img_root(get_spec_image( $deal_info_all['icon'],280,280,1)) ;
	   $deal_info['deal_attr'] = $deal_attr;
	   $deal_info['deal_attr_json'] = json_encode($deal_attr);
	   $root['deal_info'] = $deal_info;
	   return output($root,1,"成功");

	}
	
	
	public function set_cart_status(){
	    $root = array();
		$root['user_login_status']=check_login();
	    if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
	    {
	        return output($root,-1,"请先登录");
	    }
	    $user_info=$GLOBALS['user_info'];
	    if(!$user_info['mobile']){
	        return output($root,-2,"请先绑定手机");
	    }
	    $checked_ids= $GLOBALS['request']['checked_ids'];
	    $nochecked_ids= $GLOBALS['request']['nochecked_ids'];
	    

	    // app_type，来判断是从原生页面过来，还是套壳的H5页面, app_type为1时，为原生页面提交过来的JSON字符串，需要JSON解析。
	    $app_type = intval($GLOBALS['request']['app_type']);
	    if($app_type==1){
	        $checked_ids=json_decode($checked_ids,true);
	        $nochecked_ids=json_decode($nochecked_ids,true);
	    }

	    
	    $checked_id = array();
	    if($checked_ids){ //选择的，把购物车的状态改为有效
	        foreach($checked_ids as $k=>$v){
	            $checked_id[] = $v['id'];
	        }
	        
	        $GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=1 where user_id = " . $user_info['id']." and id in (".implode(",",$checked_id).")"); 
	    }
	    
	    $cart_data_all = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cart where user_id = " . $user_info['id']." and id in (".implode(",",$checked_id).")");
	    if(empty($cart_data_all)){
	        $root['jump'] = wap_url('index','cart');
	        return output($root,0,"数据不存在");
	    }   

	    require_once APP_ROOT_PATH.'system/model/cart.php';
	    //验证提交
	    $result = cheak_wap_cart($checked_ids);
	    
	    if($result['status']==0){
	        return output($root,0,$result['info']);
	    }
	    
	    $no_checked_id = array();
	    if($nochecked_ids){ //未选择的，把购物车的状态改为无效
	        foreach($nochecked_ids as $k=>$v){
	            $no_checked_id[] = $v['id'];
	        }
	        $GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=0 where user_id = " . $user_info['id']." and id in (".implode(",",$no_checked_id).")");
	    }
	    

	    if($checked_ids){  //把选择中的商品，更新购物车中的信息，包括数量，属性，单价，总价

	        foreach($checked_ids as $k=>$v){
	            $cart_data = array();
	           // $cart_data['id'] = $v['id'];
	            $cart_data['attr'] = $v['attr'];
	            $cart_data['attr_str'] = $v['attr_str'];
	            $cart_data['number'] = $v['number'];
	            
	            $cart_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cart where user_id = " . $user_info['id']." and id=".$v['id']);
	            $deal_info=$GLOBALS['db']->getRow("select id ,name, current_price , balance_price,return_score,return_money from ".DB_PREFIX."deal where id=".$cart_info['deal_id']);
	            $cart_data['unit_price'] = $deal_info['current_price'];
				$cart_data['return_score'] = $deal_info['return_score'];
				$cart_data['return_money'] = $deal_info['return_money'];
	            $cart_data['add_balance_price'] =0;
	           // $cart_attr = explode(",",$cart_data['attr']);
	            if($cart_data['attr']){
	                
	                $attr_key = implode('_', explode(",",$cart_data['attr']));
					$add_price_sql = 'SELECT price, add_balance_price FROM '.DB_PREFIX.'attr_stock WHERE deal_id='.$cart_info['deal_id'].' AND attr_key="'.$attr_key.'"';
                	$add_price_info = $GLOBALS['db']->getRow($add_price_sql);
                	if ($add_price_info) {
                		$cart_data['unit_price'] += $add_price_info['price'];
                		$cart_data['add_balance_price'] += $add_price_info['add_balance_price'];
                	}
                	$cart_data['name']=$deal_info['name']."[".$v['attr_str']."]";
                    /*$deal_attr_info=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_attr where deal_id = ".$cart_info['deal_id']." and id in (".$cart_data['attr'].")");
                   
                    foreach($deal_attr_info as $kk=>$vv){
                        $cart_data['unit_price'] += $vv['price'];
                        $cart_data['add_balance_price'] += $vv['add_balance_price'];
                    }*/
	            }

	            $cart_data['total_price'] = $cart_data['unit_price'] *  $cart_data['number'];
				$cart_data['return_total_score'] = $cart_data['return_score'] *  $cart_data['number'];
				$cart_data['return_total_money'] = $cart_data['return_money'] *  $cart_data['number'];
	            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_cart", $cart_data, $mode = 'UPDATE', 'id ='.$v['id'], $querymode = 'SILENT');
	        
	        }
	         
	    }
	    
	    

	     return output($root,1,"成功");

	}
	

	
	/**
	 * 收银台
	 *
	 * 输入:
	 * 无
	 * id: int 订单ID
	 * payment: int 支付方式
	 * all_account_money 是否全额支付
	 * 
	 *
	 * 输出:	
	 * order_id : int 订单ID
	 * total_price 合计
	 * pay_amount 已付金额
	 * payment_fee 手续费
	 * pay_price 待付金额
	 * has_account是否显示余额支付
	 * account_money 用户wtp
	 *  payment_list => Array 支付方式
        (
            [0] => Array
                (
                    [id] => 22
                    [code] => WxApp
                    [logo] => 
                    [name] => 微信支付
                )
            [1] => Array
                (
                    [id] => 24
                    [code] => Upacpapp
                    [logo] => 
                    [name] => 银联支付
                )

        )
     */
	public function pay()
	{
	    require_once APP_ROOT_PATH.'system/model/cart.php';
	
	    $user_info=$GLOBALS['user_info'];
	    $root = array();
	    $user_login_status = check_login();
	    $root['user_login_status'] = $user_login_status;
		if((check_login()==LOGIN_STATUS_TEMP&&$GLOBALS['user_info']['money']>0)||check_login()==LOGIN_STATUS_NOLOGIN)
		{
			return output($root,-1,"请先登录");
		}
	
		$order_id = intval($GLOBALS['request']['id']);
		$order_status = check_order($order_id);
		
		if(!$order_status){
		    return output($root,0,"非法数据");
		}
		
		$payment = intval($GLOBALS['request']['payment']);
		$all_account_money = intval($GLOBALS['request']['all_account_money']);
		

		$root['order_id'] = $order_id;
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id." and is_delete = 0 and user_id = ".$GLOBALS['user_info']['id']);
		if(empty($order_info))
		{
		    return output($root,0,"订单不存在");
		}
		if($order_info['pay_status']==2)
		{
		    return output($root,2,"订单已付款");
		}
		$total_price = $order_info['total_price'];
		
		
		$is_weixin = isWeixin();
		
		
		//处理购物车输出
		$cart_list = $GLOBALS['db']->getAll("select doi.*,d.id as did,d.icon,d.uname as duname from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where doi.order_id = ".$order_info['id']);
		$root['cart_list'] = $cart_list;
		$root['is_main'] = $order_info['is_main'];
        //输出支付方式
		//$app_index = APP_INDEX; APP,SDK支付做好后，使用这个判断
	
		
		$consignee_id=$order_info['consignee_id'];
		$result = count_buy_total($order_info['region_lv4'],$consignee_id,$payment,0,$all_account_money,$ecvsn='',$ecvpassword='',$cart_list,$order_info['account_money'],$order_info['ecv_money'],$bank_id=0,0,$order_info['exchange_money']);
		$root['total_price'] = $order_info['total_price'] - $order_info['payment_fee'];
		$root['total_price'] = round($root['total_price'],2);
		$root['pay_amount'] = round($order_info['pay_amount'],2);

		$root['pay_price'] = $order_info['total_price'] - $order_info['payment_fee'] - $order_info['pay_amount'] -$order_info['youhui_money'] + $result['payment_fee'];


    	$root['pay_price'] = round($root['pay_price'],2);
		$root['payment_fee'] = $result['payment_fee'];
		$root['all_account_money']=$all_account_money;
		$root['payment']=0;
		if($result['payment_info']){
		    $root['payment']=$result['payment_info']['id'];
            $root['rel']=$GLOBALS['request']['rel'];
		}

		$root['show_payment'] = 1;
		
	    $web_payment_list = load_auto_cache("cache_payment");

	    foreach($web_payment_list as $k=>$v)
	    {
	        if($v['class_name']=="Account"&&$GLOBALS['user_info']['money']>=$root['pay_price'])
	        {
	            $root['has_account'] = 1;
	        }
	    }
		    	

		if(intval($GLOBALS['request']['is_ajax'])==0&&$root['has_account']==1){
			$root['all_account_money']=1;
		}
		$root['account_money'] = round($GLOBALS['user_info']['money'],2);
		
		
		if($root['pay_price'] > 0){

    		if (APP_INDEX=='wap'  && $is_weixin)
    		{
    		    //支付列表
    		    $sql = "select id, class_name as code, logo,config from ".DB_PREFIX."payment where (online_pay = 2 or online_pay = 4 or online_pay = 5 or online_pay = 6) and is_effect = 1";
    		}elseif (APP_INDEX=='wap' && !$is_weixin)
    		{
    		    //支付列表
    		    $sql = "select id, class_name as code, logo,config from ".DB_PREFIX."payment where (online_pay = 2 or online_pay = 4 or online_pay = 5 or online_pay = 6) and is_effect = 1 and class_name !='Wwxjspay'";
    		}
    		else
    		{
    		    //支付列表
    		    $sql = "select id, class_name as code, logo,config from ".DB_PREFIX."payment where (online_pay = 3 or online_pay = 4 or online_pay = 5 or online_pay = 6) and is_effect = 1";
    		}
    		
    		
    		if(allow_show_api())
    		{
    		    $payment_list = $GLOBALS['db']->getAll($sql);
    		}
    		
    		
    		foreach($cart_list as $k=>$v)
    		{
    		    if($GLOBALS['db']->getOne("select define_payment from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
    		    {
    		        $define_payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_payment where deal_id = ".$v['deal_id']);
    		        $define_payment = array();
    		        foreach($define_payment_list as $kk=>$vv)
    		        {
    		            array_push($define_payment,$vv['payment_id']);
    		        }
    		        foreach($payment_list as $k=>$v)
    		        {
    		            if(in_array($v['id'],$define_payment))
    		            {
    		                unset($payment_list[$k]);
    		            }
    		        }
    		    }
    		}
    		
    		
    		foreach($payment_list as $k=>$v)
    		{
    		
    		    $directory = APP_ROOT_PATH."system/payment/";
    		    $file = $directory. '/' .$v['code']."_payment.php";
    		    if(file_exists($file))
    		    {
    		        require_once($file);
    		        $payment_class = $v['code']."_payment";
    		        $payment_object = new $payment_class();
    		        $payment_list[$k]['name'] = $payment_object->get_display_code();
    		    }
    		
    		    if($v['logo']!="")
    		        $payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'],40,40,1));
    		
    		    if($v['code']=="Cod"){//非驿站订单
    		        $v=$payment_list[$k];
    		        unset($payment_list[$k]);
    		    }
    		    //echo $order_info['type'];echo $v['code'];
    		    if($order_info['type']==4&&$v['code']=="Cod"){
    		        $Cod_config=unserialize($v['config']);
    		        //echo "<pre>";print_r($Cod_config);exit;
					$payment_lang=$payment_object->payment_lang;
    		        if(count($Cod_config)>0){
    		            $Cod_arr=array();
    		            $Cod=$v['name'];
    		            foreach($Cod_config['COD_PAYMENT'] as $key=>$val){
    		                $v['name']=$Cod."(".$payment_lang['COD_PAYMENT_'.$key].")";
    		                $v['rel']=$key;
    		                $Cod_arr[]=$v;
    		            }
    		        }else{
    		            $Cod_arr[]=$v;
    		        }
    		
    		    }
    		}
    		if(count($payment_list)>0&&count($Cod_arr)>0){
    		    $payment_list=array_merge($payment_list,$Cod_arr);
    		}elseif(count($payment_list)>0&&count($Cod_arr)==0){
    		
    		}elseif(count($payment_list)==0&&count($Cod_arr)>0){
    		    $payment_list=$Cod_arr;
    		}else{
    		
    		}
    		sort($payment_list);
    		$root['payment_list'] = $payment_list;
		
		}

	    $root['page_title'] = "收银台";
	    
	    return output($root);
	}
}
?>