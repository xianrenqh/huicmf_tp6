通知消息（右上角）
调用方式：
<link rel="stylesheet" href="component/pear/css/pear-module/notice.css">
<script src="component/layui/layui.js"></script>

layui.use(['notice', 'jquery', 'layer'], function() {
    var notice = layui.notice;
                         
    notice.success("成功消息")
    notice.error("危险消息")
    notice.warning("警告消息")
    notice.info("通用消息")
    notice.clear();	//移除
})
