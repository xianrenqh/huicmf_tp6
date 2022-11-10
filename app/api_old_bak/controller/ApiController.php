<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 上午11:55:35
 * Info:
 */

namespace app\api\controller;

use think\App;
use think\facade\Request;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Validate;
use think\Response;

class ApiController extends \app\BaseController
{

    //use \app\common\traits\JumpTrait;

    protected $token = '';

    protected $domain = '';

    //用户id
    protected $userId = 0;

    //Request实例
    protected $request;

    //应用实例
    protected $app;

    //是否批量验证
    protected $batchValidate = false;

    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header("Access-Control-Allow-Headers:*");
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式

        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $getKey = $this->request->header('set-key');
        //配置统一入口，只让访问init方法
        if (request()->module() != 'api' || request()->controller() != 'Index' || request()->action() != 'index') {
            if ( ! (request()->module() == 'api' && request()->controller() == 'Common')) {     //这个if是为了兼容api/Common控制器可以直接访问，为了向下兼容
                die('error');
            }

        }
    }

    public function __construct(App $app = null)
    {
        $this->request = Request::instance();
        $this->domain  = Request::instance()->domain();
    }

    //此方法用于设置参数
    public function setInit($user_id)
    {
        $this->userId = $user_id;

        return true;
    }

    /**
     * 重写验证规则
     *
     * @param array        $data
     * @param array|string $validate
     * @param array        $message
     * @param bool         $batch
     *
     * @return array|bool|string|true
     */
    public function validate(array $data, $validate, array $message = [], bool $batch = null)
    {
        try {
            self::validateCheck($data, $validate, $message, $batch);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * 验证数据
     * @access protected
     *
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     *
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validateCheck(array $data, $validate, array $message = [], bool $batch = null)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if ( ! empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
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
        $header['Access-Control-Allow-Headers'] = '*';
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

        $type = $this->getResponseType();

        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = '*';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response                               = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return 'json';
    }
}