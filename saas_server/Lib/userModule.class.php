<?php
/**
 * 会员数据接口
 * @author jobinlin
 *
 */
class userModule{
    
    /**
     * 获取用户信息接口
     * 输入：syn_id int 用户ID
     * 输出：
     */
    function get_user(){}
    
    
    /**
     * 编辑用户信息接口
     * ps:还有微信登陆相关字段，开发人员自行补充
     * 输入：
     *  array(
     *      "syn_id"=>21,   //用于同步数据产生的第三方ID 如一元夺宝的
     *      "user_name"=>"jobin",
     *      "password"=>"123123",
     *      "mobile"=>"13003899952"
     *      
     *      )
     * 输出：
     * 
     * 
     */
    function edit_user(){
        print_r($_REQUEST);exit;
    }
    
    
    
}