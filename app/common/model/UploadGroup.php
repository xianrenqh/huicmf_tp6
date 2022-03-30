<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-29
 * Time: 上午9:40:08
 * Info:
 */

namespace app\common\model;

class UploadGroup extends TimeModel
{

    protected $pk = 'group_id';

    public static function group_list()
    {
        $list = UploadGroup::order('sort asc')->field('group_id,group_name')->select()->toArray();

        return $list;
    }

}