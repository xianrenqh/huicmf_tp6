<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------

return [

    // 不需要验证登录的控制器
    'no_login_controller' => [
        'login',
    ],

    // 不需要验证登录的节点
    'no_login_node'       => [
        'login/index',
        'login/out',
    ],

    // 不需要验证权限的控制器
    'no_auth_controller'  => [
        'login',
        'index',
        'upload'
    ],

    // 不需要验证权限的节点
    'no_auth_node'        => [
        'login/index',
        'login/out',
        'system.auth/authorize'
    ],

    //数据库备份文件夹
    'backupDir'           => '/databak/',

    //不做备份的数据表
    //可以是数组也可以是字符串（英文逗号拼接）
    'backupIgnoreTables'  => [
        'hui_system_log'
    ]
];