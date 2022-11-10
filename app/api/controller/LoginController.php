<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-11-10
 * Time: 10:43:01
 * Info: 登录控制器
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
class LoginController extends BaseController
{

    /**
     * @Apidoc\Title("会员登录")
     * @Apidoc\Desc("最基础的接口注释写法")
     * @Apidoc\Method("POST")
     * @Apidoc\Url("/api/login/index")
     * @Apidoc\Author("灰灰")
     * @Apidoc\Tag("登录")
     * @Apidoc\Param("mobile", type="string",require=true, desc="用户名")
     * @Apidoc\Param("password", type="string",require=true, desc="密码")
     * @Apidoc\Returned("id", type="int", desc="用户id")
     */
    public function index()
    {
        $platform  = $this->request->param('platform', 1); //1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc
        $userModel = new UserModel();
        $data      = $this->request->param();

        $res        = $userModel->toLogin($data, 2, $platform);
        $ip_address = get_client_ip(0, true);// 获取当前请求的IP地址
        //这里可以写入登录记录【未开发】

        if ( ! empty($res['code']) && $res['code'] == 200) {
            $user  = $res['data']['user_info'];
            $token = $res['data']['token'];
            // 更新token信息和用户信息
            $token = $this->createTokenAndUpdateTime($token, $user, $ip_address);

            if ( ! $token) {
                $this->error("登录失败,请重试");
            }

            $this->success($res['msg'], $res['data']);
        } else {
            $this->error($res['msg']);
        }
    }

}