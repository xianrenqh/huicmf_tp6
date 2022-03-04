<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:41:36
 * Info:
 */

namespace app\common\model;

use app\common\model\User as UserModel;

class UserToken extends TimeModel
{

    /**
     * 根据token来获取用户id
     *
     * @param $token
     * @param $status
     *
     * @return void
     */
    public function checkToken($token, $status = 1)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        if ( ! $token) {
            return error_msg('请先登录');
        }
        $tokenInfo = $this->where(['token' => $token])->cache(true)->find();
        if ($tokenInfo) {
            //密码有效期半年
            if ($tokenInfo['ctime'] < time() - 60 * 60 * 24 * 180) {
                return error_msg('密码过期了');
            }
            $userModel = new UserModel();
            $userInfo  = $userModel->where(['id' => $tokenInfo['user_id']])->find();
            if ( ! $userInfo) {
                return error_msg('没有找到此用户');
            }
            if ($status == 1 && $userInfo['status'] != 1) {
                return error_msg('账号已停用');
            }
            $result['status'] = true;
            $result['data']   = $tokenInfo;

            return $result;
        } else {
            return error_msg('不是有效的token');
        }
    }

}