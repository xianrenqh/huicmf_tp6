<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:48:26
 * Info:
 */

namespace app\admin\controller;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
class BaseController
{

    use \app\common\traits\JumpTrait;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     *
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->check_ip();
    }

    //后台IP禁止判断
    final private function check_ip()
    {
        $ip                = get_client_ip();
        $admin_prohibit_ip = get_config('admin_prohibit_ip');
        if (empty($admin_prohibit_ip)) {
            return true;
        }
        $arr = explode(',', $admin_prohibit_ip);
        foreach ($arr as $val) {
            //是否是IP段
            if (strpos($val, '*')) {
                if (strpos($ip, str_replace('.*', '', $val)) !== false) {
                    $this->return_json(['code' => 0, 'msg' => '你在IP禁止段内,禁止访问！~~~']);
                }
            } else {
                //不是IP段,用绝对匹配
                if ($ip == $val) {
                    $this->return_json(['code' => 0, 'msg' => 'IP地址绝对匹配,禁止访问！~~~']);
                }
            }
        }
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
    protected function validate(array $data, $validate, array $message = [], bool $batch = null)
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

    /*
     * 返回json数组
     */
    function return_json($arr = [])
    {
        header('Content-Type:application/json; charset=utf-8');
        die(json_encode($arr));
    }

    /*
     * 模板变量赋值
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    /*
     * 解析和获取模板内容 用于输出
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
    }

}