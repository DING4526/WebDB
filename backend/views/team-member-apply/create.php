<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMemberApply */

$this->title = '申请成为成员';
$this->params['breadcrumbs'][] = ['label' => '成员申请', 'url' => ['create']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-common.css');
?>

<div class="team-member-apply-create">

  <div class="adm-hero">
    <div class="adm-hero-inner">
      <div>
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="desc">填写申请信息，提交后等待管理员审批</div>
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h3 class="adm-card-title">申请表单</h3>
      <span class="adm-pill">
        <span class="adm-dot"></span> 填写完整信息
      </span>
    </div>
    <div class="adm-card-body adm-form">
      <?php $form = ActiveForm::begin(); ?>

      <?php if (!empty($team)): ?>
        <?= $form->field($model, 'team_id')->hiddenInput()->label(false) ?>
        <div class="form-group">
          <label class="control-label">目标团队</label>
          <div class="adm-section" style="margin-bottom:0;">
            <div style="font-weight:900;font-size:16px;">
              <?= Html::encode($team->name) ?>
            </div>
            <?php if (!empty($team->topic)): ?>
              <div class="adm-muted" style="margin-top:4px;">
                主题：<?= Html::encode($team->topic) ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
      
      <div class="adm-grid-2col">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'readonly' => true]) ?>
      </div>

      <?= $form->field($model, 'student_no')->textInput([
          'maxlength' => true, 
          'placeholder' => '请输入本人学号'
      ]) ?>

      <?= $form->field($model, 'reason')->textarea([
          'rows' => 4,
          'placeholder' => '请简要说明申请理由（选填）'
      ]) ?>

      <div class="adm-section" style="background:rgba(59,130,246,0.04);border-color:rgba(59,130,246,0.2);">
        <div class="adm-section-title" style="color:#2563eb;">
          <span class="glyphicon glyphicon-info-sign"></span> 温馨提示
        </div>
        <ul class="adm-hint" style="margin:8px 0 0;padding-left:20px;">
          <li>提交后您的申请将进入待审核状态</li>
          <li>管理员审核通过后，您将获得成员权限</li>
          <li>请确保学号信息准确无误</li>
        </ul>
      </div>

      <div class="form-group" style="margin-top:20px;">
        <?= Html::submitButton('提交申请', ['class' => 'btn btn-soft-danger']) ?>
        <?= Html::a('返回首页', ['site/index'], ['class' => 'btn btn-soft-ghost']) ?>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>

</div>
