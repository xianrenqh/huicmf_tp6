<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-05
 * Time: 下午3:29:20
 * Info:
 */

namespace app\common\model;

use think\facade\Db;

class Tag extends TimeModel
{

    public function tag_dispose($catid, $tags, $aid)
    {
        Db::name('tag_content')->where(['aid' => $aid])->delete();
        $tags = array_unique($tags);
        foreach ($tags as $v) {
            if ( ! $v) {
                continue;
            }
            $row = Db::name('tag')->where(['tag' => $v])->find();
            if ( ! empty($row)) {
                $tagid = $row['id'];
                Db::name('tag')->where(['id' => $tagid])->inc('total')->update();
            } else {
                $tagid = Db::name('tag')->insertGetId([
                    'tag'         => $v,
                    'total'       => 1,
                    'create_time' => time()
                ]);
            }
            Db::name('tag_content')->data([
                'catid' => $catid,
                'tagid' => $tagid,
                'aid'   => $aid
            ])->insert();
        }
    }

}