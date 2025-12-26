<?php

/**
 * 苏奕扬 2311330
 * 前台人物详情视图 - 深色主题优化版
 */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \common\models\WarPerson $model */
$this->title = $model->name . ' - 抗战人物志';

// 注册深色主题 CSS
$this->registerCss("
    /* === 主题色变量 === */
    :root {
        --gold-primary: #C9A227;
        --gold-light: #D4AF37;
        --gold-muted: #A88B2A;
        --red-primary: #8B1A1A;
        --red-hover: #A52A2A;
        --text-light: #F5E6C8;
        --text-dark: #1A1A1A;
        --card-bg: rgba(30, 25, 20, 0.9);
        --card-border: rgba(201, 162, 39, 0.2);
    }

    /* 页面头部 */
    .page-header {
        margin: 20px 0 30px;
        border-bottom: none;
        text-align: center;
    }
    .page-title {
        color: var(--gold-primary) !important;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }
    .page-subtitle {
        color: var(--text-dark) !important;
        font-size: 18px;
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted)) !important;
        display: inline-block;
        padding: 5px 20px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* 通用卡片样式 */
    .content-card {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        padding: 25px;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .section-title {
        color: var(--gold-primary) !important;
        font-size: 20px;
        font-weight: bold;
        border-left: 5px solid var(--gold-primary) !important;
        padding-left: 15px;
        margin-bottom: 20px;
        margin-top: 0;
    }

    /* 人物信息区 */
    .person-image-box {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        margin-bottom: 20px;
        border: 3px solid var(--gold-muted) !important;
    }
    .person-image-box img {
        width: 100%;
        height: auto;
        display: block;
    }
    .person-intro-text {
        font-size: 16px;
        line-height: 1.8;
        color: var(--text-light) !important;
        text-indent: 2em;
        opacity: 0.9;
    }
    .person-intro-text p {
        color: var(--text-light) !important;
    }

    /* 简介文字 */
    .content-card p {
        color: var(--text-light) !important;
        opacity: 0.85;
    }
    .content-card .text-muted {
        color: rgba(245, 230, 200, 0.6) !important;
    }

    /* 事件列表 */
    .event-list {
        list-style: none;
        padding: 0;
    }
    .event-item {
        position: relative;
        padding: 15px 15px 15px 30px;
        border-bottom: 1px dashed var(--card-border);
        transition: all 0.3s;
    }
    .event-item:last-child {
        border-bottom: none;
    }
    .event-item:before {
        content: '';
        position: absolute;
        left: 5px;
        top: 22px;
        width: 10px;
        height: 10px;
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(201, 162, 39, 0.4);
    }
    .event-item:hover {
        background-color: rgba(201, 162, 39, 0.08);
        border-radius: 8px;
    }
    .event-date {
        font-weight: bold;
        color: var(--gold-primary) !important;
        margin-right: 10px;
    }
    .event-title {
        color: var(--text-light) !important;
    }
    .event-item .text-danger {
        color: var(--gold-muted) !important;
    }
    .event-item .text-danger:hover {
        color: var(--gold-light) !important;
    }

    /* 相关文章列表 */
    .list-group {
        margin-bottom: 0;
    }
    .list-group-item {
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid var(--card-border) !important;
        color: var(--text-light) !important;
        padding: 12px 20px;
        transition: all 0.2s ease;
    }
    .list-group-item:last-child {
        border-bottom: none !important;
    }
    .list-group-item:hover {
        background: rgba(201, 162, 39, 0.1) !important;
        color: var(--gold-light) !important;
        padding-left: 25px;
    }
    .list-group-item .glyphicon {
        color: var(--gold-muted);
    }
    .list-group-item .glyphicon-new-window {
        color: rgba(245, 230, 200, 0.3) !important;
    }

    /* 留言区 */
    .comment-item {
        border-bottom: 1px solid var(--card-border) !important;
        padding: 15px 0;
    }
    .comment-item:last-child {
        border-bottom: none !important;
    }
    .comment-header {
        margin-bottom: 8px;
    }
    .comment-user {
        font-weight: bold;
        color: var(--gold-primary) !important;
    }
    .comment-time {
        color: rgba(245, 230, 200, 0.5) !important;
        font-size: 12px;
        float: right;
    }
    .comment-content {
        color: var(--text-light) !important;
        line-height: 1.6;
        opacity: 0.9;
    }

    /* 提示框 */
    .alert-warning {
        background: rgba(201, 162, 39, 0.15) !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
    }

    /* 留言表单 */
    .comment-form {
        background: rgba(20, 15, 10, 0.6) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
    }
    .comment-form h4 {
        color: var(--gold-primary) !important;
    }
    .comment-form .form-control {
        background: rgba(30, 25, 20, 0.8) !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
        border-radius: 8px;
    }
    .comment-form .form-control::placeholder {
        color: rgba(245, 230, 200, 0.4);
    }
    .comment-form .form-control:focus {
        border-color: var(--gold-muted) !important;
        box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.1) !important;
    }
    .comment-form .btn-danger {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted)) !important;
        border: none !important;
        color: var(--text-dark) !important;
        font-weight: 600;
    }
    .comment-form .btn-danger:hover {
        background: linear-gradient(135deg, var(--gold-light), var(--gold-primary)) !important;
    }

    /* 滚动条 */
    .comments-list::-webkit-scrollbar {
        width: 6px;
    }
    .comments-list::-webkit-scrollbar-track {
        background: rgba(201, 162, 39, 0.05);
    }
    .comments-list::-webkit-scrollbar-thumb {
        background: rgba(201, 162, 39, 0.25);
        border-radius: 3px;
    }
    .comments-list::-webkit-scrollbar-thumb:hover {
        background: rgba(201, 162, 39, 0.4);
    }
");
?>

<div class="container person-view">
    
    <!-- 头部 -->
    <div class="page-header">
        <h1 class="page-title"><?= Html::encode($model->name) ?></h1>
        <div class="page-subtitle"><?= Html::encode($model->role_type) ?></div>
    </div>

    <div class="row">
        <!-- 左侧：图片与简介 -->
        <div class="col-md-4">
            <div class="content-card">
                <?php if ($model->coverImage): ?>
                    <div class="person-image-box">
                        <?= Html::img($model->coverImage->path, ['alt' => $model->name]) ?>
                    </div>
                <?php else: ?>
                    <div class="person-image-box" style="background:#f8f9fa; height:300px; display:flex; align-items:center; justify-content:center; color:#ccc;">
                        <i class="glyphicon glyphicon-user" style="font-size: 80px;"></i>
                    </div>
                <?php endif; ?>
                
                <h4 class="section-title" style="font-size: 18px; margin-top: 20px;">人物简介</h4>
                <?php if ($model->intro): ?>
                    <p style="color: #666; line-height: 1.6;"><?= Html::encode($model->intro) ?></p>
                <?php else: ?>
                    <p class="text-muted">暂无简介</p>
                <?php endif; ?>
            </div>

            <!-- 相关文章 -->
            <div class="content-card" style="padding: 0; overflow: hidden;">
                <div style="padding: 15px 20px; border-bottom: 1px solid #eee; background-color: #fff;">
                    <h3 class="section-title" style="margin: 0; font-size: 18px; border-left: 4px solid #a94442; padding-left: 10px;">
                        相关文章
                    </h3>
                </div>
                <div class="list-group" style="margin-bottom: 0;">
                    <?php if (empty($articles)): ?>
                        <div class="list-group-item text-muted" style="border: none; padding: 20px; text-align: center;">暂无相关文献</div>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <a href="<?= \yii\helpers\Url::to($article->path) ?>" target="_blank" class="list-group-item" style="border: none; border-bottom: 1px solid #f0f0f0; color: #555; padding: 12px 20px;">
                                <i class="glyphicon glyphicon-book" style="color: #a94442; margin-right: 5px;"></i> 
                                <?= Html::encode($article->title) ?>
                                <i class="glyphicon glyphicon-new-window pull-right" style="color: #ccc; font-size: 12px; margin-top: 2px;"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 右侧：生平与事件 -->
        <div class="col-md-8">
            <!-- 生平 -->
            <?php if ($model->biography): ?>
                <div class="content-card">
                    <h3 class="section-title">生平事迹</h3>
                    <div class="person-intro-text">
                        <?php 
                        // 按换行符分割，并过滤空行，为每段添加缩进
                        $paragraphs = array_filter(explode("\n", str_replace(["\r\n", "\r"], "\n", $model->biography)));
                        foreach ($paragraphs as $p): 
                            $p = trim($p);
                            if (empty($p)) continue;
                        ?>
                            <p><?= Html::encode($p) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 相关事件 -->
            <div class="content-card">
                <h3 class="section-title">相关历史事件</h3>
                <?php if (empty($model->events)): ?>
                    <p class="text-muted" style="padding-left: 15px;">暂未关联事件</p>
                <?php else: ?>
                    <ul class="event-list">
                        <?php foreach ($model->events as $event): ?>
                            <li class="event-item">
                                <span class="event-date"><?= Html::encode($event->event_date ?: '日期待定') ?></span>
                                <span class="event-title"><?= Html::encode($event->title) ?></span>
                                <?= Html::a('<i class="glyphicon glyphicon-share-alt"></i>', ['timeline/view', 'id' => $event->id], [
                                    'title' => '查看事件详情',
                                    'class' => 'pull-right text-danger',
                                    'style' => 'opacity: 0.6;'
                                ]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>



            <!-- 留言区 -->
            <div class="content-card" id="comments">
                <h3 class="section-title">
                    留言互动 
                    <small class="pull-right" style="font-size: 14px; font-weight: normal; color: #999; margin-top: 5px;">
                        共 <?= count($comments) ?> 条留言
                    </small>
                </h3>
                
                <!-- 留言列表 -->
                <div class="comments-list" style="margin-bottom: 30px; max-height: 500px; overflow-y: auto;">
                    <?php if (empty($comments)): ?>
                        <div class="alert alert-warning" style="background: #fff9f9; color: #a94442; border-color: #ebccd1;">
                            暂无留言，快来抢沙发吧！
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-user">
                                        <i class="glyphicon glyphicon-user"></i> <?= Html::encode($comment->nickname) ?>
                                    </span>
                                    <span class="comment-time">
                                        <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                                    </span>
                                </div>
                                <div class="comment-content">
                                    <?= nl2br(Html::encode($comment->content)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 留言表单 -->
                <div class="comment-form" style="background: #f8f9fa; padding: 20px; border-radius: 6px;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; color: #333; font-weight: bold;">发表留言</h4>
                    <?php $form = \yii\widgets\ActiveForm::begin(); ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($newMessage, 'nickname')->textInput([
                                'maxlength' => true, 
                                'placeholder' => '您的昵称',
                                'class' => 'form-control',
                                'style' => 'border-radius: 4px;'
                            ])->label(false) ?>
                        </div>
                    </div>
                    
                    <?= $form->field($newMessage, 'content')->textarea([
                        'rows' => 4, 
                        'placeholder' => '写下您的感言...',
                        'class' => 'form-control',
                        'style' => 'border-radius: 4px; resize: vertical;'
                    ])->label(false) ?>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <?= Html::submitButton('<i class="glyphicon glyphicon-send"></i> 提交留言', [
                            'class' => 'btn btn-danger',
                            'style' => 'padding: 8px 25px; border-radius: 20px;'
                        ]) ?>
                    </div>
                    
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
