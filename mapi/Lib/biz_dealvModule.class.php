<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class biz_dealvApiModule extends MainBaseApiModule
{
    /**
     * Array
    (
        [user_login_status] => 1
        [verify_info] => Array
        (
            [sn] => Array  :array 验证券码
                (
                    [0] => 2444 1662 5
                    [1] => 2335 1242 6
                )
            [location_id] => 21
            [user_id] => 74
            [order_id] => 210
            [confirm_time] => 2016-12-19 12:02 ：string验证时间
            [user_name] => 5555 ：string 会员
            [location_name] => 桥亭活鱼小镇 ：string 验证门店
        )
        [buy_info] => Array
        (
            [0] => Array
            (
                [name] => 榭都中长款毛呢大衣 格子茧型冬季外套 秋冬韩版呢子呢大衣品牌女 : string 商品名
                [attr] => Array ：array 规格
                (
                    [0] => Array
                        (
                            [name] => 颜色
                            [value] => 红色
                        )
                    [1] => Array
                        (
                            [name] => 尺寸
                            [value] => M
                        )
                )
                [unit_price] => 456 ：int 单价
                [number] => 1 ：int 数量
            )
        )
        [ctl] => biz_tuanv
        [act] => record
        [status] => 1
        [info] =>
        [city_name] => 福州
        [return] => 1
        [sess_id] => 8v5tqjch2nf5b85eispjh9j1j6
        [ref_uid] =>
    )
     * @desc
     * @author    wuqingxiang
     * @return unknown_type
     */
    public function record(){
        $root=array();
        /*参数列表*/
        $data_id = intval($GLOBALS['request']['data_id']);
        $create_time=intval($GLOBALS['request']['create_time']);
        if(!$data_id){
            return output('',0,"查询的id为空");
        }
        //商户信息
        $account_info = $GLOBALS['account_info'];
        //判断是否登录
        if(!$account_info){
            $root['biz_user_status']=0;
            return output($root,0,"商户未登录");
        }else{
            $root['biz_user_status']=1;
        }
        //验证信息
        $tuan_info=$GLOBALS['db']->getAll("select password sn,location_id,user_id,order_id,confirm_time from ".DB_PREFIX."deal_coupon where order_id=".$data_id." and confirm_time=".$create_time." ORDER BY confirm_time desc");
        if(!$tuan_info){
            return output($root,0,"团购券不存在");
        }
        $verify_info=$tuan_info[0];
        $verify_info['user_name']=$GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$verify_info['user_id']);
        $verify_info['location_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier_location where id=".$verify_info['location_id']);
        $verify_info['confirm_time']=$verify_info['confirm_time']?to_date($verify_info['confirm_time'],"Y-m-d H:i"):0;
        $verify_info['sn']=array();
        foreach($tuan_info as $val){
            $verify_info['sn'][]=implode(" ",str_split($val['sn'],4));;
        }
        $root['verify_info']=$verify_info;
        //购买信息
        $buy_info=$GLOBALS['db']->getAll("select d.name,doi.attr,doi.unit_price,doi.number from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on d.id=doi.deal_id where doi.order_id=".$data_id);
        if(!$buy_info){
            return output($root,0,"订单不存在");
        }
        foreach($buy_info as $key =>$val){
            $buy_info[$key]['unit_price']=round($val['unit_price'],2);
            if($val['attr']){
                $buy_info[$key]['attr']=$GLOBALS['db']->getAll("SELECT gta.name as name,da.name as value from ".DB_PREFIX."deal_attr as da LEFT JOIN ".DB_PREFIX."goods_type_attr as gta ON gta.id=da.goods_type_attr_id where da.id in(".$val['attr'].") ");
            }
        }
        $root['page_title']="验证详情";
        $root['buy_info']=$buy_info;
        return output($root);
    }
}
?>

