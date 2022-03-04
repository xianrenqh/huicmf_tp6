<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-01
 * Time: 下午6:05:34
 * Info:
 */

namespace app\admin\controller\content;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\common\model\Category;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="前台栏目管理")
 * Class Node
 * @package app\admin\controller\content
 */
class CategoryController extends AdminController
{

    protected $sort = [
        'sort_order' => 'asc',
        'id'         => 'asc',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new Category();
    }

    /**
     * @NodeAnotation(title="栏目列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list  = $this->model::order($this->sort)->select()->toArray();
            $count = $this->model::count();
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
     * @NodeAnotation(title="添加栏目")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $rule  = [
                'cate_name|栏目名称'   => 'require',
                'cate_en|栏目名称（en）' => 'require'
            ];
            $this->validate($param, $rule);
            //查询栏目名称|en是否存在
            $row = $this->model->whereOr('cate_en', $param['cate_en'])->find();
            if ( ! empty($row)) {
                $this->error('栏目名称en已经存在啦，请重新输入');
            }
            $save = $this->model->save($param);
            if ($save) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $id          = $this->request->param('id');
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign('id', $id);
        $this->assign('pidMenuList', $pidMenuList);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改栏目")
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'cate_name|栏目名称'   => 'require',
                'cate_en|栏目名称（en）' => 'require'
            ];
            $this->validate($param, $rule);
            $row = $this->model->where('cate_en', $param['cate_en'])->find();
            if ( ! empty($row)) {
                $this->error('栏目名称en已经存在啦，请重新输入');
            }
            $save = $this->model->update($param, ['id' => $param['id']]);
            if ($save) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $id   = $this->request->param('id');
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
     * @NodeAnotation(title="删除栏目")
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
        //查询是否存在子集
        $findSon = $this->model->where(['parent_id' => $id])->find();
        if ( ! empty($findSon)) {
            $this->error('请先删除子集后再进行操作');
        }
        $save = $row->delete();
        if ($save) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * @NodeAnotation(title="更改栏目显示状态")
     */
    public function modify()
    {
        if ($this->request->isPost()) {
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
            $row->save([$param['field'] => $param['val']]);
            $this->success('保存成功', ['refresh' => 1]);
        }
    }

}