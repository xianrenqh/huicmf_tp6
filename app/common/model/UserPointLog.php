<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:41:25
 * Info:
 */

namespace app\common\model;

use app\common\model\User as UserModel;

class UserPointLog extends TimeModel
{

    const POINT_TYPE_SIGN = 1; //签到
    const POINT_TYPE_REBATE = 2; //消费返积分
    const POINT_TYPE_DISCOUNT = 3; //使用积分
    const POINT_TYPE_ADMIN_EDIT = 4; //后台编辑

    /**
     * @title 积分变动
     *
     * @param $user_id
     * @param $point
     * @param $type
     * @param $remarks
     *
     * @return void
     */
    public function setPoint($user_id, $point, $type, $remarks = '')
    {
        if ($point != 0) {
            $userModel = new UserModel();
            $user_info = $userModel->getUserInfo($user_id);
            $new_point = $user_info['data']['point'] + $point;
            //积分余额判断
            if ($new_point < 0) {
                return ['code' => 0, 'msg' => '当前剩余积分不足以抵扣此次的调整'];
            }
            //插入记录
            $data = [
                'user_id' => $user_id,
                'type'    => $type,
                'num'     => $point,
                'balance' => $new_point,
                'remarks' => $remarks,
            ];
            $this->create($data);
            //更新会员表
            $userModel->where(['id' => $user_id])->inc('point', $point)->update();
        }

        return ['code' => 200, 'msg' => '积分变动成功'];

    }

    /**
     * 获取积分记录
     *
     * @param      $user_id
     * @param bool $type
     * @param int  $page
     * @param int  $limit
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pointLogList($user_id, $page = 1, $limit = 20, $type = false, $params = [])
    {
        $return = [
            'code'  => 0,
            'msg'   => '获取失败',
            'data'  => [],
            'count' => 0,
            'total' => 0
        ];
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        if ($user_id) {
            $where[] = ['user_id', '=', $user_id];
        }

        /*if ($params) {
            if (isset($params['mobile']) && ! empty($params['mobile']) && ! $user_id) {
                $user_id_search = get_user_id($params['mobile']);
                if ($user_id_search) {
                    $where[] = ['user_id', 'eq', $user_id_search];
                }
            }
            if (isset($params['date']) && ! empty($params['date'])) {
                $date_string = $params['date'];
                $date_array  = explode(' 到 ', urldecode($date_string));
                $sdate       = strtotime($date_array[0].' 00:00:00');
                $edate       = strtotime($date_array[1].' 23:59:59');
                $where[]     = ['create_time', ['>=', $sdate], ['<=', $edate], 'and'];
            }

        }*/
        $first = ((int)$page - 1) * $limit;
        $res   = $this->field('id, type, num, balance, remarks, create_time,user_id')->where($where)->order('create_time',
            'desc')->limit($first, $limit)->select()->toArray();
        $count = $this->where($where)->count();

        if ($res) {
            $return['code'] = 200;
            $return['msg']  = '暂无积分记录';
            if ($count > 0) {
                $return['msg']  = '积分记录获取成功';
                $return['data'] = $res;
                foreach ($return['data'] as &$v) {
                    $v['username'] = '';
                    $v['type']     = '';
                }
            }
            $return['count'] = $count;
            $return['total'] = ceil($count / $limit);
        }

        return $return;
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
        $where           = function ($query) use ($post) {
            if ( ! empty($post['user_id'])) {
                $query->where('user_id', $post['user_id']);
            }
            if (isset($post['key']['type']) && $post['key']['type'] != "") {
                $query->where('type', $post['key']['type']);
            }
            if (isset($post['key']['time']) && $post['key']['time'] != "") {
                $date_array = explode(' 到 ', urldecode($post['key']['time']));
                $sdate      = strtotime($date_array[0].' 00:00:00');
                $edate      = strtotime($date_array[1].' 23:59:59');
                $query->whereBetweenTime('create_time', $sdate, $edate);
            }
        };
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id desc";

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

        }

        return $list;
    }
}