-- SQL Dump by Erik Edgren
-- version 1.0
--
-- SQL Dump created: June 10th, 2021 @ 6:11 pm

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";



-- --------------------------------------------------------





--
-- Remove the table if it exists
--

DROP TABLE IF EXISTS `hui_admin`;


--
-- Create the table if it not exists
--

CREATE TABLE IF NOT EXISTS `hui_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `login_failure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginip` varchar(50) NOT NULL DEFAULT '0' COMMENT '登录IP',
  `login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录成功次数',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `token` varchar(59) NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员表';


--
-- List the data for the table
--

INSERT INTO `hui_admin` (`id`, `username`, `nickname`, `password`, `salt`, `avatar`, `email`, `login_failure`, `logintime`, `loginip`, `login_num`, `createtime`, `updatetime`, `token`, `status`) VALUES

('1', 'admin', '超级管理', 'a34c93785fc102733fb645ab6e2873cb', '', '', 'admin@admin.com', '1', '1623294654', '1.193.37.152', '30', '0', '0', '', 'normal');


--
-- Remove the table if it exists
--

DROP TABLE IF EXISTS `hui_auth_group`;


--
-- Create the table if it not exists
--

CREATE TABLE IF NOT EXISTS `hui_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分组表';


--
-- List the data for the table
--

INSERT INTO `hui_auth_group` (`id`, `pid`, `name`, `rules`, `createtime`, `updatetime`, `status`) VALUES

('1', '0', '超级管理员', '*', '1490883540', '149088354', 'normal');


--
-- Remove the table if it exists
--

DROP TABLE IF EXISTS `hui_auth_group_access`;


--
-- Create the table if it not exists
--

CREATE TABLE IF NOT EXISTS `hui_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限分组表';


--
-- List the data for the table
--

INSERT INTO `hui_auth_group_access` (`uid`, `group_id`) VALUES

('1', '1');


--
-- Remove the table if it exists
--

DROP TABLE IF EXISTS `hui_auth_rule`;


--
-- Create the table if it not exists
--

CREATE TABLE IF NOT EXISTS `hui_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '节点类型（1：控制器，2：节点）',
  `node` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `is_auth` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启动RBAC权限控制',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`node`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节点表';


--
-- List the data for the table
--

INSERT INTO `hui_auth_rule` (`id`, `type`, `node`, `title`, `is_auth`, `condition`, `create_time`, `update_time`) VALUES

('1', '1', 'system.admin', '管理员管理', '1', '', '1622011952', '1622011952'),
('2', '2', 'system.admin/index', '管理员列表', '1', '', '1622011952', '1622011952'),
('3', '1', 'system.node', '系统节点管理', '1', '', '1622011952', '1622011952'),
('4', '2', 'system.node/index', '列表', '1', '', '1622011952', '1622011952'),
('5', '2', 'system.node/refreshNode', '系统节点更新', '1', '', '1622011952', '1622011952'),
('6', '2', 'system.node/clearNode', '清除失效节点', '1', '', '1622195846', '1622195846'),
('7', '1', 'system.menu', '菜单管理', '1', '', '1622452395', '1622452395'),
('8', '2', 'system.menu/index', '菜单列表', '1', '', '1622452395', '1622452395'),
('9', '2', 'system.menu/add', '添加', '1', '', '1622518868', '1622518868'),
('10', '2', 'system.menu/edit', '编辑', '1', '', '1622518869', '1622518869'),
('11', '2', 'system.menu/delete', '删除', '1', '', '1622518869', '1622518869'),
('12', '2', 'system.menu/modify', '属性修改', '1', '', '1622518870', '1622518870'),
('13', '1', 'system.auth', '角色组管理', '1', '', '1623229678', '1623229678'),
('14', '2', 'system.auth/index', '角色组列表', '1', '', '1623229678', '1623229678'),
('15', '2', 'system.auth/add', '添加角色组', '1', '', '1623229678', '1623229678'),
('16', '2', 'system.auth/edit', '修改角色组', '1', '', '1623229678', '1623229678'),
('17', '2', 'system.auth/delete', '删除角色组', '1', '', '1623229678', '1623229678'),
('18', '1', 'system.database', '数据库管理', '1', '', '1623238857', '1623238857'),
('19', '2', 'system.database/index', '数据表列表', '1', '', '1623238857', '1623238857'),
('20', '2', 'system.database/backup', '数据备份', '1', '', '1623238857', '1623238857'),
('21', '2', 'system.database/restore', '数据还原', '1', '', '1623238857', '1623238857'),
('22', '2', 'system.database/databack_list', '备份列表', '1', '', '1623238857', '1623238857'),
('23', '2', 'system.database/delete', '删除备份', '1', '', '1623238857', '1623238857'),
('24', '2', 'system.database/download', '下载备份', '1', '', '1623238857', '1623238857'),
('25', '2', 'system.database/optimize', '优化表', '1', '', '1623238857', '1623238857'),
('26', '2', 'system.database/repair', '修复表', '1', '', '1623238857', '1623238857'),
('27', '2', 'system.database/table_structure', '查看表结构', '1', '', '1623238857', '1623238857'),
('28', '2', 'system.database/table_data', '查看表数据', '1', '', '1623238857', '1623238857');


--
-- Remove the table if it exists
--

DROP TABLE IF EXISTS `hui_system_menu`;


--
-- Create the table if it not exists
--

CREATE TABLE IF NOT EXISTS `hui_system_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `href` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) NOT NULL DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `href` (`href`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统菜单表';


--
-- List the data for the table
--

INSERT INTO `hui_system_menu` (`id`, `pid`, `title`, `icon`, `href`, `params`, `target`, `sort`, `status`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES

('1', '99999999', '后台首页', 'fa fa-home', 'index/welcome', '', '_self', '0', '1', '', '0', '0', '0'),
('2', '0', '系统管理', 'fa-chrome', '', '', '_self', '0', '1', '', '0', '1622614371', '0'),
('3', '2', '菜单管理', 'fa-align-justify', 'system.menu/index', '', '_self', '0', '1', '', '0', '1622614310', '0'),
('4', '2', '管理员管理', 'fa-user-md', 'system.admin/index', '', '_self', '0', '1', '', '0', '1622614329', '0'),
('5', '2', '角色管理', 'fa-asl-interpreting', 'system.auth/index', '', '_self', '0', '1', '', '0', '1623229302', '0'),
('6', '2', '节点管理', 'fa-list', 'system.node/index', '', '_self', '0', '1', '', '0', '1622614441', '0'),
('7', '0', '内容管理', '', '', '', '_self', '0', '1', '', '0', '1622515479', '0'),
('8', '7', '文字管理', 'fa-500px', '1', '', '_self', '0', '1', '', '0', '1623238743', '0'),
('9', '2', '数据库管理', 'fa-database', 'system.database/index', '', '_self', '0', '1', '数据库管理', '1623230461', '1623230478', '0');



