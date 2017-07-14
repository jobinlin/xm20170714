<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class FxOrderAction extends CommonAction{	
	/*
	 * 分销订单
	 */
	public function index()
	{
		$condition = " 1=1 ";
		if(strim($_REQUEST['order_sn'])!="")	$condition .= " and o.order_sn = ".strim($_REQUEST['order_sn']);
		if(intval($_REQUEST['deal_id'])>0) $condition .= " and i.deal_id = ".intval($_REQUEST['deal_id']);
		if(strim($_REQUEST['user_name'])!=''){
			$condition .= " and o.user_name = '".strim($_REQUEST['user_name'])."' ";
		}

		if(strim($_REQUEST['order_status'])==1)$condition .= " and o.order_status = 0";
		if(strim($_REQUEST['order_status'])==2)$condition .= " and o.order_status = 1";

		$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item as i left join ".DB_PREFIX."deal_order as o on i.order_id=o.id where i.fx_user_id>0 and ".$condition);
		$p = new Page ( $count);
		$limit=$p->firstRow . ',' . $p->listRows;
		$list =$GLOBALS['db']->getAll("select i.*,o.order_status,o.order_sn,o.deal_order_item from ".DB_PREFIX."deal_order_item as i left join ".DB_PREFIX."deal_order as o on i.order_id=o.id where i.fx_user_id>0 and ".$condition." order by o.create_time desc limit ".$limit);
		foreach ($list as $k=>$v){

			$v['fx_salary_all']=unserialize($v['fx_salary_all']);
			// $list[$k]['fx_salary_all']="销售佣金".round($v['fx_salary_all'][0],2)."元，一级推广佣金".round($v['fx_salary_all'][1],2)."元，二级推广佣金".round($v['fx_salary_all'][2],2)."元";
			$str = '';
			foreach (array('一', '二', '三') as $i => $n) {
				if (empty($v['fx_salary_all'][$i+1])) {
					break;
				}
				$str .= $n.'级推广佣金'.round($v['fx_salary_all'][$i + 1], 2).'元';
			}
			$list[$k]['fx_salary_all'] = $str;
			if($v['order_status']==0){
				$list[$k]['fx_salary_all']='-';
				$list[$k]['fx_salary']='-';
				$list[$k]['fx_salary_total']='-';
			}elseif($v['order_status']==1){
				$list[$k]['fx_salary']=format_price($v['fx_salary']);
				$list[$k]['fx_salary_total']=format_price($v['fx_salary_total']);	
			}
			foreach(unserialize($v['deal_order_item']) as $kk=>$vv){
				if($vv['id']==$list[$k]['id']) $list[$k]['deal_name']="ID：".$list[$k]['id']."。".msubstr($vv['name'],0,25);
			}
			//print_r(unserialize($v['deal_order_item']));exit;
		}
		$page = $p->show ();
		$this->assign("list",$list);
		$this->assign ( "page", $page );
		$this->display ();
		return;
	}
	
	/*
	 * 分销订单
	 */
	public function ref_index()
	{
		$condition = " 1=1 ";
		if(strim($_REQUEST['order_sn'])!="")	$condition .= " and o.order_sn = ".strim($_REQUEST['order_sn']);
		//if(intval($_REQUEST['deal_id'])>0) $condition .= " and i.deal_id = ".intval($_REQUEST['deal_id']);
		if(strim($_REQUEST['user_name'])!=''){
			$condition .= " and o.user_name = '".strim($_REQUEST['user_name'])."' ";
		}

		if(strim($_REQUEST['order_status'])==1)$condition .= " and o.order_status = 0";
		if(strim($_REQUEST['order_status'])==2)$condition .= " and o.order_status = 1";

		$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order as o where o.is_participate_ref_salary=1 and ".$condition);
		$p = new Page ( $count);
		$limit=$p->firstRow . ',' . $p->listRows;
		$list =$GLOBALS['db']->getAll("select o.* from ".DB_PREFIX."deal_order as o where o.is_participate_ref_salary=1 and ".$condition." order by o.create_time desc limit ".$limit);
		//echo "<pre>";print_r($list);exit;
		foreach ($list as $k=>$v){
			$v['ref_salary_all']=unserialize($v['ref_salary_all']);
			if($v['ref_salary_all']['ref_salary_all']['0']){
				$salary=$v['ref_salary_all']['ref_salary_all']['0'];
				$v['ref_salary1']=$salary['user_name'].'：¥'.round($salary['salary'],2);
			}else{
				$v['ref_salary1']="-";
			}
			if($v['ref_salary_all']['ref_salary_all']['1']){
				$salary=$v['ref_salary_all']['ref_salary_all']['1'];
				$v['ref_salary2']=$salary['user_name'].'：¥'.round($salary['salary'],2);
			}else{
				$v['ref_salary2']="-";
			}
			if($v['ref_salary_all']['ref_salary_all']['2']){
				$salary=$v['ref_salary_all']['ref_salary_all']['2'];
				$v['ref_salary3']=$salary['user_name'].'：¥'.round($salary['salary'],2);
			}else{
				$v['ref_salary3']="-";
			}
			$v['log']=$v['ref_salary_all']['log']?$v['ref_salary_all']['log']:'-';
			$list[$k]=$v;
		}
		$page = $p->show ();
		$this->assign("list",$list);
		$this->assign ( "page", $page );
		$this->display ();
		return;
	}
	/*
	 * 分销订单
	 */
	public function store_pay_index()
	{
		$condition = " 1=1 ";
		if(strim($_REQUEST['order_sn'])!="")	$condition .= " and o.order_sn = ".strim($_REQUEST['order_sn']);
		//if(intval($_REQUEST['deal_id'])>0) $condition .= " and i.deal_id = ".intval($_REQUEST['deal_id']);
		if(strim($_REQUEST['user_mobile'])!=''){
			$condition .= " and o.user_mobile = '".strim($_REQUEST['user_mobile'])."' ";
		}

		$count =$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."store_pay_order as o where o.is_participate_ref_salary=1 and ".$condition);
		$p = new Page ( $count);
		$limit=$p->firstRow . ',' . $p->listRows;
		$list =$GLOBALS['db']->getAll("select s.ref_user_id,s.name,o.* from ".DB_PREFIX."store_pay_order as o LEFT JOIN ".DB_PREFIX."supplier s on o.supplier_id=s.id where o.is_participate_ref_salary=1 and ".$condition." order by o.create_time desc limit ".$limit);
		foreach ($list as $k=>$v){
			$v['ref_salary_all']=unserialize($v['ref_salary_all']);
			if($v['ref_salary_all']['ref_salary_all']['0']){
				$salary=$v['ref_salary_all']['ref_salary_all']['0'];
				$v['ref_salary1']=$salary['user_name'].'：¥'.$salary['salary'];
			}else{
				$v['ref_salary1']="-";
			}
			if($v['ref_salary_all']['ref_salary_all']['1']){
				$salary=$v['ref_salary_all']['ref_salary_all']['1'];
				$v['ref_salary2']=$salary['user_name'].'：¥'.$salary['salary'];
			}else{
				$v['ref_salary2']="-";
			}
			if($v['ref_salary_all']['ref_salary_all']['2']){
				$salary=$v['ref_salary_all']['ref_salary_all']['2'];
				$v['ref_salary3']=$salary['user_name'].'：¥'.$salary['salary'];
			}else{
				$v['ref_salary3']="-";
			}
			$v['log']=$v['ref_salary_all']['log'];
			$list[$k]=$v;
		}
		$page = $p->show ();
		$this->assign("list",$list);
		$this->assign ( "page", $page );
		$this->display ();
		return;
	}

	
	
}
?>