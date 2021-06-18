layui.define(['jquery', 'form', 'layer', 'element', 'table', 'iconPickerFa', 'upload'], function (exports) {
  var $ = layui.jquery,
    form = layui.form,
    table = layui.table,
    layer = layui.layer,
    upload = layui.upload,
    iconPickerFa = layui.iconPickerFa;

  iconPickerFa.render({
    // 选择器，推荐使用input
    elem: '#iconPicker',
    // fa 图标接口
    url: "/static/lib/font-awesome-4.7.0/less/variables.less",
    // 是否开启搜索：true/false，默认true
    search: true,
    // 是否开启分页：true/false，默认true
    page: true,
    // 每页显示数量，默认16
    limit: 16,
    // 点击回调
    click: function (data) {
      //console.log(data);
    },
    // 渲染成功后的回调
    success: function (d) {
      //console.log(d);
    }
  });

  $('.fa-refresh').click(function () {
    window.location.reload();
  });

  // 监听弹出层的打开
  $('body').on('click', '[data-open]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-open');
    let reload = $(this).attr('data-reload');
    HuiAdminShow(title, url, '', '', reload);
  });

  $('body').on('click', '[data-open-full]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-open-full');
    let reload = $(this).attr('data-reload');
    HuiAdminOpenFull(title, url, reload);
  });

  $('body').on('click', '[data-confirm]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-confirm');
    let reload = $(this).attr('data-reload');
    HuiAdminConfirm(url, title, reload);
  });

  $('body').on('click', '[data-delete]', function () {
    let title = $(this).attr('data-title');
    let url = $(this).attr('data-delete');
    let reload = $(this).attr('data-reload');
    HuiAdminDel(url, title, reload);
  });

  /* 监听状态设置开关 */
  form.on('switch(switchStatus)', function (data) {
    var that = $(this),
      status = 0;
    if (!that.attr('data-href')) {
      layer.msg('请设置data-href参数');
      return false;
    }
    if (this.checked) {
      status = 1;
    }
    $.post(that.attr('data-href'), {
      val: status
    }, function (res) {
      if (res.code === 1) {
        layer.msg(res.msg, {time: 1500, icon: 1}, function () {
          if (res.data.refresh == 1) {
            window.parent.location.reload();
          }
        });
      } else {
        layer.msg(res.msg, {time: 1500, icon: 2}, function () {
          that.trigger('click');
          form.render('checkbox');
        });
      }
    });
  });

  /**
   * 监听表单提交
   * <button class="layui-btn layui-btn-sm" lay-filter="doSub" lay-submit>立即提交</button>
   */
  form.on('submit(doSub)', function (data) {
    $.ajax({
      type: 'POST',
      url: data.form.action,
      data: data.field,
      dataType: "json",
      success: function (res) {
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1, time: 2000}, function () {
            if (res.url != '') {
              window.location.href = res.url;
            } else {
              window.location.reload();
            }
          })
        } else {
          layer.msg(res.msg, {icon: 2, time: 2000})
        }
      }
    });
    return false;
  });

  window.HuiDoSub = function (data, url) {
    $.ajax({
      type: 'POST',
      url: url,
      data: data,
      dataType: "json",
      success: function (res) {
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1, time: 2000}, function () {
            if (res.url != '') {
              window.location.href = res.url;
            } else {
              window.location.reload();
            }
          })
        } else {
          layer.msg(res.msg, {icon: 2, time: 2000})
        }
      }
    });
  }

  /*满屏（全屏）打开窗口*/
  window.HuiAdminOpenFull = function (title, url, reload = 0) {
    $.get(url, function (res) {
      var index = layer.open({
        type: 2,
        title: title,
        content: url,
        skin: 'skin-layer-hui',
        closeBtn: 11,
        end: function () {
          if (reload == 1) {
            window.parent.location.reload();
          }
        }
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
  window.HuiAdminShow = function (title, url, w, h, reload = 0) {
    if (title == null || title == '') {
      title = false;
    }
    if (url == null || url == '') {
      url = "404.html";
    }
    if (w == null || w == '') {
      w = ($(window).width() * 0.9);
    }
    if (h == null || h == '') {
      h = ($(window).height() * 0.8);
    }
    $.get(url, function (res) {
      layer.open({
        type: 2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        maxmin: true,
        shadeClose: true,
        shade: 0.4,
        title: title,
        content: url,
        end: function () {
          if (reload == 1) {
            window.parent.location.reload();
          }
        }
      });
    });

  }
  /**
   * 提示弹出
   */
  window.HuiAdminConfirm = function (url, msg = '真的要这样操作么？', refresh = 0) {
    layer.confirm(msg, {skin: 'skin-layer-hui'}, function (index) {
      var loading = layer.load(0);
      $.post(url, function (res) {
        layer.close(loading);
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1, time: 1500}, function () {
            if (refresh == 1) {
              window.location.reload();
            }
          });
        } else {
          layer.msg(res.msg, {icon: 2});
        }
      })
    });
  }

  /*删除弹出提示*/
  window.HuiAdminDel = function (url, msg = '真的要删除么？', refresh = 0) {
    layer.confirm(msg, {skin: 'skin-layer-hui'}, function (index) {
      var loading = layer.load(0);
      $.post(url, function (res) {
        layer.close(loading);
        if (res.code === 1) {
          layer.msg(res.msg, {icon: 1, time: 1500}, function () {
            if (refresh == 1) {
              window.location.reload();
            }
          });
        } else {
          layer.msg(res.msg, {icon: 2});
        }
      })
    });
  }

  //图片预览
  window.hui_img_preview = function (id, src) {
    if (src == '') return;
    layer.tips('<img src="' + htmlspecialchars(src) + '" height="100">', '#' + id, {
      tips: [1, '#fff']
    });
  }


  //图像裁剪
  window.hui_img_cropper = function (cid, url) {
    var str = $('#' + cid).val();
    if (str == '') {
      layer.msg('请先上传或选择图片！');
      return false;
    }
    if (url.indexOf('?') != -1) {
      url = url + '&f=' + window.btoa(unescape(encodeURIComponent(str))) + '&cid=' + cid;
    } else {
      url = url + '?f=' + window.btoa(unescape(encodeURIComponent(str))) + '&cid=' + cid;
    }
    layer.open({
      type: 2,
      title: '图像裁剪',
      area: ['750px', '510px'],
      content: url
    });
  }


//html实体转换
  window.htmlspecialchars = function (str) {
    str = str.replace(/&/g, '&amp;');
    str = str.replace(/</g, '&lt;');
    str = str.replace(/>/g, '&gt;');
    str = str.replace(/"/g, '&quot;');
    str = str.replace(/'/g, '&#039;');
    return str;
  }
});