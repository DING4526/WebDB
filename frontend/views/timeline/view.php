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
    <div class="page-header">
        <h1 style="color: #8b0000; font-weight: bold;">
            <?= Html::encode($model->title) ?>
            <small style="display: block; margin-top: 10px;">
                <i class="glyphicon glyphicon-calendar"></i> <?= Html::encode($model->event_date) ?> 
                | <i class="glyphicon glyphicon-map-marker"></i> <?= Html::encode($model->location ?: '地点不详') ?>
            </small>
        </h1>
    </div>

    <div class="row">
        <!-- 左侧：主要内容区域 -->
        <div class="col-md-8">
            
            <!-- 1. 多图轮播 -->
            <?php if (!empty($images)): ?>
                <div id="event-carousel" class="carousel slide" data-ride="carousel" style="margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-radius: 8px; overflow: hidden;">
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
                                    <div class="carousel-caption" style="background: rgba(0,0,0,0.5); border-radius: 10px; padding: 5px 15px;">
                                        <h4><?= Html::encode($img->title) ?></h4>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 左右控制按钮 -->
                    <a class="left carousel-control" href="#event-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#event-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- 2. 正文内容 -->
            <div class="article-body" style="font-size: 1.1em; line-height: 2; color: #333; text-align: justify; margin-bottom: 40px;">
                <?= nl2br(Html::encode($model->content)) ?>
            </div>

            <!-- 3. 留言展示区域 (放在 col-md-8 内部) -->
            <div id="comments" style="margin-top: 60px; border-top: 1px dashed #eee; padding-top: 30px;">
                <h3 style="border-left: 5px solid #8b0000; padding-left: 15px; margin-bottom: 25px; font-weight: bold;">
                    社会各界感言
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
            
            <!-- 2. 相关文章 -->
            <div class="panel panel-default" style="border-top: 3px solid #333;">
                <div class="panel-heading" style="background-color: #fff;">
                    <h3 class="panel-title" style="font-weight: bold;">
                        <i class="glyphicon glyphicon-book"></i> 相关研究文章
                    </h3>
                </div>
                <div class="list-group">
                    <?php if (empty($articles)): ?>
                        <div class="list-group-item text-muted">暂无相关文献</div>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <a href="<?= Url::to($article->path) ?>" class="list-group-item" target="_blank">
                                <i class="glyphicon glyphicon-file" style="color: #777;"></i> <?= Html::encode($article->title) ?>
                                <span class="pull-right glyphicon glyphicon-new-window" style="font-size: 10px; color: #ccc;"></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. 温馨提示 -->
            <div class="alert alert-warning small" style="border-radius: 0;">
                <i class="glyphicon glyphicon-info-sign"></i> 
                <strong>提示：</strong> 点击人物头像或文章标题可了解更多历史背景。铭记历史，是为了更好地前行。
            </div>
        </div> 
    </div> 
</div>