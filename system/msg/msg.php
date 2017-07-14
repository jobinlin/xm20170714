<?php 


/**
* -
*/
class Msg
{
	/**
	 * 消息类型
	 * @var array
	 */
	private $msgType = array(
		'delivery' => '物流消息',
		'notify' => '通知消息',
		'account' => '资产消息',
		'confirm' => '验证消息',
	);
	/**
	 * 各种消息头和跳转路由
	 * @var array
	 */
	private $msgTitle = array(
		'delivery' => array(
			1 => array(
				'title' => '商品已发货',
				'ctl' => array( // 订单详情页
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			2 => array(
				'title' => '商品已签收',
				'ctl' => array( // 订单详情页
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
		),
		'notify' => array(
			1 => array(
				'title' => '注册成功',
				'ctl' => array( // 首页
					'pc' => '',
					'wap' => '',
					'app' => 'index',
				),
			),
			2 => array(
				'title' => '会员升级',
				'ctl' => array( // 帐户管理
					'pc' => 'uc_log#money',
					'wap' => 'uc_account',
					'app' => 'uc_account',
				),
			),
			3 => array(
				'title' => '分享被推荐', // 分享被推荐
				'ctl' => array( // 我的积分
					'pc' => 'uc_log#score',
					'wap' => 'uc_score',
					'app' => 'uc_score',
				),
			),
			4 => array(
				'title' => '分享被置顶', // 分享被置顶
				'ctl' => array( // 我的积分
					'pc' => 'uc_log#score',
					'wap' => 'uc_score',
					'app' => 'uc_score',
				),
			),
			5 => array(
				'title' => '退款失败',
				'ctl' => array( // 订单详情页
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			6 => array(
				'title' => '积分商品-退款失败',
				'ctl' => array( // 订单详情页
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			7 => array(
				'title' => '新手勋章过期',
				'ctl' => array( // 会员中心
					'pc' => 'uc_medal',
					'wap' => 'user_center',
					'app' => 'user_center',
				),
			),
			8 => array(
				'title' => '优惠券即将过期',
				'ctl' => array( // 首页
					'pc' => 'uc_youhui',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
			),
			9 => array(
				'title' => '优惠券已过期',
				'ctl' => array( // 优惠券详情
					'pc' => 'uc_youhui',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
				
			),
			10 => array(
				'title' => '系统推送',
				'ctl' => array(
					'pc' => '',
					'wap' => '',
					'app' => '',
				),
			),
		    11 => array(
		        'title' => '购买分销资格成功',
		        'ctl' => array(
		            'pc' => 'uc_invite',
		            'wap' => 'uc_share',
		            'app' => 'uc_share',
		        ),
		    ),
		),
		'account' => array(
			1 => array(
				'title' => '登录积分变更', // 登录
				'ctl' => array( // 我的积分
					'pc' => 'uc_log#score',
					'wap' => 'uc_score',
					'app' => 'uc_score',
				),
			),
			2 => array(
				'title' => '完成订单-积分变更', // 完成订单
				'ctl' => array( // 订单详情
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			3 => array(
				'title' => '退款成功', // 退款成功
				'ctl' => array( // 订单详情
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			4 => array(
				'title' => '充值成功', // 充值
				'ctl' => array( // 个人中心
					'pc' => 'uc_log#money',
					'wap' => 'uc_money#money_log',
					'app' => 'money_log',
				),
			),
			5 => array(
				'title' => '提现成功', // 提现成功
				'ctl' => array( // 个人中心
					'pc' => 'uc_log#money',
					'wap' => 'uc_money#withdraw_log',
					'app' => 'withdraw_log',
				),
			),
			6 => array(
				'title' => '红包到账', // 获得红包
				'ctl' => array( // 红包列表
					'pc' => 'uc_voucher',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
			),
			7 => array(
				'title' => '红包过期',
				'ctl' => array( // 红包列表
					'pc' => 'uc_voucher',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
			),
			8 => array(
				'title' => '优惠券到帐',
				'ctl' => array( // 优惠券列表
					'pc' => 'uc_youhui',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
			),
			9 => array(
				'title' => '付款成功',
				'ctl' => array( // 订单详情
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			10 => array(
				'title' => '积分商品-退款成功',
				'ctl' => array( // 我的积分
					'pc' => 'uc_order#view',
					'wap' => 'uc_order#view',
					'app' => 'uc_order_view',
				),
			),
			11 => array(
				'title' => '积分商品-兑换成功',
				'ctl' => array( // 我的积分
					'pc' => 'uc_log#score',
					'wap' => 'uc_score',
					'app' => 'uc_score',
				),
			),
		    12 => array(
		        'title' => '红包即将过期',
		        'ctl' => array( // 红包列表
		            'pc' => 'uc_voucher',
		            'wap' => 'uc_youhui',
		            'app' => 'uc_youhui',
		        ),
		    ),
		    13 => array(
		        'title' => '推荐获得佣金',
		        'ctl' => array( // 红包列表
		            'pc' => 'uc_fx_invite',
		            'wap' => 'uc_fx#income',
		            'app' => 'uc_fx_income',
		        ),
		    ),
		),
		'confirm' => array(
			1 => array(
				'title' => '提货码-验证成功',
				'ctl' => array( // 消费券列表
					'pc' => 'uc_coupon',
					'wap' => 'uc_coupon',
					'app' => 'uc_coupon',
				),
			),
			2 => array(
				'title' => '消费码-验证成功',
				'ctl' => array( // 消费券列表
					'pc' => 'uc_coupon',
					'wap' => 'uc_coupon',
					'app' => 'uc_coupon',
				),
			),
			3 => array(
				'title' => '优惠码-验证成功',
				'ctl' => array( // 优惠券列表
					'pc' => 'uc_youhui',
					'wap' => 'uc_youhui',
					'app' => 'uc_youhui',
				),
			),
			4 => array(
				'title' => '活动券-验证成功',
				'ctl' => array(
					'pc' => 'uc_event',
					'wap' => 'uc_event',
					'app' => 'uc_event',
				),
			)
		),
	);

	/**
	 * 发送信息接口
	 * @param  int $user_id 用户id
	 * @param  string $content 信息内容
	 * @param  string $type    数据类型
	 * @param  int $ext_data    消息扩展
	 * @return 
	 */
	public function send_msg($user_id, $content, $type, $ext_data)
	{
		$msg = array();
		$msg['user_id'] = $user_id;
		$msg['content'] = strim($content);
		$msg['create_time'] = NOW_TIME;
		$type = strtolower($type);
		if (!array_key_exists($type, $this->msgType)) {
			$type = 'notify';
		}
		$msg['type'] = $type;
		if (!empty($ext_data)) {
			$data = serialize($ext_data);
			$msg['data'] = $data;
		}
		// print_r($msg);exit;
		$GLOBALS['db']->autoExecute(DB_PREFIX.'msg_box', $msg, 'INSERT', '', 'SILENT');
	}

	public function load_msg($msg)
	{
		if (!empty($msg['data'])) {
			$data = unserialize($msg['data']);
			unset($msg['data']);
            
			$typeExists = array_key_exists($data['type'], $this->msgTitle[$msg['type']]);
			if ($typeExists) {
				$msg['title'] = $this->msgTitle[$msg['type']][$data['type']]['title'];
				$msg['ctl'] = $this->msgTitle[$msg['type']][$data['type']]['ctl'];
			} else {
				$msg['title'] = '其它消息';
				$msg['ctl'] = array();
			}
			if (!empty($data['data_id'])) {
			    if(($msg['type']=='notify' &&($data['type']==5 || $data['type']==6)) ||
			       ($msg['type']=='account' &&($data['type']==3 || $data['type']==10))||
			       ($msg['type']=='delivery' &&($data['type']==2)))
			    {
			         $data_id=$GLOBALS['db']->getOne("select order_id from ".DB_PREFIX."deal_order_item where id=".$data['data_id']);
			         $msg['data_id'] = $data_id;
			    }else{
			        $msg['data_id'] = $data['data_id'];
			    }
			}
			
			// 判断不同端口来返回信息
			if (defined("APP_INDEX") && APP_INDEX == "wap_index" && $msg['ctl']) { // wap 端
				$msg['link'] = wap_url('index', $msg['ctl']['wap'], array('id' => $data['data_id']));
			} elseif ($msg['ctl']) { // pc端
				$msg['link'] = url('index', $msg['ctl']['pc'], array('id' => $data['data_id']));
			}
			// $msg['short_title'] = strlen($msg['title']) > 15 ? msubstr($msg['title']) : $msg['title'];
		} else {
			$msg['title'] = '其它消息';
		}
		return $msg;
	}

	/**
	 * 获得消息类型的文字描述
	 * @param  string $type 
	 * @return string       
	 */
	public function load_title($type)
	{
		$type = $this->filterType($type);
		return $this->msgType[$type];
	}

	/**
	 * 过滤消息类型
	 * @param  string $type 
	 * @return string       
	 */
	public function filterType($type)
	{
		$type = strtolower($type);
		if (!array_key_exists($type, $this->msgType)) {
			$type = 'notify';
		}
		return $type;
	}
	
	/**
	 * 获取app的类型
	 * 
	 * @param unknown $msg_type  */
	public function getAppType($msg_type){
	    $msg_arr = require APP_ROOT_PATH."system/public_cfg/app_type_cfg.php";
        return $msg_arr[$msg_type];
    }
}

