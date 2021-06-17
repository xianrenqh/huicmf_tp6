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

/**
 * @ControllerAnnotation(title="文章管理")
 * Class Node
 * @package app\admin\controller\content
 */
class ArticleController extends AdminController
{

    /**
     * @NodeAnotation(title="内容列表")
     */
    public function index()
    {

    }

    /**
     * @NodeAnotation(title="添加内容")
     */
    public function add()
    {
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