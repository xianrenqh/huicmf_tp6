<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-12-29
 * Time: 上午9:43:00
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use think\App;
use app\common\model\UploadGroup as UploadGroupModel;
use app\common\model\UploadFile as UploadFileModel;

/**
 * @ControllerAnnotation(title="文件库管理")
 * Class Node
 * @package app\admin\controller\system
 */
class UpLibraryController extends AdminController
{

    /**
     * @NodeAnotation(title="文件库列表")
     */
    public function fileList()
    {
        if ($this->request->isAjax()) {
            // 文件列表
            $group_id  = $this->request->param('group_id', 0);
            $page      = $this->request->param('page', 1);
            $limit     = 18;
            $first     = intval(($page - 1) * $limit);
            $total     = UploadFileModel::where(function ($query) use ($group_id) {
                if (isset($group_id) && $group_id != '-1') {
                    $query->where('group_id', $group_id);
                }
            })->where('is_delete', 0)->count();
            $list      = UploadFileModel::where(function ($query) use ($group_id) {
                if (isset($group_id) && $group_id != '-1') {
                    $query->where('group_id', $group_id);
                }
            })->where('is_delete', 0)->limit($first, $limit)->order('file_id desc')->select()->toArray();
            $lastPage  = intval(ceil($total / $limit));
            $perPage   = intval($page - 1);
            $file_list = [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'file_list'    => $list
            ];

            return json(['code' => 1, 'msg' => 'ok', 'data' => $file_list]);
        }
    }

    /**
     * @NodeAnotation(title="分组列表")
     */
    public function groupList()
    {
        $list = UploadGroupModel::select();
        $this->success('ok', $list);
    }

    /**
     * @NodeAnotation(title="新增分组")
     */
    public function addGroup($group_name, $group_type = 'image')
    {
        $model = UploadGroupModel::where('group_name', $group_name)->find();
        if ( ! empty($model)) {
            $this->error('分组名称已存在');
        }
        $data     = [
            'group_type' => $group_type,
            'group_name' => $group_name
        ];
        $rowId    = UploadGroupModel::insertGetId($data);
        $group_id = $rowId;
        if ($rowId) {
            $this->success('添加成功', compact('group_id', 'group_name'));
        } else {
            $this->error('添加失败');
        }
    }

    /**
     * @NodeAnotation(title="编辑分组")
     */
    public function editGroup($group_id, $group_name)
    {
        $getRow = UploadGroupModel::find($group_id);
        if (empty($getRow)) {
            $this->error('获取数据失败');
        }
        $getRow->group_name = $group_name;
        $row                = $getRow->save();

        if ($row) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    /**
     * @NodeAnotation(title="删除分组")
     */
    public function deleteGroup($group_id)
    {
        if (empty($group_id)) {
            $this->error('分组id不能为空');
        }
        $del = UploadGroupModel::destroy($group_id);
        if ($del) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * @NodeAnotation(title="删除文件")
     */
    public function deleteFiles()
    {
        $fieldIds = $this->request->param('fileIds');
        if (empty($fieldIds)) {
            $this->error('您没有选择任何文件~');
        }
        UploadFileModel::whereIn('file_id', $fieldIds)->data(['is_delete' => 1])->update();
        $this->success('删除成功~');
    }

    /**
     * @NodeAnotation(title="批量移动文件分组")
     */
    public function moveFiles()
    {
        $groupId  = $this->request->param('group_id', 0);
        $fieldIds = $this->request->param('fileIds');
        if (empty($fieldIds)) {
            $this->error('您没有选择任何文件~');
        }
        UploadFileModel::whereIn('file_id', $fieldIds)->data(['group_id' => $groupId])->update();
        $this->success('移动分组成功~');
    }

    /**
     * 英文转为中文
     */
    private function _languageChange($msg)
    {
        $data = [
            // 上传错误信息
            'unknown upload error'                       => '未知上传错误！',
            'file write error'                           => '文件写入失败！',
            'upload temp dir not found'                  => '找不到临时文件夹！',
            'no file to uploaded'                        => '没有文件被上传！',
            'only the portion of file is uploaded'       => '文件只有部分被上传！',
            'upload File size exceeds the maximum value' => '上传文件大小超过了最大值！',
            'upload write error'                         => '文件上传保存错误！',
        ];

        return $data[$msg] ?? $msg;
    }

}
