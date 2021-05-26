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
use app\common\service\MenuService;
use think\facade\Cache;

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
        $cacheData = Cache::get('initAdmin_'.session('admin.id'));
        if ( ! empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(cmf_get_admin_id());
        $data        = [
            'logoInfo' => [
                'title' => 'HuiCMF v6.0',
                'image' => '/static/admin/images/logo.png',
                'href'  => __url('index/index'),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
        ];

        //Cache::tag('initAdmin')->set('initAdmin_' . session('admin.id'), $data);

        return json($data);
    }

    public function welcome()
    {
        return $this->fetch();
    }

}