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
$this->registerCssFile('@web/css/admin-common.css');

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$memberCount = (int)$memberDataProvider->getTotalCount();
?>
<div class="team-index">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">管理团队信息与成员列表</div>
      </div>
      <div class="adm-actions">
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">团队信息（单团队模式）</h3>
      <?php if (!$isRoot): ?>
        <span class="adm-badge adm-badge-info">只读</span>
      <?php endif; ?>
    </div>
    <div class="adm-card-body adm-form">
      <?php if ($isRoot): ?>
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal']]); ?>
          <?= $form->field($team, 'name')->textInput(['maxlength' => true]) ?>
          <?= $form->field($team, 'topic')->textInput(['maxlength' => true]) ?>
          <?= $form->field($team, 'intro')->textarea(['rows' => 3]) ?>
          <?= $form->field($team, 'status')->dropDownList(Team::getStatusList()) ?>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-11">
              <?= Html::submitButton('保存团队信息', ['class' => 'btn btn-success']) ?>
            </div>
          </div>
        <?php ActiveForm::end(); ?>
      <?php else: ?>
        <p class="adm-hint">以下为当前单一团队信息（只读）。如需调整请联系 root。</p>
        <dl class="dl-horizontal">
          <dt>团队名称</dt><dd><?= Html::encode($team->name) ?></dd>
          <dt>主题</dt><dd><?= Html::encode($team->topic) ?></dd>
          <dt>简介</dt><dd><?= nl2br(Html::encode($team->intro)) ?></dd>
          <dt>状态</dt><dd><?= Team::getStatusList()[$team->status] ?? $team->status ?></dd>
        </dl>
      <?php endif; ?>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">团队成员</h3>
      <span class="adm-pill"><span class="adm-dot"></span> 当前成员总数：<?= $memberCount ?></span>
    </div>
    <div class="adm-grid">
      <div style="margin-bottom: 12px;">
        <?php if ($isRoot): ?>
          <?= Html::a('新增成员', ['team-member/create'], ['class' => 'btn btn-soft-success']) ?>
        <?php else: ?>
          <span class="adm-hint">仅 root 可新增/编辑成员，当前为只读模式。</span>
        <?php endif; ?>
      </div>

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
          'summary' => false,
          'tableOptions' => ['class' => 'table table-hover'],
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
              [
                  'attribute' => 'user_id',
                  'value' => fn($m) => $m->user ? $m->user->username : '',
                  'filter' => $userList,
              ],
              [
                  'attribute' => 'name',
                  'contentOptions' => ['style' => 'font-weight:900;'],
              ],
              'student_no',
              [
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($m) {
                      $isActive = (bool)$m->status;
                      return $isActive
                          ? '<span class="adm-badge adm-badge-active">正常</span>'
                          : '<span class="adm-badge adm-badge-inactive">禁用</span>';
                  },
                  'filter' => TeamMember::getStatusList(),
                  'contentOptions' => ['style' => 'width:90px;'],
              ],
              [
                  'class' => 'yii\grid\ActionColumn',
                  'controller' => 'team-member',
                  'header' => '操作',
                  'template' => $isRoot ? '{view} {update} {delete}' : '{view}',
                  'buttons' => [
                      'view' => function ($url, $model) {
                          return Html::a('查看', ['team-member/view', 'id' => $model->id], ['class' => 'btn btn-xs btn-ghost']);
                      },
                      'update' => function ($url, $model) {
                          return Html::a('编辑', ['team-member/update', 'id' => $model->id], ['class' => 'btn btn-xs btn-soft-primary']);
                      },
                      'delete' => function ($url, $model) {
                          return Html::a('删除', ['team-member/delete', 'id' => $model->id], [
                              'class' => 'btn btn-xs btn-soft-danger',
                              'data' => [
                                  'confirm' => '确认删除该成员？',
                                  'method' => 'post',
                              ],
                          ]);
                      },
                  ],
                  'contentOptions' => ['class' => 'adm-actions-col', 'style' => 'min-width:200px;'],
              ],
          ],
      ]); ?>
    </div>
  </div>
</div>
