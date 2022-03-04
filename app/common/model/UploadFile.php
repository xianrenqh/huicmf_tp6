<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-29
 * Time: 下午12:07:22
 * Info:
 */

namespace app\common\model;

class UploadFile extends TimeModel
{

    public function getFildIds($fileUrl)
    {
        $fileIds = $this->whereIn('file_url', $fileUrl)->column('file_id');

        return $fileIds;
    }

}