<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dc_locationModule extends MainBaseModule
{
    public function index()
    {
        global_run();
        init_app_page();
        
        $data_id=intval($_REQUEST['data_id']);
        $menu_id=intval($_REQUEST['menu_id']);
        $data = call_api_core("dc_location","index",array("data_id"=>$data_id,"menu_id"=>$menu_id));
        
        //开始身边团购的地理定位
        $tid=intval($_REQUEST['tid']);
        $s_info=get_lastest_search_name();
        
        if(!isset($GLOBALS['geo']['address'])){
            app_redirect(wap_url('index','dcposition'));
        }
        
        if($data['is_has_location']==1)
        {
            $GLOBALS['tmpl']->assign('s_info',$s_info);
            
            $dp_info=$data['dp_info'];
            
            if(isset($dp_info['page']) && is_array($dp_info['page'])){
            
                //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
                $page = new Page($dp_info['page']['data_total'],$dp_info['page']['page_size']);   //初始化分页对象
                //$page->parameter
                $p  =  $page->show();
                //print_r($p);exit;
                $GLOBALS['tmpl']->assign('pages',$p);
            }
            $data['dclocation']['dc_avg_point']=round($dp_info['avg_point'],1);
            
            $GLOBALS['tmpl']->assign("dp_list",$dp_info['dp_list']);
            $GLOBALS['tmpl']->assign('menu_add',$data['menu_add']);
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->assign("dclocation",$data['dclocation']);
            $GLOBALS['tmpl']->assign("menu_list",$data['menu_list']);
            $GLOBALS['tmpl']->assign("cart_data",$data['cart_data']);
            $GLOBALS['tmpl']->assign('base_url', wap_url('index', 'dc_location', array('data_id' => $data_id)));
            $GLOBALS['tmpl']->display("dc/dc_location_index.html");
        }
        else{
            showErr('商家不存在',0,wap_url('index','dc'));
        }
    }
    
    public function location_dp() {
        global_run();
        
        $param['page'] = intval($_REQUEST['page']);
        $param['data_id']=intval($_REQUEST['data_id']);
        $data = call_api_core("dc_location","location_dp_list",$param);
        
        $GLOBALS['tmpl']->display("dc/dc_location_index.html");
    }
 
}