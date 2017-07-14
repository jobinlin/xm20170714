<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DistributionAction extends CommonAction {
    function __construct()
    {
        parent::__construct();
        if(!IS_OPEN_DISTRIBUTION){
            $this->error (l("请先开启驿站功能"),0);
        }
    }
    /**
     * 配送点列表
     * {@inheritDoc}
     * @see CommonAction::index()
     */
    public function index ()
    {
        $page_idx = intval($_REQUEST['p'])==0?1:intval($_REQUEST['p']);
        $page_size = C('PAGE_LISTROWS');
        $limit = (($page_idx-1)*$page_size).",".$page_size;
        
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        }
        
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = 'desc';
        }
        
        if(isset($order)) {
            $orderby = "order by ".$order." ".$sort;
        } else {
            $orderby = "";
        }

        $where = array(
            'is_delete' => 0,
            'status' => 1
        );
        if (isset($_REQUEST['name'])) {
            $name = strim($_REQUEST['name']);
            if ($name) {
                $where['name'] = array('like', '%'.$name.'%');
            }
        }
        $model = M(MODULE_NAME);
        $list = $model->where($where)->order($orderby)->limit($limit)->findAll();
        $total = $model->where($where)->count('id');

        // 未结算余额
        $where1 = array(
            'd.pay_status' => 2,
            'd.order_status' => 0,
            'd.distribution_id' => array('gt', 0)
        );
        $dist_fees = M()->table(C('DB_PREFIX') . 'deal_order AS d')->join(C('DB_PREFIX') . 'deal_order_item AS di ON d.id=di.order_id')->field('distribution_id,sum(distribution_fee) AS fee')->where($where1)->group('d.distribution_id')->findAll();
        $df_f = array();
        foreach ($dist_fees as $val) {
            $df_f[$val['distribution_id']] = $val['fee'];
        }

        foreach ($list as &$item) {
            $fee = 0;
            if (array_key_exists($item['id'], $df_f)) {
                $fee = $df_f[$item['id']];
            }
            $item['unfee'] = $fee;
        }unset($item);
        
        $p = new Page($total, '');
        $page = $p->show();
        
        $sortImg = $sort; //排序图标
        $sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
        $sort = $sort == 'desc' ? 1 : 0; //排序方式
        	
        //模板赋值显示
        $this->assign ( 'sort', $sort );
        $this->assign ( 'order', $order );
        $this->assign ( 'sortImg', $sortImg );
        $this->assign ( 'sortType', $sortAlt );
        	
        $this->assign ( 'list', $list );
        $this->assign ( "page", $page );
        $this->assign ( "nowPage",$p->nowPage);
        
        $this->display ();
        return;
    }
    
    /**
     * 新增/更新表单
     * @param number $id
     * @param number $edit_type
     */
    public function edit_form ($id=0)
    {
        $is_edit = 0;
        $vo = array();
        if ($id > 0) {
            $model = M(MODULE_NAME);
            $where = array('id' => $id, 'is_delete' => 0);
            $vo = $model->where($where)->find();
        }
        if (!empty($vo)) { // 驿站时显示新增页面
            $is_edit = 1;
            $xpoints = explode(',', $vo['xpoints']);
            $ypoints = explode(',', $vo['ypoints']);
            $this->assign('xpoints', $xpoints);
            $this->assign('ypoints', $ypoints);
            
        }
        $this->assign('vo', $vo);
        // 省市数据
        $regionObj = M('DeliveryRegion');
        $provCond = array('region_level' => 2);
        $provList = $regionObj->where($provCond)->findAll();
        $this->assign('prov_list', $provList);

        if (!empty($vo['prov_id'])) {
            $cityList = $this->citys($vo['prov_id']);
            $this->assign('city_list', $cityList);
        }

        $this->assign('is_edit', $is_edit);

        $this->display('edit_form');
    }
    
    /**
     * 新增驿站页面
     */
    public function add ()
    {
        $this->edit_form();
    }
    
    /**
     * 更新驿站页面
     */
    public function edit ()
    {
        $this->edit_form(intval($_REQUEST ['id']));
    }

    /**
     * 指定省份获取城市信息
     * @param  integer  $prov_id 省份id
     * @param  integer $ajax    是否ajax请求
     * @return mixed           
     */
    public function citys($prov_id = 0, $ajax = 0)
    {
        if (isset($_REQUEST['ajax']) && intval($_REQUEST['ajax']) == 1) {
            $ajax = 1;
            $prov_id = intval($_REQUEST['prov_id']);
        }
        $regionObj = M('DeliveryRegion');
        $cityList = $regionObj->where(array('pid' => $prov_id))->findAll();

        if ($ajax) {
            $this->ajaxReturn($cityList, '', 1, 'json');
        } else {
            return $cityList;
        }
    }
    
    /**
     * 驿站信息保存操作
     */
    public function save ()
    {
		$id = intval($_REQUEST['id']);
		$data['name'] = strim($_REQUEST['name']);
		$data['username'] = strim($_REQUEST['username']);
		$data['address'] = strim($_REQUEST['address']);
		$data['tel'] = strim($_REQUEST['tel']);
		$data['contact'] = strim($_REQUEST['contact']);
		$data['open_time'] = strim($_REQUEST['open_time']);
        $data['prov_id'] = intval($_REQUEST['prov_id']);
		$data['city_id'] = intval($_REQUEST['city_id']);
        $data['xpoint'] = floatval($_REQUEST['xpoint']);
        $xpoints = strim($_REQUEST['xpoints']);
        $data['xpoints'] = $xpoints;
        $data['ypoint'] = floatval($_REQUEST['ypoint']);
        $ypoints = strim($_REQUEST['ypoints']);
        $data['ypoints'] = $ypoints;
		$password = strim($_REQUEST['password']);

		// 参数验证
		if (!$data['name']) {
		    $this->error('请输入名称');
		}
		if (!$data['username']) {
		    $this->error('请输入账户名');
		}
		if (!$data['address']) {
		    $this->error('请输入地址');
		}
		if (!$data['tel']) {
            $this->error('请输入手机联系方式');		    
		} elseif (!check_mobile($data['tel'])) {
            $this->error('手机号码格式错误');
        }
		if (!$data['contact']) {
		    $this->error('请输入联系人');
		}
		if (!$data['city_id']) {
		    $this->error('请选择城市');
		}
		if (!$data['open_time']) {
		    $this->error('请输入营业时间');
		}
        if (empty($xpoints) || empty($ypoints)) {
            $this->error('绘制的范围数据格式有误');
        }
        $points = $this->formatPos($xpoints, $ypoints);
        if (!$points) {
            $this->error('绘制的范围数据格式有误');
        }
        if (intval($_REQUEST['addr_check']) == 0) {
            $this->error('修改地址后需重新定位！');
        }

		// 验证辐射范围是否为0
		/*if ($data['scale_meter'] == 0) {
		    $this->error('辐射范围不能为0');
		}*/
		if ($id) {
			# 修改
		    if (!filter_var($id, FILTER_VALIDATE_INT)) {
		        $this->error('编辑失败！配送点ID无效！');
		    }
		    // 验证账户名是否存在
		    $username_exist = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution where username = '".$data['username']."' and id <> '".$id."' and is_delete = 0");
		    if ($username_exist) {
		        $this->error('账户名已经存在');
		    }
		}else{
		    # 添加
		    // 验证账户名是否存在
		    $username_exist = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."distribution where username = '".$data['username']."' and is_delete = 0");
		    if ($username_exist) {
		        $this->error('账户名已经存在');
		    }
		    if (empty($password)) {
		        $this->error('请填写：密码！');
		    }
		}
		// 对密码进行处理
		if ($password) {
		    $data['password'] = md5($password);
		}

        $data['status'] = 1; // 后台添加的驿站不需要审核??
        $data['adm_memo'] = '后台新增无需审核';
        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = "'".$val."'";
            }
        }
        
        $data['points'] = "GeomFromText('Polygon((".$points."))')";

		if ($id) {
		    // 执行更新操作
		    $GLOBALS['db']->autoExecute_unsafe(DB_PREFIX."distribution",$data,"UPDATE","id=".$id);
			$errno = $GLOBALS['db']->errno();
		} else {
            $data['create_time'] = NOW_TIME;
		    // 执行新增操作
		    $GLOBALS['db']->autoExecute_unsafe(DB_PREFIX."distribution",$data);
			$id = $GLOBALS['db']->insert_id();
			$errno = $GLOBALS['db']->errno();
		}
		
		if ($errno) {
		    save_log($data['name'].$err,0);
		    $this->error('保存失败。请重试');
		} else {
		    save_log($data['name'],1);
		    $this->success("保存成功");
		}
    }
    
    /**
     * 彻底删除驿站记录操作
     */
    public function delete ()
    {
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = intval($_REQUEST['id']);
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $updata = array('is_delete' => 1);
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            
            foreach ($rel_data as $data) {
                // 判断待删除的配送点的状态是否为未删除
                if ($data['is_delete']) {
                    $this->error('您要删除的驿站：“' . $data['name'] . '”状态有误，无法删除');
                } else {
                    // 判断驿站只有在用的配送点
                    $ship = M('DistributionShipping')->where(array('dist_id' => $data['id'], 'is_delete' => 0, 'disabled' => 0))->findAll();
                    if (count($ship) > 0) {
                        $this->error('驿站有在用的配送点,无法删除!');
                    }
                    $info[] = $data['name'];
                }
            }
            if ($info) $info = implode(',', $info); 
            
            $list = M(MODULE_NAME)->where($condition)->save($updata);
            
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

    /**
     * 驿站禁用状态变更
     * @return mixed 
     */
    public function disable()
    {
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $model = M(MODULE_NAME);
        $disabled = $model->where(array('id' => $id))->getField('disabled');
        $disabled ^= 1;
        $res = $model->where(array('id' => $id))->save(array('disabled' => $disabled));
        if ($res) {
            $this->success('操作成功', $ajax);
        } else {
            $this->error('操作失败'.$model->getLastSql(), $ajax);
        }
    }

    protected function formatPos($xpoint, $ypoint)
    {
        $xpoints = explode(',', $xpoint);
        $ypoints = explode(',', $ypoint);
        if (count($xpoints) < 3 || count($xpoints) != count($ypoints)) {
            return false;
        }
        $pos = array();
        $count = count($xpoints);
        for ($i=0; $i < $count; $i++) { 
            $pos[] = $xpoints[$i].' '.$ypoints[$i];
        }
        $pos[] = current($pos);
        return (implode(',', $pos));
    }

    /**
     * 根据关键字搜索可分配驿站(未删除、已审核且未禁用)
     * @return mix 
     */
    public function keySearch()
    {
        $ajax = intval($_REQUEST['ajax']);
        $key = strim($_REQUEST['key']);
        if (empty($key)) {
            $this->error('搜索关键字不能为空', $ajax);
        }
        $condition = array('name' => array('like', '%'.$key.'%'), 'is_delete' => 0, 'status' => 1, 'disabled' => 0);
        $res = M(MODULE_NAME)->where($condition)->findAll();
        if ($res) {
            $html = '<option value="0">请选择</option>';
            foreach ($res as $val) {
                $html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
            }
            $this->success($html, $ajax);
        } else {
            $this->error('未检索到站点，换个关键字试试', $ajax);
        }
    }
    
    public function charge_index(){
        if(isset($_REQUEST['status']))
		{
			$map['status'] = intval($_REQUEST['status']);
		}		
		$model = D ("DistributionMoneySubmit");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
        
    }
    
    /*
     * 驿站提现编辑
     */
    
    public function charge_edit()
    {
        $charge_id = intval($_REQUEST['charge_id']);
        if($charge_id>0){
            $charge_info = M("DistributionMoneySubmit")->getById($charge_id);
            $distribution_info= M("Distribution")->where("id=".$charge_info['distribution_id'])->find();
            $charge_info['name']=$distribution_info['name'];
            $charge_info['distribution_id'] = $distribution_info['id'];
            //logger::write(print_r($charge_info,1));
            $this->assign("type",1);
            $this->assign("charge_info",$charge_info);
        }

    
        $this->display();
    }
    
    
    public function refuse_edit()
    {
        $charge_id = intval($_REQUEST['charge_id']);
        if($charge_id>0){
            $charge_info = M("DistributionMoneySubmit")->getById($charge_id);
            $distribution_info= M("Distribution")->where("id=".$charge_info['distribution_id'])->find();
            $charge_info['name']=$distribution_info['name'];
            $charge_info['distribution_id'] = $distribution_info['id'];
            $this->assign("type",1);
            $this->assign("charge_info",$charge_info);
        }
        $this->display();
    }
    
    public function dorefuse()
    {
        $charge_id = intval($_REQUEST['charge_id']);
        $reason = strim($_REQUEST['reason']);
        $GLOBALS['db']->query("update ".DB_PREFIX."distribution_money_submit set status = 2,reason = '".$reason."' where id = ".$charge_id." and status = 0 ");
        if($GLOBALS['db']->affected_rows()) {
            //send_supplier_msg('', 'withdrawfail', $charge_id);
            $this->success("操作成功");
        } else {
            $this->error("操作失败");
        }
    }
    
    /*
     * 驿站提现审核
     */
    public function docharge()
    {
        $charge_id = intval($_REQUEST['charge_id']);
        $distribution_id = intval($_REQUEST['distribution_id']);
        $log=strim($_REQUEST['log']);
        require_once(APP_ROOT_PATH."system/model/dist_user.php");
        //logger::write(print_r($log,1));exit;
        if($charge_id>0){
            $charge_info = M("DistributionMoneySubmit")->getById($charge_id);
            $distribution_info=M("Distribution")->getById($distribution_id);
            $charge_info['money']=floatval($_REQUEST['money']);
            $distribution_info['money']=floatval($distribution_info['money']);
            if($charge_info['money']<=0)$this->error("提现金额必须大于0");

            if($charge_info['money']>$distribution_info['money'])$this->error("提现超额");
    
            if($charge_info['status']==0)
            {
                M("DistributionMoneySubmit")->where("id=".$charge_info['id'])->setField("status",1);
                M("DistributionMoneySubmit")->where("id=".$charge_info['id'])->setField("money",$charge_info['money']);
//                M("Distribution")->where("id=".$distribution_id)->setField("money",$distribution_info['money']-$charge_info['money']);
                $data=array();
                $data['money']="-".$charge_info['money'];
                $data['type'] =2;
                modify_dist_account($data,$distribution_id,$distribution_info['name']."提现".format_price($charge_info['money'])."元审核通过。".$log);//.提现增加
                save_log($distribution_info['name']."提现".format_price($charge_info['money'])."元审核通过。".$log,1);
                $this->success("确认提现成功");
            }
            else
            {
                $this->error("已提现过，无需再次提现");
            }
    
        }
    }
    
    public function del_charge()
    {
        $id = intval($_REQUEST['id']);
        $charge = M("DistributionMoneySubmit")->getById($id);
        $distribution_info= M("Distribution")->where("id=".$charge['distribution_id'])->find();
        $list = M("DistributionMoneySubmit")->where ("id=".$id )->delete();
        if ($list!==false) {
            save_log($distribution_info['name']."驿站提现".$charge['money']."元记录".l("FOREVER_DELETE_SUCCESS"),1);
            $this->success (l("FOREVER_DELETE_SUCCESS"),1);
        } else {
            save_log($distribution_info['name']."驿站提现".$charge['money']."元记录".l("FOREVER_DELETE_FAILED"),0);
            $this->error (l("FOREVER_DELETE_FAILED"),1);
        }
    
    }
}