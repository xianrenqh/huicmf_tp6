<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:41:11
 * Info:
 */

namespace app\common\model;

class UserLog extends TimeModel
{

    const USER_LOGIN = 1;     //登录
    const USER_LOGOUT = 2;    //退出
    const USER_REG = 3;    //注册
    const USER_EDIT = 4;    //用户编辑信息

    const USER_TYPE = 1;//用户类型，会员
    const MANAGE_TYPE = 2;//用户类型，管理员

    /**
     *  添加日志
     * User:tianyu
     *
     * @param        $user_id
     * @param string $state
     */
    public function setLog($user_id, $state, $data = [], $type = self::USER_TYPE)
    {

        $data = [
            'user_id' => $user_id,
            'state'   => $state,
            'ctime'   => time(),
            'params'  => json_encode($data),
            'ip'      => get_client_ip(0, true),
            'type'    => $type
        ];
        $this->save($data);

    }
}