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
     * 编辑器
     *
     * @param $name   name
     * @param $style  样式
     * @param $isload 是否加载js,当该页面加载过编辑器js后，无需重复加载
     */
    public static function editor($name = 'content', $val = '', $style = '', $isload = true)
    {
        $val = htmlspecialchars_decode($val);
        $editorType = get_config('site_editor');
        switch ($editorType) {
            case 'uEditor';
                $res = self::editor_uEditor($name, $val, $isload);
                break;
            case 'uEditorMini';
                $res = self::editor_uEditorMini($name, $val, $isload);
                break;
            case 'iceEditor';
                $res = self::editor_iceEditor($name, $val, $isload);
                break;
            default:
                $res = self::editor_uEditorMini($name, $val, $isload);
                break;
        }

        return $res;

    }

    /**
     * 百度编辑器
     *
     * @param $name
     * @param $val
     * @param $isLoad
     */
    private static function editor_uEditor($name, $val, $isLoad)
    {
        $configJs = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.config.js';
        $Js2      = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.all.js';
        $style    = 'width:100%;height:400px';
        $string   = '';
        $string   .= '<script id="container" name="content" type="text/plain" style="'.$style.'" >'.$val.'</script>';
        $string   .= '<script type="text/javascript" src="'.$configJs.'"></script>';
        $string   .= '<script type="text/javascript" src="'.$Js2.'"></script>';
        $string   .= '<script type="text/javascript">';
        $string   .= "var ue = UE.getEditor('container');";
        $string   .= '</script>';

        return $string;
    }

    private static function editor_uEditorMini($name, $val, $isLoad)
    {
        $configJs = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.config.js';
        $Js2      = DS.'static'.DS.'lib'.DS.'ueditor-1.4.3.3'.DS.'ueditor.all.js';
        $style    = 'width:100%;height:400px';
        $string   = '';
        $string   .= '<script id="container" name="content" type="text/plain" style="'.$style.'" >'.$val.'</script>';
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
            //serverUrl :\''.url("ueditor/index").'\'
        }); </script>';

        return $string;
    }

    private static function editor_iceEditor($name, $val, $isLoad)
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