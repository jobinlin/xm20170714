<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class EventSubmitAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		$event = M("Event")->getById($map['event_id']);
		if(!$event)
		{
			$this->error("没有活动数据");
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	public function foreverdelete() {
	//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();
				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
					$event_id = $data['event_id'];
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					M("EventSubmitField")->where(array ('submit_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("Event")->where("id=".$event_id)->setField("submit_count",M("EventSubmit")->where(array("event_id"=>$event_id,"is_verify"=>1))->count());
					
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}	
	}
	
	public function set_verify()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = "活动报名ID:".$id;

		$submit_data = $GLOBALS['db']->getRow("select event_id from ".DB_PREFIX."event_submit where id = ".$id);
		if($submit_data['is_verify']==0){
		      $GLOBALS['db']->query("update ".DB_PREFIX."event set submit_count = submit_count+1 where id=".$submit_data['event_id']." and submit_count + 1 <= total_count and total_count > 0");
		}
		if($GLOBALS['db']->affected_rows()==0){
		    $this->error("名额已满");
		}else{
    		require_once(APP_ROOT_PATH."system/model/event.php");
    		verify_event_submit($id);
    		$is_verify=$GLOBALS['db']->getRow("select is_verify from ".DB_PREFIX."event_submit where id=".$id);
    		if($is_verify['is_verify']==1)
    		{
        		save_log($info."报名审核通过",1);			
        		$this->success("操作成功");
    		}else {
    		    $this->error("操作失败");
    		}
		}
	}
	
	public function refuse()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = "活动报名ID:".$id;
		
		require_once(APP_ROOT_PATH."system/model/event.php");
		refuse_event_submit($id);
		
		save_log($info."报名审核被拒绝",1);
		$this->success("操作成功");
	}
}
?>