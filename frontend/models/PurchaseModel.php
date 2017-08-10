<?php

namespace frontend\models;

use Yii;

/**
 * 订货会类型表
 * This is the model class for table "{{%purchase}}".
 *
 * @property string $purchase_id
 * @property string $purchase_name
 * @property string $p_order
 */
class PurchaseModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_name'], 'required'],
            [['p_order'], 'integer'],
            [['purchase_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'purchase_id' => 'Purchase ID',
            'purchase_name' => 'Purchase Name',
            'p_order' => 'P Order',
        ];
    }
    //获取订货会类型
    static function getPurchase(){
        $result = Yii::$app->cache->get("purchase");
        if(empty($result)){
            $result = self::find()->asArray()->all();
            Yii::$app->cache->set("purchase", $result, 86400);
        }
        return $result;
    }
}
