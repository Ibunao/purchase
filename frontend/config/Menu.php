<?php 
namespace frontend\config;

/**
* 目录
*/
class Menu
{
public static $aclList = 
[
	'order'=>
	[
	    'name'=>'订单',
	    'ctl'=>
	    [
	        [
	            'name'=>'订单统计',
	            'list_ctl'=>['default','order','cache','manage','product'],
	            'act'=>
	            [
	                'default'=>
	                [
	                    'name'=>'商品订单汇总',
	                    'default_id'=>'index',
	                    'list_act'=>[
	                    	'index'=>'商品订单汇总',
	                    	'update'=>'订单编辑',
	                    	'delete'=>'删除订单'
	                	]
	            	],
	                'order'=>
	                [
	                    'name'=>'客户订单汇总',
	                    'default_id'=>'index',
	                    'list_act'=>
	                    [
	                    	'index'=>'客户订单汇总',
	                    	'check'=>'订单编辑',
	                    	'delete'=>'删除订单',
	                    	'import'=>'订单导入'
	                    ]
	                ],

	                'manage'=>
	                [
	                    'name'=>'客户管理',
	                    'default_id'=>'index',
	                    'list_act'=>
	                    [
	                    	'index'=>'客户管理',
	                    	'add'=>'客户添加'
	                    ]
	                ],
	                'product'=>
	                [
	                    'name'=>'商品管理',
	                    'default_id'=>'index',
	                    'list_act'=>
	                    [
	                    	'index'=>'商品管理',
	                    	'add'=>'商品添加'
	                    ]
	                ],
	            ]

	        ]
	    ]
	],
];
}