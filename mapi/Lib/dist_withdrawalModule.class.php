<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class dist_withdrawalApiModule extends MainBaseApiModule
{

    /**
     * 	提现列表
     *
     * 	 输入:
     *  page    [int] 分页
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  
     *  有权限的情况下返回以下内容
     *  
     *[supplier_info] => Array      ：array 商户信息
        (
            [id] => 35              ：int 商户编号
            [name] => 美丽人生摄影工作室      ：string 商户名称
            [money] => 191          ：float 允许提现总金额
            [bank_name] => 工商银行福州分行     ：绑定的银行
            [bank_info] => 尾号0794   ：绑定的银行卡号信息
        )

        [item] => Array     ：array  提现的历史记录
        (
            [0] => Array
                (
                    [create_time] => 1433812556         ：string 提现日期
                    [money] => 1                ：float  提现金额
                    [status] => 待审核     ：string 提现申请状态
                    [f_create_time] => 2015-06-09       ：string 格式化提现日期
                )

        )
      

     */
	public function index(){
	    
	    /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];

        /*获取参数*/
        $page = intval($GLOBALS['request']['page']); //分页
        $page=$page==0?1:$page;
        
	    /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"商户未登录");
        }

	    
	    $supplier_info=$GLOBALS['db']->getRow("select id,name,money,bank_name,bank_info from  ".DB_PREFIX."supplier where id=".$supplier_id);

	    if($supplier_info['bank_info']){
	        $bank_info_str = str_replace(" ", "", $supplier_info['bank_info']);
	        $supplier_info['bank_info'] = "尾号".substr($bank_info_str, -4);
	    }else{
	        $supplier_info['bank_info'] = '';
	    }
	    $root['is_band_bank'] = $supplier_info['bank_info']?1:0;
	    
	    $supplier_info['money'] = number_format($supplier_info['money'],2);
	    
	    
	    $root['supplier_info'] = $supplier_info;
	    //分页
	    $page_size = PAGE_SIZE;
	    $limit = (($page-1)*$page_size).",".$page_size;
	    
	    $list = $GLOBALS['db']->getAll("select create_time,money,status,reason from ".DB_PREFIX."supplier_money_submit  where supplier_id=".$supplier_id." order by status asc,create_time desc limit ".$limit);
	    foreach($list as $k=>$v){
			if($v['status']==1){
				$list[$k]['status']="已确认提现";
			}else if($v['status']==2)
			{
				$list[$k]['status']="已拒绝";
				if($v['reason'])
					$list[$k]['status'].=":".$v['reason'];
			}
			else{
				$list[$k]['status']="待审核";
			}
			$list[$k]['f_create_time']= to_date($v['create_time'],'Y-m-d');
			$list[$k]['money'] = number_format($v['money'],2);
			
	    }
	    
	    $count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_money_submit  where supplier_id=".$supplier_id);

        
	    //分页
	    $page_total = ceil($count/$page_size);
	    

	    $root['item'] = $list?$list:array();
	    $root['page_title'] = "商户提现";
	    $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        
        
        return output($root);
    }
    
    
    
    /**
     * 	提现表单页面
     *
     * 	 输入:
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
     *  [supplier_id] => 12           : int 商家id
     *  [money]  => 1000.00           : float 可提现金额
     *  [bank_user]  => 方维                            : string 开户行户名
     *  [bank_info]  => 建行 尾号5234    : string 银行卡信息
     *  [is_bank]    => 1             : int 银行卡信息是否完善
     *  [mobile] => 13677890998       ：string 商户的验证手机号
     *  [sms_on] => 0                 ：短信开启和关闭的状态   0 关闭  ，1开启
    
     */
    public function submit_form(){
        /*初始化*/
        $root = array();

        /*获取参数*/
        $dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=". $GLOBALS['dist_info']['id']);
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        
        $mobile = $dist_info['tel'];

        if($mobile == ''){
            return output($root,0,"还没有绑定手机号，请绑定手机号！");
        }
        
        
        if($dist_info['bank_card'] && $dist_info['bank_name'] && $dist_info['bank_user']){
            $root['is_bank']=1;
        }
        
        $submit_money=floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."distribution_money_submit where distribution_id = ".$dist_info['id']." and status=0"));
        
        $money=floatval($dist_info['money'])-$submit_money;

        if($money>0){
            $root['money']=number_format($money,2);
        }else{
            $root['money']=0;
        }
        
        $dist_info['money'] = round($dist_info['money'],2);
        $root['dist_info'] = $dist_info;
        $root['bank_user']=$dist_info['bank_user']."&nbsp;尾号".substr($dist_info["bank_card"], -4);
        $root['bank_info']=$dist_info['bank_name'];
        
        $root['mobile'] = $mobile;
        $root['sms_on'] = app_conf("SMS_ON");
        
        $root['page_title'] = "提现";
        //print_r($root);exit;
        return output($root);
    }
    
    
    /**
     * 	提现提交
     *
     * 	 输入:
     *  money    [float] 提现金额
     *  
     *  没开启短信时候传密码/其中一个必填
     *  sms_verify  [string] 短信验证码
     *  pwd_verify     [string] 密码 
     *  
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *
     *  有权限的情况下返回以下内容
     *
     *  
    
    
     */
    public function do_submit(){
        /*初始化*/
        $root = array();
        /*获取参数*/
        $dist_info = $GLOBALS['dist_info'];
        /*获取参数*/
        $money = floatval($GLOBALS['request']['money']);
        $sms_verify = strim($GLOBALS['request']['sms_verify']);
        $account_password = strim($GLOBALS['request']['pwd_verify']);
        
        
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站用户未登录");
        }
        
        
        $mobile = $dist_info['tel'];

        if($dist_info['bank_card'] == '' || $dist_info['bank_name'] == '' || $dist_info['bank_user'] == ''){
            return output($root,0,"还没有绑定银行卡，请绑定银行卡！");
        }
        
        if($mobile == ''){
            return output($root,0,"还没有绑定手机号，请绑定手机！");
        }
    
        
        if($account_password == ''){
            return output($root,0,"请输入密码");
        }
        if(md5($account_password)!=$dist_info['password']){
            return output($root,0,"密码不正确");
        }

        if($money<=0)
        {
            return output($root,0,"请输入正确的提现金额");
        }
        
        $submitted_money=floatval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."distribution_money_submit where distribution_id = ".$dist_info['id']." and status=0"));
        if($submitted_money+$money>$dist_info['money'])
        {
            return output($root,0,"提现超额");
        }
        
        $withdraw_data = array();
        $withdraw_data['distribution_id'] = $dist_info['id'];
        $withdraw_data['bank_name'] = strim($GLOBALS['request']['bank_name']);
        $withdraw_data['bank_card'] = strim($GLOBALS['request']['bank_card']);
        $withdraw_data['bank_user'] = strim($GLOBALS['request']['bank_user']);
        $withdraw_data['money'] = $money;
        $withdraw_data['create_time'] = NOW_TIME;
        $withdraw_data['status'] = 0;
        
        $GLOBALS['db']->autoExecute(DB_PREFIX."distribution_money_submit",$withdraw_data);

        
        $GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");
        
        return output($root,1,"提现申请提交成功，请等待管理员审核");
    }
        
    /**
     * 绑定银行卡页面接口
     * 
     * 输入：
     * 
     * 输出：
     * biz_user_status [int] 商户登录状态
     * is_auth  [int] 是否有权限操作这个接口
     * 
     * 有权限返回以下
     * sms_on [int] 短信是否开启
     * mobile [string] 短信发送的手机号
     * 
     * 已绑定银行卡的商户输出银行卡信息
     * [supplier_info] =>array(
     *      [id]          => 1            :int      商户id
     *      [bank_user]   => 方维                         :string   开户行户名
     *      [bank_info]   => 52133425243  :int      银行卡号
     *      [bank_name]   => 中国银行                  :string   开户行名称
     * ) 
     */
    public function bindbank(){
        /*初始化*/
        $root = array();
        /*获取参数*/
        $dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=". $GLOBALS['dist_info']['id']);
        
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"用户未登录");
        }

        $mobile = $dist_info['tel'];
        
        if($mobile == ''){
            return output($root,0,"还没有绑定手机号，请绑定银行卡！");
        }
        
        
        $root['dist_info']=$dist_info?$dist_info:array();
        
        //短信是否打开
        $root['sms_on'] = app_conf("SMS_ON");
        $root['mobile'] = $mobile;
        $root['bank_info']['bank_name'] =strim($dist_info['bank_name']);
        $root['bank_info']['bank_card'] =strim($dist_info['bank_card']);
        $root['bank_info']['bank_user'] =strim($dist_info['bank_user']);
        if($dist_info['bank_name']&&$dist_info['bank_card']&&$dist_info['bank_user']){
            $root['page_title'] = "修改银行卡";
        }    
        else{
            $root['page_title'] = "绑定银行卡";
        }   
        return output($root);
        
    }
    
    /**
     * 绑定银行卡提交接口
     *
     * 输入：
     * bank_name    ：string 开户行
     * bank_num    ：string 卡号
     * bank_user    ：string 持卡人
     * sms_verify    ：string 短信验证码
     * pwd_verify    ：string 登录密码
     * 
     * 输出：          
     * biz_user_status [int] 商户登录状态
     * is_auth  [int] 是否有权限操作这个接口
     *
     * status   状态
     * info     错误消息
     */
    public function do_bindbank(){
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];
    
        /*获取参数*/
        $bank_name = strim($GLOBALS['request']['bank_name']);
        $bank_num = strim($GLOBALS['request']['bank_num']);
        $bank_user = strim($GLOBALS['request']['bank_user']);
        $sms_verify = strim($GLOBALS['request']['sms_verify']);
        //$pwd_verify = strim($GLOBALS['request']['pwd_verify']);
    
    
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"商户未登录");
        }

    
            $mobile_phone=$dist_info['tel'];
            if($sms_verify == ''){
                return output($root,0,"请输入手机验证码");
            }
            $sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
            $GLOBALS['db']->query($sql);
             
            $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
            if($mobile_data['code']!=$sms_verify)
            {
                return output($root,0,"手机验证码错误");
            }
    
        
        $updata = array();
        $updata['bank_name'] = $bank_name;
        $updata['bank_card'] = $bank_num;
        $updata['bank_user'] = $bank_user;
    
    
        $GLOBALS['db']->autoExecute(DB_PREFIX."distribution",$updata,'UPDATE',"id = ".$dist_info['id']);
        load_dist();
        $GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
    
        return output($root,1,"银行卡绑定成功");
    }
    
    
    /**
     * 	提现明细
     *  输入：
     *  page [int] 分页所在页数
     * 
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     [item] => Array
        (
            [0] => Array
                (
                    [id] => 1
                    [money] => 12.0000     ：金额
                    [supplier_id] => 23    ：商家ID
                    [create_time] => 2017-01-11 14:10   ：时间
                    [status] => 提现成功
                    [name] => 桥亭活鱼小镇
                    [bank_name] => 中国建设银行福建省分行
                    [bank_user] => 张三
                    [bank_info] => 尾号4771
                )

        )
    
     [page_title] => 提现明细
     */
    public function withdraw_log(){
        $root = array();
        /*获取参数*/
        $dist_info = $GLOBALS['dist_info'];
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page=$page==0?1:$page;
         
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
    
        $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."distribution_money_submit where distribution_id=".$dist_info['id']." order by create_time desc limit ".$limit);
        foreach($list as $k=>$v){
            $list[$k]['money'] = round($v['money'],2);
            $list[$k]['bank_info'] = "&nbsp;尾号".substr($v['bank_card'], -4);
            $list[$k]['create_time'] = to_date($v['create_time'],"Y-m-d H:i");
            if(!$v['reason']){
                unset($list[$k]['reason']);
            }
            if($v['status']==1){
                $list[$k]['info']="提现成功";
            }else if($v['status']==2){
                $list[$k]['info']="已拒绝";
                if($v['reason']){
                    $list[$k]['info'].=":".$v['reason'];
                }
            }
            else
            {
                $list[$k]['info']="待审核";
            }
        }
        $root['item'] = $list;
    
        $count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution_money_submit  where distribution_id=".$dist_info['id']);
        //分页
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        //print_r($root);exit;
        $root['page_title'].="提现明细";
        return output($root);
    }
    
    
    /**
     * 	资金明细
     *      
     *  输入：
     *  page [int] 分页所在页数
     * 
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
    [item] => Array
        (
            [0] => Array
                (
                    [log_info] => ID:83 骥龙免洗宝宝饭兜 订单：2017011105033030   ：资金变更记录名称
                    [create_time] => 2017-01-11 17:03   ：时间
                    [money] => 9.0000     ：金额
                    [type] => 0   ：类型（0:销售额增加； 5：提现增加 ）
                )
        )
    
     [page_title] => 资金明细
    
     */
    public function money_log(){
    
        /*初始化*/
        $root = array();
        $dist_info = $GLOBALS['dist_info'];
        /*业务逻辑*/
        $root['dist_user_status'] = $dist_info?1:0;
        if (empty($dist_info)){
            return output($root,0,"驿站未登录");
        }
        $page = intval($GLOBALS['request']['page']);
        $page=$page==0?1:$page;
         
        $page_size = PAGE_SIZE;
        $limit = (($page-1)*$page_size).",".$page_size;
    
        $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."distribution_money_log where distribution_id=".$dist_info['id']." order by create_time desc limit ".$limit);
        $count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution_money_log  where distribution_id=".$dist_info['id']);
        //print_r($list);exit;
        foreach($list as $k=>$v){
            $list[$k]['money'] = number_format($v['money'],2);
            $list[$k]['create_time'] = to_date($v['create_time'],"Y-m-d H:i");
        }
        //print_r($list);exit;
        $root['item']=$list?$list:array();
    
        $page_total = ceil($count/$page_size);
        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
        $root['page_title']="资金明细";
        //print_r($root);exit;
        return output($root);
    }
    
}
?>

