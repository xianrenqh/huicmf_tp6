/**
 * date:2020/02/27
 * author:Mr.Chung
 * version:2.0
 * description:layuimini 主体框架扩展
 */
layui.define(["jquery", "miniMenu", "element", "miniTab", "miniTheme"], function (exports) {
  // 文件上传集合
  var webuploaders = [];

  var $ = layui.$,
    layer = layui.layer,
    miniMenu = layui.miniMenu,
    miniTheme = layui.miniTheme,
    element = layui.element,
    miniTab = layui.miniTab;
  var miniAdmin = {

    /**
     * 后台框架初始化
     * @param options.iniUrl   后台初始化接口地址
     * @param options.clearUrl   后台清理缓存接口
     * @param options.urlHashLocation URL地址hash定位
     * @param options.bgColorDefault 默认皮肤
     * @param options.multiModule 是否开启多模块
     * @param options.menuChildOpen 是否展开子菜单
     * @param options.loadingTime 初始化加载时间
     * @param options.pageAnim iframe窗口动画
     * @param options.maxTabNum 最大的tab打开数量
     */
    render: function (options) {
      options.iniUrl = options.iniUrl || null;
      options.clearUrl = options.clearUrl || null;
      options.urlHashLocation = options.urlHashLocation || false;
      options.bgColorDefault = options.bgColorDefault || 0;
      options.multiModule = options.multiModule || false;
      options.menuChildOpen = options.menuChildOpen || false;
      options.loadingTime = options.loadingTime || 1;
      options.pageAnim = options.pageAnim || false;
      options.maxTabNum = options.maxTabNum || 20;
      $.getJSON(options.iniUrl, function (data) {
        if (data == null) {
          miniAdmin.error('暂无菜单信息')
        } else {
          miniAdmin.renderLogo(data.logoInfo);
          miniAdmin.renderClear(options.clearUrl);
          miniAdmin.renderHome(data.homeInfo);
          miniAdmin.renderAnim(options.pageAnim);
          miniAdmin.listen();
          miniAdmin.webuploader_image('.webUpload');
          miniAdmin.upload_image('.layUpload');
          miniAdmin.cropper();
          miniMenu.render({
            menuList: data.menuInfo,
            multiModule: options.multiModule,
            menuChildOpen: options.menuChildOpen
          });
          miniTab.render({
            filter: 'layuiminiTab',
            urlHashLocation: options.urlHashLocation,
            multiModule: options.multiModule,
            menuChildOpen: options.menuChildOpen,
            maxTabNum: options.maxTabNum,
            menuList: data.menuInfo,
            homeInfo: data.homeInfo,
            listenSwichCallback: function () {
              miniAdmin.renderDevice();
            }
          });
          miniTheme.render({
            bgColorDefault: options.bgColorDefault,
            listen: true,
          });
          miniAdmin.deleteLoader(options.loadingTime);
        }
      }).fail(function () {
        miniAdmin.error('菜单接口有误');
      });
    },

    /**
     * 初始化logo
     * @param data
     */
    renderLogo: function (data) {
      var html = '<a href="' + data.href + '"><img src="' + data.image + '" alt="logo"><h1>' + data.title + '</h1></a>';
      $('.layuimini-logo').html(html);
    },

    /**
     * 初始化首页
     * @param data
     */
    renderHome: function (data) {
      sessionStorage.setItem('layuiminiHomeHref', data.href);
      $('#layuiminiHomeTabId').html('<span class="layuimini-tab-active"></span><span class="disable-close">' + data.title + '</span><i class="layui-icon layui-unselect layui-tab-close">ဆ</i>');
      $('#layuiminiHomeTabId').attr('lay-id', data.href);
      $('#layuiminiHomeTabIframe').html('<iframe width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0"  src="' + data.href + '"></iframe>');
    },

    /**
     * 初始化缓存地址
     * @param clearUrl
     */
    renderClear: function (clearUrl) {
      $('.layuimini-clear').attr('data-href', clearUrl);
    },

    /**
     * 初始化iframe窗口动画
     * @param anim
     */
    renderAnim: function (anim) {
      if (anim) {
        $('#layuimini-bg-color').after('<style id="layuimini-page-anim">' +
          '.layui-tab-item.layui-show {animation:moveTop 1s;-webkit-animation:moveTop 1s;animation-fill-mode:both;-webkit-animation-fill-mode:both;position:relative;height:100%;-webkit-overflow-scrolling:touch;}\n' +
          '@keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
          '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
          '}\n' +
          '@-o-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
          '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
          '}\n' +
          '@-moz-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
          '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
          '}\n' +
          '@-webkit-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
          '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
          '}' +
          '</style>');
      }
    },

    fullScreen: function () {
      var el = document.documentElement;
      var rfs = el.requestFullScreen || el.webkitRequestFullScreen;
      if (typeof rfs != "undefined" && rfs) {
        rfs.call(el);
      } else if (typeof window.ActiveXObject != "undefined") {
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript != null) {
          wscript.SendKeys("{F11}");
        }
      } else if (el.msRequestFullscreen) {
        el.msRequestFullscreen();
      } else if (el.oRequestFullscreen) {
        el.oRequestFullscreen();
      } else if (el.webkitRequestFullscreen) {
        el.webkitRequestFullscreen();
      } else if (el.mozRequestFullScreen) {
        el.mozRequestFullScreen();
      } else {
        miniAdmin.error('浏览器不支持全屏调用！');
      }
    },

    /**
     * 退出全屏
     */
    exitFullScreen: function () {
      var el = document;
      var cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.exitFullScreen;
      if (typeof cfs != "undefined" && cfs) {
        cfs.call(el);
      } else if (typeof window.ActiveXObject != "undefined") {
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript != null) {
          wscript.SendKeys("{F11}");
        }
      } else if (el.msExitFullscreen) {
        el.msExitFullscreen();
      } else if (el.oRequestFullscreen) {
        el.oCancelFullScreen();
      } else if (el.mozCancelFullScreen) {
        el.mozCancelFullScreen();
      } else if (el.webkitCancelFullScreen) {
        el.webkitCancelFullScreen();
      } else {
        miniAdmin.error('浏览器不支持全屏调用！');
      }
    },

    /**
     * 初始化设备端
     */
    renderDevice: function () {
      if (miniAdmin.checkMobile()) {
        $('.layuimini-tool i').attr('data-side-fold', 1);
        $('.layuimini-tool i').attr('class', 'fa fa-outdent');
        $('.layui-layout-body').removeClass('layuimini-mini');
        $('.layui-layout-body').addClass('layuimini-all');
      }
    },


    /**
     * 初始化加载时间
     * @param loadingTime
     */
    deleteLoader: function (loadingTime) {
      setTimeout(function () {
        $('.layuimini-loader').fadeOut();
      }, loadingTime * 500)
    },

    /**
     * 成功
     * @param title
     * @returns {*}
     */
    success: function (title) {
      return layer.msg(title, {icon: 1, shade: this.shade, scrollbar: false, time: 2000, shadeClose: true});
    },

    /**
     * 失败
     * @param title
     * @returns {*}
     */
    error: function (title) {
      return layer.msg(title, {icon: 2, shade: this.shade, scrollbar: false, time: 3000, shadeClose: true});
    },

    /**
     * 判断是否为手机
     * @returns {boolean}
     */
    checkMobile: function () {
      var ua = navigator.userAgent.toLocaleLowerCase();
      var pf = navigator.platform.toLocaleLowerCase();
      var isAndroid = (/android/i).test(ua) || ((/iPhone|iPod|iPad/i).test(ua) && (/linux/i).test(pf))
        || (/ucweb.*linux/i.test(ua));
      var isIOS = (/iPhone|iPod|iPad/i).test(ua) && !isAndroid;
      var isWinPhone = (/Windows Phone|ZuneWP7/i).test(ua);
      var clientWidth = document.documentElement.clientWidth;
      if (!isAndroid && !isIOS && !isWinPhone && clientWidth > 1024) {
        return false;
      } else {
        return true;
      }
    },

    /**
     * 监听
     */
    listen: function () {

      /**
       * 清理
       */
      $('body').on('click', '[data-clear]', function () {
        var loading = layer.load(0, {shade: false, time: 2 * 1000});
        sessionStorage.clear();

        // 判断是否清理服务端
        var clearUrl = $(this).attr('data-href');
        if (clearUrl != undefined && clearUrl != '' && clearUrl != null) {
          $.getJSON(clearUrl, function (data, status) {
            layer.close(loading);
            if (data.code != 1) {
              return miniAdmin.error(data.msg);
            } else {
              return miniAdmin.success(data.msg);
            }
          }).fail(function () {
            layer.close(loading);
            return miniAdmin.error('清理缓存接口有误');
          });
        } else {
          layer.close(loading);
          return miniAdmin.success('清除缓存成功');
        }
      });

      /**
       * 刷新
       */
      $('body').on('click', '[data-refresh]', function () {
        $(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload();
        miniAdmin.success('刷新成功');
      });

      /**
       * 监听提示信息
       */
      $("body").on("mouseenter", ".layui-nav-tree .menu-li", function () {
        if (miniAdmin.checkMobile()) {
          return false;
        }
        var classInfo = $(this).attr('class'),
          tips = $(this).prop("innerHTML"),
          isShow = $('.layuimini-tool i').attr('data-side-fold');
        if (isShow == 0 && tips) {
          tips = "<ul class='layuimini-menu-left-zoom layui-nav layui-nav-tree layui-this'><li class='layui-nav-item layui-nav-itemed'>" + tips + "</li></ul>";
          window.openTips = layer.tips(tips, $(this), {
            tips: [2, '#2f4056'],
            time: 300000,
            skin: "popup-tips",
            success: function (el) {
              var left = $(el).position().left - 10;
              $(el).css({left: left});
              element.render();
            }
          });
        }
      });

      $("body").on("mouseleave", ".popup-tips", function () {
        if (miniAdmin.checkMobile()) {
          return false;
        }
        var isShow = $('.layuimini-tool i').attr('data-side-fold');
        if (isShow == 0) {
          try {
            layer.close(window.openTips);
          } catch (e) {
            console.log(e.message);
          }
        }
      });


      /**
       * 全屏
       */
      $('body').on('click', '[data-check-screen]', function () {
        var check = $(this).attr('data-check-screen');
        if (check == 'full') {
          miniAdmin.fullScreen();
          $(this).attr('data-check-screen', 'exit');
          $(this).html('<i class="fa fa-compress"></i>');
        } else {
          miniAdmin.exitFullScreen();
          $(this).attr('data-check-screen', 'full');
          $(this).html('<i class="fa fa-arrows-alt"></i>');
        }
      });

      /**
       * 点击遮罩层
       */
      $('body').on('click', '.layuimini-make', function () {
        miniAdmin.renderDevice();
      });

    },

    /**
     * 绑定图片上传组件组件-layuiUpload
     * @param elements  //获取绑定元素
     * @param onUploadSuccess //上传成功
     * @param onUploadError //上传失败
     * 使用案例：
     * <button type="button" class="layui-btn layui-btn-normal layUpload" id="lay_pic" data-multiple="false" data-input-id="lay-c-pic" data-preview-id="lay-p-pic data-type="image"><i class="layui-icon">&#xe67c;</i>上传图片</button>
     */
    upload_image: function (elements) {
      elements = typeof elements === 'undefined' ? document.body : elements;
      let chunkSize = typeof GV.site.chunksize !== "undefined" ? GV.site.chunksize : 204800;

      if ($(elements).length > 0) {
        $(elements).each(function () {
          var that = this;
          var id = $(this).prop("id") || $(this).prop("name");
          // 是否多图片上传
          var multiple = $(that).data('multiple');
          var type = $(that).data('type') === 'undefined' ? 'image' : $(that).data('type');
          if (type == 'image') {
            Exts = GV.site.upload_image_ext;
            Accept = "images";
            uploadUrl = GV.image_upload_url;
          } else {
            Exts = GV.site.upload_file_ext;
            Accept = "file";
            uploadUrl = GV.file_upload_url;
          }
          //填充ID
          var input_id = $(that).data("input-id") ? $(that).data("input-id") : "";
          console.log(input_id);
          Exts = Exts.replaceAll(",", "|");
          layui.define('upload', function (exports) {
            let upload = layui.upload;
            upload.render({
              elem: "#" + id
              , url: uploadUrl
              , accept: Accept
              , size: chunkSize
              , exts: Exts
              , done: function (res) {
                if (res.code === 1) {
                  $("#"+input_id).val(res.url);
                }
              }
            });
          });

        });

      }
    },

    /**
     * 绑定图片上传组件-webUploader
     * @param elements  //获取绑定元素
     * @param onUploadSuccess //上传成功
     * @param onUploadError //上传失败
     * 使用案例：
     * <button type="button" class="webUpload" id="picker_pic" data-multiple="false" data-input-id="c-pic" data-preview-id="p-pic" data-type="image"><i class="layui-icon">&#xe67c;</i>上传图片</button>
     */
    webuploader_image: function (elements, onUploadSuccess, onUploadError) {
      elements = typeof elements === 'undefined' ? document.body : elements;
      if ($(elements).length > 0) {
        layui.link(layui.cache.base + 'webuploader/webuploader.css?v=0.1.8');
        layui.define('webuploader', function (exports) {
          var webuploader = layui.webuploader;
          //分片
          var chunking = typeof GV.site.chunking !== "undefined" ? GV.site.chunking : false,
            chunkSize = typeof GV.site.chunksize !== "undefined" ? GV.site.chunksize : 5242880;
          $(elements).each(function () {
            var GUID = WebUploader.Base.guid();
            if ($(this).attr("initialized")) {
              return true;
            }
            $(this).attr("initialized", true);
            var that = this;
            var id = $(this).prop("id") || $(this).prop("name");
            // 是否多图片上传
            var multiple = $(that).data('multiple');
            var type = $(that).data('type');
            if (type == 'image') {
              var formData = {thumb: 0, watermark: ''};
            } else {
              var formData = chunking ? {chunkid: GUID} : {};
            }
            //填充ID
            var input_id = $(that).data("input-id") ? $(that).data("input-id") : "";
            //预览ID
            var preview_id = $(that).data("preview-id") ? $(that).data("preview-id") : "";
            var previewtpl = '<li class="file-item thumbnail"><img data-image data-original="{{d.url}}" src="{{d.url}}"><div class="file-panel">' + (multiple ? '<i class="iconfont icon-yidong move-picture"></i>' : '') + '<i class="fa fa-crop cropper" data-input-id="' + input_id + '"></i> <i class="fa fa-trash-o remove-picture"></i></div></li>';
            // 允许上传的后缀
            var $ext = type == 'image' ? GV.site.upload_image_ext : GV.site.upload_file_ext;
            // 图片限制大小
            var $size = type == 'image' ? GV.site.upload_image_size * 1024 : GV.site.upload_file_size * 1024;

            var uploader = WebUploader.create({
              // 选完图片后，是否自动上传。
              auto: true,
              // 去重
              duplicate: true,
              // 不压缩图片
              resize: false,
              compress: false,
              pick: {
                id: '#' + id,
                multiple: multiple
              },
              chunked: chunking,
              chunkSize: chunkSize,
              server: type == 'image' ? GV.image_upload_url : GV.file_upload_url,
              // 图片限制大小
              fileSingleSizeLimit: $size,
              // 只允许选择图片文件。
              accept: {
                title: type == 'image' ? 'Images' : 'Files',
                extensions: $ext,
                mimeTypes: type == 'image' ? 'image/jpg,image/jpeg,image/bmp,image/png,image/gif' : '',
              },
              // 自定义参数
              formData: formData,
            })
            element.on('tab', function (data) {
              uploader.refresh();
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
              $(that).find('.webuploader-pick').html("<i class='layui-icon layui-icon-upload'></i> 上传" + Math.floor(percentage * 100) + "%");
            });
            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
              $(that).find('.webuploader-pick').html("<i class='layui-icon layui-icon-upload'></i> 上传" + Math.floor(percentage * 100) + "%");
            });
            // 文件上传成功
            uploader.on('uploadSuccess', function (file, response) {
              var ok = function (file, response) {
                if (response.code == 1) {
                  var button = $('#' + file.id);
                  if (button) {
                    //如果有文本框则填充
                    if (input_id) {
                      var urlArr = [];
                      var inputObj = $("#" + input_id);
                      if (multiple && inputObj.val() !== "") {
                        urlArr.push(inputObj.val());
                      }
                      urlArr.push(response.url);
                      inputObj.val(urlArr.join(",")).trigger("change");
                    }
                  }
                } else {
                  layer.error(response.info);
                }
              }
              if (type == 'file' && chunking) {
                //合并
                $.ajax({
                  url: GV.file_upload_url,
                  dataType: "json",
                  type: "POST",
                  data: {
                    chunkid: GUID,
                    action: 'merge',
                    filesize: file.size,
                    filename: file.name,
                    id: file.id,
                    chunks: Math.floor(file.size / chunkSize + (file.size % chunkSize > 1 ? 1 : 0)),
                  },
                  success: function (res) {
                    ok(file, res);
                  },
                })
              } else {
                ok(file, response);
              }
              if (typeof onUploadSuccess === 'function') {
                var result = onUploadSuccess.call(file, response);
                if (result === false)
                  return;
              }
              // 完成上传完了，成功或者失败，先删除进度条。
              uploader.on('uploadComplete', function (file) {
                setTimeout(function () {
                  $(that).find('.webuploader-pick').html("<i class='layui-icon layui-icon-upload'></i> 上传");
                  uploader.refresh();
                }, 500);
              });
              // 文件验证不通过
              uploader.on('error', function (type) {
                switch (type) {
                  case 'Q_TYPE_DENIED':
                    layer.alert('类型不正确，只允许上传后缀名为：' + $ext + '，请重新上传！', {icon: 5})
                    break;
                  case 'F_EXCEED_SIZE':
                    layer.alert('不得超过' + ($size / 1024) + 'kb，请重新上传！', {icon: 5})
                    break;
                }
              });
              // 如果是多图上传，则实例化拖拽
              if (preview_id && multiple) {
                $("#" + preview_id).dragsort({
                  dragSelector: ".move-picture",
                  dragEnd: function () {
                    $("#" + preview_id).trigger("fa.preview.change");
                  },
                  placeHolderTemplate: '<li class="file-item thumbnail" style="border:1px #009688 dashed;"></li>'
                })
              }
              //刷新隐藏textarea的值
              var refresh = function (name) {

              }
              if (preview_id && input_id) {
                layui.define('laytpl', function (exports) {
                  var laytpl = layui.laytpl;
                  $(document.body).on("keyup change", "#" + input_id, function (e) {
                    var inputStr = $("#" + input_id).val();
                    var inputArr = inputStr.split(/\,/);
                    $("#" + preview_id).empty();
                    var tpl = $("#" + preview_id).data("template") ? $("#" + preview_id).data("template") : "";
                    var extend = $("#" + preview_id).next().is("textarea") ? $("#" + preview_id).next("textarea").val() : "{}";
                    var json = {};
                    try {
                      json = JSON.parse(extend);
                    } catch (e) {
                    }
                    $.each(inputArr, function (i, j) {
                      if (!j) {
                        return true;
                      }
                      var suffix = /[\.]?([a-zA-Z0-9]+)$/.exec(j);
                      suffix = suffix ? suffix[1] : 'file';
                      var value = (json && typeof json[i] !== 'undefined' ? json[i] : null);
                      var data = {
                        url: j,
                        data: $(that).data(),
                        key: i,
                        index: i,
                        value: value,
                        row: value,
                        suffix: suffix
                      };
                      laytpl(previewtpl).render(data, function (html) {
                        $("#" + preview_id).append(html);
                      });
                    });
                    refresh($("#" + preview_id).data("name"));
                  });
                  $("#" + input_id).trigger("change");
                })
              }
              if (preview_id) {
                //监听文本框改变事件
                $("#" + preview_id).on('change keyup', "input,textarea,select", function () {
                  refresh($(this).closest("ul").data("name"));
                });
                // 监听事件
                $(document.body).on("fa.preview.change", "#" + preview_id, function () {
                  var urlArr = [];
                  $("#" + preview_id + " [data-original]").each(function (i, j) {
                    urlArr.push($(this).data("original"));
                  });
                  if (input_id) {
                    $("#" + input_id).val(urlArr.join(","));
                  }
                  refresh($("#" + preview_id).data("name"));
                });
                // 移除按钮事件
                $(document.body).on("click", "#" + preview_id + " .remove-picture", function () {
                  $(this).closest("li").remove();
                  $("#" + preview_id).trigger("fa.preview.change");
                });
              }
              // 将上传实例存起来
              webuploaders.push(uploader);

            });

          });
        });
      }
    },
    cropper: function () {
      //裁剪图片
      $(document).on('click', '.cropper', function () {
        layer.msg('图片裁剪还在开发中');
      });
    }

  };


  exports("miniAdmin", miniAdmin);
});
