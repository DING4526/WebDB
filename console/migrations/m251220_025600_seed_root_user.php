<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * 初始化一个 root 用户（仅当不存在 root 账号时）
 */
class m251220_025600_seed_root_user extends Migration
{
    public function safeUp()
    {
        $exists = (new Query())
            ->from('{{%user}}')
            ->where(['username' => 'root'])
            ->exists();

        if ($exists) {
            return;
        }

        $now = time();
        $this->insert('{{%user}}', [
            'username' => 'root',
            'email' => 'root@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('root123456'),
            'password_reset_token' => null,
            'status' => 10,
            'role' => 'root',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%user}}', ['username' => 'root']);
    }
}
