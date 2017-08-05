<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%scheme}}".
 *
 * @property integer $scheme_id
 * @property string $scheme_name
 * @property string $p_order
 * @property string $disabled
 */
class SchemeModel extends \yii\db\ActiveRecord
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
        return '{{%scheme}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scheme_name'], 'required'],
            [['p_order'], 'integer'],
            [['disabled'], 'string'],
            [['scheme_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'scheme_id' => 'Scheme ID',
            'scheme_name' => 'Scheme Name',
            'p_order' => 'P Order',
            'disabled' => 'Disabled',
        ];
    }
    public function getScheme()
    {
        $result = self::find()->select(['scheme_id', 'scheme_name'])->asArray()->all();
        return $result;
    }
}
