<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-02
 * Time: 上午10:40:48
 * Info:
 */

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class TimeModel extends Model
{

    const PAGE_LIMIT = 15;

    /**
     * 自动时间戳类型
     * @var string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 添加时间
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 软删除
     */
    use SoftDelete;

    protected $defaultSoftDelete = 0;

    protected $deleteTime = false;

    /**
     * 返回layui的table所需要的格式
     *
     * @param $post
     *
     * @return mixed
     * @author sin
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = self::PAGE_LIMIT;
        }
        $tableWhere  = $this->tableWhere($post);
        $list        = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data        = $this->tableFormat($list->getCollection());//返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

        return json($re);
    }

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
        $where = function ($query) use ($post) {
        };

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];

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
        return $list;
    }

}