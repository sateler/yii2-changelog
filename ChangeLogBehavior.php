<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace sateler\changelog;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use sateler\changelog\models\Changelog;
use Ramsey\Uuid\Uuid;
use yii\helpers\ArrayHelper;

/**
 * Description of AuditLogBehavior
 *
 * @author rsateler
 */
class ChangeLogBehavior extends Behavior
{
    private $dirtyAttributes = [];
    private $old_values = [];
    private $new_values = [];
    
    public $ignore = [];
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeSave',
            
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }
    
    public function beforeSave($event)
    {
        // If its an existing record save old values
        if(!$this->owner->isNewRecord) {
            $this->old_values = $this->owner->oldAttributes;
        }
        
        $this->new_values = $this->owner->attributes;
        
        // only relevant for updates
        $this->dirtyAttributes = array_keys($this->owner->dirtyAttributes);
    }
    
    public function afterInsert($event)
    {
        $this->dirtyAttributes = array_keys($this->owner->attributes);
        $this->createChangelogs(Changelog::CHANGE_TYPE_CREATE);
    }
    
    public function afterDelete($event)
    {
        $this->dirtyAttributes = array_keys($this->owner->attributes);
        $this->new_values = [];
        $this->createChangelogs(Changelog::CHANGE_TYPE_DELETE);
    }
    
    public function afterUpdate($event)
    {
        $this->createChangelogs(Changelog::CHANGE_TYPE_UPDATE);
    }
    
    private function createChangelogs($change_type)
    {
        $uuid = Uuid::uuid4()->toString();
        $time = time();
        $pk = $this->getPkValue();
        
        foreach($this->dirtyAttributes as $attribute) {
            if(in_array($attribute, $this->ignore)) {
                continue;
            }
            if(ArrayHelper::getValue($this->old_values, $attribute) != ArrayHelper::getValue($this->new_values, $attribute)) {
                $changeLog = new Changelog([
                    'change_uuid' => $uuid,
                    'change_type' => $change_type,
                    'created_at' => $time,
                    'user_id' => Yii::$app->user->id,
                    'table_name' => $this->owner->tableName(),
                    'column_name' => $attribute,
                    'row_id' => $pk,
                    'old_value' => self::sanitizeValue(ArrayHelper::getValue($this->old_values, $attribute)),
                    'new_value' => self::sanitizeValue(ArrayHelper::getValue($this->new_values, $attribute)),
                ]);
                $changeLog->save();
            }
        }
    }
    
    private static function sanitizeValue($value)
    {
        if(is_null($value)) {
            return null;
        }
        
        return mb_substr(trim($value), 0, 255);
    }
    
    private function getPkValue()
    {
        $keys = $this->owner->primaryKey();
        $values = array_values(ArrayHelper::filter($this->owner, $keys));
        return implode("|", $values);
    }
}
