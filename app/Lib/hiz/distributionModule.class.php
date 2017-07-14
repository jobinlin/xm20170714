<?php 



class distributionModule extends HizBaseModule
{
    
	public function __construct()
	{
        parent::__construct();
        global_run();
        init_app_page();
    }

    /**
     * 驿站列表
     * 请求参数：username => string   驿站名称搜索
     * 返回数据: list => array(
     *             id => int  驿站id
     *             name => string 驿站名称
     *             contact => string 联系人
     *             tel  => string 联系电话
     *             address => string 详细地址
     *             money => float 帐户余额
     *             service_total_money => float 累积收入
     *             unfee => float 待结算金额
     *             disabled => int 状态 0:正常  1:禁用         
     *         )
     *          page => 
     * @return mixed 
     */
    public function index()
    {
        $s_account_info = $GLOBALS["hiz_account_info"];
        $account_id = $s_account_info['id'];
        $where = ' is_delete=0 AND status=1 AND agency_id = '.$account_id;
    	$keyname = strim($_REQUEST['dist_key']);
        if (!empty($keyname)) {
            $where .= ' AND name like "%'.$keyname.'%"';
            $GLOBALS['tmpl']->assign('dist_key', $keyname);
        }
        $page_size = PAGE_SIZE;
        $page = intval($_REQUEST['p']);
        if($page==0) $page = 1;
        $limit = (($page-1)*$page_size).",".$page_size;

        $fields = 'id,name,contact,tel,address,money,service_total_money,disabled';

        $sql = 'SELECT '.$fields.' FROM '.DB_PREFIX.'distribution WHERE'.$where.' ORDER BY id DESC LIMIT '.$limit;
        $countSql = 'SELECT count(id) FROM '.DB_PREFIX.'distribution WHERE'.$where;
        $total = $GLOBALS['db']->getOne($countSql);

        $list = $GLOBALS['db']->getAll($sql);

        if ($list) {
            $dist_ids = array();
            foreach ($list as $v) {
                $dist_ids[] = $v['distribution_id'];
            }

            $unbalanceSql = 'SELECT d.`distribution_id`, sum(d.`distribution_fee`) AS fee FROM '.DB_PREFIX.'deal_order d LEFT JOIN '.DB_PREFIX.'deal_order_item AS di ON d.id=di.order_id WHERE d.pay_status=2 AND d.order_status 0 AND d.distribution_id in ('.implode(',', $dist_ids).') GROUP BY d.distribution_id';
            $unbalance = $GLOBALS['db']->getAll($unbalanceSql);
            $unkey_bal = array();
            foreach ($unbalance as $u) {
                $unkey_bal[$u['distribution_id']] = $u['fee'];
            }

            foreach ($list as &$item) {
                $fee = 0;
                if (array_key_exists($item['id'], $unkey_bal)) {
                    $fee =  $unkey_bal[$item['id']];
                }
                $item['unfee'] = $fee;
                $item['disabledStr'] = $item['disabled'] == 0 ? '正常' : '禁用';
                $item['money'] = round($item['money'], 2);
                $item['service_total_money'] = round($item['service_total_money'], 2);
            }unset($item);
        }
        $GLOBALS['tmpl']->assign('list', $list);

        formatPage($total, $page_size);

        $GLOBALS['tmpl']->display("pages/distribution/index.html");
    }

    /**
     * 编辑/新增驿站
     * 请求参数：id => int  有参为编辑，无参为新增
     * 返回数据： info => array(
     *                   username => string  帐户
     *                   name => string 驿站名称 
     *                   password => string 密码
     *                   country_id => int 县区id
     *                   address => string 驿站地址
     *                   contact => string 负责人
     *                   tel => string 联系电话 
     *                   xpoint => float 定位经度
     *                   ypoint => float 定位纬度 
     *                   xpoints => string 多边形经度集        
     *                   ypoints = string 多边形纬度集
     *                   open_time => string 营业时间
     *             )
     *             country => array(   // 代理商下辖县区信息数组
     *             
     *             )
     *             has_map => int  // 驿站多边形坐标是否存在
     *             xpoints => array // 驿站多边形经度
     *             ypoints => array // 驿站多边形纬度
     * @return
     */
    public function edit()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $h_city = $h_info['city_id'];

        // 省市信息
        $pcSql = 'SELECT * FROM '.DB_PREFIX.'delivery_region WHERE id in('.$h_info['province_id'].','.$h_city.') ORDER BY id';

        $pcInfo = $GLOBALS['db']->getAll($pcSql);
        $GLOBALS['tmpl']->assign('province', $pcInfo[0]);
        $GLOBALS['tmpl']->assign('city', $pcInfo[1]);

        // 县区列表
        $countrySql = 'SELECT * FROM '.DB_PREFIX.'delivery_region WHERE pid='.$h_city;
        $country = $GLOBALS['db']->getAll($countrySql);
        $GLOBALS['tmpl']->assign('country', $country);

    	if (isset($_REQUEST['id'])) {
            $sql = 'SELECT * FROM '.DB_PREFIX.'distribution WHERE id='.intval($_REQUEST['id'].' is_delete=0 AND agency_id='.$h_id);
            $info = $GLOBALS['db']->getRow($sql);
            if (!$info) { // 驿站不存在
                app_redirect(url('hiz', 'distribution#index'));
            }

            

            $xpoints = explode(',', $info['xpoints']);
            $ypoints = explode(',', $info['ypoints']);
            if (!empty($xpoints) && !empty($ypoints) && count($xpoints) == count($ypoints)) {
                $GLOBALS['tmpl']->assign('has_map', 1);
                $GLOBALS['tmpl']->assign('xpoints', $xpoints);
                $GLOBALS['tmpl']->assign('ypoints', $ypoints);
            }
            
            $GLOBALS['tmpl']->assign('info', $info);
        }
        $GLOBALS['tmpl']->display("pages/distribution/edit.html");
    }

    /**
     * 新增/编辑保存
     * @return  
     */
    public function save()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $status = 0;
        $info = '';
        $jump = '';
        $data = array();
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        do {
            $username = strim($_REQUEST['username']);
            if (empty($username)) {
                $info = '帐户名不能为空';
                break;
            }
            // 判断驿站账号是否存在
            $uchkSql = 'SELECT count(id) FROM '.DB_PREFIX.'distribution WHERE username="'.$username.'" AND is_delete=0 AND status != 2';
            if ($id) {
                $uchkSql .= ' AND id <> '.$id;
            }
            $uexist = $GLOBALS['db']->getOne($uchkSql);
            if ($uexist) {
                $info = '帐户名已经存在';
                break;
            }

            $name = strim($_REQUEST['name']);
            if (empty($name)) {
                $info = '驿站名称不能为空';
                break;
            }

            $password = strim($_REQUEST['password']);
            if (!$id && empty($password)) {
                $info = '密码不能为空';
                break;
            }

            $address = strim($_REQUEST['address']);
            if (empty($address)) {
                $info = '驿站地址不能为空';
                break;
            }

            $xpoint = floatval($_REQUEST['xpoint']);
            $ypoint = floatval($_REQUEST['ypoint']);
            $xpoints = strim($_REQUEST['xpoints']);
            $xsc = explode(',', $xpoints);
            $ypoints = strim($_REQUEST['ypoints']);
            $ysc = explode(',', $ypoints);
            if (empty($xpoint) || empty($ypoint) || count($xsc) <= 3 || count($xsc) != count($ysc)) {
                $info = '服务范围定位异常';
                break;
            }
            $pc = count($xpoints);
            $pos = array();
            for ($i=0; $i < $pc; $i++) { 
                $pos[] = array_shift($xsc).' '.array_shift($ysc);
            }
            $pos[] = current($pos);
            $points = 'GeomFromText("Polygon(('.implode(',', $pos).'))")';

            $contact = strim($_REQUEST['contact']);
            if (empty($contact)) {
                $info = '驿站负责人不能为空';
                break;
            }
            if (mb_strlen($contact, 'UTF-8') > 10) {
                $info = '驿站负责人姓名应不超过10个字';
                break;
            }

            $tel = strim($_REQUEST['tel']);
            if (empty($tel)) {
                $info = '手机号码不能为空';
                break;
            }
            if (!check_mobile($tel)) {
                $info = '手机号码格式有误';
                break;
            }

            $open_time = strim($_REQUEST['open_time']);
            if (empty($open_time)) {
                $info = '请填写营业时间';
                break;
            }

            $county_id = intval($_REQUEST['county_id']);
            if (empty($county_id)) {
                $info = '请选择一个县区';
                break;
            }

            $data = array(
                'username' => $username,
                'name' => $name,
                'address' => $address,
                'tel' => $tel,
                'contact' => $contact,
                'open_time' => $open_time,
                'county_id' => $county_id,
                'xpoint' => $xpoint,
                'ypoint' => $ypoint,
                'xpoints' => $xpoints,
                'ypoints' => $ypoints,
            );

            if (!$id || $password) {
                $data['password'] = md5($password);
            }
            if (!$id) {
                $data['prov_id'] = $h_info['province_id'];
                $data['city_id'] = $h_info['city_id'];
                $data['agency_id'] = $h_info['id'];
            }
            foreach ($data as &$val) {
                $val = '"'.$val.'"';
            }unset($val);
            $data['points'] = $points;

            if ($id) {
                $GLOBALS['db']->autoExecute_unsafe(DB_PREFIX.'distribution', $data, 'UPDATE', 'id='.$id);
            } else {
                $GLOBALS['db']->autoExecute_unsafe(DB_PREFIX.'distribution', $data);
            }
            $errno = $GLOBALS['db']->errno();

            if ($errno) {
                $info = '新增/编辑失败,请重试';
                logger::write('驿站新增/编辑失败! SQL:'.$GLOBALS['db']->getLastSql());
                break;
            }
            if (!$id) {
                $id = $GLOBALS['db']->insert_id();
            }
            $status = 1;
            $info = '新增/编辑成功';
            $jump = url('hiz', 'distribution#edit', array('id' => $id));

        } while(0);
        ajax_return(array('status' => $status, 'info' => $info, 'jump' => $jump));
    }


    /**
     * 驿站状态(正常/禁用)切换
     * 请求参数：id => int // 驿站id
     * 返回数据： data => json {
     *                 'status' => int // 1:success  0:fail
     *             }
     * @return  json
     */
    public function distable()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $id = intval($_REQUEST['id']);

        $sql = 'UPDATE '.DB_PREFIX.'distribution SET disabled=disabled^1 WHERE id ='.$id.' AND is_delete=0 AND agency_id='.$h_id;
        $res = $GLOBALS['db']->query($sql);
        $status = 0;
        if ($res) {
            $status = 1;
        } else {
            logger::write('驿站删除失败! SQL:'.$GLOBALS['db']->getLastSql());
        }
        ajax_return(array('status' => $status));
    }

    /**
     * 配送点列表
     * 请求参数: dist_id => int // 驿站id
     * 返回数据: list => array (
     *             'id' => int  配送点id
     *             'poi_name' => string 配送点名称
     *             'poi_addr' => string 详细地址
     *             'disabled' => int 状态 0:正常 1:禁用
     *         )
     * @return  
     */
    public function shiplist()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
    	$dist_id = intval($_REQUEST['dist_id']);

        
        // 请求的驿站是否商户下辖
        $checkSql = 'SELECT name FROM '.DB_PREFIX.'distribution WHERE id='.$dist_id.' AND is_delete=0 AND agency_id='.$h_id;
        $check = $GLOBALS['db']->getOne($checkSql);

        if (!$check) { // 
            app_redirect(url('hiz', 'distribution'));
        }

        $GLOBALS['tmpl']->assign('pre_title', $check);

        $page_size = PAGE_SIZE;
        $page = intval($_REQUEST['p']);
        if($page==0) $page = 1;
        $limit = (($page-1)*$page_size).",".$page_size;

        $list_sql = 'SELECT * FROM '.DB_PREFIX.'distribution_shipping WHERE dist_id='.$dist_id.' AND is_delete=0 LIMIT '.$limit;
        $total_sql = 'SELECT count(id) FROM '.DB_PREFIX.'distribution_shipping WHERE dist_id='.$dist_id.' AND is_delete=0';
        $list = $GLOBALS['db']->getAll($list_sql);
        $total = $GLOBALS['db']->getOne($total_sql);
        foreach ($list as &$item) {
            $item['disabledStr'] = $item['disabled'] == 0 ? '正常' : '禁用';
        }unset($item);
        $GLOBALS['tmpl']->assign('dist_id', $dist_id);
        $GLOBALS['tmpl']->assign('list', $list);
        formatPage($total, $page_size);
        $GLOBALS['tmpl']->display('pages/distribution/shiplist.html');
    }

    /**
     * 新增/编辑配送点
     * 请求参数:   dist => int 驿站id(必选)
     *             id => int  //配送点id
     * 返回数据：info => array(
     *             id => int  配送点id
     *             dist_id => int 所属驿站id
     *             poi_name => string 驿站名称
     *             poi_address => string 详细地址
     *             xpoint => 经度
     *             ypoint => 纬度
     *         )
     *           country => array ( // 区县数组
     *               id => int 区县id
     *               name => string 区县名称
     *           )
     * @return  
     */
    public function shipedit()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $h_city = $h_info['city_id'];
        $dist_id = intval($_REQUEST['dist_id']);
        // 请求的驿站是否商户下辖
        $checkSql = 'SELECT id,name,city_id,county_id FROM '.DB_PREFIX.'distribution WHERE id='.$dist_id.' AND is_delete=0 AND agency_id='.$h_id;
        $check = $GLOBALS['db']->getRow($checkSql);
        if (!$check) { // 
            app_redirect(url('hiz', 'distribution#shiplist'));
        }
        $GLOBALS['tmpl']->assign('dist_name', $check['name']);
        // 驿站的市、区/县信息
        $ccSql = 'SELECT name FROM '.DB_PREFIX.'delivery_region WHERE id in ('.$check['city_id'].','.$check['county_id'].') ORDER BY id';
        $ccInfo = $GLOBALS['db']->getAll($ccSql);
        $GLOBALS['tmpl']->assign('city_name', $ccInfo[0]['name']);
        $GLOBALS['tmpl']->assign('country_name', $ccInfo[1]['name']);

    	if (isset($_REQUEST['id'])) {
            $sql = 'SELECT * FROM '.DB_PREFIX.'distribution_shipping WHERE id='.intval($_REQUEST['id']);
            $info = $GLOBALS['db']->getRow($sql);

            $GLOBALS['tmpl']->assign('info', $info);
        }

        $GLOBALS['tmpl']->assign('dist_id', $dist_id);
        $GLOBALS['tmpl']->display("pages/distribution/shipedit.html");
    }

    public function shipsave()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $status = 0;
        $info = '';
        $jump = '';
        $data = array();
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        do {

            $dist_id = intval($_REQUEST['dist_id']);
            if (empty($dist_id)) {
                $info = '参数异常，请刷新重试';
                break;
            }
            $exits = $GLOBALS['db']->getRow('SELECT id, county_id FROM '.DB_PREFIX.'distribution WHERE is_delete = 0 AND status = 1 AND disabled = 0 AND id='.$dist_id.' AND agency_id='.$h_id);
            if (empty($exits)) {
                $info = '当前驿站无法新增配送点';
                break;
            }

            $poi_name = strim($_REQUEST['poi_name']);
            if (empty($poi_name)) {
                $info = '配送点名称不能为空';
                break;
            }
            $name_check_sql = 'SELECT id FROM '.DB_PREFIX.'distribution_shipping WHERE dist_id='.$dist_id.' AND poi_name ="'.$poi_name.'"';
            if ($id) {
                $name_check_sql = ' AND id !='.$id;
            }
            $name_check = $GLOBALS['db']->getOne($name_check_sql);
            if ($name_check) {
                $info = '驿站名称已经存在，请选择其它名称';
                break;
            }

            $poi_addr = strim($_REQUEST['poi_addr']);
            if (empty($poi_addr)) {
                $info = '配送点地址不能为空';
                break;
            }

            $xpoint = floatval($_REQUEST['xpoint']);
            $ypoint = floatval($_REQUEST['ypoint']);
            
            if (empty($xpoint) || empty($ypoint)) {
                $info = '请选择一个地图坐标';
                break;
            }

            /*$region_lv4 = intval($_REQUEST['region_lv4']);
            if (empty($region_lv4)) {
                $info = '区县数据异常';
                break;
            }*/
            // 区县信息从驿站里获取

            $data = array(
                'dist_id' => $dist_id,
                'poi_name' => $poi_name,
                'poi_addr' => $poi_addr,
                'xpoint' => $xpoint,
                'ypoint' => $ypoint,
            );
            if (!$id) {
                $data['region_lv1'] = 1;
                $data['region_lv2'] = $h_info['province_id'];
                $data['region_lv3'] = $h_info['city_id'];
                $data['region_lv4'] = $exits['county_id'];
            }

            if ($id) {
                $GLOBALS['db']->autoExecute(DB_PREFIX.'distribution_shipping', $data, 'UPDATE', 'id='.$id);
            } else {
                $GLOBALS['db']->autoExecute(DB_PREFIX.'distribution_shipping', $data);
            }
            $errno = $GLOBALS['db']->errno();

            if ($errno) {
                $info = '新增/编辑失败,请重试';
                logger::write('配送点新增/编辑失败! SQL:'.$GLOBALS['db']->getLastSql());
                break;
            }
            $status = 1;
            $info = '新增/编辑成功';
            $jump = url('hiz', 'distribution#shiplist', array('dist_id' => $dist_id));
        } while(0);
        ajax_return(array('status' => $status, 'info' => $info, 'jump' => $jump));
    }

    /**
     * 配送点删除
     * 请求参数：id => int  配送点id
     * 返回数据: data => json {
     *                 status => int  0:删除失败 1:删除成功
     *                 info => string  操作状态描述
     *             }
     * @return json 
     */
    public function delship()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $id = intval($_REQUEST['id']);
        // 验证配送点数据属于当前驿站
        $checkSql = 'SELECT ds.`id` FROM '.DB_PREFIX.'distribution_shipping ds LEFT JOIN '.DB_PREFIX.'distribution d ON d.id=ds.dist_id WHERE ds.id='.$id.' AND d.agency_id='.$h_id;
        $check = $GLOBALS['db']->getOne($checkSql);
        if (!$check) {
            app_redirect(url('hiz', 'distribution'));
        }
        
        $sql = 'UPDATE '.DB_PREFIX.'distribution_shipping SET is_delete=1 WHERE id ='.$id;
        $res = $GLOBALS['db']->query($sql);
        $status = 0;
        $info = '';
        if ($res) {
            $status = 1;
        } else {
            $info = '删除失败,请重试';
        }
        ajax_return(array('status' => $status, 'info' => $info));
    }

    /**
     * 配送点状态(正常/禁用)切换
     * 请求参数：id => int // 配送点id
     * 返回数据： data => json {
     *                 'status' => int // 1:success  0:fail
     *             }
     * @return  json
     */
    public function shipable()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $id = intval($_REQUEST['id']);
        // 验证配送点数据属于当前驿站
        $checkSql = 'SELECT ds.`id` FROM '.DB_PREFIX.'distribution_shipping ds LEFT JOIN '.DB_PREFIX.'distribution d ON d.id=ds.dist_id WHERE ds.id='.$id.' AND d.agency_id='.$h_id;
        $check = $GLOBALS['db']->getOne($checkSql);
        if (!$check) {
            app_redirect(url('hiz', 'distribution'));
        }

        $sql = 'UPDATE '.DB_PREFIX.'distribution_shipping SET disabled=disabled^1 WHERE id ='.$id;
        $res = $GLOBALS['db']->query($sql);
        $status = 0;
        if ($res) {
            $status = 1;
        }
        ajax_return(array('status' => $status));
    }

    /**
     * 驿站审核列表
     * 请求参数： status(可选) => int 审核状态 0:待审核 1:审核通过 2:拒绝申请
     *            username(可选) => string 驿站名称
     * 返回数据：list => array (
     *             id => int  驿站id
     *             name => 驿站名称
     *             contact => 联系人
     *             tel  => 联系电话
     *             address => 详细地址
     *             status => 状态 0:未审核  1:审核通过  2:拒绝申请
     *             )
     * @return  
     */
    public function authList()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $where = ' is_delete=0 AND agency_id = '.$h_id;
        if (isset($_REQUEST['status'])) {
            $where .= ' status = '.intval($_REQUEST['status']);
        }
        if (!empty($_REQUEST['username'])) {
            $where .= ' username like "%'.strim($_REQUEST['username']).'%"';
        }
        $page_size = PAGE_SIZE;
        $page = intval($_REQUEST['p']);
        if($page==0) $page = 1;
        $limit = (($page-1)*$page_size).",".$page_size;

        $sql = 'SELECT * FROM '.DB_PREFIX.'distribution WHERE'.$where.' ORDER BY id DESC LIMIT '.$limit;
        $countSql = 'SELECT count(id) FROM '.DB_PREFIX.'distribution WHERE'.$where;
        $total = $GLOBALS['db']->getOne($countSql);

        $list = $GLOBALS['db']->getAll($sql);
        $authStatus = array(
            0 => '未审核',
            1 => '审核通过',
            2 => '拒绝申请',
        );
        foreach ($list as &$item) {
            $item['statusStr'] = $authStatus[$item['status']];
        }unset($item);

        $GLOBALS['tmpl']->assign('list', $list);

        formatPage($total, $page_size);

        $GLOBALS['tmpl']->display("pages/distribution/authList.html");
    }

    /**
     * 驿站审核详情
     * 请求参数：id => value  有参为编辑，无参为新增
     * 返回数据： info => array(
     *                   username => string  帐户
     *                   name => string 驿站名称 
     *                   password => string 密码
     *                   country_id => int 县区id
     *                   address => string 驿站地址
     *                   contact => string 负责人
     *                   tel => string 联系电话 
     *                   xpoint => float 定位经度
     *                   ypoint => float 定位纬度 
     *                   xpoints => string 多边形经度集        
     *                   ypoints = string 多边形纬度集
     *                   open_time => string 营业时间
     *             )
     *             country => string    // 县区信息
     *             
     *             has_map => int  // 驿站多边形坐标是否存在
     *             xpoints => array // 驿站多边形经度
     *             ypoints => array // 驿站多边形纬度
     * @return [type] [description]
     */
    public function authInfo()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $h_city = $h_info['city_id'];
        if (empty($_REQUEST['id'])) {
            app_redirect(url('hiz', 'distribution#authList'));
        }

        $id = intval($_REQUEST['id']);
        $sql = 'SELECT * FROM '.DB_PREFIX.'distribution WHERE id='.$id.' AND is_delete=0 AND agency_id='.$h_id;
        $info = $GLOBALS['db']->getRow($sql);
        // print_r($sql);
        // print_r($info);exit;
        if (!$info) { // 驿站不存在
            app_redirect(url('hiz', 'distribution'));
        }

        $countrySql = 'SELECT name FROM '.DB_PREFIX.'delivery_region WHERE id='.$info['county_id'];
        $country = $GLOBALS['db']->getOne($countrySql);
        $GLOBALS['tmpl']->assign('country', $country);

        $xpoints = explode(',', $info['xpoints']);
        $ypoints = explode(',', $info['ypoints']);
        if (!empty($xpoints) && !empty($ypoints) && count($xpoints) == count($ypoints)) {
            // $GLOBALS['tmpl']->assign('has_map', 1);
            $GLOBALS['tmpl']->assign('xpoints', $xpoints);
            $GLOBALS['tmpl']->assign('ypoints', $ypoints);
        }

        $authStatus = array(
            0 => '未审核',
            1 => '审核通过',
            2 => '拒绝申请',
        );
        $GLOBALS['tmpl']->assign('statusStr', $authStatus[$info['status']]);
        
        $GLOBALS['tmpl']->assign('info', $info);

        $GLOBALS['tmpl']->display("pages/distribution/authInfo.html");
    }


    /**
     * 驿站删除 (未审核状态下)
     * 请求参数： id => int  // 驿站id
     * 返回数据:  data => json {
     *                 'status' => int // 1:success  0:fail
     *                 'info' => string // status description
     *             }
     * @return json 
     */
    public function authDel()
    {
        $h_info = $GLOBALS["hiz_account_info"];
        $h_id = $h_info['id'];
        $id = intval($_REQUEST['id']);
        $sql = 'UPDATE '.DB_PREFIX.'distribution SET is_delete=1 WHERE id ='.$id.' AND is_delete=0 AND status=0 AND agency_id='.$h_id;
        $res = $GLOBALS['db']->query($sql);
        $status = 0;
        $info = '';
        if ($res) {
            $status = 1;
        } else {
            $info = '删除失败,请重试';
            logger::write('驿站删除失败! SQL:'.$GLOBALS['db']->getLastSql());
        }
        ajax_return(array('status' => $status, 'info' => $info));
    }
}