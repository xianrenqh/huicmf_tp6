<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-18
 * Time: 下午2:05:02
 * Info:
 */

namespace app\admin\controller\system;

use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\Exception;
use app\common\model\SystemLog;
use app\common\model\LoginLog;

/**
 * @ControllerAnnotation(title="系统日志")
 * Class Node
 * @package app\admin\controller\system
 */
class LogController extends AdminController
{

    /**
     * @NodeAnotation(title="操作日志")
     */
    public function index()
    {
        $data = SystemLog::order('id desc')->paginate(10);

        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="登陆日志")
     */
    public function login_log()
    {
        $data = LoginLog::order('id desc')->paginate(10)->each(function ($item){
            $item['desc']= explode("{",$item['desc'])[0];
        });

        $this->assign('data', $data);

        return $this->fetch();
    }

}