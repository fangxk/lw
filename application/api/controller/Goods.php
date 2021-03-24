<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;


class Goods extends Api
{
    protected $noNeedLogin = 'goods_list,goods_info,goods_attributes';
    protected $noNeedRight = '*';

    /*
     *商品列表
     */
    public function goods_list(){
        $pid   = $this->request->param("type_id");
        $page  = $this->request->param("page");
        $pageSize = 10;
        $goods = db("collar_goods")->field("category_ids,name,desc,image,price,productprice,hasoption")->where("category_ids='{$pid}'")->page($page,$pageSize)->select();
        $this->success("成 功",$goods);
    }

    /*
     * 体验商品详情 待修改关注
     */
    public function goods_info(){
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $goods_id = $this->request->request("goods_id");
        // 是否传入商品ID
        $goods_id ? $goods_id : ($this->error(__('非正常访问')));
        // 查询商品
        $goods = Db("collar_goods")
            ->where(['id' => $goods_id])
            ->find();
        // 浏览+1 & 报错
        if($goods && $goods['goods_status'] == '1'){
            Db("collar_goods")->where("id={$goods['id']}")->setInc('views'); // 浏览+1
            $this->addbrowse($goods); // 写入访问日志
        }else{
            $this->error(__('所查找的商品尚未上架'));
        }
        $this->success('返回成功', $goods);
    }

    /*
     * 商品规格
     */
    public function goods_attributes(){
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $id = $this->request->request("id");
        // 是否传入商品ID
        $id ? $id : ($this->error(__('非正常访问')));
        $goods_attr = Db("collar_goods_spu")->field("id,name,item")->where("goods_id={$id}")->select();
        foreach ($goods_attr as $k=>$v){
            if($v["item"]){
                $goods_attr[$k]["item"] = explode(',',$v["item"]);
            }
        }
        $this->success("成 功",$goods_attr);
    }

    /**
     * 选择体验商品
     */
    public function change_goods(){
        $param  = $this->request->param();
        if(empty($param["goods_id"]) || empty($param["address_id"])){
            $this->error("参数错误！");
        }
        //商品ID
        $gooods = Db("collar_goods")->field("id,name")->where("id={$param['goods_id']}")->find();
        //地址ID
        $addess = Db("collar_user_address")->field("id,name")->where("id={$param['address_id']}")->find();

        if(empty($gooods)){
            $this->error("商品不存在！！");
        }
        if(empty($addess)){
            $this->error("商品地址不存在！！",'','2');
        }

    }

    /**
     * 保存浏览商品记录
     * @param array $goods
     */
    public function addbrowse($goods =[])
    {
        //保存浏览记录
        $uuid = $this->request->server('HTTP_UUID');
        if(!isset($uuid)){
            $charid = strtoupper(md5($this->request->header('user-agent').$this->request->ip()));
            $uuid = substr($charid, 0, 8).chr(45).substr($charid, 8, 4).chr(45).substr($charid,12, 4).chr(45).substr($charid,16, 4).chr(45).substr($charid,20,12);
        }
        $record = model('app\api\model\Record');
        $where = [
            'uuid' => $uuid,
            'goods_id' => $goods['id']
        ];

        if($record->where($where)->count() == 0){
            if ($this->auth->isLogin()) {
                $record->user_id = $this->auth->id;
            }
            $record->uuid = $uuid;
            $record->goods_id = $goods['id'];
            $record->category_id = $goods['category_ids'];
            $record->ip = $this->request->ip();
            $record->save();
        }else{
            $record->where($where)->setInc('views'); //访问+1
        }
    }


    /**
     * 关注商品
     *
     * @param string $goods_id 商品ID
     */
    public function follow()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $goods_id = $this->request->request("goods_id");
        // 是否传入商品ID
        $goods_id ? $goods_id : ($this->error(__('非正常访问')));
        // 加载商品模型
        $goodsModel = model('app\api\model\Goods');
        $goodsFollowModel = model('app\api\model\GoodsFollow');
        $data = [
            'user_id' => $this->auth->id,
            'goods_id' => $goods_id
        ];
        if($goodsFollowModel->where($data)->count() == 0){//增加关注
            $goodsFollowModel->save($data);
            $goodsModel->where(['id' => $goods_id])->setInc('like'); //关注+1
            $follow = true;
        }else{//取消关注
            $goodsFollowModel->where($data)->delete();
            $goodsModel->where(['id' => $goods_id])->setDec('like'); //关注-1
            $follow = false;
        }
        $this->success("返回成功", $follow);
    }



}