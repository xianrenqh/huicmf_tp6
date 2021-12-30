/**
 * 多图上传
 */
layui.use(['jquery', 'upload'], function () {
  let $ = layui.jquery, upload = layui.upload;

//图片预览
  $(document).on('click', '[id^=preImg]', function () {
    let iHtml = "<img src='" + $(this).parent().parent().find('img:first').attr('src') + "' style='width: 100%; height: 100%;' alt=''/>";
    layer.open({
      type: 1,
      shade: false,
      title: false, //不显示标题
      area: ['50%', '50%'],
      content: iHtml //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
    });
    return false;
  });
//图片删除
  $(document).on('click', '[id^=delImg]', function () {
    let importImgF = $('#imgItemInfo').find('div:first');//importImg0、importImg1、importImg2
    let empt = $(this).parent().parent().parent();//importImg0、importImg1、importImg2
    let nextImgSrc = $(this).parent().parent().parent().next().find('img').attr('src');//src
    //判断当前DIV后面的div的url是否为空
    if (!nextImgSrc) {
      //判断是否为第一个
      if (importImgF.attr('id') === empt.attr('id')) {
        //-是 ，清空第一个 最后面的删除
        //图片url清空
        empt.find('img').attr('src', '');
        $(this).parent().parent().addClass('layui-hide');
        importImgF.find('i:first').removeClass('layui-hide');
        count--;
        $('#' + 'importImg' + count).remove();
      } else {
        // -否，删除当前
        empt.remove();
      }
    } else {
      //如果有值删除当前div
      empt.remove();
    }
    return false;
  });
//图片绑定鼠标悬浮
  $(document).on("mouseenter", ".img", function () {
    //鼠标悬浮
    $(this).find('div:first').removeClass('layui-hide');
  }).on("mouseleave", ".img", function () {
    //鼠标离开
    $(this).find('div:first').addClass('layui-hide');
  });

  let imgsId,
    uploadDemoViewId,
    uploadIconId;
  let count = 1;

  $(document).on('click', '[id^=imgDivs]', function () {
    if (count > 5) {
      layer.msg('最多上传5张', {icon: 2});
      return false;
    }
    //给id赋值
    uploadIconId = $(this).find('i').attr('id');
    uploadDemoViewId = $(this).next().attr('id');
    imgsId = $(this).next().find('img').attr('id');
    $('#importModel').click();
  });

  upload.render({
    elem: '#importModel'
    , multiple: true
    , url: GV.image_upload_url
    , before: function (obj) {
      obj.preview(function (index, file, result) {

      });
    }
    , done: function (res) {
      if (res.code !== 1) {
        return layer.msg(res.msg, {icon: 2})
      }
      $('#' + imgsId).attr('src', res.url);
      $('#' + uploadDemoViewId).removeClass('layui-hide');
      $('#' + uploadIconId).addClass('layui-hide');
      $('#imgItemInfo').append(
        '<div class="layui-upload-drag-self" id="importImg' + count + '">' +
        '<div id="imgDivs' + count + '">' +
        '<i class="layui-icon" id="uploadIcon' + count + '"> &#xe624; </i>' +
        '</div>' +
        '<div class="img layui-hide" id="uploadDemoView' + count + '">' +
        '<img class="layui-upload-img" id="imgs' + count + '" src="" alt="">' +
        '<div class="handle layui-hide" id="handle' + count + '">' +
        '<i class="layui-icon layui-icon-picture-fine" id="preImg' + count + '" style="color:#fff"></i>' +
        '<i class="layui-icon layui-icon-delete" id="delImg' + count + '" style="color:#fff"></i>' +
        '</div>' + '</div>' + '</div>'
      );
      count++;
    }
  });
});
layui.define(function (exports) {
  exports('huiPicUpload', window.huiPicUpload);
});