<?php

namespace frontend\models;

use Yii;

/**
 * 大分类表
 * This is the model class for table "{{%cat_big}}".
 *
 * @property string $big_id
 * @property string $cat_name
 * @property string $p_order
 */
class CatBigModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cat_big}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_name'], 'required'],
            [['p_order'], 'integer'],
            [['cat_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'big_id' => 'Big ID',
            'cat_name' => 'Cat Name',
            'p_order' => 'P Order',
        ];
    }
    public function getList()
    {
        return self::find()->asArray()->all();
    }
    public function getCatBig()
    {
        return self::find()->select(['big_id', 'cat_name'])->asArray()->all();
    }
}
