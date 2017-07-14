<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_eventApiModule extends MainBaseApiModule
{
	
	/**
	 * 会员中心我的活动
	 * 输入：
	 * tag:int 活动的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * page [int] 分页所在的页数
	 * 
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 * tag:int 活动的状态(1 即将过期 /2 未使用 /3 已失效) 不传默认全部
	 * item:array 
	 * [0] => Array
                (
                    [id]=>1 [int] 活动ID
                    [name] => 玛格利塔新店开业试吃    [string] 活动标题
                    [event_sn] => 284220    [string]    活动SN
                    [event_end_time] => 2021-02-26  [string] 活动结束时间
                    [confirm_time] => 2021-02-12  [string]  活动验证时间
                    [icon] => http://localhost/o2onew/public/attachment/201502/26/11/54ee9942024d3_280x170.jpg [string] 活动缩略图  140X85
                    [qrcode] => http://localhost/o2onew/public/images/qrcode/ce/c0c8984b1472c7156c6b34a7a3e390c4.png [string] 活动SN  二维码
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
				$ext_condition = " and confirm_time = 0 and event_end_time > 0 and event_end_time > ".$now." and event_end_time - ".$now." < ".(72*3600);				
			}
			if($tag==2)//未使用
			{
				$ext_condition = " and confirm_time = 0 and (event_end_time = 0 or (event_end_time>0 and event_end_time > $now))";
			}
			if($tag==3)//已失效
			{
				$ext_condition = " and (confirm_time <> 0 or (event_end_time < $now and event_end_time > 0))";
			}
			
			$root['tag']=$tag;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page=$page==0?1:$page;
				
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			

		    $sql = "select * from ".DB_PREFIX."event_submit  where  ".
		        " user_id = ".$user_id.$ext_condition." order by  create_time desc limit ".$limit;
		    $sql_count = "select count(*) from ".DB_PREFIX."event_submit  where  ".
		        " user_id = ".$user_id.$ext_condition;
		    
		    $list = $GLOBALS['db']->getAll($sql);

			$count = $GLOBALS['db']->getOne($sql_count);
	

			$page_total = ceil($count/$page_size);
			//end 分页

			//要返回的字段
			$data = array();
			foreach($list as $k=>$v)
			{
			    $event_item = array();
			    $event_item = load_auto_cache("event",array("id"=>$v['event_id']));
			    $temp_arr = array();
			    $temp_arr['id'] = $v['event_id'];
			    $temp_arr['name'] = $event_item['name'];
			    $temp_arr['event_sn'] = $v['sn'];
			    $temp_arr['event_end_time'] = $v['event_end_time']>0?to_date($v['event_end_time'],"Y-m-d"):"无限时"; //过期时间
			    $temp_arr['confirm_time'] = $v['confirm_time']>0?to_date($v['confirm_time'],"Y-m-d"):'';//验证使用时间
				$temp_arr['icon'] = get_abs_img_root(get_spec_image($event_item['icon'],140,85,1));
				$temp_arr['qrcode'] =  get_abs_img_root(gen_qrcode("event".$v['sn']));
			     
				$data[] = $temp_arr;
			}
			
			$root['item'] = $data;
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
		}	

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="我的活动";
		return output($root);
	}



	/**
	 * 活动列表方法重写
	 * @return mixed 
	 */
	public function wap_index()
	{
		$root = array();		
		/*参数初始化*/
		$tag = intval($GLOBALS['request']['tag']);
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			

		$user_login_status = check_login();
		if($user_login_status==LOGIN_STATUS_LOGINED) {

			//$ext_condition = '';
			$now = NOW_TIME;
			
			/* //未使用
			if($tag==2) {
				$ext_condition = " and es.confirm_time = 0 and (es.event_end_time = 0 or (es.event_end_time>0 and es.event_end_time > $now))";
			}
			//已失效
			if($tag==3) {
				$ext_condition = " and (es.confirm_time <> 0 or (es.event_end_time < $now and es.event_end_time > 0))";
			} */
			
			$root['tag']=$tag;

			//分页
			$page = intval($GLOBALS['request']['page']);
			$page=$page==0?1:$page;
				
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			
		    $sql = 'SELECT es.id,es.confirm_time,es.event_id,es.is_read,e.event_begin_time, e.event_end_time, es.sn, e.name AS eName, e.img, s.id AS sId, s.name AS spName FROM '.DB_PREFIX.'event_submit AS es INNER JOIN '.DB_PREFIX.'event AS e ON es.event_id=e.id INNER JOIN '.DB_PREFIX.'supplier AS s ON e.supplier_id = s.id WHERE es.user_id='.$user_id.' and es.sn is not null ORDER BY es.id desc LIMIT '.$limit;
		    $sqlCount = 'SELECT COUNT(*) FROM '.DB_PREFIX.'event_submit AS es INNER JOIN '.DB_PREFIX.'event AS e ON es.event_id=e.id INNER JOIN '.DB_PREFIX.'supplier AS s ON e.supplier_id = s.id WHERE es.user_id='.$user_id.' and es.sn is not null';
		    $list = $GLOBALS['db']->getAll($sql);
            
		    $count = 0;
		    $data = array();
		    $ids=array();
		    if (count($list)) {
		    	$count = $GLOBALS['db']->getOne($sqlCount);
		    	foreach ($list as $t => $item) {
		    	    if ($item['is_read'] == 0) {
		    	        $ids[] = $item['id'];
		    	    }
		    	    if($item['event_begin_time']!=0 && $item['event_begin_time']>$now){
		    	        $list[$t]['status']=0;
		    	        $list[$t]['info']="活动未开始";
		    	    }
		    	    else if($item['event_end_time']<$now && $item['event_end_time']!=0){
		    	        $list[$t]['status']=0;
		    	        $list[$t]['info']="活动已结束";
		    	    }
		    	    else if($item['confirm_time']>0){
		    	        $list[$t]['status']=0;
		    	        $list[$t]['info']="已使用";
		    	    }
		    	    else {
		    	        $list[$t]['status']=1;
		    	    }
		    	    $list[$t]['icon']=get_abs_img_root(get_spec_image($item['img'],280,280,1));
		    	    $list[$t]['qrcode'] =  get_abs_img_root(gen_qrcode("event".$item['sn']));
		    	    $list[$t]['event_end_time'] = $item['event_begin_time'] >0 ? to_date($item['event_begin_time'],"Y-m-d"):"";
		    	    if($item['event_begin_time']!=0){
		    	        $list[$t]['event_end_time'] .= $item['event_end_time'] > 0 ? "至".to_date($item['event_end_time'],"Y-m-d") : "起可用（永久）";
		    		}else{
		    		    $list[$t]['event_end_time'] = $item['event_end_time'] > 0 ? "至".to_date($item['event_end_time'],"Y-m-d") : "无限时";
		    		}
		    	}
		    }
		    $root['item'] = $list;
		    
		    //把未读的变为已读
		    if (!empty($ids)) {
		        $this->readStatus($ids);
		    }

			$page_total = ceil($count/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
			//end 分页		
		}

		$root['user_login_status'] = $user_login_status;

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title'] ="活动券";
		return output($root);
	}


	public function wap_view()
	{
		$root = array();		
		/*参数初始化*/
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		// $user_id  = intval($user['id']);			
		$data_id = $GLOBALS['request']['data_id'];

		$user_login_status = check_login();
		if($user_login_status == LOGIN_STATUS_LOGINED){

			$ext_condition = '';
			$now = NOW_TIME;

		    $sql = 'SELECT es.id, es.is_read, es.is_verify, es.event_end_time, es.confirm_time, es.sn, e.name AS eName, e.id AS eId, e.icon FROM '.DB_PREFIX.'event_submit AS es INNER JOIN '.DB_PREFIX.'event AS e ON es.event_id=e.id WHERE es.id='.$data_id;
		    
		    $event = $GLOBALS['db']->getRow($sql);
		    if ($event) {
		    	$event['event_end_time'] = $event['event_end_time']>0?to_date($event['event_end_time'],"Y-m-d"):"无限时";
		    	$event['icon'] = get_abs_img_root(get_spec_image($event['icon'],140,85,1));
				$event['qrcode'] =  get_abs_img_root(gen_qrcode("event".$event['sn']));
				$event['valid'] = $event['confirm_time'] > 0 ? false : true;
				if ($event['is_read'] == 0 && $event['is_verify'] == 1) {
					$GLOBALS['db']->autoExecute(DB_PREFIX.'event_submit', array('is_read' => 1), 'UPDATE', 'id='.$event['id']);
				}
		    }
			
			$root['item'] = $event;
		}
		$root['user_login_status'] = $user_login_status;	

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title'] = "活动详情";
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
	    $sql = 'UPDATE '.DB_PREFIX.'event_submit SET is_read='.$status.' WHERE id IN ('.$msg_ids.')';
	
	    $GLOBALS['db']->query($sql);
	}
	
}
?>