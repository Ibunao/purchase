<!-- 引入 ECharts 文件 -->
<script src="/js/echarts.common.min.js"></script>
<div class="order_nav w100" id="hdNav">
    <ul>
        <?php if(isset($sel) && $sel): ?>
            <li class="order_nav_title fl">我的订单</li>
            <li class="order_total"><a href="/forder/bycount" class="<?= $sel == 'bycount' ? 'act': ''; ?>"><?= Yii::$app->params['season_title']; ?>汇总</a></li>
            <li class="order_total"><a href="/forder/byprice" class="<?= $sel == 'byprice' ? 'act': ''; ?>">价格汇总</a></li>
            <li class="order_nav_bt_area fr"><a href="/forder/bydetail" class="order_nav_bt <?= $sel == 'bydetail' ? 'selected': ''; ?>">订单明细</a></li>
            <li class="fr"><a href="/forder/bycount" class="order_nav_bt <?= $sel == 'bycount' ? 'selected': ''; ?>">订单统计</a></li>
            <li class="fr"><a href="/forder/bydownuser" class="order_nav_bt <?= $sel == 'bydownuser' ? 'selected': ''; ?>">我的分销</a></li>
        <?php else: ?>
            <li class="order_nav_title fl">分销订单</li>
            <li class="order_total"><a href="/downuser/bydowncount?purchase_id=<?= $purchase_id; ?>&customer_id=<?= $customer_id; ?>" class="<?= $down == 'bydowncount' ? 'act': ''; ?>"><?= Yii::$app->params['season_title']; ?>汇总</a></li>
            <li class="order_total"><a href="/downuser/bydownprice?purchase_id=<?= $purchase_id; ?>&customer_id=<?= $customer_id; ?>" class="<?= $down == 'bydownprice' ? 'act': ''; ?>">价格汇总</a></li>
            <li class="order_nav_bt_area fr"><a href="/forder/bydownuser" class="order_nav_bt">分销列表</a></li>
            <li class=" fr">分销名称：<?= $this->down_code; ?></li>
        <?php endif; ?>
    </ul>
</div>