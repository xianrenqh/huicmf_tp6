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

        return true;
    }

    /**
     * 更新系统设置缓存
     * @return bool
     */
    public static function updateSysconfig()
    {
        Cache::tag('sysconfig')->clear();

        return true;
    }

}