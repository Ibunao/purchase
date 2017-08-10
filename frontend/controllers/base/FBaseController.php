<?php

namespace frontend\controllers\base;

use Yii;
use yii\web\Controller;
use frontend\models\OrderModel;
/**
 * 前台基础控制器
 */
class FBaseController extends Controller
{

    public $layout = 'column_user';
    //关闭csrf
    public $enableCsrfValidation = false;


    public $totalNum;//订单总数量
    public $amount;//订单总价格
    public $order_state;//订单状态

    public $customerId;//用户id
    public $purcheaseId;//订购会id
	public function init()
	{
		parent::init();
		//未登录跳转登陆
        if (empty(Yii::$app->session->get('purchase_id'))){
        	$this->redirect(['/user/index'])->send();
            exit;
        } 
        $this->customerId = Yii::$app->session->get('customer_id');
        $this->purcheaseId = Yii::$app->session->get('purchase_id');
        $this->orderTotal();
	}
    /**
     * 获取订单详情
     */
    public function orderTotal()
    {
        $orderModel = new OrderModel;
        $items = $orderModel->orderItems($this->purcheaseId, $this->customerId);
        $this->totalNum = isset($items['order_row']['total_num'])?$items['order_row']['total_num']:0;
        $this->amount = isset($items['order_row']['cost_item'])?$items['order_row']['cost_item']:'0.00';
        $this->orderNtate = isset($items['order_row']['status'])?$items['order_row']['status']:'active';
    }
}