<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-20
 * Time: 17:42:40
 * Info:
 */

namespace app\admin\controller;

use app\common\controller\Backend;
use \think\facade\View;

class IndexController extends Backend
{

    public function index()
    {
        echo "后台";
    }
}