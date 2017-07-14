<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


/**
* 会员消息接口
*/
class uc_msgApiModule extends MainBaseApiModule
{
	
	/**
	 * 消息中心
	 * @return array 
	 * [delivery|notify|account|confirm] => array(
	 * 		'id' => 消息id,
	 * 		'content' => 消息内容,
	 * 		'create_time' => 消息添加时间
	 * )
	 */
	public function index()
	{
		$data = array();
		global_run();
		$user_id = $GLOBALS['user_info']['id'];
		$user_login_status = check_login();
		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$user = $GLOBALS['user_info'];

			// 获取每个分类下未读的最后一条信息
			//$sql = 'SELECT m1.* FROM '.DB_PREFIX.'msg_box AS m1 INNER JOIN (SELECT m2.type, m2.id FROM '.DB_PREFIX.'msg_box AS m2 LEFT JOIN '.DB_PREFIX.'msg_box AS m3 ON m2.type=m3.type AND m2.id<=m3.id GROUP BY m2.type,m2.id HAVING COUNT(m3.id) <= 1) AS m4 ON m1.type=m4.type AND m1.id=m4.id where m1.user_id='.$user['id'].' ORDER BY m1.type,m1.id DESC';
			$sql="select msg.* from (select max(id) as max_id from ".DB_PREFIX."msg_box where user_id=".$user['id']." GROUP BY type) as mb left join ".DB_PREFIX."msg_box as msg on msg.id=mb.max_id where user_id=".$user['id'];
			$result = $GLOBALS['db']->getAll($sql);
			
			$typedata = array();
			require_once(APP_ROOT_PATH.'system/msg/msg.php');
			$msgClass = new Msg;
			if (is_array($result) && count($result) > 0) {
				foreach ($result as $item) {
				    $unread=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where user_id=".$user['id']." and type='".$item['type']."' and is_read=0");
				    $item['unread']=$unread;
				    $title=$msgClass->load_msg($item);
				    $item['title'] = $title['title'];
					$item['create_time'] = to_date($item['create_time'], 'Y-m-d H:i');
					
					if($GLOBALS['request']['from']=="app"){
    					$item['content']=str_replace("&lt;","<",$item['content']);
    					$item['content']=str_replace("&gt;",">",$item['content']);
					}	
					
					// $item['data'] = empty($item['data']) ? '' : @unserialize($item['data']);
					$typedata[$item['type']] = $item;
				}
			}
			
			$data['data'] = $typedata;
		}
        
		$data['page_title'] = "消息中心";
        $data['user_login_status'] = $user_login_status;
        
        return output($data);
	}
    

	/**
	 * 分类消息列表
	 * @return array 
	 */
	public function cate()
	{
		$data = array();

        // 用户登录状态判断
        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
	        $user = $GLOBALS['user_info'];
	        fanwe_require(APP_ROOT_PATH."system/model/user_center.php");

	        require_once(APP_ROOT_PATH.'system/msg/msg.php');
	        $msgClass = new Msg;

	        //分页
	        $page = intval($GLOBALS['request']['page']);
	        $page = $page == 0 ? 1 : $page;

	        $page_size = PAGE_SIZE;
	        $limit = (($page-1)*$page_size).",".$page_size;

	        $msgType = strim($GLOBALS['request']['msgType']);
	        $msgType = $msgClass->filterType($msgType);
	        list($list, $count) = getUserMsg($limit, $user['id'], $msgType);
	        $list_new = array();
	        foreach ($list as $item) {
	        	$item = $msgClass->load_msg($item); 
				// wap 和 app
				if(APP_INDEX=='app'){
				    $item['app']=$msgClass->getAppType($item['ctl']['app']);
				    $param = array();
				    if (!empty($item['data_id'])) {
				        $param['data_id'] = $item['data_id'];
				    }
				    $item['wap_link']=SITE_DOMAIN.wap_url('index', $item['ctl']['wap'], $param);
				    
    	        	if ($item['ctl']['wap']=="uc_account"){
    	        	    $item['link']="javascript:App.app_detail(201,0);";
    	        	}
    	        	else if($item['ctl']['wap']=="user_center"){
    	        	    $item['link']="javascript:App.app_detail(107,0);";
    	        	}
    	        	elseif($item['ctl']['wap']) {
    					$item['link'] = wap_url('index', $item['ctl']['wap'],$param);
    				}else{
    				    $item['link']="javascript:App.app_detail(1,0);";
				    }
				    
				    if($GLOBALS['request']['from'] == 'app'){
				        $item['content']=str_replace("&lt;","<",$item['content']);
    					$item['content']=str_replace("&gt;",">",$item['content']);
				    }
				    
				}else{
				    if ($item['ctl']['wap']) {
				        $param = array();
				        if (!empty($item['data_id'])) {
				            $param['data_id'] = $item['data_id'];
				        }
				        $item['link'] = wap_url('index', $item['ctl']['wap'], $param);
				    }
				}
				
                
	        	unset($item['ctl']);
	        	$item['create_time'] = to_date($item['create_time'], 'Y-m-d H:i');
	        	$list_new[] = $item;
	        }
	        
	        $data['data'] = $list_new;

	        // 将分页内的数据未读数据设置为已读
	        $ids = array();
	        foreach ($list as $value) {
	        	if ($value['is_read'] == 0) {
	        		$ids[] = $value['id'];
	        	}
	        	if (!empty($ids)) {
	        		$this->readStatus($ids);
	        	}
	        }
	        
	        //分页
	        $page_total = ceil($count/$page_size);
	        $data['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);

	        $data['page_title'] = $msgClass->load_title($msgType);
	        
	        if($msgType=="account"){
	            $data['page_title'] = "资产消息";
	        }
	        else if($msgType=="notify"){
	            $data['page_title'] = "通知消息";
	        }
	        else if($msgType=="delivery"){
	            $data['page_title'] = "物流消息";
	        }
	        else if($msgType=="confirm"){
	            $data['page_title'] = "验证消息";
	        }
        }
        
        $data['user_login_status'] = $user_login_status;
        
        return output($data);
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
		$sql = 'UPDATE '.DB_PREFIX.'msg_box SET is_read='.$status.' WHERE id IN ('.$msg_ids.')';
	    $GLOBALS['db']->query($sql);
	}


	/**
	 * 消息删除
	 * 
	 * @return mixed 
	 */
	public function delete()
	{
		$user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$id = intval($GLOBALS['request']['id']);
			$sql = 'UPDATE '.DB_PREFIX.'msg_box SET is_delete=1 WHERE id='.$id;
			$delete = $GLOBALS['db']->query($sql);
			if ($delete) {
				$status = 1;
				$info = '';
			} else {
				$status = 0;
				$info = '删除失败,请重试';
			}
			$data['status'] = $status;
			$data['info'] = $status;
		}
		$data['user_login_status'] = $user_login_status;
		return output($data);
	}
	
	public function countNotRead()
	{
		if($GLOBALS['request']['m_longitude']!=0&&$GLOBALS['request']['m_latitude']!=0&&APP_INDEX=="app"){
			$current_geo['xpoint']=$GLOBALS['request']['m_longitude'];
			$current_geo['ypoint']=$GLOBALS['request']['m_latitude'];
			es_session::set("current_geo",$current_geo);
			//require_once(APP_ROOT_PATH."system/model/city.php");
			//City::locate_geo($GLOBALS['request']['m_longitude'],$GLOBALS['request']['m_latitude'],"BD09");
		}
		$user_login_status = check_login();

		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$user = $GLOBALS['user_info'];
			$sql = "select count(*) from ".DB_PREFIX."msg_box where user_id=".$user['id']." and is_read=0";
			// print_r($sql);exit;
			$unread=$GLOBALS['db']->getOne($sql);
			$data['count'] = $unread;
		}
		$data['user_login_status'] = $user_login_status;
		return output($data);
	}
}