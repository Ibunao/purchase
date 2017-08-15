<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use frontend\config\ParamsClass;
/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $order_id
 * @property string $purchase_id
 * @property string $status
 * @property string $customer_id
 * @property string $customer_name
 * @property string $cost_item
 * @property string $create_time
 * @property string $edit_time
 * @property string $disabled
 */
class OrderModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'purchase_id', 'cost_item', 'create_time'], 'required'],
            [['order_id', 'purchase_id', 'customer_id', 'create_time', 'edit_time'], 'integer'],
            [['status', 'disabled'], 'string'],
            [['cost_item'], 'number'],
            [['customer_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'purchase_id' => 'Purchase ID',
            'status' => 'Status',
            'customer_id' => 'Customer ID',
            'customer_name' => 'Customer Name',
            'cost_item' => 'Cost Item',
            'create_time' => 'Create Time',
            'edit_time' => 'Edit Time',
            'disabled' => 'Disabled',
        ];
    }
    /**
     * order/default/index
     * 商品订单查询
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function orderList($params)
    {
        $query = (new Query())->from('meet_order_items as oi')
            ->where(['oi.disabled' => 'false'])
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id');

        //价格可能需要的是订单详情里的价格也就是 amount
        $select = ['sum(oi.nums)as nums', 'sum(oi.amount) as amount', 'p.name', 'p.cost_price', 'p.style_sn', 'p.product_id', 'p.img_url', 'p.serial_num', 'p.cat_b', 'p.cat_m', 'p.cat_s', 'p.size_id', 'p.type_id', 'oi.order_id'];
        if (!empty($params['purchase'])) {
            $query->andWhere(['or', "p.purchase_id='".$params['purchase']."'", "p.purchase_id='".Yii::$app->params['purchaseAB']."'"]);
        }

        if (!empty($params['style_sn'])) {
            $query->andWhere(['p.style_sn' => $params['style_sn']]);
        }
        if (!empty($params['cat_big'])) {
            $query->andWhere(['p.cat_b' => $params['cat_big']]);
        }
        if (!empty($params['cat_middle'])) {
            $query->andWhere(['p.cat_m' => $params['cat_middle']]);
        }
        if (!empty($params['cat_small'])) {
            $query->andWhere(['p.cat_s' => $params['cat_small']]);
        }

        if (!empty($params['season'])) {
            $query->andWhere(['p.season_id' => $params['season']]);
        }


        if (!empty($params['level'])) {
            $query->andWhere(['p.level_id' => $params['level']]);
        }

        if (!empty($params['wave'])) {
            $query->andWhere(['p.wave_id' => $params['wave']]);
        }

        if (!empty($params['scheme'])) {
            $query->andWhere(['p.scheme_id' => $params['scheme']]);
        }

        if (!empty($params['price_level_id'])) {
            $query->andWhere(['p.price_level_id' => $params['price_level_id']]);
        }

        if(!empty($params['ptype'])){
            $query->andWhere(['p.type_id' => $params['ptype']]);
        }
        if (!empty($params['type']) || !empty($params['name'])) {
            $query->leftJoin('meet_order as o', 'o.order_id = oi.order_id')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id');
            if (!empty($params['type'])) {

                $query->andWhere(['c.type' => $params['type']]);
            }
            if (!empty($params['name'])) {
                $query->andWhere(['like','c.name', $params['name']]);
            }
        }
        
        if (!empty($params['order'])) {
            $query->orderBy($params['order']);
        } else {
            $query->orderBy('p.serial_num asc');
        }
        if (empty($params['download'])) {
            $query->groupBy(['oi.style_sn']);
        }else{
            $query->groupBy(['oi.product_id']);
            $select = ArrayHelper::merge($select, ['p.product_sn', 'p.purchase_id']);
        }
        //获取总数量
        // $countQuery = clone $query;
        // $count = count($countQuery->select(['sum(oi.nums)as nums'])->all());
        $pagination = '';
        if (empty($params['download'])) {
            //分页  改用固定值，可以减少查询总数浪费的时间
            $pagination = new Pagination(['totalCount' => 1000, 'pageSize' => ParamsClass::$pageSize]);

            $query->offset($pagination->offset)
                ->limit($pagination->limit);
        }
        $query->select($select);
        $list = $query->all();
        /*
        
        array(15) {
          [0]=>
          array(14) {
            ["nums"]=>
            string(3) "941"
            ["amount"]=>
            string(9) "496848.00"
            ["name"]=>
            string(28) "针织拼条纹可哺乳T恤"
            ["cost_price"]=>
            string(6) "528.00"
            ["style_sn"]=>
            string(12) "173107050164"
            ["product_id"]=>
            string(1) "1"
            ["img_url"]=>
            string(24) "/images/17310705_164.jpg"
            ["serial_num"]=>
            string(1) "1"
            ["cat_b"]=>
            string(1) "1"
            ["cat_m"]=>
            string(1) "1"
            ["cat_s"]=>
            string(2) "10"
            ["size_id"]=>
            string(1) "3"
            ["type_id"]=>
            string(1) "1"
            ["order_id"]=>
            string(16) "2017031497575098"
          }
         */
        return array('item' => $list, 'pagination' => $pagination);
    }

    //根据商品查找订单数量
    public function customerOrderByProductIdCount($productIds, $params = [])
    {
        $query = new Query;
        $query->select(['oi.product_id', 'sum(oi.nums) as count', 'c.type'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->where(['in', 'oi.product_id', $productIds])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy(['oi.product_id', 'c.type']);
            // ->orderBy('oi.product_id desc');
        //判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }

        $result = $query->all();
        $productIds = [];
        foreach ($result as $key => $value) {
            $productIds[$value['product_id']][$value['type']] = $value['count'];
        }
        return $productIds;
    }
    //根据商品查找订单数量
    public function customerOrderByStyleSnCount($styleSnArr, $params = [])
    {
        $query = new Query;
        $query->select(['oi.style_sn', 'sum(oi.nums) as count', 'c.type'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->where(['in', 'oi.style_sn', $styleSnArr])
            ->andWhere(['oi.disabled' => 'false'])
            // ->indexBy('style_sn')
            ->groupBy(['oi.style_sn', 'c.type']);
        //判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }

        $result = $query->all();
        $styleSnArr = [];
        foreach ($result as $key => $value) {
            $styleSnArr[$value['style_sn']][$value['type']] = $value['count'];
        }

        return $styleSnArr;
    }
    //订单数量汇总: 订单金额汇总:
    public function getOrderAmount($product_id, $params)
    {
        $query = (new Query)
            ->from('meet_order_items  as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_order as o', 'o.order_id = oi.order_id')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->where(['oi.disabled' => 'false']);
        $select = ['sum(oi.nums) as nums', 'sum(oi.amount) as amount'];
        if (!empty($params['purchase'])) {
            $query->andWhere(['c.purchase_id' => $params['purchase']]);
            $select = ArrayHelper::merge($select, ['o.purchase_id', 'o.customer_id', 'c.`type`']);
        }else{
            $select = ArrayHelper::merge($select, ['c.`type`']);
        }
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }
    }

    /**
     * FBaseController使用+1
     * 获取用户的订单详情 
     * 添加商品订单的时候注意清缓存
     * @param  [type] $purcheaseId 订货会id
     * @param  [type] $customerId  用户id
     * @return [type]              [description]
     */
    public function orderItems($purcheaseId, $customerId)
    {   
        $cacheName = 'order-items-' . $purchaseId . '_' . $customerId;
        $model = Yii::$app->cache->get($cacheName);

        if (!$model) {
            $model = $this->orderCache();
            Yii::$app->cache->set($cacheName, $model);
        }
        //原代码有的，可以在更新状态的时候直接删除缓存就行了
        // else{
        //     //如果订单已经存在 
        //     //获取订单状态
        //     $orderRow = self::find()
        //         ->select(['status'])
        //         ->where(['purchase_id' => $purcheaseId])
        //         ->andWhere(['customer_id' => $customerId])
        //         ->asArray()
        //         ->one();

        //     if ($orderRow['status'] != $model['order_row']['status']) {
        //         $model = $this->orderCache($purcheaseId, $customerId);
        //     }
        // }
        return $model;
    }
    /**
     * 要缓存的订单信息
     * @param  [type] $purcheaseId 订购会id
     * @param  [type] $customerId  用户id
     * @return [type]              [description]
     */ 
    public function orderCache($purcheaseId, $customerId)
    {
        //查询生效的订单
        $model['order_row'] = self::find()
            ->where(['purchase_id' => $purcheaseId])//可以不添加，因为一个用户就对应了订货会类型
            ->andWhere(['customer_id' => $customerId])
            ->andWhere(['disabled' => 'false'])
            ->asArray()
            ->one();
        if (empty($model['order_row'])) {
            return ['order_row' => [], 'item_list' => []];
        }
        $itemList = (new Query)->from('meet_order_items')
            ->where(['order_id' => $model['order_row']['order_id']])
            ->andWhere(['disabled' => 'false'])
            ->all();
        $totalNum = 0;
        $costItem = 0.00;
        if (empty($itemList)) {
            //商品总数量
            $model['order_row']['total_num'] = $total_num;

            return ['order_row' => $model['order_row'], 'item_list' => []];
        }
        $isDown = $this->getProductIsDown();
        foreach ($itemList as $item) {
            $model['item_list'][$item['product_id']] = $item;
            $model['item_list'][$item['product_id']]['is_down'] = $isDown[$item['product_id']];

            $totalNum += $item['nums'];
        }
        $model['order_row']['total_num'] = $total_num;

        return $model;
    }
    /**
     * 产品是否下架
     * @return [type] [description]
     */
    public function getProductIsDown()
    {
        $purcheaseId = Yii::$app->session->get('purchase_id');
        $result = Yii::$app->cache->get("product_list_is_down_". $purcheaseId);
        if(empty($result)){
            $product = new ProductModel();
            $res = $product->productListCache();
            foreach($res as $val){
                $result[$val['product_id']] = $val['is_down'];
            }
            Yii::$app->cache->set("product_list_is_down_".Yii::app()->session['purchase_id'], $result, 86400);
        }
        return $result;
    }
    /**
     * order/order/index
     * 客户订单查询
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function orderQueryList($params)
    {
        $select = ['c.code', 'c.agent', 'c.customer_id', 'c.`name` as customer_name', 'c.`type`', 'c.purchase_id', 'c.province', 'c.area', 'c.target', 'o.order_id', 'o.`status`', 'o.cost_item', 'o.create_time', 
        '`o`.`cost_item` / `c`.`target`  as rate', 'c.parent_id', 'o.cost_item as count_all'];
        $query = (new Query)
            ->from('meet_customer as c')
            ->leftJoin('meet_order as o', 'c.customer_id = o.customer_id')
            ->where(['o.disabled' => 'false']);
        //排序条件
        if (!empty($params['order'])) {
            $orderBy = $params['order'];
        }else{
            $orderBy = 'o.cost_item';
        }
        $query->orderBy([$orderBy => SORT_DESC]);
        //订货会筛选,3为两个订货会都有的产品
        if (!empty($params['purchase'])) {
            $query->andWhere(['in', 'c.purchase_id', [$params['purchase'], 3]]);
        }
        // 部门类型
        if (!empty($params['department'])) {
            $query->andWhere(['c.department' => $params['department']]);
        }
        // 订单状态
        if(!empty($params['status'])){
            $query->andWhere(['o.status' => $params['status']]);
        }
        // 负责人
        if (!empty($params['leader'])) {
            $query->andWhere(['c.leader' => $params['leader']]);
        }
        // 客户名称
        if (!empty($params['name'])) {
            $query->andWhere(['like', 'c.name', $params['name']]);
        }
        // 负责人(代理)名字/代码
        if (!empty($params['leader_name'])) {
            $query->andWhere(['or', ['like', 'c.agent', $params['leader_name']], ['like', 'c.leader_name', $params['leader_name']]]);
        }
        // 客户代码
        if(!empty($params['code'])){
            $query->andWhere(['c.code' => $params['code']]);
        }
        // 判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }
        // 大区
        if (!empty($params['area'])) {
            $query->andWhere(['c.area' => $params['area']]);
        }
        // 用户是否登陆过
        if (!empty($params['login'])) {
            if ($params['login'] == 1) {
                $query->andWhere(['c.login' => 'not null']);
            } elseif ($params['login'] == 2) {
                $query->andWhere(['c.login' => 'null']);
            }
        }
        $countQuery = clone $query;
        $count = $countQuery->count();

// 统计订单总定额
$countMoneyQuery = clone $query;
$queryAll = $countMoneyQuery->select(['o.order_id'])->all();
$countMoney = 0;
foreach ($queryAll as $k => $item) {
    //获取订单的价格
    $countMoney += $this->getCustomerNewCount($item['order_id'])['oldprice'];
}
// 统计已经finish审核的订单总定额
$countFinishQuery = clone $query;
$queryAll = $countFinishQuery->select(['o.order_id'])->andWhere(['o.status' => 'finish'])->all();
$finishMoney = 0;
foreach ($queryAll as $k => $item) {
    //获取订单的价格
    $finishMoney += $this->getCustomerNewCount($item['order_id'])['oldprice'];
}

        $pagination = '';
        if (empty($params['download'])) {
            //分页
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => ParamsClass::$pageSize]);

            $query->offset($pagination->offset)
                ->limit($pagination->limit);
        }
        $result = $query->select($select)->all();
        //判断下订单的价格是够改动
        foreach ($result as $k => $item) {
            //获取订单的价格
            $price = $this->getCustomerNewCount($item['order_id']);
            // 已订货金额
            $result[$k]['cost_item'] = $price['oldprice'];
            $result[$k]['is_diff'] = false;
            if ($price['newprice'] != $price['oldprice']) {
                $result[$k]['is_diff'] = true;
            }
        }
        return ['list'=>$result, 'pagination'=>$pagination, 'amount'=>$countMoney, 'amount_really'=>$finishMoney];
    }

    /**
     * 使用此方法的方法
     * orderModel/orderQueryList
     *
     * 
     * 获取订单的价格(最新和下订单时的价格) 
     * @param  [type]  $order_id 订单id
     * @return [type]            [description]
     */
    public function getCustomerNewCount($order_id){

        $result = (new Query)
            ->select(['sum(oi.nums*p.cost_price) as newprice', 'sum(amount) as oldprice'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->where(['oi.order_id' => $order_id])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy('oi.order_id')
            ->orderBy('oi.model_sn ASC')
            ->all();
        return $result;
    }
}
