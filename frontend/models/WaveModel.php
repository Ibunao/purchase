<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%wave}}".
 *
 * @property string $wave_id
 * @property string $wave_no
 * @property string $wave_name
 * @property string $p_order
 */
class WaveModel extends \yii\db\ActiveRecord
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
        return '{{%wave}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wave_name'], 'required'],
            [['p_order'], 'integer'],
            [['wave_no'], 'string', 'max' => 10],
            [['wave_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'wave_id' => 'Wave ID',
            'wave_no' => 'Wave No',
            'wave_name' => 'Wave Name',
            'p_order' => 'P Order',
        ];
    }

    public function getWave()
    {
        $result = self::find()->select(['wave_id', 'wave_name'])->asArray()->all();
        return $result;
    }

    public function transWaveAll()
    {
        $result = $this->getWave();
        $item = [];
        foreach( $result as  $k => $v) {
            $item[$v['wave_id']] = $v;
        }
        return $item;
    }

}
