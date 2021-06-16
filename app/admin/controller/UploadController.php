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

    public function index()
    {
        $option              = [];
        $option['allowtype'] = $this->_get_upload_types();
        $save_path           = Request::param('save_path', 'images');
        $editor_type         = Request::param('editor_type', '');
        switch ($this->upload_mode) {
            case 'local':
                $up_file = request()->file('file');
                switch ($editor_type) {
                    case "iceEditor":
                        $file = $up_file[0];
                        break;
                    default:
                        $file = $up_file;
                        break;
                }
                try {
                    validate([
                        'imgFile' => [
                            'fileSize' => '12345006',
                            'fileExt'  => 'png,jpeg,bmp,jpg,gif,webp',
                        ]
                    ])->check(['imgFile' => $file]);
                    //上传图片到本地服务器
                    $saveName = \think\facade\Filesystem::disk('public')->putFile($save_path, $file);
                    if ( ! $saveName) {
                        return json(['code' => 0, 'msg' => '上传图片失败']);
                    }
                    $saveName = str_replace("\\", "/", $saveName);
                    $savePath = '/uploads/'.$saveName;

                    //水印
                    $this->add_water($savePath);
                    //写入数据库
                    $this->_att_write($file, $savePath);

                    switch ($editor_type) {
                        case "iceEditor":
                            return json([['url' => $savePath, 'name' => $saveName, 'error' => 0]]);
                            break;
                        case "wangEditor":
                            return json([
                                'errno' => 0,
                                'data'  => [['url' => $savePath, 'alt' => $saveName, 'href' => '']]
                            ]);
                            break;
                        default:
                            return json([
                                'code' => 1,
                                'msg'  => '图片上传成功',
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
     */
    public function _att_write($file, $fileName)
    {
        $fileinfo           = Image::open('.'.$fileName);
        $arr                = [];
        $arr['admin_id']    = $this->admin_id;
        $arr['user_id']     = $this->user_id;
        $arr['url']         = $fileName;
        $arr['imagewidth']  = $fileinfo->width();
        $arr['imageheight'] = $fileinfo->height();
        $arr['imagetype']   = $fileinfo->type();
        $arr['filesize']    = $file->getSize();
        $arr['mimetype']    = $fileinfo->mime();
        $arr['extparam']    = json_encode(['name' => $file->getOriginalName(), 'mime' => $file->getOriginalMime()]);
        $arr['createtime']  = time();
        $arr['updatetime']  = time();
        $arr['uploadtime']  = time();
        $arr['storage']     = 'local';
        $arr['sha1']        = $file->hash('sha1');
        //Db::name('attachment')->data($arr)->insert();
    }

    /**
     * 获取上传类型
     */
    private function _get_upload_types()
    {

        $arr   = explode('|', get_config('upload_types'));
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

}