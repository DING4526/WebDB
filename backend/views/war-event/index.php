<?php

/**
 * Ding 2310724
 * 抗战事件列表
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WarEventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抗战事件管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="war-event-index">
    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('新增事件', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'event_date',
            [
                'attribute' => 'stage_id',
                'value' => function ($model) {
                    return $model->stage->name ?? '';
                },
            ],
            'location',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status ? '发布' : '草稿';
                },
                'filter' => [1 => '发布', 0 => '草稿'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '快捷操作',
                'template' => '{update} {toggle-status} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']);
                    },
                    'toggle-status' => function ($url, $model) {
                        $isPublished = (bool)$model->status;
                        return Html::a($isPublished ? '下线' : '发布', ['toggle-status', 'id' => $model->id], [
                            'class' => 'btn btn-xs ' . ($isPublished ? 'btn-warning' : 'btn-success'),
                            'data-method' => 'post',
                            'data-pjax' => 0,
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('删除', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'data' => [
                                'confirm' => '确认删除该事件？',
                                'method' => 'post',
                            ],
                            'data-pjax' => 0,
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'min-width:220px;'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
