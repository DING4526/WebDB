<?php

/**
 * Ding 2310724
 * 前台时间轴视图
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '抗战时间轴';
?>

<div class="timeline-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (empty($stages)): ?>
        <div class="alert alert-info">暂无阶段/事件数据，请先在后台录入。</div>
    <?php endif; ?>

    <?php foreach ($stages as $stage): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?= Html::encode($stage->name) ?></strong>
                <?php if ($stage->start_year || $stage->end_year): ?>
                    <small>(<?= Html::encode($stage->start_year ?: '?') ?> - <?= Html::encode($stage->end_year ?: '?') ?>)</small>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <?php if (empty($stage->events)): ?>
                    <p class="text-muted">该阶段暂无事件</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($stage->events as $event): ?>
                            <li class="list-group-item">
                                <strong><?= Html::encode($event->event_date ?: '日期待定') ?></strong>
                                <?= Html::encode($event->title) ?>
                                <?php if ($event->summary): ?>
                                    <div class="text-muted small"><?= Html::encode($event->summary) ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
