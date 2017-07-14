<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/page.php';
class supplierModule extends HizBaseModule
{
    public function __construct()
    {
        parent::__construct();
        global_run();
        if(!IS_OPEN_AGENCY){
            showHizErr("非法访问",0,url("index","index"));
        }
    }
    
    /**
     * 输入：key 搜索关键字
     * 
     * 输出：
     * [list]=array(
     *     [0] => Array
            (
                [id] => 92     
                [name] => 福州代理测试          
                [money] => 100     余额
                [sale_money] => 2000.15      销售额
                [refund_money] => 180.03    用户退款金额
                [allow_refund] => 0         是否支持退款审核
                [allow_publish_verify] => 1     是否支持自动发布
                [publish_verify_balance] => 89%     自动审核时的结算费用率
                [location_count] => 1            门店数量
                [detail_url] => /o2onew/hiz.php?ctl=supplier&act=detail&id=92     编辑地址
                [location_url] => /o2onew/hiz.php?ctl=location&data_id=92     查看门店列表
            )
     *      
     * )
     * [add_supplier_url]=> /o2onew/hiz.php?ctl=supplier&act=publish   添加商家
     * [add_location_url]=> /o2onew/hiz.php?ctl=location&act=publish   添加门店
     * 
     *   */
	public function index()
	{		
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
        $account_id = $account_info['id'];
        
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }
       
        /* 获取参数 */
        $key=strim($_REQUEST['key']);
        
        /* 业务逻辑部分 */
        $conditions .= " where s.agency_id = ".$account_id; // 查询条件
        if($key)
            $conditions .= " and s.name like '%".$key."%'";
         
        /* 分页 */
        $page_size = PAGE_SIZE;
        $page = intval($_REQUEST['p']);
        if ($page == 0)
            $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $sql_count = " select count(distinct(s.id)) from " . DB_PREFIX . "supplier as s ".$conditions;
        $sql = " select s.id,s.name,s.money,s.sale_money,s.refund_money,s.allow_refund,s.allow_publish_verify,s.publish_verify_balance,count(sl.id) as location_count from " . DB_PREFIX . "supplier as s left join " . DB_PREFIX . "supplier_location as sl on sl.supplier_id=s.id ".$conditions." group by s.id order by s.id desc limit " . $limit;
        
        $total = $GLOBALS['db']->getOne($sql_count);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);

        $list = $GLOBALS['db']->getAll($sql);
        
        foreach ($list as $k => $v) {
            $list[$k]['detail_url'] = url("hiz", "supplier#supplier_detail", array(
                "data_id" => $v['id'],
            ));
            $list[$k]['location_url'] = url("hiz","location",array("data_id"=>$v['id']));
            $list[$k]['update_url'] = url("hiz","supplier#update",array("id"=>$v['id']));
            $list[$k]['money']=round($v['money'],2);
            $list[$k]['sale_money']=round($v['sale_money'],2);
            $list[$k]['refund_money']=round($v['refund_money'],2);
            $list[$k]['publish_verify_balance']=($v['publish_verify_balance']*100)."%";
        }
        
        /* 数据 */
	    $GLOBALS['tmpl']->assign("list", $list);
	    $GLOBALS['tmpl']->assign("publish_btn_url", url("hiz", "supplier#publish"));
	    $GLOBALS['tmpl']->assign("form_url", url("hiz", "supplier#no_online_index"));
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "supplier"));
	    $GLOBALS['tmpl']->assign("index_url", url("hiz", "supplier#index"));
	    $GLOBALS['tmpl']->assign("no_online_index_url", url("hiz", "supplier#no_online_index"));
        $GLOBALS['tmpl']->assign("add_supplier_url",url("hiz", "supplier#publish"));
        $GLOBALS['tmpl']->assign("add_location_url",url("hiz", "location#publish"));
        $GLOBALS['tmpl']->assign("page",$page);
	    $GLOBALS['tmpl']->assign("key",$key);
	    
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "商户列表");
        $GLOBALS['tmpl']->display("pages/supplier/index.html");
	}
	
	/**
	 * 输出：
	 * list=array(
	 *     name   商户名称
	 *     h_name   企业名称
	 *     account_mobile  联系电话
	 *     create_time   申请时间
	 *     status   状态
	 *     publish_detail_url  查看链接
	 *     is_publish = 0时，显示彻底删除 
	 * );
	 *   */
	public function publish_supplier(){
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	    
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    /* 分页 */
	    $page_size = 20;
	    $page = intval($_REQUEST['p']);
	    if ($page == 0)
	        $page = 1;
	    $limit = (($page - 1) * $page_size) . "," . $page_size;
	     
	    $sql_count = " select count(distinct(id)) from " . DB_PREFIX . "supplier_submit where agency_id=".$account_id;
	    $sql="select id,name,is_publish,h_name,account_mobile,create_time from " . DB_PREFIX . "supplier_submit where agency_id=".$account_id." order by id desc limit ".$limit;

	    $list=$GLOBALS['db']->getAll($sql);
	    
	    foreach($list as $t => $v){
	        $list[$t]['create_time']=to_date($v['create_time']); 
	        $list[$t]['publish_detail_url']=url("hiz","supplier#publish_detail",array("data_id"=>$v['id']));
	        if($v['is_publish']==0){
	            $list[$t]['status']="未审核";
	        }
	        else if($v['is_publish']==1){
	            $list[$t]['status']="同意入驻";
	        }
	        else {
	            $list[$t]['status']="拒绝申请";
	        }
	    }
	    
	    $total = $GLOBALS['db']->getOne($sql_count);
	    $page = new Page($total, $page_size); // 初始化分页对象
	    $p = $page->show();
	    
	    $GLOBALS['tmpl']->assign('pages', $p);
	    
	    $GLOBALS['tmpl']->assign("list", $list);
	    $GLOBALS['tmpl']->assign("add_supplier_url",url("hiz", "supplier#publish"));
	    $GLOBALS['tmpl']->assign("add_location_url",url("hiz", "location#publish"));
	    
	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("page_title", "商户审核列表");
	    $GLOBALS['tmpl']->display("pages/supplier/publish_supplier.html");
	}
	
	
	/**
	 * 输入：data_id 商家id
	 * 
	 * 输出：
	 * info=array(
	 *     name => 桥亭活鱼          商户名称
	 *     account_name => fanwe     账户
	 *     mobile =>  1612398201     手机号
	 *     preview => url    商家logo
	 *     allow_refund =>  1   是否支持退款
	 *     allow_refund =>  0  是否支持自动发布
	 *     publish_verify_balance  =>  10%     自动审核时的结算费用率
	 *     platform_status  =>  0   是否支持公众平台功能
	 *     is_store_payment =>  1  是否支持优惠买单
	 *     bank_user  =>  方维     银行卡户主
	 *     bank_name  =>  建行     开户行
	 *     bank_info  =>  345232314123423   银行卡号
	 *     h_name  =>  方维科技    公司名称
	 *     h_faren  =>  fanwe  公司法人
	 *     h_tel   =>   法人联系电弧
	 *     h_license =>  url   营业执照
	 *     h_other_license =>  url    其他资质
	 * );
	 * 
	 *   */
	public function supplier_detail(){
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	    $account_id = $account_info['id'];
	     
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    $supplier_id = intval($_REQUEST['data_id']);
	    
	    $sql="select * from " . DB_PREFIX . "supplier as s left join " . DB_PREFIX . "supplier_account as sa on s.id=sa.supplier_id where s.id=".$supplier_id." and s.agency_id=".$account_id." and sa.is_main=1";
	    $info=$GLOBALS['db']->getRow($sql);
	    
	    if(!$info){
	        showHizErr("商家不存在",0,url("hiz","supplier"));
	    }
	    
        $info['preview'] = get_spec_image($info['preview'], 100, 100,1);
        $info['publish_verify_balance']=($info['publish_verify_balance']*100)."%";
        $info['h_license'] = get_spec_image($info['h_license'], 100, 100,1);
        $info['h_other_license'] = get_spec_image($info['h_other_license'], 100, 100,1); 
	     
        $GLOBALS['tmpl']->assign("info",$info);
        $GLOBALS['tmpl']->display("pages/supplier/supplier_detail.html");
	}
	
	
	/**
	 * 输出：
	 * cate_list = array(
	 *     [1] => Array           一级分类
            (
                [id] => 32
                [name] => 下午时光
                [sub_type] => Array         二级分类
                    (
                        [0] => Array
                            (
                                [id] => 28
                                [name] => 甜点
                                [is_recommend] => 0
                                [sort] => 0
                            )
                    )
    
            )
	 * )
	 * json_cate_list   分类的json数据
	 * back_url         返回链接
	 *   */
	public function publish(){
	    /* 基本参数初始化 */
	    init_app_page();
	    $account_info = $GLOBALS['hiz_account_info'];
	 
	    $account_id = $account_info['id'];
	
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }

	    $cate_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_effect = 1 and is_delete = 0 order by sort desc");
	    
	    foreach ($cate_list as $t => $v){
	        $sub_sql="select ct.* from ".DB_PREFIX."deal_cate_type_link as cl left join ".DB_PREFIX."deal_cate_type as ct on ct.id=cl.deal_cate_type_id where cl.cate_id=".$v['id'];
	        $cate_list[$t]['sub_type']=$GLOBALS['db']->getAll($sub_sql);
	    }
	    $go_list_url=url("hiz", "supplier#publish_supplier");
	    
	    /* 输出数据 */
	    $GLOBALS['tmpl']->assign("cate_list",$cate_list);
	    $GLOBALS['tmpl']->assign("json_cate_list",json_encode($cate_list));
	    $GLOBALS['tmpl']->assign("back_url",url("hiz","supplier#publish_supplier"));
	    
	    /* 系统默认 */
	    $GLOBALS['tmpl']->assign("ajax_url", url("hiz", "supplier"));
	    $GLOBALS['tmpl']->assign("page_title", "商户资料编辑");
	    $GLOBALS['tmpl']->display("pages/supplier/publish.html");
	}
	
	
	/**
	 * 输入数据：
	 * name = 桥亭活鱼        商户名称
	 * account_name = fanwe   账户名称
	 * account_mobile = 14323454325   手机号码
	 * account_password = 123456    密码
	 * h_supplier_logo = 商家logo
	 * h_supplier_image = 门店图片
	 * deal_cate_id  = 商家一级 分类
	 * deal_cate_type_id = 商家二级分类
	 * open_time = 营业时间
	 * xpoint = 经度
	 * ypoint = 维度
	 * h_name = 企业名称
	 * h_faren = 企业法人
	 * h_tel = 法人联系电话
	 * h_license = 营业执照
	 * h_other_license = 其他资质 
	 * 
	 * 输出：
	 * status ：false/true  状态
	 * info ： 说明
	 * field ： 错误字段
	 * jump ： 跳转链接
	 *  */
	public function do_publish(){
	    $account_info = $GLOBALS['hiz_account_info'];
	    
	    $account_id = $account_info['id'];
	    
	    if(!$account_info){
	        showHizErr("请先登录",0,url("hiz","user#login"));
	    }
	    
	    $base_data = $_REQUEST;
	    
	    if(strim($base_data['name'])==""){
	        $data['status'] = false;
	        $data['info'] = "请输入商户名称";
	        $data['field'] = "name";
	        ajax_return($data);
	    }
	    if(strim($base_data['account_name'])==""){
	        $data['status'] = false;
	        $data['info'] = "请输入帐号";
	        $data['field'] = "account_name";
	        ajax_return($data);
	    }
	    
	    if($base_data['account_mobile']==""){
	        $data['status'] = false;
	        $data['info'] = "请输入手机号";
	        $data['field'] = "account_mobile";
	        ajax_return($data);
	    }
	    if(!check_mobile($base_data['account_mobile'])){
	        $data['status'] = false;
	        $data['info']	=	"手机号格式不正确";
	        $data['field'] = "account_mobile";
	        ajax_return($data);
	    }
	    
	    if($GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_submit where is_publish=0 and (account_mobile = '".$base_data['account_mobile']."' or account_name='".$base_data['account_name']."')")
	        || $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_account where is_delete=0 and (mobile = '".$base_data['account_mobile']."' or account_name='".$base_data['account_name']."')")
	    )
	    {
	        $data['status'] = false;
	        $data['info'] = "该帐号或手机已被注册或正在申请中";
	        $data['field'] = "account_name";
	        ajax_return($data);
	    }
	    
	    if(strim($base_data['account_password'])=='')
	    {
	        $data['status'] = false;
	        $data['info'] = "请输入密码";
	        $data['field'] = "account_password";
	        ajax_return($data);
	    }
	    
	    if($base_data['h_supplier_logo']==""){
	        $data['status'] = false;
	        $data['info'] = "请上传商家logo图片";
	        $data['field'] = "h_supplier_logo";
	        ajax_return($data);
	    }
	    if(intval($base_data['deal_cate_id'])==0){
	        $data['status'] = false;
	        $data['info'] = "请选择分类";
	        $data['field'] = "deal_cate_id";
	        ajax_return($data);
	    }
	    
	    if(strim($base_data['address'])==""){
	        $data['status'] = false;
	        $data['info'] = "请输入商家地图";
	        $data['field'] = "name";
	        ajax_return($data);
	    }
	    if($base_data['xpoint']=="" || $base_data['ypoint']==""){
	        $data['status'] = false;
	        $data['info'] = "请输入地图定位";
	        $data['field'] = "name";
	        ajax_return($data);
	    }
	    
	    if(strim($base_data['h_name'])=="")
	    {
	        $data['status'] = false;
	        $data['info']	=	"请输入企业名称";
	        $data['field'] = "h_name";
	        ajax_return($data);
	    }
	    if(strim($base_data['h_faren'])=="")
	    {
	        $data['status'] = false;
	        $data['info']	=	"请输入法人名称";
	        $data['field'] = "h_faren";
	        ajax_return($data);
	    }
	    if(strim($base_data['h_tel'])=="")
	    {
	        $data['status'] = false;
	        $data['info']	=	"请输入法人电话";
	        $data['field'] = "h_tel";
	        ajax_return($data);
	    }
	    if($base_data['h_license']=="")
	    {
	        $data['status'] = false;
	        $data['info']	=	"请上传营业执照";
	        $data['field'] = "h_license";
	        ajax_return($data);
	    }
	    
	    //生成商户提交数据
	    $ins_data['name'] = strim($base_data['name']);
	    $ins_data['cate_config'] = serialize(array('deal_cate_id'=>$base_data['deal_cate_id'],'deal_cate_type_id'=>$base_data['deal_cate_type_id']));
	    $ins_data['address'] = strim($base_data['address']);
	    $ins_data['open_time'] = strim($base_data['open_time']);
	    $ins_data['address'] = $base_data['address'];
	    $ins_data['xpoint'] = $base_data['xpoint'];
	    $ins_data['ypoint'] = $base_data['ypoint'];
	    $ins_data['location_id'] = 0;
	    
	    //查找所属城市代理商
        $ins_data['agency_id'] = $account_id;
        $ins_data['city_code'] = $account_info['city_code'];
        $ins_data['city_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_city where code=".$account_info['city_code']);
	    
	    $ins_data['account_name'] = strim($base_data['account_name']);
	    $ins_data['account_password'] = md5($base_data['account_password']);
	    $ins_data['account_mobile'] = $base_data['account_mobile'];
	    $ins_data['tel'] = $base_data['account_mobile'];
	    $ins_data['h_name'] = strim($base_data['h_name']);
	    $ins_data['h_faren'] = strim($base_data['h_faren']);
	    $ins_data['h_tel'] = strim($base_data['h_tel']);
	    
	    //图片
	    $ins_data['h_license'] = replace_domain_to_public(strim($base_data['h_license']));
	    $ins_data['h_other_license'] = replace_domain_to_public(strim($base_data['h_other_license']));
	    $ins_data['h_supplier_logo'] = replace_domain_to_public(strim($base_data['h_supplier_logo']));
	    $ins_data['h_supplier_image'] = replace_domain_to_public(strim($base_data['h_supplier_image']));

	    $ins_data['create_time'] = NOW_TIME;
	    
	    $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_submit",$ins_data,'INSERT');
	    $insert_id = $GLOBALS['db']->insert_id();
	    if($insert_id){
	        $data['status'] = true;
	        $data['info']	=	"申请成功，等待审核!";
	        $data['jump'] = url("hiz","supplier");
	        ajax_return($data);
	    }
	    else{
	        $data['status'] = false;
	        $data['info']	=	"商户申请提交失败，请重新填写提交";
	        $data['jump'] = url("hiz","supplier#publish");
	        ajax_return($data);
	    }
	}
	
    public function publish_detail(){
        init_app_page();
        $account_info = $GLOBALS['hiz_account_info'];
         
        $account_id = $account_info['id'];
         
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }
        
        $data_id=$_REQUEST['data_id'];
        
        $info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_submit where id=".$data_id." and agency_id=".$account_id);
        
        if(!$info){
            showHizErr("非法数据",0,url("hiz","supplier#publish_supplier"));
        }
        
        $info['h_supplier_logo']=get_spec_image($info['h_supplier_logo'], 100, 100,1);
        $info['h_supplier_image']=get_spec_image($info['h_supplier_image'], 100, 100,1);
        $info['h_license']=get_spec_image($info['h_license'], 100, 100,1);
        $info['h_other_license']=get_spec_image($info['h_other_license'], 100, 100,1);
        
        //分类
        $cate_list=unserialize($info['cate_config']);
        
        $cate=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id=".$cate_list['deal_cate_id']);
        $sub_cate=$GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_cate_type where id in (".implode(",", $cate_list['deal_cate_type_id']).")");
        $info['cate_name']=$cate;
        if($sub_cate){
            $new_sub=array();
            foreach ($sub_cate as $t => $v){
                $new_sub[]=$v['name'];
            }
            $sub_cate=implode("、", $new_sub);
            $info['cate_name'].=" - ".$sub_cate;
        }
        
        if($info['is_publish']==0){
            $info['status']="未审核";
        }
        elseif ($info['is_publish']==1){
            $info['status']="同意入驻";
        }
        else{
            $info['status']="拒绝申请";
        }
        
        $GLOBALS['tmpl']->assign("info",$info);
        $GLOBALS['tmpl']->assign("back_url",url("hiz","supplier#publish_supplier"));
        
        $GLOBALS['tmpl']->display("pages/supplier/publish_detail.html");
    }
    
    public function delete_publish(){
        $account_info = $GLOBALS['hiz_account_info'];
         
        $account_id = $account_info['id'];
         
        if(!$account_info){
            showHizErr("请先登录",0,url("hiz","user#login"));
        }
        
        $data_id=$_REQUEST['data_id'];
        
        $id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."supplier_submit where id=".$data_id." and agency_id=".$account_id." and is_publish=0");
        
        if(!$id){
            $data['status']=false;
            $data['info']="操作有误";
            ajax_return($data);
        }
        
        $GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_submit where id=".$id." and agency_id=".$account_id." and is_publish=0");
        
        if($GLOBALS['db']->affected_rows()){
            $data['status']=true;
            $data['info']="申请删除成功";
            ajax_return($data);
        }
        else{
            $data['status']=false;
            $data['info']="申请删除失败";
            ajax_return($data);
        }
    }
    
    
    /**
     * 获取子分类
     **/
    public function load_sub_cate(){
        $cate_id = intval($_REQUEST['cate_id']);
    
        $sub_cate_list = $GLOBALS['db']->getAll("select c.* from ".DB_PREFIX."deal_cate_type as c left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = c.id where l.cate_id = ".$cate_id);

        ajax_return($sub_cate_list);
    }
}
?>