<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;


class Home extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /*
     * 首页商品展示
     */
    public function home(){
        //首页获奖人公告
        $notice_prize = db("collar_prize_log")->field('name,prize')->where("status='1'")->order("weigh DESC")->limit(0,10)->select();
        //商品分类
        $data = db('collar_goods_category')->field("id as type_id,name,desc,image")->where("pid=0 AND is_status='1'")->order('weigh DESC')->select();
        foreach ($data as $ke=>$v){
            $data[$ke]["image"] = cdnurl($v["image"],true);
            $goods = db('collar_goods')
                ->field("id as goods_id,name,desc,image")
                ->where("category_ids in({$v["type_id"]})")
                ->order('weigh DESC')->select();
            $data[$ke]["goods"] = imgsurl($goods,'image');
        }
        $res = ["notice_prize"=>isset($notice_prize)?$notice_prize:'',"collar_goods"=>isset($data)?$data:''];
        $this->success("成功",$res);
    }

}