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
use frontend\models\PublicModel;
use frontend\models\ColorModel;
use frontend\models\ImportLogModel;
use frontend\models\CatBigModel;
use frontend\helpers\IoXls;
use frontend\helpers\ErpCsv;
use frontend\models\OrderItemsModel;
use yii\web\NotFoundHttpException;

/**
 * 下载订单
 * @author dingran
 * @date(2017.8.15)
 */
class OrderController extends BaseController
{
	public function actionIndex()
	{
		//下拉框选项
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
            	/**
            	 * 这部分有时间优化一下
            	 */
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
        	$keys = [
	        	'客户/店铺名称', 
	        	'客户/店铺代码', 
	        	'订货会', 
	        	'订货指标', 
	        	'已订货金额', 
	        	'达成率', 
	        	'未完成金额', 
	        	'下线已定货金额'
	        ];
        	$data = [];
        	foreach ($result['list'] as $k => $v) {
        	    $data[$k]['A'] = $v['customer_name'];
        	    $data[$k]['B'] = $v['code'];
        	    $data[$k]['C'] = $v['purchase_id'] == 1 ? 'oct' : 'uki';
        	    $data[$k]['E'] = $v['target'];
        	    $data[$k]['F'] = $v['cost_item'];
        	    $data[$k]['G'] = number_format($v['rate'] * 100, 2) . "%";
        	    $data[$k]['H'] = $v['target'] - $v['cost_item'] <= 0 ? 0 : $v['target'] - $v['cost_item'];
        	    $data[$k]['I'] = $v['xxydhje'];
        	}

        	//总预期达成率
        	$zyqdcl = 0.00;
        	$target_sum = floatval($statistics['target_sum']);
        	if (!empty($statistics['amount']) && !empty($target_sum)) {
        	    $zyqdcl = number_format($statistics['amount'] / $target_sum * 100, 2);
        	}

        	//实际达成率
        	$zsjdcl = 0.00;
        	if (!empty($statistics['amount_really']) && !empty($target_sum)) {
        	    $zsjdcl = number_format($statistics['amount_really'] / $target_sum * 100, 2);
        	}
        	//已选客户预期达成率
        	$yxzyqdcl = 0.00;
        	$choose_target_sum = floatval($statistics['choose_target_sum']);
        	if (!empty($statistics['amount']) && !empty($choose_target_sum)) {
        	    $yxzyqdcl = number_format($statistics['amount'] / $choose_target_sum * 100, 2);
        	}
        	//实际达成率
        	$yxzsjdcl = 0.00;
        	if (!empty($statistics['amount_really']) && !empty($choose_target_sum)) {
        	    $yxzsjdcl = number_format($statistics['amount_really'] / $choose_target_sum * 100, 2);
        	}

        	$data2 = [
        	    ['', ''],
        	    ['总订货指标', $statistics['target_sum']],
        	    ['预期已订货金额', number_format($statistics['amount'], 2)],
        	    ['预期达成率', $zyqdcl . "%"],
        	    ['实际已订货金额', number_format($statistics['amount_really'], 2)],
        	    ['实际达成率', $zsjdcl . "%"],
        	    ['', ''],
        	    ['已选客户合并指标', $statistics['choose_target_sum']],
        	    ['已选客户预期达成率', $yxzyqdcl . "%"],
        	    ['已选客户实际达成率', $yxzsjdcl . "%"]
        	];
        	$filename = '订单导出筛选结果';
        	$export = new IoXls();
        	$export->export_begin($keys, $filename, count($data));
        	$export->export_rows($data);
        	$export->export_rows($data2);
        	$export->export_finish();
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
	/**
	 * 订单详情
	 * @param  [type] $order_id 订单id
	 * @return [type]           [description]
	 */
	public function actionDetail($order_id)
	{
		$orderModel = new OrderModel;
		$orderModelSn = $orderModel->orderProductModelSn($order_id);
		if (empty($orderModelSn)) {
			echo "此订单没有商品";exit;
		}
		//订单的详细信息
		$orderInfo = $orderModel->orderInfo($order_id);
		// var_dump($orderInfo);exit;
		$result = [];
		$productModel = new ProductModel;
		foreach ($orderModelSn as $k => $v) {
			// 该款号下的尺码信息
			$sizeArr = $productModel->getSizeArr($v['model_sn']);
			// 该款号下的颜色信息
			$colorArr = $productModel->getColorArr($v['model_sn']);
			// 该款号下用户下单的数据信息
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
                        $result[$k]['cost_price'] = $iv['cost_price'];//商品现在的价格
                        // $result[$k]['cost_price'] = $iv['price'];//订单里的价格
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
		$public = new PublicModel();
		$groupSize = $public->getGroupSize();
		return $this->render('detail', [
				'result' => $result, 
				'order_info' => $orderInfo, 
				'orderlist' => $data, 
				'count'=>$count, 
				'sizeGroup'=>$groupSize
			]);
	}

	/**
	 * 审核功能
	 * @return [type] [description]
	 */
	public function actionCheck()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$orderId = $request->post('order_id');
		$status = $request->post('status');
		$loginInfo = Yii::$app->session->get('login_in');
		$name = $loginInfo['name'];
		$userId = $loginInfo['user_id'];
		if (empty($orderId)) {
			return ['code' => 400];
		}
		$order = new OrderModel();
		if ($order->updateOrderStatus($orderId, $status)) {
			return ['code' => 200];
		}
		return ['code' => 400];
	}

	/**
	 * 订单详情下载
	 * @param  [type] $order_id 订单号
	 * @return [type]           [description]
	 */
    public function actionDownloadorderitems($order_id)
    {
        if (empty($order_id)) {
            echo "订单号不能为空";exit;
        }
        $order = new OrderModel();
        $orderInfo = $order->orderInfo($order_id);//订货统计
        $orderItem = $order->DownloadOrderItemList($order_id);//商品属性
        $keys = [
            '样品代码', 
            '样品名称', 
            '单位',
            '颜色', 
            '颜色名称', 
            '尺码', 
            '尺码名称', 
            '数量', 
            '标准价', 
            '折扣', 
            '单价', 
            '标准金额',
            '金额', 
            '交货日期', 
            '订货状态', 
            '备注', 
            '关联分销订单', 
            '品牌代码', 
            '品牌名称',
            //这里开始 都是未定义
            '大类', '中类', '小类', '面料', '种类划分', '款式划分', '波段划分'
        ];
        $count_cost = 0;
        $data = [];
        foreach ($orderItem as $k => $v) {
            $data[$k]['model_sn'] = $v['model_sn'];
            $data[$k]['name'] = $v['name'];
            $data[$k]['dan_wei'] = " ";
            $data[$k]['color_no'] = $v['color_no'];
            $data[$k]['color_name'] = $v['color_name'];
            $data[$k]['size_no'] = $v['size_no'];
            $data[$k]['size_name'] = $v['size_name'];
            $data[$k]['nums'] = $v['nums'];  //数量
            $data[$k]['price'] = $v['cost_price'];//标准价
            $data[$k]['discount'] = round($orderInfo['big_'.$v['cat_b'].'_count'] / 100, 2);     //折扣
            $data[$k]['Standard_price'] = round($v['cost_price'] * $orderInfo['big_'.$v['cat_b'].'_count'] / 100, 2); //单价
            $data[$k]['Standard_amount'] = round($v['amount'] * $orderInfo['big_'.$v['cat_b'].'_count'] / 100, 2);  //标准金额
            $data[$k]['amount'] = round($v['amount'] * $orderInfo['big_'.$v['cat_b'].'_count'] / 100, 2); //金额
            $data[$k]['date'] = "2016-03-19";
            $data[$k]['status'] = "0";
            $data[$k]['remark'] = " ";
            $data[$k]['order_id'] = " ";
            $data[$k]['brand_id'] = $v['brand_id'];
            $data[$k]['brand_name'] = $v['brand_name'];
            $data[$k]['A'] = $v['big_name'];//大类
            $data[$k]['B'] = $v['middle_name'];//中类
            $data[$k]['C'] = $v['small_name'];//小类
            $data[$k]['D'] = $v['memo'];//面料
            $data[$k]['E'] = $v['scheme_name'];//种类划分
            $data[$k]['F'] = $v['level_name'];//款式划分
            $data[$k]['G'] = $v['wave_name'];//波段划分
            $count_cost += $data[$k]['amount'];
        }
        $orderInformation = array(
            array('DHJ', '订货会订单:' . $order_id),
            array('DJBH', '单据编号:'),
            array('RQ', '日期:' . date('Y-m-d H:i:s', $orderInfo['create_time'])),
            array('YDJH', '原单号:'),
            array('QDDM', '渠道:'),
            array('DM2', '仓库:'),
            array('DM2_1', '库位:'),
            array('DM1', '客户:'),
            array('DM3', '关联仓库:'),
            array('DM3_1', '关联库位:'),
            array('BYZD1', '价格选定:'),
            array('DM1_1', '折扣类型:'),
            array('BYZD12', '折扣:'),
            array('YGDM', '业务员:'),
            array('DM4', '订货会:' . $orderInfo['purchase_name']),
            array('DM4_1', '订单类型:'),
            array('YXRQ', '交货日期:2016-03-19'),
            array('BYZD7', '品牌:'),
            array('BYZD8', '定金类别:1'),
            array('BYZD9', '定金比:0.3'),
            array('BYZD10', '定金:0'),
            array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            array('1', '标准', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
        );
        $data_sum = array(
            array('合计', '', '', '', '', '', '', $orderInfo['nums'], '', '', '', $count_cost, $count_cost, '', '', '', '', '', '', '', '', '', '', '', '', ''),
            array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
            array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
        );
        $filename = $orderInfo['customer_name'] . $orderInfo['code'];
        $export = new IoXls();
        $export->download($filename . '.xls');
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style>td{vnd.ms-excel.numberformat:@}</style></head>';
        echo '<table width="100%" border="1">';
        $export->export_rows($orderInformation);
        echo '<tr><th filter=all>' . implode('</th><th filter=all>', $keys) . "</th></tr>\r\n";
        flush();
        $export->export_rows($data);
        $export->export_rows($data_sum);
        $export->export_finish();
    }
    /**
     * 导入订单
     * @return [type] [description]
     */
    public function actionImport()
    {
    	if (!Yii::$app->params['order_include']) {
    		throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    	}
    	return $this->render('import');
    }


    /**
     * 上传检查CSV文件
     */
    public function actionImportFiles()
    {
    	$code = Yii::$app->request->post('code');
        if (empty($code)) {
            echo "<script>alert('请输入客户代码');location.href='/order/order/import'</script>";
            die;
        }
        $customer_id = $code;
        $postFile = isset($_FILES["file"]) ? $_FILES['file'] : exit("请上传文件");
        //获取文件后缀
        $postFileType = pathinfo($postFile['name'], PATHINFO_EXTENSION);
        //允许的后缀
        $allowExt = ['csv'];

        if (!in_array($postFileType, $allowExt)) {
            echo "<script>alert('上传文件不支持类型，仅限传csv后缀名文件,请先下载导入模板再执行操作');location.href='/order/order/import'</script>";
            die;
        }

        if (!is_uploaded_file($postFile['tmp_name'])) {
            echo "<script>alert('不是通过HTP POST上传的文件');location.href='/order/order/import'</script>";
            die;
        }

        $query = new Query;
        $c_info = $query->select(['name', 'purchase_id', 'code', 'mobile', 'type', 'province'])
        	->from('meet_customer')
        	->where(['customer_id' => $customer_id])
        	->andWhere(['disabled' => 'false'])
        	->one();
        $nowTime = time();
        $newFileName = $nowTime . "." . $postFileType;
        $newFolder = date("Ymd", time());
        $transData = $newFolder . "/" . $newFileName;
        $newFolderPath = "upload/" . $newFolder . "/";//新地址
        if (!file_exists($newFolderPath)) mkdir($newFolderPath, 0777);
        $newFile = Yii::$app->basePath . "/web/" . $newFolderPath . $newFileName;
        // var_dump($newFile);exit;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $newFile)) {
            $handle = fopen($newFile, 'r');
            $result = ErpCsv::input_csv($handle);
            $len_result = count($result);
            if ($len_result <= 1) {
                echo "<script>alert('对不起，您的文件中没有数据，请检查');location.href='/order/order/import'</script>";
                die;
            }
            $arr = [];
            $data_str = "";
            $color_list = (new ColorModel)->colorList();
            $size_list = (new PublicModel)->sizeList();
            $productModel = new ProductModel;
            for ($i = 1; $i < $len_result; $i++) {
                $data = "";
                $model_sn = intval($result[$i][0]);
                if (empty($model_sn)) {
                    $data .= "<span>款号的<b>" . $result[$i][0] . "</b>不是数字</span>";
                } else {
                    $res = $productModel->getProductList($model_sn);
                    if ($res <= 0) {
                        $data .= "<span>款号的<b>" . $result[$i][0] . "</b>有问题</span>";
                    }
                }

                if (!isset($color_list[$result[$i][1]]['color_id'])) {
                    $data .= "<span>颜色的<b>" . $result[$i][1] . "</b>有问题</span>";
                }
                $color_id = $color_list[$result[$i][1]]['color_id'];
                if (!isset($size_list[$result[$i][2]]['size_id'])) {
                    $data .= "<span>尺码的<b>" . $result[$i][2] . "</b>有问题</span>";
                }
                $size_id = $size_list[$result[$i][2]]['size_id'];
                $num = intval($result[$i][3]);
                if (empty($num)) {
                    $data .= "<span>数量的<b>" . $result[$i][3] . "</b>有问题</span>";
                }

                $count = (new Query)->from('meet_product')
                	->where(['model_sn' => $model_sn])
                	->andWhere(['purchase_id' => $c_info['purchase_id']])
                	->andWhere(['color_id' => $color_id])
                	->andWhere(['size_id' => $size_id])
                	->andWhere(['is_down' => '0'])
                	->andWhere(['disabled' => 'false'])
                	->andWhere(['is_error' => 'false'])
                	->count();

                if ($count <= 0) {
                    $data .= "<b>款号对应的颜色、尺寸的商品不存在或者已下架，请重新添加</b>";
                }

                if (empty($data)) {
                    $arr[$model_sn."_".$color_id."_".$size_id] = $model_sn."_".$color_id."_".$size_id;
                } else {
                    $data_str .= "<p>第 " . ($i + 1) . " 行的 " . $data . "</p> ";
                }
            }

            if(empty($data_str)){
                if(($len_result -1) != count($arr)){
                    $data_str .= "<p class='text-danger'><b>系统显示：款号，色号，尺码有重复，请检查后合并订单然后再次上传！</b></p>";
                }
            }

            $product_list = array();
            if (empty($data_str)) {
                $product_list = $result;
            }

            return $this->render('importdetail', array(
                'customer' => $customer_id,
                'c_info' => $c_info,
                'file' => $transData,
                'data' => $data_str,
                'product_list' => $product_list,
            ));
        } else {
            echo "<script>alert('上传失败');location.href='/order/order/import'</script>";
            die;
        }
    }
    /**
     * ajax获取用户信息
     */
    public function actionAjaxThisCustomerExist($code)
    {
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (empty($code)) {
            die;
        }
        $res = (new Query)->from('meet_customer')->where(['code' => $code])->one();

        if (empty($res)) {
            return ['code' => '400'];
        } else {
            if ($res['purchase_id'] == 1) {
                $res['purchase_id'] = "A";
            } elseif ($res['purchase_id'] == 2) {
                $res['purchase_id'] = "B";
            } elseif ($res['purchase_id'] == 3) {
                $res['purchase_id'] = "AB";
            }
            $result = (new Query)->select(['SUM(i.nums) AS nums'])->from('meet_order as o')
            	->leftJoin('meet_order_items as i', 'i.order_id = i.order_id')
            	->where(['o.customer_id' => $res['customer_id']])
            	->one();
            	if ($result['nums'] >= 1) {
            		$res['otype'] = '追加';
            	}else{
            		$res['otype'] = '新增';
            	}
            return ['code' => '200', 'data' => $res];
        }
    }
    /**
     * 读取上传的需要导入的订单数据文件，并添加到订单中
     * @return [type] [description]
     */
    public function actionUploadingCsvData()
    {
    	$param = Yii::$app->request->post('param');
        if (!isset($param['file']) || !isset($param['customer_id'])) {
            echo "<script>alert('数据不存在！');location.href='/order/order/import'</script>";
            die;
        }

        //再次判断用户是否存在
        $customer_info = (new Query)->from('meet_customer')->select(['purchase_id', 'name', 'customer_id'])
        	->where(['customer_id' => $param['customer_id']])
        	->one();
        if (!$customer_info) {
            echo "<script>alert('用户不存在！');location.href='/order/order/import'</script>";
            die;
        }

        //尺码，颜色转换
        $color_list = (new ColorModel)->colorList();
        $size_list = (new PublicModel)->sizeList();

        //打开csv的相关操作
        $filePath = 'upload/' . $param['file'];
        $handle = fopen($filePath, 'r');
        $result = ErpCsv::input_csv($handle);
        $len_result = count($result);

        //检查此用户是否存在订单
        $order = new OrderModel();
        $order_info = (new Query)->from('meet_order')->where(['customer_id' => $customer_info['customer_id']])->one();
        if (empty($order_info)) {
            //创建订单，插入数据
            $order_id = $order->buildOrderNo();
            $params = array(
                'order_id' => $order_id,
                'purchase_id' => $customer_info['purchase_id'],
                'customer_id' => $customer_info['customer_id'],
                'customer_name' => $customer_info['name'],
                'create_time' => time(),
                'cost_item' => '0',
                'status' => 'active',
            );
            $order->setAttributes($params);
            var_dump($order->save());exit;
            if (!$order->save()) {
            	var_dump('创建订单失败', $order->errors);exit;
            };
        } else {
            //获取订单id
            $order_id = $order_info['order_id'];
        }

        for ($i = 1; $i < $len_result; $i++) {
            $model_sn = $result[$i][0];
            $color_id = $color_list[$result[$i][1]]['color_id'];
            $size_id = $size_list[$result[$i][2]]['size_id'];
            $num = intval($result[$i][3]);
            //用户导入的商品详情
            $product_info = (new Query)->select(['name', 'product_sn', 'style_sn', 'cost_price', 'product_id', 'model_sn'])
            	->from('meet_product')
            	->where(['model_sn' => $model_sn])
            	->andWhere(['color_id' => $color_id])
            	->andWhere(['size_id' => $size_id])
            	->andWhere(['is_down' => '0'])
            	->andWhere(['disabled' => 'false'])
            	->one();
            //检查订单 查看用户是否购买过此商品
            $ordered_lists = (new OrderItemsModel)->find()->select(['item_id', 'nums', 'amount'])
            	->where(['order_id' => $order_id])
            	->andWhere(['product_sn' => $product_info['product_sn']])
            	->andWhere(['style_sn' => $product_info['style_sn']])
            	->andWhere(['disabled' => 'false'])
            	->one();

            if (!empty($ordered_lists)) {
                $ordered_lists->nums = $ordered_lists->nums + $num;
                $ordered_lists->import = 'true';
                $ordered_lists->price = sprintf("%.2f", $product_info['cost_price']);
                $ordered_lists->amount = $ordered_lists->nums * $product_info['cost_price'];
                //需要修改的订单详情
                if (empty($ordered_lists->save())) {
                	echo "<script>alert('添加失败');location.href='/order/order/import'</script>";exit;
                }
            } else {
                $res['nums'] = $num;
                $res['order_id'] = $order_id;
                $res['amount'] = sprintf("%.2f", $num * $product_info['cost_price']);
                $res['import'] = "true";
                $res['product_id'] = $product_info['product_id'];
                $res['product_sn'] = $product_info['product_sn'];
                $res['style_sn'] = $product_info['style_sn'];
                $res['name'] = $product_info['name'];
                $res['model_sn'] = $product_info['model_sn'];
                $res['price'] = sprintf("%.2f", $product_info['cost_price']);
                unset($res['cost_price']);
                $orderItems = new OrderItemsModel;
                $orderItems->setAttributes($res);
                if (empty($orderItems->save())) {
                	echo "<script>alert('添加失败');location.href='/order/order/import'</script>";exit;
                }
            }
            $model_list[$model_sn] = $model_sn;
        }

        
        $implodeLog = new ImportLogModel();
        $nowTime = time();
        $implodeLog->import_file = $filePath;
        $implodeLog->insert_time = $nowTime;
        $implodeLog->type = 'order';
        $implodeLog->pre_key = $order_id;
        $implodeLog->save();

        echo "<script>alert('添加成功');location.href='/order/order/index'</script>";exit; 
    }

    /**
     * 按照客户代码导出
     * @return [type] [description]
     */
    public function actionExportMaster()
    {
    	$productModel = new ProductModel();
    	//客户总订单
        $res = $productModel->exportMasterAndSlave();
        //订货指标信息
        $count_target = $productModel->getCustomerOrderInfo();
        $amount = 0;
        $target = 0;
        foreach($res as $val){
            $amount += $val['count'];
            $target += $val['target'];
        }
        $xls = new IoXls();
        $key = [
            '客户/店铺名称', '客户/店铺代码', '订货会', '订货指标', '已订货金额', '达成率', '未完成金额', '下线已定货金额',
        ];
        $fileName = "客户订单(客户合并)";
        $xls->export_begin($key, $fileName, 0);
        $i = 1;
        $order = new OrderModel();
        foreach ($res as $val) {
            $data[$i]['A'] = $val['name'];
            $data[$i]['B'] = $val['code'];
            $data[$i]['C'] = $val['purchase_name'];
            $data[$i]['D'] = $val['target'];
            $data[$i]['E'] = $val['count'];
            if ($val['target'] == '0') {
                $data[$i]['F'] = '0%';
            } else {
                $data[$i]['F'] = round($val['count'] / $val['target'] * 100, 2) . "%";
            }
            $data[$i]['G'] = $val['target'] - $val['count'];
            if($data[$i]['G'] <= 0){
                $data[$i]['G'] = 0;
            }
            $data[$i]['H'] = $order->getMasterCount($val['code']);
            $xls->export_rows($data);
        }
        $params[] = array('','');
        $params[] = array('订单总指标', isset($count_target['des_target'])?$count_target['des_target']:0);

        $params[] = array('预期已订货金额',$amount);
        if($count_target['des_target'] == 0){
            $prePercent = 0 ."%";
        }else{
            $prePercent = round($amount / $count_target['des_target'] * 100, 2)."%";
        }
        $params[] = array('预期达成率',$prePercent);

        $params[] = array('实际已订货金额',isset($count_target['fin_target'])?$count_target['fin_target'] :0);
        if($count_target['des_target'] == 0){
            $factPercent = 0 ."%";
        }else{
            $factPercent = round( $count_target['fin_target'] / $count_target['des_target']   * 100, 2)."%";
        }
        $params[] = array('实际达成率',$factPercent);
        $xls->export_rows($params);
        $xls->export_finish();
    }
    /**
     * 折扣价格导出
     * @param  [type] $type 大分类类型
     * @return [type]       [description]
     */
    public function actionDiscount($type)
    {
    	$big_id = $type;
    	$order = new OrderModel();
        $result = $order->getAllCustomerDiscount($big_id);
        $catBig = new CatBigModel();
        $bigName = $catBig->getCatBig();
        foreach($bigName as $val){
            $big[$val['big_id']] = $val['cat_name'];
        }
        $xls = new IoXls();

        $key = array(
            '客户名称', '订货会', '客户代码', $big[$big_id].'订货指标', $big[$big_id].'已订货金额', '折扣', $big[$big_id].'折扣后价格', '折扣后'.$big[$big_id].'订货指标完成率'
        );
        $fileName = '客户'.$big[$big_id].'折扣导出';
        $xls->export_begin($key, $fileName, 0);
        $i = 1;
        $all_big_target = 0; //该大类的指标
        $all_bro_money = 0; //实际已买的金额
        $all_dis_money = 0; //折扣后的价格
        foreach($result as $k => $v){
            $data[$i]['A'] = $val['name'];
            $data[$i]['B'] = $val['purchase_id']==1?'OCT':'UKI';
            $data[$i]['C'] = $val['code'];
            $data[$i]['D'] = $val['starget'];
            $data[$i]['E'] = $val['amount'];
            $data[$i]['F'] = $val['discount'];
            $data[$i]['G'] = $val['final_amount'];
            $data[$i]['H'] = $val['percent'];
            $all_bro_money += $val['amount'];
            $all_big_target += $val['starget'];
            $all_dis_money += $val['final_amount'];
            $xls->export_rows($data);
            
        }
        if(empty($all_big_target)){
            $bro_percent = 0 .'%';
            $dis_percent = 0 .'%';
        }else{
            $bro_percent = round($all_bro_money/$all_big_target * 100 , 2) .'%';
            $dis_percent = round($all_dis_money/$all_big_target * 100 , 2) .'%';
        }
        if(empty($all))
        $params[] = array('','');
        $params[] = array($big[$big_id].'订货指标', $all_big_target);
        $params[] = array('实际已买'.$big[$big_id].'金额', $all_bro_money);
        $params[] = array('实际已买金额占'.$big[$big_id].'订货指标', $bro_percent);
        $params[] = array($big[$big_id].'折扣后金额', $all_dis_money);
        $params[] = array('实际已买折扣金额占'.$big[$big_id].'订货指标', $dis_percent);
        $xls->export_rows($params);
        $xls->export_finish();
        // ErpCsv::exportCsv(11111, $data, $fileName);
    }


    public function actionDownloadOrderItemsInOrderItemsTable()
    {
        set_time_limit(0);
    	$orderModel = new OrderModel;
    	$customer_info = $orderModel->getAllUserOrderItems();
        $model_info = $orderModel->getProductModelSnAndCatBig();
        $order_info = $orderModel->getAllOrderItemsList();

//         $xls = new IoXls();
// $objPHPExcel = new \PHPExcel;
// /*以下是一些设置 ，什么作者  标题啊之类的*/  
// $objPHPExcel->getProperties()->setCreator("转弯的阳光")  
// ->setLastModifiedBy("转弯的阳光")  
// ->setTitle("数据EXCEL导出")  
// ->setSubject("数据EXCEL导出")  
// ->setDescription("备份数据")  
// ->setKeywords("excel")  
// ->setCategory("result file");  
		$key = [
		   '商品代码',
		   '颜色编号',
		   '尺寸编号',
		   '数量',
		   '吊牌价',
		   '折扣',
		   '客户代码',
           '订单编号',
		   '备注',
		];
		$fileName = '订单详细信息导出';
		// $xls->export_begin($key, $fileName, 0);
		foreach($order_info as $i => $v){
		   $data[$i]['A'] = '\''.$v['model_sn'];
		   $data[$i]['B'] = '\''.$v['color_no'];
		   $data[$i]['C'] = '\''.$v['size_no'];
		   $data[$i]['D'] = '\''.$v['nums'];
		   $data[$i]['E'] = '\''.$v['cost_price'];
		   $data[$i]['F'] = '\''.($customer_info[$v['order_id']]['big_'.$model_info[$v['model_sn']].'_count'] /100);
		   $data[$i]['G'] = '\''.$v['code'];
           $data[$i]['H'] = '\''.$v['order_id'];
		   $data[$i]['J'] = '\''.$v['name'];

           // var_dump($data);exit;
		   $i++;

            /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/  
            // $customer = ($customer_info[$v['order_id']]['big_'.$model_info[$v['model_sn']].'_count'] /100);
            // $num=$i+1;  
            // $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推  
            // ->setCellValue('A'.$num, $v['model_sn'])     
            // ->setCellValue('B'.$num, $v['color_no'])  
            // ->setCellValue('C'.$num, $v['size_no'])  
            // ->setCellValue('D'.$num, $v['nums'])  
            // ->setCellValue('E'.$num, $v['cost_price'])  
            // ->setCellValue('F'.$num, $customer)  
            // ->setCellValue('G'.$num, $v['code']) 
            // ->setCellValue('G'.$num, $v['order_id']);  
		}
        // $objPHPExcel->getActiveSheet()->setTitle('User');  
        // $objPHPExcel->setActiveSheetIndex(0);  
        //  header('Content-Type: applicationnd.ms-excel');  
        //  header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');  
        //  header('Cache-Control: max-age=0');  
        //  $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
        //  $objWriter->save('php://output');  
        //  exit;
		// $xls->export_rows($data);
		// $xls->export_finish();

        ErpCsv::exportCsv($key, $data, $fileName.'.csv');
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
