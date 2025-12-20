<?php

/**
 * Ding 2310724
 * 团队表模型
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "team".
 *
 * @property int $id 主键
 * @property string $name 队名
 * @property string|null $topic 主题/选题
 * @property string|null $intro 团队简介
 * @property int $status 状态 1正常 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 *
 * @property TeamMember[] $teamMembers
 */
class Team extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 1;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_DISABLED => '禁用',
            self::STATUS_DELETED => '删除',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['intro'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['topic'], 'string', 'max' => 200],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'name' => '队名',
            'topic' => '主题/选题',
            'intro' => '团队简介',
            'status' => '状态 1正常 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * Gets query for [[TeamMembers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeamMembers()
    {
        return $this->hasMany(TeamMember::class, ['team_id' => 'id']);
    }
}
