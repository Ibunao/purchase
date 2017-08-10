<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%season}}".
 *
 * @property string $season_id
 * @property string $season_name
 * @property string $p_order
 */
class SeasonModel extends \yii\db\ActiveRecord
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
        return '{{%season}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['season_name'], 'required'],
            [['p_order'], 'integer'],
            [['season_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'season_name' => 'Season Name',
            'p_order' => 'P Order',
        ];
    }
}
