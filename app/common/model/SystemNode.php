<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午6:34:56
 * Info:
 */

namespace app\common\model;

class SystemNode extends TimeModel
{

    public function getNodeTreeList()
    {
        $list = $this->select()->toArray();
        $list = $this->buildNodeTree($list);

        return $list;
    }

    protected function buildNodeTree($list)
    {
        $newList      = [];
        $repeatString = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        foreach ($list as $vo) {
            if ($vo['type'] == 1) {
                $newList[] = $vo;
                foreach ($list as $v) {
                    if ($v['type'] == 2 && strpos($v['node'], $vo['node'].'/') !== false) {
                        $v['node'] = "{$repeatString}├{$repeatString}".$v['node'];
                        $newList[] = $v;
                    }
                }
            }
        }

        return $newList;
    }
}