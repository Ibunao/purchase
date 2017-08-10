<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%admin_users}}".
 *
 * @property string $user_id
 * @property string $status
 * @property string $name
 * @property string $lastlogin
 * @property string $truename
 * @property string $config
 * @property string $favorite
 * @property string $super
 * @property string $lastip
 * @property string $logincount
 * @property string $disabled
 * @property string $op_no
 * @property string $password
 * @property string $memo
 * @property string $role
 */
class AdminUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'lastlogin', 'logincount'], 'integer'],
            [['status', 'config', 'favorite', 'super', 'disabled', 'memo'], 'string'],
            [['name', 'truename'], 'string', 'max' => 30],
            [['lastip', 'role'], 'string', 'max' => 20],
            [['op_no'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'status' => 'Status',
            'name' => 'Name',
            'lastlogin' => 'Lastlogin',
            'truename' => 'Truename',
            'config' => 'Config',
            'favorite' => 'Favorite',
            'super' => 'Super',
            'lastip' => 'Lastip',
            'logincount' => 'Logincount',
            'disabled' => 'Disabled',
            'op_no' => 'Op No',
            'password' => 'Password',
            'memo' => 'Memo',
            'role' => 'Role',
        ];
    }
}
