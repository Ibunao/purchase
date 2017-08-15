<?php

namespace frontend\modules\order\controllers;

use Yii;
use yii\db\Query;
use frontend\config\ParamsClass;
use frontend\controllers\base\BaseController;
use frontend\models\PurchaseModel;
use frontend\models\ProductModel;
use frontend\models\CustomerModel;
use frontend\models\CatBigModel;
use frontend\models\CatMiddleModel;
use frontend\models\CatSmallModel;
use frontend\models\SeasonModel;
use frontend\models\WaveModel;
use frontend\models\LevelModel;
use frontend\models\SchemeModel;
use frontend\models\TypeModel;
use frontend\models\OrderModel;
use frontend\helpers\IoXls;

/**
 * 下载订单
 * @author dingran
 * @date(2017.8.14)
 */
class DownController extends BaseController
{
	public function actionIndex()
	{
		$query = new Query;
		$result = $query->select(['p.serial_num', 'p.style_sn', 'p.color_id', 'size_id', 'sum(oi.nums) as nums'])
			->from('meet_product as p')
			->leftJoin('meet_order_items as oi', 'p.product_id = oi.product_id')
			->where(['oi.disabled' => 'false'])
			->groupBy('oi.product_id')
			->all();
		var_dump($result);exit;
		$keys = ['流水号','款号','颜色','尺码','色号','订单量'];
		$filename = '商品导出筛选结果';
		$export = new IoXls();
	    $export->export_begin($keys, $filename, count($data));
	    $export->export_rows($data);
	    $export->export_rows($data2);
	    $export->export_finish();

	}
}
