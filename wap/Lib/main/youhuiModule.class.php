<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class youhuiModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();		

		$param['data_id'] = intval($_REQUEST['data_id']); //分类ID

		

		//获取品牌
		$data = call_api_core("youhui","index",$param);
		
		if(intval($data['id'])==0)
		{
		    //app_redirect(wap_url("index"));
		    //$jump_url = wap_url('index', 'youhuis');
		    $script = suiShow('优惠券不存在或已删除', $jump_url);
		    $GLOBALS['tmpl']->assign('suijump', $script);
		    $GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
		}else{

    		$youhui = $data['youhui_info'];
    
    		// 领券期格式化
    		// $youhui['format_end_time'] = empty($youhui['end_time']) ? '领券期限：永久' : '领券期至：'.date('Y-m-d', $youhui['end_time']);
    		// 使用期格式化
    		// if(empty($youhui['expire_day']) && !empty($youhui['end_time']))
    		//     $youhui['format_expire'] = '优惠券领取后，在'.date('Y-m-d', $youhui['end_time'])."前可使用";
    		// else
    		//     $youhui['format_expire'] = empty($youhui['expire_day']) ? '使用期限：永久' : "优惠券领取后".$youhui['expire_day']."天内可用";
    
    		// 已抢百分比
    		if ($youhui['total_num'] > 0) {
    			$percent = round($youhui['user_count'] / $youhui['total_num'] * 1000) / 10;
    			$youhui['user_percent'] = $percent;
    		}
    
    		// 平均分格式化
    		if ($youhui['avg_point'] > 0) {
    			$youhui['avg_point_percent'] = $youhui['avg_point'] / 5 * 100;
    		} else {
    			$youhui['avg_point_percent'] = 0;
    		}
    
    		// 判断优惠券的可领取状态
    		// $status = 9; // 默认可以领取
    		// if($data['user_login_status'] != LOGIN_STATUS_LOGINED){
    		//     // $is_login = 0;
    		//     $status = 0; // 登录后领取
    		// } elseif ($youhui['end_time'] > 0 && $youhui['end_time'] < time()) {
    		// 	$status = 1; // 领取已结束
    		// } elseif ($youhui['begin_time'] > 0 && $youhui['begin_time'] > time()) {
    		// 	$status = 2; // 活动未开始
    		// } elseif (/*$youhui['total_num'] > 0 &&*/ $youhui['total_num'] <= $youhui['user_count']) {
    		// 	$status = 3; // 已抢光
    		// } else { // 判断登录后的状态
    		// 	$user_info = $GLOBALS['user_info'];
    		// 	// 判断是否已领取
    		// 	$today_begin = strtotime(date('Y-m-d'));
    		// 	$today_end = $today_begin + 3600 * 24 - 1;
    		// 	$sql = 'SELECT count(*) FROM '.DB_PREFIX.'youhui_log WHERE user_id='.$user_info['id'].' AND youhui_id='.$param['data_id'].' AND create_time BETWEEN '.$today_begin.' AND '.$today_end;
    		// 	$has_get = $GLOBALS['db']->getOne($sql);
    		// 	if ($youhui['user_limit'] > 0 && $has_get >= $youhui['user_limit']) {
    		// 		$status = 4; // 今日已领完
    		// 	} elseif ($user_info['point'] < $youhui['point_limit']) {
    		// 		$status = 5; // 经验不足
    		// 	} elseif ($user_info['score'] < $youhui['score_limit']) {
    		// 		$status = 6; // 积分不足
    		// 	}
    		// }
    
    		/*if ($data['user_login_status'] == LOGIN_STATUS_LOGINED) {
    			// 判断是否收藏
    			$data['is_collect'] = $this->_is_collect($GLOBALS['user_info']['id'], $param['data_id']);
    		}*/

    		foreach ($data['other_supplier_location'] as $k=>$v){
				$data['other_supplier_location'][$k]['url'] =  wap_url("index", 'store', array('data_id'=>$v['id']) );
		
				// $data['other_supplier_location'][$k]['distance'] = format_distance_str($v['distance']);
			}
    
    		$GLOBALS['tmpl']->assign('youhui_status', $youhui['status']);

    		$GLOBALS['tmpl']->assign("youhui",$youhui);
    		$GLOBALS['tmpl']->assign("data",$data);	
    		$GLOBALS['tmpl']->assign("ajax_url",wap_url("index","youhui"));
    		$GLOBALS['tmpl']->display("youhui.html");
		}
	}
	
	/*
	 * 领取优惠券
	 * */
	public function download_youhui(){
	    $data_id = intval($_REQUEST['data_id']);
	    $data = call_api_core("youhui","download_youhui",array("data_id"=>$data_id));

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED)
    	{
    		$data['status'] = 0;
    		$data['info'] = "请登录后操作";
    		$data['jump']  = wap_url("index","user#login");
    	}
	    ajax_return($data);
	}
	
	public function detail(){
	    global_run();
	    init_app_page();
	    
	    $data_id = intval($_REQUEST['data_id']);
	    
	    $data = call_api_core("youhui","detail",array("data_id"=>$data_id));
	    
	    $GLOBALS['tmpl']->assign("data",$data);
	    $GLOBALS['tmpl']->assign("youhui_info",$data['youhui_info']);
	    $GLOBALS['tmpl']->display("youhui_detail.html");
	}

	/*protected function is_collect($user_id, $data_id)
	{
		$data = call_api_core('youhui', 'is_collect', array('user_id' => $user_id, 'data_id' => $data_id));
		return $data['status'];
	}*/
	

	/**
	 * 优惠券添加收藏
	 */
	public function add_collect()
	{
		global_run();

		$data_id = intval($_REQUEST['data_id']);

		$data = call_api_core('youhui', 'add_collect', array('data_id' => $data_id));

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED)
    	{
    		$data['status'] = -1;
    		$data['info'] = "登录后才能收藏";
    		$data['jump']  = wap_url("index","user#login");
    	}
	    ajax_return($data);

		// $data['info'] = 'test';
		// $data['status'] = 1;
		// $data['collect_count'] = 1;
		// ajax_return($data);
	}

	/**
	 * 优惠券取消收藏
	 * @return json 
	 */
	public function del_collect()
	{
		global_run();

		$data_id = intval($_REQUEST['data_id']);

		$data = call_api_core('youhui', 'del_collect', array('data_id' => $data_id));

		if($data['user_login_status']!=LOGIN_STATUS_LOGINED)
    	{
    		$data['status'] = -1;
    		$data['info'] = "请登录后操作";
    		$data['jump']  = wap_url("index","user#login");
    	}
	    ajax_return($data);
	}
	
	/**
	 * A-9-5 全部评论页
	 * @return
	 */
	public function reviews(){
	    global_run();
	    init_app_page();
	    $param['data_id'] = intval($_REQUEST['data_id']);
	    $param['page'] = abs(intval($_REQUEST['page']));
	
	    $data = call_api_core("youhui","reviews",$param);
	
	    if(isset($data['page']) && is_array($data['page'])){
	        $page = new Page($data['page']['data_total'],$data['page']['page_size']);
	        $p  =  $page->show();
	        $GLOBALS['tmpl']->assign('pages',$p);
	    }
	    $GLOBALS['tmpl']->assign('data', $data);
	    $GLOBALS['tmpl']->display("store_reviews.html");
	}
}
?>