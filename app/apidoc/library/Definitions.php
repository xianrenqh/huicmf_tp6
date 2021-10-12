<?php
// +----------------------------------------------------------------------
// | 定义通用注释
// +----------------------------------------------------------------------
namespace app\apidoc\library;

class Definitions
{

    /**
     * @title 获取分页数据列表的参数
     *
     * @param name:pageIndex type:int require:0 default:0 desc:查询页数
     * @param name:pageSize type:int require:0 default:20 desc:查询条数
     */
    public function pagingParam()
    {
    }

    /**
     * @title 返回字典数据
     * @return name:code type:int default:0 desc:错误码
     * @return name:msg type:string desc:提示信息
     * @return name:time type:int desc:时间戳
     * @return name:data type:string desc:返回的数据
     */
    public function dictionary()
    {
    }

}
