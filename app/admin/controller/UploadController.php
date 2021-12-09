<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-16
 * Time: 15:59:28
 * Info: 上传类控制器
 */

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use think\Image;

class UploadController
{

    public $admin_id;

    public $user_id;

    private $upload_mode;

    public function __construct()
    {
        $this->admin_id    = cmf_get_admin_id() ? cmf_get_admin_id() : '0';
        $this->user_id     = session('user_id') ? session('user_id') : '0';
        $this->upload_mode = 'local';
    }

    /**
     * 普通图片上传
     * @return \think\response\Json|void
     */
    public function index()
    {
        $option              = [];
        $option['allowtype'] = $this->_get_upload_types();
        $save_path           = Request::param('save_path', 'images');
        $editor_type         = Request::param('editor_type', '');
        switch ($this->upload_mode) {
            case 'local':
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
                    default:
                        $file = $up_file;
                        break;
                }
                try {
                    $getMime = $up_file->getMime();
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
                    $saveName = str_replace("\\", "/", $saveName);
                    $savePath = '/uploads/'.$saveName;

                    if (strstr($getMime, 'image')) {
                        //水印-图片
                        $this->add_water($savePath);
                        //写入数据库
                        $this->_att_write($file, $savePath);
                    }

                    switch ($editor_type) {
                        case "iceEditor":
                            return json([['url' => $savePath, 'name' => $saveName, 'error' => 0]]);
                            break;
                        case "wangEditor":
                            return json([
                                'errno' => 0,
                                'data'  => ['url' => $savePath, 'alt' => $saveName, 'href' => '']
                            ]);
                            break;
                        case "editorMd";
                            return json(['url' => $savePath, 'message' => '上传成功', 'success' => 1]);
                            break;
                        default:
                            return json([
                                'code' => 1,
                                'msg'  => '上传成功',
                                'name' => $saveName,
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
                break;
        }
    }

    /**
     * 图像裁剪
     */
    public function img_cropper()
    {

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

    //添加水印
    private function add_water($fileName)
    {
        //获取水印配置
        if (get_config('watermark_enable')) {
            $waterpic = "./static/water/".get_config('watermark_name');
            $pic_url  = '.'.$fileName;
            $image    = Image::open($pic_url);
            $image->water($waterpic, get_config('watermark_position'), get_config('watermark_touming'))->save($pic_url);
        } else {
            return;
        }
    }

    /**
     * 上传附件写入数据库
     * 默认方法对gif报错，改后无法获取gif的长宽
     */
    public function _att_write($file, $fileName)
    {
        $arr                = [];
        $arr['admin_id']    = $this->admin_id;
        $arr['user_id']     = $this->user_id;
        $arr['url']         = $fileName;
        $arr['imagewidth']  = 0;
        $arr['imageheight'] = 0;
        $arr['imagetype']   = '';
        $arr['mimetype']    = '';
        if (strstr($fileName, '.gif')) {
            $arr['imagetype'] = 'gif';
            $arr['mimetype']  = 'image/gif';
        } else {
            $fileinfo           = Image::open('.'.$fileName);
            $arr['imagewidth']  = $fileinfo->width();
            $arr['imageheight'] = $fileinfo->height();
            $arr['imagetype']   = $fileinfo->type();
            $arr['mimetype']    = $fileinfo->mime();
        }
        $arr['filesize']   = $file->getSize();
        $arr['extparam']   = json_encode(['name' => $file->getOriginalName(), 'mime' => $file->getOriginalMime()]);
        $arr['createtime'] = time();
        $arr['updatetime'] = time();
        $arr['uploadtime'] = time();
        $arr['storage']    = 'local';
        $arr['sha1']       = $file->hash('sha1');
        Db::name('attachment')->data($arr)->insert();
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