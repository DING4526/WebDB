<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMemberApply */

$this->title = '申请成为成员';
$this->params['breadcrumbs'][] = ['label' => '成员申请', 'url' => ['create']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="team-member-apply-create">
    <div class="panel panel-info">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-send"></span> 成员申请
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>

            <?php if (!empty($team)): ?>
              <?= $form->field($model, 'team_id')->hiddenInput()->label(false) ?>
              <div class="form-group">
                <label class="control-label">目标团队</label>
                <div class="form-control" disabled><?= \yii\helpers\Html::encode($team->name) ?></div>
              </div>
            <?php endif; ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            <?= $form->field($model, 'student_no')->textInput(['maxlength' => true, 'placeholder' => '请输入本人学号']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            <?= $form->field($model, 'reason')->textarea(['rows' => 4]) ?>

            <div class="form-group">
                <?= Html::submitButton('提交申请', ['class' => 'btn btn-success']) ?>
                <?= Html::a('返回', ['site/index'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
