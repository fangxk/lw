<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;

/**
 * 卡片类型管理
 */
class Cardtype extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     *
     *获取展示锁定卡
     */
    public function show_locking(){
        $uid =$this->auth->id;
        $lockingres = Db("collar_locking_log")->field("user_loop,num,createtime")->where("user_id={$uid} AND user_loop={$this->auth->experience_num}")->order("createtime DESC")->find();
        $this->success("数据请求成功",$lockingres);
    }

    /*
     * 添加锁定卡记录
     */
    public function add_locking(){
        //体验次数
        $user_experience = $this->auth->experience_num;
        $num = empty($this->request->param("num"))?$this->error("用户当前名次不能为空！"):$this->request->param("num");
        $data = array("user_id"=>$this->auth->id,"num"=>$num,"user_loop"=>$this->auth->experience_num,"createtime"=>time());
        Db("collar_locking_log")->insert($data);
        $this->success('返回数据成功');
    }


}
