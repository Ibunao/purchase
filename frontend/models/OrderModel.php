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
            ->andWhere(['<>', 'oi.order_id', '2017080957101504'])//暂时过滤掉电商A
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
            $query->groupBy(['oi.style_sn', 'p.size_id']);
            $select = ArrayHelper::merge($select, ['p.product_sn', 'p.purchase_id', 'p.season_id', 'p.wave_id', 'p.brand_id', 'p.style_sn', 'p.model_sn', 'p.color_id', 'p.cost_price']);
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
        $query->select(['oi.style_sn', 'p.size_id', 'sum(oi.nums) as count', 'c.type'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->where(['in', 'oi.style_sn', $styleSnArr])
            ->andWhere(['oi.disabled' => 'false'])
            ->andWhere(['<>', 'oi.order_id', '2017080957101504'])//暂时过滤掉电商A
            ->groupBy(['oi.style_sn', 'p.size_id', 'c.type']);
            // ->indexBy('style_sn')
        //判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }

        $result = $query->all();
        $styleSnArr = [];
        foreach ($result as $key => $value) {
            $styleSnArr[$value['style_sn']][$value['size_id']][$value['type']] = $value['count'];
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
    public function orderItems($purchaseId, $customerId)
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
            ->leftJoin('meet_order as o', 'c.customer_id = o.customer_id');
            
        //排序条件
        if (!empty($params['order'])) {
            $orderBy = $params['order'];
        }else{
            $orderBy = 'o.cost_item';
        }
        
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
        // 用户是否登陆过,没有登陆过的就不用过滤 o.disabled 因为为null
        if (!empty($params['login'])) {
            if ($params['login'] == 1) {
                $query->andWhere(['not', ['c.login' => null]])->andWhere(['o.disabled' => 'false']);
            } elseif ($params['login'] == 2) {
                $query->andWhere(['c.login' => null]);
            }
        }else{
            $query->andWhere(['o.disabled' => 'false']);
        }
        $countQuery = clone $query;
        $count = $countQuery->count();

// 统计订单总定额
$countMoneyQuery = clone $query;
$orderPrice = $countMoneyQuery
            ->select(['sum(oi.nums*p.cost_price) as newprice', 'sum(amount) as oldprice'])
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy('oi.order_id')
            ->orderBy('oi.model_sn ASC')
            ->all();
$countMoney = 0;
foreach ($orderPrice as $key => $order) {
    $countMoney += $order['oldprice'];
}

// 统计已经finish审核的订单总定额
$countFinishQuery = clone $query;
$queryAll = $countFinishQuery
            ->select(['sum(oi.nums*p.cost_price) as newprice', 'sum(amount) as oldprice'])
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->andWhere(['oi.disabled' => 'false'])
            ->andWhere(['o.status' => 'finish'])
            ->groupBy('oi.order_id')
            ->orderBy('oi.model_sn ASC')
            ->all();
$finishMoney = 0;
foreach ($queryAll as $key => $order) {
    $finishMoney += $order['oldprice'];
}

        $pagination = '';
        if (empty($params['download'])) {
            //分页
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => ParamsClass::$pageSize]);

            $query->offset($pagination->offset)
                ->limit($pagination->limit);
        }
        $query->orderBy([$orderBy => SORT_DESC]);
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
     * order/order/detail
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
            ->one();
        return $result;
    }
    /**
     * 使用的方法
     * order/order/index
     * 
     * 获取该用户的下线客户的预订金额
     * @param string $code
     * @return int
     */
    public function getAllPriceCount($parentId, $agent = '')
    {
        $s = 0;
        if (!empty($agent)) {
            if ($parentId == 1) {
                $query = new Query;
                $result = $query->select(['o.order_id'])
                    ->from('meet_order as o')
                    ->leftJoin('meet_customer as c', 'o.customer_id = c.customer_id')
                    ->where(['c.agent' => $agent])
                    ->andWhere(['c.parent_id' => 0])
                    ->all();
                foreach ($result as $v) {
                    $s += $this->getCustomerNewCount($v['order_id'])['oldprice'];
                }
            }
        }
        return $s;
    }

    /**
     * 使用的方法  
     * order/order/index
     * 
     * 获取订单审核的信息
     * @param  [type] $orderId 订单id
     * @return [type]          [description]
     */
    public function  getOrderLog($orderId)
    {
        $result = (new Query)->from('meet_order_log')
            ->where(['order_id' => $orderId])
            ->orderBy(['time' => SORT_DESC])
            ->one();
        
        return $result;
    }

    /**
     * 使用的方法
     * order/order/docopy
     * 
     * 获取客户订单
     * @param  [type] $customerId 客户id
     * @return [type]             [description]
     */
    public function  getCustomerOrder($customerId){
        return $result = self::find()->where(['customer_id' => $customerId])->asArray()->one();
    }

    public function orderItem($orderId)
    {
        $query = new Query;
        $result = $query->select(['oi.*', 'p.cat_b', 'p.cat_s', 'p.season_id as season', 'p.cost_price'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'oi.product_id = p.product_id')
            ->where(['order_id' => $orderId])
            ->andWhere(['oi.disabled' => 'false'])
            ->all();
        //判断是否使用最新的价格
        if(Yii::$app->params['is_latest_price']){
            foreach($result as $key => $val){
                $result[$key]['amount'] = $val['cost_price'] * $val['nums'];
            }
        }else{
            foreach($result as $key => $val){
                $result[$key]['price'] = $val['cost_price'];
            }
        }
        return $result;
    }

    /**
     * 使用的方法  
     *order/order/docopy
     * 
     * 添加订单
     * @param [type] $purchaseId   from订货会id
     * @param [type] $customerId   to用户id
     * @param [type] $customerName to用户名
     * @param [type] $costItem     from订单总额
     */
    public function addOrder($purchaseId, $customerId, $customerName, $costItem)
    {
        $createTime = time();

        $orderRow = self::find()->where(['purchase_id' => $purchaseId])
            ->andWhere(['customer_id' => $customerId])
            ->one();

        //检查是否已经生成过订单
        if (!empty($orderRow)) {
            //订单存在则更新
            $orderId = $orderRow->order_id;
            $orderRow->edit_time = $createTime;
            $orderRow->cost_item = $costItem;
            if ($orderRow->save()) {
                return $orderId;
            }
        } else {
            $orderId = $this->buildOrderNo();

            $this->order_id = $orderId;
            $this->purchase_id = $purchaseId;
            $this->status = 'active';
            $this->customer_id = $customerId;
            $this->customer_name = $customerName;
            $this->cost_item = $costItem;
            $this->create_time = $createTime;
            if ($this->save()) {
                return $orderId;
            }
        }
        return false;
    }

    /**
     *  使用方法
     *  order/order/docopy
     * 
     * 添加订单详情
     * @param [type] $orderId   订单id
     * @param [type] $orderList 订单item
     */
    public function addToOrderItem($orderId, $orderList)
    {
        $itmes = [];
        foreach ($orderList as $k => $v) {
            $item = [];
            $item[] = $orderId;
            $item[] = $v['product_id'];
            $item[] = $v['product_sn'];
            $item[] = $v['style_sn'];
            $item[] = $v['model_sn'];
            $item[] = $v['name'];
            $item[] = $v['price'];
            $item[] = $v['amount'];
            $item[] = $v['nums'];

            $items[] = $item;
        }

        $result = Yii::$app->db
            ->createCommand()
            ->batchInsert('meet_order_items',
            ['order_id', 'product_id', 'product_sn', 'style_sn', 'model_sn', 'name', 'price', 'amount', 'nums'], $items)
        ->execute();

        
        return $result;
    }

    /**
     * 生成订单号
     * @return [type] [description]
     */
    public function buildOrderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    /**
     *  使用方法
     *  order/order/detail
     * 
     * 根据订单号获取订单中商品的款号
     * @param  [type] $order_id 订单id
     * @return [type]           [description]
     */
    public function orderProductModelSn($orderId)
    {

        if (empty($orderId)) {
            return [];
        }
        $result = (new Query)->select(['model_sn'])
                ->from('meet_order_items')
                ->where(['order_id' => $order_id])
                ->andWhere(['disabled' => 'false'])
                ->groupBy(['model_sn'])
                ->all();
        return $result;
    }
    /**
     *  使用方法
     *  order/order/detail
     * 
     * 订单中商品详情
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function orderInfo($orderId)
    {
        $select = ['c.customer_id', 'c.code', 'c.name as customer_name', 'c.type', 'c.province', 'c.area', 'c.target', 'o.order_id', 'o.status', 'o.cost_item', 'o.create_time', 'mp.purchase_name', 'c.purchase_id', 'c.big_1', 'c.big_2', 'c.big_3', 'c.big_4', 'c.big_6', 'c.big_1_count', 'c.big_2_count', 'c.big_3_count', 'c.big_4_count', 'c.big_6_count'];
        $result = (new Query)->select($select)
            ->from('meet_customer as c')
            ->leftJoin('meet_order as o', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_purchase as mp', 'mp.purchase_id = o.purchase_id')
            ->where(['o.disabled' => 'false'])
            ->andWhere(['order_id' => $orderId])
            ->orderBy(['cost_item'=>SORT_DESC])
            ->all();
        //获取总数量和总钱数
        $query = (new Query)->select(['sum(nums) as nums', 'sum(amount) as finally'])
            ->from('meet_order_items')
            ->where(['order_id' => $orderId])
            ->andWhere(['disabled' => 'false'])
            ->one();
        $result['nums'] = $query['nums'];
        $result['cost_item'] = $query['finally'];
        return $result;
    }

    public function orderItemList($orderId)
    {
        $select = ['oi.*', 'p.cat_b', 'p.cat_s', 's.size_name', 'c.color_name', 'p.cost_price'];
        $result = (new Query)->select($select)
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_size as s', 'p.size_id = s.size_id')
            ->leftJoin('meet_color as c', 'p.color_id = c.color_id')
            ->where(['order_id' => $order_id])
            ->andWhere(['oi.disabled' => 'false'])
            ->orderBy(['model_sn' => SORT_DESC])
            ->all();
        return $result;
    }
}
