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


    /**
     * 清除所有服务器的缓存
     * @return [type] [description]
     */
    static function flushCacheAll()
    {
        $res = Yii::$app->params['flush_cache_url'];
        foreach ($res as $val) {
            file_get_contents($val . '/user/cache');
        }
        echo "清除所有的缓存";
    }

    /**
     * use
     * 前台_nav视图
     * 
     * 左侧分类
     */
    public static function cateList()
    {
        $purchase_id = Yii::$app->session['purchase_id'];

        $items = Yii::$app->cache->get('cat_big_small_list-'.$purchase_id);
        if (!$items) {
            //分类
            $list = (new Query)->from('meet_cat_big_small')
                ->all();
            $trans = [];
            foreach($list as $k => $val){
                $trans[$val['big_id']."_".$val['small_id']] = $val;
            }
            //所有的不重复的 style_sn 的产品
        
            $query = (new Query)->select(['cat_b', 'cat_s', 'style_sn'])
                ->from('meet_product')
                ->where(['disabled' => 'false'])
                ->andWhere(['is_down' => '0']);
            if ($purchase_id == Yii::$app->params['purchaseAB']) {
                $query->andWhere(['or', 'purchase_id', [1,2]]);
            }else{
                $query->andWhere(['purchase_id' => $purchase_id]);
            }
            $small = $query->groupBy('style_sn')->all();
            //统计大分类下小分类的数量
            foreach ($small as $v) {
                if (!isset($smallNum[$v['cat_b']][$v['cat_s']])) $smallNum[$v['cat_b']][$v['cat_s']] = 1;
                else $smallNum[$v['cat_b']][$v['cat_s']]++;
            }
            foreach ($trans as $v) {
                $items[$v['big_id']]['id'] = $v['big_id'];
                $items[$v['big_id']]['name'] = $v['big_cat_name'];
                $itemChild['id'] = $v['small_id'];
                $itemChild['name'] = $v['small_cat_name'];
                $itemChild['num'] = isset($smallNum[$v['big_id']][$v['small_id']])?$smallNum[$v['big_id']][$v['small_id']]:0;
                $items[$v['big_id']]['child'][] = $itemChild;
            }
            Yii::$app->cache->set('cat_big_small_list-'.$purchase_id,$items);
        }
        return $items;
    }
}