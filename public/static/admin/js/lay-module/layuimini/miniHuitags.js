/**
 * html代码i用方法：
 * <div class="layui-form-item">
 *     <label class="layui-form-label">TAG：</label>
 *     <div class="layui-input-inline block" style="width: 50%;">
 *         <div id="Huitags-demo1"></div>
 *     </div>
 *     <a class="layui-btn layui-btn-normal {if !check_auth('content.tag/select')}layui-hide{/if}"
 *        data-open="{:__url('content.tag/select')}"
 *        data-title="TAG标签选择" data-reload="0">选择</a>
 * </div>
 * script引用方法：
 * $("#Huitags-demo1").Huitags(
 *       {
 *         maxlength: 20,
 *         number: 10,
 *       }
 *     );
 */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
      (global.huiTags = factory());
}(this, (function () {
  layui.define(["jquery"], function (exports) {
    var $ = layui.$;
    $.fn.Huitags = function (options) {
      var defaults = {
        value: '',
        maxlength: 20,
        number: 5,
        tagsDefault: [""],
      }
      var options = $.extend(defaults, options);
      var keyCodes = {
        Enter: 13,
        Enter2: 108,
        Spacebar: 32
      }
      this.each(function () {
        var that = $(this);
        var str =
          '<div class="Huitags-wraper">' +
          '<div class="Huitags-input-wraper">' +
          '<input type="text" class="layui-input Huitags-input" placeholder="添加标签，用空格或者回车分隔" maxlength="' + options.maxlength + '" value="">' +
          '</div>' +
          '<div class="Huitags-editor cl"></div>' +
          '<div class="Huitags-list">' +
          '<div class="Huitags-notag" style="display:none">暂无常用标签</div>' +
          '<div class="Huitags-has"></div>' +
          '</div>' +
          '<input type="hidden" class="layui-input Huitags-val" name="hui_tags" value="' + options.value + '">' +
          '</div>';
        that.append(str);
        var wraper = that.find(".Huitags-wraper");
        var editor = that.find(".Huitags-editor");
        var input = that.find(".Huitags-input");
        var list = that.find(".Huitags-list");
        var has = that.find(".Huitags-has");
        var val = that.find(".Huitags-val");


        if (options.tagsDefault) {
          var tagsDefaultLength = (options.tagsDefault).length;
          for (var i = 0; i < tagsDefaultLength; i++) {
            has.append('<span>' + options.tagsDefault[i] + '</span>');
          }
          has.find("span").on('click', function () {
            var taghasV = $(this).text();
            taghasV = taghasV.replace(/(^\s*)|(\s*$)/g, "");
            editor.append('<span class="Huitags-token">' + taghasV + '</span>');
            gettagval(this);
            $(this).remove();
          });
        }

        function gettagval(obj) {
          var str = "";
          var token = that.find(".Huitags-token");
          if (token.length < 1) {
            input.val("");
            return false;
          }
          for (var i = 0; i < token.length; i++) {
            str += token.eq(i).text() + ",";
          }
          str = unique(str, 1);
          str = str.join();
          val.val(str);
        }

        /*将字符串逗号分割成数组并去重*/
        function unique(o, type) {
          //去掉前后空格
          o = o.replace(/(^\s*)|(\s*$)/g, "");
          if (type == 1) {
            //把所有的空格和中文逗号替换成英文逗号
            o = o.replace(/(\s)|(，)/g, ",");
          } else {
            //把所有的中文逗号替换成英文逗号
            o = o.replace(/(，)/g, ",");
          }
          //去掉前后英文逗号
          o = o.replace(/^,|,$/g, "");
          //去重连续的英文逗号
          o = o.replace(/,+/g, ',');
          o = o.split(",");
          var n = [o[0]]; //结果数组
          for (var i = 1; i < o.length; i++) {
            if (o.indexOf(o[i]) == i) {
              if (o[i] == "")
                continue;
              n.push(o[i]);
            }
          }
          return n;
        }

        input.on("keydown", function (e) {
          var evt = e || window.event;
          if (evt.keyCode == keyCodes.Enter || evt.keyCode == keyCodes.Enter2 || evt.keyCode == keyCodes.Spacebar) {
            var v = input.val().replace(/\s+/g, "");
            var reg = /^,|,$/gi;
            v = v.replace(reg, "");
            v = $.trim(v);
            if (v != '') {
              input.change();
            } else {
              return false;
            }
          }
        });

        input.on("change", function () {
          var v1 = input.val();
          var v2 = val.val();
          var v = v2 + ',' + v1;
          if (v != '') {
            var str = '<i class="iconfont icon-tagfill"></i>';
            var result = unique(v, 1);
            if (result.length > 0) {
              for (var j = 0; j < result.length; j++) {
                str += '<span class="Huitags-token">' + result[j] + '</span>';
              }
              val.val(result);
              editor.html(str);
              input.val("").blur();
            }
          }
        });

        $(document).on("click", ".Huitags-token", function () {
          $(this).remove();
          var str = "";
          if (that.find(".Huitags-token").length < 1) {
            val.val("");
            return false;
          } else {
            for (var i = 0; i < that.find(".Huitags-token").length; i++) {
              str += that.find(".Huitags-token").eq(i).text() + ",";
            }
            str = str.substring(0, str.length - 1);
            val.val(str);
          }
        });
        input.change();
      });
    }
  });
})));

layui.define(function (exports) {
  exports('miniHuitags', window.miniHuitags);
});