<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use frontend\helpers\IoXls;
/**
 * 临时输出
 */
class OutputController extends Controller
{
    /**
     * 导出每个五大类的总金额
     * @return [type] [description]
     */
    public function actionOutput()
    {
        $query = new Query;
        $select = ['c.code', 'c.name', 'c.leader', 'sum(oi.nums*p.cost_price) as allprice', 'p.cat_b'];
        $result = $query->select($select)
            ->from('meet_customer as c')
            ->leftJoin('meet_order as o', 'o.customer_id = c.customer_id')
            ->leftJoin('meet_order_items as oi', 'oi.order_id = o.order_id')
            ->leftJoin('meet_product as p', 'p.product_id = oi.product_id')
            ->where(['o.status' => 'finish'])
            ->groupBy(['p.cat_b', 'c.code'])
            ->all();
        $data = [];
        foreach ($result as $key => $value) {
            $data[$value['code']][] = $value;
        }
        $ding = [];
        foreach ($data as $key => $value) {
            $arr = [];
            $arr['code'] = $value[0]['code'];
            $arr['name'] = $value[0]['name'];
            $arr['leader'] = $value[0]['leader'];
            $ran = [];
            foreach ($value as $v) {
                //服装
                if ($v['cat_b'] == 1) {
                    $ran['fz'] = $v['allprice'];
                }
                //家居
                if ($v['cat_b'] == 2) {
                    $ran['jj'] = $v['allprice'];
                }
                //防辐射
                if ($v['cat_b'] == 3) {
                    $ran['ffs'] = $v['allprice'];
                }
                //备品
                if ($v['cat_b'] == 4) {
                    $ran['bp'] = $v['allprice'];
                }
                //化妆品
                if ($v['cat_b'] == 6) {
                    $ran['hzp'] = $v['allprice'];
                }
            }
            $arr['ffs'] = isset($ran['ffs'])?$ran['ffs']:0;
            $arr['fz'] = isset($ran['fz'])?$ran['fz']:0;
            $arr['jj'] = isset($ran['jj'])?$ran['jj']:0;
            $arr['hzp'] = isset($ran['hzp'])?$ran['hzp']:0;
            $arr['bp'] = isset($ran['bp'])?$ran['bp']:0;
            $ding[] = $arr;
        }
        $keys = [
            '客户代码',
            '客户名称',
            '负责人',
            '防辐射(元)',
            '服装(元)',
            '家居(元)',
            '化妆品(元)',
            '备品(元)',
        ];
        $filename = '总价格结果';
        $export = new IoXls();
        $export->export_begin($keys, $filename, count($ding));
        $export->export_rows($ding);
        $export->export_finish();

    }
}
