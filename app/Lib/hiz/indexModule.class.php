<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/page.php';
class indexModule extends HizBaseModule
{ 
	public function index()
	{	
	    //获取权限
	    global_run();
	    init_app_page();
	    
	    $data=array();
		$account_info = $GLOBALS['hiz_account_info'];
		//print_r($account_info);exit;
		if(!$account_info){
	   		app_redirect(url("hiz","user#login"));
		}

		/*代理商数据筛选*/
		$hiz_info=array();
		$hiz_info['account_name'] = $account_info['account_name'];
		$hiz_info['login_time'] = to_date($account_info['login_time']);
		$hiz_info['login_ip'] = $account_info['login_ip'];
		$hiz_info['login_count'] = intval($account_info['login_count']);
		
		if($hiz_info['login_count']==1){
		    if($account_info['city_code'])
		        $supplier_city_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_city where code=".$account_info['city_code']);
		    if($supplier_city_id)
		        $GLOBALS['db']->autoExecute(DB_PREFIX."supplier", array("agency_id"=>$account_info['id'],"city_code"=>$account_info['city_code']), "UPDATE", "city_id=".$supplier_city_id);
		}
		
		/*昨日数据*/
		//待审商家
		$supplier_submit_sql="select count(id) from ".DB_PREFIX."supplier_submit where agency_id=".$account_info['id']." 
		    and create_time >= ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 24 * 60 * 60 ))." and create_time < ".to_timespan(to_date( NOW_TIME,'Ymd'));
		$data['l_sub_count']=$GLOBALS['db']->getOne($supplier_submit_sql);

		//昨日订单
		$order_info_sql="select count(id) co,type from ".DB_PREFIX."deal_order where is_main=0 and (type=4 or type=5 or type=6) and agency_id=".$account_info['id']." 
		    and create_time >= ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 24 * 60 * 60 ))." and create_time < ".to_timespan(to_date( NOW_TIME,'Ymd')).' group by type';
		$order_info=$GLOBALS['db']->getAll($order_info_sql);
		$c = array();
		foreach ($order_info as $kk=>$vv){
		    $c[$vv['type']] = $vv['co'];
		    /*if($vv['type']==4){
		        $dist_order[]=$vv;
		    }
		    elseif ($vv['type']==6){
		        $shop_order[]=$vv;
		    }
		    else{
		        $tuan_order[]=$vv;
		    } */
		}
        $data['dist_order_count'] = $c[4]?$c[4]:0;//驿站自营订单
        $data['shop_order_count'] = $c[6]?$c[6]:0;//商城订单
        $data['tuan_order_count'] = $c[5]?$c[5]:0;//团购订单
        
		//待审驿站
		$dist_submit_sql="select count(id) from ".DB_PREFIX."distribution where id in ('".implode(",", $account_info['dist_ids'])."') and status=0 
		    and create_time >= ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 24 * 60 * 60 ))." and create_time < ".to_timespan(to_date( NOW_TIME,'Ymd'));
		$data['dist_sub_count']=$GLOBALS['db']->getOne($dist_submit_sql);
		$data['dist_sub_count']=$data['dist_sub_count']?$data['dist_sub_count']:0;
		/*总体统计*/
		//商家统计
		$data['location_count'] = count($account_info['location_ids']);
		
		//驿站统计
		$data['dist_count']=count($account_info['dist_ids']);
		
        //会员、网宝统计
        $user_info=$GLOBALS['db']->getAll("select id,user_name,is_fx from ".DB_PREFIX."user where agency_id = ".$account_info['id']);
        //print_r($GLOBALS['db']->getLastSql());exit;
        $data['user_count']=count($user_info);
		$fx_user=array();
		foreach ($user_info as $k=>$v){
		    if($v['is_fx']==1){
		        $fx_user[]=$v;
		    }  
		}
		$data['fx_count']=count($fx_user);
		
		//7天统计
		$days=array();
// 		$count_sql="select from_unixtime(create_time, '%Y-%m-%d') as ctime, count(id) as count_id, sum(is_fx) as count_fx from ".DB_PREFIX."user where agency_id = 1 
// 		    and create_time < ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 1 * 24 * 60 * 60 ))."
// 		    group by ctime";
		$sum_count_sql="select count(id) as sum_count_id, sum(is_fx) as sum_count_fx from ".DB_PREFIX."user where agency_id = 1 
		    and create_time < ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( 1 * 24 * 60 * 60 ));
		for($i=7;$i>0;$i--){
		    $days[]=to_date((to_timespan(to_date( NOW_TIME,'Ymd')) - ( $i * 24 * 60 * 60 )),'Y-m-d');
		}
		$sum=$GLOBALS['db']->getRow($sum_count_sql);
// 		$date=$GLOBALS['db']->getAll($count_sql);
		//print_r($sum);exit;
		foreach ($days as $kkk=>$vvv){
		    $sum_count_sql="select count(id) as sum_count_id, sum(is_fx) as sum_count_fx from ".DB_PREFIX."user where agency_id = 1
		    and create_time < ".(to_timespan(to_date( NOW_TIME,'Ymd')) - ( (7-$kkk) * 24 * 60 * 60 ));
		    $sum=$GLOBALS['db']->getRow($sum_count_sql);
// 		    logger::write(print_r($sum,1));
// 		    logger::write(print_r($sum_count_sql,1));
            if(sum){
                $days[$vvv]['ctime']=$vvv;
                $days[$vvv]['count_id']=$sum['sum_count_id'];
                $days[$vvv]['count_fx']=$sum['sum_count_fx'];
            }
		    unset($days[$kkk]);
		}
		$days=array_values($days);
		//print_r($days);exit;

		/*版本号*/
		$ver=$GLOBALS['db']->getOne("select value from ".DB_PREFIX."conf where name = 'DB_VERSION'");
		$version=include(APP_ROOT_PATH."public/version.php");
		$ver = '当前版本：'.'v'.$ver.'.'.$version['APP_SUB_VER'];
        
		$data['hiz_info'] = $hiz_info?$hiz_info:array();
		$data['version'] = $ver;
		
		//print_r($days);exit;
	    $GLOBALS['tmpl']->assign("days",$days);
		$GLOBALS['tmpl']->assign("hiz_info",$data['hiz_info']);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("index.html");
	}
	
	
}
?>