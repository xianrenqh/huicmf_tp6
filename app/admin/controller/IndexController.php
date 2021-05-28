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
use app\admin\model\Admin as AdminModel;
use think\facade\Env;

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

    /**
     * 修改密码
     */
    public function editPassword()
    {
        $adminId = cmf_get_admin_id();
        $data    = AdminModel::where('id', $adminId)->find();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (empty($param['old_password']) || empty($param['new_password']) || empty($param['again_password'])) {
                $this->error('密码信息填写不完整');
            }
            if ($param['new_password'] != $param['again_password']) {
                $this->error('新密码和确认密码不相同');
            }
            //判断旧密码是否正确
            if (cmf_password($param['old_password']) != $data['password']) {
                $this->error('旧密码不正确');
            }
            // 判断是否为演示站点
            $example = Env::get('hui_admin.is_demo', true);
            $example == true && $this->error('演示站点不允许修改密码');

            AdminModel::where('id', $adminId)->data([
                'password'   => cmf_password($param['new_password']),
                'updatetime' => time()
            ])->update();
            $this->success('修改成功');
        }
        $this->assign('data', $data);

        return $this->fetch();
    }

}