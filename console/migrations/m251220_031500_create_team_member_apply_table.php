<?php

use yii\db\Migration;

/**
 * 申请成为成员的审批表
 */
class m251220_031500_create_team_member_apply_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%team_member_apply}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string(50)->notNull(),
            'student_no' => $this->string(30)->notNull(),
            'email' => $this->string(120),
            'reason' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0), // 0待审批 1通过 2拒绝
            'reviewer_id' => $this->integer(),
            'reviewed_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-team_member_apply-status', '{{%team_member_apply}}', 'status');
        $this->createIndex('idx-team_member_apply-student_no', '{{%team_member_apply}}', 'student_no', true);
        $this->addForeignKey('fk-team_member_apply-user', '{{%team_member_apply}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-team_member_apply-reviewer', '{{%team_member_apply}}', 'reviewer_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-team_member_apply-reviewer', '{{%team_member_apply}}');
        $this->dropForeignKey('fk-team_member_apply-user', '{{%team_member_apply}}');
        $this->dropTable('{{%team_member_apply}}');
    }
}
