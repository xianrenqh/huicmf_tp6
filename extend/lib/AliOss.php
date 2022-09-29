<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2022-09-29
 * Time: 10:56:01
 * Info: 阿里oss上传
 */

namespace lib;

use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\OssClient;
use think\Exception;

class AliOss
{

    private $config;

    private $storageRoot;

    private $plugin;

    private $ossClient;

    public function __construct()
    {
        $config = config('ali_oss');

        $accessKeyId     = $config['access_id'];
        $accessKeySecret = $config['access_secret'];
        $endpoint        = $config['end_point'];
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            throw new Exception($e->getMessage());
        }
        $this->ossClient = $ossClient;
        $this->config    = $config;

        $domain            = $config['domain'];
        $this->storageRoot = $this->config['protocol'].'://'.$domain.'/';
    }

    /**
     * 文件上传
     *
     * @param string $file     上传文件路径
     * @param string $filePath 文件路径相对于upload目录
     * @param string $fileType 文件类型,image,video,audio,file
     * @param null   $param    额外参数
     *
     * @return array
     * @throws Exception
     */
    public function upload($file, $filePath, $fileType = 'image', $param = null)
    {
        $watermark = '';
        $bucket    = $this->config['bucket'];

        try {
            $this->ossClient->uploadFile($bucket, $file, $filePath);
        } catch (OssException $e) {
            throw new Exception('code is '.$e->getErrorCode().' and msg is '.$e->getMessage());
        }

        $previewUrl = $fileType == 'image' ? $this->getPreviewUrl($file, $watermark) : $this->getFileDownloadUrl($file);
        $url        = $fileType == 'image' ? $this->getImageUrl($file, $watermark) : $this->getFileDownloadUrl($file);

        return [
            'preview_url' => $previewUrl,
            'url'         => $url
        ];
    }

    /**
     * 获取图片预览地址
     *
     * @param string $file
     * @param string $style
     *
     * @return mixed
     */
    public function getPreviewUrl($file, $style = '')
    {
        $url = $this->getUrl($file, $style);

        return $url;
    }

    public function getImageUrl($file, $style = '')
    {
        $config = $this->config;
        $url    = $this->storageRoot.$file;

        if ( ! empty($style)) {
            $url = $url.$config['style_separator'].$style;
        }

        return $url;
    }

    /**
     * 获取文件地址
     *
     * @param string $file
     * @param string $style
     *
     * @return mixed
     */
    public function getUrl($file, $style = '')
    {
        $config = $this->config;
        $url    = $this->storageRoot.$file;

        return $url;
    }

    /**
     * 获取文件下载地址
     *
     * @param string $file
     * @param int    $expires
     *
     * @return mixed
     */
    public function getFileDownloadUrl($file, $expires = 3600)
    {
        $url = '';

        return $url;
    }

}
