<?php

namespace app\api\library;

use Exception;
use think\Db;
use think\Request;
use fast\Http;
use fast\Random;

/**
 * 自定义API模块的错误显示
 */
class WithdrawLog
{
    /**
     * 变更会员余额
     * @param int    $money   余额
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     * @param string $type    类型
     * @param string $ids  	  业务ID
     */
    public static function money($money, $user_id, $memo, $type = '', $ids = '')
    {
        $user = model('app\common\model\User')->get($user_id);
        //print_r($user->money);die;
        if ($user && $money != 0) {
            $before = $user->money;
            $after = function_exists('bcadd') ? bcadd($user->money, $money, 2) : $user->money + $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            $row = model('app\common\model\WithDrawLog')->create([
                'user_id' => $user_id,
                'money' => $money, // 操作金额
                'before' => $before, // 原金额
                'after' => $after, // 增加后金额
                'memo' => $memo, // 备注
                'type' => $type, // 类型
                'service_ids' => $ids // 业务ID
            ]);
            return $row;
        }else{
            return ['code' => 500 ,'msg' => '变更金额失败'];
        }
    }

}
