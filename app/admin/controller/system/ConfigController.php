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
use app\common\model\Config as ConfigModel;
use app\admin\service\TriggerService;
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
        $cacheData = cache('sysConfig');
        if ($cacheData) {
            $datalist = $cacheData;
        } else {
            $datalist = ConfigModel::select()->toArray();
            cache('sysConfig', $datalist);
        }
        $data = array_column($datalist, 'value', 'name');
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
            $msg   = "保存成功";
            $url   = "";
            $url2  = "";

            $clearCacheAll = false;
            foreach ($param as $key => $value) {
                $arr[$key]           = $value;
                $value               = htmlspecialchars($value);
                $oldAdminUrlPassrowd = get_config('admin_url_password');
                if ($key == 'admin_url_password') {
                    if ($oldAdminUrlPassrowd != $value) {
                        $msg = "您修改了后台加密码，请退出重新登录!";
                        rename(ROOT_PATH.'/public/'.$oldAdminUrlPassrowd.'.php', $value.'.php');
                        $url           = cmf_get_domain()."/".$value.'.php';
                        $clearCacheAll = true;
                        $url2          = $url.'/login/index';
                    }
                }
                ConfigModel::strict(false)->where(['name' => $key])->data(['value' => $value])->update();
            }
            TriggerService::updateSysconfig($clearCacheAll);

            $this->success($msg, '', $url2);
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
                TriggerService::updateSysconfig();
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
            return json(['code' => 0, 'msg' => '获取数据失败']);
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
            TriggerService::updateSysconfig();
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
        TriggerService::updateSysconfig();
        $this->success('操作成功');
    }

}
