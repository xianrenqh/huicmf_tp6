<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午5:57:33
 * Info:
 */

namespace app\common\controller;

use app\admin\controller\BaseController;
use think\facade\Env;
use think\Model;

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