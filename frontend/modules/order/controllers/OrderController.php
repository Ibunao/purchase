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
		$statistics['amount'] = $result['amount'];
		$statistics['amount_really'] = $result['amount_really'];
		$customer = new CustomerModel;
		//总订货指标
		$statistics['target_sum'] = $customer->getCustomerTargets($params);

		//已筛选客户订货指标
		$statistics['choose_target_sum'] = $customer->getCustomerTargets($params, true);
		
		if (!empty($result['list'])) {
            foreach ($result['list'] as $k => $v) {
            	// 客户下线金额
                $result['list'][$k]['xxydhje'] = $orderModel->getAllPriceCount($v['parent_id'], $v['agent']);
                // 获取审核订单的时间和操作人
                $orderLog = $orderModel->getOrderLog($v['order_id']);
                if (!empty($orderLog)) {
                    $result['list'][$k]['check_time'] = date('Y-m-d H:i:s', $orderLog['time']);
                    $result['list'][$k]['check_user'] = $orderLog['name'];
                } else {
                    $result['list'][$k]['check_time'] = '';
                    $result['list'][$k]['check_user'] = '';
                }
            }
        }

        if (empty($params['download'])) {
        	return $this->render('index', array(
        	    'result' => $result,
        	    'params' => $params,
        	    'selectOption' => $select_option,
        	    'statistics' => $statistics
        	));
        }else{
        	//下载

        }
	}
	/**
	 * 订单复制静态页
	 * @return [type] [description]
	 */
	public function actionCopy()
	{
		return $this->render('copy');
	}
	/**
	 * 订单复制操作
	 * @return [type] [description]
	 */
	public function actionDocopy()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$from = $request->post('from');
		$to = $request->post('to');
		if (empty($from) || empty($to)) {
			return ['msg' => '请填写客户编号', 'code' => 400];
		}
		$customer = new CustomerModel;
		$order = new OrderModel;

		//确认客户信息
        $from_customer_info = $customer->getCustomerInfo($from);
        $to_customer_info = $customer->getCustomerInfo($to);
        if (empty($from_customer_info)) {
        	return ['msg' => '没有被复制客户信息', 'code' => 400];
        }
        if (empty($to_customer_info)) {
        	return ['msg' => '没有复制到客户信息', 'code' => 400];
        }
        //比较客户订货会类型
        if ($from_customer_info['purchase_id'] !== $to_customer_info['purchase_id']) {
        	return ['msg' => '两个客户类型不一致', 'code' => 400];
        }

        //获取被复制客户订单
        $from_order = $order->getCustomerOrder($from_customer_info['customer_id']);
        if (empty($from_order)) {
        	return ['msg' => '被复制客户没有订单', 'code' => 400];
        }
        //获取被复制客户订单商品
        $order_list = $order->orderItem($from_order['order_id']);
        if (empty($order_list)) {
        	return ['msg' => '被复制客户订单没有商品', 'code' => 400];
        }

        //获取复制到客户订单
        $to_order = $order->getCustomerOrder($to_customer_info['customer_id']);
        if (!empty($to_order)) {
            //获取复制到订单商品
            $to_order_list = $order->orderItem($to_order['order_id']);
            if (!empty($to_order_list)) {
            	return ['msg' => '复制到客户订单存在商品，请先删除', 'code' => 400];
            }
        }

        //添加订单
        $orderId = $order->addOrder($from_order['purchase_id'], $to_customer_info['customer_id'], $to_customer_info['name'], $from_order['cost_item']);
        if (!$orderId) {
        	return ['msg' => '复制订单失败', 'code' => 400];
        }
        //添加订单商品
        if ($order->addToOrderItem($orderId, $order_list)) {
        	return ['msg' => '复制订单成功', 'code' => 400];
        } else {
        	return ['msg' => '复制订单失败', 'code' => 400];
        }
	}
	/**
	 * 订单详情
	 * @return [type] [description]
	 */
	public function actionDetail($order_id)
	{
		$orderModel = new OrderModel;
		$orderModelSn = $orderModel->orderProductModelSn($order_id);
		if (empty($orderModelSn)) {
			echo "此订单没有商品";exit;
		}
		//订单的用户信息
		$orderInfo = $orderModel->orderInfo($order_id);
		$result = [];
		$productModel = new ProductModel;
		foreach ($orderModelSn as $key => $value) {
			$sizeArr = $productModel->getSizeArr($v['model_sn']);
			$colorArr = $productModel->getColorArr($v['model_sn']);
			$orderItems = $productModel->getProductsCount($order_id, $v['model_sn']);
			foreach ($sizeArr as $sk => $sv) {
                foreach ($colorArr as $ck => $cv) {
                    $result[$k]['norm'][$cv['color_name']][$sv['size_name']] = 0;
                    foreach ($orderItems as $ik => $iv) {
                        $result[$k]['name'] = $iv['name'];
                        $result[$k]['wave_name'] = $iv['wave_name'];
                        $result[$k]['model_sn'] = $iv['model_sn'];
                        $result[$k]['size_name'][$sk] = $sv['size_name'];
                        $result[$k]['color_name'][$ck] = $cv['color_name'];
                        $result[$k]['img_url'] = $iv['img_url'];
                        // $result[$k]['cost_price'] = $iv['cost_price'];//商品现在的价格
                        $result[$k]['cost_price'] = $iv['price'];//订单里的价格
                        if ($iv['size_id'] == $sv['size_id'] && $iv['color_id'] == $cv['color_id']) {
                            $result[$k]['norm'][$cv['color_name']][$sv['size_name']] = $iv['nums'];
                        }
                    }
                }
            }
		}
		$data = [];
		$orderlist = $orderModel->orderItemList($order_id);
		foreach ($orderlist as $k => $v) {
		    $data[$v['style_sn']]['model_sn'] = $v['model_sn'];
		    $data[$v['style_sn']]['name'] = $v['name'];
		    $data[$v['style_sn']]['price'] = $v['price'];
		    $data[$v['style_sn']]['color_name'] = $v['color_name'];
		    $data[$v['style_sn']][$v['color_name']]['size_name'][$k]['size_name'] = $v['size_name'];
		    $data[$v['style_sn']][$v['color_name']]['size_name'][$k]['nums'] = $v['nums'];
		}
		$count = $orderModel->getCustomerNewCount($order_id)['oldprice'];
		$size = new SizeModel();
		$groupSize = $size->getGroupSize();
		return $this->render('detail', [
				'result' => $result, 
				'order_info' => $orderInfo, 
				'orderlist' => $data, 
				'count'=>$count, 
				'sizeGroup'=>$groupSize
			]);
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
