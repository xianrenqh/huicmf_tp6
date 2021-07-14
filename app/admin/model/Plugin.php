<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-28
 * Time: 11:25:11
 * Info:
 */

namespace app\admin\model;

use think\Model;
use app\admin\model\Hook as HookModel;

class Plugin extends Model
{

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'plugin';

    /**
     * 获取插件列表
     */
    public function getList($addon_dir = '')
    {
        if ( ! $addon_dir) {
            $addon_dir = HUICMF_ADDON_PATH;
        }
        //获取插件文件夹所有插件名
        $dirs = array_map('basename', glob($addon_dir.'*', GLOB_ONLYDIR));
        if ($dirs === false || ! file_exists($addon_dir)) {
            $this->error = '插件目录不可读或者不存在';

            return false;
        }
        $plugins = [];
        if (empty($dirs)) {
            return $plugins;
        }

        $list = $this->select()->toArray();
        //都设置为未安装
        foreach ($list as $addon) {
            $addon['uninstall']     = 0;
            $addons[$addon['name']] = $addon;
        }

        foreach ($dirs as $value) {
            if ( ! isset ($addons[$value])) {
                $class = cmf_get_plugin_class($value);
                if ( ! class_exists($class)) {
                    // 实例化插件失败忽略执行
                    continue;
                }
                $obj            = new $class ();
                $addons[$value] = $obj->info;
                if ($addons[$value]) {
                    $addons[$value]['uninstall'] = 1;
                    //unset ($addons[$value]['status']);
                }
            }
        }
        $addons = list_sort_by($addons, 'uninstall', 'desc');
        for ($i = 0; $i < count($addons); $i++) {
            switch ($addons[$i]['status']) {
                case 0;
                    $status = '未安装';
                    break;
                case 1;
                    $status = '启用';
                    break;
                case 2;
                    $status = '禁用';
                    break;
                case 3;
                    $status = '损坏';
                    break;
                default:
                    $status = '其他';
                    break;
            }
            $addons[$i]['status_name'] = $status;
        }

        return $addons;
    }

    /**
     * @TODO
     * 获取所有钩子，包括系统，应用，模板
     *
     * @param bool $refresh 是否刷新缓存
     *
     * @return array
     */
    public function getHooks($refresh = false)
    {
        if ( ! $refresh) {
            // TODO 加入缓存
        }

        $returnHooks = [];
        $systemHooks = [
            //系统钩子
            "app_init",
            "app_begin",
            "module_init",
            "action_begin",
            "view_filter",
            "app_end",
            "log_write",
            "log_write_done",
            "response_end",
            "admin_init",
            "home_init",
            "send_mobile_verification_code",
            //系统钩子结束

            //前台登录钩子
            "user_login_start",

            //模板钩子
            "body_start",
            "before_head_end",
            "before_footer",
            "footer_start",
            "before_footer_end",
            "before_body_end",
            "left_sidebar_start",
            "before_left_sidebar_end",
            "right_sidebar_start",
            "before_right_sidebar_end",
            "comment",
            "guestbook",

        ];

        $dbHooks = HookModel::column('hook');

        $returnHooks = array_unique(array_merge($systemHooks, $dbHooks));

        return $returnHooks;

    }

}