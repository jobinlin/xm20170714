<?php
/**
 * 快递
 */
class express{
    
    private $key;
    private $url;
    private $callbackurl;
    private $salt;
    private $company_arr;
    
    public function __construct(){
        $this->key=app_conf('EXPRESS_KEY');
        $this->url = 'http://highapi.kuaidi.com/openapi-receive.html';
        $this->callbackurl=SITE_DOMAIN.APP_ROOT.'/callback/express/express_callback.php';
        $this->salt='';
        $this->company_arr = $this->get_company_name();
    }
    
    /**
     * 
     * @param unknown $expressCode  快递公司代号
     * @param unknown $expressNumber  快递单号
     * @param unknown $from  快递发货城市
     * @param unknown $to   日的地城市
     * @param unknown $key  快递key
     * @param unknown $orderId  订单ID
     * @param unknown $userId   用户ID
     * @param number $supplierId  商家ID（暂时无用）
     * @param unknown $company    快递公司名称
     * @param unknown $type   
     * @param unknown $remark
     * @param number $order_type  订单类型   '0:全部订单 ,1:外卖预定订单,2:商户订单,3:普通订单,4:会员买单',
     * @return string|mixed
     */
    
    public function get($express_str, $expressNumber, $from_id=0, $to_id, $orderId, $userId, $supplierId=0,$type=0,$remark,$order_type=3) {

        if($order_type>0){
            $ordertrack = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."track where express_code='".$express_str."' and express_number='".$expressNumber."' and order_type=".$order_type);
             
        }else{
            $ordertrack = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."track where express_code='".$express_str."' and express_number='".$expressNumber."'"); 
        }
        $company_arr = $this->company_arr;
        
        $expressCode = $company_arr[$express_str]['key'];
        $company=$company_arr[$express_str]['name'];
            
        if ($ordertrack) {
            $track_data = array(
                'supplier_id' => $supplierId,
                'user_id' => $userId,
                'express_company' => $company,
                'express_code' => $express_str,
                'express_number' => $expressNumber,
                'state' => 0,
                'ischeck' => 0,
                'data' => '',
                'type' => $type,
                'remark' => $remark,
                'order_type' => $order_type,
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX."track", $track_data, $mode = 'UPDATE', "id=".$ordertrack['id'], $querymode = 'SILENT');
            
        } else {
            $track_data = array(
                'order_id' => $orderId,
                'supplier_id' => $supplierId,
                'user_id' => $userId,
                'express_company' => $company,
                'express_code' => $express_str,
                'express_number' => $expressNumber,
                'state' => 0,
                'ischeck' => 0,
                'data' => '',
                'type' => $type,
                'remark' => $remark,
                'order_type' => $order_type,
            );
            
            $GLOBALS['db']->autoExecute(DB_PREFIX."track", $track_data);
             
        }
            /*
            $result['message'] = 'success';
            return $result;
            */

        /**
         * $from_id 出发地城市，可以随机，从数据库随机抽取一个
         * 
         */
        if($from_id==0){
            $from = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where region_level=3 order by rand() limit 1");

        }else{
            $from = self::addressStr($from_id);
        }
       
        $to = self::addressStr($to_id);
        
        $data['company'] = $expressCode;
        $data['number'] = $expressNumber;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['key'] = $this->key;
        $data['parameters']['callbackurl'] = $this->callbackurl;
        $data['parameters']['salt'] = $this->salt;
/*
        $param=array(
            'param'=>$data
        );
        */
        
        $args = json_encode($data);
        $url = $this->url;
        
        require_once(APP_ROOT_PATH."system/api_login/Tencent/Tencent.php");

//         do{
//             $result = Http::request($url, $args,$method = 'POST' , $multi = true);          
//             $return = json_decode($result,true);
// logger::write(print_r($return,1));
//         }while($return['message'] == 'fail');
        
            $result = Http::request($url, $args,$method = 'POST' , $multi = true);
            $return = json_decode($result,true);
        return $return;
    }
    
    
    public function callback(){
        $param=$_REQUEST['param'];

        $param = json_decode($param,true);

        if (empty($param)) {
            die('param empty');
        }
        
        
        $lastResult = $param['lastResult'];
        
        $express_code='';
        foreach($this->company_arr as $k=>$v){
            if($v['key']==$lastResult['com']){
                $express_code=$k;
                break;
            }   
        }
        
        $ordertrack = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."track where express_code='".$express_code."' and express_number='".$lastResult['nu']."'");

        if($ordertrack){

            $track_data['state'] = $lastResult['state'];
            $track_data['ischeck'] = $lastResult['ischeck'];
            $track_data['data'] = serialize($lastResult['data']);
            $GLOBALS['db']->autoExecute(DB_PREFIX."track", $track_data, $mode = 'UPDATE', "id=".$ordertrack['id'], $querymode = 'SILENT');
             
        }
        if($GLOBALS['db']->affected_rows()){
            die('{"result":"true", "returnCode":"200", "message":"成功"}');
        }
             
    }
    
    public function get_company_name(){
        $company=array(
            'Fedex'=>array('key'=>'lianbangkuaidi','name'=>'联邦快递'),
            'Sf'=>array('key'=>'shunfeng','name'=>'顺丰快递'),
            'Sto'=>array('key'=>'shentong','name'=>'申通快递'),
            'Ttkd'=>array('key'=>'tiantian','name'=>'天天快递'),
            'Yto'=>array('key'=>'yuantong','name'=>'圆通快递'),
            'Yunda'=>array('key'=>'yunda','name'=>'韵达快递'),
            'Zjs'=>array('key'=>'zhaijisong','name'=>'宅急送快递'),
            'Zto'=>array('key'=>'zhongtong','name'=>'中通快递'),
            'Ems'=>array('key'=>'ems','name'=>'EMS')
        );
        return $company;
    }

    /**
     * 获取快递状态
     */
    public function getOrderTrack($express_id,$notice_sn,$order_type=0){
      
        if($order_type>0){
            $ordertrack = $GLOBALS['db']->getRow("select t.* from ".DB_PREFIX."track as t left join ".DB_PREFIX."express as e on t.express_code =e.class_name where e.id=".$express_id." and t.express_number='".$notice_sn."' and t.order_type=".$order_type);
             
        }else{
            $ordertrack = $GLOBALS['db']->getRow("select t.* from ".DB_PREFIX."track as t left join ".DB_PREFIX."express as e on t.express_code =e.class_name where e.id=".$express_id." and t.express_number='".$notice_sn."'");
             
        }

        return $ordertrack;
    }
    /**
     * [addressStr 获取from to字符串]
     * @param  [type] $cityId     [description]
     */
    public static function addressStr($cityId) {
        $city_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id=".$cityId);
        return $city_name;
    }
}
