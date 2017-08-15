<?php
use yii\widgets\LinkPager;

$this->params['breadcrumbs'] = [
    ['label' => '订单', 'url' => ['default/index']],
    ['label' => '订单汇总'],
    ['label' => '商品订单汇总-图片查看'],
];
?>
<div style="width: 1000px"   id="filter_form">
    <?= $this->render('filter', ['params'=>$params,'selectOption'=>$selectOption,'result'=>$result]);?>
    <?= $this->render('window');?>
</div>

    <div>
        <div class="table-responsive " style="width: 1000px;float: left" id="print_area">
            <?php foreach ($result['item'] as $v){?>
            <div role="grid" class="dataTables_wrapper " style="height: 450px;float: left;margin-left:16px" id="sample-table-2_wrapper">
                <table class="table table-striped table-bordered table-hover dataTable " id="goods-list" aria-describedby="sample-table-2_info" style="width:180px;font-size: 12px">

                    <tbody role="alert" aria-live="polite" aria-relevant="all">

                        <tr >
                            <td colspan="2" style="text-align: center"><img src='<?php echo $v['img_url']; ?>' height="160px"/> </td>
                        </tr>
                        <tr class="odd">
                            <td width="80px">大类</td>
                            <td><?php echo $v['cat_big_name']; ?></td>
                        </tr>
                        <tr class="odd">
                            <td>小类</td>
                            <td><?php echo $v['cat_small_name']; ?></td>
                        </tr>
                        <tr class="odd">
                            <td>款色</td>
                            <td><?php echo $v['style_sn']; ?></td>
                        </tr>
                        <tr class="odd">
                            <td>吊牌价</td>
                            <td><?php echo $v['cost_price']; ?></td>
                        </tr>
                        <tr class="odd">
                            <td>加盟订单</td>
                            <td><?php echo $v['customer']; ?></td>
                        </tr>
                        <tr class="odd">
                            <td>自营订单</td>
                            <td><?php echo $v['self'];  ?> </td>
                        </tr>
                        <tr class="odd">
                            <td>总订单</td>
                            <td><?php echo $v['nums']; ?>
                                <input type="button" class="btn btn-sm btn-info Dialogue pull-right" data-val="<?php echo $v['style_sn'];?>" value="明细">
                            </td>
                        </tr>


                    </tbody>
                </table>

            </div>
            <?php } ?>
        </div>
        <div role="grid" class="dataTables_wrapper col-sm-12" style="width: 1000px;float: left" id="sample-table-2_wrapper">
            <div class="row">
<?= LinkPager::widget([
    'pagination' => $result['pagination'],
]);?>
            </div>
        </div>
    </div>

