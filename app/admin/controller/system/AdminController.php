<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午5:18:55
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\admin\model\AuthGroup;
use app\common\service\AuthService;
use lib\Random;
use lib\Tree;
use think\App;
use app\admin\model\Admin as AdminModel;
use app\admin\model\AuthGroupAccess;
use app\admin\library\LibAuthService;

/**
 * Class AdminController
 * @package app\admin\controller\system
 * @ControllerAnnotation(title="管理员管理",auth=true)
 */
class AdminController extends \app\common\controller\AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new AdminModel();
        $this->assign('auth_list', $this->model->getAuthList());
    }

    /**
     * @NodeAnotation(title="管理员列表")
     */
    public function index()
    {
        $tree    = new Tree();
        $libAuth = new LibAuthService();
        if ($this->request->isAjax()) {
            $page    = (int)$this->request->param('page', 1);
            $limit   = (int)$this->request->param('limit', 10);
            $field   = $this->request->param('field', 'id');
            $order   = $this->request->param('order', 'asc');
            $first   = ($page - 1) * $limit;
            $key     = $this->request->param('key');
            $libAuth = new LibAuthService();
            $role_id = $libAuth->getChildrenGroupIds(true);

            $where = function ($query) use ($key, $role_id) {
                if ( ! empty($key['username'])) {
                    $query->where('username', $key['username']);
                }
                if ( ! empty($key['roles'])) {
                    if (in_array($key['roles'], $role_id)) {
                        $uids    = AuthGroupAccess::alias('a')->field('uid')->join('auth_group g',
                            'g.id = a.group_id')->where('a.group_id', $key['roles'])->select();
                        $cha_ids = array_column($uids->toArray(), 'uid');
                        $query->whereIn('id', $cha_ids);
                    }
                }
                if ( ! empty($key['isuse']) && ! is_numeric($key['isuse'])) {
                    $query->where('status', $key['isuse']);
                }
            };
            $count = $this->model->where($where)->count();
            $list  = $this->model->where($where)->limit($first, $limit)->order([$field => $order])->select();
            for ($i = 0; $i < count($list); $i++) {
                $group_list             = AuthService::instance()->getGroups($list[$i]['id']);
                $group_list1            = array_column($group_list->toArray(), 'name');
                $list[$i]['user_group'] = implode(',', $group_list1);
                $list[$i]['logintime']  = date('Y-m-d H:i:s', $list[$i]['logintime']);
            }
            $data = [
                'code'  => 0,
                'msg'   => 'ok',
                'count' => $count,
                'data'  => $list
            ];

            return json($data);
        }
        $uid   = cmf_get_admin_id();
        $pid   = $this->request->param('pid', 0);
        $array = [];

        //$role_id = $libAuth->getChildrenGroupIds(true);
        $data = AuthGroup::where('status', 'normal')->select()->toArray();
        foreach ($data as $v) {
            $v['selected'] = $v['id'] == $pid ? 'selected' : '';
            $v['parentid'] = $v['pid'];
            $v['title']    = $v['name'];
            $array[]       = $v;
        }

        $str = "<option value='\$id' \$selected> \$spacer \$title</option>";
        $tree->init($array);
        $group_data        = AuthService::instance()->getGroups($uid);
        $select_auth_group = $tree->get_tree($group_data[0]['pid'], $str);

        $this->assign('select_auth_group', $select_auth_group);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加管理员")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'username|登录名称' => 'require',
                'password|登录密码' => 'require',
                'auth_ids|角色组'  => 'require',
                'nickname|昵称'   => 'require'
            ];
            $this->validate($param, $rule);

            //查询用户名是否存在
            $findAdmin = $this->model->where('username', $param['username'])->find();
            if ( ! empty($findAdmin)) {
                $this->error('已存在此登录名，请更换');
            }
            //写入管理员表
            $salt       = Random::alnum();
            $insertData = [
                'username' => $param['username'],
                'salt'     => $salt,
                'password' => cmf_password($param['password'], $salt),
                'nickname' => $param['nickname'],
                'status'   => $param['status']
            ];
            $insertId   = $this->model->insertGetId($insertData);
            //写入角色对应表
            $authIdsArr = array_filter(array_keys($param['auth_ids']));
            foreach ($authIdsArr as $k => $v) {
                $data2[] = ['uid' => $insertId, 'group_id' => $v];
            }
            AuthGroupAccess::insertAll($data2);
            $this->success('添加成功');
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑管理员")
     */
    public function edit()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('id不能为空');
        }
        $find = $this->model->find($id);
        if (empty($find)) {
            $this->error('管理员信息不存在');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'id|id'         => 'require',
                'username|登录名称' => 'require',
                'auth_ids|角色组'  => 'require',
                'nickname|昵称'   => 'require'
            ];
            $this->validate($param, $rule);
            $updateData = [
                'id'       => $param['id'],
                'nickname' => $param['nickname'],
                'status'   => $param['status']
            ];
            if ( ! empty($param['password'])) {
                $salt                   = Random::alnum();
                $updateData['salt']     = $salt;
                $updateData['password'] = cmf_password($param['password'], $salt);
            } else {
                unset($param['password']);
            }
            $updateRow = $find->update($updateData);
            //写入角色对应表
            AuthGroupAccess::where('uid', $id)->delete(true);
            $authIdsArr = array_filter(array_keys($param['auth_ids']));
            foreach ($authIdsArr as $k => $v) {
                $data2[] = ['uid' => $id, 'group_id' => $v];
            }
            AuthGroupAccess::insertAll($data2);
            $this->success('修改成功');

        }
        $authGroupAccess = AuthGroupAccess::where('uid', $id)->column('group_id');
        $this->assign('data', $find);
        $this->assign('group_access', $authGroupAccess);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除管理员")
     */
    public function delete()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('id不能为空');
        }
        if ($id == 1) {
            $this->error('不能删除id为1的管理员');
        }
        $find = $this->model->where('id', $id)->find();
        if (empty($find)) {
            $this->error('管理员信息不存在');
        }
        $find->delete(true);
        $this->success('删除成功');
    }

}