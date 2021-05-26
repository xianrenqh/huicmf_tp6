<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午5:18:55
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\App;
use think\Model;

/**
 * Class AdminController
 * @package app\admin\controller\system
 * @ControllerAnnotation(title="管理员管理",auth=true)
 */
class AdminController extends \app\common\controller\AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="管理员列表",auth=true)
     */
    public function index()
    {
        halt('管理员列表');
    }
}