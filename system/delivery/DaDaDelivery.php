<?php
require_once(APP_ROOT_PATH."system/libs/TakeDelivery.php");
require_once(APP_ROOT_PATH."system/delivery/tool/AnubisApiHelper.php");
//require_once(APP_ROOT_PATH."system/delivery/tool/HttpClient.php");
require_once(APP_ROOT_PATH."system/utils/transport.php");

$delivery_lang = array(
    'name'	=>	'达达配送',
    'app_key'	=>	'app_key',
    'app_secret'	=>	'app_secret',
    'source_id'	=>	'商户ID',

);
$config = array(
    'app_key'	=>	array(
        'INPUT_TYPE'	=>	'0',
    ), //app_key
    'app_secret'	=>	array(
        'INPUT_TYPE'	=>	'0'
    ), //app_secret
    'source_id'	=>	array(
        'INPUT_TYPE'	=>	'0'
    ), //商户ID
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'DaDa';
    /* 名称 */
    $module['name']    = '达达配送';
    $module['lang']    =$delivery_lang;
    $module['config'] = $config;
    $module['reg_url'] = 'https://newopen.imdada.cn';
    return $module;
}

class DaDaDelivery implements TakeDelivery{
    
    /**
     * 达达开发者app_key
     */
    private $app_key;
    /**
     * 达达开发者app_secret
     */
    private $app_secret;
    
    /**
     * api url地址
     */
    private $url;
    
    /**
     * api版本
     */
    private $v = "1.0";
    
    /**
     * 数据格式
     */
    private $format = "json";
    
    /**
     * 商户ID
     */
    private $source_id;
    
    /**
     * http request timeout;
     */
    private $httpTimeout = 5;
    
    /**
     * 请求响应返回的数据状态
     */
    private $status;
    
    /**
     * 请求响应返回的code
     */
    private $code;
    
    /**
     * 请求响应返回的信息
     */
    private $msg;
    
    /**
     * 请求响应返回的结果
     */
    private $result;
    
    /**
     * 判断求是否异常
     */
    private $isExcepet = false;
    
    /**
     * 异常信息
     */
    private $excepetMsg;
    

    /**
     * 回调地址
     */
    private $callbackurl;
    private $is_test=1;
    
    /**
     * 构造函数
     * param array $config = array();
     */
    
    public function __construct(){
      
        
        $sql = "select * from ".DB_PREFIX."dc_third_delivery where is_effect=1 and class_name='DaDa'";
        $dada_data = $GLOBALS['db']->getRow($sql);
        $dada_config = unserialize($dada_data['config']);

        if($this->is_test==1){
            $this->url = 'newopen.qa.imdada.cn';  //测试域名
            $this->app_key = 'dadaf2397a0b71b2a54';    //填入正确的app_key
            $this->app_secret = 'b382c6cfb81ab2263d1e1032740f9713';    //填入正确的app_secret
            $this->source_id = '73753';  // 73753 测试帐号
        }else{
            $this->url = 'newopen.imdada.cn';  //线上域名
            $this->app_key = $dada_config['app_key'];    //填入正确的app_key
            $this->app_secret = $dada_config['app_secret'];    //填入正确的app_secret
            $this->source_id = $dada_config['source_id'];  // 73753 测试帐号
        }
            
        $this->timestamp = NOW_TIME;
        $this->format = 'json';
        $this->v = '1.0';
        $this->callbackurl=SITE_DOMAIN.APP_ROOT.'/callback/delivery/dada_callback.php';
        

        
    }
    
    
    /**
     * 构造请求数据
     * data:业务参数，json字符串
     */
    public function bulidRequestParams($body){
        $requestParams = array();
        $requestParams['app_key'] = $this->app_key;
        $requestParams['body'] =json_encode($body);
        $requestParams['format'] = $this->format;
        $requestParams['v'] = $this->v;
        $requestParams['source_id'] = $this->source_id;
        $requestParams['timestamp'] = NOW_TIME;
        $requestParams['signature'] = $this->_sign($requestParams);
        return $requestParams;
    }
    
    /**
     * 签名生成signature
     */
    public function _sign($data){
    
        //1.升序排序
        ksort($data);
    
        //2.字符串拼接
        $args = "";
        foreach ($data as $key => $value) {
            $args.=$key.$value;
        }
        $args = $this->app_secret.$args.$this->app_secret;
    
        //3.MD5签名,转为大写
        $sign = strtoupper(md5($args));
    
        return $sign;
    }
    
    
    /**
     * 解析响应数据
     * @param $arr返回的数据
     * 响应数据格式：{"status":"success","result":{},"code":0,"msg":"成功"}
     */
    public function parseResponseData($arr){
        if (empty($arr)) {
            $this->isExcepet = true;
            $this->excepetMsg = "接口请求失败";
        }else{
            $data = json_decode($arr, true);
            $this->status = $data['status'];
            $this->result = $data['result'];
            $this->code = $data['code'];
            $this->msg = $data['msg'];
        }
        return true;
    }
    
    
    /**
     * 获取返回result
     */
    public function getCityCode(){
        
        $dataRequest = $this->bulidRequestParams();
        $dataJson =  json_encode($dataRequest);
        $url = $this->url . '/api/cityCode/list';      
        $data=$this->transport_send($url,$dataJson);

    }
    
  
  /**
   * 发布订单到第三方平台
   * type=0,第一次推单，type=1,重复推单
   * @return mixed
   */
  
  // step 2 创建订单
  public function sendOrder($order_id) {
      $sql = "select do.* , dc.citycode ,sl.dada_shop_id from ".DB_PREFIX."dc_order as do left join ".DB_PREFIX."supplier_location as sl on do.location_id=sl.id left join ".DB_PREFIX."deal_city as dc on sl.city_id=dc.id where do.id = ".$order_id;
      $order_info = $GLOBALS['db']->getRow($sql);
      $cargo_num = $GLOBALS['db']->getOne("select sum(num) as num from ".DB_PREFIX."dc_order_menu where order_id = ".$order_id);
      if($order_info['order_delivery_time']==1){
          $order_info['order_delivery_time']=NOW_TIME+15*60;
      }
      if($this->is_test==1){
          $order_info['dada_shop_id'] = '11047059';
      }
      //把系统时间转换成当地正常时间
      $timezone = intval(app_conf('TIME_ZONE'));
      $order_info['order_delivery_time'] += $timezone * 3600;
      $order_info['create_time'] += $timezone * 3600;
      //百度地图BD09坐标---->中国正常GCJ02坐标(高德坐标系)
      $xypoint_data = Convert_BD09_To_GCJ02($order_info['ypoint'],$order_info['xpoint']);
      $xpoint =  $xypoint_data['lng']; //经度
      $ypoint =  $xypoint_data['lat']; //纬度
      //拼装data数据
      $dataArray = array(
          'shop_no'=>$order_info['dada_shop_id'],   //门店编号
          'origin_id'=>$order_info['order_sn'],  //第三方订单ID
          'city_code'=>$order_info['citycode'],  //订单所在城市的code
          'cargo_price'=>$order_info['total_price'],  //订单金额
          'cargo_num'=>$cargo_num,  //订单商品数量
          'is_prepay'=>$order_info['payment_id'],        //是否需要垫付 ,payment_id:支付方式ID，0表示在线支付，1表示货到付款,即要垫付
          'expected_fetch_time'=>$order_info['order_delivery_time'], //期望取货时间
          'receiver_name'=>$order_info['consignee'],  //收货人姓名
          'receiver_address'=>$order_info['api_address'].$order_info['address'],  //收货人地址
          'receiver_lat'=>$ypoint,   //收货人地址维度（高德坐标系）
          'receiver_lng'=>$xpoint,   //收货人地址经度（高德坐标系）
          'callback'=>$this->callbackurl,      //回调URL
          'receiver_phone'=>$order_info['mobile'],  //收货人手机号
          'create_time'=>$order_info['create_time'],  //订单创建时间
          'info'=>$order_info['dc_comment'],   //订单备注
          'invoice_title'=>$order_info['invoice'],  //发票抬头
      );
     //print_r($dataArray);exit;

    $dataRequest = $this->bulidRequestParams($dataArray);
    $dataJson =  json_encode($dataRequest);
    
    if($order_info['has_send_dada']==0){
        $url = $this->url . '/api/order/addOrder';
    }elseif($order_info['has_send_dada']==1){
        $url = $this->url . '/api/order/reAddOrder';
    }

    $data=$this->transport_send($url,$dataJson);

    if($data['code']==0){
        $delivery_fee = $data['result']['fee'];
        $sql = "update ".DB_PREFIX."dc_order set confirm_status=1 , has_send_dada =1 ,is_delivery_cancel=0, thirdpart_delivery_fee=".$delivery_fee." ,delivery_part=2 where id=".$order_id;
        $supplier_sql = "update ".DB_PREFIX."dc_supplier_order set confirm_status=1 , has_send_dada =1 , is_delivery_cancel=0, thirdpart_delivery_fee=".$delivery_fee." ,delivery_part=2 where order_id=".$order_id;
        $GLOBALS['db']->query($sql);
        $GLOBALS['db']->query($supplier_sql);
    }else{
        require_once(APP_ROOT_PATH."system/model/dc.php");
        $reason = $data['msg'];
        delivery_part_not_accept_order($order_id,$reason);
    }
    

  }


  /**
   * 获取返回result
   */
  public function testcallbackOrder(){
  
      $params = array('order_id'=>'20170203084328641');
      $dataRequest = $this->bulidRequestParams($params);
      $dataJson =  json_encode($dataRequest);
      //       $url = $this->url . '/api/order/accept';  //接受订单
      $url = $this->url . '/api/order/fetch';  //完成取货
      //       $url = $this->url . '/api/order/finish';  //完成订单
      //       $url = $this->url . '/api/order/cancel';  //取消订单
      //       $url = $this->url . '/api/order/expire';  //订单过期
      $data=$this->transport_send($url,$dataJson);
      //print_r($data);
  }
  
  /**
   * 订单回调
   * @return mixed
  */
  
  public function callbackOrder(){
      $param=$_REQUEST;
      $param = json_decode($param,true);
      
      /*
      $param = array('client_id'=>'',  //预留字段，默认为空
          'order_id'=>'20170203084328641',       //添加订单接口中的origin_id值
          'order_status'=>1,   //订单状态(待接单＝1 待取货＝2 配送中＝3 已完成＝4 已取消＝5 已过期＝7 指派单=8 可参考文末的状态说明）
          'cancel_reason'=>'订单取消原因',  //订单取消原因,其他状态下默认值为空字符串
          'cancel_from'=>1,    //订单取消原因来源(1:达达配送员取消；2:商家主动取消；3:系统或客服取消；0:默认值)
          'update_time'=>NOW_TIME,    //更新时间,时间戳
          'signature'=>'',      // 对client_id, order_id, update_time的值进行字符串升序排列，再连接字符串，取md5值
          'dm_id'=>500,          //达达配送员id，接单以后会传
          'dm_name'=>'张三',        //配送员姓名，接单以后会传
          'dm_mobile'=>'15632596632',      //配送员手机号，接单以后会传
          
      );
      */
      if (empty($param)) {
          die('param empty');
      }

      /*
       * 签名验证
       */
      $param_sign = array(
          'client_id'=>$param['client_id'],
          'order_id'=>$param['order_id'],
          'update_time'=>$param['update_time']
      );
      
      //1.升序排序
      ksort($param_sign);
      
      //2.字符串拼接
      $signargs = "";
      foreach ($param_sign as $key => $value) {
          $signargs.=$key.$value;
      }
      $sign = md5($signargs);
      if($sign!=$param['signature']){
          die('签名错误');
      }
     require_once(APP_ROOT_PATH."system/model/dc.php");
     
     $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."dc_order where order_sn='".$param['order_id']."'");
     
     switch($param['order_status']){
         
         case 4:  //已完成
             dc_confirm_delivery($order_info['id']);
             break;
         case 5:  //已取消
             //让商家或者平台重新选择是否由第三方配送
             $reason='达达配送取消订单';
             delivery_part_not_accept_order($order_info['id'],$reason);
             break;
         case 7:  //已过期
             //让商家或者平台重新选择是否由第三方配送
             $reason='达达配送长期未接单';
             delivery_part_not_accept_order($order_info['id'],$reason);
             break;
         default:
      
     }
     //保存配送信息到订单
     $dada_delivery = serialize($param);
     $GLOBALS['db']->query("update ".DB_PREFIX."dc_order set dada_delivery='".$dada_delivery."' where id=".$param['order_id']);
     $GLOBALS['db']->query("update ".DB_PREFIX."dc_supplier_order set dada_delivery='".$dada_delivery."' where order_id=".$param['order_id']);
      
     $this->getResult();
  }
  
  
  /**
   * 查询订单状态
   * @return mixed
  */
  public function queryOrder($order_sn){
      $params = array('order_id'=>$order_sn);
      $dataRequest = $this->bulidRequestParams($params);
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/api/order/status/query';
       
      $data=$this->transport_send($url,$dataJson);
      //print_r($data);
      return $data['result'];
      
  }
  
  /**
   * 取消订单
   * @return mixed
  */
  public function cancelOrder($order_sn,$cancel_reason){
      $cancel_reason_id=3; //顾客取消订单
      $params = array('order_id'=>$order_sn,
          'cancel_reason_id'=>$cancel_reason_id,
      );
      $dataRequest = $this->bulidRequestParams($params);
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/api/order/formalCancel';  
     
      $data=$this->transport_send($url,$dataJson);
      //logger::write(print_r($data,1));
      //print_r($data);
      //$this->getResult();
      return $data;
  }
  
  /**
   * 获取取消原因
   * @return mixed
   */
  public function getCancelReasons(){

      $dataRequest = $this->bulidRequestParams();
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/api/order/cancel/reasons';
       
      $data=$this->transport_send($url,$dataJson);
      //print_r($data);

  }
  
  
  /**
   * 查询配送人员状态
   * @return mixed
  */
  public function carrierOrder($order_id){
      
  }
  
  /**
   * 创建商户
   * @param $data
   * @return mixed
  */
  public function createSupplier($supplier_id){
      $sql = "select s.* , dc.name as city_name ,sa.mobile from ".DB_PREFIX."supplier as s left join ".DB_PREFIX."deal_city as dc on
       s.city_id=dc.id left join ".DB_PREFIX."supplier_account as sa on s.id=sa.supplier_id where s.id=".$supplier_id;
      $supplier_info = $GLOBALS['db']->getRow($sql);

      $params = array('mobile'=>$supplier_info['mobile'], //注册商户手机号,用于登陆商户后台
          'city_name'=>$supplier_info['city_name'],    //商户城市名称(如,上海)
          'enterprise_name'=>$supplier_info['h_name'],   //企业全称
          'enterprise_address'=>$supplier_info['address'],  //企业地址
          'contact_name'=>$supplier_info['h_faren'],   //联系人姓名
          'contact_phone'=>$supplier_info['mobile'],  //联系人电话        
      );
      //print_r($params);exit;
      $dataRequest = $this->bulidRequestParams($params);
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/merchantApi/merchant/add';  
      $data=$this->transport_send($url,$dataJson);
      //print_r($data);
      $this->getResult();
  }
  
  /**
   * 创建门店
   * @param $data
   * @return mixed
  */
  public function createStore($location_info){

      //$location_info['dada_shop_id'] ='fw123456789';
      $params = array('station_name'=>$location_info['name'], //门店名称
          'business'=>1,    //业务类型(餐饮-1,商超-9,水果生鲜-13,蛋糕-21,酒品-24,鲜花-3,其他-5)
          'city_name'=>$location_info['city_name'],   //城市名称(如,上海)
          'area_name'=>$location_info['district'],  //区域名称(如,浦东新区)
          'station_address'=>$location_info['address'],   //门店地址
          'lng'=>$location_info['xpoint'],  //门店经度
          'lat'=>$location_info['ypoint'],  //门店经度
          'contact_name'=>$location_info['contact'],  //联系人姓名
          'phone'=>$location_info['tel'],  //联系人电话
          'origin_shop_id'=>$location_info['dada_shop_id'],  //门店编码,可自定义,但必须唯一;若不填写,则系统自动生成
          'username'=>$location_info['dada_account'],  //达达商家app账号(若不需要登陆app,则不用设置)
          'password'=>$location_info['dada_password'],  //达达商家app密码(若不需要登陆app,则不用设置)
          
      );
     //print_r($params);exit;
      $location_arr[] = $params;
      $dataRequest = $this->bulidRequestParams($location_arr);
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/api/shop/add';
      $data=$this->transport_send($url,$dataJson);
      return $data;
      //$this->getResult();
  }
  
  /**
   * 更新门店
   * @param $data
   * @return mixed
  */
  public function updateStore($location_id){
      $sql = "select sl.* , dc.name as city_name ,sa.mobile from ".DB_PREFIX."supplier_location as sl left join ".DB_PREFIX."deal_city as dc on
       sl.city_id=dc.id left join ".DB_PREFIX."supplier_account as sa on sl.id=sa.supplier_id where sl.id=".$location_id;
      $location_info = $GLOBALS['db']->getRow($sql);
      //$location_info['dada_shop_id'] ='fw123456789';
      $params = array('station_name'=>$location_info['name'], //门店名称
          'business'=>1,    //业务类型(餐饮-1,商超-9,水果生鲜-13,蛋糕-21,酒品-24,鲜花-3,其他-5)
          'city_name'=>$location_info['city_name'],   //城市名称(如,上海)
          'area_name'=>$location_info['district'],  //区域名称(如,浦东新区)
          'station_address'=>$location_info['address'],   //门店地址
          'lng'=>$location_info['xpoint'],  //门店经度
          'lat'=>$location_info['ypoint'],  //门店经度
          'contact_name'=>$location_info['contact'],  //联系人姓名
          'phone'=>$location_info['tel'],  //联系人电话
          'origin_shop_id'=>$location_info['dada_shop_id'],  //门店编码,可自定义,但必须唯一;若不填写,则系统自动生成
          'status'=>$location_info['is_effect'],  //1-门店激活，0-门店下线
          
      );
     //   print_r($params);exit;

      $dataRequest = $this->bulidRequestParams($params);
      $dataJson =  json_encode($dataRequest);
      $url = $this->url . '/api/shop/update';
      $data=$this->transport_send($url,$dataJson);
     // print_r($data);
      $this->getResult();
  }
  
  /**
  * 获取返回result
  */
  public function getResult(){
    
     die('{"result":"true", "returnCode":"200", "message":"成功"}');
        
  }
    
/**
 * 
 * @param unknown $url
 * @param unknown $dataJson json格式的数据
 * @return mixed
 */
  public function transport_send($url,$dataJson){
  
      $trans = new transport();
      $trans->use_curl = true;
      $header =  array('Content-Type'=>'application/json');
      $request_data = $trans->request($url , $dataJson,$method = 'POST',$header);
      $data = $request_data['body'];
      $data = json_decode($data,1);
      return $data;
  
  }
    
    
}

