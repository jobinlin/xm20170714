<?php
//所有生活服务分类的缓存
class cache_delivery_regions_auto_cache extends auto_cache{
    public function load($param)
    {
        $param = array(); //重新定义缓存的有效参数，过滤非法参数
        $key = $this->build_key(__CLASS__,$param);
        $GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
        if(empty($format_delivery_regions)){
            $city_list = require APP_ROOT_PATH."system/public_cfg/regions_manage_cfg.php";
            //查询到省，市
            $delivery_regions=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where region_level in(2,3)");
            $format_delivery_regions = array();

            foreach ($city_list as $k=>$v){
                $province_arr = array();
                foreach ($v['province_ids'] as $p_k=>$p_v){
                    $province_temp = array();
                    $province_temp = array('id'=>$p_v,'name'=>$p_k);
                    $city_arr = array();
                    foreach ($delivery_regions as $d_k=>$d_v){
                        if($d_v['pid'] == $p_v){
                            $city_arr[] = array('id'=>$d_v['id'],'name'=>$d_v['name']);
                        }
                    }
                    $province_temp['city_list'] = $city_arr;
                    $province_arr[] = $province_temp;
                }
                $city_list[$k]['province_arr']=$province_arr;
            }
            $format_delivery_regions = $city_list;
            $GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
            $GLOBALS['cache']->set($key,$format_delivery_regions);

        }
        return $format_delivery_regions;
    }
    public function rm($param)
    {
        $key = $this->build_key(__CLASS__,$param);
        $GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
        $GLOBALS['cache']->rm($key);
    }
    public function clear_all()
    {
        $GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
        $GLOBALS['cache']->clear();
    }
}
?>