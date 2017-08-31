<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%agent}}".
 *
 * @property integer $agent_id
 * @property string $agent_name
 * @property string $agent_code
 * @property integer $p_order
 */
class AgentModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agent}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['p_order'], 'integer'],
            [['agent_name', 'agent_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'agent_id' => 'Agent ID',
            'agent_name' => 'Agent Name',
            'agent_code' => 'Agent Code',
            'p_order' => 'P Order',
        ];
    }
    /**
    * 获取代理列表
    * @return mixed
    */
    public function getAgent(){
        $result = Yii::$app->cache->get("agent");
        if(empty($result)){
            $result = self::find()->select(['agent_name', 'agent_code'])->asArray()->all();
            Yii::$app->cache->set("agent", $result, 86400);
        }
        return $result;
    }
    /**
     * use
     * order/manage/UserImport
     * 
     * 转换  code => name
     *
     * @return mixed
     */
    public function transAgentCode(){
        $agentList = $this->getAgent();
        foreach($agentList as $val){
            $item[$val['agent_code']."_".$val['agent_name']] = $val['agent_name'];
        }
        return $item;
    }
}
