<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-14
 * Time: 下午4:06:36
 * Info:
 */

namespace addons\loginbg;

use think\App;
use think\Addons;

class Loginbg extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的login_bg钩子方法
     * @return mixed
     */
    public function admin_login_style($param)
    {
        $config = $this->getConfig();
        if ( ! empty($config) && ($config['mode'] == 'random' || $config['mode'] == 'daily')) {
            $getTime    = $config['mode'] == 'random' ? mt_rand(-1, 7) : 0;
            $background = $this->getbing_bgpic($getTime);
        } else {
            $background = $config['pic'];
        }
        $bg = "background: url('{$background}') 50% 50% / cover;";

        return $bg;
    }

    /**
     *获取bing背景图
     */
    private function getbing_bgpic($getTime = 0)
    {
        $api     = "https://www.bing.com/HPImageArchive.aspx?format=js&idx=$getTime&n=1";
        $data    = json_decode(get_url($api), true);
        $pic_url = $data['images'][0]['url']; //获取数据里的图片地址
        if ($pic_url) {
            $images_url = "https://www.bing.com".$pic_url."_1920x1080.jpg";//如果图片地址存在，则输出图片地址
        } else {
            $images_url = "https://s1.ax1x.com/2018/12/10/FGbI81.jpg";
        }

        return $images_url;

    }

}