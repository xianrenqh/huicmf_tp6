<?php
/**
 * Created by PhpStorm.
 * User: 投实科技
 * Date: 2021-07-12
 * Time: 17:22:16
 * Info:
 */

namespace addons\login_bg;

// 注意命名空间规范

use think\Addons;

class Plugin extends Addons
{
    public function __construct(){

    }

// 该插件的基础信息
    public $info = [
        'name'        => 'login_bg',    // 插件标识
        'title'       => '后台登录背景图',    // 插件名称
        'description' => '后台登录背景图',    // 插件简介
        'status'      => 0,    // 状态
        'author'      => 'huihui',
        'version'     => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function hook_login_bg()
    {
        return 1;
    }

}