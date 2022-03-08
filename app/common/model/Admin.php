<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:54:48
 * Info:
 */

namespace app\common\model;

use app\admin\controller\system\Auth;
use think\Model;

class Admin extends TimeModel
{

    public function getAuthList()
    {
        $list = (new AuthGroup())->where('status', 'normal')->column('name', 'id');

        return $list;
    }

    /**
     * 获取管理员总数
     */
    public function getNums()
    {
        $total = $this->count();

        return $total;
    }

    /**
     * 根据id获取管理员信息
     */
    public function getAdminInfo($admin_id)
    {
        $info = [];
        if (empty($admin_id)) {
            return $info;
        }
        $info = $this->find($admin_id);

        return $info;
    }

}