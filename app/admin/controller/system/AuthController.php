<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-09
 * Time: 17:04:26
 * Info:
 */

namespace app\admin\controller\system;

use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="角色组管理")
 * Class Node
 * @package app\admin\controller\system
 */
class AuthController extends AdminController
{

    /**
     * @NodeAnotation(title="角色组列表")
     */
    public function index()
    {

    }

    /**
     * @NodeAnotation(title="添加角色组")
     */
    public function add()
    {

    }

    /**
     * @NodeAnotation(title="修改角色组")
     */
    public function edit()
    {

    }

    /**
     * @NodeAnotation(title="删除角色组")
     */
    public function delete()
    {

    }

}