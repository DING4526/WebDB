<?php

use yii\db\Migration;

class m251220_045600_add_team_to_team_member_apply extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%team_member_apply}}', 'team_id', $this->integer()->notNull()->defaultValue(0)->after('user_id'));
        $this->createIndex('idx-team_member_apply-team_id', '{{%team_member_apply}}', 'team_id');
        $this->addForeignKey('fk-team_member_apply-team', '{{%team_member_apply}}', 'team_id', '{{%team}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-team_member_apply-team', '{{%team_member_apply}}');
        $this->dropIndex('idx-team_member_apply-team_id', '{{%team_member_apply}}');
        $this->dropColumn('{{%team_member_apply}}', 'team_id');
    }
}
