<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-06-02
 * Time: 上午10:40:48
 * Info:
 */

namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class TimeModel extends Model
{

    /**
     * 自动时间戳类型
     * @var string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 添加时间
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 软删除
     */
    use SoftDelete;

    protected $deleteTime = false;

}