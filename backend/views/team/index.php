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
$this->registerCssFile('@web/css/team-workspace.css');

$currentUser = Yii::$app->user->getUser();
$isRoot = $currentUser && $currentUser->isRoot();
$memberCount = (int)$memberDataProvider->getTotalCount();

$userList = ArrayHelper::map(
  User::find()->orderBy(['id' => SORT_DESC])->all(),
  'id',
  'username'
);
?>

<div class="team-index team-workspace">

  <!-- Hero Header -->
  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">左侧展示成员信息，右侧编辑团队基础信息</div>
      </div>
      <div class="adm-actions">
        <?php if ($isRoot): ?>
          <?= Html::a('新增成员', ['team-member/create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?= Html::a('刷新', ['index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>
  </div>

  <!-- Workspace Layout: Left + Right -->
  <div class="team-ws-row">
    
    <!-- Left: Member List -->
    <div class="team-ws-left">
      <div class="adm-card team-ws-card">
        <div class="adm-card-head">
          <h3 class="adm-card-title">团队成员</h3>
          <span class="adm-pill">
            <span class="adm-dot"></span> 成员总数：<?= $memberCount ?>
          </span>
        </div>
        
        <div class="team-ws-members">
          <?php if (!$isRoot): ?>
            <div class="adm-hint" style="padding:12px 16px;">
              仅 root 可新增/编辑成员，当前为只读模式。
            </div>
          <?php endif; ?>

          <?php
          $members = $memberDataProvider->getModels();
          if (empty($members)): ?>
            <div class="team-ws-empty">暂无成员</div>
          <?php else: ?>
            <div class="team-ws-member-list">
              <?php foreach ($members as $m): ?>
                <?php
                  $initial = mb_substr(trim($m->name), 0, 1, 'UTF-8');
                  $isActive = (bool)$m->status;
                ?>
                <div class="team-ws-member-card">
                  <div class="team-ws-member-avatar">
                    <?= Html::encode($initial) ?>
                  </div>
                  <div class="team-ws-member-main">
                    <div class="team-ws-member-name">
                      <?= Html::encode($m->name) ?>
                    </div>
                    <div class="team-ws-member-meta">
                      <?php if ($m->student_no): ?>
                        <span class="team-ws-meta-item">
                          <span class="team-ws-icon">👤</span>
                          <?= Html::encode($m->student_no) ?>
                        </span>
                      <?php endif; ?>
                      <?php if ($m->role): ?>
                        <span class="team-ws-meta-item">
                          <span class="team-ws-icon">💼</span>
                          <?= Html::encode($m->role) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="team-ws-member-actions">
                    <div class="adm-actions-col">
                      <?= Html::a('查看', ['team-member/view', 'id' => $m->id], [
                        'class' => 'btn btn-xs btn-soft-primary',
                        'style' => 'margin-left:6px; border-radius:8px;'
                      ]) ?>
                    </div>
                    <?php if ($isRoot): ?>
                      <div class="adm-actions-col">
                        <?= Html::a('编辑', ['team-member/update', 'id' => $m->id], [
                          'class' => 'btn btn-xs btn-soft-success',
                          'style' => 'margin-left:4px; border-radius:8px;'
                        ]) ?>
                      </div>
                      <div class="adm-actions-col">
                        <?= Html::a('删除', ['team-member/delete', 'id' => $m->id], [
                          'class' => 'btn btn-xs btn-soft-danger',
                          'style' => 'margin-left:4px; border-radius:8px;',
                          'data' => [
                            'confirm' => '确认删除该成员？',
                            'method' => 'post',
                          ],
                        ]) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right: Team Info Form -->
    <div class="team-ws-right">
      <div class="adm-card team-ws-card">
        <div class="adm-card-head">
          <h3 class="adm-card-title">团队信息</h3>
          <?php if (!$isRoot): ?>
            <span class="adm-badge adm-badge-info">只读</span>
          <?php endif; ?>
        </div>
        
        <div class="team-ws-form-wrap">
          <?php if ($isRoot): ?>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'team-ws-form']]); ?>
            
            <div class="team-ws-form-section">
              <div class="team-ws-form-section-title">基础信息</div>
              
              <div class="team-ws-fieldline">
                <div class="team-ws-label">团队名称</div>
                <div class="team-ws-control">
                  <?= $form->field($team, 'name', ['options' => ['class' => 'team-ws-field']])
                    ->textInput(['maxlength' => true, 'placeholder' => '输入团队名称'])
                    ->label(false) ?>
                </div>
              </div>

              <div class="team-ws-fieldline">
                <div class="team-ws-label">项目主题</div>
                <div class="team-ws-control">
                  <?= $form->field($team, 'topic', ['options' => ['class' => 'team-ws-field']])
                    ->textInput(['maxlength' => true, 'placeholder' => '输入项目主题'])
                    ->label(false) ?>
                </div>
              </div>

              <div class="team-ws-fieldline team-ws-fieldline-full">
                <div class="team-ws-label">团队简介</div>
                <div class="team-ws-control">
                  <?= $form->field($team, 'intro', ['options' => ['class' => 'team-ws-field']])
                    ->textarea(['rows' => 4, 'placeholder' => '简要介绍团队情况'])
                    ->label(false) ?>
                </div>
              </div>

              <div class="team-ws-fieldline">
                <div class="team-ws-label">状态</div>
                <div class="team-ws-control">
                  <?= $form->field($team, 'status', ['options' => ['class' => 'team-ws-field']])
                    ->dropDownList(Team::getStatusList())
                    ->label(false) ?>
                </div>
              </div>
            </div>

            <div class="team-ws-form-footer">
              <div class="team-ws-footer-hint">
                <span class="team-ws-icon">💡</span>
                <span>保存后立即生效</span>
              </div>
              <div class="team-ws-footer-actions">
                <div class="adm-actions-col">
                  <?= Html::submitButton('保存团队信息', [
                    'class' => 'btn btn-soft-success team-ws-btn-save'
                  ]) ?>
                </div>
              </div>
            </div>

            <?php ActiveForm::end(); ?>

          <?php else: ?>
            <!-- Read-only view for non-root users -->
            <div class="team-ws-readonly">
              <div class="team-ws-readonly-item">
                <div class="team-ws-readonly-label">团队名称</div>
                <div class="team-ws-readonly-value"><?= Html::encode($team->name) ?></div>
              </div>
              <div class="team-ws-readonly-item">
                <div class="team-ws-readonly-label">项目主题</div>
                <div class="team-ws-readonly-value"><?= Html::encode($team->topic) ?></div>
              </div>
              <div class="team-ws-readonly-item">
                <div class="team-ws-readonly-label">团队简介</div>
                <div class="team-ws-readonly-value team-ws-readonly-pre"><?= nl2br(Html::encode($team->intro)) ?></div>
              </div>
              <div class="team-ws-readonly-item">
                <div class="team-ws-readonly-label">状态</div>
                <div class="team-ws-readonly-value">
                  <span class="adm-badge <?= $team->status ? 'adm-badge-active' : 'adm-badge-inactive' ?>">
                    <?= Team::getStatusList()[$team->status] ?? $team->status ?>
                  </span>
                </div>
              </div>
              <div class="adm-hint" style="margin-top:16px;">
                如需修改团队信息，请联系 root 管理员。
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

</div>
