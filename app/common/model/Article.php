<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-24
 * Time: 16:05:02
 * Info:
 */

namespace app\common\model;

class Article extends TimeModel
{

    protected $deleteTime = 'delete_time';

    /**
     * 获取总文章数
     * @return void
     */
    public function getNums()
    {
        $total = $this->count();

        return $total;
    }

}