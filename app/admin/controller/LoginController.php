<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午5:56:00
 * Info:
 */

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\facade\Env;

class LoginController extends AdminController
{

    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if ( ! empty(session('admin')) && ! in_array($action, ['out'])) {
            $adminModuleName = config('app.admin_alias_name');
            $this->success('已登录，无需再次登录', [], url($adminModuleName));
        }
    }

    public function index()
    {
        echo "登录";
    }

}