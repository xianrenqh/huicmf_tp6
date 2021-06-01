HuiCMF v6.0
===============
基于ThinkPHP 6.0开发
**【参考EasyAdmin学习重构开发，推荐支持EasyAdmin】**
> 运行环境要求PHP7.1+，兼容PHP8.0。
>
by:xiaohuihui

###

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

## 特别感谢

以下项目排名不分先后

* ThinkPHP：[https://github.com/top-think/framework](https://github.com/top-think/framework)

* EasyAdmnin：[https://gitee.com/zhongshaofa/easyadmin](https://gitee.com/zhongshaofa/easyadmin)

* Layuimini：[https://github.com/zhongshaofa/layuimini](https://github.com/zhongshaofa/layuimini)

* Annotations：[https://github.com/doctrine/annotations](https://github.com/doctrine/annotations)

* Layui：[https://github.com/sentsin/layui](https://github.com/sentsin/layui)

* Jquery：[https://github.com/jquery/jquery](https://github.com/jquery/jquery)

* CKEditor：[https://github.com/ckeditor/ckeditor4](https://github.com/ckeditor/ckeditor4)
