<?php
return [
    'mode' => array(
        'name'    => 'mode',
        'title'   => '模式',
        'type'    => 'radio',
        'options' => array(
            'fixed'  => '固定',
            'random' => '每次随机',
            'daily'  => '每日切换',
        ),
        'value'   => 'random',
        'tip'     => '随机切换只支持七天内图片',
    ),
    'pic'  => array(
        'name'  => 'pic',
        'title' => '固定图片',
        'type'  => 'image',
        'value' => '',
        'tip'   => '选择固定则需要上传此图片',
    )
];