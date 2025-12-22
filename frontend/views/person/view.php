<?php

/**
 * Ding 2310724
 * 前台人物详情视图
 */

use yii\helpers\Html;

/** @var \common\models\WarPerson $model */
$this->title = $model->name;

// 注册红色风格 CSS
$this->registerCss("
    .person-view h1, .person-view h3, .person-view h4 {
        color: #a94442; /* 深红色 */
        border-bottom: 1px solid #ebccd1;
        padding-bottom: 10px;
    }
    .person-view .list-group-item {
        border-color: #ebccd1;
        color: #a94442;
        background-color: #f2dede;
    }
    .person-view .list-group-item strong {
        color: #843534;
    }
");
?>

<div class="person-view">
    <h1><?= Html::encode($model->name) ?> <small style="color: #a94442;"><?= Html::encode($model->role_type) ?></small></h1>
    <?php if ($model->intro): ?>
        <p class="lead" style="color: #a94442;"><?= Html::encode($model->intro) ?></p>
    <?php endif; ?>

    <?php if ($model->biography): ?>
        <div class="panel panel-danger">
            <div class="panel-heading">生平</div>
            <div class="panel-body">
                <?= nl2br(Html::encode($model->biography)) ?>
            </div>
        </div>
    <?php endif; ?>

    <h3>相关事件</h3>
    <?php if (empty($model->events)): ?>
        <p class="text-muted">暂未关联事件</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($model->events as $event): ?>
                <li class="list-group-item">
                    <strong><?= Html::encode($event->event_date ?: '日期待定') ?></strong>
                    <?= Html::encode($event->title) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr style="border-top: 1px solid #ebccd1;">
    
    <div class="comments-section">
        <h3>留言互动</h3>
        
        <!-- 留言列表 -->
        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p class="text-muted">暂无留言，快来抢沙发吧！</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <strong><?= Html::encode($comment->nickname) ?></strong>
                            <span class="pull-right">
                                <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                            </span>
                        </div>
                        <div class="panel-body">
                            <?= nl2br(Html::encode($comment->content)) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 留言表单 -->
        <div class="comment-form">
            <h4>发表留言</h4>
            <?php $form = \yii\widgets\ActiveForm::begin(); ?>
            
            <?= $form->field($newMessage, 'nickname')->textInput(['maxlength' => true, 'placeholder' => '请输入您的昵称']) ?>
            
            <?= $form->field($newMessage, 'content')->textarea(['rows' => 4, 'placeholder' => '请输入留言内容']) ?>
            
            <div class="form-group">
                <?= Html::submitButton('提交留言', ['class' => 'btn btn-danger']) ?>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>
