<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-17
 * Time: 14:39:03
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use app\common\controller\AdminController;
use app\admin\model\Config as ConfigModel;
use think\Exception;

/**
 * @ControllerAnnotation(title="系统设置")
 * Class Node
 * @package app\admin\controller\system
 */
class ConfigController extends AdminController
{

    /**
     * @NodeAnotation(title="系统设置")
     */
    public function index()
    {
        $datalist = ConfigModel::select()->toArray();
        $data     = array_column($datalist, 'value', 'name');
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="保存设置")
     */
    public function save()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            foreach ($param as $key => $value) {
                $arr[$key] = $value;
                $value     = htmlspecialchars($value);
                ConfigModel::strict(false)->where(['name' => $key])->update(['value' => $value]);
            }
            $this->success('保存成功');
        }
    }

    /**
     * @NodeAnotation(title="自定义配置")
     */
    public function custom_config()
    {
        $data = ConfigModel::where('type', '99')->paginate(10);
        $this->assign('data', $data);

        return $this->fetch();

    }

    /**
     * @NodeAnotation(title="添加自定义配置")
     */
    public function custom_config_add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $cha   = ConfigModel::where('name', $param['name'])->find();
            if (is_array($cha)) {
                $this->error('配置名称已存在，请修改');
            }
            if ($param['fieldtype'] == "radio" || $param['fieldtype'] == "select") {
                $setting = json_encode(explode('|', rtrim($param['setting'][$param['fieldtype']], '|')));
            } else {
                $setting = "";
            }
            $data = [
                'name'      => $param['name'],
                'fieldtype' => $param['fieldtype'],
                'type'      => 99,
                'title'     => $param['title'],
                'status'    => 1,
                'value'     => $param['value'][$param['fieldtype']],
                'setting'   => $setting
            ];
            try {
                ConfigModel::insert($data);
            } catch (Exception $e) {
                $this->error('操作失败<br>'.$e->getMessage());
            }
            $this->success('操作成功');

        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改自定义配置")
     */
    public function custom_config_edit()
    {
        $id   = $this->request->param('id');
        $data = ConfigModel::where('id', $id)->where('type', 99)->find();
        if (empty($data)) {
            return json(['code'=>0,'msg'=>'获取数据失败']);
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            if ($data['fieldtype'] == 'radio' || $data['fieldtype'] == 'select') {
                $setting = json_decode($data['setting'], true);
                $data    = [
                    'title'  => $param['title'],
                    'value'  => $setting[$param['value']],
                    'status' => $param['status']
                ];
            } else {
                $data = ['title' => $param['title'], 'value' => $param['value'], 'status' => $param['status']];
            }
            ConfigModel::where('id', $param['id'])->data($data)->update();
            $this->success('修改成功');
        }
        if ($data['fieldtype'] == 'radio') {
            $setting      = json_decode($data['setting'], true);
            $setting_data = "";
            foreach ($setting as $k => $v) {
                $checked      = $data['value'] == $v ? "checked" : '';
                $setting_data .= '<input type="radio" name="value" value="'.$k.'" title="'.$v.'" '.$checked.'>';
            }
        } elseif ($data['fieldtype'] == 'select') {
            $setting      = json_decode($data['setting'], true);
            $setting_data = "";
            foreach ($setting as $k => $v) {
                $selected     = $data['value'] == $v ? "selected" : '';
                $setting_data .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
            }
        } else {
            $setting_data = '';
        }
        $this->assign('data', $data);
        $this->assign('setting_data', $setting_data);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除自定义配置")
     */
    public function custom_config_delete()
    {
        $id = $this->request->param('id');
        ConfigModel::where('id', $id)->delete();
        $this->success('操作成功');
    }

}