<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * 首页接口
 */
class Jumpline extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /*
     * 获得插队卡
     * 条件：1邀请用户需有注册用户每一批中有用户注册才能弹出插队卡
     */
    public function get_jumpline(){
        //注册
        $reg_log = Db("collar_invitation_record")->field("id,num")->where("invitation_id={$this->auth->id}")->order("id DESC")->find();
        $status = ["jumpline_status"=>0];

        //查询插队卡日志是否有
        $jumpline_log = Db("collar_jump_line")->where("user_id={$this->auth->id} AND user_loop={$this->auth->experience_num} AND reg_num={$reg_log["num"]}")->find();
        if(isset($jumpline_log) && empty($jumpline_log["status"])){//判断是否是有未用插队卡
            $status = ["jumpline_status"=>1];
        }
        if($reg_log["num"] && empty($jumpline_log)){//产生新的插队卡
            $user_experience = $this->auth->experience_num;
            //插队卡次数
            $jumpline_num = Db("collar_jump_line")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->count("id");
            $jump_numconfig = config("site.jump_set");

            if(empty($jumpline_num)){//首次直接用插队名次
                $jumpline_num =1;
            }else{//不是首次通过锁定卡计算次数
                $jumpline_num+=1;
            }
            //没有插队卡
            if($jumpline_num<=10){
                foreach ($jump_numconfig as $k=>$v){
                    if($jumpline_num==$k){
                        $newval = explode("-",trim($v));
                        $num    = rand(isset($newval["0"])?$newval["0"]:"99",isset($newval["1"])?$newval["1"]:"199");
                    }
                }
            }
            if($jumpline_num>10){
                $num = rand(5,12);
            };

            Db::startTrans();
            try {
                $data = array("user_id"=>$this->auth->id,"status"=>0,"num"=>$num,"user_loop"=>$this->auth->experience_num,"createtime"=>time(),"reg_num"=>$reg_log["num"]);
                Db("collar_jump_line")->insert($data);
                //添加插队卡
                $status["jumpline_status"] = 1;
                Db::commit();
            }catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
        }
        $this->success("数据返回成功！",$status);
    }


    /*
     * 添加插队卡记录
     */
    public function add_jumpline(){
        $user_experience = $this->auth->experience_num;
        $jumpline_data   = Db("collar_jump_line")->where("user_loop={$user_experience} AND user_id={$this->auth->id} AND status=0")->find();
        if(empty($jumpline_data)){
            $this->success('暂无插队卡可以用！');
        }
        //判断是否有锁定卡 有锁定卡执行更新用户的排队发货名次
        $data = array("status"=>1,"update_time"=>time());
        $res = Db("collar_jump_line")->where("id={$jumpline_data['id']}")->update($data);
        //查询出当前用户的排队发货名次 计算上次和这次插队的人数
        $line_data = Db("collar_line_log")->field("card_type,num")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->order("id DESC")->find();
        $data_area = $line_data["num"]-$jumpline_data["num"];
        $this->success('恭喜您成功插队'.$data_area.'人，赶快使用锁定卡锁定名次吧！');
    }

    /*
     * 进入任务更新任务接口
     *
     */
    public function task_update(){
        //锁定卡次数和插队卡次数一样 更新collar_line_log 中的排队发货次数
        //一样更新排队名次， 不一样开始执行下滑名次
        //锁定卡次数统计
        $user_experience = $this->auth->experience_num;
        $locking_num      = Db("collar_locking_log")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->count("id");
        //排队次数
        $junline_num      = Db("collar_jump_line")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->count("id");
        $junline_num_data = Db("collar_jump_line")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->order("id DESC")->find();
        if(empty($junline_num) || empty($locking_num)){
            $this->error("暂无任务可更新！");
        }
        $line_num         = $junline_num_data["num"];//插队最终名次
        if($locking_num<$junline_num){//开始执行直接下滑多少名次(条件锁定卡次数小于插队次数) collar_line_log
            //获取锁定卡次数
            $locking_num_data = Db("collar_locking_log")->field("num")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->order("id DESC")->find();
            $house_time =  floor((time()-$junline_num_data["update_time"])%86400/60/5);
            if($house_time>=1){//前5分钟
                $line_num+=1;
            }
            if($house_time>=2){//前10分钟
                $line_num+=2;
            }
            if($house_time>=3){//前15分钟
                $line_num+=3;
            }
            if($house_time>=4) {//此后每间隔5分钟
                for ($i = 0; $i <= $house_time - 3; $i++) {
                    //之后判断是否有锁定卡
                    if ($line_num <= $locking_num_data["num"]) {//直到用户上次使用锁定卡名次
                        $line_num += rand(1, 5);
                    } else {
                        $line_num = $locking_num_data["num"];
                        break;
                    }
                }
            }
        }
        //更新最终名次
        Db("collar_line_log")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->update(["num"=>$line_num]);
        $this->success("排队任务更新成功");
    }


    /*public function add_jumpline(){
        $user_experience = $this->auth->experience_num;
        $jumpline_num = Db("collar_jump_line")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->count("id");
        $jump_numconfig = config("site.jump_set");
        if(empty($jumpline_num)){//判断是否是第一次获取锁定卡 是第一次获取插队卡
            $jumpline_num+=1;
        }else{
            $jumpline_num+=1;
        }
        //没有插队卡
        if($jumpline_num<=10){
            foreach ($jump_numconfig as $k=>$v){
                if($jumpline_num==$k){
                    $newval = explode("-",trim($v));
                    $num    = rand(isset($newval["0"])?$newval["0"]:"99",isset($newval["1"])?$newval["1"]:"199");
                }
            }
        }
        if($jumpline_num>10){
            $num = rand(5,12);
        };
        Db::startTrans();
        try {
            //最新的插队名次
            $newjumpline = Db("collar_jump_line")->field("id,num")->where("user_loop={$user_experience} AND user_id={$this->auth->id}")->order("createtime DESC")->find();
            //计算插队多少人
            $new_jumpline_num = $newjumpline["num"]-$num;
            print_r($new_jumpline_num);die;
            $data = array("user_id"=>$this->auth->id,"num"=>$num,"user_loop"=>$this->auth->experience_num,"createtime"=>time());
            Db("collar_jump_line")->insert($data);
            //添加排队发货记录
            $data["card_type"] = "2";//插队卡类型
            //Db("collar_line_log")->insert($data);
            Db::commit();
        }catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('返回数据成功');
    }*/



}
