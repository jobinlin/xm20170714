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
	
	
	$hiz_account_info = es_session::get('hiz_account_info');
	
	if($hiz_account_info)
	{
		$GLOBALS['tmpl']->assign("hiz_account_info",$hiz_account_info);
		$share_code=base64_encode($hiz_account_info['id']);
		$GLOBALS['tmpl']->assign("share_code",$share_code);
	}
	//获取左侧菜单
	assign_hiz_nav_list();

	// 定义当前页面标题
	$GLOBALS['tmpl']->assign('page_title', getPageTitle());
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
	$user_info = es_session::get('user_info');
	
	//商户信息
	global $hiz_account_info;
	require_once(APP_ROOT_PATH."system/libs/hiz_user.php");
	$hiz_account_info = es_session::get('hiz_account_info');

	if(empty($hiz_account_info))
	{
		app_redirect(url("hiz","user#login"));
	}

	//实时刷新会员数据
	if($hiz_account_info)
	{
		$hiz_account_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."agency where is_delete = 0 and is_effect = 1 and id = ".intval($hiz_account_info['id']));
		$account_id = $hiz_account_info['id'];
		/* 业务逻辑部分 */
		$conditions .= " where agency_id = ".$account_id; // 查询条件
		/*所属代理商门店ID获取*/
		$supplier_list=$GLOBALS['db']->getAll(" select * from " . DB_PREFIX . "supplier".$conditions);
		$s_id=array();
		foreach($supplier_list as $k=>$v){
		    $s_id[]=$v['id'];
		}
		$location_list=$GLOBALS['db']->getAll(" select id from " . DB_PREFIX . "supplier_location where is_effect=1 and supplier_id in (".implode(",",$s_id).")");
		$l_id=array();
		foreach($location_list as $k=>$v){
		    $l_id[]=$v['id'];
		}
		/*所属代理商驿站ID获取*/
		$dist_info=$GLOBALS['db']->getAll(" select id from " . DB_PREFIX . "distribution".$conditions." and disabled=0 and status=1");
		$d_ids=array();
		foreach ($dist_info as $kk=>$vv){
		    $d_ids[]=$vv['id'];
		}
		$hiz_account_info['location_ids']=$l_id;
		$hiz_account_info['dist_ids']=$d_ids;
		es_session::set('hiz_account_info',$hiz_account_info);
	}
}


//编译生成css文件
function parse_css($urls)
{
	$color_cfg = require_once(APP_ROOT_PATH."app/Tpl/hiz/color_cfg.php");
	$showurl = $url = md5(implode(',',$urls).SITE_DOMAIN);
	$css_url = 'public/runtime/statics/hiz/'.$url.'.css';
	$pathwithoupublic = 'runtime/statics/hiz/';
	$url_path = APP_ROOT_PATH.$css_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/hiz/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/hiz/',0777);
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
	$js_url = 'public/runtime/statics/hiz/'.$url.'.js';
	$pathwithoupublic = 'runtime/statics/hiz/';
	$url_path = APP_ROOT_PATH.$js_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/hiz/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/hiz/',0777);

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

function check_field($field,$value,$user_id)
{
    require_once(APP_ROOT_PATH."system/model/user.php");
    $data = array();
    $data['status'] = true;
    $data['info'] = "";
    $user_data['id'] = $user_id;
    if($field=="email")
    {
        $check_rs = check_user("email",$value,$user_data);
        if(!$check_rs['status'])
        {
            $check_data = $check_rs['data'];
            if($check_data['error']==FORMAT_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "邮箱格式不正确";
                $data['field'] = "email";
                return $data;
            }
            if($check_data['error']==EXIST_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "邮箱已被注册";
                $data['field'] = "email";
                return $data;
            }
        }
    }

    if($field=="getpassword_email")
    {
        if(!check_email($value))
        {
            $data['status'] = false;
            $data['info'] = "邮箱格式不正确";
            $data['field'] = "getpassword_email";
            return $data;
        }
        $rs = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".$value."' and id <> ".$user_id);
        if(intval($rs)==0)
        {
            $data['status'] = false;
            $data['info'] = "邮箱未在本站注册过";
            $data['field'] = "getpassword_email";
            return $data;
        }

    }

    if($field=="getpassword_mobile")
    {
        if(!check_mobile($value))
        {
            $data['status'] = false;
            $data['info'] = "手机号码格式不正确";
            $data['field'] = "user_mobile";
            return $data;
        }
        $rs = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$value."' and id <> ".$user_id);
        if(intval($rs)==0)
        {
            $data['status'] = false;
            $data['info'] = "手机号未在本站注册过";
            $data['field'] = "user_mobile";
            return $data;
        }

    }

    if($field=="user_name")
    {
        $check_rs = check_user("user_name",$value,$user_data);
        if(!$check_rs['status'])
        {
            $check_data = $check_rs['data'];
            if($check_data['error']==FORMAT_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "用户名格式不正确";
                $data['field'] = "user_name";
                return $data;
            }
            if($check_data['error']==EXIST_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "用户名已被注册";
                $data['field'] = "user_name";
                return $data;
            }
        }
    }
    if($field=="mobile")
    {
        $check_rs = check_user("mobile",$value,$user_data);
        if(!$check_rs['status'])
        {
            $check_data = $check_rs['data'];
            if($check_data['error']==FORMAT_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "手机号格式不正确";
                $data['field'] = "user_mobile";
                return $data;
            }
            if($check_data['error']==EXIST_ERROR)
            {
                $data['status'] = false;
                $data['info'] = "手机号已被注册";
                $data['field'] = "user_mobile";
                return $data;
            }
        }
    }

    if($field=="verify_code")
    {

        $verify = md5($value);
        $session_verify = es_session::get('verify');
        if($verify!=$session_verify)
        {
            $data['status'] = false;
            $data['info']	=	"图片验证码错误";
            $data['field'] = "verify_code";
            return $data;
        }
    }
    return $data;
}


//左侧导航菜单
function assign_hiz_nav_list(){
    if(empty($GLOBALS['hiz_account_info'])){
        return false;
    }
    $curr_module = $_GET['ctl'];
    $curr_action = $_GET['act']?$_GET['act']:'index';

    $nav_list = require APP_ROOT_PATH."system/hiz_cfg/".APP_TYPE."/hiznav_cfg.php";

    if(!defined('DC')){
        unset($nav_list['Order']['node']['dcorder']);
        unset($nav_list['Order']['node']['dcresorder']);
    }
    foreach($nav_list as $k=>$v)
    {
        $module_name = $k;
        foreach($v['node'] as $kk=>$vv)
        {
            $module_name = $vv['module'];
            $action_name = $vv['action'];
            $nav_list[$k]['node'][$kk]['url'] = url("hiz",$module_name."#".$action_name);
            if($curr_module == $module_name&&$curr_action==$action_name){
                $nav_list[$k]['current'] = 1;
                $nav_list[$k]['node'][$kk]['current'] = 1;
            }
        }
    }



	$GLOBALS['tmpl']->assign("nav_list",$nav_list);
}

?>