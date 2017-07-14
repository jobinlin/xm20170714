<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
error_reporting(E_ALL^E_DEPRECATED^E_STRICT^E_NOTICE^E_WARNING);

if (PHP_VERSION >= '5.0.0')
{
	$begin_run_time = @microtime(true);
}
else
{
	$begin_run_time = @microtime();
}
@set_magic_quotes_runtime (0);
define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
if(!defined('IS_CGI'))
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
 if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',  rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
if(!defined('APP_ROOT')) {
        // 网站URL根目录
        $_root = dirname(_PHP_FILE_);
        $_root = (($_root=='/' || $_root=='\\')?'':$_root);
        if(defined("FILE_PATH"))
        $_root = str_replace(FILE_PATH,"",$_root);
        define('APP_ROOT', $_root  );
}
if(!defined('APP_ROOT_PATH')) 
define('APP_ROOT_PATH', str_replace('system/system_init.php', '', str_replace('\\', '/', __FILE__)));

//定义$_SERVER['REQUEST_URI']兼容性
if (!isset($_SERVER['REQUEST_URI']))
{
	if (isset($_SERVER['argv']))
	{
		$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
	}
	else
	{
		$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
	}
	$_SERVER['REQUEST_URI'] = $uri;
}



require_once APP_ROOT_PATH."system/common.php"; //加载全局函数库

filter_request($_GET);
filter_request($_POST);
$_REQUEST = array_merge($_GET,$_POST);
//关于安装的检测
if(!file_exists(APP_ROOT_PATH."public/install.lock"))
{
	app_redirect(APP_ROOT."/install/index.php");
}

//开始创建runtime目录
$runtime = APP_ROOT_PATH."public/runtime/app/";
if(!file_exists($runtime))@mkdir($runtime,0777);
$runtime = APP_ROOT_PATH."public/runtime/admin/";
if(!file_exists($runtime))@mkdir($runtime,0777);
$runtime = APP_ROOT_PATH."public/runtime/data/";
if(!file_exists($runtime))@mkdir($runtime,0777);
$runtime = APP_ROOT_PATH."public/runtime/statics/";
if(!file_exists($runtime))@mkdir($runtime,0777);

check_sys_config();

//new
define("APP_TYPE","main");

require APP_ROOT_PATH."Saas/auth.php";
define("FANWE_APP_ID",$_FANWE_SAAS_ENV['APP_ID']);
define("FANWE_AES_KEY",$_FANWE_SAAS_ENV['APP_SECRET']);

$is_open_fx =0;
$is_open_dc =0;
if($_FANWE_SAAS_ENV['APPSYS_PACKAGE'] == 'open_all'){
    $is_open_fx =1;
    $is_open_dc =1;
}elseif($_FANWE_SAAS_ENV['APPSYS_PACKAGE']=='open_fx'){
    $is_open_fx =1;
}elseif ($_FANWE_SAAS_ENV['APPSYS_PACKAGE']=='open_dc'){
    $is_open_dc =1;
}


error_reporting(0);

//引入数据库的系统配置及定义配置函数
$cfg_file = APP_ROOT_PATH.'system/config.php';

if(file_exists($cfg_file))
{
	$sys_config = require APP_ROOT_PATH.'system/config.php';
}

if(!function_exists("app_conf"))
{
	function app_conf($name)
	{
		return stripslashes($GLOBALS['sys_config'][$name]);
	}
}

//引入时区配置及定义时间函数
if(function_exists('date_default_timezone_set'))
	date_default_timezone_set(app_conf('DEFAULT_TIMEZONE'));
//end 引入时区配置及定义时间函数

$define_file = APP_ROOT_PATH."system/define.php";
if(file_exists($define_file))
	require_once $define_file; //加载常量定义
define('DB_PREFIX', app_conf('DB_PREFIX'));



//定义缓存
if(!function_exists("load_fanwe_cache"))
{
	function load_fanwe_cache()
	{
		global $distribution_cfg;
		$type = $distribution_cfg["CACHE_TYPE"];
		$cacheClass = 'Cache'.ucwords(strtolower(strim($type)))."Service";
		if(file_exists(APP_ROOT_PATH."system/cache/".$cacheClass.".php"))
		{
			require_once APP_ROOT_PATH."system/cache/".$cacheClass.".php";
			if(class_exists($cacheClass))
			{
				$cache = new $cacheClass();
			}
			return $cache;
		}
		else
		{
			$file_cache_file = APP_ROOT_PATH.'system/cache/CacheFileService.php';
			if(file_exists($file_cache_file))
				require_once APP_ROOT_PATH.'system/cache/CacheFileService.php';
			if(class_exists("CacheFileService"))
				$cache = new CacheFileService();
			return $cache;
		}
	}
}

$cache_service_file = APP_ROOT_PATH."system/cache/Cache.php";
if(file_exists($cache_service_file))
	require_once $cache_service_file;
if(class_exists("CacheService"))
	$cache = CacheService::getInstance();
//end 定义缓存

//定义DB
global $db;

require_once(APP_ROOT_PATH."system/db/db.php");

//if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/'))
@mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);

$pconnect = false;
$db = new mysql_db(app_conf('DB_HOST').":".app_conf('DB_PORT'), app_conf('DB_USER'),app_conf('DB_PWD'),app_conf('DB_NAME'),app_conf('DB_CHARACTER'),$pconnect);

//end 定义DB



if(IS_DC_DELIVERY==1){
    $sql = "select * from ".DB_PREFIX."dc_third_delivery where is_effect=1 and class_name='DaDa'";
    $dada_data = $GLOBALS['db']->getRow($sql);
    if($dada_data){
        define('IS_OPEN_DADA', 1);
    }else{
        define('IS_OPEN_DADA', 0);
    }
}else{
    define('IS_OPEN_DADA', 0);
}
//echo IS_OPEN_DADA;exit;
//定义模板引擎
$tmpl_cls_file = APP_ROOT_PATH.'system/template/template.php';
if(file_exists($tmpl_cls_file))
{
	require_once  $tmpl_cls_file;
	if(class_exists("AppTemplate"))
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_caches/'))
			mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_caches/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/'))
			mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/',0777);
		$tmpl = new AppTemplate;
	}
}

//end 定义模板引擎
$lang_file = APP_ROOT_PATH.'/app/Lang/'.app_conf("SHOP_LANG").'/lang.php';
if(file_exists($lang_file))
	$lang = require_once $lang_file;

//end 引入数据库的系统配置及定义配置函数


//end new


if(IS_DEBUG)
{
	ini_set("display_errors", true);
    error_reporting(E_ALL^E_DEPRECATED^E_STRICT^E_NOTICE^E_WARNING);
}
else
	error_reporting(0);

require_once APP_ROOT_PATH.'system/utils/es_cookie.php';
require_once APP_ROOT_PATH."system/utils/es_session.php";


function get_http()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}
function get_domain()
{
	/* 协议 */
	$protocol = get_http();

	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		/* 端口 */
		if (isset($_SERVER['SERVER_PORT']))
		{
			$port = ':' . $_SERVER['SERVER_PORT'];

			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
			{
				$port = '';
			}
		}
		else
		{
			$port = '';
		}

		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'] . $port;
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'] . $port;
		}
	}

	return $protocol . $host;
}
function get_host()
{


	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'];
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'];
		}
	}
	return $host;
}

require_once APP_ROOT_PATH."system/utils/logger.php";
$app_lib_file = APP_ROOT_PATH."system/common/".APP_TYPE."_libs.php";
if(file_exists($app_lib_file))
{
	require_once $app_lib_file;
}
$refresh_page = false;
