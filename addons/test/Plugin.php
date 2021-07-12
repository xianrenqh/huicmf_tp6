<?php

namespace addons\test;

// 注意命名空间规范

use think\Addons;

/**
 * 插件测试
 * @author byron sampson
 */
class Plugin extends Addons    // 需继承think\Addons类
{

    // 该插件的基础信息
    public $info = [
        'name'        => 'test',    // 插件标识
        'title'       => '插件测试',    // 插件名称
        'description' => 'thinkph6插件测试',    // 插件简介
        'status'      => 0,    // 状态
        'author'      => 'byron sampson',
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
    public function testhook($param)
    {

        return $this->fetch('info');
    }

}