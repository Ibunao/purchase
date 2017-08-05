<?php
namespace frontend\modules\order\controllers;

use Yii;
use frontend\controllers\base\BaseController;
use frontend\models\CustomerModel;
use frontend\models\ProductModel;
use PHPExcel;
use PHPExcel_IOFactory;
/**
 * 商品管理
 * @author        ding
 */
class ProductController extends BaseController
{
	public function actionIndex()
	{
		$request = Yii::$app->request;
		$pageIndex = $request->get('page', 1);
		$param = $request->get('param', []);
		$productModel = new ProductModel;
		$selectFilter = $productModel->getIndexFilter($param);
		//查询结果
		$resultData = $productModel->productSearch($param);

		//检查是否有错误  
		$error = $productModel->isHaveError();

		return $this->render('index', [
				'param' => $param,
				'selectFilter' => $selectFilter,      //下拉框自带参数
	            'select_option' => $resultData['result'],        //显示搜索的结果
	            'pagination' => $resultData['pagination'],
	            'is_error' => $error
			]);
	}

	 /**
     * 单个商品添加
     */
    public function actionAdd()
    {
        $productModel = new ProductModel();
        $customerModel = new CustomerModel();

        $param = Yii::$app->request->post('param', []);
        if (!empty($param)) {
            $res = $productModel->addProductOperation($param);
            if ($res) {
                $this->_clear();
                $guestModel->breakAction('添加成功', "/admin.php?r=order/product/index");
            } else {
                $guestModel->breakActions('添加失败');
            }

        }
        $result = $productModel->getProductFilter();
        $this->render('add', array(
            'selectFilter' => $result
        ));
    }

}