HuiCMF v6.0
===============
**【基于ThinkPHP6.0和layui的快速开发的后台管理系统。】**

**仅用于学习使用。**
> 运行环境要求PHP7.1+，兼容PHP8.0。
>
> 数据库要求：mysql5.5+，推荐5.7。
> 
> 编辑器使用了 editor.md | UEeditor | icEditor
>
by:xiaohuihui

## 安装

- 首先：git clone 获取数据
- 然后：composer update 更新数据
- 接着访问： http://你的域名/install
- 执行安装程序
- 最后访问后台：http://你的域名/admin.php
- 默认后台账号密码： admin admin888

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

> 为什么前端也做权限认证，权限认证不应该是后端做的吗？ 这里的权限认证指的是前端判断是否有权限查看的数据（例如：添加、删除、编辑之类的按钮），这些只有在点击到对应的url之后，后端才会进行权限认证。 为了避免用户困扰，可以在此用上前端的权限认证，判断是否显示还是隐藏

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

data-open：弹出层打开:width:90%，height:80%

data-open-full：弹出层打开全屏:width:100%，height:100%

data-confirm：普通询问对话框

data-delete：删除询问对话框

- data-reload="1"  刷新父级页面【例如点击编辑按钮弹出窗口后保存或者关闭窗口在列表页（父级）页面刷新。默认不写或者 data-reload="0"为不刷新】
- data-reload="2"  刷新当前页面【例如点击编辑按钮弹出窗口后保存或者关闭窗口在当前页面刷新。默认不写或者 data-reload="0"为不刷新】
【实例：】

~~~
//弹出层打开:width:90%，height:80%
<a href="javascript:;" data-open="{:__url('system.node/index')}" data-title="测试编辑打开" data-reload="1">编辑</a>

//弹出层打开全屏:width:100%，height:100%
<a href="javascript:;" data-open-full="{:__url('system.node/index')}" data-title="测试添加打开">添加</a>

//删除询问对话框
<a href="javascript:;" data-delete="{:__url('system.node/index')}" data-title="您确定要删除吗？">1231232313</a>

//普通询问对话框
<a href="javascript:;" data-confirm="{:__url('system.node/index')}" data-title="您确定要取消收藏吗？">1231232313</a>

~~~

## 特别感谢

以下项目排名不分先后

* ThinkPHP：[https://github.com/top-think/framework](https://github.com/top-think/framework)

* EasyAdmnin：[https://gitee.com/zhongshaofa/easyadmin](https://gitee.com/zhongshaofa/easyadmin)

* Layuimini：[https://github.com/zhongshaofa/layuimini](https://github.com/zhongshaofa/layuimini)

* Annotations：[https://github.com/doctrine/annotations](https://github.com/doctrine/annotations)

* Layui：[https://github.com/sentsin/layui](https://github.com/sentsin/layui)

* Jquery：[https://github.com/jquery/jquery](https://github.com/jquery/jquery)

* NKeditor：[https://gitee.com/blackfox/NKeditor](https://gitee.com/blackfox/NKeditor)

* CKEditor：[https://github.com/ckeditor/ckeditor4](https://github.com/ckeditor/ckeditor4)

## 免责声明

> 任何用户在使用`HuiCMF`后台框架前，请您仔细阅读并透彻理解本声明。您可以选择不使用`HuiCMF`后台框架，若您一旦使用`HuiCMF`后台框架，您的使用行为即被视为对本声明全部内容的认可和接受。

* `HuiCMF`后台框架是一款开源免费的后台快速开发框架 ，主要用于更便捷地开发后台管理；其尊重并保护所有用户的个人隐私权，不窃取任何用户计算机中的信息。更不具备用户数据存储等网络传输功能。
* 您承诺秉着合法、合理的原则使用`HuiCMF`后台框架，不利用`HuiCMF`后台框架进行任何违法、侵害他人合法利益等恶意的行为，亦不将`HuiCMF`后台框架运用于任何违反我国法律法规的 Web 平台。
* 任何单位或个人因下载使用`HuiCMF`后台框架而产生的任何意外、疏忽、合约毁坏、诽谤、版权或知识产权侵犯及其造成的损失 (包括但不限于直接、间接、附带或衍生的损失等)，本开源项目不承担任何法律责任。
* 用户明确并同意本声明条款列举的全部内容，对使用`HuiCMF`后台框架可能存在的风险和相关后果将完全由用户自行承担，本开源项目不承担任何法律责任。
* 任何单位或个人在阅读本免责声明后，应在《MIT 开源许可证》所允许的范围内进行合法的发布、传播和使用`HuiCMF`后台框架等行为，若违反本免责声明条款或违反法律法规所造成的法律责任(包括但不限于民事赔偿和刑事责任），由违约者自行承担。
* 如果本声明的任何部分被认为无效或不可执行，其余部分仍具有完全效力。不可执行的部分声明，并不构成我们放弃执行该声明的权利。
* 本开源项目有权随时对本声明条款及附件内容进行单方面的变更，并以消息推送、网页公告等方式予以公布，公布后立即自动生效，无需另行单独通知；若您在本声明内容公告变更后继续使用的，表示您已充分阅读、理解并接受修改后的声明内容。

