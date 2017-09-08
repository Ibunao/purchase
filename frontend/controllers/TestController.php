<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\AdminUsers;
use frontend\models\ProductModel;
/**
 * 测试
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
        // $result = $models->getOrderInfo('1', '10000002');
        // var_dump($result);

        $result = $models->find()
            ->where(['model_sn' => 12345679])
            ->orWhere(['serial_num' => 1000000000])
            ->count();
            var_dump($result);
    }
    public function actionGo()
    {
        echo 'www.baidu.com';
    }
    public function actionIndex()
    {
        $this->layout = false;
        return $this->render('index');
    }
    public function actionPass()
    {
        $source_str = 123456;
        $username = 18237121201;
        $createtime = 1497437769;
        //密码的MD5值连接用户名和注册时间，再算MD5
        $string_md5 = md5(md5($source_str).$username.$createtime);
        //取上面MD5值的前31位
        $front_string = substr($string_md5,0,31);
        //在前面加上字符s
        $end_string = 's'.$front_string;
        return $end_string;
    }
    public function actionPath()
    {
        echo Yii::$app->homeUrl;
    }
    public function actionEchart()
    {
        return $this->render('echart');
    }
}
