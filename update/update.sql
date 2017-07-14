4.0;
ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提',
ADD COLUMN `set_meal`  text NOT NULL  COMMENT '手机端套餐模板',
ADD COLUMN `pc_setmeal`  text NOT NULL  COMMENT 'PC端套餐模板';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提';

ALTER TABLE `%DB_PREFIX%deal_cart`
ADD COLUMN `is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提';

ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提';


CREATE TABLE `%DB_PREFIX%deal_stock` (
  `deal_id` int(11) NOT NULL,
  `buy_count` int(11) NOT NULL,
  `stock_cfg` int(11) NOT NULL,
  `time_status` tinyint(1) NOT NULL,
  `buy_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`deal_id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品库存事务表';


insert into `%DB_PREFIX%deal_stock` (deal_id,buy_count,stock_cfg,time_status,buy_status) select id,buy_count,max_bought,time_status,buy_status from %DB_PREFIX%deal;

ALTER TABLE `%DB_PREFIX%attr_stock`
ENGINE=InnoDB;


ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `real_name`  varchar(255) NOT NULL COMMENT '会员真实姓名' ;

ALTER TABLE `%DB_PREFIX%withdraw`
ADD COLUMN `bank_mobile`  varchar(255) NOT NULL COMMENT '银行预留手机号' AFTER `is_delete`,
ADD COLUMN `is_bind`  tinyint(1) NOT NULL  COMMENT '是否绑定' AFTER `bank_mobile`;



CREATE TABLE `%DB_PREFIX%user_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_account` varchar(255) NOT NULL,
  `bank_user` varchar(255) NOT NULL,
  `bank_mobile` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%store_pay_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '默认为 0 到店买单支付',
  `supplier_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 2:全部付款',
  `total_price` decimal(20,4) NOT NULL COMMENT '消费金额',
  `other_money` decimal(20,4) NOT NULL COMMENT '不可优惠金额',
  `pay_amount` decimal(20,4) NOT NULL COMMENT '实付金额 当pay_amount+discount_price = total_price 支付成功',
  `discount_price` decimal(20,4) NOT NULL COMMENT '优惠金额',
  `promote_ids` varchar(100) NOT NULL COMMENT '促销规则编号',
  `promote_data` text NOT NULL COMMENT '存储优惠的信息',
  `after_sale` tinyint(1) NOT NULL COMMENT '售后处理标识 0:正常订单 1:退款处理的订单',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `payment_id` int(11) NOT NULL COMMENT '支付方式',
  `bank_id` varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
  `location_id` int(11) NOT NULL COMMENT '消费门店ID',
  `monery` int(11) NOT NULL COMMENT '购买的积分数',
  `extra_status` tinyint(1) NOT NULL COMMENT '额外的订单标识 0:正常的订单 1.金额超额产生退款的订单（多次支付，重付通知） ,自动退款到用户的订单）',
  `user_id` int(11) NOT NULL COMMENT '用户编号',
  `user_mobile` varchar(11) NOT NULL COMMENT '用户手机号',
  `payment_fee`  decimal(20,4) NOT NULL COMMENT '手续费',
  `promote`  text NOT NULL COMMENT '该订单享受的优惠的详细数据',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sn` (`order_sn`),
  KEY `order_sn` (`order_sn`),
  KEY `type` (`type`),
  KEY `supplier_id` (`supplier_id`),
  KEY `pay_status` (`pay_status`),
  KEY `order_status` (`order_status`),
  KEY `is_delete` (`is_delete`),
  KEY `promote_id` (`promote_ids`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户订单表';

ALTER TABLE `%DB_PREFIX%payment_notice`
ADD COLUMN `order_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:全部订单 ,1:外卖预定订单,2:商户订单,3:普通订单,4:会员买单';

CREATE TABLE `%DB_PREFIX%store_pay_order_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_info` text NOT NULL,
  `log_time` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户订单操作的日志表';


ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `is_store_payment`  tinyint(1) NOT NULL COMMENT '商户是否支持到店支付',
ADD COLUMN `store_payment_rate`  decimal(8,4) NOT NULL COMMENT '到店支付费率' AFTER `is_store_payment`,
ADD COLUMN `store_pay_explain`  text NOT NULL COMMENT '买单说明' AFTER `store_payment_rate`;

ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `open_store_payment`  tinyint(1) NULL COMMENT '针对门店开启到店支付';

ALTER TABLE `%DB_PREFIX%promote`
ADD COLUMN `type`  tinyint(1) NOT NULL COMMENT '促销规则类型0：默认全站  1：商户促销' AFTER `description`,
ADD COLUMN `supplier_id`  int(11) NOT NULL COMMENT '商户ID' AFTER `type`,
ADD COLUMN `name`  varchar(255) NOT NULL COMMENT '促销活动的名称' AFTER `id`,
ADD COLUMN `supplier_or_platform`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0平台，1商户' AFTER `supplier_id`,
ADD UNIQUE INDEX `class_name_supplier_id_unique` (`class_name`, `supplier_id`);

ALTER TABLE `%DB_PREFIX%deal_cate`
ADD COLUMN `description`  varchar(255) NOT NULL COMMENT '分类描述' AFTER `brief`,
ADD COLUMN `is_new`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '1为new' AFTER `is_effect`;

INSERT INTO `%DB_PREFIX%m_config` VALUES (null,'close_index_cate','是否关闭首页分类位','0','4','基础配置','106');
UPDATE `%DB_PREFIX%conf` set `value` = '4.0' where name = 'DB_VERSION';

 