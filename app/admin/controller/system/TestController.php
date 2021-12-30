<?php
/**
 * Created by PhpStorm.
 * User: 投实科技
 * Date: 2021-12-30
 * Time: 10:04:14
 * Info: 测试控制器
 */

namespace app\admin\controller\system;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;

/**
 * @ControllerAnnotation(title="测试用控制器")
 * Class Node
 * @package app\admin\controller\system
 */
class TestController extends AdminController
{

    /**
     * @NodeAnotation(title="图片上传测试模板")
     */
    public function upload()
    {
        return $this->fetch();
    }

}