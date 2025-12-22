<?php

/**
 * Ding 2310724
 * 抗战事件模型
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 抗战事件
 */
class WarEvent extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_event}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['stage_id', 'status'], 'integer'],
            [['event_date'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['summary'], 'string', 'max' => 500],
            [['location'], 'string', 'max' => 200],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'event_date' => '日期',
            'stage_id' => '阶段',
            'summary' => '摘要',
            'content' => '详情',
            'location' => '地点',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getStage()
    {
        return $this->hasOne(WarStage::class, ['id' => 'stage_id']);
    }

    public function getEventPeople()
    {
        return $this->hasMany(WarEventPerson::class, ['event_id' => 'id']);
    }

    public function getPeople()
    {
        return $this->hasMany(WarPerson::class, ['id' => 'person_id'])
            ->via('eventPeople');
    }

    public function getMedias()
    {
        return $this->hasMany(WarMedia::class, ['event_id' => 'id']);
    }
}
