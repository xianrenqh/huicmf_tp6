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
use think\facade\Env;
use think\captcha\facade\Captcha;

class LoginController extends AdminController
{

    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if ( ! empty(session('admin')) && ! in_array($action, ['out'])) {
            $adminModuleName = config('app.admin_alias_name');
            $this->success('已登录，无需再次登录', [], url($adminModuleName));
        }
    }

    public function index()
    {
        halt(phpinfo());
        $bg = self::getbing_bgpic();
        $this->assign('bg', $bg);

        return $this->fetch();
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
     * 验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return Captcha::create();
    }
}