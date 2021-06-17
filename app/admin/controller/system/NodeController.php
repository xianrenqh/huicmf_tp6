<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2021-05-25
 * Time: 下午5:21:42
 * Info:
 */

namespace app\admin\controller\system;

use app\admin\service\TriggerService;
use app\common\controller\AdminController;
use app\admin\service\NodeService;
use app\admin\annotation\ControllerAnnotation;
use app\admin\annotation\NodeAnotation;
use think\App;
use app\admin\model\AuthRule;

/**
 * @ControllerAnnotation(title="系统节点管理")
 * Class Node
 * @package app\admin\controller\system
 */
class NodeController extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $authRule = new AuthRule();
            $count    = $authRule->count();
            $list     = $authRule->getNodeTreeList();
            $data     = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];

            return json($data);
        }

        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="系统节点更新")
     */
    public function refreshNode($force = 0)
    {
        try {
            $nodeList = (new NodeService())->getNodelist();
        } catch (\Exception $e) {
            $this->error("节点更新失败，查看是否有控制器没有引用：Annotations或者注释写错了<br>".$e->getMessage());
        }
        empty($nodeList) && $this->error('暂无需要更新的系统节点');
        $authRule = new AuthRule();
        try {
            if ($force == 1) {
                $updateNodeList = $authRule->whereIn('node', array_column($nodeList, 'node'))->select();
                $formatNodeList = array_format_key($nodeList, 'node');
                foreach ($updateNodeList as $vo) {
                    isset($formatNodeList[$vo['node']]) && $authRule->where('id', $vo['id'])->update([
                        'title'   => $formatNodeList[$vo['node']]['title'],
                        'is_auth' => $formatNodeList[$vo['node']]['is_auth'],
                    ]);
                }
            }
            $existNodeList = $authRule->field('node,title,type,is_auth')->select();
            foreach ($nodeList as $key => $vo) {
                foreach ($existNodeList as $v) {
                    if ($vo['node'] == $v->node) {
                        unset($nodeList[$key]);
                        break;
                    }
                }
            }
            $authRule->saveAll($nodeList);
            TriggerService::updateNode();
        } catch (\Exception $e) {
            $this->error('节点更新失败<br>'.$e->getMessage());
        }
        $this->success('节点更新成功');
    }

    /**
     * @NodeAnotation(title="清除失效节点")
     */
    public function clearNode()
    {
        $nodeList = (new NodeService())->getNodelist();
        $authRule = new AuthRule();
        try {
            $existNodeList  = $authRule->field('id,node,title,type,is_auth')->select()->toArray();
            $formatNodeList = array_format_key($nodeList, 'node');
            foreach ($existNodeList as $vo) {
                ! isset($formatNodeList[$vo['node']]) && $authRule->where('id', $vo['id'])->delete();
            }
            TriggerService::updateNode();
        } catch (\Exception $e) {
            $this->error('节点更新失败');
        }
        $this->success('节点更新成功');
    }

}