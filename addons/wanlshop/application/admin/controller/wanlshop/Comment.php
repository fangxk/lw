<?php

namespace app\admin\controller\wanlshop;

use app\common\controller\Backend;

/**
 * 商品评论
 *
 * @icon fa fa-circle-o
 */
class Comment extends Backend
{
    
    /**
     * Comment模型对象
     * @var \app\admin\model\wanlshop\Comment
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\wanlshop\Comment;
        $this->view->assign("stateList", $this->model->getStateList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['user','shop','goods'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['user','shop','goods'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                $row->getRelation('user')->visible(['username','nickname']);
				$row->getRelation('shop')->visible(['shopname']);
				$row->getRelation('goods')->visible(['title']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
	
	/**
	 * 评论详情
	 */
	public function detail($ids = null, $type = 0)
	{
		if($type == 1){
			$row = $this->model->get(['order_id' => $ids]);
		}else{
			$row = $this->model->get($ids);
		}
	    if (!$row) {
	        $this->error(__('No Results were found'));
	    }
		$row['images'] = explode(',', $row['images']);
		$this->view->assign("row", $row);
	    return $this->view->fetch();
	}
	
}
