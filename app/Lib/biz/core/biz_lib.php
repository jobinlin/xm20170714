<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



/**
 * 关于页面初始化时需要输出的信息
 * 全属使用的模板信息输出
 * 1. seo 基本信息
 * $GLOBALS['tmpl']->assign("shop_info",get_shop_info());
 * 2. 当前城市名称, 单城市不显示
 * 3. 输出APP_ROOT
 */
function init_app_page()
{
	//输出根路径
	$GLOBALS['tmpl']->assign("APP_ROOT",APP_ROOT);

	//定义当前语言包
	$GLOBALS['tmpl']->assign("LANG",$GLOBALS['lang']);

	//开始输出site_seo
	$site_seo['keyword']	=	app_conf("SHOP_TITLE");
	$site_seo['description']	= app_conf("SHOP_TITLE");
	$site_seo['title']  = app_conf("SHOP_TITLE");
	$GLOBALS['tmpl']->assign("site_seo",$site_seo);
	$GLOBALS['tmpl']->assign("account_info",$GLOBALS['account_info']);
    $GLOBALS['tmpl']->assign("account_mobile",substr_replace($GLOBALS['account_info']['mobile'],'****',3,4));

	//获取左侧菜单
	assign_biz_nav_list();

}


/**
 * 前端全运行函数，生成系统前台使用的全局变量
 * 1. 定位城市 GLOBALS['city'];
 * 2. 加载会员 GLOBALS['user_info'];
 * 3. 生成语言包
 * 4. 加载推荐人与来路
 * 5. 更新购物车
 */
function global_run()
{
	if(app_conf("SHOP_OPEN")==0)  //网站关闭时跳转到站点关闭页
	{
		app_redirect(url("index","close"));
	}
    if(!defined("PAGE_SIZE")){
        $page_size=app_conf("PAGE_SIZE");
        if($page_size){
            define("PAGE_SIZE",$page_size);
        }else{
            define("PAGE_SIZE",25);
        }

    };

	//输出语言包的js
	if(!file_exists(get_real_path()."public/runtime/app/lang.js"))
	{
		$str = "var LANG = {";
		foreach($GLOBALS['lang'] as $k=>$lang_row)
		{
			$str .= "\"".$k."\":\"".str_replace("nbr","\\n",addslashes($lang_row))."\",";
		}
		$str = substr($str,0,-1);
		$str .="};";
		@file_put_contents(get_real_path()."public/runtime/app/lang.js",$str);
	}
	//会员信息
	global $user_info;

	//商户信息
	global $account_info;

    refresh_user_info();

	//实时刷新会员数据
	if($user_info&&$account_info)
	{

        if(($_REQUEST['ctl']=='user'&&$_REQUEST['act']=='login')||$_REQUEST['ctl']=='user'&&$_REQUEST['act']=='register'){

            //获取权限
            $biz_account_auth = get_biz_account_auth();

            //避免重复重定向
            if(empty($biz_account_auth)){
                showBizErr("此商户账户权限不足,请更换账户登录!",0,'',0);
            }else{
                app_redirect(url("biz",$biz_account_auth[0]));
            }
        }

	}
}


//编译生成css文件
function parse_css($urls)
{
	$color_cfg = require_once(APP_ROOT_PATH."app/Tpl/biz/color_cfg.php");
	$showurl = $url = md5(implode(',',$urls).SITE_DOMAIN);
	$css_url = 'public/runtime/statics/biz/'.$url.'.css';
	$pathwithoupublic = 'runtime/statics/biz/';
	$url_path = APP_ROOT_PATH.$css_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/biz/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/biz/',0777);
		$tmpl_path = $GLOBALS['tmpl']->_var['TMPL'];

		$css_content = '';
		foreach($urls as $url)
		{
			$css_content .= @file_get_contents($url);
		}
		$css_content = preg_replace("/[\r\n]/",'',$css_content);
		$css_content = str_replace("../images/",$tmpl_path."/images/",$css_content);
		foreach($color_cfg as $k=>$v)
		{
			$css_content = str_replace($k,$v,$css_content);
		}
		//		@file_put_contents($url_path, unicode_encode($css_content));
		@file_put_contents($url_path, $css_content);
		
		if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
			syn_to_remote_file_server($css_url);
			$GLOBALS['refresh_page'] = true;
		}
	}
	if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		$domain = $GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN'];
	}
	else
	{
		$domain = SITE_DOMAIN.APP_ROOT;
	}
	return $domain."/".$css_url;
}

/**
 *
 * @param $urls 载入的脚本
 * @param $encode_url 需加密的脚本
 */
function parse_script($urls,$encode_url=array())
{
	$showurl = $url = md5(implode(',',$urls));
	$js_url = 'public/runtime/statics/biz/'.$url.'.js';
	$pathwithoupublic = 'runtime/statics/biz/';
	$url_path = APP_ROOT_PATH.$js_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/biz/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/biz/',0777);

		if(count($encode_url)>0)
		{
			require_once(APP_ROOT_PATH."system/libs/javascriptpacker.php");
		}

		$js_content = '';
		foreach($urls as $url)
		{
			$append_content = @file_get_contents($url)."\r\n";
			if(in_array($url,$encode_url))
			{
				$packer = new JavaScriptPacker($append_content);
				$append_content = $packer->pack();
			}
			$js_content .= $append_content;
		}
		//		require_once(APP_ROOT_PATH."system/libs/javascriptpacker.php");
		//	    $packer = new JavaScriptPacker($js_content);
		//		$js_content = $packer->pack();
		@file_put_contents($url_path,$js_content);
		if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
			syn_to_remote_file_server($js_url);
			$GLOBALS['refresh_page'] = true;
		}
	}
	if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		$domain = $GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN'];
	}
	else
	{
		$domain = SITE_DOMAIN.APP_ROOT;
	}
	return $domain."/".$js_url;
}


function check_auth($module,$node){
	global_run();
	if(!$GLOBALS['account_info']){
		return false;
	}
	$biznode_auth = require_once(APP_ROOT_PATH.'/system/biz_cfg/'.APP_TYPE.'/biznode_cfg.php');
	if(OPEN_WEIXIN)
	{
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1&&$GLOBALS['account_info']['platform_status']==1)
		{
			$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/wxbiznode_cfg.php";
			$biznode_auth = array_merge_biznode($biznode_auth, $config_file);
		}
	}
	if(defined("FX_LEVEL"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/fxbiznode_cfg.php";
		$biznode_auth = array_merge_biznode($biznode_auth, $config_file);
	}
	if(defined("DC"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/dcbiznode_cfg.php";
		$biznode_auth = array_merge_biznode($biznode_auth, $config_file);
	}
	$is_has = 0;
	foreach ($biznode_auth as $k=>$v){
		if($module == $k){
			foreach($v['node'] as $kk=>$vv){
				if($kk == $node){
					$is_has=1;
				}
			}
		}
	}
	if(!$is_has){ //必须是权限列表中存在的
		return false;
	}

	$account_info = $GLOBALS['account_info'];
	$result = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."supplier_account_auth WHERE supplier_account_id=".$account_info['id']." AND module='".$module);
	if($result){
		return true;
	}else{
		return false;
	}
	
}
//左侧导航菜单
function assign_biz_nav_list(){
    if(empty($GLOBALS['account_info']))
        return false;
	
	$nav_list = require APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/biznav_cfg.php";
	if(OPEN_WEIXIN)
	{
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1&&$GLOBALS['account_info']['platform_status']==1)
		{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/wxbiznav_cfg.php";
		$nav_list = array_merge_biznav($nav_list, $config_file);
		}
	}
	if(defined("FX_LEVEL"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/fxbiznav_cfg.php";
		$nav_list = array_merge_biznav($nav_list, $config_file);
	}
	if(defined("DC"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/dcbiznav_cfg.php";
		$nav_list = array_merge_biznav($nav_list, $config_file);
	}

	if(IS_DC_DELIVERY==0){
	    unset($nav_list['DcOrder']['node']['dcxorder_index']);
	}
	if($GLOBALS['account_info']['is_main']){
		foreach($nav_list as $k=>$v)
		{
			$module_name = $k;
			foreach($v['node'] as $kk=>$vv)
			{
			    if($vv['is_pc']==1){
			        $module_name = $vv['module'];
			        $action_name = $vv['action'];
			        $nav_list[$k]['node'][$kk]['url'] = url("biz",$module_name."#".$action_name);
			    }else{
			        unset($nav_list[$k]['node'][$kk]);
			    }
			    
			}
			if(!$nav_list[$k]['node']){
			    unset($nav_list[$k]);
			}
		}
	}else{
		$result = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."supplier_account_auth WHERE supplier_account_id=".$GLOBALS['account_info']['id']);
		if(empty($result)){
			return false;
		}
		foreach($result as $k=>$v){
			$has_module[] = $v['module']."_".$v['node'];
		}
		$has_module = array_unique($has_module);
	
		foreach($nav_list as $k=>$v)
		{
			$note_count = 0;
			$module_name = $k;
			foreach($v['node'] as $kk=>$vv)
			{
				if($vv['is_pc']==1){
					if(in_array($kk, $has_module)){
						$module_name = $vv['module'];
						$action_name = $vv['action'];

						$nav_list[$k]['node'][$kk]['url'] = url("biz",$module_name."#".$action_name);
						$note_count++;
					}else{
						unset($nav_list[$k]['node'][$kk]);
					}
				}else{
					unset($nav_list[$k]['node'][$kk]);
				}
			}
			if($note_count == 0){
				unset($nav_list[$k]);
			}
		}
	
    }
	$GLOBALS['tmpl']->assign("nav_list",$nav_list);
}

function get_biz_account_auth(){

    $s_account_info = $GLOBALS["account_info"];

	$nav_list = require APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/biznav_cfg.php";
	//过滤非PC 端的菜单节点
	foreach($nav_list as $k=>$v)
	{
		$temp_count = count($v['node']);
		$nav_count = 0;
		foreach($v['node'] as $kk=>$vv)
		{
			if($vv['is_pc']==0){
				unset($v['node'][$kk]);
				$nav_count++;
			}
			if($nav_count==$temp_count){
				unset($nav_list[$k]);
			}

		}
	}
	if(OPEN_WEIXIN)
	{
		$weixin_conf = load_auto_cache("weixin_conf");
		if($weixin_conf['platform_status']==1&&$GLOBALS['account_info']['platform_status']==1)
		{
			$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/wxbiznav_cfg.php";
			$nav_list = array_merge_biznav($nav_list, $config_file);
		}
	}
	if(defined("FX_LEVEL"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/fxbiznav_cfg.php";
		$nav_list = array_merge_biznav($nav_list, $config_file);
	}
	if(defined("DC"))
	{
		$config_file = APP_ROOT_PATH."system/biz_cfg/".APP_TYPE."/dcbiznav_cfg.php";
		$nav_list = array_merge_biznav($nav_list, $config_file);
	}



	if($s_account_info['is_main']){//管理员
		foreach($nav_list as $k=>$v)
		{
			foreach($v['node'] as $kk=>$vv)
			{
				$has_module[]  = $vv['module']."#".$vv['action'];

			}
		}
		$biz_account_auth = array_unique($has_module);
	}else{

		$result = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."supplier_account_auth WHERE supplier_account_id='".$s_account_info['id']."' order by id asc");

		if(empty($result)){
			return false;
		}
		foreach($result as $k=>$v){
			$has_module[] = $v['module']."#".$v['node'];
		}
		$biz_account_auth = array_unique($has_module);
	}

    return $biz_account_auth;
}


function check_module_auth($ctl="",$act="index")
{


	if ($ctl){
		$auth_key = $ctl."#".$act;
	}else{
		$ctl = strim(strtolower($_REQUEST['ctl']));
		$act = strim(strtolower($_REQUEST['act']));
		$act = $act?$act:"index";
	}

    $auth_key = $ctl."#".$act;
	//获取权限进行判断
	$biz_account_auth = get_biz_account_auth();

	if(!in_array($auth_key, $biz_account_auth)){
		return false;
	}
	else
	{
		return true;
	}
}
function get_all_files( $path )
{
		$list = array();
		$dir = @opendir($path);
	    while (false !== ($file = @readdir($dir)))
	    {
	    	if($file!='.'&&$file!='..')
	    	if( is_dir( $path.$file."/" ) ){
	         	$list = array_merge( $list , get_all_files( $path.$file."/" ) );
	        }
	        else 
	        {
	        	$list[] = $path.$file;
	        }
	    }
	    @closedir($dir);
	    return $list;
}
function refresh_user_info()
{

    //会员信息
    global $user_info;
    global $user_logined;
    require_once(APP_ROOT_PATH."system/model/user.php");
    $user_info = es_session::get('user_info');
    if(empty($user_info))
    {
        $cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
        $cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';
        if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
        {
            $cookie_uname = strim($cookie_uname);
            $cookie_upwd = strim($cookie_upwd);
            auto_do_login_user($cookie_uname,$cookie_upwd);
            $user_info = es_session::get('user_info');
        }
    }
    //商户信息
    global $account_info;
    require_once(APP_ROOT_PATH."system/libs/biz_user.php");
    $account_info = es_session::get('account_info');

    //可以只退出商户
    if($user_info)
    {

        //获取商户登录信息
        $supplier_login_info = $GLOBALS['db']->getRow("select account_name,account_password from ".DB_PREFIX."supplier_account where account_name = '".$user_info['merchant_name']."'");

        if($supplier_login_info){
            auto_do_login_biz($supplier_login_info['account_name'],$supplier_login_info['account_password'],false);
            $account_info = es_session::get('account_info');
        }

    }

    //实时刷新会员数据
    if($user_info)
    {
        $user_info = load_user($user_info['id']);
        $user_level = load_auto_cache("cache_user_level");
        $user_info['level'] = $user_level[$user_info['level_id']]['level'];
        $user_info['level_name'] = $user_level[$user_info['level_id']]['name'];
        es_session::set('user_info',$user_info);

        $user_logined_time = intval(es_session::get("user_logined_time"));
        $user_logined = es_session::get("user_logined");
        if(NOW_TIME-$user_logined_time>=MAX_LOGIN_TIME)
        {
            es_session::set("user_logined_time",0);
            es_session::set("user_logined", false);
            $user_logined = false;
        }
        else
        {
            if($user_logined)
                es_session::set("user_logined_time",NOW_TIME);
        }
    }

    //实时刷新商户数据
    if($account_info){

        $account_info = $GLOBALS['db']->getRow("select sa.*,s.is_open_dada_delivery,s.delivery_money,s.money,s.publish_verify_balance*100 as publish_verify_balance,s.platform_status,s.supplier_withdraw_cycle from ".DB_PREFIX."supplier_account as sa left join ".DB_PREFIX."supplier as s on sa.supplier_id=s.id where sa.is_delete = 0 and sa.is_effect = 1 and sa.id = ".intval($account_info['id']));
        if($account_info['is_main'] == 1){ //主账户取所有门店
            $account_locations = $GLOBALS['db']->getAll("select id as location_id from ".DB_PREFIX."supplier_location where is_effect=1 and supplier_id = ".$account_info['supplier_id']);
        }else
            $account_locations = $GLOBALS['db']->getAll("select sl.location_id from ".DB_PREFIX."supplier_account_location_link as sl left join ".DB_PREFIX."supplier_location as l on l.id=sl.location_id where l.is_effect=1 and sl.account_id = ".$account_info['id']);

        $account_location_ids = array();
        foreach($account_locations as $row)
        {
            $account_location_ids[] = $row['location_id'];
        }
        $account_info['location_ids'] =  $account_location_ids;
        $GLOBALS['account_info']['location_ids'] =  $account_location_ids;
        es_session::set('account_info',$account_info);
    }


}
?>