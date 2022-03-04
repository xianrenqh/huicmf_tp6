<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-28
 * Time: 11:22:25
 * Info:
 */

namespace app\admin\controller\plugin;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\common\model\Plugin as PluginModel;
use app\admin\service\AddonService;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="插件管理")
 * Class Node
 * @package app\admin\controller\plugin
 */
class PluginController extends AdminController
{

    protected $annotationDebug = true;

    /**
     * @NodeAnotation(title="插件列表")
     */
    public function index()
    {
        $plugins = get_plugins_list();
        $this->assign("plugins", $plugins);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="插件安装")
     */
    public function install()
    {
        if ($this->request->isAjax()) {
            $name = $this->request->param('name', '', 'trim');
            if (empty($name)) {
                $this->error('请选择需要安装的插件！');
            }
            try {
                AddonService::install($name);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('插件安装成功！');
        }
    }

    /**
     * @NodeAnotation(title="插件卸载")
     */
    public function uninstall()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('请选择需要安装的插件！');
        }
        if ( ! preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            $this->error('插件标识错误！');
        }
        try {
            AddonService::uninstall($name, true);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('插件卸载成功！清除浏览器缓存和框架缓存后生效！');
    }

    /**
     * @NodeAnotation(title="插件设置")
     */
    public function config($name = null)
    {
        $name = $name ? $name : $this->request->param("get.name");
        if ( ! $name) {
            $this->error('参数不得为空！');
        }
        if ( ! preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            $this->error('插件名称不正确！');
        }
        if ( ! is_dir(ADDON_PATH.$name)) {
            $this->error('目录不存在！');
        }
        $info = get_addons_info($name);
        unset($info['url']);
        $config = get_addons_fullconfig($name);
        if ( ! $info) {
            $this->error('配置不存在！');
        }

        if ($this->request->isPost()) {
            $params = $this->request->param('config/a');
            if ($params) {
                foreach ($config as $k => &$v) {
                    if (isset($params[$v['name']])) {
                        if ($v['type'] == 'array') {
                            $params[$v['name']] = is_array($params[$v['name']]) ? $params[$v['name']] : (array)json_decode($params[$v['name']],
                                true);
                            $value              = $params[$v['name']];
                        } else {
                            $value = is_array($params[$v['name']]) ? implode(',',
                                $params[$v['name']]) : $params[$v['name']];
                        }
                        $v['value'] = $value;
                    }
                }
                try {
                    //更新配置文件
                    set_addons_fullconfig($name, $config);
                    //AddonService::refresh();
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->success('插件配置成功！');
        }
        $data = ['info' => $info, 'config' => $config];

        $this->assign('data', $data);
        $configFile = ADDON_PATH.$name.DS.'config.html';
        if (is_file($configFile)) {
            $this->assign('custom_config', $this->app->view->fetch($configFile));
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="启用禁用")
     */
    public function status()
    {
        $name   = $this->request->param('name');
        $action = $this->request->param('action');
        if ( ! $name) {
            $this->error('参数不得为空！');
        }
        if ( ! preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            $this->error('插件名称不正确');
        }
        try {
            $action = $action == 'enable' ? $action : 'disable';
            //调用启用、禁用的方法
            AddonService::$action($name, true);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }

}