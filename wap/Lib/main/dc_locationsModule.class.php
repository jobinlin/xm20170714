<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require_once(APP_ROOT_PATH."app/Lib/main/core/dc_init.php");

class dc_locationsModule extends MainBaseModule
{
    
    public function index()
    {
        global_run();
		dc_global_run();
		init_app_page();	
		//参数处理
		require_once(APP_ROOT_PATH."system/model/dc.php");
		$s_info=get_lastest_search_name();
		
		$GLOBALS['tmpl']->assign("s_info",$s_info);
		$GLOBALS['tmpl']->assign("city_name",$GLOBALS['city']['name']);
		
		if(isset($GLOBALS['geo']['address'])){
            $param['page'] = intval($_REQUEST['page']);
            $param['cid'] = intval($_REQUEST['cid']);
            $param['sort'] = intval($_REQUEST['sort']);
            $param['dc_online_pay'] = intval($_REQUEST['dc_online_pay']);
            $param['dc_allow_cod'] = intval($_REQUEST['dc_allow_cod']);
            $param['dc_allow_invoice'] = intval($_REQUEST['dc_allow_invoice']);
            $param['no_start_price'] = intval($_REQUEST['no_start_price']);
            $param['no_delivery_price'] = intval($_REQUEST['no_delivery_price']);
            $param['is_firstorderdiscount'] = intval($_REQUEST['is_firstorderdiscount']);
            $param['is_payonlinediscount'] = intval($_REQUEST['is_payonlinediscount']);
            
            
            $data_id=intval($_REQUEST['data_id']);
            $data = call_api_core("dc_locations","index",$param);
            
            if($param['cid']){
                $GLOBALS['tmpl']->assign("cate_name",$data['cate_list'][$param['cid']]['name']);
            }
            
            $data['page_title']="送至：".$s_info['dc_title'];
            
            if(isset($data['page']) && is_array($data['page'])){
            
                //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
                $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
                //$page->parameter
                $p  =  $page->show();
                //print_r($p);exit;
                $GLOBALS['tmpl']->assign('pages',$p);
            }
            
            $GLOBALS['tmpl']->assign("param",$param);
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("dc/dc_locations_list.html");
		}
		else
		{
		    app_redirect(wap_url('index','dcposition'));
		}
    }
    
}