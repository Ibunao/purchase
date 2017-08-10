<?php 
namespace frontend\config;

/**
* 部分公用配置参数
*/
class ParamsClass
{
	//价格带
	public static $priceLevel = 
	[
		1=>'0-99',
		2=>'100-199',
		3=>'200-299',
		4=>'300-399',
		5=>'400-499',
		6=>'500-999',
		7=>'1000-1499',
		8=>'1500-2000',
		9=>'2000以上',
	];
	//页面数据条数，分页
	public static $pageSize = 15;
}