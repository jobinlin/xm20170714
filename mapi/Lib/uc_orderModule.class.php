<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class uc_orderApiModule extends MainBaseApiModule
{

    /**
     * 会员中心我的抽奖
     * 输入：
     * page:int 当前的页数
     * pay_status:int 支付状态 0未支付 1已支付
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * item:array 订单列表
     * Array(
     *    Array
     * (
     * [id] => 52 int 订单ID
     * [order_sn] => 2015050405530018 string 订单编号
     * [order_status] => 0 int 订单状态 0:未结单 1:结单(将出现删除订单按钮)
     * [pay_status] => 0 int 支付状态 0:未支付(出现取消订单按钮) 1:已支付
     * [create_time] => 2015-05-04 17:53:00  string 下单时间
     * [pay_amount] => 0 float 已付金额
     * [total_price] => 16.9 float 应付金额
     * [buy_type] = 0或1   0表示订单商品是普通商品，1表示订单商品是积分商品
     * [return_total_score]=>100   消耗的积分
     * [c] => 1 int 商品总量
     * [deal_order_item] => Array
     * (
     * [0] => Array
     * (
     * [id] => 112 int 订单表中的商品ID
     * [deal_id] => 22 int 商品ID，用于跳到商品页
     * [deal_icon] => http://192.168.1.41/o2onew/public/attachment/201502/26/11/54ee909199d43_244x148.jpg 122x74 string 商品图
     * [name] => 仅售14.9元！价值66元的雨含浴室防滑垫1张，透明材质，环保无毒，两色可选，带吸盘，选择它给您的家人多一份关爱 string 商品全名
     * [sub_name] => 雨含浴室防滑垫  string 商品短名
     * [number] => 1 int 购买数量
     * [unit_price] => 14.9 float 单价
     * [total_price] => 14.9 float 总价
     * [buy_type] = 0或1 0是普通商品，1是积分商品
     * [return_score] = 100
     * [return_total_score] = 100
     * [dp_id] => int 点评ID ，ID大于0表示已点评
     * [consume_count] => int 消费数 大于0表示可以点评
     * [delivery_status]    =>    配送状态0:未发货 1:已发货 5.无需发货
     * [is_arrival]    =>    int 是否已收货 0:未收货1:已收货2:没收到货(维权)
     * [is_refund]    =>    int 是否支持退款，由商品表同步而来，0不支持 1支持
     * [refund_status]    =>    int 退款状态 0未退款 1退款中 2已退款 3退款被拒
     * )
     *
     * ==============每个订单商品的状态与功能的关联说明===============
     * 1. 当order_status为1,consume_count大于0，时将出现点评项，dp_id大于0表示已点评，否则为未点评，可以点击链接到点评页面,点评的type为deal，data_id为商品的deal_id
     * 2. 当order_status为0（未结单），delivery_status不等于5(需要发货的商品),is_arrival等于1(已收货)时将出现点评项，dp_id大于0表示已点评，否则为未点评，可以点击链接到点评页面,点评的type为deal，data_id为商品的deal_id
     * 3. 当delivery_status为0(需发货商品，未发货时),pay_status为2（已支付时），is_refund为1(支持退款)，显示退款功能,refund_status为0时(未退款)，显示退款操作，点击后进入退款操作页(uc_order#refund item_id=deal_order_item_id),1显示退款中 2显示已退款 3显示退款被拒
     * 4. 当delivery_status为5(消费券商品，需要退券),pay_status为2（已支付时），is_refund为1(支持退款)，显示退款功能，order_status为0时（未结单）不显示状态，一概显示退款,点击后进入退款操作页(uc_coupon#refund item_id=deal_order_item_id),order_status为1（结果时），当refund_status大于0，有退款状态，显示状态,1显示退款中 2显示已退款 3显示退款被拒
     * 5. 当order_status为0（未结单）,当delivery_status不为5(实体商品)显示发货状态,delivery_status:0 显示未发货，1:已发货，is_arrival为0时（未收货）显示查询物流操作,显示确认收货操作，显示没收到货操作 is_arrival为1显示已收货, is_arrival为2显示维权中
     *
     * ==============每个订单商品的状态与功能的关联说明===============
     *
     * )
     *
     * [status] => 未支付 string 订单状态
     * )
     * )
     * page:array 分页信息 array("page"=>当前页数,"page_total"=>总页数,"page_size"=>分页量,"data_total"=>数据总量);
     * page_title:string 页面标题
     */
    public function index()
    {
    	$root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $pay_status = intval($GLOBALS['request']['pay_status']);
        
        // 条件判断重写
        if($pay_status==9){
            $condition=" and do.type = 2  ";
        }else{
            $condition=" and do.type != 2 ";
        }
		$join=" ";
        $condition .= " and do.pay_status = 2 ";
        switch ($pay_status) {
            case '1':
                $page_title = '我的订单'; //'未付款订单';
                $condition = ' and do.pay_status <> 2 and do.is_delete=0 and do.return_total_score >= 0';
                break;
            case '2':
                $page_title = '我的订单'; //'待发货订单';
                //团购和自提不需要发货
                //$condition .= 'and do.delivery_status in (0,1) and d.is_pick=0  and d.is_shop=1 and d.delivery_status = 0 and do.order_status = 0 and do.is_delete=0 and do.return_total_score >= 0 and d.refund_status in (0,3)';
				$condition .=' and do.is_delete=0 and do.order_process_status=2';
                break;
            case '3':
                $page_title = '我的订单'; //'待确认订单';
				$join=" left join " . DB_PREFIX . "deal_coupon as dc on do.id=dc.order_id ";
				//$condition .= " and do.is_delete = 0  and ( (d.is_coupon=0 and d.refund_status in (0,3)) or (d.is_coupon=1 and dc.refund_status in (0,3) and dc.confirm_time=0)) and ( do.delivery_status =2  or do.delivery_status =5)  and do.order_status = 0";
				$condition .=' and do.is_delete=0 and do.order_process_status=3';
                break;
            case '4':
				$join=" left join " . DB_PREFIX . "deal_coupon as dc on do.id=dc.order_id ";
                $page_title = '我的订单'; //'待评价订单';
                //$condition .= 'and do.order_status = 1 and d.dp_id =0 and do.is_delete=0 and do.return_total_score >= 0 AND ( ( d.is_shop = 1 AND d.refund_status IN (0, 3) ) OR ( d.is_shop = 0 AND ( is_coupon = 0 OR ( is_coupon = 1 AND d.consume_count > 0 ) ) ) ) and ( do.type<>5 or (do.type=5 and (!(end_time <>0 and end_time<'.NOW_TIME.' and (dc.confirm_time = 0 and dc.refund_status <> 2 )) or (end_time=0 or end_time>='.NOW_TIME.' ))) )';
				$condition .=' and do.is_delete=0 and do.order_process_status=4';
                break;
            case '6':
                $page_title = '待使用订单';
                $condition .= 'and do.delivery_status = 5';
                break;
            case '5':
                $page_title = '退款订单';
                $condition .= ' and do.refund_status <> 0';
                break;
            case '9':
                $page_title = '兑换记录'; // 积分兑换
                $condition = ' and do.return_total_score < 0 and do.is_delete = 0';
                break;
            default:
                $page_title = '我的订单';
                $condition = ' and do.return_total_score >= 0  ';
                break;
        }
        

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            //分页
            $page = intval($GLOBALS['request']['page']);
            $page = $page == 0 ? 1 : $page;
            $condition=$condition." and do.type!=7 ";
            $page_size = PAGE_SIZE;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $order_table_name = get_user_order_table_name($user_id);

            $sql = "select do.*,min(d.dp_id) as is_dp,d.is_coupon  from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$join." where do.is_main=0 and not (do.is_cancel=0 and do.is_delete=1) and " .
                " do.user_id = " . $user_id . " and do.type != 1 " . $condition . " GROUP BY id  order by do.create_time desc limit " . $limit;
            // print_r($sql);exit;
            $sql_count = "select count(distinct(do.id)) from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$join." where  do.is_main=0 and not (do.is_cancel=0 and do.is_delete=1) and " .
                " do.user_id = " . $user_id . " and do.type != 1 " . $condition;
            $list = $GLOBALS['db']->getAll($sql);
            //要返回的字段
            $data = array();
            $count = 0;
            if (count($list)) {
                $count = $GLOBALS['db']->getOne($sql_count);
                $page_total = ceil($count / $page_size);

                foreach ($list as $k => $v) {
                    $order_item = array();
                    $order_item['id'] = $v['id'];
                    $order_item['order_sn'] = $v['order_sn'];
					$order_item['type'] = $v['type'];
                    $order_item['order_status'] = $v['order_status'];
                    $order_item['pay_status'] = $v['pay_status'];
                    $order_item['delivery_status'] = $v['delivery_status'];
                    $order_item['create_time'] = to_date($v['create_time']);
                    $order_item['pay_amount'] = round($v['pay_amount'], 2);
                    $order_item['total_price'] = round($v['total_price'], 2);
                    $order_item['buy_type'] = 0;
                    $order_item['is_delete'] = $v['is_delete'];
					$order_item['is_coupon']=$v['is_coupon'];
                    if ($v['return_total_score'] < 0) {
                        $order_item['buy_type'] = 1;
                        $order_item['return_total_score'] = round(abs($v['return_total_score']), 2);
                    }
                    if ($v['deal_order_item']) {
                        $list[$k]['deal_order_item'] = unserialize($v['deal_order_item']);
                    } else {
                        $order_id = $v['id'];
                        update_order_cache($order_id);
                        $list[$k]['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $order_id);
                    }
                    $c = 0;
                    $order_status_id=100;
					$order_item['is_groupbuy_or_pick']=1;
                    foreach ($list[$k]['deal_order_item'] as $kk => $vv) {
                        $c += intval($vv['number']);
                        $deal_item = array();
                        $deal_item['id'] = $vv['id'];
                        $deal_item['deal_id'] = $vv['deal_id'];
                        $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 122, 74, 1));
                        $deal_item['name'] = $vv['name'];
                        $deal_item['sub_name'] = $vv['sub_name'];
                        $deal_item['number'] = $vv['number'];
                        $deal_item['unit_price'] = round($vv['unit_price'], 2);
                        $deal_item['total_price'] = round($vv['total_price'], 2);
                        $deal_item['buy_type'] = $vv['buy_type'];
                        if ($deal_item['buy_type'] == "1") {
                            $deal_item['return_score'] = round(abs($vv['return_score']), 2);
                            $deal_item['return_total_score'] = round(abs($vv['return_total_score']), 2);
                        }
                        $deal_item['consume_count'] = intval($vv['consume_count']);
                        $deal_item['dp_id'] = intval($vv['dp_id']);
                        $deal_item['delivery_status'] = intval($vv['delivery_status']);
                        $deal_item['is_arrival'] = intval($vv['is_arrival']);
                        if (!($v['is_delete'] == 1 && $v['pay_status'] != 2)) {
                            if (!$order_item['is_check_logistics']) {
                                if ($deal_item['delivery_status'] == 1) { //存在已发货的商品
                                    $order_item['is_check_logistics'] = 1; //查看物流
                                }
                            }
                            if (!$order_item['is_pick']) {
                                if ($vv['is_pick'] == 1) { //自提
                                    $order_item['is_pick'] = 1;
                                }
                            }
                            if (!$order_item['is_dp']) {
                                if ($v['order_status'] == 1) {
									if ($vv['dp_id'] == 0) { //未点评，已有使用数量
										if($vv['consume_count'] > 0){
											$order_item['is_dp'] = 1; //评价
										}elseif($vv['is_shop']==0&&$vv['is_coupon']==0){
											$order_item['is_dp'] = 1; //评价
										}
									}
                                } else {
                                    if ($vv['delivery_status'] == 1 && $vv['is_arrival'] == 1 && $vv['dp_id'] == 0) { //未点评，已发货，已收货
                                        $order_item['is_dp'] = 1;
                                    }
                                    if($vv['is_coupon']==1 && $vv['dp_id'] == 0){
                                        $coupon_info=$GLOBALS['db']->getAll("select id,deal_type,refund_status from " . DB_PREFIX . "deal_coupon where order_id=".$vv['order_id']." and order_deal_id=".$vv['id']);
                                        if($coupon_info[0]["deal_type"]==1 || $vv['is_shop'] == 1){
                                            if($vv['consume_count']>0){
                                                $order_item['is_dp'] = 1;
                                            }
                                        }else{
                                            $refund_num=0;
                                            foreach ($coupon_info as $vvv){
                                                if($vvv['refund_status']==2){
                                                    $refund_num++;
                                                }
                                            }
                                            if($vv['number']==($vv['consume_count']+$refund_num) && $vv['number']!=$refund_num){
                                                $order_item['is_dp'] = 1;
                                            }
                                        }
                                    }
                                }

                            }
                            if ($order_item['is_groupbuy_or_pick']) {//0为待发货
								if($vv['is_shop']==1){
									if($vv['delivery_status']==0){
										$order_item['is_groupbuy_or_pick']=0;
									}elseif($vv['delivery_status']==5&&$vv['is_pick']==0){
										$order_item['is_groupbuy_or_pick']=0;
									}
								}
							}
                        }
                        //获得订单商品状态
                        $order_deal_status=$this->order_deal_status($v,$vv);
                        $deal_item['deal_orders']=$order_deal_status['deal_orders'];
                        $deal_orders_id=$order_deal_status['deal_orders_id'];
                        if($order_status_id>$deal_orders_id){
                        	$order_status_id=$deal_orders_id;
                        }

                        $deal_item['is_refund'] = intval($vv['is_refund']);
                        $deal_item['refund_status'] = intval($vv['refund_status']);
                        $deal_item['supplier_id'] = intval($vv['supplier_id']);
                        $deal_item['attr_str'] = $vv['attr_str'];

                        if (!is_array($order_item['deal_order_item'][$deal_item['supplier_id']])) {
                            if ($deal_item['supplier_id'] == 0) {
								if($order_item['type']==4){
									$order_item['deal_order_item']['0']['supplier_name'] = "平台自营_驿站配送";
								}else{
									 $order_item['deal_order_item']['0']['supplier_name'] = "平台自营";
								}
                                $order_item['deal_order_item']['0']['count'] = 1;
                            } else {
                                $supplier_info = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id = " . $deal_item['supplier_id']);
                                $order_item['deal_order_item'][$deal_item['supplier_id']]['supplier_name'] = $supplier_info['name'];
                                $order_item['deal_order_item'][$deal_item['supplier_id']]['count'] = 1;
                            }

                        } else {
                            $order_item['deal_order_item'][$deal_item['supplier_id']]['count']++;
                        }
                        $order_item['deal_order_item'][$deal_item['supplier_id']]['list'][] = $deal_item;

                        //$order_item['deal_order_item'][$kk] = $deal_item;
                    }
					$order_item['deal_order_item']=array_values($order_item['deal_order_item']);
                    $order_item['count'] = $c;
                    if (!$order_item['is_check_logistics']) {
                        $order_item['is_check_logistics'] = 0;
                    }
                    if (!$order_item['is_coupon']) {
                        $order_item['is_coupon'] = 0;
                    }
                    if (!$order_item['is_dp']) {
                        $order_item['is_dp'] = 0;
                    }
                    //开始处理订单状态
                    $order_status = "";
                    /*if($v['order_status'] == 1) { //结单的订单显示说明
						$order_status = "订单已完结";
					} else {
						if($v['pay_status'] != 2) {
							$order_status = "未支付";
						} else {
							$order_status = "已支付";
						}
					}*/
                    $order_item['is_del'] = 0; //删除订单
					$order_item['is_pay'] = 0;
                    if ($v['is_delete'] != 1/* and $v['refund_status'] != 2*/) {
                        if ($v['is_delete'] == 0) {
                            if ($v['refund_status'] == 2) {
                                $order_status = '已取消';
                            }
                            if ($v['order_status'] == 1 && $v['is_dp'] > 0) {
                                $order_status = "已完成";
                                $order_item['is_del'] = 1;
                            }
                            if ($v['order_status'] == 1 && $v['is_dp'] == 0) {
                                $order_status = "待评价";
                                $order_item['is_del'] = 1;
                            }
                            if ($order_status_id == 4) {
								$order_status = '已完成';
							}elseif ($order_status_id == 3){
								$order_status = '待评价';
							}elseif ($order_status_id == 5){
								$order_status = '已取消';
							}elseif($order_status_id==2){
								$order_status = "待确认";
							}elseif($order_status_id==1){
								$order_status = "待发货";
							}elseif($order_status_id==3.5){
								$order_status = "退款中";
							}
                            //if (($v['delivery_status'] == 2 || $v['delivery_status'] == 5) && $v['order_status'] == 0) {
                            //    $order_status = "待确认";
                            //}
                            //if ($v['delivery_status'] == 0 || $v['delivery_status'] == 1 || ($v['delivery_status'] == 5&&$order_item['is_groupbuy_or_pick']==0)) {
                            //    $order_status = "待发货";
                            //}
                            if ($v['pay_status'] != 2) {
								$order_item['is_pay']=1;
                                $order_status = "待付款";
                            }
                        }

                    } else {
                        $order_status = "已取消";
                    }
					$button=array();
					if($order_item['is_delete']==0){
						if($order_item['pay_status']!=2){
							$button_arr=array();
							$button_arr['name']="去支付";
							$button_arr['type']="j-payment";
							$button_arr['url']=wap_url("index","cart#pay",array("id"=>$order_item['id']));
							$button_arr['param']=array("id"=>$order_item['id']);
							$button[]=$button_arr;
							$button_arr=array();
							$button_arr['name']="取消订单";
							$button_arr['type']="j-cancel";
							$button_arr['url']=wap_url("index","uc_order#cancel",array("id"=>$order_item['id'],"is_cancel"=>1));
							$button_arr['param']=array("id"=>$order_item['id'],"is_cancel"=>1);
							$button[]=$button_arr;
						}else{
							if($order_item['is_check_logistics']==1 &&$order_item['type']!=4){
								$button_arr=array();
								$button_arr['name']="物流&收货";
								$button_arr['type']="j-logistics|goodsreceipt";
								$button_arr['url']=wap_url("index","uc_order#logistics",array("data_id"=>$order_item['id']));
								$button_arr['param']=array("data_id"=>$order_item['id']);
								$button[]=$button_arr;
							}
							if($order_item['is_coupon']==1 ||($order_item['type']==4&&$order_item['delivery_status']==2)){
								$button_arr=array();
								$button_arr['name']="查看".app_conf("COUPON_NAME");
								$button_arr['type']="j-coupon";
								$arr = array();
								$arr['order_id'] = $order_item['id'];
								if ($order_item['is_pick'] == 1) {
									$arr['coupon_status'] = 1;
								}
								if ($order_item['type']==4&&$order_item['delivery_status']==2) {
									$arr['coupon_status'] = 2;
								}
								$button_arr['url']=wap_url("index","uc_coupon",$arr);
								$button_arr['param']=$arr;
								$button[]=$button_arr;
							}
							if($order_item['is_dp']==1){
								$button_arr=array();
								$button_arr['name']="评价";
								$button_arr['type']="j-dp";
								$button_arr['url']=wap_url("index","uc_order#order_dp",array("id"=>$order_item['id']));
								$button_arr['param']=array("id"=>$order_item['id']);
								$button[]=$button_arr;
							}
							if($order_item['is_del']==1){
								$button_arr=array();
								$button_arr['name']="删除订单";
								$button_arr['type']="j-del";
								$button_arr['url']=wap_url("index","uc_order#cancel",array("id"=>$order_item['id']));
								$button_arr['param']=array("id"=>$order_item['id']);
								$button[]=$button_arr;
							}
						}
					}
                    $order_item['status_name'] = $order_status;
					$order_item['operation'] = $button;
                    //订单状态

                    $data[$k] = $order_item;
                }
            }
            $root['item'] = $data;
			foreach ($root['item'] as $k=>$v){
				$root['item'][$k]['app_format_total_price']=format_price_html($v['total_price'],3);
				foreach ($v['deal_order_item'] as $kk=>$vv){
					$root['item'][$k]['deal_order_item'][$kk]['status_name']=$v['status_name'];
					if($vv['count']==1){
						foreach ($vv['list'] as $kkk=>$vvv){
							$root['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['app_format_unit_price'] = format_price_html($vvv['unit_price'],3);
							if($vvv['buy_type']==1){
								$root['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['app_format_return_score'] = $vvv['return_score'];
							}
						}
					}
					
					
				}
			}
        


            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
        }

        $root['user_login_status'] = $user_login_status;

        $root['pay_status'] = $pay_status;
        // $root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_title'] = $page_title;
        return output($root);
    }


    /**
     * 取消删除订单接口
     *
     * 输入
     * id: int 订单ID
     *
     * 输出
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * status: int 0失败 1成功
     * info: string 消息
     */
    public function cancel()
    {
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {
            $root['user_login_status'] = $user_login_status;
            $id = intval($GLOBALS['request']['id']);
            $is_cancel = intval($GLOBALS['request']['is_cancel']);
            $order_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order where id = " . $id . " and (is_cancel=1 or is_delete = 0) and user_id = " . $GLOBALS['user_info']['id']);
            if ($order_info) {
                if ($is_cancel == 1) {
                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set is_delete = 1,is_cancel=1 where (order_status = 1 or pay_status = 0) and (is_cancel=1 or is_delete = 0) and user_id = " . $GLOBALS['user_info']['id'] . " and id = " . $id);
                } else {
                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set is_delete = 1,is_cancel=0 where (order_status = 1 or pay_status = 0) and (is_cancel=1 or is_delete = 0) and user_id = " . $GLOBALS['user_info']['id'] . " and id = " . $id);
                }
                if ($GLOBALS['db']->affected_rows()) {
                    require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                    //开始退已付的款
                    if ($order_info['pay_status'] == 0 && $order_info['pay_amount'] > 0) {
                        $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set pay_amount = 0,ecv_id = 0,ecv_money=0,account_money = 0 where id = " . $order_info['id']);
                        require_once(APP_ROOT_PATH . "system/model/user.php");
                        if ($order_info['account_money'] > 0) {
                            modify_account(array("money" => $order_info['account_money']), $order_info['user_id'], "取消订单，退回余额支付 ");
                            order_log("用户取消订单，退回余额支付 " . $order_info['account_money'] . " 元", $order_info['id']);
                        }
                        if ($order_info['ecv_id']) {
                            $GLOBALS['db']->query("update " . DB_PREFIX . "ecv set use_count = use_count - 1 where id = " . $order_info['ecv_id']);
                            order_log("用户取消订单，代金券退回 ", $order_info['id']);
                        }

                    }
					if($order_info['pay_status'] == 0 && $order_info['exchange_money'] > 0){
						$GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set exchange_money = 0,total_price=total_price+".$order_info['exchange_money']." where id = " . $order_info['id']);
						$score_purchase=unserialize($order_info['score_purchase']);
						modify_account(array("score" =>$score_purchase['user_use_score'] ), $order_info['user_id'], "取消订单，退回积分抵扣 ");
						order_log("用户取消订单，积分抵扣退回 ", $order_info['id']);
					}
                    over_order($order_info['id']);
                    if ($is_cancel != 1) {
                        return output($root, 1, "订单删除成功");
                    } else {
                        return output($root, 1, "订单取消成功");
                    }

                } else {
                    if ($is_cancel != 1) {
                        return output($root, 0, "订单删除失败");
                    } else {
                        return output($root, 0, "订单取消失败");
                    }
                }
            } else {
                return output($root, 0, "订单不存在");
            }
        }
    }


    /**
     * 加载退款（实体商品的页面数据加载），本接口不作数据越权验证，提交时验证
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户):判断==1
     * page_title: string 页面标题
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     * item:array 订单商品数据
     *  [id] => 112 int 订单表中的商品ID
     * [deal_id] => 22 int 商品ID，用于跳到商品页
     * [deal_icon] => http://192.168.1.41/o2onew/public/attachment/201502/26/11/54ee909199d43_244x148.jpg 122x74 string 商品图
     * [name] => 仅售14.9元！价值66元的雨含浴室防滑垫1张，透明材质，环保无毒，两色可选，带吸盘，选择它给您的家人多一份关爱 string 商品全名
     * [sub_name] => 雨含浴室防滑垫  string 商品短名
     * [number] => 1 int 购买数量
     * [unit_price] => 14.9 float 单价
     * [total_price] => 14.9 float 总价
     * [dp_id] => int 点评ID ，ID大于0表示已点评
     * [consume_count] => int 消费数 大于0表示可以点评
     * [delivery_status]    =>    配送状态0:未发货 1:已发货 5.无需发货
     * [is_arrival]    =>    int 是否已收货 0:未收货1:已收货2:没收到货(维权)
     * [is_refund]    =>    int 是否支持退款，由商品表同步而来，0不支持 1支持
     * [refund_status]    =>    int 退款状态 0未退款 1退款中 2已退款 3退款被拒
     */
    public function refund()
    {


        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
        } else {

            $user_login_status = check_login();

            $root['user_login_status'] = $user_login_status;
            //if ($GLOBALS['request']['from'] == 'wap') {
                $deal_id = $GLOBALS['request']['deal_id'];
				$deal_id_arr=explode(',',$deal_id);
                $deal_id_str = implode(",", $deal_id_arr);
                $coupon_id = $GLOBALS['request']['coupon_id'];
				$coupon_id_arr=explode(',',$coupon_id);
                $coupon_id_str = implode(",", $coupon_id_arr);
//                 echo "<pre>";echo $deal_id_str;print_r($GLOBALS['request']);
                $item_deal = array();
                $item_coupon = array();
                if ($deal_id_str) {
                    $item_deal = $GLOBALS['db']->getAll("select name,deal_icon,attr_str,unit_price,number,supplier_id,discount_unit_price from " . DB_PREFIX . "deal_order_item where id in(" . $deal_id_str . ")");

                }
                if ($coupon_id_str) {
                    $item_coupon = $GLOBALS['db']->getAll("select doi.name,doi.deal_icon,doi.attr_str,doi.unit_price,doi.number,doi.supplier_id,dc.password,doi.discount_unit_price from " . DB_PREFIX . "deal_order_item doi LEFT JOIN fanwe_deal_coupon dc on doi.id=dc.order_deal_id where dc.id in(" . $coupon_id_str . ")");
                }
                $item = array_merge($item_deal, $item_coupon);


                $item_supplier = array();
                foreach ($item as $k => $v) {
                    if (!$item_supplier[$v['supplier_id']]) {
                        if ($v['supplier_id'] == 0) {
                            $item_supplier[$v['supplier_id']]['supplier_name'] = "平台自营";
                        } else {
                            $supplier_info = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id = " . $v['supplier_id']);
                            $item_supplier[$v['supplier_id']]['supplier_name'] = $supplier_info['name'];
                        }
                    }
                    $item_supplier[$v['supplier_id']]['list'][] = $v;
                }
                //echo "<pre>";print_r($item_supplier);exit;
                $root['item'] = array_values($item_supplier);
				foreach ($root['item'] as $k=>$v){
					foreach ($v['list'] as $kk=>$vv){
						$root['item'][$k]['list'][$kk]['number']=$vv['password']!=''?1:$vv['number'];
						$root['item'][$k]['list'][$kk]['deal_icon']=get_abs_img_root(get_spec_image($vv['deal_icon'], 74, 74, 1));
						$root['item'][$k]['list'][$kk]['unit_price']=intval($vv['unit_price']);
						$root['item'][$k]['list'][$kk]['format_unit_price'] = format_price_html(round($vv['discount_unit_price'],2),2);
						$root['item'][$k]['list'][$kk]['app_format_unit_price'] = format_price_html(round($vv['discount_unit_price'],2),3);
					}
				}
           /*} else {
                $item_id = intval($GLOBALS['request']['item_id']);

                $root['page_title'] = "退款申请";
                $root['item_id'] = $item_id;

                $vv = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $item_id);
                $deal_item = array();
                $deal_item['id'] = $vv['id'];
                $deal_item['deal_id'] = $vv['deal_id'];
                $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 122, 74, 1));
                $deal_item['name'] = $vv['name'];
                $deal_item['sub_name'] = $vv['sub_name'];
                $deal_item['number'] = $vv['number'];
                $deal_item['unit_price'] = round($vv['unit_price'], 2);
                $deal_item['total_price'] = round($vv['total_price'], 2);
                $deal_item['consume_count'] = intval($vv['consume_count']);
                $deal_item['dp_id'] = intval($vv['dp_id']);
                $deal_item['delivery_status'] = intval($vv['delivery_status']);
                $deal_item['is_arrival'] = intval($vv['is_arrival']);
                $deal_item['is_refund'] = intval($vv['is_refund']);
                $deal_item['refund_status'] = intval($vv['refund_status']);
                $root['item'] = $deal_item;
            }*/
        }
        //$root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_title'] = "退款申请";
		$root['placeholder'] = "请输入退款理由";
		$root['action'] = wap_url("index","uc_order#do_refund");
		$root['deal_id']=$GLOBALS['request']['deal_id'];
		$root['coupon_id']=$GLOBALS['request']['coupon_id'];
        //print_r($root);exit;
        return output($root);

    }

    /**
     * 执行退款接口(实体商品)
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     * content:string 退单理由
     *
     * 输出
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * status: int 0失败 1成功
     * info: string 消息
     *
     */
    public function do_refund()
    {
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {
            $content = strim($GLOBALS['request']['content']);
            $root['user_login_status'] = $user_login_status;
            if ($content == "") {
                return output($root, 0, "请输入退款理由");
            }
            //if ($GLOBALS['request']['from'] == 'wap') {
                $deal_id_str = $GLOBALS['request']['deal_id'];
                $deal_id = explode(",", $deal_id_str);
                $coupon_id_str = $GLOBALS['request']['coupon_id'];
                $coupon_id = explode(",", $coupon_id_str);
                $order_id=0;
                $supplier_id = 0; // 判断是否是商户的订单
                $has_success = false; //是否有一条提交成功
                if ($deal_id) {
                    $deal_order_item_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where id in(" . $deal_id_str . ")");
                    foreach ($deal_order_item_list as $k => $deal_order_item) {
                        $order_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order where id = '" . $deal_order_item['order_id'] . "' and order_status = 0 and user_id = " . $GLOBALS['user_info']['id']);
                        if($order_id==0) {
                            $order_id=$order_info['id'];
                        }

                        if ($supplier_id == 0) {
                            $supplier_id = $deal_order_item['supplier_id'];
                        }

                        if ($order_info) {
                            if ($deal_order_item['is_arrival'] != 1&& $order_info['pay_status'] == 2 && $deal_order_item['is_refund'] == 1) {
                                if ($deal_order_item['refund_status'] != 0) {
                                    return output($root, 0, "不允许退款");
                                }
                            } else {
                                return output($root, 0, "不允许退款");
                            }
                        } else {
                            return output($root, 0, "非法操作");
                        }
                    }
                    foreach ($deal_order_item_list as $k => $deal_order_item) {
                        $order_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order where id = '" . $deal_order_item['order_id'] . "' and order_status = 0 and user_id = " . $GLOBALS['user_info']['id']);
                        if ($order_info) {
                            if ($deal_order_item['is_arrival'] != 1&& $order_info['pay_status'] == 2 && $deal_order_item['is_refund'] == 1) {
                                if ($deal_order_item['refund_status'] == 0) {
                                    //执行退单,标记：deal_order_item表与deal_order表，
                                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $deal_order_item['id']);
                                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $deal_order_item['order_id']);


                                    update_order_cache($deal_order_item['order_id']);

                                    order_log($deal_order_item['sub_name'] . "申请退款，等待审核", $deal_order_item['order_id']);
                                    if ($supplier_id != 0) {
                                        send_supplier_msg($supplier_id, 'refund', $order_id);
                                    }

                                    require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                                    distribute_order($order_info['id']);
                                    $has_success = true;
                                    //return output($root,1,"退款申请已提交，请等待审核");
                                } else {
                                    return output($root, 0, "不允许退款");
                                }
                            } else {
                                return output($root, 0, "不允许退款");
                            }
                        } else {
                            return output($root, 0, "非法操作");
                        }
                    }
                }
                if ($coupon_id) {
                    foreach ($coupon_id as $cid) {
                        $cid = intval($cid);
                        $coupon = $GLOBALS['db']->getRow("select cou.*,do.is_pick from " . DB_PREFIX . "deal_coupon as cou left join " . DB_PREFIX . "deal_order_item as do on cou.order_deal_id=do.id where cou.user_id = " . $GLOBALS['user_info']['id'] . " and cou.id = " . $cid);
                        //logger::write(print_r($coupon,1));exit;
                        if($order_id==0)
                        $order_id=$coupon['order_id'];
                        if ($coupon) {
                            if ($coupon['refund_status'] == 0 && $coupon['confirm_time'] == 0) //从未退过款可以退款，且未使用过
                            {
                                                           
                                if ($coupon['any_refund'] == 1 || ($coupon['expire_refund'] == 1 && $coupon['end_time'] > 0 && $coupon['end_time'] < NOW_TIME)||$coupon['is_pick'] == 1) //随时退或过期退已过期
                                {
                                    
                                    //执行退券
                                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_coupon set refund_status = 1 where id = " . $coupon['id']);
                                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $coupon['order_deal_id']);
                                    $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $coupon['order_id']);

                                    $deal_order_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $coupon['order_deal_id']);

                                    /*$msg = array();
									$msg['rel_table'] = "deal_order";
									$msg['rel_id'] = $coupon['order_id'];
									$msg['title'] = "退款申请";
									$msg['content'] = $content;
									$msg['create_time'] = NOW_TIME;
									$msg['user_id'] = $GLOBALS['user_info']['id'];
									$GLOBALS['db']->autoExecute(DB_PREFIX."message",$msg);
									*/
                                    update_order_cache($coupon['order_id']);

                                    order_log($deal_order_item['sub_name'] . "申请退一张消费券，等待审核", $coupon['order_id']);

                                    require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                                    distribute_order($coupon['order_id']);

                                    $has_success = true;
                                }
                            }
                        }
                    }
                    //end foreach

                    $msg = array();
                    $msg['rel_table'] = "deal_order";
                    $msg['rel_id'] = $deal_order_item['order_id'];
                    $msg['title'] = "退款申请";
                    $msg['content'] = $content;
                    $msg['create_time'] = NOW_TIME;
                    $msg['user_id'] = $GLOBALS['user_info']['id'];
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);
                    $message_id = intval($GLOBALS['db']->insert_id());
                    if ($message_id) {
                        if ($deal_id) {
                            foreach ($deal_id as $k => $v) {
                                $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set message_id = " . $message_id . " where id = " . $v);
                            }
                        }
                        if ($coupon_id) {
                            foreach ($coupon_id as $k => $v) {
                                $GLOBALS['db']->query("update " . DB_PREFIX . "deal_coupon set message_id = " . $message_id . " where id = " . $v);
                                $item = $GLOBALS['db']->getRow("select id,order_deal_id from " . DB_PREFIX . "deal_coupon where id = " . $v);
                                $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set message_id = " . $message_id . " where id = " . $item['order_deal_id']);
                            }
                        }
                    }
                    if ($has_success) {
                    	$root['order_id']=$order_id;
                        return output($root, 1, "提交成功，请等待审核");
                    } else {
                        return output($root, 0, "操作失败");
                    }
                }

            /*} else {
                //退单
                $item_id = intval($GLOBALS['request']['item_id']);


                $deal_order_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $item_id);
                $order_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order where id = '" . $deal_order_item['order_id'] . "' and order_status = 0 and user_id = " . $GLOBALS['user_info']['id']);
                if ($order_info) {
                    if ($deal_order_item['is_arrival'] !=1&& $order_info['pay_status'] == 2 && $deal_order_item['is_refund'] == 1) {
                        if ($deal_order_item['refund_status'] == 0) {
                            //执行退单,标记：deal_order_item表与deal_order表，
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $deal_order_item['id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $deal_order_item['order_id']);

                            $msg = array();
                            $msg['rel_table'] = "deal_order";
                            $msg['rel_id'] = $deal_order_item['order_id'];
                            $msg['title'] = "退款申请";
                            $msg['content'] = "退款申请：" . $content;
                            $msg['create_time'] = NOW_TIME;
                            $msg['user_id'] = $GLOBALS['user_info']['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);

                            update_order_cache($deal_order_item['order_id']);

                            order_log($deal_order_item['sub_name'] . "申请退款，等待审核", $deal_order_item['order_id']);

                            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                            distribute_order($order_info['id']);

                            return output($root, 1, "退款申请已提交，请等待审核");
                        } else {
                            return output($root, 0, "不允许退款");
                        }
                    } else {
                        return output($root, 0, "不允许退款");
                    }
                } else {
                    return output($root, 0, "非法操作");
                }
            }*/

        }


    }


    /**
     * 加载退款（团购商品，消费券的页面数据加载），本接口不作数据越权验证，提交时验证
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户):判断==1
     * page_title: string 页面标题
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     * item:array 订单商品数据
     *   [id] => 112 int 订单表中的商品ID
     * [deal_id] => 22 int 商品ID，用于跳到商品页
     * [deal_icon] => http://192.168.1.41/o2onew/public/attachment/201502/26/11/54ee909199d43_244x148.jpg 122x74 string 商品图
     * [name] => 仅售14.9元！价值66元的雨含浴室防滑垫1张，透明材质，环保无毒，两色可选，带吸盘，选择它给您的家人多一份关爱 string 商品全名
     * [sub_name] => 雨含浴室防滑垫  string 商品短名
     * [number] => 1 int 购买数量
     * [unit_price] => 14.9 float 单价
     * [total_price] => 14.9 float 总价
     * [dp_id] => int 点评ID ，ID大于0表示已点评
     * [consume_count] => int 消费数 大于0表示可以点评
     * [delivery_status]    =>    配送状态0:未发货 1:已发货 5.无需发货
     * [is_arrival]    =>    int 是否已收货 0:未收货1:已收货2:没收到货(维权)
     * [is_refund]    =>    int 是否支持退款，由商品表同步而来，0不支持 1支持
     * [refund_status]    =>    int 退款状态 0未退款 1退款中 2已退款 3退款被拒
     * coupon_list:array 本单的消费券列表
     * Array(
     * Array(
     * id:int 消费券ID
     * password:string 消费券序列号
     * deal_type:int 发券类型 0按件发券 1按单发券，为1时显示，共可消费item[number]位
     * time_str:string 时间状态
     * status_str:string 消费券状态
     * is_refund:int 是否允许退款（出现退款勾选项） 0否 1是
     * )
     * )

     */
    public function refund_coupon()
    {

        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
        } else {

            $root['user_login_status'] = $user_login_status;
            $item_id = intval($GLOBALS['request']['item_id']);

            $root['page_title'] = "退款申请";
            $root['item_id'] = $item_id;

            $vv = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $item_id);

            $deal_item = array();
            $deal_item['id'] = $vv['id'];
            $deal_item['deal_id'] = $vv['deal_id'];
            $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 122, 74, 1));
            $deal_item['name'] = $vv['name'];
            $deal_item['sub_name'] = $vv['sub_name'];
            $deal_item['number'] = $vv['number'];
            $deal_item['unit_price'] = round($vv['unit_price'], 2);
            $deal_item['total_price'] = round($vv['total_price'], 2);
            $deal_item['consume_count'] = intval($vv['consume_count']);
            $deal_item['dp_id'] = intval($vv['dp_id']);
            $deal_item['delivery_status'] = intval($vv['delivery_status']);
            $deal_item['is_arrival'] = intval($vv['is_arrival']);
            $deal_item['is_refund'] = intval($vv['is_refund']);
            $deal_item['refund_status'] = intval($vv['refund_status']);
            $root['item'] = $deal_item;

            $coupon_list = array();
            $coupon_list_rs = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_coupon where is_valid > 0 and user_id = " . $GLOBALS['user_info']['id'] . " and order_deal_id = " . $vv['id']);
            foreach ($coupon_list_rs as $k => $v) {
                $coupon['id'] = $v['id'];
                $coupon['password'] = $v['password'];
                $coupon['deal_type'] = $v['deal_type'];

                if ($v['end_time']) {
                    $time_str = to_date($v['begin_time'], "Y-m-d") . "到期";
                }
                if ($v['begin_time'] == 0 && $v['end_time'] == 0) {
                    $time_str = "无限期";
                }
                $coupon['time_str'] = $time_str;


                if ($v['confirm_time'] != 0) {
                    $status_str = to_date($v['confirm_time'], "Y-m-d") . "已消费";
                } else {
                    if ($v['refund_status'] == 1) {
                        $status_str = "退款中";
                    } elseif ($v['refund_status'] == 2) {
                        $status_str = "已退款";
                    } elseif ($v['refund_status'] == 3) {
                        $status_str = "退款被拒";
                    } else {
                        if ($v['is_valid'] == 1) {
                            if ($v['end_time'] > 0 && $v['end_time'] < NOW_TIME) {
                                $status_str = "已过期";
                            } else {
                                $status_str = "有效";
                            }
                        } else {
                            $status_str = "作废";
                        }
                    }
                }
                $coupon['status_str'] = $status_str;

                $is_refund = 0;
                if ($v['refund_status'] == 0 && $v['confirm_time'] == 0) {
                    if ($v['any_refund'] == 1 || ($v['expire_refund'] == 1 && $v['end_time'] > 0 && $v['end_time'] < NOW_TIME)) {
                        $is_refund = 1;
                    }
                }
                $coupon['is_refund'] = $is_refund;

                $coupon_list[$k] = $coupon;
            }
            $root['coupon_list'] = $coupon_list;
        }

        return output($root);

    }


    /**
     * 执行退款接口(消费券)
     * 输入:
     * item_id: array 消费券ID
     * Array(
     *        1,2,3
     * )
     * content:string 退单理由
     *
     * 输出
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * status: int 0失败 1成功
     * info: string 消息
     *
     */
    public function do_refund_coupon()
    {
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {
            //退单
            $item_ids = $GLOBALS['request']['item_id'];
            $content = strim($GLOBALS['request']['content']);
            $root['user_login_status'] = $user_login_status;
            if ($content == "") {
                return output($root, 0, "请输入退款理由");
            }

            $has_success = false; //是否有一条提交成功
            foreach ($item_ids as $cid) {
                $cid = intval($cid);
                $coupon = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_coupon where user_id = " . $GLOBALS['user_info']['id'] . " and id = " . $cid);
                if ($coupon) {
                    if ($coupon['refund_status'] == 0 && $coupon['confirm_time'] == 0) //从未退过款可以退款，且未使用过
                    {
                        if ($coupon['any_refund'] == 1 || ($coupon['expire_refund'] == 1 && $coupon['end_time'] > 0 && $coupon['end_time'] < NOW_TIME)) //随时退或过期退已过期
                        {
                            //执行退券
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_coupon set refund_status = 1 where id = " . $coupon['id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $coupon['order_deal_id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $coupon['order_id']);

                            $deal_order_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $coupon['order_deal_id']);

                            $msg = array();
                            $msg['rel_table'] = "deal_order";
                            $msg['rel_id'] = $coupon['order_id'];
                            $msg['title'] = "退款申请";
                            $msg['content'] = $content;
                            $msg['create_time'] = NOW_TIME;
                            $msg['user_id'] = $GLOBALS['user_info']['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);
                            update_order_cache($coupon['order_id']);

                            order_log($deal_order_item['sub_name'] . "申请退一张消费券，等待审核", $coupon['order_id']);

                            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                            distribute_order($coupon['order_id']);

                            $has_success = true;
                        }
                    }
                }
            }
            //end foreach
            if ($has_success) {
                return output($root, 1, "提交成功，请等待审核");
            } else {
                return output($root, 0, "操作失败");
            }
        }
    }


    /**
     * 维权页面，没收到货（实体商品的页面数据加载），本接口不作数据越权验证，提交时验证
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     *
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户):判断==1
     * page_title: string 页面标题
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     * item:array 订单商品数据
     *  [id] => 112 int 订单表中的商品ID
     * [deal_id] => 22 int 商品ID，用于跳到商品页
     * [deal_icon] => http://192.168.1.41/o2onew/public/attachment/201502/26/11/54ee909199d43_244x148.jpg 122x74 string 商品图
     * [name] => 仅售14.9元！价值66元的雨含浴室防滑垫1张，透明材质，环保无毒，两色可选，带吸盘，选择它给您的家人多一份关爱 string 商品全名
     * [sub_name] => 雨含浴室防滑垫  string 商品短名
     * [number] => 1 int 购买数量
     * [unit_price] => 14.9 float 单价
     * [total_price] => 14.9 float 总价
     * [dp_id] => int 点评ID ，ID大于0表示已点评
     * [consume_count] => int 消费数 大于0表示可以点评
     * [delivery_status]    =>    配送状态0:未发货 1:已发货 5.无需发货
     * [is_arrival]    =>    int 是否已收货 0:未收货1:已收货2:没收到货(维权)
     * [is_refund]    =>    int 是否支持退款，由商品表同步而来，0不支持 1支持
     * [refund_status]    =>    int 退款状态 0未退款 1退款中 2已退款 3退款被拒
     */
    public function refuse_delivery()
    {

        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
        } else {

            $root['user_login_status'] = $user_login_status;
            $item_id = intval($GLOBALS['request']['item_id']);

            $root['page_title'] = "没收到货";
            $root['item_id'] = $item_id;

            $vv = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $item_id);
            $deal_item = array();
            $deal_item['id'] = $vv['id'];
            $deal_item['deal_id'] = $vv['deal_id'];
            $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 122, 74, 1));
            $deal_item['name'] = $vv['name'];
            $deal_item['sub_name'] = $vv['sub_name'];
            $deal_item['number'] = $vv['number'];
            $deal_item['unit_price'] = round($vv['unit_price'], 2);
            $deal_item['total_price'] = round($vv['total_price'], 2);
            $deal_item['consume_count'] = intval($vv['consume_count']);
            $deal_item['dp_id'] = intval($vv['dp_id']);
            $deal_item['delivery_status'] = intval($vv['delivery_status']);
            $deal_item['is_arrival'] = intval($vv['is_arrival']);
            $deal_item['is_refund'] = intval($vv['is_refund']);
            $deal_item['refund_status'] = intval($vv['refund_status']);
            $root['item'] = $deal_item;
        }

        return output($root);

    }


    /**
     * 执行维权，没收到货接口(实体商品)
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     * content:string 申请理由
     *
     * 输出
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * status: int 0失败 1成功
     * info: string 消息
     *
     */
    public function do_refuse_delivery()
    {
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {
            //退单
            $id = intval($GLOBALS['request']['item_id']);
            $content = strim($GLOBALS['request']['content']);
            $root['user_login_status'] = $user_login_status;
            if ($content == "") {
                return output($root, 0, "请输入具体说明");
            }

            $user_id = intval($GLOBALS['user_info']['id']);
            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $order_table_name = get_user_order_table_name($user_id);

            $delivery_notice = $GLOBALS['db']->getRow("select n.* from " . DB_PREFIX . "delivery_notice as n left join " . $order_table_name . " as o on n.order_id = o.id where n.order_item_id = " . $id . " and o.user_id = " . $user_id . " and is_arrival = 0 order by delivery_time desc");
            if ($delivery_notice) {
                require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                $res = refuse_delivery($delivery_notice['notice_sn'], $id);
                if ($res) {

                    $msg = array();
                    $msg['rel_table'] = "deal_order";
                    $msg['rel_id'] = $delivery_notice['order_id'];
                    $msg['title'] = "订单维权";
                    $msg['content'] = "订单维权：" . $content;
                    $msg['create_time'] = NOW_TIME;
                    $msg['user_id'] = $GLOBALS['user_info']['id'];
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);


                    return output($root, 1, "维权提交成功");
                } else {
                    return output($root, 0, "维权提交失败");
                }
            } else {
                return output($root, 0, "订单未发货");
            }
        }


    }


    /**
     * 确认收货接口(实体商品)
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     *
     * 输出
     * user_login_status:int 用户登录状态(1 已经登录/0 用户未登录/2临时用户)
     * status: int 0失败 1成功
     * info: string 消息
     *
     */
    public function verify_delivery()
    {
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {

            $root['user_login_status'] = $user_login_status;

            $id = intval($GLOBALS['request']['item_id']);
            $user_id = intval($GLOBALS['user_info']['id']);
            
            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            if($GLOBALS['request']['from']=='wap'){
                $delivery_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "delivery_notice where id=".$id);
            }else{
                $order_table_name = get_user_order_table_name($user_id);
                $delivery_notice = $GLOBALS['db']->getRow("select n.* from " . DB_PREFIX . "delivery_notice as n left join " . $order_table_name . " as o on n.order_id = o.id where n.order_item_id = " . $id . " and o.user_id = " . $user_id . " and is_arrival = 0 order by delivery_time desc");                
            }
            if ($delivery_notice) {
                require_once(APP_ROOT_PATH . "system/model/deal_order.php");
                $res = order_confirm_delivery($delivery_notice['notice_sn'],$delivery_notice['express_id'], $delivery_notice['order_id']);
          
                if ($res['status']) {
                    $root['ids']=$res['ids'];
                    return output($root, 1, "确认收货成功");
                } else {
                    return output($root, 0, "确认收货失败");
                }

            } else {
                return output($root, 0, "订单未发货");
            }
        }
    }

    public function verify_no_delivery(){
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output($root, 0, "请先登录");
        } else {
        
            $root['user_login_status'] = $user_login_status;

            $user_id = intval($GLOBALS['user_info']['id']);
            $order_ids = $GLOBALS['request']['order_ids'];
            if(!$order_ids){
                return output($root,0, "请选择订单商品");
            }
            
            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $i=0;
            foreach ($order_ids as $item_id){
                $id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_order_item where id=".$item_id." and is_arrival<>1 and refund_status<>2");
                if($id){
                    $res=confirm_no_delivery($item_id);
                    if(!$res){
                        break;
                    }
                    $i++;
                }else {
                    return output($root, 1, "商品确认收货失败");
                }
            }
            if($i==count($order_ids)){
                // 无需发货的商户消息推送
                $sql = 'select order_id, supplier_id from '.DB_PREFIX.'deal_order_item where id in ('.implode(',', $order_ids).')';
                $row = $GLOBALS['db']->getRow($sql);
                if ($row['supplier_id'] > 0) {
                    send_supplier_msg($row['supplier_id'], 'nodelivery', $row['order_id']);
                }

                return output($root, 1, "确认收货成功");
            }else {
                if($i>0){
                    return output($root, 0, "部分商品确认收货失败");
                }else {
                    return output($root, 0, "确认收货失败");
                }
            }
        }
    }

    /**
     * 快递查询接口
     * 输入:
     * item_id: int 订单商品表中的商品ID（order_item_id，非商品的ID）
     *
     * 输出
     * status: int 0失败 1成功
     * info: string 消息
     * url: 快递查询的手机端接口地址(仅status为1返回)
     */
    public function check_delivery()
    {
        $id = intval($GLOBALS['request']['item_id']);
        $user_id = intval($GLOBALS['user_info']['id']);
        require_once(APP_ROOT_PATH . "system/model/deal_order.php");
        $order_table_name = get_user_order_table_name($user_id);

        $delivery_notice = $GLOBALS['db']->getRow("select n.* from " . DB_PREFIX . "delivery_notice as n left join " . $order_table_name . " as o on n.order_id = o.id where n.order_item_id = " . $id . " and o.user_id = " . $user_id . " order by delivery_time desc");
        if ($delivery_notice) {
            $express_id = intval($delivery_notice['express_id']);
            $typeNu = strim($delivery_notice["notice_sn"]);
            $express_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "express where is_effect = 1 and id = " . $express_id);
            $express_info['config'] = unserialize($express_info['config']);
            $typeCom = strim($express_info['config']["app_code"]);
            if (isset($typeCom) && isset($typeNu)) {
                $root['url'] = "http://m.kuaidi100.com/index_all.html?type=" . $typeCom . "&postid=" . $typeNu;
                return output($root);
            } else {
                return output("", 0, "无效的快递查询");
            }
        } else {
            return output("", 0, "非法操作");
        }
    }


    /**
     * wap端订单方法重写
     * @return array
     */
    public function wap_index()
    {
        $root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $pay_status = intval($GLOBALS['request']['pay_status']);
        
        // 条件判断重写
        $condition = '';
        if($pay_status==9){
            $condition .=" and do.type = 2  ";
        }else{
            $condition .=" and do.type != 2  ";
        }
        
		$join=" ";
        $condition .= " and do.pay_status = 2 ";
        switch ($pay_status) {
            case '1':
                // $page_title = '我的订单'; //'未付款订单';
                $condition = ' and do.pay_status <> 2 and do.is_delete=0 and do.return_total_score >= 0';
                break;
            /*case '2':
                // $page_title = '我的订单'; //'待发货订单';
                //团购和自提不需要发货
                //$condition .= 'and do.delivery_status in (0,1) and d.is_pick=0  and d.is_shop=1 and d.delivery_status = 0 and do.order_status = 0 and do.is_delete=0 and do.return_total_score >= 0 and d.refund_status in (0,3)';
				$condition .=' and do.is_delete=0 and do.order_process_status=2';
                break;*/
            case '2':
                // $page_title = '我的订单'; //'待确认订单';
				$join=" left join " . DB_PREFIX . "deal_coupon as dc on do.id=dc.order_id ";
				//$condition .= " and do.is_delete = 0  and ( (d.is_coupon=0 and d.refund_status in (0,3)) or (d.is_coupon=1 and dc.refund_status in (0,3) and dc.confirm_time=0)) and ( do.delivery_status =2  or do.delivery_status =5)  and do.order_status = 0";
				$condition .=' and do.is_delete=0 and do.order_process_status in (2,3)';
                break;
            case '3':
				$join=" left join " . DB_PREFIX . "deal_coupon as dc on do.id=dc.order_id ";
                // $page_title = '我的订单'; //'待评价订单';
                //$condition .= 'and do.order_status = 1 and d.dp_id =0 and do.is_delete=0 and do.return_total_score >= 0 AND ( ( d.is_shop = 1 AND d.refund_status IN (0, 3) ) OR ( d.is_shop = 0 AND ( is_coupon = 0 OR ( is_coupon = 1 AND d.consume_count > 0 ) ) ) ) and ( do.type<>5 or (do.type=5 and (!(end_time <>0 and end_time<'.NOW_TIME.' and (dc.confirm_time = 0 and dc.refund_status <> 2 )) or (end_time=0 or end_time>='.NOW_TIME.' ))) )';
				$condition .=' and do.is_delete=0 and do.order_process_status=4';
                break;
            case '6':
                $page_title = '待使用订单';
                $condition .= 'and do.delivery_status = 5';
                break;
            case '5':
                $page_title = '退款订单';
                $condition .= ' and do.refund_status <> 0';
                break;
            case '9':
                $page_title = '兑换记录'; // 积分兑换
                $condition = ' and do.return_total_score < 0 and (do.is_delete = 0 or (do.is_delete=1 and do.is_cancel=1))';
                break;
            default:
                // $page_title = '我的订单';
                $condition = ' and do.return_total_score >= 0  ';
                break;
        }
        /*$id = intval($GLOBALS['request']['id']);
		if($id>0)
			$condition.=" and do.id = ".$id." ";*/
        $isCountOrder = false;
        if (in_array($pay_status, array(0,1,2,3))) {
            $isCountOrder = true;
            $pageTypeTitle = array(0 => '商城单', 1 => '团购单');
            // 团购单、商城单条件
            $pageType = 0;
            if (!empty($GLOBALS['request']['tuan']) && $GLOBALS['request']['tuan'] == 1) { // 团购单
                $condition .= ' and do.type = 5 and do.type!=7 ';
                $pageType = 1;
            } else {
                $condition .= ' and do.type != 5 and do.type!=7 ';
            }
            $page_title = $pageTypeTitle[$pageType];
        }
        

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            //分页
            $page = intval($GLOBALS['request']['page']);
            $page = $page == 0 ? 1 : $page;

            $page_size = PAGE_SIZE;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $order_table_name = get_user_order_table_name($user_id);

            $sql = "select do.*,min(d.dp_id) as is_dp,d.is_coupon  from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$join." where do.is_main=0 and not (do.is_cancel=0 and do.is_delete=1) and " .
                " do.user_id = " . $user_id . " and do.type != 1 " . $condition . " GROUP BY id  order by do.create_time desc limit " . $limit;
            // print_r($sql);exit;
            $sql_count = "select count(distinct(do.id)) from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn ".$join." where  do.is_main=0 and not (do.is_cancel=0 and do.is_delete=1) and " .
                " do.user_id = " . $user_id . " and do.type != 1 " . $condition;
            $list = $GLOBALS['db']->getAll($sql);
            //要返回的字段
            $data = array();
            $count = 0;
            if (count($list)) {
                $count = $GLOBALS['db']->getOne($sql_count);
                $page_total = ceil($count / $page_size);

                foreach ($list as $k => $v) {
                    $order_item = array();
                    $order_item['id'] = $v['id'];
                    $order_item['order_sn'] = $v['order_sn'];
					$order_item['type'] = $v['type'];
                    $order_item['order_status'] = $v['order_status'];
                    $order_item['pay_status'] = $v['pay_status'];
                    $order_item['delivery_status'] = $v['delivery_status'];
                    $order_item['create_time'] = to_date($v['create_time']);
                    $order_item['pay_amount'] = round($v['pay_amount'], 2);
                    $order_item['total_price'] = round($v['total_price'], 2);
                    $order_item['buy_type'] = 0;
                    $order_item['is_delete'] = $v['is_delete'];
					$order_item['is_coupon']=$v['is_coupon'];
					$order_item['consignee_id']=$v['consignee_id'];
                    if ($v['return_total_score'] < 0) {
                        $order_item['buy_type'] = 1;
                        $order_item['return_total_score'] = round(abs($v['return_total_score']), 2);
                    }
                    if ($v['deal_order_item']) {
                        $list[$k]['deal_order_item'] = unserialize($v['deal_order_item']);
                    } else {
                        $order_id = $v['id'];
                        update_order_cache($order_id);
                        $list[$k]['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $order_id);
                    }
                    $c = 0;
                    $order_status_id=100;
					$order_item['is_groupbuy_or_pick']=1;
					$order_item['check_logistics_status']=0;
                    foreach ($list[$k]['deal_order_item'] as $kk => $vv) {
                        $c += intval($vv['number']);
                        $deal_item = array();
                        $deal_item['id'] = $vv['id'];
                        $deal_item['deal_id'] = $vv['deal_id'];
                        $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 74, 74, 1));
                        $deal_item['name'] = htmlspecialchars_decode($vv['name']);
                        $deal_item['sub_name'] = htmlspecialchars_decode($vv['sub_name']);
                        $deal_item['number'] = $vv['number'];
                        $deal_item['unit_price'] = round($vv['unit_price'], 2);
                        $deal_item['discount_unit_price'] = round($vv['discount_unit_price'], 2);
                        $deal_item['total_price'] = round($vv['total_price'], 2);
                        $deal_item['buy_type'] = $vv['buy_type'];
                        if ($deal_item['buy_type'] == "1") {
                            $deal_item['return_score'] = round(abs($vv['return_score']), 2);
                            $deal_item['return_total_score'] = round(abs($vv['return_total_score']), 2);
                        }
                        $deal_item['consume_count'] = intval($vv['consume_count']);
                        $deal_item['dp_id'] = intval($vv['dp_id']);
                        $deal_item['delivery_status'] = intval($vv['delivery_status']);
                        $deal_item['is_arrival'] = intval($vv['is_arrival']);
                        if (!($v['is_delete'] == 1 && $v['pay_status'] != 2)) {
                            if (!$order_item['is_check_logistics']) {
                                if ($deal_item['delivery_status'] == 1) { //存在已发货的商品
                                    $order_item['is_check_logistics'] = 1; //查看物流
									if($deal_item['is_arrival']==0){//未收货
										if($order_item['consignee_id']>0){//需要配送的商品
											$order_item['check_logistics_status']=max(4,$order_item['check_logistics_status']);
										}else{//无需配送的商品
											$order_item['check_logistics_status']=max(2,$order_item['check_logistics_status']);
										}
									}elseif($deal_item['is_arrival']==1){//已收货
										if($order_item['consignee_id']>0){//需要配送的商品
											$order_item['check_logistics_status']=max(3,$order_item['check_logistics_status']);
										}else{//无需配送的商品
											$order_item['check_logistics_status']=max(1,$order_item['check_logistics_status']);
										}
									}
									
                                }
                            }
                            if (!$order_item['is_pick']) {
                                if ($vv['is_pick'] == 1) { //自提
                                    $order_item['is_pick'] = 1;
                                }
                            }
                            if (!$order_item['is_dp']) {
                                if ($v['order_status'] == 1) {
									if ($vv['dp_id'] == 0) { //未点评，已有使用数量
										if($vv['consume_count'] > 0){
											$order_item['is_dp'] = 1; //评价
										}elseif($vv['is_shop']==0&&$vv['is_coupon']==0){
											$order_item['is_dp'] = 1; //评价
										}
									}
                                } else {
                                    if ($vv['delivery_status'] == 1 && $vv['is_arrival'] == 1 && $vv['dp_id'] == 0) { //未点评，已发货，已收货
                                        $order_item['is_dp'] = 1;
                                    }
                                    if($vv['is_coupon']==1 && $vv['dp_id'] == 0){
                                        $coupon_info=$GLOBALS['db']->getAll("select id,deal_type,refund_status from " . DB_PREFIX . "deal_coupon where order_id=".$vv['order_id']." and order_deal_id=".$vv['id']);
                                        if($coupon_info[0]["deal_type"]==1 || $vv['is_shop'] == 1){
                                            if($vv['consume_count']>0){
                                                $order_item['is_dp'] = 1;
                                            }
                                        }else{
                                            $refund_num=0;
                                            foreach ($coupon_info as $vvv){
                                                if($vvv['refund_status']==2){
                                                    $refund_num++;
                                                }
                                            }
                                            if($vv['number']==($vv['consume_count']+$refund_num) && $vv['number']!=$refund_num){
                                                $order_item['is_dp'] = 1;
                                            }
                                        }
                                    }
                                }

                            }
                            if ($order_item['is_groupbuy_or_pick']) {//0为待发货
								if($vv['is_shop']==1){
									if($vv['delivery_status']==0){
										$order_item['is_groupbuy_or_pick']=0;
									}elseif($vv['delivery_status']==5&&$vv['is_pick']==0){
										$order_item['is_groupbuy_or_pick']=0;
									}
								}
							}
                        }
                        //获得订单商品状态
                        $order_deal_status=$this->order_deal_status($v,$vv);
                        $deal_item['deal_orders']=$order_deal_status['deal_orders'];
                        $deal_orders_id=$order_deal_status['deal_orders_id'];
                        if($order_status_id>$deal_orders_id){
                        	$order_status_id=$deal_orders_id;
                        }

                        $deal_item['is_refund'] = intval($vv['is_refund']);
                        $deal_item['refund_status'] = intval($vv['refund_status']);
                        $deal_item['supplier_id'] = intval($vv['supplier_id']);
                        $deal_item['attr_str'] = $vv['attr_str'];

                        if (!is_array($order_item['deal_order_item'][$deal_item['supplier_id']])) {
                            if ($deal_item['supplier_id'] == 0) {
								if($order_item['type']==4){
									$order_item['deal_order_item']['0']['supplier_name'] = "平台自营_驿站配送";
								}else{
									 $order_item['deal_order_item']['0']['supplier_name'] = "平台自营";
								}
                                $order_item['deal_order_item']['0']['count'] = 1;
                            } else {
                                $supplier_info = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id = " . $deal_item['supplier_id']);
                                $order_item['deal_order_item'][$deal_item['supplier_id']]['supplier_name'] = $supplier_info['name'];
                                $order_item['deal_order_item'][$deal_item['supplier_id']]['count'] = 1;
                            }

                        } else {
                            $order_item['deal_order_item'][$deal_item['supplier_id']]['count']++;
                        }
                        $order_item['deal_order_item'][$deal_item['supplier_id']]['list'][] = $deal_item;

                        //$order_item['deal_order_item'][$kk] = $deal_item;
                    }
					$order_item['deal_order_item']=array_values($order_item['deal_order_item']);
                    $order_item['count'] = $c;
                    if (!$order_item['is_check_logistics']) {
                        $order_item['is_check_logistics'] = 0;
                    }
                    if (!$order_item['is_coupon']) {
                        $order_item['is_coupon'] = 0;
                    }
                    if (!$order_item['is_dp']) {
                        $order_item['is_dp'] = 0;
                    }
                    //开始处理订单状态
                    $order_status = "";
                    /*if($v['order_status'] == 1) { //结单的订单显示说明
						$order_status = "订单已完结";
					} else {
						if($v['pay_status'] != 2) {
							$order_status = "未支付";
						} else {
							$order_status = "已支付";
						}
					}*/
                    $order_item['is_del'] = 0; //删除订单
					$order_item['is_pay'] = 0;
                    if ($v['is_delete'] != 1/* and $v['refund_status'] != 2*/) {
                        if ($v['is_delete'] == 0) {
                            if ($v['refund_status'] == 2) {
                                $order_status = '已取消';
                            }
                            if ($v['order_status'] == 1 && $v['is_dp'] > 0) {
                                $order_status = "已完成";
                                $order_item['is_del'] = 1;
                            }
                            if ($v['order_status'] == 1 && $v['is_dp'] == 0) {
                                $order_status = "待评价";
                                $order_item['is_del'] = 1;
                            }
                            if ($order_status_id == 4) {
								$order_status = '已完成';
							}elseif ($order_status_id == 3){
								$order_status = '待评价';
							}elseif ($order_status_id == 5){
								$order_status = '已取消';
							}elseif($order_status_id==2){
								$order_status = "待确认";
							}elseif($order_status_id==1){
								$order_status = "待发货";
							}elseif($order_status_id==3.5){
								$order_status = "退款中";
							}
                            //if (($v['delivery_status'] == 2 || $v['delivery_status'] == 5) && $v['order_status'] == 0) {
                            //    $order_status = "待确认";
                            //}
                            //if ($v['delivery_status'] == 0 || $v['delivery_status'] == 1 || ($v['delivery_status'] == 5&&$order_item['is_groupbuy_or_pick']==0)) {
                            //    $order_status = "待发货";
                            //}
                            if ($v['pay_status'] != 2) {
								$order_item['is_pay']=1;
                                $order_status = "待付款";
                            }
                        }

                    } else {
                        $order_status = "已取消";
                    }
					$button=array();
					if($order_item['is_delete']==0){
						if($order_item['pay_status']!=2){
							$button_arr=array();
							$button_arr['name']="去支付";
							$button_arr['type']="j-payment";
							$button_arr['param']=array("id"=>$order_item['id']);
							$button_arr['url']=SITE_DOMAIN.wap_url("index","cart#pay",$button_arr['param']);
							$button[]=$button_arr;
							$button_arr=array();
							$button_arr['name']="取消订单";
							$button_arr['type']="j-cancel";
							$button_arr['param']=array("id"=>$order_item['id'],"is_cancel"=>1,'pay_status'=>$pay_status);
							$button_arr['url']=SITE_DOMAIN.wap_url("index","uc_order#cancel",$button_arr['param']);
							$button[]=$button_arr;
						}else{
							if($order_item['is_check_logistics']==1 &&$order_item['type']!=4){
								$button_arr=array();
								$button_arr['name']="收货";//$this->check_logistics_status($order_item['check_logistics_status']);//"物流&收货";
								$button_arr['type']="j-logistics|goodsreceipt";
								$button_arr['param']=array("data_id"=>$order_item['id']);
								$button_arr['url']=SITE_DOMAIN.wap_url("index","uc_order#logistics",$button_arr['param']);
								$button[]=$button_arr;
							}
							if($order_item['is_coupon']==1 ||($order_item['type']==4&&$order_item['delivery_status']==2)){
								$button_arr=array();
								$button_arr['name']="查看".app_conf("COUPON_NAME");
								$button_arr['type']="j-coupon";
								$arr = array();
								$arr['order_id'] = $order_item['id'];
								if ($order_item['is_pick'] == 1) {
									$arr['coupon_status'] = 1;
								}
								if ($order_item['type']==4&&$order_item['delivery_status']==2) {
									$arr['coupon_status'] = 2;
								}
								$button_arr['param']=$arr;
								$button_arr['url']=SITE_DOMAIN.wap_url("index","uc_coupon",$button_arr['param']);
								$button[]=$button_arr;
							}
							if($order_item['is_dp']==1){
								$button_arr=array();
								$button_arr['name']="评价";
								$button_arr['type']="j-dp";
								$button_arr['param']=array("id"=>$order_item['id']);
								$button_arr['url']=SITE_DOMAIN.wap_url("index","uc_order#order_dp",$button_arr['param']);
								$button[]=$button_arr;
							}
							if($order_item['is_del']==1){
								$button_arr=array();
								$button_arr['name']="删除订单";
								$button_arr['type']="j-del";
								$button_arr['param']=array("id"=>$order_item['id'],'pay_status'=>$pay_status);
								$button_arr['url']=SITE_DOMAIN.wap_url("index","uc_order#cancel",$button_arr['param']);
								$button[]=$button_arr;
							}
						}
					}
                    $order_item['status_name'] = $order_status;
					$order_item['operation'] = $button;
                    //订单状态

                    $data[$k] = $order_item;
                }
            }
            $root['item'] = $data;
			foreach ($root['item'] as $k=>$v){
				$root['item'][$k]['app_format_total_price']=format_price_html($v['total_price'],3);
				foreach ($v['deal_order_item'] as $kk=>$vv){
					$root['item'][$k]['deal_order_item'][$kk]['status_name']=$v['status_name'];
					if($vv['count']==1){
						foreach ($vv['list'] as $kkk=>$vvv){
							$root['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['app_format_unit_price'] = format_price_html($vvv['discount_unit_price'],3);
							if($vvv['buy_type']==1){
								$root['item'][$k]['deal_order_item'][$kk]['list'][$kkk]['app_format_return_score'] = $vvv['return_score'];
							}
						}
					}
					
					
				}
			}
            // 未支付的查询
            $notPayNum = 0;
            if ($pay_status == 1) { // 查询未付款的情况
                $notPayNum = $count;
            } else {
                if ($isCountOrder) {
                    $not_pay_sql = "select count(*) from ".$order_table_name." where is_main=0 AND is_delete = 0 and user_id = ".$user_id." and pay_status <> 2";
                    $not_pay_sql .= $pageType == 1 ? ' and type = 5' : ' and type not in (1,2,5,7)';
                    $notPayNum = $GLOBALS['db']->getOne($not_pay_sql);
                }
            }
            // $notPayNum = countNotPayOrder($user_id);
            $root['not_pay'] = $notPayNum;
            // 团购未使用的查询
            $notUsedCoupon = 0;
            //if ($pay_status == 2) {
            //    $notUsedCoupon = $count;
            //} else {
                if ($isCountOrder && $pageType == 1) { // 团购时才统计
                    $notUsedCouponSql = 'select count(do.id) from '.DB_PREFIX.'deal_order as do left join '.DB_PREFIX.'deal_order_item AS d on do.order_sn=d.order_sn  left join '.DB_PREFIX.'deal_coupon as dc on do.id=dc.order_id and dc.confirm_time=0 and dc.refund_status<>2 and (dc.end_time=0 or dc.end_time>'.NOW_TIME.') and dc.is_valid<>2 where do.is_main=0 and not (do.is_cancel=0 and do.is_delete=1) and  do.user_id = '.$user_id.' and do.pay_status = 2 and do.is_delete=0 and do.order_process_status in (2,3) and do.type = 5';
                    $notUsedCoupon = $GLOBALS['db']->getOne($notUsedCouponSql);
                    // print_r($notUsedCouponSql);exit;
                }
            //}
            $root['not_use_coupon'] = $notUsedCoupon;

            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
        }

        $root['user_login_status'] = $user_login_status;

        $root['pay_status'] = $pay_status;
        // $root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_type'] = $pageType;
        $root['page_title'] = $page_title;
        return output($root);
    }


    /**
     * 交易完成
     *输入：发货单notice_sn
     */
    public function order_done()
    {
        $root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $ids = $GLOBALS['request']['ids'];
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output("", 0, "请先登录");
        } else {
            $root['user_login_status'] = $user_login_status;
            //输出订单信息
            $order_item = $GLOBALS['db']->getAll("select do.*,d.shop_cate_id,d.return_score as deal_return from ".DB_PREFIX."deal_order_item as do INNER JOIN " . DB_PREFIX . "deal AS d ON do.deal_id=d.id where do.id in (".$ids.")");            
            $root['order_id'] = $order_item['0']['order_id'];
            $score = 0;
            $money = 0;
            $cate_id = array();
            foreach ($order_item as $t => $v) {
                if ($v['is_arrival'] != 1) {
                    $root['order_status'] = 0;
                    return output("", 0, "订单未完成");
                }
                if($v['deal_return']>0){
                    $score += $v['return_total_score'];
                    $money += $v['return_total_money'];
                }
                $cate_id[] = $v['shop_cate_id'];
            }
            $root['return_total_score'] = $score;
            $root['return_total_money'] = $money;
            $root['order_status'] = 1;
            //logger::write(print_r($cate_id,1));
            //输出推介商品
            require_once(APP_ROOT_PATH . "system/model/deal.php");
            $cate_id = implode(",",$cate_id);
            
            $where = " shop_cate_id in (" . $cate_id . ") and is_shop=1 and return_score>=0 ";
            $order = " buy_count desc ";
            $deal_result = get_goods_list("0,10", array(DEAL_ONLINE), "", "", $where, $order);

            $list = $deal_result['list'];
            $goodses = array();
            foreach ($list as $k => $v) {
                $goodses[$k] = format_deal_list_item($v);
            }

            $root['item'] = $goodses ? $goodses : array();
            $root['page_title'] = "交易成功";
            return output($root);
        }
    }

    /**
     * 评价页面
     *输入：订单ID
     */
    public function order_dp()
    {
        $root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $order_id = $GLOBALS['request']['id'];
        
        $user_login_status = check_login();
        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output("", 0, "请先登录");
        } else {
            $root['user_login_status'] = $user_login_status;
            //可评价的发货商品
            $sql = "select do.id,do.deal_id,do.name,do.sub_name,do.number,do.deal_icon,do.is_coupon,do.consume_count,do.dp_id,do.is_arrival,do.delivery_status,do.is_shop,d.order_status from " . DB_PREFIX . "deal_order_item as do INNER JOIN " . DB_PREFIX . "deal_order AS d on do.order_id=d.id where do.order_id=" . $order_id." and d.pay_status = 2 and (do.is_arrival = 1 or do.consume_count>0 or (do.is_shop=0 and do.is_coupon=0)) and (do.refund_status<>2 or (d.type=5 and d.order_status=1)) and do.dp_id=0 and d.user_id=".$user_id;
            //$sql="select deal_id from ".DB_PREFIX."deal_order_item where dp_id=0 and consume_count>0 and order_id=".$order_id;
            //print_r($sql);exit;
            //$deal = $GLOBALS['db']->getAll($sql);
           
            $item = $GLOBALS['db']->getAll($sql);
            $item_id = array();
            $item_list =array();
            foreach ($item as $t => $v) {
                //$item=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp where order_id=".$order_id." and deal_id=".$v['deal_id']." and user_id=".$user_id);
                if ( $v['delivery_status'] == 5 ) {
                    //$coupon_info=$GLOBALS['db']->getAll("select id,deal_type,refund_status,end_time from " . DB_PREFIX . "deal_coupon where order_id=".$order_id." and deal_id=".$v['deal_id']);
                    if($v['is_shop'] == 1 || $v['is_coupon']==0 || $v['order_status']==1){
                        $item_list[]=$v;
                    }
                } else {
                    $item_list[] = $v;
                }
            }
            /* //可评价的团购商品或自提商品
            $coupon = $GLOBALS['db']->getAll("select deal_id,count(deal_id) as count from " . DB_PREFIX . "deal_coupon where order_id=" . $order_id);
            foreach ($coupon as $t => $v) {
                $confirm = $GLOBALS['db']->getOne("select count(*) as count from " . DB_PREFIX . "deal_coupon where order_id=" . $order_id . " and deal_id=" . $v['deal_id'] . " and confirm_time<>0");
                $item = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "supplier_location_dp where order_id=" . $order_id . " and deal_id=" . $v['deal_id'] . " and user_id=" . $user_id);
                if ($v['count'] == $confirm && !$item && $v['count'] != 0) {
                    $deal_id[] = $v['deal_id'];
                }
            } */
            //$item_id = implode(",", $item_id);
//            $deal_info = $GLOBALS['db']->getAll("select id,img,name from " . DB_PREFIX . "deal where id in($deal_id)");
            foreach ($item_list as $k => $v) {
                 $item_list[$k]['deal_icon'] = get_abs_img_root(get_spec_image($v['deal_icon'], 280, 280, 1));
            }
        }

        $root['order_id'] = $order_id;
        $root['item'] = $item_list ? $item_list : array();
        $root['page_title'] = "发表评价";
        return output($root);
    }

    /**
     * 订单评价提交
     * @return unknown_type
     */
    public function order_dp_do()
    {
        $root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $order_id = intval($GLOBALS['request']['order_id']);
        $content = $GLOBALS['request']['content'];
        $point = $GLOBALS['request']['point'];
        //$deal_id = $GLOBALS['request']['deal_id'];
        $item_id = $GLOBALS['request']['item_id'];
        
        if(APP_INDEX=="app"){ 
            if(!is_array($content)){
                $content=json_decode($content);
                $array=array();
                if (is_object($content)) {
                    foreach ($content as $key => $value) {
                        $array[$key] = $value;
                    }
                    $content=$array;
                }
            }
            
            if(!is_array($point)){
                $point=json_decode($point);
                $array=array();
                if (is_object($point)) {
                    foreach ($point as $key => $value) {
                        $array[$key] = $value;
                    }
                    $point=$array;
                }
            }
            
            if(!is_array($item_id)){
                $item_id=explode(",",$item_id);
            }
        }
        
        $user_login_status = check_login();

        if ($user_login_status != LOGIN_STATUS_LOGINED) {
            $root['user_login_status'] = $user_login_status;
            return output("", 0, "请先登录");
        } else {
            $root['user_login_status'] = $user_login_status;
            foreach ($content as $v) {
                if (strim($v) == "") {
                    return output($root, 0, "请填写评价内容");
                }
            }
            foreach ($point as $v) {
                if (intval($v) <= 0) {
                    return output($root, 0, "请选择评分");
                }
            }
            
            $item_sql="select doi.id,doi.deal_id,doi.dp_id,doi.supplier_id,doi.location_id,doi.delivery_status,do.order_status from " . DB_PREFIX . "deal_order_item as doi left join " . DB_PREFIX . "deal_order as do on do.id=doi.order_id where doi.order_id=".$order_id." and doi.id in (".implode(",",$item_id).") and (doi.refund_status<>2 or (do.type=5 and do.order_status=1)) and (doi.is_arrival=1 or doi.consume_count>0 or (doi.is_shop=0 and doi.is_coupon=0)) and doi.dp_id=0 and do.user_id=".$user_id;
            $order_item = $GLOBALS['db']->getAll($item_sql);
            
            foreach ($order_item as $t => $v) {
                //$item=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp where order_id=".$order_id." and deal_id=".$v['deal_id']." and user_id=".$user_id);
                if ( $v['delivery_status'] == 5 && $v['is_shop']==0 && $v['is_coupon']==1 && $v['order_status']==0) {
                    
                    return output($root, 0, "提交数据有误");

                }else{
                    break;
                }
            }
            
            if(count($order_item)!=count($item_id)){
                return output($root, 0, "提交数据有误");
            }
            
            foreach ($order_item as $k=>$v){
                if(!in_array($v['id'], $item_id)){
                    return output($root, 0, "提交数据有误");
                }
                $order_item_key[$v['id']]=$v;
            }
            
            /* foreach ($item_id as $v) {
                if($order_item_key[$v]['dp_id']>0){
                    return output($root, 1, "商品已评价");
                }
            } */
            
            //$supplier_arr=array();
            require_once(APP_ROOT_PATH."system/model/review.php");
            
            foreach ($item_id as $v) {
                
//                 $dp_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "supplier_location_dp where order_id=" . $order_id . " and deal_id=" . $v . " and user_id=" . $user_id);
//                 logger::write(print_r($dp_count,1));exit;
//                 if ($dp_count) {
//                     return output($root, 1, "请勿重复提交");
//                 }
                $location = $order_item_key[$v]['location_id'];//$GLOBALS['db']->getOne("select location_id from " . DB_PREFIX . "deal_order_item where order_id=".$order_id." and id=".$v);
                if($location){
                    $data['supplier_location_id'] = $location;
                }else{
                //后台发货获取门店id
                    $location = $GLOBALS['db']->getAll("select location_id from " . DB_PREFIX . "deal_location_link where deal_id=" . $deal_id);
                    if (count($location) > 1) {
                        $location_id = array();
                    foreach ($location as $vv) {
                        $location_id[] = $vv["location_id"];
                    }
                    $location_id = implode(",", $location_id);
                    $main_location = $GLOBALS['db']->getRow("select id from " . DB_PREFIX . "supplier_location where id in(" . $location_id . ") and is_main=1");
                    if ($main_location) {
                        $data['supplier_location_id'] = $main_location["id"];
                    } else {
                        $data['supplier_location_id'] = $location[0]['location_id'];
                    }
                    
                    }
                    else if($location){
                        $data['supplier_location_id'] = $location[0]['location_id'];
                    }
                    else{
                        $data['supplier_location_id'] = 0;
                    }
                }
                
                $result = save_review($user_id,array("deal_id"	=> $order_item_key[$v]['deal_id'],"location_id"	=>$data['supplier_location_id'],"order_item_id"=>$v),$content[$v],$point[$v]);
                /* $data = array();
                $deal_id=$order_item_key[$v]['deal_id'];
                $data['deal_id'] = $deal_id;
                $data['order_id'] = $order_id;
                $data['user_id'] = $user_id;
                $data['content'] = $content[$v];
                $data['create_time'] = NOW_TIME;
                $data['point'] = $point[$v];
                $data['is_content'] = 1;
                $data['status'] = 1; */
                //$supplier = $GLOBALS['db']->getRow("select supplier_id from " . DB_PREFIX . "deal_order_item where id=" . $v);
                //$data['supplier_id'] = $order_item_key[$v]['supplier_id'];//$supplier['supplier_id'];
                /* if(!in_array($supplier_arr, $data['supplier_id'])){
                    $supplier_arr[]=$data['supplier_id'];
                } */
                
                //商家端发货获取门店id
               /*  $location = $order_item_key[$v]['location_id'];//$GLOBALS['db']->getOne("select location_id from " . DB_PREFIX . "deal_order_item where order_id=".$order_id." and id=".$v);
                if($location){
                    $data['supplier_location_id'] = $location;
                }else{ */
                    //后台发货获取门店id
                    /* $location = $GLOBALS['db']->getAll("select location_id from " . DB_PREFIX . "deal_location_link where deal_id=" . $deal_id);
                    if (count($location) > 1) {
                        $location_id = array();
                        foreach ($location as $vv) {
                            $location_id[] = $vv["location_id"];
                        }
                        $location_id = implode(",", $location_id);
                        $main_location = $GLOBALS['db']->getRow("select id from " . DB_PREFIX . "supplier_location where id in(" . $location_id . ") and is_main=1");
                        if ($main_location) {
                            $data['supplier_location_id'] = $main_location["id"];
                        } else {
                            $data['supplier_location_id'] = $location[0]['location_id'];
                        }
    
                    } 
                    else if($location){
                        $data['supplier_location_id'] = $location[0]['location_id'];
                    } 
                    else{
                        $data['supplier_location_id'] = 0;
                    }
                } */
                
                /* $GLOBALS['db']->autoExecute(DB_PREFIX . "supplier_location_dp", $data, "INSERT");

                $dp_id = $GLOBALS['db']->insert_id();
                if ($dp_id) {
                    //计算总点评1-5星人数
                    //$item_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal where id = " . $deal_id);
                    $item_data=array();
                    $sql = "select count(*) as total,point from " . DB_PREFIX . "supplier_location_dp  where deal_id = " . $deal_id . " group by point ";
                    $data_result = $GLOBALS['db']->getAll($sql);
                    foreach ($data_result as $kk => $vv) {
                        $item_data['dp_count_' . $vv['point']] = $vv['total'];
                    }

                    $GLOBALS['db']->autoExecute(DB_PREFIX . "deal", $item_data, "UPDATE", " id = " . $deal_id . " ");
                    syn_deal_review_count($deal_id);
                }

                $GLOBALS['db']->autoExecute(DB_PREFIX . "deal_order_item", array("dp_id" => $dp_id), "UPDATE", " order_id=" . $order_id . " and id=" . $v); */

            }

            update_order_cache($order_id);
            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            distribute_order($order_id);
            /*foreach ($supplier_arr as $v){
                // 通知商户
                send_supplier_msg($v, 'dp', $order_id);
            }*/

            // 如果订单商品全部评价。通知商户
            $sql = 'select count(id) from '.DB_PREFIX.'deal_order_item where order_id='.$order_id.' and dp_id=0';
            $undp = $GLOBALS['db']->getOne($sql);
            if ($undp == 0) {
                // logger::write(json_encode($order_item['supplier_id']), 'INFO', 3, 'biz_log');
                send_supplier_msg($order_item['supplier_id'], 'dp', $order_id);
            }
            return output($root, 1, "评价成功");
        }
    }

    /**
     * 输入：
     * data_id:int 订单id
     * @return array
     */
    public function wap_view()
    {
        $root = array();
        /*参数初始化*/

        //检查用户, 用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $data_id = intval($GLOBALS['request']['data_id']);

        // 条件判断重写
        $condition = " and do.id =" . $data_id;
        /*$id = intval($GLOBALS['request']['id']);
			if($id>0)
			$condition.=" and do.id = ".$id." ";*/

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {

            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $order_table_name = get_user_order_table_name($user_id);

            $sql = "select do.*,min(d.dp_id) as is_dp,d.is_coupon  from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn where not ((do.pay_status=2 and do.is_delete=1) or (do.pay_status<>2 and do.is_delete=1 and do.is_cancel=0)) and " .
                " do.user_id = " . $user_id . " and do.type != 1 " . $condition . " GROUP BY id  ";
            $v = $GLOBALS['db']->getRow($sql);
            $buy_type=0;
            if ($v) {
                //要返回的字段
                //echo "<pre>";print_r();exit;
                $order_item = array();
                $order_item['id'] = $v['id'];
                $order_item['order_sn'] = $v['order_sn'];
				$order_item['type'] = $v['type'];
                $order_item['is_cancel'] = $v['is_cancel'];
                $order_item['order_status'] = $v['order_status'];
                $order_item['pay_status'] = $v['pay_status'];
                $order_item['delivery_status'] = $v['delivery_status'];
                $order_item['delivery_id'] = $v['delivery_id'];
                $order_item['memo'] = $v['memo'];
                if ($order_item['delivery_status'] != 5) {
					$delivery_region_arr=array();
					$delivery_region_arr[]=$v['region_lv1'];
					$delivery_region_arr[]=$v['region_lv2'];
					$delivery_region_arr[]=$v['region_lv3'];
					$delivery_region_arr[]=$v['region_lv4'];
					$delivery_region_list=$GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "delivery_region where id in (".implode(',',$delivery_region_arr).")");
					$region_list=array();
					foreach($delivery_region_list as $delivery_region_item){
						$region_list[$delivery_region_item['id']]=$delivery_region_item;
					}
                    //$order_item['address'] = $v['address'];
					$order_item['address'] = $region_list[$v['region_lv1']]['name'].$region_list[$v['region_lv2']]['name'].$region_list[$v['region_lv3']]['name'].$region_list[$v['region_lv4']]['name'].$v['address'].$v['street'].$v['doorplate'];
                    $order_item['mobile'] = $v['mobile'];
                    $order_item['consignee'] = $v['consignee'];
					if($v['delivery_id']>0){
						$order_item['delivery_info'] = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "delivery where id = " . $v['delivery_id']);
					}
                }
				$order_item['consignee_id']=$v['consignee_id'];
				$order_item['location_id']=$v['location_id'];
				if($order_item['location_id']>0){
					$location=$GLOBALS['db']->getRow("select name,address,tel from ".DB_PREFIX."supplier_location where id=".$order_item['location_id']);
					$order_item['location_name']=$location['name'];
					$order_item['location_address']=$location['address'];
					$order_item['tel']=$location['tel'];
					$order_item['location_address_url']=SITE_DOMAIN.wap_url('index','position',array('location_id'=>$order_item['location_id']));
				}
                $order_item['create_time'] = to_date($v['create_time']);
                $order_item['pay_amount'] = round($v['pay_amount'], 2);
				$order_item['app_format_pay_amount']=format_price_html($v['pay_amount'],3);
                $order_item['total_price'] = round($v['total_price'],2);
				$order_item['app_format_total_price']=format_price_html(round($v['total_price'], 2),3);
                $order_item['deal_total_price'] = round($v['deal_total_price']-$v['discount_price'], 2); //订单中的商品总价
                $order_item['discount_price'] = round($v['discount_price'], 2); //享受的会员折扣价
                $order_item['delivery_fee'] = round($v['delivery_fee'], 2); //实际运费
                $order_item['record_delivery_fee'] = round($v['record_delivery_fee'], 2); //记录的运费
                $order_item['ecv_money'] = round($v['ecv_money'], 2); //代金券支付部份的金额
                $order_item['youhui_money'] = round($v['youhui_money'], 2); //代金券支付部份的金额
				$order_item['cod_money'] = round($v['cod_money'], 2); //货到付款的金额
				$order_item['cod_mode'] = $v['cod_mode'];//货到付款的方式
                $order_item['promote_arr'] = unserialize($v['promote_arr']); //享受的促销信息
                // $order_item['promote_arr'] = unserialize($v['promote_arr']); //享受的促销信息
                $order_item['payment_fee'] = round($v['payment_fee'], 2); //支付方式所耗的手续费
				$order_item['exchange_money'] = round($v['exchange_money'], 2); //积分抵现的折扣
                $order_item['buy_type'] = 0;
                $order_item['is_delete'] = $v['is_delete'];
                $order_item['return_total_score']=$v['return_total_score'];
				$order_item['is_coupon']=$v['is_coupon'];
                if ($v['return_total_score'] < 0) {
                    $order_item['buy_type'] = 1;
                    $buy_type=1;
                    $order_item['return_total_score'] = round($v['return_total_score'], 2);
                }
                if ($v['deal_order_item']) {
                    $list['deal_order_item'] = unserialize($v['deal_order_item']);
                } else {
                    $order_id = $v['id'];
                    update_order_cache($order_id);
                    $list['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $order_id);
                }
                $c = 0;
                $order_status_id=100;
				$order_item['is_groupbuy_or_pick']=1;
				$order_item['existence_expire_refund']=0;
				$order_item['check_logistics_status']=0;
                foreach ($list['deal_order_item'] as $kk => $vv) {
                    //$deal = load_auto_cache("deal",array("id"=>$vv['deal_id']));
                    //echo "<pre>";print_r($deal);exit;
                    $c += intval($vv['number']);
                    $deal_item = array();
                    $deal_item['id'] = $vv['id'];
                    $deal_item['deal_id'] = $vv['deal_id'];
                    $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 74, 74, 1));
                    $deal_item['name'] = htmlspecialchars_decode($vv['name']);
                    $deal_item['sub_name'] = htmlspecialchars_decode($vv['sub_name']);
                    $deal_item['number'] = $vv['number'];
                    $deal_item['unit_price'] = round($vv['discount_unit_price'], 2);
                    $deal_item['discount_unit_price'] = round($vv['discount_unit_price'], 2);
					$deal_item['app_format_unit_price']=format_price_html($vv['discount_unit_price'],3);
                    $deal_item['total_price'] = round($vv['total_price'], 2);
                    $deal_item['buy_type'] = $vv['buy_type'];
                    if ($deal_item['buy_type'] == "1") {
                        $deal_item['return_score'] = round(abs($vv['return_score']), 2);
                        $deal_item['return_total_score'] = round(abs($vv['return_total_score']), 2);
                    }
                    $deal_item['consume_count'] = intval($vv['consume_count']);
                    $deal_item['dp_id'] = intval($vv['dp_id']);
                    $deal_item['delivery_status'] = intval($vv['delivery_status']);
                    $deal_item['is_arrival'] = intval($vv['is_arrival']);
                    if (!($v['is_delete'] == 1 && $v['pay_status'] != 2)) {
                        if (!$order_item['is_check_logistics']) {
                            if ($deal_item['delivery_status'] == 1) { //存在已发货的商品
                                $order_item['is_check_logistics'] = 1; //查看物流
								if($deal_item['is_arrival']==0){//未收货
									if($order_item['consignee_id']>0){//需要配送的商品
										$order_item['check_logistics_status']=max(4,$order_item['check_logistics_status']);
									}else{//无需配送的商品
										$order_item['check_logistics_status']=max(2,$order_item['check_logistics_status']);
									}
								}elseif($deal_item['is_arrival']==1){//已收货
									if($order_item['consignee_id']>0){//需要配送的商品
										$order_item['check_logistics_status']=max(3,$order_item['check_logistics_status']);
									}else{//无需配送的商品
										$order_item['check_logistics_status']=max(1,$order_item['check_logistics_status']);
									}
								}
                            }
                        }
                        if (!$order_item['is_pick']) {
                            if ($vv['is_pick'] == 1) { //自提
                                $order_item['is_pick'] = 1;
                            }
                        }
                        if (!$order_item['is_dp']) {
                            if ($v['order_status'] == 1) {
                                if ($vv['dp_id'] == 0) { //未点评，已有使用数量
									if($vv['consume_count'] > 0){
										$order_item['is_dp'] = 1; //评价
									}elseif($vv['is_shop']==0&&$vv['is_coupon']==0){
										$order_item['is_dp'] = 1; //评价
									}
                                }
                            } else {
                                if ($vv['delivery_status'] == 1 && $vv['is_arrival'] == 1 && $vv['dp_id'] == 0) { //未点评，已发货，已收货
                                    $order_item['is_dp'] = 1;
                                }
                                if($vv['is_coupon']==1 && $vv['dp_id'] == 0){
                                    $coupon_info=$GLOBALS['db']->getAll("select id,deal_type,refund_status from " . DB_PREFIX . "deal_coupon where order_id=".$vv['order_id']." and order_deal_id=".$vv['id']);
                                    if($coupon_info[0]["deal_type"]==1 || $vv['is_shop'] == 1){
                                        if($vv['consume_count']>0){
                                            $order_item['is_dp'] = 1;
                                        }
                                    }else{
                                        $refund_num=0;
                                        foreach ($coupon_info as $vvv){
                                            if($vvv['refund_status']==2){
                                                $refund_num++;
                                            }
                                        }
                                        if($vv['number']==($vv['consume_count']+$refund_num) && $vv['number']!=$refund_num){
                                            $order_item['is_dp'] = 1;
                                        }
                                    }
                                }
                            }

                        }
						if ($order_item['is_groupbuy_or_pick']) {//0为待发货
							if($vv['is_shop']==1){
								if($vv['delivery_status']==0){
									$order_item['is_groupbuy_or_pick']=0;
								}elseif($vv['delivery_status']==5&&$vv['is_pick']==0){
									$order_item['is_groupbuy_or_pick']=0;
								}
							}
						}
                        if (!$order_item['is_refund']) {
                            if ($vv['is_shop'] == 0) { //是团购
                                $coupon = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $vv['id'] . " and is_balance=0 and refund_status = 0 and is_valid=1 and ((any_refund=1 and (end_time=0 or end_time>" . NOW_TIME . ")) or (expire_refund=1 and ( end_time<" . NOW_TIME . " and end_time<>0)   ) )   ");
                                //echo "select count(*) from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $vv['id'] . " and is_balance=0 and refund_status = 0 and ((any_refund=1 and (end_time=0 or end_time<" . NOW_TIME . ")) or (expire_refund=1 and ( end_time<" . NOW_TIME . " or end_time<>0)   ) )   ";exit;
                                //logger::write("select * from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $vv['id'] . " and is_balance=0 and refund_status = 0 and ((any_refund=1 and (end_time=0 or end_time<" . NOW_TIME . ")) or (expire_refund=1 and ( end_time<" . NOW_TIME . " or end_time<>0)   ) )   ");
                                if (intval($coupon)) {
                                    //logger::write(111);
                                    $order_item['is_refund'] = 1;
                                }
                            } else {
                                if ($vv['is_refund'] == 1 && $vv['refund_status'] == 0) {
                                    if ($vv['is_pick'] == 1) {
                                        if ($vv['consume_count'] < 1) {
                                            $order_item['is_refund'] = 1;
                                        }
                                    } else {
                                            if ($vv['is_arrival'] != 1 && $v['total_price'] <= $v['pay_amount']) {
                                                $order_item['is_refund'] = 1;
                                            }

                                    }
                                }
                            }
                        }

                    }
                    
                    //获得订单商品状态
                    $order_deal_status=$this->order_deal_status($v,$vv,$order_item['existence_expire_refund']);
                    $deal_item['deal_orders']=$order_deal_status['deal_orders'];
                    $deal_orders_id=$order_deal_status['deal_orders_id'];
                    if($order_item['existence_expire_refund']!=1){
                    	$order_item['existence_expire_refund']=$order_deal_status['existence_expire_refund'];
                    }
                    if($order_status_id>$deal_orders_id){
                    	$order_status_id=$deal_orders_id;
                    }
					
                    $deal_item['is_refund'] = intval($vv['is_refund']);
                    $deal_item['refund_status'] = intval($vv['refund_status']);
                    $deal_item['supplier_id'] = intval($vv['supplier_id']);
                    $deal_item['attr_str'] = $vv['attr_str'];

                    if (!is_array($order_item['deal_order_item'][$deal_item['supplier_id']])) {
                        if ($deal_item['supplier_id'] == 0) {
							if($order_item['type']==4){
								$order_item['deal_order_item']['0']['supplier_name'] = "平台自营_驿站配送";
							}else{
								$order_item['deal_order_item']['0']['supplier_name'] = "平台自营";
							}
                            $order_item['deal_order_item']['0']['count'] = 1;
                        } else {
                            $supplier_info = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id = " . $deal_item['supplier_id']);
                            $order_item['deal_order_item'][$deal_item['supplier_id']]['supplier_name'] = $supplier_info['name'];
                            $order_item['deal_order_item'][$deal_item['supplier_id']]['count'] = 1;
                        }

                    } else {
                        $order_item['deal_order_item'][$deal_item['supplier_id']]['count']++;
                    }
                    $order_item['deal_order_item'][$deal_item['supplier_id']]['list'][] = $deal_item;

                    //$order_item['deal_order_item'][$kk] = $deal_item;
                }
				$order_item['deal_order_item']=array_values($order_item['deal_order_item']);
                $order_item['count'] = $c;
                if (!$order_item['is_check_logistics']) {
                    $order_item['is_check_logistics'] = 0;
                }
                if (!$order_item['is_coupon']) {
                    $order_item['is_coupon'] = 0;
                }
                if (!$order_item['is_dp']) {
                    $order_item['is_dp'] = 0;
                }
                if (!$order_item['is_refund']) {
                    $order_item['is_refund'] = 0;
                }
                //开始处理订单状态
                $order_status = "";
                /*if($v['order_status'] == 1) { //结单的订单显示说明
    			 $order_status = "订单已完结";
    			} else {
    			if($v['pay_status'] != 2) {
    			$order_status = "未支付";
    			} else {
    			$order_status = "已支付";
    			}
    			}*/
                $order_item['is_del'] = 0;
				$order_item['is_pay'] = 0;
                if ($v['is_delete'] != 1/* and $v['refund_status'] != 2*/) {
                    if ($v['refund_status'] == 2) {
                        $order_status = '已取消';
                    }
                    if ($v['order_status'] == 1 && $v['is_dp'] > 0) {
                        $order_status = "已完成";
                        $order_item['is_del'] = 1;
                    }
                    if ($v['order_status'] == 1 && $v['is_dp'] == 0) {
                        $order_status = "待评价";
                        $order_item['is_del'] = 1;
                    }
					if ($order_status_id == 4) {
                    	$order_status = '已完成';
                    }elseif ($order_status_id == 3){
                    	$order_status = '待评价';
                    }elseif ($order_status_id == 5){
                    	$order_status = '已取消';
                    }elseif($order_status_id==2){
                    	$order_status = "待确认";
                    }elseif($order_status_id==1){
                    	$order_status = "待发货";
                    }elseif($order_status_id==3.5){
                    	$order_status = "退款中";
                    }
                    //if (($v['delivery_status'] == 2 || $v['delivery_status'] == 5) && $v['order_status'] == 0) {
                    //    $order_status = "待确认";
                    //}
                    //if ($v['delivery_status'] == 0&&$v['refund_status'] != 2 || $v['delivery_status'] == 1||($v['delivery_status']==5&&$order_item['is_groupbuy_or_pick']==0)) {
                    //    $order_status = "待发货";
                    //}
                    if ($v['pay_status'] != 2) {
						$order_item['is_pay']=1;
                        $order_status = "待付款";
                    }
                } else {
                    $order_status = "已取消";
                }
                $order_item['status_name'] = $order_status;

				$fee=order_fee_arr($order_item);
                $order_item['youhui_price'] = $fee['youhui_price'];
				$order_item['app_format_youhui_price']=format_price_html($order_item['youhui_price'],3);
                $order_item['is_delivery'] = 0;//$is_delivery;
                $order_item['order_total_price'] = $fee['order_total_price'];
				$order_item['app_format_order_total_price']=format_price_html($order_item['order_total_price'],3);

                $order_item['order_pay_price'] = $fee['order_pay_price'];
				$order_item['app_format_order_pay_price']=format_price_html($order_item['order_pay_price'],3);
                
                $order_item['feeinfo'] = $fee['feeinfo'];
                $order_item['paid'] = $fee['paid'];
                $operation=array();
                if ($order_item['pay_status']==2) {
                    if ($order_item['is_check_logistics'] == 1 && $order_item['type']!=4) {
						$name="收货";//$this->check_logistics_status($order_item['check_logistics_status']);//"物流&收货";
                        $operation[] = array(
                            'name' => $name,
                            'type' => "j-logistics|goodsreceipt",
                            'url' => wap_url("index", "uc_order#logistics", array('data_id' => $order_item['id'])),
							'param'=>array('data_id' => $order_item['id'])
                        );
                    }
                    if ($order_item['is_coupon'] == 1|| ($order_item['type']==4 && $order_item['delivery_status']==2) ) {
                        $arr = array();
                        $arr['order_id'] = $order_item['id'];
                        if ($order_item['is_pick'] == 1) {
                            $arr['coupon_status'] = 1;
                        }
						if ($order_item['type']==4 && $order_item['delivery_status']==2) {
                            $arr['coupon_status'] = 2;
                        }
                        $operation[] = array(
                            'name' => "查看".app_conf("COUPON_NAME"),
                            'type' => "j-coupon",
                            'url' => wap_url("index", "uc_coupon", $arr),
							'param'=>$arr
                        );
                    }
                    if ($order_item['is_dp'] == 1) {
                        $operation[] = array(
                            'name' => "评价",
                            'type' => "j-dp",
                            'url' => wap_url("index", "uc_order#order_dp", array('id' => $order_item['id'])),
							'param'=>array('id' => $order_item['id'])
                        );
                    }
                    if ($order_item['is_refund'] == 1) {
                        $operation[] = array(
                            'name' => "退款",
                            'type' => "j-refund",
                            'url' => wap_url("index", "uc_order#order_refund", array('data_id' => $order_item['id'])),
							'param'=>array('id' => $order_item['id'])
                        );
                    }
                    if($order_item['pay_status']==0&&$order_item['is_cancel']==0){
                        $operation[] = array(
                            'name' => "去支付",
                            'type' => "j-payment",
                            'url' => wap_url("index", "cart#pay", array('id' => $order_item['id'])),
							'param'=>array('id' => $order_item['id'])
                        );
                        $operation[] = array(
                            'name' => "取消订单",
                            'type' => "j-cancel",
                            'url' => wap_url("index", "uc_order#cancel", array('id' => $order_item['id'],'is_cancel'=>1)),
                            'param'=>array('id' => $order_item['id'],'is_cancel'=>1)
                        );
                    }
                    if ($order_item['is_del'] == 1||$order_item['is_cancel']==1) {
                        $operation[] = array(
                            'name' => "删除订单",
                            'type' => "j-del",
                            'url' => wap_url("index", "uc_order#cancel", array('id' => $order_item['id'])),
							'param'=>array('id' => $order_item['id'])
                        );
                    }
                } else {
                    if ($order_item['is_cancel'] == 1) {
                        $operation[] = array(
                            'name' => "删除订单",
                            'type' => "j-del",
                            'url' => wap_url("index", "uc_order#cancel", array('id' => $order_item['id'])),
                            'param'=>array('id' => $order_item['id'])
                        );
                    } else {
                        $operation[] = array(
                            'name' => "去支付",
                            'type' => "j-payment",
                            'url' => wap_url("index", "cart#pay", array('id' => $order_item['id'])),
							'param'=>array('id' => $order_item['id'])
                        );
						if($v['is_main']!=1){
							$operation[] = array(
								'name' => "取消订单",
								'type' => "j-cancel",
								'url' => wap_url("index", "uc_order#cancel", array('id' => $order_item['id'],'is_cancel'=>1)),
								'param'=>array('id' => $order_item['id'],'is_cancel'=>1)
							);
						}
                    }
                }
                if(empty($operation)){
                    $operation[]=array('name'=>"暂无操作",'type'=>"center-none");
                }
                $order_item['operation'] = $operation;
				//echo "<pre>";print_r($operation);exit;
                if($order_item['cod_money']>0){

                    $order_item['payment_info']=$GLOBALS['db']->getRow("select pn.id,pn.money,pn.payment_config,p.class_name,p.name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id=p.id where order_id = ".$order_item['id']." and p.class_name='Cod' and pn.is_paid=1");
                    if($order_item['payment_info']){
                        $rel=get_payment_name_rel($order_item['cod_mode']);
                        $order_item['payment_info']['name']=$order_item['payment_info']['name'].$rel;
                    }else{
                        $order_item['payment_info']['name']="货到付款(现金)";
                        $order_item['payment_info']['money']=$order_item['cod_money'];
                    }
                    $order_item['payment_info']['money']=format_price($order_item['payment_info']['money']);
                }

                // 发票信息
                if ($v['invoice_info']) {
                    $order_item['invoice_info'] = unserialize($v['invoice_info']);
                }
                

                $root['item'] = $order_item;

                // 未支付的查询
                // $not_pay_sql = "select count(*) from ".$order_table_name." as do where do.is_delete = 0 and do.user_id = ".$user_id." and do.type = 0 and do.pay_status <> 2";
                // $notPayNum = $GLOBALS['db']->getOne($not_pay_sql);
                $notPayNum = countNotPayOrder($user_id);
                $root['not_pay'] = $notPayNum;
                $root['buy_type']=$buy_type;
				$status=1;
				$info = '';
            } else {
                $root['item'] = null;//array();
				$status=0;
				$info = '订单不存在，或已删除';
            }
            
        }
        $root['user_login_status'] = $user_login_status;

        // $root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";

        $root['page_title'] = "订单详情";
        //echo "<pre>";print_r($root);exit;
		return output($root, $status,$info);
    }

    /*选择退款*/
    public function order_refund()
    {
        $root = array();
        /*参数初始化*/

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $data_id = intval($GLOBALS['request']['data_id']);

        // 条件判断重写
        $condition = " and do.id =" . $data_id;
        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {

            require_once(APP_ROOT_PATH . "system/model/deal_order.php");
            $order_table_name = get_user_order_table_name($user_id);

            $sql = "select do.*,min(d.dp_id) as is_dp  from " . $order_table_name . " as do left join " . DB_PREFIX . "deal_order_item AS d on do.order_sn=d.order_sn where not (do.pay_status=2 and do.is_delete=1 and d.refund_status=3 ) and" .
                " do.user_id = " . $user_id . " and do.type != 1 and do.type != 2 " . $condition . " GROUP BY id  ";
            $v = $GLOBALS['db']->getRow($sql);
            //要返回的字段
            //echo "<pre>";print_r();exit;
            $order_item = array();
            $order_item['id'] = $v['id'];
            $order_item['order_sn'] = $v['order_sn'];
            $order_item['order_status'] = $v['order_status'];
            $order_item['pay_status'] = $v['pay_status'];
            $order_item['delivery_status'] = $v['delivery_status'];
            $order_item['delivery_id'] = $v['delivery_id'];
            $order_item['create_time'] = to_date($v['create_time']);
            $order_item['pay_amount'] = round($v['pay_amount'], 2);
            $order_item['total_price'] = round($v['total_price'], 2);
            $order_item['deal_total_price'] = round($v['deal_total_price'], 2); //订单中的商品总价
            $order_item['discount_price'] = round($v['discount_price'], 2); //享受的会员折扣价
            $order_item['delivery_fee'] = round($v['delivery_fee'], 2); //实际运费
            $order_item['record_delivery_fee'] = round($v['record_delivery_fee'], 2); //记录的运费
            $order_item['ecv_money'] = round($v['ecv_money'], 2); //代金券支付部份的金额
            $order_item['promote_arr'] = unserialize($v['promote_arr']); //享受的促销信息
            $order_item['promote_arr'] = unserialize($v['promote_arr']); //享受的促销信息
            $order_item['payment_fee'] = round($v['payment_fee'], 2); //支付方式所耗的手续费
            $order_item['buy_type'] = 0;
            $order_item['is_delete'] = $v['is_delete'];
            if ($v['return_total_score'] < 0) {
                $order_item['buy_type'] = 1;
                $order_item['return_total_score'] = round(abs($v['return_total_score']), 2);
            }
            if ($v['deal_order_item']) {
                $list['deal_order_item'] = unserialize($v['deal_order_item']);
            } else {
                $order_id = $v['id'];
                update_order_cache($order_id);
                $list['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $order_id);
            }
            //logger::write(print_r($list['deal_order_item'],1));
            $c = 0;
            foreach ($list['deal_order_item'] as $kk => $vv) {
                if($vv['is_shop'] == 1 && ($vv['refund_status']!=0||$vv['is_arrival']==1) && $vv['is_pick'] != 1 ){
                    unset($list['deal_order_item'][$kk]);
                }
                elseif($vv['is_shop'] == 0 && $vv['is_pick'] != 1) {
                    $tuan_list = array();
                    $sql_tuan = "select do.*,cou.id,cou.password,cou.order_deal_id,cou.any_refund,cou.expire_refund,cou.is_balance as cou_is_balance,cou.refund_status as cou_refund_status,cou.end_time as cou_end_time from " . DB_PREFIX . "deal_order_item as do left join " . DB_PREFIX . "deal_coupon as cou on do.id=cou.order_deal_id where do.is_shop=0 and cou.refund_status=0 and cou.is_balance=0 and do.deal_id=cou.deal_id and do.deal_id=" . $vv['deal_id'] . " and cou.order_id=" . $data_id." and do.id=".$vv['id']." and ((cou.end_time >".NOW_TIME." and cou.any_refund=1 and cou.end_time!=0) or (cou.end_time <".NOW_TIME." and cou.expire_refund=1 and cou.end_time!=0) or (cou.end_time=0 and cou.any_refund=1))";
                    $tuan_list = $GLOBALS['db']->getAll($sql_tuan);
                    unset($list['deal_order_item'][$kk]);
                    foreach ($tuan_list as $kkk => $vvv) {
                        $list['deal_order_item'][] = $vvv;
                        $root['coupon_ids'][] = $vvv['id'];
                    }
                    //logger::write(print_r($tuan_list,1));
                }
                elseif($vv['is_pick'] == 1){
                    $pick_list = array();
                    $sql_pick = "select do.*,cou.id,cou.password,cou.order_deal_id,cou.any_refund,expire_refund from " . DB_PREFIX . "deal_order_item as do left join " . DB_PREFIX . "deal_coupon as cou on do.id=cou.order_deal_id where do.is_pick=1 and cou.refund_status=0 and cou.is_balance=0 and do.deal_id=cou.deal_id and do.deal_id=" . $vv['deal_id'] . " and cou.order_id=" . $data_id." and do.id=".$vv['id'];
                    $pick_list = $GLOBALS['db']->getAll($sql_pick);
                    //print_r($pick_list);exit;
                    unset($list['deal_order_item'][$kk]);
                    foreach ($pick_list as $kkk => $vvv) {
                        $list['deal_order_item'][] = $vvv;
                        $root['coupon_ids'][] = $vvv['id'];
                    }
                }
                else {
                    $root['deal_ids'][] = $vv['id'];
                }
            }
            //print_r($list);exit;
			$order_item['is_groupbuy_or_pick']=1;
            foreach ($list['deal_order_item'] as $kk => $vv) {
                //$deal = load_auto_cache("deal",array("id"=>$vv['deal_id']));
                //echo "<pre>";print_r($deal);exit;
                $c += intval($vv['number']);
                $deal_item = array();
                $deal_item['id'] = $vv['id'];
                $deal_item['password'] = $vv['password'];
                $deal_item['deal_id'] = $vv['deal_id'];
                $deal_item['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 122, 74, 1));
                $deal_item['name'] = $vv['name'];
                $deal_item['sub_name'] = $vv['sub_name'];
                $deal_item['number'] = $vv['number'];
                $deal_item['unit_price'] = round($vv['unit_price'], 2);
                $deal_item['total_price'] = round($vv['total_price'], 2);
                $deal_item['buy_type'] = $vv['buy_type'];
                $deal_item['is_pick'] = $vv['is_pick'];
                if($vv['is_shop']==0){
                    $deal_item['expire_refund'] = $vv['expire_refund'];
                    $deal_item['any_refund'] = $vv['any_refund'];
                }
                
                if ($deal_item['buy_type'] == "1") {
                    $deal_item['return_score'] = round(abs($vv['return_score']), 2);
                    $deal_item['return_total_score'] = round(abs($vv['return_total_score']), 2);
                }
                $deal_item['consume_count'] = intval($vv['consume_count']);
                $deal_item['dp_id'] = intval($vv['dp_id']);
                $deal_item['delivery_status'] = intval($vv['delivery_status']);
                $deal_item['is_shop'] = $vv['is_shop'];
                $deal_item['is_arrival'] = intval($vv['is_arrival']);
                if (!($v['is_delete'] == 1 && $v['pay_status'] != 2)) {
                    if (!$order_item['is_check_logistics']) {
                        if ($deal_item['delivery_status'] == 1) { //存在已发货的商品
                            $order_item['is_check_logistics'] = 1; //查看物流
                        }
                    }
                    if (!$order_item['is_coupon']) {
                        if ($vv['is_shop'] == 1 && $vv['is_pick'] == 1) { //是商品，且配送方式是自提
                            $order_item['is_coupon'] = 1;
                        }
                        if ($vv['is_shop'] == 0) { //是团购
                            $order_item['is_coupon'] = 1; //查看消费券
                        }
                    }
                    if (!$order_item['is_dp']) {
                        if ($v['order_status'] == 1) {
                            if ($vv['dp_id'] == 0 && $vv['consume_count'] > 0) { //未点评，已有使用数量
                                $order_item['is_dp'] = 1; //评价
                            }
                        } else {
                            if ($vv['delivery_status'] == 1 && $vv['is_arrival'] == 1 && $vv['dp_id'] == 0) { //未点评，已发货，已收货
                                $order_item['is_dp'] = 1;
                            }
                        }

                    }
					if ($order_item['is_groupbuy_or_pick']) {//0为待发货
						if($vv['is_shop']==1){
							if($vv['delivery_status']==0){
								$order_item['is_groupbuy_or_pick']=0;
							}elseif($vv['delivery_status']==5&&$vv['is_pick']==0){
								$order_item['is_groupbuy_or_pick']=0;
							}
						}
					}
                    if (!$order_item['is_refund']) {
                        if ($vv['is_shop'] == 0) { //是团购
                            //$coupon = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_coupon where id = " . $vv['id'] . " and is_balance=0 and refund_status = 0 and ((any_refund=1 and (end_time=0 or end_time<" . NOW_TIME . ")) or (expire_refund=1 and ( end_time<" . NOW_TIME . " or end_time<>0)   ) )   ");
                            $coupon = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $vv['order_deal_id'] . " and is_balance=0 and refund_status = 0 and ((any_refund=1 and (end_time=0 or end_time>" . NOW_TIME . ")) or (expire_refund=1 and ( end_time<" . NOW_TIME . " or end_time<>0)   ) )   ");
                            //logger::write(print_r($coupon,1));
                            if (intval($coupon)) {
                                $order_item['is_refund'] = 1;
                            }
                        } else {
                            if ($vv['is_refund'] == 1 && $vv['refund_status'] == 0) {

                                if ($vv['is_pick'] == 1) {
                                    if ($vv['consume_count'] < 1) {
                                        $order_item['is_refund'] = 1;
                                    }
                                } else {
                                    if ($vv['is_arrival'] != 1 && $v['total_price'] <= $v['pay_amount']) {
                                        $order_item['is_refund'] = 1;
                                    }
                                }
                            }
                        }
                    }

                }
            if ($v['total_price'] <= $v['pay_amount']) {
                if ($vv['is_shop'] == 0) { //是团购
                    	if($vv['cou_is_balance']==0&&($vv['cou_refund_status']==0||$vv['cou_refund_status']==3)&&($vv['cou_end_time']==0 || $vv['cou_end_time']>NOW_TIME)){
                    		$deal_item['deal_orders'] = "待使用";
                    		$deal_orders_id=2;
                    	}else if($vv['cou_refund_status']==1){
                    		$deal_item['deal_orders'] = "申请退款中";
                    		$deal_orders_id=0;
                    	}else if($vv['cou_refund_status']==2){
                    		$deal_item['deal_orders'] = "已退款";
                    		$deal_orders_id=5;
                    	
                    	}else{
                    		if($vv['consume_count']>0&&$vv['dp_id']==0){
                    			$deal_item['deal_orders'] = "待评价";
                    			$deal_orders_id=3;
                    		}elseif($vv['cou_is_balance']==1&&$vv['consume_count']>0&&$vv['dp_id']>0){
                    			$deal_item['deal_orders'] = "已完成";
                    			$deal_orders_id=4;
                    		}
                    		if($vv['cou_end_time'] < NOW_TIME && $vv['cou_end_time'] <> 0){
                    			$deal_item['deal_orders'] = "已过期";
                    			$deal_orders_id=4;
                    		}
                    	}
                    } else { //是商品
                        if ($vv['refund_status'] == 1) {
                            $deal_item['deal_orders'] = "申请退款中";
                        }
                        elseif ($vv['refund_status'] == 2) {
                            $deal_item['deal_orders'] = "已退款";
                        }
                        else {
                            if ($vv['is_pick'] == 1) {
                                if ($vv['consume_count'] < 1) {
                                    $deal_item['deal_orders'] = "待自提";
                                    
                                } else {
                                    if($vv['is_arrival']==0){
                                        $deal_item['deal_orders'] = "待收货";
                                    }
                                    else if ($vv['dp_id'] == 0) {
                                        $deal_item['deal_orders'] = "待评价";
                                    } else {
                                        $deal_item['deal_orders'] = "已完成";
                                    }
                                }
                            } else {
                                if ($vv['delivery_status'] == 5) {
                                    if($order_item['is_groupbuy_or_pick']==0){
                                        $deal_item['deal_orders'] = "待发货";
                                    }
                                    else if($vv['is_arrival']==0){
                                        $deal_item['deal_orders'] = "待收货";
                                    }
                                    else if ($vv['dp_id'] == 0) {
                                        $deal_item['deal_orders'] = "待评价";
                                    } else {
                                        $deal_item['deal_orders'] = "已完成";
                                    }
                                } elseif ($vv['delivery_status'] == 0) {
                                    $deal_item['deal_orders'] = "待发货";
                                } elseif ($vv['delivery_status'] == 1) {
                                    if ($vv['is_arrival'] == 0) {
                                        $deal_item['deal_orders'] = "待收货";
                                    } elseif ($vv['is_arrival'] == 1) {
                                        if ($vv['dp_id'] == 0) {
                                            $deal_item['deal_orders'] = "待评价";
                                        } else {
                                            $deal_item['deal_orders'] = "已完成";
                                        }
                                    } else {
                                        $deal_item['deal_orders'] = "待维权";
                                    }
                                }
                            }
                        }
                    }
                }

                $deal_item['is_refund'] = intval($vv['is_refund']);
                $deal_item['refund_status'] = intval($vv['refund_status']);
                $deal_item['supplier_id'] = intval($vv['supplier_id']);
                $deal_item['attr_str'] = $vv['attr_str'];

                if (!is_array($order_item['deal_order_item'][$deal_item['supplier_id']])) {
                    if ($deal_item['supplier_id'] == 0) {
                        $order_item['deal_order_item']['0']['supplier_name'] = "平台自营";
                        $order_item['deal_order_item']['0']['count'] = 1;
                    } else {
                        $supplier_info = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "supplier where id = " . $deal_item['supplier_id']);
                        $order_item['deal_order_item'][$deal_item['supplier_id']]['supplier_name'] = $supplier_info['name'];
                        $order_item['deal_order_item'][$deal_item['supplier_id']]['count'] = 1;
                    }

                } else {
                    $order_item['deal_order_item'][$deal_item['supplier_id']]['count']++;
                }
                $order_item['deal_order_item'][$deal_item['supplier_id']]['list'][] = $deal_item;

                //$order_item['deal_order_item'][$kk] = $deal_item;
            }
            $order_item['deal_order_item']=array_values($order_item['deal_order_item']);
            $order_item['c'] = $c;
            if (!$order_item['is_check_logistics']) {
                $order_item['is_check_logistics'] = 0;
            }
            if (!$order_item['is_coupon']) {
                $order_item['is_coupon'] = 0;
            }
            if (!$order_item['is_dp']) {
                $order_item['is_dp'] = 0;
            }
            if (!$order_item['is_refund']) {
                $order_item['is_refund'] = 0;
            }
            //开始处理订单状态
            $order_status = "";
            if ($v['is_delete'] == 0 || $v['refund_status'] != 2) {

                if ($v['order_status'] == 1 && $v['is_dp'] == 1) {
                    $order_status = "已完成";
                }
                if ($v['order_status'] == 1 && $v['is_dp'] == 0) {
                    $order_status = "待评价";
                }
                if (($v['delivery_status'] == 2 || $v['delivery_status'] == 5) && $v['order_status'] == 0) {
                    $order_status = "待确认";
                }
                if ($v['delivery_status'] == 0 || $v['delivery_status'] == 1||($v['delivery_status']==5&&$order_item['is_groupbuy_or_pick']==0)) {
                    $order_status = "待发货";
                }
                if ($v['pay_status'] != 2) {
                    $order_status = "待付款";
                }
            } else {
                $order_status = "已取消";
            }
            $order_item['status'] = $order_status;


            $order_item['youhui_price'] = $order_item['discount_price'] + $order_item['ecv_money']; //优惠价=会员折扣+代金券
            foreach ($order_item['promote_arr'] as $k => $v) {
                $v['config'] = unserialize($v['config']);
                $order_item['promote_arr'][$k]['config'] = $v['config'];
                if ($v['class_name'] == 'Discountamount' || $v['class_name'] == 'Appdiscount') {
                    $order_item['deal_total_price'] += $v['config']['discount_amount'];
                    $order_item['youhui_price'] += $v['config']['discount_amount']; //优惠价+=满减
                }
                if ($v['class_name'] == 'Freebynumber' || $v['class_name'] == 'Freebyprice' || $v['class_name'] == 'Freedelivery') {
                    $is_delivery = 1;
                    unset($order_item['promote_arr'][$k]);
                }
            }
            $root['item'] = $order_item;

        }

        $root['user_login_status'] = $user_login_status;
        //$root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_title'] = "选择退款商品";
        //logger::write(print_r($root,1));
        return output($root);
    }

    public function refund_list_old()
    {
        $root = array();

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);

        $page_title = '退款订单';

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {

            //分页
            $page = intval($GLOBALS['request']['page']);
            $page = $page == 0 ? 1 : $page;

            $page_size = PAGE_SIZE;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $case = " CASE doi.refund_status WHEN 1 THEN '退款申请' WHEN 2 THEN '已退款' WHEN 3 THEN '驳回申请' END status_str";

            $sql = 'SELECT doi.id, doi.deal_id,doi.discount_unit_price, doi.number, doi.unit_price, doi.total_price, doi.refund_status, doi.name, doi.attr_str, doi.deal_icon,' . $case . ', s.name as supplier_name, m.create_time FROM ' . DB_PREFIX . 'deal_order_item doi LEFT JOIN ' . DB_PREFIX . 'supplier s ON doi.supplier_id=s.id LEFT JOIN ' . DB_PREFIX . 'message m ON doi.message_id=m.id WHERE doi.user_id=' . $user_id . ' AND refund_status <> 0 ORDER BY doi.refund_status, m.create_time DESC LIMIT ' . $limit;
            $sql_count = 'SELECT count(id) FROM ' . DB_PREFIX . 'deal_order_item WHERE user_id=' . $user_id . ' AND refund_status <> 0 ';
            // print_r($sql);exit;
            $list = $GLOBALS['db']->getAll($sql);

            $data = array();
            if (count($list)) {
                $count = $GLOBALS['db']->getOne($sql_count);
                $page_total = ceil($count / $page_size);

                foreach ($list as $item) {
                    $item['unit_price'] = round($item['discount_unit_price'], 2);
                    $item['total_price'] = round($item['total_price'], 2);
                    $item['supplier_name'] = $item['supplier_name'] ? : '平台自营';
                    $item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));
                    $item['create_time'] = to_date($item['create_time']);

                    $data[] = $item;
                }
                //usort($data, $this->_sortStatus('refund_status'));

                $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
            }
            $root['item'] = $data;
        }

        $root['user_login_status'] = $user_login_status;

        //$root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_title'] = $page_title;

        return output($root);
    }

    public function refund_list()
    {
        $root = array();
        $page_title = '退款订单';

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            $user = $GLOBALS['user_info'];
            $user_id = intval($user['id']);
            //分页
            $page = intval($GLOBALS['request']['page']);
            $page = $page == 0 ? 1 : $page;
            $page_size = PAGE_SIZE;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $refund_str = array(
                '', '退款申请', '已退款', '驳回申请',
            );

            // 根据message_id 获取商品订单表（先排除团购的）和团购订单表的信息
            $sql = 'SELECT m.`id` mid, count(m.id) cid,doi.discount_unit_price, doi.`id`, doi.`number`, doi.`unit_price`, doi.`total_price`, doi.`refund_money` rm1, doi.`refund_status` rs1, dc.`refund_status` rs2, dc.`coupon_price`,dc.`order_deal_id`, sum(dc.`refund_money`) rm2
                 FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id` and doi.is_shop = 1
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.'
                GROUP BY m.id, doi.id
                ORDER BY m.id DESC LIMIT '.$limit;


            $sql_count = 'SELECT COUNT(m.id)
                 FROM '.DB_PREFIX.'message m
                LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id` and doi.is_shop = 1
                LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id`
                WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.'
                GROUP BY m.id, doi.id';
            $list = $GLOBALS['db']->getAll($sql);// print_r($sql);exit;

            $data = array();
            if (count($list)) {
                $counts = $GLOBALS['db']->getAll($sql_count);
                // print_r($sql_count);exit;
                $count = count($counts);
                $page_total = ceil($count / $page_size);

                $s_ids = array();
                $doid = array();
                foreach ($list as $item) {
                    // 获取相关的订单商品id
                    if ($item['id'] || $item['order_deal_id']) {
                        $doid[] = $item['id'] ?: $item['order_deal_id'];
                    }
                }
                // 另外获取商品的名称和商家id信息
                $doiSql = 'SELECT id, name, deal_icon, supplier_id FROM '.DB_PREFIX.'deal_order_item WHERE id in ('.implode(',', $doid).')';
                $doi = $GLOBALS['db']->getAll($doiSql);
                $format = array();
                foreach ($doi as $val) {
                    if ($val['supplier_id']) {
                        $s_ids[] = $val['supplier_id'];
                    }
                    $format[$val['id']] = $val;
                }

                $snameSql = 'SELECT id, name FROM '.DB_PREFIX.'supplier WHERE id in ('.implode(',', $s_ids).')';
                $slist = $GLOBALS['db']->getAll($snameSql);
                $fslist = array();
                $dealist = array();
                foreach ($slist as $s) {
                    $fslist[$s['id']] = $s['name'];
                }

                foreach ($list as $item) {
                    $item_id = $item['id'];
                    $unit_price = $item['discount_unit_price'];
                    if (empty($item['discount_unit_price'])) {
                        $item_id = $item['order_deal_id'];
                        $item['number'] = $item['cid'];
                        $unit_price = $item['coupon_price'];
                        $item['total_price'] = $item['coupon_price']*$item['cid'];
                    }
                    $item['name'] = $format[$item_id]['name'] ?: $format[$item['order_deal_id']]['name'];
                    $deal_icon = $format[$item_id]['deal_icon'] ?: $format[$item['order_deal_id']]['deal_icon'];
                    
                    $item['deal_icon'] = get_abs_img_root(get_spec_image($deal_icon, 122, 74, 1));
                    $item['unit_price'] = round($unit_price, 2);
                    $item['total_price'] = round($item['total_price'], 2);
                    $item['refund_money'] = round($item['rm1'] ?: $item['rm2'], 2);
                    // $item['supplier_name'] = $item['supplier_name'] ? : '平台自营';
                    $item['supplier_name'] = $fslist[$format[$item_id]['supplier_id']] ?: '平台自营';
                    // $item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));
                    // $item['create_time'] = to_date($item['create_time']);
                    // 退款状态
                    $rs_k = $item['rs1'] ? 'rs1' : 'rs2';
                    $item['refund_status'] = $item[$rs_k];
                    $item['status_str'] = $refund_str[$item[$rs_k]]; 
                    
                    $data[] = $item;
                }
                //usort($data, $this->_sortStatus('refund_status'));

                $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
            }
            $root['item'] = $data;
        }

        $root['user_login_status'] = $user_login_status;

        //$root['page_title'] = $GLOBALS['m_config']['program_title'] ? $GLOBALS['m_config']['program_title'] . " - " : "";
        $root['page_title'] = $page_title;

        return output($root);
    }

    public function refund_view()
    {
        $root = array();
        $page_title = '退款详情';

        $user_login_status = check_login();
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            $user = $GLOBALS['user_info'];
            $user_id = intval($user['id']);

            $data_id = (int) $GLOBALS['request']['data_id'];
            $did = (int) $GLOBALS['request']['did'];
            if($did){
                $sql = 'SELECT doi.id, doi.deal_id, doi.number, doi.unit_price, doi.total_price,doi.discount_unit_price, doi.refund_status rs2, doi.name, doi.attr_str, doi.message_id , doi.deal_icon, s.name as supplier_name ,m.create_time ,doi.refund_money,doi.admin_memo
                        FROM '.DB_PREFIX.'deal_order_item as doi 
                        LEFT JOIN '.DB_PREFIX.'supplier s ON doi.supplier_id=s.id 
                        LEFT JOIN '.DB_PREFIX.'message m ON doi.message_id=m.id
                        WHERE doi.id='.$did.' AND doi.user_id='.$user_id.' AND refund_status <> 0 ';
                $list = $GLOBALS['db']->getAll($sql);
                foreach($list as $k=>$v){
                    $message = $GLOBALS['db']->getOne("SELECT content FROM ".DB_PREFIX."message WHERE id=".$v['message_id']);
                    $list[$k]['content']=$message;
                    $list[$k]['create_time']=to_date($v['create_time']);
                    $list[$k]['refund_money'] = round($v['refund_money'],2);
                }
                foreach ($list as $item) {
                    $item['unit_price'] = round($item['discount_unit_price'], 2);
                    $item['total_price'] = round($item['total_price'], 2);
                    $item['supplier_name'] = $item['supplier_name'] ?: '平台自营';
                    $item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'],122,74,1));	
                    if($item['rs2']==1){
                        $item['refund_info'] = "申请退款中";
                    }
                    elseif($item['rs2']==2){
                        $item['refund_info'] = "已退款";
                    }
                    elseif($item['rs2']==3){
                        $item['refund_info'] = "拒绝退款";
                    }
                    $data[] = $item;
                }
                //print_r($item);exit;
            }else{
                $sql = 'SELECT distinct(m.`id`) mid, m.`content`, m.`user_id`, doi.`id`, doi.discount_unit_price ,doi.`number`, doi.`name`, doi.`deal_icon`, doi.`unit_price`, doi.`total_price`, doi.`is_coupon`, doi.`deal_id`, doi.`supplier_id` sid1 , dc.`password` , dc.`refund_money` , dc.`admin_memo` , dc.`refund_status` rs2 , m.`create_time`
                    FROM '.DB_PREFIX.'message m
                    LEFT JOIN '.DB_PREFIX.'deal_order_item doi ON m.id=doi.`message_id`
                    LEFT JOIN '.DB_PREFIX.'deal_coupon dc ON m.id=dc.`message_id` AND dc.`order_deal_id`=doi.`id`
                    WHERE m.rel_table=\'deal_order\' AND m.`user_id` = '.$user_id.' AND m.id='.$data_id;
                
                $list = $GLOBALS['db']->getAll($sql);
                $data = array();
                if (count($list)) {
    
                    $s_ids = array();
                    $hasCoupon = false;
                    foreach ($list as $item) {
                        if ($item['sid1']) {
                            $s_ids[] = $item['sid1'];
                        }
                    }
    
                    $snameSql = 'SELECT id, name FROM '.DB_PREFIX.'supplier WHERE id in ('.implode(',', $s_ids).')';
                    $slist = $GLOBALS['db']->getAll($snameSql);
                    $fslist = array();
                    foreach ($slist as $s) {
                        $fslist[$s['id']] = $s['name'];
                    }
                    foreach ($list as $item) {
                        if ($item['is_coupon']) {
                            $item['number'] = 1;
                        }
                        $item['refund_money'] = round($item['refund_money'],2);
                        $item['unit_price'] = round($item['discount_unit_price'], 2);
                        $item['supplier_name'] = $fslist[$item['sid1']] ?: '平台自营';
                        $item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));
                        $item['create_time']=to_date($item['create_time']);
                        if($item['rs2']==1){
                            $item['refund_info'] = "申请退款中";
                        }
                        elseif($item['rs2']==2){
                            $item['refund_info'] = "已退款";
                        }
                        elseif($item['rs2']==3){
                            $item['refund_info'] = "拒绝退款";
                        }
                        $data[] = $item;
                    }  
                }
            }
            //print_r($data);exit;
            $root['item'] = $data;
        }
        $root['user_login_status'] = $user_login_status;
        $root['page_title'] = $page_title;
        //print_r($root);exit;
        return output($root);
    }

    /**
     * 对退货订单进行审核状态的排序
     * @param  string $key refund_status
     * @return int
     */
    private function _sortStatus($key)
    {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }


    /*退款详情*/
    public function refund_view_old()
    {
        $root = array();
        $data_id = $GLOBALS['request']['data_id'];

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $user_login_status = check_login();
        $page_title = '退款详情';

        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            //分页
            $page = intval($GLOBALS['request']['page']);
            $page = $page == 0 ? 1 : $page;

            $page_size = PAGE_SIZE;
            $limit = (($page - 1) * $page_size) . "," . $page_size;


            $sql = 'SELECT doi.id, doi.deal_id, doi.number, doi.unit_price,doi.discount_unit_price ,doi.total_price, doi.refund_status, doi.name, doi.attr_str, doi.message_id , doi.deal_icon, s.name as supplier_name FROM ' . DB_PREFIX . 'deal_order_item as doi LEFT JOIN ' . DB_PREFIX . 'supplier s ON doi.supplier_id=s.id WHERE doi.id=' . $data_id . ' AND user_id=' . $user_id . ' AND refund_status <> 0 LIMIT ' . $limit;
            $sql_count = 'SELECT count(id) FROM ' . DB_PREFIX . 'deal_order_item WHERE id=' . $data_id . ' AND user_id=' . $user_id . ' AND refund_status <> 0 ';
            $list = $GLOBALS['db']->getAll($sql);
            //AND doi.id='.$data_id.'
            //print_r($list);exit;
            $data = array();
            if (count($list)) {
                $count = $GLOBALS['db']->getOne($sql_count);
                $page_total = ceil($count / $page_size);
                foreach ($list as $k => $v) {
                    $message = $GLOBALS['db']->getOne("SELECT content FROM " . DB_PREFIX . "message WHERE id=" . $v['message_id']);
                    $root['message'] = $message;
                }
                //print_r($list);exit;
                foreach ($list as $item) {
                    $item['unit_price'] = round($item['discount_unit_price'], 2);
                    $item['total_price'] = round($item['total_price'], 2);
                    $item['supplier_name'] = $item['supplier_name'] ? : '平台自营';
                    $item['deal_icon'] = get_abs_img_root(get_spec_image($item['deal_icon'], 122, 74, 1));

                    $data[] = $item;
                }

                $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size, "data_total" => $count);
            }
            $root['item'] = $data;
        }
        $root['user_login_status'] = $user_login_status;
        $root['page_title'] = $page_title;
        //print_r($root);exit;
        return output($root);

    }
    

    /*物流详情*/
    public function logistics()
    {
        
        $root = array();
        $data_id = $GLOBALS['request']['data_id'];
    
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);
        $user_login_status = check_login();
        $page_title = '收货信息';
    
        if ($user_login_status == LOGIN_STATUS_LOGINED) {
            
            //需要发货的商品
            $dnSql = "select dn.* , do.order_sn , t.state , t.ischeck , `t`.`data` as `track_data`,t.express_company from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order as do on dn.order_id=do.id left join ".DB_PREFIX."track as t on dn.order_id=t.order_id and t.express_number=dn.notice_sn left join ".DB_PREFIX."express as e on e.id=dn.express_id and t.express_code=e.class_name where dn.order_id=".$data_id." group by dn.notice_sn,dn.express_id order by dn.id asc";
            //echo $dnSql;exit;
            $delivery_notice = $GLOBALS['db']->getAll($dnSql);
            if($delivery_notice){
                foreach($delivery_notice as $k=>$v){
                    unset($delivery_notice[$k]['order_item_id']);
                    $itemSql = "select doi.deal_id,doi.number,doi.unit_price,doi.total_price,doi.is_arrival,doi.refund_status,doi.name,doi.deal_icon,doi.attr_str,doi.discount_unit_price from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id=doi.id where dn.notice_sn='".$v['notice_sn']."' and dn.express_id=".$v['express_id']." and dn.order_id=".$data_id;
                    $deal_info = $GLOBALS['db']->getAll($itemSql);
                    $now_status=1;
                    foreach($deal_info as $kk=>$vv){
                        $deal_info[$kk]['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 360, 360, 1));
                        $deal_info[$kk]['unit_price_format']=format_price_html($vv['discount_unit_price']);
 
                        if($vv['is_arrival']==1){
                            $deal_info[$kk]['deal_status']='已收货';
                        }elseif($vv['refund_status']==2){
                            $deal_info[$kk]['deal_status']='已退款';
                        }/*elseif($vv['refund_status']==3){
                            $deal_info[$kk]['deal_status']='待确认收货';//'拒绝退款';
                            $now_status=0;
                        }*/elseif($vv['refund_status']==1){       
                            $deal_info[$kk]['deal_status']='退款申请中';
                        }else{
                            $deal_info[$kk]['deal_status']='待确认收货';
                            $now_status=0;
                        }
                        
                    }
                    $delivery_notice[$k]['deal_info']=$deal_info;
                    $delivery_notice[$k]['now_status']=$now_status;
                    $delivery_notice[$k]['state_text'] = $this->get_delivery_state($v['state']);
                    if($delivery_notice[$k]['track_data']){
                        $delivery_notice[$k]['track_data']=unserialize($v['track_data']);
                    }
                    
                }
            }
            $root['delivery_notice']=$delivery_notice?$delivery_notice:array();
            $root['delivery_count'] = count($delivery_notice);
            
            //无需发货的商品
            $order_item=$GLOBALS['db']->getAll("select do.id,do.discount_unit_price,do.attr_str,do.is_arrival,do.number,do.unit_price,do.total_price,do.deal_icon,do.name,do.deal_id,do.order_id,do.refund_status,do.delivery_memo from ".DB_PREFIX."deal_order_item as do where do.delivery_status=1 and is_delivery=0 and order_id=".$data_id." and user_id=".$user_id);
            if($order_item){
                foreach($order_item as $kk=>$vv){
                    $order_item[$kk]['deal_icon'] = get_abs_img_root(get_spec_image($vv['deal_icon'], 360, 360, 1));
                    $order_item[$kk]['unit_price_format']=format_price_html($vv['discount_unit_price']);
                    $order_item[$kk]['delivery_memo']=strim($vv['delivery_memo']);
                    if($vv['is_arrival']==1){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="已收货";
                    }else if($vv['refund_status']==1){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="退款申请中";
                    }else if($vv['refund_status']==2){
                        $order_item[$kk]['is_use']=0;
                        $order_item[$kk]['info']="已退款";
                    }/*else if($vv['refund_status']==3){
                        $order_item[$kk]['is_use']=1;
                        $order_item[$kk]['info']="拒绝退款";
                    }*/else{
                        $order_item[$kk]['is_use']=1;
                        $order_item[$kk]['info']="待确认收货";
                    }
                    
                }
                $root['delivery_count']=$root['delivery_count']+1;
            }
            
            $root['no_delivery_item']=$order_item?$order_item:array();
        }
        $root['user_login_status'] = $user_login_status;
        $root['page_title'] = $page_title;
        return output($root);
    
    }
    
    /**
     * 
     * @param unknown $state
     */
    public function get_delivery_state($state){
        
        $state_text='';
        switch ($state){
            case 0:
                $state_text='在途中';
                break;
            case 1:
                $state_text='已揽收';
                break;
            case 2:
                $state_text='疑难';
                break;
            case 3:
                $state_text='已签收';
                break;
            case 4:
                $state_text='退签';
                break;
            case 5:
                $state_text='同城 派送中';
                break;
            case 6:
                $state_text='退回';
                break;
            case 7:
                $state_text='转单';
                break;
            default:
                $state_text='不懂是什么鬼！';
            
        }
        
        return $state_text;
    }
    /*$order_info:订单信息
     * $deal_order_item:订单商品信息
     * $existence_expire_refund存在支持过期退商品，在订单详情中使用
     */
    protected function order_deal_status($order_info,$deal_order_item,$existence_expire_refund=0){//获得商品状态
    	if ($order_info['pay_status']==2) {
    		if ($deal_order_item['is_shop'] == 0) { //是团购
    			if($deal_order_item['is_coupon'] == 1){
    				$coupon_arr = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_coupon where order_deal_id = " . $deal_order_item['id']);
    				if(count($coupon_arr)>1){
    					$deal_orders_id=100;
    					foreach ($coupon_arr as $coupon){
    						if($coupon['is_balance']==0&&($coupon['refund_status']==0||$coupon['refund_status']==3)&&($coupon['end_time']==0 || $coupon['end_time']>NOW_TIME)){
    							$coupon_orders_id=2;
    						}else if($coupon['refund_status']==1){
    							if($coupon['is_balance']==1&&$coupon['confirm_time']>0&&$deal_order_item['dp_id']>0){
    								$coupon_orders_id=4;
    							}else{
    								$coupon_orders_id=2.5;
    							}
    						}else if($coupon['refund_status']==2){
    							$coupon_orders_id=5;
    	
    						}else{
    							if($coupon['confirm_time']>0&&$deal_order_item['dp_id']==0){
    								$coupon_orders_id=3;
    							}elseif($coupon['is_balance']==1&&$coupon['confirm_time']>0&&$deal_order_item['dp_id']>0){
    								$coupon_orders_id=4;
    							}elseif($coupon['end_time'] < NOW_TIME && $coupon['end_time'] <> 0){
    								$coupon_orders_id=2.8;
    								if($order_info['existence_expire_refund']==0&&$coupon['expire_refund']==1&&$coupon['refund_status']==0&&$coupon['is_valid']==1){
    									$existence_expire_refund=1;
    								}else{
    									//	if($coupon_arr['0']['is_valid']!=2||$v['order_status'] != 1){
    									//		auto_over_status($order_item['id']);
    									//	}
    								}
    							}
    						}
    						if($deal_orders_id>$coupon_orders_id){
    							$deal_orders_id=$coupon_orders_id;
    						}
    					}
    					if($deal_orders_id==2.5){
    						$deal_orders = "申请退款中";
    						$deal_orders_id=3.5;
    					}elseif($deal_orders_id==2){
    						$deal_orders = "待使用";
    					}elseif($deal_orders_id==3){
    						$deal_orders = "待评价";
    					}elseif($deal_orders_id==4){
    						$deal_orders = "已完成";
    					}elseif($deal_orders_id==5){
    						$deal_orders = "已退款";
    					}elseif($deal_orders_id==2.8){
    						$deal_orders = "已过期";
    						$deal_orders_id=2;
    						if($order_info['order_status']==1){
    							if($deal_order_item['dp_id'] == 0&&$deal_order_item['consume_count'] > 0){
    								$deal_orders_id=3;
    							}else{
    								$deal_orders_id=4;
    							}
    						}
    					}
    				}else{
    					$coupon=$coupon_arr['0'];
    					if($coupon['is_balance']==0&&($coupon['refund_status']==0||$coupon['refund_status']==3)&&($coupon['end_time']==0 || $coupon['end_time']>NOW_TIME)){
    						$deal_orders = "待使用";
    						$deal_orders_id=2;
    					}else if($coupon['refund_status']==1){
    						if($coupon['is_balance']==1&&$coupon['confirm_time']>0&&$deal_order_item['dp_id']>0){
    							$deal_orders = "已完成";
    							$deal_orders_id=4;
    						}else{
    							$deal_orders = "申请退款中";
    							$deal_orders_id=3.5;
    						}
    	
    					}else if($coupon['refund_status']==2){
    						$deal_orders = "已退款";
    						$deal_orders_id=5;
    							
    					}else{
    						if($coupon['confirm_time']>0&&$deal_order_item['dp_id']==0){
    							$deal_orders = "待评价";
    							$deal_orders_id=3;
    						}elseif($coupon['is_balance']==1&&$coupon['confirm_time']>0&&$deal_order_item['dp_id']>0){
    							$deal_orders = "已完成";
    							$deal_orders_id=4;
    						}elseif($coupon['end_time'] < NOW_TIME && $coupon['end_time'] <> 0){
    							$deal_orders = "已过期";
    							$deal_orders_id=2;
    							if($existence_expire_refund==0&&$coupon_arr['0']['expire_refund']==1&&$coupon_arr['0']['refund_status']==0&&$coupon_arr['0']['is_valid']==1){
    								$existence_expire_refund=1;
    							}else{
    								//	if($coupon_arr['0']['is_valid']!=2||$v['order_status'] != 1){
    								//		auto_over_status($order_item['id']);
    								//	}
    							}
    							if($order_info['order_status']==1){
    								$deal_orders_id=4;
    							}
    						}
    					}
    				}
    			}else{
    				if($deal_order_item['dp_id']==0){
    					$deal_orders = "待评价";
    					$deal_orders_id=3;
    				}elseif($deal_order_item['dp_id']>0){
    					$deal_orders = "已完成";
    					$deal_orders_id=4;
    				}
    			}
    		} else { //是商品
    			if($deal_order_item['is_coupon'] == 1){//自提
    				if ($deal_order_item['refund_status'] == 1) {
    					$deal_orders = "申请退款中";
    					$deal_orders_id=3.5;
    				}
    				elseif ($deal_order_item['refund_status'] == 2) {
    					$deal_orders = "已退款";
    					$deal_orders_id=5;
    				}else{
    					if ($deal_order_item['consume_count'] < 1) {
    						$deal_orders = "待自提";
    						$deal_orders_id=2;
    					} else {
    						if ($deal_order_item['dp_id'] == 0) {
    							$deal_orders = "待评价";
    							$deal_orders_id=3;
    						} else {
    							$deal_orders = "已完成";
    							$deal_orders_id=4;
    						}
    					}
    				}
    			}else{//无需配送or物流配送or驿站配送
    				if($deal_order_item['is_balance'] >0){
    					if ($deal_order_item['dp_id'] == 0) {
    						$deal_orders = "待评价";
    						$deal_orders_id=3;
    					} else {
    						$deal_orders = "已完成";
    						$deal_orders_id=4;
    					}
    				}else{
    					if ($deal_order_item['refund_status'] == 1) {
    						$deal_orders = "申请退款中";
    						$deal_orders_id=3.5;
    					}
    					elseif ($deal_order_item['refund_status'] == 2) {
    						$deal_orders = "已退款";
    						$deal_orders_id=5;
    					}else{
    						if ($deal_order_item['delivery_status'] == 5) {
    							if($order_info['is_groupbuy_or_pick']==0){
    								$deal_orders = "待发货";
    								$deal_orders_id=1;
    							}
    	
    							else if($deal_order_item['is_arrival']==0){
    								$deal_orders = "待收货";
    								$deal_orders_id=2;
    							}
    							else if ($deal_order_item['dp_id'] == 0) {
    								$deal_orders = "待评价";
    								$deal_orders_id=3;
    							} else {
    								$deal_orders = "已完成";
    								$deal_orders_id=4;
    							}
    						} elseif ($deal_order_item['delivery_status'] == 0) {
    							$deal_orders = "待发货";
    							$deal_orders_id=1;
    						} elseif ($deal_order_item['delivery_status'] == 1) {
    							if ($deal_order_item['is_arrival'] == 0) {
    								$deal_orders = "待收货";
    								$deal_orders_id=2;
    							} elseif ($deal_order_item['is_arrival'] == 1) {
    								if ($deal_order_item['dp_id'] == 0) {
    									$deal_orders = "待评价";
    									$deal_orders_id=3;
    								} else {
    									$deal_orders = "已完成";
    									$deal_orders_id=4;
    								}
    							} else {
    								$deal_orders = "待维权";
    							}
    						}
    					}
    				}
    	
    			}
    		}
    		return array('deal_orders'=>$deal_orders,'deal_orders_id'=>$deal_orders_id,"existence_expire_refund"=>$existence_expire_refund);
    	}
    }
	/*$order_info:获得物流收货按钮文字描述
     */
    protected function check_logistics_status($type){//获得商品状态
		if($type==1){
			$str='发货信息';
		}elseif($type==2){
			$str='确认收货';
		}elseif($type==3){
			$str='物流查看';
		}elseif($type==4){
			$str='物流&收货';
		}else{
			$str='物流&收货';
		}
		return $str;
	}
	
}

?>