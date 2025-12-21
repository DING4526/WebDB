<?php

/**
 * Ding 2310724
 * 抗战事件详情
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WarEvent */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '抗战事件管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-event-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该事件？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'event_date',
            [
                'label' => '阶段',
                'value' => $model->stage->name ?? '',
            ],
            'location',
            'summary:ntext',
            'content:ntext',
            [
                'attribute' => 'status',
                'value' => $model->status ? '发布' : '草稿',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
