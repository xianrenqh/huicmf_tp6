<?php
/**
 * Created by PhpStorm.
 * User: 投实科技
 * Date: 2021-11-05
 * Time: 下午3:28:31
 * Info:
 */

namespace app\admin\controller\content;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\Tag as TagModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="Tag标签管理")
 * Class Node
 * @package app\admin\controller\content
 */
class TagController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new TagModel();
    }

    /**
     * @NodeAnotation(title="Tag标签列表")
     */
    public function index()
    {

    }

    /**
     * @NodeAnotation(title="添加Tag")
     */
    public function add()
    {

    }

    /**
     * @NodeAnotation(title="修改Tag")
     */
    public function exit()
    {

    }

    /**
     * @NodeAnotation(title="删除Tag")
     */
    public function delete()
    {

    }

    /**
     * @NodeAnotation(title="选择Tag")
     */
    public function select()
    {
        if ($this->request->isAjax()) {

            $limit = 50;
            if (input('post.dosearch')) {
                $res = $this->model->where('tag', 'like', '%'.input('post.key').'%')->limit($limit)->select();
            } else {
                $res = $this->model->limit($limit)->select();
            }

            $tags = '';
            foreach ($res as $v) {
                $tags .= "<a onclick='set_val(\"".$v['tag']."\")'>#".$v['tag']."</a>";
            }
            $this->success('ok', $tags);
        }

        return $this->fetch('select');
    }
}