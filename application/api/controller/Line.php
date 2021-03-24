<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;

/**
 * 首页接口
 */
class Line extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     *展示随机排队发货
     */
    public function show_line()
    {
        //用户随机排队发货名次
        $line_order = rand(198,298);
        $data["line_order"] = $line_order;
        $this->success('请求成功',$data);
    }

    /*
     * 添加排队发货名次
     */
    public function add_line(){
        //次数限制
        $num = $this->request->param("num");
        $data = array("user_id"=>$this->auth->id,"num"=>$num,"card_type"=>0,"user_loop"=>$this->auth->experience_num,"createtime"=>time());
        Db("collar_line_log")->insert($data);
        $this->success('返回数据成功');
    }

    /**
     *
     * 展示任务数据
     */
    public function show_task(){

    }

}
