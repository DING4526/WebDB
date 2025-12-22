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

$this->title = $model->title;
// 导航
$this->params['breadcrumbs'][] = ['label' => '时间轴', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="event-detail-container">
    <!-- 头部区域 -->
    <div class="page-header">
        <h1 style="color: #8b0000;">
            <?= Html::encode($model->title) ?>
            <small style="display: block; margin-top: 10px;">
                <i class="glyphicon glyphicon-calendar"></i> <?= $model->event_date ?> 
                | <i class="glyphicon glyphicon-map-marker"></i> <?= Html::encode($model->location ?: '地点不详') ?>
            </small>
        </h1>
    </div>

    <div class="row">
        <!-- 左侧：正文与图片 -->
        <div class="col-md-8">
            <div class="event-content" style="font-size: 1.2em; line-height: 2; text-align: justify;">
                
                <!-- 封面图，展示在正文上方 -->
                <?php /* 
                <?php if ($model->image_url): ?>
                    <div class="thumbnail" style="margin-bottom: 30px;">
                        <?= Html::img($model->image_url, ['class' => 'img-responsive', 'alt' => $model->title]) ?>
                        <?php if ($model->image_caption): ?>
                            <div class="caption text-center text-muted small"><?= Html::encode($model->image_caption) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                */ ?>

                <!-- 正文内容 -->
                <div class="article-body">
                    <!-- 事件相关的媒体图片 -->
                    <?php if (!empty($eventImages)): ?>
                        <div class="event-gallery" style="margin-bottom: 30px;">
                            <?php foreach ($eventImages as $img): ?>
                                <div class="thumbnail">
                                    <?= Html::img(Url::to('@web/' . $img->path), ['class' => 'img-responsive', 'alt' => $img->title]) ?>
                                    <?php if ($img->title): ?>
                                        <div class="caption text-center small"><?= Html::encode($img->title) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?= nl2br(Html::encode($model->content)) ?>
                </div>
            </div>

            <!-- 留言区域 -->
            <div id="comments" style="margin-top: 60px;">
                <h3 style="border-left: 5px solid #8b0000; padding-left: 15px; margin-bottom: 25px;">
                    社会各界感言
                </h3>
                
                <?php if (empty($messages)): ?>
                    <p class="text-muted well">暂无留言，欢迎发表你的缅怀之情。</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="media well" style="background: #fff;">
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <strong><?= Html::encode($msg->nickname) ?></strong>
                                    <small class="pull-right text-muted"><?= date('Y-m-d H:i', $msg->created_at) ?></small>
                                </h4>
                                <div style="margin-top: 10px;">
                                    <?= Html::encode($msg->content) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 留言按钮部分 -->
                <div class="text-center" style="margin-top: 30px;">
                    <?= Html::a('我要发表缅怀留言', [
                        'message/create', 
                        'target_type' => 'event', 
                        'target_id' => $model->id
                    ], [
                        'class' => 'btn btn-lg btn-danger',
                        'style' => 'padding: 10px 40px; border-radius: 25px;'
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- 右侧：相关人物 -->
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #8b0000; color: #fff;">
                    <h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> 相关历史人物</h3>
                </div>
                <div class="panel-body">
                    <?php if (empty($model->people)): ?>
                        <p class="text-muted">暂无关联人物数据</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($model->people as $person): ?>
                                <div class="col-xs-6 text-center" style="margin-bottom: 20px;">
                                    <?php 
                                        // 获取头像路径：WarPerson 模型里有 getCoverImage，它返回的是 WarMedia 对象
                                        $avatarPath = ($person->coverImage) ? $person->coverImage->path : null;
                                    ?>

                                    <?= Html::a(
                                        $avatarPath ? 
                                            Html::img(Url::to('@web/' . $avatarPath), ['class' => 'img-circle img-thumbnail', 'style' => 'width: 80px; height: 80px; object-fit: cover;']) :
                                            '<div class="img-circle img-thumbnail" style="width: 80px; height: 80px; line-height: 70px; background: #f5f5f5; margin: 0 auto;">
                                                <i class="glyphicon glyphicon-user" style="font-size: 40px; color: #ddd; vertical-align: middle;"></i>
                                            </div>',
                                        ['person/view', 'id' => $person->id]
                                    ) ?>
                                    
                                    <div style="margin-top: 5px;">
                                        <strong><?= Html::a(Html::encode($person->name), ['person/view', 'id' => $person->id]) ?></strong>
                                        <br><small class="text-muted"><?= Html::encode($person->role_type) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 相关推荐 -->
            <div class="panel panel-warning" style="margin-top: 20px;">
                <div class="panel-body small">
                    <strong>提示：</strong> 您可以点击人物头像了解他们在本次事件中的具体事迹。铭记历史，是为了更好地前行。
                </div>
            </div>
        </div>
    </div>
</div>