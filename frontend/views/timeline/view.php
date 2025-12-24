<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * liyu 2311591
 * 详情页视图
 */

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */
/* @var $messages common\models\WarMessage[] */
/* @var $images common\models\WarMedia[] */
/* @var $articles common\models\WarMedia[] */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '时间轴', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

            <!-- 2. 正文内容 -->
            <div class="article-body" style="font-size: 1.1em; line-height: 2; color: #333; text-align: justify; margin-bottom: 40px;">
                <?= nl2br(Html::encode($model->content)) ?>
            </div>

            <!-- 3. 留言展示区域-->
            <div id="comments" style="margin-top: 60px; border-top: 1px dashed #eee; padding-top: 30px;">
                <h3 style="border-left: 5px solid #8b0000; padding-left: 15px; margin-bottom: 25px; font-weight: bold;">
                    缅怀区
                </h3>
                
                <?php if (empty($messages)): ?>
                    <p class="text-muted well">暂无留言，欢迎发表你的缅怀之情。</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="media well" style="background: #fdfdfd; border: 1px solid #eee;">
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <strong style="color: #8b0000;"><?= Html::encode($msg->nickname) ?></strong>
                                    <small class="pull-right text-muted">
                                        <i class="glyphicon glyphicon-time"></i> <?= date('Y-m-d H:i', $msg->created_at) ?>
                                    </small>
                                </h4>
                                <div style="margin-top: 10px; color: #555;">
                                    <?= Html::encode($msg->content) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 留言按钮 -->
                <div class="text-center" style="margin: 40px 0;">
                    <?= Html::a('<i class="glyphicon glyphicon-edit"></i> 我要发表缅怀留言', [
                        'message/create', 
                        'target_type' => 'event', 
                        'target_id' => $model->id
                    ], [
                        'class' => 'btn btn-lg btn-danger',
                        'style' => 'padding: 12px 50px; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 10px rgba(217, 83, 79, 0.3);'
                    ]) ?>
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
