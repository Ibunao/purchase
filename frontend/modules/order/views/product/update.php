<?php

$this->params['breadcrumbs'] = [
    ['label' => '商品管理'],
    ['label' => '商品管理'],
    ['label' => '商品修改'],
];

echo $this->render('_form',[
	'selectFilter'=>$selectFilter, 
	'action'=>'update',
	'param' => $param,
	]);
?>