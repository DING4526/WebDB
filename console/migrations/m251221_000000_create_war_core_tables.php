<?php

/**
 * Ding 2310724
 * 创建抗战专题的核心数据表（阶段、事件、人物及关联等）
 */

use yii\db\Migration;
class m251221_000000_create_war_core_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 阶段
        $this->createTable('{{%war_stage}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('阶段名称'),
            'start_year' => $this->smallInteger()->null()->comment('起始年份'),
            'end_year' => $this->smallInteger()->null()->comment('结束年份'),
            'description' => $this->text()->null()->comment('描述'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 事件
        $this->createTable('{{%war_event}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(200)->notNull()->comment('事件标题'),
            'event_date' => $this->date()->null()->comment('发生日期'),
            'stage_id' => $this->integer()->null()->comment('所属阶段'),
            'summary' => $this->string(500)->null()->comment('摘要'),
            'content' => $this->text()->null()->comment('详情'),
            'location' => $this->string(200)->null()->comment('地点'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-war_event-stage_id', '{{%war_event}}', 'stage_id');
        $this->addForeignKey('fk-war_event-stage_id', '{{%war_event}}', 'stage_id', '{{%war_stage}}', 'id', 'SET NULL', 'CASCADE');

        // 人物
        $this->createTable('{{%war_person}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('姓名'),
            'role_type' => $this->string(50)->null()->comment('身份类型'),
            'birth_year' => $this->smallInteger()->null()->comment('出生年份'),
            'death_year' => $this->smallInteger()->null()->comment('去世年份'),
            'intro' => $this->string(500)->null()->comment('简介'),
            'biography' => $this->text()->null()->comment('生平'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 事件-人物关联
        $this->createTable('{{%war_event_person}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer()->notNull()->comment('事件'),
            'person_id' => $this->integer()->notNull()->comment('人物'),
            'relation_type' => $this->string(50)->null()->comment('关系'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-war_event_person-event_id', '{{%war_event_person}}', 'event_id');
        $this->createIndex('idx-war_event_person-person_id', '{{%war_event_person}}', 'person_id');
        $this->addForeignKey('fk-war_event_person-event', '{{%war_event_person}}', 'event_id', '{{%war_event}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-war_event_person-person', '{{%war_event_person}}', 'person_id', '{{%war_person}}', 'id', 'CASCADE', 'CASCADE');

        // 媒资
        $this->createTable('{{%war_media}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(20)->notNull()->defaultValue('image')->comment('类型'),
            'path' => $this->string(255)->notNull()->comment('存储路径/URL'),
            'title' => $this->string(200)->null()->comment('标题'),
            'event_id' => $this->integer()->null()->comment('关联事件'),
            'person_id' => $this->integer()->null()->comment('关联人物'),
            'uploaded_at' => $this->integer()->notNull()->comment('上传时间'),
        ], $tableOptions);
        $this->createIndex('idx-war_media-event_id', '{{%war_media}}', 'event_id');
        $this->createIndex('idx-war_media-person_id', '{{%war_media}}', 'person_id');
        $this->addForeignKey('fk-war_media-event', '{{%war_media}}', 'event_id', '{{%war_event}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-war_media-person', '{{%war_media}}', 'person_id', '{{%war_person}}', 'id', 'SET NULL', 'CASCADE');

        // 标签与关联
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique()->comment('标签名'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%war_event_tag}}', [
            'event_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-war_event_tag', '{{%war_event_tag}}', ['event_id', 'tag_id']);
        $this->addForeignKey('fk-war_event_tag-event', '{{%war_event_tag}}', 'event_id', '{{%war_event}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-war_event_tag-tag', '{{%war_event_tag}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%war_person_tag}}', [
            'person_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-war_person_tag', '{{%war_person_tag}}', ['person_id', 'tag_id']);
        $this->addForeignKey('fk-war_person_tag-person', '{{%war_person_tag}}', 'person_id', '{{%war_person}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-war_person_tag-tag', '{{%war_person_tag}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'CASCADE');

        // 留言
        $this->createTable('{{%war_message}}', [
            'id' => $this->primaryKey(),
            'nickname' => $this->string(100)->notNull()->comment('昵称'),
            'content' => $this->text()->notNull()->comment('留言'),
            'target_type' => $this->string(20)->notNull()->comment('event/person'),
            'target_id' => $this->integer()->notNull()->comment('目标ID'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0待审核 1通过 2拒绝'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-war_message-target', '{{%war_message}}', ['target_type', 'target_id']);

        // 访问日志
        $this->createTable('{{%war_visit_log}}', [
            'id' => $this->primaryKey(),
            'target_type' => $this->string(20)->notNull(),
            'target_id' => $this->integer()->notNull(),
            'visited_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-war_visit_log-target', '{{%war_visit_log}}', ['target_type', 'target_id']);
    }

    public function down()
    {
        $this->dropTable('{{%war_visit_log}}');
        $this->dropTable('{{%war_message}}');
        $this->dropForeignKey('fk-war_person_tag-tag', '{{%war_person_tag}}');
        $this->dropForeignKey('fk-war_person_tag-person', '{{%war_person_tag}}');
        $this->dropTable('{{%war_person_tag}}');
        $this->dropForeignKey('fk-war_event_tag-tag', '{{%war_event_tag}}');
        $this->dropForeignKey('fk-war_event_tag-event', '{{%war_event_tag}}');
        $this->dropTable('{{%war_event_tag}}');
        $this->dropTable('{{%tag}}');
        $this->dropForeignKey('fk-war_media-person', '{{%war_media}}');
        $this->dropForeignKey('fk-war_media-event', '{{%war_media}}');
        $this->dropTable('{{%war_media}}');
        $this->dropForeignKey('fk-war_event_person-person', '{{%war_event_person}}');
        $this->dropForeignKey('fk-war_event_person-event', '{{%war_event_person}}');
        $this->dropTable('{{%war_event_person}}');
        $this->dropTable('{{%war_person}}');
        $this->dropForeignKey('fk-war_event-stage_id', '{{%war_event}}');
        $this->dropTable('{{%war_event}}');
        $this->dropTable('{{%war_stage}}');
    }
}
