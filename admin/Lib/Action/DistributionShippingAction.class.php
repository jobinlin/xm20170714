<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DistributionShippingAction extends CommonAction {
    function __construct()
    {
        parent::__construct();
        if(!IS_OPEN_DISTRIBUTION){
            $this->error (l("请先开启驿站功能"),0);
        }
    }
    /**
     * 社区驿站-配送设置列表
     * {@inheritDoc}
     * @see CommonAction::index()
     */
    public function index () {
        $page_idx = intval($_REQUEST['p']) == 0 ? 1 : intval($_REQUEST['p']);
        $page_size = C('PAGE_LISTROWS');
        $limit = (($page_idx-1) * $page_size) . "," . $page_size;
        
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
        
        if (isset($order)) {
//             $orderby = "order by ".$order." ".$sort;
            $orderby = array(
                $order => $sort    
            );
        } else {
            $orderby = "";
        }
        
        $where = array();
        $where['ds.is_delete'] = 0;
        if (intval($_REQUEST['dist_id'])) {
            $where['ds.dist_id'] = intval($_REQUEST['dist_id']);
        }
        // 配送点名称
        if (strlen($_REQUEST['name'])) {
            $where['d.name'] = array('LIKE', '%' . $_REQUEST['name'] . '%');
        }
        // 地址名称
        if (strlen($_REQUEST['poi_name'])) {
            $where['ds.poi_name'] = array('LIKE', '%' . $_REQUEST['poi_name'] . '%');
        }
        // 地址信息
        if (strlen($_REQUEST['poi_addr'])) {
            $where['ds.poi_addr'] = array('LIKE', '%' . $_REQUEST['poi_addr'] . '%');
        }
        
        // 统计记录数
        $total = M('distribution_shipping as `ds`')
                ->join('INNER JOIN ' . C('DB_PREFIX') . 'distribution AS `d` ON d.id = ds.dist_id')
                ->where($where)->count();
//         dump(M('distribution_shipping as `ds`')->getLastSql());
        // 取配送设置列表
        $list = M('distribution_shipping as `ds`')
                ->join('INNER JOIN ' . C('DB_PREFIX') . 'distribution AS `d` ON d.id = ds.dist_id')
                ->where($where)->field('`ds`.*, `d`.`name`')->order($orderby)->limit($limit)->select();
        if ($list) {
            foreach ($list as &$row) {
                $row['region'] = $this->_getRegionName($row['region_lv1']) . '，' .
                                 $this->_getRegionName($row['region_lv2']) . '，' .
                                 $this->_getRegionName($row['region_lv3']) . '，' .
                                 $this->_getRegionName($row['region_lv4']);
            }
        }
                
        // 创建分页对象
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
        $this->display();
        return ;
    }
    
    public function add () {
        $this->assign("page_title","新建配送点");
        $this->edit_form();
    }
    
    public function edit () {

        $this->assign("page_title","编辑配送点");
        $this->edit_form(intval($_REQUEST['id']));
    }
    
    /**
     * 新增/更新表单
     * @param number $id
     * @param number $editType
     */
    public function edit_form ($id = 0, $editType = 1) {
        $GLOBALS['tmpl'] = $this;
        $GLOBALS['tmpl']->assign('edit_type', $editType);
        
        $regionLv1List = $this->_getRegionData(0);
        $regionLv2List = $this->_getRegionData(1);
        $regionLv3List = array();
        $regionLv4List = array();
        // 输出基础数据
        $vo = array();
        if (1 == $editType) {
            $vo = M('distribution_shipping')->find($id);
            
            // 验证regionLv1
            if ($this->_getRegionName($vo['region_lv1'])) {
                $regionLv2List = $this->_getRegionData($vo['region_lv1']);
                // 验证regionLv2
                if ($this->_getRegionName($vo['region_lv2'])) {
                    $regionLv3List = $this->_getRegionData($vo['region_lv2']);
                    // 验证regionLv3
                    if ($this->_getRegionName($vo['region_lv3'])) {
                        $regionLv4List = $this->_getRegionData($vo['region_lv3']);
                    }
                }
            }
        } 

        if($vo){
            $distData = M('distribution')->where(array('is_delete'=>0,"city_id"=>$vo['region_lv3']))->field('id,name')->select();
            $GLOBALS['tmpl']->assign('distData', $distData);
        }

        $GLOBALS['tmpl']->assign('regionLv1List', $regionLv1List);
        $GLOBALS['tmpl']->assign('regionLv2List', $regionLv2List);
        $GLOBALS['tmpl']->assign('regionLv3List', $regionLv3List);
        $GLOBALS['tmpl']->assign('regionLv4List', $regionLv4List);
        $GLOBALS['tmpl']->assign('vo', $vo);
        $GLOBALS['tmpl']->display('edit_form');
    }
    
    /**
     * 配送设置保存操作
     */
    public function save () {
        $id = intval($_REQUEST['id']);
        $data = array();
        $data['dist_id'] = intval($_REQUEST['dist_id']);
        $data['region_lv1'] = intval($_REQUEST['region_lv1']);
        $data['region_lv2'] = intval($_REQUEST['region_lv2']);
        $data['region_lv3'] = intval($_REQUEST['region_lv3']);
        $data['region_lv4'] = intval($_REQUEST['region_lv4']);
        $data['xpoint'] = number_format(strim($_REQUEST['post_xpoint']), 6);
		$data['ypoint'] = number_format(strim($_REQUEST['post_ypoint']), 6);
		$data['poi_addr'] = strim($_REQUEST['poi_addr']);
		$data['poi_name'] = strim($_REQUEST['poi_name']);
		$data['is_delete'] = 0;

		// 参数验证
		if (!filter_var($data['dist_id'], FILTER_VALIDATE_INT)) {
		    $this->error('请选择：所属社区驿站！');
		}
		if (!filter_var($data['region_lv1'], FILTER_VALIDATE_INT) || !$this->_checkRegion($data['region_lv1'], 0)) {
		    $this->error('请选择：省市区！');
		} 
		if (!filter_var($data['region_lv2'], FILTER_VALIDATE_INT) || !$this->_checkRegion($data['region_lv2'], $data['region_lv1'])) {
		    $this->error('请选择：省市区！');
		}
		if (!filter_var($data['region_lv3'], FILTER_VALIDATE_INT) || !$this->_checkRegion($data['region_lv3'], $data['region_lv2'])) {
		    $this->error('请选择：省市区！');
		}
		if (!filter_var($data['region_lv4'], FILTER_VALIDATE_INT) || !$this->_checkRegion($data['region_lv4'], $data['region_lv3'])) {
		    $this->error('请选择：省市区！');
		}
		
		if (!strlen($data['poi_addr'])) {
		    $this->error('请输入：地址名称！');
		}
		if (!strlen($data['poi_name'])) {
		    $this->error('请输入：配送点名称！');
		}
        if (intval($_REQUEST['addr_check']) == 0) {
            $this->error('修改地址后需重新定位！');
        }
		
		if ($id) {
		    # 编辑
		    if (!filter_var($id, FILTER_VALIDATE_INT)) {
		        $this->error('编辑失败，配送设置ID无效！');
		    }
		    $ret = M('distribution_shipping')->where(array('id' => $id))->save($data);
		} else {
		    # 新增
		    $ret = M('distribution_shipping')->add($data);
		}
		
		if (false === $ret) {
		    save_log('配送设置编辑错误，ERRSQL：' . M('distribution_shipping')->getLastSql(), 0);
		    $this->error('编辑失败！');
		} else {
		    save_log('配送设置编辑成功', 1);
		    $this->success('保存成功！');
		}
    }
    
    /**
     * 配送设置删除动作
     */
    public function delete () {
        // 逻辑删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];

        $where = array();
        $where['id'] = array('IN', $id);
        $where['is_delete'] = 0;
        
        $delData = array();
        $delData['is_delete'] = 1;
        
        $dataSet = M('distribution_shipping')->where($where)->select();
        
        $info = array();
        foreach ($dataSet as $dataRow) {
            // 验证指定的配送设置记录的状态
            if ($dataRow['is_delete']) {
                $this->error('您要删除的配送设置ID：“' . $dataRow['id'] . '”状态有误，无法删除！');
            } else {
                $info[] = $dataRow['id'];
            }
        }
        if ($info) $info = implode(',', $info); 
        
        // 执行逻辑删除操作
        $ret = M('distribution_shipping')->where($where)->save($delData);
        
        if (false !== $ret) {
            save_log('配送设置ID：' . $info . l("DELETE_SUCCESS"), 1);
            $this->success(l("DELETE_SUCCESS"), $ajax);
        } else {
            save_log('配送设置ID：' . $info . l("DELETE_FAILED"), 0);
            $this->error(l("DELETE_FAILED"), $ajax);
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
    
    /**
     * 加载地区API
     */
    public function getRegionApi () {
        $pid = intval($_REQUEST['pid']);
        if ($pid) {
            if (!filter_var($pid, FILTER_VALIDATE_INT)) {
                $this->ajaxReturn('', '请选择：上级地区！', 0);
            }
        } else {
            $pid = 1;
        }
        $regionList = M('delivery_region')->getField('id,name',array('pid' => $pid));
        $this->ajaxReturn('', $regionList);
    }
    
    /**
     * 取地区名称
     * @param int $id 地区ID
     */
    private function _getRegionName ($id) {
        return M('delivery_region')->getField('name',array('id'=>$id));
//         return $GLOBALS['db']->getOne('SELECT name FROM ' . DB_PREFIX . 'delivery_region WHERE id = ' . $id);
    }
    
    /**
     * 取配送地区数据
     * @param int $id 地区ID
     */
    private function _getRegionData ($pid) {
        return M('delivery_region')->where(array('pid' => $pid))->select();
//         return $GLOBALS['db']->getAll('SELECT * FROM ' . DB_PREFIX . 'delivery_region WHERE pid = ' . $id);
    }
    
    /**
     * 验证地区
     * @param int $id 地区ID
     * @param int $pid 上级地区ID
     */
    private function _checkRegion ($id, $pid) {
        return M('delivery_region')->where(array('id' => $id, 'pid' => $pid))->find();
    }
    
    /**
     * 验证社区驿站
     * @param int $id 社区驿站ID
     */
    private function _checkDist ($id) {
        return M('distribution')->where(array('id' => $id, 'is_delete' => 0))->find();
    }

    /**
     * @desc 根据驿站名进行模糊查找
     * @author    吴庆祥
     */
    public function get_distribution(){
        $where = array();
        if (!empty($_REQUEST['name'])) {
            $name = strim($_REQUEST['name']);
            $where['name'] = array('like', '%'.$name.'%');
        }
        if (!empty($_REQUEST['prov_id'])) {
            $where['prov_id'] = intval($_REQUEST['prov_id']);
        }
        if (!empty($_REQUEST['city_id'])) {
            $where['city_id'] = intval($_REQUEST['city_id']);
        }
        $where['is_delete'] = 0;
        $where['status'] = 1;
        $where['disabled'] = 0;
        $model = M("distribution");
        $list = $model->field("id,name")->where($where)->select();
        // $list[] = $model->getLastSql();
        ajax_return($list);
    }
}