<?php

/**
 * Ding 2310724
 * 团队表的创建与回滚
 */

use yii\db\Migration;

/**
 * Handles the creation of table `{{%team}}`.
 */
class m251219_140834_create_team_table extends Migration
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

        $this->createTable('{{%team}}', [
            'id' => $this->primaryKey()->comment('主键'),

            'name' => $this->string(100)->notNull()->unique()->comment('队名'),
            'topic' => $this->string(200)->null()->comment('主题/选题'),
            'intro' => $this->text()->null()->comment('团队简介'),

            // 防火防盗防误删：1正常 0禁用 -1删除
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态 1正常 0禁用 -1删除'),

            // 时间戳（int），配合 TimestampBehavior 自动填
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
        ], $tableOptions);

        // 表注释（MySQL）
        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%team}} COMMENT='团队表'");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%team}}');
    }
}
