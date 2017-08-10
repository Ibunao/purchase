<?php

namespace frontend\controllers\base;

use Yii;
use yii\web\Controller;

/**
 * 基础控制器
 */
class BaseController extends Controller
{

    public $layout = '/backend';
    //关闭csrf
    public $enableCsrfValidation = false;

	public function init()
	{
		parent::init();
		//未登录跳转登陆
        if (empty(Yii::$app->session->get('login_in'))){
        	$this->redirect(['/admin/login']);
        } 
	}
}