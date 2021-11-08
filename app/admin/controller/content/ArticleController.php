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
use app\admin\model\Tag as TagModel;
use app\admin\model\Category as CategoryModel;
use think\Exception;
use think\App;
use lib\Random;
use lib\GetImgSrc;
use think\facade\Db;

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

    public function test()
    {
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
                if ( ! empty($key['title'])) {
                    $query->whereLike('title', '%'.$key['title'].'%');
                }
                if ( ! empty($key['status'])) {
                    if ($key['status'] == 2) {
                        $status = 0;
                    } else {
                        $status = 1;
                    }
                    $query->where('status', $status);
                }
            };
            $count = $this->model->where($where)->count();
            $list  = $this->model->where($where)->limit($first,
                $limit)->order('is_top desc,weight desc,id desc')->select();
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
     * @NodeAnotation(title="添加内容")
     */
    public function add()
    {
        $CategoryModel = new CategoryModel();
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
                $param['image'] = ! empty($param['image']) ? $param['image'] : '';
            }
            $param['description'] = empty($param['description']) ? str_cut(strip_tags($param['content']),
                250) : $param['description'];
            $param['content']     = htmlspecialchars($param['content']);
            try {
                $param['create_time'] = time();
                $param['update_time'] = time();
                $id                   = Db::name('article')->strict(false)->insertGetId($param);

                //写入tag标签
                $huiTags    = $param['hui_tags'];
                $huiTagsArr = explode(',', $huiTags);
                if ( ! empty($huiTagsArr)) {
                    $TagModel = new TagModel();
                    $TagModel->tag_dispose($param['type_id'], $huiTagsArr, $id);
                }

                $this->success('保存成功');
            } catch (Exception $e) {
                $this->error('保存失败'.$e->getMessage());
            }
        }
        $click       = Random::numeric(2);
        $pidMenuList = $CategoryModel->getPidMenuList();
        $this->assign('click', $click);
        $this->assign('pidMenuList', $pidMenuList);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改内容")
     */
    public function edit()
    {
        $CategoryModel = new CategoryModel();

        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $data = $this->model->withTrashed()->find($id);
        if (empty($data)) {
            $this->error('获取数据失败');
        }
        $data['thumbs']       = json_decode($data['thumbs'], true);
        $data['thumbs_count'] = count($data['thumbs']);
        $data['thumbs']       = implode(',', $data['thumbs']);
        $data['flag']         = array_filter(explode(',', $data['flag']));
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $rule  = [
                'title|标题'   => 'require',
                'content|内容' => 'require'
            ];
            $this->validate($param, $rule);
            $param['is_top']      = ( ! empty($param['flag']) && in_array(1, $param['flag'])) ? 1 : 0;
            $param['jump_url']    = ( ! empty($param['flag']) && in_array(7, $param['flag'])) ? $param['jump_url'] : '';
            $param['flag']        = ! empty($param['flag']) ? implode(',', $param['flag']) : '';
            $thumbArr             = array_filter(explode(',', $param['thumbs']));
            $param['thumbs']      = json_encode($thumbArr, true);
            $param['delete_time'] = 0;
            //自动提取缩略图
            if (isset($param['auto_image']) && $param['image'] == '') {
                $param['image'] = GetImgSrc::src($param['content'], 1);
                $param['image'] = ! empty($param['image']) ? $param['image'] : '';
            }
            $param['description'] = empty($param['description']) ? str_cut(strip_tags($param['content']),
                250) : $param['description'];
            $param['content']     = htmlspecialchars($param['content']);
            try {
                $param['update_time'] = time();
                $res                  = Db::name('article')->strict(false)->data($param)->update();
                if ($res) {
                    //写入tag标签
                    $huiTags    = $param['hui_tags'];
                    $huiTagsArr = explode(',', $huiTags);
                    if ( ! empty($huiTagsArr)) {
                        $TagModel = new TagModel();
                        $TagModel->tag_dispose($param['type_id'], $huiTagsArr, $param['id']);
                    }
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } catch (Exception $e) {
                $this->error('保存失败'.$e->getMessage());
            }
        }
        $pidMenuList = $CategoryModel->getPidMenuList();
        $tagsArr     = Db::name('tag_content')->alias('tc')->leftJoin('tag t',
            't.id=tc.tagid')->where(['tc.aid' => $data['id']])->column('t.tag');
        $tagsArr     = array_filter($tagsArr);
        $tags        = implode(',', $tagsArr);
        $this->assign('tags', $tags);
        $this->assign('pidMenuList', $pidMenuList);
        $this->assign('data', $data);
        $this->assign('description_length', mb_strlen($data['description']));

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除内容")
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id');
            if (empty($id)) {
                $this->error('参数错误');
            }
            $data = $this->model->find($id);
            if (empty($data)) {
                $this->error('获取数据失败');
            }
            try {
                $this->model->where(['id' => $id])->data(['status' => 0, 'delete_time' => time()])->update();
                $this->success('删除成功');
            } catch (Exception $e) {
                $this->error('删除失败'.$e->getMessage());
            }
        }
    }

}