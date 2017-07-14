<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class userxypointModule extends MainBaseModule
{
	public function index()
	{
		global_run();
		if($GLOBALS['geo']['xpoint']==0&&$GLOBALS['geo']['ypoint']==0)
		{
			call_api_core("userxypoint","index");
		}		
		$type=strim($_REQUEST['m_type']);
		$xpoint = strim($_REQUEST['m_longitude']);
		$ypoint = strim($_REQUEST['m_latitude']);
		if($type=="BD09"){
			
		}elseif($type=="GCJ02"){
			$geo=Convert_GCJ02_To_BD09($ypoint,$xpoint);
			
			$xpoint=$geo['lng'];
			$ypoint=$geo['lat'];

		}
		if($xpoint&&$ypoint)
		{
			$url = "http://api.map.baidu.com/geocoder/v2/?ak=FANWE_MAP_KEY&location=FANWE_MAP_YPOINT,FANWE_MAP_XPOINT&output=json";
			$url = str_replace("FANWE_MAP_KEY", app_conf("BAIDU_MAP_APPKEY"), $url);

			$url = str_replace("FANWE_MAP_YPOINT", $ypoint, $url);
			$url = str_replace("FANWE_MAP_XPOINT", $xpoint, $url);
				
			require_once(APP_ROOT_PATH."system/utils/transport.php");
			$trans = new transport();
			$trans->use_curl = true;
			$request_data = $trans->request($url);
			$data = $request_data['body'];
			$data = json_decode($data,1);
			$data = $data['result']['addressComponent'];
			$current_city = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_city where is_effect = 1 and LOCATE(name,'".$data['district']."')");
			if(empty($current_city))
				$current_city = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_city where is_effect = 1 and LOCATE(name,'".$data['city']."')");

			if($current_city&&$current_city['id']!=$GLOBALS['city']['id'])
			{
				ajax_return(array("status"=>0,"city"=>$current_city));
			}
		}
        if($GLOBALS['user_info']&&!$GLOBALS['user_info']['agency_id']){
            $this->_checkUserFx();
        }
		ajax_return(array("status"=>1));
	}
	public function do_position()
	{
		global_run();
		ajax_return(array("status"=>1,"add"=>$GLOBALS['geo']['address']));
	}

    /**
     * @desc   判断用户是否有网宝
     * @author    吴庆祥
     */
    private function _checkUserFx(){
        //定位失败不进行下一步了
        if(!$GLOBALS['city']['code'])return;
        if(!IS_OPEN_AGENCY)return;

        $address=$GLOBALS['db']->getRow("select c.id city_id,p.id province_id from ".DB_PREFIX."delivery_region c left join ".DB_PREFIX."delivery_region p on c.pid=p.id where c.code=". $GLOBALS['city']['code']);
        $province_id=$address['province_id'];
        $city_id=$address['city_id'];
        $user_info=$GLOBALS['user_info'];
        $update=array("city_code"=>$user_info['city_code'],"agency_id"=>$user_info['agency_id'],"province_id"=>$user_info['province_id'],"city_id"=>$user_info['city_id']);
        if(!$update['city_id']||!$update['province_id']){
            $update['province_id']=$province_id;
            $update['city_id']=$city_id;
        }
        if(!$update['city_code']){
            $update['city_code']=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."delivery_region where id=".$update['city_id']);
        }
        if(!$update['agency_id']&&$update['city_code']){
            $agency_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."agency where city_code=".$update['city_code']);
            if($agency_id){
                $update['agency_id']=$agency_id;
            }
        }
        //无更新数据直接跳过
        if(!$update['city_code']&&!$update['agency_id']&&!$update['city_id'])return;
        $GLOBALS['db']->autoExecute(DB_PREFIX."user",$update,"update","id=".$user_info['id']);
        refresh_user_info();
    }
	
}
?>