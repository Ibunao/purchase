<?php

namespace frontend\modules\order\controllers;

use Yii;
use yii\db\Query;
use frontend\config\ParamsClass;
use frontend\controllers\base\BaseController;
use frontend\models\PurchaseModel;
use frontend\models\ProductModel;
use frontend\models\CustomerModel;
use frontend\models\OrderModel;
use frontend\helpers\IoXls;

/**
 * 下载订单
 * @author dingran
 * @date(2017.8.15)
 */
class OrderController extends BaseController
{
	public function actionIndex()
	{
		$select_option = $this->filter();
		$request = Yii::$app->request;
		$pageIndex = $request->get('page', 1);
		$params = $request->get('param', []);
		$params['page'] = $pageIndex;

		$orderModel = new OrderModel;
		// 查询订单  
		$result = $orderModel->orderQueryList($params);
		var_dump($result['list']);
	}

	/**
	 * 筛选框选项
	 * @return [type] [description]
	 */
	private function filter()
	{
		//订货会：
        $select_option['purchase'] = PurchaseModel::getPurchase();

        // 客户类型
        $select_option['customer_type'] = CustomerModel::getList(['type'], ['type']);
        // 大区
        $select_option['customer_area'] = CustomerModel::getList(['area'], ['area']);
        // 部门类型
        $select_option['customer_department'] = CustomerModel::getList(['department'], ['department']);
        // 负责人
        $select_option['customer_leader'] = CustomerModel::getList(['leader'], ['leader']);

        return $select_option;
	}
}
