<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DeliveryRegionAction extends CommonAction{
	public function index()
	{
		$this->assign("parent_region",M("DeliveryRegion")->where("id=".intval($_REQUEST['pid']))->find());
		$condition['pid'] = intval($_REQUEST['pid']);
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$pid = intval($_REQUEST['pid']);
		$parent_region = M("DeliveryRegion")->getById($pid);
		$this->assign("parent_region",$parent_region);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add",array("pid"=>intval($_REQUEST['pid']))));
		if(!check_empty($data['name']))
		{
			$this->error(L("REGION_NAME_EMPTY_TIP"));
		}	
		
		if(!check_empty($data['code'])|| !preg_match('/^[_0-9]{6}$/',$data['code']))
		{
		    $this->error("请输入六位数字的行政区划代码");
		}
		
		if(M(MODULE_NAME)->where(array("code"=>$data['code']))->count()){
		    $this->error("该行政区划代码已存在，请重新输入");
		}
		
		$p_level=M(MODULE_NAME)->where(array("id"=>$data['pid']))->getField("region_level");
		
		$data['region_level']=$p_level+1;
		
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if($data['region_level']<>4 && false !== $list)
		    $this->regionCodePhp();
		
		if (false !== $list) {
			//成功提示
			$this->updateRegionJS();
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
		$this->display ();
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		$log_code = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("code");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("REGION_NAME_EMPTY_TIP"));
		}	
		
		if(!check_empty($data['code'])|| !preg_match('/^[_0-9]{6}$/',$data['code']))
		{
		    $this->error("请输入六位数字的行政区划代码");
		}
		
		$where=array();
		$where['code']=array("eq",$data['code']);
		$where['id']=array('neq',$data['id']);
		if(M(MODULE_NAME)->where($where)->count()){
		    $this->error("该行政区划代码已存在，请重新输入");
		}
		
		$p_level=M(MODULE_NAME)->where(array("id"=>$data['pid']))->getField("region_level");
		
		$data['region_level']=$p_level+1;
		
		// 更新数据
		$city_id=M("DealCity")->where(array("code"=>$log_code))->getField("id");
		$city_code['code']=$data['code'];
		M("DealCity")->where(array("id"=>$city_id))->save($city_code);
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."delivery_region",$data,"UPDATE","id=".$data['id']);
		$rs = $GLOBALS['db']->affected_rows();
		
		if($data['region_level']<>4 && $rs)
		    $this->regionCodePhp();
		
		if($rs)
		$this->updateRegionJS();
		save_log($log_info.L("UPDATE_SUCCESS"),1);
		 
		$this->success(L("UPDATE_SUCCESS"));

	}
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if(M(MODULE_NAME)->where(array ('pid' => array ('in', explode ( ',', $id ) ) ))->findAll())
				{
					$this->error(l("EXIST_SUB_REGION"),$ajax);
				}
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
		
				if ($list!==false) {
					$this->updateRegionJS();
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
	
	private function regionCodePhp()
	{
	    $jsStr = "<?php return ".var_export($this->getCode(),true)." ?>";
	    $path = get_real_path()."public/runtime/regionCode.php";
	    @file_put_contents($path,$jsStr);
	}
	
	private function getCode(){
	    $where=array();
	    $where['region_level']=array('neq',4);
	    $list = M("DeliveryRegion")->where($where)->findAll();
	    $region_code=array();
	    foreach ($list as $t => $v){
	        $region_code[$v['code']]=$v['name'];
	    }
	    return $region_code;
	}
	
	private function updateRegionJS()
	{
		$jsStr = "var regionConf = ".$this->getRegionChildJS();
		$path = get_real_path()."public/runtime/region.js";
		@file_put_contents($path,$jsStr);
	}
	
	private function getRegionChildJS($pid = 0)
	{
		$jsStr = "";
		$childRegionList = M("DeliveryRegion")->where("pid=".$pid)->order("id asc")->findAll();
		
		foreach($childRegionList as $childRegion)
		{
			if(empty($jsStr))
				$jsStr .= "{";
			else
				$jsStr .= ",";
				
			$childStr = $this->getRegionChildJS($childRegion['id']);
			$jsStr .= "\"r$childRegion[id]\":{\"i\":$childRegion[id],\"n\":\"$childRegion[name]\",\"o\":\"$childRegion[code]\",\"c\":$childStr}";
		}
		
		if(!empty($jsStr))
			$jsStr .= "}";
		else
			$jsStr .= "\"\"";
				
		return $jsStr;
	}
	
	
}
?>