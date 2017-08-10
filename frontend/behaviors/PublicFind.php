<?php 
namespace frontend\behaviors;

use Yii;
use yii\base\Behavior;
/**
* 查询通用方法
*/
class PublicFind extends Behavior
{
	public $object;
	public function getList()
    {
    	// $key = strtolower(str_replace('\\', '_', get_class($this->object)));
    	// $result = Yii::$app->cache->get($key);
    	// if (empty($result)) {
    	// 	$result = $this->object->find()->asArray()->all();
    	// 	Yii::$app->cache->set($key, $result);
    	// }
        return $this->object->find()->asArray()->all();
    }
}