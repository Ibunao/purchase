<?php
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => ['default/index']],
    ['label' => '默认页'],
];
?>
<style>
    .infobox {
        width: 250px !important;
    }
    .infobox-yellow{
        color:#da4944;
    }
</style>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">
        <i class="icon-remove"></i>
    </button>
    欢迎使用订货会后台系统
    <br/>
</div>
<div class="col-sm-12 infobox-container">
    <div class="infobox infobox-green  ">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= $res['login_nums']; ?>人</span>

            <div class="infobox-content">今日登陆订货人数</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['all_target']) ? $res['all_target'] : 0, 2); ?>元</span>

            <div class="infobox-content">总订货指标</div>
        </div>
    </div>
    <div class="infobox infobox-green">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['all_orders']) ? $res['all_orders'] : 0,2); ?>元</span>

            <div class="infobox-content">已定货未审核金额</div>
        </div>
    </div>
    <div class="infobox infobox-pink">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['confirm_orders']) ? $res['confirm_orders'] : 0,2); ?>
                元</span>

            <div class="infobox-content">已审核订单金额</div>
        </div>
    </div>
    <div class="infobox infobox-blue">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>

        <div class="infobox-data">
            <span class="infobox-data-number">
                <?= $res['all_target_rate'];
                ?>
                %</span>

            <div class="infobox-content">已审核订单达成率</div>
        </div>
    </div>
</div>
<div class="col-xs-12" style="height: 30px ;"></div>
<div class="col-sm-12 infobox-container">
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['jm_target']) ? $res['jm_target'] : 0,2); ?>元</span>

            <div class="infobox-content">加盟订货总指标</div>
        </div>
    </div>
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['jm_orders']) ? $res['jm_orders'] : 0,2); ?>元</span>

            <div class="infobox-content">加盟已订货总金额</div>
        </div>
    </div>
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['jm_active']) ? $res['jm_active'] : 0,2); ?>元</span>

            <div class="infobox-content">加盟已订货未提交金额</div>
        </div>
    </div>
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['jm_confirm']) ? $res['jm_confirm'] : 0,2); ?>元</span>
            <div class="infobox-content">加盟已提交未审核金额</div>
        </div>
    </div>
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['jm_finish']) ? $res['jm_finish'] : 0,2); ?>元</span>

            <div class="infobox-content">加盟已审核订货金额</div>
        </div>
    </div>
    <div class="infobox infobox-red">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= $res['jm_target_rate']; ?>%</span>

            <div class="infobox-content">加盟已审核订单完成率</div>
        </div>
    </div>
</div>
<div class="col-xs-12" style="height: 30px ;"></div>
<div class="col-sm-12 infobox-container">
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['zy_target']) ? $res['zy_target'] : 0,2); ?>元</span>

            <div class="infobox-content">直营订货总指标</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['zy_orders']) ? $res['zy_orders'] : 0,2); ?>元</span>

            <div class="infobox-content">直营已订货总金额</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['zy_active']) ? $res['zy_active'] : 0,2); ?>元</span>

            <div class="infobox-content">直营已订货未提交金额</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['zy_confirm']) ? $res['zy_confirm'] : 0,2); ?>元</span>
            <div class="infobox-content">直营已提交未审核金额</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= number_format(!empty($res['zy_finish']) ? $res['zy_finish'] : 0,2); ?>元</span>

            <div class="infobox-content">直营已审核订货金额</div>
        </div>
    </div>
    <div class="infobox infobox-orange">
        <div class="infobox-icon">
            <i class="icon-shopping-cart"></i>
        </div>
        <div class="infobox-data">
            <span class="infobox-data-number"><?= $res['zy_target_rate']; ?>%</span>

            <div class="infobox-content">直营已审核订单完成率</div>
        </div>
    </div>
</div>


