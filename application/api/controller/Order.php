<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;


class Order extends Api
{
    protected $noNeedLogin = 'commentOrder';
    protected $noNeedRight = '*';

    /**
     * 评论订单
     *
     * @ApiSummary  (WanlShop 订单接口评论订单)
     * @ApiMethod   (POST)
     *
     * @param string $id 订单ID
     */
    public function commentOrder()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post ? $post : ($this->error(__('数据异常')));
            $user_id = $this->auth->id;

            // 判断权限
            $this->getOrderState($post['order_id']) != 4 ? ($this->error(__('已经评论过或订单异常'))):'';
            // 生成列表
            $commentData = [];
            foreach ($post['goodsList'] as $value) {
                $commentData[] = [
                    'user_id'  => $user_id,
                    'shop_id'  => $post['shop']['id'],
                    'order_id' => $post['order_id'],
                    'goods_id' => $value['goods_id'],
                    'order_goods_id' => $value['id'],
                    'state'  => $value['state'],
                    'content'=> $value['comment'],
                    'suk'    => $value['difference'],
                    'images' => $value['imgList'],
                    'score'  => round((($post['shop']['describe'] + $post['shop']['service'] + $post['shop']['deliver'] + $post['shop']['logistics']) / 4) ,1),
                    'score_describe' => $post['shop']['describe'],
                    'score_service'  => $post['shop']['service'],
                    'score_deliver'  => $post['shop']['deliver'],
                    'score_logistics'=> $post['shop']['logistics'],
                    'switch' => 0
                ];
                //评论暂不考虑并发，为列表提供好评付款率，减少并发只能写进商品中
                model('app\api\model\wanlshop\Goods')->where(['id' => $value['goods_id']])->setInc('comment');
                if($value['state'] == 0){
                    model('app\api\model\wanlshop\Goods')->where(['id' => $value['goods_id']])->setInc('praise');
                }else if($value['state'] == 1){
                    model('app\api\model\wanlshop\Goods')->where(['id' => $value['goods_id']])->setInc('moderate');
                }else if($value['state'] == 2){
                    model('app\api\model\wanlshop\Goods')->where(['id' => $value['goods_id']])->setInc('negative');
                }
            }
            if(model('app\api\model\wanlshop\GoodsComment')->saveAll($commentData)){
                $order = model('app\api\model\wanlshop\Order')
                    ->where(['id' => $post['order_id'], 'user_id' => $user_id])
                    ->update(['state' => 6]);
            }
            //更新店铺评分
            $score = model('app\api\model\wanlshop\GoodsComment')
                ->where(['user_id' => $user_id])
                ->select();
            // 从数据集中取出
            $describe = array_column($score,'score_describe');
            $service = array_column($score,'score_service');
            $deliver = array_column($score,'score_deliver');
            $logistics = array_column($score,'score_logistics');
            // 更新店铺评分
            model('app\api\model\wanlshop\Shop')
                ->where(['id' => $post['shop']['id']])
                ->update([
                    'score_describe' => bcdiv(array_sum($describe), count($describe), 1),
                    'score_service' => bcdiv(array_sum($service), count($service), 1),
                    'score_deliver' => bcdiv(array_sum($deliver), count($deliver), 1),
                    'score_logistics' => bcdiv(array_sum($logistics), count($logistics), 1)
                ]);
            $this->success('ok',[]);
        }
        $this->error(__('非法请求'));
    }

}