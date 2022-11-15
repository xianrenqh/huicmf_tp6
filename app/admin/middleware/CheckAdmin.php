<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午6:18:27
 * Info:
 */

namespace app\admin\middleware;

use app\common\service\AuthService;
use think\Request;

class CheckAdmin
{

    use \app\common\traits\JumpTrait;

    public function handle(Request $request, \Closure $next)
    {
        $adminConfig = config('admin');
        $adminId     = cmf_get_admin_id();
        $expireTime  = session('admin.expire_time');
        $node        = uncamelize(request()->controller().'/'.request()->action());
        $currentNode = $node;

        $currentController = parse_name($request->controller());
        // 验证登录
        if ( ! empty($adminConfig) && ! in_array($currentController,
                $adminConfig['no_login_controller']) && ! in_array($currentNode, $adminConfig['no_login_node'])) {
            if (empty($adminId)) {
                $this->error('请先登录', [], __url('login/index'));
            }
            // 判断是否登录过期
            if ($expireTime !== true && time() > $expireTime) {
                session('admin', null);
                $this->error('登录已过期，请重新登录', [], __url('admin/login/index'));
            }
        }
        // 验证权限
        if ( ! empty($adminConfig) && ! in_array($currentController,
                $adminConfig['no_auth_controller']) && ! in_array($currentNode, $adminConfig['no_auth_node'])) {
            $checkNode = AuthService::instance()->check($currentNode, $adminId);
            if ( ! $checkNode) {
                $this->error('无访问权限！');
            }
            // 判断是否为演示环境
            if (env('huiadmin.is_demo', false) && $request->isPost()) {
                $this->error('演示环境下不允许修改');
            }
        }

        /*$Auth  = '';
        $check = $Auth->checkNode($currentNode);
        ! $check && $this->error('无权限访问');*/

        return $next($request);
    }
}