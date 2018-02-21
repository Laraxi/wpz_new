-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.0.51a-community - MySQL Community Edition (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4170
-- Date/time:                    2018-02-20 23:09:44
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table wpz_new.wpz_account_log
DROP TABLE IF EXISTS `wpz_account_log`;
CREATE TABLE IF NOT EXISTS `wpz_account_log` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL default '0' COMMENT '用户ID',
  `pay_type` varchar(50) NOT NULL default '' COMMENT '付款类别',
  `amount` varchar(50) NOT NULL default '' COMMENT '付款金额',
  `left_money` varchar(50) NOT NULL default '' COMMENT '余额',
  `mark` varchar(50) NOT NULL default '' COMMENT '备注',
  `pay_datetime` int(11) unsigned NOT NULL default '0' COMMENT '付款时间',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

-- Dumping data for table wpz_new.wpz_account_log: 0 rows
DELETE FROM `wpz_account_log`;
/*!40000 ALTER TABLE `wpz_account_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `wpz_account_log` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_admin_action
DROP TABLE IF EXISTS `wpz_admin_action`;
CREATE TABLE IF NOT EXISTS `wpz_admin_action` (
  `action_id` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID',
  `level` tinyint(2) unsigned NOT NULL default '0' COMMENT '层级',
  `parent_id` tinyint(3) unsigned NOT NULL default '0' COMMENT '父菜单ID',
  `action_code` text NOT NULL COMMENT 'action代码',
  `name` varchar(20) NOT NULL default '' COMMENT '菜单名称',
  `orders` smallint(5) unsigned NOT NULL default '0' COMMENT '排序',
  `is_system` tinyint(3) unsigned NOT NULL default '0' COMMENT '是否系统菜单',
  `class` tinyint(2) unsigned NOT NULL default '1' COMMENT '权限级别',
  PRIMARY KEY  (`action_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=291 DEFAULT CHARSET=gbk;

-- Dumping data for table wpz_new.wpz_admin_action: 13 rows
DELETE FROM `wpz_admin_action`;
/*!40000 ALTER TABLE `wpz_admin_action` DISABLE KEYS */;
INSERT INTO `wpz_admin_action` (`action_id`, `level`, `parent_id`, `action_code`, `name`, `orders`, `is_system`, `class`) VALUES
	(1, 1, 0, '', '内部管理', 1, 0, 1),
	(2, 1, 0, 'action=member', '会员管理', 2, 0, 1),
	(26, 2, 1, 'action=user', '用户管理', 1, 0, 1),
	(3, 1, 0, '', '系统管理', 0, 1, 0),
	(289, 2, 2, 'action=member&do=level', '用户等级', 5, 0, 1),
	(288, 2, 1, 'action=user&do=oaaction', '角色管理', 4, 0, 1),
	(287, 2, 1, 'action=user&do=new', '添加用户', 2, 0, 1),
	(20, 2, 2, 'action=member&do=link', '链接列表', 4, 0, 1),
	(21, 2, 2, 'action=member&do=accountlog', '消费记录', 3, 0, 1),
	(22, 2, 2, 'action=member&do=paylog', '充值记录', 2, 0, 1),
	(23, 2, 3, 'action=system', '基本信息', 0, 1, 0),
	(24, 2, 3, 'action=system&do=log', '操作日志', 0, 1, 0),
	(25, 2, 3, 'action=system&do=menu', '菜单管理', 0, 1, 0);
/*!40000 ALTER TABLE `wpz_admin_action` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_admin_log
DROP TABLE IF EXISTS `wpz_admin_log`;
CREATE TABLE IF NOT EXISTS `wpz_admin_log` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `log_time` datetime NOT NULL COMMENT '日志时间',
  `user_id` tinyint(3) unsigned NOT NULL default '0' COMMENT '用户ID',
  `log_info` varchar(255) NOT NULL default '' COMMENT '日志内容',
  `ip_address` varchar(30) NOT NULL default '' COMMENT 'IP地址',
  PRIMARY KEY  (`id`),
  KEY `log_time` (`log_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=gbk;

-- Dumping data for table wpz_new.wpz_admin_log: 0 rows
DELETE FROM `wpz_admin_log`;
/*!40000 ALTER TABLE `wpz_admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `wpz_admin_log` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_level
DROP TABLE IF EXISTS `wpz_level`;
CREATE TABLE IF NOT EXISTS `wpz_level` (
  `level_id` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID',
  `level_name` varchar(60) NOT NULL default '' COMMENT '等级名称',
  `point` int(11) unsigned NOT NULL default '0' COMMENT '所需金币',
  `action_list` text NOT NULL COMMENT '权限列表',
  `level_describe` text COMMENT '等级描述',
  `parent_id` int(8) unsigned NOT NULL default '0' COMMENT '上级ID',
  `level_grade` tinyint(2) unsigned NOT NULL default '2' COMMENT '权限级别',
  `is_default` tinyint(1) NOT NULL default '0' COMMENT '是否系统',
  PRIMARY KEY  (`level_id`),
  KEY `user_name` (`level_name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table wpz_new.wpz_level: 3 rows
DELETE FROM `wpz_level`;
/*!40000 ALTER TABLE `wpz_level` DISABLE KEYS */;
INSERT INTO `wpz_level` (`level_id`, `level_name`, `point`, `action_list`, `level_describe`, `parent_id`, `level_grade`, `is_default`) VALUES
	(2, '终身vip', 500, '', '最高级别vip', 0, 0, 0),
	(4, '黄金会员', 300, '', '黄金会员，功能少于终身vip', 1, 0, 0),
	(1, '普通会员', 0, '', '普通会员，注册即拥有', 0, 2, -1);
/*!40000 ALTER TABLE `wpz_level` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_online_user
DROP TABLE IF EXISTS `wpz_online_user`;
CREATE TABLE IF NOT EXISTS `wpz_online_user` (
  `id` int(12) unsigned NOT NULL default '0' COMMENT 'ID',
  `user_id` int(12) unsigned NOT NULL default '0' COMMENT '用户表ID',
  `username` varchar(40) NOT NULL default '' COMMENT '用户名',
  `name` varchar(20) NOT NULL default '' COMMENT '名字',
  `last_login` int(11) unsigned NOT NULL default '0' COMMENT '上次登录时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table wpz_new.wpz_online_user: 1 rows
DELETE FROM `wpz_online_user`;
/*!40000 ALTER TABLE `wpz_online_user` DISABLE KEYS */;
INSERT INTO `wpz_online_user` (`id`, `user_id`, `username`, `name`, `last_login`) VALUES
	(1, 1, 'admin', '系统管理员', 1519139357);
/*!40000 ALTER TABLE `wpz_online_user` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_pay_log
DROP TABLE IF EXISTS `wpz_pay_log`;
CREATE TABLE IF NOT EXISTS `wpz_pay_log` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL default '0' COMMENT '用户ID',
  `order_id` varchar(50) NOT NULL default '' COMMENT '订单ID',
  `amount` varchar(50) NOT NULL default '' COMMENT '付款金额',
  `status` tinyint(1) unsigned NOT NULL default '0' COMMENT '付款状态',
  `mark` varchar(50) NOT NULL default '' COMMENT '备注',
  `pay_datetime` int(11) unsigned NOT NULL default '0' COMMENT '付款时间',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table wpz_new.wpz_pay_log: 0 rows
DELETE FROM `wpz_pay_log`;
/*!40000 ALTER TABLE `wpz_pay_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `wpz_pay_log` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_role
DROP TABLE IF EXISTS `wpz_role`;
CREATE TABLE IF NOT EXISTS `wpz_role` (
  `role_id` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID',
  `role_name` varchar(60) NOT NULL default '' COMMENT '职位名称',
  `action_list` text NOT NULL COMMENT '权限列表',
  `role_describe` text COMMENT '职位描述',
  `parent_id` int(8) unsigned NOT NULL default '0' COMMENT '上级职位',
  `level_grade` tinyint(2) unsigned NOT NULL default '2' COMMENT '管理级别',
  PRIMARY KEY  (`role_id`),
  KEY `user_name` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table wpz_new.wpz_role: 2 rows
DELETE FROM `wpz_role`;
/*!40000 ALTER TABLE `wpz_role` DISABLE KEYS */;
INSERT INTO `wpz_role` (`role_id`, `role_name`, `action_list`, `role_describe`, `parent_id`, `level_grade`) VALUES
	(1, '网站管理员', '', '管理网站信息', 0, 0),
	(4, '信息录入员', '3,23,24,25,1,26,2,22,21,20', '小于超级管理员', 1, 1);
/*!40000 ALTER TABLE `wpz_role` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_setting
DROP TABLE IF EXISTS `wpz_setting`;
CREATE TABLE IF NOT EXISTS `wpz_setting` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ID',
  `lang` varchar(255) NOT NULL default '' COMMENT '基本语言',
  `website` varchar(255) NOT NULL default '' COMMENT '网站地址',
  `webtitle` varchar(255) NOT NULL default '' COMMENT '网站名称',
  `allow_file_type` varchar(255) NOT NULL default '' COMMENT '允许上传的文件类型',
  `allow_file_size` int(10) unsigned NOT NULL default '2048' COMMENT '文件大小限制',
  `qq` varchar(30) NOT NULL default '' COMMENT '客服QQ',
  `copyright` text NOT NULL COMMENT '版权信息',
  `count` text COMMENT '统计代码',
  `page_size` varchar(255) NOT NULL default '' COMMENT '默认分页条数',
  `email` varchar(255) NOT NULL default '' COMMENT '客服email',
  `coin_name` varchar(255) NOT NULL default '' COMMENT '金币名称',
  `coin_rate` varchar(255) NOT NULL default '' COMMENT '金币汇率',
  `link_prize` varchar(255) NOT NULL default '' COMMENT '发布链接奖励',
  `reg_prize` varchar(255) NOT NULL default '' COMMENT '注册奖励',
  `login_prize` varchar(255) NOT NULL default '' COMMENT '登录奖励',
  `fee` varchar(255) NOT NULL default '' COMMENT '点击扣费',
  `wait` varchar(255) NOT NULL default '' COMMENT '等待时间',
  `email_host` varchar(255) NOT NULL default '' COMMENT 'SMTP主机名',
  `email_port` varchar(255) NOT NULL default '' COMMENT '端口',
  `email_true_email` varchar(255) NOT NULL default '' COMMENT '真实地址',
  `email_username` varchar(255) NOT NULL default '' COMMENT '帐号',
  `email_password` varchar(255) NOT NULL default '' COMMENT '密码',
  `wechat_name` varchar(255) NOT NULL default '' COMMENT '公众号名称',
  `wechat_id` varchar(255) NOT NULL default '' COMMENT '公众号原始id',
  `wechat_no` varchar(255) NOT NULL default '' COMMENT '微信号',
  `wechat_appid` varchar(255) NOT NULL default '' COMMENT 'AppID（公众号）',
  `wechat_appsecret` varchar(255) NOT NULL default '' COMMENT 'AppSecret',
  `qqhl_appid` varchar(255) NOT NULL default '' COMMENT 'QQ互联APPID',
  `qqhl_appkey` varchar(255) NOT NULL default '' COMMENT 'QQ互联APPKEY',
  `alipay_account` varchar(255) NOT NULL default '' COMMENT '卖家支付宝号',
  `alipay_pid` varchar(255) NOT NULL default '' COMMENT '支付宝商户PID',
  `alipay_key` varchar(255) NOT NULL default '' COMMENT '支付宝MD5key',
  `keywords` varchar(255) default '' COMMENT '网站关键词',
  `description` varchar(255) default '' COMMENT '网站描述',
  `notice` text COMMENT '站内通知',
  `max_links` varchar(255) NOT NULL default '' COMMENT '每日发布最大链接数',
  `share_time` varchar(255) NOT NULL default '' COMMENT '发布时间系数',
  `index_size` varchar(255) NOT NULL default '' COMMENT '首页显示条数',
  `max_jump` varchar(255) NOT NULL default '' COMMENT '每日最大跳转数',
  `free_try_time` varchar(255) NOT NULL default '' COMMENT '新注册会员试用时间',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table wpz_new.wpz_setting: 1 rows
DELETE FROM `wpz_setting`;
/*!40000 ALTER TABLE `wpz_setting` DISABLE KEYS */;
INSERT INTO `wpz_setting` (`id`, `lang`, `website`, `webtitle`, `allow_file_type`, `allow_file_size`, `qq`, `copyright`, `count`, `page_size`, `email`, `coin_name`, `coin_rate`, `link_prize`, `reg_prize`, `login_prize`, `fee`, `wait`, `email_host`, `email_port`, `email_true_email`, `email_username`, `email_password`, `wechat_name`, `wechat_id`, `wechat_no`, `wechat_appid`, `wechat_appsecret`, `qqhl_appid`, `qqhl_appkey`, `alipay_account`, `alipay_pid`, `alipay_key`, `keywords`, `description`, `notice`, `max_links`, `share_time`, `index_size`, `max_jump`, `free_try_time`) VALUES
	(1, 'zh_cn', 'http://localhost', '网盘分享', '/jpg/jpeg/gif/png/doc/xls/txt/zip/rar/ppt/pdf/rm/mid/wav/bmp/swf/chm/sql/cert/pptx/xlsx/docx', 2048, '46546546', 'Copyright ©2018 百度网盘 xxx.com All Rights Reserved  \r\n<a href="http://www.miitbeian.gov.cn/" target="_blank">粤ICP备123456号-1</a>', '', '20', '123@123.cc', '金币', '11', '5', '30', '3', '5', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '站点关键词', '站点描述', '网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告网站公告', '2', '2', '5', '3', '30');
/*!40000 ALTER TABLE `wpz_setting` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_share_list
DROP TABLE IF EXISTS `wpz_share_list`;
CREATE TABLE IF NOT EXISTS `wpz_share_list` (
  `id` int(12) unsigned NOT NULL auto_increment COMMENT 'ID',
  `user_id` int(12) unsigned NOT NULL default '0' COMMENT '用户表ID',
  `face` text NOT NULL COMMENT '头像信息',
  `name` text NOT NULL COMMENT '昵称信息',
  `number` int(10) NOT NULL default '0' COMMENT '人员数量',
  `link` text NOT NULL COMMENT '网盘链接',
  `create_time` int(11) unsigned NOT NULL default '0' COMMENT '发布时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table wpz_new.wpz_share_list: 21 rows
DELETE FROM `wpz_share_list`;
/*!40000 ALTER TABLE `wpz_share_list` DISABLE KEYS */;
INSERT INTO `wpz_share_list` (`id`, `user_id`, `face`, `name`, `number`, `link`, `create_time`) VALUES
	(60, 22, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/6e505c86.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/382960a0.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/bd9709b9.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/2431bdca.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e60dd9ca.jpg', '大f---jc、hm--93、曲-神、mc--------.com、aq--------.com', 29, 'https://pan.baidu.com/mbox/homepage?short=geC6LoR--', 1510933958),
	(61, 22, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/dd50171e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/611b3c54.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8e1a4493.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/6fc17f9f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/d7b70bc9.jpg', '希望----谢谢', 48, 'https://pan.baidu.com/mbox/homepage?short=mitFTHY1', 1510933958),
	(66, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9080f93d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/aabbcd65.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/02527a73.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e5edaaa3.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e0c976bb.jpg', '辰迷--万代、新星---始人、仅与---余生、dee----hang、热情---007', 23, 'https://pan.baidu.com/mbox/homepage?short=gePxXUb', 1511128347),
	(73, 22, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/b3c1f70e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/119e6812.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/d3d8f426.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9235402e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/4d2a0157.jpg', '18年', 50, 'https://pan.baidu.com/mbox/homepage?short=gfb9kpt-', 1511725728),
	(74, 22, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/b3c1f70e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/119e6812.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/d3d8f426.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9235402e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/4d2a0157.jpg', '18年', 50, 'https://pan.baidu.com/mbox/homepage?short=gfb9kpt', 1511725933),
	(44, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/ee347600.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/bfdc1302.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8b4bce47.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/03199059.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e3331ab5.jpg', 'J-南拳、len----tan、158-----262、ab---666、楛-久', 10, 'https://pan.baidu.com/mbox/homepage?short=miOObEg', 1510853610),
	(45, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/35ead721.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/73ee3c22.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/98608239.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/da466861.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/35147594.jpg', 'c--21、爱一--一課、气温1----6703、asdf-----igvb、我能--饕餮', 39, 'https://pan.baidu.com/mbox/homepage?short=pKRP8uB', 1510853610),
	(46, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8675fd3f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/bcc7d842.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/bfb5a34d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/44a1aa5b.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/fd6bda5b.jpg', '经--话艹、鉁焀--E鈩、神-七奈、77---yyy、旅-火烧', 44, 'https://pan.baidu.com/mbox/homepage?short=jI7rlps', 1510853610),
	(47, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/86b0e803.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/19992007.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9e7bff0c.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9a503f48.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e27ad961.jpg', '每----个', 43, 'https://pan.baidu.com/mbox/homepage?short=o7M1SUm', 1510853610),
	(48, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/ead32e0f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/910bea53.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/d5fe3f5f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/1e753368.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/7270f997.jpg', '60----926、儒雅--离别、最爱---灰机、炼--fd、八级---505', 50, 'https://pan.baidu.com/mbox/homepage?short=bo9XeFH', 1510853610),
	(49, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/cb542320.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/4b640e31.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/737ac83d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8bbaac40.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/4c14fc94.jpg', 'wan----e399、一路--蜗牛、愿风--08、亚-袋、155-----336', 44, 'https://pan.baidu.com/mbox/homepage?short=geDmJN9', 1510853610),
	(50, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/0cbd6a3b.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9080f93d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/02527a73.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e5edaaa3.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e0c976bb.jpg', 'nie-----4474、辰迷--万代、仅与---余生、dee----hang、热情---007', 25, 'https://pan.baidu.com/mbox/homepage?short=gePxXUb11', 1510853610),
	(51, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8577b138.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/f8a97c5c.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e92c4d63.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/7f90b07c.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/c86cde8e.jpg', 'syo----zzs', 21, 'https://pan.baidu.com/mbox/homepage?short=dFaXsE9', 1510853610),
	(52, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/92e39b06.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e90cbc37.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/639fb65e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/b9aeaa79.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/5a7e5ccb.jpg', '动----区', 36, 'https://pan.baidu.com/mbox/homepage?short=kVkU9cb', 1510853610),
	(53, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/e991f500.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/3d3a8d0d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/b576c120.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/a02d685b.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9f05d3b6.jpg', '追-忆、被-漫、邪--飞机、菲-世杰、go---001', 9, 'https://pan.baidu.com/mbox/homepage?short=c0fUhw', 1510853610),
	(54, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/b0365229.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/f867de39.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/41baf94d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/c33bd961.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/c0884a82.jpg', '一分----分享', 29, 'https://pan.baidu.com/mbox/homepage?short=qYPvTSC', 1510853610),
	(55, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/85344803.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/5937fb1a.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/64fa4a39.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/2f24ed83.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/ab970e95.jpg', '资源----愉快', 49, 'https://pan.baidu.com/mbox/homepage?short=kV5npob', 1510853610),
	(56, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/f545f215.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/3d249e48.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/8450bd84.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/0576749f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/363daeb3.jpg', 'xc----101、ho---cv、十年---啦啦、ax--62、187-----110', 41, 'https://pan.baidu.com/mbox/homepage?short=jIGKCOA', 1510853610),
	(57, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/14e95818.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/f52b401b.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/35c40135.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/197fad6a.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/0ff52c7b.jpg', '亚麻----ooo、xul----e123、gs----shs、全-木马、咱们---爱的', 38, 'https://pan.baidu.com/mbox/homepage?short=nv65fuD', 1510853610),
	(58, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/5a1c9c3d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/bf3c515f.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9f5a6587.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/0c7c549a.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/5fa7549f.jpg', '丁-滚、李-源L、ZYT9-----2826、186-----067、fb--49', 13, 'https://pan.baidu.com/mbox/homepage?short=hrBOLoS', 1510853610),
	(59, 1, 'https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/3927aa29.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/effb295b.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/cb79ce5d.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/9ec3915e.jpg,https://ss0.bdstatic.com/7Ls0a8Sm1A5BphGlnYG/sys/portrait/item/a0480db4.jpg', 'ss--vh、LIK----AO4、原--特特、159-----775、Sol----Ezra', 40, 'https://pan.baidu.com/mbox/homepage?short=mhLQblI', 1510853610);
/*!40000 ALTER TABLE `wpz_share_list` ENABLE KEYS */;


-- Dumping structure for table wpz_new.wpz_user
DROP TABLE IF EXISTS `wpz_user`;
CREATE TABLE IF NOT EXISTS `wpz_user` (
  `id` int(12) unsigned NOT NULL auto_increment COMMENT 'ID',
  `qq_id` varchar(100) NOT NULL default '' COMMENT 'QQopenID',
  `username` varchar(40) NOT NULL default '' COMMENT '用户名',
  `password` varchar(40) NOT NULL default '' COMMENT '密码',
  `name` varchar(20) NOT NULL default '' COMMENT '名字',
  `gender` varchar(20) NOT NULL default '' COMMENT '性别',
  `phone` varchar(20) NOT NULL default '' COMMENT '手机',
  `tel` varchar(20) NOT NULL default '' COMMENT '电话',
  `email` varchar(30) NOT NULL default '' COMMENT 'E-mail',
  `qq` varchar(20) NOT NULL default '' COMMENT 'QQ',
  `wechat` varchar(30) NOT NULL default '' COMMENT '微信',
  `money` float NOT NULL default '0' COMMENT '资金',
  `status` varchar(10) NOT NULL default '' COMMENT '状态',
  `time_out` varchar(10) NOT NULL default '' COMMENT '超时时间',
  `actions` text NOT NULL COMMENT '用户权限',
  `last_login` int(11) unsigned NOT NULL default '0' COMMENT '上次登录时间',
  `logout_time` int(11) unsigned NOT NULL default '0' COMMENT '上次退出时间',
  `expire` int(11) unsigned NOT NULL default '0' COMMENT '过期时间',
  `roleid` int(11) unsigned NOT NULL default '0' COMMENT '角色',
  `parent_id` int(8) unsigned NOT NULL default '1' COMMENT '上级管理员',
  `create_id` int(8) unsigned NOT NULL default '1' COMMENT '创建者ID',
  `create_datetime` int(11) unsigned NOT NULL default '0' COMMENT '录入时间',
  `update_datetime` int(11) unsigned NOT NULL default '0' COMMENT '更新时间',
  `jump_count` varchar(255) NOT NULL default '' COMMENT '跳转计数',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Dumping data for table wpz_new.wpz_user: 3 rows
DELETE FROM `wpz_user`;
/*!40000 ALTER TABLE `wpz_user` DISABLE KEYS */;
INSERT INTO `wpz_user` (`id`, `qq_id`, `username`, `password`, `name`, `gender`, `phone`, `tel`, `email`, `qq`, `wechat`, `money`, `status`, `time_out`, `actions`, `last_login`, `logout_time`, `expire`, `roleid`, `parent_id`, `create_id`, `create_datetime`, `update_datetime`, `jump_count`) VALUES
	(1, '0', 'admin', '21232f297a57a5a743894a0e4a801fc3', '系统管理员', '男', '1234561230', 'sdfdsf', '736439095@qq.com', '', 'kuhongchen', 740, '1', '', '', 1519138306, 1519138291, 1512928679, 1, 0, 1, 0, 2017, ''),
	(2, '0', 'wangjun', 'e10adc3949ba59abbe56e057f20f883e', '王军', '男', '13012345678', '010—12345678', '12345678@qq.com', '12345678', '', 0, '正式员工', '', '', 1506369554, 0, 0, 10, 9, 1, 0, 2017, ''),
	(12, '0', 'xueyaaa', 'e10adc3949ba59abbe56e057f20f883e', 'name', '女', '13113113113', '0774-2345672', '736439095@qq.com', '736439095', '', 0, '正式员工', '', '', 0, 0, 0, 4, 1, 1, 0, 2017, '');
/*!40000 ALTER TABLE `wpz_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
