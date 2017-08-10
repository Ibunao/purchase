<?php

$this->params['breadcrumbs'] = [
    ['label' => '商品管理'],
    ['label' => '商品管理'],
    ['label' => '商品添加'],
];

echo $this->render('_form',['selectFilter'=>$selectFilter, 'action'=>'add']);
?>