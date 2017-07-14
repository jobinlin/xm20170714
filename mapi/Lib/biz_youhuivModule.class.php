<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_youhuivApiModule extends MainBaseApiModule
{

    /**
     * 	 优惠券验证接口
     *
     * 	 输入:
     *  无
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
        [location_list] => Array [array] 支持的门店数据
        (
            [0] => Array
                (
                    [id] => 21  [int] 门店编号
                    [name] => 桥亭活鱼小镇（万象城店） [string]门店名称
                )
        )

     */
	public function index(){
	    /*初始化*/
        $root = array();
        $account_info = $GLOBALS['account_info'];
        $root['page_title'] = "优惠券验证";
	    
	    /*业务逻辑*/
        $root['biz_user_status'] = $account_info?1:0;
        if (empty($account_info)){
            return output($root,0,"商户未登录");
        }
  
	    //返回商户权限
	    if(!check_module_auth("youhuiv")){
	        $root['is_auth'] = 0;
	        return output($root,0,"没有操作验证权限");
	    }else{
	        $root['is_auth'] = 1;
	    }
	    
	    
	    //获取支持的门店
	    $location_list = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $account_info['location_ids']) . ")");
        $root['location_list'] = $location_list?$location_list:'{}';
        
        
        return output($root);
    }
    
    
    /**
     * 	 优惠券提交验证接口
     *
     * 	 输入:
     *  location_id:string 门店ID
     *  youhui_sn:string 
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     *
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  如果status 为 1 的情况下
     *  [data] => Array 
        (
            [location_id] => 34 ['int'] 门店编号
            [youhui_sn] => 62376366    [string] 验证密码
        )
    
     */
    public function check_youhui(){
        /*初始化*/
        $root = array();
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $s_account_info = $GLOBALS['account_info'];
        
        /*获取参数*/
        $location_id = intval($GLOBALS['request']['location_id']);
        $youhui_sn = strim($GLOBALS['request']['youhui_sn']);
        
        /*业务逻辑*/
        $root['biz_user_status'] = $s_account_info?1:0;
        if (empty($s_account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商户权限
        if(!check_module_auth("youhuiv")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作验证权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        $result = biz_check_youhui($s_account_info,$youhui_sn,$location_id);
        if ($result['status']==1){
            $data['location_id'] = $result['location_id'];
            $data['youhui_sn'] = $result['youhui_sn'];
            $root['data'] = $data;
        }
        
        return output($root,$result['status'],$result['msg']);
        
    }
    
    
    /**
     * 	 优惠券提交接口
     *
     * 	 输入:
     *  location_id:string 门店ID
     *  youhui_sn:string
     *
     *  输出:
     *  status:int 结果状态 0失败 1成功
     *  info:信息返回
     *  biz_user_status：int 商户登录状态 0未登录/1已登录
     * 
     *  以下仅在biz_user_status为1时会返回
     *  is_auth：int 模块操作权限 0没有权限 / 1有权限
     *  如果status 为 1 的情况下
     *  [data] => Array
     (
        [location_id] => 34 ['int'] 门店编号
        [youhui_sn] => 62376366    [string] 验证密码
     )
    
     */
    public function use_youhui()
    {
        /*初始化*/
        $root = array();
        require_once(APP_ROOT_PATH."system/model/biz_verify.php");
        $s_account_info = $GLOBALS['account_info'];
        
        /*获取参数*/
        $location_id = intval($GLOBALS['request']['location_id']);
        $youhui_sn = strim($GLOBALS['request']['youhui_sn']);
        
        /*业务逻辑*/
        $root['biz_user_status'] = $s_account_info?1:0;
        if (empty($s_account_info)){
            return output($root,0,"商户未登录");
        }
        
        //返回商户权限
        if(!check_module_auth("youhuiv")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作验证权限");
        }else{
            $root['is_auth'] = 1;
        }
        
        $result = biz_use_youhui($s_account_info,$youhui_sn,$location_id);
        if ($result['status']==1){
            $data['location_id'] = $result['location_id'];
            $data['youhui_sn'] = $result['youhui_sn'];
            $root['data'] = $data;
        }

        return output($root,$result['status'],$result['msg']);
    }

    /**
     * @desc
     * Array
    (
        [verify_info] => Array
        (
            [id] => 216 ：int 验证信息id youhui_log表的id
            [youhui_id] => 42 ：int 优惠券的id
            [youhui_sn] => 81442449 ：string 验证券码
            [confirm_time] => 2016-12-19 12:02 ：string 验证时间
            [user_name] => 13700 ：string 用户名
            [location_name] => 好世界KTV ：string 验证门店
        )
        [buy_info] => Array
        (
            [name] => 可以领5张 ：string 优惠券名
            [youhui_type] => 0 ：int 优惠类型0：减免 1：折扣（前者是减钱，后者是以百分比打折，后者已经已经处理过，可以直接使用x折来显示了）
            [youhui_value] => 12 ：int 优惠额度
            [begin_time] => 0 ：string 有效期开始时间，为0就是未设置
            [end_time] => 0 ：string 有效期结束时间，为0就是未设置
        )
        [page_title] => 验证详情
        [ctl] => biz_youhuio
        [act] => record
        [status] => 1
        [info] =>
        [city_name] => 福州
        [return] => 1
        [sess_id] => uuss51ep4cbkbccaa9fkloh8e7
        [ref_uid] =>
    )
     * @author    wuqingxiang
     * @return unknown_type
     */
    public function record(){
        $root=array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        if(!$data_id){
            return output('',0,"查询的id为空");
        }
        //商户信息
        $account_info = $GLOBALS['account_info'];
        //判断是否登录
        if(!$account_info){
            $root['biz_user_status']=0;
            return output($root,0,"用户未登录");
        }else{
            $root['biz_user_status']=1;
        }
        //返回商户权限
        if(!check_module_auth("youhuiv")){
            $root['is_auth'] = 0;
            return output($root,0,"没有操作验证权限");
        }else{
            $root['is_auth'] = 1;
        }
        //该优惠券使用记录
        $youhui_record=$GLOBALS['db']->getRow("SELECT yl.id,yl.youhui_id,yl.youhui_sn,yl.confirm_time, u.user_name, sl. name AS location_name FROM ".DB_PREFIX."youhui_log AS yl LEFT JOIN ".DB_PREFIX."user AS u ON yl.user_id = u.id LEFT JOIN ".DB_PREFIX."supplier_location AS sl ON yl.location_id = sl.id WHERE yl.id =".$data_id);
        if(!$youhui_record){
            return output($root,0,"该优惠券验证记录不存在");
        }
        $youhui_record['confirm_time']=$youhui_record['confirm_time']?to_date($youhui_record['confirm_time'],"Y-m-d H:i"):0;
        $youhui_record['youhui_sn']=implode(" ",str_split($youhui_record['youhui_sn'],4));//优惠码每四位隔开
        $root['verify_info'] =$youhui_record;
        //获取优惠卷信息
        require_once(APP_ROOT_PATH."system/model/youhui.php");
        $youhui_info = get_youhui($youhui_record['youhui_id']);
        if(!$youhui_info){
            return output($root,0,"优惠券不存在");
        }
        $buy_info['name']=$youhui_info['name'];
        $buy_info['youhui_type']=$youhui_info['youhui_type'];
        $buy_info['youhui_value']=$youhui_info['youhui_value'];
        if($youhui_info['youhui_type']==1){
            $buy_info['youhui_value']=round($youhui_info['youhui_value']/10,2);
        }
        $buy_info['begin_time']=$youhui_info['begin_time']?to_date($youhui_info['begin_time'],"Y-m-d"):0;
        $buy_info['end_time']=$youhui_info['end_time']?to_date($youhui_info['end_time'],"Y-m-d"):0;
        $root['buy_info'] = $buy_info;
        $root['page_title']="验证详情";
        return output($root);
    }
}
?>

