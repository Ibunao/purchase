<?php
use yii\helpers\Url;
$this->params['breadcrumbs'] = [
    ['label' => '内容管理'],
    ['label' => '客户管理'],
    ['label' => '批量导入客户'],
];

?>
<style>
    .headers p{
        font-size: 16px;
    }
    a{
        font-size: 16px;
    }
</style>

<div class="col-xs-12 headers">
    <h3>注意：</h3>
    <p>请先检查上传表格文件在进行此操作</p>
    <p class="text-danger">如有导入表格中的<b class="text-info">客户代码</b>或者<b class="text-info">代理代码</b>是 <b class="text-info">001</b> 等前几个数字为 <b>0</b>的数字,请在数字前加入单引号 如 <b class="text-info">'001</b> , 此项为不可控，所以请必须要注意</p>
    <h2>客户批量导入需要以下条件且<b class="text-success">必须正确</b>,不可有重复，不可有错误，否则导入<b class="text-danger">失败</b></h2>
    <p>
        <b class="col-xs-2">客户代码<i class="icon-check"></i></b>
        <b class="col-xs-2">客户名称<i class="icon-check"></i></b>
        <b class="col-xs-2">手机<i class="icon-check"></i></b>
        <b class="col-xs-2">订货会<i class="icon-check"></i></b>
        <b class="col-xs-2">部门类别<i class="icon-check"></i></b>
        <b class="col-xs-2">客户类型<i class="icon-check"></i></b>
        <b class="col-xs-2">区域<i class="icon-check"></i></b>
        <b class="col-xs-2">省份<i class="icon-check"></i></b>
        <b class="col-xs-2">负责人<i class="icon-check"></i></b>
        <b class="col-xs-2">代理名称<i class="icon-check"></i></b>
        <b class="col-xs-2">代理代码<i class="icon-check"></i></b>
        <b class="col-xs-2">客户关系代码<i class="icon-check"></i></b>
        <b class="col-xs-2">总订货指标<i class="icon-check"></i></b>
        <b class="col-xs-2">订货指标：服装<i class="icon-check"></i></b>
        <b class="col-xs-2">订货指标：家居<i class="icon-check"></i></b>
        <b class="col-xs-2">订货指标：防辐射<i class="icon-check"></i></b>
        <b class="col-xs-2">订货指标：备品<i class="icon-check"></i></b>
        <b class="col-xs-2">订货指标：化妆品<i class="icon-check"></i></b>
        <b class="col-xs-2">服装折扣<i class="icon-check"></i></b>
        <b class="col-xs-2">家居折扣<i class="icon-check"></i></b>
        <b class="col-xs-2">防辐射折扣<i class="icon-check"></i></b>
        <b class="col-xs-2">备品折扣<i class="icon-check"></i></b>
        <b class="col-xs-2">化妆品折扣<i class="icon-check"></i></b>
    </p>
</div>
<a href="<?=Url::to(['/download/customer.xls']) ;?>">我是模板，要我？点我！</a>
<div class="row" style="margin-top: 20px">
    <form method="post" enctype="multipart/form-data" action="<?=Url::to(['/order/manage/user-import']) ;?>">
        <div class="form-group">
            <label for="name">上传文件</label>
            <input type="file" class="btn btn-sm btn-info" value="上传文件" name="file">
        </div>
        <div class="form-group">
            <button class="btn btn-sm btn-danger">立即导入</button>
        </div>
    </form>
</div>
