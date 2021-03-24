<?php
/**
 * member.php
 * Create By Company JUN HE
 * User XF
 * @date 2021-02-05 16:19
 */

namespace app\api\validate;
use think\Validate;

class MemberAddress extends Validate
{
    protected $rule = [
        'name'        =>'require',
        'country'     =>'require',
        'city'        =>'require',
        'district'    =>'require',
        'address'     =>'require',
        'address_name'=>'require',
        'mobile'      => 'require|regex:^1\d{10}$'
    ];
    protected $message = [
        'name.require'      => '请输入姓名！',
        'country.require'   => '请输入国家！',
        'city.require'      => '请输入城市！',
        'district.require'  => '请输入区/县！',
        'address.require'   => '请输入详细地址信息！',
        'address_name.require'=>'请输入地址名称！',
        'mobile.regex'        =>'手机号格式不正确！',
    ];
}