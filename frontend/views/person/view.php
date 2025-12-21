<?php

/**
 * Ding 2310724
 * 前台人物详情视图
 */

use yii\helpers\Html;

/** @var \common\models\WarPerson $model */
$this->title = $model->name;
?>

<div class="person-view">
    <h1><?= Html::encode($model->name) ?> <small><?= Html::encode($model->role_type) ?></small></h1>
    <?php if ($model->intro): ?>
        <p class="lead"><?= Html::encode($model->intro) ?></p>
    <?php endif; ?>

    <?php if ($model->biography): ?>
        <div class="panel panel-default">
            <div class="panel-heading">生平</div>
            <div class="panel-body">
                <?= nl2br(Html::encode($model->biography)) ?>
            </div>
        </div>
    <?php endif; ?>

    <h3>相关事件</h3>
    <?php if (empty($model->events)): ?>
        <p class="text-muted">暂未关联事件</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($model->events as $event): ?>
                <li class="list-group-item">
                    <strong><?= Html::encode($event->event_date ?: '日期待定') ?></strong>
                    <?= Html::encode($event->title) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
