<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * 中级分类
 * This is the model class for table "{{%cat_middle}}".
 *
 * @property string $cat_id
 * @property string $middle_id
 * @property string $cat_name
 * @property integer $parent_id
 * @property string $p_order
 */
class CatMiddleModel extends \yii\db\ActiveRecord
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
        return '{{%cat_middle}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['middle_id', 'parent_id', 'p_order'], 'integer'],
            [['cat_name'], 'required'],
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
            'middle_id' => 'Middle ID',
            'cat_name' => 'Cat Name',
            'parent_id' => 'Parent ID',
            'p_order' => 'P Order',
        ];
    }
    static function getCatMiddle($parentId)
    {
        $result = Yii::$app->cache->get('cat_middle_id_name');
        if (empty($result)) {
            $result = self::find()
                ->select(['middle_id', 'cat_name'])
                ->where(['parent_id' => $parentId])
                ->asArray()
                ->all();
            Yii::$app->cache->set('cat_middle_id_name', $result);
        }
        return $result;
    }
}
