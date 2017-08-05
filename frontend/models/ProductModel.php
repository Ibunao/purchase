<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use frontend\config\ParamsClass;
use frontend\models\ColorModel;
use frontend\models\CatBigModel;
use frontend\models\CatMiddleModel;
use frontend\models\PurchaseModel;
use yii\data\Pagination;
/**
 * This is the model class for table "{{%product}}".
 *
 * @property string $product_id
 * @property string $purchase_id
 * @property string $product_sn
 * @property string $style_sn
 * @property string $model_sn
 * @property string $serial_num
 * @property string $name
 * @property string $img_url
 * @property string $color_id
 * @property string $size_id
 * @property string $brand_id
 * @property string $cat_b
 * @property string $cat_m
 * @property string $cat_s
 * @property string $season_id
 * @property string $level_id
 * @property string $wave_id
 * @property string $scheme_id
 * @property string $cost_price
 * @property string $price_level_id
 * @property string $memo
 * @property integer $type_id
 * @property string $disabled
 * @property string $is_error
 * @property integer $is_down
 */
class ProductModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_id', 'product_sn', 'style_sn', 'model_sn', 'serial_num', 'name', 'color_id', 'size_id', 'brand_id', 'cat_b', 'cat_m', 'cat_s', 'season_id', 'level_id', 'wave_id', 'scheme_id', 'cost_price', 'price_level_id'], 'required'],
            [['purchase_id', 'serial_num', 'color_id', 'size_id', 'brand_id', 'cat_b', 'cat_m', 'cat_s', 'season_id', 'level_id', 'wave_id', 'scheme_id', 'price_level_id', 'type_id', 'is_down'], 'integer'],
            [['cost_price'], 'number'],
            [['disabled', 'is_error'], 'string'],
            [['product_sn', 'style_sn', 'model_sn'], 'string', 'max' => 30],
            [['name', 'img_url'], 'string', 'max' => 128],
            [['memo'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'purchase_id' => 'Purchase ID',
            'product_sn' => 'Product Sn',
            'style_sn' => 'Style Sn',
            'model_sn' => 'Model Sn',
            'serial_num' => 'Serial Num',
            'name' => 'Name',
            'img_url' => 'Img Url',
            'color_id' => 'Color ID',
            'size_id' => 'Size ID',
            'brand_id' => 'Brand ID',
            'cat_b' => 'Cat B',
            'cat_m' => 'Cat M',
            'cat_s' => 'Cat S',
            'season_id' => 'Season ID',
            'level_id' => 'Level ID',
            'wave_id' => 'Wave ID',
            'scheme_id' => 'Scheme ID',
            'cost_price' => 'Cost Price',
            'price_level_id' => 'Price Level ID',
            'memo' => 'Memo',
            'type_id' => 'Type ID',
            'disabled' => 'Disabled',
            'is_error' => 'Is Error',
            'is_down' => 'Is Down',
        ];
    }

    /**
     * 获取商品筛选条件数据，下拉框数据
     * @param array $data
     * @return mixed
     */
    public function getIndexFilter($data = [])
    {

        $result = $this->getFilter();

        $result['priceList'] = ParamsClass::$priceLevel;
        $result['catMiddle'] = [];
        if (!empty($data['catBig'])) {
            $result['catMiddle'] = CatMiddleModel::getCatMiddle($data['catBig']);
        }

        $result['catSmall'] = [];
        if (!empty($data['catMiddle'])) {
            $result['catSmall'] = CatSmallModel::getCatSmall($data['catMiddle']);
        }

        return $result;
    }
    /**
     * 查询数据
     */
    public function selectQueryRows($fields = '')
    {
        return self::find()->select([$fields])->where(['disabled' => 'false'])->groupBy($fields)->asArray()->all();
    }
    public function getFilter()
    {
        $result = Yii::$app->cache->get('product_filter');
        if (empty($result)) {
            $result['serialNum'] = $this->selectQueryRows('serial_num');
            $result['modelSn'] = $this->selectQueryRows('model_sn');
            $result['name'] = $this->selectQueryRows('name');
            
            $color = new ColorModel();
            $result['color'] = $color->getColor();

            $catBig = new CatBigModel();
            $result['catBig'] = $catBig->getCatBig();
        }
        Yii::$app->cache->set('product_filter', $result);
        return $result;
    }

    /**
     * 商品查询 ，根据关键字搜索出相应的结果
     * @param array $arr 搜索关键字
     * @param string $page 页码
     * @return array|mixed
     */
    public function productSearch($arr = [], $page = 1)
    {

        $query = self::find()
            ->where(['disabled' => 'false'])
            ->groupBy('serial_num')
            ->orderBy(['serial_num' => SORT_DESC]);
        if (!empty($arr['serialNum'])) {
            $query->andWhere(['p.serial_num' => $arr['serialNum']]);
        }
        if (!empty($arr['modelSn'])) {
            $query->andWhere(['p.model_sn' => $arr['modelSn']]);
        }
        if (!empty($arr['name'])) {
            $query->andWhere(['p.name' => $arr['name']]);
        }
        if (!empty($arr['catBig'])) {
            $query->andWhere(['p.cat_b' => $arr['catBig']]);
        }
        if (!empty($arr['catMiddle'])) {
            $query->andWhere(['p.cat_m' => $arr['catMiddle']]);
        }
        if (!empty($arr['catSmall'])) {
            $query->andWhere(['p.cat_s' => $arr['catSmall']]);
        }
        if (!empty($arr['color'])) {
            $query->andWhere(['p.color_id' => $arr['color']]);
        }
        if (!empty($arr['priceList'])) {
            $query->andWhere(['p.price_level_id' => $arr['priceList']]);
        }
        $newQuery = clone $query;
        $count = $newQuery->count();
        //分页
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => ParamsClass::$pageSize]);

        $query->alias('p')
            ->select(['p.serial_num', 'p.model_sn', 'p.name', 'b.cat_name', 'm.cat_name AS cat_middle', 'p.is_down', 's.small_cat_name', 'c.color_name', 'p.cost_price'])
            ->leftJoin('meet_color as c', 'p.color_id = c.color_id')
            ->leftJoin('meet_cat_big as b', 'p.cat_b = b.big_id')
            ->leftJoin('meet_cat_middle as m', 'm.middle_id= p.cat_m')
            ->leftJoin('meet_cat_big_small as s', 'p.cat_s=s.small_id');
        $query->offset($pagination->offset)
            ->limit($pagination->limit);
        $result = $query->asArray()->all();
        return ['result' => $result, 'pagination' => $pagination];
    }

    /**
     * 检查是否有错误信息
     * @return bool
     */
    public function isHaveError(){

        $result = self::find()->where(['is_error' => 'true'])->andWhere(['disabled' => 'false'])->count();
        return $result;
    }

    public function getAddProductFilter($data = [])
    {
        //获取订购会数据
        $purchaseModel = new PurchaseModel;
        $result['purchase'] = $purchaseModel->getPurchase();
        //获取品牌数据
        $brandModel = new BrandModel;
        $result['brand'] = $brandModel->getBrand();
        //色系信息
        $schemeModel = new SchemeModel;
        $result['scheme'] = $schemeModel->getScheme();
        //获取尺码组
        $result['sizeGroup'] = (new Query)->select(['size_group_code', 'group_id', 'size_group_name'])
            ->from('meet_size_group')
            ->all();
        //等级表
        $levelModel = new LevelModel;
        $result['level'] = $levelModel->getLevel();

        //波段表
        $waveModel = new WaveModel;
        $result['wave'] = $waveModel->getWave();

        //大分类
        $catBigModel = new CatBigModel;
        $result['catBig'] = $catBigModel->getList();

        $result['season'] = $result['catMiddle'] = $result['catSmall'] = [];
        if (!empty($data['cat_b'])) {
            //大分类含有的季节
            $result['season'] = (new Query)->select(['season_id', 'season_name'])
                ->from('meet_season_big')
                ->where(['big_id' => $data['cat_b']])
                ->all();

            $this->selectQueryRows("season_id, season_name", "{{season_big}}", "big_id = '{$data['cat_b']}'");
        }

        if (!empty($data['cat_b'])) {
            $result['catMiddle'] = $this->selectQueryRows("middle_id,cat_name", "{{cat_middle}}");
        }

        if (!empty($data['cat_b'])) {
            $result['catSmall'] = $this->selectQueryRows("small_id,small_cat_name AS cat_name", "{{cat_big_small}}", "big_id = '{$data['cat_b']}'");
        }

        $colorModel = new Color();
        $result['color'] = $colorModel->getColor();

        $typeModel = new Type();
        $result['type'] = $typeModel->getType();

        if(!empty($data['sizeGroup'])){
            $result['size'] = $this->selectQueryRows("size_id, size_name", "{{size}}", " group_id='{$data['sizeGroup']}'");
        }

        return $result;
    }
}
