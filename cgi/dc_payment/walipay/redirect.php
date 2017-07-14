<?php
define("FILE_PATH","/cgi/dc_payment/walipay"); //文件目录
require_once '../../../system/system_init.php';
$payment_notice_id = intval($_REQUEST['notice_id']);
require_once APP_ROOT_PATH."system/dc_payment/Dc_Walipay_payment.php";
$o = new Dc_Walipay_payment();
echo $o->get_redirect_url($payment_notice_id);
?>