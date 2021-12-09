<?php

return array(
    'display'  => array(
        'name'    => 'display',
        'title'   => '是否显示:',
        'type'    => 'radio',
        'options' => array(
            1 => '显示',
            0 => '不显示',
        ),
        'value'   => '0',
    ),
    'appid'    => array(
        'name'  => 'appid',
        'title' => '熊掌号appid',
        'type'  => 'text',
        'value' => '1111',
        'tip'   => '熊掌号请前往<a href="https://ziyuan.baidu.com/xzh/home/index" target="_blank">熊掌号平台</a>获取Appid和Token',
    ),
    'type'     => array(
        'name'    => 'type',
        'title'   => '开启同步登陆：',
        'type'    => 'checkbox',
        'options' => array(
            'qq'     => 'qq',
            'sian'   => 'sian',
            'weixin' => 'weixin',
        ),
        'value'   => 'sian,weixin',
    ),
    'textarea' => array(
        'name'  => 'textarea',
        'title' => '测试textarea：',
        'type'  => 'textarea',
        'value' => 'testdddddd',
        'tip'   => '测试textarea提示',
    ),
    'compress' => array(
        'name'    => 'compress',
        'title'   => '是否启用压缩',
        'type'    => 'select',
        'options' => array(
            1 => '启用压缩',
            0 => '不启用',
        ),
        'value'   => '0',
        'tip'     => '压缩备份文件需要PHP环境支持gzopen,gzwrite函数',
    ),
    'pic'      => array(
        'name'  => 'pic',
        'title' => '固定图片',
        'type'  => 'image',
        'value' => '',
        'tip'   => '选择固定则需要上传此图片',
    ),
);
