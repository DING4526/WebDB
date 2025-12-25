<?php
namespace common\models;

use yii\db\ActiveRecord;

class Event extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%event}}'; // 确保表名正确
    }
}
