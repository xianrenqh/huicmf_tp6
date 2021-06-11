<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-09
 * Time: 17:04:26
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\library\LibAuthService;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\service\AuthService;
use think\App;
use lib\Tree;
use lib\Tree2;

/**
 * @ControllerAnnotation(title="角色组管理")
 * Class Node
 * @package app\admin\controller\system
 */
class AuthController extends AdminController
{

    /**
     * @NodeAnotation(title="角色组列表")
     */
    public function index()
    {
        $list  = AuthGroup::select();
        $list1 = [];
        foreach ($list as $v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
            $list1[]          = $v;
        }
        $this->assign('list', $list1);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加角色组")
     */
    public function add()
    {
        $tree    = new Tree();
        $libAuth = new LibAuthService();
        $uid     = cmf_get_admin_id();

        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'name|角色名称'  => 'require',
                'rules|权限节点' => 'require'
            ];
            $this->validate($param, $rule);
            $param['create_time'] = time();
            $param['update_time'] = time();
            AuthGroup::create($param);
            $this->success('添加成功');
        }
        $pid     = $this->request->param('pid', 0);
        $array   = [];
        $str     = "<option value='\$id' \$selected> \$spacer \$title</option>";
        $role_id = $libAuth->getChildrenGroupIds(true);
        $data    = AuthGroup::where('id', 'in', $role_id)->select()->toArray();

        foreach ($data as $v) {
            $v['selected'] = $v['id'] == $pid ? 'selected' : '';
            $v['parentid'] = $v['pid'];
            $v['title']    = $v['name'];
            $array[]       = $v;
        }

        $group_data = AuthService::instance()->getGroups($uid);
        $tree->init($array);
        $select_menus = $tree->get_tree($group_data[0]['pid'], $str);
        $this->assign('select_menus', $select_menus);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改角色组")
     */
    public function edit()
    {
        $id   = $this->request->param('id');
        $data = AuthGroup::where('id', $id)->find();
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'name|角色名称'  => 'require',
                'rules|权限节点' => 'require'
            ];
            $this->validate($param, $rule);
            if ($param['id'] == 1) {
                $this->error('禁止更改超级管理员');
            }
            $param['update_time'] = time();
            $data->save($param);
            $this->success('修改成功');
        }
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除角色组")
     */
    public function delete()
    {
        $id   = $this->request->param('id');
        $data = AuthGroup::where('id', $id)->find();
        if (empty($data)) {
            $this->error('获取数据失败');
        }
        if ($data['id'] == 1) {
            $this->error('禁止删除超级管理员');
        }
        $data->delete(true);
        $this->success('删除成功');
    }

    /**
     * @NodeAnotation(title="授权节点")
     */
    public function authorize()
    {
        $id  = $this->request->get('id');
        $row = AuthGroup::find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isAjax()) {
            $list = AuthRule::getAuthorizeNodeListByAdminId($id);
            $this->success('获取成功', $list);
        }
        $this->assign('row', $row);

        return $this->fetch();
    }

}