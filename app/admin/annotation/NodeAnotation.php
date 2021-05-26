<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午6:05:52
 * Info:
 */

namespace app\admin\annotation;

use Doctrine\Common\Annotations\Annotation\Attributes;

/**
 * 创建节点注解类
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 * @Attributes({
 *   @Attribute("time", type = "int")
 * })
 */
final class NodeAnotation
{

    /**
     * 节点名称
     * @Required()
     * @var string
     */
    public $title;

    /**
     * 是否开启权限控制
     * @Enum({true,false})
     * @var bool
     */
    public $auth = true;

}