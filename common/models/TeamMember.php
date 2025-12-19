<?php

/**
 * Ding 2310724
 * 团队成员表模型
 */


namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "team_member".
 *
 * @property int $id 主键
 * @property int $team_id 所属团队ID
 * @property int|null $user_id 关联账号ID(可空)
 * @property string $name 姓名
 * @property string $student_no 学号
 * @property string|null $role 角色/分工
 * @property string|null $work_scope 负责内容
 * @property int $status 状态 1正常 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 *
 * @property Team $team
 * @property User $user
 */
class TeamMember extends \yii\db\ActiveRecord
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
        return 'team_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'name', 'student_no'], 'required'],
            [['team_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['work_scope'], 'string'],
            [['name', 'role'], 'string', 'max' => 50],
            [['student_no'], 'string', 'max' => 30],
            [['student_no'], 'unique'],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'team_id' => '所属团队ID',
            'user_id' => '关联账号ID(可空)',
            'name' => '姓名',
            'student_no' => '学号',
            'role' => '角色/分工',
            'work_scope' => '负责内容',
            'status' => '状态 1正常 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * Gets query for [[Team]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
