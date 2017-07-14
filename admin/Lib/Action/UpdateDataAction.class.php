<?php
class UpdateDataAction extends CommonAction{
    public function index() {
        set_time_limit(0);
        //6.2
        //同步城市CODE
        $city_data = M("DealCity")->field('id,name,code')->findAll();
        
        foreach ($city_data as $t => $v){
            $code=M("DeliveryRegion")->where(array("name"=>$v['name']))->getField("code");
            $data['code']=$code;
            if($code!=0){
                $result=M("DealCity")->where(array("id"=>$v['id']))->save($data);
            }
            
            if($code==0 || $result==false){
                $name=str_replace(array("省","市","区","县"),"",$v['name']);
                $code=M("DeliveryRegion")->where(array("name"=>$name))->getField("code");
                $result=M("DealCity")->where(array("id"=>$v['id']))->save($data);
            }
        }
        
        //6.3
        //同步商品，团购库存
        $deal_sql = "select asd.deal_id ,sum(stock_cfg)  as stock_cfg_total from ".DB_PREFIX."attr_stock as asd left join ".DB_PREFIX."deal as d on d.id=asd.deal_id GROUP BY asd.deal_id";
        $deal_data = $GLOBALS['db']->getAll($deal_sql);

        if($deal_data){
            foreach($deal_data as $k=>$v){
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_stock set stock_cfg=".$v['stock_cfg_total']." where deal_id=".$v['deal_id']);
                $GLOBALS['db']->query("update ".DB_PREFIX."deal set max_bought=".$v['stock_cfg_total']." where id=".$v['deal_id']);
            }          
        }
    }
}