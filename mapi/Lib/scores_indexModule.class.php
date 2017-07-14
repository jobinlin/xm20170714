<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class scores_indexApiModule extends MainBaseApiModule
{
	
	/**
	 * 积分商品页面接口
	 * 输入：
	 * cate_id: int 商品分类ID
	 * bid: int 品牌ID
	 * page:int 当前的页数
	 * keyword: string 关键词
	 * order_type: string 排序类型(default(默认)/avg_point(评价倒序)/newest(时间倒序)/buy_count(销量倒序)/price_asc(价格升序)/price_desc(价格降序))

	 * 输出：
	 * user_info:array 用户首页显示信息
	 * cate_id:int 当前分类ID
	 * bid:int 当前品牌ID
	 * page_title:string 页面标题
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * item:array:array 团购列表，结构如下
	 *  Array
        (
            [0] => Array
                (
                    [id] => 74 [int] 商品ID
                    [name] => 仅售75元！价值100元的镜片代金券1张，仅适用于镜片，可叠加使用。[string] 商品名称
                    [sub_name] => 镜片代金券 [string] 商品短名称
                    [brief] => 【36店通用】明视眼镜 [string] 商品简介
                    [buy_count] => 1 [int] 销量
                    [deal_score] => 75 [int] 所需积分
                    [origin_price] => 100 [float] 原价
                    [icon] => http://localhost/o2onew/public/attachment/201502/25/17/54ed9d05a1020_140x85.jpg [string] 团购图片 140x85
                    [end_time_format] => 2017-02-28 18:00:08 [string] 格式化的结束时间
                    [begin_time_format] => 2015-02-25 18:00:10 [string] 格式化的开始时间
                    [begin_time] => 1424829610 [int] 开始时间戳
                    [end_time] => 1488247208 [int] 结束时间戳
                    [is_refund] => [int] 随时退 0:否 1:是
                    [url] => http://localhost/o2onew/......  商品链接
                )
         )

	 * brand_list:array 品牌列表
	 * 结构如下
	 * Array(
	 * 		Array
	        (
	            [id] => 0 [int] 品牌ID
	            [name] => xx [string] 品牌名称
	        )
	 * )
	 * navs:array 排序菜单 
	 * 固定数据如下
	 * array(
			array("name"=>"默认","code"=>"default"),
			array("name"=>"好评","code"=>"avg_point"),
			array("name"=>"最新","code"=>"newest"),
			array("name"=>"销量","code"=>"buy_count"),
			array("name"=>"价格最低","code"=>"price_asc"),
			array("name"=>"价格最高","code"=>"price_desc"),
		);
	 * [user_info]：Array用户信息表
	 * array
            (
                [user_login_status] => 1[int]是否登录。1为登录，0为未登录
                [uid] => 71 用户ID
                [user_name] => fanwe 用户名
                [user_money_format] => &yen;9023.5 用户余额
                [user_score] => 98014 积分
                [user_score_format] => 98014积分 剩于积分
                [user_avatar] => http://localhost/o2onew/public/avatar/000/00/00/71virtual_avatar_big.jpg 头像
                [level_name] => 幼儿园  [string]会员等级
                [level_id] => 1 [int]会员等级ID
                [total_score] => 130 [int]累计会员积分
                [point] => 800  [int]会员经验值
            )
	 */
	public function index()
	{
		global $is_app;
		$root = array();
	
		$page = intval($GLOBALS['request']['page']); //分页
		$page=$page==0?1:$page;
	
        /*获取会员信息*/
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
		$root['user_login_status'] = $user_login_status;
		$user_info=array();
		if($user_login_status==LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;
		    $user_info['uid'] = $user_data['id']?$user_data['id']:0;
		    $user_info['user_name'] = $user_data['user_name']?$user_data['user_name']:0;
		    $user_info['user_money_format'] = format_price($user_data['money'])?format_price($user_data['money']):"";//用户金额
		    $user_info['user_score'] = intval($user_data['score']);
		    $user_info['user_score_format'] = format_score($user_data['score']);
		    $user_info['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"))?get_abs_img_root(get_muser_avatar($user_data['id'],"big")):"";
		    $user_info['user_group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id=".$user_data['group_id']);
		    /*会员经验等级*/
		    $user_info['user_login_score'] = app_conf("USER_LOGIN_SCORE");
		    $user_info['user_login_keep_score'] = app_conf("USER_LOGIN_KEEP_SCORE");
		    
		    $t_begin_time = to_timespan(to_date(NOW_TIME,"Y-m-d"));  //今天开始
		    $t_end_time = to_timespan(to_date(NOW_TIME,"Y-m-d"))+ (24*3600 - 1);  //今天结束
		    $t_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_data['id']." and sign_date between ".$t_begin_time." and ".$t_end_time);
  
		    if($t_sign_data){
		        $user_info['is_user_login_today'] = 1;//今天是否已经签到
		    }else{
		        $user_info['is_user_login_today'] = 0;//今天是否已经签到
		    }
		    
		    $total_signcount = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_data['id']);    
		    $user_info['user_keep_login_days'] = $total_signcount;//用户连续签到天数
		    

		    $user_info['user_login_text']='';
		    if($user_info['user_login_score'] > 0){
    		    if($user_info['is_user_login_today']==1){
    		        if($user_info['user_keep_login_days'] >= 3){
    		            $user_info['user_login_text']='明天可领取'. $user_info['user_login_score'].'积分';
    		        }else{
    		            $user_info['user_keep_login_days_min'] = 3 - $user_info['user_keep_login_days'];//用户距连续签到三数，还剩下几天   		            		 
    		            $user_info['user_login_text']='再签到'.$user_info['user_keep_login_days_min'].'天即可领取'.$user_info['user_login_keep_score'].'积分';
    		        }
    		    }else{
    		        $user_info['user_login_text']='已连续签到'.$user_info['user_keep_login_days'].'天';
    		    }
		    }
		    $user_info['level_name'] = $user_data['level_name'];
		    $user_info['level_id'] = $user_data['level_id'];
		    $user_info['total_score'] = $user_data['total_score'];
		    $user_info['point'] = $user_data['point'];
		}
		$root['user_info']=$user_info;
	
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$ext_condition = " d.buy_type = 1 and d.is_shop = 1 ";//buy_type=1为积分商品
		
		$order = "d.buy_count desc";
		$condition_param='';
		require_once(APP_ROOT_PATH."system/model/deal.php");
		$deal_result  = get_goods_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),$condition_param,"",$ext_condition,$order);
		//print_r($deal_result);exit;
		$list = $deal_result['list'];
		$count= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d where ".$deal_result['condition']);
		
		$page_total = ceil($count/$page_size);

		$goodses = array();
		foreach($list as $k=>$v)
		{
			$goodses[$k] = format_deal_list_item($v);
		}
        
	
        $root['page_title']="积分商城";
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		$root['item'] = $goodses?$goodses:array();

		$bcate_list = $this->scoresCate();
		//格式化bcate_list的url
		foreach($bcate_list as $k=>$v)
		{
		    $tmp_url_param['cate_id']=$v['id'];
		    $bcate_list[$k]["url"] = wap_url("index","scores",$tmp_url_param);
		}

		$root['bcate_list'] = $bcate_list?$bcate_list:array();
		
		
		return output($root);
	}
	
	
	/**
	 * wap版积分商城首页-大家都在竞接口
	 * 输入：
	 *
	 */
	
	
	public function load_index_list_data()
	{
	    $root = array();
	    $page = intval($GLOBALS['request']['page']); //分页
	    $page=$page==0?1:$page;
	    $page_size = PAGE_SIZE;
	    $limit = (($page-1)*$page_size).",".$page_size;
	    
	    $ext_condition = " d.buy_type = 1 and d.is_shop = 1 ";//buy_type=1为积分商品    
	    $order = "";
	    $condition_param='';
	    require_once(APP_ROOT_PATH."system/model/deal.php");
	    $deal_result  = get_goods_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),$condition_param,"",$ext_condition,$order);
	    
	    $list = $deal_result['list'];
	    $count= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d where ".$deal_result['condition']);
	    
	    $page_total = ceil($count/$page_size);
	    
	    $goodses = array();
	    foreach($list as $k=>$v)
	    {
	        $goodses[$k] = format_deal_list_item($v);
	    }
	    $root['page_total'] = $page_total;
	    $root['item'] = $goodses?$goodses:array();
	    return output($root);
	    
	}
	
	
	public function signin()
	{
	    $root = array();
	    require_once(APP_ROOT_PATH."system/model/user.php");
	    $user_id = $GLOBALS['user_info']['id'];
	    $result = signin($user_id);
	    
        $t_begin_time = to_timespan(to_date(NOW_TIME,"Y-m-d"));  //今天开始
        $t_end_time = to_timespan(to_date(NOW_TIME,"Y-m-d"))+ (24*3600 - 1);  //今天结束
        $t_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$t_begin_time." and ".$t_end_time);
        
        if($t_sign_data){
            $user_info['is_user_login_today'] = 1;//今天是否已经签到
        }else{
            $user_info['is_user_login_today'] = 0;//今天是否已经签到
        }
        
    
	    $total_signcount = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);
	    $user_info['user_keep_login_days'] = $total_signcount;//用户连续签到天数
	    $user_info['user_login_score'] = app_conf("USER_LOGIN_SCORE");
	    $user_info['user_login_keep_score'] = app_conf("USER_LOGIN_KEEP_SCORE");
	    $user_info['user_login_text']='';
	    if($user_info['user_login_score'] > 0){
	        if($user_info['is_user_login_today']==1){
	            if($user_info['user_keep_login_days'] >= 3){
	                $user_info['user_login_text']='明天可领取'. $user_info['user_login_score'].'积分';
	            }else{
	                $user_info['user_keep_login_days_min'] = 3 - $user_info['user_keep_login_days'];//用户距连续签到三数，还剩下几天
	                $user_info['user_login_text']='再签到'.$user_info['user_keep_login_days_min'].'天即可领取'.$user_info['user_login_keep_score'].'积分';
	            }
	        }else{
	            $user_info['user_login_text']='已连续签到'.$user_info['user_keep_login_days'].'天';
	        }
	        $result['sign_info']= $user_info['user_login_text'];
	    }

	    if($result['status']==1){
	        $result['info']='已签到';
	        $result['score'] = intval($GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$user_id));
	    }
	    $root['result'] = $result;
	    return output($root);
	     
	}


	public function scoresCate()
	{
		$indexs_list = $GLOBALS['cache']->get('WAP_SCORES_INDEX_CATE');
		if($indexs_list===false)
		{
			$time = NOW_TIME;
			
			$condition = 'select shop_cate_id , count(*) as shop_cate_count from '.DB_PREFIX."deal where";
			
			$condition .= ' is_effect = 1 and is_delete = 0 and buy_type=1 and is_shop=1 and (';
			
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
				$indexs_list[$k]['iconfont'] = $v['m_iconfont'];//图标名 http://fontawesome.io/icon/bars/
				$indexs_list[$k]['iconcolor'] = $v['m_iconcolor'];//颜色
				$indexs_list[$k]['iconbgcolor'] = $v['m_iconbgcolor'];//背景颜色
				$indexs_list[$k]['url'] = wap_url("index","goods",array("cate_id"=>$v['id']));
			}
				
				
			$GLOBALS['cache']->set("WAP_SCORES_INDEX_CATE",$indexs_list,300);
		}

		return $indexs_list;
	}
}
?>