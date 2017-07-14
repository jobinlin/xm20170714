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
class dist_msgApiModule extends MainBaseApiModule
{

	/**
	 * 消息中心
	 * 输出：
	 * 		Array 
			    [dist_user_status] => 1
			    [item] => Array (
					
			    )
			    [page_title] => 消息中心
			    [ctl] => biz_test
			    [act] => index
			    [status] => 1
			    [info] => 
			    [city_name] => 福州
			    [return] => 1
			    [sess_id] => jj0vparnu9imivrcm6fba244b1
			    [ref_uid] => 
			)
	 * @return array 
	 */
	public function index()
	{
		/*初始化*/
			/*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];
	    /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }

        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : $page;

        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;

        $sql = 'SELECT * FROM '.DB_PREFIX.'distribution_msg_box WHERE is_delete = 0 AND distribution_id = '.$dist_info['id'].' ORDER BY create_time DESC LIMIT '.$limit;
        $list = $GLOBALS['db']->getAll($sql);
        if ($list) {
        	$sqlCount = 'SELECT count(id) FROM '.DB_PREFIX.'distribution_msg_box WHERE is_delete = 0 AND distribution_id = '.$dist_info['id'];
        	$count = $GLOBALS['db']->getOne($sqlCount);
        	$unread = array();
        	foreach ($list as &$item) {
        		$item['create_time'] = to_date($item['create_time']);
        		if ($item['data']) {
        			$dist_data = unserialize($item['data']);
        			$item['url'] = SITE_DOMAIN.wap_url("dist","order#view",$dist_data);
        		}
        		unset($item['data']);
        		if ($item['is_read'] == 0) {
        			$unread[] = $item['id'];
        		}
        	}
        	unset($item);
        }
        $root['item'] = $list;
        $page_total = ceil($count/$page_size);
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		$root['page_title'] = "消息中心";
 
        return output($root);
	}
    


	/**
	 * 消息删除 (暂无需求)
	 * 
	 * @return mixed 
	 */
	public function delete()
	{
		$user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
			$id = intval($GLOBALS['request']['id']);
			$sql = 'UPDATE '.DB_PREFIX.'biz_msg_box SET is_delete=1 WHERE supplier_id='.$GLOBALS['account_info']['supplier_id'].' AND id='.$id;
			$delete = $GLOBALS['db']->query($sql);
			if ($delete) {
				$status = 1;
				$info = '';
			} else {
				$status = 0;
				$info = '删除失败,请重试';
			}
			$root['status'] = $status;
			$root['info'] = $status;
		}
		$root['user_login_status'] = $user_login_status;
		return output($root);
	}
	
}