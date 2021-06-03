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
use think\App;
use think\Model;
use app\admin\model\Admin as AdminModel;

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
    }

    /**
     * @NodeAnotation(title="管理员列表",auth=true)
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $page  = (int)$this->request->param('page', 1);
            $limit = (int)$this->request->param('limit', 10);
            $field = $this->request->param('field', 'id');
            $order = $this->request->param('order', 'asc');
            $first = ($page - 1) * $limit;
            $key   = $this->request->param('key');
            $where = function ($query) use ($key) {
                if ( ! empty($key['username'])) {
                    $query->where('username', $key['username']);
                }
                if ( ! empty($key['roles'])) {

                }
                if ( ! empty($key['isuse'])) {
                    $query->where('status', $key['isuse']);
                }
            };
            $count = $this->model->where($where)->count();
            $list  = $this->model->where($where)->limit($first, $limit)->order([$field => $order])->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['logintime'] = date('Y-m-d H:i:s', $list[$i]['logintime']);
            }
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list
            ];

            return json($data);
        }

        return $this->fetch();
    }
}