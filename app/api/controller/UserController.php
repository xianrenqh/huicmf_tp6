<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 下午2:20:07
 * Info:
 */

namespace app\api\controller;

use app\common\model\User as UserModel;
use app\common\model\UserPointLog as UserPointLogModel;

/**
 * @title      会员接口
 * @controller api\controller\User
 * @group      base
 */
class UserController extends ApiController
{

    /**
     * @title  会员登陆
     * @desc   最基础的接口注释写法
     *
     * @param name:method type:string require:1 default:user.login desc:接口方法
     * @param name:event type:string require:1 desc:事件名称
     *
     * @return name:code type:int default:0 desc:错误码
     * @return name:msg type:string desc:提示信息
     * @return name:data type:string desc:返回的数据 default:
     * @author 小灰灰
     * @url    /api.html
     * @method POST
     * @tag    登陆
     */
    public function login()
    {
        $platform  = $this->request->param('platform', 1); //1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc
        $userModel = new UserModel();
        $data      = $this->request->param();

        $res = $userModel->toLogin($data, 2, $platform);
        if ( ! empty($res['code']) && $res['code'] == 200) {
            $this->success($res['msg'], $res['data']);
        } else {
            $this->error($res['msg']);
        }
    }

    public function user_point()
    {
        $user_id      = $this->userId;
        $userPointLog = new UserPointLogModel();
        $params       = $this->request->param();
        $page         = $this->request->param('page', 1);
        $limit        = $this->request->param('limit', 20);
        $res          = $userPointLog->pointLogList($user_id, $page, $limit, $type = false, $params = []);
        if ( ! empty($res['code']) && $res['code'] == 200) {
            $this->success($res['msg'], $res);
        } else {
            $this->error($res['msg']);
        }
    }

}