<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%order_items}}".
 *
 * @property string $item_id
 * @property string $order_id
 * @property string $product_id
 * @property string $product_sn
 * @property string $style_sn
 * @property string $model_sn
 * @property string $name
 * @property string $price
 * @property string $amount
 * @property string $nums
 * @property string $disabled
 * @property string $import
 */
class OrderItemsModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'product_sn', 'style_sn', 'model_sn', 'name', 'price', 'nums'], 'required'],
            [['order_id', 'product_id', 'nums'], 'integer'],
            [['price', 'amount'], 'number'],
            [['disabled', 'import'], 'string'],
            [['product_sn'], 'string', 'max' => 40],
            [['style_sn', 'model_sn'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Item ID',
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'product_sn' => 'Product Sn',
            'style_sn' => 'Style Sn',
            'model_sn' => 'Model Sn',
            'name' => 'Name',
            'price' => 'Price',
            'amount' => 'Amount',
            'nums' => 'Nums',
            'disabled' => 'Disabled',
            'import' => 'Import',
        ];
    }
}
