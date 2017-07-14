<?php

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class scoresModule extends MainBaseModule
{

    public function index()
    {
        global_run();
        init_app_page();
        $param['page'] = intval($_REQUEST['page']); //分页
        $param['cate_id'] = intval($_REQUEST['cate_id']);

        $data = call_api_core("scores","index",$param);
        unset($data['bcate_list']);
        unset($data['brand_list']);
        unset($data['navs']);
        unset($data['bid']);
        unset($data['cate_id']);
        //end navs
        if(isset($data['page']) && is_array($data['page'])){

            //感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了
            $page = new Page($data['page']['data_total'],$data['page']['page_size']);   //初始化分页对象
            //$page->parameter
            $p  =  $page->show();
            //print_r($p);exit;
            $GLOBALS['tmpl']->assign('pages',$p);
        }
       // $back_url = wap_url("index","scores_index");
       // $GLOBALS['tmpl']->assign("back_url",$back_url);
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("scores.html");
    }


}
?>