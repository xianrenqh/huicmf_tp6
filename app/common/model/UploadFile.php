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

    protected $pk = 'file_id';

    public function getFildIds($fileUrl)
    {
        $fileIds = $this->whereIn('file_url', $fileUrl)->column('file_id');

        return $fileIds;
    }

    public function uploadGroup()
    {
        return $this->belongsTo("UploadGroup", 'group_id', 'group_id')->bind(['group_name' => 'group_name']);
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
        $tableWhere = $this->tableWhere($post);
        if ($isPage) {
            $list = $this->with([
                'uploadGroup'
            ])->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
            //$re['sql'] = $this->getLastSql();
            $data        = $this->tableFormat($list->getCollection()); //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = $list->total();
        } else {
            $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->select();
            if ( ! $list->isEmpty()) {
                $data = $this->tableFormat($list->toArray());
            }
            $re['count'] = count($list);
        }
        $re['code'] = 0;
        $re['msg']  = '';

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
            if (isset($post['group_id']) && $post['group_id'] != '') {
                $query->where('group_id', $post['group_id']);
            }
        };

        $orderBy   = ! empty($post['field']) ? $post['field'] : 'file_id';
        $orderType = ! empty($post['order']) ? $post['order'] : 'desc';

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
        foreach ($list as $k => $v) {
            $list[$k]['w_h']        = $v['img_width'].'*'.$v['img_height'];
            $list[$k]['file_size']  = sizecount($list[$k]['file_size']);
            $list[$k]['extension']  = build_suffix_image($list[$k]['extension'], null, '', 28);
            $list[$k]['group_name'] = ! empty($list[$k]['group_name']) ? $list[$k]['group_name'] : '默认';
        }

        return $list;
    }

    /**
     * @title 删除图片（仅删除数据库，不删除图片附件）
     *
     * @param $userId
     *
     * @return void
     */
    public function manageDel($fileId)
    {
        $result = [
            'msg'  => '',
            'code' => 0
        ];

        $this->destroy($fileId, true);
        $result['msg']  = '删除成功';
        $result['code'] = 200;

        return $result;
    }

}