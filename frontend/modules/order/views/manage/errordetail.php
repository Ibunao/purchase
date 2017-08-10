<?php
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => '内容管理'],
    ['label' => '客户管理'],
    ['label' => '客户批量导入检查结果'],
];
?>
<style>
    .info p{
        font-size: 15px;
    }
    .info p b{
        font-size: 18px;
        color: red;
    }
    .info span{
        padding-right: 20px;
    }
</style>
<div class="info">
    <h3>下面是错误的问题，请根据下面提示修改EXCEL文件后再试</h3>
    <a href="<?=Url::to(['/order/manage/import']) ;?>"><button class="btn btn-sm btn-danger">点我重试</button></a>
    <?php if($error){  echo $error;  } ?>
</div>