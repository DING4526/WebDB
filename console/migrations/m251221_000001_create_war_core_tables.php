<?php
use yii\db\Migration;

/**
 * Ding 2310724
 * 创建抗战专题核心表：阶段、事件、人物、事件-人物关联
 */

class m251221_000001_create_war_core_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1) 阶段
        $this->createTable('{{%war_stage}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('阶段名称'),
            'start_year' => $this->smallInteger()->null()->comment('起始年份'),
            'end_year' => $this->smallInteger()->null()->comment('结束年份'),
            'description' => $this->text()->null()->comment('描述'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态 0禁用 1启用'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-war_stage-status_sort', '{{%war_stage}}', ['status', 'sort_order']);

        // 2) 事件
        $this->createTable('{{%war_event}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(200)->notNull()->comment('事件标题'),
            'event_date' => $this->date()->null()->comment('发生日期'),
            'stage_id' => $this->integer()->null()->comment('所属阶段'),
            'summary' => $this->string(500)->null()->comment('摘要'),
            'content' => $this->text()->null()->comment('详情'),
            'location' => $this->string(200)->null()->comment('地点'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态 0草稿 1发布'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 高频索引：按阶段/时间轴/发布态查询
        $this->createIndex('idx-war_event-stage_id', '{{%war_event}}', 'stage_id');
        $this->createIndex('idx-war_event-event_date', '{{%war_event}}', 'event_date');
        $this->createIndex('idx-war_event-status_date', '{{%war_event}}', ['status', 'event_date']);

        $this->addForeignKey(
            'fk-war_event-stage_id',
            '{{%war_event}}',
            'stage_id',
            '{{%war_stage}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // 3) 人物
        $this->createTable('{{%war_person}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('姓名'),
            'role_type' => $this->string(50)->null()->comment('身份类型'),
            'birth_year' => $this->smallInteger()->null()->comment('出生年份'),
            'death_year' => $this->smallInteger()->null()->comment('去世年份'),
            'intro' => $this->string(500)->null()->comment('简介'),
            'biography' => $this->text()->null()->comment('生平'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('状态 0草稿 1发布'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 高频索引：人物筛选/展示
        $this->createIndex('idx-war_person-status_role', '{{%war_person}}', ['status', 'role_type']);
        $this->createIndex('idx-war_person-name', '{{%war_person}}', 'name');

        // 4) 事件-人物关联（多对多）
        $this->createTable('{{%war_event_person}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer()->notNull()->comment('事件'),
            'person_id' => $this->integer()->notNull()->comment('人物'),
            'relation_type' => $this->string(50)->null()->comment('关系(参与/指挥/见证等)'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-war_event_person-event_id', '{{%war_event_person}}', 'event_id');
        $this->createIndex('idx-war_event_person-person_id', '{{%war_event_person}}', 'person_id');

        // 关键修复：防止重复绑定同一人物
        $this->createIndex(
            'ux-war_event_person-event_person',
            '{{%war_event_person}}',
            ['event_id', 'person_id'],
            true
        );

        $this->addForeignKey(
            'fk-war_event_person-event',
            '{{%war_event_person}}',
            'event_id',
            '{{%war_event}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-war_event_person-person',
            '{{%war_event_person}}',
            'person_id',
            '{{%war_person}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        // 回滚顺序：先引用表/外键，再主体表

        // war_event_person
        $this->dropForeignKey('fk-war_event_person-person', '{{%war_event_person}}');
        $this->dropForeignKey('fk-war_event_person-event', '{{%war_event_person}}');
        $this->dropTable('{{%war_event_person}}');

        // war_event
        $this->dropForeignKey('fk-war_event-stage_id', '{{%war_event}}');
        $this->dropTable('{{%war_event}}');

        // war_person
        $this->dropTable('{{%war_person}}');

        // war_stage
        $this->dropTable('{{%war_stage}}');
    }
}
