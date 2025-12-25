<?php

/**
 * Ding 2310724
 * KongXianghao 2311439
 * 抗战阶段模型
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 抗战阶段
 *
 * @property int $id
 * @property string $name
 * @property int|null $start_year
 * @property int|null $end_year
 * @property string|null $description
 * @property int $sort_order
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class WarStage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%war_stage}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['start_year', 'end_year', 'sort_order', 'status'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '阶段名称',
            'start_year' => '起始年份',
            'end_year' => '结束年份',
            'description' => '描述',
            'sort_order' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getEvents()
    {
        return $this->hasMany(WarEvent::class, ['stage_id' => 'id'])
            ->orderBy(['event_date' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
