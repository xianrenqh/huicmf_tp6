<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:40:17
 * Info:
 */

namespace app\common\model;

use app\common\model\UserGrade as UserGradeModel;
use app\common\model\UserToken as UserTokenModel;
use app\common\model\UserLog as UserLogModel;
use lib\Random;

class User extends TimeModel
{

    protected $deleteTime = 'delete_time';

    const STATUS_NORMAL = 1;        //用户状态 正常
    const STATUS_DISABLE = 2;       //用户状态 停用

    public function grade()
    {
        return $this->hasOne("UserGrade", 'id', 'grade')->bind(['grade_name' => 'name']);
    }

    /**
     * 用户账户密码登陆
     *
     * @param array $data      用户登陆信息
     * @param int   $loginType 1就是默认的，存session，2就是返回user_token
     * @param int   $platform  平台id，主要和session有关系 1就是默认的平台，，2就是微信小程序平台，当需要放回user_token的时候，会用到此字段
     *
     */
    public function toLogin($data, $loginType = 1, $platform = 1)
    {
        $result = [
            'code' => 0,
            'msg'  => '',
            'data' => ''
        ];

        if (empty($data['mobile']) || empty($data['password'])) {
            $result['msg'] = '用户名手机号或者密码不能为空';

            return $result;
        }
        //校验验证码

        //查询会员信息
        $userInfo = $this->where(['username' => $data['mobile']])->whereOr(['mobile' => $data['mobile']])->find();
        if (empty($userInfo)) {
            $result['msg'] = '没有找到此账号';

            return $result;
        }

        //验证密码
        $userPassword = $userInfo['password'];
        $postPass     = cmf_password($data['password'], $userInfo['salt']);
        if ($userPassword != $postPass) {
            $result['msg'] = '密码错误，请重试';

            return $result;
        }
        if ($userInfo['status'] == self::STATUS_DISABLE) {
            $result['msg'] = '该账号已停用';

            return $result;
        }
        $result = $this->setSession($userInfo, $loginType, $platform);//根据登陆类型，去存session，或者是返回user_token

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
        $tableWhere = $this->tableWhere($post);
        if ($isPage) {
            $list = $this->with([
                'grade'
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
            if (isset($post['key']['sex']) && $post['key']['sex'] != "") {
                $query->where('sex', $post['key']['sex']);
            }
            if (isset($post['key']['mobile']) && $post['key']['mobile'] != "") {
                $query->where('mobile', $post['key']['mobile'])->whereOr('username', $post['key']['mobile']);
            }
            if (isset($post['key']['status']) && $post['key']['status'] != "") {
                $query->where('status', $post['key']['status']);
            }
            if (isset($post['key']['grade']) && $post['key']['grade'] != "") {
                $query->where('grade', $post['key']['grade']);
            }
        };

        $orderBy   = ! empty($post['field']) ? $post['field'] : 'id';
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
            if ($v['sex']) {
                $list[$k]['sex']      = config('params.user')['sex'][$v['sex']];
                $list[$k]['birthday'] = date('Y-m-d', $list[$k]['birthday']);
            }
        }

        return $list;
    }

    /**
     * 后台添加用户
     *
     * @param $data
     *
     * @return array
     */
    public function manageAdd($param)
    {
        if ( ! empty($param['username'])) {
            //判断用户名是否存在
            $findUsername = $this->where('username', $param['username'])->find();
            if ( ! empty($findUsername)) {
                return ['code' => 0, 'msg' => '用户名已存在'];
            }
        }
        if ( ! empty($param['mobile'])) {
            //判断手机号是否存在
            $flag = $this->checkUserByMobile($param['mobile']);
            if ($flag) {
                return ['code' => 0, 'msg' => '手机号已经存在，请更换手机号重新添加'];
            }
        }
        if ( ! empty($param['password'])) {
            $salt              = Random::alnum();
            $param['salt']     = $salt;
            $param['password'] = cmf_password($param['password'], $salt);
        }
        //默认用户等级
        if (empty($param['grade'])) {
            $userGradeModel = new UserGradeModel();
            $gradeInfo      = $userGradeModel->where('is_def', '1')->find();
            if ($gradeInfo) {
                $param['grade'] = $gradeInfo['id'];
            } else {
                $param['grade'] = 0;
            }
        }
        if ( ! empty($param['birthday'])) {
            $param['birthday'] = strtotime($param['birthday']);
        }
        $result = $this->create($param);
        if ($result) {
            return ['code' => 200, 'msg' => '添加成功'];
        } else {
            return ['code' => 0, 'msg' => '添加失败'];
        }

    }

    /**
     * @title 后台编辑会员
     *
     * @param $param
     *
     * @return void
     */
    public function manageEdit($param)
    {
        //密码不为空的时候修改密码
        if ( ! empty($param['password'])) {
            if ($param['password'] !== $param['password2']) {
                return ['code' => 0, 'msg' => '两次密码输入不一致'];
            }
            $salt              = Random::alnum();
            $param['salt']     = $salt;
            $param['password'] = cmf_password($param['password'], $salt);
        } else {
            unset($param['password']);
        }
        if ( ! empty($param['birthday'])) {
            $param['birthday'] = strtotime($param['birthday']);
        }
        $where  = ['id' => $param['id']];
        $result = $this->update($param, $where);
        if ($result) {
            return ['code' => 200, 'msg' => '修改成功'];
        } else {
            return ['code' => 0, 'msg' => '修改失败'];
        }
    }

    /**
     * @title 软删除会员
     *
     * @param $userId
     *
     * @return void
     */
    public function manageDel($userId)
    {
        $result = [
            'msg'  => '',
            'code' => 0
        ];
        if ( ! is_array($userId)) {
            $userInfo = $this->getUserInfo($userId);
            if (empty($userInfo['data'])) {
                $result['msg'] = '会员信息不存在';

                return $result;
            }
        }

        $this->destroy($userId, false);
        $result['msg']  = '删除成功';
        $result['code'] = 200;

        return $result;
    }

    /**
     * 获取用户id
     *
     * @param $userId
     *
     * @return void
     */
    public function getUserInfo($userId)
    {
        $result   = [
            'code' => 0,
            'data' => [],
            'msg'  => ''
        ];
        $userInfo = $this::with("grade")->field('*')->where(array('id' => $userId))->find();
        if ( ! empty($userInfo)) {
            $userInfo['birthday'] = date('Y-m-d', $userInfo['birthday']);
            $result['data']       = $userInfo;
            $result['code']       = 200;
        } else {
            $result['msg'] = '获取会员信息失败';
        }

        return $result;
    }

    /**
     * @title 根据用户手机号获取用户id
     *
     * @param $mobile
     *
     * @return void
     */
    public function checkUserByMobile($mobile)
    {
        $res = $this->where('mobile', $mobile)->value('id');

        return $res;
    }

    /**
     *设置用户登录信息或者更新用户登录信息
     *
     * @param     $userInfo
     * @param     $data
     * @param     $loginType 登陆类型1是存session，主要是商户端的登陆和网页版本的登陆,2就是token
     * @param int $platform  1就是普通的登陆，主要是vue登陆，2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc，写这个是为了保证h5端和小程序端可以同时保持登陆状态
     * @param int $type      1的话就是登录,2的话就是更新
     *
     * @return array
     */
    public function setSession($userInfo, $loginType, $platform = 1, $type = 1)
    {
        $result   = [
            'code' => 0,
            'data' => '',
            'msg'  => ''
        ];
        $userInfo = $userInfo->toArray();
        //判断账号状态
        if ($userInfo['status'] != self::STATUS_NORMAL) {
            $result['msg'] = '该账号已停用';

            return $result;
        }
        switch ($loginType) {
            case 1:
                session('user', $userInfo);
                $result['code'] = 200;
                break;
            case 2:
                $userTokenModel = new UserTokenModel();
                $result         = $userTokenModel->setToken($userInfo['id'], $platform);
                break;
        }

        if ($type == 1) {
            //添加登录日志
            $userLogModel = new UserLogModel();
            $userLogModel->setLog($userInfo['id'], $userLogModel::USER_LOGIN, ['会员登录成功']);
        }

        return $result;
    }

    /**
     * 获取会员总是
     */
    public function getNums(){
        $total = $this->count();
        return $total;
    }

}