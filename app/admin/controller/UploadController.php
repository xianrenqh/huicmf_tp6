<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-09-29
 * Time: 11:42:28
 * Info:
 */

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use think\Image;
use lib\AliOss;

class UploadController extends BaseController
{

    public $admin_id;

    public $user_id;

    private $upload_mode;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        $this->admin_id    = cmf_get_admin_id() ? cmf_get_admin_id() : '0';
        $this->user_id     = session('user_id') ? session('user_id') : '0';
        $this->upload_mode = get_config('upload_mode', 'local');
    }

    public function index()
    {
        $option              = [];
        $option['allowtype'] = $this->_get_upload_types();
        $save_path           = Request::param('save_path', 'images');
        $editor_type         = Request::param('editor_type', '');
        $groupId             = Request::param('group_id', '0');
        $group_id            = $groupId == '-1' ? 0 : $groupId;

        try {
            if ($editor_type === 'editorMd') {
                $up_file = request()->file();
            } else {
                $up_file = request()->file('file');
            }
        } catch (\think\Exception $e) {
            return json(['code' => 0, 'msg' => $this->_languageChange($e->getMessage())]);
        }

        switch ($editor_type) {
            case "iceEditor":
                $file = $up_file[0];
                break;
            case "editorMd";
                $file = $up_file['editormd-image-file'];
                break;
            case "tinyMce";
                $file = $up_file;
                break;
            default:
                $file = $up_file;
                break;
        }

        try {
            $getMime = $file->getMime();
            if (strstr($getMime, 'image')) {
                //判断是图片
                validate([
                    'imgFile' => [
                        'fileSize' => intval(get_config('upload_maxsize')) * 1000,
                        'fileExt'  => get_config('upload_types_image')
                    ]
                ])->check(['imgFile' => $file]);
                $errorTips = "上传图片失败";
            } else {
                //判断是其他附件
                validate([
                    'imgFile' => [
                        'fileSize' => intval(get_config('upload_maxsize')) * 1000,
                        'fileExt'  => get_config('upload_types_file')
                    ]
                ])->check(['imgFile' => $file]);
                $errorTips = "上传附件失败";
            }
            //上传图片到本地服务器
            try {
                $saveName = \think\facade\Filesystem::disk('public')->putFile($save_path, $file);
            } catch (\think\exception\ValidateException $e) {
                return json($e->getMessage());
            }
            if ( ! $saveName) {
                return json(['code' => 0, 'msg' => $errorTips]);
            }
            $saveName  = str_replace("\\", "/", $saveName);
            $picName   = explode('/', $saveName);
            $picName   = end($picName);
            $savePath  = '/uploads/'.$saveName;
            $savePath2 = 'uploads/'.$saveName;

            if (strstr($getMime, 'image')) {
                //水印-图片
                $this->add_water($savePath);
                //写入数据库
                $this->_att_write($file, $savePath, $picName, $group_id);
            }

            switch ($this->upload_mode) {
                case "ali_oss";
                    $tmp_name = $file->getPathname();
                    $key      = $savePath2;

                    $oss = new AliOss();
                    try {
                        $res      = $oss->upload($key, $tmp_name);
                        $savePath = $res['preview_url'];

                        //删除本地文件
                        @unlink(public_path().$key);
                    } catch (\Exception $e) {
                        return json(['code' => 0, 'msg' => $e->getMessage()]);
                    }
                    break;

            }

            switch ($editor_type) {
                case "iceEditor":
                    return json([['url' => $savePath, 'name' => $picName, 'error' => 0]]);
                    break;
                case "wangEditor":
                    return json([
                        'errno' => 0,
                        'data'  => ['url' => $savePath, 'alt' => $picName, 'href' => '']
                    ]);
                    break;
                case "editorMd";
                    return json(['url' => $savePath, 'message' => '上传成功', 'success' => 1]);
                    break;
                case "tinyMce";
                    return json(['location' => $savePath, 'msg' => '上传成功', 'code' => 1]);
                    break;
                default:
                    return json([
                        'code' => 1,
                        'msg'  => '上传成功',
                        'name' => $picName,
                        'url'  => $savePath
                    ]);
                    break;
            }

        } catch (\think\exception\ValidateException $e) {
            switch ($editor_type) {
                case "iceEditor":
                    return json([
                        [
                            'url'   => '',
                            'name'  => $file->getOriginalName(),
                            'error' => $e->getMessage()
                        ]
                    ]);
                    break;
                case "wangEditor":
                    return json(['errno' => '1', 'msg' => $e->getMessage()]);
                    break;
                case "editorMd";
                    return json(['url' => '', 'message' => $e->getMessage(), 'success' => 0]);
                    break;
                default:
                    return json(['code' => 0, 'msg' => $e->getMessage()]);
                    break;
            }

        }
    }

    //添加水印
    private function add_water($fileName)
    {
        //获取水印配置
        if (get_config('watermark_enable')) {
            $waterpic = "./static/water/".get_config('watermark_name');
            $pic_url  = '.'.$fileName;
            $image    = Image::open($pic_url);
            $image->water($waterpic, get_config('watermark_position'), get_config('watermark_touming'))->save($pic_url);
        }
    }

    //写入数据库
    public function _att_write($file, $fileName, $picName, $group_id)
    {
        if (strstr($fileName, '.gif')) {
            $extension  = 'gif';
            $img_width  = 0;
            $img_height = 0;
        } else {
            $fileinfo   = Image::open('.'.$fileName);
            $extension  = $fileinfo->type();
            $img_width  = $fileinfo->width();
            $img_height = $fileinfo->height();
        }
        $arr = [
            'storage'     => $this->upload_mode,
            'group_id'    => $group_id,
            'file_url'    => $fileName,
            'file_name'   => $picName,
            'file_size'   => $file->getSize(),
            'file_type'   => 'image',
            'extension'   => $extension,
            'sha1'        => $file->hash('sha1'),
            'img_width'   => $img_width,
            'img_height'  => $img_height,
            'create_time' => time()
        ];
        Db::name('upload_file')->data($arr)->insert();
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

    //获取上传文件后缀
    private function get_file_ext($file_name)
    {
        $temp_arr = explode(".", $file_name);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);

        return $file_ext;
    }

    /**
     * 获取上传类型
     */
    private function _get_upload_types()
    {

        $arr   = explode('|', get_config('upload_types_image'));
        $allow = array(
            'gif',
            'jpg',
            'png',
            'jpeg',
            'zip',
            'rar',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'pdf',
            'txt',
            'csv',
            'mp4',
            'avi',
            'wmv',
            'rmvb',
            'flv',
            'mp3',
            'wma',
            'wav',
            'amr',
            'ogg',
            'p12',
            'pem',
            'key'
        );
        foreach ($arr as $key => $val) {
            if ( ! in_array($val, $allow)) {
                unset($arr[$key]);
            }
        }

        return $arr;
    }

    public function fileList()
    {
        $type = Request::param('type', 'many');
        // 分组列表
        $group_list = Db::name('upload_group')->select();
        View::assign('type', $type);
        View::assign('group_list', $group_list);

        return View::fetch();
    }

}
