<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require_once(APP_ROOT_PATH . 'system/model/user.php');

class uc_scoreApiModule extends MainBaseModule
{

    /**
     * 我的积分
     **/
    public function index()
    {
        global_run();
        init_app_page();
        $root = array();
        //分页
        $page = intval($GLOBALS['request']['page']);
        $page = $page == 0 ? 1 : $page;
        $page_size = PAGE_SIZE;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $user_data = $GLOBALS['user_info'];
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $user_info['user_login_status'] = $user_login_status;
        } else {
            $user_info['user_login_status'] = $user_login_status;
            $user_info['uid'] = $user_data['id'] ? $user_data['id'] : 0;
            $user_info['user_name'] = $user_data['user_name'] ? $user_data['user_name'] : 0;
            $user_info['user_money_format'] = format_price($user_data['money']) ? format_price($user_data['money']) : ""; //用户金额
            $user_info['user_score'] = intval($user_data['score']);
            $user_info['user_score_format'] = format_score($user_data['score']);
            $user_info['frozen_score'] = intval($user_data['frozen_score']);

            require_once(APP_ROOT_PATH . 'system/model/user_center.php');
            if(app_conf("SCORE_RECHARGE_SWITCH")){
                $score = get_user_log($limit, $user_data['id'],'frozen_score<>0 or score'); //获取积分数据

            }else{
                $score = get_user_log($limit, $user_data['id'], 'score'); //获取积分数据
            }
            //取出积分信息
            $uc_query_data['cur_score'] = $user_data['score'];
            $cur_group = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user_group where id=" . $user_data['group_id']);
            $uc_query_data['cur_gourp'] = $cur_group['id'];
            $uc_query_data['cur_gourp_name'] = $cur_group['name'];
            $uc_query_data['cur_discount'] = floatval(sprintf('%.2f', $cur_group['discount'] * 10));

            $count = intval($score['count']);
            $page_total = ceil($count / $page_size);
            $score['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
            //print_r($score);exit;
        }
        $root = $score;
        $root['page_title'] = "我的积分";
        $root['user_info'] = $user_info;
        $root['SCORE_RECHARGE_SWITCH'] = app_conf("SCORE_RECHARGE_SWITCH");
        $root['SCORE_RECHARGE_USABLE_SCORE'] = app_conf("SCORE_RECHARGE_USABLE_SCORE");
        $root['SCORE_RECHARGE_FROZEN_SCORE'] = app_conf("SCORE_RECHARGE_FROZEN_SCORE");
        return output($root);

    }

    /**
     * @desc   购买积分页数据
     * @author    吴庆祥
     * @return unknown_type
     */
    public function buy_score()
    {
        global_run();
        init_app_page();
        $user_info = array();
        $root = array();
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $user_info['user_login_status'] = $user_login_status;
        } else {
            $user_info = $GLOBALS['user_info'];
            $user_info['user_login_status'] = $user_login_status;
            $root['score_number_array'] = explode(",", app_conf("SCORE_RECHARGE_SCORE_NUMBER_SET"));
            $root['score_number_array'] = $root['score_number_array'] ? $root['score_number_array'] : "50,100,300,500,1000,1500";
            sort($root['score_number_array']);
            $root['score_number'] = count($root['score_number_array']);
            $root['score_number_array_other'] = 0;
            //将数量拆分成数组
            if ($root['score_number'] > 6) {
                $score_number_array = array();
                $score_number_array_other = array();
                for ($i = 0; $i < count($root['score_number_array']); $i++) {
                    if ($i < 5) {
                        $score_number_array[] = $root['score_number_array'][$i];
                    } else {
                        $score_number_array_other[] = "&yen;" . $root['score_number_array'][$i];
                    }
                }
                $root['score_number_array'] = $score_number_array;
                $root['score_number_array_other'] = json_encode($score_number_array_other);
            }
            $root['payment_list'] = $this->_getPaymentList();
        }
        $root['SCORE_RECHARGE_USABLE_SCORE'] = app_conf("SCORE_RECHARGE_USABLE_SCORE");
        $root['SCORE_RECHARGE_FROZEN_SCORE'] = app_conf("SCORE_RECHARGE_FROZEN_SCORE");
        $root['page_title'] = "购买积分";
        $root['user_info'] = $user_info;
        return output($root);
    }

    private function _getPaymentList()
    {
        $is_weixin = isWeixin();
        //输出支付方式
        if (APP_INDEX == 'wap' && !$is_weixin) {
            //支付列表
            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and class_name != 'Wwxjspay' and is_effect = 1";
        } elseif (APP_INDEX == 'wap' && $is_weixin) {
            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
        } else {
            //支付列表
            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
        }

        if (allow_show_api()) {
            $payment_list = $GLOBALS['db']->getAll($sql);
        }
        //输出支付方式
        foreach ($payment_list as $k => $v) {
            $directory = APP_ROOT_PATH . "system/payment/";
            $file = $directory . '/' . $v['code'] . "_payment.php";
            if (file_exists($file)) {
                require_once($file);
                $payment_class = $v['code'] . "_payment";
                $payment_object = new $payment_class();
                $payment_list[$k]['name'] = $payment_object->get_display_code();
            }

            if ($v['logo'] != "")
                $payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'], 40, 40, 1));
        }

        sort($payment_list);
        $payment_list = $payment_list ? $payment_list : array();
        return $payment_list;
    }

    public function done()
    {
        $root = array();
        $user_data = $GLOBALS['user_info'];
        $user_id = intval($user_data['id']);
        $payment_id = intval($GLOBALS['request']['payment_id']);
        $money = floatval($GLOBALS['request']['money']);
        $is_all_account_money=floatval($GLOBALS['request']['all_account_money']);

        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
        } else {
            $root['user_login_status'] = 1;
            if ($money <= 0) {
                return output("", 0, "请输入正确的金额");
            }
            $payment_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment where id = " . $payment_id);
            if($is_all_account_money>0&&$user_data['money']<$money){
                return output("", 0, "您的余额不足");
            }
            if (!$is_all_account_money&&!$payment_info) {
                    return output("", 0, "支付方式不存在");
            }
            if ($payment_info['fee_type'] == 0) //定额
            {
                $payment_fee = $payment_info['fee_amount'];
            } else
            {
                $payment_fee = $money * $payment_info['fee_amount'];
            }
            //开始生成订单
            $now = NOW_TIME;
            $order['type'] = 7; //积分充值订单
            $order['user_id'] = $GLOBALS['user_info']['id'];
            $order['create_time'] = $now;
            $order['update_time'] = $now;
            $order['total_price'] = $money + $payment_fee;
            $order['deal_total_price'] = $money;
            $order['pay_amount'] = 0;
            $order['pay_status'] = 0;
            $order['delivery_status'] = 5;
            $order['order_status'] = 0;
            $order['payment_id'] = $payment_id;
            $order['payment_fee'] = $payment_fee;
            $order['return_total_score'] = app_conf("SCORE_RECHARGE_USABLE_SCORE")*$money;
            $order['frozen_score']=app_conf("SCORE_RECHARGE_FROZEN_SCORE")*$money;
            do {
                $order['order_sn'] = to_date(get_gmtime(), "Ymdhis") . rand(100, 999);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "deal_order", $order, 'INSERT', '', 'SILENT');
                $order_id = intval($GLOBALS['db']->insert_id());
            } while ($order_id == 0);

            require_once(APP_ROOT_PATH . "system/model/cart.php");

            if(floatval($is_all_account_money) > 0)
            {
                $account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
                $payment_notice_id = make_payment_notice($money,$order_id,$account_payment_id);
                require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
                $account_payment = new Account_payment();
                $account_payment->get_payment_code($payment_notice_id);
            }else if($payment_info){
                $payment_notice_id = make_payment_notice($order['total_price'], $order_id, $payment_info['id']);
            }
            //创建支付接口的付款单
            $root['app_index'] = APP_INDEX;
            $rs = order_paid($order_id);
            if ($rs) {
                $root['pay_status'] = 1;
                $root['order_id'] = $order_id;
            } else {

                require_once(APP_ROOT_PATH . "system/payment/" . $payment_info['class_name'] . "_payment.php");
                $payment_class = $payment_info['class_name'] . "_payment";
                $payment_object = new $payment_class();
                $payment_code = $payment_object->get_payment_code($payment_notice_id);
                $root['online_pay'] = $payment_info['online_pay'];
                es_session::set("user_charge_" . $user_id, $payment_notice_id);
                if ($payment_info['online_pay'] == 3) //sdk在线支付
                {

                    $root['pay_status'] = 0;
                    $root['order_id'] = $order_id;
                    $root['sdk_code'] = $payment_code['sdk_code'];


                    return output($root); //sdk支付
                } else {

                    $root['pay_status'] = 0;
                    $root['payment_code'] = $payment_code;
                    $root['pay_url'] = $payment_code['pay_action'];
                    $root['page_title'] .= "充值中……";
                    $root['order_id'] = $order_id;
                }
            }

        }
        return output($root);

    }
}
