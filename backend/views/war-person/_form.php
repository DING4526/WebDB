<?php

/**
 * Ding 2310724
 * 抗战人物表单
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WarPerson */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="war-person-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'role_type')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'birth_year')->textInput() ?>
    <?= $form->field($model, 'death_year')->textInput() ?>
    <?= $form->field($model, 'intro')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'biography')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'status')->dropDownList([1 => '展示', 0 => '隐藏'], ['prompt' => '请选择']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
