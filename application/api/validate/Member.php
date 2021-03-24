<?php
/**
 * member.php
 * Create By Company JUN HE
 * User XF
 * @date 2021-02-05 16:19
 */

namespace app\api\validate;
use think\Validate;

class Member extends Validate
{
    protected $rule = [
        'content' => 'require',
        'phone'   => 'require|max:11'
    ];
    protected $message = [
        'content.require' => '请输入您的建议',
        'phone.require'   => '请输入手机号码',
        'phone.max'       => '手机号格式不正确'
    ];
}