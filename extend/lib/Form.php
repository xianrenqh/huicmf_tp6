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
        $editorType = 'wangEditor';
        switch ($editorType) {
            case 'iceEditor';
                $res = self::editor_iceEditor($name, $val, $isload);
                break;
            case 'wangEditor';
                $res = self::editor_wangEditor($name, $val, $isload);
                break;
            default:
                $res = self::editor_iceEditor($name, $val, $isload);
                break;
        }

        return $res;

    }

    private static function editor_iceEditor($name, $val, $isLoad)
    {
        $libDir = DS.'static'.DS.'lib'.DS.'iceEditor'.DS.'iceEditor.min.js';
        $string = '';
        if ($isLoad) {
            $string .= '<script type="text/javascript" charset="utf-8" src="'.$libDir.'"></script>';
        }
        $string .= '<textarea id="editor" name="'.$name.'">'.$val.'</textarea>';
        $string .= '<script>iceEditor = new ice.editor("editor");iceEditor.create();</script>';

        return $string;
    }

    private static function editor_wangEditor($name, $val, $isLoad)
    {
        $jquyer = DS.'static'.DS.'lib'.DS.'jquery-3.4.1'.DS.'jquery-3.4.1.min.js';
        $libDir = DS.'static'.DS.'lib'.DS.'wangEditor-4.7.3'.DS.'wangEditor.js';
        $string = '';
        if ($isLoad) {
            $string .= '<script type="text/javascript" charset="utf-8" src="'.$jquyer.'"></script>';
            $string .= '<script type="text/javascript" charset="utf-8" src="'.$libDir.'"></script>';
        }
        $string .= '<div id="editor"></div>';
        $string .= '<textarea name="content" id="editor_text" cols="30" class="layui-textarea "></textarea>';
        $string .= "";
        $string .= <<<eof
<script type="text/javascript">
        var E = window.wangEditor
        var editor = new E('#editor')
        var text1 = $('#editor_text')
        editor.customConfig.onchange = function (html) {
            text1.val(html)
        }
        editor.create()
        text1.val(editor.txt.html())
    </script>
eof;

        return $string;
    }

}