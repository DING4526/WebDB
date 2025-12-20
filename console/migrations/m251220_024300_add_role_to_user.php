<?php

use yii\db\Migration;

/**
 * 为 user 表增加角色字段（root/member/user）
 */
class m251220_024300_add_role_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->string(20)->notNull()->defaultValue('user')->after('email'));
        $this->createIndex('idx-user-role', '{{%user}}', 'role');

        // 将首个用户（常为初始管理员）标记为 root，其他现存用户标记为 member
        $firstUserId = (new \yii\db\Query())->from('{{%user}}')->min('id');
        if ($firstUserId) {
            $this->update('{{%user}}', ['role' => 'root'], ['id' => $firstUserId]);
            $this->update('{{%user}}', ['role' => 'member'], ['and', ['<>', 'id', $firstUserId], ['role' => 'user']]);
        }
    }

    public function safeDown()
    {
        $this->dropIndex('idx-user-role', '{{%user}}');
        $this->dropColumn('{{%user}}', 'role');
    }
}
