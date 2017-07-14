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
class biz_msgApiModule extends MainBaseApiModule
{

	protected $title = array(
		1 => '订单待发货',
		// 2 => '订单已成交',
		3 => '订单评价',
		4 => '退款待处理',
		5 => '交易成功',
		6 => '提现申请已提交',
		7 => '提现成功',
		8 => '提现被驳回',

		// 外卖的消息标题
		21 => '新订单待接单',
		22 => '催单',
		23 => '确认收货'
	);
	
	/**
	 * 消息中心
	 * 输出：
	 * 		Array 
			    [biz_user_status] => 1
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
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        $sql="select msg.* from (select max(id) as max_id from ".DB_PREFIX."biz_msg_box where supplier_id=".$supplier_id." and is_delete = 0 GROUP BY type) as mb left join ".DB_PREFIX."biz_msg_box as msg on msg.id=mb.max_id where supplier_id=".$supplier_id;
		$list = $GLOBALS['db']->getAll($sql);
		
		$typeData = array();
        foreach ($list as $item) {
        	$item['create_time'] = to_date($item['create_time']);
        	
        	$typeData[$item['type']] = $item;
        }
        
        $root['item'] = $typeData;

		$root['page_title'] = "消息中心";
        
        return output($root);
	}
    

	/**
	 * 分类消息列表
	 * 输入:
	 * 		page: 分页
	 * 		cate: 分类
	 * @return array 
	 */
	public function cate()
	{
		/*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }

        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : $page;

        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;

        $cate = strim($GLOBALS['request']['cate']);
        $sql = 'SELECT * FROM '.DB_PREFIX.'biz_msg_box WHERE is_delete = 0 AND supplier_id = '.$supplier_id.' AND type ="'.$cate.'" ORDER BY create_time DESC LIMIT '.$limit;
        $list = $GLOBALS['db']->getAll($sql);
        if ($list) {
        	$sqlCount = 'SELECT count(id) FROM '.DB_PREFIX.'biz_msg_box WHERE is_delete = 0 AND supplier_id = '.$supplier_id.' AND type ="'.$cate;
        	$count = $GLOBALS['db']->getOne($sqlCount);
        	$unread = array();
        	foreach ($list as &$item) {
        		$item['create_time'] = to_date($item['create_time']);
        		if ($item['data']) {
        			$biz_data = unserialize($item['data']);
        			$ctl = substr($biz_data['ctl'], 0, 6);
        			//print_r($biz_data);exit;
        			if($biz_data['type']==1){
        			    $item['link'] = wap_url('biz', $biz_data['ctl'], array('type' => $biz_data['type'])); //1期不做跳转
        			}elseif($ctl=='biz_dc'){
        			    $item['link'] = wap_url('biz', substr($biz_data['ctl'],4), array('lid' => $biz_data['ext']['lid'],'data_id' => $biz_data['data_id'])); //1期不做跳转
        			}else{
        			    $item['link'] = wap_url('biz', $biz_data['ctl'], array('data_id' => $biz_data['data_id'])); //1期不做跳转
        			}
        			$item['title'] = $this->title[$biz_data['title']];
        		}
        		unset($item['data']);
        		if ($item['is_read'] == 0) {
        			$unread[] = $item['id'];
        		}
        	}
        	unset($item);
        }

        if (!empty($unread)) {
        	$unreadsql = 'UPDATE '.DB_PREFIX.'biz_msg_box SET is_read=1 WHERE id in ('.implode(',', $unread).')';
        	$GLOBALS['db']->query($unreadsql);
        }

        $root['item'] = $list;

        $page_total = ceil($count/$page_size);
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
	    $root['page_title'] = $cate == 'order' ? '订单消息' : '资产消息';
        //print_r($root);exit;
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