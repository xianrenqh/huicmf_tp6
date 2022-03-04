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
        $data           = $this->request->param();
        $data['return'] = 'test';

        return $data;
    }
}