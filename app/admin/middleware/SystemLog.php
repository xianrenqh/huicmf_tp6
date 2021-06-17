<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 上午10:27:11
 * Info:
 */

namespace app\admin\middleware;

use app\admin\service\SystemLogService;

class SystemLog
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected $sensitiveParams = [
        'password',
        'password_again',
    ];

    public function handle($request, \Closure $next)
    {
        $expire_time = session('admin.expire_time');
        $addTimes    = 3600 * 2;
        if (time() - $expire_time < 300) {
            session('admin.expire_time', $expire_time + $addTimes);
        }
        if ($request->isAjax()) {
            $method = strtolower($request->method());
            if (in_array($method, ['post', 'put', 'delete'])) {
                $url    = $request->url();
                $ip     = get_client_ip();
                $params = $request->param();
                if (isset($params['s'])) {
                    unset($params['s']);
                }
                foreach ($params as $key => $val) {
                    in_array($key, $this->sensitiveParams) && $params[$key] = cmf_password($val);
                }
                $data              = [
                    'admin_id'    => cmf_get_admin_id(),
                    'url'         => $url,
                    'method'      => $method,
                    'ip'          => $ip,
                    'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
                    'useragent'   => $_SERVER['HTTP_USER_AGENT'],
                    'create_time' => time(),
                ];
                $admin_log_setting = get_config('admin_log');
                if ($admin_log_setting == 1) {
                    SystemLogService::instance()->save($data);
                }
            }
        }

        return $next($request);
    }

}