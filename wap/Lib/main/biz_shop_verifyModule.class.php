<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
class biz_shop_verifyModule extends MainBaseModule
{
    /**
     * 工作台页面
     **/
    public function index()
    {
         
        global_run();
        init_app_page();
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("biz_shop_verify","index",$param);
        if ($data['biz_user_status']==0){ //用户未登录
            app_redirect(wap_url("biz","user#login"));
        }

        //设定页面类型为工作台
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_shop_verify.html");
    }
    
    /**
     * 输入验证消费券
     * 输入消费券，失去焦点或者内容改变的时候调用
     * 不验证门店
     */
    public function index_check(){
        global_run();
        init_app_page();
        
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        
        $data = call_api_core("biz_shop_verify","index_check",$param);
        if ($data['status']) {
            $data['jump'] = wap_url("biz","shop_verify#coupon_check");
        }
        ajax_return($data);
    }
    
    /**
     * 扫码验证消费券
     * 输入消费券，失去焦点或者内容改变的时候调用
     * 不验证门店
     */
    public function scan_index_check(){
        global_run();
        init_app_page();
    
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
    
        $data = call_api_core("biz_shop_verify","scan_index_check",$param);
        if ($data['status']) {
            $data['jump'] = wap_url("biz","shop_verify#coupon_check");
        }
        ajax_return($data);
    }
    
    /**
     * 确认消费券页面
     */
    public function coupon_check(){
        global_run();
        init_app_page();
        $param['coupon_pwd']    = strim($_REQUEST['coupon_pwd']);

        $data = call_api_core("biz_shop_verify","coupon_check",$param);
        if($data['biz_user_status'] != LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
        
        if (!$data['status']) {
            showErr($data['info']);
        }
        
        $pwd = $data['coupon_data']['coupon_pwd'];
        // 4个后空格
        preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})?/', $pwd, $match);
        unset($match[0]);
        $data['coupon_data']['coupon_pwd'] = implode(' ', $match);

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_coupon_check.html");
    }
    
    /**
     * 使用消费券，ajax请求
     */
    public function coupon_use(){
        global_run();
        init_app_page();
        $param['location_id']       = intval($_REQUEST['location_id']);
        $param['coupon_pwd']        = strim($_REQUEST['coupon_pwd']);
        $param['coupon_use_count']  = intval($_REQUEST['coupon_use_count']?$_REQUEST['coupon_use_count']:1);
        
        $data = call_api_core("biz_shop_verify","coupon_use",$param);

        if ($data['status']) {
            $data['jump'] = wap_url("biz","shop_verify#index");
        }
        ajax_return($data);
    }
    
    /**
     * 验证记录
     */
    public function coupon_use_log(){
        global_run();
        init_app_page();
        
        $data = call_api_core("biz_shop_verify","coupon_use_log", array());
         
        
        if($data['biz_user_status'] != LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
        
        if($data['data']){
            foreach ($data['data'] as $k=>$v){
                switch ($v['type']) {
                    case 1:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dealv#record", array("data_id"=>$v['data_id'],"create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '团购验证';
                        break;
                        
                    case 2:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","youhuiv#record", array("data_id"=>$v['data_id'],"create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '优惠券验证';
                        break;
        
                    case 3:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","eventv#record", array("data_id"=>$v['data_id'],"create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '活动验证';
                        break;
        
                    case 4:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dealv#record", array("data_id"=>$v['data_id'],"create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '自提验证';
                        break;
                        
                    case 5:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dc_resorder#record", array("data_id"=>$v['data_id'],"lid"=>$v['location_id']));
                        $data['data'][$k]['type_name'] = '订座验证';
                        break;
                    default:
                        break;
                }
                
                $pwd = $data['data'][$k]['pwd'];
             
                // 4个后空格
                preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})?/', $pwd, $match);
                unset($match[0]);
                $data['data'][$k]['pwd'] = implode(' ', $match);
                
                $data['data'][$k]['create_time'] = to_date($v['create_time'], 'Y.m.d H:i');
            }
        }

        if(isset($data['page']) && is_array($data['page'])){
            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            //print_r($p);exit;
            $GLOBALS['tmpl']->assign('pages',$p);
        }
        
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_coupon_use_log.html");
    }
    
    /**
     * 搜索消费券
     */
    public function search_log(){
        global_run();
        init_app_page();
         
        $param['coupon_pwd'] = strim($_REQUEST['coupon_pwd']);
        $data = call_api_core("biz_shop_verify","search_log", $param);
         
        
        if($data['biz_user_status'] != LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }
        
        if($data['data']){
            foreach ($data['data'] as $k=>$v){
                switch ($v['type']) {
                    case 1:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dealv#record", array("data_id"=>$v['data_id'], "create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '团购券验证';
                        break;
        
                    case 2:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","youhuiv#record", array("data_id"=>$v['data_id'], "create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '优惠券验证';
                        break;
        
                    case 3:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","eventv#record", array("data_id"=>$v['data_id'], "create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '活动验证';
                        break;
        
                    case 4:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dealv#record", array("data_id"=>$v['data_id'], "create_time"=>$v['create_time']));
                        $data['data'][$k]['type_name'] = '自提验证';
                        break;
                    case 5:
                        $data['data'][$k]['redirect_url'] = wap_url("biz","dc_resorder#record", array("data_id"=>$v['data_id'],"lid"=>$v['location_id']));
                        $data['data'][$k]['type_name'] = '订座验证';
                        break;
                    default:
                        break;
                }
                
                
                $pwd = $data['data'][$k]['pwd'];
                 
                // 4个后空格
                preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})?/', $pwd, $match);
                unset($match[0]);
                $data['data'][$k]['pwd'] = implode(' ', $match);
                
                $data['data'][$k]['create_time'] = to_date($v['create_time'], 'Y.m.d H:i');
            }
        }
        $GLOBALS['tmpl']->assign("data",$data);
        $html = $GLOBALS['tmpl']->fetch('biz_coupon_use_log.html');
        echo $html;
    }
    /**
     * 推广二维码
     **/
    public function qrcode()
    {
        global_run();
        init_app_page();
        $param=array();
        $data = call_api_core("biz_shop_verify","qrcode", $param);
        if ($data['biz_user_status']==0){ //用户未登录
            app_redirect(wap_url("biz","user#login"));
        }
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_qrcode.html");

    }
}