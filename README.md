## HuiCMF v6.0

=========== ==

[![OSCS Status](https://www.oscs1024.com/platform/badge/xianrenqh/huicmf_tp6.svg?size=small)](https://www.oscs1024.com/project/xianrenqh/huicmf_tp6?ref=badge_small)

### 项目地址：

ps:同步更新

#### **【github】**

https://github.com/xianrenqh/huicmf_tp6

#### **【gitee】**

https://gitee.com/xianrenqh/huicmf_tp6

---

**【基于ThinkPHP6.0和layui的快速开发的后台管理系统。】**

**用于学习并允许商业使用。**

> 运行环境要求PHP8.0+(推荐PHP8.1)+mysql+redis(可选)。
>
> 数据库要求：mysql5.6+，推荐5.7。
>
> 编辑器使用了 editor.md | UEeditor | icEditor
>
> 支持插件安装，（使用了think-addons扩展插件）
>

## 其他备注

ip转地区，默认使用纯真数据库（后期需要更新数据库）

更新数据库方法：

1. 打开：/vendor/itbdw/ip-database/bin/update-ip.php文件
2. 编辑：大概第22行，添加注释，如： //die("该功能已无法使用");
3. 在当前目录（/vendor/itbdw/ip-database/bin/）执行命令(linux)：
   **php update-ip.php -d=../src/libs**
4. 当前执行后下载的最新数据库会放在：
   /vendor/itbdw/ip-database/src/libs 下

## 安装

- 首先：git clone 获取数据
- 然后：composer update 更新数据
- 接着：根目录 .example.env 复制一份重命名：.env（去掉.example）
- 访问： http://你的域名/install
- 执行安装程序
- 最后访问后台：http://你的域名/生成的后台安全码.php
- 默认后台账号密码： admin admin888

### 备注：

> 如果迁移网站后访问提示：**No input file specified.** 则：
>
> 删除public目录下的 **.user.ini** 文件
>
> 后台安全访问码可以在管理后台：系统管理-系统设置-安全设置：【后台加密码】中修改。切记不要直接修改public目录下的后台文件名

## 后台演示

https://huicmf6.xiaohuihui.club/admin.php

账号：admin

密码：admin888

## 使用说明

1、后台控制器都需要继承：AdminController

2、使用了注解权限：

# 注解权限

> 注解权限只能获取后台的控制器，也就是该app/admin/controller下

## 控制器注解权限

> 控制器类注解tag @ControllerAnnotation

- 注解类： HuiCMF\annotation\ControllerAnnotation
- 作用范围： CLASS
- 参数说明： title 控制器的名称（必填） auth 是否开启权限控制，默认为true （选填，Enum:{true, false}）

#### 示例

> 备注：注解前请先引用： use app\admin\annotation\ControllerAnnotation;

~~~
/**
 * @ControllerAnnotation(title="菜单管理")
 * Class Node
 * @package app\admin\controller\system
 */
class MenuController extends AdminController
{

}
~~~

## 方法节点注解权限

> 方法节点类注解tag @NodeAnotation

- 注解类： HuiCMF\annotation\NodeAnotation
- 作用范围： METHOD
- 参数说明： title 方法节点的名称（必填） auth 是否开启权限控制，默认为true （选填，Enum:{true, false}）

#### 示例：

> 备注：注解前请先引用： use app\admin\annotation\NodeAnotation;

~~~
/**
 * @NodeAnotation(title="菜单列表")
 */
public function index()
{
}
~~~

### 3、方法中重写了url()，为__url()

所有原方法中的url()不要使用，要使用__url()方法来处理路由。

目的：隐藏模块名（admin）、后台入口。

更改为：http://你的域名/admin.php (admin.php可以自定义)

## 后台前端问题

### 1、前端auth权限验证

> 为什么前端也做权限认证，权限认证不应该是后端做的吗？
> 这里的权限认证指的是前端判断是否有权限查看的数据（例如：添加、删除、编辑之类的按钮），这些只有在点击到对应的url之后，后端才会进行权限认证。
> 为了避免用户困扰，可以在此用上前端的权限认证，判断是否显示还是隐藏

**第一种示例, 通过php的auth()方法生成layui-hide样式属性。**

~~~
<a class="layui-btn layui-btn-sm layui-btn-normal {if !check_auth('system.admin/edit')}layui-hide{/if}" data-open="{:__url('system.admin/edit')}?id={{d.id}}"
               data-title="编辑管理">编辑</a>
~~~

**第二种, 通过php的auth()方法判断, 是否显示html**

~~~
{if check_auth('system.admin/edit')}
<a class="layui-btn layui-btn-sm layui-btn-normal " data-open="{:__url('system.admin/edit')}?id={{d.id}}"
               data-title="编辑管理">编辑</a>
{/if}
~~~

### 2、按钮属性

```html

//弹出层打开:width:90%，height:80%
<a href="javascript:;" data-open="{:__url('system.node/index')}" data-title="测试编辑打开" data-reload="1"
   data-width="910"
   data-height="550">编辑</a>

//弹出层打开全屏:width:100%，height:100%
<a href="javascript:;" data-open-full="{:__url('system.node/index')}" data-title="测试添加打开">添加</a>

//删除询问对话框
<a href="javascript:;" data-delete="{:__url('system.node/index')}" data-title="您确定要删除吗？">1231232313</a>

//普通询问对话框
<a href="javascript:;" data-confirm="{:__url('system.node/index')}" data-title="您确定要取消收藏吗？">1231232313</a>
```

data-title：弹出层title名称

data-open：弹出层打开默认值:width:90%，height:80%

data-width="910" :(弹出层的宽度，只针对data-open有效)

data-height="550"(弹出层的高度，只针对data-open有效)

data-open-full：弹出层打开全屏:width:100%，height:100%

data-confirm：普通询问对话框

data-delete：删除询问对话框

- data-reload="1"  刷新父级页面【例如点击编辑按钮弹出窗口后保存或者关闭窗口在列表页（父级）页面刷新。默认不写或者
  data-reload="0"为不刷新】
- data-reload="2"  刷新当前页面【例如点击编辑按钮弹出窗口后保存或者关闭窗口在当前页面刷新。默认不写或者 data-reload="0"
  为不刷新】

### 3、上传图片

案例页面：http://你的域名/admin.php/system.test/upload.html

> HuiCMF允许你使用以下5种方式上传图片

#### 1.在页面中直接使用layui上传控件

```html
<!--html-->
<button type="button" class="layui-btn" id="test1"><i class="layui-icon"></i>上传图片</button>
```

```javascript
/*javascript*/
upload.render({
  elem: '#test1'
  , url: "{:__url('upload/index',['save_path'=>'images'])}"
  , done: function (res) {
    if (res.code === 1) {
      $("#image").val(res.url);
    }
  }
})
```

#### 2.使用封装好的layui上传（只需要一个按钮，无需在页面中写js）

#### 使用案例：

```html

<button type="button" class="layui-btn layui-btn-normal layUpload" id="lay_pic" data-input-id="c-pic" data-type="image">
    <i class="layui-icon"></i>上传图片
</button>
```

**以上代码中注释如下：**

| 参数名           | 是否允许为空      | 参数值                               |
|---------------|-------------|-----------------------------------|
| class         | 否           | layUpload [**必填一致**]              |
| id            | 否           | 例如：lay_pic                        |
| data-input-id | 否           | 图片文本框的id值，用于返回url填充，需和文本框中的id值对应  |
| data-type     | 是（默认：image） | 上传类型：image [图片格式]（默认）；file [附件格式] |

> 如果一个页面中需要有多个上传控件：
>
> 则两组上传需要保持：id和data-input-id不一致即可（返回输入框对应的id值同理）

#### 3.使用封装好的webUploader插件上传（只需要一个按钮，无需在页面中写js）

```html

<button type="button" class="webUpload" id="picker_pic" data-multiple="false" data-input-id="c-pic"
        data-preview-id="p-pic" data-type="image"><i class="layui-icon"></i>上传图片
</button>
```

**以上代码中注释如下：**

| 参数名           | 是否允许为空      | 参数值                               |
|---------------|-------------|-----------------------------------|
| class         | 否           | webUpload [**必填一致**]              |
| id            | 否           | 例如：picker_pic                     |
| data-input-id | 否           | 图片文本框的id值，用于返回url填充，需和文本框中的id值对应  |
| data-type     | 是（默认：image） | 上传类型：image [图片格式]（默认）；file [附件格式] |

> 如果一个页面中需要有多个上传控件：
>
> 则两组上传需要保持：id和data-input-id不一致即可（返回输入框对应的id值同理）

#### 4.使用封装好的上传选择框上传一张（单张）

如图所示：

![.jpg](https://s1.ax1x.com/2022/04/20/Lrmkyn.jpg)

> 如果是上传一张，上图中多选会只返回第一张图。

调用代码（两组上传示例）：

```html

<div class="layui-form-item">
    <label class="layui-form-label">缩略图</label>
    <div class="layui-input-block">
        <div class="layui-input-inline" style="width: 50%">
            <input type="text" name="image" value=""
                   onmouseover="hui_img_preview('image-select-input',this.value)"
                   onmouseout="layer.closeAll();" id="image-select-input" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-input-inline" style="width: 120px">
            <a class="layui-btn"
               data-open="{:__url('upload/fileList',['type'=>'one','select_id'=>'image-select-input'])}"
               data-title="选择图片"
               data-width="910" data-height="550"><i class="layui-icon"></i>选择图片</a>
        </div>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">缩略图2</label>
    <div class="layui-input-block">
        <div class="layui-input-inline" style="width: 50%">
            <input type="text" name="image2" value=""
                   onmouseover="hui_img_preview('image-select-input2',this.value)"
                   onmouseout="layer.closeAll();" id="image-select-input2" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-input-inline" style="width: 120px">
            <a class="layui-btn"
               data-open="{:__url('upload/fileList',['type'=>'one','select_id'=>'image-select-input2'])}"
               data-title="选择图片"
               data-width="910" data-height="550"><i class="layui-icon"></i>选择图片</a>
        </div>
    </div>
</div>
```

**说明：**

> 文本框中的 id 一定要和选择图片按钮中的date-open值：select_id值保持一致（用于返回不同文本框中的回调值）。
>
> 选择图片按钮组中的type='one'：上传单张。上传多张参考下方说明。

#### 5.使用封装好的上传选择框上传多张

> （参考案例：文章上传多张）

添加代码：

```html

<div class="layui-form-item">
    <label class="layui-form-label">多图选择</label>
    <div class="layui-input-block" style="margin-top: 15px;">
        <a class="layui-btn" data-open="{:__url('upload/fileList',['type'=>'more'])}" data-title="选择图片"
           data-width="910" data-height="550"><i class="layui-icon"></i>选择图片</a>
        <div style="margin-top: 15px;">
            <small>(可拖拽图片调整显示顺序 )</small>
        </div>
        <!--回调显示选择的图片-->
        <div class="uploader-list am-cf"></div>
    </div>
</div>
```

编辑代码：

```html

<div class="layui-form-item">
    <label class="layui-form-label">多图选择</label>
    <div class="layui-input-block" style="margin-top: 15px;">
        <a class="layui-btn" data-open="{:__url('upload/fileList',['type'=>'more'])}" data-title="选择图片"
           data-width="910" data-height="550"><i class="layui-icon"></i>选择图片</a>
        <div style="margin-top: 15px;">
            <small>(可拖拽图片调整显示顺序 )</small>
        </div>
        <!--回调显示选择的图片-->
        <div class="uploader-list am-cf">
            <!--这里是取到的循环数据-->
            {foreach name="data.thumbs" item="vo" key="k"}
            <div class="file-item file-item-id-{$vo.file_id}">
                <img src="{$vo.file_url}">
                <input type="hidden" name="params[thumbs][]" value="{$vo.file_url}">
                <i class="layui-icon layui-icon-close file-item-delete" onclick="fileItemDelete('{$vo.file_id}')"></i>
            </div>
            {/foreach}
        </div>
    </div>
</div>
```

> ps：多图上传后 可以拖动图片进行排序哦~~~。需要添加js代码：

```javascript
layui.use(['jquery', 'ddSort'], function () {
  let $ = layui.jquery;

  // 图片列表拖动，需要引入ddSort
  $('.uploader-list').DDSort({
    target: '.file-item',
    delay: 100, // 延时处理，默认为 50 ms，防止手抖点击 A 链接无效
    floatStyle: {
      'border': '1px solid #ccc',
      'background-color': '#fff'
    }
  });
})
```

### 4、二开了think-addons扩展插件

**插件基于：zzstudio/think-addons 进行了二开处理**

1. 在helper助手中增加了部分公共方法
2. 修改了Addons.php文件，方便在程序中二次调用
3. 插件文件夹里：

- 插件名要和插件控制器一直，例如： test插件文件夹里的 Test.php文件
- info.ini 一定要有，而且字段要对应
- config.php文件参数 参考 test插件里的

### 5、后台表单省市区域三级联动

```html

<div class="layui-form-item tpl-district-container">
    <label class="layui-form-label">地区</label>
    <div class="layui-inline">
        <select name="province" lay-filter="tpl-province" data-value="" class="form-control tpl-province">
        </select>
    </div>
    <div class="layui-inline">
        <select name="city" lay-filter="tpl-city" data-value="" class="form-control tpl-city">
        </select>
    </div>
    <div class="layui-inline">
        <select name="area" lay-filter="tpl-district" data-value="" class="form-control tpl-district">
        </select>
    </div>
</div>
```

> 之后在layui里引用一下即可。其中 data-value 属性是用于编辑的赋值

### 6、【小插件】-让数字动起来

```html
<span class="number-timer">66</span>
```

```js
layui.use(['jquery', 'miniCountUp'], function () {
  var $ = layui.jquery;
  var countUp = layui.miniCountUp;
  var count_up = new countUp({
    target: $('.number-timer'),
    startVal: 0, //目标开始的值，默认值为0
    endVal: 3, //到达目标值,默认值为元素的值
    decimals: 0, //小数位数，默认值为0
    duration: 1.5, //动画持续时间为秒，默认值为2
  });
  count_up.start();
});
```

### 接口文档（apidoc）

> 接口文档使用了apidoc
>
> https://hg-code.gitee.io/thinkphp-apidoc/

### **总结注意事项（爬坑）：**

#### 安装：

使用composer安装：（默认第一次composer后已安装）

~~~
composer require hg/apidoc
~~~

#### 前端访问：

http://你的域名/apidoc

如果无法访问请查看伪静态是否正确

#### 前端接口异常：

请检查路径是否正确

#### 基础案例：

1. 配置config/apidoc.php文件
2. 案例控制器文件：app/api/controller/UserController.php

---

# 开发教程

## 1、API接口开发【在用版】

> 路径： app\api文件夹下。

## **文件说明：**

### 1. config > api.php文件

配置里有详细说明。

### 2. controller > BaseController.php

基础控制器

### 3. controller > LoginController.php

普通会员登录接口

### 4. controller > TestController.php

测试接口

## **使用说明：**

**url：**

/api/login/index?mobile=test&password=123456

**method:**

GET、POST等

如果需要验证权限，需要在header中传递参数：

* XX-Token
* XX-Device-Type

如果不想验证控制器或者不想验证某一方法，请在api.php配置文件中进行配置

## 2、API接口开发【旧版】

> 【旧版的，暂时弃用。文件夹名为：api_old_bak】。如想使用，请重命名文件夹名为api即可。

### 接口说明

系统前台接口所有端都使用同一套接口，接口源码文件夹：app\api

接口的访问地址为：域名/api.html

参数格式为：method 调用具体方法

其它参数：根据每个参数来定义。

需要登录的接口，可以用$this->userId获取当前访问用户id

> $this->userId

**需要登录的接口的接口必须传递参数 token **

判断登录与否的标准就是是否在本地保存了token，如果保存了，就是登录状态，如果没有保存，就是未登录状态，需要登录的接口会自动带上token来进行请求数据。

### 新增一个接口

新增接口时，需要先在api模块中增加一个接口控制器，所有接口控制器均要继承\app\api\controller\ApiController 控制器，如：

> 控制器名称需要使用完整名称，需要带上"Controller"，如：TestController

例如：

```php
namespace app\api\controller;
class TestController extends ApiController{
    public function list(){}
}
```

然后在接口配置文件 app\api\config\api.php 中定义下新增的接口

```php
'test'=>[
    'code' => 'Test',
    'method'    =>  [
        'getlist' => [
            'code' => 'list',
            'is_login' => false
        ]
    ]
]
```

> 最外面的test是url地址调用的第一个参数
> code对应的是接口控制器类名
> method里面定义的是参数
> getlist是外部访问的方法名，getlist里面code是Test控制器里面的具体方法名
> is_login 含义为是否需要登录，当is_login 为true时，必须传token。可使用$this->userId 获取当前登录用户

### 公共接口

如果是一个公共数据接口，直接在 app\api\controller\CommonController.php 文件中写方法，任意请求方式访问：

http://你的域名/api.html/common/test 即可。

### 接口调用案例（以UserController为例）：

> 基本信息

* **接口URL：** `https://你的域名/api.html`
* **请求方式：** `POST`
* **Content-Type：** `multipart/form-data`

> 请求参数：

接口请求参数见下表：

**Body参数说明 (multipart/form-data)**

| 参数名      | 示例值        | 是否必填 | 参数描述 |
|----------|------------|------|------|
| method   | user.login | 必填   | 接口方法 |
| username | admin      | 必填   | 登录名  |
| password | admin      | 必填   | 密码   |

> 响应示例

正确响应示例

```json
{
  "code": 200,
  "data": "c9d2343fd754ca12a9be33e957574cce",
  "msg": ""
}
```

> 错误响应示例

```json


{
  "code": 0,
  "msg": "没有找到此账号",
  "data": ""
}
```

> 响应图片：

[![L0xcd0.md.png](https://s1.ax1x.com/2022/04/19/L0xcd0.png)](https://imgtu.com/i/L0xcd0)

---

# 特别感谢

以下项目排名不分先后

* ThinkPHP：[https://github.com/top-think/framework](https://github.com/top-think/framework)
* EasyAdmnin：[https://gitee.com/zhongshaofa/easyadmin](https://gitee.com/zhongshaofa/easyadmin)
* Layuimini：[https://github.com/zhongshaofa/layuimini](https://github.com/zhongshaofa/layuimini)
* Annotations：[https://github.com/doctrine/annotations](https://github.com/doctrine/annotations)
* Layui：[https://github.com/sentsin/layui](https://github.com/sentsin/layui)
* Jquery：[https://github.com/jquery/jquery](https://github.com/jquery/jquery)
* NKeditor：[https://gitee.com/blackfox/NKeditor](https://gitee.com/blackfox/NKeditor)
* CKEditor：[https://github.com/ckeditor/ckeditor4](https://github.com/ckeditor/ckeditor4)
* JetBrains：[https://jb.gg/OpenSourceSupport](https://jb.gg/OpenSourceSupport)

![PhpStorm logo](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm.svg)
![PhpStorm logo](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm_icon.svg)

## 版权信息

HuiCMF遵循Apache2.0开源协议发布，并允许商业使用。 本项目包含的第三方源码和二进制文件之版权信息另行标注。 版权所有Copyright
© 2019-2022 by
xiaohuihui (https://xiaohuihui.net.cn)
All rights reserved。

## 免责声明

> HuiCMF遵循Apache2.0开源协议发布，并允许商业使用。任何用户在使用`HuiCMF`
> 后台框架前，请您仔细阅读并透彻理解本声明。您可以选择不使用`HuiCMF`后台框架，若您一旦使用`HuiCMF`
> 后台框架，您的使用行为即被视为对本声明全部内容的认可和接受。

* `HuiCMF`后台框架是一款开源免费的后台快速开发框架 ，主要用于更便捷地开发后台管理；其尊重并保护所有用户的个人隐私权，不窃取任何用户计算机中的信息。更不具备用户数据存储等网络传输功能。
* 您承诺秉着合法、合理的原则使用`HuiCMF`后台框架，不利用`HuiCMF`
  后台框架进行任何违法、侵害他人合法利益等恶意的行为，亦不将`HuiCMF`后台框架运用于任何违反我国法律法规的 Web 平台。
* 任何单位或个人因下载使用`HuiCMF`后台框架而产生的任何意外、疏忽、合约毁坏、诽谤、版权或知识产权侵犯及其造成的损失 (
  包括但不限于直接、间接、附带或衍生的损失等)，本开源项目不承担任何法律责任。
* 用户明确并同意本声明条款列举的全部内容，对使用`HuiCMF`后台框架可能存在的风险和相关后果将完全由用户自行承担，本开源项目不承担任何法律责任。
* 任何单位或个人在阅读本免责声明后，应在《MIT 开源许可证》所允许的范围内进行合法的发布、传播和使用`HuiCMF`
  后台框架等行为，若违反本免责声明条款或违反法律法规所造成的法律责任(包括但不限于民事赔偿和刑事责任），由违约者自行承担。
* 如果本声明的任何部分被认为无效或不可执行，其余部分仍具有完全效力。不可执行的部分声明，并不构成我们放弃执行该声明的权利。
* 本开源项目有权随时对本声明条款及附件内容进行单方面的变更，并以消息推送、网页公告等方式予以公布，公布后立即自动生效，无需另行单独通知；若您在本声明内容公告变更后继续使用的，表示您已充分阅读、理解并接受修改后的声明内容。
