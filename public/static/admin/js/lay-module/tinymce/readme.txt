layui.use(['tinymce'], function() {
    var tinymce = layui.tinymce
    var edit = tinymce.render({
        elem: "#edit",
        height: 400
    });
    edit.getContent()
});