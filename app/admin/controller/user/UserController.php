<?php

/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-01
 * Time: 上午10:47:18
 * Info:
 */

namespace app\admin\controller\user;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\common\model\User as UserModel;
use app\common\model\UserGrade as UserGradeModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="会员管理")
 * Class Node
 * @package app\admin\controller\user
 */
class UserController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="会员列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $userModel = new UserModel();
            $param     = $this->request->param();

            return $userModel->tableData($param);
        }
        $userGradeModel = new UserGradeModel();
        $userGrade      = $userGradeModel->getAll();
        $this->assign('grade', $userGrade);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加会员")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'username|用户名' => 'require',
                'mobile|手机号'   => 'require|mobile'
            ];
            $this->validate($param, $rule);
            $userModel = new UserModel();

            $result = $userModel->manageAdd($param);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
        $userGradeModel = new UserGradeModel();
        $userGrade      = $userGradeModel->getAll();
        $this->assign('grade', $userGrade);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑会员")
     */
    public function edit()
    {
        $userModel = new UserModel();

        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'username|用户名' => 'require',
                'mobile|手机号'   => 'require|mobile'
            ];
            $this->validate($param, $rule);

            $result = $userModel->manageEdit($param);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
        $user_id = $this->request->param('id');
        $data    = $userModel->getUserInfo($user_id);
        if (empty($data['data'])) {
            $this->error('获取信息失败');
        }
        $userGradeModel = new UserGradeModel();
        $userGrade      = $userGradeModel->getAll();

        $this->assign('data', $data['data']);
        $this->assign('grade', $userGrade);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除会员")
     */
    public function delete()
    {
        $user_id   = $this->request->param('id');
        $userModel = new UserModel();
        $result    = $userModel->manageDel($user_id);
        if ( ! empty($result['code']) && $result['code'] == 200) {
            $this->success($result['msg']);
        } else {
            $this->error($result['msg']);
        }
    }

}