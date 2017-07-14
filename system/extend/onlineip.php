<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class gip{
//把stdClass Object转array
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    }
    if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = $this->object_array($value);
        }
    }
    return $array;
}
function getData($ip){
//ip接口地址：http://test.ip138.com/query/
//支持格式：jsonp/xml/txt
//参数：
//1. ip  string ip地址 例如 117.25.13.123
//2.datatype string txt|jsonp|xmls
//3.callback string 回调函数 当前参数仅为jsonp格式数据提供
//请求示例：http://test.ip138.com/query/?ip=117.25.13.123&datatype=text
  $ipapi='http://test.ip138.com/query/?ip=';
  $type='&datatype=jsonp';
  $url =$ipapi .$ip.$type;
  $c=curl_init();
  curl_setopt($c,CURLOPT_URL,$url);
  curl_setopt($c,CURLOPT_RETURNTRANSFER,1); 
  //curl_setopt($c,CURLOPT_CONNECTTIMEOUT,10000);
  curl_setopt($c, CURLOPT_HEADER, false); //设定是否输出页面内容
  $buf=curl_exec($c);
  $res=json_decode($buf);
  $inf=$this->object_array($res);
  return $inf;
}

}
?>