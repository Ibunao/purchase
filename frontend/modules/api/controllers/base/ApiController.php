<?php

namespace frontend\modules\api\controllers\base;

use Yii;
use yii\web\Controller;

/**
 * app基础控制器
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    public function init()
    {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    }

    /**
     * request封装
     * @param string $val  字段名
     * @param string $type 字段类型
     * @return string
     */
    public function request($val,$type='str',$default=0){
        if ($type == 'str') {
            $data = isset($_REQUEST[$val])?$_REQUEST[$val]:'';
        } else if($type == 'int') {
            $data = isset($_REQUEST[$val])?$_REQUEST[$val]:$default;
        }
        return $this->_CheckAndQuote($data);
    }
/**
     * POST获取单个数据
     *
     * @param $val
     * @param string $type
     * @return string
     */
    public function post($val,$type='str')
    {
        $data = '';
        if ($type == 'str') {
            $data = isset($_POST[$val])?$_POST[$val]:'';
        } else if($type == 'int') {
            $data = isset($_POST[$val])?$_POST[$val]:0;
        }
        return $this->_CheckAndQuote($data);
    }
    /**
     * prevent from invalidate sql sentense is put in advanced
     *
     * @param  $value value of waiting for format
     * @return string formatted value
     */
    function _CheckAndQuote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        return htmlspecialchars(addslashes($value));
    }
}