<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class GoodsTypeAttrAction extends CommonAction{
	public function index()
	{
		$goods_type_id = intval($_REQUEST['goods_type_id']);
		$goods_type_info = M("GoodsType")->getById($goods_type_id);
		if(!$goods_type_info)
		{
			$this->error(l("GOODS_TYPE_NOT_EXIST"));
		}
		$this->assign("goods_type_info",$goods_type_info);
		parent::index();
	}
	public function add()
	{
		$goods_type_id = intval($_REQUEST['goods_type_id']);
		$goods_type_info = M("GoodsType")->getById($goods_type_id);
		if(!$goods_type_info)
		{
			$this->error(l("GOODS_TYPE_NOT_EXIST"));
		}
		$this->assign("goods_type_info",$goods_type_info);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		
		$count = M("GoodsTypeAttr")->where(array("goods_type_id"=>$data['goods_type_id']))->count();
		
		if($count > 3){
		    $this->error('最多添加三个属性！');
		}
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add",array("goods_type_id"=>$data['goods_type_id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("ATTR_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['preset_value'])&&$data['input_type']==1)
		{
			$this->error(L("PRESET_VALUE_EMPTY_TIP"));
		}			
		if(mb_strlen($data['name'],'utf8') > 5){
		    $this->error('名称不能超过5个字');
		}

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
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$goods_type_id = intval($vo['goods_type_id']);
		$goods_type_info = M("GoodsType")->getById($goods_type_id);
		if(!$goods_type_info)
		{
			$this->error(l("GOODS_TYPE_NOT_EXIST"));
		}
		$this->assign("goods_type_info",$goods_type_info);
		
		$this->display ();
	}
	
public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("ATTR_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['preset_value'])&&$data['input_type']==1)
		{
			$this->error(L("PRESET_VALUE_EMPTY_TIP"));
		}
		if(mb_strlen($data['name'],'utf8') > 5){
		    $this->error('名称不能超过5个字');
		}
		
		// 更新数据
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
	
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		
		$id_arr=explode ( ',', $id );
		foreach ($id_arr as $k=>$v){
			$attr=M("DealAttr")->where("goods_type_attr_id=".$v)->findAll();
			if($attr)
				$this->error ("存在该规格的商品，请修改后再删！",$ajax);
		}
		
		//$this->error ("存在改规格的商品，请修改后再删！",$ajax);
		
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
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
	
}
?>