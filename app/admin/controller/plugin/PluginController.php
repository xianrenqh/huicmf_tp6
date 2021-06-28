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
use app\admin\model\Plugin as PluginModel;
use app\admin\logic\PluginLogic;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use think\Model;

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
        $pluginModel = new PluginModel();
        $plugins     = $pluginModel->getList();
        $this->assign("plugins", $plugins);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="插件安装")
     */
    public function install()
    {
        if ($this->request->isAjax()) {
            $pluginName = $this->request->param('name', '', 'trim');
            $result     = PluginLogic::install($pluginName);

            if ($result !== true) {
                $this->error($result);
            }

            $this->success('安装成功!');
        }
    }

}