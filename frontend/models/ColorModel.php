<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%color}}".
 *
 * @property integer $color_id
 * @property string $color_no
 * @property string $color_name
 * @property integer $scheme_id
 * @property integer $p_order
 */
class ColorModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%color}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color_no', 'color_name'], 'required'],
            [['scheme_id', 'p_order'], 'integer'],
            [['color_no'], 'string', 'max' => 5],
            [['color_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'color_id' => 'Color ID',
            'color_no' => 'Color No',
            'color_name' => 'Color Name',
            'scheme_id' => 'Scheme ID',
            'p_order' => 'P Order',
        ];
    }
    public function getColor()
    {
        $result = self::find()
            ->select(['color_id', 'color_no', 'color_name'])
            ->groupBy('color_id')
            ->asArray()
            ->all();
        return $result;
    }
}
