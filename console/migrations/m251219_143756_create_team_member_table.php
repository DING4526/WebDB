<?php

/**
 * Ding 2310724
 * 团队成员表的创建与回滚
 */

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team_member}}`.
 */
class m251219_143756_create_team_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%team_member}}', [
            'id' => $this->primaryKey()->comment('主键'),

            'team_id' => $this->integer()->notNull()->comment('所属团队ID'),
            'user_id' => $this->integer()->null()->comment('关联账号ID(可空)'),

            'name' => $this->string(50)->notNull()->comment('姓名'),
            'student_no' => $this->string(30)->notNull()->unique()->comment('学号'),
            'role' => $this->string(50)->null()->comment('角色/分工'),
            'work_scope' => $this->text()->null()->comment('负责内容'),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态 1正常 0禁用 -1删除'),

            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
        ], $tableOptions);

        // 外键字段索引（必备）
        $this->createIndex('idx-team_member-team_id', '{{%team_member}}', 'team_id');
        $this->createIndex('idx-team_member-user_id', '{{%team_member}}', 'user_id');

        // 外键：team_id -> team.id
        $this->addForeignKey(
            'fk-team_member-team_id',
            '{{%team_member}}',
            'team_id',
            '{{%team}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 外键：user_id -> user.id（可空）
        $this->addForeignKey(
            'fk-team_member-user_id',
            '{{%team_member}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%team_member}} COMMENT='团队成员表'");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // 回滚顺序：先外键 -> 索引 -> 表
        $this->dropForeignKey('fk-team_member-user_id', '{{%team_member}}');
        $this->dropForeignKey('fk-team_member-team_id', '{{%team_member}}');

        $this->dropIndex('idx-team_member-user_id', '{{%team_member}}');
        $this->dropIndex('idx-team_member-team_id', '{{%team_member}}');

        $this->dropTable('{{%team_member}}');
    }
}
