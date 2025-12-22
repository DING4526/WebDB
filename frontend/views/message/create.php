<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*
    *liyu 2311591
    *对事件发表感言
*/

$this->title = '发表缅怀感言';
?>
<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-danger" style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div class="panel-heading" style="background-color: #8b0000; color: #fff; padding: 20px;">
                    <h3 class="panel-title" style="font-weight: bold;">
                        <i class="glyphicon glyphicon-pencil"></i> <?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="panel-body" style="padding: 30px;">
                    <?php $form = ActiveForm::begin(); ?>

                    <!-- 隐藏字段，自动保存关联 ID -->
                    <?= $form->field($model, 'target_type')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'target_id')->hiddenInput()->label(false) ?>

                    <?= $form->field($model, 'nickname')->textInput(['placeholder' => '请输入您的称呼'])->label('昵称') ?>
                    <?= $form->field($model, 'content')->textarea(['rows' => 6, 'placeholder' => '请写下您的缅怀感言...'])->label('感言内容') ?>

                    <div class="form-group" style="margin-top: 20px;">
                        <?= Html::submitButton('确认提交', ['class' => 'btn btn-danger btn-block btn-lg', 'style' => 'border-radius: 20px;']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>