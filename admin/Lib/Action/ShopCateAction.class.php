<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class ShopCateAction extends CommonAction{
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
	

	public function add()
	{
		$this->assign("newsort",M(MODULE_NAME)->where("is_delete=0")->max("sort")+1);
		$cate_tree = M(MODULE_NAME)->where('is_delete = 0 and pid = 0')->findAll();
		$cate_tree = D(MODULE_NAME)->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		if($data['pid']>0)
			$data['recommend'] = 0;
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("DEALCATE_NAME_EMPTY_TIP"));
		}	

		if(mb_strlen($data['name'],"utf8") > 5){
		    $this->error ("名称限制5个字",0);
		}
		
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			 
			$this->syn_cate_brand($list);
			 
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$ids = D(MODULE_NAME)->getChildIds($id);
		$ids[] = $id;
		
		$condition['is_delete'] = 0;
		$condition['pid'] = 0;
		$condition['id'] = array('not in',$ids);
		

		$cate_tree = M(MODULE_NAME)->where($condition)->findAll();
		$cate_tree = D(MODULE_NAME)->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		$this->display ();
	}

	

    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		 
		 
		 
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		 
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		if($data['pid']>0)
			$data['recommend'] = 0;
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("title");
		
		if(mb_strlen($data['name'],"utf8") > 5){
		    $this->error ("名称限制5个字",0);
		}
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			 
            $this->syn_cate_brand($data['id']);
			 
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if(M("ShopCate")->where(array ('pid' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_SHOPCATE_EXIST"),$ajax);
				}
				if(M("Deal")->where(array ('shop_cate_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_GOODS_EXIST"),$ajax);
				}
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
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
	
	public function syn_cate_brand($cate_id){

	    $sql = "select * from ".DB_PREFIX."shop_cate where id=".$cate_id;
	    $cate_info = $GLOBALS['db']->getRow($sql);
	    
	    require_once(APP_ROOT_PATH."system/utils/child.php");
	    $ids_util = new child("shop_cate");
	    $ids = $ids_util->getChildIds($cate_id);
	    $ids[] = $cate_id;	    
	    if($cate_info['pid'] > 0){
	        $ids[] = $cate_info['pid'];
	    }
	    
	    $sql = "select * from ".DB_PREFIX."brand where shop_cate_id in (".implode(',',$ids).")";
	    $brand_list = $GLOBALS['db']->getAll($sql);

	    foreach($brand_list as $k=>$v){
	        syn_brand_match($v['id']);
	    }
	}
	
}
?>