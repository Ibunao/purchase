<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
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
    //根据条件进行搜索
    public function orderList($pagesize, $params)
    {
        $query = (new Query())->from('meet_order_items as oi')
            ->where(['oi.disabled' => 'false'])
            ->leftJoin('meet_order as o', 'o.order_id = oi.order_id')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->leftJoin('meet_type as tp', 'p.type_id = tp.type_id');
        $select = ['sum(oi.nums)as nums', 'sum(oi.amount) as amount', 'p.name', 'p.cost_price', 'p.style_sn', 'p.product_id', 'p.img_url', 'p.serial_num', 'p.cat_b', 'p.cat_m', 'p.cat_s', 's.size_name', 'tp.type_name', 'oi.order_id', 'c.type'];

        if (!empty($params['purchase'])) {
            $query->andWhere(['c.purchase_id' => $params['purchase']]);
            $select = ArrayHelper::merge($select, ['o.purchase_id', 'o.customer_id', 'c.type']);
        }
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }
        if (!empty($params['style_sn'])) {
            $query->andWhere(['p.style_sn' => $params['style_sn']]);
        }
        if (!empty($params['cat_big'])) {
            $query->andWhere(['p.cat_b' => $params['cat_big']]);
            //     ->leftJoin('meet_cat_big as cb', 'cb.big_id = p.cat_b');
            // $select = ArrayHelper::merge($select, ['cb.cat_name as cat_big_name']);
        }
        if (!empty($params['cat_middle'])) {
            $query->andWhere(['p.cat_m' => $params['cat_middle']]);
            //     ->leftJoin('meet_cat_middle as cm', 'cm.middle_id = p.cat_m');
            // $select = ArrayHelper::merge($select, ['cm.cat_name as cat_middle_name']);
        }
        if (!empty($params['cat_small'])) {
            $query->andWhere(['p.cat_s' => $params['cat_small']]);
            //     ->leftJoin(['meet_cat_big_small as cs', 'cs.small_id = p.cat_s']);
            // $select = ArrayHelper::merge($select, ['cs.small_cat_name as cat_small_name']);
            // if(!empty($params['cat_big'])){
            //     $query->andWhere(['cs.big_id' => $params['cat_big']]);
            // }
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

        if (!empty($params['name'])) {
            $query->andWhere(['like','c.name', $params['name']]);
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
            $select = ArrayHelper::merge($select, ['p.product_sn']);
        }
        $countQuery = clone $query;
        //获取总数量
        $count = count($countQuery->select(['sum(oi.nums)as nums'])->all());
        $pagination = '';
        if (empty($params['download'])) {
            //分页
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pagesize]);

            $query->offset($pagination->offset)
                ->limit($pagination->limit);
        }
        $query->select($select);
        $list = $query->all();
        return array('item' => $list, 'pagination' => $pagination);
    }
    //获取最新订单商品价格
    public function getCustomerNewCount($order_id, $default = false){
        if(Yii::$app->params['is_latest_price'] || $default){
            $result = (new Query)
                ->select(['oi.nums', 'p.cost_price'])
                ->from('meet_order_items as oi')
                ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
                ->where(['oi.order_id' => $order_id])
                ->andWhere(['oi.disabled' => 'false'])
                ->orderBy('oi.model_sn ASC')
                ->all();
            $finally = 0;
            foreach($result as $k=>$val){
                $finally += $val['nums'] * $val['cost_price'];
            }
        }else{
            $result = (new Query)
                ->select(['SUM(amount) AS finally'])
                ->from('meet_order_items')
                ->where(['order_id' => $order_id])
                ->andWhere(['disabled' => 'false'])
                ->one();
            $sql = "SELECT SUM(amount) AS finally FROM {{order_items}} WHERE order_id='{$order_id}' AND disabled='false'";
            $result = $this->QueryRow($sql);
            $finally = $result['finally'];
        }
        return $finally;
    }

    //根据商品查找订单数量
    public function customerOrderByProductIdCount($product_id, $params = [])
    {
        $query = new Query;
        $query->select(['sum(oi.nums) as count', 'c.type'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->where(['oi.product_id' => $product_id])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy(['c.type']);
        //判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }

        $result = $query->all();

        $return['self'] = 0;
        $return['customer'] = 0;
        if ($result) {
            foreach ($result as $v) {
                if ($v['type'] == '直营') {
                    $return['self'] = $v['count'];
                } else if ($v['type'] == '客户') {
                    $return['customer'] = $v['count'];
                }
            }
        }
        return $return;
    }
        //根据商品查找订单数量
    public function customerOrderByStyleSnCount($style_sn, $params = [])
    {
        $query = new Query;
        $query->select(['sum(oi.nums) as count', 'c.type'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->where(['oi.style_sn' => $style_sn])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy(['c.type']);
        //判断顾客类型
        if (!empty($params['type'])) {
            $query->andWhere(['c.type' => $params['type']]);
        }

        $result = $query->all();

        $return['self'] = 0;
        $return['customer'] = 0;
        if ($result) {
            foreach ($result as $v) {
                if ($v['type'] == '直营') {
                    $return['self'] = $v['count'];
                } else if ($v['type'] == '客户') {
                    $return['customer'] = $v['count'];
                }
            }
        }
        return $return;
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
}
