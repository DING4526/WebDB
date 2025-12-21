<?php

/**
 * Ding 2310724
 * 抗战事件人物关联模型
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 事件与人物关联
 */
class WarEventPerson extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_event_person}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['event_id', 'person_id'], 'required'],
            [['event_id', 'person_id'], 'integer'],
            [['relation_type'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => '事件',
            'person_id' => '人物',
            'relation_type' => '关系',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(WarEvent::class, ['id' => 'event_id']);
    }

    public function getPerson()
    {
        return $this->hasOne(WarPerson::class, ['id' => 'person_id']);
    }
}
