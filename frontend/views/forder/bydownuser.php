<!--主导航-->
<div id="nav_shade" class="none"></div>
<?php echo $this->render('/layouts/_nav', array('sel' => 'bydownuser')); ?>
<!--主导航-->

<div class="order_dt_rt_main">
    <?php if($downUserInfo): ?>
    <table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#dddddd" class="mb60">
        <thead>
        <tr>
            <td width="22%">客户/店铺名称</td>
            <td width="12%">客户/店铺代码</td>
            <td width="16%">订货指标</td>
            <td width="18%">已订货金额</td>
            <td width="10%">达成率</td>
            <td width="12%">未完成金额</td>
            <td width="10%">操作</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($downUserInfo as $v): ?>
            <tr>
                <td><?php echo $v['name']; ?></td>
                <td><?php echo $v['code']; ?></td>
                <td><?php echo $v['target']; ?></td>
                <td><?php echo $v['cost_item']; ?></td>
                <td><?php echo $v['percent'];?></td>
                <td><?php echo $v['left_cost'];?></td>
                <td><a href="/downuser/bydownprice?customer_id=<?php echo $v['customer_id']; ?>&purchase_id=<?php echo $v['purchase_id']; ?>" class="detail_bt_modify fancybox">明细</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align: center;font-size: 30px">暂无相关信息</p>
    <?php endif; ?>
</div>
<?php echo $this->render('/common/_footer_order',array());?>