<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


require_once(APP_ROOT_PATH.'system/model/user.php');
class uc_fxModule extends MainBaseModule
{
	public function index()
	{
		 app_redirect(url("index","uc_fx#my_fx"));
	}
	
	
	/**
	 * 我的推广
	 */
	public function my_fx(){
	    global_run();
	    if(check_save_login()!=LOGIN_STATUS_LOGINED)
	    {
	        app_redirect(url("index","user#login"));
	    }
	    init_app_page();
	    $user_info = $GLOBALS['user_info'];
	    
        if($user_info['is_fx']==0){
            app_redirect(url("index","uc_fx#vip_buy"));
        }
        
	    $u_level = array();
		if($user_info['fx_level']>0){
		    //取出等级信息
		    $u_level = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."fx_level where id = ".$user_info['fx_level']);

		    //独立出用户等级数据
		    $salarys = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where  fx_level=0 and level_id=".$u_level['id']);
		}
		$u_level = $u_level?array_merge($u_level,$salarys):array();


		//默认佣金
		$default_salary = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where level_id = 0 and fx_level=0");
		if (empty($u_level)){  //无等级的情况下取默认
		    $u_level = $default_salary;
		}
            
        //获取我的分销数据
        require_once(APP_ROOT_PATH."app/Lib/page.php");

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		require_once(APP_ROOT_PATH.'system/model/deal.php');
		
                
        //获取用户已经分销的数据
        require_once(APP_ROOT_PATH."system/model/deal.php");		
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;		
		$page_size = app_conf("DEAL_PAGE_SIZE");
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");

        $join = " left join ".DB_PREFIX."user_deal as ud on d.id = ud.deal_id ";
        $join .= ' left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id ';
        //$ext_condition = " d.buy_type <> 1  and d.is_fx = 2 and ud.user_id = ".$user_info['id'];
		 $ext_condition = " d.buy_type <> 1  and (d.is_fx=1 or ( d.is_fx = 2 and ud.user_id = ".$user_info['id'].")) GROUP BY d.id ";
        $sort_field = " ud.is_effect desc,ud.add_time desc ";
        $append_field = " ,ud.sale_count,ud.sale_total,ud.sale_balance,s.name as supplier_name,ud.is_effect as ud_is_effect,ud.type as ud_type ";
        $deal_result  = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$ext_condition,$sort_field,$append_field);

        $deal_list = $deal_result['list'];
        $total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d ".$join." where ".$deal_result['condition'],false);

        	
        $page = new Page($total,$page_size);   //初始化分页对象
        $p  =  $page->show();
        $GLOBALS['tmpl']->assign('pages',$p);
        
        
        //获取ID
        foreach ($deal_list as $k=>$v){
            $ids[] = $v['id'];
        }

        //佣金比率
        $deal_salarys = $GLOBALS['db']->getAll('select * from '.DB_PREFIX.'deal_fx_salary where deal_id in('.implode(',', $ids).') and fx_level=0 ');
        foreach ($deal_salarys as $k=>$v){
            if($v['is_fx']==1){
                $v['sale_count']="";
                $v['sale_total']='';
                $v['sale_balance']='';
                $v['ud_is_effect']='';
                $v['ud_type']='';
            }
            if ($v['fx_salary']==0){    //如果商品没设置 分销佣金比率，默认为用户当前的
                $v['fx_salary'] = $u_level['fx_salary'];
                $v['fx_salary_type'] = $u_level['fx_salary_type'];
            }else{
                $v['fx_salary'] = $v['fx_salary'];
            }
            
            $f_deal_salarys[$v['deal_id']] = $v;
        }		
        
		foreach ($deal_list as $K=>$v){
		    $temp_data['id'] = $v['id'];
		    $temp_data['name'] = $v['name'];
		    $temp_data['sub_name'] = $v['sub_name'];
		    $temp_data['icon'] = $v['icon'];
		    $temp_data['current_price'] = round($v['current_price'],2);
		    $temp_data['buy_count'] = $v['buy_count'];
		    
		    $deal_salary = $f_deal_salarys[$v['id']]['fx_salary'];
		    $fx_salary_type = $f_deal_salarys[$v['id']]['fx_salary_type'];
		    
		    $temp_data['fx_salary'] = $fx_salary_type?round($deal_salary*100,2):round($deal_salary,2);
		    $temp_data['fx_salary_type'] = $fx_salary_type;
		    $temp_data['fx_salary_money'] = $fx_salary_type?round($deal_salary*$temp_data['current_price'],2):round($deal_salary,2);
		    
		    //用户获得的佣金信息
		    $temp_data['sale_count'] = $v['sale_count'];
		    $temp_data['sale_total'] = round($v['sale_total'],2);
		    $temp_data['sale_balance'] = round($v['sale_balance'],2);
		    
		    $temp_data['url'] = url("index","deal#".$v['id'],array("r"=>base64_encode($user_info['id'])));
		    $temp_data['share_url'] = SITE_DOMAIN.$temp_data['url'];
            $temp_data['ud_is_effect'] = $v['ud_is_effect'];
            $temp_data['ud_type'] = $v['ud_type'];
            $temp_data['supplier_name'] = $v['supplier_name'];
            $temp_data['stores_url'] = url("index","stores#index",array("supplier_id"=>$v['supplier_id'],"r"=>base64_encode($user_info['id'])));

            if($v['end_time']<= NOW_TIME && $v['end_time']!=0){
                $temp_data['end_status'] = '已过期';
            }elseif(($v['begin_time']<= NOW_TIME || $v['begin_time']==0) && ($v['end_time']> NOW_TIME || $v['end_time']==0)){
                $temp_data['end_status'] = '进行中';
            }elseif($v['begin_time']> NOW_TIME && $v['begin_time']!=0){
                $temp_data['end_status'] = '预告中';
            }
            $list[] = $temp_data;
		}
		if($u_level['fx_salary_type']==1){
			$u_level['fx_salary'] = round($u_level['fx_salary']*100,2);
		}
		$GLOBALS['tmpl']->assign("list",$list);

	    $GLOBALS['tmpl']->assign("user_info",$user_info);
	    $GLOBALS['tmpl']->assign("u_level",$u_level);
	    $GLOBALS['tmpl']->assign("n_level",$n_level);

        $GLOBALS['tmpl']->assign('qrcode_url',url("index","uc_home#mall",array("r"=>base64_encode($user_info['id']))));
	    //通用模版参数定义
	    assign_uc_nav_list();//左侧导航菜单
	    $GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
	    $GLOBALS['tmpl']->assign("page_title","我的推广"); //title
	    $GLOBALS['tmpl']->display("uc/uc_fx_my_fx.html"); 
	}
	
	/**
	 * 单品推广
	 */
	public function deal_fx()
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
		init_app_page();
		$user_info = $GLOBALS['user_info'];
		
		if($user_info['is_fx']==0){
		    app_redirect(url("index","uc_fx#vip_buy"));
		}
		
		$u_level = array();
		if($user_info['fx_level']>0){
		    //取出等级信息
		    $u_level = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."fx_level where id = ".$user_info['fx_level']);

		    //独立出用户等级数据
		    $salarys = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where  fx_level=0 and level_id=".$u_level['id']);
		}
		$u_level = $u_level?array_merge($u_level,$salarys):array();


		//默认佣金
		$default_salary = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where level_id = 0 and fx_level=0");
		if (empty($u_level)){  //无等级的情况下取默认
		    $u_level = $default_salary;
		}
		
		
		require_once(APP_ROOT_PATH."app/Lib/page.php");

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		require_once(APP_ROOT_PATH.'system/model/deal.php');
		
		$s_deal_name = strim($_REQUEST['fx_deal_search']);
		$condition = '';
		if($s_deal_name !='')
		{
		    $condition = " and d.name like '%".$s_deal_name."%'";
		}
                
        //获取用户已经分销的数据
        $cur_deals = $GLOBALS['db']->getAll('select deal_id from '.DB_PREFIX.'user_deal where user_id = '.$user_info['id']);
        if($cur_deals){
            foreach ($cur_deals as $v){
                $not_deal_ids[] = $v['deal_id'];
            }
            $condition .= " and d.id not in (".  implode(",", $not_deal_ids).") ";
        }
                
        $join = ' left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id ';
        $ext_condition = " d.is_fx=2 and d.buy_type <> 1  ".$condition;
        $field = ' ,s.name as supplier_name';
        $orderby =' d.id desc ';
		$result = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$ext_condition,$orderby,$field);

		$count = $GLOBALS['db']->getOne('select count(id) from '.DB_PREFIX.'deal d where '.$result['condition']);

		$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
        //获取ID
        foreach ($result['list'] as $k=>$v){
            $ids[] = $v['id'];
        }

        //佣金比率
        $deal_salarys = $GLOBALS['db']->getAll('select * from '.DB_PREFIX.'deal_fx_salary where deal_id in('.implode(',', $ids).') and fx_level=0 ');
        

        foreach ($deal_salarys as $k=>$v){
            if ($v['fx_salary']==0){    //如果商品没设置 分销佣金比率，默认为用户当前的
                $v['fx_salary'] = $u_level['fx_salary'];
                $v['fx_salary_type'] = $u_level['fx_salary_type'];
            }
            $f_deal_salarys[$v['deal_id']] = $v;
        }		
  
		foreach ($result['list'] as $K=>$v){
		    $deal_salary = '';
		    $fx_salary_type = '';
		    
		    $temp_data['id'] = $v['id'];
		    $temp_data['name'] = $v['name'];
		    $temp_data['sub_name'] = $v['sub_name'];
		    $temp_data['icon'] = $v['icon'];
		    $temp_data['current_price'] = round($v['current_price'],2);
		    $temp_data['supplier_name'] = $v['supplier_name'];
		    $temp_data['buy_count'] = $v['buy_count'];
		  
		    $deal_salary = $f_deal_salarys[$v['id']]['fx_salary'];
		    $fx_salary_type = $f_deal_salarys[$v['id']]['fx_salary_type'];

		    $temp_data['fx_salary'] = $fx_salary_type?round($deal_salary*100,2):round($deal_salary,2);
		    $temp_data['fx_salary_type'] = $fx_salary_type;
		    $temp_data['fx_salary_money'] = $fx_salary_type?round($deal_salary*$temp_data['current_price'],2):round($deal_salary,2);
		    $temp_data['url'] = url("index","deal#".$v['id'],array("r"=>base64_encode($user_info['id'])));
		    $temp_data['share_url'] = SITE_DOMAIN.$temp_data['url'];
		    $temp_data['stores_url'] = url("index","stores#index",array("supplier_id"=>$v['supplier_id'],"r"=>base64_encode($user_info['id'])));
            $list[] = $temp_data;
		}
		
		
		$GLOBALS['tmpl']->assign("list",$list);
		
		$GLOBALS['tmpl']->assign('s_deal_name',$s_deal_name);
		
        $GLOBALS['tmpl']->assign('qrcode_url',url("index","uc_home#mall",array("r"=>base64_encode($user_info['id']))));
		//通用模版参数定义
		assign_uc_nav_list();//左侧导航菜单
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		$GLOBALS['tmpl']->assign("page_title","单品分销"); //title
		$GLOBALS['tmpl']->display("uc/uc_fx_deal_fx.html"); //title
	}
        
        public function add_fx_deal(){
            global_run();
            if(check_save_login()!=LOGIN_STATUS_LOGINED)
            {
                $result['status']=1000;
                $result['info'] = "用户未登录";
                ajax_return($result);
            }
            if($GLOBALS['user_info']['is_fx']==0){
                $result['status']=0;
                $result['info'] = "请先购买分销资格";
                $result['jump'] = url('index','uc_fx#buy_vip');
                ajax_return($result);
            }
            require_once(APP_ROOT_PATH.'system/model/fx.php');
            $user_info =  $GLOBALS['user_info'];
            $user_id = $user_info['id'];
            $deal_id = intval($_REQUEST['deal_id']);
            if($user_info['is_fx']==0){
                app_redirect(url("index","uc_fx#vip_buy"));
            }
            
            if(add_user_fx_deal($user_id, $deal_id)){
                $result['status']=1;
                $result['info'] = "添加成功";
                $result['jump'] = url('index','uc_fx#deal_fx');
            }else{
                $result['status'] = 0;
                $result['info'] = "添加失败";
            }
            ajax_return($result);
        }
	
        public function do_is_effect(){
            global_run();
            if(check_save_login()!=LOGIN_STATUS_LOGINED)
            {
                $result['status']=1000;
                $result['info'] = "用户未登录";
                ajax_return($result);
            }
            
            if($GLOBALS['user_info']['is_fx']==0){
                $result['status']=0;
                $result['info']="您还未购买分销资格会员";
                $result['jump'] = url('index','uc_fx#buy_vip');
                ajax_return($result);
            }
            
            require_once(APP_ROOT_PATH.'system/model/fx.php');
            $user_id = $GLOBALS['user_info']['id'];
            $deal_id = intval($_REQUEST['deal_id']);
            if(do_is_effect($user_id, $deal_id)){
                $result['status']=1;
                $result['info'] = "操作成功";
                $result['jump'] = url('index','uc_fx#my_fx');
            }else{
                $result['status'] = 0;
                $result['info'] = "操作失败";
            }
            ajax_return($result);
        }
        
        public function del_user_deal(){
            global_run();
            if(check_save_login()!=LOGIN_STATUS_LOGINED)
            {
                $result['status']=1000;
                $result['info'] = "用户未登录";
                ajax_return($result);
            }
            
            if($GLOBALS['user_info']['is_fx']==0){
                $result['status']=0;
                $result['info']="您还未购买分销资格会员";
                $result['jump'] = url('index','uc_fx#buy_vip');
                ajax_return($result);
            }
            
            require_once(APP_ROOT_PATH.'system/model/fx.php');
            $user_id = $GLOBALS['user_info']['id'];
            $deal_id = intval($_REQUEST['deal_id']);
            if(del_user_deal($user_id, $deal_id)){
                $result['status']=1;
                $result['info'] = "删除成功";
                $result['jump'] = url('index','uc_fx#my_fx');
            }else{
                $result['status'] = 0;
                $result['info'] = "删除失败";
            }
            ajax_return($result);
        }


    /**
     * 分销资格购买
     */
    public function vip_buy()
    {
        global_run();
        init_app_page();
        assign_uc_nav_list();//左侧导航菜单
        
        $data = array();
        $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	     
	    if(check_save_login()!=LOGIN_STATUS_LOGINED){
	        app_redirect(url("index","user#login"));
	    }
	    if($user['mobile']==""){
	        app_redirect(url("index","uc_account"));
	    }
	    if($user['is_fx']){
	        app_redirect(url("index","uc_fx#my_fx"));
	    }
        $data['id']=$user_id;
        $data['is_fx']=$user['is_fx'];
        
        $fx_buy=$GLOBALS['db']->getRow("select id,pay_fee,pay_agreement,pc_privilege from ".DB_PREFIX."fx_qualification");
        if($fx_buy['pay_fee']){
            $fx_buy['pay_fee']=round($fx_buy['pay_fee'],2);
        }
        $data['fx_buy']=$fx_buy;         
	   //print_r($data);exit;
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->assign("ajax_url",url("index","uc_fx#make_order"));
        $GLOBALS['tmpl']->display("uc/uc_fx_vip_buy.html"); //title
    }
    
    /** 
     *生成分销资格购买订单 
     * */
    public function make_order() {
        $result = array();
        global_run();
    
        $user_info=$GLOBALS['user_info'];
        $user_id=$user_info['id'];
        
        if(check_save_login()!=LOGIN_STATUS_LOGINED){
	        app_redirect(url("index","user#login"));
	    }
        else{
            $result['is_fx']=$user_info['is_fx'];
            
            if($user_info['mobile']=="")
            {
                $data['status'] = 0;
                $data['info'] = "请先完善会员的手机号码";
                $data['jump'] = url("index","uc_account");
                ajax_return($data);
            }
            
            if($user_info['is_fx']){
                $result['status']=0;
                $result['info']="您已购买过分销资格会员";
                $result['jump'] = url('index','uc_fx#my_fx');
                ajax_return($result);
            }
            else{
                $fx_buy_info=$GLOBALS['db']->getRow("select id,name,pay_fee,tz_award,fx_award from ".DB_PREFIX."fx_qualification");
                 
                $GLOBALS['db']->query("delete from ".DB_PREFIX."fx_buy_order where user_id=".$user_id);
                
                //生成订单数据
                $order_data = array();
                $order_data['create_time'] = NOW_TIME ;
                $order_data['order_status'] =  0;
                $order_data['user_id'] = $user_id ;
                $order_data['pay_status'] = 0 ;
                 
                if($fx_buy_info['pay_fee']){
                    //付费开通
                    $order_data['total_price'] =  $fx_buy_info['pay_fee'];
                    $order_data['fx_price'] =  $fx_buy_info['pay_fee'];
                     
                    //计算返利
                    if($user_info['pid']){
                        $is_rebate=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where id=".$user_info['pid']." and is_fx=1");
                        if($is_rebate){
                            $rebate=round($fx_buy_info['pay_fee']*$fx_buy_info['tz_award'],2);
                            $order_data['rebate']=$rebate;
                            $fx_buy_info['pid'] = $user_info['pid'];
                            $fx_buy_info['rebate'] = $rebate;
                        }else{
                            $fx_buy_info['pid'] = $user_info['pid'];
                            $fx_buy_info['rebate'] = 0;

                        }
                    }
                   if($user_info['agency_id']){
                       $fx_price=round($fx_buy_info['pay_fee']*$fx_buy_info['fx_award'],2);
                       $order_data['fx_charge_price'] = $fx_price;
                       $fx_buy_info['fx_charge_price']=$fx_price;
                   }
                    $order_data['rebate_data'] = serialize($fx_buy_info);
                }
                 
                do
                {
                    $order_data['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
                    $GLOBALS['db']->autoExecute(DB_PREFIX."fx_buy_order",$order_data,'INSERT','','SILENT');
                    $order_id = intval($GLOBALS['db']->insert_id());
                }while($order_id==0);
                 
                $result['order_id'] = $order_id;
                 
                $is_free=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_buy_order where total_price=0 and id=".$order_id);
                 
                //免费开通，直接结单
                if($is_free){
                    require_once(APP_ROOT_PATH."system/model/cart.php");
                    $rs = fx_buy_order_paid($order_id);
                    $result['free']=1;
                    if($rs){
                        $result['is_open']=1;
                        $result['status'] = 1;
                        $result['info']='您已成功开通分销';
                        $result['jump']=url('index','uc_fx#payment_done');
                    }else {
                        $result['status'] = 0;
                        $result['info']='免费开通分销失败';
                    }
                    ajax_return($result);
                }
                
                $result['status'] = 1;
                $result['jump']=url('index','uc_fx#check',array("order_id"=>$order_id));
                ajax_return($result);
                 
            }
        }
    }
    
    /**
     * 分销资格支付页面
     *   */
    public function check(){
        global_run();
        init_app_page();
        assign_uc_nav_list();//左侧导航菜单
        
        $user_info=$GLOBALS['user_info'];
        
        $order_id=$_REQUEST['order_id'];
        
        if(check_save_login()!=LOGIN_STATUS_LOGINED)
        {
            app_redirect(url("index","user#login"));
        }
        if($user_info['mobile']==""){
            app_redirect(url("index","uc_account"));
        }
        if($user_info['is_fx']){
            app_redirect(url("index","uc_fx#my_fx"));
        }
        
        $order_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where user_id=".$user_info['id']." and id=".$order_id);
        if(!$order_info){
            app_redirect(url("index","uc_fx#vip_buy"));
        }
        
        //开通费用
        $fee=$GLOBALS['db']->getOne("select pay_fee from ".DB_PREFIX."fx_qualification");
        if($fee){
            $fee=round($fee,2);
        }
        
        $GLOBALS['tmpl']->assign("fee",$fee);
        
        
        //输出支付方式
		$payment_list = load_auto_cache("cache_payment");	

		$icon_paylist = array(); //用图标展示的支付方式
		$disp_paylist = array(); //特殊的支付方式(Voucher,Account,Otherpay)
		$bank_paylist = array(); //网银直连
		
		$wx_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Wwxjspay'");
		if($wx_payment)
		{
			$wx_payment['config'] = unserialize($wx_payment['config']);
			if($wx_payment['config']['scan']==1)
			{
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' .$wx_payment['class_name']."_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					$payment_class = $wx_payment['class_name']."_payment";
					$payment_object = new $payment_class();
					$wx_payment['display_code'] = $payment_object->get_web_display_code();
					$disp_paylist[] = $wx_payment;
				}
			}
		}
		
		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=="Account"||$v['class_name']=="Otherpay")
			{
				if($v['class_name']=="Account" && $user_info['money']>=$fee)
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();					
					}
					$disp_paylist[] = $v;
				}
				if($v['class_name']=="Otherpay"){
				    $disp_paylist[] = $v;
				}
			}
			else if($v['class_name']!="Voucher")
			{
				if($v['is_bank']==1)
				$bank_paylist[] = $v;	
				else
				$icon_paylist[] = $v;
			}
		}
	
		
		
		
		$GLOBALS['tmpl']->assign("icon_paylist",$icon_paylist);
		$GLOBALS['tmpl']->assign("disp_paylist",$disp_paylist);//支付方式
		$GLOBALS['tmpl']->assign("bank_paylist",$bank_paylist);
        
		$GLOBALS['tmpl']->assign("order_id",$order_id);
        $GLOBALS['tmpl']->assign("ajax_url",url("index","uc_fx#pay_do"));
        $GLOBALS['tmpl']->display("uc/uc_fx_buy_check.html"); //title
    }
    
    /**
     * 前往支付
     **/
    public function pay_do()
    {
        $result=array();
        global_run();
        init_app_page();
        if(check_save_login()!=LOGIN_STATUS_LOGINED)
        {
            $result['status']=0;
            $result['info']="请先登录";
            $result['jump']=url("index","user#login");
            ajax_return($result);
        }
    
        $user_info=$GLOBALS['user_info'];
        
        if($user_info['mobile']=="")
        {
            $data['status'] = 0;
            $data['info'] = "请先完善会员的手机号码";
            $data['jump'] = url("index","uc_account");
            ajax_return($data);
        }
        
        if($user_info['is_fx']){
            $result['status']=0;
            $result['info']="您已购买过分销资格";
            $result['jump']=url("index","uc_fx#my_fx");
            ajax_return($result);
        }
        
        $payment_id = intval($_REQUEST['payment']);
        $bank_id=strim($_REQUEST['bank_id']);
        $order_id=intval($_REQUEST['order_id']);
        $all_account_money=intval($_REQUEST['all_account_money']);
        
        $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_id);
        if(!$payment_info && $all_account_money==0)
        {
            $result['status']=0;
            $result['info']="请选择支付方式";
            ajax_return($result);
        }
        
        $order_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where user_id=".$user_info['id']." and id=".$order_id);
        
        if(!$order_info){
            $result['status']=0;
            $result['info']="订单不存在";
            $result['jump']=url("index","uc_fx#vip_buy");
            ajax_return($result);
        }

        if($order_info['pay_status']==2){
            $result['status']=0;
            $result['info']="订单已支付";
            $result['jump']=url("index","uc_fx#my_fx");
            ajax_return($result);
        }
        
        require_once(APP_ROOT_PATH.'system/model/fx.php');
        
        $data = fx_pay_total($order_info['total_price']-$order_info['payment_fee'], $payment_id,$bank_id,0,$all_account_money,$order_info['user_id'],$order_id);
        
        if($data['pay_price']>0 && empty($data['payment_info']))
        {
            $result['status']=0;
            $result['info']="请选择支付方式";
            ajax_return($result);
        }
    
        $now = NOW_TIME;
        
        $order['payment_id'] = $payment_id;
        $order['bank_id'] = $bank_id;
        $order['payment_fee'] = $data['payment_fee'];
        $total_price=$order_info['total_price']-$order_info['payment_fee'];
        $order['total_price'] = $total_price+$order['payment_fee'];
        $GLOBALS['db']->autoExecute(DB_PREFIX."fx_buy_order",$order,'UPDATE','id='.$order_id,'SILENT');
        
        require_once(APP_ROOT_PATH."system/model/cart.php");
        
        $account_money=$data['account_money'];
        require_once(APP_ROOT_PATH.'system/model/cart.php');
        //1. 余额支付
        $account_pid = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
        if(floatval($account_money) > 0 && $all_account_money==1)
        {
            $payment_notice_id = make_fx_pay_payment_notice($account_money,$order_id,$account_pid);
            require_once(APP_ROOT_PATH."system/payment/Account_payment.php");
            $account_payment = new Account_payment();
            $account_payment->get_payment_code($payment_notice_id);
        }
        
        
        //3. 相应的支付接口
        $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".$payment_id);
        if($payment_info && $data['pay_price']> 0)
        {
            $payment_notice_id = make_fx_pay_payment_notice($data['pay_price'],$order_id,$payment_info['id']);
            //创建支付接口的付款单
        }
        
        $rs = fx_buy_order_paid($order_id);
        
        if($rs){
            //正常支付，支付完成        
            $result['status']=1;
            $result['order_id'] = $order_id;
            $result['info']="支付完成";
            $result['jump']=url("index","uc_fx#payment_done",array("id"=>$order_id));
            ajax_return($result);
            
        }
        else
        {
            $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."store_pay_order where id = ".$order_id);
            if($order_info['pay_status'] == 2)
            {   //付款单号重复支付,当前支付的退到会员帐户
        
                $result['status']=0;
                $result['order_id'] = $order_id;
                $result['info']="付款单号重复支付";
                $result['jump']=url("index","uc_log#money");
                ajax_return($result);
                
            }else{ //正常支付，还有部分未完成
                
                $result['status']=1;
                $result['order_id'] = $order_id;
                $result['info']="去支付";
                $result['jump']=url("index","payment#pay",array("id"=>$payment_notice_id));
                ajax_return($result);
        
            }
            
        }
    }
    
    public function payment_done(){
        global_run();
        init_app_page();
        $GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
        $order_id = intval($_REQUEST['id']);
        
        if(check_save_login()!=LOGIN_STATUS_LOGINED)
        {
            app_redirect(url("index","user#login"));
        }
        
        $user_info=$GLOBALS['user_info'];
        
        if($user_info['is_fx']==0){
            app_redirect(url("index","uc_fx#vip_buy"));
        }
        
        $GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_SUCCESS']);
        $GLOBALS['tmpl']->display("uc/uc_fx_payment_done.html"); //title
    }
    
}
?>