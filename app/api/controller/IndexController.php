<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-30
 * Time: 14:35:23
 * Info:
 */

namespace app\api\controller;

/**
 * @title      首页接口
 * @controller api\controller\Index
 * @group      base
 */
class IndexController extends BaseController
{

    /**
     * @title  发送验证码
     * @desc   最基础的接口注释写法
     *
     * @param name:email type:string require:1 desc:邮箱
     * @param name:event type:string require:1 desc:事件名称
     *
     * @return name:data type:array ref:definitions\dictionary
     * @author 御宅男
     * @url    /api/index/index
     * @method POST
     * @tag    邮箱|验证码
     */
    public function index()
    {
        $this->success('ok');
    }

    public function test()
    {

    }

}