<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->params['breadcrumbs'] = [
    ['label' => '订单'],
    ['label' => '订单详细汇总'],
];
?>
<div id="filter_form">
    <?= $this->render('filter', ['params' => $params, 'selectOption' => $selectOption, 'statistics' => $statistics]); ?>
</div>


<div class="row">
    <div>
        <div class="table-responsive">
            <div role="grid" class="dataTables_wrapper col-xs-12" id="sample-table-2_wrapper">
                <table class="table table-striped table-bordered table-hover dataTable" id="goods-list"
                       aria-describedby="sample-table-2_info">
                    <thead>
                    <tr role="row">
                        <th tabindex="0" style="width: 300px;">客户/店铺名称</th>
                        <th tabindex="0" style="width: 140px;">客户/店铺代码</th>
                        <th tabindex="0" style="width: 60px;">订货会</th>
                        <th tabindex="0" style="width: 109px;">订货指标</th>
                        <th tabindex="0" style="width: 109px;">已订货金额</th>
                        <th tabindex="0" style="width: 100px;">达成率</th>
                        <th tabindex="0" style="width: 180px;">未完成金额</th>
                        <th tabindex="0" style="width: 180px;">下线已定货金额</th>
                        <th tabindex="0" style="width: 300px;">审核人/审核时间</th>
                        <th tabindex="0" style="width: 300px;"></th>
                        <th tabindex="0" style="width: 400px;"></th>
                    </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <?php foreach ($result['list'] as $v) { ?>
                        <tr class="odd">

                            <td><?= $v['customer_name']; ?> </td>
                            <td><?= $v['code']; ?></td>
                            <td><?= ($v['purchase_id'] == 1) ? 'oct' : 'uki'; ?></td>
                            <td><?= ($v['target'] == 0) ? '' : number_format($v['target'], 2); ?></td>
                            <td><?= number_format($v['cost_item'], 2); ?></td>
                            <td><?= ($v['target'] == 0) ? '' : number_format($v['cost_item'] / $v['target'] * 100, 2) . '%'; ?> </td>
                            <td><?= ($v['target'] - $v['cost_item'] <= 0) ? 0.00 : number_format($v['target'] - $v['cost_item'], 2); ?> </td>
                            <td><?= number_format($v['xxydhje'], 2); ?></td>
                            <td><?= $v['check_user'] . '/' . $v['check_time']; ?> </td>

                            <td>
                                <span class="btn-group col-sm-5">
                                <input type="button" class="btn btn-sm btn-info "
                                       data-val="<?= $v['order_id']; ?>"
                                       onclick="window.location.href='<?=Url::to(['/order/order/detail', 'order_id'=> $v['order_id']]) ;?>'"
                                       value="明细">
                                </span>
                                <span class="btn-group col-sm-4" style=" display: <?php if ($v['status'] == 'confirm') {
                                    echo 'block';
                                } else {
                                    echo 'none';
                                } ?>">
                                <input type="button" class="btn btn-sm btn-warning checkOrder" data-status="finish"
                                       data-val="<?= $v['order_id']; ?>" value="订单审核">
                                </span>
                                <span class="btn-group col-sm-4" style=" display: <?php if ($v['status'] == 'finish') {
                                    echo 'block';
                                } else {
                                    echo 'none';
                                } ?>">
                                <input type="button" class="btn btn-sm btn-danger checkOrder" data-status="confirm"
                                       data-val="<?= $v['order_id']; ?>" value="取消审核">
                                </span>
                            </td>
                            <td>
                                <span class="btn-group">
                                <button type="button" class="btn btn-sm btn-info Dialogue "
                                        data-val="<?= $v['code']; ?>" value="">复制
                                </button>
                                <button type="button" class="btn btn-sm btn-info "
                                        onclick="window.location.href='<?=Url::to(['/order/order/downloadorderitems', 'order_id'=> $v['order_id']]) ;?>'"
                                        value="">下载
                                </button>
                                <?php if($v['is_diff']){ ?>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="window.location.href='<?=Url::to(['/order/order/differ', 'order_id'=> $v['order_id']]) ;?>'"
                                            value="">价格变动
                                    </button>
                                <?php } ?>
                                <?php if($v['parent_id'] == '1'){ ?>
                                    <button type="button" class="btn btn-sm btn-success" onclick="window.location.href='<?=Url::to(['/order/order/index', 'param[leader_name]'=> $v['code']]) ;?>'">
                                        查看下线
                                    </button>
                                <?php } ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="row">
<?= LinkPager::widget([
    'pagination' => $result['pagination'],
]);?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="Dialogue" style="display: none">
    <div style="margin-top: 140px;margin-left: 50px;">
        <?= $this->render('copy'); ?>
    </div>
</div>

<script>
    $(function ($) {

        var html = $("#Dialogue").html();
        $(".checkOrder").click(function () {
            if (confirm('确认订单审核通过吗？')) {
                var btn = $(this);
                var order_id = $(this).attr('data-val');
                var status = $(this).attr('data-status');
                $.post('<?=Url::to(['/order/order/check']) ;?>', {'order_id': order_id, 'status': status}, function (data) {
                    if (data.code !== 400) {
                        alert('操作成功');
                        btn.parent().hide();
                        btn.parent().siblings().show();
                    } else {
                        alert('操作失败');
                    }

                }, 'json');
            }

        });


        $(".Dialogue").click(function () {
            $("#Dialogue").html('')
            var from = $(this).attr('data-val');
            $.layer({
                type: 1,   //0-4的选择,
                title: false,
                border: [0],
                closeBtn: [0],
                shadeClose: true,
                area: ['800px', '300px'],
                page: {
                    html: html
                }
            });
            $("#from").val(from);
        });

    });

</script>
