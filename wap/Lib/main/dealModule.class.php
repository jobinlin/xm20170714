<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class dealModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		$data_id = intval($_REQUEST['data_id']);
		
		$location_id = intval($_REQUEST['location_id']);
		
		
		set_gopreview();
		if($data_id==0)
			$data_id = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal where uname = '".strim($_REQUEST['data_id'])."'"));
		
		$data = call_api_core("deal","index",array("data_id"=>$data_id,"type"=>1,'location_id'=>$location_id));
		$data['images_count'] = count($data['images']); 
		
		if(intval($data['id'])==0)
		{
			$jump_url = wap_url('index', 'goods');
			$script = suiShow('商品不存在或已删除', $jump_url);
			$GLOBALS['tmpl']->assign('suijump', $script);
			$GLOBALS['tmpl']->display('style5.2/inc/nodata.html');
		}else{
    		$data['detail_url'] = wap_url("index", 'deal_detail', array('data_id'=>$data['id']) );
    		$data['dp_url'] = wap_url("index", 'dp_list', array('data_id'=>$data['id'], 'type'=>'deal') );
    		
    		 
    		// 优惠互动
    		$data['promotes_list_arr'] = $data['promotes_list'];
    	    $data['promotes_list'] = join('，',  $data['promotes_list']);
    	    
    		// 商家其它团购商品
    		foreach ($data['other_location_deal'] as $k=>$v){
    		    $data['other_location_deal'][$k]['old_url'] =  wap_url("index", 'deal', array('data_id'=>$v['id']) );
    		    
    		}
    		
    		// 商家其它门店
    		foreach ($data['supplier_location_list'] as $k=>$v){
    		    $data['supplier_location_list'][$k]['location_url'] =  wap_url("index", 'store', array('data_id'=>$v['id']) );
    		    
    		   
    		    $data['supplier_location_list'][$k]['distance'] = format_distance_str($v['distance']);
    		    
    		
    		}
    
    		//当前门店
    		foreach ($data['supplier_location_list'] as $t => $v){
    		    if($v['id']==$location_id){
    		        $data['supplier_0'] = $data['supplier_location_list'][$t];
    		    }else {
    		        $data['supplier_0'] = $data['supplier_location_list'][0];
    		    }
    		}
    		

    		//是否存在关联商品
    		$relate_data = $data['relate_data'];
    		if($relate_data){
				//goodsList wap展示为两个商品一组，需要改造一下
    			$rsGoodsList = array();
    			for( $k=0;$k<ceil(count($newGoodsList)/2);$k++ ){
    				$item1 = $newGoodsList[$k*2];
    				$item2 = $newGoodsList[$k*2+1];
    				if(!$item2){
    					$item1['widthP'] = '50%';
    				}else{
    					$item1['widthP'] = '100%';
    				}
    				$rsGoodsList[$k][] = $item1;
    				if($item2){
    					$item2['widthP'] = '100%';
    					$rsGoodsList[$k][] = $item2;
    				}			
    			}
    			$GLOBALS['tmpl']->assign("goodsList",$rsGoodsList);
    			$GLOBALS['tmpl']->assign("jsonDeal",json_encode($relate_data['dealArray']));
    			$GLOBALS['tmpl']->assign("jsonAttr",json_encode($relate_data['attrArray']));
    			$GLOBALS['tmpl']->assign("jsonStock",json_encode($relate_data['stockArray']));
    		}
    		$hasRelateGoods = !empty($relate_data)?1:0;
            
    		$GLOBALS['tmpl']->assign("hasRelateGoods",$hasRelateGoods);
    		
    		$GLOBALS['tmpl']->assign("download",url("index","app_download"));
    		$GLOBALS['tmpl']->assign("data",$data);		
//    		print_r($data);exit;
    	   	$GLOBALS['tmpl']->display("deal.html");
    	}
	}

	public function add_collect(){
	    global_run();
	    init_app_page();
	    
	
	    $param=array();
	    $param['id'] = intval($_REQUEST['id']);
	    $data = call_api_core("deal","add_collect",$param);
	    ajax_return($data);
	}
	public function del_collect(){
		global_run();
		init_app_page();
		$param=array();
		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("deal","del_collect",$param);
		ajax_return($data);
	}

    public function dp_detail(){
        global_run();
        $param = array(
            'page' => intval($_REQUEST['page']),
            'data_id' => intval($_REQUEST['data_id']),
        );

        $list = call_api_core('deal', 'ajax_dp_list', $param);
        $list['supplier_info']=$list['supplier_info'];
        $list['dp_list']=$list['list'];
        $GLOBALS['tmpl']->assign("data",$list);
        $GLOBALS['tmpl']->display('deal_dp_detail.html');
    }

    /**
     * @desc 获取推荐列表的界面
     * @author    吴庆祥
     */
    public function get_recommend_data(){
        global_run();
        init_app_page();
        $param=array();
        $param["data_id"] = intval($_REQUEST['data_id']);
        $data = call_api_core("deal","get_recommend_data",$param);
        $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display('deal_recommend_data.html');
    }
	public function ajax_dp_list()
	{
		global_run();
		$param = array(
			'page' => intval($_REQUEST['page']),
			'data_id' => intval($_REQUEST['data_id']),
		);

		$list = call_api_core('deal', 'ajax_dp_list', $param);
		if ($list) {
			$GLOBALS['tmpl']->assign('dp_list', $list['list']);
			$html = $GLOBALS['tmpl']->fetch('deal_dp_list.html');
		} else {
			$html = '';
		}
		$data['html'] = $html;
		ajax_return($data);

	}
}
?>