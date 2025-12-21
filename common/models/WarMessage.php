<?php

/**
 * Ding 2310724
 * 抗战留言模型
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class WarMessage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_message}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['nickname', 'content', 'target_type', 'target_id'], 'required'],
            [['content'], 'string'],
            [['target_id', 'status'], 'integer'],
            [['nickname'], 'string', 'max' => 100],
            [['target_type'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nickname' => '昵称',
            'content' => '留言',
            'target_type' => '目标类型',
            'target_id' => '目标ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
