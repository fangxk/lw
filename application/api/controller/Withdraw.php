<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\WithdrawLog;
use think\Exception;
use think\exception\DbException;
use think\Db;

/**
 * 提现接口
 */
class Withdraw extends Api
{
    protected $noNeedLogin = ["delete_withdraw"];
    protected $noNeedRight = ['*'];

    /*
     * 用户提现支持卡
     */
    public function change_bank(){
        $bank = [["id"=>"ALIPAY","name"=>"支付宝账号"],["id"=>"CBC","name"=>"中国银行"],["id"=>"CCB","name"=>"建设银行"],["id"=>"ICBC","name"=>"工商银行"],["id"=>"ABC","name"=>"农业银行"]];
        $this->success("成功",$bank);
    }

    /**
     *展示提现账号列表
     */
    public function show_withdraw()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $row = model('app\api\model\Account')
                ->where(['user_id' => $this->auth->id])
                ->order('createtime desc')
                ->select();
            $this->success('数据返回成功', $row);
        }
        $this->error(__('非正常请求'));
    }

    /*
     * 添加提现账号
     */
    public function add_withdraw(){
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post['user_id'] = $this->auth->id;
            $row = model('app\api\model\Account')->allowField(true)->save($post);
            if($row){
                $this->success('ok', $row);
            }else{
                $this->error(__('新增失败'));
            }
        }
        $this->error(__('非正常请求'));
    }

    /*
     * 删除提现卡
     */
    public function delete_withdraw($ids = ''){
        $row = model('app\api\model\Account')
            ->where('id', 'in', $ids)
            ->where(['user_id' => $this->auth->id])
            ->delete();
        if($row){
            $this->success('删除成功', $row);
        }else{
            $this->error(__('删除失败'));
        }
    }


    /*
     * 用户提现
     */
    public function withdraw(){
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            // 金额
            $money = $this->request->post('money');
            // 账户
            $account_id = $this->request->post('account_id');
            if ($money <= 0) {
                $this->error('提现金额不正确');
            }
            if ($money > $this->auth->money) {
                $this->error('提现金额超出可提现额度');
            }
            if (!$account_id) {
                $this->error("提现账户不能为空");
            }
            // 查询提现账户
            $account = \app\api\model\Account::where(['id' => $account_id, 'user_id' => $this->auth->id])->find();
            if (!$account) {
                $this->error("提现账户不存在");
            }
            $withdrawstatus      = config("site.with_drawstatus");
            $withdraw_minmoney   = config("site.withdraw_minmoney");
            $withdraw_monthlimit = config("site.withdraw_monthlimit");


            if ($withdrawstatus){
                $this->error("系统该关闭提现功能，请联系平台客服");
            }
            if (isset($withdraw_minmoney) && $money < $withdraw_minmoney) {
                $this->error('提现金额不能低于' . $withdraw_minmoney . '元');
            }
            if ($withdraw_monthlimit) {
                $count = \app\api\model\WithDraw::where('user_id', $this->auth->id)->whereTime('createtime', 'month')->count();
                if ($count >= $withdraw_monthlimit) {
                    $this->error("已达到本月最大可提现次数");
                }
            }
            Db::startTrans();
            try {
                $data = [
                    'user_id' => $this->auth->id,
                    'money'   => $money,
                    'handingfee' => 0, // 手续费暂未开启
                    'type'    => $account['bankcode'],
                    'account' => $account['cardcode'],
                    'orderid' => date("Ymdhis") . sprintf("%08d", $this->auth->id) . mt_rand(1000, 9999)
                ];
                //添加提现
                $withdraw = \app\api\model\WithDraw::create($data);
                //变更会员余额
                $pay      = new WithdrawLog;
                $pay->money(-$money, $this->auth->id, '申请提现', 'withdraw', $withdraw['id']);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success('提现申请成功！请等待后台审核', $this->auth->money);
        }
        $this->error(__('非正常请求'));
    }


}
