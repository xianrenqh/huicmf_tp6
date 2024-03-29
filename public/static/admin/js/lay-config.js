/**
 * date:2019/08/16
 * author:Mr.Chung
 * description:此处放layui自定义扩展
 * version:2.0.4
 */

window.rootPath = (function (src) {
  src = document.scripts[document.scripts.length - 1].src;
  return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
  base: rootPath + "lay-module/",
  version: true
}).extend({
  miniAdmin: "layuimini/miniAdmin", // layuimini后台扩展
  miniMenu: "layuimini/miniMenu", // layuimini菜单扩展
  miniTab: "layuimini/miniTab", // layuimini tab扩展
  miniTheme: "layuimini/miniTheme", // layuimini 主题扩展
  miniCountUp: "layuimini/miniCountUp", // 让数字动起来
  miniPinyin: "layuimini/miniPinyin", // 汉字转拼音
  miniTongji: "layuimini/miniTongji", // layuimini 统计扩展
  miniHuitags: "layuimini/miniHuitags", // miniHuitags
  miniDistrict: "layuimini/miniDistrict", // miniDistrict
  step: 'step-lay/step', // 分步表单扩展
  treetable: 'treetable-lay/treetable', //table树形扩展
  tableSelect: 'tableSelect/tableSelect', // table选择扩展
  iconPickerFa: 'iconPicker/iconPickerFa', // fa图标选择扩展
  echarts: 'echarts/echarts', // echarts图表扩展
  echartsTheme: 'echarts/echartsTheme', // echarts图表主题扩展
  layarea: 'layarea/layarea', //  省市县区三级联动下拉选择器
  webuploader: 'webuploader/webuploader', //  webuploader上传
  huiPicUpload: 'layuimini/huiPicUpload', //  huiPicUpload上传
  fileLibrary: "layuimini/fileLibrary", // 图片库选择与上传
  tag: "tag/tag", // tag标签
  notice: "notice/notice", // notice通知
  ddSort: "ddsort/ddSort", // ddSort拖拽
});

