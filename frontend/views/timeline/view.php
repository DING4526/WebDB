<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * liyu 2311591
 * 详情页视图 - 深色主题优化版
 */

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $messages common\models\WarMessage[] */
/* @var $images common\models\WarMedia[] */
/* @var $articles common\models\WarMedia[] */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '时间轴', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// 注册深色主题样式
$this->registerCss("
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

    .event-detail-container {
        background: var(--card-bg) !important;
        padding: 25px;
        border-radius: 16px;
        border: 1px solid var(--card-border);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .event-detail-container .page-header {
        margin: 10px 0 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--card-border) !important;
    }

    .event-detail-container .page-header h2 {
        color: var(--gold-primary) !important;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .event-detail-container .page-header .glyphicon {
        color: var(--gold-muted) !important;
    }

    .event-detail-container .page-header > div {
        color: var(--text-light) !important;
        opacity: 0.8;
    }

    .event-detail-container .article-body {
        color: var(--text-light) !important;
        opacity: 0.9;
    }

    /* 轮播图 */
    #event-carousel {
        border: 1px solid var(--card-border);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4) !important;
    }

    .carousel-indicators li {
        border-color: var(--gold-primary) !important;
    }

    .carousel-indicators .active {
        background-color: var(--gold-primary) !important;
    }

    /* 侧边栏面板 */
    .panel-default {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-top: 3px solid var(--gold-primary) !important;
    }

    .panel-heading {
        background: transparent !important;
        border-bottom: 1px solid var(--card-border) !important;
    }

    .panel-title {
        color: var(--gold-primary) !important;
    }

    .panel-body {
        color: var(--text-light);
    }

    .panel-body .text-muted {
        color: rgba(245, 230, 200, 0.6) !important;
    }

    .panel-body a {
        color: var(--text-light) !important;
        transition: color 0.2s ease;
    }

    .panel-body a:hover {
        color: var(--gold-light) !important;
    }

    .img-thumbnail {
        background: var(--card-bg) !important;
        border-color: var(--gold-muted) !important;
    }

    /* 列表项 */
    .list-group-item {
        background: transparent !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
        transition: all 0.2s ease;
    }

    .list-group-item:hover {
        background: rgba(201, 162, 39, 0.1) !important;
        color: var(--gold-light) !important;
    }

    .list-group-item .glyphicon {
        color: var(--gold-muted);
    }

    /* 提示框 */
    .alert-warning {
        background: rgba(201, 162, 39, 0.15) !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
    }

    .alert-info {
        background: rgba(30, 25, 20, 0.8) !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
    }

    /* 留言区 */
    .content-card {
        background: var(--card-bg) !important;
        border: 1px solid var(--card-border);
        border-radius: 12px;
    }

    .section-title {
        color: var(--gold-primary) !important;
        border-left-color: var(--gold-primary) !important;
    }

    .section-title small {
        color: rgba(245, 230, 200, 0.6) !important;
    }

    .comment-item {
        border-bottom-color: var(--card-border) !important;
    }

    .comment-user {
        color: var(--gold-primary) !important;
    }

    .comment-time {
        color: rgba(245, 230, 200, 0.5) !important;
    }

    .comment-content {
        color: var(--text-light) !important;
        opacity: 0.9;
    }

    /* 表单区域 */
    .comment-form {
        background: rgba(20, 15, 10, 0.6) !important;
        border: 1px solid var(--card-border) !important;
    }

    .comment-form h4 {
        color: var(--gold-primary) !important;
    }

    .comment-form .form-control {
        background: rgba(30, 25, 20, 0.8) !important;
        border-color: var(--card-border) !important;
        color: var(--text-light) !important;
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

    .comment-form .text-muted {
        color: rgba(245, 230, 200, 0.5) !important;
    }
");
?>

<div class="event-detail-container" style="background: #fff; padding: 20px; border-radius: 8px;">
    <!-- 头部区域 -->
    <div class="page-header" style="margin: 10px 0 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
        <!-- 主标题-->
        <h2 style="color: #8b0000; font-weight: bold; font-size: 24px; margin: 0;">
            <?= Html::encode($model->title) ?>
        </h2>
        
        <!-- 副标题 -->
        <div style="margin-top: 8px; font-size: 13px; color: #666;">
            <!-- 日期 -->
            <span style="margin-right: 15px;">
                <i class="glyphicon glyphicon-calendar" style="color: #999;"></i> 
                <?= Html::encode($model->event_date) ?>
            </span>
            
            <!-- 地点 -->
            <span style="margin-right: 15px;">
                <i class="glyphicon glyphicon-map-marker" style="color: #999;"></i> 
                <?= Html::encode($model->location ?: '地点不详') ?>
            </span>
            
            <!-- 访问量 -->
            <span>
                <i class="glyphicon glyphicon-eye-open" style="color: #999;"></i> 
                访问量：<?= number_format($visitCount) ?> 次
            </span>
        </div>
    </div>

    <div class="row">
        <!-- 左侧：主要内容区域 -->
        <div class="col-md-8">
            
            <!-- 1. 多图轮播 -->
            <?php if (!empty($images)): ?>
                <div id="event-carousel" class="carousel slide" data-ride="carousel" style="margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-radius: 8px; overflow: hidden; position: relative;">
                    
                    <!-- 指示器 -->
                    <ol class="carousel-indicators" style="bottom: 5px; z-index: 20;">
                        <?php foreach ($images as $index => $img): ?>
                            <li data-target="#event-carousel" data-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" style="border-color: #8b0000;"></li>
                        <?php endforeach; ?>
                    </ol>

                    <!-- 轮播内容 -->
                    <div class="carousel-inner" role="listbox">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= Url::to('@web/' . $img->path) ?>" alt="<?= Html::encode($img->title) ?>" style="width:100%; height:400px; object-fit:cover;">
                                <?php if ($img->title): ?>
                                    <div class="carousel-caption" style="bottom: 35px; background: none; padding: 0; left: 0; right: 0; text-align: center;">
                                        <h4 style="
                                            display: inline-block; 
                                            background: rgba(0,0,0,0.6); 
                                            color: #fff; 
                                            padding: 4px 12px; 
                                            border-radius: 4px; 
                                            font-size: 13px; /* 调小字体 */
                                            margin: 0; 
                                            font-weight: normal; 
                                            letter-spacing: 0.5px;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                                        ">
                                            <?= Html::encode($img->title) ?>
                                        </h4>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 左右控制按钮 -->
                    <a class="left carousel-control" href="#event-carousel" role="button" data-slide="prev" style="background-image: none; width: 8%;">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="color: #fff; text-shadow: 0 0 8px rgba(0,0,0,0.6); position: absolute; top: 50%; transform: translateY(-50%); font-size: 24px;"></span>
                    </a>
                    <a class="right carousel-control" href="#event-carousel" role="button" data-slide="next" style="background-image: none; width: 8%;">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="color: #fff; text-shadow: 0 0 8px rgba(0,0,0,0.6); position: absolute; top: 50%; transform: translateY(-50%); font-size: 24px;"></span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- 正文 -->
            <div class="article-body" style="font-size: 1.1em; line-height: 2; color: #333; text-align: justify; margin-bottom: 40px;">
                <?= nl2br(Html::encode($model->content)) ?>
            </div>

            <!-- 缅怀感言区 -->
            <div class="content-card" id="comments">
                <h3 class="section-title">
                    缅怀与致敬 
                    <small class="pull-right" style="font-size: 14px; font-weight: normal; color: #999; margin-top: 5px;">
                        已有 <?= count($comments) ?> 位同胞发表致敬感言
                    </small>
                </h3>
                
                <!-- 感言列表 -->
                <div class="comments-list" style="margin-bottom: 30px; max-height: 500px; overflow-y: auto; padding-right: 10px;">
                    <?php if (empty($comments)): ?>
                        <div class="alert alert-info" style="background: #fcfcfc; color: #666; border-color: #eee;">
                            <i class="glyphicon glyphicon-info-sign"></i> 暂无致敬感言。铭记历史，从你我开始。
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                                <!-- 头部：署名居左，时间居右 -->
                                <div class="comment-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px;">
                                    <span class="comment-user" style="font-weight: bold; color: #8b0000;">
                                        <i class="glyphicon glyphicon-user"></i> <?= Html::encode($comment->nickname) ?>
                                    </span>
                                    <span class="comment-time" style="color: #999;">
                                        <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                                    </span>
                                </div>
                                <!-- 内容：另起一行 -->
                                <div class="comment-content" style="color: #333; line-height: 1.6; font-size: 14px; text-align: justify;">
                                    <?= nl2br(Html::encode($comment->content)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 提交致敬表单 -->
                <div class="comment-form" style="background: #fdfdfd; padding: 20px; border-radius: 8px; border: 1px solid #f0f0f0;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; color: #333; font-weight: bold; font-size: 16px;">
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
                                'style' => 'border-radius: 4px; border: 1px solid #ddd; box-shadow: none;'
                            ])->label(false) ?>
                        </div>
                    </div>
                    
                    <?= $form->field($newMessage, 'content')->textarea([
                        'rows' => 3, 
                        'placeholder' => '铭记这段历史，缅怀英勇先烈。请写下您的致敬感言或思考...',
                        'style' => 'border-radius: 4px; border: 1px solid #ddd; resize: vertical; box-shadow: none;'
                    ])->label(false) ?>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <?= Html::submitButton('<i class="glyphicon glyphicon-send"></i> 提交致敬感言', [
                            'class' => 'btn btn-danger',
                            'style' => 'padding: 8px 35px; border-radius: 20px; font-weight: bold; background-color: #8b0000; border: none;'
                        ]) ?>
                        <span class="text-muted small" style="margin-left: 15px;">
                            * 您的感言将在审核后公开展示，以示敬意。
                        </span>
                    </div>
                    
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div> 

        <!-- 右侧：侧边栏区域 (4格) -->
        <div class="col-md-4">
            <!-- 1. 相关人物卡片 -->
            <div class="panel panel-default" style="border-top: 3px solid #8b0000;">
                <div class="panel-heading" style="background-color: #fff;">
                    <h3 class="panel-title" style="color: #8b0000; font-weight: bold;">
                        <i class="glyphicon glyphicon-user"></i> 相关历史人物
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if (empty($model->people)): ?>
                        <p class="text-muted">暂无关联人物数据</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($model->people as $person): ?>
                                <div class="col-xs-6 text-center" style="margin-bottom: 20px;">
                                    <?php 
                                        $avatarPath = ($person->coverImage) ? $person->coverImage->path : null;
                                    ?>
                                    <?= Html::a(
                                        $avatarPath ? 
                                            Html::img(Url::to('@web/' . $avatarPath), ['class' => 'img-circle img-thumbnail', 'style' => 'width: 70px; height: 70px; object-fit: cover;']) :
                                            '<div class="img-circle img-thumbnail" style="width: 70px; height: 70px; line-height: 60px; background: #f5f5f5; margin: 0 auto;">
                                                <i class="glyphicon glyphicon-user" style="font-size: 30px; color: #ddd;"></i>
                                            </div>',
                                        ['person/view', 'id' => $person->id]
                                    ) ?>
                                    <div style="margin-top: 8px;">
                                        <strong style="display: block;"><?= Html::a(Html::encode($person->name), ['person/view', 'id' => $person->id], ['style' => 'color: #333;']) ?></strong>
                                        <small class="text-muted"><?= Html::encode($person->role_type) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 相关文章栏目 -->
            <?php foreach ($articles as $article): ?>
                <?php 
                    // 如果是外部链接直接用，如果是本地路径则处理
                    $filePath = $article->path;
                    if (!preg_match('/^http/', $filePath)) {
                        $frontendPath = Yii::getAlias('@frontend/web/' . $filePath);
                        if (!file_exists($frontendPath)) {
                            // 指向后台的 web 目录
                            $filePath = '/advanced/backend/web/' . $filePath;
                        } else {
                            $filePath = Url::to('@web/' . $filePath);
                        }
                    }
                ?>
                <a href="<?= $filePath ?>" class="list-group-item" target="_blank">
                    <i class="glyphicon glyphicon-file"></i> <?= Html::encode($article->title) ?>
                </a>
            <?php endforeach; ?>

            <!-- 3. 温馨提示 -->
            <div class="alert alert-warning small" style="border-radius: 0;">
                <i class="glyphicon glyphicon-info-sign"></i> 
                <strong>提示：</strong> 点击人物头像或文章标题可了解更多历史背景。铭记历史，是为了更好地前行。
            </div>
        </div> 
    </div> 
</div>

<style>
.carousel-indicators li {
    background-color: rgba(255, 255, 255, 0.5);
    border: 1px solid #8b0000;
    width: 12px;
    height: 12px;
    margin: 0 5px;
}
.carousel-indicators .active {
    background-color: #8b0000;
    width: 14px;
    height: 14px;
    margin: 0 5px;
}

.carousel-control.left, .carousel-control.right {
    background-image: none !important;
    filter: none !important;
}

.carousel-control:hover span {
    color: #8b0000 !important;
    scale: 1.2;
    transition: all 0.2s;
}


.carousel-caption h4 {
    font-size: 18px;
    letter-spacing: 1px;
}
</style>
