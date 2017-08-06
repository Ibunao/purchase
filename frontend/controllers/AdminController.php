<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\AdminUsers;
/**
 * Admin controller
 */
class AdminController extends Controller
{
    //关闭csrf验证
    public $enableCsrfValidation = false;
    /**
     * 登陆
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = false;
        $session = Yii::$app->session;
        //登陆过的直接跳转
        $login = $session->get('login_in');
        if (!empty($login)) {
            $this->redirect(['desktop/default/index']);
        }
        $request = Yii::$app->request;
        if ($request->post('Desktopusers')) {
            $post = $request->post('Desktopusers');
            $name = $post['name'];
            $password = md5(md5($post['password']));
            $user = AdminUsers::find()
                ->where(['name'=>$name])
                ->andWhere(['password'=>$password])
                ->one();
            if ($user) {

                $loginInfo = [
                    'user_id'=>$user['user_id'],
                    'name' => $name,
                ];
                $session->set('login_in',$loginInfo);
                $this->redirect(['desktop/default/index']);

            } else {
                $session->setFlash('error', '账号或密码不正确');
            }
        }
        return  $this->render('login');
    }
    /**
     * 退出
     */
    public function actionLogout()
    {
        //必须先要开启
        Yii::$app->session->open();
        Yii::$app->session->destroy();
        $this->redirect(['admin/login']);
    }
}
