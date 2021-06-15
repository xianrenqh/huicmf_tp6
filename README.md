HuiCMF v6.0
===============
基于ThinkPHP 6.0开发
**【参考EasyAdmin重构开发，后台非VUE，推荐支持EasyAdmin】**

**仅用于学习使用**
> 运行环境要求PHP7.1+，兼容PHP8.0。
>
by:xiaohuihui

## 安装

- 首先：git clone 获取数据
- 然后：composer update 更新数据
- 接着访问： http://你的域名/install
- 执行安装程序
- 最后访问后台：http://你的域名/admin
- 默认后台账号密码： admin admin888

## 使用说明

1、后台控制器都需要继承：AdminController

2、使用了注解权限：

~~~
//弹出层打开
<a href="javascript:;" data-open="{:url('system.node/index')}" data-title="测试">1231232313</a>
//删除询问对话框
<a href="javascript:;" data-delete="{:url('system.node/index')}" data-title="删除">1231232313</a>
~~~

# 注解权限

> 注解权限只能获取后台的控制器，也就是该app/admin/controller下

## 控制器注解权限

> 控制器类注解tag @ControllerAnnotation

- 注解类： EasyAdmin\annotation\ControllerAnnotation
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

- 注解类： EasyAdmin\annotation\NodeAnotation
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

## 后台前端问题

### 前端auth权限验证

> 为什么前端也做权限认证，权限认证不应该是后端做的吗？ 这里的权限认证指的是前端判断是否有权限查看的数据（例如：添加、删除、编辑之类的按钮），这些只有在点击到对应的url之后，后端才会进行权限认证。 为了避免用户困扰，可以在此用上前端的权限认证，判断是否显示还是隐藏

**第一种示例, 通过php的auth()方法生成layui-hide样式属性。**

~~~
<a class="layui-btn layui-btn-sm layui-btn-normal {if !check_auth('system.admin/edit')}layui-hide{/if}" data-open="{:url('system.admin/edit')}?id={{d.id}}"
               data-title="编辑管理">编辑</a>
~~~

**第二种, 通过php的auth()方法判断, 是否显示html**

~~~
{if check_auth('system.admin/edit')}
<a class="layui-btn layui-btn-sm layui-btn-normal " data-open="{:url('system.admin/edit')}?id={{d.id}}"
               data-title="编辑管理">编辑</a>
{/if}
~~~

## 特别感谢

以下项目排名不分先后

* ThinkPHP：[https://github.com/top-think/framework](https://github.com/top-think/framework)

* EasyAdmnin：[https://gitee.com/zhongshaofa/easyadmin](https://gitee.com/zhongshaofa/easyadmin)

* Layuimini：[https://github.com/zhongshaofa/layuimini](https://github.com/zhongshaofa/layuimini)

* Annotations：[https://github.com/doctrine/annotations](https://github.com/doctrine/annotations)

* Layui：[https://github.com/sentsin/layui](https://github.com/sentsin/layui)

* Jquery：[https://github.com/jquery/jquery](https://github.com/jquery/jquery)

* CKEditor：[https://github.com/ckeditor/ckeditor4](https://github.com/ckeditor/ckeditor4)
