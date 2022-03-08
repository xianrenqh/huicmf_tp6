<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-08
 * Time: 下午3:29:15
 * Info:
 */

namespace app\common\model;

class Hooks extends TimeModel
{

    /**
     * 根据id获取数据
     *
     * @param $id
     *
     * @return array
     */
    public function getDataInfo($id)
    {
        $result = $this->find($id);

        return $result;
    }

    /**
     * 添加数据
     * @return void
     */
    public function manageAdd($param)
    {
        $return = [
            'code' => 0,
            'msg'  => '',
            'data' => ''
        ];
        if ( ! empty($param['name'])) {
            //判断用户名是否存在
            $findName = $this->where('name', $param['name'])->find();
            if ( ! empty($findName)) {
                $return['msg'] = '钩子名称已存在';

                return $return;
            }
            $result = $this->create($param);
            if ($result) {
                $return['code'] = 200;
                $return['msg']  = '添加成功';
            } else {
                $result['msg'] = '添加失败';
            }

            return $return;
        }
    }

    /**
     * 修改数据
     * @return void
     */
    public function manageEdit($param)
    {
        $where  = ['id' => $param['id']];
        $result = $this->update($param, $where);
        if ($result) {
            return ['code' => 200, 'msg' => '修改成功'];
        } else {
            return ['code' => 0, 'msg' => '修改失败'];
        }
    }

    /**
     * 删除数据
     * @return void
     */
    public function manageDel($id)
    {
        $result   = [
            'msg'  => '',
            'code' => 0
        ];
        $dataInfo = $this->getDataInfo($id);
        if (empty($dataInfo)) {
            $result['msg'] = '钩子信息不存在';

            return $result;
        }

        $this->destroy($id);
        $result['msg']  = '删除成功';
        $result['code'] = 200;

        return $result;
    }

    /**
     * 返回layui的table所需要的格式
     *
     * @param $post
     *
     * @return mixed
     * @author sin
     */
    public function tableData($post, $isPage = true)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = self::PAGE_LIMIT;
        }
        $tableWhere  = $this->tableWhere($post);
        $list        = $this->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $re['sql']   = $this->getLastSql();
        $data        = $this->tableFormat($list->getCollection()); //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['count'] = $list->total();
        $re['code']  = 0;
        $re['msg']   = '';

        $re['data'] = $data;

        return json($re);
    }

    /**
     * @param $post
     *
     * @return array|mixed|void
     */
    protected function tableWhere($post)
    {
        $where = function ($query) use ($post) {
            if (isset($post['key']['type']) && $post['key']['type'] != "") {
                $query->where('type', $post['key']['type']);
            }
            if (isset($post['key']['name']) && $post['key']['name'] != "") {
                $query->where('name', 'like', '%'.$post['key']['name'].'%')->whereOr('description', 'like',
                    '%'.$post['key']['name'].'%');
            }
        };

        $orderBy   = ! empty($post['field']) ? $post['field'] : 'id';
        $orderType = ! empty($post['order']) ? $post['order'] : 'asc';

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "{$orderBy} {$orderType}";

        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     *
     * @param $list
     *
     * @return mixed|void
     */
    protected function tableFormat($list)
    {
        return $list;
    }

}