<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DistributionAuthAction extends CommonAction
{
    function __construct()
    {
        parent::__construct();
        if(!IS_OPEN_DISTRIBUTION){
            $this->error (l("请先开启驿站功能"),0);
        }
    }
    public function index()
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
        }else
        {
            $orderby = "";
        }
        $where = array();
        if (isset($_REQUEST['name'])) {
            $name = strim($_REQUEST['name']);
            if ($name) {
                $where['name'] = array('like', '%'.$name.'%');
            }
        }
        $model = M('Distribution');
        $list = $model->where($where)->order($orderby)->limit($limit)->findAll();
        $total = $model->where($where)->count('id');
        
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
    }
    
    public function edit()
    {
        $id = intval($_REQUEST['id']);

        $model = M('Distribution');
        $where = array('id' => $id, 'is_delete' => 0);
        $vo = $model->where($where)->find();
        if (empty($vo)) {
        	$this->error('参数错误');
        }

        $xpoints = explode(',', $vo['xpoints']);
        $ypoints = explode(',', $vo['ypoints']);
        if (!empty($xpoints) && !empty($ypoints) && count($xpoints) == count($ypoints)) {
        	$this->assign('has_map', 1);
        	$this->assign('xpoints', $xpoints);
        	$this->assign('ypoints', $ypoints);
        }

        if ($vo['status'] != 0) {
        	$statusStr = array(1 => '同意入驻', 2 => '拒绝申请');
        	$this->assign('status_str', $statusStr[$vo['status']]);
        }
        

        $this->assign('vo', $vo);

        // 省市数据
        $regionObj = M('DeliveryRegion');
        $provCond = array('id' => array('in', array($vo['prov_id'], $vo['city_id'])));
        $region = $regionObj->where($provCond)->order('id')->findAll();
        $this->assign('region', $region[0]['name'].'  '.$region[1]['name']);

        $this->display();
    }

    public function save()
    {
    	$id = intval($_REQUEST['id']);
    	$data = array();
    	$model = M('Distribution');
    	$where = array('id' => $id, 'is_delete' => 0, 'status' => 0);
    	$data['status'] = intval($_REQUEST['status']);
        if (empty($data['status'])) {
            $this->error('请选择一个审核结果');
        }
    	if (!empty($_REQUEST['adm_memo'])) {
    		$data['adm_memo'] = strim($_REQUEST['adm_memo']);
    	}
    	$res = $model->where($where)->save($data);
    	if ($res) {
    		$this->success('审核成功');
    	} else {
    		logger::write('后台驿站审核失败'.$model->getLastSql());
    		$this->error('审核失败');
    	}
    }

    /**
     * 彻底删除驿站记录操作
     */
    public function delete ()
    {
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $updata = array('is_delete' => 1);
            $rel_data = M('Distribution')->where($condition)->findAll();
            
            foreach ($rel_data as $data) {
                // 判断待删除的配送点的状态是否为未删除
                if ($data['is_delete']) {
                    $this->error('您要删除的配送点：“' . $data['name'] . '”状态有误，无法删除');
                } else {
                    $info[] = $data['name'];
                }
            }
            if ($info) $info = implode(',', $info); 
            
            $list = M('Distribution')->where($condition)->save($updata);
            
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
}