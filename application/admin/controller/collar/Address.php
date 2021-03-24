<?php

namespace app\admin\controller\collar;

use app\common\controller\Backend;

/**
 * 排队发货地址
 *
 * @icon fa fa-circle-o
 */
class Address extends Backend
{
    
    /**
     * Address模型对象
     * @var \app\admin\model\collar\Address
     */
    protected $model = null;
    protected $searchFields = "name,mobile";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\collar\Address;
        $this->view->assign("defaultList", $this->model->getDefaultList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','name','mobile','province','city','district','address','createtime']);
                $row->visible(['user']);
				$row->getRelation('user')->visible(['id','username','nickname']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
