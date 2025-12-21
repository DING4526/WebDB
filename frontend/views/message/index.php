<?php
/**
 * 留言板
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '纪念留言';
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?= Html::encode($this->title) ?></h2>

<p class="text-muted">
    欢迎留下对抗战先烈的纪念与敬意，留言将在审核后展示。
</p>

<hr>

<!-- 留言列表 -->
<?php if (empty($messages)): ?>
    <div class="alert alert-info">
        暂无留言，欢迎留下第一条纪念。
    </div>
<?php else: ?>
    <?php foreach ($messages as $msg): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <strong><?= Html::encode($msg->nickname) ?></strong>
                <div class="text-muted" style="font-size:12px;">
                    <?= date('Y-m-d H:i', $msg->created_at) ?>
                </div>
                <div style="margin-top:8px;">
                    <?= nl2br(Html::encode($msg->content)) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<hr>

<!-- 留言提交 -->
<h4>提交留言</h4>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'nickname')->textInput([
    'maxlength' => true,
    'placeholder' => '请输入您的昵称',
]) ?>

<?= $form->field($model, 'content')->textarea([
    'rows' => 4,
    'placeholder' => '请输入留言内容',
]) ?>

<div class="form-group">
    <?= Html::submitButton('提交留言', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
