<?php
/**
 * 接口api类配置项
 */
return [

    //是否删除旧的未失效的token
    'OLD_TOKEN_DELETE'     => false,

    //全局不做验证的类
    'NO_CHECK_CLASS'       => [
        'Login',
        'Index'
    ],

    /**
     * 控制器中不做验证的方法名
     * 【键名】 控制器名
     * 【方法名】控制器名中的方法名
     * 例如：'Test' => 'index'
     */
    'NO_CHECK_ACTION'      => [
        'Test' => 'index'
    ],

    //允许的设备类型
    'ALLOWED_DEVICE_TYPES' => [
        'android',
        'ios',
        'wechat',
        'h5'
    ],

];