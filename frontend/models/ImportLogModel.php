<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%import_log}}".
 *
 * @property string $id
 * @property string $type
 * @property string $pre_key
 * @property string $import_file
 * @property integer $insert_time
 */
class ImportLogModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%import_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string'],
            [['import_file', 'insert_time'], 'required'],
            [['insert_time'], 'integer'],
            [['pre_key'], 'string', 'max' => 50],
            [['import_file'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'pre_key' => 'Pre Key',
            'import_file' => 'Import File',
            'insert_time' => 'Insert Time',
        ];
    }
}
