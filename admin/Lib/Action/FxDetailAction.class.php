<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: jobinlin
// +----------------------------------------------------------------------

class FxDetailAction extends CommonAction{
    
public function index()
	{
	    
		
		$begin_time = strim($_REQUEST['begin_time']=='')?0:to_timespan($_REQUEST['begin_time']);
		$end_time = strim($_REQUEST['end_time']=='')?0:to_timespan($_REQUEST['end_time']);
		$type_search = isset($_REQUEST['user_type'])?1:0;
		$user_type = intval($_REQUEST['user_type']);
		$name = strim($_REQUEST['name']);

		$map[DB_PREFIX.'fx_buy_order.pay_status'] = array('eq',2);
 		//定义搜索条件条件
		if($begin_time>0||$end_time>0)
		{
		    if($begin_time>0&&$end_time>0){
		        $map[DB_PREFIX.'fx_buy_order.create_time'] = 
		        array(
		            array('gt',$begin_time),
		            array('lt',$end_time)
		        );
		    }
		    elseif($begin_time>0){
		        $map[DB_PREFIX.'fx_buy_order.create_time'] = array('gt',$begin_time);
		    }
		    elseif($end_time>0){
		        $map[DB_PREFIX.'fx_buy_order.create_time'] = array('lt',$end_time);
		    }
		    
		}	
        
		if($type_search == 1 && $name !=""){
		    $condition = '%'.$name.'%';
		    $ids_arr = array();
		    if($user_type==0)
		    {
		        $ids = M("User")->where(array("user_name"=>array('like',$condition)))->field("id")->findAll();
		    }
		    else{
		        $pids = M('User')->where(array('user_name'=>array('like',$condition)))->field("id")->findAll();
		        foreach($pids as $kk=>$vv)
		        {
		            $pids_arr[]=$vv['id'];
		        }
		        $ids = M('User')->where(array('pid'=>array('in',$pids_arr)))->field("id")->findAll();
		    }
		    
		    foreach($ids as $k=>$v)
		    {
		        array_push($ids_arr,$v['id']);
		    }
		    $map[DB_PREFIX.'fx_buy_order.user_id'] = array('in',$ids_arr);
		}
		

		//页面首次进入无条件筛选
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = M("FxBuyOrder");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	//彻底删除
	public function log_delete(){
	    $ajax = intval($_REQUEST['ajax']);
	    $id = $_REQUEST ['id'];
	    if (isset ( $id )) {
	        $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
	        $rel_data = M("FxDetail")->where($condition)->findAll();
	        $info = FX_NAME."资质购买日志删除成功!";
	        $ids = explode ( ',', $id );
	        foreach($ids as $uid)
	        {
	            $GLOBALS['db']->query("delete from ".DB_PREFIX."fx_buy_order where id = ".$uid);
	        }
	        //save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
	        $this->success ($info,$ajax);
	
	    } else {
	        $this->error (l("INVALID_OPERATION"),$ajax);
	    }
	}
}
?>