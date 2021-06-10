<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-26
 * Time: 下午2:47:44
 * Info:
 */

namespace app\admin\model;

class AuthRule extends TimeModel
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

    /**
     * 根据角色ID获取授权节点
     *
     * @param $authId
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getAuthorizeNodeListByAdminId($authId)
    {
        $checkNodeList = (new AuthGroup())->where('id', $authId)->value('rules');
        $authRule      = new AuthRule();
        $nodelList     = $authRule->where('is_auth', 1)->field('id,node,title,type,is_auth')->select()->toArray();
        $newNodeList   = [];
        foreach ($nodelList as $vo) {
            if ($vo['type'] == 1) {
                $vo            = array_merge($vo, ['field' => 'node', 'spread' => true]);
                $vo['checked'] = false;
                $vo['title']   = "{$vo['title']}【{$vo['node']}】";
                $children      = [];
                foreach ($nodelList as $v) {
                    $v['checked'] = false;
                    if ($v['type'] == 2 && strpos($v['node'], $vo['node'].'/') !== false) {
                        $v = array_merge($v, ['field' => 'node', 'spread' => true]);
                        if ($checkNodeList != '*') {
                            $checkNodeArr = explode(',', $checkNodeList);
                            $v['checked'] = in_array($v['id'], $checkNodeArr) ? true : false;
                        }
                        $v['title'] = "{$v['title']}【{$v['node']}】";
                        $children[] = $v;
                    }
                }
                ! empty($children) && $vo['children'] = $children;
                $newNodeList[] = $vo;
            }
        }

        return $newNodeList;
    }

}