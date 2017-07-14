<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_fxinviteApiModule extends MainBaseApiModule
{
	
	/**
	 * 	 会员中心我的推荐接口
	 * 
	 * 	  输入：
	 *  page:int 当前的页数
	 *  user_id:仅在查看下线会员时传入
	 *  
	 *  输出：
	 * user_login_status:int   0表示未登录   1表示已登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 * fxmoney:¥8.9  string  当前分销收益	 
	 * pname:fanwe  string  推荐人名称
	 * user_temid:72 int 查看下线会员时返回的被查看下线会员id，仅在查看下线会员时返回，如果存在此id，则不能继续查看下线
	 * list:array:array 下线会员列表，结构如下
	 *  Array
		(
		    [0] => Array
		        (
		                [id] => 19 int 下线会员id
			            [money] =>¥3.9 string 我从该会员获取的推广佣金
			            [user_name] => fanwe1  string  被推荐人名称
		        )
		)
	 */
	public function index()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];	
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();	
		if($user_login_status!=LOGIN_STATUS_LOGINED){	
				
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;

			if($user_data['pid']==0){
				$root['pname']="无推荐人";
			}else{
				$root['pname'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$user_data['pid']);
			}			
			$root['fxmoney']=format_price($user_data['fx_money']);
			
			
			$user_temid=intval($GLOBALS['request']['user_id']);
			if($user_temid>0){
				$pid =$GLOBALS['db']->getOne("select pid from ".DB_PREFIX."user where id=".$user_temid);
				if($pid==$user_data['id']){
					$user_id=$user_temid;
					$root['user_temid']=$user_temid;
				}
			}			

			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;					
			
			$list =$GLOBALS['db']->getAll("select u.id,u.user_name,fxr.money from ".DB_PREFIX."user as u left join ".DB_PREFIX."fx_user_reward as fxr on fxr.pid=".$user_data['id']." and fxr.user_id=u.id where u.pid =".$user_id." order by u.create_time desc limit ".$limit);
			$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where pid = ".$user_id);			
			

			
			$page_total = ceil($count/$page_size);
			
			foreach($list as $k=>$v)
			{				
				$list[$k]['money'] = format_price($v['money']);
			}			
			
			
			$root['list'] = $list?$list:array();
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
		
			$root['page_title']="我的好友";

		}
		
		return output($root);

	}

	public function wap_index()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];	
		$user_id = intval($user_data['id']);
		
		$user_login_status = check_login();	
		if ($user_login_status == LOGIN_STATUS_LOGINED) {
			if ($user_data['is_fx'] == 0) {
				$root['is_fx'] == 0;
			} else {
				$home_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : $user_id;
				$is_home = $home_id == $user_id ? true : false;
				$search_id = $is_home ? $user_id : $home_id;

				$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
				$page_size = PAGE_SIZE;
				$limit = (($page-1)*$page_size).",".$page_size;
				// $sql = "select u.id,u.user_name,fxr.money from ".DB_PREFIX."user as u left join ".DB_PREFIX."fx_user_reward as fxr on fxr.pid=".$search_id." and fxr.user_id=u.id where u.pid =".$user_id." order by u.create_time desc limit ".$limit;
				
				// $sql = 'SELECT u.id,u.user_name,fxr.money %s FROM '.DB_PREFIX.'user as u left join '.DB_PREFIX.'fx_user_reward AS fxr on fxr.pid=u.pid %s WHERE u.pid='.$search_id.' ORDER BY u.create_time DESC LIMIT '.$limit;
				$sql = 'SELECT u.id,u.user_name,fxr.money %s FROM '.DB_PREFIX.'user as u left join '.DB_PREFIX.'fx_user_reward AS fxr on fxr.pid='.$user_id.' AND fxr.user_id = u.id %s WHERE u.pid='.$search_id.' ORDER BY u.create_time DESC LIMIT '.$limit;
				// $counts = '';
				// $fields = '';
				// if ($user_id == $search_id) {
					$fields = ',n.nums';
					$counts = ' LEFT JOIN (SELECT a.pid,COUNT(a.id) as nums FROM '.DB_PREFIX.'user AS a INNER JOIN '.DB_PREFIX.'user AS b ON a.pid=b.id GROUP BY a.pid) AS n ON n.pid=u.id ';
				// }
				$sql = sprintf($sql, $fields, $counts);
				$list =$GLOBALS['db']->getAll($sql);
				// echo $sql;exit;
				$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where pid = ".$user_id);			
				
				$page_total = ceil($count/$page_size);
				
				foreach($list as $k=>$v) {				
					$list[$k]['money'] = format_price($v['money']);
					$list[$k]['nums'] = $v['nums'] ?: 0;
				}
				$root['is_fx'] = $user_data['is_fx'];
				$root['is_home'] = $is_home;
				$root['list'] = $list?$list:array();
				
				$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);

				$root['page_title']="我的好友";
			}

		}
		$root['user_login_status'] = $user_login_status;
		return output($root);
	}
	
	
	

	
	

	
	/**
	 * 	 会员中心分销资金日志列表
	 * 
	 * 	  输入：
	 *  page:int 当前的页数

	 *  
	 *  输出：
	 * user_login_status:[int]   0表示未登录   1表示已登录
	 * login_info:[string] 未登录状态的提示信息，已登录时无此项
	 * fxmoney:¥8.9  string  当前分销收益	 
	 * pname:fanwe  string  推荐人名称
	 * list:array:array 分销资金日志列表，结构如下
	 *  Array
		(
		    [0] => Array
		        (
		                [create_time] => 2015-06-25 10:15:24  string 操作时间
			            [money] =>¥3.9 string 金额
			            [log] =>   string  详情
		        )
		)
	

   
	 */
	public function money_log()
	{
		$root = array();		
			
		$user_data = $GLOBALS['user_info'];		
		$user_id = intval($user_data['id']);
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){			
			$root['user_login_status'] = $user_login_status;	
		}else{
			$root['user_login_status'] = 1;

			if($user_data['pid']==0){
				$root['pname']="无推荐人";
			}else{
				$root['pname'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$user_data['pid']);
			}			
			$root['fxmoney']=format_price($user_data['fx_money']);			
			
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;					
			
			$list =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_id." order by create_time desc limit ".$limit);
			$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_id);
			
	
			
			$page_total = ceil($count/$page_size);
			
			foreach($list as $k=>$v)
			{				
				$list[$k]['money'] = format_price($v['money']);
				$list[$k]['create_time'] = to_date($v['create_time']);
			}			
			
			
			$root['list'] = $list?$list:array();
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);

			$root['page_title']="分销资金日志";			
			
				
		}

	
		
		return output($root);

	}		
	
	

	
}
?>