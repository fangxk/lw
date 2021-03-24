<?php
/**
 * member.php
 * Create By Company JUN HE
 * User XF
 * @date 2021-02-05 16:19
 */

namespace app\api\validate;
use think\Validate;

class Tyaddress extends Validate
{
    protected $rule = [
        'goods_id' => 'require',
        'phone'   => 'require|max:11'
    ];
    protected $message = [
        'content.require' => '请输入您的建议',
        'phone.require'   => '请输入手机号码',
        'phone.max'       => '手机号格式不正确'
    ];
}