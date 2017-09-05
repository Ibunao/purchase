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

    public $layout = 'frondent';
    //关闭csrf
    public $enableCsrfValidation = false;


    public $totalNum;//订单总数量
    public $amount;//订单总价格
    public $orderState;//订单状态

    public $customerId;//用户id
    public $purchaseId;//订购会id
	public function init()
	{
		parent::init();
        //未登录跳转登陆
        if (empty(Yii::$app->session->get('purchase_id'))){
            $this->redirect(['/user/index'])->send();
            exit;
        }
        $this->customerId = Yii::$app->session->get('customer_id');
        $this->purchaseId = Yii::$app->session->get('purchase_id');
        $this->orderTotal();
	}
    /**
     * 获取订单详情
     */
    public function orderTotal()
    {
        $orderModel = new OrderModel;
        $items = $orderModel->orderItems($this->purchaseId, $this->customerId);
        $this->totalNum = isset($items['order_row']['total_num'])?$items['order_row']['total_num']:0;
        $this->amount = isset($items['order_row']['cost_item'])?$items['order_row']['cost_item']:'0.00';
        $this->orderState = isset($items['order_row']['status'])?$items['order_row']['status']:'active';
    }
    /**
     * 生成URL
     *
     * @param array $params
     * @return string
     */
    public function urlParams($params=array())
    {
        $request = Yii::$app->request;
        $c_id = isset($params['c_id'])?$params['c_id']:$request->get('c_id', '');
        $sd = isset($params['sd'])?$params['sd']:$request->get('sd', '');
        $wv = isset($params['wv'])?$params['wv']:$request->get('wv', '');
        $lv = isset($params['lv'])?$params['lv']:$request->get('lv', '');
        $plv = isset($params['plv'])?$params['plv']:$request->get('plv', '');
        $or = isset($params['or'])?$params['or']:$request->get('or', '');
        $price = isset($params['price'])?$params['price']:$request->get('price', '');
        $hits = isset($params['hits'])?$params['hits']:$request->get('hits', '');
        $serial_num = isset($_GET['serial_num'])?$_GET['serial_num']:'';
        $page = isset($_GET['page'])?$_GET['page']:1;


        $arr = array();
        if ($c_id) $arr[] = "c_id=$c_id";
        if ($sd) $arr[] = "sd=$sd";
        if ($wv) $arr[] = "wv=$wv";
        if ($lv) $arr[] = "lv=$lv";
        if ($plv) $arr[] = "plv=$plv";
        if ($or) $arr[] = "or=$or";
        if ($price) $arr[] = "price=$price";
        if ($hits) $arr[] = "hits=$hits";
        if($serial_num) $arr[] = "serial_num=".$serial_num;
        if (isset($params['next']) && $params['next'] == 1 && $page) {
            $nextpage = $page + 1;
            $arr[] = "page=$nextpage";
        }

        $url='/'.$this->route.'/';
        return $url.'?'.implode('&',$arr);
    }
}