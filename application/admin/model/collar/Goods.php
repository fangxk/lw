<?php

namespace app\admin\model\collar;

use think\Model;
use traits\model\SoftDelete;

class Goods extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'collar_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'goods_status_text',
        'hasoption_text',
        'is_index_show_text',
        'istop_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getGoodsStatusList()
    {   // '3' => __('Goods_status 3')
        return ['1' => __('Goods_status 1'), '0' => __('Goods_status 0'), '2' => __('Goods_status 2')];
    }

    public function getHasoptionList()
    {
        return ['0' => __('Hasoption 0'), '1' => __('Hasoption 1')];
    }

    public function getIsIndexShowList()
    {
        return ['0' => __('Is_index_show 0'), '1' => __('Is_index_show 1')];
    }

    public function getIstopList()
    {
        return ['0' => __('Istop 0'), '1' => __('Istop 1')];
    }


    public function getGoodsStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['goods_status']) ? $data['goods_status'] : '');
        $list = $this->getGoodsStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getHasoptionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['hasoption']) ? $data['hasoption'] : '');
        $list = $this->getHasoptionList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsIndexShowTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_index_show']) ? $data['is_index_show'] : '');
        $list = $this->getIsIndexShowList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIstopTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['istop']) ? $data['istop'] : '');
        $list = $this->getIstopList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function collargoodscategory()
    {
        return $this->belongsTo('app\admin\model\collar\Category', 'category_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
