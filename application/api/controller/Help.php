<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;
class Help extends Api
{
    protected $noNeedLogin = ["help_list","help_info","feedback_type"];
    protected $noNeedRight = '*';

    /**
     * 帮助中心列表
     */
    public function help_list(){
        $list = Db("collar_help_center")->field("id,title,createtime")->where("status=1")->order("weigh DESC")->select();
        $this->success("数据返回成功",$list);
    }

    /**
     * 帮助中心详情
     */
    public function help_info(){
        $id   = $this->request->param("id");
        if(empty($id)){
            $this->success("数据请求错误！");
        }
        $info = Db("collar_help_center")->field("id,title,content,createtime")->where("id={$id} AND status=1")->find();
        $this->success("数据返回成功",$info);
    }

    /**
     * 反馈类型列表展示
     */
    public function feedback_type(){
        $list = config("site.feedback_type");
        if($list){
            for ($i=1;$i<count($list);$i++){
                for($j=1;$j<count($list)-1-$i;$j++){
                    if($list[$j]>$list[$j+1]){
                        $temp = $list[$j];
                        $list[$j] = $list[$j+1];
                        $list[$j+1] = $temp;
                    }
                }
                $data = array("id"=>$i,"title"=>$list[$i]);
                $arra[]= $data;
            }
        }
        $this->success("数据成功",isset($arra)?$arra:[]);
    }

    /**
     * 提交反馈内容
     */
    public function feedback_sumbinfo(){
        $param = $this->request->param();
        $uid = $this->auth->id;
        if(empty($param["name"])||empty($param["feedback"])||empty($param["mobile"])){
            $this->error("请求数据不能为空！");
        }
        if(empty($uid)){
            $this->error("用户信息不存在！！");
        }
        $data = array("uid"=>$uid,"name"=>$param["name"],"feedback"=>$param["feedback"],"mobile"=>$param["mobile"],"createtime"=>time());
        $res = Db("collar_feedback")->insert($data);
        if($res){
            $this->success("反馈成功");
        }else{
            $this->error("数据提交失败！");
        }
    }


}
?>