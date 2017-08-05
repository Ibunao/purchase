<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\AdminUsers;
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
        var_dump($_POST);
    }
    public function actionGo()
    {
        echo 'www.baidu.com';
    }
}
