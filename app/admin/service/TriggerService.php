<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午6:29:46
 * Info:
 */

namespace app\admin\service;

use think\facade\Cache;

class TriggerService
{

    /**
     * 更新菜单缓存
     *
     * @param null $adminId
     *
     * @return bool
     */
    public static function updateMenu($adminId = null)
    {
        if (empty($adminId)) {
            Cache::tag('initAdmin')->clear();
        } else {
            Cache::delete('initAdmin_'.$adminId);
        }
        Cache::delete('systemMenu');

        return true;
    }

    /**
     * 更新节点缓存
     *
     * @param null $adminId
     *
     * @return bool
     */
    public static function updateNode($adminId = null)
    {
        if (empty($adminId)) {
            Cache::tag('authNode')->clear();
        } else {
            Cache::delete('allAuthNode_'.$adminId);
        }
        Cache::delete('systemMenu');

        return true;
    }

    /**
     * 更新系统设置缓存
     * @return bool
     */
    public static function updateSysconfig($clearAll = false)
    {
        if ($clearAll) {
            //清除所有缓存，并退出重新登录
            Cache::clear();
            session('admin', null);
        } else {
            Cache::delete('sysConfig');
        }

        return true;
    }

    /**
     * 更新会员信息
     * @return void
     */
    public static function updateAdminInfo($adminId)
    {
        Cache::delete('adminInfo_'.$adminId);

        return true;
    }

}