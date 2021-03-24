<?php
/**
 * Share.php
 * Create By Company JUN HE
 * User XF
 * @date 2021-01-19 9:54
 */
namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Exception;
use think\exception\DbException;


class Share Extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     *分享基本信息及进度展示
     */
    public function sharemsg(){
        $data = array();
        //分享文案信息
        $share_message = Db("collar_link")->field("id,name,desc,link,image")->where("status='1' AND deletetime is null")->orderRaw('rand()')->find();
        //分享人数及进度
        $share_num =  Db("collar_share_log")->where("send_userid={$this->auth->id} AND user_loop={$this->auth->experience_num}")->group("ip")->count("id");
        //拼装参数n=分享次数 s=分享者ID
       if($share_message && $share_message["link"]){
           //分享次数
           $share_num_log = Db("collar_shar_num_log")->where("user_id={$this->auth->id} AND user_loop={$this->auth->experience_num}")->count("id");
           $share_num_log+=1;
           $share_message["link"] = $share_message["link"]."?n=".$share_num_log."?s=".$this->auth->id;
       }
        $data_shar = ["user_id"=>$this->auth->id,"user_loop"=>$this->auth->experience_num,"createtime"=>time()];
        Db("collar_shar_num_log")->insert($data_shar);

        $data["progress"]["total"] = 50;
        $data["progress"]["num"] = $share_num;
        $data["progress"]["proportion"] = $share_num.'/'.$data["progress"]["total"];
        $data["progress"]["status"] = ($share_num>=$data["progress"]["total"])?1:0;//完成进度状态展示
        $data["share_message"] = !empty($share_message)?imgUrl($share_message,"image"):"";
        $this->success("数据返回成功",$data);
    }


    /*
     * 接收分享者点击链接增加
     */
    public function add_share(){
        $send_userid = $this->request->param("send_userid")?$this->request->param("send_userid"):$this->error("数据发送错误！");
        //获取ip和接收这用户
        $ip = getips();
        $shar_user = Db("collar_share_log")->field("id")->where("send_userid={$send_userid} AND user_loop={$this->auth->experience_num} AND ip='{$ip}' AND user_id={$this->auth->id}")->find();
        if(!$shar_user){
            $data = ["send_userid"=>$send_userid,"user_id"=>$this->auth->id,"ip"=>getips(),"user_loop"=>$this->auth->experience_num,"num"=>$this->request->param("num"),"createtime"=>time()];
            Db("collar_share_log")->insert($data);
        }
        $this->success("数据返回成功");
    }



}