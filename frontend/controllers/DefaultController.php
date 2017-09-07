<?php
namespace frontend\controllers;

use Yii;
use frontend\controllers\base\FBaseController;
use frontend\models\ProductModel;
/**
 * 首页controller
 */
class DefaultController extends FBaseController
{
    public $purchase;
    public $brand;
    public $color;
    public $size;
    public $cat_b;
    public $cat_m;
    public $cat_s;
    public $season;
    public $level;
    public $scheme;
    public $wave;
    public $type;
    public $parice_level;
    public $route = 'default/index';

    public function init()
    {
        parent::init();
        $productModel = new ProductModel();
        $this->purchase = $productModel->tableValue('purchase', 'purchase_name', 'purchase_id');
        $this->brand = $productModel->tableValue('brand', 'brand_name', 'brand_id');
        $this->cat_b = $productModel->tableValue('cat_big', 'cat_name', 'big_id');
        $this->cat_m = $productModel->tableValue('cat_middle', 'cat_name', 'middle_id');
        $this->cat_s = $productModel->tableValue('cat_small', 'cat_name', 'small_id');
        $this->wave = $productModel->tableValue('wave', 'wave_name', 'wave_id');
        $this->scheme = $productModel->tableValue('scheme', 'scheme_name', 'scheme_id');
        $this->season = $productModel->tableValue('season', 'season_name', 'season_id');
        $this->color = $productModel->tableValue('color', 'color_name', 'color_id');
        $this->size = $productModel->tableValue('size', 'size_name', 'size_id');
        $this->level = $productModel->tableValue('level', 'level_name', 'level_id');
        $this->type = $productModel->tableValue('type', 'type_name', 'type_id');
        $this->parice_level = array(
            1 => '0-99',
            2 => '100-199',
            3 => '200-299',
            4 => '300-399',
            5 => '400-499',
            6 => '500-999',
            7 => '1k-1.5k',
            8 => '1.5k-2k',
            9 => '2k以上',
        );

    }
    /**
     * 
     * 示例
     * ?c_id=1,12&sd=2_夏&wv=16_冬四&lv=4_基本款&plv=2_100-199&or=1&hits=1
     * @return [type] [description]
     */
    public function actionIndex()
    {
    	$request = Yii::$app->request;
    	//页码
    	$page = $request->get('page', 1);
    	$c_ids = $request->get('c_id');	//分类ID 大分类,小分类 的格式
    	$sd = $request->get('sd');		//季节
    	$wv = $request->get('wv');		//波段
    	$lv = $request->get('lv');		//等级
    	$plv = $request->get('plv');	//价格带
    	$or = $request->get('or', '');		//已订/未订
    	$price = $request->get('price', '');	//价格升降排序
    	$hits = $request->get('hits', '');		//人气升降排序
    	$serialNum = $request->get('serial_num', '');	//输入搜索

    	//搜索条件
    	$conArr = $model = [];
    	//小分类 大分类
    	$c_id = $b_id = 0;
    	if ($c_ids) {
    	    $cat_arr = explode(',', $c_ids);
            //大分类
    	    if (isset($cat_arr[0])) $b_id = $cat_arr[0];
            //小分类
    	    if (isset($cat_arr[1])) $c_id = $cat_arr[1];

    	    if ($c_id) {
    	        $conArr[] = 's_id_' . $b_id;
    	        $conArr[] = 'c_id_' . $c_id;
    	    } elseif ($b_id) {
    	        $conArr[] = 's_id_' . $b_id;
    	    }

    	}
    	if ($sd) {
    	    $sdArr = explode('_', $sd);
    	    $conArr[] = 'sd_' . $sdArr[0];
    	    $model['sd'] = $sdArr[1];
    	}
    	if ($wv) {
    	    $wvArr = explode('_', $wv);
    	    $conArr[] = 'wv_' . $wvArr[0];
    	    $model['wv'] = $wvArr[1];
    	}
    	if ($lv) {
    	    $lvArr = explode('_', $lv);
    	    $conArr[] = 'lv_' . $lvArr[0];
    	    $model['lv'] = $lvArr[1];
    	}
    	if ($plv) {
    	    $plvArr = explode('_', $plv);
    	    $conArr[] = 'plv_' . $plvArr[0];
    	    $model['plv'] = $plvArr[1];
    	}

    	$productModel = new ProductModel;
        //搜索条件
    	$params = [
    	    'or' => $or,
    	    'purchase_id' => $this->purchaseId,
    	    'customer_id' => $this->customerId,
    	    'hits' => $hits,
    	];
    	//一个用户的订单状态，根据状态改变预定按钮的状态
    	$res=$productModel->checkStatus($params['customer_id']);

        //获取搜索的商品
        $model['list'] = $productModel->newitems($conArr, $serialNum, $params, $price, $page);
    	$model['c_id'] = $c_id;
    	$model['price'] = $price;
    	$model['price_f'] = $price == 1 ? 2 : 1;
    	$model['hits'] = $hits;
    	$model['hits_f'] = $hits == 1 ? 2 : 1;
    	$model['or'] = $or;
// var_dump($model['list']);exit;
    	if ($page > 1) {
    	    return $this->renderPartial('ajaxindex', array('model' => $model, 'c_id' => $c_id, 'b_id' => $b_id,'res'=>$res));
    	} else {
    	    return $this->render('index', 
    	    	[
	    	    	'model' => $model, 
	    	    	'c_id' => $c_id, 
	    	    	'b_id' => $b_id, 
	    	    	'serial_num' => $serialNum,
	    	    	'res'=>$res
    	    	]);
    	}
    }
    /**
     * 商品详情
     */
    public function actionDetail()
    {
        $model_sn = Yii::$app->request->post('model_sn');
        $productModel = new ProductModel;
        $list = $productModel->listModelCache($model_sn);
        $purchaseId = $this->purchaseId;
        $customerId = $this->customerId;
        $order = $productModel->getThisOrderedInfo($purchaseId, $customerId, $model_sn);
        $total = 0;
        if (isset($order) && $order) {
            foreach ($order as $v) {
                if ($v['model_sn'] == $model_sn) $total += $v['nums'];
            }
        }
        // var_dump(array('list' => $list, 'total' => $total, 'order_items'=>$order));exit;
        return $this->renderPartial('detail', array('list' => $list, 'total' => $total, 'order_items'=>$order));
    }
}