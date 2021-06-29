<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-29
 * Time: 15:07:45
 * Info:
 */

namespace app\admin\model;

class LoginLog extends TimeModel
{

    public static function addRecord($loginDevice)
    {
        if (empty($loginDevice)) {
            return false;
        }

        $update                = [];
        $update['user_id']     = $loginDevice['user_id'] ?: 0;
        $update['user_name']   = $loginDevice['user_name'] ?: '';
        $update['browser']     = $loginDevice['browser'] ?: '';
        $update['browser_ver'] = $loginDevice['browser_ver'] ?: '';
        $update['os']          = $loginDevice['os'] ?: '';
        $update['os_ver']      = $loginDevice['os_ver'] ?: '';
        $update['ip_address']  = $loginDevice['ip_address'] ?: '';
        $update['country']     = $loginDevice['country'] ?: '';
        $update['area']        = $loginDevice['area'] ?: '';
        $update['city']        = $loginDevice['city'] ?: '';
        $update['isp']         = $loginDevice['isp'] ?: '';
        $update['desc']        = $loginDevice['desc'] ?: '';

        self::create($update);

        return true;
    }
}