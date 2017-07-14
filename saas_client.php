<?php
/**
 * url 请求

require_once './saas_client/url_client.php';
$saas_client = new url_client();
echo $saas_client->encode_saas_url("http://localhost/yydb/saas_server.php", array("user_name"=>"jobin","user_pwd"=>"aaaa"));
 
 */
 
/**
 * api 请求

 
require_once './saas_client/api_client.php';
$client = new api_client();
$client->invoke_data("http://localhost/yydb/sass_server.php", array("data"=>array('deal_name'=>'我想看你的笑容','price'=>'10.99')));
 */
define("FILE_PATH",""); //文件目录，空为根目录
require_once './system/system_init.php';


require_once './saas_client/api_client.php';
$client = new api_client();


//http://localhost/o2onew/index.php?ctl=deal&act=85
$deal = $GLOBALS['db']->getRow("select * from fanwe_deal where id = 85");
$parmat = array(
    'id'=>$deal['id'],
    'name'=>$deal['name'],
    'sub_name'=>$deal['sub_name'],
    'img'=>str_replace("./public",SITE_DOMAIN.APP_ROOT."/public",$deal['img']),
    'origin_price' => $deal['origin_price'],
    'current_price' => $deal['current_price'],
);

// $cate_list = $client->invoke_data("http://localhost/yydb/sass_server.php", array("act"=>"get_cate"));


$res = $client->invoke_data("http://localhost/yydb/sass_server.php", array("act"=>"get_cate"));
print_r($res);