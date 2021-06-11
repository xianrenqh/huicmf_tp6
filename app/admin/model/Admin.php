<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:54:48
 * Info:
 */

namespace app\admin\model;

use app\admin\controller\system\Auth;
use think\Model;

class Admin extends TimeModel
{

    public function getAuthList()
    {
        $list = (new AuthGroup())->where('status', 'normal')->column('name', 'id');

        return $list;
    }

}