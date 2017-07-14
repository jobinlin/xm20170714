<?php 


/**
* 驿站相关方法
*/
class Distribute
{

	/**
	 * 为订单分配驿站
	 * @param int $consig_id 会员配送地址id
	 * @return int 分配到的驿站
	 */
	public static function setDist($consig_id)
	{
		$cSql = 'SELECT region_lv3, xpoint, ypoint FROM '.DB_PREFIX.'user_consignee WHERE id = '.$consig_id;
		// logger::write('会员配送地址查询: '.$cSql);
		$points = $GLOBALS['db']->getRow($cSql);
		if (empty($points['xpoint']) || empty($points['ypoint'])) {
			return 0;
		}

		$fSql = 'SELECT %s FROM '.DB_PREFIX.'distribution WHERE %s';

		$version_sql = 'SELECT version()';
		$version = $GLOBALS['db']->getOne($version_sql);
		// logger::write('版本号: '.$version);
		$vergt56 = false; // 标记mysql版本是否大于5.6
		if (strncmp('5.6', $version, 3) == 1) { // 5.6 版本以下
			// logger::write('5.6 以下版本');
			// 查询地址所属城市的所有配送驿站
			$field = 'id,xpoints,ypoints';
			$where = 'city_id = '.$points['region_lv3'].' AND is_delete = 0 AND status = 1 AND disabled = 0';
			$sql = sprintf($fSql, $field, $where);
			// logger::write('查询城市驿站: '.$sql);
			$dists = $GLOBALS['db']->getAll($sql);
			if ($dists) { // 遍历所有驿站，匹配出包含配送地址坐标的驿站点
				foreach ($dists as $d) {
					if (static::isPtInPoly($points['xpoint'], $points['ypoint'], $d)) {
						return $d['id'];
					}
				}
			}
		} else { // 5.6 以上
			// logger::write('5.6版本');
			$field = 'id';
			$where = "ST_Contains(points, GeomFromText('Point(".$points['xpoint'].' '.$points['ypoint'].")')) AND is_delete = 0 AND status = 1 LIMIT 1";
			// 查询覆盖配送地址区域的配送驿站
			$sql = sprintf($fSql, $field, $where);
			$dist_id = $GLOBALS['db']->getOne($sql);
			if ($dist_id) {
				return $dist_id;
			}
			$vergt56 = true;
		}
		// 没有找到驿站，开始找点
		return static::closest($points['xpoint'], $points['ypoint'], $vergt56);
	}


	/**
	 * 判断坐标点是否在区域范围内
	 * @param  float  $x  经度
	 * @param  float  $y  纬度
	 * @param  array   $ps 配送驿站的信息数组(id,xpoint,ypoint)
	 * @return boolean     
	 */
	private static function isPtInPoly($x, $y, array $ps)
	{
		$xpoints = explode(',', $ps['xpoints']);
		$ypoints = explode(',', $ps['ypoints']);
		if (count($xpoints) < 3 || count($xpoints) != count($ypoints)) {
			return false;
		}
		// logger::write('驿站的坐标合法性检测');
		$count = count($xpoints);
		$sum = 0;
		for ($i=0; $i < $count; $i++) {
			$x1 = $xpoints[$i];
			$y1 = $ypoints[$i];
			if ($i == $count - 1) {
				$x2 = $xpoints[0];
				$y2 = $ypoints[0];
			} else {
				$x2 = $xpoints[$i+1];
				$y2 = $ypoints[$i+1];
			}
			// 先判断坐标是否在两个端点的水平平行线之间，有则可能有交点
			if ((($y >= $y1) && ($y < $y2)) || (($y >= $y2) && ($y < $y1))) {
				if (abs($y1 - $y2) > 0) {
					// 求出坐标向左射线与边的交点的x坐标
					$dLon = $x1 - (($x1 - $x2) * ($y1 - $y)) / ($y1 - $y2);
					// 如果交点在A点左侧，则有交点
					if ($dLon < $x) {
						$sum++;
					}
				}
			}
		}
		// 交点数位偶数，表示在区域内
		if (($sum % 2) != 0) {
			return true;
		}

		return (($sum % 2) ? true : false);
	}

	/**
	 * 搜索最近的点并判断点是否在指定距离内
	 * @param  float  $x       经度
	 * @param  float  $y       纬度
	 * @param  boolean  $vergt56 mysql版本是否大于5.6
	 * @param  integer $mindist 指定距离范围
	 * @return int           
	 */
	private static function closest($x, $y, $vergt56, $mindist = 1024)
	{
		$pi = PI;
		if ($vergt56) {
			$dist = 'ST_Distance(POINT('.$x.','.$y."),POINT(xpoint,ypoint)) * $pi / 180 as dist";
		} else {
			
			$dist = "ACOS(SIN(($y * $pi) / 180 ) *SIN((ypoint * $pi) / 180 ) +COS(($y * $pi) / 180 ) * COS((ypoint * $pi) / 180 ) *COS(($x * $pi) / 180 - (xpoint * $pi) / 180 ) ) as dist ";
		}
		$sql = 'SELECT dist_id, '.$dist.' FROM '.DB_PREFIX.'distribution_shipping WHERE is_delete = 0 AND disabled = 0 ORDER BY dist';
		$distInfo = $GLOBALS['db']->getRow($sql);
		if ($distInfo && ($distInfo['dist_id'] * EARTH_R) < $mindist) {
			return $distInfo['dist_id'];
		}
		return 0;
	}
}