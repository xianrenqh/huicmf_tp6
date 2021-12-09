<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-06
 * Time: 下午4:57:52
 * Info:
 */

namespace app\admin\library;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DStDev;
use think\Exception;
use app\admin\model\SystemMenu as SystemMenuModel;

class AddonMenu
{

    /**
     * 添加插件后台管理菜单
     *
     * @param type $info
     * @param type $adminlist
     *
     * @return boolean
     */
    public static function addAddonMenu($info, $admin_list = null)
    {
        if (empty($info)) {
            throw new Exception('没有数据！');
        }
        $data = [];
        if ( ! empty($admin_list)) {
            foreach ($admin_list as $v) {
                $data2  = [
                    'pid'         => 20,
                    'title'       => ! empty($v['title']) ? $v['title'] : $info['title'],
                    'href'        => 'addons.'.$info['name'].'/'.$v['action'],
                    'icon'        => ! empty($v['icon']) ? $v['icon'] : 'fa-list',
                    'sort'        => $v['sort'],
                    'status'      => $v['status'],
                    'create_time' => time(),
                    'update_time' => time()
                ];
                $data[] = $data2;
            }
            SystemMenuModel::insertAll($data);
        }

        return true;

    }

    /**
     * 删除对应插件菜单和权限
     *
     * @param type $info 插件信息
     *
     * @return boolean
     */
    public static function delAddonMenu($info)
    {
        if (empty($info)) {
            throw new Exception('没有数据！');
        }
        $menuId = 20;
        //删除对应菜单
        SystemMenuModel::whereLike('href', 'addons.'.$info['name'].'%')->delete();
        $count = SystemMenuModel::where('pid', $menuId)->count();
        if ( ! $count) {
            SystemMenuModel::where('id', $menuId)->update(array('status' => 0));
        }

        return true;
    }

}