<?php
/**
 * liyu 2311591
 * 前台时间轴视图 
 */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '抗战胜利80周年 - 历史时间轴';
// 引入自定义CSS
$this->registerCssFile('@web/css/timeline-style.css');
?>

<div class="container timeline-index">
    <div class="page-header text-center">
        <h1 style="color: #8b0000; font-weight: bold;">
            <i class="glyphicon glyphicon-flag"></i> 抗战大事记时间轴
        </h1>
        <p class="text-muted">1931 - 1945：十四年艰苦卓绝的抗战历程</p>
    </div>

    <?php if (empty($stages)): ?>
        <div class="alert alert-info text-center">暂无阶段/事件数据，请先在后台录入。</div>
    <?php else: ?>
        
        <ul class="timeline">
            <?php foreach ($stages as $stage): ?>
                <!-- 阶段标题行：横跨时间轴的里程碑 -->
                <li class="timeline-stage-header">
                    <div class="stage-title">
                        <?= Html::encode($stage->name) ?>
                        <?php if ($stage->start_year || $stage->end_year): ?>
                            <small>(<?= Html::encode($stage->start_year ?: '?') ?> - <?= Html::encode($stage->end_year ?: '?') ?>)</small>
                        <?php endif; ?>
                    </div>
                </li>

                <?php if (!empty($stage->events)): ?>
                    <?php foreach ($stage->events as $event): ?>
                        <li>
                            <!-- 线条上的小圆点 -->
                            <div class="timeline-badge"></div>
                            
                            <!-- 事件内容卡片 -->
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <span class="timeline-date">
                                        <i class="glyphicon glyphicon-calendar"></i> 
                                        <?= Html::encode($event->event_date ?: '日期待定') ?>
                                    </span>
                                    <h3 class="timeline-title" style="margin-top: 5px;">
                                        <?= Html::a(Html::encode($event->title), ['view', 'id' => $event->id], ['style' => 'color: #333;']) ?>
                                    </h3>
                                </div>
                                
                                <div class="timeline-body" style="margin-top: 15px;">
                                    <div class="row">
                                        <?php 
                                            // 提取封面图
                                            $cover = null;
                                            if (!empty($event->medias)) {
                                                foreach($event->medias as $m) { 
                                                    if($m->type === 'image') { 
                                                        $cover = $m; 
                                                        break; 
                                                    } 
                                                }
                                            }
                                        ?>
                                        
                                        <?php if ($cover): ?>
                                            <!-- 有图排版：左 4 右 8 -->
                                            <div class="col-sm-4 col-xs-12">
                                                <div class="thumbnail" style="padding: 0; border: none; margin-bottom: 10px;">
                                                    <?= Html::img(Url::to('@web/' . $cover->path), [
                                                        'class' => 'img-responsive img-rounded', 
                                                        'style' => 'width: 100%; height: 100px; object-fit: cover; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'
                                                    ]) ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 col-xs-12">
                                        <?php else: ?>
                                            <!-- 无图排版：全宽 -->
                                            <div class="col-sm-12">
                                        <?php endif; ?>
                                            
                                            <p class="text-muted" style="font-size: 0.9em; line-height: 1.6; height: 50px; overflow: hidden; margin-bottom: 10px;">
                                                <?= Html::encode($event->summary) ?>
                                            </p>
                                            
                                            <div class="text-right">
                                                <?= Html::a('查看详情 <i class="glyphicon glyphicon-menu-right"></i>', 
                                                    ['view', 'id' => $event->id], 
                                                    ['class' => 'btn btn-danger btn-xs', 'style' => 'padding: 2px 10px; border-radius: 10px;']
                                                ) ?>
                                            </div>
                                        </div> 
                                    </div> 
                                </div> 
                            </div> 
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </ul>
        
    <?php endif; ?>
</div>

<style>
    .timeline-panel {
        min-height: 150px; /* 保证卡片有最小高度 */
    }
    .timeline-title a:hover {
        color: #8b0000 !important;
        text-decoration: none;
    }
    /* 确保移动端缩略图不会太突兀 */
    @media (max-width: 767px) {
        .timeline-body .col-sm-4 {
            margin-bottom: 15px;
        }
        .text-muted {
            height: auto !important; 
        }
    }
</style>