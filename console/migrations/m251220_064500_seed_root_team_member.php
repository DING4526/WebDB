<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * 确保默认团队里包含 root 成员
 */
class m251220_064500_seed_root_team_member extends Migration
{
    public function up()
    {
        $teamId = \Yii::$app->has('teamProvider') ? \Yii::$app->teamProvider->getId() : null;
        if (!$teamId) {
            return;
        }

        $rootId = (new Query())
            ->from('{{%user}}')
            ->where(['username' => 'root'])
            ->select('id')
            ->scalar();

        if (!$rootId) {
            return;
        }

        $existingId = (new Query())
            ->from('{{%team_member}}')
            ->where(['team_id' => $teamId, 'user_id' => $rootId])
            ->select('id')
            ->scalar();

        if ($existingId) {
            $this->update('{{%team_member}}', ['student_no' => '000000'], ['id' => $existingId]);
            return;
        }

        $now = time();
        $this->insert('{{%team_member}}', [
            'team_id' => $teamId,
            'user_id' => $rootId,
            'name' => 'root',
            'student_no' => '000000',
            'role' => 'root',
            'work_scope' => 'system',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        $teamId = \Yii::$app->has('teamProvider') ? \Yii::$app->teamProvider->getId() : null;
        $rootId = (new Query())
            ->from('{{%user}}')
            ->where(['username' => 'root'])
            ->select('id')
            ->scalar();

        if ($teamId && $rootId) {
            $this->delete('{{%team_member}}', ['team_id' => $teamId, 'user_id' => $rootId]);
        }
    }
}
