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
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
