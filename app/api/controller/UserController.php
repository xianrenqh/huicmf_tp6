<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 下午2:20:07
 * Info:
 */

namespace app\api\controller;

class UserController extends ApiController
{

    /**
     * 登陆
     * @return array|mixed
     */
    public function login()
    {
        $data = $this->request->param();
        halt($data);
    }
}