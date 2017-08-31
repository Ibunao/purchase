<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ; ?>/css/chosen.css"/>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ; ?>/css/datepicker.css"/>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ; ?>/css/bootstrap-timepicker.css"/>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ; ?>/css/daterangepicker.css"/>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ; ?>/css/colorpicker.css"/>
<style>
    .form-group {
        margin-bottom: 5px;
    }

    .billContainer2 {
        border: 1px solid #cfcfcf;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        margin-bottom: 5px;
        height: auto;
        padding: 10px 20px 10px 20px;
    }
</style>
<div class="col-xs-12"
     style=" border: 1px solid #cfcfcf;  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);margin-bottom: 5px;padding: 10px 10px 10px 10px;">
    <form action="" method="get">
        <input type="hidden" name="r" value="order/order/index">

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">订货会: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[purchase]">
                    <option value="">请选择</option>
                    <?php foreach ($selectOption['purchase'] as $k => $v) { ?>
                        <option value="<?= $v['purchase_id'] ?>"
                                <?php if (!empty($params['purchase']) && $params['purchase'] == $v['purchase_id']){ ?>selected <?php } ?> ><?= $v['purchase_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">客户类型: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[type]">
                    <option value="">请选择</option>
                    <?php foreach ($selectOption['customer_type'] as $k => $v) { ?>
                        <option value="<?= $v['type'] ?>"
                                <?php if (!empty($params['type']) && $params['type'] == $v['type']){ ?>selected <?php } ?> ><?= $v['type']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">部门类型: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[department]">
                    <option value="">请选择</option>
                    <?php foreach ($selectOption['customer_department'] as $k => $v) { ?>
                        <option value="<?= $v['department'] ?>"
                                <?php if (!empty($params['department']) && $params['department'] == $v['department']){ ?>selected <?php } ?> ><?= $v['department']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">大区: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[area]">
                    <option value="">请选择</option>
                    <?php foreach ($selectOption['customer_area'] as $k => $v) { ?>
                        <option value="<?= $v['area'] ?>"
                                <?php if (!empty($params['area']) && $params['area'] == $v['area']){ ?>selected <?php } ?> ><?= $v['area']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">负责人: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[leader]">
                    <option value="">请选择</option>
                    <?php foreach ($selectOption['customer_leader'] as $k => $v) { ?>
                        <option value="<?= $v['leader'] ?>"
                                <?php if (!empty($params['leader']) && $params['leader'] == $v['leader']){ ?>selected <?php } ?> ><?= $v['leader']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <input type="hidden" name="param[order]"
               value="<?= isset($params['order']) ? $params['order'] : 'o.cost_item'; ?>">
        <input type="hidden" name="page" value="<?= isset($params['page']) ? $params['page'] : '1'; ?>">
        <input type="hidden" name="param[download]" value="0">

        <div class="col-sm-4 form-group" style="height: 32px">
            <label class="col-sm-4 control-label no-padding-right">代理名/代理代码: </label>

            <div class="col-sm-8 no-padding-left">
                <input type="text" class="col-xs-10" name="param[leader_name]"
                       value="<?= isset($params['leader_name']) ? $params['leader_name'] : ''; ?>">
            </div>
        </div>

        <div class="col-sm-4 form-group" style="height: 32px">
            <label class="col-sm-4 control-label no-padding-right">客户代码: </label>

            <div class="col-sm-8 no-padding-left">
                <input type="text" class="col-xs-10" name="param[code]"
                       value="<?= isset($params['code']) ? $params['code'] : ''; ?>">
            </div>
        </div>

        <div class="col-sm-4 form-group" style="height: 32px">
            <label class="col-sm-4 control-label no-padding-right">客户名称: </label>

            <div class="col-sm-8 no-padding-left">
                <input type="text" class="col-xs-10" name="param[name]"
                       value="<?= isset($params['name']) ? $params['name'] : ''; ?>">
            </div>
        </div>

        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">显示客户: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[login]">
                    <option value="">请选择</option>
                    <option value="1" <?php if (isset($params['login']) && $params['login'] == "1") {
                        echo "selected";
                    } ?>>仅显示登陆用户
                    </option>
                    <option value="2" <?php if (isset($params['login']) && $params['login'] == "2") {
                        echo "selected";
                    } ?>>仅显示未登陆用户
                    </option>
                </select>
            </div>
        </div>
        <div class="col-sm-4 form-group">
            <label class="col-sm-4 control-label no-padding-right">提交状态: </label>

            <div class="col-sm-8  no-padding-left">
                <select class="form-control" name="param[status]">
                    <option value="">请选择</option>
                    <option value="active" <?php if (isset($params['status']) && $params['status'] == "active") {
                        echo "selected";
                    } ?>>未提交
                    </option>
                    <option value="confirm" <?php if (isset($params['status']) && $params['status'] == "confirm") {
                        echo "selected";
                    } ?>>已提交未审核
                    </option>
                    <option value="finish" <?php if (isset($params['status']) && $params['status'] == "finish") {
                        echo "selected";
                    } ?>>审核完成
                    </option>
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-sm btn-primary gosubmit">
                &nbsp;&nbsp;查询&nbsp;&nbsp;
            </button>

            <button type="button" class="btn btn-sm btn-danger " onclick="location.href='/order/order/copy'"
                    value="">订单复制
            </button>
        </div>
    </form>
</div>

<div class="table-header col-xs-12">

    <lable class="col-sm-12">排序：
        <button class="btn btn-sm <?php if (isset($params['order'])) {
            echo ($params['order'] == 'o.cost_item') ? 'btn-info' : 'btn-primary';
        } else {
            echo 'btn-info';
        } ?>  order" data-val="o.cost_item">
            订货金额
        </button>
        <button class="btn btn-sm <?php if (isset($params['order'])) {
            echo ($params['order'] == 'rate') ? 'btn-info' : 'btn-primary';
        } else {
            echo 'btn-primary';
        } ?>  order" data-val="rate">
            达成率
        </button>
        <button class="btn btn-sm <?php if (isset($params['order'])) {
            echo ($params['order'] == 'c.target') ? 'btn-info' : 'btn-primary';
        } else {
            echo 'btn-primary';
        } ?>  order" data-val="c.target">
            指标
        </button>
        订单项:
        <?php if(Yii::$app->params['order_include']){ ?>
            <a href="/order/order/import">
                <button class="btn btn-sm  btn-primary upload" data-val="2">
                    订单导入
                </button>
            </a>
        <?php } ?>
        <button class="btn btn-sm  btn-primary download" data-val="1">
            订单导出
        </button>
        <button type="button" onclick="location.href='/order/order/export-master'" class="btn btn-sm btn-primary" value="">
            合并订单导出
        </button>
        <?php foreach(Yii::$app->params['output_discount'] as $key => $val){ ?>
            <a href="/order/order/discount?type=<?= $key; ?>">
                <button class="btn btn-sm btn-primary">
                    <?= $val; ?>折扣导出
                </button>
            </a>
        <?php } ?>
        <button type="button" onclick="tip();" class="btn btn-danger btn-primary" value="">
            商品订单明细
        </button>
    </lable>

    <!--S 汇总-->
    <label class="col-sm-3">总订货指标：<b><?= number_format($statistics['target_sum'], 2); ?></b></label>
    <label class="col-sm-3">预期已订货金额：<b><?= number_format($statistics['amount'], 2); ?></b></label>
    <label class="col-sm-3">预期达成率：<b><?php $target_sum = floatval($statistics['target_sum']);
            if (!empty($statistics['amount']) && !empty($target_sum)) {
                echo number_format($statistics['amount'] / $target_sum * 100, 2);
            } else {
                echo "0.00";
            } ?>%</b>
    </label>
    <label class="col-sm-3">实际已订货金额：<b><?= number_format($statistics['amount_really'], 2); ?></b></label>
    <label class="col-sm-3">实际达成率：<b><?php $target_sum = floatval($statistics['target_sum']);
        if (!empty($statistics['amount_really']) && !empty($target_sum)) {
            echo number_format($statistics['amount_really'] / $target_sum * 100, 2);
        } else {
            echo "0.00";
        } ?>%</b></label>
</div>

<div class="table-header col-xs-12">

    <!--S 汇总-->
    <label class="col-sm-4">已选客户合并指标：<b><?= number_format($statistics['choose_target_sum'], 2); ?></b></label>
    <label class="col-sm-4">预期达成率：<b><?php
            $choose_target_sum = floatval($statistics['choose_target_sum']);
            if (!empty($statistics['amount']) && !empty($choose_target_sum)) {
                echo number_format($statistics['amount'] / $choose_target_sum * 100, 2);
            } else {
                echo "0.00";
            } ?>%</b></label>
    <label class="col-sm-4">实际达成率：<b><?php
            $choose_target_sum = floatval($statistics['choose_target_sum']);
            if (!empty($statistics['amount_really']) && !empty($choose_target_sum)) {
                echo number_format($statistics['amount_really'] / $choose_target_sum * 100, 2);
            } else {
                echo "0.00";
            } ?>%</b></label>
</div>

<script>
    $().ready(function () {
        $('.gosubmit').click(function () {
            var view_val = $(this).attr('data-val');
            if (view_val == '2') {
                //等于2清空page 当前页码
                $("input[name='page']").val(1);

            } else {
                //设置 显示方式
                $("input[name='param[view]']").val(view_val);
            }

            $("form").eq(0).submit();
        });

        $('.order').click(function () {
            var order_val = $(this).attr('data-val');
            $("input[name='param[order]']").val(order_val);
            $(this).siblings().removeClass('btn-info').addClass('btn-primary');
            $(this).addClass('btn-info');

            $("form").eq(0).submit();
        });

        $('.download').click(function () {
            var val = $(this).attr('data-val');
            $("input[name='param[download]']").val(val);
            $("form").eq(0).submit();
            $("input[name='param[download]']").val(0);
        });
    });

    function tip(){
        var con = confirm('此操作会让页面停止响应至少十分钟，是否继续？');
        if(con){
            alert('点击后请勿刷新页面，等待网页响应后xls自动会下载！切记勿动，勿刷新！！！！');
            location.href='/order/order/download-order-items-in-order-items-table';
        }
    }
</script>