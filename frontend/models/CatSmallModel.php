<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%cat_small}}".
 *
 * @property string $cat_id
 * @property string $small_id
 * @property string $cat_name
 * @property string $parent_id
 * @property string $p_order
 */
class CatSmallModel extends \yii\db\ActiveRecord
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
        return '{{%cat_small}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['small_id', 'cat_name'], 'required'],
            [['small_id', 'parent_id', 'p_order'], 'integer'],
            [['cat_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => 'Cat ID',
            'small_id' => 'Small ID',
            'cat_name' => 'Cat Name',
            'parent_id' => 'Parent ID',
            'p_order' => 'P Order',
        ];
    }
    static function getCatSmall($parentId)
    {
        $result = Yii::$app->cache->get('cat_small_id_name');
        if (empty($result)) {
            $result = self::find()
                ->select(['small_id', 'cat_name'])
                ->where(['parent_id' => $parentId])
                ->asArray()
                ->all();
            Yii::$app->cache->set('cat_small_id_name', $result);
        }
        return $result;
    }
}
