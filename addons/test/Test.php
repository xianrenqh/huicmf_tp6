<?php

namespace addons\test;

use think\App;
use think\Addons;

class Test extends Addons
{

    //后台菜单
    public $admin_list = [
        [
            //方法名称
            "action" => "index",
            //菜单标题
            'title'  => '测试添加插件菜单',
            //icon图标，默认为 fa-list
            'icon'   => '',
            //附加参数 例如：a=12&id=777
            "params" => "",
            //默认排序
            'sort'   => 0,
            //状态，1是显示，0是不显示
            'status' => 1
        ],
        [
            //方法名称
            "action" => "index2",
            //菜单标题
            'title'  => '测试添加插件菜单',
            //icon图标，默认为 fa-list
            'icon'   => '',
            //附加参数 例如：a=12&id=777
            "params" => "",
            //默认排序
            'sort'   => 0,
            //状态，1是显示，0是不显示
            'status' => 1
        ]
    ];

    // 该插件的基础信息
    /*public $info = [
        'name'        => 'test',    // 插件标识
        'title'       => '插件测试',    // 插件名称
        'description' => 'thinkph6插件测试',    // 插件简介
        'status'      => 1,    // 状态
        'author'      => 'byron sampson',
        'version'     => '0.1'
    ];*/

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
        // 调用钩子时候的参数信息
        dump($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        dump($this->getConfig());

        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info');
    }
}