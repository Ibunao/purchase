<?php

namespace frontend\modules\desktop\controllers;

use yii\web\Controller;
use frontend\models\OrderModel;
/**
 * 后台
 */
class TestController extends Controller
{
    public function actionNo()
    {
        $no = substr(uniqid(), 7, 13);
        $no1 = str_split($no, 1);
        $no2 = array_map('ord', $no1);
        $no3 = implode($no2);
        $no4 = substr($no3, 0, 8);
        $result = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        var_dump($no, $no1, $no2, $no3, $no4, $result);
    }
    public function action()
    {
        
    }
}
