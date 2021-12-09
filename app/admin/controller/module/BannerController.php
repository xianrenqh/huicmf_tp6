<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-11-22
 * Time: 下午3:22:48
 * Info: 换灯模块
 */

namespace app\admin\controller\module;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\Banner as BannerModel;
use app\admin\model\BannerType as BannerTypeModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="友情链接管理")
 * Class Node
 * @package app\admin\controller\content
 */
class BannerController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new BannerModel();
    }

    /**
     * @NodeAnotation(title="轮播图列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $count = $this->model->count();
            $list  = $this->model->select();
            $data  = [
                'code'  => 0,
                'msg'   => 'ok',
                'count' => $count,
                'data'  => $list
            ];

            return json($data);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加轮播图")
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $rule  = [
                'title|标题名称' => 'require',
                'image|图片地址' => 'require',
                'url|链接地址'   => 'require'
            ];
            $this->validate($param, $rule);
            $this->model->create($param);
            $this->success('保存成功');
        }
        $types = $this->model->getType();
        $this->assign('types', $types);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑轮播图")
     */
    public function edit()
    {
        $id = $this->request->param('id');

        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $rule  = [
                'title|标题名称' => 'require',
                'image|图片地址' => 'require',
                'url|链接地址'   => 'require'
            ];
            $this->validate($param, $rule);
            $this->model->update($param);
            $this->success('保存成功');
        }
        if (empty($id)) {
            $this->error('获取数据失败');
        }
        $data  = $this->model->find($id);
        $types = $this->model->getType();
        $this->assign('types', $types);
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除轮播图")
     */
    public function delete()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('id不能为空');
        }
        $find = $this->model->where('id', $id)->find();
        if (empty($find)) {
            $this->error('此幻灯不存在');
        }
        $find->delete(true);

        $this->success('删除成功');
    }

    /**
     * @NodeAnotation(title="轮播分类管理")
     */
    public function cat_list()
    {
        $BannerType = new BannerTypeModel();
        if ($this->request->isAjax()) {
            $ids = $this->request->param('id');
            if (empty($ids)) {
                $this->error('不能为空');
            }
            $BannerType->whereIn('tid', $ids)->delete(true);
            $this->success('删除成功');
        }
        $data = $BannerType::select();
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加轮播分类")
     */
    public function cat_add()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $rule  = [
                'name|分类名称' => 'require',
            ];
            $this->validate($param, $rule);
            $BannerType = new BannerTypeModel();
            $BannerType->create($param);
            $this->success('添加成功');
        }

        return $this->fetch();
    }

}