<?php
namespace frontend\modules\order\controllers;

use Yii;
use yii\db\Query;
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
        //获取筛选条件数据 下拉框
		$selectFilter = $productModel->getIndexFilter($param);
		//查询结果
		$resultData = $productModel->productSearch($param);

		//检查是否有错误  
		// $error = $productModel->isHaveError();

		return $this->render('index', [
				'param' => $param,
				'selectFilter' => $selectFilter,      //下拉框自带参数
	            'select_option' => $resultData['result'],        //显示搜索的结果
	            'pagination' => $resultData['pagination'],
	            // 'is_error' => $error
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
                //使用setFlash
                Yii::$app->session->setFlash('info', '添加成功');
                $this->redirect("/order/product/index");
            } else {
                Yii::$app->session->setFlash('error', '添加失败');
            }

        }
        $result = $productModel->getProductFilter();
        return $this->render('add', [
            'selectFilter' => $result
        ]);
    }
    /**
     * 商品更新
     * @return [type] [description]
     */
    public function actionUpdate()
    {
        $productModel = new ProductModel;
        $request = Yii::$app->request;
        $serialNum = $request->get("serial_num");
        $purchaseId = $request->get('purchase_id');
        if (empty($serialNum)) {
            echo "没有流水号";
            die;
        }

        //该流水号的商品信息
        $param = (new Query)->select(['p.*', 's.group_id AS sizeGroup'])
            ->from('meet_product as p')
            ->leftJoin('meet_size as s', 's.size_id = p.size_id')
            ->where(['p.serial_num' => $serialNum])
            ->andWhere(['p.disabled' => 'false'])
            ->andWhere(['p.purchase_id' => $purchaseId])
            ->one();


        //size 已选尺码
        $param['size'] = [];
        //该商品存在的尺码
        $paramSize = (new Query)->select(['size_id', 'product_sn'])
            ->from('meet_product')
            ->where(['serial_num' => $serialNum])
            ->andWhere(['disabled' => 'false'])
            ->andWhere(['purchase_id' => $purchaseId])
            ->groupBy('size_id')
            ->all();
        foreach ($paramSize as $val) {
            $param['size'][] = $val['size_id'];
        }

        //自带下拉列表 每个字段所有可选的值
        $result = $productModel->getProductFilter($param);

        //post数据
        $postParam = Yii::$app->request->post("param", []);
        if (!empty($postParam)) {
            //新多出的size数据
            $moreData = array_diff($postParam['size'], $param['size']);
            //少了的size数据
            $lessData = array_diff($param['size'], $postParam['size']); 
            $res = $productModel->updateProductOperation($postParam, $moreData, $lessData, $serialNum, $purchaseId);
            $this->_clear();
            if ($res) {
                //跳转到首页
                Yii::$app->session->setFlash('info', '修改成功');
                $this->redirect(['/order/product/index']);
            } else {
                //跳转到首页
                Yii::$app->session->setFlash('info', '此款号出现多个货号，禁止修改');
                $this->redirect(['/order/product/update', 'serial_num' => $serialNum]);
            }
        }
        return $this->render('update', [
            'selectFilter' => $result,
            'param' => $param,
        ]);

    }

}