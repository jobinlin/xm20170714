-- fanwe SQL Dump Program
-- Apache/2.2.17 (Win32) PHP/5.3.3
--
-- DATE : 2015-08-13 16:05:04
-- MYSQL SERVER VERSION : 5.5.8-log
-- PHP VERSION : apache2handler
-- Vol : 1


DROP TABLE IF EXISTS `%DB_PREFIX%admin`;
CREATE TABLE `%DB_PREFIX%admin` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`adm_name` varchar(255) NOT NULL COMMENT '管理员用户名',
`adm_password` varchar(255) NOT NULL COMMENT '管理员密码',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性控制',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`role_id` int(11) NOT NULL COMMENT '角色ID(权限控制用)',
`login_time` int(11) NOT NULL COMMENT '最后登录时间',
`login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
PRIMARY KEY (`id`),
UNIQUE KEY `unique_adm_name` (`adm_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';
INSERT INTO `%DB_PREFIX%admin` VALUES ('1','admin','21232f297a57a5a743894a0e4a801fc3','1','0','4','1439424223','127.0.0.1');
DROP TABLE IF EXISTS `%DB_PREFIX%adv`;
CREATE TABLE `%DB_PREFIX%adv` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`tmpl` varchar(255) NOT NULL COMMENT '前台使用模板名称',
`adv_id` varchar(255) NOT NULL COMMENT '定义在模板文件里的广告位的ID名称，用于动态在模板上调用相应的广告位内容',
`code` text NOT NULL COMMENT '用于前台展示显示的html广告内容',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性控制标识',
`name` varchar(255) NOT NULL COMMENT '广告位名称，用于后台管理查询用',
`city_ids` varchar(255) NOT NULL COMMENT '用于控制广告显示在哪些城市，填入城市ID,用半角逗号分隔',
`rel_id` int(11) NOT NULL COMMENT '用于动态关联的广告定义，例如首页显示多个商品分类模块，每个分类模块下需要定义一个独立的广告，这种广告一般在商品分类，生活服务分类中单独设置，这里的rel_id指向相关的分类ID',
`rel_table` varchar(255) NOT NULL COMMENT '同rel_id，这里填的是相关的表名，例如商城分类的推荐广告，这里填入shop_cate',
PRIMARY KEY (`id`),
KEY `tmpl` (`tmpl`),
KEY `adv_id` (`adv_id`),
KEY `city_ids` (`city_ids`),
KEY `rel_id` (`rel_id`),
KEY `rel_table` (`rel_table`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='广告位表';
INSERT INTO `%DB_PREFIX%adv` VALUES ('50','fanwe','商城首页轮播广告2','<img src=\"./public/attachment/201502/25/11/54ed40ac7cb3a.png\" alt=\"\" border=\"0\" />','1','商城首页轮播广告2','','0','');
INSERT INTO `%DB_PREFIX%adv` VALUES ('46','fanwe','首页小轮播广告2','<img src=\"./public/attachment/201502/25/12/54ed559ba1dc1.jpg\" alt=\"\" border=\"0\" />','1','首页小轮播广告2','','0','');
INSERT INTO `%DB_PREFIX%adv` VALUES ('45','fanwe','首页小轮播广告1','<img src=\"./public/attachment/201502/25/12/54ed559176fa9.jpg\" alt=\"\" border=\"0\" />','1','首页小轮播广告1','','0','');
INSERT INTO `%DB_PREFIX%adv` VALUES ('44','fanwe','首页轮播广告2','<img src=\"./public/attachment/201502/25/11/54ed41c0e3216.png\" alt=\"\" border=\"0\" />','1','首页轮播广告2','','0','');
INSERT INTO `%DB_PREFIX%adv` VALUES ('49','fanwe','商城首页轮播广告1','<img src=\"./public/attachment/201502/25/11/54ed406379285.jpg\" alt=\"\" border=\"0\" />','1','商城首页轮播广告1','','0','');
INSERT INTO `%DB_PREFIX%adv` VALUES ('43','fanwe','首页轮播广告1','<img src=\"./public/attachment/201502/25/11/54ed41b6bfeec.JPG\" alt=\"\" border=\"0\" /><br />\r\n','1','首页轮播广告1','','0','');
DROP TABLE IF EXISTS `%DB_PREFIX%api_log`;
CREATE TABLE `%DB_PREFIX%api_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`act` varchar(30) NOT NULL,
`api` text NOT NULL,
`param_json` text NOT NULL,
`param_array` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COMMENT='移动端的调试日志表';
DROP TABLE IF EXISTS `%DB_PREFIX%api_login`;
CREATE TABLE `%DB_PREFIX%api_login` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '第三方登录名称',
`config` text NOT NULL COMMENT '序列化后的配置信息',
`class_name` varchar(255) NOT NULL COMMENT '接口类名',
`icon` varchar(255) NOT NULL COMMENT '登录用小图标显示',
`is_weibo` tinyint(1) NOT NULL COMMENT '是否微博接口，该接口标识可以同步信息到第三方的微博平台',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='第三方登录接口的安装表(新浪微博，QQ微博等)';
DROP TABLE IF EXISTS `%DB_PREFIX%apns_device_history`;
CREATE TABLE `%DB_PREFIX%apns_device_history` (
`pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
`clientid` int(11) NOT NULL,
`appname` varchar(255) NOT NULL,
`appversion` varchar(25) DEFAULT NULL,
`deviceuid` char(40) NOT NULL,
`devicetoken` char(64) NOT NULL,
`devicename` varchar(255) NOT NULL,
`devicemodel` varchar(100) NOT NULL,
`deviceversion` varchar(25) NOT NULL,
`pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
`pushalert` enum('disabled','enabled') DEFAULT 'disabled',
`pushsound` enum('disabled','enabled') DEFAULT 'disabled',
`development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
`status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
`archived` datetime NOT NULL,
PRIMARY KEY (`pid`),
KEY `clientid` (`clientid`),
KEY `devicetoken` (`devicetoken`),
KEY `devicename` (`devicename`),
KEY `devicemodel` (`devicemodel`),
KEY `deviceversion` (`deviceversion`),
KEY `pushbadge` (`pushbadge`),
KEY `pushalert` (`pushalert`),
KEY `pushsound` (`pushsound`),
KEY `development` (`development`),
KEY `status` (`status`),
KEY `appname` (`appname`),
KEY `appversion` (`appversion`),
KEY `deviceuid` (`deviceuid`),
KEY `archived` (`archived`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='弃用';
DROP TABLE IF EXISTS `%DB_PREFIX%apns_devices`;
CREATE TABLE `%DB_PREFIX%apns_devices` (
`pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
`clientid` int(11) NOT NULL,
`appname` varchar(255) NOT NULL,
`appversion` varchar(25) DEFAULT NULL,
`deviceuid` char(40) NOT NULL,
`devicetoken` char(64) NOT NULL,
`devicename` varchar(255) NOT NULL,
`devicemodel` varchar(100) NOT NULL,
`deviceversion` varchar(25) NOT NULL,
`pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
`pushalert` enum('disabled','enabled') DEFAULT 'disabled',
`pushsound` enum('disabled','enabled') DEFAULT 'disabled',
`development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
`status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
`created` datetime NOT NULL,
`modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`pid`),
UNIQUE KEY `appname` (`appname`,`appversion`,`deviceuid`),
KEY `clientid` (`clientid`),
KEY `devicetoken` (`devicetoken`),
KEY `devicename` (`devicename`),
KEY `devicemodel` (`devicemodel`),
KEY `deviceversion` (`deviceversion`),
KEY `pushbadge` (`pushbadge`),
KEY `pushalert` (`pushalert`),
KEY `pushsound` (`pushsound`),
KEY `development` (`development`),
KEY `status` (`status`),
KEY `created` (`created`),
KEY `modified` (`modified`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='ios推送设备列表';
DROP TABLE IF EXISTS `%DB_PREFIX%apns_logs`;
CREATE TABLE `%DB_PREFIX%apns_logs` (
`pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
`clientid` varchar(64) NOT NULL COMMENT '客户ID(会员ID)可为0(未登录的手机端用户)',
`fk_device` int(9) unsigned NOT NULL COMMENT '客户端信息',
`message` varchar(255) NOT NULL COMMENT '内容',
`delivery` datetime NOT NULL COMMENT '返回的发送时间',
`status` enum('queued','delivered','failed') CHARACTER SET latin1 NOT NULL DEFAULT 'queued' COMMENT '是否已发送',
`created` int(11) NOT NULL DEFAULT '0' COMMENT '系统内生成的发送时间',
`modified` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
`message_id` int(11) NOT NULL COMMENT '消息ID',
PRIMARY KEY (`pid`),
KEY `clientid` (`clientid`),
KEY `fk_device` (`fk_device`),
KEY `status` (`status`),
KEY `created` (`created`),
KEY `modified` (`modified`),
KEY `message` (`message`),
KEY `delivery` (`delivery`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COMMENT='APN推送日志表';
DROP TABLE IF EXISTS `%DB_PREFIX%apns_messages`;
CREATE TABLE `%DB_PREFIX%apns_messages` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`content` varchar(255) NOT NULL COMMENT '群发内容',
`send_time` int(11) NOT NULL COMMENT '预设发送时间',
`user_names` text NOT NULL COMMENT '用户名(用于配匹设备号，逗号分开，如填写的用户未用ios设备登录过，无法发出，不填写为全部发送)',
`status` tinyint(1) NOT NULL COMMENT '0:未发送 1:发送中 2:已发送',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ANPS群发推送消息';
DROP TABLE IF EXISTS `%DB_PREFIX%area`;
CREATE TABLE `%DB_PREFIX%area` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '名称',
`city_id` int(11) NOT NULL COMMENT '所属的城市 ',
`sort` int(11) NOT NULL COMMENT '排序，前台展示的排序，由大到小',
`pid` int(11) NOT NULL COMMENT '有pid表示为一级地区（行政区），有值为商圈（二级地区）',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='地区商圈表';
INSERT INTO `%DB_PREFIX%area` VALUES ('8','鼓楼区','15','1','0');
INSERT INTO `%DB_PREFIX%area` VALUES ('9','晋安区','15','2','0');
INSERT INTO `%DB_PREFIX%area` VALUES ('10','台江区','15','3','0');
INSERT INTO `%DB_PREFIX%area` VALUES ('11','仓山区','15','4','0');
INSERT INTO `%DB_PREFIX%area` VALUES ('12','马尾区','15','5','0');
INSERT INTO `%DB_PREFIX%area` VALUES ('13','五一广场','15','6','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('14','东街口','15','7','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('15','福州广场','15','8','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('16','省体育中心','15','9','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('17','西禅寺','15','10','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('18','社会主义学院','15','11','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('19','西洪路','15','12','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('20','屏山','15','13','8');
INSERT INTO `%DB_PREFIX%area` VALUES ('21','中亭街','15','14','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('22','六一中路','15','15','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('23','龙华大厦','15','16','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('24','时代名城','15','17','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('25','台江路','15','18','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('26','宝龙城市广场','15','19','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('27','万象城','15','20','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('28','桥亭','15','21','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('29','小桥头','15','22','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('30','交通路','15','23','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('31','中亭街','15','24','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('32','白马河','15','25','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('33','博美诗邦','15','26','10');
INSERT INTO `%DB_PREFIX%area` VALUES ('34','观海路','15','27','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('35','三叉街新村','15','28','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('36','北京金山','15','29','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('37','仓山镇','15','30','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('38','螺洲','15','31','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('39','三高路','15','32','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('40','下渡','15','33','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('41','工农路','15','34','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('42','首山路','15','35','11');
INSERT INTO `%DB_PREFIX%area` VALUES ('43','王庄新村','15','36','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('44','岳峰路','15','37','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('45','融侨东区','15','38','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('46','五里亭','15','39','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('47','五一新村','15','40','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('48','福光路','15','41','9');
INSERT INTO `%DB_PREFIX%area` VALUES ('49','五里亭','15','42','9');
DROP TABLE IF EXISTS `%DB_PREFIX%article`;
CREATE TABLE `%DB_PREFIX%article` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL COMMENT '文章标题',
`content` text NOT NULL COMMENT '文章内容',
`cate_id` int(11) NOT NULL COMMENT '文章分类ID',
`create_time` int(11) NOT NULL COMMENT '发表时间',
`update_time` int(11) NOT NULL COMMENT '更新时间',
`add_admin_id` int(11) NOT NULL COMMENT '发布人(管理员ID)',
`is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
`rel_url` varchar(255) NOT NULL COMMENT '自动跳转的外链',
`update_admin_id` int(11) NOT NULL COMMENT '更新人(管理员ID)',
`is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
`click_count` int(11) NOT NULL COMMENT '点击数',
`sort` int(11) NOT NULL COMMENT '排序 由大到小',
`seo_title` text NOT NULL COMMENT '自定义seo页面标题',
`seo_keyword` text NOT NULL COMMENT '自定义seo页面keyword',
`seo_description` text NOT NULL COMMENT '自定义seo页面标述',
`uname` varchar(255) NOT NULL,
`notice_page` tinyint(1) NOT NULL,
`sub_title` varchar(255) NOT NULL,
`brief` text NOT NULL,
PRIMARY KEY (`id`),
KEY `cate_id` (`cate_id`),
KEY `create_time` (`create_time`),
KEY `update_time` (`update_time`),
KEY `click_count` (`click_count`),
KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='文章展示';
INSERT INTO `%DB_PREFIX%article` VALUES ('20','关于我们','关于我们','11','0','1305160934','0','1','','0','0','18','11','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('27','免责条款','免责条款','19','1305160898','1305160898','0','1','','0','0','3','18','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('28','隐私保护','隐私保护','7','1305160911','1424803882','0','1','','0','0','4','19','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('29','咨询热点','咨询热点','10','1305160923','1424803868','0','1','','0','0','2','20','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('30','联系我们','联系我们','11','1305160934','1424803859','0','1','','0','0','30','21','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('31','公司简介','公司简介','11','1305160946','1424803850','0','1','','0','0','92','22','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('5','如何抽奖','如何抽奖','7','0','1424803982','0','1','','0','0','3','0','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('6','加入我们','加入我们','11','0','1324319464','0','1','u:shop|user#register','0','0','22','2','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('44','RSS订阅','','9','1424804133','1424804133','0','1','u:index|rss','0','0','0','23','','','','','0','','');
INSERT INTO `%DB_PREFIX%article` VALUES ('10','友情链接','','10','0','1424804032','0','1','u:index|link','0','0','0','6','','','','','0','','');
DROP TABLE IF EXISTS `%DB_PREFIX%article_cate`;
CREATE TABLE `%DB_PREFIX%article_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL COMMENT '分类名称',
`brief` varchar(255) NOT NULL COMMENT '分类简介(备用字段)',
`pid` int(11) NOT NULL COMMENT '父ID，程序分类可分二级',
`is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
`is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
`type_id` tinyint(1) NOT NULL COMMENT '类型\r\n0:普通文章（可通前台分类列表查找到）\r\n1.帮助文章（用于前台页面底部的站点帮助）\r\n2.公告文章（用于前台页面公告模块的调用）\r\n3.系统文章（自定义的一些文章，需要前台自定义一些入口链接到该文章）\r\n所属该分类的所有文章类型与分类一致',
`sort` int(11) NOT NULL,
`iconfont` varchar(15) NOT NULL COMMENT '针对帮助文档分类的图标',
PRIMARY KEY (`id`),
KEY `pid` (`pid`),
KEY `type_id` (`type_id`),
KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='文章分类表';
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('11','公司信息','','0','1','0','1','4','&#58899;');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('10','商务合作','','0','1','0','1','2','&#58891;');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('9','获取更新','','0','1','0','1','3','&#58898;');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('7','用户帮助','','0','1','0','1','1','&#58897;');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('18','商城公告','','0','1','0','2','5','');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('19','系统文章','','0','1','0','3','6','');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('22','热门推荐','','0','1','0','2','7','');
DROP TABLE IF EXISTS `%DB_PREFIX%attr_stock`;
CREATE TABLE `%DB_PREFIX%attr_stock` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`attr_cfg` text NOT NULL COMMENT '序列化的多维属性配置数据（包含属性ID，属性值）',
`stock_cfg` int(11) NOT NULL DEFAULT '-1' COMMENT '该属性组合的库存数',
`attr_str` text NOT NULL COMMENT '字符串展示的属性组合',
`buy_count` int(11) NOT NULL COMMENT '当前属性组合的已卖的量，用于库存验证',
`attr_key` varchar(100) NOT NULL COMMENT '属性ID以下划线从小到大排序的key',
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='规格属性库存表，用于多属性，多套餐商品的多库存定义';
DROP TABLE IF EXISTS `%DB_PREFIX%auto_cache`;
CREATE TABLE `%DB_PREFIX%auto_cache` (
`cache_key` varchar(100) NOT NULL COMMENT '程序中识别的缓存唯一ID',
`cache_type` varchar(100) NOT NULL COMMENT '缓存接口类型',
`cache_data` text NOT NULL COMMENT '缓存值',
`cache_time` int(11) NOT NULL COMMENT '缓存时间',
PRIMARY KEY (`cache_key`,`cache_type`),
KEY `cache_type` (`cache_type`),
KEY `cache_key` (`cache_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='程序内置自动缓存引擎的配套数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%brand`;
CREATE TABLE `%DB_PREFIX%brand` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '品牌名称',
`logo` varchar(255) NOT NULL COMMENT '品牌logo',
`brand_promote_logo` varchar(255) NOT NULL COMMENT '用于限时促销模块展示的品牌促销图片',
`brief` text NOT NULL COMMENT '品牌简介',
`sort` int(11) NOT NULL COMMENT '排序(由大到小)',
`shop_cate_id` int(11) NOT NULL COMMENT '所属的商品分类，用于前台分类展示用',
`brand_promote` tinyint(1) NOT NULL COMMENT '是否参与品牌限时促销的标识',
`begin_time` int(11) NOT NULL COMMENT '限时促销开始时间',
`end_time` int(11) NOT NULL COMMENT '限时促销结束时间',
`time_status` tinyint(1) NOT NULL COMMENT '0:已上线 1:未上线 2:已过期',
`dy_count` int(11) DEFAULT '0' COMMENT '品牌订阅数量',
`tag` text NOT NULL COMMENT '检索标签',
`tag_match` text NOT NULL,
`tag_match_row` text NOT NULL,
PRIMARY KEY (`id`),
KEY `shop_cate_id` (`shop_cate_id`),
FULLTEXT KEY `tag_match` (`tag_match`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='商品品牌配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%brand_dy`;
CREATE TABLE `%DB_PREFIX%brand_dy` (
`uid` int(11) NOT NULL,
`brand_id` int(11) NOT NULL,
PRIMARY KEY (`uid`,`brand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='移动端品牌订阅功能的数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%conf`;
CREATE TABLE `%DB_PREFIX%conf` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`value` text NOT NULL,
`group_id` int(11) NOT NULL,
`input_type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
`value_scope` text NOT NULL COMMENT '取值范围',
`is_effect` tinyint(1) NOT NULL,
`is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=utf8 COMMENT='系统配置表';
INSERT INTO `%DB_PREFIX%conf` VALUES ('1','DEFAULT_ADMIN','admin','1','0','','1','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('2','URL_MODEL','0','1','1','0,1','1','1','3');
INSERT INTO `%DB_PREFIX%conf` VALUES ('4','TIME_ZONE','8','1','1','0,8','1','1','1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('5','ADMIN_LOG','1','1','1','0,1','0','1','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('6','DB_VERSION','6.5','0','0','','1','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('7','DB_VOL_MAXSIZE','8000000','1','0','','1','1','11');
INSERT INTO `%DB_PREFIX%conf` VALUES ('8','WATER_MARK','./public/attachment/201011/4cdde85a27105.gif','2','2','','1','1','48');
INSERT INTO `%DB_PREFIX%conf` VALUES ('24','CURRENCY_UNIT','&yen;','3','0','','1','0','21');
INSERT INTO `%DB_PREFIX%conf` VALUES ('10','BIG_WIDTH','500','2','0','','0','0','49');
INSERT INTO `%DB_PREFIX%conf` VALUES ('11','BIG_HEIGHT','500','2','0','','0','0','50');
INSERT INTO `%DB_PREFIX%conf` VALUES ('12','SMALL_WIDTH','200','2','0','','0','0','51');
INSERT INTO `%DB_PREFIX%conf` VALUES ('13','SMALL_HEIGHT','200','2','0','','0','0','52');
INSERT INTO `%DB_PREFIX%conf` VALUES ('14','WATER_ALPHA','75','2','0','','1','1','53');
INSERT INTO `%DB_PREFIX%conf` VALUES ('15','WATER_POSITION','4','2','1','1,2,3,4,5','1','1','54');
INSERT INTO `%DB_PREFIX%conf` VALUES ('16','MAX_IMAGE_SIZE','3000000','2','0','','1','1','55');
INSERT INTO `%DB_PREFIX%conf` VALUES ('18','MAX_FILE_SIZE','1','1','0','','0','1','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('19','ALLOW_FILE_EXT','1','1','0','','0','1','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('20','BG_COLOR','#ffffff','2','0','','0','0','57');
INSERT INTO `%DB_PREFIX%conf` VALUES ('21','IS_WATER_MARK','1','2','1','0,1','1','1','58');
INSERT INTO `%DB_PREFIX%conf` VALUES ('22','TEMPLATE','fanwe','3','0','','1','0','17');
INSERT INTO `%DB_PREFIX%conf` VALUES ('126','YOUHUI_SEND_LIMIT','5','5','0','','1','0','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('25','SCORE_UNIT','积分','3','0','','1','0','22');
INSERT INTO `%DB_PREFIX%conf` VALUES ('26','USER_VERIFY','1','4','1','0,1','1','0','63');
INSERT INTO `%DB_PREFIX%conf` VALUES ('27','SHOP_LOGO','./public/attachment/201011/4cdd501dc023b.png','3','2','','1','1','19');
INSERT INTO `%DB_PREFIX%conf` VALUES ('28','SHOP_LANG','zh-cn','3','1','zh-cn','1','0','18');
INSERT INTO `%DB_PREFIX%conf` VALUES ('29','SHOP_TITLE','方维o2o商业系统','3','0','','1','1','13');
INSERT INTO `%DB_PREFIX%conf` VALUES ('30','SHOP_KEYWORD','方维o2o商业系统关键词','3','0','','1','1','15');
INSERT INTO `%DB_PREFIX%conf` VALUES ('31','SHOP_DESCRIPTION','方维o2o商业系统描述','3','0','','1','1','15');
INSERT INTO `%DB_PREFIX%conf` VALUES ('32','SHOP_TEL','400-800-8888','3','0','','1','1','23');
INSERT INTO `%DB_PREFIX%conf` VALUES ('33','SIDE_DEAL_COUNT','3','3','0','','1','1','29');
INSERT INTO `%DB_PREFIX%conf` VALUES ('34','SIDE_MESSAGE_COUNT','3','3','0','','1','0','30');
INSERT INTO `%DB_PREFIX%conf` VALUES ('35','INVITE_REFERRALS','20','4','0','','1','1','67');
INSERT INTO `%DB_PREFIX%conf` VALUES ('36','INVITE_REFERRALS_TYPE','0','4','1','0,1','1','1','68');
INSERT INTO `%DB_PREFIX%conf` VALUES ('38','ONLINE_QQ','88888888|9999999','3','0','','1','1','25');
INSERT INTO `%DB_PREFIX%conf` VALUES ('39','ONLINE_TIME','周一至周六 9:00-18:00','3','0','','1','1','26');
INSERT INTO `%DB_PREFIX%conf` VALUES ('40','DEAL_PAGE_SIZE','24','3','0','','1','1','31');
INSERT INTO `%DB_PREFIX%conf` VALUES ('41','PAGE_SIZE','24','3','0','','1','1','32');
INSERT INTO `%DB_PREFIX%conf` VALUES ('42','HELP_CATE_LIMIT','4','3','0','','1','1','34');
INSERT INTO `%DB_PREFIX%conf` VALUES ('43','HELP_ITEM_LIMIT','4','3','0','','1','1','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('44','SHOP_FOOTER','<div style=\"text-align:center;\">[方维o2o商业系统] <a target=\"_blank\" href=\"http://www.fanwe.com\">http://www.fanwe.com</a><br />\n</div>\n','3','3','','1','1','37');
INSERT INTO `%DB_PREFIX%conf` VALUES ('45','USER_MESSAGE_AUTO_EFFECT','1','4','1','0,1','1','0','64');
INSERT INTO `%DB_PREFIX%conf` VALUES ('48','MAIL_SEND_COUPON','0','5','1','0,1','1','1','73');
INSERT INTO `%DB_PREFIX%conf` VALUES ('49','SMS_SEND_COUPON','0','5','1','0,1','1','1','79');
INSERT INTO `%DB_PREFIX%conf` VALUES ('50','MAIL_SEND_PAYMENT','0','5','1','0,1','1','1','75');
INSERT INTO `%DB_PREFIX%conf` VALUES ('51','SMS_SEND_PAYMENT','0','5','1','0,1','1','1','81');
INSERT INTO `%DB_PREFIX%conf` VALUES ('62','REPLY_ADDRESS','info@fanwe.com','5','0','','1','1','77');
INSERT INTO `%DB_PREFIX%conf` VALUES ('54','MAIL_SEND_DELIVERY','0','5','1','0,1','1','1','76');
INSERT INTO `%DB_PREFIX%conf` VALUES ('55','SMS_SEND_DELIVERY','0','5','1','0,1','1','1','82');
INSERT INTO `%DB_PREFIX%conf` VALUES ('56','MAIL_ON','1','5','1','0,1','1','1','72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('57','SMS_ON','1','5','1','0,1','1','1','78');
INSERT INTO `%DB_PREFIX%conf` VALUES ('58','REFERRAL_LIMIT','1','4','0','','1','1','69');
INSERT INTO `%DB_PREFIX%conf` VALUES ('59','SMS_COUPON_LIMIT','3','5','0','','1','1','80');
INSERT INTO `%DB_PREFIX%conf` VALUES ('60','MAIL_COUPON_LIMIT','3','5','0','','1','1','74');
INSERT INTO `%DB_PREFIX%conf` VALUES ('61','COUPON_NAME','方维券','3','0','','1','1','16');
INSERT INTO `%DB_PREFIX%conf` VALUES ('63','BATCH_PAGE_SIZE','500','3','0','','1','0','33');
INSERT INTO `%DB_PREFIX%conf` VALUES ('64','COUPON_PRINT_TPL','<div style=\"border:1px solid #000000;padding:10px;margin:0px auto;width:600px;font-size:14px;\"><table class=\"dataEdit\" cellpadding=\"0\" cellspacing=\"0\">	<tbody><tr>    <td width=\"400\">    	<img src=\"./public/attachment/201011/4cdd505195d40.gif\" alt=\"\" border=\"0\" />     </td>\r\n  <td style=\"font-weight:bolder;font-size:22px;font-family:verdana;\" width=\"43%\">    序列号：{$bond.sn}<br />\r\n    密码：{$bond.password}    </td>\r\n</tr>\r\n<tr><td colspan=\"2\" height=\"1\">  <div style=\"width:100%;border-bottom:1px solid #000000;\">&nbsp;</div>\r\n  </td>\r\n</tr>\r\n<tr><td colspan=\"2\" height=\"8\"><br />\r\n</td>\r\n</tr>\r\n<tr><td style=\"font-weight:bolder;font-size:28px;height:50px;padding:5px;font-family:微软雅黑;\" colspan=\"2\">{$bond.name}</td>\r\n</tr>\r\n<tr><td style=\"line-height:22px;padding-right:20px;\" width=\"400\">{$bond.user_name}<br />\r\n  生效时间:{$bond.begin_time_format}<br />\r\n  过期时间:{$bond.end_time_format}<br />\r\n  商家电话：<br />\r\n  {$bond.tel}<br />\r\n  商家地址:<br />\r\n  {$bond.address}<br />\r\n  交通路线:<br />\r\n  {$bond.route}<br />\r\n  营业时间：<br />\r\n  {$bond.open_time}<br />\r\n  </td>\r\n  <td><div id=\"map_canvas\" style=\"width:255px;height:255px;\"></div>\r\n  <br />\r\n  </td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n','3','3','','1','0','40');
INSERT INTO `%DB_PREFIX%conf` VALUES ('65','PUBLIC_DOMAIN_ROOT','','2','0','','1','0','59');
INSERT INTO `%DB_PREFIX%conf` VALUES ('66','SHOW_DEAL_CATE','1','3','1','0,1','1','0','41');
INSERT INTO `%DB_PREFIX%conf` VALUES ('67','REFERRAL_IP_LIMIT','0','4','1','0,1','1','1','71');
INSERT INTO `%DB_PREFIX%conf` VALUES ('69','CART_ON','1','3','1','0,1','1','1','42');
INSERT INTO `%DB_PREFIX%conf` VALUES ('70','REFERRALS_DELAY','1','4','0','','1','1','70');
INSERT INTO `%DB_PREFIX%conf` VALUES ('71','SUBMIT_DELAY','5','1','0','','1','0','13');
INSERT INTO `%DB_PREFIX%conf` VALUES ('72','APP_MSG_SENDER_OPEN','1','1','1','0,1','1','1','9');
INSERT INTO `%DB_PREFIX%conf` VALUES ('73','ADMIN_MSG_SENDER_OPEN','1','1','1','0,1','1','1','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('74','SHOP_OPEN','1','3','1','0,1','1','1','46');
INSERT INTO `%DB_PREFIX%conf` VALUES ('75','SHOP_CLOSE_HTML','','3','3','','1','1','47');
INSERT INTO `%DB_PREFIX%conf` VALUES ('76','FOOTER_LOGO','./public/attachment/201011/4cdd50ed013ec.png','3','2','','1','1','20');
INSERT INTO `%DB_PREFIX%conf` VALUES ('77','GZIP_ON','0','1','1','0,1','1','1','2');
INSERT INTO `%DB_PREFIX%conf` VALUES ('78','INTEGRATE_CODE','','0','0','','1','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('79','INTEGRATE_CFG','','0','0','','1','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('80','SHOP_SEO_TITLE','方维o2o商业系统,国内最优秀的PHP开源o2o系统','3','0','','1','1','14');
INSERT INTO `%DB_PREFIX%conf` VALUES ('81','CACHE_ON','1','1','1','0,1','1','0','7');
INSERT INTO `%DB_PREFIX%conf` VALUES ('82','EXPIRED_TIME','0','1','0','','1','0','5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('120','FILTER_WORD','','1','0','','1','1','100');
INSERT INTO `%DB_PREFIX%conf` VALUES ('84','STYLE_OPEN','0','3','1','0,1','0','0','44');
INSERT INTO `%DB_PREFIX%conf` VALUES ('85','STYLE_DEFAULT','1','3','1','0,1','0','0','45');
INSERT INTO `%DB_PREFIX%conf` VALUES ('86','TMPL_DOMAIN_ROOT','','2','0','0','0','0','62');
INSERT INTO `%DB_PREFIX%conf` VALUES ('94','ICP_LICENSE','','3','0','','1','1','27');
INSERT INTO `%DB_PREFIX%conf` VALUES ('95','COUNT_CODE','','3','0','','1','1','28');
INSERT INTO `%DB_PREFIX%conf` VALUES ('96','DEAL_MSG_LOCK','0','0','0','','0','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('97','PROMOTE_MSG_LOCK','0','0','0','','0','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('98','LIST_TYPE','1','3','1','0,1','1','0','45');
INSERT INTO `%DB_PREFIX%conf` VALUES ('100','SUPPLIER_DETAIL','1','3','1','0,1','1','0','45');
INSERT INTO `%DB_PREFIX%conf` VALUES ('101','KUAIDI_APP_KEY','','1','0','','1','1','83');
INSERT INTO `%DB_PREFIX%conf` VALUES ('102','KUAIDI_TYPE','2','1','1','1,2,3','1','1','84');
INSERT INTO `%DB_PREFIX%conf` VALUES ('103','SEND_SPAN','2','1','0','','1','1','85');
INSERT INTO `%DB_PREFIX%conf` VALUES ('104','MAIL_USE_COUPON','0','5','1','0,1','1','1','77');
INSERT INTO `%DB_PREFIX%conf` VALUES ('105','SMS_USE_COUPON','0','5','1','0,1','1','1','83');
INSERT INTO `%DB_PREFIX%conf` VALUES ('106','LOTTERY_SMS_VERIFY','0','5','1','0,1','1','1','84');
INSERT INTO `%DB_PREFIX%conf` VALUES ('107','LOTTERY_SN_SMS','0','5','1','0,1','1','1','85');
INSERT INTO `%DB_PREFIX%conf` VALUES ('108','EDM_ON','0','5','1','0,1','1','0','86');
INSERT INTO `%DB_PREFIX%conf` VALUES ('109','EDM_USERNAME','','5','0','','1','0','87');
INSERT INTO `%DB_PREFIX%conf` VALUES ('110','EDM_PASSWORD','','5','4','','1','0','88');
INSERT INTO `%DB_PREFIX%conf` VALUES ('111','SHOP_SEARCH_KEYWORD','','3','0','','1','1','15');
INSERT INTO `%DB_PREFIX%conf` VALUES ('112','REC_HOT_LIMIT','4','3','0','','1','0','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('113','REC_NEW_LIMIT','4','3','0','','1','0','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('180','BAIDU_MAP_APPKEY','','1','0','','1','1','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('115','REC_CATE_GOODS_LIMIT','4','3','0','','1','0','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('116','SALE_LIST','5','3','0','','1','0','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('117','INDEX_NOTICE_COUNT','8','3','0','','1','1','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('118','RELATE_GOODS_LIMIT','5','3','0','','1','0','35');
INSERT INTO `%DB_PREFIX%conf` VALUES ('119','TMPL_CACHE_ON','1','1','1','0,1','1','0','6');
INSERT INTO `%DB_PREFIX%conf` VALUES ('121','USER_LOGIN_SCORE','0','6','0','','1','1','2');
INSERT INTO `%DB_PREFIX%conf` VALUES ('122','USER_LOGIN_MONEY','0','6','0','','1','1','1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('123','USER_REGISTER_SCORE','100','6','0','','1','1','8');
INSERT INTO `%DB_PREFIX%conf` VALUES ('124','USER_REGISTER_MONEY','0','6','0','','1','1','7');
INSERT INTO `%DB_PREFIX%conf` VALUES ('125','DOMAIN_ROOT','','1','0','','1','0','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('128','VERIFY_IMAGE','0','1','1','0,1','1','0','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('129','TUAN_SHOP_TITLE','方维团购','3','0','','1','0','13');
INSERT INTO `%DB_PREFIX%conf` VALUES ('130','MALL_SHOP_TITLE','方维商城','3','0','','1','0','13');
INSERT INTO `%DB_PREFIX%conf` VALUES ('131','APNS_MSG_LOCK','0','0','0','','0','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('132','PROMOTE_MSG_PAGE','0','0','0','','0','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('133','APNS_MSG_PAGE','0','0','0','','0','0','0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('134','STORE_SEND_LIMIT','5','5','0','','1','0','9');
INSERT INTO `%DB_PREFIX%conf` VALUES ('135','USER_LOGIN_POINT','10','6','0','','1','1','3');
INSERT INTO `%DB_PREFIX%conf` VALUES ('136','USER_REGISTER_POINT','100','6','0','','1','1','9');
INSERT INTO `%DB_PREFIX%conf` VALUES ('137','USER_LOGIN_KEEP_MONEY','0','6','0','','1','1','4');
INSERT INTO `%DB_PREFIX%conf` VALUES ('138','USER_LOGIN_KEEP_SCORE','5','6','0','','1','1','5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('139','USER_LOGIN_KEEP_POINT','50','6','0','','1','1','6');
INSERT INTO `%DB_PREFIX%conf` VALUES ('140','USER_ACTIVE_MONEY','0','6','0','','1','1','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('141','USER_ACTIVE_SCORE','0','6','0','','1','1','11');
INSERT INTO `%DB_PREFIX%conf` VALUES ('142','USER_ACTIVE_POINT','10','6','0','','1','1','12');
INSERT INTO `%DB_PREFIX%conf` VALUES ('143','USER_ACTIVE_MONEY_MAX','0','6','0','','1','1','13');
INSERT INTO `%DB_PREFIX%conf` VALUES ('144','USER_ACTIVE_SCORE_MAX','0','6','0','','1','1','14');
INSERT INTO `%DB_PREFIX%conf` VALUES ('145','USER_ACTIVE_POINT_MAX','100','6','0','','1','1','15');
INSERT INTO `%DB_PREFIX%conf` VALUES ('146','USER_DELETE_MONEY','0','6','0','','1','1','16');
INSERT INTO `%DB_PREFIX%conf` VALUES ('148','USER_DELETE_POINT','-10','6','0','','1','1','18');
INSERT INTO `%DB_PREFIX%conf` VALUES ('149','USER_ADD_MONEY','0','6','0','','1','1','19');
INSERT INTO `%DB_PREFIX%conf` VALUES ('150','USER_ADD_SCORE','0','6','0','','1','1','20');
INSERT INTO `%DB_PREFIX%conf` VALUES ('151','USER_ADD_POINT','10','6','0','','1','1','21');
INSERT INTO `%DB_PREFIX%conf` VALUES ('147','USER_DELETE_SCORE','0','6','0','','1','1','17');
INSERT INTO `%DB_PREFIX%conf` VALUES ('152','BIZ_AGREEMENT','<ul>                                	<li>                                    &nbsp;&nbsp;&nbsp;&nbsp;您确认，在开始\"实名认证\"前，您已详细阅读了本协议所有内容，一旦您开始认证流程，即表示您充分理解并同意接受本协议的全部内容。                                    </li>\n                                    <li>                                    &nbsp;&nbsp;&nbsp;&nbsp;为了提高服务的安全性和我们的商户身份的可信度，我们向您提供认证服务。在您申请认证前，您必须先注册成为用户。商户认证成功后，我们将给予每个商户一个认证标识。本公司有权采取各种其认为必要手段对商户的身份进行识别。但是，作为普通的网络服务提供商，本公司所能采取的方法有限，而且在网络上进行商户身份识别也存在一定的困难，因此，本公司对完成认证的商户身份的准确性和绝对真实性不做任何保证。                                    </li>\n                                    <li>                                    &nbsp;&nbsp;&nbsp;&nbsp;本公司有权记录并保存您提供给本公司的信息和本公司获取的结果信息，亦有权根据本协议的约定向您或第三方提供您是否通过认证的结论以及您的身份信息。                                         </li>\n									<li>										<h3>一、关于认证服务的理解与认同</h3>\n										<ol class=\"decimal\">											<li>认证服务是由本公司提供的一项身份识别服务。除非本协议另有约定，一旦您的账户完成了认证，相应的身份信息和认证结果将不因任何原因被修改或取消；如果您的身份信息在完成认证后发生了变更，您应向本公司提供相应有权部门出具的凭证，由本公司协助您变更账户的对应认证信息。</li>\n											<li>本公司有权单方随时修改或变更本协议内容，并通过网站公告变更后的协议文本，无需单独通知您。本协议进行任何修改或变更后，您还继续使用我们的服务和/或认证服务的，即代表您已阅读、了解并同意接受变更后的协议内容；您如果不同意变更后的协议内容，应立即停用我们的服务和认证服务。</li>\n										</ol>\n																</li>\n<li>										<h3>二、实名认证</h3>\n										<ol class=\"decimal\">											<li>个体工商户类商户向本公司申请认证服务时，应向本公司提供以下资料：中华人民共和国工商登记机关颁发的个体工商户营业执照或者其他身份证明文件。</li>\n											<li>企业类商户向本公司申请认证服务时，应向本公司提供以下资料：中华人民共和国工商登记机关颁发的企业营业执照或者其他身份证明文件。</li>\n                                            <li>                                            其他类商户向本公司申请认证服务时，应向本公司提供以下资料：能够证明商户合法身份的证明文件，或者其他本公司认为必要的身份证明文件。                                            </li>\n                                            <li>                                            如商户在认证后变更任何身份信息，则应在变更发生后三个工作日内书面通知本公司变更认证，否则本公司有权随时单方终止提供服务，且因此造成的全部后果，由商户自行承担。                                            </li>\n                                            <li>                                            通过实名认证的商户不能自行修改已经认证的信息，包括但不限于企业名称、姓名以及身份证件号码等。                                            </li>\n										</ol>\n									</li>\n									<li>										<h3>三、特别声明</h3>\n												<ol class=\"decimal\">																						<li>认证信息共享：<br />\n为了使您享有便捷的服务，您经由其它网站向本公司提交认证申请即表示您同意本公司为您核对所提交的全部认证信息，并同意本公司将是否通过认证的结果及相关认证信息提供给该网站。</li>\n											<li>												认证资料的管理：<br />\n     您在认证时提交给本公司的认证资料，即不可撤销的授权由本公司保留。本公司承诺除法定或约定的事由外，不公开或编辑或透露您的认证资料及保存在本公司的非公开内容用于商业目的，但本条第1项规定以及以下情形除外：												<ol class=\"lower-roman\">													<li>您授权本公司透露的相关信息；</li>\n													<li>本公司向国家司法及行政机关提供；</li>\n                                                    <li>本公司向本公司关联企业提供；</li>\n                                                    <li>第三方和本公司一起为商户提供服务时，该第三方向您提供服务所需的相关信息；</li>\n                                                    <li>基于解决您与第三方民事纠纷的需要，本公司有权向该第三方提供您的身份信息。</li>\n												</ol>\n														</li>\n										</ol>\n									</li>\n																<li>										<h3>四、第三方网站的链接</h3>\n                                    </li>\n											<li>&nbsp;&nbsp;&nbsp;&nbsp;为实现认证信息审查，我们网站上可能包含了指向第三方网站的链接（以下简称\"链接网站\"）。\"链接网站\"非由本公司控制，对于任何\"链接网站\"的内容，包含但不限于\"链接网站\"内含的任何链接，或\"链接网站\"的任何改变或更新，本公司均不予负责。自\"链接网站\"接收的网络传播或其它形式之传送，本公司不予负责。</li>\n									<li>										<h3>五、不得为非法或禁止的使用</h3>\n                                    </li>\n                                    <li>&nbsp;&nbsp;&nbsp;&nbsp;接受本协议全部的说明、条款、条件是您申请认证的先决条件。您声明并保证，您不得为任何非法或为本协议、条件及须知所禁止之目的进行认证申请。您不得以任何可能损害、使瘫痪、使过度负荷或损害网站或其他网站的服务、或干扰本公司或他人对于认证申请的使用等方式使用认证服务。您不得经由非本公司许可提供的任何方式取得或试图取得任何资料或信息。									</li>\n									<li>										<h3>六、有关免责</h3>\n                                     </li>\n                                     <li>                                     &nbsp;&nbsp;&nbsp;&nbsp;下列情况时本公司无需承担任何责任：                                     </li>\n                                     <li>											<ol class=\"decimal\">												<li>由于您将账户密码告知他人或未保管好自己的密码或与他人共享账户或任何其他非本公司的过错，导致您的个人资料泄露。</li>\n												<li>													任何由于黑客攻击、计算机病毒侵入或发作、电信部门技术调整导致之影响、因政府管制而造成的暂时性关闭、由于第三方原因(包括不可抗力，例如国际出口的主干线路及国际出口电信提供商一方出现故障、火灾、水灾、雷击、地震、洪水、台风、龙卷风、火山爆发、瘟疫和传染病流行、罢工、战争或暴力行为或类似事件等)及其他非因本公司过错而造成的认证信息泄露、丢失、被盗用或被篡改等。															</li>\n												<li>由于与本公司链接的其它网站所造成的商户身份信息泄露及由此而导致的任何法律争议和后果。</li>\n                                                <li>任何商户向本公司提供错误、不完整、不实信息等造成不能通过认证或遭受任何其他损失，概与本公司无关。</li>\n											</ol>\n									</li>\n																</ul>\n','3','3',' ','1','1','100');
INSERT INTO `%DB_PREFIX%conf` VALUES ('153','INDEX_LEFT_STORE','1','3','0',' ','1','0','1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('154','INDEX_LEFT_TUAN','1','3','0',' ','1','0','2');
INSERT INTO `%DB_PREFIX%conf` VALUES ('155','INDEX_LEFT_YOUHUI','1','3','0',' ','1','0','3');
INSERT INTO `%DB_PREFIX%conf` VALUES ('156','INDEX_LEFT_DAIJIN','1','3','0',' ','1','0','4');
INSERT INTO `%DB_PREFIX%conf` VALUES ('157','INDEX_LEFT_EVENT','1','3','0',' ','1','0','5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('158','INDEX_RIGHT_STORE','1','3','0',' ','1','0','6');
INSERT INTO `%DB_PREFIX%conf` VALUES ('159','INDEX_RIGHT_TUAN','1','3','0',' ','1','0','7');
INSERT INTO `%DB_PREFIX%conf` VALUES ('160','INDEX_RIGHT_YOUHUI','1','3','0',' ','1','0','8');
INSERT INTO `%DB_PREFIX%conf` VALUES ('161','INDEX_RIGHT_DAIJIN','1','3','0',' ','1','0','9');
INSERT INTO `%DB_PREFIX%conf` VALUES ('162','INDEX_RIGHT_EVENT','1','3','0',' ','1','0','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('163','USER_YOUHUI_DOWN_MONEY','0','6','0',' ','1','0','21');
INSERT INTO `%DB_PREFIX%conf` VALUES ('164','USER_YOUHUI_DOWN_SCORE','0','6','0',' ','1','0','21');
INSERT INTO `%DB_PREFIX%conf` VALUES ('165','USER_YOUHUI_DOWN_POINT','0','6','0',' ','1','0','21');
INSERT INTO `%DB_PREFIX%conf` VALUES ('167','APPLE_PATH','ios','3','0',' ','1','0','101');
INSERT INTO `%DB_PREFIX%conf` VALUES ('168','ANDROID_PATH','android','3','0',' ','1','0','102');
INSERT INTO `%DB_PREFIX%conf` VALUES ('171','QRCODE_SIZE','5','3','1','1,3,5','1','1','103');
INSERT INTO `%DB_PREFIX%conf` VALUES ('169','SEND_SCORE_SMS','0','5','1','0,1','1','1','82');
INSERT INTO `%DB_PREFIX%conf` VALUES ('170','SEND_SCORE_MAIL','0','5','1','0,1','1','1','76');
INSERT INTO `%DB_PREFIX%conf` VALUES ('172','YOUHUI_SEND_TEL_LIMIT','2','5','0','','1','0','10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('173','IP_LIMIT_NUM','0','1','0','','1','1','5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('174','INDEX_SUPPLIER_COUNT','8','3','0','','1','1','28');
INSERT INTO `%DB_PREFIX%conf` VALUES ('179','SUPPLIER_ORDER_NOTIFY','1','5','1','0,1','1','1','50');
INSERT INTO `%DB_PREFIX%conf` VALUES ('175','BIZ_APPLE_PATH','','3','0','','1','0','102');
INSERT INTO `%DB_PREFIX%conf` VALUES ('176','BIZ_ANDROID_PATH','','3','0','','1','0','102');
INSERT INTO `%DB_PREFIX%conf` VALUES ('181','BIZ_REGISTER_SMS','0','5','1','0,1','1','1','100');
INSERT INTO `%DB_PREFIX%conf` VALUES ('182','QRCODE_ON','0','3','1','0,1','1','1','41');
INSERT INTO `%DB_PREFIX%conf` VALUES ('183','TENCENT_MAP_APPKEY','','1','0','','1','1','35');
DROP TABLE IF EXISTS `%DB_PREFIX%coupon_log`;
CREATE TABLE `%DB_PREFIX%coupon_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`coupon_sn` varchar(255) NOT NULL COMMENT '消费券序列号',
`create_time` int(11) NOT NULL COMMENT '请求时间',
`msg` text NOT NULL COMMENT '请求信息（如短信为上行的短信内容）',
`query_id` varchar(255) NOT NULL COMMENT '第三方验证通道的请求唯一ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方消费券验证(短信上行验证，电话验证)的第三方请求回调日志';
DROP TABLE IF EXISTS `%DB_PREFIX%daren_submit`;
CREATE TABLE `%DB_PREFIX%daren_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '申请达人的会员ID',
`is_publish` tinyint(1) NOT NULL COMMENT '是否通过标识0:否 1:是',
`create_time` int(11) NOT NULL COMMENT '提交申请时间',
`reason` text NOT NULL COMMENT '用户提交的申请理由',
`daren_title` varchar(255) NOT NULL COMMENT '达人专用名称',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='达人申请表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal`;
CREATE TABLE `%DB_PREFIX%deal` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` text NOT NULL COMMENT '商品名称',
`sub_name` varchar(255) NOT NULL COMMENT '短名称，用于短信，邮件等需要节约字符数的地方显示名称用',
`cate_id` int(11) NOT NULL COMMENT '生活服务分类ID',
`supplier_id` int(11) NOT NULL COMMENT '所属的商户ID',
`img` varchar(255) NOT NULL COMMENT '主图',
`description` text NOT NULL COMMENT '信息描述详情',
`begin_time` int(11) NOT NULL COMMENT '上线开始时间，可为0为不限时',
`end_time` int(11) NOT NULL COMMENT '下架时间，可为0为不限时',
`min_bought` int(11) NOT NULL COMMENT '最小购买量，用于团购产品的成团判断',
`max_bought` int(11) NOT NULL DEFAULT '-1' COMMENT '最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)',
`user_min_bought` int(11) NOT NULL COMMENT '会员下单的最小量',
`user_max_bought` int(11) NOT NULL COMMENT '每个会员购买的上限',
`origin_price` decimal(20,4) NOT NULL COMMENT '原价',
`current_price` decimal(20,4) NOT NULL COMMENT '当前销售价',
`city_id` int(11) NOT NULL COMMENT '所属的城市',
`is_coupon` tinyint(1) NOT NULL COMMENT '是否发放消费券',
`is_delivery` tinyint(1) NOT NULL COMMENT '是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`user_count` int(11) NOT NULL COMMENT '下单量（按单计算,每组商品多件购买算一笔）',
`buy_count` int(11) NOT NULL COMMENT '销量（购买的件数）',
`time_status` tinyint(1) NOT NULL COMMENT '时间状态0:未开始1:进行中2:已过期(不上架销售，可以往消费券中查到)',
`buy_status` tinyint(1) NOT NULL COMMENT '销售状态 0:未成团 1:已成团 2:成团并卖光\r\n0:未成团，购买的用户生成消费券，但不发券\r\n1:成团，购买发券\r\n2:卖光商品不再开放购买，但不下架',
`deal_type` tinyint(1) NOT NULL COMMENT '发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)',
`allow_promote` tinyint(1) NOT NULL COMMENT '是否允许参与促销（系统内安装并配置的促销接口）',
`return_money` decimal(20,4) NOT NULL COMMENT '购买即返现的金额(该项可填负数，也可作为额外消费的金额)',
`return_score` int(11) NOT NULL COMMENT '购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)',
`brief` text NOT NULL COMMENT '商品简介',
`sort` int(11) NOT NULL COMMENT '前台展示排序 由大到小',
`deal_goods_type` int(11) NOT NULL COMMENT '商品类型（用于生成相应类型的属性规格配置项）',
`success_time` int(11) NOT NULL COMMENT '成团时间',
`coupon_time_type` tinyint(1) NOT NULL COMMENT '0：指定时间过期 1:按下单日起xx天过期',
`coupon_day` int(11) NOT NULL COMMENT '下单后xx天内失效',
`coupon_begin_time` int(11) NOT NULL COMMENT '发放消费券的生效时间',
`coupon_end_time` int(11) NOT NULL COMMENT '发放的消费券的过期时间',
`code` varchar(255) NOT NULL COMMENT '标识码,可自定义一个标识用于消费券的前缀（用于电话验证的商品只能填数字）',
`weight` decimal(20,4) NOT NULL COMMENT '商品重量，实体商品填写，用于运费计算',
`weight_id` int(11) NOT NULL COMMENT '重量单位的配置ID',
`is_referral` tinyint(1) NOT NULL COMMENT '是否允许购买返利给邀请人',
`buy_type` tinyint(1) NOT NULL COMMENT '团购商品的类型0：普通 2:订购 3秒杀 (该值仅作为前台展示以及归类用，功能上与团购商品相同) ',
`discount` decimal(20,4) NOT NULL COMMENT '商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比',
`icon` varchar(255) NOT NULL COMMENT '小图',
`notice` tinyint(1) NOT NULL COMMENT '是否参与预告（未到上线期的商品，默认不展示在前台，该项为1表示可以上线展示预告）',
`free_delivery` tinyint(1) NOT NULL COMMENT '是否开启免运费，可以单独配置针对某个配送方式的免运费规则',
`define_payment` tinyint(1) NOT NULL COMMENT '是否自定义禁用哪些支付方式',
`seo_title` text NOT NULL COMMENT '自定义的页面seo标题',
`seo_keyword` text NOT NULL COMMENT '自定义的页面seo关键词',
`seo_description` text NOT NULL COMMENT '自定义的页面seo描述',
`is_hot` tinyint(1) NOT NULL COMMENT '商城商品的热卖标识',
`is_new` tinyint(1) NOT NULL COMMENT '商城商品的新品标识',
`is_best` tinyint(1) NOT NULL COMMENT '商城商品的精品标识',
`is_lottery` tinyint(1) NOT NULL COMMENT '是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则',
`reopen` int(11) NOT NULL COMMENT '重开团的申请，往期团购前台可以申请重新开团，该项用于计数',
`uname` varchar(255) NOT NULL COMMENT 'url别名，用于重写与seo收录的优化',
`forbid_sms` tinyint(1) NOT NULL COMMENT '是否禁用短信发送功能，禁用短信则该商品的购物不会短信发券',
`cart_type` tinyint(1) NOT NULL COMMENT '购物车规则\r\n0:启用购物车(每次可以买多款)\r\n1按商品(同款商品可买多款属性)\r\n2按商家(同个商家可买多款商品)\r\n3禁用购物车(每次只能买一款)',
`shop_cate_id` int(11) NOT NULL COMMENT '商城商品的分类ID',
`is_shop` tinyint(1) NOT NULL COMMENT '标识是否为商城商品 0:否 1:是',
`total_point` int(11) NOT NULL COMMENT '用户评分的总分',
`avg_point` float(14,4) NOT NULL COMMENT '用户评分的平均分',
`create_time` int(11) NOT NULL COMMENT '管理员发布时间',
`update_time` int(11) NOT NULL COMMENT '管理员更新时间',
`name_match` text NOT NULL COMMENT '名称的全文索引unicode编码',
`name_match_row` text NOT NULL COMMENT '名称的全文索引查询栏',
`deal_cate_match` text NOT NULL COMMENT '分类的全文索引unicode',
`deal_cate_match_row` text NOT NULL COMMENT '分类的全文索引查询栏',
`shop_cate_match` text NOT NULL COMMENT '商品分类的全文索引unicode',
`shop_cate_match_row` text NOT NULL COMMENT '商品分类的全文索引查询栏',
`locate_match` text NOT NULL COMMENT '地区信息的全文索引unicode',
`locate_match_row` text NOT NULL COMMENT '地区信息的全文索引查询栏',
`tag_match` text NOT NULL COMMENT '标签全文索引unicode',
`tag_match_row` text NOT NULL COMMENT '标签全文索引查询栏',
`xpoint` varchar(255) NOT NULL COMMENT '经度（第一个分店的经度）',
`ypoint` varchar(255) NOT NULL COMMENT '纬度（第一个分店的纬度）',
`brand_id` int(11) NOT NULL COMMENT '所归属的品牌',
`brand_promote` tinyint(1) NOT NULL COMMENT '是否参与品牌促销，该项与brand表的该项同步',
`publish_wait` tinyint(1) NOT NULL COMMENT '商家提交的产品 0:已审核 1:等待审核',
`account_id` int(11) NOT NULL COMMENT '商家提交的商家帐号ID',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐到首页展示',
`balance_price` decimal(20,4) NOT NULL COMMENT '与商家的结算价（即商价提供给平台商的成本价）',
`is_refund` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可退款',
`auto_order` tinyint(1) NOT NULL COMMENT '是否打上免预约标识 0:否 1:是',
`expire_refund` tinyint(1) NOT NULL COMMENT '是否支持过期退款( 过期未消费用户即可提交退款)',
`any_refund` tinyint(1) NOT NULL COMMENT '是否支持随时退款（未消费用户即可提交退款申请）',
`multi_attr` tinyint(1) NOT NULL COMMENT '多套餐（自动判断是否有属性规格配置，有则打上该标签）',
`deal_tag` int(10) NOT NULL COMMENT '商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减',
`dp_count` int(11) NOT NULL COMMENT '总参与的点评人数',
`notes` text NOT NULL COMMENT '购买需知',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`buyin_app` tinyint(1) NOT NULL COMMENT '是否仅展示在app端0否 1是',
`is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提',
`set_meal`  text NOT NULL COMMENT '移动端套餐模板',
`pc_setmeal`  text NOT NULL COMMENT 'PC端套餐模板',
PRIMARY KEY (`id`),
KEY `cate_id` (`cate_id`),
KEY `supplier_id` (`supplier_id`),
KEY `begin_time` (`begin_time`),
KEY `end_time` (`end_time`),
KEY `current_price` (`current_price`),
KEY `city_id` (`city_id`),
KEY `buy_count` (`buy_count`),
KEY `sort` (`sort`),
KEY `buy_type` (`buy_type`),
KEY `shop_cate_id` (`shop_cate_id`),
KEY `is_shop` (`is_shop`),
KEY `create_time` (`create_time`),
KEY `update_time` (`update_time`),
KEY `buyin_app` (`buyin_app`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `tag_match` (`tag_match`),
FULLTEXT KEY `deal_cate_match` (`deal_cate_match`),
FULLTEXT KEY `all_match` (`name_match`,`deal_cate_match`,`locate_match`,`tag_match`,`shop_cate_match`),
FULLTEXT KEY `shop_cate_match` (`shop_cate_match`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COMMENT='产生支付行为的商品、团购、代金券数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_attr`;
CREATE TABLE `%DB_PREFIX%deal_attr` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '商品属性名称',
`goods_type_attr_id` int(11) NOT NULL COMMENT '商品类型ID',
`price` decimal(20,4) NOT NULL COMMENT '属性增加的额外价格',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`is_checked` tinyint(1) NOT NULL COMMENT '是否配置过该属性的库存',
`add_balance_price` decimal(20,4) NOT NULL,
PRIMARY KEY (`id`),
KEY `goods_type_attr_id` (`goods_type_attr_id`),
KEY `deal_id` (`deal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=251 DEFAULT CHARSET=utf8 COMMENT='商品属性配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cart`;
CREATE TABLE `%DB_PREFIX%deal_cart` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`session_id` varchar(255) NOT NULL COMMENT '当前用户的sessionID',
`user_id` int(11) NOT NULL COMMENT '用户ID',
`deal_id` int(11) NOT NULL COMMENT '产品ID',
`name` text NOT NULL COMMENT '购买的产品显示名称(包含购买的规格)',
`attr` varchar(255) NOT NULL COMMENT '购买的相关属性的ID，用半角逗号分隔',
`unit_price` decimal(20,4) NOT NULL COMMENT '单价',
`number` int(11) NOT NULL COMMENT '数量',
`total_price` decimal(20,4) NOT NULL COMMENT '总价',
`verify_code` varchar(255) NOT NULL COMMENT '验证唯一的标识码（由商品ID与属性ID组合加密生成）',
`create_time` int(11) NOT NULL COMMENT '加入购物车的时间',
`update_time` int(11) NOT NULL COMMENT '更新的时间',
`return_money` decimal(20,4) NOT NULL COMMENT '返现金的单价',
`return_total_money` decimal(20,4) NOT NULL COMMENT '返现金的总价',
`return_score` int(11) NOT NULL COMMENT '返积分的单价',
`return_total_score` int(11) NOT NULL COMMENT '返积分的总价',
`buy_type` tinyint(1) NOT NULL COMMENT '团购产品的类型（同deal表中的该字段）',
`sub_name` varchar(255) NOT NULL COMMENT '简短名称',
`supplier_id` int(11) NOT NULL COMMENT '产品所属的商家ID',
`attr_str` text NOT NULL COMMENT '属性组合的显示名称',
`add_balance_price` decimal(20,4) NOT NULL,
`is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提',
PRIMARY KEY (`id`),
KEY `session_id` (`session_id`),
KEY `user_id` (`user_id`),
KEY `deal_id` (`deal_id`),
KEY `update_time` (`update_time`)
) ENGINE=MyISAM AUTO_INCREMENT=457 DEFAULT CHARSET=utf8 COMMENT='购物车表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate`;
CREATE TABLE `%DB_PREFIX%deal_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分类名称',
`brief` text NOT NULL COMMENT '分类简介',
`description`  varchar(255) NOT NULL COMMENT '分类描述' ,
`pid` int(11) NOT NULL COMMENT '父ID，已弃用',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`is_new`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '1为new' ,
`sort` int(11) NOT NULL COMMENT '排序 由大到小',
`uname` varchar(255) NOT NULL COMMENT 'url 别名',
`recommend` tinyint(1) NOT NULL COMMENT '推荐到首页',
`icon` varchar(255) DEFAULT '' COMMENT '弃用',
`rec_youhui` tinyint(1) NOT NULL COMMENT '推荐到优惠券首页',
`rec_daijin` tinyint(1) NOT NULL COMMENT '推荐到代金券首页',
`iconfont` varchar(15) NOT NULL COMMENT '图标',
`iconcolor` varchar(15) NOT NULL COMMENT '图标，分类的颜色',
`icon_img` varchar(255) NOT NULL COMMENT '手机端的分类小图',
PRIMARY KEY (`id`),
KEY `pid` (`pid`),
KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='生活服务分类表';
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('8', '餐饮美食', '', '', '0', '0', '1', '0', '1', '', '1', '', '1', '1', '&#58896;', '#a1410d', '');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('9', '亲子游玩', '', '宝贝去哪儿', '0', '0', '1', '0', '2', '', '0', '', '1', '1', '', '#8fc63d', './public/attachment/201602/22/11/56ca8148abf0d.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('10', '婚纱摄影', '', '冬日碧池美食', '0', '0', '1', '0', '3', '', '1', '', '0', '1', '', '#f7941d', './public/attachment/201602/22/11/56ca80ffe5bed.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('11', '温泉', '', '养生好去处', '0', '0', '1', '0', '4', '', '0', '', '0', '1', '', '#00aeef', './public/attachment/201602/22/11/56ca80e00fd7d.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('13', '汽车服务', '', '洗车美容保养', '0', '0', '1', '0', '6', '', '0', '', '0', '1', '', '#004a80', './public/attachment/201602/22/11/56ca80c18b0a4.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('14', '麦霸开唱', '', '嗨歌无极限', '0', '0', '1', '0', '7', '', '0', '', '0', '1', '', '#a763a9', './public/attachment/201602/22/11/56ca80a1f3d10.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('15', '上门洗衣', '', '洗衣9元起', '0', '0', '1', '0', '8', '', '0', '', '0', '1', '', '#9d0a0f', './public/attachment/201602/22/11/56ca808aad159.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('16', '午茶时分', '', '每日满减专区', '0', '0', '1', '0', '9', '', '0', '', '0', '1', '', '#3e6617', './public/attachment/201602/22/11/56ca807411d6d.jpg');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('17', '糖心苹果', '', '精彩不容错过', '0', '0', '1', '0', '10', '', '0', '', '0', '1', '', '#f16522', './public/attachment/201602/22/11/56ca8052b1216.jpg');
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate_type`;
CREATE TABLE `%DB_PREFIX%deal_cate_type` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '小分类名称',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐标识，推荐到代金券，优惠券首页的相应大分类栏目中',
`sort` int(11) NOT NULL COMMENT '排序（由大到小）',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='生活服务分类子类';
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('1','咖啡','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('2','闽菜','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('3','东北菜','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('4','川菜','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('5','KTV','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('6','自助游','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('7','周边游','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('8','国内游','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('9','海外游','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('10','洗车','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('11','汽车保养','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('12','驾校','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('13','4S店','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('14','音响','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('15','车载导航','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('16','真皮座椅','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('17','打蜡','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('18','男科','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('19','妇科','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('20','儿科','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('21','口腔科','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('22','眼科','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('23','体检中心','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('24','心理诊所','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('25','疗养院','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('26','日本料理','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('27','本帮菜','1','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('28','甜点','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('29','面包','0','0');
INSERT INTO `%DB_PREFIX%deal_cate_type` VALUES ('30','烧烤','1','0');
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate_type_deal_link`;
CREATE TABLE `%DB_PREFIX%deal_cate_type_deal_link` (
`deal_id` int(11) NOT NULL,
`deal_cate_type_id` int(11) NOT NULL,
PRIMARY KEY (`deal_id`,`deal_cate_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品与生活服务子类的N-N关联表';
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('37','2');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('37','3');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('38','2');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('39','2');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('39','4');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('49','1');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('50','4');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('51','4');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('53','4');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('55','28');
INSERT INTO `%DB_PREFIX%deal_cate_type_deal_link` VALUES ('55','29');
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate_type_link`;
CREATE TABLE `%DB_PREFIX%deal_cate_type_link` (
`cate_id` int(11) NOT NULL,
`deal_cate_type_id` int(11) NOT NULL,
PRIMARY KEY (`cate_id`,`deal_cate_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='生活服务大分类与小分类的关联表';
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','1');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','2');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','3');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','4');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','26');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','27');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','28');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','29');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('8','30');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('9','1');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('9','5');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('9','6');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('10','5');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('11','6');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('11','7');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('11','8');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('11','9');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','10');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','11');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','12');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','13');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','14');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','15');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','16');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('13','17');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','18');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','19');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','20');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','21');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','22');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','23');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','24');
INSERT INTO `%DB_PREFIX%deal_cate_type_link` VALUES ('16','25');
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate_type_location_link`;
CREATE TABLE `%DB_PREFIX%deal_cate_type_location_link` (
`location_id` int(11) NOT NULL,
`deal_cate_type_id` int(11) NOT NULL,
PRIMARY KEY (`location_id`,`deal_cate_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户门店分属哪些生活服务子分类的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate_type_youhui_link`;
CREATE TABLE `%DB_PREFIX%deal_cate_type_youhui_link` (
`deal_cate_type_id` int(11) NOT NULL,
`youhui_id` int(11) NOT NULL,
PRIMARY KEY (`deal_cate_type_id`,`youhui_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券与生活服务子类的关联表';


DROP TABLE IF EXISTS `%DB_PREFIX%deal_city`;
CREATE TABLE `%DB_PREFIX%deal_city` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '城市中文名',
`uname` varchar(255) NOT NULL COMMENT '英文名（用于URL显示）与二级域名显示',
`is_effect` tinyint(1) NOT NULL,
`is_delete` tinyint(1) NOT NULL,
`pid` int(11) NOT NULL COMMENT '父ID，只能指向全国',
`is_open` tinyint(1) NOT NULL COMMENT '弃用',
`is_default` tinyint(1) NOT NULL COMMENT '默认城市（当IP定位不到时默认显示的城市）',
`description` text NOT NULL COMMENT '弃用',
`notice` text NOT NULL COMMENT '弃用',
`seo_title` text NOT NULL COMMENT '针对城市定义的城市子站的seo标题前缀',
`seo_keyword` text NOT NULL COMMENT '针对城市定义的城市子站的seo关键词前缀',
`seo_description` text NOT NULL COMMENT '针对城市定义的城市子站的seo描述前缀',
`sort` int(11) NOT NULL COMMENT '显示的排序',
`is_hot` tinyint(1) NOT NULL COMMENT '热门城市',
`code` int(11) NOT NULL COMMENT '行政区划代码',
`citycode` varchar(255) NOT NULL COMMENT '城市电话区号',
PRIMARY KEY (`id`),
KEY `pid` (`pid`),
KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='城市表';

INSERT INTO `%DB_PREFIX%deal_city` VALUES ('1', '福建', 'fujianp', '1', '0', '0', '1', '0', '', '', '', '', '', '0', '0', '350000', '');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('18', '北京', 'beijing', '1', '0', '16', '1', '0', '', '', '', '', '', '4', '0', '110000', '');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('19', '上海', 'shanghai', '1', '0', '17', '1', '0', '', '', '', '', '', '5', '0', '310000', '');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('20', '厦门', 'xiamen', '1', '0', '1', '1', '0', '', '', '', '', '', '6', '0', '350200', '');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('15', '福州', 'fuzhou', '1', '0', '1', '1', '1', '', '', '', '', '', '1', '0', '350100', '0591');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('16', '北京', 'beijingp', '1', '0', '0', '1', '0', '', '', '', '', '', '2', '0', '110000', '');
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('17', '上海', 'shanghaip', '1', '0', '0', '1', '0', '', '', '', '', '', '3', '0', '310000', '');

DROP TABLE IF EXISTS `%DB_PREFIX%deal_collect`;
CREATE TABLE `%DB_PREFIX%deal_collect` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`deal_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`create_time` int(11) NOT NULL,
PRIMARY KEY (`id`),
KEY `deal_id` (`deal_id`),
KEY `user_id` (`user_id`),
KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='商品收藏表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_coupon`;
CREATE TABLE `%DB_PREFIX%deal_coupon` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`sn` varchar(255) NOT NULL COMMENT ' 消费券序列号',
`password` varchar(255) NOT NULL COMMENT ' 消费券密码',
`begin_time` int(11) NOT NULL COMMENT ' 消费券生效时间',
`end_time` int(11) NOT NULL COMMENT ' 消费券过期时间',
`is_valid` tinyint(1) NOT NULL COMMENT ' 有效性 0:生成未发放给用户(已下单未成团) 1:已发放给用户 2：退款被禁用',
`user_id` int(11) NOT NULL COMMENT ' 会员ID',
`deal_id` int(11) NOT NULL COMMENT ' 商品ID',
`order_id` int(11) NOT NULL COMMENT ' 订单ID ',
`order_deal_id` int(11) NOT NULL COMMENT ' 订单商品ID',
`is_new` tinyint(1) NOT NULL COMMENT ' 新券标识 0:未被会员查看 1：已查看',
`supplier_id` int(11) NOT NULL COMMENT ' 商户ID',
`confirm_account` int(11) NOT NULL COMMENT ' 验证消费券的商家帐号ID',
`location_id` int(11) NOT NULL COMMENT '消费的门店',
`is_delete` tinyint(1) NOT NULL COMMENT ' 删除标识',
`confirm_time` int(11) NOT NULL COMMENT ' 验证消费的时间',
`mail_count` int(11) NOT NULL COMMENT ' 会员手动重发消费券邮件的次数，用于限制恶意重发',
`sms_count` int(11) NOT NULL COMMENT ' 会员手动重发消费券短信的次数，用于限制恶意重发',
`is_balance` tinyint(1) NOT NULL COMMENT ' 0:未结算 1:待结算 2:已结算',
`balance_memo` text NOT NULL COMMENT ' 管理员结算的备注',
`balance_price` decimal(20,4) NOT NULL COMMENT ' 生成消费券时由商品表中同步生成该值：结算单价',
`balance_time` int(11) NOT NULL COMMENT ' 结算时间',
`refund_status` tinyint(1) NOT NULL COMMENT ' 退款状态 0:无 1:用户申请退款 2:已确认 3:拒绝退款',
`expire_refund` tinyint(1) NOT NULL COMMENT ' 是否支持过期退 0:否 1:是',
`any_refund` tinyint(1) NOT NULL COMMENT ' 是否支持随时退 0:否 1:是',
`coupon_price` decimal(20,4) NOT NULL COMMENT ' 消费券的价格，用于退款时的计算，按件为单件价，按单为总价',
`coupon_score` int(11) NOT NULL COMMENT ' 消费券所产生给用户的积分，用于退款时的计算，按件为单件价，按单为总价',
`deal_type` tinyint(1) NOT NULL COMMENT ' 消费券的生成方式 0:按件生成 1:按单生成',
`coupon_money` decimal(20,4) NOT NULL COMMENT ' 消费券所产生给用户的金额（如购买返现之类的），用于退款时的计算，按件为单件价，按单为总价',
`add_balance_price` decimal(20,4) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unk_sn` (`sn`) USING BTREE,
UNIQUE KEY `unk_pw` (`password`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT=' 消费券表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_delivery`;
CREATE TABLE `%DB_PREFIX%deal_delivery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`delivery_id` int(11) NOT NULL COMMENT '被禁用的配送方式ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品的禁用配送方式的配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_dp_point_result`;
CREATE TABLE `%DB_PREFIX%deal_dp_point_result` (
`group_id` int(11) NOT NULL COMMENT '分组ID',
`point` int(11) NOT NULL COMMENT '分数',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
KEY `group_id` (`group_id`) USING BTREE,
KEY `deal_id` (`deal_id`) USING BTREE,
KEY `dp_id` (`dp_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个商品，每条点评针对每个评分分组的点评评分';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_dp_tag_result`;
CREATE TABLE `%DB_PREFIX%deal_dp_tag_result` (
`tags` varchar(255) NOT NULL COMMENT '标签列表 空格分隔',
`dp_id` int(11) NOT NULL COMMENT '关联的点评ID',
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
KEY `dp_id` (`dp_id`),
KEY `group_id` (`group_id`),
KEY `deal_id` (`deal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品按预定义的分组打标签的结果表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_filter`;
CREATE TABLE `%DB_PREFIX%deal_filter` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`filter` text NOT NULL COMMENT '关键词列表，用半角逗号分隔',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`filter_group_id` int(11) NOT NULL COMMENT '筛选分组ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COMMENT='每个商城商品针对每个筛选分组设置关键词的配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_gallery`;
CREATE TABLE `%DB_PREFIX%deal_gallery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`img` varchar(255) NOT NULL,
`deal_id` int(11) NOT NULL,
`sort` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=674 DEFAULT CHARSET=utf8 COMMENT='商品图集表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_location_link`;
CREATE TABLE `%DB_PREFIX%deal_location_link` (
`deal_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY (`deal_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品支持门店的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_msg_list`;
CREATE TABLE `%DB_PREFIX%deal_msg_list` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`dest` varchar(255) NOT NULL COMMENT '发送目标（邮件/手机号）',
`send_type` tinyint(1) NOT NULL COMMENT '发送类型 0:短信 1:邮件;2:微信;3:andoird;4:ios',
`content` text NOT NULL COMMENT '发送的内容',
`send_time` int(11) NOT NULL COMMENT '发出的时间',
`is_send` tinyint(1) NOT NULL COMMENT '是否已发送 0:否 1:等待队列发送',
`create_time` int(11) NOT NULL COMMENT '生成的时间',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`result` text NOT NULL COMMENT '发送结果（如出错存放服务器或接口返回的错误信息）',
`is_success` tinyint(1) NOT NULL COMMENT '是否发送成功',
`is_html` tinyint(1) NOT NULL COMMENT '只针对邮件使用，是否为超文本邮件 0:否 1:是',
`title` text NOT NULL COMMENT '只针对邮件使用 邮件的标题',
`is_youhui` tinyint(1) NOT NULL COMMENT '是否为优惠券的下载发送',
`youhui_id` int(11) NOT NULL COMMENT '关联的优惠券ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=283 DEFAULT CHARSET=utf8 COMMENT='业务队列表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order`;
CREATE TABLE `%DB_PREFIX%deal_order` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`type` tinyint(1) NOT NULL COMMENT '订单类型(0:商品订单 1:用户充值单)',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`update_time` int(11) NOT NULL COMMENT '更新时间',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 1:部份付款(先用余额/代金券支付部份) 2:全部付款',
`total_price` decimal(20,4) NOT NULL COMMENT '应付总额',
`pay_amount` decimal(20,4) NOT NULL COMMENT '已付总额 当pay_amount = total_price 支付成功',
`delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:部份发货 2:全部发货 5:无需发货的订单',
`order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`return_total_score` int(11) NOT NULL COMMENT '返给用户的总积分',
`refund_amount` decimal(20,4) NOT NULL COMMENT '已退款总额',
`admin_memo` text NOT NULL COMMENT '管理员的备注',
`memo` text NOT NULL COMMENT '用户下单的备注',
`region_lv1` int(11) NOT NULL COMMENT '配送地址一级地区ID',
`region_lv2` int(11) NOT NULL COMMENT '配送地址二级地区ID',
`region_lv3` int(11) NOT NULL COMMENT '配送地址三级地区ID',
`region_lv4` int(11) NOT NULL COMMENT '配送地址四级地区ID',
`address` text NOT NULL COMMENT '配送地址',
`mobile` varchar(255) NOT NULL COMMENT '联系人手机',
`zip` varchar(255) NOT NULL COMMENT '联系人邮编',
`consignee` varchar(255) NOT NULL COMMENT '收货人姓名',
`deal_total_price` decimal(20,4) NOT NULL COMMENT '订单中的商品总价',
`discount_price` decimal(20,4) NOT NULL COMMENT '享受的会员折扣价',
`delivery_fee` decimal(20,4) NOT NULL COMMENT '运费',
`ecv_id` int(11) NOT NULL COMMENT '支付所用的代金券ID',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金券支付部份的金额',
`account_money` decimal(20,4) NOT NULL COMMENT '余额支付部份的金额',
`delivery_id` int(11) NOT NULL COMMENT '配送方式',
`payment_id` int(11) NOT NULL COMMENT '支付方式',
`payment_fee` decimal(20,4) NOT NULL COMMENT '支付方式所耗的手续费',
`return_total_money` decimal(20,4) NOT NULL COMMENT '返现给用户的总额',
`extra_status` tinyint(1) NOT NULL COMMENT '额外的订单标识 0:正常的订单 1.金额超额产生退款的订单（多次支付，重付通知） 2.发货失败退款（下单时库存足够，支付成功后库存不足，自动退款到用户的订单）',
`after_sale` tinyint(1) NOT NULL COMMENT '售后处理标识 0:正常订单 1:退款处理的订单',
`refund_money` decimal(20,4) NOT NULL COMMENT '退款的总额',
`bank_id` varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
`referer` varchar(255) NOT NULL COMMENT '订单的来路 url',
`deal_ids` varchar(255) NOT NULL COMMENT '购买的商品ID，逗号分隔',
`user_name` varchar(255) NOT NULL COMMENT '下单用户名',
`refund_status` tinyint(1) NOT NULL COMMENT '0:不需退款 1:有退款申请 2:已处理',
`retake_status` tinyint(1) NOT NULL COMMENT '弃用',
`promote_description` text NOT NULL COMMENT '订单享受的促销活动描述',
`deal_order_item` text NOT NULL COMMENT '同步的订单商品数据集',
`is_refuse_delivery` tinyint(1) NOT NULL COMMENT '是否有货没有收到',
PRIMARY KEY (`id`),
UNIQUE KEY `unique_sn` (`order_sn`),
KEY `order_sn` (`order_sn`),
KEY `type` (`type`),
KEY `user_id` (`user_id`),
KEY `pay_status` (`pay_status`),
KEY `delivery_status` (`delivery_status`),
KEY `order_status` (`order_status`),
KEY `is_delete` (`is_delete`),
KEY `extra_status` (`extra_status`),
KEY `after_sale` (`after_sale`),
KEY `refund_status` (`refund_status`),
KEY `retake_status` (`retake_status`),
KEY `is_refuse_delivery` (`is_refuse_delivery`),
FULLTEXT KEY `deal_ids` (`deal_ids`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='订单表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_history`;
CREATE TABLE `%DB_PREFIX%deal_order_history` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`type` tinyint(1) NOT NULL COMMENT '订单类型(0:商品订单 1:用户充值单)',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`update_time` int(11) NOT NULL COMMENT '更新时间',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 1:部份付款(先用余额/代金券支付部份) 2:全部付款',
`total_price` decimal(20,4) NOT NULL COMMENT '应付总额',
`pay_amount` decimal(20,4) NOT NULL COMMENT '已付总额 当pay_amount = total_price 支付成功',
`delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:部份发货 2:全部发货 5:无需发货的订单',
`order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`return_total_score` int(11) NOT NULL COMMENT '返给用户的总积分',
`refund_amount` decimal(20,4) NOT NULL COMMENT '已退款总额',
`admin_memo` text NOT NULL COMMENT '管理员的备注',
`memo` text NOT NULL COMMENT '用户下单的备注',
`region_lv1` int(11) NOT NULL COMMENT '配送地址一级地区ID',
`region_lv2` int(11) NOT NULL COMMENT '配送地址二级地区ID',
`region_lv3` int(11) NOT NULL COMMENT '配送地址三级地区ID',
`region_lv4` int(11) NOT NULL COMMENT '配送地址四级地区ID',
`address` text NOT NULL COMMENT '配送地址',
`mobile` varchar(255) NOT NULL COMMENT '联系人手机',
`zip` varchar(255) NOT NULL COMMENT '联系人邮编',
`consignee` varchar(255) NOT NULL COMMENT '收货人姓名',
`deal_total_price` decimal(20,4) NOT NULL COMMENT '订单中的商品总价',
`discount_price` decimal(20,4) NOT NULL COMMENT '享受的会员折扣价',
`delivery_fee` decimal(20,4) NOT NULL COMMENT '运费',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金券支付部份的金额',
`account_money` decimal(20,4) NOT NULL COMMENT '余额支付部份的金额',
`delivery_id` int(11) NOT NULL COMMENT '配送方式',
`payment_id` int(11) NOT NULL COMMENT '支付方式',
`payment_fee` decimal(20,4) NOT NULL COMMENT '支付方式所耗的手续费',
`return_total_money` decimal(20,4) NOT NULL COMMENT '返现给用户的总额',
`extra_status` tinyint(1) NOT NULL COMMENT '额外的订单标识 0:正常的订单 1.金额超额产生退款的订单（多次支付，重付通知） 2.发货失败退款（下单时库存足够，支付成功后库存不足，自动退款到用户的订单）',
`after_sale` tinyint(1) NOT NULL COMMENT '售后处理标识 0:正常订单 1:退款处理的订单',
`refund_money` decimal(20,4) NOT NULL COMMENT '弃用',
`bank_id` varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
`referer` varchar(255) NOT NULL COMMENT '订单的来路 url',
`deal_ids` varchar(255) NOT NULL COMMENT '购买的商品ID，逗号分隔',
`user_name` varchar(255) NOT NULL COMMENT '下单用户名',
`refund_status` tinyint(1) NOT NULL COMMENT '0:不需退款 1:有退款申请 2:已处理',
`retake_status` tinyint(1) NOT NULL COMMENT '弃用',
`promote_description` text NOT NULL COMMENT '订单享受的促销活动描述',
`history_deal_order_item` text NOT NULL COMMENT '序列化存储的订单产品',
`history_deal_coupon` text NOT NULL COMMENT '序列化存储的消费券',
`history_deal_order_log` text NOT NULL COMMENT '订单日志',
`history_delivery_notice` text NOT NULL COMMENT '发货单日志',
`history_payment_notice` text NOT NULL COMMENT '付款单',
`history_message` text NOT NULL COMMENT '订单留言rel_table:deal_order,rel_id:order_id',
`history_delivery_fee` text NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_sn` (`order_sn`),
FULLTEXT KEY `deal_ids` (`deal_ids`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='历史订单表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_item`;
CREATE TABLE `%DB_PREFIX%deal_order_item` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`number` int(11) NOT NULL COMMENT '购买的数量',
`unit_price` decimal(20,4) NOT NULL COMMENT '单价',
`total_price` decimal(20,4) NOT NULL COMMENT '总价',
`delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:已发货 5.无需发货',
`name` text NOT NULL COMMENT '产品名称',
`return_score` int(11) NOT NULL COMMENT '返积分单价',
`return_total_score` int(11) NOT NULL COMMENT '返积分总价',
`attr` varchar(255) NOT NULL COMMENT '属性ID，逗号分开',
`verify_code` varchar(255) NOT NULL COMMENT '唯一标识码（产品ID+属性ID加密）',
`order_sn` varchar(255) NOT NULL,
`order_id` int(11) NOT NULL COMMENT '所属的订单ID',
`return_money` decimal(20,4) NOT NULL COMMENT '返现的单价',
`return_total_money` decimal(20,4) NOT NULL COMMENT '返现的总价',
`buy_type` tinyint(1) NOT NULL COMMENT '团购产品的类型（同deal表中的该字段）',
`sub_name` varchar(255) NOT NULL COMMENT '短名称',
`attr_str` text NOT NULL COMMENT '属性配置的字符串',
`is_balance` tinyint(1) NOT NULL COMMENT '0:未结算 1:待结算 2:已结算 3:部份结算',
`balance_unit_price` decimal(20,4) NOT NULL COMMENT '结算单价',
`balance_memo` text NOT NULL COMMENT '管理员结算备注',
`balance_total_price` decimal(20,4) NOT NULL COMMENT '结算总价',
`balance_time` int(11) NOT NULL COMMENT '结算时间',
`add_balance_price` decimal(20,4) NOT NULL,
`add_balance_price_total` decimal(20,4) NOT NULL,
`refund_status` tinyint(1) NOT NULL COMMENT '退款状态 0:无 1:用户申请退款 2:已确认 3:拒绝退款',
`dp_id` int(11) NOT NULL COMMENT '为该商品点评的ID',
`is_arrival` tinyint(1) NOT NULL COMMENT '是否已收货0:未收货1:已收货2:没收到货',
`is_coupon` tinyint(1) NOT NULL COMMENT '是否发券',
`deal_icon` varchar(255) NOT NULL COMMENT '商品图',
`location_id` int(11) NOT NULL COMMENT '发货时的门店ID',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`is_refund` tinyint(1) NOT NULL COMMENT '是否支持退款(由商品表同步而来)',
`user_id` int(11) NOT NULL COMMENT '所属的用户ID',
`is_shop` tinyint(1) NOT NULL,
`consume_count` int(11) NOT NULL COMMENT '成功消费的商品数量(已验证/已收货/已付款)',
`is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提',
PRIMARY KEY (`id`),
KEY `deal_id` (`deal_id`),
KEY `order_id` (`order_id`),
KEY `verify_code` (`verify_code`),
KEY `refund_status` (`refund_status`),
KEY `buy_type` (`buy_type`),
KEY `is_coupon` (`is_coupon`),
KEY `location_id` (`location_id`),
KEY `supplier_id` (`supplier_id`),
KEY `delivery_status` (`delivery_status`),
KEY `order_sn` (`order_sn`),
KEY `user_id` (`user_id`),
KEY `is_shop` (`is_shop`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='订单产品表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_log`;
CREATE TABLE `%DB_PREFIX%deal_order_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL,
`log_time` int(11) NOT NULL,
`order_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='订单操作的日志表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_supplier_fee`;
CREATE TABLE `%DB_PREFIX%deal_order_supplier_fee` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_id` int(11) NOT NULL COMMENT '订单ID',
`supplier_id` int(11) NOT NULL COMMENT '商户ID',
`delivery_fee` decimal(20,4) NOT NULL COMMENT '运费',
`is_arrival` tinyint(1) NOT NULL COMMENT '是否已收货 0未收货 1已收货（收货后将运费结算给商家）',
PRIMARY KEY (`id`),
KEY `order_id` (`order_id`),
KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `%DB_PREFIX%deal_payment`;
CREATE TABLE `%DB_PREFIX%deal_payment` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`deal_id` int(11) NOT NULL,
`payment_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='针对商品配置的禁用某个支付方式的配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%deal_submit`;
CREATE TABLE `%DB_PREFIX%deal_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` text NOT NULL COMMENT '商品名称',
`sub_name` varchar(255) NOT NULL COMMENT '短名称，用于短信，邮件等需要节约字符数的地方显示名称用',
`cate_id` int(11) NOT NULL COMMENT '生活服务分类ID',
`supplier_id` int(11) NOT NULL COMMENT '所属的商户ID',
`img` varchar(255) NOT NULL COMMENT '主图',
`description` text NOT NULL COMMENT '信息描述详情',
`begin_time` int(11) NOT NULL COMMENT '上线开始时间，可为0为不限时',
`end_time` int(11) NOT NULL COMMENT '下架时间，可为0为不限时',
`min_bought` int(11) NOT NULL COMMENT '最小购买量，用于团购产品的成团判断',
`max_bought` int(11) NOT NULL COMMENT '最大量，即库存上限(如有属性规格的库存，该值不生效，见attr_stock表)',
`user_min_bought` int(11) NOT NULL COMMENT '会员下单的最小量',
`user_max_bought` int(11) NOT NULL COMMENT '每个会员购买的上限',
`origin_price` decimal(20,4) NOT NULL COMMENT '原价',
`current_price` decimal(20,4) NOT NULL COMMENT '当前销售价',
`city_id` int(11) NOT NULL COMMENT '所属的城市',
`is_coupon` tinyint(1) NOT NULL COMMENT '是否发放消费券',
`is_delivery` tinyint(1) NOT NULL COMMENT '是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`user_count` int(11) NOT NULL COMMENT '下单量（按单计算,每组商品多件购买算一笔）',
`buy_count` int(11) NOT NULL COMMENT '销量（购买的件数）',
`time_status` tinyint(1) NOT NULL COMMENT '时间状态0:未开始1:进行中2:已过期(不上架销售，可以往消费券中查到)',
`buy_status` tinyint(1) NOT NULL COMMENT '销售状态 0:未成团 1:已成团 2:成团并卖光\r\n0:未成团，购买的用户生成消费券，但不发券\r\n1:成团，购买发券\r\n2:卖光商品不再开放购买，但不下架',
`deal_type` tinyint(1) NOT NULL COMMENT '发券方式 0:按件发送 1:按单发券(同类商品买多件只发放一张消费券,用于一次性验证)',
`allow_promote` tinyint(1) NOT NULL COMMENT '是否允许参与促销（系统内安装并配置的促销接口）',
`return_money` decimal(20,4) NOT NULL COMMENT '购买即返现的金额(该项可填负数，也可作为额外消费的金额)',
`return_score` int(11) NOT NULL COMMENT '购买返积分(也可以为负数，表示商品购买的积分限制，积分商品的积分也为该项，因此必需为负数)',
`brief` text NOT NULL COMMENT '商品简介',
`sort` int(11) NOT NULL COMMENT '前台展示排序 由大到小',
`deal_goods_type` int(11) NOT NULL COMMENT '商品类型（用于生成相应类型的属性规格配置项）',
`success_time` int(11) NOT NULL COMMENT '成团时间',
`coupon_begin_time` int(11) NOT NULL COMMENT '发放消费券的生效时间',
`coupon_end_time` int(11) NOT NULL COMMENT '发放的消费券的过期时间',
`code` varchar(255) NOT NULL COMMENT '标识码,可自定义一个标识用于消费券的前缀（用于电话验证的商品只能填数字）',
`weight` decimal(20,4) NOT NULL COMMENT '商品重量，实体商品填写，用于运费计算',
`weight_id` int(11) NOT NULL COMMENT '重量单位的配置ID',
`is_referral` tinyint(1) NOT NULL COMMENT '是否允许购买返利给邀请人',
`buy_type` tinyint(1) NOT NULL COMMENT '团购商品的类型0：普通 2:订购 3秒杀 (该值仅作为前台展示以及归类用，功能上与团购商品相同) ',
`discount` decimal(20,4) NOT NULL COMMENT '商品的现价与原价的折扣数，通常会自动生成，在线订购类商品因为付的是订金，该项手动计算原价与卖价的折扣比',
`icon` varchar(255) NOT NULL COMMENT '小图',
`notice` tinyint(1) NOT NULL COMMENT '是否参与预告（未到上线期的商品，默认不展示在前台，该项为1表示可以上线展示预告）',
`free_delivery` tinyint(1) NOT NULL COMMENT '是否开启免运费，可以单独配置针对某个配送方式的免运费规则',
`define_payment` tinyint(1) NOT NULL COMMENT '是否自定义禁用哪些支付方式',
`seo_title` text NOT NULL COMMENT '自定义的页面seo标题',
`seo_keyword` text NOT NULL COMMENT '自定义的页面seo关键词',
`seo_description` text NOT NULL COMMENT '自定义的页面seo描述',
`is_hot` tinyint(1) NOT NULL COMMENT '商城商品的热卖标识',
`is_new` tinyint(1) NOT NULL COMMENT '商城商品的新品标识',
`is_best` tinyint(1) NOT NULL COMMENT '商城商品的精品标识',
`is_lottery` tinyint(1) NOT NULL COMMENT '是否参与抽奖，为1则生成抽奖号，用于运营中制定相应的抽奖规则',
`reopen` int(11) NOT NULL COMMENT '重开团的申请，往期团购前台可以申请重新开团，该项用于计数',
`uname` varchar(255) NOT NULL COMMENT 'url别名，用于重写与seo收录的优化',
`forbid_sms` tinyint(1) NOT NULL COMMENT '是否禁用短信发送功能，禁用短信则该商品的购物不会短信发券',
`cart_type` tinyint(1) NOT NULL COMMENT '购物车规则\r\n0:启用购物车(每次可以买多款)\r\n1按商品(同款商品可买多款属性)\r\n2按商家(同个商家可买多款商品)\r\n3禁用购物车(每次只能买一款)',
`shop_cate_id` int(11) NOT NULL COMMENT '商城商品的分类ID',
`is_shop` tinyint(1) NOT NULL COMMENT '标识是否为商城商品 0:否 1:是',
`total_point` int(11) NOT NULL COMMENT '用户评分的总分',
`avg_point` float(14,4) NOT NULL COMMENT '用户评分的平均分',
`create_time` int(11) NOT NULL COMMENT '管理员发布时间',
`update_time` int(11) NOT NULL COMMENT '管理员更新时间',
`name_match` text NOT NULL COMMENT '名称的全文索引unicode编码',
`name_match_row` text NOT NULL COMMENT '名称的全文索引查询栏',
`deal_cate_match` text NOT NULL COMMENT '分类的全文索引unicode',
`deal_cate_match_row` text NOT NULL COMMENT '分类的全文索引查询栏',
`shop_cate_match` text NOT NULL COMMENT '商品分类的全文索引unicode',
`shop_cate_match_row` text NOT NULL COMMENT '商品分类的全文索引查询栏',
`locate_match` text NOT NULL COMMENT '地区信息的全文索引unicode',
`locate_match_row` text NOT NULL COMMENT '地区信息的全文索引查询栏',
`tag_match` text NOT NULL COMMENT '标签全文索引unicode',
`tag_match_row` text NOT NULL COMMENT '标签全文索引查询栏',
`xpoint` varchar(255) NOT NULL COMMENT '经度（第一个分店的经度）',
`ypoint` varchar(255) NOT NULL COMMENT '纬度（第一个分店的纬度）',
`brand_id` int(11) NOT NULL COMMENT '所归属的品牌',
`brand_promote` tinyint(1) NOT NULL COMMENT '是否参与品牌促销，该项与brand表的该项同步',
`publish_wait` tinyint(1) NOT NULL COMMENT '商家提交的产品 0:已审核 1:等待审核',
`account_id` int(11) NOT NULL COMMENT '商家提交的商家帐号ID',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐到首页展示',
`balance_price` decimal(20,4) NOT NULL COMMENT '与商家的结算价（即商价提供给平台商的成本价）',
`is_refund` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可退款',
`auto_order` tinyint(1) NOT NULL COMMENT '是否打上免预约标识 0:否 1:是',
`expire_refund` tinyint(1) NOT NULL COMMENT '是否支持过期退款( 过期未消费用户即可提交退款)',
`any_refund` tinyint(1) NOT NULL COMMENT '是否支持随时退款（未消费用户即可提交退款申请）',
`multi_attr` tinyint(1) NOT NULL COMMENT '多套餐（自动判断是否有属性规格配置，有则打上该标签）',
`deal_tag` int(10) NOT NULL COMMENT '商品标签\r\n2^(1-10)\r\n1.0元抽奖\r\n2.免预约\r\n3.多套餐\r\n4.可订座\r\n5.代金券\r\n6.过期退\r\n7.随时退\r\n8.七天退\r\n9.免运费\r\n10.满立减',
`dp_count` int(11) NOT NULL COMMENT '总参与的点评人数',
`notes` text NOT NULL COMMENT '购买需知',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`deal_id` int(11) NOT NULL COMMENT '商品表关联ID',
`biz_apply_status` tinyint(1) NOT NULL COMMENT '商户申请状态 1.新品上架申请 2:修改 3:下架',
`admin_check_status` tinyint(1) NOT NULL COMMENT '管理员审核状态 0:待审核 1:通过 2:拒绝',
`cache_deal_cate_type_id` varchar(255) NOT NULL COMMENT '团购商品:子分类ID缓存',
`cache_location_id` varchar(255) NOT NULL COMMENT '支持门店ID缓存',
`cache_focus_imgs` text NOT NULL COMMENT '图集缓存',
`cache_deal_attr` text NOT NULL COMMENT '属性缓存',
`cache_stock_data` text NOT NULL COMMENT '属性库存缓存',
`cache_attr_stock` text NOT NULL COMMENT '对应attr_stock表内容',
`cache_free_delivery` text NOT NULL COMMENT '商城数据:免运费配置缓存',
`cache_deal_payment` text NOT NULL COMMENT '商城数据:禁用的支付方式配置缓存',
`cache_deal_delivery` text NOT NULL COMMENT '商城数据:禁用配送方式配置缓存',
`cache_deal_filter` text NOT NULL COMMENT '商城数据:筛选关键词配置缓存',
`cache_relate` text NOT NULL COMMENT '关联商品缓存',
`is_pick`  tinyint(1) NOT NULL COMMENT '是否允许上门自提',
PRIMARY KEY (`id`),
KEY `cate_id` (`cate_id`),
KEY `supplier_id` (`supplier_id`),
KEY `begin_time` (`begin_time`),
KEY `end_time` (`end_time`),
KEY `current_price` (`current_price`),
KEY `city_id` (`city_id`),
KEY `buy_count` (`buy_count`),
KEY `sort` (`sort`),
KEY `buy_type` (`buy_type`),
KEY `shop_cate_id` (`shop_cate_id`),
KEY `is_shop` (`is_shop`),
KEY `create_time` (`create_time`),
KEY `update_time` (`update_time`),
KEY `deal_id` (`deal_id`),
KEY `account_id` (`account_id`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `tag_match` (`tag_match`),
FULLTEXT KEY `deal_cate_match` (`deal_cate_match`),
FULLTEXT KEY `all_match` (`name_match`,`deal_cate_match`,`locate_match`,`tag_match`,`shop_cate_match`),
FULLTEXT KEY `shop_cate_match` (`shop_cate_match`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='商户中心 商品、团购申请临时表';
DROP TABLE IF EXISTS `%DB_PREFIX%delivery`;
CREATE TABLE `%DB_PREFIX%delivery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '配送方式名称',
`description` text NOT NULL COMMENT '配送方式描述',
`first_fee` decimal(20,4) NOT NULL COMMENT '默认的首重价格',
`allow_default` tinyint(1) NOT NULL COMMENT '启用默认项\r\n0: 当配送地址没有匹配的运费配置时 不支持该配送方式\r\n1： 当配送地址没有匹配的运费配置时 启用默认的运费配置',
`sort` int(11) NOT NULL COMMENT '展示排序 由大到小',
`first_weight` decimal(20,4) NOT NULL COMMENT '默认的首重重量 ',
`continue_weight` decimal(20,4) NOT NULL COMMENT '默认的续重重量',
`continue_fee` decimal(20,4) NOT NULL COMMENT '默认的续重价格',
`weight_id` int(11) NOT NULL COMMENT '重量单位ID',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='配送方式配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%delivery_fee`;
CREATE TABLE `%DB_PREFIX%delivery_fee` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`delivery_id` int(11) NOT NULL COMMENT '配送方式ID',
`region_ids` text NOT NULL COMMENT '支持的配送地区ID集合，逗号分开',
`first_fee` decimal(20,4) NOT NULL COMMENT '首重价格',
`first_weight` decimal(20,4) NOT NULL COMMENT '首重重量',
`continue_fee` decimal(20,4) NOT NULL COMMENT '续重价格',
`continue_weight` decimal(20,4) NOT NULL COMMENT '续重重量',
`supplier_id` int(11) NOT NULL COMMENT '归属于商户的运费配置项',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='配送方式的支持地区运费配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%delivery_notice`;
CREATE TABLE `%DB_PREFIX%delivery_notice` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`notice_sn` varchar(255) NOT NULL COMMENT '快递单号',
`delivery_time` int(11) NOT NULL COMMENT '发货时间',
`is_arrival` tinyint(1) NOT NULL COMMENT '是否已收货0:未收货1:已收货2:没收到货',
`arrival_time` int(11) NOT NULL COMMENT '收货时间',
`order_item_id` int(11) NOT NULL COMMENT '发货的订单商品ID',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`memo` text NOT NULL COMMENT '发货说明备注',
`express_id` int(11) NOT NULL COMMENT '快递接口ID（用于查询快递与打印快递单）',
`delivery_supplier_id` int(11) NOT NULL COMMENT '发货的商家账号ID',
`location_id` int(11) NOT NULL COMMENT '发货的门店点',
`deal_id` int(11) NOT NULL COMMENT '发货的对应商品ID',
`order_id` int(11) NOT NULL COMMENT '订单ID',
PRIMARY KEY (`id`),
KEY `notice_sn` (`notice_sn`),
KEY `order_item_id` (`order_item_id`),
KEY `is_arrival` (`is_arrival`),
KEY `user_id` (`user_id`),
KEY `delivery_supplier_id` (`delivery_supplier_id`),
KEY `location_id` (`location_id`),
KEY `deal_id` (`deal_id`),
KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='发货单表';

DROP TABLE IF EXISTS `%DB_PREFIX%delivery_region`;
CREATE TABLE `%DB_PREFIX%delivery_region` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`pid` int(11) NOT NULL COMMENT '父级地区ID',
`name` varchar(50) NOT NULL COMMENT '地区名称',
`region_level` tinyint(4) NOT NULL COMMENT '1:国 2:省 3:市(县) 4:区(镇)',
`code` int(11) NOT NULL COMMENT '行政区划代码',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='配送地区表';

INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2', '1', '北京', '2', '110000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3', '1', '安徽', '2', '340000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('4', '1', '福建', '2', '350000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('5', '1', '甘肃', '2', '620000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('6', '1', '广东', '2', '440000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('7', '1', '广西', '2', '450000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('8', '1', '贵州', '2', '520000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('9', '1', '海南', '2', '460000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('10', '1', '河北', '2', '130000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('11', '1', '河南', '2', '410000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('12', '1', '黑龙江', '2', '230000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('13', '1', '湖北', '2', '420000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('14', '1', '湖南', '2', '430000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('15', '1', '吉林', '2', '220000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('16', '1', '江苏', '2', '320000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('17', '1', '江西', '2', '360000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('18', '1', '辽宁', '2', '210000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('19', '1', '内蒙古', '2', '150000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('20', '1', '宁夏', '1', '640000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('21', '1', '青海', '2', '630000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('22', '1', '山东', '2', '370000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('23', '1', '山西', '2', '140000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('24', '1', '陕西', '2', '610000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('25', '1', '上海', '2', '310000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('26', '1', '四川', '2', '510000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('27', '1', '天津', '2', '120000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('28', '1', '西藏', '2', '540000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('29', '1', '新疆', '1', '650000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('30', '1', '云南', '2', '530000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('31', '1', '浙江', '1', '330000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('32', '1', '重庆', '2', '500000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('33', '1', '香港', '2', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('34', '1', '澳门', '2', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('35', '1', '台湾', '2', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('36', '3', '安庆', '3', '340800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('37', '3', '蚌埠', '3', '340300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('38', '3', '巢湖', '3', '340181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('39', '3', '池州', '3', '341700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('40', '3', '滁州', '3', '341100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('41', '3', '阜阳', '3', '341200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('42', '3', '淮北', '3', '340600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('43', '3', '淮南', '3', '340400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('44', '3', '黄山', '3', '341000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('45', '3', '六安', '3', '341500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('46', '3', '马鞍山', '3', '340500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('47', '3', '宿州', '3', '341300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('48', '3', '铜陵', '3', '340700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('49', '3', '芜湖', '3', '340200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('50', '3', '宣城', '3', '341800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('51', '3', '亳州', '3', '341600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('52', '2', '北京', '3', '110100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('53', '4', '福州', '3', '350100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('54', '4', '龙岩', '3', '350800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('55', '4', '南平', '3', '350700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('56', '4', '宁德', '3', '350900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('57', '4', '莆田', '3', '350300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('58', '4', '泉州', '3', '350500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('59', '4', '三明', '3', '350400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('60', '4', '厦门', '3', '350200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('61', '4', '漳州', '3', '350600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('62', '5', '兰州', '3', '620100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('63', '5', '白银', '3', '620400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('64', '5', '定西', '3', '621100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('65', '5', '甘南', '3', '623000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('66', '5', '嘉峪关', '3', '620200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('67', '5', '金昌', '3', '620300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('68', '5', '酒泉', '3', '620900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('69', '5', '临夏', '3', '622900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('70', '5', '陇南', '3', '621200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('71', '5', '平凉', '3', '620800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('72', '5', '庆阳', '3', '621000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('73', '5', '天水', '3', '620500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('74', '5', '武威', '3', '620600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('75', '5', '张掖', '3', '620700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('76', '6', '广州', '3', '440100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('77', '6', '深圳', '3', '440300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('78', '6', '潮州', '3', '445100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('79', '6', '东莞', '3', '441900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('80', '6', '佛山', '3', '440600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('81', '6', '河源', '3', '441600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('82', '6', '惠州', '3', '441300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('83', '6', '江门', '3', '440700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('84', '6', '揭阳', '3', '445200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('85', '6', '茂名', '3', '440900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('86', '6', '梅州', '3', '441400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('87', '6', '清远', '3', '441800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('88', '6', '汕头', '3', '440500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('89', '6', '汕尾', '3', '441500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('90', '6', '韶关', '3', '440200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('91', '6', '阳江', '3', '441700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('92', '6', '云浮', '3', '445300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('93', '6', '湛江', '3', '440800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('94', '6', '肇庆', '3', '441200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('95', '6', '中山', '3', '442000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('96', '6', '珠海', '3', '440400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('97', '7', '南宁', '3', '450100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('98', '7', '桂林', '3', '450300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('99', '7', '百色', '3', '451000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('100', '7', '北海', '3', '450500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('101', '7', '崇左', '3', '451400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('102', '7', '防城港', '3', '450600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('103', '7', '贵港', '3', '450800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('104', '7', '河池', '3', '451200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('105', '7', '贺州', '3', '451100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('106', '7', '来宾', '3', '451300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('107', '7', '柳州', '3', '450200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('108', '7', '钦州', '3', '450700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('109', '7', '梧州', '3', '450400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('110', '7', '玉林', '3', '450900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('111', '8', '贵阳', '3', '520100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('112', '8', '安顺', '3', '520400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('113', '8', '毕节', '3', '520500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('114', '8', '六盘水', '3', '520200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('115', '8', '黔东南', '3', '522600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('116', '8', '黔南', '3', '522700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('117', '8', '黔西南', '3', '522300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('118', '8', '铜仁', '3', '520600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('119', '8', '遵义', '3', '520300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('120', '9', '海口', '3', '460100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('121', '9', '三亚', '3', '460200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('122', '9', '白沙', '3', '469025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('123', '9', '保亭', '3', '469029');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('124', '9', '昌江', '3', '469026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('125', '9', '澄迈县', '3', '469023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('126', '9', '定安县', '3', '469021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('127', '9', '东方', '3', '469007');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('128', '9', '乐东', '3', '469027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('129', '9', '临高县', '3', '469024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('130', '9', '陵水', '3', '469028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('131', '9', '琼海', '3', '469002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('132', '9', '琼中', '3', '469030');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('133', '9', '屯昌县', '3', '469022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('134', '9', '万宁', '3', '469006');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('135', '9', '文昌', '3', '469005');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('136', '9', '五指山', '3', '469001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('137', '9', '儋州', '3', '460400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('138', '10', '石家庄', '3', '130100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('139', '10', '保定', '3', '130600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('140', '10', '沧州', '3', '130900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('141', '10', '承德', '3', '130800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('142', '10', '邯郸', '3', '130400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('143', '10', '衡水', '3', '131100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('144', '10', '廊坊', '3', '131000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('145', '10', '秦皇岛', '3', '130300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('146', '10', '唐山', '3', '130200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('147', '10', '邢台', '3', '130500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('148', '10', '张家口', '3', '130700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('149', '11', '郑州', '3', '410100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('150', '11', '洛阳', '3', '410300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('151', '11', '开封', '3', '410200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('152', '11', '安阳', '3', '410500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('153', '11', '鹤壁', '3', '410600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('154', '11', '济源', '3', '419001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('155', '11', '焦作', '3', '410800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('156', '11', '南阳', '3', '411300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('157', '11', '平顶山', '3', '410400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('158', '11', '三门峡', '3', '411200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('159', '11', '商丘', '3', '411400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('160', '11', '新乡', '3', '410700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('161', '11', '信阳', '3', '411500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('162', '11', '许昌', '3', '411000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('163', '11', '周口', '3', '411600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('164', '11', '驻马店', '3', '411700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('165', '11', '漯河', '3', '411100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('166', '11', '濮阳', '3', '410900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('167', '12', '哈尔滨', '3', '230100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('168', '12', '大庆', '3', '230600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('169', '12', '大兴安岭', '3', '232700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('170', '12', '鹤岗', '3', '230400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('171', '12', '黑河', '3', '231100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('172', '12', '鸡西', '3', '230300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('173', '12', '佳木斯', '3', '230800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('174', '12', '牡丹江', '3', '231000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('175', '12', '七台河', '3', '230900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('176', '12', '齐齐哈尔', '3', '230200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('177', '12', '双鸭山', '3', '230500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('178', '12', '绥化', '3', '231200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('179', '12', '伊春', '3', '230700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('180', '13', '武汉', '3', '420100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('181', '13', '仙桃', '3', '429004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('182', '13', '鄂州', '3', '420700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('183', '13', '黄冈', '3', '421100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('184', '13', '黄石', '3', '420200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('185', '13', '荆门', '3', '420800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('186', '13', '荆州', '3', '421000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('187', '13', '潜江', '3', '429005');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('188', '13', '神农架林区', '3', '429021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('189', '13', '十堰', '3', '420300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('190', '13', '随州', '3', '421300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('191', '13', '天门', '3', '429006');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('192', '13', '咸宁', '3', '421200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('193', '13', '襄阳', '3', '420600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('194', '13', '孝感', '3', '420900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('195', '13', '宜昌', '3', '420500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('196', '13', '恩施', '3', '422800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('197', '14', '长沙', '3', '430100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('198', '14', '张家界', '3', '430800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('199', '14', '常德', '3', '430700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('200', '14', '郴州', '3', '431000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('201', '14', '衡阳', '3', '430400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('202', '14', '怀化', '3', '431200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('203', '14', '娄底', '3', '431300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('204', '14', '邵阳', '3', '430500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('205', '14', '湘潭', '3', '430300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('206', '14', '湘西', '3', '433100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('207', '14', '益阳', '3', '430900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('208', '14', '永州', '3', '431100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('209', '14', '岳阳', '3', '430600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('210', '14', '株洲', '3', '430200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('211', '15', '长春', '3', '220100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('212', '15', '吉林', '3', '220200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('213', '15', '白城', '3', '220800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('214', '15', '白山', '3', '220600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('215', '15', '辽源', '3', '220400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('216', '15', '四平', '3', '220300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('217', '15', '松原', '3', '220700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('218', '15', '通化', '3', '220500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('219', '15', '延边', '3', '222400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('220', '16', '南京', '3', '320100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('221', '16', '苏州', '3', '320500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('222', '16', '无锡', '3', '320200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('223', '16', '常州', '3', '320400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('224', '16', '淮安', '3', '320800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('225', '16', '连云港', '3', '320700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('226', '16', '南通', '3', '320600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('227', '16', '宿迁', '3', '321300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('228', '16', '泰州', '3', '321200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('229', '16', '徐州', '3', '320300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('230', '16', '盐城', '3', '320900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('231', '16', '扬州', '3', '321000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('232', '16', '镇江', '3', '321100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('233', '17', '南昌', '3', '360100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('234', '17', '抚州', '3', '361000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('235', '17', '赣州', '3', '360700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('236', '17', '吉安', '3', '360800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('237', '17', '景德镇', '3', '360200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('238', '17', '九江', '3', '360400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('239', '17', '萍乡', '3', '360300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('240', '17', '上饶', '3', '361100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('241', '17', '新余', '3', '360500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('242', '17', '宜春', '3', '360900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('243', '17', '鹰潭', '3', '360600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('244', '18', '沈阳', '3', '210100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('245', '18', '大连', '3', '210200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('246', '18', '鞍山', '3', '210300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('247', '18', '本溪', '3', '210500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('248', '18', '朝阳', '3', '211300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('249', '18', '丹东', '3', '210600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('250', '18', '抚顺', '3', '210400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('251', '18', '阜新', '3', '210900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('252', '18', '葫芦岛', '3', '211400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('253', '18', '锦州', '3', '210700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('254', '18', '辽阳', '3', '211000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('255', '18', '盘锦', '3', '211100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('256', '18', '铁岭', '3', '211200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('257', '18', '营口', '3', '210800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('258', '19', '呼和浩特', '3', '150100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('259', '19', '阿拉善盟', '3', '152900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('260', '19', '巴彦淖尔盟', '3', '150800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('261', '19', '包头', '3', '150200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('262', '19', '赤峰', '3', '150400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('263', '19', '鄂尔多斯', '3', '150600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('264', '19', '呼伦贝尔', '3', '150700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('265', '19', '通辽', '3', '150500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('266', '19', '乌海', '3', '150300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('267', '19', '乌兰察布市', '3', '150900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('268', '19', '锡林郭勒盟', '3', '152500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('269', '19', '兴安盟', '3', '152200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('270', '20', '银川', '3', '640100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('271', '20', '固原', '3', '640400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('272', '20', '石嘴山', '3', '640200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('273', '20', '吴忠', '3', '640300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('274', '20', '中卫', '3', '640500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('275', '21', '西宁', '3', '630100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('276', '21', '果洛', '3', '632600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('277', '21', '海北', '3', '632200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('278', '21', '海东', '3', '630200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('279', '21', '海南', '3', '632500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('280', '21', '海西', '3', '632800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('281', '21', '黄南', '3', '632300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('282', '21', '玉树', '3', '632700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('283', '22', '济南', '3', '370100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('284', '22', '青岛', '3', '370200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('285', '22', '滨州', '3', '371600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('286', '22', '德州', '3', '371400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('287', '22', '东营', '3', '370500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('288', '22', '菏泽', '3', '371700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('289', '22', '济宁', '3', '370800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('290', '22', '莱芜', '3', '371200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('291', '22', '聊城', '3', '371500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('292', '22', '临沂', '3', '371300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('293', '22', '日照', '3', '371100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('294', '22', '泰安', '3', '370900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('295', '22', '威海', '3', '371000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('296', '22', '潍坊', '3', '370700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('297', '22', '烟台', '3', '370600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('298', '22', '枣庄', '3', '370400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('299', '22', '淄博', '3', '370300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('300', '23', '太原', '3', '140100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('301', '23', '长治', '3', '140400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('302', '23', '大同', '3', '140200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('303', '23', '晋城', '3', '140500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('304', '23', '晋中', '3', '140700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('305', '23', '临汾', '3', '141000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('306', '23', '吕梁', '3', '141100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('307', '23', '朔州', '3', '140600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('308', '23', '忻州', '3', '140900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('309', '23', '阳泉', '3', '140300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('310', '23', '运城', '3', '140800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('311', '24', '西安', '3', '610100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('312', '24', '安康', '3', '610900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('313', '24', '宝鸡', '3', '610300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('314', '24', '汉中', '3', '610700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('315', '24', '商洛', '3', '611000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('316', '24', '铜川', '3', '610200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('317', '24', '渭南', '3', '610500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('318', '24', '咸阳', '3', '610400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('319', '24', '延安', '3', '610600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('320', '24', '榆林', '3', '610800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('321', '25', '上海', '3', '310100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('322', '26', '成都', '3', '510100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('323', '26', '绵阳', '3', '510700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('324', '26', '阿坝', '3', '513200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('325', '26', '巴中', '3', '511900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('326', '26', '达州', '3', '511700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('327', '26', '德阳', '3', '510600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('328', '26', '甘孜', '3', '513300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('329', '26', '广安', '3', '511600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('330', '26', '广元', '3', '510800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('331', '26', '乐山', '3', '511100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('332', '26', '凉山', '3', '513400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('333', '26', '眉山', '3', '511400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('334', '26', '南充', '3', '511300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('335', '26', '内江', '3', '511000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('336', '26', '攀枝花', '3', '510400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('337', '26', '遂宁', '3', '510900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('338', '26', '雅安', '3', '511800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('339', '26', '宜宾', '3', '511500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('340', '26', '资阳', '3', '512000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('341', '26', '自贡', '3', '510300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('342', '26', '泸州', '3', '510500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('343', '27', '天津', '3', '120100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('344', '28', '拉萨', '3', '540100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('345', '28', '阿里', '3', '542500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('346', '28', '昌都', '3', '540300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('347', '28', '林芝', '3', '540400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('348', '28', '那曲', '3', '542400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('349', '28', '日喀则', '3', '540200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('350', '28', '山南', '3', '540500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('351', '29', '乌鲁木齐', '3', '650100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('352', '29', '阿克苏', '3', '652900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('353', '29', '阿拉尔', '3', '659002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('354', '29', '巴音郭楞', '3', '652800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('355', '29', '博尔塔拉', '3', '652700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('356', '29', '昌吉', '3', '652300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('357', '29', '哈密', '3', '650500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('358', '29', '和田', '3', '653200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('359', '29', '喀什', '3', '653100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('360', '29', '克拉玛依', '3', '650200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('361', '29', '克孜勒苏', '3', '653000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('362', '29', '石河子', '3', '659001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('363', '29', '图木舒克', '3', '659003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('364', '29', '吐鲁番', '3', '650400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('365', '29', '五家渠', '3', '659004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('366', '29', '伊犁', '3', '654000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('367', '30', '昆明', '3', '530100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('368', '30', '怒江', '3', '533300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('369', '30', '普洱', '3', '530800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('370', '30', '丽江', '3', '530700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('371', '30', '保山', '3', '530500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('372', '30', '楚雄', '3', '532300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('373', '30', '大理', '3', '532900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('374', '30', '德宏', '3', '533100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('375', '30', '迪庆', '3', '533400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('376', '30', '红河', '3', '532500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('377', '30', '临沧', '3', '530900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('378', '30', '曲靖', '3', '530300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('379', '30', '文山', '3', '532600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('380', '30', '西双版纳', '3', '532800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('381', '30', '玉溪', '3', '530400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('382', '30', '昭通', '3', '530600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('383', '31', '杭州', '3', '330100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('384', '31', '湖州', '3', '330500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('385', '31', '嘉兴', '3', '330400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('386', '31', '金华', '3', '330700');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('387', '31', '丽水', '3', '331100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('388', '31', '宁波', '3', '330200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('389', '31', '绍兴', '3', '330600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('390', '31', '台州', '3', '331000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('391', '31', '温州', '3', '330300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('392', '31', '舟山', '3', '330900');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('393', '31', '衢州', '3', '330800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('394', '32', '重庆', '3', '500100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('395', '33', '香港', '3', '810000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('396', '34', '澳门', '3', '820000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('397', '35', '台湾', '3', '710000');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('398', '36', '迎江区', '4', '340802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('399', '36', '大观区', '4', '340803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('400', '36', '宜秀区', '4', '340811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('401', '36', '桐城市', '4', '340881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('402', '36', '怀宁县', '4', '340822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('403', '36', '枞阳县', '4', '340722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('404', '36', '潜山县', '4', '340824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('405', '36', '太湖县', '4', '340825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('406', '36', '宿松县', '4', '340826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('407', '36', '望江县', '4', '340827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('408', '36', '岳西县', '4', '340828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('409', '37', '中市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('410', '37', '东市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('411', '37', '西市区', '4', '210803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('412', '37', '郊区', '4', '140311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('413', '37', '怀远县', '4', '340321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('414', '37', '五河县', '4', '340322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('415', '37', '固镇县', '4', '340323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('416', '38', '居巢区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('417', '38', '庐江县', '4', '340124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('418', '38', '无为县', '4', '340225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('419', '38', '含山县', '4', '340522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('420', '38', '和县', '4', '340523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('421', '39', '贵池区', '4', '341702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('422', '39', '东至县', '4', '341721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('423', '39', '石台县', '4', '341722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('424', '39', '青阳县', '4', '341723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('425', '40', '琅琊区', '4', '341102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('426', '40', '南谯区', '4', '341103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('427', '40', '天长市', '4', '341181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('428', '40', '明光市', '4', '341182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('429', '40', '来安县', '4', '341122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('430', '40', '全椒县', '4', '341124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('431', '40', '定远县', '4', '341125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('432', '40', '凤阳县', '4', '341126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('433', '41', '蚌山区', '4', '340303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('434', '41', '龙子湖区', '4', '340302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('435', '41', '禹会区', '4', '340304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('436', '41', '淮上区', '4', '340311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('437', '41', '颍州区', '4', '341202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('438', '41', '颍东区', '4', '341203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('439', '41', '颍泉区', '4', '341204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('440', '41', '界首市', '4', '341282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('441', '41', '临泉县', '4', '341221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('442', '41', '太和县', '4', '341222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('443', '41', '阜南县', '4', '341225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('444', '41', '颖上县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('445', '42', '相山区', '4', '340603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('446', '42', '杜集区', '4', '340602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('447', '42', '烈山区', '4', '340604');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('448', '42', '濉溪县', '4', '340621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('449', '43', '田家庵区', '4', '340403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('450', '43', '大通区', '4', '340402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('451', '43', '谢家集区', '4', '340404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('452', '43', '八公山区', '4', '340405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('453', '43', '潘集区', '4', '340406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('454', '43', '凤台县', '4', '340421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('455', '44', '屯溪区', '4', '341002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('456', '44', '黄山区', '4', '341003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('457', '44', '徽州区', '4', '341004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('458', '44', '歙县', '4', '341021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('459', '44', '休宁县', '4', '341022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('460', '44', '黟县', '4', '341023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('461', '44', '祁门县', '4', '341024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('462', '45', '金安区', '4', '341502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('463', '45', '裕安区', '4', '341503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('464', '45', '寿县', '4', '340422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('465', '45', '霍邱县', '4', '341522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('466', '45', '舒城县', '4', '341523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('467', '45', '金寨县', '4', '341524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('468', '45', '霍山县', '4', '341525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('469', '46', '雨山区', '4', '340504');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('470', '46', '花山区', '4', '340503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('471', '46', '金家庄区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('472', '46', '当涂县', '4', '340521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('473', '47', '埇桥区', '4', '341302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('474', '47', '砀山县', '4', '341321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('475', '47', '萧县', '4', '341322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('476', '47', '灵璧县', '4', '341323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('477', '47', '泗县', '4', '341324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('478', '48', '铜官山区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('479', '48', '狮子山区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('480', '48', '郊区', '4', '340711');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('481', '48', '铜陵县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('482', '49', '镜湖区', '4', '340202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('483', '49', '弋江区', '4', '340203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('484', '49', '鸠江区', '4', '340207');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('485', '49', '三山区', '4', '340208');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('486', '49', '芜湖县', '4', '340221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('487', '49', '繁昌县', '4', '340222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('488', '49', '南陵县', '4', '340223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('489', '50', '宣州区', '4', '341802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('490', '50', '宁国市', '4', '341881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('491', '50', '郎溪县', '4', '341821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('492', '50', '广德县', '4', '341822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('493', '50', '泾县', '4', '341823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('494', '50', '绩溪县', '4', '341824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('495', '50', '旌德县', '4', '341825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('496', '51', '涡阳县', '4', '341621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('497', '51', '蒙城县', '4', '341622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('498', '51', '利辛县', '4', '341623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('499', '51', '谯城区', '4', '341602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('500', '52', '东城区', '4', '110101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('501', '52', '西城区', '4', '110102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('502', '52', '海淀区', '4', '110108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('503', '52', '朝阳区', '4', '110105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('504', '52', '崇文区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('505', '52', '宣武区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('506', '52', '丰台区', '4', '110106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('507', '52', '石景山区', '4', '110107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('508', '52', '房山区', '4', '110111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('509', '52', '门头沟区', '4', '110109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('510', '52', '通州区', '4', '110112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('511', '52', '顺义区', '4', '110113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('512', '52', '昌平区', '4', '110114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('513', '52', '怀柔区', '4', '110116');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('514', '52', '平谷区', '4', '110117');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('515', '52', '大兴区', '4', '110115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('516', '52', '密云县', '4', '110118');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('517', '52', '延庆县', '4', '110119');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('518', '53', '鼓楼区', '4', '350102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('519', '53', '台江区', '4', '350103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('520', '53', '仓山区', '4', '350104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('521', '53', '马尾区', '4', '350105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('522', '53', '晋安区', '4', '350111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('523', '53', '福清市', '4', '350181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('524', '53', '长乐市', '4', '350182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('525', '53', '闽侯县', '4', '350121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('526', '53', '连江县', '4', '350122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('527', '53', '罗源县', '4', '350123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('528', '53', '闽清县', '4', '350124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('529', '53', '永泰县', '4', '350125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('530', '53', '平潭县', '4', '350128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('531', '54', '新罗区', '4', '350802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('532', '54', '漳平市', '4', '350881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('533', '54', '长汀县', '4', '350821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('534', '54', '永定县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('535', '54', '上杭县', '4', '350823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('536', '54', '武平县', '4', '350824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('537', '54', '连城县', '4', '350825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('538', '55', '延平区', '4', '350702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('539', '55', '邵武市', '4', '350781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('540', '55', '武夷山市', '4', '350782');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('541', '55', '建瓯市', '4', '350783');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('542', '55', '建阳市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('543', '55', '顺昌县', '4', '350721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('544', '55', '浦城县', '4', '350722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('545', '55', '光泽县', '4', '350723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('546', '55', '松溪县', '4', '350724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('547', '55', '政和县', '4', '350725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('548', '56', '蕉城区', '4', '350902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('549', '56', '福安市', '4', '350981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('550', '56', '福鼎市', '4', '350982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('551', '56', '霞浦县', '4', '350921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('552', '56', '古田县', '4', '350922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('553', '56', '屏南县', '4', '350923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('554', '56', '寿宁县', '4', '350924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('555', '56', '周宁县', '4', '350925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('556', '56', '柘荣县', '4', '350926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('557', '57', '城厢区', '4', '350302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('558', '57', '涵江区', '4', '350303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('559', '57', '荔城区', '4', '350304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('560', '57', '秀屿区', '4', '350305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('561', '57', '仙游县', '4', '350322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('562', '58', '鲤城区', '4', '350502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('563', '58', '丰泽区', '4', '350503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('564', '58', '洛江区', '4', '350504');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('565', '58', '清濛开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('566', '58', '泉港区', '4', '350505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('567', '58', '石狮市', '4', '350581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('568', '58', '晋江市', '4', '350582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('569', '58', '南安市', '4', '350583');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('570', '58', '惠安县', '4', '350521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('571', '58', '安溪县', '4', '350524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('572', '58', '永春县', '4', '350525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('573', '58', '德化县', '4', '350526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('574', '58', '金门县', '4', '350527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('575', '59', '梅列区', '4', '350402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('576', '59', '三元区', '4', '350403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('577', '59', '永安市', '4', '350481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('578', '59', '明溪县', '4', '350421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('579', '59', '清流县', '4', '350423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('580', '59', '宁化县', '4', '350424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('581', '59', '大田县', '4', '350425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('582', '59', '尤溪县', '4', '350426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('583', '59', '沙县', '4', '350427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('584', '59', '将乐县', '4', '350428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('585', '59', '泰宁县', '4', '350429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('586', '59', '建宁县', '4', '350430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('587', '60', '思明区', '4', '350203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('588', '60', '海沧区', '4', '350205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('589', '60', '湖里区', '4', '350206');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('590', '60', '集美区', '4', '350211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('591', '60', '同安区', '4', '350212');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('592', '60', '翔安区', '4', '350213');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('593', '61', '芗城区', '4', '350602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('594', '61', '龙文区', '4', '350603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('595', '61', '龙海市', '4', '350681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('596', '61', '云霄县', '4', '350622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('597', '61', '漳浦县', '4', '350623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('598', '61', '诏安县', '4', '350624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('599', '61', '长泰县', '4', '350625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('600', '61', '东山县', '4', '350626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('601', '61', '南靖县', '4', '350627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('602', '61', '平和县', '4', '350628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('603', '61', '华安县', '4', '350629');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('604', '62', '皋兰县', '4', '620122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('605', '62', '城关区', '4', '540102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('606', '62', '七里河区', '4', '620103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('607', '62', '西固区', '4', '620104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('608', '62', '安宁区', '4', '620105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('609', '62', '红古区', '4', '620111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('610', '62', '永登县', '4', '620121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('611', '62', '榆中县', '4', '620123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('612', '63', '白银区', '4', '620402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('613', '63', '平川区', '4', '620403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('614', '63', '会宁县', '4', '620422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('615', '63', '景泰县', '4', '620423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('616', '63', '靖远县', '4', '620421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('617', '64', '临洮县', '4', '621124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('618', '64', '陇西县', '4', '621122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('619', '64', '通渭县', '4', '621121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('620', '64', '渭源县', '4', '621123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('621', '64', '漳县', '4', '621125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('622', '64', '岷县', '4', '621126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('623', '64', '安定区', '4', '621102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('624', '64', '安定区', '4', '621102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('625', '65', '合作市', '4', '623001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('626', '65', '临潭县', '4', '623021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('627', '65', '卓尼县', '4', '623022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('628', '65', '舟曲县', '4', '623023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('629', '65', '迭部县', '4', '623024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('630', '65', '玛曲县', '4', '623025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('631', '65', '碌曲县', '4', '623026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('632', '65', '夏河县', '4', '623027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('633', '66', '嘉峪关市', '4', '620200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('634', '67', '金川区', '4', '620302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('635', '67', '永昌县', '4', '620321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('636', '68', '肃州区', '4', '620902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('637', '68', '玉门市', '4', '620981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('638', '68', '敦煌市', '4', '620982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('639', '68', '金塔县', '4', '620921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('640', '68', '瓜州县', '4', '620922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('641', '68', '肃北', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('642', '68', '阿克塞', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('643', '69', '临夏市', '4', '622901');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('644', '69', '临夏县', '4', '622921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('645', '69', '康乐县', '4', '622922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('646', '69', '永靖县', '4', '622923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('647', '69', '广河县', '4', '622924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('648', '69', '和政县', '4', '622925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('649', '69', '东乡族自治县', '4', '622926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('650', '69', '积石山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('651', '70', '成县', '4', '621221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('652', '70', '徽县', '4', '621227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('653', '70', '康县', '4', '621224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('654', '70', '礼县', '4', '621226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('655', '70', '两当县', '4', '621228');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('656', '70', '文县', '4', '621222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('657', '70', '西和县', '4', '621225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('658', '70', '宕昌县', '4', '621223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('659', '70', '武都区', '4', '621202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('660', '71', '崇信县', '4', '620823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('661', '71', '华亭县', '4', '620824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('662', '71', '静宁县', '4', '620826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('663', '71', '灵台县', '4', '620822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('664', '71', '崆峒区', '4', '620802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('665', '71', '庄浪县', '4', '620825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('666', '71', '泾川县', '4', '620821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('667', '72', '合水县', '4', '621024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('668', '72', '华池县', '4', '621023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('669', '72', '环县', '4', '621022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('670', '72', '宁县', '4', '621026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('671', '72', '庆城县', '4', '621021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('672', '72', '西峰区', '4', '621002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('673', '72', '镇原县', '4', '621027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('674', '72', '正宁县', '4', '621025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('675', '73', '甘谷县', '4', '620523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('676', '73', '秦安县', '4', '620522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('677', '73', '清水县', '4', '620521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('678', '73', '秦州区', '4', '620502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('679', '73', '麦积区', '4', '620503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('680', '73', '武山县', '4', '620524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('681', '73', '张家川', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('682', '74', '古浪县', '4', '620622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('683', '74', '民勤县', '4', '620621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('684', '74', '天祝', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('685', '74', '凉州区', '4', '620602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('686', '75', '高台县', '4', '620724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('687', '75', '临泽县', '4', '620723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('688', '75', '民乐县', '4', '620722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('689', '75', '山丹县', '4', '620725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('690', '75', '肃南', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('691', '75', '甘州区', '4', '620702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('692', '76', '从化市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('693', '76', '天河区', '4', '440106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('694', '76', '东山区', '4', '230406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('695', '76', '白云区', '4', '440111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('696', '76', '海珠区', '4', '440105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('697', '76', '荔湾区', '4', '440103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('698', '76', '越秀区', '4', '440104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('699', '76', '黄埔区', '4', '440112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('700', '76', '番禺区', '4', '440113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('701', '76', '花都区', '4', '440114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('702', '76', '增城区', '4', '440118');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('703', '76', '从化区', '4', '440117');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('704', '76', '市郊', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('705', '77', '福田区', '4', '440304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('706', '77', '罗湖区', '4', '440303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('707', '77', '南山区', '4', '440305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('708', '77', '宝安区', '4', '440306');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('709', '77', '龙岗区', '4', '440307');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('710', '77', '盐田区', '4', '440308');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('711', '78', '湘桥区', '4', '445102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('712', '78', '潮安县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('713', '78', '饶平县', '4', '445122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('714', '79', '南城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('715', '79', '东城区', '4', '110101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('716', '79', '万江区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('717', '79', '莞城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('718', '79', '石龙镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('719', '79', '虎门镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('720', '79', '麻涌镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('721', '79', '道滘镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('722', '79', '石碣镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('723', '79', '沙田镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('724', '79', '望牛墩镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('725', '79', '洪梅镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('726', '79', '茶山镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('727', '79', '寮步镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('728', '79', '大岭山镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('729', '79', '大朗镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('730', '79', '黄江镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('731', '79', '樟木头', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('732', '79', '凤岗镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('733', '79', '塘厦镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('734', '79', '谢岗镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('735', '79', '厚街镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('736', '79', '清溪镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('737', '79', '常平镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('738', '79', '桥头镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('739', '79', '横沥镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('740', '79', '东坑镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('741', '79', '企石镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('742', '79', '石排镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('743', '79', '长安镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('744', '79', '中堂镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('745', '79', '高埗镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('746', '80', '禅城区', '4', '440604');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('747', '80', '南海区', '4', '440605');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('748', '80', '顺德区', '4', '440606');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('749', '80', '三水区', '4', '440607');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('750', '80', '高明区', '4', '440608');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('751', '81', '东源县', '4', '441625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('752', '81', '和平县', '4', '441624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('753', '81', '源城区', '4', '441602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('754', '81', '连平县', '4', '441623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('755', '81', '龙川县', '4', '441622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('756', '81', '紫金县', '4', '441621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('757', '82', '惠阳区', '4', '441303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('758', '82', '惠城区', '4', '441302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('759', '82', '大亚湾', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('760', '82', '博罗县', '4', '441322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('761', '82', '惠东县', '4', '441323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('762', '82', '龙门县', '4', '441324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('763', '83', '江海区', '4', '440704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('764', '83', '蓬江区', '4', '440703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('765', '83', '新会区', '4', '440705');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('766', '83', '台山市', '4', '440781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('767', '83', '开平市', '4', '440783');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('768', '83', '鹤山市', '4', '440784');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('769', '83', '恩平市', '4', '440785');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('770', '84', '榕城区', '4', '445202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('771', '84', '普宁市', '4', '445281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('772', '84', '揭东县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('773', '84', '揭西县', '4', '445222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('774', '84', '惠来县', '4', '445224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('775', '85', '茂南区', '4', '440902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('776', '85', '茂港区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('777', '85', '高州市', '4', '440981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('778', '85', '化州市', '4', '440982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('779', '85', '信宜市', '4', '440983');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('780', '85', '电白县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('781', '86', '梅县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('782', '86', '梅江区', '4', '441402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('783', '86', '兴宁市', '4', '441481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('784', '86', '大埔县', '4', '441422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('785', '86', '丰顺县', '4', '441423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('786', '86', '五华县', '4', '441424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('787', '86', '平远县', '4', '441426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('788', '86', '蕉岭县', '4', '441427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('789', '87', '清城区', '4', '441802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('790', '87', '英德市', '4', '441881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('791', '87', '连州市', '4', '441882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('792', '87', '佛冈县', '4', '441821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('793', '87', '阳山县', '4', '441823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('794', '87', '清新县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('795', '87', '连山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('796', '87', '连南', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('797', '88', '南澳县', '4', '440523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('798', '88', '潮阳区', '4', '440513');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('799', '88', '澄海区', '4', '440515');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('800', '88', '龙湖区', '4', '440507');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('801', '88', '金平区', '4', '440511');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('802', '88', '濠江区', '4', '440512');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('803', '88', '潮南区', '4', '440514');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('804', '89', '城区', '4', '441502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('805', '89', '陆丰市', '4', '441581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('806', '89', '海丰县', '4', '441521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('807', '89', '陆河县', '4', '441523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('808', '90', '曲江县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('809', '90', '浈江区', '4', '440204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('810', '90', '武江区', '4', '440203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('811', '90', '曲江区', '4', '440205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('812', '90', '乐昌市', '4', '440281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('813', '90', '南雄市', '4', '440282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('814', '90', '始兴县', '4', '440222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('815', '90', '仁化县', '4', '440224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('816', '90', '翁源县', '4', '440229');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('817', '90', '新丰县', '4', '440233');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('818', '90', '乳源', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('819', '91', '江城区', '4', '441702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('820', '91', '阳春市', '4', '441781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('821', '91', '阳西县', '4', '441721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('822', '91', '阳东县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('823', '92', '云城区', '4', '445302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('824', '92', '罗定市', '4', '445381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('825', '92', '新兴县', '4', '445321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('826', '92', '郁南县', '4', '445322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('827', '92', '云安县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('828', '93', '赤坎区', '4', '440802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('829', '93', '霞山区', '4', '440803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('830', '93', '坡头区', '4', '440804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('831', '93', '麻章区', '4', '440811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('832', '93', '廉江市', '4', '440881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('833', '93', '雷州市', '4', '440882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('834', '93', '吴川市', '4', '440883');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('835', '93', '遂溪县', '4', '440823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('836', '93', '徐闻县', '4', '440825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('837', '94', '肇庆市', '4', '441200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('838', '94', '高要市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('839', '94', '四会市', '4', '441284');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('840', '94', '广宁县', '4', '441223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('841', '94', '怀集县', '4', '441224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('842', '94', '封开县', '4', '441225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('843', '94', '德庆县', '4', '441226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('844', '95', '石岐街道', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('845', '95', '东区街道', '4', '510402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('846', '95', '西区街道', '4', '510403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('847', '95', '环城街道', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('848', '95', '中山港街道', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('849', '95', '五桂山街道', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('850', '96', '香洲区', '4', '440402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('851', '96', '斗门区', '4', '440403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('852', '96', '金湾区', '4', '440404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('853', '97', '邕宁区', '4', '450109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('854', '97', '青秀区', '4', '450103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('855', '97', '兴宁区', '4', '450102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('856', '97', '良庆区', '4', '450108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('857', '97', '西乡塘区', '4', '450107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('858', '97', '江南区', '4', '450105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('859', '97', '武鸣县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('860', '97', '隆安县', '4', '450123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('861', '97', '马山县', '4', '450124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('862', '97', '上林县', '4', '450125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('863', '97', '宾阳县', '4', '450126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('864', '97', '横县', '4', '450127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('865', '98', '秀峰区', '4', '450302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('866', '98', '叠彩区', '4', '450303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('867', '98', '象山区', '4', '450304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('868', '98', '七星区', '4', '450305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('869', '98', '雁山区', '4', '450311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('870', '98', '阳朔县', '4', '450321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('871', '98', '临桂县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('872', '98', '灵川县', '4', '450323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('873', '98', '全州县', '4', '450324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('874', '98', '平乐县', '4', '450330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('875', '98', '兴安县', '4', '450325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('876', '98', '灌阳县', '4', '450327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('877', '98', '荔浦县', '4', '450331');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('878', '98', '资源县', '4', '450329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('879', '98', '永福县', '4', '450326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('880', '98', '龙胜', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('881', '98', '恭城', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('882', '99', '右江区', '4', '451002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('883', '99', '凌云县', '4', '451027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('884', '99', '平果县', '4', '451023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('885', '99', '西林县', '4', '451030');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('886', '99', '乐业县', '4', '451028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('887', '99', '德保县', '4', '451024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('888', '99', '田林县', '4', '451029');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('889', '99', '田阳县', '4', '451021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('890', '99', '靖西县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('891', '99', '田东县', '4', '451022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('892', '99', '那坡县', '4', '451026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('893', '99', '隆林', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('894', '100', '海城区', '4', '450502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('895', '100', '银海区', '4', '450503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('896', '100', '铁山港区', '4', '450512');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('897', '100', '合浦县', '4', '450521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('898', '101', '江州区', '4', '451402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('899', '101', '凭祥市', '4', '451481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('900', '101', '宁明县', '4', '451422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('901', '101', '扶绥县', '4', '451421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('902', '101', '龙州县', '4', '451423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('903', '101', '大新县', '4', '451424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('904', '101', '天等县', '4', '451425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('905', '102', '港口区', '4', '450602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('906', '102', '防城区', '4', '450603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('907', '102', '东兴市', '4', '450681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('908', '102', '上思县', '4', '450621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('909', '103', '港北区', '4', '450802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('910', '103', '港南区', '4', '450803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('911', '103', '覃塘区', '4', '450804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('912', '103', '桂平市', '4', '450881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('913', '103', '平南县', '4', '450821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('914', '104', '金城江区', '4', '451202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('915', '104', '宜州市', '4', '451281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('916', '104', '天峨县', '4', '451222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('917', '104', '凤山县', '4', '451223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('918', '104', '南丹县', '4', '451221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('919', '104', '东兰县', '4', '451224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('920', '104', '都安', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('921', '104', '罗城', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('922', '104', '巴马', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('923', '104', '环江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('924', '104', '大化', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('925', '105', '八步区', '4', '451102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('926', '105', '钟山县', '4', '451122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('927', '105', '昭平县', '4', '451121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('928', '105', '富川', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('929', '106', '兴宾区', '4', '451302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('930', '106', '合山市', '4', '451381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('931', '106', '象州县', '4', '451322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('932', '106', '武宣县', '4', '451323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('933', '106', '忻城县', '4', '451321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('934', '106', '金秀', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('935', '107', '城中区', '4', '450202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('936', '107', '鱼峰区', '4', '450203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('937', '107', '柳北区', '4', '450205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('938', '107', '柳南区', '4', '450204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('939', '107', '柳江县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('940', '107', '柳城县', '4', '450222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('941', '107', '鹿寨县', '4', '450223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('942', '107', '融安县', '4', '450224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('943', '107', '融水', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('944', '107', '三江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('945', '108', '钦南区', '4', '450702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('946', '108', '钦北区', '4', '450703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('947', '108', '灵山县', '4', '450721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('948', '108', '浦北县', '4', '450722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('949', '109', '万秀区', '4', '450403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('950', '109', '蝶山区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('951', '109', '长洲区', '4', '450405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('952', '109', '岑溪市', '4', '450481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('953', '109', '苍梧县', '4', '450421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('954', '109', '藤县', '4', '450422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('955', '109', '蒙山县', '4', '450423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('956', '110', '玉州区', '4', '450902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('957', '110', '北流市', '4', '450981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('958', '110', '容县', '4', '450921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('959', '110', '陆川县', '4', '450922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('960', '110', '博白县', '4', '450923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('961', '110', '兴业县', '4', '450924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('962', '111', '南明区', '4', '520102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('963', '111', '云岩区', '4', '520103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('964', '111', '花溪区', '4', '520111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('965', '111', '乌当区', '4', '520112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('966', '111', '白云区', '4', '520113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('967', '111', '小河区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('968', '111', '金阳新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('969', '111', '新天园区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('970', '111', '清镇市', '4', '520181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('971', '111', '开阳县', '4', '520121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('972', '111', '修文县', '4', '520123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('973', '111', '息烽县', '4', '520122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('974', '112', '西秀区', '4', '520402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('975', '112', '关岭', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('976', '112', '镇宁', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('977', '112', '紫云', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('978', '112', '平坝县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('979', '112', '普定县', '4', '520422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('980', '113', '毕节市', '4', '520500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('981', '113', '大方县', '4', '520521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('982', '113', '黔西县', '4', '520522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('983', '113', '金沙县', '4', '520523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('984', '113', '织金县', '4', '520524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('985', '113', '纳雍县', '4', '520525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('986', '113', '赫章县', '4', '520527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('987', '113', '威宁', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('988', '114', '钟山区', '4', '520201');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('989', '114', '六枝特区', '4', '520203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('990', '114', '水城县', '4', '520221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('991', '114', '盘县', '4', '520222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('992', '115', '凯里市', '4', '522601');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('993', '115', '黄平县', '4', '522622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('994', '115', '施秉县', '4', '522623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('995', '115', '三穗县', '4', '522624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('996', '115', '镇远县', '4', '522625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('997', '115', '岑巩县', '4', '522626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('998', '115', '天柱县', '4', '522627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('999', '115', '锦屏县', '4', '522628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1000', '115', '剑河县', '4', '522629');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1001', '115', '台江县', '4', '522630');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1002', '115', '黎平县', '4', '522631');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1003', '115', '榕江县', '4', '522632');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1004', '115', '从江县', '4', '522633');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1005', '115', '雷山县', '4', '522634');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1006', '115', '麻江县', '4', '522635');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1007', '115', '丹寨县', '4', '522636');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1008', '116', '都匀市', '4', '522701');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1009', '116', '福泉市', '4', '522702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1010', '116', '荔波县', '4', '522722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1011', '116', '贵定县', '4', '522723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1012', '116', '瓮安县', '4', '522725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1013', '116', '独山县', '4', '522726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1014', '116', '平塘县', '4', '522727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1015', '116', '罗甸县', '4', '522728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1016', '116', '长顺县', '4', '522729');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1017', '116', '龙里县', '4', '522730');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1018', '116', '惠水县', '4', '522731');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1019', '116', '三都', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1020', '117', '兴义市', '4', '522301');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1021', '117', '兴仁县', '4', '522322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1022', '117', '普安县', '4', '522323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1023', '117', '晴隆县', '4', '522324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1024', '117', '贞丰县', '4', '522325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1025', '117', '望谟县', '4', '522326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1026', '117', '册亨县', '4', '522327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1027', '117', '安龙县', '4', '522328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1028', '118', '铜仁市', '4', '520600');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1029', '118', '江口县', '4', '520621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1030', '118', '石阡县', '4', '520623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1031', '118', '思南县', '4', '520624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1032', '118', '德江县', '4', '520626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1033', '118', '玉屏', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1034', '118', '印江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1035', '118', '沿河', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1036', '118', '松桃', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1037', '118', '万山特区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1038', '119', '红花岗区', '4', '520302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1039', '119', '务川县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1040', '119', '道真县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1041', '119', '汇川区', '4', '520303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1042', '119', '赤水市', '4', '520381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1043', '119', '仁怀市', '4', '520382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1044', '119', '遵义县', '4', '210727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1045', '119', '桐梓县', '4', '520322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1046', '119', '绥阳县', '4', '520323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1047', '119', '正安县', '4', '520324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1048', '119', '凤冈县', '4', '520327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1049', '119', '湄潭县', '4', '520328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1050', '119', '余庆县', '4', '520329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1051', '119', '习水县', '4', '520330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1052', '119', '道真', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1053', '119', '务川', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1054', '120', '秀英区', '4', '460105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1055', '120', '龙华区', '4', '460106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1056', '120', '琼山区', '4', '460107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1057', '120', '美兰区', '4', '460108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1058', '137', '市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1059', '137', '洋浦开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1060', '137', '那大镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1061', '137', '王五镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1062', '137', '雅星镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1063', '137', '大成镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1064', '137', '中和镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1065', '137', '峨蔓镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1066', '137', '南丰镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1067', '137', '白马井镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1068', '137', '兰洋镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1069', '137', '和庆镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1070', '137', '海头镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1071', '137', '排浦镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1072', '137', '东成镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1073', '137', '光村镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1074', '137', '木棠镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1075', '137', '新州镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1076', '137', '三都镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1077', '137', '其他', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1078', '138', '长安区', '4', '130102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1079', '138', '桥东区', '4', '130502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1080', '138', '桥西区', '4', '130104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1081', '138', '新华区', '4', '130105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1082', '138', '裕华区', '4', '130108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1083', '138', '井陉矿区', '4', '130107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1084', '138', '高新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1085', '138', '辛集市', '4', '139002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1086', '138', '藁城市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1087', '138', '晋州市', '4', '130183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1088', '138', '新乐市', '4', '130184');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1089', '138', '鹿泉市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1090', '138', '井陉县', '4', '130121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1091', '138', '正定县', '4', '130123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1092', '138', '栾城县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1093', '138', '行唐县', '4', '130125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1094', '138', '灵寿县', '4', '130126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1095', '138', '高邑县', '4', '130127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1096', '138', '深泽县', '4', '130128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1097', '138', '赞皇县', '4', '130129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1098', '138', '无极县', '4', '130130');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1099', '138', '平山县', '4', '130131');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1100', '138', '元氏县', '4', '130132');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1101', '138', '赵县', '4', '130133');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1102', '139', '新市区', '4', '650104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1103', '139', '南市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1104', '139', '北市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1105', '139', '涿州市', '4', '130681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1106', '139', '定州市', '4', '139001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1107', '139', '安国市', '4', '130683');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1108', '139', '高碑店市', '4', '130684');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1109', '139', '满城县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1110', '139', '清苑县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1111', '139', '涞水县', '4', '130623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1112', '139', '阜平县', '4', '130624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1113', '139', '徐水县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1114', '139', '定兴县', '4', '130626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1115', '139', '唐县', '4', '130627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1116', '139', '高阳县', '4', '130628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1117', '139', '容城县', '4', '130629');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1118', '139', '涞源县', '4', '130630');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1119', '139', '望都县', '4', '130631');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1120', '139', '安新县', '4', '130632');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1121', '139', '易县', '4', '130633');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1122', '139', '曲阳县', '4', '130634');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1123', '139', '蠡县', '4', '130635');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1124', '139', '顺平县', '4', '130636');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1125', '139', '博野县', '4', '130637');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1126', '139', '雄县', '4', '130638');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1127', '140', '运河区', '4', '130903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1128', '140', '新华区', '4', '130902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1129', '140', '泊头市', '4', '130981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1130', '140', '任丘市', '4', '130982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1131', '140', '黄骅市', '4', '130983');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1132', '140', '河间市', '4', '130984');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1133', '140', '沧县', '4', '130921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1134', '140', '青县', '4', '130922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1135', '140', '东光县', '4', '130923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1136', '140', '海兴县', '4', '130924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1137', '140', '盐山县', '4', '130925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1138', '140', '肃宁县', '4', '130926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1139', '140', '南皮县', '4', '130927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1140', '140', '吴桥县', '4', '130928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1141', '140', '献县', '4', '130929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1142', '140', '孟村', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1143', '141', '双桥区', '4', '130802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1144', '141', '双滦区', '4', '130803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1145', '141', '鹰手营子矿区', '4', '130804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1146', '141', '承德县', '4', '130821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1147', '141', '兴隆县', '4', '130822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1148', '141', '平泉县', '4', '130823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1149', '141', '滦平县', '4', '130824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1150', '141', '隆化县', '4', '130825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1151', '141', '丰宁', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1152', '141', '宽城', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1153', '141', '围场', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1154', '142', '从台区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1155', '142', '复兴区', '4', '130404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1156', '142', '邯山区', '4', '130402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1157', '142', '峰峰矿区', '4', '130406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1158', '142', '武安市', '4', '130481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1159', '142', '邯郸县', '4', '130421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1160', '142', '临漳县', '4', '130423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1161', '142', '成安县', '4', '130424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1162', '142', '大名县', '4', '130425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1163', '142', '涉县', '4', '130426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1164', '142', '磁县', '4', '130427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1165', '142', '肥乡县', '4', '130428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1166', '142', '永年县', '4', '130429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1167', '142', '邱县', '4', '130430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1168', '142', '鸡泽县', '4', '130431');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1169', '142', '广平县', '4', '130432');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1170', '142', '馆陶县', '4', '130433');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1171', '142', '魏县', '4', '130434');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1172', '142', '曲周县', '4', '130435');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1173', '143', '桃城区', '4', '131102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1174', '143', '冀州市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1175', '143', '深州市', '4', '131182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1176', '143', '枣强县', '4', '131121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1177', '143', '武邑县', '4', '131122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1178', '143', '武强县', '4', '131123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1179', '143', '饶阳县', '4', '131124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1180', '143', '安平县', '4', '131125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1181', '143', '故城县', '4', '131126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1182', '143', '景县', '4', '131127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1183', '143', '阜城县', '4', '131128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1184', '144', '安次区', '4', '131002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1185', '144', '广阳区', '4', '131003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1186', '144', '霸州市', '4', '131081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1187', '144', '三河市', '4', '131082');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1188', '144', '固安县', '4', '131022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1189', '144', '永清县', '4', '131023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1190', '144', '香河县', '4', '131024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1191', '144', '大城县', '4', '131025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1192', '144', '文安县', '4', '131026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1193', '144', '大厂', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1194', '145', '海港区', '4', '130302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1195', '145', '山海关区', '4', '130303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1196', '145', '北戴河区', '4', '130304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1197', '145', '昌黎县', '4', '130322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1198', '145', '抚宁县', '4', '621026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1199', '145', '卢龙县', '4', '130324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1200', '145', '青龙', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1201', '146', '路北区', '4', '130203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1202', '146', '路南区', '4', '130202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1203', '146', '古冶区', '4', '130204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1204', '146', '开平区', '4', '130205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1205', '146', '丰南区', '4', '130207');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1206', '146', '丰润区', '4', '130208');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1207', '146', '遵化市', '4', '130281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1208', '146', '迁安市', '4', '130283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1209', '146', '滦县', '4', '130223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1210', '146', '滦南县', '4', '130224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1211', '146', '乐亭县', '4', '130225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1212', '146', '迁西县', '4', '130227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1213', '146', '玉田县', '4', '130229');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1214', '146', '唐海县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1215', '147', '桥东区', '4', '130502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1216', '147', '桥西区', '4', '130503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1217', '147', '南宫市', '4', '130581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1218', '147', '沙河市', '4', '130582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1219', '147', '邢台县', '4', '130521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1220', '147', '临城县', '4', '130522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1221', '147', '内丘县', '4', '130523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1222', '147', '柏乡县', '4', '130524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1223', '147', '隆尧县', '4', '130525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1224', '147', '任县', '4', '130526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1225', '147', '南和县', '4', '130527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1226', '147', '宁晋县', '4', '130528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1227', '147', '巨鹿县', '4', '130529');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1228', '147', '新河县', '4', '130530');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1229', '147', '广宗县', '4', '130531');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1230', '147', '平乡县', '4', '130532');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1231', '147', '威县', '4', '130533');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1232', '147', '清河县', '4', '130534');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1233', '147', '临西县', '4', '130535');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1234', '148', '桥西区', '4', '130703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1235', '148', '桥东区', '4', '130702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1236', '148', '宣化区', '4', '130705');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1237', '148', '下花园区', '4', '130706');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1238', '148', '宣化县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1239', '148', '张北县', '4', '130722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1240', '148', '康保县', '4', '130723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1241', '148', '沽源县', '4', '130724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1242', '148', '尚义县', '4', '130725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1243', '148', '蔚县', '4', '130726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1244', '148', '阳原县', '4', '130727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1245', '148', '怀安县', '4', '130728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1246', '148', '万全县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1247', '148', '怀来县', '4', '130730');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1248', '148', '涿鹿县', '4', '130731');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1249', '148', '赤城县', '4', '130732');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1250', '148', '崇礼县', '4', '621226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1251', '149', '金水区', '4', '410105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1252', '149', '邙山区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1253', '149', '二七区', '4', '410103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1254', '149', '管城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1255', '149', '中原区', '4', '410102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1256', '149', '上街区', '4', '410106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1257', '149', '惠济区', '4', '410108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1258', '149', '郑东新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1259', '149', '经济技术开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1260', '149', '高新开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1261', '149', '出口加工区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1262', '149', '巩义市', '4', '410181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1263', '149', '荥阳市', '4', '410182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1264', '149', '新密市', '4', '410183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1265', '149', '新郑市', '4', '410184');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1266', '149', '登封市', '4', '410185');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1267', '149', '中牟县', '4', '410122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1268', '150', '西工区', '4', '410303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1269', '150', '老城区', '4', '410302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1270', '150', '涧西区', '4', '410305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1271', '150', '瀍河回族区', '4', '410304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1272', '150', '洛龙区', '4', '410311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1273', '150', '吉利区', '4', '410306');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1274', '150', '偃师市', '4', '410381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1275', '150', '孟津县', '4', '410322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1276', '150', '新安县', '4', '410323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1277', '150', '栾川县', '4', '410324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1278', '150', '嵩县', '4', '410325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1279', '150', '汝阳县', '4', '410326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1280', '150', '宜阳县', '4', '410327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1281', '150', '洛宁县', '4', '410328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1282', '150', '伊川县', '4', '410329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1283', '151', '鼓楼区', '4', '410204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1284', '151', '龙亭区', '4', '410202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1285', '151', '顺河回族区', '4', '410203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1286', '151', '金明区', '4', '410211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1287', '151', '禹王台区', '4', '410205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1288', '151', '杞县', '4', '410221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1289', '151', '通许县', '4', '410222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1290', '151', '尉氏县', '4', '410223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1291', '151', '开封县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1292', '151', '兰考县', '4', '410225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1293', '152', '北关区', '4', '410503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1294', '152', '文峰区', '4', '410502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1295', '152', '殷都区', '4', '410505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1296', '152', '龙安区', '4', '410506');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1297', '152', '林州市', '4', '410581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1298', '152', '安阳县', '4', '410522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1299', '152', '汤阴县', '4', '410523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1300', '152', '滑县', '4', '410526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1301', '152', '内黄县', '4', '410527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1302', '153', '淇滨区', '4', '410611');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1303', '153', '山城区', '4', '410603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1304', '153', '鹤山区', '4', '410602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1305', '153', '浚县', '4', '410621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1306', '153', '淇县', '4', '410622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1307', '154', '济源市', '4', '419001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1308', '155', '解放区', '4', '410802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1309', '155', '中站区', '4', '410803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1310', '155', '马村区', '4', '410804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1311', '155', '山阳区', '4', '410811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1312', '155', '沁阳市', '4', '410882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1313', '155', '孟州市', '4', '410883');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1314', '155', '修武县', '4', '410821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1315', '155', '博爱县', '4', '410822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1316', '155', '武陟县', '4', '410823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1317', '155', '温县', '4', '410825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1318', '156', '卧龙区', '4', '411303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1319', '156', '宛城区', '4', '411302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1320', '156', '邓州市', '4', '411381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1321', '156', '南召县', '4', '411321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1322', '156', '方城县', '4', '411322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1323', '156', '西峡县', '4', '411323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1324', '156', '镇平县', '4', '411324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1325', '156', '内乡县', '4', '411325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1326', '156', '淅川县', '4', '411326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1327', '156', '社旗县', '4', '411327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1328', '156', '唐河县', '4', '411328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1329', '156', '新野县', '4', '411329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1330', '156', '桐柏县', '4', '411330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1331', '157', '新华区', '4', '410402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1332', '157', '卫东区', '4', '410403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1333', '157', '湛河区', '4', '410411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1334', '157', '石龙区', '4', '410404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1335', '157', '舞钢市', '4', '410481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1336', '157', '汝州市', '4', '410482');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1337', '157', '宝丰县', '4', '410421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1338', '157', '叶县', '4', '410422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1339', '157', '鲁山县', '4', '410423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1340', '157', '郏县', '4', '410425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1341', '158', '湖滨区', '4', '411202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1342', '158', '义马市', '4', '411281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1343', '158', '灵宝市', '4', '411282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1344', '158', '渑池县', '4', '411221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1345', '158', '陕县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1346', '158', '卢氏县', '4', '411224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1347', '159', '梁园区', '4', '411402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1348', '159', '睢阳区', '4', '411403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1349', '159', '永城市', '4', '411481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1350', '159', '民权县', '4', '411421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1351', '159', '睢县', '4', '411422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1352', '159', '宁陵县', '4', '411423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1353', '159', '虞城县', '4', '411425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1354', '159', '柘城县', '4', '411424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1355', '159', '夏邑县', '4', '411426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1356', '160', '卫滨区', '4', '410703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1357', '160', '红旗区', '4', '410702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1358', '160', '凤泉区', '4', '410704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1359', '160', '牧野区', '4', '410711');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1360', '160', '卫辉市', '4', '410781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1361', '160', '辉县市', '4', '410782');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1362', '160', '新乡县', '4', '410721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1363', '160', '获嘉县', '4', '410724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1364', '160', '原阳县', '4', '410725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1365', '160', '延津县', '4', '410726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1366', '160', '封丘县', '4', '410727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1367', '160', '长垣县', '4', '410728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1368', '161', '浉河区', '4', '411502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1369', '161', '平桥区', '4', '411503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1370', '161', '罗山县', '4', '411521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1371', '161', '光山县', '4', '411522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1372', '161', '新县', '4', '411523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1373', '161', '商城县', '4', '411524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1374', '161', '固始县', '4', '411525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1375', '161', '潢川县', '4', '411526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1376', '161', '淮滨县', '4', '411527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1377', '161', '息县', '4', '411528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1378', '162', '魏都区', '4', '411002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1379', '162', '禹州市', '4', '411081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1380', '162', '长葛市', '4', '411082');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1381', '162', '许昌县', '4', '411023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1382', '162', '鄢陵县', '4', '411024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1383', '162', '襄城县', '4', '411025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1384', '163', '川汇区', '4', '411602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1385', '163', '项城市', '4', '411681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1386', '163', '扶沟县', '4', '411621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1387', '163', '西华县', '4', '411622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1388', '163', '商水县', '4', '411623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1389', '163', '沈丘县', '4', '411624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1390', '163', '郸城县', '4', '411625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1391', '163', '淮阳县', '4', '411626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1392', '163', '太康县', '4', '411627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1393', '163', '鹿邑县', '4', '411628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1394', '164', '驿城区', '4', '411702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1395', '164', '西平县', '4', '411721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1396', '164', '上蔡县', '4', '411722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1397', '164', '平舆县', '4', '411723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1398', '164', '正阳县', '4', '411724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1399', '164', '确山县', '4', '411725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1400', '164', '泌阳县', '4', '411726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1401', '164', '汝南县', '4', '411727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1402', '164', '遂平县', '4', '411728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1403', '164', '新蔡县', '4', '411729');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1404', '165', '郾城区', '4', '411103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1405', '165', '源汇区', '4', '411102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1406', '165', '召陵区', '4', '411104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1407', '165', '舞阳县', '4', '411121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1408', '165', '临颍县', '4', '411122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1409', '166', '华龙区', '4', '410902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1410', '166', '清丰县', '4', '410922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1411', '166', '南乐县', '4', '410923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1412', '166', '范县', '4', '410926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1413', '166', '台前县', '4', '410927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1414', '166', '濮阳县', '4', '410928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1415', '167', '道里区', '4', '230102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1416', '167', '南岗区', '4', '230103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1417', '167', '动力区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1418', '167', '平房区', '4', '230108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1419', '167', '香坊区', '4', '230110');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1420', '167', '太平区', '4', '210904');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1421', '167', '道外区', '4', '230104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1422', '167', '阿城区', '4', '230112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1423', '167', '呼兰区', '4', '230111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1424', '167', '松北区', '4', '230109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1425', '167', '尚志市', '4', '230183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1426', '167', '双城市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1427', '167', '五常市', '4', '230184');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1428', '167', '方正县', '4', '230124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1429', '167', '宾县', '4', '230125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1430', '167', '依兰县', '4', '230123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1431', '167', '巴彦县', '4', '230126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1432', '167', '通河县', '4', '230128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1433', '167', '木兰县', '4', '230127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1434', '167', '延寿县', '4', '230129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1435', '168', '萨尔图区', '4', '230602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1436', '168', '红岗区', '4', '230605');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1437', '168', '龙凤区', '4', '230603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1438', '168', '让胡路区', '4', '230604');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1439', '168', '大同区', '4', '230606');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1440', '168', '肇州县', '4', '230621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1441', '168', '肇源县', '4', '230622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1442', '168', '林甸县', '4', '230623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1443', '168', '杜尔伯特', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1444', '169', '呼玛县', '4', '232721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1445', '169', '漠河县', '4', '232723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1446', '169', '塔河县', '4', '232722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1447', '170', '兴山区', '4', '230407');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1448', '170', '工农区', '4', '230403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1449', '170', '南山区', '4', '230404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1450', '170', '兴安区', '4', '230405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1451', '170', '向阳区', '4', '230402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1452', '170', '东山区', '4', '230406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1453', '170', '萝北县', '4', '230421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1454', '170', '绥滨县', '4', '230422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1455', '171', '爱辉区', '4', '231102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1456', '171', '五大连池市', '4', '231182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1457', '171', '北安市', '4', '231181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1458', '171', '嫩江县', '4', '231121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1459', '171', '逊克县', '4', '231123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1460', '171', '孙吴县', '4', '231124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1461', '172', '鸡冠区', '4', '230302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1462', '172', '恒山区', '4', '230303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1463', '172', '城子河区', '4', '230306');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1464', '172', '滴道区', '4', '230304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1465', '172', '梨树区', '4', '230305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1466', '172', '虎林市', '4', '230381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1467', '172', '密山市', '4', '230382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1468', '172', '鸡东县', '4', '230321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1469', '173', '前进区', '4', '230804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1470', '173', '郊区', '4', '230811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1471', '173', '向阳区', '4', '230803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1472', '173', '东风区', '4', '230805');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1473', '173', '同江市', '4', '230881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1474', '173', '富锦市', '4', '230882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1475', '173', '桦南县', '4', '230822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1476', '173', '桦川县', '4', '230826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1477', '173', '汤原县', '4', '230828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1478', '173', '抚远县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1479', '174', '爱民区', '4', '231004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1480', '174', '东安区', '4', '231002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1481', '174', '阳明区', '4', '231003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1482', '174', '西安区', '4', '231005');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1483', '174', '绥芬河市', '4', '231081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1484', '174', '海林市', '4', '231083');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1485', '174', '宁安市', '4', '231084');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1486', '174', '穆棱市', '4', '231085');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1487', '174', '东宁县', '4', '621026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1488', '174', '林口县', '4', '231025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1489', '175', '桃山区', '4', '230903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1490', '175', '新兴区', '4', '230902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1491', '175', '茄子河区', '4', '230904');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1492', '175', '勃利县', '4', '230921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1493', '176', '龙沙区', '4', '230202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1494', '176', '昂昂溪区', '4', '230205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1495', '176', '铁峰区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1496', '176', '建华区', '4', '230203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1497', '176', '富拉尔基区', '4', '230206');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1498', '176', '碾子山区', '4', '230207');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1499', '176', '梅里斯达斡尔区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1500', '176', '讷河市', '4', '230281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1501', '176', '龙江县', '4', '230221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1502', '176', '依安县', '4', '230223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1503', '176', '泰来县', '4', '230224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1504', '176', '甘南县', '4', '230225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1505', '176', '富裕县', '4', '230227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1506', '176', '克山县', '4', '230229');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1507', '176', '克东县', '4', '230230');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1508', '176', '拜泉县', '4', '230231');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1509', '177', '尖山区', '4', '230502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1510', '177', '岭东区', '4', '230503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1511', '177', '四方台区', '4', '230505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1512', '177', '宝山区', '4', '230506');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1513', '177', '集贤县', '4', '230521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1514', '177', '友谊县', '4', '230522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1515', '177', '宝清县', '4', '230523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1516', '177', '饶河县', '4', '230524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1517', '178', '北林区', '4', '231202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1518', '178', '安达市', '4', '231281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1519', '178', '肇东市', '4', '231282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1520', '178', '海伦市', '4', '231283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1521', '178', '望奎县', '4', '231221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1522', '178', '兰西县', '4', '231222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1523', '178', '青冈县', '4', '231223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1524', '178', '庆安县', '4', '231224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1525', '178', '明水县', '4', '231225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1526', '178', '绥棱县', '4', '231226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1527', '179', '伊春区', '4', '230702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1528', '179', '带岭区', '4', '230713');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1529', '179', '南岔区', '4', '230703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1530', '179', '金山屯区', '4', '230709');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1531', '179', '西林区', '4', '230705');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1532', '179', '美溪区', '4', '230708');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1533', '179', '乌马河区', '4', '230711');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1534', '179', '翠峦区', '4', '230706');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1535', '179', '友好区', '4', '230704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1536', '179', '上甘岭区', '4', '230716');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1537', '179', '五营区', '4', '230710');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1538', '179', '红星区', '4', '230715');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1539', '179', '新青区', '4', '230707');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1540', '179', '汤旺河区', '4', '230712');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1541', '179', '乌伊岭区', '4', '230714');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1542', '179', '铁力市', '4', '230781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1543', '179', '嘉荫县', '4', '230722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1544', '180', '江岸区', '4', '420102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1545', '180', '武昌区', '4', '420106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1546', '180', '江汉区', '4', '420103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1547', '180', '硚口区', '4', '420104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1548', '180', '汉阳区', '4', '420105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1549', '180', '青山区', '4', '420107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1550', '180', '洪山区', '4', '420111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1551', '180', '东西湖区', '4', '420112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1552', '180', '汉南区', '4', '420113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1553', '180', '蔡甸区', '4', '420114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1554', '180', '江夏区', '4', '420115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1555', '180', '黄陂区', '4', '420116');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1556', '180', '新洲区', '4', '420117');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1557', '180', '经济开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1558', '181', '仙桃市', '4', '429004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1559', '182', '鄂城区', '4', '420704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1560', '182', '华容区', '4', '420703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1561', '182', '梁子湖区', '4', '420702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1562', '183', '黄州区', '4', '421102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1563', '183', '麻城市', '4', '421181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1564', '183', '武穴市', '4', '421182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1565', '183', '团风县', '4', '421121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1566', '183', '红安县', '4', '421122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1567', '183', '罗田县', '4', '421123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1568', '183', '英山县', '4', '421124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1569', '183', '浠水县', '4', '421125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1570', '183', '蕲春县', '4', '421126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1571', '183', '黄梅县', '4', '421127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1572', '184', '黄石港区', '4', '420202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1573', '184', '西塞山区', '4', '420203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1574', '184', '下陆区', '4', '420204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1575', '184', '铁山区', '4', '420205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1576', '184', '大冶市', '4', '420281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1577', '184', '阳新县', '4', '420222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1578', '185', '东宝区', '4', '420802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1579', '185', '掇刀区', '4', '420804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1580', '185', '钟祥市', '4', '420881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1581', '185', '京山县', '4', '420821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1582', '185', '沙洋县', '4', '420822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1583', '186', '沙市区', '4', '421002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1584', '186', '荆州区', '4', '421003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1585', '186', '石首市', '4', '421081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1586', '186', '洪湖市', '4', '421083');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1587', '186', '松滋市', '4', '421087');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1588', '186', '公安县', '4', '421022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1589', '186', '监利县', '4', '421023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1590', '186', '江陵县', '4', '421024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1591', '187', '潜江市', '4', '429005');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1592', '188', '神农架林区', '4', '429021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1593', '189', '张湾区', '4', '420303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1594', '189', '茅箭区', '4', '420302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1595', '189', '丹江口市', '4', '420381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1596', '189', '郧县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1597', '189', '郧西县', '4', '420322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1598', '189', '竹山县', '4', '420323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1599', '189', '竹溪县', '4', '420324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1600', '189', '房县', '4', '420325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1601', '190', '曾都区', '4', '421303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1602', '190', '广水市', '4', '421381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1603', '191', '天门市', '4', '429006');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1604', '192', '咸安区', '4', '421202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1605', '192', '赤壁市', '4', '421281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1606', '192', '嘉鱼县', '4', '421221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1607', '192', '通城县', '4', '421222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1608', '192', '崇阳县', '4', '421223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1609', '192', '通山县', '4', '421224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1610', '193', '襄城区', '4', '420602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1611', '193', '樊城区', '4', '420606');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1612', '193', '襄阳区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1613', '193', '老河口市', '4', '420682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1614', '193', '枣阳市', '4', '420683');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1615', '193', '宜城市', '4', '420684');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1616', '193', '南漳县', '4', '420624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1617', '193', '谷城县', '4', '420625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1618', '193', '保康县', '4', '420626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1619', '194', '孝南区', '4', '420902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1620', '194', '应城市', '4', '420981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1621', '194', '安陆市', '4', '420982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1622', '194', '汉川市', '4', '420984');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1623', '194', '孝昌县', '4', '420921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1624', '194', '大悟县', '4', '420922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1625', '194', '云梦县', '4', '420923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1626', '195', '长阳', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1627', '195', '五峰', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1628', '195', '西陵区', '4', '420502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1629', '195', '伍家岗区', '4', '420503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1630', '195', '点军区', '4', '420504');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1631', '195', '猇亭区', '4', '420505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1632', '195', '夷陵区', '4', '420506');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1633', '195', '宜都市', '4', '420581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1634', '195', '当阳市', '4', '420582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1635', '195', '枝江市', '4', '420583');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1636', '195', '远安县', '4', '420525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1637', '195', '兴山县', '4', '420526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1638', '195', '秭归县', '4', '420527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1639', '196', '恩施市', '4', '422801');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1640', '196', '利川市', '4', '422802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1641', '196', '建始县', '4', '422822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1642', '196', '巴东县', '4', '422823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1643', '196', '宣恩县', '4', '422825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1644', '196', '咸丰县', '4', '422826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1645', '196', '来凤县', '4', '422827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1646', '196', '鹤峰县', '4', '422828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1647', '197', '岳麓区', '4', '430104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1648', '197', '芙蓉区', '4', '430102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1649', '197', '天心区', '4', '430103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1650', '197', '开福区', '4', '430105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1651', '197', '雨花区', '4', '430111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1652', '197', '开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1653', '197', '浏阳市', '4', '430181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1654', '197', '长沙县', '4', '430121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1655', '197', '望城县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1656', '197', '宁乡县', '4', '430124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1657', '198', '永定区', '4', '430802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1658', '198', '武陵源区', '4', '430811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1659', '198', '慈利县', '4', '430821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1660', '198', '桑植县', '4', '430822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1661', '199', '武陵区', '4', '430702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1662', '199', '鼎城区', '4', '430703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1663', '199', '津市市', '4', '430781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1664', '199', '安乡县', '4', '430721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1665', '199', '汉寿县', '4', '430722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1666', '199', '澧县', '4', '430723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1667', '199', '临澧县', '4', '430724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1668', '199', '桃源县', '4', '430725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1669', '199', '石门县', '4', '430726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1670', '200', '北湖区', '4', '431002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1671', '200', '苏仙区', '4', '431003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1672', '200', '资兴市', '4', '431081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1673', '200', '桂阳县', '4', '431021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1674', '200', '宜章县', '4', '431022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1675', '200', '永兴县', '4', '431023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1676', '200', '嘉禾县', '4', '431024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1677', '200', '临武县', '4', '431025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1678', '200', '汝城县', '4', '431026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1679', '200', '桂东县', '4', '431027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1680', '200', '安仁县', '4', '431028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1681', '201', '雁峰区', '4', '430406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1682', '201', '珠晖区', '4', '430405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1683', '201', '石鼓区', '4', '430407');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1684', '201', '蒸湘区', '4', '430408');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1685', '201', '南岳区', '4', '430412');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1686', '201', '耒阳市', '4', '430481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1687', '201', '常宁市', '4', '430482');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1688', '201', '衡阳县', '4', '430421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1689', '201', '衡南县', '4', '430422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1690', '201', '衡山县', '4', '430423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1691', '201', '衡东县', '4', '430424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1692', '201', '祁东县', '4', '430426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1693', '202', '鹤城区', '4', '431202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1694', '202', '靖州', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1695', '202', '麻阳', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1696', '202', '通道', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1697', '202', '新晃', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1698', '202', '芷江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1699', '202', '沅陵县', '4', '431222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1700', '202', '辰溪县', '4', '431223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1701', '202', '溆浦县', '4', '431224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1702', '202', '中方县', '4', '431221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1703', '202', '会同县', '4', '431225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1704', '202', '洪江市', '4', '431281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1705', '203', '娄星区', '4', '431302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1706', '203', '冷水江市', '4', '431381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1707', '203', '涟源市', '4', '431382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1708', '203', '双峰县', '4', '431321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1709', '203', '新化县', '4', '431322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1710', '204', '城步', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1711', '204', '双清区', '4', '430502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1712', '204', '大祥区', '4', '430503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1713', '204', '北塔区', '4', '430511');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1714', '204', '武冈市', '4', '430581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1715', '204', '邵东县', '4', '430521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1716', '204', '新邵县', '4', '430522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1717', '204', '邵阳县', '4', '430523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1718', '204', '隆回县', '4', '430524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1719', '204', '洞口县', '4', '430525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1720', '204', '绥宁县', '4', '430527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1721', '204', '新宁县', '4', '430528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1722', '205', '岳塘区', '4', '430304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1723', '205', '雨湖区', '4', '430302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1724', '205', '湘乡市', '4', '430381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1725', '205', '韶山市', '4', '430382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1726', '205', '湘潭县', '4', '430321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1727', '206', '吉首市', '4', '433101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1728', '206', '泸溪县', '4', '433122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1729', '206', '凤凰县', '4', '433123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1730', '206', '花垣县', '4', '433124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1731', '206', '保靖县', '4', '433125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1732', '206', '古丈县', '4', '433126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1733', '206', '永顺县', '4', '433127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1734', '206', '龙山县', '4', '433130');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1735', '207', '赫山区', '4', '430903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1736', '207', '资阳区', '4', '430902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1737', '207', '沅江市', '4', '430981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1738', '207', '南县', '4', '430921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1739', '207', '桃江县', '4', '430922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1740', '207', '安化县', '4', '430923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1741', '208', '江华', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1742', '208', '冷水滩区', '4', '431103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1743', '208', '零陵区', '4', '431102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1744', '208', '祁阳县', '4', '431121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1745', '208', '东安县', '4', '431122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1746', '208', '双牌县', '4', '431123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1747', '208', '道县', '4', '431124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1748', '208', '江永县', '4', '431125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1749', '208', '宁远县', '4', '431126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1750', '208', '蓝山县', '4', '431127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1751', '208', '新田县', '4', '431128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1752', '209', '岳阳楼区', '4', '430602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1753', '209', '君山区', '4', '430611');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1754', '209', '云溪区', '4', '430603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1755', '209', '汨罗市', '4', '430681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1756', '209', '临湘市', '4', '430682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1757', '209', '岳阳县', '4', '430621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1758', '209', '华容县', '4', '430623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1759', '209', '湘阴县', '4', '430624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1760', '209', '平江县', '4', '430626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1761', '210', '天元区', '4', '430211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1762', '210', '荷塘区', '4', '430202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1763', '210', '芦淞区', '4', '430203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1764', '210', '石峰区', '4', '430204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1765', '210', '醴陵市', '4', '430281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1766', '210', '株洲县', '4', '430221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1767', '210', '攸县', '4', '430223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1768', '210', '茶陵县', '4', '430224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1769', '210', '炎陵县', '4', '430225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1770', '211', '朝阳区', '4', '220104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1771', '211', '宽城区', '4', '220103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1772', '211', '二道区', '4', '220105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1773', '211', '南关区', '4', '220102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1774', '211', '绿园区', '4', '220106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1775', '211', '双阳区', '4', '220112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1776', '211', '净月潭开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1777', '211', '高新技术开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1778', '211', '经济技术开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1779', '211', '汽车产业开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1780', '211', '德惠市', '4', '220183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1781', '211', '九台市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1782', '211', '榆树市', '4', '220182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1783', '211', '农安县', '4', '220122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1784', '212', '船营区', '4', '220204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1785', '212', '昌邑区', '4', '220202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1786', '212', '龙潭区', '4', '220203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1787', '212', '丰满区', '4', '220211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1788', '212', '蛟河市', '4', '220281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1789', '212', '桦甸市', '4', '220282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1790', '212', '舒兰市', '4', '220283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1791', '212', '磐石市', '4', '220284');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1792', '212', '永吉县', '4', '220221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1793', '213', '洮北区', '4', '220802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1794', '213', '洮南市', '4', '220881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1795', '213', '大安市', '4', '220882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1796', '213', '镇赉县', '4', '220821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1797', '213', '通榆县', '4', '220822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1798', '214', '江源区', '4', '220605');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1799', '214', '八道江区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1800', '214', '长白', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1801', '214', '临江市', '4', '220681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1802', '214', '抚松县', '4', '220621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1803', '214', '靖宇县', '4', '220622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1804', '215', '龙山区', '4', '220402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1805', '215', '西安区', '4', '220403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1806', '215', '东丰县', '4', '220421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1807', '215', '东辽县', '4', '220422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1808', '216', '铁西区', '4', '220302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1809', '216', '铁东区', '4', '220303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1810', '216', '伊通', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1811', '216', '公主岭市', '4', '220381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1812', '216', '双辽市', '4', '220382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1813', '216', '梨树县', '4', '220322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1814', '217', '前郭尔罗斯', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1815', '217', '宁江区', '4', '220702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1816', '217', '长岭县', '4', '220722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1817', '217', '乾安县', '4', '220723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1818', '217', '扶余县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1819', '218', '东昌区', '4', '220502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1820', '218', '二道江区', '4', '220503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1821', '218', '梅河口市', '4', '220581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1822', '218', '集安市', '4', '220582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1823', '218', '通化县', '4', '220521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1824', '218', '辉南县', '4', '220523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1825', '218', '柳河县', '4', '220524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1826', '219', '延吉市', '4', '222401');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1827', '219', '图们市', '4', '222402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1828', '219', '敦化市', '4', '222403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1829', '219', '珲春市', '4', '222404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1830', '219', '龙井市', '4', '222405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1831', '219', '和龙市', '4', '222406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1832', '219', '安图县', '4', '222426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1833', '219', '汪清县', '4', '222424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1834', '220', '玄武区', '4', '320102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1835', '220', '鼓楼区', '4', '320106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1836', '220', '白下区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1837', '220', '建邺区', '4', '320105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1838', '220', '秦淮区', '4', '320104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1839', '220', '雨花台区', '4', '320114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1840', '220', '下关区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1841', '220', '栖霞区', '4', '320113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1842', '220', '浦口区', '4', '320111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1843', '220', '江宁区', '4', '320115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1844', '220', '六合区', '4', '320116');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1845', '220', '溧水县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1846', '220', '高淳县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1847', '221', '沧浪区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1848', '221', '金阊区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1849', '221', '平江区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1850', '221', '虎丘区', '4', '320505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1851', '221', '吴中区', '4', '320506');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1852', '221', '相城区', '4', '320507');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1853', '221', '园区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1854', '221', '新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1855', '221', '常熟市', '4', '320581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1856', '221', '张家港市', '4', '320582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1857', '221', '玉山镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1858', '221', '巴城镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1859', '221', '周市镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1860', '221', '陆家镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1861', '221', '花桥镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1862', '221', '淀山湖镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1863', '221', '张浦镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1864', '221', '周庄镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1865', '221', '千灯镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1866', '221', '锦溪镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1867', '221', '开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1868', '221', '吴江市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1869', '221', '太仓市', '4', '320585');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1870', '222', '崇安区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1871', '222', '北塘区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1872', '222', '南长区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1873', '222', '锡山区', '4', '320205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1874', '222', '惠山区', '4', '320206');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1875', '222', '滨湖区', '4', '320211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1876', '222', '新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1877', '222', '江阴市', '4', '320281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1878', '222', '宜兴市', '4', '320282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1879', '223', '天宁区', '4', '320402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1880', '223', '钟楼区', '4', '320404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1881', '223', '戚墅堰区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1882', '223', '郊区', '4', '140311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1883', '223', '新北区', '4', '320411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1884', '223', '武进区', '4', '320412');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1885', '223', '溧阳市', '4', '320481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1886', '223', '金坛市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1887', '224', '清河区', '4', '211204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1888', '224', '清浦区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1889', '224', '楚州区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1890', '224', '淮阴区', '4', '320804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1891', '224', '涟水县', '4', '320826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1892', '224', '洪泽县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1893', '224', '盱眙县', '4', '320830');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1894', '224', '金湖县', '4', '320831');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1895', '225', '新浦区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1896', '225', '连云区', '4', '320703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1897', '225', '海州区', '4', '320706');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1898', '225', '赣榆县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1899', '225', '东海县', '4', '320722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1900', '225', '灌云县', '4', '320723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1901', '225', '灌南县', '4', '320724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1902', '226', '崇川区', '4', '320602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1903', '226', '港闸区', '4', '320611');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1904', '226', '经济开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1905', '226', '启东市', '4', '320681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1906', '226', '如皋市', '4', '320682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1907', '226', '通州市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1908', '226', '海门市', '4', '320684');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1909', '226', '海安县', '4', '320621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1910', '226', '如东县', '4', '320623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1911', '227', '宿城区', '4', '321302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1912', '227', '宿豫区', '4', '321311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1913', '227', '宿豫县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1914', '227', '沭阳县', '4', '321322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1915', '227', '泗阳县', '4', '321323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1916', '227', '泗洪县', '4', '321324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1917', '228', '海陵区', '4', '321202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1918', '228', '高港区', '4', '321203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1919', '228', '兴化市', '4', '321281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1920', '228', '靖江市', '4', '321282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1921', '228', '泰兴市', '4', '321283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1922', '228', '姜堰市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1923', '229', '云龙区', '4', '320303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1924', '229', '鼓楼区', '4', '320302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1925', '229', '九里区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1926', '229', '贾汪区', '4', '320305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1927', '229', '泉山区', '4', '320311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1928', '229', '新沂市', '4', '320381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1929', '229', '邳州市', '4', '320382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1930', '229', '丰县', '4', '320321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1931', '229', '沛县', '4', '320322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1932', '229', '铜山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1933', '229', '睢宁县', '4', '320324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1934', '230', '城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1935', '230', '亭湖区', '4', '320902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1936', '230', '盐都区', '4', '320903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1937', '230', '盐都县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1938', '230', '东台市', '4', '320981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1939', '230', '大丰市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1940', '230', '响水县', '4', '320921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1941', '230', '滨海县', '4', '320922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1942', '230', '阜宁县', '4', '320923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1943', '230', '射阳县', '4', '320924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1944', '230', '建湖县', '4', '320925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1945', '231', '广陵区', '4', '321002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1946', '231', '维扬区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1947', '231', '邗江区', '4', '321003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1948', '231', '仪征市', '4', '321081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1949', '231', '高邮市', '4', '321084');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1950', '231', '江都市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1951', '231', '宝应县', '4', '321023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1952', '232', '京口区', '4', '321102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1953', '232', '润州区', '4', '321111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1954', '232', '丹徒区', '4', '321112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1955', '232', '丹阳市', '4', '321181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1956', '232', '扬中市', '4', '321182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1957', '232', '句容市', '4', '321183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1958', '233', '东湖区', '4', '360102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1959', '233', '西湖区', '4', '360103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1960', '233', '青云谱区', '4', '360104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1961', '233', '湾里区', '4', '360105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1962', '233', '青山湖区', '4', '360111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1963', '233', '红谷滩新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1964', '233', '昌北区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1965', '233', '高新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1966', '233', '南昌县', '4', '360121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1967', '233', '新建县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1968', '233', '安义县', '4', '360123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1969', '233', '进贤县', '4', '360124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1970', '234', '临川区', '4', '361002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1971', '234', '南城县', '4', '361021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1972', '234', '黎川县', '4', '361022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1973', '234', '南丰县', '4', '361023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1974', '234', '崇仁县', '4', '361024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1975', '234', '乐安县', '4', '361025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1976', '234', '宜黄县', '4', '361026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1977', '234', '金溪县', '4', '361027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1978', '234', '资溪县', '4', '361028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1979', '234', '东乡县', '4', '361029');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1980', '234', '广昌县', '4', '361030');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1981', '235', '章贡区', '4', '360702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1982', '235', '于都县', '4', '360731');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1983', '235', '瑞金市', '4', '360781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1984', '235', '南康市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1985', '235', '赣县', '4', '360721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1986', '235', '信丰县', '4', '360722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1987', '235', '大余县', '4', '360723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1988', '235', '上犹县', '4', '360724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1989', '235', '崇义县', '4', '360725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1990', '235', '安远县', '4', '360726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1991', '235', '龙南县', '4', '360727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1992', '235', '定南县', '4', '360728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1993', '235', '全南县', '4', '360729');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1994', '235', '宁都县', '4', '360730');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1995', '235', '兴国县', '4', '360732');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1996', '235', '会昌县', '4', '360733');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1997', '235', '寻乌县', '4', '360734');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1998', '235', '石城县', '4', '360735');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1999', '236', '安福县', '4', '360829');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2000', '236', '吉州区', '4', '360802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2001', '236', '青原区', '4', '360803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2002', '236', '井冈山市', '4', '360881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2003', '236', '吉安县', '4', '360821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2004', '236', '吉水县', '4', '360822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2005', '236', '峡江县', '4', '360823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2006', '236', '新干县', '4', '360824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2007', '236', '永丰县', '4', '360825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2008', '236', '泰和县', '4', '360826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2009', '236', '遂川县', '4', '360827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2010', '236', '万安县', '4', '360828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2011', '236', '永新县', '4', '360830');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2012', '237', '珠山区', '4', '360203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2013', '237', '昌江区', '4', '360202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2014', '237', '乐平市', '4', '360281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2015', '237', '浮梁县', '4', '360222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2016', '238', '浔阳区', '4', '360403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2017', '238', '庐山区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2018', '238', '瑞昌市', '4', '360481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2019', '238', '九江县', '4', '360421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2020', '238', '武宁县', '4', '360423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2021', '238', '修水县', '4', '360424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2022', '238', '永修县', '4', '360425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2023', '238', '德安县', '4', '360426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2024', '238', '星子县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2025', '238', '都昌县', '4', '360428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2026', '238', '湖口县', '4', '360429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2027', '238', '彭泽县', '4', '360430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2028', '239', '安源区', '4', '360302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2029', '239', '湘东区', '4', '360313');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2030', '239', '莲花县', '4', '360321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2031', '239', '芦溪县', '4', '360323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2032', '239', '上栗县', '4', '360322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2033', '240', '信州区', '4', '361102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2034', '240', '德兴市', '4', '361181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2035', '240', '上饶县', '4', '361121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2036', '240', '广丰县', '4', '320321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2037', '240', '玉山县', '4', '361123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2038', '240', '铅山县', '4', '361124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2039', '240', '横峰县', '4', '361125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2040', '240', '弋阳县', '4', '361126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2041', '240', '余干县', '4', '361127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2042', '240', '波阳县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2043', '240', '万年县', '4', '361129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2044', '240', '婺源县', '4', '361130');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2045', '241', '渝水区', '4', '360502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2046', '241', '分宜县', '4', '360521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2047', '242', '袁州区', '4', '360902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2048', '242', '丰城市', '4', '360981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2049', '242', '樟树市', '4', '360982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2050', '242', '高安市', '4', '360983');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2051', '242', '奉新县', '4', '360921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2052', '242', '万载县', '4', '360922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2053', '242', '上高县', '4', '360923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2054', '242', '宜丰县', '4', '360924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2055', '242', '靖安县', '4', '360925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2056', '242', '铜鼓县', '4', '360926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2057', '243', '月湖区', '4', '360602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2058', '243', '贵溪市', '4', '360681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2059', '243', '余江县', '4', '360622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2060', '244', '沈河区', '4', '210103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2061', '244', '皇姑区', '4', '210105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2062', '244', '和平区', '4', '210102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2063', '244', '大东区', '4', '210104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2064', '244', '铁西区', '4', '210106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2065', '244', '苏家屯区', '4', '210111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2066', '244', '东陵区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2067', '244', '沈北新区', '4', '210113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2068', '244', '于洪区', '4', '210114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2069', '244', '浑南新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2070', '244', '新民市', '4', '210181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2071', '244', '辽中县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2072', '244', '康平县', '4', '210123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2073', '244', '法库县', '4', '210124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2074', '245', '西岗区', '4', '210203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2075', '245', '中山区', '4', '210202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2076', '245', '沙河口区', '4', '210204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2077', '245', '甘井子区', '4', '210211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2078', '245', '旅顺口区', '4', '210212');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2079', '245', '金州区', '4', '210213');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2080', '245', '开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2081', '245', '瓦房店市', '4', '210281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2082', '245', '普兰店市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2083', '245', '庄河市', '4', '210283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2084', '245', '长海县', '4', '210224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2085', '246', '铁东区', '4', '210302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2086', '246', '铁西区', '4', '210303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2087', '246', '立山区', '4', '210304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2088', '246', '千山区', '4', '210311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2089', '246', '岫岩', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2090', '246', '海城市', '4', '210381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2091', '246', '台安县', '4', '210321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2092', '247', '本溪', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2093', '247', '平山区', '4', '210502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2094', '247', '明山区', '4', '210504');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2095', '247', '溪湖区', '4', '210503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2096', '247', '南芬区', '4', '210505');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2097', '247', '桓仁', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2098', '248', '双塔区', '4', '211302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2099', '248', '龙城区', '4', '211303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2100', '248', '喀喇沁左翼蒙古族自治', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2101', '248', '北票市', '4', '211381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2102', '248', '凌源市', '4', '211382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2103', '248', '朝阳县', '4', '211321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2104', '248', '建平县', '4', '211322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2105', '249', '振兴区', '4', '210603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2106', '249', '元宝区', '4', '210602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2107', '249', '振安区', '4', '210604');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2108', '249', '宽甸', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2109', '249', '东港市', '4', '210681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2110', '249', '凤城市', '4', '210682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2111', '250', '顺城区', '4', '210411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2112', '250', '新抚区', '4', '210402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2113', '250', '东洲区', '4', '210403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2114', '250', '望花区', '4', '210404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2115', '250', '清原', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2116', '250', '新宾', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2117', '250', '抚顺县', '4', '210421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2118', '251', '阜新', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2119', '251', '海州区', '4', '210902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2120', '251', '新邱区', '4', '210903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2121', '251', '太平区', '4', '210904');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2122', '251', '清河门区', '4', '210905');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2123', '251', '细河区', '4', '210911');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2124', '251', '彰武县', '4', '210922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2125', '252', '龙港区', '4', '211403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2126', '252', '南票区', '4', '211404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2127', '252', '连山区', '4', '211402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2128', '252', '兴城市', '4', '211481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2129', '252', '绥中县', '4', '211421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2130', '252', '建昌县', '4', '211422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2131', '253', '太和区', '4', '210711');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2132', '253', '古塔区', '4', '210702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2133', '253', '凌河区', '4', '210703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2134', '253', '凌海市', '4', '210781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2135', '253', '北镇市', '4', '210782');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2136', '253', '黑山县', '4', '210726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2137', '253', '义县', '4', '210727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2138', '254', '白塔区', '4', '211002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2139', '254', '文圣区', '4', '211003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2140', '254', '宏伟区', '4', '211004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2141', '254', '太子河区', '4', '211011');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2142', '254', '弓长岭区', '4', '211005');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2143', '254', '灯塔市', '4', '211081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2144', '254', '辽阳县', '4', '211021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2145', '255', '双台子区', '4', '211102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2146', '255', '兴隆台区', '4', '211103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2147', '255', '大洼县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2148', '255', '盘山县', '4', '211122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2149', '256', '银州区', '4', '211202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2150', '256', '清河区', '4', '211204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2151', '256', '调兵山市', '4', '211281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2152', '256', '开原市', '4', '211282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2153', '256', '铁岭县', '4', '211221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2154', '256', '西丰县', '4', '211223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2155', '256', '昌图县', '4', '211224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2156', '257', '站前区', '4', '210802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2157', '257', '西市区', '4', '210803');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2158', '257', '鲅鱼圈区', '4', '210804');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2159', '257', '老边区', '4', '210811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2160', '257', '盖州市', '4', '210881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2161', '257', '大石桥市', '4', '210882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2162', '258', '回民区', '4', '150103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2163', '258', '玉泉区', '4', '150104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2164', '258', '新城区', '4', '150102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2165', '258', '赛罕区', '4', '150105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2166', '258', '清水河县', '4', '150124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2167', '258', '土默特左旗', '4', '150121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2168', '258', '托克托县', '4', '150122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2169', '258', '和林格尔县', '4', '150123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2170', '258', '武川县', '4', '150125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2171', '259', '阿拉善左旗', '4', '152921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2172', '259', '阿拉善右旗', '4', '152922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2173', '259', '额济纳旗', '4', '152923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2174', '260', '临河区', '4', '150802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2175', '260', '五原县', '4', '150821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2176', '260', '磴口县', '4', '150822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2177', '260', '乌拉特前旗', '4', '150823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2178', '260', '乌拉特中旗', '4', '150824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2179', '260', '乌拉特后旗', '4', '150825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2180', '260', '杭锦后旗', '4', '150826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2181', '261', '昆都仑区', '4', '150203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2182', '261', '青山区', '4', '150204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2183', '261', '东河区', '4', '150202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2184', '261', '九原区', '4', '150207');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2185', '261', '石拐区', '4', '150205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2186', '261', '白云矿区', '4', '140203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2187', '261', '土默特右旗', '4', '150221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2188', '261', '固阳县', '4', '150222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2189', '261', '达尔罕茂明安联合旗', '4', '150223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2190', '262', '红山区', '4', '150402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2191', '262', '元宝山区', '4', '150403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2192', '262', '松山区', '4', '150404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2193', '262', '阿鲁科尔沁旗', '4', '150421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2194', '262', '巴林左旗', '4', '150422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2195', '262', '巴林右旗', '4', '150423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2196', '262', '林西县', '4', '150424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2197', '262', '克什克腾旗', '4', '150425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2198', '262', '翁牛特旗', '4', '150426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2199', '262', '喀喇沁旗', '4', '150428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2200', '262', '宁城县', '4', '150429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2201', '262', '敖汉旗', '4', '150430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2202', '263', '东胜区', '4', '150602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2203', '263', '达拉特旗', '4', '150621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2204', '263', '准格尔旗', '4', '150622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2205', '263', '鄂托克前旗', '4', '150623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2206', '263', '鄂托克旗', '4', '150624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2207', '263', '杭锦旗', '4', '150625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2208', '263', '乌审旗', '4', '150626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2209', '263', '伊金霍洛旗', '4', '150627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2210', '264', '海拉尔区', '4', '150702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2211', '264', '莫力达瓦', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2212', '264', '满洲里市', '4', '150781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2213', '264', '牙克石市', '4', '150782');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2214', '264', '扎兰屯市', '4', '150783');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2215', '264', '额尔古纳市', '4', '150784');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2216', '264', '根河市', '4', '150785');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2217', '264', '阿荣旗', '4', '150721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2218', '264', '鄂伦春自治旗', '4', '150723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2219', '264', '鄂温克族自治旗', '4', '150724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2220', '264', '陈巴尔虎旗', '4', '150725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2221', '264', '新巴尔虎左旗', '4', '150726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2222', '264', '新巴尔虎右旗', '4', '150727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2223', '265', '科尔沁区', '4', '150502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2224', '265', '霍林郭勒市', '4', '150581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2225', '265', '科尔沁左翼中旗', '4', '150521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2226', '265', '科尔沁左翼后旗', '4', '150522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2227', '265', '开鲁县', '4', '150523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2228', '265', '库伦旗', '4', '150524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2229', '265', '奈曼旗', '4', '150525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2230', '265', '扎鲁特旗', '4', '150526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2231', '266', '海勃湾区', '4', '150302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2232', '266', '乌达区', '4', '150304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2233', '266', '海南区', '4', '150303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2234', '267', '化德县', '4', '150922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2235', '267', '集宁区', '4', '150902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2236', '267', '丰镇市', '4', '150981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2237', '267', '卓资县', '4', '150921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2238', '267', '商都县', '4', '150923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2239', '267', '兴和县', '4', '150924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2240', '267', '凉城县', '4', '150925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2241', '267', '察哈尔右翼前旗', '4', '150926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2242', '267', '察哈尔右翼中旗', '4', '150927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2243', '267', '察哈尔右翼后旗', '4', '150928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2244', '267', '四子王旗', '4', '150929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2245', '268', '二连浩特市', '4', '152501');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2246', '268', '锡林浩特市', '4', '152502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2247', '268', '阿巴嘎旗', '4', '152522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2248', '268', '苏尼特左旗', '4', '152523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2249', '268', '苏尼特右旗', '4', '152524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2250', '268', '东乌珠穆沁旗', '4', '152525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2251', '268', '西乌珠穆沁旗', '4', '152526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2252', '268', '太仆寺旗', '4', '152527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2253', '268', '镶黄旗', '4', '152528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2254', '268', '正镶白旗', '4', '152529');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2255', '268', '正蓝旗', '4', '152530');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2256', '268', '多伦县', '4', '152531');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2257', '269', '乌兰浩特市', '4', '152201');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2258', '269', '阿尔山市', '4', '152202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2259', '269', '科尔沁右翼前旗', '4', '152221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2260', '269', '科尔沁右翼中旗', '4', '152222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2261', '269', '扎赉特旗', '4', '152223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2262', '269', '突泉县', '4', '152224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2263', '270', '西夏区', '4', '640105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2264', '270', '金凤区', '4', '640106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2265', '270', '兴庆区', '4', '640104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2266', '270', '灵武市', '4', '640181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2267', '270', '永宁县', '4', '640121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2268', '270', '贺兰县', '4', '640122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2269', '271', '原州区', '4', '640402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2270', '271', '海原县', '4', '640522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2271', '271', '西吉县', '4', '640422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2272', '271', '隆德县', '4', '640423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2273', '271', '泾源县', '4', '640424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2274', '271', '彭阳县', '4', '640425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2275', '272', '惠农县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2276', '272', '大武口区', '4', '640202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2277', '272', '惠农区', '4', '640205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2278', '272', '陶乐县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2279', '272', '平罗县', '4', '640221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2280', '273', '利通区', '4', '640302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2281', '273', '中卫县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2282', '273', '青铜峡市', '4', '640381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2283', '273', '中宁县', '4', '640521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2284', '273', '盐池县', '4', '640323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2285', '273', '同心县', '4', '640324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2286', '274', '沙坡头区', '4', '640502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2287', '274', '海原县', '4', '640522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2288', '274', '中宁县', '4', '640521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2289', '275', '城中区', '4', '450202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2290', '275', '城东区', '4', '630102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2291', '275', '城西区', '4', '630104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2292', '275', '城北区', '4', '630105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2293', '275', '湟中县', '4', '630122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2294', '275', '湟源县', '4', '630123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2295', '275', '大通', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2296', '276', '玛沁县', '4', '632621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2297', '276', '班玛县', '4', '632622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2298', '276', '甘德县', '4', '632623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2299', '276', '达日县', '4', '632624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2300', '276', '久治县', '4', '632625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2301', '276', '玛多县', '4', '632626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2302', '277', '海晏县', '4', '632223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2303', '277', '祁连县', '4', '632222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2304', '277', '刚察县', '4', '632224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2305', '277', '门源', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2306', '278', '平安县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2307', '278', '乐都县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2308', '278', '民和', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2309', '278', '互助', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2310', '278', '化隆', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2311', '278', '循化', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2312', '279', '共和县', '4', '632521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2313', '279', '同德县', '4', '632522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2314', '279', '贵德县', '4', '632523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2315', '279', '兴海县', '4', '632524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2316', '279', '贵南县', '4', '632525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2317', '280', '德令哈市', '4', '632802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2318', '280', '格尔木市', '4', '632801');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2319', '280', '乌兰县', '4', '632821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2320', '280', '都兰县', '4', '632822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2321', '280', '天峻县', '4', '632823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2322', '281', '同仁县', '4', '632321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2323', '281', '尖扎县', '4', '632322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2324', '281', '泽库县', '4', '632323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2325', '281', '河南蒙古族自治县', '4', '632324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2326', '282', '玉树县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2327', '282', '杂多县', '4', '632722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2328', '282', '称多县', '4', '632723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2329', '282', '治多县', '4', '632724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2330', '282', '囊谦县', '4', '632725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2331', '282', '曲麻莱县', '4', '632726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2332', '283', '市中区', '4', '370103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2333', '283', '历下区', '4', '370102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2334', '283', '天桥区', '4', '370105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2335', '283', '槐荫区', '4', '370104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2336', '283', '历城区', '4', '370112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2337', '283', '长清区', '4', '370113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2338', '283', '章丘市', '4', '370181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2339', '283', '平阴县', '4', '370124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2340', '283', '济阳县', '4', '370125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2341', '283', '商河县', '4', '370126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2342', '284', '市南区', '4', '370202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2343', '284', '市北区', '4', '370203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2344', '284', '城阳区', '4', '370214');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2345', '284', '四方区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2346', '284', '李沧区', '4', '370213');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2347', '284', '黄岛区', '4', '370211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2348', '284', '崂山区', '4', '370212');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2349', '284', '胶州市', '4', '370281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2350', '284', '即墨市', '4', '370282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2351', '284', '平度市', '4', '370283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2352', '284', '胶南市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2353', '284', '莱西市', '4', '370285');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2354', '285', '滨城区', '4', '371602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2355', '285', '惠民县', '4', '371621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2356', '285', '阳信县', '4', '371622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2357', '285', '无棣县', '4', '371623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2358', '285', '沾化县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2359', '285', '博兴县', '4', '371625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2360', '285', '邹平县', '4', '371626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2361', '286', '德城区', '4', '371402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2362', '286', '陵县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2363', '286', '乐陵市', '4', '371481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2364', '286', '禹城市', '4', '371482');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2365', '286', '宁津县', '4', '371422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2366', '286', '庆云县', '4', '371423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2367', '286', '临邑县', '4', '371424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2368', '286', '齐河县', '4', '371425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2369', '286', '平原县', '4', '371426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2370', '286', '夏津县', '4', '371427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2371', '286', '武城县', '4', '371428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2372', '287', '东营区', '4', '370502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2373', '287', '河口区', '4', '370503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2374', '287', '垦利县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2375', '287', '利津县', '4', '370522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2376', '287', '广饶县', '4', '370523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2377', '288', '牡丹区', '4', '371702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2378', '288', '曹县', '4', '371721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2379', '288', '单县', '4', '371722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2380', '288', '成武县', '4', '371723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2381', '288', '巨野县', '4', '371724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2382', '288', '郓城县', '4', '371725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2383', '288', '鄄城县', '4', '371726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2384', '288', '定陶县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2385', '288', '东明县', '4', '371728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2386', '289', '市中区', '4', '370103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2387', '289', '任城区', '4', '370811');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2388', '289', '曲阜市', '4', '370881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2389', '289', '兖州市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2390', '289', '邹城市', '4', '370883');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2391', '289', '微山县', '4', '370826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2392', '289', '鱼台县', '4', '370827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2393', '289', '金乡县', '4', '370828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2394', '289', '嘉祥县', '4', '370829');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2395', '289', '汶上县', '4', '370830');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2396', '289', '泗水县', '4', '370831');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2397', '289', '梁山县', '4', '370832');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2398', '290', '莱城区', '4', '371202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2399', '290', '钢城区', '4', '371203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2400', '291', '东昌府区', '4', '371502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2401', '291', '临清市', '4', '371581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2402', '291', '阳谷县', '4', '371521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2403', '291', '莘县', '4', '371522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2404', '291', '茌平县', '4', '371523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2405', '291', '东阿县', '4', '371524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2406', '291', '冠县', '4', '371525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2407', '291', '高唐县', '4', '371526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2408', '292', '兰山区', '4', '371302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2409', '292', '罗庄区', '4', '371311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2410', '292', '河东区', '4', '371312');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2411', '292', '沂南县', '4', '371321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2412', '292', '郯城县', '4', '371322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2413', '292', '沂水县', '4', '371323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2414', '292', '苍山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2415', '292', '费县', '4', '371325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2416', '292', '平邑县', '4', '371326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2417', '292', '莒南县', '4', '371327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2418', '292', '蒙阴县', '4', '371328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2419', '292', '临沭县', '4', '371329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2420', '293', '东港区', '4', '371102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2421', '293', '岚山区', '4', '371103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2422', '293', '五莲县', '4', '371121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2423', '293', '莒县', '4', '371122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2424', '294', '泰山区', '4', '370902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2425', '294', '岱岳区', '4', '370911');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2426', '294', '新泰市', '4', '370982');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2427', '294', '肥城市', '4', '370983');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2428', '294', '宁阳县', '4', '370921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2429', '294', '东平县', '4', '370923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2430', '295', '荣成市', '4', '371082');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2431', '295', '乳山市', '4', '371083');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2432', '295', '环翠区', '4', '371002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2433', '295', '文登市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2434', '296', '潍城区', '4', '370702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2435', '296', '寒亭区', '4', '370703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2436', '296', '坊子区', '4', '370704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2437', '296', '奎文区', '4', '370705');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2438', '296', '青州市', '4', '370781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2439', '296', '诸城市', '4', '370782');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2440', '296', '寿光市', '4', '370783');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2441', '296', '安丘市', '4', '370784');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2442', '296', '高密市', '4', '370785');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2443', '296', '昌邑市', '4', '370786');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2444', '296', '临朐县', '4', '370724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2445', '296', '昌乐县', '4', '370725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2446', '297', '芝罘区', '4', '370602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2447', '297', '福山区', '4', '370611');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2448', '297', '牟平区', '4', '370612');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2449', '297', '莱山区', '4', '370613');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2450', '297', '开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2451', '297', '龙口市', '4', '370681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2452', '297', '莱阳市', '4', '370682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2453', '297', '莱州市', '4', '370683');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2454', '297', '蓬莱市', '4', '370684');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2455', '297', '招远市', '4', '370685');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2456', '297', '栖霞市', '4', '370686');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2457', '297', '海阳市', '4', '370687');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2458', '297', '长岛县', '4', '370634');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2459', '298', '市中区', '4', '370402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2460', '298', '山亭区', '4', '370406');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2461', '298', '峄城区', '4', '370404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2462', '298', '台儿庄区', '4', '370405');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2463', '298', '薛城区', '4', '370403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2464', '298', '滕州市', '4', '370481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2465', '299', '张店区', '4', '370303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2466', '299', '临淄区', '4', '370305');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2467', '299', '淄川区', '4', '370302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2468', '299', '博山区', '4', '370304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2469', '299', '周村区', '4', '370306');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2470', '299', '桓台县', '4', '370321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2471', '299', '高青县', '4', '370322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2472', '299', '沂源县', '4', '370323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2473', '300', '杏花岭区', '4', '140107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2474', '300', '小店区', '4', '140105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2475', '300', '迎泽区', '4', '140106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2476', '300', '尖草坪区', '4', '140108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2477', '300', '万柏林区', '4', '140109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2478', '300', '晋源区', '4', '140110');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2479', '300', '高新开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2480', '300', '民营经济开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2481', '300', '经济技术开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2482', '300', '清徐县', '4', '140121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2483', '300', '阳曲县', '4', '140122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2484', '300', '娄烦县', '4', '140123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2485', '300', '古交市', '4', '140181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2486', '301', '城区', '4', '140402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2487', '301', '郊区', '4', '140411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2488', '301', '沁县', '4', '140430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2489', '301', '潞城市', '4', '140481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2490', '301', '长治县', '4', '140421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2491', '301', '襄垣县', '4', '140423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2492', '301', '屯留县', '4', '140424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2493', '301', '平顺县', '4', '140425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2494', '301', '黎城县', '4', '140426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2495', '301', '壶关县', '4', '140427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2496', '301', '长子县', '4', '140428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2497', '301', '武乡县', '4', '140429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2498', '301', '沁源县', '4', '140431');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2499', '302', '城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2500', '302', '矿区', '4', '140203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2501', '302', '南郊区', '4', '140211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2502', '302', '新荣区', '4', '140212');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2503', '302', '阳高县', '4', '140221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2504', '302', '天镇县', '4', '140222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2505', '302', '广灵县', '4', '140223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2506', '302', '灵丘县', '4', '140224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2507', '302', '浑源县', '4', '140225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2508', '302', '左云县', '4', '140226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2509', '302', '大同县', '4', '140227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2510', '303', '城区', '4', '140502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2511', '303', '高平市', '4', '140581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2512', '303', '沁水县', '4', '140521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2513', '303', '阳城县', '4', '140522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2514', '303', '陵川县', '4', '140524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2515', '303', '泽州县', '4', '140525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2516', '304', '榆次区', '4', '140702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2517', '304', '介休市', '4', '140781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2518', '304', '榆社县', '4', '140721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2519', '304', '左权县', '4', '140722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2520', '304', '和顺县', '4', '140723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2521', '304', '昔阳县', '4', '140724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2522', '304', '寿阳县', '4', '140725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2523', '304', '太谷县', '4', '140726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2524', '304', '祁县', '4', '140727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2525', '304', '平遥县', '4', '140728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2526', '304', '灵石县', '4', '140729');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2527', '305', '尧都区', '4', '141002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2528', '305', '侯马市', '4', '141081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2529', '305', '霍州市', '4', '141082');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2530', '305', '曲沃县', '4', '141021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2531', '305', '翼城县', '4', '141022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2532', '305', '襄汾县', '4', '141023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2533', '305', '洪洞县', '4', '141024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2534', '305', '吉县', '4', '141028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2535', '305', '安泽县', '4', '141026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2536', '305', '浮山县', '4', '141027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2537', '305', '古县', '4', '141025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2538', '305', '乡宁县', '4', '141029');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2539', '305', '大宁县', '4', '141030');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2540', '305', '隰县', '4', '141031');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2541', '305', '永和县', '4', '141032');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2542', '305', '蒲县', '4', '141033');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2543', '305', '汾西县', '4', '141034');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2544', '306', '离石市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2545', '306', '离石区', '4', '141102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2546', '306', '孝义市', '4', '141181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2547', '306', '汾阳市', '4', '141182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2548', '306', '文水县', '4', '141121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2549', '306', '交城县', '4', '141122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2550', '306', '兴县', '4', '141123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2551', '306', '临县', '4', '141124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2552', '306', '柳林县', '4', '141125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2553', '306', '石楼县', '4', '141126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2554', '306', '岚县', '4', '141127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2555', '306', '方山县', '4', '141128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2556', '306', '中阳县', '4', '141129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2557', '306', '交口县', '4', '141130');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2558', '307', '朔城区', '4', '140602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2559', '307', '平鲁区', '4', '140603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2560', '307', '山阴县', '4', '140621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2561', '307', '应县', '4', '140622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2562', '307', '右玉县', '4', '140623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2563', '307', '怀仁县', '4', '140624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2564', '308', '忻府区', '4', '140902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2565', '308', '原平市', '4', '140981');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2566', '308', '定襄县', '4', '140921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2567', '308', '五台县', '4', '140922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2568', '308', '代县', '4', '140923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2569', '308', '繁峙县', '4', '140924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2570', '308', '宁武县', '4', '140925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2571', '308', '静乐县', '4', '140926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2572', '308', '神池县', '4', '140927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2573', '308', '五寨县', '4', '140928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2574', '308', '岢岚县', '4', '140929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2575', '308', '河曲县', '4', '140930');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2576', '308', '保德县', '4', '140931');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2577', '308', '偏关县', '4', '140932');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2578', '309', '城区', '4', '140302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2579', '309', '矿区', '4', '140303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2580', '309', '郊区', '4', '140311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2581', '309', '平定县', '4', '140321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2582', '309', '盂县', '4', '140322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2583', '310', '盐湖区', '4', '140802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2584', '310', '永济市', '4', '140881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2585', '310', '河津市', '4', '140882');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2586', '310', '临猗县', '4', '140821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2587', '310', '万荣县', '4', '140822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2588', '310', '闻喜县', '4', '140823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2589', '310', '稷山县', '4', '140824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2590', '310', '新绛县', '4', '140825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2591', '310', '绛县', '4', '140826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2592', '310', '垣曲县', '4', '140827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2593', '310', '夏县', '4', '140828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2594', '310', '平陆县', '4', '140829');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2595', '310', '芮城县', '4', '140830');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2596', '311', '莲湖区', '4', '610104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2597', '311', '新城区', '4', '150102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2598', '311', '碑林区', '4', '610103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2599', '311', '雁塔区', '4', '610113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2600', '311', '灞桥区', '4', '610111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2601', '311', '未央区', '4', '610112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2602', '311', '阎良区', '4', '610114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2603', '311', '临潼区', '4', '610115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2604', '311', '长安区', '4', '130102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2605', '311', '蓝田县', '4', '610122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2606', '311', '周至县', '4', '610124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2607', '311', '户县', '4', '610125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2608', '311', '高陵县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2609', '312', '汉滨区', '4', '610902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2610', '312', '汉阴县', '4', '610921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2611', '312', '石泉县', '4', '610922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2612', '312', '宁陕县', '4', '610923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2613', '312', '紫阳县', '4', '610924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2614', '312', '岚皋县', '4', '610925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2615', '312', '平利县', '4', '610926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2616', '312', '镇坪县', '4', '610927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2617', '312', '旬阳县', '4', '610928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2618', '312', '白河县', '4', '610929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2619', '313', '陈仓区', '4', '610304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2620', '313', '渭滨区', '4', '610302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2621', '313', '金台区', '4', '610303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2622', '313', '凤翔县', '4', '610322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2623', '313', '岐山县', '4', '610323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2624', '313', '扶风县', '4', '610324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2625', '313', '眉县', '4', '610326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2626', '313', '陇县', '4', '610327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2627', '313', '千阳县', '4', '610328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2628', '313', '麟游县', '4', '610329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2629', '313', '凤县', '4', '610330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2630', '313', '太白县', '4', '610331');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2631', '314', '汉台区', '4', '610702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2632', '314', '南郑县', '4', '610721');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2633', '314', '城固县', '4', '610722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2634', '314', '洋县', '4', '610723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2635', '314', '西乡县', '4', '610724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2636', '314', '勉县', '4', '610725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2637', '314', '宁强县', '4', '610726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2638', '314', '略阳县', '4', '610727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2639', '314', '镇巴县', '4', '610728');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2640', '314', '留坝县', '4', '610729');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2641', '314', '佛坪县', '4', '610730');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2642', '315', '商州区', '4', '611002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2643', '315', '洛南县', '4', '611021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2644', '315', '丹凤县', '4', '611022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2645', '315', '商南县', '4', '611023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2646', '315', '山阳县', '4', '611024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2647', '315', '镇安县', '4', '611025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2648', '315', '柞水县', '4', '611026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2649', '316', '耀州区', '4', '610204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2650', '316', '王益区', '4', '610202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2651', '316', '印台区', '4', '610203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2652', '316', '宜君县', '4', '610222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2653', '317', '临渭区', '4', '610502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2654', '317', '韩城市', '4', '610581');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2655', '317', '华阴市', '4', '610582');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2656', '317', '华县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2657', '317', '潼关县', '4', '610522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2658', '317', '大荔县', '4', '610523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2659', '317', '合阳县', '4', '610524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2660', '317', '澄城县', '4', '610525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2661', '317', '蒲城县', '4', '610526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2662', '317', '白水县', '4', '610527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2663', '317', '富平县', '4', '610528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2664', '318', '秦都区', '4', '610402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2665', '318', '渭城区', '4', '610404');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2666', '318', '杨陵区', '4', '610403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2667', '318', '兴平市', '4', '610481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2668', '318', '三原县', '4', '610422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2669', '318', '泾阳县', '4', '610423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2670', '318', '乾县', '4', '610424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2671', '318', '礼泉县', '4', '610425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2672', '318', '永寿县', '4', '610426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2673', '318', '彬县', '4', '610427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2674', '318', '长武县', '4', '610428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2675', '318', '旬邑县', '4', '610429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2676', '318', '淳化县', '4', '610430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2677', '318', '武功县', '4', '610431');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2678', '319', '吴起县', '4', '610626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2679', '319', '宝塔区', '4', '610602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2680', '319', '延长县', '4', '610621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2681', '319', '延川县', '4', '610622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2682', '319', '子长县', '4', '610623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2683', '319', '安塞县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2684', '319', '志丹县', '4', '610625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2685', '319', '甘泉县', '4', '610627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2686', '319', '富县', '4', '610628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2687', '319', '洛川县', '4', '610629');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2688', '319', '宜川县', '4', '610630');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2689', '319', '黄龙县', '4', '610631');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2690', '319', '黄陵县', '4', '610632');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2691', '320', '榆阳区', '4', '610802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2692', '320', '神木县', '4', '610821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2693', '320', '府谷县', '4', '610822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2694', '320', '横山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2695', '320', '靖边县', '4', '610824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2696', '320', '定边县', '4', '610825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2697', '320', '绥德县', '4', '610826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2698', '320', '米脂县', '4', '610827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2699', '320', '佳县', '4', '610828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2700', '320', '吴堡县', '4', '610829');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2701', '320', '清涧县', '4', '610830');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2702', '320', '子洲县', '4', '610831');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2703', '321', '长宁区', '4', '310105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2704', '321', '闸北区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2705', '321', '闵行区', '4', '310112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2706', '321', '徐汇区', '4', '310104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2707', '321', '浦东新区', '4', '310115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2708', '321', '杨浦区', '4', '310110');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2709', '321', '普陀区', '4', '310107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2710', '321', '静安区', '4', '310106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2711', '321', '卢湾区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2712', '321', '虹口区', '4', '310109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2713', '321', '黄浦区', '4', '310101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2714', '321', '南汇区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2715', '321', '松江区', '4', '310117');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2716', '321', '嘉定区', '4', '310114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2717', '321', '宝山区', '4', '310113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2718', '321', '青浦区', '4', '310118');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2719', '321', '金山区', '4', '310116');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2720', '321', '奉贤区', '4', '310120');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2721', '321', '崇明县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2722', '322', '青羊区', '4', '510105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2723', '322', '锦江区', '4', '510104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2724', '322', '金牛区', '4', '510106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2725', '322', '武侯区', '4', '510107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2726', '322', '成华区', '4', '510108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2727', '322', '龙泉驿区', '4', '510112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2728', '322', '青白江区', '4', '510113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2729', '322', '新都区', '4', '510114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2730', '322', '温江区', '4', '510115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2731', '322', '高新区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2732', '322', '高新西区', '4', '510403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2733', '322', '都江堰市', '4', '510181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2734', '322', '彭州市', '4', '510182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2735', '322', '邛崃市', '4', '510183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2736', '322', '崇州市', '4', '510184');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2737', '322', '金堂县', '4', '510121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2738', '322', '双流县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2739', '322', '郫县', '4', '510124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2740', '322', '大邑县', '4', '510129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2741', '322', '蒲江县', '4', '510131');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2742', '322', '新津县', '4', '510132');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2743', '322', '都江堰市', '4', '510181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2744', '322', '彭州市', '4', '510182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2745', '322', '邛崃市', '4', '510183');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2746', '322', '崇州市', '4', '510184');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2747', '322', '金堂县', '4', '510121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2748', '322', '双流县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2749', '322', '郫县', '4', '510124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2750', '322', '大邑县', '4', '510129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2751', '322', '蒲江县', '4', '510131');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2752', '322', '新津县', '4', '510132');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2753', '323', '涪城区', '4', '510703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2754', '323', '游仙区', '4', '510704');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2755', '323', '江油市', '4', '510781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2756', '323', '盐亭县', '4', '510723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2757', '323', '三台县', '4', '510722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2758', '323', '平武县', '4', '510727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2759', '323', '安县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2760', '323', '梓潼县', '4', '510725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2761', '323', '北川县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2762', '324', '马尔康县', '4', '621224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2763', '324', '汶川县', '4', '513221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2764', '324', '理县', '4', '513222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2765', '324', '茂县', '4', '513223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2766', '324', '松潘县', '4', '513224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2767', '324', '九寨沟县', '4', '513225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2768', '324', '金川县', '4', '513226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2769', '324', '小金县', '4', '513227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2770', '324', '黑水县', '4', '513228');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2771', '324', '壤塘县', '4', '513230');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2772', '324', '阿坝县', '4', '513231');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2773', '324', '若尔盖县', '4', '513232');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2774', '324', '红原县', '4', '513233');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2775', '325', '巴州区', '4', '511902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2776', '325', '通江县', '4', '511921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2777', '325', '南江县', '4', '511922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2778', '325', '平昌县', '4', '511923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2779', '326', '通川区', '4', '511702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2780', '326', '万源市', '4', '511781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2781', '326', '达县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2782', '326', '宣汉县', '4', '511722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2783', '326', '开江县', '4', '511723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2784', '326', '大竹县', '4', '511724');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2785', '326', '渠县', '4', '511725');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2786', '327', '旌阳区', '4', '510603');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2787', '327', '广汉市', '4', '510681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2788', '327', '什邡市', '4', '510682');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2789', '327', '绵竹市', '4', '510683');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2790', '327', '罗江县', '4', '510626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2791', '327', '中江县', '4', '510623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2792', '328', '康定县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2793', '328', '丹巴县', '4', '513323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2794', '328', '泸定县', '4', '513322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2795', '328', '炉霍县', '4', '513327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2796', '328', '九龙县', '4', '513324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2797', '328', '甘孜县', '4', '513328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2798', '328', '雅江县', '4', '513325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2799', '328', '新龙县', '4', '513329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2800', '328', '道孚县', '4', '513326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2801', '328', '白玉县', '4', '513331');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2802', '328', '理塘县', '4', '513334');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2803', '328', '德格县', '4', '513330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2804', '328', '乡城县', '4', '513336');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2805', '328', '石渠县', '4', '513332');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2806', '328', '稻城县', '4', '513337');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2807', '328', '色达县', '4', '513333');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2808', '328', '巴塘县', '4', '513335');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2809', '328', '得荣县', '4', '513338');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2810', '329', '广安区', '4', '511602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2811', '329', '华蓥市', '4', '511681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2812', '329', '岳池县', '4', '511621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2813', '329', '武胜县', '4', '511622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2814', '329', '邻水县', '4', '511623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2815', '330', '利州区', '4', '510802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2816', '330', '元坝区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2817', '330', '朝天区', '4', '510812');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2818', '330', '旺苍县', '4', '510821');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2819', '330', '青川县', '4', '510822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2820', '330', '剑阁县', '4', '510823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2821', '330', '苍溪县', '4', '510824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2822', '331', '峨眉山市', '4', '511181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2823', '331', '乐山市', '4', '511100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2824', '331', '犍为县', '4', '511123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2825', '331', '井研县', '4', '511124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2826', '331', '夹江县', '4', '511126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2827', '331', '沐川县', '4', '511129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2828', '331', '峨边', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2829', '331', '马边', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2830', '332', '西昌市', '4', '513401');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2831', '332', '盐源县', '4', '513423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2832', '332', '德昌县', '4', '513424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2833', '332', '会理县', '4', '513425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2834', '332', '会东县', '4', '513426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2835', '332', '宁南县', '4', '513427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2836', '332', '普格县', '4', '513428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2837', '332', '布拖县', '4', '513429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2838', '332', '金阳县', '4', '513430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2839', '332', '昭觉县', '4', '513431');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2840', '332', '喜德县', '4', '513432');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2841', '332', '冕宁县', '4', '513433');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2842', '332', '越西县', '4', '513434');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2843', '332', '甘洛县', '4', '513435');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2844', '332', '美姑县', '4', '513436');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2845', '332', '雷波县', '4', '513437');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2846', '332', '木里', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2847', '333', '东坡区', '4', '511402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2848', '333', '仁寿县', '4', '511421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2849', '333', '彭山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2850', '333', '洪雅县', '4', '511423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2851', '333', '丹棱县', '4', '511424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2852', '333', '青神县', '4', '511425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2853', '334', '阆中市', '4', '511381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2854', '334', '南部县', '4', '511321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2855', '334', '营山县', '4', '511322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2856', '334', '蓬安县', '4', '511323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2857', '334', '仪陇县', '4', '511324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2858', '334', '顺庆区', '4', '511302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2859', '334', '高坪区', '4', '511303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2860', '334', '嘉陵区', '4', '511304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2861', '334', '西充县', '4', '511325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2862', '335', '市中区', '4', '511002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2863', '335', '东兴区', '4', '511011');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2864', '335', '威远县', '4', '511024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2865', '335', '资中县', '4', '511025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2866', '335', '隆昌县', '4', '511028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2867', '336', '东  区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2868', '336', '西  区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2869', '336', '仁和区', '4', '510411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2870', '336', '米易县', '4', '510421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2871', '336', '盐边县', '4', '510422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2872', '337', '船山区', '4', '510903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2873', '337', '安居区', '4', '510904');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2874', '337', '蓬溪县', '4', '510921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2875', '337', '射洪县', '4', '510922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2876', '337', '大英县', '4', '510923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2877', '338', '雨城区', '4', '511802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2878', '338', '名山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2879', '338', '荥经县', '4', '511822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2880', '338', '汉源县', '4', '511823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2881', '338', '石棉县', '4', '511824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2882', '338', '天全县', '4', '511825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2883', '338', '芦山县', '4', '511826');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2884', '338', '宝兴县', '4', '511827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2885', '339', '翠屏区', '4', '511502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2886', '339', '宜宾县', '4', '511521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2887', '339', '南溪县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2888', '339', '江安县', '4', '511523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2889', '339', '长宁县', '4', '511524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2890', '339', '高县', '4', '511525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2891', '339', '珙县', '4', '511526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2892', '339', '筠连县', '4', '511527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2893', '339', '兴文县', '4', '511528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2894', '339', '屏山县', '4', '511529');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2895', '340', '雁江区', '4', '512002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2896', '340', '简阳市', '4', '510185');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2897', '340', '安岳县', '4', '512021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2898', '340', '乐至县', '4', '512022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2899', '341', '大安区', '4', '510304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2900', '341', '自流井区', '4', '510302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2901', '341', '贡井区', '4', '510303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2902', '341', '沿滩区', '4', '510311');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2903', '341', '荣县', '4', '510321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2904', '341', '富顺县', '4', '510322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2905', '342', '江阳区', '4', '510502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2906', '342', '纳溪区', '4', '510503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2907', '342', '龙马潭区', '4', '510504');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2908', '342', '泸县', '4', '510521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2909', '342', '合江县', '4', '510522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2910', '342', '叙永县', '4', '510524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2911', '342', '古蔺县', '4', '510525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2912', '343', '和平区', '4', '120101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2913', '343', '河西区', '4', '120103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2914', '343', '南开区', '4', '120104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2915', '343', '河北区', '4', '120105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2916', '343', '河东区', '4', '120102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2917', '343', '红桥区', '4', '120106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2918', '343', '东丽区', '4', '120110');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2919', '343', '津南区', '4', '120112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2920', '343', '西青区', '4', '120111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2921', '343', '北辰区', '4', '120113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2922', '343', '塘沽区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2923', '343', '汉沽区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2924', '343', '大港区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2925', '343', '武清区', '4', '120114');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2926', '343', '宝坻区', '4', '120115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2927', '343', '经济开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2928', '343', '宁河县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2929', '343', '静海县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2930', '343', '蓟县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2931', '344', '城关区', '4', '540102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2932', '344', '林周县', '4', '540121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2933', '344', '当雄县', '4', '540122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2934', '344', '尼木县', '4', '540123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2935', '344', '曲水县', '4', '540124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2936', '344', '堆龙德庆县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2937', '344', '达孜县', '4', '540126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2938', '344', '墨竹工卡县', '4', '540127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2939', '345', '噶尔县', '4', '542523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2940', '345', '普兰县', '4', '542521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2941', '345', '札达县', '4', '542522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2942', '345', '日土县', '4', '542524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2943', '345', '革吉县', '4', '542525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2944', '345', '改则县', '4', '542526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2945', '345', '措勤县', '4', '542527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2946', '346', '昌都县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2947', '346', '江达县', '4', '540321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2948', '346', '贡觉县', '4', '540322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2949', '346', '类乌齐县', '4', '540323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2950', '346', '丁青县', '4', '540324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2951', '346', '察雅县', '4', '540325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2952', '346', '八宿县', '4', '540326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2953', '346', '左贡县', '4', '540327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2954', '346', '芒康县', '4', '540328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2955', '346', '洛隆县', '4', '540329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2956', '346', '边坝县', '4', '540330');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2957', '347', '林芝县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2958', '347', '工布江达县', '4', '540421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2959', '347', '米林县', '4', '540422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2960', '347', '墨脱县', '4', '540423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2961', '347', '波密县', '4', '540424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2962', '347', '察隅县', '4', '540425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2963', '347', '朗县', '4', '540426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2964', '348', '那曲县', '4', '542421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2965', '348', '嘉黎县', '4', '542422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2966', '348', '比如县', '4', '542423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2967', '348', '聂荣县', '4', '542424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2968', '348', '安多县', '4', '542425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2969', '348', '申扎县', '4', '542426');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2970', '348', '索县', '4', '542427');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2971', '348', '班戈县', '4', '542428');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2972', '348', '巴青县', '4', '542429');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2973', '348', '尼玛县', '4', '542430');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2974', '349', '日喀则市', '4', '540200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2975', '349', '南木林县', '4', '540221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2976', '349', '江孜县', '4', '540222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2977', '349', '定日县', '4', '540223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2978', '349', '萨迦县', '4', '540224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2979', '349', '拉孜县', '4', '540225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2980', '349', '昂仁县', '4', '540226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2981', '349', '谢通门县', '4', '540227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2982', '349', '白朗县', '4', '540228');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2983', '349', '仁布县', '4', '540229');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2984', '349', '康马县', '4', '540230');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2985', '349', '定结县', '4', '540231');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2986', '349', '仲巴县', '4', '540232');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2987', '349', '亚东县', '4', '540233');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2988', '349', '吉隆县', '4', '540234');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2989', '349', '聂拉木县', '4', '540235');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2990', '349', '萨嘎县', '4', '540236');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2991', '349', '岗巴县', '4', '540237');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2992', '350', '乃东县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2993', '350', '扎囊县', '4', '540521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2994', '350', '贡嘎县', '4', '540522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2995', '350', '桑日县', '4', '540523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2996', '350', '琼结县', '4', '540524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2997', '350', '曲松县', '4', '540525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2998', '350', '措美县', '4', '540526');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2999', '350', '洛扎县', '4', '540527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3000', '350', '加查县', '4', '540528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3001', '350', '隆子县', '4', '540529');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3002', '350', '错那县', '4', '540530');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3003', '350', '浪卡子县', '4', '540531');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3004', '351', '天山区', '4', '650102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3005', '351', '沙依巴克区', '4', '650103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3006', '351', '新市区', '4', '650104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3007', '351', '水磨沟区', '4', '650105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3008', '351', '头屯河区', '4', '650106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3009', '351', '达坂城区', '4', '650107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3010', '351', '米东区', '4', '650109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3011', '351', '乌鲁木齐县', '4', '650121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3012', '352', '阿克苏市', '4', '652901');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3013', '352', '温宿县', '4', '652922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3014', '352', '库车县', '4', '652923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3015', '352', '沙雅县', '4', '652924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3016', '352', '新和县', '4', '652925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3017', '352', '拜城县', '4', '652926');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3018', '352', '乌什县', '4', '652927');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3019', '352', '阿瓦提县', '4', '652928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3020', '352', '柯坪县', '4', '652929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3021', '353', '阿拉尔市', '4', '659002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3022', '354', '库尔勒市', '4', '652801');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3023', '354', '轮台县', '4', '652822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3024', '354', '尉犁县', '4', '652823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3025', '354', '若羌县', '4', '652824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3026', '354', '且末县', '4', '652825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3027', '354', '焉耆', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3028', '354', '和静县', '4', '652827');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3029', '354', '和硕县', '4', '652828');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3030', '354', '博湖县', '4', '652829');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3031', '355', '博乐市', '4', '652701');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3032', '355', '精河县', '4', '652722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3033', '355', '温泉县', '4', '652723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3034', '356', '呼图壁县', '4', '652323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3035', '356', '米泉市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3036', '356', '昌吉市', '4', '652301');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3037', '356', '阜康市', '4', '652302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3038', '356', '玛纳斯县', '4', '652324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3039', '356', '奇台县', '4', '652325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3040', '356', '吉木萨尔县', '4', '652327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3041', '356', '木垒', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3042', '357', '哈密市', '4', '650500');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3043', '357', '伊吾县', '4', '650522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3044', '357', '巴里坤', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3045', '358', '和田市', '4', '653201');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3046', '358', '和田县', '4', '653221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3047', '358', '墨玉县', '4', '653222');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3048', '358', '皮山县', '4', '653223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3049', '358', '洛浦县', '4', '653224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3050', '358', '策勒县', '4', '653225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3051', '358', '于田县', '4', '653226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3052', '358', '民丰县', '4', '653227');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3053', '359', '喀什市', '4', '653101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3054', '359', '疏附县', '4', '653121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3055', '359', '疏勒县', '4', '653122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3056', '359', '英吉沙县', '4', '653123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3057', '359', '泽普县', '4', '653124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3058', '359', '莎车县', '4', '653125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3059', '359', '叶城县', '4', '653126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3060', '359', '麦盖提县', '4', '653127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3061', '359', '岳普湖县', '4', '653128');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3062', '359', '伽师县', '4', '653129');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3063', '359', '巴楚县', '4', '653130');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3064', '359', '塔什库尔干', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3065', '360', '克拉玛依市', '4', '650200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3066', '361', '阿图什市', '4', '653001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3067', '361', '阿克陶县', '4', '653022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3068', '361', '阿合奇县', '4', '653023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3069', '361', '乌恰县', '4', '653024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3070', '362', '石河子市', '4', '659001');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3071', '363', '图木舒克市', '4', '659003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3072', '364', '吐鲁番市', '4', '650400');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3073', '364', '鄯善县', '4', '650421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3074', '364', '托克逊县', '4', '650422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3075', '365', '五家渠市', '4', '659004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3076', '366', '阿勒泰市', '4', '654301');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3077', '366', '布克赛尔', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3078', '366', '伊宁市', '4', '654002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3079', '366', '布尔津县', '4', '654321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3080', '366', '奎屯市', '4', '654003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3081', '366', '乌苏市', '4', '654202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3082', '366', '额敏县', '4', '654221');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3083', '366', '富蕴县', '4', '654322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3084', '366', '伊宁县', '4', '654021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3085', '366', '福海县', '4', '654323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3086', '366', '霍城县', '4', '654023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3087', '366', '沙湾县', '4', '654223');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3088', '366', '巩留县', '4', '654024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3089', '366', '哈巴河县', '4', '654324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3090', '366', '托里县', '4', '654224');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3091', '366', '青河县', '4', '654325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3092', '366', '新源县', '4', '654025');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3093', '366', '裕民县', '4', '654225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3094', '366', '和布克赛尔', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3095', '366', '吉木乃县', '4', '654326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3096', '366', '昭苏县', '4', '654026');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3097', '366', '特克斯县', '4', '654027');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3098', '366', '尼勒克县', '4', '654028');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3099', '366', '察布查尔', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3100', '367', '盘龙区', '4', '530103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3101', '367', '五华区', '4', '530102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3102', '367', '官渡区', '4', '530111');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3103', '367', '西山区', '4', '530112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3104', '367', '东川区', '4', '530113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3105', '367', '安宁市', '4', '530181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3106', '367', '呈贡县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3107', '367', '晋宁县', '4', '530122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3108', '367', '富民县', '4', '530124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3109', '367', '宜良县', '4', '530125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3110', '367', '嵩明县', '4', '530127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3111', '367', '石林县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3112', '367', '禄劝', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3113', '367', '寻甸', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3114', '368', '兰坪', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3115', '368', '泸水县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3116', '368', '福贡县', '4', '533323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3117', '368', '贡山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3118', '369', '宁洱', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3119', '369', '思茅区', '4', '530802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3120', '369', '墨江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3121', '369', '景东', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3122', '369', '景谷', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3123', '369', '镇沅', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3124', '369', '江城', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3125', '369', '孟连', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3126', '369', '澜沧', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3127', '369', '西盟', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3128', '370', '古城区', '4', '530702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3129', '370', '宁蒗', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3130', '370', '玉龙', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3131', '370', '永胜县', '4', '530722');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3132', '370', '华坪县', '4', '530723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3133', '371', '隆阳区', '4', '530502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3134', '371', '施甸县', '4', '530521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3135', '371', '腾冲县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3136', '371', '龙陵县', '4', '530523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3137', '371', '昌宁县', '4', '530524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3138', '372', '楚雄市', '4', '532301');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3139', '372', '双柏县', '4', '532322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3140', '372', '牟定县', '4', '532323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3141', '372', '南华县', '4', '532324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3142', '372', '姚安县', '4', '532325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3143', '372', '大姚县', '4', '532326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3144', '372', '永仁县', '4', '532327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3145', '372', '元谋县', '4', '532328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3146', '372', '武定县', '4', '532329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3147', '372', '禄丰县', '4', '532331');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3148', '373', '大理市', '4', '532901');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3149', '373', '祥云县', '4', '532923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3150', '373', '宾川县', '4', '532924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3151', '373', '弥渡县', '4', '532925');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3152', '373', '永平县', '4', '532928');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3153', '373', '云龙县', '4', '532929');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3154', '373', '洱源县', '4', '532930');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3155', '373', '剑川县', '4', '532931');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3156', '373', '鹤庆县', '4', '532932');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3157', '373', '漾濞', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3158', '373', '南涧', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3159', '373', '巍山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3160', '374', '潞西市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3161', '374', '瑞丽市', '4', '533102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3162', '374', '梁河县', '4', '533122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3163', '374', '盈江县', '4', '533123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3164', '374', '陇川县', '4', '533124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3165', '375', '香格里拉县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3166', '375', '德钦县', '4', '533422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3167', '375', '维西', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3168', '376', '泸西县', '4', '532527');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3169', '376', '蒙自县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3170', '376', '个旧市', '4', '532501');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3171', '376', '开远市', '4', '532502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3172', '376', '绿春县', '4', '532531');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3173', '376', '建水县', '4', '532524');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3174', '376', '石屏县', '4', '532525');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3175', '376', '弥勒县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3176', '376', '元阳县', '4', '532528');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3177', '376', '红河县', '4', '532529');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3178', '376', '金平', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3179', '376', '河口', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3180', '376', '屏边', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3181', '377', '临翔区', '4', '530902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3182', '377', '凤庆县', '4', '530921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3183', '377', '云县', '4', '530922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3184', '377', '永德县', '4', '530923');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3185', '377', '镇康县', '4', '530924');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3186', '377', '双江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3187', '377', '耿马', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3188', '377', '沧源', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3189', '378', '麒麟区', '4', '530302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3190', '378', '宣威市', '4', '530381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3191', '378', '马龙县', '4', '530321');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3192', '378', '陆良县', '4', '530322');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3193', '378', '师宗县', '4', '530323');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3194', '378', '罗平县', '4', '530324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3195', '378', '富源县', '4', '530325');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3196', '378', '会泽县', '4', '530326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3197', '378', '沾益县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3198', '379', '文山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3199', '379', '砚山县', '4', '532622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3200', '379', '西畴县', '4', '532623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3201', '379', '麻栗坡县', '4', '532624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3202', '379', '马关县', '4', '532625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3203', '379', '丘北县', '4', '532626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3204', '379', '广南县', '4', '532627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3205', '379', '富宁县', '4', '532628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3206', '380', '景洪市', '4', '532801');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3207', '380', '勐海县', '4', '532822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3208', '380', '勐腊县', '4', '532823');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3209', '381', '红塔区', '4', '530402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3210', '381', '江川县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3211', '381', '澄江县', '4', '530422');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3212', '381', '通海县', '4', '530423');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3213', '381', '华宁县', '4', '530424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3214', '381', '易门县', '4', '530425');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3215', '381', '峨山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3216', '381', '新平', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3217', '381', '元江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3218', '382', '昭阳区', '4', '530602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3219', '382', '鲁甸县', '4', '530621');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3220', '382', '巧家县', '4', '530622');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3221', '382', '盐津县', '4', '530623');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3222', '382', '大关县', '4', '530624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3223', '382', '永善县', '4', '530625');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3224', '382', '绥江县', '4', '530626');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3225', '382', '镇雄县', '4', '530627');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3226', '382', '彝良县', '4', '530628');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3227', '382', '威信县', '4', '530629');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3228', '382', '水富县', '4', '530630');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3229', '383', '西湖区', '4', '330106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3230', '383', '上城区', '4', '330102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3231', '383', '下城区', '4', '330103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3232', '383', '拱墅区', '4', '330105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3233', '383', '滨江区', '4', '330108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3234', '383', '江干区', '4', '330104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3235', '383', '萧山区', '4', '330109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3236', '383', '余杭区', '4', '330110');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3237', '383', '市郊', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3238', '383', '建德市', '4', '330182');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3239', '383', '富阳市', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3240', '383', '临安市', '4', '330185');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3241', '383', '桐庐县', '4', '330122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3242', '383', '淳安县', '4', '330127');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3243', '384', '吴兴区', '4', '330502');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3244', '384', '南浔区', '4', '330503');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3245', '384', '德清县', '4', '330521');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3246', '384', '长兴县', '4', '330522');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3247', '384', '安吉县', '4', '330523');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3248', '385', '南湖区', '4', '330402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3249', '385', '秀洲区', '4', '330411');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3250', '385', '海宁市', '4', '330481');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3251', '385', '嘉善县', '4', '330421');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3252', '385', '平湖市', '4', '330482');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3253', '385', '桐乡市', '4', '330483');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3254', '385', '海盐县', '4', '330424');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3255', '386', '婺城区', '4', '330702');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3256', '386', '金东区', '4', '330703');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3257', '386', '兰溪市', '4', '330781');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3258', '386', '市区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3259', '386', '佛堂镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3260', '386', '上溪镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3261', '386', '义亭镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3262', '386', '大陈镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3263', '386', '苏溪镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3264', '386', '赤岸镇', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3265', '386', '东阳市', '4', '330783');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3266', '386', '永康市', '4', '330784');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3267', '386', '武义县', '4', '330723');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3268', '386', '浦江县', '4', '330726');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3269', '386', '磐安县', '4', '330727');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3270', '387', '莲都区', '4', '331102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3271', '387', '龙泉市', '4', '331181');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3272', '387', '青田县', '4', '331121');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3273', '387', '缙云县', '4', '331122');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3274', '387', '遂昌县', '4', '331123');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3275', '387', '松阳县', '4', '331124');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3276', '387', '云和县', '4', '331125');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3277', '387', '庆元县', '4', '331126');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3278', '387', '景宁', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3279', '388', '海曙区', '4', '330203');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3280', '388', '江东区', '4', '330204');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3281', '388', '江北区', '4', '330205');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3282', '388', '镇海区', '4', '330211');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3283', '388', '北仑区', '4', '330206');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3284', '388', '鄞州区', '4', '330212');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3285', '388', '余姚市', '4', '330281');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3286', '388', '慈溪市', '4', '330282');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3287', '388', '奉化市', '4', '330283');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3288', '388', '象山县', '4', '330225');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3289', '388', '宁海县', '4', '330226');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3290', '389', '越城区', '4', '330602');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3291', '389', '上虞市', '4', '330604');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3292', '389', '嵊州市', '4', '330683');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3293', '389', '绍兴县', '4', '330601');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3294', '389', '新昌县', '4', '330624');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3295', '389', '诸暨市', '4', '330681');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3296', '390', '椒江区', '4', '331002');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3297', '390', '黄岩区', '4', '331003');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3298', '390', '路桥区', '4', '331004');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3299', '390', '温岭市', '4', '331081');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3300', '390', '临海市', '4', '331082');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3301', '390', '玉环县', '4', '331021');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3302', '390', '三门县', '4', '331022');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3303', '390', '天台县', '4', '331023');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3304', '390', '仙居县', '4', '331024');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3305', '391', '鹿城区', '4', '330302');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3306', '391', '龙湾区', '4', '330303');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3307', '391', '瓯海区', '4', '330304');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3308', '391', '瑞安市', '4', '330381');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3309', '391', '乐清市', '4', '330382');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3310', '391', '洞头县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3311', '391', '永嘉县', '4', '330324');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3312', '391', '平阳县', '4', '330326');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3313', '391', '苍南县', '4', '330327');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3314', '391', '文成县', '4', '330328');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3315', '391', '泰顺县', '4', '330329');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3316', '392', '定海区', '4', '330902');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3317', '392', '普陀区', '4', '330903');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3318', '392', '岱山县', '4', '330921');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3319', '392', '嵊泗县', '4', '330922');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3320', '393', '衢州市', '4', '330800');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3321', '393', '江山市', '4', '330881');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3322', '393', '常山县', '4', '330822');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3323', '393', '开化县', '4', '330824');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3324', '393', '龙游县', '4', '330825');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3325', '394', '合川区', '4', '500117');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3326', '394', '江津区', '4', '500116');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3327', '394', '南川区', '4', '500119');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3328', '394', '永川区', '4', '500118');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3329', '394', '南岸区', '4', '500108');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3330', '394', '渝北区', '4', '500112');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3331', '394', '万盛区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3332', '394', '大渡口区', '4', '500104');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3333', '394', '万州区', '4', '500101');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3334', '394', '北碚区', '4', '500109');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3335', '394', '沙坪坝区', '4', '500106');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3336', '394', '巴南区', '4', '500113');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3337', '394', '涪陵区', '4', '500102');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3338', '394', '江北区', '4', '500105');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3339', '394', '九龙坡区', '4', '500107');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3340', '394', '渝中区', '4', '500103');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3341', '394', '黔江开发区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3342', '394', '长寿区', '4', '500115');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3343', '394', '双桥区', '4', '130802');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3344', '394', '綦江县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3345', '394', '潼南县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3346', '394', '铜梁县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3347', '394', '大足县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3348', '394', '荣昌县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3349', '394', '璧山县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3350', '394', '垫江县', '4', '500231');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3351', '394', '武隆县', '4', '500232');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3352', '394', '丰都县', '4', '500230');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3353', '394', '城口县', '4', '500229');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3354', '394', '梁平县', '4', '500228');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3355', '394', '开县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3356', '394', '巫溪县', '4', '500238');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3357', '394', '巫山县', '4', '500237');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3358', '394', '奉节县', '4', '500236');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3359', '394', '云阳县', '4', '500235');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3360', '394', '忠县', '4', '500233');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3361', '394', '石柱', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3362', '394', '彭水', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3363', '394', '酉阳', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3364', '394', '秀山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3365', '395', '沙田区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3366', '395', '东区', '4', '510402');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3367', '395', '观塘区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3368', '395', '黄大仙区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3369', '395', '九龙城区', '4', '140202');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3370', '395', '屯门区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3371', '395', '葵青区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3372', '395', '元朗区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3373', '395', '深水埗区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3374', '395', '西贡区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3375', '395', '大埔区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3376', '395', '湾仔区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3377', '395', '油尖旺区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3378', '395', '北区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3379', '395', '南区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3380', '395', '荃湾区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3381', '395', '中西区', '4', '510403');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3382', '395', '离岛区', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3383', '396', '澳门', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3384', '397', '台北', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3385', '397', '高雄', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3386', '397', '基隆', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3387', '397', '台中', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3388', '397', '台南', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3389', '397', '新竹', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3390', '397', '嘉义', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3391', '397', '宜兰县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3392', '397', '桃园县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3393', '397', '苗栗县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3394', '397', '彰化县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3395', '397', '南投县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3396', '397', '云林县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3397', '397', '屏东县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3398', '397', '台东县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3399', '397', '花莲县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3400', '397', '澎湖县', '4', '500200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1', '0', '中国', '1', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3401', '3', '合肥', '3', '340100');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3402', '3401', '瑶海', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3403', '3401', '庐阳', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3404', '3401', '蜀山', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3405', '3401', '包河', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3406', '3401', '长丰', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3407', '3401', '肥东', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3408', '3401', '肥西', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3409', '3401', '巢湖', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3410', '3401', '庐江', '4', '0');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3411', '9', '三沙', '3', '460300');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3413', '29', '塔城', '3', '654200');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3414', '29', '铁门关', '3', '659006');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3415', '29', '阿勒泰', '3', '654300');



DROP TABLE IF EXISTS `%DB_PREFIX%ecv`;
CREATE TABLE `%DB_PREFIX%ecv` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`sn` varchar(255) NOT NULL COMMENT '序列号',
`password` varchar(255) NOT NULL COMMENT '密码',
`use_limit` int(11) NOT NULL COMMENT '代金券的使用次数,0表示无限次数使用',
`use_count` int(11) NOT NULL COMMENT '已用次数',
`user_id` int(11) NOT NULL COMMENT '会员ID ',
`begin_time` int(11) NOT NULL COMMENT '有效期开始时间',
`end_time` int(11) NOT NULL COMMENT '有效期结束时间',
`money` decimal(20,4) NOT NULL COMMENT '代金券面额',
`ecv_type_id` int(11) NOT NULL COMMENT '代金额类型ID',
PRIMARY KEY (`id`),
UNIQUE KEY `unk_sn` (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=utf8 COMMENT='代金券表';
DROP TABLE IF EXISTS `%DB_PREFIX%ecv_type`;
CREATE TABLE `%DB_PREFIX%ecv_type` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '代金券类型名称',
`money` decimal(20,4) NOT NULL COMMENT '面额',
`use_limit` int(11) NOT NULL COMMENT '可用次数, 0表示无限次数',
`begin_time` int(11) NOT NULL COMMENT '有效期开始时间',
`end_time` int(11) NOT NULL COMMENT '有效期结束时间',
`gen_count` int(11) NOT NULL COMMENT '已发放的数量',
`send_type` tinyint(1) NOT NULL COMMENT '发放方式 0:管理员手动发放 1:会员积分兑换 2:序列号兑换',
`exchange_score` int(11) NOT NULL COMMENT '兑换所需的积分',
`exchange_limit` int(11) NOT NULL COMMENT '每个会员限兑换的数量',
`exchange_sn` varchar(20) DEFAULT NULL COMMENT '红包兑换的序列号',
`share_url` varchar(255) NOT NULL COMMENT '分享连接',
`memo` varchar(255) NOT NULL COMMENT '红包备注',
`tpl` varchar(255) NOT NULL COMMENT '红包模版',
`total_limit`  int(11) NOT NULL COMMENT '发放总量限制',
PRIMARY KEY (`id`),
UNIQUE KEY `exchange_sn` (`exchange_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='代金券类型表';
DROP TABLE IF EXISTS `%DB_PREFIX%event`;
CREATE TABLE `%DB_PREFIX%event` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '活动名称',
`icon` varchar(255) NOT NULL COMMENT '活动的小图',
`event_begin_time` int(11) NOT NULL COMMENT '活动开始时间',
`event_end_time` int(11) NOT NULL COMMENT '活动结束时间',
`submit_begin_time` int(11) NOT NULL COMMENT '活动报名开始时间',
`submit_end_time` int(11) NOT NULL COMMENT '活动报名结束时间',
`user_id` int(11) NOT NULL,
`content` text NOT NULL COMMENT '活动内容',
`cate_id` int(11) NOT NULL COMMENT '所属的活动分类ID',
`city_id` int(11) NOT NULL COMMENT '所属的城市 ID',
`address` varchar(255) NOT NULL COMMENT '活动地址',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`locate_match` text NOT NULL COMMENT '地址的全文索引unicode',
`locate_match_row` text NOT NULL COMMENT '地址的全文索引查询用',
`cate_match` text NOT NULL COMMENT '分类的全文索引unicode',
`cate_match_row` text NOT NULL COMMENT '分类的全文索引查询用',
`name_match` text NOT NULL COMMENT '名称的全文索引unicode',
`name_match_row` text NOT NULL COMMENT '名称的全文索引查询用',
`submit_count` int(11) NOT NULL COMMENT '报名总数',
`reply_count` int(11) NOT NULL COMMENT '回贴数量',
`brief` text NOT NULL COMMENT '简介',
`sort` int(11) NOT NULL COMMENT '排序',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`click_count` int(11) NOT NULL COMMENT '点击数',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐到首页',
`supplier_id` int(11) NOT NULL,
`publish_wait` tinyint(1) NOT NULL COMMENT '商家提交 1:待审核 0：通过',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`dp_count` int(11) NOT NULL,
`avg_point` float(14,4) NOT NULL,
`total_point` int(11) NOT NULL,
`total_count` int(11) NOT NULL COMMENT '活动名额',
`is_auto_verify` tinyint(1) NOT NULL COMMENT '自动审核，审核报名结果后不可以再修改',
`return_score` int(11) NOT NULL,
`return_point` int(11) NOT NULL,
`return_money` decimal(20,4) NOT NULL,
`score_limit` int(11) NOT NULL,
`point_limit` int(11) NOT NULL,
PRIMARY KEY (`id`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `cate_match` (`cate_match`),
FULLTEXT KEY `all_match` (`locate_match`,`cate_match`,`name_match`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商家活动表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_area_link`;
CREATE TABLE `%DB_PREFIX%event_area_link` (
`event_id` int(11) NOT NULL,
`area_id` int(11) NOT NULL,
PRIMARY KEY (`event_id`,`area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动的商圈地区关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_biz_submit`;
CREATE TABLE `%DB_PREFIX%event_biz_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '活动名称',
`icon` varchar(255) NOT NULL COMMENT '活动的小图',
`event_begin_time` int(11) NOT NULL COMMENT '活动开始时间',
`event_end_time` int(11) NOT NULL COMMENT '活动结束时间',
`submit_begin_time` int(11) NOT NULL COMMENT '活动报名开始时间',
`submit_end_time` int(11) NOT NULL COMMENT '活动报名结束时间',
`user_id` int(11) NOT NULL,
`content` text NOT NULL COMMENT '活动内容',
`cate_id` int(11) NOT NULL COMMENT '所属的活动分类ID',
`city_id` int(11) NOT NULL COMMENT '所属的城市 ID',
`address` varchar(255) NOT NULL COMMENT '活动地址',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`locate_match` text NOT NULL COMMENT '地址的全文索引unicode',
`locate_match_row` text NOT NULL COMMENT '地址的全文索引查询用',
`cate_match` text NOT NULL COMMENT '分类的全文索引unicode',
`cate_match_row` text NOT NULL COMMENT '分类的全文索引查询用',
`name_match` text NOT NULL COMMENT '名称的全文索引unicode',
`name_match_row` text NOT NULL COMMENT '名称的全文索引查询用',
`submit_count` int(11) NOT NULL COMMENT '报名总数',
`reply_count` int(11) NOT NULL COMMENT '回贴数量',
`brief` text NOT NULL COMMENT '简介',
`sort` int(11) NOT NULL COMMENT '排序',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`click_count` int(11) NOT NULL COMMENT '点击数',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐到首页',
`supplier_id` int(11) NOT NULL,
`publish_wait` tinyint(1) NOT NULL COMMENT '商家提交 1:待审核 0：通过',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`dp_count` int(11) NOT NULL,
`avg_point` float(14,4) NOT NULL,
`total_point` int(11) NOT NULL,
`total_count` int(11) NOT NULL COMMENT '活动名额',
`is_auto_verify` tinyint(1) NOT NULL COMMENT '自动审核，审核报名结果后不可以再修改',
`return_score` int(11) NOT NULL,
`return_point` int(11) NOT NULL,
`return_money` decimal(20,4) NOT NULL,
`score_limit` int(11) NOT NULL,
`point_limit` int(11) NOT NULL,
`cache_event_area_link` text NOT NULL COMMENT '序列化缓存地区列表',
`cache_event_location_link` text NOT NULL COMMENT '序列化缓存支持的门店',
`cache_event_field` text NOT NULL COMMENT '序列化缓存报名项配置',
`account_id` int(11) NOT NULL COMMENT '提交数据的商户帐号关联ID',
`event_id` int(11) NOT NULL COMMENT '关联活动主表的数据ID',
`biz_apply_status` tinyint(1) NOT NULL COMMENT '商户申请状态 1.新品上架申请 2:修改 3:下架',
`admin_check_status` tinyint(1) NOT NULL COMMENT '管理员审核状态 0 待审核 1 通过 2 拒绝',
PRIMARY KEY (`id`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `cate_match` (`cate_match`),
FULLTEXT KEY `all_match` (`locate_match`,`cate_match`,`name_match`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='商家中心发布活动临时表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_cate`;
CREATE TABLE `%DB_PREFIX%event_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
`sort` int(11) NOT NULL,
`count` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='活动的分类表';
INSERT INTO `%DB_PREFIX%event_cate` VALUES ('1','电影','1','1','0');
INSERT INTO `%DB_PREFIX%event_cate` VALUES ('2','讲座','1','2','0');
INSERT INTO `%DB_PREFIX%event_cate` VALUES ('3','试吃','1','3','1');
INSERT INTO `%DB_PREFIX%event_cate` VALUES ('4','交友','1','4','0');
INSERT INTO `%DB_PREFIX%event_cate` VALUES ('5','旅游','1','5','0');
DROP TABLE IF EXISTS `%DB_PREFIX%event_dp_point_result`;
CREATE TABLE `%DB_PREFIX%event_dp_point_result` (
`group_id` int(11) NOT NULL COMMENT '分组ID',
`point` int(11) NOT NULL COMMENT '分数',
`event_id` int(11) NOT NULL COMMENT '活动ID',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
KEY `group_id` (`group_id`) USING BTREE,
KEY `youhui_id` (`event_id`) USING BTREE,
KEY `dp_id` (`dp_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个活动，每条点评针对每个评分分组的点评评分';
DROP TABLE IF EXISTS `%DB_PREFIX%event_dp_tag_result`;
CREATE TABLE `%DB_PREFIX%event_dp_tag_result` (
`tags` varchar(255) NOT NULL COMMENT '标签列表 空格分隔',
`dp_id` int(11) NOT NULL COMMENT '关联的点评ID',
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`event_id` int(11) NOT NULL COMMENT '活动ID',
KEY `dp_id` (`dp_id`),
KEY `group_id` (`group_id`),
KEY `youhui_id` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动按预定义的分组打标签的结果表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_field`;
CREATE TABLE `%DB_PREFIX%event_field` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`event_id` int(11) NOT NULL,
`field_show_name` varchar(255) NOT NULL COMMENT '报名选项显示名称',
`field_type` tinyint(1) NOT NULL COMMENT '报名项报名方式 0:手动输入 1:预选下拉',
`value_scope` varchar(255) NOT NULL COMMENT '下拉的预选范围 用空格分隔',
`sort` int(11) NOT NULL COMMENT '排序 由小到大，自动生成',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='活动报名选项';
DROP TABLE IF EXISTS `%DB_PREFIX%event_location_link`;
CREATE TABLE `%DB_PREFIX%event_location_link` (
`event_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY (`event_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动支持的门店关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_sc`;
CREATE TABLE `%DB_PREFIX%event_sc` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uid` int(11) NOT NULL,
`event_id` int(11) NOT NULL,
`add_time` int(11) NOT NULL COMMENT '收藏时间',
PRIMARY KEY (`id`),
UNIQUE KEY `inx_youhui_sc` (`uid`,`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动收藏';
DROP TABLE IF EXISTS `%DB_PREFIX%event_submit`;
CREATE TABLE `%DB_PREFIX%event_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`create_time` int(11) NOT NULL,
`event_id` int(11) NOT NULL,
`event_begin_time` int(11) NOT NULL COMMENT '活动开始时间，报名时同步自event表。',
`event_end_time` int(11) NOT NULL COMMENT '活动结束时间',
`dp_id` int(11) NOT NULL COMMENT '为已报名的活动点评的ID',
`sn` varchar(255) DEFAULT NULL,
`location_id` int(11) NOT NULL COMMENT '验证的门店ID',
`confirm_id` int(11) NOT NULL COMMENT '操作的商家账户ID',
`confirm_time` int(11) NOT NULL,
`is_verify` tinyint(1) NOT NULL COMMENT '是否已审核，已审核才扣名额',
`return_score` int(11) NOT NULL,
`return_point` int(11) NOT NULL,
`sms_count` int(11) NOT NULL COMMENT '报名结果短信发送次数',
`mail_count` int(11) NOT NULL COMMENT '报名结果邮件发送次数',
`return_money` decimal(20,4) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `sn` (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='活动报名表';
DROP TABLE IF EXISTS `%DB_PREFIX%event_submit_field`;
CREATE TABLE `%DB_PREFIX%event_submit_field` (
`submit_id` int(11) NOT NULL COMMENT '报名的主表ID',
`field_id` int(11) NOT NULL COMMENT '选项ID',
`result` varchar(255) NOT NULL COMMENT '报名结果',
`event_id` int(11) NOT NULL,
PRIMARY KEY (`submit_id`,`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动报名，自定义报名项的报名结果';
DROP TABLE IF EXISTS `%DB_PREFIX%express`;
CREATE TABLE `%DB_PREFIX%express` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`class_name` varchar(255) NOT NULL COMMENT '快递接口类名',
`name` varchar(255) NOT NULL COMMENT '快递接口名称',
`print_tmpl` text NOT NULL COMMENT '快递单打印模板',
`is_effect` tinyint(1) NOT NULL,
`config` text NOT NULL COMMENT '相关的配置(序列化的结果)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='快递接口配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%expression`;
CREATE TABLE `%DB_PREFIX%expression` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`type` varchar(255) NOT NULL DEFAULT 'tusiji',
`emotion` varchar(255) NOT NULL,
`filename` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COMMENT='表情配置表';
INSERT INTO `%DB_PREFIX%expression` VALUES ('19','傲慢','qq','[傲慢]','aoman.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('20','白眼','qq','[白眼]','baiyan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('21','鄙视','qq','[鄙视]','bishi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('22','闭嘴','qq','[闭嘴]','bizui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('23','擦汗','qq','[擦汗]','cahan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('24','菜刀','qq','[菜刀]','caidao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('25','差劲','qq','[差劲]','chajin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('26','欢庆','qq','[欢庆]','cheer.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('27','虫子','qq','[虫子]','chong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('28','呲牙','qq','[呲牙]','ciya.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('29','捶打','qq','[捶打]','da.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('30','大便','qq','[大便]','dabian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('31','大兵','qq','[大兵]','dabing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('32','大叫','qq','[大叫]','dajiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('33','大哭','qq','[大哭]','daku.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('34','蛋糕','qq','[蛋糕]','dangao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('35','发怒','qq','[发怒]','fanu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('36','刀','qq','[刀]','dao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('37','得意','qq','[得意]','deyi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('38','凋谢','qq','[凋谢]','diaoxie.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('39','饿','qq','[饿]','er.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('40','发呆','qq','[发呆]','fadai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('41','发抖','qq','[发抖]','fadou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('42','饭','qq','[饭]','fan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('43','飞吻','qq','[飞吻]','feiwen.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('44','奋斗','qq','[奋斗]','fendou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('45','尴尬','qq','[尴尬]','gangga.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('46','给力','qq','[给力]','geili.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('47','勾引','qq','[勾引]','gouyin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('48','鼓掌','qq','[鼓掌]','guzhang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('49','哈哈','qq','[哈哈]','haha.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('50','害羞','qq','[害羞]','haixiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('51','哈欠','qq','[哈欠]','haqian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('52','花','qq','[花]','hua.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('53','坏笑','qq','[坏笑]','huaixiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('54','挥手','qq','[挥手]','huishou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('55','回头','qq','[回头]','huitou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('56','激动','qq','[激动]','jidong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('57','惊恐','qq','[惊恐]','jingkong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('58','惊讶','qq','[惊讶]','jingya.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('59','咖啡','qq','[咖啡]','kafei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('60','可爱','qq','[可爱]','keai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('61','可怜','qq','[可怜]','kelian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('62','磕头','qq','[磕头]','ketou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('63','示爱','qq','[示爱]','kiss.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('64','酷','qq','[酷]','ku.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('65','难过','qq','[难过]','kuaikule.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('66','骷髅','qq','[骷髅]','kulou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('67','困','qq','[困]','kun.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('68','篮球','qq','[篮球]','lanqiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('69','冷汗','qq','[冷汗]','lenghan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('70','流汗','qq','[流汗]','liuhan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('71','流泪','qq','[流泪]','liulei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('72','礼物','qq','[礼物]','liwu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('73','爱心','qq','[爱心]','love.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('74','骂人','qq','[骂人]','ma.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('75','不开心','qq','[不开心]','nanguo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('76','不好','qq','[不好]','no.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('77','很好','qq','[很好]','ok.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('78','佩服','qq','[佩服]','peifu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('79','啤酒','qq','[啤酒]','pijiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('80','乒乓','qq','[乒乓]','pingpang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('81','撇嘴','qq','[撇嘴]','pizui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('82','强','qq','[强]','qiang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('83','亲亲','qq','[亲亲]','qinqin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('84','出丑','qq','[出丑]','qioudale.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('85','足球','qq','[足球]','qiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('86','拳头','qq','[拳头]','quantou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('87','弱','qq','[弱]','ruo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('88','色','qq','[色]','se.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('89','闪电','qq','[闪电]','shandian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('90','胜利','qq','[胜利]','shengli.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('91','衰','qq','[衰]','shuai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('92','睡觉','qq','[睡觉]','shuijiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('93','太阳','qq','[太阳]','taiyang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('96','啊','tusiji','[啊]','aa.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('97','暗爽','tusiji','[暗爽]','anshuang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('98','byebye','tusiji','[byebye]','baibai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('99','不行','tusiji','[不行]','buxing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('100','戳眼','tusiji','[戳眼]','chuoyan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('101','很得意','tusiji','[很得意]','deyi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('102','顶','tusiji','[顶]','ding.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('103','抖抖','tusiji','[抖抖]','douxiong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('104','哼','tusiji','[哼]','heng.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('105','挥汗','tusiji','[挥汗]','huihan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('106','昏迷','tusiji','[昏迷]','hunmi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('107','互拍','tusiji','[互拍]','hupai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('108','瞌睡','tusiji','[瞌睡]','keshui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('109','笼子','tusiji','[笼子]','longzi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('110','听歌','tusiji','[听歌]','music.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('111','奶瓶','tusiji','[奶瓶]','naiping.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('112','扭背','tusiji','[扭背]','niubei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('113','拍砖','tusiji','[拍砖]','paizhuan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('114','飘过','tusiji','[飘过]','piaoguo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('115','揉脸','tusiji','[揉脸]','roulian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('116','闪闪','tusiji','[闪闪]','shanshan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('117','生日','tusiji','[生日]','shengri.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('118','摊手','tusiji','[摊手]','tanshou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('119','躺坐','tusiji','[躺坐]','tanzuo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('120','歪头','tusiji','[歪头]','waitou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('121','我踢','tusiji','[我踢]','woti.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('122','无聊','tusiji','[无聊]','wuliao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('123','醒醒','tusiji','[醒醒]','xingxing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('124','睡了','tusiji','[睡了]','xixishui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('125','旋转','tusiji','[旋转]','xuanzhuan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('126','摇晃','tusiji','[摇晃]','yaohuang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('127','耶','tusiji','[耶]','yeah.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('128','郁闷','tusiji','[郁闷]','yumen.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('129','晕厥','tusiji','[晕厥]','yunjue.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('130','砸','tusiji','[砸]','za.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('131','震荡','tusiji','[震荡]','zhendang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('132','撞墙','tusiji','[撞墙]','zhuangqiang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('133','转头','tusiji','[转头]','zhuantou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('134','抓墙','tusiji','[抓墙]','zhuaqiang.gif');
DROP TABLE IF EXISTS `%DB_PREFIX%fetch_topic`;
CREATE TABLE `%DB_PREFIX%fetch_topic` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '接口名称',
`show_name` varchar(255) NOT NULL COMMENT '接口显示的名称',
`class_name` varchar(255) NOT NULL COMMENT '类名',
`icon` varchar(255) NOT NULL COMMENT '图标',
`config` text NOT NULL COMMENT '配置信息',
`is_effect` tinyint(1) NOT NULL,
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='分享采集接口配置表';
INSERT INTO `%DB_PREFIX%fetch_topic` VALUES ('1','方维oso内部数据分享接口','站内分享','Fanwe','','N;','1','1');
DROP TABLE IF EXISTS `%DB_PREFIX%filter`;
CREATE TABLE `%DB_PREFIX%filter` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '关键词',
`filter_group_id` int(11) NOT NULL COMMENT '商城商品筛选分组ID',
PRIMARY KEY (`id`),
KEY `filter_name_idx` (`name`),
KEY `filter_group_id` (`filter_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='商品筛选关键词表';
DROP TABLE IF EXISTS `%DB_PREFIX%filter_group`;
CREATE TABLE `%DB_PREFIX%filter_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '筛选分组名称',
`cate_id` int(11) NOT NULL COMMENT '所属商城分类ID',
`sort` int(11) NOT NULL,
`is_effect` tinyint(1) NOT NULL COMMENT '是否生效用于检索分组显示于分类页',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='商城商品筛选分组配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%flower_log`;
CREATE TABLE `%DB_PREFIX%flower_log` (
`user_id` int(11) NOT NULL COMMENT '用户ID',
`type` enum('good_count','bad_count') NOT NULL COMMENT 'good_count表示鲜花',
`rec_id` int(11) NOT NULL COMMENT '相关的ID，如点评的ID，图片ID',
`rec_module` enum('image','dp') NOT NULL,
`memo` varchar(20) NOT NULL COMMENT '投票的文字显示',
`create_time` int(11) NOT NULL COMMENT '投票的时间',
PRIMARY KEY (`user_id`,`rec_id`,`rec_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评的鲜花投标记录表';
DROP TABLE IF EXISTS `%DB_PREFIX%free_delivery`;
CREATE TABLE `%DB_PREFIX%free_delivery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`delivery_id` int(11) NOT NULL COMMENT '配送方式ID',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`free_count` int(11) NOT NULL COMMENT '免运费的件数',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='每个商品针对每个配置方式设置的免运费规则表';
DROP TABLE IF EXISTS `%DB_PREFIX%goods_type`;
CREATE TABLE `%DB_PREFIX%goods_type` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`supplier_id` int(11) NOT NULL COMMENT '商户编号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='商品的类型(属性规格的分组标准)';
DROP TABLE IF EXISTS `%DB_PREFIX%goods_type_attr`;
CREATE TABLE `%DB_PREFIX%goods_type_attr` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '属性名称',
`input_type` tinyint(1) NOT NULL COMMENT '输入的方式 0手动输入 1预选下拉',
`preset_value` text NOT NULL COMMENT '预选下拉时的预设值，半角逗号分隔',
`goods_type_id` int(11) NOT NULL COMMENT '商品类型ID',
`supplier_id` int(11) NOT NULL COMMENT '商户编号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='每个商品类型的属性预设表';
DROP TABLE IF EXISTS `%DB_PREFIX%images_group`;
CREATE TABLE `%DB_PREFIX%images_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商家图片分组表';
DROP TABLE IF EXISTS `%DB_PREFIX%images_group_link`;
CREATE TABLE `%DB_PREFIX%images_group_link` (
`images_group_id` int(11) NOT NULL COMMENT '图片分组ID',
`category_id` int(11) NOT NULL COMMENT '生活服务大分类ID',
KEY `images_group_id` (`images_group_id`) USING BTREE,
KEY `category_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家图片分组与生活服务分类的关联表(属于某个分类的商家图片拥有哪些图片分组的配置)';
DROP TABLE IF EXISTS `%DB_PREFIX%link`;
CREATE TABLE `%DB_PREFIX%link` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '友情链接显示名称',
`group_id` int(11) NOT NULL COMMENT '友情链接分组ID',
`url` varchar(255) NOT NULL COMMENT '链接地址',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`img` varchar(255) NOT NULL COMMENT '链接图片',
`description` text NOT NULL COMMENT '描述说明',
`count` int(11) NOT NULL COMMENT '点击量',
`show_index` tinyint(1) NOT NULL COMMENT '是否显示到首页底部 0:否 1:是',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='友情链接表';
INSERT INTO `%DB_PREFIX%link` VALUES ('3','方维o2o商业系统','6','http://www.fanwe.com','1','1','','方维o2o商业系统','0','1');
DROP TABLE IF EXISTS `%DB_PREFIX%link_group`;
CREATE TABLE `%DB_PREFIX%link_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '友情链接分组名称',
`sort` tinyint(1) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='友情链接分组表';
INSERT INTO `%DB_PREFIX%link_group` VALUES ('6','友情链接','1','1');
DROP TABLE IF EXISTS `%DB_PREFIX%log`;
CREATE TABLE `%DB_PREFIX%log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL COMMENT '日志描述内容',
`log_time` int(11) NOT NULL COMMENT '发生时间',
`log_admin` int(11) NOT NULL COMMENT '操作的管理员ID',
`log_ip` varchar(255) NOT NULL COMMENT '操作者IP',
`log_status` tinyint(1) NOT NULL COMMENT '操作结果 1:操作成功 0:操作失败',
`module` varchar(255) NOT NULL COMMENT '操作的模块module',
`action` varchar(255) NOT NULL COMMENT '操作的命令action',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2310 DEFAULT CHARSET=utf8 COMMENT='后台操作日志表';
DROP TABLE IF EXISTS `%DB_PREFIX%lottery`;
CREATE TABLE `%DB_PREFIX%lottery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`lottery_sn` varchar(255) NOT NULL COMMENT '抽奖券序列号（顺序生成）',
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`user_id` int(11) NOT NULL COMMENT '抽奖券所属会员ID',
`mobile` varchar(255) NOT NULL COMMENT '参与抽奖的手机号',
`create_time` int(11) NOT NULL COMMENT '抽奖时间',
`buyer_id` int(11) NOT NULL COMMENT '购买人ID(产生抽奖行为的会员ID，当抽奖券由被推荐人购买时该 ID与user_id不相等)',
`sms_count` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='抽奖券表';
DROP TABLE IF EXISTS `%DB_PREFIX%m_adv`;
CREATE TABLE `%DB_PREFIX%m_adv` (
`id` smallint(6) NOT NULL AUTO_INCREMENT,
`name` varchar(100) DEFAULT '' COMMENT '广告名称',
`img` varchar(255) DEFAULT '' COMMENT '广告图片',
`mobile_type` tinyint(1) DEFAULT '0' COMMENT '手机类型0:ios/android; 1:wap',
`type` tinyint(1) DEFAULT '0' COMMENT '1分类标签广告\r\n2URL广告\r\n3分类排行\r\n4达人页\r\n5搜索页\r\n6拍照\r\n7热门\r\n8分享详细\r\n9团购列表\r\n10商品列表\r\n11活动列表\r\n12优惠列表\r\n13代金券列表\r\n14团购明细\r\n15商品明细\r\n16活动明细\r\n17优惠明细\r\n18代金券明细\r\n19关于我们\r\n20优惠券主页面\r\n21公告列表			',
`position` tinyint(1) NOT NULL COMMENT '显示的位置 0:首页 1:启动页 2:专题位',
`data` text COMMENT '配置的序列化数据（根据不同的type存放不同的结果）',
`sort` smallint(5) DEFAULT '10' COMMENT '排序',
`status` tinyint(1) DEFAULT '1' COMMENT '状态 0:无效1:有效',
`city_id` int(11) DEFAULT '0' COMMENT '所属城市',
`ctl` varchar(255) DEFAULT NULL,
`zt_id` int(11) NOT NULL COMMENT '手机端专题组的ID',
`zt_position` varchar(255) NOT NULL COMMENT '专题模板的位置显示(广告位ID)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='手机端广告配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%m_config`;
CREATE TABLE `%DB_PREFIX%m_config` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`code` varchar(255) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`val` text,
`type` tinyint(1) NOT NULL,
`group_name` varchar(50) NOT NULL DEFAULT '基础配置' COMMENT '分组显示',
`sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COMMENT='手机端的后台配置表';
INSERT INTO `%DB_PREFIX%m_config` VALUES ('19','index_logo','首页logo','./public/attachment/201202/04/16/4f2ce8336d784.png','2','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('3','has_ecv','有优惠券','1','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('6','has_message','有留言框','1','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('7','has_region','有配送地区选择项','1','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('8','region_version','配送地区版本','1','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('9','only_one_delivery','只有一个配送地区','1','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('10','kf_phone','客服电话','400-000-0000','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('11','kf_email','客服邮箱','qq@fanwe.com','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('16','page_size','分页大小','10','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('18','program_title','程序标题名称','方维O2O','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('22','sina_app_key','新浪App Key','','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('23','sina_app_secret','新浪App Secret','','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('24','sina_bind_url','新浪回调地址','http://sns.whalecloud.com/sina2/callback','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('68','wx_app_secret','微信(开放)appSecret','','0','基础配置','19');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('67','wx_app_key','微信(开放)AppID','','0','基础配置','18');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('61','ios_biz_forced_upgrade','商家ios是否强制升级(0:否;1:是)','0','0','商家手机端升级设置','12');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('62','android_biz_version','商家android版本号(yyyymmddnn)','','0','商家手机端升级设置','13');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('63','android_biz_filename','商家android下载包名','http://o2o.fanwe.net/o2ofanwe_biz.apk','0','商家手机端升级设置','14');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('65','android_biz_forced_upgrade','商家android是否强制升级(0:否;1:是)','0','0','商家手机端升级设置','16');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('64','android_biz_upgrade','商家android版本升级内容','商家android升级测试','3','商家手机端升级设置','15');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('50','ios_version','ios版本号(yyyymmddnn)','','0','手机端升级设置','1');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('51','ios_down_url','ios下载地址(appstore连接地址)','http://o2o.fanwe.net/o2ofanwe_app.ipa','0','手机端升级设置','2');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('52','ios_upgrade','ios版本升级内容','ios升级测试','3','手机端升级设置','3');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('53','ios_forced_upgrade','ios是否强制升级(0:否;1:是)','0','0','手机端升级设置','4');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('54','android_version','android版本号(yyyymmddnn)','2015021001','0','手机端升级设置','5');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('55','android_filename','android下载包名','http://o2o.fanwe.net/o2ofanwe_app.apk','0','手机端升级设置','6');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('56','android_upgrade','android版本升级内容','android升级测试','3','手机端升级设置','7');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('57','android_forced_upgrade','android是否强制升级(0:否;1:是)','0','0','手机端升级设置','8');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('58','ios_biz_version','商家ios版本号(yyyymmddnn)','','0','商家手机端升级设置','9');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('59','ios_biz_down_url','商家ios下载地址(appstore连接地址)','http://o2o.fanwe.net/o2ofanwe_biz.ipa','0','商家手机端升级设置','10');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('60','ios_biz_upgrade','商家ios版本升级内容','商家ios升级测试','3','商家手机端升级设置','11');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('29','qq_app_secret','腾讯开放平台APP KEY','','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('28','qq_app_key','腾讯开放平台APP ID','','0','基础配置','0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('69','about_info','关于我们(文章ID)','','0','基础配置','20');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('72','wx_appid','微信(公众)APPID','','0','基础配置','67');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('73','wx_secrit','微信(公众)SECRIT','','0','基础配置','68');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('74','android_biz_master_secret','商家android推送友盟AppMasterSecret','','0','手机推送配置','18');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('75','android_biz_app_key','商家android推送友盟AppKey','','0','手机推送配置','17');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('76','ios_biz_app_key','商家ios推送友盟AppKey','','0','手机推送配置','19');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('77','ios_biz_master_secret','商家ios推送友盟AppMasterSecret','','0','手机推送配置','20');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('78','android_master_secret','android推送友盟AppMasterSecret','','0','手机推送配置','24');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('79','android_app_key','android推送友盟AppKey','','0','手机推送配置','23');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('80','ios_app_key','ios推送友盟AppKey','','0','手机推送配置','25');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('81','ios_master_secret','ios推送友盟AppMasterSecret','','0','手机推送配置','26');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('82','close_index_tuan','是否关闭首页团购','0','4','基础配置','101');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('83','close_index_shop','是否关闭首页商品','0','4','基础配置','102');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('84','close_index_youhui','是否关闭首页优惠','0','4','基础配置','103');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('85','close_index_event','是否关闭首页活动','0','4','基础配置','104');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('86','close_index_supplier','是否关闭首页商户','0','4','基础配置','105');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('87','close_index_cate','是否关闭首页分类位','0','4','基础配置','106');
DROP TABLE IF EXISTS `%DB_PREFIX%m_config_list`;
CREATE TABLE `%DB_PREFIX%m_config_list` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`pay_id` varchar(50) DEFAULT NULL,
`group` int(10) DEFAULT NULL,
`code` varchar(50) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`has_calc` int(1) DEFAULT NULL,
`money` float(10,2) DEFAULT NULL,
`is_verify` int(1) DEFAULT '0' COMMENT '0:无效；1:有效',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='手机端支付时用到一些额外配置，包括支付接口等';
DROP TABLE IF EXISTS `%DB_PREFIX%m_index`;
CREATE TABLE `%DB_PREFIX%m_index` (
`id` mediumint(6) NOT NULL AUTO_INCREMENT,
`name` varchar(100) DEFAULT '',
`vice_name` varchar(100) DEFAULT NULL,
`desc` varchar(100) DEFAULT '',
`img` varchar(255) DEFAULT '',
`type` tinyint(1) DEFAULT '0' COMMENT '1.标签集,2.url地址,3.分类排行,4.最亮达人,5.搜索发现,6.一起拍,7.热门单品排行,8.直接显示某个分享',
`data` text,
`sort` smallint(5) DEFAULT '10',
`status` tinyint(1) DEFAULT '1',
`is_hot` tinyint(1) DEFAULT '0',
`is_new` tinyint(1) DEFAULT '0',
`city_id` int(11) DEFAULT '0',
`mobile_type` tinyint(1) DEFAULT '0' COMMENT '手机类型0:ios/android; 1:wap',
`ctl` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `%DB_PREFIX%m_notice`;
CREATE TABLE `%DB_PREFIX%m_notice` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '公告标题',
`content` text NOT NULL COMMENT '文章内容',
`create_time` int(11) NOT NULL COMMENT '发布时间',
`sort` int(11) NOT NULL COMMENT '排序',
`is_effect` tinyint(1) NOT NULL COMMENT '有效',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机端公告列表';
DROP TABLE IF EXISTS `%DB_PREFIX%m_zt`;
CREATE TABLE `%DB_PREFIX%m_zt` (
`id` smallint(6) NOT NULL AUTO_INCREMENT,
`name` varchar(100) DEFAULT '' COMMENT '名称',
`mobile_type` tinyint(1) DEFAULT '0' COMMENT '手机类型0:ios/android; 1:wap',
`type` tinyint(1) DEFAULT '0' COMMENT '1分类标签广告\r\n2URL广告\r\n3分类排行\r\n4达人页\r\n5搜索页\r\n6拍照\r\n7热门\r\n8分享详细\r\n9团购列表\r\n10商品列表\r\n11活动列表\r\n12优惠列表\r\n13代金券列表\r\n14团购明细\r\n15商品明细\r\n16活动明细\r\n17优惠明细\r\n18代金券明细\r\n19关于我们\r\n20优惠券主页面\r\n21公告列表			',
`data` text COMMENT '配置的序列化数据（根据不同的type存放不同的结果）',
`sort` smallint(5) DEFAULT '10' COMMENT '排序',
`status` tinyint(1) DEFAULT '1' COMMENT '状态 0:无效1:有效',
`city_id` int(11) DEFAULT '0' COMMENT '所属城市',
`ctl` varchar(255) DEFAULT NULL,
`zt_moban` varchar(255) NOT NULL COMMENT '专题模板文件路径',
`zt_title` varchar(255) NOT NULL COMMENT '专题显示的标题',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='手机端首页专题位';
DROP TABLE IF EXISTS `%DB_PREFIX%mail_list`;
CREATE TABLE `%DB_PREFIX%mail_list` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`mail_address` varchar(255) NOT NULL COMMENT '邮件的地址',
`city_id` int(11) NOT NULL COMMENT '订阅的城市ID，用于按地区群发时匹配',
`code` varchar(255) NOT NULL COMMENT '弃用',
`is_effect` tinyint(1) NOT NULL,
PRIMARY KEY (`id`),
KEY `mail_address_idx` (`mail_address`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='邮件订阅表';
DROP TABLE IF EXISTS `%DB_PREFIX%mail_server`;
CREATE TABLE `%DB_PREFIX%mail_server` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`smtp_server` varchar(255) NOT NULL COMMENT 'smtp服务器地址IP或域名',
`smtp_name` varchar(255) NOT NULL COMMENT 'smtp发件帐号名',
`smtp_pwd` varchar(255) NOT NULL COMMENT 'smtp密码',
`is_ssl` tinyint(1) NOT NULL COMMENT '是否ssl加密连接（参考具体smtp服务商的要求，如gmail要求ssl连接）',
`smtp_port` varchar(255) NOT NULL COMMENT 'smtp端口',
`use_limit` int(11) NOT NULL COMMENT '可用次数为0时表示无限次数使用, 次数满后轮到下一个配置的邮件服务器发件，直到没有可发的邮件服务器为止',
`is_reset` tinyint(1) NOT NULL COMMENT '是否自动清零，1:次数达到上限后自动清零，等待下一个轮回继续使用该邮箱发送',
`is_effect` tinyint(1) NOT NULL,
`total_use` int(11) NOT NULL COMMENT '当前已用次数',
`is_verify` tinyint(1) NOT NULL COMMENT '是否需要身份验证,通常为1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='发件用邮件服务器列表';
DROP TABLE IF EXISTS `%DB_PREFIX%medal`;
CREATE TABLE `%DB_PREFIX%medal` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`class_name` varchar(255) NOT NULL COMMENT '勋章接口名',
`name` varchar(255) NOT NULL COMMENT '显示名称',
`description` text NOT NULL COMMENT '勋章的描述',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`config` text NOT NULL COMMENT '不同勋章接口功能的配置信息',
`icon` varchar(255) NOT NULL COMMENT '勋章图片',
`image` varchar(255) NOT NULL COMMENT '备用',
`route` text NOT NULL COMMENT '勋章获取规则的描述文字',
`allow_check` tinyint(1) NOT NULL COMMENT '是否会被系统回收 0:不会 1:会',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='系统内勋章体系配置表';
INSERT INTO `%DB_PREFIX%medal` VALUES ('1','Groupuser','组长勋章','点亮表示您为组长','1','N;','./public/attachment/201203/17/15/4f6438e27aa65.png','','申请成为小组组长即可点亮该勋章','1');
INSERT INTO `%DB_PREFIX%medal` VALUES ('2','Keepsign','忠实网友勋章','点亮为忠实的网友会员','1','a:1:{s:9:\"day_count\";s:2:\"10\";}','./public/attachment/201203/17/15/4f6438f0af2c6.png','','连续签到10天以上将获得该勋章','1');
INSERT INTO `%DB_PREFIX%medal` VALUES ('3','Newuser','新手勋章','点亮您为新手，让更多的朋友找到你','1','N;','./public/attachment/201203/17/15/4f643902cd067.png','','完善用户的所有资料，即可获取该勋章','1');
INSERT INTO `%DB_PREFIX%medal` VALUES ('4','Sinabind','新浪微博勋章','新浪微博认证勋章，点亮为新浪微博用户','1','N;','./public/attachment/201203/17/15/4f64391478be2.png','','绑定新浪微博即可获得该勋章','0');
INSERT INTO `%DB_PREFIX%medal` VALUES ('5','Tencentbind','腾讯微博勋章','腾讯微博认证勋章，点亮为腾讯微博用户','1','N;','./public/attachment/201203/17/15/4f6439210f17b.png','','绑定腾讯微博即可获得该勋章','0');
DROP TABLE IF EXISTS `%DB_PREFIX%message`;
CREATE TABLE `%DB_PREFIX%message` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL COMMENT '留言标题',
`content` text NOT NULL COMMENT '留言内容',
`create_time` int(11) NOT NULL COMMENT '留言时间',
`update_time` int(11) NOT NULL COMMENT '回复时间',
`admin_reply` text NOT NULL COMMENT '管理员回复内容',
`admin_id` int(11) NOT NULL COMMENT '回复管理员ID',
`rel_table` varchar(255) NOT NULL COMMENT '相关的数据表/模块（如活动留言event，商品留言deal）',
`rel_id` int(11) NOT NULL COMMENT '相关留言的数据ID',
`user_id` int(11) NOT NULL COMMENT '留言会员ID',
`pid` int(11) NOT NULL COMMENT '弃用',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识（自动生效的留言自动为1），审核生效的留言为0',
`city_id` int(11) NOT NULL COMMENT '提交商务合作留言的城市ID（基本弃用，商务合作由商家入驻取代）',
`is_buy` tinyint(1) NOT NULL COMMENT '是否为消费后留言（即点评） ',
`contact_name` varchar(255) NOT NULL COMMENT '商务合作提交时的联系人姓名',
`contact` varchar(255) NOT NULL COMMENT '商务合作提交时的联系方式',
`point` int(11) NOT NULL COMMENT '部份留言功能需要的评分',
`is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:商家未阅读;1:商家已阅读',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COMMENT='留言表';
DROP TABLE IF EXISTS `%DB_PREFIX%message_type`;
CREATE TABLE `%DB_PREFIX%message_type` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`type_name` varchar(255) NOT NULL COMMENT '预设的代码用于留言表中的rel_table',
`is_fix` tinyint(1) NOT NULL COMMENT '系统内置类型，1:不可删除该类型 0:可删除',
`show_name` varchar(255) NOT NULL COMMENT '类型显示名称 主要在留言板页面显示',
`is_effect` tinyint(1) NOT NULL,
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='留言类型';
INSERT INTO `%DB_PREFIX%message_type` VALUES ('1','deal','1','商品评论','1','0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('2','deal_order','1','订单留言','0','0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('3','feedback','1','意见反馈','0','0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('4','seller','1','商务合作','0','0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('6','tx','1','提现申请','0','0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('10','faq','1','问题答疑','1','0');
DROP TABLE IF EXISTS `%DB_PREFIX%mobile_list`;
CREATE TABLE `%DB_PREFIX%mobile_list` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`mobile` varchar(255) NOT NULL COMMENT '手机号',
`city_id` int(11) NOT NULL COMMENT '订阅城市ID（按地区群发时匹配）',
`verify_code` varchar(255) NOT NULL COMMENT '验证码',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
PRIMARY KEY (`id`),
KEY `mobile_idx` (`mobile`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='手机订阅表';
DROP TABLE IF EXISTS `%DB_PREFIX%msg_box`;
CREATE TABLE `%DB_PREFIX%msg_box` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`content` text NOT NULL COMMENT '内容',
`user_id` int(11) NOT NULL COMMENT '消息所属的会员',
`create_time` int(11) NOT NULL COMMENT '发信时间',
`is_read` tinyint(1) NOT NULL COMMENT '是否已读 0:未读 1:已读',
`is_delete` tinyint(1) NOT NULL COMMENT '是否被用户删除',
`type` varchar(200) NOT NULL COMMENT '消息接口类型:SystemMsg/OrderMsg等，实现来源于接口调用',
`data` text NOT NULL COMMENT '消息相关数据集，序列化后用于接口调用',
`data_id` int(11) NOT NULL COMMENT '相关数据的ID,可为0',
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`),
KEY `is_read` (`is_read`),
KEY `is_delete` (`is_delete`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='新的会员站内信表';
DROP TABLE IF EXISTS `%DB_PREFIX%msg_system`;
CREATE TABLE `%DB_PREFIX%msg_system` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`content` text NOT NULL COMMENT '内容',
`create_time` int(11) NOT NULL COMMENT '发放时间点',
`user_names` text NOT NULL COMMENT '群发的用户名列表，逗号分隔(为空表示发给所有人)',
`user_ids` text NOT NULL COMMENT 'user_id的全文索引',
`end_time` int(11) NOT NULL COMMENT '过期时间点',
PRIMARY KEY (`id`),
FULLTEXT KEY `user_ids` (`user_ids`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内信群发数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%msg_template`;
CREATE TABLE `%DB_PREFIX%msg_template` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '名称标识',
`content` text NOT NULL COMMENT '模板内容',
`type` tinyint(1) NOT NULL COMMENT '类型 0:短信 1:邮件',
`is_html` tinyint(1) NOT NULL COMMENT '针对邮件设置的是否超文本标识',
`is_allow_app` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:不允许发给app;1:允许发给app',
`is_allow_wx` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:不允许发给wx;1:允许发给wx',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='系统邮件、短信模板';
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('1','TPL_MAIL_COUPON','{$coupon.user_name}你好! 你购买的{$coupon.deal_name}已购买成功，消费券序列号{$coupon.password},有效期为{$coupon.begin_time_format}到{$coupon.end_time_format}','1','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('2','TPL_SMS_COUPON','{$coupon.user_name}你好! 你购买的{$coupon.deal_sub_name}已购买成功，消费券序列号{$coupon.password},有效期为{$coupon.begin_time_format}到{$coupon.end_time_format}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('3','TPL_MAIL_USER_VERIFY','{$user.user_name}你好，请点击以下链接验证你的会员身份\r\n</p>\r\n<a href=\'{$user.verify_url}\'>{$user.verify_url}</a>','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('4','TPL_MAIL_USER_PASSWORD','{$user.user_name}你好，请点击以下链接修改您的密码\r\n</p>\r\n<a href=\'{$user.password_url}\'>{$user.password_url}</a>','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('5','TPL_SMS_PAYMENT','{$payment_notice.user_name}你好,你所下订单{$payment_notice.order_sn}的收款单{$payment_notice.notice_sn}金额{$payment_notice.money_format}于{$payment_notice.pay_time_format}支付成功','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('6','TPL_MAIL_PAYMENT','{$payment_notice.user_name}你好,你所下订单{$payment_notice.order_sn}的收款单{$payment_notice.notice_sn}金额{$payment_notice.money_format}于{$payment_notice.pay_time_format}支付成功','1','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('7','TPL_SMS_DELIVERY','{$delivery_notice.user_name}你好,你所下订单{$delivery_notice.order_sn}的商品{$delivery_notice.deal_names}于{$delivery_notice.delivery_time_format}发货成功,发货单号{$delivery_notice.notice_sn}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('8','TPL_MAIL_DELIVERY','{$delivery_notice.user_name}你好,你所下订单{$delivery_notice.order_sn}的商品{$delivery_notice.deal_names}于{$delivery_notice.delivery_time_format}发货成功,发货单号{$delivery_notice.notice_sn}','1','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('9','TPL_SMS_VERIFY_CODE','你的手机号为{$verify.mobile},验证码为{$verify.code}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('10','TPL_DEAL_NOTICE_SMS','{$notice.site_name}又有新团购啦!{$notice.deal_name},欢迎来抢团{$notice.site_url}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('11','TPL_MAIL_UNSUBSCRIBE','您好，您确定要退订{$mail.mail_address}吗？要退订请点击<a href=\"{$mail.url}\">完成退订</a>','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('12','TPL_SMS_USE_COUPON','{$coupon.user_name}你好! 你购买的{$coupon.deal_sub_name}，消费券{$coupon.password}，已于{$coupon.confirm_time_format}使用','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('13','TPL_MAIL_USE_COUPON','{$coupon.user_name}你好! 你购买的{$coupon.deal_name}，消费券{$coupon.password}，已于{$coupon.confirm_time_format}使用','1','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('14','TPL_SMS_LOTTERY','{$lottery.user_name}你好! 你参加的{$lottery.deal_sub_name}，抽奖号为{$lottery.lottery_sn}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('15','TPL_SMS_SCORE','{$username}你好! 你支付的订单{$order_sn}{$score_value}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('16','TPL_MAIL_SCORE','{$username}你好! 你支付的订单{$order_sn}{$score_value}','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('17','TPL_SMS_EVENT_SN','{$event.user_name}你好! 你报名的{$event.name}已确认，序列号{$event.sn},有效期为{$event.begin_time_format}到{$event.end_time_format}','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('18','TPL_MAIL_EVENT_SN','{$event.user_name}你好! 你报名的{$event.name}已确认，序列号{$event.sn},有效期为{$event.begin_time_format}到{$event.end_time_format}','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('19','TPL_SMS_SUPPLIER_ORDER','{$supplier_name}，您有一笔新的订单{$order_sn}，请及时处理。','0','0','1','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('20','TPL_USER_WITHDRAW_SMS','{$user_name}您好，你的提现申请已通过，{$money_format}已经转入您指定账户。','0','0','1','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('21','TPL_USER_WITHDRAW_MAIL','{$user_name}您好，你的提现申请已通过，{$money_format}已经转入您指定账户。','1','1','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('22','TPL_SUPPLIER_WITHDRAW_SMS','{$supplier_name}您好，{$money_format}已经转入您指定账户。','0','0','0','0');
DROP TABLE IF EXISTS `%DB_PREFIX%nav`;
CREATE TABLE `%DB_PREFIX%nav` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '菜单名称',
`url` varchar(255) NOT NULL COMMENT '跳转的外链URL',
`blank` tinyint(1) NOT NULL COMMENT '是否在新窗口打开',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`u_module` varchar(255) NOT NULL COMMENT '指向的前台module',
`u_action` varchar(255) NOT NULL COMMENT '指向的前台action',
`u_id` int(11) NOT NULL COMMENT '弃用',
`u_param` varchar(255) NOT NULL COMMENT 'url的参数，以原始的url传参方式填入 如：id=1&cid=2&pid=3',
`is_shop` tinyint(1) NOT NULL COMMENT '菜单显示的频道 0:全部显示 1:团购频道 2:商城频道 3:优惠券频道',
`app_index` varchar(255) NOT NULL COMMENT '指向的前台app应用入口',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='前台导航菜单配置表';
INSERT INTO `%DB_PREFIX%nav` VALUES ('32','团购','','0','0','1','tuan','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('20','首页','','0','5','1','index','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('31','商城','','0','0','1','mall','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('33','活动','','0','0','1','events','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('34','商家','','0','0','1','stores','index','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('35','达人秀','','0','0','1','daren','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('36','小组','','0','0','1','group','index','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('37','发现','','0','0','1','discover','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('39','优惠券','','0','0','1','youhuis','','0','','0','index');
INSERT INTO `%DB_PREFIX%nav` VALUES ('42','积分商城','','0','0','1','scores','','0','','0','index');
DROP TABLE IF EXISTS `%DB_PREFIX%payment`;
CREATE TABLE `%DB_PREFIX%payment` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`class_name` varchar(255) NOT NULL COMMENT '支付接口类名',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`online_pay` tinyint(1) NOT NULL COMMENT '是否为在线支付的接口',
`fee_amount` double(20,4) NOT NULL COMMENT '手续费用的计费值',
`name` varchar(255) NOT NULL,
`description` text NOT NULL,
`total_amount` double(20,4) NOT NULL,
`config` text NOT NULL COMMENT '相关的配置信息',
`logo` varchar(255) NOT NULL COMMENT '显示的图标',
`sort` int(11) NOT NULL,
`fee_type` tinyint(1) NOT NULL COMMENT '手续费的计费标准 0:定额 1:支付总额的比率',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='支付接口表';
DROP TABLE IF EXISTS `%DB_PREFIX%payment_notice`;
CREATE TABLE `%DB_PREFIX%payment_notice` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`notice_sn` varchar(255) NOT NULL COMMENT '支付单号',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`pay_time` int(11) NOT NULL COMMENT '付款时间',
`order_id` int(11) NOT NULL COMMENT '关联的订单号ID',
`is_paid` tinyint(1) NOT NULL COMMENT '是否已支付',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`payment_id` int(11) NOT NULL COMMENT '支付接口ID',
`memo` text NOT NULL COMMENT '付款单备注',
`money` decimal(20,4) NOT NULL COMMENT '应付金额',
`outer_notice_sn` varchar(255) NOT NULL COMMENT '第三方支付平台的对帐号',
`ecv_id` int(11) NOT NULL COMMENT '代金券ID',
`order_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:全部订单 ,1:外卖预定订单,2:商户订单,3:普通订单,4:会员买单',
PRIMARY KEY (`id`),
UNIQUE KEY `notice_sn_unk` (`notice_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COMMENT='支付单表';
DROP TABLE IF EXISTS `%DB_PREFIX%point_group`;
CREATE TABLE `%DB_PREFIX%point_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分组名称',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='点评评分分组配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%point_group_elink`;
CREATE TABLE `%DB_PREFIX%point_group_elink` (
`point_group_id` int(11) NOT NULL COMMENT '评分分组ID',
`category_id` int(11) NOT NULL COMMENT '生活服务大分类ID',
KEY `group_id` (`point_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评评分分组与活动大分类的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%point_group_link`;
CREATE TABLE `%DB_PREFIX%point_group_link` (
`point_group_id` int(11) NOT NULL COMMENT '评分分组ID',
`category_id` int(11) NOT NULL COMMENT '生活服务大分类ID',
KEY `group_id` (`point_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评评分分组与生活分服大分类的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%point_group_slink`;
CREATE TABLE `%DB_PREFIX%point_group_slink` (
`point_group_id` int(11) NOT NULL COMMENT '评分分组ID',
`category_id` int(11) NOT NULL COMMENT '生活服务大分类ID',
KEY `group_id` (`point_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评评分分组与商城大分类的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%promote`;
CREATE TABLE `%DB_PREFIX%promote` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`class_name` varchar(255) NOT NULL COMMENT '促销活动接口类名',
`sort` int(11) NOT NULL COMMENT '促销活动的优先级 小到大(多个促销活动生效时，由排序较小的先开始计算，优先生效)',
`config` text NOT NULL COMMENT '促销活动的配置信息',
`description` text NOT NULL COMMENT '活动描述（用于订单中记录当前所享受的促销优惠的描述）',
`type`  tinyint(1) NOT NULL COMMENT '促销规则类型0：默认全站  1：商户促销',
`supplier_id`  int(11) NOT NULL COMMENT '商户ID',
`name`  varchar(255) NOT NULL COMMENT '促销活动的名称' ,
`supplier_or_platform`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0平台，1商户' ,

PRIMARY KEY (`id`),
UNIQUE KEY `class_name_supplier_id_unique` (`class_name`, `supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='促销活动接口安装表';
DROP TABLE IF EXISTS `%DB_PREFIX%promote_msg`;
CREATE TABLE `%DB_PREFIX%promote_msg` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`type` tinyint(1) NOT NULL COMMENT '群发推广信息类型(0:短信 1:邮件)',
`title` varchar(255) NOT NULL COMMENT '群发信息（邮件标题）',
`content` text NOT NULL COMMENT '群发的内容',
`send_time` int(11) NOT NULL COMMENT '设置的自动发送的时间',
`send_status` tinyint(1) NOT NULL COMMENT '发送状态 0:未发送 1:发送中 2:已发送',
`deal_id` int(11) NOT NULL COMMENT '针对某个商品发送的推广信息',
`send_type` tinyint(1) NOT NULL COMMENT '发送方式（0:按会员组 1:按订阅地区发送 2:自定义发送，即指定邮箱、手机发送）',
`send_type_id` int(11) NOT NULL COMMENT '发送类型为按会员组时：会员组ID，发送类型为按地区时：城市ID',
`send_define_data` text NOT NULL COMMENT '自定义发送时存放指定的邮箱地址、手机号，用半角逗号分隔',
`is_html` tinyint(1) NOT NULL COMMENT '群发为邮件时的邮件类型，是否为超文本邮件',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='群发推广信息';
DROP TABLE IF EXISTS `%DB_PREFIX%promote_msg_list`;
CREATE TABLE `%DB_PREFIX%promote_msg_list` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`dest` varchar(255) NOT NULL COMMENT '发送的目标(邮件地址/手机号)',
`send_type` tinyint(1) NOT NULL COMMENT '发送类型 0:短信 1:邮件',
`content` text NOT NULL COMMENT '信息内容',
`title` varchar(255) NOT NULL COMMENT '邮件的标题',
`send_time` int(11) NOT NULL COMMENT '发送的时间',
`is_send` tinyint(1) NOT NULL COMMENT '是否已发送 0:否 1:等待队列发送',
`create_time` int(11) NOT NULL COMMENT '生成的时间',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`result` text NOT NULL COMMENT '发送结果（如出错存放服务器或接口返回的错误信息）',
`is_success` tinyint(1) NOT NULL COMMENT '是否发送成功',
`is_html` tinyint(1) NOT NULL COMMENT '只针对邮件使用，是否为超文本邮件 0:否 1:是',
`msg_id` int(11) NOT NULL COMMENT '群发信息的原消息ID promote_msg表的数据ID',
PRIMARY KEY (`id`),
KEY `dest_idx` (`dest`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='推广群发的发送队列表';
DROP TABLE IF EXISTS `%DB_PREFIX%referrals`;
CREATE TABLE `%DB_PREFIX%referrals` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '邀请人ID（即需要返利的会员ID）',
`rel_user_id` int(11) NOT NULL COMMENT '被邀请人ID',
`money` double(20,4) NOT NULL COMMENT '返利的现金',
`create_time` int(11) NOT NULL COMMENT '返利生成的时间',
`pay_time` int(11) NOT NULL COMMENT '返利发放的时间',
`order_id` int(11) NOT NULL COMMENT '关联的订单ID',
`score` int(11) NOT NULL COMMENT '返利的积分',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='邀请返利记录表';
DROP TABLE IF EXISTS `%DB_PREFIX%region_conf`;
CREATE TABLE `%DB_PREFIX%region_conf` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`pid` int(11) NOT NULL COMMENT '父级地区ID',
`name` varchar(50) NOT NULL COMMENT '地区名称',
`region_level` tinyint(4) NOT NULL COMMENT '2:省 3:市(县)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3411 DEFAULT CHARSET=utf8 COMMENT='地区信息表（会员资料修改中用到的地区信息）';
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3','1','安徽','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('4','1','福建','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('5','1','甘肃','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('6','1','广东','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('7','1','广西','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('8','1','贵州','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('9','1','海南','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('10','1','河北','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('11','1','河南','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('12','1','黑龙江','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('13','1','湖北','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('14','1','湖南','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('15','1','吉林','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('16','1','江苏','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('17','1','江西','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('18','1','辽宁','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('19','1','内蒙古','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('20','1','宁夏','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('21','1','青海','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('22','1','山东','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('23','1','山西','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('24','1','陕西','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('26','1','四川','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('28','1','西藏','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('29','1','新疆','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('30','1','云南','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('31','1','浙江','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('36','3','安庆','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('37','3','蚌埠','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('38','3','巢湖','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('39','3','池州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('40','3','滁州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('41','3','阜阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('42','3','淮北','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('43','3','淮南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('44','3','黄山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('45','3','六安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('46','3','马鞍山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('47','3','宿州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('48','3','铜陵','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('49','3','芜湖','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('50','3','宣城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('51','3','亳州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('52','2','北京','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('53','4','福州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('54','4','龙岩','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('55','4','南平','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('56','4','宁德','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('57','4','莆田','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('58','4','泉州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('59','4','三明','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('60','4','厦门','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('61','4','漳州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('62','5','兰州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('63','5','白银','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('64','5','定西','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('65','5','甘南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('66','5','嘉峪关','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('67','5','金昌','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('68','5','酒泉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('69','5','临夏','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('70','5','陇南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('71','5','平凉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('72','5','庆阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('73','5','天水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('74','5','武威','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('75','5','张掖','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('76','6','广州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('77','6','深圳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('78','6','潮州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('79','6','东莞','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('80','6','佛山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('81','6','河源','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('82','6','惠州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('83','6','江门','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('84','6','揭阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('85','6','茂名','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('86','6','梅州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('87','6','清远','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('88','6','汕头','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('89','6','汕尾','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('90','6','韶关','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('91','6','阳江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('92','6','云浮','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('93','6','湛江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('94','6','肇庆','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('95','6','中山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('96','6','珠海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('97','7','南宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('98','7','桂林','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('99','7','百色','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('100','7','北海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('101','7','崇左','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('102','7','防城港','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('103','7','贵港','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('104','7','河池','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('105','7','贺州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('106','7','来宾','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('107','7','柳州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('108','7','钦州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('109','7','梧州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('110','7','玉林','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('111','8','贵阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('112','8','安顺','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('113','8','毕节','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('114','8','六盘水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('115','8','黔东南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('116','8','黔南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('117','8','黔西南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('118','8','铜仁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('119','8','遵义','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('120','9','海口','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('121','9','三亚','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('122','9','白沙','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('123','9','保亭','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('124','9','昌江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('125','9','澄迈县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('126','9','定安县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('127','9','东方','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('128','9','乐东','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('129','9','临高县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('130','9','陵水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('131','9','琼海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('132','9','琼中','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('133','9','屯昌县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('134','9','万宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('135','9','文昌','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('136','9','五指山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('137','9','儋州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('138','10','石家庄','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('139','10','保定','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('140','10','沧州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('141','10','承德','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('142','10','邯郸','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('143','10','衡水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('144','10','廊坊','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('145','10','秦皇岛','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('146','10','唐山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('147','10','邢台','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('148','10','张家口','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('149','11','郑州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('150','11','洛阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('151','11','开封','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('152','11','安阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('153','11','鹤壁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('154','11','济源','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('155','11','焦作','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('156','11','南阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('157','11','平顶山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('158','11','三门峡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('159','11','商丘','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('160','11','新乡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('161','11','信阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('162','11','许昌','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('163','11','周口','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('164','11','驻马店','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('165','11','漯河','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('166','11','濮阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('167','12','哈尔滨','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('168','12','大庆','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('169','12','大兴安岭','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('170','12','鹤岗','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('171','12','黑河','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('172','12','鸡西','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('173','12','佳木斯','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('174','12','牡丹江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('175','12','七台河','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('176','12','齐齐哈尔','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('177','12','双鸭山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('178','12','绥化','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('179','12','伊春','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('180','13','武汉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('181','13','仙桃','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('182','13','鄂州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('183','13','黄冈','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('184','13','黄石','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('185','13','荆门','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('186','13','荆州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('187','13','潜江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('188','13','神农架林区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('189','13','十堰','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('190','13','随州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('191','13','天门','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('192','13','咸宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('193','13','襄樊','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('194','13','孝感','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('195','13','宜昌','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('196','13','恩施','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('197','14','长沙','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('198','14','张家界','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('199','14','常德','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('200','14','郴州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('201','14','衡阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('202','14','怀化','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('203','14','娄底','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('204','14','邵阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('205','14','湘潭','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('206','14','湘西','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('207','14','益阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('208','14','永州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('209','14','岳阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('210','14','株洲','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('211','15','长春','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('212','15','吉林','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('213','15','白城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('214','15','白山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('215','15','辽源','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('216','15','四平','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('217','15','松原','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('218','15','通化','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('219','15','延边','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('220','16','南京','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('221','16','苏州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('222','16','无锡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('223','16','常州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('224','16','淮安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('225','16','连云港','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('226','16','南通','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('227','16','宿迁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('228','16','泰州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('229','16','徐州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('230','16','盐城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('231','16','扬州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('232','16','镇江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('233','17','南昌','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('234','17','抚州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('235','17','赣州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('236','17','吉安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('237','17','景德镇','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('238','17','九江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('239','17','萍乡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('240','17','上饶','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('241','17','新余','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('242','17','宜春','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('243','17','鹰潭','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('244','18','沈阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('245','18','大连','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('246','18','鞍山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('247','18','本溪','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('248','18','朝阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('249','18','丹东','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('250','18','抚顺','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('251','18','阜新','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('252','18','葫芦岛','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('253','18','锦州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('254','18','辽阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('255','18','盘锦','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('256','18','铁岭','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('257','18','营口','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('258','19','呼和浩特','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('259','19','阿拉善盟','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('260','19','巴彦淖尔盟','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('261','19','包头','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('262','19','赤峰','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('263','19','鄂尔多斯','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('264','19','呼伦贝尔','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('265','19','通辽','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('266','19','乌海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('267','19','乌兰察布市','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('268','19','锡林郭勒盟','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('269','19','兴安盟','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('270','20','银川','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('271','20','固原','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('272','20','石嘴山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('273','20','吴忠','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('274','20','中卫','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('275','21','西宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('276','21','果洛','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('277','21','海北','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('278','21','海东','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('279','21','海南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('280','21','海西','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('281','21','黄南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('282','21','玉树','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('283','22','济南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('284','22','青岛','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('285','22','滨州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('286','22','德州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('287','22','东营','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('288','22','菏泽','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('289','22','济宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('290','22','莱芜','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('291','22','聊城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('292','22','临沂','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('293','22','日照','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('294','22','泰安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('295','22','威海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('296','22','潍坊','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('297','22','烟台','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('298','22','枣庄','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('299','22','淄博','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('300','23','太原','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('301','23','长治','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('302','23','大同','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('303','23','晋城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('304','23','晋中','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('305','23','临汾','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('306','23','吕梁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('307','23','朔州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('308','23','忻州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('309','23','阳泉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('310','23','运城','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('311','24','西安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('312','24','安康','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('313','24','宝鸡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('314','24','汉中','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('315','24','商洛','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('316','24','铜川','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('317','24','渭南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('318','24','咸阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('319','24','延安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('320','24','榆林','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('321','25','上海','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('322','26','成都','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('323','26','绵阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('324','26','阿坝','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('325','26','巴中','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('326','26','达州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('327','26','德阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('328','26','甘孜','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('329','26','广安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('330','26','广元','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('331','26','乐山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('332','26','凉山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('333','26','眉山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('334','26','南充','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('335','26','内江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('336','26','攀枝花','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('337','26','遂宁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('338','26','雅安','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('339','26','宜宾','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('340','26','资阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('341','26','自贡','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('342','26','泸州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('343','27','天津','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('344','28','拉萨','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('345','28','阿里','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('346','28','昌都','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('347','28','林芝','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('348','28','那曲','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('349','28','日喀则','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('350','28','山南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('351','29','乌鲁木齐','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('352','29','阿克苏','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('353','29','阿拉尔','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('354','29','巴音郭楞','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('355','29','博尔塔拉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('356','29','昌吉','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('357','29','哈密','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('358','29','和田','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('359','29','喀什','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('360','29','克拉玛依','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('361','29','克孜勒苏','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('362','29','石河子','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('363','29','图木舒克','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('364','29','吐鲁番','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('365','29','五家渠','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('366','29','伊犁','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('367','30','昆明','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('368','30','怒江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('369','30','普洱','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('370','30','丽江','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('371','30','保山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('372','30','楚雄','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('373','30','大理','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('374','30','德宏','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('375','30','迪庆','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('376','30','红河','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('377','30','临沧','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('378','30','曲靖','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('379','30','文山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('380','30','西双版纳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('381','30','玉溪','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('382','30','昭通','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('383','31','杭州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('384','31','湖州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('385','31','嘉兴','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('386','31','金华','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('387','31','丽水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('388','31','宁波','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('389','31','绍兴','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('390','31','台州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('391','31','温州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('392','31','舟山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('393','31','衢州','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('394','32','重庆','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('395','33','香港','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('396','34','澳门','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('397','35','台湾','2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('500','52','东城区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('501','52','西城区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('502','52','海淀区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('503','52','朝阳区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('504','52','崇文区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('505','52','宣武区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('506','52','丰台区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('507','52','石景山区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('508','52','房山区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('509','52','门头沟区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('510','52','通州区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('511','52','顺义区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('512','52','昌平区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('513','52','怀柔区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('514','52','平谷区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('515','52','大兴区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('516','52','密云县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('517','52','延庆县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2703','321','长宁区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2704','321','闸北区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2705','321','闵行区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2706','321','徐汇区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2707','321','浦东新区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2708','321','杨浦区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2709','321','普陀区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2710','321','静安区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2711','321','卢湾区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2712','321','虹口区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2713','321','黄浦区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2714','321','南汇区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2715','321','松江区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2716','321','嘉定区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2717','321','宝山区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2718','321','青浦区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2719','321','金山区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2720','321','奉贤区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2721','321','崇明县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2912','343','和平区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2913','343','河西区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2914','343','南开区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2915','343','河北区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2916','343','河东区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2917','343','红桥区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2918','343','东丽区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2919','343','津南区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2920','343','西青区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2921','343','北辰区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2922','343','塘沽区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2923','343','汉沽区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2924','343','大港区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2925','343','武清区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2926','343','宝坻区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2927','343','经济开发区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2928','343','宁河县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2929','343','静海县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2930','343','蓟县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3325','394','合川区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3326','394','江津区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3327','394','南川区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3328','394','永川区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3329','394','南岸区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3330','394','渝北区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3331','394','万盛区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3332','394','大渡口区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3333','394','万州区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3334','394','北碚区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3335','394','沙坪坝区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3336','394','巴南区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3337','394','涪陵区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3338','394','江北区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3339','394','九龙坡区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3340','394','渝中区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3341','394','黔江开发区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3342','394','长寿区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3343','394','双桥区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3344','394','綦江县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3345','394','潼南县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3346','394','铜梁县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3347','394','大足县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3348','394','荣昌县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3349','394','璧山县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3350','394','垫江县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3351','394','武隆县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3352','394','丰都县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3353','394','城口县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3354','394','梁平县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3355','394','开县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3356','394','巫溪县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3357','394','巫山县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3358','394','奉节县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3359','394','云阳县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3360','394','忠县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3361','394','石柱','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3362','394','彭水','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3363','394','酉阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3364','394','秀山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3365','395','沙田区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3366','395','东区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3367','395','观塘区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3368','395','黄大仙区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3369','395','九龙城区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3370','395','屯门区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3371','395','葵青区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3372','395','元朗区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3373','395','深水埗区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3374','395','西贡区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3375','395','大埔区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3376','395','湾仔区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3377','395','油尖旺区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3378','395','北区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3379','395','南区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3380','395','荃湾区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3381','395','中西区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3382','395','离岛区','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3383','396','澳门','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3384','397','台北','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3385','397','高雄','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3386','397','基隆','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3387','397','台中','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3388','397','台南','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3389','397','新竹','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3390','397','嘉义','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3391','397','宜兰县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3392','397','桃园县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3393','397','苗栗县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3394','397','彰化县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3395','397','南投县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3396','397','云林县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3397','397','屏东县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3398','397','台东县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3399','397','花莲县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3400','397','澎湖县','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3401','3','合肥','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3402','3401','瑶海','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3403','3401','庐阳','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3404','3401','蜀山','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3405','3401','包河','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3406','3401','长丰','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3407','3401','肥东','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3408','3401','肥西','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3409','3401','巢湖','3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3410','3401','庐江','3');
DROP TABLE IF EXISTS `%DB_PREFIX%relate_goods`;
CREATE TABLE `%DB_PREFIX%relate_goods` (
`good_id` int(11) NOT NULL COMMENT '商品/团购id',
`relate_ids` text NOT NULL COMMENT '关联的商品/团购id',
`is_shop` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=团购、1=商品',
UNIQUE KEY `good_id` (`good_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品关联购买';
DROP TABLE IF EXISTS `%DB_PREFIX%remind_count`;
CREATE TABLE `%DB_PREFIX%remind_count` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`topic_count` int(11) NOT NULL COMMENT '分享统计',
`topic_count_time` int(11) NOT NULL COMMENT '最后一次分享统计的时间',
`dp_count` int(11) NOT NULL COMMENT '点评统计',
`dp_count_time` int(11) NOT NULL COMMENT '最后一次点评统计的时间',
`msg_count` int(11) NOT NULL COMMENT '留言统计',
`msg_count_time` int(11) NOT NULL COMMENT '最后一次留言统计的时间',
`buy_msg_count` int(11) NOT NULL COMMENT '购物点评统计',
`buy_msg_count_time` int(11) NOT NULL COMMENT '最后一次购物点评统计的时间',
`order_count` int(11) NOT NULL COMMENT '订单统计',
`order_count_time` int(11) NOT NULL COMMENT '最后一次订单统计的时间',
`refund_count` int(11) NOT NULL COMMENT '退款统计',
`refund_count_time` int(11) NOT NULL COMMENT '最后一次退款统计的时间',
`retake_count` int(11) NOT NULL COMMENT '弃用',
`retake_count_time` int(11) NOT NULL COMMENT '弃用',
`incharge_count` int(11) NOT NULL COMMENT '充值统计',
`incharge_count_time` int(11) NOT NULL COMMENT '最后一次充值统计的时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='后台首页新进数据统计的记录表';
DROP TABLE IF EXISTS `%DB_PREFIX%role`;
CREATE TABLE `%DB_PREFIX%role` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
`is_delete` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='后台管理员角色表';
DROP TABLE IF EXISTS `%DB_PREFIX%role_access`;
CREATE TABLE `%DB_PREFIX%role_access` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`role_id` int(11) NOT NULL COMMENT '角色ID',
`node` varchar(255) NOT NULL COMMENT '节点action名',
`module` varchar(255) NOT NULL COMMENT '模块名',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 COMMENT='后台角色权限配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%session`;
CREATE TABLE `%DB_PREFIX%session` (
`session_id` varchar(255) NOT NULL,
`session_data` text NOT NULL,
`session_time` int(11) NOT NULL,
PRIMARY KEY (`session_id`),
KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `%DB_PREFIX%shop_cate`;
CREATE TABLE `%DB_PREFIX%shop_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分类名称',
`brief` text NOT NULL COMMENT '分类描述',
`pid` int(11) NOT NULL COMMENT '所属父类ID',
`is_delete` tinyint(1) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`uname` varchar(255) NOT NULL COMMENT 'url别名',
`recommend` tinyint(1) NOT NULL COMMENT '是否将该分类推荐为商城首页的分类产品模块 0:否 1:是',
`iconfont` varchar(15) NOT NULL,
`iconcolor` varchar(15) NOT NULL,
PRIMARY KEY (`id`),
KEY `sort` (`sort`),
KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='商城分类表';
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('24','服装','','0','0','1','1','cloth','1','','#438ccb');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('25','鞋帽','','0','0','1','2','','0','&#58892;','#00736a');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('26','手表眼镜','','0','0','1','3','','0','&#58884;','#a1410d');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('27','家用电器','','0','0','1','4','','0','&#58882;','#37b44a');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('28','居家生活','','0','0','1','5','','0','&#58893;','#855fa8');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('29','母婴用品','','0','0','1','6','','0','&#58886;','#f16522');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('30','女装','','24','0','1','7','','0','','');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('31','男装','','24','0','1','8','','0','','');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('32','家居服','','24','0','1','9','','0','','');
INSERT INTO `%DB_PREFIX%shop_cate` VALUES ('33','毛衣','','24','0','1','10','','0','','');
DROP TABLE IF EXISTS `%DB_PREFIX%sms`;
CREATE TABLE `%DB_PREFIX%sms` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '短信接口显示名称',
`description` text NOT NULL COMMENT '描述',
`class_name` varchar(255) NOT NULL COMMENT '类名',
`server_url` text NOT NULL COMMENT '接口的服务器通讯地址',
`user_name` varchar(255) NOT NULL COMMENT '接口商验证用用户名',
`password` varchar(255) NOT NULL COMMENT '接口商验证用密码',
`config` text NOT NULL COMMENT '额外的配置信息',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='短信接口配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%sms_mobile_verify`;
CREATE TABLE `%DB_PREFIX%sms_mobile_verify` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`mobile_phone` varchar(50) NOT NULL DEFAULT '',
`code` varchar(20) NOT NULL DEFAULT '',
`add_time` int(10) NOT NULL,
`ip` varchar(100) NOT NULL COMMENT '发送短信人的IP',
`send_count` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=169 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `%DB_PREFIX%statements`;
CREATE TABLE `%DB_PREFIX%statements` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`income_money` decimal(20,4) NOT NULL COMMENT '收入',
`income_order` decimal(20,4) NOT NULL COMMENT '收入中用于订单支付',
`income_incharge` decimal(20,4) NOT NULL COMMENT '收入用于会员充值(含超额充值)',
`out_money` decimal(20,4) NOT NULL COMMENT '支出',
`out_uwd_money` decimal(20,4) NOT NULL COMMENT '会员提现支出',
`out_swd_money` decimal(20,4) NOT NULL COMMENT '商户提现支出',
`refund_money` decimal(20,4) NOT NULL COMMENT '退款金额',
`refund_cost_money` decimal(20,4) NOT NULL,
`sale_money` decimal(20,4) NOT NULL COMMENT '销售额,所有支付成功的订单面额(不含在线充值)',
`sale_cost_money` decimal(20,4) NOT NULL COMMENT '销售额中成本(即将结算给商家的部份)',
`balance_money` decimal(20,4) NOT NULL COMMENT '商家结算额',
`verify_money` decimal(20,4) NOT NULL COMMENT '消费的数量',
`verify_cost_money` decimal(20,4) NOT NULL COMMENT '消费额中的成本',
`stat_time` date NOT NULL COMMENT '日报时间',
`stat_month` varchar(10) NOT NULL COMMENT '月份',
PRIMARY KEY (`id`),
UNIQUE KEY `stat_time` (`stat_time`) USING BTREE,
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台财务日报表\r\n';
DROP TABLE IF EXISTS `%DB_PREFIX%statements_log`;
CREATE TABLE `%DB_PREFIX%statements_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`create_time` int(11) NOT NULL,
`type` tinyint(1) NOT NULL COMMENT '0.收入 1.订单支付收入 2.会员充值收入 3.支出 4.会员提现支出 5.商户提现支出 6.退款金额 7.退款中的成本 8.销售额,所有支付成功的订单面额(不含在线充值) 9.销售额中成本(即将结算给商家的部份) 10.商家结算额 11.消费额 12.消费额中的成本',
`money` decimal(20,4) NOT NULL,
`log_info` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务报表日志';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier`;
CREATE TABLE `%DB_PREFIX%supplier` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '商户名称',
`preview` varchar(255) NOT NULL COMMENT '商家logo',
`content` text NOT NULL COMMENT '商家描述信息',
`sort` int(11) NOT NULL COMMENT '排序',
`is_effect` tinyint(1) NOT NULL,
`city_id` int(11) NOT NULL COMMENT '所属城市',
`name_match` text NOT NULL COMMENT '名称的全文索引unicode',
`name_match_row` text NOT NULL COMMENT '名称全文索引查询用',
`bank_info` text NOT NULL COMMENT '提现银行帐号',
`money` decimal(20,4) NOT NULL COMMENT '商户余额(可提现余额,已结算金额，结算后，待结算减少，已结算增加)',
`sale_money` decimal(20,4) NOT NULL COMMENT '销售总额',
`lock_money` decimal(20,4) NOT NULL COMMENT '冻结资金(即已销售，未验证，未收货的金额)',
`balance_money` decimal(20,4) NOT NULL COMMENT '待结算金额（即每验证，收货一个，增加此金额，同时扣除冻结金额）',
`refund_money` decimal(20,4) NOT NULL COMMENT '已退款金额（退款后增加此金额，同时减少lock_money冻结金额）',
`wd_money` decimal(20,4) NOT NULL COMMENT '已提现金额：（已提走的金额,提现成功后，增加，同时减少money）',
`bank_name` varchar(255) NOT NULL COMMENT '提现的开户行名称',
`bank_user` varchar(255) NOT NULL COMMENT '提现的开户行户名',
`dp_count_1` int(11) NOT NULL,
`dp_count_2` int(11) NOT NULL,
`dp_count_3` int(11) NOT NULL,
`dp_count_4` int(11) NOT NULL,
`dp_count_5` int(11) NOT NULL,
`dp_count` int(11) NOT NULL,
`avg_point` float(14,4) NOT NULL,
`total_point` int(11) NOT NULL,
`total_point_1` int(11) NOT NULL,
`avg_point_1` float(14,4) NOT NULL,
`total_point_2` int(11) NOT NULL,
`avg_point_2` float(14,4) NOT NULL,
`total_point_3` int(11) NOT NULL,
`avg_point_3` float(14,4) NOT NULL,
`total_point_4` int(11) NOT NULL,
`avg_point_4` float(14,4) NOT NULL,
`total_point_5` int(11) NOT NULL,
`avg_point_5` float(14,4) NOT NULL,
`h_name` varchar(255) NOT NULL COMMENT '公司名称',
`h_faren` varchar(255) NOT NULL COMMENT '法人名称',
`h_tel` varchar(255) NOT NULL COMMENT '法人联系电话',
`allow_refund` tinyint(1) NOT NULL COMMENT '是否支持退款审核',
`allow_publish_verify` tinyint(1) NOT NULL COMMENT '是否支持自动发布',
`publish_verify_balance` decimal(20,4) NOT NULL COMMENT '自动审核时的结算费用率',
`weishop_name` varchar(255) NOT NULL COMMENT '微店店名',
`weishop_logo` varchar(255) NOT NULL COMMENT '微店logo',
`weishop_banner` text NOT NULL COMMENT '微店banner位(多个)',
`platform_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否支持公众平台功能 0否 1是',
`is_store_payment`  tinyint(1) NOT NULL COMMENT '商户是否支持到店支付',
`store_payment_rate`  decimal(8,4) NOT NULL COMMENT '到店支付费率',
`store_pay_explain`  text NOT NULL COMMENT '买单说明',
PRIMARY KEY (`id`),
KEY `id` (`id`),
KEY `is_effect` (`is_effect`),
KEY `sort` (`sort`),
KEY `city_id` (`city_id`),
FULLTEXT KEY `name_match` (`name_match`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='商户表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_account`;
CREATE TABLE `%DB_PREFIX%supplier_account` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`account_name` varchar(255) NOT NULL COMMENT '商家帐号名',
`account_password` varchar(255) NOT NULL COMMENT '商家帐号密码',
`supplier_id` int(11) NOT NULL COMMENT '所属商家ID',
`is_effect` tinyint(1) NOT NULL,
`is_delete` tinyint(1) NOT NULL,
`description` text NOT NULL COMMENT '帐号说明（管理员备注用）',
`login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
`login_time` int(11) NOT NULL COMMENT '最后登录时间',
`update_time` int(11) NOT NULL COMMENT '最后更新时间',
`allow_delivery` tinyint(1) NOT NULL COMMENT '是否允许对订单进行发货操作',
`allow_charge` tinyint(1) NOT NULL COMMENT '是否允许提现',
`is_main` tinyint(1) NOT NULL COMMENT '是否为默认总管理员',
`mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
`dev_type` varchar(20) DEFAULT 'android' COMMENT 'android,ios 客户手机类型,一个客户只绑定一个最新的手机',
`device_token` varchar(255) DEFAULT NULL COMMENT '推送device_token一个客户只绑定一个最新的手机',
PRIMARY KEY (`id`),
UNIQUE KEY `unk_account_name` (`account_name`),
UNIQUE KEY `mobile` (`mobile`),
KEY `is_main` (`is_main`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='商家帐号表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_account_auth`;
CREATE TABLE `%DB_PREFIX%supplier_account_auth` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
`supplier_account_id` int(11) NOT NULL COMMENT '管理员帐号ID',
`module` varchar(20) NOT NULL COMMENT '授权模块',
`node` varchar(20) NOT NULL COMMENT '授权节点',
PRIMARY KEY (`id`),
UNIQUE KEY `uk` (`supplier_account_id`,`module`,`node`),
KEY `supplier_account_id` (`supplier_account_id`),
KEY `module` (`module`),
KEY `node` (`node`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家账号的授权表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_account_location_link`;
CREATE TABLE `%DB_PREFIX%supplier_account_location_link` (
`account_id` int(11) NOT NULL COMMENT '帐号ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
PRIMARY KEY (`account_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帐号可管理的门店';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_dy`;
CREATE TABLE `%DB_PREFIX%supplier_dy` (
`uid` int(11) NOT NULL,
`supplier_id` int(11) NOT NULL,
PRIMARY KEY (`uid`,`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机端商家订阅功能';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location`;
CREATE TABLE `%DB_PREFIX%supplier_location` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '门店名称',
`route` text NOT NULL COMMENT '公交线路',
`address` text NOT NULL COMMENT '门店地址',
`tel` varchar(255) NOT NULL COMMENT '门店电话',
`contact` varchar(255) NOT NULL COMMENT '联系人',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`supplier_id` int(11) NOT NULL COMMENT '所属商家ID',
`open_time` varchar(255) NOT NULL COMMENT '营业时间',
`brief` text NOT NULL COMMENT '商家简介',
`is_main` tinyint(1) NOT NULL COMMENT '是否为默认门店(总店)',
`api_address` text NOT NULL COMMENT '用于地图定位的地址',
`city_id` int(11) NOT NULL COMMENT '所属城市ID',
`deal_cate_match` text NOT NULL COMMENT '生活服务分类全文索引',
`deal_cate_match_row` text NOT NULL,
`locate_match` text NOT NULL COMMENT '地址全文索引',
`locate_match_row` text NOT NULL,
`name_match` text NOT NULL COMMENT '门店名称全文索引',
`name_match_row` text NOT NULL,
`deal_cate_id` int(11) NOT NULL COMMENT '所属生活服务大分类ID',
`preview` varchar(255) NOT NULL COMMENT '列表图',
`is_recommend` tinyint(1) NOT NULL COMMENT '标识为推荐门店',
`is_verify` tinyint(1) NOT NULL COMMENT '认证门店',
`tags` varchar(255) NOT NULL COMMENT '标签列表 多个标签以空格分隔',
`tags_match` text NOT NULL COMMENT '标签的全文索引',
`tags_match_row` text NOT NULL,
`avg_point` float(14,4) NOT NULL COMMENT '总评平均分，后台可操作更改',
`good_dp_count` int(11) NOT NULL COMMENT '好评数',
`bad_dp_count` int(11) NOT NULL COMMENT '差评数',
`common_dp_count` int(11) NOT NULL COMMENT '中评数',
`total_point` int(11) NOT NULL COMMENT '点评总分',
`dp_count` int(11) NOT NULL COMMENT '点评总数',
`image_count` int(11) NOT NULL COMMENT '门店图片数',
`ref_avg_price` float(14,4) NOT NULL COMMENT '人均消费',
`good_rate` float(14,4) NOT NULL COMMENT '好评率',
`common_rate` float(14,4) NOT NULL COMMENT '中评率',
`sms_content` varchar(255) NOT NULL DEFAULT '' COMMENT '用户短信下载的商家信息',
`index_img` varchar(255) NOT NULL DEFAULT '' COMMENT '首页用图',
`tuan_count` int(11) NOT NULL COMMENT '团购数',
`event_count` int(11) NOT NULL COMMENT '活动数',
`youhui_count` int(11) NOT NULL COMMENT '优惠券数',
`daijin_count` int(11) NOT NULL COMMENT '代金券数',
`seo_title` text NOT NULL COMMENT '自定义门店页的seo标题',
`seo_keyword` text NOT NULL COMMENT '自定义门店页的seo关键词',
`seo_description` text NOT NULL COMMENT '自定义门店页的seo描述',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`biz_license` varchar(255) NOT NULL COMMENT '商家营业执照',
`biz_other_license` varchar(255) NOT NULL COMMENT '商家的其他资质',
`new_dp_count` int(11) NOT NULL COMMENT '最新点评数量',
`new_dp_count_time` int(11) NOT NULL COMMENT '最新点评的更新时间',
`shop_count` int(11) NOT NULL COMMENT '商品数量',
`mobile_brief` text NOT NULL COMMENT '手机端列表简介',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`dp_group_point` text NOT NULL COMMENT '门店的分组点评数据的平均分',
`tuan_youhui_cache` text NOT NULL COMMENT '商家列表页团购与优惠券第一条的展示',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`adv_img_1` text NOT NULL COMMENT '门店顶部广告位',
`adv_img_2` text NOT NULL COMMENT '门店侧边广告位',
`location_qq` varchar(20) NOT NULL COMMENT '门店客服QQ',
`open_store_payment`  tinyint(1) NULL COMMENT '针对门店开启到店支付',
PRIMARY KEY (`id`),
KEY `id` (`id`),
KEY `city_id` (`city_id`),
KEY `is_verify` (`is_verify`),
KEY `is_effect` (`is_effect`),
KEY `is_recommend` (`is_recommend`),
KEY `avg_point` (`avg_point`),
KEY `good_dp_count` (`good_dp_count`),
KEY `bad_dp_count` (`bad_dp_count`),
KEY `common_dp_count` (`common_dp_count`),
KEY `total_point` (`total_point`),
KEY `dp_count` (`dp_count`),
KEY `good_rate` (`good_rate`),
KEY `common_rate` (`common_rate`),
KEY `tuan_count` (`tuan_count`),
KEY `event_count` (`event_count`),
KEY `youhui_count` (`youhui_count`),
KEY `daijin_count` (`daijin_count`),
KEY `new_dp_count` (`new_dp_count`),
KEY `is_main` (`is_main`),
KEY `supplier_id` (`supplier_id`) USING BTREE,
KEY `search_idx1` (`city_id`,`is_recommend`,`is_effect`,`is_verify`) USING BTREE,
KEY `search_idx2` (`city_id`,`avg_point`,`is_effect`) USING BTREE,
KEY `search_idx3` (`supplier_id`,`is_main`) USING BTREE,
KEY `search_idx4` (`city_id`,`deal_cate_id`,`is_verify`,`is_effect`,`is_recommend`) USING BTREE,
KEY `search_idx5` (`city_id`,`deal_cate_id`,`dp_count`,`avg_point`,`ref_avg_price`,`is_effect`,`is_recommend`,`is_verify`) USING BTREE,
KEY `search_idx6` (`good_rate`,`is_verify`,`is_effect`) USING BTREE,
KEY `sort_default` (`is_recommend`,`is_verify`,`dp_count`) USING BTREE,
KEY `ref_avg_price` (`ref_avg_price`),
KEY `shop_count` (`shop_count`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `deal_cate_match` (`deal_cate_match`),
FULLTEXT KEY `tags_match` (`tags_match`),
FULLTEXT KEY `all_match` (`deal_cate_match`,`locate_match`,`name_match`,`tags_match`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='商家门店表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_area_link`;
CREATE TABLE `%DB_PREFIX%supplier_location_area_link` (
`location_id` int(11) NOT NULL,
`area_id` int(11) NOT NULL,
PRIMARY KEY (`location_id`,`area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家门店的商圈关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_biz_submit`;
CREATE TABLE `%DB_PREFIX%supplier_location_biz_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '门店名称',
`route` text NOT NULL COMMENT '公交线路',
`address` text NOT NULL COMMENT '门店地址',
`tel` varchar(255) NOT NULL COMMENT '门店电话',
`contact` varchar(255) NOT NULL COMMENT '联系人',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`supplier_id` int(11) NOT NULL COMMENT '所属商家ID',
`open_time` varchar(255) NOT NULL COMMENT '营业时间',
`brief` text NOT NULL COMMENT '商家简介',
`is_main` tinyint(1) NOT NULL COMMENT '是否为默认门店(总店)',
`api_address` text NOT NULL COMMENT '用于地图定位的地址',
`city_id` int(11) NOT NULL COMMENT '所属城市ID',
`deal_cate_match` text NOT NULL COMMENT '生活服务分类全文索引',
`deal_cate_match_row` text NOT NULL,
`locate_match` text NOT NULL COMMENT '地址全文索引',
`locate_match_row` text NOT NULL,
`name_match` text NOT NULL COMMENT '门店名称全文索引',
`name_match_row` text NOT NULL,
`deal_cate_id` int(11) NOT NULL COMMENT '所属生活服务大分类ID',
`preview` varchar(255) NOT NULL COMMENT '列表图',
`is_recommend` tinyint(1) NOT NULL COMMENT '标识为推荐门店',
`is_verify` tinyint(1) NOT NULL COMMENT '认证门店',
`tags` varchar(255) NOT NULL COMMENT '标签列表 多个标签以空格分隔',
`tags_match` text NOT NULL COMMENT '标签的全文索引',
`tags_match_row` text NOT NULL,
`avg_point` float(14,4) NOT NULL COMMENT '总评平均分，后台可操作更改',
`good_dp_count` int(11) NOT NULL COMMENT '好评数',
`bad_dp_count` int(11) NOT NULL COMMENT '差评数',
`common_dp_count` int(11) NOT NULL COMMENT '中评数',
`total_point` int(11) NOT NULL COMMENT '点评总分',
`dp_count` int(11) NOT NULL COMMENT '点评总数',
`image_count` int(11) NOT NULL COMMENT '门店图片数',
`ref_avg_price` float(14,4) NOT NULL COMMENT '真实的总评分的平均分',
`good_rate` float(14,4) NOT NULL COMMENT '好评率',
`common_rate` float(14,4) NOT NULL COMMENT '中评率',
`sms_content` varchar(255) NOT NULL DEFAULT '' COMMENT '用户短信下载的商家信息',
`index_img` varchar(255) NOT NULL DEFAULT '' COMMENT '首页用图',
`tuan_count` int(11) NOT NULL COMMENT '团购数',
`event_count` int(11) NOT NULL COMMENT '活动数',
`youhui_count` int(11) NOT NULL COMMENT '优惠券数',
`daijin_count` int(11) NOT NULL COMMENT '代金券数',
`seo_title` text NOT NULL COMMENT '自定义门店页的seo标题',
`seo_keyword` text NOT NULL COMMENT '自定义门店页的seo关键词',
`seo_description` text NOT NULL COMMENT '自定义门店页的seo描述',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`biz_license` varchar(255) NOT NULL COMMENT '商家营业执照',
`biz_other_license` varchar(255) NOT NULL COMMENT '商家的其他资质',
`new_dp_count` int(11) NOT NULL COMMENT '最新点评数量',
`new_dp_count_time` int(11) NOT NULL COMMENT '最新点评的更新时间',
`shop_count` int(11) NOT NULL COMMENT '商品数量',
`mobile_brief` text NOT NULL COMMENT '手机端列表简介',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`dp_group_point` text NOT NULL COMMENT '门店的分组点评数据的平均分',
`tuan_youhui_cache` text NOT NULL COMMENT '商家列表页团购与优惠券第一条的展示',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`account_id` int(11) NOT NULL COMMENT '提交数据的商户帐号关联ID',
`location_id` int(11) NOT NULL COMMENT '关联活动主表的数据ID',
`cache_supplier_location_area_link` text NOT NULL COMMENT '序列化地区列表配置',
`cache_deal_cate_type_location_link` text NOT NULL COMMENT '序列化子分类列表配置',
`cache_supplier_tag` text NOT NULL COMMENT '序列化子商户标签设置配置',
`cache_supplier_location_images` text NOT NULL COMMENT '门店图库序列化缓存',
`biz_apply_status` tinyint(1) NOT NULL COMMENT '商户申请状态 1.新品上架申请 2:修改 3:下架',
`admin_check_status` tinyint(1) NOT NULL COMMENT '管理员审核状态 0 待审核 1 通过 2 拒绝',
PRIMARY KEY (`id`),
KEY `id` (`id`),
KEY `city_id` (`city_id`),
KEY `is_verify` (`is_verify`),
KEY `is_effect` (`is_effect`),
KEY `is_recommend` (`is_recommend`),
KEY `avg_point` (`avg_point`),
KEY `good_dp_count` (`good_dp_count`),
KEY `bad_dp_count` (`bad_dp_count`),
KEY `common_dp_count` (`common_dp_count`),
KEY `total_point` (`total_point`),
KEY `dp_count` (`dp_count`),
KEY `good_rate` (`good_rate`),
KEY `common_rate` (`common_rate`),
KEY `tuan_count` (`tuan_count`),
KEY `event_count` (`event_count`),
KEY `youhui_count` (`youhui_count`),
KEY `daijin_count` (`daijin_count`),
KEY `new_dp_count` (`new_dp_count`),
KEY `is_main` (`is_main`),
KEY `supplier_id` (`supplier_id`),
KEY `search_idx1` (`city_id`,`is_recommend`,`is_effect`,`is_verify`),
KEY `search_idx2` (`city_id`,`avg_point`,`is_effect`),
KEY `search_idx3` (`supplier_id`,`is_main`),
KEY `search_idx4` (`city_id`,`deal_cate_id`,`is_verify`,`is_effect`,`is_recommend`),
KEY `search_idx5` (`city_id`,`deal_cate_id`,`dp_count`,`avg_point`,`ref_avg_price`,`is_effect`,`is_recommend`,`is_verify`),
KEY `search_idx6` (`good_rate`,`is_verify`,`is_effect`),
KEY `sort_default` (`is_recommend`,`is_verify`,`dp_count`),
KEY `ref_avg_price` (`ref_avg_price`),
KEY `shop_count` (`shop_count`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`),
FULLTEXT KEY `deal_cate_match` (`deal_cate_match`),
FULLTEXT KEY `tags_match` (`tags_match`),
FULLTEXT KEY `all_match` (`deal_cate_match`,`locate_match`,`name_match`,`tags_match`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='商户平台提交商家门店临时表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_brand_link`;
CREATE TABLE `%DB_PREFIX%supplier_location_brand_link` (
`brand_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY (`brand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家门店的品牌关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_dp`;
CREATE TABLE `%DB_PREFIX%supplier_location_dp` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL COMMENT '标题',
`content` text COMMENT '内容',
`create_time` int(11) NOT NULL COMMENT '点评时间',
`point` int(11) NOT NULL COMMENT '评分',
`user_id` int(11) NOT NULL COMMENT '点评会员ID',
`is_img` tinyint(1) NOT NULL COMMENT '弃用',
`is_content` tinyint(1) NOT NULL COMMENT '是否有内容',
`is_best` tinyint(1) NOT NULL COMMENT '推荐点评',
`is_top` tinyint(1) NOT NULL COMMENT '弃用',
`status` tinyint(1) NOT NULL COMMENT '状态 0:无效 1:有效',
`good_count` int(11) NOT NULL COMMENT '有用数',
`bad_count` int(11) NOT NULL COMMENT '没用数',
`reply_count` int(11) NOT NULL COMMENT '回复数',
`supplier_location_id` int(11) NOT NULL COMMENT '点评的门店ID',
`avg_price` float(14,4) NOT NULL COMMENT '平均价',
`kb_user_id` varchar(50) NOT NULL COMMENT '弃用',
`kb_create_time` varchar(20) DEFAULT '' COMMENT '弃用',
`kb_tags` varchar(255) DEFAULT '' COMMENT '弃用',
`is_index` tinyint(1) NOT NULL COMMENT '是否置顶',
`is_buy` tinyint(1) NOT NULL COMMENT '弃用',
`from_data` varchar(255) NOT NULL COMMENT '弃用',
`rel_app_index` varchar(255) NOT NULL COMMENT '弃用',
`rel_route` varchar(255) NOT NULL COMMENT '弃用',
`rel_param` varchar(255) NOT NULL COMMENT '弃用',
`message_id` int(11) NOT NULL COMMENT '弃用',
`deal_id` int(11) NOT NULL COMMENT '关联到的商品ID，对商品的点评',
`youhui_id` int(11) NOT NULL COMMENT '关联的优惠券ID',
`reply_content` text NOT NULL COMMENT '管理员或商家回复',
`reply_supplier_account_id` int(11) NOT NULL COMMENT '商家回复的帐号',
`reply_time` int(11) NOT NULL COMMENT '回复时间',
`images_cache` text NOT NULL COMMENT '点评图片的冗余',
`supplier_id` int(11) NOT NULL COMMENT '点评所针对的商家ID',
`event_id` int(11) NOT NULL COMMENT '活动ID',
`dp_type` tinyint(1) NOT NULL COMMENT '已弃用',
`tags_match` text NOT NULL,
`tags_match_row` text NOT NULL,
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`) USING BTREE,
KEY `supplier_location_id` (`supplier_location_id`) USING BTREE,
KEY `is_img` (`is_img`) USING BTREE,
KEY `is_best` (`is_best`) USING BTREE,
KEY `is_top` (`is_top`) USING BTREE,
KEY `good_count` (`good_count`) USING BTREE,
KEY `bad_count` (`bad_count`) USING BTREE,
KEY `reply_count` (`reply_count`) USING BTREE,
KEY `avg_price` (`avg_price`) USING BTREE,
KEY `deal_id` (`deal_id`),
KEY `youhui_id` (`youhui_id`),
KEY `supplier_id` (`supplier_id`),
KEY `dp_type` (`dp_type`),
KEY `is_content` (`is_content`),
FULLTEXT KEY `tags_match` (`tags_match`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商家门店点评数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_dp_images`;
CREATE TABLE `%DB_PREFIX%supplier_location_dp_images` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`image` varchar(255) NOT NULL COMMENT '图片地址',
`dp_id` int(11) NOT NULL COMMENT '所属的点评ID',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`create_time` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY (`id`),
KEY `dp_id` (`dp_id`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评图库';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_dp_point_result`;
CREATE TABLE `%DB_PREFIX%supplier_location_dp_point_result` (
`group_id` int(11) NOT NULL COMMENT '分组ID',
`point` int(11) NOT NULL COMMENT '分数',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
KEY `group_id` (`group_id`) USING BTREE,
KEY `supplier_location_id` (`supplier_location_id`) USING BTREE,
KEY `dp_id` (`dp_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个门店，每条点评针对每个评分分组的点评评分';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_dp_reply`;
CREATE TABLE `%DB_PREFIX%supplier_location_dp_reply` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`dp_id` int(11) NOT NULL COMMENT '点评ID',
`content` text NOT NULL COMMENT '回应内容',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`create_time` int(11) NOT NULL COMMENT '回应时间',
`parent_id` int(11) NOT NULL COMMENT '弃用',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点评数据的回应表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_dp_tag_result`;
CREATE TABLE `%DB_PREFIX%supplier_location_dp_tag_result` (
`tags` varchar(255) NOT NULL COMMENT '标签列表 空格分隔',
`dp_id` int(11) NOT NULL COMMENT '关联的点评ID',
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
KEY `dp_id` (`dp_id`),
KEY `group_id` (`group_id`),
KEY `supplier_location_id` (`supplier_location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家门店按预定义的分组打标签的结果表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_images`;
CREATE TABLE `%DB_PREFIX%supplier_location_images` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`image` varchar(255) NOT NULL COMMENT '图片',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`create_time` int(11) NOT NULL COMMENT '发布时间',
`user_id` int(11) NOT NULL COMMENT '发布人ID(关联的商家会员ID)',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
`dp_id` int(11) NOT NULL COMMENT '弃用',
`good_count` int(11) NOT NULL COMMENT '弃用',
`bad_count` int(11) NOT NULL COMMENT '弃用',
`brief` varchar(255) NOT NULL COMMENT '描述',
`status` tinyint(1) NOT NULL COMMENT '状态 0:未审核  1:已审核 ',
`click_count` int(11) NOT NULL COMMENT '浏览数',
`images_group_id` int(11) NOT NULL COMMENT '图片的分组ID',
PRIMARY KEY (`id`),
KEY `uid` (`user_id`) USING BTREE,
KEY `supplier_location_id` (`supplier_location_id`) USING BTREE,
KEY `dp_id` (`dp_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店图集表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_point_result`;
CREATE TABLE `%DB_PREFIX%supplier_location_point_result` (
`group_id` int(11) NOT NULL COMMENT '评分分组ID',
`avg_point` float(14,4) NOT NULL COMMENT '平均分',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
`total_point` int(11) NOT NULL COMMENT '总分',
PRIMARY KEY (`group_id`,`supplier_location_id`),
KEY `group_id` (`group_id`) USING BTREE,
KEY `dp_id` (`total_point`) USING BTREE,
KEY `avg_point` (`avg_point`) USING BTREE,
KEY `total_point` (`total_point`) USING BTREE,
KEY `supplier_location_id` (`supplier_location_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店的分组评分的评分结果';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_location_sign_log`;
CREATE TABLE `%DB_PREFIX%supplier_location_sign_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`sign_time` int(11) NOT NULL COMMENT '签到时间',
`point` int(11) NOT NULL COMMENT '签到打分',
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`),
KEY `location_id` (`location_id`),
KEY `sign_time` (`sign_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店签到数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_money_log`;
CREATE TABLE `%DB_PREFIX%supplier_money_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL COMMENT '资金变更记录',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`create_time` int(11) NOT NULL COMMENT '变更时间',
`money` decimal(20,4) NOT NULL COMMENT '资金变更数额',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:销售额增加 1:资金冻结 2.待结算增加 3.已结算增加 4.退款增加 5.提现增加',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家财务明细表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_money_submit`;
CREATE TABLE `%DB_PREFIX%supplier_money_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`money` decimal(20,4) NOT NULL COMMENT '提现金额',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`create_time` int(11) NOT NULL COMMENT '提现申请时间',
`status` tinyint(1) NOT NULL COMMENT '状态 0:待审核 1:已确认提现 2:拒绝',
`reason` text NOT NULL COMMENT '拒绝的理由',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家提现表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_statements`;
CREATE TABLE `%DB_PREFIX%supplier_statements` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`supplier_id` int(11) NOT NULL,
`money` decimal(20,4) NOT NULL COMMENT '本日消费（提现不减）',
`sale_money` decimal(20,4) NOT NULL COMMENT '本日销售额',
`refund_money` decimal(20,4) NOT NULL COMMENT '本日退款',
`wd_money` decimal(20,4) NOT NULL COMMENT '本日提现',
`stat_time` date NOT NULL COMMENT '报表日期',
`stat_month` varchar(10) NOT NULL COMMENT '月份',
PRIMARY KEY (`id`),
KEY `supplier_id` (`supplier_id`),
KEY `stat_time` (`stat_time`),
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商家日报表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_submit`;
CREATE TABLE `%DB_PREFIX%supplier_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '商家名称',
`cate_config` text NOT NULL COMMENT '所属分类配置',
`location_config` text NOT NULL COMMENT '所属地区商券配置',
`address` varchar(255) NOT NULL COMMENT '地址',
`tel` varchar(255) NOT NULL COMMENT '电话',
`open_time` varchar(255) NOT NULL COMMENT '营业时间',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`location_id` int(11) NOT NULL COMMENT '认领的门店ID',
`is_publish` tinyint(1) NOT NULL COMMENT '0:未审核 1:已审核',
`user_id` int(1) NOT NULL COMMENT '入驻申请的会员ID',
`create_time` int(11) NOT NULL COMMENT '申请时间',
`h_name` varchar(255) NOT NULL COMMENT '企业名称',
`h_faren` varchar(255) NOT NULL COMMENT '法人',
`h_license` varchar(255) NOT NULL COMMENT '营业执照',
`h_other_license` varchar(255) NOT NULL COMMENT '其他资质上传',
`h_user_name` varchar(255) NOT NULL COMMENT '店铺管理员姓名',
`h_tel` varchar(255) NOT NULL COMMENT '存档的联系人电话',
`h_supplier_logo` varchar(255) NOT NULL COMMENT '商户商标图',
`h_supplier_image` varchar(255) NOT NULL COMMENT '门店图片',
`city_id` int(11) NOT NULL COMMENT '所在城市',
`h_bank_info` text NOT NULL COMMENT '提现银行帐号',
`h_bank_user` varchar(255) NOT NULL COMMENT '提现银行户名',
`h_bank_name` varchar(255) NOT NULL COMMENT '提现银行名称',
`account_name` varchar(255) NOT NULL COMMENT '商户登录账户',
`account_password` varchar(255) NOT NULL COMMENT '登录密码',
`account_mobile` varchar(255) DEFAULT NULL COMMENT '账户绑定手机号，用于验证，提现等功能',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家入驻申请表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_tag`;
CREATE TABLE `%DB_PREFIX%supplier_tag` (
`tag_name` varchar(255) NOT NULL COMMENT '标签',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
`group_id` int(11) NOT NULL COMMENT '关联商户子类标签分组的ID(可为前台会员点评的分组标签，也可为后台管理员编辑的分组标签), 为0时为主显标签',
`total_count` int(11) NOT NULL COMMENT '同商户，同类分组提交的次数。 用于表示该标签的热门度',
KEY `merchant_id` (`supplier_location_id`) USING BTREE,
KEY `group_id` (`group_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店分组标签的点评数据统计表';
DROP TABLE IF EXISTS `%DB_PREFIX%supplier_tag_group_preset`;
CREATE TABLE `%DB_PREFIX%supplier_tag_group_preset` (
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`supplier_location_id` int(11) NOT NULL COMMENT '门店ID',
`preset` text NOT NULL COMMENT '预选标签 空格分开'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店分组点评标签的预选标签配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%tag_group`;
CREATE TABLE `%DB_PREFIX%tag_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分组名称',
`preset` text NOT NULL COMMENT '公用的预设标签，如门店没有单独配置预选标签，以此为准',
`sort` int(11) NOT NULL COMMENT '排序 小到大',
`memo` varchar(255) NOT NULL COMMENT '弃用',
`tags` text NOT NULL COMMENT '弃用',
`allow_dp` tinyint(1) NOT NULL COMMENT '该分组标签是否可用于用户自主填写（0:否 1:是）',
`allow_search` tinyint(1) NOT NULL COMMENT '该分组内的标签是否用于商家搜索使用',
`allow_vote` tinyint(1) NOT NULL COMMENT '是否允许对标签进行直接投票点评(0:否 1:是)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='供门店点评的标签分组配置';
DROP TABLE IF EXISTS `%DB_PREFIX%tag_group_elink`;
CREATE TABLE `%DB_PREFIX%tag_group_elink` (
`tag_group_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
KEY `tag_id` (`tag_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签分组与活动大分类关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%tag_group_link`;
CREATE TABLE `%DB_PREFIX%tag_group_link` (
`tag_group_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
KEY `tag_id` (`tag_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签分组与生活服务大分类关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%tag_group_slink`;
CREATE TABLE `%DB_PREFIX%tag_group_slink` (
`tag_group_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
KEY `tag_id` (`tag_group_id`) USING BTREE,
KEY `type_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签分组与商城大分类关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%tag_user_vote`;
CREATE TABLE `%DB_PREFIX%tag_user_vote` (
`user_id` int(11) NOT NULL COMMENT '会员ID',
`tag_name` varchar(255) NOT NULL COMMENT '投票的标签',
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`location_id` int(11) NOT NULL COMMENT '所属门店ID',
PRIMARY KEY (`user_id`,`tag_name`,`group_id`,`location_id`),
KEY `user_id` (`user_id`),
KEY `tag_name` (`tag_name`),
KEY `location_id` (`location_id`),
KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用于投票的标签的投票结果';
DROP TABLE IF EXISTS `%DB_PREFIX%topic`;
CREATE TABLE `%DB_PREFIX%topic` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL COMMENT '标题',
`forum_title` varchar(255) NOT NULL COMMENT '来源于小组的分享的贴子标题',
`content` text NOT NULL COMMENT '内容',
`create_time` int(11) NOT NULL COMMENT '发表时间',
`type` varchar(255) NOT NULL COMMENT 'share 分享\r\ndealcomment 商品点评	\r\nyouhuicomment 优惠券购物点评\r\neventcomment 活动点评\r\nslocationcomment  门店点评\r\neventsubmit  活动报名	\r\nsharedeal  分享商品\r\nshareyouhui 分享优惠券	\r\nshareevent分享活劝',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`user_name` varchar(255) NOT NULL COMMENT '会员名',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`relay_id` int(11) NOT NULL COMMENT '0:原创 1:转发来源的贴子ID',
`origin_id` int(11) NOT NULL COMMENT '原创贴子ID',
`reply_count` int(11) NOT NULL COMMENT '回复数',
`relay_count` int(11) NOT NULL COMMENT '转发数',
`good_count` int(11) NOT NULL COMMENT '弃用',
`bad_count` int(11) NOT NULL COMMENT '弃用',
`click_count` int(11) NOT NULL COMMENT '查看数量',
`rel_app_index` varchar(255) NOT NULL COMMENT '相关链接的app名',
`rel_route` varchar(255) NOT NULL COMMENT '相关链接的url的route名',
`rel_param` varchar(255) NOT NULL COMMENT '相关链接的url参数',
`message_id` int(11) NOT NULL COMMENT '弃用',
`topic_group` varchar(255) NOT NULL DEFAULT 'share' COMMENT '一键分享的插件名称（默认为share）',
`fav_id` int(11) NOT NULL COMMENT '喜欢XX分享的分享ID',
`fav_count` int(11) NOT NULL COMMENT '被喜欢的数量',
`user_name_match` text NOT NULL COMMENT '用于全文索引的用户名(名括@会员的相关名称)',
`user_name_match_row` text NOT NULL,
`keyword_match` text NOT NULL COMMENT '分词后与标签的相关关键词全文索引',
`keyword_match_row` text NOT NULL,
`xpoint` varchar(255) NOT NULL COMMENT '手机发表的经度',
`ypoint` varchar(255) NOT NULL COMMENT '手机发表的纬度',
`tags` text NOT NULL COMMENT '标签，空格分开',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐到达人秀页面',
`has_image` tinyint(1) NOT NULL COMMENT '是否含图',
`source_type` tinyint(1) NOT NULL COMMENT '0:本站 1:外站',
`source_name` varchar(255) NOT NULL COMMENT '发表的来源（如网站/手机端）',
`source_url` varchar(255) NOT NULL,
`group_data` text NOT NULL COMMENT 'group插件采集同步的序列化数据',
`daren_page` varchar(255) NOT NULL COMMENT '达人秀页面的专用图',
`group_id` int(11) NOT NULL COMMENT '小组ID',
`is_top` tinyint(1) NOT NULL COMMENT '置顶',
`is_best` tinyint(1) NOT NULL COMMENT '精华',
`op_memo` text NOT NULL COMMENT '前台操作员操作日志',
`last_time` int(11) NOT NULL COMMENT '最后操作时间',
`last_user_id` int(11) NOT NULL COMMENT '最后操作人ID',
`cate_match` text NOT NULL COMMENT '所属的主题分类的全文索引(分类可由后台分配，也可由分类预设标签自动匹配)',
`cate_match_row` text NOT NULL,
`origin_topic_data` text NOT NULL COMMENT '原贴数据缓存',
`images_count` int(11) NOT NULL COMMENT '图片数',
`image_list` text NOT NULL COMMENT '图片列表缓存',
`is_cached` tinyint(1) NOT NULL COMMENT '标识相关的数据是否已缓存（包括原贴数据，图片集，小组数据等）',
`topic_group_data` text NOT NULL COMMENT '小组数据缓存',
PRIMARY KEY (`id`),
KEY `create_time` (`create_time`),
KEY `user_id` (`user_id`),
KEY `is_recommend` (`is_recommend`),
KEY `group_id` (`group_id`),
KEY `is_top` (`is_top`),
KEY `is_best` (`is_best`),
KEY `has_image` (`has_image`),
KEY `fav_id` (`fav_id`),
KEY `relay_id` (`relay_id`),
KEY `origin_id` (`origin_id`),
KEY `type` (`type`),
KEY `is_effect` (`is_effect`),
KEY `is_delete` (`is_delete`),
KEY `click_count` (`click_count`),
KEY `last_time` (`last_time`),
KEY `ordery_sort` (`create_time`,`is_top`),
KEY `last_time_sort` (`last_time`,`is_top`),
KEY `multi_key` (`is_effect`,`is_delete`,`last_time`,`is_recommend`,`group_id`,`is_top`,`is_best`,`create_time`),
FULLTEXT KEY `user_name_match` (`user_name_match`),
FULLTEXT KEY `keyword_match` (`keyword_match`),
FULLTEXT KEY `cate_match` (`cate_match`),
FULLTEXT KEY `all_match` (`keyword_match`,`cate_match`)
) ENGINE=MyISAM AUTO_INCREMENT=203 DEFAULT CHARSET=utf8 COMMENT='会员分享数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_cate_link`;
CREATE TABLE `%DB_PREFIX%topic_cate_link` (
`topic_id` int(11) NOT NULL,
`cate_id` int(11) NOT NULL,
PRIMARY KEY (`topic_id`,`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分享与分享分类的关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_group`;
CREATE TABLE `%DB_PREFIX%topic_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '小组名称',
`memo` text NOT NULL COMMENT '小组说明',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`create_time` int(11) NOT NULL COMMENT '创建时间',
`cate_id` int(11) NOT NULL COMMENT '所属小组分类ID',
`user_count` int(11) NOT NULL COMMENT '组员数量',
`topic_count` int(11) NOT NULL COMMENT '贴子总数',
`icon` varchar(255) NOT NULL COMMENT '小组图标',
`image` varchar(255) NOT NULL COMMENT '小组大图',
`is_effect` tinyint(1) NOT NULL COMMENT '是否验证通过',
`user_id` int(11) NOT NULL COMMENT '组长ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='小组（论坛）版块表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_group_cate`;
CREATE TABLE `%DB_PREFIX%topic_group_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分类名称',
`sort` int(11) NOT NULL COMMENT '排序大到小',
`icon` varchar(255) NOT NULL COMMENT '弃用',
`group_count` int(11) NOT NULL COMMENT '分类下的小组量数',
`is_effect` tinyint(1) NOT NULL COMMENT '分类是否显示 0:否 1:是',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='小组分类表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_image`;
CREATE TABLE `%DB_PREFIX%topic_image` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`topic_id` int(11) NOT NULL COMMENT '主题ID',
`name` varchar(255) NOT NULL COMMENT '文件名',
`filesize` int(11) NOT NULL COMMENT '文件大小',
`create_time` int(11) NOT NULL COMMENT '上传时间',
`user_id` int(11) NOT NULL COMMENT '所属会员ID',
`user_name` varchar(255) NOT NULL COMMENT '所属会员名',
`path` varchar(255) NOT NULL COMMENT '小图路径',
`topic_table` varchar(255) NOT NULL COMMENT '主题表名',
`o_path` varchar(255) NOT NULL COMMENT '原图路径',
`width` int(11) NOT NULL COMMENT '图片宽px',
`height` int(11) NOT NULL COMMENT '图片高px',
PRIMARY KEY (`id`),
KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 COMMENT='分享主题的相关图片数据表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_reply`;
CREATE TABLE `%DB_PREFIX%topic_reply` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`topic_id` int(11) NOT NULL COMMENT '主题ID',
`content` text NOT NULL COMMENT '回复内容',
`user_id` int(11) NOT NULL COMMENT '回复人ID',
`user_name` varchar(255) NOT NULL COMMENT '回复人用户名',
`reply_id` int(11) NOT NULL COMMENT '被回应的回复的ID',
`reply_user_id` int(11) NOT NULL COMMENT '被回应的回复的用户ID',
`reply_user_name` varchar(255) NOT NULL COMMENT '被回应的回复的用户名',
`create_time` int(11) NOT NULL COMMENT '回复时间',
`is_effect` tinyint(1) NOT NULL,
`is_delete` tinyint(1) NOT NULL,
PRIMARY KEY (`id`),
KEY `reply_id` (`reply_id`),
KEY `topic_id` (`topic_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='主题回复表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_tag`;
CREATE TABLE `%DB_PREFIX%topic_tag` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '标签名称',
`is_recommend` tinyint(1) NOT NULL COMMENT '是否推荐, 标为推荐将默认显示在发现栏目的全部分类的标签中',
`count` int(11) NOT NULL COMMENT '与该标签相关的分享主题数',
`is_preset` tinyint(1) NOT NULL COMMENT '是否为预设标签(预选标签在会员中心发分享时可提供给用户选取)',
`color` varchar(10) NOT NULL COMMENT '发现栏目显示标签的颜色',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='主题检索用的标签集';
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('1','电影','1','2','1','','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('2','自助游','1','0','1','','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('3','闽菜','1','0','1','','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('4','川菜','1','0','1','','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('5','咖啡','1','0','1','#fff100','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('6','牛排','1','0','1','#a1410d','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('7','包包','1','0','0','#ed008c','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('8','复古','1','0','0','#a36209','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('9','甜美','1','0','0','','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('10','日系','1','0','0','#a4d49d','0');
INSERT INTO `%DB_PREFIX%topic_tag` VALUES ('11','欧美','1','0','0','#ee1d24','0');
DROP TABLE IF EXISTS `%DB_PREFIX%topic_tag_cate`;
CREATE TABLE `%DB_PREFIX%topic_tag_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '分类名',
`sub_name` varchar(255) NOT NULL COMMENT '附标题(手机端使用)',
`mobile_title_bg` varchar(255) NOT NULL COMMENT '手机分类背景图',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`showin_mobile` tinyint(1) NOT NULL COMMENT '是否显示在网站',
`showin_web` tinyint(1) NOT NULL COMMENT '是否显示在手机端',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='分享标签的分类表';
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('1','休闲娱乐','','','0','1','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('2','乐享美食','','','0','1','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('3','旅游酒店','','','0','1','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('4','都市购物','','','0','1','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('5','幸福居家','','','1','0','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('6','浪漫婚恋','','','2','0','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate` VALUES ('7','玩乐帮派','','','3','0','1');
DROP TABLE IF EXISTS `%DB_PREFIX%topic_tag_cate_link`;
CREATE TABLE `%DB_PREFIX%topic_tag_cate_link` (
`cate_id` int(11) NOT NULL,
`tag_id` int(11) NOT NULL,
PRIMARY KEY (`cate_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分类与预设标签的关联表，主题的自动分类依据该表';
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('1','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('2','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('3','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('4','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('5','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('6','11');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','1');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','2');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','3');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','4');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','5');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','6');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','7');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','8');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','9');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','10');
INSERT INTO `%DB_PREFIX%topic_tag_cate_link` VALUES ('7','11');
DROP TABLE IF EXISTS `%DB_PREFIX%topic_title`;
CREATE TABLE `%DB_PREFIX%topic_title` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '话题名称',
`type` tinyint(1) NOT NULL COMMENT '0:主题1:活动',
`is_recommend` tinyint(1) NOT NULL COMMENT '是否为推荐',
`count` int(11) NOT NULL COMMENT '话题中的主题数量',
`color` varchar(10) NOT NULL COMMENT '显示的颜色',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`,`type`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='分享主题的话题列表';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_title_cate_link`;
CREATE TABLE `%DB_PREFIX%topic_title_cate_link` (
`title_id` int(11) NOT NULL,
`cate_id` int(11) NOT NULL,
PRIMARY KEY (`title_id`,`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题与分类的关联表，也用于分享的自动分类用';
DROP TABLE IF EXISTS `%DB_PREFIX%topic_vote_log`;
CREATE TABLE `%DB_PREFIX%topic_vote_log` (
`user_id` int(11) NOT NULL,
`topic_id` int(11) NOT NULL,
`vote_count` int(11) NOT NULL,
KEY `user_id` (`user_id`),
KEY `topic_id` (`topic_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分享的投票表';
DROP TABLE IF EXISTS `%DB_PREFIX%urls`;
CREATE TABLE `%DB_PREFIX%urls` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`url` text NOT NULL COMMENT '外链的url',
`count` int(11) NOT NULL COMMENT '该链接被点击的次数',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='分享中关于外链的加密存储表';
DROP TABLE IF EXISTS `%DB_PREFIX%user`;
CREATE TABLE `%DB_PREFIX%user` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_name` varchar(255) DEFAULT NULL COMMENT '会员名',
`user_pwd` varchar(255) NOT NULL COMMENT '会员密码',
`create_time` int(11) NOT NULL COMMENT '注册时间',
`update_time` int(11) NOT NULL COMMENT '修改时间',
`login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
`group_id` int(11) NOT NULL COMMENT '会员组ID',
`is_effect` tinyint(1) NOT NULL COMMENT '是否被禁用（未验证）',
`is_delete` tinyint(1) NOT NULL COMMENT '删除',
`email` varchar(255) DEFAULT NULL COMMENT '会员邮件',
`mobile` varchar(255) DEFAULT NULL COMMENT '会员手机号',
`score` int(11) NOT NULL COMMENT '会员积分',
`total_score` int(11) NOT NULL COMMENT '累积积分：用于会员组升级',
`money` double(20,4) NOT NULL COMMENT '会员余额',
`verify` varchar(255) NOT NULL COMMENT '验证注册生效时生成的验证码',
`code` varchar(255) NOT NULL COMMENT '登录用的标识码(md5加密前缀)',
`pid` int(11) NOT NULL COMMENT '推荐人ID',
`login_time` int(11) NOT NULL COMMENT '最后登录时间',
`referral_count` int(11) NOT NULL COMMENT '返利数量',
`password_verify` varchar(255) NOT NULL COMMENT '取回密码的验证码',
`integrate_id` int(11) NOT NULL COMMENT '会员整合的用户ID（如uc中的会员ID）',
`sina_id` varchar(255) NOT NULL COMMENT '新浪同步的会员ID',
`renren_id` varchar(255) NOT NULL COMMENT '预留',
`kaixin_id` varchar(255) NOT NULL COMMENT '预留',
`sohu_id` varchar(255) NOT NULL COMMENT '预留',
`lottery_mobile` varchar(255) NOT NULL COMMENT '抽奖时用的手机号',
`lottery_verify` varchar(255) NOT NULL COMMENT '抽奖手机的验证码',
`verify_create_time` int(11) NOT NULL COMMENT '抽奖手机验证码生成时间',
`tencent_id` varchar(255) NOT NULL COMMENT '腾讯微博ID',
`referer` varchar(255) NOT NULL COMMENT '会员来路',
`login_pay_time` int(11) NOT NULL COMMENT '弃用',
`focus_count` int(11) NOT NULL COMMENT '关注别人的数量',
`focused_count` int(11) NOT NULL COMMENT '粉丝数',
`province_id` int(11) NOT NULL COMMENT '所属省份ID',
`city_id` int(11) NOT NULL COMMENT '所属城市 ID',
`sex` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '性别',
`my_intro` varchar(255) NOT NULL COMMENT '个人简介',
`is_merchant` tinyint(1) NOT NULL COMMENT '是否绑定商家',
`merchant_name` varchar(255) NOT NULL COMMENT '商家帐号名',
`is_daren` tinyint(1) NOT NULL COMMENT '达人标识 ',
`daren_title` varchar(255) NOT NULL COMMENT '达人称号',
`step` tinyint(1) NOT NULL COMMENT '新手已完成步骤',
`byear` int(4) NOT NULL COMMENT '生日年',
`bmonth` int(2) NOT NULL COMMENT '生日月',
`bday` int(2) NOT NULL COMMENT '生日日',
`locate_time` int(11) DEFAULT '0' COMMENT '用户最后登陆时间',
`xpoint` float(10,6) DEFAULT '0.000000' COMMENT '用户最后登陆x座标',
`ypoint` float(10,6) DEFAULT '0.000000' COMMENT '用户最后登陆y座标',
`topic_count` int(11) NOT NULL COMMENT '主题数',
`fav_count` int(11) NOT NULL COMMENT '喜欢数',
`faved_count` int(11) NOT NULL COMMENT '被喜欢数',
`dp_count` int(11) NOT NULL COMMENT '点评总数',
`insite_count` int(11) NOT NULL COMMENT '站点分享数总（本站的商品等数据）',
`outsite_count` int(11) NOT NULL COMMENT '外站的分享数（如有实现的淘宝分享等）',
`level_id` int(11) NOT NULL COMMENT '等级ID',
`point` int(11) NOT NULL COMMENT '经验值',
`sina_app_key` varchar(255) NOT NULL COMMENT '新浪的同步验证key',
`sina_app_secret` varchar(255) NOT NULL COMMENT '新浪的同步验证密码',
`is_syn_sina` tinyint(1) NOT NULL COMMENT '是否同步发微博到新浪',
`tencent_app_key` varchar(255) NOT NULL COMMENT '腾讯的同步验证key',
`tencent_app_secret` varchar(255) NOT NULL COMMENT '腾讯的同步验证密码',
`is_syn_tencent` tinyint(1) NOT NULL COMMENT '是否同步发微博到腾讯',
`sina_token` varchar(255) NOT NULL COMMENT '新浪的授权码',
`t_access_token` varchar(255) NOT NULL COMMENT '腾讯微博授权码',
`t_openkey` varchar(255) NOT NULL COMMENT '腾讯微博的openkey',
`t_openid` varchar(255) NOT NULL COMMENT '腾讯微博OPENID',
`avatar` varchar(255) NOT NULL DEFAULT './public/avatar/noavatar.gif',
`is_tmp` tinyint(1) NOT NULL COMMENT '表示是否为临时用户（如手机注册）',
`qqv2_id` varchar(255) NOT NULL,
`qq_token` varchar(255) NOT NULL,
`t_name` varchar(255) NOT NULL,
`dev_type` varchar(20) DEFAULT 'android' COMMENT 'android,ios 客户手机类型,一个客户只绑定一个最新的手机',
`device_token` varchar(255) DEFAULT NULL COMMENT '推送device_token一个客户只绑定一个最新的手机',
`wx_openid` varchar(255) NOT NULL COMMENT '微信OPENID',
`real_name`  varchar(255) NOT NULL COMMENT '会员真实姓名',
PRIMARY KEY (`id`),
UNIQUE KEY `unk_user_name` (`user_name`),
UNIQUE KEY `unk_email` (`email`),
UNIQUE KEY `unk_mobile` (`mobile`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COMMENT='会员表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_active_log`;
CREATE TABLE `%DB_PREFIX%user_active_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`create_time` int(11) NOT NULL COMMENT '发生时间',
`point` int(11) NOT NULL COMMENT '经验',
`score` int(11) NOT NULL COMMENT '积分',
`money` double(11,4) NOT NULL COMMENT '钱',
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='关于会员活跃度的帐号变更日志';
DROP TABLE IF EXISTS `%DB_PREFIX%user_auth`;
CREATE TABLE `%DB_PREFIX%user_auth` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`m_name` varchar(255) NOT NULL,
`a_name` varchar(255) NOT NULL,
`rel_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='会员前台操作权限的配置表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_cate_link`;
CREATE TABLE `%DB_PREFIX%user_cate_link` (
`user_id` int(11) NOT NULL,
`cate_id` int(11) NOT NULL,
PRIMARY KEY (`user_id`,`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='达人会员所属的分类';
DROP TABLE IF EXISTS `%DB_PREFIX%user_consignee`;
CREATE TABLE `%DB_PREFIX%user_consignee` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`region_lv1` int(11) NOT NULL COMMENT '国',
`region_lv2` int(11) NOT NULL COMMENT '省',
`region_lv3` int(11) NOT NULL COMMENT '市',
`region_lv4` int(11) NOT NULL COMMENT '地区',
`address` text NOT NULL COMMENT '地址',
`mobile` varchar(255) NOT NULL COMMENT '手机',
`zip` varchar(255) NOT NULL COMMENT '邮编',
`consignee` varchar(255) NOT NULL COMMENT '收货人',
`is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为默认配送地址',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='会员的收货地址';
DROP TABLE IF EXISTS `%DB_PREFIX%user_extend`;
CREATE TABLE `%DB_PREFIX%user_extend` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`field_id` int(11) NOT NULL COMMENT '扩展字段ID',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`value` varchar(255) NOT NULL COMMENT '值',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='会员扩展字段的资料';
DROP TABLE IF EXISTS `%DB_PREFIX%user_field`;
CREATE TABLE `%DB_PREFIX%user_field` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`field_name` varchar(255) NOT NULL COMMENT '字段名（代码）',
`field_show_name` varchar(255) NOT NULL COMMENT '字段显示用的名称',
`input_type` tinyint(1) NOT NULL COMMENT '0:手动输入 1：预选下拉',
`value_scope` text NOT NULL COMMENT '预选下拉的预选值,以逗号分隔',
`is_must` tinyint(1) NOT NULL COMMENT '是否必填',
`sort` int(11) NOT NULL COMMENT '排序大到小',
PRIMARY KEY (`id`),
UNIQUE KEY `unk_field_name` (`field_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='会员的扩展字段';
DROP TABLE IF EXISTS `%DB_PREFIX%user_focus`;
CREATE TABLE `%DB_PREFIX%user_focus` (
`focus_user_id` int(11) NOT NULL COMMENT '关注人ID',
`focused_user_id` int(11) NOT NULL COMMENT '被关注人ID',
`focus_user_name` varchar(255) NOT NULL COMMENT '关注人用户名',
`focused_user_name` varchar(255) NOT NULL COMMENT '被关注人用户名',
`to_focus` tinyint(1) DEFAULT NULL,
PRIMARY KEY (`focus_user_id`,`focused_user_id`),
KEY `focus_user_id` (`focus_user_id`),
KEY `focused_user_id` (`focused_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员关注、被关注表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_frequented`;
CREATE TABLE `%DB_PREFIX%user_frequented` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uid` int(11) DEFAULT '0' COMMENT '员会ID',
`title` varchar(50) DEFAULT NULL,
`addr` varchar(255) DEFAULT NULL,
`xpoint` float(12,6) DEFAULT '0.000000' COMMENT 'longitude',
`ypoint` float(12,6) DEFAULT '0.000000' COMMENT 'latitude',
`latitude_top` float(12,6) DEFAULT NULL,
`latitude_bottom` float(12,6) DEFAULT NULL,
`longitude_left` float(12,6) DEFAULT NULL,
`longitude_right` float(12,6) DEFAULT NULL,
`zoom_level` int(5) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机端附近会员的记录表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_group`;
CREATE TABLE `%DB_PREFIX%user_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '会员组名',
`score` int(11) NOT NULL COMMENT '所需积分',
`discount` double(20,4) NOT NULL COMMENT '享受的折扣',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员组配置表';
INSERT INTO `%DB_PREFIX%user_group` VALUES ('1','普通会员','0','1.0000');
INSERT INTO `%DB_PREFIX%user_group` VALUES ('2','VIP会员','8888','0.8000');
DROP TABLE IF EXISTS `%DB_PREFIX%user_level`;
CREATE TABLE `%DB_PREFIX%user_level` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '等级名称',
`point` int(11) NOT NULL COMMENT '所需经验值',
PRIMARY KEY (`id`),
UNIQUE KEY `unk` (`point`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='会员等级表';
INSERT INTO `%DB_PREFIX%user_level` VALUES ('1','幼儿园','0');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('2','小学生','1000');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('3','中学生','2000');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('4','大学生','5000');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('5','研究生','10000');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('6','博士生','50000');
DROP TABLE IF EXISTS `%DB_PREFIX%user_log`;
CREATE TABLE `%DB_PREFIX%user_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL COMMENT '日志内容',
`log_time` int(11) NOT NULL COMMENT '发生时间',
`log_admin_id` int(11) NOT NULL COMMENT '操作管理员的ID',
`log_user_id` int(11) NOT NULL COMMENT '操作的前台会员ID',
`money` double(20,4) NOT NULL COMMENT '相关的钱',
`score` int(11) NOT NULL COMMENT '相关的积分',
`point` int(11) NOT NULL COMMENT '相关的经验',
`user_id` int(11) NOT NULL COMMENT '会员ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COMMENT='会员的资金、积分、经验日志';
DROP TABLE IF EXISTS `%DB_PREFIX%user_medal`;
CREATE TABLE `%DB_PREFIX%user_medal` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`medal_id` int(11) NOT NULL COMMENT '勋章ID',
`name` varchar(255) NOT NULL COMMENT '勋章名称',
`create_time` int(11) NOT NULL COMMENT '获取时间',
`is_delete` tinyint(1) NOT NULL COMMENT '是否被删除',
`icon` varchar(255) NOT NULL COMMENT '勋章图片',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员勋章表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_sign_log`;
CREATE TABLE `%DB_PREFIX%user_sign_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`sign_date` int(11) NOT NULL COMMENT '签到时间',
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员签到日志';
DROP TABLE IF EXISTS `%DB_PREFIX%user_topic_group`;
CREATE TABLE `%DB_PREFIX%user_topic_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`group_id` int(11) NOT NULL COMMENT '加入小组的时间',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`create_time` int(11) NOT NULL COMMENT '加入的时间',
`type` tinyint(1) NOT NULL COMMENT '0:普通组员 1:管理员',
PRIMARY KEY (`id`),
UNIQUE KEY `unk` (`group_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员加入的小组表';
DROP TABLE IF EXISTS `%DB_PREFIX%user_x_y_point`;
CREATE TABLE `%DB_PREFIX%user_x_y_point` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uid` int(11) NOT NULL,
`xpoint` float(14,6) NOT NULL DEFAULT '0.000000',
`ypoint` float(14,6) NOT NULL DEFAULT '0.000000',
`locate_time` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员手机端地理定位记录';
DROP TABLE IF EXISTS `%DB_PREFIX%vote`;
CREATE TABLE `%DB_PREFIX%vote` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '调查的项目名称',
`begin_time` int(11) NOT NULL COMMENT '开始时间',
`end_time` int(11) NOT NULL COMMENT '结束时间',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`sort` int(11) NOT NULL,
`description` text NOT NULL COMMENT '描述',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='在线调查表';
DROP TABLE IF EXISTS `%DB_PREFIX%vote_ask`;
CREATE TABLE `%DB_PREFIX%vote_ask` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '投票项名称',
`type` tinyint(1) NOT NULL COMMENT '投票类型，单选多选/自定义可叠加 1:单选 2:多选 3:自定义',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`vote_id` int(11) NOT NULL COMMENT '调查ID',
`val_scope` text NOT NULL COMMENT '预选范围 逗号分开',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='在线调查投票项设置';
DROP TABLE IF EXISTS `%DB_PREFIX%vote_result`;
CREATE TABLE `%DB_PREFIX%vote_result` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '投票的名称',
`count` int(11) NOT NULL COMMENT '计数',
`vote_id` int(11) NOT NULL COMMENT '调查项ID',
`vote_ask_id` int(11) NOT NULL COMMENT '投票项（问题）ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='投票结果表';
DROP TABLE IF EXISTS `%DB_PREFIX%weight_unit`;
CREATE TABLE `%DB_PREFIX%weight_unit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '重量名称',
`rate` decimal(20,4) NOT NULL COMMENT '换算比率(标准为1)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='重量单位配置表';
INSERT INTO `%DB_PREFIX%weight_unit` VALUES ('1','克','1.0000');
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_account`;
CREATE TABLE `%DB_PREFIX%weixin_account` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`appid` varchar(255) NOT NULL COMMENT 'AppID(应用ID)-第三方平台指 授权方appid',
`appsecret` varchar(255) NOT NULL COMMENT 'AppSecret(应用密钥)-第三方平台无用',
`app_url` varchar(255) NOT NULL COMMENT 'URL(服务器地址)-第三方平台无用',
`app_token` varchar(255) NOT NULL COMMENT 'Token(令牌)-第三方平台无用',
`app_encodingAESKey` varchar(255) NOT NULL COMMENT 'EncodingAESKey(消息加解密密钥)-第三方平台无用',
`authorizer_appid` varchar(255) NOT NULL COMMENT '授权方appid',
`authorizer_access_token` varchar(255) NOT NULL COMMENT '授权方令牌-第三方平台无用',
`expires_in` int(11) NOT NULL COMMENT '授权方令牌 有效时间-第三方平台无用',
`authorizer_refresh_token` varchar(255) NOT NULL COMMENT '刷新令牌-第三方平台',
`func_info` text NOT NULL COMMENT '公众号授权给开发者的权限集列表',
`verify_type_info` tinyint(1) NOT NULL COMMENT '授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证',
`service_type_info` tinyint(1) NOT NULL COMMENT '授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号',
`nick_name` varchar(255) NOT NULL,
`user_name` varchar(255) NOT NULL COMMENT '授权方公众号的原始ID',
`authorizer_info` varchar(255) NOT NULL COMMENT '授权方昵称',
`head_img` varchar(255) NOT NULL COMMENT '授权方头像',
`alias` varchar(255) NOT NULL COMMENT '授权方公众号所设置的微信号，可能为空',
`qrcode_url` varchar(255) NOT NULL COMMENT '二维码图片的URL，开发者最好自行也进行保存',
`location_report` tinyint(1) NOT NULL COMMENT '地理位置上报选项 0 无上报 1 进入会话时上报 2 每5s上报',
`voice_recognize` tinyint(1) NOT NULL COMMENT '语音识别开关选项 0 关闭语音识别 1 开启语音识别',
`customer_service` tinyint(1) NOT NULL COMMENT '客服开关选项 0 关闭多客服 1 开启多客服',
`is_authorized` tinyint(1) NOT NULL DEFAULT '0' COMMENT '授权方是否取消授权 0表示取消授权 1表示授权',
`user_id` int(11) NOT NULL COMMENT '商户ID ，诺type为1，user_id 为空',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 表示前台商户会员 1 表示后台管理员',
`industry_1` int(11) NOT NULL,
`industry_1_status` tinyint(1) NOT NULL,
`industry_2` int(11) NOT NULL,
`industry_2_status` tinyint(1) NOT NULL,
`test_user` varchar(255) DEFAULT NULL COMMENT '测试微信号',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `authorizer_appid` (`authorizer_appid`),
UNIQUE KEY `user_id` (`user_id`),
KEY `appid` (`appid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='//微信公众号列表';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_api_get_record`;
CREATE TABLE `%DB_PREFIX%weixin_api_get_record` (
`openid` varchar(255) NOT NULL,
`user_id` int(11) NOT NULL,
`account_id` int(11) NOT NULL,
`create_time` int(11) NOT NULL,
PRIMARY KEY (`openid`),
KEY `account_id` (`account_id`),
KEY `idx_0` (`account_id`,`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='请求的用户记录';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_conf`;
CREATE TABLE `%DB_PREFIX%weixin_conf` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`name` varchar(255) NOT NULL,
`value` text NOT NULL,
`group_id` int(11) NOT NULL,
`type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
`value_scope` text NOT NULL COMMENT '取值范围',
`is_require` tinyint(1) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
`is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='微信配置选项';
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('1','第三方平台appid','platform_appid','','0','0','','0','1','1','1');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('2','公众号消息校验Token','platform_token','','0','0','','0','1','1','2');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('3','公众号消息加解密Key','platform_encodingAesKey','','0','0','','0','1','1','3');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('4','是否开启第三方平台','platform_status','0','0','4','0,1','0','1','1','4');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('5','第三方平台AppSecret','platform_appsecret','','0','0','','0','1','1','1');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('6','component_verify_ticket','platform_component_verify_ticket','0','0','0','','0','1','0','6');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('7','第三方平台access_token','platform_component_access_token','0','0','0','','0','1','0','7');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('8','第三方平台预授权码','platform_pre_auth_code','0','0','0','','0','1','0','8');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('9','第三方平台access_token有效期','platform_component_access_token_expire','0','0','0','','0','1','0','9');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('10','第三方平台预授权码有效期','platform_pre_auth_code_expire','0','0','0','','0','1','0','10');
INSERT INTO `%DB_PREFIX%weixin_conf` VALUES ('11','是否已全网发布','platform_all_publish','0','0','4','0,1','0','1','1','11');
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_nav`;
CREATE TABLE `%DB_PREFIX%weixin_nav` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(100) DEFAULT '' COMMENT '菜单名称',
`sort` int(11) DEFAULT '0' COMMENT '菜单排序 大->小',
`event_type` enum('click') DEFAULT 'click' COMMENT '按钮的事件，目前微信只支持click',
`account_id` int(11) DEFAULT '0' COMMENT '商户ID,0表示平台',
`status` tinyint(1) DEFAULT '0' COMMENT '是否已推送到微信(0:未推送或失败 1:成功)，该列同一个商家全部相同，菜单为一次性推送,对菜单本地修改时，批量更新该值为0',
`pid` int(11) DEFAULT '0',
`ctl` varchar(255) NOT NULL,
`data` text NOT NULL,
PRIMARY KEY (`id`),
KEY `sort` (`sort`),
KEY `event_type` (`event_type`),
KEY `account_id` (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='为微信自定义的菜单设置';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_reply`;
CREATE TABLE `%DB_PREFIX%weixin_reply` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`i_msg_type` enum('event','link','location','image','text') DEFAULT 'text' COMMENT '接收到的微信的推送到本系统api中的MsgType',
`o_msg_type` enum('news','music','text') DEFAULT 'text' COMMENT '用于响应并回复给微信推送的消息类型 news:图文 music:音乐 text:纯文本',
`keywords` varchar(300) DEFAULT NULL COMMENT '用于响应文本(i_msg_type:text或者i_event:click时对key的响应)类型的回复时进行匹配的关键词',
`keywords_match` text COMMENT 'keywords的全文索引列',
`keywords_match_row` text COMMENT 'keywords全文索引的未作unicode编码的原文，用于开发者查看',
`address` text COMMENT '用于显示的地理地址',
`api_address` text COMMENT '用于地理定位的API地址',
`x_point` varchar(100) DEFAULT '' COMMENT '用于lbs消息,i_msg_type:location 匹配的经度',
`y_point` varchar(100) DEFAULT '' COMMENT '用于lbs消息,i_msg_type:location 匹配的纬度',
`scale_meter` int(11) DEFAULT '0' COMMENT '用于lbs消息,i_msg_type:location 匹配的距离范围(米)',
`i_event` enum('subscribe','unsubscribe','click','empty') DEFAULT 'empty' COMMENT '用于响应i_msg_type为event时的对应事件',
`reply_content` text COMMENT '回复的文本消息',
`reply_music` varchar(255) DEFAULT '' COMMENT '回复的音乐链接',
`reply_news_title` text COMMENT '图文回复的标题',
`reply_news_description` text COMMENT '图文回复的描述',
`reply_news_picurl` varchar(255) DEFAULT '' COMMENT '图文回复的图片链接',
`reply_news_url` varchar(255) DEFAULT '' COMMENT '图文回复的跳转链接',
`reply_news_content` text,
`type` tinyint(1) DEFAULT '0' COMMENT '回复归类 \r\n0:普通的回复 \r\n1:默认回复(只能一条文本或图文) \r\n4.关注时回复(只能有一条文本或图文) ',
`account_id` int(11) DEFAULT '0' COMMENT '所属的商家ID',
`default_close` tinyint(1) DEFAULT '1' COMMENT '默认回复是否关闭 0：关闭 1：开启',
`match_type` tinyint(1) NOT NULL DEFAULT '0',
`ctl` varchar(255) NOT NULL,
`data` text NOT NULL,
PRIMARY KEY (`id`),
KEY `i_msg_type` (`i_msg_type`),
KEY `o_msg_type` (`o_msg_type`),
KEY `i_event` (`i_event`),
KEY `type` (`type`),
KEY `account_id` (`account_id`),
KEY `match_type` (`account_id`,`match_type`,`keywords`),
FULLTEXT KEY `keywords_match` (`keywords_match`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='商家回复设置表';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_reply_relate`;
CREATE TABLE `%DB_PREFIX%weixin_reply_relate` (
`main_reply_id` int(11) DEFAULT '0' COMMENT '主回复ID',
`relate_reply_id` int(11) DEFAULT '0' COMMENT '关联的多图文用的子回复ID',
`sort` tinyint(1) DEFAULT '0',
KEY `main_reply_id` (`main_reply_id`),
KEY `relate_reply_id` (`relate_reply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='多图文回复的关联配置';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_tmpl`;
CREATE TABLE `%DB_PREFIX%weixin_tmpl` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(100) DEFAULT '' COMMENT '模板名称',
`msg` text COMMENT '模板内容',
`template_id` varchar(255) DEFAULT NULL COMMENT '模板ID',
`template_id_short` varchar(255) DEFAULT NULL COMMENT '模板编号',
`account_id` int(11) DEFAULT '0' COMMENT '所属的商家ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='微信模板';
DROP TABLE IF EXISTS `%DB_PREFIX%weixin_user`;
CREATE TABLE `%DB_PREFIX%weixin_user` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) DEFAULT NULL,
`account_id` int(11) NOT NULL COMMENT '商家ID,平台级为0',
`subscribe` tinyint(1) NOT NULL COMMENT '用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。',
`nickname` varchar(255) NOT NULL,
`sex` tinyint(1) NOT NULL COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
`city` varchar(255) NOT NULL,
`country` varchar(255) DEFAULT NULL,
`province` varchar(255) DEFAULT NULL,
`language` varchar(20) DEFAULT NULL,
`headimgurl` varchar(255) DEFAULT NULL,
`subscribe_time` varchar(255) DEFAULT NULL COMMENT '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间',
`unionid` varchar(255) DEFAULT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。',
`remark` varchar(255) DEFAULT NULL COMMENT '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注',
`groupid` int(11) DEFAULT NULL COMMENT '用户所在的分组ID',
PRIMARY KEY (`id`),
UNIQUE KEY `user_id` (`user_id`,`account_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='//微信公众号会员列表';
DROP TABLE IF EXISTS `%DB_PREFIX%withdraw`;
CREATE TABLE `%DB_PREFIX%withdraw` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`money` decimal(20,4) NOT NULL COMMENT '提现金额',
`create_time` int(11) NOT NULL,
`is_paid` tinyint(1) NOT NULL COMMENT '是否已确认',
`pay_time` int(11) NOT NULL COMMENT '确认支付时间',
`bank_name` varchar(255) NOT NULL COMMENT '开户行名称',
`bank_account` varchar(255) NOT NULL COMMENT '开户行账号',
`bank_user` varchar(255) NOT NULL COMMENT '开户行会员名',
`is_delete` tinyint(1) NOT NULL,
`bank_mobile`  varchar(255) NOT NULL COMMENT '银行预留手机号' ,
`is_bind`  tinyint(1) NOT NULL  COMMENT '是否绑定',
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `%DB_PREFIX%youhui`;
CREATE TABLE `%DB_PREFIX%youhui` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '优惠券名称',
`icon` varchar(255) NOT NULL COMMENT '列表展示用图',
`image` varchar(255) NOT NULL COMMENT '大图',
`deal_cate_id` int(11) NOT NULL COMMENT '所属的生活服务大分类ID',
`begin_time` int(11) NOT NULL COMMENT '有效期开始时间',
`end_time` int(11) NOT NULL COMMENT '有效期结束时间',
`expire_day` int(11) NOT NULL COMMENT '领取后的过期时间(0表示以优惠券结束时间为依据)，如优惠券结束时间也为0则不过期',
`city_id` int(11) NOT NULL COMMENT '所属城市ID',
`send_type` tinyint(1) NOT NULL COMMENT '弃用',
`total_num` int(11) NOT NULL COMMENT '总条数(限制下载用（针对验证类型的优惠券）)',
`sms_count` int(11) NOT NULL COMMENT '弃用',
`print_count` int(11) NOT NULL COMMENT '弃用',
`view_count` int(11) NOT NULL COMMENT '弃用',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`sms_content` varchar(255) NOT NULL COMMENT '短信下载优惠券的短信内容',
`is_sms` tinyint(1) NOT NULL COMMENT '是否支持短信下载',
`is_print` tinyint(1) NOT NULL DEFAULT '0' COMMENT '弃用',
`brief` text NOT NULL COMMENT '内容、条款',
`youhui_type` tinyint(1) NOT NULL COMMENT '减免0/折扣1',
`total_fee` int(11) NOT NULL COMMENT '消费的总金额（由商家验证时填写累加）',
`deal_cate_match_row` text NOT NULL,
`deal_cate_match` text NOT NULL COMMENT '分类的全文索引',
`locate_match_row` text NOT NULL,
`locate_match` text NOT NULL COMMENT '地区信息的全文索引',
`name_match_row` text NOT NULL,
`name_match` text NOT NULL COMMENT '名称的全文索引',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`user_id` int(11) NOT NULL COMMENT '用户发布的',
`supplier_id` int(11) NOT NULL COMMENT '商户',
`create_time` int(11) NOT NULL COMMENT '发布时间',
`brand_id` int(11) NOT NULL COMMENT '弃用',
`pub_by` tinyint(1) NOT NULL COMMENT '0:管理员 1:会员 2:商家',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐',
`list_brief` text NOT NULL COMMENT '优惠券列表简介',
`description` text NOT NULL COMMENT '详情描述',
`index_img` varchar(255) NOT NULL COMMENT '弃用',
`image_3` varchar(255) NOT NULL COMMENT '手机端用图',
`image_3_w` int(11) NOT NULL COMMENT '手机端用图宽',
`image_3_h` int(11) NOT NULL COMMENT '手机端用图高',
`address` varchar(255) NOT NULL COMMENT '地址',
`publish_wait` tinyint(1) NOT NULL COMMENT '0:待审核  1:已审核 ',
`return_money` decimal(11,4) NOT NULL COMMENT '备用',
`return_score` int(11) NOT NULL COMMENT '备用',
`return_point` int(11) NOT NULL COMMENT '备用',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`dp_count` int(11) NOT NULL,
`avg_point` float(14,4) NOT NULL,
`total_point` int(11) NOT NULL,
`use_notice` text NOT NULL COMMENT '使用需知',
`user_count` int(11) NOT NULL COMMENT '总下载次数',
`user_limit` int(11) NOT NULL COMMENT '每个会员的下载限制',
`score_limit` int(11) NOT NULL COMMENT '下载优惠券所消耗的积分',
`point_limit` int(11) NOT NULL COMMENT '下载优惠券所需的经验，不扣除',
PRIMARY KEY (`id`),
KEY `city_id` (`city_id`),
KEY `cate_id` (`deal_cate_id`) USING BTREE,
FULLTEXT KEY `f_t` (`deal_cate_match`,`name_match`,`locate_match`),
FULLTEXT KEY `cate_match` (`deal_cate_match`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='优惠券表';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_biz_submit`;
CREATE TABLE `%DB_PREFIX%youhui_biz_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '优惠券名称',
`icon` varchar(255) NOT NULL COMMENT '列表展示用图',
`image` varchar(255) NOT NULL COMMENT '大图',
`deal_cate_id` int(11) NOT NULL COMMENT '所属的生活服务大分类ID',
`begin_time` int(11) NOT NULL COMMENT '有效期开始时间',
`end_time` int(11) NOT NULL COMMENT '有效期结束时间',
`expire_day` int(11) NOT NULL COMMENT '领取后的过期时间(0表示以优惠券结束时间为依据)，如优惠券结束时间也为0则不过期',
`city_id` int(11) NOT NULL COMMENT '所属城市ID',
`send_type` tinyint(1) NOT NULL COMMENT '弃用',
`total_num` int(11) NOT NULL COMMENT '总条数(限制下载用（针对验证类型的优惠券）)',
`sms_count` int(11) NOT NULL COMMENT '弃用',
`print_count` int(11) NOT NULL COMMENT '弃用',
`view_count` int(11) NOT NULL COMMENT '弃用',
`sort` int(11) NOT NULL COMMENT '排序 大到小',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`sms_content` varchar(255) NOT NULL COMMENT '短信下载优惠券的短信内容',
`is_sms` tinyint(1) NOT NULL COMMENT '是否支持短信下载',
`is_print` tinyint(1) NOT NULL DEFAULT '0' COMMENT '弃用',
`brief` text NOT NULL COMMENT '内容、条款',
`youhui_type` tinyint(1) NOT NULL COMMENT '减免0/折扣1',
`total_fee` int(11) NOT NULL COMMENT '消费的总金额（由商家验证时填写累加）',
`deal_cate_match_row` text NOT NULL,
`deal_cate_match` text NOT NULL COMMENT '分类的全文索引',
`locate_match_row` text NOT NULL,
`locate_match` text NOT NULL COMMENT '地区信息的全文索引',
`name_match_row` text NOT NULL,
`name_match` text NOT NULL COMMENT '名称的全文索引',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`user_id` int(11) NOT NULL COMMENT '用户发布的',
`supplier_id` int(11) NOT NULL COMMENT '商户',
`create_time` int(11) NOT NULL COMMENT '发布时间',
`brand_id` int(11) NOT NULL COMMENT '弃用',
`pub_by` tinyint(1) NOT NULL COMMENT '0:管理员 1:会员 2:商家',
`is_recommend` tinyint(1) NOT NULL COMMENT '推荐',
`list_brief` text NOT NULL COMMENT '优惠券列表简介',
`description` text NOT NULL COMMENT '详情描述',
`index_img` varchar(255) NOT NULL COMMENT '弃用',
`image_3` varchar(255) NOT NULL COMMENT '手机端用图',
`image_3_w` int(11) NOT NULL COMMENT '手机端用图宽',
`image_3_h` int(11) NOT NULL COMMENT '手机端用图高',
`address` varchar(255) NOT NULL COMMENT '地址',
`publish_wait` tinyint(1) NOT NULL COMMENT '0:待审核  1:已审核 ',
`return_money` decimal(11,4) NOT NULL COMMENT '备用',
`return_score` int(11) NOT NULL COMMENT '备用',
`return_point` int(11) NOT NULL COMMENT '备用',
`dp_count_1` int(11) NOT NULL COMMENT '一星点评数',
`dp_count_2` int(11) NOT NULL COMMENT '2星点评数',
`dp_count_3` int(11) NOT NULL COMMENT '3星点评数',
`dp_count_4` int(11) NOT NULL COMMENT '4星点评数',
`dp_count_5` int(11) NOT NULL COMMENT '5星点评数',
`dp_count` int(11) NOT NULL,
`avg_point` float(14,4) NOT NULL,
`total_point` int(11) NOT NULL,
`use_notice` text NOT NULL COMMENT '使用需知',
`user_count` int(11) NOT NULL COMMENT '总下载次数',
`user_limit` int(11) NOT NULL COMMENT '每个会员的下载限制',
`score_limit` int(11) NOT NULL COMMENT '下载优惠券所消耗的积分',
`point_limit` int(11) NOT NULL COMMENT '下载优惠券所需的经验，不扣除',
`cache_deal_cate_type_youhui_link` text NOT NULL COMMENT '序列化子分类列表',
`cache_youhui_location_link` text NOT NULL COMMENT '序列化支持的门店',
`account_id` int(11) NOT NULL COMMENT '提交数据的商户帐号关联ID',
`youhui_id` int(11) NOT NULL COMMENT '关联优惠主表的数据ID',
`biz_apply_status` tinyint(1) NOT NULL COMMENT '商户申请状态 1.新品上架申请 2:修改 3:下架',
`admin_check_status` tinyint(1) NOT NULL COMMENT '管理员审核状态 0 待审核 1 通过 2 拒绝',
PRIMARY KEY (`id`),
KEY `city_id` (`city_id`),
KEY `cate_id` (`deal_cate_id`),
FULLTEXT KEY `f_t` (`deal_cate_match`,`name_match`,`locate_match`),
FULLTEXT KEY `cate_match` (`deal_cate_match`),
FULLTEXT KEY `name_match` (`name_match`),
FULLTEXT KEY `locate_match` (`locate_match`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='商户中心发布优惠券临时表';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_dp_point_result`;
CREATE TABLE `%DB_PREFIX%youhui_dp_point_result` (
`group_id` int(11) NOT NULL COMMENT '分组ID',
`point` int(11) NOT NULL COMMENT '分数',
`youhui_id` int(11) NOT NULL COMMENT '优惠券ID',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
KEY `group_id` (`group_id`) USING BTREE,
KEY `youhui_id` (`youhui_id`) USING BTREE,
KEY `dp_id` (`dp_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个优惠券，每条点评针对每个评分分组的点评评分';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_dp_tag_result`;
CREATE TABLE `%DB_PREFIX%youhui_dp_tag_result` (
`tags` varchar(255) NOT NULL COMMENT '标签列表 空格分隔',
`dp_id` int(11) NOT NULL COMMENT '关联的点评ID',
`group_id` int(11) NOT NULL COMMENT '标签分组ID',
`youhui_id` int(11) NOT NULL COMMENT '优惠券ID',
KEY `dp_id` (`dp_id`),
KEY `group_id` (`group_id`),
KEY `youhui_id` (`youhui_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券按预定义的分组打标签的结果表';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_location_link`;
CREATE TABLE `%DB_PREFIX%youhui_location_link` (
`youhui_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY (`youhui_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券支持的门店关联表';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_log`;
CREATE TABLE `%DB_PREFIX%youhui_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`youhui_id` int(11) NOT NULL COMMENT '优惠券ID',
`youhui_sn` varchar(255) NOT NULL COMMENT '短信生成的优惠券序列号',
`user_id` int(11) NOT NULL COMMENT '下载的会员ID',
`order_count` int(5) NOT NULL COMMENT '订餐人数',
`is_private_room` tinyint(1) NOT NULL COMMENT '是否包间',
`mobile` varchar(255) NOT NULL COMMENT '下载的手机号',
`date_time` int(11) NOT NULL COMMENT '预订时间',
`confirm_id` int(11) NOT NULL COMMENT '确认使用的商家ID',
`total_fee` int(11) NOT NULL COMMENT '消费金额',
`create_time` int(11) NOT NULL COMMENT '下载时间',
`confirm_time` int(11) NOT NULL COMMENT '确认时间',
`dp_id` int(11) NOT NULL COMMENT '为优惠券点评的ID',
`location_id` int(11) NOT NULL COMMENT '优惠券消费门店ID',
`return_money` decimal(20,4) NOT NULL COMMENT '验证返现',
`return_score` int(11) NOT NULL COMMENT '验证返积分',
`return_point` int(11) NOT NULL COMMENT '验证返经验',
`expire_time` int(11) NOT NULL COMMENT '优惠券验证的过期时间0为无限期',
`sms_count` int(11) NOT NULL COMMENT '短信通知的次数',
PRIMARY KEY (`id`),
UNIQUE KEY `sn` (`youhui_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COMMENT='优惠券短信下载记录';
DROP TABLE IF EXISTS `%DB_PREFIX%youhui_sc`;
CREATE TABLE `%DB_PREFIX%youhui_sc` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uid` int(11) NOT NULL,
`youhui_id` int(11) NOT NULL,
`add_time` int(11) NOT NULL COMMENT '收藏时间',
PRIMARY KEY (`id`),
UNIQUE KEY `inx_youhui_sc` (`uid`,`youhui_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机端优惠券收藏表';

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
`other_money`  decimal(20,4) NOT NULL COMMENT '不可优惠金额',
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

CREATE TABLE `%DB_PREFIX%store_pay_order_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL,
`log_time` int(11) NOT NULL,
`order_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户订单操作的日志表';



DROP TABLE IF EXISTS `%DB_PREFIX%dc_menu`;
CREATE TABLE `%DB_PREFIX%dc_menu` (
`id` int(11) NOT NULL  auto_increment,
`name` varchar(255) NOT NULL COMMENT '菜单名称',
`cate_id` int(11) NOT NULL COMMENT '商家定义的分类ID',
`price` decimal(20,4) NOT NULL COMMENT '菜品价格',
`image` varchar(255) NOT NULL COMMENT '图片',
`tags` text NOT NULL COMMENT '菜品标签，用于搜索用，预选值由系统的菜品分类中选取',
`tags_match` text NOT NULL COMMENT '标签的全文索引',
`tags_match_row` text NOT NULL COMMENT '全文索引原型',
`is_effect` tinyint(1) NOT NULL,
`location_id` int(11) NOT NULL COMMENT '门店ID',
`supplier_id` int(11) NOT NULL COMMENT '商户ID',
`buy_count` int(11) NOT NULL COMMENT '累积销量(可作成月销量，每月清零，待议)',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`menu_cate_type`  tinyint(1) NOT NULL COMMENT '类型ID，对应dc_menu_cate的type',
`open_time_cfg_str`  varchar(255) NOT NULL COMMENT '营业时间的组合字符串，由配置表同步来',
PRIMARY KEY  (`id`),
KEY `location_id` (`location_id`),
KEY `supplier_id` (`supplier_id`),
FULLTEXT KEY `tags_match` (`tags_match`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单表或者超市的货物表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_supplier_location_open_time`;
CREATE TABLE `%DB_PREFIX%dc_supplier_location_open_time` (
`location_id` int(11) NOT NULL,
`begin_time_h` int(2) NOT NULL COMMENT '营业时段的开始时间的时',
`begin_time_m` int(2) NOT NULL COMMENT '营业时间开始时段的分',
`end_time_h` int(2) NOT NULL COMMENT '营业时间结束时段的时',
`end_time_m` int(2) NOT NULL COMMENT '结束时间分,结束的时间不作索引',
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店营业时间配置表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_cate`;
CREATE TABLE `%DB_PREFIX%dc_cate` (
`id` int(11) NOT NULL auto_increment COMMENT '订餐分类ID',
`name` varchar(100) NOT NULL COMMENT '订餐分类名称',
`sort` int(11) NOT NULL COMMENT '排序由小到大',
`iconfont` varchar(15) NOT NULL COMMENT '分类的字体图标',
`iconcolor` varchar(15) NOT NULL COMMENT '分类颜色',
`icon_img` varchar(255) NOT NULL COMMENT 'app端用的分类图片',
`type`	tinyint(1) NOT NULL COMMENT '分类类型：0餐厅分类 1商品分类',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐的分类（只有一级）';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_menu_cate`;
CREATE TABLE `%DB_PREFIX%dc_menu_cate` (
`id` int(11) NOT NULL auto_increment COMMENT '订餐菜品分类ID',
`name` varchar(100) NOT NULL COMMENT '订餐分类名称',
`sort` int(11) NOT NULL COMMENT '排序由小到大',
`iconfont` varchar(15) NOT NULL COMMENT '分类的字体图标',
`iconcolor` varchar(15) NOT NULL COMMENT '分类颜色',
`icon_img` varchar(255) NOT NULL COMMENT 'app端用的分类图片',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`type`	tinyint(1) NOT NULL COMMENT '分类类型：0外卖标签 1便利店标签',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐的菜单分类（只有一级）';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_supplier_menu_cate`;
CREATE TABLE `%DB_PREFIX%dc_supplier_menu_cate` (
`id` int(11) NOT NULL auto_increment COMMENT '订餐菜品分类ID',
`name` varchar(100) NOT NULL COMMENT '订餐分类名称',
`sort` int(11) NOT NULL COMMENT '排序由小到大',
`iconfont` varchar(15) NOT NULL COMMENT '分类的字体图标',
`iconcolor` varchar(15) NOT NULL COMMENT '分类颜色',
`icon_img` varchar(255) NOT NULL COMMENT 'app端用的分类图片',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性',
`supplier_id` int(11) NOT NULL COMMENT '分类所属的商户ID',
`location_id` int(11) NOT NULL COMMENT '分类所属的门店ID',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订餐的商户自定义的菜单分类（只有一级）';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_cate_supplier_location_link`;
CREATE TABLE `%DB_PREFIX%dc_cate_supplier_location_link` (
`dc_cate_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
KEY `dc_cate_id` (`dc_cate_id`),
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店与订餐分类的n-n关联表，用于生成索引';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_delivery`;
CREATE TABLE `%DB_PREFIX%dc_delivery` (
`id` int(11) NOT NULL auto_increment,
`location_id` int(11) NOT NULL COMMENT '门店ID',
`start_price` decimal(20,4) NOT NULL COMMENT '起送价',
`scale` int(11) NULL COMMENT '用于限制标准的公里数(lbs直线距离)',
`delivery_price` decimal(20,4) NOT NULL COMMENT '配送费',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关于订餐配送的送配费，起送价配置表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_package_conf`;
CREATE TABLE `%DB_PREFIX%dc_package_conf` (
`id` int(11) NOT NULL auto_increment,
`package_price` decimal(20,4) NOT NULL COMMENT '打包费',
`package_start_price` decimal(20,4) NOT NULL COMMENT '基础价（即超过该价格的打包费）',
`location_id` int(11) NOT NULL COMMENT '门店ID',
PRIMARY KEY  (`id`),
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关于订餐打包费的配置表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_order`;
CREATE TABLE `%DB_PREFIX%dc_order` (
`id` int(11) NOT NULL auto_increment,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`order_status` tinyint(1) NOT NULL COMMENT '订单的结单状态标识，结单后的订单允许删除,0:否 1:是\r\n结单条件\r\n1.用户确认到货\r\n2.商家在超期后帮用户确认到货\r\n3.用户退款被确认4.验证成功5.过期不验证商家操作关闭',
`confirm_status` tinyint(1) NOT NULL COMMENT '订单商家确认状态\r\n0:未确认（未接单，用户未付款可以取消，已付款可直接退款）\r\n1.已确认（商家已接单,客户可与商家联系，申请退款）\r\n2.已配送（商家已配送,不可以申请退款）',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态: 0未支付 1已支付（不做部份付款，支付成功后扣钱）',
`total_price` decimal(20,4) NOT NULL COMMENT '应付总额\r\ntotal_price = menu_price+package_price+delivery_price+rs_price-44',
`menu_price` decimal(20,4) NOT NULL COMMENT '菜金总额',
`package_price` decimal(20,4) NOT NULL COMMENT '打包费',
`delivery_price` decimal(20,4) NOT NULL COMMENT '运费',
`promote_str` text NOT NULL COMMENT '享受的优惠措失，带换行的字符串',
`pay_amount` decimal(20,4) NOT NULL COMMENT '已付总额，pay_amount>=total_price时表示支付成功',
`pay_time` int(11) NOT NULL COMMENT '支付成功时间',
`online_pay` decimal(20,4) NOT NULL COMMENT '在线支付的额度',
`ecv_id` int(11) NOT NULL COMMENT '使用的代金券的ID',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金券的支付额度',
`account_money` decimal(20,4) NOT NULL COMMENT '余额支付',
`payment_id` int(11) NOT NULL COMMENT '支付方式ID，0表示在线支付，1表示货到付款',
`refund_status` tinyint(1) NOT NULL COMMENT '退款状态\r\n0无退款1退款申请中2已退款3退款驳回',
`refund_memo` text NOT NULL COMMENT '会员申请退款的理由',
`refuse_memo` text NOT NULL COMMENT '退款驳回的理由',
`order_menu` text NOT NULL COMMENT '序列化下来的订单的菜单数据，含名称，数量，图片，单价，总价等',
`user_id` int(11) NOT NULL COMMENT '下单会员ID',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`address` varchar(255) NOT NULL COMMENT '收货地址',
`api_address` varchar(255) NOT NULL COMMENT '位置信息，用于定位的地址',
`consignee` varchar(255) NOT NULL COMMENT '收货人称呼',
`mobile` varchar(20) NOT NULL COMMENT '手机号',
`order_delivery_time` int(11) NOT NULL COMMENT '下单时填写的送餐时间',
`confirm_time` int(11) NOT NULL COMMENT '确认成功时间，外卖确认到货或者预订验证成功的时间',
`arrival_time` int(11) NOT NULL COMMENT '到货确认时间',
`is_cancel` tinyint(1) NOT NULL COMMENT '是否被用户/商家(拒绝接单)/管理员取消,0:未取消,1:用户取消,2:商户取消,3.管理员取消',
`balance_price` decimal(20,4) NOT NULL COMMENT '结算给商家的钱，按门店的预设分配',
`rs_price` decimal(20,4) NOT NULL COMMENT '定金',
`is_rs` tinyint(1) NOT NULL COMMENT '是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券',
`sn_id` varchar(255) default NULL COMMENT '预定单的验证码ID',
`is_dp` tinyint(1) NOT NULL COMMENT '是否已点评',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
`is_send_admin` tinyint(1) NOT NULL COMMENT '是否维权单，即商家审核退款后',
`send_admin_memo` text NOT NULL COMMENT '维权备注',
`is_sn_confirm` tinyint(1) NOT NULL COMMENT '验证码被验证使用，0:否 1:商家验证  2：管理员验证',
`sn_confirm_time`	int(11) NOT NULL COMMENT '验证时间',
`dc_comment`  text NOT NULL COMMENT '外卖预订的客户留言',
`invoice`  varchar(255) NOT NULL COMMENT '发票',
`location_name`  varchar(255) NOT NULL COMMENT '门店名',
`user_name`  varchar(255) NOT NULL COMMENT '会员名称',
`type_del`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除状态: 0未经过删除处理 1经过删除',
`promote_amount`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '促销优惠金额',
`payment_fee`  decimal(20,4) NOT NULL COMMENT '支付手续费',
`balance_time`  int(11) NOT NULL COMMENT '商家结算时间',
`refund_time`  int(11) NOT NULL COMMENT '退款时间',
`refund_price`  decimal(20,4) NOT NULL COMMENT '退款金额',
`bank_id`  varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
PRIMARY KEY  (`id`),
KEY `sn_id` (`sn_id`),
KEY `order_sn` (`order_sn`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`),
KEY `user_id` (`user_id`),
KEY `order_status` (`order_status`),
KEY `confirm_status` (`confirm_status`),
KEY `pay_status` (`pay_status`),
KEY `is_cancel` (`is_cancel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐的订单表，可散列';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_order_log`;
CREATE TABLE `%DB_PREFIX%dc_order_log` (
`id` int(11) NOT NULL auto_increment,
`order_id` int(11) NOT NULL COMMENT '订单ID',
`log_info` text NOT NULL COMMENT '日志内容',
`log_time` int(11) NOT NULL COMMENT '发生时间',
PRIMARY KEY  (`id`),
KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐订单的日志表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_order_history`;
CREATE TABLE `%DB_PREFIX%dc_order_history` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`order_status` tinyint(1) NOT NULL COMMENT '订单的结单状态标识，结单后的订单允许删除,0:否 1:是\r\n结单条件\r\n1.用户确认到货\r\n2.商家在超期后帮用户确认到货\r\n3.用户退款被确认',
`confirm_status` tinyint(1) NOT NULL COMMENT '订单商家确认状态\r\n0:未确认（未接单，用户未付款可以取消，已付款可申请退款）\r\n1.已确认（商家已接单，会员不可取消，只有商家支持退款会员才可以发起退款申请）\r\n2.已配送（商家已配送，退款同上）',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态: 0未支付 1已支付（不做部份付款，支付成功后扣钱）',
`total_price` decimal(20,4) NOT NULL COMMENT '应付总额\r\ntotal_price = menu_price+package_price+delivery_price+rs_price',
`menu_price` decimal(20,4) NOT NULL COMMENT '菜金总额',
`package_price` decimal(20,4) NOT NULL COMMENT '打包费',
`delivery_price` decimal(20,4) NOT NULL COMMENT '运费',
`promote_str` text NOT NULL COMMENT '享受的优惠措失，带换行的字符串',
`pay_amount` decimal(20,4) NOT NULL COMMENT '已付总额，pay_amount>=total_price时表示支付成功',
`pay_time` int(11) NOT NULL COMMENT '支付成功时间',
`online_pay` decimal(20,4) NOT NULL COMMENT '在线支付的额度',
`ecv_id` int(11) NOT NULL COMMENT '使用的代金券的ID',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金券的支付额度',
`account_money` decimal(20,4) NOT NULL COMMENT '余额支付',
`payment_id` int(11) NOT NULL COMMENT '支付方式ID，0表示货到付款',
`refund_status` tinyint(1) NOT NULL COMMENT '退款状态\r\n0无退款1退款申请中2已退款3退款驳回',
`refund_memo` text NOT NULL COMMENT '会员申请退款的理由',
`refuse_memo` text NOT NULL COMMENT '退款驳回的理由',
`order_menu` text NOT NULL COMMENT '序列化下来的订单的菜单数据，含名称，数量，图片，单价，总价等',
`user_id` int(11) NOT NULL COMMENT '下单会员ID',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`address` varchar(255) NOT NULL COMMENT '收货地址',
`api_address` varchar(255) NOT NULL COMMENT '位置信息，用于定位的地址',
`consignee` varchar(255) NOT NULL COMMENT '收货人称呼',
`mobile` varchar(20) NOT NULL COMMENT '手机号',
`order_delivery_time` int(11) NOT NULL COMMENT '下单时填写的送餐时间',
`delivery_time` int(11) NOT NULL COMMENT '实际配送时间',
`arrival_time` int(11) NOT NULL COMMENT '到货确认时间',
`is_cancel` tinyint(1) NOT NULL COMMENT '是否被用户/商家(拒绝接单)/管理员取消,0:未取消,1:用户取消,2:商户取消,3.管理员取消',
`balance_price` decimal(20,4) NOT NULL COMMENT '结算给商家的钱，按门店的预设分配',
`rs_price` decimal(20,4) NOT NULL COMMENT '定金',
`is_rs` tinyint(1) NOT NULL COMMENT '是否为预定定单，0:否 1:是，预定定单不用选配送，发团购券',
`sn_id` int(11) NOT NULL COMMENT '预定单的验证码的ID',
`is_dp` tinyint(1) NOT NULL COMMENT '是否已点评',
`dp_id` int(11) NOT NULL COMMENT '点评ID',
`is_send_admin` tinyint(1) NOT NULL COMMENT '是否维权单，即商家审核退款后',
`send_admin_memo` text NOT NULL COMMENT '维权备注',
`dc_comment` text NOT NULL COMMENT '外卖预订的客户留言',
`invoice` varchar(255) NOT NULL COMMENT '发票',
`location_name` varchar(255) NOT NULL COMMENT '门店名',
`user_name` varchar(255) NOT NULL COMMENT '会员名称',
`type_del` tinyint(1) NOT NULL COMMENT '删除状态: 0未经过删除处理 1为经过删除',
`promote_amount` decimal(20,4) NOT NULL COMMENT '促销优惠金额',
`confirm_time` int(11) NOT NULL COMMENT '确认成功时间，外卖确认到货或者预订验证成功的时间',
`payment_fee` decimal(20,4) NOT NULL COMMENT '支付手续费',
`balance_time` int(11) NOT NULL COMMENT '商家结算时间',
`refund_time` int(11) NOT NULL COMMENT '退款时间',
`refund_price` decimal(20,4) NOT NULL COMMENT '退款金额',
`bank_id`  varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
`history_dc_coupon` text NOT NULL COMMENT '订单电子券数据',
`history_dc_order_menu` text NOT NULL COMMENT '菜单数据',
`history_payment_notice` text NOT NULL COMMENT '付款单数据',
`history_dc_order_log` text NOT NULL COMMENT '订单日志',
PRIMARY KEY (`id`),
KEY `order_sn` (`order_sn`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`),
KEY `user_id` (`user_id`),
KEY `order_status` (`order_status`),
KEY `confirm_status` (`confirm_status`),
KEY `pay_status` (`pay_status`),
KEY `is_cancel` (`is_cancel`),
KEY `sn_id` (`sn_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='外卖预订订单历史表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_promote`;
CREATE TABLE `%DB_PREFIX%dc_promote` (
`id` int(11) NOT NULL auto_increment,
`class_name` varchar(100) NOT NULL COMMENT '促销接口的接口名',
`sort` int(11) NOT NULL COMMENT '促销接口的权重，由小到大',
`config` text NOT NULL COMMENT '被序列化后的促销接口配置',
`description` text NOT NULL COMMENT '活动描述',
PRIMARY KEY  (`id`),
KEY `class_name` (`class_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='外卖促销规则的配置';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_order_menu`;
CREATE TABLE `%DB_PREFIX%dc_order_menu` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL COMMENT '商品名称',
`icon` varchar(255) NOT NULL COMMENT '商品图片，冗余',
`menu_id` int(11) NOT NULL COMMENT '所归属的商品ID',
`supplier_id` int(11) NOT NULL COMMENT '商品所属商家',
`location_id` int(11) NOT NULL COMMENT '所属门店',
`order_id` int(11) NOT NULL COMMENT '订单ID',
`user_id` int(11) NOT NULL COMMENT '下单会员',
`num` int(11) NOT NULL COMMENT '份数',
`unit_price` decimal(20,4) NOT NULL COMMENT '单价',
`total_price` decimal(20,4) NOT NULL COMMENT '总价',
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`type`  tinyint(1) NOT NULL COMMENT '类型，0为预订，1为外卖',
`table_time_id`  int(1) NOT NULL COMMENT '预订位置时间ID',
PRIMARY KEY  (`id`),
KEY `menu_id` (`menu_id`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`),
KEY `order_id` (`order_id`),
KEY `user_id` (`user_id`),
KEY `order_sn` (`order_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单预订的商品表,可散列';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_cart`;
CREATE TABLE `%DB_PREFIX%dc_cart` (
`id` int(11) NOT NULL auto_increment,
`session_id` varchar(255) NOT NULL COMMENT 'session_id',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`name` varchar(255) NOT NULL COMMENT '菜单的名称',
`icon` varchar(255) NOT NULL COMMENT '菜单图标',
`num` int(11) NOT NULL COMMENT '数量',
`unit_price` decimal(20,4) NOT NULL COMMENT '单价',
`total_price` decimal(20,4) NOT NULL COMMENT '总价',
`menu_id` int(11) NOT NULL COMMENT '菜单ID',
`table_time_id`  int(11) NOT NULL COMMENT '预定餐桌时间段关联ID',
`table_time`  int(11) NOT NULL COMMENT '订餐餐桌时间',
`cart_type`  tinyint(1) NOT NULL COMMENT '0代表餐桌,1代表菜品',
`add_time`  int(11) NOT NULL COMMENT '添加时间',
`is_effect`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '0为不生效，1为生效，默认为1',
PRIMARY KEY  (`id`),
KEY `session_id` (`session_id`),
KEY `user_id` (`user_id`),
KEY `location_id` (`location_id`),
KEY `supplier_id` (`supplier_id`),
KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐购物车';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_rs_item`;
CREATE TABLE `%DB_PREFIX%dc_rs_item` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL COMMENT '预定项目的名称(如2-4人桌，大包间等)',
`location_id` int(11) NOT NULL COMMENT '所属门店ID',
`supplier_id` int(11) NOT NULL COMMENT '所属商家ID',
`sort` int(11) NOT NULL COMMENT '排序，由小到大',
`icon` varchar(255) NOT NULL COMMENT '可能存在的图片',
`is_effect` tinyint(1) NOT NULL COMMENT '是否有效',
`price` decimal(20,4) NOT NULL COMMENT '预定的定金',
PRIMARY KEY  (`id`),
KEY `location_id` (`location_id`),
KEY `supplier_id` (`supplier_id`),
KEY `is_effect` (`is_effect`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个门店可提供预定的项目表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_rs_item_time`;
CREATE TABLE `%DB_PREFIX%dc_rs_item_time` (
`id` int(11) NOT NULL auto_increment,
`item_id` int(11) NOT NULL COMMENT '预定项目的关联ID',
`total_count` int(11) NOT NULL COMMENT '每个时间段的',
`rs_time` time NOT NULL COMMENT '接受预定的时间，当前时间比该时间提早一小时表示可以预定。当时间为0时，表示都不限时间',
`is_effect` tinyint(1) NOT NULL COMMENT '是否开放该时段',
`supplier_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
PRIMARY KEY  (`id`),
KEY `item_id` (`item_id`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可用于预定的项目的时间配置表以及库存';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_rs_item_day`;
CREATE TABLE `%DB_PREFIX%dc_rs_item_day` (
`id` int(11) NOT NULL auto_increment,
`time_id` int(11) NOT NULL COMMENT '预定时间的关联ID',
`item_id` int(11) NOT NULL COMMENT '预定项目的关联ID',
`buy_count` int(11) NOT NULL,
`rs_time` time NOT NULL COMMENT '接受预定的时间，当前时间比该时间提早一小时表示可以预定。当时间为0时，表示都不限时间',
`supplier_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
`rs_date`  date NOT NULL COMMENT '预订的日期，以天为单位',
PRIMARY KEY  (`id`),
KEY `time_id` (`time_id`),
KEY `item_id` (`item_id`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可用于预定的项目的时间配置表以及库存';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_consignee`;
CREATE TABLE `%DB_PREFIX%dc_consignee` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`address` text NOT NULL COMMENT '地址',
`api_address` text NOT NULL COMMENT 'api地址',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`consignee` varchar(100) NOT NULL COMMENT '收货人',
`mobile` varchar(20) NOT NULL COMMENT '手机号',
`is_main`  tinyint(1) NOT NULL,
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订餐收货人保存表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_location_sc`;
CREATE TABLE `%DB_PREFIX%dc_location_sc` (
`id` int(11) NOT NULL auto_increment,
`location_id` int(11) NOT NULL COMMENT '门店ID',
`user_id` int(11) NOT NULL COMMENT '会员ID',
`add_time` int(11) NOT NULL COMMENT '收藏时间',
PRIMARY KEY  (`id`),
KEY `location_id` (`location_id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门店收藏，订餐处体现';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_coupon`;
CREATE TABLE `%DB_PREFIX%dc_coupon` (
`id` int(11) NOT NULL auto_increment,
`sn` varchar(255) NOT NULL COMMENT ' 预订电子劵的序列号',
`begin_time` int(11) NOT NULL COMMENT ' 预订电子劵生效时间',
`end_time` int(11) NOT NULL COMMENT ' 预订电子劵过期时间',
`is_valid` tinyint(1) NOT NULL COMMENT ' 有效性 0:生成未发放给用户 1:已发放给用户 2：退款被禁用',
`user_id` int(11) NOT NULL COMMENT ' 会员ID',
`order_id` int(11) NOT NULL COMMENT ' 订单ID ',
`supplier_id` int(11) NOT NULL COMMENT ' 商户ID',
`location_id` int(11) NOT NULL COMMENT '消费的门店',
`confirm_account` int(11) NOT NULL COMMENT ' 验证团购券的商家帐号ID',
`is_used` tinyint(1) NOT NULL COMMENT '是否已经验证过',
`confirm_time` int(11) NOT NULL COMMENT ' 验证消费的时间',
PRIMARY KEY  (`id`),
UNIQUE KEY `sn` (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家预订电子劵表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_reminder`;
CREATE TABLE `%DB_PREFIX%dc_reminder` (
`id` int(11) NOT NULL auto_increment,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`order_id` int(11) NOT NULL COMMENT '订单ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`supplier_id` int(11) NOT NULL COMMENT '商户ID',
`create_time` int(11) NOT NULL COMMENT '生成时间',
`user_id` int(11) NOT NULL COMMENT '催单会员ID',
`address` varchar(255) NOT NULL COMMENT '收货地址',
`api_address` varchar(255) NOT NULL COMMENT '位置信息，用于定位的地址',
`consignee` varchar(255) NOT NULL COMMENT '收货人称呼',
`mobile` varchar(20) NOT NULL COMMENT '手机号',
PRIMARY KEY  (`id`),
KEY `order_sn` (`order_sn`),
KEY `order_id` (`order_id`),
KEY `user_id` (`user_id`),
KEY `location_id` (`location_id`),
KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='外卖的催单记录表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_statements`;
CREATE TABLE `%DB_PREFIX%dc_statements` (
`id` int(11) NOT NULL auto_increment,
`order_num` int(11) NOT NULL COMMENT '订单数',
`sale_money` decimal(20,4) NOT NULL COMMENT '营业额',
`balance_money` decimal(20,4) NOT NULL COMMENT '结算额',
`online_pay_money` decimal(20,4) NOT NULL COMMENT '在线支付额',
`promote_money` decimal(20,4) NOT NULL COMMENT '活动补贴',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金劵',
`refund_money` decimal(20,4) NOT NULL COMMENT '退款,取消订单金额',
`admin_charges` decimal(20,4) NOT NULL COMMENT '佣金',
`stat_time` date NOT NULL COMMENT '日报时间',
`stat_month` varchar(10) NOT NULL COMMENT '月份',
PRIMARY KEY  (`id`),
UNIQUE KEY `stat_time` USING BTREE (`stat_time`),
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台外卖财务日报表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_statements_log`;
CREATE TABLE `%DB_PREFIX%dc_statements_log` (
`id` int(11) NOT NULL auto_increment,
`create_time` int(11) NOT NULL,
`type` tinyint(1) NOT NULL COMMENT '0.营业额 1.结算额 2.在线支付额 3.活动补贴 4.代金劵 5.退款,取消订单金额 6.佣金',
`money` decimal(20,4) NOT NULL,
`log_info` text NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台外卖财务报表日志';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_supplier_statements`;
CREATE TABLE `%DB_PREFIX%dc_supplier_statements` (
`id` int(11) NOT NULL auto_increment,
`supplier_id` int(11) NOT NULL,
`sale_money` decimal(20,4) NOT NULL COMMENT '营业额',
`balance_money` decimal(20,4) NOT NULL COMMENT '已结算金额',
`unbalance_money` decimal(20,4) NOT NULL COMMENT '待结算金额',
`confirm_money` decimal(20,4) NOT NULL COMMENT '已完成金额',
`unconfirm_money` decimal(20,4) NOT NULL COMMENT '未完成金额',
`online_pay_money` decimal(20,4) NOT NULL COMMENT '在线支付额',
`promote_money` decimal(20,4) NOT NULL COMMENT '活动补贴',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金劵',
`refund_money` decimal(20,4) NOT NULL COMMENT '退款,取消订单金额',
`admin_charges` decimal(20,4) NOT NULL COMMENT '佣金',
`stat_time` date NOT NULL COMMENT '报表日期',
`stat_month` varchar(10) NOT NULL COMMENT '月份',
PRIMARY KEY  (`id`),
KEY `supplier_id` (`supplier_id`),
KEY `stat_time` (`stat_time`),
KEY `stat_month` (`stat_month`)
)  ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家外卖日报表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_supplier_order`;
CREATE TABLE `%DB_PREFIX%dc_supplier_order` (
`id` int(11) NOT NULL auto_increment,
`order_sn` varchar(255) NOT NULL COMMENT '订单编号',
`order_id` int(11) NOT NULL COMMENT '订单ID',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`location_id` int(11) NOT NULL COMMENT '门店ID',
`create_time` int(11) NOT NULL COMMENT '下单时间',
`order_status` tinyint(1) NOT NULL COMMENT '订单的结单状态标识，结单后的订单允许删除,0:否 1:是\r\n结单条件\r\n1.用户确认到货\r\n2.商家在超期后帮用户确认到货\r\n3.用户退款被确认4.验证成功5.过期不验证商家操作关闭',
`confirm_status` tinyint(1) NOT NULL COMMENT '订单商家确认状态\r\n0:未确认（未接单，用户未付款可以取消，已付款可直接退款）\r\n1.已确认（商家已接单,客户可与商家联系，申请退款）\r\n2.已配送（商家已配送,不可以申请退款）',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态: 0未支付 1已支付（不做部份付款，支付成功后扣钱）',
`total_price` decimal(20,4) NOT NULL COMMENT '应付总额',
`pay_amount` decimal(20,4) NOT NULL COMMENT '已付总额，pay_amount + promote_amount >=total_price时表示支付成功',
`pay_time` int(11) NOT NULL COMMENT '支付成功时间',
`online_pay` decimal(20,4) NOT NULL COMMENT '在线支付的额度',
`ecv_money` decimal(20,4) NOT NULL COMMENT '代金券的支付额度',
`account_money` decimal(20,4) NOT NULL COMMENT '余额支付',
`payment_id` int(11) NOT NULL COMMENT '支付方式ID，0表示在线支付，1表示货到付款',
`refund_status` tinyint(1) NOT NULL COMMENT '退款状态\r\n0无退款1退款申请中2已退款3退款驳回',
`user_id` int(11) NOT NULL COMMENT '下单会员ID',
`is_cancel` tinyint(1) NOT NULL COMMENT '是否被用户/商家(拒绝接单)取消\r\n0:未取消\r\n1:用户取消\r\n2:商户取消',
`balance_price` decimal(20,4) NOT NULL COMMENT '结算给商家的钱，按门店的预设分配',
`location_name` varchar(255) NOT NULL COMMENT '门店名',
`promote_amount` decimal(20,4) NOT NULL COMMENT '促销优惠金额',
`confirm_time` int(11) NOT NULL COMMENT '确认成功时间，外卖确认到货或者预订验证成功的时间',
`balance_time` int(11) NOT NULL COMMENT '商家结算时间',
`refund_time` int(11) NOT NULL COMMENT '退款时间',
`refund_price` decimal(20,4) NOT NULL COMMENT '退款金额',
PRIMARY KEY  (`id`),
KEY `order_sn` (`order_sn`),
KEY `supplier_id` (`supplier_id`),
KEY `location_id` (`location_id`),
KEY `user_id` (`user_id`),
KEY `order_status` (`order_status`),
KEY `confirm_status` (`confirm_status`),
KEY `pay_status` (`pay_status`),
KEY `refund_status` (`refund_status`),
KEY `is_cancel` (`is_cancel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户已完成订单列表';

DROP TABLE IF EXISTS `%DB_PREFIX%dc_supplier_money_log`;
CREATE TABLE `%DB_PREFIX%dc_supplier_money_log` (
`id` int(11) NOT NULL auto_increment,
`log_info` text NOT NULL COMMENT '资金变更记录',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`create_time` int(11) NOT NULL COMMENT '变更时间',
`money` decimal(20,4) NOT NULL COMMENT '资金变更数额',
`type` tinyint(1) NOT NULL default '0' COMMENT '0:销售额增加 1:资金冻结 2.待结算增加 3.已结算增加 4.退款增加 5.提现增加',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商家外卖财务明细表';


ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `is_dc`  tinyint(1) NOT NULL COMMENT '是否为订餐门店0:否1:是',
ADD COLUMN `dc_cate_match`  text NOT NULL COMMENT '订餐分类的全文索引',
ADD COLUMN `dc_cate_match_row`  text NOT NULL COMMENT '订餐分类全文索引的原文',
ADD COLUMN `is_reserve`  tinyint(1) NOT NULL COMMENT '是否能预定 0:否 1:能 (主要指包间，餐座的预定)',
ADD COLUMN `dc_online_pay`  tinyint(1) NOT NULL COMMENT '是否支持在线支付',
ADD COLUMN `supplier_promote`  text NOT NULL COMMENT '被冗余下来的商家促销规则，即促销接口中的描述，在接口更新描述中清空该字段，访问时同步',
ADD COLUMN `open_time_match`  text NOT NULL COMMENT '营业时间的全文索引配置，每半小时一个时段(11_00,11_30,12_00)由多时段配置表中同步而来',
ADD COLUMN `open_time_match_row`  text NOT NULL COMMENT '营业时间的索引原型，因是数字，该字段与match相同',
ADD COLUMN `is_close`  tinyint(1) NOT NULL COMMENT '是否暂停营业0：否 1：是，商家可以关闭',
ADD COLUMN `open_time_cfg_str`  varchar(255) NOT NULL COMMENT '营业时间的组合字符串，由配置表同步来，用于展示，如09:30-12:00 14:00-16:00 18:00-22:00',
ADD COLUMN `dc_allow_cod`  tinyint(1) NOT NULL COMMENT '是否支持线下支付（即餐到付款）',
ADD COLUMN `balance_type`  tinyint(1) NOT NULL COMMENT '计费方式0:按比例抽每单的营业额 1:按每个菜单的固定结算价结算',
ADD COLUMN `balance_amount`  decimal(20,4) NOT NULL COMMENT '按比例时的提成百分比(0-1之间)，按每单时，填写固定的金额，该值由管理员设置',
ADD FULLTEXT INDEX `dc_cate_match` (`dc_cate_match`),
ADD FULLTEXT INDEX `open_time_match` (`open_time_match`),
ADD INDEX `is_reserve` (`is_reserve`),
ADD INDEX `is_dc` (`is_dc`);



ALTER TABLE `%DB_PREFIX%supplier_location_biz_submit`
ADD COLUMN `is_dc`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否支持订餐';




ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `max_delivery_scale`  int(11) NOT NULL COMMENT '最大配送距离',
ADD COLUMN `dc_location_notice`  text NOT NULL COMMENT '订餐商家公告';



ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `dc_buy_count`  int(11) NOT NULL COMMENT '外卖销售数量统计';



ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `dc_allow_invoice`  tinyint(1) NOT NULL COMMENT '是否支持发票',
ADD COLUMN `dc_allow_ecv`  tinyint(1) NOT NULL COMMENT '是否支持代金卷';


ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `is_payonlinediscount`  tinyint(1) NOT NULL COMMENT '是否支持在线支付优惠',
ADD COLUMN `is_firstorderdiscount`  tinyint(1) NOT NULL COMMENT '是否支持新单立减';

ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `dc_ptag`  int(11) NOT NULL COMMENT '外卖促销：2^(1-7),dc_online_pay：是否在线支付，值定为1 ,dc_allow_cod：支持货到付款，值定为2 ,is_firstorderdiscount：是否支持新单立减，值定为3 ,is_payonlinediscount：是否支持在线支付优惠，值定为4 ,dc_allow_ecv：支持代金卷，值定为5 ,dc_allow_invoice：是否支持发票，值定为6';



ALTER TABLE `%DB_PREFIX%supplier_location_dp` ADD COLUMN `order_id`  int(11) NOT NULL COMMENT '关联到的订餐订单ID，通过订单对商家的点评';

ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `dc_is_share_first`  tinyint(1) NOT NULL COMMENT '是否已经享受过外卖订单首单立减的优惠';

INSERT INTO `%DB_PREFIX%msg_template` VALUES ('', 'TPL_DC_USER_COUPON_SMS', '{$user_name}您好,您已成功预订{$order_info.location_name} {$order_info.table_time_format}到店消费。电子券：{$order_info.sn}。地址：{$order_info.location_address}，电话：{$order_info.location_tel}。请准时到店消费 ', '0', '0','0', '0');

ALTER TABLE `%DB_PREFIX%supplier` ADD COLUMN `is_voice`  tinyint(1) NOT NULL COMMENT '是否有通知音效';

INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'REFRESH_TIME', '10', '5', '0', '', '1', '1', '74');

CREATE TABLE `%DB_PREFIX%user_deal` (
`id` int(11) NOT NULL auto_increment,
`deal_id` int(11) NOT NULL COMMENT '分销的商品ID',
`add_time` int(11) NOT NULL COMMENT '分销获取的时间',
`user_id` int(11) NOT NULL COMMENT '所属的会员',
`add_price` decimal(20,4) NOT NULL COMMENT '分销追加价格(暂不开放)',
`sale_count` int(11) NOT NULL COMMENT '销量(结单的订单才计算销量，以及打款)',
`sale_total` decimal(20,4) NOT NULL COMMENT '总销量',
`sale_balance` decimal(20,4) NOT NULL COMMENT '分销获取的利润(不含推荐人返利)',
`is_effect` tinyint(1) NOT NULL COMMENT '上架与下架（销量记录保留）',
`type` tinyint(1) NOT NULL COMMENT '用户上架商品的类型（0:自行挑选(可下架与删除) 1:管理员分配(不可下架与删除)）',
PRIMARY KEY  (`id`),
KEY `deal_id` (`deal_id`),
KEY `user_id` (`user_id`),
KEY `is_effect` (`is_effect`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员分销商品表';

CREATE TABLE `%DB_PREFIX%deal_fx_salary` (
`deal_id` int(11) NOT NULL COMMENT '商品ID',
`fx_level` int(11) NOT NULL COMMENT '分销返还等级由0开始，0表示为分销店主的分成利润',
`fx_salary` decimal(20,4) NOT NULL COMMENT '分销当前等级的提成率(以会员标价的单前购买价为提成标准)',
`fx_salary_type` tinyint(1) NOT NULL COMMENT '佣金类型：0定额 1比率',
KEY `deal_id` (`deal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品分销提成配置';

CREATE TABLE `%DB_PREFIX%fx_salary` (
`level_id` int(11) NOT NULL COMMENT '分销等级的ID(0表示默认等级，即全局配置)',
`fx_level` int(11) NOT NULL COMMENT '分销返还等级由0开始，0表示为分销店主的分成利润',
`fx_salary` decimal(20,4) NOT NULL COMMENT '分销当前等级的提成率(以会员标价的单前购买价为提成标准)',
`fx_salary_type` tinyint(1) NOT NULL COMMENT '佣金类型：0定额 1比率'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销会员等级分销提成配置';



ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `is_fx`  tinyint(1) NOT NULL COMMENT '是否为分销商品0:不是 1:系统强制分配 2:允许会员领取';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `fx_user_id`  int(11) NOT NULL COMMENT '分销所属的会员ID',
ADD COLUMN `fx_salary`  decimal(20,4) NOT NULL COMMENT '分销销售佣金，发放佣金时更新',
ADD COLUMN `fx_salary_total`  decimal(20,4) NOT NULL COMMENT '分销佣金总额，发放佣金时更新',
ADD COLUMN `fx_salary_all`  text NOT NULL COMMENT '分销佣金的等级配比，序列化后的分销佣金配比[0级分销佣金,1级佣金,2级佣金...]，发放佣金时更新';

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `is_fx`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否允许分销(默认都允许)',
ADD COLUMN `fx_total_money`  decimal(20,4) NOT NULL COMMENT '分销的累积营业额',
ADD COLUMN `fx_total_balance`  decimal(20,4) NOT NULL COMMENT '分销的累积利润',
ADD COLUMN `fx_money`  decimal(20,4) NOT NULL COMMENT '分销的利润（可提现）',
ADD COLUMN `fx_level`  int(11) NOT NULL COMMENT '分销会员的等级(0表示默认等级)',
ADD COLUMN `fx_mall_bg`  varchar(255) NOT NULL COMMENT '分销小店背景';


CREATE TABLE `%DB_PREFIX%fx_level` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL COMMENT '分销等级名称',
`money` decimal(20,4) NOT NULL COMMENT '分销等级的累积营业额',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销会员的等级';


CREATE TABLE `%DB_PREFIX%fx_withdraw` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL COMMENT '会员ID',
`money` decimal(20,4) NOT NULL COMMENT '提现金额',
`create_time` int(11) NOT NULL,
`is_paid` tinyint(1) NOT NULL COMMENT '是否已确认',
`pay_time` int(11) NOT NULL COMMENT '确认支付时间',
`bank_name` varchar(255) NOT NULL COMMENT '开户行名称',
`bank_account` varchar(255) NOT NULL COMMENT '开户行账号',
`bank_user` varchar(255) NOT NULL COMMENT '开户行会员名',
`type` tinyint(1) NOT NULL COMMENT '提现至：0提现至余额 1提现至银行卡',
`is_delete` tinyint(1) NOT NULL COMMENT '是否会员删除',
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销提现';


CREATE TABLE `%DB_PREFIX%fx_statements` (
`id` int(11) NOT NULL auto_increment,
`sale_money` decimal(20,4) NOT NULL COMMENT '分销订单的总营业额',
`fx_extend_salary` decimal(20,4) NOT NULL COMMENT '推广产生的佣金',
`fx_salary` decimal(20,4) NOT NULL COMMENT '分销产生的佣金',
`fx_withdraw` decimal(20,4) NOT NULL COMMENT '佣金提现',
`stat_time` date NOT NULL,
`stat_month` varchar(10) NOT NULL,
PRIMARY KEY  (`id`),
KEY `stat_time` (`stat_time`),
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销的平台报表';


CREATE TABLE `%DB_PREFIX%fx_statements_log` (
`id` int(11) NOT NULL auto_increment,
`money` decimal(20,4) NOT NULL COMMENT '分销报表的资金',
`create_time` int(11) NOT NULL COMMENT '发生时间',
`type` tinyint(1) NOT NULL COMMENT '类型0:营业销 1分销佣金2推广佣金 3分销提现',
`log` text NOT NULL COMMENT '日志内容',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销的平台报表日志';


CREATE TABLE `%DB_PREFIX%fx_user_money_log` (
`id` int(11) NOT NULL auto_increment,
`money` decimal(20,4) NOT NULL,
`user_id` int(11) NOT NULL,
`create_time` int(11) NOT NULL,
`log` text NOT NULL COMMENT '日志内容',
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销的会员资金表';


CREATE TABLE `%DB_PREFIX%fx_user_reward` (
`pid` int(11) NOT NULL COMMENT '分销商ID',
`user_id` int(11) NOT NULL COMMENT '每个会员针对每个分销商的返佣情况',
`money` decimal(20,4) NOT NULL COMMENT '总返佣的钱'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个会员针对每个分销商的返佣情况';

ALTER TABLE `%DB_PREFIX%fx_user_reward`
ADD UNIQUE INDEX `uk` (`pid`, `user_id`);



CREATE TABLE `%DB_PREFIX%agency` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '代理商名',
`account_name` varchar(255) NOT NULL COMMENT '代理商帐号名',
`account_password` varchar(255) NOT NULL COMMENT '代理商帐号密码',
`mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
`is_effect` tinyint(1) NOT NULL,
`is_delete` tinyint(1) NOT NULL,
`province_id` int(11) NOT NULL COMMENT '省份ID',
`city_id` int(11) NOT NULL COMMENT '城市ID',
`region_id` tinyint(1) NOT NULL COMMENT '商圈ID',
`bank_name` varchar(255) NOT NULL COMMENT '提现的开户行名称',
`bank_user` varchar(255) NOT NULL COMMENT '提现的开户行户名',
`bank_info` text NOT NULL COMMENT '提现银行帐号',
`login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
`login_time` int(11) NOT NULL COMMENT '最后登录时间',
PRIMARY KEY (`id`),
KEY `city_id` (`city_id`),
KEY `region_id` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理商表';


ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `agency_id`  int(11) NOT NULL COMMENT '代理商ID';

ALTER TABLE `%DB_PREFIX%supplier_submit`
ADD COLUMN `agency_id`  int(11) NOT NULL COMMENT '代理商ID';

INSERT INTO `%DB_PREFIX%conf` VALUES ('','ADMIN_FEE_RATE','10','7','0','','1','1','1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('','AGENCY_WITHDRAW_DAY','3','7','0','','1','1','1');

CREATE TABLE `%DB_PREFIX%agency_statements` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`agency_id` int(11) NOT NULL,
`total_money` decimal(20,4) NOT NULL COMMENT '本日总佣金',
`toady_wd_money` decimal(20,4) NOT NULL COMMENT '本日可提现佣金',
`sale_money` decimal(20,4) NOT NULL COMMENT '本日团购商城佣金',
`dc_sale_money` decimal(20,4) NOT NULL COMMENT '本日外卖佣金',
`store_pay_money` decimal(20,4) NOT NULL COMMENT '本日优惠买单佣金',
`wd_money` decimal(20,4) NOT NULL COMMENT '本日提现',
`wd_status` tinyint(1) NOT NULL COMMENT '本日提现状态，0为未提现，1为已提现，2为提交申请',
`stat_time` date NOT NULL COMMENT '报表日期',
`stat_month` varchar(10) NOT NULL COMMENT '月份',
PRIMARY KEY (`id`),
KEY `agency_id` (`agency_id`),
KEY `stat_time` (`stat_time`),
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理商日报表';



CREATE TABLE `%DB_PREFIX%agency_statements_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`agency_id` int(11) NOT NULL COMMENT '代理商ID',
`create_time` int(11) NOT NULL,
`type` tinyint(1) NOT NULL COMMENT '1.团购商城 2.外卖 3.优惠买单 4.提现',
`money` decimal(20,4) NOT NULL,
`log_info` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理商财务报表日志';


CREATE TABLE `%DB_PREFIX%agency_money_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`agency_id` int(11) NOT NULL COMMENT '代理商ID',
`money` decimal(20,4) NOT NULL COMMENT '提现金额',
`create_time` int(11) NOT NULL,
`is_paid` tinyint(1) NOT NULL COMMENT '是否已确认',
`pay_time` int(11) NOT NULL COMMENT '确认支付时间',
`bank_name` varchar(255) NOT NULL COMMENT '开户行名称',
`bank_account` varchar(255) NOT NULL COMMENT '开户行账号',
`bank_user` varchar(255) NOT NULL COMMENT '开户行会员名',
`is_delete` tinyint(1) NOT NULL,
PRIMARY KEY (`id`),
KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理商提现表';


ALTER TABLE `%DB_PREFIX%agency_money_submit`
ADD COLUMN `id_str`  varchar(255) NOT NULL COMMENT '该提现申请包含了agency_statements表中哪些id';

ALTER TABLE `%DB_PREFIX%supplier_statements`
ADD COLUMN `deal_sale_money`  decimal(20,4) NOT NULL COMMENT '统计商户团购商城营业额(不是结算金额)' AFTER `supplier_id`,
ADD COLUMN `store_pay_money`  decimal(20,4) NOT NULL COMMENT '统计商户优惠买单营业额(不是结算金额)' AFTER `deal_sale_money`;


CREATE TABLE `%DB_PREFIX%form_verify` (
`session_id` varchar(255) NOT NULL,
`verify_data` text NOT NULL,
`update_time` varchar(20) NOT NULL,
PRIMARY KEY (`session_id`),
KEY `session_id` (`session_id`),
KEY `update_time` (`update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `%DB_PREFIX%weixin_account_conf`;
CREATE TABLE `%DB_PREFIX%weixin_account_conf` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`name` varchar(255) NOT NULL,
`value` text NOT NULL,
`group_id` int(11) NOT NULL,
`type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
`value_scope` text NOT NULL COMMENT '取值范围',
`is_require` tinyint(1) NOT NULL,
`is_effect` tinyint(1) NOT NULL,
`is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='微信账号配置选项';


INSERT INTO `%DB_PREFIX%weixin_account_conf` VALUES ('9', '移动应用AppID', 'mappid', '', '0', '0', '', '0', '1', '1', '0');
INSERT INTO `%DB_PREFIX%weixin_account_conf` VALUES ('10', '移动应用AppSecret', 'mappsecret', '', '0', '0', '', '0', '1', '1', '0');


ALTER TABLE `%DB_PREFIX%weixin_user`
ADD COLUMN `union_id`  varchar(255) NULL;

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `union_id`  varchar(255) NOT NULL;

ALTER TABLE `%DB_PREFIX%weixin_user`
ADD COLUMN `m_openid`  varchar(255) NULL;

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `m_openid`  varchar(255) NOT NULL;


ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';
ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';


ALTER TABLE `%DB_PREFIX%m_index`
ADD COLUMN `iconbgcolor` varchar(100) DEFAULT '' COMMENT '分类图标的背景颜色';


ALTER TABLE `%DB_PREFIX%deal_cate`
ADD COLUMN `m_iconfont` varchar(100) DEFAULT NULL COMMENT 'WAP分类图标',
ADD COLUMN `m_iconcolor` varchar(100) DEFAULT '' COMMENT 'WAP分类图标颜色',
ADD COLUMN `m_iconbgcolor` varchar(100) DEFAULT '' COMMENT 'WAP分类图标的背景颜色';


ALTER TABLE `%DB_PREFIX%shop_cate`
ADD COLUMN `m_iconfont` varchar(100) DEFAULT NULL COMMENT 'WAP分类图标',
ADD COLUMN `m_iconcolor` varchar(100) DEFAULT '' COMMENT 'WAP分类图标颜色',
ADD COLUMN `m_iconbgcolor` varchar(100) DEFAULT '' COMMENT 'WAP分类图标的背景颜色';


ALTER TABLE `%DB_PREFIX%m_zt`
ADD COLUMN `page`  varchar(255) NOT NULL COMMENT '专题位显示的页面，1为首页，2为团购首页，3.为商城首页';

ALTER TABLE `%DB_PREFIX%deal_cart`
ADD COLUMN `is_effect` tinyint(1) NOT NULL DEFAULT '0' COMMENT '购物车中商品的状态，0为无效，1为有效';


ALTER TABLE `%DB_PREFIX%deal_cart`
ADD COLUMN `is_disable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '购物车中商品的失效状态，0为未失效，1为失效，该字段用于统计商户或后台更改价格或者属性导致购物车中的商品与原商品不符而失效';

ALTER TABLE `%DB_PREFIX%shop_cate`
ADD COLUMN `cate_img` varchar(255) NOT NULL COMMENT '手机端的分类小图';
ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `is_location`  tinyint(1) NOT NULL COMMENT '存在支持门店，0：否1：是';
UPDATE fanwe_deal SET fanwe_deal.is_location = 1 WHERE (select count(*) from fanwe_deal_location_link  where fanwe_deal_location_link.deal_id=fanwe_deal.id  );
DROP TABLE IF EXISTS `%DB_PREFIX%agreement`;
CREATE TABLE `%DB_PREFIX%agreement` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
`name` varchar(255) NOT NULL COMMENT '名称',
`agreement` text NOT NULL COMMENT '用户协议',
`sort` int(11) NOT NULL COMMENT '排序',
`is_effect` tinyint(1) NOT NULL,
`create_time` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户协议表';
INSERT INTO `%DB_PREFIX%agreement` VALUES ('1', '用户协议', '<article class=\"protocol\">            <section>                <h2>第一条 定义</h2>\n                <p>section：与 div 的无语义相对，简单地说 section 就是带有语义的 div 了，但是千万不要觉得真得这么简单。section 表示一段专题性的内容，一般会带有标题。</p>\n            </section>            <section>                <h2>第二条 会员</h2>\n                <p>section 应用的典型场景有文章的章节、标签对话框中的标签页、或者论文中有编号的部分。一个网站的主页可以分成简介、新闻和联系信息等几部分。</p>\n            </section>            <section>                <h2>第三条 会员</h2>\n                <p>section 不仅仅是一个普通的容器标签。当一个标签只是为了样式化或者方便脚本使用时，应该使用 div 。一般来说，当元素内容明确地出现在文档大纲中时.</p>\n            </section>            <section>                <h2>第三条 会员</h2>\n                <p>article为可以嵌套的元素，原则上需article内层应与外层的内容相关联，例如一篇文章的正文及对正文的评论就可以同过article进行嵌套</p>\n            </section>        </article>', '1', '1', '1477880143');
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `consignee_id` int(11) NOT NULL COMMENT '会员收货地址id';

ALTER TABLE `%DB_PREFIX%event`
ADD COLUMN `img` varchar(255) NOT NULL COMMENT 'wap活动列表展示图';

ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `district`  text NOT NULL COMMENT '门店区域';

ALTER TABLE `%DB_PREFIX%supplier_location_biz_submit`
ADD COLUMN `district`  text NOT NULL COMMENT '门店区域';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN`is_delivery` tinyint(1) NOT NULL COMMENT '是否需要配送（实体商品），需要配送的产品前台会出现配送方式的选项，并计算相应运费';


ALTER TABLE `%DB_PREFIX%youhui`
ADD COLUMN `youhui_value` int(4) NOT NULL COMMENT '类型为减免(0)时为减免的金额/类型为折扣(1)时为折扣比例(0--100%)';

ALTER TABLE `%DB_PREFIX%youhui_biz_submit`
ADD COLUMN `youhui_value` int(4) NOT NULL COMMENT '类型为减免(0)时为减免的金额/类型为折扣(1)时为折扣比例(0--100%)';


ALTER TABLE `%DB_PREFIX%event`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';
ALTER TABLE `%DB_PREFIX%event_biz_submit`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';

ALTER TABLE `%DB_PREFIX%youhui`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';
ALTER TABLE `%DB_PREFIX%youhui_biz_submit`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';


ALTER TABLE `%DB_PREFIX%user_bank`
ADD COLUMN `use_times` int(11) NOT NULL COMMENT '使用次数';

ALTER TABLE `%DB_PREFIX%youhui_log`
ADD COLUMN `expire_msg` tinyint(1) NOT NULL COMMENT '优惠券过期信息是否发送：0-未发送，1-已发送即将过期信息，2-已发送过期信息';

ALTER TABLE `%DB_PREFIX%ecv`
ADD COLUMN `expire_msg` tinyint(1) NOT NULL COMMENT '红包过期信息是否发送：0-未发送，1-已发送即将过期信息，2-已发送过期信息';


ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `is_cancel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '和is_delete都为1时才为取消状态';


ALTER TABLE `%DB_PREFIX%m_index`
ADD COLUMN `page`  varchar(255) NOT NULL COMMENT '菜单显示的页面，1为首页，2为团购首页，3.为商城首页 ，4为积分商城';

update `%DB_PREFIX%m_index` set `page`='1,4';


CREATE TABLE `%DB_PREFIX%track` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
`supplier_id` int(11) NOT NULL COMMENT '商家ID',
`user_id` int(11) NOT NULL COMMENT '用户ID',
`order_id` int(11) DEFAULT NULL COMMENT '订单编号',
`express_company` varchar(20) DEFAULT NULL COMMENT '快递公司',
`express_code` varchar(20) DEFAULT NULL COMMENT '快递公司代码',
`express_number` varchar(32) DEFAULT NULL COMMENT '快递单号',
`state` int(11) NOT NULL DEFAULT '0' COMMENT '当前状态:0 在途中、1 已揽收、2 疑难、3 已签收、4 退签、5 同城 派送中、6 退回、7 转单',
`ischeck` int(11) DEFAULT '0' COMMENT '是否签收',
`data` text COMMENT '状态详细',
`type` tinyint(1) unsigned DEFAULT '0' COMMENT '0物流公司 1其他物流 2无需物流',
`remark` text COMMENT '备注',
`order_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:全部订单 ,1:外卖预定订单,2:商户订单,3:普通订单,4:会员买单',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单跟踪';

INSERT INTO `%DB_PREFIX%conf` VALUES ('','EXPRESS_KEY','6d80e9c4db33f1a700c3f30cc5079c47','1','0','','1','1','84');



ALTER TABLE `%DB_PREFIX%ecv`
ADD COLUMN `is_read` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '新红包标识 0:未查看 1:已查看';

ALTER TABLE `%DB_PREFIX%youhui_log`
ADD COLUMN `is_read` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '新券标识 0:未查看 1:已查看';

ALTER TABLE `%DB_PREFIX%event_submit`
ADD COLUMN `is_read` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '新活动标识 0:未查看 1:已查看';


ALTER TABLE `%DB_PREFIX%topic`
ADD COLUMN `fav_id_name` TEXT NULL  COMMENT '点赞数据(会员id=>会员名)序列化数组';

ALTER TABLE `%DB_PREFIX%topic_reply`
MODIFY COLUMN `content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '回复内容';

ALTER TABLE `%DB_PREFIX%topic`
MODIFY COLUMN `content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容';

ALTER TABLE `%DB_PREFIX%deal`
ADD INDEX `uname` (`uname`) USING BTREE ;

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `promote_arr` text NOT NULL COMMENT '订单享受促销信息';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `record_delivery_fee` decimal(20,4) NOT NULL COMMENT '记录的运费';
ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `message_id` int(11) NOT NULL COMMENT '退款消息messageid';
ALTER TABLE `%DB_PREFIX%deal_coupon`
ADD COLUMN `message_id` int(11) NOT NULL COMMENT '退款消息messageid';

DROP TABLE IF EXISTS `%DB_PREFIX%ip`;
CREATE TABLE `%DB_PREFIX%ip` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
`ip` varchar(50) NOT NULL COMMENT 'ip 地址',
`nation` varchar(30) NOT NULL COMMENT '国家',
`provincial` varchar(30) NOT NULL COMMENT '省会',
`city` varchar(30) NOT NULL COMMENT '城市',
`service` varchar(30) DEFAULT NULL COMMENT '运营商',
PRIMARY KEY (`id`),
UNIQUE KEY `ipindex` (`ip`) USING BTREE,
KEY `city` (`city`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `%DB_PREFIX%supplier_location_dp`
MODIFY COLUMN `content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容';

ALTER TABLE `%DB_PREFIX%supplier_location_dp_reply`
MODIFY COLUMN `content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容';

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `fx_total_vip_buy` DECIMAL(20,4) DEFAULT 0  NOT NULL  COMMENT '推荐的会员购买分销资格的累积返佣';

ALTER TABLE `%DB_PREFIX%fx_statements_log`
ADD COLUMN `user_id` INT NOT NULL  COMMENT '分销会员ID';



alter table %DB_PREFIX%user alter column is_fx drop default;
alter table %DB_PREFIX%user alter column is_fx set default "0";

ALTER TABLE `%DB_PREFIX%fx_statements`
ADD COLUMN `vip_buy_salary` decimal(20,4) NOT NULL COMMENT '推荐佣金';

CREATE TABLE `%DB_PREFIX%fx_buy_order` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(255) NOT NULL DEFAULT '订单号',
`create_time` int(11) NOT NULL,
`user_id` int(11) NOT NULL COMMENT '用户id',
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 2:全部付款',
`total_price` decimal(20,4) NOT NULL COMMENT '总费用',
`fx_price` decimal(20,4) NOT NULL COMMENT '分销购买费用',
`rebate` decimal(20,4) NOT NULL COMMENT '上级返利金额',
`rebate_data` text NOT NULL COMMENT '返利数据',
`order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`payment_id` int(11) NOT NULL COMMENT '支付方式',
`bank_id` varchar(255) NOT NULL COMMENT '''银行直连支付的银行编号',
`extra_status` tinyint(1) NOT NULL COMMENT '额外的订单标识 0:正常的订单 1.金额超额产生退款的订单（多次支付，重付通知） ,自动退款到用户的订单）',
`payment_fee` decimal(20,4) NOT NULL COMMENT '手续费',
`pay_amount` decimal(20,4) NOT NULL COMMENT '支付金额',
PRIMARY KEY (`id`),
UNIQUE KEY `unique_sn` (`order_sn`) USING BTREE,
KEY `user_id` (`user_id`),
KEY `pay_status` (`pay_status`) USING BTREE,
KEY `order_status` (`order_status`) USING BTREE,
KEY `is_delete` (`is_delete`) USING BTREE,
KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分销资格购买订单表';

CREATE TABLE `%DB_PREFIX%fx_buy_order_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL,
`log_time` int(11) NOT NULL,
`order_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销资格购买订单操作的日志表';

CREATE TABLE `%DB_PREFIX%fx_qualification` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) DEFAULT NULL COMMENT '分销员名称',
`pay_fee` decimal(20,4) DEFAULT NULL COMMENT '缴纳费用',
`tz_award` decimal(20,4) DEFAULT NULL COMMENT '推荐奖励',
`pay_agreement` text COMMENT '购买协议',
`pc_privilege` text COMMENT 'pc特权',
`phone_description` text COMMENT '手机特权',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分销资质设置表';

ALTER TABLE `%DB_PREFIX%ecv` ADD INDEX (`expire_msg`) USING BTREE ;
ALTER TABLE `%DB_PREFIX%youhui_log` ADD INDEX (`expire_msg`) USING BTREE ;

ALTER TABLE `%DB_PREFIX%deal_cate`
ADD COLUMN `app_icon_img` varchar(255) NOT NULL COMMENT 'app团购首页菜单背景图';
ALTER TABLE `%DB_PREFIX%shop_cate`
ADD COLUMN `app_icon_img` varchar(255) NOT NULL COMMENT 'app商城首页菜单背景图';


ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `platform_type`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '发布平台类型(默认平台发布)';

ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `delivery_type`  tinyint(1) NULL DEFAULT 1 COMMENT '配送方式(默认物流配送)1物流、2无需配送、3驿站' ;

ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `dist_service_rate`  decimal(20,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '驿站服务费率';

ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `carriage_template_id`  int(11) NOT NULL DEFAULT 0 COMMENT '运费模板关联ID';

ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `recommend_user_id`  int(11) NULL DEFAULT 0 COMMENT '此商品的推荐会员ID' ,
ADD COLUMN `recommend_user_return_ratio`  decimal(20,2) NULL DEFAULT 0 COMMENT '推荐会员返佣率,必须存在推荐会员ID' ;

CREATE TABLE `%DB_PREFIX%carriage_detail` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`carriage_id` int(11) NOT NULL COMMENT '关联运费模板ID',
`express_start` decimal(10,2) DEFAULT NULL COMMENT '初始运费商品：多少件或kg',
`express_postage` decimal(10,2) DEFAULT '0.00' COMMENT '运费金额',
`express_plus` decimal(10,2) DEFAULT NULL COMMENT '增加运费商品：多少件或kg',
`express_postage_plus` decimal(10,2) DEFAULT '0.00' COMMENT '运费根据商品增加增加的金额',
`region_ids` text COMMENT '指定运费设置的地区（地区ID 根据逗号分隔）',
`sort`  int(11) NULL DEFAULT 0 COMMENT '排序，默认配送为0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='运费价格详情表';


CREATE TABLE `%DB_PREFIX%carriage_template` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL COMMENT '模板名称',
`country` int(11) DEFAULT '1' COMMENT '发货国家',
`province` int(11) DEFAULT NULL COMMENT '发货省份',
`city` int(11) DEFAULT NULL COMMENT '发货城市',
`area` int(11) DEFAULT NULL COMMENT '发货区县',
`carriage_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '运费类型：1自定义，2平台/卖家承担运费（免运费）',
`valuation_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '计价类型：1按件数，2按重量',
`tpl_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模板类型：1快递',
`supplier_id` int(11) DEFAULT '0' COMMENT '商户ID：如果存在就为商户的运费模板，没有则为平台',
`supplier_name`  varchar(255) NULL COMMENT '商户名称',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='物流模板表';


ALTER TABLE `%DB_PREFIX%carriage_template`
ADD COLUMN `is_use`  tinyint NOT NULL DEFAULT 0 COMMENT '是否被商品使用过（如果为1则不可被删除）' AFTER `supplier_name`,
ADD COLUMN `create_time`  int(11) NOT NULL DEFAULT 0 COMMENT '第一次创建数据的时间' AFTER `is_use`,
ADD COLUMN `update_time`  int(11) NOT NULL DEFAULT 0 COMMENT '最后一次更新数据的时间' AFTER `create_time`;

ALTER TABLE `%DB_PREFIX%carriage_template`
ADD COLUMN `cache_carriage_detail_data`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板计算价格明细缓存' AFTER `update_time`,
ADD COLUMN `is_region`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否有指定区域' AFTER `cache_carriage_detail_data`;


ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `carriage_template_id`  int(11) NOT NULL DEFAULT 0 COMMENT '运费模板关联ID';

ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `delivery_type`  tinyint(1) NULL DEFAULT 1 COMMENT '配送方式(默认物流配送)1物流、2无需配送、3驿站' ;

CREATE TABLE `%DB_PREFIX%use_coupon_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
`data_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购券自提order_id, 其它都是优惠券自增id',
`name` varchar(255) DEFAULT '' COMMENT '商品名称',
`pwd` varchar(20) DEFAULT '' COMMENT '券的序列号',
`type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1团购 ，2优惠券，3活动 ，4自提',
`location_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
`create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消费时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



ALTER TABLE `%DB_PREFIX%supplier_money_submit`
ADD COLUMN `bank_name`  text NOT NULL COMMENT '开户行',
ADD COLUMN `bank_info`  text NOT NULL COMMENT '银行卡号',
ADD COLUMN `bank_user`  text NOT NULL COMMENT '持卡人姓名';


ALTER TABLE `%DB_PREFIX%store_pay_order`
ADD COLUMN `create_ym`  int(11) NOT NULL COMMENT '订单创建年月格式:Ym 例(201605)';

UPDATE `%DB_PREFIX%store_pay_order` set create_ym = FROM_UNIXTIME(create_time,'%Y%m');

ALTER TABLE `%DB_PREFIX%deal_cart`
ADD COLUMN `in_cart`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '该商品是否经过购物车页面，1为经过购物车页面，0为下单后，不经过购物车，直接进入订单提交页面';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `is_main`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是订单的主单，1为订单的主单，0为订单的子单';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `supplier_id`  int(11) NOT NULL DEFAULT 0 COMMENT '商家ID';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `supplier_data`  text NOT NULL COMMENT '主单中，各商家的订单信息，包括各商家的运费和会员备注等信息';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `order_id`  int(11) NOT NULL DEFAULT 0 COMMENT '主单ID';
ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `delivery_type`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '配送方式(默认物流配送)1物流、2无需配送、3驿站';
ALTER TABLE `%DB_PREFIX%deal_order`
MODIFY COLUMN `type`  tinyint(1) NOT NULL COMMENT '订单类型(0:商品订单 1:用户充值单,2:积分兑换订单，3:平台自营物流配送订单，4:平台自营驿站配送订单,5:商家团购订单,6:商家商品订单)';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `location_id`  int(11) NOT NULL DEFAULT 0 COMMENT '自提门店ID';

update `%DB_PREFIX%deal` set is_delivery=1 where is_shop=1 and (delivery_type=1 or delivery_type=3);
update `%DB_PREFIX%deal` set is_delivery=0 where is_shop=1 and delivery_type=2;
update `%DB_PREFIX%deal` set allow_promote=0;

ALTER TABLE `%DB_PREFIX%payment_notice`
ADD COLUMN `sub_order_data`  text NOT NULL COMMENT '子订单的付款数据';


ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `deal_data`  text NOT NULL COMMENT '用于冗余商品，返佣，服务费等数据';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `is_balance_recommend_money`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '商品会员推荐返佣是否已结算';

ALTER TABLE `%DB_PREFIX%deal_coupon`
ADD COLUMN `is_balance_recommend_money`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '团购会员推荐返佣是否已结算';



insert  into `%DB_PREFIX%msg_template`(`name`,`content`,`type`,`is_html`,`is_allow_app`,`is_allow_wx`) values ('TPL_SUPPLIER_ORDER_DELIVERY','{$supplier_name},您的订单{$order_sn}已确认收货',0,0,0,0),('TPL_SUPPLIER_ORDER_DP','{$supplier_name},您的订单{$order_sn}已完成评价，前往查看',0,0,0,0),('TPL_SUPPLIER_ORDER_REFUND','{$supplier_name},您有新的退款信息需要处理，前往处理',0,0,0,0),('TPL_SUPPLIER_ORDER_DONE','{$supplier_name},订单{$order_sn}已结算，余额增加{$money_format}',0,0,0,0),('TPL_SUPPLIER_WITHDRAW','{$supplier_name},您的提现申请已提交，余额扣除{$money_format}',0,0,0,0);


UPDATE `%DB_PREFIX%msg_template` SET `content` = '{$supplier_name},您的提现申请已提交，余额扣除{$money}' WHERE `name` = 'TPL_SUPPLIER_WITHDRAW';


CREATE TABLE `%DB_PREFIX%biz_msg_box` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`content` TEXT NOT NULL COMMENT '内容',
`supplier_id` INT(11) NOT NULL COMMENT '消息所属的商家id',
`create_time` INT(11) NOT NULL COMMENT '消息产生时间',
`is_read` TINYINT(1) DEFAULT 0 NOT NULL COMMENT '是否已读 0:未读 1:已读',
`is_delete` TINYINT(1) DEFAULT 0 NOT NULL COMMENT '是否被用户删除',
`type` VARCHAR(25) NOT NULL COMMENT '消息接口类型',
`data` TEXT NOT NULL COMMENT '消息相关数据集，序列化后用于接口调用',
PRIMARY KEY (`id`),
KEY `supplier_id` (`supplier_id`),
KEY `is_read` (`is_read`),
KEY `is_delete` (`is_delete`)
) ENGINE=MyISAM COMMENT='商户站内信表';

UPDATE `%DB_PREFIX%msg_template` SET `content` = '{$user_name}您好,您已成功预订{$order_info.location_name} {$order_info.table_time_format}到店消费。电子券：{$order_info.sn}。地址：{$order_info.location_address}，电话：{$order_info.location_tel}。请准时到店消费 ' WHERE `name` = 'TPL_DC_USER_COUPON_SMS';

INSERT INTO `%DB_PREFIX%msg_template` (`name`, `content`, `type`, `is_html`) VALUES ('TPL_SUPPLIER_WITHDRAW_FAIL_SMS', '您的提现申请被驳回', '0', '0');

UPDATE `%DB_PREFIX%msg_template` SET `content` = '{$supplier_name},{$order_sn}已确认收货,余额增加{$money}' WHERE `name` = 'TPL_SUPPLIER_ORDER_DELIVERY';

UPDATE `%DB_PREFIX%msg_template` SET `content` = '{$supplier_name}您好，您的提现申请已通过,资金已转入您的银行卡({$bank_name} {$bank_info})中。' WHERE `name` = 'TPL_SUPPLIER_WITHDRAW_SMS';

UPDATE `%DB_PREFIX%msg_template` SET `content` = '{$supplier_name},{$order_sn}已确认收货,余额增加{$money}' WHERE `name` = 'TPL_SUPPLIER_ORDER_DONE';

ALTER TABLE `%DB_PREFIX%user_consignee`
ADD COLUMN `street` VARCHAR(50) NULL  COMMENT '小区街道信息',
ADD COLUMN `xpoint` VARCHAR(255) NULL  COMMENT '收货地址经度',
ADD COLUMN `ypoint` VARCHAR(255) NULL  COMMENT '收货地址纬度',
ADD COLUMN `is_temp` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '临时地址，不展示做地图标记用';


ALTER TABLE `%DB_PREFIX%user_consignee`
DROP COLUMN `is_temp`,
ADD COLUMN `doorplate` VARCHAR(50) NULL  COMMENT '地址门牌号';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `distribution_id`  int(11) NOT NULL DEFAULT 0 COMMENT '分配的配送驿站id';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `refund_money`  decimal(20,4) NULL COMMENT '商品退款金额' AFTER `refund_status`;
ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `admin_memo`  text COMMENT '管理员备注';
ALTER TABLE `%DB_PREFIX%deal_coupon`
ADD COLUMN `admin_memo`  text COMMENT '管理员备注';
ALTER TABLE `%DB_PREFIX%deal_coupon`
ADD COLUMN `refund_money`  decimal(20,4) NOT NULL DEFAULT '0' COMMENT '团购优惠券退款金额';


ALTER TABLE `%DB_PREFIX%deal_order_history`
ADD COLUMN `is_main`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否主订单';

ALTER TABLE `%DB_PREFIX%deal_order_history`
ADD COLUMN `supplier_id`  int UNSIGNED NOT NULL;


ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN  `street` varchar(50) DEFAULT NULL COMMENT '小区街道信息',
ADD COLUMN  `doorplate` varchar(50) DEFAULT NULL COMMENT '地址门牌号';


CREATE TABLE `%DB_PREFIX%distribution_msg_box` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`content` text NOT NULL COMMENT '内容',
`distribution_id` int(11) NOT NULL COMMENT '消息所属的驿站id',
`create_time` int(11) NOT NULL COMMENT '消息产生时间',
`is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读 0:未读 1:已读',
`is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被用户删除',
`type` varchar(25) NOT NULL COMMENT '消息接口类型',
`data` text NOT NULL COMMENT '消息相关数据集，序列化后用于接口调用',
PRIMARY KEY (`id`),
KEY `distribution_id` (`distribution_id`),
KEY `is_read` (`is_read`),
KEY `is_delete` (`is_delete`)
) ENGINE=MyISAM CHARSET=utf8 COMMENT='驿站消息表';

INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'DISTRIBUTION_ORDER_NOTIFY', '1', '5', '1', '0,1', '1', '1', '51');

INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'ios_dist_version', '驿站ios版本号(yyyymmddnn)', NULL, '0', '驿站app升级设置', '0');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'ios_dist_down_url', '驿站ios下载地址(appstore下载地址)', NULL, '0', '驿站app升级设置', '1');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'ios_dist_upgrade', '驿站ios版本升级内容', NULL, '3', '驿站app升级设置', '2');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('','ios_dist_forced_upgrade', 'ios是否强制升级(0:否;1:是)', '0', '0', '驿站app升级设置', '3');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'android_dist_version', '驿站android版本号(yyyymmddnn)', NULL, '0', '驿站app升级设置', '4');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'android_dist_down_url', '驿站android下载地址', NULL, '0', '驿站app升级设置', '5');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'android_dist_upgrade', '驿站android版本升级内容', NULL, '3', '驿站app升级设置', '6');
INSERT INTO `%DB_PREFIX%m_config` (`id`, `code`, `title`, `val`, `type`, `group_name`, `sort`) VALUES ('', 'android_dist_forced_upgrade', 'android是否强制升级(0:否;1:是)', '0', '0', '驿站app升级设置', '7');


CREATE TABLE `%DB_PREFIX%distribution` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`username` varchar(100) NOT NULL COMMENT '账号',
`password` char(32) NOT NULL COMMENT '密码',
`name` varchar(255) NOT NULL COMMENT '配送点名称',
`address` text NOT NULL COMMENT '详细地址',
`tel` varchar(255) NOT NULL COMMENT '配送点电话',
`contact` varchar(255) NOT NULL COMMENT '联系人',
`open_time` varchar(255) NOT NULL COMMENT '营业时间',
`city_id` int(10) NOT NULL COMMENT '城市ID',
`xpoint` varchar(255) NOT NULL COMMENT '经度',
`ypoint` varchar(255) NOT NULL COMMENT '纬度',
`deleted` tinyint(1) NOT NULL COMMENT '是否删除（0：否，1：是）',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='配送点';

CREATE TABLE `%DB_PREFIX%distribution_shipping` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`dist_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '驿站ID',
`region_lv1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
`region_lv2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省',
`region_lv3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '市',
`region_lv4` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区',
`poi_addr` varchar(255) NOT NULL DEFAULT '' COMMENT '地图的地址信息',
`poi_name` varchar(255) NOT NULL DEFAULT '' COMMENT '地图的地址名称',
`xpoint` varchar(255) NOT NULL DEFAULT '' COMMENT '经度',
`ypoint` varchar(255) NOT NULL DEFAULT '' COMMENT '纬度',
`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0=否，1=是',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='驿站配送设置';

ALTER TABLE `%DB_PREFIX%distribution`
ADD COLUMN `bank_card`  varchar(22) NOT NULL COMMENT '提现银行卡号' AFTER `deleted`,
ADD COLUMN `bank_name`  varchar(255) NOT NULL COMMENT '提现的开户行名称' AFTER `bank_card`,
ADD COLUMN `bank_user`  varchar(255) NOT NULL COMMENT '提现的开户用户名' AFTER `bank_name`,
ADD COLUMN `money`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '可以提余额' AFTER `bank_user`,
ADD COLUMN `service_total_money`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '服务费总额' AFTER `money`;


CREATE TABLE `%DB_PREFIX%distribution_statements` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`distribution_id` int(11) NOT NULL,
`sale_money` decimal(20,4) NOT NULL COMMENT '本日的服务费总额',
`withdrawals_money` decimal(20,4) NOT NULL COMMENT '本日提现',
`stat_time` date NOT NULL COMMENT '报表日期 格式 2015-02-26',
`stat_month` varchar(10) NOT NULL COMMENT '月份 格式 2015-02',
PRIMARY KEY (`id`),
KEY `distribution_id` (`distribution_id`),
KEY `stat_time` (`stat_time`),
KEY `stat_month` (`stat_month`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='驿站日报表';

CREATE TABLE `%DB_PREFIX%distribution_money_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`log_info` text NOT NULL COMMENT '资金变更记录',
`distribution_id` int(11) NOT NULL COMMENT '驿站ID',
`create_time` int(11) NOT NULL COMMENT '变更时间',
`money` decimal(20,4) NOT NULL COMMENT '资金变更数额',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:服务费增加（只有在用户收货结算后） 2.提现增加',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='驿站财务明细表';


CREATE TABLE `%DB_PREFIX%distribution_money_submit` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`money` decimal(20,4) NOT NULL COMMENT '提现金额',
`distribution_id` int(11) NOT NULL COMMENT '驿站ID',
`create_time` int(11) NOT NULL COMMENT '提现申请时间',
`status` tinyint(1) NOT NULL COMMENT '状态 0:待审核 1:已确认提现 2:拒绝',
`reason` text NOT NULL COMMENT '拒绝的理由',
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='驿站提现表';


ALTER TABLE `%DB_PREFIX%distribution_money_submit`
ADD COLUMN `bank_name`  varchar(255) NOT NULL COMMENT '开户行' ,
ADD COLUMN `bank_card`  varchar(255) NOT NULL COMMENT '银行卡号' ,
ADD COLUMN `bank_user`  varchar(255) NOT NULL COMMENT '持卡人姓名' ;



CREATE TABLE `%DB_PREFIX%distribution_coupon` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
`sn` varchar(255) NOT NULL COMMENT '配送码',
`create_time` int(11) NOT NULL COMMENT '配送码生成时间',
`is_valid` tinyint(1) NOT NULL COMMENT '配送码是否有效',
`user_id` int(11) NOT NULL COMMENT '用户ID',
`order_id` int(11) NOT NULL COMMENT '订单ID',
`distribution_id` int(11) NOT NULL COMMENT '配送驿站ID',
`is_read` tinyint(1) NOT NULL COMMENT '是否已读',
`is_delete` tinyint(1) NOT NULL COMMENT '是否删除',
`confirm_time` int(11) NOT NULL COMMENT '验证时间',
`is_balance` tinyint(1) NOT NULL COMMENT '是否结单',
`refund_status` tinyint(1) NOT NULL COMMENT '退款状态：0-无；1-退款中；2-已退款；3-拒绝退款',
`memo` text NOT NULL COMMENT '配送备注',
PRIMARY KEY (`id`),
UNIQUE KEY `sn` (`sn`),
KEY `user_id` (`user_id`),
KEY `order_id` (`order_id`),
KEY `is_delete` (`is_delete`),
KEY `distribution_id` (`distribution_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='驿站配送码表';


ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `distribution_fee`  decimal(20,4) NOT NULL COMMENT '驿站服务费';

INSERT INTO `%DB_PREFIX%conf` VALUES ('', 'SHARE_ICON', '', '4', '2', '', '1', '1', '71');
INSERT INTO `%DB_PREFIX%conf` VALUES ('', 'SHARE_TITLE', '', '4', '0', '', '1', '1', '72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('', 'SHARE_CONTENT', '', '4', '0', '', '1', '1', '73');

ALTER TABLE `%DB_PREFIX%distribution`
ADD COLUMN `points`  polygon NOT NULL COMMENT '经纬度空间数据类型 格式GeomFromText(\'Polygon((lat1 lng1,lat2 lng2,...,lag1 lng1))\')',
CHANGE COLUMN `deleted` `is_deleted`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除（0：否，1：是）';


ALTER TABLE `%DB_PREFIX%distribution_shipping`
CHANGE COLUMN `deleted` `is_deleted`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除：0=否，1=是';


ALTER TABLE `%DB_PREFIX%distribution`
CHANGE COLUMN `is_deleted` `is_delete`  tinyint(1) NOT NULL COMMENT '是否删除（0:否，1:是）';

ALTER TABLE `%DB_PREFIX%distribution_shipping`
CHANGE COLUMN `is_deleted` `is_delete`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除：0:否，1:是';


ALTER TABLE `%DB_PREFIX%distribution`
MODIFY COLUMN `xpoint`  double NOT NULL COMMENT '中心点经度',
MODIFY COLUMN `ypoint`  double NOT NULL COMMENT '中心点纬度',
MODIFY COLUMN `points`  polygon NOT NULL COMMENT '经纬度空间数据类型 格式GeomFromText(\'Polygon((lat1 lng1,lat2 lng2,...,lag1 lng1))\')，在5.6以上的数据库使用',
ADD COLUMN `prov_id`  int NOT NULL COMMENT '省份ID',
ADD COLUMN `xpoints`  text NOT NULL COMMENT '多边形经度集',
ADD COLUMN `ypoints`  text NOT NULL COMMENT '多边形纬度集',
ADD COLUMN `status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态 0:未审核  1:审核通过  2:已拒绝',
ADD COLUMN `adm_type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '后台审核类型:    0:后台审核  1:代理商审核',
ADD COLUMN `adm_memo`  varchar(255)  COMMENT '驿站审核备注',
ADD COLUMN `county_id`  int NOT NULL DEFAULT 0 COMMENT '县区id  代理商所属驿站有值' AFTER `city_id`,
ADD COLUMN `agency_id`  int NOT NULL DEFAULT 0 COMMENT '驿站所属代理商id  0:表示不属于任何代理商',
ADD COLUMN `disabled`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态  0:正常  1:禁用';

ALTER TABLE `%DB_PREFIX%distribution_shipping`
ADD COLUMN `disabled`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0:正常  1:禁用';

ALTER TABLE `%DB_PREFIX%supplier_submit`
ADD COLUMN `memo`  text NOT NULL COMMENT '拒绝申请说明';
ALTER TABLE `%DB_PREFIX%supplier_submit`
MODIFY COLUMN `is_publish`  tinyint(1) NOT NULL COMMENT '0:未审核 1:已审核 2：拒绝申请';

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `is_id_validate`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实名认证：0未发起审核，1审核通过，2审核中，3审核失败';

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `bind_count` int(11) DEFAULT 0  NOT NULL  COMMENT '会员合并次数';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `order_process_status`  int(11) NOT NULL COMMENT '订单进程状态，待付款1,待发货2,待确认3,待评价4,已取消5,已完成6,退款中7,已删除8';

INSERT INTO `%DB_PREFIX%conf` (id,name,group_id,input_type,is_effect,is_conf,sort)
VALUES ('',"APP_ABOUT_US",3,5,1,1,unix_timestamp(now()));


ALTER TABLE `%DB_PREFIX%agency`
ADD COLUMN `login_count`  int(11) NOT NULL DEFAULT 0 COMMENT '登录次数';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `delivery_time`  int(11) NOT NULL DEFAULT 0 COMMENT '发货时间';


ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `delivery_memo` varchar(255) DEFAULT NULL COMMENT '发货备注';

ALTER TABLE `%DB_PREFIX%deal_submit`
MODIFY COLUMN `biz_apply_status`  tinyint(1) NOT NULL COMMENT '商户申请状态 1.新品上架申请 2:修改 3:下架 4:重新上架';

ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `deal_submit_memo` varchar(255) DEFAULT NULL COMMENT '审核备注';

ALTER TABLE `%DB_PREFIX%supplier_submit`
MODIFY COLUMN `is_publish`  tinyint(1) NOT NULL COMMENT '0:未审核 1:已审核 2：拒绝申请';


ALTER TABLE `%DB_PREFIX%delivery_region`
ADD COLUMN `code`  int(11) NOT NULL COMMENT '行政区划代码';

ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `city_code`  int(11) NOT NULL COMMENT '城市行政区划代码';

ALTER TABLE `%DB_PREFIX%supplier_submit`
ADD COLUMN `city_code`  int(11) NOT NULL COMMENT '城市行政区划代码';

ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `h_license`  varchar(255) NOT NULL COMMENT '营业执照',
ADD COLUMN `h_other_license`  varchar(255) NOT NULL COMMENT '其他资质';

ALTER TABLE `%DB_PREFIX%supplier_location_biz_submit`
ADD COLUMN `memo`  text NOT NULL COMMENT '拒绝理由说明';

ALTER TABLE `%DB_PREFIX%agency`
ADD COLUMN `city_code`  int(11) NOT NULL DEFAULT 0 COMMENT '城市code';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `agency_id`  int(11) NOT NULL DEFAULT 0 COMMENT '代理商id' ,
ADD COLUMN `city_code`  int(11) NOT NULL DEFAULT 0 COMMENT '城市代理区域code' ;

ALTER TABLE `%DB_PREFIX%agency_statements_log`
MODIFY COLUMN `type`  tinyint(1) NOT NULL COMMENT '1.团购商城 2.外卖 3.优惠买单 4.提现 5.升级网宝';

ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `allow_user_discount`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否参与会员等级折扣优惠，0为不参与，1为参与';


ALTER TABLE `%DB_PREFIX%deal`
MODIFY COLUMN `cate_id`  varchar(255) NOT NULL COMMENT '生活服务分类ID',
MODIFY COLUMN `shop_cate_id`  varchar(255) NOT NULL COMMENT '商城商品的分类ID';

ALTER TABLE `%DB_PREFIX%deal_attr`
DROP COLUMN `add_balance_price`;
ALTER TABLE `%DB_PREFIX%deal_attr`
DROP COLUMN `price`;



ALTER TABLE `%DB_PREFIX%attr_stock`
ADD COLUMN `add_balance_price`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '递增成本价',
ADD COLUMN `price`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '递增销售价';


ALTER TABLE `%DB_PREFIX%deal`
ADD COLUMN `publish_verify_balance` decimal(20,4) NOT NULL COMMENT '结算费用率';

ALTER TABLE `%DB_PREFIX%agency`
ADD COLUMN `money`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '代理商总余额' ;

ALTER TABLE `%DB_PREFIX%deal_submit`
MODIFY COLUMN `cate_id`  varchar(255) NOT NULL COMMENT '生活服务分类ID',
MODIFY COLUMN `shop_cate_id`  varchar(255) NOT NULL COMMENT '商城商品的分类ID';
ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `set_meal` text NOT NULL COMMENT '移动端套餐模板',
ADD COLUMN `pc_setmeal` text NOT NULL COMMENT 'PC端套餐模板';
ALTER TABLE `%DB_PREFIX%deal_submit`
ADD COLUMN `publish_verify_balance` decimal(20,4) NOT NULL COMMENT '结算费用率';

ALTER TABLE `%DB_PREFIX%agency`
ADD UNIQUE INDEX `account_name` (`account_name`) ;

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `deal_total_price`  decimal(20,2) NOT NULL DEFAULT 0 COMMENT '商品原始总价';

INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'SCORE_PURCHASE_SWITCH', '0', '0', '1', '0,1', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'SCORE_PURCHASE_EXCHANGE_MONEY', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'SCORE_PURCHASE_MAX_MONEY', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'SCORE_PURCHASE_MAX_PROPORTION_MONEY', '0', '0', '0', '', '1', '0', '0');
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `exchange_money` decimal(20,4) NOT NULL COMMENT '积分抵扣金额';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `score_purchase` text NOT NULL COMMENT '积分抵扣信息';
ALTER TABLE `%DB_PREFIX%store_pay_order`
ADD COLUMN `exchange_money` decimal(20,4) NOT NULL COMMENT '积分抵扣金额';
ALTER TABLE `%DB_PREFIX%store_pay_order`
ADD COLUMN `score_purchase` text NOT NULL COMMENT '积分抵扣信息';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `cod_money` decimal(20,4) NOT NULL COMMENT '货到付款的金额';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `cod_mode` text NOT NULL COMMENT '货到付款的方式';
ALTER TABLE `%DB_PREFIX%payment_notice`
ADD COLUMN `payment_config` text NOT NULL COMMENT '支付方式会员选择的配置';


ALTER TABLE `%DB_PREFIX%dc_rs_item`
ADD COLUMN `comment`  varchar(50) NULL COMMENT '项目备注';


ALTER TABLE `%DB_PREFIX%dc_cart`
ADD COLUMN `table_menu_id`  int NOT NULL DEFAULT 0 COMMENT '门店可预订的项目id' AFTER `is_effect`;
ALTER TABLE `%DB_PREFIX%dc_cart`
DROP COLUMN `table_menu_id`;


ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `dada_delivery`  text NOT NULL COMMENT '达达配送信息';

ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `address` varchar(255) NOT NULL COMMENT '地址';

update `%DB_PREFIX%supplier` as s , `%DB_PREFIX%supplier_location` as sl set s.address=sl.address where s.id=sl.supplier_id and sl.is_main=1;

ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `delivery_part`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '配送方式：1为商家配送，2为达达配送，3为蜂鸟配送',
ADD COLUMN `thirdpart_delivery_fee`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '第三方配送费';



CREATE TABLE `%DB_PREFIX%dc_third_delivery` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`class_name` varchar(255) NOT NULL COMMENT '支付接口类名',
`is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
`name` varchar(255) NOT NULL,
`config` text NOT NULL COMMENT '相关的配置信息',
`logo` varchar(255) NOT NULL COMMENT '显示的图标',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='外卖配送接口表';

INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'DELIVERY_MIN_MONEY', '0', '8', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` (`id`, `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('', 'DELIVERY_ALARM_MONEY', '0', '8', '0', '', '1', '0', '0');

ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `is_open_dada_delivery`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启达达配送';

ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `delivery_type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '外卖配送方式：0为自动接单，并由商家配送，1为手动接单，手动指派，2为自动接单，并由达达配送',
ADD COLUMN `dada_account`  varchar(255) NOT NULL COMMENT '达达商家帐号',
ADD COLUMN `dada_password`  varchar(255) NOT NULL COMMENT '达达商家密码';

ALTER TABLE `%DB_PREFIX%supplier_location`
ADD COLUMN `dada_shop_id`  varchar(255) NOT NULL DEFAULT '' COMMENT '达达门店编号';

ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `delivery_money`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '配送费余额';


INSERT INTO `%DB_PREFIX%conf` ( `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('SCORE_RECHARGE_SWITCH', '0', '1', '0', '0,1', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` ( `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('SCORE_RECHARGE_USABLE_SCORE', '0', '1', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` ( `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('SCORE_RECHARGE_FROZEN_SCORE', '0', '1', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` ( `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('SCORE_RECHARGE_SCORE_NUMBER_SET', '50,100,300,500,1000', '1', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` ( `name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('SCORE_RECHARGE_MONEY_NUMBER_SET', '50,100,300,500,1000', '4', '0', '', '1', '1', '0');

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `frozen_score`  int(11) NOT NULL COMMENT '冻结积分';
ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `frozen_score`  int(11) NOT NULL COMMENT '冻结积分';
ALTER TABLE `%DB_PREFIX%deal_order`
MODIFY COLUMN `type`  tinyint(1) NOT NULL COMMENT '订单类型(0:商品订单 1:用户充值单,2:积分兑换订单，3:平台自营物流配送订单，4:平台自营驿站配送订单,5:商家团购订单,6:商家商品订单,7:积分充值订单)';
ALTER TABLE `%DB_PREFIX%user_log`
ADD COLUMN `frozen_score`  int(11) NOT NULL COMMENT '冻结积分';
ALTER TABLE `%DB_PREFIX%fx_qualification`
ADD COLUMN `fx_award`  decimal(20,4) NOT NULL COMMENT '代理商佣金比例(%)';
ALTER TABLE `%DB_PREFIX%fx_buy_order`
ADD COLUMN `fx_charge_price`  decimal(20,4) NOT NULL COMMENT '代理商佣金';


ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `qrcode_type`  int(11) NOT NULL DEFAULT 0 COMMENT '会员邀请二维码目标地址,我的小店0,平台首页1,wap商城首页2,wap团购首页3';

CREATE TABLE `%DB_PREFIX%supplier_delivery_charge_order` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(255) NOT NULL,
`supplier_id` int(11) NOT NULL,
`create_time` int(11) NOT NULL,
`pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 2:全部付款',
`total_price` decimal(20,4) NOT NULL COMMENT '消费金额',
`pay_amount` decimal(20,4) NOT NULL COMMENT '实付金额 当pay_amount+discount_price = total_price 支付成功',
`order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
`is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
`payment_id` int(11) NOT NULL COMMENT '支付方式',
`bank_id` varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
`payment_fee` decimal(20,4) NOT NULL COMMENT '手续费',
PRIMARY KEY (`id`),
UNIQUE KEY `unique_sn` (`order_sn`),
KEY `order_sn` (`order_sn`),
KEY `supplier_id` (`supplier_id`),
KEY `pay_status` (`pay_status`),
KEY `order_status` (`order_status`),
KEY `is_delete` (`is_delete`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商户配送费充值订单表';


ALTER TABLE `%DB_PREFIX%payment_notice`
ADD COLUMN `supplier_id`  int(11) NOT NULL COMMENT '商家ID';

ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `delivery_cancel_reason`  varchar(255) NOT NULL COMMENT '配送异常原因';

ALTER TABLE `%DB_PREFIX%dc_delivery`
MODIFY COLUMN `scale`  decimal(20,2) NULL DEFAULT NULL COMMENT '用于限制标准的公里数(lbs直线距离)';


ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `delivery_cancel_reason`  varchar(255) NOT NULL COMMENT '配送异常原因';


ALTER TABLE `%DB_PREFIX%dc_supplier_order`
ADD COLUMN `dada_delivery`  text NOT NULL COMMENT '达达配送信息';

ALTER TABLE `%DB_PREFIX%dc_supplier_order`
ADD COLUMN `delivery_part`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '配送方式：1为商家配送，2为达达配送，3为蜂鸟配送',
ADD COLUMN `thirdpart_delivery_fee`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '第三方配送费';

ALTER TABLE `%DB_PREFIX%dc_supplier_order`
ADD COLUMN `delivery_cancel_reason`  varchar(255) NOT NULL COMMENT '配送异常原因';


ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `is_delivery_cancel`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否配送异常';

ALTER TABLE `%DB_PREFIX%dc_supplier_order`
ADD COLUMN `is_delivery_cancel`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否配送异常';


ALTER TABLE `%DB_PREFIX%dc_supplier_order`
ADD COLUMN `has_send_dada`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已向达达推单过';


ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `has_send_dada`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已向达达推单过';
ALTER TABLE `%DB_PREFIX%supplier_account`  ADD COLUMN `user_id` int(11) DEFAULT 0 COMMENT '会员关联ID';

ALTER TABLE `%DB_PREFIX%supplier_money_log`
MODIFY COLUMN `type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:销售额增加 1:资金冻结 2.待结算增加 3.已结算增加 4.退款增加 5.提现增加 7.买单订单明细 8.外卖配送费扣除';

ALTER TABLE `%DB_PREFIX%statements_log`
MODIFY COLUMN `type`  tinyint(1) NOT NULL COMMENT '0.收入 1.订单支付收入 2.会员充值收入 3.支出 4.会员提现支出 5.商户提现支出 6.退款金额 7.退款中的成本 8.销售额,所有支付成功的订单面额(不含在线充值) 9.销售额中成本(即将结算给商家的部份) 10.商家结算额 11.消费额 12.消费额中的成本 13.外卖配送费扣除';

ALTER TABLE `%DB_PREFIX%payment_notice`
MODIFY COLUMN `order_type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:全部订单 ,1:外卖预定订单,2:商户配送费预充值订单,3:普通订单,4:会员买单 5.分销资格购买订单';

ALTER TABLE `%DB_PREFIX%dc_order`
ADD COLUMN `delivery_operation`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '配送操作：0：不操作，1：向第三方推单，2：向第三方取消订单';

CREATE TABLE `%DB_PREFIX%schedule_list` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`type` varchar(20) NOT NULL COMMENT '计划任务类型，不同类型有不同的执行接口',
`name` varchar(255) NOT NULL COMMENT '计划任务可识别的名称',
`dest` varchar(255) DEFAULT NULL COMMENT '计划任务标（手机，邮箱，推送用的openid，内部执行程序为NULL）',
`data` longtext NOT NULL COMMENT '计划任务运行参数',
`schedule_date` date NOT NULL COMMENT '计划任务的日期',
`schedule_time` int(11) NOT NULL COMMENT '计划任务启动执行时间',
`lock_time` int(11) NOT NULL COMMENT 'exec_lock加锁时间',
`exec_begin_time` int(11) NOT NULL COMMENT '执行开始时间',
`exec_end_time` int(11) NOT NULL COMMENT '执行结束时间',
`exec_status` tinyint(1) NOT NULL COMMENT '执行结果 0未执行 1执行中 2执行结束',
`exec_info` longtext NOT NULL COMMENT '执行结果的相关信息',
`exec_lock` tinyint(1) NOT NULL COMMENT ' 任务是否锁住，防止多次请求 0否 1是',
PRIMARY KEY (`id`),
KEY `type` (`type`),
KEY `dest` (`dest`),
KEY `schedule_date` (`schedule_date`),
KEY `idx` (`type`,`schedule_date`),
KEY `exec_status` (`exec_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='计划任务';

INSERT INTO `%DB_PREFIX%msg_template` VALUES ('','TPL_SMS_COD_PAYMENT','{$payment_notice.user_name}你好,你所下的货到付款订单{$payment_notice.order_sn},于{$payment_notice.pay_time_format}下单成功','0','0','0','0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('','TPL_MAIL_COD_PAYMENT','{$payment_notice.user_name}你好,你所下的货到付款订单{$payment_notice.order_sn},于{$payment_notice.pay_time_format}下单成功','1','0','0','0');

ALTER TABLE `%DB_PREFIX%ecv_type`
ADD COLUMN `valid_type`  tinyint(1) ZEROFILL NOT NULL COMMENT '有效期设置类型（0：固定日期有效；1：领券后固定有效天数）',
ADD COLUMN `expire_day`  int(11) NOT NULL COMMENT '有效天数' ,
ADD COLUMN `start_use_price`  decimal(20,4) NOT NULL COMMENT '消费XX元可使用';

ALTER TABLE `%DB_PREFIX%youhui`
MODIFY COLUMN `expire_day` int(11) NOT NULL COMMENT '领券后固定有效天数(0表示以优惠券结束时间为依据)，如优惠券结束时间也为0则不过期',
MODIFY COLUMN `youhui_type` tinyint(1) NOT NULL COMMENT '优惠类型（1为实体券，2为电子券）',
MODIFY COLUMN `youhui_value` int(11) NOT NULL COMMENT '优惠金额',
ADD COLUMN `valid_type` tinyint(1) NOT NULL COMMENT '有效类型（1为固定天数，2为固定日期）',
ADD COLUMN `use_begin_time` int(11) NOT NULL COMMENT '领取后可以使用的开始时间',
ADD COLUMN `use_end_time` int(11) NOT NULL COMMENT '领取后过期时间',
ADD COLUMN `user_everyday_limit` int(11) NOT NULL COMMENT '每人每天限领',
ADD COLUMN `start_use_price` int(11) NOT NULL COMMENT '可以使用的起始金额';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `youhui_log_id`  int(11) NOT NULL DEFAULT 0 COMMENT '优惠劵ID',
ADD COLUMN `youhui_money`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '优惠劵优惠的金额';

ALTER TABLE `%DB_PREFIX%deal_order_item`
ADD COLUMN `discount_unit_price`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '商品折扣后单价';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `is_all_balance`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否整笔订单一起结算';


update %DB_PREFIX%deal_order_item set discount_unit_price=unit_price where deal_total_price > 0;

update %DB_PREFIX%deal_order_item set total_price=deal_total_price where deal_total_price > 0;

update %DB_PREFIX%deal_order_item set unit_price=total_price/number where deal_total_price > 0;


ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `open_xn_talk`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启小能帐户',
ADD COLUMN `xn_talk_id`  varchar(255) NULL COMMENT '小能商户企业ID',
ADD COLUMN `xn_talk_login_id`  varchar(50) NULL COMMENT '小能登录名',
ADD COLUMN `xn_talk_pwd`  varchar(255) NULL COMMENT '小能登录密码',
ADD COLUMN `xn_talk_custom_id`  varchar(255) NULL COMMENT '小能接待客服ID';


CREATE TABLE `%DB_PREFIX%help_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(50) NOT NULL COMMENT '分类名称',
`is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效性标识',
`is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识',
`sort` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帮助文章分类';

CREATE TABLE `%DB_PREFIX%help_article`(
`id` INT NOT NULL AUTO_INCREMENT,
`title` VARCHAR(50) NOT NULL COMMENT '文章标题',
`cate_id` INT NOT NULL COMMENT '文章分类id',
`content` TEXT COMMENT '文章内容',
`phone_content` TEXT COMMENT '文章内容(手机端)',
`sort` INT COMMENT '文章排序',
`is_effect` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有效性标识',
`is_delete` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '删除标识',
PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='帮助文章列表';



CREATE TABLE `%DB_PREFIX%invoice_conf`(
`id` INT NOT NULL AUTO_INCREMENT,
`supplier_id` INT NOT NULL COMMENT '商户Id， 为0表示平台',
`invoice_type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '开具发票类型 0:不开发票 1:普通发票',
`invoice_content` TEXT COMMENT '发票内容',
PRIMARY KEY (`id`),
UNIQUE INDEX (`supplier_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='开发票配置表';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `invoice_info`  text NULL COMMENT '订单开票信息 type:0 不开票, 1 普通发票；title(发票抬头):0 个人, 1 企业；persons(开票人或企业)；taxnu(企业纳税人识别号)';

INSERT INTO `%DB_PREFIX%conf` (`name`, `value`, `group_id`, `input_type`, `value_scope`, `is_effect`, `is_conf`, `sort`) VALUES ('INVOICE_NOTICE', '', '3', '3', '', '1', '1', '102');
ALTER TABLE `%DB_PREFIX%fx_withdraw`
ADD COLUMN `fee`  decimal(20,4) NOT NULL DEFAULT 0 COMMENT '分销提现手续费';

INSERT INTO `%DB_PREFIX%conf` VALUES ('','FX_AUTO_WITHDRAW','0',40,1,'0,1',1,0,2);

INSERT INTO `%DB_PREFIX%conf` VALUES ('','XN_SETTING_ID','',3,0,'',1,1,25);
UPDATE `%DB_PREFIX%conf` SET `group_id`='40', `input_type`='0', `is_conf`='0' WHERE (`name`='INVOICE_NOTICE');
INSERT INTO `%DB_PREFIX%conf` VALUES ('','REF_SALARY','',40,0,'',1,0,2);
ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `user_id`  int(11) NOT NULL COMMENT '绑定的会员id',
ADD COLUMN `ref_user_id`  int(11) NOT NULL COMMENT '推荐人id',
ADD COLUMN `is_store_payment_fx`  tinyint(1) NOT NULL COMMENT '是否开启优惠买单三级分销',
ADD COLUMN `store_payment_fx_salary`  text NOT NULL COMMENT '优惠买单三级分销设置';
ALTER TABLE `%DB_PREFIX%fx_statements`
ADD COLUMN `ref_salary`  decimal(20,4) NOT NULL COMMENT '推荐商家出售商品和团购产生的佣金',
ADD COLUMN `store_payment_salary`  decimal(20,4) NOT NULL COMMENT '推荐商家到店买单产生的佣金';

ALTER TABLE `%DB_PREFIX%deal_order`
ADD COLUMN `is_participate_ref_salary` tinyint(1) NOT NULL COMMENT '是否参与推荐商家返佣',
ADD COLUMN `ref_total` decimal(20,4) NOT NULL COMMENT '参与推荐商家返佣的商品金额',
ADD COLUMN `ref_salary_total` decimal(20,4) NOT NULL COMMENT '总返佣金额',
ADD COLUMN `ref_salary_all` text NOT NULL COMMENT '返佣详情';
ALTER TABLE `%DB_PREFIX%store_pay_order`
ADD COLUMN `is_participate_ref_salary` tinyint(1) NOT NULL COMMENT '是否参与推荐商家买单返佣',
ADD COLUMN `ref_salary_total` decimal(20,4) NOT NULL COMMENT '总返佣金额',
ADD COLUMN `ref_salary_all` text NOT NULL COMMENT '返佣详情';

INSERT INTO `%DB_PREFIX%conf` VALUES ('','FX_WITHDRAW_RATE','0',40,0,'',1,0,2);
INSERT INTO `%DB_PREFIX%conf` VALUES ('','FX_WITHDRAW_CYCLE','0',40,0,'',1,0,2);
INSERT INTO `%DB_PREFIX%conf` VALUES ('','SUPPLIER_WITHDRAW_CYCLE','0',40,0,'',1,0,2);
ALTER TABLE `%DB_PREFIX%supplier`
ADD COLUMN `supplier_withdraw_cycle`  int(11) NOT NULL DEFAULT '-1' COMMENT '订单金额结算给商户 n 天后才可提现';
ALTER TABLE `%DB_PREFIX%fx_statements_log`
ADD COLUMN `stat_time` date NOT NULL COMMENT '报表日期';
UPDATE %DB_PREFIX%fx_statements_log set stat_time=FROM_UNIXTIME(create_time+28800,'%Y-%m-%d');
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `fx_total_vip_money`  decimal(20,4) NOT NULL COMMENT '推荐的会员购买分销资格的累积营业额';