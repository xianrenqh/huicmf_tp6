<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace plugins\demo\controller;

//Demo插件英文名，改成你的插件英文就行了

use app\common\controller\AdminController;
use app\user\model\UserModel;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;

/**
 * Class AdminController
 * @package plugins\demo\controller
 * @ControllerAnnotation(title="演示插件")
 */
class AdminIndexController extends AdminController
{

    protected function initialize()
    {
        parent::initialize();
        $adminId = cmf_get_admin_id();
        if ( ! empty($adminId)) {
            $this->assign('admin_id', $adminId);
        }
    }

    /**
     * @NodeAnotation(title="用户列表")
     */
    public function index()
    {
        halt('获取到用户列表啦');
    }

    /**
     * @NodeAnotation(title="演示插件设置")
     */
    public function setting()
    {
        halt('这里是演示插件设置哦');
    }
}
