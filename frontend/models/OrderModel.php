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
     * use
     * FBaseController
     * this->addAjax
     * 
     * 获取用户的订单详情 
     * 添加商品订单的时候注意清缓存
     * @param  [type] $purchaseId 订货会id
     * @param  [type] $customerId  用户id
     * @return [type]              [description]
     */
    public function orderItems($purchaseId, $customerId)
    {   
        // $cacheName = 'order-items-' . $purchaseId . '_' . $customerId;
        // $model = Yii::$app->cache->get($cacheName);

        // if (!$model) {
            $model = $this->orderCache($purchaseId, $customerId);
            // Yii::$app->cache->set($cacheName, $model);
        // }
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
     * use
     * this/orderItems
     * this/AddAjax
     * 
     * 要缓存的订单信息     没缓存
     * @param  [type] $purchaseId 订购会id
     * @param  [type] $customerId  用户id
     * @return [type]              [description]
     */ 
    public function orderCache($purchaseId, $customerId)
    {
        //查询生效的订单
        $model['order_row'] = self::find()
            ->where(['purchase_id' => $purchaseId])//可以不添加，因为一个用户就对应了订货会类型
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
            $model['order_row']['total_num'] = $totalNum;

            return ['order_row' => $model['order_row'], 'item_list' => []];
        }
        $isDown = $this->getProductIsDown();
        foreach ($itemList as $item) {
            $model['item_list'][$item['product_id']] = $item;
            $model['item_list'][$item['product_id']]['is_down'] = $isDown[$item['product_id']];

            $totalNum += $item['nums'];
        }
        $model['order_row']['total_num'] = $totalNum;

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
            Yii::$app->cache->set("product_list_is_down_".Yii::$app->session['purchase_id'], $result, 86400);
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
     * $this/getMasterCount
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
                ->where(['order_id' => $orderId])
                ->andWhere(['disabled' => 'false'])
                ->groupBy(['model_sn'])
                ->all();
        return $result;
    }
    /**
     *  使用方法
     *  order/order/detail
     *  order/order
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
            ->one();
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

    public function DownloadOrderItemList($orderId)
    {
        $select = ['oi.*', 'p.cat_b', 'p.cat_s', 's.size_id', 's.size_no', 's.size_name', 'ms.scheme_id', 'ms.scheme_name', 'p.color_id', 'c.color_no', 'c.color_name', 'cb.big_id', 'cb.cat_name as big_name', 
        'cm.middle_id', 'cm.cat_name as middle_name', 'sm.small_id', 'sm.cat_name as small_name', 'cs.season_id', 'cs.season_name', 'p.level_id', 'l.level_name', 'p.memo', 'b.brand_name', 'b.brand_id', 'g.wave_name', 'g.wave_id', 'p.cost_price'];
        $result = (new Query)->select($select)
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_size as s', 'p.size_id = s.size_id')
            ->leftJoin('meet_color as c', 'c.color_id = p.color_id')
            ->leftJoin('meet_cat_big as cb', 'cb.big_id = p.cat_b')
            ->leftJoin('meet_cat_middle as cm', 'cm.middle_id = p.cat_m')
            ->leftJoin('meet_cat_small as sm', 'sm.small_id = p.cat_s')
            ->leftJoin('meet_scheme as ms', 'ms.scheme_id = p.scheme_id')
            ->leftJoin('meet_season as cs', 'cs.season_id = p.season_id')
            ->leftJoin('meet_level as l', 'l.level_id = p.level_id')
            ->leftJoin('meet_brand as b', 'b.brand_id = p.brand_id')
            ->leftJoin('meet_wave as g', 'g.wave_id = p.wave_id')
            ->where(['oi.order_id' => $orderId])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy(['oi.product_sn'])
            ->orderBy(['oi.model_sn' => SORT_ASC])
            ->all();
        if (empty($result)) {
            return [];
        }
        //最新价格的商品总价
        //如果更新商品价格后刷新订单价格则不需要这一步
        foreach ($result as $key => $value) {
            $result[$key]['amount'] = $value['cost_price'] * $value['nums'];
        }
        return $result;
    }
    /**
     * 用法
     * order/order/detail
     * 
     * 商品订单的详细信息
     * @param  [type] $orderId 订单id
     * @return [type]          [description]
     */
    public function orderItemList($orderId)
    {
        $select = ['oi.*', 'p.cat_b', 'p.cat_s', 's.size_name', 'c.color_name', 'p.cost_price'];
        $result = (new Query)->select($select)
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_size as s', 'p.size_id = s.size_id')
            ->leftJoin('meet_color as c', 'p.color_id = c.color_id')
            ->where(['order_id' => $orderId])
            ->andWhere(['oi.disabled' => 'false'])
            ->orderBy(['model_sn' => SORT_DESC])
            ->all();
        return $result;
    }

    /**
     * 用法
     * order/order/check
     * 
     * 审核，更新订单状态
     * @param  [type] $orderId 订单号
     * @param  [type] $status  状态
     * @return [type]          [description]
     */
    public function updateOrderStatus($orderId, $status)
    {
        //完成，作废，确认，正常
        $statusArr = ['finish', 'dead', 'confirm', 'active'];
        if (in_array($status, $statusArr)) {
            $item = self::find()
                ->where(['order_id' => $orderId])
                ->one();
            $item->status = $status;
            if ($item->save()) {
                return true;
            }
        }
        return false;
    }
    /**
     * use
     * order/order/ExportMaster
     * 
     * 该用户的所有线下的客户的预定金额
     * @param  [type] $data 客户code拼接字符串
     * @return [type]       [description]
     */
    public function getMasterCount($data)
    {
        $codes = explode(",", $data);
        $result = 0;
        $rest = (new Query)->select(['parent_id', 'agent', 'code'])
            ->from('meet_customer')
            ->where(['in', 'code', $codes])
            ->indexBy('code')
            ->all();
        $agentArr = [];
        foreach ($rest as $code => $value) {
            if ($value['parent_id'] == 1) {
                $agentArr[] = $value['agent'];
            }
        }
        $orders = (new Query)->select(['o.order_id'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'o.customer_id = c.customer_id')
            ->where(['in', 'c.agent', $agentArr])
            ->andWhere(['c.parent_id' => 0])
            ->all();
        $orderArr = [];
        foreach ($orders as $order) {
            $orderArr[] = $order['order_id'];
        }
        $priceResult = (new Query)
            ->select(['sum(oi.nums*p.cost_price) as newprice'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->where(['in', 'oi.order_id' , $orderArr])
            ->andWhere(['oi.disabled' => 'false'])
            ->one();
        $result = $priceResult['newprice'];
        return $result;
    }

    /**
     * 获取所有客户此大类的折扣信息
     * @param  [type] $type_id [description]
     * @return [type]          [description]
     */
    public function getAllCustomerDiscount($type_id)
    {
        $btc = "c.big_{$type_id}_count" . ' AS discount';
        $bt = "c.big_{$type_id} AS starget";
        $select = ['o.order_id', $btc, 'c.name', 'c.purchase_id', 'c.code', $bt];
        $result = (new Query)->select($select)
            ->from('meet_order AS o')
            ->leftJoin('meet_customer AS c', 'c.customer_id=o.customer_id')
            ->where(['o.disabled' => 'false'])
            ->all();
        
        foreach($result as $val){
            $val['amount'] = $this->getThisCustomerCatBigBroughtInfo($type_id,$val['order_id']);
            $val['final_amount'] = $val['amount'] * $val['discount'] /100;
            if(empty($val['starget'])){
                $val['percent'] = 0 .'%';
            }else{
                $val['percent'] = round( $val['final_amount'] / $val['starget'] * 100, 2 ).'%';
            }
            $res[] = $val;
        }
        if(empty($res)) return [];
        return $res;
    }
    /**
     * 获取此大类的商品的购买价格之和
     *
     * @param $type_id
     * @param $order_id
     * @return int
     */
    public function getThisCustomerCatBigBroughtInfo($type_id, $order_id){
        $res = (new Query)->select(['p.cost_price', 'i.nums', 'i.amount'])
            ->from('meet_order as o')
            ->leftJoin('meet_order_items AS i', 'i.order_id=o.order_id')
            ->leftJoin('meet_customer AS c', 'c.customer_id=o.customer_id')
            ->leftJoin('meet_product AS p', 'p.product_id=i.product_id')
            ->where(['p.cat_b' => $type_id])
            ->andWhere(['o.order_id' => $order_id])
            ->andWhere(['i.disabled' => 'false'])
            ->all();
        $final = 0;
        foreach($res as $val){
            $final += $val['cost_price']*$val['nums'];
        }
        return $final;
    }

    /**
     * 获取所有用户的折扣信息
     * @return [type] [description]
     */
    public function getAllUserOrderItems()
    {
        $result = (new Query)->select(['o.order_id', 'c.customer_id', 'c.big_1_count', 'c.big_2_count', 'c.big_3_count', 'c.big_4_count', 'c.big_6_count'])
            ->from('meet_order AS o')
            ->leftJoin('meet_customer  AS c', 'c.customer_id=o.customer_id')
            ->where(['c.disabled' => 'false'])
            ->andWhere(['o.disabled' => 'false'])
            ->all();

        $item = [];
        foreach($result as $val){
            $item[$val['order_id']] = $val;
        }
        return $item;
    }

    /**
     * 根据款号获取其大类(大类小类根据款号修改)
     *
     * @return array
     */
    public function getProductModelSnAndCatBig(){
        $sql = "SELECT model_sn,cat_b FROM `meet_product` WHERE disabled='false'";
        $result = (new Query)->select(['model_sn', 'cat_b'])
            ->from('meet_product')
            ->where(['disabled' => 'false'])
            ->all();
        $res = [];
        foreach($result as $val){
            $res[$val['model_sn']] = $val['cat_b'];
        }
        return $res;
    }

    public function getAllOrderItemsList(){
        // $select = ['i.model_sn', 'i.nums', 'i.order_id', 's.size_no', 'c.code', 'p.cost_price', 'color.color_no'];
        $select = ['i.model_sn', 'i.nums', 'i.order_id', 'o.customer_id', 'i.product_id'];
        $allQuery = (new Query)->select($select)
            ->from('meet_order AS o')
            ->leftJoin('meet_order_items as i', 'o.order_id=i.order_id')
            ->where(['i.disabled' => 'false'])
            ->all();
        $productQuery = (new Query)->select(['product_id', 'size_id', 'cost_price', 'color_id'])
            ->from('meet_product')
            ->indexBy('product_id')
            ->all();
        $customerQuery = (new Query)->select(['customer_id', 'code'])
            ->from('meet_customer')
            ->indexBy('customer_id')
            ->all();
        $sizeQuery = (new Query)->select(['size_id', 'size_no'])
            ->from('meet_size')
            ->indexBy('size_id')
            ->all();
        $colorQuery = (new Query)->select(['color_id', 'color_no'])
            ->from('meet_color')
            ->indexBy('color_id')
            ->all();
        $result = [];
        foreach ($allQuery as $val) {
            $arr = [];
            $arr['model_sn'] = $val['model_sn'];
            $arr['nums'] = $val['nums'];
            $arr['order_id'] = $val['order_id'];
            $arr['size_no'] = $sizeQuery[$productQuery[$val['product_id']]['size_id']]['size_no'];
            try {
                
                $arr['code'] = $customerQuery[$val['customer_id']]['code'];
            } catch (\Exception $e) {
                var_dump($val['customer_id']);
            }
            $arr['cost_price'] = $productQuery[$val['product_id']]['cost_price'];
            $arr['color_no'] = $colorQuery[$productQuery[$val['product_id']]['color_id']]['color_no'];
            $result[] = $arr;
        }
        return $result;
    }

    /**
     * use
     * forder/actionGetAllPrice
     *
     * 前台添加商品
     */
    public function addAjax($product, $purchase_id, $customer_id, $customer_name)
    {
        $create_time = time();
        //如果该会员已经生成订单，在原来的订单上添加订购的商品
        //会员订单是否生成
        $order_list = $this->orderItems($purchase_id, $customer_id);//已经订购的产品
        // var_dump($order_list);exit;
        //订单order_id获取
        if ($order_list['order_row']) {
            $order_id = $order_list['order_row']['order_id'];
            //更新订单时间
            $orderModel = OrderModel::find()
                ->where(['order_id' => $order_list['order_row']['order_id']])
                ->one();
            $orderModel->edit_time = $create_time;
            $result = $orderModel->save();
        }else {
            //创建订单
            $order_id = $this->buildOrderNo();

            $orderModel = new OrderModel;
            $orderModel->order_id = $order_id;
            $orderModel->purchase_id = $purchase_id;
            $orderModel->status = 'active';
            $orderModel->customer_id = $customer_id;
            $orderModel->customer_name = $customer_name;
            $orderModel->cost_item = 0;
            $orderModel->create_time = $create_time;
            $result = $orderModel->save();
            
        }
        if (empty($result)) {
            return false;
        }
        //订单商品列表
        $result = $this->itemListAjax($product, $order_id, $order_list['item_list']);
        return $result;
        //更新订单缓存
        // $this->orderCache($purchase_id, $customer_id);
    
    }
    /**
     * use
     * this->addAjax
     *
     * 根据用户提交的预定商品来进行更新订单详情状态
     *
     * 待解决，订单的总额信息不准
     * @param  [type] $product   预定提交的产品
     * @param  [type] $order_id  订单id
     * @param  [type] $item_list 订单已经存在的产品
     * @return [type]            [description]
     */
    public function itemListAjax($product, $order_id, $item_list)
    {
        $productModel = new ProductModel();
        //商品的id
        $product_list = $productModel->listcacheId();
        $sql = '';
        $order_product_list = isset($item_list) && $item_list ? $item_list : array();//已定单 数量等
// var_dump($order_product_list);exit;
        foreach ($product as $k => $v) {
            $num = isset($v[1]) ? $v[1] : '';
            if ($num == 0) {
                $num = '';
            }
            $res = $productModel->checkThisProductIsDown($v[0]);
            //产品下架
            if(empty($res)){
                //下架的产品在再次添加的时候如果检测到下架，将已经添加到购物车中的产品改为删除状态
                $where = "order_id = " . $order_id . " AND product_id = '" . $v[0] . "' AND disabled='false'";
                $delete_data_arr = OrderItemsModel::updateAll(['disabled' => 'true'], $where);
                continue;
            }
            //如果用户将添加到商品订单的商品调到0了
            if ($num == '') {
                if (isset($product_list[$v[0]]) && $product_list[$v[0]]){
                    $where = "order_id = " . $order_id . " AND product_id = '" . $v[0] . "' AND disabled='false'";
                    $delete_data_arr = OrderItemsModel::updateAll(['disabled' => 'true'], $where);
                }
                continue;
            }
// var_dump($product_list);exit;
            $product_sn = $product_list[$v[0]]['product_sn'];
            $style_sn = $product_list[$v[0]]['style_sn'];
            $model_sn = $product_list[$v[0]]['model_sn'];
            $name = $product_list[$v[0]]['name'];
            $price = $product_list[$v[0]]['cost_price'];
            $amount = sprintf('%.2f', $price * $num);
            //订单已经存在，更新 同时更新价格
            if (isset($order_product_list[$v[0]]) && $order_product_list[$v[0]]) {
                $where = 'order_id = ' . $order_id . ' AND product_id = ' . $v[0]. " AND disabled='false'";
                $update = [
                    'price' => $price,
                    'amount' => $amount,
                    'nums' => $num,
                ];
                $result = OrderItemsModel::updateAll($update, $where);
                // if (!$result) {
                //     return false;
                // }
            } else {
                //插入
                $insert_data_arr[] = [$order_id, $v[0], $product_sn, $style_sn, $model_sn, $name, $price, $amount, $num];
            }
        }
        //新增商品
        if (isset($insert_data_arr) && $insert_data_arr){
            $keys = ['order_id', 'product_id', 'product_sn', 'style_sn', 'model_sn', 'name', 'price', 'amount', 'nums'];
        
            $result = Yii::$app->db
            ->createCommand()
            ->batchInsert(OrderItemsModel::tableName(),
                $keys,
                $insert_data_arr)
            ->execute();
            if (!$result) {
                return false;
            }
        }
    return true;
    }

    /**
     * use
     * forder/GetAllPrice
     * 
     * 获取该用户的订货总量、金额、完成率、目标
     * @param $customer_id
     * @return mixed
     */
    public function getCustomerOrdered($customer_id)
    {
        $res = (new Query)->select(['SUM(i.nums) as nums', 'sum(i.amount) as cost_item'])
            ->from('meet_order as o')
            ->leftJoin('meet_order_items as i', 'i.order_id = o.order_id')
            ->where(['o.customer_id' => $customer_id])
            ->andWhere(['i.disabled' => 'false'])
            ->one();
        $target = (new Query)->select(['target'])
            ->from('meet_customer')
            ->where(['customer_id' => $customer_id])
            ->one();
        $result['nums'] = isset($res['nums']) ? $res['nums'] : 0;
        $result['cost_item'] = isset($res['cost_item']) ? $res['cost_item'] : 0;
        $result['target'] = isset($target['target']) ? $target['target'] : 0;
        if ($result['target'] == 0) {
            $result['percent'] = "0";
        } else {
            $result['percent'] = (string)round($result['cost_item'] / $result['target'] * 100, 2);
        }
        return $result;
    }

    /**
     * use
     * forder/Bydownuser
     *
     * 
     * 获取下线订单ID
     *
     * @param $customer_id
     * @return bool|mixed
     */
    public function getUserDownUsers($customer_id)
    {
        $customer_info = (new Query)->select(['code'])
            ->from('meet_customer')
            ->where(['customer_id' => $customer_id])
            ->one();
        if(!$customer_info) return false;
        $result = (new Query)->select(['c.name', 'c.code', 'c.target', 'o.cost_item', 'c.customer_id', 'c.purchase_id'])
            ->from('meet_customer as c')
            ->leftJoin('meet_order as o', 'c.customer_id=o.customer_id')
            ->andWhere(['c.parent_id' => 0])
            ->andWhere(['c.agent' => $customer_info['code']])
            ->andWhere(['o.disabled' => 'false'])
            ->andWhere(['c.disabled' => 'false'])
            ->all();
        if(!$result){
            return [];
        }else{
            foreach($result as &$v)
            {
                $v['percent'] = $v['target'] == 0 ?  '0%'  : round($v['cost_item'] / $v['target'] * 100 , 2). '%';
                $v['left_cost'] = $v['target'] > $v['cost_item'] ? $v['target'] - $v['cost_item'] : 0;
            }
            return $result;
        }
    }
    /**
     * use
     * forder/submit
     * 订单提交
     *
     * @param $purchase_id
     * @param $customer_id
     * @return mixed
     */
    public function orderSubmit($purchase_id, $customer_id)
    {
        $where = 'purchase_id = ' .$purchase_id . ' AND customer_id = ' .$customer_id;
        $result = self::updateAll(['status' => 'confirm'], $where);
        
        //更新订单缓存
        // $this->orderCache($purchase_id, $customer_id);

        return $result;
    }
    /**
     * 订单撤销
     *
     * @param $purchase_id
     * @param $customer_id
     * @return mixed
     */
    public function orderRepealCheck($purchase_id, $customer_id)
    {
        $order_row = self::find()->select(['status'])
            ->where(['purchase_id' => $purchase_id])
            ->andWhere(['customer_id' => $customer_id])
            ->asArray()
            ->one();

        if ($order_row['status'] != 'confirm') return false;

        return true;
    }
    /**
     * 订单撤销
     *
     * @param $purchase_id
     * @param $customer_id
     * @return mixed
     */
    public function orderRepeal($purchase_id, $customer_id)
    {
        $where = 'purchase_id = ' .$purchase_id . ' AND customer_id = ' .$customer_id;
        $result = self::updateAll(['status' => 'active'], $where);
        //更新订单缓存
        // $this->orderCache($purchase_id, $customer_id);

        return $result;
    }
    /**
     * 商品列表
     *
     * @param $purchase_id
     * @param $customer_id
     * @param $page
     * @internal $page
     * @return array
     */
    public function fOrderItemList($purchase_id, $customer_id, $page)
    {
        $model['order_row'] = self::find()->select(['order_id'])->where(['purchase_id' => $purchase_id])
            ->andWhere(['customer_id' => $customer_id])
            ->asArray()
            ->one();
        if (!isset($model['order_row']) || !$model['order_row']) return array('order_row' => array(), 'item_list' => array());
        $model_sn = $this->orderItemItem($model['order_row']['order_id'], $page);

        if (!$model_sn) return array('order_row' => $model['order_row'], 'item_list' => array());

        $item_list =  (new Query)
            ->from('meet_order_items')
            ->where(['disabled' => 'false'])
            ->andWhere(['order_id' => $model['order_row']['order_id']])
            ->andWhere(['in', 'model_sn', $model_sn])
            ->orderBy([
                        'model_sn' => SORT_ASC,
                    ])
            ->all();

        $res = (new ProductModel)->listcacheAllId();
        foreach ($item_list as $v) {
            $model['item_list'][$v['product_id']] = $v;
            $model['item_list'][$v['product_id']]['is_down'] = isset($res[$v['product_id']]['is_down']) ? $res[$v['product_id']]['is_down'] : 0;
            $model['item_list'][$v['product_id']]['type_id'] = isset($res[$v['product_id']]['type_id']) ? $res[$v['product_id']]['type_id'] : '';
            $model['item_list'][$v['product_id']]['wave_id'] = isset($res[$v['product_id']]['wave_id']) ? $res[$v['product_id']]['wave_id'] : '' ;
            $model['item_list'][$v['product_id']]['img_url'] = isset($res[$v['product_id']]['img_url']) ? $res[$v['product_id']]['img_url'] : '';
            $model['item_list'][$v['product_id']]['name'] = isset($res[$v['product_id']]['name']) ? $res[$v['product_id']]['name'] : '';
            $model['item_list'][$v['product_id']]['memo'] = isset($res[$v['product_id']]['memo']) ? $res[$v['product_id']]['memo'] : '';
            $model['item_list'][$v['product_id']]['product_sn'] = isset($res[$v['product_id']]['product_sn']) ? $res[$v['product_id']]['product_sn'] : '';
            $model['item_list'][$v['product_id']]['product_id'] = isset($res[$v['product_id']]['product_id']) ? $res[$v['product_id']]['product_id'] : '';
            $model['item_list'][$v['product_id']]['cost_price'] = isset($res[$v['product_id']]['cost_price']) ? $res[$v['product_id']]['cost_price'] : '';
            $model['item_list'][$v['product_id']]['serial_num'] = isset($res[$v['product_id']]['serial_num']) ? $res[$v['product_id']]['serial_num'] : '';
        }
        return $model;
    }
    /**
     * 商品列表
     *
     * @param $order_id
     * @param $page
     * @internal param $purchase_id
     * @internal param $customer_id
     * @return array
     */
    public function orderItemItem($order_id, $page)
    {
        $start = ($page - 1) * 8;
        $item_list = (new Query)->select(['model_sn'])
            ->from('meet_order_items')
            ->where(['disabled' => 'false'])
            ->andWhere(['order_id' => $order_id])
            ->orderBy(['item_id' => SORT_DESC])
            ->all();

        $model_sn_items = array();
        foreach ($item_list as $v) {
            $model_sn_items[$v['model_sn']] = $v['model_sn'];
        }
        $model_sn = array_keys($model_sn_items);
        $model_sn = array_slice($model_sn, $start, 8);
        return $model_sn;
    }
  
}
