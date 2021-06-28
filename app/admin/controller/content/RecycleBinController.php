<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-28
 * Time: 18:14:18
 * Info:
 */

namespace app\admin\controller\content;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\Article as ArticleModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="回收站管理")
 * Class Node
 * @package app\admin\controller\content
 */
class RecycleBinController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new ArticleModel();
    }

    /**
     * @NodeAnotation(title="回收站列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $page  = (int)$this->request->param('page', 1);
            $limit = (int)$this->request->param('limit', 10);
            $first = ($page - 1) * $limit;
            $key   = $this->request->param('key');

            $where = function ($query) use ($key) {
                $query->where('delete_time', '>', 0);
                if ( ! empty($key['title'])) {
                    $query->whereLike('title', '%'.$key['title'].'%');
                }
            };
            $count = $this->model->where($where)->count();
            $list  = $this->model->onlyTrashed()->limit($first,
                $limit)->order('is_top desc,weight desc,id desc')->select();

            $data = [
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
     * @NodeAnotation(title="回收站删除")
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id');
            if (empty($id)) {
                $this->error('参数错误');
            }
            $data = $this->model->onlyTrashed()->find($id);
            if (empty($data)) {
                $this->error('获取数据失败');
            }
            try {
                $this->model->destroy($id, true);
                $this->success('删除成功');
            } catch (Exception $e) {
                $this->error('删除失败'.$e->getMessage());
            }
        }
    }

}