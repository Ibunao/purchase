<?php 
namespace frontend\controllers;

use frontend\controllers\base\FBaseController;
use frontend\models\OrderModel;
/**
* ce
*/
class ForderController extends FBaseController
{
	
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
            $order = $this->getCustomerOrdered($this->customerId);
            if ($order) {
                echo json_encode(array('code'=>'200','data'=>$order, 'msg'=>'订货成功'));
            } else {
                echo json_encode(array('code'=>'400', 'msg'=>'订货成功'));
            }
        }else{
            echo json_encode(array('code'=>'400', 'msg'=>'订货失败'));
        }
        
    }
}