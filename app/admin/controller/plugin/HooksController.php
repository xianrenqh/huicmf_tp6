<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-08
 * Time: 下午3:29:58
 * Info:
 */

namespace app\admin\controller\plugin;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\common\model\Hooks as HooksModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="钩子管理")
 * Class Node
 * @package app\admin\controller\plugin
 */
class HooksController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="钩子列表")
     */
    public function index()
    {
        $hooksModel = new HooksModel();
        if ($this->request->isAjax()) {
            $param = $this->request->param();

            return $hooksModel->tableData($param);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑钩子")
     */
    public function add()
    {
        $hooksModel = new HooksModel();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'name|钩子名称' => 'require'
            ];
            $this->validate($param, $rule);

            $res = $hooksModel->manageAdd($param);
            if ( ! empty($res['code']) && $res['code'] == 200) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑钩子")
     */
    public function edit()
    {
        $hooksModel = new HooksModel();
        $id         = $this->request->param('id');
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'name|钩子名称' => 'require'
            ];
            $this->validate($param, $rule);
            $result = $hooksModel->manageEdit($param);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
        $data = $hooksModel->getDataInfo($id);
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除钩子")
     */
    public function delete()
    {
        $id         = $this->request->param('id');
        $hooksModel = new HooksModel();
        $result     = $hooksModel->manageDel($id);
        if ( ! empty($result['code']) && $result['code'] == 200) {
            $this->success($result['msg']);
        } else {
            $this->error($result['msg']);
        }
    }

}