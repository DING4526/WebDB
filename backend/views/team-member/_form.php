<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Team;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */
/* @var $form yii\widgets\ActiveForm */

// 只列出正常的团队（建议）
$teamList = ArrayHelper::map(
    Team::find()
        ->where(['status' => Team::STATUS_ACTIVE])
        ->orderBy(['id' => SORT_DESC])
        ->all(),
    'id',
    'name'
);

$userList = ArrayHelper::map(
    User::find()->orderBy(['id' => SORT_DESC])->all(),
    'id',
    'username'
);

?>

<div class="team-member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'team_id')->dropDownList(
        $teamList,
        ['prompt' => '请选择团队']
    ) ?>

    <?= $form->field($model, 'user_id')->dropDownList(
        $userList, 
        ['prompt' => '（可空）']
    ); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'student_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_scope')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusList()) ?>

    <?php // created_at / updated_at 不要手填，TimestampBehavior 会自动写入 ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
