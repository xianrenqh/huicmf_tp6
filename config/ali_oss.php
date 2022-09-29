<?php
/**
 * oss配置项
 */
return [
    'access_id'     => '',
    'access_secret' => '',
    'bucket'        => '',
    'end_point'     => '',
    'domain'        => '',  //不带域名前缀，例如：baidu.com
    'protocol'      => 'https', //http或者https

    // sts授权用信息
    'sts_id'        => '',
    'sts_key'       => '',
    'arn'           => '',
    'sts_expire'    => 900,// 已经是最小值
];
