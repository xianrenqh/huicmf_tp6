<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-03-30
 * Time: 16:08:35
 * Info:
 */

namespace app\admin\controller\module;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\common\model\UploadFile as UploadFileModel;
use app\common\model\UploadGroup as UploadGroupModel;
use think\Exception;
use think\App;

/**
 * @ControllerAnnotation(title="图片管理")
 * Class Node
 * @package app\admin\controller\module
 */
class ImagesController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new UploadFileModel();
    }

    /**
     * @NodeAnotation(title="图片列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();

            return $this->model->tableData($param);
        }
        $uploadGroupList = UploadGroupModel::group_list();
        $this->assign('group_list', $uploadGroupList);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除图片")
     */
    public function delete()
    {
        if ($this->request->isAjax()) {
            $file_id = $this->request->param('file_id');

            $result = $this->model->manageDel($file_id);
            if ( ! empty($result['code']) && $result['code'] == 200) {
                $this->success($result['msg']);
            } else {
                $this->error($result['msg']);
            }
        }
    }

}