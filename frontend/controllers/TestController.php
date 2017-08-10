<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\AdminUsers;
use frontend\models\ProductModel;
/**
 * Admin controller
 */
class TestController extends Controller
{
    //关闭csrf验证
    public $enableCsrfValidation = false;
    /**
     * 登陆
     *
     * @return mixed
     */
    public function actionTest()
    {
        $models = new ProductModel;
        $result = $models->getOrderInfo('1', '10000002');
        var_dump($result);
    }
    public function actionGo()
    {
        echo 'www.baidu.com';
    }
}
