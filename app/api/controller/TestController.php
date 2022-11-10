<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-11-10
 * Time: 10:22:35
 * Info:
 */

namespace app\api\controller;

use think\facade\Db;

class TestController extends BaseController
{

    public function index()
    {
        $this->success('这是免验证的');
    }

    public function test()
    {
        $this->success('这个是需要验证的哦');
    }

}