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
        <h1 style="color: #8b0000; font-weight: bold;">抗战大事记时间轴</h1>
        <p class="text-muted">1931 - 1945：十四年艰苦卓绝的抗战历程</p>
    </div>

    <?php if (empty($stages)): ?>
        <div class="alert alert-info">暂无阶段/事件数据，请先在后台录入。</div>
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
                            <!-- 小圆点 -->
                            <div class="timeline-badge"></div>
                            
                            <!-- 事件内容卡片 -->
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <span class="timeline-date">
                                        <i class="glyphicon glyphicon-calendar"></i> 
                                        <?= Html::encode($event->event_date ?: '日期待定') ?>
                                    </span>
                                    <h3 class="timeline-title">
                                        <?= Html::a(Html::encode($event->title), ['view', 'id' => $event->id]) ?>
                                    </h3>
                                </div>
                                <div class="timeline-body">
                                    <?php if ($event->summary): ?>
                                        <p class="text-muted"><?= Html::encode($event->summary) ?></p>
                                    <?php endif; ?>
                                    <div class="text-right">
                                        <?= Html::a('查看详情 &raquo;', ['view', 'id' => $event->id], ['class' => 'btn btn-danger btn-xs']) ?>
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