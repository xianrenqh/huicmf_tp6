<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

require __DIR__.'/../vendor/autoload.php';

// 定义目录分隔符
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__.DS.'..'.DS);
define('HUICMF_VERSION', '1.0');
define('HUICMF_ADDON_PATH', ROOT_PATH.'/addons/');

// 执行HTTP应用并响应
$http     = (new  App())->http;
$response = $http->name('admin')->run();
$response->send();
$http->end($response);