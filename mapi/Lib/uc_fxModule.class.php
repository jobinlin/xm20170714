<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class uc_fxApiModule extends MainBaseApiModule
{
    
    
    /**
     * 我的分销接口
     * 输入：
     * page [int] 分页
     * 
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * 

        [user_data] => Array  ：array 用户头部数据
        (
            [user_name] => fanwe    ：string 用户名
            [fx_money] => 10        ：float  总佣金
            [user_avatar] => http://localhost/o2onew/public/avatar/000/00/00/71virtual_avatar_big.jpg       ：string 用户头像    页面上限定 85*85
            [share_mall_qrcode] => http://localhost/o2onew/public/images/qrcode/c8/8a32058ff72bd95e5cb1c7c74e5a80d3.png      ：string 我的小店  二维码图片
            [share_mall_url] =>
            [fx_mall_bg] => http://localhost/o2onew/public/attachment/201506/16/15/nofxm23fs_740x300.jpg    ：string 我的分销背景图片    360*150
        )

        [item] => Array ：array 我分销的商品数据列表
            (
                [0] => Array
                    (
                        [id] => 95  
                        [name] => 0元抽奖
                        [sub_name] => 0元抽奖
                        [icon] => http://localhost/o2onew/public/attachment/201504/03/16/551e4a76556ad_170x170.jpg      :string 商品图片 85*85
                        [current_price] => 0    ：float  当前价格
                        [sale_count] => 10      ：int  销量
                        [sale_total] => 1   ：float    总销售额
                        [sale_balance] => 0  ：float  分销商品获得的总佣金
                        [share_url] => http://localhost/o2onew/index.php?ctl=deal&act=95&r=NzE%3D   ：string 分享的商品连接
                        [ud_is_effect] => 1 ：int 是否上架 1 上架   ，0 下架
                        [ud_type] => 0  ：int 分销商品类型  0用户领取，1为系统分配
                        [end_status] => 0   ：int 商品 结束类型
                    )

     *
     * */
    public function my_fx(){
		$root = array();		
		/*参数初始化*/
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			

		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;	
		}
		else
		{
			$root['user_login_status'] = $user_login_status;
			$GLOBALS['ref_uid'] = $user_id;
			$root['is_fx']=$user['is_fx'];
			if($user['is_fx']==1){
    			//返回会员信息
    			$user_data = array();
    			$user_data['user_name'] = $user['user_name'];
    			$user_data['fx_money'] = round($user['fx_money'],2);
    			$user_data['user_avatar'] = get_abs_img_root(get_muser_avatar($user_id,"big"))?get_abs_img_root(get_muser_avatar($user_id,"big")):"";
    			$user_data['share_mall_qrcode'] = get_abs_img_root(gen_qrcode(SITE_DOMAIN.wap_url("index","uc_fx#mall",array("r"=>base64_encode($user_id)))));
    			$user_data['share_mall_url'] = SITE_DOMAIN.wap_url("index","uc_fx#mall",array("r"=>base64_encode($user_id)));
    				
    			$user_data['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($user['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";
    			if($user['pid']){
    			    $user_data['pname']=$GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$user['pid']);
    			}
    			
    			$root['user_data'] = $user_data;
    			
    			
    			$u_level = array();
    			if($user['fx_level']>0){
    			    //取出等级信息
    			    $u_level = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."fx_level where id = ".$user['fx_level']);
    			
    			    $root['u_level']=$u_level['name'];
    			    
    			    //独立出用户等级数据
    			    $salarys = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where  fx_level=0 and level_id=".$u_level['id']);
    			}
    			$u_level = $u_level?array_merge($u_level,$salarys):array();
    			
    			
    			//默认佣金
    			$default_salary = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where level_id = 0 and fx_level=0");
    			if (empty($u_level)){  //无等级的情况下取默认
    			    $u_level = $default_salary;
    			}
    			
    			
    			//分页
    			$page = intval($GLOBALS['request']['page']);
    			$page=$page==0?1:$page;
    				
    			$page_size = PAGE_SIZE;
    			$limit = (($page-1)*$page_size).",".$page_size;
    			
        		require_once(APP_ROOT_PATH.'system/model/deal.php');
        	
        		$join = " left join ".DB_PREFIX."user_deal as ud on d.id = ud.deal_id ";
                $ext_condition = " d.buy_type <> 1  and  (d.is_fx=1 or ( d.is_fx = 2 and ud.user_id = ".$user_id.")) GROUP BY d.id ";
                $sort_field = " ud.is_effect desc,ud.add_time desc ";
                $append_field = " ,ud.sale_count,ud.sale_total,ud.sale_balance,ud.is_effect as ud_is_effect,ud.type as ud_type ";
                $deal_result  = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$ext_condition,$sort_field,$append_field);
        		$deal_list = $deal_result['list'];
        		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d ".$join." where ".$deal_result['condition'],false);
    	
    
    			$page_total = ceil($count/$page_size);
    			//end 分页
    			
    			
    
    			
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
                    /*if ($v['fx_salary']==0){    //如果商品没设置 分销佣金比率，默认为用户当前的
                        $v['fx_salary'] = $u_level['fx_salary'];
                        $v['fx_salary_type'] = $u_level['fx_salary_type'];
                    }else{
                        $v['fx_salary'] = $v['fx_salary'];
                    }*/
                    
                    $f_deal_salarys[$v['deal_id']] = $v;
                }
                	
                //要返回的字段
                $data = array();
                
        		foreach ($deal_list as $K=>$v){
        		    $temp_data['id'] = $v['id'];
        		    $temp_data['name'] = msubstr($v['name'],0,40);
        		    $temp_data['sub_name'] = $v['sub_name'];
        		    $temp_data['icon'] = get_abs_img_root(get_spec_image($v['icon'],85,85,1));
        		    $temp_data['current_price'] = round($v['current_price'],2);
        		    
        		    $deal_salary = $f_deal_salarys[$v['id']]['fx_salary'];
        		    $fx_salary_type = $f_deal_salarys[$v['id']]['fx_salary_type'];
        		    
         		    $temp_data['fx_salary'] = $fx_salary_type?round($deal_salary*100,2):round($deal_salary,2);
         		    $temp_data['fx_salary_type'] = $fx_salary_type;
         		    $temp_data['fx_salary_money'] = $fx_salary_type?round($deal_salary*$temp_data['current_price'],2):round($deal_salary,2);
        		    
        		    //用户获得的佣金信息
        		    $temp_data['sale_count'] = $v['sale_count'];
        		    $temp_data['sale_total'] = round($v['sale_total'],2);
        		    $temp_data['sale_balance'] = round($v['sale_balance'],2);
        		    
        		    $temp_data['is_fx']=$v['is_fx'];
        		    $temp_data['ud_is_effect']=$v['ud_is_effect'];
        		    $temp_data['ud_type']=$v['ud_type'];
        		    
        		    $temp_data['buy_count']=$v['buy_count'];
                    $temp_data['ud_is_effect'] = intval($v['ud_is_effect']);
                    $temp_data['ud_type'] = $v['ud_type'];
                    
                    //分享
                    $temp_data['share_title']=$temp_data['name'];
                    $temp_data['share_img']=$temp_data['icon'];
                    $temp_data['share_url'] = SITE_DOMAIN.wap_url("index","deal",array("data_id"=>$v['id'],"r"=>base64_encode($user_id)));
                    $temp_data['share_content']=$v['brief'];
        

                    
                    if($v['end_time']<= NOW_TIME && $v['end_time']!=0){
                        $temp_data['end_status'] = 0;   //过期
                    }elseif(($v['begin_time']<= NOW_TIME || $v['begin_time']==0) && ($v['end_time']> NOW_TIME || $v['end_time']==0)){
                        $temp_data['end_status'] = 1;   //进行中
                    }elseif($v['begin_time']> NOW_TIME && $v['begin_time']!=0){
                        $temp_data['end_status'] = 2;   //预告
                    }
                    $data[$v['id']] = $temp_data;
        		}
        		$root['deal_json']=json_encode($data);
    			$root['item'] = $data;
    			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
			}
		}	

		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="我的分销";
		return output($root);
	}	
	
	/**
	 * 选取分销商品页面
	 * 输入：
	 * page [int] 分页
	 * fx_seach_key [string] 搜索条件  分销商品名称
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 *
	
	 [item] => Array ：array 我分销的商品数据列表
	 (
    	 [0] => Array
    	 (
        	 [id] => 95
        	 [name] => 0元抽奖
        	 [sub_name] => 0元抽奖
        	 [icon] => http://localhost/o2onew/public/attachment/201504/03/16/551e4a76556ad_170x170.jpg      :string 商品图片 85*85
        	 [current_price] => 0    ：float  当前价格
        	 [fx_salary] => 0.6      ：float  分销佣金比率或者金额
        	 [fx_salary_type] => 1   ：int    分销佣金的类型    0金额 1比率
        	 [fx_salary_money] => 0  ：float  分销商品可以获得的佣金
        	 [end_status] => 0   ：int 商品 结束类型
    	 )
     )
	
	 *
	 * */
	public function deal_fx(){
	    $root = array();
	    /*参数初始化*/
	
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        // 未登录
	    } elseif ($user['is_fx'] == 0) {
	    	$root['is_fx'] = 0;
	    } else {
	        $u_level = array();
	        if($user['fx_level']>0){
	            //取出等级信息
	            $u_level = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."fx_level where id = ".$user['fx_level']);
	            	
	            //独立出用户等级数据
	            $salarys = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where  fx_level=0 and level_id=".$u_level['id']);
	        }
	        $u_level = $u_level?array_merge($u_level,$salarys):array();
	        	
	        	
	        //默认佣金
	        $default_salary = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_salary where level_id = 0 and fx_level=0");
	        if (empty($u_level)){  //无等级的情况下取默认
	            $u_level = $default_salary;
	        }
	        	
	        
	        //分页
	        $page = intval($GLOBALS['request']['page']);
	        $page=$page==0?1:$page;
	
	        $page_size = PAGE_SIZE;
	        $limit = (($page-1)*$page_size).",".$page_size;
	        	
	        require_once(APP_ROOT_PATH.'system/model/deal.php');
	         
	        $s_deal_name = strim($GLOBALS['request']['fx_seach_key']);
	        $condition = '';
	        if($s_deal_name !='')
	        {
	            $condition = " and d.name like '%".$s_deal_name."%'";
	        }
	        
	        //获取用户已经分销的数据
	        $cur_deals = $GLOBALS['db']->getCol('select deal_id from '.DB_PREFIX.'user_deal where user_id = '.$user_id);

	        // 分销市场不显示已代理的商品
	        if($cur_deals){
	        	$condition .= ' and d.id not in ('.implode(',', $cur_deals).') ';
	            // foreach ($cur_deals as $v){
	            //     $not_deal_ids[] = $v['deal_id'];
	            // }
	            // $condition .= " and d.id not in (".  implode(",", $not_deal_ids).") ";
	        }

            $ext_condition = " d.is_fx = 2 and d.buy_type <> 1  ".$condition;

            $orderby =' d.id desc ';
    		$deal_result = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),'',$ext_condition,$orderby);
    
    		
	        $deal_list = $deal_result['list'];
	        $count = $GLOBALS['db']->getOne('select count(id) from '.DB_PREFIX.'deal d where '.$deal_result['condition'],false);
	
	
	        $page_total = ceil($count/$page_size);
	        //end 分页
	        	
	        //获取ID
	        foreach ($deal_list as $k=>$v){
	            $ids[] = $v['id'];
	        }
	
	        //佣金比率
	        if($ids){
    	        $deal_salarys = $GLOBALS['db']->getAll('select * from '.DB_PREFIX.'deal_fx_salary where deal_id in('.implode(',', $ids).') and fx_level=0 ');
        	    foreach ($deal_salarys as $k=>$v){
                    if ($v['fx_salary']==0){    //如果商品没设置 分销佣金比率，默认为用户当前的
                        $v['fx_salary'] = $u_level['fx_salary'];
                        $v['fx_salary_type'] = $u_level['fx_salary_type'];
                    }else{
                        $v['fx_salary'] = $v['fx_salary'];
                    }
                    
                    $f_deal_salarys[$v['deal_id']] = $v;
                }
	       }
	        //要返回的字段
	        $data = array();
	        foreach ($deal_list as $K=>$v){
	            $temp_data['id'] = $v['id'];
	            $temp_data['name'] = msubstr($v['name'],0,40);
	            $temp_data['sub_name'] = $v['sub_name'];
	            $temp_data['icon'] = get_abs_img_root(get_spec_image($v['icon'],85,85,1));
	            $temp_data['current_price'] = round($v['current_price'],2);
	            
	            $deal_salary = $f_deal_salarys[$v['id']]['fx_salary'];
	            $fx_salary_type = $f_deal_salarys[$v['id']]['fx_salary_type'];
	            
	            $temp_data['fx_salary'] = $fx_salary_type?round($deal_salary*100,2):round($deal_salary,2);
	            $temp_data['fx_salary_type'] = $fx_salary_type;
	            $temp_data['fx_salary_money'] = $fx_salary_type?round($deal_salary*$temp_data['current_price'],2):round($deal_salary,2);
	            
	            
	            $temp_data['ud_type'] = $v['ud_type'];
	
	            if($v['end_time']<= NOW_TIME && $v['end_time']!=0){
	                $temp_data['end_status'] = 0;   //过期
	            }elseif(($v['begin_time']<= NOW_TIME || $v['begin_time']==0) && ($v['end_time']> NOW_TIME || $v['end_time']==0)){
	                $temp_data['end_status'] = 1;   //进行中
	            }elseif($v['begin_time']> NOW_TIME && $v['begin_time']!=0){
	                $temp_data['end_status'] = 2;   //预告
	            }
	            
	            $temp_data['has_fx'] = in_array($v['id'], $cur_deals) ? 1 : 0;
	            $temp_data['is_fx'] = $v['is_fx'];
	            
	            $data[] = $temp_data;
	        }
	        $root['is_fx'] = 1;
	        $root['item'] = $data;
	        $root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
	
	    }
	    
	    $root['user_login_status'] = $user_login_status;
	
	  
	    // $root['page_title'].="逛市场";
	    $root['page_title'] = "分销市场";
	    return output($root);
	}
	
	/**
	 * 添加我的分销
	 * 输入：
	 * deal_id [int] 商品ID
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 *
	 status   int 状态
	 info     string 消息
	 *
	 * */
	public function add_user_fx_deal(){
	    $root = array();
	    /*参数初始化*/
	    $deal_id = intval($GLOBALS['request']['deal_id']);
	     
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	     
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	        require_once(APP_ROOT_PATH.'system/model/fx.php');
	        if(add_user_fx_deal($user_id, $deal_id)){
	            return output($root,1,"操作成功");
	        }else{
	            return output($root,0,"操作失败");
	        }
	    }
	    return output($root);
	}
	
	
	/**
     * 修改我的分销状态接口（上架、下架状态）
     * 输入：
     * deal_id [int] 商品ID
     * 
     * 输出：
     * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
     * 
       status   int 状态
       info     string 消息
     *
     * */
	public function do_is_effect(){
	    $root = array();
	    /*参数初始化*/
	    $deal_id = intval($GLOBALS['request']['deal_id']);
	    
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	        $fx_type=$GLOBALS['db']->getOne("select is_fx from ".DB_PREFIX."deal where id=".$deal_id);
	        if($fx_type==1){
	            return output($root,0,"系统分配，无法下架");
	        }
	        require_once(APP_ROOT_PATH.'system/model/fx.php');
	        if(do_is_effect($user_id, $deal_id)){
    	        return output($root,1,"操作成功");
    	    }else{
    	        return output($root,0,"操作失败");
    	    }
	    }
	    return output($root);
	}
	
	/**
	 * 删除我的分销
	 * 输入：
	 * deal_id [int] 商品ID
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 *
    	 status   int 状态
    	 info     string 消息
	 *
	 * */
	public function del_user_deal(){
	    $root = array();
	    /*参数初始化*/
	    $deal_id = intval($GLOBALS['request']['deal_id']);
	    
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
    	    require_once(APP_ROOT_PATH.'system/model/fx.php');
    	    if(del_user_deal($user_id, $deal_id)){
    	        return output($root,1,"操作成功");
    	    }else{
    	        return output($root,0,"操作失败");
    	    }
	    }
	    return output($root);
	}
    
	

	/**
	 * 我的小店
	 * 输入：
	 * page [int] 分页
	 * type [int] 0团购 1商城
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 *
	 [type] => 0     ：int 0团购 1商城
     [is_why] => 2   ：int 1 自己，2其它登录用户看，3未登录用户看
	 [user_data] => Array  ：array 用户头部数据
	 (
    	 [user_name] => fanwe    ：string 用户名
    	 [user_avatar] => http://localhost/o2onew/public/avatar/000/00/00/71virtual_avatar_big.jpg       ：string 用户头像    页面上限定 85*85
    	 [fx_mall_bg] => http://localhost/o2onew/public/attachment/201506/16/15/nofxm23fs_740x300.jpg    ：string 我的分销背景图片    360*150
	 )
	
	[deal_list] => Array
        (
            [0] => Array
                (
                    [id] => 68
                    [name] => 仅售228元！最高价值446元的希腊之旅套餐A/希腊之旅套餐B2选1，男女不限，提供免费WiFi。
                    [icon_157] => http://localhost/o2onew/public/attachment/201502/25/16/54ed8e6b70b46_314x314.jpg      ：string 商城显示图片
                    [icon_85] => http://localhost/o2onew/public/attachment/201502/25/16/54ed8e6b70b46_170x170.jpg       ：string 团购显示图片
                    [origin_price] => 446   ：float 原价
                    [current_price] => 228  ：float 现价
                )

        )
	
	 *
	 * */
    public function mall(){
	    $root = array();
	    /*参数初始化*/
	    $type = intval($GLOBALS['request']['type']); //0团购类 1商城类
	    $id = $GLOBALS['ref_uid']; //用户推荐ID
	    
	    
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    
	    $is_why = 0; //1 自己，2其它登录用户看，3未登录用户看
        if($id == $user_id)
        {
            $is_why = 1;
            $home_user_info = $user;
        }
        else
        {
            $is_why = 3;
            $home_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".$id);
            if($home_user_info){
                $is_why = 2;
            }
        }
        $root['deal_list'] = array();
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    
	    //返回会员信息
	    $user_data = array();
	    $user_data['user_name'] = $home_user_info['user_name'];
	    $user_data['user_avatar'] = get_abs_img_root(get_muser_avatar($home_user_info['id'],"big"))?get_abs_img_root(get_muser_avatar($home_user_info['id'],"big")):"";
	    $user_data['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($home_user_info['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";
	    
	    $root['user_data'] = $user_data;
	    
	        $root['user_login_status'] = $user_login_status;
    	    $root['type'] = $type;
    	    $root['is_why'] = $is_why;
    	    $root['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($user['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";

    	    //分页
    	    $page = intval($GLOBALS['request']['page']);
    	    $page=$page==0?1:$page;
    	    
    	    $page_size = PAGE_SIZE;
    	    $limit = (($page-1)*$page_size).",".$page_size;
    	    
    		require_once(APP_ROOT_PATH."system/model/deal.php");		
    		
    	
    		if($type==0)
    		{
    			$join = " left join ".DB_PREFIX."user_deal as ud on d.id = ud.deal_id and ud.user_id = ".$id." ";
    			$ext_condition = " d.buy_type <> 1 and d.is_shop = 0 and (d.is_fx = 1 or (d.is_fx = 2 and ud.is_effect = 1)) ";
    			$sort_field = " ud.add_time desc ";
    			$deal_result  = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array("city_id"=>$GLOBALS['city']['id']),$join,$ext_condition,$sort_field);
    			
    			$deal_list = $deal_result['list'];		
    			$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d ".$join." where ".$deal_result['condition'],false);
    
  
    		}
    		else
    		{
    			$join = " left join ".DB_PREFIX."user_deal as ud on d.id = ud.deal_id and ud.user_id = ".$id." ";
    			$ext_condition = " d.buy_type <> 1 and d.is_shop = 1 and (d.is_fx = 1 or (d.is_fx = 2 and ud.is_effect = 1)) ";
    			$sort_field = " ud.add_time desc ";
    			$deal_result  = get_goods_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$ext_condition,$sort_field);
    				
    			$deal_list = $deal_result['list'];
    			$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d ".$join." where ".$deal_result['condition'],false);
    		
    		}
    		
    		foreach ($deal_list as $k=>$v){
    		    $temp_data['id'] = $v['id'];
    		    $temp_data['name'] = $v['name'];
    		    $temp_data['icon_157'] = get_abs_img_root(get_spec_image($v['icon'],157,157,1));
    		    $temp_data['icon_85'] = get_abs_img_root(get_spec_image($v['icon'],85,85,1));
    		    $temp_data['origin_price'] = round($v['origin_price'],2);
    		    $temp_data['current_price'] = round($v['current_price'],2);
    		    $deal_list[$k] = $temp_data;
    		}
    		
    		//end 分页
    		$page_total = ceil($count/$page_size);
    		$root['page_title'] = $home_user_info['user_name']."的小店";
    		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
    		$root['deal_list'] = $deal_list?$deal_list:array();
	    
	    return output($root);
	}

	public function wap_mall()
	{
	    $root = array();
	    /*参数初始化*/
	    $type = intval($GLOBALS['request']['type']); //0团购类 1商城类
	    // $id = $GLOBALS['ref_uid']; //用户推荐ID
	    $user = $GLOBALS['user_info'];
	    $user_login_status = check_login();
	    $home_user_info = array();
	    if($user_login_status == LOGIN_STATUS_LOGINED){
	        $home_user_info = $user;
	        $id = $user['id'];
	    }/* elseif ($user['is_fx'] == 0) {
	    	$root['is_fx'] = 0;
	    } else {*/

		    if (isset($GLOBALS['request']['rid'])) {
		    	$id = $GLOBALS['request']['rid'];
		    	$home_user_info = load_user($id);
		    }/* else {
		    	$id = $user['id'];
		    	$home_user_info = $user;
		    }*/
			if ($home_user_info && $home_user_info['is_fx'] == 1) {
				//返回会员信息
			    $user_data = array();
			    $user_data['user_name'] = $home_user_info['user_name'];
			    $user_data['user_avatar'] = get_abs_img_root(get_muser_avatar($home_user_info['id'],"big"))?get_abs_img_root(get_muser_avatar($home_user_info['id'],"big")):"";
			    $user_data['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($home_user_info['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";
			    
			    $root['user_data'] = $user_data;
			    
		        $root['user_login_status'] = $user_login_status;
			    $root['type'] = $type;
			    $root['is_why'] = $is_why;
			    $root['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($user['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";

			    //分页
			    $page = intval($GLOBALS['request']['page']);
			    $page=$page==0?1:$page;
			    $page_size = PAGE_SIZE;
			    $limit = (($page-1)*$page_size).",".$page_size;
			    
				require_once(APP_ROOT_PATH."system/model/deal.php");
				$join = " left join ".DB_PREFIX."user_deal as ud on d.id = ud.deal_id and ud.user_id = ".$id." ";
				$sort_field = " ud.add_time desc ";
				//团购
				$deal_ext_condition = " d.buy_type <> 1 and d.is_shop = 0 and (d.is_fx = 1 or (d.is_fx = 2 and ud.is_effect = 1)) ";
				//商品
				$goods_ext_condition = " d.buy_type <> 1 and d.is_shop = 1 and (d.is_fx = 1 or (d.is_fx = 2 and ud.is_effect = 1)) ";
				
				$tuan_count=get_deal_count(array(DEAL_ONLINE,DEAL_NOTICE),"",$join,$deal_ext_condition);;
				$goods_count=get_deal_count(array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$goods_ext_condition);
				
				if($type==0) {
				    $deal_result  = get_deal_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(/*"city_id"=>$GLOBALS['city']['id']*/),$join,$deal_ext_condition,$sort_field);
					$deal_list = $deal_result['list'];		
					$count = $tuan_count;
				} else {
				    $goods_result  = get_goods_list($limit,array(DEAL_ONLINE,DEAL_NOTICE),array(),$join,$goods_ext_condition,$sort_field);
					$deal_list = $goods_result['list'];
					$count = $goods_count;
				}
				
				foreach ($deal_list as $k=>$v){
				    $temp_data['id'] = $v['id'];
				    $temp_data['name'] = $v['name'];
				    $temp_data['icon_157'] = get_abs_img_root(get_spec_image($v['icon'],157,157,1));
				    $temp_data['icon_85'] = get_abs_img_root(get_spec_image($v['icon'],85,85,1));
				    $temp_data['origin_price'] = round($v['origin_price'],2);
				    $temp_data['current_price'] = round($v['current_price'],2);
				    $temp_data['buy_count'] = $v['buy_count'];
				    $temp_data['url'] = SITE_DOMAIN.wap_url("index","deal",array("data_id"=>$v['id'],"r"=>base64_encode($user['id'])));
				    $deal_list[$k] = $temp_data;
				}
				//end 分页
				$page_total = ceil($count/$page_size);
				$root['tuan_count']=$tuan_count;
				$root['goods_count']=$goods_count;
				$root['is_fx'] = 1;
				$root['page_title'] = $home_user_info['user_name']."的小店";
				$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
				$root['deal_list'] = $deal_list?$deal_list:array();
				 return output($root);
			} elseif ($home_user_info && $home_user_info['is_fx'] == 0) {
				return output(array(), 0, '小店还未开通分销资格');
			} else {
				return output(array(), 0, '参数错误');
			}
		/*}*/
		$root['user_login_status'] = $user_login_status;

		return output($root);
	}
	
	
	/**
	 * 修改小店背景
	 *
	 * 输入：
	 * $_FILES['file']：头像文件
	 *
	 * 输出：
	 * status: int 0失败 1成功
	 * info:string 信息提示
	 * fx_mall_bg:string  小店背景图片 360*150
	 */
	public function upload_bg()
	{
	    $root = array();
	
	    if($GLOBALS['user_info'])
	    {

	        if($_FILES['file'])
	        {
	            $res = $this->upload_file($_FILES, $GLOBALS['user_info']['id']);
	            if($res['error']==0)
	            {
	               //保存到用户表
	               
	               $GLOBALS['db']->autoExecute(DB_PREFIX."user",array("fx_mall_bg"=>$res['url']),'UPDATE'," id=".$GLOBALS['user_info']['id']);
	               $root['fx_mall_bg'] = $fx_mall_bg_url = get_abs_img_root($res['thumb']['fx_mall_bg']['url']);
	               return output($root);
	            }
	            else
	            {
	                return output($root,0,$res['message']);
	            }
	        }
	        else
	        {
	            return output($root,0,"请上传文件");
	        }
	    }
	    else
	    {
	        return output($root,0,"请先登录");
	    }
	
	}
	
	
	public function upload_file($_files,$uid){
	    //上传处理
	    //创建comment目录
	    if (!is_dir(APP_ROOT_PATH."public/comment")) {
	        @mkdir(APP_ROOT_PATH."public/comment");
	        @chmod(APP_ROOT_PATH."public/comment", 0777);
	    }
	    
	    $dir = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	        @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	    }
	    
	    $dir = $dir."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	        @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	    }
	    
	    $dir = $dir."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	        @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	    }
	    
	    $img_result = save_image_upload($_files,"file","comment/".$dir,$whs=array('fx_mall_bg'=>array(360,150,1,0)),0,1);

	    if(intval($img_result['error'])!=0)
	    {
	        return $img_result;
	    }
	    else
	    {
	        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	        {
	            syn_to_remote_image_server($img_result['file']['url']);
	            syn_to_remote_image_server($img_result['file']['thumb']['preview']['url']);
	        }
	    
	    }
	    
	    $data_result['error'] = 0;
	    $data_result['url'] = $img_result['file']['url'];
	    $data_result['path'] = $img_result['file']['path'];
	    $data_result['name'] = $img_result['file']['name'];
	    $data_result['thumb'] = $img_result['file']['thumb'];
	    
	    return $data_result;
	}
    /**
	 * 分销收益统计
	 * 输入：
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)

	 *
	 * */
	public function income(){
	    $root = array();
	    /*参数初始化*/
	    $deal_id = intval($GLOBALS['request']['deal_id']);
	    
	    //检查用户,用户密码
	    $user_data = $GLOBALS['user_info'];
	    $user_id  = intval($user_data['id']);
	    $page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		
	    $user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;	
		}
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
			if($user_data['pid']==0){
				$root['pname']="无推荐人";
			}else{
				$root['pname'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$user_data['pid']);
			}	
			
			//可提现金额
			$fx_money=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."fx_withdraw where user_id=".$user_id." and is_paid=0 and is_delete=0");
			
			$root['fxmoney']=format_price($user_data['fx_money'] - $fx_money);			
			$root['fx_total_balance']=format_price($user_data['fx_total_balance']+$user_data['fx_total_vip_buy']);	
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;					
			
			$list =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_id." and money >0 order by create_time desc limit ".$limit);
			$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_user_money_log where user_id = ".$user_id." and money >0");
			
	
			
			$page_total = ceil($count/$page_size);
			
			foreach($list as $k=>$v)
			{				
				$list[$k]['money'] = format_price($v['money']);
				//Y-m-d H:i:s
				$y=to_date($v['create_time'],"Y");
				$m=to_date($v['create_time'],"m");
				$d=to_date($v['create_time'],"d");
				if(to_date(NOW_TIME,"Y")!=$y){
					$list[$k]['create_time_y'] = $y;
					$list[$k]['create_time_m_d'] = $m."月".$d."日";
				}elseif(to_date(NOW_TIME,"m")==$m&&(to_date(NOW_TIME,"d")-$d)<=1){
					if((to_date(NOW_TIME,"d")-$d)==1){
						$list[$k]['create_time_m_d']="昨天";
					}else{
						$list[$k]['create_time_m_d']="今天";
					}
				}else{
					$list[$k]['create_time_m_d'] = $m."月".$d."日";
				}
				$list[$k]['create_time_H_i'] = to_date($v['create_time'],"H:i");
				$list[$k]['create_time'] = to_date($v['create_time']);
			}			
			
			
			$root['list'] = $list?$list:array();
			
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size,"data_total"=>$count);
			$root['is_fx']=$user_data['is_fx'];
			$root['page_title']="收益统计";
	    }
	    return output($root);
	}
    
	/**
	 * 分销资格购买
	 * 输入：
	 *
	 * 输出：
	 * user_login_status:int 用户登录状态(1 已经登录/0用户未登录/2临时用户)
	 * mobile:varchar 用户手机号，判断用户是否绑定手机，为空前往绑定手机页
	
	 *
	 * */
	public function vip_buy(){
	    $root = array();
     
	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	     
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	        $root['is_fx']=$user['is_fx'];
	        
	        $fx_buy=$GLOBALS['db']->getRow("select id,pay_fee,pay_agreement,phone_description from ".DB_PREFIX."fx_qualification");
	        if($fx_buy['pay_fee']){
	            $fx_buy['pay_fee']=round($fx_buy['pay_fee'],2);
	        }
	        $root['fx_buy']=$fx_buy;
	        $root['mobile']=$user['mobile'];
	        $root['page_title']="分销资质购买";
	    }
	    return output($root);
	}
	
	/**
	 * 小店二维码
	
	 *
	 * */
	public function qrcode(){
	    $root = array();

	    //检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			

		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
		    $root['user_login_status'] = $user_login_status;	
		}
		else
		{
			$root['user_login_status'] = $user_login_status;
			$GLOBALS['ref_uid'] = $user_id;
			$root['is_fx']=$user['is_fx'];
			if($user['is_fx']==1){
    			//返回会员信息
    			$user_data = array();
    			$user_data['user_name'] = $user['user_name'];
                $user_data['qrcode_type'] = intval($user['qrcode_type']);
    			$user_data['fx_money'] = round($user['fx_money'],2);
    			$user_data['user_avatar'] = get_abs_img_root(get_muser_avatar($user_id,"big"))?get_abs_img_root(get_muser_avatar($user_id,"big")):"";
                if($user_data['qrcode_type']==0){
                    $qrcode_url=wap_url("index","uc_fx#mall",array("r"=>base64_encode($user_id)));
                }elseif($user_data['qrcode_type']==1){
                    $qrcode_url=wap_url("index","index",array("r"=>base64_encode($user_id)));
                }elseif($user_data['qrcode_type']==2){
                    $qrcode_url=wap_url("index","shop",array("r"=>base64_encode($user_id)));
                }elseif($user_data['qrcode_type']==3){
                    $qrcode_url=wap_url("index","main",array("r"=>base64_encode($user_id)));
                }
    			$user_data['share_mall_qrcode'] = get_abs_img_root(gen_qrcode(SITE_DOMAIN.$qrcode_url));
    			$user_data['share_mall_url'] = SITE_DOMAIN.$qrcode_url;
    				
    			$user_data['fx_mall_bg'] = $user['fx_mall_bg']?get_abs_img_root(get_spec_image($user['fx_mall_bg'],320,150,1)):SITE_DOMAIN.APP_ROOT."/mapi/image/nofxmallbg.jpg";
    			if($user['pid']){
    			    $user_data['pname']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."user where id=".$user['pid']);
    			}
				
				require APP_ROOT_PATH.'system/utils/es_image.php';
				$new_pic= new es_image();
				$dir_invite=APP_ROOT_PATH.'public/attachment/invite';
				if(!file_exists($dir_invite)){
					@mkdir($dir_invite);
				}
				// 二维码
				$qrcode = $dir_invite."/invite_qrcode_".$user_id.".jpg";
				copy($user_data['share_mall_qrcode'], $qrcode);
				
				$user_logo = get_muser_avatar($user_id,"big",0)?get_muser_avatar($user_id,"big",0):"";
				$user_logo=str_replace(SITE_DOMAIN.APP_ROOT,APP_ROOT_PATH, $user_logo);
				$user_logo_circular=$dir_invite."/noavatar_".$user_id.".".pathinfo($user_logo, PATHINFO_EXTENSION);
				//copy($user_logo, $user_logo_circular);
				
				require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
				$image = new es_imagecls();
				
				$thumbname=$dir_invite."/invite_".$user_id."_160x160.jpg";
				
				// 图片大小设置,生成二维码缩略图
				$thumb=$image->thumb($qrcode,$width=160,$height=160,$thumb_type=0,true,$thumbname);
				
				// 图片大小设置,生成头像缩略图
				$user_logo_circular=$image->thumb($user_logo,$width=60,$height=60,$thumb_type=0,true,$user_logo_circular);
				
				@unlink($qrcode);
				$savename_bg=$dir_invite."/invite_bg_".$user_id.".jpg";
				@unlink($savename_bg);
				
				$new_pic::background_png($savename_bg,array("width"=>300,"height"=>450),array("255","255","255"));
				
				// 把二维码加入背景图中
				$new_pic::water($savename_bg, $thumb['path'], $savename_bg,$alpha=100,$position="6");
				
				// 将图片切为圆形图
				$new_pic::circular_png($user_logo_circular['path']);
				$new_pic::water($savename_bg, $user_logo_circular['path'] , $savename_bg,$alpha=100,$position="9");
				@unlink($user_logo_circular['path']);
				$path = $dir_invite."/invite_user_name_".$user_id.".png";
				//$user_name_pic = $this -> make_transparent_pic(300,80,$path,$user_info['user_name']);
				//$new_pic::water($savename_bg, $user_name_pic , $savename_bg,$alpha=100,$position="8");
				
				//向图片写入文字
				$new_pic::png_str($savename_bg,$savename_bg,$user_data['user_name'],"simhei.ttf",1,array('posY'=>150));
				$new_pic::png_str($savename_bg,$savename_bg,"长按此图 识别图中二维码","simhei.ttf",1,array('posY'=>399.5));
				@unlink($thumb['path']);
				$img_url = get_abs_img_root(str_replace(APP_ROOT_PATH,"./", $savename_bg));
				
				if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
				{
				    syn_to_remote_image_server(str_replace(APP_ROOT_PATH,"./", $savename_bg));
				}

				@unlink($path);
				$root['img_url'] = $img_url;
				
    			$root['user_data'] = $user_data;
    		}
		}	

        $set_up=array();
        $set_up[]=array('type'=>1,'name'=>'平台首页');
        $set_up[]=array('type'=>2,'name'=>'商城首页');
        $set_up[]=array('type'=>3,'name'=>'团购首页');
        $set_up[]=array('type'=>0,'name'=>'我的小店');
        $root['set_up']=$set_up;
		//$root['page_title'] = $GLOBALS['m_config']['program_title']?$GLOBALS['m_config']['program_title']." - ":"";
		$root['page_title']="推广二维码";
	    return output($root);
	}

    public function save_qrcode_type(){
        $root = array();
        $qrcode_type = intval($GLOBALS['request']['qrcode_type']);
        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id  = intval($user['id']);
        $root['status']=0;
        $root['info']="修改失败";
        $user_login_status = check_login();
        if($user_login_status!=LOGIN_STATUS_LOGINED){
            $root['user_login_status'] = $user_login_status;
        }
        else
        {
            $root['user_login_status'] = $user_login_status;
            $user_data['qrcode_type'] = $qrcode_type;
            $GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"UPDATE","id=".$user_id);
            $root['status']=1;
            $root['info']="修改成功";

        }
        return output($root,$root['status'],$root['info']);
    }
	public function make_transparent_pic($width,$height,$path,$text=''){
		
		//1.生成真彩图
		$img = imagecreatetruecolor($width, $height);
		//2.上色
		$color=imagecolorallocate($img,255,255,255);
		//3.设置透明
		imagecolortransparent($img,$color);
		imagefill($img,0,0,$color);
		//4.向画布上写字
		$textcolor=imagecolorallocate($img,255,0,0);
		$font_file=APP_ROOT_PATH.'public/attachment/font/STKAITI.TTF';

		//$img = $this->  pngthumb($path,$path,$width,$height);
		imagettftext($img, 27, 0, 10, 60, $textcolor, $font_file, $text);
	    //5.保存
		
		imagepng($img,$path);
	    //6.释放
		
		imagedestroy($img);
		return $path;
	}
	
	/**
	 * 生成分销资格订单
	 */
	public function make_order() {
	    $root = array();
	
	    $user_login_status=check_login();
	    $user_info=$GLOBALS['user_info'];
	    $user_id=$user_info['id'];
	    $root['user_login_status']=$user_login_status;
	    
	    if ($user_login_status!=LOGIN_STATUS_LOGINED) {

	        return output($root,0,"请先登录");
	        
	    }
	    else if (!$user_info['mobile']){
	        $root['mobile']=1;
	        return output($root,0,"请先绑定手机");
	    }
	    else{
	        $root['is_fx']=$user_info['is_fx'];
	        
	        if($user_info['is_fx']){ 
                return output($root,0,"你已购买过分销资格");    
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
                    if($fx_buy_info['fx_award']){
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
	            
	            $root['order_id'] = $order_id;
	            
	            $is_free=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_buy_order where total_price=0 and id=".$order_id);
	            
	            //免费开通，直接结单
	            if($is_free){
	                require_once(APP_ROOT_PATH."system/model/cart.php");
	                $rs = fx_buy_order_paid($order_id);
	                $root['free']=1;
	                if($rs){
	                    $root['is_open']=1;
	                    return output($root,1,"您已成功开通分销");
	                }else {
	                    return output($root,0,"开通失败");
	                }
	            }
	            
	        }
	        return output($root);
	    }
	}
    
	/**
	 * 订单支付页面
	 * 输入
	 * order_id int 门店ID
	 *
	 * 输出
	 * user_login_status int 用户登录状态
	 *
	 * order_info：array 订单信息
	 * order_id ：int 订单号
	 * has_account： int 是否显示余额支付方式
	 * payment_list:付款方式列表
	 * account_money：用户帐户中的余额
	 * order_info['total_price']:总计金额
	 * order_info['discount_price']：已优惠金额
	 * pay_data:为输出订单的支付款项，包括  总计，已优惠，手续费
	 * [pay_data] => Array
	 (
	 [0] => Array
	 (
	 [name] => 总计
	 [price] => 1000
	 )
	
	 [1] => Array
	 (
	 [name] => 已优惠
	 [price] => 100.0000
	 )
	
	 )
	 */
	public function check() {
	
	    //获取参数
	     
	    $order_id = intval($GLOBALS['request']['order_id']);
	
	    $user_info = $GLOBALS['user_info'];
	
	
	    $root = array();
	
	    if ((check_login() == LOGIN_STATUS_TEMP && $GLOBALS['user_info']['money'] > 0) || check_login() == LOGIN_STATUS_NOLOGIN) {
	        $root['user_login_status']=0;
	        return output($root, 0, "请先登录");
	    }else{
	        $root['user_login_status']=check_login();
	        $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id=".$order_id." and user_id=".$user_info['id']);
	
	        if($order_info['pay_status']==2){
	            $root['order_id']=$order_info['id'];
	            return output($root, 2, "支付成功");
	        }
	        
	        if($order_info['order_status']==1 || $order_info['is_delete']==1){
	            return output($root, 0, "非法订单");
	        }
	         
	         
	        //计算优惠
	        require_once(APP_ROOT_PATH.'system/model/fx.php');
	        $count_data = fx_pay_total($order_info['total_price'],0,0,0,0,$order_info['user_id'],$order_id);
	        
	        $order_info['total_price']-=$order_info['payment_fee'];
	        $total_price = $order_info['total_price'];
	        $order_id = $order_info['id'];
	        $root['order_id'] = $order_id;
	
	        $root['order_info']=$order_info;
	
	
	
	        if($total_price > 0)
	            $show_payment = 1;
	        else
	            $show_payment = 0;
	        $root['show_payment'] = $show_payment;
	        if($GLOBALS['user_info']['money'] >= $total_price){
	            $root['has_account'] = 1;  //允许余额支付
	        }else{
	            $root['has_account'] = 0;  //允许余额支付
	        }
	
	        $is_weixin=isWeixin();
	        $app_index=APP_INDEX;
	
	        //输出支付方式
	        if ($app_index == 'wap' && !$is_weixin) {
	            //支付列表
	            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and class_name != 'Wwxjspay' and is_effect = 1";
	        }
	        elseif ($app_index == 'wap' && $is_weixin) {
	            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 2 or online_pay = 4 or online_pay = 5) and is_effect = 1";
	             
	        }
	        else {
	            //支付列表
	            $sql = "select id, class_name as code, logo, fee_amount,fee_type from " . DB_PREFIX . "payment where (online_pay = 3 or online_pay = 4 or online_pay = 5) and is_effect = 1";
	        }
	        if (allow_show_api()) {
	            $payment_list = $GLOBALS['db']->getAll($sql);
	        }
	
	        foreach ($payment_list as $k => $v) {
	            $directory = APP_ROOT_PATH . "system/payment/";
	            $file = $directory . '/' . $v['code'] . "_payment.php";
	            if (file_exists($file)) {
	                require_once($file);
	                $payment_class = $v['code'] . "_payment";
	                $payment_object = new $payment_class();
	                $payment_list[$k]['name'] = $payment_object->get_display_code();
	            }
	
	            if ($v['logo'] != "")
	                $payment_list[$k]['logo'] = get_abs_img_root(get_spec_image($v['logo'], 40, 40, 1));
	            
	            if($v['fee_type'])
	                $payment_list[$k]['fee_amount']= $order_info['total_price']*$v['fee_amount'];
	        }
	        
	        sort($payment_list);
	        $root['payment_list'] = $payment_list;
	
	        $root['page_title'] = "收银台";
	        $root['account_money'] = round($GLOBALS['user_info']['money'], 2);
	
	        $show_pay_info=array();
	        if($total_price > 0){
	            $pay_info=array();
	            $pay_info['name']='总计';
	            $pay_info['value']=format_price($order_info['total_price']);
	            $show_pay_info[]=$pay_info;
	        }
	        
	        $root['pay_data']=$show_pay_info;
	
	        return output($root);
	    }
	}
	
	
	/**
	 * 订单支付页，点击付款方式的请求接口
	 *
	 * 输入:
	 * order_id: int 订单ID
	 * all_account_money：是否全额支付
	 * payment_id：付款方式的ID
	 *
	 * 输出:
	 * status:int 状态 0:失败 1:成功
	 * info: string 失败的原因
	 * 以下参数为成功时返回
	 * pay_status: int 支付状态 0:未支付 1:已支付 2：付款单号重复支付
	 *
	 * order_id: int 订单ID
	 * payment_notice_id：当pay_status为0和2时，返回的付款单号
	 *
	 *
	 */
	public function pay_done()
	{
	    global_run();
	    if(check_login() == LOGIN_STATUS_TEMP || check_login() == LOGIN_STATUS_NOLOGIN) {
	
	        $root['user_login_status']=0;
	        return output($root,-1,"请先登录");
	    }else{
	
	        $root['user_login_status']=1;
	        $user_id=isset($GLOBALS['user_info']['id'])?$GLOBALS['user_info']['id']:0;
	         
	        $order_id = intval($GLOBALS['request']['order_id']);
	        $all_account_money = intval($GLOBALS['request']['all_account_money']);
	        $payment_id = intval($GLOBALS['request']['payment_id']);
	        $bank_id = strim($GLOBALS['request']['bank_id']);
	
	        $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
	
	        if(empty($order_info))
	        {
	            return output($root,0,"订单不存在");
	        }
	
	
	        require_once(APP_ROOT_PATH.'system/model/fx.php');
	
	        $data = fx_pay_total($order_info['total_price']-$order_info['payment_fee'], $payment_id,$bank_id,0,$all_account_money,$order_info['user_id'],$order_id);
	
	
	        if($data['pay_price']>0 && empty($data['payment_info']))
	        {
	            return output($root,0,"请选择支付方式");
	        }
	         
	        $root['data']=$data;
	        $root['app_index'] = APP_INDEX;
	
	        $now = NOW_TIME;
	
	        $order['payment_id'] = $payment_id;
	        $order['bank_id'] = $bank_id;
	        $order['payment_fee'] = $data['payment_fee'];
	        $total_price=$order_info['total_price']-$order_info['payment_fee'];
	        $order['total_price'] = $total_price+$order['payment_fee'];
	        $GLOBALS['db']->autoExecute(DB_PREFIX."fx_buy_order",$order,'UPDATE','id='.$order_id,'SILENT');
	
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
	
	            $root['pay_status']=1;
	            $root['order_id'] = $order_id;
	            return output($root);
	
	        }
	        else
	        {
	            $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
	            if($order_info['pay_status'] == 2)
	            {   //付款单号重复支付,当前支付的退到会员帐户
	
	                $root['pay_status']=2;
	                $root['order_id'] = $order_id;
	                $root['payment_notice_id']=$payment_notice_id;
	                return output($root);
	            }else{ //正常支付，还有部分未完成
	                $root['pay_status']=0;
	                $root['order_id'] = $order_id;
	                $root['payment_notice_id']=$payment_notice_id;
	                
	                if(APP_INDEX=='app'){
	                    $data_pay = call_api_core("uc_fx","payment_done",array("order_id"=>$order_id));
	                    $root['sdk_code'] = $data_pay['payment_code']['sdk_code'];
	                    $root['pay_url'] = $data_pay['payment_code']['pay_action'];
	                    $root['online_pay'] = $data['payment_info']['online_pay'];
	                    $root['title'] = $data_pay['title'];
	                    $root['is_account_pay'] =$data_pay['is_account_pay'];
	                }
	
	            }
	            return output($root);
	        }
	    }
	}
	
	/**
	 * 支付完成
	 * 输入：order_id
	 *   */
	public function payment_done()
	{
	    global_run();
	    $root = array();
	    $order_id = intval($GLOBALS['request']['order_id']);
	    $user_id = $GLOBALS['user_info']['id'];
	    
	    $user_login_status=check_login();
	    
	    $root['user_login_status']=$user_login_status;

	    if ($user_login_status!=LOGIN_STATUS_LOGINED) {
	        return output($root,0,"请先登录");  
	    }
	    else{
    	    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id." and user_id=".$user_id);
    	    
    	    if(empty($order_info))
    	    {
    	        return output($root,-1,"订单不存在");
    	    }
    	    
    	    if($order_info['pay_status']!=2){
    	        $payment_id = $order_info['payment_id'];
	
        	    $payment_notice_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id." and payment_id=".$payment_id." and order_type=5 order by create_time desc limit 1");
        	    $payment_notice_id =$payment_notice_info['id'];
        	    $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice_info['payment_id']);
        	
        	    $app_index=APP_INDEX;
        	    
        	    if($app_index=="wap") {
        	        if ($payment_info['online_pay']!=2&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5) {
        	            return output(array(),0,"该支付方式不支持wap支付");
        	        }
        	    } else {
        	        if ($payment_info['online_pay']!=3&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5) {
        	            return output(array(),0,"该支付方式不支持手机支付");
        	        }
        	    }
        	    	
        	    require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
        	    $payment_class = $payment_info['class_name']."_payment";
        	    $payment_object = new $payment_class();
        	    $payment_code = $payment_object->get_payment_code($payment_notice_id);
    	        
        	    $root['title'] = $payment_info['name'];
        	    	
        	    if($payment_info['class_name']=='Account'){
        	        $is_account_pay = 1;
        	    }else{
        	        $is_account_pay = 0;
        	    }
        	    $root['is_account_pay'] = $is_account_pay;
        	    $root['pay_status'] = 0;
        	    $root['payment_code'] = $payment_code;
        	    $root['app_index'] = $app_index;
        	    $root['payment_info']=$payment_info;
        	    $root['order_id'] = $order_id;
        	    
    	    }else{
        	    $root['page_title'] = "付款成功";
        	    $root['pay_status'] = 1;
        	    $root['order_sn'] = $order_info['order_sn'];
        	    $root['order_id'] = $order_id;
    	    }
    	    return output($root);
	    }   
	}
	
	/**
	 * 支付未完成调用生成第三方接口链接
	 * @return [type] [description]
	 */
	public function third_pay_interface()
	{
	    $order_id = intval($GLOBALS['request']['order_id']);
	    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."fx_buy_order where id = ".$order_id);
	    	
	    $payment_id = $order_info['payment_id'];
	
	    $payment_notice_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id." and payment_id=".$payment_id." and order_type=5 order by create_time desc limit 1");
	    $payment_notice_id =$payment_notice_info['id'];
	    $payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice_info['payment_id']);
	
	    if($GLOBALS['request']['from']=="wap") {
	        if ($payment_info['online_pay']!=2&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5) {
	            return output(array(),0,"该支付方式不支持wap支付");
	        }
	    } else {
	        if ($payment_info['online_pay']!=3&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5) {
	            return output(array(),0,"该支付方式不支持手机支付");
	        }
	    }
	    	
	    require_once(APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php");
	    $payment_class = $payment_info['class_name']."_payment";
	    $payment_object = new $payment_class();
	    $payment_code = $payment_object->get_payment_code($payment_notice_id);
	
	    //$root['order_id']=$order_id;
	    //$root['order_sn']=$order_info['order_sn'];
	    // $root['pay_status']=$pay_status;
	    //$root['payment_code'] = $payment_code;
	
	    return output($payment_code);
	}
}

