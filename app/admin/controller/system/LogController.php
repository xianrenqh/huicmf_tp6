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
        $username   = $this->request->param('username', '');
        $ip_address = $this->request->param('ip_address', '');
        $login_status = $this->request->param('login_status', '');
        $data       = LoginLog::order('id desc')->where(function ($query) use ($username,$ip_address,$login_status) {
            if ( ! empty($username)) {
                $query->where('user_name', $username);
            }
            if ( ! empty($ip_address)) {
                $query->where('ip_address', $ip_address);
            }
            if(!empty($login_status)){
                $query->whereLike('desc',$login_status.'：%');
            }
        })->paginate(10)->each(function ($item) {
            $item['desc'] = explode("{", $item['desc'])[0];
        });

        $this->assign('data', $data);
        $this->assign('username', $username);
        $this->assign('ip_address', $ip_address);
        $this->assign('login_status', $login_status);

        return $this->fetch();
    }

}