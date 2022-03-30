<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-15
 * Time: 18:09:06
 * Info:
 */

namespace lib;

class Form
{

    /**
     * 富文本编辑器
     *
     * @param $name   name
     * @param $style  样式
     * @param $isload 是否加载js,当该页面加载过编辑器js后，无需重复加载
     */
    public static function editor($name = 'content', $val = '', $style = '', $isload = true): string
    {
        $val        = htmlspecialchars_decode($val);
        $editorType = get_config('site_editor');
        switch ($editorType) {
            case 'uEditor';
                $res = self::editor_uEditor($name, $val, $isload, $style);
                break;
            case 'uEditorMini';
                $res = self::editor_uEditorMini($name, $val, $isload, $style);
                break;
            case 'tinyMCE';
                $res = self::editor_tinyMCE($name, $val, $isload, $style);
                break;
            case 'iceEditor';
                $res = self::editor_iceEditor($name, $val, $isload, $style);
                break;
            default:
                $res = self::editor_uEditorMini($name, $val, $isload, $style);
                break;
        }

        return $res;

    }

    /**
     * MD编辑器
     *
     * @param string $name
     * @param string $val
     *
     * @return string
     */
    public static function editorMd($name = 'content', $val = '')
    {
        $css1     = DS.'static'.DS.'lib'.DS.'editor.md-1.5.0'.DS.'css'.DS.'editormd.min.css';
        $jquery   = DS.'static'.DS.'lib'.DS.'jquery-3.4.1'.DS.'jquery-3.4.1.min.js';
        $editormd = DS.'static'.DS.'lib'.DS.'editor.md-1.5.0'.DS.'editormd.min.js';
        $libPath  = '/static/lib/editor.md-1.5.0/lib/';
        $string   = '';
        $string   .= '<link href="'.$css1.'" rel="stylesheet">';
        $string   .= '<div id="content-editor">';
        $string   .= '<textarea style="display:none;" name="'.$name.'">'.$val.'</textarea>';
        $string   .= '</div>';
        $string   .= '<script type="text/javascript" charset="utf-8" src="'.$jquery.'"></script>';
        $string   .= '<script type="text/javascript" charset="utf-8" src="'.$editormd.'"></script>';
        $string   .= '<script type="text/javascript">';
        $string   .= '$(function() {
            var editor = editormd("content-editor", {
            path   : "'.$libPath.'",
            height : "500px",
            imageUpload : true,
            autoFocus : false,
            //theme : "dark",
            imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
            imageUploadURL : "'.__url('upload/index', ['editor_type' => 'editorMd']).'",
            saveHTMLToTextarea : true, // 保存 HTML 到 Textarea
        });
        })';
        $string   .= '</script>';

        return $string;
    }

    /**
     * 百度编辑器
     *
     * @param $name
     * @param $val
     * @param $isLoad
     */
    private static function editor_uEditor($name, $val, $isLoad, $style = '')
    {
        $configJs = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.config.js';
        $Js2      = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.all.js';
        $string   = '';
        $string   .= '<script id="container" name="'.$name.'" type="text/plain" style="'.$style.'" >'.$val.'</script>';
        $string   .= '<script type="text/javascript" src="'.$configJs.'"></script>';
        $string   .= '<script type="text/javascript" src="'.$Js2.'"></script>';
        $string   .= '<script type="text/javascript">';
        $string   .= "var ue = UE.getEditor('container');";
        $string   .= '</script>';

        return $string;
    }

    private static function editor_uEditorMini($name, $val, $isLoad, $style = '')
    {
        $configJs = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.config.js';
        $Js2      = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.all.js';
        $string   = '';
        $string   .= '<script id="container" name="'.$name.'" type="text/plain" style="'.$style.'" >'.$val.'</script>';
        $string   .= '<script type="text/javascript" src="'.$configJs.'"></script>';
        $string   .= '<script type="text/javascript" src="'.$Js2.'"></script>';
        $string   .= '
			<script type="text/javascript"> var ue = UE.getEditor("container",{
            toolbars:[[ "fullscreen","source","|","bold","italic","underline","blockquote","forecolor","|",
            "removeformat", "formatmatch", "autotypeset","fontfamily","fontsize","|",
            "simpleupload","insertimage","insertcode","|","link","unlink","emotion","date","time","drafts","|",
            "preview", "searchreplace", "help"]],
            //关闭elementPath
            elementPathEnabled:false,
            //serverUrl :\''.__url('upload/index', ['editor_type' => 'iceEditor']).'\'
        }); </script>';

        return $string;
    }

    private static function editor_tinyMCE($name, $val, $isLoad, $style = '')
    {
        //$libDir  = '/static/lib/tinymce-6.0.1/tinymce.min.js';
        $libDir = DS.'static'.DS.'lib'.DS.'tinymce-6.0.1'.DS.'tinymce.min.js';
        $string = '';
        $string .= '<script type="text/javascript" charset="utf-8" src="'.$libDir.'"></script>';
        $string .= '<script>
        tinymce.init({
            selector: "#tinyMCEArea",
            images_upload_url:"'.__url('upload/index', ['editor_type' => 'tinyMce']).'",
            branding: false, //隐藏右下角powerby
            language:"zh_CN",
            inline: false,//开启内联模式
            plugins: "code quickbars preview searchreplace autolink fullscreen image link media codesample table charmap advlist lists wordcount emoticons",
            quickbars_selection_toolbar: "bold italic forecolor | link blockquote quickimage",
        });
            </script>';
        $string .= '<textarea id="tinyMCEArea" name="'.$name.'">'.$val.'</textarea>';

        return $string;
    }

    private static function editor_iceEditor($name, $val, $isLoad, $style = '')
    {
        $Codecss = DS.'static'.DS.'lib'.DS.'iceEditor'.DS.'iceCode.css';
        $libDir  = DS.'static'.DS.'lib'.DS.'iceEditor'.DS.'iceEditor.min.js';
        $string  = '';
        if ($isLoad) {
            $string .= '<link href="'.$Codecss.'" rel="stylesheet">';
            $string .= '<script type="text/javascript" charset="utf-8" src="'.$libDir.'"></script>';
        }
        $string .= '<textarea id="editor" name="'.$name.'">'.$val.'</textarea>';
        $string .= '<script>
                iceEditor = new ice.editor("editor");
                iceEditor.uploadUrl="'.__url('upload/index', ['editor_type' => 'iceEditor']).'";
                iceEditor.create();
                </script>
                ';

        return $string;
    }

}