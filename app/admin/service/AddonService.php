<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-06
 * Time: 下午3:07:23
 * Info:
 */

namespace app\admin\service;

use think\facade\Db;
use think\Exception;
use app\admin\library\AddonMenu as AddonMenuLib;

class AddonService
{

    /**
     * 安装插件
     *
     * @param $name  插件名称
     * @param $force 是否覆盖
     *
     * @return void
     */
    public static function install($name, $force = false)
    {
        try {
            // 检查插件是否完整
            self::check($name);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // 复制静态资源
        $sourceAssetsDir = self::getSourceAssetsDir($name);
        $destAssetsDir   = self::getDestAssetsDir($name);
        if (is_dir($destAssetsDir)) {
            \lib\File::copy_dir($sourceAssetsDir, $destAssetsDir);
        }
        try {
            // 默认启用该插件
            $info = get_addons_info($name);
            unset($info['url']);

            if (0 >= $info['status']) {
                $info['status'] = 1;
                set_addons_info($name, $info);
            }
            // 执行安装脚本
            $class = get_addons_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                $addon->install();
            }
            if (isset($info['has_adminlist']) && $info['has_adminlist']) {
                $admin_list = property_exists($addon, 'admin_list') ? $addon->admin_list : '';
                //添加菜单
                AddonMenuLib::addAddonMenu($info, $admin_list);
            }
            self::runSQL($name);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        self::refresh();

        return true;
    }

    /**
     * 卸载插件.
     *
     * @param string  $name
     * @param boolean $force 是否强制卸载
     *
     * @return bool
     * @throws Exception
     */
    public static function uninstall($name, $force = false)
    {
        if ( ! $name || ! is_dir(ADDON_PATH.$name)) {
            throw new Exception('插件不存在！');
        }
        // 移除插件基础资源目录
        $destAssetsDir = self::getDestAssetsDir($name);
        if (is_dir($destAssetsDir)) {
            \lib\File::del_dir($destAssetsDir);
        }
        // 执行卸载脚本
        try {
            // 默认禁用该插件
            $info = get_addons_info($name);
            if ($info['status'] != -1) {
                $info['status'] = -1;
                set_addons_info($name, $info);
            }
            //删除插件后台菜单
            if (isset($info['has_adminlist']) && $info['has_adminlist']) {
                AddonMenuLib::delAddonMenu($info);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        self::refresh();

        return true;
    }

    /**
     * 执行安装数据库脚本
     *
     * @param type $name 模块名(目录名)
     *
     * @return boolean
     */
    public static function runSQL($name = '', $Dir = 'install')
    {
        $sql_file = ADDON_PATH."{$name}".DS."{$Dir}.sql";
        if (file_exists($sql_file)) {
            $sqlTxt = file_get_contents($sql_file);
            if ( ! empty($sqlTxt)) {
                try {
                    Db::query($sqlTxt);
                } catch (\Exception $e) {
                    throw new Exception('导入SQL失败，请检查{$name}.sql的语句是否正确');
                }
            }
        }

        return true;
    }

    /**
     * 获取插件源资源文件夹
     *
     * @param string $name 插件名称
     *
     * @return  string
     */
    protected static function getSourceAssetsDir($name)
    {
        return ADDON_PATH.$name.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
    }

    /**
     * 检测插件是否完整.
     *
     * @param string $name 插件名称
     *
     * @return bool
     * @throws Exception
     */
    public static function check($name)
    {
        if ( ! $name || ! is_dir(ADDON_PATH.$name)) {
            throw new Exception('插件不存在！');
        }
        $addonClass = get_addons_class($name);
        if ( ! $addonClass) {
            throw new Exception('插件主启动程序不存在');
        }

        return true;
    }

    /**
     * 启用.
     *
     * @param string $name  插件名称
     * @param bool   $force 是否强制覆盖
     *
     * @return bool
     */
    public static function enable($name, $force = false)
    {
        if ( ! $name || ! is_dir(ADDON_PATH.$name)) {
            throw new Exception('插件不存在！');
        }
        //执行启用脚本
        try {
            $class = get_addons_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                if (method_exists($class, 'enable')) {
                    $addon->enable();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $info           = get_addons_info($name);
        $info['status'] = 1;
        set_addons_info($name, $info);
        self::refresh();

        return true;
    }

    /**
     * 禁用.
     *
     * @param string $name  插件名称
     * @param bool   $force 是否强制禁用
     *
     * @return bool
     * @throws Exception
     */
    public static function disable($name, $force = false)
    {
        if ( ! $name || ! is_dir(ADDON_PATH.$name)) {
            throw new Exception('插件不存在！');
        }
        // 执行禁用脚本
        try {
            $class = get_addons_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                if (method_exists($class, 'disable')) {
                    $addon->disable();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $info           = get_addons_info($name);
        $info['status'] = 0;
        set_addons_info($name, $info);
        self::refresh();

        return true;
    }

    /**
     * 获取插件目标资源文件夹
     *
     * @param string $name 插件名称
     *
     * @return  string
     */
    protected static function getDestAssetsDir($name)
    {
        $assetsDir = ROOT_PATH.str_replace("/", DIRECTORY_SEPARATOR, "public/static/addons/{$name}/");
        if ( ! is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        return $assetsDir;
    }

    /**
     * 刷新插件缓存文件.
     * @return bool
     * @throws Exception
     */
    protected static function refresh()
    {
        $file   = ROOT_PATH.'config'.DS.'addons.php';
        $config = get_addons_autoload_config(true);
        if ($config['autoload']) {
            return;
        }
        if ( ! \lib\File::is_really_writable($file)) {
            throw new Exception('addons.php文件没有写入权限');
        }
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php\n\n".'return '.var_export_short($config, true).';');
            fclose($handle);
        } else {
            throw new Exception('文件没有写入权限');
        }

        return true;

    }

}