<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class userModule extends HizBaseModule{
	public function login(){
		init_app_page();
		$GLOBALS['tmpl']->display("login.html");
	}

	
	function do_login(){
		$account_name = strim($_POST['account_name']);
		$account_password = strim($_POST['account_password']);
		
		$data = array();
		//验证
		
		//验证码
		$verify = md5(strim($_POST['verify_code']));
		$session_verify = es_session::get('verify');
		
		if($verify!=$session_verify)
		{
			$data['status'] = false;
			$data['info']	=	"图片验证码错误";
			$data['field'] = "verify_code";
			ajax_return($data);
		}
		if($account_name == ''){
			$data['status'] = false;
			$data['info'] = "请输入用户名";
			$data['field'] = "account_user";
			ajax_return($data);
		}
		if($account_password == ''){
			$data['status'] = false;
			$data['info'] = "请输入密码";
			$data['field'] = "account_password";
			ajax_return($data);
		}
		$account_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."agency WHERE account_name='".$account_name."' AND is_delete=0");
		
		require_once(APP_ROOT_PATH."system/libs/hiz_user.php");

		$result = do_login_hiz($account_name,$account_password);

		if($result['status'])
		{

			//获取权限

			$jump_url = url("hiz","index");
			$return['status'] = true;
			$return['info'] = "登录成功";
			$return['data'] = $result['msg'];
			$return['jump'] = $jump_url;
			$return['tip'] = $tip;

			ajax_return($return);
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$field = "account_name";
				$err = $GLOBALS['lang']['USER_NOT_EXIST'];
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$field = "account_password";
				$err = $GLOBALS['lang']['PASSWORD_ERROR'];
			}
			if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
			{
				$field = "account_name";
				$err = $GLOBALS['lang']['USER_NOT_VERIFY'];
			}
			$data['status'] = false;
			$data['info']	=	$err;
			$data['field'] = $field;
			ajax_return($data);
		}
		
	}
	
	
	/**
	 * 验证会员字段的有效性
	 * @param array $data  字段名称/值
	 * @return array
	 */
	function check_register_field($data)
	{
		$data = array();
		$data['status'] = true;
		$data['info'] = "";
		
		if(strim($data['account_name']))
		{
			$rs = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_account where account_name = '".$data['account_name']."'");
			if(intval($rs)>0)
			{
				$data['status'] = false;
				$data['info'] = "账户已被注册";
				$data['field'] = "account_name";
				return $data;
			}
		}
		
		if(strim($data['account_mobile']))
		{
			if(!check_mobile($data['account_mobile']))
			{
				$data['status'] = false;
				$data['info'] = "手机号格式不正确";
				$data['field'] = "account_mobile";
				return $data;
			}
			$rs = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_account where account_mobile = '".$data['account_mobile']."'");
			if(intval($rs)>0)
			{
				$data['status'] = false;
				$data['info'] = "手机号已被注册";
				$data['field'] = "account_mobile";
				return $data;
			}
		}

		if(strim($data['verify_code']) && app_conf("SMS_ON") == 1)
		{
	
			$verify = md5($data['verify_code']);
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{
				$data['status'] = false;
				$data['info']	=	"图片验证码错误";
				$data['field'] = "verify_code";
				return $data;
			}
		}
		
		return $data;
	}
	
	public function logout()
	{
		require_once(APP_ROOT_PATH."system/libs/hiz_user.php");
		loginout_hiz();
		es_session::delete("hiz_nav_list");
		$jump = url("hiz","user#login");
		app_redirect($jump);
	}
	
	public function edit_password(){
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->display("edit_password.html");
	}
	
	public function do_edit_password(){
		global_run();
		$data = array();
		$data['status'] = 1;
		
		$account_password = strim($_POST['account_password']);
		$new_account_password = strim($_POST['new_account_password']);
		$rnew_account_password = strim($_POST['rnew_account_password']);
		if($account_password == ''){
			$data['status'] = 0;
			$data['info'] = "原密码不能为空";
			ajax_return($data);
		}
		if($new_account_password == ''){
			$data['status'] = 0;
			$data['info'] = "新密码不能为空";
			ajax_return($data);
		}
		if(strlen($new_account_password)<6){
			$data['status'] = 0;
			$data['info'] = "新密码长度不能小于6位";
			ajax_return($data);
		}
		if($rnew_account_password == ''){
			$data['status'] = 0;
			$data['info'] = "请确认新密码";
			ajax_return($data);
		}
		if($new_account_password != $rnew_account_password){
			$data['status'] = 0;
			$data['info'] = "请确认两次输入的新密码";
			ajax_return($data);
		}
		$account_info = $GLOBALS['hiz_account_info'];

		if($account_info){//用户必须登录存在

			if(md5($account_password) != $account_info['account_password']){
				$data['status'] = 0;
				$data['info'] = "原密码错误";
				ajax_return($data);
			}else{
				$GLOBALS['db']->query("update ".DB_PREFIX."agency set account_password = '".md5($new_account_password)."' where id = ".intval($account_info['id']));
				$data['jump'] = url("hiz","user#logout");
			}
		}else{
			$data['status'] = 0;
			$data['info'] = "请登录后修改！";
			ajax_return($data);
			
		}
		ajax_return($data);
		
	}
	public function getpassword(){
	    $GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
	    $GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());
	    $GLOBALS['tmpl']->display("getpassword.html");
	}
	public function do_getpassword(){
	    $account_name=strim($_REQUEST['account_name']);
	    $mobile = strim($_REQUEST['user_mobile']);
	    $mobile_info=$GLOBALS['db']->getRow("select id,mobile from ".DB_PREFIX."agency where account_name='".$account_name."' and mobile='".$mobile."'");
	    $sms_verify = intval($_REQUEST['sms_verify']);
	    $sql = 'SELECT add_time FROM '.DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
	    $add_time = $GLOBALS['db']->getOne($sql);
	    $account_id=$mobile_info['id'];
	    $account_password=strim($_REQUEST['account_password']);
// 	    if (empty($add_time)) {
// 	        $data['status'] = false;
// 	        $data['info'] = '短信验证码错误';
// 	        $data['field']= "sms_verify";
// 	    }
// 	    elseif ($add_time < NOW_TIME - 300) {
// 	        $data['status'] = false;
// 			$data['info'] = '验证码已过期';
// 			$data['field']= "sms_verify";
// 		}
//         elseif(!$mobile_info){
//             $data['status'] = false;
//             $data['info'] = '该用户不存在或手机号错误';
//         }
// 	    else {
// 	        $account_password=strim($_REQUEST['account_password']);
// 	        if (strlen($account_password) < 4) {
// 	            $data['status'] = false;
// 	            $data['info'] = '密码过短';
// 	        } else {
// 	            $account_id=$mobile_info['id'];
// 	            $update = array('account_password' => md5($account_password), 'update_time' => NOW_TIME);
// 	            $GLOBALS['db']->autoExecute(DB_PREFIX.'agency', $update, 'UPDATE', 'id='.$account_id);
// 	            //logger::write(print_r($GLOBALS['db']->getLastSql(),1));exit;
// 	            $data['status'] = true;
// 	            $data['info'] = '密码修改成功';
// 	            $data['jump'] = url('hiz', 'user#login');
// 	            // 删除验证码
// 	            $sql = "DELETE FROM ".DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
// 	            $GLOBALS['db']->query($sql);
// 	        }
	        
// 	    }
	    do {
	        if (empty($add_time)) {
	            $status = false;
	            $info = '短信验证码错误';
	            break;
	        }
	        if ($add_time < NOW_TIME - 300) {
	            $status = false;
	            $info = '验证码已过期';
	            break;
	        }
	        if(!$mobile_info){
	            $status = false;
	            $info = '该用户不存在或手机号错误';
	            break;
	        }
	        if (strlen($account_password) < 4) {
	            $status = false;
	            $info = '密码过短';
	            break;
	        }
	        $update = array('account_password' => md5($account_password), 'update_time' => NOW_TIME);
	        $GLOBALS['db']->autoExecute(DB_PREFIX.'agency', $update, 'UPDATE', 'id='.$account_id);
	        //logger::write(print_r($GLOBALS['db']->getLastSql(),1));exit;
	        $status = true;
	        $info = '密码修改成功';
	        $jump = url('hiz', 'user#login');
	        // 删除验证码
	        $sql = "DELETE FROM ".DB_PREFIX.'sms_mobile_verify WHERE mobile_phone='.$mobile.' AND code='.$sms_verify;
	        $GLOBALS['db']->query($sql);
	    } while(0);
	    $data = array('status' => $status, 'info' => $info ,'jump'=>$jump);
 	    ajax_return($data);
	    
	}
	public function load_sub_cate()
	{
		$cate_id = intval($_REQUEST['id']);
		$type_list = $GLOBALS['db']->getAll("select t.* from ".DB_PREFIX."deal_cate_type as t left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = t.id where l.cate_id = ".$cate_id);
		$html = "";
		foreach($type_list as $item)
		{
			$html.='<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_cate_type_id[]" value="'.$item['id'].'" />'.$item['name'].'</label>';
		}
	
		header("Content-Type:text/html; charset=utf-8");
		echo $html;
	}
	
	public function load_city_area()
	{
		$city_id = intval($_REQUEST['id']);
		$area_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where city_id = ".$city_id." and pid = 0 order by sort desc");
		$html = "";
		if($area_list)
		{
			$html = "<select name='area_id[]'  class='ui-select'>";
			foreach($area_list as $item)
			{
				$html .= "<option value='".$item['id']."'>".$item['name']."</option>";
			}
			$html.="</select>";
		}
		header("Content-Type:text/html; charset=utf-8");
		echo $html;
	
	}
	public function load_quan_list()
	{
		$area_id = intval($_REQUEST['id']);
		$area_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where pid = ".$area_id." order by sort desc");
		$html = "";
		foreach($area_list as $item)
		{
			$html.='<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="area_id[]" value="'.$item['id'].'" />'.$item['name'].'</label>';
		}
	
		header("Content-Type:text/html; charset=utf-8");
		echo $html;
	}

    public function user_list(){
        global_run();
        init_app_page();
        //获取参数
        $page=intval($_REQUEST['p']);
        $name=$_REQUEST['name'];
        $hiz_info=$GLOBALS['hiz_account_info'];
        $limit=formatLimit($page);
        $where=" ";
        if($name){
            $where=" and (u.user_name='".$name."' or u.mobile='".$name."') ";
        }
        $list=$GLOBALS['db']->getAll("select u.id,u.user_name,u.mobile,u.point,u.score,u.total_score,u.money,p.user_name p_user_name,u.login_time,u.is_effect from ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user p on u.pid=p.id where u.agency_id=".$hiz_info['id'].$where."order by u.id desc".$limit);
        $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user u where u.agency_id=".$hiz_info['id'].$where);
        foreach($list as $key=>$value){
            $list[$key]['money']=number_format($value['money'],2);
            $list[$key]['is_effect']=$value['is_effect']?"有效":"禁用";
            $list[$key]['login_time']=to_date($value['login_time']);
        }
        formatPage($count);
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->display("pages/user/user_list.html");
    }
    public function user_log(){
        global_run();
        init_app_page();
        //获取参数
        $page=$_REQUEST['p'];
        $user_id=intval($_REQUEST['user_id']);
        $limit=formatLimit($page);
        //开始取数据
        $user_name=$GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$user_id);
        $list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_log where user_id=".$user_id." order by id desc ".$limit);
        $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_log where user_id=".$user_id);
        foreach($list as $key=>$value){
            $list[$key]['log_time']=to_date($value['log_time']);
            $list[$key]['score']=$value['score']."积分";
            $list[$key]['money']=$value['money']<0?"-&yen;".number_format(abs($value['money']),2):"&yen;".number_format($value['money'],2);
        }
        formatPage($count);
        $GLOBALS['tmpl']->assign("user_name",$user_name);
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->display("pages/user/user_log.html");
    }
    public function user_detail(){
        global_run();
        init_app_page();
        //获取参数
        $data=array();
        $user_id=intval($_REQUEST['user_id']);
        $user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".$user_id);
        //分解会员信息
        //基本信息
        $base_info=array();
        $base_info['user_name']=$user_info['user_name'];
        $base_info['email']=$user_info['email']?$user_info['email']:"未设置";
        $base_info['mobile']=$user_info['mobile']?$user_info['mobile']:"未设置";
        if($user_info['city_id']){
           $address=$GLOBALS['db']->getCol("select name from ".DB_PREFIX."delivery_region where id in(".$user_info["province_id"].",".$user_info['city_id'].") order by id");
           $base_info['address']=implode(" ",$address);
        }else{
            $base_info['address']="未设置";
        }
        if($user_info['byear']){
            $base_info['birth_day']=$user_info['byear']."-".$user_info['bmonth']."-".$user_info['bday'];
        }else{
            $base_info['birth_day']="未设置";
        }
        $row=$GLOBALS['db']->getRow("select g.name as group_name ,l.name as level_name from ".DB_PREFIX."user_group g,".DB_PREFIX."user_level l where g.id=".$user_info['group_id']." and l.id=".$user_info['level_id']);
        $base_info['group_name']=$row['group_name'];
        $base_info['level_name']=$row['level_name'];
        $data['base_info']=$base_info;
        //资产信息
        $asset_info=array();
        $asset_info['money']=number_format($user_info['money'],2);
        $asset_info['score']=$user_info['score'];
        $asset_info['total_score']=$user_info['total_score'];
        $asset_info['point']=$user_info['point'];
        $data['asset_info']=$asset_info;
        //其他信息
        $other_info=array();
        $other_info['create_time']=to_date($user_info['create_time']);
        $other_info['login_time']=to_date($user_info['login_time']);
        $data['other_info']=$other_info;

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("pages/user/user_detail.html");
    }
}
function check_issupplier()
{
    $account_name = $GLOBALS['user_info']['merchant_name'];
    $account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_account where account_name = '".$account_name."' and is_effect = 1 and is_delete = 0");
    if($account)
    {
        $s_account_info = es_session::get("account_info");
        if(intval($s_account_info['id'])==0)
        {
            showErr("您已经是商家会员，请登录",0,url("biz"));
        }
        else
            app_redirect(url("biz"));
    }

}