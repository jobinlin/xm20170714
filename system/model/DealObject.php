<?php
/**
 * Created by PhpStorm.
 * User: jobin.lin
 * Date: 2017/4/11
 * Time: 18:40
*/
class DealObject{
    private $id;    //数据ID
    private $name;      //商品名称
    private $sub_name;      //短名称
    private $cate_id;       //生活服务分类ID
    private $supplier_id;   //所属的商户ID
    private $img;           //主图
    private $description;     //信息描述详情
    private $begin_time;      //上线开始时间，可为0为不限时
    private $end_time;        //下架时间，可为0为不限时
    private $min_bought;      //最小购买量，用于团购产品的成团判断
    private $max_bought;      //最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)
    private $user_min_bought;      //会员下单的最小量
    private $user_max_bought;      //每个会员购买的上限
    private $origin_price;       //原价
    private $current_price;      //当前销售价
    private $city_id;        //所属的城市
    private $is_coupon;      //是否发放消费券
    private $is_delivery;    //是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费
    private $is_effect;      //有效性标识
    private $is_delete;      //删除标识
    private $user_count;     //下单量（按单计算,每组商品多件购买算一笔）
    private $buy_count;      //销量（购买的件数）
    private $time_status;    //时间状态0:未开始1:进行中2:已过期(不上架销售，可以往消费券中查到)
    private $buy_status;     //销售状态 0:未成团 1:已成团 2:成团并卖光\r\n0:未成团，购买的用户生成消费券，但不发券\r\n1:成团，购买发券\r\n2:卖光商品不再开放购买，但不下架
    private $deal_type;      //发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)

    private $return_money;      //购买即返现的金额(该项可填负数，也可作为额外消费的金额)
    private $return_score;      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
    private $brief;     //商品简介
    private $sort;      //前台展示排序 由大到小
    private $deal_goods_type;   //商品类型（用于生成相应类型的属性规格配置项）
    private $success_time;      //成团时间
    private $coupon_time_type;      //0：指定时间过期 1:按下单日起xx天过期
    private $coupon_day;            //下单后xx天内失效
    private $coupon_begin_time;     //发放消费券的生效时间
    private $coupon_end_time;       //发放的消费券的过期时间

    private $weight;        //商品重量，实体商品填写，用于运费计算
    private $weight_id;     //重量单位的配置ID
    private $is_referral;   //是否允许购买返利给邀请人
    private $buy_type;      //团购商品的类型0：普通 2:订购 3秒杀 (该值仅作为前台展示以及归类用，功能上与团购商品相同)
    private $discount;      //商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比
    private $icon;          //小图
    private $notice;        //是否参与预告（未到上线期的商品，默认不展示在前台，该项为1表示可以上线展示预告）

    private $seo_title;        //自定义的页面seo标题
    private $seo_keyword;      //自定义的页面seo关键词
    private $seo_description;      //自定义的页面seo描述
    private $is_lottery;      //是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则
    private $uname;      //url别名，用于重写与seo收录的优化

    private $shop_cate_id;      //商城商品的分类ID
    private $is_shop;      //标识是否为商城商品 0:否 1:是
    private $total_point;      //用户评分的总分
    private $avg_point;      //用户评分的平均分
    private $create_time;      //管理员发布时间
    private $update_time;      //管理员更新时间,
    private $name_match;      //名称的全文索引unicode编码
    private $name_match_row;      //名称的全文索引查询栏
    private $deal_cate_match;      //分类的全文索引unicode
    private $deal_cate_match_row;      //分类的全文索引查询栏
    private $shop_cate_match;      //商品分类的全文索引unicode
    private $shop_cate_match_row;      //商品分类的全文索引查询栏
    private $locate_match;      //地区信息的全文索引unicode
    private $locate_match_row;      //地区信息的全文索引查询栏
    private $tag_match;      //标签全文索引unicode
    private $tag_match_row;      //标签全文索引查询栏
    private $xpoint;      //经度（第一个分店的经度）
    private $ypoint;      //纬度（第一个分店的纬度）
    private $brand_id;      //所归属的品牌


    private $account_id;      //商家提交的商家帐号ID
    private $is_recommend;      //推荐到首页展示
    private $balance_price;      //与商家的结算价（即商价提供给平台商的成本价）
    private $is_refund;      //是否可退款
    private $auto_order;      //是否打上免预约标识 0:否 1:是
    private $expire_refund;      //是否支持过期退款( 过期未消费用户即可提交退款)
    private $any_refund;      //是否支持随时退款（未消费用户即可提交退款申请）
    private $multi_attr;      //多套餐（自动判断是否有属性规格配置，有则打上该标签）
    private $deal_tag;      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减
    private $dp_count;      //总参与的点评人数
    private $notes;      //购买需知
    private $dp_count_1;      //一星点评数
    private $dp_count_2;      //2星点评数
    private $dp_count_3;      //3星点评数
    private $dp_count_4;      //4星点评数
    private $dp_count_5;      //5星点评数
    private $buyin_app;      //是否仅展示在app端0否 1是
    private $is_pick;      //是否允许上门自提
    private $set_meal;      //移动端套餐模板
    private $pc_setmeal;      //PC端套餐模板
    private $phone_description;      //手机端描述
    private $is_location;      //存在支持门店，0：否1：是
    private $platform_type;      //发布平台类型(默认平台发布)
    private $delivery_type;      //配送方式(默认物流配送)1物流、2无需配送、3驿站
    private $dist_delivery_rate;      //驿站服务费率
    private $carriage_template_id;      //运费模板关联ID
    private $recommend_user_id;      //此商品的推荐会员ID
    private $recommend_user_return_ratio;      //推荐会员返佣率,必须存在推荐会员ID
    private $dist_service_rate;      //驿站服务费率
    private $allow_user_discount;    //是否参与会员等级折扣优惠，0为不参与，1为参与
    private $publish_verify_balance;    //商户结算费率 入库的为百分比计算后的数值80%，入库0.8

    private $deal_cate_type_id;  //团购生活服务子分类数据ID 数据用逗号“,”分割
    private $imgs;  //图片数组
    private $relate_goods_id;   //组合购买数据id数组
    private $forbid_sms;      //是否禁用短信发送功能，禁用短信则该商品的购物不会短信发券

    //门店处理字段
    private $location_id;   //商户门店ID

    //属性库存处理字段
    private $deal_attr;
    private $deal_attr_price;
    private $deal_add_balance_price;
    private $deal_attr_stock_hd;
    private $stock_cfg_num;
    private $stock_buy_count;   //属性库存销量


    //预留字段
    private $is_hot;      //商城商品的热卖标识
    private $is_new;      //商城商品的新品标识
    private $is_best;      //商城商品的精品标识

    //废弃字段
    private $code;          //标识码,可自定义一个标识用于消费券的前缀（用于电话验证的商品只能填数字）
    private $allow_promote;     //是否允许参与促销（系统内安装并配置的促销接口）

    private $cart_type;       //购物车规则\r\n0:启用购物车(每次可以买多款)\r\n1按商品(同款商品可买多款属性)\r\n2按商家(同个商家可买多款商品)\r\n3禁用购物车(每次只能买一款)
    private $reopen;     //重开团的申请，往期团购前台可以申请重新开团，该项用于计数
    private $brand_promote;      //是否参与品牌促销，该项与brand表的该项同步
    private $publish_wait;      //商家提交的产品 0:已审核 1:等待审核
    private $free_delivery;       //是否开启免运费，可以单独配置针对某个配送方式的免运费规则
    private $define_payment;      //是否自定义禁用哪些支付方式

    //其他
    private $data = array();
    private $type = '';

    //商户提交部分用到字段
    private $deal_id;   //'商品表关联ID'
    private $biz_apply_status;  //商户申请状态 1.新品上架申请 2:修改 3:下架
    private $admin_check_status;  //管理员审核状态 0:待审核 1:通过 2:拒绝
    private $cache_deal_cate_type_id;   //团购商品:子分类ID缓存
    private $cache_location_id; //支持门店ID缓存
    private $cache_focus_imgs;  //图集缓存
    private $cache_deal_attr;   //属性缓存
    private $cache_stock_data;  //属性库存缓存
    private $cache_attr_stock;  //对应attr_stock表内容
    private $cache_relate;  //关联商品缓存

    //关于分销部分字段
    private $is_allow_fx;   //是否参与分销
    private $is_fx;      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取
    private $fx_salary_type;  //佣金分配
    private $fx_salary; //佣金设置

    /**
     * @param $req
     * @param $type string (tuan,shop,score)
     */
    public function setParamet($req,$type)
    {
        $this->type = $type;
        $this->id = intval($req['id']);
        $this->name = strim($req['name']);      //商品名称
        $this->sub_name = strim($req['sub_name']);      //短名称
        $this->description = $req['description'];     //信息描述详情
        $this->phone_description = $req['phone_description'];      //手机端描述

        $this->begin_time = strim($req['begin_time'])==''?0:to_timespan($req['begin_time']);      //上线开始时间，可为0为不限时
        $this->end_time = strim($req['end_time'])==''?0:to_timespan($req['end_time']);        //下架时间，可为0为不限时
        $this->min_bought = intval($req['min_bought']);      //最小购买量，用于团购产品的成团判断
        $this->max_bought = intval($req['max_bought']);      //最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)
        $this->user_min_bought = intval($req['user_min_bought']);      //会员下单的最小量
        $this->user_max_bought = intval($req['user_max_bought']);      //每个会员购买的上限

        $this->is_delete = 0;      //删除标识
        $this->brief = strim($req['brief']);     //商品简介
        $this->is_effect = intval($req['is_effect']);      //有效性标识
        $this->sort = intval($req['sort']);      //前台展示排序 由大到小

        $this->seo_title = strim($req['seo_title']);        //自定义的页面seo标题
        $this->seo_keyword = strim($req['seo_keyword']);      //自定义的页面seo关键词
        $this->seo_description = strim($req['seo_description']);      //自定义的页面seo描述
        $this->uname = strim($req['uname']);      //url别名，用于重写与seo收录的优化
        $this->buyin_app = intval($req['buyin_app']);      //是否仅展示在app端0否 1是

        $this->imgs = $req['img'];  //图片数组
        $this->create_time = NOW_TIME;  //发布时间
        $this->update_time = NOW_TIME;  //更新时间

        //过滤掉改版后不要的数据
        $this->free_delivery = 0;
        $this->define_payment = 0;
        $this->cart_type = 0;
        $this->allow_promote = 0;

        //需要处理的数据
        $imgs = $req['img'];
        $this->icon = current($imgs);          //小图
        $this->img = current($imgs);          //主图

        
        switch($type){
            case "tuan":
                $this->setTuan($req);
                break;
            case "shop":
                $this->setShop($req);
                break;
            case "score":
                $this->setScore($req);
                break;
        }
        
    }

    public function setTuan($req)
    {
        $this->supplier_id = intval($req['supplier_id']);   //所属的商户ID

        $supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$this->supplier_id);
        $this->cate_id = strim($req['cate_id']);       //生活服务分类ID 数据格式用英文“，”逗号分隔
        $this->deal_cate_type_id = strim($req['second_cate_id']);      //生活服务子分类ID 数据格式用英文“，”逗号分隔

        $this->origin_price = round(floatval($req['origin_price']),2);       //原价
        $this->current_price = round(floatval($req['current_price']),2);      //当前销售价
        $this->balance_price = round(floatval($req['balance_price']),2);       //与商家的结算价（即商价提供给平台商的成本价）

        $this->city_id = intval($supplier_info['city_id']);        //所属的城市
        $this->buy_count = intval($req['buy_count']);      //销量（购买的件数）
        $this->is_coupon = intval($req['is_coupon']);      //是否发放消费券
        $this->deal_type = intval($req['deal_type']);      //发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)
        $this->return_money = floatval($req['return_money']);      //购买即返现的金额(该项可填负数，也可作为额外消费的金额)
        $this->return_score = intval($req['return_score']);      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
        $this->deal_goods_type = intval($req['deal_goods_type']);   //商品类型（用于生成相应类型的属性规格配置项）
        $this->coupon_time_type = strim($req['coupon_time_type']);      //0：指定时间过期 1:按下单日起xx天过期
        $this->coupon_day = intval($req['coupon_day']);            //下单后xx天内失效

        $this->coupon_begin_time = strim($req['coupon_begin_time'])==''?0:to_timespan($req['coupon_begin_time']);     //发放消费券的生效时间
        $this->coupon_end_time = strim($req['coupon_end_time'])==''?0:to_timespan($req['coupon_end_time']);       //发放的消费券的过期时间
        $this->is_referral = intval($req['is_referral']);   //是否允许购买返利给邀请人
        $this->discount = round(($req['current_price']/$req['origin_price'])*10,2);      //商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比
        $this->notice = intval($req['notice']);        //是否参与预告（未到上线期的商品，默认不展示在前台，该项为1表示可以上线展示预告）
        $this->forbid_sms = intval($req['forbid_sms']);     //是否禁用短信发送功能，禁用短信则该商品的购物不会短信发券
        $this->xpoint = strim($req['xpoint']);      //经度（第一个分店的经度）
        $this->ypoint = strim($req['ypoint']);      //纬度（第一个分店的纬度）

        $this->notes = $req['notes'];      //购买需知
        $this->is_lottery = intval($req['is_lottery']);      //是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则

        $this->expire_refund = intval($req['expire_refund']);   //是否支持过期退款( 过期未消费用户即可提交退款)
        $this->any_refund = intval($req['any_refund']);      //是否支持随时退款（未消费用户即可提交退款申请）
        $this->is_recommend = intval($req['is_recommend']);      //推荐到首页展示

        $this->set_meal = $req['set_meal'];      //移动端套餐模板
        $this->pc_setmeal = $req['pc_setmeal'];      //PC端套餐模板


        $this->auto_order = 0;  //是否打上免预约标识 0:否 1:是
        $this->allow_user_discount = intval($req['allow_user_discount']);   //是否参与会员等级折扣优惠，0为不参与，1为参与
        //如果费率和商户的相同则 0 入库，如果不相等就入库定义的值
        if($supplier_info['publish_verify_balance'] == floatval($req['publish_verify_balance']/100)){
            $this->publish_verify_balance = 0;    //商户结算费率 入库的为百分比计算后的数值80%，入库0.8
        }else{
            $this->publish_verify_balance = floatval($req['publish_verify_balance']/100);
        }


        //需要判断处理的数据
        $this->location_id = $req['location_id'];  //商户下门店
        $this->is_location = $this->location_id?1:0;      //存在支持门店，0：否1：是

        //标签属性
        $deal_tags = $req['deal_tag'];
        $temp_deal_tag = 0;
        foreach($deal_tags as $t)
        {
            $t2 = pow(2,$t);
            //根据tag计算免预约
            if($t==1)
            {
                $this->auto_order = 1;      //是否打上免预约标识 0:否 1:是
            }
            $temp_deal_tag = $temp_deal_tag|$t2;
        }

        $this->deal_tag = $temp_deal_tag;      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减

        //退款判断
        if($req['any_refund']==1||$req['expire_refund']==1)
        {
            $this->is_refund = 1;       //是否可退款
        }


        if($req['deal_attr']&&count($req['deal_attr'])>0)
        {
            $this->multi_attr = 1;      //多套餐（自动判断是否有属性规格配置，有则打上该标签）
        }
        else
        {
            $this->multi_attr = 0;
        }

        //开始处理属性库存数据
        $this->deal_attr = $req['deal_attr'];   //属性数据数组
        $this->deal_attr_price = $req['deal_attr_price'];   //递增价格数组
        $this->deal_add_balance_price = $req['deal_add_balance_price']; //递增结算价数组
        $this->stock_cfg_num = $req['stock_cfg_num'];   //属性库存数量
        $this->stock_buy_count = $req['stock_buy_count'];   //属性库存销量

        //关于分销部分字段
        $this->is_allow_fx = intval($req['is_allow_fx']);   //是否参与分销
        $this->is_fx = intval($req['is_fx']);      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取
        $this->fx_salary_type = intval($req['fx_salary_type']);  //佣金分配
        $this->fx_salary = $req['fx_salary']; //佣金设置


        //商户提交时候存在
        $this->account_id = intval($req['account_id']);      //商家提交的商家帐号ID
        if($this->account_id){//设置商户端提交要保存的数据
            $this->setSupplierSubmit($req);
        }


        //强制属性
        $this->is_delivery = 0;    //是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费
        $this->delivery_type = 2;   //配送方式(默认物流配送)1物流、2无需配送、3驿站
        $this->buy_type = 0;      //团购商品的类型0：普通 2:订购 3秒杀 (该值仅作为前台展示以及归类用，功能上与团购商品相同)
        $this->is_shop = 0;      //标识是否为商城商品 0:否 1:是
        $this->platform_type = 2;      //发布平台类型(默认平台发布)
        $this->is_pick = 0; //是否允许上门自提
    }

    /**
     * 设置商品数据
     * @param $req
     */
    public function setShop($req)
    {
        $this->supplier_id = intval($req['supplier_id']);   //商户ID
        $supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$this->supplier_id);
        $this->shop_cate_id = strim($req['shop_cate_id']);      //商城商品的分类ID 数据格式用“，”逗号分隔
        $this->city_id = intval($supplier_info['city_id']);        //所属的城市
        $this->origin_price = round(floatval($req['origin_price']),2);       //原价
        $this->current_price = round(floatval($req['current_price']),2);      //当前销售价
        $this->balance_price = round(floatval($req['balance_price']),2);       //与商家的结算价（即商价提供给平台商的成本价）
        $this->is_pick = intval($req['is_pick']);      //是否允许上门自提
        $this->return_money = floatval($req['return_money']);      //购买即返现的金额(该项可填负数，也可作为额外消费的金额)
        $this->return_score = intval($req['return_score']);      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
        $this->weight = round($req['weight'],2);        //商品重量，实体商品填写，用于运费计算
        $this->deal_goods_type = intval($req['deal_goods_type']);   //商品类型（用于生成相应类型的属性规格配置项）
        $this->is_referral = intval($req['is_referral']);   //是否允许购买返利给邀请人
        $this->discount = round(($req['current_price']/$req['origin_price'])*10,2);      //商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比
        $this->brand_id = intval($req['brand_id']);      //所归属的品牌
        $this->is_recommend = intval($req['is_recommend']);      //推荐到首页展示
        $this->relate_goods_id = $req['relate_goods_id'];   //组合购买数据ID
        $this->notes = $req['notes'];      //购买需知
        $this->is_refund = intval($req['is_refund']);      //是否支持随时退款（未消费用户即可提交退款申请）

        $this->deal_tag = intval($req['deal_tag']);      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减


        $this->is_fx = intval($req['is_fx']);      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取
        $this->delivery_type = intval($req['delivery_type']);      //配送方式(默认物流配送)1物流、2无需配送、3驿站
        $this->carriage_template_id = intval($req['carriage_template_id']);      //运费模板关联ID
        $this->recommend_user_id = intval($req['recommend_user_id']);      //此商品的推荐会员ID
        $this->recommend_user_return_ratio = floatval($req['recommend_user_return_ratio']);      //推荐会员返佣率,必须存在推荐会员ID
        $this->dist_service_rate = floatval($req['dist_service_rate']);      //驿站服务费率
        $this->allow_user_discount = intval($req['allow_user_discount']);   //是否参与会员等级折扣优惠，0为不参与，1为参与
        //如果费率和商户的相同则 0 入库，如果不相等就入库定义的值
        if($supplier_info['publish_verify_balance'] == floatval($req['publish_verify_balance']/100)){
            $this->publish_verify_balance = 0;    //商户结算费率 入库的为百分比计算后的数值80%，入库0.8
        }else{
            $this->publish_verify_balance = floatval($req['publish_verify_balance']/100);
        }
        //需要处理的数据

        $this->location_id = $req['location_id'];  //商户下门店

        //标签属性
        $deal_tags = $req['deal_tag'];
        $temp_deal_tag = 0;
        foreach($deal_tags as $t)
        {
            $t2 = pow(2,$t);
            $temp_deal_tag = $temp_deal_tag|$t2;
        }

        $this->deal_tag = $temp_deal_tag;      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减

        //多套餐标示
        if($req['deal_attr']&&count($req['deal_attr'])>0)
        {
            $this->multi_attr = 1;      //多套餐（自动判断是否有属性规格配置，有则打上该标签）
        }
        else
        {
            $this->multi_attr = 0;
        }

        //关于配送,物流和驿站都需要配送
        if(intval($req['delivery_type'])==1 || intval($req['delivery_type'])==3){
            $this->is_delivery = 1; //是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费
        }else{
            $this->is_delivery = 0;
        }

        //如果是平台代替商户发布的，强制发布平台类型为商户
        if($req['supplier_id']>0){
            $this->platform_type = 2;   //发布平台类型(默认平台发布)
        }else{
            $this->platform_type = 1;
        }

        //开始处理属性库存数据
        $this->deal_attr = $req['deal_attr'];   //属性数据数组
        $this->deal_attr_price = $req['deal_attr_price'];   //递增价格数组
        $this->deal_add_balance_price = $req['deal_add_balance_price']; //递增结算价数组
        $this->stock_cfg_num = $req['stock_cfg_num'];   //属性库存数量
        $this->stock_buy_count = $req['stock_buy_count'];   //属性库存销量

        //关于分销部分字段
        $this->is_allow_fx = intval($req['is_allow_fx']);   //是否参与分销
        $this->is_fx = intval($req['is_fx']);      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取
        $this->fx_salary_type = intval($req['fx_salary_type']);  //佣金分配
        $this->fx_salary = $req['fx_salary']; //佣金设置
        //商户提交时候存在
        $this->account_id = intval($req['account_id']);      //商家提交的商家帐号ID
        if($this->account_id){//设置商户端提交要保存的数据
            $this->setSupplierSubmit($req);
        }

        //强制属性
        $this->buy_type = 0;      //团购商品的类型0：普通(团购、商品)，1：积分商品
        $this->is_shop = 1;      //标识是否为商城商品 0:否 1:是

        $this->expire_refund = 0;   //是否支持过期退款( 过期未消费用户即可提交退款)
        $this->any_refund = 0;      //是否支持随时退款（未消费用户即可提交退款申请）
        $this->is_lottery = 0;      //是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则
        $this->is_location = 0;      //存在支持门店，0：否1：是
        
        $this->buy_count = intval($req['buy_count']);      //销量（购买的件数）
    }

    /**
     * 设置积分商品数据
     * @param $req
     */
    public function setScore($req)
    {
        $this->shop_cate_id = strim($req['shop_cate_id']);      //商城商品的分类ID
        $this->brand_id = intval($req['brand_id']);      //所归属的品牌
        $this->weight = round($req['weight'],2);       //商品重量，实体商品填写，用于运费计算
        $this->delivery_type = intval($req['delivery_type']);      //配送方式(默认物流配送)1物流、2无需配送、3驿站
        $this->carriage_template_id = intval($req['carriage_template_id']);      //运费模板关联ID
        $this->return_score = "-".abs($req['deal_score']);  //购买积分商品需要的积分
        $this->notes = $req['notes'];      //购买需知
        //需要处理的数据

        //关于配送,物流和驿站都需要配送
        if(intval($req['delivery_type'])==1 || intval($req['delivery_type'])==3){
            $this->is_delivery = 1; //是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费
        }else{
            $this->is_delivery = 0;
        }

        //强制属性
        $this->is_shop = 1;      //标识是否为商城商品 0:否 1:是
        $this->is_pick = 0;      //是否允许上门自提
        $this->buy_type = 1;      //团购商品的类型0：普通(团购、商品)，1：积分商品
        $this->deal_tag = 0;      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减
        $this->expire_refund = 0;   //是否支持过期退款( 过期未消费用户即可提交退款)
        $this->any_refund = 0;      //是否支持随时退款（未消费用户即可提交退款申请）
        $this->is_refund = 0;   //是否可以退款
        $this->multi_attr = 0;      //多套餐（自动判断是否有属性规格配置，有则打上该标签）
        $this->is_lottery = 0;      //是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则
        $this->is_location = 0;      //存在支持门店，0：否1：是
    }

    /**
     * 设置商户要保存的数据
     * @param $req
     */
    public function setSupplierSubmit($req)
    {
        //商户提交部分用到字段
        $this->deal_id = intval($req['deal_id']);   //商品表关联ID

        $this->cache_deal_cate_type_id = $req['cache_deal_cate_type_id'];   //团购商品:子分类ID缓存
        $this->cache_location_id = $req['cache_location_id']; //支持门店ID缓存
        $this->cache_focus_imgs = $req['cache_focus_imgs'];  //图集缓存
        $this->cache_deal_attr = $req['cache_deal_attr'];   //属性缓存
        $this->cache_stock_data = $req['cache_stock_data'];  //属性库存缓存
        $this->cache_attr_stock = $req['cache_attr_stock'];  //对应attr_stock表内容
        $this->cache_relate = $req['cache_relate'];  //关联商品缓存
        $this->biz_apply_status = $req['biz_apply_status'];  //审核状态

    }

    /**
     * 设置商户要保存的数据
     * @param $req
     */
    public function setSaveSupplierSubumit()
    {
        $save_data = array(

            'deal_id'=>$this->deal_id,   //商品表关联ID
            'cache_deal_cate_type_id'=>$this->cache_deal_cate_type_id,   //团购商品:子分类ID缓存
            'cache_location_id'=>$this->cache_location_id , //支持门店ID缓存
            'cache_focus_imgs'=>$this->cache_focus_imgs ,  //图集缓存
            'cache_deal_attr'=>$this->cache_deal_attr ,   //属性缓存
            'cache_stock_data'=>$this->cache_stock_data ,  //属性库存缓存
            'cache_attr_stock'=>$this->cache_attr_stock ,  //对应attr_stock表内容
            'cache_relate'=>$this->cache_relate ,  //关联商品缓存
        	'biz_apply_status'=>$this->biz_apply_status ,  //审核状态
        	'create_time'=>$this->create_time ,  //发布时间
        );
        $this->data = array_merge($this->data,$save_data);


    }

    /**
     * 设置保存的基础数据
     */
    public function setSaveBase()
    {
        $this->data = array(
            'name' => $this->name ,      //商品名称
            'sub_name' => $this->sub_name ,      //短名称
            'description' => $this->description ,     //信息描述详情
            'phone_description' => $this->phone_description ,      //手机端描述

            'begin_time' => $this->begin_time ,      //上线开始时间，可为0为不限时
            'end_time' => $this->end_time ,        //下架时间，可为0为不限时
            'min_bought' => $this->min_bought ,      //最小购买量，用于团购产品的成团判断
            'max_bought' => $this->max_bought ,      //最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)
            'user_min_bought' => $this->user_min_bought ,      //会员下单的最小量
            'user_max_bought' => $this->user_max_bought ,      //每个会员购买的上限

            'is_delete' => $this->is_delete ,      //删除标识
            'brief' => $this->brief ,     //商品简介
            'is_effect' => $this->is_effect ,      //有效性标识
            'sort' => $this->sort ,      //前台展示排序 由大到小

            'seo_title' => $this->seo_title ,        //自定义的页面seo标题
            'seo_keyword' => $this->seo_keyword ,      //自定义的页面seo关键词
            'seo_description' => $this->seo_description ,      //自定义的页面seo描述
            'uname' => $this->uname ,      //url别名，用于重写与seo收录的优化
            'buyin_app' => $this->buyin_app ,      //是否仅展示在app端0否 1是

            //强制属性
            'is_shop' =>$this->is_shop,         //标识是否为商城商品 0:否 1:是
            'buy_type' => $this->buy_type ,      //团购商品的类型0：普通(团购、商品)，1：积分商品
            'is_delivery' => $this->is_delivery , //是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费

            //过滤掉改版后不要的数据
            'free_delivery' => $this->free_delivery ,
            'define_payment' => $this->define_payment ,
            'cart_type' => $this->cart_type ,
            'allow_promote' => $this->allow_promote ,

            //需要处理的数据
            'icon' => $this->icon ,          //小图
            'img' => $this->img ,          //主图

            'deal_tag' => $this->deal_tag ,      //商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减
            'expire_refund' => $this->expire_refund ,   //是否支持过期退款( 过期未消费用户即可提交退款)
            'any_refund' => $this->any_refund ,      //是否支持随时退款（未消费用户即可提交退款申请）
            'is_refund' => $this->is_refund ,       //是否可退款
            'multi_attr' => $this->multi_attr ,      //多套餐（自动判断是否有属性规格配置，有则打上该标签）
            'is_lottery' => $this->is_lottery ,      //是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则
            'is_location' => $this->is_location ,      //存在支持门店，0：否1：是
            'is_pick' => $this->is_pick ,      //是否允许上门自提
            'max_bought' => $this->max_bought ,      //最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)
        );
        if ($this->id){
            $this->data['update_time'] = $this->update_time;
        }else{
            $this->data['create_time'] = $this->create_time;
            $this->data['update_time'] = $this->update_time;
        }

    }

    /**
     * 设置保存团购的数据
     */
    public function setSaveTaun()
    {
        $save_data = array(
            'cate_id' => $this->cate_id ,       //生活服务分类ID 数据格式用英文“，”逗号分隔
            'supplier_id' => $this->supplier_id ,   //所属的商户ID

            'origin_price' => $this->origin_price ,       //原价
            'current_price' => $this->current_price ,      //当前销售价
            'balance_price' => $this->balance_price ,      //与商家的结算价（即商价提供给平台商的成本价）
            'buy_count' => $this->buy_count,    //虚拟件数
            'city_id' => $this->city_id ,        //所属的城市
            'is_coupon' => $this->is_coupon ,      //是否发放消费券
            'deal_type' => $this->deal_type ,      //发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)
            'return_money' => $this->return_money ,      //购买即返现的金额(该项可填负数，也可作为额外消费的金额)
            'return_score' => $this->return_score ,      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
            'deal_goods_type' => $this->deal_goods_type ,   //商品类型（用于生成相应类型的属性规格配置项）
            'coupon_time_type' => $this->coupon_time_type ,      //0：指定时间过期 1:按下单日起xx天过期
            'coupon_day' => $this->coupon_day ,            //下单后xx天内失效

            'coupon_begin_time' => $this->coupon_begin_time ,     //发放消费券的生效时间
            'coupon_end_time' => $this->coupon_end_time ,       //发放的消费券的过期时间
            'is_referral' => $this->is_referral ,   //是否允许购买返利给邀请人
            'discount' => $this->discount ,      //商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比
            'notice' => $this->notice ,        //是否参与预告（未到上线期的商品，默认不展示在前台，该项为1表示可以上线展示预告）
            'forbid_sms'=>$this->forbid_sms,    //是否禁用短信发送功能，禁用短信则该商品的购物不会短信发券
            'xpoint' => $this->xpoint ,      //经度（第一个分店的经度）
            'ypoint' => $this->ypoint ,      //纬度（第一个分店的纬度）
            'notes' => $this->notes ,      //购买需知

            'set_meal' => $this->set_meal ,      //移动端套餐模板
            'pc_setmeal' => $this->pc_setmeal ,      //PC端套餐模板
            'is_fx' => $this->is_fx ,      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取

            'auto_order' => $this->auto_order ,  //是否打上免预约标识 0:否 1:是


            //商户提交时候存在
            'account_id' => $this->account_id ,      //商家提交的商家帐号ID

            'delivery_type' => $this->delivery_type ,   //配送方式(默认物流配送)1物流、2无需配送、3驿站
            'platform_type' => $this->platform_type ,      //发布平台类型(默认平台发布)
            'publish_verify_balance'=>$this->publish_verify_balance,    //商户商品费率
            'allow_user_discount'=>$this->allow_user_discount,    //是否参与会员等级折扣优惠，0为不参与，1为参与
            'is_recommend' => $this->is_recommend ,      //推荐到首页展示

        );
        $this->data = array_merge($this->data,$save_data);


    }

    /**
     * 设置保存商品的数据
     */
    public function setSaveShop()
    {
        $save_data = array(
            'shop_cate_id' => $this->shop_cate_id ,      //商城商品的分类ID 数据格式用“，”逗号分隔
            'supplier_id' => $this->supplier_id,  //商户ID
            'origin_price' => $this->origin_price ,       //原价
            'current_price' => $this->current_price ,      //当前销售价
            'balance_price' => $this->balance_price ,      //与商家的结算价（即商价提供给平台商的成本价）.
            'buy_count' => $this->buy_count,    //虚拟件数
            'return_money' => $this->return_money ,      //购买即返现的金额(该项可填负数，也可作为额外消费的金额)
            'return_score' => $this->return_score ,      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
            'weight' => $this->weight ,        //商品重量，实体商品填写，用于运费计算
            'deal_goods_type' => $this->deal_goods_type ,   //商品类型（用于生成相应类型的属性规格配置项）
            'is_referral' => $this->is_referral ,   //是否允许购买返利给邀请人
            'discount' => $this->discount ,      //商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比
            'brand_id' => $this->brand_id ,      //所归属的品牌
            'is_recommend' => $this->is_recommend ,      //推荐到首页展示
            'notes' => $this->notes ,      //购买需知
            'is_fx' => $this->is_fx ,      //是否为分销商品0:不是 1:系统强制分配 2:允许会员领取
            'delivery_type' => $this->delivery_type ,      //配送方式(默认物流配送)1物流、2无需配送、3驿站
            'carriage_template_id' => $this->carriage_template_id ,      //运费模板关联ID
            'recommend_user_id' => $this->recommend_user_id ,      //此商品的推荐会员ID
            'recommend_user_return_ratio' => $this->recommend_user_return_ratio ,      //推荐会员返佣率,必须存在推荐会员ID
            'dist_service_rate' => $this->dist_service_rate ,      //驿站服务费率


            //如果是平台代替商户发布的，强制发布平台类型为商户
            'platform_type' => $this->platform_type ,   //发布平台类型(默认平台发布)

            //强制属性
            'publish_verify_balance'=>$this->publish_verify_balance,    //商户商品费率
            'allow_user_discount'=>$this->allow_user_discount,    //是否参与会员等级折扣优惠，0为不参与，1为参与
        );
        $this->data = array_merge($this->data,$save_data);
    }

    /**
     * 设置积分商品保存的数据
     */
    public function setSaveScore()
    {
        $save_data = array(
            'shop_cate_id' => $this->shop_cate_id ,      //商城商品的分类ID
            'brand_id' => $this->brand_id ,      //所归属的品牌
            'notes' => $this->notes ,      //购买需知
            'weight' => $this->weight ,       //商品重量，实体商品填写，用于运费计算
            'delivery_type' => $this->delivery_type ,      //配送方式(默认物流配送)1物流、2无需配送、3驿站
            'carriage_template_id' => $this->carriage_template_id ,      //运费模板关联ID
            'return_score' => $this->return_score ,      //购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)
        );
        $this->data = array_merge($this->data,$save_data);
    }

    /**
     * 保存方法
     * @param $req
     */
    public function save()
    {
        $result = array();

        //设置标准的数据保存
        $this->setSaveBase();
        //设置相关类型的数据保存
        switch ($this->type){
            case 'tuan': $this->SetSaveTaun();break;
            case 'shop': $this->SetSaveShop();break;
            case 'score': $this->SetSaveScore();break;
        }

        if($this->id>0){
            $deal_old_data = $GLOBALS['db']->getRow("select current_price from ".DB_PREFIX."deal where id=".$this->id);
            if($deal_old_data['current_price']!=$this->current_price){
                set_deal_disable($this->id);
            }
            $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$this->data,"UPDATE","id=".$this->id);

        }else{
            $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$this->data);
            $this->id = $GLOBALS['db']->insert_id();
        }

        //主表数据保存城后处理根据商品类型，保存不同的关联表
        if ($this->id>0){
            switch ($this->type){
                case 'tuan': $this->saveTuan();break;
                case 'shop': $this->saveShop();break;
                case 'score': $this->saveScore();break;
            }



            $result['info'] = "保存成功";
            $result['status'] = 1;
            $result['id'] = $this->id;
        }else{
            $result['info'] = "保存失败";
            $result['status'] = 0;
        }

        return $result;
    }

    /**
     * 保存团购关联数据
     * @param $req
     */
    public function saveTuan()
    {
        $this->saveParametImg();
        $this->saveParametAttrAndStock();
        $this->saveParametCate();
        $this->saveParametDealFxSalary();
        $this->saveParametLocation();

        //同步信息
        syn_deal_status($this->id);
        syn_deal_match($this->id);
    }

    /**
     * 保存商品关联数据
     * @param $req
     */
    public function saveShop()
    {
        $this->saveParametImg();
        $this->saveParametAttrAndStock();
        $this->saveParametDealFxSalary();
        $this->saveParametRelateGoods();
        $this->saveParametCarriageTemplate();
        $this->saveParametLocation();

        //同步信息
        syn_deal_status($this->id);
        syn_deal_match($this->id);
    }

    /**
     * 保存积分商品关联数据
     * @param $req
     */
    public function saveScore($req)
    {
        $this->saveParametImg($req);
        $this->saveParametCate($req);
        $this->saveParametCarriageTemplate();

        //同步信息
        syn_deal_status($this->id);
        syn_deal_match($this->id);
    }

    /**
     * 保存商户提交数据
     * @param bool $is_auto_publish
     */
    public function saveSupplierSubmiet($is_auto_publish = false)
    {
        $result = array();
        //设置标准的数据保存
        $this->setSaveBase();
        //设置相关类型的数据保存
        switch ($this->type){
            case 'tuan': $this->SetSaveTaun();break;
            case 'shop': $this->SetSaveShop();break;
        }
        //是否自动发布
        if($is_auto_publish){
            $table = DB_PREFIX."deal";
            //判断更新还是插入
            if($this->deal_id){
                $GLOBALS['db']->autoExecute($table,$this->data,"UPDATE","id=".$this->deal_id);
                $this->id = $this->deal_id;
            }else{
                $GLOBALS['db']->autoExecute($table,$this->data);
                $this->id = $GLOBALS['db']->insert_id();
            }
        }else{
            $table = DB_PREFIX."deal_submit";
            $this->setSaveSupplierSubumit();
            $GLOBALS['db']->autoExecute($table,$this->data);
            $this->id = $GLOBALS['db']->insert_id();
        }

        $result['id'] = intval($this->id);

        //是否自动发布
        if($is_auto_publish){
            if ($this->id>0){
                switch ($this->type){
                    case 'tuan': $this->saveTuan();break;
                    case 'shop': $this->saveShop();break;
                }

                //同步信息
                syn_deal_status($this->id);
                syn_deal_match($this->id);

                $result['info'] = "保存成功";
                $result['status'] = 1;
            }else{
                $result['info'] = "保存失败";
                $result['status'] = 0;
            }
        }else{
            if ($this->id>0){
                $result['info'] = "保存成功";
                $result['status'] = 1;
            }else{
                $result['info'] = "保存失败";
                $result['status'] = 0;
            }
        }
        return $result;
    }

    /************************************************************************
     *                          关联数据处理部分
     ************************************************************************/

    /**
     * 处理图片
     * @param $req
     */
    private function saveParametImg()
    {
        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_gallery where deal_id = ".$this->id);
        $imgs = $this->imgs;
        foreach($imgs as $k=>$v)
        {
            if($v!='')
            {
                $img_data['deal_id'] = $this->id;
                $img_data['img'] = $v;
                $img_data['sort'] = $k;
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_gallery",$img_data);
            }
        }
    }

    /**
     * 处理属性库存
     * @param $req
     */
    private function saveParametAttrAndStock()
    {
        //开始处理属性
        $deal_attr = $this->deal_attr;
        $deal_attr_price = $this->deal_attr_price;
        $deal_add_balance_price = $this->deal_add_balance_price;
        $stock_cfg_num = $this->stock_cfg_num;
        $stock_buy_count = $this->stock_buy_count;
        //属性的数量，确定遍历的级别

        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_attr where deal_id=".$this->id);
        //保存属性数据
        foreach ($deal_attr as $k=>$v){
            foreach ($v as $kk=>$attr_name){
                $ins_data = array();
                $ins_data['deal_id'] = $this->id;
                $ins_data['name'] = $attr_name;
                $ins_data['goods_type_attr_id'] = $k;
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_attr",$ins_data);
            }
        }
        //取出带数据ID的属性值
        $deal_attr_db = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_attr where deal_id = ".$this->id);
        $deal_attr_format = array();
        foreach ($deal_attr_db as $k=>$v){
            $deal_attr_format[$v['goods_type_attr_id']][$v['name']] = $v['id'];
        }

        //格式化提交的属性数据
        $deal_attr_req_format = array();
        foreach ($deal_attr as $k=>$v){
            foreach ($v as $kk=>$attr_name){
                $deal_attr_req_format[$k][$deal_attr_format[$k][$attr_name]] = $attr_name;
            }
        }
        //格式化属性对应库存的数据格式
        $group_attr = array();
        $this->groupAttrFun($deal_attr_req_format,$group_attr);

        //开始创建属性库存
        $GLOBALS['db']->query("delete from ".DB_PREFIX."attr_stock where deal_id=".$this->id);

        foreach ($group_attr as $k=>$v){
            $stock_data = array();
            $stock_data['deal_id'] = $this->id;
            $stock_data['attr_cfg'] = serialize($v['data']);
            $stock_data['stock_cfg'] = $stock_cfg_num[$k];
            $stock_data['attr_str'] = $v['attr_key_str']['attr_str'];
            $stock_data['attr_key'] = $v['attr_key_str']['attr_key'];
            $stock_data['add_balance_price'] = $deal_add_balance_price[$k];
            $stock_data['price'] = $deal_attr_price[$k];
            $stock_data['buy_count'] = $stock_buy_count[$k];

            $GLOBALS['db']->autoExecute(DB_PREFIX."attr_stock",$stock_data);

        }
    }

    /**
     * 处理分类库存调整
     * @param $deal_attr
     * @param $group_attr
     */
    private function groupAttrFun($deal_attr,&$group_attr)
    {
        $attr_count = count($deal_attr);
        $level_1 = current($deal_attr);
        if($attr_count > 1)
            $level_2 = next($deal_attr);
        if ($attr_count>2)
            $level_3 = next($deal_attr);

        foreach ($level_1 as $k=>$v){
            if($attr_count==1){
                $k_name = $k;
                $v_value = $v;
                $group_attr[] = array("attr_key_str"=>array('attr_key'=>$k_name,'attr_str'=>$v_value),'data'=>array($k=>$v));
            }else{
                foreach ($level_2 as $kk=>$vv){
                    if($attr_count==2){
                            $k_name = $k."_".$kk;
                            $v_value = $v.$vv;
                            $group_attr[] = array("attr_key_str"=>array('attr_key'=>$k_name,'attr_str'=>$v_value),'data'=>array($k=>$v,$kk=>$vv));
                    }else{
                        foreach ($level_3 as $kkk=>$vvv){
                            $k_name = $k."_".$kk."_".$kkk;
                            $v_value = $v.$vv.$vvv;
                            $group_attr[] = array("attr_key_str"=>array('attr_key'=>$k_name,'attr_str'=>$v_value),'data'=>array($k=>$v,$kk=>$vv,$kkk=>$vvv));
                        }

                    }
                }
            }
        }
    }

    /**
     * 处理生活服务分类
     * @param $req
     */
    private function saveParametCate()
    {
        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cate_type_deal_link where deal_id=".$this->id);
        $deal_cate_type_id = explode(",",$this->deal_cate_type_id);
        foreach($deal_cate_type_id as $type_id)
        {
            $link_data = array();
            $link_data['deal_cate_type_id'] = $type_id;
            $link_data['deal_id'] = $this->id;
            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_cate_type_deal_link",$link_data);
        }
    }

    /**
     * 关联商品处理
     * @param $req
     */
    private function saveParametRelateGoods()
    {
        //增加商品关联购买
        $GLOBALS['db']->query("delete from ".DB_PREFIX."relate_goods where good_id=".$this->id);
        if(!empty($this->relate_goods_id)){
            $saveArray = array(
                'good_id'		=> $this->id,
                'relate_ids'	=> implode(',', $this->relate_goods_id),
                'is_shop'		=> 1,
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX."relate_goods",$saveArray);
        }
    }

    /**
     * 总库存设置
     */
    private function saveParametStock()
    {
        $deal_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_stock where deal_id = ".$this->id);
        if(!$deal_stock)
        {
            $deal_stock['deal_id'] = $this->id;
            $deal_stock['stock_cfg'] = $this->max_bought;
            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_stock",$deal_stock,"INSERT","","SILENT");
        }
        else
        {
            $GLOBALS['db']->query("update ".DB_PREFIX."deal_stock set stock_cfg = ".$this->max_bought." where deal_id = ".$this->id);
        }
    }

    /**
     * 处理分销数据
     */
    private function saveParametDealFxSalary()
    {
        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_fx_salary where deal_id=".$this->id);
        if($this->is_allow_fx){
            foreach ($this->fx_salary as $k=>$v){
                $fx_data = array();
                $fx_data['deal_id'] = $this->id;
                $fx_data['fx_level'] = $k;
                $fx_data['fx_salary_type'] = $this->fx_salary_type;
                $fx_data['fx_salary'] = $this->fx_salary_type?floatval($v/100):floatval($v);
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_fx_salary",$fx_data,"INSERT","","SILENT");
            }
        }
    }

    private function saveParametCarriageTemplate()
    {
        if($this->carriage_template_id){
            $GLOBALS['db']->query("update ".DB_PREFIX."carriage_template set is_use = 1 where id=".$this->carriage_template_id);
        }
    }

    private function saveParametLocation()
    {
        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_location_link where deal_id=".$this->id);

        foreach($this->location_id as $location_id)
        {
            $link_data = array();
            $link_data['location_id'] = $location_id;
            $link_data['deal_id'] = $this->id;
            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_location_link",$link_data,"INSERT","","SILENT");
        }
    }
}