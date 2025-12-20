<?php

// http://localhost/advanced/backend/web/index.php?r=team-member

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\TeamMember;
use yii\helpers\ArrayHelper;
use common\models\Team;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeamMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '成员管理';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->getUser();
$isRoot = $user && $user->isRoot();

$teamFilter = ArrayHelper::map(Team::find()->all(), 'id', 'name');

$userList = ArrayHelper::map(
    User::find()->orderBy(['id' => SORT_DESC])->all(),
    'id',
    'username'
);

?>
<div class="team-member-index">
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-user"></span>
      成员管理
      <?php if (!$isRoot): ?>
        <span class="label label-default ml10">只读</span>
      <?php endif; ?>
    </div>
    <div class="panel-body">
      <p>
        <?php if ($isRoot): ?>
          <?= Html::a('新增成员', ['create'], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
          <span class="text-muted">仅 root 可新增/编辑成员，当前为只读模式。</span>
        <?php endif; ?>
      </p>

      <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'tableOptions' => ['class' => 'table table-striped table-condensed'],
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [
                  'attribute' => 'team_id',
                  'value' => fn($m) => $m->team ? $m->team->name : '',
                  'filter' => $teamFilter,
              ],
              [
                  'attribute' => 'user_id',
                  'value' => function ($m) {
                      return $m->user ? $m->user->username : '';
                  },
                  'filter' => $userList,
              ],
              'name',
              'student_no',
              [
                  'attribute' => 'status',
                  'value' => fn($m) => TeamMember::getStatusList()[$m->status] ?? $m->status,
                  'filter' => TeamMember::getStatusList(),
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'template' => $isRoot ? '{view} {update} {delete}' : '{view}',
              ],
          ],
      ]); ?>
    </div>
  </div>
</div>
