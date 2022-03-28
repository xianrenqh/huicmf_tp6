<?php
// 应用公共文件
use think\facade\Db;
use app\common\service\AuthService;
use app\admin\library\LibAuthService;

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

if ( ! function_exists('cmf_get_admin_username')) {
    function cmf_get_admin_username()
    {
        return session('admin.username');
    }
}

/**
 * 获取当前登录的管理员角色id
 * @return int
 */
if ( ! function_exists('cmf_get_admin_role_id')) {
    function cmf_get_admin_role_id()
    {
        $libAuth = new LibAuthService();
        $roleId  = $libAuth->getChildrenGroupIds(true);

        return $roleId;
    }
}

/**
 * 根据手机号获取会员id
 */
if ( ! function_exists('cmf_get_user_id')) {
    function cmf_get_user_id($mobile)
    {
        $userModel = new app\common\model\User();
        $user      = $userModel->where(array('mobile' => $mobile))->find();
        if ($user) {
            return $user->id;
        } else {
            return false;
        }
    }
}

/**
 * 按钮权限验证
 */
if ( ! function_exists('check_auth')) {
    function check_auth($rule_name = '')
    {
        $Auth = AuthService::instance();

        return $Auth->check($rule_name, cmf_get_admin_id());
    }
}

/**
 * 获取文件目录列表,该方法返回数组
 */
if ( ! function_exists('getDir')) {
    function getDir($dir)
    {
        $dirArray[] = null;
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".." && ! strpos($file, ".")) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            //关闭句柄
            closedir($handle);
        }

        return $dirArray;
    }
}

/**
 * 构建URL地址
 * 重写url 助手函数
 *
 * @param string $url
 * @param array  $vars
 * @param bool   $suffix
 * @param bool   $domain
 *
 * @return string
 */
if ( ! function_exists('__url')) {
    function __url($url = '', array $vars = [], $suffix = true, $domain = false)
    {
        //return url($url, $vars, $suffix, $domain)->build();

        $defalutModule = 'admin';
        $currentModule = app('http')->getName();
        $string        = (string)url($url, $vars, $suffix, $domain);
        $hideHomeName  = true; // 是否去除url中默认模块名admin/
        if ($hideHomeName && $currentModule == $defalutModule) {
            #去除url中默认模块名admin
            $search = '/'.$defalutModule.'/';
            $pos    = stripos($string, $search);
            $string = substr($string, 0, $pos).'/'.substr($string, $pos + strlen($search));
        }

        return $string;
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
        $cacheData = cache('sysConfig');
        if ( ! empty($cacheData)) {
            $data = $cacheData;
        } else {
            $data = Db::name('config')->where('status', 1)->select()->toArray();
            cache('sysConfig', $data);
        }
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

/**
 * debug调试
 *
 * @param string|array $data   打印信息
 * @param string       $type   类型
 * @param string       $suffix 文件后缀名
 * @param bool         $force
 * @param null         $file
 */
if ( ! function_exists('xdebug')) {
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

if ( ! function_exists('is_file')) {
    function is_file($file)
    {
        if (is_file($file) !== false) {
            return true;
        } else {
            return false;
        }
    }

}

/**
 * 二位数组重新组合数据
 *
 * @param $array
 * @param $key
 *
 * @return array
 */
if ( ! function_exists('array_format_key')) {
    function array_format_key($array, $key)
    {
        $newArray = [];
        foreach ($array as $vo) {
            $newArray[$vo[$key]] = $vo;
        }

        return $newArray;
    }

}

/**
 * 列出目录下所有文件
 *
 * @param string $path 路径
 * @param string $exts 扩展名
 * @param array  $list 增加的文件列表
 *
 * @return  array  所有满足条件的文件
 */
if ( ! function_exists('dir_path')) {
    function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path.'/';
        }

        return $path;
    }
}

/**
 * 删除目录及目录下面的所有文件
 *
 * @param string $dir 路径
 *
 * @return  bool   如果成功则返回 TRUE，失败则返回 FALSE
 */
if ( ! function_exists('dir_delete')) {
    function dir_delete($dir)
    {
        $dir = dir_path($dir);
        if ( ! is_dir($dir)) {
            return false;
        }
        $list = glob($dir.'*');
        foreach ($list as $v) {
            is_dir($v) ? dir_delete($v) : @unlink($v);
        }

        return @rmdir($dir);
    }
}

/**
 * 转换字节数为其他单位
 *
 * @param string $filesize 字节大小
 *
 * @return    string    返回大小
 */
if ( ! function_exists('sizecount')) {
    function sizecount($filesize)
    {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 .' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 .' KB';
        } else {
            $filesize = $filesize.' Bytes';
        }

        return $filesize;
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

/*
/**
 * 获取网站根目录
 * @return string 网站根目录
 */
function cmf_get_root()
{
    $root = "";

    /*$root = str_replace("//", '/', $root);
    $root = str_replace('/index.php', '', $root);
    if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
        $root = preg_replace('/\/api(.php)$/', '', $root);
    }
    $root = rtrim($root, '/');*/

    return $root;
}

/**
 * 添加钩子
 *
 * @param string $hook   钩子名称
 * @param mixed  $params 传入参数
 * @param bool   $once
 *
 * @return mixed
 */
/*function hook($hook, $params = null, $once = false)
{
    $hook = cmf_parse_name($hook, 1);

    return \think\facade\Event::trigger($hook, $params, $once);
}*/

/**
 * 添加钩子,只执行一个
 *
 * @param string $hook   钩子名称
 * @param mixed  $params 传入参数
 *
 * @return mixed
 */
/*function hook_one($hook, $params = null)
{
    $hook = cmf_parse_name($hook, 1);

    return \think\facade\Event::trigger($hook, $params, true);
}*/

/**
 * 获取插件类名
 *
 * @param string $name 插件名
 *
 * @return string
 *
 */
function cmf_get_plugin_class($name, $type = 'hook', $class = null)
{
    $name  = cmf_parse_name($name);
    $class = cmf_parse_name(is_null($class) ? $name : $class, 1);
    switch ($type) {
        case 'controller':
            $namespace = "\\addons\\".$name."\\controller\\".$class;
            break;
        default:
            $namespace = "\\addons\\".$name."\\Plugin";
    }

    return class_exists($namespace) ? $namespace : '';
}

/**
 * 获取插件配置
 *
 * @param string $name 插件名，大驼峰格式
 *
 * @return array
 */
function cmf_get_plugin_config($name)
{
    $class = cmf_get_plugin_class($name);
    if (class_exists($class)) {
        $plugin = new $class();

        return $plugin->getConfig();
    } else {
        return [];
    }
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 *
 * @param string  $name    字符串
 * @param integer $type    转换类型
 * @param bool    $ucfirst 首字母是否大写（驼峰规则）
 *
 * @return string
 */
function cmf_parse_name($name, $type = 0, $ucfirst = true)
{
    if ($type) {
        $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name);

        return $ucfirst ? ucfirst($name) : lcfirst($name);
    }

    return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
}

/**
 * 替代scan_dir的方法
 *
 * @param string $pattern 检索模式 搜索模式 *.txt,*.doc; (同glog方法)
 * @param int    $flags
 * @param        $pattern
 *
 * @return array
 */
function cmf_scan_dir($pattern, $flags = null)
{
    $files = glob($pattern, $flags);
    if (empty($files)) {
        $files = [];
    } else {
        $files = array_map('basename', $files);
    }

    return $files;
}

/**
 * 生成访问插件的url
 *
 * @param string $url    url格式：插件名://控制器名/方法
 * @param array  $vars   参数
 * @param bool   $domain 是否显示域名 或者直接传入域名
 *
 * @return string
 */
function cmf_plugin_url($url, $vars = [], $domain = false)
{
    /*global $CMF_GV_routes;

    if (empty($CMF_GV_routes)) {
        $routeModel    = new \app\common\model\RouteModel();
        $CMF_GV_routes = $routeModel->getRoutes();
    }*/

    $url              = parse_url($url);
    $case_insensitive = true;
    $plugin           = $case_insensitive ? cmf_parse_name($url['scheme']) : $url['scheme'];
    $controller       = $case_insensitive ? cmf_parse_name($url['host']) : $url['host'];
    $action           = trim($case_insensitive ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $vars = array_merge($query, $vars);
    }

    /* 基础参数 */
    $params = [
        '_plugin'     => $plugin,
        '_controller' => $controller,
        '_action'     => $action,
    ];

    $pluginUrl = '\\app\\index\\controller\\PluginController@index?'.http_build_query($params);

    if ( ! empty($vars) && ! empty($CMF_GV_routes[$pluginUrl])) {

        foreach ($CMF_GV_routes[$pluginUrl] as $actionRoute) {
            $sameVars = array_intersect_assoc($vars, $actionRoute['vars']);

            if (count($sameVars) == count($actionRoute['vars'])) {
                ksort($sameVars);
                $pluginUrl = $pluginUrl.'&'.http_build_query($sameVars);
                $vars      = array_diff_assoc($vars, $sameVars);
                break;
            }
        }
    }

    return __url($pluginUrl, $vars, true, $domain);
}

/**
 * 文件下载
 *
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */
if ( ! function_exists('file_down')) {
    function file_down($filepath, $filename = '')
    {
        if ( ! $filename) {
            $filename = basename($filepath);
        }
        if (is_ie()) {
            $filename = rawurlencode($filename);
        }
        $filetype = file_ext($filename);
        $filesize = sprintf("%u", filesize($filepath));
        if (ob_get_length() !== false) {
            @ob_end_clean();
        }
        header('Pragma: public');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Encoding: none');
        header('Content-type: '.$filetype);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-length: '.$filesize);
        readfile($filepath);
        exit;
    }
}
/**
 * IE浏览器判断
 */
if ( ! function_exists('is_ie')) {
    function is_ie()
    {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) {
            return false;
        }
        if (strpos($useragent, 'msie ') !== false) {
            return true;
        }

        return false;
    }
}
/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 *
 * @return 扩展名
 */
if ( ! function_exists('file_ext')) {
    function file_ext($filename)
    {
        return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
    }
}

/**
 * 数组层级缩进转换
 *
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 *
 * @return array
 */
if ( ! function_exists('array2level')) {
    function array2level($array, $pid = 0, $level = 1)
    {
        static $list = [];
        foreach ($array as $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                $list[]     = $v;
                array2level($array, $v['id'], $level + 1);
            }
        }

        return $list;
    }
}

/**
 * 字符截取
 *
 * @param $string    要截取的字符串
 * @param $length    截取长度
 * @param $dot       截取之后用什么表示
 * @param $code      编码格式，支持UTF8/GBK
 */
if ( ! function_exists('str_cut')) {
    function str_cut($string, $length, $dot = '...', $code = 'utf-8')
    {
        $strlen = strlen($string);
        if ($strlen <= $length) {
            return $string;
        }
        $string = str_replace(array(
            ' ',
            '&nbsp;',
            '&amp;',
            '&quot;',
            '&#039;',
            '&ldquo;',
            '&rdquo;',
            '&mdash;',
            '&lt;',
            '&gt;',
            '&middot;',
            '&hellip;',
            '\r\n'
        ), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
        $strcut = '';
        if ($code == 'utf-8') {
            $length = intval($length - strlen($dot) - $length / 3);
            $n      = $tn = $noc = 0;
            while ($n < strlen($string)) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn  = 2;
                    $n   += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t <= 239) {
                    $tn  = 3;
                    $n   += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn  = 4;
                    $n   += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn  = 5;
                    $n   += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn  = 6;
                    $n   += 6;
                    $noc += 2;
                } else {
                    $n++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
            $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(
                ' ',
                '&amp;',
                '&quot;',
                '&#039;',
                '&ldquo;',
                '&rdquo;',
                '&mdash;',
                '&lt;',
                '&gt;',
                '&middot;',
                '&hellip;'
            ), $strcut);
        } else {
            $dotlen      = strlen($dot);
            $maxi        = $length - $dotlen - 1;
            $current_str = '';
            $search_arr  = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
            $replace_arr = array(
                '&amp;',
                '&nbsp;',
                '&quot;',
                '&#039;',
                '&ldquo;',
                '&rdquo;',
                '&mdash;',
                '&lt;',
                '&gt;',
                '&middot;',
                '&hellip;',
                ' '
            );
            $search_flip = array_flip($search_arr);
            for ($i = 0; $i < $maxi; $i++) {
                $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
                if (in_array($current_str, $search_arr)) {
                    $key         = $search_flip[$current_str];
                    $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
                }
                $strcut .= $current_str;
            }
        }

        return $strcut.$dot;
    }
}

/**
 * 获取客户端操作系统
 * php_uname()
 *
 * @param $agent //$_SERVER['HTTP_USER_AGENT']
 *
 * @return array[os]            操作系统名称
 * @return array[os_ver]        操作系统版本号
 * @return array[equipment]     终端设备类型
 */
function getClientOS($agent = '')
{
    $os        = '';
    $equipment = '';
    $os_ver    = php_uname();
    //window系统
    $agent = strtolower($agent);
    if (stripos($agent, 'window')) {
        $os        = 'Windows';
        $equipment = '电脑';
        if (preg_match('/nt 6.0/i', $agent)) {
            $os_ver = 'Vista';
        } elseif (preg_match('/nt 11.0/i', $agent)) {
            $os_ver = '11';
        } elseif (preg_match('/nt 10.0/i', $agent)) {
            $os_ver = '10';
        } elseif (preg_match('/nt 6.3/i', $agent)) {
            $os_ver = '8.1';
        } elseif (preg_match('/nt 6.2/i', $agent)) {
            $os_ver = '8.0';
        } elseif (preg_match('/nt 6.1/i', $agent)) {
            $os_ver = '7';
        } elseif (preg_match('/nt 5.1/i', $agent)) {
            $os_ver = 'XP';
        } elseif (preg_match('/nt 5/i', $agent)) {
            $os_ver = '2000';
        } elseif (preg_match('/nt 98/i', $agent)) {
            $os_ver = '98';
        } elseif (preg_match('/nt/i', $agent)) {
            $os_ver = 'nt';
        } else {
            $os_ver = '';
        }
        if (preg_match('/x64/i', $agent)) {
            $os .= '(x64)';
        } elseif (preg_match('/x32/i', $agent)) {
            $os .= '(x32)';
        }
    } elseif (stripos($agent, 'linux')) {
        if (stripos($agent, 'android')) {
            preg_match('/android\s([\d\.]+)/i', $agent, $match);
            $os        = 'Android';
            $equipment = 'Mobile phone';
            $os_ver    = $match[1];
        } else {
            $os = 'Linux';
        }
    } elseif (stripos($agent, 'unix')) {
        $os = 'Unix';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $agent)) {
        preg_match('/OS\s([0-9_\.]+)/i', $agent, $match);
        $os     = 'IOS';
        $os_ver = str_replace('_', '.', $match[1]);
        if (preg_match('/iPhone/i', $agent)) {
            $equipment = 'iPhone';
        } elseif (preg_match('/iPad/i', $agent)) {
            $equipment = 'iPad';
        } elseif (preg_match('/iPod/i', $agent)) {
            $equipment = 'iPod';
        }
    } elseif (stripos($agent, 'mac os')) {
        preg_match('/Mac OS X\s([0-9_\.]+)/i', $agent, $match);
        $os        = 'Mac OS X';
        $equipment = '电脑';
        $os_ver    = str_replace('_', '.', $match[1]);
    } else {
        $os = 'Other';
    }

    return ['os' => $os, 'os_ver' => $os_ver, 'equipment' => $equipment];
}

/**
 * 获取客户端浏览器以及版本号
 *
 * @param $agent //$_SERVER['HTTP_USER_AGENT']
 *
 * @return array[browser]       浏览器名称
 * @return array[browser_ver]   浏览器版本号
 */
function getClientBrowser($agent = '')
{
    $browser     = '';
    $browser_ver = '';
    if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
        $browser     = 'OmniWeb';
        $browser_ver = $regs[2];
    }
    if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $browser     = 'Netscape';
        $browser_ver = $regs[2];
    }
    if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
        $browser     = 'Safari';
        $browser_ver = $regs[1];
    }
    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        $browser     = 'Internet Explorer';
        $browser_ver = $regs[1];
    }
    if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
        $browser     = 'Opera';
        $browser_ver = $regs[1];
    }
    if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
        $browser     = '(Internet Explorer '.$browser_ver.') NetCaptor';
        $browser_ver = $regs[1];
    }
    if (preg_match('/Maxthon/i', $agent, $regs)) {
        $browser     = '(Internet Explorer '.$browser_ver.') Maxthon';
        $browser_ver = '';
    }
    if (preg_match('/360SE/i', $agent, $regs)) {
        $browser     = '(Internet Explorer '.$browser_ver.') 360SE';
        $browser_ver = '';
    }
    if (preg_match('/SE 2.x/i', $agent, $regs)) {
        $browser     = '(Internet Explorer '.$browser_ver.') 搜狗';
        $browser_ver = '';
    }
    if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        $browser     = 'FireFox';
        $browser_ver = $regs[1];
    }
    if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
        $browser     = 'Lynx';
        $browser_ver = $regs[1];
    }
    if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
        $browser     = 'Chrome';
        $browser_ver = $regs[1];
    }
    if (preg_match('/MicroMessenger\/([^\s]+)/i', $agent, $regs)) {
        $browser     = '微信浏览器';
        $browser_ver = $regs[1];
    }
    if ($browser != '') {
        return ['browser' => $browser, 'browser_ver' => $browser_ver];
    } else {
        return ['browser' => '未知', 'browser_ver' => ''];
    }
}

/**
 * 根据IP地址判断地区
 *
 * @param $clientIP
 *
 * @return string
 */
function getIpToArea($clientIP)
{
    $ip_add      = "";
    $appkey      = '4bd2987ea67c1ea77bb68f0ae8b2ef58';
    $url         = "http://apis.juhe.cn/ip/ipNew";
    $params      = array(
        "ip"    => $clientIP,//需要查询的IP地址或域名
        "key"   => $appkey,//应用APPKEY(应用详细页查询)
        "dtype" => "json",//返回数据的格式,xml或json，默认json
    );
    $paramstring = http_build_query($params);
    $content     = curl_post($url, $paramstring);
    $result      = json_decode($content, true);
    if ( ! empty($result) && $result['resultcode'] == '200') {
        $return = ['code' => 1, 'msg' => $result['reason'], 'data' => $result['result']];
    } else {
        $return = [
            'code' => 0,
            'msg'  => $result['reason'],
            'data' => ['Country' => '', 'Province' => '', 'City' => '', 'Isp' => '']
        ];
    }

    return $return;
}

/**
 * 对查询结果集进行排序
 * @access public
 *
 * @param array  $list   查询结果
 * @param string $field  排序的字段名
 * @param array  $sortby 排序类型
 *                       asc正向排序 desc逆向排序 nat自然排序
 *
 * @return array
 * @author wapai 邮箱:wapai@foxmail.com
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }

    return false;
}

/**
 * 返回经addslashes处理过的字符串或数组
 *
 * @param $string 需要处理的字符串或数组
 *
 * @return mixed
 */
if ( ! function_exists('new_addslashes')) {
    function new_addslashes($string)
    {
        if ( ! is_array($string)) {
            return addslashes($string);
        }
        foreach ($string as $key => $val) {
            $string[$key] = new_addslashes($val);
        }

        return $string;
    }
}

/**
 * 转换数据为HTML代码
 *
 * @param $data 数组
 *
 * @return bool|string
 */
if ( ! function_exists('arr_to_html')) {
    function arr_to_html($data)
    {
        if (is_array($data)) {
            $str = 'array(';
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $str .= "'$key'=>".arr_to_html($val).",";
                } else {
                    //如果是变量的情况
                    if (strpos($val, '$') === 0) {
                        $str .= "'$key'=>$val,";
                    } else {
                        if (preg_match("/^([a-zA-Z_].*)\(/i", $val, $matches)) {
                            //判断是否使用函数
                            if (function_exists($matches[1])) {
                                $str .= "'$key'=>$val,";
                            } else {
                                $str .= "'$key'=>'".newAddslashes($val)."',";
                            }
                        } else {
                            $str .= "'$key'=>'".newAddslashes($val)."',";
                        }
                    }
                }
            }

            return $str.')';
        }

        return false;
    }
}

/**
 * 返回经addslashes处理过的字符串或数组
 *
 * @param $string 需要处理的字符串或数组
 *
 * @return array|string
 */
if ( ! function_exists('newAddslashes')) {
    function newAddslashes($string)
    {
        if ( ! is_array($string)) {
            return addslashes($string);
        }
        foreach ($string as $key => $val) {
            $string[$key] = newAddslashes($val);
        }

        return $string;
    }
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 *
 * @param mixed $mix 变量
 *
 * @return string
 */
if ( ! function_exists('to_guid_string')) {
    function to_guid_string($mix)
    {
        if (is_object($mix)) {
            return spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $mix = get_resource_type($mix).strval($mix);
        } else {
            $mix = serialize($mix);
        }

        return md5($mix);
    }
}

/**
 * 兼容低版本的array_column
 *
 * @param  $array      多维数组
 * @param  $column_key 需要返回值的列
 * @param  $index_key  可选。作为返回数组的索引/键的列。
 *
 * @return array       返回一个数组，数组的值为输入数组中某个单一列的值。
 */
if ( ! function_exists('hui_array_column')) {
    function hui_array_column($array, $column_key, $index_key = null)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column_key, $index_key);
        }

        $result = array();
        foreach ($array as $key => $value) {
            if ( ! is_array($value)) {
                continue;
            }
            if ($column_key) {
                if ( ! isset($value[$column_key])) {
                    continue;
                }
                $tmp = $value[$column_key];
            } else {
                $tmp = $value;
            }
            if ($index_key) {
                $key = isset($value[$index_key]) ? $value[$index_key] : $key;
            }
            $result[$key] = $tmp;
        }

        return $result;
    }
}

/**
 * 传入时间戳,计算距离现在的时间
 *
 * @param number $time 时间戳
 *
 * @return string     返回多少以前
 */
if ( ! function_exists('hui_word_time')) {
    function hui_word_time($time)
    {
        $time = (int)substr($time, 0, 10);
        $int  = time() - $time;
        $str  = '';
        if ($int <= 2) {
            $str = sprintf('刚刚', $int);
        } elseif ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } elseif ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } elseif ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } elseif ($int < 1728000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y-m-d H:i:s', $time);
        }

        return $str;
    }
}

/**
 * var_export 格式化为5.4数组
 */
function var_export_short($data, $return = true)
{
    $dump = var_export($data, true);
    $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
    $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
    $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
    if (gettype($data) == 'object') { // Deal with object states
        $dump = str_replace('__set_state(array(', '__set_state([', $dump);
        $dump = preg_replace('#\)\)$#', "])", $dump);
    } else {
        $dump = preg_replace('#\)$#', "]", $dump);
    }
    if ($return === true) {
        return $dump;
    } else {
        echo $dump;
    }
}
