<?php

/**
 * 运费模板通用类
 * User: jobinlin
 * Date: 2017/3/4
 * Time: 14:34
 */
class CarriageTemplate
{
    public static function save($carriageTemplateData,$carriageDetailData=array(),$id=0){

        //定义要保存的数据
        $data = array(
            'name'=> $carriageTemplateData['name'], //模板名称
            'country'=> $carriageTemplateData['country'],   //发货国家
            'province'=> $carriageTemplateData['province'], //发货省份
            'city'=> $carriageTemplateData['city'],     //发货城市
            'area'=> $carriageTemplateData['area'],     //发货区县
            'carriage_type'=> $carriageTemplateData['carriage_type'],   //运费类型：1自定义，2平台/卖家承担运费（免运费）
            'valuation_type'=> $carriageTemplateData['valuation_type'], //计价类型：1按件数，2按重量
            'tpl_type'=> 1, //模板类型：1快递
            'supplier_id'=> intval($carriageTemplateData['supplier_id']),   //如果存在就为商户的运费模板，没有则为平台
            'supplier_name'=> $carriageTemplateData['supplier_name'],   //商户名称
        );

        if ($id>0){ //更新数据
            $data['update_time'] = NOW_TIME;
            $data['cache_carriage_detail_data'] = '';
            $data['is_region'] = '';
            $GLOBALS['db']->autoExecute(DB_PREFIX."carriage_template",$data,"UPDATE","id=".$id);

            //删除旧的计费详情数据
            $GLOBALS['db']->query("delete from ".DB_PREFIX."carriage_detail where carriage_id=".$id);
        }else{//新增数据
            $data['is_use'] = 0;
            $data['create_time'] = NOW_TIME;
            $data['update_time'] = NOW_TIME;
            $data['cache_carriage_detail_data'] = '';
            $data['is_region'] = '';
            $GLOBALS['db']->autoExecute(DB_PREFIX."carriage_template",$data);
            $id = $GLOBALS['db']->insert_id();
            if (!$id>0){
                return false;
            }
        }

        if ($id>0 && $carriageDetailData){

            $is_region = 0; //记录是否有指定区域
            $cache_carriagetemplate_detail = array();

            foreach ($carriageDetailData as $k=>$v){
                if($v['region_ids']!=''){
                    $is_region = 1;
                }

                $temp_detail_data = array();
                //#carriage_type 运费类型 如果为2（卖家承担运费），的时候，详细费用都为0，指定地址为空
                $temp_detail_data['carriage_id'] = $id;     //关联运费模板ID
                $temp_detail_data['express_start'] = $data['carriage_type']==1?$v['express_start']:0;   //初始运费商品：多少件或kg
                $temp_detail_data['express_postage'] = $data['carriage_type']==1?$v['express_postage']:0;   //运费金额
                $temp_detail_data['express_plus'] = $data['carriage_type']==1?$v['express_plus']:0;     //增加运费商品：多少件或kg
                $temp_detail_data['express_postage_plus'] = $data['carriage_type']==1?$v['express_postage_plus']:0; //运费根据商品增加增加的金额
                $temp_detail_data['region_ids'] = $data['carriage_type']==1?$v['region_ids']:'';     //指定运费设置的地区（地区ID 根据逗号分隔）
                $temp_detail_data['sort'] = $k; //排序，默认配送为0

                //循环插入详情数据
                $GLOBALS['db']->autoExecute(DB_PREFIX."carriage_detail",$temp_detail_data);
                $cache_carriagetemplate_detail[] = $temp_detail_data;
            }


            //更新主表
            $update_data = array(
                'is_region'=>$is_region,
                'cache_carriage_detail_data'=>serialize($cache_carriagetemplate_detail),
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX."carriage_template",$update_data,"UPDATE","id=".$id);
        }
        return true;
    }

    public static function del($id){
        $result = array(
            'status'=>0,
            'info'=>'',
        );
        $carriage_template_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."carriage_template where id=".$id);
        if ($carriage_template_info){

        }else{

        }
    }
}