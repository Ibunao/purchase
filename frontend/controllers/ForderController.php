<?php 
namespace frontend\controllers;

use Yii;
use frontend\controllers\base\FBaseController;
use frontend\models\OrderModel;
use frontend\models\ProductModel;
/**
* ce
*/
class ForderController extends FBaseController
{
	public $wave;
	/**
     *  ajax 预订
     */
    public function actionGetAllPrice()
    {

        $orderItem = isset($_POST['dt']) ? $_POST['dt'] : '非法访问';
        // var_dump($_POST);exit;
        // array (size=1)
        // 'dt' => string '|7_4|8_|9_|10_2|11_|12_'
        $orderItem = substr($orderItem, 1);
        $arr = explode("|", $orderItem);
        foreach ($arr as $k => $v) {
            $result[] = explode("_", $v);
        }
        $orderModel = new OrderModel();
        if($orderModel->addAjax($result, $this->purchaseId, $this->customerId, $this->username)){
            $order = $orderModel->getCustomerOrdered($this->customerId);

            echo json_encode(array('code'=>'200','data'=>$order, 'msg'=>'订货成功'));

        }else{
            echo json_encode(array('code'=>'400', 'msg'=>'订货失败'));
        }
        
    }
    /**
     * 季节汇总、订单统计
     */
    public function actionBycount()
    {
        $page = Yii::$app->request->get('page', 1);
        $orderModel = new OrderModel();
        $productModel = new ProductModel();
        $order = $orderModel->orderItems($this->purchaseId, $this->customerId);
        $result = $productModel->orderSprandSumItems($order['item_list']);
        // var_dump($result);exit;
        return $this->render('bycount', array('list' => $result['list'], 'result' => $result));
    }

    /**
     * 价格汇总
     */
    public function actionByprice()
    {
        $page = Yii::$app->request->get('page', 1);
        $orderModel = new OrderModel();
        $productModel = new ProductModel();
        $order = $orderModel->orderItems($this->purchaseId, $this->customerId);//已经购买的产品
        $result = $productModel->orderJiaGeDaiItems($order['item_list']);

        return $this->render('byprice', array('list' => $result['list'], 'result' => $result));
    }
    /**
     * 我的分销
     */
    public function actionBydownuser()
    {
        $orders = new OrderModel();
        $downUserInfo = $orders->getUserDownUsers($this->customerId);
        return $this->render('bydownuser',
            array(
                'downUserInfo' => $downUserInfo
            )
        );
    }

    /**
     * 订单提交
     */
    public function actionSubmit()
    {
        $orderModel = new OrderModel();
        $orderModel->orderSubmit($this->purchaseId, $this->customerId);

        $url = Yii::$app->request->getReferrer();
        $this->redirect($url);
    }
    /**
     *  订单明细
     */
    public function actionBydetail()
    {
        $page = Yii::$app->request->get('page', 1);
        $orderModel = new OrderModel();
        $productModel = new ProductModel();
        $this->wave = $productModel->tableValue('wave', 'wave_name', 'wave_id');
        $order = $orderModel->fOrderItemList($this->purchaseId, $this->customerId, $page);
        $model_items = array();
        $model_sn = array();
        $order_row = isset($order['order_row']) ? $order['order_row'] : array();
        $list = array();
        $product_num = array();
        if (isset($order['item_list']) && $order['item_list']) {
            $order_items = $order['item_list'];
            foreach ($order_items as $v) {
                if (isset($model_items[$v['model_sn']]))
                    $model_items[$v['model_sn']] += $v['nums'];
                else $model_items[$v['model_sn']] = $v['nums'];
                $product_num[$v['product_sn']] = $v['nums'];
                if (!in_array($v['model_sn'], $model_sn)) $model_sn[] = $v['model_sn'];
            }
        }
        if ($model_sn) {
            foreach ($model_sn as $v) {
                $list[] = $productModel->listModelCache($v);
            }
        }
        // var_dump($list);exit;
        return $this->render('bydetail', array('model_items' => $model_items, 'order_row' => $order_row, 'list' => $list, 'product_num' => $product_num, 'next' => $page + 1));
    }
    /**
     * 订单撤销验证
     */
    public function actionRepealcheck()
    {
        $orderModel = new OrderModel();
        $result = $orderModel->orderRepealCheck($this->purchaseId, $this->customerId);

        if ($result) echo '200';
        else echo '400';
    }
    /**
     * 订单撤销
     */
    public function actionRepeal()
    {
        $orderModel = new OrderModel();
        $orderModel->orderRepeal($this->purchaseId, $this->customerId);

        $url = Yii::$app->request->getReferrer();
        $this->redirect($url);
    }
    /**
     * ajax片段提交
     * 向下滚动bug ，拉倒最底下会一直请求数据
     */
    public function actionDetail()
    {
        $page = Yii::$app->request->get('page', 1);
        $orderModel = new OrderModel();
        $productModel = new ProductModel();
        $this->wave = $productModel->tableValue('wave', 'wave_name', 'wave_id');
        $order = $orderModel->fOrderItemList($this->purchaseId, $this->customerId, $page);
        $model_items = array();
        $model_sn = array();
        $order_row = isset($order['order_row']) ? $order['order_row'] : array();
        $list = array();
        $product_num = array();
        if (isset($order['item_list']) && $order['item_list']) {
            $order_items = $order['item_list'];
            foreach ($order_items as $v) {
                if (isset($model_items[$v['model_sn']]))
                    $model_items[$v['model_sn']] += $v['nums'];
                else $model_items[$v['model_sn']] = $v['nums'];
                $product_num[$v['product_sn']] = $v['nums'];
                if (!in_array($v['model_sn'], $model_sn)) $model_sn[] = $v['model_sn'];
            }
        }
        if ($model_sn) {
            foreach ($model_sn as $v) {
                $list[] = $productModel->listModelCache($v);
            }
        }
        // var_dump($list);exit;
        return $this->render('detail', array('model_items' => $model_items, 'order_row' => $order_row, 'list' => $list, 'product_num' => $product_num, 'next' => $page + 1));
    }
}