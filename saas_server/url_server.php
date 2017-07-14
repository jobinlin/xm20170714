<?php
define("FILE_PATH","/saas_client"); //文件目录
require_once './system/system_init.php';
require_once('./Saas/SAASAPIServer.php');

$appid = 'fw9ae7883339a8a55f';
$appsecret = '5cce8819673f948c40e60fcade608dbb';
class url_server{
    var $server;
    function __construct(){
        if(empty($this->server)){
            $this->server = new SAASAPIServer($GLOBALS['appid'], $GLOBALS['appsecret']);
        }
    }
    
    /**
     * 解析SAAS过来的URL
     */
    function decode_saas_url(){
        $ret = $this->server->takeSecurityParams($_SERVER['QUERY_STRING']);
        return $ret;
    }
}
?>