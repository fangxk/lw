<?php

namespace app\admin\model\collar;

use think\Model;


class Category extends Model
{

    

    

    // 表名
    protected $name = 'collar_goods_category';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getIsStatusList()
    {
        return ['1' => __('Is_status 1'), '0' => __('Is_status 0')];
    }


    public function getIsStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_status']) ? $data['is_status'] : '');
        $list = $this->getIsStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
