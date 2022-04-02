<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-09
 * Time: 17:17:49
 * Info:
 */

namespace app\admin\controller\system;

use app\common\controller\AdminController;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\App;
use app\admin\library\Backup;
use think\Exception;
use think\facade\Db;
use app\common\traits\DebugTrait;
use ZipArchive;

/**
 * @ControllerAnnotation(title="数据库管理")
 * Class Node
 * @package app\admin\controller\system
 */
class DatabaseController extends AdminController
{

    protected $noNeedRight = ['backuplist'];

    /**
     * @NodeAnotation(title="数据表列表")
     */
    public function index()
    {
        $list = Db::query("SHOW TABLE STATUS");
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['Data_length'] = sizecount($list[$i]['Data_length']);
        }
        if ($this->request->isAjax()) {
            $return = ["code" => 0, 'msg' => '获取成功', 'count' => count($list), 'data' => $list];

            return json($return);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="数据备份")
     */
    public function backup()
    {
        $DataBase  = config('database');
        $CAdmin    = config('admin');
        $backupDir = root_path()."public".DS.$CAdmin['backupDir'];
        if ($this->request->isAjax()) {
            try {
                $backup = new Backup($DataBase['connections']['mysql']['hostname'],
                    $DataBase['connections']['mysql']['username'], $DataBase['connections']['mysql']['database'],
                    $DataBase['connections']['mysql']['password'], $DataBase['connections']['mysql']['hostport']);
                $backup->setIgnoreTable($CAdmin['backupIgnoreTables'])->backup($backupDir);
            } catch (Exception $e) {
                return json(['msg' => $e->getMessage(), 'status' => 0]);
            }

            return json(['msg' => '备份成功', 'status' => 1]);
        }

        return;
    }

    /**
     * @NodeAnotation(title="数据还原")
     */
    public function restore()
    {
        $BackUp    = config('admin');
        $backupDir = root_path()."public".DS.$BackUp['backupDir'];
        $file      = $this->request->param('file');
        if ($file) {
            try {
                $dir = public_path().'databak'.DS;
                if ( ! is_dir($dir)) {
                    mkdir($dir, 0755);
                }
                $file = $backupDir.$file;
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive;
                    if ($zip->open($file) !== true) {
                        throw new Exception('无法打开备份文件');
                    }
                    if ( ! $zip->extractTo($dir)) {
                        $zip->close();
                        throw new Exception('无法解压备份文件');
                    }

                    $filename = basename($file);
                    $sqlFile  = $dir.str_replace('.zip', '.sql', $filename);
                    if ( ! is_file($sqlFile)) {
                        throw new Exception('未找到SQL文件');
                    }
                    $filesize = filesize($sqlFile);
                    $list     = Db::query('SELECT @@global.max_allowed_packet');

                    if (isset($list[0]['@@global.max_allowed_packet']) && $filesize >= $list[0]['@@global.max_allowed_packet']) {
                        Db::execute('SET @@global.max_allowed_packet = '.($filesize + 1024));
                    }
                    $sql = file_get_contents($sqlFile);

                    if (preg_match('/.*;$/', trim($sql))) {
                        try {
                            $sqlArr = array_filter(explode(";\n\n", trim($sql)));
                            foreach ($sqlArr as $k => $v) {
                                $res = Db::execute($v);
                            }
                            unlink($sqlFile);
                            $this->success('还原成功！！！');
                        } catch (Exception $e) {
                            $this->error($e->getMessage());
                        }
                    } else {
                        return 0;
                    }
                }
                $this->error('非zip包');
            } catch (Exception $e) {
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }
        }
        $this->error('错错错，是我的错');
    }

    /**
     * @NodeAnotation(title="备份列表")
     */
    public function databack_list()
    {
        $BackUp     = config('admin');
        $backupDir  = root_path()."public".DS.$BackUp['backupDir'];
        $backuplist = [];
        foreach (glob($backupDir."*.zip") as $filename) {
            $time              = filemtime($filename);
            $backuplist[$time] = [
                'file' => str_replace($backupDir, '', $filename),
                'date' => date("Y-m-d H:i:s", $time),
                'size' => sizecount(filesize($filename))
            ];

        }
        krsort($backuplist);
        $this->assign('data', $backuplist);

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除备份")
     */
    public function delete()
    {
        $BackUp    = config('admin');
        $backupDir = root_path()."public".DS.$BackUp['backupDir'];
        $file      = $backupDir.input('file');
        if (file_exists($file)) {
            unlink($file);

            return json(['msg' => '删除成功', 'code' => 1]);
        } else {
            return json(['msg' => '删除失败', 'code' => 0]);
        }
    }

    /**
     * @NodeAnotation(title="下载备份")
     */
    public function download()
    {
        $BackUp    = config('admin');
        $backupDir = root_path()."public".DS.$BackUp['backupDir'];
        $file      = input('file');
        if (file_exists($backupDir.$file)) {
            file_down($backupDir.$file, $file);
        }
    }

    /**
     * @NodeAnotation(title="优化表")
     */
    public function optimize()
    {
        $name = $this->request->param('table');
        if (is_array($name)) {
            foreach ($name as $key => $row) {
                Db::execute("OPTIMIZE TABLE `{$row}`");
            }
            $return = ['msg' => '全部优化成功', 'icon' => 1];
        } else {
            if (Db::execute("OPTIMIZE TABLE `{$name}`")) {
                $return = ['msg' => '优化表 ['.$name.'] 成功', 'icon' => 1];
            } else {
                $return = ['msg' => '优化表 ['.$name.'] 失败', 'icon' => 2];
            }
        }

        return json($return);
    }

    /**
     * @NodeAnotation(title="修复表")
     */
    public function repair()
    {
        $name = $this->request->param('table');
        if (is_array($name)) {
            foreach ($name as $key => $row) {
                Db::execute("REPAIR TABLE `{$row}`");
            }
            $return = ['msg' => '全部修复成功', 'icon' => 1];
        } else {
            if (Db::execute("REPAIR TABLE `{$name}`")) {
                $return = ['msg' => '修复表 ['.$name.'] 成功', 'icon' => 1];
            } else {
                $return = ['msg' => '修复表 ['.$name.'] 成功', 'icon' => 1];
            }
        }

        return json($return);
    }

    /**
     * @NodeAnotation(title="查看表结构")
     */
    public function table_structure()
    {
        $name = $this->request->param('table');
        $row  = Db::query("SHOW CREATE TABLE `{$name}`");
        $row  = array_values($row[0]);
        $info = $row[1];
        echo "<style>pre {display: block;font-family: Monaco,Menlo,Consolas,\"Courier New\",monospace;padding: 9.5px;margin-bottom: 10px;font-size: 12px;line-height: 20px;word-break: break-all;word-wrap: break-word;white-space: pre;white-space: pre-wrap;background-color: #f5f5f5;border: 1px solid #ccc;border-radius: 4px;color: #333;}</style>";
        echo "<pre>{$info};</pre>";
    }

    /**
     * @NodeAnotation(title="查看表数据")
     */
    public function table_data()
    {
        $name     = $this->request->param('table');
        $row      = Db::query("SHOW CREATE TABLE `{$name}`");
        $sqlquery = "SELECT * FROM `{$name}`";
        $this->do_query($sqlquery);
    }

    private function do_query($sql = null)
    {

        $sqlquery = $sql ? $sql : $this->request->post('sqlquery');
        if ($sqlquery == '') {
            exit(__('SQL can not be empty'));
        }
        $sqlquery  = str_replace("\r", "", $sqlquery);
        $sqls      = preg_split("/;[ \t]{0,}\n/i", $sqlquery);
        $maxreturn = 100;
        $r         = '';
        foreach ($sqls as $key => $val) {
            if (trim($val) == '') {
                continue;
            }
            $val = rtrim($val, ';');
            $r   .= "SQL：<span style='color:green;'>{$val}</span> ";
            if (preg_match("/^(select|explain)(.*)/i ", $val)) {
                DebugTrait::remark("begin");
                $limit = stripos(strtolower($val), "limit") !== false ? true : false;
                $count = Db::execute($val);
                if ($count > 0) {
                    $resultlist = Db::query($val.(! $limit && $count > $maxreturn ? ' LIMIT '.$maxreturn : ''));
                } else {
                    $resultlist = [];
                }
                DebugTrait::remark("end");
                $time     = DebugTrait::getRangeTime('begin', 'end', 4);
                $rang_mem = DebugTrait::getRangeMem('begin', 'end', 4);

                $usedseconds = "用时 ".$time." 秒；";
                $members     = "消耗内存 ".$rang_mem." <br />";
                if ($count <= 0) {
                    $r .= '返回结果为空';
                } else {
                    $r .= ('共有'.$count.'条记录!').(! $limit && $count > $maxreturn ? ',最大返回'.$maxreturn.'条' : '');
                }
                $r = $r.','.$usedseconds.$members;
                $j = 0;
                foreach ($resultlist as $m => $n) {
                    $j++;
                    if ( ! $limit && $j > $maxreturn) {
                        break;
                    }
                    $r .= "<hr/>";
                    $r .= "<font color='red'> 记录".$j."</font><br />";
                    foreach ($n as $k => $v) {
                        $r .= "<font color='blue'>{$k}：</font>{$v}<br/>\r\n";
                    }
                }
            } else {
                DebugTrait::remark("begin");
                $count = Db::execute($val);
                DebugTrait::remark("end");
                $time     = DebugTrait::getRangeTime('begin', 'end', 4);
                $rang_mem = DebugTrait::getRangeMem('begin', 'end', 4);
                $r        .= __('Query affected %s rows and took %s seconds', $count, $time, $rang_mem)."<br />";
            }
        }
        echo $r;

    }

}