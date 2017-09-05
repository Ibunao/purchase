<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\CustomerModel;
use frondent\models\PublicModel;
/**
 * 前台登录页
 *
 * @author        丁冉
 */

class UserController extends Controller
{
    //关闭csrf
    public $enableCsrfValidation = false;
    /**
     * 用户登录
     */
    public function actionIndex()
    {
        $this->layout = 'frondent';
        $request = Yii::$app->request;
        $error = false;
        if ($request->isPost) {
            $account = $request->post('account', '');
            $password = $request->post('password', '');

            $user = (new CustomerModel)->login($account, $password);
            if (!empty($user)) {
                $nowTime = time();
                $session = Yii::$app->session;
                $session->set('code', $user->code);
                Yii::$app->session['customer_id'] = $user->customer_id;
                Yii::$app->session['purchase_id'] = $user->purchase_id;
                Yii::$app->session['name'] = $user->name;
                Yii::$app->session['mobile'] = $user->mobile;
                Yii::$app->session['type'] = $user->type;
                Yii::$app->session['province'] = $user->province;
                Yii::$app->session['area'] = $user->area;
                Yii::$app->session['target'] = $user->target;
                Yii::$app->session['login_time'] = $nowTime;
                $user->login = $nowTime;
                if ($user->save()) {
                    $this->redirect('/default/index')->send();
                }
            } else {
                $error = true;
            }
        }

        return $this->render('index',['error'=>$error]);
    }

    /**
     * 用户退出
     */
    public function actionLogout()
    {
        //必须先要开启
        Yii::$app->session->open();
        Yii::$app->session->destroy();

        $this->redirect('index');
    }

    /**
     * 删除缓存
     */
    public function actionCache()
    {
        Yii::$app->cache->flush();
        echo "清除缓存";
    }
    /**
     * 清除所有的缓存
     * @return [type] [description]
     */
    public function actionFlushCacheAll()
    {
        PublicModel::flushCacheAll();
    }
    
}