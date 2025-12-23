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
                      <?php if ($m->user): ?>
                        <span class="team-ws-meta-item">
                          <span class="team-ws-icon">👤</span>
                          <?= Html::encode($m->user->username) ?>
                        </span>
                      <?php endif; ?>
                      <?php if ($m->student_no): ?>
                        <span class="team-ws-meta-item">
                          <span class="team-ws-icon">🎓</span>
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
                    <span class="adm-badge <?= $isActive ? 'adm-badge-active' : 'adm-badge-inactive' ?>">
                      <?= $isActive ? '正常' : '禁用' ?>
                    </span>
                    <?php if ($isRoot): ?>
                      <?= Html::a('编辑', ['team-member/update', 'id' => $m->id], [
                        'class' => 'btn btn-xs btn-soft-primary',
                        'style' => 'margin-left:6px;'
                      ]) ?>
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
                <?= Html::submitButton('保存团队信息', [
                  'class' => 'btn btn-success team-ws-btn-save'
                ]) ?>
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

<style>
/* ===== Team Workspace Layout ===== */
.team-workspace {
  padding: 0;
}

.team-ws-row {
  display: flex;
  gap: 16px;
  margin-top: 14px;
  align-items: flex-start;
}

.team-ws-left {
  flex: 1 1 65%;
  min-width: 0;
}

.team-ws-right {
  flex: 0 0 420px;
  min-width: 320px;
}

.team-ws-card {
  height: auto;
}

/* ===== Member List Styling ===== */
.team-ws-members {
  padding: 0;
}

.team-ws-member-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 12px 16px;
}

.team-ws-member-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px;
  background: #f8fafc;
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 12px;
  transition: all 0.2s ease;
}

.team-ws-member-card:hover {
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transform: translateY(-1px);
}

.team-ws-member-avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 20px;
  flex-shrink: 0;
}

.team-ws-member-main {
  flex: 1;
  min-width: 0;
}

.team-ws-member-name {
  font-weight: 900;
  font-size: 15px;
  color: #0f172a;
  margin-bottom: 6px;
}

.team-ws-member-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
  color: #64748b;
}

.team-ws-meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 700;
}

.team-ws-icon {
  font-size: 14px;
}

.team-ws-member-actions {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}

.team-ws-empty {
  padding: 40px 20px;
  text-align: center;
  color: #94a3b8;
  font-weight: 700;
}

/* ===== Form Styling (Optimized) ===== */
.team-ws-form-wrap {
  padding: 0;
}

.team-ws-form {
  padding: 16px 20px 20px;
}

.team-ws-form-section {
  margin-bottom: 20px;
}

.team-ws-form-section-title {
  font-weight: 900;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid rgba(0,0,0,0.06);
}

.team-ws-fieldline {
  display: grid;
  grid-template-columns: 100px 1fr;
  gap: 12px;
  align-items: start;
  margin-bottom: 16px;
}

.team-ws-fieldline-full {
  grid-template-columns: 100px 1fr;
}

.team-ws-label {
  font-weight: 900;
  font-size: 14px;
  color: #334155;
  padding-top: 10px;
}

.team-ws-control {
  flex: 1;
}

.team-ws-field {
  margin: 0;
}

.team-ws-field .form-control {
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,0.12);
  padding: 10px 14px;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.team-ws-field .form-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
  outline: none;
}

.team-ws-field textarea.form-control {
  line-height: 1.6;
  resize: vertical;
}

.team-ws-field .help-block {
  margin-top: 6px;
  margin-bottom: 0;
  font-size: 12px;
  color: #ef4444;
  font-weight: 700;
}

.team-ws-form-footer {
  margin-top: 24px;
  padding-top: 20px;
  border-top: 2px solid rgba(0,0,0,0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.team-ws-footer-hint {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #64748b;
  font-size: 13px;
  font-weight: 700;
}

.team-ws-footer-actions {
  display: flex;
  gap: 8px;
}

.team-ws-btn-save {
  border-radius: 10px;
  padding: 10px 20px;
  font-weight: 900;
  font-size: 14px;
}

/* ===== Read-only View ===== */
.team-ws-readonly {
  padding: 16px 20px 20px;
}

.team-ws-readonly-item {
  margin-bottom: 20px;
}

.team-ws-readonly-item:last-child {
  margin-bottom: 0;
}

.team-ws-readonly-label {
  font-weight: 900;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 8px;
}

.team-ws-readonly-value {
  font-weight: 700;
  font-size: 15px;
  color: #0f172a;
  padding: 10px 14px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,0.06);
}

.team-ws-readonly-pre {
  white-space: pre-wrap;
  line-height: 1.6;
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
  .team-ws-row {
    flex-direction: column;
  }
  
  .team-ws-left,
  .team-ws-right {
    flex: 1 1 100%;
    width: 100%;
  }
  
  .team-ws-right {
    flex: 1 1 100%;
  }
}

@media (max-width: 640px) {
  .team-ws-fieldline {
    grid-template-columns: 1fr;
    gap: 8px;
  }
  
  .team-ws-label {
    padding-top: 0;
  }
  
  .team-ws-member-card {
    flex-wrap: wrap;
  }
  
  .team-ws-member-actions {
    width: 100%;
    justify-content: flex-start;
    margin-top: 8px;
  }
}
</style>
