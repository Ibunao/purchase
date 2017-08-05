<?php 
namespace frontend\behaviors;

use yii\base\Behavior;
/**
* 查询通用方法
*/
class PublicFind extends Behavior
{
	public $object;
	public function getList()
    {
        return $this->object->find()->asArray()->all();
    }
}