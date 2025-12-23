<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Team;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */
/* @var $form yii\widgets\ActiveForm */

// 获取当前团队（如果是新建，从teamProvider获取）
$teamId = $model->team_id ?: (Yii::$app->teamProvider ? Yii::$app->teamProvider->getId() : null);
$teamName = '未指定';
if ($teamId) {
    $team = Team::findOne($teamId);
    if ($team) {
        $teamName = $team->name;
    }
}

?>

<div class="team-member-form team-ws-form-wrap">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'team-ws-form']]); ?>

    <div class="team-ws-form-section">
      <div class="team-ws-form-section-title">基础信息</div>

      <!-- 团队（只读显示） -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">所属团队</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'team_id')->hiddenInput()->label(false) ?>
          <div class="team-ws-readonly-value">
            <?= Html::encode($teamName) ?>
          </div>
        </div>
      </div>

      <!-- 姓名 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">姓名</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'name', ['options' => ['class' => 'team-ws-field']])
            ->textInput(['maxlength' => true, 'placeholder' => '输入成员姓名'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 学号 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">学号</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'student_no', ['options' => ['class' => 'team-ws-field']])
            ->textInput(['maxlength' => true, 'placeholder' => '输入学号'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 角色 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">角色</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'role', ['options' => ['class' => 'team-ws-field']])
            ->textInput(['maxlength' => true, 'placeholder' => '如：组长、开发、测试等'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 工作范围 -->
      <div class="team-ws-fieldline team-ws-fieldline-full">
        <div class="team-ws-label">工作范围</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'work_scope', ['options' => ['class' => 'team-ws-field']])
            ->textarea(['rows' => 4, 'placeholder' => '描述成员负责的工作内容'])
            ->label(false) ?>
        </div>
      </div>

      <!-- 状态 -->
      <div class="team-ws-fieldline">
        <div class="team-ws-label">状态</div>
        <div class="team-ws-control">
          <?= $form->field($model, 'status', ['options' => ['class' => 'team-ws-field']])
            ->dropDownList($model::getStatusList())
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
        <?= Html::submitButton('保存', ['class' => 'btn btn-success team-ws-btn-save']) ?>
        <?= Html::a('取消', ['/team/index'], ['class' => 'btn btn-default']) ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
/* Reuse team-ws form styles */
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

.team-ws-readonly-value {
  font-weight: 700;
  font-size: 15px;
  color: #0f172a;
  padding: 10px 14px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,0.06);
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

.team-ws-icon {
  font-size: 14px;
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

@media (max-width: 640px) {
  .team-ws-fieldline {
    grid-template-columns: 1fr;
    gap: 8px;
  }
  
  .team-ws-label {
    padding-top: 0;
  }
}
</style>
