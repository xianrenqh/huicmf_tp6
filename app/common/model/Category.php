<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-01
 * Time: 下午6:09:41
 * Info:
 */

namespace app\common\model;

class Category extends TimeModel
{

    /**
     * 默认是否显示全部
     *
     * @param bool $type
     *
     */
    public function getPidMenuList($type = '')
    {
        $where       = function ($query) use ($type) {
            if ( ! empty($type)) {
                if (is_array($type)) {
                    $query->whereIn('type', $type);
                } else {
                    $query->where('type', $type);
                }
            }
        };
        $list        = $this->field('id,parent_id,cate_name')->where($where)->select()->toArray();
        $pidMenuList = $this->buildPidMenu(0, $list);

        return $pidMenuList;
    }

    protected function buildPidMenu($pid, $list, $level = 0)
    {
        $newList = [];
        foreach ($list as $vo) {
            if ($vo['parent_id'] == $pid) {
                $level++;
                foreach ($newList as $v) {
                    if ($vo['parent_id'] == $v['parent_id'] && isset($v['level'])) {
                        $level = $v['level'];
                        break;
                    }
                }
                $vo['level'] = $level;
                if ($level > 1) {
                    $repeatString    = "&nbsp;&nbsp;";
                    $markString      = str_repeat("{$repeatString}├{$repeatString}", $level - 1);
                    $vo['cate_name'] = $markString.$vo['cate_name'];
                }
                $newList[] = $vo;
                $childList = $this->buildPidMenu($vo['id'], $list, $level);
                ! empty($childList) && $newList = array_merge($newList, $childList);
            }

        }

        return $newList;
    }
}