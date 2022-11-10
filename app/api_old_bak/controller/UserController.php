<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 下午2:20:07
 * Info:
 */

namespace app\api\controller;

use hg\apidoc\annotation as Apidoc;
use app\common\model\User as UserModel;
use app\common\model\UserPointLog as UserPointLogModel;

/**
 * @Apidoc\Title("会员接口")
 * @Apidoc\Group("base")
 * @Apidoc\Sort(0)
 */
class UserController extends ApiController
{

    /**
     * @Apidoc\Title("会员登录")
     * @Apidoc\Desc("最基础的接口注释写法")
     * @Apidoc\Method("POST")
     * @Apidoc\Url("/api.html")
     * @Apidoc\Author("灰灰")
     * @Apidoc\Tag("登录")
     * @Apidoc\Param("method", default="user.login",type="string",require=true, desc="用户名")
     * @Apidoc\Param("username", type="abc",require=true, desc="用户名")
     * @Apidoc\Param("password", type="string",require=true, desc="密码")
     * @Apidoc\Returned("id", type="int", desc="用户id")
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