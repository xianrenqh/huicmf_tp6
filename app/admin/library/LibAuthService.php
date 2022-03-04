<?php
/**
 * Created by PhpStorm.
 * User: ***
 * Date: 2021-06-09
 * Time: 16:06:39
 * Info:
 */

namespace app\admin\library;

use lib\Tree2;
use app\common\model\Admin as AdminModel;
use app\common\model\AuthGroup as AuthGroupModel;
use app\common\model\AuthGroupAccess as AuthGroupAccessModel;
use app\common\service\AuthService;

class LibAuthService extends AuthService
{

    protected $auth;

    public $group_id;

    public $uid;

    public $breadcrumb = [];

    public function __construct()
    {
        $this->auth     = AuthService::instance();
        $this->uid      = cmf_get_admin_id();
        $this->group_id = AuthService::instance()->getGroups(cmf_get_admin_id());
    }

    public function getRuleIds($uid = null)
    {
        $uid = is_null($uid) ? $this->uid : $uid;

        return $this->auth->getRuleIds($uid);
    }

    /**
     * 判断是否超级管理
     * @return bool
     */
    public function isSuperAdmin()
    {
        return in_array('*', $this->getRuleIds($this->uid)) ? true : false;
    }

    /**
     * 取出当前管理员所拥有权限的管理员
     *
     * @param boolean $withself 是否包含自身
     *
     * @return array
     */
    public function getChildrenAdminIds($withself = false)
    {
        $childrenAdminIds = [];
        if ( ! $this->isSuperAdmin()) {
            $groupIds      = $this->getChildrenGroupIds(true);
            $authGroupList = AuthGroupAccessModel::field('uid,group_id')->where('group_id', 'in', $groupIds)->select();
            foreach ($authGroupList as $k => $v) {
                $childrenAdminIds[] = $v['uid'];
            }
        } else {
            $childrenAdminIds = AdminModel::column('id');
        }
        if ($withself) {
            if ( ! in_array($this->uid, $childrenAdminIds)) {
                $childrenAdminIds[] = $this->uid;
            }
        } else {
            $childrenAdminIds = array_diff($childrenAdminIds, [$this->uid]);
        }
        $childrenAdminIds = array_values(array_unique($childrenAdminIds));

        return $childrenAdminIds;

    }

    /**
     * 取出当前管理员所拥有权限的分组
     *
     * @param boolean $withself 是否包含当前所在的分组
     *
     * @return array
     */
    public function getChildrenGroupIds($withself = false)
    {
        $groups         = AuthService::getGroups($this->uid);
        $groupIds       = array_column($groups->toArray(), 'id');
        $originGroupIds = $groupIds;
        foreach ($groups as $k => $v) {
            if (in_array($v['pid'], $originGroupIds)) {
                $groupIds = array_diff($groupIds, [$v['id']]);
                unset($groups[$k]);
            }
        }
        $groupList = AuthGroupModel::where(['status' => 'normal'])->select();
        $objList   = [];
        foreach ($groups as $k => $v) {
            if ($v['rules'] === '*') {
                $objList = $groupList;
                break;
            }
            // 取出包含自己的所有子节点
            $childrenList = Tree2::instance()->init($groupList)->getChildren($v['id'], true);
            $obj          = Tree2::instance()->init($childrenList)->getTreeArray($v['pid']);
            $objList      = array_merge($objList, Tree2::instance()->getTreeList($obj));
        }
        $childrenGroupIds = [];
        foreach ($objList as $k => $v) {
            $childrenGroupIds[] = $v['id'];
        }

        if ( ! $withself) {
            $childrenGroupIds = array_diff($childrenGroupIds, $groupIds);
        }

        return $childrenGroupIds;
    }

}