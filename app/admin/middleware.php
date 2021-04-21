<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-04-21
 * Time: 下午6:43:14
 * Info:
 */
return [
    // Session初始化
    \think\middleware\SessionInit::class,

    \app\admin\middleware\CheckAdmin::class,

    // 系统操作日志
    //\app\admin\middleware\SystemLog::class,
];