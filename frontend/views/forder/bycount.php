<!--主导航-->
<div id="nav_shade" class="none"></div>
<?= $this->render('/layouts/_nav', array('sel' => 'bycount')); ?>
<!--主导航-->
<!--右侧主区域-->
<div class="order_dt_table_all">
<!--表头-->
<ul class="normal_line">
    <!--第一行 first_l 第一列 first_v-->
    <li class="div10 fl first_v first_l">大类</li>
    <li class="div12 fl first_l">小类</li>
    <li class="div10 fl first_l">款数</li>
    <li class="div10 fl first_l"><?= Yii::$app->params['season_one_name']; ?>季</li>
    <li class="div10 fl first_l"><?= Yii::$app->params['season_two_name']; ?>季</li>
    <li class="div14 fl first_l">总订货数量</li>
    <li class="div10 fl first_l">总数量占比</li>
    <li class="div14 fl first_l">总订货金额</li>
    <li class="div10 fl first_l">总金额占比</li>
</ul>
<!--表头-->

<?php foreach ($list as $v):?>
<!--大类1-->
<ul class="normal_line">
    <li class="div10 fl first_v"><?= $v['b_name'];?></li>
    <li class="div12 fl">
        <!--小类-->
        <ul class="semi_level">
            <!--二级的首行first_l-->
            <?php foreach ($v['small'] as $kk=>$vv):?>
            <li><?= $vv['name'];?></li>
            <?php endforeach;?>
        </ul>
        <!--小类-->
    </li>
    <li class="div10 fl">
        <!--款数-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= count(array_unique($vv['model']));?></li>
            <?php endforeach;?>
        </ul>
        <!--款数-->
    </li>
    <li class="div10 fl">
        <!--秋季-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['season_id_1'];?></li>
            <?php endforeach;?>
        </ul>
        <!--秋季-->
    </li>
    <li class="div10 fl">
        <!--冬季-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['season_id_2'];?></li>
            <?php endforeach;?>
        </ul>
        <!--冬季-->
    </li>
    <li class="div14 fl">
        <!--订单数量-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['nums'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订单数量-->
    </li>
    <li class="div10 fl">
        <!--数量占比-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= round(($vv['nums']/$result['total_nums'])*100,1).'%';?></li>
            <?php endforeach;?>
        </ul>
        <!--数量占比-->
    </li>
    <li class="div14 fl">
        <!--订货金额-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['amount'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订货金额-->
    </li>
    <li class="div10 fl last_v">
        <!--金额占比-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= round(($vv['amount']/$result['amount'])*100,1).'%';?></li>
            <?php endforeach;?>
        </ul>
        <!--金额占比-->
    </li>
</ul>
<ul class="statistics_line">
    <li class="div22 fl first_v"><?= $v['b_name'];?></li>
    <li class="div10 fl word_red"><?= count(array_unique($v['model']))?></li>
    <li class="div10 fl word_red"><?= $v['season_id_1'];?></li>
    <li class="div10 fl word_red"><?= $v['season_id_2'];?></li>
    <li class="div14 fl word_red"><?= $v['nums'];?></li>
    <li class="div10 fl word_red"><?= round(($v['nums']/$result['total_nums'])*100,1).'%';?></li>
    <li class="div14 fl word_red"><?= $v['amount'];?></li>
    <li class="div10 fl word_red last_v"><?= round(($v['amount']/$result['amount'])*100,1).'%';?></li>
</ul>
<!--大类1-->
<?php endforeach;?>

<!--总计-->
<ul class="statistics_line">
    <li class="div22 fl first_v">订货总计</li>
    <li class="div10 fl word_red last_l"><?= count(array_unique($result['model']));?></li>
    <li class="div10 fl word_red last_l"><?= $result['season_1'];?></li>
    <li class="div10 fl word_red last_l"><?= $result['season_2'];?></li>
    <li class="div14 fl word_red last_l"><?= $result['total_nums'];?></li>
    <li class="div10 fl word_red last_l"><?= $result['total_nums']?'100%':'0%';?></li>
    <li class="div14 fl word_red last_l"><?= $result['amount'];?></li>
    <li class="div10 fl word_red last_v last_l"><?= $result['total_nums']?'100%':'0%';?></li>
</ul>
<!--总计-->
</div>
<?= $this->render('/common/_footer_order',array());?>