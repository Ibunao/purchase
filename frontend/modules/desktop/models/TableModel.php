<?php 
namespace frontend\modules\desktop\models;

use yii\db\Query;
/**
* 综合数据查询
* @user ding
* @date(2017.07.27)
*/
class TableModel
{
	/**
	 * 获取指定时间登陆的用户量		今日
	 * @return [type] [description]
	 */
	public function getAllNewLogin()
	{
		$begin = strtotime(date('Y-m-d 00:00:00'));
		$end = time();
		$row = (new Query)
			->from('meet_customer')
			->where(['between','login', $begin, $end])
			->groupBy(['customer_id'])
			->count();
		return $row;
	}
	/**
	 * 获取订单金额
	 * @param  string $today 
	 * @return [type]        [description]
	 */
	public function getAllOrder($finish = '')
	{
		//目前只有两种状态 finish已审核完成 active 正常订单  
		$row = (new Query)
			->from('meet_order')
			->where(['disabled' => 'false'])
			->andFilterWhere(['status' => $finish])
			->sum('cost_item');
		return $row;
	}
	/**
	 * 获取指标
	 * @param  string $type 类型 直营或客户
	 * @return [type]       指标
	 */		
	public function getAllUserTarget($type='')
	{
		$row = (new Query)
			->from('meet_customer')
			->where(['disabled' => 'false'])
			->andFilterWhere(['type' => $type])
			->sum('target');
		return $row;
	}
	/**
	 * 不同类型用户在不同状态下的订单金额
	 * @param  string $type 客户类型 客户和直营
	 * @param  string $status 状态 finish、active、finish
	 * @return [type]       [description]
	 */
	public function getOrderNum($type='', $status='')
	{
		$row = (new Query)
			->from('meet_order as o')
			->leftJoin('meet_customer as c', 'c.customer_id=o.customer_id')
			->where(['c.disabled' => 'false'])
			->andFilterWhere(['c.type' => $type])
			->andFilterWhere(['o.status' => $status])
			->sum('o.cost_item');
		return $row;
	}
}

?>