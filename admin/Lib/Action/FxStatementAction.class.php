<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class FxStatementAction extends CommonAction{


	public function __call($method, $arguments)
	{
		$method = strim($method);
		switch ($method) {
			case 'salary':
				$type = 1;
				break;
			case 'promote':
				$type = 2;
				break;
			case 'withdraw':
				$type = 3;
				break;
			case 'refer':
				$type = 4;
				break;
			case 'ref_promote':
				$type = 5;
				break;
			case 'store_payment_promote':
				$type = 6;
				break;
			case 'sales':
			default:
				$type = 0;
				break;
		}
		$_REQUEST['type'] = $type;
		$this->assign('method', $method);
		$this->assign('type', $type);
		$this->_index();
	}

	public function index()
	{
// 		8..订单收入明细、2. 充值收入收细、5商户提现明细、4会员提现明细、6会员退款明细
		
		$type = intval($_REQUEST['type']);
		
		switch ($type) {
			case '1':
				$balance_title = '分销佣金';
				break;
			case 2:
				$balance_title = '推广佣金';
				break;
			case 3:
				$balance_title = '分销提现';
				break;
			case 4:
				$balance_title = '推荐返佣';
				break;
			default:
				$balance_title = '营业额报表';
				break;
		}
		
		$year = intval($_REQUEST['year']);
		$month = intval($_REQUEST['month']);
		
		$current_year = intval(to_date(NOW_TIME,"Y"));
		$current_month = intval(to_date(NOW_TIME,"m"));
		
		if($year==0)$year = $current_year;
		if($month==0)$month = $current_month;
		
		$year_list = array();
		for($i=$current_year-10;$i<=$current_year+10;$i++)
		{
			$current = $year==$i?true:false;
			$year_list[] = array("year"=>$i,"current"=>$current);
		}
		
		$month_list = array();
		for($i=1;$i<=12;$i++)
		{
			$current = $month==$i?true:false;
			$month_list[] = array("month"=>$i,"current"=>$current);
		}
		
		
		$this->assign("year_list",$year_list);
		$this->assign("month_list",$month_list);
		
		$this->assign("cyear",$year);
		$this->assign("cmonth",$month);
		
		
		$begin_time = $year."-".str_pad($month,2,"0",STR_PAD_LEFT)."-01";
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i:s");
		
		$next_month = $month+1;
		$next_year = $year;
		if($next_month > 12)
		{
			$next_month = 1;
			$next_year = $next_year + 1;
		}
		$end_time = $next_year."-".str_pad($next_month,2,"0",STR_PAD_LEFT)."-01";
		$end_time_s = to_timespan($end_time,"Y-m-d H:i:s");
		
		$this->assign("balance_title",$year."-".str_pad($month,2,"0",STR_PAD_LEFT)." ".$balance_title);
		$this->assign("month_title",$year."-".str_pad($month,2,"0",STR_PAD_LEFT));
		//
		
		$map['type'] = $type;
		$map['money'] = array("gt",0);
		if($begin_time_s&&$end_time_s) {
			$map['create_time'] = array("between",array($begin_time_s,$end_time_s));
		} elseif($begin_time_s) {
			$map['create_time'] = array("gt",$begin_time_s);
		} elseif($end_time_s) {
			$map['create_time'] = array("lt",$end_time_s);
		}

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		$model = D ("FxStatementsLog");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$sum_money = $model->where($map)->sum("money");
		$this->assign("sum_money",$sum_money);
		
		$voList = $this->get("list");
		$page_sum_money = 0;
		foreach($voList as $row)
		{
			$page_sum_money+=floatval($row['money']);
		}
		$this->assign("page_sum_money",$page_sum_money);
		
		
		
		//开始计算利润率		
		$stat_month = $year."-".str_pad($month,2,"0",STR_PAD_LEFT);		
		$sql = "select sum(sale_money) as sale_money,
				sum(fx_extend_salary) as fx_extend_salary,
				sum(fx_salary) as fx_salary from ".DB_PREFIX."fx_statements where stat_month = '".$stat_month."'";
		$stat_result = $GLOBALS['db']->getRow($sql);
		
		
		$fx_cost = floatval($stat_result['fx_extend_salary']) + floatval($stat_result['fx_salary']);

		$this->assign("fx_cost",$fx_cost);
		$this->assign("stat_result",$stat_result);
		
		$this->display ('index');
		return;
	}
	
	
	public function foreverdelete() {
		$year = intval($_REQUEST['year']);
		$month = intval($_REQUEST['month']);
		
		if($year==0||$month==0)
		{
			$this->error("请选择日期");
		}
		
		
		$begin_time = $year."-".str_pad($month,2,"0",STR_PAD_LEFT)."-01";
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i:s");
		
		$next_month = $month+1;
		$next_year = $year;
		if($next_month > 12)
		{
			$next_month = 1;
			$next_year = $next_year + 1;
		}
		$end_time = $next_year."-".str_pad($next_month,2,"0",STR_PAD_LEFT)."-01";
		$end_time_s = to_timespan($end_time,"Y-m-d H:i:s");
		
		$stat_month = $year."-".str_pad($month,2,"0",STR_PAD_LEFT);
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."fx_statements_log where create_time between $begin_time_s and $end_time_s");
		$GLOBALS['db']->query("delete from ".DB_PREFIX."fx_statements where stat_month = '".$stat_month."'");
		
		$this->error("清空成功");
		
	}
	
	public function _index()
	{
// 		8..订单收入明细、2. 充值收入收细、5商户提现明细、4会员提现明细、6会员退款明细
		
		$type = intval($_REQUEST['type']);
		
		switch ($type) {
			case 3:
				$balance_title = '分销提现';
				break;
			case 4:
				$balance_title = '推荐返佣';
				break;
			case 2:
			default:
				$balance_title = '推广佣金';
				break;
		}
		
		$year = intval($_REQUEST['year']);
		$month = intval($_REQUEST['month']);
		$user_name = isset($_REQUEST['user_name']) ? strim($_REQUEST['user_name']) : '';
		
		$user_where = '';
		if ($user_name) {
			$ids = M('user')->field('id')->where(array('user_name' => array('like', '%'.$user_name.'%')))->findAll();
			$user_id = array(0);
			if ($ids) {
				foreach ($ids as $id) {
					$user_id[] = $id['id'];
				}
			}
			$user_where = ' AND id in ('.implode(',', $user_id).') ';
			$map['fx.user_id'] = array('in', $user_id);
			
		}
		$this->assign("user_name",$user_name);

		$current_year = intval(to_date(NOW_TIME,"Y"));
		$current_month = intval(to_date(NOW_TIME,"m"));
		
		if($year==0)$year = $current_year;
		if($month==0)$month = $current_month;
		
		$year_list = array();
		for($i=$current_year-10;$i<=$current_year+10;$i++)
		{
			$current = $year==$i?true:false;
			$year_list[] = array("year"=>$i,"current"=>$current);
		}
		
		$month_list = array();
		for($i=1;$i<=12;$i++)
		{
			$current = $month==$i?true:false;
			$month_list[] = array("month"=>$i,"current"=>$current);
		}
		
		$this->assign("year_list",$year_list);
		$this->assign("month_list",$month_list);
		
		$this->assign("cyear",$year);
		$this->assign("cmonth",$month);
		
		$begin_time = $year."-".str_pad($month,2,"0",STR_PAD_LEFT)."-01";
		$begin_time_s = to_timespan($begin_time,"Y-m-d H:i:s");
		
		$next_month = $month+1;
		$next_year = $year;
		if($next_month > 12)
		{
			$next_month = 1;
			$next_year = $next_year + 1;
		}
		$end_time = $next_year."-".str_pad($next_month,2,"0",STR_PAD_LEFT)."-01";
		$end_time_s = to_timespan($end_time,"Y-m-d H:i:s");
		
		$this->assign("balance_title",$year."-".str_pad($month,2,"0",STR_PAD_LEFT)." ".$balance_title);
		$this->assign("month_title",$year."-".str_pad($month,2,"0",STR_PAD_LEFT));
		//
		
		$map['fx.type'] = $type;
		$map['fx.money'] = array("gt",0);
		if($begin_time_s&&$end_time_s) {
			$map['fx.create_time'] = array("between",array($begin_time_s,$end_time_s));
		} elseif($begin_time_s) {
			$map['fx.create_time'] = array("gt",$begin_time_s);
		} elseif($end_time_s) {
			$map['fx.create_time'] = array("lt",$end_time_s);
		}

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		$model = D ("FxStatementsLog");
		if (! empty ( $model )) {
			$this->_userlist ( $model, $map );
		}
		
		$sum_money = $model->table(DB_PREFIX.'fx_statements_log fx')->where($map)->sum("fx.money");

		$this->assign("sum_money",$sum_money);
		
		// 分销佣金计算
		$map['fx.type'] = 2;
		$sum_salary = $model->table(DB_PREFIX.'fx_statements_log fx')->where($map)->sum("fx.money");
		$this->assign('sum_salary', $sum_salary);

		$voList = $this->get("list");
		$page_sum_money = 0;
		foreach($voList as $row)
		{
			$page_sum_money+=floatval($row['money']);
		}
		$this->assign("page_sum_money",$page_sum_money);

		$this->assign("fx_cost",$fx_cost);
		$this->assign("stat_result",$stat_result);
		
		$this->display ('index');
		return;
	}


	private function _userlist($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		$tablePrefix = C('DB_PREFIX');
		//取得满足条件的记录数
		$count = $model->table($tablePrefix.'fx_statements_log fx')->where ( $map )->count ( 'fx.id' );

		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
			
			$voList = $model->table($tablePrefix.'fx_statements_log fx')->join($tablePrefix.'user u on fx.user_id=u.id')->field('fx.*,u.user_name')->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->findAll ();

			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式

			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page);
			$this->assign ( "nowPage",$p->nowPage);
		}
		return;
	}

	/**
	 * 图表的数据重新处理
	 * @return json 
	 */
	public function fx_sale_month_line()
	{
		$year = intval($_REQUEST['year']);
		$month = intval($_REQUEST['month']);
		$user_name = strim($_REQUEST['user_name']);
		$type = intval($_REQUEST['type']);
		$user_where = '';
		if ($user_name) {
			$ids = D('user')->where(array('user_name' => array('like', '%'.$user_name.'%')))->field('id')->select();
			$user_ids = array(0);
			if ($ids) {
				foreach ($ids as $item) {
					$user_ids[] = $item['id'];
				}
			}
			$user_where = ' AND user_id in ('.implode(',', $user_ids).') ';
		}
		
		$current_year = intval(to_date(NOW_TIME,"Y"));
		$current_month = intval(to_date(NOW_TIME,"m"));
		
		if($year==0)$year = $current_year;
		if($month==0)$month = $current_month;
		
		$days_list = array(31,28,31,30,31,30,31,31,30,31,30,31);
		$days = $days_list[$month-1];
		if($days==28&&$year%4==0&&($year%100!=0||$year%400==0)) {
			$days = 29;
		}
				
		$stat_month = $year."-".str_pad($month,2,"0",STR_PAD_LEFT);
		
		//月数据	
		$x_labels = array();  //x轴的标题
		for($i=1;$i<=$days;$i++) {
			$x_labels[] = $i."日";
		}
		$result['x_axis'] = array("labels"=>array("labels"=>$x_labels));

		$start_time = mktime(0,0,0,$month,1,$year);
		$end_time = mktime(0,0,0,$month + 1,1,$year) - 1;

		$sql = 'SELECT SUM(money) AS salary, FROM_UNIXTIME(create_time, "%Y-%m-%d") AS stat_time FROM '.DB_PREFIX.'fx_statements_log WHERE create_time BETWEEN '.$start_time.' AND '.$end_time.' AND type='.$type.$user_where.' GROUP BY stat_time';

		$stat_result = D()->query($sql); // $GLOBALS['db']->getAll($sql);
	
		//开始定义每个数据的线条元素
		$max_value = 0;
	
		switch ($type) {
			case 3:
				$balance_title = '分销提现';
				break;
			case 4:
				$balance_title = '推荐返佣';
				break;
			case 2:
			default:
				$balance_title = '推广佣金';
				break;
		}
	
		//推广佣金线条元素
		$fx_extend_salary_line_values = array();
		for($i=1;$i<=$days;$i++)
		{
			$stat_time = $stat_month."-".str_pad($i,2,"0",STR_PAD_LEFT);
			$data_row = array("value"=>0,"tip"=>$stat_time.$balance_title."0元");
			foreach($stat_result as $row)
			{
				if($row['stat_time']==$stat_time)
				{
					if($row['salary']>$max_value)$max_value = $row['salary'];
					$data_row = array("value"=>floatval($row['salary']),"tip"=>$stat_time.$balance_title.round($row['salary'],2)."元");
				}
			}
			$fx_extend_salary_line_values[] = $data_row;
		}
		$fx_extend_salary_line_element = array("type"=>"line","colour"=>VERIFY_COLOR,"text"=>$balance_title,"width"=>2,"values"=>$fx_extend_salary_line_values);
	
		$max_value = ofc_max($max_value);

		$result['y_axis'] = array("max"=>floatval($max_value));
		$result['elements'] = array($fx_extend_salary_line_element);
		$result['bg_colour']	= "#ffffff";

		ajax_return($result);
	}
}
?>