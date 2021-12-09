<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-22
 * Time: 下午3:23:40
 * Info:
 */

namespace app\admin\model;

use app\admin\model\BannerType;

class Banner extends TimeModel
{

    public function getType()
    {
        $list = BannerType::select();

        return $list;
    }

}