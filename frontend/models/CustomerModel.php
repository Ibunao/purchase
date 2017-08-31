<?php

namespace frontend\models;

use Yii;
use frontend\models\PurchaseModel;
use frontend\models\AgentModel;
use yii\data\Pagination;
use yii\db\Query;
/**
 * 客户表
 * This is the model class for table "{{%customer}}".
 *
 * @property string $customer_id
 * @property string $parent_id
 * @property integer $purchase_id
 * @property string $code
 * @property string $relation_code
 * @property string $name
 * @property string $password
 * @property string $mobile
 * @property string $type
 * @property string $province
 * @property string $area
 * @property string $target
 * @property string $disabled
 * @property string $department
 * @property string $leader
 * @property string $leader_name
 * @property string $agent
 * @property string $big_1
 * @property string $big_2
 * @property string $big_3
 * @property string $big_4
 * @property string $big_6
 * @property string $big_1_count
 * @property string $big_2_count
 * @property string $big_3_count
 * @property string $big_4_count
 * @property string $big_6_count
 * @property string $big_count1
 * @property string $big_count2
 * @property string $big_count3
 * @property string $big_count4
 * @property string $login
 */
class CustomerModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'purchase_id', 'login', 'code', 'relation_code', 'name', 'password', 'mobile', 'type', 'target', 'big_1', 'big_2', 'big_3', 'big_4', 'big_6', 'big_1_count', 'big_2_count', 'big_3_count', 'big_4_count', 'big_6_count', 'big_count1', 'big_count2', 'big_count3', 'big_count4', 'disabled', 'province', 'area', 'agent', 'department', 'leader', 'leader_name'], 'safe'],
            // [['parent_id', 'purchase_id', 'login'], 'integer'],
            // [['purchase_id', 'code', 'relation_code', 'name', 'password', 'mobile', 'type'], 'required'],
            // [['target', 'big_1', 'big_2', 'big_3', 'big_4', 'big_6', 'big_1_count', 'big_2_count', 'big_3_count', 'big_4_count', 'big_6_count', 'big_count1', 'big_count2', 'big_count3', 'big_count4'], 'number'],
            // [['disabled'], 'string'],
            // [['code', 'relation_code', 'name', 'mobile', 'type', 'province', 'area', 'agent'], 'string', 'max' => 30],
            // [['password'], 'string', 'max' => 60],
            // [['department', 'leader', 'leader_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'parent_id' => 'Parent ID',
            'purchase_id' => 'Purchase ID',
            'code' => 'Code',
            'relation_code' => 'Relation Code',
            'name' => 'Name',
            'password' => 'Password',
            'mobile' => 'Mobile',
            'type' => 'Type',
            'province' => 'Province',
            'area' => 'Area',
            'target' => 'Target',
            'disabled' => 'Disabled',
            'department' => 'Department',
            'leader' => 'Leader',
            'leader_name' => 'Leader Name',
            'agent' => 'Agent',
            'big_1' => 'Big 1',
            'big_2' => 'Big 2',
            'big_3' => 'Big 3',
            'big_4' => 'Big 4',
            'big_6' => 'Big 6',
            'big_1_count' => 'Big 1 Count',
            'big_2_count' => 'Big 2 Count',
            'big_3_count' => 'Big 3 Count',
            'big_4_count' => 'Big 4 Count',
            'big_6_count' => 'Big 6 Count',
            'big_count1' => 'Big Count1',
            'big_count2' => 'Big Count2',
            'big_count3' => 'Big Count3',
            'big_count4' => 'Big Count4',
            'login' => 'Login',
        ];
    }

    public function login($name, $password)
    {
        return self::find()
            ->where(['code' => $name])
            ->andWhere(['password' => md5(md5($password))])
            ->andWhere(['disabled' => 'false'])
            ->one();
    }
/**
 * 获取下拉框选项
 * @param  array  $select  查询字段
 * @param  array  $groupBy 分组字段
 * @return [type]          [description]
 */
    static function getList($select = ['type'], $groupBy = ['type'])
    {
        $key = 'customer_' . implode('_', $select) . '_list';
        $result = Yii::$app->cache->get($key);
        if (empty($result)) {
            $result = self::find()
                ->select($select)
                ->groupBy($groupBy)
                ->asArray()
                ->all();
            Yii::$app->cache->set($key, $result);
        }
        return $result;
    }

    /**
     * use
     * order/manage/index
     * order/manage/add
     * order/manage/userImport
     * 
     * 显示分类
     * @return mixed
     */
    public function userFilter()
    {
        //客户类型
        $result['type'] = Yii::$app->params['customer_type'];

        //地区
        $result['area'] = Yii::$app->params['customer_area'];

        //部门类别
        $result['department'] = Yii::$app->params['customer_department'];

        //负责人
        $result['leader'] = Yii::$app->params['customer_leader'];

        //订货会类型
        $purchase = new PurchaseModel();
        $purchase_list = $purchase->getPurchase();
        foreach ($purchase_list as $k => $v) {
            $result['purchase'][$v['purchase_id']] = $v['purchase_name'];
        }

        //省份
        $result['province'] = Yii::$app->params['customer_province'];

        //代理名称
        $agent = new AgentModel();
        $result['leader_name'] = $agent->getAgent();
        return $result;
    }

    /**
     * use
     * order/manage/index
     * 
     * 查询检索的数据
     * @param array $data 需要查询的数据数组
     * @param string $pageIndex 当前页码
     * @return mixed 返回查询出来的数组
     */
    public function selectLikeDatabaseOperation($data = [])
    {
        $query = self::find()
            ->alias('c')//设置别名
            ->leftJoin('meet_order as o', 'o.customer_id=c.customer_id')
            ->where(['c.disabled' => 'false']);
        if (!empty($data['code'])) {
            $query->andWhere(['c.code' => $data['code']]);
        }
        if (!empty($data['name'])) {
            $query->andWhere(['like', 'c.name', $data['name']]);
        }
        if (!empty($data['type'])) {
            $query->andWhere(['c.type' => $data['type']]);
        }
        if (!empty($data['purchase_id'])) {
            $query->andWhere(['c.purchase_id' => $data['purchase_id']]);

        }
        if (!empty($data['province'])) {
            $query->andWhere(['c.province' => $data['province']]);
        }

        if(!empty($data['order'])){
            if($data['order'] == '1'){
                $query->andWhere(['<>', 'o.cost_item', 0]);
            }elseif($data['order'] == '2'){
                $query->andWhere(['o.cost_item' => null]);
            }
        }

        if (!empty($data['area'])) {
            $query->andWhere(['c.area' => $data['area']]);
        }
        $newQuery = clone $query;
        $count = $newQuery->count();

        $query->select(['c.customer_id', 'c.name', 'c.mobile', 'c.code', 'c.purchase_id', 'c.target', 'o.cost_item']);
        //分页
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 15]);

        $query->offset($pagination->offset)
            ->limit($pagination->limit);
        $result = $query->asArray()->all();
        return ['result' => $result, 'pagination' => $pagination];
    }
    /**
     * use
     * order/manage/add
     * 
     * 用户新增
     * @param array $data
     * @return mixed
     */
    public function insertDatabaseOperation($data = [])
    {
        if(($data['big_1']+$data['big_2']+$data['big_3']+$data['big_4']+$data['big_6'] == '100') && !empty($data['target'])){
            $data['big_1'] = (string)round($data['target'] * $data['big_1'] /100 , 2);
            $data['big_2'] = (string)round($data['target'] * $data['big_2'] /100 , 2);
            $data['big_3'] = (string)round($data['target'] * $data['big_3'] /100 , 2);
            $data['big_4'] = (string)round($data['target'] * $data['big_4'] /100 , 2);
            $data['big_6'] = (string)round($data['target'] * $data['big_6'] /100 , 2);
        }
        if(empty($data['big_1_count'])){
            $data['big_1_count'] = 100;
        }
        if(empty($data['big_2_count'])){
            $data['big_2_count'] = 100;
        }
        if(empty($data['big_3_count'])){
            $data['big_3_count'] = 100;
        }
        if(empty($data['big_4_count'])){
            $data['big_4_count'] = 100;
        }
        if(empty($data['big_6_count'])){
            $data['big_6_count'] = 100;
        }

        //密码默认手机号码后四位
        if (!empty($data['password'])) {
            $data['password'] = md5(md5($data['password']));
        } else {
            $data['password'] = md5(md5(substr($data['mobile'], -4)));
        }
        $agentResult = (new Query)->from('meet_agent')->where(['agent_code' => $data['leader_name']])->one();
        $data['leader_name'] = '';
        $data['agent'] = '';
        if (!empty($agentResult)) {
            $data['leader_name'] = $agentResult['agent_name'];
            $data['agent'] = $agentResult['agent_code'];
        }
        $data['parent_id'] = 0;
        if (!empty($data['agent'])) {
            if ($agentResult['agent_code'] == $data['code']) {
                $data['parent_id'] = 1;
            }
        }
        $this->setAttributes($data);
        return $this->insert();
    }

    /**
     * use
     * order/manage/export
     * 
     * 获取所有的用户
     * @return mixed
     */
    public function getAllCustomers()
    {
        $result = self::find()->where(['disabled' => 'false'])->asArray()->all();
        return $result;
    }

    /**
     * 转换
     *
     * @return mixed
     */
    public function transAllGuest()
    {
        $result = $this->getAllCustomers();
        if(empty($result)) return [];
        foreach ($result as $val) {
            $item[$val['code']] = $val;
        }
        return $item;
    }
    /**
     * 使用的方法  
     * order/order/index
     * 
     * 获取客户订货指标
     * @param  [type] $param 
     * @param  [type] $isSelect 所选或总订货指标 
     * @return [type]        [description]
     */
    public function getCustomerTargets($param, $isSelect = false)
    {
        $query = self::find()->where(['disabled' => 'false']);

        //客户订货会类型
        if (!empty($param['purchase'])) {
            $query->andWhere(['purchase_id' => $param['purchase']]);
        }
        // 客户类型
        if (!empty($param['type'])) {
            $query->andWhere(['type' => $param['type']]);
        }
        // 获取已选用户的订货指标
        if ($isSelect) {
            // 部门类型
            if (!empty($param['department'])) {
                $query->andWhere(['department' => $param['department']]);
            }
            // 负责人
            if (!empty($param['leader'])) {
                $query->andWhere(['leader' => $param['leader']]);
            }
            // 客户名称
            if (!empty($param['name'])) {
                $query->andWhere(['like', 'name', $param['name']]);
            }
            // 负责人(代理)名字/代码
            if (!empty($param['leader_name'])) {
                $query->andWhere(['or', ['like', 'agent', $param['leader_name']], ['like', 'leader_name', $param['leader_name']]]);
            }
            // 客户代码
            if(!empty($param['code'])){
                $query->andWhere(['code' => $param['code']]);
            }
            // 大区
            if (!empty($param['area'])) {
                $query->andWhere(['area' => $param['area']]);
            }
            // 用户是否登陆过
            if (!empty($param['login'])) {
                if ($param['login'] == 1) {
                    $query->andWhere(['not', ['login' => null]]);
                } elseif ($param['login'] == 2) {
                    $query->andWhere(['login' => null]);
                }
            }
        }
        $result = $query->sum('target');
        return $result;
    }

    /**
     * 使用的方法
     * order/order/docopy
     * 
     * 查询指定code用户信息
     * @param  [type] $code code码
     * @return [type]       [description]
     */
    public function getCustomerInfo($code)
    {
        return $result = self::find()->where(['code' => $code])->asArray()->one();
    }
    
    /**
     * use
     * order/manage/ajax
     * 
     * 检查code是否存在
     * @param  [type] $code 客户代码
     * @return [type]       [description]
     */
    public function checkCode($code)
    {
        $result = self::find()->where(['code'=>$code])->count();
        return $result;
    }
}
