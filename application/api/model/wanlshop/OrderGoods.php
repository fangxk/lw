<?php

namespace app\api\model\wanlshop;

use think\Model;
use traits\model\SoftDelete;

class OrderGoods extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'wanlshop_order_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
}
