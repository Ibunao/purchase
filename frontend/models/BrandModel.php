<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%brand}}".
 *
 * @property string $brand_id
 * @property string $brand_name
 * @property string $p_order
 */
class BrandModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_name'], 'required'],
            [['p_order'], 'integer'],
            [['brand_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'p_order' => 'P Order',
        ];
    }
    
    public function getBrand()
    {
        $result = self::find()->select(['brand_id', 'brand_name'])->asArray()->all();
        return $result;
    }
}
