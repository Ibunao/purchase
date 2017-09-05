<?php

$this->params['breadcrumbs'] = [
    ['label' => '商品管理'],
    ['label' => '商品管理'],
    ['label' => '商品导入'],
];
?>
<style>
    .headers p{
        font-size: 16px;
    }
    a{
        font-size: 16px;
    }
    .color a{
        color: #006600;
    }
</style>

<div class="col-xs-12 headers">
    <h3>注意：</h3>
    <h2 class="text-danger">本功能仅限全新的款号/流水号导入，如果系统已经存在的款号/流水号，请直接到商品管理中添加修改</h2>
    <h2>商品导入需要以下条件且<b class="text-success">必须正确</b>，否则导入<b class="text-danger">失败</b></h2>
    <p>
        <b>款号<i class="icon-check"></i></b>
        <b>订货会<i class="icon-check"></i></b>
        <b>品牌<i class="icon-check"></i></b>
        <b>品名<i class="icon-check"></i></b>
        <b>流水号<i class="icon-check"></i></b>
        <b>颜色<i class="icon-check"></i></b>
        <b>尺码<i class="icon-check"></i></b>
        <b>大类<i class="icon-check"></i></b>
        <b>中类<i class="icon-check"></i></b>
        <b>小类<i class="icon-check"></i></b>
        <b>季节<i class="icon-check"></i></b>
        <b>波段<i class="icon-check"></i></b>
        <b>等级<i class="icon-check"></i></b>
        <b>色系<i class="icon-check"></i></b>
        <b>价格带<i class="icon-check"></i></b>
        <b>吊牌价<i class="icon-check"></i></b>
        <b>描述<i class="icon-check"></i></b>
        <b>商品类型<i class="icon-check"></i></b>
    </p>
</div>
<h3 class="text-danger">对于每次导入的商品会出现各种新的错误，请根据系统提示修改后重新上传</h3>
<div>
    <a href="/images/download/page.csv" class="text-info">我是批量导入模板，要我？点我！</a> <b class="text-danger">(请不要删除模板中第一行数据)</b>
</div>

<div class="color">
    <a href="/admin.php?r=order/product/ExportColor" class="text-success">我是颜色色号对照表，要我？点我！</a>
</div>

<div class="row" style="margin-top: 20px">
    <form method="post" enctype="multipart/form-data" action="/order/product/ImportFiles">
        <div class="form-group">
            <label for="name">上传文件</label>
            <input type="file" class="btn btn-sm btn-info" value="上传文件" name="file">
        </div>
        <div class="form-group">
            <button class="btn btn-sm btn-danger">立即导入</button>
        </div>
    </form>
</div>
