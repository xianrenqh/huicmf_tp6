<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-04
 * Time: 下午3:49:26
 * Info:
 */

namespace app\api\controller;

use app\common\controller\Api;

/**
 * old 此控制器不用api校验，不需要token登陆，就是单纯的标准的接口数据,用于取一些通用的信息
 * new 此控制器不校验token登陆但是最好也放到api体系了里面吧
 * Class Common
 * @package app\api\controller
 */
class CommonController extends ApiController
{

    /**
     * 加载方法
     */
    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
    }

}