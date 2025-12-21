<?php

/**
 * Ding 2310724
 * 抗战访问日志模型
 */

namespace common\models;

use yii\db\ActiveRecord;

class WarVisitLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_visit_log}}';
    }

    public function rules()
    {
        return [
            [['target_type', 'target_id', 'visited_at'], 'required'],
            [['target_id', 'visited_at'], 'integer'],
            [['target_type'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target_type' => '目标类型',
            'target_id' => '目标ID',
            'visited_at' => '访问时间',
        ];
    }
}
