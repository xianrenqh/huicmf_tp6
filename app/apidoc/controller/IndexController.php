<?php

namespace app\apidoc\controller;

use think\facade\Request;

class IndexController extends BaseController
{

    protected $config = [
        'title'           => 'APi接口文档',
        'copyright'       => 'Powered By HuiCMF',
        'controllers'     => [],
        'versions'        => [],
        'groups'          => [],
        'with_cache'      => false,
        'responses'       => '{
            "code":"状态码",
            "message":"操作描述",
            "data":"业务数据",
            "timestamp":"响应时间戳"
        }',
        'global_auth_key' => "Authorization",
        'auth'            => [
            'with_auth'     => false,
            'auth_password' => "123456",
            'headers_key'   => "apidocToken",
        ],
        'definitions'     => "xianrenqh\apidoc_v2\lib\Definitions",
        'filter_method'   => [
            '_empty',
        ],
    ];

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
        $config       = config('apidoc_v2') ? config('apidoc_v2') : config('apidoc_v2.');
        $this->config = array_merge($this->config, $config);
        if ( ! empty($this->config['auth'])) {
            $this->config['auth'] = [
                'with_auth'   => $this->config['auth']['with_auth'],
                // 验证类型，password=密码验证，只在进入时做密码验证
                'headers_key' => $this->config['auth']['headers_key'],
            ];
        }

        return json($this->config);
    }

    /**
     * 获取接口列表
     * @return array
     */
    public function getList()
    {
        $config       = config('apidoc_v2') ? config('apidoc_v2') : config('apidoc_v2.');
        $this->config = array_merge($this->config, $config);
        // 验证token身份
        if ($this->config['auth']['with_auth'] === true) {
            $tokenRes = $this->verifyToken();
        }

        $params = $this->request->param();

        $version = "";
        if ( ! empty($params) && ! empty($params['version'])) {
            $version = $params['version'];
        }
        $cacheFiles = [];
        $cacheName  = "";
        if ($this->config['with_cache']) {
            // 获取缓存数据
            $cachePath = "../runtime/apidoc/".$version;
            if (file_exists($cachePath) && $params['reload'] == 'false') {
                $cacheFilePath = "";
                $filePaths     = glob($cachePath.'/*.json');
                if (count($filePaths) > 0) {
                    $cacheFilePath = $filePaths[count($filePaths) - 1];
                }
                if ( ! empty($params) && ! empty($params['cacheFileName'])) {
                    // 前端传入的缓存文件名
                    $cacheFileName = $params['cacheFileName'];
                    $cacheFilePath = $cachePath."/".$cacheFileName.'.json';
                }

                if ($cacheFilePath && file_exists($cacheFilePath)) {
                    $fileContent = file_get_contents($cacheFilePath);

                    if ( ! empty($fileContent)) {
                        $fileJson  = json_decode($fileContent);
                        $list      = $fileJson;
                        $cacheName = str_replace(".json", "", basename($cacheFilePath));

                    } else {
                        $list = $this->getApiList($version);
                    }
                } else {
                    // 不存在缓存文件，生成数据并存缓存
                    $list = $this->getApiList($version);
                    // 生成缓存数据
                    $cacheName = $this->createJsonFile($list, $version);
                }

            } else {
                // 不存在缓存文件，生成数据并存缓存
                $list = $this->getApiList($version);
                // 生成缓存数据
                $cacheName = $this->createJsonFile($list, $version);
            }
            $filePaths = glob($cachePath.'/*.json');
            if (count($filePaths) > 0) {
                foreach ($filePaths as $item) {
                    $cacheFiles[] = str_replace(".json", "", basename($item));
                }
            }
        } else {
            $list = $this->getApiList($version);
        }
        if (isset($this->config['groups']) && count($this->config['groups']) > 0) {
            array_unshift($this->config['groups'], ['title' => '全部', 'name' => 0]);
        }
        $data = array(
            "title"      => $this->config['title'],
            "version"    => $version,
            "copyright"  => $this->config['copyright'],
            "responses"  => $this->config['responses'],
            "list"       => $list,
            "cacheFiles" => $cacheFiles,
            "cacheName"  => $cacheName,
            "groups"     => $this->config['groups'],
        );

        $res = [
            'code' => 0,
            'msg'  => '',
            'data' => $data,
        ];

        return json($res);
    }

    /**
     * 验证身份
     */
    public function verifyAuth()
    {
        $config       = config('apidoc_v2') ? config('apidoc_v2') : config('apidoc_v2.');
        $this->config = array_merge($this->config, $config);
        $request      = Request::instance();
        $params       = $request->param();
        if ($this->config['auth']['with_auth'] === true) {
            // 密码验证
            if (md5($this->config['auth']['auth_password']) === $params['password']) {
                $token = md5($params['password'].strtotime(date('Y-m-d', time())));

                return json(array("token" => $token));
            } else {
                throw new \think\Exception("密码不正确，请重新输入");
            }
        }

        return json($params);
    }

    /**
     * 获取api接口文档
     */
    public function getApiList($version)
    {
        $config       = config('apidoc_v2') ? config('apidoc_v2') : config('apidoc_v2.');
        $this->config = array_merge($this->config, $config);
        $list         = [];
        $controllers  = $this->config['controllers'];
        $versionPath  = "";
        if ( ! empty($version)) {
            foreach ($this->config['versions'] as $item) {
                if ($item['title'] == $version && ! empty($item['folder'])) {
                    $versionPath = $item['folder']."\\";
                }
            }
        }
        foreach ($controllers as $k => $class) {
            $class = "app\\".$versionPath.$class;
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $doc_str    = $reflection->getDocComment();
                $doc        = new \xianrenqh\apidoc_v2\lib\Parser($this->config);
                // 解析控制器类的注释
                $class_doc = $doc->parseClass($doc_str);

                // 获取当前控制器Class的所有方法
                $method        = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                $filter_method = array_merge(['__construct'], $this->config['filter_method']);
                $actions       = [];
                foreach ($method as $j => $action) {
                    // 过滤不解析的方法
                    if ( ! in_array($action->name, $filter_method)) {
                        // 获取当前方法的注释
                        $actionDoc    = new \xianrenqh\apidoc_v2\lib\Parser($this->config);
                        $actionDocStr = $action->getDocComment();
                        if ($actionDocStr) {
                            // 解析当前方法的注释
                            $action_doc = $actionDoc->parseAction($actionDocStr);
                            //$action_doc['name'] = $class."::".$action->name;
                            $action_doc['id']     = $k."-".$j;
                            $action_doc['author'] = ! empty($action_doc['author']) ? str_replace(" ", "",
                                $action_doc['author']) : '';
                            $action_doc['desc']   = ! empty($action_doc['desc']) ? str_replace(" ", "",
                                $action_doc['desc']) : '';
                            $action_doc['method'] = ! empty($action_doc['method']) ? str_replace(" ", "",
                                $action_doc['method']) : '';
                            $action_doc['title']  = ! empty($action_doc['title']) ? str_replace(" ", "",
                                $action_doc['title']) : '';
                            $action_doc['url']    = ! empty($action_doc['url']) ? str_replace(" ", "",
                                $action_doc['url']) : '';
                            $action_doc['tag']    = ! empty($action_doc['tag']) ? str_replace(" ", "",
                                $action_doc['tag']) : '';
                            $action_doc['tag']    = ! empty($action_doc['tag']) ? str_replace("|", " ",
                                $action_doc['tag']) : '';
                            // 解析方法
                            $actions[] = $action_doc;
                        }
                    }
                }
                $class_doc['children'] = $actions;
                $class_doc['id']       = $k."";
                if (empty($class_doc['title']) && empty($class_doc['controller'])) {
                    $class_doc['title'] = $controllers[$k];
                }
                $list[] = $class_doc;
            }
        }

        return $list;
    }

    public function verifyToken()
    {
        if ( ! empty($this->config['auth'])) {
            if ($this->config['auth']['with_auth'] === true) {
                $token = $this->request->header($this->config['auth']['headers_key']);

                if ($token === md5(md5($this->config['auth']['auth_password']).strtotime(date('Y-m-d', time())))) {
                    return true;
                } else {
                    throw new \think\exception\HttpException(401, "身份令牌已过期，请重新登录");
                }
            }
        }

        return true;
    }

    /**
     * 创建接口参数缓存文件
     *
     * @param $json
     * @param $version
     *
     * @return bool|false|string
     */
    protected function createJsonFile($json, $version)
    {
        if (empty($json)) {
            return false;
        }
        $fileName    = date("Y-m-d H_i_s");
        $fileJson    = $json;
        $fileContent = json_encode($fileJson);
        $dir         = "../runtime/apidoc/".$version;
        $path        = $dir."/".$fileName.".json";
        //判断文件夹是否存在
        if ( ! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $myfile = fopen($path, "w") or die("Unable to open file!");
        fwrite($myfile, $fileContent);
        fclose($myfile);

        return $fileName;
    }

}