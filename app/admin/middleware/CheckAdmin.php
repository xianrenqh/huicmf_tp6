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
        $adminId    = session('admin.id');
        $expireTime = session('admin.expire_time');

        $currentNode       = [];
        $currentController = [];
        // 验证登录
        empty($adminId) && $this->error('请先登录后台', [], url('admin/login/index'));

        // 判断是否登录过期
        if ($expireTime !== true && time() > $expireTime) {
            session('admin', null);
            $this->error('登录已过期，请重新登录', [], url('admin/login/index'));
        }

        // 验证权限
        exit;
        $Auth  = '';
        $check = $Auth->checkNode($currentNode);
        ! $check && $this->error('无权限访问');

        return $next($request);
    }
}