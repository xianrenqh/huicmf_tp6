<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-22
 * Time: 下午3:23:40
 * Info:
 */

namespace app\common\model;

use app\common\model\BannerType;

class Banner extends TimeModel
{

    public function getType()
    {
        $list = BannerType::select();

        return $list;
    }

}