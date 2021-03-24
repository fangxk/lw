<?php
/**
 * Member.php
 * Create By Company JUN HE
 * User XF
 * @date 2021-01-19 17:46
 */
namespace app\api\controller;
use app\common\controller\Api;
use fast\Random;
use think\Loader;
use think\Validate;

class Member extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';



    /**
     * 登录
     * @param mobile 手机号
     */
    public function member_mobile()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $mobile = $this->request->param("mobile");
        if(!$mobile){
            $this->error(__('手机号不能为空！'));
        }
        if(!validate::regex($mobile,"^1\d{10}$")){
            $this->error(__('手机号格式错误！'));
        }
        // 开始登录
        $user = \app\common\model\User::getByMobile($mobile);

        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('用户已被禁用！'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->userLogin($mobile);
        } else {
            $ret = $this->auth->register(Random::Randname(),'123123', '', $mobile, []);
        }
        //登录成功返回用户信息
        if ($ret) {
            $data = [
                'userinfo' => $this->auth->getUserinfo(),
            ];
            $this->success(__('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     *
     * @author XF
     * 用户详情实时展示
     */
    public function getuserinfo(){
        $userinfo = $this->auth->getUserinfo();
        $this->success("返回数据成功",$userinfo);
    }

    /**
     *
     * @author XF
     * 虚拟会员展示
     * @date 2021-02-07 10:35
     */
    public function member_empty(){
        $show_people = $this->request->param("show_people");
        $total_people = !empty($show_people)?$show_people:10; //随机用户数量
        for($i=1;$i<=$total_people;$i++){
          $peoples[$i]["username"] = Random::Randname();
          $peoples[$i]["avatar"]   = cdnurl(config("site.ptavatar"),true);
        }
        $this->success("获取成功",$peoples);
    }

    /**
     *
     * @author XF
     * 增加用户邀请注册记录
     * @date 2021-03-05 16:12
     */
    public  function invitation_record(){
        //查询记录
        $invitation_id = $this->request->param("invitation_id");
        $num           = $this->request->param("num");
        $uid    = $this->auth->id;
        $ip     = getips();
        $invitations = Db("collar_invitation_record")->where("user_id={$uid} AND invitation_id={$invitation_id} AND ip='{$ip}'")->find();
        if(empty($invitations)){//插入邀请记录
            $data = ["user_id"=>$uid,"ip"=>$ip,"invitation_id"=>$invitation_id,"num"=>$num,"createtime"=>time()];
            Db("collar_invitation_record")->insert($data);
            //增加虚拟用户注册量数量
            //$regnum = Db("collar_invitation_record")->where("invitation_id={$invitation_id}")->count("id");

        }
        $this->success("返回数据成功！");
    }


    //增加浏览记录及更新用户
    public function addmember(){
        $uid = $this->auth->id;
        $invitation_id = $this->request->param("invitation_id");
        //获取邀请的用户数量
        $data = ["user_id"=>$uid,"ip"=>getips(),"invitation_id"=>$invitation_id,"createtime"=>time()];
        Db("collar_views_record")->insert($data);
        $this->success("浏览记录增加成功");
    }

    /*
     * 更新用户的虚拟数量
     */
    public function editmember(){
        //不同ip访问量
        $invitation_num =  Db("collar_views_record")->where("invitation_id={$this->auth->id}")->group("ip")->count("id");
        //真实用户的注册量
        $real_regnum =  Db("collar_invitation_record")->where("invitation_id={$this->auth->id}")->group("user_id")->count("id");

        //虚拟用户的数量
        $virtual_num =ceil(($invitation_num-$real_regnum)*0.8-$this->auth->virtual_num);
        if($virtual_num>0){//虚拟用户变化更新
            $user = $this->auth->getUser();
            $user->virtual_num = $virtual_num;
            $user->save();
        }
        $this->success("返回数据成功");
    }



    /**
     * 用户体验收货地址添加修改
     */
    public function member_address(){
        $param = $this->request->param();
        if($param["type"]=='add'){//用户添加地址
            unset($param["type"]);
            //查询用户是否有收货地址
            $member_address = db("collar_user_address")->where("user_id={$this->auth->id}")->find();
            if($member_address){
                $this->error("您已创建收货地址！");
            }
            $validate = Loader::validate('MemberAddress');
            if (!$validate->check($param)) {
                $this->error($validate->getError());
            }
            $param["user_id"]    = $this->auth->id;
            $param["createtime"] = time();
            $ret = db("collar_user_address")->insert($param);
            if($ret){
                $this->success("收货地址创建成功");
            }else{
                $this->error("收货地址创建失败！");
            }
        }
        if($param["type"]=='edit'){
            unset($param["type"]);
            //查询用户是否有收货地址
            $member_address = db("collar_user_address")->where("user_id={$this->auth->id}")->find();
            if(empty($member_address)){
                $this->error("地址数据错误！");
            }
            $validate = Loader::validate('MemberAddress');
            if (!$validate->check($param)) {
                $this->error($validate->getError());
            }
            $param["user_id"]    = $this->auth->id;
            $ret = db("collar_user_address")->where("id={$member_address['id']}")->update($param);
            if($ret){
                $this->success("收货地址修改成功");
            }else{
                $this->error("收货地址修改失败！");
            }
        }
        $this->error("非法操作！");
    }

}