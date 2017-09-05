<?php

$this->params['breadcrumbs'] = [
    ['label' => '商品管理'],
    ['label' => '商品管理'],
    ['label' => '商品复制'],
];

echo $this->render('_form',['selectFilter'=>$selectFilter, 'action'=>'copy', 'param'=>$param]);
?>