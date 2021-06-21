<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午5:56:00
 * Info:
 */

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\facade\Cookie;
use think\facade\Env;
use think\captcha\facade\Captcha;
use app\admin\model\Admin as AdminModel;
use think\facade\Session;

class LoginController extends AdminController
{

    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if ( ! empty(session('admin')) && ! in_array($action, ['out'])) {
            $this->success('您已登录，无需再次登录', [], __url('admin/index/index'));
        }
    }

    public function index()
    {
        $bg = self::getbing_bgpic();
        $this->assign('bg', $bg);

        return $this->fetch();
    }

    public function check_login()
    {
        $param = $this->request->param();

        if ( ! captcha_check($param['captcha'])) {
            $this->error('验证码不正确');
        }
        if (empty($param['username'])) {
            $this->error('用户名不能为空');
        }
        if (empty($param['password'])) {
            $this->error('密码不能为空');
        }
        if (empty($param['captcha'])) {
            $this->error('验证码不能为空');
        }

        $adminInfo = AdminModel::where('username', $param['username'])->find();
        if (empty($adminInfo)) {
            $this->error('用户名或密码不正确！');
        }
        $password = cmf_password($param['password'], $adminInfo['salt']);
        if ($password != $adminInfo['password']) {
            $login_failure_retry = Env::get('huiadmin.login_failure_retry');
            $login_failure_times = Env::get('huiadmin.login_failure_times');
            $login_failure_min   = Env::get('huiadmin.login_failure_min');
            AdminModel::where('username', $param['username'])->inc('login_failure')->update();
            if ($login_failure_retry && $adminInfo['login_failure'] >= $login_failure_times && (time() - $adminInfo['updatetime']) < $login_failure_min * 60) {
                $this->error('密码错误次数超过'.$login_failure_times.'次，请'.$login_failure_min.'分钟之后重试！');
            }
            $this->error('用户名或密码不正确！！！');
        }
        if ($adminInfo['status'] != 'normal') {
            $this->error('该账号已被禁用');
        }
        $adminInfo->login_num    += 1;
        $adminInfo->loginip      = get_client_ip();
        $adminInfo->logintime    = time();
        $adminInfo->loginfailure = 0;
        $adminInfo->save();

        $adminInfo                = $adminInfo->toArray();
        $adminInfo['expire_time'] = ( ! empty($param['keep_login']) && $param['keep_login'] == 'on') ? true : time() + 3600 * 2;
        unset($adminInfo['password']);
        session('admin', $adminInfo);
        $this->success('登录成功');
    }

    /**
     *获取bing背景图
     */
    public function getbing_bgpic()
    {
        $idx     = $this->request->param('idx', 0);
        $api     = "https://cn.bing.com/HPImageArchive.aspx?format=js&idx=$idx&n=1";
        $data    = json_decode(get_url($api), true);
        $pic_url = $data['images'][0]['url']; //获取数据里的图片地址
        if ($pic_url) {
            $images_url = "https://cn.bing.com/".$pic_url;      //如果图片地址存在，则输出图片地址
        } else {
            $images_url = "https://s1.ax1x.com/2018/12/10/FGbI81.jpg";     //否则输入一个自定义图
        }

        return $images_url;

    }

    /**
     * 退出登录
     */
    public function out()
    {
        if ($this->request->isPost()) {
            session('admin', null);
            $this->success('退出登录成功');
        }
    }

    /**
     * 验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return Captcha::create();
    }

}