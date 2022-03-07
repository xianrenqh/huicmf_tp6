/*
 Navicat Premium Data Transfer

 Source Server         : huicmf_tp6_auth
 Source Server Type    : MySQL
 Source Server Version : 50734
 Source Host           : 82.156.11.220:3306
 Source Schema         : huicmf_tp6_auth

 Target Server Type    : MySQL
 Target Server Version : 50734
 File Encoding         : 65001

 Date: 07/03/2022 17:04:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cmf_admin
-- ----------------------------
DROP TABLE IF EXISTS `cmf_admin`;
CREATE TABLE `cmf_admin` (
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
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `token` varchar(59) NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员表';

-- ----------------------------
-- Records of cmf_admin
-- ----------------------------
BEGIN;
INSERT INTO `cmf_admin` VALUES (1, 'admin', '超级管理员', '3296135c44983633d8b1254dd62c6ff1', 'abedf7', '/uploads/avatars/20211105/0783bcd626c8895f2cd082cf7ce502bd.jpg', 'admin@admin.com', 2, 1646636150, '127.0.0.1', 104, 1635759162, 1646636150, '', 'normal');
COMMIT;

-- ----------------------------
-- Table structure for cmf_article
-- ----------------------------
DROP TABLE IF EXISTS `cmf_article`;
CREATE TABLE `cmf_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '' COMMENT '文章标题',
  `admin_id` int(5) NOT NULL DEFAULT '0' COMMENT '添加人id',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑昵称',
  `type_id` int(5) NOT NULL DEFAULT '0' COMMENT '分类id',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `thumbs` text COMMENT '图集（json）',
  `flag` varchar(30) NOT NULL DEFAULT '' COMMENT '1置顶,2头条,3特荐,4推荐,5热点,6幻灯,7跳转',
  `is_top` int(1) NOT NULL DEFAULT '0' COMMENT '置顶（根据flag=1来判断）',
  `jump_url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `click` int(10) NOT NULL DEFAULT '0' COMMENT '点击量',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '排序（数字越大越靠前）',
  `editor` int(1) NOT NULL DEFAULT '1' COMMENT '内容编辑器：1=富文本编辑器；2=MD编辑器',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `keywords` varchar(250) NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(500) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text,
  `content_md` text,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `delete_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cmf_article
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_group`;
CREATE TABLE `cmf_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分组表';

-- ----------------------------
-- Records of cmf_auth_group
-- ----------------------------
BEGIN;
INSERT INTO `cmf_auth_group` VALUES (1, 0, '超级管理员', '*', 1623383000, 1623383000, 'normal');
INSERT INTO `cmf_auth_group` VALUES (2, 0, '管理员', '1,2,30,31,7,8,9,10', 1623383000, 1623383000, 'normal');
COMMIT;

-- ----------------------------
-- Table structure for cmf_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_group_access`;
CREATE TABLE `cmf_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限分组表';

-- ----------------------------
-- Records of cmf_auth_group_access
-- ----------------------------
BEGIN;
INSERT INTO `cmf_auth_group_access` VALUES (1, 1);
COMMIT;

-- ----------------------------
-- Table structure for cmf_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_rule`;
CREATE TABLE `cmf_auth_rule` (
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
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节点表';

-- ----------------------------
-- Records of cmf_auth_rule
-- ----------------------------
BEGIN;
INSERT INTO `cmf_auth_rule` VALUES (1, 1, 'system.admin', '管理员管理', 1, '', 1622011952, 1622011952);
INSERT INTO `cmf_auth_rule` VALUES (2, 2, 'system.admin/index', '管理员列表', 1, '', 1622011952, 1622011952);
INSERT INTO `cmf_auth_rule` VALUES (3, 1, 'system.node', '系统节点管理', 1, '', 1622011952, 1622011952);
INSERT INTO `cmf_auth_rule` VALUES (4, 2, 'system.node/index', '列表', 1, '', 1622011952, 1622011952);
INSERT INTO `cmf_auth_rule` VALUES (5, 2, 'system.node/refreshNode', '系统节点更新', 1, '', 1622011952, 1622011952);
INSERT INTO `cmf_auth_rule` VALUES (6, 2, 'system.node/clearNode', '清除失效节点', 1, '', 1622195846, 1622195846);
INSERT INTO `cmf_auth_rule` VALUES (7, 1, 'system.menu', '菜单管理', 1, '', 1622452395, 1622452395);
INSERT INTO `cmf_auth_rule` VALUES (8, 2, 'system.menu/index', '菜单列表', 1, '', 1622452395, 1622452395);
INSERT INTO `cmf_auth_rule` VALUES (9, 2, 'system.menu/add', '添加', 1, '', 1622518868, 1622518868);
INSERT INTO `cmf_auth_rule` VALUES (10, 2, 'system.menu/edit', '编辑', 1, '', 1622518869, 1622518869);
INSERT INTO `cmf_auth_rule` VALUES (11, 2, 'system.menu/delete', '删除', 1, '', 1622518869, 1622518869);
INSERT INTO `cmf_auth_rule` VALUES (12, 2, 'system.menu/modify', '属性修改', 1, '', 1622518870, 1622518870);
INSERT INTO `cmf_auth_rule` VALUES (13, 1, 'system.auth', '角色组管理', 1, '', 1623229678, 1623229678);
INSERT INTO `cmf_auth_rule` VALUES (14, 2, 'system.auth/index', '角色组列表', 1, '', 1623229678, 1623229678);
INSERT INTO `cmf_auth_rule` VALUES (15, 2, 'system.auth/add', '添加角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `cmf_auth_rule` VALUES (16, 2, 'system.auth/edit', '修改角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `cmf_auth_rule` VALUES (17, 2, 'system.auth/delete', '删除角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `cmf_auth_rule` VALUES (18, 1, 'system.database', '数据库管理', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (19, 2, 'system.database/index', '数据表列表', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (20, 2, 'system.database/backup', '数据备份', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (21, 2, 'system.database/restore', '数据还原', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (22, 2, 'system.database/databack_list', '备份列表', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (23, 2, 'system.database/delete', '删除备份', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (24, 2, 'system.database/download', '下载备份', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (25, 2, 'system.database/optimize', '优化表', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (26, 2, 'system.database/repair', '修复表', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (27, 2, 'system.database/table_structure', '查看表结构', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (28, 2, 'system.database/table_data', '查看表数据', 1, '', 1623238857, 1623238857);
INSERT INTO `cmf_auth_rule` VALUES (29, 2, 'system.auth/authorize', '授权节点', 1, '', 1623394528, 1623394528);
INSERT INTO `cmf_auth_rule` VALUES (30, 2, 'system.admin/add', '添加管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `cmf_auth_rule` VALUES (31, 2, 'system.admin/edit', '编辑管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `cmf_auth_rule` VALUES (32, 2, 'system.admin/delete', '删除管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `cmf_auth_rule` VALUES (33, 1, 'system.config', '系统设置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (34, 2, 'system.config/index', '系统设置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (35, 2, 'system.config/save', '保存设置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (36, 2, 'system.config/custom_config', '自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (37, 2, 'system.config/custom_config_add', '添加自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (38, 2, 'system.config/custom_config_edit', '修改自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (39, 2, 'system.config/custom_config_delete', '删除自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (40, 1, 'content.article', '文章管理', 1, '', 1623913353, 1623913353);
INSERT INTO `cmf_auth_rule` VALUES (41, 2, 'content.article/index', '内容列表', 1, '', 1623913354, 1623913354);
INSERT INTO `cmf_auth_rule` VALUES (42, 2, 'content.article/add', '添加内容', 1, '', 1623913354, 1623913354);
INSERT INTO `cmf_auth_rule` VALUES (43, 2, 'content.article/edit', '修改内容', 1, '', 1623983125, 1623983125);
INSERT INTO `cmf_auth_rule` VALUES (44, 2, 'content.article/delete', '删除内容', 1, '', 1623983126, 1623983126);
INSERT INTO `cmf_auth_rule` VALUES (45, 1, 'system.log', '系统日志', 1, '', 1623997395, 1623997395);
INSERT INTO `cmf_auth_rule` VALUES (46, 2, 'system.log/index', '操作日志', 1, '', 1623997395, 1623997395);
INSERT INTO `cmf_auth_rule` VALUES (47, 1, 'plugin.plugin', '插件管理', 1, '', 1624863982, 1624863982);
INSERT INTO `cmf_auth_rule` VALUES (48, 2, 'plugin.plugin/index', '插件列表', 1, '', 1624863982, 1624863982);
INSERT INTO `cmf_auth_rule` VALUES (49, 2, 'plugin.plugin/install', '插件安装', 1, '', 1624863982, 1624863982);
INSERT INTO `cmf_auth_rule` VALUES (51, 1, 'content.recycle_bin', '回收站管理', 1, '', 1624876870, 1624876870);
INSERT INTO `cmf_auth_rule` VALUES (52, 2, 'content.recycle_bin/index', '回收站列表', 1, '', 1624876870, 1624876870);
INSERT INTO `cmf_auth_rule` VALUES (53, 2, 'content.recycle_bin/delete', '回收站删除', 1, '', 1624876870, 1624876870);
INSERT INTO `cmf_auth_rule` VALUES (54, 2, 'system.log/login_log', '登陆日志', 1, '', 1624956151, 1624956151);
INSERT INTO `cmf_auth_rule` VALUES (55, 1, 'content.category', '前台栏目管理', 1, '', 1635761235, 1635761235);
INSERT INTO `cmf_auth_rule` VALUES (56, 2, 'content.category/index', '栏目列表', 1, '', 1635761235, 1635761235);
INSERT INTO `cmf_auth_rule` VALUES (57, 2, 'content.category/add', '添加栏目', 1, '', 1636094642, 1636094642);
INSERT INTO `cmf_auth_rule` VALUES (58, 2, 'content.category/edit', '修改栏目', 1, '', 1636094642, 1636094642);
INSERT INTO `cmf_auth_rule` VALUES (59, 2, 'content.category/delete', '删除栏目', 1, '', 1636094642, 1636094642);
INSERT INTO `cmf_auth_rule` VALUES (60, 2, 'content.category/modify', '更改栏目显示状态', 1, '', 1636094642, 1636094642);
INSERT INTO `cmf_auth_rule` VALUES (61, 1, 'content.tag', 'Tag标签管理', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (62, 2, 'content.tag/index', 'Tag标签列表', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (63, 2, 'content.tag/add', '添加Tag', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (64, 2, 'content.tag/exit', '修改Tag', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (65, 2, 'content.tag/delete', '删除Tag', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (66, 2, 'content.tag/select', '选择Tag', 1, '', 1636702184, 1636702184);
INSERT INTO `cmf_auth_rule` VALUES (67, 2, 'plugin.plugin/uninstall', '插件卸载', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (68, 2, 'plugin.plugin/config', '插件设置', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (69, 2, 'plugin.plugin/status', '启用禁用', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (70, 1, 'module.banner', '友情链接管理', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (71, 2, 'module.banner/index', '轮播图列表', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (72, 2, 'module.banner/add', '添加轮播图', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (73, 2, 'module.banner/edit', '编辑轮播图', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (74, 2, 'module.banner/delete', '删除轮播图', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (75, 2, 'module.banner/cat_list', '轮播分类管理', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (76, 2, 'module.banner/cat_add', '添加轮播分类', 1, '', 1639018642, 1639018642);
INSERT INTO `cmf_auth_rule` VALUES (77, 1, 'system.test', '测试用控制器', 1, '', 1640851494, 1640851494);
INSERT INTO `cmf_auth_rule` VALUES (78, 2, 'system.test/upload', '图片上传测试模板', 1, '', 1640851494, 1640851494);
INSERT INTO `cmf_auth_rule` VALUES (79, 2, 'system.test/index', '管理员列表', 1, '', 1640851494, 1640851494);
INSERT INTO `cmf_auth_rule` VALUES (80, 2, 'system.test/add', '添加管理员', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (81, 2, 'system.test/edit', '编辑管理员', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (82, 2, 'system.test/delete', '删除管理员', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (83, 1, 'system.up_library', '文件库管理', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (84, 2, 'system.up_library/fileList', '文件库列表', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (85, 2, 'system.up_library/groupList', '分组列表', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (86, 2, 'system.up_library/addGroup', '新增分组', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (87, 2, 'system.up_library/editGroup', '编辑分组', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (88, 2, 'system.up_library/deleteGroup', '删除分组', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (89, 2, 'system.up_library/deleteFiles', '删除文件', 1, '', 1640851495, 1640851495);
INSERT INTO `cmf_auth_rule` VALUES (90, 2, 'system.up_library/moveFiles', '批量移动文件分组', 1, '', 1640851495, 1640851495);
COMMIT;

-- ----------------------------
-- Table structure for cmf_banner
-- ----------------------------
DROP TABLE IF EXISTS `cmf_banner`;
CREATE TABLE `cmf_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(150) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `typeid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1显示0隐藏',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `typeid` (`typeid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cmf_banner
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_banner_type
-- ----------------------------
DROP TABLE IF EXISTS `cmf_banner_type`;
CREATE TABLE `cmf_banner_type` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of cmf_banner_type
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_category
-- ----------------------------
DROP TABLE IF EXISTS `cmf_category`;
CREATE TABLE `cmf_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(100) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `cate_en` varchar(255) NOT NULL DEFAULT '' COMMENT '栏目名称（en）；type=3时，此为url链接',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类别：1=列表栏目；2=单页面；3=外链',
  `parent_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `show_in_nav` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否导航显示',
  `sort_order` smallint(6) NOT NULL DEFAULT '50' COMMENT '排序',
  `cat_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '栏目描述',
  `keywords` varchar(30) NOT NULL DEFAULT '',
  `setting` text COMMENT '相关配置信息',
  `content` text,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_config
-- ----------------------------
DROP TABLE IF EXISTS `cmf_config`;
CREATE TABLE `cmf_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '配置说明',
  `value` text NOT NULL COMMENT '配置值',
  `fieldtype` varchar(20) NOT NULL DEFAULT '' COMMENT '字段类型',
  `setting` text NOT NULL COMMENT '字段设置',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `tips` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置';

-- ----------------------------
-- Records of cmf_config
-- ----------------------------
BEGIN;
INSERT INTO `cmf_config` VALUES (1, 'site_name', 1, '站点名称', 'HuiCMF6后台系统', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (2, 'site_url', 1, '站点跟网址', 'http://huicmf6.cc/', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (3, 'admin_log', 3, '启用后台管理操作日志', '0', 'radio', '', 1, '');
INSERT INTO `cmf_config` VALUES (4, 'site_keyword', 1, '站点关键字', 'shop', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (5, 'site_copyright', 1, '网站版权信息', 'Powered By HuiCMF6后台系统 © 2019-2021 小灰灰工作室', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (6, 'site_beian', 1, '站点备案号', '豫ICP备666666号', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (7, 'site_description', 1, '站点描述', '我是描述', 'text', '', 1, '');
INSERT INTO `cmf_config` VALUES (8, 'site_code', 1, '统计代码', '', 'text', '', 1, '');
INSERT INTO `cmf_config` VALUES (9, 'admin_prohibit_ip', 3, '禁止访问网站的IP', '', 'text', '', 1, '');
INSERT INTO `cmf_config` VALUES (10, 'mail_server', 4, 'SMTP服务器', 'smtp.exmail.qq.com', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (11, 'mail_port', 4, 'SMTP服务器端口', '465', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (12, 'mail_user', 4, 'SMTP服务器的用户帐号', '', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (13, 'mail_pass', 4, 'SMTP服务器的用户密码', '', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (14, 'mail_inbox', 4, '收件邮箱地址', '', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (15, 'upload_maxsize', 2, '允许上传附件大小', '204800', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (16, 'watermark_enable', 2, '是否开启图片水印', '0', 'radio', '{\"0\":\"否\",\"1\":\"是\"}', 1, '');
INSERT INTO `cmf_config` VALUES (17, 'watermark_name', 2, '水印图片名称', 'mark.png', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (18, 'watermark_position', 2, '水印的位置', '9', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (19, 'watermark_touming', 2, '水印透明度', '80', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (20, 'upload_types_image', 2, '允许上传图片类型', 'jpg,jpeg,png,gif,bmp', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (21, 'upload_mode', 2, '图片上传方式', 'local', 'string', '', 1, '');
INSERT INTO `cmf_config` VALUES (22, 'site_editor', 1, '文本编辑器', 'uEditorMini', 'string', ' ', 1, '');
INSERT INTO `cmf_config` VALUES (23, 'upload_types_file', 2, '允许上传附件类型', ' doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,gz,bz2,7z', 'string', ' ', 1, '');
COMMIT;

-- ----------------------------
-- Table structure for cmf_login_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_login_log`;
CREATE TABLE `cmf_login_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `user_name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `browser` varchar(100) NOT NULL DEFAULT '' COMMENT '浏览器名',
  `browser_ver` varchar(100) NOT NULL DEFAULT '' COMMENT '浏览器版本',
  `os` varchar(100) NOT NULL DEFAULT '' COMMENT '操作系统',
  `os_ver` varchar(500) NOT NULL DEFAULT '' COMMENT '操作系统版本',
  `ip_address` varchar(100) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `country` char(100) NOT NULL DEFAULT '' COMMENT '国家',
  `area` char(100) NOT NULL DEFAULT '' COMMENT '省',
  `city` char(100) NOT NULL DEFAULT '' COMMENT '市',
  `isp` varchar(255) NOT NULL DEFAULT '' COMMENT '网络：【电信、联通】',
  `desc` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台登录记录表';

-- ----------------------------
-- Records of cmf_login_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_plugin
-- ----------------------------
DROP TABLE IF EXISTS `cmf_plugin`;
CREATE TABLE `cmf_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '插件类型;1:网站;8:微信',
  `has_admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理,0:没有;1:有',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:开启;0:禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `hooks` varchar(255) NOT NULL DEFAULT '' COMMENT '实现的钩子;以“,”分隔',
  `author` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `description` varchar(255) NOT NULL COMMENT '插件描述',
  `config` text COMMENT '插件配置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='插件表';

-- ----------------------------
-- Records of cmf_plugin
-- ----------------------------
BEGIN;
INSERT INTO `cmf_plugin` VALUES (7, 1, 1, 1, 1624862689, 'Demo', '插件演示', 'http://demo.thinkcmf.com', '', 'ThinkCMF', 'http://www.thinkcmf.com', '1.0.2', '插件演示', '{\"custom_config\":\"0\",\"text\":\"hello,ThinkCMF!\",\"password\":\"\",\"number\":\"1.0\",\"select\":\"1\",\"checkbox\":1,\"radio\":\"1\",\"radio2\":\"1\",\"textarea\":\"\\u8fd9\\u91cc\\u662f\\u4f60\\u8981\\u586b\\u5199\\u7684\\u5185\\u5bb9\",\"date\":\"2017-05-20\",\"datetime\":\"2017-05-20\",\"color\":\"#103633\",\"image\":\"\",\"file\":\"\",\"location\":\"\"}');
COMMIT;

-- ----------------------------
-- Table structure for cmf_system_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_system_log`;
CREATE TABLE `cmf_system_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned DEFAULT '0' COMMENT '管理员ID',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `method` varchar(50) NOT NULL COMMENT '请求方法',
  `title` varchar(100) DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) DEFAULT '' COMMENT 'User-Agent',
  `create_time` int(10) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='后台操作日志表 - 202106';

-- ----------------------------
-- Records of cmf_system_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `cmf_system_menu`;
CREATE TABLE `cmf_system_menu` (
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `title` (`title`) USING BTREE,
  KEY `href` (`href`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统菜单表';

-- ----------------------------
-- Records of cmf_system_menu
-- ----------------------------
BEGIN;
INSERT INTO `cmf_system_menu` VALUES (1, 99999999, '后台首页', 'fa fa-home', 'index/welcome', '', '_self', 0, 1, '', 0, 0, 0);
INSERT INTO `cmf_system_menu` VALUES (2, 0, '系统管理', 'fa-chrome', '', '', '_self', 0, 1, '', 0, 1622614371, 0);
INSERT INTO `cmf_system_menu` VALUES (3, 2, '菜单管理', 'fa-align-justify', 'system.menu/index', '', '_self', 0, 1, '', 0, 1622614310, 0);
INSERT INTO `cmf_system_menu` VALUES (4, 2, '管理员管理', 'fa-user-md', 'system.admin/index', '', '_self', 0, 1, '', 0, 1622614329, 0);
INSERT INTO `cmf_system_menu` VALUES (5, 2, '角色管理', 'fa-asl-interpreting', 'system.auth/index', '', '_self', 0, 1, '', 0, 1623229302, 0);
INSERT INTO `cmf_system_menu` VALUES (6, 2, '节点管理', 'fa-list', 'system.node/index', '', '_self', 51, 1, '', 0, 1623920758, 0);
INSERT INTO `cmf_system_menu` VALUES (7, 0, '内容管理', '', '', '', '_self', 0, 1, '', 0, 1622515479, 0);
INSERT INTO `cmf_system_menu` VALUES (8, 7, '文章管理', 'fa-align-justify', 'content.article/index', '', '_self', 0, 1, '', 0, 1623727177, 0);
INSERT INTO `cmf_system_menu` VALUES (9, 2, '数据库管理', 'fa-database', 'system.database/index', '', '_self', 52, 1, '数据库管理', 1623230461, 1624933860, 0);
INSERT INTO `cmf_system_menu` VALUES (10, 2, '系统设置', 'fa-cogs', 'system.config/index', '', '_self', 0, 1, '', 1623913925, 1623913959, 0);
INSERT INTO `cmf_system_menu` VALUES (11, 2, '自定义设置', 'fa-cog', 'system.config/custom_config', '', '_self', 0, 1, '', 1623920740, 1623920740, 0);
INSERT INTO `cmf_system_menu` VALUES (12, 2, '系统日志', 'fa-book', 'system.log/index', '', '_self', 50, 1, '', 1624000849, 1624000871, 0);
INSERT INTO `cmf_system_menu` VALUES (13, 7, '回收站管理', 'fa-drupal', 'content.recycle_bin/index', '', '_self', 0, 1, '', 1624876985, 1624876985, 0);
INSERT INTO `cmf_system_menu` VALUES (14, 2, '登陆日志', 'fa-flag', 'system.log/login_log', '', '_self', 45, 1, '', 1624956129, 1624956129, 0);
INSERT INTO `cmf_system_menu` VALUES (15, 7, '栏目管理', 'fa-certificate', 'content.category/index', '', '_self', 0, 1, '', 1635761077, 1635836956, 0);
INSERT INTO `cmf_system_menu` VALUES (16, 0, '模块管理', 'fa-modx', 'module', '', '_self', 98, 1, '', 1638499060, 1646102724, 0);
INSERT INTO `cmf_system_menu` VALUES (17, 16, '轮播管理', 'fa-picture-o', 'module.banner/index', '', '_self', 0, 1, '', 1638499108, 1638499108, 0);
INSERT INTO `cmf_system_menu` VALUES (18, 0, '插件管理', 'fa-plug', 'plugin', '', '_self', 99, 1, '', 1638772849, 1646102722, 0);
INSERT INTO `cmf_system_menu` VALUES (19, 18, '插件管理', 'fa-plug', 'plugin.plugin/index', '', '_self', 0, 1, '', 1638773084, 1638773104, 0);
INSERT INTO `cmf_system_menu` VALUES (20, 18, '插件后台列表', 'fa-list', '', '', '_self', 0, 0, '', 1638781367, 1638861440, 0);
INSERT INTO `cmf_system_menu` VALUES (23, 20, '测试添加插件菜单', 'fa-list', 'addons.test/index', '', '_self', 0, 1, '', 1639015517, 1639015517, 0);
INSERT INTO `cmf_system_menu` VALUES (24, 20, '测试添加插件菜单', 'fa-list', 'addons.test/index2', '', '_self', 0, 1, '', 1639015517, 1639015517, 0);
INSERT INTO `cmf_system_menu` VALUES (25, 0, '会员管理', 'fa-user-circle', '', '', '_self', 2, 1, '', 1646102772, 1646102891, 0);
INSERT INTO `cmf_system_menu` VALUES (26, 25, '会员列表', 'fa-user-circle', 'user.user/index', '', '_self', 0, 1, '', 1646102917, 1646102917, 0);
INSERT INTO `cmf_system_menu` VALUES (27, 25, '会员等级', 'fa-graduation-cap', 'user.grade/index', '', '_self', 0, 1, '', 1646103055, 1646103055, 0);
COMMIT;

-- ----------------------------
-- Table structure for cmf_tag
-- ----------------------------
DROP TABLE IF EXISTS `cmf_tag`;
CREATE TABLE `cmf_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL DEFAULT '',
  `total` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '文章总数',
  `times` int(10) NOT NULL DEFAULT '0' COMMENT '次数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `tag` (`tag`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cmf_tag
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_tag_content
-- ----------------------------
DROP TABLE IF EXISTS `cmf_tag_content`;
CREATE TABLE `cmf_tag_content` (
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL DEFAULT '0',
  `tagid` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `tag_index` (`modelid`,`aid`) USING BTREE,
  KEY `tagid_index` (`tagid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of cmf_tag_content
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_upload_file
-- ----------------------------
DROP TABLE IF EXISTS `cmf_upload_file`;
CREATE TABLE `cmf_upload_file` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `storage` varchar(20) NOT NULL DEFAULT '' COMMENT '存储方式',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件分组id',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '存储域名',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小(字节)',
  `file_type` varchar(20) NOT NULL DEFAULT '' COMMENT '文件类型',
  `extension` varchar(20) NOT NULL DEFAULT '' COMMENT '文件扩展名',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `sha1` varchar(200) NOT NULL DEFAULT '0' COMMENT '哈希值',
  `img_width` int(10) NOT NULL DEFAULT '0' COMMENT '图片长度',
  `img_height` int(10) NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_upload_file
-- ----------------------------
BEGIN;
INSERT INTO `cmf_upload_file` VALUES (1, 'local', 0, '/uploads/images/20220301/a5c90269b388616f34e4708f86305a3d.jpg', 'a5c90269b388616f34e4708f86305a3d.jpg', 20200, 'image', 'jpeg', 0, 'a3b88e50540043010b9ec977c52b8c492f03beed', 350, 350, 1646101631);
COMMIT;

-- ----------------------------
-- Table structure for cmf_upload_group
-- ----------------------------
DROP TABLE IF EXISTS `cmf_upload_group`;
CREATE TABLE `cmf_upload_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `group_type` varchar(10) NOT NULL DEFAULT '' COMMENT '文件类型',
  `group_name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类排序(数字越小越靠前)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`group_id`),
  KEY `type_index` (`group_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_upload_group
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_user
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user`;
CREATE TABLE `cmf_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码盐',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '3' COMMENT '1=男 2=女 3=未知',
  `birthday` int(10) NOT NULL DEFAULT '0' COMMENT '生日',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `point` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `grade` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '用户等级',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 = 正常 2 = 停用',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人',
  `remarks` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志 有数据就是删除',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `mobile` (`mobile`),
  KEY `status` (`status`),
  KEY `username` (`username`),
  KEY `sex` (`sex`),
  KEY `balance` (`balance`),
  KEY `point` (`point`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户表';

-- ----------------------------
-- Records of cmf_user
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_user_grade
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_grade`;
CREATE TABLE `cmf_user_grade` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `is_def` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1默认，2不默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户等级表';

-- ----------------------------
-- Records of cmf_user_grade
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_user_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_log`;
CREATE TABLE `cmf_user_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '登录 1  退出2,3注册',
  `params` varchar(200) NOT NULL DEFAULT '' COMMENT '参数',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型，1会员，2管理员',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志 有数据就是删除',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of cmf_user_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_user_point_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_point_log`;
CREATE TABLE `cmf_user_point_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 1=签到 2=消费送积分 3=使用积分；4=后台变动积分',
  `num` int(10) NOT NULL DEFAULT '0' COMMENT '积分数量',
  `balance` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '积分余额',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志 有数据就是删除',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户积分记录表';

-- ----------------------------
-- Records of cmf_user_point_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cmf_user_token
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_token`;
CREATE TABLE `cmf_user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` char(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `platform` smallint(2) NOT NULL DEFAULT '1' COMMENT '平台类型，1就是默认，2就是微信小程序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志 有数据就是删除',
  PRIMARY KEY (`id`,`token`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户token';

-- ----------------------------
-- Records of cmf_user_token
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
