<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:42:40
 * Info:
 */

namespace app\admin\controller;

use app\admin\model\AuthGroup;
use app\admin\model\LoginLog;
use app\admin\model\SystemLog;
use app\common\controller\AdminController;
use app\common\service\MenuService;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use app\admin\model\Admin as AdminModel;
use think\facade\Env;
use think\facade\Db;

class IndexController extends AdminController
{

    /*
     * @NodeAnotation
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function menu_json()
    {
        $cacheData = Cache::get('initAdmin_'.session('admin.id'));
        if ( ! empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(cmf_get_admin_id());
        $data        = [
            'logoInfo' => [
                'title' => 'HuiCMF v6.0',
                'image' => '/static/admin/images/logo.png',
                'href'  => __url('index/index'),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
        ];

        //Cache::tag('initAdmin')->set('initAdmin_' . session('admin.id'), $data);

        return json($data);
    }

    public function welcome()
    {
        $adminId   = cmf_get_admin_id();
        $roleId    = cmf_get_admin_role_id();
        $roleName  = AuthGroup::whereIn('id', $roleId)->column('name');
        $adminInfo = AdminModel::where('id', $adminId)->find();

        $adminInfo['role_name'] = '';
        if ( ! empty($roleName)) {
            $adminInfo['role_name'] = implode(',', $roleName);
        }
        $this->assign('is_file_admin', file_exists(ROOT_PATH.'/public/admin1.php'));
        $this->assign('admin_info', $adminInfo);
        $this->assign('sys_info', $this->get_sys_info());

        return $this->fetch();
    }

    /**
     * phpinfo信息 按需显示在前台
     * @return array
     */
    public function get_sys_info()
    {
        //$sys_info['os'] = PHP_OS; //操作系统
        $sys_info['ip']           = GetHostByName($_SERVER['SERVER_NAME']); //服务器IP
        $sys_info['web_server']   = $_SERVER['SERVER_SOFTWARE']; //服务器环境
        $sys_info['phpv']         = phpversion(); //php版本
        $sys_info['fileupload']   = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown'; //文件上传限制
        $sys_info['memory_limit'] = ini_get('memory_limit'); //最大占用内存
        //$sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false; //最大执行时间
        //$sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO'; //Zlib支持
        //$sys_info['safe_mode'] = (boolean) ini_get('safe_mode') ? 'YES' : 'NO'; //安全模式
        //$sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        //$sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO'; //Curl支持
        //$sys_info['max_ex_time'] = @ini_get("max_execution_time") . 's';
        $sys_info['domain']          = $_SERVER['HTTP_HOST']; //域名
        $sys_info['remaining_space'] = round((@disk_free_space(".") / (1024 * 1024)), 2).'M'; //剩余空间
        //$sys_info['user_ip'] = $_SERVER['REMOTE_ADDR']; //用户IP地址
        $sys_info['beijing_time'] = gmdate("Y年n月j日 H:i:s", time() + 8 * 3600); //北京时间
        $sys_info['time']         = date("Y年n月j日 H:i:s"); //服务器时间
        //$sys_info['web_directory'] = $_SERVER["DOCUMENT_ROOT"]; //网站目录
        $mysqlinfo                 = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        if (function_exists("gd_info")) {
            //GD库版本
            $gd                 = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }

        return $sys_info;
    }

    /**
     *
     */
    public function welcome_xiugai()
    {
        return $this->fetch('welcome_xiugai');
    }

    /**
     * 修改密码
     */
    public function editPassword()
    {
        $adminId = cmf_get_admin_id();
        $data    = AdminModel::where('id', $adminId)->find();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (empty($param['old_password']) || empty($param['new_password']) || empty($param['again_password'])) {
                $this->error('密码信息填写不完整');
            }
            if ($param['new_password'] != $param['again_password']) {
                $this->error('新密码和确认密码不相同');
            }
            //判断旧密码是否正确
            if (cmf_password($param['old_password']) != $data['password']) {
                $this->error('旧密码不正确');
            }
            // 判断是否为演示站点
            $example = Env::get('hui_admin.is_demo', true);
            $example == true && $this->error('演示站点不允许修改密码');

            AdminModel::where('id', $adminId)->data([
                'password'   => cmf_password($param['new_password']),
                'updatetime' => time()
            ])->update();
            $this->success('修改成功');
        }
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * 修改后台登录管理员资料
     */
    public function editInfo()
    {
        $admin_id = cmf_get_admin_id();
        $row      = AdminModel::where('id', $admin_id)->withoutField('password')->find();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $res   = $row->save($param);
            if ($res) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $data_login  = LoginLog::where('user_id', $admin_id)->order('id desc')->paginate(10)->each(function ($item) {
            $item['desc'] = explode("{", $item['desc'])[0];
        });
        $data_system = SystemLog::where('admin_id', $admin_id)->order('id desc')->paginate(10);
        $this->assign('data', $row);
        $this->assign('data_login', $data_login);
        $this->assign('data_system', $data_system);

        return $this->fetch();
    }

    /**
     * 清理缓存接口
     */
    public function clearCache()
    {
        Cache::clear();
        $this->success('清理缓存成功');
    }

    function determinebrowser($Agent)
    {
        $browseragent   = ""; //浏览器
        $browserversion = ""; //浏览器的版本
        if (ereg('MSIE ([0-9].[0-9]{1,2})', $Agent, $version)) {
            $browserversion = $version[1];
            $browseragent   = "Internet Explorer";
        } else {
            if (ereg('Opera/([0-9]{1,2}.[0-9]{1,2})', $Agent, $version)) {
                $browserversion = $version[1];
                $browseragent   = "Opera";
            } else {
                if (ereg('Firefox/([0-9.]{1,5})', $Agent, $version)) {
                    $browserversion = $version[1];
                    $browseragent   = "Firefox";
                } else {
                    if (ereg('Chrome/([0-9.]{1,3})', $Agent, $version)) {
                        $browserversion = $version[1];
                        $browseragent   = "Chrome";
                    } else {
                        if (ereg('Safari/([0-9.]{1,3})', $Agent, $version)) {
                            $browseragent   = "Safari";
                            $browserversion = "";
                        } else {
                            $browserversion = "";
                            $browseragent   = "Unknown";
                        }
                    }
                }
            }
        }

        return $browseragent." ".$browserversion;
    }

}