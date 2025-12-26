<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * liyu 2311591
 * 详情页视图 - 深色主题优化版（统一风格）
 * 注意：主题色变量定义在 site.css 中
 */

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $messages common\models\WarMessage[] */
/* @var $images common\models\WarMedia[] */
/* @var $articles common\models\WarMedia[] */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '时间轴', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// 注册深色主题样式（统一风格）
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

    .detail-header .page-meta {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        color: var(--text-light);
        opacity: 0.8;
        font-size: 14px;
    }

    .detail-header .page-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-header .page-meta .glyphicon {
        color: var(--gold-muted);
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

    /* ===== 正文内容 ===== */
    .article-body {
        color: var(--text-light) !important;
        font-size: 16px;
        line-height: 2;
        text-align: justify;
        opacity: 0.9;
    }

    .article-body p {
        margin-bottom: 1.5em;
        text-indent: 2em;
    }

    /* ===== 轮播图 ===== */
    #event-carousel {
        border: 1px solid var(--card-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4) !important;
        margin-bottom: 25px;
    }

    .carousel-indicators li {
        border-color: var(--gold-primary) !important;
        background-color: rgba(255, 255, 255, 0.3);
    }

    .carousel-indicators .active {
        background-color: var(--gold-primary) !important;
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

    /* ===== 人物头像列表 ===== */
    .person-avatar-list {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .person-avatar-item {
        text-align: center;
        width: calc(50% - 8px);
    }

    .person-avatar-item .avatar-img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold-muted);
        transition: all 0.3s ease;
        margin: 0 auto;
        display: block;
    }

    .person-avatar-item:hover .avatar-img {
        border-color: var(--gold-light);
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(201, 162, 39, 0.3);
    }

    .person-avatar-item .avatar-placeholder {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(30, 25, 20, 0.8);
        border: 2px solid var(--gold-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .person-avatar-item .avatar-placeholder .glyphicon {
        font-size: 28px;
        color: var(--gold-muted);
        opacity: 0.5;
    }

    .person-avatar-item .avatar-name {
        margin-top: 8px;
        font-size: 14px;
        color: var(--text-light);
        transition: color 0.2s ease;
    }

    .person-avatar-item:hover .avatar-name {
        color: var(--gold-light);
    }

    .person-avatar-item .avatar-role {
        font-size: 12px;
        color: rgba(245, 230, 200, 0.5);
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

    /* ===== 提示卡片 ===== */
    .tip-card {
        background: rgba(201, 162, 39, 0.1) !important;
        border: 1px solid var(--card-border);
        border-radius: 8px;
        padding: 15px;
        color: var(--text-light);
        font-size: 13px;
        line-height: 1.6;
    }

    .tip-card .glyphicon {
        color: var(--gold-muted);
        margin-right: 5px;
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

    .comment-form .form-hint {
        color: rgba(245, 230, 200, 0.5);
        font-size: 12px;
        margin-left: 15px;
    }

    /* ===== 表单操作区域 ===== */
    .form-actions {
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }

    /* ===== 轮播控制按钮 ===== */
    .carousel-control {
        background-image: none !important;
        width: 8%;
    }

    .carousel-control .carousel-arrow {
        color: #fff;
        text-shadow: 0 0 8px rgba(0,0,0,0.6);
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 24px;
        transition: all 0.2s ease;
    }

    .carousel-control:hover .carousel-arrow {
        color: var(--gold-light);
        transform: translateY(-50%) scale(1.2);
    }

    /* ===== 图片占位 ===== */
    .img-thumbnail {
        background: var(--card-bg) !important;
        border-color: var(--gold-muted) !important;
    }
");
?>

<div class="detail-page-container">
    <!-- 页面头部 -->
    <div class="detail-header">
        <h1 class="page-title"><?= Html::encode($model->title) ?></h1>
        <div class="page-meta">
            <span class="meta-item">
                <i class="glyphicon glyphicon-calendar"></i>
                <?= Html::encode($model->event_date) ?>
            </span>
            <span class="meta-item">
                <i class="glyphicon glyphicon-map-marker"></i>
                <?= Html::encode($model->location ?: '地点不详') ?>
            </span>
            <span class="meta-item">
                <i class="glyphicon glyphicon-eye-open"></i>
                访问量：<?= number_format($visitCount) ?> 次
            </span>
        </div>
    </div>

    <div class="row">
        <!-- 左侧：主要内容区域 -->
        <div class="col-md-8">
            
            <!-- 1. 多图轮播 -->
            <?php if (!empty($images)): ?>
                <div id="event-carousel" class="carousel slide" data-ride="carousel">
                    <!-- 指示器 -->
                    <ol class="carousel-indicators">
                        <?php foreach ($images as $index => $img): ?>
                            <li data-target="#event-carousel" data-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></li>
                        <?php endforeach; ?>
                    </ol>

                    <!-- 轮播内容 -->
                    <div class="carousel-inner" role="listbox">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= Url::to('@web/' . $img->path) ?>" alt="<?= Html::encode($img->title) ?>" style="width:100%; height:400px; object-fit:cover;">
                                <?php if ($img->title): ?>
                                    <div class="carousel-caption" style="bottom: 35px; background: none; padding: 0;">
                                        <span style="display: inline-block; background: rgba(0,0,0,0.7); color: #fff; padding: 6px 16px; border-radius: 20px; font-size: 13px;">
                                            <?= Html::encode($img->title) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 左右控制按钮 -->
                    <a class="left carousel-control" href="#event-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left carousel-arrow"></span>
                    </a>
                    <a class="right carousel-control" href="#event-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right carousel-arrow"></span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- 正文 -->
            <div class="content-card">
                <h3 class="section-title">
                    <span class="title-text"><i class="glyphicon glyphicon-file"></i> 事件详情</span>
                </h3>
                <div class="article-body">
                    <?php 
                    $paragraphs = array_filter(explode("\n", str_replace(["\r\n", "\r"], "\n", $model->content)));
                    foreach ($paragraphs as $p): 
                        $p = trim($p);
                        if (empty($p)) continue;
                    ?>
                        <p><?= Html::encode($p) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 缅怀感言区 -->
            <div class="comments-section" id="comments">
                <div class="comments-header">
                    <h3>
                        <i class="glyphicon glyphicon-heart"></i> 缅怀与致敬
                    </h3>
                    <span class="comment-count">已有 <?= count($comments) ?> 位同胞发表感言</span>
                </div>
                
                <div class="comments-body">
                    <!-- 感言列表 -->
                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <div class="empty-comments">
                                <i class="glyphicon glyphicon-comment"></i>
                                <p>暂无致敬感言。铭记历史，从你我开始。</p>
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

                    <!-- 提交致敬表单 -->
                    <div class="comment-form">
                        <h4 class="form-title">
                            <i class="glyphicon glyphicon-edit"></i> 寄托哀思
                        </h4>
                        
                        <?php $form = \yii\widgets\ActiveForm::begin([
                            'action' => ['view', 'id' => $model->id, '#' => 'comments'],
                        ]); ?>
                        
                        <div class="row">
                            <div class="col-md-5">
                                <?= $form->field($newMessage, 'nickname')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => '您的署名',
                                    'class' => 'form-control'
                                ])->label(false) ?>
                            </div>
                        </div>
                        
                        <?= $form->field($newMessage, 'content')->textarea([
                            'rows' => 3, 
                            'placeholder' => '铭记这段历史，缅怀英勇先烈。请写下您的致敬感言...',
                            'class' => 'form-control'
                        ])->label(false) ?>
                        
                        <div class="form-group form-actions">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-send"></i> 提交感言', [
                                'class' => 'btn btn-submit'
                            ]) ?>
                            <span class="form-hint">* 您的感言将在审核后公开展示</span>
                        </div>
                        
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div> 

        <!-- 右侧：侧边栏区域 -->
        <div class="col-md-4">
            <!-- 1. 相关人物卡片 -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h4><i class="glyphicon glyphicon-user"></i> 相关历史人物</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($model->people)): ?>
                        <p class="text-muted" style="text-align: center; margin: 0;">暂无关联人物数据</p>
                    <?php else: ?>
                        <div class="person-avatar-list">
                            <?php foreach ($model->people as $person): ?>
                                <?php $avatarPath = ($person->coverImage) ? $person->coverImage->path : null; ?>
                                <a href="<?= Url::to(['person/view', 'id' => $person->id]) ?>" class="person-avatar-item">
                                    <?php if ($avatarPath): ?>
                                        <img src="<?= Url::to('@web/' . $avatarPath) ?>" class="avatar-img" alt="<?= Html::encode($person->name) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="avatar-name"><?= Html::encode($person->name) ?></div>
                                    <div class="avatar-role"><?= Html::encode($person->role_type) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 2. 相关文章 -->
            <?php if (!empty($articles)): ?>
            <div class="sidebar-card">
                <div class="card-header">
                    <h4><i class="glyphicon glyphicon-book"></i> 相关文献资料</h4>
                </div>
                <div class="card-body" style="padding: 0;">
                    <ul class="article-list">
                        <?php foreach ($articles as $article): ?>
                            <?php 
                                $filePath = $article->path;
                                if (!preg_match('/^http/', $filePath)) {
                                    $frontendPath = Yii::getAlias('@frontend/web/' . $filePath);
                                    if (!file_exists($frontendPath)) {
                                        $filePath = '/advanced/backend/web/' . $filePath;
                                    } else {
                                        $filePath = Url::to('@web/' . $filePath);
                                    }
                                }
                            ?>
                            <a href="<?= $filePath ?>" class="article-list-item" target="_blank">
                                <i class="glyphicon glyphicon-file article-icon"></i>
                                <span class="article-title"><?= Html::encode($article->title) ?></span>
                                <i class="glyphicon glyphicon-new-window article-arrow"></i>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- 3. 温馨提示 -->
            <div class="tip-card">
                <i class="glyphicon glyphicon-info-sign"></i>
                <strong>提示：</strong> 点击人物头像或文章标题可了解更多历史背景。铭记历史，是为了更好地前行。
            </div>
        </div> 
    </div> 
</div>
