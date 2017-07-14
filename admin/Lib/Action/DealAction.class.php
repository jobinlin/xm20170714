<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DealAction extends CommonAction{
	public $page = 1;
	
	private $RELATE_GOODS_NUM = 6;//可以关联商品的个数
	private $img_info = '建议尺寸：640 x 640 像素，最多可上传8张图片。首张为列表缩列图，您可以拖拽图片调整图片顺序。'; //图片插件的提示

	public function __construct(){
		parent::__construct();
		
		if(isset($_REQUEST['page'])){
			$this->page = max(1,(int)$_REQUEST['page']);
		}
		if(isset($_REQUEST['isajax'])){
			$this->isajax = (int)$_REQUEST['isajax'] > 0 ? 1 : 0;
		}
		if(isset($_REQUEST['page'])){
			$this->page = max(1,(int)$_REQUEST['page']);
		}

		$this->assign('img_info',$this->img_info);
		$this->assign('pager_num_now',$this->page);
	}
	
	public function index()
	{
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		//开始加载搜索条件
		if(intval($_REQUEST['id'])>0)
		$map['id'] = intval($_REQUEST['id']);
		$map['is_delete'] = 0;
		if(strim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.strim($_REQUEST['name']).'%');			
		}

		if(intval($_REQUEST['city_id'])>0)
		{
			require_once(APP_ROOT_PATH."system/utils/child.php");
			$child = new Child("deal_city");
			$city_ids = $child->getChildIds(intval($_REQUEST['city_id']));
			$city_ids[] = intval($_REQUEST['city_id']);
			$map['city_id'] = array("in",$city_ids);
		}
        //商品状态:出售中0，已售罄1，仓库中2
         //商品状态:出售中0，已售罄1，仓库中2
        $status=intval($_REQUEST['status']);
        if($status==0){
            $map['is_effect']=1;
            $map['begin_time']=array("elt",NOW_TIME);
            $map['end_time']=array(array("egt",NOW_TIME),array("eq",0),"or");
            $map['max_bought']=array(array('eq',-1),array('gt',0),'or');
        }else if($status==1){
            $map['is_effect']=1;
            $map['begin_time']=array("elt",NOW_TIME);
            $map['end_time']=array(array("egt",NOW_TIME),array("eq",0),"or");
            $map['max_bought']=array("eq",0);
        }else if($status==2){
            $map['_string']=" is_effect=0 or (is_effect=1 and ((begin_time>".NOW_TIME." and begin_time>0) or (end_time<".NOW_TIME." and end_time>0)))";
        }
        $this->assign("status",$status);

		
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once(APP_ROOT_PATH."system/utils/child.php");
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		
		
		if(strim($_REQUEST['supplier_name'])!='')
		{
			if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier"))<50000)
			$sql  ="select group_concat(id) from ".DB_PREFIX."supplier where name like '%".strim($_REQUEST['supplier_name'])."%'";
			else 
			{
				$kws_div = div_str(trim($_REQUEST['supplier_name']));
				foreach($kws_div as $k=>$item)
				{
					$kw[$k] = str_to_unicode_string($item);
				}
				$kw_unicode = implode(" ",$kw);
				$sql = "select group_concat(id) from ".DB_PREFIX."supplier where (match(name_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			}
			$ids = $GLOBALS['db']->getOne($sql);
			$map['supplier_id'] = array("in",$ids);
		}
		$map['publish_wait'] = 0;
		$map['is_shop'] = 0;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	
	public function trash()
	{
		$condition['is_delete'] = 1;
        //自营商城0，自营积分1，商户商城商品2，商户团购商品3
        $type=intval($_REQUEST['type']);
        if($type==0){
            $condition['supplier_id']=0;
            $condition['buy_type']=0;
        }else if($type==1){
            $condition['supplier_id']=0;
            $condition['buy_type']=1;
        }else if($type==2){
            $condition['supplier_id']=array("gt",0);
            $condition['is_shop'] = 1;
        }else if($type==3){
            $condition['supplier_id']=array("gt",0);
            $condition['is_shop'] = 0;
        }
        $this->assign("type",$type);
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		$this->assign("new_sort", M("Deal")->where("is_delete=0")->max("sort")+1);
		
		$shop_cate_tree = M("ShopCate")->where('is_delete = 0')->findAll();
		
		$shop_cate_tree = D("ShopCate")->toFormatTree($shop_cate_tree,'name');
		$this->assign("shop_cate_tree",$shop_cate_tree);
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
		$this->assign("goods_type_list",$goods_type_list);
		
		$weight_list = M("WeightUnit")->findAll();
		$this->assign("weight_list",$weight_list);
		
		$brand_list = M("Brand")->findAll();
		$this->assign("brand_list",$brand_list);	
		$this->assign("img_index",0);
		//输出配送方式列表
		$delivery_list = M("Delivery")->where("is_effect=1")->findAll();
		$this->assign("delivery_list",$delivery_list);
		
		$user_group = M("UserGroup")->order("score asc")->findAll();
		$this->assign("user_group",json_encode($user_group));
		
		//输出支付方式
		$payment_list = M("Payment")->where("is_effect=1")->findAll();
		$this->assign("payment_list",$payment_list);
		
		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',0);
		$this->assign("tc_mobile_moban",$this->load_tc_page("tc_mobile"));
		$this->assign("tc_pc_moban",$this->load_tc_page("tc_pc"));

		$this->assign("deal_notes_html",file_get_contents(APP_ROOT_PATH."admin/edit_tpl/deal_notes.html"));
		$this->display();
	}
	private function load_tc_page($file)
	{
		$directory = APP_ROOT_PATH."mapi/mobile_tc/".$file;
		$files = get_all_files($directory);
		$tmpl_files = array();
		foreach($files as $item)
		{
			if(substr($item,-5)==".html")
			{
				$item = explode($directory,$item);
				$item = $item[1];
				if(substr($item,0,4)!="inc/")
					$tmpl_files[] = $item;
			}
		}
		return $tmpl_files;
	}
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//对于商户请求操作
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type']);
        
		if($id>0 && $edit_type==2){//商户申请新增团购
		    unset($_REQUEST['id']);
		}
		//开始验证有效性
		
		if(empty($data['img'])){
		    $this->error("必须添加团购商品图片");
		}
		
		if(intval($_REQUEST['continue_add'])==1){
		    $this->assign("jumpUrl",u(MODULE_NAME."/add"));
		}else{
		    $this->assign("jumpUrl",u(MODULE_NAME."/index"));
		}
		
		if($data['current_price']<0)
		{
		    $this->error("销售价不能为负数");
		}
			
		if($data['origin_price']<0)
		{
		    $this->error("原价不能为负数");
		}
		if($data['balance_price']<0)
		{
		    $this->error("结算价不能为负数");
		}
		
		if($data['balance_price'] > $data['current_price'])
		{
		    $this->error("结算价不能高于销售价");
		}
		
		if(intval($data['return_score'])<0)
		{
			$this->error("积分返还不能为负数");
		}
		if(floatval($data['return_money'])<0)
		{
			$this->error("现金返还不能为负数");
		}
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}	
		if($_REQUEST['cate_id']=='' && $_REQUEST['second_cate_id']=='')
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}
		if(intval($data['supplier_id'])==0)
		{
		    $this->error("请选择商户");
		}
		elseif (empty($_REQUEST['location_id'])){
		    $this->error("必须选择一家门店");
		}
		$city_info = M("DealCity")->where("id=".intval($data['city_id']))->find();
		if($data['min_bought']<0)
		{
			$this->error(L("DEAL_MIN_BOUGHT_ERROR_TIP"));
		}
		if($data['user_min_bought']<0)
		{
			$this->error(L("DEAL_USER_MIN_BOUGHT_ERROR_TIP"));
		}		
		if($data['user_max_bought']<0)
		{
			$this->error(L("DEAL_USER_MAX_BOUGHT_ERROR_TIP"));
		}
		if($data['user_max_bought']<$data['user_min_bought']&&$data['user_max_bought']>0)
		{
			$this->error(L("DEAL_USER_MAX_MIN_BOUGHT_ERROR_TIP"));
		}
		
		if($data['max_bought'] < 0){
		    $this->error(L("请输入有效库存"));
		}
		
		if($_REQUEST['deal_attr']){
		    foreach($_REQUEST['deal_attr'] as $k=>$deal_attr){
		        foreach($deal_attr as $kk=>$vv){
		            if(strim($vv)==''){
		                $this->error(L("请填写商品属性"));
		                break 2;
		            }
		        }
		    }
		}
		
		
		// 更新数据


		if(intval($_REQUEST['is_allow_fx'])==0){
		    $_REQUEST['is_fx']=0;
		}else{
		    $_REQUEST['fx_salary_type']=$_REQUEST['fx_salary_type'];
		    $_REQUEST['fx_salary']=$_REQUEST['fx_salary'];
		}
		
		$_REQUEST['allow_user_discount'] = intval($_REQUEST['allow_user_discount']);
		$_REQUEST['is_pick'] = intval($_REQUEST['is_pick']);
		$_REQUEST['is_referral'] = intval($_REQUEST['is_referral']);
		$_REQUEST['is_allow_fx'] = intval($_REQUEST['is_allow_fx']);
		$_REQUEST['buyin_app'] = intval($_REQUEST['buyin_app']);
		$_REQUEST['any_refund'] = intval($_REQUEST['any_refund']);
		$_REQUEST['is_recommend'] = intval($_REQUEST['is_recommend']);
		$_REQUEST['forbid_sms'] = intval($_REQUEST['forbid_sms']);
		$_REQUEST['notice'] = intval($_REQUEST['notice']);
		$_REQUEST['expire_refund'] = intval($_REQUEST['expire_refund']);
		$_REQUEST['is_lottery'] = intval($_REQUEST['is_lottery']);
        if($_REQUEST['second_cate_id']!=''){
            $_REQUEST['cate_id']=$this->tuan_cate_pid($_REQUEST['cate_id'],$_REQUEST['second_cate_id']);
        }
		if($_REQUEST['is_lottery']==1){  //0元抽奖
		    $_REQUEST['deal_tag'][] = 0;
		}
		if($_REQUEST['any_refund']==1){  //支持随时退
		    $_REQUEST['deal_tag'][] = 6;
		}
		if($_REQUEST['expire_refund']==1){//支持过期退
		    $_REQUEST['deal_tag'][] = 5;
		}
		
		require_once APP_ROOT_PATH."/system/model/DealObject.php";
		$deal_object = new DealObject();
		
		$type_string = 'tuan' ;
		
		$deal_object->setParamet($_REQUEST, $type_string);
		$result = $deal_object->save($_REQUEST);
		
		// 更新数据
		if ($result['status']==1) {
			if($id>0 && $edit_type == 2){ //商户提交审核
				//同步商户数据表
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("deal_id"=>$result['id'],"admin_check_status"=>1,"deal_submit_memo"=>$_REQUEST['deal_submit_memo']),"UPDATE","id=".$id);
			}		
			save_log($_REQUEST['name'].L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($_REQUEST['name'].L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function edit() {
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
		$vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
		$vo['coupon_begin_time'] = $vo['coupon_begin_time']!=0?to_date($vo['coupon_begin_time']):'';
		$vo['coupon_end_time'] = $vo['coupon_end_time']!=0?to_date($vo['coupon_end_time']):'';
	    
	    $supplier_info = M("Supplier")->where("id=".$vo['supplier_id'])->find();
	    $this->assign("supplier_info",$supplier_info);
	    
	    if( $vo['publish_verify_balance'] == 0){
	        $vo['publish_verify_balance'] = $supplier_info['publish_verify_balance'];
	    }
	    $vo['publish_verify_balance'] =  $vo['publish_verify_balance'] *100;

        $deal_fx_salary = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_fx_salary where deal_id=".$id);
        if($vo['is_fx'] > 0 && $deal_fx_salary){
            $vo['fx_salary_type'] = $deal_fx_salary[0]['fx_salary_type'];
            foreach($deal_fx_salary as $k=>$v){
                if($vo['fx_salary_type'] == 1){
                    $deal_fx_salary[$k]['fx_salary'] =$v['fx_salary'] * 100;
                }
            }
        }
    
        $deal_fx_salary = data_format_idkey($deal_fx_salary,$key='fx_level');
	    
	    $this->assign ( 'fx_salary', $deal_fx_salary );
		$this->assign ( 'vo', $vo );
		
		
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$this->assign("cate_tree",$cate_tree);
		
		$user_group = M("UserGroup")->order("score asc")->findAll();
		$this->assign("user_group",json_encode($user_group));

		$cate1=M("DealCate");
		if(strim($vo['cate_id'])){
			$first_cate_data=$cate1->where('id in ('.$vo['cate_id'].')')->findAll();
		}else{
			$first_cate_data=array();
		}
		
        $sql = "select dct.* , dc.name as first_cate,dc.id as pid from ".DB_PREFIX."deal_cate_type_deal_link as dctdl left join "
        .DB_PREFIX."deal_cate_type as dct on dctdl.deal_cate_type_id=dct.id left join ".DB_PREFIX."deal_cate_type_link as dctl
        on dct.id=dctl.deal_cate_type_id left join ".DB_PREFIX."deal_cate as dc on dctl.cate_id=dc.id where dctdl.deal_id = ".$id." and dctdl.deal_cate_type_id > 0";
		$second_cate_data = $GLOBALS['db']->getAll($sql);

		$second_cate_id_arr = array();
		foreach($second_cate_data as $k=>$v){
		    $second_cate_id_arr[] = $v['id'];
		}
		$second_cate_id = implode(',',$second_cate_id_arr);
		$this->assign("second_cate_id",$second_cate_id);	
		$shop_cate = array_merge($first_cate_data,$second_cate_data);


        $this->assign("shop_cate",$shop_cate);
		$brand_list = M("Brand")->findAll();
		$this->assign("brand_list",$brand_list);	
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		

		
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
		$this->assign("goods_type_list",$goods_type_list);
		
		//输出图片集
		$img_list = M("DealGallery")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
		$imgs = array();
		foreach($img_list as $k=>$v)
		{
			$imgs[$v['sort']] = $v['img']; 
		}
		$this->assign("img_list",$imgs);
		$this->assign("img_list",$imgs);
		$this->assign("img_index",count($imgs));
		
		$weight_list = M("WeightUnit")->findAll();
		$this->assign("weight_list",$weight_list);
		
		
		//输出配送方式列表
		$delivery_list = M("Delivery")->where("is_effect=1")->findAll();
		foreach($delivery_list as $k=>$v)
		{
			$delivery_list[$k]['free_count'] = M("FreeDelivery")->where("deal_id=".$vo['id']." and delivery_id = ".$v['id'])->getField("free_count");			
			$delivery_list[$k]['checked'] = M("DealDelivery")->where("deal_id=".$vo['id']." and delivery_id = ".$v['id'])->count();	
		}
		$this->assign("delivery_list",$delivery_list);
		
		//输出支付方式
		$payment_list = M("Payment")->where("is_effect=1")->findAll();
		foreach($payment_list as $k=>$v)
		{
			$payment_list[$k]['checked'] = M("DealPayment")->where("deal_id=".$vo['id']." and payment_id = ".$v['id'])->count();			
		}
		$this->assign("payment_list",$payment_list);
		
		
		//输出规格库存的配置

		$attr_json_data = $this->get_attr_json_data($vo['id']);
		$attr_cfg_json = $attr_json_data['attr_cfg_json'];
		$attr_stock_json = $attr_json_data['attr_stock_json'];
		$this->assign("attr_cfg_json",$attr_cfg_json);	
		$this->assign("attr_stock_json",$attr_stock_json);
			
		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',0);
		//关联商品
		$tempInfo = M('Relate_goods')->where('good_id='.$id.' and is_shop=0')->find();
		if( $tempInfo ){
			$relate_goods = array();
			$tmpList = M('Deal')->where('id in ('.$tempInfo['relate_ids'].')')->field('id,name,img')->select();
			foreach($tmpList as $item){
				$durl = url("index","preview#deal",array('id'=>$item['id'],'type'=>0));
				$relate_goods[] = array(
						'id'		=>	$item['id'],
						'name'		=>	$item['name'],
						'url'		=>	$durl,
						'share_url'	=>	SITE_DOMAIN.$durl,
						'img'		=>	$item['img']
				);
			}

			$this->assign("relate_goods",$relate_goods);
		}
		$this->assign("tc_mobile_moban",$this->load_tc_page("tc_mobile"));
		$this->assign("tc_pc_moban",$this->load_tc_page("tc_pc"));
		$this->display ();
	}
	
	/**
	 * 商户提交数据审核编辑
	 */
	public function biz_apply_edit(){
	    $id = intval($_REQUEST['id']);
	    $condition['is_delete'] = 0;
	    $condition['id'] = $id;
	    $vo = M("DealSubmit")->where($condition)->find();
	    $vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
	    $vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
	    $vo['coupon_begin_time'] = $vo['coupon_begin_time']!=0?to_date($vo['coupon_begin_time']):'';
	    $vo['coupon_end_time'] = $vo['coupon_end_time']!=0?to_date($vo['coupon_end_time']):'';
	    $vo['publish_verify_balance']=$GLOBALS['db']->getOne("select publish_verify_balance from ".DB_PREFIX ."supplier where id =".$vo['supplier_id'])*100;
	    $this->assign ( 'vo', $vo );
	    
		$this->assign("new_sort", M("Deal")->where("is_delete=0")->max("sort")+1);
	    $cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
	    $cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
	    $this->assign("cate_tree",$cate_tree);
	    
	    $user_group = M("UserGroup")->order("score asc")->findAll();
	    $this->assign("user_group",json_encode($user_group));

	    //选中门店
	    $select_location = implode(",", unserialize($vo['cache_location_id']));
		//分类
	    $cate1=M("DealCate");
	    $first_cate_data=$cate1->where('id in ('.$vo['cate_id'].')')->findAll();
	    $sql = " SELECT dct.*,dc. NAME AS first_cate,dc. id AS pid FROM fanwe_deal_cate_type AS dct ".
			   " LEFT JOIN ".DB_PREFIX."deal_cate_type_link AS dctl ON dct.id = dctl.deal_cate_type_id ".
			   " LEFT JOIN ".DB_PREFIX."deal_cate AS dc ON dctl.cate_id = dc.id ".
			   " WHERE dct.id in (".$vo['cache_deal_cate_type_id'].")";
	    $second_cate_data = $GLOBALS['db']->getAll($sql);
	    
	    $brand_list = M("Brand")->findAll();
	    $this->assign("brand_list",$brand_list);
	    $second_cate_id_arr = array();
	    foreach($second_cate_data as $k=>$v){
	    	$second_cate_id_arr[] = $v['id'];
	    }
	    $second_cate_id = implode(',',$second_cate_id_arr);
	    $this->assign("second_cate_id",$second_cate_id);
	    if($second_cate_data&&$first_cate_data){
	    	$shop_cate = array_merge($first_cate_data,$second_cate_data);
	    }elseif($second_cate_data){
	    	$shop_cate=$second_cate_data;
	    }elseif($first_cate_data){
	    	$shop_cate=$first_cate_data;
	    }
	    
	    $this->assign("shop_cate",$shop_cate);
	    //分类end
	    
	    
	    
	    //输出团购城市
	    $city_list = M("DealCity")->where('is_delete = 0')->findAll();
	    $city_list = D("DealCity")->toFormatTree($city_list,'name');
	    $this->assign("city_list",$city_list);
	    
	    $supplier_info = M("Supplier")->where("id=".$vo['supplier_id'])->find();
	    $this->assign("supplier_info",$supplier_info);
	    
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
	    $this->assign("goods_type_list",$goods_type_list);
	    
	    //输出图片集
	    //$img_list = M("DealGallery")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
	    $img_list = unserialize($vo['cache_focus_imgs']);
	    $this->assign("img_list",$img_list);
	    $this->assign("img_index",count($img_list));
	    
	    
	    $weight_list = M("WeightUnit")->findAll();
	    $this->assign("weight_list",$weight_list);
	    
	    
	    //输出配送方式列表
	    $delivery_list = M("Delivery")->where("is_effect=1")->findAll();
	    foreach($delivery_list as $k=>$v)
	    {
	        $delivery_list[$k]['free_count'] = M("FreeDelivery")->where("deal_id=".$vo['id']." and delivery_id = ".$v['id'])->getField("free_count");
	        $delivery_list[$k]['checked'] = M("DealDelivery")->where("deal_id=".$vo['id']." and delivery_id = ".$v['id'])->count();
	    }
	    $this->assign("delivery_list",$delivery_list);
	    
	    //输出支付方式
	    $payment_list = M("Payment")->where("is_effect=1")->findAll();
	    foreach($payment_list as $k=>$v)
	    {
	        $payment_list[$k]['checked'] = M("DealPayment")->where("deal_id=".$vo['id']." and payment_id = ".$v['id'])->count();
	    }
	    $this->assign("payment_list",$payment_list);
	    
	    
	    //输出规格库存的配置
	    //$attr_stock = M("AttrStock")->where("deal_id=".intval($vo['id']))->order("id asc")->findAll();
	    // 输出规格库存的配置
            $attr_stock = unserialize($vo['cache_attr_stock']);
	    $attr_cfg_json = "{";
	    $attr_stock_json = "{";
	    
	    foreach($attr_stock as $k=>$v)
	    {
	        $attr_cfg_json.=$k.":"."{";
	        $attr_stock_json.=$k.":"."{";
	        foreach($v as $key=>$vvv)
	        {
	            if($key!='attr_cfg')
	                $attr_stock_json.="\"".$key."\":"."\"".$vvv."\",";
	        }
	        $attr_stock_json = substr($attr_stock_json,0,-1);
	        $attr_stock_json.="},";
	        	
	        $attr_cfg_data = unserialize($v['attr_cfg']);
	        foreach($attr_cfg_data as $attr_id=>$vv)
	        {
	            $attr_cfg_json.=$attr_id.":"."\"".$vv."\",";
	        }
	        $attr_cfg_json = substr($attr_cfg_json,0,-1);
	        $attr_cfg_json.="},";
	    }
	    if($attr_stock)
	    {
	        $attr_cfg_json = substr($attr_cfg_json,0,-1);
	        $attr_stock_json = substr($attr_stock_json,0,-1);
	    }
	    
	    $attr_cfg_json .= "}";
	    $attr_stock_json .= "}";

		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',0);
		//关联商品
		//$tempInfo = M('Relate_goods')->where('good_id='.$id.' and is_shop=0')->find();
		$tempInfo = unserialize($vo['cache_relate']);
		if( $tempInfo ){
			$relate_goods = array();
			$tmpList = M('Deal')->where('id in ('.$tempInfo['relate_ids'].')')->field('id,name,img')->select();
			foreach($tmpList as $item){
				$durl = url("index","preview#deal",array('id'=>$item['id'],'type'=>0));
				$relate_goods[] = array(
						'id'		=>	$item['id'],
						'name'		=>	$item['name'],
						'url'		=>	$durl,
						'share_url'	=>	SITE_DOMAIN.$durl,
						'img'		=>	$item['img']
				);
			}

			$this->assign("relate_goods",$relate_goods);
		}	    
	    
	    
	    $this->assign("select_location",$select_location);
	    $this->assign("attr_cfg_json",$attr_cfg_json);
	    $this->assign("attr_stock_json",$attr_stock_json);
	    $this->assign("tc_mobile_moban",$this->load_tc_page("tc_mobile"));
	    $this->assign("tc_pc_moban",$this->load_tc_page("tc_pc"));
	    $this->display();
	}
	
	public function biz_apply_shop_edit(){
	    $id = intval($_REQUEST['id']);
    
	    $condition['is_delete'] = 0;
	    $condition['id'] = $id;
	    $vo = M("DealSubmit")->where($condition)->find();

        $carriage_data = getCarriageTemplate($vo['supplier_id']);
        
        $this->assign("carriage_template",$carriage_data['carriage_template']);
        $this->assign("carriage_number",$carriage_data['carriage_number']);
        $this->assign("default_delivery",$carriage_data['default_delivery']);
        $this->assign("delivery_type",$carriage_data['delivery_type']);
        
        $user_group = M("UserGroup")->order("score asc")->findAll();
        $this->assign("user_group",json_encode($user_group));
        
	    $vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
	    $vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
		$vo['publish_verify_balance']=$GLOBALS['db']->getOne("select publish_verify_balance from ".DB_PREFIX ."supplier where id =".$vo['supplier_id'])*100;
	    $this->assign ( 'vo', $vo );
	     
		$this->assign("new_sort", M("Deal")->where("is_delete=0")->max("sort")+1);
		//new
		$cate_tree = M("ShopCate")->where('is_delete = 0 and pid=0')->findAll();		
		$this->assign("cate_tree",$cate_tree);
		
		$sub_cate_tree = M("ShopCate")->where('is_delete = 0 and pid > 0')->findAll();
		$this->assign("sub_cate_tree",$sub_cate_tree);
		
		$cate1=M("shop_cate");
        $name1=$cate1->where('id in ('.$vo['shop_cate_id'].')')->findAll();

        foreach ($name1 as $k=>$v){            
            if($v['pid'] > 0){
                $first_cate = $cate1->where(array('id'=>$v['pid']))->find();
                $name1[$k]['first_cate'] = $first_cate['name'];
            }else{
                $name1[$k]['first_cate']='';
            }
        }
        $this->assign("shop_cate",$name1);
		
		
		//end
	

	    //选中门店
	    $select_location = array();
	    $select_location = implode(",", unserialize($vo['cache_location_id']));
	     
	    
	     
	    $brand_list = M("Brand")->findAll();
	    $this->assign("brand_list",$brand_list);
	     
	     
	    $supplier_info = M("Supplier")->where("id=".$vo['supplier_id'])->find();
	    $this->assign("supplier_info",$supplier_info);
	     
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
	    $this->assign("goods_type_list",$goods_type_list);
	     
	    //输出图片集
	    $img_list = unserialize($vo['cache_focus_imgs']);
	    $this->assign("img_list",$img_list);
		$this->assign("img_index",count($img_list));
	     
	     
	    $weight_list = M("WeightUnit")->findAll();
	    $this->assign("weight_list",$weight_list);


	    // 输出规格库存的配置
	    $attr_stock = unserialize($vo['cache_attr_stock']);
	    $attr_cfg_json = "{";
	    $attr_stock_json = "{";
	     
	    foreach($attr_stock as $k=>$v)
	    {
	        $attr_cfg_json.=$k.":"."{";
	        $attr_stock_json.=$k.":"."{";
	        foreach($v as $key=>$vvv)
	        {
	            if($key!='attr_cfg')
	                $attr_stock_json.="\"".$key."\":"."\"".$vvv."\",";
	        }
	        $attr_stock_json = substr($attr_stock_json,0,-1);
	        $attr_stock_json.="},";
	
	        $attr_cfg_data = unserialize($v['attr_cfg']);
	        foreach($attr_cfg_data as $attr_id=>$vv)
	        {
	            $attr_cfg_json.=$attr_id.":"."\"".$vv."\",";
	        }
	        $attr_cfg_json = substr($attr_cfg_json,0,-1);
	        $attr_cfg_json.="},";
	    }
	    if($attr_stock)
	    {
	        $attr_cfg_json = substr($attr_cfg_json,0,-1);
	        $attr_stock_json = substr($attr_stock_json,0,-1);
	    }
	     
	    $attr_cfg_json .= "}";
	    $attr_stock_json .= "}";


	    $this->assign("select_location",$select_location);
	    $this->assign("attr_cfg_json",$attr_cfg_json);
	    $this->assign("attr_stock_json",$attr_stock_json);

		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',0);
		//关联商品
		//$tempInfo = M('Relate_goods')->where('good_id='.$id.' and is_shop=0')->find();
		$tempInfo = unserialize($vo['cache_relate']);
		if( $tempInfo ){
			$relate_goods = array();
			$tmpList = M('Deal')->where('id in ('.$tempInfo['relate_ids'].')')->field('id,name,img')->select();
			foreach($tmpList as $item){
				$durl = url("index","preview#deal",array('id'=>$item['id'],'type'=>0));
				$relate_goods[] = array(
						'id'		=>	$item['id'],
						'name'		=>	$item['name'],
						'url'		=>	$durl,
						'share_url'	=>	SITE_DOMAIN.$durl,
						'img'		=>	$item['img']
				);
			}

			$this->assign("relate_goods",$relate_goods);
		}	    
	    
	    //输出配送方式列表
	    $delivery_list = M("Delivery")->where("is_effect=1")->findAll();
	    foreach($delivery_list as $k=>$v)
	    {
	        $delivery_list[$k]['free_count'] = M("FreeDelivery")->where("deal_id=".$vo['deal_id']." and delivery_id = ".$v['id'])->getField("free_count");
	        $delivery_list[$k]['checked'] = M("DealDelivery")->where("deal_id=".$vo['deal_id']." and delivery_id = ".$v['id'])->count();
	    }
	    $this->assign("delivery_list",$delivery_list);
	    //输出支付方式
	    $payment_list = M("Payment")->where("is_effect=1")->findAll();
	    foreach($payment_list as $k=>$v)
	    {
	        $payment_list[$k]['checked'] = M("DealPayment")->where("deal_id=".$vo['deal_id']." and payment_id = ".$v['id'])->count();
	    }
	    $this->assign("payment_list",$payment_list);
	    
	    $this->display();
	}
	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//对于商户请求操作
		if(intval($_REQUEST['edit_type']) == 2 && intval($_REQUEST['deal_id'])>0){ //商户提交修改审核
		    $deal_submit_id = intval($_REQUEST['id']);
		    $_REQUEST['id'] = intval($_REQUEST['deal_id']);
		}
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		
		if(empty($data['img'])){
		    $this->error("必须添加团购商品图片");
		}
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$_REQUEST['id'])));
		
		if($data['current_price']<0)
		{
		    $this->error("销售价不能为负数");
		}
			
		if($data['origin_price']<0)
		{
		    $this->error("原价不能为负数");
		}
		if($data['balance_price']<0)
		{
		    $this->error("结算价不能为负数");
		}
		if($data['balance_price'] > $data['current_price'])
		{
		    $this->error("结算价不能高于销售价");
		}
		if(intval($data['return_score'])<0)
		{
			$this->error("积分返还不能为负数");
		}
		if(floatval($data['return_money'])<0)
		{
			$this->error("现金返还不能为负数");
		}
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}		
		if($_REQUEST['cate_id']=='' && $_REQUEST['second_cate_id']=='' )
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}

		if($data['max_bought'] < 0){
		    $this->error(L("请输入有效库存"));
		}
		
		if(intval($data['supplier_id'])==0)
		{
		    $this->error("请选择商户");
		}
		elseif (empty($_REQUEST['location_id'])){
		    $this->error("必须选择一家门店");
		}

		if($data['min_bought']<0)
		{
			$this->error(L("DEAL_MIN_BOUGHT_ERROR_TIP"));
		}
		if($data['user_min_bought']<0)
		{
			$this->error(L("DEAL_USER_MIN_BOUGHT_ERROR_TIP"));
		}		
		if($data['user_max_bought']<0)
		{
			$this->error(L("DEAL_USER_MAX_BOUGHT_ERROR_TIP"));
		}
		if($data['user_max_bought']<$data['user_min_bought']&&$data['user_max_bought']!=0)
		{
			$this->error(L("DEAL_USER_MAX_MIN_BOUGHT_ERROR_TIP"));
		}
	
		if($_REQUEST['deal_attr']){
		    foreach($_REQUEST['deal_attr'] as $k=>$deal_attr){
		        foreach($deal_attr as $kk=>$vv){
		            if(strim($vv)==''){
		                $this->error(L("请填写商品属性"));
		                break 2;
		            }
		        }
		    }
		}
		
		
		if(intval($_REQUEST['is_allow_fx'])==0){
		    $_REQUEST['is_fx']=0;
		}else{
		    $_REQUEST['fx_salary_type']=$_REQUEST['fx_salary_type'];
		    $_REQUEST['fx_salary']=$_REQUEST['fx_salary'];
		}
		
		$_REQUEST['allow_user_discount'] = intval($_REQUEST['allow_user_discount']);
		$_REQUEST['is_pick'] = intval($_REQUEST['is_pick']);
		$_REQUEST['is_referral'] = intval($_REQUEST['is_referral']);
		$_REQUEST['is_allow_fx'] = intval($_REQUEST['is_allow_fx']);
		$_REQUEST['buyin_app'] = intval($_REQUEST['buyin_app']);
		$_REQUEST['any_refund'] = intval($_REQUEST['any_refund']);
		$_REQUEST['is_recommend'] = intval($_REQUEST['is_recommend']);
		$_REQUEST['forbid_sms'] = intval($_REQUEST['forbid_sms']);
		$_REQUEST['notice'] = intval($_REQUEST['notice']);
		$_REQUEST['expire_refund'] = intval($_REQUEST['expire_refund']);
		$_REQUEST['is_lottery'] = intval($_REQUEST['is_lottery']);
        if($_REQUEST['second_cate_id']!=''){
            $_REQUEST['cate_id']=$this->tuan_cate_pid($_REQUEST['cate_id'],$_REQUEST['second_cate_id']);
        }
		if($_REQUEST['is_lottery']==1){  //0元抽奖
		    $_REQUEST['deal_tag'][] = 0;
		}
		if($_REQUEST['any_refund']==1){  //支持随时退
		    $_REQUEST['deal_tag'][] = 6;
		}
		if($_REQUEST['expire_refund']==1){//支持过期退
		    $_REQUEST['deal_tag'][] = 5;
		}

		require_once APP_ROOT_PATH."/system/model/DealObject.php";
		$deal_object = new DealObject();		

		$type_string = 'tuan' ;

		$deal_object->setParamet($_REQUEST, $type_string);
		$result = $deal_object->save($_REQUEST);
		
		// 更新数据
		if ($result['status']==1) {
			if($deal_submit_id>0 && intval($_REQUEST['edit_type']) == 2){ //商户提交审核
				//同步商户数据表
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("deal_id"=>$result['id'],"admin_check_status"=>1,"deal_submit_memo"=>$_REQUEST['deal_submit_memo']),"UPDATE","id=".$deal_submit_id);
			}
			//成功提示
			save_log($_REQUEST['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($_REQUEST['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0);
		}
	}
	
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				M("DealCoupon")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->setField("is_delete",1);
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
					 
				}
				if($info) $info = implode(",",$info);
                $field=array();
                $field['is_delete']=1;
                $field['is_effect']=0;
				$list = M(MODULE_NAME)->where ( $condition )->save ($field);

				if ($list!==false) {
					$locations = M("DealLocationLink")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($locations as $location)
					{
						recount_supplier_data_count($location['location_id'],"daijin");
						recount_supplier_data_count($location['location_id'],"tuan");
					}
					
					// 删除关键字
					require_once(APP_ROOT_PATH."system/model/search_key_words.php");
					deleteKeyWordsApi( explode(',', $id), 1);
					
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
	
	public function restore() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				M("DealCoupon")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->setField("is_delete",0);
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
					 					
				}
				if($info) $info = implode(",",$info);
                $field=array();
                $field['is_delete']=0;
                $field['is_effect']=0;
				$list = M(MODULE_NAME)->where ( $condition )->save ($field);
				if ($list!==false) {
					 
					 
					$locations = M("DealLocationLink")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($locations as $location)
					{
						recount_supplier_data_count($location['location_id'],"daijin");
						recount_supplier_data_count($location['location_id'],"tuan");
					}
					
					// 添加关键字
					require_once(APP_ROOT_PATH."system/model/search_key_words.php");
					insertKeyWordsApi(explode(',', $id), 1);
					
					save_log($info.l("RESTORE_SUCCESS"),1);
					$this->success (l("RESTORE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("RESTORE_FAILED"),0);
					$this->error (l("RESTORE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				//删除的验证
				if(M("DealOrder")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error(l("DEAL_ORDER_NOT_EMPTY"),$ajax);
				}
				M("DealCoupon")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealDelivery")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealPayment")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealAttr")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("AttrStock")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealCateTypeDealLink")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealLocationLink")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				//删除关联商品
				M("Relate_goods")->where(array ('good_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
					 
					 
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
					
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
	/**
	 * 删除商户提交数据
	 */
	public function biz_submit_del() {
	    //彻底删除指定记录
	    $ajax = intval($_REQUEST['ajax']);
	    $id = $_REQUEST ['id'];
	    if (isset ( $id )) {
	        $condition = array ('id' => array ('in', explode ( ',', $id ) ) );

	        $rel_data = M("DealSubmit")->where($condition)->findAll();
	        foreach($rel_data as $data)
	        {
	            $info[] = $data['name'];
	
	
	        }
	        if($info) $info = implode(",",$info);
	        $list = M("DealSubmit")->where ( $condition )->delete();
	        	
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
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField('name');
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		 
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id in({$id})")->save(array("is_effect"=>$n_is_effect,"update_time"=>NOW_TIME));
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		 
		$locations = M("DealLocationLink")->where(array ('deal_id' => $id ))->findAll();
					foreach($locations as $location)
					{
						recount_supplier_data_count($location['location_id'],"daijin");
						recount_supplier_data_count($location['location_id'],"tuan");
					}
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	public function deal_upline(){
        $id = $_REQUEST['id'];
        if(!$id) $this->ajaxReturn(0,l("id不能为空"),1)	;
        $info = M(MODULE_NAME)->where("id in($id)")->getField("name");
        M(MODULE_NAME)->where("id in({$id})")->save(array("is_effect"=>1,"update_time"=>NOW_TIME));
        save_log($info.l("SET_EFFECT_0"),1);

        $locations = M("DealLocationLink")->where("deal_id in({$id})")->findAll();
        foreach($locations as $location)
        {
            recount_supplier_data_count($location['location_id'],"daijin");
            recount_supplier_data_count($location['location_id'],"tuan");
        }
        $this->ajaxReturn(1,l("上架成功"),1)	;
    }
    public function deal_downline(){
        $id = $_REQUEST['id'];
        if(!$id) $this->ajaxReturn(0,l("id不能为空"),1);
        $info = M(MODULE_NAME)->where("id in($id)")->getField("name");
        M(MODULE_NAME)->where("id in({$id})")->save(array("is_effect"=>0,"update_time"=>NOW_TIME));
        save_log($info.l("SET_EFFECT_0"),1);
        $locations = M("DealLocationLink")->where("deal_id in({$id})")->findAll();
        foreach($locations as $location)
        {
            recount_supplier_data_count($location['location_id'],"daijin");
            recount_supplier_data_count($location['location_id'],"tuan");
        }
        $this->ajaxReturn(0,l("下架成功"),1)	;
    }
	public function attr_html()
	{
		$deal_goods_type = intval($_REQUEST['deal_goods_type']);
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type']);
		
		$edit_type = $edit_type==0?1:$edit_type;
		
		$is_data = false;
		if($edit_type == 1 && $GLOBALS['db']->getOne("select deal_goods_type from ".DB_PREFIX."deal where id = ".$id)==$deal_goods_type){
		    $is_data = true;
		}elseif($edit_type==2 && $GLOBALS['db']->getOne("select deal_goods_type from ".DB_PREFIX."deal_submit where id = ".$id)==$deal_goods_type){
		    $is_data = true;
		}

		if($id>0 && $is_data)
		{		
		    $goods_type_attr = null;
		    if ($edit_type == 1){
			     $goods_type_attr = M()->query("select a.name as attr_name,a.is_checked as is_checked,a.id as deal_attr_id ,b.* from ".conf("DB_PREFIX")."deal_attr as a left join ".conf("DB_PREFIX")."goods_type_attr as b on a.goods_type_attr_id = b.id where a.deal_id=".$id." order by a.id asc");
		    }else{
		        //商品分类属性
		        $goods_type_attr_data = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_type_attr where goods_type_id = ".$deal_goods_type);
		        foreach($goods_type_attr_data as $k=>$v){
		            $f_goods_type_attr[$v['id']] = $v;
		        }
		        //团购已经选择的分类属性值
		        $deal_attr_data = unserialize($GLOBALS['db']->getOne("select cache_deal_attr from ".DB_PREFIX."deal_submit where id=".$id));

		        foreach($deal_attr_data as $k=>$v){
		        	$temp_data=$v;
		        	$temp_data['name']=$f_goods_type_attr[$v['id']]['name'];
		        	$temp_data['input_type']=$f_goods_type_attr[$v['id']]['input_type'];
		        	$temp_data['preset_value']=$f_goods_type_attr[$v['id']]['preset_value'];
		        
		        
		            $goods_type_attr[] = $temp_data;
		        }
		    }

		    $goods_type_attr_new = array();
			$goods_type_attr_id = 0;
			if($goods_type_attr)
			{
				foreach($goods_type_attr as $k=>$v)
				{
					$goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
					if($goods_type_attr_id!=$v['id'])
					{
						$goods_type_attr[$k]['is_first'] = 1;
					}
					else
					{
						$goods_type_attr[$k]['is_first'] = 0;
					}
					$goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
					$goods_type_attr_id = $v['id'];
				}	

			}
			else 
			{
				$goods_type_attr = M("GoodsTypeAttr")->where("goods_type_id=".$deal_goods_type)->findAll();
				foreach($goods_type_attr as $k=>$v)
				{
					$goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
					$goods_type_attr[$k]['is_first'] = 1;
					$goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
				}

			}		
		}
		else
		{
			$goods_type_attr = M("GoodsTypeAttr")->where("goods_type_id=".$deal_goods_type)->findAll();
			foreach($goods_type_attr as $k=>$v)
			{
				$goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
				$goods_type_attr[$k]['is_first'] = 1;
				$goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
			}

		}

		$this->assign("goods_type_attr",$goods_type_attr_new);

		$this->display();
	}
	
	public function show_detail()
	{
		$id = intval($_REQUEST['id']);
		
		$deal_info = M("Deal")->getById($id);
		$this->assign("deal_info",$deal_info);
		//购买的单数
		$real_user_count = intval($GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.deal_id = ".$id." and do.pay_status = 2"));
		$this->assign("real_user_count",$real_user_count);
		
		$real_buy_count =  intval($GLOBALS['db']->getOne("select sum(doi.number) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.deal_id = ".$id." and do.pay_status = 2"));
		$this->assign("real_buy_count",$real_buy_count);
		
		$real_coupon_count = intval(M("DealCoupon")->where("deal_id=".$id." and is_valid=1")->count());
		$this->assign("real_coupon_count",$real_coupon_count);

		//总收款，不计退款
		$pay_total_rows = $GLOBALS['db']->getAll("select pn.money from ".DB_PREFIX."payment_notice as pn left join ".DB_PREFIX."deal_order as do on pn.order_id = do.id left join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.pay_status = 2 and doi.deal_id = ".$id." and pn.is_paid = 1 group by pn.id");
		$pay_total = 0;
		foreach($pay_total_rows as $money)
		{
			$pay_total = $pay_total + floatval($money['money']);
		}		
		$this->assign("pay_total",$pay_total);

		//每个支付方式下的收款
		$payment_list = M("Payment")->findAll();
		foreach($payment_list as $k=>$v)
		{
			$payment_pay_total = 0;
			$payment_pay_total_rows = $GLOBALS['db']->getAll("select pn.money from ".DB_PREFIX."payment_notice as pn left join ".DB_PREFIX."deal_order as do on pn.order_id = do.id left join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.pay_status = 2 and doi.deal_id = ".$id." and pn.is_paid = 1 and pn.payment_id = ".$v['id']." group by pn.id");
			foreach($payment_pay_total_rows as $money)
			{
				$payment_pay_total = $payment_pay_total + floatval($money['money']);
			}	
			$payment_list[$k]['pay_total'] = $payment_pay_total;
		}
		$this->assign("payment_list",$payment_list);
		
		
		//订单实收
		$order_total = 0;
		$order_total_rows = $GLOBALS['db']->getAll("select do.pay_amount as money from ".DB_PREFIX."deal_order as do inner join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.pay_status = 2 and doi.deal_id = ".$id." group by do.id");
		foreach($order_total_rows as $money)
		{
				$order_total = $order_total + floatval($money['money']);
		}	
		$this->assign("order_total",$order_total);
		
		//额外退款的订单
		$extra_count = $GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.extra_status > 0 and doi.deal_id = ".$id);
		$this->assign("extra_count",$extra_count);
		
		//额外退款的订单
		$aftersale_count = $GLOBALS['db']->getOne("select count(distinct(do.id)) from ".DB_PREFIX."deal_order as do left join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.after_sale > 0 and doi.deal_id = ".$id);
		$this->assign("aftersale_count",$aftersale_count);
		
		//售后退款
		$refund_money = 0;
		$refund_total_rows = $GLOBALS['db']->getAll("select do.refund_money as money from ".DB_PREFIX."deal_order as do inner join ".DB_PREFIX."deal_order_item as doi on do.id = doi.order_id where do.pay_status = 2 and doi.deal_id = ".$id." group by do.id");
		foreach($refund_total_rows as $money)
		{
				$refund_money = $refund_money + floatval($money['money']);
		}
		$this->assign("refund_money",$refund_money);
		$this->display();
	}
	
	
	public function shop()
	{
		//分类

        $cate_tree = M("ShopCate")->where('is_delete = 0')->findAll();
        $cate_tree = D("ShopCate")->toFormatTree($cate_tree,'name');
        $this->assign("cate_tree",$cate_tree);
       

		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		//输出品牌
		$brand_list = M("Brand")->findAll();
		$this->assign("brand_list",$brand_list);
		
		$type = intval($_REQUEST['type']);
		$this->assign("type",$type);
        //输出判断条件
		$map=$this->_shopMap();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);

		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	private function _shopMap(){
        $map=array();
        //开始加载搜索条件
        if(intval($_REQUEST['id'])>0)
            $map['id'] = intval($_REQUEST['id']);
        $map['is_delete'] = 0;
        $map['_string'] = "1=1 ";
            //自营商城0，自营积分1，商户商城商品2，商户团购商品3
        $type=intval($_REQUEST['type']);
        if($type==0){
            $map['supplier_id']=0;
            $map['buy_type']=0;
        }else if($type==1){
            $map['supplier_id']=0;
            $map['buy_type']=1;
        }else if($type==2){
            $map['supplier_id']=array("gt",0);
            $map['is_shop'] = 1;
        }else if($type==3){
            $map['supplier_id']=array("gt",0);
            $map['is_shop'] = 0;
        }
        if(strim($_REQUEST['name'])!='')
        {
            $map['name'] = array('like','%'.strim($_REQUEST['name']).'%');
        }
        if(intval($_REQUEST['city_id'])>0)
        {
            require_once(APP_ROOT_PATH."system/utils/child.php");
            $child = new Child("deal_city");
            $city_ids = $child->getChildIds(intval($_REQUEST['city_id']));
            $city_ids[] = intval($_REQUEST['city_id']);
            $map['city_id'] = array("in",$city_ids);
        }

        /*if(intval($_REQUEST['cate_id'])>0)
        {
            require_once(APP_ROOT_PATH."system/utils/child.php");
            $child = new Child("shop_cate");
            $cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
            $cate_ids[] = intval($_REQUEST['cate_id']);
            $map['shop_cate_id'] = array("in",$cate_ids);
        }
        */
        if(intval($_REQUEST['cate_id'])>0)
        {
            $cate_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX ."shop_cate where id =".intval($_REQUEST['cate_id']));
            $map['_string'].=" and  FIND_IN_SET('$cate_name',shop_cate_match_row)";
        }

        if(intval($_REQUEST['brand_id'])>0)
            $map['brand_id'] = intval($_REQUEST['brand_id']);
        if(strim($_REQUEST['supplier_name'])!='')
        {
            if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier"))<50000)
                $sql  ="select group_concat(id) from ".DB_PREFIX."supplier where name like '%".strim($_REQUEST['supplier_name'])."%'";
            else
            {
                $kws_div = div_str(trim($_REQUEST['supplier_name']));
                foreach($kws_div as $k=>$item)
                {
                    $kw[$k] = str_to_unicode_string($item);
                }
                $kw_unicode = implode(" ",$kw);
                $sql = "select group_concat(id) from ".DB_PREFIX."supplier where (match(name_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
            }
            $ids = $GLOBALS['db']->getCol($sql);
            $map['supplier_id'] = array("in",$ids);
        }    
        //商品状态:出售中0，已售罄1，仓库中2
        $status=intval($_REQUEST['status']);
        if($status==0){
            $map['is_effect']=1;
            $map['begin_time']=array("elt",NOW_TIME);
            $map['end_time']=array(array("egt",NOW_TIME),array("eq",0),"or");
            $map['max_bought']=array(array('eq',-1),array('gt',0),'or');
        }else if($status==1){
            $map['is_effect']=1;
            $map['begin_time']=array("elt",NOW_TIME);
            $map['end_time']=array(array("egt",NOW_TIME),array("eq",0),"or");
            $map['max_bought']=array("eq",0);
        }else if($status==2){
            $map['_string'].=" and is_effect=0 or (is_effect=1 and ((begin_time>".NOW_TIME." and begin_time>0) or (end_time<".NOW_TIME." and end_time>0)))";
        }
        $this->assign("status",$status);
        $map['publish_wait'] = 0;
        return $map;
    }
	
	
	public function shop_add()
	{
		$this->assign("new_sort", M("Deal")->where("is_delete=0")->max("sort")+1);
		
		$cate_tree = M("ShopCate")->where('is_delete = 0 and pid=0')->findAll();
		$this->assign("cate_tree",$cate_tree);
		
		$user_group = M("UserGroup")->order("score asc")->findAll();
		$this->assign("user_group",json_encode($user_group));
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
		M("GoodsType")->findAll();
		
		
		$this->assign("goods_type_list",$goods_type_list);
		$this->assign("img_index",0);
		$weight_list = M("WeightUnit")->findAll();
		$this->assign("weight_list",$weight_list);
		
		$brand_list = M("Brand")->findAll();
		$this->assign("brand_list",$brand_list);	
		$type = intval($_REQUEST['type']);
		$this->assign("type",$type);      

		//输出配送方式列表
		$delivery_list = M("Delivery")->where("is_effect=1")->findAll();
		$this->assign("delivery_list",$delivery_list);
		
		//输出配送模板      

        $carriage_data = getCarriageTemplate(0);
        $this->assign("carriage_template",$carriage_data['carriage_template']);
        $this->assign("carriage_number",$carriage_data['carriage_number']);
        $this->assign("default_delivery",$carriage_data['default_delivery']);
        $this->assign("delivery_type",$carriage_data['delivery_type']);

		//输出商品活动
		$event_list = M("deal_event")->findAll();
		$this->assign("event_list",$event_list);
		//var_dump($event_list);die;
		
		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',1);
		$this->assign("tc_mobile_moban",$this->load_tc_page("tc_mobile"));
		$this->assign("tc_pc_moban",$this->load_tc_page("tc_pc"));
		$this->display();
	}
	
	public function carriage_template(){
	     
	    //输出配送模板
	    $supplier_id = $_REQUEST['supplier_id'];
	    if($supplier_id){
	        $carriage_template = $GLOBALS['db']->getAll("select ct.* from ".DB_PREFIX."carriage_template as ct where ct.supplier_id = ".$supplier_id."");
	    }else{
	        $carriage_template = $GLOBALS['db']->getAll("select ct.* from ".DB_PREFIX."carriage_template as ct where ct.supplier_id=0");
	    }
	    ajax_return($carriage_template);
	     
	}
	
	
	public function shop_insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//对于商户请求操作
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type']);
		
		if($id>0 && $edit_type==2){//商户申请新增团购
		    unset($_REQUEST['id']);
		}
		//开始验证有效性
		
		$type = intval($_REQUEST['type']);

		if(intval($_REQUEST['continue_add'])==1){
		    $this->assign("jumpUrl",u(MODULE_NAME."/shop_add",array("type"=>$type)));
		}else{
		    $this->assign("jumpUrl",u(MODULE_NAME."/shop",array("type"=>$type)));
		}
		
		if(empty($data['img'])){
		    $this->error("必须添加商品图片");
		}
		
		if($data['buy_type']==0)
		{
			if(intval($data['return_score'])<0)
			{
				$this->error("积分返还不能为负数");
			}
			if(floatval($data['return_money'])<0)
			{
				$this->error("现金返还不能为负数");
			}
			if($data['current_price']<0)
			{
			    $this->error("销售价不能为负数");
			}
				
			if($data['origin_price']<0)
			{
			    $this->error("原价不能为负数");
			}
			if($data['balance_price']<0)
			{
			    $this->error("结算价不能为负数");
			}
			if($data['balance_price'] > $data['current_price'])
			{
			    $this->error("结算价不能高于销售价");
			}
		}
		else
		{
			$data['return_score'] = "-".abs($_REQUEST['deal_score']);
			if(intval($_REQUEST['deal_score'])==0)
			{
				$this->error("请输入所需的积分");
			}
		}
		if(floatval($data['dist_service_rate'])<0 || floatval($data['dist_service_rate'])>100 )
		{
		    $this->error("驿站服务费率请填写0-100");
		}
		if(floatval($data['recommend_user_return_ratio'])<0 || floatval($data['recommend_user_return_ratio'])>100 )
		{
		    $this->error("会员返佣率请填写0-100");
		}
		
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}	
		if($data['shop_cate_id']==0)
		{
			$this->error(L("SHOP_CATE_EMPTY_TIP"));
		}		

		if($data['user_min_bought']<0)
		{
			$this->error(L("DEAL_USER_MIN_BOUGHT_ERROR_TIP"));
		}		
		if($data['user_max_bought']<0)
		{
			$this->error(L("DEAL_USER_MAX_BOUGHT_ERROR_TIP"));
		}
		if($data['user_max_bought']<$data['user_min_bought']&&$data['user_max_bought']>0)
		{
			$this->error(L("DEAL_USER_MAX_MIN_BOUGHT_ERROR_TIP"));
		}

		if($data['max_bought'] < 0){
		    $this->error(L("请输入有效库存"));
		}
		
		if(!$data['supplier_id']){
		    $data['is_pick']=0;
		}
		if($data['buy_type']==0&&$data['supplier_id']!=0&&empty($_REQUEST['location_id'])){
		    $this->error(L("必须选择一家门店！"));
		}
		if($data['supplier_id']!=0 && empty($_REQUEST['location_id']) && $data['is_pick']=1){
		    $this->error(L("自提商品必须选择门店"));
		}
		if(!$data['carriage_template_id'] && ($data['delivery_type']  == 1 || $data['delivery_type']  == 3)){
		    $this->error(L("请选择一个运费模板"));
		}
		
		if($_REQUEST['deal_attr']){
		    foreach($_REQUEST['deal_attr'] as $k=>$deal_attr){
		        foreach($deal_attr as $kk=>$vv){
		            if(strim($vv)==''){
		                $this->error(L("请填写商品属性"));
		                break 2;
		            }
		        }
		    }
		}
		
		
		if(intval($_REQUEST['is_allow_fx'])==0){
		    $_REQUEST['is_fx']=0;
		}else{
		    $_REQUEST['fx_salary_type']=$_REQUEST['fx_salary_type'];
		    $_REQUEST['fx_salary']=$_REQUEST['fx_salary'];
		}
		
		$_REQUEST['allow_user_discount'] = intval($_REQUEST['allow_user_discount']);
		$_REQUEST['is_pick'] = intval($_REQUEST['is_pick']);
		$_REQUEST['is_referral'] = intval($_REQUEST['is_referral']);
		$_REQUEST['is_allow_fx'] = intval($_REQUEST['is_allow_fx']);
		$_REQUEST['buyin_app'] = intval($_REQUEST['buyin_app']);
		$_REQUEST['is_refund'] = intval($_REQUEST['is_refund']);
		$_REQUEST['is_recommend'] = intval($_REQUEST['is_recommend']);
		
		if($_REQUEST['is_refund']==1){
		    $_REQUEST['deal_tag'][] = 6;
		}
		
		require_once APP_ROOT_PATH."/system/model/DealObject.php";
		$deal_object = new DealObject();
		
		if($type==1){
		    $type_string = 'score' ;
		    $_REQUEST['return_score'] = $_REQUEST['deal_score'];
		}else{
		    $type_string = 'shop' ;
		}
		$deal_object->setParamet($_REQUEST, $type_string);
		$result = $deal_object->save();

		if ($result['status']==1) {
			if($id>0 && $edit_type == 2){ //商户提交审核
				//同步商户数据表
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("deal_id"=>$result['id'],"admin_check_status"=>1,"deal_submit_memo"=>$_REQUEST['deal_submit_memo']),"UPDATE","id=".$id);
			}
			save_log($_REQUEST['name'].L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($_REQUEST['name'].L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function shop_edit() {		
		$id = intval($_REQUEST ['id']);
	
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		if($vo['buy_type']==1){ //积分商品
		    $type = 1;
		}else{
		    if($vo['supplier_id'] > 0){ //商家商品
		        $type = 2;
		    }else{ //平台自营商品
		        $type = 0;
		    }
		}
		$vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
		$vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
		$vo['coupon_begin_time'] = $vo['coupon_begin_time']!=0?to_date($vo['coupon_begin_time']):'';
		$vo['coupon_end_time'] = $vo['coupon_end_time']!=0?to_date($vo['coupon_end_time']):'';

		$vo['weight'] = round($vo['weight'],2);
		if($vo['buy_type']==1)
		$vo['deal_score'] = abs($vo['return_score']);
		$stock_cfg = intval($GLOBALS['db']->getOne("select stock_cfg from ".DB_PREFIX."deal_stock where deal_id = ".$id));
		$vo['max_bought'] = $stock_cfg?$stock_cfg:$vo['max_bought'];

		
		$this->assign ( 'type', $type );

		if($type == 2 || $type ==0){
		    $deal_fx_salary = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_fx_salary where deal_id=".$id);
		    if($vo['is_fx'] > 0 && $deal_fx_salary){
		        $vo['fx_salary_type'] = $deal_fx_salary[0]['fx_salary_type'];
		        foreach($deal_fx_salary as $k=>$v){
		            if($vo['fx_salary_type'] == 1){
		               $deal_fx_salary[$k]['fx_salary'] =$v['fx_salary'] * 100;
		            }		            
		        }
		    }	
		    
		   $deal_fx_salary = data_format_idkey($deal_fx_salary,$key='fx_level');
		    
		    $this->assign ( 'fx_salary', $deal_fx_salary );
		}
		$supplier_info = M("Supplier")->where("id=".$vo['supplier_id'])->find();
		$this->assign("supplier_info",$supplier_info);
		
		if( $vo['publish_verify_balance'] == 0){
		    $vo['publish_verify_balance'] = $supplier_info['publish_verify_balance'];
		}
		$vo['publish_verify_balance'] =  $vo['publish_verify_balance'] *100;
		$this->assign ( 'vo', $vo );

		$cate_tree = M("ShopCate")->where('is_delete = 0 and pid=0')->findAll();		
		$this->assign("cate_tree",$cate_tree);
		
		$user_group = M("UserGroup")->order("score asc")->findAll();
		$this->assign("user_group",json_encode($user_group));
		//输出商品活动
		$event_list = M("deal_event")->findAll();
		$this->assign("event_list",$event_list);
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		

		
		$goods_type_list = $GLOBALS['db']->getAll("SELECT gt.* from ".DB_PREFIX."goods_type_attr as ta  LEFT JOIN ".DB_PREFIX."goods_type as gt on gt.id=ta.goods_type_id GROUP BY ta.goods_type_id");
		$this->assign("goods_type_list",$goods_type_list);
		
		$cate1=M("shop_cate");
        $name1=$cate1->where('id in ('.$vo['shop_cate_id'].')')->findAll();

        foreach ($name1 as $k=>$v){            
            if($v['pid'] > 0){
                $first_cate = $cate1->where(array('id'=>$v['pid']))->find();
                $name1[$k]['first_cate'] = $first_cate['name'];
            }else{
                $name1[$k]['first_cate']='';
            }
        }
        $this->assign("shop_cate",$name1);

        
        $brand1=M("brand");
        $brand_all = array();
        $brand_all_id  = array();
        foreach ($name1 as $k=>$v){
            $brand_name=$v['name'];
            $brand_list=$brand1->query("select * from ".DB_PREFIX."brand where FIND_IN_SET('".$brand_name."',tag_match_row)");
            foreach($brand_list as $kk=>$brand){
                if(!in_array($brand['id'],$brand_all_id)){
                    $brand_all[] = $brand;
                    $brand_all_id[] = $brand['id'];
                }
            }
        }
        

	    $this->assign("brand_list",$brand_all);	
		
		//输出图片集
		$img_list = M("DealGallery")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
		$imgs = array();

		foreach($img_list as $k=>$v)
		{
			$imgs[$v['sort']] = $v['img']; 
		}
		
		$this->assign("img_list",$imgs);
		$this->assign("img_index",count($imgs));
		
		$weight_list = M("WeightUnit")->findAll();
		$this->assign("weight_list",$weight_list);

        //获取所属发布平台输出配置
		
		$carriage_data = getCarriageTemplate($vo['supplier_id']);		
		$this->assign("carriage_template",$carriage_data['carriage_template']);
		$this->assign("carriage_number",$carriage_data['carriage_number']);
		$this->assign("default_delivery",$carriage_data['default_delivery']);
		$this->assign("delivery_type",$carriage_data['delivery_type']);
		

		
		//输出支付方式
// 		$payment_list = M("Payment")->where("is_effect=1")->findAll();
// 		foreach($payment_list as $k=>$v)
// 		{
// 			$payment_list[$k]['checked'] = M("DealPayment")->where("deal_id=".$vo['id']." and payment_id = ".$v['id'])->count();			
// 		}
// 		$this->assign("payment_list",$payment_list);
		
		
		//输出规格库存的配置		

		$attr_json_data = $this->get_attr_json_data($vo['id']);
		$attr_cfg_json = $attr_json_data['attr_cfg_json'];
		$attr_stock_json = $attr_json_data['attr_stock_json'];
		
		$this->assign("attr_cfg_json",$attr_cfg_json);	
		$this->assign("attr_stock_json",$attr_stock_json);	
		
		//商品或团购 可以关联商品数量
		$this->assign('relate_goods_num',$this->RELATE_GOODS_NUM);
		$this->assign('is_shop',1);
		//关联商品
		$tempInfo = M('Relate_goods')->where('good_id='.$id.' and is_shop=1')->find();
		if( $tempInfo ){
			$relate_goods = array();
			$tmpList = M('Deal')->where('id in ('.$tempInfo['relate_ids'].')')->field('id,name,img')->select();
			foreach($tmpList as $item){
				$durl = url("index","deal#".$item['id'],array('preview'=>1));
				$relate_goods[] = array(
						'id'		=>	$item['id'],
						'name'		=>	$item['name'],
						'url'		=>	$durl,
						'share_url'	=>	SITE_DOMAIN.$durl,
						'img'		=>	$item['img']
				);
			}

			$this->assign("relate_goods",$relate_goods);
			
		}

		$this->assign("relate_goods_count",intval(count($relate_goods)));
		$this->assign("tc_mobile_moban",$this->load_tc_page("tc_mobile"));
		$this->assign("tc_pc_moban",$this->load_tc_page("tc_pc"));
		$this->display ();
	}
	
	
	public function shop_update() {


		B('FilterString');
		$data = M(MODULE_NAME)->create ();

        //logger::write(print_r($data,1));
		//对于商户请求操作
		if(intval($_REQUEST['edit_type']) == 2 && intval($_REQUEST['deal_id'])>0){ //商户提交修改审核
		    $deal_submit_id = intval($_REQUEST['id']);
		    $_REQUEST['id'] = intval($_REQUEST['deal_id']);
		}
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$type = intval($_REQUEST['type']);
		
		if(intval($_REQUEST['continue_add'])==1){
		    $this->assign("jumpUrl",u(MODULE_NAME."/shop_add"));
		}else{
		    $this->assign("jumpUrl",u(MODULE_NAME."/shop_edit",array("id"=>$_REQUEST['id'])));
		}
		if(empty($data['img'])){
		    $this->error("必须添加商品图片");
		}
		if($data['buy_type']==0)
		{
			if(intval($data['return_score'])<0)
			{
				$this->error("积分返还不能为负数");
			}
			if(floatval($data['return_money'])<0)
			{
				$this->error("现金返还不能为负数");
			}
			
			if($data['current_price']<0)
			{
			    $this->error("销售价不能为负数");
			}
			
			if($data['origin_price']<0)
			{
			    $this->error("原价不能为负数");
			}
			if($data['balance_price']<0)
			{
			    $this->error("结算价不能为负数");
			}
			
			if($data['balance_price'] > $data['current_price'])
			{
			    $this->error("结算价不能高于销售价");
			}
		}
		else
		{
			$data['return_score'] = "-".abs($_REQUEST['deal_score']);
			if(intval($_REQUEST['deal_score'])==0)
			{
				$this->error("请输入所需的积分");
			}
		}
		if(floatval($data['dist_service_rate'])<0 || floatval($data['dist_service_rate'])>100 )
		{
		    $this->error("驿站服务费率请填写0-100");
		}
		if(floatval($data['recommend_user_return_ratio'])<0 || floatval($data['recommend_user_return_ratio'])>100 )
		{
		    $this->error("会员返佣率请填写0-100");
		}
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}	
		if($data['shop_cate_id']=='')
		{
			$this->error(L("SHOP_CATE_EMPTY_TIP"));
		}
		if($data['user_min_bought']<0)
		{
			$this->error(L("DEAL_USER_MIN_BOUGHT_ERROR_TIP"));
		}		
		if($data['user_max_bought']<0)
		{
			$this->error(L("DEAL_USER_MAX_BOUGHT_ERROR_TIP"));
		}
		if($data['user_max_bought']<$data['user_min_bought']&&$data['user_max_bought']>0)
		{
			$this->error(L("DEAL_USER_MAX_MIN_BOUGHT_ERROR_TIP"));
		}
		
		if(!$data['supplier_id']){
		    $data['is_pick']=0;
		}
		if($data['buy_type']==0&&$data['supplier_id']!=0&&empty($_REQUEST['location_id'])){
		    $this->error(L("必须选择一家门店！"));
		}
		if($data['supplier_id']!=0 && empty($_REQUEST['location_id']) && $data['is_pick']=1){
		    $this->error(L("自提商品必须选择门店！"));
		}
		if(!$data['carriage_template_id'] && ($data['delivery_type']  == 1 || $data['delivery_type']  == 3)){
		    $this->error(L("请选择一个运费模板"));
		}
		
		if($data['max_bought'] < 0){
		     $this->error(L("请输入有效库存"));
		}
		
		if($_REQUEST['deal_attr']){
		    foreach($_REQUEST['deal_attr'] as $k=>$deal_attr){
		        foreach($deal_attr as $kk=>$vv){
		            if(strim($vv)==''){
		                $this->error(L("请填写商品属性"));
		                break 2;
		            }		        
		        }		        
		    }	    
		}
		
		if(intval($_REQUEST['is_allow_fx'])==0){
		    $_REQUEST['is_fx']=0;
		}else{
		    $_REQUEST['fx_salary_type']=$_REQUEST['fx_salary_type'];
		    $_REQUEST['fx_salary']=$_REQUEST['fx_salary'];
		}
		
		$_REQUEST['allow_user_discount'] = intval($_REQUEST['allow_user_discount']);
		$_REQUEST['is_pick'] = intval($_REQUEST['is_pick']);
		$_REQUEST['is_referral'] = intval($_REQUEST['is_referral']);
		$_REQUEST['is_allow_fx'] = intval($_REQUEST['is_allow_fx']);
		$_REQUEST['buyin_app'] = intval($_REQUEST['buyin_app']);
		$_REQUEST['is_refund'] = intval($_REQUEST['is_refund']);
		$_REQUEST['is_recommend'] = intval($_REQUEST['is_recommend']);

		if($_REQUEST['is_refund']==1){
		    $_REQUEST['deal_tag'][] = 6;
		}

        require_once APP_ROOT_PATH."/system/model/DealObject.php";
        $deal_object = new DealObject();
        
        if($type==1){
            $type_string = 'score' ;
            $_REQUEST['return_score'] = $_REQUEST['deal_score'];
        }else{
            $type_string = 'shop' ;
        }

        $deal_object->setParamet($_REQUEST, $type_string);
        $result = $deal_object->save($_REQUEST);
		if ($result['status']==1) {
			if($deal_submit_id>0 && intval($_REQUEST['edit_type']) == 2){ //商户提交审核
				//同步商户数据表
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("deal_id"=>$result['id'],"admin_check_status"=>1,"deal_submit_memo"=>$_REQUEST['deal_submit_memo']),"UPDATE","id=".$deal_submit_id);
			}	
			//成功提示
			save_log($_REQUEST['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			//$dbErr = M()->getDbError();
			save_log($_REQUEST['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0);
		}
	}
	
	public function filter_html()
	{
		$shop_cate_id = intval($_REQUEST['shop_cate_id']);
		$edit_type = intval($_REQUEST['edit_type']);
		$id = intval($_REQUEST['id']);
		
		$ids = $this->get_parent_ids($shop_cate_id);
		$filter_group = M("FilterGroup")->where(array("cate_id"=>array("in",$ids)))->findAll();
		
		if($edit_type == 1){ //管理员添加数据
    		
    		foreach($filter_group as $k=>$v)
    		{
    			$filter_group[$k]['value'] = M("DealFilter")->where("filter_group_id = ".$v['id']." and deal_id = ".$id)->getField("filter");
    		}
		
		}elseif ($edit_type == 2){//商户提交数据

		    $cache_deal_filter = $GLOBALS['db']->getOne("select cache_deal_filter from ".DB_PREFIX."deal_submit where id = ".$id);
		    $cache_deal_filter = unserialize($cache_deal_filter);
		    foreach($filter_group as $k=>$v)
		    {
		        $filter_group[$k]['value'] = $cache_deal_filter[$v['id']]['filter'];
		    }  
		}
		$this->assign("filter_group",$filter_group);
		$this->display();
	}
	
	//获取当前分类的所有父分类包含本分类的ID
	private $cate_ids = array();
	private function get_parent_ids($shop_cate_id)
	{
		$pid = $shop_cate_id;
		do{
			$pid = M("ShopCate")->where("id=".$pid)->getField("pid");
			if($pid>0)
			$this->cate_ids[] = $pid;
		}while($pid!=0);

		$this->cate_ids[] = $shop_cate_id;

		return $this->cate_ids;
	}
	
	
	//可购买优惠券列表 is_shop = 2
	
	
	function load_sub_cate()
	{
		$cate_id = intval($_REQUEST['cate_id']);
        $edit_type = intval($_REQUEST['edit_type']);
        $id = intval($_REQUEST['id']);
        
       
        $sub_cate_list = $GLOBALS['db']->getAll("select c.* from ".DB_PREFIX."deal_cate_type as c left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = c.id where l.cate_id = ".$cate_id);
        if($edit_type == 1){ //管理员添加数据
            $sub_cate_arr_data = $GLOBALS['db']->getAll("select deal_cate_type_id from ".DB_PREFIX."deal_cate_type_deal_link where deal_id = ".$id);
            foreach ($sub_cate_arr_data as $k=>$v){
                $sub_cate_arr[] = $v['deal_cate_type_id'];
            }
        
        }elseif ($edit_type == 2){//商户提交数据
            $select_sub_cate = $GLOBALS['db']->getOne("select cache_deal_cate_type_id from ".DB_PREFIX."deal_submit where id = ".$id);
            $sub_cate_arr = unserialize($select_sub_cate);
        
        }
        
        //处理选择状态
        foreach ($sub_cate_list as $k=>$v){
            if(in_array($v['id'], $sub_cate_arr)){
                $sub_cate_list[$k]['checked'] =1 ;
            }
        }

		$this->assign("sub_cate_list",$sub_cate_list);
		
		if($sub_cate_list)
		$result['status'] = 1;
		else
		$result['status'] = 0;
		$result['html'] = $this->fetch();
		$this->ajaxReturn($result['html'],"",$result['status']);
	}
	
	function load_supplier_location()
	{
		$supplier_id = intval($_REQUEST['supplier_id']);
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type'])==0?1:intval($_REQUEST['edit_type']);
		
		$supplier_location_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id." and is_effect=1");
		if($edit_type == 1){ // 管理员提交数据
		    $select_location = $GLOBALS['db']->getAll("select location_id from ".DB_PREFIX."deal_location_link where deal_id = ".$id);
		    foreach ($select_location as $k=>$v){
		        $supplier_location_arr[] = $v['location_id'];
		    }
		}elseif ($edit_type == 2){ // 商户提交数据
		    $select_location = $GLOBALS['db']->getOne("select cache_location_id from ".DB_PREFIX."deal_submit where id = ".$id);
		    $supplier_location_arr = unserialize($select_location);
		}
		
		foreach($supplier_location_list as $k=>$v)
		{
		    if(in_array($v['id'], $supplier_location_arr)){
                $supplier_location_list[$k]['checked'] =1 ;
            }
			
		}

		$this->assign("supplier_location_list",$supplier_location_list);
		
		if($supplier_location_list)
		$result['status'] = 1;
		else
		$result['status'] = 0;
		$result['html'] = $this->fetch();
		$this->ajaxReturn($result['html'],"",$result['status']);
	}
	
	
	
	public function shop_publish()
	{
	    $admin_check_status = intval($_REQUEST['admin_check_status']);
	    if($admin_check_status==0||$admin_check_status==3){
	    	if($admin_check_status==3){
	    		$map['admin_check_status'] = 0;
	    	}
	    }else{
	    	$map['admin_check_status'] = $admin_check_status;
	    }
	    $status = intval($_REQUEST['status']);
	    $this->assign("status",$status);
	    if($status==0){
	    	$map['biz_apply_status']=array("in","1,2");
	    }else{
	    	$map['biz_apply_status']=array("in","3,4");
	    }
	    if(strim($_REQUEST['name'])!='')
	    {
	    	$map['name'] = array('like','%'.strim($_REQUEST['name']).'%');
	    }
	    $map['is_shop']=1;
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    $name="DealSubmit";
	    $model = D ($name);
	    if (! empty ( $model )) {
	        $this->_list ( $model, $map );
	    }
        $this->assign("page_title","商品审核");
	    $this->assign("show_status_check_btn",U("Deal/shop_publish",array("admin_check_status"=>0)));
	    $this->display ("publish");
	    return;
	}
	
	public function tuan_publish()
	{

		$admin_check_status = intval($_REQUEST['admin_check_status']);
	    if($admin_check_status==0||$admin_check_status==3){
	    	if($admin_check_status==3){
	    		$map['admin_check_status'] = 0;
	    	}
	    }else{
	    	$map['admin_check_status'] = $admin_check_status;
	    }
	    $status = intval($_REQUEST['status']);
	    $this->assign("status",$status);
	    if($status==0){
	    	$map['biz_apply_status']=array("in","1,2");
	    }else{
	    	$map['biz_apply_status']=array("in","3,4");
	    }
	    if(strim($_REQUEST['name'])!='')
	    {
	    	$map['name'] = array('like','%'.strim($_REQUEST['name']).'%');
	    }
	    $map['is_shop']=0;
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    $name="DealSubmit";
	    $model = D ($name);
	    if (! empty ( $model )) {
	        $this->_list ( $model, $map );
	    }
        $this->assign("page_title","团购审核");
	    $this->assign("show_status_check_btn",U("Deal/tuan_publish",array("admin_check_status"=>0)));
	    $this->display ("publish");
	    return;
	}
	
	public function refused_apply(){
	    $id = intval($_REQUEST['id']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    $this->assign("deal_submit_info",$deal_submit_info);
	    $this->display();
	}
	/**
	 * 拒绝商户申请
	 */
	public function do_refused_apply(){
	    $id = intval($_REQUEST['id']);
	    $memo = strim($_REQUEST['memo']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    if($deal_submit_info['admin_check_status'] == 0){
	        //更新商户表状态为拒绝
	        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("admin_check_status"=>2,"deal_submit_memo"=>$memo),"UPDATE","id=".$id);
// 	        $result['status'] = 1;
// 	        $result['info'] = "已经拒绝用户申请";
	        $this->success("已经拒绝用户申请");
	    }else{
// 	        $result['status'] = 0;
// 	        $result['info'] = "申请不存在";
	        $this->error("申请不存在");
	    }
// 	    ajax_return($result);
	}
	public function upline(){
	    $id = intval($_REQUEST['id']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    $this->assign("deal_submit_info",$deal_submit_info);
	    $this->display();
	}
	public function downline(){
	    $id = intval($_REQUEST['id']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    $this->assign("deal_submit_info",$deal_submit_info);
	    $this->display();
	}
	/**
	 * 上架申请（二次上架）
	 */
	public function do_upline(){
	    $id = intval($_REQUEST['id']);
	    $memo = strim($_REQUEST['memo']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    if($deal_submit_info && $deal_submit_info['biz_apply_status']==4){
	        //更新商户表状态为拒绝
	        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("admin_check_status"=>1,"deal_submit_memo"=>$memo),"UPDATE","id=".$id);
	        //更新团购数据表
	        $GLOBALS['db']->autoExecute(DB_PREFIX."deal",array("is_effect"=>1),"UPDATE","id=".$deal_submit_info['deal_id']);
// 	        $result['status'] = 1;
// 	        $result['info'] = "商品已经成功上架";
	        $this->success("商品已经成功上架");
	    }else{
// 	        $result['status'] = 0;
// 	        $result['info'] = "申请不存在";
	        $this->error("申请不存在");
	    }
// 	    ajax_return($result);
	}
	/**
	 * 下架申请
	 */
	public function do_downline(){
	    $id = intval($_REQUEST['id']);
	    $memo = strim($_REQUEST['memo']);
	    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
	    if($deal_submit_info && $deal_submit_info['biz_apply_status']==3){
	        //更新商户表状态为拒绝
	        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("admin_check_status"=>1,"deal_submit_memo"=>$memo),"UPDATE","id=".$id);
	        //更新团购数据表
	        $GLOBALS['db']->autoExecute(DB_PREFIX."deal",array("is_effect"=>0),"UPDATE","id=".$deal_submit_info['deal_id']);
// 	        $result['status'] = 1;
// 	        $result['info'] = "商品已经成功下架";
	        $this->success("商品已经成功下架");
	    }else{
// 	        $result['status'] = 0;
// 	        $result['info'] = "申请不存在";
	        $this->error("申请不存在");
	    }
// 	    ajax_return($result);
	}
	
	/**
	 * 商品列表
	 */
	public function ajaxGoodsList(){
		$keywords = strim($_REQUEST['keywords']);
		$currId	  = intval($_REQUEST['id']);
		$supplier_id  = intval($_REQUEST['supplier_id']);
		
		$where = array();
		//关联商品必须为普通商品(积分兑换商品和秒杀商品都不在列)
		$where['buy_type'] = 0;
		//只能在app端展示的除外
		$where['buyin_app'] = 0;
		//平台发布必须是物流配送的或者驿站的
        $where['delivery_type'] = array('in','1,3');
		$where['is_delete'] = 0;
		$where['publish_wait'] = 0;
        $where['is_delivery'] = 1;
		$where['is_shop'] = intval($_REQUEST['is_shop']);

 		if($keywords){
 			$where['name'] = array('like','%'.strim($keywords).'%');
 		}
		
 		$where['supplier_id'] = $supplier_id;
		if($currId >0 ){
			$where['id'] = array('neq',$currId);
		}
		
 		$list = array();
 		$m = M('Deal');
 		$count = $m->where($where)->count();

 		$pager = buildPage(MODULE_NAME.'/'.ACTION_NAME,$_REQUEST,$count,$this->page,10,U("Deal/ajaxGoodsList","page"));
 		if($count > 0){
 			$tmpList =  $m->where($where)->order('id desc')->limit($pager['limit'])->findAll();
 			foreach($tmpList as $item){
 				if(intval($where['is_shop'])==1){//商品
 					$durl = url("index","deal#".$item['id'],array('preview'=>1));
 				}else{//团购
 					$durl = url("index","preview#deal",array('id'=>$item['id'],'type'=>0));
 				}
 				$list[] = array(
 					'id'		=>	$item['id'],
 					'name'		=>	$item['name'],
 					'url'		=>	$durl,
 					'share_url'	=>	SITE_DOMAIN.$durl,
 					'img'		=>	$item['icon']
 				);
 			}
 		}
 		$this->assign('keywords',$keywords);
		$this->assign("list",$list);
		$this->assign('pager',$pager);
		$this->assign('currId',$currId);
		
		$this->display();
	}	
	
	public function brand_list(){
	    $cate_id=$_REQUEST['cate_id'];
        $cate=M("shop_cate");
        $name=$cate->where(array("id"=>$cate_id))->find();
        $name=$name['name'];
        
        $brand=M("brand");
        $brand_list=$brand->query("select * from ".DB_PREFIX."brand where FIND_IN_SET('".$name."',tag_match_row)");
	    
        echo json_encode($brand_list);
	}

    /**
     * @desc      根据id获取地址和模板详情的默认
     * @author    吴庆祥
     */
    public function get_carriage_detail(){
        $data=array();
        $carriage_template_id=$_REQUEST['id'];
        $carriage_template=$GLOBALS['db']->getRow("select id,name,valuation_type as type,cache_carriage_detail_data,province,area,city from ".DB_PREFIX."carriage_template where id=".$carriage_template_id);
        $address=$GLOBALS['db']->getCol("select name from ".DB_PREFIX."delivery_region where id in (".$carriage_template['province'].",".$carriage_template['city'].",".$carriage_template['area'].") order by id");
        $data['address']=implode("-",$address);
        $carriage_template_detail=unserialize($carriage_template['cache_carriage_detail_data']);
        foreach($carriage_template_detail as $key =>$val){
            if(!$val['region_ids']){
                $data['carriage_template_detail']=$val;
            }
        }
        if($carriage_template['type']==1){
            $data['delivery_info'] = '当前运费模版，按件计费';
        }else{
            $data['delivery_info'] = '当前运费模版，按物流重量（含包装）计费';
        }
        
        ajax_return($data);
    }
    
    
     public function syn_second_cate(){
         
        
         $cate_id=intval($_REQUEST['cate_id']);
         $is_shop=intval($_REQUEST['is_shop']);  //is_shop=0 为团购的二级分类，is_shop=1为商城的二级分类
         if($is_shop==1 && $cate_id > 0){
             $cate=M("ShopCate");
             $sub_cate_tree=$cate->where(array("pid"=>$cate_id,'is_delete'=>0,'is_effect'=>1))->findAll();
         }elseif($is_shop==0 && $cate_id > 0){

             $sql = 'select dct.*,dcl.cate_id as pid from '.DB_PREFIX.'deal_cate_type_link as dcl left join '.DB_PREFIX.'deal_cate_type as dct on dcl.deal_cate_type_id=dct.id  where dcl.cate_id = '.$cate_id ;
             $sub_cate_tree = $GLOBALS['db']->getAll($sql);
         } 

         $this->assign("sub_cate_tree",$sub_cate_tree);
         $this->display();
         
     }
     
     public function attr_table(){
         $attr_row_arr = $_REQUEST['attr_row_arr'];
         $deal_id = intval($_REQUEST['deal_id']);
         $is_show_attr=intval($_REQUEST['is_show_attr']);
         $edit_type=intval($_REQUEST['edit_type']);
         $supplier_id=intval($_REQUEST['supplier_id']);
         $publish_verify_balance=$_REQUEST['publish_verify_balance'];

         //当$attr_row_arr为空的时候且is_show_attr为1，根据订单id去查规格商品
         if($is_show_attr&&!$attr_row_arr){
             $edit_type=1;
             $temp_attr=array();
             $attr_row_arr=$GLOBALS['db']->getAll("select a.name as attr_name,a.is_checked as is_checked,a.id as deal_attr_id ,b.* from ".DB_PREFIX."deal_attr as a left join ".DB_PREFIX."goods_type_attr as b on a.goods_type_attr_id = b.id where a.deal_id=".$deal_id." order by a.id asc");
             foreach($attr_row_arr as $val){
                 $temp_attr[$val['name']][]=array('attr_name'=>$val['attr_name'],"key"=>$val['deal_attr_id']);
             }
             $attr_row_arr=array();
             foreach($temp_attr as $key=>$val){
                 $attr_row_arr[]=array('name'=>$key,'attr'=>$val);
             }
             $this->assign("is_show_attr",$is_show_attr);
         }
         
         if($attr_row_arr){

             $attr_row_count = count($attr_row_arr);
             if($attr_row_count == 0){
                 $html='';
             }else{
             $html='<table><tboty><tr>';
    
             foreach($attr_row_arr as $k=>$v){
                 $html .='<th>'.$v['name'].'</th>';
                 $attr_row_arr[$k]['count'] = count($v['attr']);
             }
             if($supplier_id>0){
                 $p_str='递增结算价';
             }else{
                 $p_str='递增成本价';
             }
             $html .='<th>递增销售价</th><th>'.$p_str.'</th><th>库存</th><th>销量</th></tr>';
             
             foreach($attr_row_arr as $k=>$v){
    
                 $span = 1;
                 for ($i=0;$i < $attr_row_count;$i++){
                     if($k < $i){
                         $span *= $attr_row_arr[$i]['count'];
                     }  
                 }
                 $attr_row_arr[$k]['span'] = $span;
             }
    
    
    		if($edit_type==1){
    			$attr_stock = M("AttrStock")->where("deal_id=".intval($deal_id))->order("id asc")->findAll();
    			foreach($attr_stock as $k=>$v)
    			{
    				$attr_stock[$k]['attr_cfg'] = unserialize($v['attr_cfg']);
    			}
    		}else{
    			$attr_stock =$GLOBALS['db']->getOne("select cache_attr_stock from ".DB_PREFIX."deal_submit where id=".$deal_id);
    			$attr_stock=unserialize($attr_stock);
    		}
    		
             
    
             require_once(APP_ROOT_PATH."system/model/dc.php");
             $attr_stock = data_format_idkey($attr_stock,$key='attr_key');
             //第一层
             foreach($attr_row_arr[0]['attr'] as $kk=>$vv){
                 $html .='<tr><td rowspan="'.$attr_row_arr[0]['span'].'">'.$vv['attr_name'].'</td>';
                 
                 
                 //第二层
                 if($attr_row_arr[1]['attr']){
                     
    
                 foreach($attr_row_arr[1]['attr'] as $kkk=>$vvv){
                     if($attr_row_arr[0]['span'] > 1 && $kkk > 0){
    
                         $html .='<tr><td rowspan="'.$attr_row_arr[1]['span'].'">'.$vvv['attr_name'].'</td>';
                         //第三层
                         if($attr_row_arr[2]['attr']){
                         foreach($attr_row_arr[2]['attr'] as $kkkk=>$vvvv){
                             if($attr_row_arr[1]['span'] > 1 && $kkkk > 0){
                                 $html .='<tr><td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                                    
                                 //第四层
                                 if($attr_row_arr[3]['attr']){
                                 foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                     if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                         $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                 
                                         $html .='</tr>';
                                     }else{
                                         $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                     }
                                      
                                 }
                                 }else{
    
                                     $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                     $key=  $this-> get_data_key($attr_stock,$key_arr);
    
                                     $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                                     $html .= $row_html;
    
                                 }
                                 $html .='</tr>';
                             }else{
                                 $html .='<td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                                 //第四层
                                 if($attr_row_arr[3]['attr']){
                                 foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                     if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                         $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                          
                                         $html .='</tr>';
                                     }else{
                                         $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                     }
                                 
                                 }
                                 }else{
                                 
                                     $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                     $key=  $this-> get_data_key($attr_stock,$key_arr);
                                      
                                     $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                                     $html .= $row_html;
                                     
                                 }
                             }
                         
                         }
                         }else{
    
                             $key_arr =array($vv['key'] , $vvv['key']);   
                             $key=  $this-> get_data_key($attr_stock,$key_arr);
                             
                             $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                             $html .= $row_html;
                             
                         }
                         
                         $html .='</tr>';
                     }else{
                         $html .='<td rowspan="'.$attr_row_arr[1]['span'].'">'.$vvv['attr_name'].'</td>';
                         //第三层
                         if($attr_row_arr[2]['attr']){
                         foreach($attr_row_arr[2]['attr'] as $kkkk=>$vvvv){
                             if($attr_row_arr[1]['span'] > 1 && $kkkk > 0){
                                 $html .='<tr><td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                                 //第四层
                                 if($attr_row_arr[3]['attr']){
                                 foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                     if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                         $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                          
                                         $html .='</tr>';
                                     }else{
                                         $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                     }
                                 
                                 }
                                 }else{
                                      
                                     $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                     $key=  $this-> get_data_key($attr_stock,$key_arr);
                                 
                                     $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                                     $html .= $row_html;
                                     
                                 }
                                 $html .='</tr>';
                             }else{
                                 $html .='<td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                                 
                                 //第四层
                                 if($attr_row_arr[3]['attr']){
                                 foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                     if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                         $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                          
                                         $html .='</tr>';
                                     }else{
                                         $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                     }
                                 
                                 }
                                 }else{
                                 
                                     $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                     $key=  $this-> get_data_key($attr_stock,$key_arr);
                                      
                                     $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                                     $html .= $row_html;
    
                                 }
                                
                             }
                              
                         }
                         }else{
                             
                             $key_arr =array($vv['key'] , $vvv['key']);
                             $key=  $this-> get_data_key($attr_stock,$key_arr);
    
                             $row_html = $this->get_attr_row_html($attr_stock,$key,$supplier_id,$publish_verify_balance);
                             $html .= $row_html;
                         }
                     }
    
                 }
                 }else{
                     $row_html = $this->get_attr_row_html($attr_stock,$vv['key'],$supplier_id,$publish_verify_balance);
                     $html .= $row_html;
                 }
                 $html .='</tr>';
             }
             
             
             $html .='</tboty></table>';
             }
         }else{
             $this->assign("is_show_attr",1);
             $html='';
         }
         $this->assign("supplier_id",$supplier_id);
         $this->assign("html",$html);
         $this->display();


          
     }
     
     
     public function get_data_key($data,$key_arr){
         if(count($key_arr)==1){
             return $key_arr[0];
         }elseif(count($key_arr)==2){
             
             if($data[$key_arr[0].'_'.$key_arr[1]]){
                 return $key_arr[0].'_'.$key_arr[1];
             }else{
                 return $key_arr[1].'_'.$key_arr[0];
             }
         }elseif(count($key_arr)==3){
             
             if($data[$key_arr[0].'_'.$key_arr[1].'_'.$key_arr[2]]){
                 return $key_arr[0].'_'.$key_arr[1].'_'.$key_arr[2];
             }elseif($data[$key_arr[0].'_'.$key_arr[2].'_'.$key_arr[1]]){
                 return $key_arr[0].'_'.$key_arr[2].'_'.$key_arr[1];
             }elseif($data[$key_arr[1].'_'.$key_arr[0].'_'.$key_arr[2]]){
                 return $key_arr[1].'_'.$key_arr[0].'_'.$key_arr[2];
             }elseif($data[$key_arr[1].'_'.$key_arr[2].'_'.$key_arr[0]]){
                 return $key_arr[1].'_'.$key_arr[2].'_'.$key_arr[0];
             }elseif($data[$key_arr[2].'_'.$key_arr[1].'_'.$key_arr[0]]){
                 return $key_arr[2].'_'.$key_arr[1].'_'.$key_arr[0];
             }elseif($data[$key_arr[2].'_'.$key_arr[0].'_'.$key_arr[1]]){
                 return $key_arr[2].'_'.$key_arr[0].'_'.$key_arr[1];
             }

         }
         
     }
     public function get_attr_json_data($deal_id){
         //输出规格库存的配置
         $attr_stock = M("AttrStock")->where("deal_id=".intval($deal_id))->order("id asc")->findAll();
         $attr_cfg_json = "{";
         $attr_stock_json = "{";
         
         foreach($attr_stock as $k=>$v)
         {
             $attr_cfg_json.=$k.":"."{";
             $attr_stock_json.=$k.":"."{";
             foreach($v as $key=>$vvv)
             {
                 if($key!='attr_cfg')
                     $attr_stock_json.="\"".$key."\":"."\"".$vvv."\",";
             }
             $attr_stock_json = substr($attr_stock_json,0,-1);
             $attr_stock_json.="},";
             	
             $attr_cfg_data = unserialize($v['attr_cfg']);
             foreach($attr_cfg_data as $attr_id=>$vv)
             {
                 $attr_cfg_json.=$attr_id.":"."\"".$vv."\",";
             }
             $attr_cfg_json = substr($attr_cfg_json,0,-1);
             $attr_cfg_json.="},";
         }
         if($attr_stock)
         {
             $attr_cfg_json = substr($attr_cfg_json,0,-1);
             $attr_stock_json = substr($attr_stock_json,0,-1);
         }
         
         $attr_cfg_json .= "}";
         $attr_stock_json .= "}";
         
         $result['attr_cfg_json'] = $attr_cfg_json ;
         $result['attr_stock_json'] = $attr_stock_json ;
         return $result;
     }
    public function show_attr_stock(){
        $id=$_REQUEST['id'];
        $result=$this->get_attr_json_data($id);
    }
    
    public function syn_publish_verify_balance(){
        $supplier_id=intval($_REQUEST['supplier_id']);
        $result = array();
        if($supplier_id > 0){
            $publish_verify_balance = $GLOBALS['db']->getOne("select publish_verify_balance from ".DB_PREFIX."supplier where id=".$supplier_id);          
            
            $result['status']=1;
            $result['publish_verify_balance']=$publish_verify_balance * 100;

        }else{
            $result['status']=0;
        }
        ajax_return($result);
    }
    
    public function get_shop_brand(){
        $shop_cate_id = $_REQUEST['shop_cate_id'];
        $cate1=M("shop_cate");
        $shop_cate=$cate1->where('id in ('.$shop_cate_id.')')->findAll();
             
        $brand1=M("brand");
        $brand_all = array();
        $brand_all_id = array();
        foreach ($shop_cate as $k=>$v){
            $brand_name=$v['name'];
            $brand_list=$brand1->query("select * from ".DB_PREFIX."brand where FIND_IN_SET('".$brand_name."',tag_match_row)");
            foreach($brand_list as $kk=>$brand){
                if(!in_array($brand['id'],$brand_all_id)){
                    $brand_all[] = $brand;
                    $brand_all_id[] = $brand['id'];
                }
            }
        }
        $this->assign("brand_list",$brand_all);
        $this->display();
    }

    public function get_attr_row_html($attr_stock , $key,$supplier_id,$publish_verify_balance){
        $html = '' ;
        $html .='<td><input style="width:100px;" type="text" class="pricebox" name="deal_attr_price[]" value="'.$attr_stock[$key]['price'].'" /></td>';
      

        if($supplier_id >0){
            $add_balance_price = $publish_verify_balance * $attr_stock[$key]['price'] / 100;
            $html .='<td><input type="hidden" name="deal_add_balance_price[]" value="'.$add_balance_price.'" /><span class="balance_item">'.$add_balance_price.'</span></td>';
             
        }else{
            $html .='<td><input style="width:100px;" type="text" class="pricebox" name="deal_add_balance_price[]" value="'.$attr_stock[$key]['add_balance_price'].'" /></td>';
             
        }
        
        
        $html .='<td><input style="width:100px;"  type="text" name="stock_cfg_num[]" value="'.$attr_stock[$key]['stock_cfg'].'" /></td>';
        $html .='<td><input type="hidden" name="stock_buy_count[]" value="'.$attr_stock[$key]['buy_count'].'" />'.intval($attr_stock[$key]['buy_count']).'</td>';
        return $html;
    }
    private function tuan_cate_pid($cate_id_str,$second_id_str){
        $sql = " SELECT cate_id FROM ".DB_PREFIX."deal_cate_type_link where deal_cate_type_id in (".$second_id_str.")";
        $second_cate_pid = $GLOBALS['db']->getAll($sql);
        if($cate_id_str!=''){
            $cate_arr=explode(',',$cate_id_str);
        }else{
            $cate_arr=array();
        }
        foreach($second_cate_pid as $v){
            if(!in_array($v['cate_id'],$cate_arr)){
                $cate_arr[]=$v['cate_id'];
            }
        }
        if(count($cate_arr)>0){
            $cate_id_str=implode(',',$cate_arr);
        }else{
            $cate_id_str="";
        }
        return $cate_id_str;
    }
}