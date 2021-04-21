<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午5:57:33
 * Info:
 */

namespace app\common\controller;

use app\BaseController;
use think\facade\Env;
use think\Model;
use app\common\traits\JumpTrait;

class AdminController extends BaseController
{

    /**
     * 当前模型
     * @Model
     * @var object
     */
    protected $model;

    /**
     * 字段排序
     * @var array
     */
    protected $sort = [
        'id' => 'desc',
    ];

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [
        'status',
        'sort',
        'remark',
        'is_delete',
        'is_auth',
        'title',
    ];

    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 模板变量赋值
     *
     * @param string|array $name  模板变量
     * @param mixed        $value 变量值
     *
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    /**
     * 解析和获取模板内容 用于输出
     *
     * @param string $template
     * @param array  $vars
     *
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
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
            parent::validate($data, $validate, $message, $batch);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return true;
    }
}