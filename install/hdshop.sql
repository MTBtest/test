SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `hd_admin_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_action_log`;
CREATE TABLE `hd_admin_action_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` char(15) NOT NULL DEFAULT '0' COMMENT '执行行为者ip',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '操作URL',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`) USING BTREE,
  KEY `user_id_ix` (`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='行为日志表';

-- ----------------------------
--  Table structure for `hd_admin_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_auth_group`;
CREATE TABLE `hd_admin_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户组定义表';

-- ----------------------------
--  Table structure for `hd_admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_user`;
CREATE TABLE `hd_admin_user` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `valid` char(10) NOT NULL,
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `last_ip` char(15) NOT NULL DEFAULT '0',
  `login_num` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
--  Table structure for `hd_adv`
-- ----------------------------
DROP TABLE IF EXISTS `hd_adv`;
CREATE TABLE `hd_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `loading` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hist` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='广告管理';

-- ----------------------------
--  Table structure for `hd_adv_position`
-- ----------------------------
DROP TABLE IF EXISTS `hd_adv_position`;
CREATE TABLE `hd_adv_position` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='广告位管理';

-- ----------------------------
--  Table structure for `hd_announcement`
-- ----------------------------
DROP TABLE IF EXISTS `hd_announcement`;
CREATE TABLE `hd_announcement` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='公告发布';

-- ----------------------------
--  Table structure for `hd_article`
-- ----------------------------
DROP TABLE IF EXISTS `hd_article`;
CREATE TABLE `hd_article` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
--  Table structure for `hd_article_category`
-- ----------------------------
DROP TABLE IF EXISTS `hd_article_category`;
CREATE TABLE `hd_article_category` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- ----------------------------
--  Table structure for `hd_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `hd_attribute`;
CREATE TABLE `hd_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `model_id` mediumint(8) unsigned DEFAULT '0' COMMENT '模型ID',
  `name` varchar(50) DEFAULT '' COMMENT '名称',
  `value` text COMMENT '属性值(逗号分隔)',
  `spec_ids` text,
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持搜索0不支持1支持',
  `type` tinyint(1) unsigned DEFAULT '1' COMMENT '输入控件的类型,1:单选,2:复选,3:下拉',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='属性表';

-- ----------------------------
--  Table structure for `hd_brand`
-- ----------------------------
DROP TABLE IF EXISTS `hd_brand`;
CREATE TABLE `hd_brand` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `logo` varchar(80) NOT NULL DEFAULT '',
  `descript` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `pushstatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isrecommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='品牌列表';

-- ----------------------------
--  Table structure for `hd_cart`
-- ----------------------------
DROP TABLE IF EXISTS `hd_cart`;
CREATE TABLE `hd_cart` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `products_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `clientip` char(15) NOT NULL DEFAULT '',
  `key` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='购物车';

-- ----------------------------
--  Table structure for `hd_comment`
-- ----------------------------
DROP TABLE IF EXISTS `hd_comment`;
CREATE TABLE `hd_comment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `reply` text NOT NULL,
  `reply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` char(15) NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品评论表';

-- ----------------------------
--  Table structure for `hd_consult`
-- ----------------------------
DROP TABLE IF EXISTS `hd_consult`;
CREATE TABLE `hd_consult` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned DEFAULT '0',
  `question` text NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `reply` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `reply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` char(15) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品咨询表';

-- ----------------------------
--  Table structure for `hd_coupons`
-- ----------------------------
DROP TABLE IF EXISTS `hd_coupons`;
CREATE TABLE `hd_coupons` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `limit` smallint(6) unsigned NOT NULL DEFAULT '0',
  `descript` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='代金券管理';

-- ----------------------------
--  Table structure for `hd_coupons_list`
-- ----------------------------
DROP TABLE IF EXISTS `hd_coupons_list`;
CREATE TABLE `hd_coupons_list` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sn` varchar(10) NOT NULL DEFAULT '',
  `password` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `to_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned DEFAULT '0',
  `user_name` varchar(60) DEFAULT '',
  `use_time` int(10) unsigned DEFAULT '0',
  `use_order` varchar(20) DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='代金券明细列表';

-- ----------------------------
--  Table structure for `hd_delivery`
-- ----------------------------
DROP TABLE IF EXISTS `hd_delivery`;
CREATE TABLE `hd_delivery` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `enname` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `descript` text NOT NULL,
  `type` varchar(200) NOT NULL DEFAULT '',
  `weight` varchar(20) NOT NULL DEFAULT '',
  `insure` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `weightprice` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `pays` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='配送方式表';

-- ----------------------------
--  Table structure for `hd_delivery_region`
-- ----------------------------
DROP TABLE IF EXISTS `hd_delivery_region`;
CREATE TABLE `hd_delivery_region` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` int(8) unsigned NOT NULL DEFAULT '0',
  `weightprice` varchar(20) NOT NULL DEFAULT '',
  `region_id` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='配送地区设置';

-- ----------------------------
--  Table structure for `hd_focus`
-- ----------------------------
DROP TABLE IF EXISTS `hd_focus`;
CREATE TABLE `hd_focus` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pic` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='首页幻灯片';

-- ----------------------------
--  Table structure for `hd_friend_link`
-- ----------------------------
DROP TABLE IF EXISTS `hd_friend_link`;
CREATE TABLE `hd_friend_link` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `descript` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='友情链接';

-- ----------------------------
--  Table structure for `hd_gifts`
-- ----------------------------
DROP TABLE IF EXISTS `hd_gifts`;
CREATE TABLE `hd_gifts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT '',
  `sn` varchar(60) NOT NULL DEFAULT '',
  `barcode` varchar(30) NOT NULL DEFAULT '',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `brief` varchar(255) NOT NULL DEFAULT '',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cost_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `descript` text NOT NULL,
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `warn_number` tinyint(3) NOT NULL DEFAULT '2',
  `weight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `hist` int(11) unsigned NOT NULL DEFAULT '0',
  `list_img` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`,`barcode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='赠品管理';

-- ----------------------------
--  Table structure for `hd_gifts_category`
-- ----------------------------
DROP TABLE IF EXISTS `hd_gifts_category`;
CREATE TABLE `hd_gifts_category` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='赠品分类表';

-- ----------------------------
--  Table structure for `hd_goods`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods`;
CREATE TABLE `hd_goods` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT '',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cat_ids` varchar(255) NOT NULL DEFAULT '',
  `keyword` varchar(255) CHARACTER SET utf8 COLLATE utf8_romanian_ci NOT NULL,
  `brief` varchar(255) NOT NULL,
  `give_integral` int(11) NOT NULL DEFAULT '-1',
  `descript` text NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `warn_number` tinyint(3) NOT NULL DEFAULT '2',
  `weight` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `favorite` int(11) NOT NULL DEFAULT '0',
  `spec_array` text NOT NULL,
  `list_img` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `status_ext` varchar(100) NOT NULL DEFAULT '',
  `model` int(8) NOT NULL,
  `sales_number` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  `sort` int(8) NOT NULL DEFAULT '100',
  `min_shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `prom_id` mediumint(8) NOT NULL DEFAULT '0',
  `prom_type` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品列表';

-- ----------------------------
--  Table structure for `hd_goods_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_attribute`;
CREATE TABLE `hd_goods_attribute` (
  `goods_id` int(10) NOT NULL DEFAULT '0',
  `attribute_id` int(10) NOT NULL,
  `attribute_value` varchar(255) NOT NULL,
  `type` smallint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(8) NOT NULL DEFAULT '100',
  KEY `goods_id` (`goods_id`,`attribute_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品属性表';

-- ----------------------------
--  Table structure for `hd_goods_category`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_category`;
CREATE TABLE `hd_goods_category` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) NOT NULL DEFAULT '0',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `show_in_nav` tinyint(1) NOT NULL DEFAULT '0',
  `grade` text NOT NULL COMMENT '价格分级',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(8) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
--  Table structure for `hd_goods_message`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_message`;
CREATE TABLE `hd_goods_message` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `notify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '通知状态（-1：已忽略；0：未通知；1：已通知；）',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`) USING BTREE,
  KEY `goods_id, product_id` (`goods_id`,`product_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品通知表';

-- ----------------------------
--  Table structure for `hd_goods_products`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_products`;
CREATE TABLE `hd_goods_products` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) NOT NULL DEFAULT '0',
  `products_sn` varchar(60) NOT NULL DEFAULT '',
  `products_barcode` varchar(60) NOT NULL DEFAULT '',
  `spec_array` text NOT NULL,
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(10,2) NOT NULL,
  `min_shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_shop_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_number` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='产品表';

-- ----------------------------
--  Table structure for `hd_goods_promotion`
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_promotion`;
CREATE TABLE `hd_goods_promotion` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `award_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `award_value` varchar(255) NOT NULL DEFAULT '',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `group` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品促销表';

-- ----------------------------
--  Table structure for `hd_help`
-- ----------------------------
DROP TABLE IF EXISTS `hd_help`;
CREATE TABLE `hd_help` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `fpid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `displayplay` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='帮助中心';

-- ----------------------------
--  Table structure for `hd_model`
-- ----------------------------
DROP TABLE IF EXISTS `hd_model`;
CREATE TABLE `hd_model` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '模型名称',
  `cat_ids` text COMMENT '分类ID逗号分隔',
  `sort` tinyint(1) DEFAULT '100',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='模型表';

-- ----------------------------
--  Table structure for `hd_msg_template`
-- ----------------------------
DROP TABLE IF EXISTS `hd_msg_template`;
CREATE TABLE `hd_msg_template` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='消息模板表';

-- ----------------------------
--  Table structure for `hd_navigation`
-- ----------------------------
DROP TABLE IF EXISTS `hd_navigation`;
CREATE TABLE `hd_navigation` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '导航名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（默认为1）',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='站点导航设置';

-- ----------------------------
--  Table structure for `hd_node`
-- ----------------------------
DROP TABLE IF EXISTS `hd_node`;
CREATE TABLE `hd_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `name` char(40) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `g` char(20) NOT NULL DEFAULT '' COMMENT '所属控制器',
  `m` char(20) NOT NULL DEFAULT '' COMMENT '所属模块',
  `a` char(20) NOT NULL DEFAULT '' COMMENT '所属操作',
  `data` char(100) NOT NULL DEFAULT '' COMMENT '附加操作',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `url` char(255) NOT NULL DEFAULT '',
  `pluginid` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `listorder` (`sort`) USING BTREE,
  KEY `parentid` (`parentid`) USING BTREE,
  KEY `module` (`m`,`g`,`a`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='菜单&权限节点表';

-- ----------------------------
--  Table structure for `hd_notify`
-- ----------------------------
DROP TABLE IF EXISTS `hd_notify`;
CREATE TABLE `hd_notify` (
  `code` varchar(50) NOT NULL DEFAULT '',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态',
  `config` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知系统配置信息';

-- ----------------------------
--  Table structure for `hd_notify_template`
-- ----------------------------
DROP TABLE IF EXISTS `hd_notify_template`;
CREATE TABLE `hd_notify_template` (
  `id` varchar(100) NOT NULL DEFAULT '' COMMENT '嵌入点名称',
  `driver` text NOT NULL,
  `template` text NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模版注释',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知模版设置';

-- ----------------------------
--  Table structure for `hd_order`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order`;
CREATE TABLE `hd_order` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `pay_code` varchar(100) NOT NULL COMMENT '支付方式(0:在线支付;1:货到付款)',
  `delivery_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '配送方式ID',
  `delivery_txt` varchar(100) NOT NULL DEFAULT '' COMMENT '配送方式名称',
  `delivery_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '快递单号',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(100) NOT NULL,
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `delivery_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `accept_name` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `zipcode` varchar(6) NOT NULL DEFAULT '',
  `telphone` varchar(20) NOT NULL DEFAULT '',
  `province` int(10) unsigned NOT NULL DEFAULT '0',
  `city` int(10) unsigned NOT NULL DEFAULT '0',
  `area` int(10) unsigned NOT NULL DEFAULT '0',
  `address` varchar(250) NOT NULL DEFAULT '',
  `payable_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  `real_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实付订单金额',
  `payable_freight` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  `taxes` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  `insured` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `coupons_id` mediumint(8) NOT NULL DEFAULT '0',
  `coupons` decimal(15,2) NOT NULL DEFAULT '0.00',
  `integral` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单支付时间',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单确认时间',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `completion_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单完成时间',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `invoice_title` varchar(100) NOT NULL DEFAULT '',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `promotion_id` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单促销ID',
  `promotion_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '订单促销描述',
  `give_point` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '订单赠送积分',
  `give_coupons_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '赠送的优惠券',
  `is_comment` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `balance_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额付款金额',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单管理表';

-- ----------------------------
--  Table structure for `hd_order_goods`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_goods`;
CREATE TABLE `hd_order_goods` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `barcode` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `attribute` varchar(255) NOT NULL DEFAULT '',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用积分情况',
  `shop_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `spec_array` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '商品分类',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `promotion_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `give_point` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '赠送积分',
  `give_coupons_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '赠送优惠券',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单商品明细表';

-- ----------------------------
--  Table structure for `hd_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_log`;
CREATE TABLE `hd_order_log` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `msg` text NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `clientip` char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单日志记录表';

-- ----------------------------
--  Table structure for `hd_order_parcel`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_parcel`;
CREATE TABLE `hd_order_parcel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态（-1：无法配货；0：待配货；1：配货中；2：已配货；）',
  `dateline` int(10) unsigned NOT NULL,
  `accept_name` varchar(20) NOT NULL,
  `province` int(10) NOT NULL,
  `city` int(10) NOT NULL,
  `area` int(10) NOT NULL,
  `mobile` varchar(60) NOT NULL,
  `delivery_txt` varchar(100) NOT NULL,
  `goods_list` text NOT NULL,
  `address` varchar(250) NOT NULL,
  `total_number` smallint(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='发货单管理';

-- ----------------------------
--  Table structure for `hd_order_parcel_log`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_parcel_log`;
CREATE TABLE `hd_order_parcel_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL,
  `user_id` mediumint(8) NOT NULL,
  `user_name` varchar(60) NOT NULL,
  `msg` text NOT NULL,
  `action` varchar(50) NOT NULL,
  `dateline` int(10) NOT NULL,
  `clientip` char(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;

-- ----------------------------
--  Table structure for `hd_order_promotion`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_promotion`;
CREATE TABLE `hd_order_promotion` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `award_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `award_value` varchar(255) NOT NULL DEFAULT '',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `group` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单促销表';

-- ----------------------------
--  Table structure for `hd_order_track`
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_track`;
CREATE TABLE `hd_order_track` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `track_msg` text NOT NULL,
  `isstyem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL,
  `clientip` char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单跟踪';

-- ----------------------------
--  Table structure for `hd_pay`
-- ----------------------------
DROP TABLE IF EXISTS `hd_pay`;
CREATE TABLE `hd_pay` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '递增ID',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '支付接口',
  `trade_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '唯一订单号',
  `subject` varchar(250) NOT NULL DEFAULT '' COMMENT '商品名称',
  `total_fee` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '交易总额（单位：分）',
  `buyer_email` varchar(100) NOT NULL DEFAULT '' COMMENT '买家支付宝账号',
  `method` varchar(50) NOT NULL DEFAULT '' COMMENT '支付方式',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态（0：未支付；1：已支付）',
  `trade_no` char(64) NOT NULL DEFAULT '' COMMENT '支付宝交易号',
  `notify_id` varchar(200) NOT NULL DEFAULT '' COMMENT '通知校验ID',
  `notify_time` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '通知时间',
  `dateline` int(10) unsigned DEFAULT '0' COMMENT '订单时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='在线充值记录';

-- ----------------------------
--  Table structure for `hd_payment`
-- ----------------------------
DROP TABLE IF EXISTS `hd_payment`;
CREATE TABLE `hd_payment` (
  `pay_code` varchar(50) NOT NULL DEFAULT '',
  `pay_ico` varchar(100) NOT NULL DEFAULT '',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_fee` varchar(5) NOT NULL DEFAULT '',
  `pay_desc` text NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态',
  `config` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `isonline` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否在线支付',
  `applies` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`pay_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付方式配置信息';

-- ----------------------------
--  Table structure for `hd_plugin`
-- ----------------------------
DROP TABLE IF EXISTS `hd_plugin`;
CREATE TABLE `hd_plugin` (
  `pluginid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `adminid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL DEFAULT '',
  `identifier` varchar(40) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `datatables` varchar(255) NOT NULL DEFAULT '',
  `directory` varchar(100) NOT NULL DEFAULT '',
  `copyright` varchar(100) NOT NULL DEFAULT '',
  `modules` text NOT NULL,
  `version` varchar(20) NOT NULL DEFAULT '',
  `author` varchar(250) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`pluginid`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
--  Table structure for `hd_pluginvar`
-- ----------------------------
DROP TABLE IF EXISTS `hd_pluginvar`;
CREATE TABLE `hd_pluginvar` (
  `pluginvarid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pluginid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `variable` varchar(40) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `value` text NOT NULL,
  `extra` text NOT NULL,
  PRIMARY KEY (`pluginvarid`),
  KEY `pluginid` (`pluginid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='插件变量表';

-- ----------------------------
--  Table structure for `hd_points_promotion`
-- ----------------------------
DROP TABLE IF EXISTS `hd_points_promotion`;
CREATE TABLE `hd_points_promotion` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `gifts_id` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `award_value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='积分促销';

-- ----------------------------
--  Table structure for `hd_print_tpl_delivery`
-- ----------------------------
DROP TABLE IF EXISTS `hd_print_tpl_delivery`;
CREATE TABLE `hd_print_tpl_delivery` (
  `delivery_id` smallint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`delivery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='快递单打印模板';

-- ----------------------------
--  Table structure for `hd_print_tpl_parcel`
-- ----------------------------
DROP TABLE IF EXISTS `hd_print_tpl_parcel`;
CREATE TABLE `hd_print_tpl_parcel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `parcel_name` varchar(50) NOT NULL,
  `status` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='发货单打印模板';

-- ----------------------------
--  Table structure for `hd_regiment`
-- ----------------------------
DROP TABLE IF EXISTS `hd_regiment`;
CREATE TABLE `hd_regiment` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sum_count` int(11) unsigned NOT NULL DEFAULT '0',
  `least_count` int(11) unsigned NOT NULL DEFAULT '0',
  `regiment_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='团购营销';

-- ----------------------------
--  Table structure for `hd_region`
-- ----------------------------
DROP TABLE IF EXISTS `hd_region`;
CREATE TABLE `hd_region` (
  `area_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上一级的id值',
  `area_name` varchar(50) NOT NULL DEFAULT '' COMMENT '地区名称',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='地区信息';

-- ----------------------------
--  Table structure for `hd_reg_promotion`
-- ----------------------------
DROP TABLE IF EXISTS `hd_reg_promotion`;
CREATE TABLE `hd_reg_promotion` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `coupons_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='注册营销';

-- ----------------------------
--  Table structure for `hd_sms`
-- ----------------------------
DROP TABLE IF EXISTS `hd_sms`;
CREATE TABLE `hd_sms` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `stype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='站内信';

-- ----------------------------
--  Table structure for `hd_spec`
-- ----------------------------
DROP TABLE IF EXISTS `hd_spec`;
CREATE TABLE `hd_spec` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '格式类型',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='规格列表';

-- ----------------------------
--  Table structure for `hd_timed_promotion`
-- ----------------------------
DROP TABLE IF EXISTS `hd_timed_promotion`;
CREATE TABLE `hd_timed_promotion` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `goods_config` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品促销';

-- ----------------------------
--  Table structure for `hd_user`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user`;
CREATE TABLE `hd_user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `valid` char(10) NOT NULL DEFAULT '',
  `group_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `last_session` varchar(200) NOT NULL DEFAULT '',
  `repwd_key` varchar(200) NOT NULL DEFAULT '',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_money` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  `true_name` varchar(50) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `mobile_phone` varchar(20) NOT NULL DEFAULT '',
  `birthday` int(10) unsigned NOT NULL DEFAULT '0',
  `address_id` int(10) unsigned NOT NULL DEFAULT '0',
  `qq` varchar(20) NOT NULL DEFAULT '',
  `msn` varchar(60) NOT NULL DEFAULT '',
  `office_phone` varchar(20) NOT NULL DEFAULT '',
  `home_phone` varchar(20) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `norder_keys` text NOT NULL,
  `freeze_money` decimal(15,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`mobile_phone`,`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员列表';

-- ----------------------------
--  Table structure for `hd_user_address`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_address`;
CREATE TABLE `hd_user_address` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `address_name` varchar(50) NOT NULL DEFAULT '',
  `province` int(5) unsigned NOT NULL DEFAULT '0',
  `city` int(5) unsigned NOT NULL DEFAULT '0',
  `district` int(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(120) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员收货人信息表';

-- ----------------------------
--  Table structure for `hd_user_collect`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_collect`;
CREATE TABLE `hd_user_collect` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员收藏纪录';

-- ----------------------------
--  Table structure for `hd_user_group`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_group`;
CREATE TABLE `hd_user_group` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `min_points` int(10) unsigned NOT NULL DEFAULT '0',
  `max_points` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员等级组';

-- ----------------------------
--  Table structure for `hd_user_moneylog`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_moneylog`;
CREATE TABLE `hd_user_moneylog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `msg` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='财务变动记录表';

-- ----------------------------
--  Table structure for `hd_user_oauth`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_oauth`;
CREATE TABLE `hd_user_oauth` (
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方绑定表';

-- ----------------------------
--  Table structure for `hd_user_pointslog`
-- ----------------------------
DROP TABLE IF EXISTS `hd_user_pointslog`;
CREATE TABLE `hd_user_pointslog` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pay_points` int(11) unsigned NOT NULL DEFAULT '0',
  `descript` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员积分日志表';

-- ----------------------------
--  Table structure for `hd_zone`
-- ----------------------------
DROP TABLE IF EXISTS `hd_zone`;
CREATE TABLE `hd_zone` (
  `id` smallint(2) NOT NULL AUTO_INCREMENT COMMENT '区域列表',
  `name` varchar(60) NOT NULL DEFAULT '',
  `provinces` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='区域表';


-- ----------------------------
-- Records of hd_delivery
-- ----------------------------

INSERT INTO `hd_adv_position` VALUES ('1','页头通栏广告','0','0','页头通栏广告','','0','100'), ('2','右下角伸缩广告','0','0','','','0','100'), ('3','滚屏居中广告','0','0','','','0','100'), ('4','对联广告','0','0','','','0','100'), ('5','登录页广告','0','0','','','0','100'), ('6','列表页左侧广告','0','0','','','0','100'), ('7','列表页产品上方广告','0','0','','','1','100');

INSERT INTO `hd_delivery` VALUES ('1','shunfeng','顺丰快递','','a:2:{i:0;s:1:\"0\";i:1;s:1:\"1\";}','','0','','1','100','a:3:{i:0;s:6:\"alipay\";i:1;s:4:\"bank\";i:2;s:6:\"ws_wap\";}'), ('2','shentong','申通快递','','a:1:{i:0;s:1:\"0\";}','','0','','1','100','a:4:{i:0;s:6:\"alipay\";i:1;s:4:\"bank\";i:2;s:6:\"ws_wap\";i:3;s:12:\"alipay_escow\";}'), ('3','zhongtong','中通快递','','N;','','0','','1','100','N;'), ('4','yuantong','圆通快递','','0','','0','','0','100',''), ('5','huitongkuaidi','百世汇通','','0','','0','','0','100',''), ('6','yunda','韵达快运','','0','','0','','1','100',''), ('7','ems','EMS邮政特快','','0','','0','','0','100',''), ('13','guotong','国通快递','','s:1:\"0\";','','0','','1','100','N;'), ('10','tiantian','天天快递','','a:1:{i:0;s:1:\"1\";}','','0','','1','100','N;'), ('12','debang','德邦快递','','s:1:\"0\";','','0','','1','100','N;');

INSERT INTO `hd_node` VALUES ('1','0','站点设置','Admin','Site','index','','100','1','0','','0'), ('2','0','商品管理','Admin','Product','index','','100','1','0','','0'), ('3','0','订单管理','Admin','Order','index','','100','1','0','','0'), ('4','0','会员管理','Admin','Member','index','','100','1','0','','0'), ('5','0','运营推广','Admin','Market','index','','100','1','0','','0'), ('6','0','内容管理','Admin','Article','index','','100','1','0','','0'), ('7','0','统计报表','Admin','xx3','init','','100','1','0','','0'), ('8','0','云平台','Admin','xx4','init','','100','1','0','','0'), ('9','1','后台首页','Admin','Index','home','','1','1','0','','0'), ('10','1','站点设置','Admin','Site','setup','','2','1','0','','0'), ('11','1','注册与登录设置','Admin','Site','reg','','3','1','0','','0'), ('14','1','主题设置','Admin','Theme','manage','','6','1','0','','0'), ('15','1','支付平台设置','Pay','Pay','manage','','7','1','0','','0'), ('16','1','物流配送设置','Admin','SiteDelivery','lists','','8','1','0','','0'), ('17','1','地区管理','Admin','Region','index','','9','1','0','','0'), ('18','1','权限管理','Admin','AuthManager','index','','11','1','0','','0'), ('19','1','后台管理团队','Admin','AdminUser','index','','12','1','0','','0'), ('20','1','管理团队日志','Admin','AdminLog','index','','13','1','0','','0'), ('21','1','数据库管理','Admin','Database','index','','14','1','0','','0'), ('22','2','商品列表','Goods','Goods','lists','','1','1','0','','0'), ('23','2','添加商品','Goods','Goods','add','','2','1','0','','0'), ('24','2','商品到货通知&HR','Goods','GoodsMessage','lists','','3','1','0','','0'), ('25','2','分类列表','Goods','ProductCategory','lists','','4','1','0','','0'), ('26','2','品牌列表','Goods','ProductBand','lists','','5','1','0','','0'), ('27','2','规格列表','Goods','ProductSpec','lists','','6','1','0','','0'), ('28','2','商品信息导入','Goods','Goods','import','','8','1','0','','0'), ('29','3','订单列表&HR','Goods','AdminOrder','lists','','1','1','0','','0'), ('84','8','在线升级','Admin','Cloud','uppack_trip','','2','1','0','','0'), ('83','3','快递单模板管理','Admin','PrintDelivery','index','','3','1','0','','0'), ('32','3','发货单模板编辑','Admin','PrintParcel','index','','4','1','0','','0'), ('33','3','发货单列表','Admin','Parcel','index','','5','1','0','','0'), ('34','3','退换货单&HR','Admin','','','','6','0','0','','0'), ('35','3','快递100API设置','Goods','AdminOrder','kuaidi','','7','1','0','','0'), ('36','4','会员列表','User','Member','lists','','1','1','0','','0'), ('37','4','会员等级','User','MemberGroup','lists','','2','1','0','','0'), ('38','4','添加会员&HR','User','Member','add','','3','1','0','','0'), ('39','4','咨询列表','User','MemberConsult','lists','','4','1','0','','0'), ('40','4','评论列表&HR','User','MemberComment','lists','','5','1','0','','0'), ('41','4','群发优惠券','User','PushCoupons','lists','','6','1','0','','0'), ('42','4','群发短信','User','','','','7','0','0','','0'), ('43','4','群发站内信','User','PushMessage','lists','','8','1','0','','0'), ('44','4','群发电子邮件&HR','User','PushMail','lists','','9','1','0','','0'), ('45','5','订单促销','Market','OrderPromotion','lists','','1','1','0','','0'), ('46','5','商品促销','Market','GoodsPromotion','lists','','2','1','0','','0'), ('47','5','限时促销&HR','Market','TimedPromotion','lists','','3','1','0','','0'), ('48','5','优惠券管理','Goods','GoodsCoupons','lists','','4','1','0','','0'), ('50','5','积分营销&HR','Admin','','','','6','0','0','','0'), ('51','5','赠品管理','Admin','','','','7','0','0','','0'), ('52','5','赠品分类管理&HR','Admin','','','','8','0','0','','0'), ('53','5','团购管理','Admin','','','','9','0','0','','0'), ('54','5','预售营销','Admin','','','','10','0','0','','0'), ('55','6','站点公告','Article','ArticleAnnounce','lists','','1','1','0','','0'), ('56','6','站点广告','Article','ArticleAdvPosition','lists','','2','1','0','','0'), ('57','6','首页幻灯片&HR','Article','ArticleFocus','lists','','4','1','0','','0'), ('58','6','友情链接','Article','ArticleLink','lists','','5','1','0','','0'), ('59','6','站点帮助&HR','Article','ArticleHelp','lists','','6','1','0','','0'), ('60','6','文章列表','Article','Article','lists','','7','1','0','','0'), ('61','6','分类列表','Article','ArticleCategory','lists','','8','1','0','','0'), ('62','1','区域划分&HR','Admin','Zone','manage','','10','1','0','','0'), ('63','2','商品类型&HR','Goods','ProductModel','lists','','7','1','0','','0'), ('64','6','导航设置','Article','ArticleNavigation','manage','','3','1','0','','0'), ('65','7','销售分析&HR','Count','Count','sell','','1','1','0','','0'), ('66','7','商品数据分析','Count','Count','goods','','2','1','0','','0'), ('67','7','会员数据分析','Count','Count','user','','3','1','0','','0'), ('68','8','平台首页','Admin','Cloud','index','','1','1','0','','0'), ('69','8','插件管理','Admin','Plugin','manage','','3','1','0','','0'), ('81','5','注册营销','','','','','5','0','0','/index.php?m=admin&c=plugin&a=module&pluginid=3&mod=regexp','3'), ('82','1','移动端设置','Admin','Mobile','setting','','6','1','0','','0'), ('85','1','数据库恢复','Admin','Database','index','type=import','14','1','0','','0'), ('86','4','会员财务管理','User','AdminMoneyLog','index','','99','1','0','','0'), ('91','1','通知平台设置','Notify','Notify','setting','','4','1','0','','0'), ('92','1','通知模版设置&HR','Notify','Notify','template','','4','1','0','','0');

INSERT INTO `hd_user_group` VALUES ('1','注册会员','0','199','100','0','100'), ('2','铜牌会员','200','1999','98','0','100'), ('3','银牌会员','2000','9999','95','0','100'), ('4','金牌会员','10000','29999','90','0','100'), ('5','钻石会员','30000','1000000','85','0','100');

INSERT INTO `hd_zone` VALUES ('1', '华北地区', '110000,120000,130000,140000,150000', '1418626517', '100', '1')
, ('2', '华东地区', '310000,320000,330000,340000,360000,370000', '1416193572', '100', '1')
, ('3', '华中地区', '410000,420000,430000', '1416193594', '100', '1')
, ('4', '华南地区', '350000,440000,450000,460000', '1418626596', '100', '1')
, ('5', '东北地区', '210000,220000,230000', '1416193641', '100', '1')
, ('6', '西北地区', '610000,620000,630000,640000,650000', '1416193676', '100', '1')
, ('7', '西南地区', '500000,510000,520000,530000,540000', '1416193714', '100', '1')
, ('8', '港澳台地区', '710000,810000,820000', '1417490296', '100', '1');

INSERT INTO `hd_help` VALUES ('1','0','','','新手指南','','1','100'), ('2','0','','','支付方式','','1','100'), ('3','0','','','配送方式','','1','100'), ('4','0','','','售后服务','','1','100'), ('5','0','','','媒体中心','','1','100'), ('6','1','','','常见问题','<p>内容编辑中……x</p>','1','100'), ('7','1','','','购物流程','','1','100'), ('8','2','','','货到付款','','1','100'), ('9','2','','','网银在线支付','','1','100'), ('10','3','','','到店自提','范德萨范德萨发生','1','100'), ('11','3','','','送货上门','','1','100'), ('12','3','','','快递配送','','1','100'), ('13','4','','','退换货办理','','1','100'), ('14','4','','','客户服务中心','','1','100'), ('15','5','','','关于我们','','1','100'), ('16','5','','','媒体报道','','1','100');

INSERT INTO `hd_friend_link` VALUES ('1','海盗云商','http://www.haidao.la','','','1','100'), ('2','网店系统','http://www.haidao.la','','','1','100');

INSERT INTO `hd_msg_template` VALUES ('1','到货通知','最近到货通知','<p>dear：{$user_name}你关注的商品：{$goods_name}已到货，由于此商品近期销售火爆，请及时购买！</p>\n<p>-------HD商场</p>','0'), ('2','网站订阅','2011年1月最新上架商品','2011年1月最新上架商品','0'), ('3','找回密码','密码找回','<p>dear：{$user_name}：</p><br /><p>您的新密码为{$password},请您尽快登陆用户中心，修改为您常用的密码！</p><br /><p>-------{$site_name}</p><br />','0'), ('4','用户邮件重置密码','{$site_name}重置密码邮件','<div><p>尊敬的{$site_name}用户{$user_name}：</p><p>请点击下面的地址重置你在{$site_name}的密码：</p><p>有效时间5小时!</p><p><a href=\'{$url}\' target=\'_blank\'>{$url}</a></p></div>','0');

INSERT INTO `hd_navigation` VALUES ('1','首页','/index.php','1','100','');
 
INSERT INTO `hd_print_tpl_parcel` VALUES ('1','<table style=\"width:100%;height:10%;\" border=\"0\"><tbody><tr class=\"firstRow\"><td style=\"height:100%;\" rowspan=\"3\"><img style=\"float:left;\" src=\"../../../../statics/images/logo.gif\"/></td><td style=\"width:33%;\" rowspan=\"3\"><p style=\"font-size:200%;text-align:center; display:block; overflow:hidden;\">商品发货单</p></td><td style=\"width:33%;text-align:center;\" rowspan=\"2\"><span style=\"font-size: 20px;\"></span><img style=\"float:left;height:100%;\" src=\"../../../../statics/images/logo_1.gif\"/></td></tr><tr></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:120%;\">{order_sn}</span></td></tr></tbody></table><table style=\"width:100%;margin:2.5% 0px 1% 0px;\" border=\"0\"><tbody><tr class=\"firstRow\" style=\"width:100%;\"><td style=\"width:40%;\">收货人：{accept_name}</td><td style=\"width:26%;\">电话：{mobile}</td><td style=\"width:33%;\">&nbsp;</td><td style=\"width:33%;\"><br/></td></tr><tr style=\"width:100%;\"><td style=\"width:40%;\">省份：{province}</td><td style=\"width:27%;\">城市：{city}</td><td style=\"width:33%;\">区县：{area}</td><td style=\"width:33%;\"><br/></td></tr><tr style=\"width:100%;\"><td style=\"width: 40%; word-break: break-all;\">收货地址: {address}</td><td style=\"width: 27%; word-break: break-all;\">配送公司：{delivery_txt}</td><td style=\"width:33%;\">打印时间：{print_time}</td><td style=\"width:33%;\"><br/></td></tr><tr style=\"width:100%;\"><td style=\"width: 33%; word-break: break-all;\" colspan=\"4\">订单号：{order_sn}</td></tr></tbody></table><h3 style=\"float: left;\">商品清单</h3><table style=\"text-align:center;\" class=\"goodslist\" border=\"1\" cellspacing=\"0\" width=\"100%\"><tbody><tr class=\"firstRow\"><th style=\"border-bottom:1px solid #000000;\">序号</th><th style=\"border-bottom:1px solid #000000;\">商品货号</th><th style=\"border-bottom:1px solid #000000;\">商品名称</th><th style=\"border-bottom:1px solid #000000;\">单价</th><th style=\"border-bottom:1px solid #000000;\">数量</th><th style=\"border-bottom:1px solid #000000;\">金额</th></tr><tr id=\"goodslist\"><td style=\"border-bottom:1px solid #000000;\">{sort_id}</td><td style=\"border-bottom:1px solid #000000;\">{products_sn}</td><td style=\"border-bottom:1px solid #000000;\">{goods_name}</td><td style=\"border-bottom:1px solid #000000;\">{shop_price}</td><td style=\"border-bottom:1px solid #000000;\">{number}</td><td style=\"border-bottom:1px solid #000000;\">{total_goods_price}</td></tr><tr><td style=\"border-bottom:0px solid #000000;\">&nbsp;</td><td style=\"border-bottom:0px solid #000000;\">&nbsp;</td><td style=\"border-bottom:0px solid #000000;\">&nbsp;</td><td style=\"border-bottom:0px solid #000000;\">合计</td><td style=\"border-bottom:0px solid #000000;\">{total_num}</td><td style=\"border-bottom:0px solid #000000;\">{total_price}</td></tr></tbody></table><table style=\"width: 100%; margin-top: 20%;\" border=\"0\"><tbody><tr class=\"firstRow\"><td rowspan=\"3\" style=\"width:5%;\"><img style=\"float:left;\" src=\"../../../../statics/images/logo_1.gif\"/></td><td colspan=\"10\" style=\"width:5%;\">海盗云商</td><td style=\"width:5%;\">&nbsp;</td></tr><tr><td colspan=\"10\" style=\"width:5%;\">最灵活的企业级电子商务系统软件</td><td style=\"width:5%;\">&nbsp;</td></tr><tr><td colspan=\"10\" style=\"width:5%;\">客服电话：400-600-2042</td><td style=\"width:5%;\">1/1</td></tr><tr><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td><td style=\"width:5%;\">&nbsp;</td></tr></tbody></table>','1432543485','默认发货单模板','1');

INSERT INTO `hd_notify_template` VALUES ('n_order_success','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"wc7VIZl96tjYnxxE53xwdavNACnaYyJvZZeEIWvbWfU\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u611f\\u8c22\\u60a8\\u5728{site_name}\\u8d2d\\u7269!&lt;\\/p&gt;&lt;p&gt;\\u60a8\\u7684\\u8ba2\\u5355{order}\\u5df2\\u4e0b\\u5355\\u6210\\u529f\\uff0c\\u60a8\\u9009\\u62e9\\u7684\\u4ed8\\u6b3e\\u65b9\\u5f0f\\u662f{pay_style}&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','下单成功'), ('n_pay_success','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"b75AgG_5UYPcWxZA9cZY09h0vAr5SW-DCsP4PAziNlM\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u60a8\\u7684\\u6b3e\\u9879\\u6211\\u4eec\\u5df2\\u7ecf\\u6536\\u5230\\uff0c\\u6211\\u4eec\\u4f1a\\u5c3d\\u5feb\\u786e\\u8ba4\\u60a8\\u7684\\u8ba2\\u5355\\u5e76\\u5b89\\u6392\\u53d1\\u8d27\\uff0c\\u611f\\u8c22\\u60a8\\u5728{site_name}\\u8d2d\\u7269!&lt;\\/p&gt;&lt;p&gt;\\u6b22\\u8fce\\u60a8\\u968f\\u65f6\\u5173\\u6ce8\\u8ba2\\u5355\\u72b6\\u6001\\uff0c\\u8ba2\\u5355\\u4fe1\\u606f\\u4ee5\\u201c\\u6211\\u7684\\u8ba2\\u5355\\u201d\\u9875\\u9762\\u663e\\u793a\\u4e3a\\u51c6\\uff0c\\u5982\\u6709\\u7591\\u95ee\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d\\u3002&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','付款成功'), ('n_confirm_order','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"-ZnUJ_6EcHrZOazxcorSM6u8Dlv9nTVXHNDGfYLKIaY\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u60a8\\u7684\\u8ba2\\u5355{order}\\u5df2\\u7ecf\\u786e\\u8ba4\\uff0c\\u6211\\u4eec\\u6b63\\u5728\\u7ed9\\u4f60\\u5b89\\u6392\\u914d\\u8d27\\u5e76\\u5c3d\\u5feb\\u5b89\\u6392\\u53d1\\u8d27\\uff0c\\u611f\\u8c22\\u60a8\\u5728{site_name}\\u8d2d\\u7269!&lt;\\/p&gt;&lt;p&gt;\\u6b22\\u8fce\\u60a8\\u968f\\u65f6\\u5173\\u6ce8\\u8ba2\\u5355\\u72b6\\u6001\\uff0c\\u8ba2\\u5355\\u4fe1\\u606f\\u4ee5\\u201c\\u6211\\u7684\\u8ba2\\u5355\\u201d\\u9875\\u9762\\u663e\\u793a\\u4e3a\\u51c6\\uff0c\\u5982\\u6709\\u7591\\u95ee\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d\\u3002&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','确认订单'), ('n_order_delivery','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"g3CLCL0iL3GCCVJS98lVPe8F8fX2W2TvFXPS0GNoe10\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u60a8\\u7684\\u8ba2\\u5355{order}\\u5df2\\u7ecf\\u5df2\\u7ecf\\u51fa\\u5e93\\uff0c\\u6b63\\u5728\\u914d\\u9001\\u5230\\u60a8\\u7684\\u6536\\u8d27\\u5730\\u5740\\uff0c\\u611f\\u8c22\\u60a8\\u5728[site_name]\\u8d2d\\u7269!&lt;\\/p&gt;&lt;p&gt;\\u6b22\\u8fce\\u60a8\\u968f\\u65f6\\u5173\\u6ce8\\u8ba2\\u5355\\u72b6\\u6001\\uff0c\\u8ba2\\u5355\\u4fe1\\u606f\\u4ee5\\u201c\\u6211\\u7684\\u8ba2\\u5355\\u201d\\u9875\\u9762\\u663e\\u793a\\u4e3a\\u51c6\\uff0c\\u5982\\u6709\\u7591\\u95ee\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d\\u3002&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','订单发货'), ('n_recharge_success','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"fcNM7QJDi7skrDzQuoZ50XoVlSw9kVZ5RyAzLxDQ828\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u60a8\\u6b63\\u5728{site_name}\\u5145\\u503c\\u8d26\\u6237\\u4f59\\u989d&lt;\\/p&gt;&lt;p&gt;\\u5145\\u503c\\u91d1\\u989d\\uff1a{total_fee}&lt;\\/p&gt;&lt;p&gt;\\u5df2\\u7ecf\\u5145\\u503c\\u6210\\u529f\\uff0c\\u611f\\u8c22\\u60a8\\u5bf9{site_name}\\u7684\\u652f\\u6301\\u4e0e\\u5173\\u6ce8&lt;\\/p&gt;&lt;p&gt;\\u5982\\u4e0d\\u662f\\u60a8\\u672c\\u4eba\\u64cd\\u4f5c\\uff0c\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d\\uff0c\\u795d\\u60a8\\u751f\\u6d3b\\u6109\\u5feb&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','充值成功'), ('n_money_change','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"1\"}','{\"weixin\":\"SNZvQDbGCnW0tSzyLwcco9rREZ25f--ReMeznpLVFOM\",\"email\":\"&lt;meta charset=&quot;UTF-8&quot;\\/&gt;&lt;title&gt;\\u6d77\\u76d7\\u4e91&lt;\\/title&gt;&lt;h3&gt;\\u5c0a\\u656c\\u7684{user}&lt;\\/h3&gt;&lt;p&gt;\\u60a8\\u5728{site_name}\\u7684\\u8d26\\u6237\\u4f59\\u989d\\u53d1\\u751f\\u53d8\\u52a8&lt;\\/p&gt;&lt;p&gt;\\u53d8\\u52a8\\u539f\\u56e0\\uff1a{msg}&lt;\\/p&gt;&lt;p&gt;\\u6700\\u65b0\\u4f59\\u989d\\uff1a{money}&lt;\\/p&gt;&lt;p&gt;\\u5982\\u4e0d\\u662f\\u60a8\\u672c\\u4eba\\u64cd\\u4f5c\\uff0c\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d\\uff0c\\u795d\\u60a8\\u751f\\u6d3b\\u6109\\u5feb&lt;\\/p&gt;&lt;p&gt;\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d&lt;\\/p&gt;&lt;p&gt;&lt;span style=&quot;float:left;&quot;&gt;Copyright 2013-2014\\u4e91\\u5357\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709 \\u6ec7ICP\\u590713005806\\u53f7&lt;\\/span&gt;&lt;\\/p&gt;\",\"sms\":null,\"alipay\":null}','余额变动'), ('n_goods_arrival','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"0\"}','','商品到货'), ('n_back_pwd','{\"alipay\":\"0\",\"email\":\"1\",\"sms\":\"0\",\"weixin\":\"0\"}','','找回密码'), ('n_reg_validate','{\"alipay\":0,\"email\":\"0\",\"sms\":\"0\",\"weixin\":\"0\"}','','注册验证'), ('n_reg_success','{\"email\":\"1\",\"weixin\":\"0\",\"sms\":\"0\",\"alipay\":\"0\"}','{\"weixin\":\"reg_success tempnn\",\"email\":\"\",\"sms\":null,\"alipay\":null}','注册成功');

## v1.10.0.20150703
##微信自定义菜单表
DROP TABLE IF EXISTS `hd_menu`;
CREATE TABLE `hd_menu` (
`id`  smallint(5) NOT NULL AUTO_INCREMENT ,
`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`type`  tinyint(1) UNSIGNED NOT NULL COMMENT '1:内置链接 2:自定义链接' ,
`link`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`parent_id`  smallint(5) NOT NULL ,
`sort`  int(8) NOT NULL ,
`dateline`  int(10) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

##内置菜单表
DROP TABLE IF EXISTS `hd_default_menu`;
CREATE TABLE `hd_default_menu` (
`id`  smallint(5) NOT NULL ,
`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`url`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status`  tinyint(1) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `hd_default_menu` VALUES (1, '绑定用户', '?m=user&c=public&a=login', 1);
INSERT INTO `hd_default_menu` VALUES (2, '商城首页', '?m=goods&c=index&a=index', 1);
INSERT INTO `hd_default_menu` VALUES (3, '会员中心', '?m=user&c=index&a=index', 1);
INSERT INTO `hd_default_menu` VALUES (4, '我的订单', '?m=user&c=order&a=manage', 1);
INSERT INTO `hd_default_menu` VALUES (5, '商品分类', '?m=goods&c=index&a=category', 1);
INSERT INTO `hd_default_menu` VALUES (6, '我的收藏', '?m=user&c=collect&a=lists', 1);
INSERT INTO `hd_default_menu` VALUES (7, '我的优惠券', '?m=user&c=coupons&a=couponslist', 1);

##新增节点自定义菜单设置、微信公众号设置
INSERT INTO `hd_node` VALUES (93, 1, '自定义菜单设置&HR', 'Admin', 'Menu', 'lists', '', 10, 1, 0, '', 0);
INSERT INTO `hd_node` VALUES (94, 1, '微信公众号配置', 'Admin', 'Site', 'weixin', '', 9, 1, 0, '', 0);
UPDATE `hd_node` SET `sort` = 9 WHERE `id` = 62;

##新增注册成功的邮件模版
UPDATE `hd_notify_template` SET `template` = '{"weixin":null,"email":"<h3 style=\\"margin-left:50px;\\">\\u5c0a\\u656c\\u7684{user}\\uff1a<\\/h3><div class=\\"content\\" style=\\"margin:0 100px;\\"><p>\\u611f\\u8c22\\u60a8\\u6ce8\\u518c{site_name}<\\/p><p>\\u8bf7\\u59a5\\u5584\\u4fdd\\u7ba1\\u60a8\\u7684\\u8d26\\u6237\\u540d\\u53ca\\u5bc6\\u7801\\uff0c\\u5982\\u679c\\u60a8\\u5fd8\\u8bb0\\u8d26\\u6237\\u5bc6\\u7801\\uff0c\\u53ef\\u70b9\\u51fb\\u5fd8\\u8bb0\\u5bc6\\u7801\\u8fdb\\u884c\\u627e\\u56de<\\/p><p>\\u795d\\u60a8\\u5728{site_name}\\u8d2d\\u7269\\u6109\\u5feb\\uff0c\\u5982\\u6709\\u7591\\u95ee\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d<\\/p><\\/div><div style=\\"margin-left:50px;font-size:12px;line-height:100px;border-bottom:1px dashed #999999;\\">\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8<\\/div><div style=\\"margin-left:50px;font-size:12px;line-height:50px;color:#9C9C99;\\">\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d<\\/div><div style=\\"height: 150px; margin-top: 20px; background: rgb(247, 247, 247) none repeat scroll 0% 0%; line-height: 150px; font-size: 12px; color: rgb(156, 156, 153);\\"><span style=\\"float:right;\\">Copyright 2013-2015 \\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709<\\/span><\\/div><p><br\\/><\\/p>","letter":["\\u6ce8\\u518c\\u6210\\u529f","\\u5c0a\\u656c\\u7684{user}\\uff1a&lt;br\\/&gt;\\r\\n&amp;nbsp;&amp;nbsp;\\u611f\\u8c22\\u60a8\\u6ce8\\u518c{site_name}&lt;br\\/&gt;\\r\\n&amp;nbsp;&amp;nbsp;\\u8bf7\\u59a5\\u5584\\u4fdd\\u7ba1\\u60a8\\u7684\\u8d26\\u6237\\u540d\\u53ca\\u5bc6\\u7801\\uff0c\\u5982\\u679c\\u60a8\\u5fd8\\u8bb0\\u8d26\\u6237\\u5bc6\\u7801\\uff0c\\u53ef\\u70b9\\u51fb\\u5fd8\\u8bb0\\u5bc6\\u7801\\u8fdb\\u884c\\u627e\\u56de\\r\\n&amp;nbsp;&amp;nbsp;\\u795d\\u60a8\\u5728{site_name}\\u8d2d\\u7269\\u6109\\u5feb\\uff0c\\u5982\\u6709\\u7591\\u95ee\\u8bf7\\u81f4\\u7535\\u5546\\u57ce\\u5ba2\\u670d"],"sms":null,"alipay":null}' where `id` = 'n_reg_success';

## 更新通知模版找回密码和商品到货通知
UPDATE `hd_notify_template` SET `template` = '{\"weixin\":null,\"email\":\"<h3 style=\\\"margin-left:50px;\\\">\\u5c0a\\u656c\\u7684{user}\\uff1a<\\/h3><div class=\\\"content\\\" style=\\\"margin:0 100px;\\\"><p>\\u4f60\\u5173\\u6ce8\\u7684\\u5546\\u54c1\\uff1a{goods_name}\\u5df2\\u5230\\u8d27<\\/p><p>\\u7531\\u4e8e\\u6b64\\u5546\\u54c1\\u8fd1\\u671f\\u9500\\u552e\\u706b\\u7206\\uff0c\\u8bf7\\u53ca\\u65f6\\u8d2d\\u4e70\\uff01<\\/p><\\/div><div style=\\\"margin-left:50px;font-size:12px;line-height:100px;border-bottom:1px dashed #999999;\\\">\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8<\\/div><div style=\\\"margin-left:50px;font-size:12px;line-height:50px;color:#9C9C99;\\\">\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d<\\/div><div style=\\\"height: 150px; margin-top: 20px; background: rgb(247, 247, 247) none repeat scroll 0% 0%; line-height: 150px; font-size: 12px; color: rgb(156, 156, 153);\\\"><span style=\\\"float:right;\\\">Copyright 2013-2015 \\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709<\\/span><\\/div><p><br\\/><\\/p>\",\"letter\":[\"\",\"\"],\"sms\":null,\"alipay\":null}' WHERE id = 'n_goods_arrival';
UPDATE `hd_notify_template` SET `template` = '{\"weixin\":null,\"email\":\"<h3 style=\\\"margin-left:50px;\\\">\\u5c0a\\u656c\\u7684{user}\\uff1a<\\/h3><div class=\\\"content\\\" style=\\\"margin:0 100px;\\\"><p>\\u8bf7\\u70b9\\u51fb\\u4e0b\\u9762\\u7684\\u5730\\u5740\\u91cd\\u7f6e\\u4f60\\u5728{site_name}\\u7684\\u5bc6\\u7801\\uff1a<\\/p><p>\\u6709\\u6548\\u65f6\\u95f45\\u5c0f\\u65f6!<\\/p><p><a href=\\\"{$url}\\\" target=\\\"_blank\\\">{url}<\\/a><\\/p><\\/div><div style=\\\"margin-left:50px;font-size:12px;line-height:100px;border-bottom:1px dashed #999999;\\\">\\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8<\\/div><div style=\\\"margin-left:50px;font-size:12px;line-height:50px;color:#9C9C99;\\\">\\u6b64\\u4e3a\\u7cfb\\u7edf\\u90ae\\u4ef6\\u8bf7\\u52ff\\u56de\\u590d<\\/div><div style=\\\"height: 150px; margin-top: 20px; background: rgb(247, 247, 247) none repeat scroll 0% 0%; line-height: 150px; font-size: 12px; color: rgb(156, 156, 153);\\\"><span style=\\\"float:right;\\\">Copyright 2013-2015 \\u8fea\\u7c73\\u76d2\\u5b50\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8 \\u7248\\u6743\\u6240\\u6709<\\/span><\\/div><p><br\\/><\\/p><p><br\\/><\\/p>\",\"letter\":null,\"sms\":null,\"alipay\":null}' WHERE id = 'n_back_pwd';

## 新增 订单退货管理节点
INSERT INTO `hd_node` VALUES ('', 3, '订单退货管理&HR', 'Goods', 'AdminOrder','order_return', '', 2, 1, 0, '', 0);

## 新增 订单商品表 退货数量 字段
ALTER TABLE `hd_order_goods` ADD COLUMN `return_nums`  smallint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退货数量' AFTER `give_coupons_id`;

## 订单退货表
DROP TABLE IF EXISTS `hd_order_return`;
CREATE TABLE `hd_order_return` (
`rid`  mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
`order_id`  mediumint(8) UNSIGNED NOT NULL COMMENT '订单ID' ,
`user_id`  mediumint(8) UNSIGNED NOT NULL COMMENT '会员ID' ,
`return_type`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型(退货：1；换货：2；)' ,
`return_delivery_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递名称' ,
`return_delivery_sn`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递号' ,
`return_descript`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退货描述' ,
`return_imgs`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退货传图' ,
`return_status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态(-1：已取消，0：申请中，1：已通过，2：未通过)' ,
`return_date`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请时间' ,
`return_text`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '审核描述' ,
`return_examine_date`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间' ,
PRIMARY KEY (`rid`)
)
ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

## 新增和修改商品图片相关字段
ALTER TABLE `hd_goods` ADD COLUMN `small_pics` text NOT NULL COMMENT '商品显示小图';
ALTER TABLE `hd_goods` MODIFY COLUMN `thumb` text NOT NULL;