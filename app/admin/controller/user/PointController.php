<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-03
 * Time: 下午3:29:37
 * Info:
 */

namespace app\admin\controller\user;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use think\Exception;
use think\App;
use app\admin\model\UserPointLog as UserPointLogModel;
use app\admin\model\User as UserModel;

/**
 * @ControllerAnnotation(title="积分管理")
 * Class Node
 * @package app\admin\controller\user
 */
class PointController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="积分列表")
     */
    public function index()
    {
        $user_id = $this->request->param('user_id');
        if ($this->request->isAjax()) {
            $userPointModel = new UserPointLogModel();
            $param          = $this->request->param();

            return $userPointModel->tableData($param);
        }
        $this->assign('user_id', $user_id);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="积分修改")
     */
    public function edit()
    {
        $userModel      = new UserModel();
        $userPointModel = new UserPointLogModel();
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $rule  = [
                'user_id|会员id' => 'require',
                'point|变动积分'   => 'require',
                'remarks|变动说明' => 'require'
            ];
            $this->validate($param, $rule);
            $user_id = $param['user_id'];
            $point   = $param['point'];
            $remarks = $param['remarks'];
            $result  = $userPointModel->setPoint($user_id, $point, $userPointModel::POINT_TYPE_ADMIN_EDIT, $remarks);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
        $user_id = $this->request->param('user_id');
        $data    = $userModel->getUserInfo($user_id);
        if (empty($data['data'])) {
            $this->error('获取会员信息失败');
        }
        $this->assign('data', $data['data']);

        return $this->fetch();
    }

}