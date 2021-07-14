<?php
/**
 * Created by PhpStorm.
 * User: 投实科技
 * Date: 2021-07-12
 * Time: 16:22:21
 * Info:
 */

namespace addons\login_bg\controller;

class IndexController
{

    /**
     *获取bing背景图
     */
    public function getbing_bgpic_bak()
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
}