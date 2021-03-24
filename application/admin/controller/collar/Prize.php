<?php

namespace app\admin\controller\collar;

use app\admin\controller\LeSoft;
use app\common\controller\Backend;
use think\Config;
use think\Db;

/**
 * 领取日志
 *
 * @icon fa fa-circle-o
 */
class Prize extends Backend
{
    
    /**
     * Prize模型对象
     * @var \app\admin\model\collar\Prize
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\collar\Prize;
        $this->view->assign("identiyList", $this->model->getIdentiyList());
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
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','weigh','name','prize','identiy','status','createtime']);
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /*
     * 新增
     */
    /**
     *新增活动
     */
    public function add(){
        if ($this->request->isPost()) {
            $params    = $this->request->post("row/a");
            $params['createtime'] = time();
            if ($params) {
                if($params["identiy"]){
                    $user_nickname = db('user')->field("nickname,username")->where('id', $params["uid"])->find();
                    $params["name"] = $user_nickname["nickname"];
                }
                $result = Db::name('collar_prize_log')->insertGetId($params);
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('插入失败！'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return view();
    }


    /*
     * 编辑
     */
    public function edit($ids=null){
        $row = $this->model->get($ids);
        $id  = $row['id'];
        if(empty($id)){
            $this->error(__("暂无资源！"));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('您暂无权限访问'));
            }
        }
        $rows = db('collar_prize_log')->where('id', $row['id'])->find();
        if ($this->request->isPost()){//提交
            $params = $this->request->post("row/a");
            if ($params) {
                if($params["identiy"]){
                    $user_nickname = db('user')->field("nickname,username")->where('id', $params["uid"])->find();
                    $params["name"] = $user_nickname["nickname"];
                }else{
                    $params["uid"]='';
                }
                $params = $this->preExcludeFields($params);
                $res = db('collar_prize_log')->where('id',$id)->update($params);
                if ($res !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign('row', $rows);
        return view();
    }

}
