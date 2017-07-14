<?php 

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("EMPTY_ERROR",1);  //未填写的错误
define("FORMAT_ERROR",2); //格式错误
define("EXIST_ERROR",3); //已存在的错误

define("ACCOUNT_NO_EXIST_ERROR",1); //帐户不存在
define("ACCOUNT_PASSWORD_ERROR",2); //帐户密码错误
define("ACCOUNT_NO_VERIFY_ERROR",3); //帐户未激活


function auto_do_login_dist($account_user,$account_md5_password,$from_cookie = true){
	$result = array();
	$result['status'] = 1;
	$result['data'] = "";

	$dist_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."distribution WHERE username='".$account_user."' AND is_delete = 0");
	if($dist_data){
		$pwdOK = false;
		if($from_cookie)
		{
			$pwdOK = md5($dist_data['password']."_EASE_COOKIE")==$account_md5_password;
		}
		else
		{
			$pwdOK = $dist_data['password']==$account_md5_password;
		}
		
		if($pwdOK){
			//$GLOBALS['db']->query("update ".DB_PREFIX."supplier_account set login_ip = '".CLIENT_IP."' where id=".$biz_data['id']);
			es_session::set("dist_info",$dist_data);
			$GLOBALS['dist_info'] = $dist_data;
		}
	}
}

/**
 * 代理商登录时候的数据库验证
 * @param string $account_user
 * @param string $account_password
 */
function do_login_dist($account_user,$account_password){
	$dist_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."distribution WHERE username='".$account_user."' AND is_delete=0");
	
	$result = array();
	$result['status'] =1;
	$result['data'] = '';
	if(!$dist_data)
	{
		$result['status'] = 0;
		$result['data'] = ACCOUNT_NO_EXIST_ERROR;
		return $result;
	}else{
		$result['dist_info'] = $dist_data;
		if($dist_data['password'] != md5($account_password)){
			$result['status'] = 0;
			$result['data'] = ACCOUNT_PASSWORD_ERROR;
			return $result;
		}
		
		// 保存登录会话到session
		$dist_data = save_dist_account_info($dist_data);
		$GLOBALS['dist_info'] = $dist_data;

// 		$GLOBALS['db']->query("update ".DB_PREFIX."distribution set login_ip = '".CLIENT_IP."',login_time= ".NOW_TIME." where id =".$dist_data['id']);
		return $result;
	}
	
}

function save_dist_account_info($account_info)
{
    //实时刷新会员数据
    if($account_info)
    {
        es_session::set('dist_info',$account_info);
    }

    return $account_info;
}


/**
 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
 */
function loginout_dist()
{
	$dist_info = es_session::get("dist_info");
	if(!$dist_info)
	{
		return false;
	}
	else
	{
		es_session::delete("dist_info");
	}
}

/**
 * @desc
 * @author    吴庆祥
 * @param $data  $data=array(type=>"",money=>"")
 *                          type 1.服务费增加（只有在用户收货结算后） 2.提现增加 //默认使用1
 *                          money 资金变动，可以为负数
 * @param int $dist_id   驿站fanwe_distribution表中的id
 * @param string $log_msg   $log_msg=''日志内容信息
 * @return
 */
function modify_dist_account($data,$dist_id,$log_msg=''){
    $r_data=array();
    $r_data['status']=0;
    $money=floatval($data['money']);
    if($money>0)
    {
        $GLOBALS['db']->query("update ".DB_PREFIX."distribution set money = money + ".$money.",service_total_money=service_total_money+".$money." where id =".$dist_id);
    }else if(floatval($data['money'])<0){
        $GLOBALS['db']->query("update ".DB_PREFIX."distribution set money = money + ".$money." where id =".$dist_id." and money+".$money.">=0");
    }
    if(!$GLOBALS['db']->affected_rows()){
        $r_data['info']="操作失败";
        return $r_data;
    }
    //存入distribution_money_log的数据
    $log_info=array();
    $log_info['log_info'] = $log_msg;
    $log_info['create_time'] = NOW_TIME;
    if($data['type']==2){
        $log_info['type']=2;
    }else{
        $log_info['type']=1;
    }
    $log_info['distribution_id']=$dist_id;
    $log_info['money']=$data['money'];
    $GLOBALS['db']->autoExecute(DB_PREFIX."distribution_money_log",$log_info);
    //存入distribution_statements的数据
    $dist_statements=array();
    $dist_statements["distribution_id"]=$dist_id;
    if($data['type']==2){
        $dist_statements['sale_money']=0;
        $dist_statements['withdrawals_money']=$data['money'];
    }else{
        $dist_statements['sale_money']=$data['money'];
        $dist_statements['withdrawals_money']=0;
    }
    $dist_statements['stat_time']=to_date(NOW_TIME,"Y-m-d");
    $dist_statements['stat_month']=to_date(NOW_TIME,"Y-m");
    $GLOBALS['db']->autoExecute(DB_PREFIX."distribution_statements",$dist_statements);
    
    $r_data['status']=1;
    load_dist_user($dist_id);
    return $r_data;
}
function load_dist_user($dist_id){
    $dist_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=".$dist_id);
    if($dist_info)
    {
        es_session::set('dist_info',$dist_info);
    }
    return $dist_info;
}