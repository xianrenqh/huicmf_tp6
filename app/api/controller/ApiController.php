<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 上午11:55:35
 * Info:
 */

namespace app\api\controller;

use think\App;
use think\facade\Request;

class ApiController extends \app\BaseController
{

    use \app\common\traits\JumpTrait;

    protected $token = '';

    protected $domain = '';

    protected $userId = 0;                        //用户id

    protected function initialize()
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        parent::initialize();
        $getKey = $this->request->header('set-key');
        //配置统一入口，只让访问init方法
        if (request()->module() != 'api' || request()->controller() != 'Index' || request()->action() != 'index') {
            if ( ! (request()->module() == 'api' && request()->controller() == 'Common')) {     //这个if是为了兼容api/Common控制器可以直接访问，为了向下兼容
                die('error');
            }

        }
    }

    public function __construct(App $app = null)
    {
        $this->request = Request::instance();
        $this->domain  = Request::instance()->domain();
    }

    //此方法用于设置参数
    public function setInit($user_id)
    {
        $this->userId = $user_id;

        return true;
    }

}