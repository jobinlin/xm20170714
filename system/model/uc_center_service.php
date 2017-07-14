<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//查询会员邀请及返利列表
function get_invite_list($limit,$user_id)
{
	$user_id = intval($user_id);
	$sql = "select u.user_name as i_user_name,u.referral_count as i_referral_count,u.create_time as i_reg_time,o.order_sn as i_order_sn,r.create_time as i_referral_time, r.pay_time as i_pay_time,r.money as i_money,r.score as i_score from ".DB_PREFIX."user as u left join ".DB_PREFIX."referrals as r on u.id = r.rel_user_id and u.pid = r.user_id left join ".DB_PREFIX."deal_order as o on r.order_id = o.id where u.pid = ".$user_id." limit ".$limit;
	$sql_count = "select count(*) from ".DB_PREFIX."user where pid = ".$user_id;
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

function get_collect_list($limit,$user_id)
{
	$user_id = intval($user_id);
	$now_time=NOW_TIME;
	$sql = "select d.id,d.name,d.sub_name,d.end_time,d.origin_price,d.current_price,d.buy_count,d.buy_type,d.brief,d.icon, case when d.end_time < ".$now_time." and d.end_time > 0 then 0 else 1 end as is_valid,c.create_time as add_time ,c.id as cid from ".DB_PREFIX."deal_collect as c left join ".DB_PREFIX."deal as d on d.id = c.deal_id where c.user_id = ".$user_id." and d.is_delete = 0 and d.is_effect = 1 order by is_valid desc,c.create_time desc limit ".$limit;
	$sql_count = "select count(*) from ".DB_PREFIX."deal_collect where user_id = ".$user_id;
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

function get_youhui_collect($limit,$user_id)
{
	$user_id = intval($user_id);
	$now_time=NOW_TIME;
	$sql = "select y.id,y.name,y.icon,y.user_count,y.list_brief,y.begin_time,y.end_time,y.score_limit,y.point_limit, case when y.end_time < ".$now_time." and y.end_time > 0 then 0 else 1 end as is_valid,c.uid,c.add_time,c.id as cid  from ".DB_PREFIX."youhui_sc as c left join ".DB_PREFIX."youhui as y on y.id = c.youhui_id where c.uid = ".$user_id." and y.is_effect=1 order by is_valid desc,y.end_time desc limit ".$limit;
	$sql_count = "select count(*) from ".DB_PREFIX."youhui_sc where uid = ".$user_id;
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

function get_event_collect($limit,$user_id)
{
	$user_id = intval($user_id);
	$now_time=NOW_TIME;
	$sql = "select e.id,e.name,e.score_limit,e.point_limit,e.img,e.icon,e.brief,e.submit_count,e.submit_end_time, case when e.event_end_time < ".$now_time." and e.event_end_time > 0 then 0 when e.submit_end_time < ".$now_time." and e.submit_end_time > 0 then 0 else 1 end as is_valid,c.uid,c.add_time,c.id as cid  from ".DB_PREFIX."event_sc as c left join ".DB_PREFIX."event as e on e.id = c.event_id where c.uid = ".$user_id." and e.is_effect=1 order by is_valid desc,e.event_end_time desc limit ".$limit;
	$sql_count = "select count(*) from ".DB_PREFIX."event_sc where uid = ".$user_id;
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

function get_dc_location_collect($limit,$user_id,$geo,$ypoint,$xpoint,$pi,$r)
{
    $user_id = intval($user_id);
    $now_time=NOW_TIME;
//     $sql = "select sl.id ,sl.name,sl.preview,sl.city_id,sl.avg_point,sl.dp_count,dci.name as city_name,sc.add_time,sc.id as link_id, min(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r) as distance from ".DB_PREFIX."supplier_location as sl 
//         left join ".DB_PREFIX."dc_location_sc as sc on sl.id= sc.location_id 
//         left join ".DB_PREFIX."deal_city as dci on sl.city_id = dci.id 
//         where sc.user_id=".$user_id." order by sc.add_time desc limit ".$limit;
    $sql="select sl.id ,sl.name,sl.preview,sl.avg_point,sl.dp_count , sc.add_time,sc.id as link_id,ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((sl.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((sl.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (sl.xpoint * $pi) / 180 ) ) * $r as distance from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."dc_location_sc as sc on sl.id= sc.location_id where sc.user_id=".$user_id." order by sc.add_time desc limit ".$limit;
    $sql_count = "select count(*) from ".DB_PREFIX."dc_location_sc where user_id=".$user_id;
    $list = $GLOBALS['db']->getAll($sql);
    $count = $GLOBALS['db']->getOne($sql_count);
    return array("list"=>$list,'count'=>$count);
}

//查询代金券列表
function get_voucher_list($limit,$user_id)
{
	$user_id = intval($user_id);
	$sql="select e.id,e.password,e.use_count,e.end_time,e.money,ey.name,ey.start_use_price,
			        case when ( e.end_time < ".NOW_TIME." and e.end_time <> 0 ) then 0 when e.use_count = 1 then 1 else 2 end order_status
			        from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as ey on ey.id=e.ecv_type_id where e.user_id=".$user_id."
		            order by order_status desc, e.id desc limit ".$limit;
	$sql_count="select count(*) from ".DB_PREFIX."ecv where user_id=".$user_id;
	
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

//查询可兑换代金券列表
function get_exchange_voucher_list($limit,$user_id,$score)
{
	$user_id = intval($user_id);
	$sql = "select et.*,count(e.id) as user_count,case when et.gen_count>=et.total_limit and et.total_limit<>0 then 0 when et.exchange_score>".$score." then 1 when count(e.id)>=et.exchange_limit and et.exchange_limit<>0 then 2 else 3 end order_status
			from ".DB_PREFIX."ecv_type as et left join ".DB_PREFIX."ecv as e on e.ecv_type_id =et.id and e.user_id=".$user_id."
			where et.send_type = 1 and (et.end_time>".NOW_TIME." or et.end_time = 0) and (et.begin_time<".NOW_TIME." or et.begin_time = 0)
			group by et.id order by order_status desc,et.id desc limit ".$limit;
	$sql_count = "select count(*) from ".DB_PREFIX."ecv_type where send_type = 1 and (end_time>".NOW_TIME." or end_time = 0) and (begin_time<".NOW_TIME." or begin_time = 0) ";

	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	return array("list"=>$list,'count'=>$count);
}

?>