<?php
// 应用公共文件
use think\Db;

/**
 * 返回带协议的域名
 */
if ( ! function_exists('get_client_ip')) {
    function get_client_ip()
    {
        return request()->ip();
    }
}

if ( ! function_exists('cmf_get_admin_id')) {
    function cmf_get_admin_id()
    {
        return session('admin.id');
    }
}

/**
 * CMF密码加密方法
 *
 * @param string $pw       要加密的原始密码
 * @param string $authCode 加密字符串,salt
 *
 * @return string
 */
if ( ! function_exists('cmf_password')) {
    function cmf_password($pw, $authCode = 'hui_cmf6')
    {
        $result = md5('###'.md5($pw).$authCode);

        return $result;
    }
}

/**
 * 获取客户端IP地址
 *
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 *
 * @return string
 */
if ( ! function_exists('get_client_ip')) {
    function get_client_ip($type = 0, $adv = true)
    {
        return request()->ip($type, $adv);
    }
}

/**
 * 获取系统配置信息
 *
 * @param $key 键值，可为空，为空获取整个数组
 *
 * @return array|string
 */
if ( ! function_exists('get_config')) {
    function get_config($key = '')
    {
        $data    = Db::name('config')->where('status', 1)->select();
        $configs = array();
        foreach ($data as $val) {
            $configs[$val['name']] = $val['value'];
        }
        if ( ! $key) {
            return $configs;
        } else {
            return array_key_exists($key, $configs) ? $configs[$key] : '';
        }
    }
}

if ( ! function_exists('xdebug')) {
    /**
     * debug调试
     *
     * @param string|array $data   打印信息
     * @param string       $type   类型
     * @param string       $suffix 文件后缀名
     * @param bool         $force
     * @param null         $file
     */
    function xdebug($data, $type = 'xdebug', $suffix = null, $force = false, $file = null)
    {
        ! is_dir(public_path().'xdebug/') && mkdir(public_path().'xdebug/');
        if (is_null($file)) {
            $file = is_null($suffix) ? public_path().'xdebug/'.date('Ymd').'.txt' : public_path().'xdebug/'.date('Ymd')."_{$suffix}".'.txt';
        }
        file_put_contents($file,
            "[".date('Y-m-d H:i:s')."] "."========================= {$type} ===========================".PHP_EOL,
            FILE_APPEND);
        $str = (is_string($data) ? $data : (is_array($data) || is_object($data) ? print_r($data,
                true) : var_export($data, true))).PHP_EOL;

        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}
if ( ! function_exists('get_url')) {
    function get_url($url)
    {
        $ch       = curl_init();
        $header[] = "";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }
}

/**
 * @param $post_url
 * @param $post_data
 *
 * @return bool|mixed|string
 */
if ( ! function_exists('curl_post')) {
    function curl_post($post_url, $post_data, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}

/**
 * 　　* 下划线转驼峰
 * 　　* 思路:
 * 　　* step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * 　　* step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 * 　　*/
function camelize($uncamelized_words, $separator = '_')
{
    $uncamelized_words = $separator.str_replace($separator, " ", strtolower($uncamelized_words));

    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
}

/**
 * 　　* 驼峰命名转下划线命名
 * 　　* 思路:
 * 　　* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * 　　*/
function uncamelize($camelCaps, $separator = '_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1".$separator."$2", $camelCaps));
}