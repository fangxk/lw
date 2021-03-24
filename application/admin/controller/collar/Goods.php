<?php

namespace app\admin\controller\collar;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 体验商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{
    
    /**
     * Goods模型对象
     * @var \app\admin\model\collar\Goods
     */
    protected $model = null;
    protected $searchFields = 'name';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\collar\Goods;
        $this->view->assign("goodsStatusList", $this->model->getGoodsStatusList());
        $this->view->assign("hasoptionList", $this->model->getHasoptionList());
        $this->view->assign("isIndexShowList", $this->model->getIsIndexShowList());
        $this->view->assign("istopList", $this->model->getIstopList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                    ->with(['collargoodscategory'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            foreach ($list as $row) {
                $row->visible(['id','weigh','name','goods_status','price','total','createtime']);
                $row->visible(['collargoodscategory']);
				$row->getRelation('collargoodscategory')->visible(['id','name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        //设置过滤方法
        $this->request->filter(['']);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $result = false;
                Db::startTrans();
                try {
                    //判断是否是多规格
                    if($params["hasoption"]){
                        $spudata = isset($params['spu'])?$params['spu']:$this->error(__('请填写商品属性'));
                        $spuItem = isset($params['spuItem'])?$params['spuItem']:$this->error(__('请填写-产品规格'));
                    }else{//不是度多规格删除spu
                        unset($params['spu']);
                    }
                    if(empty($params["name"])){
                        $this->error(__('商品名称必须填！'));
                    }
                    if(empty($params["image"])){
                        $this->error(__('商品缩略图必填！'));
                    }
                    //封装数据
                    $this->model->category_ids = $params['category_ids'];
                    $this->model->name = $params['name'];
                    $this->model->desc = $params['desc'];
                    $this->model->goods_status = $params['goods_status'];
                    $this->model->hasoption = $params['hasoption'];
                    $this->model->price = $params['price'];
                    $this->model->costprice = $params['costprice'];
                    $this->model->productprice = $params['productprice'];
                    $this->model->sales = $params['sales'];
                    $this->model->seller_count = $params['seller_count'];
                    $this->model->showsales = $params['showsales'];
                    $this->model->weight = $params['weight'];
                    $this->model->total = $params['total'];
                    $this->model->image = $params['image'];
                    $this->model->images = $params['images'];
                    $this->model->day_salescount = $params['day_salescount'];
                    $this->model->is_index_show = $params['isIndexShowList'];
                    $this->model->istop = $params['istopList'];

                    if($this->model->allowField(true)->save()){
                        $result = true;
                    }

                    if($params["hasoption"]){//多规格商品
                        // 写入SPU
                        $spu = [];
                        foreach (explode(",", $spudata) as $key => $value) {
                            $spu[] = [
                                'goods_id'	=> $this->model->id,
                                'name'		=> $value,
                                'item'		=> $spuItem[$key]
                            ];
                        }

                        if(!model('app\admin\model\collar\GoodsSpu')->allowField(true)->saveAll($spu)){
                            $result == false;
                        }

                        // 写入SKU
                        $sku = [];//print_r($params);die;
                        foreach ($params['sku']  as $key => $value) {
                            $sku[] = [
                                'goods_id' 		=> $this->model->id,
                                'difference' 	=> $value,
                                'market_price' 	=> $params['market_price'][$key],
                                'price' 		=> $params['prices'][$key],
                                'stock' 		=> $params['stocks'][$key],
                                'weigh' 		=> $params['weigh'][$key]!=''?$params['weigh'][$key] : 0,
                                'sn' 			=> $params['sn'][$key]!=''?$params['sn'][$key] : 'wanl_'.time()
                            ];
                        }

                        if(!model('app\admin\model\collar\GoodsSku')->allowField(true)->saveAll($sku)){
                            $result == false;
                        }
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        //设置过滤方法
        $this->request->filter(['']);
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        // 查询SKU
        $skuItem = model('app\admin\model\collar\GoodsSku')
            ->where(['goods_id' => $ids, 'state' => 0])
            ->field('id,difference,price,market_price,stock,weigh,sn,sales,state')
            ->select();
        
        //print_r($skuItem);die;
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                // 判断产品属性是否存在
                $result = false;
                Db::startTrans();
                try {

                    //判断是否是多规格
                    if($params["hasoption"]){
                        $spudata = isset($params['spu'])?$params['spu']:$this->error(__('请填写商品属性'));
                        $spuItem = isset($params['spuItem'])?$params['spuItem']:$this->error(__('请填写-产品规格'));
                    }else{//不是度多规格删除spu
                        unset($params['spu']);
                    }

                    // 写入表单
                    $data          = $params;
                    $data['price'] = $data['price'];
                    $result = $row->allowField(true)->save($data);

                    /*判断是否启用规格*/
                    if($params["hasoption"]){
                        // 删除原来数据,重新写入SPU
                        model('app\admin\model\collar\GoodsSpu')
                            ->where('goods_id','in',$ids)
                            ->delete();
                        $spu = [];
                        foreach (explode(",", $spudata) as $key => $value) {
                            $spu[] = [
                                'goods_id' => $ids,
                                'name' => $value,
                                'item' => $spuItem[$key]
                            ];
                        }
                        if(!model('app\admin\model\collar\GoodsSpu')->allowField(true)->saveAll($spu)){
                            $result == false;
                        }
                        //标记旧版SKU数据
                        $oldsku = [];
                        foreach ($skuItem as $value) {
                            $oldsku[] = [
                                'id' => $value['id'],
                                'state' => 1
                            ];
                        }
                        if(!model('app\admin\model\collar\GoodsSku')->allowField(true)->saveAll($oldsku)){
                            $result == false;
                        }
                        // 写入SKU
                        $sku = [];
                        foreach ($params['sku'] as $key => $value) {
                            $sku[] = [
                                'goods_id' => $ids,
                                'difference' => $value,
                                'market_price' => $params['market_price'][$key],
                                'price' => $params['prices'][$key],
                                'stock' => $params['stocks'][$key],
                                'weigh' => $params['weigh'][$key]!=''?$params['weigh'][$key] : 0,
                                'sn' => $params['sn'][$key]!=''?$params['sn'][$key] : 'wanl_'.time()
                            ];
                        }
                        if(!model('app\admin\model\collar\GoodsSku')->allowField(true)->saveAll($sku)){
                            $result == false;
                        }
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $spuData = model('app\admin\model\collar\GoodsSpu')->all(['goods_id' => $ids]);
        $suk = [];
        foreach ($skuItem as $vo) {
            $suk[] = explode(",", $vo['difference']);
        }
        $spu = [];
        foreach ($spuData as $vo) {
            $spu[] = $vo['name'];
        }
        $spuItem = [];
        foreach ($spuData as $vo) {
            $spuItem[] = explode(",", $vo['item']);
        }
        $skulist = [];
        foreach ($skuItem as $vo) {
            $skulist[$vo['difference']] = $vo;
        }
        $this->assignconfig('spu', $spu);
        $this->assignconfig('spuItem', $spuItem);
        $this->assignconfig('sku', $suk);
        $this->assignconfig('skuItem', $skulist);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
