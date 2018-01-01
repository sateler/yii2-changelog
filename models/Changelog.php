<?php

namespace sateler\changelog\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "changelog".
 *
 * @property integer $id
 * @property string $change_uuid
 * @property string $change_type
 * @property string $change_type_name
 * @property integer $created_at
 * @property integer $user_id
 * @property string $table_name
 * @property string $column_name
 * @property integer $row_id
 * @property string $old_value
 * @property string $new_value
 */
class Changelog extends \yii\db\ActiveRecord
{
    const CHANGE_TYPE_CREATE = 'create';
    const CHANGE_TYPE_UPDATE = 'update';
    const CHANGE_TYPE_DELETE = 'delete';
    
    public static $types = [
        self::CHANGE_TYPE_CREATE => "Create",
        self::CHANGE_TYPE_UPDATE => "Update",
        self::CHANGE_TYPE_DELETE => "Delete",
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'changelog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['change_uuid', 'change_type', 'created_at', 'table_name', 'column_name'], 'required'],
            [['created_at', 'user_id', 'row_id'], 'integer'],
            [['change_uuid'], 'string', 'max' => 36],
            [['change_type'], 'string', 'max' => 10],
            [['table_name', 'column_name', 'old_value', 'new_value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'change_uuid' => 'Uuid',
            'change_type' => 'Type',
            'created_at' => 'Date',
            'user_id' => 'User',
            'table_name' => 'Table',
            'column_name' => 'Column',
            'row_id' => 'Record',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
        ];
    }

    public function getChange_type_name()
    {
        return ArrayHelper::getValue(self::$types, $this->change_type, $this->change_type);
    }
}
