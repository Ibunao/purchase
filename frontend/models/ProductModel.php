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
            ->leftJoin('meet_cat_middle as m', 'm.middle_id= p.cat_m')
            ->leftJoin('meet_cat_big_small as s', 'p.cat_s=s.small_id');
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
            //源代码没有添加条件，可放置则不用放在if里面
            // $result['catMiddle'] = (new Query)->select(['middle_id', 'cat_name'])
            //     ->from('meet_cat_middle')
            //     ->where(['parent_id' => $data['cat_b']])
            //     ->all();
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
     * 修改商品操作
     * @param $param
     * @param $moreData
     * @param $lessData
     * @param $serialNum
     * @return bool
     */
    /**
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
        $sql_add = "";

        //新增尺码数据
        if (!empty($moreData)) {
            $sql_add .= $this->_addOnlyAddProducts($param, $moreData, $serialNum, $purchaseId);
        }

        //下架该尺码
        if (!empty($lessData)) {
             $this->_updateProducts($lessData, $serialNum);
        }

        //执行上面返回的sql
        if (!empty($sql_add)) {
            $this->ModelExecute($sql_add);
        }

        //修改其他商品基本数据
        if ($this->_updateAllSerialNumProduct($param, $serialNum)) {
            return true;
        } else {
            return false;
        }
    }
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
        if(empty($param['type'])){
            $param['type'] = 0;
        }
        // 色号转换。
        $color_no = (new ColorModel)->select(['color_no'])
            ->where(['color_id' => $param['color_id']])
            ->asArray()
            ->one();
        //当上传图片为空，给定默认值
        if (empty($param['image'])) {
            $param['image'] = "/images/" . $param['modelSn'] . "_" . $color_no . ".jpg";
        }
        //检验param是否该填的都填了，前端判断了这里就可以不用判断了  


        //款号
        $style_sn = $param['modelSn'] . sprintf("%04d", $color_no);

        //查询此款款号的最大货号（以便生成新的货号）
    }

    /**
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
    }
    /**
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
            $row = self::find()->select(['style_sn'])
                ->where(['like', 'model_sn', $serial.'%', false])//右模糊
                ->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => 0])
                ->andWhere(['purchase_id' => $params['purchase_id']])
                ->distinct()
                ->all();

            if (empty($row)) return [];
            //根据查询出的款号 和 搜索条件 获取商品的详细信息
            $items = $this->listStyleSn($row, $params, $conArr);
        }else{
            if (!empty($serial)) {
                //流水号
                $row = self::find()->select(['style_sn'])
                ->where(['serial_num', $serial])
                ->andWhere(['disabled' => 'false'])
                ->andWhere(['is_down' => 0])
                ->andWhere(['purchase_id' => $params['purchase_id']])
                ->distinct()
                ->all();

                if (empty($row)) return [];
                $items = $this->listStyleSn($row, $params, $conArr);
            }else{

                $style_sn = '';
                $items = $this->listSerial($style_sn, $params, $conArr);
            }
        }
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
            array_multisort($hits_sort, $sort2, $items);
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
        //从数组中取出指定分页需要的数据
        return array_slice($items, ($page - 1) * $pagesize, $pagesize);
    }

    /**
     * 缓存指定订购会所有产品 包括下架的 并根据流水号合并
     * @return [type] [description]
     */
    public function productListCache()
    {
        $purchaseId = Yii::$app->session->get('purchase_id');
        $list = Yii::$app->cache->get('all-product-list-without-down-' . $purchaseId);
        if (empty($list)) {
            $list = self::find()
                ->where(['purchase_id' => $purchaseId])
                ->andWhere(['disabled' => 'false'])
                ->orderBy(['serial_num' => SORT_ASC])
                ->asArray()
                ->all();
            Yii::$app->cache->set('all-product-list-without-down-' . $purchaseId, $list, 86400);
        }
        return $list;
    }




}
