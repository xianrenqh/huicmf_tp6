<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-15
 * Time: 上午11:19:54
 * Info:
 */

namespace app\admin\controller\content;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\Article as ArticleModel;
use think\Exception;
use think\App;
use lib\Random;
use lib\GetImgSrc;

/**
 * @ControllerAnnotation(title="文章管理")
 * Class Node
 * @package app\admin\controller\content
 */
class ArticleController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new ArticleModel();
    }

    /**
     * @NodeAnotation(title="内容列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $page  = (int)$this->request->param('page', 1);
            $limit = (int)$this->request->param('limit', 10);
            $first = ($page - 1) * $limit;
            $key   = $this->request->param('key');

            $where = function ($query) use ($key) {

            };
            $count = $this->model->where($where)->count();
            $list  = $this->model->where($where)->limit($first,
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
     * @NodeAnotation(title="添加内容")
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $rule  = [
                'title|标题'   => 'require',
                'content|内容' => 'require'
            ];
            $this->validate($param, $rule);
            $param['is_top']   = ( ! empty($param['flag']) && in_array(1, $param['flag'])) ? 1 : 0;
            $param['flag']     = ! empty($param['flag']) ? implode(',', $param['flag']) : '';
            $thumbArr          = array_filter(explode(',', $param['thumbs']));
            $param['thumbs']   = json_encode($thumbArr, true);
            $param['admin_id'] = cmf_get_admin_id();
            //自动提取缩略图
            if (isset($param['auto_image']) && $param['image'] == '') {
                $param['image'] = GetImgSrc::src($param['content'], 1);
            }
            $param['content'] = htmlspecialchars($param['content']);
            try {
                $this->model->save($param);
                $this->success('保存成功');
            } catch (Exception $e) {
                $this->error('保存失败'.$e->getMessage());
            }
        }
        $click = Random::numeric(2);
        $this->assign('click', $click);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改内容")
     */
    public function edit()
    {
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除内容")
     */
    public function delete()
    {
        return $this->fetch();
    }

}