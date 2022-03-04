<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-18
 * Time: 下午2:06:54
 * Info:
 */

namespace app\common\model;

class SystemLog extends TimeModel
{

    public function admin()
    {
        return $this->belongsTo('app\common\model\Admin', 'admin_id', 'id');
    }
}