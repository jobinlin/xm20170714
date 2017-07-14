<?php

/**
 * 手机端提现接口
 *
 * @author jobinlin
 */
class uc_moneyApiModule extends MainBaseApiModule
{
    
    /**
     * 用户提现首页
     * 输入：
     * 输出： money ：float 用余额
     */
    public function index()
	{
	
		$root = array();		
		/*参数初始化*/
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;
		}
		else
        {
            $root['user_login_status'] = $user_login_status;
            $root['money'] = round($user['money'],2);
            $root['mobile'] = $user['mobile'];
            $bank=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_bank where user_id=".$user['id']." and use_times=(select max(use_times) from ".DB_PREFIX."user_bank where user_id=".$user['id'].")");
            if($bank)
            $root['bank']=$bank['bank_name']."&nbsp;尾号".substr($bank["bank_account"], -4); 
        }
		
		$root['page_title'].="余额";
		return output($root);
	}
    
    /**
     * 银行卡列表
     * 输入：无
     * 输出：
     * bank_list | array 绑定的银行列表
     * array(
     *      id
     *      bank_name
     * )
     * money | float 余额
     * real_name |string  会员真实姓名(用于再次绑定银行卡)
     */
    public function withdraw_bank_list(){
        $root = array();		
    	/*参数初始化*/
    	
    	//检查用户,用户密码
    	$user = $GLOBALS['user_info'];
    	//print_r($user);exit;
    	$user_login_status = check_login();
    	//分页
    	$page = intval($GLOBALS['request']['page']);
    	$page=$page==0?1:$page;
    	$page_size = PAGE_SIZE;
    	$limit = (($page-1)*$page_size).",".$page_size;
    	if($user_login_status!=LOGIN_STATUS_LOGINED){
    	    $root['user_login_status'] = $user_login_status;
    	} else {
            $root['user_login_status'] = $user_login_status;
            $default = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_bank where user_id=".$user['id']." and use_times=(select max(use_times) from ".DB_PREFIX."user_bank where user_id=".$user['id'].")");
            $root['default_id']=$default['id'];
            $root['bank_user']=$default['bank_user'];
            $root['bank_info']=$default['bank_name']."&nbsp;尾号".substr($default["bank_account"], -4); 
            $root['mobile'] = $user['mobile'];
            
            //可提现金额
            $submitted_money = floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."withdraw where user_id = ".$user['id']." and is_delete = 0 and is_paid = 0"));
            $all_money=round($GLOBALS['db']->getOne("select money from ".DB_PREFIX."user where id = ".$user['id']),2);
            if($all_money>$submitted_money){
                $root['money'] = $all_money-$submitted_money;
            }else{
                $root['money'] = 0;
            }
            $bank_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_bank where user_id = ".$user['id']);
            $f_bank_list = array();

            //分页
            $page = intval($GLOBALS['request']['page']);
            $page=$page==0?1:$page;


            if($bank_list){
                foreach ($bank_list as $k=>$v){
                    $temp_arr = array();
                    $temp_arr['id'] = $v['id'];
                    $temp_arr['bank_user'] = $v['bank_user'];
                    //$tmp_bank_name = strripos($v['bank_name'], "银行")?substr($v['bank_name'],  0,strripos($v['bank_name'], "银行")+8):$v['bank_name'];
                    $temp_arr['bank_name'] = $v['bank_name']." 尾号".  substr($v['bank_account'], -4);
                    $f_bank_list[] = $temp_arr;
                }
                $root['bank']=1;
            }else{
                $root['bank']=0;
            }
            $root['bank_list'] = $bank_list?$f_bank_list:array();
            $root['step']=1;
        }
	
	    $root['page_title'].="提现";
	    //print_r($root);exit;
	    return output($root);
    }
    
    /**
     * 提现明细
     */
    public function withdraw_log(){
        $root = array();		
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else
        {
          $root['user_login_status'] = $user_login_status;
            fanwe_require(APP_ROOT_PATH."system/model/user_center.php");
            //分页
            $page = intval($GLOBALS['request']['page']);
            $page=$page==0?1:$page;
            	
            $page_size = PAGE_SIZE;
            $limit = (($page-1)*$page_size).",".$page_size;

            $result = get_user_withdraw($limit,$user['id']);
            foreach ($result['list'] as $k => $v) {
                $result['list'][$k]['create_time'] = to_date($v['create_time'],'Y-m-d H:i');
                $result['list'][$k]['money'] = round($v['money'],2);
                $result['list'][$k]['bank_user'] = $v['bank_user'];
                $result['list'][$k]['bank_name'] = $v['bank_name'];
                $tmp_bank_name = strripos($v['bank_name'], "银行")?substr($v['bank_name'],  0,strripos($v['bank_name'], "银行")+6):$v['bank_name'];
                $user_name = $v['bank_user'];
                $result['list'][$k]['bank_info'] =" 尾号".  substr($v['bank_account'], -4);
            }
            $root['data'] = $result['list'];
            $count = $result['count'];
            //分页
            $page_total = ceil($count/$page_size);
            $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
         }
        //print_r($root);exit;
        $root['page_title'].="提现明细";
        return output($root);
    }
    
    /**
     * 登陆密码验证
     */
    public function password_check(){
        $root = array();		
        /*参数初始化*/
        $check_pwd = strim($GLOBALS['request']['check_pwd']);
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
            $pwd = $GLOBALS['db']->getOne("select user_pwd from ".DB_PREFIX."user where id = ".$user['id']);

            if(md5($check_pwd) == $pwd){
                return output($root,1,"密码验证通过");
            }else{
                return output($root,0,"密码验证失败");
            }
        }
        return output($root);
    }
    
    /**
     * 提现申请提交
     * 输入：
     * user_bank_id :int 绑定的银行卡ID
     * money：提现的金额
     * check_pwd:验证成功的密码
     * 
     * 以下信息可为空
     * sms_verify:短信验证码
     * 
     * 银行信息 | string
     * bank_name    开户行名称
     * bank_account 开户行账号
     * bank_user    开会真实姓名
     * bank_mobile  银行预留的手机号
     * 
     * 输出：
     * 已有银行卡操作：
     * status: 0 失败 1 成功
     * info ：错误或者成功的消息
     * 
     * 新银行卡
     * 多一个 withdraw_id：int 新卡的数据库ID ，下一步操作使用
     */
    public function do_withdraw(){
    
    	$root=array();
        $user = $GLOBALS['user_info'];
  
        //获取参数
        $user_bank_id = intval($GLOBALS['request']['user_bank_id']);
        $money = floatval($GLOBALS['request']['money']);
        $check_pwd = strim($GLOBALS['request']['check_pwd']);
        
        
        $sms_verify = strim($GLOBALS['request']['sms_verify']);
        
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
            //有银行卡信息的~
            if($GLOBALS['request']['bank_name']){
                $bank_name = strim($GLOBALS['request']['bank_name']);
                $bank_account = strim($GLOBALS['request']['bank_account']);
                $bank_user = strim($GLOBALS['request']['bank_user']);
                $bank_mobile = strim($GLOBALS['request']['bank_mobile']);
                
                if($bank_name=="")
                {
                        return output($root,0,"请输入开户行全称");
                }
                if($bank_account=="")
                {
                        return output($root,0,"请输入开户行账号");
                }
                if($bank_user=="")
                {
                        return output($root,0,"请输入开户人真实姓名");
                }
                if($bank_mobile=="")
                {
                        return output($root,0,"请输入银行预留手机号");
                }
                
                
                //短信码验证
                if($sms_verify == ''){
                    return output($root,0,"请输入手机验证码");
                }
                $sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
                $GLOBALS['db']->query($sql);

                $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$bank_mobile."'");

                if($mobile_data['code']!=$sms_verify)
                {
                    return output($root,0,"手机验证码错误");
                }
            }

            //验证金额
            if($money <=0){
                return output($root,0,"提现金额必须大于0");
            }
            
            if($GLOBALS['request']['bank_name']){   //银行卡表单提交并且提现
            	
                $submitted_money = floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."withdraw where user_id = ".$user['id']." and is_delete = 0 and is_paid = 0"));
                if($submitted_money+$money>$user['money'])
                {
                        return output($root,0,"提现超额");
                }
                $withdraw_data = array();
                $withdraw_data['user_id'] = $user['id'];
                $withdraw_data['create_time'] = NOW_TIME;
                
                $withdraw_data['money'] = $money;
                $withdraw_data['create_time'] = NOW_TIME;
                $withdraw_data['bank_name'] = $bank_name;
                $withdraw_data['bank_account'] = $bank_account;
                $withdraw_data['bank_user'] = $bank_user;
                $withdraw_data['bank_mobile'] = $bank_mobile;

                $GLOBALS['db']->autoExecute(DB_PREFIX."withdraw",$withdraw_data);
                $root['withdraw_id'] = $GLOBALS['db']->insert_id();
                $GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$bank_mobile."'");
                
                
                
            }else{
                //密码验证
              
                if(md5($check_pwd)!==$user['user_pwd']){
                    return output($root,0,"密码验证失败");
                }
                if($user_bank_id<=0){
                    return output($root,0,"提交数据不正确");
                }
                $user_bank_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_bank where id = ".$user_bank_id);
                if(!$user_bank_info){
                    return output($root,0,"提交数据不正确");
                }
                $submitted_money = floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."withdraw where user_id = ".$user['id']." and is_delete = 0 and is_paid = 0"));
                if($submitted_money+$money>$user['money'])
                {
                	return output($root,0,"提现超额");
                }
                
                $withdraw_data = array();
                $withdraw_data['user_id'] = $user['id'];
                $withdraw_data['create_time'] = NOW_TIME;
                
                $withdraw_data['money'] = $money;
                $withdraw_data['create_time'] = NOW_TIME;
                $withdraw_data['bank_name'] = $user_bank_info['bank_name'];
                $withdraw_data['bank_account'] = $user_bank_info['bank_account'];
                $withdraw_data['bank_user'] = $user_bank_info['bank_user'];
                $withdraw_data['bank_moblie'] = $user_bank_info['bank_moblie'];
                $GLOBALS['db']->autoExecute(DB_PREFIX."withdraw",$withdraw_data);
                $GLOBALS['db']->query("update ".DB_PREFIX."user_bank set use_times=use_times+1 where id=".$user_bank_id);
            }
            $tmp_bank_name = strripos($withdraw_data['bank_name'], "银行")?substr($withdraw_data['bank_name'],  0,strripos($withdraw_data['bank_name'], "银行")+6):$withdraw_data['bank_name'];
            $root['bank_name'] = $tmp_bank_name." 尾号".  substr($withdraw_data['bank_account'], -4);
            return output($root,1,"提现申请提交成功，请等待审核");
            
            
        }
        return output($root);
    }
    
    /**
     * 绑定
     * withdraw_id：int 银行卡ID
     * is_bind：int 是否绑定
     * bank_name | string 格式化好的银行名称+卡号尾数
     */
    public function do_bind_bank(){
        $user = $GLOBALS['user_info'];
        $mobile = $GLOBALS['user_info']['mobile'];
        //获取参数
        $withdraw_id = intval($GLOBALS['request']['withdraw_id']);
        $is_bind = intval($GLOBALS['request']['is_bind']);
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
            if($is_bind>0 && $withdraw_id>0){
                $withdraw_info = $GLOBALS['db']->getRow("select count(*) from ".DB_PREFIX."withdraw where user_id= ".$user['id']." and id=".$withdraw_id);
                if($withdraw_info){
                    $GLOBALS['db']->autoExecute(DB_PREFIX."withdraw",array("is_bind"=>1),"UPDATE"," id = ".$withdraw_id);
                    if($GLOBALS['db']->affected_rows()){
                        output ($root,1,"操作成功");
                    }
                        
                }
            }
            //提现时新增到数据库
            $bank_name = strim($GLOBALS['request']['bank_name']);
            $bank_account = strim($GLOBALS['request']['bank_account']);
            $bank_user = strim($GLOBALS['request']['bank_user']);
            $sms_verify = strim($GLOBALS['request']['sms_verify']);
            $bank_mobile = $GLOBALS['request']['bank_mobile'];
            //'select count(*) from '.DB_PREFIX.'user_bank where bank_account='.$bank_account;
            if($bank_account=="")
            {
                return output($root,0,"请输入开户行账号");
            }
            if($bank_name=="")
            {
            	return output($root,0,"请输入开户行全称");
            }
            if($bank_user=="")
            {
            	return output($root,0,"请输入开户人真实姓名");
            }
            $acount=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_bank where bank_account=".$bank_account." and user_id=".$user['id']);
            if($acount){
                return output($root,0,"您已绑定过该银行卡");
            }
            //短信码验证
            if($sms_verify == ''){
            	return output($root,0,"请输入手机验证码");
            }
            $sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
            $GLOBALS['db']->query($sql);
            $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$GLOBALS['user_info']['mobile']."'");
            
            if($mobile_data['code']!=$sms_verify)
            {
            	return output($root,0,"手机验证码错误");
            }
            $data['user_id'] = $GLOBALS['user_info']['id'];
            $data['bank_name'] = $bank_name;
            $data['bank_account'] = $bank_account;
            $data['bank_user'] = $bank_user;
            $data['bank_moblie'] = $bank_mobile;
            $GLOBALS['db']->autoExecute(DB_PREFIX."user_bank",$data);
            if($GLOBALS['db']->affected_rows()){
                return output($root,1,"添加成功");
            }else{
                return output($root,0,"添加失败");
            }

        }
        return output($root);
    }


    /**
     * 获取用户添加过的银行卡列表
     *
     * @return array  ['id' => ID, 'bank_name' => 银行名称, 'bank_user' => 绑定用户名]
     */
    public function bank_list()
    {
        $user_login_status = check_login();
        if($user_login_status == LOGIN_STATUS_LOGINED){
            $bank_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_bank where user_id = ".$GLOBALS['user_info']['id']);
            $f_bank_list = array();

            if($bank_list){
                foreach ($bank_list as $k=>$v){
                    $temp_arr = array();
                    $temp_arr['id'] = $v['id'];
                    //格式化输出数据（在此多添加两个组合输出字段，[bank_name_r] => 中国工商银行，[bank_user_r] => 张三 尾号3350，方便显示）
                    $temp_arr['bank_name_r']=$v['bank_name'];
                    $temp_arr['bank_user_r']=$v['bank_user']." 尾号".  substr($v['bank_account'], -4);
                    $tmp_bank_name = strripos($v['bank_name'], "银行")?substr($v['bank_name'],  0,strripos($v['bank_name'], "银行")+8):$v['bank_name'];
                    $temp_arr['bank_name'] = $tmp_bank_name." 尾号".  substr($v['bank_account'], -4);
                    $temp_arr['bank_user'] = $v['bank_user'];
                    $f_bank_list[] = $temp_arr;
                }
            }
            $return['data'] = $f_bank_list;
            $return['page_title']="我的银行卡";
        } 
        $return['user_login_status'] = $user_login_status;
        //print_r($return);exit;
        return output($return);
    }

    /**
     * 删除用户绑定银行卡的接口
     * 请求数据格式 bank_id => id1,id2...  字符串 用, - | 或空格隔开
     * @return mixed 
     */
    public function del_bank()
    {
        $user = $GLOBALS['user_info'];
        $user_login_status = check_login();
        $status = 0;
        $info = '';
        $data = array();
        if($user_login_status == LOGIN_STATUS_LOGINED){
            $bank_id = $GLOBALS['request']['bank_id'];

            //$bank_ids = array_filter(preg_split('/[\-,\|\s]*/', $bank_id));
//             if (empty($bank_ids)) {
                
//                 $info = '参数格式错误';
//                 goto end;
//             }
//             $bank_id = implode(',', $bank_id);
            $sql = 'DELETE FROM '.DB_PREFIX.'user_bank where user_id='.$GLOBALS['user_info']['id'].' and id in ('.$bank_id.')';
            $delete = $GLOBALS['db']->query($sql);
//             if ($delete) {
               
//                 $data['data'] = $bank_id;
//                 $status = 1;
//                 goto end;
                
//             }
//            $info = '删除失败,请重试';
            $info = '删除成功';
//         }

//         end:
        }
        $data['user_login_status'] = $user_login_status;
        return output($data, $status, $info);
    }
 
    /* *
     *资金明细 
     */
    public function money_log(){
        
        $user = $GLOBALS['user_info'];
        $user_id=$GLOBALS['user_info']['id'];
        
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }else{
            $root['user_login_status'] = $user_login_status;
            
            $page = intval($GLOBALS['request']['page']);
            $page=$page==0?1:$page;
            	
            $page_size = PAGE_SIZE;
            $limit = (($page-1)*$page_size).",".$page_size;
            
            require_once(APP_ROOT_PATH.'system/model/user_center.php');
            $data = get_user_log($limit,$user_id,'money'); //获取资金数据
            $list=$data['list'];
            foreach ($list as $t => $v){
                $list[$t]['money']=round($v['money'],2);
            }
            
            $root['item']=$list?$list:array();
            
            $count=$data['count'];
            $page_total = ceil($count/$page_size);
            $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
            
        }
        
        $root['page_title']="资金明细";
        return output($root);
    }
}
