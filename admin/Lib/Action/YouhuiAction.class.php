<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class YouhuiAction extends CommonAction
{
	public function index()
	{
		$model = M(MODULE_NAME);
		$map['supplier_id'] = 0;
		if (!empty($model)) {
			$this->_list($model, $map);
		}

		$this->display();
	}

	public function add()
	{
		$this->display();
	}
	public function insert() {
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		if(mb_strlen($_REQUEST['name'],'utf8')>15)
		{
			$this->error("优惠券名称不能超过15个字");
		}
		if(floatval($data['youhui_value'])>999)
		{
			$this->error("请输入1~999之间的整数！");
		}

		//数据处理
		$data['name'] = strim($_REQUEST['name']); //优惠券名称
		$data['begin_time'] = strim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']); //发放开始时间
		$data['end_time'] = strim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']); //发放结束时间
		$data['youhui_value'] = floatval($_REQUEST['youhui_value']); //面额
		$data['total_num'] = intval($_REQUEST['total_num']); //发放总数量
		$data['user_limit'] = intval($_REQUEST['user_limit']); //每人最多可领取
		$data['user_everyday_limit'] = intval($_REQUEST['user_everyday_limit']); //每天最多只能领取
		$data['start_use_price'] = $_REQUEST['start_use_price']; //使用限制（订单满多少可用）
		$data['valid_type'] = intval($_REQUEST['valid_type']); //有效期设置
		if(intval($_REQUEST['valid_type'])==2){//有效期设置，固定日期有效
			$data['use_begin_time'] = strim($_REQUEST['use_begin_time'])==''?0:to_timespan($_REQUEST['use_begin_time']); //有效期开始时间
			$data['use_end_time'] = strim($_REQUEST['use_end_time'])==''?0:to_timespan($_REQUEST['use_end_time']); //有效期截止时间
			$data['expire_day'] = '';//领券后固定有效天数
		}elseif(intval($_REQUEST['valid_type'])==1){//有效期设置，领券后固定有效天数
			$data['expire_day'] = intval($_REQUEST['expire_day']);//领券后固定有效天数
			$data['use_begin_time'] = ''; //有效期开始时间
			$data['use_end_time'] = ''; //有效期截止时间
		}
		$data['youhui_type'] = 2; //电子券
		$data['create_time'] = NOW_TIME; //创建时间
		$data['is_effect'] = 1; //有效性
		$data['is_recommend'] = 1; //推荐
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	public function edit() {
		$condition['id'] = intval($_REQUEST ['id']);
		$data = M(MODULE_NAME)->where($condition)->find();

		$data['begin_time'] = to_date($data['begin_time']);
		$data['end_time'] = to_date($data['end_time']);
		$data['use_begin_time'] = to_date($data['use_begin_time']);
		$data['use_end_time'] = to_date($data['use_end_time']);

		$this->assign ( 'data', $data );
		$this->display();
	}

	public function update() {
		$data = M(MODULE_NAME)->create ();
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");


		//开始验证有效性
		if(mb_strlen($_REQUEST['name'],'utf8')>15)
		{
			$this->error("优惠券名称不能超过15个字");
		}
		if(floatval($data['youhui_value'])>999)
		{
			$this->error(L("请输入1~999之间的整数！"));
		}

		//数据处理
		$data['name'] = strim($_REQUEST['name']); //优惠券名称
		$data['begin_time'] = strim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']); //发放开始时间
		$data['end_time'] = strim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']); //发放结束时间
		$data['youhui_value'] = floatval($_REQUEST['youhui_value']); //面额
		$data['total_num'] = intval($_REQUEST['total_num']); //发放总数量
		$data['user_limit'] = intval($_REQUEST['user_limit']); //每人最多可领取
		$data['user_everyday_limit'] = intval($_REQUEST['user_everyday_limit']); //每天最多只能领取
		$data['start_use_price'] = $_REQUEST['start_use_price']; //使用限制（订单满多少可用）
		$data['valid_type'] = intval($_REQUEST['valid_type']); //有效期设置
		if(intval($_REQUEST['valid_type'])==2){//有效期设置，固定日期有效
			$data['use_begin_time'] = strim($_REQUEST['use_begin_time'])==''?0:to_timespan($_REQUEST['use_begin_time']); //有效期开始时间
			$data['use_end_time'] = strim($_REQUEST['use_end_time'])==''?0:to_timespan($_REQUEST['use_end_time']); //有效期截止时间
			$data['expire_day'] = '';//领券后固定有效天数
		}elseif(intval($_REQUEST['valid_type'])==1){//有效期设置，领券后固定有效天数
			$data['expire_day'] = intval($_REQUEST['expire_day']); //领券后固定有效天数
			$data['use_begin_time'] = ''; //有效期开始时间
			$data['use_end_time'] = ''; //有效期截止时间
		}
		$data['youhui_type'] = 2; //电子券
		$data['create_time'] = NOW_TIME; //创建时间
		$data['is_effect'] = 1; //有效性
		$data['is_recommend'] = 1; //推荐// 更新数据

		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	public function delete(){
		$condition['id'] = $_REQUEST['id'];
		$log_info['name'] = M(MODULE_NAME)->where($condition)->getField('name');

		$list = M(MODULE_NAME)->where($condition)->delete();
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("DELETE_SUCCESS"),1);
			$this->success(L("DELETE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("DELETE_FAILED"),0);
			$this->error(L("DELETE_FAILED"),0,$log_info.L("DELETE_FAILED"));
		}
	}

	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );

			$rel_data = M(MODULE_NAME)->where($condition)->findAll();
			foreach($rel_data as $data)
			{
				$info[] = $data['name'];
			}
			if($info) {
				$info = implode(",",$info);
			}

			$list = M(MODULE_NAME)->where ( $condition )->delete();
			if ($list!==false) {
				save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField('name');
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}

	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);
		M(MODULE_NAME)->where("id=".$id)->setField("update_time",NOW_TIME);
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$locations = M("YouhuiLocationLink")->where(array ('youhui_id' => $id ))->findAll();
		foreach($locations as $location)
		{
			recount_supplier_data_count($location['location_id'],"youhui");
		}
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
	}
}
?>