<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_youhuiApiModule extends MainBaseApiModule
{
	
	/**
	 * 会员中心我的消费券
	 * 输入：
	 * tag:int 优惠券的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * page [int] 分页所在的页数
	 * 
	 * 
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
	 * tag:int 优惠券的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * item:array 
	 * [0] => Array
                (
                    [id]=>1 [int] 优惠券ID 
                    [name] => 华莱士30元抵用券 [string] 优惠券名称
                    [youhui_sn] => 91723490 [string] 优惠券SN
                    [expire_time] => 2015-04-17 [string] 有效日期
                    [confirm_time] => 2015-04-2 [string] 使用时间
                    [icon] => http://localhost/o2onew/public/attachment/201502/26/11/54ee8fc5497f9_320x320.jpg [string] 优惠券ICON 140X85
                    [qrcode] => http://localhost/o2onew/public/images/qrcode/cc/f627508892a154946f4a7ce3a56d4110.png [string]
                )
	 * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
	 * page_title:string 页面标题
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
				$ext_condition = " and confirm_time = 0 and expire_time > 0 and expire_time > ".$now." and expire_time - ".$now." < ".(72*3600);				
			}
			if($tag==2)//未使用
			{
				$ext_condition = " and confirm_time = 0 and (expire_time = 0 or (expire_time>0 and expire_time > $now))";
			}
			if($tag==3)//已失效
			{
				$ext_condition = " and (confirm_time <> 0 or (expire_time < $now and expire_time > 0))";
			}
			
			$root['tag']=$tag;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page=$page==0?1:$page;
				
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			
            $sql = "select youhui_id,youhui_sn,total_fee,confirm_time,expire_time from ".DB_PREFIX."youhui_log  where  ".
			" user_id = ".$user_id.$ext_condition." order by  create_time desc limit ".$limit;
		    $sql_count = "select count(*) from ".DB_PREFIX."youhui_log  where  ".
				" user_id = ".$user_id.$ext_condition;
			
       
			$list = $GLOBALS['db']->getAll($sql);
			$count = $GLOBALS['db']->getOne($sql_count);
	
			
			
			$page_total = ceil($count/$page_size);
			//end 分页

			//要返回的字段
			$data = array();
			foreach($list as $k=>$v)
			{
			    $youhui_item = array();
			    $youhui_item = load_auto_cache("youhui",array("id"=>$v['youhui_id']));
			    $temp_arr = array();
			    $temp_arr['id'] = $youhui_item['id'];
			    $temp_arr['name'] = $youhui_item['name'];
			    $temp_arr['youhui_sn'] = $v['youhui_sn'];
			    $temp_arr['expire_time'] = $v['expire_time']>0?to_date($v['expire_time'],"Y-m-d"):"无限时"; //过期时间
			    $temp_arr['confirm_time'] = $v['confirm_time']>0?to_date($v['confirm_time'],"Y-m-d"):'';//验证使用时间
				$temp_arr['icon'] = get_abs_img_root(get_spec_image($youhui_item['icon'],140,85,1));
				$temp_arr['qrcode'] =  get_abs_img_root(gen_qrcode($v['youhui_sn']));
			     
				$data[] = $temp_arr;
			}
			
			$root['item'] = $data;
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		}	

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="我的优惠券";
		return output($root);
	}	
	
	/**
	 * 优惠券列表方法重写
	 * @return mixed 
	 */
	public function wap_index()
	{
		$root = array();		
		/*参数初始化*/
		//$tag = intval($GLOBALS['request']['tag']);
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			

		$user_login_status = check_login();
		if($user_login_status == LOGIN_STATUS_LOGINED){
	
			$ext_condition = '';
			$now = NOW_TIME;
			
			$type = intval($GLOBALS['request']['type']);

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page = $page ==0 ? 1 : $page;
				
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;

			if(!$type){
    		    $sql = 'SELECT yl.id,yl.youhui_id,yl.youhui_sn,yl.confirm_time,yl.expire_time,yl.create_time,y.name AS youhui_name,y.youhui_type,y.youhui_value,y.start_use_price,y.supplier_id,s.name as supplier_name,
    		        case when yl.confirm_time>0 then 1 when (yl.expire_time<'.NOW_TIME.' and yl.expire_time<>0) then 0 else 2 end order_status
    		        FROM '.DB_PREFIX.'youhui_log AS yl left join '.DB_PREFIX.'youhui as y ON yl.youhui_id = y.id left join '.DB_PREFIX.'supplier as s on y.supplier_id=s.id 
		            WHERE y.is_effect=1 and yl.user_id='.$user_id.' order by order_status desc,yl.id desc LIMIT '.$limit;
    		   
    		    $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_log AS yl INNER JOIN '.DB_PREFIX.'youhui as y ON yl.youhui_id = y.id INNER JOIN '.DB_PREFIX.'supplier AS s ON y.supplier_id=s.id WHERE y.is_effect=1 and  yl.user_id='.$user_id;
                
    			$list = $GLOBALS['db']->getAll($sql);
				//logger::write(print_r($GLOBALS['db']->getLastSql(),1));
    			if ($list) {		
    				foreach ($list as $t => $v) {
    				    if( $v['confirm_time']>0 ){
    				        $list[$t]['status']=2;
    				        $list[$t]['info']="已使用";
    				    }
    				    else if($v['expire_time']<$now && $v['expire_time']!=0){
    				        $list[$t]['status']=0;
    				        $list[$t]['info']="已过期";
    				    }
    				    else{
    				        $list[$t]['status']=1;
    				        $list[$t]['info']="待使用";
    				    }
    				    $list[$t]['type']=$v['youhui_type'];
    			        $list[$t]['value']=$v['youhui_value'];
    			        
    				    $list[$t]['qrcode'] = get_abs_img_root(gen_qrcode($v['youhui_sn']));
    					$list[$t]['expire_time'] = $v['expire_time'] > 0 ? to_date($v['expire_time'], 'Y-m-d H:i'):"永久";
    					$list[$t]['confirm_time'] = $v['confirm_time']>0?to_date($v['confirm_time'],"Y-m-d H:i"):0;//验证使用时间
    				    
    					/* if($v['supplier_id']){
    					    $location_info=$GLOBALS['db']->getAll("select sl.id,sl.name,sl.address,sl.tel from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."youhui_location_link as yl on yl.location_id=sl.id where yl.youhui_id=".$v['youhui_id']);
    					    $list[$t]['location_info']=$location_info;
    					} */
    				}
    			}
			}
			
			if($type==1){
			    $sql="select e.id,e.password,e.use_count,e.end_time,e.money,ey.name,ey.start_use_price,
			        case when e.use_count=1 then 1 when (e.end_time<".NOW_TIME." and e.end_time<>0) then 2 else 0 end order_status
			        from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as ey on ey.id=e.ecv_type_id where e.user_id=".$user_id." 
		            order by order_status, e.id desc limit ".$limit;
			    $sqlCount="select count(*) from ".DB_PREFIX."ecv where user_id=".$user_id;
			    
			    $list = $GLOBALS['db']->getAll($sql);
			    
			    if ($list) {
    			    foreach ($list as $t => $v){
    			        if( $v['use_count']>0 ){
    			            $list[$t]['status']=2;
    			            $list[$t]['info']="已使用";
    			        }
    			        else if($v['end_time']<$now && $v['end_time']!=0){
    			            $list[$t]['status']=0;
    			            $list[$t]['info']="已过期";
    			        }
    			        else{
    			            $list[$t]['status']=1;
    			            $list[$t]['info']="待使用";
    			        }
    			        
    			        $list[$t]['start_use_price']=round($v['start_use_price']);
    			        $list[$t]['money']=round($v['money']);
    			        $list[$t]['qrcode'] = get_abs_img_root(gen_qrcode('ecv'.$v['password']));
    			        $list[$t]['end_time'] = $v['end_time'] > 0 ? to_date($v['end_time'], 'Y-m-d H:i'):"永久";
    			    }
			    }
			}
			
			$root['item'] = $list?$list:array();

			//end 分页
			$counts = $GLOBALS['db']->getOne($sqlCount);
			$page_total = ceil($counts/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$counts);
		}
		$root['user_login_status'] = $user_login_status;

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="优惠券";
		return output($root);
	}
	
	
	/**
	 * 获取门店
	 * 输入：id 优惠券ID
	 *
	 *   */
	public function get_location(){
	    $root = array();
	    
	    $id = intval($GLOBALS['request']['id']);
	    
	    $location_info=$GLOBALS['db']->getAll("select sl.id,sl.name,sl.address,sl.tel from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."youhui_location_link as yl on yl.location_id=sl.id where yl.youhui_id=".$id);

	    if($location_info){
	        foreach ($location_info as  $t=>$v){
	            $location_info[$t]['jump']=wap_url("index","store",array("data_id"=>$v['id']));
	        }
	        
	        $root['location_info']=$location_info;
	        
	        return output($root);
	    }else {
	        $root['status']=0;
	        $root['info']="门店信息";
	        return output($root,0,"暂无门店信息");
	    }
	    
	    
	}

	/**
	 * 优惠券详情
	 * @return mixed 
	 */
	public function wap_view()
	{
		$root = array();		
		/*参数初始化*/
		$youhui_id = intval($GLOBALS['request']['data_id']); // 优惠ID

		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			

		$user_login_status = check_login();
		if($user_login_status == LOGIN_STATUS_LOGINED){
	
			$ext_condition = '';
			$now = NOW_TIME;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page=$page==0?1:$page;
				
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;

		    $sql = 'SELECT yl.id, yl.youhui_id,yl.youhui_sn,yl.total_fee,yl.confirm_time,yl.expire_time, yl.is_read, y.name AS yName, y.icon FROM '.DB_PREFIX.'youhui_log AS yl INNER JOIN '.DB_PREFIX.'youhui as y ON yl.youhui_id = y.id WHERE yl.user_id='.$user_id.' AND yl.youhui_id='.$youhui_id.' ORDER BY yl.id DESC LIMIT '.$limit;
		    $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'youhui_log AS yl INNER JOIN '.DB_PREFIX.'youhui as y ON yl.youhui_id = y.id WHERE yl.user_id='.$user_id.' AND yl.youhui_id='.$youhui_id;

			$list = $GLOBALS['db']->getAll($sql);
			$count = 0;
			$data = array();
			if (count($list)) {
				$counts = $GLOBALS['db']->getAll($sqlCount);
				$count = count($counts);
				$not_read_ids = array();
				foreach ($list as $item) {
					$item['qrcode'] = get_abs_img_root(gen_qrcode('youhui'.$item['youhui_sn']));
					$item['expire_time'] =  $item['expire_time'] > 0 ? to_date($item['expire_time'], 'Y-m-d') : '无限期';
					$item['valid'] = $item['confirm_time'] == 0 && ($item['expire_time'] == 0 || $item['expire_time'] > $now);

					if (empty($root['title'])) {
						$title = array(
							'id' => $item['youhui_id'],
							'name' => $item['yName'],
							'icon' => $item['icon'],
							
						);
						$root['title'] = $title;
					}
					$data[] = $item;
					// 修改成已读状态的数组
					if ($item['is_read'] == 0) {
						$not_read_ids[] = $item['id'];
					}
				}
				if (!empty($not_read_ids)) {
					$where = 'id in ('.implode(',', $not_read_ids).')';
					$GLOBALS['db']->autoExecute(DB_PREFIX.'youhui_log', array('is_read' => 1), 'UPDATE', $where);
				}
			}
			$root['item'] = $data;

			//end 分页	
			$page_total = ceil($count/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		}
		$root['user_login_status'] = $user_login_status;

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="优惠券详情";
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
	    $sql = 'UPDATE '.DB_PREFIX.'youhui_log SET is_read='.$status.' WHERE id IN ('.$msg_ids.')';
	
	    $GLOBALS['db']->query($sql);
	}
}
?>