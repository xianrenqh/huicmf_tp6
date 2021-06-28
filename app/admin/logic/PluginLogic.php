<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\admin\model\Plugin as PluginModel;
use app\admin\model\HookPlugin as HookPluginModel;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use think\facade\Cache;

class PluginLogic
{

    protected $annotationDebug = true;

    /**
     * 安装应用
     */
    public static function install($pluginName)
    {
        $class = cmf_get_plugin_class($pluginName);
        if ( ! class_exists($class)) {
            return '插件不存在!';
        }

        $pluginModel = new PluginModel();
        $pluginCount = $pluginModel->where('name', $pluginName)->count();

        if ($pluginCount > 0) {
            return '插件已安装!';
        }

        $plugin = new $class;
        $info   = $plugin->info;
        if ( ! $info || ! $plugin->checkInfo()) {//检测信息的正确性
            return '插件信息缺失!';
        }

        $installSuccess = $plugin->install();
        if ( ! $installSuccess) {
            return '插件预安装失败!';
        }

        $methods = get_class_methods($plugin);

        foreach ($methods as $methodKey => $method) {
            $methods[$methodKey] = cmf_parse_name($method);
        }

        $systemHooks = $pluginModel->getHooks(true);

        $pluginHooks = array_intersect($systemHooks, $methods);

        //$info['hooks'] = implode(",", $pluginHooks);

        if ( ! empty($plugin->hasAdmin)) {
            $info['has_admin'] = 1;
        } else {
            $info['has_admin'] = 0;
        }

        $info['config'] = json_encode($plugin->getConfig());

        $pluginModel->save($info);

        foreach ($pluginHooks as $pluginHook) {
            $hookPluginModel = new HookPluginModel();
            $hookPluginModel->save(['hook' => $pluginHook, 'plugin' => $pluginName, 'status' => 1]);
        }

        self::getActions($pluginName);

        Cache::clear('init_hook_plugins');
        Cache::clear('admin_menus');// 删除后台菜单缓存

        return true;
    }

    public static function update($pluginName)
    {
        $class = cmf_get_plugin_class($pluginName);
        if ( ! class_exists($class)) {
            return '插件不存在!';
        }

        $plugin = new $class;
        $info   = $plugin->info;
        if ( ! $info || ! $plugin->checkInfo()) {//检测信息的正确性
            return '插件信息缺失!';
        }

        if (method_exists($plugin, 'update')) {
            $updateSuccess = $plugin->update();
            if ( ! $updateSuccess) {
                return '插件预升级失败!';
            }
        }

        $methods = get_class_methods($plugin);

        foreach ($methods as $methodKey => $method) {
            $methods[$methodKey] = cmf_parse_name($method);
        }

        $pluginModel = new PluginModel();
        $systemHooks = $pluginModel->getHooks(true);

        $pluginHooks = array_intersect($systemHooks, $methods);

        if ( ! empty($plugin->hasAdmin)) {
            $info['has_admin'] = 1;
        } else {
            $info['has_admin'] = 0;
        }

        $config = $plugin->getConfig();

        $defaultConfig = $plugin->getDefaultConfig();

        $pluginModel = new PluginModel();

        $config = array_merge($defaultConfig, $config);

        $info['config'] = json_encode($config);

        $pluginModel->where('name', $pluginName)->update($info);

        $hookPluginModel = new HookPluginModel();

        $pluginHooksInDb = $hookPluginModel->where('plugin', $pluginName)->column('hook');

        $samePluginHooks = array_intersect($pluginHooks, $pluginHooksInDb);

        $shouldDeleteHooks = array_diff($samePluginHooks, $pluginHooksInDb);

        $newHooks = array_diff($pluginHooks, $samePluginHooks);

        if (count($shouldDeleteHooks) > 0) {
            $hookPluginModel->where('hook', 'in', $shouldDeleteHooks)->delete();
        }

        foreach ($newHooks as $pluginHook) {
            $hookPluginModel->save(['hook' => $pluginHook, 'plugin' => $pluginName]);
        }
        
        //self::getActions($pluginName);

        Cache::clear('init_hook_plugins');
        Cache::clear('admin_menus');// 删除后台菜单缓存

        return true;
    }

    public function getActions($pluginName)
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->annotationCacheDir = runtime_path().'annotation'.DIRECTORY_SEPARATOR.'node';
        $reader                   = new FileCacheReader(new AnnotationReader(), $this->annotationCacheDir,
            $this->annotationDebug);

        $newMenus  = [];
        $pluginDir = cmf_parse_name($pluginName);

        $filePatten = public_path().'plugins/'.$pluginDir.'/controller/Admin*Controller.php';

        $controllers = cmf_scan_dir($filePatten);

        $app = 'plugin/'.$pluginName;

        if ( ! empty($controllers)) {
            foreach ($controllers as $controller) {

                $controller      = preg_replace('/\.php$/', '', $controller);
                $controllerName  = uncamelize(preg_replace("/Controller$/", '', $controller));
                $controllerClass = "plugins\\$pluginDir\\controller\\$controller";

                $reflectionClass = new \ReflectionClass($controllerClass);
                $methods         = $reflectionClass->getMethods();
                $actionList      = [];

                foreach ($methods as $method) {
                    // 读取NodeAnotation的注解
                    $nodeAnnotation = $reader->getMethodAnnotation($method, NodeAnotation::class);
                    if ( ! empty($nodeAnnotation) && ! empty($nodeAnnotation->title)) {
                        $actionTitle  = ! empty($nodeAnnotation) && ! empty($nodeAnnotation->title) ? $nodeAnnotation->title : null;
                        $actionAuth   = ! empty($nodeAnnotation) && ! empty($nodeAnnotation->auth) ? $nodeAnnotation->auth : false;
                        $actionList[] = [
                            'node'    => $app.'/'.$controllerName.'/'.$method->name,
                            'title'   => $actionTitle,
                            'is_auth' => $actionAuth,
                            'type'    => 2,
                        ];
                    }
                }
                // 方法非空才读取控制器注解
                if ( ! empty($actionList)) {
                    // 读取Controller的注解
                    $controllerAnnotation = $reader->getClassAnnotation($reflectionClass, ControllerAnnotation::class);
                    $controllerTitle      = ! empty($controllerAnnotation) && ! empty($controllerAnnotation->title) ? $controllerAnnotation->title : null;
                    $controllerAuth       = ! empty($controllerAnnotation) && ! empty($controllerAnnotation->auth) ? $controllerAnnotation->auth : false;
                    $nodeList[]           = [
                        'node'    => $app.$controllerName,
                        'title'   => $controllerTitle,
                        'is_auth' => $controllerAuth,
                        'type'    => 1,
                    ];
                    $nodeList             = array_merge($nodeList, $actionList);
                }
                halt($nodeList);

            }

        }
    }
}
