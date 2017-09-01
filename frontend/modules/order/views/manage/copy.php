<?php 
$this->params['breadcrumbs'] = [
    ['label' => '内容管理'],
    ['label' => '客户管理'],
    ['label' => '客户复制'],
];

echo $this->render('_form',array('insert_option'=>$insert_option, 'action'=>'copy','param'=>$select_result));
?>