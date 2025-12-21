<?php

/**
 * 苏奕扬 2311330
 * 抗战人物模型
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 抗战人物
 */
class WarPerson extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_person}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['biography'], 'string'],
            [['birth_year', 'death_year', 'status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['role_type'], 'string', 'max' => 50],
            [['intro'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'role_type' => '身份',
            'birth_year' => '出生年份',
            'death_year' => '去世年份',
            'intro' => '简介',
            'biography' => '生平',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getEventPeople()
    {
        return $this->hasMany(WarEventPerson::class, ['person_id' => 'id']);
    }

    public function getEvents()
    {
        return $this->hasMany(WarEvent::class, ['id' => 'event_id'])
            ->via('eventPeople')
            ->orderBy(['event_date' => SORT_ASC]);
    }

    public function getMedias()
    {
        return $this->hasMany(WarMedia::class, ['person_id' => 'id']);
    }

    public function getCoverImage()
    {
        return $this->hasOne(WarMedia::class, ['person_id' => 'id'])
            ->where(['type' => 'image']);
    }
}
