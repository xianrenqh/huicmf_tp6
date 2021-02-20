<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-02-19
 * Time: 17:49:23
 * Info:
 */

namespace think;

require __DIR__.'/../vendor/autoload.php';

// 执行HTTP应用并响应
$http     = (new  App())->http;
$response = $http->name('admin')->run();
$response->send();
$http->end($response);