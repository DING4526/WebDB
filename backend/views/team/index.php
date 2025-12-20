<?php

// http://localhost/advanced/backend/web/index.php?r=team

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\Team;
use common\models\TeamMember;
use yii\helpers\ArrayHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $team common\models\Team */
/* @var $memberSearchModel backend\models\TeamMemberSearch */
/* @var $memberDataProvider yii\data\ActiveDataProvider */
/* @var $isRoot bool */

$this->title = '团队管理';
$this->params['breadcrumbs'][] = $this->title;
$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
?>
<div class="team-index">

  <div class="panel panel-info">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-briefcase"></span>
      团队信息（单团队模式）
      <?php if (!$isRoot): ?><span class="label label-default ml10">只读</span><?php endif; ?>
    </div>
    <div class="panel-body">
      <?php if ($isRoot): ?>
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal']]); ?>
          <?= $form->field($team, 'name')->textInput(['maxlength' => true]) ?>
          <?= $form->field($team, 'topic')->textInput(['maxlength' => true]) ?>
          <?= $form->field($team, 'intro')->textarea(['rows' => 3]) ?>
          <?= $form->field($team, 'status')->dropDownList(Team::getStatusList()) ?>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-11">
              <?= Html::submitButton('保存团队信息', ['class' => 'btn btn-primary']) ?>
            </div>
          </div>
        <?php ActiveForm::end(); ?>
      <?php else: ?>
        <p class="text-muted">以下为当前单一团队信息（只读）。如需调整请联系 root。</p>
        <dl class="dl-horizontal">
          <dt>团队名称</dt><dd><?= Html::encode($team->name) ?></dd>
          <dt>主题</dt><dd><?= Html::encode($team->topic) ?></dd>
          <dt>简介</dt><dd><?= nl2br(Html::encode($team->intro)) ?></dd>
          <dt>状态</dt><dd><?= Team::getStatusList()[$team->status] ?? $team->status ?></dd>
        </dl>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-user"></span>
      团队成员
      <?php if (!$isRoot): ?><span class="label label-default ml10">只读</span><?php endif; ?>
    </div>
    <div class="panel-body">
      <p>
        <?php if ($isRoot): ?>
          <?= Html::a('新增成员', ['team-member/create'], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
          <span class="text-muted">仅 root 可新增/编辑成员，当前为只读模式。</span>
        <?php endif; ?>
      </p>

      <?php
        $userList = ArrayHelper::map(
          User::find()->orderBy(['id' => SORT_DESC])->all(),
          'id',
          'username'
        );
      ?>

      <?= GridView::widget([
          'dataProvider' => $memberDataProvider,
          'filterModel' => $memberSearchModel,
          'tableOptions' => ['class' => 'table table-striped table-condensed'],
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [
                  'attribute' => 'user_id',
                  'value' => fn($m) => $m->user ? $m->user->username : '',
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
                  'controller' => 'team-member',
                  'template' => $isRoot ? '{view} {update} {delete}' : '{view}',
              ],
          ],
      ]); ?>
    </div>
  </div>
</div>
