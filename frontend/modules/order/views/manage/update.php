<?php

$this->params['breadcrumbs'] = [
    ['label' => '内容管理'],
    ['label' => '客户管理'],
    ['label' => '添加客户'],
];
echo $this->render('_form',array('insert_option'=>$insert_option, 'action'=>'update','param'=>$select_result));
?>