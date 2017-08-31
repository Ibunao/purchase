<?php
$this->params['breadcrumbs'] = [
    ['label' => '订单'],
    ['label' => '订单导入'],
];
?>
<style>
    .headers {
        padding-bottom: 20px;
        font-size: 16px
    }

    .headers i {
        color: #438eb9;
    }

    #sub {
        display: none;
    }

    #more {
        display: none;
    }

    .padding {
        padding-left: 20px;
    }

    #more span {
        font-weight: bold;
        color: #ff8482;
        padding-right: 20px;;
    }
    table tr td{
        width: 150px;
    }
</style>
<div class="col-xs-12 headers">
    <h3>注意：</h3>

    <p>1、导入文件为 <b class="text-danger">CSV</b> 格式 （<a href="/download/order.csv">我是模板，点我下载</a> 请勿删除模板中的第一行数据）</p>

    <p>2、订单导入需要以下条件且必须正确，否则导入失败</p>

    <p>
        <b>款号<i class="icon-check"></i></b>
        <b>色号<i class="icon-check"></i></b>
        <b>尺码<i class="icon-check"></i></b>
        <b>个数<i class="icon-check"></i></b>
    </p>
    格式如下：
    <p>
        <table border="1" class="text-center">
            <tr>
                <td>款号</td>
                <td>色号</td>
                <td>尺码</td>
                <td>个数<b class="text-danger">(整数)</b></td>
            </tr>
            <tr>
                <td>100000000</td>
                <td>00无色</td>
                <td>M</td>
                <td>2</td>
            </tr>
            <tr>
                <td>100000001</td>
                <td>494浅绿花沙</td>
                <td>75B</td>
                <td>3</td>
            </tr>
        <tr>
            <td>...</td>
            <td>...</td>
            <td>...</td>
            <td>...</td>
        </tr>
        </table>
    </p>
</div>
<div class="col-xs-12" style="border-top: 2px solid #ff8482;">
    <form action="/order/order/import-files" method="post" enctype="multipart/form-data"
          style="margin-top: 20px">
        <div class="form-group">
            <label for="name">上传文件</label>
            <input type="file" class="btn btn-sm btn-info" value="上传文件" name="file">
        </div>
        <div class="form-group">
            <label for="name">输入客户代码</label>
            <input type="text" id="code" placeholder="请输入您要导入的客户代码" style="width: 180px">
            <input type="hidden" id="ucode" name="code" value="">
            <a class="btn btn-sm btn-danger" id="check">点击检查</a>
        </div>
        <div id="more">

        </div>
        <div class="form-group" id="sub">
            <button type="submit" class="btn btn-sm btn-success">上传</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        $("#check").on("click", function () {
            var code = $("#code").val();
            if (code == "") {
                $("#more").append("<p>请输入客户代码</p>");
                return false;
            }
            $("#sub").hide();
            $.ajax({
                type: "get",
                url: "/order/order/ajax-this-customer-exist?code=" + code,
                dataType: "json",
                success: function (data) {
                    if (data.code == 200) {
                        $("#sub").show();
                        $("#ucode").val("").val(data.data.customer_id);
                        $("#more").show().empty().append("<p>您输入的客户信息(请核对后上传):</p> <p>客户代码：<span>" + data.data.code + "</span> 名称：<span>" + data.data.name + "</span> 订货会：<span>" + data.data.purchase_id + "</span> 类型：<span>" + data.data.type + "</span> 省份：<span>" + data.data.province + "</span> 区域：<span>" + data.data.area + "</span> 手机：<span>" + data.data.mobile + "</span>订货目标：<span>" + data.data.target + "</span>订单类型：<span>" + data.data.otype + "</span></p>");
                    } else if (data.code == 400) {
                        $("#more").show().empty().append("<p>对不起，未找到您输入的用户信息，请重试</p>");
                    }
                }
            });
        });
    });
</script>

