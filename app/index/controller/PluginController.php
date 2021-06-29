<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-29
 * Time: 9:50:37
 * Info:
 */

namespace app\index\controller;

use app\BaseController;
use think\facade\App;
use think\Loader;

class PluginController extends BaseController
{

    public function index($_plugin, $_controller, $_action)
    {

        $_controller = cmf_parse_name($_controller, 1);

        if ( ! preg_match('/^[A-Za-z](\w|\.)*$/', $_controller)) {
            abort(404, 'controller not exists:'.$_controller);
        }

        if ( ! preg_match('/^[A-Za-z](\w|\.)*$/', $_plugin)) {
            abort(404, 'plugin not exists:'.$_plugin);
        }

        $pluginControllerClass = "plugins\\{$_plugin}\\controller\\{$_controller}Controller";;

        $vars = [];

        return App::invokeMethod([$pluginControllerClass, $_action], $vars);
    }
}