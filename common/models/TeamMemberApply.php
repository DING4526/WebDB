<?php

/**
 * Ding 2310724
 * 团队成员申请模型
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int $team_id
 * @property string $name
 * @property string $student_no
 * @property string|null $email
 * @property string|null $reason
 * @property int $status 0待审批 1通过 2拒绝
 * @property int|null $reviewer_id
 * @property int|null $reviewed_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property User $reviewer
 * @property Team $team
 */
class TeamMemberApply extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public static function tableName()
    {
        return '{{%team_member_apply}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name', 'student_no'], 'required'],
            [['user_id', 'team_id', 'status', 'reviewer_id', 'reviewed_at', 'created_at', 'updated_at'], 'integer'],
            [['reason'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['student_no'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 120],
            [['student_no'], 'unique'],
            [['team_id'], 'required'],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reviewer_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '申请人',
            'team_id' => '目标团队',
            'name' => '姓名',
            'student_no' => '学号',
            'email' => '邮箱',
            'reason' => '申请理由',
            'status' => '状态',
            'reviewer_id' => '审核人',
            'reviewed_at' => '审核时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function statusList()
    {
        return [
            self::STATUS_PENDING => '待审批',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
        ];
    }

    public function getStatusLabel()
    {
        return static::statusList()[$this->status] ?? $this->status;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getReviewer()
    {
        return $this->hasOne(User::class, ['id' => 'reviewer_id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }
}
