<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_couponApiModule extends MainBaseApiModule
{
	
	/**
	 * 会员中心我的消费券
	 * 输入：
	 * tag:int 消费券的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * page [int] 分页所在的页数
	 * 
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
	 * tag:int 消费券的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * page_title:string 页面标题
	 * item:array 消费券数据列表
	 * [0] => Array
                (
                    [sub_name] => 泰宁大金湖 [string] 简短团购名
                    [name] => 仅售758元！价值838元的福建春秋国际旅行社提供的泰宁大金团人数 [string] 完整团购名
                    [number] => 1   [int] 消费券数量
                    [password] => 66353664  [string] 消费券密码
                    [end_time] => 2015-04-30    [string] 消费券过期时间
                    [confirm_time] => 2015-04-21    [string] 消费券使用时间
                    [deal_id] => 65 [int] 团购商品ID
                    [order_id] => 33    [int] 团购订单ID
                    [order_deal_id] => 87   [int] 团购订单商品ID
                    [supplier_id] => 31 [int] 团购商户ID
                    [couponSn] => 65930923 [string] 团购序列号
                    [less_time] => 2429473 [string] 即将到期时间
                    [dealIcon] => http://localhost/o2onew/public/attachment/201502/25/16/54ed84087507c_140x85.jpg [string] 团购商品缩略图 140x85
                    [spAddress] => 鼓楼区五一中路18号正大广场御景台1623 [string] 商户门店地址
                    [spTel] => 0591-88592106/88592109 [string] 商户门店电话
                    [spName] => 国际旅游社   [string]//商户门店名称
                    [qrcode] => http://localhost/o2onew/public/images/qrcode/c8/6cd2a7d7e724977bf18e835c4e573fc1.png  [string] 团购密码验证 二维码图片
                )
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 */
	public function index()
	{
		$root = array();		
		/*参数初始化*/
		$tag = intval($GLOBALS['request']['tag']);
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
        $user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;
		}
		else
		{
			$root['user_login_status'] = $user_login_status;		
			
			
			$ext_condition = '';
			$now = NOW_TIME;
			if($tag==1)//即将过期
			{
				$ext_condition = " and c.confirm_time = 0 and c.end_time > 0 and c.end_time > ".$now." and c.end_time - ".$now." < ".(72*3600);				
			}
			if($tag==2)//未使用
			{
				$ext_condition = " and c.is_valid = 1 and c.refund_status = 0 and c.confirm_time = 0 and (c.end_time = 0 or (c.end_time>0 and c.end_time > $now))";
			}
			if($tag==3)//已失效
			{
				$ext_condition = " and (c.is_valid = 2 or c.refund_status = 1 or (c.confirm_time <> 0 or (c.end_time < $now and c.end_time > 0)))";
			}
			
			$root['tag']=$tag;

           
			
			//分页
			$page = intval($GLOBALS['request']['page']);
			$page=$page==0?1:$page;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			
		    $sql = "select doi.id as did,doi.sub_name,doi.name,doi.number,c.sn,c.password,c.end_time,c.confirm_time,c.deal_id,c.order_id,c.order_deal_id,c.supplier_id from ".DB_PREFIX."deal_coupon as c left join ".
		        DB_PREFIX."deal_order_item as doi on doi.id = c.order_deal_id where c.is_valid > 0 and ".
		        " c.user_id = ".$user_id.$ext_condition." order by c.id desc limit ".$limit;
		    $sql_count = "select count(*) from ".DB_PREFIX."deal_coupon as c where c.is_valid > 0 and ".
		        " c.user_id = ".$user_id.$ext_condition;
			

			$list = $GLOBALS['db']->getAll($sql);
			$count = $GLOBALS['db']->getOne($sql_count);
	
			
			
			$page_total = ceil($count/$page_size);
			//end 分页

			//要返回的字段
			$data = array();
			foreach($list as $k=>$v)
			{
			    $temp_arr = array();
			    $temp_arr['sub_name'] = $v['sub_name'];
			    $temp_arr['name'] = $v['name'];
			    $temp_arr['number'] = $v['number'];
			    $temp_arr['password'] = $v['password'];
			    $temp_arr['end_time'] =  $v['end_time']>0?to_date($v['end_time'],"Y-m-d"):"永久"; //过期时间
			    $temp_arr['confirm_time'] = $v['confirm_time']>0?to_date($v['confirm_time'],"Y-m-d"):'';//验证使用时间
			    $temp_arr['deal_id'] = $v['deal_id'];
			    $temp_arr['order_id'] = $v['order_id'];
			    $temp_arr['order_deal_id'] = $v['order_deal_id'];
			    $temp_arr['supplier_id'] = $v['supplier_id'];
			    $temp_arr['couponSn'] = $v['sn'];
			    $temp_arr['less_time'] = $v['end_time']>0?$v['end_time']-NOW_TIME:"永久";
			    
				//商品信息
				$deal = array();
				$deal = load_auto_cache("deal",array("id"=>$v['deal_id']));
				
				$temp_arr['dealIcon'] = get_abs_img_root(get_spec_image($deal['icon'],140,85,1));
				
				//获取商户数据
				$supplier_id = intval($GLOBALS['db']->getOne("select supplier_id from ".DB_PREFIX."deal where id = ".$v['deal_id']));
				$supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id." and is_main = 1");
				
				$temp_arr['spName'] = $supplier_info['name']?$supplier_info['name']:"";	
				$temp_arr['spAddress'] = $supplier_info['address']?$supplier_info['address']:"";
				$temp_arr['spTel'] = $supplier_info['tel']?$supplier_info['tel']:"";
				
				$temp_arr['qrcode'] =  get_abs_img_root(gen_qrcode($v['password']));// str_replace('sjmapi', '', get_domain().gen_qrcode($v['password']));
			     
				$data[] = $temp_arr;
			}
			
			$root['item'] = $data;
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		}	

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="我的消费券";
		return output($root);
	}	
	

	/**
	 * wap端 消费券方法重写
	 * @return array 
	 */
	public function wap_index()
	{
		$root = array();		
		/*参数初始化*/
		$coupon_status = intval($GLOBALS['request']['coupon_status']);
		$order_id = intval($GLOBALS['request']['order_id']);
		
		$root['coupon_status']=$coupon_status;
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
        $user_login_status = check_login();
		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			//$ext_condition = '';
			$now = NOW_TIME;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page= $page ?: 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			
			//未读条数统计
			$ids = array();
			
            if(!$coupon_status){
        		//团购消费券的信息
                if($order_id){
        		    $tuan_sql = 'SELECT dc.deal_id,dc.user_id,d.name,dc.end_time,dc.begin_time,d.img,dc.order_id FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.user_id='.$user_id.' AND dc.is_delete=0 and d.shop_cate_id=0 and dc.is_valid<>0 and dc.order_id='.$order_id.' GROUP BY dc.order_id,dc.deal_id order by dc.id desc LIMIT '.$limit;
                }else {
                    $tuan_sql = 'SELECT dc.deal_id,dc.user_id,d.name,dc.end_time,dc.begin_time,d.img,dc.order_id FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.user_id='.$user_id.' AND dc.is_delete=0 and d.shop_cate_id=0 and dc.is_valid<>0 GROUP BY dc.order_id,dc.deal_id order by dc.id desc LIMIT '.$limit;
                }
        
        		//$tuan_sqlCount = 'SELECT count(*) FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id INNER JOIN '.DB_PREFIX.'supplier AS s ON dc.supplier_id=s.id WHERE dc.user_id='.$user_id.$ext_condition.' AND dc.is_delete=0 and d.shop_cate_id=0 and dc.is_valid=1 ';
        
        		$tuan_list = $GLOBALS['db']->getAll($tuan_sql);
        		if (count($tuan_list)){
        			$tuan_new=array();
        			foreach ($tuan_list as $t => $v){
        			    $coupon=$GLOBALS['db']->getAll("select dc.id,dc.password,dc.end_time,dc.confirm_time,dc.is_new,dc.begin_time,dc.end_time,dc.is_valid,dc.refund_status from ".DB_PREFIX."deal_coupon as dc INNER JOIN ".DB_PREFIX."deal AS d ON dc.deal_id=d.id where deal_id=".$v['deal_id']." and order_id=".$v['order_id']);
        			    foreach ($coupon as $tt => $vv){
        			        if ($vv['is_new'] == 0) {
        			            $ids[] = $vv['id'];
        			        }
        			        $coupon[$tt]['qrcode'] = get_abs_img_root(gen_qrcode($vv['password']));
        			        if($vv['confirm_time']){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="已使用";
        			        }else if($vv['refund_status']==1){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="申请退款中";
        			        }else if($vv['refund_status']==2){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="退款被禁用";
        			        }else if($vv['end_time']<$now && $vv['end_time']!=0){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="已过期";
        			        }else {
        			            $coupon[$tt]['status']=1;
        			            $coupon[$tt]['info']="可使用";
        			        }
        			    }
        			    $tuan_list[$t]['img']=get_abs_img_root(get_spec_image($v['img'],280,280,1));
        			    $tuan_list[$t]['coupon_end_time']=$v['begin_time']?to_date($v['begin_time'],"Y-m-d"):"";
        			    if( $tuan_list[$t]['coupon_end_time'] ){
        			        $tuan_list[$t]['coupon_end_time'].=$v['end_time']>0?"至".to_date($v['end_time'],"Y-m-d"):"起可用（永久）";
        			    }else {
        			        $tuan_list[$t]['coupon_end_time'].=$v['end_time']>0?"至".to_date($v['end_time'],"Y-m-d"):"永久";
        			    }
        			    
        		        $tuan_list[$t]['count']=count($coupon);
        			    $tuan_list[$t]['coupon']=$coupon;
        			}
        			
        			if($order_id){
        			    $tuan_count = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon as dc INNER JOIN ".DB_PREFIX."deal AS d ON dc.deal_id=d.id where dc.user_id=".$user_id." AND dc.is_delete=0 and d.shop_cate_id=0 and dc.is_valid=1 and dc.order_id=".$order_id." GROUP BY dc.order_id,deal_id");
        			}else{
        			    $tuan_count = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon as dc INNER JOIN ".DB_PREFIX."deal AS d ON dc.deal_id=d.id where dc.user_id=".$user_id." AND dc.is_delete=0 and d.shop_cate_id=0 and dc.is_valid=1 GROUP BY dc.order_id,deal_id");
        			}
        			$tuan_count=count($tuan_count);
        			$page_total = ceil($tuan_count/$page_size);
        			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$tuan_count);
        		}
            }
			
            if($coupon_status==1){
    			//自提消费券的获取
    			if($order_id){
    			    $pick_sql = 'SELECT dc.supplier_id ,dc.order_id,dc.deal_id,dc.is_valid FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.user_id='.$user_id.' AND dc.is_delete=0 and d.cate_id=0 and dc.is_valid<>0 and dc.order_id='.$order_id.' GROUP BY dc.supplier_id,dc.order_id order by dc.id desc LIMIT '.$limit;
    			}else {
    			    $pick_sql = 'SELECT dc.supplier_id ,dc.order_id,dc.deal_id,dc.is_valid FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.user_id='.$user_id.' AND dc.is_delete=0 and d.cate_id=0 and dc.is_valid<>0 GROUP BY dc.supplier_id,dc.order_id order by dc.id desc LIMIT '.$limit;
    			}
    			$pick_list = $GLOBALS['db']->getAll($pick_sql);
    			
    			if (count($pick_list)){
    			    foreach ($pick_list as $t => $v){
    			        if($v['supplier_id']==0){
    			            $pick_list[$t]['supplier_name']="平台自营";
    			        }else{
        			        //$location_name=$GLOBALS['db']->getAll("select l.name as location_name from ".DB_PREFIX."deal_location_link as dl LEFT JOIN ".DB_PREFIX."supplier_location as l on dl.location_id=l.id where dl.deal_id=".$v['deal_id']." order by dl.location_id desc");
        			        //if($location_name){
        			            //$pick_list[$t]['supplier_name']=$location_name[0]['location_name'];
        			        //}else{
        			            $supplier_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$v['supplier_id']);
        			            $pick_list[$t]['supplier_name']=$supplier_name;
        			        //}
    			        }
    		            $order_sn=$GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id=".$v['order_id']);
    			        $number=$GLOBALS['db']->getOne("select sum(number) from ".DB_PREFIX."deal_order_item where order_id=".$v['order_id']." and supplier_id=".$v['supplier_id']);
    			        
    			        $pick_list[$t]['order_sn']=$order_sn;
    			        $pick_list[$t]['all_number']=$number;
    			        $pick_list[$t]['end_time']=$v['end_time']>0?to_date($v['end_time'],"Y-m-d"):1;
        			    $coupon=$GLOBALS['db']->getAll("select id,password,end_time,confirm_time,is_new,is_valid,refund_status from ".DB_PREFIX."deal_coupon where supplier_id=".$v['supplier_id']." and order_id=".$v['order_id']);
        			    foreach ($coupon as $tt => $vv){
        			        if ($vv['is_new'] == 0) {
        			            $ids[] = $vv['id'];
        			        }
        			        $coupon[$tt]['qrcode'] = get_abs_img_root(gen_qrcode($vv['password']));
        			        if($vv['confirm_time']){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="已使用";
        			        }else if($vv['refund_status']==1){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="申请退款中";
        			        }else if($vv['refund_status']==2){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="退款被禁用";
        			        }else if($vv['end_time']<$now && $vv['end_time']!=0){
        			            $coupon[$tt]['status']=0;
        			            $coupon[$tt]['info']="已过期";
        			        }else {
        			            $coupon[$tt]['status']=1;
        			            $coupon[$tt]['info']="可用";
        			        }
        			    }
        			    $pick_list[$t]['count']=count($coupon);
        			    $pick_list[$t]['coupon']=$coupon;
    			    }
    			    if($order_id){
    			        $pick_count=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_coupon AS dc INNER JOIN ".DB_PREFIX."deal AS d ON dc.deal_id=d.id WHERE dc.user_id=".$user_id." AND dc.is_delete=0 and d.cate_id=0 and dc.is_valid=1 and dc.order_id=".$order_id." GROUP BY dc.supplier_id,dc.order_id");
    			    }else{
    			        $pick_count=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_coupon AS dc INNER JOIN ".DB_PREFIX."deal AS d ON dc.deal_id=d.id WHERE dc.user_id=".$user_id." AND dc.is_delete=0 and d.cate_id=0 and dc.is_valid=1 GROUP BY dc.supplier_id,dc.order_id");
    			    }
    			    $pick_count=count($pick_count);
    			    $page_total = ceil($pick_count/$page_size);
    			    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$pick_count);
    			
    			}
            }

            //驿站取货码
            if(IS_OPEN_DISTRIBUTION==1 and $coupon_status==2){
                if($order_id){
                    $sql="select dc.*,do.order_sn as order_sn,dist.name as dist_name from ".DB_PREFIX."distribution_coupon as dc left join ".DB_PREFIX."deal_order as do on do.id=dc.order_id left join ".DB_PREFIX."distribution as dist on dist.id=dc.distribution_id where dc.user_id=".$user_id." and dc.is_delete=0 and dc.order_id=".$order_id."  GROUP BY dc.order_id order by dc.id desc LIMIT ".$limit;
                    $count_sql="select count(dc.*) from ".DB_PREFIX."distribution_coupon as dc left join ".DB_PREFIX."deal_order as do on do.id=dc.order_id left join ".DB_PREFIX."distribution as dist on dist.id=dc.distribution_id where user_id=".$user_id." and dc.is_delete=0 and dc.order_id=".$order_id." GROUP BY dc.order_id";
                }
                else {
                    $sql="select dc.*,do.order_sn as order_sn,dist.name as dist_name from ".DB_PREFIX."distribution_coupon as dc left join ".DB_PREFIX."deal_order as do on do.id=dc.order_id left join ".DB_PREFIX."distribution as dist on dist.id=dc.distribution_id where dc.user_id=".$user_id." and dc.is_delete=0 GROUP BY dc.order_id order by dc.id desc LIMIT ".$limit;
                    $count_sql="select count(dc.*) from ".DB_PREFIX."distribution_coupon as dc left join ".DB_PREFIX."deal_order as do on do.id=dc.order_id left join ".DB_PREFIX."distribution as dist on dist.id=dc.distribution_id where dc.user_id=".$user_id." and dc.is_delete=0 GROUP BY dc.order_id";
                }    
                
                $dist_item=$GLOBALS['db']->getAll($sql);
                
                if($dist_item){
                    foreach ($dist_item as $tt => $vv){
                        $dist_item[$tt]['number']=$GLOBALS['db']->getOne("select sum(doi.number) from ".DB_PREFIX."deal_order_item as doi where doi.refund_status<>2 and doi.order_id=".$vv['order_id']);
                        $dist_item[$tt]['qrcode'] = get_abs_img_root(gen_qrcode($vv['sn']));
                        if($vv['confirm_time']>0){
    			            $dist_item[$tt]['status']=0;
    			            $dist_item[$tt]['info']="已使用";
    			        }else if(!$dist_item[$tt]['number']){
    			            $dist_item[$tt]['status']=0;
    			            $dist_item[$tt]['info']="退款被禁用";
    			        }else {
    			            $dist_item[$tt]['status']=1;
    			            $dist_item[$tt]['info']="可用";
    			        }
    			        if ($vv['is_read'] == 0) {
    			            $ids[] = $vv['id'];
    			        }
                    }
                    $count=$GLOBALS['db']->getOne($count_sql);
                    $page_total = ceil($count/$page_size);
                    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
                }
            }
            
			//把未读的变为已读
			if (!empty($ids)) {
			    $this->readStatus($ids);
			}
			
			$root['is_open_distribution']=IS_OPEN_DISTRIBUTION;
			$root['dist_item']=$dist_item?$dist_item:array();
			$root['tuan_item'] = $tuan_list?$tuan_list:array();
			$root['pick_item'] = $pick_list?$pick_list:array();
		
		}
		$root['user_login_status'] = $user_login_status;

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']=app_conf("COUPON_NAME");
		return output($root);
	}

	/**
	 * 优惠券详情列表
	 * @return array 
	 */
	public function wap_view()
	{
		$root = array();		
		/*参数初始化*/
		$tag = intval($GLOBALS['request']['tag']);
		$supplier_id = intval($GLOBALS['request']['sp_id']);
		$deal_id = intval($GLOBALS['request']['deal_id']);

		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
        $user_login_status = check_login();
		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$ext_condition = '';
			$now = NOW_TIME;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page= $page ?: 1;
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;

			// 消费券的信息
			$sql = 'SELECT dc.id,dc.sn, dc.is_new, dc.password, dc.end_time, dc.deal_id, dc.is_valid, dc.refund_status, dc.confirm_time, d.sub_name, d.icon FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.deal_id='.$deal_id.' AND dc.user_id='.$user_id.$ext_condition.' AND dc.is_delete=0 order by dc.id desc LIMIT '.$limit;

			$sqlCount = 'SELECT count(*) FROM '.DB_PREFIX.'deal_coupon AS dc INNER JOIN '.DB_PREFIX.'deal AS d ON dc.deal_id=d.id WHERE dc.user_id='.$user_id.$ext_condition.' AND dc.is_delete=0';

			$list = $GLOBALS['db']->getAll($sql);
			$count = 0;
			if (count($list)) {
				$counts = $GLOBALS['db']->getAll($sqlCount);
				$count = count($counts);
				$format_list = array();
				$not_read_ids = array();
				foreach ($list as $item) {
					$item['valid'] = $item['is_valid'] == 1 && $item['refund_status'] == 0 && $item['confirm_time'] == 0 && ($item['end_time'] == 0 || $item['end_time'] > $NOW_TIME);
					$item['end_time'] = $item['end_time'] > 0 ? to_date($item['end_time'], 'Y-m-d') : '无限时';

					$item['qrcode'] =  get_abs_img_root(gen_qrcode("tuan".$v['password']));

					if (empty($root['title'])) {
						$title = array(
							'deal_id' => $item['deal_id'],
							'deal_name' => $item['sub_name'],
							'icon' => $item['icon'],
						);
						$root['title'] = $title;
					}
					$format_list[] = $item;
					if ($item['is_new'] == 0 && $item['is_valid'] == 1 && $item['delete'] == 0) {
						$not_read_ids[] = $item['id'];
					}
				}
				if (!empty($not_read_ids)) {
					$where = 'id in ('.implode(',', $not_read_ids).')';
					$GLOBALS['db']->autoExecute(DB_PREFIX.'deal_coupon', array('is_new' => 1), 'UPDATE', $where);
				}
			}

			$root['item'] = $format_list;

			// 分页
			$page_total = ceil($count/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		}

		$root['user_login_status'] = $user_login_status;

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="消费券详情";
		return output($root);
	}
	
	/**
	 * 更改指定消息的读状态
	 * @param  int|array  $msg_id
	 * @param  boolean $to_readed true:变为已读  false:变为未读
	 * @return
	 */
	public function readStatus($msg_id, $to_readed = true)
	{
	    
	    if (is_array($msg_id) && count($msg_id) > 0) {
	        $msg_ids = implode(',', $msg_id);
	    } elseif (is_string($msg_id)) {
	        $msg_ids = $msg_id;
	    } else {
	        return;
	    }
	    if ($to_readed) {
	        $status = 1;
	    } else {
	        $status = 0;
	    }
	    $sql = 'UPDATE '.DB_PREFIX.'deal_coupon SET is_new='.$status.' WHERE id IN ('.$msg_ids.')';

	    $GLOBALS['db']->query($sql);
	}
}
?>