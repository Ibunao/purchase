<?php
use yii\widgets\LinkPager;

$this->params['breadcrumbs'] = [
    ['label' => '订单', 'url' => ['default/index']],
    ['label' => '订单汇总'],
    ['label' => '商品订单汇总-列表查看'],
];
?>
<div style="width: 1000px"    id="filter_form">
    <?= $this->render('filter', ['params'=>$params,'selectOption'=>$selectOption,'result'=>$result]);?>
    <?= $this->render('window');?>
</div>


<div style="width: 1000px"   class="row">
    <div class="col-xs-12">


        <div class="table-responsive" id="print_area">
            <div role="grid" class="dataTables_wrapper col-xs-12" id="sample-table-2_wrapper">
                <table class="table table-striped table-bordered table-hover dataTable" id="goods-list" aria-describedby="sample-table-2_info" style="width:977px">
                    <thead>
                    <tr role="row">
                        <th>大类</th>
                        <th>中类</th>
                        <th>小类</th>
                        <th>款色</th>
                        <th>流水</th>
                        <th>商品类型</th>
                        <th>吊牌价</th>
                        <th>加盟订货</th>
                        <th>直营订货</th>
                        <th>总订货</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <?php foreach ($result['item'] as $v){?>
                        <tr class="odd">
                            <td><?= isset($v['cat_big_name'])?$v['cat_big_name']:''; ?> </td>
                            <td><?= isset($v['cat_middle_name'])?$v['cat_middle_name']:''; ?> </td>
                            <td><?= isset($v['cat_small_name'])?$v['cat_small_name']:''; ?></td>
                            <td><?= $v['style_sn']; ?></td>
                            <td><?= $v['serial_num']; ?></td>
                            <td><?= $v['type_name']; ?></td>
                            <td><?= $v['cost_price']; ?></td>
                            <td><?= $v['customer']; ?></td>
                            <td><?= $v['self'];  ?> </td>
                            <td><?= $v['nums']; ?> </td>
                            <td> <input type="button" class="btn btn-sm btn-info Dialogue" data-val="<?= $v['style_sn'];?>"  value="明细"></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
                <div class="row"  style="width:977px">
<?= LinkPager::widget([
    'pagination' => $result['pagination'],
]);?>
                </div>
    </div>
</div>


