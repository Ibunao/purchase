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
    public function getList()
    {
        return self::find()
            ->select(['type',])
            ->groupBy(['type'])
            ->asArray()
            ->all();
    }

    /**
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
}
