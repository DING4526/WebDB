<?php

/**
 * 苏奕扬 2311330
 * 前台人物详情视图 - 深色主题优化版（统一风格）
 * 注意：主题色变量定义在 site.css 中
 */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \common\models\WarPerson $model */
$this->title = $model->name . ' - 抗战人物志';

// 注册深色主题 CSS（统一风格）
$this->registerCss("
    /* ===== 页面容器 ===== */
    .detail-page-container {
        position: relative;
        z-index: 2;
    }

    /* ===== 页面头部 ===== */
    .detail-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--card-border);
    }

    .detail-header .page-title {
        color: var(--gold-primary) !important;
        font-weight: bold;
        font-size: 28px;
        margin: 0 0 12px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .detail-header .page-subtitle {
        display: inline-block;
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
        color: var(--text-dark) !important;
        padding: 6px 24px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 15px;
    }

    /* ===== 通用卡片样式 ===== */
    .content-card {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        padding: 25px;
        margin-bottom: 25px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .section-title {
        color: var(--gold-primary) !important;
        font-size: 18px;
        font-weight: bold;
        border-left: 4px solid var(--gold-primary) !important;
        padding-left: 12px;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-title .title-text {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title .title-count {
        font-size: 13px;
        font-weight: normal;
        color: rgba(245, 230, 200, 0.6) !important;
    }

    /* ===== 人物图片 ===== */
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

    .person-image-placeholder {
        height: 280px;
        background: rgba(30, 25, 20, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 2px dashed var(--card-border);
        margin-bottom: 20px;
    }

    .person-image-placeholder .glyphicon {
        font-size: 60px;
        color: var(--gold-muted);
        opacity: 0.3;
    }

    /* ===== 简介文字 ===== */
    .intro-text {
        color: var(--text-light) !important;
        line-height: 1.8;
        opacity: 0.9;
    }

    .intro-text p {
        margin-bottom: 1em;
        text-indent: 2em;
    }

    /* ===== 侧边栏卡片 ===== */
    .sidebar-card {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .sidebar-card .card-header {
        background: rgba(201, 162, 39, 0.1) !important;
        padding: 15px 20px;
        border-bottom: 1px solid var(--card-border);
    }

    .sidebar-card .card-header h4 {
        margin: 0;
        color: var(--gold-primary) !important;
        font-size: 16px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-card .card-body {
        padding: 20px;
        color: var(--text-light);
    }

    .sidebar-card .card-body .text-muted {
        color: rgba(245, 230, 200, 0.6) !important;
    }

    /* ===== 文章列表 ===== */
    .article-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .article-list-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid var(--card-border);
        color: var(--text-light) !important;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .article-list-item:last-child {
        border-bottom: none;
    }

    .article-list-item:hover {
        background: rgba(201, 162, 39, 0.1);
        color: var(--gold-light) !important;
        padding-left: 20px;
    }

    .article-list-item .article-icon {
        color: var(--gold-muted);
        margin-right: 10px;
        font-size: 14px;
    }

    .article-list-item .article-title {
        flex: 1;
    }

    .article-list-item .article-arrow {
        color: rgba(245, 230, 200, 0.3);
        font-size: 12px;
    }

    /* ===== 事件列表 ===== */
    .event-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .event-item {
        position: relative;
        padding: 15px 15px 15px 30px;
        border-bottom: 1px solid var(--card-border);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .event-item:last-child {
        border-bottom: none;
    }

    .event-item:before {
        content: '';
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 10px;
        height: 10px;
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted));
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(201, 162, 39, 0.4);
    }

    .event-item:hover {
        background-color: rgba(201, 162, 39, 0.08);
    }

    .event-item .event-info {
        flex: 1;
    }

    .event-date {
        font-weight: bold;
        color: var(--gold-primary) !important;
        margin-right: 10px;
        font-size: 13px;
    }

    .event-title {
        color: var(--text-light) !important;
        font-size: 14px;
    }

    .event-item .event-link {
        color: var(--gold-muted) !important;
        opacity: 0.6;
        transition: all 0.2s ease;
    }

    .event-item:hover .event-link {
        opacity: 1;
        color: var(--gold-light) !important;
    }

    /* ===== 留言/感言区域 ===== */
    .comments-section {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .comments-header {
        background: rgba(201, 162, 39, 0.08);
        padding: 18px 25px;
        border-bottom: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .comments-header h3 {
        margin: 0;
        color: var(--gold-primary) !important;
        font-size: 18px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .comments-header .comment-count {
        font-size: 13px;
        font-weight: normal;
        color: rgba(245, 230, 200, 0.6);
    }

    .comments-body {
        padding: 20px 25px;
    }

    .comments-list {
        max-height: 450px;
        overflow-y: auto;
        margin-bottom: 25px;
        padding-right: 10px;
    }

    /* 滚动条美化 */
    .comments-list::-webkit-scrollbar {
        width: 6px;
    }
    .comments-list::-webkit-scrollbar-track {
        background: rgba(201, 162, 39, 0.05);
        border-radius: 3px;
    }
    .comments-list::-webkit-scrollbar-thumb {
        background: rgba(201, 162, 39, 0.25);
        border-radius: 3px;
    }
    .comments-list::-webkit-scrollbar-thumb:hover {
        background: rgba(201, 162, 39, 0.4);
    }

    .comment-item {
        padding: 15px 0;
        border-bottom: 1px solid var(--card-border);
    }

    .comment-item:last-child {
        border-bottom: none;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .comment-user {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: var(--gold-primary) !important;
        font-size: 14px;
    }

    .comment-time {
        font-size: 12px;
        color: rgba(245, 230, 200, 0.5) !important;
    }

    .comment-content {
        color: var(--text-light) !important;
        line-height: 1.7;
        font-size: 14px;
        opacity: 0.9;
    }

    .empty-comments {
        text-align: center;
        padding: 30px 20px;
        color: rgba(245, 230, 200, 0.6);
    }

    .empty-comments .glyphicon {
        font-size: 36px;
        color: var(--gold-muted);
        opacity: 0.3;
        display: block;
        margin-bottom: 15px;
    }

    /* ===== 留言表单 ===== */
    .comment-form {
        background: rgba(20, 15, 10, 0.5);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 20px;
    }

    .comment-form .form-title {
        color: var(--gold-primary) !important;
        font-size: 16px;
        font-weight: bold;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .comment-form .form-control {
        background: rgba(30, 25, 20, 0.8) !important;
        border: 1px solid var(--card-border) !important;
        color: var(--text-light) !important;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.2s ease;
    }

    .comment-form .form-control::placeholder {
        color: rgba(245, 230, 200, 0.4);
    }

    .comment-form .form-control:focus {
        border-color: var(--gold-muted) !important;
        box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.1) !important;
        outline: none;
    }

    .comment-form .btn-submit {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-muted)) !important;
        border: none !important;
        color: var(--text-dark) !important;
        font-weight: 600;
        padding: 10px 30px;
        border-radius: 25px;
        transition: all 0.2s ease;
    }

    .comment-form .btn-submit:hover {
        background: linear-gradient(135deg, var(--gold-light), var(--gold-primary)) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(201, 162, 39, 0.3);
    }
");
?>

<div class="detail-page-container">
    
    <!-- 页面头部 -->
    <div class="detail-header">
        <h1 class="page-title"><?= Html::encode($model->name) ?></h1>
        <div class="page-subtitle"><?= Html::encode($model->role_type) ?></div>
    </div>

    <div class="row">
        <!-- 左侧：图片与简介 -->
        <div class="col-md-4">
            <!-- 人物照片卡片 -->
            <div class="content-card">
                <?php if ($model->coverImage): ?>
                    <div class="person-image-box">
                        <?= Html::img($model->coverImage->path, ['alt' => $model->name]) ?>
                    </div>
                <?php else: ?>
                    <div class="person-image-placeholder">
                        <i class="glyphicon glyphicon-user"></i>
                    </div>
                <?php endif; ?>
                
                <h4 class="section-title" style="font-size: 16px;">
                    <span class="title-text"><i class="glyphicon glyphicon-info-sign"></i> 人物简介</span>
                </h4>
                <?php if ($model->intro): ?>
                    <p class="intro-text"><?= Html::encode($model->intro) ?></p>
                <?php else: ?>
                    <p style="color: rgba(245, 230, 200, 0.5); text-align: center;">暂无简介</p>
                <?php endif; ?>
            </div>

            <!-- 相关文章 -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h4><i class="glyphicon glyphicon-book"></i> 相关文献资料</h4>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($articles)): ?>
                        <div style="padding: 25px; text-align: center; color: rgba(245, 230, 200, 0.5);">
                            暂无相关文献
                        </div>
                    <?php else: ?>
                        <ul class="article-list">
                            <?php foreach ($articles as $article): ?>
                                <a href="<?= \yii\helpers\Url::to($article->path) ?>" target="_blank" class="article-list-item">
                                    <i class="glyphicon glyphicon-file article-icon"></i>
                                    <span class="article-title"><?= Html::encode($article->title) ?></span>
                                    <i class="glyphicon glyphicon-new-window article-arrow"></i>
                                </a>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 右侧：生平与事件 -->
        <div class="col-md-8">
            <!-- 生平 -->
            <?php if ($model->biography): ?>
                <div class="content-card">
                    <h3 class="section-title">
                        <span class="title-text"><i class="glyphicon glyphicon-book"></i> 生平事迹</span>
                    </h3>
                    <div class="intro-text">
                        <?php 
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
                <h3 class="section-title">
                    <span class="title-text"><i class="glyphicon glyphicon-time"></i> 相关历史事件</span>
                    <span class="title-count"><?= count($model->events) ?> 个事件</span>
                </h3>
                <?php if (empty($model->events)): ?>
                    <div style="text-align: center; padding: 20px; color: rgba(245, 230, 200, 0.5);">
                        <i class="glyphicon glyphicon-calendar" style="font-size: 30px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                        暂未关联事件
                    </div>
                <?php else: ?>
                    <ul class="event-list">
                        <?php foreach ($model->events as $event): ?>
                            <li class="event-item">
                                <div class="event-info">
                                    <span class="event-date"><?= Html::encode($event->event_date ?: '日期待定') ?></span>
                                    <span class="event-title"><?= Html::encode($event->title) ?></span>
                                </div>
                                <?= Html::a('<i class="glyphicon glyphicon-share-alt"></i>', ['timeline/view', 'id' => $event->id], [
                                    'title' => '查看事件详情',
                                    'class' => 'event-link'
                                ]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- 留言区 -->
            <div class="comments-section" id="comments">
                <div class="comments-header">
                    <h3>
                        <i class="glyphicon glyphicon-comment"></i> 留言互动
                    </h3>
                    <span class="comment-count">共 <?= count($comments) ?> 条留言</span>
                </div>
                
                <div class="comments-body">
                    <!-- 留言列表 -->
                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <div class="empty-comments">
                                <i class="glyphicon glyphicon-comment"></i>
                                <p>暂无留言，快来抢沙发吧！</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <span class="comment-user">
                                            <i class="glyphicon glyphicon-user"></i>
                                            <?= Html::encode($comment->nickname) ?>
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
                    <div class="comment-form">
                        <h4 class="form-title">
                            <i class="glyphicon glyphicon-edit"></i> 发表留言
                        </h4>
                        <?php $form = \yii\widgets\ActiveForm::begin(); ?>
                        
                        <div class="row">
                            <div class="col-md-5">
                                <?= $form->field($newMessage, 'nickname')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => '您的昵称',
                                    'class' => 'form-control'
                                ])->label(false) ?>
                            </div>
                        </div>
                        
                        <?= $form->field($newMessage, 'content')->textarea([
                            'rows' => 4, 
                            'placeholder' => '写下您的感言...',
                            'class' => 'form-control'
                        ])->label(false) ?>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-send"></i> 提交留言', [
                                'class' => 'btn btn-submit'
                            ]) ?>
                        </div>
                        
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
