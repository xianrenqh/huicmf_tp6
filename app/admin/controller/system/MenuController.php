<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-31
 * Time: 下午5:10:58
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\model\SystemMenu;
use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;

/**
 * @ControllerAnnotation(title="菜单管理")
 * Class Node
 * @package app\admin\controller\system
 */
class MenuController extends AdminController
{

    /**
     * @NodeAnotation(title="菜单列表")
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $list  = SystemMenu::select()->toArray();
            $count = SystemMenu::count();
            $data  = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list
            ];

            return json($data);

            $data = [
                'code'  => 1,
                'msg'   => '',
                'count' => 1,
                'data'  => [
                    [
                        'id'     => 1,
                        'title'  => '系统管理',
                        'sort'   => 1,
                        'href'   => 'system.menu/index',
                        'icon'   => 'fa fa-home',
                        'status' => 1,
                        'isMenu' => 0,
                        'pid'    => -1
                    ]
                ]
            ];

            return json($data);
        }

        return $this->fetch();
    }

}