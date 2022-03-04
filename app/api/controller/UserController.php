<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 下午2:20:07
 * Info:
 */

namespace app\api\controller;

/**
 * @title      会员接口
 * @controller app\api\controller\User
 * @group      base
 */
class UserController extends ApiController
{

    /**
     * @title  会员登陆
     * @desc   最基础的接口注释写法
     *
     * @param name:method type:string require:1 desc:必须
     * @param name:event type:string require:1 desc:事件名称
     *
     * @author 小灰灰
     * @url    /api.html
     * @method POST
     * @tag    登陆
     */
    public function login()
    {
        $data = $this->request->param();
        halt($data);
    }
}