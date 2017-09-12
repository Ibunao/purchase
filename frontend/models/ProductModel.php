<?php

namespace frontend\models;

use Yii;
use yii\db\Query;
use frontend\config\ParamsClass;
use frontend\models\ColorModel;
use frontend\models\CatBigModel;
use frontend\models\CatMiddleModel;
use frontend\models\PurchaseModel;
use frontend\models\OrderModel;
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
            ->groupBy(['serial_num', 'purchase_id'])
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
            ->select(['p.purchase_id', 'p.serial_num', 'p.model_sn', 'p.name', 'b.cat_name', 'm.cat_name AS cat_middle', 'p.is_down', 's.small_cat_name', 'c.color_name', 'p.cost_price'])
            ->leftJoin('meet_color as c', 'p.color_id = c.color_id')
            ->leftJoin('meet_cat_big as b', 'p.cat_b = b.big_id')
            ->leftJoin('meet_cat_middle as m', 'm.middle_id = p.cat_m')
            ->leftJoin('meet_cat_big_small as s', 'p.cat_s = s.small_id');
        $query->offset($pagination->offset)
            ->limit($pagination->limit);
        $result = $query->asArray()->all();
        return ['result' => $result, 'pagination' => $pagination];
    }

    /**
     * 不用
     * 检查是否有错误信息
     * @return bool
     */
    public function isHaveError(){

        $result = self::find()->where(['is_error' => 'true'])->andWhere(['disabled' => 'false'])->count();
        return $result;
    }
    /**
     * use
     * order/product/add
     * order/product/update
     * order/product/copy
     * 
     * 获取增加／修改产品时所有的可选项
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function getProductFilter($data = [])
    {
        $result = Yii::$app->cache->get('add-product-filter');
        if (empty($result)) {
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

            //颜色
            $colorModel = new ColorModel();
            $result['color'] = $colorModel->getColor();
            //类型
            $typeModel = new TypeModel();
            $result['type'] = $typeModel->getType();
            //中级分类
            $result['catMiddle'] = (new Query)->select(['middle_id', 'cat_name'])
                ->from('meet_cat_middle')
                ->all();
            Yii::$app->cache->set('add-product-filter', $result);
        }

        $result['season'] = $result['catSmall'] = [];
        if (!empty($data['cat_b'])) {
            //大分类含有的季节
            $result['season'] = (new Query)->select(['season_id', 'season_name'])
                ->from('meet_season_big')
                ->where(['big_id' => $data['cat_b']])
                ->all();

            //大分类含有的小类
            $result['catSmall'] = (new Query)->select(['small_id', 'small_cat_name AS cat_name'])
                ->from('meet_cat_big_small')
                ->where(['big_id' => $data['cat_b']])
                ->all();
        }

        if(!empty($data['sizeGroup'])){
            //尺寸组下的尺寸
            $result['size'] = (new Query)->select(['size_id', 'size_name'])
                ->from('meet_size')
                ->where(['group_id' => $data['sizeGroup']])
                ->all();
        }

        return $result;
    }


    /**
     * use
     * order/product/update
     * 
     * 修改商品
     * @param  [type] $param     产品参数
     * @param  [type] $moreData  多的尺寸
     * @param  [type] $lessData  少的尺寸
     * @param  [type] $serialNum 流水号
     * @return [type]            [description]
     */
    public function updateProductOperation($param, $moreData, $lessData, $serialNum, $purchaseId)
    {
        if ($param['color_id'] == "" || $param['scheme_id'] == "") {
            echo "<script>alert('数据出错，请重试');</script>";
            die;
        }

        if (empty($param['size'])) {
            echo "<script>alert('如果你不想让这个款号出现，请刷新本页后选择：下架此商品');</script>";
            die;
        }
//??? 更新的不用判断,没必要判断一定是为空的
        //再次判断款号与色号是否已存在
        // $query_model_color_exist = self::find()
        //     ->select(['serial_num'])
        //     ->where(['model_sn' => $param['modelSn']])
        //     ->andWhere(['color_id' => $param['color_id']])
        //     ->andWhere(['<>', 'serial_num', $serialNum])
        //     ->asArray()
        //     ->one();

        // if ($query_model_color_exist) {

        //     $this->redirect(['/order/product/update', 'serial_num' => $serialNum, 'pruchase_id' => $purchaseId]);
        //     die;
        // }

        $param['size'] = $moreData;

        //新增尺码数据
        if (!empty($moreData)) {
            $addResult= $this->_addOnlyAddProducts($param, $moreData, $serialNum, $purchaseId);
        }

        //下架该尺码
        if (!empty($lessData)) {
             $this->_updateProducts($lessData, $serialNum, $purchaseId);
        }


        //修改其他商品基本数据
        return $this->_updateAllSerialNumProduct($param, $serialNum, $purchaseId);

    }
    /**
     * use
     * updateProductOperation
     * 
     * @param [type] $param      参数     
     * @param [type] $moreData   要添加的size id
     * @param [type] $serialNum  流水号
     * @param [type] $purchaseId 订货会类型
     */
    private function _addOnlyAddProducts($param, $moreData, $serialNum, $purchaseId)
    {
        //检查该新增的商品在数据库中是否存在，如果存在就直接把 disabled 修改为 false就好
        $nowTime = time();
        foreach ($moreData as $key => $value) {
            $productObj = self::find()
            ->where(['serial_num' => $serialNum])
            ->andWhere(['purchase_id' => $purchaseId])
            ->andWhere(['size_id' => $value])
            ->one();
            if (!empty($productObj->product_id)) {
                if ($productObj->is_error == 'false') {
                    $productObj->disabled = 'false';
                    if (!$productObj->save()) {
                        var_dump('更新失败', $productObj->errors);exit;
                    }
                    //增加修改日志。等待添加
                    
                }
                unset($moreData[$key]);
            }

        }
        if (empty($moreData)) {
            return '';
        }
        $param['size'] = $moreData;
        //添加动作
        return $this->addProductOperation($param);
    }

    /**
     * use 
     * this/updateProductOperation
     *
     *                                      没有优化
     * 删除商品   disabled=true商品
     * @param $lessData
     * @param $serialNum
     * @return string
     */
    private function _updateProducts($lessData, $serialNum, $purchaseId)
    {
        $nowTime = time();
        foreach ($lessData as $k => $v) {
            $error_product = self::find()->select(['product_id', 'COUNT(*) AS counts'])
                ->where(['serial_num' => $serialNum])
                ->andWhere(['size_id' => $v])
                ->andWhere(['is_error' => 'true'])
                ->andWhere(['purchase_id' => $purchaseId])
                ->asArray()
                ->one();
            if($error_product['counts'] < 0){
                $productObj = self::find()
                    ->where(['serial_num' => $serialNum])
                    ->andWhere(['size_id' => $v])
                    ->andWhere(['is_error' => 'false'])
                    ->andWhere(['disabled' => 'false'])
                    ->andWhere(['purchase_id' => $purchaseId])
                    ->one();

                $isBrought = (new Query)->select()
                    ->from('meet_order_items')
                    ->andWhere(['product_id' => $productObj->product_id])
                    ->andWhere(['disabled' => 'false'])
                    ->andWhere(['purchase_id' => $purchaseId])
                    ->asArray()
                    ->one();

                //添加到购物车的不允许更改
                if (empty($isBrought['nums'])) {
                    $productObj->disabled = 'true';
                    $result = $productObj->save();
                    if (!$result) {
                        var_dump('更新失败');exit;
                    }

                    //添加日志
                    
                }
            }
        }
        return true;
    }

    /**
     * use
     * this/updateProductOperation
     *
     * 更新数据
     * @param  [type] $param     [description]
     * @param  [type] $serialNum [description]
     * @return [type]            [description]
     */
    private function _updateAllSerialNumProduct($param, $serialNum, $purchaseId)
    {
        //色号转换
        $color_no = (new ColorModel)->transColorAll()[$param['color_id']]['color_no'];

        $model_sn = self::find()->where(['serial_num' => $serialNum])
            ->andWhere(['disabled' => 'false'])
            ->andWhere(['is_down' => 0])
            ->andWhere(['purchase_id' => $purchaseId])
            ->one()->model_sn;
        $this->disabledErrorProduct($model_sn);
        //当上传图片为空，给定默认值
        if (empty($param['image'])) {
            $param['image'] = Yii::$app->params['imagePath'] . $param['modelSn'] . "_" . $color_no . ".jpg";
        }

        $nowTime = time();
        $style_sn = $param['modelSn'] . sprintf("%04d", $color_no);
        $price_level_id = $this->_transCostPriceToLevel($param['costPrice']);
        //检查这个款号是否被购买，如果有人购买了，不可修改品牌，名称
        $count = (new Query)->from('meet_order_items')
            ->where(['style_sn' => $style_sn])
            ->count();
        if ($count) {
            //根据流水号修改的数据
            $updateParam = array(
                'img_url' => addslashes($param['image']),
                'memo' => addslashes($param['memo']),
                'is_down' => $param['status'],
            );
            //根据款号修改的数据
            $updateBaseInfo = array(
                'cost_price' => $param['costPrice'],
                'price_level_id' => $price_level_id,
                'cat_b' => $param['catBig'],
                'cat_m' => $param['catMiddle'],
                'cat_s' => $param['catSmall'],
                'season_id' => $param['season'],
                'level_id' => $param['level'],
                'wave_id' => $param['wave'],
                'type_id' => $param['type'],
                'brand_id' => $param['brand'],
            );
        } else {
            //根据流水号修改的数据
            $updateParam = array(
                'name' => addslashes($param['name']),
                'img_url' => addslashes($param['image']),
                'memo' => addslashes($param['memo']),
                'is_down' => $param['status'],
            );
            //根据款号修改的数据
            $updateBaseInfo = array(
                'cost_price' => $param['costPrice'],
                'price_level_id' => $price_level_id,
                'brand_id' => $param['brand'],
                'purchase_id' => $param['purchase'],
                'cat_b' => $param['catBig'],
                'cat_m' => $param['catMiddle'],
                'cat_s' => $param['catSmall'],
                'season_id' => $param['season'],
                'level_id' => $param['level'],
                'wave_id' => $param['wave'],
                'type_id' => $param['type'],
            );
        }
        //修改日志文件
        $log1 = serialize($updateParam);
        $log2 = serialize($updateBaseInfo);


        //添加日志

        
        $updateCon = "serial_num='{$serialNum}' AND purchase_id = {$purchaseId}";
        $result1 = self::updateAll($updateParam, $updateCon);
        $updateCon = "model_sn='{$model_sn}' AND purchase_id = {$purchaseId}";
        $result2 = self::updateAll($updateBaseInfo, $updateCon);
        
        if ($result1 && $result2) {
            return true;
        }
        return false;
    }
    /**
     * use
     * default/index
     * api/login/login
     * 
     * 前台查询用户订单状态
     * @param  [type] $customerId 用户id
     * @return [type]             [description]
     */
    public function checkStatus($customerId)
    {
        $result = (new Query)->select(['status'])
            ->from('meet_order')
            ->where(['customer_id' => $customerId])
            ->all();
        return $result;
    }

    /**
     *  use
     *  this/getThisProductIsDown
     *  this->listModelCache
     *  this->orderSeasonTable
     * 
     * 缓存指定订购会所有产品 包括下架的 并根据流水号排序
     * @return [type] [description]
     */
    public function productListCache()
    {
        $purchaseId = Yii::$app->session->get('purchase_id');
        $list = Yii::$app->cache->get('all-product-list-without-down-' . $purchaseId);
        if (empty($list)) {
            $query = self::find();
            if ($purchaseId == Yii::$app->params['purchaseAB']) {
                $query->where(['in', 'purchase_id' => $purchaseId]);
            }else{
                $query->where(['purchase_id' => $purchaseId]);
            }
                
            $list = $query->andWhere(['disabled' => 'false'])
                ->orderBy(['serial_num' => SORT_ASC])
                ->asArray()
                ->all();
            Yii::$app->cache->set('all-product-list-without-down-' . $purchaseId, $list, 86400);
        }
        return $list;
    }

    /**
     * use
     * order/default/dialogue 
     *
     * 
     * 根据款号查询基本信息
     * @param  string $style_sn [description]
     * @return [type]           [description]
     */
    public function getList($style_sn = '')
    {
        $query = new Query;
        $result = $query->select(['p.name', 'cb.cat_name as big_name', 'cs.cat_name as small_name', 'season.season_name', 'scheme.scheme_name', 
        'p.img_url', 'p.cost_price', 'color.color_name', 'p.style_sn', 'p.product_sn', 'p.memo'])
        ->from('meet_product as p')
        ->leftJoin('meet_cat_big as cb', 'cb.big_id = p.cat_b')
        ->leftJoin('meet_cat_small as cs', 'cs.small_id = p.cat_s')
        ->leftJoin('meet_scheme as scheme', 'scheme.scheme_id = p.scheme_id')
        ->leftJoin('meet_season as season', 'season.season_id = p.season_id')
        ->leftJoin('meet_color as color', 'color.color_id = p.color_id')
        ->where(['style_sn'=>$style_sn])
        ->andWhere(['p.disabled'=>'false'])
        ->one();
        return $result;
    }
    /**
     * use
     * order/default/dialogue
     *  
     * 获取产品订单详情
     * 要求商品表中产品唯一
     * @param  [type] $style_sn [description]
     * @return [type]           [description]
     */
    public function getProductSizeOrder($style_sn)
    {

        $query = new Query;
        //根据款号查询尺码
        $result = $query->select(['p.*', 's.size_name'])
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->where(['p.style_sn' => $style_sn])
            ->andWhere(['p.disabled' => 'false'])
            ->groupBy('p.size_id')
            ->orderBy('p.cat_b')
            ->indexBy('size_id')
            ->all();
        if (empty($result)) {
            return [];
        }
        foreach ($result as $k => $v) {
            //尺寸
            $order[$k]['size'] = $v['size_name'];
            $order[$k]['self'] = 0;
            $order[$k]['customer'] = 0;

            $query = new Query;
            $result = $query->select(['sum(oi.nums) as count', 'c.type'])
                ->from('meet_order as o')
                ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
                ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
                ->where(['oi.product_id' => $v['product_id']])
                ->andWhere(['oi.disabled' => 'false'])
                ->groupBy('c.type')
                ->all();
            if ($result) {
                foreach ($result as $vv) {
                    if ($vv['type'] == '直营') {
                        $order[$k]['self'] += $vv['count'];
                    } else {
                        $order[$k]['customer'] += $vv['count'];
                    }
                }
            }
        }
        return $order;
    }
    /**
     * use
     * order/order/detail
     * 
     * 获取指定款号产品的尺码信息
     * @param  [type] $modelSn 款号
     * @return [type]          [description]
     */
    public function getSizeArr($modelSn)
    {
        $select = ['p.size_id', 's.size_name'];
        $result = (new Query)->select($select)
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->where(['p.model_sn' => $modelSn])
            ->andWhere(['p.disabled' => 'false'])
            ->groupBy('s.size_id')
            ->all();
        return $result;
    }
    /**
     * use
     * order/order/detail
     * 
     * 获取指定款号产品的尺码信息
     * @param  [type] $modelSn 款号
     * @return [type]          [description]
     */
    public function getColorArr($modelSn)
    {
        $select = ['p.color_id', 'c.color_name'];
        $result = (new Query)->select($select)
            ->from('meet_product as p')
            ->leftJoin('meet_color as c', 'c.color_id = p.color_id')
            ->where(['p.model_sn' => $modelSn])
            ->andWhere(['p.disabled' => 'false'])
            ->groupBy('c.color_id')
            ->all();
        return $result;
        
    }
    /**
     * use 
     * order/order/detail
     * 
     * 用户下单的产品中指定款号的商品订单信息
     * @param  [type] $orderId 单号
     * @param  [type] $modelSn 款号
     * @return [type]          [description]
     */
    public function getProductsCount($orderId, $modelSn)
    {
        $select = ['oi.*', 'p.wave_id', 'p.size_id', 'p.color_id', 's.size_name', 'c.color_name', 'w.wave_name', 'p.img_url', 'p.cost_price'];
        $result = (new Query)->select($select)
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->leftJoin('meet_color as c', 'c.color_id = p.color_id')
            ->leftJoin('meet_wave as w', 'w.wave_id = p.wave_id')
            ->where(['order_id' => $orderId])
            ->andWhere(['oi.model_sn' => $modelSn])
            ->andWhere(['oi.disabled' => 'false'])
            ->all();
        return $result;
    }
    /**
     * use 
     * order/order/ExportMaster
     *
     * 不区分订货会输出客户总订单
     * @return [type] [description]
     */
    public function exportMasterAndSlave()
    {
        //查出有订单的客户
        $res = (new Query)->select(['c.relation_code', 'c.customer_id'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id = o.customer_id')
            ->where(['o.disabled' => 'false'])
            ->andWhere(['c.disabled' => 'false'])
            ->all();
        if (empty($res)) {
            return [];
        }
        $customerArr = [];
        foreach ($res as $pre => $suf) {
            $trans[$suf['customer_id']] = $suf;
            $customerArr[] = $suf['customer_id'];
        }
        //批量查询
        $orderResult = (new Query)->select(['o.order_id', 'c.target', 'c.customer_id', 'c.code', 'c.relation_code', 'c.name', 'pu.purchase_name'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'c.customer_id=o.customer_id')
            ->leftJoin('meet_purchase as pu', 'pu.purchase_id = c.purchase_id')
            ->where(['in', 'c.customer_id', $customerArr])
            ->indexBy('customer_id')
            ->all();
        $orderArr = [];
        foreach ($orderResult as $key => $value) {
            $trans[$key] = $value['order_id'];
            $orderArr[] = $value['order_id'];
        }
        $priceResult = (new Query)
            ->select(['sum(oi.nums*p.cost_price) as newprice', 'oi.order_id'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->where(['in', 'oi.order_id' , $orderArr])
            ->andWhere(['oi.disabled' => 'false'])
            ->groupBy('oi.order_id')
            ->orderBy('oi.model_sn ASC')
            ->indexBy('order_id')
            ->all();
        //查询订单的总额等信息
        foreach ($trans as $customerId => $orderId) {
            $result = $orderResult[$customerId];

            $result['count'] = isset($priceResult[$orderId])?$priceResult[$orderId]['newprice']:0;
            $rest[$customerId] = $result;
        }
        foreach($rest as $key => $val){
            $arr[$val['relation_code']]['target'] = isset($arr[$val['relation_code']]['target']) ? $arr[$val['relation_code']]['target'] : 0;
            $arr[$val['relation_code']]['count'] = isset($arr[$val['relation_code']]['count']) ? $arr[$val['relation_code']]['count'] : 0;
            $arr[$val['relation_code']]['order_id'] = isset($arr[$val['relation_code']]['order_id']) ? $arr[$val['relation_code']]['order_id'] : "";
            $arr[$val['relation_code']]['customer_id'] = isset($arr[$val['relation_code']]['customer_id']) ? $arr[$val['relation_code']]['customer_id'] : "";
            $arr[$val['relation_code']]['code'] = isset($arr[$val['relation_code']]['code']) ? $arr[$val['relation_code']]['code'] : "";
            $arr[$val['relation_code']]['name'] = isset($arr[$val['relation_code']]['name']) ? $arr[$val['relation_code']]['name'] : "";
            $arr[$val['relation_code']]['purchase_name'] = isset($arr[$val['relation_code']]['purchase_name']) ? $arr[$val['relation_code']]['purchase_name'] : "";
            $arr[$val['relation_code']]['target'] += $val['target'];
            $arr[$val['relation_code']]['count'] += $val['count'];
            $arr[$val['relation_code']]['order_id'] .= $val['order_id'].",";
            $arr[$val['relation_code']]['customer_id'] .= $val['customer_id'].",";
            $arr[$val['relation_code']]['code'] .= $val['code'].",";
            $arr[$val['relation_code']]['name'] .= $val['name'].",";
            $arr[$val['relation_code']]['purchase_name'] .= $val['purchase_name'].",";
        }
        return $arr;
    }

    /**
     * use
     * order/order/ExportMaster
     *
     * 
     * 获取总指标/已定金额/已审核完成金额       可以优化
     * @return [type] [description]
     */
    public function getCustomerOrderInfo()
    {
        //所有用户的订单真实订货量  
        $real_target = (new Query)->select(['o.order_id', 'status'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'o.customer_id = c.customer_id')
            ->where(['o.disabled' => 'false'])
            ->andWhere(['c.disabled' => 'false'])
            ->indexBy('order_id')
            ->all();
        $orderArr = [];
        $finishOrders = [];
        foreach ($real_target as $orderId => $value) {
            $orderArr[] = $orderId;
            if ($value['status'] = 'finish') {
                $finishOrders[] = $orderId;
            }
        }
        $priceResult = (new Query)
            ->select(['sum(oi.nums*p.cost_price) as newprice'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->where(['in', 'oi.order_id' , $orderArr])
            ->andWhere(['oi.disabled' => 'false'])
            ->one();
        
        //总金额
        $real_tar = $priceResult['newprice'];

        $target = (new Query)->select(['SUM(target) AS target'])
            ->from('meet_customer')
            ->where(['disabled' => 'false'])
            ->one();
        //所有用户提交审核的订货量
        $real_target = (new Query)->select(['o.order_id'])
            ->from('meet_order as o')
            ->leftJoin('meet_customer as c', 'o.customer_id = c.customer_id')
            ->where(['o.disabled' => 'false'])
            ->andWhere(['c.disabled' => 'false'])
            ->andWhere(['o.status' => 'finish'])
            ->all();
        $finishPriceResult = (new Query)
            ->select(['sum(oi.nums*p.cost_price) as newprice'])
            ->from('meet_order_items as oi')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id ')
            ->where(['in', 'oi.order_id' , $finishOrders])
            ->andWhere(['oi.disabled' => 'false'])
            ->one();

        $finish_tar = $finishPriceResult['newprice'];
        $result['real_target'] = $real_tar;
        $result['des_target'] = $target['target'];
        $result['fin_target'] = $finish_tar;
        return $result;
    }
    public function getProductList($modelSn)
    {
        $count = self::find()->where(['model_sn' => $modelSn])
            ->andWhere(['is_down' => '0'])
            ->andWhere(['disabled' => 'false'])
            ->count();
        return $count;
    }
    /**
     * use
     * order/product/add
     * order/product/update
     * order/product/copy
     *
     * 添加产品
     * @param [type] $param [description]
     */
    public function addProductOperation($param)
    {
        if(empty($param['type'])){
            $param['type'] = '0';
        }
        //色号转换
        $color_no = (new ColorModel)->transColorAll()[$param['color']]['color_no'];
        //当上传图片为空，给定默认值
        if (empty($param['image'])) {
            $param['image'] = Yii::$app->params['imagePath'] . $param['modelSn'] . "_" . $color_no . ".jpg";
        }

        //判断是否有空值
        foreach ($param as $key => $value) {
            if ($value == '') {
                return ['code' => 400, 'msg' => "请检查{$k}的{$val}是空，请检查后提交"];
            }
        }

        //再次判断款号与色号是否已存在，如果重复则跳转商品修改页面
        $query_model_color_exist = self::find()->select(['serial_num'])
            ->where(['model_sn' => $param['modelSn']])
            ->andWhere(['color_id' => $param['color']])
            ->asArray()
            ->one();
        if (!empty($query_model_color_exist)) {
            echo "<script>location.href = '/order/product/update&serial_num={$query_model_color_exist['serial_num']}';</script>";
            die;
        }

        //款号
        $style_sn = $param['modelSn'] . sprintf("%04d", $color_no);

        //最大的流水号
        $query_serial_num = self::find()->select(['MAX( serial_num ) AS largest'])
            ->asArray()
            ->one();
        //生成流水号
        $serialNum = $query_serial_num['largest'] + 1;

        //查询本款号的货号的最大一位（以便生成货号）
        $query_model_sn_numbers = self::find()->select(['MAX(SUBSTRING(product_sn,-3,LENGTH(product_sn))) AS nums'])
            ->where(['model_sn' => $param['modelSn']])
            ->asArray()
            ->one();

        if(empty($query_model_sn_numbers['nums'])){
            $countModelSn = 001;
        }else{
            $countModelSn = $query_model_sn_numbers['nums'];
        }

        //获取价格带id
        $priceLevel = $this->_transCostPriceToLevel($param['costPrice']);

        $sql_value = [];
        foreach ($param['size'] as $v) {
            //货号
            $countModelSn++;
            // 三位
            $backNum = sprintf("%03d", $countModelSn);
            // 商品货号
            $product_sn = $style_sn . $backNum;

            $insert_param = array(
                'purchase_id' => $param['purchase'],
                'product_sn' => $product_sn,
                'style_sn' => $style_sn,
                'model_sn' => $param['modelSn'],
                'serial_num' => $serialNum,
                'name' => addslashes($param['name']),
                'img_url' => $param['image'],
                'brand_id' => $param['brand'],
                'cat_b' => $param['catBig'],
                'cat_m' => $param['catMiddle'],
                'cat_s' => $param['catSmall'],
                'color_id' => $param['color'],
                'size_id' => $v,
                'season_id' => $param['season'],
                'level_id' => $param['level'],
                'wave_id' => $param['wave'],
                'scheme_id' => $param['scheme'],
                'cost_price' => $param['costPrice'],
                'price_level_id' => $priceLevel,
                'memo' => addslashes($param['memo']),
                'type_id' => $param['type'],
                'disabled' => 'false',
                'is_down' => $param['status'],
            );
            $sql_value[] = $insert_param;
        }
        $fields = ['purchase_id', 'product_sn', 'style_sn', 'model_sn',
        'serial_num', 'name', 'img_url', 'brand_id', 'cat_b', 'cat_m', 
        'cat_s', 'color_id', 'size_id', 'season_id', 'level_id', 'wave_id', 'scheme_id', 'cost_price', 'price_level_id', 'memo', 'type_id', 'disabled', 'is_down'];
        $result = Yii::$app->db
            ->createCommand()
            ->batchInsert(self::tableName(),
                $fields,
                $sql_value)
            ->execute();
        if ($result) {
            return true;
        }
        return ['code' => 400, 'msg' => "添加失败"];

    }


    /**
     * 判断吊牌价所处的价格带id
     * @param string $costPrice
     * @return string
     */
    private function _transCostPriceToLevel($costPrice = '')
    {
        $costPrice = (int)$costPrice;
        if ($costPrice <= 99) {
            return "1";

        } elseif ($costPrice >= 100 && $costPrice <= 199) {
            return "2";

        } elseif ($costPrice >= 200 && $costPrice <= 299) {
            return "3";

        } elseif ($costPrice >= 300 && $costPrice <= 399) {
            return "4";

        } elseif ($costPrice >= 400 && $costPrice <= 499) {
            return "5";

        } elseif ($costPrice >= 500 && $costPrice <= 999) {
            return "6";

        } elseif ($costPrice >= 1000 && $costPrice <= 1499) {
            return "7";

        } elseif ($costPrice >= 1500 && $costPrice <= 2000) {
            return "8";

        } else {
            return "9";
        }
    }

    /**
     * use
     * order/product/explort
     *
     * 导出商品
     * @return [type] [description]
     */
    public function getListModel()
    {
        $select = ['pu.purchase_name', 'p.purchase_id', 'p.name', 'p.model_sn', 'p.serial_num', 'p.cost_price', 'br.brand_id', 'br.brand_name', 'z.size_no', 'z.size_name', 'co.color_no', 'co.color_name', 'cb.big_id as cat_b_id', 'cb.cat_name as cat_b', 'cm.middle_id as cat_m_id', 'cm.cat_name as cat_m', 'cs.small_id as cat_s_id', 'cs.cat_name as cat_s', 'le.level_name', 'sc.scheme_name', 'se.season_id', 'se.season_name', 'wa.wave_no', 'wa.wave_name', 'p.memo', 't.type_name'];
        $list = (new Query)->select($select)
            ->from('meet_product p')
            ->leftJoin('meet_size z', 'p.size_id = z.size_id')
            ->leftJoin( 'meet_color co', 'p.color_id = co.color_id')
            ->leftJoin( 'meet_cat_big cb', 'p.cat_b = cb.big_id')
            ->leftJoin( 'meet_cat_middle cm', 'p.cat_m = cm.middle_id')
            ->leftJoin( 'meet_cat_small cs', 'p.cat_s = cs.small_id')
            ->leftJoin( 'meet_level le', 'p.level_id = le.level_id')
            ->leftJoin( 'meet_purchase pu', 'p.purchase_id = pu.purchase_id')
            ->leftJoin( 'meet_scheme sc', 'p.scheme_id = sc.scheme_id')
            ->leftJoin( 'meet_season se', 'p.season_id = se.season_id')
            ->leftJoin( 'meet_wave wa', 'p.wave_id = wa.wave_id')
            ->leftJoin( 'meet_brand br', 'p.brand_id = br.brand_id')
            ->leftJoin( 'meet_type t', 't.type_id = p.type_id')
            ->where( ['p.disabled' => 'false'])
            ->orderBy(['p.purchase_id' => SORT_ASC, 'p.serial_num' => SORT_ASC])
            ->all();
        $result = array();
        foreach ($list as $v) {
            if (!isset($result[$v['model_sn']]) || !$result[$v['model_sn']]) $result[$v['model_sn']] = $v;
            $result[$v['model_sn']]['color_str'][] = $v['color_no'] . '[' . $v['color_name'] . ']' . ';' . '000[无定义]';
            $result[$v['model_sn']]['size_str'][] = $v['size_no'] . '[' . $v['size_name'] . ']';
        }


        return $result;
    }



/**
 * 前台的方法
 */

    /**
     * use
     * default/index
     * 
     * 前台商品搜索
     * @param $conArr  搜索条件
     * @param $serial   搜索型号
     * @param $params   小条件
     * @param int $price  价格排序
     * @param int $page  页码
     * @param int $pagesize
     * @return array
     */
    public function newitems($conArr, $serial, $params, $price = 1, $page = 1, $pagesize = 8){
        
        //根据输入框的长度来判断是否是 model_sn型号 还是 serial_num 流水号查询 出去重的 style_sn 款号
        if(strlen($serial) >4){
            //获取查询的去重的款号 的型号  
            $query = self::find()->select(['style_sn'])
                ->where(['like', 'model_sn', $serial.'%', false])//右模糊
                ->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => 0]);
                if ($params['purchase_id'] == Yii::$app->params['purchaseAB']) {
                    $query->andWhere(['in', 'purchase_id', [1,2]]);
                }else{
                    $query->andWhere(['purchase_id' => $params['purchase_id']]);
                }
                $row = $query->distinct()
                ->indexBy('style_sn')
                ->asArray()
                ->all();
            if (empty($row)) return [];
            //根据查询出的款号 和 搜索条件 获取商品的详细信息
            $items = $this->listStyleSn($row, $params, $conArr);
        }else{
            if (!empty($serial)) {
                //流水号
                $query = self::find()->select(['style_sn'])
                ->where(['serial_num', $serial])
                ->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => 0]);
                if ($params['purchase_id'] == Yii::$app->params['purchaseAB']) {
                    $query->andWhere(['in', 'purchase_id', [1,2]]);
                }else{
                    $query->andWhere(['purchase_id' => $params['purchase_id']]);
                }
                $row = $query->distinct()
                ->indexBy('style_sn')
                ->asArray()
                ->all();

                if (empty($row)) return [];
                $items = $this->listStyleSn($row, $params, $conArr);

            }else{
                $style_sn = [];
                $items = $this->listStyleSn($style_sn, $params, $conArr);
            }
        }
// var_dump($items);exit;
        
// var_dump($items);exit;
        //人气排序 1:降序  2:升序
        $hits_sort = [];
        if ($params['hits'] && !empty($items)) {
            //根据下单数量来定义人气
            $order_item_list = (new Query)->select(['style_sn', 'SUM(nums) AS num'])
            ->from('meet_order_items')
            ->where(['disabled' => 'false'])
            ->groupBy('style_sn')
            ->all();
            foreach ($order_item_list as $v) {
                $order_item_list[$v['style_sn']] = $v['num'];
            }

            foreach ($items as $k => $v) {
                $num = isset($order_item_list[$v['style_sn']]) ? $order_item_list[$v['style_sn']] : 0;
                $items[$k]['hit_num'] = $num;
                $hits_sort[$k] = $num;
            }

            $sort2 = $params['hits'] == 2 ? SORT_ASC : SORT_DESC;
            //这个方法应该就没有起到排序的作用
            //只有在按照人气从低到高的时候使用这个方法
            if ($params['hits'] == 2) {
                array_multisort($hits_sort, $sort2, $items);
            }else{
//人为规定排序
        $orderList = (new Query)->select(['style_sn', 'min(`order`) as porder'])
        ->from('meet_product')
        ->where(['disabled' => 'false'])
        ->andWhere(['<>', 'order', 999])
        ->groupBy('style_sn')
        ->orderBy(['porder' => SORT_ASC])
        ->indexBy('style_sn')
        ->all();

        foreach ($orderList as $k => $v) { 
            if (isset($items[$k])) {
                $orderProduct[$k] = $items[$k];
                unset($items[$k]);    
                   }       
        }
// var_dump($orderProduct);exit;
        if (isset($orderProduct)) {
            $items = $orderProduct + $items;
        }
}
        }

        //价格升降排序 1:升序  2:降序
        $price_sort = [];
        if ($price && !empty($items)) {
            foreach ($items as $k => $v) {
                $price_sort[$k] = $v['cost_price'];
            }
            $sort1 = $price == 2 ? SORT_ASC : SORT_DESC;
            array_multisort($price_sort, $sort1, $items);
        }
        //这里可以根据查询条件进行缓存的，这样分页太差劲了
        //分页超出
        if (($page - 1) * $pagesize > count($items)) return [];
        // var_dump($items);exit;
        //从数组中取出指定分页需要的数据
        return array_slice($items, ($page - 1) * $pagesize, $pagesize);
    }

    /**
     * use
     * this->newitems
     * 
     * 搜索框为空是搜索的产品
     * @return [type] [description]
     */
    public function listStyleSn($style_sn, $params, $conArr)
    {
        //尺码表获取所有的尺码  
        $size_list = $this->getSizes();
        //获取该订购会下所有上线的商品
        $list = $this->getProductUp();

        //获取客户订单详细信息
        $order_row = $this->getOrderInfo($params['purchase_id'], $params['customer_id']);

        $items_model_sn = [];
        //记录客户下单的款号style_sn
        foreach ($order_row as $v) {
            $items_model_sn[] = $v['style_sn'];
        }
        $items = [];
        foreach ($list as $v) {
            //款号筛选
            if ($style_sn && !isset($style_sn[$v['style_sn']])){
                continue;
            }
            //搜索已订条件的产品
            if ($params['or'] == 1 && !in_array($v['style_sn'], $items_model_sn)) continue;
            //搜索未订购条件的产品
            if ($params['or'] == 2 && in_array($v['style_sn'], $items_model_sn)) continue;
            $item = $v;
            //筛选条件
            $item['search_id'] = [
                's_id_' . $v['cat_b'],
                'c_id_' . $v['cat_s'],
                'sd_' . $v['season_id'],
                'wv_' . $v['wave_id'],
                'lv_' . $v['level_id'],
                'plv_' . $v['price_level_id'],
            ];

            //根据筛选条件进行筛选  
            //根据该条记录拼接数来的筛选条件和用户传过来的筛选条件进行交集，看是否等于用户的筛选条件，如果等于则符合用户筛选
            if (array_intersect($conArr, $item['search_id']) != $conArr) continue;

            //该商品是否已订 
            $item['is_order'] = in_array($v['style_sn'], $items_model_sn) ? 1 : 2;

            //尺码
            //款号style_sn 相同则尺寸信息相同  
            //获取一个style_sn 下产品的所有尺寸,以及商品信息
            if (isset($items[$v['style_sn']])) {
                $item['size'] = $items[$v['style_sn']]['size'];
                $item['size_item'] = $items[$v['style_sn']]['size_item'];
            }

            if (!isset($item['size']) || !in_array($size_list[$v['size_id']], $item['size'])) {
                $item['size'][$v['size_id']] = $size_list[$v['size_id']];
            }
            $row['product_id'] = $v['product_id'];
            $row['product_sn'] = $v['product_sn'];
            $row['size_name'] = $size_list[$v['size_id']];//尺码
            $item['size_item'][] = $row;
            $items[$v['style_sn']] = $item;//款号的信息
        }
        return $items;
    }
    /**
     * use
     * this->listSerial
     *
     * 
     * 获取尺码数据
     * @return [type] [description]
     */
    public function getSizes()
    {
        $items = Yii::$app->cache->get('size-id-list');
        if (empty($items)) {
            $result = (new Query)->select(['size_name', 'size_id'])
                ->from('meet_size')
                ->all();
            $items = [];
            foreach ($result as $row) {
                $items[$row['size_id']] = $row['size_name'];
            }
            Yii::$app->cache->set('size-id-list', $items);
        }
        return $items;
    }
    /**
     * use
     * this->listSerial
     *
     * 获取指定订购会下所有上架的商品
     * @return [type] [description]
     */
    public function getProductUp()
    {
        $purchaseId = Yii::$app->session['purchase_id'];
        $items = Yii::$app->cache->get('product-list-'.$purchaseId);
        if (empty($items)) {
            $query = self::find();
            if ($purchaseId == Yii::$app->params['purchaseAB']) {
                $query->where(['or', 'purchase_id', [1,2]]);
            }else{
                $query->where(['purchase_id' => $purchaseId]);
            }
        
            $items = $query->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => 0])
                ->orderBy(['serial_num' => SORT_ASC])
                ->indexBy('product_id')
                ->asArray()
                ->all();
            Yii::$app->cache->set('product-list-'.$purchaseId, $items, 3600*24);
        }
        return $items;
    }
    /**
     * use
     * this->listSerial
     *
     * 
     * 获取客户订单详情
     * @param  [type] $purchaseId [description]
     * @param  [type] $customerId [description]
     * @return [type]             [description]
     */
    public function getOrderInfo($purchaseId, $customerId)
    {
        //获取订单详情
        $items = (new Query)->select(['oi.nums', 'oi.product_id', 'oi.style_sn', 'oi.model_sn'])
            ->from('meet_order as order')
            ->leftJoin('meet_order_items as oi', 'order.order_id = oi.order_id')
            ->where(['order.disabled' => 'false'])
            ->andWhere(['oi.disabled' => 'false'])
            ->andWhere(['order.purchase_id' => $purchaseId])
            ->andWhere(['order.customer_id' => $customerId])
            ->all();

        if (empty($items)) {
            return [];
        }
        $upProductIds = $this->getProductUp($purchaseId);
        $model = [];
        //判断订单里的产品是否下架
        foreach ($items as $item) {
            $model[$item['product_id']] = $item;
            $model[$item['product_id']]['is_down'] = isset($upProductIds[$item['product_id']])?0:1;
        }
        return $model;
    }
     /**
     * id转名称
     *
     * @param $table
     * @param $select
     * @param $where_id
     * @return array
     */
    public function tableValue($table, $select, $where_id)
    {
        $items = Yii::$app->cache->get($table . '-id-list');
        if (!$items) {
            $list = (new Query)->select([$where_id, $select])
                ->from('meet_' . $table)
                ->all();
            $items = array();
            foreach ($list as $v) {
                $items[$v[$where_id]] = $v[$select];
            }
            Yii::$app->cache->set($table . '-id-list', $items, 3600 * 24);
        }
        return $items;
    }

    /**
     * use
     * default/detail
     *
     * 用户订单详情以及商品是否下架
     * @param  [type]  $purchase_id 订货会品牌
     * @param  [type]  $customer    客户id
     * @param  boolean $model_sn    型号
     * @return [type]               用户订单详情以及商品是否下架
     */
    public function getThisOrderedInfo($purchase_id, $customer, $model_sn = ''){
        $orderId = $query = (new Query)->select(['order_id'])
            ->from('meet_order')
            ->where(['customer_id' => $customer])
            ->andWhere(['disabled' => 'false']);
        if ($purchase_id == 3) {
            $query->andWhere(['in', 'purchase_id', [1,2]]);
        }else{
            $query->andWhere(['purchase_id' => $purchase_id]);
        }
        $orderId = $query->one();
        if(empty($orderId)) return [];
        //获取订单的详细信息
        $item_list = (new Query)->select(['nums', 'product_id', 'style_sn', 'model_sn'])
            ->from('meet_order_items')
            ->where(['order_id' => $orderId['order_id']])
            ->andWhere(['disabled' => 'false'])
            ->andFilterWhere(['model_sn' => $model_sn])
            ->all();
        if(empty($item_list)) return [];

        //是否下架
        $res = $this->getThisProductIsDown();
        foreach ($item_list as $v) {
            $model[$v['product_id']] = $v;
            $model[$v['product_id']]['is_down'] = isset($res[$v['product_id']]) ? $res[$v['product_id']] : 0;
        }
        return $model;
    }
    /**
     * use 
     * this->getThisOrderedInfo
     *
     * 产品id和是否下架
     * @return [type] [description]
     */
    public function getThisProductIsDown()
    {
        $res = $this->productListCache();
        $result = [];
        foreach($res as $val){
            $result[$val['product_id']] = $val['is_down'];
        }
        return $result;
    }
    /**
     * use
     * default/detail
     * 
     * 按款号缓存
     * 
     * @param $model_sn
     * @return mixed
     */
    public function listModelCache($model_sn)
    {
        $size_list = $this->tableValue('size', 'size_name', 'size_id');
        $color_list = $this->tableValue('color', 'color_name', 'color_id');
        $purchaseId = Yii::$app->session['purchase_id'];
        $items = Yii::$app->cache->get('model-product-list-' . $purchaseId);
        
        if (!$items) {
            $list = $this->productListCache();;
            foreach ($list as $v) {
                $item = $v;
                $product_item['product_id'] = $v['product_id'];
                $product_item['product_sn'] = $v['product_sn'];
                $product_item['cost_price'] = $v['cost_price'];
                $product_item['size_id'] = $v['size_id'];
                $product_item['color_id'] = $v['color_id'];
                $product_item['is_down'] = $v['is_down'];
                $size_item['size_id'] = $v['size_id'];
                $size_item['size_name'] = $size_list[$v['size_id']];
                $color_item['color_id'] = $v['color_id'];
                $color_item['color_name'] = $color_list[$v['color_id']];
                if (isset($items[$v['model_sn']])) {
                    $items[$v['model_sn']]['product_list'][$v['size_id'] . '_' . $v['color_id']] = $product_item;
                    if (!in_array($size_item, $items[$v['model_sn']]['size_list']))
                        $items[$v['model_sn']]['size_list'][$v['size_id']] = $size_item;
                    if (!in_array($color_item, $items[$v['model_sn']]['color_list']))
                        $items[$v['model_sn']]['color_list'][] = $color_item;
                    continue;
                }
                $item['product_list'][$v['size_id'] . '_' . $v['color_id']] = $product_item;
                $item['size_list'][$v['size_id']] = $size_item;
                $item['color_list'][] = $color_item;
                $items[$v['model_sn']] = $item;
            }
            Yii::$app->cache->set('model-product-list-' . $purchaseId, $items, 3600 * 24);
        }
        return $items[$model_sn];
    }

    /**
     * use
     * ordermodel/itemListAjax
     * 前台使用的
     * 缓存商品id，不包含下架的
     * @return [type] [description]
     */
    public function listcacheId()
    {
        $list = Yii::$app->cache->get('all-product-list-id-' . Yii::$app->session['purchase_id']);
        if (!$list) {
            $purchaseId = Yii::$app->session['purchase_id'];
            $query = self::find();
            if ($purchaseId == Yii::$app->params['purchaseAB']) {
                $query->where(['in', 'purchase_id' => $purchaseId]);
            }else{
                $query->where(['purchase_id' => $purchaseId]);
            }
            
            $result = $query->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => '0'])
                ->orderBy(['serial_num' => SORT_ASC])
                ->asArray()
                ->all();
            foreach ($result as $v) {
                $list[$v['product_id']] = $v;
            }
            Yii::$app->cache->set('all-product-list-id-' . Yii::$app->session['purchase_id'], $list, 3600 * 24);
        }
        return $list;
    }
    /**
     * use
     * ordermodel/orderItemItem
     * 前台使用的
     * 缓存商品id，包含下架的
     * @return [type] [description]
     */
    public function listcacheAllId()
    {
        $list = Yii::$app->cache->get('all-product-list-all-id-' . Yii::$app->session['purchase_id']);
        if (!$list) {
            $purchaseId = Yii::$app->session['purchase_id'];
            $query = self::find();
            if ($purchaseId == Yii::$app->params['purchaseAB']) {
                $query->where(['in', 'purchase_id' => $purchaseId]);
            }else{
                $query->where(['purchase_id' => $purchaseId]);
            }
            
            $result = $query->andWhere(['disabled' => 'false'])
                ->asArray()
                ->all();
            foreach ($result as $v) {
                $list[$v['product_id']] = $v;
            }
            Yii::$app->cache->set('all-product-list-all-id-' . Yii::$app->session['purchase_id'], $list, 3600 * 24);
        }
        return $list;
    }
    /**
     * 判断此商品是否为下架
     * @param $product_id
     * @return string
     */
    public function checkThisProductIsDown($product_id){
        
        $result = self::find()->select(['is_down'])
            ->where(['product_id' => $product_id])
            ->andWhere(['disabled' => 'false'])
            ->asArray()
            ->one();
        if($result['is_down'] == '1'){
            return "";
        }else{
            return $product_id;
        }
    }

    /**
     * use
     * forder/byprice
     * 
     * @param  [type] $item_list [description]
     * @return [type]            [description]
     */
    public function orderJiaGeDaiItems($item_list)
    {
        //按product_id 排列数组
        foreach ($this->productListCache() as $v) {
            $all_list[$v['product_id']] = $v;
        }

        $big = $this->tableValue('cat_big', 'cat_name', 'big_id');
        $jgd = array(
            '1' => '0-99',
            '2' => '100-199',
            '3' => '200-299',
            '4' => '300-399',
            '5' => '400-499',
            '6' => '500-999',
            '7' => '1000-1499',
            '8' => '1500-2000',
            '9' => '2000以上',
        );
        //var_dump($jgd);die;
        $items = array();
        $model = array();
        $total_nums = 0;
        $js = array();
        //$all=0;
        $amount = 0.00;
        foreach ($item_list as $v) { //$item_list所有已定款式
            if (!isset($all_list[$v['product_id']])) continue;
            $product_item = $all_list[$v['product_id']];//$product_item 是循环出来的该产品的product的商品信息
            $item['product_id'] = $v['product_id'];
            $item['s_id'] = $product_item['cat_s'];
            $dpj = $product_item['price_level_id'];
            $item['dpj'] = $jgd[$dpj];
            $item['all'] =
            $item['nums'] = $v['nums'];//购买该产品的数量
            //总计
            $total_nums += $v['nums'];//统计购买该产品的数量
            $model[] = $v['model_sn'];//统计型号
            $amount += $v['amount'];//统计购买此产品的总价格

            $items[$product_item['cat_b']]['b_id'] = $product_item['cat_b'];//该大类的ID号
            $items[$product_item['cat_b']]['b_name'] = $big[$product_item['cat_b']];//大类名称
            $items[$product_item['cat_b']]['model'][] = $v['model_sn'];//此商品的款号

            if (!isset($items[$product_item['cat_b']]['nums'])) $items[$product_item['cat_b']]['nums'] = 0;//判断该大类下是否有该商品
            $items[$product_item['cat_b']]['nums'] += $v['nums'];//统计该大类的数量

            if (!isset($items[$product_item['cat_b']]['amount'])) $items[$product_item['cat_b']]['amount'] = 0;
            $items[$product_item['cat_b']]['amount'] += $v['amount'];//统计该大类商品总金额数量


            $items[$product_item['cat_b']]['dpj'][$item['dpj']]['s_id'] = $item['s_id'];//显示大类名称
            $items[$product_item['cat_b']]['dpj'][$item['dpj']]['name'] = $item['dpj'];//显示吊牌价区间
            //小分类款号
            $items[$product_item['cat_b']]['dpj'][$item['dpj']]['model'][] = $v['model_sn'];

            //小分类商品数量
            if (!isset($items[$product_item['cat_b']]['dpj'][$item['dpj']]['nums'])) $items[$product_item['cat_b']]['dpj'][$item['dpj']]['nums'] = 0;
            $items[$product_item['cat_b']]['dpj'][$item['dpj']]['nums'] += $v['nums'];
            //var_dump($items[$product_item['cat_b']]['dpj'][$item['s_id']]['nums']);die;
            //小分类商品总金额数量
            if (!isset($items[$product_item['cat_b']]['dpj'][$item['dpj']]['amount'])) $items[$product_item['cat_b']]['dpj'][$item['dpj']]['amount'] = 0;
            $items[$product_item['cat_b']]['dpj'][$item['dpj']]['amount'] += $v['amount'];
            $js[] = $v['model_sn'];
//                //小分类季节商品数量
//                if (!isset($items[$product_item['cat_b']]['dpj'][$item['dpj']]['dpj'])) $items[$product_item['cat_b']]['dpj'][$item['dpj']]['dpj'] = 0;
////                if (!isset($items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'])) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'] = 0;
//                if ($product_item['dpj'] == $item['dpj']) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_1'] += $v['nums'];
////                if ($product_item['season_id'] == 2) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'] += $v['nums'];
        }
        $all = count(array_unique($js));
        return array('list' => $items, 'total_nums' => $total_nums, 'model' => $model, 'amount' => $amount, 'all' => $all);
    }

    public function orderSprandSumItems($item_list)
    {
        $season_one = Yii::$app->params['season_one'];
        $season_two = Yii::$app->params['season_two'];
        //按product_id 排列数组
        foreach ($this->productListCache() as $v) {
            $all_list[$v['product_id']] = $v;
        }

        $big = $this->tableValue('cat_big', 'cat_name', 'big_id');
        $small = $this->tableValue('cat_small', 'cat_name', 'small_id');

        $items = array();
        $model = array();
        $total_nums = 0;
        $season_1 = 0;
        $season_2 = 0;
        $season_1_amount = 0.00;
        $season_2_amount = 0.00;
        $amount = 0.00;
        $js = array();
        foreach ($item_list as $v) {
            if (!isset($all_list[$v['product_id']])) continue;
            $product_item = $all_list[$v['product_id']];
            $item['product_id'] = $v['product_id'];
            $item['s_id'] = $product_item['cat_s'];
            $item['s_name'] = $small[$product_item['cat_s']];
            $item['nums'] = $v['nums'];

            //总计
            $total_nums += $v['nums'];
            $model[] = $v['model_sn'];
            $amount += $v['amount'];

            $items[$product_item['cat_b']]['b_id'] = $product_item['cat_b'];
            $items[$product_item['cat_b']]['b_name'] = $big[$product_item['cat_b']];
            //大分类款号
            $items[$product_item['cat_b']]['model'][] = $v['model_sn'];

            //大分类商品数量
            if (!isset($items[$product_item['cat_b']]['nums'])) $items[$product_item['cat_b']]['nums'] = 0;
            $items[$product_item['cat_b']]['nums'] += $v['nums'];
            //大分类商品总金额数量
            if (!isset($items[$product_item['cat_b']]['amount'])) $items[$product_item['cat_b']]['amount'] = 0;
            $items[$product_item['cat_b']]['amount'] += $v['amount'];

            //大分类季节商品数量
            if (!isset($items[$product_item['cat_b']]['season_id_1'])) $items[$product_item['cat_b']]['season_id_1'] = 0;
            if (!isset($items[$product_item['cat_b']]['season_id_2'])) $items[$product_item['cat_b']]['season_id_2'] = 0;
            if ($product_item['season_id'] == $season_one) {
                $items[$product_item['cat_b']]['season_id_1'] += $v['nums'];
                $season_1 += $v['nums'];
                $season_1_amount += $v['amount'];
            }
            if ($product_item['season_id'] == $season_two) {
                $items[$product_item['cat_b']]['season_id_2'] += $v['nums'];
                $season_2 += $v['nums'];
                $season_1_amount += $v['amount'];
            }

            $items[$product_item['cat_b']]['small'][$item['s_id']]['s_id'] = $item['s_id'];
            $items[$product_item['cat_b']]['small'][$item['s_id']]['name'] = $small[$item['s_id']];
            //小分类款号
            $items[$product_item['cat_b']]['small'][$item['s_id']]['model'][] = $v['model_sn'];

            //小分类商品数量
            if (!isset($items[$product_item['cat_b']]['small'][$item['s_id']]['nums'])) $items[$product_item['cat_b']]['small'][$item['s_id']]['nums'] = 0;
            $items[$product_item['cat_b']]['small'][$item['s_id']]['nums'] += $v['nums'];
            //小分类商品总金额数量
            if (!isset($items[$product_item['cat_b']]['small'][$item['s_id']]['amount'])) $items[$product_item['cat_b']]['small'][$item['s_id']]['amount'] = 0;
            $items[$product_item['cat_b']]['small'][$item['s_id']]['amount'] += $v['amount'];

            //小分类季节商品数量
            if (!isset($items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_1'])) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_1'] = 0;
            if (!isset($items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'])) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'] = 0;
            if ($product_item['season_id'] == $season_one) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_1'] += $v['nums'];
            if ($product_item['season_id'] == $season_two) $items[$product_item['cat_b']]['small'][$item['s_id']]['season_id_2'] += $v['nums'];
        }
        $all = count(array_unique($js));
        return array('list' => $items, 'total_nums' => $total_nums, 'season_1' => $season_1, 'season_2' => $season_2, 'model' => $model, 'amount' => $amount, 'all' => $all, 'season_1_amount' => $season_1_amount, 'season_2_amount' => $season_2_amount);
    }

    /**
     * use
     * api/default/index
     *     
     * 获取当前款号总数
     * @param  [type] $customer_id 客户id 
     * @param  [type] $model_sn    款号
     * @param  string $serial_num  流水号
     * @return [type]              [description]
     */
    public function getThisModelOrdered($customer_id, $model_sn, $serial_num = "")
    {
        $styleSn = '';
        if (!empty($serial_num)) {
            $styRes = self::find()->select(['style_sn'])
                ->where(['serial_num' => $serial_num])
                ->andWhere(['disabled' => 'false'])
                ->asArray()
                ->one();
            $styleSn = $styRes['style_sn'];
        }
        $num = (new Query)->from('meet_order as o')
            ->leftJoin('meet_order_items as i', 'i.order_id = o.order_id')
            ->where(['o.customer_id' => $customer_id])
            ->andWhere(['i.model_sn' => $model_sn])
            ->andFilterWhere(['i.style_sn' => $styleSn])
            ->andWhere(['i.disabled' => 'false'])
            ->sum('i.nums');
        return $num;
    }
    /**
     * use
     * api/default/index
     * this->getThisProductInfo
     * 
     * app获取该款号的所有尺寸详细信息
     * @param $model_sn
     * @param $purchase_id
     * @param $customer_id
     * @return array
     */
    public function getProductListsInfo($model_sn, $purchase_id, $customer_id)
    {
        $size_list = $this->tableValue('size', 'size_name', 'size_id');
        $color_list = $this->tableValue('color', 'color_name', 'color_id');

        $product_list = Yii::$app->cache->get("app-get-size-color-product_sn-" . $model_sn);
        if (!$product_list) {
            $query = slef::find()
                ->select(['size_id', 'color_id', 'product_sn', 'product_id'])
                ->where(['model_sn' => $model_sn]);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id' => $purchase_id]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $res = $query->andWhere(['disabled' => 'false'])
                ->asArray()
                ->all();
            foreach ($res as $v) {
                $product_list[$v['size_id'] . "_" . $v['color_id']] = $v;
            }
            Yii::$app->cache->set("app-get-size-color-product_sn-" . $model_sn, $product_list, 3600);
        }

        $size_tran_list = Yii::$app->cache->get("app-get-size_id-by-purchase-id-" . $model_sn);
        if (!$size_tran_list) {
            $query = self::find()->select(['size_id'])
                ->where(['model_sn' => $model_sn]);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id' => $purchase_id]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $size_tran_list = $query->andWhere(['disabled' => 'false'])
                ->groupBy('size_id')
                ->asArray()
                ->indexBy('size_id')
                ->all();

            Yii::$app->cache->set("app-get-size_id-by-purchase-id-" . $model_sn, $size_tran_list, 3600);
        }
        $customers_product = (new Query)->select(['i.nums', 'i.product_sn'])
            ->from('meet_order_items as i')
            ->leftJoin('meet_order as o', 'o.order_id=i.order_id')
            ->where(['o.customer_id' => $customer_id])
            ->andWhere(['i.disabled' => 'false'])
            ->all();

        foreach ($customers_product as $v) {
            $item[$v['product_sn']] = $v['nums'];
        }

        $all_color = Yii::$app->cache->get("app-get-color_id-by-purchase-id-" . $model_sn);
        if (!$all_color) {
            $query = self::find()->select(['color_id'])
                ->where(['model_sn' => $model_sn]);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id' => $purchase_id]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $all_color = $query->andWhere(['disabled' => 'false'])
                ->groupBy('color_id')
                ->asArray()
                ->all();

            Yii::$app->cache->set("app-get-color_id-by-purchase-id-" . $model_sn, $all_color, 3600);
        }
        $arr = [];

        foreach ($all_color as $v) {
            $product_info['color_id'] = $v['color_id'];
            $product_info['color_name'] = $color_list[$v['color_id']];
            foreach ($size_tran_list as &$val) {
                $items[$val['size_id']]['product_sn'] = isset($product_list[$val['size_id'] . "_" . $v['color_id']]['product_sn']) ? $product_list[$val['size_id'] . "_" . $v['color_id']]['product_sn'] : 0;
                $items[$val['size_id']]['size_id'] = $val['size_id'];
                $items[$val['size_id']]['size_name'] = $size_list[$val['size_id']];
                $items[$val['size_id']]['product_id'] = isset($product_list[$val['size_id'] . "_" . $v['color_id']]['product_id']) ? $product_list[$val['size_id'] . "_" . $v['color_id']]['product_id'] : 0;
                if (isset($items[$val['size_id']]['product_sn']) && !empty($items[$val['size_id']]['product_sn'])) {
                    $items[$val['size_id']]['nums'] = isset($item[$items[$val['size_id']]['product_sn']]) ? $item[$items[$val['size_id']]['product_sn']] : 0;
                } else {
                    $items[$val['size_id']]['nums'] = 0;
                }
            }
            ksort($items);
            $product_info['size_list'] = array_values($items);
            $arr[] = $product_info;
        }
        return array_values($arr);
    }

    /**
     * use 
     * api/default/order-product
     *
     * 检查用户是否存在
     * @param  [type] $customer    [description]
     * @param  [type] $purchase_id [description]
     * @return [type]              [description]
     */
    public function checkThisCustomerIsValid($customer, $purchase_id)
    {
        $result = (new Query)->from('meet_customer')
            ->where(['customer_id' => $customer])
            ->andWhere(['purchase_id' => $purchase_id])
            ->andWhere(['disabled' => 'false'])
            ->one();
        return $result;
    }
    /**
     * use
     * api/default/OrderProduct
     * 
     * @param  [type] $model_sn    [description]
     * @param  [type] $purchase_id [description]
     * @param  [type] $customer_id [description]
     * @return [type]              [description]
     */
    public function getOnlyBroughtModel($model_sn, $purchase_id, $customer_id)
    {
        $size_list = $this->tableValue('size', 'size_name', 'size_id');
        $color_list = $this->tableValue('color', 'color_name', 'color_id');

        $product_list = Yii::$app->cache->get("app-get-size-color-product_sn-" . $model_sn);
        if (!$product_list) {
            $query = self::find()->select(['size_id', 'color_id', 'product_sn', 'product_id'])
                ->where(['model_sn' => $model_sn]);
            if ($params['purchase_id'] == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id', [1,2]]);
            }else{
                $query->andWhere(['purchase_id' => $params['purchase_id']]);
            }
            $res = $query->andWhere(['disabled' => 'false'])
                ->asArray()
                ->all();

            foreach ($res as $v) {
                $product_list[$v['size_id'] . "_" . $v['color_id']] = $v;
            }
            Yii::$app->cache->set("app-get-size-color-product_sn-" . $model_sn, $product_list, 3600);
        }

        $size_tran_list = Yii::$app->cache->get("app-get-size_id-by-purchase-id-" . $model_sn);
        if (!$size_tran_list) {
            $query = self::find()->select(['size_id'])
                ->where(['model_sn' => $model_sn]);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id', [1,2]]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $size_tran_list = $query->andWhere(['disabled' => 'false'])
                ->groupBy('size_id ')
                ->asArray()
                ->indexBy('size_id')
                ->all();
            Yii::$app->cache->set("app-get-size_id-by-purchase-id-" . $model_sn, $size_tran_list, 3600);
        }

        $customers_product = (new Query)->select(['i.nums', 'i.product_sn'])
            ->from('meet_order_items as i')
            ->leftJoin('meet_order as o', 'o.order_id=i.order_id')
            ->where(['o.customer_id' => $customer_id])
            ->andWhere(['i.disabled' => 'false'])
            ->all();
        foreach ($customers_product as $v) {
            $item[$v['product_sn']] = $v['nums'];
        }

        $all_color = Yii::$app->cache->get("app-get-color_id-by-purchase-id-" . $model_sn);
        if (!$all_color) {
            $query = self::find()->select(['color_id', 'model_sn', 'name', 'wave_id', 'cost_price'])
                ->where(['model_sn' => $model_sn]);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['in', 'purchase_id', [1,2]]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $all_color = $query->andWhere(['disabled' => 'false'])
                ->groupBy('color_id')
                ->asArray()
                ->all();
            Yii::$app->cache->set("app-get-color_id-by-purchase-id-" . $model_sn, $all_color, 3600);
        }
        $arr = array();
        $order_num = 0;
        foreach ($all_color as $v) {
            $product_info['color_id'] = $v['color_id'];
            $product_info['color_name'] = $color_list[$v['color_id']];
            $num = 0;
            foreach ($size_tran_list as &$val) {
                $items[$val['size_id']]['product_sn'] = isset($product_list[$val['size_id'] . "_" . $v['color_id']]['product_sn']) ? $product_list[$val['size_id'] . "_" . $v['color_id']]['product_sn'] : 0;
                $items[$val['size_id']]['size_id'] = $val['size_id'];
                $items[$val['size_id']]['size_name'] = $size_list[$val['size_id']];
                $items[$val['size_id']]['product_id'] = isset($product_list[$val['size_id'] . "_" . $v['color_id']]['product_id']) ? $product_list[$val['size_id'] . "_" . $v['color_id']]['product_id'] : 0;
                if (isset($items[$val['size_id']]['product_sn']) && !empty($items[$val['size_id']]['product_sn'])) {
                    $items[$val['size_id']]['nums'] = isset($item[$items[$val['size_id']]['product_sn']]) ? $item[$items[$val['size_id']]['product_sn']] : 0;
                } else {
                    $items[$val['size_id']]['nums'] = 0;
                }
                $num += $items[$val['size_id']]['nums'];
                $order_num += $items[$val['size_id']]['nums'];
            }
            if ($num == 0) {
                continue;
            } else {
                ksort($items);
                $product_info['nums'] = $num;
                $product_info['size_list'] = array_values($items);
                $arr[] = $product_info;
            }
        }
        $result['product_list'] = array_values($arr);
        $result['order_num'] = $order_num;
        return $result;
    }

    /**
     *  use
     *  api/default/OrderThisProduct
     *  
     * 此流水号的商品规格参数
     * @param  [type] $serial_num  [description]
     * @param  [type] $purchase_id [description]
     * @param  [type] $customer_id [description]
     * @return [type]              [description]
     */
    public function getThisProductInfo($serial_num, $purchase_id, $customer_id)
    {
        $data = self::find()->select(['name', 'model_sn', 'wave_id', 'cost_price'])
            ->where(['serial_num' => $serial_num])
            ->andWhere(['disabled' => 'false'])
            ->asArray()
            ->one();

        if (empty($data)) {
            return [];
        }
        
        $data['product_list'] = $this->getProductListsInfo($result['model_sn'], $purchase_id, $customer_id);
        $data['order_num'] = $this->getThisModelOrdered($customer_id, $result['model_sn']);
        $have = $this->getThisModelOrdered($customer_id, $result['model_sn'], $serial_num);
        if ($have >= 1) {
            $data['is_order'] = 1;
        } else {
            $data['is_order'] = 2;
        }
        return $data;
    }
    /**
     * use
     * api/default/OrderListSheet
     * 
     * 按季节排序
     * @param $orderList
     * @return mixed
     */
    public function orderSeasonTable($orderList)
    {
        //配置第一个季节ID
        $season_spring_one = Yii::app()->params['season_one'];
        //配置第二个季节ID
        $season_spring_two = Yii::app()->params['season_two'];

        $listProducts = $this->productListCache();
        $all_list = array();
        foreach ($listProducts as $list) {
            $all_list[$list['product_id']] = $list;
        }

        $big = $this->tableValue('cat_big', 'cat_name', 'big_id');
        $small = $this->tableValue('cat_small', 'cat_name', 'small_id');

        $total = 0;
        $amount = 0;
        $season_spring = 0;
        $season_summer = 0;
        $model_num = array();

        foreach ($orderList['item_list'] as $trans) {
            if (!isset($all_list[$trans['product_id']])) continue;
            $product_items = $all_list[$trans['product_id']];
            $cat_big = $product_items['cat_b'];
            $cat_small = $product_items['cat_s'];
            //大类id
            $item[$cat_big]['cat_id'] = $cat_big;

            //大类名称
            $item[$cat_big]['cat_name'] = $big[$cat_big];

            //小类ID
            $season[$cat_small]['cat_id'] = $cat_small;
            $lists['cat_id'] = $season[$cat_small]['cat_id'];

            //小类名称
            $season[$cat_small]['cat_name'] = $small[$cat_small];
            $lists['cat_name'] = $season[$cat_small]['cat_name'];

            $model_num[] = $trans['model_sn'];

            //小类款号数量
            $season[$cat_big."_".$cat_small]['model_sn'][] = $trans['model_sn'];
            $season[$cat_big."_".$cat_small]['model_sn_nums'] = count(array_unique($season[$cat_big."_".$cat_small]['model_sn']));
            $lists['model_count'] = $season[$cat_big."_".$cat_small]['model_sn_nums'];

            //season_id = 1数量
            if (!isset($season[$cat_big."_".$cat_small]['spring'])) $season[$cat_big."_".$cat_small]['spring'] = 0;
            if (!isset($item[$cat_big]['season_spring_count'])) $item[$cat_big]['season_spring_count'] = 0;
            if ($product_items['season_id'] == $season_spring_one) {
                $season_spring += $trans['nums'];
                $season[$cat_big."_".$cat_small]['spring'] += $trans['nums'];
                $item[$cat_big]['season_spring_count'] += $trans['nums'];
            }
            $lists['season_spring'] = $season[$cat_big."_".$cat_small]['spring'];

            //season_id = 2数量
            if (!isset($season[$cat_big."_".$cat_small]['summer'])) $season[$cat_big."_".$cat_small]['summer'] = 0;
            if (!isset($item[$cat_big]['season_summer_count'])) $item[$cat_big]['season_summer_count'] = 0;
            if ($product_items['season_id'] == $season_spring_two) {
                $season_summer += $trans['nums'];
                $season[$cat_big."_".$cat_small]['summer'] += $trans['nums'];
                $item[$cat_big]['season_summer_count'] += $trans['nums'];
            }
            $lists['season_summer'] = $season[$cat_big."_".$cat_small]['summer'];

            //小类总件数
            if (!isset($season[$cat_big."_".$cat_small]['cat_nums'])) $season[$cat_big."_".$cat_small]['cat_nums'] = 0;
            $season[$cat_big."_".$cat_small]['cat_nums'] += $trans['nums'];
            $lists['cat_nums'] = $season[$cat_big."_".$cat_small]['cat_nums'];
            $total += $trans['nums'];

            //小类总金额
            if (!isset($season[$cat_big."_".$cat_small]['amount'])) $season[$cat_big."_".$cat_small]['amount'] = 0;
            $season[$cat_big."_".$cat_small]['amount'] += $trans['amount'];
            $lists['cat_amount'] = $season[$cat_big."_".$cat_small]['amount'];
            $amount += $trans['amount'];

            //大类总件数
            if (!isset($item[$cat_big]['all_num'])) $item[$cat_big]['all_num'] = 0;
            $item[$cat_big]['all_num'] += $trans['nums'];

            //大类总金额
            if (!isset($item[$cat_big]['all_amount'])) $item[$cat_big]['all_amount'] = 0;
            $item[$cat_big]['all_amount'] += $trans['amount'];
            $item[$cat_big]['all_list'][$cat_small] = $lists;
        }
        //总体
        $products['total'] = $total;
        $products['total_percent'] = "100%";
        $products['amount'] = $amount;
        $products['amount_percent'] = "100%";
        $products['spring'] = $season_spring;
        $products['summer'] = $season_summer;
        $products['model'] = count(array_unique($model_num));

        if (empty($item)) return array();

        //再算占比
        $productItem = array();
        foreach ($item as $cat_b) {
            $all_model = 0;
            $cat_list = array();
            foreach ($cat_b['all_list'] as $cat_s) {

                //大类款号总数
                $all_model += $cat_s['model_count'];

                //小类数量占比
                if (empty($products['total'])) {
                    $cat_s['cat_num_percent'] = 0;
                } else {
                    $cat_s['cat_num_percent'] = (round($cat_s['cat_nums'] / $products['total'], 3) * 100) . "%";
                }

                //小类金额占比
                if (empty($products['amount'])) {
                    $cat_s['cat_amount_percent'] = 0;
                } else {
                    $cat_s['cat_amount_percent'] = (round($cat_s['cat_amount'] / $products['amount'], 3) * 100) . "%";
                }

                $cat_list[] = $cat_s;
            }
            unset($cat_b['all_list']);
            if (empty($products['total'])) {
                $cat_b['all_num_percent'] = 0;
            } else {
                $cat_b['all_num_percent'] = (round($cat_b['all_num'] / $products['total'], 3) * 100) . "%";
            }
            if (empty($products['amount'])) {
                $cat_b['all_amount_percent'] = 0;
            } else {
                $cat_b['all_amount_percent'] = (round($cat_b['all_amount'] / $products['amount'], 3) * 100) . "%";
            }
            $cat_b['all_list'] = $cat_list;
            $cat_b['all_model'] = $all_model;
            $productItem[] = $cat_b;
        }
        $info['all_list'] = $productItem;
        $info['total_list'] = $products;
        return $info;
    }
    /**
     * use
     * api/default/OrderListSheet
     * 
     * 根据价格带
     * @param $orderList
     * @return array
     */
    public function orderPriceTable($orderList)
    {
        $listProducts = $self->productListCache();
        foreach ($listProducts as $list) {
            $all_list[$list['product_id']] = $list;
        }

        $big = $this->tableValue('cat_big', 'cat_name', 'big_id');
        $priceList = $this->getThisCostPrice();
        $totals = 0;
        $amount = 0;
        $model = array();
        foreach ($orderList['item_list'] as $v) {
            if (!isset($all_list[$v['product_id']])) continue;
            //购买的商品信息
            $productInfo = $all_list[$v['product_id']];

            $item[$productInfo['cat_b']]['cat_id'] = $productInfo['cat_b'];
            //大类名称
            $item[$productInfo['cat_b']]['cat_name'] = $big[$productInfo['cat_b']];

            //价格带的款数
            $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['model_sn'][] = $v['model_sn'];
            $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['model_count'] = count(array_unique($price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['model_sn']));

            //大类总款数
            $item[$productInfo['cat_b']]['model_sn'][] = $v['model_sn'];

            //大类总数量
            if (!isset($item[$productInfo['cat_b']]['all_num'])) $item[$productInfo['cat_b']]['all_num'] = 0;
            $item[$productInfo['cat_b']]['all_num'] += $v['nums'];

            $totals += $v['nums'];
            $amount += $v['amount'];
            $model[] = $v['model_sn'];
            //大类总金额
            if (!isset($item[$productInfo['cat_b']]['all_amount'])) $item[$productInfo['cat_b']]['all_amount'] = 0;
            $item[$productInfo['cat_b']]['all_amount'] += $v['amount'];

            //此价格带的数量
            if (!isset($price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_nums'])) $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_nums'] = 0;
            $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_nums'] += $v['nums'];

            //此价格带的金额
            if (!isset($price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_amount'])) $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_amount'] = 0;
            $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_amount'] += $v['amount'];

            //此价格带的名称
            $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]]['cat_name'] = $priceList[$productInfo['price_level_id']];

            $item[$productInfo['cat_b']]['all_list'][$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]] = $price[$productInfo['cat_b'].$priceList[$productInfo['price_level_id']]];
        }
        $total['total'] = $totals;
        $total['amount'] = $amount;
        $total['total_percent'] = "100%";
        $total['amount_percent'] = "100%";
        $total['model'] = count(array_unique($model));
        $total['model_percent'] = "100%";

        if (empty($item)) return array();

        foreach ($item as $val) {
            $val['all_model'] = count(array_unique($val['model_sn']));
            unset($val['model_sn']);
            $cat_list = array();
            foreach ($val['all_list'] as $info) {
                unset($info['model_sn']);
                if (empty($total['model'])) {
                    $info['cat_model_percent'] = 0;
                } else {
                    $info['cat_model_percent'] = (round($info['model_count'] / $total['model'], 3) * 100) . "%";
                }

                if (empty($total['amount'])) {
                    $info['cat_amount_percent'] = 0;
                } else {
                    $info['cat_amount_percent'] = (round($info['cat_amount'] / $total['amount'], 3) * 100) . "%";
                }

                if (empty($total['total'])) {
                    $info['cat_num_percent'] = 0;
                } else {
                    $info['cat_num_percent'] = (round($info['cat_nums'] / $total['total'], 3) * 100) . "%";
                }
                $cat_list[] = $info;
            }
            if (empty($total['total'])) {
                $val['all_num_percent'] = 0;
            } else {
                $val['all_num_percent'] = (round($val['all_num'] / $total['total'], 3) * 100) . "%";
            }
            if (empty($total['model'])) {
                $val['all_model_percent'] = 0;
            } else {
                $val['all_model_percent'] = (round($val['all_model'] / $total['model'], 3) * 100) . "%";
            }
            if (empty($total['amount'])) {
                $val['all_amount_percent'] = 0;
            } else {
                $val['all_amount_percent'] = (round($val['all_amount'] / $total['amount'], 3) * 100) . "%";
            }
            unset($val['all_list']);
            $val['all_list'] = $cat_list;
            $result[] = $val;
        }
        $results['all_list'] = $result;
        $results['total_list'] = $total;
        return $results;
    }
    /**
     * 获取该价格的价格带
     */
    public function getThisCostPrice()
    {
        return array(
            '1' => '0-99',
            '2' => '100-199',
            '3' => '200-299',
            '4' => '300-399',
            '5' => '400-499',
            '6' => '500-999',
            '7' => '1000-1499',
            '8' => '1500-2000',
            '9' => '2000以上',
        );
    }

    /**
     * use
     * api/default/SubmitOrder
     * 
     * 检查此用户是否是提交了
     *
     * @param $purchase_id
     * @param $customer_id
     * @return string
     */
    public function checkThisSubmit($purchase_id, $customer_id)
    {
        $query = (new Query)->select(['status'])
            ->from('meet_order')
            ->where(['customer_id' => $customer_id]);
        // 只有查询商品的时候需要
        // if ($purchase_id == Yii::$app->params['purchaseAB']) {
        //     $query->andWhere(['in', 'purchase_id' => $purchase_id]);
        // }else{
        //     $query->andWhere(['purchase_id' => $purchase_id]);
        // }
        $res = $query->andWhere(['purchase_id' => $purchase_id])
            ->andWhere(['disabled' => 'false'])
            ->one();
   
        if (empty($res)) $res['status'] = "active";
        return $res['status'];
    }
}
