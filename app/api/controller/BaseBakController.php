<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-30
 * Time: 14:46:37
 * Info:
 */

namespace app\api\controller;

use think\App;
use think\facade\Request;

class BaseController extends \app\BaseController
{

    const TOKEN_EXPIRE_TIME = 86400 * 60; // token过期时间 86400*60

    protected $token = '';

    protected $domain = '';

    //设备类型
    protected $deviceType = '';

    protected $version = '';

    protected $apiVersion;

    //用户 id
    protected $userId;

    protected $request;

    //用户
    protected $user;

    protected $app;

    //用户类型
    protected $userType;

    protected $allowedDeviceTypes = ['mobile', 'wechat'];

    protected $mchid;

    protected $appid;

    protected $appKey;

    protected $apiKey;

    public function __construct(App $app = null)
    {
        $this->token;
        $this->request = Request::instance();
        $this->domain  = Request::instance()->domain();
        $this->_initUser();

    }

    public function _initUser()
    {
        $token      = $this->request->header('Hui-Token');
        $deviceType = $this->request->header('Hui-Device-Type');
        $controller = $this->request->controller() ?: '';

        if (empty($deviceType) || ! in_array($deviceType, $this->allowedDeviceTypes)) {
            $this->success('获取成功0');
        }
        $analysis_data = [
            'controller_name' => $controller,
            'action_name'     => $this->request->action() ?: '',
            'token'           => $token ?: '',
            'page_path'       => $this->request->baseUrl(true),
            'page_query'      => $this->request->param(),
            'user_agent'      => $this->request->header('user-agent'),
            'ip_address'      => get_client_ip()
        ];
        if ($this->withoutTokenPass($analysis_data)) {
            return true;
        }
        if (empty($token)) {
            $this->error('请先登录');
        }
        $this->token = $token;
        $user_id     = $this->userTokenDecode();
        if ( ! $user_id) {
            $this->error('请先登录');
        }
        $this->userId = '';
    }

    protected function withoutTokenPass($analysis)
    {
        if (empty($analysis)) {
            return false;
        }
        if (isset($analysis['controller_name']) && in_array($analysis['controller_name'], ['Login', 'Captcha'])) {
            return true;
        }
        //免检
        if ($analysis['controller_name'] == 'Index') {
            $ActionName = [
                'article_list'
            ];
            if (isset($analysis['action_name']) && in_array($analysis['action_name'], $ActionName)) {
                return true;
            }
        }

        return false;
    }

    protected function userTokenDecode()
    {

    }

}