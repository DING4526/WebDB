<?php
use yii\db\Migration;

/**
 * Ding 2310724
 * 创建抗战专题扩展表：媒资、标签体系、留言、访问日志
 */

class m251221_000002_create_war_extra_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1) 媒资
        $this->createTable('{{%war_media}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(20)->notNull()->defaultValue('image')->comment('类型 image/document'),
            'path' => $this->string(255)->notNull()->comment('存储路径/URL'),
            'title' => $this->string(200)->null()->comment('标题'),
            'event_id' => $this->integer()->null()->comment('关联事件'),
            'person_id' => $this->integer()->null()->comment('关联人物'),
            'uploaded_at' => $this->integer()->notNull()->comment('上传时间'),
        ], $tableOptions);

        $this->createIndex('idx-war_media-event_id', '{{%war_media}}', 'event_id');
        $this->createIndex('idx-war_media-person_id', '{{%war_media}}', 'person_id');
        $this->createIndex('idx-war_media-type_time', '{{%war_media}}', ['type', 'uploaded_at']);

        $this->addForeignKey(
            'fk-war_media-event',
            '{{%war_media}}',
            'event_id',
            '{{%war_event}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-war_media-person',
            '{{%war_media}}',
            'person_id',
            '{{%war_person}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // 2) 标签
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique()->comment('标签名'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 事件-标签
        $this->createTable('{{%war_event_tag}}', [
            'event_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-war_event_tag', '{{%war_event_tag}}', ['event_id', 'tag_id']);
        $this->createIndex('idx-war_event_tag-tag', '{{%war_event_tag}}', 'tag_id');

        $this->addForeignKey(
            'fk-war_event_tag-event',
            '{{%war_event_tag}}',
            'event_id',
            '{{%war_event}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-war_event_tag-tag',
            '{{%war_event_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 人物-标签
        $this->createTable('{{%war_person_tag}}', [
            'person_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-war_person_tag', '{{%war_person_tag}}', ['person_id', 'tag_id']);
        $this->createIndex('idx-war_person_tag-tag', '{{%war_person_tag}}', 'tag_id');

        $this->addForeignKey(
            'fk-war_person_tag-person',
            '{{%war_person_tag}}',
            'person_id',
            '{{%war_person}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-war_person_tag-tag',
            '{{%war_person_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 3) 留言（target_type 约定仅 event/person，数据库层不加 CHECK 以兼容不同 MySQL）
        $this->createTable('{{%war_message}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null()->comment('提交用户ID(可空)'),
            'nickname' => $this->string(100)->notNull()->comment('昵称'),
            'content' => $this->text()->notNull()->comment('留言'),
            'target_type' => $this->string(20)->notNull()->comment('目标类型: event/person'),
            'target_id' => $this->integer()->notNull()->comment('目标ID'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0待审核 1通过 2拒绝'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-war_message-target', '{{%war_message}}', ['target_type', 'target_id']);
        $this->createIndex('idx-war_message-status_time', '{{%war_message}}', ['status', 'created_at']);
        $this->createIndex('idx-war_message-target_status', '{{%war_message}}', ['target_type', 'target_id', 'status']);

        // user_id 索引 + 外键
        $this->createIndex('idx-war_message-user_id', '{{%war_message}}', 'user_id');
        $this->addForeignKey(
            'fk-war_message-user_id',
            '{{%war_message}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // 4) 访问日志
        $this->createTable('{{%war_visit_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null()->comment('访问用户ID(可空)'),
            'target_type' => $this->string(20)->notNull()->comment('目标类型: event/person'),
            'target_id' => $this->integer()->notNull()->comment('目标ID'),
            'visited_at' => $this->integer()->notNull()->comment('访问时间'),
        ], $tableOptions);

        $this->createIndex('idx-war_visit_log-target', '{{%war_visit_log}}', ['target_type', 'target_id']);
        $this->createIndex('idx-war_visit_log-time', '{{%war_visit_log}}', 'visited_at');

        // 新增：user_id 索引 + 外键
        $this->createIndex('idx-war_visit_log-user_id', '{{%war_visit_log}}', 'user_id');
        $this->addForeignKey(
            'fk-war_visit_log-user_id',
            '{{%war_visit_log}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function down()
    {
        // 回滚顺序：先删引用表/外键，再删被引用表

        // 访问日志：外键可能不存在（因为老版本 migration 没创建过）
        $fks = $this->db->schema->getTableForeignKeys('{{%war_visit_log}}');
        if (isset($fks['fk-war_visit_log-user_id'])) {
            $this->dropForeignKey('fk-war_visit_log-user_id', '{{%war_visit_log}}');
        }
        $this->dropTable('{{%war_visit_log}}');

        // 留言：同理
        $fks = $this->db->schema->getTableForeignKeys('{{%war_message}}');
        if (isset($fks['fk-war_message-user_id'])) {
            $this->dropForeignKey('fk-war_message-user_id', '{{%war_message}}');
        }
        $this->dropTable('{{%war_message}}');

        // war_person_tag
        $this->dropForeignKey('fk-war_person_tag-tag', '{{%war_person_tag}}');
        $this->dropForeignKey('fk-war_person_tag-person', '{{%war_person_tag}}');
        $this->dropTable('{{%war_person_tag}}');

        // war_event_tag
        $this->dropForeignKey('fk-war_event_tag-tag', '{{%war_event_tag}}');
        $this->dropForeignKey('fk-war_event_tag-event', '{{%war_event_tag}}');
        $this->dropTable('{{%war_event_tag}}');

        // tag
        $this->dropTable('{{%tag}}');

        // war_media
        $this->dropForeignKey('fk-war_media-person', '{{%war_media}}');
        $this->dropForeignKey('fk-war_media-event', '{{%war_media}}');
        $this->dropTable('{{%war_media}}');
    }
}
