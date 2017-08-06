<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%type}}".
 *
 * @property string $type_id
 * @property string $type_name
 * @property string $p_order
 */
class TypeModel extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return  ArrayHelper::merge(parent::behaviors(),
        [
            [
                'class' => 'frontend\behaviors\PublicFind',
                'object' => $this,
            ],
        ]);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'p_order'], 'required'],
            [['p_order'], 'integer'],
            [['type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type_id' => 'Type ID',
            'type_name' => 'Type Name',
            'p_order' => 'P Order',
        ];
    }
    public function getType()
    {
        $result = self::find()->select(['type_id', 'type_name'])->asArray()->all();
        return $result;
    }
}
