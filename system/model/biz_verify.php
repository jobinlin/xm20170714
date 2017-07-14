<?php

/**
 * 验证所有类型消费券统一入口
 * @param array $account_info       登录商户信息
 * @param string $coupon_pwd        消费券序列号
 * @param string $location_id       门店ID
 * @param number $no_check_location 0验证门店，1不验证门店
 * 
 * @return array $result
 */
function biz_unified_check_coupon($account_info, $coupon_pwd, $location_id, $no_check_location=0){
    
    $type = substr($coupon_pwd, 0, 1);  // $type = 1团购 ，2优惠券，3活动 ，4自提, 1和4验证使用都是用一个方法
    
    // 判断位数
    if(!is_numeric($coupon_pwd) || strlen($coupon_pwd) != 12){
        $result = array('status'=>0, 'info'=>'您输入的券码无效');
        return $result;
    }
    $result['coupon_type'] = $type;
    switch ($type) {
        case 1:
            $check_result = biz_super_check_coupon($account_info, $coupon_pwd, $location_id, $no_check_location);
            
            if ($check_result['status'] == 1 ) {

                $result['coupon_data']['sub_name']      = $check_result['coupon_data']['sub_name'];
                $result['coupon_data']['unit_price']    = number_format($check_result['coupon_data']['unit_price'],2);
                $result['coupon_data']['begin_time']    = $check_result['coupon_data']['begin_time'];
                $result['coupon_data']['end_time']      = $check_result['coupon_data']['end_time'];
                $result['coupon_data']['number']        = $check_result['coupon_data']['number'];   // 总的券数（购买的数量）
                $result['coupon_data']['count']         = $check_result['count'];                   // 剩余有效券数
                $result['coupon_data']['coupon_pwd']    = $coupon_pwd;
            }
            
            break;
        
        case 2:
            $check_result = biz_check_youhui($account_info, $coupon_pwd, $location_id, $no_check_location);
            
            if ($check_result['status'] == 1 ) {
                
                $result['coupon_data']['name']          = $check_result['data']['name'];
                $result['coupon_data']['youhui_type']   = $check_result['data']['youhui_type'];   // 优惠券类型，1折扣券 ，0减免券
                $result['coupon_data']['youhui_value']  = $check_result['data']['youhui_value'];
                $result['coupon_data']['begin_time']    = to_date($check_result['data']['youhui_data']['begin_time'],'Y-m-d');
                $result['coupon_data']['end_time']      = to_date($check_result['data']['youhui_data']['end_time'],'Y-m-d');
                $result['coupon_data']['coupon_pwd']    = $coupon_pwd;
                 
            } 
             
            break;
            
        case 3:
            $check_result = biz_check_event($account_info, $coupon_pwd, $location_id, $no_check_location);
            
            if ($check_result['status'] == 1 ) {
                
                $result['coupon_data']['name']              = $check_result['event_data']['name'];
                $result['coupon_data']['city_id']           = $check_result['event_data']['city_id'];
                $result['coupon_data']['address']           = $check_result['event_data']['address'];
                $result['coupon_data']['event_begin_time']  = $check_result['event_data']['event_begin_time'];
                $result['coupon_data']['event_end_time']    = $check_result['event_data']['event_end_time'];
                $result['coupon_data']['user_id']           = $check_result['event_submit']['user_id'];
                $result['coupon_data']['coupon_pwd']        = $coupon_pwd;
                $result['coupon_data']['field_info']        = $check_result['field_info'];
                
                // 活动需要获取： 用户名 手机号
                $user_info = $GLOBALS['db']->getRow("select user_name, mobile from " . DB_PREFIX . "user where id={$result['coupon_data']['user_id']} ");
                $result['coupon_data']['user_name'] = $user_info['user_name'];
                $result['coupon_data']['mobile']    = $user_info['mobile'];
                
            }
            
            break;
        
        case 4:
            $check_result = biz_super_check_coupon($account_info, $coupon_pwd, $location_id, $no_check_location);
           
            if ( $check_result['status'] == 1 ) {
                 
                $result['coupon_data']['sub_name']      = $check_result['coupon_data']['sub_name'];
                $result['coupon_data']['unit_price']    = number_format($check_result['coupon_data']['unit_price'],2);
                $result['coupon_data']['begin_time']    = $check_result['coupon_data']['begin_time'];
                $result['coupon_data']['end_time']      = $check_result['coupon_data']['end_time'];
                $result['coupon_data']['number']        = $check_result['coupon_data']['number'];   // 总的券数（购买的数量）
                $result['coupon_data']['attr_str']      = $check_result['coupon_data']['attr_str'];
                $result['coupon_data']['count']         = $check_result['count'];                   // 剩余有效券数
                $result['coupon_data']['coupon_pwd']    = $coupon_pwd;
            }
            
            if ($check_result['status'] == 0) {
                $check_result['msg'] = str_replace('消费券', '自提券', $check_result['msg']);
            }
            
            break;
        case 5:
            require_once(APP_ROOT_PATH."system/model/dc.php");
            $check_result = biz_check_dcverify($account_info, $coupon_pwd, $location_id, $no_check_location);
             
            if ( $check_result['status'] == 1 ) {
                //logger::write(print_r($check_result,1));
                $result['coupon_data']['consignee']      = $check_result['consignee'];
                $result['coupon_data']['total_price']    = number_format($check_result['total_price'],2);
                $result['coupon_data']['mobile']    = $check_result['mobile'];
                $result['coupon_data']['table_name']      = $check_result['table_name'];
                $result['coupon_data']['location_name']      = $check_result['location_name'];
                $result['coupon_data']['dc_comment']        = $check_result['dc_comment'];   // 总的券数（购买的数量）
                $result['coupon_data']['table_time_format']      = $check_result['table_time_format'];
                $result['coupon_data']['menu_list']         = $check_result['menu_list'];                   // 剩余有效券数
                $result['coupon_data']['coupon_pwd']    = $coupon_pwd;
            }

            break;
            
        default:
			$check_result['status'] = 0;
			$check_result['msg'] = '您输入的券码无效';
            break;
    }
    
    
    $result['status']   = $check_result['status'];
    $result['info']     = $check_result['msg'];
    unset($check_result['msg']);
   
    return $result;
}



/**
 * 使用消费券统一入口
 * @param array $account_info       登录商户信息
 * @param string $coupon_pwd        消费券序列号
 * @param string $location_id       门店ID
 * @param number $coupon_use_count  要消费的券数量
 * 
 * @return array $result
 */
function biz_unified_use_coupon($account_info, $coupon_pwd, $location_id, $coupon_use_count=0){

    $type = substr($coupon_pwd, 0, 1);  // $type = 团购 1，优惠券2，活动 3，自提券：4
    
    switch ($type) {
        case 1: 
            $result = biz_super_use_coupon($account_info, $location_id, $coupon_pwd, $coupon_use_count);
            unset($result['send_data']);
            break;

        case 2:
            $result = biz_use_youhui($account_info, $coupon_pwd, $location_id);
            $result['coupon_pwd'] = $result['youhui_sn'];
            unset($result['youhui_sn']);
            unset($result['data']);
            break;

        case 3:
            $result = biz_use_event($account_info, $coupon_pwd, $location_id);
            $result['coupon_pwd'] = $result['event_sn'];
            unset($result['event_sn']);
            unset($result['event_data']);
            unset($result['event_submit']);
            break;

        case 4:
            $result = biz_super_use_coupon($account_info, $location_id, $coupon_pwd, $coupon_use_count);
            unset($result['send_data']);
            break;
            
        case 5:
            require_once(APP_ROOT_PATH."system/model/dc.php");
            $result = biz_use_dcverify($account_info, $coupon_pwd ,$location_id);
            unset($result['data']);
            break;
            

        default:
            $result = array('status'=>0, 'info'=>'序列号错误');
            break;
    }
    $result['coupon_type'] = $type;
    $result['info'] = $result['msg'];
    unset($result['msg']);

    return $result;
}

/**
 * 消费券单独验证
 * @param array $s_account_info 
 * @param string $pwd //验证码
 * @param int $location_id //门店编号
 * @return string|number
 */
function biz_check_coupon($s_account_info,$pwd,$location_id, $no_check_location=0)
{

		$now = NOW_TIME;

		$supplier_id = intval($s_account_info['supplier_id']);
		
		$sql = "select c.refund_status, c.user_id as user_id, c.id as id,c.deal_id, c.order_id,c.order_deal_id, c.is_valid,doi.sub_name, doi.name as name,doi.number as number, doi.unit_price as unit_price, doi.attr_str as attr_str, c.sn as sn ,c.password as password,c.supplier_id as supplier_id,c.confirm_time as confirm_time,c.deal_type,c.begin_time,c.end_time from ".DB_PREFIX."deal_coupon as c left join ".DB_PREFIX."deal_order_item as doi on c.order_deal_id = doi.id where  c.password = '".$pwd."' and c.is_valid in(1,2) and c.is_delete = 0  and c.begin_time <".$now." and (c.end_time = 0 or c.end_time>".$now.")";
		$coupon_data = $GLOBALS['db']->getRow($sql);
		 
		
		if($coupon_data['deal_type'] == 1){
		    $coupon_data['number'] = 1 ;
		}else{
		    //计算是否有用过的团购券,验证消费券时间大于零就是用过和已退款的
		    $use_nmuber_pwd = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."deal_coupon where ( confirm_time > 0 or ( refund_status in(1,2 )) ) and order_id = ".$coupon_data['order_id']);
		    //如果有就减去已用的
		    if($use_nmuber_pwd){
		        $coupon_data['number'] = $coupon_data['number'] - $use_nmuber_pwd;
		    }
		}
		if(empty($coupon_data)){
			$result['status'] = 0;
			$result['msg'] = "没有消费券数据";
			return $result;
		}
		
		// 查询是否有符合：密码正确，有效性 为 1:已发放给用户 2：退款被禁用',，没有过期，
		if($coupon_data)
		{
			if($coupon_data['confirm_time'] > 0)	//验证时间
			{
				$result['status'] = 0;
				$result['msg'] = "该券已于".to_date($coupon_data['confirm_time'])."使用过";
			
				return $result;
			}
			if($coupon_data['is_valid'] == 2){ //退款被禁用
				$result['status'] = 0;
				$result['msg'] = "该消费券已经失效无法验证";
				return $result;
			}

			if($coupon_data['refund_status'] == 1 || $coupon_data['refund_status'] == 2){	//退款状态 0:无  1:用户申请退款 2:已确认 3:拒绝，但是可以再使用
				$result['status'] = 0;
				$result['msg'] = "消费券提交了退款申请，无法验证";
				return $result;
			}
			
			
			if ( !in_array($location_id,$s_account_info['location_ids']) && $no_check_location==0 ){
			    $result['status'] = 0;
			    $result['msg'] = "没有门店权限验证该消费券";
			    return $result;
			}
			//查询门店对商品是否有权限
			if ($no_check_location == 1) {
			    $sql = "select d.* from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where l.deal_id = ".$coupon_data['deal_id'];
			}else{
			    $sql = "select d.* from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where l.deal_id = ".$coupon_data['deal_id']." and l.location_id =".$location_id;
			}
			
			$deal_info = $GLOBALS['db']->getRow($sql);
			
			if ( $no_check_location == 0 ) {
			
    			if(!$deal_info)		
    			{
    				$result['status'] = 0;
    				$result['msg'] = "没有门店权限验证该消费券";					
    				return $result;
    			}
    			if($coupon_data['supplier_id']!=$supplier_id)	//是否是该商户的商品
    			{
    			    $result['status'] = 0;
    			    $result['msg'] = "该券为其他团购商户的消费券，不能确认";
    			
    			    return $result;
    			}
			}
			
			
			if(!$coupon_data['name'])
				$coupon_data['name'] = $deal_info['name'];
			if(!$coupon_data['sub_name'])
				$coupon_data['sub_name'] = $deal_info['sub_name'];
			
			if($coupon_data['end_time']!=0){
			    $coupon_data['begin_time']=to_date($coupon_data['begin_time'],'Y-m-d');
			    $coupon_data['end_time']=to_date($coupon_data['end_time'],'Y-m-d');
			}else{
			    $coupon_data['end_time']='永久';
			}
			
			$result['status'] = 1;
			$result['coupon_data'] = $coupon_data;
			$result['location_id'] = $location_id;
			$result['coupon_pwd'] = $pwd;
			//'发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)',
			if($coupon_data['deal_type'] == 1) 
			{
				$result['msg'] = $coupon_data['name']."(购买数量：".$coupon_data['number']."), 有效";
				$result['sub_msg'] = $coupon_data['sub_name']."(购买数量：".$coupon_data['number']."), 有效";
				$result['number'] = $coupon_data['number'];
			}
			else
			{
				$result['msg'] = "名称：".$coupon_data['name']."<br/>有效期：".$coupon_data['end_time'].
				"<br/>消费券券码：".$coupon_data['password']."<br/>是否有效：有效！";
				$result['sub_msg'] = "名称：".$coupon_data['sub_name']."<br/>有效期：".$coupon_data['end_time'].
				"<br/>消费券券码：".$coupon_data['password']."<br/>是否有效：有效！";
				$result['number'] = 1;
			}
			return $result;
		}
}	
	
/**
 * 消费券单独使用
 * @param unknown_type $s_account_info
 * @param unknown_type $pwd
 * @param unknown_type $location_id
 * @return Ambigous <string, number, unknown, number>
 */
function biz_use_coupon($s_account_info,$pwd,$location_id)
{
	$result = biz_check_coupon($s_account_info,$pwd,$location_id);

	if ($result['status'] == 1){
		$coupon_data = $result['coupon_data'];
		$now = NOW_TIME;
		$supplier_id = intval($s_account_info['supplier_id']);
			
		//开始确认
		require_once(APP_ROOT_PATH."system/model/deal_order.php");
		use_coupon($pwd,$location_id,$s_account_info['id'],true,true);
		$deal_type = intval($GLOBALS['db']->getOne("select deal_type from ".DB_PREFIX."deal where id = ".intval($coupon_data['deal_id'])));
		if($deal_type == 1)
		{
			$result['msg'] = $coupon_data['name']."(购买数量：".$coupon_data['number'].")".sprintf($GLOBALS['lang']['COUPON_USED_OK'],to_date($now));
			$result['sub_msg'] = $coupon_data['sub_name']."(购买数量：".$coupon_data['number'].")".sprintf($GLOBALS['lang']['COUPON_USED_OK'],to_date($now));
		}
		else
		{
			$result['msg'] = $coupon_data['name'].sprintf($GLOBALS['lang']['COUPON_USED_OK'],to_date($now));;
			$result['sub_msg'] = $coupon_data['sub_name'].sprintf($GLOBALS['lang']['COUPON_USED_OK'],to_date($now));
		}
	}
	return $result;
}
/**
 * 消费券批量验证， 如果全部正确则直接使用
 * @param unknown_type $s_account_info
 * @param unknown_type $location_id
 * @param unknown_type $coupon_pwds
 * @return string
 */
function biz_check_coupon_batch($s_account_info,$location_id,$coupon_pwds)
{
	$now = NOW_TIME;
	$supplier_id = intval($s_account_info['supplier_id']);

	$check_data = array();	//检查已经验证过的数据
	$result_data = array();	//对应PWD 的验证过的数据
	$count_err = 0;
	foreach($coupon_pwds as $k=>$v){
		$is_err = 0;
		$pwd=$v;
		if(empty($pwd)){//为空的
			$result[$k]['msg'] = "";
			$result[$k]['status']=-1;
			continue;
		}

		if(!is_numeric($pwd)){
			$result[$k] = $result_data[$pwd];
			$result[$k]['msg'] = "验证码必须为数字";
			$result[$k]['status']=0;
			$is_err=1;
		}
		if(in_array($pwd, $check_data)){ //如果有重复数据
			$result[$k] = $result_data[$pwd];
			$result[$k]['msg'] = "重复的验证码";
			$result[$k]['status']=0;
			$is_err=1;
			$count_err++;
			continue;
		}
		if($is_err==0){
			$coupon_data = $GLOBALS['db']->getRow("select c.refund_status,c.begin_time,c.end_time, c.id as id,c.is_valid,c.deal_id,doi.name as name,doi.sub_name as sub_name,c.password as password,doi.number as number,c.sn as sn,c.supplier_id as supplier_id,c.confirm_time as confirm_time from ".DB_PREFIX."deal_coupon as c left join ".DB_PREFIX."deal_order_item as doi on c.order_deal_id = doi.id where c.password = '".$pwd."' and c.is_valid in(1,2) and c.is_delete = 0 ");
		    
		}
		if($coupon_data)
		{
			if($coupon_data['confirm_time'] > 0  && $is_err ==0)
			{
				$result[$k]['msg'] = sprintf($GLOBALS['lang']['COUPON_INVALID_USED'],to_date($coupon_data['confirm_time']));
				$result[$k]['status']=0;
				$is_err=1;
			}
			if($coupon_data['is_valid'] == 2 && $is_err ==0){//改团购劵因为退款被锁定
				$result[$k]['msg'] = "该消费券已经失效无法验证";
				$result[$k]['status']=0;
				$is_err=1;
			}
				
			if($coupon_data['refund_status'] > 0 && $coupon_data['refund_status'] !=3){
				$result[$k]['msg'] = "消费券提交了退款申请，无法验证";
				$result[$k]['status']=0;
				$is_err=1;
			}
				
			if($coupon_data['begin_time']>0&&$coupon_data['begin_time']>get_gmtime()  && $is_err ==0){//未启用
				$result[$k]['msg'] = "消费券未生效";
				$result[$k]['status']=0;
				$is_err=1;
			}
			if($coupon_data['end_time']>0&&$coupon_data['end_time']<get_gmtime()  && $is_err ==0){//过期
				$result[$k]['msg'] = "消费券已过期";
				$result[$k]['status']=0;
				$is_err=1;
			}
			if (!in_array($location_id,$s_account_info['location_ids'])){
			    $result[$k]['msg'] = "没有门店权限验证该消费券";
			    $result[$k]['status']=0;
			    $is_err=1;
			}	
			$sql = "select d.* from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where l.deal_id = ".$coupon_data['deal_id']." and l.location_id =".$location_id;
			$deal_info = $GLOBALS['db']->getRow($sql);
			if(!$deal_info && $is_err ==0)
			{
				$result[$k]['msg'] = $GLOBALS['lang']['NO_AUTH'];
				$result[$k]['status']=0;
				$is_err=1;
			}
				
			if($coupon_data['supplier_id']!=$supplier_id  && $is_err ==0)
			{
				$result[$k]['msg'] = "该券为其他团购商户的消费券，不能确认";
				$result[$k]['status']=0;
				$is_err=1;
			}
			if($is_err==0){
				//开始确认
				$result[$k]['msg']= '验证成功,';
				$result[$k]['id'] = $coupon_data['id'];
				$result[$k]['name'] = $coupon_data['name'];
				$result[$k]['sub_name'] = $coupon_data['sub_name'];
				$result[$k]['password'] = $coupon_data['password'];
				$result[$k]['status']=1;
			}
		}
		else
		{
			$result[$k]['msg'] = '验证失败';//$GLOBALS['lang']['COUPON_INVALID'];
			$result[$k]['status']=0;
			$is_err=1;
		}


		$coupon_data['password'] = $coupon_data['password']?$coupon_data['password']:$pwd;
		$result[$k]['id'] = $coupon_data['id'];
		$result[$k]['name'] = $coupon_data['name'];
		$result[$k]['sub_name'] = $coupon_data['sub_name'];
		$result[$k]['password'] = $coupon_data['password'];


		//存放已经验证过的数据
		$check_data[] = $coupon_data['password'];
		//根据密码数据对应
		$result_data[$coupon_data['password']] = $result[$k];
		if($is_err)$count_err++;
	}
	if($count_err > 0) //如果有错误
		$data['is_err']=1;
	else
		$data['is_err']=0;

	if($count_err==0 && $result_data){ //如果都没有错误执行验证
		foreach($result as $k=>$v){
			//开始确认
			require_once(APP_ROOT_PATH."system/model/deal_order.php");
			$result[$k]['send_status'] = use_coupon($v['password'],$location_id,$s_account_info['id'],true,true);
			$result[$k]['msg'] = "使用成功，使用时间为：".to_date($now);
		}

	}
	
	$data['data'] = $result;

	return $data;
}	
	

/**
 * 消费券超级验证，返回可用条数
 * @param unknown_type $s_account_info
 * @param unknown_type $pwd
 * @param unknown_type $location_id
 * @return Ambigous <string, number, unknown, number>
 */
function biz_super_check_coupon($s_account_info,$pwd,$location_id, $no_check_location=0){
	//判断密码是否有效
	$result = biz_check_coupon($s_account_info,$pwd,$location_id, $no_check_location);
	
	$now = NOW_TIME;
	if($result["status"]==1){//有效数据
        $coupon_data = $result['coupon_data'];
        $supplier_id = intval($s_account_info['supplier_id']); //商户编号
        
        //查询该密码下所有同一订单 和 同一商品的消费券数量
        $result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon as c left join ".DB_PREFIX."deal_order_item as doi on c.order_deal_id = doi.id where c.deal_id=".$coupon_data['deal_id']." and c.order_id=".$coupon_data['order_id']." and c.order_deal_id = ".$coupon_data['order_deal_id']." and c.is_valid = 1 and c.refund_status in(0,3) and c.is_delete = 0 and c.confirm_time='' and c.begin_time <".$now." and (c.end_time = 0 or c.end_time>'".$now."')");
	}
	return $result;
	
}

/**
 * 团购全超级验证的使用， 使用全的数量= 当前输入的券+随机取出的券
 * @param unknown_type $s_account_info
 * @param unknown_type $location_id
 * @param unknown_type $pwd
 * @param unknown_type $coupon_use_count
 * @return boolean
 */
function biz_super_use_coupon($s_account_info,$location_id,$pwd,$coupon_use_count){
    $use_count_success = 0;
	$now = NOW_TIME;
	require_once(APP_ROOT_PATH."system/model/biz_verify.php");
	$s_account_info = es_session::get("account_info");
	
	//$location_id = intval($_REQUEST['location_id']);
	//$pwd = strim($_REQUEST['coupon_pwd']);
	$result = biz_super_check_coupon($s_account_info,$pwd,$location_id);
	 
	if($result['status']==0){
	    $data['status'] = 0;
	    $data['msg'] = $result['msg'];
	    return $data ;
	}
	if($result['count'] == 0){
		$data['status'] = 0;
		$data['msg'] = "没有可以使用的消费券";
		ajax_return($data);
	}
	if(!$coupon_use_count >=1){
	    $data['status'] = 0;
	    $data['msg'] = "至少使用1条";
	    ajax_return($data);
	}
	if($coupon_use_count>$result['count']){
		$data['status'] = 0;
		$data['msg'] = "超出使用条数";
		ajax_return($data);
	}
	
	$coupon_pwd_list = $GLOBALS['db']->getAll("select c.password as password from ".DB_PREFIX."deal_coupon as c  where c.deal_id=".$result['coupon_data']['deal_id']." and c.order_id=".$result['coupon_data']['order_id']." and c.order_deal_id = ".$result['coupon_data']['order_deal_id']."  and c.is_valid = 1 and c.refund_status in(0,3) and c.is_delete = 0 and c.confirm_time='' and c.begin_time <".$now." and (c.end_time = 0 or c.end_time>'".$now."')");
	$f_coupon_pwd_list = array();
	require_once(APP_ROOT_PATH."system/model/deal_order.php");
	foreach ($coupon_pwd_list as $k=>$v){
	    if ($pwd == $v['password']){
	       unset($coupon_pwd_list[$k]);   
	    }else
		$f_coupon_pwd_list[] = $v['password'];
	}
	
	 
	array_unshift($f_coupon_pwd_list,$pwd);


    for ($i=0;$i<$coupon_use_count;$i++){
        $v = $f_coupon_pwd_list[$i];
        $temp['pwd'] =$v;
        $temp['send_status'] = use_coupon($v,$location_id,$s_account_info['id'],true,true);
        if($temp['send_status']){
            $use_count_success++;
        }
        $send_log[] = $temp;
    }
    

	//已经成功执行
	$data['status'] = 1;
	$data['send_data'] = $send_log;
	$data['location_id'] = $location_id;
	$data['coupon_pwd'] = $pwd;
	$data['coupon_use_count'] = $use_count_success;
	$data['name']  = $result['coupon_data']['sub_name'];
	
	
	// 插入消费券日志表
	if ($use_count_success >= 1) {
	    $type = substr($pwd, 0, 1);
	    $now_time = NOW_TIME; 
        $GLOBALS['db']->query( "INSERT INTO `".DB_PREFIX."use_coupon_log` (`uid`, `data_id`, `name`, `pwd`, `type`, `create_time`, `location_id`) VALUES ( '{$result['coupon_data']['user_id']}', '{$result['coupon_data']['order_id']}', '{$result['coupon_data']['sub_name']}', '{$pwd}', '{$type}', '{$now_time}', '{$location_id}')" );
	}
	
    if($use_count_success>1){
        $data['msg'] = "核销成功";
    }elseif($use_count_success==1){
        // 消费金额
        //$coupon_price = $GLOBALS['db']->getOne("select coupon_price from ".DB_PREFIX."deal_coupon as c  where c.deal_id=".$result['coupon_data']['deal_id']);
        $data['msg'] = '核销成功';
    }else
        $data['msg'] = "使用失败";
	return $data;
}
	

/**************************************************************
 * 				                            优惠券验证代码
 **************************************************************/
function biz_check_youhui($s_account_info,$sn,$location_id, $no_check_location=0)
{
	if(intval($s_account_info['id'])==0)
	{
		$result['status'] = 0;
		$result['msg'] = $GLOBALS['lang']['SUPPLIER_NOT_LOGIN'];
		return $result;
	}

	$now = NOW_TIME;
	
	
	 
	
	    
	$youhui_log = $GLOBALS['db']->getRow("select y.name,y.youhui_type,y.youhui_value,y.begin_time,y.end_time,yl.* from ".DB_PREFIX."youhui as y left join ".DB_PREFIX."youhui_log as yl on y.id=yl.youhui_id where yl.youhui_sn = '".$sn."'");
	
	if($youhui_log)
	{
	    if (!in_array($location_id,$s_account_info['location_ids']) && $no_check_location==0){
	        $result['status'] = 0;
			$result['msg'] = "没有门店权限验证该优惠券";
			return $result;
	    }
		$sql = "select y.* from ".DB_PREFIX."youhui as y left join ".DB_PREFIX."youhui_location_link as l on l.youhui_id = y.id where l.youhui_id = ".$youhui_log['youhui_id']." and l.location_id =".$location_id;
		$youhui_info = $GLOBALS['db']->getRow($sql);
		if(!$youhui_info && $no_check_location == 0)
		{
			$result['status'] = 0;
			$result['msg'] = $GLOBALS['lang']['NO_AUTH'];
			return $result;
		}
		if($youhui_log['expire_time']>0 && $youhui_log['expire_time'] < $now){
		    $result['status'] = 0;
		    $result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_END'],to_date($youhui_log['expire_time']));
		    return $result;
		}
		if($youhui_log['confirm_id']>0&&$youhui_log['confirm_time']>0)
		{
			$result['status'] = 0;
			$result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_USED'],to_date($youhui_log['confirm_time']));
			return $result;
		}
		else
		{
			$youhui_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui where id = ".$youhui_log['youhui_id']);
			if($youhui_data)
			{
				if($youhui_data['begin_time']>0&&$youhui_data['begin_time']>$now)
				{
					$result['status'] = 0;
					$result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_NOT_BEGIN'],to_date($youhui_data['begin_time']));
					return $result;
				}
				else
				{
					$result['status'] = 1;
					$youhui_log['youhui_data'] = $youhui_data;
					$result['data'] = $youhui_log;
					$result['location_id'] = $location_id;
					$result['youhui_sn'] = $sn;
					/* $result['msg'] = $youhui_data['name']."[".$GLOBALS['lang']['YOUHUI_SN'].":".$youhui_log['youhui_sn']."]".$GLOBALS['lang']['IS_VALID_YOUHUI'];
					if($youhui_log['order_count']>0)
						$result['msg'].="\n".$GLOBALS['lang']['YOUHUI_ORDER_COUNT'].":".$youhui_log['order_count'].$GLOBALS['lang']['ORDER_COUNT_PERSON'];
					if($youhui_log['is_private_room'])
						$result['msg'].="(".$GLOBALS['lang']['IS_PRIVATE_ROOM'].")";
					if($youhui_log['date_time']>0)
						$result['msg'].="\n".$GLOBALS['lang']['ORDER_DATE_TIME'].":".to_date($youhui_log['date_time'],"Y-m-d H:i");
					$result['msg'].="\n".$GLOBALS['lang']['CONFIRM_USE_YOUHUI']; */
					if($youhui_log['expire_time']!=0){
					    $youhui_log['expire_time']=to_date($youhui_log['expire_time'],'Y-m-d');
					}else{
					    $youhui_log['expire_time']='永久';
					}
					
					$result['msg']="名称：".$youhui_log['name']."<br/>有效期：".$youhui_log['expire_time']."<br/>减免：".round($youhui_log['youhui_value'],2)."元"
					."<br/>优惠券券码：".$youhui_log['youhui_sn']."<br/>是否有效：有效！";
				}
			}
			else
			{
				$result['status'] = 0;
				$result['msg'] = $GLOBALS['lang']['YOUHUI_INVALID'];
			}
		}
	}
	else
	{
		$result['status'] = 0;
		$result['msg'] = $GLOBALS['lang']['YOUHUI_SN_INVALID'];
	}
	
	return $result;
}

function biz_use_youhui($s_account_info,$sn,$location_id)
{
	if(intval($s_account_info['id'])==0)
	{
		$result['status'] = 0;
		$result['msg'] = $GLOBALS['lang']['SUPPLIER_LOGIN_FIRST'];
	}
	else
	{
		$now = NOW_TIME;
			
		$youhui_log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_log where youhui_sn = '".$sn."'");
		if($youhui_log)
		{
			$sql = "select y.* from ".DB_PREFIX."youhui as y left join ".DB_PREFIX."youhui_location_link as l on l.youhui_id = y.id where l.youhui_id = ".$youhui_log['youhui_id']." and l.location_id =".$location_id;
			$youhui_info = $GLOBALS['db']->getRow($sql);
			if(!$youhui_info)
			{
				$result['status'] = 0;
				$result['msg'] = $GLOBALS['lang']['NO_AUTH'];
				//ajax_return($result);
				return $result;
			}
			if($youhui_log['confirm_id']>0&&$youhui_log['confirm_time']>0)
			{
				$result['status'] = 0;
				$result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_USED'],to_date($youhui_log['confirm_time']));
			}
			else
			{
				$youhui_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui where id = ".$youhui_log['youhui_id']);
				if($youhui_data)
				{
					if($youhui_data['begin_time']>0&&$youhui_data['begin_time']>$now)
					{
						$result['status'] = 0;
						$result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_NOT_BEGIN'],to_date($youhui_data['begin_time']));
					}
					elseif($youhui_data['end_time']>0&&$youhui_data['end_time']<$now)
					{
						$result['status'] = 0;
						$result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_END'],to_date($youhui_data['end_time']));
					}
					else
					{
					    
					    
					    
						$youhui_log['confirm_id'] = $s_account_info['id'];
						$youhui_log['confirm_time'] = $now;
						require_once(APP_ROOT_PATH.'system/model/deal_order.php');
						$youhui_log['send_status'] = use_youhui($sn,$location_id,$s_account_info['id'],true,true);
						$result['status'] = 1;
						$result['location_id'] = $location_id;
						$result['youhui_sn'] = $sn;
						$result['name'] = $youhui_info['name'];
						$result['msg'] = '核销成功';
						$youhui_log['youhui_data'] = $youhui_data;
						$result['data'] = $youhui_log;
						
						// 插入消费券日志表
					    $now_time = NOW_TIME;
					    $GLOBALS['db']->query( "INSERT INTO `".DB_PREFIX."use_coupon_log` (`uid`, `data_id`, `name`, `pwd`, `type`, `create_time`, `location_id`) VALUES ( '{$youhui_log['user_id']}', '{$youhui_log['id']}', '{$youhui_info['name']}', '{$sn}', '2', '{$now_time}', '{$location_id}')" );
						
					}
				}
				else
				{
					$result['status'] = 0;
					$result['msg'] = $GLOBALS['lang']['YOUHUI_INVALID'];
				}
			}
		}
		else
		{
			$result['status'] = 0;
			$result['msg'] = $GLOBALS['lang']['YOUHUI_SN_INVALID'];
		}
	}
	return $result;
}


/**
 * 上线电子劵的使用
 * @param unknown $sn
 */
function online_youhui_use($id)
{

        $now = NOW_TIME;
        	
        $youhui_log = $GLOBALS['db']->getRow("select yl.*,y.name,y.supplier_id,y.begin_time,y.end_time from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as y on yl.youhui_id=y.id  where yl.id = ".$id);
        if($youhui_log)
        {
            if($youhui_log['confirm_time']>0)
            {
                $result['status'] = 0;
                $result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_USED'],to_date($youhui_log['confirm_time']));
            }
            else
            {

                    if($youhui_log['begin_time']>0&&$youhui_log['begin_time']>$now)
                    {
                        $result['status'] = 0;
                        $result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_NOT_BEGIN'],to_date($youhui_log['begin_time']));
                    }
                    elseif($youhui_log['end_time']>0&&$youhui_log['end_time']<$now)
                    {
                        $result['status'] = 0;
                        $result['msg'] = sprintf($GLOBALS['lang']['YOUHUI_HAS_END'],to_date($youhui_log['end_time']));
                    }
                    else
                    {
                      	
                        $youhui_log['confirm_id'] = $youhui_log['supplier_id'];
                        $youhui_log['confirm_time'] = $now;
                        
                        $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_log", $youhui_log,"UPDATE",'id='.$youhui_log['id']);

                        $youhui_data=array();
                        $youhui_data['uid'] = $youhui_log['user_id'];
                        $youhui_data['data_id'] = $youhui_log['id'];
                        $youhui_data['name'] = $youhui_log['name'];
                        $youhui_data['pwd'] = $youhui_log['youhui_sn'];
                        $youhui_data['type'] = 2;
                        $youhui_data['create_time'] = $now;
                        // 插入消费券日志表
                        $GLOBALS['db']->autoExecute(DB_PREFIX."use_coupon_log", $youhui_data);

                        $result['status'] = 1;
                        $result['msg'] = '核销成功';
                        
                    }
    
            }
        }
        else
        {
            $result['status'] = 0;
            $result['msg'] = $GLOBALS['lang']['YOUHUI_SN_INVALID'];
        }
    
    return $result;
}
	
/*********************************************************
 *                  活动报名验证
 ********************************************************/

/**
 * 活动验证
 * @param unknown $s_account_info
 * @param unknown $sn
 * @param unknown $location_id
 * @return string|unknown
 */
function biz_check_event($s_account_info,$sn,$location_id, $no_check_location=0){
    $supplier_id = intval($s_account_info['supplier_id']);
    if($s_account_info['id']<=0){
        $result['status'] = 0;
        $result['msg'] = $GLOBALS['lang']['SUPPLIER_LOGIN_FIRST'];
    }else{
        //查询是否存在报名
        $event_submit = $GLOBALS['db']->getRow("SELECT e.name,es.* FROM ".DB_PREFIX."event as e left join ".DB_PREFIX."event_submit as es on e.id=es.event_id WHERE es.sn = '".$sn."' and (es.event_begin_time<=".NOW_TIME." or es.event_begin_time=0) and (es.event_end_time>".NOW_TIME." or es.event_end_time=0)");
        if($event_submit){
            $field_result  = $GLOBALS['db']->getAll("select f.field_show_name,r.result from ".DB_PREFIX."event_submit_field as r left join ".DB_PREFIX."event_field as f on f.id = r.field_id where r.submit_id = ".$event_submit['id']);
        }
        
        if(empty($event_submit)){
            $result['status'] = 0;
            $result['msg'] = "不存在活动报名信息或已经过期";
            return $result;
        }else{
            //是否审核
            if ($event_submit['is_verify'] == 0){
                $result['status'] = 0;
                $result['msg'] = "活动报名信息未审核";
                return $result;
            }
            if($event_submit['confirm_id']>0){
                $result['status'] = 0;
                $result['msg'] = "已经验证使用过";
                return $result;
            }
        }
        
        
        
        if (!in_array($location_id,$s_account_info['location_ids']) && $no_check_location==0 ){
            $result['status'] = 0;
            $result['msg'] =  "活动不支持该门店验证";
            return $result;
        }
        //查询报名的活动信息
        if ( $no_check_location==1 ) {
            $sql = "SELECT e.* FROM ".DB_PREFIX."event e left join ".DB_PREFIX."event_location_link el on el.event_id = e.id where id = ".$event_submit['event_id'];
        }else{
            $sql = "SELECT e.* FROM ".DB_PREFIX."event e left join ".DB_PREFIX."event_location_link el on el.event_id = e.id where id = ".$event_submit['event_id']." and el.location_id = ".$location_id;
        }
        
        $event = $GLOBALS['db']->getRow($sql);
        if(empty($event)){//门店关联查询，门店是否支持验证
            $result['status'] = 0;
            $result['msg'] = "活动不支持该门店验证";
            return $result;
        }
        if( ($event['supplier_id'] != $supplier_id) && $no_check_location == 0 ){//是否为该商户的活动
            $result['status'] = 0;
            $result['msg'] = "活动不是该商户的";
            return $result;
        }
        
        $result['status'] = 1;
        $result['location_id'] = $location_id;
        $result['event_sn'] = $sn;
        $result['msg'] = $event['name'];
        //报名信息
        $field_info = "";
        foreach($field_result as $k=>$v){
            $field_info.=$v['field_show_name'].":".$v['result']."<br/>";
        }
        $result['field_info']=$field_info;
        if($event['event_end_time']!=0){
            $event['event_begin_time']=to_date($event['event_begin_time'],'Y-m-d');
            $event['event_end_time']=to_date($event['event_end_time'],'Y-m-d');
        }else{
            $event['event_end_time']='永久';
        }
        $result['event_data'] = $event;
        if ($event['event_end_time']){
            $result['msg'].= "，该活动的结束时间为:".to_date($event['event_end_time']);
        }
        $result['event_submit'] = $event_submit;
        $result['msg']=$event_submit['name']."<br/>有效期：".$event_submit['end_time'].
				"<br/>活动券码：".$event_submit['sn']."<br/>是否有效：有效！";
    }
    return $result;
    
}
/**
 * 活动使用
 * @param unknown $s_account_info
 * @param unknown $sn
 * @param unknown $location_id
 * @return unknown
 */
function biz_use_event($s_account_info,$sn,$location_id){
    $supplier_id = intval($s_account_info['supplier_id']);
    $result = biz_check_event($s_account_info,$sn,$location_id);
    $event_submit = $result['event_submit'];
    if($result['status']){
        require_once(APP_ROOT_PATH.'system/model/deal_order.php');
        $result['send_status'] = use_event($sn,$location_id,$s_account_info['id'],true,true);
        $result['status'] = 1;
        $result['location_id'] = $location_id;
        $result['event_sn'] = $sn;
        $result['name'] = $result['event_data']['name'];
        $result['msg'] = '核销成功';
        
        // 插入消费券日志表
        $now_time = NOW_TIME;
        $GLOBALS['db']->query( "INSERT INTO `".DB_PREFIX."use_coupon_log` (`uid`, `data_id`, `name`, `pwd`, `type`, `create_time`, `location_id`) VALUES ( '{$result['event_submit']['user_id']}', '{$result['event_submit']['id']}', '{$result['event_data']['name']}', '{$sn}', '3', '{$now_time}', '{$location_id}')" );
        
        
    }
    return $result;
}
?>