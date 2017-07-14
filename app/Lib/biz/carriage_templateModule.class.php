<?php
/**
 * @desc      
 * @author    吴庆祥
 * @since     2017-02-14 09:12  
 */
require APP_ROOT_PATH . 'app/Lib/page.php';

class carriage_templateModule extends BizBaseModule
{
    private $delivery_regions=array();
    function __construct()
    {
        parent::__construct();
        global_run();
        $this->check_auth();
    }
    public function index(){
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        //分页
        $page = intval($_REQUEST['p']);
        $limit =formatLimit($page,5);
        //where条件
        $where="";
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

        $total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."carriage_template as ct  where ct.supplier_id=".$account_info['supplier_id'].$where);
        $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."carriage_template  where supplier_id=".$account_info['supplier_id'].$where." order by name,id desc ".$limit);
        foreach ($list as $k=>$v){
            if($v["carriage_type"]==2)continue;
            $carriage_detail_data = unserialize($v['cache_carriage_detail_data']);
            $carriage_detail_data=$this->_cityIdTransferToCityName($carriage_detail_data);
            $list[$k]['carriage_detail_data']=$carriage_detail_data;
        }

        formatPage($total,5);
        $GLOBALS['tmpl']->assign("name",$name);
        $GLOBALS['tmpl']->assign("valuation_type",$valuation_type);
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->assign("add_template_url",url("biz","carriage_template#add"));
        $GLOBALS['tmpl']->assign("page_title", "配送模板列表");
        $GLOBALS['tmpl']->display("pages/carriage_template/index.html");
    }
    public function add(){
        init_app_page();
        //输出省数据
        $delivery_region_county=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where pid=1");
        $delivery_regions = load_auto_cache('cache_delivery_regions');
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "配送模板添加");
        $GLOBALS['tmpl']->assign("delivery_regions",$delivery_regions);
        $GLOBALS['tmpl']->assign("delivery_region_county",$delivery_region_county);
        $GLOBALS['tmpl']->assign("form_url",url("biz","carriage_template#insert"));
        $GLOBALS['tmpl']->assign("delivery_region_url",url("biz","carriage_template#get_delivery_region"));
        $GLOBALS['tmpl']->display("pages/carriage_template/add_or_edit.html");
    }
    public function edit(){
        init_app_page();
        $data_id=$_REQUEST['data_id'];
        $list=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."carriage_template where id=".$data_id);
        $regions_list=unserialize($list['cache_carriage_detail_data']);
        $default_regions_list=array();
        $limit_regions_list=array();
        foreach($regions_list as $val){
            if($list['valuation_type']==1){
                $val['express_start']=intval($val['express_start']);
                $val['express_plus']=intval($val['express_plus']);
            }else{
                $val['express_start']=number_format($val['express_start'],1);
                $val['express_plus']=number_format($val['express_plus'],1);
            }
            if($val["region_ids"]){
                $limit_regions_list[]=$val;
            }else{
                $default_regions_list=$val;
            }
        }
        $regions_list=$this->_cityIdTransferToCityName($limit_regions_list);
        //获取区域选择的数据
        $delivery_regions =$this->delivery_regions;

        //查询省市区
        $delivery_region_county=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where pid=1");
        $delivery_region_city=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where pid=".$list['province']);
        $delivery_region_area=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where pid=".$list['city']);
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "配送模板编辑");
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->assign("regions_list",$regions_list);
        $GLOBALS['tmpl']->assign("default_regions_list",$default_regions_list);
        $GLOBALS['tmpl']->assign("delivery_regions",$delivery_regions);
        $GLOBALS['tmpl']->assign("delivery_region_county",$delivery_region_county);
        $GLOBALS['tmpl']->assign("delivery_region_city",$delivery_region_city);
        $GLOBALS['tmpl']->assign("delivery_region_area",$delivery_region_area);
        $GLOBALS['tmpl']->assign("form_url",url("biz","carriage_template#update"));
        $GLOBALS['tmpl']->assign("delivery_region_url",url("biz","carriage_template#get_delivery_region"));
        $GLOBALS['tmpl']->display("pages/carriage_template/add_or_edit.html");
    }
    public function insert(){
        $carriage_template=array();
        $data=array();
        $data['status']=1;
        $data['info']="保存数据成功";
        $data['jump']=url("biz","carriage_template#index");
        $account_info = $GLOBALS['account_info'];
        //配送模板数据验证
        $supplier_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$account_info['supplier_id']);
        $carriage_template['name']=$_REQUEST['name'];
        $carriage_template['province']=$_REQUEST['province'];
        $carriage_template['supplier_name']=$supplier_name;
        $carriage_template['city']=$_REQUEST['city'];
        $carriage_template['area']=$_REQUEST['area'];
        $carriage_template['supplier_id']=intval($account_info['supplier_id']);
        $carriage_template['carriage_type']=$_REQUEST['carriage_type'];
        $carriage_template['valuation_type']=$_REQUEST['valuation_type'];

        $this->_checkTemplateData($carriage_template);

        //地区设置
        $carriage_arr=array();
        $carriage_arr[] = array(
            'express_start'=>floatval($_REQUEST['express_start']),
            'express_postage'=>floatval($_REQUEST['express_postage']),
            'express_plus'=>floatval($_REQUEST['express_plus']),
            'express_postage_plus'=>floatval($_REQUEST['express_postage_plus']),
            'region_ids'=>'',
        );
        if($carriage_template['carriage_type']==1){
            foreach($_REQUEST['region_first_weight'] as $key=>$val){
                $detail=array();
                if($_REQUEST['region_first_fee'][$key]==""||$_REQUEST['region_continue_fee'][$key]==""){
                    continue;
                }
                $detail['express_start']=floatval($val);
                $detail['express_postage']=floatval($_REQUEST['region_first_fee'][$key]);
                $detail['express_plus']=floatval($_REQUEST['region_continue_weight'][$key]);
                $detail['express_postage_plus']=floatval($_REQUEST['region_continue_fee'][$key]);
                $detail['region_ids']=$_REQUEST['region_support_region'][$key];
                $carriage_arr[]=$detail;
            }

        }

        require_once APP_ROOT_PATH."system/model/CarriageTemplate.php";
        CarriageTemplate::save($carriage_template,$carriage_arr);

        ajax_return($data);
    }
    public function update(){
        $carriage_template=array();
        $data=array();
        $data['status']=1;
        $data['info']="保存数据成功";
        $data['jump']=url("biz","carriage_template#index");
        $id=$_REQUEST['id'];
        $this->_isEmpty($id,"","id");
        $account_info = $GLOBALS['account_info'];
        $supplier_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".$account_info['supplier_id']);
        //配送模板数据验证
        $carriage_template['name']=$_REQUEST['name'];
        $carriage_template['id']=$id;
        $carriage_template['province']=$_REQUEST['province'];
        $carriage_template['city']=$_REQUEST['city'];
        $carriage_template['area']=$_REQUEST['area'];
        $carriage_template['supplier_name']=$supplier_name;
        $carriage_template['supplier_id']=$account_info['supplier_id'];
        $carriage_template['carriage_type']=$_REQUEST['carriage_type'];
        $carriage_template['valuation_type']=$_REQUEST['valuation_type'];
        $this->_checkTemplateData($carriage_template);

        //地区设置
        $carriage_arr=array();
        $carriage_arr[] = array(
            'express_start'=>floatval($_REQUEST['express_start']),
            'express_postage'=>floatval($_REQUEST['express_postage']),
            'express_plus'=>floatval($_REQUEST['express_plus']),
            'express_postage_plus'=>floatval($_REQUEST['express_postage_plus']),
            'region_ids'=>'',
        );
        if($carriage_template['carriage_type']==1){
            foreach($_REQUEST['region_first_weight'] as $key=>$val){
                $detail=array();
                if($_REQUEST['region_first_fee'][$key]==""||$_REQUEST['region_continue_fee'][$key]==""){
                    continue;
                }

                $detail['express_start']=$val;
                $detail['express_postage']=$_REQUEST['region_first_fee'][$key];
                $detail['express_plus']=$_REQUEST['region_continue_weight'][$key];
                $detail['express_postage_plus']=$_REQUEST['region_continue_fee'][$key];
                $detail['region_ids']=$_REQUEST['region_support_region'][$key];
                $carriage_arr[]=$detail;
            }

        }

        require_once APP_ROOT_PATH."system/model/CarriageTemplate.php";

        CarriageTemplate::save($carriage_template,$carriage_arr,$id);

        ajax_return($data);
    }
    public function delete(){
        $data=array();
        $data['status']=1;
        $data['info']="数据删除成功";
        $id=$_REQUEST['data_id'];
        $this->_isEmpty($id,"","id");
        $GLOBALS['db']->query("delete from ".DB_PREFIX."carriage_template where id=".$id." and is_use=0");
        if($GLOBALS['db']->affected_rows()>0){
            $GLOBALS['db']->query("delete from ".DB_PREFIX."carriage_detail where carriage_id=".$id);
        }else{
            $data['status']=0;
            $data['info']="模板已经被使用不能删除";
        }
        ajax_return($data);
    }
    public function get_delivery_region(){
        $pid=$_REQUEST['pid'];
        $data['status']=1;
        $data['data']=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery_region where pid=".$pid);
        ajax_return($data);
    }
    private function _checkTemplateData($carriage_template){
        $this->_isEmpty($carriage_template,"name","模板名称");
        $this->_isEmpty($carriage_template,"province","省份");
        $this->_isEmpty($carriage_template,"city","城市");
        $this->_isEmpty($carriage_template,"carriage_type","运费类型");
        $this->_isEmpty($carriage_template,"valuation_type","计价类型");
    }

    private function _isEmpty($checkData,$name,$value){
        $data=array();
        $data['status']=0;
        if(!is_array($checkData)&&checkData==""){
            $data['info']=$value."不能为空";
            ajax_return($data);
        }else if($checkData[$name]==""){
            $data['info']=$value."不能为空";
            ajax_return($data);
        }
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
    private function array_diff_fast($firstArray, $secondArray) {
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
}