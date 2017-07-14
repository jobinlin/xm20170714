<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DealCityAction extends CommonAction{
	public function index()
	{
		$condition['is_delete'] = 0;
		$condition['pid'] = 0;
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['level'] = -1;
			$v['name'] = $v['name'];
			$result[$row] = $v;
			$row++;
			$sub_cate = M(MODULE_NAME)->where(array("id"=>array("in",D(MODULE_NAME)->getChildIds($v['id'])),'is_delete'=>0))->findAll();
			$sub_cate = D(MODULE_NAME)->toFormatTree($sub_cate,'name');
			foreach($sub_cate as $kk=>$vv)
			{
				$vv['name']	=	$vv['title_show'];
				$result[$row] = $vv;
				$row++;
			}
		}
		//dump($result);exit;
		$this->assign("list",$result);
		$this->display ();
		return;
	}
// 	public function trash()
// 	{
// 		$condition['is_delete'] = 1;
// 		$this->assign("default_map",$condition);
// 		parent::index();
// 	}
	public function add()
	{
		$city_list = M("DealCity")->where('pid = 0')->findAll();
		$this->assign("city_list",$city_list);
		$this->assign("new_sort", M("DealCity")->where("is_delete=0")->max("sort")+1);
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$city_list = M("DealCity")->where('pid = 0 and id <> '.$vo['id'])->findAll();
		$this->assign("city_list",$city_list);
		
		$this->display ();
	}
//	public function delete() {
//	//删除指定记录
//		$ajax = intval($_REQUEST['ajax']);
//		$id = $_REQUEST ['id'];
//		if (isset ( $id )) {
//				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
//				if(M("DealCity")->where(array ('id' => array ('in', explode ( ',', $id ) )))->getField("pid")==0)
//				{
//					$this->error (l("ALL_CANT_DELETE"),$ajax);
//				}
//				if(M("DealCity")->where(array ('pid' => array ('in', explode ( ',', $id ) ),'is_delete'=>0 ))->count()>0)
//				{
//					$this->error (l("SUB_CITY_EXIST"),$ajax);
//				}
//				if(M("Deal")->where(array ('city_id' => array ('in', explode ( ',', $id ) ),'is_delete'=>0 ))->count()>0)
//				{
//					$this->error (l("SUB_DEAL_EXIST"),$ajax);
//				}
//				if(M("Area")->where(array ('city_id' => array ('in', explode ( ',', $id ) ),'is_delete'=>0 ))->count()>0)
//				{
//					$this->error (l("SUB_AREA_EXIST"),$ajax);
//				}
//				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
//				foreach($rel_data as $data)
//				{
//					$info[] = $data['name'];	
//				}
//				if($info) $info = implode(",",$info);
//				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 1 );
//				if ($list!==false) {
//					save_log($info.l("DELETE_SUCCESS"),1);
//			 
//					$this->success (l("DELETE_SUCCESS"),$ajax);
//				} else {
//					save_log($info.l("DELETE_FAILED"),0);
//					$this->error (l("DELETE_FAILED"),$ajax);
//				}
//			} else {
//				$this->error (l("INVALID_OPERATION"),$ajax);
//		}	
//	}
	
// 	public function restore() {
// 		//删除指定记录
// 		$ajax = intval($_REQUEST['ajax']);
// 		$id = $_REQUEST ['id'];
// 		if (isset ( $id )) {
// 				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
// 				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
// 				foreach($rel_data as $data)
// 				{
// 					$info[] = $data['name'];						
// 				}
// 				if($info) $info = implode(",",$info);
// 				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
// 				if ($list!==false) {
// 					save_log($info.l("RESTORE_SUCCESS"),1);
// 					$this->success (l("RESTORE_SUCCESS"),$ajax);
// 				} else {
// 					save_log($info.l("RESTORE_FAILED"),0);
// 					$this->error (l("RESTORE_FAILED"),$ajax);
// 				}
// 			} else {
// 				$this->error (l("INVALID_OPERATION"),$ajax);
// 		}		
// 	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
		
				if(M("DealCity")->where(array ('pid' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error (l("SUB_CITY_EXIST"),$ajax);
				}
				if(M("Deal")->where(array ('city_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("CITY_DEAL_EXIST"),$ajax);
				}
				if(M("Area")->where(array ('city_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_AREA_EXIST"),$ajax);
				}
				if(M("Supplier")->where(array ('city_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_SUPPLIER_EXIST"),$ajax);
				}
				if(M("Event")->where(array ('city_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_EVENT_EXIST"),$ajax);
				}
				if(M("Youhui")->where(array ('city_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_YOUHUI_EXIST"),$ajax);
				}
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
				//删除相关预览图
//				foreach($rel_data as $data)
//				{
//					@unlink(get_real_path().$data['preview']);
//				}			
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
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		$data['is_effect'] = 1;
		$data['is_open'] = 1;
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("CITY_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['uname']))
		{
			$this->error(L("CITY_UNAME_EMPTY_TIP"));
		}	
		
		if(M("DealCity")->where("is_default=1")->count()==0)
		{
			$data['is_default'] = 1;
		}	
		
		if(M(MODULE_NAME)->where(array("code"=>$data['code']))->count()){
		    $this->error("该地区已存在");
		}
		
		/* if(substr($data['code'],-4)!="0000" && $data['pid']==0){
		    $this->error("请选择省份");
		} */
		
		/* if($data['pid']){
		    $pcode = M(MODULE_NAME)->where(array("id"=>$data['pid']))->getField("code");
		    if(substr($data['code'],0,2)!=substr($pcode,0,2))
		        $this->error("你选择的地区不属于该省份，请重新选择");
		} */
		
		// 更新数据
		if($data['pid']==0){
		    $data['citycode']='';
		}
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$DBerr,0);
			$this->error(L("INSERT_FAILED").$DBerr);
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$data['is_effect'] = 1;
		$data['is_open'] = 1;
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("CITY_NAME_EMPTY_TIP"));
		}	
		
		if(!check_empty($data['uname'])&&$data['pid']>0)
		{
			$this->error(L("CITY_UNAME_EMPTY_TIP"));
		}			

		$where=array();
		$where['code']=array("eq",$data['code']);
		$where['id']=array("neq",$data['id']);
		if(M(MODULE_NAME)->where($where)->count()){
		    $this->error("该地区已存在");
		}
		
		/* if(substr($data['code'],-4)!="0000" && $data['pid']==0){
		    $this->error("请选择省份");
		} */
		
		/* if($data['pid']){
		    $pcode = M(MODULE_NAME)->where(array("id"=>$data['pid']))->getField("code");
		    if(substr($data['code'],0,2)!=substr($pcode,0,2))
		        $this->error("你选择的地区不属于该省份，请重新选择");
		} */
		
		// 更新数据
	    if($data['pid']==0){
	        $data['citycode']='';
	    }
		    
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			
			if($data['pid']>0)
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_city set pid = ".$data['pid']." where pid = ".$data['id']);
			
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			 
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log($log_info.L("UPDATE_FAILED").$DBerr,0);
			$this->error(L("UPDATE_FAILED").$DBerr,0);
		}
	}
// 	public function set_sort()
// 	{
// 		$id = intval($_REQUEST['id']);
// 		$sort = intval($_REQUEST['sort']);
// 		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
// 		if(!check_sort($sort))
// 		{
// 			$this->error(l("SORT_FAILED"),1);
// 		}
// 		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
			 
			 
// 		save_log($log_info.l("SORT_SUCCESS"),1);
// 		$this->success(l("SORT_SUCCESS"),1);
// 	}	
// 	public function set_effect()
// 	{
// 		$id = intval($_REQUEST['id']);
// 		$ajax = intval($_REQUEST['ajax']);
// 		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
// 		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
				
// 		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
// 		if(M("DealCity")->where(array ('id' => array ('in', explode ( ',', $id ) )))->getField("pid")!=0)
// 		{
// 		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
// 		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
			 
// 		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
// 		}
// 		else
// 		$this->ajaxReturn(1,l("SET_EFFECT_1"),1)	;	
// 	}
	
	public function set_default()
	{
		$id = intval($_REQUEST['id']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		M(MODULE_NAME)->setField("is_default",0);	
		M(MODULE_NAME)->where("id=".$id)->setField("is_default",1);		
		 	
		save_log($info.l("SET_DEFAULT"),1);
		$this->success(L("UPDATE_SUCCESS"));	
	}
	
	public function search() {
	    $key=strim($_REQUEST['key']);
	    
	    if($key==""){
	        $data['status']=0;
	        $data['info']="请输入城市";
	        ajax_return($data);
	    }   
	   
	    if(file_exists(APP_ROOT_PATH."public/runtime/regionCode.php")){
	       $region_list=require_once(APP_ROOT_PATH."public/runtime/regionCode.php");
	    }else{
	        $region_list=$this->getCode();
	        $jsStr = "<?php return ".var_export($region_list,true)." ?>";
	        $path = get_real_path()."public/runtime/regionCode.php";
	        @file_put_contents($path,$jsStr);
	    }
	    
	    $arr = array();
	    foreach($region_list as $t => $v ){
	        if (strpos( $v , $key )!==false){
	            $pcode=substr($t,0,2)."0000";
	            
	            if($pcode!=$t){
	                $arr[]=array(
	                    "code"=>$t,
	                    "name"=>$v,
	                    'pcode'=>$pcode,
	                    'pname'=>$region_list[$pcode],
	                );
	                
	            }else {
	                $arr[]=array(
	                    "code"=>$t,
	                    "name"=>$v,
	                );
	            }
	        }
	    }
	    
	    if($arr){
	        $data['status']=1;
	        $data['info']="已获取相关城市";
	        $data['list']=$arr;
	        ajax_return($data);
	    }
	    else {
	        $data['status']=0;
	        $data['info']="该地区不存在，请确认输入地区是否为省级或市级";
	        ajax_return($data);
	    }
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
}
?>