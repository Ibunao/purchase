<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class PublicModel extends Model
{
    /**
     * user
     * $this
     * 
     * 获取颜色信息
     * @return [type] [description]
     */
    public function getSize()
    {
        $result = Yii::$app->cache->get('size');
        if (!$result) {
            $result = (new Query)->from('meet_size')
                ->all();
            Yii::$app->cache->set('size', $result, 86400);
        }
        return $result;
    }
    /**
     * user
     * order/order/detail
     * 
     * 获取尺码组
     * @return [type] [description]
     */
    public function getGroupSize()
    {
        $result = $this->getSize();
        foreach ($result as $val) {
            $arr[$val['group_id']][] = $val['size_name'];
        }
        return $arr;
    }

    /**
     * use 
     * order/order/ImportFiles
     *
     * 获取尺寸信息
     * @return [type] [description]
     */
    public function sizeList()
    {
        $result = $this->getSize();
        $items = [];
        foreach ($result as $v) {
            $items[$v['size_name']] = $v;
        }
        return $items;
    }
}