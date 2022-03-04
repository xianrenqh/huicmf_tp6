<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:40:58
 * Info:
 */

namespace app\common\model;

class UserGrade extends TimeModel
{

    const IS_DEF_YES = 1; //默认
    const IS_DEF_NO = 2; //不默认

    /**
     * 根据输入的查询条件，返回所需要的where
     *
     * @param $post
     *
     * @return mixed
     * @author sin
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = "id asc";

        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     *
     * @param $list
     *
     * @return mixed
     * @author sin
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['is_def'] == self::IS_DEF_YES) {
                $list[$k]['is_def'] = "默认";
            } else {
                $list[$k]['is_def'] = "";
            }
        }

        return $list;
    }

    /**
     * 后台添加
     *
     * @param $data
     *
     * @return array
     */
    public function manageAdd($param)
    {
        if ( ! empty($param['name'])) {
            //判断用户名是否存在
            $findGradeName = $this->where('name', $param['name'])->find();
            if ( ! empty($findGradeName)) {
                return ['code' => 0, 'msg' => '等级已存在'];
            }
        }
        $result = $this->create($param);
        if ($result) {
            return ['code' => 200, 'msg' => '添加成功'];
        } else {
            return ['code' => 0, 'msg' => '添加失败'];
        }

    }

    /**
     * @title 后台编辑
     *
     * @param $param
     *
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
     * @title 删除等级
     *
     * @param $id
     *
     * @return void
     */
    public function manageDel($id)
    {
        $result    = [
            'msg'  => '',
            'code' => 0
        ];
        $gradeInfo = $this->getGradeInfo($id);
        if (empty($gradeInfo)) {
            $result['msg'] = '等级信息不存在';

            return $result;
        }

        $this->destroy($id, true);
        $result['msg']  = '删除成功';
        $result['code'] = 200;

        return $result;
    }

    /***
     * 获取全部会员等级
     * @return array
     */
    public function getAll()
    {
        $data = $this->order('id asc')->select();
        if ( ! $data->isEmpty()) {
            return $data->toArray();
        }

        return [];
    }

    /***
     * 根据id获取等级信息
     * @return array
     */
    public function getGradeInfo($id)
    {
        $data = $this->where('id', $id)->find();
        if ( ! $data->isEmpty()) {
            return $data->toArray();
        }

        return [];
    }

}