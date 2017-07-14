<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


//用于处理 api同步登录的回调

define("FILE_PATH","/callback/delivery"); //文件目录
require_once '../../system/system_init.php';
require_once(APP_ROOT_PATH.'app/Lib/'.APP_TYPE.'/core/main_init.php');

$api_class = "DaDaDelivery";
if(file_exists(APP_ROOT_PATH.'system/delivery/'.$api_class.'.php'))
{
	require_once(APP_ROOT_PATH.'system/delivery/'.$api_class.'.php');
	$api_obj = new $api_class();
	$api_obj->callbackOrder();
}

?>