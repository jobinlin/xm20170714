<?php

class biz_store_pay_orderModule extends MainBaseModule
{
    public function index()
    {
        global_run();
        init_app_page();

        $param=array();
        $param['page'] = intval($_REQUEST['page']);
        $param['create_ym'] = intval($_REQUEST['create_ym']);
        $data = call_api_core("biz_store_pay_order","index",$param);

        if($data['biz_user_status']!=LOGIN_STATUS_LOGINED){
            app_redirect(wap_url("biz","user#login"));
        }

        if(isset($data['page']) && is_array($data['page'])){
            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            $GLOBALS['tmpl']->assign('pages',$p);
        }
        if($data['status']==0){
            $jump_url = wap_url('biz', 'shop_verify');
            $script = suiShow($data['info'], $jump_url);
            $GLOBALS['tmpl']->assign('suijump', $script);
            $GLOBALS['tmpl']->display('style5.2/inc/biz_nodata.html');
        }else{
            $GLOBALS['tmpl']->assign("month_data_url",wap_url("biz","store_pay_order",$param['create_ym']));
            $GLOBALS['tmpl']->assign("data",$data);
            $GLOBALS['tmpl']->display("biz_store_pay_order.html");
        }

    }


}
?>