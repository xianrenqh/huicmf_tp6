/*
 Navicat Premium Data Transfer

 Source Server         : huicmf_tp6_auth
 Source Server Type    : MySQL
 Source Server Version : 50733
 Source Host           : 82.156.11.220:3306
 Source Schema         : huicmf_tp6_auth

 Target Server Type    : MySQL
 Target Server Version : 50733
 File Encoding         : 65001

 Date: 28/06/2021 18:43:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for hui_admin
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin`;
CREATE TABLE `hui_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `login_failure` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `logintime` int(10) NOT NULL DEFAULT 0 COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '登录IP',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录成功次数',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `token` varchar(59) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_admin
-- ----------------------------
INSERT INTO `hui_admin` VALUES (1, 'admin', '超级管理', 'a34c93785fc102733fb645ab6e2873cb', 'hui_cmf6', '', 'admin@admin.com', 1, 1624847925, '127.0.0.1', 53, 0, 1624847925, '', 'normal');

-- ----------------------------
-- Table structure for hui_article
-- ----------------------------
DROP TABLE IF EXISTS `hui_article`;
CREATE TABLE `hui_article`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `admin_id` int(5) NOT NULL DEFAULT 0 COMMENT '添加人id',
  `nickname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编辑昵称',
  `type_id` int(5) NOT NULL DEFAULT 0 COMMENT '分类id',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片',
  `thumbs` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图集（json）',
  `flag` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '1置顶,2头条,3特荐,4推荐,5热点,6幻灯,7跳转',
  `is_top` int(1) NOT NULL DEFAULT 0 COMMENT '置顶（根据flag=1来判断）',
  `jump_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '跳转链接',
  `click` int(10) NOT NULL DEFAULT 0 COMMENT '点击量',
  `weight` int(10) NOT NULL DEFAULT 0 COMMENT '排序（数字越大越靠前）',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  `delete_time` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_article
-- ----------------------------

-- ----------------------------
-- Table structure for hui_attachment
-- ----------------------------
DROP TABLE IF EXISTS `hui_attachment`;
CREATE TABLE `hui_attachment`  (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `mimetype` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) NOT NULL DEFAULT 0 COMMENT '创建日期',
  `updatetime` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `uploadtime` int(10) NOT NULL DEFAULT 0 COMMENT '上传时间',
  `storage` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for hui_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `hui_auth_group`;
CREATE TABLE `hui_auth_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父组别',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则ID',
  `createtime` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分组表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_auth_group
-- ----------------------------
INSERT INTO `hui_auth_group` VALUES (1, 0, '超级管理员', '*', 1623383000, 1623383000, 'normal');
INSERT INTO `hui_auth_group` VALUES (2, 0, '管理员', '1,2,30,31,7,8,9,10', 1623383000, 1623383000, 'normal');

-- ----------------------------
-- Table structure for hui_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `hui_auth_group_access`;
CREATE TABLE `hui_auth_group_access`  (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '级别ID',
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限分组表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_auth_group_access
-- ----------------------------
INSERT INTO `hui_auth_group_access` VALUES (1, 1);
INSERT INTO `hui_auth_group_access` VALUES (2, 2);

-- ----------------------------
-- Table structure for hui_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `hui_auth_rule`;
CREATE TABLE `hui_auth_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT 3 COMMENT '节点类型（1：控制器，2：节点）',
  `node` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `is_auth` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启动RBAC权限控制',
  `condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '条件',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`node`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 54 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '节点表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_auth_rule
-- ----------------------------
INSERT INTO `hui_auth_rule` VALUES (1, 1, 'system.admin', '管理员管理', 1, '', 1622011952, 1622011952);
INSERT INTO `hui_auth_rule` VALUES (2, 2, 'system.admin/index', '管理员列表', 1, '', 1622011952, 1622011952);
INSERT INTO `hui_auth_rule` VALUES (3, 1, 'system.node', '系统节点管理', 1, '', 1622011952, 1622011952);
INSERT INTO `hui_auth_rule` VALUES (4, 2, 'system.node/index', '列表', 1, '', 1622011952, 1622011952);
INSERT INTO `hui_auth_rule` VALUES (5, 2, 'system.node/refreshNode', '系统节点更新', 1, '', 1622011952, 1622011952);
INSERT INTO `hui_auth_rule` VALUES (6, 2, 'system.node/clearNode', '清除失效节点', 1, '', 1622195846, 1622195846);
INSERT INTO `hui_auth_rule` VALUES (7, 1, 'system.menu', '菜单管理', 1, '', 1622452395, 1622452395);
INSERT INTO `hui_auth_rule` VALUES (8, 2, 'system.menu/index', '菜单列表', 1, '', 1622452395, 1622452395);
INSERT INTO `hui_auth_rule` VALUES (9, 2, 'system.menu/add', '添加', 1, '', 1622518868, 1622518868);
INSERT INTO `hui_auth_rule` VALUES (10, 2, 'system.menu/edit', '编辑', 1, '', 1622518869, 1622518869);
INSERT INTO `hui_auth_rule` VALUES (11, 2, 'system.menu/delete', '删除', 1, '', 1622518869, 1622518869);
INSERT INTO `hui_auth_rule` VALUES (12, 2, 'system.menu/modify', '属性修改', 1, '', 1622518870, 1622518870);
INSERT INTO `hui_auth_rule` VALUES (13, 1, 'system.auth', '角色组管理', 1, '', 1623229678, 1623229678);
INSERT INTO `hui_auth_rule` VALUES (14, 2, 'system.auth/index', '角色组列表', 1, '', 1623229678, 1623229678);
INSERT INTO `hui_auth_rule` VALUES (15, 2, 'system.auth/add', '添加角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `hui_auth_rule` VALUES (16, 2, 'system.auth/edit', '修改角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `hui_auth_rule` VALUES (17, 2, 'system.auth/delete', '删除角色组', 1, '', 1623229678, 1623229678);
INSERT INTO `hui_auth_rule` VALUES (18, 1, 'system.database', '数据库管理', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (19, 2, 'system.database/index', '数据表列表', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (20, 2, 'system.database/backup', '数据备份', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (21, 2, 'system.database/restore', '数据还原', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (22, 2, 'system.database/databack_list', '备份列表', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (23, 2, 'system.database/delete', '删除备份', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (24, 2, 'system.database/download', '下载备份', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (25, 2, 'system.database/optimize', '优化表', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (26, 2, 'system.database/repair', '修复表', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (27, 2, 'system.database/table_structure', '查看表结构', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (28, 2, 'system.database/table_data', '查看表数据', 1, '', 1623238857, 1623238857);
INSERT INTO `hui_auth_rule` VALUES (29, 2, 'system.auth/authorize', '授权节点', 1, '', 1623394528, 1623394528);
INSERT INTO `hui_auth_rule` VALUES (30, 2, 'system.admin/add', '添加管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `hui_auth_rule` VALUES (31, 2, 'system.admin/edit', '编辑管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `hui_auth_rule` VALUES (32, 2, 'system.admin/delete', '删除管理员', 1, '', 1623406245, 1623406245);
INSERT INTO `hui_auth_rule` VALUES (33, 1, 'system.config', '系统设置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (34, 2, 'system.config/index', '系统设置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (35, 2, 'system.config/save', '保存设置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (36, 2, 'system.config/custom_config', '自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (37, 2, 'system.config/custom_config_add', '添加自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (38, 2, 'system.config/custom_config_edit', '修改自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (39, 2, 'system.config/custom_config_delete', '删除自定义配置', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (40, 1, 'content.article', '文章管理', 1, '', 1623913353, 1623913353);
INSERT INTO `hui_auth_rule` VALUES (41, 2, 'content.article/index', '内容列表', 1, '', 1623913354, 1623913354);
INSERT INTO `hui_auth_rule` VALUES (42, 2, 'content.article/add', '添加内容', 1, '', 1623913354, 1623913354);
INSERT INTO `hui_auth_rule` VALUES (43, 2, 'content.article/edit', '修改内容', 1, '', 1623983125, 1623983125);
INSERT INTO `hui_auth_rule` VALUES (44, 2, 'content.article/delete', '删除内容', 1, '', 1623983126, 1623983126);
INSERT INTO `hui_auth_rule` VALUES (45, 1, 'system.log', '系统日志', 1, '', 1623997395, 1623997395);
INSERT INTO `hui_auth_rule` VALUES (46, 2, 'system.log/index', '日志列表', 1, '', 1623997395, 1623997395);
INSERT INTO `hui_auth_rule` VALUES (47, 1, 'plugin.plugin', '插件管理', 1, '', 1624863982, 1624863982);
INSERT INTO `hui_auth_rule` VALUES (48, 2, 'plugin.plugin/index', '插件列表', 1, '', 1624863982, 1624863982);
INSERT INTO `hui_auth_rule` VALUES (49, 2, 'plugin.plugin/install', '插件安装', 1, '', 1624863982, 1624863982);
INSERT INTO `hui_auth_rule` VALUES (51, 1, 'content.recycle_bin', '回收站管理', 1, '', 1624876870, 1624876870);
INSERT INTO `hui_auth_rule` VALUES (52, 2, 'content.recycle_bin/index', '回收站列表', 1, '', 1624876870, 1624876870);
INSERT INTO `hui_auth_rule` VALUES (53, 2, 'content.recycle_bin/delete', '回收站删除', 1, '', 1624876870, 1624876870);

-- ----------------------------
-- Table structure for hui_config
-- ----------------------------
DROP TABLE IF EXISTS `hui_config`;
CREATE TABLE `hui_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置类型',
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置值',
  `fieldtype` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '字段类型',
  `setting` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段设置',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `tips` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of hui_config
-- ----------------------------
INSERT INTO `hui_config` VALUES (1, 'site_name', 1, '站点名称', 'HuiCMF6后台系统', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (2, 'site_url', 1, '站点跟网址', 'http://huicmf6.cc/', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (3, 'admin_log', 3, '启用后台管理操作日志', '0', 'radio', '', 1, '');
INSERT INTO `hui_config` VALUES (4, 'site_keyword', 1, '站点关键字', 'shop', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (5, 'site_copyright', 1, '网站版权信息', 'Powered By HuiCMF6后台系统 © 2019-2021 小灰灰工作室', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (6, 'site_beian', 1, '站点备案号', '京ICP备666666号', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (7, 'site_description', 1, '站点描述', '我是描述', 'text', '', 1, '');
INSERT INTO `hui_config` VALUES (8, 'site_code', 1, '统计代码', '', 'text', '', 1, '');
INSERT INTO `hui_config` VALUES (9, 'admin_prohibit_ip', 3, '禁止访问网站的IP', '', 'text', '', 1, '');
INSERT INTO `hui_config` VALUES (10, 'mail_server', 4, 'SMTP服务器', 'smtp.exmail.qq.com', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (11, 'mail_port', 4, 'SMTP服务器端口', '465', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (12, 'mail_user', 4, 'SMTP服务器的用户帐号', '', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (13, 'mail_pass', 4, 'SMTP服务器的用户密码', '', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (14, 'mail_inbox', 4, '收件邮箱地址', '', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (15, 'upload_maxsize', 2, '允许上传附件大小', '204800', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (16, 'watermark_enable', 2, '是否开启图片水印', '0', 'radio', '{\"0\":\"否\",\"1\":\"是\"}', 1, '');
INSERT INTO `hui_config` VALUES (17, 'watermark_name', 2, '水印图片名称', 'mark.png', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (18, 'watermark_position', 2, '水印的位置', '9', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (19, 'watermark_touming', 2, '水印透明度', '80', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (20, 'upload_types', 2, '允许上传类型', 'jpg,jpeg,png,gif,bmp', 'string', '', 1, '');
INSERT INTO `hui_config` VALUES (21, 'upload_mode', 2, '图片上传方式', 'local', 'string', '', 1, '');

-- ----------------------------
-- Table structure for hui_hook
-- ----------------------------
DROP TABLE IF EXISTS `hui_hook`;
CREATE TABLE `hui_hook`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '钩子类型(1:系统钩子;2:应用钩子;3:模板钩子;4:后台模板钩子)',
  `once` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否只允许一个插件运行(0:多个;1:一个)',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '钩子名称',
  `hook` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '钩子',
  `app` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '应用名(只有应用钩子才用)',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统钩子表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_hook
-- ----------------------------

-- ----------------------------
-- Table structure for hui_hook_plugin
-- ----------------------------
DROP TABLE IF EXISTS `hui_hook_plugin`;
CREATE TABLE `hui_hook_plugin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `list_order` float NOT NULL DEFAULT 10000 COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态(0:禁用,1:启用)',
  `hook` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '钩子名',
  `plugin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统钩子插件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_hook_plugin
-- ----------------------------
INSERT INTO `hui_hook_plugin` VALUES (1, 10000, 1, 'footer_start', 'Demo');
INSERT INTO `hui_hook_plugin` VALUES (2, 10000, 1, 'footer_start', 'Demo');
INSERT INTO `hui_hook_plugin` VALUES (3, 10000, 1, 'footer_start', 'Demo');
INSERT INTO `hui_hook_plugin` VALUES (4, 10000, 1, 'footer_start', 'Demo');

-- ----------------------------
-- Table structure for hui_plugin
-- ----------------------------
DROP TABLE IF EXISTS `hui_plugin`;
CREATE TABLE `hui_plugin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '插件类型;1:网站;8:微信',
  `has_admin` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否有后台管理,0:没有;1:有',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态;1:开启;0:禁用',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插件安装时间',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `demo_url` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `hooks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '实现的钩子;以“,”分隔',
  `author` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_url` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件版本号',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件描述',
  `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '插件配置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '插件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_plugin
-- ----------------------------
INSERT INTO `hui_plugin` VALUES (7, 1, 1, 1, 1624862689, 'Demo', '插件演示', 'http://demo.thinkcmf.com', '', 'ThinkCMF', 'http://www.thinkcmf.com', '1.0.2', '插件演示', '{\"custom_config\":\"0\",\"text\":\"hello,ThinkCMF!\",\"password\":\"\",\"number\":\"1.0\",\"select\":\"1\",\"checkbox\":1,\"radio\":\"1\",\"radio2\":\"1\",\"textarea\":\"\\u8fd9\\u91cc\\u662f\\u4f60\\u8981\\u586b\\u5199\\u7684\\u5185\\u5bb9\",\"date\":\"2017-05-20\",\"datetime\":\"2017-05-20\",\"color\":\"#103633\",\"image\":\"\",\"file\":\"\",\"location\":\"\"}');

-- ----------------------------
-- Table structure for hui_system_log
-- ----------------------------
DROP TABLE IF EXISTS `hui_system_log`;
CREATE TABLE `hui_system_log`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '管理员ID',
  `url` varchar(1500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作页面',
  `method` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '请求方法',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '日志标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'User-Agent',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '后台操作日志表 - 202106' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_system_log
-- ----------------------------

-- ----------------------------
-- Table structure for hui_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `hui_system_menu`;
CREATE TABLE `hui_system_menu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `href` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '菜单排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态(0:禁用,1:启用)',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE,
  INDEX `href`(`href`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统菜单表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hui_system_menu
-- ----------------------------
INSERT INTO `hui_system_menu` VALUES (1, 99999999, '后台首页', 'fa fa-home', 'index/welcome', '', '_self', 0, 1, '', 0, 0, 0);
INSERT INTO `hui_system_menu` VALUES (2, 0, '系统管理', 'fa-chrome', '', '', '_self', 0, 1, '', 0, 1622614371, 0);
INSERT INTO `hui_system_menu` VALUES (3, 2, '菜单管理', 'fa-align-justify', 'system.menu/index', '', '_self', 0, 1, '', 0, 1622614310, 0);
INSERT INTO `hui_system_menu` VALUES (4, 2, '管理员管理', 'fa-user-md', 'system.admin/index', '', '_self', 0, 1, '', 0, 1622614329, 0);
INSERT INTO `hui_system_menu` VALUES (5, 2, '角色管理', 'fa-asl-interpreting', 'system.auth/index', '', '_self', 0, 1, '', 0, 1623229302, 0);
INSERT INTO `hui_system_menu` VALUES (6, 2, '节点管理', 'fa-list', 'system.node/index', '', '_self', 51, 1, '', 0, 1623920758, 0);
INSERT INTO `hui_system_menu` VALUES (7, 0, '内容管理', '', '', '', '_self', 0, 1, '', 0, 1622515479, 0);
INSERT INTO `hui_system_menu` VALUES (8, 7, '文章管理', 'fa-align-justify', 'content.article/index', '', '_self', 0, 1, '', 0, 1623727177, 0);
INSERT INTO `hui_system_menu` VALUES (9, 2, '数据库管理', 'fa-database', 'system.database/index', '', '_self', 52, 1, '数据库管理', 1623230461, 1623920765, 0);
INSERT INTO `hui_system_menu` VALUES (10, 2, '系统设置', 'fa-cogs', 'system.config/index', '', '_self', 0, 1, '', 1623913925, 1623913959, 0);
INSERT INTO `hui_system_menu` VALUES (11, 2, '自定义设置', 'fa-cog', 'system.config/custom_config', '', '_self', 0, 1, '', 1623920740, 1623920740, 0);
INSERT INTO `hui_system_menu` VALUES (12, 2, '系统日志', 'fa-book', 'system.log/index', '', '_self', 50, 1, '', 1624000849, 1624000871, 0);
INSERT INTO `hui_system_menu` VALUES (13, 7, '回收站管理', 'fa-drupal', 'content.recycle_bin/index', '', '_self', 0, 1, '', 1624876985, 1624876985, 0);

SET FOREIGN_KEY_CHECKS = 1;
