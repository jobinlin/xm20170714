<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class CarriageTemplateAction extends CommonAction{
    private $delivery_regions=array();
    public function index()
    {
        $where = '';
        $name=$_REQUEST['name'];
        $valuation_type=intval($_REQUEST['valuation_type']);
        if($name){
            $where.=" and instr(name,'{$name}')>0 ";
        }
        if($valuation_type){
            if($valuation_type==3){
                $where.=" and carriage_type=2 ";
            }else{
                $where.=" and carriage_type=1 and valuation_type={$valuation_type} ";
            }
        }
		
        $count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."carriage_template as ct where ct.supplier_id=0 ".$where);
        $p = new Page ($count);
        $limit=$p->firstRow . ',' . $p->listRows;
        $order_by = " order by name asc,id asc ";

		$sql = "select *  from ".DB_PREFIX."carriage_template where supplier_id=0".$where.$order_by." limit ".$limit;
		
        $list = $GLOBALS['db']->getAll($sql);

        foreach ($list as $k=>$v){
            $carriage_detail_data = unserialize($v['cache_carriage_detail_data']);
            $carriage_detail_data=$this->_cityIdTransferToCityName($carriage_detail_data);
            $list[$k]['carriage_detail_data']=$carriage_detail_data;
        }
        $page = $p->show ();
        $this->assign("list",$list);
        $this->assign ( "page", $page );
        $this->display ();
    }
    public function add()
    {
        $region_lv1 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = 0");  //一级地址
        $delivery_regions = load_auto_cache('cache_delivery_regions');

        $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = 1");  //二级地址

        $this->assign("region_lv1",$region_lv1);
        $this->assign("region_lv2",$region_lv2);
        $this->assign("delivery_regions",$delivery_regions);
        $this->display();
    }
    public function insert() {
        $data = M(MODULE_NAME)->create ();
        $log_info = "新增运费模板";
        //开始验证有效性
        if(!check_empty($data['name']))
        {
            $this->error("模板名称不能为空");
        }

        //发货地区
        $data['country'] = intval($_REQUEST['region_lv1']);
        $data['province'] = intval($_REQUEST['region_lv2']);
        $data['city'] = intval($_REQUEST['region_lv3']);
        $data['area'] = intval($_REQUEST['region_lv4']);

        require_once APP_ROOT_PATH."system/model/CarriageTemplate.php";

        //开始处理配送地区
        $carriage_arr[] = array(
            'express_start'=>floatval($_REQUEST['express_start']),
            'express_postage'=>floatval($_REQUEST['express_postage']),
            'express_plus'=>floatval($_REQUEST['express_plus']),
            'express_postage_plus'=>floatval($_REQUEST['express_postage_plus']),
            'region_ids'=>'',
        );
        if($data['carriage_type']==1){
            //是否存在区域物流费用设置
            if($_REQUEST['tbl_except_group']){
                $tbl_except_group = explode(",",$_REQUEST['tbl_except_group']);
                foreach ($tbl_except_group as $k=>$v){
                    $except_temp = array();
                    //必须存在有特殊配置的城市
                    if($_REQUEST['express_areas_'.$v]){
                        $except_temp['express_start'] =floatval($_REQUEST['express_start_'.$v]);
                        $except_temp['express_postage'] =floatval($_REQUEST['express_postage_'.$v]);
                        $except_temp['express_plus'] =floatval($_REQUEST['express_plus_'.$v]);
                        $except_temp['express_postage_plus'] =floatval($_REQUEST['express_postage_plus_'.$v]);
                        $except_temp['region_ids'] = $_REQUEST['express_areas_'.$v];
                        $carriage_arr[] = $except_temp;
                    }
                }
            }
        }

        $rel = CarriageTemplate::save($data,$carriage_arr);

        if (false !== $rel) {
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
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $data = M(MODULE_NAME)->where($condition)->find();
        $this->assign("data",$data);

        $delivery_regions = load_auto_cache('cache_delivery_regions');
        $this->assign("delivery_regions",$delivery_regions);

        //格式化城市id逗号分隔字符串数组如：[江苏] => 220,221,222,223,224,225,226,227,228,229,230,231,232
        $format_province_list = array();
        $format_city_list = array();
        foreach ($delivery_regions as $k=>$v){
            foreach ($v['province_arr'] as $s_k=>$s_v){
                $city_temp = array();
                foreach ($s_v['city_list'] as $ss_k=>$ss_v){
                    $city_temp[] = $ss_v['id'];
                    $format_city_list[$ss_v['name']] = $ss_v['id'];
                }
                $format_province_list[$s_v['name']] = implode(",",$city_temp);
            }
        }


        //获取模板下运费明细列表
        $carriage_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."carriage_detail where carriage_id=".$data['id']." order by sort asc");

        $format_carriage_list = array();
        $default_carriage = array();

        foreach ($carriage_list as $k=>$v){
            $region_ids_arr = explode(",",$v['region_ids']);
            if ($v['region_ids']==''){
                $default_carriage = $v;
                unset($carriage_list[$k]);
            }else{
                $city_name_temp_arr = array();
                $diff_arr = array();
                foreach ($format_province_list as $c_k=>$c_v){
                    $diff_arr = explode(",",$c_v);//要比较的数组
                    $diff_rel = array(); //对比的返回结果
                    $diff_rel = $this->array_diff_fast($diff_arr,$region_ids_arr);
                    if(empty($diff_rel)){
                        $city_name_temp_arr[] = $c_k;
                        $region_ids_arr = $this->array_diff_fast($region_ids_arr,$diff_arr);
                    }
                }
                foreach ($format_city_list as $city_k=>$city_v){
                    if(in_array($city_v,$region_ids_arr)){
                    $city_name_temp_arr[] = $city_k;
                }
            }
                $carriage_list[$k]['show_city_name'] = implode(",",$city_name_temp_arr);
            }
        }
        if (empty($default_carriage)){
            $default_carriage['express_start']=1;
            $default_carriage['express_plus']=1;
        }

        $this->assign("default_carriage",$default_carriage);
        $this->assign("carriage_list",$carriage_list);

        
        //输出发货地址
        $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = 1");  //二级地址
        foreach($region_lv2 as $k=>$v)
        {
            if($v['id'] == $data['province'])
            {
                $region_lv2[$k]['selected'] = 1;
                break;
            }
        }

        $region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$data['province']);  //三级地址
        foreach($region_lv3 as $k=>$v)
        {
            if($v['id'] == $data['city'])
            {
                $region_lv3[$k]['selected'] = 1;
                break;
            }
        }

        $region_lv4 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$data['city']);  //四级地址
        foreach($region_lv4 as $k=>$v)
        {
            if($v['id'] == $data['area'])
            {
                $region_lv4[$k]['selected'] = 1;
                break;
            }
        }

        $this->assign("region_lv2",$region_lv2);
        $this->assign("region_lv3",$region_lv3);
        $this->assign("region_lv4",$region_lv4);
        $this->display();

    }
    public function update() {
        $id = intval($_REQUEST['id']);
        $data = M(MODULE_NAME)->create ();
        $log_info = "编辑运费模板";
        //开始验证有效性
        if(!check_empty($data['name']))
        {
            $this->error("模板名称不能为空");
        }

        //发货地区
        $data['country'] = intval($_REQUEST['region_lv1']);
        $data['province'] = intval($_REQUEST['region_lv2']);
        $data['city'] = intval($_REQUEST['region_lv3']);
        $data['area'] = intval($_REQUEST['region_lv4']);

        require_once APP_ROOT_PATH."system/model/CarriageTemplate.php";

        //开始处理配送地区
        $carriage_arr[] = array(
            'express_start'=>floatval($_REQUEST['express_start']),
            'express_postage'=>floatval($_REQUEST['express_postage']),
            'express_plus'=>floatval($_REQUEST['express_plus']),
            'express_postage_plus'=>floatval($_REQUEST['express_postage_plus']),
        );

        if($_REQUEST['carriage_type']==1){//自定义运费
            //是否存在区域物流费用设置
            if($_REQUEST['tbl_except_group']){
                $tbl_except_group = explode(",",$_REQUEST['tbl_except_group']);

                foreach ($tbl_except_group as $k=>$v){
                    $except_temp = array();
                    //必须存在有特殊配置的城市
                    if($_REQUEST['express_areas_'.$v]){
                        $except_temp['express_start'] =floatval($_REQUEST['express_start_'.$v]);
                        $except_temp['express_postage'] =floatval($_REQUEST['express_postage_'.$v]);
                        $except_temp['express_plus'] =floatval($_REQUEST['express_plus_'.$v]);
                        $except_temp['express_postage_plus'] =floatval($_REQUEST['express_postage_plus_'.$v]);
                        $except_temp['region_ids'] = $_REQUEST['express_areas_'.$v];
                        $carriage_arr[] = $except_temp;
                    }
                }
            }
        }

        if ($id) {
            $rel = CarriageTemplate::save($data,$carriage_arr,$id);
            //成功提示
            save_log($log_info.L("UPDATE_SUCCESS"),1);

            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);
            $this->error(L("UPDATE_FAILED"));
        }
    }

    public function foreverdelete() {

        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset ( $id )){
            $list = M(MODULE_NAME)->where ( array ('id' => array ('in', explode ( ',', $id ) ),'is_use'=>array('eq',0),'supplier_id'=>0))->delete();
            if ($list!==false&&$list>0) {
                M("CarriageDetail")->where ( array ('carriage_id' => array ('in', explode ( ',', $id ) )) )->delete();
                save_log("运费模板".l("FOREVER_DELETE_SUCCESS"),1);
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $carriage_template_is_use = M(MODULE_NAME)->where ( array ('id' => array ('in', explode ( ',', $id ) ),'is_use'=>array('gt',0),'supplier_id'=>0))->findAll();
                if($carriage_template_is_use){
                    save_log("运费模板".l("FOREVER_DELETE_FAILED"),0);
                    $this->error (l("模板已使用过，删除失败"),$ajax);
                }else{
                    save_log("运费模板".l("FOREVER_DELETE_FAILED"),0);
                    $this->error (l("商户模板不能删除"),$ajax);
                }
            }
        }else{
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
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

    //模拟 array_diff() 函数在处理大数组时的效率问题
    public function array_diff_fast($firstArray, $secondArray) {
        // 转换第二个数组的键值关系
        $secondArray = array_flip($secondArray);
        // 循环第一个数组
        foreach($firstArray as $key => $value) {
            // 如果第二个数组中存在第一个数组的值
            if (isset($secondArray[$value])) {
                // 移除第一个数组中对应的元素
                unset($firstArray[$key]);
            }
        }
        return $firstArray;
    }
    private  function _cityIdTransferToCityName($carriage_list){
        if(empty($this->delivery_regions)){
            $this->delivery_regions = load_auto_cache('cache_delivery_regions');
        }
        $delivery_regions=$this->delivery_regions;
        //格式化城市id逗号分隔字符串数组如：[江苏] => 220,221,222,223,224,225,226,227,228,229,230,231,232
        $format_province_list = array();
        $format_city_list = array();
        foreach ($delivery_regions as $k=>$v){
            foreach ($v['province_arr'] as $s_k=>$s_v){
                $city_temp = array();
                foreach ($s_v['city_list'] as $ss_k=>$ss_v){
                    $city_temp[] = $ss_v['id'];
                    $format_city_list[$ss_v['name']] = $ss_v['id'];
                }
                $format_province_list[$s_v['name']] = implode(",",$city_temp);
            }
        }
        foreach ($carriage_list as $k=>$v){
            $region_ids_arr = explode(",",$v['region_ids']);
            if ($v['region_ids']==''){
                continue;
            }else{
                $city_name_temp_arr = array();
                foreach ($format_province_list as $c_k=>$c_v){
                    $diff_arr = explode(",",$c_v);//要比较的数组
                    $diff_rel = $this->array_diff_fast($diff_arr,$region_ids_arr);
                    if(empty($diff_rel)){
                        $city_name_temp_arr[] = $c_k;
                        $region_ids_arr = $this->array_diff_fast($region_ids_arr,$diff_arr);
                    }
                }
                foreach ($format_city_list as $city_k=>$city_v){
                    if(in_array($city_v,$region_ids_arr)){
                        $city_name_temp_arr[] = $city_k;
                    }
                }
                $carriage_list[$k]['show_city_name'] = implode(",",$city_name_temp_arr);
            }
        }
        return $carriage_list;
    }
}
?>