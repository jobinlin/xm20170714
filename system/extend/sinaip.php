<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class gip{
    //把stdClass Object转array
    public function object_array($array) {
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
public function getData($ip){
  //$ip='121.204.96.211';
  //新浪：http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=115.156.238.114
  $ipapi='http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=Json&ip=';
  $url =$ipapi .$ip;
  $c=curl_init();
  curl_setopt($c,CURLOPT_URL,$url);
  curl_setopt($c,CURLOPT_RETURNTRANSFER,1); 
  //curl_setopt($c,CURLOPT_CONNECTTIMEOUT,10000);
  curl_setopt($c, CURLOPT_HEADER, false); //设定是否输出页面内容
  $buf=curl_exec($c);
  $arr =json_decode($buf);
  $inf=$this->object_array($arr);
  return $inf;
}

}
?>