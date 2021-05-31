layui.define(['jquery', 'form', 'layer', 'element'], function (exports) {
  var $ = layui.jquery,
    form = layui.form,
    layer = layui.layer;

  $('.fa-refresh').click(function () {
    window.location.reload();
  });

  // 监听弹出层的打开
  $('body').on('click', '[data-open]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-open');
    HuiAdminOpenFull(title, url);
  });

  $('body').on('click', '[data-delete]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-delete');
    HuiAdminDel(url, title);
  });


  /*满屏（全屏）打开窗口*/
  window.HuiAdminOpenFull = function (title, url) {
    $.get(url, function (res) {
      var index = layer.open({
        type: 2,
        title: title,
        content: url,
        skin: 'skin-layer-hui'
      });
      layer.full(index);
    });
  }

  /*
    * @todo 弹出层，弹窗方法
    * layui.use 加载layui.define 定义的模块，当外部 js 或 onclick调用 use 内部函数时，需要在 use 中定义 window 函数供外部引用
    * http://blog.csdn.net/xcmonline/article/details/75647144
    */
  /*
      参数解释：
      title   标题
      url     请求的url
      id      需要操作的数据id
      w       弹出层宽度（缺省调默认值）
      h       弹出层高度（缺省调默认值）
  */
  window.HuiAdminShow = function (title, url, w, h) {
    if (title == null || title == '') {
      title = false;
    }
    ;
    if (url == null || url == '') {
      url = "404.html";
    }
    ;
    if (w == null || w == '') {
      w = ($(window).width() * 0.9);
    }
    ;
    if (h == null || h == '') {
      h = ($(window).height() * 0.8);
    }
    ;
    $.get(url, function (res) {
      layer.open({
        type: 2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        maxmin: true,
        shadeClose: false,
        shade: 0.4,
        title: title,
        content: url
      });
    });

  }
  /**
   * 提示弹出
   */
  window.HuiAdminConfirm = function (url, msg = '真的要这样操作么？') {
    layer.confirm(msg, {skin: 'skin-layer-hui'}, function (index) {
      $.post(url, function (res) {
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1});
        } else {
          layer.msg(res.msg, {icon: 2});
        }
      })
    });
  }

  /*删除弹出提示*/
  window.HuiAdminDel = function (url, msg = '真的要删除么？') {
    layer.confirm(msg, {skin: 'skin-layer-hui'}, function (index) {
      $.post(url, function (res) {
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1});
        } else {
          layer.msg(res.msg, {icon: 2});
        }
      })
    });
  }

});
