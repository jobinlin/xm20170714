<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class youhuisModule extends MainBaseModule
{
	public function index() 
	{		
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉

		require_once(APP_ROOT_PATH."system/model/youhui.php");
		/* //浏览历史
		$history_ids = get_view_history("youhui");
		//浏览历史
		if($history_ids)
		{
			$ids_conditioin = " y.id in (".implode(",", $history_ids).") ";
			$history_deal_list = get_youhui_list(app_conf("SIDE_DEAL_COUNT"),array(YOUHUI_ONLINE),array("city_id"=>$GLOBALS['city']['id']),"",$ids_conditioin);
				
			
			//重新组装排序
			$history_list = array();
			foreach($history_ids as $k=>$v)
			{
				foreach($history_deal_list['list'] as $history_item)
				{
					if($history_item['id']==$v)
					{
						$history_list[] = $history_item;
					}
				}
			}
				
			$GLOBALS['tmpl']->assign("history_deal_list",$history_list);
		} */
		
		//参数处理
		$deal_cate_id = intval($_REQUEST['cid']);
		$page=intval($_REQUEST['page']);
		
		//$GLOBALS['tmpl']->assign("cid",$deal_cate_id?$deal_cate_id:0);
		
		//输出自定义的filter_row
		/* array(
				"nav_list"=>array(
						array( //导航类型的切换
							"current"=>array("name"=>'xxx',"url"=>"当前的地址","cancel"=>"取消的地址"),
							"list"=>array(
									array("name"=>"xxx","url"=>"xxx")
								)
						)
				),
				"filter_list"=>array( //列表类型的切换
					array(
						"name"=>"分类",
						"list"	=> array(
								array("name"=>"xxx","url"=>"xxx")
						)
					)		
				)
			
		); */

		//seo元素
		$page_title = "优惠券";
		$page_keyword = "优惠券";
		$page_description = "优惠券";
		
		$city_id = $GLOBALS['city']['id'];
		
		$cate_sql="select dc.id,dc.name,count(yl.id) as yl_count from ".DB_PREFIX."deal_cate as dc left join ".DB_PREFIX."youhui as yl on dc.id=yl.deal_cate_id and yl.is_effect=1 and (yl.youhui_type=2 or (yl.youhui_type=1 and yl.city_id=".$city_id.")) where dc.is_delete=0 and dc.is_effect=1 group by dc.id";
		$cate_info=$GLOBALS['db']->getAll($cate_sql);
		
		$new_cate=array();
		$new_cate[0]['id']=0;
		$new_cate[0]['name']="精选";
		$new_cate[0]['url']=url("index","youhuis");
		if(!$deal_cate_id){
		    $new_cate[0]['is_checked']=1;
		}
		
		$cate_status=0;
		foreach ($cate_info as $t => $v){
		    $v['url']=url("index","youhuis",array("cid"=>$v['id']));
		    
		    if($v['id']==$deal_cate_id){
		        $v['is_checked']=1;
		        if($v['yl_count']>0)
		          $cate_status=true;
		    }
		    
		    if($v['yl_count']>0){
		        $new_cate[$v['id']]=$v;
		    }
		    
		}
		
		if($cate_status==false){
		    $new_cate[0]['is_checked']=1;
		    $deal_cate_id=0;
		}
		
		$GLOBALS['tmpl']->assign("cate_count",count($new_cate));
		
		$GLOBALS['tmpl']->assign("cate_info",$new_cate);
		
		//输出排序
		$sort_row_data = array();
		/* $sort_row_data = array(
			"sort"	=> array(
				array("name"=>"xxx","key"=>"xxx","type"=>"desc|asc","url"=>"xxx","current"=>"true|false")		
			),
			"range"	=> array(
				array
				(
					array("name"=>"xxx","url"=>"xxx","selected"=>"true|false"),
					array("name"=>"xxx","url"=>"xxx","selected"=>"true|false"),
					array("name"=>"xxx","url"=>"xxx","selected"=>"true|false"),
					array("name"=>"xxx","url"=>"xxx","selected"=>"true|false"),
				)
			),
			"tag"	=> array(
				array("name"=>"xxx","url"=>"xxx","checked"=>"true|false")
			)		
		); */
		
		
		//开始获取优惠券
		//获取排序条件 
		require_once(APP_ROOT_PATH."app/Lib/page.php");
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$page_size=12;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		if($deal_cate_id)
		    $where = " y.deal_cate_id= ".$deal_cate_id." and ";
		
		$youhui_sql="select y.id,y.name,y.supplier_id,y.youhui_type,y.youhui_value,y.start_use_price,y.valid_type,y.expire_day,y.use_end_time,s.name as supplier_name, 
		    case when (end_time<>0 and end_time<".NOW_TIME.") then 2 when (y.total_num<>0 and y.total_num<=y.user_count) then 1 else 0 end sort_status  
		    from ".DB_PREFIX."youhui as y left join ".DB_PREFIX."supplier as s on s.id=y.supplier_id
		    where ".$where." y.is_effect=1 and (y.youhui_type=2 or (y.youhui_type=1 and y.city_id=".$city_id.")) and (y.valid_type=1 or (y.valid_type=2 and (y.use_end_time=0 or y.use_end_time>".NOW_TIME."))) and (y.begin_time < ".NOW_TIME." or y.begin_time = 0) order by sort_status,y.create_time desc limit ".$limit;
        $youhui_count="select count(*) from ".DB_PREFIX."youhui as y where ".$where." y.is_effect=1  and (y.begin_time < ".NOW_TIME." or y.begin_time = 0) and (y.valid_type=1 or (y.valid_type=2 and (y.use_end_time=0 or y.use_end_time>".NOW_TIME."))) and  (y.youhui_type=2 or (y.youhui_type=1 and y.city_id=".$city_id."))";
		
		$youhui_list = $GLOBALS['db']->getAll($youhui_sql);		
		
		foreach ($youhui_list as $t => $v){
		    $youhui_list[$t]['use_end_time']=$v['use_end_time']?to_date($v['use_end_time']):0;
		}
		
		$total = $GLOBALS['db']->getOne($youhui_count);
		$page = new Page($total,$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign('youhui_list',$youhui_list);
		
		$GLOBALS['tmpl']->assign("page_title",$page_title);
		$GLOBALS['tmpl']->assign("page_keyword",$page_keyword);
		$GLOBALS['tmpl']->assign("page_description",$page_description);
		
		$GLOBALS['tmpl']->display("youhuis.html");
	}
}
?>