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

    protected $deleteTime = 'delete_time';

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
            'code' => 0,
            'data' => '',
            'msg'  => ''
        ];
        if ( ! $token) {
            $result['msg'] = '请先登录';

            return $result;
        }
        $tokenInfo = $this->where(['token' => $token, 'delete_time' => 0])->cache(true)->order('id desc')->find();
        if ( ! empty($tokenInfo)) {
            //密码有效期半年
            if (strtotime($tokenInfo['create_time']) < time() - 60 * 60 * 24 * 180) {
                $result['msg'] = '密码过期了';

                return $result;
            }
            $userModel = new UserModel();
            $userInfo  = $userModel->where(['id' => $tokenInfo['user_id']])->find();
            if ( ! $userInfo) {
                $result['msg'] = '没有找到此用户';

                return $result;
            }
            if ($status == 1 && $userInfo['status'] != 1) {
                $result['msg'] = '账号已停用';

                return $result;
            }
            $result['code'] = 200;
            $result['data'] = $tokenInfo;

            return $result;
        } else {
            $result['msg'] = '不是有效的token';

            return $result;
        }
    }

    /**
     * 登陆存token
     *
     * @param     $user_id
     * @param int $platform //1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc
     *
     * @return array
     */
    public function setToken($user_id, $platform = 1)
    {
        $result    = [
            'code' => 0,
            'data' => '',
            'msg'  => ''
        ];
        $userModel = new UserModel();
        $userInfo  = $userModel->where(array('id' => $user_id))->find()->toArray();
        if (empty($userInfo)) {
            $result['msg'] = '没有找到此账户';

            return $result;
        }
        if ($userInfo['status'] == $userModel::STATUS_DISABLE) {
            $result['msg'] = '账号已停用';

            return $result;
        }
        $data['user_id']     = $user_id;
        $data['platform']    = $platform;
        $data['create_time'] = time();
        $data['token']       = $this->algorithm($userInfo['id'], $userInfo['password'], $platform,
            $data['create_time']);
        $re                  = $this->save($data);
        if ($re) {
            $this->where([
                ['user_id', '=', $user_id],
                ['platform', '=', $platform],
                ['token', '<>', $data['token']]
            ])->data(['delete_time' => time()])->update();
            $result['data'] = ['token' => $data['token'], 'user_info' => $userInfo];
            $result['code'] = 200;
            $result['msg']  = '获取成功';

            //如果需要绑定手机号码，但是用户的手机号码为空，那么就需要绑定手机号码

            return $result;
        } else {
            $result['msg'] = '写入user_token表数据失败';

            return $result;
        }
    }

    private function algorithm($user_id, $password, $platform, $createtime)
    {
        return md5(md5($user_id.$password.$platform.$createtime).rand(1, 10000));
    }

}