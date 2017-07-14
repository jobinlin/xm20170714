<?php
define("FILE_PATH","/callback/dc_payment"); //文件目录
require_once '../../system/system_init.php';

require_once(APP_ROOT_PATH."system/dc_payment/Dc_Yjwap_payment.php");
$o = new Dc_Yjwap_payment();
$_REQUEST['from'] = "app";
$o->response($_REQUEST);
?>