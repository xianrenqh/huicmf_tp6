## tag标签
```script
layui.use(['tag'], function () {
let tag = laui.tag;
})
//获取标签内容
let tagArray = tag.data("demo");
```

#### Tag 标签组件
```html
<link rel="stylesheet" href="component/pear/css/pear.css">
 并
<script src="component/layui/layui.js"></script>
 并
<script src="component/pear/pear.js"></script>
```

---
#### 主题风格
```html
<div class="layui-btn-container tag">
    <button lay-id="11" type="button" class="tag-item tag-item-normal layui-btn layui-btn-primary layui-btn-sm">网站设置</button>
    <button lay-id="22" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">用户管理</button>
    <button lay-id="33" type="button" class="tag-item tag-item-warm layui-btn layui-btn-primary layui-btn-sm">权限分配</button>
    <button lay-id="44" type="button" class="tag-item tag-item-danger layui-btn layui-btn-primary layui-btn-sm">商品管理</button>
</div>
```
---

#### 动态操作
```html
<div class="layui-btn-container tag" lay-filter="demo" lay-allowclose="true" lay-newTag="true">
    <button lay-id="11" type="button" class="tag-item tag-item-normal">网站设置</button>
    <button lay-id="22" type="button" class="tag-item">用户管理</button>
    <button lay-id="33" type="button" class="tag-item tag-item-warm">权限分配</button>
    <button lay-id="44" type="button" class="tag-item tag-item-danger">商品管理</button>
</div>
<script>
    tag.add('demo', {text: '新选项',id: 12})
    tag.delete('demo', '44');
</script>
```
---

#### 圆角风格
```html
<div class="layui-btn-container tag" lay-filter="test" lay-newtags="true">
    <button lay-id="11" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">网站设置</button>
    <button lay-id="22" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">用户管理</button>
    <button lay-id="33" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">权限分配</button>
    <button lay-id="44" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">商品管理</button>
    <button lay-id="55" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">订单管理</button>
</div>
```
---
#### 删除功能
```html
<div class="layui-btn-container tag" lay-allowclose="true">
    <button lay-id="11" type="button" class="tag-item tag-item-danger layui-btn layui-btn-primary layui-btn-sm">网站设置<i class="layui-icon layui-unselect tag-close">ဆ</i></button>
    <button lay-id="22" type="button" class="tag-item tag-item-danger layui-btn layui-btn-primary layui-btn-sm">用户管理<i class="layui-icon layui-unselect tag-close">ဆ</i></button>
    <button lay-id="33" type="button" class="tag-item tag-item-danger layui-btn layui-btn-primary layui-btn-sm">权限分配<i class="layui-icon layui-unselect tag-close">ဆ</i></button>
    <button lay-id="44" type="button" class="tag-item tag-item-danger layui-btn layui-btn-primary layui-btn-sm">商品管理<i class="layui-icon layui-unselect tag-close">ဆ</i></button>
</div>
 <script> 
tag.on('delete(demo)', function(data) {
    console.log('删除');
    console.log(this); 
    console.log(data.index);
    console.log(data.elem);
    console.log(data.othis);
});
</script>
```
---
#### 新建功能
```html
<div class="layui-btn-container tag" lay-newtags="true">
    <button lay-id="11" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">网站设置</button>
    <button lay-id="22" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">用户管理</button>
    <button lay-id="33" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">权限分配</button>
    <button lay-id="44" type="button" class="tag-item layui-btn layui-btn-primary layui-btn-sm">商品管理</button>
</div>

<script>
    tag.on('add(demo)', function(data) {
        console.log('新建');
        console.log(this);
        console.log(data.index);
        console.log(data.elem);
    });
</script>
```

