<?php

define("FILE_PATH","/callback/dc_payment"); //文件目录
require_once '../../system/system_init.php';

require_once(APP_ROOT_PATH."system/dc_payment/Dc_Upacpapp_payment.php");
$o = new Dc_Upacpapp_payment();
$o->notify($_POST);

?>