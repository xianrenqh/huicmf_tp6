<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:42:40
 * Info:
 */

namespace app\admin\controller;

use app\admin\model\LoginLog;
use app\admin\model\SystemLog;
use app\common\controller\AdminController;
use app\common\service\MenuService;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
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

    /**
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
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

    /**
     * 修改后台登录管理员资料
     */
    public function editInfo()
    {
        $admin_id = cmf_get_admin_id();
        $row      = AdminModel::where('id', $admin_id)->withoutField('password')->find();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $res   = $row->save($param);
            if ($res) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $data_login  = LoginLog::where('user_id', $admin_id)->order('id desc')->paginate(10)->each(function ($item) {
            $item['desc'] = explode("{", $item['desc'])[0];
        });
        $data_system = SystemLog::where('admin_id', $admin_id)->order('id desc')->paginate(10);
        $this->assign('data', $row);
        $this->assign('data_login', $data_login);
        $this->assign('data_system', $data_system);

        return $this->fetch();
    }

    /**
     * 清理缓存接口
     */
    public function clearCache()
    {
        Cache::clear();
        $this->success('清理缓存成功');
    }

    function determinebrowser($Agent)
    {
        $browseragent   = ""; //浏览器
        $browserversion = ""; //浏览器的版本
        if (ereg('MSIE ([0-9].[0-9]{1,2})', $Agent, $version)) {
            $browserversion = $version[1];
            $browseragent   = "Internet Explorer";
        } else {
            if (ereg('Opera/([0-9]{1,2}.[0-9]{1,2})', $Agent, $version)) {
                $browserversion = $version[1];
                $browseragent   = "Opera";
            } else {
                if (ereg('Firefox/([0-9.]{1,5})', $Agent, $version)) {
                    $browserversion = $version[1];
                    $browseragent   = "Firefox";
                } else {
                    if (ereg('Chrome/([0-9.]{1,3})', $Agent, $version)) {
                        $browserversion = $version[1];
                        $browseragent   = "Chrome";
                    } else {
                        if (ereg('Safari/([0-9.]{1,3})', $Agent, $version)) {
                            $browseragent   = "Safari";
                            $browserversion = "";
                        } else {
                            $browserversion = "";
                            $browseragent   = "Unknown";
                        }
                    }
                }
            }
        }

        return $browseragent." ".$browserversion;
    }

}