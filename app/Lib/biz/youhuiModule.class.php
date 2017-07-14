<?php
/**
 * 商户中心优惠券管理
 * @author Administrator
 *
 */
require APP_ROOT_PATH . 'app/Lib/page.php';

class youhuiModule extends BizBaseModule
{

    function __construct()
    {
        parent::__construct();
        global_run();
        $this->check_auth();
    }

    public function index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        /* 获取参数 */
        
        /* 业务逻辑部分 */
        $conditions .= " where  y.supplier_id = ".$supplier_id; // 查询条件
        

        // 需要连表操作 只查询支持门店的
        $join = " left join ".DB_PREFIX."youhui_location_link as yl on yl.youhui_id = y.id ";
        $conditions .= " and (yl.location_id in(" . implode(",", $account_info['location_ids']) . ") or youhui_type=2 )";
        
        
        $sql_count = " select count(distinct(y.id)) from " . DB_PREFIX . "youhui as y ";
        $sql = " select distinct(y.id),y.name,y.begin_time,y.end_time,y.total_num,y.user_count,y.youhui_type,y.youhui_value,y.is_effect from " . DB_PREFIX . "youhui as y ";
        
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0)  $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        
        $total = $GLOBALS['db']->getOne($sql_count . $join . $conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);		
        $list = $GLOBALS['db']->getAll($sql . $join . $conditions . " order by y.id desc limit " . $limit);
        
        foreach ($list as $k => $v) {
            $list[$k]['begin_time'] = $v['begin_time'] != 0 ? to_date($v['begin_time']).'开始' : "";
            $list[$k]['end_time'] = $v['end_time'] != 0 ? to_date($v['end_time']).'截止' : "永久有效";
            $list[$k]['images'][0]['img'] = $v['icon'];
            $list[$k]['edit_url'] = url("biz", "youhui#edit", array("id" => $v['id'],"edit_type" =>1));
            $list[$k]['preview_url'] = url("index", "preview#youhui", array( "id" => $v['id'],"type" =>0));
        }
       
        /* 数据 */
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "youhui#publish"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "youhui#index"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "youhui"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "youhui#index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "youhui#no_online_index"));
        
        
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "优惠券项目管理");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }

    /**
     * 未发布的列表
     */
    public function no_online_index()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        
        /* 获取参数 */
	    if(isset($_REQUEST['filter_admin_check']) && $_REQUEST['filter_admin_check']!=''){
	        $filter_admin_check = intval($_REQUEST['filter_admin_check']);
	    }else{
	        $filter_admin_check = -1;
	    }      
        
        
        
        /* 业务逻辑部分 */
        $conditions .= " where 1= 1 "; // 查询条件
        
        if ($account_info['is_main'] == 1) { // 总管理员
            $conditions .= " and y.supplier_id = " . $supplier_id;
        } else { // 子账户操作
               // 只查询支持门店的
            $conditions .= " and y.account_id =" . $account_id;
        }
        
        
    	if ($filter_admin_check >= 0)  {
            $conditions .= " and admin_check_status = " . $filter_admin_check;
        }
        
        $sql_count = " select count(*) from " . DB_PREFIX . "youhui_biz_submit as y";
        $sql = " select y.id,y.name,y.begin_time,y.end_time,y.biz_apply_status,y.admin_check_status,y.image,y.icon,y.youhui_id from ".DB_PREFIX."youhui_biz_submit as y";
        
        /* 分页 */
        $page_size = 10;
        $page = intval($_REQUEST['p']);
        if ($page == 0) $page = 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $total = $GLOBALS['db']->getOne($sql_count . $conditions);
        $page = new Page($total, $page_size); // 初始化分页对象
        $p = $page->show();
        $GLOBALS['tmpl']->assign('pages', $p);

        $list = $GLOBALS['db']->getAll($sql . $conditions . " order by id desc limit " . $limit);
        
        foreach ($list as $k => $v) {
        	$list[$k]['begin_time'] = $v['begin_time'] != 0 ? to_date($v['begin_time']) : "不限";
            $list[$k]['end_time'] = $v['end_time'] != 0 ? to_date($v['end_time']) : "不限";
            $list[$k]['images'][] = $v['icon'];
            $list[$k]['edit_url'] = url("biz", "youhui#edit", array("id" => $v['id'],"edit_type" =>2));
            $list[$k]['preview_url'] = url("index", "preview#youhui", array( "id" => $v['id'],"type" =>1));
        }
        
        /* 数据 */
        $GLOBALS['tmpl']->assign("filter_admin_check", $filter_admin_check);
        $GLOBALS['tmpl']->assign("list", $list);
        $GLOBALS['tmpl']->assign("publish_btn_url", url("biz", "youhui#publish"));
        $GLOBALS['tmpl']->assign("form_url", url("biz", "youhui#no_online_index"));
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "youhui"));
        $GLOBALS['tmpl']->assign("index_url", url("biz", "youhui#index"));
        $GLOBALS['tmpl']->assign("no_online_index_url", url("biz", "youhui#no_online_index"));
        
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("page_title", "优惠券项目管理");
        $GLOBALS['tmpl']->display("pages/project/index.html");
    }

    /**
     * 优惠券添加
     */
    public function publish()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        
        // 支持门店
        $location_infos = $GLOBALS['db']->getAll("select id,name,xpoint,ypoint from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $GLOBALS['account_info']['location_ids']) . ")");
        
        $biz_root=SITE_DOMAIN.$_SERVER['PHP_SELF'];
        $GLOBALS['tmpl']->assign("biz_root",$biz_root);
        $app_root=SITE_DOMAIN.APP_ROOT;
        $GLOBALS['tmpl']->assign("app_root",$app_root);
        /* 数据 */
        $GLOBALS['tmpl']->assign("location_infos", $location_infos); // 支持门店 

        //是否开启自动审核
        //$GLOBALS['tmpl']->assign('allow_publish_verify',intval($GLOBALS['db']->getOne("select allow_publish_verify from ".DB_PREFIX."supplier where id=".$supplier_id)));
         
        //分类数据
        $cate_info=$GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "deal_cate where is_delete=0 and is_effect=1 group by sort");
        $GLOBALS['tmpl']->assign("cate_info", $cate_info);
        
        
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "youhui"));
        $GLOBALS['tmpl']->assign("page_title", "优惠券发布");
        $GLOBALS['tmpl']->display("pages/project/youhui_publish.html");
    }

    public function edit()
    {
        /* 基本参数初始化 */
        init_app_page();
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        $id = intval($_REQUEST['id']);
        
        /* 业务逻辑 */
        // 支持门店
        $location_infos = $GLOBALS['db']->getAll("select id,name,xpoint,ypoint from " . DB_PREFIX . "supplier_location where id in(" . implode(",", $GLOBALS['account_info']['location_ids']) . ")");
            
        $youhui_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "youhui where id=".$id." and supplier_id = ".$supplier_id);

        if (empty($youhui_info)) {
            showBizErr("数据不存在或没有操作权限！",0,url("biz","youhui#no_online_index"));
            exit();
        }
        // 支持门店 , 门店选中状态
        $curr_location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."youhui_location_link where  youhui_id = ".$id);//该优惠券门店
        foreach($curr_location_list as $k=>$v){
            $curr_locations[] = $v['location_id']; 
        }
        
        foreach ($location_infos as $k => $v) {
            if (in_array($v['id'], $curr_locations) ) {
                $location_infos[$k]['checked'] = 1;
            }
        }
        
        $go_list_url = url("biz","youhui#index");
        
        //分类数据
        $cate_info=$GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "deal_cate where is_delete=0 and is_effect=1 group by sort");
        foreach ($cate_info as $t => $v){
            if($v['id']==$youhui_info['deal_cate_id']){
                $cate_info[$t]['is_checked']=1;
            }else{
                $cate_info[$t]['is_checked']=0;
            }
        }
        
        $GLOBALS['tmpl']->assign("cate_info", $cate_info);
    
        
        // 时间格式化
        $youhui_info['begin_time'] = to_date($youhui_info['begin_time'], "Y-m-d H:i");
        $youhui_info['end_time'] = to_date($youhui_info['end_time'], "Y-m-d H:i");
        $youhui_info['use_begin_time'] = to_date($youhui_info['use_begin_time'], "Y-m-d H:i");
        $youhui_info['use_end_time'] = to_date($youhui_info['use_end_time'], "Y-m-d H:i");

        $GLOBALS['tmpl']->assign("location_infos", $location_infos); // 支持门店
        $GLOBALS['tmpl']->assign("youhui_info", $youhui_info); // 商品所有数据

        $GLOBALS['tmpl']->assign("go_list_url", $go_list_url); // 返回列表连接
        
        $biz_root=SITE_DOMAIN.$_SERVER['PHP_SELF'];
        $GLOBALS['tmpl']->assign("biz_root",$biz_root);
        $app_root=SITE_DOMAIN.APP_ROOT;
        $GLOBALS['tmpl']->assign("app_root",$app_root);
        /* 系统默认 */
        $GLOBALS['tmpl']->assign("ajax_url", url("biz", "youhui"));
        $GLOBALS['tmpl']->assign("page_title", "优惠券编辑");
        $GLOBALS['tmpl']->display("pages/project/youhui_edit.html");
    }

    public function del()
    {
        /* 基本参数初始化 */
        init_app_page();
        
        $id = intval($_REQUEST['id']);
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        /* 业务逻辑 */
        if ($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "youhui where id=" . $id . " and supplier_id =" . $supplier_id)) {
            // 存在切用户有权限删除
            $GLOBALS['db']->query("delete from " . DB_PREFIX . "youhui where id=" . $id . " and supplier_id =" . $supplier_id);
            $data['status'] = 1;
            $data['info'] = "删除成功";
        } else {
            $data['status'] = 0;
            $data['info'] = "数据不存在或没有管理权限";
        }
        ajax_return($data);
    }

    /**
     * 保存团购产品数据
     */
    public function do_save_publish()
    {
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        //print_r($_REQUEST);exit;
        $edit_type = intval($_REQUEST['edit_type']);
        $id = intval($_REQUEST['id']);
        
        // 白名单过滤
        require_once(APP_ROOT_PATH . 'system/model/no_xss.php');

        if (strim($_REQUEST['name']) == ''){
            $result['status'] = 0;
            $result['info'] = '请输入优惠券名称';
            ajax_return($result);
        }
        
        if(intval($_REQUEST['deal_cate_id'])==0){
            $result['status'] = 0;
            $result['info'] = '请选择分类';
            ajax_return($result);
        }
        
        if (intval($_REQUEST['youhui_value'])<=0 || intval($_REQUEST['youhui_value'])>999){
            $result['status'] = 0;
            $result['info'] = '优惠券面额必须为正整数';
            ajax_return($result);
        }
        
        if (intval($_REQUEST['youhui_type']) == 1 && !$_REQUEST['location_id']){
            $result['status'] = 0;
            $result['info'] = '实体券必须选择门店';
            ajax_return($result);
        }
        
        if($_REQUEST['total_num'] && intval($_REQUEST['total_num'])<0){
            $result['status'] = 0;
            $result['info'] = '发放总量不能小于0';
            ajax_return($result);
        }
        if($_REQUEST['user_limit'] && intval($_REQUEST['user_limit'])<0){
            $result['status'] = 0;
            $result['info'] = '每人最多领取不能小于0';
            ajax_return($result);
        }
        if($_REQUEST['user_everyday_limit'] && intval($_REQUEST['user_everyday_limit'])<0){
            $result['status'] = 0;
            $result['info'] = '每天最多领取不能小于0';
            ajax_return($result);
        }
        if($_REQUEST['start_use_price'] && intval($_REQUEST['start_use_price'])<0){
            $result['status'] = 0;
            $result['info'] = '使用限制不能小于0';
            ajax_return($result);
        }
        if($_REQUEST['expire_day'] && intval($_REQUEST['expire_day'])<0){
            $result['status'] = 0;
            $result['info'] = '有效天数不能小于0';
            ajax_return($result);
        }
        
        $data['begin_time'] = strim($_REQUEST['begin_time']) == '' ? 0 : to_timespan($_REQUEST['begin_time'], "Y-m-d H:i");
        $data['end_time'] = strim($_REQUEST['end_time']) == '' ? 0 : to_timespan($_REQUEST['end_time'], "Y-m-d H:i");
        $data['use_begin_time'] = strim($_REQUEST['use_begin_time']) == '' ? 0 : to_timespan($_REQUEST['use_begin_time'], "Y-m-d H:i");
        $data['use_end_time'] = strim($_REQUEST['use_end_time']) == '' ? 0 : to_timespan($_REQUEST['use_end_time'], "Y-m-d H:i");
        
        if($data['end_time']>0 && $data['end_time']<NOW_TIME){
            $result['status'] = 0;
            $result['info'] = '优惠券发放结束时间不能小于当前时间';
            ajax_return($result);
        }
        
        if($data['end_time']>0 && $data['begin_time']>0 && $data['end_time']<=$data['begin_time']){
            $result['status'] = 0;
            $result['info'] = '优惠券发放开始时间不能大于发放结束时间';
            ajax_return($result);
        }
        
        if($data['use_end_time']>0 && $data['use_end_time']<NOW_TIME){
            $result['status'] = 0;
            $result['info'] = '优惠券有效期结束时间不能小于当前时间';
            ajax_return($result);
        }
        
        if($data['use_end_time']>0 && $data['use_begin_time']>0 && $data['use_end_time']<=$data['use_begin_time']){
            $result['status'] = 0;
            $result['info'] = '优惠券有效期开始时间不能大于有效期结束时间';
            ajax_return($result);
        }
        
        $data['supplier_id'] = $supplier_id; // 所属商户
        $data['account_id'] = $account_id;
        $data['name'] = strim($_REQUEST['name']); // 优惠券名称
        
		$data['expire_day'] = intval($_REQUEST['expire_day']); // 有效天数
		$data['total_num'] = intval($_REQUEST['total_num']); // 总条数
		$data['user_limit'] = intval($_REQUEST['user_limit']); // 下载限制		
        $data['city_id'] = $GLOBALS['db']->getOne("select city_id from ".DB_PREFIX."supplier where id=".$supplier_id); // 城市
        $data['youhui_type'] = intval($_REQUEST['youhui_type']); // 优惠券类型
        $data['youhui_value'] = intval($_REQUEST['youhui_value']); // 面额 
        $data['create_time'] = NOW_TIME;  
        $data['is_sms'] = 1;
        $data['pub_by']=2;
        $data['publish_wait']=1;
        $data['valid_type']=intval($_REQUEST['valid_type']);
        $data['user_everyday_limit']=intval($_REQUEST['user_everyday_limit']);
        $data['start_use_price']=intval($_REQUEST['start_use_price']);
        $data['deal_cate_id']=intval($_REQUEST['deal_cate_id']);
        
        $location_id = $_REQUEST['location_id']; // 支持门店
        
        if ($id > 0) {

            $GLOBALS['db']->autoExecute(DB_PREFIX."youhui", $data, "UPDATE", " id=".$id . " and supplier_id =" . $supplier_id);
            
            $GLOBALS['db']->query("delete from  ".DB_PREFIX."youhui_location_link where youhui_id=".$id);
            
            foreach ($location_id as $k=>$v){
                $link_location['location_id']=intval($v);
                $link_location['youhui_id']=$id;
                $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_location_link", $link_location );
            
                recount_supplier_data_count(intval($v),"youhui");
            }
            
            $result['status'] = 1;
            $result['info'] = "修改成功";
            $result['jump'] = url("biz", "youhui");
            
        } else {
            $data['is_effect'] =1;
            
            $list = $GLOBALS['db']->autoExecute(DB_PREFIX."youhui", $data);
            if ($list && $GLOBALS['db']->error()=='') {
                $youhui_id=$GLOBALS['db']->insert_id();
                
                foreach ($location_id as $k=>$v){
                    $link_location['location_id']=intval($v);
                    $link_location['youhui_id']=$youhui_id;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_location_link", $link_location );
                    
                    recount_supplier_data_count(intval($v),"youhui");
                }
                
                $result['status'] = 1;
                $result['info'] = "优惠券发布成功";
                $result['jump'] = url("biz", "youhui");
            }
        }
        
        
        
        /* //自动审核
 		 $allow_publish_verify=$GLOBALS['db']->getOne("select allow_publish_verify from ".DB_PREFIX."supplier where id=".$supplier_id);      
        if($allow_publish_verify==1){
        	 require_once(APP_ROOT_PATH . 'system/model/youhui.php');
        	 if(youhui_check_biz_submit($youhui_submit_id,$supplier_id)){
        	 	$result['info'] = "发布成功";
        	 	$result['jump'] = url("biz", "youhui");
        	 }
        } */
        
        
        
 		 ajax_return($result);
    }
    
    /**
     * 下架操作
     */
    public function down_line(){
        $account_info = $GLOBALS['account_info'];
        $account_id = $account_info['id'];
        $supplier_id = $account_info['supplier_id'];
        //是否开启自动审核
        $supplier_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id=".$supplier_id);
        if($supplier_data['allow_publish_verify']){
            $allow_publish_verify = $supplier_data['allow_publish_verify'];
            $publish_verify_balance = $supplier_data['publish_verify_balance'];
            require_once(APP_ROOT_PATH.'system/model/youhui.php');
        }
        $id = intval($_REQUEST['id']);
        
        if($id>0){
            //商户提交数据
            $youhui_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_biz_submit where youhui_id =".$id." and supplier_id=".$supplier_id);
            //真实团购数据
            $youhui_info = $GLOBALS['db']->getRow("select y.* from ".DB_PREFIX."youhui as y left join " . DB_PREFIX . "youhui_location_link as  yl on yl.youhui_id = y.id where y.id=".$id." and yl.location_id in(".implode(",", $GLOBALS['account_info']['location_ids']).")");
            if($youhui_info){
                //数据导入 deal_submit表
                $data = array();      
                $data['admin_check_status'] = 0;
                $data['biz_apply_status'] = 3;
                $data['supplier_id'] = $supplier_id;
                $data['account_id'] = $account_id;

                
                if($youhui_submit_info){ //存在数据
                    if($allow_publish_verify){ //自动审核
                        if($youhui_submit_info['biz_apply_status']!=3){ //更新状态
                            $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_biz_submit",$data,"UPDATE","id=".$youhui_submit_info['id']);
                        }
                        youhui_auto_downline($youhui_submit_info['id']);
                        $result['status'] = 1;
                        $result['info'] = "下架申请成功";
                    }else{
                        if($youhui_submit_info['biz_apply_status']!=3){ //更新状态
                            $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_biz_submit",$data,"UPDATE","id=".$youhui_submit_info['id']);
                            $result['status'] = 1;
                            $result['info'] = "下架申请成功等待管理员审核";
                        }elseif($youhui_submit_info['biz_apply_status']==3){
                            $result['status'] = 0;
                            $result['info'] = "下架待审核中，请勿重复申请";
                        }
                    }
                    
                }else{ //增加新数据

                    $data['youhui_id'] = $youhui_info['id'];
                    $data['name'] = $youhui_info['name'];
                    $data['deal_cate_id'] = $youhui_info['deal_cate_id'];
                    $data['city_id'] = $youhui_info['city_id'];
                    $data['icon'] = $youhui_info['icon'];
                    $data['create_time'] = $youhui_info['create_time'];

                    $GLOBALS['db']->autoExecute(DB_PREFIX."youhui_biz_submit",$data);
                    $id = $GLOBALS['db']->insert_id();
                    if($allow_publish_verify){ //自动审核
                        youhui_auto_downline($id);
                        $result['status'] = 1;
                        $result['info'] = "下架申请成功";
                    }else{
                        $result['status'] = 1;
                        $result['info'] = "下架申请成功等待管理员审核";
                    }
                  
                }
                
            }else{
                $result['status'] = 0;
                $result['info'] = "数据不存在或权限不足";
                
            }
        }else{
            $result['status'] = 0;
            $result['info'] = "请正确提交数据";
        }
        ajax_return($result);
    }

     /**
     * 加载子分类
     */
    public function load_sub_cate(){
        $cate_id = intval($_REQUEST['cate_id']);
        $edit_type = intval($_REQUEST['edit_type']);
        $id = intval($_REQUEST['id']);
    
        $sub_cate_list = $GLOBALS['db']->getAll("select c.* from ".DB_PREFIX."deal_cate_type as c left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = c.id where l.cate_id = ".$cate_id);
        if($edit_type == 1){ //管理员添加数据
            $sub_cate_arr_data = $GLOBALS['db']->getAll("select deal_cate_type_id from ".DB_PREFIX."deal_cate_type_youhui_link where youhui_id = ".$id);
            foreach ($sub_cate_arr_data as $k=>$v){
                $sub_cate_arr[] = $v['deal_cate_type_id'];
            }
    
        }elseif ($edit_type == 2){//商户提交数据
            $sub_cate_arr = unserialize($GLOBALS['db']->getOne("select cache_deal_cate_type_youhui_link from ".DB_PREFIX."youhui_biz_submit where id=".$id));  //序列化的字段  
        }
        //处理选择状态
        foreach ($sub_cate_list as $k=>$v){
            if(in_array($v['id'], $sub_cate_arr)){
                $sub_cate_list[$k]['checked'] =1 ;
            }
        }
    
    
        $html = '';
        if($sub_cate_list){
            $result['status'] = 1;
            foreach($sub_cate_list as $k=>$v){
                if($v['checked']){
                    $html.='<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_cate_type_id[]" value="'.$v['id'].'" checked="checked"/>'.$v['name'].'</label>';
                }else{
                    $html.='<label class="ui-checkbox" rel="common_cbo"><input type="checkbox" name="deal_cate_type_id[]" value="'.$v['id'].'" />'.$v['name'].'</label>';
                }
            }
    
        }else
            $result['status'] = 0;
    
        $result['html'] = $html;
        ajax_return($result);
    }
    
    public function effect_change(){
        
        /* 基本参数初始化 */
        init_app_page();
        
        $id = intval($_REQUEST['id']);
        $account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        $account_id = $account_info['id'];
        
        $info=$GLOBALS['db']->getRow("select id,is_effect from " . DB_PREFIX . "youhui where id=" . $id . " and supplier_id =" . $supplier_id);
        
        if($info){
            if($info['is_effect']==1){
                $GLOBALS['db']->query("update " . DB_PREFIX . "youhui set is_effect=0 where id=".$id);
                $is_effect=0;
            }else{
                $GLOBALS['db']->query("update " . DB_PREFIX . "youhui set is_effect=1 where id=".$id);
                $is_effect=1;
            }
            
            if($GLOBALS['db']->affected_rows()){
                $data['status'] = 1;
                $data['is_effect'] = $is_effect;
                $data['info'] = "状态修改成功";
            }
            else{
                $data['status'] = 0;
                $data['info'] = "状态修改失败";
            }
        }
        else{
            $data['status'] = 0;
            $data['info'] = "数据不存在或没有管理权限";
        }
        
        ajax_return($data);
    }
    
}

?>