<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-02
 * Time: 下午5:42:17
 * Info:
 */

namespace app\admin\controller\user;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\UserGrade as UserGradeModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="等级管理")
 * Class Node
 * @package app\admin\controller\user
 */
class GradeController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="等级列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $userGradeModel = new UserGradeModel();
            $param          = $this->request->param();

            return $userGradeModel->tableData($param);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加等级")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'name|等级名称' => 'require'
            ];
            $this->validate($param, $rule);
            $userGradeModel = new UserGradeModel();

            $result = $userGradeModel->manageAdd($param);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑等级")
     */
    public function edit()
    {
        $userGradeModel = new UserGradeModel();

        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'name|等级名称' => 'require'
            ];
            $this->validate($param, $rule);

            $result = $userGradeModel->manageEdit($param);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
        $id   = $this->request->param('id');
        $data = $userGradeModel->getGradeInfo($id);
        if (empty($data)) {
            $this->error('获取信息失败');
        }

        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除等级")
     */
    public function delete()
    {
        $id   = $this->request->param('id');
        $userGradeModel = new UserGradeModel();
        $result    = $userGradeModel->manageDel($id);
        if ( ! empty($result['code']) && $result['code'] == 200) {
            $this->success($result['msg']);
        } else {
            $this->error($result['msg']);
        }
    }

}