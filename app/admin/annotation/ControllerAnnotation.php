<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午6:12:10
 * Info:
 */

namespace app\admin\annotation;

use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class ControllerAnnotation
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("title", type="string"),
 * })
 *
 * @since 2.0
 */
final class ControllerAnnotation
{

    /**
     * Route group prefix for the controller
     *
     * @Required()
     *
     * @var string
     */
    public $title = '';

    /**
     * 是否开启权限控制
     * @Enum({true,false})
     * @var bool
     */
    public $auth = true;

    /**
     * 是否菜单
     * @Enum({true,false})
     * @var bool
     */
    public $menu = true;

}