<?php

class biz_locationModule extends MainBaseModule
{
    public function index()
    {
        global_run();
        init_app_page();

        $param=array();
        $param['page'] = intval($_REQUEST['page']);

        $data = call_api_core("biz_location","index",$param);

        if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }

        if(isset($data['page']) && is_array($data['page'])){
            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            //print_r($p);exit;
            $GLOBALS['tmpl']->assign('pages',$p);
        }

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_location.html");
    }

    /*
     * 门店信息
     * */
    public function detail()
    {
        global_run();
        init_app_page();

        $param=array();
        $param['page'] = intval($_REQUEST['page']);
        $param['id'] = 113;//门店id
        $data = call_api_core("biz_location","detail",$param);

        if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }

        if(isset($data['page']) && is_array($data['page'])){
            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            //print_r($p);exit;
            $GLOBALS['tmpl']->assign('pages',$p);
        }

        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("biz_location_detail.html");
    }


}
?>