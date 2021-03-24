<?php

namespace app\api\controller\wanlshop;

use app\common\controller\Api;
use fast\Tree;

class Category extends Api
{
    protected $noNeedLogin = ['*'];

    protected $noNeedRight = ['*'];


    /**
     * 获取商品分类以及子分类
     */
    public function getCategoryList()
    {
        $tree = Tree::instance();
        $tree->init(model('app\api\model\wanlshop\Category')->where(['type' => 'goods', 'status' => 'normal', 'isnav' => 1])->field('id, pid, name, image')->order('weigh asc')->select());
        $this->success('获取成功', $tree->getTreeArray(0));
    }


    /**
     * 获取一级分类
     */
    public function getOneCategory()
    {
        $data = model('app\api\model\wanlshop\Category')->where(['type' => 'goods', 'status' => 'normal', 'isnav' => 1, 'pid' => 0])->field('id, pid, name, image')->order('id desc')->select();
        $data = collection($data)->each(function ($item) {
            $item['level'] = 1;
            $item["image"] = cdnurl($item['image'],true);
            return $item;
        });

        $this->success('获取成功', $data);
    }


    /**
     * 获取二级分类
     */
    public function getTwoCategory()
    {
        $pid = $this->request->param('pid');

        $data = model('app\api\model\wanlshop\Category')->where(['type' => 'goods', 'status' => 'normal', 'isnav' => 1, 'pid' => $pid])->field('id, pid, name, image')->order('id desc')->select();
        $data = collection($data)->each(function ($item) {
            $item['level'] = 2;
            $item["image"] = cdnurl($item['image'],true);
            return $item;
        });
        $this->success('获取成功', $data);
    }


    /**
     * 获取三级分类
     */
    public function getThreeCategory()
    {
        $pid = $this->request->param('pid');

        $data = model('app\api\model\wanlshop\Category')->where(['type' => 'goods', 'status' => 'normal', 'isnav' => 1, 'pid' => $pid])->field('id, pid, name, image')->order('id desc')->select();
        $data = collection($data)->each(function ($item) {
            $item['level'] = 3;
            $item["image"] = cdnurl($item['image'],true);
            return $item;
        });
        $this->success('获取成功', $data);
    }
}