<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-11-10
 * Time: 10:13:44
 * Info:
 */

namespace app\api\controller;

use think\App;
use think\facade\Db;
use think\facade\Cache;
use think\Response;
use think\Container;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use app\common\model\User as UserModel;
use app\common\model\UserToken as UserTokenModel;

class BaseController
{

    // token过期时间
    const TOKEN_EXPIRE_TIME = 86400;

    //token
    protected $token = '';

    //设备类型
    protected $deviceType = '';

    //用户 id
    protected $userId = 0;

    //用户
    protected $user;

    protected $app;

    //Request实例
    protected $request;

    // 验证失败是否抛出异常
    protected $failException = false;

    /**
     * 前置操作方法列表
     * @var array $beforeActionList
     * @access protected
     */
    protected $beforeActionList = [];

    public function __construct(App $app = null)
    {
        $this->app     = $app ?: Container::get('app');
        $this->request = $this->app['request'];
        // 用户验证初始化
        $this->_initUser();

        // 控制器初始化
        $this->initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ? $this->beforeAction($options) : $this->beforeAction($method, $options);
            }
        }
    }

    private function _initUser()
    {
        if ($this->allVerifyPass()) {
            return true;// 不经过user相关的校验(如notify)
        }
        $token      = $this->request->header('XX-Token');
        $controller = $this->request->controller() ?: '';
        $deviceType = $this->request->header('XX-Device-Type');
        if (empty($deviceType) || ! in_array($deviceType, config('api.ALLOWED_DEVICE_TYPES'))) {
            $this->success('设备类型为空哦', []);
        }

        $analysis_data = [
            'controller_name' => $this->request->controller() ?: '',
            'action_name'     => $this->request->action() ?: '',
            'token'           => $token ?: '',
            'device_type'     => $deviceType ?: '',
            'user_id'         => 0,
            'page_path'       => $this->request->baseUrl(true),
            'page_query'      => $this->request->param(),
            'user_agent'      => $this->request->header('user-agent'),
            'ip_address'      => get_client_ip(0, true)
        ];

        $this->deviceType = strtolower($deviceType);

        //api配置中，不做验证的方法
        $noCheckAction = config('api.NO_CHECK_ACTION');
        if ( ! empty($noCheckAction)) {
            if ( ! empty($noCheckAction[$controller])) {
                if ($this->request->action() == $noCheckAction[$controller]) {
                    return true;
                }
            }
        }

        //不做验证的控制器
        if ($this->withOutTokenPass($analysis_data)) {
            return true;
        }
        if (empty($token)) {
            //这里可以写一下接口请求记录到数据库【默认没写】

            $this->error(['code' => 1001, 'msg' => '请先登录']);
        }
        $this->token = $token;
        // token解析，延长及判断单设备
        $user_id = $this->userTokenDecode();

        if ( ! $user_id) {
            $this->error(['code' => 1001, 'msg' => '请先登录']);
        }

        $user = UserModel::where('id', $user_id)->find();
        if ($user->isEmpty()) {
            $this->error(['code' => 1001, 'msg' => '请先登录']);
        } else {
            if ($user->status == 0) {
                $this->error(['code' => 1001, 'msg' => '请先登录']);
            }
        }
        $this->userId             = (int)$user->id;
        $analysis_data['user_id'] = $this->userId;

        //这里写入访问记录【未开发】

        return true;
    }

    // 初始化
    protected function initialize()
    {

    }

    protected function allVerifyPass()
    {
        $controller_name = $this->request->controller() ?: '';
        if (in_array($controller_name, config('api.NO_CHECK_CLASS'))) {
            return true;
        }

        return false;
    }

    private function userTokenDecode()
    {
        $token = $this->token;
        if ( ! Cache::has($token)) {
            $this->error(['code' => 1001, 'msg' => '请先登录']);
        }
        $cache_value = Cache::get($token);
        $cache_arr   = json_decode($cache_value, true);

        if (empty($cache_arr) || ! isset($cache_arr['user_id']) || empty($cache_arr['user_id'])) {
            $this->error(['code' => 1001, 'msg' => '请先登录']);
        }
        $user_id    = (int)$cache_arr['user_id'];
        $last_login = $this->getLastToken($user_id);

        if (empty($last_login) || $last_login == $token) {
            // 删除过期的token
            if (config('api.OLD_TOKEN_DELETE')) {
                Cache::delete($token);
            }
        }

        return $user_id;
    }

    /**
     * 前置操作
     * @access protected
     *
     * @param string $method  前置操作方法名
     * @param array  $options 调用参数 ['only'=>[...]] 或者['except'=>[...]]
     */
    protected function beforeAction($method, $options = [])
    {

    }

    protected function withOutTokenPass($analysis)
    {
        if (empty($analysis)) {
            return false;
        }

        if (isset($analysis['controller_name']) && in_array($analysis['controller_name'], ['Login'])) {
            return true;
        }

        return false;
    }

    /**
     * 设置验证失败后是否抛出异常
     * @access protected
     *
     * @param bool $fail 是否抛出异常
     *
     * @return $this
     */
    protected function validateFailException($fail = true)
    {
        $this->failException = $fail;

        return $this;
    }

    /**
     * 验证数据
     * @access protected
     *
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     * @param mixed        $callback 回调方法（闭包）
     *
     * @return bool
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = $this->app->validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $v = $this->app->validate($validate);
            if ( ! empty($scene)) {
                $v->scene($scene);
            }
        }

        if (is_array($message)) {
            $v->message($message);
        }

        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if ( ! $v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            } else {
                return $v->getError();
            }
        } else {
            return true;
        }
    }

    protected function setExpireKey($key_name, $message, $expire_time = 60)
    {
        if (Cache::has($key_name)) {
            $this->error($message);
        } else {
            Cache::set($key_name, 1, $expire_time);
        }

        return true;
    }

    protected function deleteExpireKey($key_name)
    {
        if (Cache::has($key_name)) {
            Cache::rm($key_name);
        }

        return true;
    }

    protected function createTokenAndUpdateTime($token, $user, $ip)
    {
        $time      = time();
        $user_id   = $user['id'];
        $token_arr = [
            'user_id'     => $user_id,
            'create_time' => $time,
            'ip'          => $ip,
        ];
        Cache::set($token, json_encode($token_arr), self::TOKEN_EXPIRE_TIME);

        // 添加登录记录【未开发】

        return $token;
    }

    private function getLastToken($user_id)
    {
        $userTokenModel = new UserTokenModel();
        $token          = $userTokenModel->getLastToken($user_id);

        return $token;
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     *
     * @param mixed $msg    提示信息
     * @param mixed $data   返回的数据
     * @param array $header 发送的Header信息
     *
     * @return void
     */
    protected function success($msg = '', $data = '', array $header = [])
    {
        $code   = 200;
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type                                   = $this->getResponseType();
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Mac,XX-Api-Version,XX-Wxapp-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response                               = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     *
     * @param mixed $msg    提示信息,若要指定错误码,可以传数组,格式为['code'=>您的错误码,'msg'=>'您的错误消息']
     * @param mixed $data   返回的数据
     * @param array $header 发送的Header信息
     *
     * @return void
     */
    protected function error($msg = '', $data = '', array $header = [])
    {
        $code = 0;
        if (is_array($msg)) {
            $code = $msg['code'];
            $msg  = $msg['msg'];
        }
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type                                   = $this->getResponseType();
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Mac,XX-Api-Version,XX-Wxapp-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response                               = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return 'json';
    }

}