<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

$this->title = '我的学号';
$this->params['breadcrumbs'][] = ['label' => '团队成员', 'url' => ['team/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="team-member-my">
  <div class="panel panel-info">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-tag"></span> 更新学号
    </div>
    <div class="panel-body">
      <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'name')->textInput(['readonly' => true]) ?>
      <?= $form->field($model, 'student_no')->textInput(['maxlength' => true]) ?>

      <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('返回首页', ['site/index'], ['class' => 'btn btn-default']) ?>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
