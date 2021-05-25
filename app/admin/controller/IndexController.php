<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:42:40
 * Info:
 */

namespace app\admin\controller;

use app\common\controller\AdminController;

class IndexController extends AdminController
{

    /*
     * @NodeAnotation
     */
    public function index()
    {
        return $this->fetch();
    }

    public function menu_json()
    {
        $data = [
            'homeInfo' => ['title' => '首页', 'href' => 'admin/index/welcome'],
            'logoInfo' => ['title' => 'LAYUI MINI', 'image' => '__STATIC_ADMIN__/images/logo.png', 'href' => ''],
            'menuInfo' => [
                [
                    'title'  => '常规管理',
                    'href'   => 'page/setting.html',
                    'icon'   => 'fa fa-file-text',
                    'target' => '_self',
                    'child'  => [
                        [
                            'title'  => '菜单管理',
                            'href'   => 'menu',
                            'icon'   => 'fa fa-window-maximize',
                            'target' => '_self',
                        ]
                    ]
                ]
            ]
        ];

        return json($data);
    }

    public function welcome()
    {
        return $this->fetch();
    }

}