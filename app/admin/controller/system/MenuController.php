<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-31
 * Time: 下午5:10:58
 * Info:
 */

namespace app\admin\controller\system;

use app\common\model\SystemMenu;
use app\admin\service\TriggerService;
use app\common\constants\MenuConstant;
use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\facade\Cache;
use think\App;

/**
 * @ControllerAnnotation(title="菜单管理")
 * Class Node
 * @package app\admin\controller\system
 */
class MenuController extends AdminController
{

    protected $sort = [
        'sort' => 'asc',
        'id'   => 'asc',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemMenu();
    }

    /**
     * @NodeAnotation(title="菜单列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $cacheData = Cache::get('systemMenu');
            if ( ! empty($cacheData)) {
                $list = $cacheData;
            } else {
                $list = SystemMenu::order($this->sort)->select()->toArray();
                Cache::tag('systemMenu')->set('systemMenu', $list);
            }
            $count = SystemMenu::count();
            $data  = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list
            ];

            return json($data);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'pid|上级菜单'   => 'require',
                'title|菜单名称' => 'require',
                'icon|菜单图标'  => 'require',
            ];
            $this->validate($param, $rule);
            //查询链接是否已添加
            $row = $this->model->where('href', '<>', '')->where('href', $param['href'])->find();
            if ( ! empty($row)) {
                $this->error('该链接已被添加过了！');
            }
            $save = $this->model->save($param);
            if ($save) {
                TriggerService::updateMenu();
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $homeId = $this->model->where(['pid' => MenuConstant::HOME_PID,])->value('id');
        $id     = $this->request->param('id');
        if ($id == $homeId) {
            $this->error('首页不能添加子菜单');
        }
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign('id', $id);
        $this->assign('pidMenuList', $pidMenuList);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑")
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'pid|上级菜单'   => 'require',
                'title|菜单名称' => 'require',
                'icon|菜单图标'  => 'require',
            ];
            $this->validate($param, $rule);
            $save = $this->model->update($param, ['id' => $param['id']]);
            if ($save) {
                TriggerService::updateMenu();
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $id   = $this->request->param('id');
        if($id==1){
            $this->error('不能编辑id=1的数据');
        }
        $data = $this->model->findOrEmpty($id);
        if ($data->isEmpty()) {
            $this->error('获取数据失败');
        }

        $pidMenuList = $this->model->getPidMenuList();
        $this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('pidMenuList', $pidMenuList);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除")
     */
    public function delete()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $row = $this->model->find($id);
        if (empty($row)) {
            $this->error('数据不存在');
        }
        $save = $row->delete();
        if ($save) {
            TriggerService::updateMenu();
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * @NodeAnotation(title="属性修改")
     */
    public function modify()
    {
        $param = $this->request->param();
        $rule  = [
            'id|ID'    => 'require',
            'field|字段' => 'require',
            'val|值'    => 'require',
        ];
        $this->validate($param, $rule);
        $row = $this->model->find($param['id']);
        if (empty($row)) {
            $this->error('数据不存在');
        }
        $homeId = $this->model->where([
            'pid' => MenuConstant::HOME_PID,
        ])->value('id');

        if ($param['id'] == $homeId && $param['field'] == 'status') {
            $this->error('首页状态不允许关闭');
        }
        $row->save([$param['field'] => $param['val']]);
        TriggerService::updateMenu();
        $this->success('保存成功', ['refresh' => 1]);
    }

}
