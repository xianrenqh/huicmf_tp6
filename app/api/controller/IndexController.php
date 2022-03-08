<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 上午11:54:57
 * Info:
 */

namespace app\api\controller;

use app\common\model\UserToken as UserTokenModel;

/**
 * @title      首页接口
 * @controller api\controller\Index
 * @group      base
 */
class IndexController extends ApiController
{

    protected $loginAction = [];

    protected $action = [];

    public function index()
    {

        //解决跨域问题
        /*
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
        */

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
            exit;
        }
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');

        $api    = config('api');
        $method = explode('.', input('param.method'));
        if (count($method) != 2 && count($method) != 3) {
            //method参数结构错误
            $this->error('method参数结构错误');
        }

        //如果有第三个参数，就是插件里的方法，走插件流程（未开发）
        if (count($method) == 3) {

        }
        if (isset($api[$method[0]])) {
            $cname = $api[$method[0]]['code'];
        } else {
            $this->error('method参数1不存在');
        }

        if (isset($api[$method[0]]['method'][$method[1]])) {
            $aname = $api[$method[0]]['method'][$method[1]]['code'];
        } else {
            $this->error('method参数2不存在');
        }

        //判断是否需要登陆
        if ($api[$method[0]]['method'][$method[1]]['is_login']) {
            $token = $this->request->param('token');
            if ( ! $token) {
                $this->error('请先登录');
            }
            $userTokenModel = new UserTokenModel();
            $result         = $userTokenModel->checkToken($token);

            if ( ! $result['code']) {
                $this->error('用户身份过期请重新登录');
            } else {
                $this->userId = $result['data']['user_id'];
            }
        } else {
            $this->userId = 0;
        }
        try {
            $cname = $cname.'Controller';
            $obj   = '\\app\\api\\controller\\'.$cname;
            $c     = new $obj();
            $c->setInit($this->userId);
        } catch (\Exception $e) {
            $this->error('操作失败，请重试1');
        }
        if ( ! method_exists($c, $aname)) {
            $this->error('操作失败，请重试2');
        }

        trace(input('param.'), 'api');
        $data = $c->$aname();
        trace($data, 'api');

        return $data;
    }
}