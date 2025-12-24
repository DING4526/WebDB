<?php

/**
 * Ding 2310724
 * liyu 2311591
 * 抗战媒资模型
 */

namespace common\models;

use yii\db\ActiveRecord;

class WarMedia extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_media}}';
    }

    public function rules()
    {
        return [
            [['type', 'path'], 'required'],
            [['event_id', 'person_id', 'uploaded_at'], 'integer'],
            ['type', 'in', 'range' => ['image', 'document', 'article']], 
            [['path'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 200],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'path' => '路径',
            'title' => '标题',
            'event_id' => '事件',
            'person_id' => '人物',
            'uploaded_at' => '上传时间',
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
