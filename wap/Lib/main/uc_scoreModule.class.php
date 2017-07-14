<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require_once(APP_ROOT_PATH . 'system/model/user.php');

class uc_scoreModule extends MainBaseModule
{

    /**
     * 我的积分
     **/
    public function index()
    {
        global_run();
        init_app_page();
        $param = array();
        $user_info = $GLOBALS['user_info'];
        $param['id'] = intval($user_info['id']);
        $param['page'] = intval($_REQUEST['page']);
        $data = call_api_core("uc_score", "index", $param);
        if ($data['user_info']['user_login_status'] != LOGIN_STATUS_LOGINED) {
            login_is_app_jump();
            //app_redirect(wap_url("index","user#login"));
        }
        //分页输出
        if (isset($data['page']) && is_array($data['page'])) {
            $page = new Page($data['page']['data_total'], $data['page']['page_size']); //初始化分页对象
            $p = $page->show();
            $GLOBALS['tmpl']->assign('pages', $p);
        }
        //数据
        $GLOBALS['tmpl']->assign('user_info', $data['user_info']);
        $GLOBALS['tmpl']->assign('list', $data['list']);
        $GLOBALS['tmpl']->assign('data', $data);
        $GLOBALS['tmpl']->assign("SCORE_RECHARGE_SWITCH", app_conf("SCORE_RECHARGE_SWITCH"));
        $GLOBALS['tmpl']->assign("SCORE_RECHARGE_USABLE_SCORE", app_conf("SCORE_RECHARGE_USABLE_SCORE"));
        $GLOBALS['tmpl']->assign("SCORE_RECHARGE_FROZEN_SCORE", app_conf("SCORE_RECHARGE_FROZEN_SCORE"));
        $GLOBALS['tmpl']->display("uc_score.html");

    }

    public function buy_score()
    {
        global_run();
        init_app_page();
        $data = call_api_core("uc_score", "buy_score");
        if ($data['user_info']['user_login_status'] != LOGIN_STATUS_LOGINED) {
            login_is_app_jump();
        }
        $GLOBALS['tmpl']->assign('data', $data);
        $GLOBALS['tmpl']->display("uc_score_buy_score.html");
    }

    public function done()
    {
        global_run();
        init_app_page();
        $param = array();
        $param['money'] = floatval(abs($_REQUEST['money']));
        $param['all_account_money'] = floatval($_REQUEST['all_account_money']);
        $param['payment_id'] = intval($_REQUEST['payment_id']);
        $data = call_api_core("uc_score", "done", $param);
        if ($data['status'] == -1) {
            $ajaxobj['status'] = 1;
            $ajaxobj['jump'] = wap_url("index", "user#login");
            ajax_return($ajaxobj);
        } elseif ($data['status'] == 1) {
            $data['jump'] = wap_url("index","payment#done",array("id"=>$data['order_id']));
            ajax_return($data);
        } else {
            $ajaxobj['status'] = $data['status'];
            $ajaxobj['info'] = $data['info'];
            $data['jump'] = $data['pay_url'];
            ajax_return($ajaxobj);
        }
    }
}
